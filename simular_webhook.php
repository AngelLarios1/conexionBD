<?php
// simular_webhook.php
$webhookUrl = 'http://localhost/miproyecto/webhook.php'; 

// Estructura JSON estándar que envía Openpay cuando se liquida un pago
$payload = [
    'type'       => 'charge.succeeded',
    'event_date' => date('Y-m-d\TH:i:sP'),
    'transaction' => [
        'id'          => 'tr1clujvg3rnohryzbbc',
        'amount'      => 299.99,
        'status'      => 'completed',
        'description' => 'Compra de Camisa'
    ]
];

$ch = curl_init($webhookUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 200) {
    echo "<h2>✅ Webhook Simulado Exitosamente</h2>";
    echo "<p>El webhook procesó la notificación correctamente (HTTP 200).</p>";
    echo "<p>Abre phpMyAdmin para verificar que el registro en la tabla <b>pedidos</b> cambió de 'Pendiente de Pago' a <b>'Pagado'</b>.</p>";
} else {
    echo "<h2>❌ Error al ejecutar Webhook</h2>";
    echo "<p>Código HTTP devuelto: <b>{$httpCode}</b></p>";
    echo "<p>Respuesta: {$response}</p>";
}