<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../../includes/auth.php';
protegerPagina(); 

require_once __DIR__ . '/../../includes/sidebar.php';
require_once __DIR__ . '/../../config/conexion.php';

$paginaActual = 'Usuarios';

/* ================= 1. CONSULTA DE USUARIOS ================= */
$sqlUsuarios = "SELECT 
                    u.id, 
                    u.nombre, 
                    u.username, 
                    u.rol_id, 
                    u.almacen_id, 
                    u.activo,
                    r.nombre AS rol_nombre,
                    IFNULL(a.nombre, 'Acceso Global') AS almacen_nombre
                FROM usuarios u
                LEFT JOIN roles r ON u.rol_id = r.id
                LEFT JOIN almacenes a ON u.almacen_id = a.id
                ORDER BY u.nombre ASC";

$resultUsuarios = $conexion->query($sqlUsuarios);
$usuarios = [];
if ($resultUsuarios) {
    while ($row = $resultUsuarios->fetch_assoc()) {
        $usuarios[] = $row;
    }
}

/* ================= 2. CONSULTA PARA SELECTORES ================= */
$rolesQuery = $conexion->query("SELECT id, nombre FROM roles ORDER BY nombre ASC");
$almacenesQuery = $conexion->query("SELECT id, nombre FROM almacenes WHERE activo = 1 ORDER BY nombre ASC");

// Guardamos roles en un array para usarlos en el JS de edición
$rolesArray = [];
$rolesQuery->data_seek(0);
while($r = $rolesQuery->fetch_assoc()) $rolesArray[] = $r;

$almacenesArray = [];
$almacenesQuery->data_seek(0);
while($a = $almacenesQuery->fetch_assoc()) $almacenesArray[] = $a;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuarios | Sistema</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root { --sidebar-width: 260px; --navbar-height: 20px; }
        body { background-color: #f4f7f6; font-family: 'Inter', sans-serif; }
        
        .main-content {
            margin-left: var(--sidebar-width);
            padding: 40px;
            margin-top: var(--navbar-height);
            transition: all 0.3s;
        }

        .card-table { border: none; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); background: white; }
        .badge-role { padding: 6px 12px; border-radius: 8px; font-weight: 600; font-size: 0.8rem; display: inline-block; }
        
        /* Colores de Roles dinámicos */
        .role-1 { background: #e0e7ff; color: #4338ca; border: 1px solid #c7d2fe; } /* Admin */
        .role-2 { background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; } /* Almacén */
        .role-3 { background: #fef3c7; color: #92400e; border: 1px solid #fde68a; } /* Vendedor */

        .avatar-circle { font-weight: bold; text-transform: uppercase; flex-shrink: 0; }
        
        @media (max-width: 768px) { .main-content { margin-left: 0; padding: 20px; } }
    </style>
</head>
<body>

    <?php renderSidebar($paginaActual); ?>

    <main class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold m-0 text-dark">Personal del Sistema</h2>
                <p class="text-muted">Gestión de cuentas, roles y sucursales asignadas</p>
            </div>
            <button class="btn btn-primary rounded-pill px-4 shadow-sm" onclick="nuevoUsuario()">
                <i class="bi bi-person-plus-fill me-2"></i> Nuevo Usuario
            </button>
        </div>

        <div class="card card-table p-4">
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text bg-transparent border-end-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" id="busquedaReal" class="form-control border-start-0" placeholder="Buscar usuario o nombre...">
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th class="border-0">Nombre del Colaborador</th>
                            <th class="border-0">Usuario</th>
                            <th class="border-0">Rol</th>
                            <th class="border-0">Ubicación</th>
                            <th class="border-0 text-center">Estado</th>
                            <th class="border-0 text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="listaUsuarios">
                        <?php if (empty($usuarios)): ?>
                            <tr><td colspan="6" class="text-center py-4">No hay usuarios registrados</td></tr>
                        <?php else: ?>
                            <?php foreach ($usuarios as $u): ?>
                            <tr class="user-row">
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-circle me-3 bg-primary text-white d-flex align-items-center justify-content-center rounded-circle" style="width: 38px; height: 38px;">
                                            <?= strtoupper(substr($u['nombre'], 0, 1)) ?>
                                        </div>
                                        <span class="fw-bold text-dark"><?= htmlspecialchars($u['nombre']) ?></span>
                                    </div>
                                </td>
                                <td><span class="badge bg-light text-muted border">@<?= htmlspecialchars($u['username']) ?></span></td>
                                <td>
                                    <span class="badge-role role-<?= $u['rol_id'] ?>">
                                        <?= htmlspecialchars($u['rol_nombre']) ?>
                                    </span>
                                </td>
                                <td>
                                    <small class="text-muted"><i class="bi bi-geo-alt me-1"></i><?= htmlspecialchars($u['almacen_nombre']) ?></small>
                                </td>
                                <td class="text-center">
                                    <div class="form-check form-switch d-inline-block">
                                        <input class="form-check-input" type="checkbox" role="switch" <?= $u['activo'] == 1 ? 'checked' : '' ?> onchange="eliminarUsuario(<?= $u['id'] ?>)">
                                    </div>
                                </td>
                                <td class="text-end">
                                    <button class="btn btn-sm btn-light border" onclick="editarUsuario(<?= htmlspecialchars(json_encode($u)) ?>)">
                                        <i class="bi bi-pencil-square text-primary"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <div class="modal fade" id="modalUsuario" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form id="formUsuario" class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-dark text-white p-4">
                    <h5 class="modal-title fw-bold" id="modalTitle">Nuevo Usuario</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <input type="hidden" name="id" id="userId">
                    <input type="hidden" name="accion" value="guardar">
                    
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label small fw-bold">Nombre Completo</label>
                            <input type="text" name="nombre" id="userName" class="form-control bg-light" placeholder="Ej. Juan Pérez" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Nombre de Usuario</label>
                            <input type="text" name="username" id="userLogin" class="form-control bg-light" placeholder="juan.perez" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Contraseña</label>
                            <input type="password" name="password" id="userPass" class="form-control bg-light" placeholder="••••••••">
                            <small class="text-muted d-block mt-1" id="passNote" style="display:none;">Vacío para no cambiar</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Rol de Acceso</label>
                            <select name="rol_id" id="userRol" class="form-select bg-light" required>
                                <option value="">Seleccione...</option>
                                <?php foreach($rolesArray as $r): ?>
                                    <option value="<?= $r['id'] ?>"><?= $r['nombre'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Almacén Asignado</label>
                            <select name="almacen_id" id="userAlmacen" class="form-select bg-light">
                                <option value="">Acceso Global</option>
                                <?php foreach($almacenesArray as $a): ?>
                                    <option value="<?= $a['id'] ?>"><?= $a['nombre'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light p-3">
                    <button type="button" class="btn btn-secondary border-0" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary px-4 shadow-sm">Guardar Usuario</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        const modalEl = new bootstrap.Modal('#modalUsuario');

        // Buscador Instantáneo
        $("#busquedaReal").on("keyup", function() {
            let value = $(this).val().toLowerCase();
            $("#listaUsuarios tr").filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });

        function nuevoUsuario() {
            $('#formUsuario')[0].reset();
            $('#userId').val(0);
            $('#modalTitle').text('Registrar Nuevo Colaborador');
            $('#userPass').prop('required', true);
            $('#passNote').hide();
            modalEl.show();
        }

        function editarUsuario(u) {
            $('#userId').val(u.id);
            $('#userName').val(u.nombre);
            $('#userLogin').val(u.username);
            $('#userRol').val(u.rol_id);
            $('#userAlmacen').val(u.almacen_id);
            $('#userPass').prop('required', false).val('');
            $('#modalTitle').text('Actualizar Usuario');
            $('#passNote').show();
            modalEl.show();
        }

        // Envío de Formulario
        $('#formUsuario').on('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);

            fetch('/cfsistem/app/backend/usuarios/crud_usuarios.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(res => {
                if(res.status === 'success') {
                    Swal.fire({ icon: 'success', title: res.message, showConfirmButton: false, timer: 1500 })
                    .then(() => location.reload());
                } else {
                    Swal.fire('Error', res.message, 'error');
                }
            });
        });

        // Activar/Desactivar
        function eliminarUsuario(id) {
            const formData = new FormData();
            formData.append('accion', 'eliminar');
            formData.append('id', id);

            fetch('/cfsistem/app/backend/usuarios/crud_usuarios.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(res => {
                if(res.status !== 'success') {
                    Swal.fire('Error', res.message, 'error').then(() => location.reload());
                }
            });
        }
    </script>
</body>
</html>