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
<div class="modal fade" id="modalAjuste" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form id="formAjuste"> 
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Ajuste de Faltantes</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="ajuste_compra_id" id="ajuste_compra_id">
                    
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th>Faltante</th>
                                    <th>Cantidad a Recibir</th>
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
                    <button type="submit" class="btn btn-primary">Confirmar Entrada</button>
                </div>
            </div>
        </form>
    </div>
</div>
   <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // 1. Inicialización de constantes (Manteniendo tus nombres originales)
    const modalRegistro = new bootstrap.Modal('#modalRegistro');
    const modalProd = new bootstrap.Modal('#modalNuevoProducto');
    const modalDist = new bootstrap.Modal('#modalDistribucion');
    const modalVer = new bootstrap.Modal('#modalVerDetalle');
    const modalAjusteForm = new bootstrap.Modal('#modalAjuste');

    // 2. Datos del servidor pasados a JSON (Para que JS los use sin recargar)
    const almacenes = <?= json_encode($almacenes_array) ?>;
    
    // Esta es la variable clave que corregirá que no se vea la lista
    const productosBase = [
        <?php
        // Reiniciamos el puntero del resultado de productos por si se usó antes
        $productos_res->data_seek(0); 
        while($p = $productos_res->fetch_assoc()): 
        ?>
        {
            id: "<?= $p['id'] ?>",
            nombre: "<?= addslashes($p['nombre']) ?>",
            sku: "<?= addslashes($p['sku']) ?>"
        },
        <?php endwhile; ?>
    ];

    let filaEnDistribucion = null;

    // --- FUNCIONES DE CARGA DINÁMICA ---
    // Aquí puedes añadir funciones extra si las necesitas, 
    // pero con productosBase ya disponible, tus otros scripts 
    // podrán leer la lista de productos correctamente.
</script>

 <script src="/cfsistem/app/backend/compras_js/funcion_detalle.js"></script> 
<script src="/cfsistem/app/backend/compras_js/funcion_ajuste.js"></script>
<script src="/cfsistem/app/backend/compras_js/abrirmodal.js"></script>
<script src="/cfsistem/app/backend/compras_js/elementos_modal_compra.js"></script>
<!-- <script src="/cfsistem/app/backend/compras_js/guardando_prodcuto_nuevo.js"></script> -->
 <script>
 

    // GUARDADO PRODUCTO NUEVO (MANTENIDO)
    $('#formNuevoProducto').on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            url: '/cfsistem/app/backend/almacen/guardar_producto_simple.php',
            type: 'POST',
            data: $(this).serialize(),
            success: function(r) {
                let res = JSON.parse(r);
                if (res.status === 'success') {
                    $('.select-prod').append(
                        `<option value="${res.id}">${res.nombre} (${res.sku})</option>`);
                    modalProd.hide();
                    Swal.fire('Éxito', 'Producto creado', 'success');
                }
            }
        });
    });
 </script>
<script src="/cfsistem/app/backend/compras_js/guardando_compra.js"></script></body>