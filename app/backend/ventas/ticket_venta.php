<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Impresión de Ticket</title>
    <link rel="icon" type="image/png" href="/cfsistem/public/assets/logo.png">
    <link rel="shortcut icon" href="/cfsistem/public/assets/logo.ico" type="image/x-icon">

    <!-- Librería para generación de PDF en dispositivos móviles -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        @page {
            margin: 0;
        }

        body {
            font-family: 'Courier New', Courier, monospace;
            width: 72mm;
            margin: 0 auto;
            padding: 5px;
            color: #000;
            font-size: 12px;
            text-transform: uppercase;
            background-color: #fff;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .bold {
            font-weight: bold;
        }

        .divider {
            border-top: 1px dashed #000;
            margin: 5px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .item-row td {
            padding: 5px 0;
            vertical-align: top;
        }

        .btn-imprimir {
            padding: 12px;
            width: 100%;
            background-color: #000;
            color: #fff;
            border: none;
            font-weight: bold;
            font-size: 14px;
            cursor: pointer;
            border-radius: 4px;
            transition: opacity 0.2s;
        }

        .btn-imprimir:hover {
            opacity: 0.85;
        }

        #cargando {
            text-align: center;
            padding: 20px;
            font-weight: bold;
        }

        @media print {
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>

<body>

    <div class="no-print text-center" style="margin-bottom: 20px; padding: 10px; background-color: #eee;">
        <button class="btn-imprimir" onclick="procesarImpresion()">🖨️ IMPRIMIR TICKET / GENERAR PDF</button>
        <button class="btn-imprimir" style="margin-top:10px; background-color:#0d6efd;"
            onclick="enviarTicketPorCorreo()" id="btn-enviar-correo">
            📧 ENVIAR POR CORREO
        </button>
    </div>

    <!-- Indicador de Carga -->
    <div id="cargando">CARGANDO DATOS DEL TICKET...</div>

    <!-- Contenedor Principal del Ticket -->
    <div id="contenedor-ticket" style="display: none;">
        <div class="text-center">
            <span class="bold" style="font-size: 14px;" id="almacen-nombre"></span><br>
            <span id="almacen-direccion"></span><br>
            <span class="bold" id="ticket-titulo">TICKET DE VENTA</span>
        </div>

        <div class="divider"></div>

        <table>
            <tr>
                <td>FOLIO: <span id="ticket-folio"></span></td>
            </tr>
            <tr>
                <td>FECHA: <span id="ticket-fecha"></span></td>
            </tr>
            <tr>
                <td>CLIENTE: <span id="ticket-cliente"></span></td>
            </tr>
            <tr>
                <td>NOTAS: <span id="ticket-notas"></span></td>
            </tr>
        </table>

        <div class="divider"></div>

        <table>
            <thead>
                <tr>
                    <th align="left">DESC.</th>
                    <th align="right" class="col-subtotal">SUBT.</th>
                </tr>
            </thead>
            <tbody id="tabla-detalles">
                <!-- Marca de Agua -->
                <img src="/cfsistem/public/assets/logo.ico" style="
                        position: fixed;
                        top: 19.5%;
                        left: 50%;
                        transform: translate(-50%, -50%);
                        width: 180px;
                        opacity: 0.08;
                        z-index: -1;
                    ">
            </tbody>
        </table>

        <div class="divider"></div>

        <div id="seccion-totales">
            <table style="font-size: 14px;">
                <tr class="bold">
                    <td align="right">TOTAL:</td>
                    <td align="right" style="width: 60%;" id="ticket-total"></td>
                </tr>
            </table>

            <table style="font-size:14px; width:100%;" id="tabla-pagos">
                <!-- Contenido de pagos generado dinámicamente -->
            </table>
        </div>

        <div style="margin-top: 30px;" class="text-center">
            <br> __________________________
            <br> FIRMA DE RECIBIDO
        </div>

        <div class="text-center" style="margin-top: 15px;">
            <p>Vendedor: <span id="ticket-vendedor"></span></p>
            <p class="bold">¡GRACIAS POR SU COMPRA!</p>
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
            const { venta, detalles, pagos } = data;

            document.getElementById('almacen-nombre').innerText = (venta.nombre_almacen || '').toUpperCase();
            document.getElementById('almacen-direccion').innerText = venta.direccion_almacen || '';
            document.getElementById('ticket-titulo').innerText = mostrarPrecios ? 'TICKET DE VENTA' : 'VALE DE ENTREGA';
            document.getElementById('ticket-folio').innerText = venta.folio || '';
            document.getElementById('ticket-fecha').innerText = formatearFecha(venta.fecha);
            document.getElementById('ticket-cliente').innerText = (venta.nombre_comercial || '').substring(0, 30);
            document.getElementById('ticket-notas').innerText = (venta.observaciones || '').substring(0, 30);
            document.getElementById('ticket-vendedor').innerText = venta.vendedor || venta.nombre_vendedor || '';

            if (!mostrarPrecios) {
                document.querySelectorAll('.col-subtotal').forEach(el => el.style.display = 'none');
                document.getElementById('seccion-totales').style.display = 'none';
            }

            const tbody = document.getElementById('tabla-detalles');
            tbody.innerHTML = '';

            detalles.forEach(item => {
                const cantidadOriginal = parseFloat(item.cantidad) || 0;
                const equiv = 1 / parseFloat(item.odmaEquivalencia) || 0;
                let cantidadReal = cantidadOriginal;

                if (equiv > 0) {
                    cantidadReal = Math.round(cantidadOriginal / equiv);
                }

                let rowHtml = `
                    <tr class="item-row">
                        <td>
                            <div class="bold" style="font-size:13px;">${item.producto_nombre}</div>
                            <div style="margin-top:3px;font-size:12px;">
                                Cantidad: <span class="bold">${cantidadReal} ${item.odmaNombre || ''}</span>
                            </div>
                        </td>
                `;

                if (mostrarPrecios) {
                    rowHtml += `
                        <td align="right" class="bold">
                            $${parseFloat(item.subtotal || 0).toFixed(2)}<br>
                            ( $${parseFloat(item.precio_unitario || 0).toFixed(2)} X ${item.odmaNombre || ''} )
                        </td>
                    `;
                }

                rowHtml += `</tr>`;
                tbody.insertAdjacentHTML('beforeend', rowHtml);
            });

            if (mostrarPrecios) {
                document.getElementById('ticket-total').innerText = `$${parseFloat(venta.total || venta.subtotal || 0).toFixed(2)} (${venta.estado_pago || ''})`;

                const tablaPagos = document.getElementById('tabla-pagos');
                tablaPagos.innerHTML = '';

                pagos.forEach(pago => {
                    let pagoHtml = `
                        <tr><td colspan="4" style="border-top:1px dashed #000; padding-top:6px;"></td></tr>
                        <tr>
                            <td class="bold" style="padding:4px 0;">Método de pago:</td>
                            <td colspan="3" style="padding:4px 0;">${pago.metodo_pago}</td>
                        </tr>
                        <tr>
                            <td class="bold" style="padding:4px 0;">Total pagado:</td>
                            <td colspan="3" style="padding:4px 0;">$${parseFloat(pago.monto || 0).toFixed(2)}</td>
                        </tr>
                    `;

                    if ((pago.metodo_pago || '').toLowerCase() === 'efectivo' && parseFloat(pago.efectivoPagado) > 0) {
                        const efectivo = parseFloat(pago.efectivoPagado);
                        const monto = parseFloat(pago.monto);
                        const cambio = efectivo - monto;

                        pagoHtml += `
                            <tr>
                                <td class="bold" style="padding:4px 0;">Caja:</td>
                                <td colspan="3" style="padding:4px 0;">Caja Rápida</td>
                            </tr>
                            <tr>
                                <td class="bold" style="padding:4px 0;">Efectivo recibido:</td>
                                <td colspan="3" style="padding:4px 0;">$${efectivo.toFixed(2)}</td>
                            </tr>
                            <tr>
                                <td class="bold" style="padding:4px 0;">Cambio:</td>
                                <td colspan="3" style="padding:4px 0;">$${cambio.toFixed(2)}</td>
                            </tr>
                        `;
                    }

                    pagoHtml += `<tr><td colspan="4" style="border-bottom:1px dashed #000; padding-bottom:6px;"></td></tr>`;
                    tablaPagos.insertAdjacentHTML('beforeend', pagoHtml);
                });
            }

            document.getElementById('cargando').style.display = 'none';
            document.getElementById('contenedor-ticket').style.display = 'block';

            setTimeout(procesarImpresion, 500);
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
            const elemento = document.getElementById('contenedor-ticket');

            if (esMovil) {
                const opciones = {
                    margin: [4, 4, 4, 4],
                    filename: `Ticket_${idVenta}.pdf`,
                    image: { type: 'jpeg', quality: 0.98 },
                    html2canvas: { scale: 3, useCORS: true, letterRendering: true },
                    jsPDF: { unit: 'mm', format: [80, 297], orientation: 'portrait' }
                };

                const botonControl = document.querySelector('.no-print');
                if (botonControl) botonControl.style.display = 'none';

                html2pdf().set(opciones).from(elemento).save().then(() => {
                    if (botonControl) botonControl.style.display = 'block';
                });
            } else {
                window.print();
            }
        }

        // ============================================
        // ENVIAR TICKET POR CORREO (con SweetAlert)
        // ============================================
        // ============================================
        // ENVIAR TICKET POR CORREO (con SweetAlert)
        // ============================================
        async function enviarTicketPorCorreo() {
            // Validar que el ticket ya se cargó
            const contenedor = document.getElementById('contenedor-ticket');
            if (!contenedor || contenedor.style.display === 'none') {
                swalCF.fire({
                    icon: 'warning',
                    title: 'Ticket no listo',
                    text: 'Espera a que el ticket termine de cargar antes de enviarlo.',
                    confirmButtonText: 'Entendido'
                });
                return;
            }

            // Datos del ticket
            const folio = document.getElementById('ticket-folio').innerText || 'S/N';
            const cliente = document.getElementById('ticket-cliente').innerText || 'Cliente';
            const total = document.getElementById('ticket-total').innerText || '';
            const fecha = document.getElementById('ticket-fecha').innerText || '';
            const almacen = document.getElementById('almacen-nombre').innerText || 'CF System';

            // 📧 Correo por defecto (de pruebas)
            const correoPorDefecto = 'saulenriquealbatapia252@gmail.com';

            // ============================================
            // MODAL ÚNICO: correo + resumen + botón enviar
            // ============================================
            const { value: correoDestino } = await swalCF.fire({
                title: '📧 Enviar ticket por correo',
                html: `
            <div style="text-align:left; font-size:13px; line-height:1.7; color:#475569; margin-bottom:14px;">
                <p style="margin:0 0 4px;">🎫 <strong>Folio:</strong> ${folio}</p>
                <p style="margin:0 0 4px;">👤 <strong>Cliente:</strong> ${cliente}</p>
                <p style="margin:0 0 4px;">🏬 <strong>Almacén:</strong> ${almacen}</p>
                ${total ? `<p style="margin:0;">💰 <strong>Total:</strong> ${total}</p>` : ''}
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

            // Si canceló el modal
            if (!correoDestino) return;

            // ============================================
            // LOADER MIENTRAS ENVÍA
            // ============================================
            swalCF.fire({
                title: 'Enviando correo...',
                html: 'Por favor espera un momento ⏳',
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            try {
                // Preparar título y descripción
                const titulo = `Ticket ${folio} - ${almacen}`;
                const descripcion =
                    `Buenas tardes ${cliente},\n\n` +
                    `Por este medio le enviamos su ticket de compra con folio ${folio} ` +
                    `con fecha ${fecha}.\n\n` +
                    (total ? `Total: ${total}\n\n` : '') +
                    `Gracias por su preferencia.`;

                // HTML del ticket envuelto con estilos para PDF
                const htmlTicket = `
            <!DOCTYPE html>
            <html lang="es">
            <head>
                <meta charset="UTF-8">
                <style>
                    @page { margin: 0; }
                    body {
                        font-family: 'Courier New', Courier, monospace;
                        width: 72mm;
                        margin: 0 auto;
                        padding: 5px;
                        color: #000;
                        font-size: 12px;
                        text-transform: uppercase;
                        background: #fff;
                    }
                    .text-center { text-align: center; }
                    .text-right  { text-align: right; }
                    .bold        { font-weight: bold; }
                    .divider     { border-top: 1px dashed #000; margin: 5px 0; }
                    table        { width: 100%; border-collapse: collapse; }
                    .item-row td { padding: 5px 0; vertical-align: top; }
                    th           { font-size: 12px; }
                </style>
            </head>
            <body>${contenedor.innerHTML}</body>
            </html>
        `;

                const resultado = await enviarCorreo({
                    correo: correoDestino,
                    titulo: titulo,
                    descripcion: descripcion,
                    nombreDocumento: `Ticket_${folio}.pdf`,
                    htmlDocumento: htmlTicket,
                    urlBackend: '/cfsistem/app/controllers/correoController.php'
                });

                // ============================================
                // ÉXITO
                // ============================================
                swalCF.fire({
                    icon: 'success',
                    title: '¡Correo enviado!',
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
                    No se pudo enviar el correo.<br>
                    <strong style="color:#dc2626;">${err.message}</strong>
                </p>
            `,
                    confirmButtonText: 'Cerrar'
                });
            }
        } // ============================================
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

        function escaparHtml(texto) {
            return String(texto)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }
    </script>

    <!-- Estilos personalizados para SweetAlert2 -->
    <style>
        .swal-cf-popup {
            font-family: 'Segoe UI', Arial, sans-serif;
            border-radius: 14px !important;
            padding: 24px !important;
        }

        .swal-cf-confirm {
            background: linear-gradient(135deg, #0d6efd, #0a58ca) !important;
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
            box-shadow: 0 4px 12px rgba(13, 110, 253, 0.4);
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
            border-color: #0d6efd !important;
            box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.15) !important;
            outline: none !important;
        }
    </style>
</body>

</html>