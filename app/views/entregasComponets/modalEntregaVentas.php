<style>
    /* ═══════════════════════════════════════════════════════════
       MODAL DESPACHO VENTA TOTAL — z-index 100080
       ═══════════════════════════════════════════════════════════ */
    #modalDespachoVentaTotal {
        z-index: 10080 !important;
    }

    /* 🔥 Forzar que SweetAlert2 y su fondo oscuro aparezcan siempre arriba (por encima del modal 10110) */
    .swal2-container {
        z-index: 10120 !important;
    }
</style>

<div class="modal fade" id="modalDespachoVentaTotal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content border-0 shadow-lg bg-body text-body" style="border-radius: 24px; overflow: hidden;">

            <!-- HEADER -->
            <div class="modal-header border-bottom border-secondary-subtle px-4 py-3 bg-body-tertiary">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-success text-white d-flex align-items-center justify-content-center shadow-sm"
                        style="width: 48px; height: 48px; border-radius: 14px;">
                        <i class="bi bi-box-seam-fill fs-4"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-1">Despacho Masivo Por Venta</h5>
                        <span class="badge bg-secondary-subtle text-secondary border px-2 py-1 fw-semibold"
                            id="txtFolioVenta">Cargando...</span>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- BODY -->
            <div class="modal-body px-4 py-4">
                <!-- Tabla limpia y adaptable a modo oscuro -->
                <div class="table-responsive rounded-4 border border-secondary-subtle shadow-sm mb-4">
                    <table class="table table-hover align-middle mb-0 text-body">
                        <thead class="table-light border-bottom border-secondary-subtle">
                            <tr>
                                <th class="py-3 px-3">Producto</th>
                                <th class="py-3 px-3">Disponible</th>
                                <th class="py-3 px-3">Comprada</th>
                                <th class="py-3 px-3">Restante</th>
                                <th class="py-3 px-3" style="width: 130px;">Entregar</th>
                                <th class="py-3 px-3">Lote</th>
                            </tr>
                        </thead>
                        <tbody id="listaItemsDespacho"></tbody>
                    </table>
                </div>

                <!-- SECCIÓN LOGÍSTICA -->
                <div id="seccionLogisticaMasiva" class="d-none animate__animated animate__fadeIn">
                    <hr class="my-4 border-secondary-subtle opacity-50">

                    <!-- Selector de método -->
                    <div class="p-3 bg-body-secondary rounded-4 border border-secondary-subtle shadow-sm mb-3">
                        <label class="text-uppercase fw-bold text-primary mb-2 d-block"
                            style="font-size: 0.7rem; letter-spacing: 1.2px;">Método de Salida</label>
                        <div class="btn-group w-100 shadow-sm" role="group">
                            <input type="radio" class="btn-check" name="tipo_entrega_masiva" id="optPatio" value="patio"
                                checked onchange="toggleFormRuta(false)">
                            <label class="btn btn-outline-success py-2 fw-bold" for="optPatio">
                                <i class="bi bi-box-seam me-2"></i>ENTREGA EN PATIO
                            </label>

                            <input type="radio" class="btn-check" name="tipo_entrega_masiva" id="optRuta" value="ruta"
                                onchange="toggleFormRuta(true)">
                            <label class="btn btn-outline-primary py-2 fw-bold" for="optRuta">
                                <i class="bi bi-truck me-2"></i>ASIGNAR RUTA
                            </label>
                        </div>
                    </div>

                    <!-- Formulario de Ruta / Logística -->
                    <div id="formRutaIntegrado" class="animate__animated animate__fadeInUp">
                        <div id="wrapperLogistica"
                            class="p-4 rounded-4 shadow-sm bg-body-tertiary border border-secondary-subtle"
                            style="transition: all 0.3s ease;">
                            <div class="row g-3">
                                <div class="col-12">
                                    <div id="contenedorDireccion"
                                        class="p-3 rounded-4 bg-body border border-secondary-subtle shadow-sm">
                                        <label id="lblDinamicoPrincipal" class="small fw-bold text-body-secondary mb-1"
                                            style="font-size: 0.65rem;">PUNTO DE ENTREGA / OBRA</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-transparent border-0 pe-2">
                                                <i id="iconDinamico" class="bi bi-geo-alt-fill text-danger fs-5"></i>
                                            </span>
                                            <textarea id="mv_direccion"
                                                class="form-control bg-transparent border-0 text-body p-1 text-uppercase shadow-none"
                                                rows="2" style="font-size: 0.9rem; resize: none;"
                                                placeholder="Dirección exacta..."></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6" id="colVehiculo">
                                    <label class="small fw-bold text-body-secondary mb-1">UNIDAD / VEHÍCULO</label>
                                    <select id="mv_vehiculo_id"
                                        class="form-select bg-body shadow-sm rounded-3 p-2.5 border-secondary-subtle text-body"></select>
                                </div>
                                <div class="col-md-6">
                                    <label id="lblPersonal" class="small fw-bold text-body-secondary mb-1">CHOFER
                                        RESPONSABLE</label>
                                    <select id="mv_chofer_id"
                                        class="form-select bg-body shadow-sm rounded-3 p-2.5 border-secondary-subtle text-body"></select>
                                </div>

                                <div class="col-12">
                                    <label class="small fw-bold text-body-secondary mb-1">AYUDANTES DE CARGA
                                        (OPCIONAL)</label>
                                    <select id="mv_tripulantes"
                                        class="form-select bg-body shadow-sm rounded-3 p-2 border-secondary-subtle text-body"
                                        style="font-size: 0.85rem;"></select>
                                    <small class="text-body-secondary mt-1 d-block" style="font-size: 0.65rem;">
                                        <i class="bi bi-info-circle me-1"></i>Mantén presionada la tecla <b>Ctrl</b>
                                        para elegir varios.
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- FOOTER -->
            <div class="modal-footer border-top border-secondary-subtle py-3 px-4 bg-body-tertiary">
                <button type="button" class="btn btn-outline-secondary rounded-pill px-4 fw-bold"
                    data-bs-dismiss="modal">Cancelar</button>
                <button type="button" id="btnEjecutarDespachoMasivo"
                    class="btn btn-success rounded-pill px-5 fw-bold shadow-sm" disabled>
                    <i class="bi bi-check-circle me-2"></i>Confirmar Despacho
                </button>
            </div>
        </div>
    </div>
</div>
<script>
    // ✅ FIX: mayúsculas solo en campos de texto visibles del modal
    document.querySelectorAll('#modalDespachoVentaTotal input[type="text"], #modalDespachoVentaTotal textarea')
        .forEach(elemento => {
            elemento.addEventListener('input', function () {
                this.value = this.value.toUpperCase();
            });
        });

    /* ═══════════════════════════════════════════════════════════
       HELPER GLOBAL — definido ANTES de cualquier función que lo use
       ═══════════════════════════════════════════════════════════ */
    window.extraerDvid = function (fuente) {
        if (fuente == null) return '';
        if (typeof fuente === 'number') return String(fuente);
        if (typeof fuente === 'string') {
            const s = fuente.trim();
            return (s === '' || s === '[object Object]' || s === 'undefined' || s === 'null') ? '' : s;
        }
        if (typeof fuente === 'object') {
            if (fuente.dvid != null) return window.extraerDvid(fuente.dvid);
            if (fuente.dvid_id != null) return window.extraerDvid(fuente.dvid_id);
            if (fuente.id != null) return window.extraerDvid(fuente.id);
            if (fuente.row != null) return window.extraerDvid(fuente.row);
            if (fuente.data != null) return window.extraerDvid(fuente.data);
        }
        return '';
    };

    function toggleFormRuta(esRuta) {
        const btnConfirmar = $('#btnEjecutarDespachoMasivo');

        if (esRuta) {
            $('#lblDinamicoPrincipal').text('PUNTO DE ENTREGA / OBRA (EDITABLE)');
            $('#iconDinamico').removeClass('bi-person-fill text-success').addClass('bi-geo-alt-fill text-danger');
            $('#mv_direccion').attr('placeholder', 'Dirección exacta de entrega...');
            $('#lblPersonal').text('CHOFER RESPONSABLE');
            $('#colVehiculo').fadeIn();
            btnConfirmar.removeClass('btn-success').addClass('btn-primary');

            // 🔥 SI ES RUTA: El vehículo pasa a ser obligatorio
            $('#mv_vehiculo_id').prop('required', true);

        } else {
            $('#lblDinamicoPrincipal').text('NOTAS / QUIÉN RECIBE (OPCIONAL)');
            $('#iconDinamico').removeClass('bi-geo-alt-fill text-danger').addClass('bi-person-fill text-success');
            $('#mv_direccion').attr('placeholder', 'Ej. Se lo lleva el cliente en su camioneta...');
            $('#lblPersonal').text('DESPACHADOR RESPONSABLE (PATIO)');
            $('#colVehiculo').hide();
            btnConfirmar.removeClass('btn-primary').addClass('btn-success');

            // 🔥 SI NO ES RUTA (ES PATIO): Quitamos la obligatoriedad del vehículo para que no bloquee
            $('#mv_vehiculo_id').prop('required', false);
            // Limpiamos su valor por si se quedó seleccionado algo antes
            $('#mv_vehiculo_id').val('');
        }
    }

    let modal = null;
    window.carrito = {};

    async function lotes(producto_id, almacenId) {
        const resp = await fetch(
            `/cfsistem/app/controllers/entregasController.php?ajax=despachar&prodId=${producto_id}&almacen=${almacenId}`
        );
        return await resp.json();
    }



    async function abrirModalDespachoVentaTotal(almacenId, ventaId) {
        console.log('[Despacho] almacenId:', almacenId, 'ventaId:', ventaId);
        const URL_ENTREGAS = 'entregasController.php';
        const modalElement = document.getElementById('modalDespachoVentaTotal');
        modal = new bootstrap.Modal(modalElement);

        const contenedor = document.getElementById('listaItemsDespacho');
        const txtFolio = document.getElementById('txtFolioVenta');
        const btnConfirmar = document.getElementById('btnEjecutarDespachoMasivo');
        const logSection = document.getElementById('seccionLogisticaMasiva');

        // Reset UI
        txtFolio.innerHTML = `<span class="opacity-50 small">Sincronizando...</span>`;
        contenedor.innerHTML = `<div class="text-center py-4"><div class="spinner-border text-success opacity-25"></div></div>`;
        logSection.classList.add('d-none');
        $('#optPatio').prop('checked', true);
        toggleFormRuta(false);
        btnConfirmar.disabled = true;
        modal.show();

        try {
            // 1. Productos pendientes
            const respIds = await fetch(`${URL_ENTREGAS}?ajax=get_productos_para_despacho&venta_id=${ventaId}`);
            const dataIds = await respIds.json();
            const idsParaProcesar = dataIds.ids || [];
            console.log(idsParaProcesar);
            const primerId = idsParaProcesar[0]?.venta_id;


            if (!idsParaProcesar.length) {
                contenedor.innerHTML = `<div class="alert alert-warning mx-2 small">No hay productos pendientes de despacho.</div>`;
                return;
            }

            // 2. Lotes
            window.carrito = {};
            const todos_los_lotes = await Promise.all(
                idsParaProcesar.map(item => lotes(item.producto_id, almacenId))
            );

            contenedor.innerHTML = '';

            idsParaProcesar.forEach((item, index) => {
                const cantidadRealFaltante = (Number(item.cantidad) || 0) - (Number(item.cantidad_entregada) || 0);

                // ✅ Si ya no hay faltante, no se pinta esta fila
                if (cantidadRealFaltante <= 0) return;

                const cantidad_maxima = Math.min(
                    Number(item.disponible) || 0,
                    cantidadRealFaltante
                );

                const factor = Number(item.factor_conversion) || 1;
                const usaUnidadReporte =
                    (cantidadRealFaltante / factor) >= 1 &&
                    (Number(item.disponible) / factor) >= 1;
                const factorFila = usaUnidadReporte ? factor : 1;

                const stockMostrar = (Number(item.disponible) || 0) / factorFila;
                const compradaMostrar = (Number(item.cantidad) || 0) / factorFila;
                const faltanteMostrar = cantidadRealFaltante / factorFila;
                const unidadMostrar = usaUnidadReporte ? item.unidad_reporte : item.unidad_medida;
                const maxInput = (cantidad_maxima / factorFila).toFixed(4);

                // ✅ dvid seguro desde el backend
                const dvid = window.extraerDvid(item.dvid ?? item);
                if (!dvid) {
                    console.warn('[Despacho] Fila sin dvid válido, se omite:', item);
                    return;
                }

                const inputId = `cantidad_despacho_${dvid}`;
                const hiddenId = `cantidad_despacho_hidden_${dvid}`;
                const loteSelectId = `merma_lote_${dvid}`;

                const tr = document.createElement('tr');
                tr.dataset.dvid = dvid;
                tr.innerHTML = `
        <td>
            <span class="fw-bold card-title-text">
                <i class="bi bi-box-seam me-1 text-success"></i>
                ${item.producto}
            </span>
        </td>
        <td><span class="bg-info-subtle card-title-text">${stockMostrar.toFixed(3)} ${unidadMostrar}</span></td>
        <td><span class="bg-primary-subtle card-title-text">${compradaMostrar.toFixed(3)} ${unidadMostrar}</span></td>
        <td><span class="bg-warning-subtle card-title-text">${faltanteMostrar.toFixed(3)} ${unidadMostrar}</span></td>
        <td style="width:140px;">
            <input type="number"
                   step="0.0001"
                   min="0"
                   max="${maxInput}"
                   id="${inputId}"
                   class="form-control form-control-sm entregador"
                   data-factor="${factorFila}"
                   data-dvid="${dvid}"
                   data-id="${item.producto_id}"
                   data-max="${maxInput}">
            <input type="hidden"
                   id="${hiddenId}"
                   class="input-entrega"
                   data-dvid="${dvid}"
                   data-id="${item.producto_id}">
        </td>
        <td style="min-width:220px;">
            <select id="${loteSelectId}"
                    name="${loteSelectId}"
                    class="form-select form-select-sm lote-select"
                    data-dvid="${dvid}"
                    required>
                <option value="">Seleccione lote</option>
            </select>
        </td>
    `;

                const select = tr.querySelector('select');
                const lotesData = todos_los_lotes[index]?.data || [];
                lotesData.forEach(lote => {
                    const option = document.createElement('option');
                    option.value = lote.id;
                    const stockLoteFila = (Number(lote.cantidad_actual) || 0) / factorFila;
                    option.textContent = `${lote.codigo_lote} (${stockLoteFila.toFixed(3)} ${unidadMostrar})`;
                    option.dataset.stock = lote.cantidad_actual;
                    option.dataset.stockFila = stockLoteFila;
                    select.appendChild(option);
                });

                contenedor.appendChild(tr);

                window.carrito[dvid] = {
                    producto_id: item.producto_id,
                    nombre: item.nombre,
                    factor: factorFila,
                    factorConversion: factor,
                    equivalencia: item.equivalencia,
                    lote: null,
                    loteNombre: null
                };
            });
            // 3. Recursos
            const [respDetalle, respRecursos] = await Promise.all([
                fetch(`${URL_ENTREGAS}?ajax=get_recursos_reparto&id=${primerId}`),
                fetch(`${URL_ENTREGAS}?ajax=get_recursos_sucursal&almacen_id=${0}`)
            ]);

            const resDetalle = await respDetalle.json();
            const resRecursos = await respRecursos.json();

            if (resDetalle.success && resRecursos.success) {
                console.log(resDetalle);
                $('#mv_direccion').val(resDetalle.data.entrega.cliente_direccion_fiscal || '');

                const selectV = $('#mv_vehiculo_id')
                    .empty()
                    .append('<option value="">Seleccione unidad...</option>');
                (resRecursos.unidades || []).forEach(u => {
                    selectV.append(
                        `<option value="${String(u.id)}">${u.nombre} [${u.placas || 'S/P'}](${u.tipo || 'S/P'})</option>`
                    );
                });

                const selectC = $('#mv_chofer_id')
                    .empty()
                    .append('<option value="">Seleccione responsable...</option>');
                const selectT = $('#mv_tripulantes')
                    .empty()
                    .append('<option value="">Seleccione tripulante...</option>');

                (resRecursos.choferes || []).forEach(c => {
                    const id = String(c.id);
                    const opt = `<option value="${id}">${c.nombre}</option>`;
                    selectC.append(opt);
                    selectT.append(opt);
                });

                $('#mv_vehiculo_id').off('change').on('change', function () {
                    const vehiculo_id = $(this).val();

                    // Limpiar selección previa de chofer y tripulantes
                    $('#mv_chofer_id').val('').prop('disabled', false);
                    $('#mv_tripulantes').val('');

                    if (!vehiculo_id) return;

                    fetch(`${URL_ENTREGAS}?ajax=get_datos_vehiculo&vehiculo_id=${vehiculo_id}`)
                        .then(r => r.json())
                        .then(res => {
                            // Ya NO se asigna chofer ni tripulante por default.
                            // Solo se deja la lista disponible para que el usuario elija manualmente.

                            if (res.success && res.data &&
                                (res.data.encargado || (res.data.tripulantes && res.data.tripulantes.length))) {
                                // Opcional: aquí podrías mostrar un aviso tipo
                                // "Este vehículo sugiere a X como chofer" sin seleccionarlo.
                            } else {
                                // Fallback: si el vehículo no tiene datos, recargar lista de trabajadores
                                const selectCf = $('#mv_chofer_id')
                                    .empty()
                                    .append('<option value="">Seleccione responsable...</option>');
                                const selectTf = $('#mv_tripulantes')
                                    .empty()
                                    .append('<option value="">Seleccione tripulante...</option>');

                                (resRecursos.trabajadoresDisponibles || []).forEach(t => {
                                    const id = String(t.id);
                                    const opt = `<option value="${id}">${t.nombre}</option>`;
                                    selectCf.append(opt);
                                    selectTf.append(opt);
                                });

                                $('#mv_chofer_id').val('');
                                $('#mv_tripulantes').val('');
                            }
                        })
                        .catch(err => console.error('Error fetch:', err));
                });
                $('#mv_chofer_id').off('change').on('change', function () {
                    const sel = String($(this).val());
                    $('#mv_tripulantes option').each(function () {
                        const val = String($(this).val());
                        if (!val) return;
                        if (sel && val === sel) {
                            $(this).prop('disabled', true).prop('selected', false).hide();
                            if ($('#mv_tripulantes').val() === sel) $('#mv_tripulantes').val('');
                        } else {
                            $(this).prop('disabled', false).show();
                        }
                    });
                    $('#mv_tripulantes').trigger('change');
                });

                logSection.classList.remove('d-none');
                btnConfirmar.disabled = false;
                btnConfirmar.onclick = async () => { await procesarEntregaFinal(); };
            }
        } catch (err) {
            console.error('[Despacho] Error:', err);
            contenedor.innerHTML = `<div class="alert alert-danger mx-2 small">${err.message}</div>`;
        }
    }
    document.addEventListener('change', function (e) {
        if (!e.target.matches('select.lote-select')) return;

        const dvid = window.extraerDvid(e.target.dataset.dvid);
        const item = window.carrito[dvid];
        if (!item) {
            console.warn(`[Lote] No existe carrito[${dvid}]`);
            return;
        }

        const optionSeleccionada = e.target.options[e.target.selectedIndex];
        const stockLote = parseFloat(optionSeleccionada.dataset.stock || 0);

        item.lote = e.target.value;
        item.loteNombre = optionSeleccionada.textContent;

        console.log('dvid:', dvid, '| Producto:', item.nombre, '| Lote:', item.lote, '| Stock:', stockLote);
    });

    async function procesarEntregaFinal() {
        const fd = new FormData();
        const itemsValidos = [];
        const errores = [];

        // 1) Sincronizar hidden + recolectar valores del DOM
        $('#listaItemsDespacho tr').each(function () {
            const $tr = $(this);
            const $inputVisible = $tr.find('input.entregador');
            const $inputHidden = $tr.find('input.input-entrega');

            if (!$inputVisible.length || $inputVisible.prop('disabled')) return;

            const dvid = window.extraerDvid($inputVisible.attr('data-dvid'))
                || window.extraerDvid($tr.attr('data-dvid'));

            if (!dvid) {
                errores.push('Hay una fila sin identificador (dvid). Recarga el modal.');
                return;
            }

            const factor = parseFloat($inputVisible.attr('data-factor')) || 1;
            const max = parseFloat($inputVisible.attr('data-max') || $inputVisible.attr('max'));
            const maxLimit = isNaN(max) ? Infinity : max;

            const raw = String($inputVisible.val() || '').trim().replace(',', '.');
            const v = parseFloat(raw);

            if (raw === '') { $inputHidden.val(''); return; }

            if (isNaN(v) || v <= 0) {
                errores.push(`Cantidad inválida en "${window.carrito[dvid]?.nombre || dvid}".`);
                $inputHidden.val('');
                return;
            }
            if (v > maxLimit) {
                errores.push(`"${window.carrito[dvid]?.nombre || dvid}": ${v} excede el máximo ${maxLimit}.`);
                $inputHidden.val('');
                return;
            }

            const cantBase = (v * factor).toFixed(4);
            $inputHidden.val(cantBase);

            // Obtenemos el lote seleccionado desde window.carrito
            const loteSeleccionado = window.carrito[dvid]?.lote || null;

            itemsValidos.push({
                dvid,
                nombreProducto: window.carrito[dvid]?.nombre || 'Desconocido',
                cantVisible: v,
                cantBase,
                factor,
                loteId: loteSeleccionado
            });
        });

        if (errores.length) {
            Swal.fire({
                icon: 'warning',
                title: 'Revisa las cantidades',
                html: `<ul style="text-align:left;margin:0;padding-left:1.2rem">${errores.map(e => `<li>${e}</li>`).join('')}</ul>`,
                confirmButtonColor: '#198754'
            });
            return false;
        }

        if (!itemsValidos.length) {
            Swal.fire('Atención', 'Indique al menos una cantidad válida para entregar', 'warning');
            return false;
        }

        // 2) Validar que cada ítem válido tenga su lote asignado
        for (const it of itemsValidos) {
            if (!it.loteId) {
                Swal.fire('Atención', `Debes seleccionar un lote para el producto con cantidad (dvid ${it.dvid}).`, 'warning');
                return false;
            }
        }

        // 3) Datos del formulario
        const tipo = $('input[name="tipo_entrega_masiva"]:checked').val(); // 'ruta' o 'patio' (o el valor que maneje tu input)
        const vehiculoId = $('#mv_vehiculo_id').val();
        const choferId = $('#mv_chofer_id').val();
        const tripulanteId = $('#mv_tripulantes').val();
        const direccion = ($('#mv_direccion').val() || '').trim();

        // 🔥 Validación dinámica basada en el tipo de entrega seleccionado
        if (tipo === 'ruta') {
            if (!vehiculoId) {
                Swal.fire({ icon: 'error', title: 'Campos incompletos', text: 'Por favor, selecciona un vehículo para la ruta.', confirmButtonColor: '#d33' });
                return false;
            }
            if (!choferId) {
                Swal.fire({ icon: 'error', title: 'Campos incompletos', text: 'Por favor, selecciona un chofer responsable.', confirmButtonColor: '#d33' });
                return false;
            }
            if (!direccion) {
                Swal.fire({ icon: 'error', title: 'Campos incompletos', text: 'Por favor, escribe la dirección de entrega.', confirmButtonColor: '#d33' });
                return false;
            }
        } else {
            // Validación para modo PATIO (el vehículo ya no se valida porque está oculto/desactivado)
            if (!choferId) {
                Swal.fire({ icon: 'error', title: 'Campos incompletos', text: 'Por favor, selecciona un despachador responsable.', confirmButtonColor: '#d33' });
                return false;
            }
            // Si en patio la dirección es opcional, omitimos su validación estricta aquí.
        }
        // 4) Armar FormData para el primer endpoint (`guardarEntregaMasiva`)
        itemsValidos.forEach(it => {
            fd.append(`productos[${it.dvid}]`, it.cantBase);
        });

        fd.append('tipo_logistica', tipo);
        fd.append('vehiculo_id', tipo === 'ruta' ? vehiculoId : 0);
        fd.append('chofer_id', choferId || '');
        fd.append('tripulante_id', tripulanteId || '');
        fd.append('direccion', direccion);
        fd.append('venta_id', ventaActual.info.id);

        try {
            // 🔥 LLAMADA AL PRIMER CONTROLADOR (guardarEntregaMasiva)
            const res = await fetch(`${URL_CONTROLLER}?action=guardarEntregaMasiva`, { method: 'POST', body: fd });
            const result = await res.json();

            console.log('[Despacho] Respuesta de guardarEntregaMasiva (result.ids):', result.ids);

            if (result.status === 'success') {
                if (Array.isArray(result.ids) && result.ids.length) {
                    // 🔥 LLAMAMOS A LA SEGUNDA FUNCIÓN PASÁNDOLE LOS IDS DEL SERVIDOR Y LOS LOTES LOCALES
                    if (typeof ejecutarSalidaMasivaFinal === 'function') {
                        await ejecutarSalidaMasivaFinal(result.ids, itemsValidos);
                    } else {
                        console.error("La función ejecutarSalidaMasivaFinal no está definida.");
                    }
                }
                return true;
            } else {
                Swal.fire('No se pudo entregar', result.message || 'Error desconocido', 'error');
                return false;
            }
        } catch (e) {
            console.error("Error al procesar entrega masiva:", e);
            Swal.fire('Error Técnico', 'Hubo un problema de conexión con el servidor', 'error');
            return false;
        }
    }
    async function ejecutarSalidaMasivaFinal(result_ids, itemsValidos) {
        const tipo = $('input[name="tipo_entrega_masiva"]:checked').val();

        if (!$('#mv_chofer_id').val()) {
            return Swal.fire('Atención', 'Seleccione un responsable.', 'warning');
        }
        if (tipo === 'ruta' && !$('#mv_vehiculo_id').val()) {
            return Swal.fire('Atención', 'Seleccione una unidad.', 'warning');
        }

        try {
            const formData = new FormData();
            formData.append('ajax', 'despachar_venta_completaConLotes');
            formData.append('tipo_logistica', tipo);
            formData.append('vehiculo_id', tipo === 'ruta' ? $('#mv_vehiculo_id').val() : 0);
            formData.append('chofer_id', $('#mv_chofer_id').val());
            formData.append('direccion', $('#mv_direccion').val() || '');
            formData.append('tripulante_id', $('#mv_tripulantes').val() || 0);

            // ───────────────────────────────────────────────────────────
            // movId  → viene del servidor:   result_ids[i].movimiento_id
            // loteId → viene de itemsValidos: itemsValidos[i].loteId
            // Se cruzan por índice (ambos se armaron en el mismo orden)
            // ───────────────────────────────────────────────────────────
            result_ids.forEach((row, index) => {
                if (row == null) return;

                const movId = row.movimiento_id ?? row.movId ?? row.id ?? null;
                const loteVal = itemsValidos[index]?.loteId ?? '';

                if (movId != null) formData.append('ids_movimientos[]', movId);
                formData.append('lotes[]', loteVal);

                console.log('[SalidaMasiva] movId:', movId, '| lote:', loteVal);
            });

            // Debug: confirma alineación y que los lotes viajan con valor
            console.log('[SalidaMasiva] ids_movimientos:', formData.getAll('ids_movimientos[]'));
            console.log('[SalidaMasiva] lotes:', formData.getAll('lotes[]'));

            const resp = await fetch('entregasController.php', { method: 'POST', body: formData });
            const res = await resp.json();

            if (res.success) {
                if (typeof modalObj !== 'undefined' && modalObj && typeof modalObj.hide === 'function') modalObj.hide();
                if (modal && typeof modal.hide === 'function') modal.hide();
                if (typeof getVentas === 'function') getVentas();

                Swal.fire({ icon: 'success', title: '¡Éxito!', text: res.ids || 'Despacho registrado' });

                setTimeout(() => {
                    if (typeof cargarRepartos2 === 'function') cargarRepartos2(ventaActual.info.id);
                }, 500);
            } else {
                throw new Error(res.message || 'Error desconocido');
            }
        } catch (e) {
            console.error('[Salida Masiva] Error:', e);
            Swal.fire('Error de Despacho', e.message, 'error');
        }
    }
    async function cargarRepartos2(idVenta) {
        try {
            const resp = await fetch(`/cfsistem/app/controllers/repartosController.php?action=get_repartos_entrega&id=${idVenta}`);
            const repartoViaje = await resp.json();
            const repartos = repartoViaje.data || [];

            if (repartos.length > 0) {
                const ultimoReparto = repartos[repartos.length - 1];
                if (typeof imprimirRuta === 'function') {
                    imprimirRuta(ultimoReparto.entrega_id, ultimoReparto.folio);
                }
            }
        } catch (error) {
            console.error('Error al cargar repartos:', error);
        }
    }
</script>