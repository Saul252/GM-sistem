<div class="modal fade" id="modalCorteCaja" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 15px; overflow: hidden;">

            <!-- HEADER -->
            <div class="modal-header bg-dark text-white border-0 py-3">
                <div class="d-flex align-items-center">
                    <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-3"
                         style="width: 40px; height: 40px;">
                        <i class="fas fa-cash-register text-white"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0">Finalizar Corte de Caja</h5>
                        <small class="text-light opacity-75">Confirma los totales antes de guardar</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <!-- BODY -->
            <div class="modal-body p-4 bg-light">

                <form id="formGuardarCorte">

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">FECHA</label>
                            <input type="date" class="form-control form-control-sm"
                                   name="fecha_corte"
                                   value="<?php echo date('Y-m-d'); ?>">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold">ALMACÉN</label>
                            <select id="almacen_id_modal" class="form-select form-select-sm">
                                <?php foreach ($listaAlmacenes as $almacen): ?>
                                    <option value="<?= $almacen['id'] ?>">
                                        <?= $almacen['nombre'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <!-- RESUMEN -->
                    <div class="bg-white p-3 rounded shadow-sm mb-3">

                        <div class="d-flex justify-content-between mb-2">
                            <span>Efectivo</span>
                            <strong id="modal-efectivo-txt">$0.00</strong>
                        </div>

                        <div class="d-flex justify-content-between mb-2">
                            <span>Tarjeta</span>
                            <strong id="modal-tarjeta-txt">$0.00</strong>
                        </div>

                        <div class="d-flex justify-content-between mb-2">
                            <span>Transferencia</span>
                            <strong id="modal-transferencia-txt">$0.00</strong>
                        </div>

                        <hr>

                        <div class="d-flex justify-content-between">
                            <span class="fw-bold">TOTAL</span>
                            <strong class="text-primary" id="modal-total-txt">$0.00</strong>
                        </div>

                    </div>

                    <textarea name="observaciones" class="form-control" rows="2"
                              placeholder="Observaciones..."></textarea>

                    <input type="hidden" name="accion" value="guardarCorte">

                </form>

            </div>

            <!-- FOOTER -->
            <div class="modal-footer border-0">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button id="btnConfirmarCorte" class="btn btn-primary">
                    Guardar Corte
                </button>
            </div>

        </div>
    </div>
</div>
<script>
const url = "/cfsistem/app/controllers/corteCajaController.php";

document.addEventListener('DOMContentLoaded', function() {

    const modalElement = document.getElementById('modalCorteCaja');
    const myModal      = new bootstrap.Modal(modalElement);
    const btnGuardar   = document.getElementById('btnConfirmarCorte');

    const selectPrincipal = document.getElementById('almacen_id');
    const selectModal     = document.getElementById('almacen_id_modal');

    // ===============================
    // 🔥 LIMPIAR NUMEROS
    // ===============================
    const limpiarNumero = (id) => {
        const el = document.getElementById(id);
        if (!el) return 0;
        return parseFloat((el.innerText || '0').replace(/[^0-9.-]+/g, "")) || 0;
    };

    // ===============================
    // 🔥 ACTUALIZAR MODAL
    // ===============================
    function actualizarModal() {

        const efectivo      = limpiarNumero('res-total-efectivoMasSaldo');
        const tarjeta       = limpiarNumero('res-total-tarjetaMasSaldo');
        const transferencia = limpiarNumero('res-total-transMasSaldo');

        const total = efectivo + tarjeta + transferencia;

        document.getElementById('modal-efectivo-txt').innerText =
            '$' + efectivo.toLocaleString('es-MX');

        document.getElementById('modal-tarjeta-txt').innerText =
            '$' + tarjeta.toLocaleString('es-MX');

        document.getElementById('modal-transferencia-txt').innerText =
            '$' + transferencia.toLocaleString('es-MX');

        document.getElementById('modal-total-txt').innerText =
            '$' + total.toLocaleString('es-MX');

        console.log("📊 MODAL:", {efectivo, tarjeta, transferencia, total});
    }

    // ===============================
    // 🔥 AL ABRIR MODAL
    // ===============================
    modalElement.addEventListener('show.bs.modal', function () {

        selectModal.value = selectPrincipal.value;

        actualizarModal();
    });

    // ===============================
    // 🔥 CAMBIO DE ALMACÉN EN MODAL
    // ===============================
    selectModal.addEventListener('change', function() {

        selectPrincipal.value = this.value;

        // 🔥 dispara recarga del sistema
        $('#almacen_id').trigger('change');

        setTimeout(() => {
            actualizarModal();
        }, 400);
    });

    // ===============================
    // 🔥 GUARDAR
    // ===============================
    btnGuardar.addEventListener('click', function() {

        const formData = new FormData(document.getElementById('formGuardarCorte'));

        const almacenId = selectModal.value;

        formData.append('accion', 'guardarCorte');
        formData.append('almacen_id', almacenId);

        // 🔥 DATOS
        formData.append('total_efectivo', limpiarNumero('res-total-efectivoMasSaldo'));
        formData.append('total_tarjeta', limpiarNumero('es-total-tarjetaMasSaldo'));
        formData.append('total_transferencia', limpiarNumero('res-total-transMasSaldo'));

        formData.append('gran_total_ingresos',
            limpiarNumero('res-total-efectivoMasSaldo') +
            limpiarNumero('res-total-tarjetaMasSaldo') +
            limpiarNumero('res-total-transMasSaldo')
        );

        // 🔥 DEBUG
        console.log("🧾 ENVIANDO:");
        for (let [k,v] of formData.entries()) {
            console.log(k, v);
        }

        btnGuardar.disabled = true;
        btnGuardar.innerHTML = "Guardando...";

        fetch(url, {
            method: 'POST',
            body: formData
        })
        .then(r => r.json())
        .then(data => {

            console.log("📦 RESPUESTA:", data);

            if (data.status === 'success') {
                Swal.fire("OK", "Corte guardado", "success")
                .then(() => location.reload());
            } else {
                throw new Error(data.message);
            }

        })
        .catch(err => {
            Swal.fire("Error", err.message, "error");
        })
        .finally(() => {
            btnGuardar.disabled = false;
            btnGuardar.innerHTML = "Guardar Corte";
        });

    });

});
</script>