<div class="modal fade" id="modalEditarCotizacion" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content"
            style="border-radius: 20px; border: none; overflow: hidden; box-shadow: 0 15px 50px rgba(0,0,0,0.2);">
            <form id="formEditarSolicitud">
                <input type="hidden" id="editar_cotizacion_id" name="cotizacion_id" value="">

                <div class="modal-header bg-white border-0 pt-4 px-4">
                    <div class="d-flex align-items-center">
                        <div class="bg-warning text-dark rounded-3 p-2 me-3 shadow-sm">
                            <i class="bi bi-pencil-square fs-4"></i>
                        </div>
                        <div>
                            <h4 class="fw-bold mb-0">Editar Cotizacion</h4>
                            <p class="text-muted small mb-0">Modifique los datos de la cotización existente</p>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body px-4">
                    <div
                        class="row g-3 mb-4 p-4 rounded-4 bg-white border border-light-subtle shadow-sm align-items-end">

                        <!-- 1. Almacén de Cargo -->
                        <div class="col-md-4 col-lg-3">
                            <label
                                class="form-label small fw-semibold text-secondary text-uppercase tracking-wider mb-2">
                                <i class="bi bi-box-seam me-1 text-primary"></i> Almacén de Cargo
                            </label>
                            <select name="almacen_id_editar" id="almacen_id_editar"
                                class="form-select border-light-subtle" required>
                                <option value="">Seleccionar ubicación...</option>
                                <?php foreach($almacenes as $a): ?>
                                <option value="<?= $a['id'] ?>"><?= $a['nombre'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- 2. Cliente -->
                        <div class="col-md-4 col-lg-3">
                            <label
                                class="form-label small fw-semibold text-secondary text-uppercase tracking-wider mb-2">
                                <i class="bi bi-person me-1 text-primary"></i> Cliente
                            </label>
                            <div class="input-group">
                                <select name="cliente_id_editar" id="cliente_id_editar"
                                    class="form-select select2-modal-editar border-light-subtle" required>
                                    <option value="">Seleccionar cliente...</option>
                                    <?php foreach($clientes as $p): ?>
                                    <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['nombre_comercial']) ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                                <button class="btn btn-outline-primary px-3" type="button"
                                    onclick="abrirModalNuevoCliente()" title="Nuevo Cliente">
                                    <i class="bi bi-person-plus-fill"></i>
                                </button>
                            </div>
                        </div>

                        <!-- 3. Vendedor -->
                        <div class="col-md-4 col-lg-3">
                            <label
                                class="form-label small fw-semibold text-secondary text-uppercase tracking-wider mb-2">
                                <i class="bi bi-person-badge me-1 text-primary"></i> Vendedor
                            </label>
                            <select name="select-vendedor1" id="select-vendedor1"
                                class="form-select select2-modal-editar border-light-subtle" required>
                                <option value="">Seleccionar vendedor...</option>
                            </select>
                        </div>

                        <!-- 4. Añadir Producto -->
                        <div class="col-12 col-lg-3">
                            <label
                                class="form-label small fw-semibold text-secondary text-uppercase tracking-wider mb-2">
                                <i class="bi bi-search me-1 text-primary"></i> Añadir Producto (SKU o Nombre)
                            </label>
                            <div class="input-group">
                                <select id="buscadorProductosEditar"
                                    class="form-select select2-modal-editar border-light-subtle">
                                    <option value="">Escribe para buscar...</option>
                                    <?php foreach($listaProductos as $pr): ?>
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
                                <button type="button" class="btn btn-primary d-flex align-items-center px-3"
                                    onclick="abrirModalProducto()" title="Agregar nuevo producto">
                                    <i class="bi bi-plus-lg me-1"></i>
                                    <span>Nuevo</span>
                                </button>
                            </div>
                        </div>

                    </div>

                    <div class="table-responsive border rounded-4 bg-white">
                        <table class="table align-middle mb-0" id="tablaDetalleEditar">
                            <thead class="bg-light">
                                <tr class="text-muted small uppercase">
                                    <th class="ps-4" style="width: 45%;">Producto</th>
                                    <th style="width: 20%;">Cantidad</th>
                                    <th style="width: 25%;">Presentación / Unidad</th>
                                    <th style="width: 25%;">Tipo de precio</th>
                                    <th class="ps-4">Precio Unitario</th>
                                    <th style="width: 50%;">TOTAL</th>
                                    <th style="width: 10%;" class="text-end pe-4">Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>

                        <div id="emptyStateEditar" class="text-center py-5 text-muted">
                            <div class="mb-3">
                                <i class="bi bi-cart-plus opacity-25" style="font-size: 3.5rem;"></i>
                            </div>
                            <p class="fw-medium">La lista está vacía</p>
                            <small>Utiliza el buscador de arriba para añadir artículos</small>
                        </div>
                    </div>

                    <div class="text-end mt-3">
                        <small class="d-block text-muted fw-semibold mb-1" style="letter-spacing:.5px;">
                            COSTO TOTAL DE COMPRA (EDICIÓN)
                        </small>
                        <div id="costoTotalCompraEditar" class="fw-bold text-success"
                            style="font-size:2rem; line-height:1;">
                            $0.00
                        </div>
                        <input type="hidden" id="totalCotizacionEditar" name="totalCotizacionEditar">
                    </div>
                </div>

                <div class="modal-footer border-0 p-4" id="modal-footer">
                    <button type="button" class="btn btn-link text-decoration-none text-muted fw-bold"
                        data-bs-dismiss="modal">
                        Cancelar
                    </button>

                    <button type="button" class="btn btn-success rounded-pill px-4 fw-bold shadow"
                        onclick="procederPago($('#totalCotizacionEditar').val(), $('#editar_cotizacion_id').val())">
                        <i class="bi bi-cart-check me-2"></i> Convertir a Venta
                    </button>

                    <button type="submit" class="btn btn-warning rounded-pill px-5 py-2 fw-bold shadow text-dark">
                        <i class="bi bi-check2-circle me-2"></i> Actualizar Cotización
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
const URL_CONTROLADOR_EDITAR = '/cfsistem/app/controllers/cotizacionesController.php';

// =====================================================
// SELECT2 EDITAR
// =====================================================
$('.select2-modal-editar').select2({
    theme: 'bootstrap-5',
    dropdownParent: $('#modalEditarCotizacion')
});

// =====================================================
// CALCULAR TOTAL EDITAR
// =====================================================
let recalculandoFilaEditar = false;

async function cargarVendedores3(vendedor_id) {
    const select = document.getElementById('select-vendedor1');
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
            $('#select-vendedor1').val(vendedor_id).trigger('change.select2');

        } else {
            select.innerHTML = '<option value="">No se pudieron cargar los usuarios</option>';
            console.error('El backend no devolvió success:true o la estructura cambió');
        }

    } catch (error) {
        select.innerHTML = '<option value="">Error al cargar la lista</option>';
        console.error('Error al ejecutar cargarUsuariosSelect:', error);
    }
}

function calcularTotalSolEditar(input) {
    if (recalculandoFilaEditar) return;
    recalculandoFilaEditar = true;

    try {
        const fila = input.closest('tr');
        const cantidad = parseFloat(fila.querySelector('.cantidad-editar').value) || 0;
        const precioUnitarioOriginal = parseFloat(fila.querySelector('.precio-unitario-editar').value) || 0;
        const selectUnidad = fila.querySelector('.unidad-select-editar');
        const equivalencia = parseFloat(selectUnidad?.selectedOptions[0]?.dataset?.equivalencia) || 0;

        const precioUnitarioAjustado = precioUnitarioOriginal;
        const precioTotal = cantidad * precioUnitarioAjustado;

        fila.querySelector('.precio-total-editar').value = precioTotal.toFixed(2);
        let id = document.getElementById('editar_cotizacion_id').value;
        console.log(id);

        // SUMA GENERAL
        let totalCompraEditar = 0;
        document.querySelectorAll('#tablaDetalleEditar .precio-total-editar').forEach(el => {
            totalCompraEditar += parseFloat(el.value) || 0;
        });

        document.getElementById('costoTotalCompraEditar').textContent = totalCompraEditar.toLocaleString('es-MX', {
            style: 'currency',
            currency: 'MXN'
        });
        document.getElementById('totalCotizacionEditar').value = totalCompraEditar;

    } finally {
        recalculandoFilaEditar = false;
    }
}

// =====================================================
// RECARGAR PRODUCTOS EDITAR
// =====================================================
async function recargarProductosEditar() {
    try {
        const resp = await fetch(`${URL_CONTROLADOR_EDITAR}?action=obtenerProductos`);
        const res = await resp.json();

        if (!res.success) {
            throw new Error(res.message);
        }

        const select = document.getElementById('buscadorProductosEditar');
        select.innerHTML = `<option value="">Escribe para buscar...</option>`;

        res.data.forEach(pr => {
            const option = document.createElement('option');
            option.value = pr.producto_id;
            option.dataset.nombre = pr.nombre;
            option.dataset.medidas = JSON.stringify(pr.medidas_adicionales || []);
            option.dataset.sku = pr.sku;
            option.dataset.um = pr.unidad_medida;
            option.dataset.ur = pr.unidad_reporte;
            option.dataset.preMin = pr.precio_minorista;
            option.dataset.preMat = pr.precio_mayorista;
            option.dataset.preDis = pr.precio_distribuidor;
            option.dataset.factor = pr.factor_conversion || 1;

            option.textContent = `[${pr.sku}] ${pr.nombre}`;
            select.appendChild(option);
        });

    } catch (e) {
        console.error(e);
        Swal.fire('Error', 'No se pudo actualizar la lista de productos', 'error');
    }
    $('#buscadorProductosEditar').trigger('change.select2');
}

// =====================================================
// EVENTO SELECT2: AGREGAR PRODUCTO A EDICIÓN
// =====================================================
$('#buscadorProductosEditar').on('select2:select', function(e) {
    const d = e.params.data.element.dataset;
    const id = $(this).val();

    // VALIDAR DUPLICADO EN TABLA DE EDICIÓN
    if ($(`#filaEditar-${id}`).length) {
        Swal.fire('Aviso', 'El producto ya está en la lista', 'info');
        return;
    }

    $('#emptyStateEditar').addClass('d-none');
    const medidas = JSON.parse(d.medidas || '[]');

    let opcionesUnidad = ``;
    medidas.forEach(m => {
        opcionesUnidad += `
        <option value="${m.id}" data-equivalencia="${m.equivalencia}" data-medida-id="${m.id}">
            ${m.nombre}
        </option>`;
    });

    // AGREGAR FILA A TABLA EDITAR
    $('#tablaDetalleEditar tbody').append(`
    <tr id="filaEditar-${id}">
        <td class="ps-4">
            <b>${d.nombre}</b><br>
            <small class="text-muted">${d.sku}</small>
        </td>

        <td>
            <input 
                type="number"
                name="itemsEditar[${id}][cant]"
                class="form-control cantidad-editar"
                step="0.01"
                value="0"
                min="0.01"
                required
                oninput="calcularTotalSolEditar(this)">
        </td>

        <td>
            <select 
                name="itemsEditar[${id}][unidad]" 
                class="form-select unidad-select-editar"
                onchange="calcularPrecioSugeridoEditar(this)">
                <option value="0" data-equivalencia="0" data-medida-id="0">Seleccione</option>
                ${opcionesUnidad}
            </select>
        </td>
        
        <td>
            <select 
                name="itemsEditar[${id}][tipoPrecio]" 
                class="form-select tipoPrecio-select-editar"
                onchange="calcularPrecioSugeridoEditar(this)">
                <option value="seleccionar" data-precio="0">seleccione</option>
                <option value="minorista" data-precio="${d.preMin}">Min ${d.preMin * d.factor} x ${d.ur}</option>
                <option value="mayorista" data-precio="${d.preMat}">May ${d.preMat * d.factor} x ${d.ur}</option>
                <option value="distribuidor" data-precio="${d.preDis}">Dis ${d.preDis * d.factor} x ${d.ur}</option>
            </select>
        </td>

        <td>
            <input 
                type="number"
                lang="en-US"
                name="itemsEditar[${id}][precioUnitario]"
                class="form-control precio-unitario-editar"
                step="0.01"
                min="0"
                placeholder="0.00"
                required
                oninput="calcularTotalSolEditar(this)"
            >
        </td>

        <td style="min-width:160px;">
            <input 
                type="number"
                lang="en-US"
                name="itemsEditar[${id}][precio]"
                class="form-control precio-total-editar fw-bold text-success bg-light"
                step="0.01"
                min="0"
                placeholder="0.00"
                oninput="calcularTotalSolEditar(this)"
                style="font-size:1.1rem; height:45px; min-width:140px;"
            >
        </td>

        <td>
            <button type="button" class="btn btn-link text-danger" onclick="quitarFilaEditar(${id})">
                <i class="bi bi-trash"></i>
            </button>
        </td>
    </tr>
    `);

    $(this).val(null).trigger('change');
});

// =====================================================
// CALCULAR PRECIO SUGERIDO EDITAR
// =====================================================
function calcularPrecioSugeridoEditar(select) {
    const fila = select.closest('tr');
    const inputPrecio = fila.querySelector('.precio-unitario-editar');
    const unidadSelect = fila.querySelector('.unidad-select-editar');
    const tipoSelect = fila.querySelector('.tipoPrecio-select-editar');
    const inputtotal = fila.querySelector('.precio-total-editar');
    const unidadOption = unidadSelect.options[unidadSelect.selectedIndex];
    const tipoOption = tipoSelect.options[tipoSelect.selectedIndex];

    const equivalencia = Number(unidadOption?.dataset.equivalencia || 1);
    const precioBase = Number(tipoOption?.dataset.precio || 0);

    const sugerido = precioBase / equivalencia;
    inputPrecio.value = sugerido.toFixed(2);
    inputtotal.value = sugerido.toFixed(2);
    let totalCompraEditar = 0;
    document.querySelectorAll('#tablaDetalleEditar .precio-total-editar').forEach(el => {
        totalCompraEditar += parseFloat(el.value) || 0;
    });

    document.getElementById('costoTotalCompraEditar').textContent = totalCompraEditar.toLocaleString('es-MX', {
        style: 'currency',
        currency: 'MXN'
    });
    document.getElementById('totalCotizacionEditar').value = totalCompraEditar;

}

document.addEventListener('input', function(e) {
    if (e.target.classList.contains('precio-unitario-editar')) {
        e.target.dataset.editado = "1";
    }
});

// =====================================================
// GUARDAR ACTUALIZACIÓN (SUBMIT FORM)
// =====================================================
$('#formEditarSolicitud').on('submit', async function(e) {
    e.preventDefault();

    if (!$('#tablaDetalleEditar tbody tr').length) {
        Swal.fire('Error', 'Agregue productos', 'warning');
        return;
    }

    const payload = {
        cotizacion_id: $('#editar_cotizacion_id').val(), // ID de la fila a actualizar
        almacen_id: $('#almacen_id_editar').val(),
        cliente_id: $('#cliente_id_editar').val(),
        vendedor: $('#select-vendedor1').val(),
        totalCotizacion: $('#totalCotizacionEditar').val(),
        items: []
    };

    $('#tablaDetalleEditar tbody tr').each(function() {
        const fila = $(this);
        const id = fila.attr('id').replace('filaEditar-', '');

        const unidadSelect = fila.find('.unidad-select-editar option:selected');
        const tipoPrecioSelect = fila.find('.tipoPrecio-select-editar option:selected');

        payload.items.push({
            producto_id: id,
            cantidad: fila.find('.cantidad-editar').val(),
            unidad: unidadSelect.val(),
            unidad_id: unidadSelect.data('medida-id'),
            equivalencia: unidadSelect.data('equivalencia'),
            tipoPrecio: tipoPrecioSelect.val(),
            precioUnitario: fila.find('.precio-unitario-editar').val(),
            precio: fila.find('.precio-total-editar').val()
        });
    });

    console.log('JSON ENVIADO EDICIÓN:', payload);

    Swal.fire({
        title: 'Actualizando...',
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading()
    });

    try {
        // Se envía a la acción de actualizar/editar en tu controlador
        const resp = await fetch(`${URL_CONTROLADOR_EDITAR}?action=actualizar`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(payload)
        });

        const res = await resp.json();

        if (res.status === 'success') {
            await Swal.fire({
                icon: 'success',
                title: '¡Actualizado!',
                text: res.message,
                timer: 1500,
                showConfirmButton: false
            });
            location.reload();
        } else {
            Swal.fire('Error', res.message, 'error');
        }
    } catch (e) {
        console.error(e);
        Swal.fire('Error', 'Fallo de conexión', 'error');
    }
});

// =====================================================
// ELIMINAR FILA EDITAR
// =====================================================
function quitarFilaEditar(id) {
    $(`#filaEditar-${id}`).remove();

    if (!$('#tablaDetalleEditar tbody tr').length) {
        $('#emptyStateEditar').removeClass('d-none');
    }

    // Forzar el recalculo global tras eliminar fila
    let totalCompraEditar = 0;
    document.querySelectorAll('#tablaDetalleEditar .precio-total-editar').forEach(el => {
        totalCompraEditar += parseFloat(el.value) || 0;
    });
    document.getElementById('costoTotalCompraEditar').textContent = totalCompraEditar.toLocaleString('es-MX', {
        style: 'currency',
        currency: 'MXN'
    });
    document.getElementById('totalCotizacionEditar').value = totalCompraEditar;
}

// =====================================================
// INICIALIZAR MODAL EDITAR CON DATOS DE LA DB
// ========

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
}

async function procederPago(total, id) {
    cargarUsuariosSelectPago();
    let nuevo = [];

    totalGlobalPago = total;

    const resp = await fetch(`${URL_CONTROLADOR}?action=obtenerDetalle&id=${id}`);
    const datos = await resp.json();

    let data = datos.data;

    let html = '';

    data.forEach((i, index) => {

        const cantidad = parseFloat(i.cantidad) || 0;
        canti = cantidad / i.equivalencia;
        data[index].cantidadR = parseFloat(canti);
        data[index].entrega_hoy = 0;
        document.getElementById('montoPago').value = data[index].total;
        datost = data;



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

        input.addEventListener('input', function() {

            const index = this.dataset.index;



            data[index].entrega_hoy = parseFloat((this.value * (1 / data[index].equivalencia))) ||
            0;
            data[index].monto_pagado = parseFloat(
                document.getElementById('montoPago').value
            ) || 0;

            datost = data;
            console.log("datos", datost);
            console.log(data);
        });

    });

    // actualizar monto pagado en TODO el arreglo
    document.getElementById('montoPago').addEventListener('input', function() {

        const monto = parseFloat(this.value) || 0;

        data.forEach(item => {
            item.monto_pagado = monto;
        });
        datos = data;

        console.log(datost);
    });




    const htmlboton = `<button class="btn btn-light w-50 rounded-3" data-bs-dismiss="modal">
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
    if ($(this).find(':selected').data('metodo') === 'credito') {
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
async function convertirToCompra(data, id) {
    try {
        console.log('data 1', datost);

        const payload = {
            accion: 'guardar_venta',
            data: datost,
            monto_pagado: parseFloat($('#montoPago').val()) || 0,
            metodo_pago: $('#metodoPago').val() || 'Efectivo',
            referencia: $('#referenciaPago').val() || '',
            vendedor: $('#select-vendedor1').val() || 1,
            idCotizacion: id,
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
</script>