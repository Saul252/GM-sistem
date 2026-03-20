 <?php if (function_exists('cargarEstilos')) { cargarEstilos(); } ?>

    <?php if (function_exists('renderizarLayout')) {
        renderizarLayout($paginaActual); 
    } ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Despacho de Materiales (Patio) | Sistema</title>
    
    <?php cargarEstilos(); ?>
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    
    <style>
        :root { --glass-bg: rgba(255, 255, 255, 0.9); }
        body { 
            background-color: #f4f7fa; 
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            padding-top: 75px; 
        }
        .main-content { padding: 1.5rem; min-height: calc(100vh - 75px); }
        .card-custom { 
            border: none; border-radius: 16px; 
            box-shadow: 0 4px 12px rgba(0,0,0,0.03); 
            background: var(--glass-bg); backdrop-filter: blur(10px);
        }
        .table thead th { 
            background-color: #fcfcfd; color: #64748b; font-weight: 700;
            text-transform: uppercase; font-size: 0.7rem; letter-spacing: 0.05em;
            padding: 1.25rem; border-bottom: 2px solid #f1f5f9;
        }
        .btn-despachar {
            background: #2563eb; color: white; border: none;
            font-weight: 700; font-size: 0.75rem; padding: 6px 18px;
            transition: all 0.2s;
            border-radius: 50px;
        }
        .btn-despachar:hover { background: #1d4ed8; transform: translateY(-1px); box-shadow: 0 4px 6px rgba(37, 99, 235, 0.2); }
        .input-disabled { background-color: #f8fafc !important; color: #94a3b8; cursor: not-allowed; }

        @media print {
            body * { visibility: hidden; }
            #documentoPatio, #documentoPatio * { visibility: visible; }
            #documentoPatio { position: absolute; left: 0; top: 0; width: 100%; padding: 20px; background: white; }
            .modal-footer, .btn-close, .modal-header, .btn-print-action { display: none !important; }
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

    <?php cargarScripts(); ?>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    

    <script>
$(document).ready(function() {
    let movimientoActualID = null;

    const tabla = $('#tablaEntregas').DataTable({
        language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json' },
        dom: '<"d-flex justify-content-between p-3 border-bottom"f>rt<"p-3"ip>',
        order: [[0, 'asc']],
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
                if(res.data) {
                    res.data.forEach(m => {
                        let accionHtml = '';
                        if (parseInt(m.ya_despachado) === 1) {
                            accionHtml = `
                               <div class="d-flex align-items-center justify-content-end pe-3">
        <span class="badge rounded-pill bg-success-subtle text-success border border-success-subtle px-3 py-2 me-3" style="font-size: 0.75rem;">
            <i class="bi bi-check-circle-fill me-1"></i> ENTREGADO
        </span>

        <div class="btn-group shadow-sm" role="group" style="border-radius: 20px; overflow: hidden;">
            <button onclick="imprimirComprobante(${m.id})" 
                    class="btn btn-sm btn-white border-end" 
                    title="Imprimir Vale de Patio"
                    style="padding: 8px 12px; transition: all 0.2s;">
                <i class="bi bi-printer text-secondary"></i>
            </button>
            
            <button onclick="verDetalleGanancia(${m.id})" 
                    class="btn btn-sm btn-white" 
                    title="Ver Análisis Financiero"
                    style="padding: 8px 12px; transition: all 0.2s;">
                <i class="bi bi-graph-up-arrow text-success"></i>
            </button>
            <button class="btn btn-primary btn-sm px-3 rounded-pill shadow-sm ms-2" onclick="prepararModalReparto(${m.id}, ${m.almacen_origen_id})">
                    <i class="bi bi-truck me-1"></i> Asignar Ruta
               </button>
            </div>
    </div>`;
                        } else {
                            accionHtml = `
                                <div class="pe-3 text-end">
                                    <button onclick="prepararDespacho(${m.id})" class="btn btn-despachar rounded-pill shadow-sm">
                                        <i class="bi bi-file-earmark-ruled me-1"></i> Preparar
                                    </button>
                                </div>`;
                        }

// AGREGADO: Folio de Venta y Número de Operación (ID del movimiento)
                        tabla.row.add([
                            `<span class="ps-3 fw-bold text-secondary">#${m.id}</span>`, // ID Movimiento
                            `<span class="fw-bold text-primary">${m.folio_venta || '---'}</span>`, // Folio Venta
                            `<span class="text-dark">${m.fecha_format}</span>`, // Fecha formateada
                            `<b>${m.producto}</b><br><small class="text-primary font-monospace">${m.sku}</small>`,
                            `<div class="text-center">${formatQty(m.cantidad, m.factor_conversion, m.unidad_reporte)}</div>`,
                            `<div><span class="badge bg-light text-dark border"><i class="bi bi-geo-alt me-1"></i>${m.origen}</span></div>`,
                            accionHtml
                        ]);
                    });
                }
                tabla.draw();
            },
            complete: () => $('#loader').addClass('d-none')
        });
    }

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