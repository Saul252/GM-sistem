<?php
/**
 * vehiculos_view.php 
 * Versión ajustada para segmentación por Almacén - cfsistem
 */
$estadosUnidad = [
    'disponible'     => ['label' => 'Disponible', 'class' => 'st-disponible', 'dot' => '#28a745'],
    'en_ruta'        => ['label' => 'En Ruta', 'class' => 'st-ruta', 'dot' => '#007aff'],
    'mantenimiento'  => ['label' => 'Taller', 'class' => 'st-taller', 'dot' => '#ff9500'],
    'fuera_servicio' => ['label' => 'Fuera de Servicio', 'class' => 'st-fuera', 'dot' => '#ff3b30']
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transporte | cfsistem</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    
    <?php if (function_exists('cargarEstilos')) { cargarEstilos(); } ?>
  
    <style>
        :root { 
            --sidebar-width: 260px; 
            --navbar-height: 65px;
            --glass-bg: rgba(255, 255, 255, 0.85);
            --accent-color: #007aff;
        }

        body { 
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
        }

        .main-content { 
            margin-left: var(--sidebar-width); 
            padding: 40px; 
            padding-top: calc(var(--navbar-height) + 20px); 
            transition: all 0.3s ease;
        }

        .card-premium {
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.3);
            border-radius: 24px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.05);
            padding: 30px;
        }

        .ios-micro-card {
            background: white;
            border-radius: 18px;
            padding: 12px 20px;
            border: none;
            box-shadow: 0 4px 15px rgba(0,0,0,0.03);
            display: flex;
            align-items: center;
            gap: 15px;
            transition: transform 0.2s;
        }
        .ios-micro-card:hover { transform: translateY(-3px); }
        .ios-icon-circle {
            width: 40px; height: 40px;
            background: #eef6ff;
            color: var(--accent-color);
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.2rem;
        }

        .badge-premium {
            padding: 8px 14px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 0.72rem;
            letter-spacing: 0.5px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border: 1px solid rgba(0,0,0,0.05);
        }
        .st-disponible { background: #e6ffed; color: #1e7e34; }
        .st-ruta { background: #e8f4ff; color: #007aff; }
        .st-taller { background: #fff8e6; color: #d97706; }
        .st-fuera { background: #fff0f0; color: #d11a2a; }

        .table thead th {
            background: transparent;
            color: #8e8e93;
            font-weight: 700;
            font-size: 0.75rem;
            text-transform: uppercase;
            border: none;
            padding-bottom: 20px;
        }
        .table tbody tr {
            border-bottom: 1px solid #f2f2f7;
            transition: background 0.2s;
        }
        .table tbody tr:hover { background: rgba(0,122,255,0.02); }

        .btn-gradient {
            background: linear-gradient(135deg, #007aff 0%, #0056b3 100%);
            color: white; border: none; border-radius: 12px;
            padding: 10px 25px; font-weight: 600;
            box-shadow: 0 8px 20px rgba(0,122,255,0.25);
        }
        .btn-gradient:hover { color: white; opacity: 0.9; transform: translateY(-1px); }

        @media (max-width: 768px) { .main-content { margin-left: 0; padding: 20px; padding-top: 100px; } }
    </style>
</head>
<body>
    <?php if (function_exists('renderizarLayout')) { renderizarLayout($paginaActual); } ?>

    <main class="main-content">
        <div class="d-flex justify-content-between align-items-end flex-wrap mb-5" style="gap: 20px;">
            <div>
                <h1 class="fw-bold m-0 text-dark" style="letter-spacing: -1px;">Flota Vehicular</h1>
                <span class="text-muted fw-medium">Panel de control logístico de cfsistem</span>
            </div>

            <div class="d-flex align-items-center gap-3">
                <div class="ios-micro-card">
                    <div class="ios-icon-circle"><i class="bi bi-truck"></i></div>
                    <div>
                        <p class="ios-m-label mb-0">TOTAL</p>
                        <div class="ios-m-value fs-5"><?= count($vehiculos) ?> Unid.</div>
                    </div>
                </div>

                <button class="btn btn-gradient btn-lg px-4" onclick="nuevoVehiculo()">
                    <i class="bi bi-plus-lg me-2"></i>Nueva Unidad
                </button>
            </div>
        </div>

        <div class="card card-premium">
            <div class="row mb-4 g-3 align-items-center">
                <div class="col-md-5">
                    <div class="input-group search-box shadow-sm rounded-4 overflow-hidden">
                        <span class="input-group-text bg-white border-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" id="busquedaVehiculo" class="form-control border-0 p-3" placeholder="Buscar por placa, VIN o nombre...">
                    </div>
                </div>
                <div class="col-md-3">
                    <select id="filtroEstado" class="form-select border-0 shadow-sm p-3 rounded-4 fw-medium">
                        <option value="">Todos los estados</option>
                        <?php foreach($estadosUnidad as $key => $val): ?>
                            <option value="<?= $key ?>"><?= $val['label'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col text-end">
                    <button class="btn btn-light rounded-4 p-3 border shadow-sm fw-bold" onclick="limpiarFiltros()">
                        <i class="bi bi-arrow-counterclockwise me-2"></i>Reset
                    </button>
                </div>
            </div>

            <div class="table-responsive">
                <table id="tablaVehiculos" class="table align-middle">
                    <thead>
                        <tr>
                            <th>Unidad</th>
                            <th>Identificación</th>
                            <th>Capacidad</th>
                            <?php if ($_SESSION['almacen_id'] == 0): ?><th>Almacén</th><?php endif; ?>
                            <th>Estado</th>
                            <th class="text-end">Opciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($vehiculos as $v): ?>
                        <tr data-estado="<?= $v['estado_unidad'] ?>">
                            <td class="py-4">
                                <div class="d-flex align-items-center">
                                    <div class="p-3 bg-white rounded-4 shadow-sm me-3">
                                        <i class="bi bi-truck-front text-primary fs-4"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark fs-6"><?= htmlspecialchars($v['nombre']) ?></div>
                                        <span class="text-muted small">Mod. <?= $v['modelo_año'] ?: 'N/A' ?></span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="badge bg-white text-dark border px-3 py-2 rounded-pill shadow-xs mb-1">
                                    <i class="bi bi-card-text me-2 text-primary"></i><strong><?= htmlspecialchars($v['placas']) ?></strong>
                                </div>
                                <div class="text-muted small ps-2">VIN: <?= htmlspecialchars($v['serie_vin'] ?: '---') ?></div>
                            </td>
                            <td>
                                <div class="fw-bold">
                                    <span class="fs-5"><?= number_format($v['capacidad_carga_kg'], 0) ?></span>
                                    <small class="text-muted fw-normal">kg</small>
                                </div>
                            </td>
                            <?php if ($_SESSION['almacen_id'] == 0): ?>
                            <td>
                                <span class="small text-muted"><i class="bi bi-geo-alt"></i> ID: <?= $v['almacen_id'] ?></span>
                            </td>
                            <?php endif; ?>
                            <td>
                                <span class="badge-premium <?= $estadosUnidad[$v['estado_unidad']]['class'] ?>">
                                    <span style="height: 8px; width: 8px; background:<?= $estadosUnidad[$v['estado_unidad']]['dot'] ?>; border-radius:50%"></span>
                                    <?= strtoupper($estadosUnidad[$v['estado_unidad']]['label']) ?>
                                </span>
                            </td>
                            <td class="text-end">
                                <button class="btn btn-white btn-sm rounded-3 shadow-sm border me-2" onclick='editarVehiculo(<?= json_encode($v) ?>)'>
                                    <i class="bi bi-pencil-fill text-primary"></i>
                                </button>
                                <button class="btn btn-white btn-sm rounded-3 shadow-sm border" onclick="eliminarVehiculo(<?= $v['id'] ?>)">
                                    <i class="bi bi-trash-fill text-danger"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <div class="modal fade" id="modalVehiculo" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-2xl" style="border-radius: 30px; overflow: hidden;">
                <form id="formVehiculo">
                    <div class="modal-header border-0 bg-white p-4 pb-0">
                        <h4 class="fw-bold text-dark" id="modalTitulo">Detalles Técnicos</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4 row g-4">
                        <input type="hidden" name="id" id="v_id" value="0">
                        <input type="hidden" name="action" value="guardar">
                        
                        <div class="col-12">
                            <label class="form-label fw-bold text-secondary">Descripción de Unidad</label>
                            <input type="text" name="nombre" id="v_nombre" class="form-control form-control-lg bg-light border-0 rounded-4" placeholder="Ej. Kenworth T680" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-secondary">Placas</label>
                            <input type="text" name="placas" id="v_placas" class="form-control bg-light border-0 rounded-4 text-uppercase" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-secondary">Capacidad (kg)</label>
                            <input type="number" step="0.01" name="capacidad_carga_kg" id="v_capacidad" class="form-control bg-light border-0 rounded-4">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-secondary">Estado</label>
                            <select name="estado_unidad" id="v_estado" class="form-select bg-light border-0 rounded-4 fw-medium">
                                <?php foreach($estadosUnidad as $key => $val): ?>
                                    <option value="<?= $key ?>"><?= $val['label'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-secondary">Año</label>
                            <input type="number" name="modelo_año" id="v_modelo" class="form-control bg-light border-0 rounded-4">
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-bold text-secondary">Almacén Asignado</label>
                            <?php if ($_SESSION['almacen_id'] == 0): ?>
                                <select name="almacen_id" id="v_almacen_id" class="form-select bg-light border-0 rounded-4 fw-medium" required>
                                    <option value="">Seleccionar Almacén...</option>
                                    <?php foreach($listaAlmacenes as $alm): ?>
                                        <option value="<?= $alm['id'] ?>"><?= htmlspecialchars($alm['nombre']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            <?php else: ?>
                                <input type="text" class="form-control bg-light border-0 rounded-4" value="Sucursal Actual" readonly>
                                <input type="hidden" name="almacen_id" id="v_almacen_id" value="<?= $_SESSION['almacen_id'] ?>">
                            <?php endif; ?>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-bold text-secondary">VIN / Serie</label>
                            <input type="text" name="serie_vin" id="v_vin" class="form-control bg-light border-0 rounded-4 text-uppercase">
                        </div>
                    </div>
                    <div class="modal-footer border-0 p-4">
                        <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-gradient px-5">Confirmar y Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

   <script>
let tabla;

$(document).ready(function() {
    tabla = $('#tablaVehiculos').DataTable({
        "language": { "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json" },
        "dom": 'rt<"d-flex justify-content-between align-items-center mt-4 px-2"ip>',
        "pageLength": 8,
        "responsive": true,
        "columnDefs": [
            { "orderable": false, "targets": <?php echo ($_SESSION['almacen_id'] == 0) ? '5' : '4'; ?> }
        ]
    });

    $('#busquedaVehiculo').on('keyup', function() { tabla.search(this.value).draw(); });

    $('#filtroEstado').on('change', function() {
        const val = $(this).val();
        $.fn.dataTable.ext.search.pop();
        if (val !== "") {
            $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
                const rowEstado = $(tabla.row(dataIndex).node()).attr('data-estado');
                return rowEstado === val;
            });
        }
        tabla.draw();
    });
});

function nuevoVehiculo() {
    $('#formVehiculo')[0].reset();
    $('#v_id').val('0');
    if ($('#v_almacen_id').is('select')) $('#v_almacen_id').val('');
    $('#modalTitulo').html('<i class="bi bi-plus-circle me-2"></i>Añadir Nueva Unidad');
    $('#modalVehiculo').modal('show');
}

function editarVehiculo(v) {
    $('#modalTitulo').html('<i class="bi bi-pencil-square me-2"></i>Gestionar Unidad');
    $('#v_id').val(v.id);
    $('#v_nombre').val(v.nombre);
    $('#v_placas').val(v.placas);
    $('#v_modelo').val(v.modelo_año);
    $('#v_capacidad').val(v.capacidad_carga_kg);
    $('#v_estado').val(v.estado_unidad);
    $('#v_vin').val(v.serie_vin);
    if ($('#v_almacen_id').is('select')) $('#v_almacen_id').val(v.almacen_id);
    $('#modalVehiculo').modal('show');
}

$('#formVehiculo').on('submit', async function(e) {
    e.preventDefault();
    Swal.fire({ title: 'Procesando...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
    try {
        const formData = new FormData(this);
        const resp = await fetch('/cfsistem/app/controllers/vehiculosController.php', { method: 'POST', body: formData });
        const res = await resp.json();
        if (res.status === 'success') {
            Swal.fire({ icon: 'success', title: '¡Éxito!', timer: 1500, showConfirmButton: false }).then(() => location.reload());
        } else {
            Swal.fire('Error', res.message || 'No se pudo guardar', 'error');
        }
    } catch (error) { Swal.fire('Error', 'Error de Conexión', 'error'); }
});

async function eliminarVehiculo(id) {
    const confirmacion = await Swal.fire({
        title: '¿Confirmar Baja?',
        text: "La unidad se marcará como inactiva.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ff3b30',
        confirmButtonText: 'Sí, dar de baja'
    });
    if (confirmacion.isConfirmed) {
        const fd = new FormData();
        fd.append('action', 'eliminar');
        fd.append('id', id);
        const resp = await fetch('/cfsistem/app/controllers/vehiculosController.php', { method: 'POST', body: fd });
        const res = await resp.json();
        if (res.status === 'success') location.reload();
    }
}

function limpiarFiltros() {
    $('#busquedaVehiculo').val('');
    $('#filtroEstado').val('');
    $.fn.dataTable.ext.search.pop();
    tabla.search('').draw();
}
</script>
</body>
</html>