<?php
// webhook.php - Endpoint que escucha las notificaciones de Openpay
require_once 'dbcon.php';

// 1. Leer la notificación enviada por Openpay en formato JSON
$input = file_get_contents('php://input');
$event = json_decode($input, true);

if (!$event) {
    http_response_code(400);
    exit('Payload no válido');
}

// 2. Identificar el tipo de evento
$eventType   = $event['type'] ?? '';
$transaction = $event['transaction'] ?? [];

if ($eventType === 'charge.succeeded') {
    $transaccionId = $transaction['id'] ?? '';
    $monto         = $transaction['amount'] ?? 0;

    try {
        // Actualizamos el último pedido pendiente a "Pagado"
        $sql  = "UPDATE pedidos SET estatus = 'Pagado' WHERE estatus = 'Pendiente de Pago' ORDER BY id DESC LIMIT 1";
        $stmt = $con->prepare($sql);
        $stmt->execute();

        // Responder siempre con HTTP 200 OK para confirmar recepción a Openpay
        http_response_code(200);
        echo json_encode(['status' => 'success']);
    } catch (Exception $e) {
        http_response_code(500);
    }
} else {
    // Para otros eventos no manejados, responder HTTP 200 para no saturar reintentos
    http_response_code(200);
}