<div id="gastoDetalle_seccionImpresion">
<div class="modal fade" id="gastoDetalle_modalPrincipal" tabindex="-1" aria-hidden="true" data-bs-focus="false">
    <div class="modal-dialog modal-xl"> 
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; overflow: hidden;">
            <div class="modal-body p-0" id="gastoDetalle_contenedorContenido"></div>
            <div class="modal-footer border-0 bg-light justify-content-center">
                <button type="button" class="btn btn-dark btn-sm px-4 rounded-pill" onclick="gastoDetalle_ejecutarImpresion()">
                    <i class="bi bi-printer me-2"></i>IMPRIMIR GASTO 
                </button>
                <button type="button" class="btn btn-outline-secondary btn-sm px-4 rounded-pill" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
</div>

<style>
/* Estilos replicados del formato Premium para mantener consistencia visual */
.gasto-invoice-box {
    max-width: 950px;
    margin: auto;
    padding: 20px;
    background: #fff;
    font-family: 'Segoe UI', Helvetica, Arial, sans-serif;
    color: #334155;
}
.gasto-table-layout {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 10px;
}
.gasto-logo-container {
    display: flex;
    align-items: center;
    gap: 10px;
}
.gasto-brand-title {
    font-size: 16pt;
    font-weight: 800;
    color: #1e3a8a;
    line-height: 1.1;
    letter-spacing: -0.5px;
}
.gasto-company-address {
    font-size: 8.5pt;
    color: #64748b;
    line-height: 1.4;
    text-align: center;
}
.gasto-remision-badge {
    background: #0f172a;
    color: #fff;
    padding: 6px 12px;
    text-align: center;
    font-size: 8pt;
    font-weight: 700;
    letter-spacing: 1px;
    border-radius: 4px;
    float: right;
    min-width: 140px;
}
.gasto-remision-badge span {
    display: block;
    font-size: 13pt;
    font-weight: 800;
    color: #38bdf8;
    margin-top: 2px;
}
.gasto-date-tile {
    width: 140px;
    float: right;
    margin-top: 5px;
    border-collapse: collapse;
    font-size: 8.5pt;
}
.gasto-date-tile td {
    padding: 4px 8px;
    border: 1px solid #e2e8f0;
}
.gasto-date-tile .title-td {
    background: #f1f5f9;
    color: #64748b;
    font-weight: 600;
}
.gasto-card-info {
    border: 1px solid #e2e8f0;
    border-radius: 6px;
    padding: 10px 12px;
    min-height: 90px;
    background-color: #ffffff;
}
.gasto-card-title {
    font-size: 7.5pt;
    font-weight: 800;
    color: #64748b;
    letter-spacing: 0.5px;
    margin-bottom: 6px;
    border-bottom: 1px solid #f1f5f9;
    padding-bottom: 3px;
    text-transform: uppercase;
}
.gasto-items-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 15px;
    font-size: 9pt;
}
.gasto-items-table th {
    background: #0f172a;
    color: #fff;
    font-weight: 600;
    font-size: 8pt;
    text-transform: uppercase;
    padding: 7px 10px;
    letter-spacing: 0.5px;
}
.gasto-items-table td {
    padding: 8px 10px;
    border-bottom: 1px solid #e2e8f0;
}
.gasto-items-table tr:nth-child(even) {
    background-color: #f8fafc;
}
.gasto-items-table .total-row td {
    border-bottom: none;
    padding-top: 12px;
}
.gasto-total-highlight {
    font-size: 13pt;
    font-weight: 800;
    color: #1e3a8a;
    background: #eff6ff;
    padding: 6px 12px !important;
    border-radius: 4px;
    border: 1px solid #bfdbfe !important;
}
.gasto-card-obs {
    margin-top: 15px;
    border: 1px dashed #cbd5e1;
    border-radius: 6px;
    padding: 10px 12px;
    font-size: 8pt;
    background: #fdfdfd;
    line-height: 1.5;
}

/* Manejo de Impresión clásica por CSS */
@media print {
    body * {
        visibility: hidden;
    }
    #gastoDetalle_seccionImpresion, 
    #gastoDetalle_seccionImpresion * {
        visibility: visible !important;
    }
    #gastoDetalle_seccionImpresion {
        display: block !important;
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
    }
    .modal-backdrop, .modal-footer {
        display: none !important;
    }
}
</style>

<script>
function gastoDetalle_cargarVista(gastoTipo, gastoId) {
    if (gastoTipo === 'compra') return;

    $.get(`/cfsistem/app/controllers/egresosController.php?action=obtenerDetalleMovimiento&tipo=${gastoTipo}&id=${gastoId}`, function(responseDetalle) {
        if (!responseDetalle.success) return Swal.fire('Error', responseDetalle.message, 'error');

        const cabeceraGasto = responseDetalle.cabecera;
        let filasHtmlGasto = '';

        responseDetalle.items.forEach(itemGasto => {
            filasHtmlGasto += `
                <tr>
                    <td style="font-family: monospace; color: #64748b; font-size: 9pt;">${itemGasto.sku || 'N/A'}</td>
                    <td class="fw-bold" style="color: #475569; text-transform: uppercase;">UNIDAD</td>
                    <td class="fw-bold" style="color: #0f172a;">${itemGasto.descripcion}</td>
                    <td class="text-end fw-bold" style="color: #0f172a;">${parseFloat(itemGasto.cantidad || 0).toFixed(4)}</td>
                    <td class="text-end" style="color: #475569;">$${parseFloat(itemGasto.precio_unitario).toFixed(2)}</td>
                    <td class="text-end fw-bold" style="color: #1e3a8a;">$${parseFloat(itemGasto.subtotal).toFixed(2)}</td>
                </tr>`;
        });

        let htmlCategoriaGasto = '';
        if (cabeceraGasto.categoria_nombre) {
            htmlCategoriaGasto = `
                <br><strong>Categoría:</strong> <span style="color:#0284c7; font-weight:600;">${cabeceraGasto.categoria_nombre.toUpperCase()}</span>`;
        }

        const estructuraTicketHTML = `
            <div class="gasto-invoice-box" id="gastoDetalle_areaCapturaPDF">
                
                <table class="gasto-table-layout">
                    <tr>
                        <td style="width: 32%;">
                            <div class="gasto-logo-container">
                                <img src="/cfsistem/public/assets/logo.ico" style="width: 38px; height: auto;" alt="Logo">
                                <div class="gasto-brand-title">FORTALEZA<br><span style="font-size:12pt; font-weight:600; color:#0284c7;">CENTRO</span></div>
                            </div>
                        </td>
                        
                        <td style="width: 38%;" class="gasto-company-address">
                            <span style="font-weight: 600; color: #1e293b;">${cabeceraGasto.almacen_nombre || 'ALMACÉN GENERAL'}</span><br>
                            Control de Egresos Interno<br>
                            <span style="font-size: 7.5pt; color: #94a3b8;">Gestión de Inventarios y Egresos</span>
                        </td>
                        
                        <td style="width: 30%;">
                            <div class="gasto-remision-badge">
                                N° ${gastoTipo.toUpperCase()}
                                <span>${cabeceraGasto.folio}</span>
                            </div>
                            <div style="clear: both;"></div>
                            <table class="gasto-date-tile">
                                <tr>
                                    <td class="title-td">Fecha</td>
                                    <td class="fw-bold" style="color: #334155;">${cabeceraGasto.fecha_registro || cabeceraGasto.fecha_gasto}</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>

                <table class="gasto-table-layout" style="margin-top: 4px;">
                    <tr>
                        <td style="width: 70%; padding-right: 6px;">
                            <div class="gasto-card-info">
                                <div class="gasto-card-title">BENEFICIARIO DE LA OPERACIÓN</div>
                                <table style="width:100%; border-collapse:collapse; font-size: 8.5pt;">
                                   <tr>
                                        <td style="color:#64748b; width: 18%;"><strong>Nombre:</strong></td>
                                        <td class="fw-bold" style="color:#1e3a8a; font-size:9.5pt;">${cabeceraGasto.beneficiario || 'N/A'}</td>
                                    </tr>
                                    <tr>
                                        <td style="color:#64748b;"><strong>Usuario:</strong></td>
                                        <td style="color:#475569; font-size:8pt;">${cabeceraGasto.usuario_nombre}</td>
                                    </tr>
                                </table>
                            </div>
                        </td>
                        
                        <td style="width: 30%;">
                            <div class="gasto-card-info" style="background-color: #f8fafc;">
                                <div class="gasto-card-title" style="color:#0284c7;">Información</div>
                                <div style="line-height: 1.4; color:#64748b; font-size: 8.5pt;">
                                    <strong>Estado:</strong> <span class="badge ${cabeceraGasto.estado === 'confirmada' || cabeceraGasto.estado === 'pagado' ? 'bg-success' : 'bg-warning'}">${cabeceraGasto.estado.toUpperCase()}</span>
                                    ${htmlCategoriaGasto}
                                </div>
                            </div>
                        </td>
                    </tr>
                </table>

                <table class="gasto-items-table">
                    <thead>
                        <tr>
                            <th style="width: 12%;">CÓDIGO</th>
                            <th style="width: 15%;">UNIDAD</th>
                            <th style="width: 43%;">DESCRIPCIÓN DEL GASTO</th>
                            <th class="text-right" style="width: 10%;">CANTIDAD</th>
                            <th class="text-right" style="width: 10%;">PRECIO U.</th>
                            <th class="text-right" style="width: 10%;">IMPORTE</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${filasHtmlGasto}
                        
                        <tr class="total-row">
                            <td colspan="4"></td>
                            <td class="text-right" style="color: #475569; font-size: 10pt; font-weight: 600; vertical-align: middle;">TOTAL MXN</td>
                            <td class="text-right gasto-total-highlight">$${parseFloat(cabeceraGasto.total).toLocaleString('es-MX', {minimumFractionDigits: 2})}</td>
                        </tr>
                    </tbody>
                </table>

                <div class="gasto-card-obs">
                    <div style="font-weight: 700; color: #334155; margin-bottom: 2px; text-transform: uppercase; font-size: 7.5pt; letter-spacing: 0.3px;">Validación de Operación</div>
                    <strong>Método de Pago:</strong> ${cabeceraGasto.metodo_pago || 'N/A'} &nbsp;|&nbsp; 
                    <strong>Control Interno:</strong> Sistema Egresos Premium<br>
                    <strong>Observaciones:</strong>
                    ${cabeceraGasto.observaciones ? `
                        <div style="margin-top: 3px; border-top: 1px solid #e2e8f0; padding-top: 2px;">
                             <span style="color:#1e293b;">${cabeceraGasto.observaciones}</span>
                        </div>
                    ` : ' Sin observaciones registradas.'}
                </div>

            </div>`;

        $('#gastoDetalle_contenedorContenido').html(estructuraTicketHTML);
        const refModalGasto = document.getElementById('gastoDetalle_modalPrincipal');
        const instanciaModalGasto = bootstrap.Modal.getOrCreateInstance(refModalGasto);
        instanciaModalGasto.show();
    });
}

function gastoDetalle_ejecutarImpresion() {
    const esMovil = /Android|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
    const elemento = document.getElementById('gastoDetalle_areaCapturaPDF');
    const folio = document.querySelector('.gasto-remision-badge span').innerText;

    if (esMovil) {
        // Ejecución exacta para Media Hoja Horizontal (A5 Landscape) usando html2pdf
        const opciones = {
            margin:       [8, 8, 8, 8],
            filename:     `Gasto_Premium_${folio}.pdf`,
            image:        { type: 'jpeg', quality: 0.98 },
            html2canvas:  { scale: 2, useCORS: true, letterRendering: true },
            jsPDF:        { unit: 'mm', format: 'a5', orientation: 'landscape' }
        };

        html2pdf().set(opciones).from(elemento).save();
    } else {
        // Escritorio: Mapea el clon al print template nativo
        const areaPrint = document.getElementById('gastoDetalle_seccionImpresion');
        areaPrint.innerHTML = elemento.outerHTML;
        window.print();
        areaPrint.innerHTML = '';
    }
}
</script>