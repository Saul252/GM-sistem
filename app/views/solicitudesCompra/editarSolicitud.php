<!-- ═══════════════════════════════════════════════════════════
     MODAL EDITAR SOLICITUD  (prefijo único: edS_)
════════════════════════════════════════════════════════════ -->
<div class="modal fade" id="modalEditarSolicitud" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-custom">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <form id="edS_form">
                <input type="hidden" name="edS_solicitud_id" id="edS_solicitud_id">

                <!-- HEADER -->
                <div class="modal-header border-0 pt-4 px-4 align-items-center">
                    <div class="d-flex align-items-center">
                        <div class="bg-warning text-white rounded-3 p-2 me-3 shadow-sm">
                            <i class="bi bi-pencil-square fs-4"></i>
                        </div>
                        <div>
                            <h4 class="fw-bold mb-0">Editar Solicitud de Compra</h4>
                            <p class="text-body-secondary small mb-0">
                                Folio: <span id="edS_folio" class="fw-bold text-warning"></span>
                                &nbsp;·&nbsp;
                                <span id="edS_estado" class="badge bg-secondary">Cargando...</span>
                            </p>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body px-4">
                    <div class="row g-3 mb-4 p-4 rounded-4 shadow-sm align-items-end border">

                        <!-- Almacén de Cargo -->
                        <div class="col-md-6 col-lg-4 min-w-0">
                            <label for="edS_almacen_id"
                                class="form-label form-label-custom mb-1 text-uppercase tracking-wider">
                                <i class="bi bi-box-seam me-1 text-primary"></i> Almacén de Cargo
                            </label>
                            <select id="edS_almacen_id" name="edS_almacen_id" class="form-select border-slate-200"
                                required>
                                <option value="">Seleccionar ubicación...</option>
                                <?php foreach ($almacenes as $a): ?>
                                    <option value="<?= $a['id'] ?>"><?= htmlspecialchars($a['nombre']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Proveedor -->
                        <div class="col-md-6 col-lg-4 min-w-0">
                            <label for="edS_proveedor_id"
                                class="form-label form-label-custom mb-1 text-uppercase tracking-wider">
                                <i class="bi bi-truck me-1 text-primary"></i> Proveedor
                            </label>
                            <select id="edS_proveedor_id" name="edS_proveedor_id"
                                class="form-select select2-edS-modal border-slate-200" required>
                                <option value="">Seleccionar proveedor...</option>
                                <?php foreach ($proveedores as $p): ?>
                                    <option value="<?= $p['id'] ?>">
                                        <?= htmlspecialchars($p['nombre_comercial']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Ubicación / Sucursal -->
                        <div class="col-md-6 col-lg-4 min-w-0">
                            <label for="edS_ubicacion_proveedor_id"
                                class="form-label form-label-custom mb-1 text-uppercase tracking-wider">
                                <i class="bi bi-geo-alt me-1 text-primary"></i> Ubicación / Sucursal
                            </label>
                            <select id="edS_ubicacion_proveedor_id" name="edS_ubicacion_proveedor_id"
                                class="form-select select2-edS-modal border-slate-200">
                                <option value="">Seleccione un proveedor primero...</option>
                            </select>
                        </div>

                        <!-- Añadir Producto -->
                        <div class="col-md-12 col-lg-4 min-w-0">
                            <label for="edS_buscadorProductos"
                                class="form-label form-label-custom mb-1 text-uppercase tracking-wider">
                                <i class="bi bi-search me-1 text-primary"></i> Añadir Producto
                            </label>
                            <div class="input-group input-group-fixed flex-nowrap w-100">
                                <select id="edS_buscadorProductos"
                                    class="form-select select2-edS-modal border-slate-200">
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
                                    <i class="bi bi-plus-lg"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Tabla de Detalle -->
                    <div class="table-responsive border rounded-4">
                        <table class="table align-middle mb-0" id="edS_tablaDetalle">
                            <thead>
                                <tr class="text-body-secondary small uppercase">
                                    <th class="ps-4" style="width: 45%;">Producto</th>
                                    <th style="width: 15%;">Cantidad</th>
                                    <th style="width: 15%;">Presentación</th>
                                    <th style="width: 15%;">P. Unitario</th>
                                    <th style="width: 15%;">Precio</th>
                                    <th style="width: 5%;" class="text-end pe-4"></th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>

                        <div id="edS_emptyState" class="text-center py-5">
                            <div class="mb-3">
                                <i class="bi bi-cart-plus opacity-25" style="font-size: 3.5rem;"></i>
                            </div>
                            <p class="fw-medium">La lista está vacía</p>
                            <small>Utiliza el buscador de arriba para añadir artículos</small>
                        </div>
                    </div>

                    <!-- Resumen -->
                    <div class="d-flex justify-content-end align-items-center mt-4">
                        <div class="bg-dark bg-gradient text-white p-3 rounded-4 shadow-sm text-end px-4 min-w-200">
                            <small class="d-block text-white-50 fw-bold text-uppercase tracking-wider mb-1"
                                style="font-size: 0.65rem; letter-spacing: 0.8px;">
                                Costo Total de Compra
                            </small>
                            <div id="edS_costoTotalCompra" class="fw-bolder text-success"
                                style="font-size: 2rem; line-height: 1;">
                                $0.00
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-warning d-flex align-items-center gap-2 mt-3 mb-0" role="alert">
                        <i class="bi bi-info-circle-fill"></i>
                        <div class="small">
                            Los productos eliminados de la tabla se <b>borrarán</b> al guardar.
                            Los productos que ya tenían registro se <b>actualizarán</b>.
                        </div>
                    </div>
                </div>

                <!-- FOOTER -->
                <div class="modal-footer border-0 p-4 d-flex justify-content-between align-items-center">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4 fw-semibold"
                        data-bs-dismiss="modal">
                        Cancelar
                    </button>
                    <button type="submit"
                        class="btn btn-warning rounded-pill px-5 py-2.5 fw-bold shadow-sm d-flex align-items-center text-dark">
                        <i class="bi bi-save2 fs-5 me-2"></i> Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    /* ═══════════════════════════════════════════════════════════
   MODAL EDITAR SOLICITUD — Namespace edS_
═══════════════════════════════════════════════════════════ */
    (function () {
        'use strict';

        /* ── 1. Inicializar Select2 (una sola vez) ─────────────── */
        function edS_initSelect2() {
            if (!window.jQuery || !$.fn.select2) return;

            ['#edS_proveedor_id', '#edS_ubicacion_proveedor_id', '#edS_buscadorProductos']
                .forEach(sel => {
                    if (!$(sel).data('select2')) {
                        $(sel).select2({
                            theme: 'bootstrap-5',
                            dropdownParent: $('#modalEditarSolicitud'),
                            width: '100%'
                        });
                    }
                });
        }

        /* ── 2. Cargar ubicaciones del proveedor ───────────────── */
        function edS_cargarUbicaciones(proveedorId, ubicacionSeleccionada) {
            const sel = document.getElementById('edS_ubicacion_proveedor_id');
            if (!sel) return;

            sel.innerHTML = '<option value="">Cargando...</option>';
            if (window.jQuery && $(sel).data('select2')) {
                $(sel).trigger('change.select2');
            }

            if (!proveedorId) {
                sel.innerHTML = '<option value="">Seleccione un proveedor primero...</option>';
                if (window.jQuery && $(sel).data('select2')) $(sel).trigger('change.select2');
                return;
            }

            fetch(`/cfsistem/app/controllers/proveedoresController.php?action=listarDireccionesProveedor&proveedor_id=${proveedorId}`)
                .then(r => r.json())
                .then(data => {
                    sel.innerHTML = '<option value="0">Sin ubicación específica</option>';

                    if (data.success && data.data.length) {
                        data.data.forEach(u => {
                            const opt = document.createElement('option');
                            opt.value = u.id;
                            opt.textContent = `${u.nombre} - ${u.direccion_completa}` +
                                (u.telefono ? ` (Tel: ${u.telefono})` : '');
                            sel.appendChild(opt);
                        });
                    }

                    sel.value = String(ubicacionSeleccionada || 0);
                    if (window.jQuery && $(sel).data('select2')) $(sel).trigger('change.select2');
                })
                .catch(err => {
                    console.error('edS_cargarUbicaciones:', err);
                    sel.innerHTML = '<option value="0">Error al cargar</option>';
                    if (window.jQuery && $(sel).data('select2')) $(sel).trigger('change.select2');
                });
        }

        /* ── 3. Agregar fila a la tabla de edición ─────────────── */
        function edS_agregarFila(item, opts = { esNueva: false }) {
            const tbody = document.querySelector('#edS_tablaDetalle tbody');
            if (!tbody) return;

            const id = item.producto_id;
            if (document.getElementById(`edS-fila-${id}`)) {
                Swal.fire('Aviso', 'El producto ya está en la lista', 'info');
                return;
            }

            const detalleId = opts.esNueva ? 0 : (item.detalle_id || 0);
            const nombre = item.producto_nombre || item.nombre || '';
            const sku = item.sku || '';
            const cantidad = parseFloat(item.cantidad) || 1;
            const unidadReporte = item.unidad_reporte || 'Mayoreo';
            const precioUnit = parseFloat(item.precio_unitario) ||
                (parseFloat(item.costo) / (cantidad || 1)) || 0;
            const precioTotal = parseFloat(item.precio) || (cantidad * precioUnit);

            const tr = document.createElement('tr');
            tr.id = `edS-fila-${id}`;
            tr.innerHTML = `
            <input type="hidden" name="edS_items[${id}][detalle_id]" value="${detalleId}">
            <input type="hidden" name="edS_items[${id}][producto_id]" value="${id}">

            <td class="ps-4">
                <b>${nombre}</b><br>
                <small class="text-body-secondary">${sku}</small>
            </td>

            <td>
                <input type="number"
                    name="edS_items[${id}][cant]"
                    class="form-control edS-cantidad"
                    step="0.000001"
                    value="${cantidad}"
                    min="0.000001"
                    required
                    oninput="edS_recalcularTotal()">
            </td>

            <td>
                <select name="edS_items[${id}][unidad]" class="form-select edS-unidad"
                        onchange="edS_recalcularTotal()">
                    <option value="1" ${unidadReporte === 'Mayoreo' ? 'selected' : ''}>${unidadReporte}</option>
                </select>
            </td>

            <td>
                <input type="number"
                    name="edS_items[${id}][precioUnitario]"
                    class="form-control edS-precio-unitario"
                    step="0.0001"
                    min="0"
                    value="${precioUnit.toFixed(2)}"
                    required
                    oninput="edS_recalcularTotal()">
            </td>

            <td>
                <input type="number"
                    name="edS_items[${id}][precio]"
                    class="form-control edS-precio-total fw-bold text-success"
                    step="0.01"
                    min="0"
                    value="${precioTotal.toFixed(2)}"
                    oninput="edS_recalcularTotal()"
                    style="font-size:1.05rem;height:40px;">
            </td>

            <td class="text-end pe-4">
                <button type="button" class="btn btn-link text-danger"
                    onclick="edS_quitarFila(${id})">
                    <i class="bi bi-trash"></i>
                </button>
            </td>
        `;

            tbody.appendChild(tr);
            document.getElementById('edS_emptyState').classList.add('d-none');
        }

        /* ── 4. Quitar fila ────────────────────────────────────── */
        function edS_quitarFila(id) {
            const tr = document.getElementById(`edS-fila-${id}`);
            if (tr) tr.remove();

            if (!document.querySelector('#edS_tablaDetalle tbody tr')) {
                document.getElementById('edS_emptyState').classList.remove('d-none');
            }
            edS_recalcularTotal();
        }

        /* ── 5. Recalcular totales (scoped a #edS_tablaDetalle) ── */
        function edS_recalcularTotal() {
            const filas = document.querySelectorAll('#edS_tablaDetalle tbody tr');
            let totalGeneral = 0;

            filas.forEach(tr => {
                const cant = parseFloat(tr.querySelector('.edS-cantidad').value) || 0;
                const precioU = parseFloat(tr.querySelector('.edS-precio-unitario').value) || 0;
                const inputTotal = tr.querySelector('.edS-precio-total');

                const totalFila = cant * precioU;
                inputTotal.value = totalFila.toFixed(2);

                totalGeneral += totalFila;
            });

            const el = document.getElementById('edS_costoTotalCompra');
            if (el) {
                el.textContent = totalGeneral.toLocaleString('es-MX', {
                    style: 'currency', currency: 'MXN'
                });
            }
        }

        /* ── 6. Abrir modal y precargar ────────────────────────── */
        function edS_abrirModalEditar(id) {
            edS_initSelect2();

            Swal.fire({
                title: 'Cargando solicitud...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            fetch(`/cfsistem/app/controllers/solicitudesCompraController.php?action=obtenerDetalle&id=${id}`)
                .then(res => res.json())
                .then(res => {
                    Swal.close();

                    if (res.status !== 'success' || !res.data || !res.data.length) {
                        Swal.fire('Error', 'No se pudo cargar la solicitud', 'error');
                        return;
                    }

                    const data = res.data;
                    const base = data[0];

                    /* Cabecera */
                    document.getElementById('edS_solicitud_id').value = id;
                    document.getElementById('edS_folio').textContent =
                        `#${id.toString().padStart(5, '0')}`;

                    const badge = document.getElementById('edS_estado');
                    badge.textContent = base.estado_nombre || 'Borrador';
                    badge.className = 'badge ' + (base.estado_clase || 'bg-secondary');

                    /* Selects de cabecera */
                    document.getElementById('edS_almacen_id').value = base.almacen_id || '';

                    const selProv = document.getElementById('edS_proveedor_id');
                    selProv.value = base.proveedor_id || '';
                    if (window.jQuery && $(selProv).data('select2')) $(selProv).trigger('change.select2');

                    edS_cargarUbicaciones(base.proveedor_id, base.ubicacion_destino || 0);

                    /* Tabla */
                    const tbody = document.querySelector('#edS_tablaDetalle tbody');
                    tbody.innerHTML = '';
                    document.getElementById('edS_emptyState').classList.add('d-none');

                    data.forEach(item => edS_agregarFila(item, { esNueva: false }));

                    if (!data.length) {
                        document.getElementById('edS_emptyState').classList.remove('d-none');
                    }

                    /* Calcular total a partir de lo almacenado (no sobreescribir precios) */
                    const totalStored = data.reduce((acc, i) => acc + (parseFloat(i.precio) || 0), 0);
                    document.getElementById('edS_costoTotalCompra').textContent =
                        totalStored.toLocaleString('es-MX', { style: 'currency', currency: 'MXN' });

                    /* Mostrar modal */
                    bootstrap.Modal.getOrCreateInstance(
                        document.getElementById('modalEditarSolicitud')
                    ).show();
                })
                .catch(err => {
                    Swal.close();
                    console.error('edS_abrirModalEditar:', err);
                    Swal.fire('Error', 'No se pudo conectar con el servidor', 'error');
                });
        }

        /* ── 7. Handlers con namespaces (evitan duplicados) ────── */

        // Cambio de proveedor → recargar ubicaciones
        $(document).on('change.edS', '#edS_proveedor_id', function () {
            const provId = $(this).val();
            edS_cargarUbicaciones(provId || 0, 0);
        });

        // Seleccionar producto → agregar fila nueva
        $(document).on('select2:select.edS', '#edS_buscadorProductos', function (e) {
            const option = e.params.data.element;
            const data = option.dataset;

            edS_agregarFila({
                producto_id: option.value,
                producto_nombre: data.nombre,
                sku: data.sku,
                unidad_reporte: data.ur || 'Mayoreo',
                cantidad: 1,
                precio_unitario: 0,
                precio: 0
            }, { esNueva: true });

            $(this).val(null).trigger('change');
        });

        // Submit del form
        $(document).on('submit.edS', '#edS_form', async function (e) {
            e.preventDefault();

            if (!document.querySelector('#edS_tablaDetalle tbody tr')) {
                return Swal.fire('Atención', 'Agregue al menos un producto', 'warning');
            }

            const almacenId = document.getElementById('edS_almacen_id').value;
            if (!almacenId) {
                return Swal.fire('Atención', 'Seleccione un almacén de cargo', 'warning');
            }

            Swal.fire({
                title: 'Guardando cambios...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            try {
                const formData = new FormData(this);
                const resp = await fetch(`/cfsistem/app/controllers/solicitudesCompraController.php?action=actualizarSolicitud`, {
                    method: 'POST',
                    body: formData
                });

                const res = await resp.json();

                if (res.status === 'success') {
                    await Swal.fire({
                        icon: 'success',
                        title: '¡Actualizada!',
                        text: res.message || 'Los cambios se guardaron correctamente.',
                        showDenyButton: true,
                        confirmButtonText: 'IMPRIMIR',
                        denyButtonText: 'CERRAR',
                        confirmButtonColor: '#34c759',
                        denyButtonColor: '#5856d6'
                    }).then(r => {
                        bootstrap.Modal.getInstance(
                            document.getElementById('modalEditarSolicitud')
                        )?.hide();

                        if (r.isConfirmed) {
                            prepararImpresion(res.id || document.getElementById('edS_solicitud_id').value);
                        }
                        setTimeout(() => location.reload(), 400);
                    });
                } else {
                    Swal.fire('Error', res.message || 'No se pudo actualizar', 'error');
                }
            } catch (err) {
                console.error('edS_form submit:', err);
                Swal.fire('Error', 'Fallo de conexión', 'error');
            }
        });

        /* ── 8. Exponer al scope global ────────────────────────── */
        window.edS_abrirModalEditar = edS_abrirModalEditar;
        window.edS_quitarFila = edS_quitarFila;
        window.edS_recalcularTotal = edS_recalcularTotal;
        window.edS_cargarUbicaciones = edS_cargarUbicaciones;

        /* Inicializar Select2 cuando el DOM esté listo */
        $(document).ready(edS_initSelect2);
    })();
</script>