<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Entregas | Sistema</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <?php require_once __DIR__ . '/layout/icono.php' ?>
    <?php if (function_exists('cargarEstilos')) { cargarEstilos(); } ?>


    <style>
    :root {
        --sidebar-width: 250px;
        --primary-dark: #2c3e50;
        --accent-color: #34495e;
        --bg-body: #f8f9fa;
    }

    body {
        background-color: var(--bg-body);
        overflow-x: hidden;
        padding-top: 20px;
        text-transform: uppercase !important;
    }

    .main-content {
        margin-left: var(--sidebar-width);
        padding: 2rem;
        min-height: 100vh;
        transition: all 0.3s;
    }

    .scroll-table {
        background: white;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        overflow: hidden;
    }

    .table thead th {
        background-color: var(--primary-dark);
        color: white;
        font-weight: 500;
        text-transform: uppercase;
        font-size: 0.75rem;
        padding: 12px;
        border: none;
    }

    .btn-action {
        background-color: var(--accent-color);
        color: white;
        border: none;
    }

    .btn-action:hover {
        background-color: var(--primary-dark);
        color: white;
    }

    .filter-card {
        border: none;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        border-radius: 10px;
    }

    .modal-header {
        background-color: var(--primary-dark);
        color: white;
        border: none;
    }

    .input-entrega {
        border: 2px solid #28a745 !important;
        max-width: 90px;
        text-align: center;
        font-weight: bold;
    }

    @media (max-width: 992px) {
        .main-content {
            margin-left: 0;
            padding: 1rem;
        }
    }

    /* Esto asegura que SweetAlert siempre esté por encima del modal de Bootstrap */
    .swal2-container {
        z-index: 9999 !important;
    }
    </style>
</head>

<body>
    <?php if (function_exists('renderizarLayout')) {
        renderizarLayout($paginaActual); 
    } ?>
    <div class="main-content">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="fw-bold text-dark m-0">Historial de Ventas</h3> 
                <div id="loader" class="spinner-border spinner-border-sm text-secondary d-none"></div>
            </div>
  <div class="dropdown">
        <button 
            class="btn btn-add dropdown-toggle"
            type="button"
            data-bs-toggle="dropdown"
            aria-expanded="false"
            style="border-radius: 10px; background: #123e77; color: #ffffff;">
            
            <i class="bi bi-gear me-2"></i> Mis repartos
        </button>

        <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3">

            

            <li>
                <a class="dropdown-item d-flex align-items-center gap-2"
                   href="/cfsistem/app/controllers/misRepartosController.php">
                    <i class="bi bi-list-ul text-primary"></i>
                    Gestionar mis repartos
                </a>
            </li>

        </ul>
    </div>
            <div class="card filter-card mb-4">
                <div class="card-body">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">Buscador</label>
                            <input type="text" id="f_search" class="form-control form-control-sm"
                                placeholder="Folio o Cliente..." onkeyup="getVentas()">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small fw-bold">Estatus Entrega</label>
                            <select id="f_status" class="form-select form-select-sm" onchange="getVentas()">
                                <option value="">Todos</option>
                                <option value="pendiente">Pendiente</option>
                                <option value="parcial">Parcial</option>
                                <option value="entregado">Entregado</option>
                            </select>
                        </div>
                          <div class="col-md-2">
    <label for="select-usuarios" class="form-label fw-bold small text-muted text-uppercase">Vendedor</label>
    <select class="form-select rounded-pill" id="select-usuarios" name="usuario_id" onchange="getVentas()">
       <option value="" > Seleccione vendedor</option>
    </select>
</div>
                        <div class="col-md-2">
                            <label class="form-label small fw-bold">Estatus Pago</label>
                            <select id="f_pago" class="form-select form-select-sm" onchange="getVentas()">
                                <option value="">Todos</option>
                                <option value="deuda">Con Deuda</option>
                                <option value="pagado">Pagados</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small fw-bold">Periodo</label>
                            <select id="f_rango" class="form-select form-select-sm" onchange="togglePerso()">
  <option value="semana">Semana</option>
                                <option value="hoy">Hoy</option>
                                <option value="ayer">Ayer</option>
                                <option value="semana">Semana</option>
                                <option value="mes">Mes</option>
                                <option value="todos">Historial Completo</option>
                                <option value="personalizado">Rango...</option>
                            </select>
                        </div>
                        <div class="col-md-3 d-none" id="div_p">
                            <label class="form-label small fw-bold">Fechas</label>
                            <div class="input-group input-group-sm">
                                <input type="date" id="f_ini" class="form-control" onchange="getVentas()">
                                <input type="date" id="f_fin" class="form-control" onchange="getVentas()">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small fw-bold">Ubicación</label>
                            <select id="f_almacen" class="form-select form-select-sm" onchange="getVentas()">
                             <option value="">Todas</option>
                            <?php foreach($almacenes as $a): ?>
                                    <option value="<?= $a['id'] ?>"
                                        <?= ($a['id'] == $_SESSION['almacen_id']) ? 'selected' : '' ?>>
                                        <?= $a['nombre'] ?>
                                    </option>
                                    <?php endforeach; ?>
                            </select>
                        </div>
                         <div class="col-md-2">
                            <label class="form-label small fw-bold">Estatus Factura</label>
                            <select id="estado_factura" class="form-select form-select-sm" onchange="getVentas()">
                                <option value="">Todos</option>
                                <option value="1">Facturada</option>
                                <option value="0">No factuarada</option>
                               
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="scroll-table shadow-sm">
                <div class="table-responsive" style="max-height: 60vh;">
                    <table class="table table-hover align-middle mb-0" id="tablaVentas">
                        <thead>
                            <tr>
                                
                                <th class="ps-3">Fecha</th>
                                <th>Folio</th>
                                <th>Almacén</th>
                                <th>Vendedor</th>
                                <th>Cliente</th>
                                <th>Total</th>
                                <th>Saldo Cobro</th>
                                <th>Facturada</th>
                                <th class="text-center">Estado Entrega</th>
                                <th class="text-end pe-3">Acciones</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
           
    </div>

    <div class="modal fade" id="modalDetalle" tabindex="-1">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content border-0">
                <div class="modal-header">
                    <h6 class="modal-title fw-bold">Gestión de Venta: <span id="spanFolio"></span></h6>
                     <span id="IdFolio"style="visibility: hidden;"></span>
                      <span id="Almacen_id" style="visibility: hidden;"></span>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-0">
                    <div class="row g-0">
                        <div class="col-md-3 bg-light border-end p-4">
                            <p class="fw-bold small mb-1">Cliente:</p>
                            <p id="detCliente" class="fw-bold small mb-1"></p>
                            <p class="fw-bold small mb-1">Almacen:</p>
                            <p id="detAlmacen" class="fw-bold small mb-3"></p>
                            <p class="fw-bold small mb-1">Vendedor:</p>
                            <p id="detVendedor" class="fw-bold small mb-3"></p>
                            <p class="fw-bold small mb-1">Folio Factura:</p>
                            <p id="folioFactura" class="fw-bold small mb-3"></p>

                          

                            <div class="mb-4 p-2 bg-white border rounded shadow-sm text-center">
                                <div class="mb-2 pb-2 border-bottom">
                                    <span class="d-block small text-muted text-uppercase fw-bold">Total de Venta</span>
                                    <span id="detTotalLabel" class="h6 fw-bold text-dark">$0.00</span>
                                </div>

                                <div>
                                    <span class="d-block small text-muted text-uppercase fw-bold">Saldo Pendiente</span>
                                    <span id="detSaldoLabel" class="h5 fw-bold text-danger">$0.00</span>
                                </div>
                            </div>
                           
<?php if($_SESSION['rol_id']==1||$_SESSION['rol_id']==2): ?>
                             <div id="contenedorBoton">
                               <button id="btnHabilitar"
        class="btn btn-action w-100 mb-2 py-2 fw-bold"
        onclick="abrirModalDespachoVentaTotal(
            $('#Almacen_id').text(),
            $('#IdFolio').text()
        )">
    Nueva Entrega
</button>
  </div>
<?php endif; ?>
                                <!-- <button id="btnAbonar" class="btn btn-primary w-100 mb-2 py-2 fw-bold"
                                    onclick="abrirFlujoAbono()">
                                    <i class="bi bi-cash"></i> Registrar Abono
                                </button> -->
                          

                            <div class="text-end pe-3">



                            </div>
                            <div id="controlesGuardar" class="d-none">
                                <button class="btn btn-success w-100 mb-2 py-2 fw-bold"
                                    onclick="procesarEntrega()">Guardar Cambios</button>

                                <button class="btn btn-link text-secondary w-100 btn-sm"
                                    onclick="alternarModo(false)">Cancelar</button>
                            </div>
                            
                            <style>
                            .btn-animado-entrega {
                                position: relative;
                                overflow: hidden;
                                color: #fff;
                                font-weight: 600;
                                letter-spacing: .3px;
                                transition: all .25s ease;

                                background: linear-gradient(270deg,
                                        #7c3aed,
                                        #ec4899,
                                        #f97316,
                                        #3b82f6,
                                        #7c3aed);

                                background-size: 600% 600%;
                                animation: moverGradiente 8s ease infinite;

                                box-shadow:
                                    0 4px 18px rgba(124, 58, 237, .35),
                                    0 2px 8px rgba(236, 72, 153, .25);
                            }

                            .btn-animado-entrega:hover {
                                transform: translateY(-2px) scale(1.02);
                                box-shadow:
                                    0 8px 24px rgba(124, 58, 237, .45),
                                    0 4px 14px rgba(236, 72, 153, .35);
                            }

                            .btn-animado-entrega:disabled {
                                opacity: .7;
                                cursor: not-allowed;
                            }

                            .btn-animado-entrega::before {
                                content: '';
                                position: absolute;
                                top: 0;
                                left: -120%;
                                width: 80%;
                                height: 100%;

                                background: linear-gradient(120deg,
                                        transparent,
                                        rgba(255, 255, 255, .35),
                                        transparent);

                                animation: brillo 2.8s linear infinite;
                            }

                            @keyframes moverGradiente {
                                0% {
                                    background-position: 0% 50%;
                                }

                                50% {
                                    background-position: 100% 50%;
                                }

                                100% {
                                    background-position: 0% 50%;
                                }
                            }

                            @keyframes brillo {
                                0% {
                                    left: -120%;
                                }

                                100% {
                                    left: 140%;
                                }
                            }
                            </style>
                        </div>
                        <div class="col-md-9 p-4">
                            <div class="table-responsive border rounded mb-3" style="max-height: 180px;">
                                <table class="table table-sm align-middle mb-0">
                                    <thead class="table-light">
                                        <tr class="small text-uppercase">
                                            <th>Producto</th>
                                            <th class="text-center">Venta</th>
                                            <th class="text-center">Surtido</th>
                                            
                                            <th class="text-center text-danger">Falta</th>
                                            <th class="text-center col-input d-none">Entrega</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbodyDetalle" class="small"></tbody>
                                </table>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <h6 class="small fw-bold text-uppercase text-muted"><i class="bi bi-truck"></i>
                                        Historial de Entregas</h6>
                                    <div class="table-responsive border rounded" style="max-height: 180px;">
                                        <table class="table table-sm align-middle mb-0">
                                            <thead class="table-light">
                                                <tr class="small text-uppercase">
                                                    <th>Fecha</th>
                                                    <th>Responsable</th>
                                                    <th>Producto</th>
                                                    <th class="text-center">Cant</th>
                                                </tr>
                                            </thead>
                                            <tbody id="tbodyHistorial" class="small"></tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="small fw-bold text-uppercase text-muted"><i class="bi bi-cash-stack"></i>
                                        Historial de Pagos</h6>
                                    <div class="table-responsive border rounded" style="max-height: 180px;">
                                        <table class="table table-sm align-middle mb-0">
                                            <thead class="table-light">
                                                <tr class="small text-uppercase">
                                                    <th>Fecha</th>
                                                    <th>Monto</th>
                                                    <th>Método</th>
                                                    <th>Referencia</th>
                                                </tr>
                                            </thead>
                                            <tbody id="tbodyPagos" class="small"></tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="col-md-12 mt-3">
                                    <h6 class="small fw-bold text-uppercase text-muted">
                                        <i class="bi bi-map"></i>
                                        Repartos
                                    </h6>

                                    <div class="table-responsive border rounded" style="max-height: 220px;">
                                        <table class="table table-sm align-middle mb-0">
                                            <thead class="table-light">
                                                <tr class="small text-uppercase">
                                                    <th># Reparto</th>
                                                    <th>Fecha Entrega</th>
                                                    <th>Direccion</th>
                                                    <th class="text-center">Ruta</th>
                                                </tr>
                                            </thead>

                                            <tbody id="tbodyRepartos" class="small">

                                                <!-- ejemplo -->
                                               
                                                

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                              <h4 id="cancelado" class="fw-bold text-danger padding-top-3 mb-3"></h4>
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<div class="modal fade" id="modalImprimirRuta" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">

            <div class="modal-header bg-light align-items-center py-3">
                <h5 class="modal-title d-flex align-items-center gap-2 fw-bold text-dark">
                    <i class="bi bi-receipt text-primary"></i>
                    Ruta de Reparto: <span id="folioRutaPrint" class="text-primary"></span>
                </h5>

                <div class="d-flex gap-2 ms-auto me-2">
                    <button class="btn btn-primary btn-sm px-3 d-flex align-items-center gap-1" onclick="imprimirModalRuta()">
                        <span>🖨</span> Imprimir
                    </button>
                </div>

                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body p-0" id="contenidoRutaPrint">
                <!-- AQUÍ SE RENDERIZA TODO -->
            </div>

        </div>
    </div>
</div>
<div class="modal fade" id="modalAgregarFactura" tabindex="-1" aria-labelledby="modalAgregarFacturaLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm"> <div class="modal-content rounded-3 border-0 shadow">
            
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold text-dark fs-5" id="modalAgregarFacturaLabel">
                    <i class="bi bi-file-earmark-plus text-primary me-2"></i>Nueva Factura
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body py-3">
                <form id="formFactura" onsubmit="event.preventDefault(); ">
                    <div class="mb-2">
                        <input type="hidden" 
                               class="form-control rounded-pill border-secondary border-opacity-25" 
                               id="id_venta_factura" 
                               >
                    
                        <label for="folio-factura" class="form-label fw-bold small text-muted text-uppercase ls-wide">
                            Folio o Número de Factura
                        </label>
                        <input type="text" 
                               class="form-control rounded-pill border-secondary border-opacity-25" 
                               id="folio-factura" 
                               placeholder="Ej. FACT-12345" 
                               required 
                               autocomplete="off">
                    </div>
                </form>
            </div>
            
            <div class="modal-footer border-top-0 pt-0 d-flex gap-2">
                <button type="button" class="btn btn-sm btn-light rounded-pill flex-grow-1 fw-bold text-muted" data-bs-dismiss="modal">
                    Cancelar
                </button>
                <button type="button" class="btn btn-sm btn-primary rounded-pill flex-grow-1 fw-bold" onclick="agregarFactura ($('#id_venta_factura').val(),$('#folio-factura').val())">
                    Guardar
                </button>
            </div>

        </div>
    </div>
</div>
<div class="modal fade" id="modalCancelarVenta" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Cancelar Venta</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <input type="hidden" id="cancelar_id_venta">

                <div class="mb-3">
                    <label class="form-label">Motivo de la cancelación</label>
                    <textarea
                        id="cancelar_motivo"
                        class="form-control text-uppercase"
                        rows="4"
                        placeholder="Escriba el motivo..."
                    ></textarea>
                </div>
            </div>

            <div class="modal-footer">
                <button class="btn btn-success" onclick="procesarCancelacion(true)">
                    Con Saldo a Favor
                </button>

                <button class="btn btn-danger" onclick="procesarCancelacion(false)">
                    Sin Saldo
                </button>

                <button class="btn btn-secondary" data-bs-dismiss="modal">
                    Regresar
                </button>
            </div>
        </div>
    </div>
</div>

    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <?php require_once __DIR__ . '/ventasHistorialModales/registarAbono.php'; ?>
    <?php require_once __DIR__ . '/entregasComponets/modalEntregaVentas.php'; ?>

    <script>
        let modalCancelarVenta;

document.addEventListener('DOMContentLoaded', () => {
    modalCancelarVenta = new bootstrap.Modal(
        document.getElementById('modalCancelarVenta')
    );
});

function abrirModalCancelacion(idVenta, folio) {

    document.getElementById('cancelar_id_venta').value = idVenta;
    document.getElementById('cancelar_motivo').value = '';

    document.querySelector('#modalCancelarVenta .modal-title').innerHTML =
        `Cancelar Venta ${folio}`;

    modalCancelarVenta.show();
}
async function procesarCancelacion(conSaldo) {

    const idVenta = document.getElementById('cancelar_id_venta').value;
    const motivo = document.getElementById('cancelar_motivo').value.trim();

    if (!motivo) {
        Swal.fire({
            icon: 'warning',
            title: 'Motivo requerido',
            text: 'Debe capturar el motivo de la cancelación'
        });
        return;
    }

    modalCancelarVenta.hide();

    const accion = conSaldo
        ? 'cancelarVenta'
        : 'cancelarVentaSinSaldo';

    Swal.fire({
        title: 'Procesando...',
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading()
    });

    try {

        const response = await fetch(`${URL_CONTROLLER}?action=${accion}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                id_venta: idVenta,
                motivo: motivo
            })
        });

        const res = await response.json();

        if (res.status === 'success') {

            Swal.fire({
                icon: 'success',
                title: 'Venta cancelada',
                text: res.message
            });

            getVentas();

        } else {

            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: res.message
            });

        }

    } catch (error) {

        console.error(error);

        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'No se pudo conectar con el servidor'
        });
    }
}
      // PASO 1: Esta función se dispara al dar click al botón de la tabla (abre el modal)
function modalFactura(id,factura) {
    const modalElement = document.getElementById('modalAgregarFactura');
    const folioInput = document.getElementById("folio-factura");
    
    // Limpiamos el input y errores previos por si acaso
    folioInput.value =factura;
    folioInput.classList.remove("is-invalid");

    // Guardamos el ID de la venta/viaje en el modal para no perderlo
    modalElement.setAttribute('data-id-actual', id);
   document.getElementById('id_venta_factura').value=id;

    // Abrimos el modal programáticamente con Bootstrap
    const modalInstance = new bootstrap.Modal(modalElement);
    modalInstance.show();
}

// PASO 2: Esta función se dispara al dar click en "Guardar" dentro del modal


// Tu// Función final encargada del backend
async function agregarFactura( id,folio) {
    console.log(`Guardando en BD -> ID: ${id}, Folio Factura: ${folio}`);
    
    // 1. Creamos el objeto FormData y le inyectamos los datos que necesita el controlador PHP
    const data = new FormData();
    data.append('venta_id', id);
    data.append('factura', folio);

    try {
        // Asumiendo que URL_CONTROLLER es tu constante global (ej: '../controllers/ventasController.php')
        const res = await fetch(`/cfsistem/app/controllers/ventasHistorialController.php?action=guardarFactura`, {
            method: 'POST',
            body: data // Enviamos el FormData con los valores
        });

        // Verificamos si la respuesta del servidor es un JSON válido
        const result = await res.json();

        if (result.status === 'success') {
            
            // Ojo: Si usaste la instancia limpia que te pasé en el paso anterior, 
            // puedes cerrar el modal de Bootstrap 5 así si no tienes 'modalObj' global:
            const modalElement = document.getElementById('modalAgregarFactura');
            const modalInstance = bootstrap.Modal.getInstance(modalElement);
            if (modalInstance) modalInstance.hide();

            // Recargamos la tabla principal de ventas
            if (typeof getVentas === 'function') getVentas();

            // Alerta de éxito con SweetAlert2
            Swal.fire({
                title: '¡Listo!',
                text: 'Factura guardada correctamente',
                icon: 'success',
                timer: 1000, // Subí a 1000ms (1 segundo) para que el usuario alcance a notar la palomita de éxito
                showConfirmButton: false
            });

            // 🔥 Volver a abrir automáticamente el detalle si es necesario
            setTimeout(() => {
                // Usamos el 'id' que entró originalmente por parámetro a esta función
                if (typeof verDetalle === 'function') {
                    verDetalle(id); 
                }
            }, 1005);

        } else {
            // Aquí manejamos errores devueltos por el backend (Excepciones del try/catch de tu PHP)
            Swal.fire('No se pudo guardar', result.message || 'Error desconocido', 'error');
        }

    } catch (e) {
        console.error("Error al procesar la factura:", e);
        Swal.fire('Error Técnico', 'Hubo un problema de conexión con el servidor', 'error');
    }
}

// Esta es la función que necesitas que se ejecute:


     cargarUsuariosSelect();
    async function cargarUsuariosSelect() {
    const select = document.getElementById('select-usuarios');
    if (!select) return; // Seguridad por si el select no está en la vista actual

    try {
        // 1. Realizar la petición a tu controlador de Cf System
        const url = '/cfsistem/app/controllers/ventasHistorialController.php?action=obtenerUsuarios';
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

    const modalObj = new bootstrap.Modal('#modalDetalle');
    let ventaActual = null;
    // La ruta al controlador (ajusta si el nombre del archivo varía)
    const URL_CONTROLLER = '/cfsistem/app/controllers/ventasHistorialController.php';

    async function getVentas() {
        $('#loader').removeClass('d-none');


        const params = new URLSearchParams({
            action: 'listar',
            // <--- Nuevo parámetro para el ID de venta
            f_search: $('#f_search').val(),
            f_rango: $('#f_rango').val(),
            f_inicio: $('#f_ini').val(),
            f_fin: $('#f_fin').val(),
            f_almacen: $('#f_almacen').val(),
            f_status: $('#f_status').val(),
            f_pago: $('#f_pago').val(),
            f_vendedor:$('#select-usuarios').val() ?? '',
            f_factura:$('#estado_factura').val() ?? ''
            

        });

        try {
            const res = await fetch(`${URL_CONTROLLER}?${params.toString()}`);
            const data = await res.json();
            //<td class="ps-3 small">${v.id}</td>
            let totalVendido=0;
            let deuda=0;

$('#tablaVentas tbody').html(data.map(v => {
    let total = 0;
                let pagado =  0;
                if(v.estado_general!='cancelada')
                {
                total = parseFloat(v.total) || 0;
                pagado = parseFloat(v.pagado) || 0;}     
    let saldo = total - pagado;
    
    if (v.estado_general == 'activa') {
        totalVendido += total;
        deuda += (total - pagado);
    }

    let badgeCobro = (saldo <= 0) ?
        '<span class="text-success small fw-bold"><i class="bi bi-check-circle"></i> Pagado</span>' :
        `<span class="text-danger small fw-bold">Debe: $${saldo.toFixed(2)}</span>`;

    let entrega = (v.estado_general == 'activa') ?
        `<span class="badge ${v.estado_entrega=='entregado'?'bg-success':(v.estado_entrega=='parcial'?'bg-warning text-dark':'bg-danger')}">
            ${v.estado_entrega.toUpperCase()}
        </span>` :
        '<span class="text-danger small fw-bold"><i class="bi bi-check-circle"></i> Cancelado</span>';

    let factura = (v.estado_general == 'activa') ?
        `${v.factura}
        <button type="button" class="btn btn-link text-primary p-1 border-0" onclick="modalFactura(${v.id},${v.factura})" title="Agregar Factura">
            <i class="bi bi-pencil-square me-2"></i>
        </button>` : '';
let rolAct=<?=  $rol ?>;
let botonCancelar=rolAct==1?`<button type="button" class="btn btn-link text-danger btn-sm px-3 border-0" 
                onclick="abrirModalCancelacion('${v.id}','${v.folio}')" 
                data-bs-toggle="tooltip" 
                data-bs-placement="top" 
                title="Cancelar Venta">
            <i class="bi bi-trash3 fs-5"></i>
        </button>`:'';
    let cancelada = (v.estado_general == 'activa') ? `
        
${botonCancelar}
        <div class="btn-group" role="group">
            <button type="button" class="btn btn-link text-secondary btn-sm px-3 border-0 dropdown-toggle remove-caret" 
                    data-bs-toggle="dropdown" 
                    aria-expanded="false"
                    data-bs-toggle="tooltip" 
                    data-bs-placement="top" 
                    title="Más opciones">
                <i class="bi bi-three-dots fs-5"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3 mt-2">
                <li>
                    <a class="dropdown-item py-2 text-warning" href="../controllers/editarVentaController.php?id=${v.id}">
                        <i class="bi bi-pencil-square me-2"></i> Editar Venta
                    </a>
                </li>
                <li><hr class="dropdown-divider opacity-50"></li>
                <li>
                    <a class="dropdown-item py-2 text-primary" href="/cfsistem/app/backend/ventas/ticket_venta.php?id=${v.id}" target="_blank">
                        <i class="bi bi-receipt me-2"></i> Imprimir Ticket
                    </a>
                </li>
                <li>
                    <a class="dropdown-item py-2 text-info" href="/cfsistem/app/backend/ventas/ticket_sin_precio.php?id=${v.id}" target="_blank">
                        <i class="bi bi-file-earmark-text me-2"></i> Imprimir Ticket sin precio
                    </a>
                </li><li>
                    <a class="dropdown-item py-2 text-info" href="/cfsistem/app/backend/ventas/ticketFormal.php?id=${v.id}" target="_blank">
                        <i class="bi bi-file-earmark-text me-2"></i> Imprimir Ticket Formal
                    </a>
                </li>
            </ul>
        </div>` : ``;

    return `<tr>
        <td class="ps-3 small">${v.fecha}</td>
        <td class="fw-bold">${v.folio}</td>
        <td><span class="badge bg-light text-dark border fw-normal">${v.almacen_nombre}</span></td>
        <td><div class="small fw-bold">${v.vendedor}</div></td>
        <td><div class="small fw-bold">${v.cliente}</div></td>
        <td class="fw-bold text-dark">$${total.toFixed(2)}</td>
        <td>${v.estado_general=='activa'? badgeCobro : '<span class="text-danger small fw-bold"><i class="bi bi-check-circle"></i> Cancelado</span>'}</td>
        <td><div class="small fw-bold">${factura}</div></td>
        <td class="text-center">${entrega}</td>
        <td class="text-end pe-3">
            <div class="btn-group bg-white rounded-3 shadow-sm border p-1" role="group" aria-label="Acciones de venta">
                <button type="button" class="btn btn-link text-dark btn-sm px-3 border-0" 
                        onclick="verDetalle(${v.id})" 
                        data-bs-toggle="tooltip" 
                        data-bs-placement="top" 
                        title="Gestionar Venta">
                    <i class="bi bi-sliders2 fs-5"></i>
                </button>
                ${cancelada}
            </div>
        </td>
    </tr>`;
}).join(''));

// Fila de totales corregida (Sin 'v.almacen_nombre' para evitar errores)
let totales = `<tr class="table-light fw-bold border-top border-dark">
    <td class="ps-3 small"></td>
    <td class="fw-bold">TOTALES</td>
    <td></td>
    <td></td>
    <td></td>
    <td class="text-dark">Total: $${totalVendido.toFixed(2)}</td>
    <td class="text-success">Cobrado: $${(totalVendido - deuda).toFixed(2)}</td>
    <td class="text-danger">Por Cobrar: $${deuda.toFixed(2)}</td>
    <td></td>
    <td></td>
</tr>`;



// CORRECCIÓN AQUÍ: Agregamos la fila al final del tbody usando .append() sin .join()
$('#tablaVentas tbody').append(totales);
        } catch (e) {
            console.error("Error al cargar ventas:", e);
        } finally {
            $('#loader').addClass('d-none');
        }
    }
   async function verDetalle(id) {
        try {
            // 🔥 OBTENER IDS PENDIENTES
            const respIds = await fetch(
                `/cfsistem/app/controllers/entregasController.php?ajax=get_ids_pendientes_venta&venta_id=${id}`
            );
            const resNAlmacen = await fetch(
                `/cfsistem/app/controllers/entregasController.php?ajax=obtener_id_almacen&id=${id}`
            );

            const dataAlmacen = await resNAlmacen.json();
            const almacen_id_conseguido = dataAlmacen.almacen.almacen_id;
            console.log(dataAlmacen.almacen.almacen_id);

            const dataIds = await respIds.json();

            console.log(dataIds.ids);

            // =====================================================
            // 🔥 HABILITAR / DESHABILITAR BOTÓN
            // =====================================================

            if (
                Array.isArray(dataIds.ids) &&
                dataIds.ids.length > 0

            ) {
               

            } else {

                $('#btnGestionVenta')
                    .addClass('d-none')
                    .prop('disabled', true)
                    .removeAttr('onclick');

            }
            const res = await fetch(`${URL_CONTROLLER}?action=obtenerDetalle&id=${id}`);
           cargarRepartos(id);
            const data = await res.json();
            console.log(data);
           
            
            ventaActual = data;
             $('#folioFactura').text(data.info.factura);
if (data.info.estado_general === 'cancelada') {
    $('#btnGestionVenta')
                    .addClass('d-none')
                    .prop('disabled', true)
                    .removeAttr('onclick');
                    $('#btnAbonar')
                    .addClass('d-none')
                    .prop('disabled', true)
                    .removeAttr('onclick'); $('#btnHabilitar')
                    .addClass('d-none')
                    .prop('disabled', true)
                    .removeAttr('onclick');
    $('#cancelado').text(`Cancelada por: ${data.info.observaciones}`);
} else {
    $('#cancelado').text('');
}
            $('#spanFolio').text(data.info.folio);
            $('#IdFolio').text(data.info.id);
             $('#Almacen_id').text(data.info.almacen_id);

            $('#detCliente').text(data.info.nombre_comercial);
            $('#detAlmacen').text(data.info.almacen);
             $('#detVendedor').text(data.info.vendedor);

            const total = parseFloat(data.info.total) || 0;
            const pagado = parseFloat(data.info.total_pagado) || 0;
            const deuda = total - pagado;
            $('#detTotalLabel').text('$' + total.toFixed(2));

            if (deuda <= 0) {
                $('#detSaldoLabel').text('LIQUIDADO').removeClass('text-danger').addClass('text-success');
                $('#btnAbonar').addClass('d-none');
            } else {
                $('#detSaldoLabel').text('$' + deuda.toFixed(2)).removeClass('text-success').addClass(
                    'text-danger');
                $('#btnAbonar').removeClass('d-none');
            }

            // --- RENDERIZADO DE PRODUCTOS CON CONVERSIÓN ---
            // --- RENDERIZADO DE PRODUCTOS CON CONVERSIÓN ---
            $('#tbodyDetalle').html(data.productos.map(p => {
                console.log(p);
                let cant = parseFloat(p.cantidad) || 0;
                let pendiente = (cant - (parseFloat(p.cantidad_entregada) || 0)).toFixed(3);

                let factor = parseFloat(p.factor_conversion) || 1;
                let cantPendiente = pendiente / factor;

                let pen = Number(pendiente/(1/p.equivalencia));
                let pendi = Number(cantPendiente);
                let disponible = (p.disponible / factor);
                console.log(disponible);
                let entregada = p.cantidad_entregada / factor;

                console.log({
                    pen,
                    tipo: typeof pen,
                    comparacion: pen > 0
                });
                // 1. Definimos qué se verá en la columna "Venta"
                let visualizacionVenta = "";
                let infoEquivalenciaSub = "";
                let unm = (parseFloat(p.cantidad_entregada) / (1 / parseFloat(p.equivalencia)));
                console.log(unm);
                unm = unm % 1 !== 0 ? unm.toFixed(0) : unm;
                if (factor > 1 && cant >= factor) {
                    // Si alcanza el factor (Ej: 20 bultos >= 20 factor)
                    let unidadesMayores = (cant / factor);
                    // Formateamos para que si es entero no muestre .00 (Ej: 1 en vez de 1.00)
                    let totalUnidadesStr = Number.isInteger(unidadesMayores) ? unidadesMayores :
                        unidadesMayores.toFixed(2);


                    // Lo que se verá grande en la celda
                    visualizacionVenta =
                        `<span class="fw-bold">${totalUnidadesStr} ${p.unidad_reporte}</span> <br> <small class="text-muted">(${cant} ${p.unidad_medida})</small>`;

                    // Leyenda pequeña debajo del nombre del producto (opcional, para referencia)
                    infoEquivalenciaSub =
                        `<div class="text-muted small" style="font-size: 0.65rem;">1 ${p.unidad_reporte} = ${factor} ${p.unidad_medida}</div>`;
                } else {
                    // Si no llega al factor (Ej: 10 bultos) mostramos la unidad normal
                    //agregar observaciones en ticket 
                    visualizacionVenta = `<span>${cant} ${p.unidad_medida}</span>`;
                }


                return `<tr>
        <td>
            <div class="fw-bold text-dark">${p.producto}</div>
            ${infoEquivalenciaSub}
        </td>
        <td class="text-center">
        ${ p.equivalencia>=1?cant/(1/p.equivalencia).toFixed(2):(cant*(p.equivalencia)).toFixed(2)} ${p.nombre}
        
      
        (${cant} ${p.unidad_medida})
            
        </td>
        <td class="text-center">
        
      
        ${entregada>1?entregada+' '+ p.unidad_reporte:
        (p.cantidad_entregada/(1/p.equivalencia))>=1?(p.cantidad_entregada/(1/p.equivalencia)).toFixed(3) +' '+ p.nombre:
        p.cantidad_entregada +' '+p.unidad_medida}</td>
        
        <td class="text-center text-danger fw-bold">${(cantPendiente>=1?cantPendiente.toFixed(3):pen.toFixed(3))} ${cantPendiente>=1? p.unidad_reporte:p.cantidad/(1/p.equivalencia)>1?p.nombre:p.unidad_medida}</td>
         <td class="text-center col-input d-none">
            ${pen.toFixed(4) > 0 ? 
                `<input type="number"
    class="form-control form-control-sm input-entrega2 mx-auto"
    max="${pen<=p.disponible ? (pendi>=1 ? pendi : pen) : (disponible>1 ? disponible : p.disponible)}"
    min="0"
    step="0.01"
    value="0.00"
    data-dvid="${p.dvid}"
    data-id="${p.producto_id}"
    data-factor="${(pendi>=1 && disponible>=1) ? factor : 1}"
    style="width:70px">
                   <input type="hidden" class="form-control form-control-sm input-entrega0 mx-auto" 
                    value="0"data-dvid=${p.dvid} data-id="${p.producto_id}" style="width:70px"step="0.01" min="0">
                     <span class="badge bg-success">${
                    (pendi>=1&& disponible>=1)?p.unidad_reporte:p.unidad_medida}</span>` 
                     
                : '<span class="badge bg-success">Completo</span>'}
        </td>
    </tr>`;
            }).join(''));
            // ... (dentro de verDetalle, después de renderizar historial de entregas)
            $('#tbodyHistorial').html(data.historial.length > 0 ? data.historial.map(h => {
                    // 1. Extraemos los valores del historial
                    // Si salen vacíos o undefined, es que el PHP no los está mandando en el JSON de historial
                    let cantH = parseFloat(h.cantidad) || 0;
                    let factorH = parseFloat(h.factor_conversion) || 1;
                    let uReporteH = h.unidad_reporte || '';
                    let uMedidaH = h.unidad_medida || '';

                    let visualizacionHistorial = "";
                    console.log((h.cantidad/(1/h.equivalencia))>=1?(h.cantidad/(1/h.equivalencia)).toFixed(3):cantH);
                    // 2. Aplicamos la misma lógica que usas arriba
                    
                        // Aquí verás si unidad_medida viene vacío desde la base de datos
                        visualizacionHistorial = `<span>${(h.cantidad/(1/h.equivalencia))>=1?(h.cantidad/(1/h.equivalencia)).toFixed(3):cantH} ${(h.cantidad/(1/h.equivalencia))>=1?(h.nombre):uMedidaH}</span>`;
                   
                    return `
    <tr>
        <td class="small">${h.fecha}</td>
        <td class="small">${h.usuario_nombre}</td>
        <td>
            <div class="fw-bold" style="font-size:0.85rem;">${h.producto}</div>
        </td>
        <td class="text-center">
            ${visualizacionHistorial}
        </td>
    </tr>`;
                }).join('') :
                '<tr><td colspan="4" class="text-center text-muted p-3">No hay entregas registradas</td></tr>');


            // --- RENDERIZADO DE HISTORIAL DE PAGOS ---
            if (data.pagos && data.pagos.length > 0) {
                $('#tbodyPagos').html(data.pagos.map(p => `
        <tr>
            <td class="small">${p.fecha}</td>
            <td class="fw-bold text-success">$${parseFloat(p.monto).toFixed(2)}</td>
            <td>
                <span class="badge bg-light text-dark border fw-normal">${p.metodo_pago} </span>
               
                <div class="text-muted" style="font-size:0.65rem">Recibió: ${p.usuario_nombre}</div>
            </td>
            <td>
            <span>
    ${
        p.metodo_pago !== 'Efectivo' &&
        p.metodo_pago !== 'Saldo a Favor'
            ? (p.referencia ?? '')
            : '-'
    }
</span> 
            </td>
        </tr>
    `).join(''));
            } else {
                $('#tbodyPagos').html(
                    '<tr><td colspan="3" class="text-center text-muted p-3">No hay abonos registrados</td></tr>'
                );
            }
            alternarModo(false);
            modalObj.show();
        } catch (error) {
            console.error("Error al obtener detalle:", error);
        }
    }
    document.addEventListener('input', e => {

      if (e.target.classList.contains('input-entrega1')) {

    const max = parseFloat(e.target.max) || 0;
    const min = parseFloat(e.target.min) || 0;
    const factor = parseFloat(e.target.dataset.factor) || 1;

    let value = e.target.value;

    // 👉 PERMITIR BORRADO COMPLETO
    if (value === "") {
        const contenedor = e.target.parentElement;
        const inputEntrega = contenedor.querySelector('.input-entrega');

        if (inputEntrega) {
            inputEntrega.value = "";
        }
        return; // 🔥 importante: no seguir procesando
    }

    value = parseFloat(value);

    if (isNaN(value)) return;

    if (value > max) value = max;
    if (value < min) value = min;

    e.target.value = value;

    const contenedor = e.target.parentElement;
    const inputEntrega = contenedor.querySelector('.input-entrega');

    if (inputEntrega) {
        inputEntrega.value = (value * factor).toFixed(2);
    }
}
    });
    async function cargarRepartos(idVenta) {

    const resp = await fetch(
        `/cfsistem/app/controllers/repartosController.php?action=get_repartos_entrega&id=${idVenta}`
    );
   

    const repartoViaje = await resp.json();
    let repartos=repartoViaje.data;
     console.log(repartoViaje);

    const tbody = document.getElementById('tbodyRepartos');
    tbody.innerHTML = '';

    if (!repartoViaje.success) return;

    // ================================
    // AGRUPAR POR FOLIO VIAJE
    // ================================
   
    // ================================
    // RENDER TABLA
    // ================================
    repartos.forEach(g => {

        const estadoClass =
            g.estatus_logistico === 'completado'
                ? 'bg-success'
                : 'bg-warning text-dark';

        const tr = `
            <tr>

                <td class="fw-bold">
                    ${g.entrega_id}
                </td>

                <td>
                    ${g.fecha}
                </td>

                <td>
                    <span >
                        ${g.direccion_entrega}
                    </span>
                </td>

                <td class="text-center">

                    <button class="btn btn-sm btn-outline-primary"
                      onclick="imprimirRuta('${g.entrega_id}','${g.folio}')">

                      
                        Ver Reparto 
                    </button>

                </td>

            </tr>
        `;

        tbody.insertAdjacentHTML('beforeend', tr);
    });
}
    async function procesarEntrega() {
        const fd = new FormData();
        let ok = false;

        $('.input-entrega').each(function() {

            const cant = parseFloat($(this).val());

            console.log($(this).data('dvid'), cant);

            if (cant > 0) {

                fd.append(
                    `productos[${$(this).data('dvid')}]`,
                    cant
                );

                ok = true;
            }
        });

        if (!ok) return Swal.fire('Atención', 'Indique al menos una cantidad válida para entregar', 'warning');

        fd.append('venta_id', ventaActual.info.id);

        try {
            const res = await fetch(`${URL_CONTROLLER}?action=guardarEntrega`, {
                method: 'POST',
                body: fd
            });

            // Verificamos si la respuesta del servidor es un JSON válido
            const result = await res.json();

            if (result.status === 'success') {

                modalObj.hide();

                getVentas();

                Swal.fire({
                    title: '¡Listo!',
                    text: 'Entrega guardada correctamente',
                    icon: 'success',
                    timer: 500,
                    showConfirmButton: false
                });

                // 🔥 volver a abrir automáticamente
                setTimeout(() => {

                    verDetalle(ventaActual.info.id);

                }, 501);

            } else {
                // AQUÍ MANEJAMOS EL ERROR DE STOCK (o cualquier otro error del Model)
                // Usamos result.message que es el que trae "Stock insuficiente en almacén..."
                Swal.fire('No se pudo entregar', result.message || 'Error desconocido', 'error');
            }

        } catch (e) {
            console.error("Error al procesar entrega:", e);
            Swal.fire('Error Técnico', 'Hubo un problema de conexión con el servidor', 'error');
        }
    } // Instanciamos el nuevo modal
    const modalAbonoObj = new bootstrap.Modal('#modalAbono');



    function togglePerso() {
        $('#div_p').toggleClass('d-none', $('#f_rango').val() !== 'personalizado');
        getVentas();
    }

    function alternarModo(e) {
        $('.col-input').toggleClass('d-none', !e);
        $('#btnHabilitar').toggle(!e && ventaActual.info.estado_entrega !== 'entregado');
        $('#controlesGuardar').toggleClass('d-none', !e);
    }

    $(document).ready(function() {
        // 1. Carga inicial de datos
        getVentas();

        // 2. Escuchadores para filtros (opcional, pero recomendado para centralizar)
        $('#f_rango').on('change', togglePerso);
        // getVentas ya se llama mediante onchange/onkeyup en tu HTML, lo cual está bien.

        console.log("Sistema de historial listo.");
    });
    </script>
    <script>
    async function confirmarCancelacion(idVenta, folio, total, pagado) {

        // 1. Lanzamos el SweetAlert con las 3 opciones
        const result = await Swal.fire({
            title: `¿Cancelar Venta ${folio}?`,
            text: "Selecciona si deseas reintegrar el dinero al saldo del cliente o solo anular la venta.",
            icon: 'warning',
            input: 'text',
            inputLabel: 'Motivo de la cancelación',
            inputPlaceholder: 'Escriba por qué se cancela...',
            showCancelButton: true,
            showDenyButton: true,
            confirmButtonColor: '#28a745', // Verde -> Con Saldo
            denyButtonColor: '#d33', // Rojo -> Sin Saldo
            cancelButtonColor: '#6c757d', // Gris -> Regresar
            confirmButtonText: '<i class="bi bi-cash-stack"></i> Con Saldo a Favor',
            denyButtonText: '<i class="bi bi-x-circle"></i> Sin Saldo',
            cancelButtonText: 'Regresar',
            inputValidator: (value) => {
                if (!value) return '¡El motivo es obligatorio!';
            }
        });

        // 2. Si se presionó cualquiera de los dos botones de ejecución (Confirmar o Denegar)
        if (result.isConfirmed || result.isDenied) {
            // IMPORTANTE: Capturamos el motivo desde result.value
            const motivo = 'cancelacion';

            // Elegimos la ruta del controlador según el botón
            const accion = result.isConfirmed ? 'cancelarVenta' : 'cancelarVentaSinSaldo';

            Swal.fire({
                title: 'Procesando...',
                didOpen: () => {
                    Swal.showLoading()
                },
                allowOutsideClick: false
            });

            try {
                const response = await fetch(`${URL_CONTROLLER}?action=${accion}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        id_venta: idVenta,
                        motivo: motivo
                    })
                });

                const res = await response.json();

                if (res.status === 'success') {
                    await Swal.fire({
                        title: '¡Venta Cancelada!',
                        text: res.message,
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false
                    });

                    // Refrescamos la tabla de ventas
                    if (typeof getVentas === 'function') getVentas();

                } else {
                    Swal.fire('Error', res.message, 'error');
                }
            } catch (error) {
                console.error("Error en la petición:", error);
                Swal.fire('Error', 'No se pudo conectar con el servidor', 'error');
            }
        }
    }
   async function imprimirRuta(entrega_ida, folioViaje) {

    document.getElementById('folioRutaPrint').textContent = entrega_ida;

    const respuesta = await fetch(
        `/cfsistem/app/controllers/repartosController.php?action=get_ruta_entrega_por_despacho&entrega_id=${entrega_ida}&id=${encodeURIComponent(folioViaje)}`
    );

    const data = await respuesta.json();

    console.log(data);

    if (!data.success) return;

    const cont = document.getElementById('contenidoRutaPrint');
    const datos = data.data;

    // =========================================
    // DATOS GENERALES
    // =========================================
    
    

    // =========================================
    // AGRUPAR PRODUCTOS
    // =========================================
    const productosAgrupados = {};

    datos.forEach(item => {
        const key = item.nombreProducto;
        if (!productosAgrupados[key]) {
            productosAgrupados[key] = {
                nombreProducto: item.nombreProducto,
                totalCantidad: 0,
                unidadMedida: item.unidadMedida,
                unidadReporte: item.unidadReporte,
                factor: item.factor
            };
        }
        productosAgrupados[key].totalCantidad += parseFloat(item.totalCantidad || 0);
    });

    // =========================================
    // GENERAR FILAS
    // =========================================
    let filas = '';

    datos.forEach((prod, i) => {
        
        const total = prod.totalCantidad / prod.factor;
        const totalCantidad = total >= 1 ? total : prod.totalCantidad;
        const unidad = total >= 1 ? prod.unidadReporte : (totalCantidad/(1/prod.equi))>=1?prod.nombreEqui:prod.unidadMedida;

        // Formatear dinámicamente el color del badge del estado en la tabla
        let badgeColor = 'bg-warning text-dark';
        if (prod.estatus_logistico === 'completado') badgeColor = 'bg-success text-white';
        if (prod.estatus_logistico === 'en_transito') badgeColor = 'bg-primary text-white';

        filas += `
            <tr>
                <td class="text-muted fw-semibold">${i + 1}</td>
                <td style="max-width:350px;" class="fw-medium text-dark">${prod.nombreProducto}</td>
                <td style="max-width:250px;" class="fw-bold text-primary">
                    ${parseFloat(totalCantidad/(1/prod.equi)).toFixed(2)} <span class="text-muted fw-normal small">${unidad}</span>
                </td>
                <td style="max-width:250px;" class="text-muted small">${prod.direccion_entrega ?? '-'}</td>
                <td class="text-center">
                    <span class="badge ${badgeColor}  text-uppercase font-monospace">${prod.estatus_logistico}</span>
                </td>
            </tr>
        `;
    });

    // Formatear color de estatus general
    let generalBadgeColor = 'bg-warning text-dark';
    if (data.data[0].estatus_logistico === 'completado') generalBadgeColor = 'bg-success text-white';
    if (data.data[0].estatus_logistico === 'en_transito') generalBadgeColor = 'bg-primary text-white';

    // =========================================
    // HTML GENERADO
    // =========================================
    console.log(data.data);
    let html = `
        <div class="hoja-ruta-container p-4">

            <!-- HEADER INTERNO -->
            <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
                <div>
                    <h4 class="fw-bold text-dark m-0 d-flex align-items-center gap-2">
                        <span>🚚</span> Venta ${data.data[0].folio_venta}: Hoja de Ruta
                    </h4>

                    <div class="text-muted small mt-1">
                        Folio de viaje: <span class="fw-bold text-dark font-monospace">${data.data[0].folio_viaje}</span>
                    </div><div class="text-muted small mt-1">
                        Registro de viaje: <span class="fw-bold text-dark font-monospace">${data.data[0].fecha_viaje ?? '-'}</span>
                    </div>
                </div>

                <div class="text-end">
                    <div class="small text-muted mb-1">Fecha de Salida:____________________</div>
                    <div class="small text-muted mb-1">Fecha de llegada:____________________</div>
                </div>
            </div>
<style>
*{
text-transform: uppercase !important;}
.info-grid{
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 12px;
    width: 100%;
}

/* tarjeta */
.info-box{
    border:1px solid #e9e9e9;
    border-radius:8px;
    padding:10px 12px;
    background:#fff;

    /* CLAVE: evita deformación en modal */
    min-width: 0;
}

/* títulos */
.info-title{
    font-size:10.5px;
    color:#6c757d;
    text-transform:uppercase;
    letter-spacing:.5px;
    margin-bottom:4px;
}

/* valor */
.info-value{
    font-size:13px;
    font-weight:600;
    line-height:1.2;

    /* evita desbordes en modal */
    white-space: normal;
    word-break: break-word;
}

/* subtítulo */
.info-sub{
    font-size:11.5px;
    color:#666;
}
</style>

            <!-- BLOQUES DE INFORMACIÓN PRINCIPAL -->
          <div class="info-grid">

    <div class="info-box">
        <div class="info-title">Unidad de Transporte</div>
        <div class="info-value">${data.data[0].unidad_nombre ?? '-'}</div>
        <div class="info-sub mt-1">
            Placas: <span class="fw-semibold">${data.data[0].unidad_placas ?? '-'}</span>
        </div>
    </div>

    <div class="info-box">
        <div class="info-title">Operador / Chofer</div>
        <div class="info-value">${data.data[0].nombre_chofer ?? '-'}</div>
        <div class="info-sub mt-1 text-muted">Asignado de ruta</div>
    </div>

    <div class="info-box">
        <div class="info-title">Cliente Destino</div>
        <div class="info-value">${data.data[0].cliente ?? '-'}</div>
        <div class="info-sub mt-1">
            Tel: <span class="fw-semibold">${data.data[0].tel_cliente ?? 'Sin teléfono'}</span>
        </div>
    </div>

</div>

            <!-- SECCIÓN DE DETALLES / TABLA -->
            <div class="table-responsive border rounded mb-4">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th style="width: 5%">#</th>
                            <th style="width: 40%">Producto descripción</th>
                            <th style="width: 20%">Cantidad total</th>
                            <th style="width: 23%">Dirección de entrega</th>
                            <th style="width: 12%" class="text-center">Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${filas}
                    </tbody>
                </table>
            </div>

            <!-- ÁREA DE FIRMAS FORMALIZADA -->
            <div class="firmas-container pt-4">
                <div class="row g-5">
                    <div class="col-4">
                        <div class="firma-box">
                            <div class="firma-linea"></div>
                            <div class="firma-nombre">Firma Chofer / Transportista</div>
                            <div class="text-muted small">Nombre y Fecha</div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="firma-box">
                            <div class="firma-linea"></div>
                            <div class="firma-nombre">Firma Cliente / Recibe</div>
                            <div class="text-muted small">Sello y Firma de conformidad</div>
                        </div>
                    </div>
                    <div class="col-4">
                       <div class="info-box">
        <div class="info-sub mt-1">Observaciones y Comentarios:</div>
        
    </div>
                     
                    
                </div>
            </div>

        </div>
    `;

    cont.innerHTML = html;

    const modal = new bootstrap.Modal(document.getElementById('modalImprimirRuta'));
    modal.show();
}
function imprimirModalRuta() {

    const contenido = document.getElementById('contenidoRutaPrint').innerHTML;
    // Se abre en un formato horizontal adecuado para media hoja
    const ventana = window.open('', '_blank', 'width=950,height=650');

    ventana.document.write(`
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <title>Hoja de Ruta</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
            <style>
                body {
                    font-family: 'Segoe UI', Arial, sans-serif;
                    background: #f8f9fa;
                    color: #333;
                    padding: 15px;
                    font-size: 11.5px; /* Un poco más compacta para media hoja */
                }

                /* Contenedor tipo hoja limpia ajustable */
                .ticket {
                    width: 100%;
                    max-width: 820px;
                    margin: auto;
                    background: #fff;
                    border: 1px solid #e0e0e0;
                    border-radius: 12px;
                    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
                    overflow: hidden;
                }

                .hoja-ruta-container {
                    padding: 20px !important;
                }

                /* Cajas de datos estilizadas */
                .info-box {
                    border: 1px solid #e2e8f0;
                    border-radius: 8px;
                    padding: 10px 12px;
                    background: #f8fafc;
                    height: 100%;
                }

                .info-title {
                    font-size: 10px;
                    color: #64748b;
                    text-transform: uppercase;
                    letter-spacing: .6px;
                    font-weight: 700;
                    margin-bottom: 3px;
                }

                .info-value {
                    font-size: 12.5px;
                    font-weight: 600;
                    color: #1e293b;
                    line-height: 1.2;
                }

                .info-sub {
                    font-size: 11px;
                    color: #64748b;
                }

                /* Tablas pulidas */
                table thead th {
                    background-color: #f1f5f9 !important;
                    color: #475569 !important;
                    font-size: 10px;
                    text-transform: uppercase;
                    letter-spacing: .5px;
                    font-weight: 700;
                    padding: 8px 10px !important;
                    border-bottom: 2px solid #e2e8f0 !important;
                }

                table tbody td {
                    
                  
                    font-size: 11.5px;
                }

                /* Sección de Firmas estructurada */
                .firmas-container {
                    page-break-inside: avoid;
                }

                .firma-box {
                    text-align: center;
                    padding: 5px;
                }

                .firma-linea {
                    width: 75%;
                    margin: 35px auto 5px auto;
                    border-top: 1px solid #94a3b8;
                }

                .firma-nombre {
                    font-size: 11px;
                    font-weight: 600;
                    color: #1e293b;
                }
@media print {
    .info-grid{
        display: grid !important;
        grid-template-columns: repeat(3, 1fr) !important;
        gap: 10px !important;
    }
}
                /* Forzado estricto de Media Hoja (Formato Horizontal Compacto) */
                @media print {
                   
                    body {
                        background: #f8f9fa03 !important;
                        -webkit-print-color-adjust: exact !important;
                        print-color-adjust: exact !important;
                        padding: 0 !important;
                    }
                    .ticket {
                        width: 100% !important; /* Toma el ancho horizontal disponible sin estirarse verticalmente */
                        max-width: 100% !important;
                        background: #ffffff00 !important;
                        border: 1px solid #e0e0e0 !important;
                        border-radius: 12px !important;
                        box-shadow: 0 4px 12px rgba(0,0,0,0.05) !important;
                        margin: 0 auto !important;
                        -webkit-print-color-adjust: exact !important;
                        print-color-adjust: exact !important;
                    }
                    .no-print {
                        display: none !important;
                    }
                    tr { 
                        page-break-inside: avoid !important; 
                    }
                    .info-box {
                        background: #f8fafc14 !important;
                        border: 1px solid #e2e8f0 !important;
                        -webkit-print-color-adjust: exact !important;
                        print-color-adjust: exact !important;
                    }
                    table thead th {
                        background-color: #f1f5f9 !important;
                        -webkit-print-color-adjust: exact !important;
                        print-color-adjust: exact !important;
                    }
                }
                    table {
    border-collapse: collapse !important;
}

/* 🔥 CLAVE: elimina espacio interno de TODAS las celdas */
table tbody td {
    padding: 2px 6px !important;
    margin: 0 !important;
    line-height: 1.1 !important;
    vertical-align: middle !important;
}

/* elimina espacio extra de párrafos y saltos */
table p {
    margin: 0 !important;
    padding: 0 !important;
}

/* elimina saltos visuales tipo bloque */
table br {
    display: none !important;
}

/* si quieres aún MÁS compacto */
.table-compact td {
    padding: 1px 4px !important;
}
            </style>
        </head>
        <body>
<img
    src="/cfsistem/public/assets/logo.ico"
    style="
        position: fixed;
        top: 22.5%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 300px;
        opacity: 0.08;
        z-index: -1;
    "
>
            <div class="text-end mb-3 no-print" style="max-width: 850px; margin: auto;">
                <button class="btn btn-dark px-4 shadow-sm fw-semibold" onclick="window.print()">
                    🖨 Enviar a Impresora
                </button>
            </div>

            <div class="ticket">
                <div class="hoja-ruta-container">
                    ${contenido}
                </div>
            </div>

            <!-- Disparador automático de impresión al terminar de cargar -->
            <script>
                window.onload = function() {
                    setTimeout(function() {
                        window.print();
                    }, 300);
                };
            <\/script>

        </body>
        </html>
    `);

    ventana.document.close();
    ventana.focus();
}
   </script>
   <script>
    // Selecciona todos los inputs de texto y también los textareas
    document.querySelectorAll('input[type="text"], textarea').forEach(elemento => {
        elemento.addEventListener('input', function() {
            // Convierte el valor a mayúsculas en tiempo real
            this.value = this.value.toUpperCase();
        });
    });
</script>
</body>

</html>