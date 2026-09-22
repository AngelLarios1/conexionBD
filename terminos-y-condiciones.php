<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
header("Content-Type: text/html; charset=UTF-8");
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <link rel="shortcut icon" type="image/x-icon" href="images/ics.ico">
    <title>Términos y Condiciones | EliteStore</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-0evHe/X+R7YkIZDRvuzKMRqM+OrBnVFBL6DOitfPri4tjfHxaWutUpFmBp4vmVor" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="shortcut icon" href="images/ico.ico" type="image/x-icon">
    <style>
        .legal-container { max-width: 860px; margin: 130px auto 80px; padding: 0 20px; }
        .legal-container h1 { font-size: 1.8rem; font-weight: 700; margin-bottom: 25px; }
        .legal-container h2 { font-size: 1.15rem; font-weight: 700; margin-top: 30px; margin-bottom: 10px; color: #1F4E79; }
        .legal-container p, .legal-container li { font-size: 0.98rem; line-height: 1.6; color: #333; }
    </style>
</head>

<body style="background-color: #f5f5f5;">

    <?php include 'includes/menu.php'; ?>

    <div class="legal-container">
        <h1>Términos y Condiciones de Uso y Deslinde de Responsabilidad</h1>

        <h2>1. Aceptación de Términos</h2>
        <p>Al acceder, navegar o realizar compras en este sitio web, el usuario acepta de manera expresa y sin reservas los presentes Términos y Condiciones. Si no se encuentra de acuerdo con alguno de los puntos redactados, deberá abstenerse de utilizar la plataforma.</p>

        <h2>2. Cuentas de Usuario y Gestión de Credenciales</h2>
        <p>El usuario es el único responsable de mantener la confidencialidad de sus credenciales de acceso (correo electrónico y contraseña). La empresa no se responsabiliza por pérdidas, daños o compras no autorizadas derivadas de la negligencia en el resguardo o descuido de las contraseñas por parte del usuario. En caso de extravío o robo de credenciales, el usuario deberá utilizar las herramientas automatizadas de recuperación de contraseña habilitadas en el sistema.</p>

        <h2>3. Disponibilidad del Servicio y Exclusión de Garantías</h2>
        <p>El sitio web realiza esfuerzos razonables para mantener la disponibilidad del servicio las 24 horas del día. Sin embargo, no se garantiza el acceso ininterrumpido o libre de errores. El sitio web se deslinda de responsabilidad por caídas de servidor, mantenimiento programado, fallas de la red de internet, ataques informáticos o eventos de fuerza mayor que interrumpan temporalmente la navegación o el procesamiento de órdenes.</p>

        <h2>4. Procesamiento de Pagos y Responsabilidad Financiera</h2>
        <p>Los pagos con tarjeta de crédito o débito son procesados a través de la infraestructura tokenizada de Openpay. El sitio web no se hace responsable por rechazos bancarios, bloqueos preventivos por fraude de la institución emisora o inconsistencias en los datos proporcionados por el usuario.</p>

        <h2>5. Políticas de Reembolso, Devolución y Cancelación</h2>
        <ul>
            <li><strong>Reembolsos:</strong> Proceden únicamente en aquellos casos donde se demuestre defecto de fábrica comprobable en el producto o incumplimiento en el envío. Las solicitudes deben enviarse al correo de soporte dentro de los primeros 5 días hábiles tras recibir el pedido.</li>
            <li><strong>Devoluciones:</strong> El producto debe devolverse en su empaque original, sellado y en condiciones óptimas. Los gastos de envío por devolución por arrepentimiento o error del cliente correrán por cuenta del usuario.</li>
        </ul>

        <h2>6. Propiedad Intelectual</h2>
        <p>Todos los contenidos presentados en la tienda (marcas, logotipos, código fuente, diseño de interfaz y fotografías) son propiedad exclusiva del titular o cuentan con licencias para su explotación. Queda estrictamente prohibida la reproducción parcial o total sin autorización expresa por escrito.</p>

        <h2>7. Legislación y Jurisdicción</h2>
        <p>Para la resolución de cualquier controversia relativa al presente sitio web, las partes se someten a las leyes federales de los Estados Unidos Mexicanos y a los tribunales competentes de la ciudad de Aguascalientes, Aguascalientes, renunciando a cualquier otro fuero que por sus domicilios presentes o futuros pudiera corresponderles.</p>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
