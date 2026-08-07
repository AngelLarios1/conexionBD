<?php
session_start();

// Bloquear el acceso si no es administrador
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] !== 'Administrador') {
    die("Acceso denegado. Solo los administradores pueden registrar usuarios.");
}

// Cargar la conexión y PHPMailer mediante el autoload de Composer
require_once 'vendor/autoload.php';
require_once 'dbcon.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$mensaje = "";
$tipo_alerta = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $apellidopaterno = trim($_POST['apellidopaterno']);
    $apellidomaterno = trim($_POST['apellidomaterno']);
    $correo = trim($_POST['correo']);
    $password_plana = trim($_POST['password']); // La contraseña temporal que le enviaremos por correo
    $rol = intval($_POST['rol']); // 1 = Admin, 2 = Colaborador

    if (!empty($nombre) && !empty($correo) && !empty($password_plana)) {
        try {
            // 1. Insertar el usuario en la base de datos
            $sql = "INSERT INTO usuarios (nombre, apellidopaterno, apellidomaterno, username, password, rol, estatus, medio) 
                    VALUES (:nombre, :apellidopaterno, :apellidomaterno, :correo, :password, :rol, 1, '')";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                'nombre' => $nombre,
                'apellidopaterno' => $apellidopaterno,
                'apellidomaterno' => $apellidomaterno,
                'correo' => $correo,
                'password' => $password_plana, // Guardada temporalmente en plano o puedes usar password_hash()
                'rol' => $rol
            ]);

            // 2. Configurar y enviar el correo con PHPMailer
            $mail = new PHPMailer(true);

            try {
                // Configuración del Servidor SMTP (Ejemplo usando Gmail)
                // NOTA: Reemplaza con tus datos SMTP reales
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';                     // Servidor SMTP de Gmail (o el tuyo)
                $mail->SMTPAuth   = true;                                 // Habilitar autenticación SMTP
                $mail->Username   = 'angeleduardolarios23@gmail.com';                // Tu correo SMTP de salida
                $mail->Password   = 'ooojiaksqhkxehop';        // Tu contraseña de aplicación (no la común)
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;       // Cifrado TLS implícito
                $mail->Port       = 587;                                  // Puerto TCP para TLS
                $mail->CharSet    = 'UTF-8';

                // Destinatarios
                $mail->setFrom('angeleduardolarios23@gmail.com', 'Fastpack Industrial');
                $mail->addAddress($correo, $nombre . ' ' . $apellidopaterno); // Al nuevo usuario

                // Contenido del Correo (HTML)
                $mail->isHTML(true);
                $mail->Subject = 'Bienvenido al Portal Fastpack - Tus accesos';
                
                $mail->Body = "
                <div style='font-family: Arial, sans-serif; max-width: 600px; margin: auto; padding: 20px; border: 1px solid #eee; border-radius: 10px;'>
                    <h2 style='color: #0d6efd; text-align: center;'>¡Bienvenido a Fastpack!</h2>
                    <p>Hola <strong>{$nombre}</strong>,</p>
                    <p>Se ha creado exitosamente tu cuenta en nuestro sistema. A continuación, te compartimos tus credenciales de acceso:</p>
                    <table style='width: 100%; margin: 20px 0; border-collapse: collapse;'>
                        <tr>
                            <td style='padding: 8px; border-bottom: 1px solid #ddd;'><strong>Enlace de acceso:</strong></td>
                            <td style='padding: 8px; border-bottom: 1px solid #ddd;'><a href='http://localhost/miproyecto/'>Iniciar Sesión aquí</a></td>
                        </tr>
                        <tr>
                            <td style='padding: 8px; border-bottom: 1px solid #ddd;'><strong>Usuario (Correo):</strong></td>
                            <td style='padding: 8px; border-bottom: 1px solid #ddd;'><code>{$correo}</code></td>
                        </tr>
                        <tr>
                            <td style='padding: 8px; border-bottom: 1px solid #ddd;'><strong>Contraseña temporal:</strong></td>
                            <td style='padding: 8px; border-bottom: 1px solid #ddd;'><code>{$password_plana}</code></td>
                        </tr>
                    </table>
                    <p style='color: #d9534f; font-size: 13px;'>* Por motivos de seguridad, te sugerimos cambiar tu contraseña al ingresar por primera vez.</p>
                    <hr style='border: none; border-top: 1px solid #eee; margin: 20px 0;'>
                    <p style='font-size: 11px; color: #999; text-align: center;'>Este es un mensaje generado automáticamente. Por favor no respondas a este correo.</p>
                </div>";

                $mail->send();
                $mensaje = "¡Usuario registrado con éxito y correo de notificación enviado!";
                $tipo_alerta = "success";
            } catch (Exception $e) {
                $mensaje = "Usuario guardado en la Base de Datos, pero el correo no pudo enviarse. Error de PHPMailer: {$mail->ErrorInfo}";
                $tipo_alerta = "warning";
            }

        } catch (\PDOException $e) {
            $mensaje = "Error al guardar el usuario en la base de datos: " . $e->getMessage();
            $tipo_alerta = "danger";
        }
    } else {
        $mensaje = "Por favor, completa todos los campos requeridos.";
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
                <div class="alert alert-<?php echo $tipo_alerta; ?>" role="alert">
                    <?php echo $mensaje; ?>
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