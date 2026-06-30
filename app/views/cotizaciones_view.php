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
    <title>Cotizaciones| cfsistem</title>

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
                    <h1 class="h3 fw-bold mb-1">Cotizaciones</h1>
                    <p class="text-muted small">Gestión de cotizaciones de materiales</p>
                </div>
                <div class="col-md-4 text-md-end">
                    <button class="btn btn-add" onclick="nuevaCotizacion()">
                        <i class="bi bi-plus-lg me-2"></i> Crear Cotizacion
                    </button>
                </div>
            </div>

          <div class="glass-card p-4 mb-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="fw-bold mb-1">
                <i class="bi bi-funnel-fill text-primary me-2"></i>
                Filtros de búsqueda
            </h5>
            <small class="text-muted">
                Filtra las cotizaciones por fecha, almacén, estado o cliente.
            </small>
        </div>
    </div>

    <div class="row g-3 align-items-end">

        <!-- Fecha Inicio -->
        <div class="col-lg-2 col-md-6">
            <label class="form-label small fw-bold text-muted text-uppercase">
                Inicio
            </label>

            <input
                type="date"
                id="fechaInicio"
                value="<?= date('Y-m-01') ?>"
                class="form-control border-0 bg-light shadow-sm"
                style="border-radius:12px;">
        </div>

        <!-- Fecha Fin -->
        <div class="col-lg-2 col-md-6">
            <label class="form-label small fw-bold text-muted text-uppercase">
                Fin
            </label>

            <input
                type="date"
                id="fechaFin"
                value="<?= date('Y-m-d') ?>"
                class="form-control border-0 bg-light shadow-sm"
                style="border-radius:12px;">
        </div>

        <!-- Almacén -->
        <div class="col-lg-3 col-md-6">
            <label class="form-label small fw-bold text-muted text-uppercase">
                Almacén
            </label>

            <select id="filtroAlmacen" class="form-select border-0 bg-light shadow-sm"
                style="border-radius:12px;">

                <?php if (isset($es_admin) && $es_admin): ?>

                <option value="">Todos los almacenes</option>
<?php endif ;?>
                <?php foreach ($almacenes as $alm): ?>

                <option value="<?= htmlspecialchars($alm['id']) ?>">
                    <?= htmlspecialchars($alm['nombre']) ?>
                </option>

                <?php endforeach; ?>

            </select>
        </div>

        <!-- Estado -->
        <div class="col-lg-2 col-md-6">
            <label class="form-label small fw-bold text-muted text-uppercase">
                Estado
            </label>

            <select id="filtroEstado" class="form-select border-0 bg-light shadow-sm"
                style="border-radius:12px;">

                <option value="">Todos</option>
                <option value="PENDIENTE">Pendiente</option>
                <option value="COMPLETADO">Completado</option>
                <option value="CANCELADO">Cancelado</option>

            </select>
        </div>
               <?php if ($puede == true): ?>
<div class="col-md-2">
    <label for="select-usuarios" class="form-label fw-bold small text-muted text-uppercase">Vendedor</label>
    <select class="form-select rounded-pill" id="select-usuarios" name="usuario_id" onchange="getVentas()">
     <option value="" > Seleccione vendedor</option>
    </select>
</div>
<?php endif; ?>

        <!-- Buscador -->
        <div class="col-lg-3 col-md-12">
            <label class="form-label small fw-bold text-muted text-uppercase">
                Buscar
            </label> 

            <div class="input-group shadow-sm">

                <span class="input-group-text bg-light border-0">
                    <i class="bi bi-search text-secondary"></i>
                </span>

                <input
                    type="text"
                    id="buscadorGeneral"
                    class="form-control border-0 bg-light"
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
                            <th>Proveedor</th>
                            <th>Almacén</th>
                            <th>Vendedor</th>
                            <th>Estado</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tablaCotizacionesListar">
                        
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

                <!-- HEADER -->
                <div class="modal-header text-white border-0"
                    style="background: linear-gradient(135deg, #1f2a37 0%, #334155 100%);">
                    <h5 class="fw-bold mb-0">
                        Vista de Cotizacion
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body p-0">

                    <div id="areaImpresion" class="p-5 bg-white" style="min-height: 650px; font-size: 0.95rem;">

                        <!-- ENCABEZADO -->
                        <div class="d-flex justify-content-between align-items-start mb-4">
                            <div>
                                <h2 class="fw-bold text-uppercase mb-1" style="color:#1f2a37;">
                                    COTIZACION
                                </h2>
                                <div class="text-muted" id="print-folio">FOLIO: #00000</div>
                            </div>

                            <div class="text-end">
                                <div class="fw-bold" style="color:#334155;">cfsistem</div>
                                <div class="text-muted small" id="print-fecha">Fecha: --/--/----</div>
                            </div>
                        </div>

                        <div class="mb-4"
                            style="height:2px; background:linear-gradient(90deg,#1f2a37,#334155); opacity:0.8;">
                        </div>

                        <!-- INFO -->
           <div class="row g-2">
    <div class="col-6">
        <div class="  ">
            <small class="text-uppercase text-muted fw-bold d-block mb-1" style="font-size: 0.7rem; letter-spacing: 0.5px;">
                Vendedor:
            </small>
            <div class="fw-bold text-black" id="vendedor" style="font-size: 0.9rem;">---</div>
        </div>
    </div>
    
    <div class="col-6">
        <div class=" ">
            <small class="text-uppercase text-muted fw-bold d-block mb-1" style="font-size: 0.7rem; letter-spacing: 0.5px;">
                Cliente:
            </small>
            <div class="fw-bold text-black" id="clienteData" style="font-size: 0.9rem;">---</div>
        </div>
    </div>
</div>

                        <!-- TABLA -->
                        <div class="table-responsive mb-3">
                            <table class="table align-middle">

                                <thead style="background:#1f2a37; color:#fff;">
                                    <tr>
                                        <th>Producto</th>
                                        <th class="text-center">Cantidad</th>
                                        <th class="text-center">Costo Unitario</th>
                                        <th class="text-center">Costo Total</th>
                                    </tr>
                                </thead>

                                <tbody id="print-tabla-cuerpo"></tbody>

                            </table>
                        </div>

                        <!-- TOTAL -->
                        <div class="text-end mb-4">
                            <div class="fw-bold fs-5">
                                Total: <span id="costo_total"></span>
                            </div>
                        </div>

                        <!-- FIRMAS -->
                        <div class="mt-5 pt-3">
                            <div class="row text-center">

                                <div class="col-6">
                                    <div class="border-top pt-2">
                                        <small class="text-muted text-uppercase">Solicita</small>
                                    </div>
                                </div>

                                <div class="col-6">
                                    <div class="border-top pt-2">
                                        <small class="text-muted text-uppercase">Autoriza</small>
                                    </div>
                                </div>
                                <div class="modal-footer bg-light border-0" id="footer"></div>


                            </div>
                        </div>

                    </div>
                </div>

                <!-- FOOTER -->


            </div>
        </div>
    </div>
    <div class="modal fade" id="modalPago" tabindex="-1" aria-hidden="true" style="z-index: 1060;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">

            <div class="modal-header text-white" style="background: linear-gradient(135deg, #1f2a37, #334155);">
                <h6 class="modal-title fw-semibold">
                    <i class="bi bi-wallet2 me-2"></i> Registrar pago
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            
            <input type="hidden" id="idC" name="idC">
            
            <div class="modal-body p-4" >
                <div class="table-responsive mb-3">
                    <table class="table align-middle">
                        <thead style="background:#1f2a37; color:#fff;">
                            <tr>
                                
                             
                                
                            </tr>
                        </thead>
                        <tbody id="print-productos"></tbody>
                    </table>
                </div>

                <div class="text-center mb-4">
                    <small class="text-muted d-block">Total a pagar</small>
                    <h3 id="pagoTotal" class="fw-bold text-dark m-0">$0.00</h3>
                </div>

                <div class="mb-3">
                    <label class="form-label small text-muted">Monto recibido</label>
                    <input type="number" id="montoPago"
                        class="form-control form-control-lg border-0 bg-light rounded-3" placeholder="0.00">
                </div>

                <div class="mb-3">
                    <label class="form-label small text-muted">Método de pago</label>
                    <select id="metodoPago" class="form-select form-select-lg border-0 bg-light rounded-3">
                        <option value="">Seleccione</option>
                        <option value="efectivo">Efectivo</option>
                        <option value="transferencia">Transferencia</option>
                        <option value="tarjeta">Tarjeta</option>
                        <option value="deposito">Depósito</option>
                        <option data-metodo="credito" value="">Compra a credito</option>
                    </select>
                </div>

                <div class="mb-3 d-none" id="refBox">
                    <label class="form-label small text-muted">Referencia</label>
                    <input type="text" id="referenciaPago"
                        class="form-control form-control-lg border-0 bg-light rounded-3"
                        placeholder="Número de referencia">
                </div>
                 
            </div>

            <div class="modal-footer border-0 px-4 pb-4 pt-0" id="boton">
                </div>

        </div>
    </div>
</div> y
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




 <?php require_once __DIR__ . '/cotizacionesModales/editarCotizacion.php'; ?>
    <?php require_once __DIR__ . '/cotizacionesModales/ModalCotizacion.php'; ?>
    <?php require_once __DIR__ . '/cotizacionesModales/nuevoClienteModal.php'; ?>
    <?php require_once __DIR__ . '/egresosComponets/agregarPoductoModal.php'; ?>
    <?php require_once __DIR__ . '/egresosComponets/modalProveedoresCompra.php'; ?>
    <script>
    let totalGlobalPago = 0;
    let datost=0;
            
$(document).ready(function () {
    cargarCotizaciones();
    cargarCotizaciones();
});

 cargarUsuariosSelect();
    async function cargarUsuariosSelect() {
    const select = document.getElementById('select-usuarios');
    if (!select) return; // Seguridad por si el select no está en la vista actual

    try {
        // 1. Realizar la petición a tu controlador de Cf System
        const url = '/cfsistem/app/controllers/accesoController.php?action=obtenerUsuarios';
        const respuesta = await fetch(url);
        
        if (!respuesta.ok) throw new Error('Error en la respuesta del servidor');
        
        const resultado = await respuesta.json();

        // 2. Verificar que la respuesta sea exitosa y contenga los datos
        if (resultado.success && Array.isArray(resultado.data)) {
            
            // Limpiamos el select y dejamos una opción inicial neutra
           // select.innerHTML = '<option value="" selected disabled> Seleccione vendedor</option>';

            // 3. Recorrer los usuarios y crear las opciones
            resultado.data.forEach(usuario => {
                const opcion = document.createElement('option');
                opcion.value = usuario.id; // El ID que se enviará en el formulario
                
                // Formateamos el texto: "Nombre (Almacén - Rol)" para que sea súper descriptivo
                const almacen = usuario.almacen_nombre || 'Sin Almacén';
                opcion.textContent = `${usuario.nombre}`;
                
                // Agregamos la opción al select
                select.appendChild(opcion);
            });

        } else {
            select.innerHTML = '<option value="">No se pudieron cargar los usuarios</option>';
            console.error('El backend no devolvió success:true o la estructura cambió');
        }

    } catch (error) {
        select.innerHTML = '<option value="">Error al cargar la lista</option>';
        console.error('Error al ejecutar cargarUsuariosSelect:', error);
    }
}

 
    async function cargarUsuariosSelectPago() {
    const select = document.getElementById('select-usuarios2');
    if (!select) return; // Seguridad por si el select no está en la vista actual

    try {
        // 1. Realizar la petición a tu controlador de Cf System
        const url = '/cfsistem/app/controllers/accesoController.php?action=obtenerUsuarios';
        const respuesta = await fetch(url);
        
        if (!respuesta.ok) throw new Error('Error en la respuesta del servidor');
        
        const resultado = await respuesta.json();

        // 2. Verificar que la respuesta sea exitosa y contenga los datos
        if (resultado.success && Array.isArray(resultado.data)) {
            
            // Limpiamos el select y dejamos una opción inicial neutra
           // select.innerHTML = '<option value="" selected disabled> Seleccione vendedor</option>';

            // 3. Recorrer los usuarios y crear las opciones
            resultado.data.forEach(usuario => {
                const opcion = document.createElement('option');
                opcion.value = usuario.id; // El ID que se enviará en el formulario
                
                // Formateamos el texto: "Nombre (Almacén - Rol)" para que sea súper descriptivo
                const almacen = usuario.almacen_nombre || 'Sin Almacén';
                opcion.textContent = `${usuario.nombre}`;
                
                // Agregamos la opción al select
                select.appendChild(opcion);
            });

        } else {
            select.innerHTML = '<option value="">No se pudieron cargar los usuarios</option>';
            console.error('El backend no devolvió success:true o la estructura cambió');
        }

    } catch (error) {
        select.innerHTML = '<option value="">Error al cargar la lista</option>';
        console.error('Error al ejecutar cargarUsuariosSelect:', error);
    }
}
async function cargarCotizaciones() {
   let almacen= $('#filtroAlmacen').val();
   let fechaInicio= $('#fechaInicio').val();
    let fechaFin=$('#fechaFin').val();
console.log(almacen);

   const params = new URLSearchParams({
    action: 'listarCotizaciones',
    almacen: $('#filtroAlmacen').val(),
    fechaInicio: $('#fechaInicio').val(),
    fechaFin:$('#fechaFin').val(),
    estado:$('#filtroEstado').val(),
    buscador:$('#buscadorGeneral').val(),
    vendedor:$('#select-usuarios').val()
});

    let rol = <?= isset($_SESSION['rol_id']) ? (int)$_SESSION['rol_id'] : 0 ?>;
let tablahtml = '';
const res = await fetch(
    `/cfsistem/app/controllers/cotizacionesController.php?${params.toString()}`
);

let data=await res.json();
    


    data.data.forEach(s => {

        const id = String(s.id).padStart(5, '0');

        const fecha = new Date(s.fecha);
        const fechaFormateada =
            fecha.toLocaleDateString('es-MX') + ' ' +
            fecha.toLocaleTimeString('es-MX', {
                hour: '2-digit',
                minute: '2-digit'
            });

        const status = (s.estado || 'PENDIENTE').toUpperCase();

        let clase = 'bg-secondary text-white';

        switch (status) {
            case 'PENDIENTE':
                clase = 'bg-warning text-dark';
                break;
            case 'COMPLETADO':
                clase = 'bg-primary text-white';
                break;
            case 'CANCELADO':
                clase = 'bg-danger text-white';
                break;
        }

        tablahtml += `
            <tr>

                <td>
                    <span class="text-dark fw-bold">#${id}</span>
                </td>

                <td class="text-muted small">
                    ${fechaFormateada}
                </td>

                <td class="fw-medium">
                    ${s.cliente_nombre ?? 'Sin asignar'}
                </td>

                <td>
                    <span class="badge bg-light text-dark border">
                        ${s.almacen_nombre}
                    </span>
                </td>
 <td>
                    <span class="badge bg-light text-dark border">
                        ${s.vendedor}
                    </span>
                </td>

                <td>
                    <span class="badge badge-status ${clase} rounded-pill">
                        ${status}
                    </span>
                </td>

                <td class="text-end">
        `;

        // Pendiente
        if (status === 'PENDIENTE' && rol < 3) {

            tablahtml += `
                <button class="btn btn-sm btn-white border shadow-sm"
                    onclick="gestionarSolicitud(${s.id})">
                    <i class="bi bi-eye text-primary"></i> Gestionar
                </button>

                <button class="btn btn-sm btn-white border shadow-sm"
                    onclick="eliminarSolicitud(${s.id})">
                    <i class="bi bi-trash text-danger"></i>
                </button>
            `;
        }

        // Completado
        if (status === 'COMPLETADO' && rol < 3) {

            tablahtml += `
                <button class="btn btn-sm btn-white border shadow-sm"
                    onclick="gestionarSolicitud(${s.id})">
                    <i class="bi bi-eye text-primary"></i> REUTILIZAR
                </button>
            `;
        }

        // Imprimir
        tablahtml += `
                    <button class="btn btn-sm btn-white border shadow-sm rounded-pill px-3"
                        onclick="prepararImpresion(${s.id})"
                        title="Imprimir solicitud">

                        <i class="bi bi-printer text-primary me-1"></i>
                        <span class="text-dark fw-medium">Imprimir</span>

                    </button>

                </td>

            </tr>
        `;

    });

   $('#tablaCotizacionesListar').html(tablahtml);
}         
      

    
   async function procederPago(total, id) {
    cargarUsuariosSelectPago();
    let nuevo=[];

    totalGlobalPago = total;

    const resp = await fetch(`${URL_CONTROLADOR}?action=obtenerDetalle&id=${id}`);
    const datos = await resp.json();

    let data = datos.data;

    let html = '';

    data.forEach((i, index) => {

        const cantidad = parseFloat(i.cantidad) || 0;
         canti=cantidad/i.equivalencia;
         data[index].cantidadR = parseFloat(canti);
         data[index].entrega_hoy =0;
         document.getElementById('montoPago').value= data[index].total;
         datost=data;
        
         

        html += `
        <tr>
          

           

            <td class="text-center">
                <input 
                    type="hidden"
                    step="0.01"
                    value="0"
                    max="${i.entregar_hoy}"
                    class="form-control entrega-hoy"
                    data-index="${index}"
                >
            </td>
        </tr>`;
    });

    $('#print-productos').html(html);

    // actualizar entrega_hoy
document.querySelectorAll('.entrega-hoy').forEach(input => {

    input.addEventListener('input', function () {

        const index = this.dataset.index;
        


        data[index].entrega_hoy = parseFloat((this.value*(1/data[index].equivalencia))) || 0;
        data[index].monto_pagado = parseFloat(
            document.getElementById('montoPago').value
        ) || 0;
        
        datost=data;
console.log("datos",datost);
        console.log(data);
    });

});

// actualizar monto pagado en TODO el arreglo
document.getElementById('montoPago').addEventListener('input', function () {

    const monto = parseFloat(this.value) || 0;

    data.forEach(item => {
        item.monto_pagado = monto;
    });
    datos=data;

    console.log(datost);
});
       

  

const htmlboton=`<button class="btn btn-light w-50 rounded-3" data-bs-dismiss="modal">
                        Cancelar
                    </button>

                    <button class="btn w-50 text-white rounded-3" style="background: #334155;"
                      onclick='convertirToCompra(${JSON.stringify(datost)}, ${id})'">
                        Confirmar
                    </button>;`
    document.getElementById('pagoTotal').textContent =
        total.toLocaleString('es-MX', {
            style: 'currency',
            currency: 'MXN'
        });

    document.getElementById('montoPago').value = total;
    document.getElementById('idC').value = id;

    // opcional: guardarlo para usarlo al enviar pago
    window.detallePago = data;

    const modal = new bootstrap.Modal(
        document.getElementById('modalPago')
    );
$('#boton').html(htmlboton);
    modal.show();
}
    
   document.getElementById('metodoPago').addEventListener('change', function() {
    // 1. Validamos si el método seleccionado requiere caja de referencia
    const requiere = ['transferencia', 'tarjeta', 'deposito'].includes(this.value);
    
    // 2. Corregido: Condicional IF correcto usando el valor de 'this.value'
    if ($(this).find(':selected').data('metodo')=== 'credito') {
        $('#montoPago').val(0);
        $('#montoPago').prop('disabled', true);
    } else {
        // Buena práctica: Si cambian de opinión y eligen otro método, 
        // volvemos a habilitar el campo de monto.
        $('#montoPago').prop('disabled', false);
    }

    // 3. Mostrar u ocultar la caja de referencia (utiliza vanilla JS como tu listener)
    document.getElementById('refBox').classList.toggle('d-none', !requiere);
});
  async function gestionarSolicitud(id) {
    try {
        // 1. Limpiar la tabla de edición por si tenía datos anteriores
        $('#tablaDetalleEditar tbody').empty();
        $('#formEditarSolicitud')[0].reset();

        console.log('Cargando cotización ID:', id);

        // 2. Consultar el detalle al controlador
        const resp = await fetch(`${URL_CONTROLADOR}?action=obtenerDetalle&id=${id}`);
        const datos = await resp.json();
        const data = datos.data;

        console.log('DATOS RECUPERADOS:', data);

        if (!Array.isArray(data) || data.length === 0) {
            Swal.fire('Error', 'No se encontraron productos en esta cotización', 'warning');
            return;
        }

        // 3. Setear datos de cabecera usando el primer elemento del array
        const infoBase = data[0];
        
        $('#editar_cotizacion_id').val(infoBase.cotizacion_id);
        $('#almacen_id_editar').val(infoBase.almacen_origen_id);
        $('#cliente_id_editar').val(infoBase.cliente_id).trigger('change.select2');
        cargarVendedores3(infoBase.vendedor_id);

        // Ocultar el estado vacío porque vamos a meter filas
        $('#emptyStateEditar').addClass('d-none');

        // 4. Recorrer los productos e inyectarlos como filas interactivas
        data.forEach(i => {
            const prodId = i.producto_id;

            // Reconstruimos las opciones de unidad/medida basándonos en la actual de la BD
            // Nota: Si manejas más medidas adicionales dinámicas, aquí puedes añadir la lógica de tu data-medidas.
            let opcionesUnidadHtml = `
                <option 
                    value="${i.unidadMedida}" 
                    data-equivalencia="${i.equivalencia}" 
                    data-medida-id="${i.unidadMedida}" 
                    selected>
                    ${i.nombre}
                </option>
            `;

            // Estructura HTML idéntica a la generada por el Select2 de búsqueda
            const nuevaFilaHtml = `
            <tr id="filaEditar-${prodId}">
                <td class="ps-4">
                    <b>${i.producto_nombre}</b><br>
                    <small class="text-muted">${i.sku}</small>
                </td>

                <td>
                    <input 
                        type="number"
                        name="itemsEditar[${prodId}][cant]"
                        class="form-control cantidad-editar"
                        step="0.01"
                        value="${parseFloat(i.cantidad)}"
                        min="0.01"
                        required
                        oninput="calcularTotalSolEditar(this)">
                </td>

                <td>
                    <select 
                        name="itemsEditar[${prodId}][unidad]" 
                        class="form-select unidad-select-editar"
                        onchange="calcularPrecioSugeridoEditar(this)">
                        ${opcionesUnidadHtml}
                    </select>
                </td>
                
                <td>
                    <select 
                        name="itemsEditar[${prodId}][tipoPrecio]" 
                        class="form-select tipoPrecio-select-editar"
                        id="tipoPrecio_editar_${prodId}"
                        onchange="calcularPrecioSugeridoEditar(this)">
                        <option value="seleccionar" data-precio="0">seleccione</option>
                        
                        <option value="minorista" data-precio="${i.precio_unitario}">
                            Min ${parseFloat(i.precio_unitario) * parseFloat(i.factor_conversion || 1)} x ${i.unidad_reporte}
                        </option>
                    </select>
                </td>

                <td>
                    <input 
                        type="number"
                        lang="en-US"
                        name="itemsEditar[${prodId}][precioUnitario]"
                        class="form-control precio-unitario-editar"
                        step="0.01"
                        min="0"
                        value="${parseFloat(i.precio_unitario).toFixed(2)}"
                        required
                        oninput="calcularTotalSolEditar(this)"
                    >
                </td>

                <td style="min-width:160px;">
                    <input 
                        type="number"
                        lang="en-US"
                        name="itemsEditar[${prodId}][precio]"
                        class="form-control precio-total-editar fw-bold text-success bg-light"
                        step="0.01"
                        min="0"
                        value="${parseFloat(i.subtotal).toFixed(2)}"
                        readonly
                        style="font-size:1.1rem; height:45px; min-width:140px;"
                    >
                </td>

                <td>
                    <button type="button" class="btn btn-link text-danger" onclick="quitarFilaEditar(${prodId})">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            </tr>
            `;

            // Insertar la fila en el tbody de edición
            $('#tablaDetalleEditar tbody').append(nuevaFilaHtml);

            // Pre-seleccionar el tipo de precio guardado en la Base de Datos ('minorista', 'mayorista', etc.)
            $(`#tipoPrecio_editar_${prodId}`).val(i.tipo_precio);
        });

        // 5. Forzar el recálculo general del costo total de compra basándonos en las nuevas filas
        let totalCompraEditar = 0;
        document.querySelectorAll('#tablaDetalleEditar .precio-total-editar').forEach(el => {
            totalCompraEditar += parseFloat(el.value) || 0;
        });

        document.getElementById('costoTotalCompraEditar').textContent = totalCompraEditar.toLocaleString('es-MX', {
            style: 'currency',
            currency: 'MXN'
        });
        document.getElementById('totalCotizacionEditar').value = totalCompraEditar;

        // 6. Cargar buscador interno del modal y finalmente abrir el modal de edición independiente
        await recargarProductosEditar();

        new bootstrap.Modal(
            document.getElementById('modalEditarCotizacion')
        ).show();

    } catch (e) {
        console.error('Error al gestionar/mapear la solicitud:', e);
        Swal.fire('Error', 'No se pudo estructurar el editor de la cotización', 'error');
    }
}  async function prepararImpresion(id) {
        try {
            
            $('#tablaConversion tbody').empty();

            console.log(id);

            const resp = await fetch(`${URL_CONTROLADOR}?action=obtenerDetalle&id=${id}`);

            const datos = await resp.json(); // 👈 AQUÍ
            const data = datos.data;

            console.log('DATA REAL:', data);


            if (!Array.isArray(data) || data.length === 0) {
                console.error('Sin datos');
                return;
            }

            const infoBase = data[0];

            $('#print-folio').text(`FOLIO: #${id.toString().padStart(5, '0')}`);
            $('#print-fecha').text(`Fecha: ${new Date().toLocaleDateString()}`);
            $('#print-almacen').text(infoBase.almacen_nombre);
            $('#print-proveedor').text(infoBase.cliente_nombre || 'No especificado');
let cliente=`<div class="card border-0 bg-light rounded-3 p-3 mb-3">
    <div class="d-flex flex-column gap-2">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-person-fill text-secondary fs-5"></i>
            <div>
                <small class="text-muted d-block" style="font-size: 0.75rem;">Cliente</small>
                <span id="print-cliente-nombre" class="fw-bold text-dark">${infoBase.cliente_nombre}</span>
            </div>
        </div>
        
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-geo-alt-fill text-secondary fs-5"></i>
            <div>
                <small class="text-muted d-block" style="font-size: 0.75rem;">Dirección</small>
                <span id="print-cliente-direccion" class="text-secondary small">${infoBase.direccion}</span>
            </div>
        </div>

        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-telephone-fill text-secondary fs-5"></i>
            <div>
                <small class="text-muted d-block" style="font-size: 0.75rem;">Teléfono</small>
                <span id="print-cliente-telefono" class="text-secondary small">${infoBase.telefono}</span>
            </div>
        </div>
    </div>
</div>`;
$('#clienteData').html(cliente);

let vendedor=`<div class="card border-0 bg-light rounded-3 p-3 mb-3">
    <div class="d-flex flex-column gap-2">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-person-fill text-secondary fs-5"></i>
            <div>
                <small class="text-muted d-block" style="font-size: 0.75rem;">Vendedor</small>
                <span id="print-cliente-nombre" class="fw-bold text-dark">${infoBase.nombre}</span>
            </div>
        </div>
        
        
    </div>
</div>`;
$('#vendedor').html(vendedor);

            let totalGeneral = 0;
            let html = '';

            data.forEach(i => {

                const cantidad = parseFloat(i.cantidad) || 0;
                const precioUnitario = parseFloat(i.precio_unitario) || 0;
                const subtotal = parseFloat(i.subtotal) || 0;

                totalGeneral += subtotal;

                html += `
                <tr>
                    <td class="fw-bold">${i.producto_nombre}(${i.sku})</td>

                    <td class="text-center">
                        ${cantidad} ${i.nombre || ''}
                    </td>

                    <td class="text-center">
                        ${precioUnitario.toLocaleString('es-MX', { style: 'currency', currency: 'MXN' })}
                    </td>

                    <td class="text-center">
                        ${subtotal.toLocaleString('es-MX', { style: 'currency', currency: 'MXN' })}
                    </td>
                </tr>`;
            });

            $('#costo_total').text(
                totalGeneral.toLocaleString('es-MX', {
                    style: 'currency',
                    currency: 'MXN'
                })
            );

            $('#print-tabla-cuerpo').html(html);

            ejecutarImpresion();

        } catch (e) {
            console.error(e);
        }
    }

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
async function convertirToCompra(data, id) {
    try {
        console.log('data 1',datost);

        const payload = {
            accion: 'guardar_venta',
            data: datost,
            monto_pagado: parseFloat($('#montoPago').val()) || 0,
            metodo_pago: $('#metodoPago').val() || 'Efectivo',
            referencia: $('#referenciaPago').val() || '',
            vendedor:$('#select-vendedor1').val() || 1,
            idCotizacion:id,
            descuento: 0,
            observaciones: ''
        };

        const resp = await fetch(`${URL_CONTROLADOR}?action=guardar_venta&id=${id}`, {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json'
    },
    body: JSON.stringify(payload)
});

const datos = await resp.json();
console.log(datos);

if (datos.status === 'success') {

    Swal.fire({
        icon: 'success',
        title: 'Venta guardada',
        text: `Folio: ${datos.folio || ''}`,
        confirmButtonText: 'OK'
    }).then(() => location.reload());

} else {

    Swal.fire({
        icon: 'error',
        title: 'Error al guardar',
        text: datos.message || 'Ocurrió un error inesperado',
        confirmButtonText: 'Cerrar'
    });

}

    } catch (e) {
        console.error(e);
    }
}
 $(document).ready(function() {
       
       
$('#buscadorGeneral').on('keyup', cargarCotizaciones);
$('#filtroAlmacen').on('change', cargarCotizaciones);
$('#filtroEstado').on('change', cargarCotizaciones);
$('#fechaInicio').on('change', cargarCotizaciones);
$('#fechaFin').on('change', cargarCotizaciones);
$('#select-usuarios').on('change', cargarCotizaciones);
        

     });
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
    <!-- <script>
    const URL_CONTROLADOR_SOLICITUD = '/cfsistem/app/controllers/solicitudesCompraController.php';

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
            dropdownParent: $('#modalCotizacion')
        });
function calcularTotal(input) {

    const fila = input.closest('tr');

    const cantidad = parseFloat(
        fila.querySelector('.cantidad').value
    ) || 0;

    const precioUnitario = parseFloat(
        fila.querySelector('.precio-unitario').value
    ) || 0;

    const factor = parseFloat(
        fila.querySelector('.unidad-select').value
    ) || 1;

    // 🔥 TOTAL
    const total = cantidad * precioUnitario * factor;

    fila.querySelector('.precio-total').value =
        total.toFixed(2);
}

      
      
        // ENVÍO DEL FORMULARIO DE CONVERSIÓN
        $('#formConvertirCompra').on('submit', async function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Procesando ingreso...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });
            try {
                const resp = await fetch(`${URL_CONTROLADOR_SOLICITUD}?action=convertirACompra`, {
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
            const resp = await fetch(`${URL_CONTROLADOR_SOLICITUD}?action=eliminar`, {
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
        $('#tablaConversion tbody').empty();
        console.log(id);

        const resp = await fetch(`${URL_CONTROLADOR_SOLICITUD}?action=obtenerDetalle&id=${id}`);
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
        


// 🔥 DESACTIVAR SI NO HAY DEUDA
if (deuda <= 0) {

    $('#input_pagar_deuda')
        .prop('disabled', true)
        .addClass('bg-light');

} else {

    $('#input_pagar_deuda')
        .prop('disabled', false)
        .removeClass('bg-light');
}
const almacenInput = document.getElementById('almacen_id2');

const almacen_id2 = almacenInput ? almacenInput.value : 0;

console.log('ALMACEN:', almacen_id2);
        let html = '';

        items.forEach((i, index) => {
            
             
            const factor = parseFloat(i.factor_conversion) || 1;
            const uBase = i.unidad_medida || 'pzas';
            const uRep = i.unidad_reporte || 'Mayoreo';
            const costo =i.costo;
            const cantidadSolicitada = parseFloat(i.cantidad) || 0;
const cantidad = parseFloat(i.cantidad);

const cantMayoreo = Math.floor(cantidad); // 1
 const cantSueltas = ((cantidad - cantMayoreo)*factor);    // 0.5
            
          
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
                        value="${cantMayoreo}" step=".01" oninput="recalcularFila(${index})" readonly>
                </td>

                <td>
                    <label class="small text-muted text-uppercase fw-bold">${uBase}</label>
                    <input type="number" class="form-control form-control-sm i-sueltas border-primary" 
                        value="${cantSueltas}" step="0.01" oninput="recalcularFila(${index})" readonly>
                </td>

                <td>
                    <label class="form-label small text-danger fw-semibold mb-1">Faltantes</label>
                  <input type="number"
    id="faltante_${index}"
    class="form-control form-control-sm border-danger shadow-sm i-faltante"
    name="items[${index}][cantidad_faltante]"
    value="0"
    step=".01"
    min="0"
    oninput="recalcularFila(${index})">

                   
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

               
    
    <input 
        type="hidden"
        value="${almacen_id2}"
        class="form-control i-almacen-id"
    >
</td>       
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

    let faltante = parseFloat(fila.find('.i-faltante').val()*factor) || 0;
    const excedente = parseFloat(fila.find('.i-excedente').val()) *factor|| 0;

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

    // 🔥 RECORRER TODAS LAS FILAS
    $('.fila-item').each(function() {

        const fila = $(this);
        const index = fila.data('index');

        const almId = $('#almacen_id2').val();
        const cantTotal = parseFloat(
            fila.find('.h-total-piezas').val()
        ) || 0;

        const costoTotal = parseFloat(
            fila.find('.i-costo-total').val()
        ) || 0;

        const excedente = parseFloat(
            fila.find('.i-excedente').val()
        ) || 0;

        const faltante = parseFloat(
            fila.find('.i-faltante').val()
        ) || 0;

        const productoId = fila.find(
            `input[name="items[${index}][producto_id]"]`
        ).val();

        // 🔥 VALIDAR PRODUCTO
        if (!productoId) {
            console.warn('Producto inválido en fila:', index);
            return;
        }
 const factor = parseFloat(fila.find('.h-factor').val()) || 1;
        detalle.push({

            producto_id: productoId,

            input_mayoreo: parseFloat(
                fila.find('.i-mayoreo').val()
            ) || 0,

            input_sueltas: parseFloat(
                fila.find('.i-sueltas').val()
            ) || 0,

            cantidad_excedente: (excedente*factor),

            cantidad_faltante: (faltante*factor),

            total_item: costoTotal,

            precio_lote: parseFloat(
                fila.find('.h-precio-lote').val()
            ) || 0,

            hidden_factor: parseFloat(
                fila.find('.h-factor').val()
            ) || 1,

            almacenes: {
                [almId]: {
                    activo: 'on',
                    cantidad: cantTotal
                }
            }

        });

    });

    console.log('DETALLE A ENVIAR:', detalle);

    // 🔥 VALIDACIÓN GENERAL
    if (detalle.length === 0) {

        Swal.fire(
            'Atención',
            'No hay productos para guardar.',
            'warning'
        );

        return;
    }

    // 🔥 VALIDAR COSTOS
    const costoInvalido = detalle.some(item =>
        parseFloat(item.total_item) <= 0
    );

    if (costoInvalido) {

        Swal.fire(
            'Atención',
            'Todos los productos deben tener un costo mayor a 0.',
            'warning'
        );

        return;
    }

    // 🔥 FORM DATA
    const formData = new FormData(this);

    formData.append(
        'action',
        'guardarCompraCompleta'
    );

    // 🔥 ENVIAR JSON COMPLETO
    formData.append(
        'items',
        JSON.stringify(detalle)
    );

    formData.append(
        'solicitud_id',
        $('#uni-solicitud-id').val()
    );

    formData.append(
    'almacen_id',
    $('#almacen_id2').val()
);

    formData.append(
        'proveedor',
        $('#uni-proveedor').val()
    );

    Swal.fire({

        title: '¿Confirmar Ingreso?',

        text: 'Se registrará la entrada en inventario y se cerrará la solicitud.',

        icon: 'question',

        showCancelButton: true,

        confirmButtonColor: '#198754',

        confirmButtonText: 'Sí, guardar',

        cancelButtonText: 'Cancelar'

    }).then((result) => {

        if (!result.isConfirmed) return;

        Swal.fire({

            title: 'Procesando...',

            html: 'Guardando datos y generando lotes',

            allowOutsideClick: false,

            didOpen: () => {
                Swal.showLoading();
            }

        });

        $.ajax({

            url: URL_CONTROLADOR_SOLICITUD,

            type: 'POST',

            data: formData,

            processData: false,

            contentType: false,

            dataType: 'json',

            success: function(res) {

                console.log('RESPUESTA:', res);

                if (res.success) {

                    Swal.fire(
                        '¡Éxito!',
                        res.message,
                        'success'
                    ).then(() => {
                        location.reload();
                    });

                } else {

                    Swal.fire(
                        'Error de negocio',
                        res.message || 'Error desconocido',
                        'error'
                    );

                }
            },

            error: function(jqXHR, textStatus, errorThrown) {

                console.error('AJAX ERROR:', textStatus);
                console.error('ERROR THROWN:', errorThrown);
                console.error('RESPUESTA:', jqXHR.responseText);

                Swal.fire({

                    icon: 'error',

                    title: 'Error del Servidor',

                    html: `
                    <div style="
                        text-align:left;
                        font-size:11px;
                        background:#eee;
                        padding:10px;
                        max-height:250px;
                        overflow:auto;
                        border-radius:6px;
                    ">
                        ${jqXHR.responseText || 'Error desconocido (posible 500)'}
                    </div>`,

                    footer: 'Revisa la pestaña Network en F12 para más detalles.'

                });

            }

        });

    });

});
  }); </script>
    <script>
    /**
     * Llena el modal de impresión con la data de la solicitud
     */
    


    </script> -->
</body>

</html>