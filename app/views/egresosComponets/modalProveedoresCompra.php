<div class="modal fade" id="modalNuevoProveedorRapido" tabindex="-1" aria-hidden="true" style="z-index: 1070;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-dark text-white">
                <h6 class="modal-title"><i class="bi bi-person-plus-fill me-2"></i>Nuevo Proveedor Directo</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body bg-light">
                <form id="formProvRapido">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label small fw-bold">Nombre Comercial / Empresa</label>
                            <input type="text" name="nombre_comercial" class="form-control" placeholder="Ej: Distribuidora Central" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold">Razón Social (Opcional)</label>
                            <input type="text" name="razon_social" class="form-control" placeholder="Nombre legal">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">RFC</label>
                            <input type="text" name="rfc" class="form-control" placeholder="XAXX010101000" maxlength="13">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Teléfono</label>
                            <input type="tel" name="telefono" class="form-control" placeholder="10 dígitos">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer bg-white">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary px-4" onclick="guardarProvRapido()">
                    <i class="bi bi-save me-2"></i>Registrar y Seleccionar
                </button>
            </div>
        </div>
    </div>
</div>

<script>


/**
 * Abre el modal de creación rápida asegurando el orden de capas (z-index)
 */
function abrirModalNuevoProveedor() {
    const modalProv = new bootstrap.Modal(document.getElementById('modalNuevoProveedorRapido'));
    modalProv.show();
}

/**
 * Envía el formulario al controlador y refresca el select de la compra
 */
function guardarProvRapido() {
    const form = document.getElementById('formProvRapido');
    
    // Validación básica
    if (!form.nombre_comercial.value.trim()) {
        Swal.fire('Atención', 'El nombre comercial es obligatorio', 'warning');
        return;
    }

    const formData = new FormData(form);

    // Bloquear botón para evitar doble clic
    const btn = event.target;
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Guardando...';

    fetch('egresosController.php?action=guardarProveedor', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Proveedor Registrado',
                text: 'Se ha añadido a la lista y seleccionado automáticamente',
                timer: 2000,
                showConfirmButton: false
            });

            // Cerrar modal y resetear form
            bootstrap.Modal.getInstance(document.getElementById('modalNuevoProveedorRapido')).hide();
            form.reset();

            // Refrescar la lista de proveedores en el select principal
            actualizarListaProveedores(data.nuevo_nombre || formData.get('nombre_comercial'));
        } else {
            Swal.fire('Error', data.message || 'No se pudo guardar', 'error');
        }
    })
    .catch(err => {
        console.error(err);
        Swal.fire('Error', 'Fallo de conexión con el servidor', 'error');
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-save me-2"></i>Registrar y Seleccionar';
    });
}

/**
 * Obtiene la lista actualizada de proveedores y selecciona el recién creado
 */
function actualizarListaProveedores(seleccionarNombre = null) {
    fetch('egresosController.php?action=getProveedoresJSON')
        .then(res => res.json())
        .then(data => {
            const $select = $('#select_proveedor');
            $select.empty().append('<option value="">Seleccione o busque un proveedor...</option>');
            
            data.forEach(p => {
                // Usamos trim() para evitar espacios fantasma
                const option = new Option(p.nombre_comercial.trim(), p.nombre_comercial.trim(), false, false);
                $select.append(option);
            });

            if (seleccionarNombre) {
                console.log("Intentando seleccionar:", seleccionarNombre.trim());
                $select.val(seleccionarNombre.trim()).trigger('change');
            }
        });
}
window.addEventListener('load', function() {
    // Verificamos que tanto jQuery como el plugin Select2 estén disponibles
    if (window.jQuery && $.fn.select2) {
        
        $('#select_proveedor').select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: 'Escribe para buscar...',
            dropdownParent: $('#modalNuevaCompra') // Mantiene el buscador funcional dentro del modal
        });

        console.log("Select2 inicializado correctamente.");
    } else {
        console.warn("Select2 no se pudo inicializar: jQuery o el plugin no están cargados.");
    }
});
</script>

