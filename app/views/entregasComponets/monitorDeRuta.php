<style>
    /* Estilos específicos para el Monitor de Viajes (Línea iOS) */
    .card-monitor {
        background: rgba(255, 255, 255, 0.92);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.7);
        border-radius: 22px;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.04);
        overflow: hidden;
    }

    .header-monitor {
        background: #1d1d1f; 
        color: white;
        padding: 1.2rem 1.5rem;
        border: none;
    }

    /* Estilo para el SELECT de Almacenes en el Header */
    #filtroAlmacenMonitor {
        background-color: rgba(255, 255, 255, 0.15);
        color: white;
        border: 1px solid rgba(255, 255, 255, 0.2);
        transition: all 0.3s ease;
        cursor: pointer;
    }
    #filtroAlmacenMonitor:hover {
        background-color: rgba(255, 255, 255, 0.25);
    }
    #filtroAlmacenMonitor option {
        color: #333; /* Texto oscuro para que se vea al desplegar */
        background-color: white;
    }

    .table-monitor thead th {
        font-size: 0.72rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #86868b;
        font-weight: 600;
        padding: 1.2rem;
        border-bottom: 2px solid #f2f2f7;
    }

    .table-monitor tbody tr {
        transition: all 0.2s ease;
    }

    .table-monitor tbody tr:hover {
        background-color: rgba(0, 122, 255, 0.02);
    }

    .badge-folio {
        background: #e8f4ff;
        color: #007aff;
        font-family: 'SF Mono', SFMono-Regular, ui-monospace, monospace;
        font-weight: 600;
        padding: 4px 10px;
        border-radius: 8px;
        font-size: 0.7rem;
    }

    .carga-scroll {
        background: #f5f5f7;
        border-radius: 12px;
        padding: 12px;
        font-size: 0.85rem;
        color: #424245;
        max-height: 100px;
        overflow-y: auto;
        border: 1px solid rgba(0,0,0,0.03);
    }

    .avatar-chofer {
        width: 36px;
        height: 36px;
        background: #007aff;
        color: white;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        box-shadow: 0 4px 12px rgba(0, 122, 255, 0.2);
    }

    .btn-finish {
        background: #34c759;
        color: white;
        border: none;
        border-radius: 10px;
        padding: 8px 16px;
        font-weight: 600;
        transition: all 0.2s;
    }

    .btn-finish:hover {
        background: #28a745;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(52, 199, 89, 0.2);
    }
</style>

<div class="main-content">
    <div class="card card-monitor animate__animated animate__fadeIn">
        <div class="header-monitor d-flex justify-content-between align-items-center">
   

            <button class="btn btn-sm btn-outline-light rounded-pill px-3 border-opacity-25" onclick="cargarMonitorViajes()">
                <i class="bi bi-arrow-repeat me-1"></i> Actualizar
            </button>
        </div>
        
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-monitor align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4">Unidad / Folio Ruta</th>
                            <th>Chofer Responsable</th>
                            <th>Tripulación</th>
                            <th>Carga Consolidada</th>
                            <th class="text-end pe-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="bodyMonitorViajes">
                        </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
window.cargarMonitorViajes = async function() {
    const body = $('#bodyMonitorViajes');
    
    // Obtenemos el almacén si el select existe (Admin), si no mandamos vacío
    const selectAlm = document.getElementById('filtroAlmacenMonitor');
    const almacenId = selectAlm ? selectAlm.value : '';

    try {
        body.html('<tr><td colspan="5" class="text-center py-5"><div class="spinner-border text-primary spinner-border-sm"></div><div class="mt-2 text-muted small">Consultando rutas...</div></td></tr>');
        
        const resp = await fetch(`/cfsistem/app/controllers/repartosController.php?action=listar_viajes_activos&almacen_id=${almacenId}`);
        const result = await resp.json();
        
        const data = result.data || result; 

        if (result.success === false) {
             body.html(`<tr><td colspan="5" class="text-center py-5 text-danger">${result.message}</td></tr>`);
             return;
        }

        if (!data || data.length === 0) {
            body.html('<tr><td colspan="5" class="text-center py-5 text-muted"><i class="bi bi-geo-alt fs-2 d-block mb-2 opacity-25"></i> No hay unidades en ruta actualmente</td></tr>');
            return;
        }

        body.empty();
        data.forEach(v => {
            const listaAyudantes = v.tripulantes 
                ? `<div class="small text-muted fw-medium"><i class="bi bi-people-fill me-1 text-primary"></i> ${v.tripulantes}</div>`
                : `<span class="badge bg-light text-secondary fw-normal border" style="font-size:0.65rem;">Solo Chofer</span>`;

            body.append(`
                <tr class="animate__animated animate__fadeIn">
                    <td class="ps-4">
                        <div class="fw-bold text-dark" style="font-size:0.95rem;">${v.unidad}</div>
                        <div class="badge-folio mt-1"><i class="bi bi-hash"></i>${v.viaje_folio}</div>
                        <div class="small text-muted mt-1" style="font-size:0.7rem;">📍 ${v.almacen_nombre || 'N/A'}</div>
                    </td>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="avatar-chofer me-3">
                                <i class="bi bi-person-badge"></i>
                            </div>
                            <div>
                                <div class="fw-bold text-uppercase" style="font-size: 0.8rem; color:#1d1d1f;">${v.chofer}</div>
                                <small class="text-muted">Conductor</small>
                            </div>
                        </div>
                    </td>
                    <td>${listaAyudantes}</td>
                    <td>
                        <div class="carga-scroll">
                            ${v.detalles_carga}
                        </div>
                    </td>
                    <td class="text-end pe-4">
                        <button class="btn btn-finish btn-sm" 
                                onclick="finalizarViaje(${v.vehiculo_id}, '${v.viaje_folio}')">
                            <i class="bi bi-check-all me-1"></i> FINALIZAR
                        </button>
                    </td>
                </tr>
            `);
        });
    } catch (e) { 
        console.error("Error al cargar monitor:", e); 
        body.html('<tr><td colspan="5" class="text-center py-4 text-danger">Error de comunicación con el controlador</td></tr>');
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
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: '¡Viaje Finalizado!',
                    text: res.message,
                    icon: 'success',
                    confirmButtonColor: '#007aff'
                });
            } else {
                alert(res.message);
            }
            cargarMonitorViajes(); 
        } else {
            throw new Error(res.message || "Error desconocido");
        }
    } catch (e) {
        alert('Error: ' + e.message);
    }
};
</script>