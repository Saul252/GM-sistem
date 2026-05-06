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
    <?php require_once __DIR__ . '/layout/icono.php' ?>
    <?php if (function_exists('cargarEstilos')) { cargarEstilos(); } ?>
    <link href="/cfsistem/css/solicitudesCompra.css" rel="stylesheet" />

</head>

<body>
    <?php renderizarLayout($paginaActual); ?>

    <main class="main-content">

        <div class="glass-card p-4 mb-4">

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

                                <?php if($status === 'PENDIENTE'): ?>
                                <button class="btn btn-sm btn-white border shadow-sm"
                                    onclick="gestionarSolicitud(<?= $s['id'] ?>)">
                                    <i class="bi bi-eye text-primary"></i> Gestionar
                                </button>
                                <button class="btn btn-sm btn-white border shadow-sm"
                                    onclick="eliminarSolicitud(<?= $s['id'] ?>)">
                                    <i class="bi bi-trash text-danger"></i>
                                </button>
                                <?php endif; ?>
                                <button class="btn btn-sm btn-white border shadow-sm rounded-pill px-3"
                                    onclick="prepararImpresion(<?= $s['id'] ?>)" title="Imprimir solicitud">
                                    <i class="bi bi-printer text-primary me-1"></i>
                                    <span class="text-dark fw-medium">Imprimir</span>
                                </button>
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
            <div class="modal-content"
                style="border-radius: 20px; border: none; overflow: hidden; box-shadow: 0 15px 50px rgba(0,0,0,0.2);">
                <form id="formSolicitud">
                    <div class="modal-header bg-white border-0 pt-4 px-4">
                        <div class="d-flex align-items-center">
                            <div class="bg-primary text-white rounded-3 p-2 me-3 shadow-sm">
                                <i class="bi bi-file-earmark-plus fs-4"></i>
                            </div>
                            <div>
                                <h4 class="fw-bold mb-0">Nueva Solicitud de Compra</h4>
                                <p class="text-muted small mb-0">Complete los datos para requerir materiales al almacén
                                </p>
                            </div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body px-4">
                        <div class="row g-3 mb-4 p-3 rounded-4 bg-light shadow-sm align-items-end">

                            <div class="col-md-3">
                                <label class="form-label small fw-bold text-muted text-uppercase">Almacén de
                                    Destino</label>
                                <?php if (isset($es_admin) && $es_admin): ?>
                                <select name="almacen_id" class="form-select select2-modal" required>
                                    <option value="">-- Seleccionar --</option>
                                    <?php foreach ($almacenes as $alm): ?>
                                    <option value="<?= $alm['id'] ?>"><?= htmlspecialchars($alm['nombre']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <?php else: ?>
                                <input type="text" class="form-control border-0 bg-white fw-bold"
                                    value="<?= htmlspecialchars($almacenes[0]['nombre'] ?? 'Almacén Asignado') ?>"
                                    readonly>
                                <input type="hidden" name="almacen_id"
                                    value="<?= $almacen_usuario ?? ($almacenes[0]['id'] ?? '') ?>">
                                <?php endif; ?>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label small fw-bold text-muted text-uppercase">Proveedor
                                    Sugerido</label>
                                <select name="proveedor_id" class="form-select select2-modal" required>
                                    <option value="">Seleccionar proveedor...</option>
                                    <?php foreach($proveedores as $p): ?>
                                    <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['nombre_comercial']) ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted text-uppercase">Añadir Producto (SKU o
                                    Nombre)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0"><i
                                            class="bi bi-search"></i></span>
                                    <select id="buscadorProductos" class="form-select select2-modal border-start-0">
                                        <option value="">Escribe para buscar...</option>
                                        <?php foreach($productos as $pr): ?>
                                        <option value="<?= $pr['producto_id'] ?>"
                                            data-nombre="<?= htmlspecialchars($pr['nombre']) ?>"
                                            data-sku="<?= htmlspecialchars($pr['sku']) ?>"
                                            data-um="<?= htmlspecialchars($pr['unidad_medida']) ?>"
                                            data-ur="<?= htmlspecialchars($pr['unidad_reporte']) ?>"
                                            data-factor="<?= $pr['factor_conversion'] ?? 1 ?>">
                                            [<?= $pr['sku'] ?>] <?= $pr['nombre'] ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive border rounded-4 bg-white">
                            <table class="table align-middle mb-0" id="tablaDetalle">
                                <thead class="bg-light">
                                    <tr class="text-muted small uppercase">
                                        <th class="ps-4" style="width: 45%;">Producto</th>
                                        <th style="width: 20%;">Cantidad</th>
                                        <th style="width: 25%;">Presentación / Unidad</th>
                                         <th style="width: 25%;">Precio</th>
                                        <th style="width: 10%;" class="text-end pe-4">Acción</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>

                            <div id="emptyState" class="text-center py-5 text-muted">
                                <div class="mb-3">
                                    <i class="bi bi-cart-plus opacity-25" style="font-size: 3.5rem;"></i>
                                </div>
                                <p class="fw-medium">La lista está vacía</p>
                                <small>Utiliza el buscador de arriba para añadir artículos</small>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer border-0 p-4">
                        <button type="button" class="btn btn-link text-decoration-none text-muted fw-bold"
                            data-bs-dismiss="modal">
                            Cancelar
                        </button>
                        <button type="submit" class="btn btn-primary rounded-pill px-5 py-2 fw-bold shadow">
                            <i class="bi bi-check2-circle me-2"></i> Confirmar Solicitud
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modalGestionSolicitud" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 24px; overflow:hidden;">

            <!-- HEADER -->
            <div class="modal-header bg-success text-white px-4 py-3 border-0">
                <div>
                    <h5 class="fw-bold mb-0">
                        <i class="bi bi-box-arrow-in-down me-2"></i>
                        Convertir Solicitud <span id="uni-folio"></span>
                    </h5>
                    <small class="opacity-75">Generación de compra e inventario</small>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <form id="formConvertirCompra" enctype="multipart/form-data">
                <input type="hidden" name="solicitud_id" id="uni-solicitud-id">

                <div class="modal-body bg-light px-4 py-4">

                    <!-- CARD PROVEEDOR / FINANZAS -->
                    <div class="bg-white rounded-4 shadow-sm p-4 mb-4">

                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="fw-semibold text-muted small">Información del proveedor</span>
                            <span class="badge bg-success-subtle text-success px-3 py-2 rounded-pill">
                                Compra en proceso
                            </span>
                        </div>

                        <div class="row g-3 align-items-end">

                            <!-- PROVEEDOR -->
                            <div class="col-md-3">
                                <label class="form-label small text-muted">Proveedor</label>
                                <input type="text" id="uni-proveedor"
                                    class="form-control bg-light border-0 fw-bold rounded-3" readonly>

                                <input type="hidden" name="proveedor" id="uni-proveedor-nombre">

                                <!-- DEUDA -->
                                <div class="mt-2 p-2 rounded-3 bg-danger-subtle border border-danger-subtle">
                                    <small class="text-danger fw-semibold d-block">Deuda actual</small>
                                    <input type="text"
                                        name="deudaProveedor"
                                        id="uni-proveedor-deuda"
                                        class="form-control border-0 bg-transparent text-danger fw-bold p-0"
                                        readonly>
                                </div>
                            </div>

                            <!-- FOLIO -->
                            <div class="col-md-2">
                                <label class="form-label small text-muted">Folio factura</label>
                                <input type="text" name="folio"
                                    class="form-control rounded-3 shadow-sm"
                                    placeholder="FAC-000" required>
                            </div>

                            <!-- EVIDENCIA -->
                            <div class="col-md-3">
                                <label class="form-label small text-muted">Evidencia</label>
                                <input type="file"
                                    name="evidencia_compra"
                                    class="form-control rounded-3 shadow-sm"
                                    accept="image/*,.pdf">
                            </div>

                            <!-- PAGAR DEUDA -->
                            <div class="col-md-2">
                                <label class="form-label small text-primary fw-semibold">
    <i class="bi bi-cash-coin"></i> Abono 
    <span class="label-abono-info text-muted"></span>
</label>
                                <input type="number"
                                    id="input_pagar_deuda"
                                    name="saldo_a_pagar"
                                    class="form-control border-primary shadow-sm rounded-3"
                                    value="0" min="0" step="0.1">
                            </div>

                            <!-- TOTAL -->
                            <div class="col-md-2 text-end">
                                <label class="form-label small text-muted">Total compra</label>
                                <div class="p-2 rounded-3 bg-success-subtle">
                                    <span class="h4 fw-bold text-success mb-0" id="uni-gran-total">
                                        $ 0.00
                                    </span>
                                </div>
                            </div>

                        </div>

                        <!-- MÉTODO PAGO -->
                        <div class="mt-3">
                            <label class="form-label small text-muted">Método de pago</label>
                            <select name="metodo_pago" id="metodo_pago"
                                class="form-select rounded-3 shadow-sm">
                                <option value="">Seleccione...</option>
                                <option value="Efectivo">Efectivo</option>
                                <option value="Transferencia">Transferencia</option>
                                <option value="Tarjeta">Tarjeta</option>
                            </select>
                        </div>

                    </div>

                    <!-- TABLA -->
                    <div class="bg-white rounded-4 shadow-sm p-2">
                        <table class="table align-middle mb-0" id="tablaConversion">
                            <thead class="table-light">
                                <tr class="small text-muted">
                                    <th>Producto</th>
                                    <th>Mayoreo</th>
                                    <th>Sueltas</th>
                                    <th>Faltantes</th>
                                    <th>Excedentes</th>
                                    <th>Costo</th>
                                    <th>Almacén</th>
                                    <th class="text-end">Total</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>

                </div>

                <!-- FOOTER -->
                <div class="modal-footer bg-light border-0 px-4 pb-4">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4"
                        data-bs-dismiss="modal">
                        Cancelar
                    </button>

                    <button type="submit"
                        class="btn btn-success rounded-pill px-5 shadow-sm fw-semibold">
                        <i class="bi bi-check2-circle me-2"></i> Confirmar compra
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>
    <div class="modal fade" id="modalImprimirSolicitud" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 15px;">
                <div class="modal-header bg-dark text-white border-0">
                    <h5 class="fw-bold mb-0"><i class="bi bi-printer me-2"></i>Vista de Impresión</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-0">
                    <div id="areaImpresion" class="p-5 bg-white">
                        <div class="d-flex justify-content-between align-items-start mb-4">
                            <div>
                                <h2 class="fw-bold text-uppercase mb-0">Solicitud de Compra</h2>
                                <p class="text-muted mb-0" id="print-folio">FOLIO: #00000</p>
                            </div>
                            <div class="text-end">
                                <h5 class="fw-bold mb-0">cfsistem</h5>
                                <p class="small text-muted mb-0" id="print-fecha">Fecha: --/--/----</p>
                            </div>
                        </div>

                        <hr>

                        <div class="row mb-4">
                            <div class="col-6">
                                <label class="small text-muted d-block">ALMACÉN DESTINO:</label>
                                <span class="fw-bold" id="print-almacen">---</span>
                            </div>
                            <div class="col-6">
                                <label class="small text-muted d-block">PROVEEDOR :</label>
                                <span class="fw-bold" id="print-proveedor">---</span>
                            </div>
                        </div>

                        <table class="table table-bordered align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Descripción del Material</th>
                                    <th width="120" class="text-center">Cantidad</th>
                                    <th width="150">Unidad Mayor</th>
                                       <th width="150">Unidad Menor</th>
                                          <th width="150">Costo</th>
                                </tr>
                            </thead>
                            <tbody id="print-tabla-cuerpo">
                            </tbody>
                        </table>

                        <div class="mt-5 pt-4">
                            <div class="row text-center">
                                <div class="col-4">
                                    <div style="border-top: 1px solid #000; margin: 0 20px;"></div>
                                    <small>Solicita</small>
                                </div>
                                <div class="col-4">
                                    <div style="border-top: 1px solid #000; margin: 0 20px;"></div>
                                    <small>Autoriza</small>
                                </div>
                                <div class="col-4">
                                    <div style="border-top: 1px solid #000; margin: 0 20px;"></div>
                                    <small>Recibe (Compras)</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 bg-light">
                    <button type="button" class="btn btn-secondary rounded-pill px-4"
                        data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary rounded-pill px-4" onclick="ejecutarImpresion()">
                        <i class="bi bi-printer-fill me-2"></i> Imprimir Ahora
                    </button>
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
                     <td><input type="number" name="items[${id}][precio]" class="form-control" step="0.01" placeholder="costo" required></td>
                   
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
        
function asignarSiguienteFolioCompra() {
    const inputFolio = document.getElementsByName('folio')[0];
    if (!inputFolio) return;
    fetch(`${URL_CONTROLADOR}?action=getSiguienteFolio`)
        .then(res => res.json())
        .then(data => {
            if (data.success) inputFolio.value = data.folio;
        })
        .catch(err => console.error("Error al obtener folio:", err));
}

async function gestionarSolicitud(id) {
    try {
        $('#tablaConversion tbody').empty();
        console.log(id);

        const resp = await fetch(`${URL_CONTROLADOR}?action=obtenerDetalle&id=${id}`);
        asignarSiguienteFolioCompra();

        if (!resp.ok) throw new Error(`Error de servidor: ${resp.status}`);

        const res = await resp.json();

        if (res.status !== 'success') {
            throw new Error(res.message || 'Error al obtener datos');
        }

        console.log(res);

        const items = res.data || [];

        if (items.length === 0) {
            Swal.fire('Info', 'La solicitud no tiene productos.', 'info');
            return;
        }

        // 🔥 MANEJO SEGURO DE DEUDA (UNA SOLA VEZ)
        const deuda = Number(res?.deuda?.[0]?.pendiente) || 0;

        // 🔹 Datos generales
        $('#uni-solicitud-id').val(id);
        $('#uni-folio').text(`#${id.toString().padStart(5, '0')}`);
        $('#uni-proveedor').val(items[0].proveedor_nombre || 'Sin Proveedor');
        $('#uni-proveedor-nombre').val(items[0].proveedor_nombre || '');

        // 🔹 Deuda segura
        $('#uni-proveedor-deuda').val(deuda);
        $('#input_pagar_deuda').val(0);
        $('#input_pagar_deuda').attr('max', deuda);
        $('.label-abono-info').text(`Máximo: ${deuda}`);

        let html = '';

        items.forEach((i, index) => {
            const factor = parseFloat(i.factor_conversion) || 1;
            const uBase = i.unidad_medida || 'pzas';
            const uRep = i.unidad_reporte || 'Mayoreo';
            const costo =i.costo;
            const cantidadSolicitada = parseFloat(i.cantidad) || 0;

            const cantMayoreo = Math.floor(cantidadSolicitada / factor);
            const cantSueltas = cantidadSolicitada % factor;
            const totalUnidad=cantidadSolicitada / factor;

            html += `
            <tr class="fila-item" data-index="${index}">
                <td>
                    <input type="hidden" name="items[${index}][producto_id]" value="${i.producto_id}">
                    <input type="hidden" class="h-factor" value="${factor}">
                    <div class="fw-bold text-dark">${i.producto_nombre} </div>
                    <small class="text-muted d-block">Total Pedido ${totalUnidad} ${uRep} </small>
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
                    <label class="form-label small text-danger fw-semibold mb-1">Faltantes</label>
                    <input type="number"
                        class="form-control form-control-sm border-danger shadow-sm i-faltante"
                        value="0" min="0" 
                        oninput="recalcularFila(${index})">

                    <input type="hidden" id="faltante_${index}"  
                        name="items[${index}][cantidad_faltante]" 
                        class="hidden-faltante" value="0">
                </td>

                <td>
                    <label class="form-label small text-success fw-semibold mb-1">Excedente</label>
                    <input type="number"
                        name="items[${index}][cantidad_excedente]"
                        class="form-control form-control-sm border-success shadow-sm i-excedente"
                        value="0" min="0" step="0.01"
                        oninput="recalcularFila(${index})">
                </td>

                <td>
                    <label class="small text-muted fw-bold">Costo Total Renglón</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light">$</span>
                        <input type="number" step="0.01" class="form-control i-costo-total" 
                            placeholder="0.00" required value="${costo}"
                            oninput="recalcularFila(${index})">
                    </div>

                    <input type="hidden" class="h-precio-lote">

                    <div class="mt-1" style="font-size:0.75rem">
                        Cost. Unit: 
                        <span class="s-precio-lote fw-bold text-secondary">$ 0.00</span>
                    </div>
                </td>

                <td>
                    <label class="small text-muted fw-bold">Almacén Destino</label>
                    <select class="form-select form-select-sm bg-light i-almacen-id">
                        <option value="${i.almacen_origen_id}" selected>
                            📍 ${i.almacen_nombre}
                        </option>
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

        $('.fila-item').each(function(idx) {
            recalcularFila(idx);
        });

        $('#modalGestionSolicitud').removeAttr('aria-hidden').modal('show');

    } catch (e) {
        console.error(e);
        Swal.fire('Error', e.message, 'error');
    }
}
function recalcularFila(index) {

    const fila = $(`.fila-item[data-index="${index}"]`);

    const factor = parseFloat(fila.find('.h-factor').val()) || 1;
    const mayoreo = parseFloat(fila.find('.i-mayoreo').val()) || 0;
    const sueltas = parseFloat(fila.find('.i-sueltas').val()) || 0;

    let faltante = parseFloat(fila.find('.i-faltante').val()) || 0;
    const excedente = parseFloat(fila.find('.i-excedente').val()) || 0;

    const costoTotalRenglon = parseFloat(fila.find('.i-costo-total').val()) || 0;

    const totalBase = (mayoreo * factor) + sueltas;

    // 🔴 evitar faltante mayor al total
    if (faltante > totalBase) faltante = totalBase;

    const totalPiezasFinal = totalBase - faltante + excedente;

    const displayTotal = Number.isInteger(totalPiezasFinal)
        ? totalPiezasFinal
        : totalPiezasFinal.toFixed(2);

    fila.find('.s-total-piezas').text(displayTotal);
    fila.find('.h-total-piezas').val(totalPiezasFinal);

    // actualizar hidden faltante
    fila.find('.hidden-faltante').val(faltante);

    let precioUnitario = totalBase > 0
        ? costoTotalRenglon / totalBase
        : 0;

    fila.find('.h-precio-lote').val(precioUnitario.toFixed(4));

    fila.find('.s-precio-lote').text('$ ' + precioUnitario.toLocaleString(undefined, {
        minimumFractionDigits: 2,
        maximumFractionDigits: 4
    }));

    actualizarGranTotal();
}

function actualizarGranTotal() {
    let granTotal = 0;
    $('.i-costo-total').each(function() {
        granTotal += parseFloat($(this).val()) || 0;
    });

    $('#uni-gran-total').text('$ ' + granTotal.toLocaleString(undefined, {
        minimumFractionDigits: 2
    }));
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

            const excedente = parseFloat(fila.find('.i-excedente').val()) || 0;
            const faltante = parseFloat(fila.find('.i-faltante').val()) || 0;

            // Captura el producto_id buscando específicamente en la fila actual
            const productoId = fila.find(`input[name="items[${index}][producto_id]"]`).val();

            detalle.push({
                producto_id: productoId,
                input_mayoreo: fila.find('.i-mayoreo').val() || 0,
                input_sueltas: fila.find('.i-sueltas').val() || 0,

                // ✅ CORRECTO
                cantidad_excedente: excedente,
                cantidad_faltante: faltante,

                total_item: costoTotal,
                precio_lote: fila.find('.h-precio-lote').val(),
                hidden_factor: fila.find('.h-factor').val() || 1,

                almacenes: {
                    [almId]: {
                        activo: 'on',
                        cantidad: cantTotal
                    }
                }
            });
        }

        // Validación preventiva
        if (detalle.length === 0 || detalle[0].total_item <= 0) {
            Swal.fire('Atención', 'El costo del producto debe ser mayor a 0 para generar el lote.',
                'warning');
            return;
        }

        // Preparamos el FormData con todos los campos necesarios para el controlador
        const formData = new FormData(this);
        formData.append('action', 'guardarCompraCompleta');
        formData.append('items', JSON.stringify(detalle));
        formData.append('solicitud_id', $('#uni-solicitud-id').val());
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
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: URL_CONTROLADOR,
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    success: function(res) {
                        if (res.success) {
                            Swal.fire('¡Éxito!', res.message, 'success').then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire('Error de negocio', res.message || 'Error desconocido', 'error');
                        }
                    },
                    error: function(jqXHR) {
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
}); </script>
    <script>
    /**
     * Llena el modal de impresión con la data de la solicitud
     */
    function prepararImpresion(id) {
    fetch(`${URL_CONTROLADOR}?action=obtenerDetalle&id=${id}`)
        .then(res => res.json())
        .then(res => {

            if (res.status !== 'success') return;

            const data = res.data;
            const infoBase = data[0];

            // 🔹 CABECERA
            $('#print-folio').text(`FOLIO: #${id.toString().padStart(5, '0')}`);
            $('#print-fecha').text(`Fecha: ${new Date().toLocaleDateString()}`);
            $('#print-almacen').text(infoBase.almacen_nombre);
            $('#print-proveedor').text(infoBase.proveedor_nombre || 'No especificado');

            let html = '';

            data.forEach(i => {

                const factor = parseFloat(i.factor_conversion) || 1;
                const uBase = i.unidad_medida || 'pzas';
                const uRep = i.unidad_reporte || 'Mayoreo';
                const costo=i.costo;

                const cantidad = parseFloat(i.cantidad) || 0;

                // 🔥 SOPORTE PARA FACTORES DECIMALES (ej: 1.5)
                let cantMayoreo = 0;
                let cantSueltas = 0;

                if (factor > 1) {
                    cantMayoreo = Math.floor(cantidad / factor);
                    cantSueltas = (cantidad % factor);
                } else {
                    // Si factor es decimal tipo 1.5
                    cantMayoreo = (cantidad / factor).toFixed(2);
                    cantSueltas = 0;
                }

                const totalUnidades = (cantidad / factor).toFixed(2);

                html += `
                <tr>
                    <td style="width:40%">
                        <div class="fw-bold">${i.producto_nombre}</div>
                        <small class="text-muted">SKU: ${i.sku || '-'}</small><br>
                        <small class="text-muted">
                            1 ${uRep} = ${factor} ${uBase}
                        </small>
                    </td>

                   
<td class="text-center text-primary fw-bold">
                        ${totalUnidades} ${uRep}
                    </td>
                    <td class="text-center">
                        <div><b>${cantMayoreo}</b> ${uRep}</div>
                      
                    </td>
                    <td class="text-center fw-bold">
                        ${cantidad} ${uBase}
                    </td>

                    
                     

                    <td class="text-center text-primary fw-bold">
                        ${costo}
                    </td>
                </tr>`;
            });

            $('#print-tabla-cuerpo').html(html);

            new bootstrap.Modal(document.getElementById('modalImprimirSolicitud')).show();
        })
        .catch(err => {
            console.error(err);
        });
}
    /**
     * Llama al comando de impresión del navegador
     */
    function ejecutarImpresion() {
        // 1. Obtener el contenido del área de impresión
        const contenido = document.getElementById('areaImpresion').innerHTML;
        const folio = $('#print-folio').text();

        // 2. Crear una ventana nueva
        const ventana = window.open('', '_blank', 'height=600,width=800');

        // 3. Escribir el HTML necesario para que se vea bien
        ventana.document.write(`
        <html>
            <head>
                <title>Imprimir ${folio}</title>
                <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
                <style>
                    body { font-family: 'Inter', sans-serif; padding: 30px; }
                    .table-bordered th, .table-bordered td { border: 1px solid #000 !important; }
                    .fw-bold { font-weight: bold !important; }
                    @media print {
                        .no-print { display: none; }
                        @page { margin: 1cm; }
                    }
                    .firma-linea { border-top: 1px solid #000; margin-top: 50px; text-align: center; padding-top: 5px; font-size: 12px; }
                </style>
            </head>
            <body>
                ${contenido}
                <script>
                    // Esperar a que cargue el CSS y luego imprimir
                    window.onload = function() {
                        window.print();
                        window.onafterprint = function() { window.close(); };
                    };
                <\/script>
            </body>
        </html>
    `);

        ventana.document.close();
    }
    </script>
</body>

</html>