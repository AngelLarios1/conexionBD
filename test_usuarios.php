<?php
// test_usuarios.php
require_once 'dbcon.php';

if (isset($_POST['crear_usuario'])) {
    $nombre   = $_POST['nombre'] ?? '';
    $paterno  = $_POST['paterno'] ?? '';
    $materno  = $_POST['materno'] ?? '';
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? ''; // O password_hash si usas cifrado
    $rol      = (int)($_POST['rol'] ?? 2);
    $estatus  = 1;

    try {
        $sql = "INSERT INTO usuarios (nombre, apellidopaterno, apellidomaterno, username, password, rol, estatus, medio) 
                VALUES (:nombre, :paterno, :materno, :username, :password, :rol, :estatus, '')";
        
        $stmt = $con->prepare($sql);
        $stmt->execute([
            ':nombre'   => $nombre,
            ':paterno'  => $paterno,
            ':materno'  => $materno,
            ':username' => $username,
            ':password' => $password,
            ':rol'      => $rol,
            ':estatus'  => $estatus
        ]);

        echo "<div style='background:#d4edda; color:#155724; padding:12px; border-radius:5px; margin-bottom:15px;'>";
        echo "✅ <b>¡Usuario registrado con éxito!</b> ID: " . $con->lastInsertId();
        echo "</div>";
    } catch (PDOException $e) {
        echo "<div style='background:#f8d7da; color:#721c24; padding:12px; border-radius:5px; margin-bottom:15px;'>";
        echo "❌ Error: " . $e->getMessage();
        echo "</div>";
    }
}
?>

<h2>Gestión de Usuarios</h2>

<form action="test_usuarios.php" method="POST" style="max-width:400px; display:flex; flex-direction:column; gap:8px; margin-bottom:20px;">
    <label>Nombre: <input type="text" name="nombre" value="Carlos" required></label>
    <label>Apellido Paterno: <input type="text" name="paterno" value="López" required></label>
    <label>Apellido Materno: <input type="text" name="materno" value="Gómez"></label>
    <label>Correo / Username: <input type="email" name="username" value="carlos@prueba.com" required></label>
    <label>Contraseña: <input type="text" name="password" value="123456" required></label>
    <label>Rol: 
        <select name="rol">
            <option value="1">1 - Administrador</option>
            <option value="2" selected>2 - Cliente</option>
        </select>
    </label>
    <button type="submit" name="crear_usuario" style="padding:10px; background:#28a745; color:white; border:none; cursor:pointer;">
        Crear Usuario
    </button>
</form>

<hr>
<h3>Tabla de Usuarios Registrados:</h3>

<?php
$stmt = $con->query("SELECT id, nombre, apellidopaterno, apellidomaterno, username, rol, estatus FROM usuarios");
$usuarios = $stmt->fetchAll();

if ($usuarios) {
    echo "<table border='1' cellpadding='8' style='border-collapse:collapse; width:100%; text-align:left;'>";
    echo "<tr style='background:#f2f2f2;'><th>ID</th><th>Nombre Completo</th><th>Username / Email</th><th>Rol</th><th>Estatus</th></tr>";
    foreach ($usuarios as $u) {
        $nombreCompleto = trim("{$u['nombre']} {$u['apellidopaterno']} {$u['apellidomaterno']}");
        echo "<tr>";
        echo "<td>{$u['id']}</td>";
        echo "<td>{$nombreCompleto}</td>";
        echo "<td>{$u['username']}</td>";
        echo "<td>" . ($u['rol'] == 1 ? 'Admin' : 'Cliente') . "</td>";
        echo "<td>" . ($u['estatus'] == 1 ? 'Activo' : 'Inactivo') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
}
?>