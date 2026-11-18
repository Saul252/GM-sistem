<style>
    /* ============================================================
       ESTILO BASE .modalc (iOS look & feel + modo oscuro)
       ============================================================ */
    .modalc .modal-content {
        border-radius: 20px;
        background-color: var(--bs-body-bg, #ffffff);
        color: var(--bs-body-color, #212529);
        border: 1px solid rgba(0, 0, 0, 0.08);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
    }

    [data-bs-theme="dark"] .modalc .modal-content,
    body.dark-mode .modalc .modal-content,
    .dark-theme .modalc .modal-content {
        background-color: #1c1c1e;
        border: 1px solid rgba(255, 255, 255, 0.1);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.6);
    }

    .modalc .form-control,
    .modalc .form-select {
        border-radius: 8px;
        padding: 0.35rem 0.65rem;
        font-size: 0.85rem;
        background-color: rgba(0, 0, 0, 0.02);
        border: 1px solid rgba(0, 0, 0, 0.12);
        transition: all 0.2s ease-in-out;
    }

    [data-bs-theme="dark"] .modalc .form-control,
    [data-bs-theme="dark"] .modalc .form-select,
    body.dark-mode .modalc .form-control,
    body.dark-mode .modalc .form-select {
        background-color: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.15);
        color: #f5f5f7;
    }

    .modalc .form-control:focus,
    .modalc .form-select:focus {
        background-color: transparent;
        border-color: #007aff;
        box-shadow: 0 0 0 3px rgba(0, 122, 255, 0.15);
    }

    .modalc .form-label {
        font-size: 0.8rem;
        font-weight: 500;
        color: #6c757d;
        margin-bottom: 0;
    }

    [data-bs-theme="dark"] .modalc .form-label,
    body.dark-mode .modalc .form-label {
        color: #98989f;
    }

    .modalc .btn {
        border-radius: 10px;
        padding: 0.5rem 1rem;
        font-weight: 500;
        font-size: 0.85rem;
        transition: transform 0.1s ease, background-color 0.2s;
    }

    .modalc .btn:active {
        transform: scale(0.97);
    }

    .modalc .modal-header {
        border-top-left-radius: 20px;
        border-top-right-radius: 20px;
        background: transparent !important;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    }

    [data-bs-theme="dark"] .modalc .modal-header,
    body.dark-mode .modalc .modal-header {
        border-bottom: 1px solid rgba(255, 255, 255, 0.08);
    }

    /* ============================================================
       🔹 MODAL ANCHO + SCROLL INTERNO + FOOTER FIJO
       ============================================================ */
    #modalNuevoCliente .modal-dialog {
        max-width: 90vw;
        width: 90vw;
        max-height: 92vh;
        margin: 4vh auto;
    }

    #modalNuevoCliente .modal-content {
        max-height: 92vh;
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    #modalNuevoCliente .modal-header {
        flex-shrink: 0;
    }

    #modalNuevoCliente form {
        flex: 1 1 auto;
        display: flex;
        flex-direction: column;
        min-height: 0;
        overflow: hidden;
    }

    #modalNuevoCliente .modal-body {
        flex: 1 1 auto;
        overflow-y: auto;
        min-height: 0;
    }

    #modalNuevoCliente .modal-footer {
        flex-shrink: 0;
        border-top: 1px solid rgba(0, 0, 0, 0.06);
    }

    @media (max-width: 991px) {
        #modalNuevoCliente .modal-dialog {
            max-width: 95vw;
            width: 95vw;
        }
    }

    /* Scrollbar fina */
    #modalNuevoCliente .modal-body::-webkit-scrollbar {
        width: 8px;
    }

    #modalNuevoCliente .modal-body::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 4px;
    }

    #modalNuevoCliente .modal-body::-webkit-scrollbar-track {
        background: transparent;
    }

    /* Títulos de sección */
    #modalNuevoCliente .form-section-title {
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: #007aff;
        margin: 1rem 0 0.75rem;
        padding-bottom: 0.35rem;
        border-bottom: 2px solid rgba(0, 122, 255, 0.15);
    }

    #modalNuevoCliente .form-section-title:first-child {
        margin-top: 0;
    }
</style>

<!-- ============================================================
     MODAL NUEVO CLIENTE
     ============================================================ -->
<div class="modal fade modalc" id="modalNuevoCliente" tabindex="-1" aria-labelledby="modalNuevoClienteLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <!-- Cabecera minimalista tipo iOS -->
            <div class="modal-header px-4 pt-4 pb-2">
                <h5 class="modal-title fw-bold fs-5" id="modalNuevoClienteLabel">
                    Registrar Nuevo Cliente
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="formNuevoCliente">
                <div class="modal-body px-4 py-3">
                    <input type="hidden" name="almacen_id" value="<?= $almacen_usuario ?>">

                    <!-- ============================================ -->
                    <!-- SECCIÓN 1: INFORMACIÓN GENERAL                -->
                    <!-- ============================================ -->


                    <div class="d-flex flex-column gap-2 mb-2">

                        <div class="row align-items-center g-0">
                            <div class="col-md-4 text-md-end pe-md-3 mb-1 mb-md-0">
                                <label class="form-label">Nombre Comercial *</label>
                            </div>
                            <div class="col-md-8">
                                <input type="text" name="nombre_comercial" id="nombre_comercial"
                                    class="form-control text-uppercase" placeholder="EJ. MATERIALES EL CENTRO" required>
                            </div>
                        </div>

                        <div class="row align-items-center g-0">
                            <div class="col-md-4 text-md-end pe-md-3 mb-1 mb-md-0">
                                <label class="form-label">Razón Social</label>
                            </div>
                            <div class="col-md-8">
                                <input type="text" name="razon_social" class="form-control text-uppercase"
                                    placeholder="NOMBRE LEGAL COMPLETO">
                            </div>
                        </div>

                        <div class="row align-items-center g-0">
                            <div class="col-md-4 text-md-end pe-md-3 mb-1 mb-md-0">
                                <label class="form-label">Contacto *</label>
                            </div>
                            <div class="col-md-8">
                                <input type="text" name="contacto" class="form-control text-uppercase"
                                    placeholder="NOMBRE DEL CONTACTO">
                            </div>
                        </div>

                        <div class="row align-items-center g-0">
                            <div class="col-md-4 text-md-end pe-md-3 mb-1 mb-md-0">
                                <label class="form-label">Correo Electrónico</label>
                            </div>
                            <div class="col-md-8">
                                <input type="email" name="correo" class="form-control" placeholder="CLIENTE@CORREO.COM">
                            </div>
                        </div>

                        <div class="row align-items-center g-0">
                            <div class="col-md-4 text-md-end pe-md-3 mb-1 mb-md-0">
                                <label class="form-label">Teléfono</label>
                            </div>
                            <div class="col-md-8">
                                <input type="tel" name="telefono" class="form-control" placeholder="55 0000 0000">
                            </div>
                        </div>


                    </div>

                    <!-- ============================================ -->
                    <!-- SECCIÓN 2: DATOS FISCALES                     -->
                    <!-- ============================================ -->


                    <div class="d-flex flex-column gap-2 ">

                        <div class="row align-items-center g-0">
                            <div class="col-md-4 text-md-end pe-md-3 mb-1 mb-md-0">
                                <label class="form-label">RFC</label>
                            </div>
                            <div class="col-md-8">
                                <input type="text" name="rfc" id="rfc" class="form-control text-uppercase"
                                    maxlength="13" placeholder="ABCD000000XXX">
                                <small class="text-muted d-block mt-1" style="font-size: 0.7rem;">
                                    Si se deja vacío se generará automáticamente a partir del nombre
                                </small>
                            </div>
                        </div>

                        <div class="row align-items-start g-0 mb-2">

                            <div class="col-md-8">
                                <input type="hidden" name="regimen_fiscal" class="form-control text-uppercase"
                                    maxlength="3" placeholder="601">

                            </div>
                        </div>

                        <div class="row align-items-center g-0 mb-2">
                            <div class="row align-items-center g-0 mb-2 d-none">
                                <div class="col-md-4 text-md-end pe-md-3 mb-1 mb-md-0">
                                    <label class="form-label">Uso de CFDI</label>
                                </div>
                                <div class="col-md-8">
                                    <select name="uso_cfdi" class="form-select">
                                        <option value="G03" selected>G03 - Gastos en general</option>
                                        <option value="S01">S01 - Sin efectos fiscales</option>
                                        <option value="G01">G01 - Adquisición de mercancías</option>
                                        <option value="P01">P01 - Por definir</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- ============================================ -->
                        <!-- SECCIÓN 3: DIRECCIÓN                          -->
                        <!-- ============================================ -->


                        <div class="d-flex flex-column gap-2 ">

                            <div class="row align-items-start g-0">
                                <div class="col-md-4 text-md-end pe-md-3 pt-1 mb-1 mb-md-0">
                                    <label class="form-label">Calle</label>
                                </div>
                                <div class="col-md-8">
                                    <textarea name="calle" class="form-control text-uppercase" rows="2"
                                        placeholder="CALLE Y NÚMERO"></textarea>
                                </div>
                            </div>

                            <div class="row align-items-start g-0">
                                <div class="col-md-4 text-md-end pe-md-3 pt-1 mb-1 mb-md-0">
                                    <label class="form-label">Colonia</label>
                                </div>
                                <div class="col-md-8">
                                    <textarea name="colonia" class="form-control text-uppercase" rows="2"
                                        placeholder="COLONIA..."></textarea>
                                </div>
                            </div>

                            <div class="row align-items-start g-0">
                                <div class="col-md-4 text-md-end pe-md-3 pt-1 mb-1 mb-md-0">
                                    <label class="form-label">Población</label>
                                </div>
                                <div class="col-md-8">
                                    <textarea name="pueblo" class="form-control text-uppercase" rows="2"
                                        placeholder="PUEBLO"></textarea>
                                </div>
                            </div>

                            <div class="row align-items-start g-0">
                                <div class="col-md-4 text-md-end pe-md-3 pt-1 mb-1 mb-md-0">
                                    <label class="form-label">Ciudad</label>
                                </div>
                                <div class="col-md-8">
                                    <textarea name="ciudad" class="form-control text-uppercase" rows="2"
                                        placeholder="CIUDAD"></textarea>
                                </div>
                            </div>
                            <div class="row align-items-center g-0">
                                <div class="col-md-4 text-md-end pe-md-3 mb-1 mb-md-0">
                                    <label class="form-label">Código Postal *</label>
                                </div>
                                <div class="col-md-8">
                                    <input type="text" name="codigo_postal" class="form-control" maxlength="5"
                                        placeholder="00000" required>
                                </div>
                            </div>
                        </div>

                        <!-- ============================================ -->
                        <!-- SECCIÓN 4: ASIGNACIÓN (solo admin)            -->
                        <!-- ============================================ -->
                        <?php if ($almacen_usuario == 0): ?>
                            <!-- Si es admin y quieres ocultarlo visualmente pero que siga enviándose, usamos d-none -->
                            <div class="row align-items-center g-0 mb-2 d-none">
                                <div class="col-md-4 text-md-end pe-md-3 mb-1 mb-md-0">
                                    <label class="form-label text-primary">Asignar a Almacén *</label>
                                </div>
                                <div class="col-md-8">
                                    <select name="almacen_id" class="form-select border-primary">
                                        <option value="">-- Selecciona un almacén --</option>
                                        <?php foreach ($almacenes as $alm): ?>
                                            <option value="<?= $alm['id'] ?>">
                                                <?= htmlspecialchars($alm['nombre']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <small class="text-muted d-block mt-1" style="font-size: 0.7rem;">
                                        Como administrador, debes elegir a qué sucursal pertenece este cliente.
                                    </small>
                                </div>
                            </div>
                            <!-- Input oculto de respaldo por si necesitas forzar un valor predeterminado al estar oculto -->
                            <input type="hidden" name="almacen_id" value="1">
                        <?php else: ?>
                            <input type="hidden" name="almacen_id" value="<?= $almacen_usuario ?>">
                        <?php endif; ?>
                    </div>

                    <!-- Footer fijo -->
                    <div class="modal-footer px-4 py-3">
                        <button type="button" class="btn btn-light border px-4" data-bs-dismiss="modal">
                            Cancelar
                        </button>
                        <button type="submit" class="btn btn-primary px-4 shadow-sm" id="btnGuardarCliente">
                            Guardar Cliente
                        </button>
                    </div>
            </form>
        </div>
    </div>
</div>

<script>
    // ============================================================
    // FUNCIONES
    // ============================================================
    function abrirModalNuevoCliente() {
        new bootstrap.Modal(document.getElementById('modalNuevoCliente')).show();
    }

    // RFC automático a partir del nombre (solo si el campo está vacío)
    function obtenerOGenerarRFC() {
        let rfc = $('#rfc').val() ? $('#rfc').val().trim() : '';
        let nombre = $('#nombre_comercial').val() ? $('#nombre_comercial').val().trim() : '';

        if (rfc !== '') {
            return rfc.toUpperCase();
        }

        let limpio = nombre
            .normalize("NFD")
            .replace(/[\u0300-\u036f]/g, "")
            .toUpperCase()
            .replace(/[^A-Z\s]/g, "");

        let letras = limpio.replace(/\s+/g, '');

        let rfcGenerado = '';
        if (letras.length >= 4) {
            rfcGenerado = letras.substring(0, 4) + '010101XXX';
        } else if (letras.length > 0) {
            rfcGenerado = letras.padEnd(4, 'X') + '010101XXX';
        } else {
            rfcGenerado = 'XAXX010101000';
        }

        $('#rfc').val(rfcGenerado);
        return rfcGenerado;
    }

    // ============================================================
    // SUBMIT — Guardar cliente
    // ============================================================
    document.getElementById('formNuevoCliente').addEventListener('submit', function (e) {
        e.preventDefault();

        obtenerOGenerarRFC();

        // Forzar mayúsculas en inputs/textareas antes de enviar
        this.querySelectorAll('input[type="text"], textarea').forEach(input => {
            if (input.value) {
                input.value = input.value.toUpperCase();
            }
        });

        const formData = new FormData(this);

        Swal.fire({
            title: 'Guardando cliente...',
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); },
            customClass: { popup: 'swal-zindex' }
        });

        fetch('/cfsistem/app/controllers/clientesController.php?action=guardar', {
            method: 'POST',
            body: formData
        })
            .then(res => res.json())
            .then(res => {
                if (res.success === true) {
                    Swal.fire({
                        title: '¡Éxito!',
                        text: res.message,
                        icon: 'success',
                        customClass: { popup: 'swal-zindex' }
                    });

                    if (typeof cargarClientes === 'function') cargarClientes();

                    const selectCliente = document.getElementById('selectCliente');
                    if (selectCliente) {
                        const idAlmacenDestino = formData.get('almacen_id');
                        const idAlmacenActualVentas =
                            document.getElementById('almacen_id_actual')?.value || idAlmacenDestino;

                        if (idAlmacenDestino == idAlmacenActualVentas) {
                            const nombre = formData.get('nombre_comercial');
                            const option = new Option(nombre, res.id, true, true);

                            option.setAttribute('data-rfc', formData.get('rfc'));
                            option.setAttribute('data-rs', formData.get('razon_social'));
                            option.setAttribute('data-cp', formData.get('codigo_postal'));
                            option.setAttribute('data-regimen', formData.get('regimen_fiscal'));

                            selectCliente.add(option);
                            selectCliente.dispatchEvent(new Event('change'));
                        }
                    }

                    const modalElement = document.getElementById('modalNuevoCliente');
                    const modal = bootstrap.Modal.getInstance(modalElement);
                    if (modal) modal.hide();

                    this.reset();

                    if (typeof fetchData === 'function') {
                        const filtro = document.getElementById('filtroAlmacen')?.value || 0;
                        fetchData(filtro);
                    }
                } else {
                    Swal.fire({
                        title: 'Error',
                        text: res.message || 'Error desconocido',
                        icon: 'error',
                        customClass: { popup: 'swal-zindex' }
                    });
                }
            })
            .catch(err => {
                console.error(err);
                Swal.fire('Error', 'No se pudo conectar con el servidor', 'error');
            });
    });

    // ============================================================
    // Mayúsculas en tiempo real
    // ============================================================
    document.querySelectorAll('input[type="text"], textarea').forEach(elemento => {
        elemento.addEventListener('input', function () {
            this.value = this.value.toUpperCase();
        });
    });
</script>