<?php
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
    $nombre   = trim($_POST['nombre']);
    $email    = trim($_POST['email']); // Se guardará en la columna 'username'
    $password = $_POST['password'];

    if (!empty($nombre) && !empty($email) && !empty($password)) {
        
        // Verificar si el usuario/correo ya existe en la columna 'username'
        $checkUser = $conn->prepare("SELECT id FROM usuarios WHERE username = ?");
        $checkUser->bind_param("s", $email);
        $checkUser->execute();
        $result = $checkUser->get_result();

        if ($result->num_rows > 0) {
            echo "<script>
                    alert('El usuario/correo ya está registrado.');
                    window.location.href = 'index.php';
                  </script>";
        } else {
            // Insertar el nuevo usuario en tu tabla
            $stmt = $conn->prepare("INSERT INTO usuarios (nombre, username, password, rol, estatus) VALUES (?, ?, ?, 2, 1)");
            $stmt->bind_param("sss", $nombre, $email, $password);

            if ($stmt->execute()) {
                echo "<script>
                        alert('¡Registro exitoso! Ya puedes ingresar.');
                        window.location.href = 'index.php';
                      </script>";
            } else {
                echo "Error al registrar: " . $conn->error;
            }
            $stmt->close();
        }
        $checkUser->close();
    } else {
        echo "<script>
                alert('Por favor completa todos los campos.');
                window.location.href = 'index.php';
              </script>";
    }
}
$conn->close();
?>