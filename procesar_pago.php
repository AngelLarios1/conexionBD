<?php
// procesar_pago.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'dbcon.php';

// Credenciales Sandbox
$merchant_id = 'm43ygegi362bajsjomkm'; // ID de comercio numérico correcto
$private_key = 'sk_92fff961d95f4262a6cc601920b701e4'; 
$domain      = 'sandbox-api.openpay.mx';

$tokenId         = $_POST['token_id'] ?? '';
$deviceSessionId = $_POST['device_session_id'] ?? '';
$monto           = (float) ($_POST['monto'] ?? 0);

if (empty($tokenId) || empty($deviceSessionId)) {
    exit('Datos de transacción incompletos.');
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
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$resData = json_decode($response, true);

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
    echo "<h1>❌ Error en el pago</h1><p>" . htmlspecialchars($error) . "</p>";
}