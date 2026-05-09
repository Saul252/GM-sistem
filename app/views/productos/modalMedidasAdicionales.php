<!-- MODAL CREAR MEDIDA ADICIONAL -->
<style>
    /* =========================================================
   MODAL MEDIDA ADICIONAL
========================================================= */

/* Modal arriba de TODOS los demás */
#modalMedidaAdicional {
    z-index: 99999 !important;
}
.miSwalZ{
    z-index: 999999 !important;
}

.swal2-container{
    z-index: 999999 !important;
}



/* Animación modal */
@keyframes modalPop {

    from {
        opacity: 0;
        transform: scale(.94) translateY(10px);
    }

    to {
        opacity: 1;
        transform: scale(1) translateY(0);
    }
}

/* Gradiente animado */
@keyframes moverGradiente {

    0% {
        background-position: 0% 50%;
    }

    50% {
        background-position: 100% 50%;
    }

    100% {
        background-position: 0% 50%;
    }
}
</style>
<div class="modal fade" id="modalMedidaAdicional"  tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">

            <!-- HEADER -->
            <div class="modal-header bg-dark text-white border-0">
                <div>
                    <h5 class="modal-title fw-bold mb-0">
                        <i class="bi bi-rulers me-2"></i>
                        Nueva Medida
                    </h5>

                    <small id="infoProductoModal" class="text-white-50">
                        Configura equivalencia
                    </small>
                </div>

                <button type="button"
                        class="btn-close btn-close-white"
                        data-bs-dismiss="modal">
                </button>
            </div>

            <!-- FORM -->
            <form id="formMedidaAdicional">

                <input type="hidden" name="producto_id" id="id_producto_crear">
                <input type="hidden" name="almacen_id" id="id_almacen_crear">

                <div class="modal-body bg-light p-4">

                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-uppercase text-muted">
                            Nombre de la medida
                        </label>

                        <input type="text"
                               name="nombre"
                               class="form-control rounded-3"
                               placeholder="Ej: Carretilla"
                               required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-uppercase text-muted">
                            Equivalencia
                        </label>

                        <div class="input-group">

                            <input type="number"
                                   name="equivalencia"
                                   class="form-control"
                                   step="0.0001"
                                   placeholder="0.0000"
                                   required>

                            <span id="medida" class="input-group-text"></span>
                        </div>

                        <small class="text-muted">
                            Cantidad equivalente en la unidad principal.
                        </small>
                    </div>

                </div>

                <div class="modal-footer border-0 bg-white px-4 pb-4">

                    <button type="button"
                            class="btn btn-light rounded-pill px-4"
                            data-bs-dismiss="modal">
                        Cancelar
                    </button>

                    <button type="submit"
                            class="btn btn-dark rounded-pill px-5 shadow-sm">
                        Guardar
                    </button>

                </div>

            </form>

        </div>
    </div>
</div>

<script>

if (typeof URL_MEDIDAS === 'undefined') {
    window.URL_MEDIDAS = '/cfsistem/app/controllers/productosController.php';
}

/**
 * ABRIR MODAL
 */
window.prepararNuevaMedida = function (
    idProducto,
    idAlmacen,
    nombreProducto,
    unidad_medida
) {

    document.getElementById('id_producto_crear').value = idProducto;

    document.getElementById('id_almacen_crear').value = idAlmacen;

    document.getElementById('infoProductoModal').innerText =
        `Producto: ${nombreProducto}`;

    document.getElementById('medida').innerText =
        `en cada ${unidad_medida}`;

    const modalEl = document.getElementById('modalMedidaAdicional');

    const modal =
        bootstrap.Modal.getOrCreateInstance(modalEl);

    modal.show();
};


/**
 * GUARDAR
 */
document
    .getElementById('formMedidaAdicional')
    .addEventListener('submit', async function (e) {

        e.preventDefault();

        try {

            Swal.fire({
                title: 'Guardando...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            const formData = new FormData(this);

            const resp = await fetch(
                `${URL_MEDIDAS}?action=guardarOpcionMedida`,
                {
                    method: 'POST',
                    body: formData
                }
            );

            const data = await resp.json();

            Swal.close();

            if (data.success || data.status === 'success') {

                await Swal.fire({
                    icon: 'success',
                    title: 'Guardado',
                    text: 'Medida agregada correctamente',
                    timer: 1500,
                    showConfirmButton: false,
                    customClass: {
                         popup: 'miSwalZ'
                    }
                });

                const modalEl =
                    document.getElementById('modalMedidaAdicional');

                const modal =
                    bootstrap.Modal.getInstance(modalEl);

                if (modal) {
                    modal.hide();
                }

                document
                    .getElementById('formMedidaAdicional')
                    .reset();
                    recargarModalMedidas();

            } else {

             Swal.fire({
    icon: 'error',
    title: 'Error',
    text: data.message || 'No se pudo guardar',

    customClass: {
        popup: 'miSwalZ'
    }
});
            }

        } catch (error) {

            console.error(error);

            Swal.fire(
                'Error',
                'Falló la comunicación con el servidor',
                'error'
            );
        }

    });

</script>