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

            <div class="card mb-4 shadow-sm border-0">
                <div class="card mb-4 shadow-sm border-0">
                    <div class="card-body bg-light rounded">
                        <?php 
        $periodo_sel = $_GET['periodo_filtro'] ?? 'mes'; 
        $tipo_sel    = $_GET['tipo_filtro'] ?? 'todos';
        ?>
                        <form id="formFiltros" method="GET" action="">
                            <div class="row g-3 align-items-end">

                                <div class="col-md-2">
                                    <label class="form-label fw-bold small text-uppercase text-primary">Periodo:</label>
                                    <select id="filtro_rapido" name="periodo_filtro"
                                        class="form-select border-primary fw-bold">
                                        <option value="hoy" <?= ($periodo_sel == 'hoy') ? 'selected' : '' ?>>Hoy
                                        </option>
                                        <option value="ayer" <?= ($periodo_sel == 'ayer') ? 'selected' : '' ?>>Ayer
                                        </option>
                                        <option value="semana" <?= ($periodo_sel == 'semana') ? 'selected' : '' ?>>Esta
                                            Semana</option>
                                        <option value="mes" <?= ($periodo_sel == 'mes') ? 'selected' : '' ?>>Este Mes
                                        </option>
                                        <option value="personalizado"
                                            <?= ($periodo_sel == 'personalizado') ? 'selected' : '' ?>>📅 Personalizado
                                        </option>
                                    </select>
                                </div>

                                <div class="col-md-2">
                                    <label class="form-label fw-bold small text-uppercase">Desde:</label>
                                    <input type="date" name="desde" id="fecha_desde" class="form-control"
                                        value="<?= $fecha_desde ?>"
                                        <?= ($periodo_sel !== 'personalizado') ? 'disabled' : '' ?>>
                                </div>

                                <div class="col-md-2">
                                    <label class="form-label fw-bold small text-uppercase">Hasta:</label>
                                    <input type="date" name="hasta" id="fecha_hasta" class="form-control"
                                        value="<?= $fecha_hasta ?>"
                                        <?= ($periodo_sel !== 'personalizado') ? 'disabled' : '' ?>>
                                </div>

                                <div class="col-md-2">
                                    <label class="form-label fw-bold small text-uppercase text-primary">Mostrar:</label>
                                    <select name="tipo_filtro" id="tipo_filtro"
                                        class="form-select fw-bold border-primary shadow-sm">
                                        <option value="todos" <?= ($tipo_sel == 'todos') ? 'selected' : '' ?>>📁 Todos
                                        </option>
                                        <option value="compra" <?= ($tipo_sel == 'compra') ? 'selected' : '' ?>>🛒
                                            Compras</option>
                                        <option value="gasto" <?= ($tipo_sel == 'gasto') ? 'selected' : '' ?>>💸 Gastos
                                        </option>
                                    </select>
                                </div>

                                <?php if ($_SESSION['rol_id'] == 1): ?>
                                <div class="col-md-2">
                                    <label class="form-label fw-bold small text-uppercase">Almacén:</label>
                                    <select id="almacen_filtro" name="almacen_filtro" class="form-select">
                                        <option value="0">🌐 Todos</option>
                                        <?php foreach ($almacenes as $alm): ?>
                                        <option value="<?= $alm['id'] ?>"
                                            <?= (isset($_GET['almacen_filtro']) && $_GET['almacen_filtro'] == $alm['id']) ? 'selected' : '' ?>>
                                            📍 <?= $alm['nombre'] ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <?php endif; ?>

                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-primary w-100 fw-bold">
                                        <i class="bi bi-funnel"></i> FILTRAR
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
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
                                <th>Almacen</th>
                                <th>Fecha</th>
                                <th>Folio</th>
                                <th>Tipo</th>
                                <th>Entidad</th>
                                <th class="text-end">Total</th>
                                <th class="text-center">Faltantes</th>
                                <th class="text-center">Evidencia</th>
                                <th class="text-end pe-3">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(!empty($egresos)): ?>
                            <?php foreach($egresos as $e): ?>
                            <tr>
                                <td class="ps-3"><span class="text-muted small">#</span><?= $e['id'] ?></td>
                                <td>
                                    <span class="text-secondary small fw-semibold">
                                        <i class="bi bi-geo-alt-fill text-danger" style="font-size: 0.7rem;"></i>
                                        <?= htmlspecialchars($e['almacen_nombre']) ?>
                                    </span>
                                </td>

                                <td class="text-muted small"><?= date('d/m/Y', strtotime($e['fecha'])) ?></td>
                                <td class="fw-bold text-dark">
                                    <?php      $prefijo = ($e['tipo'] == 'compra') ? 'FC-' : 'FG-'; 
                                           // Imprimimos el prefijo unido al folio     
                                              echo $prefijo . $e['folio'];     ?>
                                </td>
                                <td>
                                    <span
                                        class="badge rounded-pill <?= $e['tipo'] == 'compra' ? 'bg-primary' : 'bg-warning text-dark' ?>">
                                        <?= strtoupper($e['tipo']) ?>
                                    </span>
                                </td>

                                <td><?= htmlspecialchars($e['entidad']) ?></td>
                                <td class="fw-bold text-end">$<?= number_format($e['total'], 2) ?></td>

                                <td class="text-center">
                                    <?php if($e['tipo'] == 'compra'): ?>
                                    <?php if($e['piezas_faltantes'] > 0): ?>
                                    <div class="d-flex flex-column align-items-center">
                                        <span class="badge bg-danger mb-1" style="font-size: 0.7rem;">
                                            FALTAN: <?= number_format($e['piezas_faltantes'], 2) ?>
                                        </span>

                                    </div>
                                    <?php elseif( $e['piezas_faltantes'] <= 0): ?>
                                    <span class="badge bg-success" style="font-size: 0.7rem;">
                                        <i class="bi bi-check-circle"></i> COMPLETADO
                                    </span>
                                    <?php else: ?>
                                    <span class="text-muted small">-</span>
                                    <?php endif; ?>
                                    <?php else: ?>
                                    <span class="text-muted small">-</span>
                                    <?php endif; ?>
                                </td>

                                <td class="text-center">
                                    <?php if(!empty($e['documento_url'])): ?>
                                    <?php 
            // Determinamos el prefijo de la ruta según el tipo
            // Si es gasto, añadimos la carpeta intermedia
            $ruta_base = ($e['tipo'] == 'gasto') ? 'uploads/evidencias/' : '';
        ?>

                                    <a href="../../<?= $ruta_base . $e['documento_url'] ?>" target="_blank"
                                        class="text-primary h5">
                                        <i class="bi bi-file-earmark-pdf"></i>
                                    </a>
                                    <?php else: ?>
                                    <span class="text-muted small">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end pe-3">
                                    <?php 
// 1. Verificamos que exista la llave y que NO sea NULL
// 2. Verificamos que sea mayor a 0 para mostrar el botón
if (isset($e['piezas_faltantes']) && $e['piezas_faltantes'] !== null): ?>

                                    <?php if ($e['piezas_faltantes'] <= 0): ?>

                                    <?php else: ?>
                                    <button class="btn btn-sm btn-outline-danger py-0 px-2" style="font-size: 0.65rem;"
                                        onclick="abrirModalAjuste(<?= $e['id'] ?>, '<?= $e['folio'] ?>')">
                                        <i class="bi bi-wrench-adjustable"></i> Ajustar
                                    </button>
                                    <?php endif; ?>

                                    <?php endif; ?>

                                    <button class="btn btn-sm btn-light border"
                                        onclick="verDetalle('<?= $e['tipo'] ?>', <?= $e['id'] ?>)">
                                        <i class="bi bi-eye"></i>
                                    </button>  
                                   <?php if ($e['tipo'] == 'compra'): ?>
                <span class="badge bg-success">Compra</span>
                <button class="btn btn-sm btn-light border" 
                        onclick="confirmarCancelacionCompra('<?= $e['id'] ?>', '<?= $e['folio'] ?>')">
                    <i class="fas fa-ban"></i> Anular
                </button>
                
            <?php else: ?>
                <span class="badge bg-info">Gasto</span>
                <button class="btn btn-sm btn-light border" 
                        onclick="confirmarCancelacionGasto('<?= $e['id'] ?>', '<?= $e['folio'] ?>')">
                    <i class="fas fa-ban"></i> Anular
                </button>
            <?php endif; ?>
                                    
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php else: ?>
                            <tr>
                                <td colspan="9" class="text-center py-4 text-muted">No se encontraron movimientos en
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
                                <input type="text" id="folio_gasto" name="folio" class="form-control"
                                    placeholder="Cargando..." readonly required>
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
    <script>$(document).on('show.bs.modal', '.modal', function () {
    const zIndex = 1050 + (10 * $('.modal:visible').length);
    $(this).css('z-index', zIndex);
    setTimeout(function() {
        $('.modal-backdrop').not('.modal-stack').css('z-index', zIndex - 1).addClass('modal-stack');
    }, 0);
});</script>
    <script>
    // Forzamos que sea global con window.
    window.DATA_COMPRAS = {
        productos: <?php echo json_encode($productos); ?>,
        almacenes: <?php echo json_encode($almacenes); ?>
    };
    // Imprime esto en la consola para que verifiques si hay datos
    console.log("Productos cargados:", window.DATA_COMPRAS.productos);
    </script>
    <script src="/cfsistem/app/backend/compras_js/modalGastoslogica.js"></script>

    <script>
    (function() {
        document.addEventListener('DOMContentLoaded', function() {
            const f = {
                form: document.getElementById('formFiltros'),
                periodo: document.getElementById('filtro_rapido'),
                desde: document.getElementById('fecha_desde'),
                hasta: document.getElementById('fecha_hasta'),
                almacen: document.getElementById('almacen_filtro'),
                tipo: document.getElementById('tipo_filtro')
            };

            const formatearFecha = (date) => {
                const y = date.getFullYear();
                const m = String(date.getMonth() + 1).padStart(2, '0');
                const d = String(date.getDate()).padStart(2, '0');
                return `${y}-${m}-${d}`;
            };

            const procesarEnvio = () => {
                // Habilitar campos para que viajen en la URL
                f.desde.disabled = false;
                f.hasta.disabled = false;
                f.form.submit();
            };

            // Evento Periodo Rápido
            f.periodo.addEventListener('change', function() {
                if (this.value === 'personalizado') {
                    f.desde.disabled = false;
                    f.hasta.disabled = false;
                    f.desde.focus();
                    return;
                }

                let hoy = new Date();
                let d = new Date();
                let h = new Date();

                switch (this.value) {
                    case 'ayer':
                        d.setDate(hoy.getDate() - 1);
                        h.setDate(hoy.getDate() - 1);
                        break;
                    case 'semana':
                        const day = hoy.getDay();
                        const diff = hoy.getDate() - day + (day === 0 ? -6 : 1);
                        d.setDate(diff);
                        break;
                    case 'mes':
                        d = new Date(hoy.getFullYear(), hoy.getMonth(), 1);
                        break;
                }

                f.desde.value = formatearFecha(d);
                f.hasta.value = formatearFecha(h);
                procesarEnvio();
            });

            // Evento Cambios Manuales en Fechas
            [f.desde, f.hasta].forEach(el => {
                el.addEventListener('change', () => {
                    if (f.periodo.value !== 'personalizado') {
                        f.periodo.value = 'personalizado';
                    }
                    if (f.desde.value && f.hasta.value) {
                        procesarEnvio();
                    }
                });
            });

            // Evento Almacén y Tipo
            if (f.almacen) f.almacen.addEventListener('change', procesarEnvio);
            if (f.tipo) f.tipo.addEventListener('change', procesarEnvio);

            // Asegurar envío manual por botón
            f.form.addEventListener('submit', function(e) {
                f.desde.disabled = false;
                f.hasta.disabled = false;
            });
        });
    })();
    </script>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Escuchamos la apertura del modal usando JS Nativo (Vanilla JS)
        document.addEventListener('show.bs.modal', function(event) {
            // Obtenemos el ID del modal que se está abriendo
            const modalId = event.target.id;

            // AHORA COINCIDE: modalGasto
            if (modalId === 'modalGasto') {
                console.log("Modal de Gasto detectado. Solicitando folio...");

                const inputFolio = document.getElementById('folio_gasto');
                if (!inputFolio) return;

                // Petición al controlador
                fetch('egresosController.php?action=getSiguienteFolioGasto')
                    .then(res => {
                        // Si el servidor falla (404, 500), lo veremos aquí
                        if (!res.ok) throw new Error("Código de error servidor: " + res.status);
                        return res.json();
                    })
                    .then(data => {
                        console.log("Respuesta del servidor para el folio:",
                        data); // Mira esto en la consola
                        if (data.success) {
                            document.getElementById('folio_gasto').value = data.folio;
                        } else {
                            console.error("El modelo falló:", data.message);
                        }
                    })
                    .catch(err => {
                        console.error(
                            "Error al procesar el JSON. Es posible que el PHP esté enviando errores de texto antes del JSON.",
                            err);
                    });
            }
        });
    });
    </script>
 <script>
function confirmarCancelacionCompra(id, folio) {
    Swal.fire({
        title: '¿Anular Compra ' + folio + '?',
        text: "Se restará el stock y se eliminarán los lotes. Esta acción no se puede deshacer.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, anular compra',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            // Mostramos un cargando manual para evitar clics dobles
            Swal.fire({
                title: 'Procesando...',
                text: 'Por favor espera',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });

            $.ajax({
                // AJUSTA ESTA RUTA: Asegúrate que apunte a tu controlador
                url: '../controllers/egresosController.php?action=cancelarCompra', 
                type: 'POST',
                data: { id: id },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        Swal.fire('¡Anulada!', response.message, 'success').then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire('Atención', response.message, 'error');
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.error("Error del servidor:", jqXHR.responseText);
                    Swal.fire('Error de Sistema', 'No se pudo procesar la cancelación. Revisa la consola (F12).', 'error');
                }
            });
        }
    });
}
</script>
<script>
    function confirmarCancelacionGasto(id, folio) {
    Swal.fire({
        title: `¿Anular Gasto: ${folio}?`,
        text: "El registro se marcará como cancelado.",
        icon: 'warning',
        input: 'textarea',
        inputPlaceholder: 'Escribe la razón...',
        showCancelButton: true,
        confirmButtonText: 'Confirmar',
        cancelButtonText: 'Regresar',
        inputValidator: (value) => {
            if (!value) return '¡Es obligatorio escribir una razón!';
        }
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                // Verifica que esta ruta sea correcta desde donde llamas al JS
                url: '../controllers/egresosController.php?action=cancelarGasto',
                method: 'POST',
                data: { id: id, razon: result.value },
                dataType: 'json',
                beforeSend: function() {
                    Swal.fire({ title: 'Procesando...', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); } });
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire('¡Anulado!', response.message, 'success').then(() => { location.reload(); });
                    } else {
                        Swal.fire('Error', response.message, 'error');
                    }
                },
                error: function(xhr) {
                    // ESTO DESBLOQUEA EL LIMBO: Si hay un error de PHP, aquí lo verás
                    console.error(xhr.responseText);
                    Swal.fire('Error Crítico', 'El servidor devolvió un error. Revisa la consola (F12).', 'error');
                }
            });
        }
    });
}
    </script>

    <?php require_once __DIR__ . '/egresosComponets/modalCompra.php'; ?>
    <?php require_once __DIR__ . '/egresosComponets/modalAjuste.php'; ?>
    <?php require_once __DIR__ . '/egresosComponets/modalDetalles.php'; ?>

</body>

</html>