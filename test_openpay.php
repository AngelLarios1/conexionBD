<?php
// test_openpay.php
require_once 'dbcon.php';

$merchant_id = 'm43ygegi362bajsjomkm'; 
$private_key = 'sk_92fff961d95f4262a6cc601920b701e4'; 
$domain      = 'sandbox-api.openpay.mx';

$mensaje = '';

// Función auxiliar para crear un Token de tarjeta de prueba en Openpay
function crearTokenPrueba($domain, $merchant_id, $private_key, $nombre) {
    $url  = "https://{$domain}/v1/{$merchant_id}/tokens";
    $cardData = [
        'card_number'      => '4111111111111111', // Tarjeta aprobada de prueba
        'holder_name'      => $nombre,
        'expiration_year'  => '28',
        'expiration_month' => '12',
        'cvv2'             => '123'
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($cardData));
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($ch, CURLOPT_USERPWD, $private_key . ":");
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $data = json_decode($response, true);
    return ($httpCode === 200 || $httpCode === 201) ? $data['id'] : false;
}

// Procesar el pago
if (isset($_POST['procesar_pago'])) {
    $monto       = (float) $_POST['monto'];
    $descripcion = $_POST['descripcion'];
    $nombre      = $_POST['nombre'];
    $email       = $_POST['email'];

    // 1. Generar token de tarjeta dinámico
    $source_id = crearTokenPrueba($domain, $merchant_id, $private_key, $nombre);

    if (!$source_id) {
        $mensaje = "<div style='background:#f8d7da; color:#721c24; padding:15px; border-radius:5px;'>
                        ❌ Error al generar el Token de tarjeta de prueba en Openpay.
                    </div>";
    } else {
        // 2. Realizar el cargo con el token generado
        $bodyData = [
            'method'            => 'card',
            'source_id'         => $source_id,
            'amount'            => $monto,
            'description'       => $descripcion,
            'device_session_id' => 'k324kn234kn324',
            'customer'          => [
                'name'  => $nombre,
                'email' => $email
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
            $status        = $resData['status'];

            try {
                $con->beginTransaction();
                // Consulta ajustada a la estructura de tu BD
                $sql  = "INSERT INTO pedidos (total, estatus, fecha) VALUES (:total, 'Pagado', NOW())";
                $stmt = $con->prepare($sql);
                $stmt->execute([':total' => $monto]);
                $con->commit();

                $mensaje = "<div style='background:#d4edda; color:#155724; padding:15px; border-radius:5px;'>
                                ✅ <b>¡Pago Procesado Exitosamente en Openpay Sandbox!</b><br>
                                ID Transacción: <b>{$transaccionId}</b> | Estado: <b>{$status}</b><br>
                                Registro guardado en la base de datos local.
                            </div>";
            } catch (Exception $e) {
                if ($con->inTransaction()) $con->rollBack();
                $mensaje = "<div style='background:#fff3cd; color:#856404; padding:15px; border-radius:5px;'>
                                ⚠️ Pago cobrado en Openpay ({$transaccionId}), error en BD: " . $e->getMessage() . "
                            </div>";
            }
        } else {
            $errorMsg = $resData['description'] ?? 'Error procesando cargo.';
            $mensaje  = "<div style='background:#f8d7da; color:#721c24; padding:15px; border-radius:5px;'>
                            ❌ <b>Error de Openpay (HTTP {$httpCode}):</b> {$errorMsg}
                        </div>";
        }
    }
}
?>

<h2>Prueba de Conexión Openpay Sandbox</h2>

<div style="background:#d4edda; color:#155724; padding:10px; border-radius:5px; margin-bottom:15px;">
    🟢 <b>Credenciales Válidas:</b> Servidor activo <code><?= $domain ?></code>
</div>

<?= $mensaje ?>

<form action="test_openpay.php" method="POST" style="max-width:400px; display:flex; flex-direction:column; gap:10px; margin-top:15px;">
    <label>Cliente: <input type="text" name="nombre" value="Angel Larios" required></label>
    <label>Email: <input type="email" name="email" value="angel306070@gmail.com" required></label>
    <label>Concepto: <input type="text" name="descripcion" value="Compra de Camisa" required></label>
    <label>Monto ($ MXN): <input type="number" step="0.01" name="monto" value="299.99" required></label>
    <button type="submit" name="procesar_pago" style="padding:10px; background:#007bff; color:white; border:none; cursor:pointer;">
        Procesar Pago Simulado
    </button>
</form>