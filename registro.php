<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/dbcon.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email    = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (!empty($email) && !empty($password)) {
        try {
            $stmt = $con->prepare("SELECT id, nombre, password FROM usuarios WHERE username = :email");
            $stmt->execute([':email' => $email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                if ($password === $user['password'] || password_verify($password, $user['password'])) {
                    $_SESSION['usuario_id']     = $user['id'];
                    $_SESSION['usuario_nombre'] = $user['nombre'];

                    echo "<script>
                            alert('¡Bienvenido de nuevo, " . addslashes($user['nombre']) . "!');
                            window.location.href = 'index.php';
                          </script>";
                    exit;
                } else {
                    echo "<script>
                            alert('Contraseña incorrecta.');
                            window.location.href = 'index.php';
                          </script>";
                    exit;
                }
            } else {
                // Si el usuario no existe, lanza la alerta y redirige de inmediato
                echo "<script>
                        alert('El usuario no está registrado.');
                        window.location.href = 'index.php';
                      </script>";
                exit;
            }
        } catch (PDOException $e) {
            die("Error en el sistema: " . $e->getMessage());
        }
    } else {
        echo "<script>
                alert('Por favor completa todos los campos.');
                window.location.href = 'index.php';
              </script>";
        exit;
    }
} else {
    header("Location: index.php");
    exit;
}
?>