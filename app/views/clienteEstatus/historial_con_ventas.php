<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expediente: <?= htmlspecialchars($cliente['nombre_comercial']) ?> | CF System</title>
    <link rel="icon" type="image/png" href="/cfsistem/public/assets/logo.png">

    <link rel="shortcut icon" href="/cfsistem/public/assets/logo.ico" type="image/x-icon">


    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

    <style>
        :root {
            --bs-primary: #007aff;
            --bs-info: #3abaf4;
            --bs-success: #1cc88a;
            --bs-danger: #e74a3b;
            --bs-warning: #f6c23e;
            --bg-light: #f8f9fc;
        }

        body {
            background-color: var(--bg-light);
            font-family: 'Inter', sans-serif;
            color: #4e73df;
        }

        .header-expediente {
            background: white;
            border-bottom: 1px solid #e3e6f0;
            padding: 1rem 2rem;
        }

        .kpi-widget {
            background: white;
            border-radius: 12px;
            padding: 1.25rem;
            border: none;
            border-left: 4px solid #e3e6f0;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
            height: 100%;
        }

        .kpi-label {
            font-size: 0.7rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 5px;
        }

        .kpi-value {
            font-size: 1.4rem;
            font-weight: 700;
            color: #5a5c69;
        }

        .border-left-primary {
            border-left-color: var(--bs-primary) !important;
        }

        .border-left-success {
            border-left-color: var(--bs-success) !important;
        }

        .border-left-danger {
            border-left-color: var(--bs-danger) !important;
        }

        .border-left-info {
            border-left-color: var(--bs-info) !important;
        }

        .folio-container {
            background: white;
            border-radius: 12px;
            border: 1px solid #e3e6f0;
            margin-bottom: 2rem;
            overflow: hidden;
        }

        .folio-debe {
            border-left: 5px solid var(--bs-danger);
        }

        .folio-liquidado {
            border-left: 5px solid var(--bs-success);
            background-color: #f6fff9;
        }

        .folio-favor {
            border-left: 5px solid var(--bs-info);
            background-color: #f0f7ff;
        }

        .folio-cancelado {
            border-left: 5px solid #858796;
            background-color: #f8f9fc;
        }

        .folio-header {
            background-color: rgba(0, 0, 0, 0.02);
            padding: 1rem 1.5rem;
            border-bottom: 1px solid #e3e6f0;
        }

        .col-pagos {
            background-color: #fafbfc;
            border-left: 1px solid #e3e6f0;
            padding: 1.5rem;
        }

        .payment-pill {
            background: white;
            border: 1px solid #e3e6f0;
            border-left: 4px solid var(--bs-success);
            border-radius: 8px;
            padding: 10px;
            margin-bottom: 8px;
        }

        @media (min-width: 992px) {
            .w-lg-60 {
                width: 60% !important;
            }
        }

        /* ===== Sub-tabla de productos por venta ===== */
        .productos-row {
            background: transparent;
        }

        .productos-row>td {
            padding: 0 !important;
            border-top: none !important;
        }

        /* Desactiva hover de Bootstrap sobre la fila de productos */
        .table-hover>tbody>tr.productos-row:hover>td {
            background-color: transparent !important;
        }

        .productos-container {
            padding: 0.6rem 1.5rem 1rem 2rem;
            background: #fafbfc;
            border-left: 4px solid #007aff;
            border-radius: 0 0 10px 10px;
            margin: 0 0.75rem 0.9rem 0.75rem;
            box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.03);
        }

        .productos-container table {
            font-size: 0.8rem;
            margin-bottom: 0;
        }

        .productos-container thead th {
            font-size: 0.68rem;
            text-transform: uppercase;
            color: #858796;
            font-weight: 700;
            letter-spacing: 0.3px;
            border-bottom: 1px solid #e3e6f0;
            padding: 0.35rem 0.5rem;
            background: transparent;
        }

        .productos-container tbody td {
            padding: 0.4rem 0.5rem;
            border-bottom: 1px solid #f0f1f5;
            vertical-align: middle;
        }

        .productos-container tbody tr:last-child td {
            border-bottom: none;
        }

        /* Separador visual entre cada venta */
        .venta-row>td {
            border-bottom: none !important;
        }

        .venta-row:not(:first-child)>td {
            border-top: 2px solid #e3e6f0 !important;
        }
    </style>
</head>

<body>

    <header class="header-expediente shadow-sm mb-4">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <div>
                <h4 class="fw-bold mb-0 text-dark"><?= htmlspecialchars($cliente['nombre_comercial']) ?></h4>
                <span class="badge bg-primary-subtle text-primary">RFC: <?= htmlspecialchars($cliente['rfc']) ?></span>
            </div>

            <div style="display:flex; gap:10px; align-items:end; margin-bottom:15px; flex-wrap:wrap;">



                <?php
                date_default_timezone_set('America/Mexico_City');
                $fechaInicio = $_GET['fecha_inicio'] ?? date('Y-m-01');
                $fechaFin = $_GET['fecha_fin'] ?? date('Y-m-t');
                ?>

                <div>
                    <label style="font-size:12px;">Fecha inicio</label>
                    <input type="date" id="fecha_inicio" class="form-control"
                        value="<?= htmlspecialchars($fechaInicio) ?>">
                </div>

                <div>
                    <label style="font-size:12px;">Fecha fin</label>
                    <input type="date" id="fecha_fin" class="form-control" value="<?= htmlspecialchars($fechaFin) ?>">
                </div>

                <button class="btn btn-primary" onclick="filtrarExpediente()">
                    Filtrar
                </button>


                <button class="btn btn-dark btn-sm" onclick="imprimirEstadoCuenta() ">
                    <i class="bi bi-printer"></i> Imprimir
                </button>
            </div>

        </div>
    </header>


    <div class="container-fluid px-4">

        <?php if ($resumen['saldo_total'] < -0.01): ?>
            <div class="alert alert-info border-0 shadow-sm mb-4 d-flex align-items-center p-3"
                style="border-radius: 12px;">
                <i class="bi bi-info-square-fill fs-3 me-3 text-info"></i>
                <div>
                    <h5 class="mb-0 fw-bold">Saldo a Favor General</h5>
                    <span>El cliente tiene <b>$ <?= number_format(abs($resumen['saldo_total']), 2) ?></b> disponible.</span>
                </div>
            </div>
        <?php endif; ?>
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="kpi-widget border-left-primary">
                    <div class="kpi-label text-primary">Compras Totales</div>
                    <div class="kpi-value">$ <?= number_format($resumen['total_comprado'], 2) ?></div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="kpi-widget border-left-success">
                    <div class="kpi-label text-success">Total Pagado</div>
                    <div class="kpi-value">$ <?= number_format($resumen['total_pagado'], 2) ?></div>
                </div>
            </div>

            <?php
            $saldoReal = $resumen['saldo_total'];
            $esSaldoAFavor = $saldoReal < -0.01;
            $claseColor = $esSaldoAFavor ? 'border-left-info' : 'border-left-danger';
            ?>

            <div class="col-md-3">
                <div class="kpi-widget <?= $claseColor ?>">
                    <div class="kpi-label <?= $esSaldoAFavor ? 'text-info' : 'text-danger' ?>">
                        <?= $esSaldoAFavor ? 'A Favor' : 'Saldo Pendiente' ?>
                    </div>
                    <div class="kpi-value <?= $esSaldoAFavor ? 'text-info' : 'text-danger' ?>">
                        $ <?= number_format(abs($saldoReal), 2) ?>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="kpi-widget d-flex align-items-center justify-content-between border-left-primary">
                    <div style="width: 60px; height: 60px;"><canvas id="chartDona"></canvas></div>
                    <div class="text-end">
                        <div class="kpi-label">Estatus</div>
                        <div class="fw-bold text-dark">
                            <?= round(($resumen['total_pagado'] / max($resumen['total_comprado'], 1)) * 100) ?>%
                        </div>
                    </div>
                </div>
            </div>



            <div class="d-flex justify-content-center px-2">
                <div class="card border-0 shadow-sm mb-3 w-100 w-lg-60" style="max-width: 1200px;">
                    <h5 class="fw-bold mb-3 text-dark">Folios Detallados</h5>

                    <div class="table-responsive">
                        <table class="table align-middle table-hover mb-0">
                            <thead>
                                <tr>
                                    <th scope="col" class="ps-3">Fecha</th>
                                    <th scope="col">Folio Interno</th>
                                    <th scope="col" class="text-end">Total Facturado</th>
                                    <th scope="col" class="text-end">Total Abonado</th>
                                    <th scope="col" class="text-end">Saldo</th>
                                    <th scope="col" class="text-center">Estado / Acción</th>
                                    <th scope="col" class="text-center pe-3">Opciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($expediente as $v):
                                    $idActual = $v['venta_id'] ?? $v['id'];
                                    $esCancelada = (isset($v['estado_general']) && $v['estado_general'] == 'cancelada');
                                    $saldoFolio = floatval($v['total']) - floatval($v['total_pagado']);
                                    $folioLiquidado = abs($saldoFolio) <= 0.01;
                                    $folioAFavorIndividual = $saldoFolio < -0.01;
                                    ?>
                                    <!-- FILA PRINCIPAL DE LA VENTA -->
                                    <tr class="venta-row">
                                        <td class="ps-3">
                                            <small
                                                class="text-body-secondary"><?= date('d/m/Y', strtotime($v['fecha'])) ?></small>
                                        </td>

                                        <td>
                                            <span class="badge bg-light text-dark border">
                                                <?= htmlspecialchars($v['folio']) ?>
                                            </span>
                                        </td>

                                        <td class="text-end fw-semibold text-dark">
                                            $<?= number_format($v['total'], 2) ?>
                                        </td>

                                        <td class="text-end fw-semibold text-success">
                                            $<?= number_format($v['total_pagado'], 2) ?>
                                        </td>

                                        <td class="text-end fw-bold <?= $saldoFolio > 0 ? 'text-danger' : 'text-info' ?>">
                                            $<?= number_format(abs($saldoFolio), 2) ?>
                                        </td>

                                        <td class="text-center">
                                            <?php if ($esCancelada): ?>
                                                <span class="badge bg-secondary px-3 py-2">CANCELADA</span>
                                            <?php elseif ($folioLiquidado): ?>
                                                <span class="badge bg-success px-3 py-2">LIQUIDADO</span>
                                            <?php elseif ($folioAFavorIndividual): ?>
                                                <span class="badge bg-info px-3 py-2">A FAVOR</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger px-3 py-2">PENDIENTE</span>
                                            <?php endif; ?>
                                        </td>

                                        <td class="text-center pe-3">
                                            <div class="d-flex gap-1 justify-content-center">
                                                <a class="btn btn-outline-primary btn-sm"
                                                    href="/cfsistem/app/backend/ventas/ticket_venta.php?id=<?= $idActual ?>"
                                                    target="_blank" title="Ticket">
                                                    <i class="bi bi-receipt"></i>
                                                </a>
                                                <a class="btn btn-outline-secondary btn-sm"
                                                    href="/cfsistem/app/backend/ventas/ticketFormal.php?id=<?= $idActual ?>"
                                                    target="_blank" title="Remisión">
                                                    <i class="bi bi-file-earmark-text"></i>
                                                </a>
                                                <button class="btn btn-sm btn-dark"
                                                    onclick="verDetalle(<?= intval($idActual) ?>)" title="Ver Detalle">
                                                    <i class="bi bi-eye-fill"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- FILA DE PRODUCTOS (se llena automáticamente vía JS) -->
                                    <tr class="productos-row" id="productos-row-<?= $idActual ?>">
                                        <td colspan="7">
                                            <div class="productos-container" data-venta-id="<?= $idActual ?>">
                                                <div class="text-center py-2">
                                                    <div class="spinner-border spinner-border-sm text-primary"></div>
                                                    <small class="text-muted ms-2">Cargando productos...</small>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="modal fade" id="modalDetalle" tabindex="-1">
                    <div class="modal-dialog modal-xl modal-dialog-centered">
                        <div class="modal-content border-0 shadow">

                            <div class="modal-header bg-dark text-white">
                                <h6 class="modal-title fw-bold">
                                    Información Venta: <span id="spanFolio" class="text-warning"></span>
                                </h6>
                                <button type="button" class="btn-close btn-close-white"
                                    data-bs-dismiss="modal"></button>
                            </div>

                            <div class="modal-body p-0">
                                <div class="row g-0">

                                    <div class="col-md-3 bg-light border-end p-4">

                                        <div class="mb-3">
                                            <small class="text-uppercase text-body-secondary fw-bold">Cliente</small>
                                            <div id="detCliente" class="fw-semibold"></div>
                                        </div>

                                        <div class="mb-4">
                                            <small class="text-uppercase text-body-secondary fw-bold">Almacén</small>
                                            <div id="detAlmacen" class="fw-semibold"></div>
                                        </div>

                                        <div class="card border-0 shadow-sm mb-3">
                                            <div class="card-body text-center">

                                                <div class="mb-3">
                                                    <small class="text-uppercase text-body-secondary fw-bold d-block">
                                                        Total de Venta
                                                    </small>
                                                    <span id="detTotalLabel" class="fs-5 fw-bold text-primary">
                                                        $0.00
                                                    </span>
                                                </div>

                                                <hr>

                                                <div>
                                                    <small class="text-uppercase text-body-secondary fw-bold d-block">
                                                        Saldo Pendiente
                                                    </small>
                                                    <span id="detSaldoLabel" class="fs-4 fw-bold text-danger">
                                                        $0.00
                                                    </span>
                                                </div>


                                            </div>
                                        </div>
                                        <button type="button" class="btn btn-outline-primary rounded-pill px-3"
                                            onclick="imprimirContenidoModal()">
                                            <i class="bi bi-printer-fill me-1"></i> Imprimir a PDF
                                        </button>

                                        <!-- <div id="boton"></div> -->
                                    </div>

                                    <div class="col-md-9 p-4">

                                        <div class="card border-0 shadow-sm mb-3">
                                            <div
                                                class="card-header bg-white fw-bold small text-uppercase text-body-secondary">
                                                Productos
                                            </div>
                                            <div class="table-responsive" style="max-height: 180px;">
                                                <table class="table table-sm align-middle mb-0">
                                                    <thead class="table-light">
                                                        <tr class="small text-uppercase">
                                                            <th>Producto</th>
                                                            <th class="text-center">Venta</th>
                                                            <th class="text-center">Surtido</th>
                                                            <th class="text-center text-danger">Falta</th>
                                                            <th class="text-center d-none">Entrega</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="tbodyDetalle"></tbody>
                                                </table>
                                            </div>
                                        </div>

                                        <div class="row g-3">

                                            <div class="col-12">
                                                <div class="card border-0 shadow-sm">
                                                    <div
                                                        class="card-header bg-white fw-bold small text-uppercase text-body-secondary">
                                                        Historial de Pagos
                                                    </div>
                                                    <div class="table-responsive" style="max-height: 180px;">
                                                        <table class="table table-sm align-middle mb-0">
                                                            <thead class="table-light">
                                                                <tr class="small text-uppercase">
                                                                    <th>Fecha</th>
                                                                    <th>Monto</th>
                                                                    <th>Método</th>
                                                                    <th>REFERENCIA</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody id="tbodyPagos"></tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-12 d-none">
                                                <div class="card border-0 shadow-sm">
                                                    <div
                                                        class="card-header bg-white fw-bold small text-uppercase text-body-secondary">
                                                        Historial de Entregas
                                                    </div>
                                                    <div class="table-responsive" style="max-height: 180px;">
                                                        <table class="table table-sm align-middle mb-0">
                                                            <thead class="table-light">
                                                                <tr class="small text-uppercase">
                                                                    <th>Fecha</th>
                                                                    <th>Responsable</th>
                                                                    <th>Producto</th>
                                                                    <th class="text-center">Cant</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody id="tbodyHistorial"></tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-12 d-none">
                                                <div class="card border-0 shadow-sm">
                                                    <div
                                                        class="card-header bg-white fw-bold small text-uppercase text-body-secondary">
                                                        Repartos
                                                    </div>
                                                    <div class="table-responsive" style="max-height: 220px;">
                                                        <table class="table table-sm align-middle mb-0">
                                                            <thead class="table-light">
                                                                <tr class="small text-uppercase">
                                                                    <th># Reparto</th>
                                                                    <th>Fecha Entrega</th>
                                                                    <th>Estado</th>
                                                                    <th class="text-center">Ruta</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody id="tbodyRepartos"></tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>

                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
                <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
                <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

                <?php require_once __DIR__ . '/../ventasHistorialModales/registarAbonoCliente.php' ?>


                <script>
                    const modalNuevoAbonoObj = new bootstrap.Modal('#modalNuevoAbono');


                    let ventaActual = null;
                    // La ruta al controlador (ajusta si el nombre del archivo varía)
                    const URL_CONTROLLER = '/cfsistem/app/controllers/ventasHistorialController.php';
                    $(document).ready(function () {
                        renderCharts();
                    });

                    function renderCharts() {
                        const ctx = document.getElementById('chartDona');
                        if (!ctx) return;

                        new Chart(ctx, {
                            type: 'doughnut',
                            data: {
                                datasets: [{
                                    data: [
                                        <?= isset($resumen['total_pagado']) ? floatval($resumen['total_pagado']) : 0 ?>,
                                        <?= isset($resumen['saldo_total']) ? max(0, floatval($resumen['saldo_total'])) : 0 ?>
                                    ],
                                    backgroundColor: ['#1cc88a', '#e74a3b'],
                                    borderWidth: 0
                                }]
                            },
                            options: {
                                cutout: '75%',
                                plugins: {
                                    legend: {
                                        display: false
                                    }
                                },
                                responsive: true,
                                maintainAspectRatio: false
                            }
                        });
                    }

                    function abrirModalSaldarFavor(favorDisponible, deudaPendiente) {
                        // Calculamos el límite: no podemos usar más de lo que hay, ni pagar más de lo que se debe
                        const montoMaximo = Math.min(favorDisponible, deudaPendiente);

                        Swal.fire({
                            title: 'Compensación de Saldos',
                            icon: 'info',
                            html: `
            <div class="text-start border-bottom pb-2 mb-3" style="font-size: 0.9rem;">
                <div class="d-flex justify-content-between mb-1">
                    <span>Saldo a Favor:</span>
                    <b class="text-success">$ ${favorDisponible.toLocaleString('es-MX', { minimumFractionDigits: 2 })}</b>
                </div>
                <div class="d-flex justify-content-between">
                    <span>Deuda en Contra:</span>
                    <b class="text-danger">$ ${deudaPendiente.toLocaleString('es-MX', { minimumFractionDigits: 2 })}</b>
                </div>
            </div>
            <div class="text-start">
                <label class="form-label fw-bold small">Monto a compensar:</label>
                <div class="input-group">
                    <span class="input-group-text">$</span>
                    <input type="number" id="monto_cruce" class="form-control form-control-lg fw-bold" 
                           value="${montoMaximo.toFixed(2)}" 
                           max="${montoMaximo}" min="0.01" step="0.01">
                </div>
                <p class="text-body-secondary mt-2" style="font-size: 0.75rem;">
                    <i class="bi bi-info-circle"></i> Este ajuste restará el monto de ambas cuentas para limpiar el historial del cliente.
                </p>
            </div>
        `,
                            showCancelButton: true,
                            confirmButtonText: 'Aplicar Ajuste',
                            cancelButtonText: 'Cancelar',
                            confirmButtonColor: '#f6c23e',
                            reverseButtons: true,
                            preConfirm: () => {
                                const monto = document.getElementById('monto_cruce').value;
                                if (!monto || monto <= 0 || monto > montoMaximo) {
                                    Swal.showValidationMessage(
                                        `Monto inválido. Máximo permitido: $${montoMaximo.toFixed(2)}`);
                                }
                                return monto;
                            }
                        }).then((result) => {
                            if (result.isConfirmed) {
                                procesarAjusteContable(result.value);
                            }
                        });
                    }

                    function procesarAjusteContable(monto) {
                        Swal.fire({
                            title: 'Procesando ajuste...',
                            allowOutsideClick: false,
                            didOpen: () => Swal.showLoading()
                        });

                        // Ajustamos la llamada para que coincida con tu estructura de 'accion'
                        $.post('/cfsistem/app/controllers/clienteExpedienteController.php', {
                            accion: 'saldar_deuda_con_favor', // El nombre del case que definimos
                            id_cliente: <?= json_encode($id_cliente ?? 0) ?>,
                            monto_a_usar: monto
                        }, function (res) {
                            if (res.status === 'success') {
                                Swal.fire({
                                    title: '¡Éxito!',
                                    text: res.message,
                                    icon: 'success',
                                    confirmButtonColor: '#007aff'
                                }).then(() => {
                                    location.reload(); // Recarga para actualizar los KPIs con los nuevos saldos
                                });
                            } else {
                                Swal.fire('Error', res.message || 'Error al procesar el ajuste.', 'error');
                            }
                        }, 'json').fail(function () {
                            Swal.fire('Error', 'No se pudo conectar con el servidor.', 'error');
                        });
                    }
                </script>

                <script>
                    function filtrarExpediente() {
                        const fechaInicio = document.getElementById('fecha_inicio').value;
                        const fechaFin = document.getElementById('fecha_fin').value;

                        const urlParams = new URLSearchParams(window.location.search);
                        const id = urlParams.get('id');

                        console.log("ID:", id);

                        // REDIRECCIÓN
                        window.location.href =
                            `/cfsistem/app/controllers/clienteExpedienteController.php?id=${id}&fecha_inicio=${fechaInicio}&fecha_fin=${fechaFin}`;


                    }
                </script>
                <script>
                    async function verDetalle(id) {
                        console.log(id);
                        let venta = parseInt(id);
                        try {
                            // 🔥 OBTENER IDS PENDIENTES
                            const respIds = await fetch(
                                `/cfsistem/app/controllers/accesoController.php?action=get_ids_pendientes_venta&venta_id=${id}`
                            );
                            const resNAlmacen = await fetch(
                                `/cfsistem/app/controllers/accesoController.php?action=obtener_id_almacen&id=${id}`
                            );

                            const dataAlmacen = await resNAlmacen.json();
                            const almacen_id_conseguido = dataAlmacen.almacen.almacen_id;
                            console.log(dataAlmacen.almacen.almacen_id);

                            const dataIds = await respIds.json();
                            console.log(dataIds.ids);

                            // =====================================================
                            // 🔥 HABILITAR / DESHABILITAR BOTÓN GESTIÓN
                            // =====================================================
                            if (Array.isArray(dataIds.ids) && dataIds.ids.length > 0) {
                                console.log('hola');
                                $('#btnGestionVenta')
                                    .removeClass('d-none')
                                    .prop('disabled', false)
                                    .attr(
                                        'onclick',
                                        `abrirModalDespachoVentaTotal(${id}, ${almacen_id_conseguido})`
                                    );
                            } else {
                                $('#btnGestionVenta')
                                    .addClass('d-none')
                                    .prop('disabled', true)
                                    .removeAttr('onclick');
                            }

                            const res = await fetch(`/cfsistem/app/controllers/historialPedidosVendedorController.php?action=obtenerDetalle&id=${id}`);
                            cargarRepartos(id);
                            const data = await res.json();



                            $('#spanFolio').text(data.info.folio);
                            $('#detCliente').text(data.info.nombre_comercial);
                            $('#detAlmacen').text(data.info.almacen);

                            // =====================================================
                            // CORRECCIÓN: PRIMERO INYECTAMOS EL BOTÓN EN EL DOM
                            // =====================================================
                            const htmlboton = `
            <button id="btnAbonar"
                class="btn btn-primary w-100 fw-bold shadow-sm"
                onclick="abrirNuevoAbono(${id})">
                <i class="bi bi-cash-coin me-1"></i> Registrar Abono
            </button>
        `;
                            $('#boton').html(htmlboton);

                            // =====================================================
                            // AHORA QUE EXISTE, VALIDAMOS EL SALDO Y SU VISIBILIDAD
                            // =====================================================
                            const total = parseFloat(data.info.total) || 0;
                            const pagado = parseFloat(data.info.total_pagado) || 0;
                            const deuda = total - pagado;
                            $('#detTotalLabel').text('$' + total.toFixed(2));

                            if (deuda <= 0) {
                                $('#detSaldoLabel').text('LIQUIDADO').removeClass('text-danger').addClass('text-success');
                                $('#btnAbonar').addClass('d-none'); // <-- Ya funciona porque el botón ya existe
                            } else {
                                $('#detSaldoLabel').text('$' + deuda.toFixed(2)).removeClass('text-success').addClass('text-danger');
                                $('#btnAbonar').removeClass('d-none');
                            }

                            // --- RENDERIZADO DE PRODUCTOS CON CONVERSIÓN ---
                            $('#tbodyDetalle').html(data.productos.map(p => {
                                let cant = parseFloat(p.cantidad) || 0;
                                let pendiente = (cant - (parseFloat(p.cantidad_entregada) || 0)).toFixed(3);

                                let factor = parseFloat(p.factor_conversion) || 1;
                                let cantPendiente = pendiente / factor;

                                let pen = Number(pendiente);
                                let pendi = Number(cantPendiente);
                                let disponible = (p.disponible / factor);
                                console.log(disponible);
                                let entregada = p.cantidad_entregada / factor;

                                console.log({
                                    pen,
                                    tipo: typeof pen,
                                    comparacion: pen > 0
                                });

                                let visualizacionVenta = "";
                                let infoEquivalenciaSub = "";
                                let unm = (parseFloat(p.cantidad_entregada) / (1 / parseFloat(p.equivalencia)));
                                console.log(unm);
                                unm = unm % 1 !== 0 ? unm.toFixed(0) : unm;

                                if (factor > 1 && cant >= factor) {
                                    let unidadesMayores = (cant / factor);
                                    let totalUnidadesStr = Number.isInteger(unidadesMayores) ? unidadesMayores : unidadesMayores.toFixed(2);

                                    visualizacionVenta = `<span class="fw-bold">${totalUnidadesStr} ${p.unidad_reporte}</span> <br> <small class="text-body-secondary">(${cant} ${p.unidad_medida})</small>`;
                                    infoEquivalenciaSub = `<div class="text-body-secondary small" style="font-size: 0.65rem;">1 ${p.unidad_reporte} = ${factor} ${p.unidad_medida}</div>`;
                                } else {
                                    visualizacionVenta = `<span>${cant} ${p.unidad_medida}</span>`;
                                }

                                return `<tr>
                <td>
                    <div class="fw-bold text-dark">${p.producto}</div>
                    ${infoEquivalenciaSub}
                </td>
                <td class="text-center">
                    ${cant} ${p.unidad_medida} 
                    (${p.equivalencia >= 1 ? cant / (1 / p.equivalencia).toFixed(2) : (cant * (p.equivalencia)).toFixed(2)} ${p.nombre})
                </td>
                <td class="text-center">${entregada > 1 ? entregada + p.unidad_reporte : p.cantidad_entregada + p.unidad_medida}</td>
                <td class="text-center text-danger fw-bold">${(cantPendiente >= 1 ? cantPendiente.toFixed(3) : pen)} ${cantPendiente >= 1 ? p.unidad_reporte : p.unidad_medida}</td>
                <td class="text-center col-input d-none">
                    ${pen.toFixed(4) > 0 ?
                                        `<input type="number"
                            class="form-control form-control-sm input-entrega1 mx-auto"
                            max="${pen <= p.disponible ? (pendi >= 1 ? pendi : pen) : (disponible > 1 ? disponible : p.disponible)}"
                            min="0"
                            step="0.01"
                            value="0.00"
                            data-dvid="${p.dvid}"
                            data-id="${p.producto_id}"
                            data-factor="${(pendi >= 1 && disponible >= 1) ? factor : 1}"
                            style="width:70px">
                        <input type="hidden" class="form-control form-control-sm input-entrega mx-auto" 
                            value="0" data-dvid=${p.dvid} data-id="${p.producto_id}" style="width:70px" step="0.01" min="0">
                        <span class="badge bg-success">${(pendi >= 1 && disponible >= 1) ? p.unidad_reporte : p.unidad_medida}</span>`
                                        : '<span class="badge bg-success">Completo</span>'}
                </td>
            </tr>`;
                            }).join(''));

                            // --- RENDERIZADO DE HISTORIAL DE ENTREGAS ---
                            $('#tbodyHistorial').html(data.historial && data.historial.length > 0 ? data.historial.map(h => {
                                let cantH = parseFloat(h.cantidad) || 0;
                                let factorH = parseFloat(h.factor_conversion) || 1;
                                let uReporteH = h.unidad_reporte || '';
                                let uMedidaH = h.unidad_medida || '';
                                let visualizacionHistorial = "";

                                if (factorH > 1 && cantH >= factorH) {
                                    let unidadesMayoresH = (cantH / factorH);
                                    let totalUnidadesStrH = Number.isInteger(unidadesMayoresH) ? unidadesMayoresH : unidadesMayoresH.toFixed(2);
                                    visualizacionHistorial = `<span class="fw-bold text-primary">${totalUnidadesStrH} ${uReporteH}</span><br><small class="text-body-secondary">(${cantH} ${uMedidaH})</small>`;
                                } else {
                                    visualizacionHistorial = `<span>${cantH} ${uMedidaH}</span>`;
                                }

                                return `<tr>
                    <td class="small">${h.fecha}</td>
                    <td class="small">${h.usuario_nombre}</td>
                    <td><div class="fw-bold" style="font-size:0.85rem;">${h.producto}</div></td>
                    <td class="text-center">${visualizacionHistorial}</td>
                </tr>`;
                            }).join('') :
                                '<tr><td colspan="4" class="text-center text-body-secondary p-3">No hay entregas registradas</td></tr>'
                            );

                            // --- RENDERIZADO DE HISTORIAL DE PAGOS ---
                            if (data.pagos && data.pagos.length > 0) {
                                $('#tbodyPagos').html(data.pagos.map(p => `
                <tr>
                    <td class="small">${p.fecha}</td>
                    <td class="fw-bold text-success">$${parseFloat(p.monto).toFixed(2)}</td>
                    <td>
                        <span class="badge bg-light text-dark border fw-normal">${p.metodo_pago}</span>
                        <div class="text-body-secondary" style="font-size:0.65rem">Recibió: ${p.usuario_nombre}</div>
                    </td>
                    <td>
                     <span>
    ${p.metodo_pago !== 'Efectivo' &&
                                        p.metodo_pago !== 'Saldo a Favor'
                                        ? (p.referencia ?? '')
                                        : '-'
                                    }
</span> 
                </tr>
            `).join(''));
                            } else {
                                $('#tbodyPagos').html('<tr><td colspan="3" class="text-center text-body-secondary p-3">No hay abonos registrados</td></tr>');
                            }

                            const modalObj = new bootstrap.Modal('#modalDetalle');
                            modalObj.show();
                        } catch (error) {
                            console.error("Error al obtener detalle:", error);
                        }
                    }
                    async function cargarRepartos(idVenta) {

                        const resp = await fetch(
                            `/cfsistem/app/controllers/repartosController.php?action=get_repartos_venta&id=${idVenta}`
                        );

                        const repartoViaje = await resp.json();

                        const tbody = document.getElementById('tbodyRepartos');
                        tbody.innerHTML = '';

                        if (!repartoViaje.success) return;

                        // ================================
                        // AGRUPAR POR FOLIO VIAJE
                        // ================================
                        const grupos = {};

                        repartoViaje.data.forEach(item => {

                            if (!grupos[item.folio_viaje]) {

                                grupos[item.folio_viaje] = {
                                    folio_viaje: item.folio_viaje,
                                    fecha_viaje: item.fecha_viaje,
                                    estatus_logistico: item.estatus_logistico,
                                    productos: [],
                                    clientes: new Set()
                                };
                            }

                            grupos[item.folio_viaje].productos.push(item.productos);
                            grupos[item.folio_viaje].clientes.add(item.cliente);
                        });

                        // ================================
                        // RENDER TABLA
                        // ================================
                        Object.values(grupos).forEach(g => {

                            const estadoClass =
                                g.estatus_logistico === 'completado'
                                    ? 'bg-success'
                                    : 'bg-warning text-dark';

                            const tr = `
            <tr>

                <td class="fw-bold">
                    ${g.folio_viaje}
                </td>

                <td>
                    ${g.fecha_viaje}
                </td>

                <td>
                    <span class="badge ${estadoClass}">
                        ${g.estatus_logistico}
                    </span>
                </td>

                <td class="text-center">

                    <button class="btn btn-sm btn-outline-primary"
                      onclick="imprimirRuta('${idVenta}','${g.folio_viaje}')">

                      
                        Ver Reparto 
                    </button>

                </td>

            </tr>
        `;

                            tbody.insertAdjacentHTML('beforeend', tr);
                        });
                    }

                    function togglePerso() {
                        $('#div_p').toggleClass('d-none', $('#f_rango').val() !== 'personalizado');
                        getVentas();
                    }
                    function imprimirContenidoModal() {
                        // 1. Obtener los elementos clave del modal actual
                        const folio = $('#spanFolio').text();
                        const cliente = $('#detCliente').text();
                        const almacen = $('#detAlmacen').text();

                        // 2. Clonar las tablas de datos para no alterar el modal visual
                        const tablaProductos = $('#tbodyDetalle').html();
                        const tablaEntregas = $('#tbodyHistorial').html();
                        const tablaPagos = $('#tbodyPagos').html();

                        const total = $('#detTotalLabel').text();
                        const saldo = $('#detSaldoLabel').text();

                        // 3. Crear una nueva ventana temporal en el navegador
                        const ventanaImpresion = window.open('', '_blank');

                        // 4. Inyectar el HTML estructurado con estilos limpios y profesionales
                        ventanaImpresion.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>Venta - Folio ${folio}</title>
      <link rel="icon" type="image/png" href="/cfsistem/public/assets/logo.png">

    <link rel="shortcut icon" href="/cfsistem/public/assets/logo.ico" type="image/x-icon">

            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
            <style>
                body {font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; padding: 30px; color: #333; }
                .ticket-header { border-bottom: 2px solid #007aff; padding-bottom: 15px; margin-bottom: 20px; }
                .meta-box { background: #f8f9fa; padding: 12px; border-radius: 8px; margin-bottom: 15px; }
                .section-title { font-size: 0.85rem; font-weight: bold; text-transform: uppercase; color: #666; margin-top: 25px; margin-bottom: 10px; letter-spacing: 0.5px; }
                .table-responsive { max-height: none !important; overflow: visible !important; }
                .d-none { display: none !important; } /* Oculta columnas de inputs si están activas */
                @media print {
                    body { padding: 20px;  }
                    .btn-imprimir { display: none; }
                }
                     @page { 
                        margin: 0; /* Esto elimina el título de arriba y la fecha/hora de abajo */
                    }
            </style>
        </head>
        <body>
         <div id="areaImpresion" class="text-uppercase  bg-white" style="min-height: 650px; font-size: 0.95rem;">
 <img
    src="/cfsistem/public/assets/logo.ico"
    style="
        position: fixed;
        top: 30%;                  /* Centro vertical */
        left: 50%;                 /* Centro horizontal */
        transform: translate(-50%, -50%); /* Compensa el propio tamaño de la imagen */
        width: 240px;
        opacity: 0.08;
        z-index: 1;               /* Cambiado a -1 para que quede detrás del texto y no tape los clics */
        pointer-events: none;      /* Evita que interfiera si alguien intenta hacer clic sobre ella */
    "
>
                        <!-- ENCABEZADO -->
                        
<div class=" ">

    <!-- Logo + Título -->
    <div class="">

        <img src="/cfsistem/public/assets/logo.ico"
             alt="Logo"
             width="55"
             height="55"
             class="me-3">

         <div class="ticket-header d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="fw-bold m-0">CF SYSTEM</h4>
                    <small class="text-body-secondary">Reporte de Operación de Venta</small>
                </div>
                <div class="text-end">
                    <h5 class="text-primary fw-bold m-0">Folio: ${folio}</h5>
                </div>
            </div>

            
    </div>

  


                        </div>
                        <div class="row g-3">
                <div class="col-6">
                    <div class="meta-box">
                        <small class="text-body-secondary d-block text-uppercase fw-semibold" style="font-size:0.7rem;">Cliente</small>
                        <span class="fw-bold">${cliente}</span>
                    </div>
                </div>
                <div class="col-6">
                    <div class="meta-box">
                        <small class="text-body-secondary d-block text-uppercase fw-semibold" style="font-size:0.7rem;">Almacén Origen</small>
                        <span class="fw-bold">${almacen}</span>
                    </div>
                </div>
            </div>
<div class="section-title">📦 Productos</div>
            <div class="table-responsive" style="max-height: 180px;">
                                <table class="table table-sm align-middle mb-0">
                                    <thead class="table-light">
                                        <tr class="small text-uppercase">
                                            <th>Producto</th>
                                            <th class="text-center">Venta</th>
                                            <th class="text-center">Surtido</th>
                                            <th class="text-center text-danger">Falta</th>
                                            <th class="text-center d-none">Entrega</th>
                                        </tr>
                                    </thead>
                                    <tbody >${tablaProductos}</tbody>
                                </table>
                            </div>
                        <div class="row g-3 py-5">

                            <div class="col-12">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-header bg-white fw-bold small text-uppercase text-body-secondary">
                                        Historial de Pagos
                                    </div>
                                    <div class="table-responsive" style="max-height: 180px;">
                                        <table class="table table-sm align-middle mb-0">
                                            <thead class="table-light">
                                                <tr class="small text-uppercase">
                                                    <th>Fecha</th>
                                                    <th>Monto</th>
                                                    <th>Método</th>
                                                    <th>REFERENCIA</th>
                                                </tr>
                                            </thead>
                                            <tbody>${tablaPagos}</tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12  d-none">
                           
                                <div class="card border-0 shadow-sm">
                                    <div class="card-header bg-white fw-bold small text-uppercase text-body-secondary">
                                        Historial de Entregas
                                    </div>
                                    <div class="table-responsive" style="max-height: 180px;">
                                        <table class="table table-sm align-middle mb-0">
                                            <thead class="table-light">
                                                <tr class="small text-uppercase">
                                                    <th>Fecha</th>
                                                    <th>Responsable</th>
                                                    <th>Producto</th>
                                                    <th class="text-center">Cant</th>
                                                </tr>
                                            </thead>
                                            <tbody ">${tablaEntregas}</tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
            
            

            <div class="row justify-content-end mt-4">
                <div class="col-5">
                    <table class="table table-sm table-borderless border-top pt-2">
                        <tr>
                            <td class="text-end text-body-secondary">Total Venta:</td>
                            <td class="text-end fw-bold">${total}</td>
                        </tr>
                        <tr>
                            <td class="text-end text-body-secondary">Saldo Pendiente:</td>
                            <td class="text-end fw-bold text-danger">${saldo}</td>
                        </tr>
                    </table>
                </div>
            </div>


</div>
                       

                        

                    </div>
             <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"><\/script> 
                <script>
   window.addEventListener('DOMContentLoaded', () => {
        // 1. Detectar si el usuario está en un dispositivo móvil
        const esMovil = /Android|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);

        // 2. Esperar 1 segundo a que carguen estilos, fuentes e imágenes
        setTimeout(() => {
            if (esMovil) {
                // --- COMPORTAMIENTO EN CELULARES: DESCARGA DE PDF AUTOMÁTICA ---
                const elementoParaConvertir = document.getElementById('areaImpresion');

                const opciones = {
                    margin:       1,
                    filename:     'expediente_${folio}.pdf',
                    image:        { type: 'jpeg', quality: 0.98 },
                    html2canvas:  { scale: 2, useCORS: true }, // Mayor calidad visual
                    jsPDF:        { unit: 'cm', format: 'letter', orientation: 'portrait' }
                };

                // Generar y descargar el PDF directamente
                html2pdf().set(opciones).from(elementoParaConvertir).save();
                
            } else {
                // --- COMPORTAMIENTO EN COMPUTADORAS: DIÁLOGO NATIVO DE IMPRESIÓN ---
                window.print();
            }
        }, 1000); // 1000 milisegundos = 1 segundo de espera
    });
 <\/script>
        </body>
        </html>
    `);

                        ventanaImpresion.document.close();
                    }

                </script>

                <!-- ======================================================= -->
                <!-- CARGA AUTOMÁTICA DE PRODUCTOS EN CADA FILA DE VENTA     -->
                <!-- ======================================================= -->
                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        cargarProductosDeTodasLasVentas();
                    });

                    async function cargarProductosDeTodasLasVentas() {
                        const contenedores = document.querySelectorAll('.productos-container[data-venta-id]');

                        // Carga escalonada para no saturar el servidor
                        const tareas = Array.from(contenedores).map((contenedor, index) =>
                            (async () => {
                                await new Promise(r => setTimeout(r, index * 40));
                                const ventaId = contenedor.dataset.ventaId;
                                if (!ventaId) return;

                                try {
                                    const res = await fetch(
                                        `/cfsistem/app/controllers/historialPedidosVendedorController.php?action=obtenerDetalle&id=${ventaId}`
                                    );
                                    const data = await res.json();
                                    contenedor.innerHTML = renderProductosInline(data.productos);
                                } catch (e) {
                                    console.error('Error cargando productos de venta', ventaId, e);
                                    contenedor.innerHTML =
                                        '<div class="text-danger small p-2"><i class="bi bi-exclamation-triangle"></i> No se pudieron cargar los productos.</div>';
                                }
                            })()
                        );

                        await Promise.all(tareas);
                    }

                    function renderProductosInline(productos) {
                        if (!productos || productos.length === 0) {
                            return '<div class="text-muted small p-2"><i class="bi bi-inbox"></i> Sin productos registrados</div>';
                        }

                        const rows = productos.map(p => {
                            const cant = parseFloat(p.cantidad) || 0;
                            const entregada = parseFloat(p.cantidad_entregada) || 0;
                            const pendiente = Math.max(0, cant - entregada);
                            const factor = parseFloat(p.factor_conversion) || 1;
                            const uRep = p.unidad_reporte || p.unidad_medida || '';
                            const uMed = p.unidad_medida || '';

                            // ---- Vendido ----
                            let vendidoTxt;
                            if (factor > 1 && cant >= factor) {
                                const um = cant / factor;
                                vendidoTxt = `<b>${Number.isInteger(um) ? um : um.toFixed(2)}</b> ${uRep}
                                              <small class="text-muted">(${cant} ${uMed})</small>`;
                            } else {
                                vendidoTxt = `${cant} ${uMed}`;
                            }

                            // ---- Entregado ----
                            let entregadoTxt;
                            if (factor > 1 && entregada >= factor) {
                                const um = entregada / factor;
                                entregadoTxt = `<b>${Number.isInteger(um) ? um : um.toFixed(2)}</b> ${uRep}
                                                <small class="text-muted">(${entregada} ${uMed})</small>`;
                            } else {
                                entregadoTxt = `${entregada} ${uMed}`;
                            }

                            // ---- Pendiente ----
                            let pendienteTxt;
                            if (factor > 1 && pendiente >= factor) {
                                const um = pendiente / factor;
                                pendienteTxt = `<b>${Number.isInteger(um) ? um : um.toFixed(2)}</b> ${uRep}
                                                <small class="text-muted">(${pendiente.toFixed(2)} ${uMed})</small>`;
                            } else {
                                pendienteTxt = `${pendiente.toFixed(2)} ${uMed}`;
                            }

                            // ---- Estado ----
                            let estado;
                            if (pendiente <= 0.001) {
                                estado = '<span class="badge bg-success">Entregado</span>';
                            } else if (entregada > 0.001) {
                                estado = '<span class="badge bg-warning text-dark">Parcial</span>';
                            } else {
                                estado = '<span class="badge bg-secondary">Pendiente</span>';
                            }

                            return `<tr>
                                <td class="fw-semibold text-dark">${p.producto}</td>
                                <td class="text-center">${vendidoTxt}</td>
                                <td class="text-center text-success">${entregadoTxt}</td>
                                <td class="text-center ${pendiente > 0.001 ? 'text-danger fw-bold' : 'text-success fw-semibold'}">${pendienteTxt}</td>
                                <td class="text-center">${estado}</td>
                            </tr>`;
                        }).join('');

                        return `
                            <div class="d-flex align-items-center mb-2 pt-1">
                                <i class="bi bi-box-seam text-primary me-2"></i>
                                <span class="fw-bold small text-uppercase text-primary" style="letter-spacing:0.5px;">
                                    Productos de esta venta
                                </span>
                            </div>
                            <table class="table table-sm table-borderless mb-0">
                                <thead>
                                    <tr>
                                        <th>Producto</th>
                                        <th class="text-center">Vendido</th>
                                        <th class="text-center">Entregado</th>
                                        <th class="text-center">Pendiente</th>
                                        <th class="text-center">Estado</th>
                                    </tr>
                                </thead>
                                <tbody>${rows}</tbody>
                            </table>
                        `;
                    }
                </script>
                <script>
                    function imprimirEstadoCuenta() {
                        // ─────────────────────────────────────────────────
                        // 1. EXTRAER DATOS DEL DOM
                        // ─────────────────────────────────────────────────
                        const header = document.querySelector('.header-expediente');
                        const cliente = header.querySelector('h4')?.textContent.trim() || '';
                        const rfc = header.querySelector('.badge')?.textContent.replace(/RFC:\s*/i, '').trim() || '';

                        const fechaInicio = document.getElementById('fecha_inicio')?.value || '';
                        const fechaFin = document.getElementById('fecha_fin')?.value || '';

                        const _fmt = (s) => {
                            if (!s) return '';
                            const p = s.split('-');
                            return p.length === 3 ? `${p[2]}/${p[1]}/${p[0]}` : s;
                        };

                        // ── KPIs ──
                        const kpis = [];
                        document.querySelectorAll('.kpi-widget').forEach((k) => {
                            const label = k.querySelector('.kpi-label')?.textContent.trim() || '';
                            let value = k.querySelector('.kpi-value')?.textContent.trim() || '';
                            if (!value) {
                                // KPI Estatus (widget con canvas)
                                value = k.querySelector('.text-end .fw-bold')?.textContent.trim() || '';
                            }
                            let tipo = 'primary';
                            if (k.classList.contains('border-left-success')) tipo = 'success';
                            else if (k.classList.contains('border-left-danger')) tipo = 'danger';
                            else if (k.classList.contains('border-left-info')) tipo = 'info';
                            if (label) kpis.push({ label, value, tipo });
                        });

                        // ── Ventas + productos ──
                        const ventas = [];
                        document.querySelectorAll('.venta-row').forEach((row) => {
                            const tds = row.querySelectorAll('td');
                            if (tds.length < 6) return;

                            const badge = tds[5].querySelector('.badge');
                            let estadoClass = '';
                            if (badge) {
                                if (badge.classList.contains('bg-success')) estadoClass = 'liquidado';
                                else if (badge.classList.contains('bg-danger')) estadoClass = 'pendiente';
                                else if (badge.classList.contains('bg-info')) estadoClass = 'favor';
                                else if (badge.classList.contains('bg-secondary')) estadoClass = 'cancelada';
                            }

                            const venta = {
                                fecha: tds[0].textContent.trim(),
                                folio: tds[1].textContent.trim(),
                                total: tds[2].textContent.trim(),
                                pagado: tds[3].textContent.trim(),
                                saldo: tds[4].textContent.trim(),
                                estado: badge ? badge.textContent.trim() : '',
                                estadoClass,
                                productos: []
                            };

                            const productosRow = row.nextElementSibling;
                            if (productosRow && productosRow.classList.contains('productos-row')) {
                                const cont = productosRow.querySelector('.productos-container');
                                cont?.querySelectorAll('tbody tr').forEach((tr) => {
                                    const c = tr.querySelectorAll('td');
                                    if (c.length < 5) return;
                                    venta.productos.push({
                                        producto: c[0].querySelector('.fw-semibold')?.textContent.trim() || c[0].textContent.trim(),
                                        vendido: c[1].innerHTML.trim(),
                                        entregado: c[2].innerHTML.trim(),
                                        pendiente: c[3].innerHTML.trim(),
                                        estado: c[4].textContent.trim()
                                    });
                                });
                            }
                            ventas.push(venta);
                        });

                        // ── Saldo a favor (banner) ──
                        const alertFavor = document.querySelector('.alert.alert-info');
                        const saldoFavorMsg = alertFavor ? alertFavor.textContent.replace(/\s+/g, ' ').trim() : '';

                        // ─────────────────────────────────────────────────
                        // 2. GENERAR HTML CON CSS PURO
                        // ─────────────────────────────────────────────────
                        const fechaGen = new Date().toLocaleString('es-MX', { dateStyle: 'long', timeStyle: 'short' });

                        const kpisHTML = kpis.map(k => `
        <div class="kpi kpi--${k.tipo}">
            <div class="kpi__lbl">${k.label}</div>
            <div class="kpi__val">${k.value}</div>
        </div>
    `).join('');

                        const ventasHTML = ventas.map(v => {
                            const productosHTML = v.productos.length ? `
            <table class="tbl-prod">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th class="tc">Vendido</th>
                        <th class="tc">Entregado</th>
                        <th class="tc">Pendiente</th>
                        <th class="tc">Estado</th>
                    </tr>
                </thead>
                <tbody>
                    ${v.productos.map(p => `
                        <tr>
                            <td class="prod-name">${p.producto}</td>
                            <td class="tc">${p.vendido}</td>
                            <td class="tc">${p.entregado}</td>
                            <td class="tc">${p.pendiente}</td>
                            <td class="tc">${p.estado}</td>
                        </tr>
                    `).join('')}
                </tbody>
            </table>` : `<div class="empty">Sin productos registrados</div>`;

                            return `
        <article class="venta venta--${v.estadoClass}">
            <header class="venta__head">
                <div>
                    <span class="venta__folio">Folio: ${v.folio}</span>
                    <span class="venta__fecha">${v.fecha}</span>
                </div>
                <span class="pill pill--${v.estadoClass}">${v.estado}</span>
            </header>
            <div class="venta__nums">
                <span>Facturado: <b>${v.total}</b></span>
                <span>Abonado: <b class="c-success">${v.pagado}</b></span>
                <span>Saldo: <b class="${v.estadoClass === 'favor' ? 'c-info' : 'c-danger'}">${v.saldo}</b></span>
            </div>
            <div class="venta__prods">${productosHTML}</div>
        </article>`;
                        }).join('');

                        const html = `<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Estado de Cuenta · ${cliente}</title>
<style>
    *,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
    body{font-family:'Segoe UI',system-ui,-apple-system,sans-serif;color:#2c3038;background:#fff;
         font-size:12px;line-height:1.45;padding:26px 32px}

    /* ── Encabezado ── */
    .head{display:flex;justify-content:space-between;align-items:flex-end;flex-wrap:wrap;gap:14px;
          padding-bottom:14px;border-bottom:3px solid #007aff;margin-bottom:24px}
    .head__brand h1{font-size:22px;color:#007aff;letter-spacing:.5px}
    .head__brand small{color:#8b93a4;font-size:11px;display:block;margin-top:2px}
    .head__meta{text-align:right}
    .head__meta .cliente{font-size:15px;font-weight:700;color:#1b1f27}
    .head__meta .rfc{font-size:11px;color:#6b7280;margin-top:2px}
    .head__meta .periodo{font-size:10.5px;color:#9aa1af;margin-top:3px}

    /* ── Banner saldo a favor ── */
    .banner-favor{background:#e8f5fd;border-left:4px solid #3abaf4;color:#1a7fa4;
                  padding:11px 16px;border-radius:6px;margin-bottom:22px;font-size:11.5px;font-weight:600}

    /* ── KPIs ── */
    .kpis{display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:26px}
    .kpi{border:1px solid #e6e9f0;border-left:4px solid #007aff;border-radius:7px;
         padding:11px 14px;background:#fafbfd}
    .kpi--success{border-left-color:#1cc88a}
    .kpi--danger {border-left-color:#e74a3b}
    .kpi--info   {border-left-color:#3abaf4}
    .kpi__lbl{font-size:9px;text-transform:uppercase;letter-spacing:.7px;font-weight:800;color:#8a92a3}
    .kpi__val{font-size:16px;font-weight:700;color:#1b1f27;margin-top:4px}

    /* ── Título de sección ── */
    .section-title{font-size:12.5px;font-weight:800;color:#007aff;text-transform:uppercase;
                   letter-spacing:.7px;margin:22px 0 12px;padding-bottom:6px;border-bottom:1px solid #dfe3ea}

    /* ── Bloque venta ── */
    .venta{border:1px solid #e6e9f0;border-radius:8px;margin-bottom:16px;overflow:hidden;
           page-break-inside:avoid;break-inside:avoid}
    .venta__head{display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:8px;
                 padding:10px 14px;background:#f4f6fa;border-left:4px solid #e74a3b}
    .venta--liquidado .venta__head{border-left-color:#1cc88a;background:#f3fcf8}
    .venta--favor     .venta__head{border-left-color:#3abaf4;background:#f3faff}
    .venta--cancelada .venta__head{border-left-color:#8790a1;background:#f6f7fa}
    .venta__folio{font-weight:700;color:#1b1f27;font-size:12px}
    .venta__fecha{color:#8a92a3;font-size:10.5px;margin-left:12px}

    .pill{display:inline-block;padding:3px 11px;border-radius:12px;font-size:9px;font-weight:800;
          text-transform:uppercase;letter-spacing:.7px}
    .pill--pendiente{background:#fbe0dd;color:#a32b1e}
    .pill--liquidado{background:#d4f5e5;color:#0d7a4e}
    .pill--favor    {background:#d4effc;color:#1a7fa4}
    .pill--cancelada{background:#e8e9ee;color:#4e5665}

    .venta__nums{display:flex;gap:22px;flex-wrap:wrap;padding:10px 14px;font-size:11.5px;
                 border-bottom:1px solid #eef0f5;background:#fff}
    .venta__nums b{color:#1b1f27}
    .c-success{color:#1cc88a!important}
    .c-danger {color:#e74a3b!important}
    .c-info   {color:#3abaf4!important}

    .venta__prods{padding:0 10px 10px;background:#fafbfd}

    /* ── Tabla productos ── */
    .tbl-prod{width:100%;border-collapse:collapse;font-size:10.5px;background:#fff;
              border-radius:5px;overflow:hidden}
    .tbl-prod thead th{background:#eef2f7;text-align:left;padding:6px 10px;font-size:9px;
                       text-transform:uppercase;letter-spacing:.5px;color:#5a6272;font-weight:700}
    .tbl-prod tbody td{padding:6px 10px;border-top:1px solid #f0f2f7;color:#495261;vertical-align:middle}
    .tbl-prod tbody tr:first-child td{border-top:none}
    .prod-name{font-weight:600;color:#1b1f27}
    .tc{text-align:center}
    .empty{padding:10px;color:#9aa1af;font-style:italic;text-align:center;font-size:10.5px}

    /* ── Compatibilidad con HTML inyectado (badges y textos) ── */
    b{font-weight:700;color:#1b1f27}
    small{font-size:.85em}
    .text-muted{color:#8a92a3}
    .text-danger{color:#e74a3b}
    .text-success{color:#1cc88a}
    .text-primary{color:#007aff}
    .text-warning{color:#c68a00}
    .badge{display:inline-block;padding:2px 8px;border-radius:10px;font-size:9px;font-weight:700}
    .badge.bg-success  {background:#d4f5e5;color:#0d7a4e}
    .badge.bg-warning  {background:#fff3cd;color:#7a5e00}
    .badge.bg-secondary{background:#e8e9ee;color:#4e5665}
    .badge.bg-danger   {background:#fbe0dd;color:#a32b1e}
    .badge.bg-info     {background:#d4effc;color:#1a7fa4}

    /* ── Footer ── */
    .footer{margin-top:30px;padding-top:12px;border-top:1px solid #dfe3ea;
            text-align:center;font-size:9.5px;color:#9aa1af}
    .footer strong{color:#007aff}

    /* ── Print ── */
    @page{margin:12mm}
    @media print{
        body{padding:0;font-size:11px}
        .venta{page-break-inside:avoid;break-inside:avoid}
        .kpi{background:#fff}
    }
</style>
</head>
<body>

<header class="head">
    <div class="head__brand">
        <h1>CF SYSTEM</h1>
        <small>Estado de Cuenta del Cliente</small>
    </div>
    <div class="head__meta">
        <div class="cliente">${cliente}</div>
        <div class="rfc">RFC: ${rfc}</div>
        <div class="periodo">Periodo: ${_fmt(fechaInicio)} &mdash; ${_fmt(fechaFin)}</div>
    </div>
</header>

${saldoFavorMsg ? `<div class="banner-favor">💰 ${saldoFavorMsg}</div>` : ''}

<section class="kpis">${kpisHTML}</section>

<h2 class="section-title">Folios Detallados</h2>
${ventasHTML || '<div class="empty">No hay folios en este periodo</div>'}

<footer class="footer">
    Documento generado el ${fechaGen} &middot; <strong>CF SYSTEM</strong>
</footer>

<script>
    window.addEventListener('load', () => {
        const esMovil = /Android|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
        setTimeout(() => {
            if (esMovil) {
                const s = document.createElement('script');
                s.src = 'https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js';
                s.onload = () => html2pdf().set({
                    margin: 1,
                    filename: 'estado_cuenta.pdf',
                    image: { type: 'jpeg', quality: 0.98 },
                    html2canvas: { scale: 2, useCORS: true },
                    jsPDF: { unit: 'cm', format: 'letter', orientation: 'portrait' }
                }).from(document.body).save();
                document.head.appendChild(s);
            } else {
                window.print();
            }
        }, 400);
    });
<\/script>
</body>
</html>`;

                        const win = window.open('', '_blank');
                        win.document.write(html);
                        win.document.close();
                    }
                </script>

</body>

</html>