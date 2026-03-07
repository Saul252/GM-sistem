<?php
// El controlador ya definió $almacen_usuario y $conexion
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
        :root { --glass-bg: rgba(255, 255, 255, 0.9); }
       body { 
    background-color: #f4f7fa; 
    font-family: 'Inter', system-ui, -apple-system, sans-serif;
    /* Ajusta este valor según la altura de tu navbar. 
       Si tu navbar es alto, prueba con 80px o 100px 
    */
    padding-top: 75px; 
}

/* Si usas un sidebar y un main-content, asegúrate de que el padding 
   esté en el contenedor correcto */
.main-content { 
    padding: 1.5rem; 
    min-height: calc(100-vh - 75px);
}

/* Mejora opcional: que el header de la página tenga un margen superior extra */
.page-header {
    margin-top: 10px;
    margin-bottom: 25px;
}
        .card-custom { 
            border: none; 
            border-radius: 16px; 
            box-shadow: 0 4px 12px rgba(0,0,0,0.03); 
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
        }

        /* Tabla Estilizada */
        .table thead th { 
            background-color: #fcfcfd; 
            color: #64748b; 
            font-weight: 700;
            text-transform: uppercase; 
            font-size: 0.7rem; 
            letter-spacing: 0.05em;
            padding: 1.25rem;
            border-bottom: 2px solid #f1f5f9;
        }
        .table tbody td { padding: 1rem 1.25rem; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
        
        /* Badges Modernos (Glassmorphism light) */
        .badge-mov { 
            min-width: 90px; 
            padding: 6px 12px; 
            border-radius: 8px; 
            font-weight: 700; 
            font-size: 0.65rem; 
            letter-spacing: 0.03em;
            text-transform: uppercase;
        }
        .bg-opacity-10 { background-color: rgba(var(--bs-primary-rgb), 0.1) !important; }

        /* Rutas Origen -> Destino */
        .ruta-pill {
            display: inline-flex;
            align-items: center;
            background: #fff;
            border: 1px solid #e2e8f0;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            color: #475569;
        }
        .ruta-arrow { color: #94a3b8; margin: 0 8px; font-size: 0.9rem; }

        /* Form Controls */
        .form-select, .form-control { 
            border: 1px solid #e2e8f0; 
            border-radius: 10px; 
            padding: 0.6rem 0.8rem;
            font-size: 0.85rem;
            transition: all 0.2s;
        }
        .form-select:focus { border-color: #3b82f6; box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1); }
        .input-disabled { background-color: #f8fafc !important; color: #cbd5e1; cursor: not-allowed; }
    </style>
</head>
<body>

    <?php renderizarLayout($paginaActual); ?>

    <div class="main-content">
        <div class="container-fluid">
            
            <div class="d-flex justify-content-between align-items-end mb-4">
                <div>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-1">
                            <li class="breadcrumb-item small"><a href="#">Inventario</a></li>
                            <li class="breadcrumb-item small active">Historial</li>
                        </ol>
                    </nav>
                    <h2 class="fw-bold m-0 text-dark">Movimientos de Stock</h2>
                </div>
                <div id="loader" class="spinner-border text-primary d-none" role="status"></div>
            </div>

            <div class="card card-custom mb-4">
                <div class="card-body p-4">
                    <form id="formFiltros" class="row g-3 align-items-end">
                        <div class="col-md-2">
                            <label class="form-label small fw-bold text-muted">PERIODO</label>
                            <select id="selectorPeriodo" class="form-select border-0 bg-light">
                                <option value="hoy">Hoy</option>
                                <option value="ayer">Ayer</option>
                                <option value="semana">Últimos 7 días</option>
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

                        <div class="col-md-2">
                            <label class="form-label small fw-bold text-muted">ALMACÉN</label>
                            <select id="filtroAlmacen" class="form-select border-0 bg-light" <?= ($almacen_usuario > 0) ? 'disabled' : '' ?>>
                                <?php if($almacen_usuario == 0): ?>
                                    <option value="0">-- Todos --</option>
                                    <?php 
                                    $q_alm = $conexion->query("SELECT id, nombre FROM almacenes WHERE activo = 1 ORDER BY nombre");
                                    while($a = $q_alm->fetch_assoc()): ?>
                                        <option value="<?= $a['id'] ?>"><?= $a['nombre'] ?></option>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <option value="<?= $almacen_usuario ?>">Mi Almacén</option>
                                <?php endif; ?>
                            </select>
                        </div>
                        
                        <div class="col-md-2">
                            <label class="form-label small fw-bold text-muted">TIPO</label>
                            <select id="filtroTipo" class="form-select border-0 bg-light">
                                <option value="">Todos</option>
                                <option value="entrada">Entradas</option>
                                <option value="salida">Salidas</option>
                                <option value="traspaso">Traspasos</option>
                                <option value="ajuste">Ajustes</option>
                            </select>
                        </div>
                        
                        <div class="col-md-2">
                            <button type="button" id="btnReset" class="btn btn-dark w-100 rounded-pill fw-bold border-0 shadow-sm">
                                <i class="bi bi-arrow-counterclockwise"></i> Limpiar
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card card-custom overflow-hidden">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table id="tablaHistorial" class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th class="ps-4">Fecha / Hora</th>
                                    <th>Producto</th>
                                    <th class="text-center">Operación</th>
                                    <th class="text-center">Cant.</th>
                                    <th>Trayectoria (Origen ➔ Destino)</th>
                                    <th>Usuario</th>
                                    <th class="no-export pe-4 text-end">PDF</th>
                                </tr>
                            </thead>
                            <tbody class="text-secondary" style="font-size: 0.85rem;"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php cargarScripts(); ?>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>

    <script>
    $(document).ready(function() {
        const almacenUsuarioSesion = <?= $almacen_usuario ?>;

        const tabla = $('#tablaHistorial').DataTable({
            language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json' },
            dom: '<"d-flex justify-content-between p-3 border-bottom"Bf>rt<"p-3"ip>',
            buttons: [{
                extend: 'pdfHtml5',
                text: '<i class="bi bi-file-pdf"></i> Reporte PDF',
                className: 'btn btn-outline-danger btn-sm px-4 rounded-pill',
                orientation: 'landscape',
                exportOptions: { columns: [0, 1, 2, 3, 4, 5] }
            }],
            order: [[0, 'desc']],
            pageLength: 20
        });

        function formatQty(m) {
            const cant = parseFloat(m.cantidad);
            const factor = parseFloat(m.factor_conversion);
            if(factor > 1 && cant >= factor) {
                const uReporte = Math.floor(cant / factor);
                const resto = Math.round((cant % factor) * 100) / 100;
                return `<div class="fw-bold text-dark">${uReporte} ${m.unidad_reporte}</div>` +
                       (resto > 0 ? `<small class="text-muted">+ ${resto} pzas</small>` : '');
            }
            return `<div class="fw-bold text-dark">${cant} <small class="fw-normal">pzas</small></div>`;
        }

        function cargarHistorial() {
            // Si el select de almacén está habilitado, lo usamos, si no (es usuario normal) usamos el valor de sesión
            const idAlmacenBusqueda = (almacenUsuarioSesion > 0) ? almacenUsuarioSesion : $('#filtroAlmacen').val();

            $('#loader').removeClass('d-none');

            $.ajax({
                url: 'movimientosController.php', // Cambia a la ruta de tu controlador
                data: {
                    ajax: 1,
                    periodo: $('#selectorPeriodo').val(),
                    f_inicio: $('#f_inicio').val(),
                    f_fin: $('#f_fin').val(),
                    tipo: $('#filtroTipo').val(),
                    almacen_id: idAlmacenBusqueda
                },
                dataType: 'json',
                success: function(res) {
                    tabla.clear();
                    if(res.data) {
                        res.data.forEach(m => {
                            // Resaltamos el nombre del almacén si es el filtrado
                            const labelOri = (m.almacen_origen_id == idAlmacenBusqueda) ? `<strong>${m.origen}</strong>` : m.origen;
                            const labelDes = (m.almacen_destino_id == idAlmacenBusqueda) ? `<strong>${m.destino}</strong>` : m.destino;

                            tabla.row.add([
                                `<span class="ps-3 text-dark fw-bold">${m.fecha_format}</span>`,
                                `<div><div class="text-dark fw-bold">${m.producto}</div><small class="text-primary">${m.sku}</small></div>`,
                                `<div class="text-center"><span class="badge badge-mov bg-${m.color} bg-opacity-10 text-${m.color} border border-${m.color} border-opacity-25">${m.tipo}</span></div>`,
                                `<div class="text-center">${formatQty(m)}</div>`,
                                `<div><div class="ruta-pill">${labelOri} <i class="bi bi-arrow-right ruta-arrow"></i> ${labelDes}</div></div>`,
                                `<div><i class="bi bi-person-circle me-1 text-muted"></i> ${m.u_reg}</div>`,
                                `<div class="pe-3 text-end"><a href="imprimir.php?id=${m.id}" target="_blank" class="btn btn-sm btn-white border shadow-xs"><i class="bi bi-printer text-danger"></i></a></div>`
                            ]);
                        });
                    }
                    tabla.draw();
                },
                complete: () => $('#loader').addClass('d-none')
            });
        }

        // Eventos
        $('#selectorPeriodo').on('change', function() {
            const isPerso = $(this).val() === 'personalizado';
            $('#f_inicio, #f_fin').prop('disabled', !isPerso).toggleClass('input-disabled', !isPerso);
            if(!isPerso) cargarHistorial();
        });

        $('#f_inicio, #f_fin, #filtroTipo, #filtroAlmacen').on('change', cargarHistorial);
        
        $('#btnReset').on('click', () => { 
            $('#formFiltros')[0].reset(); 
            $('#f_inicio, #f_fin').prop('disabled', true).addClass('input-disabled');
            cargarHistorial(); 
        });

        // Carga inicial
        cargarHistorial();
    });
    </script>
</body>
</html>