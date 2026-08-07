<?php
session_start();

// 1. Validar inicio de sesión
if (!isset($_SESSION['usuario_id'])) {
    die("<div style='text-align:center; margin-top:50px; font-family:sans-serif;'>
            <h2>❌ SESIÓN NO INICIADA</h2>
            <p>Por favor, debes iniciar sesión primero.</p>
            <a href='index.php'>Ir al Login</a>
         </div>");
}

// 2. Cargar la conexión estandarizada
require_once 'dbcon.php';

// 3. Consultar listado de usuarios
try {
    $stmt = $con->query("SELECT id, nombre, apellidopaterno, username, rol FROM usuarios");
    $usuarios = $stmt->fetchAll();
} catch (\PDOException $e) {
    die("Error al consultar la base de datos: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Fastpack</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/menu.css">
    <link rel="stylesheet" href="css/sidenav.css">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <span class="text-muted text-uppercase small">Panel de Control</span>
            <div class="d-flex align-items-center gap-3">
                <h1 class="m-0">USUARIOS</h1>
                <a href="crear_usuario.php" class="btn btn-success btn-sm mt-1">
                    <i class="bi bi-person-plus-fill"></i> Registrar Usuario
                </a>
            </div>
        </div>
        <div>
            <span class="me-3 text-secondary">Bienvenido, <strong><?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?></strong>!</span>
            <span class="badge bg-info text-dark fs-6 me-2">Rol: <?php echo htmlspecialchars($_SESSION['usuario_rol']); ?></span>
            <a href="logout.php" class="btn btn-danger btn-sm">Cerrar Sesión</a>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-4">
            <h3 class="card-title mb-4">Fastpack Industrial</h3>
            
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th style="width: 5%;">#</th>
                            <th>Nombre</th>
                            <th>Correo</th>
                            <th>Rol</th>
                            <th style="width: 15%;" class="text-center">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($usuarios) > 0): ?>
                            <?php foreach ($usuarios as $user): ?>
                                <tr>
                                    <td><strong><?php echo $user['id']; ?></strong></td>
                                    <td><?php echo htmlspecialchars($user['nombre'] . ' ' . $user['apellidopaterno']); ?></td>
                                    <td><?php echo htmlspecialchars($user['username']); ?></td>
                                    <td>
                                        <span class="badge <?php echo $user['rol'] == 1 ? 'bg-primary' : 'bg-secondary'; ?>">
                                            <?php echo $user['rol'] == 1 ? 'Administrador' : 'Colaborador'; ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <a href="editar_usuario.php?id=<?php echo $user['id']; ?>" class="btn btn-sm btn-warning me-1" title="Editar">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <button class="btn btn-sm btn-danger" title="Eliminar">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted">No hay usuarios registrados.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <div class="text-muted small mt-2">
                Total de registros: <?php echo count($usuarios); ?>
            </div>
        </div>
    </div>
</div>

</body>
</html>