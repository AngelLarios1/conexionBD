<?php
// procesar_pago.php
require_once __DIR__ . '/includes/security.php';   // cabeceras + sesión segura + CSRF
require_once 'dbcon.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}
csrf_require();

// Credenciales desde .env (antes estaban escritas en el código fuente)
$merchant_id = env_required('OPENPAY_ID');
$private_key = env_required('OPENPAY_SK');
$domain      = $_ENV['OPENPAY_DOMAIN'] ?? 'sandbox-api.openpay.mx';

$tokenId         = $_POST['token_id'] ?? '';
$deviceSessionId = $_POST['device_session_id'] ?? '';
$monto           = (float) ($_POST['monto'] ?? 0);

if (empty($tokenId) || empty($deviceSessionId)) {
    exit('Datos de transacción incompletos.');
}
// TODO (pendiente): el monto NO debe venir del navegador. Recalcularlo en el servidor
// a partir de los IDs de producto (ver hallazgo P-01 del reporte).
if (!is_finite($monto) || $monto <= 0) {
    exit('Monto inválido.');
}

$bodyData = [
    'method'            => 'card',
    'source_id'         => $tokenId,
    'amount'            => $monto,
    'description'       => 'Compra de producto en tienda',
    'device_session_id' => $deviceSessionId,
    'customer'          => [
        'name'  => 'Angel Larios',
        'email' => 'angel306070@gmail.com'
    ]
];

$ch = curl_init("https://{$domain}/v1/{$merchant_id}/charges");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($bodyData));
curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
curl_setopt($ch, CURLOPT_USERPWD, $private_key . ":");
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);   // antes: false (permitía ataques MITM)
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
if ($response === false) {
    error_log('cURL Openpay: ' . curl_error($ch));
}
curl_close($ch);

$resData = is_string($response) ? json_decode($response, true) : null;

if ($httpCode === 200 || $httpCode === 201) {
    $transaccionId = $resData['id'];

    // 1. Guardar la orden en la base de datos
    $sql  = "INSERT INTO pedidos (total, estatus, fecha) VALUES (:total, 'Pagado', NOW())";
    $stmt = $con->prepare($sql);
    $stmt->execute([':total' => $monto]);

    // 2. Limpiar el carrito de la sesión
    unset($_SESSION['carrito']);

    // 3. Redirigir a la vista de éxito
    header("Location: gracias.php?id=" . urlencode($transaccionId));
    exit;
} else {
    $error = $resData['description'] ?? 'No se pudo procesar el pago.';
    echo "<h1>❌ Error en el pago</h1><p>" . e($error) . "</p>";
}