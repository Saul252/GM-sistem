<?php
/**
 * clientes_view.php
 * Vista de administración de clientes con filtro de almacén optimizado
 */
$usosCFDI = ['G01' => 'Adquisición', 'G03' => 'Gastos', 'P01' => 'Por definir', 'S01' => 'Sin efectos'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Clientes | Sistema</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <?php if (function_exists('cargarEstilos')) { cargarEstilos(); } ?>
    <style>
        :root { --sidebar-width: 260px; --navbar-height: 65px; }
        body { background-color: #f4f7f6; }
        .main-content { margin-left: var(--sidebar-width); padding: 40px; padding-top: calc(var(--navbar-height) + 20px); }
        .card-table { border: none; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); background: white; }
        .badge-ubicacion { 
            background-color: #f8f9fa; 
            color: #333; 
            border: 1px solid #ddd; 
            padding: 0.5rem 0.75rem;
            font-size: 0.875rem;
        }
        .filtros-activos {
            background-color: #e3f2fd !important;
            border-color: #2196f3 !important;
            color: #1976d2 !important;
            font-weight: bold;
        }
        @media (max-width: 768px) { 
            .main-content { margin-left: 0; padding: 20px; padding-top: 90px; } 
        }
    </style>
</head>
<body>
    <?php renderizarLayout($paginaActual); ?>

    <main class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold m-0">Cartera de Clientes</h2>
                <p class="text-muted mb-0">Gestiona tus clientes y sus ubicaciones</p>
            </div>
            <button class="btn btn-primary rounded-pill px-4" onclick="nuevoCliente()">
                <i class="bi bi-plus-circle me-2"></i>Nuevo Cliente
            </button>
        </div>

        <div class="card card-table p-4">
            <div class="row mb-4 g-3">
                <div class="col-md-5">
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                        <input type="text" id="busquedaCliente" class="form-control border-start-0" placeholder="Buscar por nombre, RFC...">
                    </div>
                </div>

                <?php if ($almacen_usuario == 0): ?>
                <div class="col-md-4">
                    <select id="filtroAlmacenVista" class="form-select">
                        <option value="">📍 Todos los Almacenes</option>
                        <?php foreach ($almacenes as $alm): ?>
                            <option value="<?= $alm['id'] ?>">
                                <?= htmlspecialchars($alm['nombre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <button class="btn btn-outline-secondary w-100" onclick="limpiarFiltros()">
                        <i class="bi bi-arrow-clockwise"></i> Limpiar Filtros
                    </button>
                </div>
                <?php endif; ?>
            </div>

            <div class="table-responsive">
                <table id="tablaClientes" class="table table-hover align-middle w-100">
                    <thead class="table-dark">
                        <tr>
                            <th>Nombre Comercial</th>
                            <th>RFC</th>
                            <th>Ubicación</th>
                            <th>Estado</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($clientes as $c): ?>
                        <tr class="fila-cliente" data-almacen-id="<?= $c['almacen_id'] ?>">
                            <td>
                                <strong><?= htmlspecialchars($c['nombre_comercial']) ?></strong>
                                <?php if (!empty($c['razon_social'])): ?>
                                <br><small class="text-muted"><?= htmlspecialchars($c['razon_social']) ?></small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border px-2 py-1">
                                    <?= htmlspecialchars($c['rfc']) ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge badge-ubicacion">
                                    <i class="bi bi-house-door me-1"></i>
                                    <?= htmlspecialchars($c['nombre_almacen'] ?? 'Principal') ?>
                                </span>
                            </td>
                            <td>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" 
                                           type="checkbox" 
                                           <?= $c['activo'] ? 'checked' : '' ?> 
                                           onchange="cambiarEstado(<?= $c['id'] ?>, this.checked ? 1 : 0)"
                                           id="switch_<?= $c['id'] ?>">
                                    <label class="form-check-label" for="switch_<?= $c['id'] ?>">
                                        <?= $c['activo'] ? 'Activo' : 'Inactivo' ?>
                                    </label>
                                </div>
                            </td>
                            <td class="text-end">
                                <div class="btn-group">
                                    <button class="btn btn-sm btn-outline-primary border-0" 
                                            onclick="editarCliente(<?= $c['id'] ?>)" title="Editar">
                                        <i class="bi bi-pencil-square fs-5"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-success border-0" 
                                            onclick="verDetalles(<?= $c['id'] ?>)" title="Ver detalles">
                                        <i class="bi bi-eye fs-5"></i>
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

    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
    let tabla;

    // Función para escapar caracteres especiales en búsqueda regex
    function escapeRegExp(string) {
        return string.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
    }

    $(document).ready(function() {
        // Inicializar DataTable
        tabla = $('#tablaClientes').DataTable({
            "language": { "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json" },
            "dom": 'rt<"row mt-3"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
            "pageLength": 15,
            "responsive": true,
            "columnDefs": [
                { "targets": [4], "orderable": false, "searchable": false },
                { "targets": [2], "visible": <?= ($almacen_usuario == 0) ? 'true' : 'false' ?> }
            ],
            "order": [[0, 'asc']]
        });

        // Buscador de texto libre
        $('#busquedaCliente').on('keyup', function() {
            tabla.search(this.value).draw();
        });

        // FILTRO DE ALMACÉN (Lógica mejorada basada en IDs)
        $('#filtroAlmacenVista').on('change', function() {
            const almacenId = $(this).val();
            
            // Limpiamos filtros personalizados anteriores
            $.fn.dataTable.ext.search.pop();

            if (almacenId !== "") {
                $(this).addClass('filtros-activos');
                
                // Agregamos nueva regla de filtrado
                $.fn.dataTable.ext.search.push(
                    function(settings, data, dataIndex) {
                        const row = tabla.row(dataIndex).node();
                        const idEnFila = $(row).attr('data-almacen-id');
                        return idEnFila == almacenId;
                    }
                );
            } else {
                $(this).removeClass('filtros-activos');
            }
            
            tabla.draw();
        });
    });

    function limpiarFiltros() {
        $('#busquedaCliente').val('');
        $('#filtroAlmacenVista').val('').removeClass('filtros-activos');
        $.fn.dataTable.ext.search.pop();
        tabla.search('').draw();
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
                Swal.fire({ 
                    icon: 'success', 
                    title: '¡Actualizado!', 
                    timer: 1000, 
                    showConfirmButton: false 
                });
                $(`#switch_${id}`).next('label').text(estado ? 'Activo' : 'Inactivo');
            }
        } catch (e) {
            console.error('Error:', e);
        }
    }

    function nuevoCliente() {
        window.location.href = 'clientesController.php?action=nuevo';
    }

    function editarCliente(id) {
        window.location.href = `clientesController.php?action=editar&id=${id}`;
    }

    function verDetalles(id) {
        Swal.fire({
            title: 'Detalles del Cliente',
            text: 'Cargando información...',
            icon: 'info',
            timer: 800,
            showConfirmButton: false
        });
    }
    </script>
</body>
</html>