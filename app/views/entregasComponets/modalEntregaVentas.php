<style>
    /* ═══════════════════════════════════════════════════════════
       MODAL DESPACHO VENTA TOTAL — z-index 100080
       ═══════════════════════════════════════════════════════════ */
    #modalDespachoVentaTotal {
        z-index: 100080 !important;
    }
</style>

<div class="modal fade" id="modalDespachoVentaTotal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 25px;">
            <div class="modal-header border-0 border border-subtle"
                style="border-radius: 25px 25px 0 0; padding: 1.5rem 2rem;">
                <div class="bg-success text-white rounded p-2 d-flex align-items-center justify-content-center me-3"
                    style="width: 45px; height: 45px; border-radius: 12px !important;">
                    <i class="bi bi-box-seam-fill fs-4"></i>
                </div>
                <div>
                    <h5 class="modal-title fw-bold mb-0">Despacho Masivo Por Venta</h5>
                    <span class="bg-light card-title-text border mt-1" id="txtFolioVenta">Cargando...</span>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body px-4 py-3">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Producto</th>
                                <th>Disponible</th>
                                <th>Comprada</th>
                                <th>Restante</th>
                                <th>Entregar</th>
                                <th>Lote</th>
                            </tr>
                        </thead>
                        <tbody id="listaItemsDespacho"></tbody>
                    </table>
                </div>

                <div id="seccionLogisticaMasiva" class="d-none animate__animated animate__fadeIn">
                    <hr class="my-4 opacity-10">

                    <div class="p-3 border rounded-4 border border-subtle shadow-sm mb-3">
                        <label class="text-uppercase fw-bold text-primary mb-2 d-block"
                            style="font-size: 0.7rem; letter-spacing: 1.2px;">Método de Salida</label>
                        <div class="btn-group w-100" role="group">
                            <input type="radio" class="btn-check" name="tipo_entrega_masiva" id="optPatio" value="patio"
                                checked onchange="toggleFormRuta(false)">
                            <label class="btn btn-outline-success rounded-start-pill py-2 fw-bold" for="optPatio">
                                <i class="bi bi-box-seam me-2"></i>ENTREGA EN PATIO
                            </label>

                            <input type="radio" class="btn-check" name="tipo_entrega_masiva" id="optRuta" value="ruta"
                                onchange="toggleFormRuta(true)">
                            <label class="btn btn-outline-primary rounded-end-pill py-2 fw-bold" for="optRuta">
                                <i class="bi bi-truck me-2"></i>ASIGNAR RUTA
                            </label>
                        </div>
                    </div>

                    <div id="formRutaIntegrado" class="animate__animated animate__fadeInUp">
                        <div id="wrapperLogistica" class="p-4 rounded-4 shadow-sm"
                            style="transition: all 0.3s ease; border: 1px solid;">
                            <div class="row g-3">
                                <div class="col-12">
                                    <div id="contenedorDireccion"
                                        class="p-3 rounded-4 mb-1 border border border-subtle shadow-sm">
                                        <label id="lblDinamicoPrincipal" class="small fw-bold text-body-secondary mb-1"
                                            style="font-size: 0.65rem;">PUNTO DE ENTREGA / OBRA</label>
                                        <div class="input-group">
                                            <span class="input-group-text border border-subtle border-0">
                                                <i id="iconDinamico" class="bi bi-geo-alt-fill text-danger"></i>
                                            </span>
                                            <textarea id="mv_direccion" class="form-control border-0 p-2 text-uppercase"
                                                rows="2" style="font-size: 0.9rem; resize: none;"
                                                placeholder="Dirección exacta..."></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6" id="colVehiculo">
                                    <label class="small fw-bold text-body-secondary mb-1">UNIDAD / VEHÍCULO</label>
                                    <select id="mv_vehiculo_id"
                                        class="form-select border-0 shadow-sm rounded-3 p-3 border border-subtle"></select>
                                </div>
                                <div class="col-md-6">
                                    <label id="lblPersonal" class="small fw-bold text-body-secondary mb-1">CHOFER
                                        RESPONSABLE</label>
                                    <select id="mv_chofer_id"
                                        class="form-select border-0 shadow-sm rounded-3 p-3 border border-subtle"></select>
                                </div>

                                <div class="col-12">
                                    <label class="small fw-bold text-body-secondary mb-1">AYUDANTES DE CARGA
                                        (OPCIONAL)</label>
                                    <select id="mv_tripulantes"
                                        class="form-select border-0 shadow-sm rounded-3 p-2 border border-subtle"
                                        style="font-size: 0.85rem;"></select>
                                    <small class="text-body-secondary mt-2 d-block" style="font-size: 0.6rem;">* Mantén
                                        presionada la tecla <b>Ctrl</b> para elegir varios.</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer border-0 border border-subtle py-3 px-4" style="border-radius: 0 0 25px 25px;">
                <button type="button" class="btn btn-light rounded-pill px-4 fw-bold"
                    data-bs-dismiss="modal">Cancelar</button>
                <button type="button" id="btnEjecutarDespachoMasivo"
                    class="btn btn-success rounded-pill px-5 fw-bold shadow" disabled>
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

    function toggleFormRuta(esRuta) {
        const btnConfirmar = $('#btnEjecutarDespachoMasivo');

        if (esRuta) {
            $('#lblDinamicoPrincipal').text('PUNTO DE ENTREGA / OBRA (EDITABLE)');
            $('#iconDinamico').removeClass('bi-person-fill text-success').addClass('bi-geo-alt-fill text-danger');
            $('#mv_direccion').attr('placeholder', 'Dirección exacta de entrega...');
            $('#lblPersonal').text('CHOFER RESPONSABLE');
            $('#colVehiculo').fadeIn();
            btnConfirmar.removeClass('btn-success').addClass('btn-primary');
        } else {
            $('#lblDinamicoPrincipal').text('NOTAS / QUIÉN RECIBE (OPCIONAL)');
            $('#iconDinamico').removeClass('bi-geo-alt-fill text-danger').addClass('bi-person-fill text-success');
            $('#mv_direccion').attr('placeholder', 'Ej. Se lo lleva el cliente en su camioneta...');
            $('#lblPersonal').text('DESPACHADOR RESPONSABLE (PATIO)');
            $('#colVehiculo').hide();
            btnConfirmar.removeClass('btn-primary').addClass('btn-success');
        }
    }

    let modal = null;
    window.carrito = [];   // indexado por dvid (string)

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
            const primerId = almacenId;

            if (!idsParaProcesar.length) {
                contenedor.innerHTML = `<div class="alert alert-warning mx-2 small">No hay productos pendientes de despacho.</div>`;
                return;
            }

            // 2. Lotes (en paralelo, por índice del array ORIGINAL — no filtrado)
            window.carrito = {};
            const todos_los_lotes = await Promise.all(
                idsParaProcesar.map(item => lotes(item.producto_id, almacenId))
            );

            contenedor.innerHTML = '';

            idsParaProcesar.forEach((item, index) => {
                const cantidadRealFaltante = (Number(item.cantidad) || 0) - (Number(item.cantidad_entregada) || 0);

                // ✅ FIX: NO hacemos return; si no hay faltante, renderizamos fila deshabilitada
                //          para que los índices del DOM coincidan 1:1 con idsParaProcesar.
                const sinFaltante = cantidadRealFaltante <= 0;

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

                // ✅ FIX: max con 4 decimales para no capar 1.1 / 2.1
                const maxInput = (cantidad_maxima / factorFila).toFixed(4);

                // ✅ FIX: id único por dvid (no por index)
                const dvid = String(item.dvid);
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
                               class="form-control form-control-sm input-entrega1"
                               data-factor="${factorFila}"
                               data-dvid="${dvid}"
                               data-id="${item.producto_id}"
                               ${sinFaltante ? 'disabled' : ''}>
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
                                ${sinFaltante ? 'disabled' : ''}
                                required>
                            <option value="">Seleccione lote</option>
                        </select>
                    </td>
                `;

                // Lotes en el select
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

                // Visible → Hidden (unidad base para backend)
                const inputVisible = tr.querySelector('.input-entrega1');
                const inputHidden = tr.querySelector('.input-entrega');

                inputVisible.addEventListener('input', () => {
                    const f = parseFloat(inputVisible.dataset.factor) || 1;
                    const v = parseFloat(inputVisible.value);
                    inputHidden.value = (isNaN(v) || v <= 0) ? '' : (v * f).toFixed(4);
                });

                // Carrito indexado por dvid
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
                fetch(`${URL_ENTREGAS}?ajax=get_recursos_sucursal&almacen_id=${almacenId}`)
            ]);

            const resDetalle = await respDetalle.json();
            const resRecursos = await respRecursos.json();

            if (resDetalle.success && resRecursos.success) {
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

                // Cambio de vehículo (con fallback)
                $('#mv_vehiculo_id').off('change').on('change', function () {
                    const vehiculo_id = $(this).val();
                    if (!vehiculo_id) return;

                    fetch(`${URL_ENTREGAS}?ajax=get_datos_vehiculo&vehiculo_id=${vehiculo_id}`)
                        .then(r => r.json())
                        .then(res => {
                            console.log("Respuesta servidor:", res);

                            $('#mv_chofer_id').val('').prop('disabled', false);
                            $('#mv_tripulantes').val('');

                            if (res.success && res.data &&
                                (res.data.encargado || (res.data.tripulantes && res.data.tripulantes.length))) {
                                const data = res.data;

                                if (data.encargado && data.encargado.id) {
                                    const idChofer = String(data.encargado.id);
                                    if ($(`#mv_chofer_id option[value="${idChofer}"]`).length) {
                                        $('#mv_chofer_id').val(idChofer).trigger('change');
                                        $('#mv_chofer_id').prop('disabled', true);
                                    }
                                }

                                if (Array.isArray(data.tripulantes) && data.tripulantes.length > 0) {
                                    const idPrimerTripulante = String(data.tripulantes[0].id);
                                    if ($(`#mv_tripulantes option[value="${idPrimerTripulante}"]`).length) {
                                        $('#mv_tripulantes').val(idPrimerTripulante).trigger('change');
                                    }
                                }
                            } else {
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

                // Exclusión chofer → tripulante
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

    async function procesarEntregaFinal() {
        const fd = new FormData();
        const itemsValidos = [];   // guardamos {dvid, cantBase, loteId}

        // ✅ FIX: leer inputs visibles, aplicar factor, validar lote por dvid
        $('.input-entrega1').each(function () {
            if ($(this).prop('disabled')) return;

            const factor = parseFloat($(this).data('factor')) || 1;
            const cantVisible = parseFloat($(this).val());
            const dvid = String($(this).data('dvid'));

            if (!isNaN(cantVisible) && cantVisible > 0) {
                itemsValidos.push({
                    dvid,
                    cantVisible,
                    cantBase: (cantVisible * factor).toFixed(4),
                    factor
                });
            }
        });

        if (!itemsValidos.length) {
            Swal.fire('Atención', 'Indique al menos una cantidad válida para entregar', 'warning');
            return false;
        }

        // Validar lote por cada item con cantidad
        for (const it of itemsValidos) {
            const loteVal = $(`#merma_lote_${it.dvid}`).val();
            if (!loteVal) {
                Swal.fire('Atención', `Debes seleccionar un lote para el producto con cantidad (dvid ${it.dvid}).`, 'warning');
                return false;
            }
            it.loteId = loteVal;
        }

        // Datos del formulario
        const tipo = $('input[name="tipo_entrega_masiva"]:checked').val();
        const vehiculoId = $('#mv_vehiculo_id').val();
        const choferId = $('#mv_chofer_id').val();
        const tripulanteId = $('#mv_tripulantes').val();
        const direccion = $('#mv_direccion').val() ? $('#mv_direccion').val().trim() : '';

        if (tipo === 'ruta') {
            if (!vehiculoId) { Swal.fire({ icon: 'error', title: 'Campos incompletos', text: 'Por favor, selecciona un vehículo.', confirmButtonColor: '#d33' }); return false; }
            if (!choferId) { Swal.fire({ icon: 'error', title: 'Campos incompletos', text: 'Por favor, selecciona un chofer.', confirmButtonColor: '#d33' }); return false; }
        }
        if (!direccion) {
            Swal.fire({ icon: 'error', title: 'Campos incompletos', text: 'Por favor, escribe una dirección.', confirmButtonColor: '#d33' });
            return false;
        }

        // Append productos/lotes al FormData por dvid
        itemsValidos.forEach(it => {
            fd.append(`productos[${it.dvid}]`, it.cantBase);
            fd.append(`lotes[${it.dvid}]`, it.loteId);
        });

        fd.append('tipo_logistica', tipo);
        fd.append('vehiculo_id', tipo === 'ruta' ? vehiculoId : 0);
        fd.append('chofer_id', choferId || '');
        fd.append('tripulante_id', tripulanteId || '');
        fd.append('direccion', direccion);
        fd.append('venta_id', ventaActual.info.id);

        console.log('Datos enviados:', Object.fromEntries(fd));

        try {
            const res = await fetch(`${URL_CONTROLLER}?action=guardarEntregaMasiva`, { method: 'POST', body: fd });
            const result = await res.json();
            console.log("Respuesta servidor:", result);

            if (result.status === 'success') {
                if (typeof modalObj !== 'undefined' && modalObj && typeof modalObj.hide === 'function') modalObj.hide();
                if (modal && typeof modal.hide === 'function') modal.hide();
                if (typeof getVentas === 'function') getVentas();

                Swal.fire({ title: '¡Listo!', text: 'Entrega guardada correctamente', icon: 'success', showConfirmButton: true });

                if (Array.isArray(result.ids) && result.ids.length) {
                    await ejecutarSalidaMasivaFinal(result.ids, itemsValidos);
                }
                return true;
            } else {
                Swal.fire('No se pudo entregar', result.message || 'Error desconocido', 'error');
                return false;
            }
        } catch (e) {
            console.error("Error al procesar entrega:", e);
            Swal.fire('Error Técnico', 'Hubo un problema de conexión con el servidor', 'error');
            return false;
        }
    }

    // Listener de lote con dvid
    document.addEventListener('change', function (e) {
        if (!e.target.matches('select.lote-select')) return;

        const dvid = String(e.target.dataset.dvid);
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

            // ✅ FIX: el lote se busca por dvid, NO por posición i
            result_ids.forEach(row => {
                const movId = row.movimiento_id ?? row;
                const dvid = String(row.dvid ?? row);
                const loteVal = $(`#merma_lote_${dvid}`).val() || '';
                formData.append('ids_movimientos[]', movId);
                formData.append('lotes[]', loteVal);
            });

            console.log('Payload final:', Object.fromEntries(formData));

            const resp = await fetch('entregasController.php', { method: 'POST', body: formData });
            const res = await resp.json();

            if (res.success) {
                if (typeof modalObj !== 'undefined' && modalObj && typeof modalObj.hide === 'function') modalObj.hide();
                if (modal && typeof modal.hide === 'function') modal.hide();
                if (typeof getVentas === 'function') getVentas();

                Swal.fire({ icon: 'success', title: '¡Éxito!', text: res.ids || 'Despacho registrado', showConfirmButton: true });

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