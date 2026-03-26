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
        padding-top: 20px
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
                <h3 class="fw-bold text-dark m-0">Control de Entregas</h3>
                <div id="loader" class="spinner-border spinner-border-sm text-secondary d-none"></div>
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
                                <option value="todos">Historial Completo</option>
                                <option value="hoy">Hoy</option>
                                <option value="ayer">Ayer</option>
                                <option value="semana">Semana</option>
                                <option value="mes">Mes</option>
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
                            <select id="f_almacen" class="form-select form-select-sm" onchange="getVentas()"
                                <?= ($_SESSION['rol_id'] != 1 ? 'disabled':'') ?>>
                                <option value="">Todas</option>
                                <?php 
                                $alms = $conexion->query("SELECT id, nombre FROM almacenes");
                                while($a = $alms->fetch_assoc()){
                                    $sel = ($_SESSION['rol_id'] != 1 && $_SESSION['almacen_id'] == $a['id']) ? 'selected':'';
                                    echo "<option value='{$a['id']}' $sel>{$a['nombre']}</option>";
                                }
                                ?>
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
                                <th>Id</th>
                                <th class="ps-3">Fecha</th>
                                <th>Folio</th>
                                <th>Almacén</th>
                                <th>Cliente</th>
                                <th>Total</th>
                                <th>Saldo Cobro</th>
                                <th class="text-center">Estado Entrega</th>
                                <th class="text-end pe-3">Acciones</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalDetalle" tabindex="-1">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content border-0">
                <div class="modal-header">
                    <h6 class="modal-title fw-bold">Gestión de Venta: <span id="spanFolio"></span></h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-0">
                    <div class="row g-0">
                        <div class="col-md-3 bg-light border-end p-4">
                            <p id="detCliente" class="fw-bold small mb-1"></p>
                            <p id="detAlmacen" class="fw-bold small mb-3"></p>

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

                            <div id="contenedorBoton">
                                <button id="btnHabilitar" class="btn btn-action w-100 mb-2 py-2 fw-bold"
                                    onclick="alternarModo(true)">Nueva Entrega</button>

                                <button id="btnAbonar" class="btn btn-primary w-100 mb-2 py-2 fw-bold"
                                    onclick="abrirFlujoAbono()">
                                    <i class="bi bi-cash"></i> Registrar Abono
                                </button>
                            </div>

                            <div id="controlesGuardar" class="d-none">
                                <button class="btn btn-success w-100 mb-2 py-2 fw-bold"
                                    onclick="procesarEntrega()">Guardar Cambios</button>
                                <button class="btn btn-link text-secondary w-100 btn-sm"
                                    onclick="alternarModo(false)">Cancelar</button>
                            </div>
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
                                                </tr>
                                            </thead>
                                            <tbody id="tbodyPagos" class="small"></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modalAbono" tabindex="-1" style="z-index: 1060;">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content shadow-lg border-0">
                <div class="modal-header bg-dark text-white">
                    <h6 class="modal-title">Registrar Abono</h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary text-uppercase">Monto a Recibir</label>
                        <div class="input-group input-group-lg">
                            <span class="input-group-text bg-light border-end-0">$</span>
                            <input type="number" id="inputMontoAbono" class="form-control border-start-0 ps-0 fw-bold"
                                step="any">
                        </div>
                        <div id="infoSaldo" class="badge bg-light text-dark border w-100 mt-2 py-2"></div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary text-uppercase">Método de Pago</label>
                        <select id="selectMetodoPago" class="form-select">
                            <option value="Efectivo">Efectivo</option>
                            <option value="Transferencia">Transferencia</option>
                            <option value="Tarjeta">Tarjeta</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer p-2">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary btn-sm" onclick="guardarAbonoModal()">Guardar</button>
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
    const modalObj = new bootstrap.Modal('#modalDetalle');
    let ventaActual = null;
    // La ruta al controlador (ajusta si el nombre del archivo varía)
    const URL_CONTROLLER = '../controllers/ventasHistorialController.php';

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
            f_pago: $('#f_pago').val()
        });

        try {
            const res = await fetch(`${URL_CONTROLLER}?${params.toString()}`);
            const data = await res.json();

            $('#tablaVentas tbody').html(data.map(v => {
                let total = parseFloat(v.total) || 0;
                let pagado = parseFloat(v.pagado) || 0;
                let saldo = total - pagado;
                let badgeCobro = (saldo <= 0) ?
                    '<span class="text-success small fw-bold"><i class="bi bi-check-circle"></i> Pagado</span>' :
                    `<span class="text-danger small fw-bold">Debe: $${saldo.toFixed(2)}</span>`;

                return `<tr>
                <td class="ps-3 small">${v.id}</td>
                <td class="ps-3 small">${v.fecha}</td>
                <td class="fw-bold">${v.folio}</td>
                <td><span class="badge bg-light text-dark border fw-normal">${v.almacen_nombre}</span></td>
                <td><div class="small fw-bold">${v.cliente}</div></td>
                <td class="fw-bold text-dark">$${total.toFixed(2)}</td>
                <td>${badgeCobro}</td>
                <td class="text-center">
                    <span class="badge ${v.estado_entrega=='entregado'?'bg-success':(v.estado_entrega=='parcial'?'bg-warning text-dark':'bg-danger')}">
                        ${v.estado_entrega.toUpperCase()}
                    </span>
                </td>
                <td class="text-end pe-3">
                    <div class="btn-group">
                        <a href="../controllers/editarVentaController.php?id=${v.id}" class="btn btn-warning btn-sm shadow-sm">
                            <i class="fas fa-edit"></i> Editar Venta
                        </a>
                        <button class="btn btn-sm btn-dark shadow-sm" onclick="verDetalle(${v.id})">
                            <i class="bi bi-gear-fill"></i> Gestionar
                        </button>
                        <a class="btn btn-sm btn-primary shadow-sm" href="/cfsistem/app/backend/ventas/ticket_venta.php?id=${v.id}" target="_blank">
                            <i class="bi bi-currency-dollar"></i> Ticket
                        </a>
                        <a class="btn btn-sm btn-info text-white shadow-sm" href="/cfsistem/app/backend/ventas/ticket_sin_precio.php?id=${v.id}" target="_blank" title="Imprimir Remisión sin Precios">
                            <i class="bi bi-file-earmark-text"></i> Remisión
                        </a>
                        <button class="btn btn-sm btn-danger shadow-sm" onclick="confirmarCancelacion(${v.id}, '${v.folio}')" title="Cancelar Venta">
                <i class="bi bi-x-circle-fill"></i> Cancelar
            </button>
                    </div>
                </td>
            </tr>`;
            }).join(''));
        } catch (e) {
            console.error("Error al cargar ventas:", e);
        } finally {
            $('#loader').addClass('d-none');
        }
    }
    async function verDetalle(id) {
        try {
            const res = await fetch(`${URL_CONTROLLER}?action=obtenerDetalle&id=${id}`);
            const data = await res.json();
            ventaActual = data;

            $('#spanFolio').text(data.info.folio);
            $('#detCliente').text(data.info.nombre_comercial);
            $('#detAlmacen').text(data.info.almacen);

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
                let cant = parseFloat(p.cantidad) || 0;
                let pen = cant - (parseFloat(p.cantidad_entregada) || 0);
                let factor = parseFloat(p.factor_conversion) || 1;

                // 1. Definimos qué se verá en la columna "Venta"
                let visualizacionVenta = "";
                let infoEquivalenciaSub = "";

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
                    visualizacionVenta = `<span>${cant} ${p.unidad_medida}</span>`;
                }

                return `<tr>
        <td>
            <div class="fw-bold text-dark">${p.producto}</div>
            ${infoEquivalenciaSub}
        </td>
        <td class="text-center">
            ${visualizacionVenta}
        </td>
        <td class="text-center">${p.cantidad_entregada}</td>
        <td class="text-center text-danger fw-bold">${pen}</td>
        <td class="text-center col-input d-none">
            ${pen > 0 ? 
                `<input type="number" class="form-control form-control-sm input-entrega mx-auto" 
                    max="${pen}" min="0" value="0" data-id="${p.id}" style="width:70px">` 
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

    // 2. Aplicamos la misma lógica que usas arriba
    if (factorH > 1 && cantH >= factorH) {
        let unidadesMayoresH = (cantH / factorH);
        let totalUnidadesStrH = Number.isInteger(unidadesMayoresH) ? 
            unidadesMayoresH : 
            unidadesMayoresH.toFixed(2);

        visualizacionHistorial = `
            <span class="fw-bold text-primary">${totalUnidadesStrH} ${uReporteH}</span> 
            <br> <small class="text-muted">(${cantH} ${uMedidaH})</small>
        `;
    } else {
        // Aquí verás si unidad_medida viene vacío desde la base de datos
        visualizacionHistorial = `<span>${cantH} ${uMedidaH}</span>`;
    }

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
}).join('') : '<tr><td colspan="4" class="text-center text-muted p-3">No hay entregas registradas</td></tr>');


            // --- RENDERIZADO DE HISTORIAL DE PAGOS ---
            if (data.pagos && data.pagos.length > 0) {
                $('#tbodyPagos').html(data.pagos.map(p => `
        <tr>
            <td class="small">${p.fecha}</td>
            <td class="fw-bold text-success">$${parseFloat(p.monto).toFixed(2)}</td>
            <td>
                <span class="badge bg-light text-dark border fw-normal">${p.metodo_pago}</span>
                <div class="text-muted" style="font-size:0.65rem">Recibió: ${p.usuario_nombre}</div>
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
    async function procesarEntrega() {
        const fd = new FormData();
        let ok = false;
        $('.input-entrega').each(function() {
            if ($(this).val() > 0) {
                fd.append(`productos[${$(this).data('id')}]`, $(this).val());
                ok = true;
            }
        });

        if (!ok) return Swal.fire('Error', 'Indique cantidades', 'warning');

        fd.append('venta_id', ventaActual.info.id);

        try {
            // Mandamos action por URL para que el controlador lo atrape en $_GET['action']
            const res = await fetch(`${URL_CONTROLLER}?action=guardarEntrega`, {
                method: 'POST',
                body: fd
            });
            const result = await res.json();
            if (result.status == 'success') {
                modalObj.hide();
                getVentas();
                Swal.fire('Listo', 'Entrega guardada', 'success');
            }
        } catch (e) {
            console.error("Error al procesar entrega:", e);
        }
    }
    // Instanciamos el nuevo modal
    const modalAbonoObj = new bootstrap.Modal('#modalAbono');

    function abrirFlujoAbono() {
        const totalVenta = parseFloat(ventaActual.info.total || 0);
        const pagado = parseFloat(ventaActual.info.total_pagado || 0);
        const saldoPendiente = totalVenta - pagado;

        if (saldoPendiente <= 0) {
            Swal.fire('Venta Liquidada', 'Sin saldo pendiente.', 'success');
            return;
        }

        // Llenamos los datos en el mini-modal
        $('#inputMontoAbono').val(saldoPendiente.toFixed(2));
        $('#infoSaldo').text('Saldo máximo: $' + saldoPendiente.toFixed(2));

        // Mostramos el modal
        modalAbonoObj.show();

        // Forzamos el foco al abrir (esto ya no fallará)
        document.getElementById('modalAbono').addEventListener('shown.bs.modal', () => {
            document.getElementById('inputMontoAbono').focus();
            document.getElementById('inputMontoAbono').select();
        }, {
            once: true
        });
    }

    // Agrega este listener para validar en tiempo real mientras el usuario escribe
    $(document).on('input', '#inputMontoAbono', function() {
        const totalVenta = parseFloat(ventaActual.info.total || 0);
        const pagado = parseFloat(ventaActual.info.total_pagado || 0);
        const saldoPendiente = parseFloat((totalVenta - pagado).toFixed(2));
        const montoIngresado = parseFloat($(this).val()) || 0;

        if (montoIngresado > saldoPendiente) {
            $(this).addClass('is-invalid text-danger');
            $('#infoSaldo').removeClass('bg-light text-dark').addClass('bg-danger text-white');
        } else {
            $(this).removeClass('is-invalid text-danger');
            $('#infoSaldo').removeClass('bg-danger text-white').addClass('bg-light text-dark');
        }
    });

    async function guardarAbonoModal() {
        const totalVenta = parseFloat(ventaActual.info.total || 0);
        const pagado = parseFloat(ventaActual.info.total_pagado || 0);
        const saldoPendiente = parseFloat((totalVenta - pagado).toFixed(2));
        const monto = parseFloat($('#inputMontoAbono').val());

        // Obtenemos el método seleccionado
        const metodo = $('#selectMetodoPago').val();

        if (!monto || monto <= 0) {
            return Swal.fire('Error', 'Ingrese un monto válido', 'warning');
        }

        if (monto > saldoPendiente) {
            return Swal.fire('Error', `El monto excede el saldo ($${saldoPendiente})`, 'error');
        }

        const fd = new FormData();
        fd.append('venta_id', ventaActual.info.id);
        fd.append('monto', monto);
        fd.append('metodo_pago', metodo); // <--- Esto envía el método al controlador

        try {
            const res = await fetch(`${URL_CONTROLLER}?action=guardarAbono`, {
                method: 'POST',
                body: fd
            });
            const data = await res.json();

            if (data.status === 'success') {
                modalAbonoObj.hide();
                Swal.fire('Éxito', 'Abono guardado correctamente', 'success');
                await verDetalle(ventaActual.info.id);
                getVentas();
            } else {
                Swal.fire('Error', data.message || 'Error al guardar', 'error');
            }
        } catch (e) {
            console.error("Error en la petición:", e);
        }
    }

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
    async function confirmarCancelacion(idVenta, folio) {
    // 1. Pedimos confirmación y motivo
    const { value: motivo } = await Swal.fire({
        title: `¿Cancelar Venta ${folio}?`,
        text: "Esta acción devolverá el stock al almacén y anulará los pagos realizados. No se puede deshacer.",
        icon: 'warning',
        input: 'text',
        inputLabel: 'Motivo de la cancelación',
        inputPlaceholder: 'Escriba por qué se cancela...',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, cancelar venta',
        cancelButtonText: 'Regresar',
        inputValidator: (value) => {
            if (!value) {
                return '¡Debes escribir un motivo para la cancelación!'
            }
        }
    });

    // 2. Si el usuario confirmó y escribió un motivo
    if (motivo) {
        // Mostramos loader mientras procesa
        Swal.fire({
            title: 'Procesando...',
            didOpen: () => { Swal.showLoading() },
            allowOutsideClick: false
        });

        try {
            // Petición POST al controlador
            const response = await fetch(`${URL_CONTROLLER}?action=cancelarVenta`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    id_venta: idVenta,
                    motivo: motivo
                })
            });

            const result = await response.json();

            if (result.status === 'success') {
                await Swal.fire({
                    title: '¡Venta Cancelada!',
                    text: result.message,
                    icon: 'success',
                    timer: 2000
                });
                
                // Recargamos la tabla (la venta desaparecerá por el filtro del modelo)
                getVentas(); 
            } else {
                Swal.fire('Error', result.message, 'error');
            }
        } catch (error) {
            console.error("Error en la cancelación:", error);
            Swal.fire('Error', 'No se pudo conectar con el servidor', 'error');
        }
    }
}
</script>
</body>

</html>