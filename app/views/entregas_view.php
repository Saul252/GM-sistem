<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Despacho de Materiales (Patio) | Sistema</title>

    <link rel="icon" type="image/png" href="/cfsistem/public/assets/logo.png">
    <link rel="shortcut icon" href="/cfsistem/public/assets/logo.ico" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

    <?php require_once __DIR__ . '/layout/icono.php' ?>
    <?php if (function_exists('cargarEstilos')) {
        cargarEstilos();
    } ?>

    <style>
        :root {
            --sidebar-width: 260px;
            --navbar-height: 65px;
            --apple-bg: #f5f5f7;
            --accent-blue: #007aff;
        }

        body {
            background-color: var(--apple-bg);
            font-family: 'SF Pro Display', -apple-system, sans-serif;
        }

        .main-content {
            margin-left: var(--sidebar-width);
            padding: 40px;
            padding-top: calc(var(--navbar-height) + 20px);
        }

        .card-premium {
            border: none;
            border-radius: 20px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.04);
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
        }

        .badge-ubicacion {
            background-color: #f2f2f7;
            color: #1d1d1f;
            border: 1px solid #d1d1d6;
            padding: .4rem .7rem;
            font-size: .75rem;
            font-weight: 600;
            border-radius: 8px;
        }

        .dataTables_wrapper .pagination .page-item.active .page-link {
            background-color: var(--accent-blue);
            border-color: var(--accent-blue);
            border-radius: 8px;
        }

        .table thead th {
            background: #fbfbfd;
            font-size: .75rem;
            text-transform: uppercase;
            letter-spacing: .05em;
            color: #86868b;
            border-bottom: 1px solid #d1d1d6;
        }

        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
                padding: 20px;
                padding-top: 90px;
            }
        }

        .form-check-input:checked {
            background-color: var(--accent-blue);
            border-color: var(--accent-blue);
        }

        .form-switch .form-check-input {
            width: 2.5em;
            cursor: pointer;
        }

        .transition-ios:active {
            transform: scale(0.95);
            opacity: 0.8;
        }

        .opacity-75-hover:hover {
            opacity: 0.7;
        }

        @media print {
            body * {
                visibility: hidden !important;
            }

            #modalSimulacion,
            #modalSimulacion * {
                visibility: visible !important;
            }

            #modalSimulacion {
                position: absolute !important;
                left: 0 !important;
                top: 0 !important;
                width: 100% !important;
                height: auto !important;
                background: white !important;
                margin: 0 !important;
                padding: 0 !important;
                overflow: visible !important;
            }

            #modalSimulacion .modal-dialog {
                max-width: 100% !important;
                width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
            }

            #modalSimulacion .modal-content {
                border: none !important;
                box-shadow: none !important;
                border-radius: 0 !important;
                background: #fff !important;
            }

            #modalSimulacion .modal-header,
            #modalSimulacion .modal-footer,
            #btnImprimirModal,
            #btnConfirmarFinal,
            .btn-close {
                display: none !important;
            }

            #documentoPatio {
                padding: 20px !important;
                font-size: 12px !important;
                color: #000 !important;
            }

            table {
                width: 100% !important;
                border-collapse: collapse !important;
            }

            table th,
            table td {
                border: 1px solid #000 !important;
                padding: 6px !important;
            }
        }
    </style>
</head>

<body>

    <?php renderizarLayout($paginaActual); ?>

    <div class="main-content">
        <div class="container-fluid">

            <div class="d-flex justify-content-between align-items-end mb-4">
                <div>
                    <h2 class="fw-bold m-0">Despacho de Materiales</h2>
                    <p class="text-body-secondary small">Control físico de lotes y entregas en patio</p>
                </div>
                <div id="loader" class="spinner-border text-primary d-none" role="status"></div>
            </div>

            <div class="row g-3 mt-2" id="widgetGanancias">
                <div class="col-md-3">
                    <div class="card shadow-sm border-0 rounded-4 p-3 text-center">
                        <div class="small text-body-secondary">Unidades</div>
                        <div class="fs-4 fw-bold" id="w_unidades">0</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card shadow-sm border-0 rounded-4 p-3 text-center">
                        <div class="small text-body-secondary">Costo Total</div>
                        <div class="fs-4 fw-bold text-danger" id="w_costo">$0</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card shadow-sm border-0 rounded-4 p-3 text-center">
                        <div class="small text-body-secondary">Ventas</div>
                        <div class="fs-4 fw-bold text-primary" id="w_venta">$0</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card shadow-sm border-0 rounded-4 p-3 text-center">
                        <div class="small text-body-secondary">Ganancia</div>
                        <div class="fs-4 fw-bold" id="w_ganancia">$0</div>
                    </div>
                </div>
            </div>

            <div class="card card-custom mb-4">
                <div class="card-body p-4">
                    <form id="formFiltros" class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-body-secondary">PERIODO</label>
                            <select id="selectorPeriodo" class="form-select border-0">
                                <option value="hoy" selected>Hoy</option>
                                <option value="ayer">Ayer</option>
                                <option value="semana">Últimos 7 días</option>
                                <option value="mes">Este Mes</option>
                                <option value="personalizado">📅 Rango Manual</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small fw-bold text-body-secondary">DESDE</label>
                            <input type="date" id="f_inicio" class="form-control input-disabled" disabled>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small fw-bold text-body-secondary">HASTA</label>
                            <input type="date" id="f_fin" class="form-control input-disabled" disabled>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-body-secondary">ALMACÉN</label>
                            <select id="filtroAlmacen" class="form-select border-0" <?= ($almacen_usuario > 0) ? 'disabled' : '' ?>>
                                <?php if ($almacen_usuario == 0): ?>
                                    <option value="0">-- Todos los Almacenes --</option>
                                    <?php
                                    $q_alm = $conexion->query("SELECT id, nombre FROM almacenes WHERE activo = 1 ORDER BY nombre");
                                    while ($a = $q_alm->fetch_assoc()): ?>
                                        <option value="<?= $a['id'] ?>"><?= $a['nombre'] ?></option>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <?php $res_mio = $conexion->query("SELECT nombre FROM almacenes WHERE id = $almacen_usuario LIMIT 1")->fetch_assoc(); ?>
                                    <option value="<?= $almacen_usuario ?>" selected>📍 <?= $res_mio['nombre'] ?></option>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <div class="form-check form-switch pt-2">
                                <input class="form-check-input" type="checkbox" id="checkAgruparVenta">
                                <label class="form-check-label small fw-bold text-primary" for="checkAgruparVenta">
                                    <i class="bi bi-layers-half me-1"></i> AGRUPAR POR VENTA
                                </label>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <button type="button" id="btnReset"
                                class="btn btn-dark w-100 rounded-pill fw-bold">Limpiar</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card card-custom overflow-hidden">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table id="tablaEntregas" class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th class="ps-4">Operación</th>
                                    <th>Folio Venta</th>
                                    <th>Cliente</th>
                                    <th>Fecha</th>
                                    <th>Producto / SKU</th>
                                    <th class="text-center">Cant. Solicitada</th>
                                    <th>Almacén</th>
                                    <th class="text-end pe-4">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="text-secondary" style="font-size: .85rem;"></tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Modal de simulación -->
    <div class="modal fade" id="modalSimulacion" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-dark text-white d-flex justify-content-between align-items-center">
                    <h5 class="modal-title m-0"><i class="bi bi-file-earmark-ruled me-2"></i>Orden de Despacho</h5>
                    <div>
                        <button type="button" class="btn btn-outline-light btn-sm btn-print-action me-2"
                            onclick="window.print()">
                            <i class="bi bi-printer me-1"></i> Imprimir
                        </button>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                </div>
                <div class="modal-body p-4" id="documentoPatio"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" id="btnConfirmarFinal" class="btn btn-primary px-4 fw-bold">
                        <i class="bi bi-check-circle me-1"></i> Generar Entrega
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- ══════════════════════════════════════════════════════════════════ -->
    <!--  PARTIALS (HTML de modales) — se cargan ANTES de las librerías JS -->
    <!--  para que si alguna incluye su propio jQuery, DataTables se      -->
    <!--  enganche al jQuery final.                                       -->
    <!-- ══════════════════════════════════════════════════════════════════ -->


    <!-- ══════════════════════════════════════════════════════════════════ -->
    <!--  LIBRERÍAS JS                                                    -->
    <!-- ══════════════════════════════════════════════════════════════════ -->
    <!-- ══════════════════════════════════════════════════════════════════ -->
    <!--  LIBRERÍAS JS (intento normal)                                   -->
    <!-- ══════════════════════════════════════════════════════════════════ -->
    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- ══════════════════════════════════════════════════════════════════ -->
    <!--  FALLBACK CONDICIONAL — solo carga lo que falte (solo esta vista)-->
    <!--  Usa document.write durante el parseo, así mantiene el orden    -->
    <!--  síncrono y jQuery queda disponible antes del script principal. -->
    <!-- ══════════════════════════════════════════════════════════════════ -->
    <script>
        if (typeof window.jQuery === 'undefined') {
            document.write('<script src="https://code.jquery.com/jquery-3.7.0.min.js"><\/script>');
        }
    </script>
    <script>
        if (window.jQuery && typeof window.jQuery.fn.DataTable === 'undefined') {
            document.write('<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"><\/script>');
        }
    </script>
    <script>
        if (window.jQuery && window.jQuery.fn.DataTable && typeof window.jQuery.fn.dataTable === 'undefined') {
            document.write('<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"><\/script>');
        }
    </script>
    <script>
        if (typeof window.bootstrap === 'undefined') {
            document.write('<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"><\/script>');
        }
    </script>
    <script>
        if (typeof window.Toastify === 'undefined') {
            document.write('<script src="https://cdn.jsdelivr.net/npm/toastify-js"><\/script>');
        }
    </script>
    <script>
        if (typeof window.Swal === 'undefined') {
            document.write('<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"><\/script>');
        }
    </script>

    <!-- ══════════════════════════════════════════════════════════════════ -->
    <!--  SCRIPT PRINCIPAL (se ejecuta al final, con todo garantizado)    -->
    <!-- ══════════════════════════════════════════════════════════════════ -->
    <script>
        jQuery(function ($) {
            if (typeof $.fn.DataTable !== 'function') {
                console.error('❌ DataTables sigue sin cargar. Revisa la consola de red.');
                return;
            }
            /* ... el resto de tu lógica ... */
        });
    </script>
    <!-- ══════════════════════════════════════════════════════════════════ -->
    <!--  SCRIPT PRINCIPAL                                                -->
    <!-- ══════════════════════════════════════════════════════════════════ -->
    <script>
        jQuery(function ($) {

            // ─── Salvaguarda: verificar que DataTables se haya cargado ────────
            if (typeof $.fn.DataTable !== 'function') {
                console.error('❌ DataTables no está disponible. Revisa que ninguna partial esté recargando jQuery después de DataTables.');
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error de librería',
                        text: 'DataTables no se cargó correctamente. Revisa la consola del navegador.'
                    });
                }
                return; // detiene la ejecución
            }

            let movimientoActualID = null;

            const tabla = $('#tablaEntregas').DataTable({
                language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json' },
                dom: '<"d-flex justify-content-between p-3 border-bottom"f>rt<"p-3"ip>',
                order: [[1, 'desc']],
                pageLength: 20
            });

            function formatQty(cantidad, factor, unidad, unidad_medida) {
                const cant = parseFloat(cantidad);
                const fac = parseFloat(factor || 1);
                if (fac > 1 && cant >= fac) {
                    const uReporte = Math.floor(cant / fac);
                    const resto = Math.round((cant % fac) * 100) / 100;
                    return `<div class="fw-bold fs-6">${uReporte} ${unidad}</div>` +
                        (resto > 0 ? `<small class="text-body-secondary">+ ${resto} ${unidad_medida}</small>` : '');
                }
                return `<div class="fw-bold fs-6">${cant} <small class="fw-normal text-body-secondary">${unidad_medida}</small></div>`;
            }

            function renderWidgetGanancias(data) {
                const f = new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' });
                $('#w_unidades').text(data.total_unidades || 0);
                $('#w_costo').text(f.format(data.costo_total || 0));
                $('#w_venta').text(f.format(data.total_venta || 0));
                const ganancia = data.ganancia_total || 0;
                $('#w_ganancia')
                    .text(f.format(ganancia))
                    .removeClass('text-success text-danger')
                    .addClass(ganancia >= 0 ? 'text-success' : 'text-danger');
            }

            function cargarEntregas() {
                $('#loader').removeClass('d-none');
                const agrupar = $('#checkAgruparVenta').is(':checked');

                $.ajax({
                    url: 'entregasController.php',
                    data: {
                        ajax: 'listar',
                        periodo: $('#selectorPeriodo').val(),
                        f_inicio: $('#f_inicio').val(),
                        f_fin: $('#f_fin').val(),
                        almacen_id: $('#filtroAlmacen').val()
                    },
                    dataType: 'json',
                    success: function (res) {
                        tabla.clear();
                        if (!res.data) { tabla.draw(); return; }

                        let datosAMostrar = res.data;
                        renderWidgetGanancias(res.ganancias);

                        if (agrupar) {
                            const grupos = {};
                            res.data.forEach(item => {
                                const folio = item.folio_venta || 'SIN-FOLIO';
                                if (!grupos[folio]) {
                                    grupos[folio] = {
                                        ...item,
                                        cliente: item.cliente || 'Público General',
                                        total_items: 0,
                                        items_despachados: 0,
                                        items_en_ruta: 0,
                                        items_completados: 0,
                                        ids_movimientos: []
                                    };
                                }
                                grupos[folio].total_items++;
                                if (parseInt(item.ya_despachado) === 1) grupos[folio].items_despachados++;
                                if (item.estado_reparto === 'en_transito') grupos[folio].items_en_ruta++;
                                if (item.estado_reparto === 'completado') grupos[folio].items_completados++;
                                grupos[folio].ids_movimientos.push(item.id);
                            });
                            datosAMostrar = Object.values(grupos);
                        }

                        datosAMostrar.forEach(m => {
                            let accionHtml = '', prodCol = '', cantCol = '';

                            if (agrupar && m.total_items > 1) {
                                const todoDespachado = (m.total_items === m.items_despachados);
                                const todoCompletado = (m.total_items === m.items_completados);
                                const algoEnRuta = (m.items_en_ruta > 0);

                                cantCol = `<div class="text-center text-body-secondary small">${m.total_items} Artículos</div>`;
                                prodCol = `<b>Venta Consolidada</b><br><small class="text-body-secondary">Folio: ${m.folio_venta}</small>`;

                                if (todoCompletado) {
                                    accionHtml = `
                                <div class="card border-0 shadow-sm mb-3" style="border-radius:18px;background:rgba(255,255,255,.8);backdrop-filter:blur(15px);border:1px solid rgba(255,255,255,.4)!important;padding:12px 16px;">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="d-flex align-items-center" style="gap:15px;">
                                            <button class="btn d-flex align-items-center p-0 transition-ios" style="background:transparent;border:none;color:#8e8e93;font-weight:600;font-size:.65rem;letter-spacing:.5px;" onclick="verDetalleGananciaVenta(${m.venta_id})">
                                                <i class="bi bi-shield-check me-1" style="font-size:.9rem;"></i> AUDITORÍA
                                            </button>
                                            <button class="btn d-flex align-items-center p-0 transition-ios" style="background:transparent;border:none;color:#007aff;font-weight:600;font-size:.65rem;letter-spacing:.5px;" onclick="verDetalleDespachoAlmacen(${m.venta_id})">
                                                <i class="bi bi-box-seam me-1" style="font-size:.9rem;"></i> LOGÍSTICA
                                            </button>
                                        </div>
                                        <div class="d-flex align-items-center px-3" style="background:rgba(52,199,89,.12);color:#248a3d;border:1px solid rgba(52,199,89,.2);border-radius:20px;height:28px;">
                                            <i class="bi bi-check-circle-fill me-1" style="font-size:.75rem;"></i>
                                            <span style="font-size:.6rem;font-weight:800;letter-spacing:.5px;">ENTREGADO</span>
                                        </div>
                                    </div>
                                </div>`;
                                } else if (algoEnRuta) {
                                    accionHtml = `
                                <div class="text-end pe-3">
                                    <span class="badge rounded-pill p-2 px-3" style="background:rgba(255,149,0,.1);color:#ff9500;border:1px solid #ff9500;">
                                        <i class="bi bi-truck me-1"></i> MERCANCÍA EN TRÁNSITO
                                    </span>
                                </div>
                                <button onclick="verDetalleGanancia(${m.id})" class="btn btn-link ms-2 text-decoration-none" style="color:#ceced2;">
                                    <i class="bi bi-graph-up-arrow fs-6"></i>
                                </button>`;
                                } else if (todoDespachado) {
                                    accionHtml = `
                                <div class="d-flex align-items-center gap-2 py-1">
                                    <button onclick="abrirModalDespachoVentaGfin(${m.venta_id}, ${m.almacen_origen_id})" class="btn d-flex align-items-center justify-content-center shadow-sm transition-ios" style="background:rgba(0,122,255,.1);color:#007aff;border:1px solid rgba(0,122,255,.2);border-radius:12px;font-weight:700;height:32px;padding:0 15px;">
                                        <i class="bi bi-geo-alt-fill me-2" style="font-size:.8rem;"></i>
                                        <span style="font-size:.65rem;letter-spacing:.3px;text-transform:uppercase;">Destino Entrega</span>
                                    </button>
                                    <button class="btn d-flex align-items-center opacity-75-hover" style="background:transparent;border:none;color:#8e8e93;font-weight:600;font-size:.7rem;letter-spacing:.5px;" onclick="verDetalleGananciaVenta(${m.venta_id})">
                                        <i class="bi bi-shield-check me-1" style="font-size:.9rem;"></i> AUDITORÍA
                                    </button>
                                </div>`;
                                } else {
                                    accionHtml = `
                                <div class="text-end pe-3">
                                    <button class="btn btn-sm rounded-pill btn-dark px-4 shadow-sm" onclick="abrirModalDespachoVentaTotal(${m.venta_id},${m.almacen_origen_id})">
                                        <i class="bi bi-list-check me-1"></i> GESTIONAR VENTA
                                    </button>
                                </div>`;
                                }
                            } else {
                                const yaDespachado = (parseInt(m.ya_despachado) === 1);
                                const enRuta = (m.estado_reparto === 'en_transito');
                                const completado = (m.estado_reparto === 'completado');

                                if (completado || enRuta) {
                                    const color = completado ? '#28a745' : '#ff9500';
                                    const texto = completado ? 'MATERIAL ENTREGADO' : 'MERCANCÍA EN TRÁNSITO';
                                    accionHtml = `
                                <div class="d-flex align-items-center justify-content-end pe-3 py-1">
                                    <span class="fw-bold me-3" style="color:${color};font-size:.7rem;">${texto}</span>
                                    <button onclick="imprimirComprobante(${m.id})" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                        <i class="bi bi-printer"></i>
                                    </button>
                                    <button onclick="verDetalleGanancia(${m.id})" class="btn btn-link ms-2 text-decoration-none" style="color:#ceced2;">
                                        <i class="bi bi-graph-up-arrow fs-6"></i>
                                    </button>
                                </div>`;
                                } else if (yaDespachado) {
                                    accionHtml = `
                                <div class="d-flex align-items-center justify-content-end pe-3 py-1" style="gap:10px;">
                                    <button type="button" class="btn btn-sm d-flex align-items-center opacity-75-hover" style="background:transparent;border:none;color:#ff3b30;font-weight:600;font-size:.7rem;letter-spacing:.5px;" onclick="confirmarReversaDespacho(${m.id})">
                                        <i class="bi bi-arrow-counterclockwise me-1" style="font-size:.9rem;"></i> REVERSAR
                                    </button>
                                    <button onclick="prepararModalPatio(${m.id}, ${m.almacen_origen_id})" class="btn d-flex align-items-center justify-content-center shadow-sm transition-ios" style="background:rgba(0,122,255,.1);color:#007aff;border:1px solid rgba(0,122,255,.2);border-radius:12px;font-weight:700;height:32px;padding:0 15px;">
                                        <i class="bi bi-box-seam me-2" style="font-size:.8rem;"></i>
                                        <span style="font-size:.65rem;letter-spacing:.3px;">PATIO</span>
                                    </button>
                                    <button onclick="prepararModalReparto(${m.id}, ${m.almacen_origen_id})" class="btn d-flex align-items-center justify-content-center shadow-sm transition-ios" style="background:#1c1c1e;color:#fff;border:none;border-radius:12px;font-weight:700;height:32px;padding:0 15px;">
                                        <i class="bi bi-truck me-2" style="font-size:.8rem;"></i>
                                        <span style="font-size:.65rem;letter-spacing:.3px;">RUTA</span>
                                    </button>
                                </div>`;
                                } else {
                                    accionHtml = `
                                `;
                                }
                                prodCol = `<b>${m.producto}</b><br><small class="text-primary font-monospace">${m.sku}</small>`;
                                cantCol = `<div class="text-center">${formatQty(m.cantidad, m.factor_conversion, m.unidad_reporte, m.unidad_medida)}</div>`;
                            }

                            tabla.row.add([
                                `<span class="ps-3 fw-bold text-secondary">#${m.id}</span>`,
                                `<span class="fw-bold text-primary">${m.folio_venta || '---'}</span>`,
                                `<span class="text-uppercase fw-semibold" style="font-size:.75rem;">${m.cliente || '---'}</span>`,
                                `<span class="small">${m.fecha_format}</span>`,
                                prodCol,
                                cantCol,
                                `<div><span class="badge text-success border small"><i class="bi bi-geo-alt me-1"></i>${m.origen}</span></div>`,
                                accionHtml
                            ]);
                        });

                        tabla.draw();
                    },
                    complete: () => $('#loader').addClass('d-none')
                });
            }

            // Binding único
            $('#checkAgruparVenta').on('change', cargarEntregas);

            // ─── FASE 1: SIMULACIÓN ────────────────────────────────────────
            window.prepararDespacho = function (id) {
                movimientoActualID = id;
                $('#loader').removeClass('d-none');
                $.getJSON('entregasController.php', { ajax: 'simular', id: id }, function (res) {
                    if (!res.success) { Swal.fire('Atención', res.message, 'error'); return; }

                    const textoTotal = formatQty(res.total_solicitado, res.factor_conversion, res.unidad_reporte, res.unidad_medida);

                    let html = `
                    <div class="text-center mb-4">
                        <h4 class="mb-1 text-uppercase fw-bold">Hoja de Ruta de Patio</h4>
                        <p class="text-body-secondary small">Despacho de Material por Lotes (PEPS)</p>
                        <div class="mt-2">${textoTotal}</div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered align-middle">
                            <thead class="table-light small fw-bold text-uppercase">
                                <tr>
                                    <th>CÓDIGO LOTE</th>
                                    <th>FECHA INGRESO</th>
                                    <th class="text-end">STOCK ACTUAL</th>
                                    <th class="text-end text-primary">A EXTRAER</th>
                                    <th class="text-end">SALDO FINAL</th>
                                </tr>
                            </thead>
                            <tbody style="font-size:.85rem;">`;

                    res.lotes.forEach(l => {
                        html += `
                        <tr>
                            <td><code class="fw-bold">${l.codigo}</code></td>
                            <td>${l.fecha_entrada}</td>
                            <td class="text-end text-body-secondary">${l.cantidad_en_lote} ${l.unidad_medida}</td>
                            <td class="text-end fw-bold text-primary">-${l.cantidad_a_extraer} ${l.unidad_medida}</td>
                            <td class="text-end">${l.saldo_final <= 0 ? '<span class="badge bg-danger">AGOTADO</span>' : l.saldo_final + l.unidad_medida}</td>
                        </tr>`;
                    });

                    html += `</tbody></table></div>`;

                    if (res.pendiente > 0) {
                        html += `<div class="alert alert-danger mt-3 d-flex align-items-center">
                        <i class="bi bi-exclamation-octagon-fill fs-4 me-2"></i>
                        <div><b>Inconsistencia:</b> Stock insuficiente. Faltan ${res.pendiente} ${res.unidad_medida}</div>
                    </div>`;
                        $('#btnConfirmarFinal').prop('disabled', true).addClass('d-none');
                    } else {
                        $('#btnConfirmarFinal').prop('disabled', false).removeClass('d-none');
                    }

                    $('#documentoPatio').html(html);
                    $('#modalSimulacion').modal('show');
                }).always(() => $('#loader').addClass('d-none'));
            };

            // ─── FASE 2: CONFIRMACIÓN ──────────────────────────────────────
            $('#btnConfirmarFinal').on('click', function () {
                Swal.fire({
                    title: '¿Confirmar Entrega?',
                    text: "Se descontará el stock de los lotes y se generará el vale de salida.",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Sí, despachar',
                    confirmButtonColor: '#2563eb'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const btn = $(this);
                        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Procesando...');
                        $.post('entregasController.php', { ajax: 'despachar', id_movimiento: movimientoActualID }, function (res) {
                            $('#modalSimulacion').modal('hide');
                            if (res.success) {
                                Toastify({ text: "🚚 Despacho completado", style: { background: "#10b981" } }).showToast();
                                cargarEntregas();
                            } else {
                                Swal.fire('Error', res.message, 'error');
                            }
                        }, 'json').always(() => {
                            btn.prop('disabled', false).html('<i class="bi bi-check-circle me-1"></i> Generar Entrega');
                        });
                    }
                });
            });

            // ─── EVENTOS DE FILTROS ────────────────────────────────────────
            $('#selectorPeriodo').on('change', function () {
                const isPerso = $(this).val() === 'personalizado';
                $('#f_inicio, #f_fin').prop('disabled', !isPerso).toggleClass('input-disabled', !isPerso);
                if (!isPerso) cargarEntregas();
            });

            $('#f_inicio, #f_fin, #filtroAlmacen').on('change', cargarEntregas);

            $('#btnReset').on('click', () => {
                $('#formFiltros')[0].reset();
                $('#f_inicio, #f_fin').prop('disabled', true).addClass('input-disabled');
                cargarEntregas();
            });

            // ─── VALE DE ENTREGA ───────────────────────────────────────────
            window.imprimirComprobante = function (id) {
                $('#btnConfirmarFinal').addClass('d-none');
                $('#btnImprimirModal').removeClass('d-none');
                $('#loader').removeClass('d-none');

                $.getJSON('entregasController.php', { ajax: 'imprimir', id: id }, function (res) {
                    if (res.success) {
                        const d = res.data;
                        let html = `
                        <div class="text-center mb-4">
                            <h4 class="mb-1 text-uppercase fw-bold">Vale de Entrega (Patio)</h4>
                            <p class="text-body-secondary small">Folio de Movimiento: #<b>${d.movimiento_id}</b></p>
                            <div class="mt-2 text-primary fw-bold">${d.cantidad_convertida}</div>
                        </div>
                        <div class="row g-3 mb-3 small">
                            <div class="col-6">
                                <p class="mb-1"><strong>Fecha Despacho:</strong> ${d.fecha_despacho}</p>
                                <p class="mb-1"><strong>Almacén:</strong> ${d.almacen_origen}</p>
                            </div>
                            <div class="col-6 text-end">
                                <p class="mb-1"><strong>Producto:</strong> ${d.producto}</p>
                                <p class="mb-1"><strong>SKU:</strong> <span class="font-monospace">${d.sku}</span></p>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered align-middle">
                                <thead class="table-light small fw-bold text-uppercase">
                                    <tr><th>CÓDIGO LOTE</th><th class="text-end">DETALLE DE SALIDA</th></tr>
                                </thead>
                                <tbody style="font-size:.85rem;">
                                    <tr>
                                        <td class="py-2 font-monospace">${d.detalle_lotes}</td>
                                        <td class="text-end fw-bold text-primary py-2">${(d.cantidad_total / d.factor_conversion) >= 1 ? (d.cantidad_total / d.factor_conversion) : d.cantidad_total} ${(d.cantidad_total / d.factor_conversion) >= 1 ? d.unidad_reporte : d.unidad_medida}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="row mt-5 pt-4 text-center">
                            <div class="col-6">
                                <div style="border-top:1px solid #dee2e6;width:80%;margin:0 auto;" class="pt-2">
                                    <small class="text-body-secondary d-block">Despachó (Patio)</small>
                                    <strong class="small">${d.usuario_despacho}</strong>
                                </div>
                            </div>
                            <div class="col-6">
                                <div style="border-top:1px solid #dee2e6;width:80%;margin:0 auto;" class="pt-2">
                                    <small class="text-body-secondary d-block">Recibió (Firma)</small><br>
                                </div>
                            </div>
                        </div>`;
                        $('#documentoPatio').html(html);
                        $('#modalSimulacion').modal('show');
                    } else {
                        Swal.fire('Error', res.message, 'error');
                    }
                }).always(() => $('#loader').addClass('d-none'));
            };

            // ─── DETALLE GANANCIA INDIVIDUAL ──────────────────────────────
            window.verDetalleGanancia = function (id) {
                $('#btnConfirmarFinal').addClass('d-none');
                $('#btnImprimirModal').removeClass('d-none');
                $('#loader').removeClass('d-none');

                $.getJSON('/cfsistem/app/controllers/entregasController.php', { ajax: 'imprimirGanancia', id: id }, function (res) {
                    if (!res.success) { Swal.fire('Error de Consulta', res.message, 'error'); return; }

                    const d = res.data;
                    const ganancia = parseFloat(d.ganancia_neta || 0);
                    const colorGanancia = ganancia < 0 ? 'text-danger' : 'text-success';

                    let filasLotes = '';
                    if (d.detalle_financiero) {
                        d.detalle_financiero.split('___').forEach(reg => {
                            const c = reg.split('|');
                            if (c.length === 4) {
                                const cant = parseFloat(c[1] || 0);
                                const cost = parseFloat(c[2] || 0);
                                const prec = parseFloat(c[3] || 0);
                                const subC = cant * cost;
                                const subV = cant * prec;
                                const util = subV - subC;
                                filasLotes += `
                                <tr>
                                    <td class="font-monospace text-start ps-2">${c[0]}</td>
                                    <td>${cant}</td>
                                    <td class="text-end text-body-secondary">$ ${cost.toFixed(2)}</td>
                                    <td class="text-end">$ ${prec.toFixed(2)}</td>
                                    <td class="text-end text-body-secondary">$ ${subC.toFixed(2)}</td>
                                    <td class="text-end fw-bold">$ ${subV.toFixed(2)}</td>
                                    <td class="text-end fw-bold ${util < 0 ? 'text-danger' : 'text-success'}">$ ${util.toFixed(2)}</td>
                                </tr>`;
                            }
                        });
                    }

                    const html = `
                    <div class="text-center mb-4">
                        <div class="badge bg-success mb-2">Reporte Financiero</div>
                        <h4 class="mb-1 text-uppercase fw-bold">Rentabilidad de Venta</h4>
                        <p class="text-body-secondary small">ID Movimiento: #<b>${d.movimiento_id}</b> | Producto: <b>${d.producto || 'N/A'}</b></p>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover table-bordered align-middle text-center">
                            <thead class="table-dark small">
                                <tr>
                                    <th>Lote Origen</th><th>Cant.</th><th>Costo Adq.</th>
                                    <th>Precio Venta</th><th>Inversión</th><th>Ingreso Bruto</th><th>Utilidad</th>
                                </tr>
                            </thead>
                            <tbody style="font-size:.75rem;">
                                ${filasLotes || '<tr><td colspan="7">No hay datos de lotes</td></tr>'}
                            </tbody>
                            <tfoot class="table-info fw-bold">
                                <tr>
                                    <td colspan="4" class="text-end small">RESUMEN DE OPERACIÓN:</td>
                                    <td class="text-end">$ ${parseFloat(d.total_costo || 0).toFixed(2)}</td>
                                    <td class="text-end">$ ${parseFloat(d.total_venta || 0).toFixed(2)}</td>
                                    <td class="text-end ${colorGanancia}" style="font-size:.95rem;">$ ${ganancia.toFixed(2)}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>`;

                    $('#documentoPatio').html(html);
                    $('#modalSimulacion').modal('show');
                }).fail(function (jqXHR, textStatus, errorThrown) {
                    console.error("Error en la petición:", textStatus, errorThrown);
                    Swal.fire('Error', 'No se pudo conectar con el servidor.', 'error');
                }).always(() => $('#loader').addClass('d-none'));
            };

            // ─── AUDITORÍA POR VENTA ──────────────────────────────────────
            window.verDetalleGananciaVenta = function (idVenta) {
                $('#loader').removeClass('d-none');
                $.getJSON('entregasController.php', { ajax: 'obtenerAuditoriaVenta', id_venta: idVenta }, function (res) {
                    if (!res.success) { Swal.fire('Atención', res.message, 'warning'); return; }

                    const r = res.data;
                    const colorGlobal = r.ganancia_neta_total < 0 ? 'text-danger' : 'text-success';

                    let htmlProductos = r.productos.map(p => {
                        const gananciaProd = parseFloat(p.ganancia_prod || 0);
                        const colorProd = gananciaProd < 0 ? 'text-danger' : 'text-success';

                        let filasLotes = '';
                        if (p.detalle_financiero) {
                            p.detalle_financiero.split('___').forEach(reg => {
                                const c = reg.split('|');
                                if (c.length === 4) {
                                    const subU = parseFloat(c[3]) - parseFloat(c[2]);
                                    filasLotes += `
                                    <tr>
                                        <td class="text-start small fw-bold text-secondary">${c[0]}</td>
                                        <td>${c[1]}</td>
                                        <td class="text-end text-body-secondary small">$${parseFloat(c[2]).toFixed(2)}</td>
                                        <td class="text-end small">$${parseFloat(c[3]).toFixed(2)}</td>
                                        <td class="text-end fw-bold ${subU < 0 ? 'text-danger' : 'text-success'}">$${subU.toFixed(2)}</td>
                                    </tr>`;
                                }
                            });
                        }

                        return `
                    <div class="card mb-4 border-0 shadow-sm" style="border-radius:15px;overflow:hidden;">
                        <div class="card-header border-0 py-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="badge bg-dark mb-1">${p.sku}</span>
                                    <h5 class="mb-0 fw-bold">${p.producto}</h5>
                                </div>
                                <div class="text-end">
                                    <small class="text-body-secondary d-block small-caps">UTILIDAD ARTÍCULO</small>
                                    <span class="h5 mb-0 fw-bold ${colorProd}">$ ${gananciaProd.toLocaleString()}</span>
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-sm mb-0 align-middle">
                                <thead class="small text-uppercase text-body-secondary">
                                    <tr>
                                        <th class="ps-3" style="font-size:.65rem;">Lote</th>
                                        <th style="font-size:.65rem;">Cant.</th>
                                        <th class="text-end" style="font-size:.65rem;">Costo U.</th>
                                        <th class="text-end" style="font-size:.65rem;">Venta U.</th>
                                        <th class="text-end pe-3" style="font-size:.65rem;">Margen U.</th>
                                    </tr>
                                </thead>
                                <tbody class="border-top-0" style="font-size:.8rem;">${filasLotes}</tbody>
                            </table>
                        </div>
                    </div>`;
                    }).join('');

                    let htmlFinal = `
                    <div class="px-2">
                        <div class="text-center mb-4 pt-2">
                            <h6 class="text-uppercase text-body-secondary fw-bold" style="letter-spacing:2px;font-size:.7rem;">Auditoría de Rentabilidad</h6>
                            <h3 class="fw-bold mb-0">Folio: ${r.productos[0].folio || 'Venta'}</h3>
                            <hr class="mx-auto" style="width:50px;height:3px;background:#28a745;border:0;opacity:1;">
                        </div>
                        <div class="row g-3 mb-4 text-center">
                            <div class="col-4"><div class="p-3 rounded-4 shadow-sm border"><small class="text-body-secondary d-block small-caps">INVERSIÓN TOTAL</small><span class="h6 fw-bold">$ ${parseFloat(r.gran_total_costo).toLocaleString()}</span></div></div>
                            <div class="col-4"><div class="p-3 rounded-4 shadow-sm border"><small class="text-body-secondary d-block small-caps">INGRESO BRUTO</small><span class="h6 fw-bold">$ ${parseFloat(r.gran_total_venta).toLocaleString()}</span></div></div>
                            <div class="col-4"><div class="p-3 rounded-4 shadow-sm border" style="background:#f0fff4;"><small class="text-body-secondary d-block small-caps">GANANCIA NETA</small><span class="h6 fw-bold ${colorGlobal}">$ ${parseFloat(r.ganancia_neta_total).toLocaleString()}</span></div></div>
                        </div>
                        ${htmlProductos}
                    </div>`;

                    $('#documentoPatio').html(htmlFinal);
                    $('#modalSimulacion').modal('show');
                }).always(() => $('#loader').addClass('d-none'));
            };

            // ─── ORDEN DE DESPACHO (LOGÍSTICA) ────────────────────────────
            window.verDetalleDespachoAlmacen = function (idVenta) {
                $('#loader').removeClass('d-none');
                $.getJSON('entregasController.php', { ajax: 'obtenerAuditoriaVenta', id_venta: idVenta }, function (res) {
                    if (!res.success) { Swal.fire('Atención', res.message, 'warning'); return; }

                    const r = res.data;

                    let htmlProductos = r.productos.map((p, index) => {
                        let totalEntregado = 0;
                        let filasLotes = '';

                        if (p.detalle_financiero) {
                            p.detalle_financiero.split('___').forEach(reg => {
                                const c = reg.split('|');
                                if (c.length >= 2) {
                                    totalEntregado = totalEntregado + (Number(c[1]) || 0);
                                    filasLotes += `
                                    <tr>
                                        <td class="py-1 ps-2">${c[0]}</td>
                                        <td class="text-end py-1 pe-2 fw-semibold">
                                            ${(c[1] / p.factor_conversion) >= 1 ? (c[1] / p.factor_conversion) : c[1]}
                                            ${(c[1] / p.factor_conversion) >= 1 ? p.unidad_reporte : p.unidad_medida || ''}
                                        </td>
                                        <td class="text-end py-1 pe-2 fw-semibold">${c[4]}</td>
                                    </tr>`;
                                }
                            });
                        }

                        return `
                    <div class="mb-3 pb-2" style="border-bottom:1px dashed #999;">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <div class="fw-bold text-uppercase" style="font-size:14px;">${index + 1}. ${p.producto}</div>
                                <div class="text-body-secondary" style="font-size:11px;">SKU: ${p.sku}</div>
                            </div>
                            <div class="text-end">
                                <div class="fw-bold" style="font-size:15px;line-height:1;">
                                    Venta Total:
                                    ${(p.cantidad_total / p.factor_conversion) > 1 ? (p.cantidad_total / p.factor_conversion) : p.cantidad_total}
                                    ${(p.cantidad_total / p.factor_conversion) > 1 ? p.unidad_reporte : p.unidad_medida || ''}
                                </div>
                            </div>
                        </div>
                        <table class="table table-sm table-borderless mb-0" style="font-size:12px;">
                            <thead>
                                <tr style="border-bottom:1px solid #ddd;">
                                    <th class="fw-semibold text-body-secondary">LOTE / UBICACIÓN</th>
                                    <th class="fw-semibold text-body-secondary text-end">CANTIDAD</th>
                                    <th class="fw-semibold text-body-secondary text-end">FECHA DE SALIDA</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${filasLotes}
                                <td></td>
                                <td>Total Entregado:</td>
                                <td>${(totalEntregado / p.factor_conversion) >= 1 ? (totalEntregado / p.factor_conversion) + ' ' + p.unidad_reporte : totalEntregado + ' ' + p.unidad_medida}</td>
                            </tbody>
                        </table>
                    </div>`;
                    }).join('');

                    let htmlFinal = `
                    <div style="background:#fff;color:#000;padding:20px;font-family:'Segoe UI',sans-serif;border:1px solid #ccc;">
                        <div class="text-center mb-4">
                            <div style="font-size:22px;font-weight:700;letter-spacing:1px;">ORDEN DE DESPACHO</div>
                            <div style="font-size:12px;color:#666;">REPORTE DE SALIDA DE ALMACÉN</div>
                        </div>
                        <table class="w-100 mb-4" style="font-size:12px;">
                            <tr>
                                <td><strong>Folio:</strong> ${r.productos[0].folio || 'N/A'}</td>
                                <td class="text-end"><strong>Fecha:</strong> ${new Date().toLocaleDateString()}</td>
                            </tr>
                        </table>
                        ${htmlProductos}
                        <div class="row mt-5 text-center">
                            <div class="col-6"><div style="border-top:1px solid #000;width:80%;margin:auto;padding-top:4px;font-size:11px;">ALMACENISTA</div></div>
                            <div class="col-6"><div style="border-top:1px solid #000;width:80%;margin:auto;padding-top:4px;font-size:11px;">CHOFER / CLIENTE</div></div>
                        </div>
                    </div>`;

                    $('#documentoPatio').html(htmlFinal);
                    $('#btnConfirmarFinal').prop('disabled', true).addClass('d-none');
                    $('#modalSimulacion').modal('show');
                }).always(() => $('#loader').addClass('d-none'));
            };

            // ─── REVERSA DE DESPACHO ──────────────────────────────────────
            window.confirmarReversaDespacho = function (idMovimiento) {
                Swal.fire({
                    title: '<span style="font-weight:700;color:#1d1d1f;">¿Reversar Despacho?</span>',
                    html: '<p style="font-size:.9rem;color:#86868b;">El material regresará a sus lotes originales y el registro de salida será eliminado permanentemente.</p>',
                    icon: 'warning',
                    iconColor: '#ff3b30',
                    showCancelButton: true,
                    confirmButtonColor: '#ff3b30',
                    cancelButtonColor: '#f5f5f7',
                    confirmButtonText: 'Sí, devolver stock',
                    cancelButtonText: '<span style="color:#1d1d1f;">Cancelar</span>',
                    reverseButtons: true,
                    padding: '2em',
                    background: '#ffffff',
                    customClass: {
                        popup: 'border-0 shadow-lg',
                        confirmButton: 'rounded-pill px-4 fw-bold',
                        cancelButton: 'rounded-pill px-4 fw-bold'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        $('#loader').removeClass('d-none');
                        $.ajax({
                            url: '/cfsistem/app/controllers/entregasController.php',
                            type: 'POST',
                            data: { ajax: 'cancelarDespachoFisico', id_movimiento: idMovimiento },
                            dataType: 'json',
                            success: function (res) {
                                if (res.success) {
                                    Swal.fire({
                                        title: '<span style="color:#1d1d1f;">Stock Restaurado</span>',
                                        text: res.message,
                                        icon: 'success',
                                        showConfirmButton: false,
                                        timer: 1800
                                    });
                                    if (typeof listarMovimientos === 'function') listarMovimientos();
                                    else cargarEntregas();
                                } else {
                                    Swal.fire('Error', res.message, 'error');
                                }
                            },
                            error: () => Swal.fire('Error', 'No se pudo conectar con el servidor', 'error'),
                            complete: () => $('#loader').addClass('d-none')
                        });
                    }
                });
            };

            // ─── CARGA INICIAL ─────────────────────────────────────────────
            cargarEntregas();
        });
    </script>
</body>

</html>