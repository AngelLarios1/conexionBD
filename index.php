<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Tienda Online | Openpay</title>
    <!-- Bootstrap 5 CSS & FontAwesome Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Openpay SDKs -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://resources.openpay.mx/lib/openpay-js/1.2.38/openpay.v1.min.js"></script>
    <script src="https://resources.openpay.mx/lib/openpay-data-js/1.2.38/openpay-data.v1.min.js"></script>

    <style>
        .product-card { transition: transform 0.2s, box-shadow 0.2s; border: none; }
        .product-card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.1); }
        .badge-cart { position: absolute; top: -5px; right: -5px; }
        .nav-tabs .nav-link.active { font-weight: bold; border-bottom: 3px solid #0d6efd; }
    </style>
</head>
<body class="bg-light">

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold" href="#"><i class="fa-solid fa-bag-shopping me-2"></i>MiTienda</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"></button>
        
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link active" href="#">Inicio</a></li>
                <li class="nav-item"><a class="nav-link" href="#productos">Productos</a></li>
            </ul>
            
            <div class="d-flex gap-2 align-items-center">
                <!-- Botón Login -->
                <button class="btn btn-outline-light btn-sm" data-bs-toggle="modal" data-bs-target="#loginModal">
                    <i class="fa-solid fa-user me-1"></i> Iniciar Sesión
                </button>
                
                <!-- Botón Carrito -->
                <button class="btn btn-primary btn-sm position-relative" data-bs-toggle="modal" data-bs-target="#cartModal">
                    <i class="fa-solid fa-cart-shopping me-1"></i> Carrito
                    <span id="cart-count" class="badge bg-danger rounded-pill badge-cart">0</span>
                </button>
            </div>
        </div>
    </div>
</nav>

<!-- HERO BANNER -->
<div class="bg-primary text-white text-center py-5 shadow-sm" style="background: linear-gradient(135deg, #0d6efd, #0d3efd);">
    <div class="container">
        <h1 class="display-5 fw-bold">Grandes Ofertas de Temporada</h1>
        <p class="lead">Paga con tarjeta de crédito/débito o en efectivo con Paynet de forma segura.</p>
    </div>
</div>

<!-- CATÁLOGO DE PRODUCTOS -->
<div class="container my-5" id="productos">
    <h2 class="fw-bold mb-4"><i class="fa-solid fa-store text-primary me-2"></i>Catálogo de Productos</h2>
    
    <div class="row g-4">
        <!-- Producto 1 -->
        <div class="col-md-4">
            <div class="card h-100 product-card shadow-sm">
                <img src="https://images.unsplash.com/photo-1521572267360-ee0c2909d518?w=500" class="card-img-top" alt="Camisa">
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title fw-bold">Camisa Casual Azul</h5>
                    <p class="card-text text-muted flex-grow-1">100% Algodón, corte moderno y fresco para cualquier ocasión.</p>
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <span class="fs-4 fw-bold text-success">$299.99 MXN</span>
                        <button class="btn btn-primary" onclick="addToCart('Camisa Casual Azul', 299.99)">
                            <i class="fa-solid fa-cart-plus"></i> Agregar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Producto 2 -->
        <div class="col-md-4">
            <div class="card h-100 product-card shadow-sm">
                <img src="https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=500" class="card-img-top" alt="Tenis">
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title fw-bold">Tenis Deportivos Red</h5>
                    <p class="card-text text-muted flex-grow-1">Suela con amortiguación ligera, ideales para correr o uso diario.</p>
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <span class="fs-4 fw-bold text-success">$899.00 MXN</span>
                        <button class="btn btn-primary" onclick="addToCart('Tenis Deportivos Red', 899.00)">
                            <i class="fa-solid fa-cart-plus"></i> Agregar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Producto 3 -->
        <div class="col-md-4">
            <div class="card h-100 product-card shadow-sm">
                <img src="https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=500" class="card-img-top" alt="Reloj">
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title fw-bold">Reloj Elegante Smart</h5>
                    <p class="card-text text-muted flex-grow-1">Monitoreo de ritmo cardíaco, notificaciones y batería de larga duración.</p>
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <span class="fs-4 fw-bold text-success">$1,250.00 MXN</span>
                        <button class="btn btn-primary" onclick="addToCart('Reloj Elegante Smart', 1250.00)">
                            <i class="fa-solid fa-cart-plus"></i> Agregar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL LOGIN -->
<div class="modal fade" id="loginModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title fw-bold"><i class="fa-solid fa-right-to-bracket me-2"></i>Iniciar Sesión</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <form id="login-form">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Correo Electrónico</label>
                        <input type="email" class="form-control" placeholder="correo@ejemplo.com" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Contraseña</label>
                        <input type="password" class="form-control" placeholder="••••••••" required>
                    </div>
                    <button type="submit" class="btn btn-dark w-100 fw-bold py-2">Ingresar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- MODAL CARRITO / CHECKOUT -->
<div class="modal fade" id="cartModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold"><i class="fa-solid fa-cart-shopping me-2"></i>Tu Carrito de Compras</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <!-- Resumen de Pedido -->
                <div class="table-responsive mb-3">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Precio</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody id="cart-items">
                            <tr>
                                <td colspan="3" class="text-center text-muted">El carrito está vacío.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-between align-items-center p-3 bg-light rounded mb-4">
                    <span class="fs-5 fw-bold">Total a pagar:</span>
                    <span id="cart-total" class="fs-4 fw-bold text-success">$0.00 MXN</span>
                </div>

                <!-- PASARELA DE PAGO OPENPAY -->
                <div id="checkout-section" style="display:none;">
                    <hr class="my-4">
                    <h5 class="fw-bold mb-3"><i class="fa-solid fa-credit-card me-2"></i>Selecciona tu Método de Pago</h5>

                    <ul class="nav nav-tabs nav-justified mb-3" id="paymentTabs">
                        <li class="nav-item">
                            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-card"><i class="fa-solid fa-credit-card me-1"></i> Tarjeta</button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-cash"><i class="fa-solid fa-store me-1"></i> Efectivo (Paynet)</button>
                        </li>
                    </ul>

                    <div class="tab-content">
                        <!-- TARJETA -->
                        <div class="tab-pane fade show active" id="tab-card">
                            <form id="card-payment-form" action="procesar_pago.php" method="POST">
                                <input type="hidden" name="token_id" id="token_id">
                                <input type="hidden" name="monto" id="card-monto-val">

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Nombre en la Tarjeta</label>
                                    <input type="text" class="form-control" data-openpay-card="holder_name" value="Angel Larios" required>
                                </div>
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between">
                                        <label class="form-label fw-bold">Número de Tarjeta</label>
                                        <span id="card-brand" class="text-primary fw-bold">💳 Tarjeta</span>
                                    </div>
                                    <input type="text" id="card_number" class="form-control" data-openpay-card="card_number" maxlength="19" placeholder="4111 1111 1111 1111" required>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Expiración</label>
                                        <div class="d-flex gap-2">
                                            <input type="text" id="exp_month" class="form-control" data-openpay-card="expiration_month" placeholder="MM" maxlength="2" required>
                                            <input type="text" id="exp_year" class="form-control" data-openpay-card="expiration_year" placeholder="YY" maxlength="2" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">CVV</label>
                                        <input type="password" id="cvv" class="form-control" data-openpay-card="cvv2" placeholder="123" maxlength="4" required>
                                    </div>
                                </div>
                                <div id="card-error" class="alert alert-danger" style="display:none;"></div>
                                <button type="submit" id="btn-pay-card" class="btn btn-success w-100 py-2 fw-bold">Pagar Ahora</button>
                            </form>
                        </div>

                        <!-- EFECTIVO -->
                        <div class="tab-pane fade" id="tab-cash">
                            <form action="pago_efectivo.php" method="POST">
                                <input type="hidden" name="monto" id="cash-monto-val">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Nombre del Cliente</label>
                                    <input type="text" name="nombre" class="form-control" value="Angel Larios" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Correo Electrónico</label>
                                    <input type="email" name="email" class="form-control" value="angel306070@gmail.com" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Concepto</label>
                                    <input type="text" name="descripcion" class="form-control" value="Compra en Tienda Online" required>
                                </div>
                                <button type="submit" name="generar_ficha" class="btn btn-primary w-100 py-2 fw-bold">Generar Ficha Paynet</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JS Bootstrap & Lógica del Carrito -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/bootstrap.bundle.min.js"></script>

<script>
    let cart = [];

    function addToCart(title, price) {
        cart.push({ title, price });
        updateCartUI();
    }

    function removeFromCart(index) {
        cart.splice(index, 1);
        updateCartUI();
    }

    function updateCartUI() {
        $('#cart-count').text(cart.length);
        let itemsHtml = '';
        let total = 0;

        if (cart.length === 0) {
            itemsHtml = '<tr><td colspan="3" class="text-center text-muted">El carrito está vacío.</td></tr>';
            $('#checkout-section').hide();
        } else {
            cart.forEach((item, index) => {
                total += item.price;
                itemsHtml += `
                    <tr>
                        <td class="fw-bold">${item.title}</td>
                        <td class="text-success">$${item.price.toFixed(2)} MXN</td>
                        <td><button class="btn btn-danger btn-sm" onclick="removeFromCart(${index})"><i class="fa-solid fa-trash"></i></button></td>
                    </tr>
                `;
            });
            $('#checkout-section').show();
        }

        $('#cart-items').html(itemsHtml);
        $('#cart-total').text(`$${total.toFixed(2)} MXN`);
        $('#card-monto-val, #cash-monto-val').val(total.toFixed(2));
    }

    $(document).ready(function() {
        // Openpay Config
        OpenPay.setId('m43ygegi362bajsjomkm');
        OpenPay.setApiKey('pk_cabd2389d9144b9890a7a25dc2752ef');
        OpenPay.setSandboxMode(true);
        OpenPay.deviceData.setup("card-payment-form", "device_session_id");

        // Detección de tarjeta
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

        $('#exp_month, #exp_year, #cvv').on('input', function() {
            $(this).val($(this).val().replace(/\D/g, ''));
        });

        // Tokenización de Tarjeta
        $('#card-payment-form').submit(function(e) {
            e.preventDefault();
            $('#btn-pay-card').prop('disabled', true).text('Procesando Pago...');
            $('#card-error').hide();

            OpenPay.token.extractFormAndCreate('card-payment-form', function(response) {
                $('#token_id').val(response.data.id);
                $('#card-payment-form')[0].submit();
            }, function(response) {
                var desc = response.data.description || response.message || 'Error con los datos de la tarjeta.';
                $('#card-error').text('❌ ' + desc).show();
                $('#btn-pay-card').prop('disabled', false).text('Pagar Ahora');
            });
        });
    });
</script>
</body>
</html>