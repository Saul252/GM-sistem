 
    
   
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Despacho de Materiales (Patio) | Sistema</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    
     <?php require_once __DIR__ . '/layout/icono.php' ?>
    <?php if (function_exists('cargarEstilos')) { cargarEstilos(); } ?>
    
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
            color: #1d1d1f;
        }

        .main-content { 
            margin-left: var(--sidebar-width); 
            padding: 40px; 
            padding-top: calc(var(--navbar-height) + 20px); 
        }

        .card-premium { 
            border: none; 
            border-radius: 20px; 
            box-shadow: 0 8px 30px rgba(0,0,0,0.04); 
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
        }

        .badge-ubicacion { 
            background-color: #f2f2f7; 
            color: #1d1d1f; 
            border: 1px solid #d1d1d6; 
            padding: 0.4rem 0.7rem;
            font-size: 0.75rem;
            font-weight: 600;
            border-radius: 8px;
        }

        /* DataTables Custom */
        .dataTables_wrapper .pagination .page-item.active .page-link {
            background-color: var(--accent-blue);
            border-color: var(--accent-blue);
            border-radius: 8px;
        }

        .table thead th {
            background: #fbfbfd;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #86868b;
            border-bottom: 1px solid #d1d1d6;
        }

        @media (max-width: 768px) { 
            .main-content { margin-left: 0; padding: 20px; padding-top: 90px; } 
        }
        .form-check-input:checked {
    background-color: var(--accent-blue);
    border-color: var(--accent-blue);
}

.form-switch .form-check-input {
    width: 2.5em;
    cursor: pointer;
}
    </style>
   
   
</head>
<body>

    <?php renderizarLayout($paginaActual); ?>

    <div class="main-content">
        <div class="container-fluid">
            
            <div class="d-flex justify-content-between align-items-end mb-4">
                <div>
                    <h2 class="fw-bold m-0 text-dark">Despacho de Materiales</h2>
                    <p class="text-muted small">Control físico de lotes y entregas en patio</p>
                </div>
                <div id="loader" class="spinner-border text-primary d-none" role="status"></div>
            </div>

            <div class="card card-custom mb-4">
                <div class="card-body p-4">
                    <form id="formFiltros" class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-muted">PERIODO</label>
                            <select id="selectorPeriodo" class="form-select border-0 bg-light">
                                <option value="hoy">Hoy</option>
                                <option value="ayer">Ayer</option>
                                <option value="semana" selected>Últimos 7 días</option>
                                <option value="mes">Este Mes</option>
                                <option value="personalizado">📅 Rango Manual</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small fw-bold text-muted">DESDE</label>
                            <input type="date" id="f_inicio" class="form-control input-disabled" disabled>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small fw-bold text-muted">HASTA</label>
                            <input type="date" id="f_fin" class="form-control input-disabled" disabled>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-muted">ALMACÉN</label>
                            <select id="filtroAlmacen" class="form-select border-0 bg-light" <?= ($almacen_usuario > 0) ? 'disabled' : '' ?>>
                                <?php if($almacen_usuario == 0): ?>
                                    <option value="0">-- Todos los Almacenes --</option>
                                    <?php 
                                    $q_alm = $conexion->query("SELECT id, nombre FROM almacenes WHERE activo = 1 ORDER BY nombre");
                                    while($a = $q_alm->fetch_assoc()): ?>
                                        <option value="<?= $a['id'] ?>"><?= $a['nombre'] ?></option>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <?php $res_mio = $conexion->query("SELECT nombre FROM almacenes WHERE id = $almacen_usuario LIMIT 1")->fetch_assoc(); ?>
                                    <option value="<?= $almacen_usuario ?>" selected>📍 <?= $res_mio['nombre'] ?></option>
                                <?php endif; ?>
                            </select>
                        </div>
                        
    <div class="form-check form-switch pt-2">
        <input class="form-check-input" type="checkbox" id="checkAgruparVenta">
        <label class="form-check-label small fw-bold text-primary" for="checkAgruparVenta">
            <i class="bi bi-layers-half me-1"></i> AGRUPAR POR VENTA
        </label>
    
</div>
<div class="col-md-2">
    <button type="button" id="btnReset" class="btn btn-dark w-100 rounded-pill fw-bold">Limpiar</button>
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
                                    <th>Fecha</th>
                                    <th>Producto / SKU</th>
                                    <th class="text-center">Cant. Solicitada</th>
                                    <th>Almacén</th>
                                    <th class="text-end pe-4">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="text-secondary" style="font-size: 0.85rem;"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalSimulacion" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-dark text-white d-flex justify-content-between align-items-center">
                    <h5 class="modal-title m-0"><i class="bi bi-file-earmark-ruled me-2"></i>Orden de Despacho</h5>
                    <div>
                        <button type="button" class="btn btn-outline-light btn-sm btn-print-action me-2" onclick="window.print()">
                            <i class="bi bi-printer me-1"></i> Imprimir
                        </button>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                </div>
                <div class="modal-body p-4" id="documentoPatio"></div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" id="btnConfirmarFinal" class="btn btn-primary px-4 fw-bold">
                        <i class="bi bi-check-circle me-1"></i> Generar Entrega
                    </button>
                </div>
            </div>
        </div>
    </div>


     <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
     
 
    <?php require_once __DIR__ . '/entregasComponets/entregasPatioModal.php'; ?>
<?php require_once __DIR__ . '/entregasComponets/modalVerDetalleEntregas.php'; ?>
<?php require_once __DIR__ . '/entregasComponets/modalEntregaVentas.php'; ?>

    <script>
$(document).ready(function() {
    let movimientoActualID = null;

    const tabla = $('#tablaEntregas').DataTable({
    language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json' },
    dom: '<"d-flex justify-content-between p-3 border-bottom"f>rt<"p-3"ip>',
    // CAMBIO: De 'asc' a 'desc' para que el ID más alto (el más nuevo) aparezca primero
    order: [[1, 'desc']], 
    pageLength: 20
});

    /**
     * AJUSTE: Función de formateo para mostrar Unidades de Reporte (Ej: Toneladas)
     */
    function formatQty(cantidad, factor, unidad) {
        const cant = parseFloat(cantidad);
        const fac = parseFloat(factor || 1);
        
        if(fac > 1 && cant >= fac) {
            const uReporte = Math.floor(cant / fac);
            const resto = Math.round((cant % fac) * 100) / 100;
            return `<div class="fw-bold text-dark fs-6">${uReporte} ${unidad}</div>` +
                   (resto > 0 ? `<small class="text-muted">+ ${resto} pzas</small>` : '');
        }
        return `<div class="fw-bold text-dark fs-6">${cant} <small class="fw-normal text-muted">pzas</small></div>`;
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
        success: function(res) {
            tabla.clear();
            if (!res.data) { tabla.draw(); return; }

            let datosAMostrar = res.data;

            if (agrupar) {
                const grupos = {};
                res.data.forEach(item => {
                    const folio = item.folio_venta || 'SIN-FOLIO';
                    if (!grupos[folio]) {
                        grupos[folio] = { 
                            ...item, 
                            total_items: 0, 
                            items_despachados: 0, 
                            items_en_ruta: 0,
                            items_completados: 0,
                            ids_movimientos: [] 
                        };
                    }
                    grupos[folio].total_items++;
                    
                    // Contadores de estado para decidir qué botones mostrar
                    if (parseInt(item.ya_despachado) === 1) grupos[folio].items_despachados++;
                    if (item.estado_reparto === 'en_transito') grupos[folio].items_en_ruta++;
                    if (item.estado_reparto === 'completado') grupos[folio].items_completados++;
                    
                    grupos[folio].ids_movimientos.push(item.id);
                });
                datosAMostrar = Object.values(grupos);
            }

            datosAMostrar.forEach(m => {
                let accionHtml = '';
                let prodCol = '';
                let cantCol = '';

                // --- LÓGICA PARA VISTA AGRUPADA ---
                if (agrupar && m.total_items > 1) {
                    const todoDespachado = (m.total_items === m.items_despachados);
                    const todoCompletado = (m.total_items === m.items_completados);
                    const algoEnRuta     = (m.items_en_ruta > 0);

                    cantCol = `<div class="text-center text-muted small">${m.total_items} Artículos</div>`;
                    prodCol = `<b>Venta Consolidada</b><br><small class="text-muted">Folio: ${m.folio_venta}</small>`;

                    if (todoCompletado) {
                        // ESTADO: ENTREGADO
                        accionHtml = `
                            <div class="text-end pe-3">
                                <span class="badge rounded-pill p-2 px-3" style="background: rgba(40, 167, 69, 0.1); color: #28a745; border: 1px solid #28a745;">
                                    <i class="bi bi-check2-all me-1"></i> MATERIAL ENTREGADO
                                </span>
                            </div>
                              <button onclick="verDetalleGanancia(${m.id})" class="btn btn-link ms-2 text-decoration-none" style="color: #ceced2;">
                    <i class="bi bi-graph-up-arrow fs-6"></i>
                </button>`;
                    } 
                    else if (algoEnRuta) {
                        // ESTADO: EN TRANSITO
                        accionHtml = `
                            <div class="text-end pe-3">
                                <span class="badge rounded-pill p-2 px-3" style="background: rgba(255, 149, 0, 0.1); color: #ff9500; border: 1px solid #ff9500;">
                                    <i class="bi bi-truck me-1"></i> MERCANCÍA EN TRÁNSITO
                                </span>
                            </div>
                              <button onclick="verDetalleGanancia(${m.id})" class="btn btn-link ms-2 text-decoration-none" style="color: #ceced2;">
                    <i class="bi bi-graph-up-arrow fs-6"></i>
                </button>`;
                    }
                    else if (todoDespachado) {
                        // ESTADO: YA DESPACHADO (Mostrar botones de Patio/Ruta para el grupo)
                        accionHtml = `
                            <div class="d-flex align-items-center justify-content-end pe-3" style="gap: 8px;">
                                <button onclick="prepararModalPatioMasivo('${m.ids_movimientos.join(',')}', ${m.almacen_origen_id})"
                                        class="btn rounded-pill px-3 shadow-sm d-flex align-items-center"
                                        style="background: #007aff; color: #fff; border: none; height: 35px; font-size: 0.75rem; font-weight: 600;">
                                    <i class="bi bi-box-seam me-2"></i>Entregar en Patio 
                                </button>
                                <button onclick="prepararModalRepartoMasivo('${m.ids_movimientos.join(',')}', ${m.almacen_origen_id})"
                                        class="btn rounded-pill px-3 shadow-sm d-flex align-items-center"
                                        style="background: #1c1c1e; color: #fff; border: none; height: 35px; font-size: 0.75rem; font-weight: 600;">
                                    <i class="bi bi-truck me-2"></i> Asignar a Ruta
                                </button>
                            </div>`;
                    } 
                    else {
                        // ESTADO: PENDIENTE DE DESPACHO
                        accionHtml = `
                            <div class="text-end pe-3">
                                <button class="btn btn-sm rounded-pill btn-dark px-4 shadow-sm" onclick="abrirModalDespachoVenta(${m.venta_id},${m.almacen_origen_id})">
                                    <i class="bi bi-list-check me-1"></i> GESTIONAR VENTA
                                </button>
                            </div>`;
                    }
                } 
                // --- LÓGICA PARA VISTA INDIVIDUAL ---
                else {
                    const yaDespachado = (parseInt(m.ya_despachado) === 1);
                    const enRuta       = (m.estado_reparto === 'en_transito');
                    const completado   = (m.estado_reparto === 'completado');

                    if (completado || enRuta) {
                        const color = completado ? '#28a745' : '#ff9500';
                        const texto = completado ? 'MATERIAL ENTREGADO' : 'MERCANCÍA EN TRÁNSITO';
                        accionHtml = `
                            <div class="d-flex align-items-center justify-content-end pe-3 py-1">
                                <span class="fw-bold me-3" style="color: ${color}; font-size: 0.7rem;">${texto}</span>
                                <button onclick="imprimirComprobante(${m.id})" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                    <i class="bi bi-printer"></i>
                                </button>
                            </div>
                              <button onclick="verDetalleGanancia(${m.id})" class="btn btn-link ms-2 text-decoration-none" style="color: #ceced2;">
                    <i class="bi bi-graph-up-arrow fs-6"></i>
                </button>`;
                    } 
                    else if (yaDespachado) {
                        accionHtml = `
                            <div class="d-flex align-items-center justify-content-end pe-3 py-1" style="gap: 8px;">
                               <button onclick="prepararModalPatio(${m.id}, ${m.almacen_origen_id})"
                            class="btn rounded-pill px-3 d-flex align-items-center justify-content-center"
                            style="background: #007aff; color: #fff; border: none; font-weight: 600; height: 38px; transition: 0.3s;">
                        <i class="bi bi-box-seam me-2"></i><span style="font-size: 0.75rem;">ENTREGAR EN PATIO</span>
                    </button>

                    <button onclick="prepararModalReparto(${m.id}, ${m.almacen_origen_id})"
                            class="btn rounded-pill px-3 d-flex align-items-center justify-content-center"
                            style="background: #1c1c1e; color: #fff; border: none; font-weight: 600; height: 38px; transition: 0.3s;">
                        <i class="bi bi-truck me-2"></i><span style="font-size: 0.75rem;">ASIGNAR A RUTA</span>
                    </button>
                            </div>`;
                    } 
                    else {
                        accionHtml = `
                            <div class="pe-3 text-end py-1">
                                <button onclick="prepararDespacho(${m.id})" class="btn btn-sm rounded-pill shadow-sm px-4" style="background: #5856d6; color: white; font-weight: 600;">
                                    <i class="bi bi-file-earmark-check me-1"></i> DESPACHAR
                                </button>
                            </div>`;
                    }
                    prodCol = `<b>${m.producto}</b><br><small class="text-primary font-monospace">${m.sku}</small>`;
                    cantCol = `<div class="text-center">${formatQty(m.cantidad, m.factor_conversion, m.unidad_reporte)}</div>`;
                }

                // Renderizado final de la fila
                tabla.row.add([
                    `<span class="ps-3 fw-bold text-secondary">#${agrupar ? m.id : m.id}</span>`,
                    `<span class="fw-bold text-primary">${m.folio_venta || '---'}</span>`,
                    `<span class="text-dark small">${m.fecha_format}</span>`,
                    prodCol,
                    cantCol,
                    `<div><span class="badge bg-light text-dark border small"><i class="bi bi-geo-alt me-1"></i>${m.origen}</span></div>`,
                    accionHtml
                ]);
            });
            tabla.draw();
        },
        complete: () => $('#loader').addClass('d-none')
    });
}// Evento para recargar al cambiar el check
$('#checkAgruparVenta').on('change', function() {
    cargarEntregas();
});
// Escuchar el cambio del checkbox
$('#checkAgruparVenta').on('change', cargarEntregas);
    // FASE 1: SIMULACIÓN CON CONVERSIÓN
    window.prepararDespacho = function(id) {
        movimientoActualID = id;
        $('#loader').removeClass('d-none');

        $.getJSON('entregasController.php', { ajax: 'simular', id: id }, function(res) {
            if (!res.success) {
                Swal.fire('Atención', res.message, 'error');
                return;
            }

            // Calculamos visualmente el total en unidades de reporte para el encabezado del modal
            const textoTotal = formatQty(res.total_solicitado, res.factor_conversion, res.unidad_reporte);

            let html = `
                <div class="text-center mb-4">
                    <h4 class="mb-1 text-uppercase fw-bold">Hoja de Ruta de Patio</h4>
                    <p class="text-muted small">Despacho de Material por Lotes (PEPS)</p>
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
                        <tbody style="font-size: 0.85rem;">`;
            
            res.lotes.forEach(l => {
                html += `
                    <tr>
                        <td><code class="text-dark fw-bold">${l.codigo}</code></td>
                        <td>${l.fecha_entrada}</td>
                        <td class="text-end text-muted">${l.cantidad_en_lote} pzas</td>
                        <td class="text-end fw-bold text-primary">-${l.cantidad_a_extraer} pzas</td>
                        <td class="text-end">${l.saldo_final <= 0 ? '<span class="badge bg-danger">AGOTADO</span>' : l.saldo_final + ' pzas'}</td>
                    </tr>`;
            });

            html += `</tbody></table></div>`;

            if (res.pendiente > 0) {
                html += `<div class="alert alert-danger mt-3 d-flex align-items-center">
                    <i class="bi bi-exclamation-octagon-fill fs-4 me-2"></i> 
                    <div><b>Inconsistencia:</b> Stock insuficiente. Faltan ${res.pendiente} pzas.</div>
                </div>`;
                $('#btnConfirmarFinal').prop('disabled', true).addClass('d-none');
            } else {
                $('#btnConfirmarFinal').prop('disabled', false).removeClass('d-none');
            }

            $('#documentoPatio').html(html);
            $('#modalSimulacion').modal('show');
        }).always(() => $('#loader').addClass('d-none'));
    };

    // FASE 2: CONFIRMACIÓN
    $('#btnConfirmarFinal').on('click', function() {
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

                $.post('entregasController.php', { ajax: 'despachar', id_movimiento: movimientoActualID }, function(res) {
                    $('#modalSimulacion').modal('hide');
                    if(res.success) {
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

    // EVENTOS
    $('#selectorPeriodo').on('change', function() {
        const isPerso = $(this).val() === 'personalizado';
        $('#f_inicio, #f_fin').prop('disabled', !isPerso).toggleClass('input-disabled', !isPerso);
        if(!isPerso) cargarEntregas();
    });

    $('#f_inicio, #f_fin, #filtroAlmacen').on('change', cargarEntregas);

    $('#btnReset').on('click', () => { 
        $('#formFiltros')[0].reset(); 
        $('#f_inicio, #f_fin').prop('disabled', true).addClass('input-disabled');
        cargarEntregas(); 
    });

   window.imprimirComprobante = function(id) {
    $('#btnConfirmarFinal').addClass('d-none'); // Ocultar botón de despacho
    $('#btnImprimirModal').removeClass('d-none'); // Mostrar botón de impresora
    $('#loader').removeClass('d-none');
    
    $.getJSON('entregasController.php', { ajax: 'imprimir', id: id }, function(res) {
        if(res.success) {
            const d = res.data;
            
            // Reutilizamos exactamente tu estructura de "Simular"
            let html = `
                <div class="text-center mb-4">
                    <h4 class="mb-1 text-uppercase fw-bold">Vale de Entrega (Patio)</h4>
                    <p class="text-muted small">Folio de Movimiento: #<b>${d.movimiento_id}</b></p>
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
                            <tr>
                                <th>CÓDIGO LOTE</th>
                                <th class="text-end">DETALLE DE SALIDA</th>
                            </tr>
                        </thead>
                        <tbody style="font-size: 0.85rem;">
                            <tr>
                                <td class="py-2 font-monospace text-dark">${d.detalle_lotes.replace(/\n/g, '<br>')}</td>
                                <td class="text-end fw-bold text-primary py-2">${d.cantidad_total} pzas</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="row mt-5 pt-4 text-center">
                    <div class="col-6">
                        <div style="border-top: 1px solid #dee2e6; width: 80%; margin: 0 auto;" class="pt-2">
                            <small class="text-muted d-block">Despachó (Patio)</small>
                            <strong class="small">${d.usuario_despacho}</strong>
                        </div>
                    </div>
                    <div class="col-6">
                        <div style="border-top: 1px solid #dee2e6; width: 80%; margin: 0 auto;" class="pt-2">
                            <small class="text-muted d-block">Recibió (Firma)</small>
                            <br>
                        </div>
                    </div>
                </div>
            `;
            
            $('#documentoPatio').html(html);
            $('#modalSimulacion').modal('show');
        } else {
            Swal.fire('Error', res.message, 'error');
        }
    }).always(() => $('#loader').addClass('d-none'));
};

window.verDetalleGanancia = function(id) {
    // Ajuste de UI para el modal de auditoría
    $('#btnConfirmarFinal').addClass('d-none'); 
    $('#btnImprimirModal').removeClass('d-none'); 
    $('#loader').removeClass('d-none');
    
    $.getJSON('entregasController.php', { ajax: 'imprimirGanancia', id: id }, function(res) {
        if(res.success) {
            const d = res.data;
            const colorGanancia = parseFloat(d.ganancia_neta) < 0 ? 'text-danger' : 'text-success';
            
            let filasLotes = '';
            if (d.detalle_financiero) {
                const registros = d.detalle_financiero.split('___');
                registros.forEach(reg => {
                    const c = reg.split('|'); 
                    if (c.length === 4) {
                        const subC = parseFloat(c[1]) * parseFloat(c[2]);
                        const subV = parseFloat(c[1]) * parseFloat(c[3]);
                        const util = subV - subC;

                        filasLotes += `
                            <tr>
                                <td class="font-monospace text-start ps-2">${c[0]}</td>
                                <td>${c[1]}</td>
                                <td class="text-end text-muted">$ ${parseFloat(c[2]).toFixed(2)}</td>
                                <td class="text-end">$ ${parseFloat(c[3]).toFixed(2)}</td>
                                <td class="text-end text-muted">$ ${subC.toFixed(2)}</td>
                                <td class="text-end fw-bold">$ ${subV.toFixed(2)}</td>
                                <td class="text-end fw-bold ${util < 0 ? 'text-danger' : 'text-success'}">$ ${util.toFixed(2)}</td>
                            </tr>`;
                    }
                });
            }

           // Dentro de tu función window.verDetalleGanancia:

// 1. Cambiamos el color del encabezado de la tabla a oscuro para denotar "Auditoría"
let html = `
    <div class="text-center mb-4">
        <div class="badge bg-success mb-2">Reporte Financiero</div>
        <h4 class="mb-1 text-uppercase fw-bold text-dark">Rentabilidad de Venta</h4>
        <p class="text-muted small">ID Movimiento: #<b>${d.movimiento_id}</b> | Folio: <b>${d.folio_venta || 'N/A'}</b></p>
    </div>
    
    <div class="table-responsive">
        <table class="table table-sm table-hover table-bordered align-middle text-center">
            <thead class="table-dark small"> <tr>
                    <th>Lote Origen</th>
                    <th>Cant.</th>
                    <th>Costo Adq.</th>
                    <th>Precio Venta</th>
                    <th>Inversión</th>
                    <th>Ingreso Bruto</th>
                    <th>Utilidad</th>
                </tr>
            </thead>
            <tbody style="font-size: 0.75rem;">
                ${filasLotes}
            </tbody>
            <tfoot class="table-info fw-bold"> <tr>
                    <td colspan="4" class="text-end small">RESUMEN DE OPERACIÓN:</td>
                    <td class="text-end">$ ${parseFloat(d.total_costo).toFixed(2)}</td>
                    <td class="text-end">$ ${parseFloat(d.total_venta).toFixed(2)}</td>
                    <td class="text-end ${colorGanancia}" style="font-size: 0.95rem;">
                        $ ${parseFloat(d.ganancia_neta).toFixed(2)}
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
    ...
`;
            // Recuerda que si clonas el modal, el ID del contenedor debe ser distinto
            // o puedes reusar el mismo si no se abren al mismo tiempo.
            $('#documentoPatio').html(html); 
            $('#modalSimulacion').modal('show');
        } else {
            Swal.fire('Error de Consulta', res.message, 'error');
        }
    }).always(() => $('#loader').addClass('d-none'));
};

    cargarEntregas();
});
</script>
 <?php require_once __DIR__ . '/entregasComponets/repartoModalEntregas.php'; ?>
   
</body>
</html>