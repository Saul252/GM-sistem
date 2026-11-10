<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Egresos | Sistema Almacén</title>
    <link rel="icon" type="image/png" href="/cfsistem/public/assets/logo.png">
    <link rel="shortcut icon" href="/cfsistem/public/assets/logo.ico" type="image/x-icon">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />

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
            padding: 1.5rem;
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

        /* ✅ FIX: table-responsive siempre al 100% */
        .table-responsive {
            width: 100%;
            max-width: 100%;
            border-radius: var(--primary-radius);
            border: 1px solid #e2e8f0;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        /* ✅ FIX: min-width en la tabla, nowrap solo en celdas */
        #egresosTabla {
            min-width: 900px;
        }
        #egresosTabla th,
        #egresosTabla td {
            white-space: nowrap;
        }
        #egresosTabla td:nth-child(6) {
            white-space: normal;
            max-width: 150px;
        }

        /* ✅ FIX: KPI responsive */
        .card-kpi h3 {
            font-size: 1.25rem;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        /* ✅ FIX: estilos de la barra de acciones movidos al head */
        .action-bar {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05);
            transition: background-color 0.3s ease, border-color 0.3s ease;
        }

        .btn-action-slate {
            background: #f8fafc;
            border: 1px solid #cbd5e1;
            color: #475569;
            font-weight: 500;
            border-radius: 8px;
            transition: all 0.2s ease;
        }
        .btn-action-slate:hover {
            background: #f1f5f9;
            color: #0f172a;
            border-color: #94a3b8;
        }

        .btn-action-warning {
            background: #fffbebf5;
            border: 1px solid #fde68a;
            color: #b45309;
            font-weight: 600;
        }
        .btn-action-warning:hover {
            background: #fef3c7;
            border-color: #fcd34d;
            color: #92400e;
        }

        .btn-action-primary {
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            color: #1d4ed8;
            font-weight: 600;
        }
        .btn-action-primary:hover {
            background: #dbeafe;
            border-color: #93c5fd;
            color: #1e40af;
        }

        [data-bs-theme="dark"] .action-bar,
        .dark .action-bar {
            background: #0f172a;
            border-color: #1e293b;
            box-shadow: none;
        }
        [data-bs-theme="dark"] .btn-action-slate,
        .dark .btn-action-slate {
            background: #1e293b;
            border-color: #334155;
            color: #94a3b8;
        }
        [data-bs-theme="dark"] .btn-action-slate:hover,
        .dark .btn-action-slate:hover {
            background: #334155;
            color: #f8fafc;
            border-color: #475569;
        }
        [data-bs-theme="dark"] .btn-action-warning,
        .dark .btn-action-warning {
            background: rgba(245, 158, 11, 0.1);
            border-color: rgba(245, 158, 11, 0.3);
            color: #fbbf24;
        }
        [data-bs-theme="dark"] .btn-action-primary,
        .dark .btn-action-primary {
            background: rgba(59, 130, 246, 0.1);
            border-color: rgba(59, 130, 246, 0.3);
            color: #60a5fa;
        }

        /* --- RESPONSIVE (MÓVIL Y TABLET) --- */
        @media (max-width: 992px) {
            .main-content {
                margin-left: 0;
                width: 100%;
                padding: 1rem 0.5rem;
            }

            h2 {
                font-size: 1.35rem;
            }

            /* ✅ FIX: barra de acciones se apila */
            .action-bar {
                flex-direction: column;
                align-items: stretch !important;
                gap: 0.5rem !important;
            }

            .action-bar > div {
                width: 100%;
                justify-content: stretch !important;
            }

            .action-bar .btn {
                flex: 1 1 auto;
                font-size: 0.8rem;
                padding: 0.45rem 0.6rem;
            }

            /* ✅ FIX: card con tabla, scroll interno */
            .card > .table-responsive {
                width: 100%;
                max-width: 100%;
                overflow-x: auto;
            }

            /* ✅ FIX: inputs táctiles */
            .form-control,
            .form-select,
            .btn {
                min-height: 44px;
            }

            /* ✅ FIX: KPI más pequeño en móvil */
            .card-kpi h3 {
                font-size: 1rem;
            }
        }

        /* --- GESTIÓN DE MODALES (Z-INDEX) --- */
        #modalGasto,
        #modalNuevaCompra,
        #compraDetalle_seccionImpresion,
        #compraDetalle_modalPrincipal,
        #gastoDetalle_seccionImpresion,
        #gastoDetalle_modalPrincipal,
        #modalAjusteFaltante {
            z-index: 1060 !important;
        }

        #modalAgregarProducto,
        #modalNuevoProveedorRapido,
        #modalConfirmarExcedente {
            z-index: 1110 !important;
        }

        #modalAgregarCategoria,
        #modalNuevaCategoriaGasto {
            z-index: 1160 !important;
        }

        .modal-backdrop:nth-of-type(1) { z-index: 1055 !important; }
        .modal-backdrop:nth-of-type(2) { z-index: 1105 !important; }
        .modal-backdrop:nth-of-type(3) { z-index: 1155 !important; }

        .select2-container--open {
            z-index: 9999 !important;
        }

        .select2-container .select2-selection--single {
            height: 38px !important;
            display: flex;
            align-items: center;
        }
    </style>
</head>

<body class="">

    <?php renderizarLayout($tituloPagina); ?>

    <main class="main-content">
        <div class="container-fluid">

            <div class="row align-items-center mb-4 g-3">
                <div class="col-12 col-lg-7">
                    <h2 class="fw-bold card-title-text mb-1" style="letter-spacing: -0.5px;">Compras y Gastos</h2>
                    <p class="text-body-secondary mb-0 small text-uppercase fw-semibold" style="letter-spacing: 0.5px;">
                        <i class="bi bi-layers-half"></i> Gestión de flujo de caja e inventario
                    </p>
                </div>
                <div class="col-12 col-lg-5">
                    <div class="d-flex flex-wrap gap-2 justify-content-lg-end justify-content-start">

                        <div class="dropdown">
                            <button class="btn btn-add dropdown-toggle" type="button" data-bs-toggle="dropdown"
                                aria-expanded="false"
                                style="border-radius: 10px; background: #127717; color: #ffffff;">
                                <i class="bi bi-gear me-2"></i> Solicitudes de compra
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3">
                                <li>
                                    <a class="dropdown-item d-flex align-items-center gap-2" href="#"
                                        onclick="nuevaSolicitud()">
                                        <i class="bi bi-plus-lg text-success"></i> Crear Solicitud
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item d-flex align-items-center gap-2"
                                        href="/cfsistem/app/controllers/solicitudesCompraController.php">
                                        <i class="bi bi-list-ul text-primary"></i> Gestionar Solicitudes
                                    </a>
                                </li>
                            </ul>
                        </div>

                        <div class="d-flex align-items-center justify-content-between gap-3 p-2 action-bar">
                            <div class="d-flex align-items-center gap-2">
                                <button type="button"
                                    class="btn btn-sm btn-action-slate px-3 py-2 d-inline-flex align-items-center gap-2"
                                    onclick="imprimirTablaPDF('egresosTabla')">
                                    <i class="bi bi-printer-fill text-danger fs-6"></i>
                                    <span>PDF</span>
                                </button>
                                <button type="button"
                                    class="btn btn-sm btn-action-slate px-3 py-2 d-inline-flex align-items-center gap-2"
                                    onclick="exportarTablaCSV('egresosTabla','')">
                                    <i class="bi bi-filetype-csv text-success fs-6"></i>
                                    <span>CSV</span>
                                </button>
                            </div>

                            <div class="d-flex align-items-center gap-2">
                                <button type="button"
                                    class="btn btn-sm btn-action-warning px-3 py-2 d-inline-flex align-items-center gap-2"
                                    onclick="abrirModalGasto()">
                                    <i class="bi bi-cash-stack fs-6"></i>
                                    <span>Nuevo Gasto</span>
                                </button>
                                <button type="button"
                                    class="btn btn-sm btn-action-primary px-3 py-2 d-inline-flex align-items-center gap-2"
                                    onclick="abrirModalCompra()">
                                    <i class="bi bi-cart-plus-fill fs-6"></i>
                                    <span>Nueva Compra</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-4 shadow-sm border-0" style="border-radius: 15px;">
                <div class="card-body p-3 p-md-4">
                    <?php
                    $periodo_sel = $_GET['periodo_filtro'] ?? 'mes';
                    $tipo_sel = $_GET['tipo_filtro'] ?? 'todos';
                    $categoria_gasto_id = $_GET['categoria_gasto_filtro'] ?? 0;
                    ?>
                    <form id="formFiltros" method="GET" action="">
                        <div class="row g-3 align-items-end">

                            <div class="col-6 col-md-2">
                                <label class="form-label fw-bold small text-uppercase text-primary">
                                    <i class="bi bi-calendar3 me-1"></i> Periodo
                                </label>
                                <select id="filtro_rapido" name="periodo_filtro"
                                    class="form-select border border-subtle fw-bold" style="border-radius: 10px;">
                                    <option value="hoy" <?= ($periodo_sel == 'hoy') ? 'selected' : '' ?>>Hoy</option>
                                    <option value="ayer" <?= ($periodo_sel == 'ayer') ? 'selected' : '' ?>>Ayer</option>
                                    <option value="semana" <?= ($periodo_sel == 'semana') ? 'selected' : '' ?>>Esta Semana</option>
                                    <option value="mes" <?= ($periodo_sel == 'mes') ? 'selected' : '' ?>>Este Mes</option>
                                    <option value="personalizado" <?= ($periodo_sel == 'personalizado') ? 'selected' : '' ?>>📅 Personalizado</option>
                                </select>
                            </div>

                            <div class="col-6 col-md-2 div-fechas <?= ($periodo_sel !== 'personalizado') ? 'd-none' : '' ?>">
                                <label class="form-label fw-bold small text-uppercase text-body-secondary">Desde</label>
                                <input type="date" name="desde" id="fecha_desde"
                                    class="form-control border border-subtle" style="border-radius: 10px;"
                                    value="<?= $fecha_desde ?>" <?= ($periodo_sel !== 'personalizado') ? 'disabled' : '' ?>>
                            </div>

                            <div class="col-6 col-md-2 div-fechas <?= ($periodo_sel !== 'personalizado') ? 'd-none' : '' ?>">
                                <label class="form-label fw-bold small text-uppercase text-body-secondary">Hasta</label>
                                <input type="date" name="hasta" id="fecha_hasta"
                                    class="form-control border border-subtle" style="border-radius: 10px;"
                                    value="<?= $fecha_hasta ?>" <?= ($periodo_sel !== 'personalizado') ? 'disabled' : '' ?>>
                            </div>

                            <div class="col-6 col-md-2">
                                <label class="form-label fw-bold small text-uppercase text-primary">Mostrar</label>
                                <select name="tipo_filtro" id="tipo_filtro"
                                    class="form-select fw-bold border border-subtle" style="border-radius: 10px;">
                                    <option value="todos" <?= ($tipo_sel == 'todos') ? 'selected' : '' ?>>📁 Todos</option>
                                    <option value="compra" <?= ($tipo_sel == 'compra') ? 'selected' : '' ?>>🛒 Compras</option>
                                    <option value="gasto" <?= ($tipo_sel == 'gasto') ? 'selected' : '' ?>>💸 Gastos</option>
                                </select>
                            </div>

                            <div class="col-6 col-md-2">
                                <label class="form-label fw-bold small text-uppercase text-primary">Método de pago</label>
                                <select name="metodo_filtro" id="metodo_filtro" class="form-select">
                                    <option value="todos" <?= $metodo_filtro == 'todos' ? 'selected' : '' ?>>Todos</option>
                                    <option value="efectivo" <?= $metodo_filtro == 'efectivo' ? 'selected' : '' ?>>Efectivo</option>
                                    <option value="transferencia" <?= $metodo_filtro == 'transferencia' ? 'selected' : '' ?>>Transferencia</option>
                                    <option value="tarjeta" <?= $metodo_filtro == 'tarjeta' ? 'selected' : '' ?>>Tarjeta</option>
                                </select>
                            </div>

                            <div class="col-6 col-md-2">
                                <label class="form-label fw-bold small text-uppercase text-primary">Exceso de material</label>
                                <select name="deuda_filtro" id="deuda_filtro" class="form-select">
                                    <option value="todos" <?= $deuda_filtro == 'todos' ? 'selected' : '' ?>>Todos</option>
                                    <option value="1" <?= $deuda_filtro == '1' ? 'selected' : '' ?>>Con deuda</option>
                                    <option value="0" <?= $deuda_filtro == '0' ? 'selected' : '' ?>>Sin deuda</option>
                                </select>
                            </div>

                            <div class="col-6 col-md-2 d-none" id="contenedor_categoria">
                                <label class="form-label fw-bold small text-uppercase text-body-secondary">Categoría</label>
                                <select id="categoria_gasto_filtro" name="categoria_gasto_filtro"
                                    class="form-select border border-subtle" style="border-radius: 10px;">
                                    <option value="0">-- Todas --</option>
                                    <?php foreach ($listaCategoriasGastos as $cat): ?>
                                        <option value="<?= $cat['id'] ?>" <?= ($categoria_gasto_id == $cat['id']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($cat['nombre']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <?php if ($_SESSION['rol_id'] == 1 || $_SESSION['rol_id'] == 3): ?>
                                <div class="col-6 col-md-2">
                                    <label class="form-label fw-bold small text-uppercase text-body-secondary">Almacén</label>
                                    <select id="almacen_filtro" name="almacen_filtro"
                                        class="form-select border border-subtle" style="border-radius: 10px;">
                                        <option value="0">🌐 Todos</option>
                                        <?php foreach ($almacenes as $alm): ?>
                                            <option value="<?= $alm['id'] ?>" <?= (isset($_GET['almacen_filtro']) && $_GET['almacen_filtro'] == $alm['id']) ? 'selected' : '' ?>>
                                                📍 <?= $alm['nombre'] ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            <?php endif; ?>

                            <div class="col-6 col-md-auto">
                                <button type="submit"
                                    class="btn btn-primary shadow-sm d-flex align-items-center justify-content-center w-100"
                                    style="border-radius: 12px; height: 40px; transition: all 0.3s;">
                                    <i class="bi bi-funnel-fill" style="font-size: 1.1rem;"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-6 col-md-4">
                    <div class="card card-kpi border-start border-primary border-4 p-2">
                        <div class="card-body py-2">
                            <p class="text-body-secondary small fw-bold mb-1">TOTAL COMPRAS</p>
                            <h3 id="kpi_compras" class="fw-bold mb-0 text-primary text-truncate">
                                $ <?= number_format($totalSumCompras, 2) ?>
                            </h3>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-4">
                    <div class="card card-kpi border-start border-warning border-4 p-2">
                        <div class="card-body py-2">
                            <p class="text-body-secondary small fw-bold mb-1">GASTOS OPERATIVOS</p>
                            <h3 id="kpi_gastos" class="fw-bold mb-0 text-warning text-truncate">
                                $ <?= number_format($totalSumGastos, 2) ?>
                            </h3>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="card card-kpi border-start border-danger border-4 p-2">
                        <div class="card-body py-2">
                            <p class="text-danger small fw-bold mb-1">TOTAL EGRESOS</p>
                            <h3 id="kpi_total" class="fw-bold mb-0 card-title-text text-truncate">
                                $ <?= number_format($granTotalEgresos, 2) ?>
                            </h3>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="egresosTabla">
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
                            <?php if (!empty($egresos)): ?>
                                <?php foreach ($egresos as $e): ?>
                                    <tr class="border-bottom" style="transition: all 0.2s ease;">
                                        <td class="ps-4">
                                            <span class="border border-subtle card-title-text border fw-medium">#<?= $e['id'] ?></span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="rounded-circle bg-danger bg-opacity-10 d-flex align-items-center justify-content-center me-2"
                                                    style="width: 24px; height: 24px;">
                                                    <i class="bi bi-geo-alt text-danger" style="font-size: 0.75rem;"></i>
                                                </div>
                                                <span class="fw-medium card-title-text small"><?= htmlspecialchars($e['almacen_nombre'] ?? 'N/A') ?></span>
                                            </div>
                                        </td>
                                        <td class="text-secondary small">
                                            <?= date('d/m/Y', strtotime($e['fecha'])) ?>
                                        </td>
                                        <td>
                                            <span class="fw-bold card-title-text" style="letter-spacing: -0.3px;">
                                                <?= ($e['tipo'] == 'compra' ? 'FC-' : ($e['tipo'] == 'gasto' ? 'FG-' : 'PD-')) . $e['folio'] ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php
                                            $bg_tipo = match ($e['tipo']) {
                                                'compra' => 'bg-info bg-opacity-10 text-info border-info',
                                                'gasto' => 'bg-warning bg-opacity-10 text-warning-emphasis border-warning',
                                                'pago_deuda' => 'bg-purple bg-opacity-10 text-purple border-purple',
                                                default => 'bg-secondary bg-opacity-10 text-secondary'
                                            };
                                            if ($e['tipo'] == 'pago_deuda')
                                                $bg_tipo = 'bg-primary bg-opacity-10 text-primary border-primary';
                                            ?>
                                            <span class="border py-1.5 px-2 fw-semibold text-uppercase <?= $bg_tipo ?>"
                                                style="font-size: 0.65rem;">
                                                <?= str_replace('_', ' ', strtoupper($e['tipo'])) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="card-title-text fw-medium small text-truncate" style="max-width: 150px;">
                                                <?= htmlspecialchars($e['entidad']) ?>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <?php if (($e['tiene_deuda'] ?? 0) == 1): ?>
                                                <span class="bg-danger rounded-circle p-1" title="Pendiente de pago">
                                                    <i class="bi bi-clock-history"></i>
                                                </span>
                                            <?php else: ?>
                                                <i class="bi bi-dash text-body-secondary"></i>
                                            <?php endif; ?>
                                        </td>
                                        <td class="fw-bold text-end card-title-text">
                                            $<?= number_format($e['total'], 2) ?>
                                        </td>
                                        <?php
                                        $metodo = strtoupper($e['metodo_pago'] ?? 'EFECTIVO');
                                        $dot_color = 'text-secondary';
                                        if (str_contains($metodo, 'EFECT'))
                                            $dot_color = 'card-title-text';
                                        elseif (str_contains($metodo, 'TARJ'))
                                            $dot_color = 'text-primary';
                                        elseif (str_contains($metodo, 'TRANS'))
                                            $dot_color = 'text-warning';
                                        ?>
                                        <td class="text-end">
                                            <span class="small fw-semibold text-secondary">
                                                <i class="bi bi-circle-fill me-1 <?= $dot_color ?>" style="font-size: 0.5rem;"></i>
                                                <?= $metodo ?>
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <?php if ($e['tipo'] == 'compra'): ?>
                                                <?php if (($e['piezas_faltantes'] ?? 0) > 0): ?>
                                                    <span class="bg-white text-danger border border-danger fw-bold shadow-sm" style="font-size: 0.7rem;">
                                                        - <?= number_format($e['piezas_faltantes'], 2) ?>
                                                    </span>
                                                <?php else: ?>
                                                    <span class="bg-success bg-opacity-10 text-success border border-success rounded-circle">
                                                        <i class="bi bi-check"></i>
                                                    </span>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <span class="text-body-secondary opacity-50 small">N/A</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center align-items-center gap-1">
                                                <?php if (!empty($e['documento_url'])): ?>
                                                    <?php $documentos = explode(';;;', $e['documento_url']); ?>
                                                    <div class="dropdown">
                                                        <button class="btn btn-sm btn-light border position-relative" type="button" data-bs-toggle="dropdown">
                                                            <i class="bi bi-folder2-open text-success"></i>
                                                            <span class="position-absolute top-0 start-100 translate-middle rounded-pill bg-primary">
                                                                <?= count($documentos) ?>
                                                            </span>
                                                        </button>
                                                        <ul class="dropdown-menu dropdown-menu-end shadow border-0" style="min-width:320px;">
                                                            <li>
                                                                <h6 class="dropdown-header">
                                                                    <i class="bi bi-files me-1"></i> Documentos adjuntos
                                                                    <button class="btn btn-sm btn-outline-primary rounded-pill"
                                                                        onclick="subirDocumentoCompra(<?= $e['id'] ?>, '<?= $e['folio'] ?? '' ?>', '<?= $e['documento_url'] ?? '' ?>', '<?= $e['tipo'] ?? '' ?>')">
                                                                        Agregar Nuevo <i class="bi bi-upload"></i>
                                                                    </button>
                                                                </h6>
                                                            </li>
                                                            <?php foreach ($documentos as $doc): ?>
                                                                <?php
                                                                $partes = explode('|||', $doc);
                                                                $nombre = $partes[0] ?? '';
                                                                $direccion = $partes[1] ?? '';
                                                                $idDoc = $partes[2] ?? 0;
                                                                if (empty($direccion)) continue;
                                                                ?>
                                                                <li>
                                                                    <div class="dropdown-item d-flex justify-content-between align-items-center py-2">
                                                                        <a href="../../<?= $direccion ?>" target="_blank"
                                                                            class="text-decoration-none card-title-text flex-grow-1">
                                                                            <i class="bi bi-file-earmark-pdf text-danger me-2"></i>
                                                                            <span class="small"><?= htmlspecialchars($nombre) ?></span>
                                                                        </a>
                                                                        <button class="btn btn-sm btn-outline-danger border-0" title="Eliminar documento"
                                                                            onclick="eliminarDocumento(<?= $idDoc ?>)">
                                                                            <i class="bi bi-trash"></i>
                                                                        </button>
                                                                    </div>
                                                                </li>
                                                            <?php endforeach; ?>
                                                        </ul>
                                                    </div>
                                                <?php endif; ?>

                                                <?php if ($e['tipo'] == 'compra' || $e['tipo'] == 'gasto'): ?>
                                                    <?php if (empty($e['documento_url'])): ?>
                                                        <button class="btn btn-sm btn-outline-primary rounded-pill"
                                                            onclick="subirDocumentoCompra(<?= $e['id'] ?>, '<?= $e['folio'] ?? '' ?>', '<?= $e['documento_url'] ?? '' ?>', '<?= $e['tipo'] ?? '' ?>')">
                                                            Agregar <i class="bi bi-upload"></i>
                                                        </button>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                            </div>
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
                                                    <button class="btn btn-sm btn-dark" onclick="abrirDetallePago(<?= $e['id'] ?>)">
                                                        <i class="bi bi-eye"></i>
                                                    </button>
                                                <?php endif; ?>
                                                <?php if ($e['tiene_deuda'] == 1): ?>
                                                    <button class="btn btn-sm btn-danger shadow-sm px-2"
                                                        onclick="abrirDeudaCompra(<?= $e['id'] ?>)" title="Pagar Deuda">
                                                        <i class="bi bi-wallet2"></i>
                                                    </button>
                                                <?php endif; ?>
                                                <?php if (($e['pagado_cpp'] ?? 0) == 1): ?>
                                                    <button class="btn btn-sm btn-success shadow-sm px-2" disabled>
                                                        <i class="bi bi-patch-check-fill"></i>
                                                    </button>
                                                <?php endif; ?>
                                                <?php if ($e['tipo'] != 'pago_deuda'): ?>
                                                    <?php if ($e['tipo'] != 'gasto'): ?>
                                                        <button class="btn btn-sm btn-light border shadow-sm px-2 text-primary"
                                                            onclick="verDetalle('<?= $e['tipo'] ?>', <?= $e['id'] ?>)">
                                                            <i class="bi bi-eye-fill"></i>
                                                        </button>
                                                    <?php endif; ?>
                                                    <?php if ($e['tipo'] == 'gasto'): ?>
                                                        <button class="btn btn-sm btn-light border shadow-sm px-2 text-primary"
                                                            onclick="gastoDetalle_cargarVista('gasto', <?= $e['id'] ?>)">
                                                            <i class="bi bi-eye-fill"></i>
                                                        </button>
                                                    <?php endif; ?>
                                                    <button class="btn btn-sm btn-light border shadow-sm px-2 text-danger"
                                                        onclick="<?= ($e['tipo'] == 'compra') ? "confirmarCancelacionCompra" : "confirmarCancelacionGasto" ?>('<?= $e['id'] ?>','<?= $e['folio'] ?>')">
                                                        <i class="bi bi-trash3"></i>
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="12" class="text-center py-5 text-body-secondary">
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
    <!-- ✅ FIX: </main> cerrado correctamente -->

    <?php require_once __DIR__ . '/egresosComponets/modalCompra.php'; ?>
    <!-- ✅ FIX: modalCompra incluido una sola vez -->

    <?php require_once __DIR__ . '/egresosComponets/modalAjuste.php'; ?>
    <?php require_once __DIR__ . '/egresosComponets/modalDetalles.php'; ?>
    <?php require_once __DIR__ . '/egresosComponets/modalDetalleGasto.php'; ?>
    <?php require_once __DIR__ . '/egresosComponets/modalGasto.php'; ?>
    <?php require_once __DIR__ . '/egresosComponets/cuentasPendientes.php'; ?>
    <?php require_once __DIR__ . '/egresosComponets/historialCuentasPorPagar.php'; ?>
    <?php require_once __DIR__ . '/egresosComponets/modalDetallePago.php'; ?>
    <?php require_once __DIR__ . '/solicitudesCompra/imprimirModal.php'; ?>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <?php require_once __DIR__ . '/solicitudesCompra/ModalSolicitud.php'; ?>

    <script>
        window.DATA_COMPRAS = {
            productos: <?php echo json_encode($productos); ?>,
            almacenes: <?php echo json_encode($almacenes); ?>
        };
        console.log("Productos cargados:", window.DATA_COMPRAS.productos);
    </script>

    <script>
        function imprimirTablaPDF(tablaId = 'egresosTabla') {
            const tablaOriginal = document.getElementById(tablaId);

            if (!tablaOriginal) {
                console.error(`No se encontró la tabla con el ID: ${tablaId}`);
                return;
            }

            const tablaClonada = tablaOriginal.cloneNode(true);

            // 1. Remover la columna de "Acciones" (última columna)
            tablaClonada.querySelectorAll('tr').forEach(row => {
                if (row.lastElementChild) {
                    row.lastElementChild.remove();
                }
            });

            // 2. Limpiar elementos innecesarios
            tablaClonada.querySelectorAll('button, .dropdown-menu, script').forEach(el => el.remove());

            // ✅ FIX: ajustar colspan al eliminar la última columna
            tablaClonada.querySelectorAll('td[colspan]').forEach(td => {
                const cs = parseInt(td.getAttribute('colspan')) || 1;
                td.setAttribute('colspan', Math.max(1, cs - 1));
            });

            const ventanaImpresion = window.open('', '_blank', 'width=1000,height=750');

            const contenidoHTML = `
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <title>Reporte de Egresos</title>
            <style>
                @page { size: A4 landscape; margin: 8mm; }
                * {
                    box-sizing: border-box;
                    -webkit-print-color-adjust: exact !important;
                    print-color-adjust: exact !important;
                }
                body {
                    font-family: Arial, Helvetica, sans-serif;
                    color: #111;
                    margin: 0;
                    padding: 0;
                    font-size: 8.5pt;
                    line-height: 1.1;
                }
                .header-reporte {
                    display: flex;
                    justify-content: space-between;
                    align-items: flex-end;
                    border-bottom: 1.5pt solid #2c3e50;
                    padding-bottom: 4px;
                    margin-bottom: 8px;
                }
                .header-reporte h2 {
                    margin: 0;
                    color: #2c3e50;
                    font-size: 13pt;
                    text-transform: uppercase;
                    letter-spacing: -0.3px;
                }
                .header-reporte .meta-info { font-size: 8pt; color: #555; }
                table {
                    width: 100% !important;
                    max-width: 100% !important;
                    table-layout: fixed;
                    border-collapse: collapse;
                    word-wrap: break-word;
                    overflow-wrap: break-word;
                }
                th, td {
                    padding: 4px 3px !important;
                    vertical-align: middle;
                    border-bottom: 1px solid #d1d5db;
                    overflow: hidden;
                    text-overflow: ellipsis;
                    white-space: nowrap;
                }
                th {
                    background-color: #f1f5f9 !important;
                    color: #1e293b;
                    font-size: 7.5pt;
                    font-weight: bold;
                    text-transform: uppercase;
                    border-bottom: 1.5pt solid #94a3b8;
                }
                th:nth-child(1), td:nth-child(1) { width: 4%; text-align: center; }
                th:nth-child(2), td:nth-child(2) { width: 12%; }
                th:nth-child(3), td:nth-child(3) { width: 8%; text-align: center; }
                th:nth-child(4), td:nth-child(4) { width: 9%; }
                th:nth-child(5), td:nth-child(5) { width: 8%; text-align: center; }
                th:nth-child(6), td:nth-child(6) { width: 22%; }
                th:nth-child(7), td:nth-child(7) { width: 5%; text-align: center; }
                th:nth-child(8), td:nth-child(8) { width: 10%; text-align: right; }
                th:nth-child(9), td:nth-child(9) { width: 9%; text-align: center; }
                th:nth-child(10), td:nth-child(10) { width: 7%; text-align: center; }
                th:nth-child(11), td:nth-child(11) { width: 6%; text-align: center; }
                tbody tr:nth-child(even) { background-color: #f8fafc !important; }
                .text-center { text-align: center !important; }
                .text-end { text-align: right !important; }
                .fw-bold { font-weight: bold !important; }
                .btn, .dropdown, button, i.bi-folder2-open { display: none !important; }
                .footer-reporte {
                    margin-top: 10px;
                    display: flex;
                    justify-content: space-between;
                    font-size: 7.5pt;
                    color: #64748b;
                }
                @media print {
                    html, body { width: 100%; height: 100%; }
                }
            </style>
        </head>
        <body>
            <div class="header-reporte">
                <div><h2>Reporte de Movimientos de Egreso</h2></div>
                <div class="meta-info">
                    Impreso el: ${new Date().toLocaleDateString('es-MX')} ${new Date().toLocaleTimeString('es-MX', { hour: '2-digit', minute: '2-digit' })}
                </div>
            </div>
            ${tablaClonada.outerHTML}
            <div class="footer-reporte">
                <span>cfsistem - Control Financiero</span>
                <span>Página 1 de 1</span>
            </div>
            <script>
                window.onload = function() {
                    window.print();
                    setTimeout(function() { window.close(); }, 300);
                };
            <\/script>
        </body>
        </html>
    `;

            ventanaImpresion.document.open();
            ventanaImpresion.document.write(contenidoHTML);
            ventanaImpresion.document.close();
        }

        function exportarTablaCSV(tablaId = 'egresosTabla', nombreArchivo = 'Reporte_Egresos') {
            const tablaOriginal = document.getElementById(tablaId);
            if (!tablaOriginal) {
                console.error(`No se encontró la tabla con el ID: ${tablaId}`);
                return;
            }

            const filas = tablaOriginal.querySelectorAll('tr');
            const lineasCSV = [];

            filas.forEach(fila => {
                const celdas = fila.querySelectorAll('th, td');
                if (celdas.length === 0) return;

                const valoresFila = [];
                for (let i = 0; i < celdas.length - 1; i++) {
                    let texto = celdas[i].innerText || celdas[i].textContent || '';
                    texto = texto.replace(/\r?\n|\r/g, ' ').replace(/\s+/g, ' ').trim();
                    texto = texto.replace(/"/g, '""');
                    valoresFila.push(`"${texto}"`);
                }
                lineasCSV.push(valoresFila.join(','));
            });

            const contenidoCSV = lineasCSV.join('\n');
            const blob = new Blob(['\uFEFF' + contenidoCSV], { type: 'text/csv;charset=utf-8;' });
            const url = URL.createObjectURL(blob);
            const enlace = document.createElement('a');
            const fecha = new Date().toISOString().slice(0, 10);

            enlace.href = url;
            enlace.download = `${nombreArchivo}_${fecha}.csv`;
            document.body.appendChild(enlace);
            enlace.click();
            document.body.removeChild(enlace);
            URL.revokeObjectURL(url);
        }

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
                    if (f.desde) f.desde.disabled = false;
                    if (f.hasta) f.hasta.disabled = false;
                    f.form.submit();
                };

                const toggleCategoria = () => {
                    if (!f.tipo || !f.cont_cat) return;
                    if (f.tipo.value === 'gasto') {
                        f.cont_cat.classList.remove('d-none');
                    } else {
                        f.cont_cat.classList.add('d-none');
                        if (f.categoria) f.categoria.value = "0";
                    }
                };

                if (f.periodo) {
                    f.periodo.addEventListener('change', function () {
                        const esPerso = this.value === 'personalizado';
                        const divs = document.querySelectorAll('.div-fechas');
                        const inputs = document.querySelectorAll('.div-fechas input');

                        divs.forEach(div => esPerso ? div.classList.remove('d-none') : div.classList.add('d-none'));
                        inputs.forEach(i => {
                            i.disabled = !esPerso;
                            // ✅ FIX: resetear value al ocultar
                            if (!esPerso) i.value = '';
                        });

                        if (!esPerso) enviar();
                    });
                }

                [f.almacen, f.deuda, f.metodo, f.categoria].forEach(el => {
                    if (el) el.addEventListener('change', enviar);
                });

                if (f.tipo) {
                    f.tipo.addEventListener('change', () => {
                        toggleCategoria();
                        enviar();
                    });
                }

                [f.desde, f.hasta].forEach(el => {
                    if (!el) return;
                    el.addEventListener('change', () => {
                        if (f.periodo) f.periodo.value = 'personalizado';
                        if (f.desde.value && f.hasta.value) enviar();
                    });
                });

                toggleCategoria();
            });
        })();

        (function () {
            $(document).on('hidden.bs.modal', '.modal', function () {
                if ($('.modal.show').length === 0) {
                    $('.modal-backdrop').remove();
                    $('body').removeClass('modal-open').css('padding-right', '');
                } else {
                    $('body').addClass('modal-open');
                }
            });
        })();

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
                        success: function (res) {
                            if (res.success) {
                                Swal.fire('¡Anulada!', res.message, 'success').then(() => location.reload());
                            } else {
                                Swal.fire('Atención', res.message, 'error');
                            }
                        },
                        error: function (xhr) {
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
                        success: function (res) {
                            if (res.success) {
                                Swal.fire('¡Anulado!', res.message, 'success').then(() => location.reload());
                            } else {
                                Swal.fire('Error', res.message, 'error');
                            }
                        },
                        error: function (xhr) {
                            console.error(xhr.responseText);
                            Swal.fire('Error Crítico', 'Consulta la consola (F12).', 'error');
                        }
                    });
                }
            });
        }

        function subirDocumentoCompra(compra_id, folio, documento_actual = '', tipo) {
            console.log('gasto');

            Swal.fire({
                title: 'Documento de Compra',
                html: `
            <div class="text-start">
                <label class="fw-bold small mb-2">Subir / Reemplazar documento</label>
                <input type="file" id="swal_file_doc" class="form-control mb-2" accept=".pdf,image/*">
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
                    formData.append('tipo', tipo);

                    try {
                        const response = await fetch('/cfsistem/app/controllers/egresosController.php?action=subirDocumento', {
                            method: 'POST',
                            body: formData
                        });

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
                }).then(() => {
                    location.reload();
                });
                if (typeof cargarCompras === 'function') {
                    cargarCompras();
                }
            });
        }

        function eliminarDocumento(id) {
            console.log('gasto');

            Swal.fire({
                title: 'Eliminar Documento',
                icon: 'info',
                showCancelButton: true,
                confirmButtonText: 'Guardar',
                confirmButtonColor: '#ed0909',
                focusConfirm: false,

                preConfirm: async () => {
                    const formData = new FormData();
                    formData.append('id', id);

                    try {
                        const response = await fetch('/cfsistem/app/controllers/egresosController.php?action=eliminarDocumento', {
                            method: 'POST',
                            body: formData
                        });

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
                    title: 'Eliminado',
                    text: 'Documento eliminado correctamente',
                    timer: 1800,
                    showConfirmButton: false
                }).then(() => {
                    location.reload();
                });
                if (typeof cargarCompras === 'function') {
                    cargarCompras();
                }
            });
        }
    </script>

</body>

</html>