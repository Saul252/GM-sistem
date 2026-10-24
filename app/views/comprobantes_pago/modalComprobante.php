<style>
    /* ═══════════════════════════════════════════════════════════
   MODAL COTIZACIÓN — Estilo Bootstrap + CSS Custom
   Compatible con modo claro / oscuro
   ═══════════════════════════════════════════════════════════ */

    /* ─── RESET: no heredar uppercase del body ─── */
    #modalCotizacion,
    #modalCotizacion * {
        text-transform: none;
    }

    #modalCotizacion .text-uppercase {
        text-transform: uppercase !important;
    }

    /* ─── CONTENEDOR PRINCIPAL ─── */
    #modalCotizacion .modal-cotizacion-content {
        border: none;
        border-radius: 20px;
        overflow: hidden;
        width: 95%;
        max-width: 1140px;
        margin: 0 auto;
        background: var(--bs-body-bg, #ffffff);
        box-shadow:
            0 15px 50px rgba(15, 23, 42, 0.2),
            0 8px 24px rgba(15, 23, 42, 0.12);
    }

    [data-bs-theme="dark"] #modalCotizacion .modal-cotizacion-content {
        box-shadow:
            0 15px 50px rgba(0, 0, 0, 0.6),
            0 8px 24px rgba(0, 0, 0, 0.4);
    }

    /* ─── ICONO DEL HEADER ─── */
    #modalCotizacion .icon-header-cotizacion {
        width: 45px;
        height: 45px;
        border-radius: 12px;
        background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
        color: #ffffff;
        box-shadow:
            0 4px 12px rgba(99, 102, 241, 0.35),
            inset 0 1px 0 rgba(255, 255, 255, 0.25);
        transition: transform 0.3s ease;
    }

    #modalCotizacion .icon-header-cotizacion:hover {
        transform: scale(1.05) rotate(-3deg);
    }

    /* ─── BLOQUE DE CAMPOS ─── */
    #modalCotizacion .bloque-campos-cotizacion {
        background: var(--bs-tertiary-bg, #f8fafc);
        border-color: var(--bs-border-color, #e2e8f0) !important;
        transition: border-color 0.2s ease;
    }

    /* ─── LABELS ─── */
    #modalCotizacion .form-label {
        font-size: 0.72rem;
        letter-spacing: 0.03em;
        color: var(--bs-secondary-color, #475569);
        margin-bottom: 0.5rem;
        display: block;
    }

    #modalCotizacion .form-label i {
        color: #6366f1;
        margin-right: 2px;
    }

    [data-bs-theme="dark"] #modalCotizacion .form-label i {
        color: #818cf8;
    }

    /* ─── INPUTS Y SELECTS ─── */
    #modalCotizacion .form-control,
    #modalCotizacion .form-select {
        font-size: 0.88rem;
        font-weight: 500;
        padding: 0.6rem 0.9rem;
        border-radius: 10px;
        border: 1.5px solid var(--bs-border-color, #e2e8f0);
        transition: all 0.2s ease;
    }

    #modalCotizacion .form-control:focus,
    #modalCotizacion .form-select:focus {
        border-color: #6366f1;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.12);
        outline: none;
    }

    [data-bs-theme="dark"] #modalCotizacion .form-control:focus,
    [data-bs-theme="dark"] #modalCotizacion .form-select:focus {
        border-color: #818cf8;
        box-shadow: 0 0 0 3px rgba(129, 140, 248, 0.2);
    }

    /* ─── INPUT GROUPS ─── */
    #modalCotizacion .input-group>.input-group-text {
        background: var(--bs-tertiary-bg, #f8fafc);
        border: 1.5px solid var(--bs-border-color, #e2e8f0);
        border-right: none;
        border-radius: 10px 0 0 10px;
        color: var(--bs-secondary-color, #64748b);
        font-weight: 600;
        padding: 0 0.85rem;
    }

    #modalCotizacion .input-group>.form-control,
    #modalCotizacion .input-group>.form-select {
        border-radius: 0 10px 10px 0;
    }

    #modalCotizacion .input-group>.form-select:only-child {
        border-radius: 10px 0 0 10px;
    }

    #modalCotizacion .input-group>.btn-primary {
        border-radius: 0 10px 10px 0;
        border: none;
        padding: 0 1rem;
        transition: all 0.2s ease;
    }

    #modalCotizacion .input-group>.btn-primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(79, 70, 229, 0.35);
    }

    /* ─── MONTO ─── */
    #modalCotizacion .monto-input {
        font-weight: 700;
        color: #059669;
        font-feature-settings: 'tnum' 1;
    }

    [data-bs-theme="dark"] #modalCotizacion .monto-input {
        color: #34d399;
    }

    #modalCotizacion .input-group-text.text-success {
        color: #059669 !important;
        font-weight: 700;
        font-size: 1rem;
    }

    [data-bs-theme="dark"] #modalCotizacion .input-group-text.text-success {
        color: #34d399 !important;
    }

    /* ─── SELECT2 ─── */
    #modalCotizacion .select2-container--default .select2-selection--single {
        height: auto;
        padding: 0.6rem 0.9rem;
        border: 1.5px solid var(--bs-border-color, #e2e8f0);
        border-radius: 10px 0 0 10px;
        background: var(--bs-body-bg, #ffffff);
        transition: all 0.2s ease;
    }

    #modalCotizacion .select2-container--default .select2-selection--single .select2-selection__rendered {
        padding: 0;
        line-height: 1.4;
        color: var(--bs-body-color, #0f172a);
        font-size: 0.88rem;
        font-weight: 500;
    }

    #modalCotizacion .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 100%;
        right: 8px;
    }

    #modalCotizacion .select2-container--default.select2-container--focus .select2-selection--single,
    #modalCotizacion .select2-container--default.select2-container--open .select2-selection--single {
        border-color: #6366f1;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.12);
        outline: none;
    }

    [data-bs-theme="dark"] #modalCotizacion .select2-container--default .select2-selection--single {
        background: var(--bs-body-bg, #1e293b);
        border-color: var(--bs-border-color, #334155);
    }

    [data-bs-theme="dark"] #modalCotizacion .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: var(--bs-body-color, #f1f5f9);
    }

    /* ─── FOOTER ─── */
    #modalCotizacion .modal-footer .btn-light {
        padding: 0.6rem 1.5rem;
        font-size: 0.85rem;
        transition: all 0.2s ease;
    }

    #modalCotizacion .modal-footer .btn-light:hover {
        transform: translateY(-1px);
    }

    #modalCotizacion .modal-footer .btn-primary {
        padding: 0.7rem 2rem;
        font-size: 0.85rem;
        border: none;
        background: linear-gradient(180deg, #6366f1 0%, #4f46e5 100%);
        transition: all 0.2s ease;
        box-shadow:
            0 4px 12px rgba(79, 70, 229, 0.35),
            inset 0 1px 0 rgba(255, 255, 255, 0.25);
        position: relative;
        overflow: hidden;
    }

    #modalCotizacion .modal-footer .btn-primary:hover {
        background: linear-gradient(180deg, #4f46e5 0%, #4338ca 100%);
        transform: translateY(-1px);
        box-shadow:
            0 8px 20px rgba(79, 70, 229, 0.45),
            inset 0 1px 0 rgba(255, 255, 255, 0.3);
    }

    #modalCotizacion .modal-footer .btn-primary:active {
        transform: translateY(0) scale(0.98);
    }

    #modalCotizacion .modal-footer .btn-primary::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(120deg,
                transparent 30%,
                rgba(255, 255, 255, 0.3) 50%,
                transparent 70%);
        transition: left 0.6s ease;
        pointer-events: none;
    }

    #modalCotizacion .modal-footer .btn-primary:hover::before {
        left: 100%;
    }

    /* ─── ANIMACIÓN ─── */
    #modalCotizacion.fade .modal-dialog {
        transform: translateY(-24px) scale(0.96);
        transition: transform 0.35s cubic-bezier(0.34, 1.56, 0.64, 1);
    }

    #modalCotizacion.show .modal-dialog {
        transform: translateY(0) scale(1);
    }

    /* ─── RESPONSIVE ─── */
    @media (max-width: 768px) {
        #modalCotizacion .modal-cotizacion-content {
            width: calc(100% - 20px);
            border-radius: 16px;
        }

        #modalCotizacion .modal-header {
            padding: 1.25rem 1.25rem 0.5rem;
        }

        #modalCotizacion .modal-body {
            padding: 0.75rem 1.25rem;
        }

        #modalCotizacion .bloque-campos-cotizacion {
            padding: 1rem !important;
        }

        #modalCotizacion .modal-footer {
            padding: 0.75rem 1.25rem 1.25rem;
        }
    }

    /* ─── CALENDARIO DARK ─── */
    [data-bs-theme="dark"] #modalCotizacion input[type="date"]::-webkit-calendar-picker-indicator {
        filter: invert(0.7);
        cursor: pointer;
    }

    /* ─── SCROLLBAR ─── */
    #modalCotizacion .modal-body::-webkit-scrollbar {
        width: 8px;
    }

    #modalCotizacion .modal-body::-webkit-scrollbar-thumb {
        background: rgba(100, 116, 139, 0.3);
        border-radius: 4px;
    }

    #modalCotizacion .modal-body::-webkit-scrollbar-thumb:hover {
        background: rgba(100, 116, 139, 0.5);
    }
</style>

<div class="modal fade" id="modalCotizacion" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content modal-cotizacion-content">
            <form id="formSolicitud">

                <!-- ═══════ HEADER ═══════ -->
                <div class="modal-header border-0 pt-4 px-4 pb-2">
                    <div class="d-flex align-items-center">
                        <div class="icon-header-cotizacion d-flex align-items-center justify-content-center me-3">
                            <i class="bi bi-file-earmark-plus fs-4"></i>
                        </div>
                        <div>
                            <h4 class="fw-bold mb-0">Nuevo Comprobante / Depósito</h4>
                            <p class="text-body-secondary small mb-0">
                                Complete los datos para registrar el movimiento en el sistema
                            </p>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <!-- ═══════ BODY ═══════ -->
                <div class="modal-body px-4 pt-3">
                    <div class="row g-3 p-4 rounded-4 border align-items-end mb-2 bloque-campos-cotizacion">

                        <!-- Almacén -->
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-secondary mb-2">
                                <i class="bi bi-box-seam me-1"></i> Almacén de Cargo
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bi bi-geo-alt"></i>
                                </span>
                                <select name="almacen_id" id="almacen_id" class="form-select" required>
                                    <option value="">Seleccionar ubicación...</option>
                                    <?php foreach ($almacenes as $a): ?>
                                        <option value="<?= $a['id'] ?>">
                                            <?= htmlspecialchars($a['nombre']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <!-- Cliente -->
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-secondary mb-2">
                                <i class="bi bi-person me-1"></i> Cliente
                            </label>
                            <div class="input-group">
                                <select name="cliente_id" id="cliente_id" class="form-select select2-modal" required>
                                    <option value="">Seleccionar cliente...</option>
                                </select>
                                <button class="btn btn-primary flex-shrink-0" type="button"
                                    onclick="abrirModalNuevoCliente()" title="Registrar nuevo cliente">
                                    <i class="bi bi-person-plus-fill"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Órdenes -->
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-secondary mb-2">
                                <i class="bi bi-bookmark me-1"></i> Número(s) de órdenes
                            </label>
                            <input type="text" placeholder="Ej. Pago compra orden #123..." id="numero_venta"
                                name="numero_venta" class="form-control">
                        </div>

                        <!-- Fecha Depósito (solo rol 1) -->
                        <?php if ($rolAct == 1): ?>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold text-secondary mb-2">
                                    <i class="bi bi-calendar3 me-1"></i> Fecha de Depósito
                                </label>
                                <input type="date" id="fecha_deposito" value="<?= date('Y-m-d') ?>" name="fecha_deposito"
                                    class="form-control" required>
                            </div>
                        <?php endif; ?>

                        <!-- Método de pago -->
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-secondary mb-2">
                                <i class="bi bi-credit-card me-1"></i> Método de Pago
                            </label>
                            <select id="metodo_pago_m" name="metodo_pago_m" class="form-select fw-bold">
                                <option value="Efectivo">💵 Efectivo</option>
                                <option value="Transferencia">🏦 Transferencia</option>
                                <option value="Tarjeta">💳 Tarjeta</option>
                            </select>
                        </div>

                        <!-- Monto -->
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-secondary mb-2">
                                <i class="bi bi-currency-dollar me-1"></i> Monto
                            </label>
                            <div class="input-group">
                                <span class="input-group-text text-success fw-bold">$</span>
                                <input type="number" step="0.01" placeholder="0.00" id="monto_depositado"
                                    name="monto_depositado" class="form-control fw-bold monto-input" required>
                            </div>
                        </div>

                        <!-- Referencia -->
                        <div class="col-12">
                            <label class="form-label small fw-bold text-secondary mb-2">
                                <i class="bi bi-bookmark me-1"></i> Referencia / Concepto
                            </label>
                            <input type="text" placeholder="Ej. Pago compra orden #123..." id="referencia"
                                name="referencia" class="form-control">
                        </div>

                    </div>
                </div>

                <!-- ═══════ FOOTER ═══════ -->
                <div class="modal-footer border-0 p-4 pt-2">
                    <button type="button" class="btn btn-light text-body-secondary fw-bold rounded-pill px-4 me-2"
                        data-bs-dismiss="modal">
                        Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary rounded-pill px-5 py-2 fw-bold shadow-sm">
                        <i class="bi bi-check2-circle me-2"></i> Crear Comprobante
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>

<script>
    const URL_CONTROLADOR = '/cfsistem/app/controllers/comprobantesPagoController.php';

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
                    $('#cliente_id').val(clientesFiltrados[0].id).trigger('change');
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
       INICIALIZACIÓN ÚNICA
       ========================================================= */
    $(function () {

        // 1. Al abrir el modal, inicializar Select2 con templates + dropdownParent
        $('#modalCotizacion').on('shown.bs.modal', function () {

            // Destruir Select2 previo si existe
            ['#cliente_id', '#metodo_pago_m'].forEach(sel => {
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

            // Otros .select2-modal (excluyendo #cliente_id)
            $('.select2-modal').not('#cliente_id').each(function () {
                $(this).select2({
                    theme: 'bootstrap-5',
                    dropdownParent: $('#modalCotizacion')
                });
            });
        });

        // 2. Cambio de almacén → recargar clientes
        $('#almacen_id').on('change', function () {
            const almacenId = this.value;
            if (!almacenId) {
                console.log('Se deseleccionó el almacén');
            } else {
                console.log(`Almacén cambiado a ID: ${almacenId} - ${this.options[this.selectedIndex].text}`);
            }
            cargarClientes();
        });

        // 3. Carga inicial de clientes (sin tocar Select2)
        cargarClientes();

        // 4. Submit
        $('#formSolicitud').on('submit', async function (e) {
            e.preventDefault();

            const payload = {
                almacen_id: $('#almacen_id').val(),
                cliente_id: $('#cliente_id').val(),
                monto_depositado: $('#monto_depositado').val(),
                referencia: $('#referencia').val(),
                fecha: $('#fecha_deposito').val(),
                metodo: $('#metodo_pago_m').val(),
                numeroventa: $('#numero_venta').val()
            };

            console.log('JSON ENVIADO:', payload);

            Swal.fire({
                title: 'Guardando...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            try {
                const resp = await fetch(`${URL_CONTROLADOR}?action=guardar`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                });

                const res = await resp.json();
                console.log('RESPUESTA:', res);

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
                Swal.fire('Error', 'Fallo de conexión o error en el servidor', 'error');
            }
        });

        // 5. Cerrar modal al hacer clic en cancelar (opcional, Bootstrap lo hace solo)
        // 6. Nueva cotización → abrir modal y cargar clientes
        window.nuevaCotizacion = function () {
            $('#formSolicitud')[0].reset();
            $('#modalCotizacion').modal('show');

            // Cargar clientes después de que el modal se abra
            $('#modalCotizacion').one('shown.bs.modal', function () {
                cargarClientes();
            });
        };
    });
</script>