<style>
    /* ═══ Z-INDEX ═══ */
    #modalDetalle {
        z-index: 10050 !important;
    }



    /* ═══ RESET: no heredar uppercase del body ═══ */
    #modalDetalle,
    #modalDetalle * {
        text-transform: none;
    }

    #modalDetalle .modal-title,
    #modalDetalle table thead th {
        text-transform: uppercase !important;
    }

    /* ═══ HEADER ═══ */
    #modalDetalle .modal-header {
        background-color: #2c3e50 !important;
        color: #fff;
        border: none;
        padding: 14px 18px;
    }

    #modalDetalle .modal-title {
        font-weight: 700;
        color: #fff;
    }

    #modalDetalle .btn-close {
        filter: invert(1) brightness(200%);
        opacity: 0.85;
    }

    #modalDetalle .btn-close:hover {
        opacity: 1;
    }

    /* ═══ CONTENT ═══ */
    #modalDetalle .modal-content {
        border: none;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.25);
    }

    #modalDetalle .modal-body {}

    /* ═══ TABLAS ═══ */
    #modalDetalle table thead th {
        background-color: #2c3e50;
        color: #fff;
        font-size: 0.7rem;
        padding: 8px 10px;
        border: none;
    }

    #modalDetalle table tbody td {
        padding: 6px 10px;
        font-size: 0.82rem;
        vertical-align: middle;
    }

    /* ═══ INPUTS ═══ */
    #modalDetalle .input-entrega {
        border: 2px solid #28a745 !important;
        max-width: 90px;
        text-align: center;
        font-weight: bold;
    }

    /* El contenedor raíz de SweetAlert2 */
    .swal2-container {
        z-index: 99999 !important;
    }

    /* El backdrop de SweetAlert */
    .swal2-container.swal2-backdrop-show {
        z-index: 99999 !important;
    }

    /* El popup en sí */
    .swal2-popup {
        z-index: 100000 !important;
        position: relative;
    }

    /* El toast (notificaciones sin backdrop) */
    .swal2-toast-shown .swal2-container {
        z-index: 99999 !important;
    }
</style>


<div class="modal fade" id="modalDetalle" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0">
            <div class="modal-header">
                <h6 class="modal-title fw-bold">Gestión de Venta: <span id="spanFolio"></span></h6>
                <span id="IdFolio" style="visibility: hidden;"></span>
                <span id="Almacen_id" style="visibility: hidden;"></span>
                <button type="button" class="btn btn-outline-primary" onclick="imprimirDetalleVenta()">
                    <i class="bi bi-printer-fill"></i> Imprimir
                </button>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <div class="row g-0">
                    <div class="col-md-3  border-end p-4">
                        <div class="d-flex flex-column gap-2 mb-4">

                            <!-- Cliente -->
                            <div class="p-2 px-3 rounded-3 bg-body-tertiary border border-light-subtle">
                                <small class="d-block text-body-secondary fw-semibold text-uppercase"
                                    style="font-size: 0.68rem; letter-spacing: 0.5px;">
                                    <i class="bi bi-person me-1 text-primary"></i> Cliente
                                </small>
                                <span id="detCliente"
                                    class="fw-bold card-title-text small d-block text-truncate">--</span>
                            </div>

                            <!-- Almacén -->
                            <div class="p-2 px-3 rounded-3 bg-body-tertiary border border-light-subtle">
                                <small class="d-block text-body-secondary fw-semibold text-uppercase"
                                    style="font-size: 0.68rem; letter-spacing: 0.5px;">
                                    <i class="bi bi-box-seam me-1 text-primary"></i> Almacén
                                </small>
                                <span id="detAlmacen"
                                    class="fw-bold card-title-text small d-block text-truncate">--</span>
                            </div>

                            <!-- Vendedor -->
                            <div class="p-2 px-3 rounded-3 bg-body-tertiary border border-light-subtle">
                                <small class="d-block text-body-secondary fw-semibold text-uppercase"
                                    style="font-size: 0.68rem; letter-spacing: 0.5px;">
                                    <i class="bi bi-person-badge me-1 text-primary"></i> Vendedor
                                </small>
                                <span id="detVendedor"
                                    class="fw-bold card-title-text small d-block text-truncate">--</span>
                            </div>

                            <!-- Folio Factura -->
                            <div class="p-2 px-3 rounded-3 bg-body-tertiary border border-light-subtle">
                                <small class="d-block text-body-secondary fw-semibold text-uppercase"
                                    style="font-size: 0.68rem; letter-spacing: 0.5px;">
                                    <i class="bi bi-receipt me-1 text-primary"></i> Folio / Factura
                                </small>
                                <span id="folioFactura"
                                    class="fw-bold card-title-text small d-block text-truncate">--</span>
                            </div>

                        </div>



                        <div class="mb-4 p-2  border rounded shadow-sm text-center">
                            <div class="mb-2 pb-2 border-bottom">
                                <span class="d-block small text-body-secondary text-uppercase fw-bold">Total de
                                    Venta</span>
                                <span id="detTotalLabel" class="h6 fw-bold card-title-text">$0.00</span>
                            </div>

                            <div>
                                <span class="d-block small text-body-secondary text-uppercase fw-bold">Saldo
                                    Pendiente</span>
                                <span id="detSaldoLabel" class="h5 fw-bold text-danger">$0.00</span>
                            </div>
                        </div>

                        <?php if ($_SESSION['rol_id'] == 1 || $_SESSION['rol_id'] == 2): ?>
                            <div id="contenedorBoton">
                                <button id="btnHabilitar" class="btn btn-action w-100 mb-2 py-2 fw-bold" onclick="abrirModalDespachoVentaTotal(
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
                                <h6 class="small fw-bold text-uppercase text-body-secondary"><i class="bi bi-truck"></i>
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
                                <h6 class="small fw-bold text-uppercase text-body-secondary"><i
                                        class="bi bi-cash-stack"></i>
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
                                <h6 class="small fw-bold text-uppercase text-body-secondary">
                                    <i class="bi bi-map"></i>
                                    Repartos
                                </h6>

                                <div class="table-responsive border rounded" style="max-height: 220px;">
                                    <table class="table table-sm align-middle mb-0">
                                        <thead class="table-light">
                                            <tr class="small text-uppercase">
                                                <th># Reparto</th>
                                                <th class="py-2 px-3">Folio de viaje</th>
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
<script>
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
                    .removeAttr('onclick');
                $('#btnHabilitar')
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

                let pen = Number(pendiente / (1 / p.equivalencia));
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
                        `<span class="fw-bold">${totalUnidadesStr} ${p.unidad_reporte}</span> <br> <small class="text-body-secondary">(${cant} ${p.unidad_medida})</small>`;

                    // Leyenda pequeña debajo del nombre del producto (opcional, para referencia)
                    infoEquivalenciaSub =
                        `<div class="text-body-secondary small" style="font-size: 0.65rem;">1 ${p.unidad_reporte} = ${factor} ${p.unidad_medida}</div>`;
                } else {
                    // Si no llega al factor (Ej: 10 bultos) mostramos la unidad normal
                    //agregar observaciones en ticket 
                    visualizacionVenta = `<span>${cant} ${p.unidad_medida}</span>`;
                }


                return `<tr>
        <td>
            <div class="fw-bold card-title-text">${p.producto}</div>
            ${infoEquivalenciaSub}
        </td>
        <td class="text-center">
        ${p.equivalencia >= 1 ? cant / (1 / p.equivalencia).toFixed(2) : (cant * (p.equivalencia)).toFixed(2)} ${p.nombre}
        
      
        (${cant} ${p.unidad_medida})
            
        </td>
        <td class="text-center">
        
      
        ${entregada > 1 ? entregada + ' ' + p.unidad_reporte :
                        (p.cantidad_entregada / (1 / p.equivalencia)) >= 1 ? (p.cantidad_entregada / (1 / p.equivalencia)).toFixed(3) + ' ' + p.nombre :
                            p.cantidad_entregada + ' ' + p.unidad_medida}</td>
        
        <td class="text-center text-danger fw-bold">${(cantPendiente >= 1 ? cantPendiente.toFixed(3) : pen.toFixed(3))} ${cantPendiente >= 1 ? p.unidad_reporte : p.cantidad / (1 / p.equivalencia) > 1 ? p.nombre : p.unidad_medida}</td>
         <td class="text-center col-input d-none">
            ${pen.toFixed(4) > 0 ?
                        `<input type="number"
    class="form-control form-control-sm input-entrega2 mx-auto"
    max="${pen <= p.disponible ? (pendi >= 1 ? pendi : pen) : (disponible > 1 ? disponible : p.disponible)}"
    min="0"
    step="0.01"
    value="0.00"
    data-dvid="${p.dvid}"
    data-id="${p.producto_id}"
    data-factor="${(pendi >= 1 && disponible >= 1) ? factor : 1}"
    style="width:70px">
                   <input type="hidden" class="form-control form-control-sm input-entrega0 mx-auto" 
                    value="0"data-dvid=${p.dvid} data-id="${p.producto_id}" style="width:70px"step="0.01" min="0">
                     <span class="badge bg-success">${(pendi >= 1 && disponible >= 1) ? p.unidad_reporte : p.unidad_medida}</span>`

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
                console.log((h.cantidad / (1 / h.equivalencia)) >= 1 ? (h.cantidad / (1 / h
                    .equivalencia)).toFixed(3) : cantH);
                // 2. Aplicamos la misma lógica que usas arriba

                // Aquí verás si unidad_medida viene vacío desde la base de datos
                visualizacionHistorial =
                    `<span>${(h.cantidad / (1 / h.equivalencia)) >= 1 ? (h.cantidad / (1 / h.equivalencia)).toFixed(3) : cantH} ${(h.cantidad / (1 / h.equivalencia)) >= 1 ? (h.nombre) : uMedidaH}</span>`;

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
                '<tr><td colspan="4" class="text-center text-body-secondary p-3">No hay entregas registradas</td></tr>'
            );


            // --- RENDERIZADO DE HISTORIAL DE PAGOS ---
            if (data.pagos && data.pagos.length > 0) {
                $('#tbodyPagos').html(data.pagos.map(p => `
        <tr>
            <td class="small">${p.fecha}</td>
            <td class="fw-bold text-success">$${parseFloat(p.monto).toFixed(2)}</td>
            <td>
                <span class="badge bg-light text-dark border fw-normal">${p.metodo_pago} </span>
               
                <div class="text-body-secondary" style="font-size:0.65rem">Recibió: ${p.usuario_nombre}</div>
            </td>
            <td>
            <span>
    ${(p.referencia ?? '')

                    }
</span> 
            </td>
        </tr>
    `).join(''));
            } else {
                $('#tbodyPagos').html(
                    '<tr><td colspan="3" class="text-center text-body-secondary p-3">No hay abonos registrados</td></tr>'
                );
            }
            alternarModo(false);
            modalObj.show();
        } catch (error) {
            console.error("Error al obtener detalle:", error);
        }
    }
    async function cargarRepartos(idVenta) {

        const resp = await fetch(
            `/cfsistem/app/controllers/repartosController.php?action=get_repartos_entrega&id=${idVenta}`);
        const repartoViaje = await resp.json();
        let repartos = repartoViaje.data;
        console.log(repartoViaje);

        const tbody = document.getElementById('tbodyRepartos');
        tbody.innerHTML = '';

        if (!repartoViaje.success) return;
        // ================================
        // RENDER TABLA
        // ================================
        repartos.forEach(g => {

            const estadoClass =
                g.estatus_logistico === 'completado' ?
                    'bg-success' :
                    'bg-warning card-title-text';

            const tr = `
            <tr>
                <td class="fw-bold">
                    ${g.entrega_id}
                </td>
                <td class="fw-bold">
                    ${g.viaje_folio}
                </td>

                <td>
                    ${g.fecha}
                </td>

                <td>
                    <span >
                        ${g.direccion_entrega}
                    </span>
                </td>
<td class="text-center align-middle py-2">
    <div class="d-inline-flex gap-1 bg-light p-1 rounded-pill border border-translucent shadow-xs">
     <button type="button" 
                class="btn btn-xs btn-primary rounded-pill px-2.5 py-1 d-inline-flex align-items-center border-0 fw-semibold" 
                style="font-size: 0.75rem; transition: all 0.2s;"
                onclick="imprimirRuta('${g.entrega_id}','${g.folio}')">
            <i class="bi bi-truck me-1" style="font-size: 0.85rem;"></i>
            <span>Ver Despacho</span>
        </button>

        <button type="button" 
                class="btn btn-xs btn-dark rounded-pill px-2.5 py-1 d-inline-flex align-items-center border-0 fw-semibold" 
                style="font-size: 0.75rem; transition: all 0.2s;"
                onclick="abrirModalEvidencias(${g.entrega_id})">
            <i class="bi bi-images me-1" style="font-size: 0.85rem;"></i>
            <span>Evidencias</span>
        </button>
    </div>
</td>

            </tr>
        `;

            tbody.insertAdjacentHTML('beforeend', tr);
        });
    }
</script>