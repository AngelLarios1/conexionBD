<?php
/**
 * includes/security.php
 *
 * Bootstrap de seguridad de la aplicación. Se incluye AL INICIO de cada página
 * (antes de cualquier salida) en lugar de llamar a session_start() directamente.
 *
 *  1. Cabeceras HTTP de seguridad (CSP, X-Frame-Options, nosniff, HSTS, ...)
 *  2. Sesión endurecida (HttpOnly, Secure, SameSite, strict mode, timeout)
 *  3. Protección CSRF (token por sesión)
 *  4. Helpers: e(), env_required(), require_login(), require_admin()
 *
 * Para endpoints sin navegador (p. ej. webhooks) define ANTES del require:
 *     define('APP_NO_SESSION', true);
 */

if (defined('APP_SECURITY_LOADED')) {
    return;
}
define('APP_SECURITY_LOADED', true);

/* ------------------------------------------------------------------
 * 1. HTTPS (soporta proxy inverso)
 * ------------------------------------------------------------------ */
function app_is_https(): bool
{
    if (!empty($_SERVER['HTTPS']) && strtolower((string) $_SERVER['HTTPS']) !== 'off') {
        return true;
    }
    return strtolower((string) ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '')) === 'https';
}

/* ------------------------------------------------------------------
 * 2. Cabeceras HTTP de seguridad
 * ------------------------------------------------------------------ */
function app_send_security_headers(): void
{
    if (headers_sent()) {
        return;
    }

    header_remove('X-Powered-By');

    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: SAMEORIGIN');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('Permissions-Policy: geolocation=(), microphone=(), camera=()');
    header('Cross-Origin-Opener-Policy: same-origin');

    if (app_is_https()) {
        header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
    }

    // CSP: restringe de dónde se cargan scripts, estilos, frames y formularios.
    // NOTA: 'unsafe-inline' sigue presente porque el proyecto usa <script> y
    // onclick="" en línea. Ver roadmap del reporte (migrar a nonces).
    $openpay = 'https://*.openpay.mx https://openpay.s3.amazonaws.com';
    $csp = [
        "default-src 'self'",
        "script-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://code.jquery.com "
            . "https://cdn.datatables.net https://cdnjs.cloudflare.com https://maps.googleapis.com $openpay",
        "style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://cdn.datatables.net "
            . "https://cdnjs.cloudflare.com https://fonts.googleapis.com",
        "font-src 'self' data: https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://fonts.gstatic.com",
        "img-src 'self' data: blob: https:",
        "media-src 'self'",
        "connect-src 'self' $openpay https://maps.googleapis.com",
        "frame-src 'self' $openpay",
        "object-src 'none'",
        "base-uri 'self'",
        "form-action 'self'",
        "frame-ancestors 'self'",
    ];
    header('Content-Security-Policy: ' . implode('; ', $csp));
}

/* ------------------------------------------------------------------
 * 3. Sesión endurecida
 * ------------------------------------------------------------------ */
function app_session_start(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }

    ini_set('session.use_strict_mode', '1');   // rechaza IDs de sesión no generados por el servidor
    ini_set('session.use_only_cookies', '1');  // nunca aceptar el ID por URL

    session_set_cookie_params([
        'lifetime' => 0,                 // cookie de sesión (se borra al cerrar el navegador)
        'path'     => '/',
        'secure'   => app_is_https(),    // solo por HTTPS
        'httponly' => true,              // inaccesible desde JavaScript (mitiga robo por XSS)
        'samesite' => 'Lax',             // no se envía en POST cross-site (mitiga CSRF)
    ]);

    session_start();

    // Caducidad por inactividad: 30 minutos
    $ahora = time();
    if (isset($_SESSION['_last_activity']) && ($ahora - (int) $_SESSION['_last_activity']) > 1800) {
        $_SESSION = [];
        session_regenerate_id(true);
    }
    $_SESSION['_last_activity'] = $ahora;
}

/* ------------------------------------------------------------------
 * 4. CSRF
 * ------------------------------------------------------------------ */
function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/** Campo oculto listo para pegar dentro de cualquier <form method="POST"> */
function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

function csrf_validate(?string $token): bool
{
    return is_string($token)
        && !empty($_SESSION['csrf_token'])
        && hash_equals($_SESSION['csrf_token'], $token);
}

/** Corta la petición con 403 si el token CSRF del POST no es válido */
function csrf_require(): void
{
    if (!csrf_validate($_POST['csrf_token'] ?? null)) {
        http_response_code(403);
        exit('Solicitud no válida (token CSRF ausente o expirado). Recarga la página e inténtalo de nuevo.');
    }
}

/* ------------------------------------------------------------------
 * 5. Helpers
 * ------------------------------------------------------------------ */

/** Escapa una cadena para HTML (contenido y atributos) */
function e($valor): string
{
    return htmlspecialchars((string) $valor, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** Lee una variable de entorno obligatoria; si falta, falla cerrado sin filtrar detalles al cliente */
function env_required(string $clave): string
{
    $valor = $_ENV[$clave] ?? getenv($clave);
    if ($valor === false || $valor === null || $valor === '') {
        error_log("Configuración faltante: la variable de entorno {$clave} no está definida.");
        http_response_code(500);
        exit('Error de configuración del servidor.');
    }
    return (string) $valor;
}

function require_login(string $redirigirA = 'index.php'): void
{
    if (empty($_SESSION['usuario_id'])) {
        header('Location: ' . $redirigirA);
        exit;
    }
}

function require_admin(string $redirigirA = 'index.php'): void
{
    require_login($redirigirA);
    if (($_SESSION['usuario_rol'] ?? '') !== 'Administrador') {
        http_response_code(403);
        exit('Acceso denegado.');
    }
}

/* ------------------------------------------------------------------
 * Arranque
 * ------------------------------------------------------------------ */
app_send_security_headers();
if (!defined('APP_NO_SESSION')) {
    app_session_start();
}
