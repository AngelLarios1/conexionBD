<?php
$transactionId = $_GET['id'] ?? null;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>¡Gracias por tu compra!</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center vh-100">
    <div class="container text-center">
        <div class="card p-5 shadow-sm mx-auto" style="max-width: 500px;">
            <div class="display-1 text-success mb-3">✓</div>
            <h1 class="h3 fw-bold mb-2">¡Pago Realizado con Éxito!</h1>
            <p class="text-muted">Hemos recibido tu pedido correctamente.</p>
            
            <?php if ($transactionId): ?>
                <div class="alert alert-secondary text-break my-3">
                    <small class="d-block text-muted">ID de Transacción Openpay:</small>
                    <strong><?= htmlspecialchars($transactionId) ?></strong>
                </div>
            <?php endif; ?>

            <a href="index.php" class="btn btn-primary mt-3">Volver a la tienda</a>
        </div>
    </div>
</body>
</html>