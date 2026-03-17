<?php
/**
 * solicitudesCompra_view.php - Versión Elegante & Profesional
 */
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitudes de Compra | cfsistem</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
     <?php if (function_exists('cargarEstilos')) { cargarEstilos(); } ?>
    
    <style>
        :root { 
            --primary-color: #1a5c37; /* Verde Bosque */
            --accent-color: #28a745; 
            --bg-light: #f8f9fa;
            --sidebar-width: 260px;
        }
        
        body { background-color: var(--bg-light); font-family: 'Inter', sans-serif; color: #334155; }
        
        .main-content { 
            margin-left: var(--sidebar-width); 
            padding: 2.5rem; 
            min-height: 100vh;
            transition: all 0.3s;
        }

        /* Tarjetas con estilo moderno */
        .glass-card {
            background: #ffffff;
            border: 1px solid rgba(0,0,0,0.05);
            border-radius: 1.25rem;
            box-shadow: 0 4px 20px rgba(0,0,0,0.03);
        }

        /* Badges estilizados */
        .badge-status {
            padding: 0.6em 1.2em;
            font-weight: 600;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            font-size: 0.75rem;
        }

        /* Tabla estilizada */
        .table thead th {
            background-color: transparent;
            color: #64748b;
            text-transform: uppercase;
            font-size: 0.8rem;
            font-weight: 700;
            border-bottom: 2px solid #f1f5f9;
        }

        /* Botón Nueva Solicitud */
        .btn-add {
            background-color: var(--primary-color);
            color: white;
            border-radius: 50px;
            padding: 10px 24px;
            font-weight: 600;
            box-shadow: 0 4px 15px rgba(26, 92, 55, 0.2);
            transition: all 0.3s;
        }
        .btn-add:hover { background-color: #14462a; color: white; transform: translateY(-2px); }

        /* Ajustes Modal */
        .modal-content { border-radius: 1.5rem; border: none; overflow: hidden; }
        .modal-header { border-bottom: none; padding: 2rem 2rem 1rem; }
        .modal-body { padding: 1rem 2rem 2rem; }
        
        .product-row { transition: all 0.2s; }
        .product-row:hover { background-color: #f1f5f9; }

        @media (max-width: 768px) { .main-content { margin-left: 0; padding: 1.5rem; } }
    </style>
</head>
<body>
    <?php renderizarLayout($paginaActual); ?>

    <main class="main-content">
        <div class="row align-items-center mb-5">
            <div class="col-md-8">
                <h1 class="h3 fw-bold mb-1" style="color: #0f172a;">Solicitudes de Compra</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="#" class="text-decoration-none text-muted">Inventario</a></li>
                        <li class="breadcrumb-item active fw-medium" aria-current="page">Solicitudes</li>
                    </ol>
                </nav>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <button class="btn btn-add" onclick="nuevaSolicitud()">
                    <i class="bi bi-plus-lg me-2"></i> Crear Solicitud
                </button>
            </div>
        </div>

        <div class="glass-card p-4">
            <div class="table-responsive">
                <table id="tablaSolicitudes" class="table align-middle w-100">
                    <thead>
                        <tr>
                            <th>Folio</th>
                            <th>Fecha</th>
                            <th>Proveedor</th>
                            <th>Almacén Destino</th>
                            <th>Estado</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($solicitudes as $s): ?>
                        <tr>
                            <td><span class="text-dark fw-bold">#<?= str_pad($s['id'], 5, "0", STR_PAD_LEFT) ?></span></td>
                            <td class="text-muted small"><?= date('d/m/Y H:i', strtotime($s['fecha_creacion'])) ?></td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="bg-light rounded-circle p-2 me-2">
                                        <i class="bi bi-shop text-success"></i>
                                    </div>
                                    <span class="fw-medium text-dark"><?= htmlspecialchars($s['proveedor_nombre'] ?? 'Sin asignar') ?></span>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border rounded-pill fw-normal">
                                    <i class="bi bi-geo-alt-fill text-danger me-1"></i><?= htmlspecialchars($s['almacen_nombre']) ?>
                                </span>
                            </td>
                            <td>
                                <?php 
                                    $status = strtolower($s['estado'] ?? 'pendiente');
                                    $clase = match($status) {
                                        'pendiente' => 'bg-warning text-dark',
                                        'procesada' => 'bg-primary text-white',
                                        'recibida'  => 'bg-success text-white',
                                        default     => 'bg-secondary text-white'
                                    };
                                ?>
                                <span class="badge badge-status <?= $clase ?> rounded-pill">
                                    <?= $status ?>
                                </span>
                            </td>
                            <td class="text-end">
                                <div class="btn-group shadow-sm rounded-3 overflow-hidden">
                                    <button class="btn btn-white btn-sm px-3 border" onclick="verDetalles(<?= $s['id'] ?>)" title="Ver Detalle">
                                        <i class="bi bi-eye text-primary"></i>
                                    </button>
                                    <?php if($status === 'pendiente'): ?>
                                    <button class="btn btn-white btn-sm px-3 border" onclick="eliminarSolicitud(<?= $s['id'] ?>)" title="Eliminar">
                                        <i class="bi bi-trash3 text-danger"></i>
                                    </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <div class="modal fade" id="modalSolicitud" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <form id="formSolicitud">
                    <div class="modal-header">
                        <div>
                            <h4 class="fw-bold mb-0">Crear Nueva Solicitud</h4>
                            <p class="text-muted small mb-0">Completa los datos para generar el pedido</p>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    
                    <div class="modal-body">
                        <div class="row g-4 mb-4">
                            <div class="col-md-4">
                                <label class="form-label fw-bold text-dark small text-uppercase">1. Almacén Solicitante</label>
                                <?php if ($es_admin): ?>
                                    <select name="almacen_id" class="form-select select2-modal" required>
                                        <option value="">-- Seleccionar --</option>
                                        <?php foreach ($almacenes as $alm): ?>
                                            <option value="<?= $alm['id'] ?>"><?= htmlspecialchars($alm['nombre']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                <?php else: ?>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-0"><i class="bi bi-lock-fill text-muted"></i></span>
                                        <input type="text" class="form-control bg-light border-0 fw-bold" 
                                               value="<?= htmlspecialchars($almacenes[0]['nombre'] ?? 'Mi Almacén') ?>" readonly>
                                    </div>
                                    <input type="hidden" name="almacen_id" value="<?= $almacen_usuario ?>">
                                <?php endif; ?>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold text-dark small text-uppercase">2. Proveedor Sugerido</label>
                                <select name="proveedor_id" id="proveedor_id" class="form-select select2-modal" required>
                                    <option value="">Buscar proveedor...</option>
                                    <?php foreach($proveedores as $p): ?>
                                        <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['nombre_comercial']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold text-dark small text-uppercase">3. Buscar Productos</label>
                                <select id="buscadorProductos" class="form-select select2-modal">
                                    <option value="">Nombre o SKU del producto...</option>
                                    <?php foreach($productos as $pr): ?>
                                        <option value="<?= $pr['producto_id'] ?>" 
                                                data-nombre="<?= htmlspecialchars($pr['nombre']) ?>"
                                                data-sku="<?= htmlspecialchars($pr['sku']) ?>"
                                                data-um="<?= htmlspecialchars($pr['unidad_medida'] ?? 'Pz') ?>"
                                                data-ur="<?= htmlspecialchars($pr['unidad_reporte'] ?? 'Bulto') ?>"
                                                data-factor="<?= $pr['factor_conversion'] ?? 1 ?>">
                                            [<?= htmlspecialchars($pr['sku'] ?? 'N/A') ?>] <?= htmlspecialchars($pr['nombre']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="table-responsive border rounded-4 overflow-hidden">
                            <table class="table mb-0" id="tablaDetalle">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="ps-4">Producto</th>
                                        <th width="150">Cantidad</th>
                                        <th width="220">Presentación</th>
                                        <th width="80" class="text-center"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    </tbody>
                            </table>
                            <div id="emptyState" class="text-center py-5 text-muted">
                                <i class="bi bi-cart3 fs-1 d-block mb-2 opacity-25"></i>
                                <p>No hay productos agregados a la solicitud</p>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer border-0 p-4 pt-0">
                        <button type="button" class="btn btn-link text-muted text-decoration-none px-4" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-add px-5">
                            <i class="bi bi-check2-circle me-2"></i> Confirmar Solicitud
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<div class="modal fade" id="modalVerDetalle" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <div>
                    <h4 class="fw-bold mb-0">Detalle de Solicitud <span id="det-folio" class="text-primary"></span></h4>
                    <p class="text-muted small mb-0" id="det-fecha"></p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-4">
                    <div class="col-sm-6">
                        <small class="text-uppercase fw-bold text-muted d-block">Proveedor</small>
                        <span id="det-proveedor" class="fw-medium"></span>
                    </div>
                    <div class="col-sm-6 text-sm-end">
                        <small class="text-uppercase fw-bold text-muted d-block">Almacén Destino</small>
                        <span id="det-almacen" class="fw-medium"></span>
                    </div>
                </div>
                
                <div class="table-responsive border rounded-4">
                    <table class="table mb-0" id="tablaVerDetalle">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Producto / SKU</th>
                                <th class="text-center">Cantidad Solicitada</th>
                                <th class="text-end pe-4">Unidad</th>
                            </tr>
                        </thead>
                        <tbody>
                            </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
    // Configuración Global: Ruta hacia el controlador
    const URL_CONTROLADOR = '../controllers/solicitudesCompraController.php';

    $(document).ready(function() {
        // Inicializar DataTable
        $('#tablaSolicitudes').DataTable({
            language: { url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json" },
            order: [[0, 'desc']],
            dom: '<"d-flex justify-content-between mb-3"f>rtip'
        });

        // Inicializar Select2 en Modales
        $('.select2-modal').select2({
            theme: 'bootstrap-5',
            dropdownParent: $('#modalSolicitud')
        });

        // Evento: Seleccionar producto del buscador
        $('#buscadorProductos').on('select2:select', function(e) {
            const d = e.params.data.element.dataset;
            const id = $(this).val();
            
            if ($(`#fila-${id}`).length) {
                Swal.fire('¡Atención!', 'Este producto ya está en tu lista.', 'info');
                $(this).val(null).trigger('change');
                return;
            }

            $('#emptyState').addClass('d-none');

            const fila = `
                <tr id="fila-${id}" class="product-row">
                    <td class="ps-4">
                        <div class="d-flex flex-column">
                            <span class="fw-bold text-dark">${d.nombre}</span>
                            <span class="text-muted small">${d.sku}</span>
                        </div>
                    </td>
                    <td>
                        <input type="number" name="items[${id}][cant]" class="form-control border-light shadow-sm rounded-3" step="0.01" min="0.01" value="1" required>
                    </td>
                    <td>
                        <select name="items[${id}][unidad]" class="form-select border-light shadow-sm rounded-3">
                            <option value="1">Unidad (${d.um})</option>
                            <option value="${d.factor}">Bulto/Caja (${d.ur})</option>
                        </select>
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-outline-danger btn-sm rounded-circle border-0" onclick="quitarFila(${id})">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </td>
                </tr>
            `;
            $('#tablaDetalle tbody').append(fila);
            $(this).val(null).trigger('change');
        });
    });

    /**
     * Lógica de Interfaz
     */
    function quitarFila(id) {
        $(`#fila-${id}`).remove();
        if ($('#tablaDetalle tbody tr').length === 0) {
            $('#emptyState').removeClass('d-none');
        }
    }

    function nuevaSolicitud() {
        $('#formSolicitud')[0].reset();
        $('#tablaDetalle tbody').empty();
        $('#emptyState').removeClass('d-none');
        $('.select2-modal').val(null).trigger('change');
        $('#modalSolicitud').modal('show');
    }

    /**
     * AJAX: Guardar Solicitud
     */
    $('#formSolicitud').on('submit', async function(e) {
        e.preventDefault();
        
        if ($('#tablaDetalle tbody tr').length === 0) {
            Swal.fire('Formulario incompleto', 'Debes agregar al menos un producto a la solicitud.', 'warning');
            return;
        }

        Swal.fire({ title: 'Procesando...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });

        try {
            const formData = new FormData(this);
            const resp = await fetch(`${URL_CONTROLADOR}?action=guardar`, { 
                method: 'POST', 
                body: formData 
            });
            const res = await resp.json();
            
            if (res.status === 'success') {
                Swal.fire({ icon: 'success', title: '¡Creada!', text: res.message, timer: 2000 })
                .then(() => location.reload());
            } else {
                Swal.fire('Error', res.message, 'error');
            }
        } catch (e) {
            Swal.fire('Error de red', 'No se pudo comunicar con el servidor.', 'error');
        }
    });

    /**
     * AJAX: Eliminar Solicitud
     */
    async function eliminarSolicitud(id) {
        const r = await Swal.fire({
            title: '¿Confirmas la eliminación?',
            text: "Se borrará la solicitud permanentemente y no se podrá recuperar.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="bi bi-trash me-2"></i>Sí, eliminar',
            cancelButtonText: 'Cancelar',
            customClass: {
                confirmButton: 'rounded-pill px-4',
                cancelButton: 'rounded-pill px-4'
            }
        });

        if (r.isConfirmed) {
            Swal.fire({ title: 'Eliminando...', didOpen: () => Swal.showLoading() });
            const fd = new FormData();
            fd.append('id', id);
            try {
                const resp = await fetch(`${URL_CONTROLADOR}?action=eliminar`, { 
                    method: 'POST', 
                    body: fd 
                });
                const res = await resp.json();
                if (res.status === 'success') {
                    Swal.fire({ icon: 'success', title: 'Eliminado', showConfirmButton: false, timer: 1500 })
                    .then(() => location.reload());
                } else {
                    Swal.fire('Error', res.message, 'error');
                }
            } catch (e) {
                Swal.fire('Error', 'Comunicación fallida con el servidor.', 'error');
            }
        }
    }

    /**
     * AJAX: Ver Detalles (Modal de Consulta)
     */
    async function verDetalles(id) {
    console.log("Consultando ID:", id); // Debug
    try {
        const resp = await fetch(`${URL_CONTROLADOR}?action=getDetalleJSON&id=${id}`);
        
        // Si la respuesta no es 200 OK, hay un error de ruta o servidor
        if (!resp.ok) {
            console.error("Error HTTP:", resp.status);
            throw new Error(`Error del servidor (Código ${resp.status})`);
        }

        const textoRaw = await resp.text(); // Leemos como texto primero
        console.log("Respuesta bruta del servidor:", textoRaw); // <--- ESTO ES CLAVE

        const data = JSON.parse(textoRaw); // Intentamos convertir a JSON
        
        if (data.status === 'error') throw new Error(data.message);

        // ... resto del código para llenar la tabla ...
        $('#tablaVerDetalle tbody').empty();
        let html = '';
        data.forEach(item => {
            html += `<tr class="align-middle">
                        <td class="ps-4"><b>${item.producto_nombre}</b><br><small>${item.sku}</small></td>
                        <td class="text-center">${item.cantidad}</td>
                        <td class="text-end pe-4">${item.unidad_medida}</td>
                     </tr>`;
        });
        $('#tablaVerDetalle tbody').html(html);
        $('#modalVerDetalle').modal('show');
        Swal.close();

    } catch (e) {
        console.error("Error detallado:", e);
        Swal.fire('Error', 'Mensaje: ' + e.message, 'error');
    }
}</script>
</body>
</html>