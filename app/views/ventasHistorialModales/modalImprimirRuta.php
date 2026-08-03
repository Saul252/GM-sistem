
<div class="modal fade" id="modalImprimirRuta" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">

            <div class="modal-header bg-light align-items-center py-3">
                <h5 class="modal-title d-flex align-items-center gap-2 fw-bold text-dark">
                    <i class="bi bi-receipt text-primary"></i>
                    Ruta de Reparto: <span id="folioRutaPrint" class="text-primary"></span>
                </h5>

                <div class="d-flex gap-2 ms-auto me-2">
                    <button class="btn btn-primary btn-sm px-3 d-flex align-items-center gap-1" onclick="imprimirModalRuta()">
                        <span>🖨</span> Imprimir
                    </button>
                </div>

                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body p-0" id="contenidoRutaPrint">
                <!-- AQUÍ SE RENDERIZA TODO -->
            </div>

        </div>
    </div>
</div>
<script>
     async function imprimirRuta(entrega_ida, folioViaje) {

    document.getElementById('folioRutaPrint').textContent = entrega_ida;

    const respuesta = await fetch(
        `/cfsistem/app/controllers/repartosController.php?action=get_ruta_entrega_por_despacho&entrega_id=${entrega_ida}&id=${encodeURIComponent(folioViaje)}`
    );

    const data = await respuesta.json();

    console.log(data);

    if (!data.success) return;

    const cont = document.getElementById('contenidoRutaPrint');
    const datos = data.data;

    // =========================================
    // DATOS GENERALES
    // =========================================
    
    

    // =========================================
    // AGRUPAR PRODUCTOS
    // =========================================
    const productosAgrupados = {};

    datos.forEach(item => {
        const key = item.nombreProducto;
        if (!productosAgrupados[key]) {
            productosAgrupados[key] = {
                nombreProducto: item.nombreProducto,
                totalCantidad: 0,
                unidadMedida: item.unidadMedida,
                unidadReporte: item.unidadReporte,
                factor: item.factor
            };
        }
        productosAgrupados[key].totalCantidad += parseFloat(item.totalCantidad || 0);
    });

    // =========================================
    // GENERAR FILAS
    // =========================================
    let filas = '';

    datos.forEach((prod, i) => {
        console.log(prod);
        
        const total = prod.totalCantidad / prod.factor;
        const totalCantidad = prod.totalCantidad;
        const unidad = total >= 1 ? prod.unidadReporte : (totalCantidad/(1/prod.equi))>=1?prod.nombreEqui:prod.unidadMedida;
let cantidad=(1/prod.equi)==1?totalCantidad/prod.factor:totalCantidad/(1/prod.equi);
        // Formatear dinámicamente el color del badge del estado en la tabla
        let badgeColor = 'bg-warning text-dark';
        if (prod.estatus_logistico === 'completado') badgeColor = 'bg-success text-white';
        if (prod.estatus_logistico === 'en_transito') badgeColor = 'bg-primary text-white';

        filas += `
            <tr>
                <td class="text-body-secondary fw-semibold">${i + 1}</td>
                <td style="max-width:350px;" class="fw-medium text-dark">${prod.nombreProducto}</td>
                <td style="max-width:250px;" class="fw-bold text-primary">
                    ${parseFloat(cantidad).toFixed(2)} <span class="text-body-secondary fw-normal small">${unidad}</span>
                </td>
                <td style="max-width:250px;" class="text-body-secondary small">${prod.direccion_entrega ?? '-'}</td>
                <td class="text-center">
                    <span class="badge ${badgeColor}  text-uppercase font-monospace">${prod.estatus_logistico}</span>
                </td>
            </tr>
        `;
    });

    // Formatear color de estatus general
    let generalBadgeColor = 'bg-warning text-dark';
    if (data.data[0].estatus_logistico === 'completado') generalBadgeColor = 'bg-success text-white';
    if (data.data[0].estatus_logistico === 'en_transito') generalBadgeColor = 'bg-primary text-white';

    // =========================================
    // HTML GENERADO
    // =========================================
    console.log(data.data);
    let html = `
        <div class="hoja-ruta-container p-4">

            <!-- HEADER INTERNO -->
            <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
                <div>
                    <h4 class="fw-bold text-dark m-0 d-flex align-items-center gap-2">
                        <span>🚚</span> Venta ${data.data[0].folio_venta}: Hoja de Ruta
                    </h4>

                    <div class="text-body-secondary small mt-1">
                        Folio de viaje: <span class="fw-bold text-dark font-monospace">${data.data[0].folio_viaje}</span>
                    </div><div class="text-body-secondary small mt-1">
                        Registro de viaje: <span class="fw-bold text-dark font-monospace">${data.data[0].fecha_viaje ?? '-'}</span>
                    </div>
                </div>

                <div class="text-end">
                    <div class="small text-body-secondary mb-1">Fecha de Salida:____________________</div>
                    <div class="small text-body-secondary mb-1">Fecha de llegada:____________________</div>
                </div>
            </div>
<style>
*{
text-transform: uppercase !important;}
.info-grid{
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 12px;
    width: 100%;
}

/* tarjeta */
.info-box{
    border:1px solid #e9e9e9;
    border-radius:8px;
    padding:10px 12px;
    background:#fff;

    /* CLAVE: evita deformación en modal */
    min-width: 0;
}

/* títulos */
.info-title{
    font-size:10.5px;
    color:#6c757d;
    text-transform:uppercase;
    letter-spacing:.5px;
    margin-bottom:4px;
}

/* valor */
.info-value{
    font-size:13px;
    font-weight:600;
    line-height:1.2;

    /* evita desbordes en modal */
    white-space: normal;
    word-break: break-word;
}

/* subtítulo */
.info-sub{
    font-size:11.5px;
    color:#666;
}
</style>

            <!-- BLOQUES DE INFORMACIÓN PRINCIPAL -->
          <div class="info-grid">

    <div class="info-box">
        <div class="info-title">Unidad de Transporte</div>
        <div class="info-value">${data.data[0].unidad_nombre ?? '-'}</div>
        <div class="info-sub mt-1">
            Placas: <span class="fw-semibold">${data.data[0].unidad_placas ?? '-'}</span>
        </div>
    </div>

    <div class="info-box">
        <div class="info-title">Operador / Chofer</div>
        <div class="info-value">${data.data[0].nombre_chofer ?? '-'}</div>
        <div class="info-sub mt-1 text-body-secondary">Asignado de ruta</div>
    </div>

    <div class="info-box">
        <div class="info-title">Cliente Destino</div>
        <div class="info-value">${data.data[0].cliente ?? '-'}</div>
        <div class="info-sub mt-1">
            Tel: <span class="fw-semibold">${data.data[0].tel_cliente ?? 'Sin teléfono'}</span>
        </div>
    </div>

</div>

            <!-- SECCIÓN DE DETALLES / TABLA -->
            <div class="table-responsive border rounded mb-4">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th style="width: 5%">#</th>
                            <th style="width: 40%">Producto descripción</th>
                            <th style="width: 20%">Cantidad total</th>
                            <th style="width: 23%">Dirección de entrega</th>
                            <th style="width: 12%" class="text-center">Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${filas}
                    </tbody>
                </table>
            </div>

            <!-- ÁREA DE FIRMAS FORMALIZADA -->
            <div class="firmas-container pt-4">
                <div class="row g-5">
                    <div class="col-4">
                        <div class="firma-box">
                            <div class="firma-linea"></div>
                            <div class="firma-nombre">Firma Chofer / Transportista</div>
                            <div class="text-body-secondary small">Nombre y Fecha</div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="firma-box">
                            <div class="firma-linea"></div>
                            <div class="firma-nombre">Firma Cliente / Recibe</div>
                            <div class="text-body-secondary small">Sello y Firma de conformidad</div>
                        </div>
                    </div>
                    <div class="col-4">
                       <div class="info-box">
        <div class="info-sub mt-1">Observaciones y Comentarios:</div>
        
    </div>
                     
                    
                </div>
            </div>

        </div>
    `;

    cont.innerHTML = html;

    const modal = new bootstrap.Modal(document.getElementById('modalImprimirRuta'));
    modal.show();
}
function imprimirModalRuta() {

    const contenido = document.getElementById('contenidoRutaPrint').innerHTML;
    // Se abre en un formato horizontal adecuado para media hoja
    const ventana = window.open('', '_blank', 'width=950,height=650');

    ventana.document.write(`
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <title>Hoja de Ruta</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
            <style>
                body {
                    font-family: 'Segoe UI', Arial, sans-serif;
                    background: #f8f9fa;
                    color: #333;
                    padding: 15px;
                    font-size: 11.5px; /* Un poco más compacta para media hoja */
                }

                /* Contenedor tipo hoja limpia ajustable */
                .ticket {
                    width: 100%;
                    max-width: 820px;
                    margin: auto;
                    background: #fff;
                    border: 1px solid #e0e0e0;
                    border-radius: 12px;
                    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
                    overflow: hidden;
                }

                .hoja-ruta-container {
                    padding: 20px !important;
                }

                /* Cajas de datos estilizadas */
                .info-box {
                    border: 1px solid #e2e8f0;
                    border-radius: 8px;
                    padding: 10px 12px;
                    background: #f8fafc;
                    height: 100%;
                }

                .info-title {
                    font-size: 10px;
                    color: #64748b;
                    text-transform: uppercase;
                    letter-spacing: .6px;
                    font-weight: 700;
                    margin-bottom: 3px;
                }

                .info-value {
                    font-size: 12.5px;
                    font-weight: 600;
                    color: #1e293b;
                    line-height: 1.2;
                }

                .info-sub {
                    font-size: 11px;
                    color: #64748b;
                }

                /* Tablas pulidas */
                table thead th {
                    background-color: #f1f5f9 !important;
                    color: #475569 !important;
                    font-size: 10px;
                    text-transform: uppercase;
                    letter-spacing: .5px;
                    font-weight: 700;
                    padding: 8px 10px !important;
                    border-bottom: 2px solid #e2e8f0 !important;
                }

                table tbody td {
                    
                  
                    font-size: 11.5px;
                }

                /* Sección de Firmas estructurada */
                .firmas-container {
                    page-break-inside: avoid;
                }

                .firma-box {
                    text-align: center;
                    padding: 5px;
                }

                .firma-linea {
                    width: 75%;
                    margin: 35px auto 5px auto;
                    border-top: 1px solid #94a3b8;
                }

                .firma-nombre {
                    font-size: 11px;
                    font-weight: 600;
                    color: #1e293b;
                }
@media print {
    .info-grid{
        display: grid !important;
        grid-template-columns: repeat(3, 1fr) !important;
        gap: 10px !important;
    }
}
                /* Forzado estricto de Media Hoja (Formato Horizontal Compacto) */
                @media print {
                   
                    body {
                        background: #f8f9fa03 !important;
                        -webkit-print-color-adjust: exact !important;
                        print-color-adjust: exact !important;
                        padding: 0 !important;
                    }
                    .ticket {
                        width: 100% !important; /* Toma el ancho horizontal disponible sin estirarse verticalmente */
                        max-width: 100% !important;
                        background: #ffffff00 !important;
                        border: 1px solid #e0e0e0 !important;
                        border-radius: 12px !important;
                        box-shadow: 0 4px 12px rgba(0,0,0,0.05) !important;
                        margin: 0 auto !important;
                        -webkit-print-color-adjust: exact !important;
                        print-color-adjust: exact !important;
                    }
                    .no-print {
                        display: none !important;
                    }
                    tr { 
                        page-break-inside: avoid !important; 
                    }
                    .info-box {
                        background: #f8fafc14 !important;
                        border: 1px solid #e2e8f0 !important;
                        -webkit-print-color-adjust: exact !important;
                        print-color-adjust: exact !important;
                    }
                    table thead th {
                        background-color: #f1f5f9 !important;
                        -webkit-print-color-adjust: exact !important;
                        print-color-adjust: exact !important;
                    }
                }
                    table {
    border-collapse: collapse !important;
}

/* 🔥 CLAVE: elimina espacio interno de TODAS las celdas */
table tbody td {
    padding: 2px 6px !important;
    margin: 0 !important;
    line-height: 1.1 !important;
    vertical-align: middle !important;
}

/* elimina espacio extra de párrafos y saltos */
table p {
    margin: 0 !important;
    padding: 0 !important;
}

/* elimina saltos visuales tipo bloque */
table br {
    display: none !important;
}

/* si quieres aún MÁS compacto */
.table-compact td {
    padding: 1px 4px !important;
}
            </style>
        </head>
        <body>
<img
    src="/cfsistem/public/assets/logo.ico"
    style="
        position: fixed;
        top: 22.5%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 300px;
        opacity: 0.08;
        z-index: -1;
    "
>
            <div class="text-end mb-3 no-print" style="max-width: 850px; margin: auto;">
                <button class="btn btn-dark px-4 shadow-sm fw-semibold" onclick="window.print()">
                    🖨 Enviar a Impresora
                </button>
            </div>

            <div class="ticket">
                <div class="hoja-ruta-container">
                    ${contenido}
                </div>
            </div>

            <!-- Disparador automático de impresión al terminar de cargar -->
            <script>
                window.onload = function() {
                    setTimeout(function() {
                        window.print();
                    }, 300);
                };
            <\/script>

        </body>
        </html>
    `);

    ventana.document.close();
    ventana.focus();
}
   
</script>