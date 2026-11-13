<!-- MODAL PRINCIPAL -->
<div class="modal fade" id="modalAjusteFaltante" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">

            <!-- HEADER -->
            <div class="modal-header bg-danger text-white px-4 py-3 border-0">
                <h5 class="modal-title fw-semibold d-flex align-items-center gap-2">
                    <i class="bi bi-diagram-3-fill fs-5"></i>
                    <span>Distribución de Faltantes</span>
                    <span id="ajF_folio" class="badge bg-white text-danger fw-bold ms-2 px-3 py-1"></span>
                </h5>
                <button type="button" class="btn-close btn-close-white opacity-75" data-bs-dismiss="modal"></button>
            </div>

            <form id="ajF_form">
                <div class="modal-body px-4 py-4">

                    <input type="hidden" name="compra_id" id="ajF_compra_id">

                    <!-- Campos ocultos para almacenar la selección de excedente si se confirma -->
                    <input type="hidden" name="excedente_confirmado" id="ajF_excedente_confirmado" value="0">
                    <input type="hidden" name="excedente_almacen_id" id="ajF_excedente_almacen_id" value="">
                    <input type="hidden" name="excedente_cantidad" id="ajF_excedente_cantidad" value="0">
                    <input type="hidden" name="excedente_producto_id" id="ajF_excedente_producto_id" value="">

                    <!-- ALERTA PREMIUM -->
                    <div
                        class="d-flex align-items-start gap-3 p-3 mb-4 rounded-4 shadow-sm border-start border-4 border-danger">
                        <div>
                            <i class="bi bi-exclamation-triangle-fill text-danger fs-4"></i>
                        </div>
                        <div class="small text-body-secondary">
                            <div class="fw-semibold mb-1">Control de Entradas</div>
                            Habilite el almacén de destino y después capture la cantidad recibida para evitar errores en
                            inventario.
                        </div>
                    </div>

                    <!-- CONTENEDOR DE PRODUCTOS -->
                    <div id="ajF_lista_productos" class="row g-4">
                        <!-- contenido dinámico -->
                    </div>

                </div>

                <!-- FOOTER -->
                <div class="modal-footer px-4 py-3 border-0 d-flex justify-content-between">

                    <button type="button" class="btn btn-light rounded-pill px-4 shadow-sm" data-bs-dismiss="modal">
                        Cancelar
                    </button>

                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-outline-danger rounded-pill px-4 fw-semibold shadow-sm"
                            onclick="ajF_aplicarFaltantesCompra()">
                            <i class="bi bi-arrow-repeat me-1"></i> Ajustar compra
                        </button>

                        <button type="button" class="btn btn-danger rounded-pill px-4 fw-bold shadow"
                            onclick="ajF_procesarAjuste()">
                            <i class="bi bi-check-circle-fill me-1"></i> Registrar entrada
                        </button>
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>

<!-- MODAL PARA CONFIRMAR Y ASIGNAR EXCEDENTE -->
<div class="modal fade" id="modalConfirmarExcedente" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-warning text-dark border-0">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-exclamation-circle-fill me-2"></i>Cantidad Excedida Detectada
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <p class="mb-3 fs-6">
                    Has sobrepasado la cantidad pendiente asignada. ¿Deseas guardar este sobrante como un
                    <b>excedente</b> en el inventario?
                </p>

                <div class="bg-light p-3 rounded-3 mb-3 border">
                    <div class="d-flex justify-content-between mb-1 align-items-center gap-2">
                        <span class="text-secondary">Cantidad Sobrante:</span>
                        <input type="number" id="ajF_excedente_cantidadReal"
                            class="form-control form-control-sm w-auto">
                        <span id="ajF_excedente_lbl" class="fw-bold text-danger">0.00</span>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Selecciona el Almacén para el Excedente:</label>
                    <select id="ajF_excedente_almacen" class="form-select border-warning">
                        <!-- Opciones cargadas dinámicamente -->
                    </select>
                </div>
            </div>
            <div class="modal-footer border-0 d-flex justify-content-end gap-2">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal"
                    onclick="ajF_cancelarExcedente()">
                    Cancelar edición
                </button>
                <button type="button" class="btn btn-warning rounded-pill px-4 fw-bold" onclick="ajF_asignarExtra()">
                    Continuar y Asignar Excedente
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    .ajF-pointer {
        cursor: pointer;
    }

    #modalAjusteFaltante .form-switch .form-check-input:checked {
        background-color: #dc3545;
        border-color: #dc3545;
    }
</style>

<script>
    // ═══════════════════════════════════════════════════════════
    // Estado global (namespace para evitar colisiones)
    // ═══════════════════════════════════════════════════════════
    const ajF_State = {
        almacenes: [],
        idCompra: 0,
        prodIdTemp: 0,
        excedenteTemp: { prodId: null, cantidad: 0, factor: 1, inputElem: null }
    };

    // ═══════════════════════════════════════════════════════════
    // Abrir modal principal
    // ═══════════════════════════════════════════════════════════
    function abrirModalAjuste(id, folio) {
        const modalElem = document.getElementById('modalAjusteFaltante');
        const modal = bootstrap.Modal.getOrCreateInstance(modalElem);

        document.getElementById('ajF_folio').innerText = folio;
        document.getElementById('ajF_compra_id').value = id;
        ajF_State.idCompra = id;

        const contenedor = document.getElementById('ajF_lista_productos');
        contenedor.innerHTML = '<div class="col-12 text-center py-5"><div class="spinner-border text-danger"></div></div>';

        modal.show();

        ajF_State.almacenes = <?= json_encode($almacenes ?? 0) ?>;

        fetch(`/cfsistem/app/controllers/egresosController.php?action=obtenerFaltantes&compra_id=${id}`)
            .then(res => res.json())
            .then(data => {
                contenedor.innerHTML = '';

                data.forEach(p => {
                    let tablaAlmacenes = `
                    <table class="table table-sm align-middle mb-0">
                        <thead class="text-body-secondary" style="font-size: 0.75rem;">
                            <tr>
                                <th width="50" class="text-center">Envío</th>
                                <th>Almacén Destino</th>
                                <th width="140">Cantidad</th>
                            </tr>
                        </thead>
                        <tbody>`;

                    ajF_State.almacenes.forEach(alm => {
                        tablaAlmacenes += `
                        <tr>
                            <td class="text-center">
                                <div class="form-check form-switch d-inline-block">
                                    <input class="form-check-input ajF-pointer" type="checkbox"
                                           id="ajF_chk_${p.producto_id}_${alm.id}"
                                           data-prod-id="${p.producto_id}"
                                           data-alm-id="${alm.id}"
                                           onchange="ajF_toggleAlmacen(this, ${p.producto_id}, ${alm.id})">
                                </div>
                            </td>
                            <td class="small fw-semibold text-secondary">${alm.nombre}</td>
                            <td>
                                <input type="number"
                                       id="ajF_in_vis_${p.producto_id}_${alm.id}"
                                       name="distribucion[${p.producto_id}][${alm.id}]1"
                                       class="form-control form-control-sm border-danger ajF-input-visible"
                                       data-prod-id="${p.producto_id}"
                                       data-alm-id="${alm.id}"
                                       data-max="${p.cantidad_pendiente}"
                                       data-factor="${p.factor_conversion}"
                                       disabled
                                       placeholder="0.00"
                                       step=".01"
                                       min="0"
                                       oninput="ajF_recalcularRestante(${p.producto_id}, ${p.factor_conversion}, this)">

                                <input type="hidden"
                                       id="ajF_in_hid_${p.producto_id}_${alm.id}"
                                       name="distribucion[${p.producto_id}][${alm.id}]"
                                       class="ajF-input-hidden"
                                       data-prod-id="${p.producto_id}"
                                       data-alm-id="${alm.id}"
                                       data-max="${p.cantidad_pendiente}"
                                       disabled>
                            </td>
                        </tr>`;
                    });

                    tablaAlmacenes += `</tbody></table>`;

                    const restanteInicial = (parseFloat(p.cantidad_pendiente) / parseFloat(p.factor_conversion)).toFixed(2);

                    contenedor.innerHTML += `
                    <div class="col-lg-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header border-bottom-0 py-3 d-flex justify-content-between align-items-center">
                                <h6 class="fw-bold mb-0">${p.nombre}</h6>
                                <span class="badge rounded-pill bg-danger-subtle text-danger border border-danger-subtle">
                                    Pendiente: ${restanteInicial} ${p.unidad_reporte}
                                </span>
                            </div>
                            <div class="card-body pt-0">
                                <div class="border rounded-3 overflow-hidden">
                                    ${tablaAlmacenes}
                                </div>
                                <div class="small text-danger fw-bold mt-2 ajF-restante-prod" data-restante="${p.producto_id}">
                                    Restante por asignar: <b>${restanteInicial}</b>
                                </div>
                            </div>
                        </div>
                    </div>`;
                });
            })
            .catch(err => {
                contenedor.innerHTML = '<div class="col-12 text-center text-danger py-4">Error al cargar los productos faltantes.</div>';
                console.error(err);
            });
    }

    // ═══════════════════════════════════════════════════════════
    // Toggle de almacén (checkbox) — todo por ID
    // ═══════════════════════════════════════════════════════════
    function ajF_toggleAlmacen(check, prodId, almId) {
        const inputVisible = document.getElementById(`ajF_in_vis_${prodId}_${almId}`);
        const inputHidden = document.getElementById(`ajF_in_hid_${prodId}_${almId}`);

        if (!inputVisible || !inputHidden) return;

        if (check.checked) {
            inputVisible.disabled = false;
            inputHidden.disabled = false;
            inputVisible.focus();
        } else {
            inputVisible.disabled = true;
            inputHidden.disabled = true;
            inputVisible.value = '';
            inputHidden.value = '';

            const factor = parseFloat(inputVisible.dataset.factor) || 1;
            ajF_recalcularRestante(prodId, factor, inputVisible);
        }
    }

    // ═══════════════════════════════════════════════════════════
    // Recalcular restante — itera por ID/data-attr
    // ═══════════════════════════════════════════════════════════
    function ajF_recalcularRestante(prodId, factor = 1, inputModificado = null) {
        const inputs = document.querySelectorAll(
            `input.ajF-input-visible[data-prod-id="${prodId}"]`
        );

        let suma = 0;
        let maximo = 0;
        ajF_State.prodIdTemp = prodId;

        inputs.forEach(input => {
            if (!input.disabled) {
                const valor = parseFloat(input.value) || 0;
                suma += valor * factor;
                maximo = parseFloat(input.dataset.max) || 0;

                const almId = input.dataset.almId;
                const hidden = document.getElementById(`ajF_in_hid_${prodId}_${almId}`);
                if (hidden) {
                    hidden.value = (valor * factor).toFixed(2);
                }
            }
        });

        suma = Math.round(suma * 1000) / 1000;

        // Si excede el máximo → modal de confirmación
        if (suma > maximo) {
            const exceso = (suma - maximo) / factor;

            ajF_State.excedenteTemp = {
                prodId: prodId,
                cantidad: exceso,
                factor: factor,
                inputElem: inputModificado || document.activeElement
            };

            document.getElementById('ajF_excedente_lbl').innerText = exceso.toFixed(2);
            document.getElementById('ajF_excedente_cantidadReal').value = exceso * factor;

            const selAlmacen = document.getElementById('ajF_excedente_almacen');
            selAlmacen.innerHTML = '';
            ajF_State.almacenes.forEach(alm => {
                selAlmacen.innerHTML += `<option value="${alm.id}">${alm.nombre}</option>`;
            });

            const modalExcedente = new bootstrap.Modal(document.getElementById('modalConfirmarExcedente'));
            modalExcedente.show();
            return;
        }

        const restante = Math.max(0, (Math.round((maximo - suma) * 1000) / 1000) / factor);

        const textos = document.querySelectorAll(`.ajF-restante-prod[data-restante="${prodId}"]`);
        textos.forEach(texto => {
            texto.innerHTML = `Restante por asignar: <b>${restante.toFixed(2)}</b>`;
        });
    }

    // ═══════════════════════════════════════════════════════════
    // Asignar extra → endpoint actualizarExcedente
    // ═══════════════════════════════════════════════════════════
    async function ajF_asignarExtra() {
        Swal.fire({
            title: 'Consultando servidor...',
            text: 'Por favor espere un momento',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });

        const id = ajF_State.idCompra;
        const cantidad = document.getElementById('ajF_excedente_cantidadReal').value;
        const prodId = ajF_State.prodIdTemp;

        const formData = new FormData();
        formData.append('compra_id', id);
        formData.append('producto_id', prodId);
        formData.append('excedente', cantidad);

        try {
            const url = '/cfsistem/app/controllers/egresosController.php?action=actualizarExcedente';
            const respuesta = await fetch(url, { method: 'POST', body: formData });

            if (!respuesta.ok) {
                throw new Error(`Respuesta no válida del servidor (HTTP ${respuesta.status})`);
            }

            const resultado = await respuesta.json();
            console.log('Respuesta del servidor:', resultado);

            if (resultado.success) {
                Swal.fire({
                    icon: 'success',
                    title: '¡Operación Exitosa!',
                    text: resultado.message || 'La consulta se procesó correctamente.',
                    timer: 2000,
                    showConfirmButton: false
                });
                ajF_aplicarFaltantesCompra();
            } else {
                Swal.fire({
                    icon: 'warning',
                    title: 'Aviso del Servidor',
                    text: resultado.message || 'No se pudo completar la solicitud.',
                    confirmButtonColor: '#3085d6'
                });
            }
        } catch (error) {
            console.error('Error al consultar el servidor:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error de Conexión',
                text: 'No se pudo establecer comunicación con el servidor.',
                confirmButtonColor: '#d33'
            });
        }
    }

    // ═══════════════════════════════════════════════════════════
    // Guardar excedente en campos ocultos
    // ═══════════════════════════════════════════════════════════
    function ajF_guardarExcedenteModal() {
        const almacenId = document.getElementById('ajF_excedente_almacen').value;

        document.getElementById('ajF_excedente_confirmado').value = "1";
        document.getElementById('ajF_excedente_almacen_id').value = almacenId;
        document.getElementById('ajF_excedente_cantidad').value = ajF_State.excedenteTemp.cantidad;
        document.getElementById('ajF_excedente_producto_id').value = ajF_State.excedenteTemp.prodId;

        const modalExcedenteElem = document.getElementById('modalConfirmarExcedente');
        const modalExcedente = bootstrap.Modal.getInstance(modalExcedenteElem);
        if (modalExcedente) modalExcedente.hide();

        const textos = document.querySelectorAll(`.ajF-restante-prod[data-restante="${ajF_State.excedenteTemp.prodId}"]`);
        textos.forEach(texto => {
            texto.innerHTML = `Restante por asignar: <b>0.00</b> <span class="badge bg-warning text-dark ms-2">Excedente asignado</span>`;
        });
    }

    // ═══════════════════════════════════════════════════════════
    // Cancelar excedente
    // ═══════════════════════════════════════════════════════════
    function ajF_cancelarExcedente() {
        const temp = ajF_State.excedenteTemp;

        if (temp.inputElem) {
            temp.inputElem.value = '';
            const almId = temp.inputElem.dataset.almId;
            const hidden = document.getElementById(`ajF_in_hid_${temp.prodId}_${almId}`);
            if (hidden) hidden.value = '0';

            ajF_recalcularRestante(temp.prodId, temp.factor, temp.inputElem);
        }

        document.getElementById('ajF_excedente_confirmado').value = "0";
        document.getElementById('ajF_excedente_almacen_id').value = "";
        document.getElementById('ajF_excedente_cantidad').value = "0";
        document.getElementById('ajF_excedente_producto_id').value = "";
    }

    // ═══════════════════════════════════════════════════════════
    // Aplicar faltantes (resetea compra)
    // ═══════════════════════════════════════════════════════════
    function ajF_aplicarFaltantesCompra() {
        const compra_id = document.getElementById('ajF_compra_id').value;

        Swal.fire({
            title: '¿Aplicar faltantes?',
            text: 'Se descontarán del total y los faltantes se pondrán en 0.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, aplicar',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Procesando...',
                    text: 'Aplicando cambios',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });

                fetch(`/cfsistem/app/controllers/egresosController.php?action=aplicarFaltantesCompras&compra_id=${compra_id}`)
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Actualizado',
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => location.reload());
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: data.message || 'No se pudo aplicar'
                            });
                        }
                    })
                    .catch(err => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error de conexión',
                            text: 'No se pudo conectar con el servidor'
                        });
                        console.error(err);
                    });
            }
        });
    }

    // ═══════════════════════════════════════════════════════════
    // Procesar ajuste → construye FormData MANUALMENTE por IDs
    // ═══════════════════════════════════════════════════════════
    function ajF_procesarAjuste() {
        const formData = new FormData();

        // Datos base (todos por ID)
        formData.append('compra_id',
            document.getElementById('ajF_compra_id').value);
        formData.append('excedente_confirmado',
            document.getElementById('ajF_excedente_confirmado').value);
        formData.append('excedente_almacen_id',
            document.getElementById('ajF_excedente_almacen_id').value);
        formData.append('excedente_cantidad',
            document.getElementById('ajF_excedente_cantidad').value);
        formData.append('excedente_producto_id',
            document.getElementById('ajF_excedente_producto_id').value);

        // Distribución: iterar SOLO los hidden habilitados (fuente de verdad)
        let hayDatos = false;

        document.querySelectorAll('input.ajF-input-hidden').forEach(hidden => {
            if (hidden.disabled) return;

            const cant = parseFloat(hidden.value) || 0;
            if (cant <= 0) return;

            const prodId = hidden.dataset.prodId;
            const almId = hidden.dataset.almId;

            // Cantidad base (lo que realmente se descuenta)
            formData.append(`distribucion[${prodId}][${almId}]`, hidden.value);

            // Cantidad visible con sufijo "1" (compatibilidad con backend actual)
            const vis = document.getElementById(`ajF_in_vis_${prodId}_${almId}`);
            if (vis && vis.value !== '') {
                formData.append(`distribucion[${prodId}][${almId}]1`, vis.value);
            }

            hayDatos = true;
        });

        if (!hayDatos) {
            return Swal.fire('Sin datos', 'Habilite al menos un almacén e ingrese cantidad.', 'warning');
        }

        Swal.fire({
            title: '¿Confirmar Ingreso?',
            text: "Se afectará el stock de los almacenes habilitados.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            confirmButtonText: 'Sí, registrar'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('/cfsistem/app/controllers/egresosController.php?action=procesarAjusteFaltante', {
                    method: 'POST',
                    body: formData
                })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire('¡Éxito!', data.message, 'success').then(() => location.reload());
                        } else {
                            Swal.fire('Error', data.message, 'error');
                        }
                    })
                    .catch(err => {
                        Swal.fire('Error', 'No se pudo procesar la solicitud.', 'error');
                        console.error(err);
                    });
            }
        });
    }
</script>