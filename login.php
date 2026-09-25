<?php
require_once __DIR__ . '/includes/security.php';
require_once __DIR__ . '/dbcon.php';

/**
 * Muestra un mensaje y redirige. json_encode con JSON_HEX_* evita que el texto
 * (p. ej. el nombre del usuario) pueda cerrar la etiqueta <script> o la cadena JS.
 * (Antes se usaba addslashes(), que NO protege contra "</script>" ni saltos de línea.)
 */
function login_alerta(string $mensaje): void
{
    $m = json_encode($mensaje, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE);
    echo "<script>alert($m); window.location.href = 'index.php';</script>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

// 1) Protección CSRF
if (!csrf_validate($_POST['csrf_token'] ?? null)) {
    http_response_code(403);
    login_alerta('Tu sesión expiró. Recarga la página e inténtalo de nuevo.');
}

// 2) Anti fuerza bruta básica (por sesión): 5 fallos => bloqueo de 5 minutos
if (($_SESSION['login_bloqueado_hasta'] ?? 0) > time()) {
    login_alerta('Demasiados intentos fallidos. Espera unos minutos e inténtalo de nuevo.');
}

$email    = trim($_POST['email'] ?? '');
$password = trim($_POST['password'] ?? '');

if ($email === '' || $password === '') {
    login_alerta('Por favor completa todos los campos.');
}

try {
    $stmt = $con->prepare('SELECT id, nombre, password FROM usuarios WHERE username = :email LIMIT 1');
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    $valido = false;

    if ($user) {
        $guardado = (string) $user['password'];

        if (!empty(password_get_info($guardado)['algo'])) {
            // Contraseña ya hasheada (bcrypt/argon)
            $valido = password_verify($password, $guardado);
            if ($valido && password_needs_rehash($guardado, PASSWORD_DEFAULT)) {
                $upd = $con->prepare('UPDATE usuarios SET password = :p WHERE id = :id');
                $upd->execute([':p' => password_hash($password, PASSWORD_DEFAULT), ':id' => $user['id']]);
            }
        } else {
            // Registro LEGADO con contraseña en texto plano: se valida en tiempo constante
            // y se migra a hash en este mismo inicio de sesión (upgrade-on-login).
            $valido = hash_equals($guardado, $password);
            if ($valido) {
                $upd = $con->prepare('UPDATE usuarios SET password = :p WHERE id = :id');
                $upd->execute([':p' => password_hash($password, PASSWORD_DEFAULT), ':id' => $user['id']]);
            }
        }
    } else {
        // Usuario inexistente: gastar el mismo tiempo de CPU para no revelar si el correo existe
        password_verify($password, '$2y$10$abcdefghijklmnopqrstuuabcdefghijklmnopqrstuvwxyz01234');
    }

    if ($valido) {
        session_regenerate_id(true);               // evita session fixation
        unset($_SESSION['login_fallos'], $_SESSION['login_bloqueado_hasta'], $_SESSION['csrf_token']);
        $_SESSION['usuario_id']     = (int) $user['id'];
        $_SESSION['usuario_nombre'] = $user['nombre'];
        login_alerta('¡Bienvenido de nuevo, ' . $user['nombre'] . '!');
    }

    // Fallo: mensaje GENÉRICO (antes se distinguía "usuario no registrado" de "contraseña incorrecta")
    $_SESSION['login_fallos'] = (int) ($_SESSION['login_fallos'] ?? 0) + 1;
    if ($_SESSION['login_fallos'] >= 5) {
        $_SESSION['login_bloqueado_hasta'] = time() + 300;
        $_SESSION['login_fallos'] = 0;
    }
    login_alerta('Correo o contraseña incorrectos.');
} catch (PDOException $e) {
    // El detalle va al log del servidor, NO al navegador
    error_log('Error en login: ' . $e->getMessage());
    login_alerta('Error en el sistema. Inténtalo más tarde.');
}
