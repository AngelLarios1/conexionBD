<?php session_start(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tienda Elite | Moda & Tecnología</title>
    
    <!-- Google Fonts & FontAwesome -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Scripts base: jQuery, Bootstrap Bundle y Openpay SDKs -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://resources.openpay.mx/lib/openpay-js/1.2.38/openpay.v1.min.js"></script>
    <script src="https://resources.openpay.mx/lib/openpay-data-js/1.2.38/openpay-data.v1.min.js"></script>

    <style>
        :root {
            --primary-accent: #6366f1;
            --primary-hover: #4f46e5;
            --secondary-accent: #ec4899;
            --dark-surface: #0f172a;
            --card-border: #e2e8f0;
            --bg-body: #f8fafc;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-body);
            color: #1e293b;
        }

        /* Top Notification Bar */
        .top-bar {
            background: linear-gradient(90deg, #4f46e5 0%, #7c3aed 100%);
            color: #ffffff;
            font-size: 0.825rem;
            font-weight: 500;
        }

        /* Navbar estilo Glassmorphism */
        .navbar-custom {
            background-color: rgba(15, 23, 42, 0.92);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }

        .navbar-brand {
            font-weight: 800;
            letter-spacing: -0.5px;
        }

        /* Hero Section Premium */
        .hero-banner {
            background: radial-gradient(circle at top right, #312e81, #0f172a 70%);
            position: relative;
            overflow: hidden;
            border-radius: 0 0 2.5rem 2.5rem;
        }

        .hero-banner::after {
            content: '';
            position: absolute;
            width: 300px;
            height: 300px;
            background: rgba(99, 102, 241, 0.25);
            filter: blur(100px);
            border-radius: 50%;
            top: -50px;
            right: 10%;
        }

        /* Feature Badges Hero */
        .hero-feature-badge {
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(8px);
            border-radius: 50px;
            padding: 8px 18px;
            font-size: 0.85rem;
            color: #cbd5e1;
        }

        /* Tarjetas de Producto */
        .product-card {
            border: 1px solid var(--card-border);
            border-radius: 1.25rem;
            transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
            overflow: hidden;
            background: #ffffff;
        }

        .product-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 25px -5px rgba(15, 23, 42, 0.08), 0 8px 10px -6px rgba(15, 23, 42, 0.04);
            border-color: #cbd5e1;
        }

        .product-img-wrapper {
            position: relative;
            overflow: hidden;
            height: 250px;
            background-color: #f1f5f9;
        }

        .product-img-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.6s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .product-card:hover .product-img-wrapper img {
            transform: scale(1.08);
        }

        .badge-tag {
            position: absolute;
            top: 14px;
            left: 14px;
            font-size: 0.725rem;
            font-weight: 700;
            padding: 6px 14px;
            border-radius: 30px;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        /* Botones personalizados */
        .btn-custom-primary {
            background-color: var(--primary-accent);
            color: #fff;
            border: none;
            border-radius: 0.6rem;
            font-weight: 600;
            transition: all 0.2s;
        }

        .btn-custom-primary:hover {
            background-color: var(--primary-hover);
            color: #fff;
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.35);
        }

        .btn-added {
            background-color: #10b981 !important;
            color: #fff !important;
        }

        .badge-cart {
            position: absolute;
            top: -5px;
            right: -5px;
            font-size: 0.7rem;
            padding: 4px 7px;
        }

        /* Animación Contador */
        @keyframes bump {
            0% { transform: scale(1); }
            50% { transform: scale(1.35); }
            100% { transform: scale(1); }
        }
        .cart-bump { animation: bump 0.3s ease-out; }

        /* Modales */
        .modal-content {
            border: none;
            border-radius: 1.5rem;
            overflow: hidden;
        }

        .nav-pills .nav-link {
            border-radius: 0.75rem;
            font-weight: 600;
            color: #64748b;
        }

        .nav-pills .nav-link.active {
            background-color: var(--primary-accent);
        }

        /* Footer */
        footer {
            background-color: var(--dark-surface);
            color: #94a3b8;
            border-top: 1px solid rgba(255, 255, 255, 0.05);
        }
    </style>
</head>
<body>

<!-- TOP ANNOUNCEMENT BAR -->
<div class="top-bar text-center py-2">
    <i class="fa-solid fa-truck-fast me-2"></i> Envíos gratis a todo el país en compras mayores a $999 MXN
</div>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-dark navbar-custom sticky-top py-3">
    <div class="container">
        <a class="navbar-brand fs-4 d-flex align-items-center gap-2 text-white" href="#">
            <div class="bg-primary text-white rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                <i class="fa-solid fa-bolt fs-6"></i>
            </div>
            EliteStore
        </a>
        
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto ms-lg-4 fw-medium">
                <li class="nav-item"><a class="nav-link active" href="#">Inicio</a></li>
                <li class="nav-item"><a class="nav-link" href="#productos">Catálogo Completo</a></li>
            </ul>
            
            <div class="d-flex gap-3 align-items-center mt-3 mt-lg-0">
                <!-- SESIÓN DEL USUARIO -->
                <?php if (isset($_SESSION['usuario_nombre'])): ?>
                    <div class="dropdown">
                        <button class="btn btn-outline-light btn-sm px-3 py-2 rounded-pill dropdown-toggle d-flex align-items-center gap-2 border-secondary" type="button" data-bs-toggle="dropdown">
                            <i class="fa-solid fa-circle-user text-success fs-6"></i>
                            <span class="fw-semibold"><?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?></span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 mt-2 p-2 rounded-3">
                            <li class="px-3 py-1 text-muted small">Sesión activa</li>
                            <li class="px-3 pb-2 fw-bold border-bottom text-dark"><?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?></li>
                            <li><a class="dropdown-item text-danger mt-1 rounded-2" href="logout.php"><i class="fa-solid fa-right-from-bracket me-2"></i>Cerrar Sesión</a></li>
                        </ul>
                    </div>
                <?php else: ?>
                    <button type="button" class="btn btn-outline-light btn-sm px-3 py-2 rounded-pill fw-semibold border-secondary" data-bs-toggle="modal" data-bs-target="#authModal">
                        <i class="fa-regular fa-user me-1"></i> Mi Cuenta
                    </button>
                <?php endif; ?>
                
                <!-- CARRITO -->
                <button type="button" class="btn btn-primary btn-sm px-3 py-2 rounded-pill position-relative fw-semibold d-flex align-items-center gap-2" style="background-color: var(--primary-accent); border:none;" data-bs-toggle="modal" data-bs-target="#cartModal">
                    <i class="fa-solid fa-bag-shopping"></i>
                    <span>Carrito</span>
                    <span id="cart-count" class="badge bg-danger rounded-circle badge-cart">0</span>
                </button>
            </div>
        </div>
    </div>
</nav>

<!-- HERO BANNER -->
<div class="hero-banner text-white py-5 mb-5">
    <div class="container py-5 text-center">
        <div class="d-flex justify-content-center gap-2 mb-3 flex-wrap">
            <span class="hero-feature-badge"><i class="fa-solid fa-shield-halved text-success me-1"></i> Pagos Encriptados</span>
            <span class="hero-feature-badge"><i class="fa-solid fa-store text-warning me-1"></i> Depósitos Paynet</span>
            <span class="hero-feature-badge"><i class="fa-solid fa-rotate-left text-info me-1"></i> Devolución Garantizada</span>
        </div>
        <h1 class="display-3 fw-extrabold mb-3" style="letter-spacing: -1px;">Tecnología y Estilo <br><span class="text-transparent bg-clip-text" style="background: linear-gradient(90deg, #818cf8, #c084fc); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Sin Complicaciones</span></h1>
        <p class="lead opacity-75 max-w-2xl mx-auto mb-4 fs-5" style="max-width: 650px;">Explora nuestra selección exclusiva de productos con procesamiento seguro de pagos en tiempo real.</p>
        <a href="#productos" class="btn btn-light btn-lg px-4 py-3 rounded-pill fw-bold text-dark shadow-sm">
            Ver Catálogo <i class="fa-solid fa-arrow-down ms-2 fs-6"></i>
        </a>
    </div>
</div>

<!-- CATÁLOGO DE PRODUCTOS EXPANSIÓN -->
<div class="container mb-5" id="productos">
    <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
        <div>
            <h3 class="fw-bold m-0 text-dark"><i class="fa-solid fa-sparkles text-primary me-2"></i>Catálogo Destacado</h3>
            <p class="text-muted small m-0">Selecciona tus productos e inician tu orden de compra en un clic.</p>
        </div>
    </div>
    
    <div class="row g-4">
        <!-- Producto 1 -->
        <div class="col-6 col-md-4 col-lg-3">
            <div class="card product-card h-100 shadow-sm">
                <div class="product-img-wrapper">
                    <span class="badge bg-danger badge-tag">MÁS VENDIDO</span>
                    <img src="https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=500" alt="Audífonos">
                </div>
                <div class="card-body d-flex flex-column p-3">
                    <h6 class="card-title fw-bold text-dark mb-1">Audífonos Pro Wireless</h6>
                    <p class="card-text text-muted small flex-grow-1">Cancelación de ruido activa y batería de 30 hrs.</p>
                    <div class="mt-2">
                        <span class="fs-5 fw-bold text-dark d-block">$1,499.00 <small class="fs-6 text-muted">MXN</small></span>
                        <button type="button" class="btn btn-custom-primary w-100 mt-2 py-2 fs-7" onclick="addToCart(this, 'Audífonos Pro Wireless', 1499.00)">
                            <i class="fa-solid fa-cart-plus me-1"></i> Agregar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Producto 2 -->
        <div class="col-6 col-md-4 col-lg-3">
            <div class="card product-card h-100 shadow-sm">
                <div class="product-img-wrapper">
                    <span class="badge bg-success badge-tag">NUEVO</span>
                    <img src="https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=500" alt="Tenis">
                </div>
                <div class="card-body d-flex flex-column p-3">
                    <h6 class="card-title fw-bold text-dark mb-1">Tenis Sport Red Runner</h6>
                    <p class="card-text text-muted small flex-grow-1">Diseño ergonómico para máximo rendimiento.</p>
                    <div class="mt-2">
                        <span class="fs-5 fw-bold text-dark d-block">$899.00 <small class="fs-6 text-muted">MXN</small></span>
                        <button type="button" class="btn btn-custom-primary w-100 mt-2 py-2 fs-7" onclick="addToCart(this, 'Tenis Sport Red Runner', 899.00)">
                            <i class="fa-solid fa-cart-plus me-1"></i> Agregar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Producto 3 -->
        <div class="col-6 col-md-4 col-lg-3">
            <div class="card product-card h-100 shadow-sm">
                <div class="product-img-wrapper">
                    <span class="badge bg-warning text-dark badge-tag">OFERTA</span>
                    <img src="https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=500" alt="Reloj">
                </div>
                <div class="card-body d-flex flex-column p-3">
                    <h6 class="card-title fw-bold text-dark mb-1">Smartwatch Elite V2</h6>
                    <p class="card-text text-muted small flex-grow-1">Monitor de salud, GPS y pantalla AMOLED.</p>
                    <div class="mt-2">
                        <span class="fs-5 fw-bold text-dark d-block">$1,250.00 <small class="fs-6 text-muted">MXN</small></span>
                        <button type="button" class="btn btn-custom-primary w-100 mt-2 py-2 fs-7" onclick="addToCart(this, 'Smartwatch Elite V2', 1250.00)">
                            <i class="fa-solid fa-cart-plus me-1"></i> Agregar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Producto 4 -->
        <div class="col-6 col-md-4 col-lg-3">
            <div class="card product-card h-100 shadow-sm">
                <div class="product-img-wrapper">
                    <span class="badge bg-info text-dark badge-tag">POPULAR</span>
                    <img src="https://images.unsplash.com/photo-1583394838336-acd977736f90?w=500" alt="Gafas">
                </div>
                <div class="card-body d-flex flex-column p-3">
                    <h6 class="card-title fw-bold text-dark mb-1">Gafas de Sol Dark Horizon</h6>
                    <p class="card-text text-muted small flex-grow-1">Protección UV400 con armazón ultraligero.</p>
                    <div class="mt-2">
                        <span class="fs-5 fw-bold text-dark d-block">$450.00 <small class="fs-6 text-muted">MXN</small></span>
                        <button type="button" class="btn btn-custom-primary w-100 mt-2 py-2 fs-7" onclick="addToCart(this, 'Gafas de Sol Dark Horizon', 450.00)">
                            <i class="fa-solid fa-cart-plus me-1"></i> Agregar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Producto 5 -->
        <div class="col-6 col-md-4 col-lg-3">
            <div class="card product-card h-100 shadow-sm">
                <div class="product-img-wrapper">
                    <img src="https://images.unsplash.com/photo-1553062407-98eeb64c6a62?w=500" alt="Mochila">
                </div>
                <div class="card-body d-flex flex-column p-3">
                    <h6 class="card-title fw-bold text-dark mb-1">Mochila Ejecutiva Anti-Robo</h6>
                    <p class="card-text text-muted small flex-grow-1">Puerto de carga USB y material impermeable.</p>
                    <div class="mt-2">
                        <span class="fs-5 fw-bold text-dark d-block">$699.00 <small class="fs-6 text-muted">MXN</small></span>
                        <button type="button" class="btn btn-custom-primary w-100 mt-2 py-2 fs-7" onclick="addToCart(this, 'Mochila Ejecutiva Anti-Robo', 699.00)">
                            <i class="fa-solid fa-cart-plus me-1"></i> Agregar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Producto 6 -->
        <div class="col-6 col-md-4 col-lg-3">
            <div class="card product-card h-100 shadow-sm">
                <div class="product-img-wrapper">
                    <span class="badge bg-danger badge-tag">TOP</span>
                    <img src="https://images.unsplash.com/photo-1608231387042-66d1773070a5?w=500" alt="Tenis White">
                </div>
                <div class="card-body d-flex flex-column p-3">
                    <h6 class="card-title fw-bold text-dark mb-1">Tenis Urban White Class</h6>
                    <p class="card-text text-muted small flex-grow-1">Estilo minimalista en piel sintética suave.</p>
                    <div class="mt-2">
                        <span class="fs-5 fw-bold text-dark d-block">$950.00 <small class="fs-6 text-muted">MXN</small></span>
                        <button type="button" class="btn btn-custom-primary w-100 mt-2 py-2 fs-7" onclick="addToCart(this, 'Tenis Urban White Class', 950.00)">
                            <i class="fa-solid fa-cart-plus me-1"></i> Agregar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Producto 7 -->
        <div class="col-6 col-md-4 col-lg-3">
            <div class="card product-card h-100 shadow-sm">
                <div class="product-img-wrapper">
                    <img src="https://images.unsplash.com/photo-1585386959984-a4155224a1ad?w=500" alt="Perfume">
                </div>
                <div class="card-body d-flex flex-column p-3">
                    <h6 class="card-title fw-bold text-dark mb-1">Perfume Black Velvet 100ml</h6>
                    <p class="card-text text-muted small flex-grow-1">Fragancia amaderada de larga duración.</p>
                    <div class="mt-2">
                        <span class="fs-5 fw-bold text-dark d-block">$1,100.00 <small class="fs-6 text-muted">MXN</small></span>
                        <button type="button" class="btn btn-custom-primary w-100 mt-2 py-2 fs-7" onclick="addToCart(this, 'Perfume Black Velvet 100ml', 1100.00)">
                            <i class="fa-solid fa-cart-plus me-1"></i> Agregar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Producto 8 -->
        <div class="col-6 col-md-4 col-lg-3">
            <div class="card product-card h-100 shadow-sm">
                <div class="product-img-wrapper">
                    <span class="badge bg-success badge-tag">NUEVO</span>
                    <img src="https://images.unsplash.com/photo-1572635196237-14b3f281503f?w=500" alt="Lentes Moda">
                </div>
                <div class="card-body d-flex flex-column p-3">
                    <h6 class="card-title fw-bold text-dark mb-1">Lentes Retro Gold Edition</h6>
                    <p class="card-text text-muted small flex-grow-1">Montura metálica dorada y cristales UV.</p>
                    <div class="mt-2">
                        <span class="fs-5 fw-bold text-dark d-block">$520.00 <small class="fs-6 text-muted">MXN</small></span>
                        <button type="button" class="btn btn-custom-primary w-100 mt-2 py-2 fs-7" onclick="addToCart(this, 'Lentes Retro Gold Edition', 520.00)">
                            <i class="fa-solid fa-cart-plus me-1"></i> Agregar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Producto 9 -->
        <div class="col-6 col-md-4 col-lg-3">
            <div class="card product-card h-100 shadow-sm">
                <div class="product-img-wrapper">
                    <img src="https://images.unsplash.com/photo-1600185365483-26d7a4cc7519?w=500" alt="Sudadera">
                </div>
                <div class="card-body d-flex flex-column p-3">
                    <h6 class="card-title fw-bold text-dark mb-1">Sudadera Hoodie Minimalist</h6>
                    <p class="card-text text-muted small flex-grow-1">Algodón fleece ultra suave y abrigador.</p>
                    <div class="mt-2">
                        <span class="fs-5 fw-bold text-dark d-block">$599.00 <small class="fs-6 text-muted">MXN</small></span>
                        <button type="button" class="btn btn-custom-primary w-100 mt-2 py-2 fs-7" onclick="addToCart(this, 'Sudadera Hoodie Minimalist', 599.00)">
                            <i class="fa-solid fa-cart-plus me-1"></i> Agregar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Producto 10 -->
        <div class="col-6 col-md-4 col-lg-3">
            <div class="card product-card h-100 shadow-sm">
                <div class="product-img-wrapper">
                    <span class="badge bg-warning text-dark badge-tag">PROMO</span>
                    <img src="https://images.unsplash.com/photo-1546868871-7041f2a55e12?w=500" alt="Smartwatch Sport">
                </div>
                <div class="card-body d-flex flex-column p-3">
                    <h6 class="card-title fw-bold text-dark mb-1">Reloj Fitness Tracker</h6>
                    <p class="card-text text-muted small flex-grow-1">Sumergible IP68 con contador de calorías.</p>
                    <div class="mt-2">
                        <span class="fs-5 fw-bold text-dark d-block">$780.00 <small class="fs-6 text-muted">MXN</small></span>
                        <button type="button" class="btn btn-custom-primary w-100 mt-2 py-2 fs-7" onclick="addToCart(this, 'Reloj Fitness Tracker', 780.00)">
                            <i class="fa-solid fa-cart-plus me-1"></i> Agregar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Producto 11 -->
        <div class="col-6 col-md-4 col-lg-3">
            <div class="card product-card h-100 shadow-sm">
                <div class="product-img-wrapper">
                    <img src="https://images.unsplash.com/photo-1627123424574-724758594e93?w=500" alt="Cartera">
                </div>
                <div class="card-body d-flex flex-column p-3">
                    <h6 class="card-title fw-bold text-dark mb-1">Cartera de Piel Genuine Leather</h6>
                    <p class="card-text text-muted small flex-grow-1">Bloqueo RFID para seguridad de tarjetas.</p>
                    <div class="mt-2">
                        <span class="fs-5 fw-bold text-dark d-block">$380.00 <small class="fs-6 text-muted">MXN</small></span>
                        <button type="button" class="btn btn-custom-primary w-100 mt-2 py-2 fs-7" onclick="addToCart(this, 'Cartera de Piel Genuine Leather', 380.00)">
                            <i class="fa-solid fa-cart-plus me-1"></i> Agregar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Producto 12 -->
        <div class="col-6 col-md-4 col-lg-3">
            <div class="card product-card h-100 shadow-sm">
                <div class="product-img-wrapper">
                    <span class="badge bg-primary badge-tag">PREMIUM</span>
                    <img src="https://images.unsplash.com/photo-1544816155-12df9643f363?w=500" alt="Kindle/Ebook">
                </div>
                <div class="card-body d-flex flex-column p-3">
                    <h6 class="card-title fw-bold text-dark mb-1">Lector e-Reader Paper HD</h6>
                    <p class="card-text text-muted small flex-grow-1">Pantalla antirreflejo y luz cálida regulable.</p>
                    <div class="mt-2">
                        <span class="fs-5 fw-bold text-dark d-block">$2,199.00 <small class="fs-6 text-muted">MXN</small></span>
                        <button type="button" class="btn btn-custom-primary w-100 mt-2 py-2 fs-7" onclick="addToCart(this, 'Lector e-Reader Paper HD', 2199.00)">
                            <i class="fa-solid fa-cart-plus me-1"></i> Agregar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL MI CUENTA (LOGIN & REGISTRO UNIFICADOS) -->
<div class="modal fade" id="authModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg">
            <div class="modal-header border-0 pb-0">
                <ul class="nav nav-pills w-100 nav-justified bg-light p-1 rounded-3" id="authTabs">
                    <li class="nav-item">
                        <button class="nav-link active py-2" data-bs-toggle="pill" data-bs-target="#tab-login">Iniciar Sesión</button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link py-2" data-bs-toggle="pill" data-bs-target="#tab-register">Crear Cuenta</button>
                    </li>
                </ul>
                <button type="button" class="btn-close ms-2" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body p-4">
                <div class="tab-content">
                    <!-- FORMULARIO LOGIN -->
                    <div class="tab-pane fade show active" id="tab-login">
                        <form action="login.php" method="POST">
                            <div class="mb-3">
                                <label class="form-label fw-semibold text-dark">Correo Electrónico</label>
                                <input type="email" name="email" class="form-control form-control-lg fs-6" placeholder="ejemplo@correo.com" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold text-dark">Contraseña</label>
                                <input type="password" name="password" class="form-control form-control-lg fs-6" placeholder="••••••••" required>
                            </div>
                            <button type="submit" class="btn btn-custom-primary w-100 py-2.5 fw-semibold mt-2">Ingresar a mi Cuenta</button>
                        </form>
                    </div>

                    <!-- FORMULARIO REGISTRO -->
                    <div class="tab-pane fade" id="tab-register">
                        <form action="registro.php" method="POST">
                            <div class="mb-3">
                                <label class="form-label fw-semibold text-dark">Nombre Completo</label>
                                <input type="text" name="nombre" class="form-control form-control-lg fs-6" placeholder="Angel Larios" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold text-dark">Correo Electrónico</label>
                                <input type="email" name="email" class="form-control form-control-lg fs-6" placeholder="ejemplo@correo.com" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold text-dark">Contraseña</label>
                                <input type="password" name="password" class="form-control form-control-lg fs-6" placeholder="Mínimo 8 caracteres" minlength="8" required>
                            </div>
                            <button type="submit" class="btn btn-success w-100 py-2.5 fw-semibold mt-2" style="background-color: #10b981; border:none;">Registrarme Ahora</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL CARRITO / CHECKOUT -->
<div class="modal fade" id="cartModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow-lg">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold text-dark"><i class="fa-solid fa-bag-shopping me-2 text-primary"></i>Tu Carrito de Compras</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="table-responsive mb-3">
                    <table class="table align-middle">
                        <thead>
                            <tr class="text-muted small">
                                <th>PRODUCTO</th>
                                <th>PRECIO</th>
                                <th class="text-end">ACCIÓN</th>
                            </tr>
                        </thead>
                        <tbody id="cart-items">
                            <tr>
                                <td colspan="3" class="text-center text-muted py-4">El carrito está vacío.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-between align-items-center p-3 bg-light rounded-3 mb-4">
                    <span class="fs-5 fw-bold text-dark">Total a pagar:</span>
                    <span id="cart-total" class="fs-4 fw-bold text-success">$0.00 MXN</span>
                </div>

                <div id="checkout-section" style="display:none;">
                    <hr class="my-4">
                    <h5 class="fw-bold mb-3 text-dark"><i class="fa-solid fa-credit-card me-2 text-primary"></i>Método de Pago</h5>

                    <ul class="nav nav-pills nav-justified bg-light p-1 rounded-3 mb-3" id="paymentTabs">
                        <li class="nav-item">
                            <button class="nav-link active py-2" data-bs-toggle="pill" data-bs-target="#tab-card"><i class="fa-solid fa-credit-card me-1"></i> Tarjeta</button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link py-2" data-bs-toggle="pill" data-bs-target="#tab-cash"><i class="fa-solid fa-store me-1"></i> Efectivo (Paynet)</button>
                        </li>
                    </ul>

                    <div class="tab-content">
                        <!-- TARJETA -->
                        <div class="tab-pane fade show active" id="tab-card">
                            <form id="card-payment-form" action="procesar_pago.php" method="POST">
                                <input type="hidden" name="token_id" id="token_id">
                                <input type="hidden" name="monto" id="card-monto-val">

                                <div class="mb-3">
                                    <label class="form-label fw-semibold text-dark">Nombre en la Tarjeta</label>
                                    <input type="text" class="form-control" data-openpay-card="holder_name" value="<?php echo isset($_SESSION['usuario_nombre']) ? htmlspecialchars($_SESSION['usuario_nombre']) : 'Angel Larios'; ?>" required>
                                </div>
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between">
                                        <label class="form-label fw-semibold text-dark">Número de Tarjeta</label>
                                        <span id="card-brand" class="text-primary fw-bold">💳 Tarjeta</span>
                                    </div>
                                    <input type="text" id="card_number" class="form-control" data-openpay-card="card_number" maxlength="19" placeholder="4111 1111 1111 1111" required>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-semibold text-dark">Expiración</label>
                                        <div class="d-flex gap-2">
                                            <input type="text" id="exp_month" class="form-control" data-openpay-card="expiration_month" placeholder="MM" maxlength="2" required>
                                            <input type="text" id="exp_year" class="form-control" data-openpay-card="expiration_year" placeholder="YY" maxlength="2" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-semibold text-dark">CVV</label>
                                        <input type="password" id="cvv" class="form-control" data-openpay-card="cvv2" placeholder="123" maxlength="4" required>
                                    </div>
                                </div>
                                <div id="card-error" class="alert alert-danger" style="display:none;"></div>
                                <button type="submit" id="btn-pay-card" class="btn btn-success w-100 py-2.5 fw-semibold" style="background-color: #10b981; border:none;">Pagar Ahora</button>
                            </form>
                        </div>

                        <!-- EFECTIVO -->
                        <div class="tab-pane fade" id="tab-cash">
                            <form action="pago_efectivo.php" method="POST">
                                <input type="hidden" name="monto" id="cash-monto-val">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold text-dark">Nombre del Cliente</label>
                                    <input type="text" name="nombre" class="form-control" value="<?php echo isset($_SESSION['usuario_nombre']) ? htmlspecialchars($_SESSION['usuario_nombre']) : 'Angel Larios'; ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold text-dark">Correo Electrónico</label>
                                    <input type="email" name="email" class="form-control" value="angel306070@gmail.com" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold text-dark">Concepto</label>
                                    <input type="text" name="descripcion" class="form-control" value="Compra en EliteStore" required>
                                </div>
                                <button type="submit" name="generar_ficha" class="btn btn-primary w-100 py-2.5 fw-semibold" style="background-color: var(--primary-accent); border:none;">Generar Ficha Paynet</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- FOOTER -->
<footer class="py-4 mt-5 text-center">
    <div class="container">
        <p class="m-0 small opacity-75">© 2026 EliteStore Online. Todos los derechos reservados.</p>
    </div>
</footer>

<script>
    let cart = [];

    // Carrito de Compras
    function addToCart(btnElement, title, price) {
        cart.push({ title, price });
        updateCartUI();

        const $btn = $(btnElement);
        const originalHtml = $btn.html();
        
        $btn.addClass('btn-added')
            .html('<i class="fa-solid fa-check me-1"></i> ¡Añadido!')
            .prop('disabled', true);

        setTimeout(() => {
            $btn.removeClass('btn-added')
                .html(originalHtml)
                .prop('disabled', false);
        }, 800);

        const $badge = $('#cart-count');
        $badge.addClass('cart-bump');
        setTimeout(() => {
            $badge.removeClass('cart-bump');
        }, 300);
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
            itemsHtml = '<tr><td colspan="3" class="text-center text-muted py-4">El carrito está vacío.</td></tr>';
            $('#checkout-section').hide();
        } else {
            cart.forEach((item, index) => {
                total += item.price;
                itemsHtml += `
                    <tr>
                        <td class="fw-semibold text-dark">${item.title}</td>
                        <td class="text-success fw-bold">$${item.price.toFixed(2)} MXN</td>
                        <td class="text-end"><button class="btn btn-outline-danger btn-sm rounded-circle" onclick="removeFromCart(${index})"><i class="fa-solid fa-trash"></i></button></td>
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
        // Credenciales Openpay Sandbox
        OpenPay.setId('m43ygegi362bajsjomkm');
        OpenPay.setApiKey('pk_cabd23893d9144b9890a7a25dc2752ef');
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

// Tokenización de Tarjeta (Limpia espacios automáticamente)
$('#card-payment-form').submit(function(e) {
    e.preventDefault();
    $('#btn-pay-card').prop('disabled', true).text('Procesando Pago...');
    $('#card-error').hide();

    // Elimina los espacios antes de tokenizar la tarjeta
    var rawCardNumber = $('#card_number').val().replace(/\s+/g, '');
    $('#card_number').val(rawCardNumber);

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