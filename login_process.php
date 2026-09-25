<?php
require_once __DIR__ . '/includes/security.php';   // cabeceras + sesión segura
require_once 'vendor/autoload.php';
require_once 'dbcon.php';

// ================================
// FUNCIÓN DE DESCIFRADO OpenSSL
// ================================
function desencriptar(string $dato): string {
    $clave   = $_ENV['ENCRYPTION_KEY'];
    $metodo  = 'AES-256-CBC';
    $decoded = base64_decode($dato);
    $iv      = substr($decoded, 0, 16);
    $cifrado = substr($decoded, 16);
    $resultado = openssl_decrypt($cifrado, $metodo, $clave, 0, $iv);
    return $resultado !== false ? $resultado : $dato;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $correo     = trim($_POST['correo'] ?? '');
    $contrasena = trim($_POST['contrasena'] ?? '');

    if (!empty($correo) && !empty($contrasena)) {
        try {
            $stmt = $con->prepare("SELECT * FROM usuarios WHERE username = :correo LIMIT 1");
            $stmt->execute(['correo' => $correo]);
            $usuario = $stmt->fetch();

            if ($usuario && password_verify($contrasena, $usuario['password'])) {
                // Descifrar nombre y apellidos al iniciar sesión
                $nombre          = desencriptar($usuario['nombre']);
                $apellidopaterno = desencriptar($usuario['apellidopaterno']);

                session_regenerate_id(true);   // evita session fixation
                $_SESSION['usuario_id']     = $usuario['id'];
                $_SESSION['usuario_nombre'] = $nombre . ' ' . $apellidopaterno;
                $_SESSION['usuario_rol']    = ($usuario['rol'] == 1) ? 'Administrador' : 'Colaborador';

                header("Location: dashboard.php");
                exit;
            }
        } catch (\PDOException $e) {
            error_log('Error login_process: ' . $e->getMessage());
            header("Location: index.php?error=db");
            exit;
        }
    }

    header("Location: index.php?error=1");
    exit;
}
?>