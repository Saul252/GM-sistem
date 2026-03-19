<div class="modal fade" id="modalVehiculo" tabindex="-1" role="dialog" aria-labelledby="modalLabel">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header border-0 pt-4 px-4">
                <h5 class="fw-bold m-0 text-dark" id="modalLabel">
                    <i class="bi bi-truck-flatbed me-2 text-primary"></i>Despacho de Logística
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="formReparto">
                <div class="modal-body p-4">
                    <div class="p-3 rounded-4 mb-3" style="background: #f8f9fa; border: 1px dashed #dee2e6;">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <small class="text-muted fw-bold d-block mb-1" style="font-size: 0.65rem;">MATERIAL A ENTREGAR</small>
                                <div id="info_producto_modal" class="fw-bold text-dark">---</div>
                            </div>
                            <div class="text-end">
                                <small class="text-muted fw-bold d-block mb-1" style="font-size: 0.65rem;">CANTIDAD</small>
                                <div id="info_cantidad_modal">---</div>
                            </div>
                        </div>
                    </div>

                    <div class="p-3 rounded-4 mb-4" style="background: #eef6ff; border: 1px solid #cfe2ff;">
                        <div class="mb-3">
                            <small class="text-muted fw-bold d-block mb-1" style="font-size: 0.65rem;">CLIENTE RECEPTOR</small>
                            <div class="d-flex align-items-center">
                                <i class="bi bi-person-check-fill text-primary me-2"></i>
                                <span id="v_cliente_nombre" class="fw-bold text-dark small">---</span>
                            </div>
                        </div>

                        <div class="mb-0">
                            <label class="small fw-bold text-muted mb-1" style="font-size: 0.65rem;">PUNTO DE ENTREGA / OBRA (EDITABLE)</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-white border-0 shadow-sm"><i class="bi bi-geo-alt-fill text-danger"></i></span>
                                <textarea name="direccion_entrega" id="v_direccion_entrega" 
                                          class="form-control border-0 shadow-sm p-2" 
                                          rows="2" style="font-size: 0.85rem;" 
                                          placeholder="Dirección exacta..." required></textarea>
                            </div>
                        </div>
                    </div>

                    <input type="hidden" name="movimiento_id" id="rep_movimiento_id">

                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label small fw-bold text-muted mb-1">UNIDAD DE TRANSPORTE</label>
                            <select name="vehiculo_id" id="v_vehiculo_id" class="form-select border-0 bg-light p-3 rounded-3 shadow-sm" required>
                                <option value="">Seleccione camión...</option>
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label small fw-bold text-muted mb-1">OPERADOR RESPONSABLE (CHOFER)</label>
                            <select name="chofer_id" id="v_chofer_id" class="form-select border-0 bg-light p-3 rounded-3 shadow-sm" required>
                                <option value="">Seleccione chofer...</option>
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label small fw-bold text-muted mb-1">AYUDANTES / TRIPULACIÓN ADICIONAL</label>
                            <select name="tripulantes[]" id="v_tripulantes" class="form-select border-0 bg-light p-2 rounded-3 shadow-sm" style="font-size: 0.85rem;" multiple size="3">
                            </select>
                            <small class="text-muted" style="font-size: 0.6rem;">* Control + Click para seleccionar varios</small>
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-0 pb-4 px-4">
                    <button type="button" class="btn btn-link text-muted fw-bold text-decoration-none" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" id="btnGuardarReparto" class="btn btn-gradient px-4 py-2 shadow">
                        <i class="bi bi-send-check me-2"></i>Confirmar Salida
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
/**
 * LÓGICA DEL MODAL DE REPARTO
 */
$(document).ready(function() {
    const URL_CONTROLLER = '/cfsistem/app/controllers/repartosController.php';

    // 1. FUNCIÓN GLOBAL PARA ABRIR EL MODAL
    window.prepararModalReparto = async function(movimientoId) {
        if(typeof Swal !== 'undefined') Swal.fire({ title: 'Cargando...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });

        try {
            const resp = await fetch(`${URL_CONTROLLER}?action=get_recursos_reparto&id=${movimientoId}`);
            const res = await resp.json();
            if(typeof Swal !== 'undefined') Swal.close();

            if (res.success) {
                const e = res.data.entrega;
                $('#rep_movimiento_id').val(movimientoId);
                $('#info_producto_modal').text(e.producto_nombre);
                $('#info_cantidad_modal').html(window.formatQty(e.cantidad, e.factor_conversion, e.unidad_reporte));
                $('#v_cliente_nombre').text(e.cliente_nombre || 'Venta Mostrador');
                $('#v_direccion_entrega').val(e.cliente_direccion_fiscal || '');

                // Llenar Unidades
                const selectU = $('#v_vehiculo_id').empty().append('<option value="">Seleccione camión...</option>');
                res.data.unidades.forEach(u => selectU.append(`<option value="${u.id}">${u.nombre} [${u.placas}]</option>`));

                // Llenar Chofer y Tripulación
                const selectC = $('#v_chofer_id').empty().append('<option value="">Seleccione chofer...</option>');
                const selectT = $('#v_tripulantes').empty(); 

                res.data.choferes.forEach(c => {
                    selectC.append(`<option value="${c.id}">${c.nombre}</option>`);
                    selectT.append(`<option value="${c.id}" id="opt_t_${c.id}">${c.nombre}</option>`);
                });

                $('#modalVehiculo').modal('show');
            }
        } catch (error) {
            console.error(error);
            if(typeof Swal !== 'undefined') Swal.fire('Error', 'No se pudo cargar la información del servidor.', 'error');
        }
    };

    // 2. EXCLUIR CHOFER DE TRIPULANTES
    $(document).on('change', '#v_chofer_id', function() {
        const choferId = $(this).val();
        $('#v_tripulantes option').show().prop('disabled', false);
        if (choferId) {
            const opt = $(`#opt_t_${choferId}`);
            opt.hide().prop('disabled', true).prop('selected', false);
        }
    });

    // 3. GUARDAR FORMULARIO (UNIFICADO)
    $('#formReparto').on('submit', async function(e) {
        e.preventDefault();

        // Validación básica
        if(!$('#v_vehiculo_id').val() || !$('#v_chofer_id').val()) {
            Swal.fire('Atención', 'Selecciona unidad y chofer responsable.', 'warning');
            return;
        }

        const btn = $('#btnGuardarReparto');
        const originalText = btn.html();
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Guardando...');

        try {
            const formData = new FormData(this);
            formData.append('action', 'guardar_reparto');

            const resp = await fetch(URL_CONTROLLER, { method: 'POST', body: formData });
            
            // Capturamos la respuesta como texto primero para validar si es JSON
            const rawResponse = await resp.text();
            
            try {
                const res = JSON.parse(rawResponse);
                if (res.success) {
                    $('#modalVehiculo').modal('hide'); 
                    Swal.fire({ icon: 'success', title: '¡Listo!', text: res.message, timer: 1500, showConfirmButton: false });
                    
                    if(typeof window.cargarPendientes === 'function') {
                        window.cargarPendientes();
                    }
                } else {
                    throw new Error(res.message || 'Error desconocido en el servidor');
                }
            } catch (jsonErr) {
                console.error("Respuesta no válida del servidor:", rawResponse);
                throw new Error("El servidor devolvió un error de sistema. Revisa la consola.");
            }

        } catch (error) {
            Swal.fire('Error al guardar', error.message, 'error');
        } finally {
            btn.prop('disabled', false).html(originalText);
        }
    });

    // 4. LIMPIAR AL CERRAR
    $('#modalVehiculo').on('hidden.bs.modal', function () {
        $('#formReparto')[0].reset();
        $('#v_tripulantes option').show().prop('disabled', false);
    });
});
</script>