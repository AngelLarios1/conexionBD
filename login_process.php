<?php
// Iniciar la sesión de PHP
session_start();

// Importar la conexión estandarizada
require_once 'dbcon.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $correo     = trim($_POST['correo'] ?? '');
    $contrasena = trim($_POST['contrasena'] ?? '');

    if (!empty($correo) && !empty($contrasena)) {
        try {
            // Consulta preparada con la variable de conexión $con
            $stmt = $con->prepare("SELECT * FROM usuarios WHERE username = :correo LIMIT 1");
            $stmt->execute(['correo' => $correo]);
            $usuario = $stmt->fetch();

            if ($usuario) {
                // Soportar verificación encriptada y contraseña plana legacy
                $pwdValida = password_verify($contrasena, $usuario['password']) || ($contrasena === $usuario['password']);

                if ($pwdValida) {
                    // Guardar datos en la sesión
                    $_SESSION['usuario_id']     = $usuario['id'];
                    $_SESSION['usuario_nombre'] = $usuario['nombre'] . ' ' . $usuario['apellidopaterno'];
                    $_SESSION['usuario_rol']    = ($usuario['rol'] == 1) ? 'Administrador' : 'Colaborador';

                    // Redirigir al dashboard principal
                    header("Location: dashboard.php");
                    exit;
                }
            }
        } catch (\PDOException $e) {
            // Manejar error de consulta
            header("Location: index.php?error=db");
            exit;
        }
    }

    // Si la autenticación falla, regresar al login con alerta
    header("Location: index.php?error=1");
    exit;
}
?>