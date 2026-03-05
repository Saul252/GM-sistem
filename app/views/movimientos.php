<?php
require_once __DIR__ . '/../../includes/auth.php';
protegerPagina();
require_once __DIR__ . '/../controllers/LayoutController.php';
require_once __DIR__ . '/../../config/conexion.php';


$tituloPagina = 'Historial';
$almacen_usuario = $_SESSION['almacen_id'] ?? 0;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial de Movimientos | Sistema</title>
    
    <?php cargarEstilos(); ?>

    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">
    <style>
        /* Ajustes de UI para que combine con el nuevo Sidebar claro */
        body { background-color: #f3f4f6; }
        .input-disabled { background-color: #f1f5f9 !important; cursor: not-allowed; }
        .table-sm-text { font-size: 0.85rem; }
        .badge-mov { width: 85px; padding: 6px; border-radius: 6px; font-weight: 600; text-transform: uppercase; font-size: 0.7rem; }
        
        /* Personalización de la Card y Tabla */
        .card { border: none; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); }
        .table thead th { 
            background-color: #f8fafc; 
            color: #64748b; 
            text-transform: uppercase; 
            font-size: 0.75rem; 
            letter-spacing: 0.025em;
            padding: 15px;
        }
        .main-content h2 { color: #1e293b; letter-spacing: -0.025em; }
    </style>
</head>
<body>

    <?php renderizarLayout($tituloPagina); ?>

    <div class="main-content">
        <div class="container-fluid">
            
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold m-0"><i class="bi bi-clock-history text-primary me-2"></i> Historial de Movimientos</h2>
                    <p class="text-muted small m-0">Consulta y exporta el registro de entradas y salidas.</p>
                </div>
                <div id="loader" class="spinner-border text-primary d-none" role="status">
                    <span class="visually-hidden">Cargando...</span>
                </div>
            </div>

            <?php if($almacen_usuario > 0): ?>
                <div class="alert alert-primary border-0 shadow-sm d-flex align-items-center py-2" role="alert">
                    <i class="bi bi-info-circle-fill me-2"></i> 
                    <small>Usted está visualizando únicamente los movimientos de su almacén asignado.</small>
                </div>
            <?php endif; ?>
            
            <div class="card mb-4">
                <div class="card-body bg-white p-4">
                    <form id="formFiltros" class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-secondary">RANGO DE TIEMPO</label>
                            <select id="selectorPeriodo" class="form-select shadow-sm border-light">
                                <option value="hoy">Hoy</option>
                                <option value="ayer">Ayer</option>
                                <option value="semana">Últimos 7 días</option>
                                <option value="mes">Este Mes</option>
                                <option value="personalizado">📅 Seleccionar Rango</option>
                            </select>
                        </div>
                        
                        <div class="col-md-2">
                            <label class="form-label small fw-bold text-secondary">DESDE</label>
                            <input type="date" id="f_inicio" class="form-control input-disabled shadow-sm border-light" disabled>
                        </div>
                        
                        <div class="col-md-2">
                            <label class="form-label small fw-bold text-secondary">HASTA</label>
                            <input type="date" id="f_fin" class="form-control input-disabled shadow-sm border-light" disabled>
                        </div>
                        
                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-secondary">TIPO DE OPERACIÓN</label>
                            <select id="filtroTipo" class="form-select shadow-sm border-light">
                                <option value="">-- Todos los movimientos --</option>
                                <option value="entrada">Entradas</option>
                                <option value="salida">Salidas</option>
                                <option value="traspaso">Traspasos</option>
                                <option value="ajuste">Ajustes</option>
                            </select>
                        </div>
                        
                        <div class="col-md-2 text-end">
                            <button type="button" id="btnReset" class="btn btn-outline-secondary w-100 fw-bold border-light-subtle shadow-sm">
                                <i class="bi bi-arrow-counterclockwise"></i> Limpiar
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card overflow-hidden">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table id="tablaHistorial" class="table table-hover align-middle mb-0 table-sm-text">
                            <thead>
                                <tr>
                                    <th class="ps-4">Fecha / Hora</th>
                                    <th>Producto / SKU</th>
                                    <th class="text-center">Tipo</th>
                                    <th class="text-center">Cant.</th>
                                    <th>Ruta Origen/Destino</th>
                                    <th>Usuarios</th>
                                    <th>Obs.</th>
                                    <th class="no-export pe-4 text-end">Acción</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php cargarScripts(); ?>

    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>

 <script>
    $(document).ready(function() {
        const almacenUsuario = <?= $almacen_usuario ?>;

        const tabla = $('#tablaHistorial').DataTable({
            language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json' },
            dom: '<"d-flex justify-content-between align-items-center p-3"Bf>rt<"p-3"ip>',
            buttons: [
                {
                    extend: 'pdfHtml5',
                    text: '<i class="bi bi-file-earmark-pdf"></i> Exportar PDF',
                    className: 'btn btn-danger btn-sm px-3 rounded-pill shadow-sm me-2',
                    title: 'Reporte de Movimientos de Inventario',
                    orientation: 'landscape',
                    exportOptions: { 
                        columns: [0, 1, 2, 3, 4, 5, 6],
                        stripHtml: true // Para que en el PDF no salgan etiquetas HTML
                    }
                },
                {
                    extend: 'print',
                    text: '<i class="bi bi-printer"></i> Imprimir',
                    className: 'btn btn-dark btn-sm px-3 rounded-pill shadow-sm',
                    exportOptions: { columns: ':not(.no-export)' }
                }
            ],
            order: [[0, 'desc']],
            pageLength: 25
        });

        // --- FUNCIÓN DE CONVERSIÓN PARA EL HISTORIAL ---
        function formatearCantidadHistorial(cantidad, factor, unidad) {
            const cant = parseFloat(cantidad) || 0;
            const fact = parseFloat(factor) || 1;
            const unid = unidad || 'Unid.';

            // Si hay factor y la cantidad alcanza para formar al menos una unidad mayor
            if (fact > 1 && cant >= fact) {
                const unidadesMayores = Math.floor(cant / fact);
                const restoPiezas = Math.round((cant % fact) * 100) / 100;
                
                let html = `<div class="fw-bold text-dark">${unidadesMayores} ${unid}</div>`;
                if (restoPiezas > 0) {
                    html += `<small class="text-muted text-nowrap">+ ${restoPiezas} pzas</small>`;
                }
                return html;
            }
            
            // Si es menor al factor, mostrar piezas normales
            return `<div class="fw-bold text-dark">${cant} <small class="fw-normal text-muted">pzas</small></div>`;
        }

        function cargarHistorial() {
            const params = {
                periodo: $('#selectorPeriodo').val(),
                f_inicio: $('#f_inicio').val(),
                f_fin: $('#f_fin').val(),
                tipo: $('#filtroTipo').val(),
                almacen_id: almacenUsuario
            };

            $('#loader').removeClass('d-none');

            $.ajax({
                url: '/cfsistem/app/backend/movimientos/obtener_historial.php',
                type: 'GET',
                data: params,
                dataType: 'json',
                success: function(response) {
                    tabla.clear();
                    if(response.data && response.data.length > 0) {
                        response.data.forEach(m => {
                            // Aplicamos la conversión antes de añadir la fila
                            const cantidadCelda = formatearCantidadHistorial(m.cantidad, m.factor_conversion, m.unidad_reporte);
                            
                            tabla.row.add([
                                `<span class="ps-3 text-dark fw-bold">${m.fecha_format}</span>`,
                                `<div>${m.producto}</div><small class="text-primary fw-medium">${m.sku}</small>`,
                                `<div class="text-center"><span class="badge badge-mov bg-${m.color}">${m.tipo}</span></div>`,
                                `<div class="text-center">${cantidadCelda}</div>`,
                                `<div class="small"><b>De:</b> ${m.origen || '-'}<br><b>A:</b> ${m.destino || '-'}</div>`,
                                `<div class="small text-muted"><i class="bi bi-person"></i> ${m.u_reg}</div>`,
                                `<small class="text-muted fst-italic">${m.obs || '-'}</small>`,
                                `<div class="pe-3 text-end"><a href="/cfsistem/app/backend/movimientos/imprimir_movimiento.php?id=${m.id}" target="_blank" class="btn btn-sm btn-light border shadow-sm"><i class="bi bi-file-pdf text-danger"></i></a></div>`
                            ]);
                        });
                    }
                    tabla.draw();
                },
                error: function(e) {
                    console.error("Error al cargar historial", e);
                },
                complete: () => $('#loader').addClass('d-none')
            });
        }

        // Manejo de Filtros
        $('#selectorPeriodo').on('change', function() {
            if($(this).val() === 'personalizado') {
                $('#f_inicio, #f_fin').prop('disabled', false).removeClass('input-disabled');
            } else {
                $('#f_inicio, #f_fin').prop('disabled', true).addClass('input-disabled').val('');
                cargarHistorial();
            }
        });

        $('#f_inicio, #f_fin, #filtroTipo').on('change', cargarHistorial);
        $('#btnReset').on('click', function() {
            $('#formFiltros')[0].reset();
            $('#f_inicio, #f_fin').prop('disabled', true).addClass('input-disabled');
            cargarHistorial();
        });

        // Carga inicial al abrir la página
        cargarHistorial();
    });
    </script>
</body>
</html>