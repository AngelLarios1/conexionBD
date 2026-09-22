<?php
session_start();

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] !== 'Administrador') {
    die("Acceso denegado. Solo los administradores pueden registrar usuarios.");
}

require_once 'vendor/autoload.php';
require_once 'dbcon.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// ================================
// FUNCIONES DE CIFRADO OpenSSL
// ================================
function encriptar(string $dato): string {
    $clave  = $_ENV['ENCRYPTION_KEY'];
    $metodo = 'AES-256-CBC';
    $iv     = random_bytes(16);
    $cifrado = openssl_encrypt($dato, $metodo, $clave, 0, $iv);
    return base64_encode($iv . $cifrado);
}

function desencriptar(string $dato): string {
    $clave   = $_ENV['ENCRYPTION_KEY'];
    $metodo  = 'AES-256-CBC';
    $decoded = base64_decode($dato);
    $iv      = substr($decoded, 0, 16);
    $cifrado = substr($decoded, 16);
    return openssl_decrypt($cifrado, $metodo, $clave, 0, $iv);
}

$mensaje     = "";
$tipo_alerta = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre          = trim($_POST['nombre']);
    $apellidopaterno = trim($_POST['apellidopaterno']);
    $apellidomaterno = trim($_POST['apellidomaterno']);
    $correo          = trim($_POST['correo']);
    $password_plana  = trim($_POST['password']);
    $rol             = intval($_POST['rol']);

    if (!empty($nombre) && !empty($correo) && !empty($password_plana)) {
        try {
            // Cifrar datos sensibles con OpenSSL
            $nombre_cifrado          = encriptar($nombre);
            $apellidopaterno_cifrado = encriptar($apellidopaterno);
            $apellidomaterno_cifrado = encriptar($apellidomaterno);

            // Hashear contraseña con BCRYPT
            $password_hash = password_hash($password_plana, PASSWORD_BCRYPT);

            $sql = "INSERT INTO usuarios (nombre, apellidopaterno, apellidomaterno, username, password, rol, estatus, medio) 
                    VALUES (:nombre, :apellidopaterno, :apellidomaterno, :correo, :password, :rol, 1, '')";

            $stmt = $con->prepare($sql);
            $stmt->execute([
                'nombre'          => $nombre_cifrado,
                'apellidopaterno' => $apellidopaterno_cifrado,
                'apellidomaterno' => $apellidomaterno_cifrado,
                'correo'          => $correo,
                'password'        => $password_hash,
                'rol'             => $rol
            ]);

            // Enviar correo con datos originales (no cifrados)
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'angeleduardolarios23@gmail.com';
                $mail->Password   = 'ooojiaksqhkxehop';
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = 587;
                $mail->CharSet    = 'UTF-8';

                $mail->setFrom('angeleduardolarios23@gmail.com', 'Fastpack Industrial');
                $mail->addAddress($correo, $nombre . ' ' . $apellidopaterno);

                $mail->isHTML(true);
                $mail->Subject = 'Bienvenido al Portal Fastpack - Tus accesos';
                $mail->Body = "
                <div style='font-family: Arial, sans-serif; max-width: 600px; margin: auto; padding: 20px; border: 1px solid #eee; border-radius: 10px;'>
                    <h2 style='color: #0d6efd; text-align: center;'>¡Bienvenido a Fastpack!</h2>
                    <p>Hola <strong>{$nombre}</strong>,</p>
                    <p>Se ha creado exitosamente tu cuenta. A continuación tus credenciales:</p>
                    <table style='width: 100%; margin: 20px 0; border-collapse: collapse;'>
                        <tr>
                            <td style='padding: 8px; border-bottom: 1px solid #ddd;'><strong>Usuario:</strong></td>
                            <td style='padding: 8px; border-bottom: 1px solid #ddd;'><code>{$correo}</code></td>
                        </tr>
                        <tr>
                            <td style='padding: 8px; border-bottom: 1px solid #ddd;'><strong>Contraseña temporal:</strong></td>
                            <td style='padding: 8px; border-bottom: 1px solid #ddd;'><code>{$password_plana}</code></td>
                        </tr>
                    </table>
                    <p style='color: #d9534f; font-size: 13px;'>* Por seguridad, cambia tu contraseña al ingresar por primera vez.</p>
                </div>";

                $mail->send();
                $mensaje     = "¡Usuario registrado con éxito y correo enviado!";
                $tipo_alerta = "success";
            } catch (Exception $e) {
                $mensaje     = "Usuario guardado, pero el correo no pudo enviarse. Error: {$mail->ErrorInfo}";
                $tipo_alerta = "warning";
            }

        } catch (\PDOException $e) {
            $mensaje     = "Error al guardar el usuario: " . $e->getMessage();
            $tipo_alerta = "danger";
        }
    } else {
        $mensaje     = "Por favor, completa todos los campos requeridos.";
        $tipo_alerta = "danger";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registrar Nuevo Usuario - Fastpack</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5" style="max-width: 600px;">
    <div class="card shadow">
        <div class="card-header bg-dark text-white text-center">
            <h3>Registrar Nuevo Usuario</h3>
        </div>
        <div class="card-body p-4">

            <?php if (!empty($mensaje)): ?>
                <div class="alert alert-<?= $tipo_alerta ?>" role="alert">
                    <?= $mensaje ?>
                </div>
            <?php endif; ?>

            <form action="crear_usuario.php" method="POST">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nombre *</label>
                        <input type="text" name="nombre" class="form-control" required placeholder="Ej. Juan">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Apellido Paterno *</label>
                        <input type="text" name="apellidopaterno" class="form-control" required placeholder="Ej. Pérez">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Apellido Materno</label>
                    <input type="text" name="apellidomaterno" class="form-control" placeholder="Ej. Gómez">
                </div>

                <div class="mb-3">
                    <label class="form-label">Correo Electrónico *</label>
                    <input type="email" name="correo" class="form-control" required placeholder="colaborador@fastpack.mx">
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Contraseña de acceso *</label>
                        <input type="password" name="password" class="form-control" required placeholder="Contraseña inicial">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Rol del Usuario *</label>
                        <select name="rol" class="form-select" required>
                            <option value="2">Colaborador</option>
                            <option value="1">Administrador</option>
                        </select>
                    </div>
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <a href="dashboard.php" class="btn btn-secondary">Volver al Dashboard</a>
                    <button type="submit" class="btn btn-primary">Guardar y Enviar Accesos</button>
                </div>
            </form>
        </div>
    </div>
</div>

</body>
</html>