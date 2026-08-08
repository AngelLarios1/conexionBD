<?php
session_start();

// Configuración de la base de datos con el puerto 3323
$host = "127.0.0.1";
$user = "root";
$pass = "";
$db   = "practica";
$port = 3323;

$conn = new mysqli($host, $user, $pass, $db, $port);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email    = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (!empty($email) && !empty($password)) {
        
        // Buscar el usuario en la columna 'username'
        $stmt = $conn->prepare("SELECT id, nombre, password FROM usuarios WHERE username = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            // Verificar la contraseña (admite tanto hash como texto plano)
            if ($password === $user['password'] || password_verify($password, $user['password'])) {
                $_SESSION['usuario_id']     = $user['id'];
                $_SESSION['usuario_nombre'] = $user['nombre'];

                echo "<script>
                        alert('¡Bienvenido de nuevo, " . addslashes($user['nombre']) . "!');
                        window.location.href = 'index.php';
                      </script>";
            } else {
                echo "<script>
                        alert('Contraseña incorrecta.');
                        window.location.href = 'index.php';
                      </script>";
            }
        } else {
            echo "<script>
                    alert('El usuario/correo no está registrado.');
                    window.location.href = 'index.php';
                  </script>";
        }
        $stmt->close();
    } else {
        echo "<script>
                alert('Por favor completa todos los campos.');
                window.location.href = 'index.php';
              </script>";
    }
}
$conn->close();
?>