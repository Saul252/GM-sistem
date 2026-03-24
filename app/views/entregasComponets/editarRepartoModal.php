<div class="modal fade" id="modalEditarViaje" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 22px; background: rgba(245, 245, 247, 0.95); backdrop-filter: blur(20px);">
            
            <div class="modal-header bg-white border-bottom-0 pb-3" style="border-radius: 22px 22px 0 0;">
                <div class="d-flex align-items-center">
                    <div class="icon-box me-3 shadow-sm" style="background: #007aff; padding: 12px; border-radius: 14px;">
                        <i class="bi bi-pencil-square text-white fs-5"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0 text-dark" id="editFolioTitle">Ajustar Logística</h5>
                        <small class="text-primary fw-bold" id="editUnidadSubtitle" style="font-size: 0.75rem; letter-spacing: 0.5px;"></small>
                    </div>
                </div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body p-4">
                <form id="formEditarViaje">
                    <input type="hidden" id="edit_viaje_folio" name="viaje_folio">
                    <input type="hidden" id="edit_vehiculo_id" name="vehiculo_id">

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <div class="form-group p-3 bg-white" style="border-radius: 15px; border: 1px solid #e5e5ea;">
                                <label class="form-label small fw-bold text-muted text-uppercase mb-2" style="font-size: 0.65rem;">Responsable (Chofer)</label>
                                <select id="edit_chofer_id" name="chofer_id" class="form-select border-0 bg-light fw-bold" style="border-radius: 10px; padding: 10px;"></select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group p-3 bg-white" style="border-radius: 15px; border: 1px solid #e5e5ea;">
                                <label class="form-label small fw-bold text-muted text-uppercase mb-2" style="font-size: 0.65rem;">Tripulación (Ayudantes)</label>
                                <select id="edit_tripulantes" name="tripulantes[]" class="form-select border-0 bg-light" multiple style="border-radius: 10px;"></select>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-end mb-2">
                        <label class="form-label small fw-bold text-muted text-uppercase mb-0" style="font-size: 0.65rem; margin-left: 10px;">Manifiesto de Carga y Clientes</label>
                        <span class="badge bg-dark rounded-pill" style="font-size: 0.6rem;">EDITABLE</span>
                    </div>
                    
                    <div id="listaMaterialesEdit" class="list-group shadow-sm border-0" style="border-radius: 18px; overflow: hidden;">
                        </div>
                </form>
            </div>

            <div class="modal-footer border-0 bg-white p-3" style="border-radius: 0 0 22px 22px;">
                <button type="button" class="btn btn-link text-secondary fw-bold text-decoration-none" data-bs-dismiss="modal" style="font-size: 0.9rem;">Cancelar</button>
                <button type="button" onclick="guardarCambiosViaje()" class="btn btn-primary fw-bold shadow-sm" style="border-radius: 12px; padding: 12px 30px; background: #007aff; border: none;">Guardar Cambios</button>
            </div>
        </div>
    </div>
</div>
<script>



window.abrirModalEdicionViaje = async function(folio, vehiculoId) {
    // 1. Limpieza inicial y asignación de Folio
    $('#edit_viaje_folio').val(folio);
    
    // --- ESTA ES LA LÍNEA QUE FALTABA ---
    // Si pasas el vehiculoId por parámetro, lo usamos; si no, lo tomaremos de info.vehiculo_id abajo
    if(vehiculoId) $('#edit_vehiculo_id').val(vehiculoId);
    
    $('#editFolioTitle').text('Ruta ' + folio);
    
    try {
        // A. Traer detalles del viaje
        const respViaje = await fetch(`/cfsistem/app/controllers/repartosController.php?action=get_detalles_viaje&folio=${folio}`);
        const viaje = await respViaje.json();
        
        if (!viaje.success) throw new Error(viaje.message);
        const info = viaje.data;
        
        // RESPALDO: Si no venía por parámetro, lo sacamos de la base de datos
        if(!$('#edit_vehiculo_id').val()) {
            $('#edit_vehiculo_id').val(info.vehiculo_id);
        }

        // Mostrar la unidad en el subtítulo (Estética iOS)
        $('#editUnidadSubtitle').html(`
            <i class="fas fa-truck me-1"></i> ${info.unidad_nombre} 
            <span class="badge bg-light text-dark ms-2" style="font-weight: 500;">${info.unidad_placas}</span>
        `);

        // B. Traer recursos (Choferes/Ayudantes) de la sucursal
        const respRecursos = await fetch(`/cfsistem/app/controllers/repartosController.php?action=get_recursos_sucursal&almacen_id=${info.almacen_id}`);
        const recursos = await respRecursos.json();

        // 1. Llenar Choferes
        let hChofer = '<option value="">Seleccione chofer...</option>';
        recursos.choferes.forEach(c => {
            hChofer += `<option value="${c.id}" ${c.id == info.chofer_id ? 'selected' : ''}>${c.nombre}</option>`;
        });
        $('#edit_chofer_id').html(hChofer);

        // 2. Llenar Ayudantes (Tripulantes)
        let hTrip = '';
        const idsActuales = (info.tripulantes_ids || []).map(id => id.toString());
        recursos.choferes.forEach(a => {
            const isSel = idsActuales.includes(a.id.toString()) ? 'selected' : '';
            hTrip += `<option value="${a.id}" ${isSel}>${a.nombre}</option>`;
        });
        $('#edit_tripulantes').html(hTrip);

        // 3. Llenar Materiales
        let hMat = '';
        if(info.materiales && info.materiales.length > 0) {
            info.materiales.forEach(m => {
                hMat += `
                    <div class="list-group-item border-0 border-bottom p-3 bg-white" id="item_mov_${m.movimiento_id}" style="transition: all 0.2s;">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <span class="badge bg-light text-primary mb-1" style="font-size: 0.65rem;">SKU: ${m.sku || 'N/A'}</span>
                                <div class="fw-bold text-dark" style="font-size: 0.9rem;">${m.producto}</div>
                                <small class="text-muted fw-bold">Cant: ${parseFloat(m.cantidad).toFixed(2)} ${m.um || ''}</small>
                            </div>
                            <div class="text-end">
                                <span class="d-block fw-bold text-primary small">${m.cliente || 'Público General'}</span>
                                <button type="button" class="btn btn-sm btn-light text-danger rounded-circle mt-1" 
                                        onclick="quitarEntregaDeRuta(${m.movimiento_id})" title="Quitar de esta ruta">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                        <div class="input-group mt-2" style="border-radius: 8px; overflow: hidden; border: 1px solid #f0f0f5;">
                            <span class="input-group-text border-0 bg-light"><i class="fas fa-map-marker-alt text-secondary" style="font-size: 0.8rem;"></i></span>
                            <input type="text" class="form-control form-control-sm border-0 bg-light destino-input" 
                                   data-movid="${m.movimiento_id}" value="${m.destino || 'Entrega en Obra'}" 
                                   placeholder="Dirección o punto de entrega" style="font-size: 0.8rem;">
                        </div>
                    </div>`;
            });
        } else {
            hMat = '<div class="p-4 text-center text-muted">No hay productos en esta ruta</div>';
        }
        $('#listaMaterialesEdit').html(hMat);

        $('#modalEditarViaje').modal('show');

    } catch (e) {
        console.error("Error en abrirModal:", e);
        Swal.fire('Atención', 'Ocurrió un error al cargar los datos: ' + e.message, 'warning');
    }
}






async function guardarCambiosViaje() {
    const form = document.getElementById('formEditarViaje');
    const btnGuardar = document.querySelector('#modalEditarViaje .btn-primary');
    
    // 1. Recopilamos las direcciones de los inputs .destino-input
    const destinos = {};
    const inputs = document.querySelectorAll('.destino-input');
    
    if (inputs.length === 0) {
        return Swal.fire('Atención', 'No hay materiales en esta ruta.', 'warning');
    }

    inputs.forEach(input => {
        destinos[input.dataset.movid] = input.value.trim();
    });

    // 2. Preparamos el FormData
    const formData = new FormData(form);
    formData.append('action', 'guardar_cambios_viaje');
    // Enviamos el objeto de direcciones como un string JSON
    formData.append('destinos_data', JSON.stringify(destinos));

    try {
        // Efecto visual de carga (iOS Style)
        btnGuardar.disabled = true;
        btnGuardar.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Guardando...';

        const resp = await fetch(`/cfsistem/app/controllers/repartosController.php`, {
            method: 'POST',
            body: formData
        });

        // Leemos como texto primero para detectar errores de PHP (Warnings/Espacios)
        const text = await resp.text();
        let res;

        try {
            res = JSON.parse(text);
        } catch (e) {
            console.error("Respuesta no válida:", text);
            throw new Error("El servidor devolvió un formato incorrecto. Revisa la consola (F12).");
        }

        if (res.success) {
            Swal.fire({
                icon: 'success',
                title: '¡Logística Actualizada!',
                text: res.message,
                timer: 1500,
                showConfirmButton: false
            });
            
            $('#modalEditarViaje').modal('hide');
            
            // Refrescar tabla o lista
            if (typeof listarViajesActivos === 'function') listarViajesActivos();
            else location.reload();

        } else {
            throw new Error(res.message);
        }

    } catch (err) {
        Swal.fire('Error', err.message, 'error');
    } finally {
        btnGuardar.disabled = false;
        btnGuardar.innerHTML = 'Guardar Cambios';
    }
}
</script>