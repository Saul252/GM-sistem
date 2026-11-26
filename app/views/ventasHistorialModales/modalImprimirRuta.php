<style>
    #modalImprimirRuta {
        z-index: 10110 !important;
    }

    /* 🔥 Forzar que SweetAlert2 y su fondo oscuro aparezcan siempre arriba (por encima del modal 10110) */
    .swal2-container {
        z-index: 10120 !important;
    }
</style>

<div class="modal fade" id="modalImprimirRuta" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">

            <div class="modal-header bg-body-tertiary align-items-center py-3">
                <h6 class="modal-title fw-bold">
                    <i class="bi bi-receipt text-primary"></i>
                    Ruta de Reparto: <span id="folioRutaPrint" class="text-primary"></span>
                </h6>

                <div class="d-flex gap-2 ms-auto me-2">
                    <button class="btn btn-primary btn-sm px-3 d-flex align-items-center gap-1"
                        onclick="imprimirModalRuta()">
                        <span>🖨</span> Imprimir
                    </button>
                    <div id="contenedorBotonCancelar"></div>
                </div>

                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body p-0" id="contenidoRutaPrint">
                <!-- AQUÍ SE RENDERIZA TODO EL CONTENIDO DINÁMICO -->
            </div>

        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/html3pdf@0.12.1/dist/html3pdf.bundle.min.js"></script>
<script>
    // ═══════════════════════════════════════════════════════════════
    // HELPERS DE CONVERSIÓN
    // ═══════════════════════════════════════════════════════════════

    function formatearCantidadRuta(cantidadBase, prod) {
        const cantBase = parseFloat(cantidadBase) || 0;
        const factor = parseFloat(prod.factor) || 1;
        const uBase = prod.unidadMedida || '';
        const uRep = prod.unidadReporte || uBase;

        if (factor <= 1) {
            return { cantidad: cantBase, unidad: uBase };
        }

        const enReporte = cantBase / factor;

        if (enReporte >= 1) {
            return { cantidad: enReporte, unidad: uRep };
        }

        return { cantidad: cantBase, unidad: uBase };
    }

    function fmtNumRuta(n) {
        const v = parseFloat(n) || 0;
        return Number.isInteger(v) ? v.toString() : v.toFixed(3).replace(/\.?0+$/, '');
    }
    function limpiarDireccion(dir) {
        if (!dir) return '-';

        // Palabras comunes a omitir si están solas o al inicio
        const prefijos = /^(calle|col\.?|colonia|pueblo|ciudad|cp|c\.p\.|estado|municipio|delegaci[oó]n|entre calles?)\b/i;

        return String(dir)
            .split(',')
            .map(seg => {
                let limpio = seg.trim();
                // Quitamos el prefijo si está al inicio
                limpio = limpio.replace(prefijos, '').trim();
                // Quitamos signos de puntuación sobrantes al inicio o final del segmento (:, ;, ., etc.)
                limpio = limpio.replace(/^[:;.,\s]+|[:;.,\s]+$/g, '');
                return limpio;
            })
            // Filtramos segmentos vacíos o que hayan quedado reducidos a palabras genéricas o símbolos
            .filter(seg => seg.length > 0 && !/^(pueblo|colonia|calle|cp)$/i.test(seg))
            .join(', ');
    }

    // ═══════════════════════════════════════════════════════════════
    // CANCELAR ENTREGA
    // ═══════════════════════════════════════════════════════════════
    function cancelarEntrega(idEntrega) {
        Swal.fire({
            title: '¿Estás seguro?',
            text: "¡Se cancelará la entrega y se devolverá el stock al inventario y lotes!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, cancelar entrega',
            cancelButtonText: 'No'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/cfsistem/app/controllers/repartosController.php',
                    type: 'GET',
                    dataType: 'json',
                    data: {
                        action: 'cancelar_entrega',
                        id: idEntrega
                    },
                    success: function (response) {
                        if (response.success) {
                            Swal.fire(
                                '¡Cancelado!',
                                response.message,
                                'success'
                            ).then(() => {
                                // Cerrar el modal actual y refrescar la vista
                                const modalElement = document.getElementById('modalImprimirRuta');
                                const modalInstance = bootstrap.Modal.getInstance(modalElement);
                                if (modalInstance) modalInstance.hide();
                                location.reload();
                            });
                        } else {
                            Swal.fire('Error', response.message || 'No se pudo procesar la solicitud.', 'error');
                        }
                    },
                    error: function () {
                        Swal.fire('Error de red', 'Ocurrió un error al comunicarse con el servidor.', 'error');
                    }
                });
            }
        });
    }

    // ═══════════════════════════════════════════════════════════════
    // RENDER DE RUTA DE REPARTO
    // ═══════════════════════════════════════════════════════════════

    async function imprimirRuta(entrega_ida, folioViaje) {
        document.getElementById('folioRutaPrint').textContent = entrega_ida;
        document.getElementById('contenedorBotonCancelar').innerHTML = `
        <button type="button" class="btn btn-outline-danger btn-sm px-2 d-flex align-items-center gap-1" onclick="cancelarEntrega('${entrega_ida}')">
            <i class="bi bi-x-circle"></i> Cancelar Entrega
        </button>
    `;

        const respuesta = await fetch(
            `/cfsistem/app/controllers/repartosController.php?action=get_ruta_entrega_por_despacho&entrega_id=${entrega_ida}&id=${encodeURIComponent(folioViaje)}`
        );

        const data = await respuesta.json();
        if (!data.success) return;

        const cont = document.getElementById('contenidoRutaPrint');
        const datos = data.data;
        const info = datos[0] || {};
        console.log(datos);

        const stringEntrega = String(entrega_ida || '').toUpperCase();
        const stringFolio = String(info.folio_viaje || '').toUpperCase();
        const esEntregaPatio = stringEntrega.includes('EN_PAT') || stringFolio.includes('EN_PAT');

        let filas = '';
        datos.forEach((prod, i) => {
            const fmt = formatearCantidadRuta(prod.totalCantidad, prod);
            const codigoProd = prod.codigo || prod.sku || (i + 1);

            filas += `
            <tr>
                <td class="info-value font-monospace fw-semibold text-body">${codigoProd}</td>
                <td class="info-value text-body">${prod.nombreProducto}</td>
                <td class="info-value text-end">${fmtNumRuta(fmt.cantidad)}</td>
                <td class="info-value text-center">${fmt.unidad}</td>
                <td class="info-value text-center no-print-lote">${prod.codigo_lote ?? '-'}</td>
            </tr>
        `;
        });

        const tituloDocumento = esEntregaPatio ? 'ORDEN DE ENTREGA EN PATIO' : 'ORDEN DE ENTREGA / HOJA DE RUTA';
        const direccionLimpia = limpiarDireccion(info.direccion_entrega) || '-';

        const bloqueOperador = esEntregaPatio ? `
        <div class="info-card h-100">
            <span class="info-value text-body">Punto de Entrega</span>
            <div class="info-value text-body mb-1">ENTREGA EN PATIO / MOSTRADOR</div>
            <div class="info-meta">
                <span class="meta-title text-body">Entrego:</span>
                <span class="meta-value text-body">${info.nombre_chofer ?? '-'}</span>
            </div>
        </div>
    ` : `
        <div class="info-card h-100">
            <div class="info-split d-flex align-items-center justify-content-between">
                <div class="info-col text-center" style="flex:1;">
                    <span class="info-value text-body">Operador / Chofer</span>
                    <div class="info-value text-body">${info.nombre_chofer ?? '-'}</div>
                </div>
                <div class="info-divider-v mx-2"></div>
                <div class="info-col text-center" style="flex:1;">
                    <span class="info-value text-body">Unidad de Transporte</span>
                    <div class="info-value text-body">${info.unidad_nombre ?? '-'}</div>
                    <div class="info-meta justify-content-center gap-1">
                        <span class="meta-title text-body">Placas:</span>
                        <span class="meta-value text-body">${info.unidad_placas ?? '-'}</span>
                    </div>
                </div>
            </div>
        </div>
    `;

        const fechasHeader = esEntregaPatio ? `
        <div class="info-value mb-1">Fecha Despacho:____________________</div>
        <div class="info-value mb-1">Hora Entrega:____________________</div>
    ` : `
        <div class="info-value mb-1">Fecha de Salida:____________________</div>
        <div class="info-value mb-1">Fecha de llegada:____________________</div>
    `;

        const firmasSeccion = esEntregaPatio ? `
        <div class="row g-4">
            <div class="col-4">
                <div class="firma-box text-center">
                    <div class="firma-linea"></div>
                    <div class="info-value text-body">Firma Despachador / Patio</div>
                    <div class="text-body info-value">Entrega Material</div>
                </div>
            </div>
            <div class="col-4">
                <div class="firma-box text-center">
                    <div class="firma-linea"></div>
                    <div class="info-value text-body">Firma Cliente / Recibe</div>
                    <div class="text-body info-value">Sello y Firma de conformidad</div>
                </div>
            </div>
            <div class="col-4">
                <div class="info-box p-2 border rounded bg-body-tertiary" style="min-height: 80px;">
                    <div class="info-value">Observaciones / Check Patio:</div>
                </div>
            </div>
        </div>
    ` : `
        <div class="row g-4">
            <div class="col-4">
                <div class="firma-box text-center">
                    <div class="firma-linea"></div>
                    <div class="firma-nombre text-body">Firma Chofer / Transportista</div>
                    <div class="text-body small">Nombre y Fecha</div>
                </div>
            </div>
            <div class="col-4">
                <div class="firma-box text-center">
                    <div class="firma-linea"></div>
                    <div class="firma-nombre text-body">Firma Cliente / Recibe</div>
                    <div class="text-body small">Sello y Firma de conformidad</div>
                </div>
            </div>
            <div class="col-4">
                <div class="info-box p-2 border rounded bg-body-tertiary" style="min-height: 80px;">
                    <div class="info-sub small text-body">Observaciones y Comentarios:</div>
                </div>
            </div>
        </div>
    `;

        let html = `
    <div class="hoja-ruta-container p-2">
        <style>
            .hoja-ruta-container * {
                text-transform: uppercase !important;
                font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            }
            .font-monospace {
                font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace !important;
            }
            .header-orden {
                display: grid;
                grid-template-columns: 1fr 1.2fr 1.1fr;
                gap: 10px;
                align-items: center;
                padding-bottom: 8px;
                border-bottom: 2px solid var(--bs-border-color);
                margin-bottom: 8px;
            }
            .sucursal-info {
                text-align: center;
                font-size: 9px;
                line-height: 1.25;
                color: var(--bs-body-color);
                font-weight: 500;
            }
            .folios-grid {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 4px;
            }
            .folio-box {
                border: 1px solid var(--bs-border-color);
                border-radius: 6px;
                overflow: hidden;
                text-align: center;
                background: var(--bs-body-bg);
            }
            .folio-box.full-span {
                grid-column: span 2;
                display: flex;
                align-items: center;
                justify-content: space-between;
                padding: 3px 8px;
            }
            .folio-box-header {
                background: var(--bs-tertiary-bg);
                font-size: 8.5px;
                font-weight: 700;
                letter-spacing: 0.05em;
                padding: 2px 4px;
                border-bottom: 1px solid var(--bs-border-color);
                color: var(--bs-body-color);
            }
            .folio-box-val {
                padding: 3px 6px;
                font-size: 11.5px;
                font-weight: 700;
                color: var(--bs-body-color);
            }
            .info-card {
                background: var(--bs-tertiary-bg);
                border-radius: 10px;
                border: 1px solid var(--bs-border-color);
                padding: 8px 12px;
            }
            .info-label {
                display: block;
                font-size: 9.5px;
                font-weight: 700;
                letter-spacing: 0.08em;
                color: var(--bs-body-color);
                margin-bottom: 2px;
            }
            .info-value {
                font-size: 11.5px;
                font-weight: 600;
                line-height: 1.25;
                color: var(--bs-body-color);
            }
            .info-meta {
                margin-top: 2px;
                font-size: 10px;
                display: flex;
                flex-wrap: wrap;
                gap: 4px;
                align-items: center;
            }
            .meta-title {
                color: var(--bs-body-color);
                font-weight: 500;
            }
            .meta-value {
                color: var(--bs-body-color);
                font-weight: 600;
            }
            .meta-badge {
                background: var(--bs-body-bg);
                border: 1px solid var(--bs-border-color);
                padding: 1px 4px;
                border-radius: 4px;
                font-size: 9.5px;
                font-weight: 700;
                color: var(--bs-body-color);
            }
            .tabla-orden th {
                font-size: 10px;
                font-weight: 700;
                letter-spacing: 0.05em;
                color: var(--bs-body-color);
                border-bottom: 2px solid var(--bs-border-color) !important;
                padding: 6px 6px;
            }
            .tabla-orden td {
                font-size: 11px;
                padding: 6px 6px;
                border-bottom: 1px solid var(--bs-border-color);
                color: var(--bs-body-color);
            }
            .firma-linea {
                width: 85%;
                margin: 25px auto 4px auto;
                border-top: 1px solid var(--bs-body-color);
            }
            .firma-nombre {
                font-size: 10px;
                font-weight: 700;
                color: var(--bs-body-color);
            }
            @media print {
                .no-print-lote {
                    display: none !important;
                }
            }
        </style>

        <div class="header-orden">
            <div class="d-flex align-items-center gap-2">
                <img src="/cfsistem/public/assets/logo.ico" alt="Logo" style="width:30px; height:30px; object-fit:contain;">
                <div>
                    <div class="fw-bold text-body lh-1" style="font-size: 14.5px; letter-spacing: -0.01em;">BODEGA FORTALEZA</div>
                    <div class="fw-bold text-body lh-1 mt-1" style="font-size: 12px; letter-spacing: 0.04em;">CENTRO</div>
                </div>
            </div>

            <div class="sucursal-info">
                <div class="info-value fw-bold text-body">BODEGA FORTALEZA CENTRO</div>
                <div class="info-value text-body">ÁLVARO OBREGÓN #13 COLONIA CENTRO</div>
                <div class="info-value text-body">SAN MARTÍN CUAUTLALPAN C.P. 62574, CHALCO, EDOMEX</div>
            </div>

            <div class="folios-grid">
                <div class="folio-box">
                    <div class="info-value folio-box-header"># ENTREGA</div>
                    <div class="info-value folio-box-val font-monospace">${info.folio_viaje ?? entrega_ida}</div>
                </div>
                <div class="folio-box">
                    <div class="info-value folio-box-header"># VENTA</div>
                    <div class="info-value folio-box-val font-monospace">${info.folio_venta ?? '-'}</div>
                </div>
                <div class="folio-box full-span">
                    <span class="info-value p-0">FECHA</span>
                    <span class="info-value">${info.fecha_viaje ?? '-'}</span>
                </div>
            </div>
        </div>

        <div class="row g-2 mb-2">
            <div class="col-6">
                <div class="info-card h-100">
                    <span class="info-value text-body">Vendido a</span>
                    <div class="info-value text-body mb-1">${info.cliente ?? '-'}</div>
                    <div class="info-meta">
                        <span class="meta-title text-body">Teléfono:</span>
                        <span class="meta-value text-body font-monospace">${info.tel_cliente ?? 'SIN TELÉFONO'}</span>
                    </div>
                    <div class="info-meta">
                        <span class="meta-title text-body">Dirección:</span>
                        <span class="meta-value text-body">${direccionLimpia}</span>
                    </div>
                </div>
            </div>

            <div class="col-6">
                ${bloqueOperador}
            </div>
        </div>

        <div class="table-responsive mb-1">
            <table class="table tabla-orden align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width: 15%">CÓDIGO</th>
                        <th style="width: 53%">PRODUCTO</th>
                        <th style="width: 16%" class="text-end">CANTIDAD</th>
                        <th style="width: 16%" class="text-center">UNIDAD</th>
                        <th style="width: 16%" class="text-center no-print-lote">LOTE</th>
                    </tr>
                </thead>
                <tbody>
                    ${filas}
                </tbody>
            </table>
        </div>

        <div class="text-end my-2">
            ${fechasHeader}
        </div>

        <div class="mt-2 pt-1">
            ${firmasSeccion}
        </div>
    </div>
    `;

        cont.innerHTML = html;

        const modal = new bootstrap.Modal(document.getElementById('modalImprimirRuta'));
        modal.show();
    }

    function imprimirModalRuta() {
        const esMovil = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) || window.innerWidth < 768;

        if (esMovil) {
            descargarPDFModalRuta();
        } else {
            imprimirConIframeEscritorio();
        }
    }

    function descargarPDFModalRuta() {
        const elementoOriginal = document.getElementById('contenidoRutaPrint');
        const folio = document.getElementById('folioRutaPrint')?.textContent?.trim() || 'RUTA';

        if (!elementoOriginal) {
            Swal.fire('Error', 'No se encontró el contenido para generar el PDF.', 'error');
            return;
        }

        // Clon temporal sin la columna LOTE para no alterar el modal visible
        const clon = elementoOriginal.cloneNode(true);
        clon.querySelectorAll('.no-print-lote').forEach(el => el.remove());

        const wrapper = document.createElement('div');
        wrapper.style.cssText = 'position:fixed;left:-9999px;top:0;width:820px;background:#ffffff;color:#000000;';
        wrapper.appendChild(clon);
        document.body.appendChild(wrapper);

        const opt = {
            margin: [5, 5, 5, 5],
            filename: `Orden_Entrega_${folio}.pdf`,
            image: { type: 'jpeg', quality: 0.98 },
            html2canvas: { scale: 2, useCORS: true, letterRendering: true },
            jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
        };

        Swal.fire({
            title: 'Generando PDF...',
            text: 'Por favor espere un momento.',
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); }
        });

        html3pdf().from(clon).set(opt).save().then(() => {
            wrapper.remove();
            Swal.close();
        }).catch(err => {
            wrapper.remove();
            console.error('Error al generar PDF:', err);
            Swal.fire('Error', 'No se pudo generar el archivo PDF en este dispositivo.', 'error');
        });
    }

    function imprimirConIframeEscritorio() {
        const origen = document.getElementById('contenidoRutaPrint');
        const clon = origen.cloneNode(true);
        clon.querySelectorAll('.no-print-lote').forEach(el => el.remove());

        const contenido = clon.innerHTML;
        const folio = document.getElementById('folioRutaPrint')?.textContent?.trim() || 'RUTA';

        const iframe = document.createElement('iframe');
        iframe.setAttribute('aria-hidden', 'true');
        Object.assign(iframe.style, {
            position: 'fixed', right: '0', bottom: '0',
            width: '0', height: '0', border: '0', visibility: 'hidden'
        });
        document.body.appendChild(iframe);

        const doc = iframe.contentWindow.document;
        doc.open();
        doc.write(`
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Hoja de Entrega / Ruta - ${folio}</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <style>
            *, *::before, *::after { box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif !important; }
            html, body { margin: 0; padding: 0; }
            body { background: #ffffff; color: #000000; font-size: 11px; line-height: 1.35; }
            .ticket { width: 100%; max-width: 820px; margin: 0 auto; background: #ffffff; border: 1px solid #000000; border-radius: 6px; overflow: hidden; }
            .hoja-ruta-container { padding: 10px 14px !important; }
            .info-card { background: #ffffff; border: 1px solid #333333; border-radius: 6px; overflow: hidden; }
            table { border-collapse: collapse !important; width: 100%; }
            table thead th { background: #e5e7eb !important; color: #000000 !important; font-size: 9.5px; font-weight: 700; padding: 6px 8px !important; border-bottom: 2px solid #000000 !important; }
            table tbody td { padding: 5px 8px !important; font-size: 11px; color: #000000; border-bottom: 1px solid #cccccc; }
            .no-print-lote { display: none !important; }
            @media print {
                html, body { margin: 0 !important; padding: 0 !important; background: transparent !important; -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
                .ticket { width: 100% !important; max-width: 100% !important; border: none !important; box-shadow: none !important; margin: 0 !important; }
                .hoja-ruta-container { padding: 0 !important; }
                .no-print, .no-print-lote { display: none !important; }
            }
            @page { size: A4 portrait; margin: 10mm; }
        </style>
    </head>
    <body>
        <div class="ticket">
            <div class="hoja-ruta-container">
                ${contenido}
            </div>
        </div>
    </body>
    </html>
    `);
        doc.close();

        const win = iframe.contentWindow;
        let limpiado = false;
        const limpiar = () => {
            if (limpiado) return;
            limpiado = true;
            if (iframe && iframe.parentNode) iframe.parentNode.removeChild(iframe);
        };

        win.onafterprint = limpiar;

        const imgs = Array.from(iframe.contentDocument.images || []);
        const esperarImagenes = Promise.all(
            imgs.map(img => img.complete && img.naturalWidth > 0 ? Promise.resolve() : new Promise(res => {
                img.onload = res; img.onerror = res; setTimeout(res, 2500);
            }))
        );

        Promise.race([esperarImagenes, new Promise(res => setTimeout(res, 2000))])
            .then(() => requestAnimationFrame(() => {
                try {
                    win.focus();
                    win.print();
                } catch (e) {
                    console.error('Error al imprimir:', e);
                    limpiar();
                }
                setTimeout(limpiar, 2500);
            }));
    }

</script>