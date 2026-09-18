<style>
    /* Estilo iOS / Apple Look & Feel con soporte Modo Oscuro */
    .modalc .modal-content {
        border-radius: 20px;
        background-color: var(--bs-body-bg, #ffffff);
        color: var(--bs-body-color, #212529);
        border: 1px solid rgba(0, 0, 0, 0.08);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
    }

    /* Adaptación automática para Modo Oscuro en contenedores */
    [data-bs-theme="dark"] .modalc .modal-content,
    body.dark-mode .modalc .modal-content,
    .dark-theme .modalc .modal-content {
        background-color: #1c1c1e;
        /* Gris oscuro clásico de iOS */
        border: 1px solid rgba(255, 255, 255, 0.1);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.6);
    }

    /* Inputs y Selects estilo Cupertino */
    .modalc .form-control,
    .modalc .form-select {
        border-radius: 12px;
        padding: 0.65rem 1rem;
        font-size: 0.95rem;
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

    /* Efecto Focus muy suave tipo iOS (Gris/Azul difuminado) */
    .modalc .form-control:focus,
    .modalc .form-select:focus {
        background-color: transparent;
        border-color: #007aff;
        /* Azul sistema iOS */
        box-shadow: 0 0 0 4px rgba(0, 122, 255, 0.15);
    }

    /* Tipografías y etiquetas delicadas */
    .modalc .form-label {
        font-size: 0.75rem;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        font-weight: 600;
        color: #86868b;
        /* Gris sutil de Apple */
    }

    /* Botones refinados */
    .modalc .btn {
        border-radius: 12px;
        padding: 0.6rem 1.2rem;
        font-weight: 500;
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
</style>
<div class="modal fade modalc" id="modalNuevoCliente" tabindex="-1" aria-labelledby="modalNuevoClienteLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
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

                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label">Nombre Comercial *</label>
                            <input type="text" name="nombre_comercial" id="nombre_comercial"
                                class="form-control text-uppercase" placeholder="EJ. MATERIALES EL CENTRO" required>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Razón Social</label>
                            <input type="text" name="razon_social" class="form-control text-uppercase"
                                placeholder="NOMBRE LEGAL COMPLETO">
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Contacto *</label>
                            <input type="text" name="contacto" class="form-control text-uppercase"
                                placeholder="NOMBRE DEL CONTACTO">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">RFC</label>
                            <input type="text" name="rfc" id="rfc" class="form-control text-uppercase" maxlength="13"
                                placeholder="ABCD000000XXX">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Código Postal *</label>
                            <input type="text" name="codigo_postal" class="form-control" maxlength="5"
                                placeholder="00000" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Régimen Fiscal</label>
                            <input type="text" name="regimen_fiscal" class="form-control text-uppercase" maxlength="3"
                                placeholder="601">
                            <small class="text-muted d-block mt-1" style="font-size: 0.7rem;">Clave del catálogo del
                                SAT</small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Uso de CFDI</label>
                            <select name="uso_cfdi" class="form-select">
                                <option value="G03" selected>G03 - Gastos en general</option>
                                <option value="S01">S01 - Sin efectos fiscales</option>
                                <option value="G01">G01 - Adquisición de mercancías</option>
                                <option value="P01">P01 - Por definir</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Correo Electrónico</label>
                            <input type="email" name="correo" class="form-control" placeholder="CLIENTE@CORREO.COM">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Teléfono</label>
                            <input type="tel" name="telefono" class="form-control" placeholder="55 0000 0000">
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Calle</label>
                            <textarea name="calle" class="form-control text-uppercase" rows="2"
                                placeholder="CALLE Y NÚMERO"></textarea>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Colonia</label>
                            <textarea name="colonia" class="form-control text-uppercase" rows="2"
                                placeholder="COLONIA..."></textarea>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Pueblo</label>
                            <textarea name="pueblo" class="form-control text-uppercase" rows="2"
                                placeholder="PUEBLO"></textarea>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Ciudad</label>
                            <textarea name="ciudad" class="form-control text-uppercase" rows="2"
                                placeholder="CIUDAD"></textarea>
                        </div>

                        <?php if ($almacen_usuario == 0): ?>
                            <div class="col-md-12 mb-2" style="display: none;">
                                <label class="form-label text-primary">Asignar a Almacén *</label>
                                <select name="almacen_id" class="form-select">
                                    <option value="1">-- Selecciona un almacén --</option>
                                    <?php foreach ($almacenes as $alm): ?>
                                        <option value="<?= $alm['id'] ?>">
                                            <?= htmlspecialchars($alm['nombre']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Footer delicado -->
                <div class="modal-footer px-4 py-3 border-0 bg-transparent">
                    <button type="button" class="btn btn-light border px-4" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary px-4 shadow-sm" id="btnGuardarCliente">
                        Guardar Cliente
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function abrirModalNuevoCliente() {
        new bootstrap.Modal(document.getElementById('modalNuevoCliente')).show();
    }

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

    document.getElementById('formNuevoCliente').addEventListener('submit', function (e) {
        e.preventDefault();

        obtenerOGenerarRFC();

        // Forzar conversión a mayúsculas real en inputs de texto y textareas antes de enviar
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
                        const idAlmacenActualVentas = document.getElementById('almacen_id_actual')?.value || idAlmacenDestino;

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
</script>