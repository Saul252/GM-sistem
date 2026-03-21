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
                    <input type="hidden" name="almacen_id" id="rep_almacen_id">

                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label small fw-bold text-muted mb-1">UNIDAD DE TRANSPORTE</label>
                            <select name="vehiculo_id" id="v_vehiculo_id" class="form-select border-0 bg-light p-3 rounded-3 shadow-sm" required>
                                <option value="">Cargando unidades...</option>
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label small fw-bold text-muted mb-1">OPERADOR RESPONSABLE (CHOFER)</label>
                            <select name="chofer_id" id="v_chofer_id" class="form-select border-0 bg-light p-3 rounded-3 shadow-sm" required>
                                <option value="">Cargando choferes...</option>
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
                    <button type="submit" id="btnGuardarReparto" class="btn btn-primary px-4 py-2 shadow rounded-pill">
                        <i class="bi bi-send-check me-2"></i>Confirmar Salida
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // CAMBIO 1: Nueva ruta del controlador
    const URL_ENTREGAS = '/cfsistem/app/controllers/entregasController.php';

    window.prepararModalReparto = async function(movimientoId, almacenId) {
        if(typeof Swal !== 'undefined') {
            Swal.fire({ 
                title: 'Cargando recursos...', 
                allowOutsideClick: false, 
                didOpen: () => Swal.showLoading() 
            });
        }

        try {
            // CAMBIO 2: Usar ajax= en lugar de action=
            const [respDetalle, respRecursos] = await Promise.all([
                fetch(`${URL_ENTREGAS}?ajax=get_recursos_reparto&id=${movimientoId}`),
                fetch(`${URL_ENTREGAS}?ajax=get_recursos_sucursal&almacen_id=${almacenId}`)
            ]);

            const resDetalle = await respDetalle.json();
            const resRecursos = await respRecursos.json();

            if (resDetalle.success && resRecursos.success) {
                const e = resDetalle.data.entrega;

                $('#rep_movimiento_id').val(movimientoId);
                $('#rep_almacen_id').val(almacenId);
                $('#info_producto_modal').text(e.producto_nombre || e.producto);
                $('#v_cliente_nombre').text(e.cliente_nombre || 'Venta Mostrador');
                $('#v_direccion_entrega').val(e.cliente_direccion_fiscal || '');
                
                // Ajuste de cantidad
                $('#info_cantidad_modal').text(`${e.cantidad} ${e.unidad_reporte || ''}`);

                // CAMBIO 3: Llenar selects con los datos del controlador de entregas
                const selectU = $('#v_vehiculo_id').empty().append('<option value="">Seleccione camión...</option>');
                if(resRecursos.unidades && resRecursos.unidades.length > 0) {
                    resRecursos.unidades.forEach(u => selectU.append(`<option value="${u.id}">${u.nombre} [${u.placas || ''}]</option>`));
                } else {
                    selectU.append('<option disabled>No hay unidades disponibles</option>');
                }

                const selectC = $('#v_chofer_id').empty().append('<option value="">Seleccione chofer...</option>');
                const selectT = $('#v_tripulantes').empty();
                if(resRecursos.choferes && resRecursos.choferes.length > 0) {
                    resRecursos.choferes.forEach(c => {
                        selectC.append(`<option value="${c.id}">${c.nombre}</option>`);
                        selectT.append(`<option value="${c.id}">${c.nombre}</option>`);
                    });
                }

                if(typeof Swal !== 'undefined') Swal.close();
                $('#modalVehiculo').modal('show');
            } else {
                throw new Error(resDetalle.message || resRecursos.message || "Error al cargar datos");
            }
        } catch (error) {
            console.error("Error en el modal:", error);
            Swal.fire('Error', 'No se pudieron sincronizar los datos del almacén.', 'error');
        }
    };

    $('#formReparto').on('submit', async function(e) {
        e.preventDefault();
        const btn = $('#btnGuardarReparto');
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Enviando...');

        try {
            const formData = new FormData(this);
            // CAMBIO 4: Mandar la acción como 'ajax' para que el controlador lo reciba en el bloque POST
            formData.append('ajax', 'guardar_reparto');

            const resp = await fetch(URL_ENTREGAS, { 
                method: 'POST', 
                body: formData 
            });
            const res = await resp.json();

            if (res.success) {
                $('#modalVehiculo').modal('hide');
                Swal.fire({
                    icon: 'success',
                    title: 'Logística Confirmada',
                    text: res.message,
                    timer: 2000
                });
                if (window.cargarPendientes) window.cargarPendientes();
            } else {
                Swal.fire('Atención', res.message, 'warning');
            }
        } catch (error) {
            console.error(error);
            Swal.fire('Error', 'Ocurrió un fallo en el servidor al procesar el reparto.', 'error');
        } finally {
            btn.prop('disabled', false).html('<i class="bi bi-send-check me-2"></i>Confirmar Salida');
        }
    });
});
</script>