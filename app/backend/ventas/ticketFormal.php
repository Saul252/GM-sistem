<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Remisión / Ticket de Venta</title>
    <link rel="icon" type="image/png" href="/cfsistem/public/assets/logo.png">
    <link rel="shortcut icon" href="/cfsistem/public/assets/logo.ico" type="image/x-icon">

    <!-- Librería para generación de PDF en dispositivos móviles -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        /* Configuración de Impresión Media Hoja (A5 Horizontal) */
        @page {
            margin: 6mm 8mm;
        }

        body {
            text-transform: uppercase !important;
            font-family: 'Segoe UI', Inter, Helvetica, Arial, sans-serif;
            color: #1e293b;
            font-size: 9pt;
            line-height: 1.3;
            margin: 0;
            padding: 0;
            background-color: #fff;
        }

        /* Barra de control superior estilizada */
        .no-print {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            padding: 14px;
            text-align: center;
            border-bottom: 2px solid #3b82f6;
            margin-bottom: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .btn-print {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            color: white;
            border: none;
            padding: 10px 28px;
            font-size: 14px;
            font-weight: 600;
            border-radius: 6px;
            cursor: pointer;
            box-shadow: 0 2px 4px rgba(37, 99, 235, 0.3);
            margin: 0 6px;
            transition: transform 0.15s, box-shadow 0.15s;
        }

        .btn-print:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 14px rgba(37, 99, 235, 0.4);
        }

        .btn-email {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            box-shadow: 0 2px 4px rgba(16, 185, 129, 0.3);
        }

        .btn-email:hover {
            box-shadow: 0 6px 14px rgba(16, 185, 129, 0.4);
        }

        .invoice-box {
            max-width: 100%;
            margin: auto;
            position: relative;
        }

        /* Layout Base */
        .table-layout {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 6px;
        }

        .table-layout td {
            vertical-align: top;
        }

        /* Cabecera Estilo Corporativo */
        .logo-container {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .brand-title {
            font-size: 16pt;
            font-weight: 800;
            line-height: 1.1;
            color: #1e3a8a;
            letter-spacing: -0.5px;
        }

        .company-address {
            font-size: 8pt;
            color: #64748b;
            text-align: center;
            padding: 0 10px;
            line-height: 1.4;
        }

        /* Bloque Destacado de Folio / Fecha */
        .remision-badge {
            background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%);
            color: white;
            border-radius: 6px;
            text-align: center;
            padding: 4px;
            font-weight: 700;
            font-size: 9pt;
            width: 150px;
            float: right;
            box-shadow: 0 2px 4px rgba(30, 58, 138, 0.2);
        }

        .remision-badge span {
            display: block;
            font-size: 12pt;
            font-weight: 800;
            margin-top: 2px;
            color: #f8fafc;
        }

        .date-tile {
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            width: 100%;
            border-collapse: separate;
            background-color: #f8fafc;
            overflow: hidden;
            margin-top: 4px;
        }

        .date-tile td {
            padding: 6px;
            font-size: 8.5pt;
            text-align: center;
        }

        .date-tile .title-td {
            font-weight: 700;
            background: #e2e8f0;
            color: #334155;
            width: 30%;
        }

        /* Tarjetas de Información */
        .card-info {
            border: 1px solid #e2e8f0;
            background: #ffffff;
            border-radius: 8px;
            padding: 8px;
            min-height: 72px;
            font-size: 8.5pt;
        }

        .card-title {
            font-weight: 700;
            font-size: 8.5pt;
            color: #1e3a8a;
            margin-bottom: 5px;
            border-bottom: 2px solid #f1f5f9;
            padding-bottom: 3px;
            letter-spacing: 0.5px;
        }

        /* Tabla de Contenido Premium */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6px;
            border-radius: 6px;
            overflow: hidden;
            border: 1px solid #e2e8f0;
        }

        .items-table th {
            background-color: #1e3a8a;
            color: #ffffff;
            font-weight: 600;
            text-align: left;
            padding: 6px 8px;
            font-size: 9pt;
        }

        .items-table td {
            padding: 6px 8px;
            font-size: 8.5pt;
            border-bottom: 1px solid #f1f5f9;
            color: #334155;
        }

        .items-table tr:nth-child(even) td {
            background-color: #f8fafc;
        }

        /* Hilera de Totales */
        .total-row td {
            font-size: 11pt;
            font-weight: 700;
            padding: 8px;
            border-bottom: none;
        }

        .total-highlight {
            background: #f1f5f9;
            color: #1e3a8a;
            border-radius: 4px;
            font-size: 12pt;
            font-weight: 800;
        }

        /* Sección Inferior de Control */
        .card-obs {
            border: 1px solid #e2e8f0;
            background: #fafafa;
            border-radius: 6px;
            padding: 6px 10px;
            font-size: 8pt;
            width: 100%;
            box-sizing: border-box;
            margin-top: 10px;
            color: #475569;
        }

        #cargando {
            text-align: center;
            padding: 40px;
            font-weight: bold;
            font-size: 14px;
            color: #1e3a8a;
        }

        .text-right {
            text-align: right !important;
        }

        .text-center {
            text-align: center !important;
        }

        .bold {
            font-weight: bold;
        }

        @media print {
            .no-print {
                display: none !important;
            }

            .card-info {
                border: 1px solid #cbd5e1;
            }

            .items-table {
                border: 1px solid #cbd5e1;
            }
        }

        /* ============================================
           ESTILOS PERSONALIZADOS SWEETALERT2
           ============================================ */
        .swal-cf-popup {
            font-family: 'Segoe UI', Arial, sans-serif;
            border-radius: 14px !important;
            padding: 24px !important;
        }

        .swal-cf-confirm {
            background: linear-gradient(135deg, #10b981, #059669) !important;
            color: #fff !important;
            border: none !important;
            border-radius: 8px !important;
            padding: 10px 22px !important;
            font-weight: 600 !important;
            font-size: 14px !important;
            margin: 0 6px !important;
            cursor: pointer;
            transition: transform 0.15s, box-shadow 0.15s;
        }

        .swal-cf-confirm:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.4);
        }

        .swal-cf-cancel {
            background: #e5e7eb !important;
            color: #374151 !important;
            border: none !important;
            border-radius: 8px !important;
            padding: 10px 22px !important;
            font-weight: 600 !important;
            font-size: 14px !important;
            margin: 0 6px !important;
            cursor: pointer;
            transition: background 0.15s;
        }

        .swal-cf-cancel:hover {
            background: #d1d5db !important;
        }

        .swal2-title {
            font-size: 20px !important;
            font-weight: 700 !important;
            color: #1e293b !important;
        }

        .swal2-html-container {
            font-size: 14px !important;
        }

        .swal2-input {
            border-radius: 8px !important;
            border: 2px solid #e5e7eb !important;
            font-size: 14px !important;
            padding: 10px 12px !important;
        }

        .swal2-input:focus {
            border-color: #10b981 !important;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.15) !important;
            outline: none !important;
        }
    </style>
</head>

<body>

    <div class="no-print">
        <button class="btn-print" onclick="procesarImpresion()">🖨️ IMPRIMIR REMISIÓN PREMIUM</button>
        <button class="btn-print btn-email" id="btn-enviar-correo" onclick="enviarRemisionPorCorreo()">
            📧 ENVIAR POR CORREO
        </button>
    </div>

    <!-- Indicador de Carga -->
    <div id="cargando">CARGANDO DATOS DE LA REMISIÓN...</div>

    <!-- Contenedor Principal de la Remisión (Elegante) -->
    <div id="contenedor-remision" style="display: none;">
        <div class="invoice-box">

            <table class="table-layout">
                <tr>
                    <td style="width: 32%;">
                        <div class="logo-container">
                            <img src="/cfsistem/public/assets/logo.ico" style="width: 38px; height: auto;" alt="Logo">
                            <div class="brand-title">FORTALEZA<br><span
                                    style="font-size:12pt; font-weight:600; color:#0284c7;">CENTRO</span></div>
                        </div>
                    </td>

                    <td style="width: 38%;" class="company-address">
                        <span style="font-weight: 600; color: #1e293b;" id="almacen-nombre"></span><br>
                        <span id="almacen-direccion"></span><br>
                        <span style="font-size: 7.5pt; color: #94a3b8;">Control de Distribución Interna</span>
                    </td>

                    <td style="width: 30%;">
                        <div class="remision-badge">
                            <span id="label-tipo-documento" style="font-size: 8pt; font-weight: 700;">N° REMISIÓN</span>
                            <span id="ticket-folio"></span>
                        </div>
                        <div style="clear: both;"></div>
                        <table class="date-tile">
                            <tr>
                                <td class="title-td">Fecha</td>
                                <td class="bold" style="color: #334155;" id="ticket-fecha"></td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>

            <table class="table-layout" style="margin-top: 4px;">
                <tr>
                    <td style="width: 70%; padding-right: 6px;">
                        <div class="card-info">
                            <div class="card-title">VENDIDO A</div>
                            <table style="width:100%; border-collapse:collapse; font-size: 8.5pt;">
                                <tr>
                                    <td style="width: 20%; color:#64748b;"><strong>Nombre:</strong></td>
                                    <td class="bold" style="color:#1e3a8a; font-size:9.5pt;" id="ticket-cliente"></td>
                                </tr>
                                <tr>
                                    <td style="color:#64748b;"><strong>Dirección:</strong></td>
                                    <td style="color:#475569; font-size:8pt;" id="ticket-direccion"></td>
                                </tr>
                                <tr>
                                    <td style="color:#64748b;"><strong>Teléfono:</strong></td>
                                    <td style="color:#475569;" id="ticket-telefono"></td>
                                </tr>
                            </table>
                        </div>
                    </td>

                    <td style="width: 30%;">
                        <div class="card-info" style="background-color: #f8fafc;">
                            <div class="card-title" style="color:#0284c7;">Información Reparto</div>
                            <div style="line-height: 1.5; color:#64748b;">
                                <strong>Estado:</strong> <span id="ticket-estado-entrega"></span>
                            </div>
                        </div>
                    </td>
                </tr>
            </table>

            <table class="items-table">
                <thead>
                    <tr id="encabezado-tabla">
                        <th style="width: 12%;">CÓDIGO</th>
                        <th style="width: 15%;">UNIDAD</th>
                        <th style="width: 43%;">DESCRIPCIÓN DEL PRODUCTO</th>
                        <th class="text-right" style="width: 10%;">CANTIDAD</th>
                        <th class="text-right col-precios" style="width: 10%;">PRECIO U.</th>
                        <th class="text-right col-precios" style="width: 10%;">IMPORTE</th>
                    </tr>
                </thead>
                <tbody id="tabla-detalles">
                    <!-- Filas generadas dinámicamente -->
                </tbody>
            </table>

            <div class="card-obs">
                <div
                    style="font-weight: 700; color: #334155; margin-bottom: 2px; text-transform: uppercase; font-size: 7.5pt; letter-spacing: 0.3px;">
                    Validación de Operación</div>
                <strong>Cajero Emisor:</strong> <span id="ticket-vendedor-emisor"></span> &nbsp;|&nbsp;
                <strong>Ejecutivo:</strong> <span id="ticket-vendedor"></span><br>
                <strong>Observaciones:</strong>
                <div style="margin-top: 3px; border-top: 1px solid #e2e8f0; padding-top: 2px;">
                    <span style="color:#1e293b;" id="ticket-notas"></span>
                </div>
            </div>

        </div>
    </div>

    <script>
        // ============================================
        // CONFIGURACIÓN GLOBAL DE SWEETALERT
        // ============================================
        const swalCF = Swal.mixin({
            customClass: {
                popup: 'swal-cf-popup',
                confirmButton: 'swal-cf-confirm',
                cancelButton: 'swal-cf-cancel'
            },
            buttonsStyling: false
        });

        const urlParams = new URLSearchParams(window.location.search);
        const idVenta = urlParams.get('id') || urlParams.get('id_venta') || 0;
        const mostrarPrecios = urlParams.get('precios') !== '0';

        document.addEventListener('DOMContentLoaded', () => {
            if (!idVenta || idVenta === '0') {
                document.getElementById('cargando').innerText = 'ERROR: ID DE VENTA NO PROPORCIONADO EN LA URL.';
                return;
            }
            cargarDatosTicket();
        });

        async function cargarDatosTicket() {
            try {
                const response = await fetch(`/cfsistem/app/controllers/ticketController.php?action=obtenerTicketData&id_venta=${idVenta}`);

                if (!response.ok) {
                    throw new Error(`Error en el servidor (${response.status})`);
                }

                const res = await response.json();

                if (!res.success) {
                    throw new Error(res.message || 'Respuesta no válida del servidor');
                }

                renderizarTicket(res.data);
            } catch (error) {
                document.getElementById('cargando').innerText = 'ERROR: ' + error.message;
            }
        }

        function renderizarTicket(data) {
            const { venta, detalles } = data;

            // Llenado de Cabecera e Información General
            document.getElementById('almacen-nombre').innerText = (venta.nombre_almacen || '').toUpperCase();
            document.getElementById('almacen-direccion').innerText = venta.direccion_almacen || '';
            document.getElementById('label-tipo-documento').innerText = mostrarPrecios ? 'N° REMISIÓN' : 'VALE DE ENTREGA';
            document.getElementById('ticket-folio').innerText = venta.folio || '';
            document.getElementById('ticket-fecha').innerText = formatearFecha(venta.fecha);

            // Datos del Cliente y Reparto
            document.getElementById('ticket-cliente').innerText = (venta.nombre_comercial || '').toUpperCase();
            document.getElementById('ticket-direccion').innerText = (venta.direccion || '').toUpperCase();
            document.getElementById('ticket-telefono').innerText = venta.telefono ? `#${venta.telefono}` : 'N/A';
            document.getElementById('ticket-estado-entrega').innerText = (venta.estado_entrega || 'PENDIENTE').toUpperCase();

            // Datos de Control Inferior
            document.getElementById('ticket-vendedor-emisor').innerText = venta.nombre_vendedor || 'SISTEMA';
            document.getElementById('ticket-vendedor').innerText = venta.vendedor || venta.nombre_vendedor || 'N/A';
            document.getElementById('ticket-notas').innerText = venta.observaciones || 'SIN OBSERVACIONES';

            // Ocultar columnas de precios si viene parametrizado
            if (!mostrarPrecios) {
                document.querySelectorAll('.col-precios').forEach(el => el.style.display = 'none');
            }

            // Renderizado de Detalles del Pedido
            const tbody = document.getElementById('tabla-detalles');
            tbody.innerHTML = '';
            console.log(detalles);
            detalles.forEach(item => {
                const equiv = Math.round(parseFloat(item.odmaEquivalencia) || 1);
                const cantidadReal = Math.round(item.cantidad * equiv);

                const sku = item.sku ? item.sku : ('06020' + item.producto_id);
                const precioUnitario = parseFloat(item.precio_unitario || 0);
                const importe = precioUnitario * cantidadReal;

                let rowHtml = `
                    <tr>
                        <td style="font-family: monospace; color: #64748b; font-size: 9pt;">${sku}</td>
                        <td class="bold" style="color: #475569;">${(item.odmaNombre || '').toUpperCase()}</td>
                        <td class="bold" style="color: #0f172a;">${item.producto_nombre}</td>
                        <td class="text-right bold" style="color: #0f172a;">${cantidadReal.toFixed(4)}</td>
                `;

                if (mostrarPrecios) {
                    rowHtml += `
                        <td class="text-right" style="color: #475569;">$${precioUnitario.toFixed(2)}</td>
                        <td class="text-right bold" style="color: #1e3a8a;">$${importe.toFixed(2)}</td>
                    `;
                }

                rowHtml += `</tr>`;
                tbody.insertAdjacentHTML('beforeend', rowHtml);
            });

            // Fila de Total
            if (mostrarPrecios) {
                const totalVenta = parseFloat(venta.total || venta.subtotal || 0);
                const totalHtml = `
                    <tr class="total-row">
                        <td colspan="4"></td>
                        <td class="text-right" style="color: #475569; font-size: 10pt;">TOTAL MXN</td>
                        <td class="text-right total-highlight">$${totalVenta.toFixed(2)}</td>
                    </tr>
                `;
                tbody.insertAdjacentHTML('beforeend', totalHtml);
            }

            // Muestra del contenedor y ocultamiento del cargando
            document.getElementById('cargando').style.display = 'none';
            document.getElementById('contenedor-remision').style.display = 'block';

            setTimeout(procesarImpresion, 600);
        }

        function formatearFecha(fechaCadena) {
            if (!fechaCadena) return '';
            const fecha = new Date(fechaCadena);
            if (isNaN(fecha.getTime())) return fechaCadena;
            const d = String(fecha.getDate()).padStart(2, '0');
            const m = String(fecha.getMonth() + 1).padStart(2, '0');
            const y = fecha.getFullYear();
            const h = String(fecha.getHours()).padStart(2, '0');
            const min = String(fecha.getMinutes()).padStart(2, '0');
            return `${d}/${m}/${y} ${h}:${min}`;
        }

        function procesarImpresion() {
            const esMovil = /Android|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
            const elemento = document.getElementById('contenedor-remision');
            const folio = document.getElementById('ticket-folio').innerText || idVenta;

            if (esMovil) {
                const opciones = {
                    margin: [8, 8, 8, 8],
                    filename: `Remision_Premium_${folio}.pdf`,
                    image: { type: 'jpeg', quality: 0.98 },
                    html2canvas: { scale: 2, useCORS: true, letterRendering: true },
                    jsPDF: { unit: 'mm', format: 'a5', orientation: 'landscape' }
                };

                const controlBoton = document.querySelector('.no-print');
                if (controlBoton) controlBoton.style.display = 'none';

                html2pdf().set(opciones).from(elemento).save().then(() => {
                    if (controlBoton) controlBoton.style.display = 'block';
                });
            } else {
                window.print();
            }
        }

        // ============================================
        // ============================================
        // ENVIAR REMISIÓN POR CORREO (con SweetAlert)
        // ============================================
        async function enviarRemisionPorCorreo() {
            // Validar que la remisión ya se cargó
            const contenedor = document.getElementById('contenedor-remision');
            if (!contenedor || contenedor.style.display === 'none') {
                swalCF.fire({
                    icon: 'warning',
                    title: 'Remisión no lista',
                    text: 'Espera a que la remisión termine de cargar antes de enviarla.',
                    confirmButtonText: 'Entendido'
                });
                return;
            }

            // Datos de la remisión
            const folio = document.getElementById('ticket-folio').innerText || 'S/N';
            const cliente = document.getElementById('ticket-cliente').innerText || 'Cliente';
            const almacen = document.getElementById('almacen-nombre').innerText || 'CF System';
            const fecha = document.getElementById('ticket-fecha').innerText || '';
            const estado = document.getElementById('ticket-estado-entrega').innerText || '';

            // 📧 Correo por defecto (de pruebas)
            const correoPorDefecto = 'saulenriquealbatapia252@gmail.com';

            // ============================================
            // MODAL ÚNICO: correo + resumen + botón enviar
            // ============================================
            const { value: correoDestino } = await swalCF.fire({
                title: '📧 Enviar remisión por correo',
                html: `
                    <div style="text-align:left; font-size:13px; line-height:1.8; color:#475569; margin-bottom:14px;
                                background:#f8fafc; padding:12px; border-radius:8px; border:1px solid #e2e8f0;">
                        <p style="margin:0 0 4px;">🎫 <strong>Folio:</strong> ${folio}</p>
                        <p style="margin:0 0 4px;">👤 <strong>Cliente:</strong> ${cliente}</p>
                        <p style="margin:0 0 4px;">🏬 <strong>Almacén:</strong> ${almacen}</p>
                        <p style="margin:0 0 4px;">📅 <strong>Fecha:</strong> ${fecha}</p>
                        ${estado ? `<p style="margin:0;">📦 <strong>Estado:</strong> ${estado}</p>` : ''}
                    </div>
                    <input id="swal-correo" class="swal2-input" type="email"
                           placeholder="correo@ejemplo.com"
                           value="${correoPorDefecto}"
                           style="width:90%; font-size:14px; margin:0 auto;">
                `,
                focusConfirm: false,
                showCancelButton: true,
                confirmButtonText: '📨 Enviar',
                cancelButtonText: 'Cancelar',
                didOpen: () => {
                    const input = document.getElementById('swal-correo');
                    input.focus();
                    input.select();
                },
                preConfirm: () => {
                    const valor = document.getElementById('swal-correo').value.trim();
                    if (!valor || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(valor)) {
                        Swal.showValidationMessage('Ingresa un correo válido');
                        return false;
                    }
                    return valor;
                }
            });

            // Si canceló
            if (!correoDestino) return;

            // ============================================
            // LOADER MIENTRAS ENVÍA
            // ============================================
            swalCF.fire({
                title: 'Enviando remisión...',
                html: 'Por favor espera un momento ⏳',
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            try {
                // Preparar título y descripción
                const titulo = `Remisión ${folio} - ${almacen}`;
                const descripcion =
                    `Buenas tardes ${cliente},\n\n` +
                    `Por este medio le enviamos su remisión con folio ${folio} ` +
                    `con fecha ${fecha}.\n\n` +
                    (estado ? `Estado de entrega: ${estado}\n\n` : '') +
                    `Gracias por su preferencia.`;

                // HTML de la remisión envuelto con estilos para PDF
                const htmlRemision = `
                    <!DOCTYPE html>
                    <html lang="es">
                    <head>
                        <meta charset="UTF-8">
                        <style>
                            @page { margin: 6mm 8mm; }
                            body {
                                text-transform: uppercase !important;
                                font-family: 'Segoe UI', Inter, Helvetica, Arial, sans-serif;
                                color: #1e293b;
                                font-size: 9pt;
                                line-height: 1.3;
                                margin: 0;
                                padding: 0;
                                background-color: #fff;
                            }
                            .invoice-box { max-width: 100%; margin: auto; position: relative; }
                            .table-layout { width: 100%; border-collapse: collapse; margin-bottom: 6px; }
                            .table-layout td { vertical-align: top; }
                            .logo-container { display: flex; align-items: center; gap: 12px; }
                            .brand-title { font-size: 16pt; font-weight: 800; line-height: 1.1; color: #1e3a8a; letter-spacing: -0.5px; }
                            .company-address { font-size: 8pt; color: #64748b; text-align: center; padding: 0 10px; line-height: 1.4; }
                            .remision-badge {
                                background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%);
                                color: white; border-radius: 6px; text-align: center; padding: 4px;
                                font-weight: 700; font-size: 9pt; width: 150px; float: right;
                            }
                            .remision-badge span { display: block; font-size: 12pt; font-weight: 800; margin-top: 2px; color: #f8fafc; }
                            .date-tile {
                                border: 1px solid #e2e8f0; border-radius: 6px; width: 100%;
                                border-collapse: separate; background-color: #f8fafc;
                                overflow: hidden; margin-top: 4px;
                            }
                            .date-tile td { padding: 6px; font-size: 8.5pt; text-align: center; }
                            .date-tile .title-td { font-weight: 700; background: #e2e8f0; color: #334155; width: 30%; }
                            .card-info {
                                border: 1px solid #e2e8f0; background: #ffffff; border-radius: 8px;
                                padding: 8px; min-height: 72px; font-size: 8.5pt;
                            }
                            .card-title {
                                font-weight: 700; font-size: 8.5pt; color: #1e3a8a;
                                margin-bottom: 5px; border-bottom: 2px solid #f1f5f9;
                                padding-bottom: 3px; letter-spacing: 0.5px;
                            }
                            .items-table {
                                width: 100%; border-collapse: collapse; margin-top: 6px;
                                border-radius: 6px; overflow: hidden; border: 1px solid #e2e8f0;
                            }
                            .items-table th {
                                background-color: #1e3a8a; color: #ffffff; font-weight: 600;
                                text-align: left; padding: 6px 8px; font-size: 9pt;
                            }
                            .items-table td {
                                padding: 6px 8px; font-size: 8.5pt;
                                border-bottom: 1px solid #f1f5f9; color: #334155;
                            }
                            .items-table tr:nth-child(even) td { background-color: #f8fafc; }
                            .total-row td { font-size: 11pt; font-weight: 700; padding: 8px; border-bottom: none; }
                            .total-highlight {
                                background: #f1f5f9; color: #1e3a8a;
                                border-radius: 4px; font-size: 12pt; font-weight: 800;
                            }
                            .card-obs {
                                border: 1px solid #e2e8f0; background: #fafafa; border-radius: 6px;
                                padding: 6px 10px; font-size: 8pt; width: 100%;
                                box-sizing: border-box; margin-top: 10px; color: #475569;
                            }
                            .text-right { text-align: right !important; }
                            .text-center { text-align: center !important; }
                            .bold { font-weight: bold; }
                        </style>
                    </head>
                    <body>${contenedor.innerHTML}</body>
                    </html>
                `;

                const resultado = await enviarCorreo({
                    correo: correoDestino,
                    titulo: titulo,
                    descripcion: descripcion,
                    nombreDocumento: `Remision_${folio}.pdf`,
                    htmlDocumento: htmlRemision,
                    urlBackend: '/cfsistem/app/controllers/correoController.php'
                });

                // ============================================
                // ÉXITO
                // ============================================
                swalCF.fire({
                    icon: 'success',
                    title: '¡Remisión enviada!',
                    html: `
                        <div style="text-align:left; font-size:14px; line-height:1.8;">
                            <p>📬 <strong>Destinatario:</strong><br>${correoDestino}</p>
                            <p>📎 <strong>Adjunto:</strong> ${resultado.conAdjunto ? 'Sí (' + folio + '.pdf)' : 'No'}</p>
                            <p style="color:#16a34a; font-weight:600; margin-top:10px;">
                                ${resultado.mensaje}
                            </p>
                        </div>
                    `,
                    confirmButtonText: '👍 Aceptar',
                    timer: 5000,
                    timerProgressBar: true
                });

            } catch (err) {
                // ============================================
                // ERROR
                // ============================================
                swalCF.fire({
                    icon: 'error',
                    title: 'Error al enviar',
                    html: `
                        <p style="color:#555; font-size:14px;">
                            No se pudo enviar la remisión.<br>
                            <strong style="color:#dc2626;">${err.message}</strong>
                        </p>
                    `,
                    confirmButtonText: 'Cerrar'
                });
            }
        }

        // ============================================
        // FUNCIÓN GENÉRICA PARA ENVIAR CORREO
        // ============================================
        async function enviarCorreo({
            correo,
            titulo,
            descripcion,
            nombreDocumento = 'Documento.pdf',
            htmlDocumento = null,
            urlBackend = '/cfsistem/app/controllers/correoController.php',
            remitente = 'CF System'
        }) {
            if (!correo || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(correo)) {
                throw new Error('Correo inválido');
            }
            if (!titulo || !titulo.trim()) {
                throw new Error('El título es obligatorio');
            }
            if (!descripcion || !descripcion.trim()) {
                throw new Error('La descripción es obligatoria');
            }

            let htmlFinal = htmlDocumento;
            if (!htmlFinal) {
                const contenedor = document.getElementById('documento-terminos');
                if (contenedor && contenedor.innerHTML.trim().length > 0) {
                    htmlFinal = contenedor.innerHTML;
                }
            }
            const documentoExiste = typeof htmlFinal === 'string' && htmlFinal.trim().length > 0;

            const cuerpoHtml = `
                <div style="font-family: Arial, sans-serif; color:#333; max-width:600px; margin:auto;">
                    <div style="background:#1e293b; color:#fff; padding:20px; text-align:center; border-radius:8px 8px 0 0;">
                        <h2 style="margin:0;">${escaparHtml(titulo)}</h2>
                    </div>
                    <div style="padding:20px; background:#f8f9fa; border:1px solid #e5e7eb; border-top:none; border-radius:0 0 8px 8px;">
                        <p style="white-space:pre-line; line-height:1.6;">${escaparHtml(descripcion)}</p>
                        ${documentoExiste
                    ? `<p style="margin-top:20px; color:#0d6efd;">
                                   📎 Se adjunta: <strong>${escaparHtml(nombreDocumento)}</strong>
                               </p>`
                    : ''
                }
                        <hr style="margin:25px 0; border:none; border-top:1px solid #ddd;">
                        <p style="font-size:12px; color:#888; text-align:center;">
                            ${escaparHtml(remitente)} &copy; ${new Date().getFullYear()}
                        </p>
                    </div>
                </div>
            `;

            const datos = {
                modo: 'archivos',
                para: correo,
                asunto: titulo,
                contenido: cuerpoHtml,
                adjuntos: documentoExiste
                    ? [{ html: htmlFinal, nombre: nombreDocumento }]
                    : []
            };

            const respuesta = await fetch(urlBackend, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(datos)
            });

            const data = await respuesta.json();

            if (!data.ok) {
                throw new Error(data.error || 'Error al enviar el correo');
            }

            return {
                enviado: true,
                conAdjunto: documentoExiste,
                mensaje: data.mensaje || 'Correo enviado correctamente'
            };
        }

        // Utilidad anti-inyección HTML
        function escaparHtml(texto) {
            return String(texto)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }
    </script>
</body>

</html>