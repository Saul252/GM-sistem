<?php 
$ruta = __DIR__ . '/egresosComponets/modalCompra.php';
if (!file_exists($ruta)) {
    echo "<script>console.error('ERROR: El archivo del modal no existe en: $ruta');</script>";
}
require_once $ruta;
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Egresos | Sistema Almacén</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <?php if (function_exists('cargarEstilos')) { cargarEstilos(); } ?>

    <style>
    :root {
        --nav-height: 65px;
        --sidebar-width: 260px;
    }

    .main-content {
        margin-left: var(--sidebar-width);
        margin-top: var(--nav-height);
        padding: 1.5rem 2rem;
        width: calc(100% - var(--sidebar-width));
        min-height: calc(100vh - var(--nav-height));
        transition: all 0.3s ease;
        display: block;
    }

    .card-kpi {
        border: none;
        border-radius: 12px;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }

    .table-responsive {
        border-radius: 12px;
        background: white;
        border: 1px solid #e2e8f0;
    }

    @media (max-width: 992px) {
        .main-content {
            margin-left: 0;
            width: 100%;
            padding: 1rem;
        }
    }
    </style>
</head>

<body class="bg-light">

    <?php renderizarLayout($tituloPagina); ?>

    <main class="main-content">
        <div class="container-fluid">

            <div class="row align-items-center mb-4">
                <div class="col-md-7">
                    <h2 class="fw-bold text-dark mb-1">Compras y gastos</h2>
                    <p class="text-muted mb-0">Gestión de flujo de caja e inventario</p>
                </div>
                <div class="col-md-5 text-md-end mt-3 mt-md-0">
                    <div class="d-grid d-md-flex gap-2 justify-content-md-end">
                        <button class="btn btn-warning" onclick="abrirModalGasto()">
                            <i class="bi bi-cash-stack"></i> Nuevo Gasto
                        </button>

                        <button class="btn btn-primary" onclick="abrirModalCompra()">
                            <i class="bi bi-cart-plus"></i> Nueva Compra
                        </button>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mb-4 border-0 rounded-3">
                <div class="card-body p-3">
                    <form method="GET" class="row g-3 align-items-end">
                        <div class="col-6 col-md-3">
                            <label class="form-label small fw-bold text-secondary">DESDE</label>
                            <input type="date" name="desde" class="form-control form-control-sm"
                                value="<?= $fecha_desde ?>">
                        </div>
                        <div class="col-6 col-md-3">
                            <label class="form-label small fw-bold text-secondary">HASTA</label>
                            <input type="date" name="hasta" class="form-control form-control-sm"
                                value="<?= $fecha_hasta ?>">
                        </div>
                        <div class="col-12 col-md-6 d-flex gap-2">
                            <button type="submit" class="btn btn-dark btn-sm w-100 fw-bold">Filtrar Movimientos</button>
                            <a href="egresos.php" class="btn btn-outline-secondary btn-sm w-100">Limpiar</a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="card card-kpi border-start border-primary border-4 p-2">
                        <div class="card-body py-2">
                            <p class="text-muted small fw-bold mb-1">TOTAL COMPRAS</p>
                            <h3 class="fw-bold mb-0 text-primary">$ <?= number_format($totalSumCompras, 2) ?></h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card card-kpi border-start border-warning border-4 p-2">
                        <div class="card-body py-2">
                            <p class="text-muted small fw-bold mb-1">GASTOS OPERATIVOS</p>
                            <h3 class="fw-bold mb-0 text-warning">$ <?= number_format($totalSumGastos, 2) ?></h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card card-kpi border-start border-danger border-4 p-2">
                        <div class="card-body py-2">
                            <p class="text-danger small fw-bold mb-1">TOTAL EGRESOS</p>
                            <h3 class="fw-bold mb-0 text-dark">$ <?= number_format($granTotalEgresos, 2) ?></h3>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-3">ID</th>
                                <th>Fecha</th>
                                <th>Folio</th>
                                <th>Tipo</th>
                                <th>Entidad</th>
                                <th class="text-end">Total</th>
                                <th class="text-center">Evidencia</th>
                                <th class="text-end pe-3">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(!empty($egresos)): ?>
                            <?php foreach($egresos as $e): ?>
                            <tr>
                                <td class="ps-3"><span class="text-muted small">#</span><?= $e['id'] ?></td>

                                <td class="text-muted small"><?= date('d/m/Y', strtotime($e['fecha'])) ?></td>
                                <td class="fw-bold text-dark"><?= $e['folio'] ?></td>
                                <td>
                                    <span
                                        class="badge rounded-pill <?= $e['tipo'] == 'compra' ? 'bg-primary' : 'bg-warning text-dark' ?>">
                                        <?= strtoupper($e['tipo']) ?>
                                    </span>
                                </td>
                                <td><?= htmlspecialchars($e['entidad']) ?></td>
                                <td class="fw-bold text-end">$<?= number_format($e['total'], 2) ?></td>
                                <td class="text-center">
                                    <?php if(!empty($e['documento_url'])): ?>
                                    <a href="../../uploads/evidencias/<?= $e['documento_url'] ?>" target="_blank"
                                        class="text-primary h5">
                                        <i class="bi bi-file-earmark-pdf"></i>
                                    </a>
                                    <?php else: ?>
                                    <span class="text-muted small">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end pe-3">
                                    <button class="btn btn-sm btn-light border"
                                        onclick="verDetalle('<?= $e['tipo'] ?>', <?= $e['id'] ?>)">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center py-4 text-muted">No se encontraron movimientos en
                                    este rango.</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
    <div class="modal fade" id="modalGasto" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <form id="formNuevoGasto" enctype="multipart/form-data">
                <div class="modal-content">
                    <div class="modal-header bg-warning text-dark">
                        <h5 class="modal-title fw-bold"><i class="bi bi-cash-stack me-2"></i> Registrar Nuevo Gasto</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Folio/Factura</label>
                                <input type="text" name="folio" class="form-control" placeholder="Ej: GAS-001" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Almacén Destino</label>
                                <select name="almacen_id" class="form-select bg-light"
                                    <?= ($_SESSION['rol_id'] != 1) ? 'readonly style="pointer-events: none;"' : '' ?>
                                    required>
                                    <?php foreach($almacenes as $alm): ?>
                                    <option value="<?= $alm['id'] ?>"
                                        <?= ($_SESSION['almacen_id'] == $alm['id']) ? 'selected' : '' ?>>
                                        <?= $alm['nombre'] ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if($_SESSION['rol_id'] != 1): ?>
                                <div class="form-text text-muted">Asignado automáticamente a tu almacén.</div>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-8">
                                <label class="form-label small fw-bold">Beneficiario (Quién recibe)</label>
                                <input type="text" name="beneficiario" class="form-control"
                                    placeholder="Ej: CFE, Gasolinera, Juan Pérez" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Método de Pago</label>
                                <select name="metodo_pago" class="form-select">
                                    <option value="Efectivo">Efectivo</option>
                                    <option value="Transferencia">Transferencia</option>
                                    <option value="Tarjeta">Tarjeta</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Comprobante (Evidencia)</label>
                                <input type="file" name="documento" class="form-control" accept=".jpg,.png,.pdf">
                            </div>
                        </div>

                        <hr>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="fw-bold mb-0">Conceptos del Gasto</h6>
                            <button type="button" class="btn btn-sm btn-outline-dark" onclick="agregarFilaGasto()">
                                <i class="bi bi-plus-circle"></i> Agregar Concepto
                            </button>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-sm" id="tablaConceptosGasto">
                                <thead>
                                    <tr>
                                        <th>Descripción</th>
                                        <th width="120">Cant.</th>
                                        <th width="150">Precio Unit.</th>
                                        <th width="120">Subtotal</th>
                                        <th width="40"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><input type="text" name="desc[]" class="form-control form-control-sm"
                                                required></td>
                                        <td><input type="number" name="cant[]" class="form-control form-control-sm cant"
                                                value="1" step="any" oninput="calcularGasto()"></td>
                                        <td><input type="number" name="precio[]"
                                                class="form-control form-control-sm precio" value="0.00" step="any"
                                                oninput="calcularGasto()"></td>
                                        <td class="text-end fw-bold subtotal_fila">$0.00</td>
                                        <td></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="row">
                            <div class="col-md-8">
                                <label class="form-label small fw-bold">Observaciones</label>
                                <textarea name="observaciones" class="form-control" rows="2"></textarea>
                            </div>
                            <div class="col-md-4 text-end">
                                <h4 class="text-muted mb-0">TOTAL</h4>
                                <h2 class="fw-bold text-dark" id="txtTotalGasto">$ 0.00</h2>
                                <input type="hidden" name="total_final" id="inputTotalGasto" value="0">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-warning fw-bold">Guardar Gasto</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script src="/cfsistem/app/backend/compras_js/modalGastoslogica.js"></script>
<script>
    // Forzamos que sea global con window.
    window.DATA_COMPRAS = {
        productos: <?php echo json_encode($productos); ?>,
        almacenes: <?php echo json_encode($almacenes); ?>
    };
    // Imprime esto en la consola para que verifiques si hay datos
    console.log("Productos cargados:", window.DATA_COMPRAS.productos);
</script>

<?php require_once __DIR__ . '/egresosComponets/modalCompra.php'; ?>
</body>

</html>