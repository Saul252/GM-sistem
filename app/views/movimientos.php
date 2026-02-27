<?php
require_once __DIR__ . '/../../includes/auth.php';
protegerPagina();
require_once __DIR__ . '/../../includes/sidebar.php';
require_once __DIR__ . '/../../config/conexion.php';

$paginaActual = 'Historial';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Historial de Movimientos | Sistema</title>
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="/cfsistem/css/almacenes.css" rel="stylesheet">
    <style>
        .input-disabled { background-color: #e9ecef !important; cursor: not-allowed; }
        .table-sm-text { font-size: 0.85rem; }
        .badge-mov { width: 80px; display: inline-block; text-align: center; }
    </style>
</head>
<body>
    <?php renderSidebar($paginaActual); ?>

    <div class="main-content">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="fw-bold m-0"><i class="bi bi-clock-history text-primary"></i> Historial de Movimientos</h2>
                <div id="loader" class="spinner-border text-primary d-none" role="status">
                    <span class="visually-hidden">Cargando...</span>
                </div>
            </div>
            
            <div class="card shadow-sm mb-4 border-0">
                <div class="card-body bg-light rounded">
                    <form id="formFiltros" class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-uppercase">Periodo</label>
                            <select id="selectorPeriodo" class="form-select border-primary shadow-sm">
                                <option value="hoy">Hoy</option>
                                <option value="ayer">Ayer</option>
                                <option value="semana">Últimos 7 días</option>
                                <option value="mes">Este Mes</option>
                                <option value="personalizado">📅 Rango Personalizado</option>
                            </select>
                        </div>
                        
                        <div class="col-md-2">
                            <label class="form-label small fw-bold text-uppercase">Desde</label>
                            <input type="date" id="f_inicio" class="form-control input-disabled" disabled>
                        </div>
                        
                        <div class="col-md-2">
                            <label class="form-label small fw-bold text-uppercase">Hasta</label>
                            <input type="date" id="f_fin" class="form-control input-disabled" disabled>
                        </div>
                        
                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-uppercase">Tipo de Movimiento</label>
                            <select id="filtroTipo" class="form-select shadow-sm">
                                <option value="">-- Todos los movimientos --</option>
                                <option value="entrada">Entradas</option>
                                <option value="salida">Salidas</option>
                                <option value="traspaso">Traspasos</option>
                                <option value="ajuste">Ajustes</option>
                            </select>
                        </div>
                        
                        <div class="col-md-2 text-end">
                            <button type="button" id="btnReset" class="btn btn-outline-secondary w-100">
                                <i class="bi bi-arrow-counterclockwise"></i> Limpiar
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table id="tablaHistorial" class="table table-hover align-middle mb-0 table-sm-text">
                            <thead class="table-dark">
                                <tr>
                                    <th>Fecha y Hora</th>
                                    <th>Producto / SKU</th>
                                    <th>Tipo</th>
                                    <th>Cantidad</th>
                                    <th>Ruta (Origen/Destino)</th>
                                    <th>Usuarios Involucrados</th>
                                    <th>Observaciones</th>
                                    <th class="no-export">Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>

    <script>
    $(document).ready(function() {
        // 1. Inicializar DataTable
        const tabla = $('#tablaHistorial').DataTable({
            language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json' },
            dom: 'Bfrtip',
            buttons: [
                { 
                    extend: 'print', 
                    text: '<i class="bi bi-printer"></i> Imprimir Reporte', 
                    className: 'btn btn-dark btn-sm me-2 rounded',
                    exportOptions: { columns: ':not(.no-export)' } 
                },
                { 
                    extend: 'pdf', 
                    text: '<i class="bi bi-file-pdf"></i> Exportar PDF', 
                    className: 'btn btn-danger btn-sm rounded',
                    exportOptions: { columns: ':not(.no-export)' } 
                }
            ],
            order: [[0, 'desc']],
            pageLength: 25
        });

        // 2. Función para obtener datos asíncronamente
        function cargarHistorial() {
            const params = {
                periodo: $('#selectorPeriodo').val(),
                f_inicio: $('#f_inicio').val(),
                f_fin: $('#f_fin').val(),
                tipo: $('#filtroTipo').val()
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
                            tabla.row.add([
                                `<b>${m.fecha_format}</b>`,
                                `<div>${m.producto}</div><small class="text-primary">${m.sku}</small>`,
                                `<span class="badge badge-mov bg-${m.color}">${m.tipo.toUpperCase()}</span>`,
                                `<span class="fw-bold">${m.cantidad}</span>`,
                                `<div class="small"><b>De:</b> ${m.origen || 'N/A'}<br><b>A:</b> ${m.destino || 'N/A'}</div>`,
                                `<div class="small text-muted">Reg: ${m.u_reg}<br>Env: ${m.u_env || '-'}<br>Rec: ${m.u_rec || '-'}</div>`,
                                `<small class="text-muted fst-italic">${m.obs || 'Sin observaciones'}</small>`,
                                `<a href="/cfsistem/app/backend/movimientos/imprimir_movimiento.php?id=${m.id}" target="_blank" class="btn btn-sm btn-outline-danger shadow-sm"><i class="bi bi-file-earmark-pdf"></i></a>`
                            ]);
                        });
                    }
                    tabla.draw();
                },
                error: function() {
                    console.error("Error al cargar los datos.");
                },
                complete: function() {
                    $('#loader').addClass('d-none');
                }
            });
        }

        // 3. Control de inputs de fecha
        $('#selectorPeriodo').on('change', function() {
            const val = $(this).val();
            if(val === 'personalizado') {
                $('#f_inicio, #f_fin').prop('disabled', false).removeClass('input-disabled');
            } else {
                $('#f_inicio, #f_fin').prop('disabled', true).addClass('input-disabled').val('');
                cargarHistorial(); // Cargar automáticamente al cambiar periodo predefinido
            }
        });

        // 4. Listeners para tiempo real
        $('#f_inicio, #f_fin, #filtroTipo').on('change', cargarHistorial);

        $('#btnReset').on('click', function() {
            $('#formFiltros')[0].reset();
            $('#f_inicio, #f_fin').prop('disabled', true).addClass('input-disabled');
            cargarHistorial();
        });

        // Carga inicial al abrir la página
        cargarHistorial();
    });


    // Configuración de los botones dentro del DataTable
buttons: [
    {
        extend: 'pdfHtml5',
        text: '<i class="bi bi-file-pdf"></i> Exportar Todo a PDF',
        className: 'btn btn-danger btn-sm shadow-sm',
        title: 'Reporte de Movimientos de Inventario',
        orientation: 'landscape', // Mejor para tablas anchas
        pageSize: 'LETTER',
        exportOptions: { columns: [0, 1, 2, 3, 4, 5, 6] }, // Excluye la columna de acciones
        customize: function (doc) {
            doc.content[1].table.widths = ['12%', '18%', '10%', '10%', '20%', '15%', '15%'];
            doc.styles.tableHeader.fillColor = '#212529'; // Encabezado oscuro
            doc.defaultStyle.fontSize = 9;
        }
    },
    {
        extend: 'print',
        text: '<i class="bi bi-printer"></i> Imprimir Tabla',
        className: 'btn btn-dark btn-sm shadow-sm',
        exportOptions: { columns: ':not(.no-export)' }
    }
]
    </script>
</body>
</html>