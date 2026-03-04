<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clientes | Sistema</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <style>
    :root { 
        --sidebar-width: 260px; 
        --navbar-height: 65px; /* Altura exacta de tu navbar-premium */
    }
    
    body { background-color: #f4f7f6; }

    .main-content { 
        margin-left: var(--sidebar-width); 
        padding: 40px; 
        padding-top: calc(var(--navbar-height) + 20px); /* Esto empuja el contenido hacia abajo */
        transition: all 0.3s ease; 
    }

    /* Cuando el sidebar se oculta, quitamos el margen izquierdo */
    body.sidebar-hidden .main-content { 
        margin-left: 0; 
    }

    .card-table { border: none; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); background: white; }
    .fila-inactiva { background-color: #fdfdfe !important; color: #9ca3af !important; }
    
    /* Aseguramos que el texto del navbar sea blanco */
    .navbar-premium * { color: white !important; }

    /* Ajuste para móviles */
    @media (max-width: 768px) {
        .main-content {
            margin-left: 0;
            padding: 20px;
            padding-top: calc(var(--navbar-height) + 20px);
        }
    }
</style>
</head>
<body>

    <?php renderSidebar($paginaActual); ?>

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
                <table id="tablaClientes" class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Nombre Comercial</th>
                            <th>RFC / CP</th>
                            <th>Contacto</th>
                            <th>Uso CFDI</th>
                            <th class="text-center">Estado</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
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
                    <input type="hidden" name="id" id="clienteId">
                    <input type="hidden" name="accion" id="accionInput" value="guardar">
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
                    <button type="submit" class="btn btn-primary px-4 shadow-sm">Guardar</button>
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
            tabla = $('#tablaClientes').DataTable({
                "ajax": "/cfsistem/app/backend/clientes/operaciones.php?accion=listar",
                "createdRow": (row, data) => { if (data.activo == 0) $(row).addClass('fila-inactiva'); },
                "columns": [
                    { "data": "nombre_comercial", "render": (data, type, row) => `<b>${data}</b><br><small class="text-muted">${row.razon_social || ''}</small>` },
                    { "data": "rfc", "render": (data, type, row) => `<span class="badge bg-light text-dark border">${data}</span><br><small>CP: ${row.codigo_postal}</small>` },
                    { "data": "correo", "render": (data, type, row) => `<small><i class="bi bi-envelope me-1"></i>${data || 'N/A'}<br><i class="bi bi-telephone me-1"></i>${row.telefono || 'N/A'}</small>` },
                    { "data": "uso_cfdi", "render": data => `<span class="badge bg-info-subtle text-info border-info">${data}</span>` },
                    { "data": "activo", "className": "text-center", "render": (data, type, row) => `
                        <div class="form-check form-switch d-inline-block">
                            <input class="form-check-input" type="checkbox" ${data == 1 ? 'checked' : ''} onchange="cambiarEstadoCliente(${row.id})">
                        </div>` 
                    },
                    { "data": null, "className": "text-end", "render": (data, type, row) => `
                        <button class="btn btn-sm btn-light border" onclick='editarCliente(${JSON.stringify(row)})'>
                            <i class="bi bi-pencil-square text-primary"></i>
                        </button>` 
                    }
                ],
                "language": { "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json" },
                "dom": 'rtp'
            });

            $('#busquedaCliente').on('keyup', function() { tabla.search(this.value).draw(); });
        });

        function cambiarEstadoCliente(id) {
            $.post('/cfsistem/app/backend/clientes/operaciones.php', { accion: 'eliminar', id: id }, () => tabla.ajax.reload(null, false));
        }

        function nuevoCliente() {
            $('#formCliente')[0].reset();
            $('#clienteId').val(0);
            $('#accionInput').val('guardar');
            $('#modalTitle').text('Nuevo Cliente');
            modalCl.show();
        }

        function editarCliente(c) {
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
            $('#accionInput').val('editar');
            $('#modalTitle').text('Editar Cliente');
            modalCl.show();
        }

        $('#formCliente').on('submit', function(e) {
            e.preventDefault();
            $.post('/cfsistem/app/backend/clientes/operaciones.php', $(this).serialize(), function(res) {
                if(res.status === 'success') {
                    modalCl.hide();
                    tabla.ajax.reload(null, false);
                    Swal.fire({ icon: 'success', title: 'Éxito', timer: 1000, showConfirmButton: false });
                }
            });
        });
    </script>
</body>
</html>