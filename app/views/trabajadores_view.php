<?php
/**
 * trabajadores_view.php
 * Vista de administración de personal: Filtros, CRUD por Modales y AJAX.
 */
// Definimos los roles y estados que coinciden con el ENUM de la BD para validación visual
$rolesEnum = ['administrador', 'vendedor', 'chofer', 'almacenista', 'cargador'];
$estadosEnum = ['activo', 'inactivo', 'vacaciones', 'en_ruta'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Personal | Sistema</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  
  
  <?php require_once __DIR__ . '/layout/icono.php' ?>
    <?php if (function_exists('cargarEstilos')) { cargarEstilos(); } ?>
     <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <style>
        :root { --sidebar-width: 260px; --navbar-height: 65px; }
        body { background-color: #f4f7f6; }
        .main-content { margin-left: var(--sidebar-width); padding: 40px; padding-top: calc(var(--navbar-height) + 20px); }
        .card-table { border: none; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); background: white; }
        
        /* Estilo Micro-Widget iOS */
        .ios-micro-card {
            background: #ffffff !important;
            border-radius: 12px !important;
            border: 1px solid rgba(0,0,0,0.05) !important;
            padding: 4px 10px !important;
            min-width: 85px !important;
            box-shadow: 0 2px 4px rgba(0,0,0,0.02) !important;
            display: flex !important;
            flex-direction: column !important;
            justify-content: center !important;
            border-left: 3px solid #34c759 !important; /* Verde iOS */
        }
        .ios-m-label { 
            color: #8e8e93; font-size: 0.55rem; font-weight: 700; 
            text-transform: uppercase; letter-spacing: 0.05em; line-height: 1.1; margin: 0;
        }
        .ios-m-value { 
            color: #1c1c1e; font-size: 1rem; font-weight: 700; 
            letter-spacing: -0.02em; line-height: 1; margin-top: 1px;
        }

        @media (max-width: 768px) { 
            .main-content { margin-left: 0; padding: 20px; padding-top: 90px; } 
        }
    </style>
</head>
<body>
    <?php if (function_exists('renderizarLayout')) { renderizarLayout($paginaActual); } ?>

    <main class="main-content">
        <div class="d-flex justify-content-between align-items-center flex-wrap mb-4" style="gap: 15px; width: 100%;">
            <div style="flex: 1; min-width: 200px;">
                <h2 class="fw-bold m-0" style="letter-spacing: -0.02em; color: #1c1c1e;">Gestión de Personal</h2>
                <p class="text-muted mb-0" style="font-size: 0.85rem;">Control de trabajadores, roles y disponibilidad</p>
            </div>

            <div class="d-flex align-items-center" style="gap: 12px;">
                <div class="ios-micro-card">
                    <p class="ios-m-label">Staff Total</p>
                    <div class="ios-m-value" id="conteoTrabajadores">
                        <?= count($trabajadores) ?>
                    </div>
                </div>

                <button class="btn btn-primary rounded-pill px-4 shadow-sm" onclick="nuevoTrabajador()" style="height: 34px; font-weight: 600; font-size: 0.85rem;">
                    <i class="bi bi-person-plus-fill me-1"></i> Agregar
                </button>
            </div>
        </div>

        <div class="card card-table p-4">
            <div class="row mb-4 g-3">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                        <input type="text" id="busquedaTrabajador" class="form-control border-start-0" placeholder="Buscar por nombre o teléfono...">
                    </div>
                </div>
                <div class="col-md-3">
                    <select id="filtroRol" class="form-select">
                        <option value="">Todos los Roles</option>
                        <?php foreach($rolesEnum as $rol): ?>
                            <option value="<?= $rol ?>"><?= ucfirst($rol) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <button class="btn btn-outline-secondary w-100" onclick="limpiarFiltros()">
                        <i class="bi bi-arrow-clockwise"></i> Limpiar
                    </button>
                </div>
            </div>

            <div class="table-responsive">
                <table id="tablaTrabajadores" class="table table-hover align-middle w-100">
                    <thead class="table-dark">
                        <tr>
                            <th>Nombre</th>
                            <th>Teléfono</th>
                            <th>Rol / Puesto</th>
                            <th>Almacén</th>
                            <th>Estado</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($trabajadores as $t): ?>
                        <tr class="fila-trabajador" data-rol="<?= $t['rol'] ?>">
                            <td><strong><?= htmlspecialchars($t['nombre']) ?></strong></td>
                            <td>
                                <a href="https://wa.me/52<?= $t['telefono'] ?>" target="_blank" class="text-decoration-none text-dark small">
                                    <i class="bi bi-whatsapp text-success me-1"></i> <?= htmlspecialchars($t['telefono']) ?>
                                </a>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border fw-normal text-uppercase" style="font-size: 0.7rem;">
                                    <?= $t['rol'] ?>
                                </span>
                            </td>
                            <td>
                                <span class="small text-muted"><i class="bi bi-geo-alt"></i> ID: <?= $t['almacen_id'] ?></span>
                            </td>
                            <td>
                                <?php 
                                    $claseEstado = match($t['estado']) {
                                        'activo' => 'bg-success',
                                        'vacaciones' => 'bg-warning text-dark',
                                        default => 'bg-danger'
                                    };
                                ?>
                                <span class="badge rounded-pill <?= $claseEstado ?>" style="font-size: 0.7rem;">
                                    <?= strtoupper($t['estado']) ?>
                                </span>
                            </td>
                            <td class="text-end">
                                <div class="btn-group">
                                    <button class="btn btn-sm btn-outline-primary border-0" onclick="editarTrabajador(<?= htmlspecialchars(json_encode($t)) ?>)">
                                        <i class="bi bi-pencil-square fs-5"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger border-0" onclick="eliminarTrabajador(<?= $t['id'] ?>)">
                                        <i class="bi bi-trash fs-5"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <div class="modal fade" id="modalTrabajador" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <form id="formTrabajador">
                    <div class="modal-header bg-dark text-white">
                        <h5 class="modal-title" id="modalTitulo">Nuevo Trabajador</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id" id="trabajador_id" value="0">
                        <input type="hidden" name="action" value="guardar">
                        
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label fw-bold small">Nombre Completo</label>
                                <input type="text" name="nombre" id="t_nombre" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">Teléfono</label>
                                <input type="text" name="telefono" id="t_telefono" class="form-control" maxlength="10" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">Puesto / Rol</label>
                                <select name="rol" id="t_rol" class="form-select" required>
                                    <?php foreach($rolesEnum as $rol): ?>
                                        <option value="<?= $rol ?>"><?= ucfirst($rol) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label fw-bold small">Almacén / Sucursal</label>
                                <?php if ($_SESSION['almacen_id'] == 0): ?>
                                    <select name="almacen_id" id="t_almacen_id" class="form-select" required>
                                        <option value="">Seleccionar Almacén...</option>
                                        <?php foreach($listaAlmacenes as $alm): ?>
                                            <option value="<?= $alm['id'] ?>"><?= htmlspecialchars($alm['nombre']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                <?php else: ?>
                                    <input type="text" class="form-control bg-light" value="Asignación Automática" readonly>
                                    <input type="hidden" name="almacen_id" id="t_almacen_id" value="<?= $_SESSION['almacen_id'] ?>">
                                <?php endif; ?>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label fw-bold small">Estado Laboral</label>
                                <select name="estado" id="t_estado" class="form-select">
                                    <?php foreach($estadosEnum as $est): ?>
                                        <option value="<?= $est ?>"><?= ucfirst($est) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-0">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary px-4">Guardar Cambios</button>
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
        tabla = $('#tablaTrabajadores').DataTable({
            "language": { "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json" },
            "dom": 'rt<"row mt-3"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
            "pageLength": 10,
            "order": [[0, 'asc']]
        });

        $('#busquedaTrabajador').on('keyup', function() {
            tabla.search(this.value).draw();
        });

        $('#filtroRol').on('change', function() {
            const val = $(this).val();
            tabla.column(2).search(val ? `^${val}$` : '', true, false).draw();
        });
    });

    function nuevoTrabajador() {
        $('#formTrabajador')[0].reset();
        $('#trabajador_id').val('0');
        // Si el select de almacén existe, resetearlo también
        if ($('#t_almacen_id').is('select')) $('#t_almacen_id').val('');
        $('#modalTitulo').text('Nuevo Trabajador');
        $('#modalTrabajador').modal('show');
    }

    function editarTrabajador(t) {
        $('#modalTitulo').text('Editar Trabajador');
        $('#trabajador_id').val(t.id);
        $('#t_nombre').val(t.nombre);
        $('#t_telefono').val(t.telefono);
        $('#t_rol').val(t.rol);
        $('#t_estado').val(t.estado);
        // Seteamos el almacén
        if ($('#t_almacen_id').is('select')) {
            $('#t_almacen_id').val(t.almacen_id);
        }
        $('#modalTrabajador').modal('show');
    }

    $('#formTrabajador').on('submit', async function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        
        try {
            const resp = await fetch('/cfsistem/app/controllers/trabajadoresController.php', { method: 'POST', body: formData });
            const res = await resp.json();
            if (res.status === 'success') {
                Swal.fire({ icon: 'success', title: '¡Éxito!', showConfirmButton: false, timer: 1000 })
                .then(() => location.reload());
            } else {
                Swal.fire('Error', res.message, 'error');
            }
        } catch (e) { Swal.fire('Error', 'No se pudo guardar', 'error'); }
    });

    async function eliminarTrabajador(id) {
        const result = await Swal.fire({
            title: '¿Eliminar trabajador?',
            text: "Esta acción no se puede deshacer",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Sí, eliminar'
        });

        if (result.isConfirmed) {
            const fd = new FormData();
            fd.append('action', 'eliminar');
            fd.append('id', id);
            const resp = await fetch('/cfsistem/app/controllers/trabajadoresController.php', { method: 'POST', body: fd });
            const res = await resp.json();
            if(res.status === 'success') location.reload();
        }
    }

    function limpiarFiltros() {
        $('#busquedaTrabajador').val('');
        $('#filtroRol').val('');
        tabla.search('').column(2).search('').draw();
    }
    </script>
</body>
</html>