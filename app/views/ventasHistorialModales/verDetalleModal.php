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

    /* SweetAlert2 */
    .swal2-container,
    .swal2-container.swal2-backdrop-show,
    .swal2-toast-shown .swal2-container {
        z-index: 99999 !important;
    }

    .swal2-popup {
        z-index: 100000 !important;
        position: relative;
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

                        <div id="contenedorBoton">
                            <button id="btnHabilitar" class="btn btn-action w-100 mb-2 py-2 fw-bold" onclick="abrirModalDespachoVentaTotal(
            $('#Almacen_id').text(),
            $('#IdFolio').text()
        )">
                                Nueva Entrega
                            </button>
                        </div>

                        <div class="text-end pe-3"></div>
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
                                        <tbody id="tbodyRepartos" class="small"></tbody>
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
    // ═══════════════════════════════════════════════════════════════
    // HELPERS DE CONVERSIÓN
    // ═══════════════════════════════════════════════════════════════
    // Regla:
    //   factor_conversion = cuántas unidades base hay en 1 unidad de reporte
    //   (ej: 1000 kg = 1 tonelada  →  factor = 1000)
    //
    //   base → reporte:  cantidadBase / factor
    //   reporte → base:  cantidadReporte * factor
    // ═══════════════════════════════════════════════════════════════

    function factorProducto(p) {
        const f = parseFloat(p.factor_conversion);
        return (isFinite(f) && f > 0) ? f : 1;
    }

    /**
     * Formatea una cantidad en unidad base para mostrarla
     * en su unidad de reporte si aplica, o en su unidad base si no.
     * Devuelve { cantidad, unidad } ya redondeados.
     */
    function formatearCantidad(cantidadBase, p) {
        const cantBase = parseFloat(cantidadBase) || 0;
        const factor = factorProducto(p);
        const uBase = p.unidad_medida || '';
        const uRep = p.unidad_reporte || uBase;

        // Si no hay conversión real (factor <= 1), quedarse en unidad base
        if (factor <= 1) {
            return { cantidad: cantBase, unidad: uBase };
        }

        // Si es >= 1 unidad de reporte, mostrar en unidad de reporte
        const enReporte = cantBase / factor;
        if (enReporte >= 1) {
            return { cantidad: enReporte, unidad: uRep };
        }

        // Si es menor a 1 unidad de reporte, mostrar en unidad base
        return { cantidad: cantBase, unidad: uBase };
    }

    function fmtNum(n) {
        const v = parseFloat(n) || 0;
        // Enteros sin decimales, con decimales a 3 posiciones máximo
        return Number.isInteger(v) ? v.toString() : v.toFixed(3).replace(/\.?0+$/, '');
    }

    // ═══════════════════════════════════════════════════════════════
    // VER DETALLE
    // ═══════════════════════════════════════════════════════════════
    async function verDetalle(id) {
        try {
            const respIds = await fetch(
                `/cfsistem/app/controllers/entregasController.php?ajax=get_ids_pendientes_venta&venta_id=${id}`
            );
            const resNAlmacen = await fetch(
                `/cfsistem/app/controllers/entregasController.php?ajax=obtener_id_almacen&id=${id}`
            );

            const dataAlmacen = await resNAlmacen.json();
            const almacen_id_conseguido = dataAlmacen.almacen.almacen_id;
            console.log('almacen_id:', almacen_id_conseguido);

            const dataIds = await respIds.json();
            console.log('ids pendientes:', dataIds.ids);

            // Habilitar/deshabilitar botón según pendientes
            if (!(Array.isArray(dataIds.ids) && dataIds.ids.length > 0)) {
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
                $('#detSaldoLabel').text('$' + deuda.toFixed(2))
                    .removeClass('text-success').addClass('text-danger');
                $('#btnAbonar').removeClass('d-none');
            }

            // ─────────────────────────────────────────────
            // RENDERIZADO DE PRODUCTOS (CONVERSIÓN CORREGIDA)
            // ─────────────────────────────────────────────
            $('#tbodyDetalle').html(data.productos.map(p => {
                const factor = factorProducto(p);
                const cant = parseFloat(p.cantidad) || 0;              // en unidad base
                const entregada = parseFloat(p.cantidad_entregada) || 0;    // en unidad base
                const disponible = parseFloat(p.disponible) || 0;           // en unidad base
                const pendiente = Math.max(cant - entregada, 0);            // en unidad base

                const uBase = p.unidad_medida || '';
                const uRep = p.unidad_reporte || uBase;

                // ── Columna VENTA ──
                // Mostrar en unidad de reporte si hay factor y la cantidad
                // alcanza para al menos 1 unidad de reporte.
                let ventaHtml;
                if (factor > 1 && cant >= factor) {
                    const cantRep = cant / factor;
                    ventaHtml = `
                        <span class="fw-bold">${fmtNum(cantRep)} ${uRep}</span><br>
                        <small class="text-body-secondary">(${fmtNum(cant)} ${uBase})</small>`;
                } else {
                    ventaHtml = `<span>${fmtNum(cant)} ${uBase}</span>`;
                }

                // Leyenda de equivalencia debajo del producto
                const leyendaEq = (factor > 1)
                    ? `<div class="text-body-secondary small" style="font-size:0.65rem;">
                         1 ${uRep} = ${fmtNum(factor)} ${uBase}
                       </div>`
                    : '';

                // ── Columna SURTIDO ──
                const surtidoFmt = formatearCantidad(entregada, p);
                const surtidoHtml = (entregada > 0)
                    ? `<span>${fmtNum(surtidoFmt.cantidad)} ${surtidoFmt.unidad}</span>`
                    : '<span class="text-body-secondary">—</span>';

                // ── Columna FALTA ──
                const faltaFmt = formatearCantidad(pendiente, p);
                const faltaHtml = (pendiente > 0)
                    ? `<span class="fw-bold">${fmtNum(faltaFmt.cantidad)} ${faltaFmt.unidad}</span>`
                    : '<span class="text-success fw-bold">Completo</span>';

                // ── Input de entrega ──
                // Máximo en unidad de reporte = min(pendiente, disponible) / factor
                // Máximo en unidad base     = min(pendiente, disponible)
                const topeBase = Math.min(pendiente, disponible);
                const topeRep = topeBase / factor;

                // Si hay stock suficiente para cubrir el pendiente completo en
                // unidad de reporte, usamos unidad de reporte; si no, unidad base.
                const usarReporte = (factor > 1 && topeRep >= 1);
                const maxInput = usarReporte ? topeRep : topeBase;
                const unidadInput = usarReporte ? uRep : uBase;
                const stepInput = usarReporte ? '0.001' : '0.001';

                let inputHtml;
                if (pendiente > 0) {
                    inputHtml = `
                        <input type="number"
                               class="form-control form-control-sm input-entrega2 mx-auto"
                               max="${maxInput}"
                               min="0"
                               step="${stepInput}"
                               value="0.000"
                               data-dvid="${p.dvid}"
                               data-id="${p.producto_id}"
                               data-factor="${usarReporte ? factor : 1}"
                               data-unidad="${unidadInput}"
                               style="width:80px">
                        <input type="hidden"
                               class="input-entrega0"
                               value="0"
                               data-dvid="${p.dvid}"
                               data-id="${p.producto_id}">
                        <span class="badge bg-success mt-1">${unidadInput}</span>`;
                } else {
                    inputHtml = '<span class="badge bg-success">Completo</span>';
                }

                return `<tr>
                    <td>
                        <div class="fw-bold card-title-text">${p.producto}</div>
                        ${leyendaEq}
                    </td>
                    <td class="text-center">${ventaHtml}</td>
                    <td class="text-center">${surtidoHtml}</td>
                    <td class="text-center text-danger">${faltaHtml}</td>
                    <td class="text-center col-input d-none">${inputHtml}</td>
                </tr>`;
            }).join(''));

            // ─────────────────────────────────────────────
            // HISTORIAL DE ENTREGAS (CONVERSIÓN CORREGIDA)
            // ─────────────────────────────────────────────
            $('#tbodyHistorial').html(
                data.historial.length > 0
                    ? data.historial.map(h => {
                        const cantH = parseFloat(h.cantidad) || 0;
                        const fmt = formatearCantidad(cantH, h);

                        return `<tr>
                            <td class="small">${h.fecha}</td>
                            <td class="small">${h.usuario_nombre}</td>
                            <td>
                                <div class="fw-bold" style="font-size:0.85rem;">${h.producto}</div>
                            </td>
                            <td class="text-center">${fmtNum(fmt.cantidad)} ${fmt.unidad}</td>
                        </tr>`;
                    }).join('')
                    : '<tr><td colspan="4" class="text-center text-body-secondary p-3">No hay entregas registradas</td></tr>'
            );

            // ─────────────────────────────────────────────
            // HISTORIAL DE PAGOS
            // ─────────────────────────────────────────────
            if (data.pagos && data.pagos.length > 0) {
                $('#tbodyPagos').html(data.pagos.map(p => `
                    <tr>
                        <td class="small">${p.fecha}</td>
                        <td class="fw-bold text-success">$${parseFloat(p.monto).toFixed(2)}</td>
                        <td>
                            <span class="badge bg-light text-dark border fw-normal">${p.metodo_pago}</span>
                            <div class="text-body-secondary" style="font-size:0.65rem">
                                Recibió: ${p.usuario_nombre}
                            </div>
                        </td>
                        <td><span>${p.referencia ?? ''}</span></td>
                    </tr>`).join(''));
            } else {
                $('#tbodyPagos').html(
                    '<tr><td colspan="4" class="text-center text-body-secondary p-3">No hay abonos registrados</td></tr>'
                );
            }

            alternarModo(false);
            modalObj.show();
        } catch (error) {
            console.error('Error al obtener detalle:', error);
        }
    }

    // ═══════════════════════════════════════════════════════════════
    // REPARTOS
    // ═══════════════════════════════════════════════════════════════
    async function cargarRepartos(idVenta) {
        const resp = await fetch(
            `/cfsistem/app/controllers/repartosController.php?action=get_repartos_entrega&id=${idVenta}`
        );
        const repartoViaje = await resp.json();
        const repartos = repartoViaje.data;

        const tbody = document.getElementById('tbodyRepartos');
        tbody.innerHTML = '';

        if (!repartoViaje.success) return;

        repartos.forEach(g => {
            const tr = `
                <tr>
                    <td class="fw-bold">${g.entrega_id}</td>
                    <td class="fw-bold">${g.viaje_folio}</td>
                    <td>${g.fecha}</td>
                    <td><span>${g.direccion_entrega}</span></td>
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
                </tr>`;
            tbody.insertAdjacentHTML('beforeend', tr);
        });
    }
</script>