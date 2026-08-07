<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'dbcon.php';

$alert = isset($_SESSION['alert']) ? $_SESSION['alert'] : null;

if (!empty($alert)) {
    $title = isset($alert['title']) ? json_encode($alert['title']) : '"Notificación"';
    $message = isset($alert['message']) ? json_encode($alert['message']) : '""';
    $icon = isset($alert['icon']) ? json_encode($alert['icon']) : '"info"';

    echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    title: $title,
                    " . (!empty($alert['message']) ? "text: $message," : "") . "
                    icon: $icon,
                    confirmButtonText: 'OK'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Hacer algo si se confirma la alerta
                    }
                });
            });
        </script>";
    unset($_SESSION['alert']);
}

if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];

    $stmt_user = $con->prepare("SELECT * FROM usuarios WHERE username = :username");
    $stmt_user->execute([':username' => $username]);
    $user_data = $stmt_user->fetch();

    if (!$user_data) {
        $_SESSION['alert'] = [
            'title' => 'USUARIO NO ENCONTRADO',
            'icon' => 'error'
        ];
        header('Location: login.php');
        exit();
    }
} else {
    $_SESSION['alert'] = [
        'message' => 'Para acceder debes iniciar sesión primero',
        'title' => 'SESIÓN NO INICIADA',
        'icon' => 'error'
    ];
    header('Location: login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="shortcut icon" type="image/x-icon" href="images/ics.ico">
    <title>Carga tienda en línea | Fastpack</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-0evHe/X+R7YkIZDRvuzKMRqM+OrBnVFBL6DOitfPri4tjfHxaWutUpFmBp4vmVor" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.css">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="shortcut icon" href="images/ico.ico" type="image/x-icon">
</head>

<body class="sb-nav-fixed">
    <?php include 'sidenav.php'; ?>
    <div id="layoutSidenav">
        <div id="layoutSidenav_content">
            <div class="container-fluid">
                <div class="row mb-5 mt-4">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 style="color:#fff" class="m-1">TIENDA EN LÍNEA <small>(PRODUCTOS ACTIVOS)</small>
                                    <button type="button" class="btn btn-primary btn-sm float-end btn-sm m-1" data-bs-toggle="modal" data-bs-target="#exampleModal">
                                        Nuevo producto
                                    </button>

                                    <button type="button" class="btn btn-secondary btn-sm float-end btn-sm m-1" data-bs-toggle="modal" data-bs-target="#duplicarModal">
                                        Agregar tallas
                                    </button>
                                </h4>
                            </div>
                            <div class="card-body" style="overflow-y:scroll;">
                                <table id="miTabla" class="table table-bordered table-striped" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Título</th>
                                            <th>Subtítulo</th>
                                            <th>Categoría</th>
                                            <th>Subcategoría</th>
                                            <th>Talla</th>
                                            <th>Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $query = "SELECT p.id, 
                                                    p.titulo, 
                                                    p.subtitulo, 
                                                    p.talla,
                                                    GROUP_CONCAT(DISTINCT c.categoria ORDER BY c.categoria ASC SEPARATOR ', ') AS categorias,
                                                    GROUP_CONCAT(DISTINCT i.subcategoria ORDER BY i.subcategoria ASC SEPARATOR ', ') AS subcategorias
                                                FROM productosventa p
                                                LEFT JOIN categoriasasociadasventa c ON p.id = c.idproducto
                                                LEFT JOIN subcategoriasasociadasventa i ON p.id = i.idproducto
                                                WHERE p.estatus = 1
                                                GROUP BY p.id
                                                ORDER BY p.id DESC";

                                        $stmt_prod = $con->query($query);
                                        $productos = $stmt_prod->fetchAll();

                                        if (count($productos) > 0) {
                                            foreach ($productos as $registro) {
                                        ?>
                                                <tr>
                                                    <td><p><?= $registro['id']; ?></p></td>
                                                    <td><p><?= htmlspecialchars($registro['titulo'] ?? '', ENT_QUOTES, 'UTF-8'); ?></p></td>
                                                    <td><p><?= htmlspecialchars($registro['subtitulo'] ?? '', ENT_QUOTES, 'UTF-8'); ?></p></td>
                                                    <td><p><?= htmlspecialchars($registro['categorias'] ?? '', ENT_QUOTES, 'UTF-8'); ?></p></td>
                                                    <td><p><?= htmlspecialchars($registro['subcategorias'] ?? '', ENT_QUOTES, 'UTF-8'); ?></p></td>
                                                    <td><p><?= htmlspecialchars($registro['talla'] ?? '', ENT_QUOTES, 'UTF-8'); ?></p></td>
                                                    <td>
                                                        <a href="editarproductoventa.php?id=<?= $registro['id']; ?>" class="btn btn-warning btn-sm m-1"><i class="bi bi-pencil-square"></i></a>

                                                        <a href="duplicar-producto-venta.php?id=<?= $registro['id']; ?>" class="btn btn-secondary btn-sm m-1"><i class="bi bi-copy"></i></a>

                                                        <form action="codeproductosventa.php" method="POST" class="d-inline">
                                                            <button type="submit" name="delete" value="<?= $registro['id']; ?>" class="btn btn-danger btn-sm m-1"><i class="bi bi-trash-fill"></i></button>
                                                        </form>
                                                    </td>
                                                </tr>
                                        <?php
                                            }
                                        } else {
                                            echo "<tr><td colspan='7' class='text-center'><p>No se encontró ningún registro</p></td></tr>";
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Nuevo Producto -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">NUEVO PRODUCTO</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="codeproductosventa.php" method="POST" class="row" enctype="multipart/form-data">
                        <div class="col-12 col-md-12 form-floating mb-3">
                            <input type="text" class="form-control" name="titulo" id="titulo" placeholder="Titulo" autocomplete="off" required>
                            <label for="titulo">Título</label>
                        </div>

                        <div class="col-12 col-md-12 form-floating mb-3">
                            <textarea class="form-control" name="subtitulo" id="subtitulo" placeholder="Subtitulo" required style="min-height: 100px;"></textarea>
                            <label for="subtitulo">Subtítulo</label>
                        </div>

                        <div class="col-12 col-md-12 form-floating mb-3">
                            <textarea class="form-control" name="detalles" id="detalles" placeholder="Detalles" required style="min-height: 150px;"></textarea>
                            <label for="detalles">Detalles</label>
                        </div>

                        <div class="col-12 col-md-4 mb-3">
                            <label>Industrias</label>
                            <div id="industria">
                                <?php
                                $stmt_ind = $con->query("SELECT * FROM industrias");
                                while ($industria = $stmt_ind->fetch()) {
                                    $opcion = htmlspecialchars($industria['industria'], ENT_QUOTES, 'UTF-8');
                                    echo "
                                    <div class='form-check'>
                                        <input class='form-check-input' type='checkbox' name='industria[]' value='$opcion' id='industria_$opcion'>
                                        <label class='form-check-label' for='industria_$opcion'>$opcion</label>
                                    </div>";
                                }
                                ?>
                            </div>
                        </div>

                        <div class="col-12 col-md-4 mb-3">
                            <label>Categorías</label>
                            <div id="categoria">
                                <?php
                                $stmt_cat = $con->query("SELECT * FROM categorias");
                                while ($categoria = $stmt_cat->fetch()) {
                                    $opcion = htmlspecialchars($categoria['categoria'], ENT_QUOTES, 'UTF-8');
                                    echo "
                                    <div class='form-check'>
                                        <input class='form-check-input' type='checkbox' name='categoria[]' value='$opcion' id='categoria_$opcion'>
                                        <label class='form-check-label' for='categoria_$opcion'>$opcion</label>
                                    </div>";
                                }
                                ?>
                            </div>
                        </div>

                        <div class="col-12 col-md-4 mb-3">
                            <label>Subcategorías</label>
                            <div id="subcategoria">
                                <?php
                                $stmt_sub = $con->query("SELECT * FROM subcategorias");
                                while ($subcategoria = $stmt_sub->fetch()) {
                                    $opcion = htmlspecialchars($subcategoria['subcategoria'], ENT_QUOTES, 'UTF-8');
                                    echo "
                                    <div class='form-check'>
                                        <input class='form-check-input' type='checkbox' name='subcategoria[]' value='$opcion' id='subcategoria_$opcion'>
                                        <label class='form-check-label' for='subcategoria_$opcion'>$opcion</label>
                                    </div>";
                                }
                                ?>
                            </div>
                        </div>

                        <div class="col-12 col-md-2 form-floating mb-3">
                            <input type="number" class="form-control" name="stock" id="stock" placeholder="Stock" autocomplete="off" required>
                            <label for="stock">Stock</label>
                        </div>

                        <div class="col-12 col-md-10 form-floating mb-3">
                            <input type="text" class="form-control" name="sku" id="sku" placeholder="SKU" autocomplete="off" required>
                            <label for="sku">SKU</label>
                        </div>

                        <div class="col-12 col-md-3 form-floating mb-3">
                            <input type="number" class="form-control" name="stockminimo" id="stockminimo" placeholder="Stock minimo" autocomplete="off" required>
                            <label for="stockminimo">Stock minimo</label>
                        </div>

                        <div class="col-12 col-md-3 form-floating mb-3">
                            <input type="text" class="form-control" name="preciounitario" id="preciounitario" placeholder="Precio unitario" autocomplete="off" required>
                            <label for="preciounitario">Precio unitario</label>
                        </div>

                        <div class="col-12 col-md-6 form-floating mb-3">
                            <input type="text" class="form-control" name="preciomayoreo" id="preciomayoreo" placeholder="Precio mayoreo" autocomplete="off" required>
                            <label for="preciomayoreo">Precio mayoreo</label>
                        </div>

                        <div class="col-12 col-md-4 form-floating mb-3">
                            <input type="number" class="form-control" name="cantidadmayoreo" id="cantidadmayoreo" placeholder="Cantidad mayoreo" autocomplete="off" required>
                            <label for="cantidadmayoreo">Cantidad mínima para mayoreo</label>
                        </div>

                        <div class="col-12 col-md-8 form-floating mb-3">
                            <input type="text" class="form-control" name="descuento" id="descuento" placeholder="Descuento" autocomplete="off" required>
                            <label for="descuento">Descuento</label>
                        </div>

                        <div class="col-12 col-md-6 mb-3">
                            <label for="medio">Medios</label>
                            <input type="file" name="medios[]" id="medio" class="form-control" accept=".jpg, .jpeg, .png" multiple>
                        </div>

                        <div class="col-12 col-md-6 form-floating mb-3">
                            <input type="text" class="form-control" name="cantidadpack" placeholder="Cantidad pack" autocomplete="off">
                            <label for="cantidadpack">Cantidad que contendrá el paquete asociado</label>
                        </div>

                        <div class="col-12 col-md-12 mb-3">
                            <p class="mb-1"><b>Asociar producto principal <small>(Seleccionar el producto de venta individual)</small></b></p>
                            <select class="form-select mb-3" name="idproductopack">
                                <option value="" selected>Selecciona otra opción o deje esta opción seleccionada para no asociar ningún producto</option>
                                <?php
                                $stmt_pv = $con->query("SELECT * FROM productosventa");
                                while ($prod = $stmt_pv->fetch()) {
                                    $titulo = htmlspecialchars($prod['titulo'] ?? '', ENT_QUOTES, 'UTF-8');
                                    $subtitulo = htmlspecialchars($prod['subtitulo'] ?? '', ENT_QUOTES, 'UTF-8');
                                    $talla = htmlspecialchars($prod['talla'] ?? '', ENT_QUOTES, 'UTF-8');
                                    $idProd = $prod['id'];
                                    echo "<option value='$idProd'>$titulo - $subtitulo - $talla</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <div class="modal-footer col-12">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                            <button type="submit" class="btn btn-primary" name="save">Guardar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Agregar Tallas -->
    <div class="modal fade" id="duplicarModal" tabindex="-1" aria-labelledby="duplicarModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="duplicarModalLabel">AGREGAR TALLAS A PRODUCTO</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="codeproductosventa.php" method="POST">
                    <div class="modal-body">
                        <div class="col-12 col-md-12 mb-3">
                            <p class="mb-1"><b>Selecciona el producto al que le quieres agregar tallas</b></p>
                            <select class="form-select mb-3" name="idproductoprincipal">
                                <option value="" selected>Selecciona una opción</option>
                                <?php
                                $query_dupl = "SELECT p.*, a.idproductotalla AS ya_asociado
                                               FROM productosventa p
                                               LEFT JOIN asociartallas a ON a.idproductotalla = p.id
                                               ORDER BY p.titulo ASC";
                                $stmt_dupl = $con->query($query_dupl);

                                while ($prod = $stmt_dupl->fetch()) {
                                    $idProd    = $prod['id'];
                                    $titulo    = htmlspecialchars($prod['titulo'] ?? '', ENT_QUOTES, 'UTF-8');
                                    $subtitulo = htmlspecialchars($prod['subtitulo'] ?? '', ENT_QUOTES, 'UTF-8');
                                    $talla     = htmlspecialchars($prod['talla'] ?? '', ENT_QUOTES, 'UTF-8');

                                    $disabled  = !is_null($prod['ya_asociado']) ? 'disabled' : '';
                                    $color     = !is_null($prod['ya_asociado']) ? '#e7e7e7' : '';
                                    $textoTalla = ($talla !== 'Unitalla' && !empty($talla)) ? " - $talla" : '';

                                    echo "<option style='background-color: $color;' value='$idProd' data-talla='$talla' $disabled>
                                            $idProd-$titulo - $subtitulo $textoTalla
                                          </option>";
                                }
                                ?>
                            </select>
                        </div>

                        <div class="col-12 mb-3">
                            <p><b>Tallas: <small>(No marcar ninguna opción si no aplica talla para el producto)</small></b></p>
                            <?php 
                            $tallas = ['XS', 'CH', 'M', 'G', 'XL', '6', '7', '8', '9', '10', '21cm', '22cm', '23cm', '24cm', '25cm', '26cm', '27cm', '28cm', '29cm', '30cm'];
                            foreach($tallas as $idx => $tVal) {
                                echo "<div class='form-check form-check-inline'>
                                        <input class='form-check-input' type='checkbox' id='inlineCheckbox$idx' value='$tVal' name='talla[]'>
                                        <label class='form-check-label' for='inlineCheckbox$idx'>$tVal</label>
                                      </div>";
                            }
                            ?>
                        </div>

                        <div class="form-floating col-12">
                            <input type="number" name="cantidadpack" class="form-control" placeholder="cantidad" required>
                            <label for="cantidadpack">¿Cuántas piezas tiene el producto que seleccionaste?</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-primary" name="saveTalla" id="saveTallas" disabled>Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-pprn3073KE6tl6bjs2QrFaJGz5/SUsLqktiwsUTF55Jfv3qYSDhgCecCxMW52nD2" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.js"></script>
    <script src='https://cdn.jsdelivr.net/npm/sweetalert2@10'></script>
    <script>
        $(document).ready(function() {
            $('#miTabla').DataTable({
                "order": [[1, "asc"]],
                "pageLength": 25
            });
        });

        document.addEventListener("DOMContentLoaded", function() {
            const soloEnteros = ["stock", "stockminimo", "cantidadmayoreo"];
            const soloDecimales = ["descuento", "precio", "preciomayoreo", "preciounitario"];

            soloEnteros.forEach(id => {
                const input = document.getElementById(id);
                if (input) {
                    input.addEventListener("input", function() {
                        this.value = this.value.replace(/[^0-9]/g, '');
                    });
                }
            });

            soloDecimales.forEach(id => {
                const input = document.getElementById(id);
                if (input) {
                    input.addEventListener("input", function() {
                        this.value = this.value
                            .replace(/[^0-9.]/g, '') 
                            .replace(/(\..*)\./g, '$1'); 
                    });
                }
            });

            const selectProducto = document.querySelector('select[name="idproductoprincipal"]');
            const checkboxes = document.querySelectorAll('input[name="talla[]"]');
            const btnGuardar = document.getElementById('saveTallas');

            function validarFormulario() {
                const productoSeleccionado = selectProducto.value !== '';
                const algunaTallaMarcada = Array.from(checkboxes).some(cb => cb.checked);
                btnGuardar.disabled = !(productoSeleccionado && algunaTallaMarcada);
            }

            selectProducto.addEventListener('change', validarFormulario);
            checkboxes.forEach(cb => cb.addEventListener('change', validarFormulario));

            selectProducto.addEventListener('change', function() {
                const selected = this.options[this.selectedIndex];
                const tallaProducto = selected.dataset.talla;
                const idProducto = this.value;

                checkboxes.forEach(cb => {
                    cb.checked = false;
                    cb.disabled = true;
                });

                if (tallaProducto === 'Unitalla') {
                    checkboxes.forEach(cb => cb.disabled = false);
                } else {
                    checkboxes.forEach(cb => {
                        if (cb.value !== tallaProducto) {
                            cb.disabled = false;
                        }
                    });
                }

                fetch('getTallasAsociadas.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: 'id=' + idProducto
                })
                .then(res => res.json())
                .then(tallasExistentes => {
                    checkboxes.forEach(cb => {
                        if (tallasExistentes.includes(cb.value)) {
                            cb.disabled = true;
                            cb.checked = false;
                        }
                    });
                });
            });
        });
    </script>
</body>

</html>