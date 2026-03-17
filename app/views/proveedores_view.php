<?php
/**
 * Vista de Proveedores - Sistema CFDI
 */
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proveedores | Sistema</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    
    <?php if (function_exists('cargarEstilos')) { cargarEstilos(); } ?>
    
   <style>
    .card-table { 
        border: none; 
        border-radius: 15px; 
        box-shadow: 0 10px 30px rgba(0,0,0,0.05); 
        background: white; 
    }
    .fila-inactiva { 
        opacity: 0.5; 
        filter: grayscale(1); 
        background-color: #f8f9fa; 
    }
    .main-content { 
        padding: 20px; 
        transition: all 0.3s; 
        /* AJUSTE AQUÍ: Margen superior para librar el Navbar */
        margin-top: 70px; 
    }
    @media (min-width: 768px) { 
        .main-content { 
            margin-left: 260px; 
            /* Mantenemos el margen arriba en escritorio */
            margin-top: 70px; 
        } 
    }
</style>
</head>

<body>

    <?php if (function_exists('renderizarLayout')) { renderizarLayout($tituloPagina); } ?>

    <div class="main-content">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold m-0"><i class="bi bi-truck text-primary me-2"></i> Proveedores</h2>
                    <p class="text-muted small">Gestión de suministros y cuentas por pagar</p>
                </div>
                <button class="btn btn-primary rounded-pill px-4 shadow-sm" onclick="nuevoProveedor()">
                    <i class="bi bi-plus-lg me-2"></i> Nuevo Proveedor
                </button>
            </div>

            <div class="card card-table p-4">
                <div class="table-responsive">
                    <table class="table table-hover align-middle w-100" id="tablaProveedores">
                        <thead class="table-light">
                            <tr>
                                <th>Nombre / RFC</th>
                                <th>Contacto</th>
                                <th>Estado</th>
                                <th class="text-end">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($proveedores as $p): ?>
                            <tr class="<?= $p['activo'] ? '' : 'fila-inactiva' ?>">
                                <td>
                                    <div class="fw-bold"><?= htmlspecialchars($p['nombre_comercial']) ?></div>
                                    <small class="text-muted"><?= htmlspecialchars($p['rfc'] ?: 'SIN RFC') ?></small>
                                </td>
                                <td>
                                    <div class="small"><i class="bi bi-envelope me-1"></i><?= $p['correo'] ?: 'S/C' ?></div>
                                    <div class="small"><i class="bi bi-telephone me-1"></i><?= $p['telefono'] ?: 'S/T' ?></div>
                                </td>
                                <td>
                                    <span class="badge rounded-pill <?= $p['activo'] ? 'bg-success' : 'bg-danger' ?>">
                                        <?= $p['activo'] ? 'Activo' : 'Inactivo' ?>
                                    </span>
                                </td>
                                <td class="text-end">
                                    <button class="btn btn-sm btn-outline-primary border-0" 
                                            onclick='editarProveedor(<?= json_encode($p) ?>)' title="Editar">
                                        <i class="bi bi-pencil-square fs-5"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger border-0" 
                                            onclick="eliminarProveedor(<?= $p['id'] ?>, <?= $p['activo'] ?>)" title="Cambiar Estado">
                                        <i class="bi <?= $p['activo'] ? 'bi-trash' : 'bi-arrow-counterclockwise' ?> fs-5"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalProveedor" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                <div class="modal-header bg-light py-3">
                    <h5 class="modal-title fw-bold" id="tituloModal">Nuevo Proveedor</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formProveedor">
                    <div class="modal-body p-4">
                        <input type="hidden" id="proveedor_id" name="id">
                        
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Nombre Comercial / Alias</label>
                            <input type="text" class="form-control rounded-pill" name="nombre_comercial" id="nombre_comercial" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Razón Social</label>
                            <input type="text" class="form-control rounded-pill" name="razon_social" id="razon_social">
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label small fw-bold">RFC</label>
                                <input type="text" class="form-control rounded-pill" name="rfc" id="rfc">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label small fw-bold">Teléfono</label>
                                <input type="text" class="form-control rounded-pill" name="telefono" id="telefono">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Correo Electrónico</label>
                            <input type="email" class="form-control rounded-pill" name="correo" id="correo">
                        </div>
                    </div>
                    <div class="modal-footer border-0 p-4">
                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Funciones Globales (Disponibles para los onclick)
        function nuevoProveedor() {
            $('#formProveedor')[0].reset();
            $('#proveedor_id').val('');
            $('#tituloModal').text('Nuevo Proveedor');
            $('#modalProveedor').modal('show');
        }

        function editarProveedor(datos) {
            $('#proveedor_id').val(datos.id);
            $('#nombre_comercial').val(datos.nombre_comercial);
            $('#razon_social').val(datos.razon_social);
            $('#rfc').val(datos.rfc);
            $('#telefono').val(datos.telefono);
            $('#correo').val(datos.correo);
            
            $('#tituloModal').text('Editar Proveedor');
            $('#modalProveedor').modal('show');
        }

        function eliminarProveedor(id, estadoActual) {
            const accion = estadoActual ? 'desactivar' : 'activar';
            Swal.fire({
                title: `¿Deseas ${accion} este proveedor?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, confirmar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post('/cfsistem/app/controllers/proveedoresController.php?action=cambiarEstado', {
                        id: id, 
                        estado: estadoActual ? 0 : 1 
                    }, function(res) {
                        if(res.success) location.reload();
                    }, 'json');
                }
            });
        }

        $(document).ready(function() {
            // DataTable
            $('#tablaProveedores').DataTable({
                language: { url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json' },
                order: [[2, 'desc'], [0, 'asc']]
            });

            // Guardar Formulario
            $('#formProveedor').on('submit', function(e) {
                e.preventDefault();
                $.post('/cfsistem/app/controllers/proveedoresController.php?action=guardar', $(this).serialize(), function(res) {
                    if(res.success) {
                        Swal.fire('¡Éxito!', res.message, 'success').then(() => location.reload());
                    } else {
                        Swal.fire('Error', res.message, 'error');
                    }
                }, 'json');
            });
        });
    </script>
</body>
</html>