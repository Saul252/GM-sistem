<!-- =========================================================
     MODAL TRASPASO MASIVO (independiente del #modalTraspaso)
     ========================================================= -->
<div class="modal fade" id="modalTraspasoMasivo" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title">
                    <i class="bi bi-boxes me-1"></i> Traspaso Masivo
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <!-- Cabecera fija -->
                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label class="form-label fw-bold">1. Almacén de Origen</label>
                        <select id="tm_origen_id" class="form-select border-primary" required>
                            <option value="">Seleccione donde sale la mercancía...</option>
                            <?php foreach ($almacenes as $alm): ?>
                                <option value="<?= $alm['id'] ?>"><?= htmlspecialchars($alm['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold">2. Almacén Destino</label>
                        <select id="tm_destino_id" class="form-select" required>
                            <option value="">¿A dónde va la mercancía?</option>
                            <?php foreach ($todosLosAlmacenes as $alm_dest): ?>
                                <?php if ($almacen_usuario > 0 && $alm_dest['id'] == $almacen_usuario)
                                    continue; ?>
                                <option value="<?= $alm_dest['id'] ?>"><?= htmlspecialchars($alm_dest['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold">3. Observaciones</label>
                        <textarea id="tm_observaciones" class="form-control" rows="1"
                            placeholder="Notas del traspaso..."></textarea>
                    </div>
                </div>

                <!-- Buscador de producto -->
                <div class="mb-3">
                    <label class="form-label fw-bold">Agregar producto</label>
                    <select id="tm_buscador_producto" class="form-select" style="width:100%;">
                        <option value="">Primero seleccione un origen...</option>
                    </select>
                </div>

                <!-- Tabla carrito -->
                <div class="table-responsive">
                    <table class="table table-sm table-hover align-middle mb-0" id="tm_tabla_carrito">
                        <thead class="table-light">
                            <tr>
                                <th style="width:28%;">Producto</th>
                                <th style="width:12%;">Stock</th>
                                <th style="width:12%;">Cantidad</th>
                                <th style="width:18%;">Unidad</th>
                                <th style="width:18%;">Lote</th>
                                <th style="width:12%;" class="text-center">Acción</th>
                            </tr>
                        </thead>
                        <tbody id="tm_tbody_carrito"></tbody>
                    </table>
                </div>

                <div id="tm_empty_state" class="text-center text-body-secondary small py-4">
                    <i class="bi bi-box fs-3 d-block mb-1 opacity-50"></i>
                    No hay productos en la lista. Busca uno arriba para agregarlo.
                </div>

            </div>

            <div class="modal-footer d-flex justify-content-between">
                <div class="small text-body-secondary">
                    <i class="bi bi-list-check me-1"></i>
                    <span id="tm_contador_carrito">0 productos</span>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-secondary" id="tm_btn_limpiar">
                        <i class="bi bi-eraser"></i> Limpiar
                    </button>
                    <button type="button" class="btn btn-primary" id="tm_btn_enviar">
                        <i class="bi bi-send"></i> Solicitar Traspasos
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>
<script>
    /* =========================================================
       TRASPASO MASIVO — Prefijo tm_ (independiente)
       Endpoint: /cfsistem/app/backend/almacen/procesar_traspaso_masivo.php
       ========================================================= */
    (function () {
        'use strict';

        // Cache por almacén (independiente del cacheProductosPorAlmacen del modal simple)
        const tmCacheProductos = {};
        // Mapa productoId -> { nombre, stock, factor, unidad, medida }
        const tmProductosMeta = {};

        /* ---------- Select2 del buscador ---------- */
        function tmInitSelect2Producto() {
            if (typeof window.jQuery === 'undefined' || !window.jQuery.fn.select2) return;
            const $modal = window.jQuery('#modalTraspasoMasivo');
            const $select = $modal.find('#tm_buscador_producto');
            if (!$select.length) return;

            if ($select.hasClass('select2-hidden-accessible')) $select.select2('destroy');

            $select.select2({
                theme: 'bootstrap-5',
                width: '100%',
                dropdownParent: $modal,
                placeholder: 'Escriba SKU o nombre...',
                allowClear: true,
                language: {
                    noResults: () => 'No hay productos disponibles',
                    searching: () => 'Buscando...'
                }
            });

            // Al seleccionar un producto -> agregarlo al carrito
            $select
                .off('select2:select.tmMasivo')
                .on('select2:select.tmMasivo', function (e) {
                    const id = e.params.data.id;
                    tmAgregarProductoAlCarrito(id);
                    $(this).val(null).trigger('change.select2');
                });
        }

        /* ---------- Fetch productos con cache ---------- */
        async function tmCargarProductosDesdeAPI(origenId) {
            if (!origenId) return [];
            if (tmCacheProductos[origenId]) return tmCacheProductos[origenId];

            try {
                const resp = await fetch(`/cfsistem/app/controllers/accesoController.php?action=obtenerProductosAlmacen&id=${origenId}`);
                const texto = await resp.text();
                let res;
                try { res = JSON.parse(texto); }
                catch (e) { console.error('❌ Respuesta no JSON:', texto); return []; }

                if (!res.success || !Array.isArray(res.data)) return [];

                const lista = res.data.map(p => ({
                    ...p,
                    almacen_id: p.almacen_id ?? origenId,
                    stock: Number(p.stock ?? 0),
                    factor_conversion: Number(p.factor_conversion ?? 1)
                }));

                tmCacheProductos[origenId] = lista;
                return lista;
            } catch (err) {
                console.error('❌ Error fetch productos:', err);
                return [];
            }
        }

        /* ---------- Repoblar buscador al cambiar origen ---------- */
        async function tmFiltrarProductosPorOrigen() {
            const origenId = document.getElementById('tm_origen_id').value;
            const selectProd = document.getElementById('tm_buscador_producto');

            selectProd.innerHTML = '<option value="">Seleccione producto...</option>';
            tmProductosMetaClear();

            if (!origenId) {
                selectProd.disabled = true;
                tmInitSelect2Producto();
                return;
            }

            const productos = await tmCargarProductosDesdeAPI(origenId);
            const disponibles = productos.filter(p => p.almacen_id == origenId && p.stock > 0);

            if (disponibles.length) {
                disponibles.forEach(p => {
                    const opt = document.createElement('option');
                    opt.value = p.producto_id ?? p.id;
                    opt.text = `${p.nombre} [ ${p.sku} ]`;
                    opt.dataset.stock = p.stock;
                    opt.dataset.factor = p.factor_conversion || 1;
                    opt.dataset.unidad = p.unidad_reporte || 'Unid.';
                    opt.dataset.medida = p.unidad_medida || 'Unid.';
                    opt.dataset.nombre = p.nombre || '';
                    selectProd.appendChild(opt);

                    tmProductosMeta[opt.value] = {
                        nombre: p.nombre || '',
                        stock: Number(p.stock || 0),
                        factor: Number(p.factor_conversion || 1),
                        unidad: p.unidad_reporte || 'Unid.',
                        medida: p.unidad_medida || 'Unid.'
                    };
                });
                selectProd.disabled = false;
            } else {
                selectProd.innerHTML = '<option value="">No hay productos disponibles</option>';
                selectProd.disabled = true;
            }

            tmInitSelect2Producto();
        }

        function tmProductosMetaClear() {
            Object.keys(tmProductosMeta).forEach(k => delete tmProductosMeta[k]);
        }

        /* ---------- Agregar producto al carrito ---------- */
        function tmAgregarProductoAlCarrito(productoId) {
            if (!productoId) return;
            if (document.querySelector(`#tm_tbody_carrito tr[data-producto-id="${productoId}"]`)) {
                Swal.fire('Aviso', 'El producto ya está en la lista.', 'info');
                return;
            }

            const meta = tmProductosMeta[productoId];
            if (!meta) return;

            // Ocultar empty state
            document.getElementById('tm_empty_state').classList.add('d-none');

            const tr = document.createElement('tr');
            tr.dataset.productoId = productoId;
            tr.dataset.productoNombre = meta.nombre;

            const stockMostrar = meta.factor > 0 ? (meta.stock / meta.factor) : meta.stock;
            const stockUnidad = stockMostrar >= 1 ? meta.unidad : meta.medida;
            const stockValor = stockMostrar >= 1 ? stockMostrar.toFixed(2) : meta.stock;

            tr.innerHTML = `
            <td style="max-width:0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                <b>${meta.nombre}</b>
            </td>
            <td>
                <span class="badge bg-light text-dark border">
                    ${stockValor} ${stockUnidad}
                </span>
                <input type="hidden" class="tm-stock-base" value="${meta.stock}">
                <input type="hidden" class="tm-factor" value="${meta.factor}">
            </td>
            <td>
                <input type="number" class="form-control form-control-sm tm-cantidad" step="0.01" min="0.01" value="1">
                <input type="hidden" class="tm-cantidad-base" value="1">
            </td>
            <td>
                <select class="form-select form-select-sm tm-unidad-select">
                    <option value="1" data-equivalencia="1" data-medida="${meta.medida}">${meta.medida}</option>
                    <option value="${meta.factor}" data-equivalencia="${meta.factor}" data-medida="${meta.unidad}">${meta.unidad}</option>
                </select>
            </td>
            <td>
                <select class="form-select form-select-sm tm-lote-select">
                    <option value="">Cargando lotes...</option>
                </select>
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-link text-danger p-0 tm-btn-quitar" title="Quitar">
                    <i class="bi bi-trash fs-5"></i>
                </button>
            </td>
        `;

            document.getElementById('tm_tbody_carrito').appendChild(tr);

            // Eventos de la fila
            tr.querySelector('.tm-btn-quitar').addEventListener('click', () => {
                tr.remove();
                tmActualizarContador();
                if (!document.querySelectorAll('#tm_tbody_carrito tr').length) {
                    document.getElementById('tm_empty_state').classList.remove('d-none');
                }
            });

            tr.querySelector('.tm-cantidad').addEventListener('input', () => tmRecalcularFila(tr));
            tr.querySelector('.tm-unidad-select').addEventListener('change', () => tmRecalcularFila(tr));

            // Cargar lotes de este producto en este almacén
            tmCargarLotesFila(productoId, document.getElementById('tm_origen_id').value, tr);

            // Inicializar cantidad base
            tmRecalcularFila(tr);
            tmActualizarContador();
        }

        /* ---------- Cargar lotes en la fila ---------- */
        async function tmCargarLotesFila(productoId, almacenId, tr) {
            const select = tr.querySelector('.tm-lote-select');
            if (!select) return;

            try {
                const resp = await fetch(`/cfsistem/app/controllers/entregasController.php?ajax=despachar&prodId=${productoId}&almacen=${almacenId}`);
                const data = await resp.json();

                select.innerHTML = '<option value="">Sin lote (automático)</option>';

                if (data && Array.isArray(data.data)) {
                    data.data.forEach(lote => {
                        const opt = document.createElement('option');
                        opt.value = lote.id;
                        opt.textContent = `${lote.codigo_lote} (${lote.cantidad_actual})`;
                        select.appendChild(opt);
                    });
                }
            } catch (e) {
                console.error('❌ Error cargando lotes:', e);
                select.innerHTML = '<option value="">Sin lote (automático)</option>';
            }
        }

        /* ---------- Recalcular cantidad base de la fila ---------- */
        function tmRecalcularFila(tr) {
            const cantInput = tr.querySelector('.tm-cantidad');
            const unidadSel = tr.querySelector('.tm-unidad-select');
            const baseInput = tr.querySelector('.tm-cantidad-base');
            const stockBase = parseFloat(tr.querySelector('.tm-stock-base').value) || 0;

            const cant = parseFloat(cantInput.value) || 0;
            const equiv = parseFloat(unidadSel.selectedOptions[0]?.dataset.equivalencia) || 1;
            const cantidadBase = cant * equiv;

            baseInput.value = cantidadBase;

            // Validación visual de stock
            if (cantidadBase > stockBase) {
                cantInput.classList.add('is-invalid');
            } else {
                cantInput.classList.remove('is-invalid');
            }
        }

        /* ---------- Contador ---------- */
        function tmActualizarContador() {
            const n = document.querySelectorAll('#tm_tbody_carrito tr').length;
            document.getElementById('tm_contador_carrito').textContent = `${n} producto${n === 1 ? '' : 's'}`;
        }

        /* ---------- Limpiar carrito ---------- */
        function tmLimpiarCarrito() {
            document.getElementById('tm_tbody_carrito').innerHTML = '';
            document.getElementById('tm_empty_state').classList.remove('d-none');
            tmActualizarContador();
        }

        /* ---------- Enviar traspasos (uno por uno) ---------- */
        /* ---------- Simulación: reporte de lo que se enviaría ---------- */
        // async function tmEnviarTraspasosMasivos() {
        //     const origenId = document.getElementById('tm_origen_id').value;
        //     const destinoId = document.getElementById('tm_destino_id').value;
        //     const observ = document.getElementById('tm_observaciones').value || '';

        //     // 🔒 ENVÍO DESHABILITADO: solo reporte
        //     if (!origenId) return Swal.fire('Falta origen', 'Seleccione el almacén de origen.', 'warning');
        //     if (!destinoId) return Swal.fire('Falta destino', 'Seleccione el almacén de destino.', 'warning');
        //     if (origenId === destinoId) {
        //         return Swal.fire('Error', 'El destino no puede ser igual al origen.', 'error');
        //     }

        //     const filas = Array.from(document.querySelectorAll('#tm_tbody_carrito tr'));
        //     if (!filas.length) {
        //         return Swal.fire('Carrito vacío', 'Agregue al menos un producto.', 'warning');
        //     }

        //     // Preparar items (misma estructura que usaba el envío real)
        //     const items = filas.map(tr => ({
        //         id: tr.dataset.productoId,
        //         nombre: tr.dataset.productoNombre || tr.dataset.productoId,
        //         cantidadBase: parseFloat(tr.querySelector('.tm-cantidad-base').value) || 0,
        //         loteId: tr.querySelector('.tm-lote-select').value || '',
        //         stockBase: parseFloat(tr.querySelector('.tm-stock-base').value) || 0,
        //         unidad: tr.querySelector('.tm-unidad-select')?.selectedOptions[0]?.dataset?.medida || '',
        //         cantidadUI: parseFloat(tr.querySelector('.tm-cantidad').value) || 0
        //     }));

        //     // Validar cantidades
        //     const invalidos = items.filter(i => !(i.cantidadBase > 0));
        //     if (invalidos.length) {
        //         return Swal.fire('Error', 'Hay filas con cantidad inválida (0 o vacía).', 'error');
        //     }

        //     // =========================================================
        //     // 🔍 REPORTE EN CONSOLA
        //     // =========================================================
        //     const origenNombre = document.querySelector('#tm_origen_id  option:checked')?.textContent?.trim() || origenId;
        //     const destinoNombre = document.querySelector('#tm_destino_id option:checked')?.textContent?.trim() || destinoId;

        //     console.group('%c📦 REPORTE DE TRASPASO MASIVO (SIMULACIÓN — NO SE ENVÍA)', 'color:#0d6efd;font-weight:bold;font-size:13px');
        //     console.log('Origen :', origenNombre, `(id=${origenId})`);
        //     console.log('Destino:', destinoNombre, `(id=${destinoId})`);
        //     console.log('Observaciones:', observ || '(sin observaciones)');
        //     console.log('Productos:', items.length);
        //     console.table(items.map((it, idx) => ({
        //         '#': idx + 1,
        //         Producto: it.nombre,
        //         'Cant. UI': it.cantidadUI,
        //         Unidad: it.unidad || '-',
        //         'Cant. base': it.cantidadBase,
        //         'Stock base': it.stockBase,
        //         'Lote ID': it.loteId || '(automático)',
        //         'Producto ID': it.id
        //     })));
        //     console.log('Payload que se enviaría por cada producto:', items.map(it => ({
        //         almacen_origen_id: origenId,
        //         almacen_destino_id: destinoId,
        //         producto_id: it.id,
        //         lote_id: it.loteId,
        //         cantidad: it.cantidadBase,
        //         observaciones: observ,
        //         id_traspaso_id: 0,          // 0 en el primero, luego el folio
        //         codigo_traspaso: ''
        //     })));
        //     console.groupEnd();

        //     // =========================================================
        //     // 🔍 REPORTE EN SWEETALERT
        //     // =========================================================
        //     const filasHtml = items.map((it, idx) => {
        //         const loteTxt = it.loteId ? `Lote #${it.loteId}` : 'Lote automático (PEPS)';
        //         const stockOk = it.cantidadBase <= it.stockBase;
        //         const badge = stockOk
        //             ? '<span class="badge bg-success-subtle text-success-emphasis">OK</span>'
        //             : '<span class="badge bg-danger-subtle text-danger-emphasis">EXCEDE</span>';
        //         return `
        //     <tr>
        //         <td class="text-center">${idx + 1}</td>
        //         <td>
        //             <div class="fw-semibold">${it.nombre}</div>
        //             <small class="text-body-secondary">${loteTxt}</small>
        //         </td>
        //         <td class="text-end">${it.cantidadUI} ${it.unidad || ''}</td>
        //         <td class="text-end"><b>${it.cantidadBase}</b></td>
        //         <td class="text-end">${it.stockBase}</td>
        //         <td class="text-center">${badge}</td>
        //     </tr>`;
        //     }).join('');

        //     const totalBase = items.reduce((s, it) => s + it.cantidadBase, 0);

        //     Swal.fire({
        //         icon: 'info',
        //         title: '🧪 Simulación de envío (NO se envía)',
        //         width: 900,
        //         html: `
        //     <div class="text-start">
        //         <div class="alert alert-warning py-2 px-3 small mb-3">
        //             <i class="bi bi-info-circle me-1"></i>
        //             El envío está <b>deshabilitado</b>. Este es el reporte de lo que se enviaría.
        //         </div>
        //         <div class="row g-2 mb-3">
        //             <div class="col-md-6">
        //                 <div class="border rounded p-2 small">
        //                     <div class="text-body-secondary">Origen</div>
        //                     <div class="fw-semibold">${origenNombre}</div>
        //                 </div>
        //             </div>
        //             <div class="col-md-6">
        //                 <div class="border rounded p-2 small">
        //                     <div class="text-body-secondary">Destino</div>
        //                     <div class="fw-semibold">${destinoNombre}</div>
        //                 </div>
        //             </div>
        //             <div class="col-12">
        //                 <div class="border rounded p-2 small">
        //                     <div class="text-body-secondary">Observaciones</div>
        //                     <div>${observ || '<em class="text-body-secondary">(sin observaciones)</em>'}</div>
        //                 </div>
        //             </div>
        //         </div>

        //         <div class="table-responsive" style="max-height:340px;overflow-y:auto">
        //             <table class="table table-sm align-middle mb-0">
        //                 <thead class="table-light sticky-top">
        //                     <tr>
        //                         <th style="width:40px" class="text-center">#</th>
        //                         <th>Producto</th>
        //                         <th class="text-end">Cant. UI</th>
        //                         <th class="text-end">Cant. base</th>
        //                         <th class="text-end">Stock base</th>
        //                         <th class="text-center">Estado</th>
        //                     </tr>
        //                 </thead>
        //                 <tbody>${filasHtml}</tbody>
        //                 <tfoot class="table-light">
        //                     <tr>
        //                         <th colspan="3" class="text-end">Total base:</th>
        //                         <th class="text-end">${totalBase}</th>
        //                         <th colspan="2"></th>
        //                     </tr>
        //                 </tfoot>
        //             </table>
        //         </div>

        //         <div class="text-body-secondary small mt-2">
        //             <i class="bi bi-terminal me-1"></i>
        //             Revisa la consola del navegador para ver el payload completo.
        //         </div>
        //     </div>
        // `,
        //         confirmButtonText: 'Entendido',
        //         confirmButtonColor: '#0d6efd',
        //         allowOutsideClick: true
        //     });
        // }













        async function tmEnviarTraspasosMasivos() {
            const origenId = document.getElementById('tm_origen_id').value;
            const destinoId = document.getElementById('tm_destino_id').value;
            const observ = document.getElementById('tm_observaciones').value || '';

            if (!origenId) return Swal.fire('Falta origen', 'Seleccione el almacén de origen.', 'warning');
            if (!destinoId) return Swal.fire('Falta destino', 'Seleccione el almacén de destino.', 'warning');
            if (origenId === destinoId) {
                return Swal.fire('Error', 'El destino no puede ser igual al origen.', 'error');
            }

            const filas = Array.from(document.querySelectorAll('#tm_tbody_carrito tr'));
            if (!filas.length) {
                return Swal.fire('Carrito vacío', 'Agregue al menos un producto.', 'warning');
            }

            // Preparar items
            const items = filas.map(tr => ({
                id: tr.dataset.productoId,
                nombre: tr.dataset.productoNombre || tr.dataset.productoId,
                cantidadBase: parseFloat(tr.querySelector('.tm-cantidad-base').value) || 0,
                loteId: tr.querySelector('.tm-lote-select').value || '',
                stockBase: parseFloat(tr.querySelector('.tm-stock-base').value) || 0
            }));

            // Validar cantidades
            const invalidos = items.filter(i => !(i.cantidadBase > 0));
            if (invalidos.length) {
                return Swal.fire('Error', 'Hay filas con cantidad inválida (0 o vacía).', 'error');
            }

            // ---------- UI de progreso en vivo ----------
            const estados = items.map(() => 'pendiente'); // pendiente | procesando | ok | error
            const mensajes = items.map(() => '');

            let folioLote = null; // se llena en el primer request exitoso

            const renderLista = () => items.map((it, i) => {
                const e = estados[i];
                const icon = e === 'pendiente'
                    ? '<i class="bi bi-hourglass-split text-secondary"></i>'
                    : e === 'procesando'
                        ? '<span class="spinner-border spinner-border-sm text-primary"></span>'
                        : e === 'ok'
                            ? '<i class="bi bi-check-circle-fill text-success"></i>'
                            : '<i class="bi bi-x-circle-fill text-danger"></i>';
                const detalle = mensajes[i]
                    ? `<br><small class="text-body-secondary" style="margin-left:24px">${mensajes[i]}</small>`
                    : '';
                return `<li class="mb-1">
                            <span style="display:inline-block;width:22px">${icon}</span>
                            <b>${it.nombre}</b>${detalle}
                        </li>`;
            }).join('');

            const cabeceraFolio = () => folioLote
                ? `<div class="alert alert-light border py-1 px-2 small mb-2">
                       <i class="bi bi-upc-scan me-1"></i>
                       Folio: <b>${folioLote.codigo_traspaso}</b>
                   </div>`
                : '';

            const actualizarSwal = (extra = '') => {
                Swal.update({
                    title: `Traspasando... ${extra}`,
                    html: `<div class="text-start" style="max-height:320px;overflow-y:auto">
                               ${cabeceraFolio()}
                               <ul class="list-unstyled mb-0">${renderLista()}</ul>
                           </div>`,
                    showConfirmButton: false,
                    showCancelButton: false,
                    allowOutsideClick: false,
                    allowEscapeKey: false
                });
            };

            Swal.fire({
                title: 'Traspasando...',
                html: `<div class="text-start" style="max-height:320px;overflow-y:auto">
                           ${cabeceraFolio()}
                           <ul class="list-unstyled mb-0">${renderLista()}</ul>
                       </div>`,
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                showCancelButton: false,
                didOpen: () => Swal.showLoading()
            });

            // ---------- Procesar uno por uno ----------
            let exitosos = 0, fallidos = 0;

            for (let i = 0; i < items.length; i++) {
                const item = items[i];
                estados[i] = 'procesando';
                actualizarSwal(`(${i + 1} de ${items.length})`);

                try {
                    const fd = new FormData();
                    fd.append('almacen_origen_id', origenId);
                    fd.append('almacen_destino_id', destinoId);
                    fd.append('producto_id', item.id);
                    fd.append('lote_id', item.loteId);
                    fd.append('cantidad', item.cantidadBase);
                    fd.append('observaciones', observ);

                    // 🔑 Primer producto: 0/'' → backend calcula MAX+1 y arma el código.
                    //    Siguientes: reenvía el folio recibido.
                    fd.append('id_traspaso_id', folioLote ? folioLote.id_traspaso_id : 0);
                    fd.append('codigo_traspaso', folioLote ? folioLote.codigo_traspaso : '');

                    const resp = await fetch('/cfsistem/app/backend/almacen/procesar_traspaso_masivo.php', {
                        method: 'POST',
                        body: fd
                    });

                    let res;
                    try { res = await resp.json(); }
                    catch { throw new Error('Respuesta inválida del servidor'); }

                    if (res.status === 'success') {
                        // Capturar el folio en el primer request exitoso
                        if (!folioLote && res.id_traspaso_id && res.codigo_traspaso) {
                            folioLote = {
                                id_traspaso_id: res.id_traspaso_id,
                                codigo_traspaso: res.codigo_traspaso
                            };
                        }
                        estados[i] = 'ok';
                        mensajes[i] = res.message || 'Traspaso creado';
                        exitosos++;
                    } else {
                        estados[i] = 'error';
                        mensajes[i] = res.message || 'Error desconocido';
                        fallidos++;
                    }
                } catch (err) {
                    estados[i] = 'error';
                    mensajes[i] = 'Error de conexión: ' + err.message;
                    fallidos++;
                }

                actualizarSwal(`(${i + 1} de ${items.length})`);
            }

            // ---------- Resumen final ----------
            const todoOk = fallidos === 0;

            Swal.fire({
                icon: todoOk ? 'success' : 'warning',
                title: todoOk
                    ? `✅ ${exitosos} traspaso(s) creado(s)`
                    : `${exitosos} creado(s), ${fallidos} fallido(s)`,
                html: `<div class="text-start" style="max-height:340px;overflow-y:auto">
                           ${cabeceraFolio()}
                           <ul class="list-unstyled mb-0">${renderLista()}</ul>
                       </div>`,
                width: 600,
                confirmButtonText: 'Cerrar',
                allowOutsideClick: true
            }).then(() => {
                if (todoOk) {
                    // Todo salió bien: limpiamos el carrito y cerramos el modal
                    tmLimpiarCarrito();
                    const modalEl = document.getElementById('modalTraspasoMasivo');
                    const inst = bootstrap.Modal.getInstance(modalEl);
                    if (inst) inst.hide();
                    // Si quieres recargar la página:
                    // location.reload();
                }
                // Si hay fallos: dejamos las filas para reintentar manualmente
            });
        }

        // /* ---------- Wiring ---------- */





        document.addEventListener('DOMContentLoaded', function () {
            const modalEl = document.getElementById('modalTraspasoMasivo');
            if (!modalEl) return;

            // Al abrir: init Select2 y contador
            modalEl.addEventListener('shown.bs.modal', function () {
                tmInitSelect2Producto();
                tmActualizarContador();
            });

            // Cambio de origen
            const selOrigen = document.getElementById('tm_origen_id');
            if (selOrigen) selOrigen.addEventListener('change', tmFiltrarProductosPorOrigen);

            // Botones
            const btnLimpiar = document.getElementById('tm_btn_limpiar');
            if (btnLimpiar) btnLimpiar.addEventListener('click', tmLimpiarCarrito);

            const btnEnviar = document.getElementById('tm_btn_enviar');
            if (btnEnviar) btnEnviar.addEventListener('click', tmEnviarTraspasosMasivos);
        });

        // Exponer solo lo necesario para el botón que abre el modal (si lo necesitas)
        window.tmInitSelect2Producto = tmInitSelect2Producto;

    })();

</script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const origen = document.getElementById('tm_origen_id');
        const destino = document.getElementById('tm_destino_id');

        // Guardamos las opciones originales (incluye el placeholder)
        const origenOpts = Array.from(origen.options).map(o => ({ value: o.value, text: o.text }));
        const destinoOpts = Array.from(destino.options).map(o => ({ value: o.value, text: o.text }));

        /**
         * Reconstruye un <select> excluyendo una opción concreta.
         * Mantiene el placeholder y re-marca el valor actual.
         */
        function rebuild(select, options, excludeValue, currentValue) {
            select.innerHTML = '';
            options.forEach(opt => {
                // Nunca excluimos el placeholder (value === '')
                if (opt.value === '' || !excludeValue || opt.value !== excludeValue) {
                    const o = document.createElement('option');
                    o.value = opt.value;
                    o.text = opt.text;
                    if (opt.value === currentValue) o.selected = true;
                    select.appendChild(o);
                }
            });
        }

        function sync() {
            const origenVal = origen.value;
            const destinoVal = destino.value;

            // Destino: oculta la opción elegida en Origen
            rebuild(destino, destinoOpts, origenVal, destinoVal);

            // Origen: oculta la opción elegida en Destino
            rebuild(origen, origenOpts, destinoVal, origenVal);
        }

        origen.addEventListener('change', sync);
        destino.addEventListener('change', sync);
    });
</script>