<?php
// pago_efectivo.php
require_once __DIR__ . '/includes/security.php';   // cabeceras + sesión segura + CSRF
require_once 'dbcon.php';

// Credenciales desde .env (antes estaban escritas en el código fuente)
$merchant_id = env_required('OPENPAY_ID');
$private_key = env_required('OPENPAY_SK');
$domain      = $_ENV['OPENPAY_DOMAIN'] ?? 'sandbox-api.openpay.mx';

$mensaje = '';
$fichaPago = null;

if (isset($_POST['generar_ficha'])) {
    csrf_require();

    $monto       = (float) ($_POST['monto'] ?? 0);
    $nombre      = trim($_POST['nombre'] ?? '');
    $email       = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
    $descripcion = trim($_POST['descripcion'] ?? '');

    // TODO (pendiente): recalcular el monto en el servidor (hallazgo P-01 del reporte)
    if (!is_finite($monto) || $monto <= 0 || $nombre === '' || $email === false || $descripcion === '') {
        http_response_code(400);
        exit('Datos inválidos.');
    }

    // Estructura para cobro en tiendas de conveniencia (Paynet)
    $bodyData = [
        'method'      => 'store',
        'amount'      => $monto,
        'description' => $descripcion,
        'customer'    => [
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
        $referencia    = $resData['payment_method']['reference'];
        $barcodeUrl    = $resData['payment_method']['barcode_url'];

        // Guardar pedido con estatus "Pendiente"
        $sql  = "INSERT INTO pedidos (total, estatus, fecha) VALUES (:total, 'Pendiente de Pago', NOW())";
        $stmt = $con->prepare($sql);
        $stmt->execute([':total' => $monto]);

        $fichaPago = [
            'id'        => $transaccionId,
            'referencia'=> $referencia,
            'barcode'   => $barcodeUrl,
            'monto'     => $monto
        ];
    } else {
        $errorMsg = $resData['description'] ?? 'Error generando ficha de pago.';
        $mensaje  = "<div style='color:red; background:#f8d7da; padding:10px; border-radius:4px;'>❌ " . e($errorMsg) . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Pago en Efectivo - Paynet</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f6f9; padding: 20px; }
        .box { max-width: 450px; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 20px; }
        .field { display: flex; flex-direction: column; gap: 5px; margin-bottom: 10px; }
        .field input { padding: 8px; border: 1px solid #ccc; border-radius: 4px; }
        .btn { background: #28a745; color: white; border: none; padding: 10px; font-weight: bold; cursor: pointer; border-radius: 4px; width: 100%; }
        .ficha { border: 2px dashed #007bff; background: #f0f8ff; padding: 15px; text-align: center; border-radius: 8px; }
        .referencia { font-size: 20px; font-weight: bold; letter-spacing: 2px; color: #007bff; margin: 10px 0; }
    </style>
</head>
<body>

<div class="box">
    <h2>Pago en Efectivo (Tiendas / Paynet)</h2>
    <?= $mensaje ?>

    <form method="POST">
        <?= csrf_field() ?>
        <div class="field">
            <label>Nombre:</label>
            <input type="text" name="nombre" value="Angel Larios" required>
        </div>
        <div class="field">
            <label>Correo Electrónico:</label>
            <input type="email" name="email" value="angel306070@gmail.com" required>
        </div>
        <div class="field">
            <label>Concepto:</label>
            <input type="text" name="descripcion" value="Compra de Camisa" required>
        </div>
        <div class="field">
            <label>Monto ($ MXN):</label>
            <input type="number" step="0.01" name="monto" value="299.99" required>
        </div>
        <button type="submit" name="generar_ficha" class="btn">Generar Ficha de Pago</button>
    </form>
</div>

<?php if ($fichaPago): ?>
    <div class="box ficha">
        <h3>✅ Ficha de Pago Generada</h3>
        <p>Monto a pagar: <b>$<?= number_format($fichaPago['monto'], 2) ?> MXN</b></p>
        <p>Dicta el siguiente número de referencia en la caja:</p>
        <div class="referencia"><?= e(chunk_split($fichaPago['referencia'], 4, ' ')) ?></div>
        <p>O muestra este código de barras:</p>
        <img src="<?= e($fichaPago['barcode']) ?>" alt="Código de barras Paynet" style="max-width:100%;">
        <p><small>ID Transacción: <?= e($fichaPago['id']) ?></small></p>
    </div>
<?php endif; ?>

</body>
</html>