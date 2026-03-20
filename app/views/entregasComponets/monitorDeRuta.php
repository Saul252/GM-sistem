<div class="card shadow-sm border-0">
    <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center py-3">
        <h5 class="mb-0 small text-uppercase fw-bold"><i class="fas fa-truck-moving me-2"></i> Monitor de Logística Consolidada</h5>
        <button class="btn btn-sm btn-outline-light rounded-pill px-3" onclick="cargarMonitorViajes()">
            <i class="fas fa-sync-alt"></i> Actualizar
        </button>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr class="small text-muted text-uppercase">
                        <th class="ps-4">Unidad / Folio Ruta</th>
                        <th>Chofer (Responsable)</th>
                        <th>Ayudantes</th>
                        <th>Carga Detallada (Consolidado)</th>
                        <th class="text-end pe-4">Acciones</th>
                    </tr>
                </thead>
                <tbody id="bodyMonitorViajes"></tbody>
            </table>
        </div>
    </div>
</div>

<script>
window.cargarMonitorViajes = async function() {
    const body = $('#bodyMonitorViajes');
    try {
        const resp = await fetch(`/cfsistem/app/controllers/repartosController.php?action=listar_viajes_activos`);
        const result = await resp.json();
        const data = result.data || result; 

        if (!data || data.length === 0) {
            body.html('<tr><td colspan="5" class="text-center py-5 text-muted">No hay movimientos en ruta actualmente</td></tr>');
            return;
        }

        body.empty();
        data.forEach(v => {
            const listaAyudantes = v.tripulantes 
                ? `<div class="small text-secondary"><i class="fas fa-users me-1 text-info"></i>${v.tripulantes}</div>`
                : `<span class="badge bg-light text-warning fw-normal border">Solo Chofer</span>`;

            body.append(`
                <tr class="border-bottom">
                    <td class="ps-4">
                        <div class="fw-bold text-dark">${v.unidad}</div>
                        <div class="badge bg-info text-dark border-0 small font-monospace" style="font-size: 0.7rem;">
                            <i class="fas fa-hashtag me-1"></i>${v.viaje_folio}
                        </div>
                    </td>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2 shadow-sm" style="width: 35px; height: 35px;">
                                <i class="fas fa-user-tie"></i>
                            </div>
                            <div class="fw-bold text-uppercase text-primary" style="font-size: 0.85rem;">${v.chofer}</div>
                        </div>
                    </td>
                    <td>${listaAyudantes}</td>
                    <td>
                        <div class="small p-2 bg-light rounded border-start border-3 border-primary" style="max-height: 120px; overflow-y: auto; line-height: 1.5;">
                            ${v.detalles_carga}
                        </div>
                    </td>
                    <td class="text-end pe-4">
                        <button class="btn btn-success btn-sm rounded-pill px-3 shadow-sm" 
                                onclick="finalizarViaje(${v.vehiculo_id}, '${v.viaje_folio}')">
                            <i class="fas fa-flag-checkered me-1"></i> Finalizar
                        </button>
                    </td>
                </tr>
            `);
        });
    } catch (e) { 
        console.error("Error al cargar monitor:", e); 
        body.html('<tr><td colspan="5" class="text-center py-4 text-danger">Error de conexión con el servidor</td></tr>');
    }
};

$(document).ready(() => {
    setTimeout(cargarMonitorViajes, 300);
});

window.finalizarViaje = async function(vehiculoId, folioRuta) {
    if (!confirm(`¿Confirmas la llegada de la unidad y entrega de todos los materiales?\nFolio: ${folioRuta}`)) return;

    try {
        const formData = new FormData();
        formData.append('vehiculo_id', vehiculoId);
        formData.append('viaje_folio', folioRuta);

        const resp = await fetch(`/cfsistem/app/controllers/repartosController.php?action=finalizar_viaje`, {
            method: 'POST',
            body: formData
        });

        const res = await resp.json();
        
        if (res.success) {
            // Si usas SweetAlert2 (opcional, si no alert normal)
            if (typeof Swal !== 'undefined') {
                Swal.fire('¡Viaje Finalizado!', res.message, 'success');
            } else {
                alert(res.message);
            }
            cargarMonitorViajes(); 
        } else {
            throw new Error(res.message || "Error desconocido en el servidor");
        }
    } catch (e) {
        console.error("Error al finalizar:", e);
        alert('No se pudo finalizar el viaje: ' + e.message);
    }
};
</script>