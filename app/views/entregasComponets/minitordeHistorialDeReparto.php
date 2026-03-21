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

    .badge-folio {
        font-weight: 700;
        color: #1d1d1f;
        font-size: 0.9rem;
    }

    .fecha-finalizado {
        font-size: 0.72rem;
        color: #86868b;
        display: block;
        margin-top: 4px;
    }

    .destino-historico {
        font-size: 0.75rem;
        color: #424245;
        max-height: 60px;
        overflow-y: auto;
        line-height: 1.4;
    }
</style>

<div class="main-content">
    <div class="card card-history animate__animated animate__fadeIn">
        <div class="header-history d-flex justify-content-between align-items-center">
            <div>
                <h5 class="mb-0 fw-bold text-dark">Historial de Logística</h5>
                <p class="text-muted small mb-0">Rutas agrupadas por viaje</p>
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
                            <th class="ps-4" style="font-size: 0.7rem; color: #86868b;">VIAJE</th>
                            <th style="font-size: 0.7rem; color: #86868b;">UNIDAD Y PERSONAL</th>
                            <th style="font-size: 0.7rem; color: #86868b;">RUTA REALIZADA</th>
                            <th style="font-size: 0.7rem; color: #86868b;">ESTADO</th>
                            <th class="text-end pe-4" style="font-size: 0.7rem; color: #86868b;">ACCIONES</th>
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
// Variable global temporal para evitar errores de cuotas/comillas en el HTML
let datosHistorialTemp = {};

window.cargarHistorialRepartos = async function() {
    const body = $('#bodyHistorialRepartos');
    const almacenId = $('#filtroAlmacenHistorial').val() || 0;

    try {
        body.html('<tr><td colspan="5" class="text-center py-5"><div class="spinner-border text-muted spinner-border-sm"></div></td></tr>');
        
        const resp = await fetch(`/cfsistem/app/controllers/repartosController.php?action=listar_historial&almacen_id=${almacenId}`);
        const result = await resp.json();
        
        if (!result.success || !result.data || result.data.length === 0) {
            body.html('<tr><td colspan="5" class="text-center py-5 text-muted">No hay registros</td></tr>');
            return;
        }

        body.empty();
        datosHistorialTemp = {}; // Limpiamos temporales

        result.data.forEach((v, index) => {
            const est = (v.estado_final || '').toLowerCase();
            const esOk = ['finalizado', 'terminado', 'entregado'].includes(est);
            
            const statusClass = esOk ? 'badge-status-finalizado' : 'badge-status-cancelado';
            const statusText = esOk ? 'Finalizado' : 'Cancelado';

            // Guardamos los datos en el objeto temporal usando el índice como llave
            datosHistorialTemp[index] = {
                folio: v.viaje_folio,
                carga: v.detalles_carga,
                ruta: v.ruta_destinos
            };

            body.append(`
                <tr class="animate__animated animate__fadeIn">
                    <td class="ps-4">
                        <div class="badge-folio">#${v.viaje_folio}</div>
                        <span class="fecha-finalizado"><i class="bi bi-clock-history me-1"></i>Cerrado</span>
                    </td>
                    <td>
                        <div class="fw-bold" style="font-size:0.85rem; color:#1d1d1f;">${v.unidad}</div>
                        <div class="small text-muted text-uppercase" style="font-size:0.7rem;">
                            <i class="bi bi-person-fill"></i> ${v.chofer || 'N/A'}
                        </div>
                    </td>
                    <td>
                        <div class="destino-historico">
                            ${v.ruta_destinos || '📍 Entrega'}
                        </div>
                    </td>
                    <td>
                        <span class="${statusClass}">${statusText}</span>
                    </td>
                    <td class="text-end pe-4">
                        <button class="btn btn-sm btn-light border rounded-pill px-3 shadow-sm" 
                                onclick="verDetalleSeguro(${index})">
                            <i class="bi bi-info-circle me-1"></i> Detalles
                        </button>
                    </td>
                </tr>
            `);
        });
    } catch (e) {
        console.error("Error:", e);
        body.html('<tr><td colspan="5" class="text-center py-4 text-danger">Error de carga</td></tr>');
    }
};

// Función de modal que lee del objeto temporal (Inmune a errores de comillas)
window.verDetalleSeguro = function(index) {
    const data = datosHistorialTemp[index];
    if (!data) return;

    // Convertimos el string de destinos en una lista visual con iconos
    const paradasHTML = data.ruta.split('<br>').map(punto => `
        <div class="d-flex align-items-start mb-2 p-2" style="background: #fff; border-radius: 12px; border: 1px solid #efeff4;">
            <i class="bi bi-geo-alt-fill text-danger me-2 mt-1" style="font-size: 0.9rem;"></i>
            <div style="font-size: 0.85rem; color: #1d1d1f; font-weight: 500;">${punto}</div>
        </div>
    `).join('');

    Swal.fire({
        title: `<div style="font-weight:700; color: #1d1d1f; font-size: 1.2rem; margin-top:10px;">Detalle del Viaje #${data.folio}</div>`,
        html: `
            <div class="text-start mt-3" style="font-family: -apple-system, BlinkMacSystemFont, sans-serif;">
                
                <div class="mb-4">
                    <label class="text-muted mb-2 d-block" style="font-size: 0.65rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; padding-left: 5px;">
                        Itinerario de Entregas
                    </label>
                    <div style="background: #f5f5f7; padding: 10px; border-radius: 18px;">
                        ${paradasHTML}
                    </div>
                </div>

                <div>
                    <label class="text-muted mb-2 d-block" style="font-size: 0.65rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; padding-left: 5px;">
                        Resumen de Mercancía
                    </label>
                    <div class="p-3" style="background: #f5f5f7; border-radius: 18px; font-size: 0.85rem; color: #424245; max-height: 200px; overflow-y: auto; line-height: 1.6;">
                        ${data.carga}
                    </div>
                </div>

            </div>
        `,
        confirmButtonText: 'Cerrar',
        confirmButtonColor: '#007aff',
        buttonsStyling: true,
        showCloseButton: true,
        customClass: {
            popup: 'rounded-4 border-0 shadow-lg',
            confirmButton: 'rounded-pill px-5 fw-bold'
        },
        showClass: {
            popup: 'animate__animated animate__fadeInUp animate__faster'
        }
    });
};
$(document).ready(() => { cargarHistorialRepartos(); });
</script>
