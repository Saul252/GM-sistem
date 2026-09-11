<!-- Modal de Evidencias de Entrega -->
<div class="modal fade" id="modalEvidenciasEntrega" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header bg-dark text-white p-3" style="border-radius: 20px 20px 0 0;">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-card-checklist me-2 text-warning"></i> Evidencias de Entrega
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>

            <div class="modal-body p-4 bg-light">
                <!-- Spinner de Carga -->
                <div id="cargandoEvidencias" class="text-center py-5">
                    <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                    <p class="mt-2 text-muted fw-bold">Obteniendo información del reparto...</p>
                </div>

                <!-- Contenedor Principal de Resultados -->
                <div id="contenidoEvidencias" class="d-none"></div>
            </div>

            <div class="modal-footer bg-white border-top-0" style="border-radius: 0 0 20px 20px;">
                <button type="button" class="btn btn-secondary fw-bold px-4" data-bs-dismiss="modal"
                    style="border-radius: 10px;">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<script>
    /**
     * Abre el modal y consulta las evidencias pasando el ID de entrega (folio)
     * @param {string|number} entregaId ID o Folio de entrega (Ej: 847)
     */
    async function abrirModalEvidencias(entregaId) {
        const modalEl = document.getElementById('modalEvidenciasEntrega');
        const modalBs = new bootstrap.Modal(modalEl);
        const divCargando = document.getElementById('cargandoEvidencias');
        const divContenido = document.getElementById('contenidoEvidencias');

        // Mostrar modal y resetear estados
        divCargando.classList.remove('d-none');
        divContenido.classList.add('d-none');
        divContenido.innerHTML = '';
        modalBs.show();

        try {
            const url = `/cfsistem/app/controllers/misRepartosController.php?action=get_evidencias_por_entrega_id&folio=${entregaId}`;
            const res = await fetch(url);


            if (!res.ok) throw new Error(`HTTP Error: ${res.status}`);

            const respuesta = await res.json();
            console.log(respuesta);
            if (respuesta.success && Array.isArray(respuesta.data) && respuesta.data.length > 0) {
                renderizarEvidencias(respuesta.data);
                divCargando.classList.add('d-none');
                divContenido.classList.remove('d-none');
            } else {
                divContenido.innerHTML = `
                <div class="alert alert-warning text-center rounded-4 shadow-sm my-3 p-4">
                    <i class="bi bi-exclamation-triangle fs-1 d-block mb-2"></i>
                    <h6 class="fw-bold">Sin evidencias registradas</h6>
                    <p class="mb-0 small">No se encontraron registros de entregas ni evidencias asociadas a este folio (${entregaId}).</p>
                </div>`;
                divCargando.classList.add('d-none');
                divContenido.classList.remove('d-none');
            }

        } catch (error) {
            console.error('Error al cargar evidencias:', error);
            divContenido.innerHTML = `
            <div class="alert alert-danger text-center rounded-4 shadow-sm my-3 p-4">
                <i class="bi bi-x-circle fs-1 d-block mb-2"></i>
                <h6 class="fw-bold">Error de Conexión</h6>
                <p class="mb-0 small">No se pudo consultar la información. Intente nuevamente.</p>
            </div>`;
            divCargando.classList.add('d-none');
            divContenido.classList.remove('d-none');
        }
    }

    /**
     * Renderiza dinámicamente las tarjetas de venta y evidencias
     * @param {Array} listaVentas 
     */
    function renderizarEvidencias(listaVentas) {
        const contenedor = document.getElementById('contenidoEvidencias');
        let html = '';

        listaVentas.forEach((v, index) => {
            const badgeEntregado = v.ya_entregado == 1
                ? '<span class="badge bg-success-subtle text-success border border-success px-3 py-2 rounded-pill"><i class="bi bi-check-circle-fill me-1"></i> Entregado</span>'
                : '<span class="badge bg-warning-subtle text-warning border border-warning px-3 py-2 rounded-pill"><i class="bi bi-clock-fill me-1"></i> Pendiente</span>';

            const clienteNombre = v.cliente ? v.cliente : 'VENTA MOSTRADOR / GENERAL';

            // Imagen Foto Entrega
            const imgFotoHtml = v.foto_registrada
                ? `<div class="col-md-6 text-center mb-3">
                    <label class="form-label micro-text fw-bold text-secondary d-block">FOTO DE ENTREGA</label>
                    <a href="${v.foto_registrada}" target="_blank">
                        <img src="${v.foto_registrada}" class="img-fluid rounded-3 border shadow-sm style-img-evidencia" style="max-height: 220px; object-fit: cover; width: 100%;">
                    </a>
               </div>`
                : `<div class="col-md-6 text-center mb-3">
                    <label class="form-label micro-text fw-bold text-secondary d-block">FOTO DE ENTREGA</label>
                    <div class="p-4 bg-body-tertiary rounded-3 border text-muted small">Sin fotografía de entrega</div>
               </div>`;

            // Imagen Foto Nota
            const imgNotaHtml = v.nota_registrada
                ? `<div class="col-md-6 text-center mb-3">
                    <label class="form-label micro-text fw-bold text-secondary d-block">NOTA / COMPROBANTE</label>
                    <a href="${v.nota_registrada}" target="_blank">
                        <img src="${v.nota_registrada}" class="img-fluid rounded-3 border shadow-sm style-img-evidencia" style="max-height: 220px; object-fit: cover; width: 100%;">
                    </a>
               </div>`
                : `<div class="col-md-6 text-center mb-3">
                    <label class="form-label micro-text fw-bold text-secondary d-block">NOTA / COMPROBANTE</label>
                    <div class="p-4 bg-body-tertiary rounded-3 border text-muted small">Sin foto de nota registrada</div>
               </div>`;

            html += `
        <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px; overflow: hidden;">
            <div class="card-header bg-white border-bottom p-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <h6 class="fw-bold mb-0 text-primary">
                        <i class="bi bi-receipt me-1"></i> Venta: ${v.folio_venta} 
                        <span class="text-muted small ms-2">| Viaje: ${v.folio_viaje}</span>
                    </h6>
                    <small class="text-secondary fw-semibold">
                        <i class="bi bi-geo-alt-fill text-danger me-1"></i> ${v.direccion_entrega}
                    </small>
                </div>
                <div>${badgeEntregado}</div>
            </div>
            
            <div class="card-body p-4">
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <div class="p-3 bg-white border rounded-3 h-100">
                            <span class="micro-text fw-bold text-muted d-block text-uppercase">Cliente</span>
                            <span class="fw-bold text-dark">${clienteNombre}</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 bg-white border rounded-3 h-100">
                            <span class="micro-text fw-bold text-muted d-block text-uppercase">Comentario de Evidencia</span>
                            <span class="fw-semibold text-secondary">${v.comentario_evidencia ? v.comentario_evidencia : 'Sin observaciones'}</span>
                        </div>
                    </div>
                </div>

                <div class="p-3 bg-white border rounded-3 mb-3">
                    <span class="micro-text fw-bold text-muted d-block text-uppercase mb-2">Resumen de Productos</span>
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="fw-bold text-dark small">${v.productos}</span>
                        <span class="badge bg-primary rounded-pill px-3">${v.total_piezas_venta} Pzas Totales</span>
                    </div>
                    <small class="text-muted d-block">Detalle: ${v.cantidades_detalladas}</small>
                </div>

                <div class="row pt-2">
                    ${imgFotoHtml}
                    ${imgNotaHtml}
                </div>
            </div>
        </div>`;
        });

        contenedor.innerHTML = html;
    }
</script>

<style>
    .micro-text {
        font-size: 0.725rem;
        letter-spacing: 0.5px;
    }

    .style-img-evidencia {
        transition: transform 0.2s ease-in-out;
    }

    .style-img-evidencia:hover {
        transform: scale(1.02);
    }
</style>