<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'dbcon.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

/* ==========================================================================
   1. ELIMINAR PRODUCTO Y RELACIONES
   ========================================================================== */
if (isset($_POST['delete'])) {
    $id = (int) $_POST['delete'];

    try {
        $con->beginTransaction();

        $queries = [
            "DELETE FROM productosventa WHERE id = :id",
            "DELETE FROM mediosventa WHERE idproducto = :id",
            "DELETE FROM industriaasociadaventa WHERE idproducto = :id",
            "DELETE FROM categoriasasociadasventa WHERE idproducto = :id",
            "DELETE FROM subcategoriasasociadasventa WHERE idproducto = :id",
            "DELETE FROM asociarproductos WHERE idproductopadre = :id OR idproductopack = :id",
            "DELETE FROM asociartallas WHERE idproductoprincipal = :id OR idproductotalla = :id"
        ];

        foreach ($queries as $sql) {
            $stmt = $con->prepare($sql);
            $stmt->execute([':id' => $id]);
        }

        $con->commit();

        $_SESSION['alert'] = [
            'title' => 'ELIMINADO',
            'message' => 'Producto eliminado exitosamente',
            'icon' => 'success'
        ];
    } catch (Exception $e) {
        if ($con->inTransaction()) {
            $con->rollBack();
        }
        $_SESSION['alert'] = [
            'title' => 'ERROR',
            'message' => 'Notifica a soporte: ' . $e->getMessage(),
            'icon' => 'error'
        ];
    }

    header("Location: carga-tienda-en-linea.php");
    exit();
}

/* ==========================================================================
   2. ELIMINAR MEDIO ESPECÍFICO
   ========================================================================== */
if (isset($_POST['deletemedio'])) {
    $id = (int) $_POST['deletemedio'];
    $idproducto = (int) $_POST['idproducto'];

    try {
        $stmt = $con->prepare("DELETE FROM mediosventa WHERE id = :id");
        $stmt->execute([':id' => $id]);

        $_SESSION['alert'] = [
            'title' => 'MEDIO ELIMINADO',
            'message' => 'Medio eliminado exitosamente',
            'icon' => 'success'
        ];
    } catch (Exception $e) {
        $_SESSION['alert'] = [
            'title' => 'ERROR',
            'message' => 'Medio no eliminado.',
            'icon' => 'error'
        ];
    }

    header('Location: editarproductoventa.php?id=' . $idproducto);
    exit();
}

/* ==========================================================================
   3. ACTUALIZAR PRODUCTO Y SUS RELACIONES
   ========================================================================== */
if (isset($_POST['update'])) {
    $idproducto = (int) $_POST['id'];
    $titulo = $_POST['titulo'] ?? '';
    $subtitulo = $_POST['subtitulo'] ?? '';
    $estatus = $_POST['estatus'] ?? '1';
    $detalles = $_POST['detalles'] ?? '';
    $stock = $_POST['stock'] ?? 0;
    $sku = $_POST['sku'] ?? '';
    $stockminimo = $_POST['stockminimo'] ?? 0;
    $preciounitario = $_POST['preciounitario'] ?? 0;
    $preciomayoreo = $_POST['preciomayoreo'] ?? 0;
    $cantidadmayoreo = $_POST['cantidadmayoreo'] ?? 0;
    $descuento = $_POST['descuento'] ?? 0;
    $medios_delete = isset($_POST['medios_delete']) ? $_POST['medios_delete'] : [];

    try {
        $con->beginTransaction();

        // Update producto
        $sql_update = "UPDATE productosventa SET 
            titulo = :titulo, 
            subtitulo = :subtitulo, 
            estatus = :estatus, 
            detalles = :detalles, 
            stock = :stock, 
            sku = :sku, 
            stockminimo = :stockminimo, 
            preciounitario = :preciounitario, 
            preciomayoreo = :preciomayoreo, 
            cantidadmayoreo = :cantidadmayoreo, 
            descuento = :descuento 
            WHERE id = :id";

        $stmt = $con->prepare($sql_update);
        $stmt->execute([
            ':titulo' => $titulo,
            ':subtitulo' => $subtitulo,
            ':estatus' => $estatus,
            ':detalles' => $detalles,
            ':stock' => $stock,
            ':sku' => $sku,
            ':stockminimo' => $stockminimo,
            ':preciounitario' => $preciounitario,
            ':preciomayoreo' => $preciomayoreo,
            ':cantidadmayoreo' => $cantidadmayoreo,
            ':descuento' => $descuento,
            ':id' => $idproducto
        ]);

        // Eliminar categorías, subcategorías e industrias anteriores
        $con->prepare("DELETE FROM categoriasasociadasventa WHERE idproducto = :id")->execute([':id' => $idproducto]);
        $con->prepare("DELETE FROM subcategoriasasociadasventa WHERE idproducto = :id")->execute([':id' => $idproducto]);
        $con->prepare("DELETE FROM industriaasociadaventa WHERE idproducto = :id")->execute([':id' => $idproducto]);

        // Reinsertar Subcategorías
        if (!empty($_POST['subcategoria']) && is_array($_POST['subcategoria'])) {
            $stmt_sub = $con->prepare("INSERT INTO subcategoriasasociadasventa (idproducto, subcategoria) VALUES (:idproducto, :subcategoria)");
            foreach ($_POST['subcategoria'] as $subcat) {
                $stmt_sub->execute([':idproducto' => $idproducto, ':subcategoria' => $subcat]);
            }
        }

        // Reinsertar Categorías
        if (!empty($_POST['categoria']) && is_array($_POST['categoria'])) {
            $stmt_cat = $con->prepare("INSERT INTO categoriasasociadasventa (idproducto, categoria) VALUES (:idproducto, :categoria)");
            foreach ($_POST['categoria'] as $cat) {
                $stmt_cat->execute([':idproducto' => $idproducto, ':categoria' => $cat]);
            }
        }

        // Reinsertar Industrias
        if (!empty($_POST['industria']) && is_array($_POST['industria'])) {
            $stmt_ind = $con->prepare("INSERT INTO industriaasociadaventa (idproducto, industria) VALUES (:idproducto, :industria)");
            foreach ($_POST['industria'] as $ind) {
                $stmt_ind->execute([':idproducto' => $idproducto, ':industria' => $ind]);
            }
        }

        // Eliminar medios seleccionados
        if (!empty($medios_delete) && is_array($medios_delete)) {
            $stmt_get_medio = $con->prepare("SELECT medio FROM mediosventa WHERE id = :id");
            $stmt_del_medio = $con->prepare("DELETE FROM mediosventa WHERE id = :id");

            foreach ($medios_delete as $medio_id) {
                $m_id = (int) $medio_id;
                $stmt_get_medio->execute([':id' => $m_id]);
                if ($row = $stmt_get_medio->fetch()) {
                    if (file_exists($row['medio'])) {
                        unlink($row['medio']);
                    }
                }
                $stmt_del_medio->execute([':id' => $m_id]);
            }
        }

        // Subir e insertar nuevos medios
        if (isset($_FILES['medios']) && !empty($_FILES['medios']['tmp_name'][0])) {
            $directorio = 'productosventa/';
            if (!is_dir($directorio)) {
                mkdir($directorio, 0777, true);
            }

            $stmt_ins_medio = $con->prepare("INSERT INTO mediosventa (idproducto, medio) VALUES (:idproducto, :medio)");

            foreach ($_FILES['medios']['tmp_name'] as $key => $tmp_name) {
                $nombre_original = $_FILES['medios']['name'][$key];
                $tipo = $_FILES['medios']['type'][$key];
                $ext = strtolower(pathinfo($nombre_original, PATHINFO_EXTENSION));

                $nombre_archivo = uniqid() . ".jpg";

                if (in_array($tipo, ['image/jpeg', 'image/png', 'image/jpg'])) {
                    $imagen = imagecreatefromstring(file_get_contents($tmp_name));
                    if ($imagen !== false) {
                        imagejpeg($imagen, $directorio . $nombre_archivo);
                        imagedestroy($imagen);
                    }
                } elseif ($ext === 'pdf' || $ext === 'mp4') {
                    $nombre_archivo = uniqid() . "." . $ext;
                    move_uploaded_file($tmp_name, $directorio . $nombre_archivo);
                } else {
                    continue;
                }

                $ruta_archivo = $directorio . $nombre_archivo;
                $stmt_ins_medio->execute([':idproducto' => $idproducto, ':medio' => $ruta_archivo]);
            }
        }

        // Manejo de productos asociados (packs)
        if (!empty($_POST['idproductopack'])) {
            $idasoc = $_POST['idasoc'] ?? '0';
            $idproductopadre = (int) $_POST['idproductopack'];
            $cantidadpack = (int) ($_POST['cantidadpack'] ?? 1);

            if (empty($idasoc) || $idasoc === "0") {
                $stmt_pack = $con->prepare("INSERT INTO asociarproductos (idproductopack, idproductopadre, cantidadpack) VALUES (:idpack, :idpadre, :cant)");
                $stmt_pack->execute([':idpack' => $idproducto, ':idpadre' => $idproductopadre, ':cant' => $cantidadpack]);
            } else {
                $stmt_pack = $con->prepare("UPDATE asociarproductos SET cantidadpack = :cant, idproductopadre = :idpadre WHERE idproductopack = :idpack AND idproductopadre = :idasoc");
                $stmt_pack->execute([':cant' => $cantidadpack, ':idpadre' => $idproductopadre, ':idpack' => $idproducto, ':idasoc' => $idasoc]);
            }
        }

        $con->commit();

        $_SESSION['alert'] = [
            'title' => 'ACTUALIZADO',
            'message' => 'Producto actualizado con éxito',
            'icon' => 'success'
        ];
    } catch (Exception $e) {
        if ($con->inTransaction()) {
            $con->rollBack();
        }
        $_SESSION['alert'] = [
            'title' => 'ERROR',
            'message' => 'Error al actualizar el producto: ' . $e->getMessage(),
            'icon' => 'error'
        ];
    }

    header('Location: editarproductoventa.php?id=' . $idproducto);
    exit();
}

/* ==========================================================================
   4. GUARDAR NUEVO PRODUCTO
   ========================================================================== */
if (isset($_POST['save'])) {
    $titulo = $_POST['titulo'] ?? '';
    $subtitulo = $_POST['subtitulo'] ?? '';
    $detalles = $_POST['detalles'] ?? '';
    $stock = $_POST['stock'] ?? 0;
    $sku = $_POST['sku'] ?? '';
    $stockminimo = $_POST['stockminimo'] ?? 0;
    $preciounitario = $_POST['preciounitario'] ?? 0;
    $preciomayoreo = $_POST['preciomayoreo'] ?? 0;
    $cantidadmayoreo = $_POST['cantidadmayoreo'] ?? 0;
    $descuento = $_POST['descuento'] ?? 0;
    $estatus = '1';

    try {
        $con->beginTransaction();

        $sql_ins = "INSERT INTO productosventa (titulo, subtitulo, detalles, stock, sku, stockminimo, preciounitario, preciomayoreo, cantidadmayoreo, descuento, estatus) 
                    VALUES (:titulo, :subtitulo, :detalles, :stock, :sku, :stockminimo, :preciounitario, :preciomayoreo, :cantidadmayoreo, :descuento, :estatus)";

        $stmt = $con->prepare($sql_ins);
        $stmt->execute([
            ':titulo' => $titulo,
            ':subtitulo' => $subtitulo,
            ':detalles' => $detalles,
            ':stock' => $stock,
            ':sku' => $sku,
            ':stockminimo' => $stockminimo,
            ':preciounitario' => $preciounitario,
            ':preciomayoreo' => $preciomayoreo,
            ':cantidadmayoreo' => $cantidadmayoreo,
            ':descuento' => $descuento,
            ':estatus' => $estatus
        ]);

        $idproducto = $con->lastInsertId();

        // Categorías
        if (!empty($_POST['categoria']) && is_array($_POST['categoria'])) {
            $stmt_cat = $con->prepare("INSERT INTO categoriasasociadasventa (idproducto, categoria) VALUES (:idproducto, :categoria)");
            foreach ($_POST['categoria'] as $cat) {
                $stmt_cat->execute([':idproducto' => $idproducto, ':categoria' => $cat]);
            }
        }

        // Industrias
        if (!empty($_POST['industria']) && is_array($_POST['industria'])) {
            $stmt_ind = $con->prepare("INSERT INTO industriaasociadaventa (idproducto, industria) VALUES (:idproducto, :industria)");
            foreach ($_POST['industria'] as $ind) {
                $stmt_ind->execute([':idproducto' => $idproducto, ':industria' => $ind]);
            }
        }

        // Subcategorías
        if (!empty($_POST['subcategoria']) && is_array($_POST['subcategoria'])) {
            $stmt_sub = $con->prepare("INSERT INTO subcategoriasasociadasventa (idproducto, subcategoria) VALUES (:idproducto, :subcategoria)");
            foreach ($_POST['subcategoria'] as $subcat) {
                $stmt_sub->execute([':idproducto' => $idproducto, ':subcategoria' => $subcat]);
            }
        }

        // Medios
        if (isset($_FILES['medios']) && !empty($_FILES['medios']['tmp_name'][0])) {
            $directorio = 'productosventa/';
            if (!is_dir($directorio)) {
                mkdir($directorio, 0777, true);
            }

            $stmt_ins_medio = $con->prepare("INSERT INTO mediosventa (idproducto, medio) VALUES (:idproducto, :medio)");

            foreach ($_FILES['medios']['tmp_name'] as $key => $tmp_name) {
                $nombre_original = $_FILES['medios']['name'][$key];
                $tipo = $_FILES['medios']['type'][$key];
                $ext = strtolower(pathinfo($nombre_original, PATHINFO_EXTENSION));

                $nombre_archivo = uniqid() . ".jpg";

                if (in_array($tipo, ['image/jpeg', 'image/png', 'image/jpg'])) {
                    $imagen = imagecreatefromstring(file_get_contents($tmp_name));
                    if ($imagen !== false) {
                        imagejpeg($imagen, $directorio . $nombre_archivo);
                        imagedestroy($imagen);
                    }
                } elseif ($ext === 'pdf' || $ext === 'mp4') {
                    $nombre_archivo = uniqid() . "." . $ext;
                    move_uploaded_file($tmp_name, $directorio . $nombre_archivo);
                } else {
                    continue;
                }

                $ruta_archivo = $directorio . $nombre_archivo;
                $stmt_ins_medio->execute([':idproducto' => $idproducto, ':medio' => $ruta_archivo]);
            }
        }

        // Producto pack asociado
        if (!empty($_POST['idproductopack'])) {
            $idproductopadre = (int) $_POST['idproductopack'];
            $cantidadpack = (int) ($_POST['cantidadpack'] ?? 1);

            $stmt_pack = $con->prepare("INSERT INTO asociarproductos (cantidadpack, idproductopack, idproductopadre) VALUES (:cant, :idpack, :idpadre)");
            $stmt_pack->execute([':cant' => $cantidadpack, ':idpack' => $idproducto, ':idpadre' => $idproductopadre]);
        }

        $con->commit();

        $_SESSION['alert'] = [
            'title' => 'REGISTRADO',
            'message' => 'Producto registrado con éxito',
            'icon' => 'success'
        ];
    } catch (Exception $e) {
        if ($con->inTransaction()) {
            $con->rollBack();
        }
        $_SESSION['alert'] = [
            'title' => 'ERROR',
            'message' => 'Notifica a soporte: ' . $e->getMessage(),
            'icon' => 'error'
        ];
    }

    header("Location: carga-tienda-en-linea.php");
    exit();
}

/* ==========================================================================
   5. DUPLICAR PRODUCTO
   ========================================================================== */
if (isset($_POST['duplicar'])) {
    $idproducto = (int) $_POST['id'];

    try {
        $con->beginTransaction();

        $query_producto = "INSERT INTO productosventa (titulo, subtitulo, estatus, detalles, stock, sku, stockminimo, preciounitario, preciomayoreo, cantidadmayoreo, descuento, talla)
                           SELECT titulo, subtitulo, estatus, detalles, stock, sku, stockminimo, preciounitario, preciomayoreo, cantidadmayoreo, descuento, 'Unitalla'
                           FROM productosventa WHERE id = :id";
        
        $stmt_prod = $con->prepare($query_producto);
        $stmt_prod->execute([':id' => $idproducto]);
        $nuevoProductoId = $con->lastInsertId();

        // Duplicar categorías
        $con->prepare("INSERT INTO categoriasasociadasventa (idproducto, categoria) SELECT :nuevo, categoria FROM categoriasasociadasventa WHERE idproducto = :viejo")
            ->execute([':nuevo' => $nuevoProductoId, ':viejo' => $idproducto]);

        // Duplicar subcategorías
        $con->prepare("INSERT INTO subcategoriasasociadasventa (idproducto, subcategoria) SELECT :nuevo, subcategoria FROM subcategoriasasociadasventa WHERE idproducto = :viejo")
            ->execute([':nuevo' => $nuevoProductoId, ':viejo' => $idproducto]);

        // Duplicar industrias
        $con->prepare("INSERT INTO industriaasociadaventa (idproducto, industria) SELECT :nuevo, industria FROM industriaasociadaventa WHERE idproducto = :viejo")
            ->execute([':nuevo' => $nuevoProductoId, ':viejo' => $idproducto]);

        // Duplicar medios
        $con->prepare("INSERT INTO mediosventa (idproducto, medio) SELECT :nuevo, medio FROM mediosventa WHERE idproducto = :viejo")
            ->execute([':nuevo' => $nuevoProductoId, ':viejo' => $idproducto]);

        // Duplicar packs asociados
        $stmt_pack = $con->prepare("SELECT idproductopadre, cantidadpack FROM asociarproductos WHERE idproductopack = :id");
        $stmt_pack->execute([':id' => $idproducto]);
        $rows_pack = $stmt_pack->fetchAll();

        if (count($rows_pack) > 0) {
            $stmt_ins_pack = $con->prepare("INSERT INTO asociarproductos (idproductopack, idproductopadre, cantidadpack) VALUES (:nuevo, :padre, :cant)");
            foreach ($rows_pack as $row) {
                if (!empty($row['idproductopadre'])) {
                    $stmt_ins_pack->execute([
                        ':nuevo' => $nuevoProductoId,
                        ':padre' => $row['idproductopadre'],
                        ':cant' => $row['cantidadpack']
                    ]);
                }
            }
        }

        $con->commit();

        $_SESSION['alert'] = [
            'title' => 'Éxito',
            'message' => 'Tallas agregadas correctamente',
            'icon' => 'success'
        ];
    } catch (Exception $e) {
        if ($con->inTransaction()) {
            $con->rollBack();
        }
        $_SESSION['alert'] = [
            'title' => 'Error al duplicar el producto',
            'message' => 'Contacte a su proveedor: ' . $e->getMessage(),
            'icon' => 'error'
        ];
    }

    header('Location: carga-tienda-en-linea.php');
    exit();
}

/* ==========================================================================
   6. GUARDAR TALLAS
   ========================================================================== */
if (isset($_POST['saveTalla'])) {
    if (empty($_POST['idproductoprincipal']) || empty($_POST['talla']) || !is_array($_POST['talla'])) {
        die('Datos incompletos');
    }

    $idProductoPrincipal = (int) $_POST['idproductoprincipal'];
    $tallas = $_POST['talla'];
    $cantidadpack = isset($_POST['cantidadpack']) ? (int) $_POST['cantidadpack'] : 1;

    try {
        $con->beginTransaction();

        // 1. Obtener producto base
        $stmt_base = $con->prepare("SELECT * FROM productosventa WHERE id = :id LIMIT 1");
        $stmt_base->execute([':id' => $idProductoPrincipal]);
        $producto = $stmt_base->fetch(PDO::FETCH_ASSOC);

        if (!$producto) {
            throw new Exception('Producto no encontrado');
        }

        // 2. Primera talla actualiza el registro existente
        $primeraTalla = array_shift($tallas);
        $stmt_upd_talla = $con->prepare("UPDATE productosventa SET talla = :talla WHERE id = :id");
        $stmt_upd_talla->execute([':talla' => $primeraTalla, ':id' => $idProductoPrincipal]);

        // 3. Preparar inserción para las tallas restantes
        unset($producto['id']);
        $columnas = array_keys($producto);
        $placeholders = implode(',', array_map(fn($col) => ":$col", $columnas));

        $sql_dupl = "INSERT INTO productosventa (" . implode(',', $columnas) . ") VALUES ($placeholders)";
        $stmt_ins_dupl = $con->prepare($sql_dupl);

        $stmt_ins_asoc_talla = $con->prepare("INSERT INTO asociartallas (idproductoprincipal, idproductotalla, talla) VALUES (:principal, :talla_id, :talla_val)");

        // 4. Determinar idproductopadre
        $idProductoPadreFinal = null;
        
        $stmt_p = $con->prepare("SELECT idproductopadre FROM asociarproductos WHERE idproductopadre = :id LIMIT 1");
        $stmt_p->execute([':id' => $idProductoPrincipal]);
        if ($stmt_p->rowCount() > 0) {
            $idProductoPadreFinal = $idProductoPrincipal;
        } else {
            $stmt_pk = $con->prepare("SELECT idproductopadre FROM asociarproductos WHERE idproductopack = :id LIMIT 1");
            $stmt_pk->execute([':id' => $idProductoPrincipal]);
            if ($row_pk = $stmt_pk->fetch()) {
                $idProductoPadreFinal = $row_pk['idproductopadre'];
            }
        }

        // Función auxiliar para duplicar relaciones
        $duplicarRelacion = function($con, $tabla, $idOrigen, $idNuevo) {
            $stmt_sel = $con->prepare("SELECT * FROM $tabla WHERE idproducto = :id");
            $stmt_sel->execute([':id' => $idOrigen]);
            $rows = $stmt_sel->fetchAll(PDO::FETCH_ASSOC);

            foreach ($rows as $row) {
                unset($row['id']);
                $row['idproducto'] = $idNuevo;

                $cols = array_keys($row);
                $ph = implode(',', array_map(fn($c) => ":$c", $cols));
                
                $stmt_ins = $con->prepare("INSERT INTO $tabla (" . implode(',', $cols) . ") VALUES ($ph)");
                
                $params = [];
                foreach ($row as $k => $v) {
                    $params[":$k"] = $v;
                }
                $stmt_ins->execute($params);
            }
        };

        // 5. Iterar sobre las demás tallas
        foreach ($tallas as $talla) {
            $producto['talla'] = $talla;
            
            $params = [];
            foreach ($producto as $k => $v) {
                $params[":$k"] = $v;
            }
            $stmt_ins_dupl->execute($params);
            $idProductoTalla = $con->lastInsertId();

            $stmt_ins_asoc_talla->execute([
                ':principal' => $idProductoPrincipal,
                ':talla_id' => $idProductoTalla,
                ':talla_val' => $talla
            ]);

            $duplicarRelacion($con, 'categoriasasociadasventa', $idProductoPrincipal, $idProductoTalla);
            $duplicarRelacion($con, 'subcategoriasasociadasventa', $idProductoPrincipal, $idProductoTalla);
            $duplicarRelacion($con, 'industriaasociadaventa', $idProductoPrincipal, $idProductoTalla);
            $duplicarRelacion($con, 'mediosventa', $idProductoPrincipal, $idProductoTalla);

            if (!is_null($idProductoPadreFinal)) {
                $stmt_ins_pack = $con->prepare("INSERT INTO asociarproductos (idproductopadre, idproductopack, cantidadpack) VALUES (:padre, :pack, :cant)");
                $stmt_ins_pack->execute([
                    ':padre' => $idProductoPadreFinal,
                    ':pack' => $idProductoTalla,
                    ':cant' => $cantidadpack
                ]);
            }
        }

        $con->commit();
        header('Location: carga-tienda-en-linea.php');
        exit();
    } catch (Exception $e) {
        if ($con->inTransaction()) {
            $con->rollBack();
        }
        die('Error: ' . $e->getMessage());
    }
}