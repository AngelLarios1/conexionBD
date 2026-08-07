<?php
require_once __DIR__ . '/vendor/autoload.php';

use Dotenv\Dotenv;

// Cargar variables de entorno sin lanzar excepción fatal si falta el archivo
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

$host     = $_ENV['DB_HOST'] ?? '127.0.0.1';
$port     = $_ENV['DB_PORT'] ?? '3306';
$db       = $_ENV['DB_NAME'] ?? '';
$user     = $_ENV['DB_USER'] ?? 'root';
$pass     = $_ENV['DB_PASS'] ?? '';
$charset  = 'utf8mb4';

$dsn = "mysql:host=$host;port=$port;dbname=$db;charset=$charset";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $con = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    // Registra el error en los logs del servidor
    error_log("Error de conexión PDO: " . $e->getMessage());
    die("Error crítico: No se pudo conectar a la base de datos. Por favor intenta más tarde.");
}
?>