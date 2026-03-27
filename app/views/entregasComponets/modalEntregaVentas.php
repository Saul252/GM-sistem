<div class="modal fade" id="modalDespachoVenta" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 25px; background: #f8f9fa;">
            <div class="modal-header border-0 bg-white" style="border-radius: 25px 25px 0 0; padding: 1.5rem 2rem;">
                <div class="bg-success text-white rounded p-2 d-flex align-items-center justify-content-center me-3" style="width: 45px; height: 45px; border-radius: 12px !important;">
                    <i class="bi bi-box-seam-fill fs-4"></i>
                </div>
                <div>
                    <h5 class="modal-title fw-bold mb-0">Despacho Masivo de Venta</h5>
                    <span class="badge bg-light text-dark border mt-1" id="txtFolioVenta">Cargando...</span>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body px-4 py-3">
                <div id="listaItemsDespacho" class="pe-2 mb-2" style="max-height: 250px; overflow-y: auto;"></div>

                <div id="seccionLogisticaMasiva" class="d-none animate__animated animate__fadeIn">
                    <hr class="my-4 opacity-10">
                    
                    <div class="p-3 border rounded-4 bg-white shadow-sm mb-3">
                        <label class="text-uppercase fw-bold text-primary mb-2 d-block" style="font-size: 0.7rem; letter-spacing: 1.2px;">Método de Salida</label>
                        <div class="btn-group w-100" role="group">
                            <input type="radio" class="btn-check" name="tipo_entrega_masiva" id="optPatio" value="patio" checked onchange="toggleFormRuta(false)">
                            <label class="btn btn-outline-primary rounded-start-pill py-2 fw-bold" for="optPatio">
                                <i class="bi bi-box-seam me-2"></i>ENTREGA EN PATIO
                            </label>
                            
                            <input type="radio" class="btn-check" name="tipo_entrega_masiva" id="optRuta" value="ruta" onchange="toggleFormRuta(true)">
                            <label class="btn btn-outline-dark rounded-end-pill py-2 fw-bold" for="optRuta">
                                <i class="bi bi-truck me-2"></i>ASIGNAR RUTA
                            </label>
                        </div>
                    </div>

                   

                    <div id="formRutaIntegrado" class="d-none animate__animated animate__fadeInUp">
                        <div class="p-4 rounded-4 shadow-sm" style="background: #eef6ff; border: 1px solid #cfe2ff;">
                            <div class="row g-3">
                                 <div id="contenedorDireccion" class="p-3 rounded-4 mb-3 border bg-white shadow-sm animate__animated animate__fadeIn">
                        <label class="small fw-bold text-muted mb-1" style="font-size: 0.65rem;">PUNTO DE ENTREGA / OBRA (EDITABLE)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-0"><i class="bi bi-geo-alt-fill text-danger"></i></span>
                            <textarea id="mv_direccion" class="form-control border-0 p-2" rows="2" style="font-size: 0.9rem; resize: none;" placeholder="Dirección exacta de entrega..."></textarea>
                        </div>
                    </div>
                                <div class="col-md-6">
                                    <label class="small fw-bold text-muted mb-1">UNIDAD / VEHÍCULO</label>
                                    <select id="mv_vehiculo_id" class="form-select border-0 shadow-sm rounded-3 p-3"></select>
                                </div>
                                <div class="col-md-6">
                                    <label class="small fw-bold text-muted mb-1">CHOFER RESPONSABLE</label>
                                    <select id="mv_chofer_id" class="form-select border-0 shadow-sm rounded-3 p-3"></select>
                                </div>
                                <div class="col-12">
                                    <label class="small fw-bold text-muted mb-1">AYUDANTES / TRIPULACIÓN</label>
                                    <select id="mv_tripulantes" class="form-select border-0 shadow-sm rounded-3 p-2" multiple size="3" style="font-size: 0.85rem;"></select>
                                    <small class="text-muted mt-2 d-block" style="font-size: 0.6rem;">* Mantén presionada la tecla <b>Ctrl</b> para elegir varios ayudantes.</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer border-0 bg-white py-3 px-4" style="border-radius: 0 0 25px 25px;">
                <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" id="btnEjecutarDespachoMasivo" class="btn btn-success rounded-pill px-5 fw-bold shadow" disabled>
                    <i class="bi bi-check-circle me-2"></i>Confirmar Despacho
                </button>
            </div>
        </div>
    </div>
</div>

<script>
/**
 * Alterna la visualización de los campos de ruta
 */
function toggleFormRuta(mostrar) {
    const form = document.getElementById('formRutaIntegrado');
    mostrar ? form.classList.remove('d-none') : form.classList.add('d-none');
}

/**
 * FUNCIÓN PRINCIPAL: Abre modal, carga lotes y carga recursos de logística
 */
async function abrirModalDespachoVenta(ventaId,almacenId) {
    const URL_ENTREGAS = 'entregasController.php'; 
    const modalElement = document.getElementById('modalDespachoVenta');
    const modal = new bootstrap.Modal(modalElement);
    const contenedor = document.getElementById('listaItemsDespacho');
    const txtFolio = document.getElementById('txtFolioVenta');
    const btnConfirmar = document.getElementById('btnEjecutarDespachoMasivo');
    const logSection = document.getElementById('seccionLogisticaMasiva');

    // Reset UI
    txtFolio.innerHTML = `<span class="opacity-50 small">Sincronizando con almacén...</span>`;
    contenedor.innerHTML = `<div class="text-center py-5"><div class="spinner-border text-success opacity-25"></div></div>`;
    logSection.classList.add('d-none');
    $('#formRutaIntegrado').addClass('d-none');
    $('#optPatio').prop('checked', true);
    btnConfirmar.disabled = true;
    modal.show();

    try {
        // 1. Obtener IDs de la venta
        const respIds = await fetch(`${URL_ENTREGAS}?ajax=get_ids_pendientes_venta&venta_id=${ventaId}`);
        const dataIds = await respIds.json();
        if (!dataIds.success || !dataIds.ids?.length) throw new Error("No hay pendientes.");

        const idsParaProcesar = dataIds.ids;
        const primerId = idsParaProcesar[0];
        const paramsLotes = idsParaProcesar.map(id => `ids[]=${id}`).join('&');
        
        // 2. Simular Stock
        const respSim = await fetch(`${URL_ENTREGAS}?ajax=simular_masivo&${paramsLotes}`);
        const sim = await respSim.json();
        if (!sim.success) throw new Error(sim.message);

        txtFolio.innerHTML = `<i class="bi bi-hash opacity-50"></i>${ventaId}`;
        
        // Renderizado de items
        let htmlLotes = '';
        sim.data.forEach(item => {
            htmlLotes += `
                <div class="mb-2 p-2 bg-white rounded-3 border shadow-sm small d-flex justify-content-between">
                    <span class="fw-bold">${item.producto}</span>
                    <span class="badge bg-success-subtle text-success">${parseFloat(item.total_solicitado)} PZA</span>
                </div>`;
        });
        contenedor.innerHTML = htmlLotes;

        // 3. Validar y Cargar Recursos
        const hayFaltantes = sim.data.some(i => parseFloat(i.pendiente) > 0);
        
        if (!hayFaltantes) {
            

            // CARGA PARALELA (Igual a tu función prepararModalReparto)
            const [respDetalle, respRecursos] = await Promise.all([
                fetch(`${URL_ENTREGAS}?ajax=get_recursos_reparto&id=${primerId}`),
                fetch(`${URL_ENTREGAS}?ajax=get_recursos_sucursal&almacen_id=${almacenId}`)
            ]);

            const resDetalle = await respDetalle.json();
            const resRecursos = await respRecursos.json();

            if (resDetalle.success && resRecursos.success) {
                const e = resDetalle.data.entrega;

                // DIRECCIÓN EDITABLE: Se llena automáticamente pero el usuario puede cambiarla
                $('#mv_direccion').val(e.cliente_direccion_fiscal || '');

                // Llenar Vehículos
                const selectV = $('#mv_vehiculo_id').empty().append('<option value="">Seleccione unidad...</option>');
                resRecursos.unidades.forEach(u => selectV.append(`<option value="${u.id}">${u.nombre} [${u.placas || 'S/P'}]</option>`));

                // Llenar Choferes y Ayudantes
                const selectC = $('#mv_chofer_id').empty().append('<option value="">Seleccione chofer...</option>');
                const selectT = $('#mv_tripulantes').empty();
                resRecursos.choferes.forEach(c => {
                    selectC.append(`<option value="${c.id}">${c.nombre}</option>`);
                    selectT.append(`<option value="${c.id}">${c.nombre}</option>`);
                });

                // Lógica de exclusión chofer/ayudante
                $('#mv_chofer_id').off('change').on('change', function() {
                    const sel = $(this).val();
                    $('#mv_tripulantes option').each(function() {
                        if (sel && $(this).val() === sel) {
                            $(this).prop('disabled', true).hide().prop('selected', false);
                        } else {
                            $(this).prop('disabled', false).show();
                        }
                    });
                });

                logSection.classList.remove('d-none');
                btnConfirmar.disabled = false;
                btnConfirmar.onclick = () => ejecutarSalidaMasivaFinal(idsParaProcesar, btnConfirmar);
            }
        } else {
            contenedor.innerHTML += `<div class="alert alert-danger mt-2 small">Falta stock para salida masiva.</div>`;
        }
    } catch (err) {
        contenedor.innerHTML = `<div class="alert alert-danger mx-2 small">${err.message}</div>`;
    }
}

function toggleFormRuta(mostrar) {
    const form = $('#formRutaIntegrado');
    mostrar ? form.removeClass('d-none').addClass('animate__fadeInUp') : form.addClass('d-none');
}
/**
 * PROCESO FINAL: Envía despacho + logística en una sola petición
 */
async function ejecutarSalidaFinal(ids, boton) {
    const tipo = $('input[name="tipo_entrega_masiva"]:checked').val();
    
    if (tipo === 'ruta' && (!$('#mv_vehiculo_id').val() || !$('#mv_chofer_id').val())) {
        return Swal.fire('Atención', 'Datos de ruta incompletos', 'warning');
    }

    boton.disabled = true;
    boton.innerHTML = `<span class="spinner-border spinner-border-sm me-2"></span>Procesando...`;

    try {
        const formData = new FormData();
        formData.append('ajax', 'despachar_y_entregar_masivo');
        formData.append('tipo_logistica', tipo);
        formData.append('vehiculo_id', $('#mv_vehiculo_id').val());
        formData.append('chofer_id', $('#mv_chofer_id').val());
        formData.append('direccion', $('#mv_direccion').val());
        ids.forEach(id => formData.append('ids_movimientos[]', id));

        const resp = await fetch('/cfsistem/app/controllers/repartosController.php', { method: 'POST', body: formData });
        const res = await resp.json();

        if (res.success) {
            Swal.fire({ icon: 'success', title: '¡Listo!', text: res.message, timer: 2000, showConfirmButton: false })
            .then(() => location.reload());
        } else {
            throw new Error(res.message);
        }
    } catch (e) {
        Swal.fire('Error', e.message, 'error');
        boton.disabled = false;
        boton.innerHTML = '<i class="bi bi-check-circle me-2"></i>Procesar Salida';
    }
}
</script>