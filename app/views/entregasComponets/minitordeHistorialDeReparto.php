<style>
    /* Estilo para el Historial (Variante del Monitor) */
    .card-history {
        background: #ffffff;
        border-radius: 22px;
        border: 1px solid #e5e5ea;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.02);
    }

    .header-history {
        background: #fbfbfd;
        padding: 1.5rem;
        border-bottom: 1px solid #e5e5ea;
        border-radius: 22px 22px 0 0;
    }

    /* Badge de Estado Finalizado (iOS Style) */
    .badge-status-finalizado {
        background: #eaf9ee;
        color: #248a3d;
        font-size: 0.65rem;
        font-weight: 700;
        padding: 5px 12px;
        border-radius: 20px;
        text-transform: uppercase;
    }

    /* Badge de Estado Cancelado */
    .badge-status-cancelado {
        background: #fff1f0;
        color: #cf1322;
        font-size: 0.65rem;
        font-weight: 700;
        padding: 5px 12px;
        border-radius: 20px;
        text-transform: uppercase;
    }

    .fecha-finalizado {
        font-size: 0.72rem;
        color: #86868b;
        display: block;
        margin-top: 4px;
    }

    .destino-historico {
        font-size: 0.8rem;
        color: #424245;
        margin-bottom: 4px;
    }
</style>

<div class="main-content">
    <div class="card card-history animate__animated animate__fadeIn">
        <div class="header-history d-flex justify-content-between align-items-center">
            <div>
                <h5 class="mb-0 fw-bold text-dark">Historial de Logística</h5>
                <p class="text-muted small mb-0">Registro de rutas completadas y canceladas</p>
            </div>
            <div class="d-flex gap-2">
                <select id="filtroAlmacenHistorial" class="form-select form-select-sm rounded-pill border-light shadow-sm" style="width: 200px;" onchange="cargarHistorialRepartos()">
                    <option value="0">Todos los Almacenes</option>
                    </select>
                <button class="btn btn-sm btn-light rounded-pill px-3 border" onclick="cargarHistorialRepartos()">
                    <i class="bi bi-arrow-clockwise"></i>
                </button>
            </div>
        </div>
        
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4" style="font-size: 0.7rem; color: #86868b;">VIAJE / FECHA</th>
                            <th style="font-size: 0.7rem; color: #86868b;">UNIDAD Y PERSONAL</th>
                            <th style="font-size: 0.7rem; color: #86868b;">RUTA REALIZADA</th>
                            <th style="font-size: 0.7rem; color: #86868b;">ESTADO</th>
                            <th class="text-end pe-4" style="font-size: 0.7rem; color: #86868b;">DETALLE</th>
                        </tr>
                    </thead>
                    <tbody id="bodyHistorialRepartos">
                        </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
window.cargarHistorialRepartos = async function() {
    const body = $('#bodyHistorialRepartos');
    const almacenId = $('#filtroAlmacenHistorial').val() || 0;

    try {
        body.html('<tr><td colspan="5" class="text-center py-5"><div class="spinner-border text-muted spinner-border-sm"></div></td></tr>');
        
        // Llamada al motor: listar_historial
        const resp = await fetch(`/cfsistem/app/controllers/repartosController.php?action=listar_historial&almacen_id=${almacenId}`);
        const result = await resp.json();
        
        if (!result.success || result.data.length === 0) {
            body.html('<tr><td colspan="5" class="text-center py-5 text-muted">No hay registros en el historial</td></tr>');
            return;
        }

        body.empty();
        result.data.forEach(v => {
            const statusClass = v.estado_final === 'finalizado' ? 'badge-status-finalizado' : 'badge-status-cancelado';
            const statusText = v.estado_final === 'finalizado' ? 'Completado' : 'Cancelado';

            body.append(`
                <tr>
                    <td class="ps-4">
                        <div class="badge-folio">#${v.viaje_folio}</div>
                        <span class="fecha-finalizado"><i class="bi bi-calendar3 me-1"></i>${v.fecha_finalizado}</span>
                    </td>
                    <td>
                        <div class="fw-bold" style="font-size:0.85rem;">${v.unidad}</div>
                        <div class="small text-muted text-uppercase" style="font-size:0.7rem;">
                            <i class="bi bi-person-fill"></i> ${v.chofer}
                            ${v.tripulantes ? ` | <i class="bi bi-people"></i> ${v.tripulantes}` : ''}
                        </div>
                    </td>
                    <td>
                        <div style="max-height: 60px; overflow-y: auto;">
                            ${v.ruta_destinos}
                        </div>
                    </td>
                    <td>
                        <span class="${statusClass}">${statusText}</span>
                    </td>
                    <td class="text-end pe-4">
                        <button class="btn btn-sm btn-light border" onclick="verResumenCarga('${v.viaje_folio}')" title="Ver Carga">
                            <i class="bi bi-box-seam"></i>
                        </button>
                    </td>
                </tr>
            `);
        });
    } catch (e) {
        body.html('<tr><td colspan="5" class="text-center py-4 text-danger">Error al cargar historial</td></tr>');
    }
};

// Función para mostrar un modal rápido con lo que se entregó
window.verResumenCarga = function(folio) {
    // Aquí puedes disparar un SweetAlert o un Modal que muestre v.detalles_carga
    // Por ahora lo dejamos listo para implementar
    Swal.fire({
        title: 'Carga de la Ruta ' + folio,
        html: `<div class="text-start p-2" style="font-size:0.9rem;">Cargando detalles...</div>`,
        showConfirmButton: false,
        timer: 1500
    });
};

$(document).ready(() => {
    cargarHistorialRepartos();
});
</script>