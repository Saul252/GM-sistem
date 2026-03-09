<?php 
if (function_exists('cargarEstilos')) { cargarEstilos(); } 
renderizarLayout($paginaActual);

$id_venta = intval($_GET['id'] ?? 0);
?>

<style>
    :root { --sidebar-width: 260px; --navbar-height: 65px; }
    body { background-color: #f4f7f6; }
    .main-content { margin-left: var(--sidebar-width); padding: 40px; padding-top: calc(var(--navbar-height) + 20px); transition: all 0.3s ease; }
    .card-edit { border: none; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); background: white; }
    .resumen-cabecera { background: #4e73df; color: white; border-radius: 12px 12px 0 0; padding: 20px; }
    .input-group-text { min-width: 85px; font-size: 0.75rem; justify-content: center; background-color: #f8f9fc; }
    @media (max-width: 768px) { .main-content { margin-left: 0; padding: 20px; padding-top: 90px; } }
</style>

<main class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold m-0 text-dark">Edición de Venta</h2>
            <p class="text-muted">Ajuste de unidades compuestas para Folio: <span id="folio_titulo" class="fw-bold text-primary">...</span></p>
        </div>
        <button class="btn btn-light rounded-pill shadow-sm" onclick="history.back()">
            <i class="bi bi-arrow-left"></i> Volver
        </button>
    </div>

    <div class="card card-edit">
        <div class="resumen-cabecera">
            <div class="row align-items-center">
                <div class="col-md-4">
                    <small class="text-uppercase opacity-75">Cliente</small>
                    <h5 id="lbl_cliente" class="fw-bold m-0">Cargando...</h5>
                </div>
                <div class="col-md-4 text-center">
                    <small class="text-uppercase opacity-75">Fecha</small>
                    <h5 id="lbl_fecha" class="m-0">--/--/----</h5>
                </div>
                <div class="col-md-4 text-md-end">
                    <small class="text-uppercase opacity-75">ID Interno</small>
                    <h5 class="m-0">#<?= $id_venta ?></h5>
                </div>
            </div>
        </div>

        <div class="card-body p-4">

            <form id="formEditarVenta">
                <div class="row mb-4">
    <div class="col-md-6">
        <div class="input-group">
            <span class="input-group-text bg-primary text-white"><i class="bi bi-search"></i></span>
            <input type="text" id="buscarProducto" class="form-control" placeholder="Buscar producto para agregar a esta venta...">
            <div id="resultadosBusqueda" class="list-group position-absolute w-100" style="top: 40px; z-index: 1000; display: none; box-shadow: 0 5px 15px rgba(0,0,0,0.1);"></div>
        </div>
    </div>
</div>
                <input type="hidden" id="edit_venta_id" value="<?= $id_venta ?>">
                <input type="hidden" id="edit_cliente_id"> 
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead class="text-muted small fw-bold text-uppercase">
                            <tr>
                                <th style="width: 30%;">Producto / Conversión</th>
                                <th class="text-center">Precio Unit.</th>
                                <th class="text-center">Cant. Original</th>
                                <th class="text-center text-success">Cantidad total solicitada</th>
                                <th class="text-center" style="width: 250px;">Agregar a entregar hoy</th>
                                <th class="text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody id="detallesEdicion">
                            </tbody>
                    </table>
                </div>

                <div class="row mt-4 justify-content-end">
                    <div class="col-md-4">
                        <div class="p-3 rounded-3 bg-light border">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Subtotal:</span>
                                <span class="fw-bold" id="txt_subtotal">$0.00</span>
                            </div>
                            <div class="d-flex justify-content-between text-primary">
                                <span class="h5 fw-bold">Total Final:</span>
                                <span class="h5 fw-bold" id="txt_total">$0.00</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="text-end mt-4">
                    <button type="button" class="btn btn-primary btn-lg rounded-pill px-5 shadow" onclick="enviarEdicion()">
                        <i class="bi bi-save me-2"></i> Actualizar Venta e Inventario
                    </button>
                </div>
            </form>
        </div>
    </div>
</main>

<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
/**
 * LÓGICA DE EDICIÓN DE VENTAS - "cfsistem"
 * - Validación de Stock para entregas.
 * - Manejo de unidades compuestas (Mayor/Menor).
 * - Sincronización de cálculos en tiempo real.
 */

const VENTA_ID = <?= $id_venta ?>;
const URL_API = 'editarVentaController.php';
let catalogoProductos = [];

$(document).ready(() => { 
    if(VENTA_ID > 0) cargarDatosVenta(); 

    // Cerrar buscador al hacer clic fuera
    $(document).on('click', (e) => {
        if (!$(e.target).closest('.input-group').length) $('#resultadosBusqueda').hide();
    });

    // Validar mínimos y stock al cambiar cualquier número
    $(document).on('change', 'input[type="number"]', function() {
        corregirAlMinimo(this);
    });
});

/**
 * 1. CARGA DE DATOS (PRODUCTOS GUARDADOS)
 */
function cargarDatosVenta() {
    $.get(URL_API, { action: 'obtenerDetalle', id: VENTA_ID }, function(res) {
        if(res.status === 'error') return Swal.fire('Error', res.message, 'error');

        $('#folio_titulo').text(res.info.folio);
        $('#lbl_cliente').text(res.info.nombre_comercial);
        $('#lbl_fecha').text(res.info.fecha);
        $('#edit_cliente_id').val(res.info.id_cliente);
        $('#formEditarVenta').data('almacen-id', res.info.almacen_id);
        
        cargarCatalogo(res.info.almacen_id);
        
        let html = '';
        res.productos.forEach(p => {
            let factor = parseFloat(p.factor_conversion) || 1;
            let m_vta = Math.floor(p.cantidad / factor);
            let s_vta = (p.cantidad % factor).toFixed(2).replace(/\.00$/, '');
            let entregado = parseFloat(p.cantidad_entregada) || 0;

            html += crearFilaProducto({
                id: p.id,
                prod_id: p.producto_id,
                nombre: p.producto,
                precio: p.precio_unitario,
                factor: factor,
                u_mayor: p.u_menor, // Normalización de tu BD
                u_menor: p.u_mayor, 
                entregado_prev: entregado, 
                cantidad_ref: p.cantidad,   
                m_vta: m_vta,
                s_vta: s_vta,
                stock: parseFloat(p.stock_actual) || 0, // Viene del JOIN con 'inventario'
                nuevo: false
            });
        });
        $('#detallesEdicion').html(html);
        recalculateAll();
    }, 'json');
}

/**
 * 2. FUNCIÓN DE LA FILA (EL HTML)
 */
function crearFilaProducto(d) {
    let cantTotalActual = (parseInt(d.m_vta) * parseFloat(d.factor)) + parseFloat(d.s_vta || 0);
    const sinStock = d.stock <= 0;
    
    return `
        <tr data-id="${d.id}" data-prod-id="${d.prod_id}" data-precio="${d.precio}" 
            data-factor="${d.factor}" data-entregado="${d.entregado_prev}" 
            data-stock="${d.stock}" class="${d.nuevo ? 'table-info' : ''}">
            <td>
                <div class="fw-bold text-dark">${d.nombre}</div>
                <div class="d-flex justify-content-between align-items-center">
                    <small class="text-primary">1 ${d.u_mayor} = ${d.factor} ${d.u_menor}</small>
                    <span class="badge ${sinStock ? 'bg-danger' : 'bg-light text-dark'} border">
                        Stock: ${d.stock} ${d.u_menor}
                    </span>
                </div>
            </td>
            <td class="text-center text-secondary">
                <div class="fw-bold">$${parseFloat(d.precio).toFixed(2)}</div>
            </td>

            <td class="text-center bg-light border-end">
                <span class="badge bg-secondary mb-1">${d.nuevo ? 'NUEVO' : d.cantidad_ref + ' ' + d.u_menor}</span>
                <div class="small text-success">Prev. Entregado: <b>${d.entregado_prev}</b></div>
            </td>
            
            <td>
                <div class="d-flex flex-column gap-1">
                    <div class="input-group input-group-sm">
                        <input type="number" class="form-control text-center input-vta-mayor" value="${d.m_vta}" min="0" oninput="sincronizar(this, 'vta')">
                        <span class="input-group-text" style="width:55px; font-size:0.65rem">${d.u_mayor}</span>
                    </div>
                    <div class="input-group input-group-sm">
                        <input type="number" class="form-control text-center input-vta-sueltas" value="${d.s_vta}" min="0" step="0.01" oninput="sincronizar(this, 'vta')">
                        <span class="input-group-text" style="width:55px; font-size:0.65rem">${d.u_menor}</span>
                    </div>
                </div>
                <input type="hidden" class="input-cantidad-total" value="${cantTotalActual.toFixed(2)}">
            </td>

            <td style="background-color: #f0fdf4;">
                <div class="d-flex flex-column gap-1">
                    <div class="input-group input-group-sm">
                        <input type="number" class="form-control border-success text-center input-ent-mayor" 
                               value="0" min="0" oninput="sincronizar(this, 'ent')" ${sinStock ? 'disabled' : ''}>
                        <span class="input-group-text bg-success text-white border-success" style="width:55px; font-size:0.65rem">${d.u_mayor}</span>
                    </div>
                    <div class="input-group input-group-sm">
                        <input type="number" class="form-control border-success text-center input-ent-sueltas" 
                               value="0" min="0" step="0.01" oninput="sincronizar(this, 'ent')" ${sinStock ? 'disabled' : ''}>
                        <span class="input-group-text bg-success text-white border-success" style="width:55px; font-size:0.65rem">${d.u_menor}</span>
                    </div>
                </div>
                <input type="hidden" class="input-entrega-hoy-total" value="0">
                ${sinStock ? '<div class="text-center text-danger fw-bold mt-1" style="font-size:0.6rem;">BAJO STOCK - NO ENTREGABLE</div>' : ''}
            </td>

            <td class="text-end fw-bold subtotal-fila">$0.00</td>
            <td class="text-center">
                ${d.entregado_prev <= 0 ? 
                    `<button type="button" class="btn btn-sm btn-outline-danger border-0" onclick="eliminarFila(this)"><i class="bi bi-trash"></i></button>` : 
                    `<i class="bi bi-lock-fill text-muted" title="Historial protegido"></i>`}
            </td>
        </tr>`;
}

/**
 * 3. LÓGICA DE SINCRONIZACIÓN Y VALIDACIÓN
 */
function sincronizar(el, tipo) {
    let tr = $(el).closest('tr');
    let factor = parseFloat(tr.data('factor')) || 1;
    let inMayor = tr.find(`.input-${tipo}-mayor`);
    let inSueltas = tr.find(`.input-${tipo}-sueltas`);
    let m = parseInt(inMayor.val()) || 0;
    let s = parseFloat(inSueltas.val()) || 0;

    // Normalizar sueltas a unidades mayores si exceden el factor
    if (s >= factor) {
        m += Math.floor(s / factor);
        s = s % factor;
        inMayor.val(m);
        inSueltas.val(s.toFixed(2).replace(/\.00$/, ''));
    }
    
    let totalCalculado = (m * factor) + s;
    if (tipo === 'vta') tr.find('.input-cantidad-total').val(totalCalculado.toFixed(2));
    else tr.find('.input-entrega-hoy-total').val(totalCalculado.toFixed(2));
    
    corregirAlMinimo(el);
}

function corregirAlMinimo(el) {
    let tr = $(el).closest('tr');
    let factor = parseFloat(tr.data('factor')) || 1;
    let stock = parseFloat(tr.data('stock')) || 0;
    let entregadoPrev = parseFloat(tr.data('entregado')) || 0;
    
    let vtaTotal = parseFloat(tr.find('.input-cantidad-total').val()) || 0;
    let entHoyTotal = parseFloat(tr.find('.input-entrega-hoy-total').val()) || 0;

    // VALIDACIÓN 1: No vender menos de lo que ya salió del almacén físicamente
    if (vtaTotal < entregadoPrev) {
        vtaTotal = entregadoPrev;
        actualizarInputs(tr, 'vta', vtaTotal, factor);
    }

    // VALIDACIÓN 2: BLOQUEO DE STOCK (Lo que pides para hoy no puede superar lo que hay en inventario)
    if (entHoyTotal > stock) {
        entHoyTotal = stock;
        actualizarInputs(tr, 'ent', entHoyTotal, factor);
        Swal.fire({
            icon: 'warning',
            title: 'Inventario insuficiente',
            text: `Solo hay ${stock} disponibles en stock. La venta se registrará, pero no se puede entregar el excedente hoy.`,
            toast: true,
            position: 'top-end',
            timer: 3500,
            showConfirmButton: false
        });
    }

    // VALIDACIÓN 3: No puedes entregar hoy más de lo que falta por completar de la venta
    let pendientePorEntregar = vtaTotal - entregadoPrev;
    if (entHoyTotal > pendientePorEntregar) {
        entHoyTotal = pendientePorEntregar;
        actualizarInputs(tr, 'ent', entHoyTotal, factor);
    }

    recalculateAll();
}

function actualizarInputs(tr, tipo, cantidad, factor) {
    let m = Math.floor(cantidad / factor);
    let s = (cantidad % factor).toFixed(2).replace(/\.00$/, '');
    tr.find(`.input-${tipo}-mayor`).val(m);
    tr.find(`.input-${tipo}-sueltas`).val(s);
    if (tipo === 'vta') tr.find('.input-cantidad-total').val(cantidad.toFixed(2));
    else tr.find('.input-entrega-hoy-total').val(cantidad.toFixed(2));
}

function recalculateAll() {
    let granTotal = 0;
    $('#detallesEdicion tr').each(function() {
        let tr = $(this);
        let vtaTotal = parseFloat(tr.find('.input-cantidad-total').val()) || 0;
        let precio = parseFloat(tr.data('precio')) || 0;
        let sub = precio * vtaTotal;
        tr.find('.subtotal-fila').text('$' + sub.toLocaleString('es-MX', {minimumFractionDigits: 2}));
        granTotal += sub;
    });
    $('#txt_subtotal, #txt_total').text('$' + granTotal.toLocaleString('es-MX', {minimumFractionDigits: 2}));
}

/**
 * 4. BUSCADOR Y CATÁLOGO
 */
function cargarCatalogo(almacenId) {
    $.get(URL_API, { action: 'obtenerProductos', almacen_id: almacenId }, function(res) {
        if(res.status === 'success') catalogoProductos = res.data;
    });
}

$('#buscarProducto').on('input', function() {
    let busqueda = $(this).val().toLowerCase();
    let resultados = $('#resultadosBusqueda');
    if (busqueda.length < 2) return resultados.hide();
    
    let filtrados = catalogoProductos.filter(p => 
        p.nombre.toLowerCase().includes(busqueda) || p.sku.toLowerCase().includes(busqueda)
    ).slice(0, 5);
    
    let html = '';
    filtrados.forEach(p => {
        html += `<a href="javascript:void(0)" class="list-group-item list-group-item-action" onclick='agregarFilaNueva(${JSON.stringify(p)})'>
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <strong>${p.nombre}</strong><br>
                    <small class="text-muted">Disponibilidad: ${p.stock}</small>
                </div>
                <span class="badge bg-primary">$${parseFloat(p.precio_minorista).toFixed(2)}</span>
            </div></a>`;
    });
    resultados.html(html).show();
});

function agregarFilaNueva(p) {
    $('#resultadosBusqueda').hide();
    $('#buscarProducto').val('');
    if ($(`#detallesEdicion tr[data-prod-id="${p.id}"]`).length > 0) return Swal.fire('Aviso', 'El producto ya está en la lista', 'info');

    let html = crearFilaProducto({
        id: 0,
        prod_id: p.id,
        nombre: p.nombre,
        precio: p.precio_minorista,
        factor: p.factor_conversion,
        u_mayor: p.unidad_reporte,
        u_menor: p.unidad_medida,
        entregado_prev: 0,
        cantidad_ref: 0,
        m_vta: 0,
        s_vta: 0,
        stock: p.stock, // Stock actual del catálogo
        nuevo: true
    });
    $('#detallesEdicion').append(html);
    recalculateAll();
}

function eliminarFila(btn) {
    $(btn).closest('tr').fadeOut(200, function() { $(this).remove(); recalculateAll(); });
}

/**
 * 5. GUARDADO FINAL
 */
function enviarEdicion() {
    const total = parseFloat($('#txt_total').text().replace(/[^0-9.-]+/g,""));
    const data = {
        venta_id: VENTA_ID,
        id_cliente: $('#edit_cliente_id').val(),
        nuevo_total: total,
        almacen_id: $('#formEditarVenta').data('almacen-id'),
        productos: []
    };
    
    $('#detallesEdicion tr').each(function() {
        data.productos.push({
            detalle_id: $(this).data('id'),
            producto_id: $(this).data('prod-id'),
            nueva_cantidad: $(this).find('.input-cantidad-total').val(),
            entrega_hoy: $(this).find('.input-entrega-hoy-total').val(),
            precio_unitario: $(this).data('precio')
        });
    });

    Swal.fire({
        title: '¿Guardar cambios?',
        text: "Se actualizará la venta y se generará la salida de stock correspondiente.",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#4e73df',
        confirmButtonText: 'Confirmar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: URL_API + '?action=guardarEdicion', 
                type: 'POST',
                data: JSON.stringify(data),
                contentType: 'application/json',
                success: (res) => {
                    if (res.status === 'success') {
                        Swal.fire('Éxito', 'Venta e inventario actualizados.', 'success').then(() => location.reload());
                    } else {
                        Swal.fire('Error', res.message, 'error');
                    }
                }
            });
        }
    });
}
</script>