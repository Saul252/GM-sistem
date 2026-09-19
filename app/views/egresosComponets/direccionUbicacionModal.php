<!-- MODAL NUEVA DIRECCIÓN DE PROVEEDOR -->
<div class="modal fade" id="modalNuevaDireccionProveedor" tabindex="-1" aria-hidden="true" style="z-index: 1100;">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 rounded-4 shadow-lg overflow-hidden bg-body text-body">

            <!-- ENCABEZADO SUPERIOR -->
            <div class="modal-header border-0 pb-0 pt-4 px-4 bg-body">
                <h5 class="modal-title fw-semibold fs-5 text-body">
                    <i class="bi bi-geo-alt-fill me-2 text-primary"></i>Nueva Ubicación / Sucursal
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <!-- CUERPO -->
            <div class="modal-body px-4 py-3 bg-body">
                <form id="formDireccionProveedor">

                    <!-- ID Oculto del Proveedor -->
                    <input type="hidden" name="proveedor_id" id="input_proveedor_id">

                    <!-- BANNER INFORMATIVO / NOMBRE DEL PROVEEDOR (SIN atributo "name" para que NO se envíe) -->
                    <div class="header-form-modal mb-4"
                        style="background: linear-gradient(135deg, #4f46e5, #7c3aed); border-radius: 16px; padding: 16px 20px; color: #fff;">
                        <small class="d-block text-uppercase fw-semibold"
                            style="letter-spacing: .5px; opacity: .85; font-size: .72rem;">Proveedor
                            seleccionado</small>
                        <input type="text" id="nombre_proveedor_banner"
                            class="form-control-plaintext text-white fw-bold fs-6 p-0 border-0 bg-transparent shadow-none"
                            readonly value="Cargando proveedor...">
                    </div>

                    <!-- TARJETA DE DATOS -->
                    <div
                        class="card-seccion p-3 p-md-4 bg-body-secondary border border-secondary-subtle rounded-4 shadow-sm">
                        <div class="titulo-seccion text-uppercase text-body-secondary fw-bold mb-3"
                            style="font-size: .78rem; letter-spacing: .6px;">
                            <i class="bi bi-map me-1 text-primary"></i> Datos de la Ubicación
                        </div>

                        <div class="row g-3">
                            <!-- Nombre / Sucursal -->
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-body-secondary mb-1">Nombre / Sucursal
                                    *</label>
                                <div class="input-group">
                                    <span
                                        class="input-group-text bg-body border-secondary-subtle border-end-0 rounded-start-4 ps-3 text-body-secondary">
                                        <i class="bi bi-tag"></i>
                                    </span>
                                    <input type="text" name="nombre"
                                        class="form-control bg-body text-body border-secondary-subtle border-start-0 ps-0 text-uppercase rounded-end-4 shadow-none"
                                        placeholder="Ej. Bodega Principal" required style="min-height: 44px;">
                                </div>
                            </div>

                            <!-- Teléfono -->
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-body-secondary mb-1">Teléfono</label>
                                <input type="text" name="telefono"
                                    class="form-control bg-body text-body border-secondary-subtle text-uppercase rounded-3 shadow-none"
                                    placeholder="Teléfono" style="min-height: 44px;">
                            </div>

                            <!-- Dirección Completa -->
                            <div class="col-12">
                                <label class="form-label small fw-bold text-body-secondary mb-1">Dirección Completa
                                    *</label>
                                <div class="input-group">
                                    <span
                                        class="input-group-text bg-body border-secondary-subtle border-end-0 rounded-start-4 ps-3 text-body-secondary align-items-start pt-2">
                                        <i class="bi bi-signpost-split"></i>
                                    </span>
                                    <textarea
                                        class="form-control bg-body text-body border-secondary-subtle border-start-0 ps-0 text-uppercase rounded-end-4 shadow-none"
                                        name="direccion_completa" rows="2"
                                        placeholder="Calle, número, colonia, ciudad, C.P." required
                                        style="min-height: 70px;"></textarea>
                                </div>
                            </div>

                            <!-- Contacto -->
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-body-secondary mb-1">Contacto /
                                    Responsable</label>
                                <input type="text" name="contacto"
                                    class="form-control bg-body text-body border-secondary-subtle text-uppercase rounded-3 shadow-none"
                                    placeholder="Nombre de contacto" style="min-height: 44px;">
                            </div>

                            <!-- Extensión -->
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-body-secondary mb-1">Extensión</label>
                                <input type="text" name="extencion"
                                    class="form-control bg-body text-body border-secondary-subtle text-uppercase rounded-3 shadow-none"
                                    placeholder="Extensión" style="min-height: 44px;">
                            </div>
                        </div>
                    </div>

                </form>
            </div>

            <!-- PIE DE MODAL -->
            <div class="modal-footer px-4 py-3 border-0 bg-body-tertiary">
                <button type="button" class="btn btn-outline-secondary px-4 rounded-3 fw-semibold"
                    data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-guardar px-4 rounded-3 text-white fw-semibold"
                    style="background: linear-gradient(135deg, #4f46e5, #7c3aed); border: none; box-shadow: 0 4px 12px rgba(79, 70, 229, .25);"
                    onclick="guardarDireccionProveedor(event)">
                    <i class="bi bi-check2-circle me-1"></i>Guardar Ubicación
                </button>
            </div>

        </div>
    </div>
</div>

<script>
    function abrirModalNuevaDireccion(proveedorId, nombre) {
        if (!proveedorId || proveedorId <= 0) {
            Swal.fire('Atención', 'Debe seleccionar un proveedor válido primero', 'warning');
            return;
        }

        const form = document.getElementById('formDireccionProveedor');
        form.reset();

        document.getElementById('input_proveedor_id').value = proveedorId;
        document.getElementById('nombre_proveedor_banner').value = nombre;

        const modal = new bootstrap.Modal(document.getElementById('modalNuevaDireccionProveedor'));
        modal.show();

        setTimeout(() => {
            document.querySelector('#formDireccionProveedor input[name="nombre"]').focus();
        }, 300);
    }

    function guardarDireccionProveedor(e) {
        const form = document.getElementById('formDireccionProveedor');
        const btn = e.target;
        const proveedorId = form.proveedor_id.value;
        const nombre = form.nombre.value.trim();
        const direccion = form.direccion_completa.value.trim();

        if (!proveedorId || proveedorId <= 0) {
            Swal.fire('Error', 'ID de proveedor no válido', 'error');
            return;
        }

        if (!nombre || !direccion) {
            Swal.fire('Atención', 'El nombre de la sucursal y la dirección completa son obligatorios', 'warning');
            return;
        }

        const formData = new FormData(form);

        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Guardando...';

        fetch('/cfsistem/app/controllers/proveedoresController.php?action=guardarDireccionProveedor', {
            method: 'POST',
            body: formData
        })
            .then(res => {
                if (!res.ok) throw new Error("Respuesta inválida del servidor");
                return res.json();
            })
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Ubicación registrada',
                        text: 'Se ha agregado correctamente',
                        timer: 1800,
                        showConfirmButton: false
                    });

                    bootstrap.Modal.getInstance(document.getElementById('modalNuevaDireccionProveedor')).hide();
                    form.reset();

                    if (typeof cargarDireccionesProveedor === 'function') {
                        cargarDireccionesProveedor(proveedorId);
                    }
                } else {
                    Swal.fire('Error', data.message || 'No se pudo guardar la ubicación', 'error');
                }
            })
            .catch(err => {
                console.error(err);
                Swal.fire('Error', err.message || 'Fallo de conexión', 'error');
            })
            .finally(() => {
                btn.disabled = false;
                btn.innerHTML = '<i class="bi bi-check2-circle me-1"></i>Guardar Ubicación';
            });
    }

    document.querySelectorAll('#formDireccionProveedor input[type="text"], #formDireccionProveedor textarea').forEach(elemento => {
        elemento.addEventListener('input', function () {
            this.value = this.value.toUpperCase();
        });
    });
</script>