<div class="modal fade" id="modalVerDetalle" tabindex="-1" aria-hidden="true" data-bs-focus="false">
    <div class="modal-dialog modal-xl"> <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
            <div class="modal-body p-0" id="ticketContenido">
                </div>
            <div class="modal-footer border-0 bg-light justify-content-center">
                <button type="button" class="btn btn-dark btn-sm px-4 rounded-pill" onclick="window.print()">
                    <i class="bi bi-printer me-2"></i>Imprimir Ticket
                </button>
                <button type="button" class="btn btn-outline-secondary btn-sm px-4 rounded-pill" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<style>

.ticket-header { background: #f8f9fa; border-bottom: 2px dashed #dee2e6; padding: 2rem 1.5rem; text-align: center; }
.ticket-body { padding: 1.5rem; font-family: 'Courier New', Courier, monospace; }
.ticket-row { display: flex; justify-content: space-between; margin-bottom: 0.5rem; font-size: 0.9rem; }
.ticket-divider { border-top: 1px dashed #ccc; my: 3; }
.badge-ticket { font-size: 0.7rem; padding: 3px 8px; border-radius: 4px; }

/* Estilo para impresión: Oculta todo excepto el contenido del ticket */
@media print {
    /* 1. Ocultamos TODO lo que esté en el body */
    body * {
        visibility: hidden;
        -webkit-print-color-adjust: exact !important; /* Mantiene colores de fondo */
        print-color-adjust: exact !important;
    }

    /* 2. Solo el contenido del ticket y sus hijos serán visibles */
    #modalVerDetalle, 
    #modalVerDetalle .modal-content,
    #ticketContenido, 
    #ticketContenido * {
        visibility: visible !important;
    }

    /* 3. Posicionamos el modal al inicio de la página de impresión */
    #modalVerDetalle {
        position: absolute !important;
        left: 0 !important;
        top: 0 !important;
        margin: 0 !important;
        padding: 0 !important;
        width: 100% !important;
        display: block !important;
        overflow: visible !important;
    }

    /* 4. Quitamos sombras, bordes de modal y botones que no queremos en papel */
    .modal-dialog {
        max-width: 100% !important;
        margin: 0 !important;
    }
    .modal-content {
        border: none !important;
        box-shadow: none !important;
    }
    .modal-footer, .btn-close, .btn, .modal-backdrop {
        display: none !important;
    }

    /* 5. Forzamos que el body no tenga scroll ni fondos grises */
    body {
        background-color: white !important;
    }

}
</style>

<script>
    function verDetalle(tipo, id) {
    $.get(`/cfsistem/app/controllers/egresosController.php?action=obtenerDetalleMovimiento&tipo=${tipo}&id=${id}`, function(data) {
        if (!data.success) return Swal.fire('Error', data.message, 'error');


        const c = data.cabecera;
        const esCompra = (tipo === 'compra');
        let filasHtml = '';

        data.items.forEach(item => {
            let nombre = esCompra ? item.producto_nombre : item.descripcion;
            let conversionInfo = '';
            let detalleMovimientos = '';
            
            if (esCompra) {
                const totalCant = parseFloat(item.cantidad_pedida || item.cantidad || 0);
                const factor = parseFloat(item.factor_prod || 1);
                
                if (factor > 1) {
                    const uniReporte = (totalCant / factor).toFixed(2);
                    conversionInfo = `<div class="text-primary fw-bold" style="font-size: 0.75rem;">
                        Equivale a: ${uniReporte} ${item.unidad_reporte} (1 ${item.unidad_reporte} = ${factor} ${item.unidad_medida})
                    </div>`;
                }

                if (item.desglose_movimientos) {
                    const movimientos = item.desglose_movimientos.split('||');
                    detalleMovimientos = '<div class="mt-1 text-uppercase text-muted fw-bold" style="font-size: 0.6rem;">Rastreo de Entradas:</div>';
                    movimientos.forEach(mov => {
                        detalleMovimientos += `
                            <div class="small p-1 mb-1 bg-white border-start border-success border-3 shadow-sm" style="font-size: 0.7rem;">
                                <i class="bi bi-arrow-right text-success"></i> ${mov} ${item.unidad_medida}
                            </div>`;
                    });
                }
            }

            filasHtml += `
                <tr class="align-top">
                    <td class="small text-muted">${item.sku || 'N/A'}</td>
                    <td class="text-start">
                        <div class="fw-bold text-dark">${nombre}</div>
                        ${conversionInfo}
                        ${esCompra ? detalleMovimientos : ''}
                    </td>
                    <td class="text-center bg-light">
                        <span class="d-block fw-bold">${parseFloat(item.cantidad_pedida || item.cantidad || 0)}</span>
                        <small class="text-muted">${esCompra ? item.unidad_medida : 'unidades'}</small>
                    </td>
                    ${esCompra ? `
                        <td class="text-center text-success fw-bold bg-light">${parseFloat(item.cantidad_recibida || 0)}</td>
                        <td class="text-center bg-light">${parseFloat(item.cantidad_faltante || 0)}</td>
                    ` : ''}
                    <td class="text-end">$${parseFloat(item.precio_unitario).toFixed(2)}</td>
                    <td class="text-end fw-bold">$${parseFloat(item.subtotal).toFixed(2)}</td>
                </tr>`;
        });
console.log(c.categoria_nombre );
        // --- LÓGICA DE LA CATEGORÍA (SOLO PARA GASTOS) ---
        let htmlCategoria = '';
        if (!esCompra && c.categoria_nombre) {
            htmlCategoria = `
                <div class="mt-2 pt-2 border-top">
                    <small class="text-muted d-block" style="font-size: 0.65rem;">CATEGORÍA DEL GASTO</small>
                    <span class="fw-bold text-primary"><i class="bi bi-tag-fill me-1"></i> ${c.categoria_nombre.toUpperCase()}</span>
                </div>`;
        }

        const docHTML = `
            <div class="p-4 bg-white shadow-sm mx-auto" style="max-width: 950px; color: #333; font-family: 'Segoe UI', sans-serif;">
                <div class="row border-bottom border-3 border-primary pb-3 mb-4">
                    <div class="col-7 text-start">
                        <h2 class="fw-bold text-primary mb-0">CF SISTEM</h2>
                        <p class="text-muted mb-0">Gestión de Inventarios y Egresos</p>
                    </div>
                    <div class="col-5 text-end">
                        <div class="h4 fw-bold text-dark mb-0">${tipo.toUpperCase()}</div>
                        <div class="badge bg-dark fs-6">FOLIO: ${c.folio}</div>
                        <div class="small text-muted mt-1">Fecha: ${c.fecha_registro || c.fecha_gasto}</div>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-6 text-start">
                        <div class="p-3 border rounded-3 bg-light h-100">
                            <small class="text-muted fw-bold text-uppercase d-block mb-2 border-bottom">Control Interno</small>
                            <div><strong>Almacén:</strong> ${c.almacen_nombre || 'N/A'}</div>
                            <div><strong>Usuario:</strong> ${c.usuario_nombre}</div>
                            ${htmlCategoria} <div class="mt-2"><strong>Estado:</strong> <span class="badge ${c.estado === 'confirmada' || c.estado === 'pagado' ? 'bg-success' : 'bg-warning'}">${c.estado.toUpperCase()}</span></div>
                        </div>
                    </div>
                    <div class="col-6 text-end">
                        <div class="p-3 border rounded-3 bg-light h-100">
                            <small class="text-muted fw-bold text-uppercase d-block mb-2 border-bottom">${esCompra ? 'Proveedor' : 'Beneficiario'}</small>
                            <div class="h6 mb-1 fw-bold text-primary">${c.proveedor || c.beneficiario}</div>
                            
                            <small class="d-block text-muted">Método: ${c.metodo_pago || 'N/A'}</small>
                            ${c.documento_url ? `<a href="${c.documento_url}" target="_blank" class="btn btn-link btn-sm p-0 text-decoration-none"><i class="bi bi-file-earmark-pdf"></i> Ver Comprobante</a>` : '<small class="text-muted">Sin adjunto</small>'}
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead class="table-dark">
                            <tr class="small text-uppercase">
                                <th style="width: 10%">SKU</th>
                                <th style="width: ${esCompra ? '40%' : '55%'}">Descripción</th>
                                <th class="text-center">Cant.</th>
                                ${esCompra ? '<th class="text-center">Recibido</th><th class="text-center">Pend.</th>' : ''}
                                <th class="text-end">P. Unit</th>
                                <th class="text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${filasHtml}
                        </tbody>
                        <tfoot>
                            <tr class="table-secondary">
                                <td colspan="${esCompra ? 5 : 4}" class="text-end fw-bold text-uppercase">Total Neto:</td>
                                <td class="text-end fw-bold h5 text-primary mb-0">$${parseFloat(c.total).toLocaleString('es-MX', {minimumFractionDigits: 2})}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                ${c.observaciones ? `<div class="p-3 bg-light border-start border-4 border-warning my-3 small italic text-start"><strong>Notas:</strong> ${c.observaciones}</div>` : ''}

                <div class="row mt-5 pt-4 text-center">
                    <div class="col-4"><div style="border-top: 2px solid #333; margin: 0 15px;" class="pt-2 small fw-bold">SOLICITADO POR</div></div>
                    <div class="col-4"><div style="border-top: 2px solid #333; margin: 0 15px;" class="pt-2 small fw-bold">ALMACÉN / RECIBO</div></div>
                    <div class="col-4"><div style="border-top: 2px solid #333; margin: 0 15px;" class="pt-2 small fw-bold">AUTORIZACIÓN</div></div>
                </div>
            </div>`;

        $('#ticketContenido').html(docHTML);
        const modalEl = document.getElementById('modalVerDetalle');
        const modalInstance = bootstrap.Modal.getOrCreateInstance(modalEl);
        modalInstance.show();
    });
}
</script>