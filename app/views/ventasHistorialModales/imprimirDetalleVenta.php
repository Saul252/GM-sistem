<script>
    /* ═══════════════════════════════════════════════════════════════
   🖨️ IMPRIMIR DETALLE DE VENTA — Compacto profesional (≤1 hoja)
   ═══════════════════════════════════════════════════════════════ */
    function imprimirDetalleVenta() {
        if (!ventaActual || !ventaActual.info) {
            alert('No hay información de venta para imprimir. Abre primero el detalle.');
            return;
        }

        const info = ventaActual.info;
        const productos = ventaActual.productos || [];
        const historial = ventaActual.historial || [];
        const pagos = ventaActual.pagos || [];

        const total = parseFloat(info.total) || 0;
        const pagado = parseFloat(info.total_pagado) || 0;
        const deuda = total - pagado;
        const liquidado = deuda <= 0;

        const fechaImp = new Date().toLocaleString('es-MX', {
            day: '2-digit', month: 'short', year: 'numeric',
            hour: '2-digit', minute: '2-digit'
        });

        const fmt = n => '$' + (parseFloat(n) || 0).toLocaleString('es-MX', {
            minimumFractionDigits: 2, maximumFractionDigits: 2
        });

        // ═══════════════════════════════════════════════════════════
        // PRODUCTOS
        // ═══════════════════════════════════════════════════════════
        const filasProductos = productos.map((p, i) => {
            const cant = parseFloat(p.cantidad) || 0;
            const entregada = parseFloat(p.cantidad_entregada) || 0;
            const pendiente = cant - entregada;
            const factor = parseFloat(p.factor_conversion) || 1;
            const equivalencia = parseFloat(p.equivalencia) || 1;

            let cantVendidaTxt = `${cant} ${p.unidad_medida || ''}`;
            if (factor > 1 && cant >= factor) {
                const um = cant / factor;
                const umStr = Number.isInteger(um) ? um : um.toFixed(2);
                cantVendidaTxt = `${umStr} ${p.unidad_reporte}<span class="alt">(${cant} ${p.unidad_medida})</span>`;
            }

            const entregadaTxt = entregada > 0
                ? (entregada > 1 ? `${entregada} ${p.unidad_reporte}` : `${entregada} ${p.unidad_medida}`)
                : '<span class="dash">—</span>';

            const cantPendiente = pendiente / factor;
            const pen = pendiente / (1 / equivalencia);
            const pendienteTxt = pendiente > 0.001
                ? `<span class="pend">${cantPendiente >= 1 ? cantPendiente.toFixed(2) : pen.toFixed(2)} ${cantPendiente >= 1 ? (p.unidad_reporte || '') : (p.unidad_medida || '')}</span>`
                : '<span class="ok">✓</span>';

            return `
            <tr>
                <td class="idx">${String(i + 1).padStart(2, '0')}</td>
                <td class="prod">
                    <span class="pname">${p.producto}</span>
                    ${factor > 1 && cant >= factor
                    ? `<span class="peq"> · 1 ${p.unidad_reporte}=${factor}${p.unidad_medida}</span>`
                    : ''}
                </td>
                <td class="num">${cantVendidaTxt}</td>
                <td class="num">${entregadaTxt}</td>
                <td class="num">${pendienteTxt}</td>
            </tr>
        `;
        }).join('');

        // ═══════════════════════════════════════════════════════════
        // ENTREGAS
        // ═══════════════════════════════════════════════════════════
        const filasHistorial = historial.length > 0
            ? historial.map(h => {
                const cantH = parseFloat(h.cantidad) || 0;
                const eq = parseFloat(h.equivalencia) || 1;
                const cantConv = cantH / (1 / eq);
                const cantTxt = cantConv >= 1
                    ? `${cantConv.toFixed(2)} ${h.nombre || ''}`
                    : `${cantH} ${h.unidad_medida || ''}`;

                return `
                <tr>
                    <td class="fecha">${h.fecha}</td>
                    <td class="small">${h.usuario_nombre}</td>
                    <td class="prod-name">${h.producto}</td>
                    <td class="num">${cantTxt}</td>
                </tr>
            `;
            }).join('')
            : `<tr><td colspan="4" class="empty">Sin entregas</td></tr>`;

        // ═══════════════════════════════════════════════════════════
        // PAGOS
        // ═══════════════════════════════════════════════════════════
        const filasPagos = pagos.length > 0
            ? pagos.map(p => `
            <tr>
                <td class="fecha">${p.fecha}</td>
                <td class="num amount">${fmt(p.monto)}</td>
                <td class="small">${p.metodo_pago}</td>
                <td class="mono">${p.referencia || '—'}</td>
                <td class="small">${p.usuario_nombre}</td>
            </tr>
        `).join('')
            : `<tr><td colspan="5" class="empty">Sin abonos</td></tr>`;

        // ═══════════════════════════════════════════════════════════
        // HTML
        // ═══════════════════════════════════════════════════════════
        const html = `
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <title>Reporte ${info.folio || ''}</title>
            <link rel="preconnect" href="https://fonts.googleapis.com">
            <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
            <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Fraunces:opsz,wght@9..144,400;9..144,600&display=swap" rel="stylesheet">
            <style>
                @page { size: letter; margin: 8mm 10mm; }
                * { box-sizing: border-box; margin: 0; padding: 0; }

                :root {
                    --ink:    #111;
                    --ink-2:  #2e2e2e;
                    --ink-3:  #5c5c5c;
                    --ink-4:  #8a8a8a;
                    --ink-5:  #bdbdbd;
                    --rule:   #dcdcdc;
                    --rule-2: #efefef;
                    --danger: #a12222;
                    --success:#1a5c2e;
                }

                body {
                    font-family: 'Inter', -apple-system, sans-serif;
                    font-size: 7pt;
                    font-weight: 400;
                    color: var(--ink);
                    line-height: 1.25;
                    background: #fff;
                    -webkit-font-smoothing: antialiased;
                    -webkit-print-color-adjust: exact;
                    print-color-adjust: exact;
                    font-feature-settings: 'tnum' 1, 'lnum' 1, 'cv02' 1;
                }

                /* ═════════ MASTHEAD ═════════ */
                .masthead {
                    display: flex;
                    justify-content: space-between;
                    align-items: flex-end;
                    padding-bottom: 5px;
                    margin-bottom: 6px;
                    border-bottom: 1px solid var(--ink);
                }
                .masthead .kicker {
                    font-size: 5.5pt;
                    font-weight: 500;
                    letter-spacing: 0.24em;
                    text-transform: uppercase;
                    color: var(--ink-4);
                    margin-bottom: 2px;
                }
                .masthead h1 {
                    font-family: 'Fraunces', serif;
                    font-size: 12pt;
                    font-weight: 400;
                    letter-spacing: -0.02em;
                    line-height: 1;
                    color: var(--ink);
                    font-variation-settings: 'opsz' 144;
                }
                .masthead h1 em {
                    font-style: italic;
                    color: var(--ink-3);
                }
                .masthead .right {
                    text-align: right;
                    font-size: 6pt;
                    color: var(--ink-3);
                    line-height: 1.3;
                }
                .masthead .right .folio {
                    font-weight: 600;
                    font-size: 7pt;
                    color: var(--ink);
                    display: block;
                    margin-bottom: 1px;
                }
                .masthead .right .date {
                    font-size: 5.5pt;
                    color: var(--ink-4);
                }

                /* ═════════ STATUS (inline, sin bloque) ═════════ */
                .status-inline {
                    font-size: 5.5pt;
                    letter-spacing: 0.18em;
                    text-transform: uppercase;
                    color: var(--ink-4);
                    margin-bottom: 8px;
                    font-weight: 500;
                }
                .status-inline .dot {
                    display: inline-block;
                    width: 3px; height: 3px;
                    border-radius: 50%;
                    background: var(--success);
                    vertical-align: middle;
                    margin-right: 5px;
                    position: relative;
                    top: -1px;
                }
                .status-inline.is-cancel { color: var(--danger); }
                .status-inline.is-cancel .dot { background: var(--danger); }

                /* ═════════ SECCIONES ═════════ */
                .section { margin-bottom: 8px; page-break-inside: avoid; }
                .section-head {
                    display: flex;
                    align-items: baseline;
                    gap: 6px;
                    margin-bottom: 3px;
                    padding-bottom: 2px;
                    border-bottom: 0.5px solid var(--rule);
                }
                .section-head .n {
                    font-family: 'Fraunces', serif;
                    font-size: 8pt;
                    font-style: italic;
                    color: var(--ink-5);
                    line-height: 1;
                }
                .section-head .t {
                    font-size: 5.5pt;
                    font-weight: 600;
                    letter-spacing: 0.22em;
                    text-transform: uppercase;
                    color: var(--ink);
                }

                /* ═════════ DATOS (2 columnas compactas) ═════════ */
                .kv-line {
                    display: flex;
                    flex-wrap: wrap;
                    gap: 4px 14px;
                    font-size: 6.5pt;
                    line-height: 1.4;
                }
                .kv-line .kv {
                    display: inline-flex;
                    gap: 4px;
                    align-items: baseline;
                }
                .kv-line .k {
                    font-size: 5.5pt;
                    font-weight: 500;
                    letter-spacing: 0.14em;
                    text-transform: uppercase;
                    color: var(--ink-4);
                }
                .kv-line .v {
                    font-size: 6.5pt;
                    font-weight: 500;
                    color: var(--ink);
                }
                .kv-line .v.mono {
                    font-feature-settings: 'tnum' 1;
                    letter-spacing: 0.02em;
                }

                /* ═════════ RESUMEN FINANCIERO — inline, no gigante ═════════ */
                .financials {
                    display: grid;
                    grid-template-columns: 1fr 1fr 1fr 1fr;
                    gap: 0 12px;
                    font-size: 6.5pt;
                }
                .fin {
                    padding: 3px 0 2px;
                    border-top: 0.75px solid var(--ink-4);
                }
                .fin .k {
                    font-size: 5.5pt;
                    font-weight: 500;
                    letter-spacing: 0.18em;
                    text-transform: uppercase;
                    color: var(--ink-4);
                    display: block;
                    margin-bottom: 1px;
                }
                .fin .v {
                    font-family: 'Fraunces', serif;
                    font-size: 11pt;
                    font-weight: 400;
                    letter-spacing: -0.02em;
                    line-height: 1;
                    color: var(--ink);
                    font-feature-settings: 'tnum' 1, 'lnum' 1;
                }
                .fin.paid .v { color: var(--success); }
                .fin.due .v { color: var(--danger); }
                .fin.clear .v {
                    font-style: italic;
                    font-size: 8pt;
                    color: var(--success);
                }

                /* ═════════ TABLAS — muy compactas ═════════ */
                table {
                    width: 100%;
                    border-collapse: collapse;
                    font-size: 6.5pt;
                }
                thead th {
                    text-align: left;
                    font-size: 5.5pt;
                    font-weight: 600;
                    letter-spacing: 0.16em;
                    text-transform: uppercase;
                    color: var(--ink-4);
                    padding: 0 0 2px;
                    border-bottom: 0.5px solid var(--ink);
                    white-space: nowrap;
                }
                thead th.num { text-align: right; }
                thead th.idx { width: 16px; }

                tbody td {
                    padding: 2px 0;
                    border-bottom: 0.5px solid var(--rule-2);
                    vertical-align: middle;
                    color: var(--ink-2);
                    font-size: 6.5pt;
                }
                tbody tr:last-child td { border-bottom: 0.5px solid var(--rule); }

                td.idx {
                    width: 16px;
                    font-size: 5.5pt;
                    color: var(--ink-5);
                    font-feature-settings: 'tnum' 1;
                }
                td.num {
                    text-align: right;
                    font-feature-settings: 'tnum' 1;
                    white-space: nowrap;
                }
                td.fecha {
                    color: var(--ink-4);
                    font-size: 6pt;
                    font-feature-settings: 'tnum' 1;
                    white-space: nowrap;
                }
                td.mono {
                    font-feature-settings: 'tnum' 1;
                    font-size: 6pt;
                    color: var(--ink-3);
                }
                td.small { font-size: 6pt; color: var(--ink-3); }
                td.amount {
                    font-weight: 600;
                    color: var(--ink);
                    font-size: 7pt;
                }

                .pname {
                    font-weight: 500;
                    color: var(--ink);
                    font-size: 6.5pt;
                }
                .prod-name {
                    font-weight: 500;
                    color: var(--ink);
                    font-size: 6.5pt;
                }
                .peq {
                    font-size: 5.5pt;
                    color: var(--ink-4);
                    font-weight: 400;
                }
                .alt {
                    display: block;
                    font-size: 5.5pt;
                    color: var(--ink-4);
                }
                .pend { color: var(--danger); font-weight: 500; }
                .ok { color: var(--success); font-weight: 600; }
                .dash { color: var(--ink-5); }
                .empty {
                    text-align: center;
                    padding: 4px 0;
                    color: var(--ink-4);
                    font-style: italic;
                    font-size: 6pt;
                }

                /* ═════════ COLOPHON ═════════ */
                .colophon {
                    margin-top: 10px;
                    padding-top: 4px;
                    border-top: 0.5px solid var(--rule);
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    font-size: 5.5pt;
                    letter-spacing: 0.16em;
                    text-transform: uppercase;
                    color: var(--ink-4);
                }
                .colophon .sig {
                    font-family: 'Fraunces', serif;
                    font-style: italic;
                    font-size: 7pt;
                    color: var(--ink-3);
                    text-transform: none;
                    letter-spacing: 0;
                }

                /* ═════════ BOTÓN ═════════ */
                .print-btn {
                    position: fixed;
                    bottom: 20px; right: 20px;
                    background: var(--ink);
                    color: #fff;
                    border: none;
                    padding: 10px 20px;
                    font-family: 'Inter', sans-serif;
                    font-size: 8pt;
                    font-weight: 600;
                    letter-spacing: 0.2em;
                    text-transform: uppercase;
                    cursor: pointer;
                    border-radius: 2px;
                    box-shadow: 0 6px 20px rgba(0,0,0,.22);
                }
                .print-btn:hover { transform: translateY(-1px); }

                @media print {
                    .print-btn { display: none !important; }
                    .section { page-break-inside: avoid; }
                }

                @media screen {
                    body {
                        padding: 20px 24px;
                        max-width: 800px;
                        margin: 0 auto;
                        background: #f5f5f5;
                    }
                    /* En pantalla simulamos la hoja */
                    .page {
                        background: #fff;
                        padding: 22px 26px;
                        box-shadow: 0 4px 24px rgba(0,0,0,.08);
                        border-radius: 2px;
                    }
                }
                @media print {
                    .page { background: #fff; padding: 0; box-shadow: none; }
                }
            </style>
        </head>
        <body>
        <div class="page">

            <!-- ═══════ MASTHEAD ═══════ -->
            <header class="masthead">
                <div>
                    <div class="kicker">Comprobante Interno</div>
                    <h1>Reporte de <em>Venta</em></h1>
                </div>
                <div class="right">
                    <span class="folio">${info.folio || '—'}</span>
                    <div>Factura ${info.factura || '—'}</div>
                    <div class="date">${fechaImp}</div>
                </div>
            </header>

            <!-- ═══════ STATUS ═══════ -->
            ${info.estado_general === 'cancelada'
                ? `<div class="status-inline is-cancel"><span class="dot"></span>Venta cancelada · ${info.observaciones || 'Sin motivo'}</div>`
                : `<div class="status-inline"><span class="dot"></span>Documento emitido · Estado ${(info.estado_general || 'activo')}</div>`
            }

            <!-- ═══════ 01 · DATOS GENERALES ═══════ -->
            <section class="section">
                <div class="section-head">
                    <span class="n">01</span>
                    <span class="t">Datos Generales</span>
                </div>
                <div class="kv-line">
                    <div class="kv"><span class="k">Cliente</span><span class="v">${info.nombre_comercial || '—'}</span></div>
                    <div class="kv"><span class="k">Vendedor</span><span class="v">${info.vendedor || '—'}</span></div>
                    <div class="kv"><span class="k">Almacén</span><span class="v">${info.almacen || '—'}</span></div>
                    <div class="kv"><span class="k">ID</span><span class="v mono">${info.id || '—'}</span></div>
                    <div class="kv"><span class="k">ID Alm.</span><span class="v mono">${info.almacen_id || '—'}</span></div>
                </div>
            </section>

            <!-- ═══════ 02 · RESUMEN FINANCIERO ═══════ -->
            <section class="section">
                <div class="section-head">
                    <span class="n">02</span>
                    <span class="t">Resumen Financiero</span>
                </div>
                <div class="financials">
                    <div class="fin">
                        <span class="k">Total</span>
                        <span class="v">${fmt(total)}</span>
                    </div>
                    <div class="fin paid">
                        <span class="k">Pagado</span>
                        <span class="v">${fmt(pagado)}</span>
                    </div>
                    <div class="fin ${liquidado ? 'clear' : 'due'}">
                        <span class="k">Saldo</span>
                        <span class="v">${liquidado ? 'Liquidado' : fmt(deuda)}</span>
                    </div>
                    <div class="fin">
                        <span class="k">Estado</span>
                        <span class="v" style="font-size: 8pt;">${liquidado ? 'Al corriente' : 'Pendiente'}</span>
                    </div>
                </div>
            </section>

            <!-- ═══════ 03 · PRODUCTOS ═══════ -->
            <section class="section">
                <div class="section-head">
                    <span class="n">03</span>
                    <span class="t">Productos</span>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th class="idx">#</th>
                            <th>Producto</th>
                            <th class="num">Vendido</th>
                            <th class="num">Entregado</th>
                            <th class="num">Pendiente</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${filasProductos || '<tr><td colspan="5" class="empty">Sin productos</td></tr>'}
                    </tbody>
                </table>
            </section>

            <!-- ═══════ 04 · ENTREGAS ═══════ -->
            <section class="section">
                <div class="section-head">
                    <span class="n">04</span>
                    <span class="t">Historial de Entregas</span>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Usuario</th>
                            <th>Producto</th>
                            <th class="num">Cantidad</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${filasHistorial}
                    </tbody>
                </table>
            </section>

            <!-- ═══════ 05 · PAGOS ═══════ -->
            <section class="section">
                <div class="section-head">
                    <span class="n">05</span>
                    <span class="t">Historial de Pagos</span>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th class="num">Monto</th>
                            <th>Método</th>
                            <th>Referencia</th>
                            <th>Recibió</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${filasPagos}
                    </tbody>
                </table>
            </section>

            <!-- ═══════ COLOPHON ═══════ -->
            <footer class="colophon">
                <span>Sistema de Almacenes</span>
                <span class="sig">— Fin del documento —</span>
                <span>${info.folio || ''}</span>
            </footer>

        </div>

            <button class="print-btn" onclick="window.print()">
                Imprimir / Guardar PDF
            </button>

        </body>
        </html>
    `;

        const printWindow = window.open('', '_blank', 'width=900,height=760');
        if (!printWindow) {
            alert('Por favor permite las ventanas emergentes para imprimir.');
            return;
        }
        printWindow.document.open();
        printWindow.document.write(html);
        printWindow.document.close();
    }
</script>