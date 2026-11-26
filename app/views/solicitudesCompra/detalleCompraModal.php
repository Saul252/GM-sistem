<!-- =====================================================================
     MÓDULO INDEPENDIENTE: Detalle e Impresión de COMPRA
     Prefijo único: cfcd_ (funciones) / cfcd- (clases CSS) / cfcd_ (IDs)
     API pública:  cfcd_abrir(tipo, id) | cfcd_imprimir() | cfcd_cerrar()
     ===================================================================== -->
<div id="cfcd_seccionImpresion">
    <div class="modal fade" id="cfcd_modalPrincipal" tabindex="-1" aria-hidden="true" data-bs-focus="false">
        <div class="modal-dialog modal-xl">
            <div class="modal-content cfcd-modal-content border-0 shadow-lg"
                style="border-radius:12px; overflow:hidden;">
                <div class="modal-body p-0" id="cfcd_contenedorContenido"></div>
                <div class="modal-footer cfcd-modal-footer border-0 justify-content-center">
                    <button type="button" class="btn btn-primary btn-sm px-4 rounded-pill" onclick="cfcd_imprimir()">
                        <i class="bi bi-printer me-2"></i>IMPRIMIR COMPRA
                    </button>
                    <button type="button" class="btn btn-outline-secondary btn-sm px-4 rounded-pill"
                        onclick="cfcd_cerrar()">
                        Cerrar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* ================= Variables de tema (Claro por defecto / Oscuro con [data-bs-theme="dark"]) ================= */
    #cfcd_seccionImpresion {
        --cfcd-bg: #ffffff;
        --cfcd-surface: #f8fafc;
        --cfcd-surface-alt: #f1f5f9;
        --cfcd-rastreo-bg: #f1f5f9;
        --cfcd-border: #e2e8f0;
        --cfcd-border-dash: #cbd5e1;
        --cfcd-text: #334155;
        --cfcd-text-strong: #0f172a;
        --cfcd-muted: #64748b;
        --cfcd-subtle: #94a3b8;
        --cfcd-brand: #1e3a8a;
        --cfcd-brand-sub: #0284c7;
        --cfcd-header-bg: #0f172a;
        --cfcd-header-text: #ffffff;
        --cfcd-accent: #38bdf8;
        --cfcd-total-bg: #eff6ff;
        --cfcd-total-border: #bfdbfe;
        --cfcd-total-text: #1e3a8a;
    }

    [data-bs-theme="dark"] #cfcd_seccionImpresion {
        --cfcd-bg: #1e293b;
        --cfcd-surface: #0f172a;
        --cfcd-surface-alt: #334155;
        --cfcd-rastreo-bg: #1e293b;
        --cfcd-border: #334155;
        --cfcd-border-dash: #475569;
        --cfcd-text: #cbd5e1;
        --cfcd-text-strong: #f8fafc;
        --cfcd-muted: #94a3b8;
        --cfcd-subtle: #64748b;
        --cfcd-brand: #93c5fd;
        --cfcd-brand-sub: #38bdf8;
        --cfcd-header-bg: #0b1120;
        --cfcd-header-text: #f8fafc;
        --cfcd-accent: #38bdf8;
        --cfcd-total-bg: rgba(56, 189, 248, 0.12);
        --cfcd-total-border: rgba(56, 189, 248, 0.35);
        --cfcd-total-text: #7dd3fc;
    }

    /* ================= Estilos (prefijo cfcd-) ================= */
    .cfcd-modal-content {
        background-color: var(--cfcd-bg);
        color: var(--cfcd-text);
    }

    .cfcd-modal-footer {
        background-color: var(--cfcd-surface);
    }

    .cfcd-invoice-box {
        max-width: 950px;
        margin: auto;
        padding: 20px;
        background-color: var(--cfcd-bg);
        font-family: 'Segoe UI', Helvetica, Arial, sans-serif;
        color: var(--cfcd-text);
    }

    .cfcd-table-layout {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 10px;
    }

    .cfcd-logo-container {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .cfcd-brand-title {
        font-size: 16pt;
        font-weight: 800;
        color: var(--cfcd-brand);
        line-height: 1.1;
        letter-spacing: -.5px;
    }

    .cfcd-brand-sub {
        font-size: 10pt;
        font-weight: 600;
        color: var(--cfcd-brand-sub);
    }

    .cfcd-company-address {
        font-size: 8.5pt;
        color: var(--cfcd-muted);
        line-height: 1.4;
        text-align: center;
    }

    .cfcd-company-name {
        font-weight: 600;
        color: var(--cfcd-text-strong);
    }

    .cfcd-company-tag {
        font-size: 7.5pt;
        color: var(--cfcd-subtle);
    }

    .cfcd-remision-badge {
        background-color: var(--cfcd-header-bg);
        color: var(--cfcd-header-text);
        padding: 6px 12px;
        text-align: center;
        font-size: 8pt;
        font-weight: 700;
        letter-spacing: 1px;
        border-radius: 4px;
        float: right;
        min-width: 140px;
    }

    .cfcd-remision-badge span {
        display: block;
        font-size: 13pt;
        font-weight: 800;
        color: var(--cfcd-accent);
        margin-top: 2px;
    }

    .cfcd-date-tile {
        width: 140px;
        float: right;
        margin-top: 5px;
        border-collapse: collapse;
        font-size: 8.5pt;
    }

    .cfcd-date-tile td {
        padding: 4px 8px;
        border: 1px solid var(--cfcd-border);
        color: var(--cfcd-text);
    }

    .cfcd-date-tile .cfcd-title-td {
        background-color: var(--cfcd-surface-alt);
        color: var(--cfcd-muted);
        font-weight: 600;
    }

    .cfcd-card-info {
        border: 1px solid var(--cfcd-border);
        border-radius: 6px;
        padding: 10px 12px;
        min-height: 90px;
        background-color: var(--cfcd-bg);
    }

    .cfcd-card-info--alt {
        background-color: var(--cfcd-surface);
    }

    .cfcd-card-title {
        font-size: 7.5pt;
        font-weight: 800;
        color: var(--cfcd-muted);
        letter-spacing: .5px;
        margin-bottom: 6px;
        border-bottom: 1px solid var(--cfcd-border);
        padding-bottom: 3px;
        text-transform: uppercase;
    }

    .cfcd-card-title--pago {
        color: var(--cfcd-brand-sub);
    }

    .cfcd-label-muted {
        color: var(--cfcd-muted);
    }

    .cfcd-proveedor-nombre {
        color: var(--cfcd-brand);
        font-size: 9.5pt;
    }

    .cfcd-usuario-nombre {
        color: var(--cfcd-text);
        font-size: 8pt;
    }

    .cfcd-pago-info {
        line-height: 1.4;
        color: var(--cfcd-muted);
        font-size: 8.5pt;
    }

    .cfcd-items-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 15px;
        font-size: 8.5pt;
    }

    .cfcd-items-table th {
        background-color: var(--cfcd-header-bg);
        color: var(--cfcd-header-text);
        font-weight: 600;
        font-size: 7.5pt;
        text-transform: uppercase;
        padding: 7px 8px;
        letter-spacing: .5px;
    }

    .cfcd-items-table td {
        padding: 8px;
        border-bottom: 1px solid var(--cfcd-border);
        vertical-align: top;
        color: var(--cfcd-text);
    }

    .cfcd-items-table tbody tr:nth-child(even) {
        background-color: var(--cfcd-surface);
    }

    .cfcd-cell-metric {
        background-color: var(--cfcd-surface);
    }

    .cfcd-sku {
        font-family: monospace;
        color: var(--cfcd-muted);
    }

    .cfcd-prod-nombre {
        color: var(--cfcd-text-strong);
    }

    .cfcd-conversion {
        color: var(--cfcd-brand-sub);
        font-size: .75rem;
    }

    .cfcd-rastreo-item {
        background-color: var(--cfcd-rastreo-bg);
        color: var(--cfcd-text);
        font-size: .7rem;
    }

    .cfcd-subtotal-val {
        color: var(--cfcd-brand);
    }

    .cfcd-items-table .cfcd-total-row td {
        border-bottom: none;
        padding-top: 12px;
        background-color: transparent !important;
    }

    .cfcd-total-label {
        color: var(--cfcd-muted);
        font-size: 10pt;
        font-weight: 600;
        vertical-align: middle;
    }

    .cfcd-total-highlight {
        font-size: 13pt;
        font-weight: 800;
        color: var(--cfcd-total-text);
        background-color: var(--cfcd-total-bg);
        padding: 6px 12px !important;
        border-radius: 4px;
        border: 1px solid var(--cfcd-total-border) !important;
    }

    .cfcd-card-obs {
        margin-top: 15px;
        border: 1px dashed var(--cfcd-border-dash);
        border-radius: 6px;
        padding: 10px 12px;
        font-size: 8pt;
        background-color: var(--cfcd-surface);
        color: var(--cfcd-muted);
        line-height: 1.5;
    }

    .cfcd-obs-title {
        font-weight: 700;
        color: var(--cfcd-text-strong);
        margin-bottom: 2px;
        text-transform: uppercase;
        font-size: 7.5pt;
        letter-spacing: .3px;
    }

    .cfcd-obs-text {
        color: var(--cfcd-text);
    }

    .cfcd-signatures {
        margin-top: 30px;
        padding-top: 10px;
        text-align: center;
    }

    .cfcd-signature-line {
        border-top: 2px solid var(--cfcd-border-dash);
        margin: 0 15px;
        padding-top: 5px;
        font-size: 7.5pt;
        font-weight: 700;
        color: var(--cfcd-text);
    }

    @media print {
        body * {
            visibility: hidden;
        }

        #cfcd_seccionImpresion,
        #cfcd_seccionImpresion * {
            visibility: visible !important;
        }

        #cfcd_seccionImpresion {
            --cfcd-bg: #ffffff;
            --cfcd-surface: #f8fafc;
            --cfcd-surface-alt: #f1f5f9;
            --cfcd-rastreo-bg: #ffffff;
            --cfcd-border: #e2e8f0;
            --cfcd-border-dash: #cbd5e1;
            --cfcd-text: #334155;
            --cfcd-text-strong: #0f172a;
            --cfcd-muted: #64748b;
            --cfcd-subtle: #94a3b8;
            --cfcd-brand: #1e3a8a;
            --cfcd-brand-sub: #0284c7;
            --cfcd-header-bg: #0f172a;
            --cfcd-header-text: #ffffff;
            --cfcd-accent: #38bdf8;
            --cfcd-total-bg: #eff6ff;
            --cfcd-total-border: #bfdbfe;
            --cfcd-total-text: #1e3a8a;
            display: block !important;
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }

        .modal-backdrop,
        #cfcd_seccionImpresion .modal-footer {
            display: none !important;
        }
    }
</style>

<script>
    (function (window, document) {
        'use strict';

        /* ================= CONFIGURACIÓN (ajusta si cambia tu entorno) ================= */
        var CFCD = {
            endpoint: '/cfsistem/app/controllers/egresosController.php',
            logoUrl: '/cfsistem/public/assets/logo.ico',
            faviconPng: '/cfsistem/public/assets/logo.png',
            faviconIco: '/cfsistem/public/assets/logo.ico',
            pdfScript: 'https://cdn.jsdelivr.net/npm/html3pdf@0.12.1/dist/html3pdf.bundle.min.js',
            reloadOnClose: true
        };

        /* ================= HELPERS INTERNOS ================= */
        function _esTemaOscuro() {
            return document.documentElement.getAttribute('data-bs-theme') === 'dark';
        }
        function _temaSwal() {
            var oscuro = _esTemaOscuro();
            return {
                background: oscuro ? '#1e293b' : '#ffffff',
                color: oscuro ? '#f8fafc' : '#1e2022'
            };
        }
        function _swal(title, text, icon) {
            if (typeof Swal !== 'undefined') {
                var opts = Object.assign({ title: title, text: text, icon: icon }, _temaSwal());
                Swal.fire(opts);
            } else if (icon === 'error') {
                alert(title + '\n' + text);
            }
            console.log('[cfcd] ' + icon + ': ' + title + ' - ' + text);
        }
        function _loading() {
            if (typeof Swal !== 'undefined') {
                Swal.fire(Object.assign({
                    title: 'Generando PDF…',
                    text: 'Espera un momento…',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    didOpen: function () { Swal.showLoading(); }
                }, _temaSwal()));
            }
        }
        function _closeSwal() { if (typeof Swal !== 'undefined') Swal.close(); }

        var _pdfLibPromise = null;
        function _asegurarLibreriaPDF() {
            if (_pdfLibPromise) return _pdfLibPromise;
            _pdfLibPromise = new Promise(function (resolve, reject) {
                if (typeof html3pdf !== 'undefined' || typeof window.html2pdf !== 'undefined') {
                    return resolve();
                }
                var s = document.createElement('script');
                s.src = CFCD.pdfScript;
                s.onload = function () { resolve(); };
                s.onerror = function () { reject(new Error('No se pudo cargar la librería de PDF')); };
                document.head.appendChild(s);
            });
            return _pdfLibPromise;
        }

        /* ================= CARGA DE DATOS (AJAX) ================= */
        function _cargarDetalle(tipo, id) {
            var url = CFCD.endpoint
                + '?action=obtenerDetalleMovimiento'
                + '&tipo=' + encodeURIComponent(tipo)
                + '&id=' + encodeURIComponent(id);
            return fetch(url, { headers: { 'Accept': 'application/json' } })
                .then(function (r) {
                    if (!r.ok) throw new Error('HTTP ' + r.status);
                    return r.json();
                });
        }

        /* ================= CONSTRUCCIÓN DE HTML DE ITEMS ================= */
        function _buildFilas(items) {
            var html = '';
            items.forEach(function (item) {
                var factor = parseFloat(item.factor_prod || 1) || 1;

                var conversionInfo = '';
                if (factor > 0) {
                    conversionInfo = '<div class="cfcd-conversion fw-bold">'
                        + '(1 ' + item.unidad_reporte + ' = ' + factor + ' ' + item.unidad_medida + ')'
                        + '</div>';
                }

                var detalleMovimientos = '';
                if (item.desglose_movimientos) {
                    detalleMovimientos = '<div class="mt-1 text-uppercase cfcd-label-muted fw-bold" style="font-size:.6rem;">Rastreo de Entradas:</div>';
                    item.desglose_movimientos.split('||').forEach(function (mov) {
                        detalleMovimientos += '<div class="small p-1 mb-1 cfcd-rastreo-item border-start border-success border-3 rounded-end shadow-sm">'
                            + '<i class="bi bi-arrow-right text-success"></i> '
                            + item.producto_id + ' ' + mov + ' ' + item.unidad_medida + '</div>';
                    });
                }

                var unidadc = parseFloat(item.cantidad_pedida || item.cantidad || 0);
                var unidadr = parseFloat(item.cantidad_recibida || 0);
                var totalComprado = (unidadc / factor).toFixed(2);
                var totale = (parseFloat(item.cantidad_excedente || 0) / factor).toFixed(2);
                var totalr = (unidadr / factor).toFixed(2);
                var totalf = (totalComprado - totalr).toFixed(2);

                var excedenteCell = item.cantidad_excedente > 0
                    ? '<span class="d-block fw-bold">' + totale + ' ' + item.unidad_reporte + '</span>'
                    + '<small class="cfcd-label-muted">' + item.cantidad_excedente + ' ' + item.unidad_medida + '</small>'
                    : '0';

                var faltanteCell = item.cantidad_faltante > 0
                    ? '<span class="d-block fw-bold">' + totalf + ' ' + item.unidad_reporte + '</span>'
                    + '<small class="cfcd-label-muted">' + item.cantidad_faltante + ' ' + item.unidad_medida + '</small>'
                    : '0';

                var precioCell = (unidadr >= 1)
                    ? parseFloat(item.precio_unitario * factor).toFixed(2) + ' x ' + item.unidad_reporte
                    : parseFloat(item.precio_unitario).toFixed(2) + ' x ' + item.unidad_medida;

                html += '<tr>'
                    + '<td class="cfcd-sku">' + (item.sku || 'N/A') + '</td>'
                    + '<td><div class="fw-bold cfcd-prod-nombre">' + item.producto_nombre + '</div>'
                    + conversionInfo + detalleMovimientos + '</td>'
                    + '<td class="text-center cfcd-cell-metric"><span class="d-block fw-bold">'
                    + totalComprado + ' ' + item.unidad_reporte + '</span>'
                    + '<small class="cfcd-label-muted">(' + unidadc + ' ' + item.unidad_medida + ')</small></td>'
                    + '<td class="text-center text-success fw-bold cfcd-cell-metric"><span class="d-block fw-bold">'
                    + totalr + ' ' + item.unidad_reporte + '</span>'
                    + '<small class="cfcd-label-muted">(' + unidadr + ' ' + item.unidad_medida + ')</small></td>'
                    + '<td class="text-center text-success fw-bold cfcd-cell-metric">' + excedenteCell + '</td>'
                    + '<td class="text-center cfcd-cell-metric">' + faltanteCell + '</td>'
                    + '<td class="text-end">$' + precioCell + '</td>'
                    + '<td class="text-end fw-bold cfcd-subtotal-val">$'
                    + parseFloat(item.subtotal).toFixed(2) + '</td>'
                    + '</tr>';
            });
            return html;
        }

        /* ================= CONSTRUCCIÓN DE HTML DE DOCUMENTO ================= */
        function _buildDocHTML(c, filasHtml) {
            var estadoClass = (c.estado === 'confirmada' || c.estado === 'pagado') ? 'bg-success' : 'bg-warning text-dark';
            var totalFmt = parseFloat(c.total || 0).toLocaleString('es-MX', { minimumFractionDigits: 2 });

            return ''
                + '<div class="cfcd-invoice-box" id="cfcd_areaCapturaPDF">'
                + '<table class="cfcd-table-layout"><tr>'
                + '<td style="width:32%;">'
                + '<div class="cfcd-logo-container">'
                + '<img src="' + CFCD.logoUrl + '" style="width:38px;height:auto;" alt="Logo">'
                + '<div class="cfcd-brand-title">BODEGA FORTALEZA CENTRO<br>'
                + '<span class="cfcd-brand-sub">INVENTARIOS</span></div>'
                + '</div>'
                + '</td>'
                + '<td style="width:38%;" class="cfcd-company-address">'
                + '<span class="cfcd-company-name">' + (c.almacen_nombre || 'ALMACÉN GENERAL') + '</span><br>'
                + 'Gestión de Inventarios y Egresos<br>'
                + '<span class="cfcd-company-tag">Control de Entradas / Compras</span>'
                + '</td>'
                + '<td style="width:30%;">'
                + '<div class="cfcd-remision-badge">COMPRA<span>' + c.folio + '</span></div>'
                + '<div style="clear:both;"></div>'
                + '<table class="cfcd-date-tile"><tr>'
                + '<td class="cfcd-title-td">Fecha</td>'
                + '<td class="fw-bold">' + (c.fecha_registro || c.fecha_gasto) + '</td>'
                + '</tr></table>'
                + '</td>'
                + '</tr></table>'
                + '<table class="cfcd-table-layout" style="margin-top:4px;"><tr>'
                + '<td style="width:65%;padding-right:6px;">'
                + '<div class="cfcd-card-info">'
                + '<div class="cfcd-card-title">PROVEEDOR</div>'
                + '<table style="width:100%;border-collapse:collapse;font-size:8.5pt;">'
                + '<tr><td class="cfcd-label-muted" style="width:22%;"><strong>Nombre:</strong></td>'
                + '<td class="fw-bold cfcd-proveedor-nombre">' + (c.proveedorNombre || 'N/A') + '</td></tr>'
                + '<tr><td class="cfcd-label-muted"><strong>Usuario:</strong></td>'
                + '<td class="cfcd-usuario-nombre">' + c.usuario_nombre + '</td></tr>'
                + '</table>'
                + '</div>'
                + '</td>'
                + '<td style="width:35%;">'
                + '<div class="cfcd-card-info cfcd-card-info--alt">'
                + '<div class="cfcd-card-title cfcd-card-title--pago">INFORMACIÓN DE PAGO</div>'
                + '<div class="cfcd-pago-info">'
                + '<strong>Estado:</strong> <span class="badge ' + estadoClass + '">'
                + String(c.estado || '').toUpperCase() + '</span><br>'
                + '<strong>Método:</strong> ' + (c.metodo_pago || 'N/A')
                + '</div>'
                + '</div>'
                + '</td>'
                + '</tr></table>'
                + '<table class="cfcd-items-table"><thead><tr>'
                + '<th style="width:10%;">SKU</th>'
                + '<th style="width:28%;">DESCRIPCIÓN</th>'
                + '<th class="text-center" style="width:12%;">CANT. COMPRADA</th>'
                + '<th class="text-center" style="width:12%;">RECIBIDO</th>'
                + '<th class="text-center" style="width:12%;">EXCEDENTE</th>'
                + '<th class="text-center" style="width:8%;">PEND.</th>'
                + '<th class="text-end" style="width:9%;">P. UNIT</th>'
                + '<th class="text-end" style="width:9%;">TOTAL</th>'
                + '</tr></thead><tbody>'
                + filasHtml
                + '<tr class="cfcd-total-row">'
                + '<td colspan="6"></td>'
                + '<td class="text-end cfcd-total-label">TOTAL NETO</td>'
                + '<td class="text-end cfcd-total-highlight">$' + totalFmt + '</td>'
                + '</tr>'
                + '</tbody></table>'
                + '<div class="cfcd-card-obs">'
                + '<div class="cfcd-obs-title">Observaciones</div>'
                + (c.observaciones ? '<span class="cfcd-obs-text">' + c.observaciones + '</span>' : 'Sin observaciones registradas.')
                + '</div>'
                + '<div class="row cfcd-signatures">'
                + '<div class="col-4"><div class="cfcd-signature-line">SOLICITADO POR</div></div>'
                + '<div class="col-4"><div class="cfcd-signature-line">ALMACÉN / RECIBO</div></div>'
                + '<div class="col-4"><div class="cfcd-signature-line">AUTORIZACIÓN</div></div>'
                + '</div>'
                + '</div>';
        }

        /* ================= FUNCIÓN PRINCIPAL ================= */
        function cfcd_abrir(tipo, id) {
            if (tipo !== 'compra') return;

            _cargarDetalle(tipo, id)
                .then(function (data) {
                    if (!data || !data.success) {
                        _swal('Error', (data && data.message) || 'No se pudo cargar el detalle', 'error');
                        return;
                    }
                    var filasHtml = _buildFilas(data.items || []);
                    var html = _buildDocHTML(data.cabecera || {}, filasHtml);

                    var cont = document.getElementById('cfcd_contenedorContenido');
                    if (cont) cont.innerHTML = html;

                    var modalEl = document.getElementById('cfcd_modalPrincipal');
                    if (modalEl && typeof bootstrap !== 'undefined') {
                        bootstrap.Modal.getOrCreateInstance(modalEl).show();
                    }
                })
                .catch(function (err) {
                    console.error('[cfcd] error al cargar:', err);
                    _swal('Error', 'No se pudo cargar el detalle de la compra', 'error');
                });
        }

        /* ================= CERRAR ================= */
        function cfcd_cerrar() {
            var modalEl = document.getElementById('cfcd_modalPrincipal');
            if (modalEl && typeof bootstrap !== 'undefined') {
                var inst = bootstrap.Modal.getInstance(modalEl);
                if (inst) inst.hide();
            }

        }

        /* ================= IMPRIMIR ================= */
        function cfcd_imprimir() {
            var elemento = document.getElementById('cfcd_areaCapturaPDF');
            if (!elemento) return;

            var folioEl = elemento.querySelector('.cfcd-remision-badge span');
            var folio = folioEl ? folioEl.innerText.trim() : '000';

            var esMovil =
                /Android|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini|Mobile|Tablet/i.test(navigator.userAgent) ||
                (navigator.maxTouchPoints && navigator.maxTouchPoints > 2 && /MacIntel/.test(navigator.platform));

            if (esMovil) {
                _loading();
                _asegurarLibreriaPDF()
                    .then(function () {
                        var generador = (typeof html3pdf !== 'undefined') ? html3pdf : window.html2pdf;
                        if (typeof generador !== 'function') {
                            throw new Error('Librería PDF no disponible');
                        }
                        return generador()
                            .set({
                                margin: [5, 5, 5, 5],
                                filename: 'Compra_' + folio + '.pdf',
                                image: { type: 'jpeg', quality: 0.95 },
                                html2canvas: { scale: 2, useCORS: true, letterRendering: true, logging: false, scrollY: 0 },
                                jsPDF: { unit: 'mm', format: 'a4', orientation: 'landscape' },
                                pagebreak: { mode: ['css', 'legacy'] }
                            })
                            .from(elemento)
                            .save();
                    })
                    .then(function () {
                        _closeSwal();
                        console.log('[cfcd] PDF generado:', 'Compra_' + folio + '.pdf');
                    })
                    .catch(function (err) {
                        console.error('[cfcd] error PDF:', err);
                        _closeSwal();
                        _swal('Error', 'No se pudo generar el PDF. Intenta de nuevo.', 'error');
                    });
                return;
            }

            var iframe = document.createElement('iframe');
            iframe.setAttribute('aria-hidden', 'true');
            iframe.style.cssText = 'position:fixed;right:0;bottom:0;width:0;height:0;border:0;visibility:hidden;';
            document.body.appendChild(iframe);

            var oscuro = _esTemaOscuro();
            var doc = iframe.contentWindow.document;
            doc.open();
            doc.write(
                '<!DOCTYPE html><html lang="es"' + (oscuro ? ' data-bs-theme="dark"' : '') + '><head>'
                + '<meta charset="UTF-8">'
                + '<meta name="viewport" content="width=device-width, initial-scale=1.0">'
                + '<title>COMPRA - ' + folio + '</title>'
                + '<link rel="icon" type="image/png" href="' + CFCD.faviconPng + '">'
                + '<link rel="shortcut icon" href="' + CFCD.faviconIco + '" type="image/x-icon">'
                + '<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">'
                + '<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">'
                + '<style>'
                + '@page{margin:0;}'
                + 'body{font-family:"Segoe UI",Helvetica,Arial,sans-serif;padding:10px;background:' + (oscuro ? '#0f172a' : '#ffffff') + ';color:' + (oscuro ? '#cbd5e1' : '#334155') + ';'
                + '-webkit-print-color-adjust:exact!important;print-color-adjust:exact!important;}'
                + '.cfcd-invoice-box{width:100%;margin:auto;background:' + (oscuro ? '#1e293b' : '#ffffff') + ';}'
                + '.cfcd-table-layout{width:100%;border-collapse:collapse;margin-bottom:10px;}'
                + '.cfcd-logo-container{display:flex;align-items:center;gap:10px;}'
                + '.cfcd-brand-title{font-size:16pt;font-weight:800;color:' + (oscuro ? '#93c5fd' : '#1e3a8a') + ';line-height:1.1;letter-spacing:-.5px;}'
                + '.cfcd-brand-sub{font-size:10pt;font-weight:600;color:#0284c7;}'
                + '.cfcd-company-address{font-size:8.5pt;color:' + (oscuro ? '#94a3b8' : '#64748b') + ';line-height:1.4;text-align:center;}'
                + '.cfcd-company-name{font-weight:600;color:' + (oscuro ? '#f8fafc' : '#1e293b') + ';}'
                + '.cfcd-company-tag{font-size:7.5pt;color:' + (oscuro ? '#64748b' : '#94a3b8') + ';}'
                + '.cfcd-remision-badge{background:#0f172a!important;color:#fff!important;padding:6px 12px;text-align:center;'
                + 'font-size:8pt;font-weight:700;letter-spacing:1px;border-radius:4px;float:right;min-width:140px;}'
                + '.cfcd-remision-badge span{display:block;font-size:13pt;font-weight:800;color:#38bdf8!important;margin-top:2px;}'
                + '.cfcd-date-tile{width:140px;float:right;margin-top:5px;border-collapse:collapse;font-size:8.5pt;}'
                + '.cfcd-date-tile td{padding:4px 8px;border:1px solid ' + (oscuro ? '#334155' : '#e2e8f0') + ';}'
                + '.cfcd-date-tile .cfcd-title-td{background:' + (oscuro ? '#334155' : '#f1f5f9') + '!important;color:' + (oscuro ? '#cbd5e1' : '#64748b') + ';font-weight:600;}'
                + '.cfcd-card-info{border:1px solid ' + (oscuro ? '#334155' : '#e2e8f0') + ';border-radius:6px;padding:10px 12px;min-height:90px;background:' + (oscuro ? '#1e293b' : '#ffffff') + ';}'
                + '.cfcd-card-info--alt{background:' + (oscuro ? '#0f172a' : '#f8fafc') + '!important;}'
                + '.cfcd-card-title{font-size:7.5pt;font-weight:800;color:' + (oscuro ? '#94a3b8' : '#64748b') + ';letter-spacing:.5px;margin-bottom:6px;'
                + 'border-bottom:1px solid ' + (oscuro ? '#334155' : '#f1f5f9') + ';padding-bottom:3px;text-transform:uppercase;}'
                + '.cfcd-card-title--pago{color:#0284c7;}'
                + '.cfcd-label-muted{color:' + (oscuro ? '#94a3b8' : '#64748b') + ';}'
                + '.cfcd-proveedor-nombre{color:' + (oscuro ? '#93c5fd' : '#1e3a8a') + ';font-size:9.5pt;}'
                + '.cfcd-usuario-nombre{color:' + (oscuro ? '#cbd5e1' : '#475569') + ';font-size:8pt;}'
                + '.cfcd-pago-info{line-height:1.4;color:' + (oscuro ? '#94a3b8' : '#64748b') + ';font-size:8.5pt;}'
                + '.cfcd-items-table{width:100%;border-collapse:collapse;margin-top:15px;font-size:8.5pt;}'
                + '.cfcd-items-table th{background:#0f172a!important;color:#fff!important;font-weight:600;font-size:7.5pt;'
                + 'text-transform:uppercase;padding:7px 8px;letter-spacing:.5px;}'
                + '.cfcd-items-table td{padding:8px;border-bottom:1px solid ' + (oscuro ? '#334155' : '#e2e8f0') + ';vertical-align:top;}'
                + '.cfcd-items-table tbody tr:nth-child(even),.cfcd-cell-metric{background-color:' + (oscuro ? '#0f172a' : '#f8fafc') + '!important;}'
                + '.cfcd-sku{font-family:monospace;color:' + (oscuro ? '#94a3b8' : '#64748b') + ';}'
                + '.cfcd-prod-nombre{color:' + (oscuro ? '#f8fafc' : '#0f172a') + ';}'
                + '.cfcd-conversion{color:#0284c7;font-size:.75rem;}'
                + '.cfcd-rastreo-item{background-color:' + (oscuro ? '#1e293b' : '#f1f5f9') + '!important;font-size:.7rem;}'
                + '.cfcd-subtotal-val{color:' + (oscuro ? '#93c5fd' : '#1e3a8a') + ';}'
                + '.cfcd-items-table .cfcd-total-row td{border-bottom:none;padding-top:12px;background:transparent!important;}'
                + '.cfcd-total-label{color:' + (oscuro ? '#94a3b8' : '#475569') + ';font-size:10pt;font-weight:600;vertical-align:middle;}'
                + '.cfcd-total-highlight{font-size:13pt;font-weight:800;color:' + (oscuro ? '#7dd3fc' : '#1e3a8a') + '!important;background:' + (oscuro ? 'rgba(56,189,248,.12)' : '#eff6ff') + '!important;'
                + 'padding:6px 12px!important;border-radius:4px;border:1px solid ' + (oscuro ? 'rgba(56,189,248,.35)' : '#bfdbfe') + '!important;}'
                + '.cfcd-card-obs{margin-top:15px;border:1px dashed ' + (oscuro ? '#475569' : '#cbd5e1') + ';border-radius:6px;padding:10px 12px;'
                + 'font-size:8pt;background:' + (oscuro ? '#0f172a' : '#f8fafc') + ';color:' + (oscuro ? '#94a3b8' : '#64748b') + ';line-height:1.5;}'
                + '.cfcd-obs-title{font-weight:700;color:' + (oscuro ? '#f8fafc' : '#334155') + ';margin-bottom:2px;text-transform:uppercase;font-size:7.5pt;letter-spacing:.3px;}'
                + '.cfcd-obs-text{color:' + (oscuro ? '#cbd5e1' : '#1e293b') + ';}'
                + '.cfcd-signatures{margin-top:40px;padding-top:10px;text-align:center;}'
                + '.cfcd-signature-line{border-top:2px solid ' + (oscuro ? '#475569' : '#334155') + ';margin:0 15px;padding-top:5px;'
                + 'font-size:7.5pt;font-weight:700;color:' + (oscuro ? '#cbd5e1' : '#334155') + ';}'
                + '</style></head><body>' + elemento.outerHTML + '</body></html>'
            );
            doc.close();

            var win = iframe.contentWindow;
            var limpiado = false;
            function limpiar() {
                if (limpiado) return;
                limpiado = true;
                if (iframe && iframe.parentNode) iframe.parentNode.removeChild(iframe);
            }
            win.onafterprint = limpiar;

            var imgs = Array.prototype.slice.call(iframe.contentDocument.images || []);
            var esperarImgs = Promise.all(imgs.map(function (img) {
                if (img.complete && img.naturalWidth > 0) return Promise.resolve();
                return new Promise(function (res) {
                    img.onload = res; img.onerror = res;
                    setTimeout(res, 2000);
                });
            }));

            Promise.race([esperarImgs, new Promise(function (res) { setTimeout(res, 1500); })])
                .then(function () {
                    requestAnimationFrame(function () {
                        try { win.focus(); win.print(); }
                        catch (e) { console.error('[cfcd] error al imprimir:', e); limpiar(); }
                        setTimeout(limpiar, 2000);
                    });
                });
        }

        /* ================= API PÚBLICA ================= */
        window.cfcd_abrir = cfcd_abrir;
        window.cfcd_imprimir = cfcd_imprimir;
        window.cfcd_cerrar = cfcd_cerrar;
        window.CFCD_CONFIG = CFCD;

    })(window, document);
</script>