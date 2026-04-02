<div class="modal fade animate__animated animate__fadeIn" id="modalEvidenciasRuta" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; background: #f5f5f7;">
            <div class="modal-header border-0 bg-white p-4" style="border-radius: 20px 20px 0 0;">
                <div>
                    <h5 class="modal-title fw-bold text-dark mb-0">Evidencias del Reparto</h5>
                    <small id="txtFolioRuta" class="text-muted fw-bold"></small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body p-4" id="contenedorEvidenciasRuta">
                </div>

            <div class="modal-footer border-0 bg-white justify-content-center">
                <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<style>
    .entrega-item-card {
        background: #ffffff;
        border-radius: 18px;
        padding: 18px;
        margin-bottom: 20px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.04);
        border: 1px solid rgba(0,0,0,0.02);
    }
    .img-evidencia-thumb {
        width: 100%;
        height: 120px;
        object-fit: cover;
        border-radius: 12px;
        cursor: pointer;
        transition: transform 0.2s ease;
        border: 1px solid #eee;
    }
    .img-evidencia-thumb:hover {
        transform: scale(1.03);
    }
    .label-foto {
        font-size: 0.65rem;
        font-weight: 800;
        text-transform: uppercase;
        color: #8e8e93;
        display: block;
        margin-bottom: 4px;
        text-align: center;
    }
    .badge-estado-entrega {
        font-size: 0.6rem;
        padding: 4px 10px;
        border-radius: 50px;
    }
</style>
<script>
    /**
 * Función principal para ver evidencias de un viaje específico
 * @param {string} viajeFolio - El folio de la ruta (Ej: RUT-2026-0203)
 */
function verEvidenciasPorFolio(viajeFolio) {
    // 1. Preparar el modal y la interfaz
    $('#txtFolioRuta').text(viajeFolio);
    const contenedor = $('#contenedorEvidenciasRuta');
    
    // Spinner de carga con estilo limpio
    contenedor.html(`
        <div class="text-center py-5">
            <div class="spinner-border text-primary mb-3" role="status"></div>
            <p class="text-muted small">Obteniendo reportes de entrega...</p>
        </div>
    `);

    // Mostrar el modal
    const modalInstance = new bootstrap.Modal(document.getElementById('modalEvidenciasRuta'));
    modalInstance.show();

    // 2. Petición AJAX al controlador
    $.ajax({
        url: '/cfsistem/app/controllers/misRepartosController.php',
        type: 'GET',
        data: { 
            action: 'get_evidencias_por_folio', 
            folio: viajeFolio 
        },
        dataType: 'json',
        success: function(response) {
            if (response.success && response.data.length > 0) {
                let html = '';

                response.data.forEach(entrega => {
                    html += `
                    <div class="entrega-item-card animate__animated animate__fadeInUp">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <h6 class="fw-bold mb-0" style="color: #1d1d1f;">${entrega.cliente}</h6>
                                <p class="text-muted mb-0" style="font-size: 0.75rem;">
                                    <i class="bi bi-geo-alt-fill text-danger"></i> ${entrega.direccion}
                                </p>
                            </div>
                            <span class="badge-estado-entrega bg-success-subtle text-success border border-success-subtle">
                                ${entrega.estado}
                            </span>
                        </div>

                        <div class="my-3 p-2 rounded-3 bg-light" style="border-left: 3px solid #007aff;">
                            <p class="mb-0 text-dark italic" style="font-size: 0.8rem;">
                                <i class="bi bi-chat-left-text me-1 text-muted"></i> "${entrega.comentario}"
                            </p>
                        </div>

                        <div class="row g-2">
                            ${entrega.foto_1 ? `
                                <div class="col-6">
                                    <span class="label-foto">Material Entregado</span>
                                    <img src="${entrega.foto_1}" class="img-evidencia-thumb shadow-sm" onclick="window.open(this.src, '_blank')">
                                </div>` : ''}
                            
                            ${entrega.foto_2 ? `
                                <div class="col-6">
                                    <span class="label-foto">Nota de Remisión</span>
                                    <img src="${entrega.foto_2}" class="img-evidencia-thumb shadow-sm" onclick="window.open(this.src, '_blank')">
                                </div>` : ''}
                        </div>

                        <div class="mt-3 pt-2 border-top d-flex justify-content-between align-items-center">
                            <small class="text-muted" style="font-size: 0.65rem;">
                                <i class="bi bi-calendar3 me-1"></i> ${entrega.fecha}
                            </small>
                            <small class="fw-bold text-primary" style="font-size: 0.7rem;">
                                Venta: #${entrega.venta_folio || 'S/F'}
                            </small>
                        </div>
                    </div>`;
                });
                contenedor.html(html);
            } else {
                contenedor.html(`
                    <div class="text-center py-5">
                        <i class="bi bi-camera-video-off display-4 text-muted mb-3"></i>
                        <p class="text-muted">No se encontraron evidencias físicas cargadas para esta ruta aún.</p>
                    </div>
                `);
            }
        },
        error: function() {
            contenedor.html(`
                <div class="alert alert-danger rounded-4 p-4 text-center">
                    <i class="bi bi-exclamation-triangle-fill mb-2 d-block"></i>
                    Error de conexión con el servidor.
                </div>
            `);
        }
    });
}
</script>