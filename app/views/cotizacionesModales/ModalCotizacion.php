<div class="modal fade" id="modalCotizacion" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-custom">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <form id="formSolicitud">
                <!-- Encabezado -->
                <div class="modal-header  border-0 pt-4 px-4 align-items-center">
                    <div class="d-flex align-items-center">
                        <div class="bg-primary text-white rounded-3 p-2 me-3 shadow-sm">
                            <i class="bi bi-file-earmark-plus fs-4"></i>
                        </div>
                        <div>
                            <h4 class="fw-bold mb-0">Nueva Cotización</h4>
                            <p class="text-body-secondary small mb-0">Complete los datos para requerir materiales al
                                almacén</p>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body px-4">
                    <!-- Fila de Controles -->
                    <div class="row g-3 mb-4 p-4 rounded-4  shadow-sm align-items-end border">

                        <!-- 1. Almacén de Cargo -->
                        <div class="col-md-6 col-lg-3 min-w-0">
                            <label for="almacen_id"
                                class="form-label form-label-custom mb-1 text-uppercase tracking-wider">
                                <i class="bi bi-box-seam me-1 text-primary"></i> Almacén de Cargo
                            </label>
                            <select name="almacen_id" id="almacen_id"
                                class="form-select border-slate-200 control-fixed-height w-100 shadow-none" required>
                                <option value="">Seleccionar ubicación...</option>
                                <?php foreach ($almacenes as $a): ?>
                                    <option value="<?= $a['id'] ?>"><?= $a['nombre'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- 2. Cliente -->
                        <div class="col-md-6 col-lg-3 min-w-0">
                            <label for="cliente_id"
                                class="form-label form-label-custom mb-1 text-uppercase tracking-wider">
                                <i class="bi bi-person me-1 text-primary"></i> Cliente
                            </label>
                            <div class="input-group input-group-fixed flex-nowrap w-100">
                                <select name="cliente_id" id="cliente_id"
                                    class="form-select select2-modal border-slate-200" required>
                                    <option value="">Seleccionar cliente...</option>

                                </select>
                                <button
                                    class="btn btn-outline-primary px-3 d-flex align-items-center justify-content-center flex-shrink-0"
                                    type="button" onclick="abrirModalNuevoCliente()" title="Nuevo Cliente">
                                    <i class="bi bi-person-plus-fill"></i>
                                </button>
                            </div>
                        </div>

                        <!-- 3. Vendedor -->
                        <div class="col-md-6 col-lg-3 min-w-0">
                            <label for="vendedor-select"
                                class="form-label form-label-custom mb-1 text-uppercase tracking-wider">
                                <i class="bi bi-person-badge me-1 text-primary"></i> Vendedor
                            </label>
                            <select class="form-select select2-modal border-slate-200 w-100" id="vendedor-select"
                                name="usuario_id3" required>
                                <option value="">Seleccione vendedor</option>
                            </select>
                        </div>

                        <!-- 4. Añadir Producto -->
                        <div class="col-md-6 col-lg-3 min-w-0">
                            <label for="buscadorProductos"
                                class="form-label form-label-custom mb-1 text-uppercase tracking-wider">
                                <i class="bi bi-search me-1 text-primary"></i> Añadir Producto
                            </label>
                            <div class="input-group input-group-fixed flex-nowrap w-100">
                                <select id="buscadorProductos" class="form-select select2-modal border-slate-200">
                                    <option value="">Escribe SKU o nombre...</option>
                                    <?php foreach ($listaProductos as $pr): ?>
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
                                <button type="button"
                                    class="btn btn-primary d-flex align-items-center justify-content-center px-3 flex-shrink-0"
                                    onclick="abrirModalProducto()" title="Agregar nuevo producto">
                                    <i class="bi bi-plus-lg me-1"></i>
                                    <span class="fw-medium">Nuevo</span>
                                </button>
                            </div>
                        </div>

                    </div>

                    <!-- Tabla de Detalle -->
                    <div class="table-responsive border rounded-4 ">
                        <table class="table align-middle mb-0" id="tablaDetalle">
                            <thead class="">
                                <tr class="text-body-secondary small uppercase">
                                    <th class="ps-4" style="width: 45%;">Producto</th>
                                    <th style="width: 20%;">Cantidad</th>
                                    <th style="width: 25%;">Presentación / Unidad</th>
                                    <th style="width: 25%;">Tipo de precio</th>
                                    <th class="ps-4">Precio Unitario</th>
                                    <th style="width: 50%" ;>TOTAL</th>
                                    <th style="width: 10%;" class="text-end pe-4">Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>

                        </table>

                        <div id="emptyState" class="text-center py-5 text-body-secondary">
                            <div class="mb-3">
                                <i class="bi bi-cart-plus opacity-25" style="font-size: 3.5rem;"></i>
                            </div>
                            <p class="fw-medium">La lista está vacía</p>
                            <small>Utiliza el buscador de arriba para añadir artículos</small>
                        </div>
                    </div>

                    <!-- Resumen del Total -->
                    <div class="d-flex justify-content-end align-items-center mt-4">
                        <div class="bg-dark bg-gradient text-white p-3 rounded-4 shadow-sm text-end px-4 min-w-200">
                            <small class="d-block text-white-50 fw-bold text-uppercase tracking-wider mb-1"
                                style="font-size: 0.65rem; letter-spacing: 0.8px;">
                                Costo Total de Compra
                            </small>
                            <div id="costoTotalCompra" class="fw-bolder text-success"
                                style="font-size: 2rem; line-height: 1;">
                                $0.00
                            </div>
                            <input type="hidden" id="totalCotizacion" name="totalCotizacion">
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="modal-footer border-0 p-4  d-flex justify-content-between align-items-center">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4 fw-semibold"
                        data-bs-dismiss="modal">
                        Cancelar
                    </button>
                    <button type="submit"
                        class="btn btn-primary rounded-pill px-5 py-2.5 fw-bold shadow-sm d-flex align-items-center">
                        <i class="bi bi-check2-circle fs-5 me-2"></i> Confirmar Solicitud
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    /* Ampliación del Modal */
    @media (min-width: 1200px) {
        .modal-dialog-custom {
            max-width: 92% !important;
        }
    }

    :root {
        --control-height: 42px;
        --border-color: #dee2e6;
        --bg-input: #ffffff;
    }

    .min-w-0 {
        min-width: 0 !important;
    }

    .min-w-200 {
        min-width: 200px;
    }

    .form-label-custom {
        font-size: 0.72rem !important;
        font-weight: 600 !important;
        color: #6c757d !important;
        letter-spacing: 0.5px !important;
    }

    /* Forzar anchos de Input Group y Select2 */
    .input-group-fixed {
        width: 100% !important;
        max-width: 100% !important;
    }

    .input-group-fixed .select2-container {
        flex: 1 1 auto !important;
        width: 1% !important;
        min-width: 0 !important;
    }

    /* Control de altura universal */
    .control-fixed-height,
    .modal-body .form-select,
    .modal-body .input-group-fixed .btn {
        height: var(--control-height) !important;
    }

    /* Reglas estrictas Select2 para evitar desbordamientos */
    .modal-body .select2-container--bootstrap-5 .select2-selection,
    .modal-body .select2-container .select2-selection--single {
        height: var(--control-height) !important;
        background-color: var(--bg-input) !important;
        border-color: var(--border-color) !important;
        border-radius: 0.375rem !important;
        display: flex !important;
        align-items: center !important;
        font-size: 0.875rem !important;
        width: 100% !important;
        max-width: 100% !important;
    }

    .modal-body .select2-container .select2-selection--single .select2-selection__rendered {
        line-height: calc(var(--control-height) - 2px) !important;
        color: #212529 !important;
        padding-left: 0.75rem !important;
        padding-right: 1.75rem !important;
        white-space: nowrap !important;
        overflow: hidden !important;
        text-overflow: ellipsis !important;
        width: 100% !important;
    }

    /* Tabla Fija */
    .table-fixed {
        table-layout: fixed !important;
        width: 100% !important;
    }
</style>
<script>
    const URL_CONTROLADOR = '/cfsistem/app/controllers/cotizacionesController.php';

    /* =========================================================
       CARGAR CLIENTES (solo llena <option>, NO toca Select2)
       ========================================================= */
    async function cargarClientes() {
        console.log("cargo clientes");

        const almacenId = $('#almacen_id').val();
        const select = document.getElementById('cliente_id');
        if (!select) return;

        try {
            const url = '/cfsistem/app/controllers/accesoController.php?action=obtenerClientes';
            const respuesta = await fetch(url);

            if (!respuesta.ok) throw new Error('Error en la respuesta del servidor');

            const resultado = await respuesta.json();
            console.log(resultado);

            if (resultado.success && Array.isArray(resultado.data)) {

                // FILTRADO
                const clientesFiltrados = resultado.data.filter(cliente => {
                    const nombreNorm = String(cliente.nombre_comercial || '').toLowerCase().trim();
                    const esPublicoGeneral = nombreNorm.includes('publico en general') || nombreNorm.includes('público en general');

                    if (esPublicoGeneral) {
                        return cliente.almacen_id == almacenId;
                    }

                    return true;
                });

                // 🔥 Construir opciones con data-* forzados a string
                const fragment = document.createDocumentFragment();

                const first = document.createElement('option');
                first.value = '';
                first.textContent = '-- Seleccione un cliente --';
                fragment.appendChild(first);

                clientesFiltrados.forEach(cliente => {
                    const opcion = document.createElement('option');
                    opcion.value = cliente.id;
                    opcion.textContent = String(cliente.nombre_comercial || '');

                    opcion.dataset.nombre = String(cliente.nombre_comercial || '');
                    opcion.dataset.razonSocial = String(cliente.razon_social || '');
                    opcion.dataset.contacto = String(cliente.contacto || '');

                    fragment.appendChild(opcion);
                });

                select.replaceChildren(fragment);

                // 🔥 SOLO refrescar Select2 (NO destruir, NO re-inicializar)
                $(select).trigger('change.select2');

                // Autoselección del primer cliente
                if (clientesFiltrados.length > 0) {
                    const primerClienteId = clientesFiltrados[0].id;
                    $('#cliente_id').val(primerClienteId).trigger('change');
                } else {
                    $('#cliente_id').val(null).trigger('change');
                }

            } else {
                select.innerHTML = '<option value="">No se pudieron cargar los usuarios</option>';
                $(select).trigger('change.select2');
            }
        } catch (error) {
            select.innerHTML = '<option value="">Error al cargar la lista</option>';
            $(select).trigger('change.select2');
            console.error('Error al ejecutar cargarClientes:', error);
        }
    }

    /* =========================================================
       CARGAR VENDEDORES
       ========================================================= */
    async function cargarVendedores() {
        const select = document.getElementById('vendedor-select');
        if (!select) return;

        try {
            const url = '/cfsistem/app/controllers/accesoController.php?action=obtenerUsuarios';
            const respuesta = await fetch(url);

            if (!respuesta.ok) throw new Error('Error en la respuesta del servidor');

            const resultado = await respuesta.json();

            if (resultado.success && Array.isArray(resultado.data)) {
                select.innerHTML = '<option value="">Seleccione vendedor</option>';

                resultado.data.forEach(usuario => {
                    const opcion = document.createElement('option');
                    opcion.value = usuario.id;
                    opcion.textContent = String(usuario.nombre || '').toUpperCase();
                    select.appendChild(opcion);
                });
            } else {
                select.innerHTML = '<option value="">No se pudieron cargar los usuarios</option>';
            }

        } catch (error) {
            select.innerHTML = '<option value="">Error al cargar la lista</option>';
            console.error('Error al ejecutar cargarVendedores:', error);
        }
    }

    /* =========================================================
       CARGAR PRODUCTOS
       ========================================================= */
    async function recargarProductos() {
        try {
            const resp = await fetch('/cfsistem/app/controllers/cotizacionesController.php?action=obtenerProductos');
            const res = await resp.json();

            if (!res.success) throw new Error(res.message);

            const select = document.getElementById('buscadorProductos');

            const fragment = document.createDocumentFragment();
            fragment.appendChild(new Option('Escribe para buscar...', '', true, true));

            (res.data || []).forEach(pr => {
                const option = document.createElement('option');
                option.value = pr.producto_id;
                option.dataset.nombre = String(pr.nombre || '');
                option.dataset.medidas = JSON.stringify(pr.medidas_adicionales || []);
                option.dataset.sku = String(pr.sku || '');
                option.dataset.um = String(pr.unidad_medida || '');
                option.dataset.ur = String(pr.unidad_reporte || '');
                option.dataset.preMin = pr.precio_minorista || 0;
                option.dataset.preMat = pr.precio_mayorista || 0;
                option.dataset.preDis = pr.precio_distribuidor || 0;
                option.dataset.factor = pr.factor_conversion || 1;
                option.textContent = `[${pr.sku}] ${pr.nombre}`;
                fragment.appendChild(option);
            });

            select.replaceChildren(fragment);
            $('#buscadorProductos').trigger('change.select2');

        } catch (e) {
            console.error(e);
            Swal.fire('Error', 'No se pudo actualizar la lista de productos', 'error');
        }
    }

    /* =========================================================
       INICIALIZACIÓN ÚNICA
       ========================================================= */
    $(function () {
        // 1. Al abrir el modal, inicializar Select2 con templates y dropdownParent
        $('#modalCotizacion').on('shown.bs.modal', function () {

            // Destruir Select2 previo si existe (evita duplicados)
            ['#cliente_id', '#vendedor-select', '#buscadorProductos'].forEach(sel => {
                if ($(sel).hasClass('select2-hidden-accessible')) {
                    $(sel).select2('destroy');
                }
            });

            // 🔥 Cliente con templates + matcher + dropdownParent al modal
            $('#cliente_id').select2({
                theme: 'bootstrap-5',
                width: 'resolve',
                dropdownParent: $('#modalCotizacion'),
                matcher: function (params, data) {
                    if (!params.term || params.term.trim() === '') return data;

                    const term = String(params.term).toLowerCase().trim();
                    const text = String(data.text || '').toLowerCase();

                    const $el = $(data.element);
                    const razonSocial = String($el.data('razonSocial') || '').toLowerCase();
                    const contacto = String($el.data('contacto') || '').toLowerCase();

                    if (text.includes(term) || razonSocial.includes(term) || contacto.includes(term)) {
                        return data;
                    }

                    return null;
                },
                templateResult: function (state) {
                    if (!state.id) return state.text;

                    const $opt = $(state.element);
                    const nombre = String($opt.data('nombre') || state.text || '');
                    const razonSocial = String($opt.data('razonSocial') || '');
                    const contacto = String($opt.data('contacto') || '');

                    if (!razonSocial && !contacto) {
                        return $(`<span class="fw-semibold">${nombre.toUpperCase()}</span>`);
                    }

                    return $(`
                        <div class="d-flex flex-column">
                            <span class="fw-semibold">${nombre.toUpperCase()}</span>
                            <div class="d-flex justify-content-between align-items-center gap-2 mt-1">
                                <small class="text-truncate" style="max-width:65%;">${razonSocial.toUpperCase()}</small>
                                <small class="fw-semibold text-nowrap">${contacto ? `👤 ${contacto.toUpperCase()}` : ''}</small>
                            </div>
                        </div>
                    `);
                },
                templateSelection: function (state) {
                    if (!state.id) return state.text;
                    const nombre = String($(state.element).data('nombre') || state.text || '');
                    return nombre.toUpperCase();
                }
            });

            // Vendedor
            $('#vendedor-select').select2({
                theme: 'bootstrap-5',
                dropdownParent: $('#modalCotizacion')
            });

            // Buscador de productos
            $('#buscadorProductos').select2({
                theme: 'bootstrap-5',
                dropdownParent: $('#modalCotizacion')
            });
        });

        // 2. Cambio de almacén → recargar productos y clientes
        $('#almacen_id').on('change', function () {
            const almacenId = this.value;
            if (!almacenId) return;

            console.log(`Almacén cambiado a ID: ${almacenId} - ${this.options[this.selectedIndex].text}`);
            recargarProductos();
            cargarClientes();
        });

        // 3. Carga inicial de clientes (sin tocar Select2)
        cargarClientes();

        // 4. Producto seleccionado → agregar fila
        $('#buscadorProductos').on('select2:select', function (e) {
            const d = e.params.data.element.dataset;
            const id = $(this).val();

            if ($(`#fila-${id}`).length) {
                Swal.fire('Aviso', 'El producto ya está en la lista', 'info');
                return;
            }

            $('#emptyState').addClass('d-none');

            const medidas = JSON.parse(d.medidas || '[]');
            const opcionesUnidad = medidas.map(m =>
                `<option value="${m.id}" data-equivalencia="${m.equivalencia}" data-medida-id="${m.id}">${m.nombre}</option>`
            ).join('');

            const preMin = parseFloat(d.preMin) || 0;
            const preMat = parseFloat(d.preMat) || 0;
            const preDis = parseFloat(d.preDis) || 0;
            const factor = parseFloat(d.factor) || 1;

            $('#tablaDetalle tbody').append(`
        <tr id="fila-${id}">
            <td class="ps-4">
                <b>${d.nombre}</b><br>
                <small class="text-body-secondary">${d.sku}</small>
            </td>
            <td>
                <input type="number" name="items[${id}][cant]" class="form-control cantidad"
                    step="0.01" value="1" min="0.01" required oninput="calcularTotalSol(this)">
            </td>
            <td>
                <select name="items[${id}][unidad]" class="form-select unidad-select"
                    onchange="calcularPreciosugerido(this)">
                    <option value="0" data-equivalencia="0" data-medida-id="0">Seleccione</option>
                    ${opcionesUnidad}
                </select>
            </td>
            <td>
                <select name="items[${id}][tipoPrecio]" class="form-select tipoPrecio-select"
                    onchange="calcularPreciosugerido(this)">
                    <option value="seleccionar" data-precio="0">Seleccione</option>
                    <option value="minorista" data-precio="${preMin}">Min $${(preMin * factor).toFixed(2)} x ${d.ur}</option>
                    <option value="mayorista" data-precio="${preMat}">May $${(preMat * factor).toFixed(2)} x ${d.ur}</option>
                    <option value="distribuidor" data-precio="${preDis}">Dis $${(preDis * factor).toFixed(2)} x ${d.ur}</option>
                </select>
            </td>
            <td>
                <input type="number" lang="en-US" name="items[${id}][precioUnitario]"
                    class="form-control precio-unitario"
                    step="0.01" min="0" placeholder="0.00" required
                    onkeydown="if(event.key==='Enter'){ event.preventDefault(); $('#buscadorProductos').select2('open'); }"
                    oninput="calcularTotalSol(this)">
            </td>
            <td style="min-width:160px;">
                <input type="number" lang="en-US" name="items[${id}][precio]"
                    class="form-control precio-total fw-bold text-success"
                    step="0.01" min="0" placeholder="0.00"
                    onkeydown="if(event.key==='Enter'){ event.preventDefault(); $('#buscadorProductos').select2('open'); }"
                    oninput="calcularTotalSol(this)"
                    style="font-size:1.1rem; height:45px; min-width:140px;">
            </td>
            <td>
                <button type="button" class="btn btn-link text-danger" onclick="quitarFila(${id})">
                    <i class="bi bi-trash"></i>
                </button>
            </td>
        </tr>
    `);

            // 🔥 Enfocar y seleccionar el input de cantidad de la fila recién creada
            setTimeout(() => {
                const $fila = $(`#fila-${id}`);
                const $cantidad = $fila.find('.cantidad');

                if ($cantidad.length) {
                    const inputCantidad = $cantidad[0];
                    inputCantidad.focus();
                    inputCantidad.select();

                    $cantidad.css({
                        'box-shadow': '0 0 0 3px rgba(37, 99, 235, 0.25)',
                        'border-color': '#2563eb',
                        'transition': 'all 0.3s'
                    });

                    setTimeout(() => {
                        $cantidad.css({
                            'box-shadow': '',
                            'border-color': '',
                            'transition': 'all 0.3s'
                        });
                    }, 1500);
                }
            }, 30);

            $(this).val(null).trigger('change');
        });
        // 5. Submit
        $('#formSolicitud').on('submit', async function (e) {
            e.preventDefault();

            if (!$('#tablaDetalle tbody tr').length) {
                Swal.fire('Error', 'Agregue productos', 'warning');
                return;
            }

            const payload = {
                almacen_id: $('#almacen_id').val(),
                cliente_id: $('#cliente_id').val(),
                totalCotizacion: $('#totalCotizacion').val(),
                vendedor: $('#vendedor-select').val(),
                items: []
            };

            $('#tablaDetalle tbody tr').each(function () {
                const fila = $(this);
                const id = fila.attr('id').replace('fila-', '');

                const unidadSelect = fila.find('.unidad-select option:selected');
                const tipoPrecioSelect = fila.find('.tipoPrecio-select option:selected');

                payload.items.push({
                    producto_id: id,
                    cantidad: fila.find('.cantidad').val(),
                    unidad: unidadSelect.val(),
                    unidad_id: unidadSelect.data('medida-id'),
                    equivalencia: unidadSelect.data('equivalencia'),
                    tipoPrecio: tipoPrecioSelect.val(),
                    precioUnitario: fila.find('.precio-unitario').val(),
                    precio: fila.find('.precio-total').val()
                });
            });

            Swal.fire({ title: 'Guardando...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });

            try {
                const resp = await fetch(`${URL_CONTROLADOR}?action=guardar`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
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
                console.error(e);
                Swal.fire('Error', 'Fallo de conexión', 'error');
            }
        });
    });

    /* =========================================================
       CÁLCULOS
       ========================================================= */
    let recalculandoFila = false;

    function calcularTotalSol(input) {
        if (recalculandoFila) return;
        recalculandoFila = true;

        try {
            const fila = input.closest('tr');
            const cantidad = parseFloat(fila.querySelector('.cantidad').value) || 0;
            const precioUnitario = parseFloat(fila.querySelector('.precio-unitario').value) || 0;

            const precioTotal = cantidad * precioUnitario;
            fila.querySelector('.precio-total').value = precioTotal.toFixed(2);

            let totaLCompra = 0;
            document.querySelectorAll('.precio-total').forEach(el => {
                totaLCompra += parseFloat(el.value) || 0;
            });

            document.getElementById('costoTotalCompra').textContent = totaLCompra.toLocaleString('es-MX', {
                style: 'currency', currency: 'MXN'
            });
            document.getElementById('totalCotizacion').value = totaLCompra;

        } finally {
            recalculandoFila = false;
        }
    }

    function calcularPreciosugerido(select) {
        const fila = select.closest('tr');
        const inputPrecio = fila.querySelector('.precio-unitario');
        const inputtotal = fila.querySelector('.precio-total');
        const unidadSelect = fila.querySelector('.unidad-select');
        const tipoSelect = fila.querySelector('.tipoPrecio-select');

        const unidadOption = unidadSelect.options[unidadSelect.selectedIndex];
        const tipoOption = tipoSelect.options[tipoSelect.selectedIndex] ?? 0;

        const equivalencia = Number(unidadOption?.dataset.equivalencia || 1);
        const precioBase = Number(tipoOption?.dataset.precio || 0) ?? 0;
        const sugerido = equivalencia > 0 ? (precioBase / equivalencia) : precioBase;

        inputPrecio.value = sugerido.toFixed(2);
        inputtotal.value = sugerido.toFixed(2);

        let totaLCompra = 0;
        document.querySelectorAll('.precio-total').forEach(el => {
            totaLCompra += parseFloat(el.value) || 0;
        });

        document.getElementById('costoTotalCompra').textContent = totaLCompra.toLocaleString('es-MX', {
            style: 'currency', currency: 'MXN'
        });
        document.getElementById('totalCotizacion').value = totaLCompra;
    }

    /* =========================================================
       ELIMINAR FILA
       ========================================================= */
    function quitarFila(id) {
        $(`#fila-${id}`).remove();

        let totaLCompra = 0;
        document.querySelectorAll('.precio-total').forEach(el => {
            totaLCompra += parseFloat(el.value) || 0;
        });

        document.getElementById('costoTotalCompra').textContent = totaLCompra.toLocaleString('es-MX', {
            style: 'currency', currency: 'MXN'
        });
        document.getElementById('totalCotizacion').value = totaLCompra;

        if (!$('#tablaDetalle tbody tr').length) {
            $('#emptyState').removeClass('d-none');
        }
    }

    /* =========================================================
       NUEVA COTIZACIÓN
       ========================================================= */
    function nuevaCotizacion() {
        $('#formSolicitud')[0].reset();
        $('#tablaDetalle tbody').empty();
        $('#emptyState').removeClass('d-none');
        $('#costoTotalCompra').text('$0.00');
        $('#totalCotizacion').val('');

        $('#modalCotizacion').modal('show');

        // Cargar datos después de que el modal se abra
        $('#modalCotizacion').one('shown.bs.modal', function () {
            cargarClientes();
            recargarProductos();
            cargarVendedores();
        });
    }
</script>