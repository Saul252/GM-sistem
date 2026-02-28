<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/sidebar.php';
require_once __DIR__ . '/../../config/conexion.php';
protegerPagina();

$paginaActual = 'Egresos';

// Obtener Almacenes para la distribución
$almacenes_res = $conexion->query("SELECT id, nombre, codigo FROM almacenes WHERE activo = 1");
$almacenes_array = [];
while($a = $almacenes_res->fetch_assoc()) { $almacenes_array[] = $a; }

// Obtener Categorías para el modal de producto nuevo
$categorias_res = $conexion->query("SELECT * FROM categorias ORDER BY nombre ASC");
$categorias = [];
while($c = $categorias_res->fetch_assoc()) { $categorias[] = $c; }

// Obtener Productos actuales
$productos_res = $conexion->query("SELECT id, nombre, sku FROM productos WHERE activo = 1 ORDER BY nombre ASC");
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Egresos | Sistema Almacén</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <link href="/cfsistem/css/compras.css" rel="stylesheet">

</head>

<body>
    <?php renderSidebar($paginaActual); ?>

    <main class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold mb-0">Egresos</h2>
                <p class="text-muted">Gestión de Compras (Inventario) y Gastos Operativos</p>
            </div>
            <div class="gap-2 d-flex">
                <button class="btn btn-primary px-4 shadow-sm" onclick="abrirModal('compra')">
                    <i class="bi bi-cart-plus me-2"></i> Nueva Compra
                </button>
                <button class="btn btn-warning px-4 shadow-sm" onclick="abrirModal('gasto')">
                    <i class="bi bi-cash-stack me-2"></i> Nuevo Gasto
                </button>
            </div>
        </div>

        <div class="card card-custom p-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Folio</th>
                            <th>Tipo</th>
                            <th>Entidad</th>
                            <th>Total Factura</th>
                            <th>Estado</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        // SQL Ajustado para traer la columna tiene_faltantes
                        $sqlEgresos = "(SELECT id, folio, proveedor as entidad, fecha_compra as fecha, total, 'compra' as tipo, tiene_faltantes FROM compras)
                                       UNION
                                       (SELECT id, folio, beneficiario as entidad, fecha_gasto as fecha, total, 'gasto' as tipo, 0 as tiene_faltantes FROM gastos)
                                       ORDER BY fecha DESC LIMIT 50";
                        $resEgresos = $conexion->query($sqlEgresos);
                        while($e = $resEgresos->fetch_assoc()): 
                            $hayFaltantes = ($e['tipo'] == 'compra' && $e['tiene_faltantes'] == 1);
                        ?>
                        <tr class="<?= $hayFaltantes ? 'table-warning' : '' ?>">
                            <td><?= date('d/m/Y', strtotime($e['fecha'])) ?></td>
                            <td class="fw-bold"><?= $e['folio'] ?></td>
                            <td><span
                                    class="badge rounded-pill <?= $e['tipo'] == 'compra' ? 'bg-primary' : 'bg-warning text-dark' ?>"><?= strtoupper($e['tipo']) ?></span>
                            </td>
                            <td><?= htmlspecialchars($e['entidad']) ?></td>
                            <td class="fw-bold text-primary">$<?= number_format($e['total'], 2) ?></td>
                            <td>
                                <?php if($hayFaltantes): ?>
                                <span class="badge bg-danger">INCOMPLETO</span>
                                <?php else: ?>
                                <span class="badge bg-success">COMPLETO</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end">
                                <div class="btn-group">
                                    <button class="btn btn-sm btn-outline-dark"
                                        onclick="verDetalle('<?= $e['tipo'] ?>', <?= $e['id'] ?>)">Ver</button>
                                    <?php if($hayFaltantes): ?>
                                    <button class="btn btn-sm btn-danger" onclick="abrirAjuste(<?= $e['id'] ?>)">
                                        <i class="bi bi-tools"></i> AJUSTE
                                    </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <div class="modal fade" id="modalRegistro" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-xl">
            <form id="formEgreso" class="modal-content border-0 shadow-lg" enctype="multipart/form-data">
                <div class="modal-header py-3" id="modalHeader">
                    <h5 class="modal-title fw-bold text-white"><i class="bi bi-plus-circle me-2"></i> Nuevo Registro
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <input type="hidden" name="tipo_egreso" id="tipo_egreso">

                    <div class="row g-3 mb-4 bg-light p-3 rounded border">
                        <div class="col-md-2">
                            <label class="form-label small fw-bold">FOLIO / FACTURA</label>
                            <input type="text" name="folio" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold" id="lblEntidad">PROVEEDOR</label>
                            <input type="text" name="entidad" class="form-control" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-danger">MONTO TOTAL FACTURA ($)</label>
                            <input type="number" step="0.01" name="total_factura" id="total_factura"
                                class="form-control border-danger fw-bold" placeholder="Ej: 1200.00" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">COMPROBANTE (PDF)</label>
                            <input type="file" name="documento" class="form-control" accept=".pdf,image/*">
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
                        <div>
                            <h6 class="fw-bold text-primary mb-0"><i class="bi bi-list-stars me-2"></i>DESGLOSE DE
                                PARTIDAS</h6>
                            <small class="text-muted">Indique los productos o servicios que componen el total
                                anterior.</small>
                        </div>
                        <button type="button" class="btn btn-sm btn-dark" onclick="agregarFila()">+ Agregar
                            Concepto</button>
                    </div>

                    <div id="contenedorItems"></div>

                    <div id="alertaMonto" class="alert alert-warning mt-3 d-none">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        La suma del desglose no coincide con el total de la factura.
                    </div>
                </div>
                <div class="modal-footer bg-light border-top">
                    <div class="me-auto">
                        <span class="text-muted small d-block">Diferencia por desglosar:</span>
                        <span id="txtDiferencia" class="h4 fw-bold">$ 0.00</span>
                    </div>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" id="btnGuardarEgreso" class="btn btn-primary px-5 fw-bold shadow">GUARDAR
                        REGISTRO</button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="modalNuevoProducto" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-lg">
            <form id="formNuevoProducto" class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-success text-white py-2">
                    <h5 class="modal-title fs-6">Nuevo Producto en Catálogo</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-2">
                        <div class="col-md-4">
                            <label class="form-label small mb-1">SKU</label>
                            <input type="text" name="sku" class="form-control form-control-sm" required>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label small mb-1">Nombre</label>
                            <input type="text" name="nombre" class="form-control form-control-sm" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small mb-1">Categoría</label>
                            <select name="categoria_id" class="form-select form-select-sm">
                                <?php foreach($categorias as $c): ?>
                                <option value="<?= $c['id'] ?>"><?= $c['nombre'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small mb-1">Unidad Medida</label>
                            <input type="text" name="unidad_medida" class="form-control form-control-sm" value="PZA">
                        </div>
                        <div class="col-12 mt-3"><small class="fw-bold text-success">DATOS FISCALES (SAT)</small>
                            <hr class="my-1">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small mb-1">Clave Prod/Serv</label>
                            <input type="text" name="fiscal_clave_prod" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small mb-1">Clave Unidad</label>
                            <input type="text" name="fiscal_clave_unit" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small mb-1">IVA %</label>
                            <select name="impuesto_iva" class="form-select form-select-sm">
                                <option value="16.00">16%</option>
                                <option value="8.00">8%</option>
                                <option value="0.00">0%</option>
                            </select>
                        </div>
                        <input type="hidden" name="precio_adquisicion" value="0.00">
                    </div>
                </div>
                <div class="modal-footer p-2">
                    <button type="submit" class="btn btn-success btn-sm w-100">Registrar en Catálogo</button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="modalDistribucion" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-dark text-white py-2">
                    <h5 class="modal-title fs-6">Repartir Stock</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="listaAlmacenesDist"></div>
                </div>
                <div class="modal-footer p-2">
                    <button type="button" class="btn btn-primary btn-sm w-100" id="btnConfirmarDist">Confirmar</button>
                </div>
            </div>
        </div>
    </div>
<div class="modal fade" id="modalVerDetalle" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title"><i class="bi bi-eye me-2"></i> Detalle de Egreso</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="contenidoDetalle">
                    </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalAjuste" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg">
        <form id="formAjuste" class="modal-content border-0 shadow">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="bi bi-tools me-2"></i> Ajuste de Recepción (Faltantes)</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="compra_id" id="ajuste_compra_id">
                <div class="alert alert-info small">
                    Aquí puede registrar la entrada física de los productos que marcaron como faltantes originalmente.
                </div>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Pendiente</th>
                                <th>Recibir Ahora</th>
                                <th>Almacén</th>
                            </tr>
                        </thead>
                        <tbody id="tablaAjuste">
                            </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="submit" class="btn btn-danger">Confirmar Entrada de Faltantes</button>
            </div>
        </form>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script src="/cfsistem/app/backend/compras_js/abrirmodal.js"></script>
<script src="/cfsistem/app/backend/compras_js/elementos_modal_compra.js"></script>
<script src="/cfsistem/app/backend/compras_js/guardando_prodcuto_nuevo.js"></script>
<script src="/cfsistem/app/backend/compras_js/guardando_compra.js"></script>

<script>
    const modalRegistro = new bootstrap.Modal('#modalRegistro');
    const modalProd = new bootstrap.Modal('#modalNuevoProducto');
    const modalDist = new bootstrap.Modal('#modalDistribucion');
    const modalVer = new bootstrap.Modal('#modalVerDetalle');
    const modalAjusteForm = new bootstrap.Modal('#modalAjuste');
    const almacenes = <?= json_encode($almacenes_array) ?>;
    
    // Sobreescribimos cualquier verDetalle anterior
    function verDetalle(tipo, id) {
        console.log("Ejecutando verDetalle para:", tipo, id); // Para debug
        modalVer.show();
        $('#contenidoDetalle').html('<div class="text-center p-5"><div class="spinner-border text-primary"></div></div>');

        $.get('/cfsistem/app/backend/compras/obtener_detalle.php', { tipo: tipo, id: id }, function(html) {
            $('#contenidoDetalle').html(html);
        }).fail(function() {
            $('#contenidoDetalle').html('<div class="alert alert-danger">Error: No se encontró el archivo PHP.</div>');
        });
    }

    function abrirAjuste(id) {
        $('#ajuste_compra_id').val(id);
        $('#tablaAjuste').html('<tr><td colspan="4" class="text-center">Cargando...</td></tr>');
        modalAjusteForm.show();

        $.getJSON('/cfsistem/app/backend/compras/obtener_pendientes.php', { id: id }, function(data) {
            let html = '';
            data.forEach(item => {
                html += `<tr>
                    <td>${item.nombre}</td>
                    <td class="text-danger fw-bold">${item.cantidad_faltante}</td>
                    <td><input type="number" step="0.01" class="form-control form-control-sm" name="ajuste[${item.id}][cantidad]" value="${item.cantidad_faltante}"></td>
                    <td>
                        <input type="hidden" name="ajuste[${item.id}][producto_id]" value="${item.producto_id}">
                        <input type="hidden" name="ajuste[${item.id}][detalle_id]" value="${item.id}">
                        <select class="form-select form-select-sm" name="ajuste[${item.id}][almacen_id]">
                            ${almacenes.map(a => `<option value="${a.id}">${a.nombre}</option>`).join('')}
                        </select>
                    </td>
                </tr>`;
            });
            $('#tablaAjuste').html(html || '<tr><td colspan="4">Sin pendientes.</td></tr>');
        });
    }

    // CORRECCIÓN IMPORTANTE: La URL del envío debe ser procesar_ajuste.php
    $('#formAjuste').on('submit', function(e) {
        e.preventDefault();
        $.post('/cfsistem/app/backend/compras/procesar_ajuste.php', $(this).serialize(), function(res) {
            if(res.status === 'success') {
                Swal.fire("Listo", res.message, "success").then(() => location.reload());
            } else {
                Swal.fire("Error", res.message, "error");
            }
        }, 'json');
    });
</script>
</body>