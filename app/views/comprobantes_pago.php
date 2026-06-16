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
                    <h1 class="h3 fw-bold mb-1">Comprobante de pago</h1>
                    <p class="text-muted small">Gestión de Comprobate de pago</p>
                </div>
                <div class="col-md-4 text-md-end">
                    <button class="btn btn-dark" onclick="nuevaCotizacion()">
                        <i class="bi bi-plus-lg me-2"></i> Crear Comprobante de pago
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
                        <option value="activo">Activo</option>
                     
                        <option value="cancelado">Cancelado</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted text-uppercase">Buscador</label>
                    <div class="input-group shadow-sm">
                        <span class="input-group-text bg-white border-end-0"><i
                                class="bi bi-search text-muted"></i></span>
                        <input type="text" id="buscadorGeneral" class="form-control border-start-0 ps-0"
                            placeholder="Folio o Cliente">
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
                            <th>Cliente</th>
                            <th>Almacén</th>
                            <th>Monto</th>
                            <th>Estado</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cotizaciones as $s): ?>
                        <tr>
                            <td><span class="text-dark fw-bold">#<?= str_pad($s['id'], 5, "0", STR_PAD_LEFT) ?></span>
                            </td>
                            <td class="text-muted small"><?= date('d/m/Y H:i', strtotime($s['fecha']))?></td>
                            <td class="fw-medium"><?= htmlspecialchars($s['nombre_comercial'] ?? 'Sin asignar') ?></td>
                            <td><span
                                    class="badge bg-light text-dark border"><?= htmlspecialchars($s['almacen']) ?></span>
                            </td>

                            <td><?= htmlspecialchars($s['monto']) ?></td>
                             <td><span
                                    class="badge bg-light text-dark border"><?= htmlspecialchars($s['estado']) ?></span>
                            </td>
                            <td>
                                <button type="button" class="btn btn-primary" onclick="imprmirComprobante(<?= $s['id'] ?>)">
    Imprimir
</button> <?php if ($s['estado'] !== 'cancelado'): ?> 
    <button class="btn btn-danger" onclick="eliminarSolicitud(<?= $s['id'] ?>)">Cancelar</button>
<?php endif; ?>                 </td>
                            
                          
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>


    <div class="modal fade" id="modalGestionSolicitud" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 24px; overflow:hidden;">

                <!-- HEADER -->
                <div class="modal-header bg-success text-white px-4 py-3 border-0">
                    <div>
                        <h5 class="fw-bold mb-0">
                            <i class="bi bi-box-arrow-in-down me-2"></i>
                            Convertir Solicitud <span name="uni-folio" id="uni-folio"></span>
                        </h5>
                        <small class="opacity-75">Generación de compra e inventario</small>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <form id="formConvertirCompra" enctype="multipart/form-data">

                    <input type="hidden" name="solicitud_id" id="uni-solicitud-id">

                    <div class="modal-body bg-light px-4 py-4">

                        <!-- ====================================== -->
                        <!-- CARD PRINCIPAL -->
                        <!-- ====================================== -->

                        <div class="bg-white rounded-4 shadow-sm p-4 mb-4 border">

                            <!-- HEADER -->
                            <div class="d-flex justify-content-between align-items-center mb-4">

                                <div>
                                    <h5 class="fw-bold mb-1 text-dark">
                                        <i class="bi bi-cart-check me-2 text-success"></i>
                                        Información de Compra
                                    </h5>

                                    <small class="text-muted">
                                        Datos generales de la entrada
                                    </small>
                                </div>

                                <span class="badge bg-success-subtle text-success px-3 py-2 rounded-pill fw-semibold">
                                    Compra en proceso
                                </span>

                            </div>

                            <!-- ====================================== -->
                            <!-- FILA 1 -->
                            <!-- ====================================== -->

                            <div class="row g-3">

                                <!-- ALMACÉN -->
                                <div class="col-md-3">

                                    <label class="form-label small fw-bold text-muted text-uppercase">
                                        Almacén destino
                                    </label>

                                    <?php if (isset($es_admin) && $es_admin): ?>

                                    <select id="almacen_id2" name="almacen_id2" class="form-select rounded-3 shadow-sm"
                                        required>
                                        <option value="">-- Seleccionar --</option>

                                        <?php foreach ($almacenes as $alm): ?>

                                        <option value="<?= $alm['id'] ?>">
                                            <?= htmlspecialchars($alm['nombre']) ?>
                                        </option>

                                        <?php endforeach; ?>

                                    </select>

                                    <?php else: ?>

                                    <input type="text" class="form-control rounded-3 shadow-sm bg-light fw-bold"
                                        value="<?= htmlspecialchars($almacenes[0]['nombre'] ?? 'Almacén Asignado') ?>"
                                        readonly>

                                    <input type="hidden" id="almacen_id2" name="almacen_id2"
                                        value="<?= $almacen_usuario ?? ($almacenes[0]['id'] ?? '') ?>">

                                    <?php endif; ?>

                                </div>

                                <!-- PROVEEDOR -->
                                <div class="col-md-3">

                                    <label class="form-label small fw-bold text-muted text-uppercase">
                                        Proveedor
                                    </label>

                                    <input type="text" id="uni-proveedor"
                                        class="form-control rounded-3 shadow-sm bg-light fw-bold" readonly>

                                    <input type="hidden" name="proveedor" id="uni-proveedor-nombre">

                                </div>

                                <!-- FOLIO -->
                                <div class="col-md-2">

                                    <label class="form-label small fw-bold text-muted text-uppercase">
                                        Folio factura
                                    </label>

                                    <input type="text" name="folio" class="form-control rounded-3 shadow-sm"
                                        placeholder="FAC-000" required>

                                </div>

                                <!-- MÉTODO -->
                                <div class="col-md-2">

                                    <label class="form-label small fw-bold text-muted text-uppercase">
                                        Método pago
                                    </label>

                                    <select name="metodo_pago" id="metodo_pago" class="form-select rounded-3 shadow-sm"
                                        required>
                                        <option value="">Seleccione...</option>
                                        <option value="Efectivo">Efectivo</option>
                                        <option value="Transferencia">Transferencia</option>
                                        <option value="Tarjeta">Tarjeta</option>
                                    </select>

                                </div>

                                <!-- EVIDENCIA -->
                                <div class="col-md-2">

                                    <label class="form-label small fw-bold text-muted text-uppercase">
                                        Evidencia
                                    </label>

                                    <input type="file" name="evidencia_compra" class="form-control rounded-3 shadow-sm"
                                        accept="image/*,.pdf">

                                </div>

                            </div>

                            <!-- ====================================== -->
                            <!-- FILA 2 -->
                            <!-- ====================================== -->

                            <div class="row g-3 mt-2 align-items-stretch">

                                <!-- DEUDA -->
                                <div class="col-md-4">

                                    <div class="bg-danger-subtle border border-danger-subtle rounded-4 p-3 h-100">

                                        <div class="d-flex justify-content-between align-items-center">

                                            <div>

                                                <small class="text-danger fw-semibold d-block mb-1">
                                                    Deuda actual proveedor
                                                </small>

                                                <input type="text" name="deudaProveedor" id="uni-proveedor-deuda"
                                                    class="form-control border-0 bg-transparent text-danger fw-bold fs-4 p-0"
                                                    readonly>

                                            </div>

                                            <i class="bi bi-exclamation-triangle-fill text-danger fs-2"></i>

                                        </div>

                                    </div>

                                </div>

                                <!-- ABONO -->
                                <div class="col-md-3">

                                    <div class="bg-primary-subtle border border-primary-subtle rounded-4 p-3 h-100">

                                        <label class="form-label small text-primary fw-bold mb-2">
                                            <i class="bi bi-cash-coin me-1"></i>
                                            Abono a deuda
                                        </label>

                                        <input type="number" id="input_pagar_deuda" name="saldo_a_pagar"
                                            class="form-control border-primary shadow-sm rounded-3" value="0" min="0"
                                            step="0.1">

                                        <small class="label-abono-info text-muted mt-2 d-block"></small>

                                    </div>

                                </div>

                                <!-- TOTAL -->
                                <div class="col-md-5">

                                    <div
                                        class="bg-success-subtle border border-success-subtle rounded-4 p-3 h-100 text-end">

                                        <small class="text-success fw-semibold text-uppercase d-block mb-2">
                                            Total compra
                                        </small>

                                        <span class="fw-bold text-success" id="uni-gran-total" style="font-size:2rem;">
                                            $ 0.00
                                        </span>

                                    </div>

                                </div>

                            </div>

                        </div>

                        <!-- ====================================== -->
                        <!-- TABLA -->
                        <!-- ====================================== -->

                        <div class="bg-white rounded-4 shadow-sm border overflow-hidden">

                            <div class="p-3 border-bottom bg-light">

                                <h6 class="fw-bold mb-1">
                                    <i class="bi bi-box-seam me-2 text-success"></i>
                                    Productos
                                </h6>

                                <small class="text-muted">
                                    Conversión de unidades y costos
                                </small>

                            </div>

                            <div class="table-responsive">

                                <table class="table align-middle mb-0" id="tablaConversion">

                                    <thead class="table-light">

                                        <tr class="small text-muted">

                                            <th class="ps-4">Producto</th>
                                            <th>Mayoreo</th>
                                            <th>Sueltas</th>
                                            <th>Faltantes</th>
                                            <th>Excedentes</th>
                                            <th>Costo</th>
                                            <th class="text-end pe-4">Total</th>

                                        </tr>

                                    </thead>

                                    <tbody></tbody>

                                </table>

                            </div>

                        </div>

                    </div>

                    <!-- ====================================== -->
                    <!-- FOOTER -->
                    <!-- ====================================== -->

                    <div class="modal-footer bg-light border-0 px-4 pb-4">

                        <button type="button" class="btn btn-outline-secondary rounded-pill px-4"
                            data-bs-dismiss="modal">
                            Cancelar
                        </button>

                        <button type="submit" class="btn btn-success rounded-pill px-5 shadow-sm fw-semibold">
                            <i class="bi bi-check2-circle me-2"></i>
                            Confirmar compra
                        </button>

                    </div>

                </form>
            </div>
        </div>
    </div>
   <div class="modal fade" id="modalImprimirSolicitud" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">

            <div class="modal-header text-white border-0 py-3"
                style="background: linear-gradient(135deg, #1f2a37 0%, #334155 100%);">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-receipt fs-4"></i> <h5 class="fw-bold mb-0">Detalle del Comprobante</h5>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body p-0 bg-secondary bg-opacity-10">
    <div id="areaImpresion" class="bg-white my-3 mx-auto p-4 shadow-sm text-dark" 
         style="width: 320px; font-family: 'Courier New', Courier, monospace; font-size: 0.85rem; border-radius: 4px;">
        
        <div class="text-center mb-2">
            <h4 class="fw-bold text-uppercase mb-0" style="letter-spacing: 1px; font-family: sans-serif; font-weight: 800; color: #1f2a37;">
                CF SYSTEM
            </h4>
            <p class="text-muted small mb-1" style="font-family: sans-serif; font-size: 0.7rem;">COMPROBANTE DE PAGO</p>
            
            <div class="small fw-bold border-top border-bottom py-1 my-2" style="border-style: dashed !important; border-color: #000 !important;" id="print-folio">
                FOLIO: #00000
            </div>
        </div>
         
        <div class="py-1" style="font-family: sans-serif;">
            <span class="text-uppercase text-muted fw-bold d-block" style="font-size: 0.65rem; letter-spacing: 0.5px;">Cliente:</span>
            <div class="fw-bold text-dark fs-6" id="print-cliente" style="line-height: 1.2;">---</div>
        </div>

        <div class="mb-2">
           <table class="w-100 style-ticket-table" style="font-size: 0.8rem; line-height: 1.4;">
    <tr>
        <td class="text-muted py-1" style="width: 40%;">NÚMERO VENTA:</td>
        <td class="fw-bold text-end py-1 text-dark" id="print-numero_venta">---</td>
    </tr>
    <tr>
        <td class="text-muted py-1">FECHA:</td>
        <td class="fw-semibold text-end py-1 text-dark" id="print-fecha_dep">---</td>
    </tr>
    <tr>
        <td class="text-muted py-1">REFERENCIA:</td>
        <td class="text-end py-1">
            <input type="text" class="form fw-semibold text-end text-dark border-0 bg-transparent p-0 w-100" id="print-referencia" value="---">
        
        </td>
    </tr>
</table>
        </div>

       

        
      <div >
    <table class="w-100" style="font-family: sans-serif; table-layout: fixed; font-size: 11px; line-height: 1.2;">
        <tr>
            <td style="width: 45%; color: #6b7280; font-weight: bold; text-transform: uppercase; padding-bottom: 4px;">
                MÉTODO PAGO:
            </td>
            <td class="text-end fw-bold text-dark text-uppercase" id="metodo_pago_dep" style="width: 55%; font-size: 12px; padding-bottom: 4px;">
                ---
            </td>
        </tr>
        
        <tr>
            <td style="width: 45%; color: #6b7280; font-weight: bold; text-transform: uppercase;">
                TOTAL RECIBIDO:
            </td>
            <td class="text-end fw-bold text-dark" id="costo_total" style="width: 55%; font-size: 14px;">
                $0.00
            </td>
        </tr>
    </table>
</div>

        <div class="text-center mt-4 pt-2 border-top" style="border-style: dashed !important; border-color: #000 !important; font-family: sans-serif; font-size: 0.7rem;">
            <p class="text-muted mb-0">*** Gracias por su confianza ***</p>
        </div>

        <div style="display:none;">
            <div id="print-almacen">---</div>
            <div id="print-usuario">---</div>
        </div>

    </div>
</div>

            <div class="modal-footer bg-light border-top-0 justify-content-end gap-2 py-3 px-4" id="footer">
                <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">
                    Cerrar
                </button>
            </div>

        </div>
    </div>
</div>
   
    <style>
    /* =========================
   MODAL BASE
========================= */
    #modalImprimirSolicitud .modal-content {
        border-radius: 10px;
        border: none;
        box-shadow: 0 12px 35px rgba(0, 0, 0, 0.12);
        overflow: hidden;
        background: #fff;
    }

    #modalImprimirSolicitud .modal-header {
        background: #ffffff;
        border-bottom: 1px solid #e5e7eb;
        color: #111827;
        padding: 1.2rem 1.5rem;
    }

    #modalImprimirSolicitud .modal-footer {
        background: #ffffff;
        border-top: 1px solid #e5e7eb;
        padding: 1rem 1.5rem;
    }

    /* =========================
   ÁREA DE IMPRESIÓN
========================= */
    #areaImpresion {
        padding: 2rem;
        background: #ffffff;
        color: #111827;
        font-family: "Segoe UI", system-ui, sans-serif;
    }

    /* =========================
   ENCABEZADOS
========================= */
    #areaImpresion h2 {
        font-weight: 700;
        font-size: 1.6rem;
        color: #1f2937;
        margin-bottom: 0.3rem;
    }

    #areaImpresion h5 {
        font-weight: 500;
        color: #4b5563;
    }

    /* =========================
   BLOQUES INFO
========================= */
    .card {
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        box-shadow: none;
        background: #ffffff;
    }

    .card:hover {
        transform: none;
        box-shadow: none;
    }

    /* =========================
   TABLA ESTILO DOCUMENTO
========================= */
    .table {
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        overflow: hidden;
    }

    .table thead th {
        background: #f3f4f6;
        color: #111827;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 1px solid #e5e7eb;
        padding: 12px;
    }

    .table tbody td {
        border-color: #f1f5f9;
        padding: 10px;
        font-size: 0.95rem;
    }

    .table tbody tr:nth-child(even) {
        background: #fafafa;
    }

    /* =========================
   DIVISOR LIMPIO
========================= */
    .divider {
        height: 1px;
        background: #e5e7eb;
        margin: 1.5rem 0;
    }

    /* =========================
   FIRMAS
========================= */
    .signature-line {
        width: 180px;
        height: 1px;
        background: #111827;
        margin-bottom: 6px;
    }

    .signature-label {
        font-size: 0.75rem;
        color: #6b7280;
        text-transform: uppercase;
    }

    /* =========================
   BOTONES / ACCIONES
========================= */
    .btn-primary {
        background: #2563eb;
        border: none;
    }

    .btn-primary:hover {
        background: #1d4ed8;
    }

    /* =========================
   PRINT MODE
========================= */
    @media print {

        body * {
            visibility: hidden;
        }

        #modalImprimirSolicitud,
        #modalImprimirSolicitud * {
            visibility: visible;
        }

        #modalImprimirSolicitud {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }

        .modal-header,
        .modal-footer {
            display: none !important;
        }

        .modal-content {
            box-shadow: none !important;
            border: none !important;
        }

        #areaImpresion {
            padding: 1rem;
        }

        .table thead th {
            background: #f0f0f0 !important;
            color: #000 !important;
        }

        .table tbody tr:nth-child(even) {
            background: #fff !important;
        }
    }
    </style>

    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>





    <?php require_once __DIR__ . '/comprobantes_pago/modalComprobante.php'; ?>
    <?php require_once __DIR__ . '/cotizacionesModales/nuevoClienteModal.php'; ?>
    <?php require_once __DIR__ . '/egresosComponets/agregarPoductoModal.php'; ?>
    <?php require_once __DIR__ . '/egresosComponets/modalProveedoresCompra.php'; ?>
    <script>
    let totalGlobalPago = 0;
    let datost=0;
async function imprmirComprobante(id) {
    try {
        console.log("Solicitando ID:", id);

        const resp = await fetch(`/cfsistem/app/controllers/comprobantesPagoController.php?action=obtenerDetalle&id=${id}`);
        
        // CORRECCIÓN 1: Cambiado .data() por .json()
        const datos = await resp.json(); 
        
        console.log('RESPUESTA DEL SERVIDOR:', datos);

        if (datos.status !== 'success') {
            Swal.fire('Error', datos.message || 'No se encontraron datos', 'error');
            return;
        }

        const data = datos.data; 

        // 1. FORMATEAR EL MONTO A MONEDA (MXN)
        const montoFormateado = parseFloat(data.monto).toLocaleString('es-MX', { 
            style: 'currency', 
            currency: 'MXN' 
        });

        // 2. INYECTAR LOS DATOS DIRECTAMENTE EN EL MODAL
        $('#print-folio').text(`#${String(data.id).padStart(5, '0')}`);
        $('#print-cliente').text(data.nombre_comercial);
        
        // CORRECCIÓN 2: Se usa 'nombre_almacen' que es el alias que viene del SQL
        $('#print-almacen').text(data.nombre_almacen); 
        
        $('#print-usuario').text(data.usuario);
        $('#print-referencia').val(data.referencia || 'Sin referencia');
        $('#print-fecha_dep').text(data.fecha);
        
        $('#costo_total').text(montoFormateado);
        $('#metodo_pago_dep').text(data.metodo_pago);
        $('#print-numero_venta').text(data.numero_ventas);

        // 3. GENERAR EL BOTÓN EN EL FOOTER
        const footer = `
        
            
            <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">
    Cerrar
</button>

<button type="button" class="btn btn-dark rounded-pill px-4" onclick="prepararImpresion(${data.id})">
    Imprimir
</button>

<button type="button" class="btn btn-outline-success rounded-pill px-4" onclick="actualizar(${data.id})">
    Actualizar
</button>
        `;
        $('#footer').html(footer);

        // 4. LEVANTAR EL MODAL
        const miModal = new bootstrap.Modal(document.getElementById('modalImprimirSolicitud'));
        miModal.show();

    } catch (e) {
        console.error("Error en imprmirComprobante:", e);
        Swal.fire('Error', 'Fallo de conexión al recuperar el detalle', 'error');
    }
}
async function prepararImpresion(id) {
        try {

            $('#tablaConversion tbody').empty();

           console.log("Solicitando ID:", id);

        const resp = await fetch(`/cfsistem/app/controllers/comprobantesPagoController.php?action=obtenerDetalle&id=${id}`);
        
        // CORRECCIÓN 1: Cambiado .data() por .json()
        const datos = await resp.json(); 
        
        console.log('RESPUESTA DEL SERVIDOR:', datos);

        if (datos.status !== 'success') {
            Swal.fire('Error', datos.message || 'No se encontraron datos', 'error');
            return;
        }
       

        const data = datos.data; 
        let ref=$('#print-referencia').val();
        console.log(ref);


            

            const infoBase = data[0];
const montoFormateado = parseFloat(data.monto).toLocaleString('es-MX', { 
            style: 'currency', 
            currency: 'MXN' 
        });

        // 2. INYECTAR LOS DATOS DIRECTAMENTE EN EL MODAL
        $('#print-folio').text(`#${String(data.id).padStart(5, '0')}`);
        $('#print-cliente').text(data.nombre_comercial);
        
        // CORRECCIÓN 2: Se usa 'nombre_almacen' que es el alias que viene del SQL
        $('#print-almacen').text(data.nombre_almacen); 
        
        $('#print-usuario').text(data.usuario);
        
        $('#print-referencia').val(ref);
        $('#print-fecha_dep').text(data.fecha);
        
        $('#costo_total').text(montoFormateado);
        $('#metodo_pago_dep').text(data.metodo_pago);
         $('#numero_venta').text(data.numero_ventas);

        


           

            ejecutarImpresion();

        } catch (e) {
            console.error(e);
        }
    }

   function ejecutarImpresion() {
    // 1. Clonar el contenedor para no alterar el modal visual del usuario
    const contenedorOriginal = document.getElementById('areaImpresion');
    const clon = contenedorOriginal.cloneNode(true);

    // 2. TRUCO CLAVE: Pasar el valor real de los inputs del DOM original a su clon
    const inputOriginal = contenedorOriginal.querySelector('#print-referencia');
    const inputClonado = clon.querySelector('#print-referencia');
    
    if (inputOriginal && inputClonado) {
        // Transferimos el valor real actual como un atributo físico para que 'innerHTML' lo detecte
        inputClonado.setAttribute('value', inputOriginal.value);
    }

    // Ahora sí extraemos el HTML con los valores reales inyectados físicamente
    const contenido = clon.innerHTML;
    const folio = $('#print-folio').text();

    // 3. Crear una ventana nueva
    const ventana = window.open('', '_blank', 'height=600,width=800');

    // 4. Escribir el HTML necesario
    ventana.document.write(`
    <html>
        <head>
            <title>Imprimir ${folio}</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
            <style>
                /* =========================================================================
                   ESTILOS BASE (Estructura de Ticket POS)
                   ========================================================================= */
                body { 
                    font-family: 'Courier New', Courier, monospace;
                    padding: 0; 
                    margin: 0;
                    background: #ffffff;
                    color: #000000;
                    font-size: 9pt;
                    line-height: 1.3;
                    -webkit-print-color-adjust: exact;
                    print-color-adjust: exact;
                }
                
                h1, .h1 { font-family: sans-serif; font-size: 1.4rem !important; font-weight: 800; text-align: center; margin-bottom: 2px; }
                h2, .h2 { font-family: sans-serif; font-size: 1.1rem !important; font-weight: 700; text-align: center; margin-bottom: 4px; }
                h3, .h3 { font-family: sans-serif; font-size: 0.95rem !important; font-weight: 700; }
                p { margin-bottom: 3px; }

                /* CORRECCIÓN: Corrección en la sintaxis de la propiedad letter-spacing */
                .metodo {
                    font-size: 0.75rem !important; 
                    letter-spacing: 0.5px !important;
                }
                .pago {
                    font-size: 0.75rem !important; 
                    letter-spacing: 0.5px !important;
                }

                /* =========================================================================
                   COMPONENTES ADAPTADOS A TICKET
                   ========================================================================= */
                .table {
                    width: 100% !important;
                    margin-bottom: 6px !important;
                    border: none !important;
                }
                .table-bordered th, .table-bordered td { 
                    border: none !important; 
                }
                
                .table thead th {
                    background-color: transparent !important;
                    color: #000000 !important;
                    padding: 3px 0 !important;
                    font-size: 8.5pt !important;
                    text-transform: uppercase;
                    border-bottom: 1px dashed #000000 !important;
                    font-weight: 700;
                }
                .table tbody td {
                    padding: 3px 0 !important;
                    font-size: 8.5pt !important;
                    border: none !important;
                }
                .table tbody tr:nth-child(even) {
                    background: transparent !important;
                }

                .divider, .ticket-divider {
                    border-top: 1px dashed #000000 !important;
                    margin: 8px 0;
                    width: 100%;
                    display: block;
                }

                .card, .bg-light {
                    background: transparent !important;
                    border: none !important;
                    padding: 0 !important;
                    margin-bottom: 6px !important;
                    border-radius: 0 !important;
                }

                .fw-bold { font-weight: 700 !important; }
                
                .firma-linea { 
                    border-top: 1px dashed #000000; 
                    margin-top: 30px; 
                    text-align: center; 
                    padding-top: 3px; 
                    font-size: 8pt; 
                    text-transform: uppercase;
                    color: #000000;
                    font-weight: 600;
                }

                /* =========================================================================
                   CONFIGURACIÓN DE IMPRESIÓN TÉRMICA (Ancho Fijo)
                   ========================================================================= */
                @media print {
                    @page { 
                        size: 80mm auto; 
                        margin: 0mm 3mm 0mm 3mm; 
                    }
                    
                    body { 
                        padding: 0 !important; /* CORRECCIÓN: Quitados los márgenes toscos */
                        width: 100% !important; /* CORRECCIÓN: Asegurar el 100% de los 80mm del rollo */
                    }
                    
                    .no-print { display: none !important; }

                    .table, tr, img, p, div {
                        page-break-inside: avoid !important;
                    }
                }
            </style>
        </head>
        <body>
        <img
    src="/cfsistem/public/assets/logo.ico"
    style="
        position: fixed;
        top: 10.5%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 180px;
        opacity: 0.08;
        z-index: -1;
    "
>
            <div style="width: 100%; max-width: 80mm; margin: 0 auto;">
                ${contenido}
            </div>
            <script>
                window.onload = function() {
                    setTimeout(function() {
                        window.print();
                        window.close();
                    }, 250); // Delay de seguridad para procesar fuentes antes de mandar a la tiquetera
                };
            <\/script>
        </body>
    </html>
    `);

    ventana.document.close();
}$(document).ready(function() {
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
            table.column(5).search(this.value).draw();
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
      async function eliminarSolicitud(id) {
        console.log(id);
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
            const resp = await fetch(`/cfsistem/app/controllers/comprobantesPagoController.php?action=eliminar`, {
                method: 'POST',
                body: fd
            });
            const res = await resp.json();

if (res.status === 'success') {
    Swal.fire({
        title: '¡Éxito!',
        text: res.message || 'Operación realizada correctamente.',
        icon: 'success',
        confirmButtonText: 'Aceptar',
        confirmButtonColor: '#1f2a37', // Combinando con el tono oscuro de tu CF System
        timer: 2000, // Se cierra automáticamente en 2 segundos si no dan clic
        timerProgressBar: true
    }).then(() => {
        // Al dar clic en "Aceptar" o cumplirse el tiempo, se recarga la página
        location.reload();
    });
} else {
    // Por si el servidor responde con un error controlado
    Swal.fire({
        title: 'Error',
        text: res.message || 'Ocurrió un problema en el servidor.',
        icon: 'error',
        confirmButtonText: 'Entendido',
        confirmButtonColor: '#334155'
    });
}
        }
    }

      async function actualizar(id) {
    console.log("Actualizando ID:", id);
    
    // Obtenemos el valor de la referencia desde tu input del ticket
    let referencia = $('#print-referencia').val();

    const r = await Swal.fire({
        title: '¿Actualizar referencia?',
        text: 'Se guardará la nueva referencia en este comprobante.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Sí, guardar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#1f2a37',
        cancelButtonColor: '#6b7280'
    });

    if (r.isConfirmed) {
        // CORRECCIÓN 1: Estructurar correctamente el FormData (una línea por variable)
        const fd = new FormData();
        fd.append('id', id);
        fd.append('referencia', referencia);

        try {
            // CORRECCIÓN 2: Cambiado de '?action=eliminar' a '?action=actualizar'
            const resp = await fetch(`/cfsistem/app/controllers/comprobantesPagoController.php?action=actualizar`, {
                method: 'POST',
                body: fd
            });
            
            const res = await resp.json();

            if (res.status === 'success') {
                Swal.fire({
                    title: '¡Éxito!',
                    text: res.message || 'Operación realizada correctamente.',
                    icon: 'success',
                    confirmButtonText: 'Aceptar',
                    confirmButtonColor: '#1f2a37',
                    timer: 2000,
                    timerProgressBar: true
                }).then(() => {
                    location.reload();
                });
            } else {
                Swal.fire({
                    title: 'Error',
                    text: res.message || 'Ocurrió un problema en el servidor.',
                    icon: 'error',
                    confirmButtonText: 'Entendido',
                    confirmButtonColor: '#334155'
                });
            }
        } catch (error) {
            Swal.fire({
                title: 'Error de Red',
                text: 'No se pudo conectar con el servidor.',
                icon: 'error',
                confirmButtonText: 'Entendido'
            });
        }
    }
}
    </script>

</body>

</html>