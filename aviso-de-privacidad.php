<?php
require_once __DIR__ . '/includes/security.php';   // cabeceras + sesión segura + CSRF
header("Content-Type: text/html; charset=UTF-8");
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <link rel="shortcut icon" type="image/x-icon" href="images/ics.ico">
    <title>Aviso de Privacidad | EliteStore</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-0evHe/X+R7YkIZDRvuzKMRqM+OrBnVFBL6DOitfPri4tjfHxaWutUpFmBp4vmVor" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="shortcut icon" href="images/ico.ico" type="image/x-icon">
    <style>
        .legal-container { max-width: 860px; margin: 130px auto 80px; padding: 0 20px; }
        .legal-container h1 { font-size: 1.8rem; font-weight: 700; margin-bottom: 25px; }
        .legal-container h2 { font-size: 1.15rem; font-weight: 700; margin-top: 30px; margin-bottom: 10px; color: #1F4E79; }
        .legal-container p, .legal-container li { font-size: 0.98rem; line-height: 1.6; color: #333; }
        .legal-container .updated { color: #777; font-size: 0.85rem; margin-top: 40px; }
    </style>
</head>

<body style="background-color: #f5f5f5;">

    <?php include 'includes/menu.php'; ?>

    <div class="legal-container">
        <h1>Aviso de Privacidad Integral</h1>

        <h2>1. Identidad y Domicilio del Responsable</h2>
        <p>EliteStore, con domicilio ubicado en Universidad Tecnológica Metropolitana, Aguascalientes, México, es responsable de recabar, usar y proteger sus datos personales en apego a la Ley Federal de Protección de Datos Personales en Posesión de los Particulares (LFPDPPP) y demás disposiciones aplicables.</p>

        <h2>2. Datos Personales Recabados</h2>
        <p>Para llevar a cabo las operaciones de venta y servicio, recopilamos los siguientes datos personales:</p>
        <ul>
            <li><strong>Datos identificativos y de contacto:</strong> Nombre completo, dirección de correo electrónico y número telefónico.</li>
            <li><strong>Datos de entrega y facturación:</strong> Dirección de envío (calle, número, colonia, código postal, ciudad) y Registro Federal de Contribuyentes (RFC) en caso de requerir comprobante fiscal.</li>
            <li><strong>Datos de pago tokenizados:</strong> Tokens de transacción generados y provistos por Openpay. Se aclara que nuestra plataforma NUNCA almacena ni procesa números de tarjeta, códigos CVV ni fechas de vencimiento directamente en sus bases de datos.</li>
        </ul>

        <h2>3. Finalidades del Tratamiento</h2>
        <p>Sus datos personales serán utilizados para las siguientes finalidades primarias:</p>
        <ul>
            <li>Procesar, confirmar y enviar las órdenes de compra realizadas en la tienda.</li>
            <li>Procesar cobros electrónicos de forma segura mediante Openpay.</li>
            <li>Generar comprobantes fiscales o facturas electrónicas.</li>
            <li>Ofrecer soporte técnico, atención al cliente y seguimiento de garantías o devoluciones.</li>
        </ul>
        <p><strong>Finalidades secundarias (opcionales):</strong></p>
        <ul>
            <li>Envío de boletines informativos, promociones y novedades comerciales (previa autorización del titular).</li>
        </ul>

        <h2>4. Transferencia de Datos</h2>
        <p>Sus datos transaccionales son transferidos a Openpay, S.A. de C.V., pasarela de pagos certificada bajo el estándar PCI-DSS, con el único objetivo de validar y procesar el cobro correspondiente. No se realizan transferencias adicionales de datos a terceros sin consentimiento expreso, salvo las excepciones establecidas en el artículo 37 de la LFPDPPP.</p>

        <h2>5. Ejercicio de Derechos ARCO y Revocación del Consentimiento</h2>
        <p>Usted tiene derecho a Acceder, Rectificar, Cancelar u Oponerse (Derechos ARCO) al tratamiento de sus datos personales, así como a revocar el consentimiento otorgado. Para iniciar este proceso, deberá enviar una solicitud por escrito al correo electrónico: <strong>soporte@tudominio.com</strong>. La solicitud será respondida en un plazo máximo de 20 días hábiles.</p>

        <h2>6. Modificaciones al Aviso de Privacidad</h2>
        <p>Nos reservamos el derecho de modificar o actualizar este aviso en cualquier momento. Dichas modificaciones estarán siempre disponibles en el enlace ubicado en el pie de página (footer) de nuestro sitio web.</p>

        <p class="updated">Última actualización: Septiembre de 2026.</p>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
