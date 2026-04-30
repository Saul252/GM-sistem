<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Egresos | Sistema Almacén</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">


    <?php require_once __DIR__ . '/layout/icono.php' ?>
    <?php if (function_exists('cargarEstilos')) { cargarEstilos(); } ?>
    <style>
    :root {
        --nav-height: 65px;
        --sidebar-width: 260px;
        --primary-radius: 12px;
    }

    /* --- ESTRUCTURA BASE --- */
    .main-content {
        margin-left: var(--sidebar-width);
        margin-top: var(--nav-height);
        padding: 1.5rem 2rem;
        width: calc(100% - var(--sidebar-width));
        min-height: calc(100vh - var(--nav-height));
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        display: block;
    }

    /* --- COMPONENTES --- */
    .card-kpi {
        border: none;
        border-radius: var(--primary-radius);
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        margin-bottom: 1rem;
    }

    .table-responsive {
        border-radius: var(--primary-radius);
        background: white;
        border: 1px solid #e2e8f0;
        /* Evita que la tabla rompa el layout en móvil */
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    /* --- RESPONSIVE (MÓVIL Y TABLET) --- */
    @media (max-width: 992px) {
        .main-content {
            margin-left: 0;
            width: 100%;
            padding: 1rem 0.75rem;
            /* Menos padding en los lados para ganar espacio */
        }

        /* Ajuste de títulos para que no se corten */
        h2 {
            font-size: 1.5rem;
        }

        /* Botones de acción en móvil: se apilan si es necesario */
        .d-md-flex.gap-2 {
            flex-direction: column;
            gap: 0.5rem !important;
        }

        /* Mejora táctil para inputs y selects */
        .form-control,
        .form-select,
        .btn {
            min-height: 44px;
            /* Tamaño recomendado para dedos */
        }
    }

    /* --- GESTIÓN DE MODALES (Z-INDEX) --- */
    /* Nivel 1: Principales */
    #modalGasto,
    #modalNuevaCompra,
    #modalVerDetalle,
    #modalAjusteFaltante {
        z-index: 1060 !important;
    }

    /* Nivel 2: Secundarios (Productos, Proveedores) */
    #modalAgregarProducto,
    #modalNuevoProveedorRapido {
        z-index: 1110 !important;
    }

    /* Nivel 3: Terciarios (Categorías) */
    #modalAgregarCategoria {
        z-index: 1160 !important;
    }

    /* Backdrops forzados para modales anidados */
    .modal-backdrop:nth-of-type(1) {
        z-index: 1055 !important;
    }

    .modal-backdrop:nth-of-type(2) {
        z-index: 1105 !important;
    }

    .modal-backdrop:nth-of-type(3) {
        z-index: 1155 !important;
    }

    /* Select2: Debe estar por encima de todos los modales anteriores */
    .select2-container--open {
        z-index: 9999 !important;
    }

    /* Ajuste específico para Select2 en Móvil */
    .select2-container .select2-selection--single {
        height: 38px !important;
        display: flex;
        align-items: center;
    }
    </style>
</head>

<body class="bg-light">

    <?php renderizarLayout($tituloPagina); ?>

    <main class="main-content">
        <div class="container-fluid">

            <div class="row align-items-center mb-4">
                <div class="col-md-7">
                    <h2 class="fw-bold text-dark mb-1" style="letter-spacing: -0.5px;">Compras y Gastos</h2>
                    <p class="text-muted mb-0 small text-uppercase fw-semibold" style="letter-spacing: 0.5px;">
                        <i class="bi bi-layers-half"></i> Gestión de flujo de caja e inventario
                    </p>
                </div>
                <div class="col-md-5 text-md-end mt-3 mt-md-0">
                    <div class="d-flex gap-2 justify-content-md-end">
                        <button class="btn btn-warning fw-bold px-3 shadow-sm border-0" onclick="abrirModalGasto()"
                            style="border-radius: 10px; background: #ffc107; color: #000;">
                            <i class="bi bi-cash-stack me-1"></i> Nuevo Gasto
                        </button>

                        <button class="btn btn-primary fw-bold px-3 shadow-sm border-0" onclick="abrirModalCompra()"
                            style="border-radius: 10px; background: #0d6efd;">
                            <i class="bi bi-cart-plus me-1"></i> Nueva Compra
                        </button>
                    </div>
                </div>
            </div>

            <div class="card mb-4 shadow-sm border-0" style="border-radius: 15px;">
                <div class="card-body p-4">
                    <?php 
            $periodo_sel = $_GET['periodo_filtro'] ?? 'mes'; 
            $tipo_sel    = $_GET['tipo_filtro'] ?? 'todos';
            // Asegúrate de usar la variable correcta que viene del controller
            $categoria_gasto_id = $_GET['categoria_gasto_filtro'] ?? 0;
        ?>
                    <form id="formFiltros" method="GET" action="">
                        <div class="row g-3 align-items-end">

                            <div class="col-md-2">
                                <label class="form-label fw-bold small text-uppercase text-primary">
                                    <i class="bi bi-calendar3 me-1"></i> Periodo
                                </label>
                                <select id="filtro_rapido" name="periodo_filtro"
                                    class="form-select border-0 bg-light fw-bold" style="border-radius: 10px;">
                                    <option value="hoy" <?= ($periodo_sel == 'hoy') ? 'selected' : '' ?>>Hoy</option>
                                    <option value="ayer" <?= ($periodo_sel == 'ayer') ? 'selected' : '' ?>>Ayer</option>
                                    <option value="semana" <?= ($periodo_sel == 'semana') ? 'selected' : '' ?>>Esta
                                        Semana</option>
                                    <option value="mes" <?= ($periodo_sel == 'mes') ? 'selected' : '' ?>>Este Mes
                                    </option>
                                    <option value="personalizado"
                                        <?= ($periodo_sel == 'personalizado') ? 'selected' : '' ?>>📅 Personalizado
                                    </option>
                                </select>
                            </div>

                            <div class="col-md-2 div-fechas <?= ($periodo_sel !== 'personalizado') ? 'd-none' : '' ?>">
                                <label class="form-label fw-bold small text-uppercase text-muted">Desde</label>
                                <input type="date" name="desde" id="fecha_desde" class="form-control border-0 bg-light"
                                    style="border-radius: 10px;" value="<?= $fecha_desde ?>"
                                    <?= ($periodo_sel !== 'personalizado') ? 'disabled' : '' ?>>
                            </div>

                            <div class="col-md-2 div-fechas <?= ($periodo_sel !== 'personalizado') ? 'd-none' : '' ?>">
                                <label class="form-label fw-bold small text-uppercase text-muted">Hasta</label>
                                <input type="date" name="hasta" id="fecha_hasta" class="form-control border-0 bg-light"
                                    style="border-radius: 10px;" value="<?= $fecha_hasta ?>"
                                    <?= ($periodo_sel !== 'personalizado') ? 'disabled' : '' ?>>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label fw-bold small text-uppercase text-primary">Mostrar</label>
                                <select name="tipo_filtro" id="tipo_filtro"
                                    class="form-select fw-bold border-0 bg-light" style="border-radius: 10px;">
                                    <option value="todos" <?= ($tipo_sel == 'todos') ? 'selected' : '' ?>>📁 Todos
                                    </option>
                                    <option value="compra" <?= ($tipo_sel == 'compra') ? 'selected' : '' ?>>🛒 Compras
                                    </option>
                                    <option value="gasto" <?= ($tipo_sel == 'gasto') ? 'selected' : '' ?>>💸 Gastos
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label fw-bold small text-uppercase text-primary">Metodo de
                                    pago</label>
                                <select name="metodo_filtro" id="metodo_filtro" class="form-select">
    <option value="todos" <?= $metodo_filtro == 'todos' ? 'selected' : '' ?>>Todos</option>
    <option value="efectivo" <?= $metodo_filtro == 'efectivo' ? 'selected' : '' ?>>Efectivo</option>
    <option value="transferencia" <?= $metodo_filtro == 'transferencia' ? 'selected' : '' ?>>Transferencia</option>
    <option value="tarjeta" <?= $metodo_filtro == 'tarjeta' ? 'selected' : '' ?>>Tarjeta</option>
</select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label fw-bold small text-uppercase text-primary">Exeso de
                                    material</label>
                                <select name="deuda_filtro" id="deuda_filtro" class="form-select">
    <option value="todos" <?= $deuda_filtro == 'todos' ? 'selected' : '' ?>>Todos</option>
    <option value="1" <?= $deuda_filtro == '1' ? 'selected' : '' ?>>Con deuda</option>
    <option value="0" <?= $deuda_filtro == '0' ? 'selected' : '' ?>>Sin deuda</option>
</select>
                            </div>

                            <div class="col-md-2 d-none animate__animated animate__fadeIn" id="contenedor_categoria">
                                <label class="form-label fw-bold small text-uppercase text-muted">Categoría</label>
                                <select id="categoria_gasto_filtro" name="categoria_gasto_filtro"
                                    class="form-select border-0 bg-light" style="border-radius: 10px;">
                                    <option value="0">-- Todas --</option>
                                    <?php foreach ($listaCategoriasGastos as $cat): ?>
                                    <option value="<?= $cat['id'] ?>"
                                        <?= ($categoria_gasto_id == $cat['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($cat['nombre']) ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <?php if ($_SESSION['rol_id'] == 1): ?>
                            <div class="col-md-2">
                                <label class="form-label fw-bold small text-uppercase text-muted">Almacén</label>
                                <select id="almacen_filtro" name="almacen_filtro" class="form-select border-0 bg-light"
                                    style="border-radius: 10px;">
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

                            <div class="col-md-auto">
                                <button type="submit"
                                    class="btn btn-primary shadow-sm d-flex align-items-center justify-content-center"
                                    style="border-radius: 12px; width: 45px; height: 40px; transition: all 0.3s;">
                                    <i class="bi bi-funnel-fill" style="font-size: 1.1rem;"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="card card-kpi border-start border-primary border-4 p-2">
                        <div class="card-body py-2">
                            <p class="text-muted small fw-bold mb-1">TOTAL COMPRAS</p>
                            <h3 id="kpi_compras" class="fw-bold mb-0 text-primary">
                                $ <?= number_format($totalSumCompras, 2) ?>
                            </h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card card-kpi border-start border-warning border-4 p-2">
                        <div class="card-body py-2">
                            <p class="text-muted small fw-bold mb-1">GASTOS OPERATIVOS</p>
                            <h3 id="kpi_gastos" class="fw-bold mb-0 text-warning">
                                $ <?= number_format($totalSumGastos, 2) ?>
                            </h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card card-kpi border-start border-danger border-4 p-2">
                        <div class="card-body py-2">
                            <p class="text-danger small fw-bold mb-1">TOTAL EGRESOS</p>
                            <h3 id="kpi_total" class="fw-bold mb-0 text-dark">
                                $ <?= number_format($granTotalEgresos, 2) ?>
                            </h3>
                        </div>
                    </div>
                </div>
            </div>
           <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead style="background-color: #f8f9fa; border-bottom: 2px solid #f1f3f5;">
                <tr class="text-secondary">
                    <th class="ps-4 py-3 fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">ID</th>
                    <th class="py-3 fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">Almacén</th>
                    <th class="py-3 fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">Fecha</th>
                    <th class="py-3 fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">Folio</th>
                    <th class="py-3 fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">Tipo</th>
                    <th class="py-3 fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">Entidad</th>
                    <th class="py-3 fw-bold text-uppercase text-center" style="font-size: 0.75rem; letter-spacing: 0.5px;">Deuda</th>
                    <th class="py-3 fw-bold text-uppercase text-end" style="font-size: 0.75rem; letter-spacing: 0.5px;">Total</th>
                    <th class="py-3 fw-bold text-uppercase text-end" style="font-size: 0.75rem; letter-spacing: 0.5px;">Método</th>
                    <th class="py-3 fw-bold text-uppercase text-center" style="font-size: 0.75rem; letter-spacing: 0.5px;">Estado</th>
                    <th class="py-3 fw-bold text-uppercase text-center" style="font-size: 0.75rem; letter-spacing: 0.5px;">Doc</th>
                    <th class="py-3 fw-bold text-uppercase text-end pe-4" style="font-size: 0.75rem; letter-spacing: 0.5px;">Acciones</th>
                </tr>
            </thead>

            <tbody class="border-top-0">
                <?php if(!empty($egresos)): ?>
                <?php foreach($egresos as $e): ?>
                <tr class="border-bottom" style="transition: all 0.2s ease;">

                    <td class="ps-4">
                        <span class="badge bg-light text-dark border fw-medium">#<?= $e['id'] ?></span>
                    </td>

                    <td>
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle bg-danger bg-opacity-10 d-flex align-items-center justify-content-center me-2" style="width: 24px; height: 24px;">
                                <i class="bi bi-geo-alt text-danger" style="font-size: 0.75rem;"></i>
                            </div>
                            <span class="fw-medium text-dark small"><?= htmlspecialchars($e['almacen_nombre'] ?? 'N/A') ?></span>
                        </div>
                    </td>

                    <td class="text-secondary small">
                        <?= date('d/m/Y', strtotime($e['fecha'])) ?>
                    </td>

                    <td>
                        <span class="fw-bold text-dark" style="letter-spacing: -0.3px;">
                            <?= ($e['tipo'] == 'compra' ? 'FC-' : ($e['tipo'] == 'gasto' ? 'FG-' : 'PD-')) . $e['folio'] ?>
                        </span>
                    </td>

                    <td>
                        <?php 
                            $bg_tipo = match($e['tipo']) {
                                'compra' => 'bg-info bg-opacity-10 text-info border-info',
                                'gasto' => 'bg-warning bg-opacity-10 text-warning-emphasis border-warning',
                                'pago_deuda' => 'bg-purple bg-opacity-10 text-purple border-purple', // Requiere CSS para purple o usar primary
                                default => 'bg-secondary bg-opacity-10 text-secondary'
                            };
                            // Fallback para pago_deuda si no tienes purple en tu CSS
                            if($e['tipo'] == 'pago_deuda') $bg_tipo = 'bg-primary bg-opacity-10 text-primary border-primary';
                        ?>
                        <span class="badge border py-1.5 px-2 fw-semibold text-uppercase <?= $bg_tipo ?>" style="font-size: 0.65rem;">
                            <?= str_replace('_', ' ', strtoupper($e['tipo'])) ?>
                        </span>
                    </td>

                    <td>
                        <div class="text-dark fw-medium small text-truncate" style="max-width: 150px;">
                            <?= htmlspecialchars($e['entidad']) ?>
                        </div>
                    </td>

                    <td class="text-center">
                        <?php if(($e['tiene_deuda'] ?? 0) == 1): ?>
                            <span class="badge bg-danger rounded-circle p-1" title="Pendiente de pago">
                                <i class="bi bi-clock-history"></i>
                            </span>
                        <?php else: ?>
                            <i class="bi bi-dash text-muted"></i>
                        <?php endif; ?>
                    </td>

                    <td class="fw-bold text-end text-dark">
                        $<?= number_format($e['total'], 2) ?>
                    </td>

                    <?php
                        $metodo = strtoupper($e['metodo_pago'] ?? 'EFECTIVO');
                        $dot_color = 'text-secondary';
                        if (str_contains($metodo, 'EFECT')) $dot_color = 'text-dark';
                        elseif (str_contains($metodo, 'TARJ')) $dot_color = 'text-primary';
                        elseif (str_contains($metodo, 'TRANS')) $dot_color = 'text-warning';
                    ?>
                    <td class="text-end">
                        <span class="small fw-semibold text-secondary">
                            <i class="bi bi-circle-fill me-1 <?= $dot_color ?>" style="font-size: 0.5rem;"></i>
                            <?= $metodo ?>
                        </span>
                    </td>

                    <td class="text-center">
                        <?php if($e['tipo'] == 'compra'): ?>
                            <?php if(($e['piezas_faltantes'] ?? 0) > 0): ?>
                                <span class="badge bg-white text-danger border border-danger fw-bold shadow-sm" style="font-size: 0.7rem;">
                                    - <?= number_format($e['piezas_faltantes'], 2) ?>
                                </span>
                            <?php else: ?>
                                <span class="badge bg-success bg-opacity-10 text-success border border-success rounded-circle">
                                    <i class="bi bi-check"></i>
                                </span>
                            <?php endif; ?>
                        <?php else: ?>
                            <span class="text-muted opacity-50 small">N/A</span>
                        <?php endif; ?>
                    </td>

                    <td class="text-center">
                        <?php if(!empty($e['documento_url'])): ?>
                            <a href="../../<?= ($e['tipo']=='gasto'?'uploads/evidencias/':'') . $e['documento_url'] ?>"
                                target="_blank" class="btn btn-sm btn-outline-primary border-0 rounded-circle">
                                <i class="bi bi-file-earmark-text-fill"></i>
                            </a>
                        <?php else: ?>
                            <i class="bi bi-slash-circle text-muted opacity-25"></i>
                        <?php endif; ?>
                        <?php if(!empty($e['tipo']=='compra')): ?>
                        <button class="btn btn-sm btn-outline-primary rounded-pill"
    onclick="subirDocumentoCompra(
        <?= $e['id'] ?>, 
        '<?= $e['folio'] ?? ''?>', 
        '<?= $e['documento_url'] ?? ''?>'
    )">
    <i class="bi bi-upload"></i>
</button>
<?php endif; ?>
                    </td>

                    <td class="text-end pe-4">
                        <div class="d-flex justify-content-end gap-1">
                              <?php if ($e['tipo'] == 'compra' && ($e['piezas_faltantes'] ?? 0) > 0): ?>
                            <button class="btn btn-sm btn-outline-danger py-0 px-2" 
                                    onclick="abrirModalAjuste(<?= $e['id'] ?>, '<?= $e['folio'] ?>')">
                                <i class="bi bi-wrench-adjustable"></i>
                            </button>
                        <?php endif; ?>
                         <?php if ($e['tipo'] == 'pago_deuda'): ?>
                           
                                    <button class="btn btn-sm btn-dark" onclick="abrirDetallePago(<?=$e['id']  ?>)">
    <i class="bi bi-eye"></i>
</button>
                            </button>
                        <?php endif; ?>
                       

                            <?php if($e['tiene_deuda'] == 1): ?>
                                <button class="btn btn-sm btn-danger shadow-sm px-2" onclick="abrirDeudaCompra(<?= $e['id'] ?>)" title="Pagar Deuda">
                                    <i class="bi bi-wallet2"></i>
                                </button>
                            <?php endif; ?>

                            <?php if(($e['pagado_cpp'] ?? 0) == 1): ?>
                                <button class="btn btn-sm btn-success shadow-sm px-2" disabled>
                                    <i class="bi bi-patch-check-fill"></i>
                                </button>
                            <?php endif; ?>
                            <?php if ($e['tipo'] != 'pago_deuda'): ?>
                              <button class="btn btn-sm btn-light border shadow-sm px-2 text-primary" onclick="verDetalle('<?= $e['tipo'] ?>', <?= $e['id'] ?>)">
                                <i class="bi bi-eye-fill"></i>
                            </button>

                            <button class="btn btn-sm btn-light border shadow-sm px-2 text-danger" 
                                onclick="<?= ($e['tipo']=='compra') ? "confirmarCancelacionCompra" : "confirmarCancelacionGasto" ?>('<?= $e['id'] ?>','<?= $e['folio'] ?>')">
                                <i class="bi bi-trash3"></i>
                            </button>
                        <?php endif; ?>

                          
                        </div>
                    </td>

                </tr>
                <?php endforeach; ?>
                <?php else: ?>
                <tr>
                    <td colspan="12" class="text-center py-5 text-muted">
                        <i class="bi bi-inbox h1 d-block opacity-25"></i>
                        No se encontraron movimientos registrados.
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
        </div>
    </main>



    <?php 
$ruta = __DIR__ . '/egresosComponets/modalCompra.php';
if (!file_exists($ruta)) {
    echo "<script>console.error('ERROR: El archivo del modal no existe en: $ruta');</script>";
}
require_once $ruta;
?>


    <?php require_once __DIR__ . '/egresosComponets/modalCompra.php'; ?>
    <?php require_once __DIR__ . '/egresosComponets/modalAjuste.php'; ?>
    <?php require_once __DIR__ . '/egresosComponets/modalDetalles.php'; ?>
    <?php require_once __DIR__ . '/egresosComponets/modalGasto.php'; ?>
    <?php require_once __DIR__ . '/egresosComponets/cuentasPendientes.php'; ?>
    <?php require_once __DIR__ . '/egresosComponets/historialCuentasPorPagar.php'; ?>
 <?php require_once __DIR__ . '/egresosComponets/modalDetallePago.php'; ?>





    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
    // Forzamos que sea global con window.
    window.DATA_COMPRAS = {
        productos: <?php echo json_encode($productos); ?>,
        almacenes: <?php echo json_encode($almacenes); ?>
    };
    // Imprime esto en la consola para que verifiques si hay datos
    console.log("Productos cargados:", window.DATA_COMPRAS.productos);
    </script>
<script>
/**
 * SISTEMA DE FILTROS Y UI
 * Gestiona el envío automático, visibilidad de fechas y categorías.
 */
(function () {
    document.addEventListener('DOMContentLoaded', function () {
        const f = {
            form: document.getElementById('formFiltros'),
            periodo: document.getElementById('filtro_rapido'),
            desde: document.getElementById('fecha_desde'),
            hasta: document.getElementById('fecha_hasta'),
            almacen: document.getElementById('almacen_filtro'),
            tipo: document.getElementById('tipo_filtro'),
            categoria: document.getElementById('categoria_gasto_filtro'),
            deuda: document.getElementById('deuda_filtro'),
            metodo: document.getElementById('metodo_filtro'),
            cont_cat: document.getElementById('contenedor_categoria')
        };

        const enviar = () => {
            if (!f.form) return;
            // Aseguramos que las fechas se envíen (PHP no recibe campos disabled)
            if (f.desde) f.desde.disabled = false;
            if (f.hasta) f.hasta.disabled = false;
            f.form.submit();
        };

        // --- 1. Lógica de Categorías (Mostrar/Ocultar) ---
        const toggleCategoria = () => {
            if (!f.tipo || !f.cont_cat) return;
            if (f.tipo.value === 'gasto') {
                f.cont_cat.classList.remove('d-none');
            } else {
                f.cont_cat.classList.add('d-none');
                if (f.categoria) f.categoria.value = "0"; // Reset si no es gasto
            }
        };

        // --- 2. Lógica de Periodos (Rápido vs Personalizado) ---
        if (f.periodo) {
            f.periodo.addEventListener('change', function() {
                const esPerso = this.value === 'personalizado';
                const divs = document.querySelectorAll('.div-fechas');
                const inputs = document.querySelectorAll('.div-fechas input');

                divs.forEach(div => esPerso ? div.classList.remove('d-none') : div.classList.add('d-none'));
                inputs.forEach(i => i.disabled = !esPerso);

                if (!esPerso) enviar();
            });
        }

        // --- 3. Eventos de Cambio Directo ---
        // Almacén, Deuda, Método: Envían al cambiar
        [f.almacen, f.deuda, f.metodo, f.categoria].forEach(el => {
            if (el) el.addEventListener('change', enviar);
        });

        // Tipo: Cambia visibilidad de categoría y envía
        if (f.tipo) {
            f.tipo.addEventListener('change', () => {
                toggleCategoria();
                enviar();
            });
        }

        // --- 4. Fechas Manuales ---
        [f.desde, f.hasta].forEach(el => {
            if (!el) return;
            el.addEventListener('change', () => {
                if (f.periodo) f.periodo.value = 'personalizado';
                if (f.desde.value && f.hasta.value) enviar();
            });
        });

        // Ejecución inicial para restaurar estado tras recarga
        toggleCategoria();
    });
})();

/**
 * PARCHE PARA MODALES
 * Corrige el scroll y el backdrop en modales anidados o cierres rápidos.
 */
(function() {
    $(document).on('hidden.bs.modal', '.modal', function() {
        if ($('.modal.show').length === 0) {
            $('.modal-backdrop').remove();
            $('body').removeClass('modal-open').css('padding-right', '');
        } else {
            $('body').addClass('modal-open');
        }
    });
})();

/**
 * ACCIONES: CANCELACIONES (AJAX + SWEETALERT2)
 */
function confirmarCancelacionCompra(id, folio) {
    Swal.fire({
        title: `¿Anular Compra ${folio}?`,
        text: "Se restará el stock y se eliminarán los lotes. Acción irreversible.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        confirmButtonText: 'Sí, anular',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({ title: 'Procesando...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });

            $.ajax({
                url: '../controllers/egresosController.php?action=cancelarCompra',
                type: 'POST',
                data: { id: id },
                dataType: 'json',
                success: function(res) {
                    if (res.success) {
                        Swal.fire('¡Anulada!', res.message, 'success').then(() => location.reload());
                    } else {
                        Swal.fire('Atención', res.message, 'error');
                    }
                },
                error: function(xhr) {
                    console.error(xhr.responseText);
                    Swal.fire('Error', 'No se pudo procesar la cancelación.', 'error');
                }
            });
        }
    });
}

function confirmarCancelacionGasto(id, folio) {
    Swal.fire({
        title: `¿Anular Gasto: ${folio}?`,
        text: "Por favor, escribe la razón de la cancelación:",
        icon: 'warning',
        input: 'textarea',
        inputPlaceholder: 'Escribe aquí la razón...',
        showCancelButton: true,
        confirmButtonText: 'Confirmar',
        cancelButtonText: 'Regresar',
        inputValidator: (value) => { if (!value) return '¡La razón es obligatoria!'; }
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({ title: 'Procesando...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });

            $.ajax({
                url: '../controllers/egresosController.php?action=cancelarGasto',
                method: 'POST',
                data: { id: id, razon: result.value },
                dataType: 'json',
                success: function(res) {
                    if (res.success) {
                        Swal.fire('¡Anulado!', res.message, 'success').then(() => location.reload());
                    } else {
                        Swal.fire('Error', res.message, 'error');
                    }
                },
                error: function(xhr) {
                    console.error(xhr.responseText);
                    Swal.fire('Error Crítico', 'Consulta la consola (F12).', 'error');
                }
            });
        }
    });
}
function subirDocumentoCompra(compra_id, folio, documento_actual = '') {

    Swal.fire({
        title: 'Documento de Compra',
        html: `
            <div class="text-start">
                <label class="fw-bold small mb-2">Subir / Reemplazar documento</label>
                <input type="file" id="swal_file_doc" class="form-control mb-2" accept=".pdf,image/*">
                
                ${documento_actual 
                    ? `<a href="/cfsistem/${documento_actual}" target="_blank" class="small text-primary">Ver documento actual</a>` 
                    : `<div class="small text-muted">Sin documento</div>`
                }
            </div>
        `,
        icon: 'info',
        showCancelButton: true,
        confirmButtonText: 'Guardar',
        confirmButtonColor: '#198754',
        focusConfirm: false,

        preConfirm: async () => {

            const fileInput = document.getElementById('swal_file_doc');
            const file = fileInput?.files[0];

            if (!file) {
                Swal.showValidationMessage('Selecciona un archivo');
                return false;
            }

            const formData = new FormData();
            formData.append('action', 'subirDocumento');
            formData.append('compra_id', compra_id);
            formData.append('folio', folio);
            formData.append('documento', file);

            try {
                const response = await fetch('/cfsistem/app/controllers/egresosController.php?action=subirDocumento', {
                    method: 'POST',
                    body: formData
                });

                // 🔥 LEEMOS COMO TEXTO PRIMERO (ANTI "Unexpected token <")
                const text = await response.text();
                console.log('RESPUESTA CRUDA:', text);

                let res;
                try {
                    res = JSON.parse(text);
                } catch (e) {
                    throw new Error('El servidor no devolvió JSON válido');
                }

                if (!res.success) {
                    throw new Error(res.message || 'Error al subir archivo');
                }

                return res;

            } catch (err) {
                Swal.showValidationMessage(err.message);
                return false;
            }
        }

    }).then(result => {

        if (!result.isConfirmed || !result.value) return;

        Swal.fire({
            icon: 'success',
            title: 'Guardado',
            text: 'Documento actualizado correctamente',
            timer: 1800,
            showConfirmButton: false
        });

        if (typeof cargarCompras === 'function') {
            cargarCompras();
        }
    });
}</script>

</body>

</html>