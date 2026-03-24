<style>
    @media print {
    /* Ocultar todo lo que está fuera del modal */
    body * {
        visibility: hidden;
    }
    
    /* Mostrar solo el modal y sus hijos */
    #modalDetalleViaje, #modalDetalleViaje * {
        visibility: visible;
    }

    /* Posicionar el modal al inicio de la página */
    #modalDetalleViaje {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        margin: 0;
        padding: 0;
        visibility: visible;
    }

    /* Ocultar botones de cerrar e imprimir en la hoja */
    .modal-footer, .btn-close {
        display: none !important;
    }

    /* Ajustes estéticos para papel */
    .modal-content {
        border: none !important;
        box-shadow: none !important;
        background: white !important;
        backdrop-filter: none !important;
    }

    .table {
        width: 100% !important;
        border-collapse: collapse !important;
    }

    /* Asegurar que el texto sea negro y legible */
    .text-dark, .fw-bold {
        color: #000 !important;
    }
}
</style>
<div class="modal fade" id="modalDetalleViaje" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content" style="border-radius: 22px; border: none; overflow: hidden; backdrop-filter: blur(15px); background: rgba(255, 255, 255, 0.85);">
            <div class="modal-header" style="border: none; background: rgba(255, 255, 255, 0.5);">
                <h5 class="modal-title fw-bold text-dark"><i class="fas fa-truck-loading me-2"></i>Hoja de Ruta: <span id="txtFolioViaje"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <div class="p-3" style="background: white; border-radius: 18px; box-shadow: 0 4px 12px rgba(0,0,0,0.05);">
                            <small class="text-muted d-block text-uppercase fw-bold" style="font-size: 0.65rem;">Unidad y Placas</small>
                            <span id="txtUnidad" class="d-block fw-bold text-dark"></span>
                            <span id="txtPlacas" class="badge bg-light text-dark mt-1"></span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3" style="background: white; border-radius: 18px; box-shadow: 0 4px 12px rgba(0,0,0,0.05);">
                            <small class="text-muted d-block text-uppercase fw-bold" style="font-size: 0.65rem;">Equipo de Trabajo</small>
                            <span class="d-block text-dark"><strong>Chofer:</strong> <span id="txtChofer"></span></span>
                            <small id="txtAyudantes" class="text-secondary"></small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3" style="background: white; border-radius: 18px; box-shadow: 0 4px 12px rgba(0,0,0,0.05);">
                            <small class="text-muted d-block text-uppercase fw-bold" style="font-size: 0.65rem;">Estado de Logística</small>
                            <div id="txtEstatusLogistico" class="mt-1"></div>
                        </div>
                    </div>
                </div>

                <div class="table-responsive" style="border-radius: 15px; background: white;">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3" style="font-size: 0.75rem;">ORDEN</th>
                                <th style="font-size: 0.75rem;">CLIENTE / DESTINO</th>
                                <th style="font-size: 0.75rem;">TICKET</th>
                                <th style="font-size: 0.75rem;">PRODUCTO</th>
                                <th class="text-center" style="font-size: 0.75rem;">CANTIDAD</th>
                                <th class="pe-3 text-center" style="font-size: 0.75rem;">MAPA</th>
                            </tr>
                        </thead>
                        <tbody id="bodyDetalleViaje" style="font-size: 0.85rem;">
                            </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer" style="border: none;">
                <button type="button" class="btn btn-light fw-bold" data-bs-dismiss="modal" style="border-radius: 12px;">Cerrar</button>
                <button type="button" class="btn btn-dark fw-bold" onclick="window.print()" style="border-radius: 12px;">Imprimir Ruta</button>
            </div>
        </div>
    </div>
</div>
<script>
function verDetalleViaje(folio) {
    console.log("1. Folio enviado:", folio);
    if (!folio) return;

    fetch('/cfsistem/app/controllers/repartosController.php?action=get_detalle_ruta_completa&id=' + folio)
        .then(res => res.json()) // PASO 1: Convertir a JSON
        .then(response => {      // PASO 2: Trabajar con los datos
            console.log("2. Respuesta JSON completa:", response);

            if (!response.success || !response.data || response.data.length === 0) {
                // Aquí verás el error de SQL si PHP lo manda
                console.error("Error reportado por PHP:", response.message);
                return;
            }

            const registros = response.data;
            const v = registros[0];

            // Llenar cabecera
            document.getElementById('txtFolioViaje').innerText = v.folio_viaje || 'S/N';
            document.getElementById('txtUnidad').innerText = v.unidad_nombre || 'N/A';
            document.getElementById('txtPlacas').innerText = v.unidad_placas || '';
            document.getElementById('txtChofer').innerText = v.nombre_chofer || 'No asignado';
            document.getElementById('txtAyudantes').innerText = v.ayudantes ? `Ayudantes: ${v.ayudantes}` : 'Sin ayudantes';

            // Badge de estatus
            const estatus = (v.estatus_logistico || 'pendiente').toLowerCase();
            const badgeColor = estatus === 'completado' ? 'success' : (estatus === 'en_transito' ? 'primary' : 'warning');
            document.getElementById('txtEstatusLogistico').innerHTML = `<span class="badge rounded-pill bg-${badgeColor}">${estatus.toUpperCase()}</span>`;

            // Llenar tabla
            let html = '';
            registros.forEach(item => {
                const mapsUrl = (item.latitud && item.longitud) 
                    ? `<a href="https://www.google.com/maps?q=${item.latitud},${item.longitud}" target="_blank" class="btn btn-sm btn-outline-primary" style="border-radius: 10px;"><i class="fas fa-map-marker-alt"></i></a>`
                    : `<span class="text-muted small">N/A</span>`;

                html += `
                <tr>
                    <td class="ps-3 text-center"><span class="badge bg-dark rounded-circle">${item.orden_visita}</span></td>
                    <td>
                        <strong class="text-dark">${item.cliente}</strong>
                        <small class="d-block text-secondary" style="font-size: 0.7rem;">${item.direccion_entrega}</small>
                    </td>
                    <td><code class="text-primary">${item.folio_venta}</code></td>
                    <td>${item.producto_nombre} <br> <small class="text-muted">${item.um || ''}</small></td>
                    <td class="text-center fw-bold">${parseFloat(item.cantidad).toFixed(2)}</td>
                    <td class="text-center">${mapsUrl}</td>
                </tr>`;
            });

            document.getElementById('bodyDetalleViaje').innerHTML = html;

            // Abrir modal
            const modalEl = document.getElementById('modalDetalleViaje');
            modalEl.removeAttribute('aria-hidden'); 
            const modalInstance = bootstrap.Modal.getOrCreateInstance(modalEl);
            modalInstance.show();
        })
        .catch(err => {
            console.error("3. Error fatal en fetch o proceso de datos:", err);
        });
}
</script>