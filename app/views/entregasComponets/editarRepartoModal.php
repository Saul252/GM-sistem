<div class="modal fade" id="modalEditarViaje" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; background: #f5f5f7;">
            <div class="modal-header bg-white border-bottom-0 pb-0" style="border-radius: 20px 20px 0 0;">
                <div class="d-flex align-items-center">
                    <div class="icon-box me-3" style="background: #e5e5ea; padding: 10px; border-radius: 12px;">
                        <i class="bi bi-pencil-square text-primary"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0" id="editFolioTitle">Editando Ruta</h5>
                        <small class="text-muted" id="editUnidadSubtitle"></small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body p-4">
                <form id="formEditarViaje">
                    <input type="hidden" id="edit_viaje_folio" name="viaje_folio">
                    <input type="hidden" id="edit_vehiculo_id" name="vehiculo_id">

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase">Responsable (Chofer)</label>
                            <select id="edit_chofer_id" name="chofer_id" class="form-select border-0 shadow-sm" style="border-radius: 10px; padding: 12px;"></select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase">Tripulación (Ayudantes)</label>
                            <select id="edit_tripulantes" name="tripulantes[]" class="form-select border-0 shadow-sm" multiple style="border-radius: 10px;"></select>
                        </div>
                    </div>

                    <label class="form-label small fw-bold text-muted text-uppercase mb-2">Manifiesto de Carga y Destinos</label>
                    <div id="listaMaterialesEdit" class="list-group shadow-sm" style="border-radius: 15px; overflow: hidden;">
                        </div>
                </form>
            </div>

            <div class="modal-footer border-0 bg-white" style="border-radius: 0 0 20px 20px;">
                <button type="button" class="btn btn-light fw-bold" data-bs-dismiss="modal" style="border-radius: 12px; padding: 10px 20px;">Descartar</button>
                <button type="button" onclick="guardarCambiosViaje()" class="btn btn-primary fw-bold" style="border-radius: 12px; padding: 10px 25px; background: #007aff;">Actualizar Ruta</button>
            </div>
        </div>
    </div>
</div>
<script>window.abrirModalEdicionViaje = async function(folio, vehiculoId) {
    $('#edit_viaje_folio').val(folio);
    $('#editFolioTitle').text('Ruta ' + folio);
    
    try {
        // A. Primero traemos los detalles del viaje para saber de qué ALMACÉN es
        const respViaje = await fetch(`/cfsistem/app/controllers/repartosController.php?action=get_detalles_viaje&folio=${folio}`);
        const viaje = await respViaje.json();
        
        if (!viaje.success) throw new Error(viaje.message);
        const info = viaje.data;
        const idSucursal = info.almacen_id; // <--- ¡Aquí está la magia!

        // B. Ahora que sabemos el almacén, traemos los choferes de esa sucursal
        const respRecursos = await fetch(`/cfsistem/app/controllers/repartosController.php?action=get_recursos_sucursal&almacen_id=${idSucursal}`);
        const recursos = await respRecursos.json();

        // 1. Llenar Choferes
        let hChofer = '<option value="">Seleccione...</option>';
        recursos.choferes.forEach(c => {
            hChofer += `<option value="${c.id}" ${c.id == info.chofer_id ? 'selected' : ''}>${c.nombre}</option>`;
        });
        $('#edit_chofer_id').html(hChofer);

        // 2. Llenar Ayudantes
        let hTrip = '';
        const idsActuales = (info.tripulantes_ids || []).map(id => id.toString());
        recursos.choferes.forEach(a => {
            const isSel = idsActuales.includes(a.id.toString()) ? 'selected' : '';
            hTrip += `<option value="${a.id}" ${isSel}>${a.nombre}</option>`;
        });
        $('#edit_tripulantes').html(hTrip);

        // 3. Llenar Materiales
        let hMat = '';
        info.materiales.forEach(m => {
            hMat += `
                <div class="list-group-item border-0 border-bottom p-3 bg-white" id="item_mov_${m.movimiento_id}">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="fw-bold small">${m.producto} (${m.cantidad})</span>
                        <button type="button" class="btn btn-sm text-danger" onclick="quitarEntregaDeRuta(${m.movimiento_id})">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                    <input type="text" class="form-control form-control-sm bg-light border-0 destino-input" 
                           data-movid="${m.movimiento_id}" value="${m.destino || 'Entrega en Obra'}">
                </div>`;
        });
        $('#listaMaterialesEdit').html(hMat);

        $('#modalEditarViaje').modal('show');

    } catch (e) {
        Swal.fire('Error', 'No se pudo cargar la logística: ' + e.message, 'error');
    }
}</script>