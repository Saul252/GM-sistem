<div class="modal fade" id="modalCorteCaja" tabindex="-1" aria-labelledby="modalCorteCajaLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 15px; overflow: hidden;">
            <div class="modal-header bg-dark text-white border-0 py-3">
                <div class="d-flex align-items-center">
                    <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                        <i class="fas fa-cash-register text-white"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0" id="modalCorteCajaLabel">Finalizar Corte de Caja</h5>
                        <small class="text-light opacity-75">Confirma los totales antes de guardar</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body p-4 bg-light">
                <form id="formGuardarCorte">
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-bold">FECHA DEL CORTE</label>
                            <input type="date" class="form-control form-control-sm shadow-sm border-0" 
                                   id="fecha_corte_modal" name="fecha_corte" value="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-bold">ALMACÉN</label>
                            <select class="form-select form-select-sm shadow-sm border-0 bg-white" id="almacen_id_modal" name="almacen_id" required>
                                <?php foreach ($listaAlmacenes as $almacen): ?>
                                    <option value="<?= $almacen['id'] ?>" <?= ($target == $almacen['id']) ? 'selected' : '' ?>>
                                        <?= $almacen['nombre'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="glass-card p-3 mb-4 bg-white border-start border-primary border-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted small">Efectivo esperado:</span>
                            <span class="fw-bold text-dark" id="modal-efectivo-txt">$0.00</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted small">Total Ingresos (Real):</span>
                            <span class="fw-bold text-primary h5 mb-0" id="modal-total-txt">$0.00</span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">NOTAS / OBSERVACIONES</label>
                        <textarea class="form-control border-0 shadow-sm" name="observaciones" rows="2" 
                                  placeholder="Ej. Todo cuadrado, faltante de $10, etc..." style="border-radius: 10px;"></textarea>
                    </div>

                    <input type="hidden" name="accion" value="guardarCorte">
                </form>
            </div>

            <div class="modal-footer border-0 bg-light p-4 pt-0">
                <button type="button" class="btn btn-link text-muted text-decoration-none fw-bold" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" id="btnConfirmarCorte" class="btn btn-primary px-4 shadow-sm" style="border-radius: 10px;">
                    <i class="fas fa-check-circle me-2"></i> Confirmar y Cerrar Caja
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const modalElement = document.getElementById('modalCorteCaja');
    const myModal = new bootstrap.Modal(modalElement);
    const btnGuardar = document.getElementById('btnConfirmarCorte');
    const formCorte = document.getElementById('formGuardarCorte');

    // 1. Al abrir el modal, actualizamos los textos con lo que hay en pantalla actualmente
    modalElement.addEventListener('show.bs.modal', function () {
        const efectivoActual = document.getElementById('res-total').innerText; // El ID del HTML anterior
        const totalActual = document.getElementById('res-cobrado-total').innerText;

        document.getElementById('modal-efectivo-txt').innerText = efectivoActual;
        document.getElementById('modal-total-txt').innerText = totalActual;
    });

    // 2. Función para guardar vía AJAX
    btnGuardar.addEventListener('click', function() {
        const formData = new FormData(formCorte);

        // Bloqueamos botón para evitar doble clic
        btnGuardar.disabled = true;
        btnGuardar.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Guardando...';

        fetch('/cfsistem/app/controllers/corteCajaController.php', { // Ajusta a la ruta real de tu controlador
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                Swal.fire({
                    icon: 'success',
                    title: '¡Corte Exitoso!',
                    text: 'El cierre de caja ha sido registrado correctamente.',
                    confirmButtonColor: '#0d6efd'
                }).then(() => {
                    myModal.hide();
                    location.reload(); // Recargamos para ver los cambios o limpiar
                });
            } else {
                throw new Exception(data.message);
            }
        })
        .catch(error => {
            Swal.fire({
                icon: 'error',
                title: 'Error al guardar',
                text: error.message || 'Ocurrió un problema en el servidor.'
            });
        })
        .finally(() => {
            btnGuardar.disabled = false;
            btnGuardar.innerHTML = '<i class="fas fa-check-circle me-2"></i> Confirmar y Cerrar Caja';
        });
    });
});
</script>