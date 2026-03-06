<?php
// Definimos los usos de CFDI por si la variable no viene del controlador
$usosCFDI = [
    'G01' => 'Adquisición de mercancías',
    'G03' => 'Gastos en general',
    'P01' => 'Por definir',
    'CP01' => 'Pagos',
    'S01' => 'Sin efectos fiscales'
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clientes | Sistema</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

    <?php if (function_exists('cargarEstilos')) { cargarEstilos(); } ?>

    <style>
        :root { --sidebar-width: 260px; --navbar-height: 65px; }
        body { background-color: #f4f7f6; }
        .main-content { 
            margin-left: var(--sidebar-width); 
            padding: 40px; 
            padding-top: calc(var(--navbar-height) + 20px); 
            transition: all 0.3s ease; 
        }
        .card-table { border: none; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); background: white; }
        .fila-inactiva { opacity: 0.6; grayscale: 1; }
        @media (max-width: 768px) { .main-content { margin-left: 0; padding: 20px; padding-top: 90px; } }
    </style>
</head>
<body>

    <?php if (function_exists('renderizarLayout')) {
        renderizarLayout($paginaActual); 
    } ?>

    <main class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold m-0 text-dark">Cartera de Clientes</h2>
                <p class="text-muted">Administración de datos y estados de cuenta</p>
            </div>
            <button class="btn btn-primary rounded-pill px-4 shadow-sm" onclick="nuevoCliente()">
                <i class="bi bi-person-plus-fill me-2"></i> Nuevo Cliente
            </button>
        </div>

        <div class="card card-table p-4">
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="input-group shadow-sm">
                        <span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span>
                        <input type="text" id="busquedaCliente" class="form-control border-start-0" placeholder="Buscar cliente...">
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table id="tablaClientes" class="table table-hover align-middle w-100">
                    <thead>
                        <tr>
                            <th>Nombre Comercial</th>
                            <th>RFC / Datos</th>
                            <th>Contacto</th>
                            <th>Estado</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($clientes as $c): ?>
                        <tr class="<?= $c['activo'] ? '' : 'fila-inactiva' ?>">
                            <td>
                                <strong><?= htmlspecialchars($c['nombre_comercial']) ?></strong><br>
                                <small class="text-muted"><?= htmlspecialchars($c['razon_social']) ?></small>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border"><?= $c['rfc'] ?></span><br>
                                <small class="text-muted">CP: <?= $c['codigo_postal'] ?> | Uso: <?= $c['uso_cfdi'] ?></small>
                            </td>
                            <td>
                                <i class="bi bi-envelope small"></i> <?= $c['correo'] ?><br>
                                <i class="bi bi-telephone small"></i> <?= $c['telefono'] ?>
                            </td>
                            <td>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" role="switch" 
                                        <?= $c['activo'] ? 'checked' : '' ?> 
                                        onchange="cambiarEstado(<?= $c['id'] ?>, this.checked ? 1 : 0)">
                                </div>
                            </td>
                            <td class="text-end">
                                <button class="btn btn-sm btn-outline-primary border-0" onclick="editarCliente(<?= $c['id'] ?>)">
                                    <i class="bi bi-pencil-square fs-5"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <div class="modal fade" id="modalCliente" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <form id="formCliente" class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-dark text-white p-4">
                    <h5 class="modal-title fw-bold" id="modalTitle">Nuevo Cliente</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <input type="hidden" name="cliente_id" id="clienteId" value="0">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label small fw-bold">Nombre Comercial</label>
                            <input type="text" name="nombre_comercial" id="nombreCom" class="form-control bg-light" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">RFC</label>
                            <input type="text" name="rfc" id="rfcInput" class="form-control bg-light" maxlength="13" style="text-transform: uppercase;" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold">Razón Social</label>
                            <input type="text" name="razon_social" id="razonSocial" class="form-control bg-light">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">CP</label>
                            <input type="text" name="codigo_postal" id="cpInput" class="form-control bg-light" maxlength="5" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Régimen Fiscal</label>
                            <input type="text" name="regimen_fiscal" id="regimenInput" class="form-control bg-light">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Uso CFDI</label>
                            <select name="uso_cfdi" id="usoInput" class="form-select bg-light">
                                <?php foreach($usosCFDI as $clave => $desc): ?>
                                    <option value="<?= $clave ?>"><?= $clave ?> - <?= $desc ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Correo</label>
                            <input type="email" name="correo" id="correoInput" class="form-control bg-light">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Teléfono</label>
                            <input type="text" name="telefono" id="telInput" class="form-control bg-light">
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold">Dirección</label>
                            <textarea name="direccion" id="dirInput" class="form-control bg-light" rows="2"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light p-3">
                    <button type="button" class="btn btn-secondary border-0" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary px-4 shadow-sm">Guardar Cliente</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        const modalCl = new bootstrap.Modal('#modalCliente');
        let tabla;

        $(document).ready(function() {
            // Inicializar DataTable
            tabla = $('#tablaClientes').DataTable({
                "language": { "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json" },
                "dom": 'rtp',
                "pageLength": 10,
                "order": [[3, "desc"]] // Ordenar por activos primero
            });

            // Buscador personalizado
            $('#busquedaCliente').on('keyup', function() {
                tabla.search(this.value).draw();
            });
        });

        function nuevoCliente() {
            $('#formCliente')[0].reset();
            $('#clienteId').val(0);
            $('#modalTitle').text('Nuevo Cliente');
            modalCl.show();
        }

        async function editarCliente(id) {
            try {
                const response = await fetch(`clientesController.php?action=obtenerPorId&id=${id}`);
                const res = await response.json();
                
                if (res.success) {
                    const c = res.data;
                    $('#clienteId').val(c.id);
                    $('#nombreCom').val(c.nombre_comercial);
                    $('#razonSocial').val(c.razon_social);
                    $('#rfcInput').val(c.rfc);
                    $('#cpInput').val(c.codigo_postal);
                    $('#regimenInput').val(c.regimen_fiscal);
                    $('#usoInput').val(c.uso_cfdi);
                    $('#correoInput').val(c.correo);
                    $('#telInput').val(c.telefono);
                    $('#dirInput').val(c.direccion);
                    
                    $('#modalTitle').text('Editar Cliente');
                    modalCl.show();
                } else {
                    Swal.fire('Error', res.message, 'error');
                }
            } catch (error) {
                console.error(error);
            }
        }

        async function cambiarEstado(id, estado) {
            const formData = new FormData();
            formData.append('id', id);
            formData.append('estado', estado);

            try {
                const response = await fetch('clientesController.php?action=cambiarEstado', {
                    method: 'POST',
                    body: formData
                });
                const res = await response.json();
                if (res.success) {
                    Swal.fire({ icon: 'success', title: res.message, timer: 800, showConfirmButton: false })
                    .then(() => location.reload());
                }
            } catch (error) {
                Swal.fire('Error', 'No se pudo cambiar el estado', 'error');
            }
        }

        $('#formCliente').on('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            
            try {
                const response = await fetch('clientesController.php?action=guardar', {
                    method: 'POST',
                    body: formData
                });
                const res = await response.json();
                
                if (res.success) {
                    modalCl.hide();
                    Swal.fire({ icon: 'success', title: '¡Guardado!', text: res.message, timer: 1500 })
                    .then(() => location.reload());
                } else {
                    Swal.fire('Atención', res.message, 'warning');
                }
            } catch (error) {
                Swal.fire('Error', 'Ocurrió un error al procesar la solicitud', 'error');
            }
        });
    </script>
</body>
</html>