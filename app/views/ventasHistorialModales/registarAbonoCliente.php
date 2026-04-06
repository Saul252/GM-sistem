<div class="modal fade" id="modalAbono" tabindex="-1" style="z-index: 1060;">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content shadow-lg border-0">
            <div class="modal-header bg-dark text-white">
                <h6 class="modal-title">Registrar Abono</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input  id="modal_id_venta" name="id_venta" value="">
                <input type="hidden" id="modal_saldo_max">
                 <input type="hidden" id="cliente_id">

                <div class="mb-3">
                    <label class="form-label small fw-bold text-secondary text-uppercase">Monto a Recibir</label>
                    <div class="input-group input-group-lg">
                        <span class="input-group-text bg-light border-end-0">$</span>
                        <input type="number" id="inputMontoAbono" class="form-control border-start-0 ps-0 fw-bold" step="any">
                    </div>
                    <div id="infoSaldo" class="badge bg-light text-dark border w-100 mt-2 py-2"></div>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold text-secondary text-uppercase">Método de Pago</label>
                    <select id="selectMetodoPago" class="form-select">
    <option value="Efectivo">Efectivo</option>
    <option value="Transferencia">Transferencia</option>
    <option value="Tarjeta">Tarjeta</option>
</select>
                </div>

                <hr class="my-3 opacity-10">

                <div class="form-check form-switch mb-2">
                    <input class="form-check-input" type="checkbox" id="checkFechaPersonalizada" onchange="toggleFechaAbono(this.checked)">
                    <label class="form-check-label small fw-bold text-primary" for="checkFechaPersonalizada">Fecha personalizada</label>
                </div>

                <div id="containerFechaAbono" style="display: none;">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary text-uppercase">Fecha y Hora</label>
                        <input type="datetime-local" id="inputFechaAbono" class="form-control form-control-sm">
                    </div>
                </div>
            </div>
            <div class="modal-footer p-2">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary btn-sm" onclick="guardarAbonoModal()">Guardar</button>
            </div>
        </div>
    </div>
</div>
<script>
    let modalAbonoObj;

$(document).ready(function() {
    // Inicializamos el objeto del modal de Bootstrap
    modalAbonoObj = new bootstrap.Modal(document.getElementById('modalAbono'));
});

/**
 * Abre el modal y configura los límites de pago
 */
function abrirFlujoAbono(idVenta,cliente_id, folio, saldoPendiente) {
    console.log(idVenta,cliente_id, folio,saldoPendiente)
    if (saldoPendiente <= 0) {
        Swal.fire('Venta Liquidada', 'Sin saldo pendiente.', 'success');
        return;
    }

    // Seteamos valores internos
    $('#modal_id_venta').val(idVenta);
     $('#modal_cliente_id').val(cliente_id);
    $('#modal_saldo_max').val(saldoPendiente);
    
    // Llenamos la interfaz
    $('.modal-title').text('Abonar a Folio: ' + folio);
    $('#inputMontoAbono').val(saldoPendiente.toFixed(2));
    $('#infoSaldo').text('Saldo máximo: $' + saldoPendiente.toFixed(2)).removeClass('bg-danger text-white').addClass('bg-light text-dark');
    $('#inputMontoAbono').removeClass('is-invalid text-danger');

    modalAbonoObj.show();

    // Autofocus al abrir
    const modalEl = document.getElementById('modalAbono');
    modalEl.addEventListener('shown.bs.modal', () => {
        document.getElementById('inputMontoAbono').focus();
        document.getElementById('inputMontoAbono').select();
    }, { once: true });
}

/**
 * Validación en tiempo real del monto
 */
$(document).on('input', '#inputMontoAbono', function() {
    const saldoMax = parseFloat($('#modal_saldo_max').val()) || 0;
    const montoIngresado = parseFloat($(this).val()) || 0;

    if (montoIngresado > saldoMax || montoIngresado <= 0) {
        $(this).addClass('is-invalid text-danger');
        $('#infoSaldo').removeClass('bg-light text-dark').addClass('bg-danger text-white');
    } else {
        $(this).removeClass('is-invalid text-danger');
        $('#infoSaldo').removeClass('bg-danger text-white').addClass('bg-light text-dark');
    }
});

/**
 * Guarda el abono enviando los datos al controlador
 */async function guardarAbonoModal() {
    const idVenta = $('#modal_id_venta').val();
    const saldoMax = parseFloat($('#modal_saldo_max').val());
    const monto = parseFloat($('#inputMontoAbono').val());
    const metodo = $('#selectMetodoPago').val(); // Verifica que este ID exista en tu HTML
    
    if (!monto || monto <= 0) return Swal.fire('Error', 'Monto inválido', 'warning');
    if (monto > (saldoMax + 0.01)) return Swal.fire('Error', 'Excede el saldo', 'error');

    const checkFechaManual = document.getElementById('checkFechaPersonalizada');
    const inputFechaManual = document.getElementById('inputFechaAbono');
    let fechaFinal = "";

    if (checkFechaManual && checkFechaManual.checked && inputFechaManual.value) {
        fechaFinal = inputFechaManual.value.replace('T', ' ') + ':00';
    }

    const fd = new FormData();
    fd.append('venta_id', idVenta);
    fd.append('monto', monto);
    fd.append('metodo_pago', metodo);
    fd.append('fecha_pago', fechaFinal);

    try {
        // CAMBIO: action=guardarAbono para que coincida con el switch de PHP
        const res = await fetch('/cfsistem/app/controllers/clienteExpedienteController.php?action=guardarAbono', {
            method: 'POST',
            body: fd
        });
        
        const text = await res.text(); // Primero leemos como texto para depurar si hay errores de PHP
        try {
            const data = JSON.parse(text);
            if (data.status === 'success' || data.success) {
                modalAbonoObj.hide();
                Swal.fire('¡Éxito!', data.message || 'Abono registrado', 'success').then(() => location.reload());
            } else {
                Swal.fire('Error', data.message || 'Error al guardar', 'error');
            }
        } catch (jsonErr) {
            console.error("Respuesta no válida del servidor:", text);
            Swal.fire('Error de Respuesta', 'El servidor devolvió un error técnico. Revisa la consola.', 'error');
        }
    } catch (e) {
        Swal.fire('Error Crítico', 'No se pudo conectar con el servidor', 'error');
    }
}
function toggleFechaAbono(show) {
    const container = document.getElementById('containerFechaAbono');
    const inputFecha = document.getElementById('inputFechaAbono');
    if (show) {
        container.style.display = 'block';
        if (!inputFecha.value) {
            const ahora = new Date();
            ahora.setMinutes(ahora.getMinutes() - ahora.getTimezoneOffset());
            inputFecha.value = ahora.toISOString().slice(0, 16);
        }
    } else {
        container.style.display = 'none';
        inputFecha.value = '';
    }
}
</script>