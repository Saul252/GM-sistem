<div class="modal fade" id="modalAbono" tabindex="-1" style="z-index: 1060;">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content shadow-lg border-0">
            <div class="modal-header bg-dark text-white">
                <h6 class="modal-title">Registrar Abono</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
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

                <div id="containerFechaAbono" style="display: none;" class="animate__animated animate__fadeIn">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary text-uppercase">Fecha y Hora del Pago</label>
                        <input type="datetime-local" id="inputFechaAbono" class="form-control form-control-sm">
                        <small class="text-muted" style="font-size: 0.65rem;">Útil para registrar pagos de días anteriores.</small>
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
    /**
 * Controla la visibilidad del selector de fecha en el abono
 */
function toggleFechaAbono(show) {
    const container = document.getElementById('containerFechaAbono');
    const inputFecha = document.getElementById('inputFechaAbono');

    if (show) {
        container.style.display = 'block';
        // Si el campo está vacío, le ponemos la fecha y hora actual por defecto
        if (!inputFecha.value) {
            const ahora = new Date();
            ahora.setMinutes(ahora.getMinutes() - ahora.getTimezoneOffset());
            inputFecha.value = ahora.toISOString().slice(0, 16);
        }
    } else {
        container.style.display = 'none';
        inputFecha.value = ''; // Limpiamos si se deshabilita
    }
}
</script>
<script>
       function abrirFlujoAbono() {
        const totalVenta = parseFloat(ventaActual.info.total || 0);
        const pagado = parseFloat(ventaActual.info.total_pagado || 0);
        const saldoPendiente = totalVenta - pagado;

        if (saldoPendiente <= 0) {
            Swal.fire('Venta Liquidada', 'Sin saldo pendiente.', 'success');
            return;
        }

        // Llenamos los datos en el mini-modal
        $('#inputMontoAbono').val(saldoPendiente.toFixed(2));
        $('#infoSaldo').text('Saldo máximo: $' + saldoPendiente.toFixed(2));

        // Mostramos el modal
        modalAbonoObj.show();

        // Forzamos el foco al abrir (esto ya no fallará)
        document.getElementById('modalAbono').addEventListener('shown.bs.modal', () => {
            document.getElementById('inputMontoAbono').focus();
            document.getElementById('inputMontoAbono').select();
        }, {
            once: true
        });
    }

    // Agrega este listener para validar en tiempo real mientras el usuario escribe
    $(document).on('input', '#inputMontoAbono', function() {
        const totalVenta = parseFloat(ventaActual.info.total || 0);
        const pagado = parseFloat(ventaActual.info.total_pagado || 0);
        const saldoPendiente = parseFloat((totalVenta - pagado).toFixed(2));
        const montoIngresado = parseFloat($(this).val()) || 0;

        if (montoIngresado > saldoPendiente) {
            $(this).addClass('is-invalid text-danger');
            $('#infoSaldo').removeClass('bg-light text-dark').addClass('bg-danger text-white');
        } else {
            $(this).removeClass('is-invalid text-danger');
            $('#infoSaldo').removeClass('bg-danger text-white').addClass('bg-light text-dark');
        }
    });

     async function guardarAbonoModal() {
    const totalVenta = parseFloat(ventaActual.info.total || 0);
    const pagado = parseFloat(ventaActual.info.total_pagado || 0);
    const saldoPendiente = parseFloat((totalVenta - pagado).toFixed(2));
    const monto = parseFloat($('#inputMontoAbono').val());
    const metodo = $('#selectMetodoPago').val();

    // --- LÓGICA DE FECHA Y HORA ---
    const checkFechaManual = document.getElementById('checkFechaPersonalizada');
    const inputFechaManual = document.getElementById('inputFechaAbono');
    let fechaFinal = "";

    if (checkFechaManual && checkFechaManual.checked && inputFechaManual.value) {
        // Si el usuario eligió una fecha manual, convertimos el formato T de datetime-local a espacio
        fechaFinal = inputFechaManual.value.replace('T', ' ') + ':00';
    } else {
        // Si no está habilitado, generamos la fecha y hora actual del sistema
        const ahora = new Date();
        const yyyy = ahora.getFullYear();
        const mm = String(ahora.getMonth() + 1).padStart(2, '0');
        const dd = String(ahora.getDate()).padStart(2, '0');
        const hh = String(ahora.getHours()).padStart(2, '0');
        const min = String(ahora.getMinutes()).padStart(2, '0');
        const ss = String(ahora.getSeconds()).padStart(2, '0');
        
        fechaFinal = `${yyyy}-${mm}-${dd} ${hh}:${min}:${ss}`;
    }

    // --- VALIDACIONES ---
    if (!monto || monto <= 0) {
        return Swal.fire('Error', 'Ingrese un monto válido', 'warning');
    }

    if (monto > saldoPendiente) {
        return Swal.fire('Error', `El monto excede el saldo ($${saldoPendiente})`, 'error');
    }

    // --- PREPARACIÓN DE ENVÍO ---
    const fd = new FormData();
    fd.append('venta_id', ventaActual.info.id);
    fd.append('monto', monto);
    fd.append('metodo_pago', metodo);
    fd.append('fecha_pago', fechaFinal); // <--- Enviamos la fecha calculada

    try {
        const res = await fetch(`${URL_CONTROLLER}?action=guardarAbono`, {
            method: 'POST',
            body: fd
        });
        
        // Manejo de errores de JSON ( Unexpected end of input )
        const text = await res.text();
        let data;
        try {
            data = JSON.parse(text);
        } catch (e) {
            throw new Error("Respuesta inválida del servidor: " + text);
        }

        if (data.status === 'success') {
            modalAbonoObj.hide();
            Swal.fire('Éxito', 'Abono guardado correctamente', 'success');
            await verDetalle(ventaActual.info.id);
            if (typeof getVentas === "function") getVentas();
        } else {
            Swal.fire('Error', data.message || 'Error al guardar', 'error');
        }
    } catch (e) {
        console.error("Error en la petición:", e);
        Swal.fire('Error Crítico', e.message, 'error');
    }
}
</script>