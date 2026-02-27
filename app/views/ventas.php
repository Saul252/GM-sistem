<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../../includes/auth.php';
protegerPagina();

require_once __DIR__ . '/../../includes/sidebar.php';
require_once __DIR__ . '/../../config/conexion.php';

$paginaActual = 'Ventas';

/* ================================
   CATEGORIAS
================================ */
$categorias = $conexion->query("SELECT id, nombre FROM categorias ORDER BY nombre ASC");

/* ================================
   ALMACENES
================================ */
$almacenes = $conexion->query("SELECT id, nombre FROM almacenes WHERE activo = 1 ORDER BY nombre ASC");

/* ================================
   PRODUCTOS
================================ */
$sql = "SELECT 
    p.id,
    p.sku,
    p.nombre,
    p.categoria_id,
    c.nombre AS categoria_nombre,
    i.stock,
    i.almacen_id,
    a.nombre AS almacen_nombre,
    IFNULL(pp.precio_minorista,0) precio_minorista,
    IFNULL(pp.precio_mayorista,0) precio_mayorista,
    IFNULL(pp.precio_distribuidor,0) precio_distribuidor
FROM inventario i
INNER JOIN productos p ON i.producto_id = p.id
INNER JOIN almacenes a ON i.almacen_id = a.id
LEFT JOIN categorias c ON p.categoria_id = c.id
LEFT JOIN precios_producto pp 
    ON pp.producto_id = p.id 
    AND pp.almacen_id = i.almacen_id
WHERE p.activo = 1
AND i.stock > 0
ORDER BY p.nombre ASC";

$result = $conexion->query($sql);
$productos = [];

while($row = $result->fetch_assoc()){
    $productos[] = $row;
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Ventas</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="/cfsistem/css/ventas.css" rel="stylesheet">

</head>

<body>

    <?php renderSidebar($paginaActual); ?>

    <div class="main-content">

        <h2 class="mb-4 fw-bold">
            <i class="bi bi-cart-fill text-primary"></i> Módulo de Ventas
        </h2>

        <div class="row">

            <!-- ================= PRODUCTOS ================= -->
            <div class="col-lg-8">
                <div class="card p-3">

                    <!-- FILTROS -->
                    <div class="row mb-3">

                        <div class="col-md-4">
                            <select id="filtroCategoria" class="form-select">
                                <option value="">Todas las categorías</option>
                                <?php while($cat = $categorias->fetch_assoc()): ?>
                                <option value="<?= $cat['id'] ?>">
                                    <?= htmlspecialchars($cat['nombre']) ?>
                                </option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <select id="filtroAlmacen" class="form-select">
                                <option value="">Todos los almacenes</option>
                                <?php while($alm = $almacenes->fetch_assoc()): ?>
                                <option value="<?= $alm['id'] ?>">
                                    <?= htmlspecialchars($alm['nombre']) ?>
                                </option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <input type="text" id="buscador" class="form-control" placeholder="🔎 Buscar producto...">
                        </div>

                    </div>

                    <div class="table-responsive tabla-scroll">
                        <table class="table table-bordered table-hover tabla-productos">
                            <thead class="table-dark">
                                <tr>
                                    <th>SKU</th>
                                    <th>Producto</th>
                                    <th>Stock</th>
                                    <th>Almacén</th>
                                    <th>Precio</th>
                                    <th width="90">Cant</th>
                                    <th width="60"></th>
                                </tr>
                            </thead>
                            <tbody>

                                <?php foreach($productos as $p): ?>
                                <tr data-categoria="<?= $p['categoria_id'] ?>" data-almacen="<?= $p['almacen_id'] ?>">

                                    <td><?= $p['sku'] ?></td>
                                    <td><?= htmlspecialchars($p['nombre']) ?></td>
                                    <td><span class="badge bg-success"><?= $p['stock'] ?></span></td>
                                    <td><?= htmlspecialchars($p['almacen_nombre']) ?></td>

                                    <td>
                                        <select class="form-select form-select-sm select-precio">
                                            <option value="<?= $p['precio_minorista'] ?>">
                                                Minorista - $<?= number_format($p['precio_minorista'],2) ?>
                                            </option>
                                            <option value="<?= $p['precio_mayorista'] ?>">
                                                Mayorista - $<?= number_format($p['precio_mayorista'],2) ?>
                                            </option>
                                            <option value="<?= $p['precio_distribuidor'] ?>">
                                                Distribuidor - $<?= number_format($p['precio_distribuidor'],2) ?>
                                            </option>
                                        </select>
                                    </td>

                                    <td>
                                        <input type="number" class="form-control form-control-sm cantidad" min="1"
                                            max="<?= $p['stock'] ?>" value="1">
                                    </td>

                                    <td class="text-center">
                                        <button type="button" class="btn btn-success btn-sm"
                                            data-producto-id="<?= $p['id'] ?>" data-almacen-id="<?= $p['almacen_id'] ?>"
                                            data-almacen="<?= htmlspecialchars($p['almacen_nombre']) ?>"
                                            onclick="agregarProducto(this)">
                                            <i class="bi bi-plus"></i>
                                        </button>
                                    </td>

                                </tr>
                                <?php endforeach; ?>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- ================= CARRITO ================= -->
            <div class="col-lg-4">
                <div class="card p-3 carrito">

                    <h5 class="fw-bold mb-3">
                        <i class="bi bi-bag-fill text-success"></i> Carrito
                    </h5>

                    <div class="table-responsive">
                        <table class="table table-sm" id="tablaCarrito">
                            <thead>
                                <tr>
                                    <th>Almacén</th>
                                    <th>Producto</th>
                                    <th>Cant</th>
                                    <th>Sub</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>

                    <hr>

                    <h4 class="text-end fw-bold">
                        Total: $<span id="total">0.00</span>
                    </h4>

                    <button class="btn btn-primary w-100 mt-3" onclick="abrirModalFinalizar()">
                        <i class="bi bi-cash-stack"></i> Finalizar Venta
                    </button>

                </div>
            </div>

        </div>
    </div>
    <div class="modal fade" id="modalFinalizarVenta" tabindex="-1">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title fw-bold"><i class="bi bi-receipt-cutoff me-2"></i>Finalizar Transacción</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body p-4">
                    <div class="row g-4">
                        <div class="col-lg-7 border-end">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="text-uppercase fw-bold m-0 text-primary">Detalle de Salida de Material</h6>
                                <span class="badge bg-warning text-dark">Control de Stock en tiempo real</span>
                            </div>

                            <div class="table-responsive" style="max-height: 350px;">
                                <table class="table table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Producto</th>
                                            <th class="text-center" width="100">Venta</th>
                                            <th class="text-center" width="120">Entregar Hoy</th>
                                            <th class="text-end" width="100">Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tablaConfirmacion">
                                    </tbody>
                                </table>
                            </div>

                            <div class="card bg-light border-0 mt-3">
                                <div class="card-body">
                                    <div class="row align-items-center text-end">
                                        <div class="col-6">
                                            <label class="small fw-bold">Descuento ($)</label>
                                            <input type="number" id="descuentoGeneral"
                                                class="form-control form-control-sm text-end" value="0">
                                        </div>
                                        <div class="col-6">
                                            <span class="text-muted small d-block">Total Final</span>
                                            <h2 class="fw-bold mb-0 text-primary">$<span
                                                    id="totalFinalModal">0.00</span></h2>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-5">
                            <h6 class="text-uppercase fw-bold mb-3 text-primary">Información del Cliente</h6>
                            <div class="input-group mb-3">
                                <select id="selectCliente" class="form-select border-primary">
                                    <?php 
                                $cls = $conexion->query("SELECT * FROM clientes WHERE activo = 1 ORDER BY nombre_comercial ASC");
                                while($c = $cls->fetch_assoc()): ?>
                                    <option value="<?= $c['id'] ?>" data-rfc="<?= $c['rfc'] ?>"
                                        data-rs="<?= $c['razon_social'] ?>" data-cp="<?= $c['codigo_postal'] ?>"
                                        data-regimen="<?= $c['regimen_fiscal'] ?>">
                                        <?= htmlspecialchars($c['nombre_comercial']) ?>
                                    </option>
                                    <?php endwhile; ?>
                                </select>
                                <button class="btn btn-outline-primary" type="button"
                                    onclick="abrirModalNuevoCliente()">
                                    <i class="bi bi-person-plus"></i>
                                </button>
                            </div>

                            <div class="p-3 border rounded mb-3 bg-white shadow-sm">
                                <div class="row g-2">
                                    <div class="col-12"><small class="text-muted d-block">RFC:</small><span id="f_rfc"
                                            class="fw-bold">---</span></div>
                                    <div class="col-12"><small class="text-muted d-block">Razón Social:</small><span
                                            id="f_razon_social" class="fw-bold small">---</span></div>
                                    <div class="col-6"><small class="text-muted d-block">C.P.:</small><span id="f_cp"
                                            class="fw-bold">---</span></div>
                                    <div class="col-6"><small class="text-muted d-block">Régimen:</small><span
                                            id="f_regimen" class="badge bg-info">---</span></div>
                                </div>
                            </div>

                            <div class="mb-0">
                                <label class="form-label small fw-bold text-danger"><i class="bi bi-truck"></i> Notas de
                                    Entrega</label>
                                <textarea id="obsVenta" class="form-control" rows="2"
                                    placeholder="Ej: Recoge el lunes, solo pagó hoy."></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-light border-0">
                    <button class="btn btn-link text-muted me-auto" data-bs-dismiss="modal">Cerrar</button>
                    <button class="btn btn-success btn-lg px-5 shadow fw-bold" onclick="procesarVenta()">
                        <i class="bi bi-check-circle-fill"></i> FINALIZAR VENTA
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modalNuevoCliente" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Registrar Nuevo Cliente</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="formNuevoCliente">
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Nombre Comercial</label>
                                <input type="text" name="nombre_comercial" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Razón Social</label>
                                <input type="text" name="razon_social" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">RFC</label>
                                <input type="text" name="rfc" class="form-control" placeholder="XAXX010101000" required
                                    maxlength="13">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Régimen Fiscal (SAT)</label>
                                <input type="text" name="regimen_fiscal" class="form-control" placeholder="601"
                                    maxlength="3">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Uso CFDI</label>
                                <select name="uso_cfdi" class="form-select">
                                    <option value="G03">G03 - Gastos en general</option>
                                    <option value="P01">P01 - Por definir</option>
                                    <option value="S01">S01 - Sin efectos fiscales</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Correo Electrónico</label>
                                <input type="email" name="correo" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Teléfono</label>
                                <input type="text" name="telefono" class="form-control">
                            </div>
                            <div class="col-md-10">
                                <label class="form-label small fw-bold">Dirección</label>
                                <input type="text" name="direccion" class="form-control">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small fw-bold">CP</label>
                                <input type="text" name="codigo_postal" class="form-control" required maxlength="5">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary px-4">Guardar Cliente</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
    let carrito = [];
    </script>
    <script src="/cfsistem/app/backend/js_ventas/carrito.js"></script>
    <script src="/cfsistem/app/backend/js_ventas/filtros.js"></script>
    <script src="/cfsistem/app/backend/js_ventas/modal_finalizar.js"></script>
    <script src="/cfsistem/app/backend/js_ventas/nuevo_cliente.js"></script>
    <script src="/cfsistem/app/backend/js_ventas/procesar_venta.js"></script>
    <script>
    document.getElementById('selectCliente').addEventListener('change', function() {
    const selected = this.options[this.selectedIndex];
    
    // Actualizar los textos del modal
    document.getElementById('f_rfc').textContent = selected.dataset.rfc || '---';
    document.getElementById('f_razon_social').textContent = selected.dataset.rs || '---';
    document.getElementById('f_cp').textContent = selected.dataset.cp || '---';
    document.getElementById('f_regimen').textContent = selected.dataset.regimen || '---';
});
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    </script>
</body>

</html>