<?php
// webhook.php - Endpoint que escucha las notificaciones de Openpay
//
// Seguridad (antes: cualquiera podía enviar {"type":"charge.succeeded"} y marcar un pedido como "Pagado"):
//   1. Solo acepta POST.
//   2. Autenticación HTTP Basic (usuario/contraseña configurados en el panel de Openpay al registrar el webhook).
//   3. Confirma el cargo consultando directamente la API de Openpay (no se confía en el JSON recibido).
//   4. Solo marca como pagado un pedido pendiente cuyo total coincide con el monto confirmado.

define('APP_NO_SESSION', true);                       // endpoint máquina-a-máquina: sin cookie de sesión
require_once __DIR__ . '/includes/security.php';
require_once __DIR__ . '/dbcon.php';

header('Content-Type: application/json; charset=UTF-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    exit;
}

// ---- 1) Autenticación Basic ----
$authUser = $_SERVER['PHP_AUTH_USER'] ?? '';
$authPass = $_SERVER['PHP_AUTH_PW'] ?? '';
if ($authUser === '' && !empty($_SERVER['HTTP_AUTHORIZATION'] ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? '')) {
    $hdr = $_SERVER['HTTP_AUTHORIZATION'] ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
    if (stripos($hdr, 'Basic ') === 0) {
        $decoded = base64_decode(substr($hdr, 6), true);
        if ($decoded !== false && strpos($decoded, ':') !== false) {
            [$authUser, $authPass] = explode(':', $decoded, 2);
        }
    }
}

$okUser = hash_equals(env_required('OPENPAY_WEBHOOK_USER'), (string) $authUser);
$okPass = hash_equals(env_required('OPENPAY_WEBHOOK_PASS'), (string) $authPass);
if (!($okUser && $okPass)) {
    http_response_code(401);
    header('WWW-Authenticate: Basic realm="openpay-webhook"');
    exit;
}

// ---- 2) Leer y validar el payload (máx. 64 KB) ----
$input = file_get_contents('php://input', false, null, 0, 65536);
$event = json_decode((string) $input, true);

if (!is_array($event)) {
    http_response_code(400);
    exit(json_encode(['status' => 'error', 'message' => 'Payload no válido']));
}

$eventType   = $event['type'] ?? '';
$transaction = is_array($event['transaction'] ?? null) ? $event['transaction'] : [];

if ($eventType !== 'charge.succeeded') {
    http_response_code(200);           // eventos no manejados: OK para que Openpay no reintente
    exit(json_encode(['status' => 'ignored']));
}

$transaccionId = (string) ($transaction['id'] ?? '');
if (!preg_match('/^[A-Za-z0-9]{10,40}$/', $transaccionId)) {
    http_response_code(400);
    exit(json_encode(['status' => 'error', 'message' => 'ID de transacción inválido']));
}

// ---- 3) Confirmar el cargo directamente con Openpay ----
$merchant_id = env_required('OPENPAY_ID');
$private_key = env_required('OPENPAY_SK');
$domain      = $_ENV['OPENPAY_DOMAIN'] ?? 'sandbox-api.openpay.mx';

$ch = curl_init("https://{$domain}/v1/{$merchant_id}/charges/{$transaccionId}");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
curl_setopt($ch, CURLOPT_USERPWD, $private_key . ':');
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
curl_setopt($ch, CURLOPT_TIMEOUT, 20);
$resp     = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$cargo = is_string($resp) ? json_decode($resp, true) : null;
if ($httpCode !== 200 || !is_array($cargo) || ($cargo['status'] ?? '') !== 'completed') {
    error_log("Webhook: no se pudo confirmar el cargo {$transaccionId} (HTTP {$httpCode})");
    http_response_code(200);           // no reintentar en bucle; queda registrado en el log
    exit(json_encode(['status' => 'unverified']));
}
$monto = (float) ($cargo['amount'] ?? 0);

// ---- 4) Marcar como pagado SOLO un pedido pendiente con ese mismo total ----
try {
    // Recomendado: guardar el id de Openpay en el pedido al crear el cargo y buscar por él
    // (columna openpay_id). Mientras tanto se exige coincidencia de monto.
    $stmt = $con->prepare(
        "UPDATE pedidos SET estatus = 'Pagado'
         WHERE estatus = 'Pendiente de Pago' AND total = :total
         ORDER BY id DESC LIMIT 1"
    );
    $stmt->execute([':total' => $monto]);

    http_response_code(200);
    echo json_encode(['status' => 'success']);
} catch (Exception $e) {
    error_log('Error webhook: ' . $e->getMessage());
    http_response_code(500);
}
