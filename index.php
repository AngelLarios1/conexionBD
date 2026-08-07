<?php
session_start();

// Redirigir al dashboard si ya existe una sesión activa
if (isset($_SESSION['usuario_id'])) {
    header("Location: dashboard.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Fastpack</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/styles.css">
</head>
<body class="bg-light d-flex align-items-center vh-100">

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-4">
            <div class="card shadow">
                <div class="card-body p-4">
                    <h3 class="text-center mb-4">FASTPACK</h3>
                    
                    <?php if (isset($_GET['error'])): ?>
                        <div class="alert alert-danger text-center" role="alert">
                            <?php 
                                if ($_GET['error'] === 'db') {
                                    echo "Error de conexión con la base de datos.";
                                } else {
                                    echo "Contraseña o correo incorrectos.";
                                }
                            ?>
                        </div>
                    <?php endif; ?>

                    <form action="login_process.php" method="POST">
                        <div class="mb-3">
                            <label for="correo" class="form-label">Correo Electrónico</label>
                            <input type="email" name="correo" id="correo" class="form-control" required placeholder="ejemplo@fastpack.mx">
                        </div>
                        <div class="mb-3">
                            <label for="contrasena" class="form-label">Contraseña</label>
                            <input type="password" name="contrasena" id="contrasena" class="form-control" required placeholder="********">
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Ingresar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>