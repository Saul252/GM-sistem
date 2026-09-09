<!-- Librerías requeridas vía CDN -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    /* Botón con gradiente e interacción hover */
    .btn-gradient {
        background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
        color: #ffffff;
        border: none;
    }
    .btn-gradient:hover {
        background: linear-gradient(135deg, #0b5ed7 0%, #084298 100%);
        color: #ffffff;
    }

    /* Contenedores con variables nativas de Bootstrap 5 para modo claro / oscuro */
    .box-dashed-highlight {
        background-color: var(--bs-tertiary-bg);
        border: 1px dashed var(--bs-border-color);
    }
    .box-info-highlight {
        background-color: var(--bs-secondary-bg);
        border: 1px solid var(--bs-border-color-translucent);
    }
</style>

<div class="modal fade" id="modalVehiculo" tabindex="-1" role="dialog" aria-labelledby="modalLabel">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            
            <div class="modal-header border-0 pt-4 px-4">
                <h5 class="fw-bold m-0 text-body" id="modalLabel">
                    <i class="bi bi-truck-flatbed me-2 text-primary"></i>Despacho de Logística
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="formReparto">
                <div class="modal-body p-4">
                    
                    <!-- Resumen del Producto -->
                    <div class="p-3 rounded-4 mb-3 box-dashed-highlight">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <small class="text-body-secondary fw-bold d-block mb-1" style="font-size: 0.65rem;">MATERIAL A ENTREGAR</small>
                                <div id="info_producto_modal" class="fw-bold text-body">---</div>
                            </div>
                            <div class="text-end">
                                <small class="text-body-secondary fw-bold d-block mb-1" style="font-size: 0.65rem;">CANTIDAD</small>
                                <div id="info_cantidad_modal">---</div>
                            </div>
                        </div>
                    </div>

                    <!-- Datos del Cliente y Obra -->
                    <div class="p-3 rounded-4 mb-4 box-info-highlight">
                        <div class="mb-3">
                            <small class="text-body-secondary fw-bold d-block mb-1" style="font-size: 0.65rem;">CLIENTE RECEPTOR</small>
                            <div class="d-flex align-items-center">
                                <i class="bi bi-person-check-fill text-primary me-2"></i>
                                <span id="v_cliente_nombre" class="fw-bold text-body small">---</span>
                            </div>
                        </div>

                        <div class="mb-0">
                            <label class="small fw-bold text-body-secondary mb-1" style="font-size: 0.65rem;">PUNTO DE ENTREGA / OBRA (EDITABLE)</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-body-tertiary border-0 text-danger shadow-sm">
                                    <i class="bi bi-geo-alt-fill"></i>
                                </span>
                                <textarea name="direccion_entrega" id="v_direccion_entrega" 
                                          class="form-control bg-body-tertiary text-body border-0 shadow-sm p-2" 
                                          rows="2" style="font-size: 0.85rem;" 
                                          placeholder="Dirección exacta..." required></textarea>
                            </div>
                        </div>
                    </div>

                    <input type="hidden" name="movimiento_id" id="rep_movimiento_id">
                    <input type="hidden" name="almacen_id" id="rep_almacen_id">

                    <!-- Selección de Personal y Unidad -->
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label small fw-bold text-body-secondary mb-1">UNIDAD DE TRANSPORTE</label>
                            <select name="vehiculo_id" id="v_vehiculo_id" class="form-select border-0 bg-body-tertiary text-body p-3 rounded-3 shadow-sm" required>
                                <option value="">Cargando unidades...</option>
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label small fw-bold text-body-secondary mb-1">OPERADOR RESPONSABLE (CHOFER)</label>
                            <select name="chofer_id" id="v_chofer_id" class="form-select border-0 bg-body-tertiary text-body p-3 rounded-3 shadow-sm" required>
                                <option value="">Cargando choferes...</option>
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label small fw-bold text-body-secondary mb-1">AYUDANTES / TRIPULACIÓN ADICIONAL</label>
                            <select name="tripulantes[]" id="v_tripulantes" class="form-select border-0 bg-body-tertiary text-body p-2 rounded-3 shadow-sm" style="font-size: 0.85rem;" multiple size="3">
                            </select>
                            <small class="text-body-secondary d-block mt-1" style="font-size: 0.65rem;">* Control + Click para seleccionar varios</small>
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-0 pb-4 px-4">
                    <button type="button" class="btn btn-link text-body-secondary fw-bold text-decoration-none" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" id="btnGuardarReparto" class="btn btn-gradient px-4 py-2 shadow-sm rounded-3 fw-bold">
                        <i class="bi bi-send-check me-2"></i>Confirmar Salida
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // 1. Configuración de Ruta
    const URL_ENTREGAS = '/cfsistem/app/controllers/entregasController.php';

    /**
     * Exclusión de Chofer en Ayudantes
     * Escucha el cambio en el select de chofer para deshabilitar esa opción en ayudantes.
     */
    $('#v_chofer_id').on('change', function() {
        const selectedChofer = $(this).val();
        
        $('#v_tripulantes option').each(function() {
            const val = $(this).val();
            if (selectedChofer && val === selectedChofer) {
                $(this).prop('disabled', true).hide();
                if ($(this).is(':selected')) {
                    $(this).prop('selected', false);
                }
            } else {
                $(this).prop('disabled', false).show();
            }
        });
    });

    /**
     * FUNCIÓN AUXILIAR: Formateo de cantidades
     */
    function formatUnits(cantidad, factor, uReporte, uMedida) {
        const qty = parseFloat(cantidad) || 0;
        const f = parseFloat(factor) || 1;
        const unitRep = uReporte || 'Unid.';
        const unitMed = uMedida || 'Pz';

        if (f > 1) {
            const enteros = Math.floor(qty / f);
            const sobrantes = qty % f;

            let partes = [];
            if (enteros > 0) partes.push(`<span class="fw-bold text-primary">${enteros}</span> ${unitRep}`);
            if (sobrantes > 0) partes.push(`<span class="fw-bold text-primary">${sobrantes}</span> ${unitMed}`);

            return partes.length > 0 ? partes.join(' + ') : `0 ${unitMed}`;
        }
        return `<span class="fw-bold text-primary">${qty}</span> ${unitMed}`;
    }

    /**
     * FUNCIÓN B: Cargar recursos y abrir modal
     */
    window.prepararModalReparto = async function(movimientoId, almacenId) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({ 
                title: 'Sincronizando...', 
                allowOutsideClick: false, 
                didOpen: () => Swal.showLoading() 
            });
        }

        try {
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
                
                const htmlCantidad = formatUnits(
                    e.cantidad, 
                    e.factor_conversion, 
                    e.unidad_reporte, 
                    e.unidad_medida
                );
                $('#info_cantidad_modal').html(htmlCantidad);

                // Llenar Unidades / Vehículos
                const selectU = $('#v_vehiculo_id').empty().append('<option value="">Seleccione camión...</option>');
                if (resRecursos.unidades && resRecursos.unidades.length > 0) {
                    resRecursos.unidades.forEach(u => {
                        selectU.append(`<option value="${u.id}">${u.nombre} [${u.placas || 'S/P'}]</option>`);
                    });
                } else {
                    selectU.append('<option disabled>❌ Sin camiones disponibles</option>');
                }

                // Handler dinámico al cambiar vehículo
                $('#v_vehiculo_id').off('change').on('change', function () {
                    const vehiculo_id = $(this).val();
                    if (!vehiculo_id) return;

                    fetch(`${URL_ENTREGAS}?ajax=get_datos_vehiculo&vehiculo_id=${vehiculo_id}`)
                        .then(res => res.json())
                        .then(res => {
                            const selectC = $('#v_chofer_id').empty().append('<option value="">Seleccione chofer...</option>');
                            const selectT = $('#v_tripulantes').empty();

                            if (resRecursos.choferes && resRecursos.choferes.length > 0) {
                                resRecursos.choferes.forEach(c => {
                                    const opt = `<option value="${c.id}">${c.nombre}</option>`;
                                    selectC.append(opt);
                                    selectT.append(opt);
                                });
                            }

                            if (res.success && res.data && (res.data.encargado || (res.data.tripulantes && res.data.tripulantes.length))) {
                                const data = res.data;

                                if (data.encargado && data.encargado.id) {
                                    $('#v_chofer_id').val(data.encargado.id).trigger('change');
                                }

                                if (Array.isArray(data.tripulantes)) {
                                    const ids = data.tripulantes.map(t => t.id);
                                    $('#v_tripulantes').val(ids).trigger('change');
                                }
                            } else {
                                selectC.empty().append('<option value="">Seleccione chofer...</option>');
                                selectT.empty();

                                if (resRecursos.trabajadoresDisponibles) {
                                    resRecursos.trabajadoresDisponibles.forEach(t => {
                                        const opt = `<option value="${t.id}">${t.nombre}</option>`;
                                        selectC.append(opt);
                                        selectT.append(opt);
                                    });
                                }
                                
                                $('#v_chofer_id').val('').trigger('change');
                                $('#v_tripulantes').val([]).trigger('change');
                            }
                        })
                        .catch(err => console.error('Error en fetch:', err));
                });

                // Llenar Choferes y Ayudantes iniciales
                const selectC = $('#v_chofer_id').empty().append('<option value="">Seleccione chofer...</option>');
                const selectT = $('#v_tripulantes').empty();

                if (resRecursos.choferes && resRecursos.choferes.length > 0) {
                    resRecursos.choferes.forEach(c => {
                        selectC.append(`<option value="${c.id}">${c.nombre}</option>`);
                        selectT.append(`<option value="${c.id}">${c.nombre}</option>`);
                    });
                }

                $('#v_chofer_id').trigger('change');

                if (typeof Swal !== 'undefined') Swal.close();
                $('#modalVehiculo').modal('show');
            } else {
                throw new Error("No se pudieron cargar los datos del servidor.");
            }
        } catch (error) {
            console.error("Error modal:", error);
            Swal.fire('Error', 'No se pudo conectar con el almacén.', 'error');
        }
    };

    /**
     * FUNCIÓN C: Guardar el despacho
     */
    $('#formReparto').on('submit', async function(e) {
        e.preventDefault();
        const btn = $('#btnGuardarReparto');
        const originalHtml = btn.html();
        
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Enviando...');

        try {
            const formData = new FormData(this);
            formData.append('ajax', 'guardar_reparto');

            const resp = await fetch(URL_ENTREGAS, { method: 'POST', body: formData });
            const res = await resp.json();

            if (res.success) {
                $('#modalVehiculo').modal('hide');
                Swal.fire({
                    icon: 'success',
                    title: 'Salida Autorizada',
                    text: res.message,
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    location.reload();
                });
            } else {
                Swal.fire('Atención', res.message, 'warning');
            }
        } catch (error) {
            Swal.fire('Error', 'Fallo crítico al procesar el despacho.', 'error');
        } finally {
            btn.prop('disabled', false).html(originalHtml);
        }
    });
});
</script>