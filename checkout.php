<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Checkout Unificado Openpay</title>
    <!-- jQuery y SDKs oficiales de Openpay -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://resources.openpay.mx/lib/openpay-js/1.2.38/openpay.v1.min.js"></script>
    <script src="https://resources.openpay.mx/lib/openpay-data-js/1.2.38/openpay-data.v1.min.js"></script>

    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f4f6f9; display: flex; justify-content: center; padding: 20px; }
        .checkout-card { background: white; width: 100%; max-width: 450px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); overflow: hidden; }
        
        /* Pestañas */
        .tabs { display: flex; background: #eef2f5; border-bottom: 1px solid #ddd; }
        .tab-btn { flex: 1; padding: 15px; border: none; background: transparent; font-weight: bold; cursor: pointer; color: #666; transition: 0.3s; }
        .tab-btn.active { background: white; color: #007bff; border-bottom: 3px solid #007bff; }
        
        /* Formularios */
        .tab-content { padding: 20px; display: none; }
        .tab-content.active { display: block; }
        .form-group { display: flex; flex-direction: column; margin-bottom: 15px; }
        .form-group label { font-size: 13px; font-weight: 600; margin-bottom: 5px; color: #333; }
        .form-group input { padding: 10px; border: 1px solid #ccc; border-radius: 5px; font-size: 14px; outline: none; }
        .form-group input:focus { border-color: #007bff; }
        
        .row { display: flex; gap: 10px; }
        .row > .form-group { flex: 1; }
        
        .card-header-label { display: flex; justify-content: space-between; }
        .card-brand { color: #007bff; font-weight: bold; }
        
        .btn-pay { width: 100%; background: #28a745; color: white; border: none; padding: 12px; font-size: 16px; font-weight: bold; border-radius: 5px; cursor: pointer; }
        .btn-pay:hover { background: #218838; }
        .btn-pay:disabled { background: #aaa; cursor: not-allowed; }
        
        .error-msg { color: #dc3545; font-size: 13px; background: #f8d7da; padding: 10px; border-radius: 5px; margin-bottom: 15px; display: none; }
    </style>
</head>
<body>

<div class="checkout-card">
    <!-- Pestañas de selección -->
    <div class="tabs">
        <button type="button" class="tab-btn active" onclick="switchTab('card')">💳 Tarjeta</button>
        <button type="button" class="tab-btn" onclick="switchTab('cash')">🏪 Efectivo (Paynet)</button>
    </div>

    <!-- PESTAÑA 1: PAGO CON TARJETA -->
    <div id="tab-card" class="tab-content active">
        <form id="card-payment-form" action="procesar_pago.php" method="POST">
            <input type="hidden" name="token_id" id="token_id">
            
            <div class="form-group">
                <label>Nombre en la Tarjeta</label>
                <input type="text" data-openpay-card="holder_name" value="Angel Larios" required>
            </div>

            <div class="form-group">
                <div class="card-header-label">
                    <label>Número de Tarjeta</label>
                    <span id="card-brand" class="card-brand">💳 Tarjeta</span>
                </div>
                <input type="text" id="card_number" data-openpay-card="card_number" maxlength="19" placeholder="4111 1111 1111 1111" autocomplete="off" required>
            </div>

            <div class="row">
                <div class="form-group">
                    <label>Expiración</label>
                    <div class="row">
                        <input type="text" id="exp_month" data-openpay-card="expiration_month" placeholder="MM" maxlength="2" required>
                        <input type="text" id="exp_year" data-openpay-card="expiration_year" placeholder="YY" maxlength="2" required>
                    </div>
                </div>
                <div class="form-group">
                    <label>CVV</label>
                    <input type="password" id="cvv" data-openpay-card="cvv2" placeholder="123" maxlength="4" autocomplete="off" required>
                </div>
            </div>

            <div class="form-group">
                <label>Monto ($ MXN)</label>
                <input type="number" name="monto" value="299.99" readonly style="background:#eef2f5;">
            </div>

            <div id="card-error" class="error-msg"></div>

            <button type="submit" id="btn-pay-card" class="btn-pay">Pagar $299.99 MXN</button>
        </form>
    </div>

    <!-- PESTAÑA 2: PAGO EN EFECTIVO -->
    <div id="tab-cash" class="tab-content">
        <form id="cash-payment-form" action="pago_efectivo.php" method="POST">
            <div class="form-group">
                <label>Nombre del Cliente</label>
                <input type="text" name="nombre" value="Angel Larios" required>
            </div>

            <div class="form-group">
                <label>Correo Electrónico</label>
                <input type="email" name="email" value="angel306070@gmail.com" required>
            </div>

            <div class="form-group">
                <label>Concepto</label>
                <input type="text" name="descripcion" value="Compra de Camisa" required>
            </div>

            <div class="form-group">
                <label>Monto ($ MXN)</label>
                <input type="number" name="monto" value="299.99" readonly style="background:#eef2f5;">
            </div>

            <button type="submit" name="generar_ficha" class="btn-pay" style="background:#007bff;">Generar Ficha Paynet</button>
        </form>
    </div>
</div>

<script>
    // Cambio entre pestañas
    function switchTab(type) {
        $('.tab-btn').removeClass('active');
        $('.tab-content').removeClass('active');

        if (type === 'card') {
            $('.tab-btn:eq(0)').addClass('active');
            $('#tab-card').addClass('active');
        } else {
            $('.tab-btn:eq(1)').addClass('active');
            $('#tab-cash').addClass('active');
        }
    }

    $(document).ready(function() {
        // Credenciales Openpay Sandbox
        OpenPay.setId('m43ygegi362bajsjomkm');
        OpenPay.setApiKey('pk_cabd2389d9144b9890a7a25dc2752ef');
        OpenPay.setSandboxMode(false);
        // Dispositivo Antifraude
        OpenPay.deviceData.setup("card-payment-form", "device_session_id");

        // Formato y marca de tarjeta
        $('#card_number').on('input', function() {
            let val = $(this).val().replace(/\D/g, '').substring(0, 16);
            let formatted = val.match(/.{1,4}/g)?.join(' ') || '';
            $(this).val(formatted);

            let brand = '💳 Tarjeta';
            if (/^4/.test(val)) brand = '💳 Visa';
            else if (/^5[1-5]|^2[2-7]/.test(val)) brand = '💳 Mastercard';
            else if (/^3[47]/.test(val)) brand = '💳 Amex';
            
            $('#card-brand').text(brand);
        });

        // Filtrar números en fecha y CVV
        $('#exp_month, #exp_year, #cvv').on('input', function() {
            $(this).val($(this).val().replace(/\D/g, ''));
        });

        // Procesar tokenización al enviar formulario de tarjeta
        $('#card-payment-form').submit(function(e) {
            e.preventDefault();
            $('#btn-pay-card').prop('disabled', true).text('Procesando...');
            $('#card-error').hide();

            OpenPay.token.extractFormAndCreate('card-payment-form', function(response) {
                $('#token_id').val(response.data.id);
                $('#card-payment-form')[0].submit();
            }, function(response) {
                var desc = response.data.description || response.message || 'Error en los datos de la tarjeta.';
                $('#card-error').text('❌ ' + desc).show();
                $('#btn-pay-card').prop('disabled', false).text('Pagar $299.99 MXN');
            });
        });
    });
</script>

</body>
</html>