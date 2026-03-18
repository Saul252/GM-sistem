<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
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
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css"
        rel="stylesheet" />
    <?php if (function_exists('cargarEstilos')) { cargarEstilos(); } ?>
<link href="/cfsistem/css/solicitudesCompra.css"
        rel="stylesheet" />
   
</head>

<body>
    <?php renderizarLayout($paginaActual); ?>

    <main class="main-content">
        <div class="glass-card p-4 mb-4">
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted text-uppercase">Rango de Fecha</label>
                    <select id="filtroFecha" class="form-select border-light shadow-sm">
                        <option value="todos">Todas las fechas</option>
                        <option value="hoy">Hoy</option>
                        <option value="ayer">Ayer</option>
                        <option value="semana">Esta Semana</option>
                        <option value="mes">Este Mes</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted text-uppercase">Almacén</label>
                    <select id="filtroAlmacen" class="form-select border-light shadow-sm">
                        <option value="">Todos los almacenes</option>
                        <?php foreach ($almacenes as $alm): ?>
                        <option value="<?= htmlspecialchars($alm['nombre']) ?>"><?= htmlspecialchars($alm['nombre']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted text-uppercase">Estado</label>
                    <select id="filtroEstado" class="form-select border-light shadow-sm">
                        <option value="">Todos los estados</option>
                        <option value="PENDIENTE">Pendiente</option>
                        <option value="PROCESADA">Procesada</option>
                        <option value="RECIBIDA">Recibida</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted text-uppercase">Buscador</label>
                    <div class="input-group shadow-sm">
                        <span class="input-group-text bg-white border-end-0"><i
                                class="bi bi-search text-muted"></i></span>
                        <input type="text" id="buscadorGeneral" class="form-control border-start-0 ps-0"
                            placeholder="Folio o Proveedor">
                    </div>
                </div>
            </div>
        </div>

        <div class="row align-items-center mb-5">
            <div class="col-md-8">
                <h1 class="h3 fw-bold mb-1">Solicitudes de Compra</h1>
                <p class="text-muted small">Gestión de requerimientos de materiales</p>
            </div>
            <div class="col-md-4 text-md-end">
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
                            <th>Almacén</th>
                            <th>Estado</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($solicitudes as $s): ?>
                        <tr>
                            <td><span class="text-dark fw-bold">#<?= str_pad($s['id'], 5, "0", STR_PAD_LEFT) ?></span>
                            </td>
                            <td class="text-muted small"><?= date('d/m/Y H:i', strtotime($s['fecha_creacion'])) ?></td>
                            <td class="fw-medium"><?= htmlspecialchars($s['proveedor_nombre'] ?? 'Sin asignar') ?></td>
                            <td><span
                                    class="badge bg-light text-dark border"><?= htmlspecialchars($s['almacen_nombre']) ?></span>
                            </td>
                            <td>
                                <?php 
                                    $status = strtoupper($s['estado'] ?? 'PENDIENTE');
                                    $clase = match($status) {
                                        'PENDIENTE' => 'bg-warning text-dark',
                                        'PROCESADA' => 'bg-primary text-white',
                                        'RECIBIDA'  => 'bg-success text-white',
                                        default     => 'bg-secondary text-white'
                                    };
                                ?>
                                <span class="badge badge-status <?= $clase ?> rounded-pill"><?= $status ?></span>
                            </td>
                            <td class="text-end">
                                <button class="btn btn-sm btn-white border shadow-sm"
                                    onclick="gestionarSolicitud(<?= $s['id'] ?>)">
                                    <i class="bi bi-eye text-primary"></i> Gestionar
                                </button>
                                <?php if($status === 'PENDIENTE'): ?>
                                <button class="btn btn-sm btn-white border shadow-sm"
                                    onclick="eliminarSolicitud(<?= $s['id'] ?>)">
                                    <i class="bi bi-trash text-danger"></i>
                                </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <div class="modal fade" id="modalGestionSolicitud" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-xl">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                <div class="modal-header bg-success text-white pt-4 px-4 border-0">
                    <h5 class="fw-bold"><i class="bi bi-box-arrow-in-down me-2"></i>Convertir Solicitud <span
                            id="uni-folio"></span> a Compra</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <form id="formConvertirCompra" enctype="multipart/form-data">
                    <input type="hidden" name="solicitud_id" id="uni-solicitud-id">
                    <div class="modal-body px-4 bg-light">

                        <div class="row g-3 mb-4 p-3 rounded-4 bg-white shadow-sm align-items-end">
                            <div class="col-md-3">
                                <label class="small text-muted d-block fw-bold">Proveedor</label>
                                <input type="text" id="uni-proveedor" class="form-control border-0 bg-light fw-bold"
                                    readonly>
                                <input type="hidden" name="proveedor" id="uni-proveedor-nombre">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small fw-bold">Folio de Factura</label>
                                <input type="text" id="folio_compra" name="folio" class="form-control"
                                    placeholder="Cargando..." readonly required>
                            </div>
                            <div class="col-md-3">
                                <label class="small text-muted d-block fw-bold">Evidencia (PDF/IMG)</label>
                                <input type="file" name="evidencia_compra" class="form-control" accept="image/*,.pdf">
                            </div>
                            <div class="col-md-4 text-end">
                                <label class="small text-muted d-block fw-bold text-uppercase">Total Factura</label>
                                <span class="h2 fw-bold text-success" id="uni-gran-total">$ 0.00</span>
                            </div>
                        </div>

                        <div class="table-responsive bg-white rounded-4 shadow-sm p-3">
                            <table class="table align-middle" id="tablaConversion">
                                <thead>
                                    <tr class="text-muted small">
                                        <th>Producto</th>
                                        <th width="120">Cant. Mayoreo</th>
                                        <th width="100">Sueltas</th>
                                        <th width="180">Costo Total Renglón</th>
                                        <th width="150">Almacén</th>
                                        <th width="150" class="text-end">Stock a Ingresar</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>

                    <div class="modal-footer border-0 px-4 pb-4 bg-light">
                        <button type="button" class="btn btn-link text-muted" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success rounded-pill px-5 shadow">
                            <i class="bi bi-save me-2"></i> Guardar Compra e Inventario
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modalGestionSolicitud" tabindex="-1" data-bs-backdrop="static"
        aria-labelledby="labelModal" aria-hidden="false">
        <div class="modal-dialog modal-xl">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                <div class="modal-header bg-success text-white pt-4 px-4 border-0">
                    <h5 class="fw-bold"><i class="bi bi-box-arrow-in-down me-2"></i>Convertir Solicitud <span
                            id="uni-folio"></span> a Compra</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <form id="formConvertirCompra" enctype="multipart/form-data">
                    <input type="hidden" name="solicitud_id" id="uni-solicitud-id">
                    <div class="modal-body px-4 bg-light">

                        <div class="row g-3 mb-4 p-3 rounded-4 bg-white shadow-sm">
                            <div class="col-md-3">
                                <label class="small text-muted d-block fw-bold">Proveedor</label>
                                <input type="text" id="uni-proveedor" class="form-control border-0 bg-light fw-bold"
                                    readonly>
                                <input type="hidden" name="proveedor" id="uni-proveedor-nombre">
                            </div>
                            <div class="col-md-2">
                                <label class="small text-muted d-block fw-bold">Folio Factura</label>
                                <input type="text" name="folio" class="form-control form-control-sm"
                                    placeholder="Ej: FAC-123" required>
                            </div>
                            <div class="col-md-3">
                                <label class="small text-muted d-block fw-bold">Evidencia (PDF/IMG)</label>
                                <input type="file" name="evidencia_compra" class="form-control form-control-sm"
                                    accept="image/*,.pdf">
                            </div>
                            <div class="col-md-4 text-end">
                                <label class="small text-muted d-block fw-bold">TOTAL FACTURA</label>
                                <span class="h3 fw-bold text-success" id="uni-gran-total">$ 0.00</span>
                            </div>
                        </div>

                        <div class="table-responsive bg-white rounded-4 shadow-sm p-2">
                            <table class="table align-middle" id="tablaConversion">
                                <thead>
                                    <tr class="text-muted small">
                                        <th>Producto</th>
                                        <th width="110">Cant. Mayoreo</th>
                                        <th width="100">Sueltas</th>
                                        <th width="150">Costo Renglón</th>
                                        <th width="180">Almacén Destino</th>
                                        <th width="130" class="text-end">Total Piezas</th>
                                    </tr>
                                </thead>
                                <tbody style="border-top: 0;">
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="modal-footer border-0 px-4 pb-4 bg-light">
                        <button type="button" class="btn btn-link text-muted" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success rounded-pill px-5 shadow">
                            <i class="bi bi-save me-2"></i> Guardar Compra e Inventario
                        </button>
                    </div>
                </form>
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
    const URL_CONTROLADOR = '../controllers/solicitudesCompraController.php';

    $(document).ready(function() {
        const table = $('#tablaSolicitudes').DataTable({
            language: {
                url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
            },
            order: [
                [0, 'desc']
            ],
            dom: 'rt<"d-flex justify-content-between align-items-center mt-3"ip>'
        });

        $('#buscadorGeneral').on('keyup', function() {
            table.search(this.value).draw();
        });
        $('#filtroAlmacen').on('change', function() {
            table.column(3).search(this.value).draw();
        });
        $('#filtroEstado').on('change', function() {
            table.column(4).search(this.value).draw();
        });

        $('#filtroFecha').on('change', function() {
            const rango = $(this).val();
            $.fn.dataTable.ext.search = [];
            if (rango !== 'todos') {
                $.fn.dataTable.ext.search.push(function(settings, data) {
                    const [d, m, a] = data[1].split(' ')[0].split('/');
                    const fechaFila = new Date(a, m - 1, d);
                    const hoy = new Date();
                    hoy.setHours(0, 0, 0, 0);
                    if (rango === 'hoy') return fechaFila.getTime() === hoy.getTime();
                    if (rango === 'ayer') {
                        const ayer = new Date(hoy);
                        ayer.setDate(hoy.getDate() - 1);
                        return fechaFila.getTime() === ayer.getTime();
                    }
                    if (rango === 'semana') {
                        const sem = new Date(hoy);
                        sem.setDate(hoy.getDate() - 7);
                        return fechaFila >= sem;
                    }
                    return true;
                });
            }
            table.draw();
        });

        $('.select2-modal').select2({
            theme: 'bootstrap-5',
            dropdownParent: $('#modalSolicitud')
        });

        $('#buscadorProductos').on('select2:select', function(e) {
            const d = e.params.data.element.dataset;
            const id = $(this).val();
            if ($(`#fila-${id}`).length) {
                Swal.fire('Aviso', 'El producto ya está en la lista', 'info');
                return;
            }
            $('#emptyState').addClass('d-none');
            $('#tablaDetalle tbody').append(`
                <tr id="fila-${id}">
                    <td class="ps-4"><b>${d.nombre}</b><br><small class="text-muted">${d.sku}</small></td>
                    <td><input type="number" name="items[${id}][cant]" class="form-control" step="0.01" value="1" required></td>
                    <td><select name="items[${id}][unidad]" class="form-select">
                        <option value="1">Unidad (${d.um})</option>
                        <option value="${d.factor}">Presentación (${d.ur})</option>
                    </select></td>
                    <td><button type="button" class="btn btn-link text-danger" onclick="quitarFila(${id})"><i class="bi bi-trash"></i></button></td>
                </tr>`);
            $(this).val(null).trigger('change');
        });

        $('#formSolicitud').on('submit', async function(e) {
            e.preventDefault();
            if (!$('#tablaDetalle tbody tr').length) {
                Swal.fire('Error', 'Agregue productos', 'warning');
                return;
            }
            Swal.fire({
                title: 'Guardando...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });
            try {
                const resp = await fetch(`${URL_CONTROLADOR}?action=guardar`, {
                    method: 'POST',
                    body: new FormData(this)
                });
                const res = await resp.json();
                if (res.status === 'success') {
                    await Swal.fire({
                        icon: 'success',
                        title: '¡Éxito!',
                        text: res.message,
                        timer: 1500,
                        showConfirmButton: false
                    });
                    location.reload();
                } else {
                    Swal.fire('Error', res.message, 'error');
                }
            } catch (e) {
                Swal.fire('Error', 'Fallo de conexión', 'error');
            }
        });

        // ENVÍO DEL FORMULARIO DE CONVERSIÓN
        $('#formConvertirCompra').on('submit', async function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Procesando ingreso...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });
            try {
                const resp = await fetch(`${URL_CONTROLADOR}?action=convertirACompra`, {
                    method: 'POST',
                    body: new FormData(this)
                });
                const res = await resp.json();
                if (res.status === 'success') {
                    await Swal.fire({
                        icon: 'success',
                        title: 'Ingresado',
                        text: res.message
                    });
                    location.reload();
                } else {
                    Swal.fire('Error', res.message, 'error');
                }
            } catch (e) {
                Swal.fire('Error', 'Fallo de conexión', 'error');
            }
        });
    });

    function quitarFila(id) {
        $(`#fila-${id}`).remove();
        if (!$('#tablaDetalle tbody tr').length) $('#emptyState').removeClass('d-none');
    }

    function nuevaSolicitud() {
        $('#formSolicitud')[0].reset();
        $('#tablaDetalle tbody').empty();
        $('#emptyState').removeClass('d-none');
        $('#modalSolicitud').modal('show');
    }

    async function eliminarSolicitud(id) {
        const r = await Swal.fire({
            title: '¿Eliminar?',
            text: 'No podrás revertir esto',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, borrar'
        });
        if (r.isConfirmed) {
            const fd = new FormData();
            fd.append('id', id);
            const resp = await fetch(`${URL_CONTROLADOR}?action=eliminar`, {
                method: 'POST',
                body: fd
            });
            const res = await resp.json();
            if (res.status === 'success') location.reload();
            else Swal.fire('Error', res.message, 'error');
        }
    }
    </script>
  <script>
async function gestionarSolicitud(id) {
    try {
        // Limpiamos la tabla antes de cargar para evitar residuos visuales
        $('#tablaConversion tbody').empty();

        const resp = await fetch(`${URL_CONTROLADOR}?action=obtenerDetalle&id=${id}`);
        asignarSiguienteFolioCompra();

        if (!resp.ok) throw new Error(`Error de servidor: ${resp.status}`);
        const res = await resp.json();

        if (res.status !== 'success') throw new Error(res.message || 'Error al obtener datos');

        const items = res.data;
        if (!items || items.length === 0) {
            Swal.fire('Info', 'La solicitud no tiene productos.', 'info');
            return;
        }

        $('#uni-solicitud-id').val(id);
        $('#uni-folio').text(`#${id.toString().padStart(5, '0')}`);
        $('#uni-proveedor').val(items[0].proveedor_nombre || 'Sin Proveedor');
        $('#uni-proveedor-nombre').val(items[0].proveedor_nombre || '');

        let html = '';
        // Aunque solo sea uno, iteramos el array que devuelve el servidor
        items.forEach((i, index) => {
            const factor = parseFloat(i.factor_conversion) || 1;
            const uBase = i.unidad_medida || 'pzas';
            const uRep = i.unidad_reporte || 'Mayoreo';
            const cantidadSolicitada = parseFloat(i.cantidad) || 0;

            const cantMayoreo = Math.floor(cantidadSolicitada / factor);
            const cantSueltas = cantidadSolicitada % factor;

            html += `
            <tr class="fila-item" data-index="${index}">
                <td>
                    <input type="hidden" name="items[${index}][producto_id]" value="${i.producto_id}">
                    <input type="hidden" class="h-factor" value="${factor}">
                    <div class="fw-bold text-dark">${i.producto_nombre}</div>
                    <small class="text-muted d-block">1 ${uRep} = ${factor} ${uBase}</small>
                </td>
                <td>
                    <label class="small text-muted text-uppercase fw-bold">${uRep}</label>
                    <input type="number" class="form-control form-control-sm i-mayoreo border-success" 
                           value="${cantMayoreo}" step="1" oninput="recalcularFila(${index})">
                </td>
                <td>
                    <label class="small text-muted text-uppercase fw-bold">${uBase}</label>
                    <input type="number" class="form-control form-control-sm i-sueltas border-primary" 
                           value="${cantSueltas}" step="0.01" oninput="recalcularFila(${index})">
                </td>
                <td>
                    <label class="small text-muted fw-bold">Costo Total Renglón</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light">$</span>
                        <input type="number" step="0.01" class="form-control i-costo-total" placeholder="0.00" required 
                               oninput="recalcularFila(${index})">
                    </div>
                    <input type="hidden" class="h-precio-lote">
                    <div class="mt-1" style="font-size:0.75rem">
                        Cost. Unit: <span class="s-precio-lote fw-bold text-secondary">$ 0.00</span>
                    </div>
                </td>
                <td>
                    <label class="small text-muted fw-bold">Almacén Destino</label>
                    <select class="form-select form-select-sm bg-light i-almacen-id">
                        <option value="${i.almacen_origen_id}" selected>📍 ${i.almacen_nombre}</option>
                    </select>
                </td>
                <td class="text-end bg-light-subtle">
                    <div class="h5 mb-0 fw-bold text-primary s-total-piezas">0</div>
                    <small class="text-muted">${uBase}</small>
                    <input type="hidden" class="h-total-piezas">
                </td>
            </tr>`;
        });

        $('#tablaConversion tbody').html(html);
        $('.fila-item').each(function(idx) { recalcularFila(idx); });
        
        // Removemos aria-hidden antes de mostrar para evitar errores de consola
        $('#modalGestionSolicitud').removeAttr('aria-hidden').modal('show');

    } catch (e) {
        Swal.fire('Error', e.message, 'error');
    }
}

function recalcularFila(index) {
    const fila = $(`.fila-item[data-index="${index}"]`);
    const factor = parseFloat(fila.find('.h-factor').val()) || 1;
    const mayoreo = parseFloat(fila.find('.i-mayoreo').val()) || 0;
    const sueltas = parseFloat(fila.find('.i-sueltas').val()) || 0;
    const costoTotalRenglon = parseFloat(fila.find('.i-costo-total').val()) || 0;

    const totalPiezas = (mayoreo * factor) + sueltas;
    const displayTotal = Number.isInteger(totalPiezas) ? totalPiezas : totalPiezas.toFixed(2);

    fila.find('.s-total-piezas').text(displayTotal);
    fila.find('.h-total-piezas').val(totalPiezas);

    let precioUnitario = totalPiezas > 0 ? costoTotalRenglon / totalPiezas : 0;
    fila.find('.h-precio-lote').val(precioUnitario.toFixed(4));
    fila.find('.s-precio-lote').text('$ ' + precioUnitario.toLocaleString(undefined, {
        minimumFractionDigits: 2, 
        maximumFractionDigits: 4
    }));

    actualizarGranTotal();
}

function actualizarGranTotal() {
    let granTotal = 0;
    $('.i-costo-total').each(function() { granTotal += parseFloat($(this).val()) || 0; });
    $('#uni-gran-total').text('$ ' + granTotal.toLocaleString(undefined, {minimumFractionDigits: 2}));
}

function asignarSiguienteFolioCompra() {
    const inputFolio = document.getElementsByName('folio')[0];
    if (!inputFolio) return;
    fetch(`${URL_CONTROLADOR}?action=getSiguienteFolio`)
        .then(res => res.json())
        .then(data => { if (data.success) inputFolio.value = data.folio; })
        .catch(err => console.error("Error al obtener folio:", err));
}

</script>
<script>
    $(document).ready(function() {
    // Usamos .off() para evitar registros duplicados
    $('#formConvertirCompra').off('submit').on('submit', function(e) {
        e.preventDefault();

        const detalle = [];
        const fila = $('.fila-item').first();
        
        if (fila.length > 0) {
            const index = fila.data('index');
            const almId = fila.find('.i-almacen-id').val();
            const cantTotal = fila.find('.h-total-piezas').val();
            const costoTotal = parseFloat(fila.find('.i-costo-total').val()) || 0;
            
            // Captura el producto_id buscando específicamente en la fila actual
            const productoId = fila.find(`input[name="items[${index}][producto_id]"]`).val();

            detalle.push({
                producto_id: productoId,
                input_mayoreo: fila.find('.i-mayoreo').val() || 0,      
                input_sueltas: fila.find('.i-sueltas').val() || 0,      
                total_item: costoTotal,    
                precio_lote: fila.find('.h-precio-lote').val(),   
                hidden_factor: fila.find('.h-factor').val() || 1,      
                cantidad_faltante: 0, 
                almacenes: {
                    [almId]: { activo: 'on', cantidad: cantTotal }
                }
            });
        }

        // Validación preventiva
        if (detalle.length === 0 || detalle[0].total_item <= 0) {
            Swal.fire('Atención', 'El costo del producto debe ser mayor a 0 para generar el lote.', 'warning');
            return;
        }

        // Preparamos el FormData con todos los campos necesarios para el controlador
        const formData = new FormData(this); 
        formData.append('action', 'guardarCompraCompleta'); 
        formData.append('items', JSON.stringify(detalle));
        formData.append('solicitud_id', $('#uni-solicitud-id').val()); // ¡Importante para cerrar la solicitud!
        formData.append('almacen_id', $('.i-almacen-id').first().val()); 
        formData.append('proveedor', $('#uni-proveedor').val());

        Swal.fire({
            title: '¿Confirmar Ingreso?',
            text: "Se registrará la entrada en inventario y se cerrará la solicitud.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#198754',
            confirmButtonText: 'Sí, guardar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({ 
                    title: 'Procesando...', 
                    html: 'Guardando datos y generando lotes',
                    allowOutsideClick: false, 
                    didOpen: () => { Swal.showLoading(); } 
                });

                $.ajax({
                    url: URL_CONTROLADOR, 
                    type: 'POST',
                    data: formData,
                    processData: false, 
                    contentType: false, 
                    dataType: 'json', // Forzamos a que espere un JSON
                    success: function(res) {
                        if (res.success) {
                            Swal.fire('¡Éxito!', res.message, 'success').then(() => {
                                location.reload();
                            });
                        } else {
                            // Si el controlador responde con success: false
                            Swal.fire('Error de negocio', res.message || 'Error desconocido', 'error');
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        // SI DA ERROR, ESTO TE DIRÁ POR QUÉ:
                        console.error("Respuesta del servidor:", jqXHR.responseText);
                        
                        Swal.fire({
                            icon: 'error',
                            title: 'Error del Servidor',
                            html: `<div style="text-align:left; font-size:11px; background:#eee; padding:10px; max-height:200px; overflow:auto;">
                                    ${jqXHR.responseText || 'Error desconocido (posible 500)'}
                                   </div>`,
                            footer: 'Revisa la pestaña Network en F12 para más detalles.'
                        });
                    }
                });
            }
        });
    });
});
</script>
</body>

</html>