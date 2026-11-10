<div id="gastoDetalle_seccionImpresion">
    <div class="modal fade" id="gastoDetalle_modalPrincipal" tabindex="-1" aria-hidden="true" data-bs-focus="false">
        <div class="modal-dialog modal-xl">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; overflow: hidden;">
                <div class="modal-body p-0" id="gastoDetalle_contenedorContenido"></div>
                <div class="modal-footer border-0 bg-light justify-content-center">
                    <button type="button" class="btn btn-dark btn-sm px-4 rounded-pill"
                        onclick="gastoDetalle_ejecutarImpresion()">
                        <i class="bi bi-printer me-2"></i>IMPRIMIR GASTO
                    </button>
                    <button type="button" class="btn btn-dark btn-sm px-4 rounded-pill"
                        onclick="gastoDetalle_ejecutarImpresionIVA()">
                        <i class="bi bi-printer me-2"></i>IMPRIMIR GASTO CON IVA
                    </button>
                    <button type="button" class="btn btn-outline-secondary btn-sm px-4 rounded-pill"
                        data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ✅ html2canvas (captura) + jsPDF (convierte canvas a PDF) + SweetAlert2 -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    .gasto-invoice-box {
        max-width: 950px;
        margin: auto;
        padding: 20px;
        background: #fff;
        font-family: 'Segoe UI', Helvetica, Arial, sans-serif;
        color: #334155;
        box-sizing: border-box;
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

    /* ============================================================
       ✅ FORZAR TEMA CLARO DENTRO DEL WRAPPER (para html2canvas)
       Anula cualquier variable oscura heredada de Bootstrap o del body
       ============================================================ */
    .gasto-force-light {
        color-scheme: light !important;
        background-color: #ffffff !important;
        color: #1e293b !important;

        /* Variables Bootstrap 5.3+ */
        --bs-body-bg: #ffffff !important;
        --bs-body-color: #1e293b !important;
        --bs-emphasis-color: #0f172a !important;
        --bs-secondary-color: #64748b !important;
        --bs-secondary-bg: #e9ecef !important;
        --bs-tertiary-bg: #f8f9fa !important;
        --bs-border-color: #dee2e6 !important;
        --bs-heading-color: #0f172a !important;
        --bs-table-bg: transparent !important;
        --bs-table-color: #1e293b !important;
    }

    .gasto-force-light,
    .gasto-force-light * {
        color-scheme: light !important;
    }

    .gasto-force-light .text-body-secondary {
        color: #64748b !important;
    }

    .gasto-force-light .text-dark {
        color: #1e293b !important;
    }

    .gasto-force-light .text-body {
        color: #1e293b !important;
    }

    .gasto-force-light .text-muted {
        color: #64748b !important;
    }

    .gasto-force-light .text-white {
        color: #ffffff !important;
    }

    .gasto-force-light .bg-light {
        background-color: #f8f9fa !important;
    }

    .gasto-force-light .bg-white {
        background-color: #ffffff !important;
    }

    .gasto-force-light .bg-dark {
        background-color: #0f172a !important;
    }

    .gasto-force-light .border,
    .gasto-force-light .border-bottom {
        border-color: #dee2e6 !important;
    }

    .gasto-force-light .table {
        color: #1e293b !important;
    }

    .gasto-force-light .table-dark {
        --bs-table-bg: #0f172a !important;
        --bs-table-color: #ffffff !important;
        color: #ffffff !important;
    }

    .gasto-force-light .table-dark th {
        color: #ffffff !important;
        background-color: #0f172a !important;
    }

    .gasto-force-light .badge.bg-success {
        background-color: #198754 !important;
        color: #ffffff !important;
    }

    .gasto-force-light .badge.bg-warning {
        background-color: #ffc107 !important;
        color: #000000 !important;
    }

    .gasto-force-light h1,
    .gasto-force-light h2,
    .gasto-force-light h3,
    .gasto-force-light h4,
    .gasto-force-light h5,
    .gasto-force-light h6 {
        color: #0f172a !important;
    }

    @media print {
        body.gasto-print-mode * {
            visibility: hidden !important;
        }

        body.gasto-print-mode .modal-backdrop,
        body.gasto-print-mode .modal-header,
        body.gasto-print-mode .modal-footer,
        body.gasto-print-mode .btn,
        body.gasto-print-mode .btn-close {
            display: none !important;
        }

        body.gasto-print-mode #gastoDetalle_modalPrincipal,
        body.gasto-print-mode .modal-dialog,
        body.gasto-print-mode .modal-content,
        body.gasto-print-mode .modal-body {
            position: static !important;
            display: block !important;
            visibility: visible !important;
            background: transparent !important;
            border: none !important;
            box-shadow: none !important;
            padding: 0 !important;
            margin: 0 !important;
            width: 100% !important;
            max-width: 100% !important;
            overflow: visible !important;
        }

        body.gasto-print-mode #gastoDetalle_areaCapturaPDF,
        body.gasto-print-mode #gastoDetalle_areaCapturaPDF * {
            visibility: visible !important;
        }

        body.gasto-print-mode #gastoDetalle_areaCapturaPDF {
            position: absolute !important;
            left: 0 !important;
            top: 0 !important;
            width: 100% !important;
            margin: 0 !important;
            padding: 10px !important;
            background: #ffffff !important;
        }

        body.gasto-print-mode .gasto-table-layout,
        body.gasto-print-mode .gasto-items-table,
        body.gasto-print-mode .gasto-date-tile {
            display: table !important;
            width: 100% !important;
        }

        body.gasto-print-mode tr {
            page-break-inside: avoid !important;
        }
    }
</style>

<script>
    let observaciones = '';
    let filasHtmlGastoIVA = '';

    function gastoDetalle_cargarVista(gastoTipo, gastoId) {
        if (gastoTipo === 'compra') return;

        filasHtmlGastoIVA = '';
        observaciones = '';

        $.get(`/cfsistem/app/controllers/egresosController.php?action=obtenerDetalleMovimiento&tipo=${gastoTipo}&id=${gastoId}`, function (responseDetalle) {
            if (!responseDetalle.success) return Swal.fire('Error', responseDetalle.message, 'error');

            const cabeceraGasto = responseDetalle.cabecera;
            let filasHtmlGasto = '';
            console.log(responseDetalle);

            responseDetalle.items.forEach(itemGasto => {
                filasHtmlGasto += `
                <tr>
                    <td style="font-family: monospace; color: #64748b; font-size: 9pt;">${itemGasto.sku || 'N/A'}</td>
                      <td class="fw-bold" style="color: #475569; text-transform: uppercase;">
            ${itemGasto.insumo_u_ma || 'UNIDAD'}
        </td>
                    <td class="fw-bold" style="color: #0f172a;">${itemGasto.descripcion}</td>
                    <td class="text-end fw-bold" style="color: #0f172a;">${parseFloat(itemGasto.cantidad || 0).toFixed(4)}</td>
                    <td class="text-end" style="color: #475569;">$${parseFloat(itemGasto.precio_unitario).toFixed(2)}</td>
                    <td class="text-end fw-bold" style="color: #1e3a8a;">$${parseFloat(itemGasto.subtotal).toFixed(2)}</td>
                </tr>`;
                filasHtmlGastoIVA += `
                <tr>
                    <td style="font-family: monospace; color: #64748b; font-size: 9pt;">${itemGasto.sku || 'N/A'}</td>
                    <td class="fw-bold" style="color: #475569; text-transform: uppercase;">${itemGasto.insumo_u_ma || 'UNIDAD'}</td>
                    <td class="fw-bold" style="color: #0f172a;">${itemGasto.descripcion}</td>
                    <td class="text-end fw-bold" style="color: #0f172a;">${parseFloat(itemGasto.cantidad || 0).toFixed(4)}</td>
                    <td class="text-end" style="color: #475569;">$${parseFloat((itemGasto.precio_unitario) * (.84)).toFixed(2)}</td>
                    <td class="text-end" style="color: #475569;">$${parseFloat((itemGasto.precio_unitario) * (.16)).toFixed(2)}</td>
                    <td class="text-end fw-bold" style="color: #1e3a8a;">$${parseFloat(itemGasto.subtotal).toFixed(2)}</td>
                </tr>`;
            });

            let htmlCategoriaGasto = '';
            if (cabeceraGasto.categoria_nombre) {
                htmlCategoriaGasto = `
                <br><strong>Categoría:</strong> <span style="color:#0284c7; font-weight:600;">${cabeceraGasto.categoria_nombre.toUpperCase()}</span>`;
            }

            observaciones = `<div id="datos" class="gasto-card-obs">
                <strong>Observaciones:</strong>
                ${cabeceraGasto.observaciones ? `
                    <div style="margin-top: 3px; border-top: 1px solid #e2e8f0; padding-top: 2px;">
                        <span style="color:#1e293b;">${cabeceraGasto.observaciones}</span>
                    </div>` : ' Sin observaciones registradas.'}
                <div style="font-weight: 700; color: #334155; margin-bottom: 2px; text-transform: uppercase; font-size: 7.5pt; letter-spacing: 0.3px;">Validación de Operación</div>
                <strong>Método de Pago:</strong> ${cabeceraGasto.metodo_pago || 'N/A'} &nbsp;|&nbsp;
            </div>`;

            const estructuraTicketHTML = `
            <div class="gasto-invoice-box" id="gastoDetalle_areaCapturaPDF">
                <table class="gasto-table-layout">
                    <tr>
                        <td style="width: 32%;">
                            <div class="gasto-logo-container">
                                <img src="/cfsistem/public/assets/logo.png" style="width: 38px; height: auto;" alt="Logo" crossorigin="anonymous">
                                <div class="gasto-brand-title">FORTALEZA<br><span style="font-size:12pt; font-weight:600; color:#0284c7;">CENTRO</span></div>
                            </div>
                        </td>
                        <td style="width: 38%;" class="gasto-company-address">
                            <span style="font-weight: 600; color: #1e293b;">${cabeceraGasto.almacen_nombre || 'ALMACÉN GENERAL'}</span><br>
                            Control de Egresos Interno<br>
                            <span style="font-size: 7.5pt; color: #94a3b8;">Gestión de Inventarios y Egresos</span>
                        </td>
                        <td style="width: 30%;">
                            <div class="gasto-remision-badge">N° ${gastoTipo.toUpperCase()}<span>${cabeceraGasto.folio}</span></div>
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
                                        <td style="color:#64748b;"><strong>Direccion</strong></td>
                                        <td style="color:#475569; font-size:8pt;">${(cabeceraGasto.proveedor_direccion_formateada || 'N/A').toUpperCase()}</td>
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
                            <td class="text-right gasto-total-highlight">$${parseFloat(cabeceraGasto.total).toLocaleString('es-MX', { minimumFractionDigits: 2 })}</td>
                        </tr>
                    </tbody>
                </table>

                ${observaciones}
            </div>`;

            $('#gastoDetalle_contenedorContenido').html(estructuraTicketHTML);
            const refModalGasto = document.getElementById('gastoDetalle_modalPrincipal');
            bootstrap.Modal.getOrCreateInstance(refModalGasto).show();
        });
    }

    /* =========================================================
       UTILIDAD: ESCRITORIO (iframe oculto, autolimpieza) — MÓVIL (PDF)
       ========================================================= */
    async function gastoDetalle_generarComprobante(htmlBody, nombreArchivo) {
        const esMovil = /Android|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);

        // ---------- ESCRITORIO: iframe oculto (no abre ventana, no traba el sistema) ----------
        if (!esMovil) {
            const iframe = document.createElement('iframe');
            iframe.setAttribute('aria-hidden', 'true');
            iframe.style.position = 'fixed';
            iframe.style.right = '0';
            iframe.style.bottom = '0';
            iframe.style.width = '0';
            iframe.style.height = '0';
            iframe.style.border = '0';
            iframe.style.visibility = 'hidden';
            document.body.appendChild(iframe);

            const doc = iframe.contentWindow.document;
            doc.open();
            doc.write(`
                <!DOCTYPE html>
                <html lang="es" data-bs-theme="light">
                <head>
                    <meta charset="UTF-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <meta name="color-scheme" content="light">
                    <title>${nombreArchivo}</title>
                    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
                    <style>
                        :root, [data-bs-theme="light"] { color-scheme: light; }
                        @page { margin: 0; }
                        html, body {
                            background: #fff !important;
                            color: #1e293b !important;
                            color-scheme: light !important;
                        }
                        body {
                            font-family: 'Inter', system-ui, -apple-system, sans-serif;
                            padding: 1.2cm;
                        }
                        .marca-agua {
                            position: fixed; top: 35%; left: 50%;
                            transform: translate(-50%, -50%);
                            width: 240px; opacity: 0.06; z-index: -1; pointer-events: none;
                        }
                        .firma-linea {
                            border-top: 1px solid #000; margin-top: 50px;
                            text-align: center; padding-top: 5px; font-size: 11px;
                        }
                        .text-body-secondary { color: #64748b !important; }
                        .text-dark { color: #1e293b !important; }
                        .bg-light { background-color: #f8f9fa !important; }
                        .bg-white { background-color: #ffffff !important; }
                        .table-dark { --bs-table-bg: #0f172a !important; --bs-table-color: #ffffff !important; }
                    </style>
                </head>
                <body>${htmlBody}</body>
                </html>
            `);
            doc.close();

            const win = iframe.contentWindow;

            // Limpieza idempotente: elimina el iframe del DOM una sola vez
            let limpiado = false;
            const limpiar = () => {
                if (limpiado) return;
                limpiado = true;
                if (iframe && iframe.parentNode) {
                    iframe.parentNode.removeChild(iframe);
                }
                setTimeout(() => window.location.reload(), 300);
            };

            // Registrar el cierre ANTES de imprimir (esto es lo que antes fallaba)
            win.onafterprint = limpiar;

            // Esperar a que carguen las imágenes (logo, marca de agua) antes de imprimir
            const imgs = Array.from(iframe.contentDocument.images || []);
            const esperarImagenes = Promise.all(
                imgs.map(img =>
                    img.complete && img.naturalWidth > 0
                        ? Promise.resolve()
                        : new Promise(res => {
                            img.onload = res;
                            img.onerror = res;
                            setTimeout(res, 2000);
                        })
                )
            );

            // Carrera: o cargan todas las imágenes, o pasan 1.5 s como máximo
            Promise.race([esperarImagenes, new Promise(res => setTimeout(res, 1500))])
                .then(() => {
                    // Un frame extra para asegurar el paint
                    requestAnimationFrame(() => {
                        try {
                            win.focus();
                            win.print();
                        } catch (e) {
                            console.error('Error al imprimir:', e);
                            limpiar();
                        }

                        // Respaldo: si onafterprint no se disparó, limpiar igualmente
                        setTimeout(limpiar, 2000);
                    });
                });
            return;
        }

        // ---------- MÓVIL: capturar con html2canvas → PDF con jsPDF → descarga automática ----------
        if (typeof html2canvas === 'undefined' || typeof window.jspdf === 'undefined') {
            Swal.fire({
                icon: 'error',
                title: 'Faltan librerías',
                text: 'No se cargaron html2canvas o jsPDF. Recarga la página.',
                confirmButtonText: 'Cerrar'
            });
            return;
        }

        nombreArchivo = nombreArchivo.replace(/\.png$/i, '.pdf');

        const cover = document.createElement('div');
        cover.id = 'gastoDetalle_coverPDF';
        cover.style.cssText = `
            position: fixed; inset: 0;
            background: #ffffff;
            z-index: 2147483646;
            display: flex; align-items: center; justify-content: center;
            font-family: system-ui, sans-serif;
            color: #334155;
        `;
        cover.innerHTML = `
            <div style="text-align:center;">
                <div style="font-size:16px;font-weight:600;margin-bottom:8px;color:#334155;">Generando PDF…</div>
                <div style="font-size:12px;color:#94a3b8;">Por favor espera</div>
            </div>
        `;
        document.body.appendChild(cover);

        const htmlEl = document.documentElement;
        const prevThemeAttr = htmlEl.getAttribute('data-bs-theme');
        const prevDarkClasses = [];
        ['dark', 'theme-dark', 'dark-mode'].forEach(cls => {
            if (htmlEl.classList.contains(cls)) {
                prevDarkClasses.push(cls);
                htmlEl.classList.remove(cls);
            }
            if (document.body.classList.contains(cls)) {
                prevDarkClasses.push('body:' + cls);
                document.body.classList.remove(cls);
            }
        });
        htmlEl.setAttribute('data-bs-theme', 'light');
        const prevColorScheme = htmlEl.style.colorScheme;
        htmlEl.style.colorScheme = 'light';

        const wrapper = document.createElement('div');
        wrapper.id = 'gastoDetalle_wrapperPDF';
        wrapper.className = 'gasto-force-light';
        wrapper.style.cssText = `
            position: absolute;
            top: 0; left: 0;
            width: 900px;
            background: #ffffff;
            padding: 24px;
            box-sizing: border-box;
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            color: #1e293b;
            color-scheme: light;
            z-index: 2147483645;
        `;
        wrapper.innerHTML = htmlBody;
        wrapper.querySelectorAll('.marca-agua').forEach(el => el.remove());
        document.body.appendChild(wrapper);

        const restaurarTema = () => {
            if (prevThemeAttr) htmlEl.setAttribute('data-bs-theme', prevThemeAttr);
            else htmlEl.removeAttribute('data-bs-theme');
            htmlEl.style.colorScheme = prevColorScheme || '';
            prevDarkClasses.forEach(cls => {
                if (cls.startsWith('body:')) document.body.classList.add(cls.replace('body:', ''));
                else htmlEl.classList.add(cls);
            });
        };

        const cleanup = () => {
            if (wrapper.parentNode) wrapper.remove();
            if (cover.parentNode) cover.remove();
            restaurarTema();
        };

        try {
            await Promise.all(
                Array.from(wrapper.querySelectorAll('img')).map(img =>
                    img.complete && img.naturalWidth > 0
                        ? Promise.resolve()
                        : new Promise(res => {
                            img.onload = res;
                            img.onerror = res;
                            setTimeout(res, 3000);
                        })
                )
            );

            await new Promise(res => requestAnimationFrame(() => requestAnimationFrame(res)));

            const canvas = await html2canvas(wrapper, {
                scale: 1.5,
                useCORS: false,
                allowTaint: false,
                backgroundColor: '#ffffff',
                logging: false,
                scrollX: 0,
                scrollY: 0
            });

            console.log('[GastoPDF] Canvas:', canvas.width, 'x', canvas.height);

            const { jsPDF } = window.jspdf;
            const orientacion = canvas.width >= canvas.height ? 'landscape' : 'portrait';
            const pdf = new jsPDF({ unit: 'mm', format: 'a4', orientation: orientacion });

            const pageW = pdf.internal.pageSize.getWidth();
            const pageH = pdf.internal.pageSize.getHeight();
            const margen = 5;

            const maxW = pageW - margen * 2;
            const maxH = pageH - margen * 2;

            let imgW = maxW;
            let imgH = (canvas.height * imgW) / canvas.width;

            if (imgH > maxH) {
                imgH = maxH;
                imgW = (canvas.width * imgH) / canvas.height;
            }

            const x = (pageW - imgW) / 2;
            const y = (pageH - imgH) / 2;

            const imgData = canvas.toDataURL('image/jpeg', 0.92);
            pdf.addImage(imgData, 'JPEG', x, y, imgW, imgH);

            const pdfBlob = pdf.output('blob');
            cleanup();

            if (!pdfBlob || pdfBlob.size === 0) {
                throw new Error('El PDF generado está vacío');
            }

            console.log('[GastoPDF] PDF size:', (pdfBlob.size / 1024).toFixed(1), 'KB');

            const url = URL.createObjectURL(pdfBlob);
            const a = document.createElement('a');
            a.href = url;
            a.download = nombreArchivo;
            a.rel = 'noopener';
            a.style.display = 'none';
            document.body.appendChild(a);
            a.click();

            setTimeout(() => {
                if (a.parentNode) document.body.removeChild(a);
                URL.revokeObjectURL(url);
            }, 3000);

            Swal.fire({
                icon: 'success',
                title: 'PDF descargado',
                text: nombreArchivo,
                toast: true,
                position: 'top-end',
                timer: 2500,
                timerProgressBar: true,
                showConfirmButton: false
            });
        } catch (err) {
            console.error('[GastoPDF] Error:', err);
            cleanup();
            Swal.fire({
                icon: 'error',
                title: 'Error al generar PDF',
                html: `
                    <p>${err.message || 'Error desconocido'}</p>
                    <p style="font-size:12px;color:#64748b;">Intenta desde otro navegador o dispositivo.</p>
                `,
                confirmButtonText: 'Cerrar'
            });
        }
    }

    /* =========================================================
       IMPRESIÓN SIN IVA
       ========================================================= */
    function gastoDetalle_ejecutarImpresion() {
        const folio = document.querySelector('.gasto-remision-badge span')?.innerText || '000';
        const fecha = document.querySelector('.gasto-date-tile td.fw-bold')?.innerText || new Date().toLocaleDateString('es-MX');
        const beneficiario = document.querySelector('.gasto-card-info td.fw-bold')?.innerText || 'N/A';
        const usuario = document.querySelector('.gasto-card-info td[style*="color:#475569"]')?.innerText || 'N/A';
        const filasTabla = document.querySelector('.gasto-items-table tbody')?.innerHTML || '';
        const observaciones2 = typeof observaciones !== 'undefined' ? observaciones : '';

        const htmlBody = `
            <img src="/cfsistem/public/assets/logo.png" class="marca-agua" alt="Watermark" crossorigin="anonymous">
            <div id="areaImpresion" class="text-uppercase bg-white" style="font-size: 0.9rem;">
                <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                    <div class="d-flex align-items-center">
                        <img src="/cfsistem/public/assets/logo.png" alt="Logo" width="50" height="50" class="me-3" crossorigin="anonymous">
                        <div>
                            <h3 class="fw-bold text-uppercase mb-0" style="color:#0f172a; letter-spacing:1px;">COMPROBANTE DE GASTO</h3>
                            <div class="text-body-secondary small mt-1 text-uppercase">Folio: <span class="fw-bold text-dark">${folio}</span></div>
                        </div>
                    </div>
                    <div class="text-end text-uppercase">
                        <div class="fw-bold fs-5" style="color:#1e3a8a;">FORTALEZA CENTRO</div>
                        <div class="text-body-secondary small">Fecha: ${fecha}</div>
                    </div>
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-7">
                        <div class="p-2 border rounded bg-light">
                            <small class="text-body-secondary fw-bold d-block" style="font-size: 0.65rem;">BENEFICIARIO DE LA OPERACIÓN</small>
                            <div class="fw-bold text-dark mt-1" style="font-size: 0.85rem;">${beneficiario}</div>
                        </div>
                    </div>
                    <div class="col-5">
                        <div class="p-2 border rounded bg-light">
                            <small class="text-body-secondary fw-bold d-block" style="font-size: 0.65rem;">REGISTRADO POR</small>
                            <div class="fw-bold text-dark mt-1" style="font-size: 0.85rem;">${usuario}</div>
                        </div>
                    </div>
                </div>
                <div class="table-responsive mb-3">
                    <table class="table table-sm align-middle text-uppercase" style="font-size: 0.85rem;">
                        <thead class="table-dark">
                            <tr>
                                <th style="width: 15%;">CÓDIGO</th>
                                <th style="width: 15%;">UNIDAD</th>
                                <th>DESCRIPCIÓN</th>
                                <th class="text-end" style="width: 12%;">CANT.</th>
                                <th class="text-end" style="width: 15%;">P. UNITARIO</th>
                                <th class="text-end" style="width: 15%;">IMPORTE</th>
                            </tr>
                        </thead>
                        <tbody>${filasTabla}</tbody>
                    </table>
                </div>
                ${observaciones2}
                <div class="row mt-4 pt-3">
                    <div class="col-6"><div class="firma-linea">FIRMA / CONFORMIDAD DE ENTREGA</div></div>
                    <div class="col-6"><div class="firma-linea">AUTORIZADO POR CONTROL INTERNO</div></div>
                </div>
            </div>
        `;

        gastoDetalle_generarComprobante(htmlBody, `Comprobante_Gasto_${folio}.pdf`);
    }

    /* =========================================================
       IMPRESIÓN CON IVA
       ========================================================= */
    function gastoDetalle_ejecutarImpresionIVA() {
        const folio = document.querySelector('.gasto-remision-badge span')?.innerText || '000';
        const fecha = document.querySelector('.gasto-date-tile td.fw-bold')?.innerText || new Date().toLocaleDateString('es-MX');
        const beneficiario = document.querySelector('.gasto-card-info td.fw-bold')?.innerText || 'N/A';
        const usuario = document.querySelector('.gasto-card-info td[style*="color:#475569"]')?.innerText || 'N/A';
        const observaciones2 = typeof observaciones !== 'undefined' ? observaciones : '';

        const filasConIVA = (typeof filasHtmlGastoIVA !== 'undefined' && filasHtmlGastoIVA) ? filasHtmlGastoIVA : '';

        if (!filasConIVA) {
            Swal.fire({
                icon: 'warning',
                title: 'Sin datos de IVA',
                text: 'Primero abre el detalle del gasto para cargar el desglose.',
                confirmButtonText: 'Entendido'
            });
            return;
        }

        const htmlBody = `
            <img src="/cfsistem/public/assets/logo.png" class="marca-agua" alt="Watermark" crossorigin="anonymous">
            <div id="areaImpresion" class="text-uppercase bg-white" style="font-size: 0.9rem;">
                <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                    <div class="d-flex align-items-center">
                        <img src="/cfsistem/public/assets/logo.png" alt="Logo" width="50" height="50" class="me-3" crossorigin="anonymous">
                        <div>
                            <h3 class="fw-bold text-uppercase mb-0" style="color:#0f172a; letter-spacing:1px;">COMPROBANTE DE GASTO (CON IVA)</h3>
                            <div class="text-body-secondary small mt-1 text-uppercase">Folio: <span class="fw-bold text-dark">${folio}</span></div>
                        </div>
                    </div>
                    <div class="text-end text-uppercase">
                        <div class="fw-bold fs-5" style="color:#1e3a8a;">FORTALEZA CENTRO</div>
                        <div class="text-body-secondary small">Fecha: ${fecha}</div>
                    </div>
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-7">
                        <div class="p-2 border rounded bg-light">
                            <small class="text-body-secondary fw-bold d-block" style="font-size: 0.65rem;">BENEFICIARIO DE LA OPERACIÓN</small>
                            <div class="fw-bold text-dark mt-1" style="font-size: 0.85rem;">${beneficiario}</div>
                        </div>
                    </div>
                    <div class="col-5">
                        <div class="p-2 border rounded bg-light">
                            <small class="text-body-secondary fw-bold d-block" style="font-size: 0.65rem;">REGISTRADO POR</small>
                            <div class="fw-bold text-dark mt-1" style="font-size: 0.85rem;">${usuario}</div>
                        </div>
                    </div>
                </div>
                <div class="table-responsive mb-3">
                    <table class="table table-sm align-middle text-uppercase" style="font-size: 0.85rem;">
                        <thead class="table-dark">
                            <tr>
                                <th style="width: 15%;">CÓDIGO</th>
                                <th style="width: 15%;">UNIDAD</th>
                                <th>DESCRIPCIÓN</th>
                                <th class="text-end" style="width: 12%;">CANT.</th>
                                <th class="text-end" style="width: 15%;">P. UNITARIO</th>
                                <th class="text-end" style="width: 15%;">IVA</th>
                                <th class="text-end" style="width: 15%;">IMPORTE</th>
                            </tr>
                        </thead>
                        <tbody>${filasConIVA}</tbody>
                    </table>
                </div>
                ${observaciones2}
                <div class="row mt-4 pt-3">
                    <div class="col-6"><div class="firma-linea">FIRMA / CONFORMIDAD DE ENTREGA</div></div>
                    <div class="col-6"><div class="firma-linea">AUTORIZADO POR CONTROL INTERNO</div></div>
                </div>
            </div>
        `;

        gastoDetalle_generarComprobante(htmlBody, `Comprobante_Gasto_IVA_${folio}.pdf`);
    }
</script>