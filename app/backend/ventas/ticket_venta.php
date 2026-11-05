<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Impresión de Ticket</title>
    <link rel="icon" type="image/png" href="/cfsistem/public/assets/logo.png">
    <link rel="shortcut icon" href="/cfsistem/public/assets/logo.ico" type="image/x-icon">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
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

        /* 🔥 NOTA CON SALTO DE LÍNEA */
        #ticket-notas {
            display: inline-block;
            white-space: normal !important;
            word-wrap: break-word !important;
            overflow-wrap: break-word !important;
            word-break: break-word !important;
            max-width: 100%;
            line-height: 1.3;
            vertical-align: top;
        }

        /* 🔥 CELDA DE LA NOTA: permitir wrap */
        #contenedor-ticket td {
            white-space: normal;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }

        #contenedor-ticket table {
            table-layout: fixed;
            width: 100%;
        }

        #contenedor-ticket table td {
            word-break: break-word;
            overflow-wrap: break-word;
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

    <div id="cargando">CARGANDO DATOS DEL TICKET...</div>

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
            <table style="font-size: 13px; width:100%;" id="tabla-pagos">
                <!-- TOTAL + PAGOS se generan dinámicamente aquí -->
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
                console.log(res.data);
            } catch (error) {
                document.getElementById('cargando').innerText = 'ERROR: ' + error.message;
            }
        }

        /**
         * Formatea una dirección agregando ":" después de calle, col, ciudad, pueblo.
         * Ejemplo: "calle Av. Juárez 123, col Centro" → "CALLE: AV. JUÁREZ 123, COL: CENTRO"
         */
        function formatearDireccion(direccion) {
            if (!direccion) return '';

            const etiquetas = ['calle', 'col', 'ciudad', 'pueblo'];
            let texto = String(direccion);

            etiquetas.forEach(etq => {
                const regex = new RegExp(`\\b${etq}\\s*:?\\s*`, 'gi');
                texto = texto.replace(regex, `${etq}: `);
            });

            return texto.toUpperCase().trim();
        }

        function renderizarTicket(data) {
            const { venta, detalles, pagos } = data;

            // ============ CABECERA ============
            document.getElementById('almacen-nombre').innerText = (venta.nombre_almacen || '').toUpperCase();
            document.getElementById('almacen-direccion').innerText = venta.direccion_almacen || '';
            document.getElementById('ticket-titulo').innerText = mostrarPrecios ? 'TICKET DE VENTA' : 'VALE DE ENTREGA';
            document.getElementById('ticket-folio').innerText = venta.folio || '';
            document.getElementById('ticket-fecha').innerText = formatearFecha(venta.fecha);
            document.getElementById('ticket-cliente').innerText = (venta.nombre_comercial || '').toUpperCase();
            document.getElementById('ticket-notas').innerText = (venta.observaciones || 'SIN OBSERVACIONES').toUpperCase();
            document.getElementById('ticket-vendedor').innerText = venta.vendedor || venta.nombre_vendedor || '';

            if (!mostrarPrecios) {
                document.querySelectorAll('.col-subtotal').forEach(el => el.style.display = 'none');
                document.getElementById('seccion-totales').style.display = 'none';
            }

            // ============ DETALLES ============
            const tbody = document.getElementById('tabla-detalles');
            tbody.innerHTML = '';

            detalles.forEach(item => {
                console.log(detalles);
                const cantidadOriginal = parseFloat(item.cantidad) || 0;
                const unidadOriginal = parseFloat(item.cantidad) / parseFloat(item.factor) || 0;
                const equivalencia = parseFloat(item.odmaEquivalencia) || 1;
                const division = equivalencia > 0 ? (cantidadOriginal * equivalencia) : cantidadOriginal;
                const esCasiUno = Math.abs(division - 1) <= 0.01;

                let cantidadMostrar;
                let unidadMostrar;

                if (esCasiUno) {
                    cantidadMostrar = 1;
                    unidadMostrar = item.odmaNombre || '';
                } else {
                    cantidadMostrar = division;
                    unidadMostrar = item.odmaNombre || '';
                }

                const cantidadFormateada = Number.isInteger(cantidadMostrar)
                    ? cantidadMostrar
                    : cantidadMostrar.toFixed(2);

                let rowHtml = `
            <tr class="item-row">
                <td>
                    <div class="bold" style="font-size:13px;">${item.producto_nombre}</div>
                    <div style="margin-top:3px;font-size:12px;">
                        Cantidad: <span class="bold">${cantidadFormateada} ${unidadMostrar}</span>
                    </div>
                </td>
        `;

                if (mostrarPrecios) {
                    rowHtml += `
                <td align="right" class="bold">
                    $${parseFloat(item.subtotal || 0).toFixed(2)}<br>
                    ( $${parseFloat(item.precio_unitario || 0).toFixed(2)} X ${unidadMostrar} )
                </td>
            `;
                }

                rowHtml += `</tr>`;
                tbody.insertAdjacentHTML('beforeend', rowHtml);
            });

            // ============ TOTAL + PAGOS EN UNA SOLA TABLA (ALINEADO A LA DERECHA) ============
            if (mostrarPrecios) {
                const totalVenta = parseFloat(venta.total || venta.subtotal || 0);

                const tablaPagos = document.getElementById('tabla-pagos');
                tablaPagos.innerHTML = '';

                // ---------- 1) FILA DE TOTAL ----------
                tablaPagos.insertAdjacentHTML('beforeend', `
            <tr>
                <td class="bold" style="padding:6px 0; font-size:13px; text-align:right;">
                    TOTAL
                </td>
                <td class="bold" style="padding:6px 0; font-size:13px; text-align:right;">
                    $${totalVenta.toFixed(2)}
                </td>
            </tr>
        `);

                const pagosReales = (pagos || []).filter(p => parseFloat(p.monto || 0) > 0);

                if (pagosReales.length === 0) {
                    // ---------- 2A) SIN PAGOS: solo PENDIENTE ----------
                    tablaPagos.insertAdjacentHTML('beforeend', `
                <tr>
                    <td class="bold" style="padding:6px 0; font-size:13px; color:#b91c1c; text-align:right;">
                        PENDIENTE DE PAGO
                    </td>
                    <td class="bold" style="padding:6px 0; font-size:13px; color:#b91c1c; text-align:right;">
                        $${totalVenta.toFixed(2)}
                    </td>
                </tr>
            `);
                } else {
                    // ---------- 2B) CON PAGOS ----------
                    const totalAbonado = pagosReales.reduce((s, p) => s + parseFloat(p.monto || 0), 0);

                    pagosReales.forEach((pago, idx) => {
                        const metodo = (pago.metodo_pago || '').trim();
                        const esEfectivo = metodo.toLowerCase() === 'efectivo';
                        const montoPago = parseFloat(pago.monto || 0);
                        const efectivoRec = parseFloat(pago.efectivoPagado || 0);

                        const etiqueta = pagosReales.length > 1
                            ? `PAGO ${idx + 1}: ${metodo}`
                            : `PAGO: ${metodo}`;

                        // Línea principal del pago
                        tablaPagos.insertAdjacentHTML('beforeend', `
                    <tr>
                        <td class="bold" style="padding:4px 0; font-size:13px; text-align:right;">
                            ${etiqueta.toUpperCase()}
                        </td>
                        <td class="bold" style="padding:4px 0; font-size:13px; text-align:right;">
                            $${montoPago.toFixed(2)}
                        </td>
                    </tr>
                `);

                        // Sub-líneas de efectivo
                        if (esEfectivo && efectivoRec > 0) {
                            const cambio = efectivoRec - totalVenta;

                            if (Math.abs(efectivoRec - montoPago) > 0.001) {
                                tablaPagos.insertAdjacentHTML('beforeend', `
                            <tr>
                                <td class="bold" style="padding:2px 0; font-size:13px;  text-align:right;">
                                    Efectivo recibido
                                </td>
                                <td class="bold" style="padding:2px 0; font-size:13px;  text-align:right;">
                                    $${efectivoRec.toFixed(2)}
                                </td>
                            </tr>
                        `);
                            }

                            if (cambio > 0.001) {
                                tablaPagos.insertAdjacentHTML('beforeend', `
        <tr>
            <td style="padding:2px 0; font-size:14px;  text-align:right; font-weight:bold;">
                Cambio
            </td>
            <td style="padding:2px 0; font-size:14px;  text-align:right; font-weight:bold;">
                $${cambio.toFixed(2)}
            </td>
        </tr>
    `);
                            }
                        }

                        // Referencia
                        if (pago.referencia) {
                            tablaPagos.insertAdjacentHTML('beforeend', `
                        <tr>
                            <td class="bold" style="padding:2px 0; font-size:13px; color:#475569; text-align:right;">
                                Ref.
                            </td>
                            <td class="bold" style="padding:2px 0; font-size:13px; color:#475569; text-align:right;">
                                ${pago.referencia}
                            </td>
                        </tr>
                    `);
                        }
                    });

                    // Saldo pendiente
                    const saldoPendiente = totalVenta - totalAbonado;
                    if (saldoPendiente > 0.001) {
                        tablaPagos.insertAdjacentHTML('beforeend', `
                    <tr>
                        <td class="bold" style="padding:6px 0; font-size:13px; color:#b91c1c; text-align:right; border-top:1px solid #333;">
                            PENDIENTE DE PAGO
                        </td>
                        <td class="bold" style="padding:6px 0; font-size:13px; color:#b91c1c; text-align:right; border-top:1px solid #333;">
                            $${saldoPendiente.toFixed(2)}
                        </td>
                    </tr>
                `);
                    }

                    // TOTAL PAGADO (solo si hay más de un pago)
                    if (pagosReales.length > 1) {
                        tablaPagos.insertAdjacentHTML('beforeend', `
                    <tr>
                        <td class="bold" style="padding:6px 0; font-size:13px; text-align:right; border-top:1px solid #333;">
                            TOTAL PAGADO
                        </td>
                        <td class="bold" style="padding:6px 0; font-size:13px; text-align:right; border-top:1px solid #333;">
                            $${totalAbonado.toFixed(2)}
                        </td>
                    </tr>
                `);
                    }
                }
            }

            // ============ MOSTRAR TICKET ============
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

        async function enviarTicketPorCorreo() {
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

            const folio = document.getElementById('ticket-folio').innerText || 'S/N';
            const cliente = document.getElementById('ticket-cliente').innerText || 'Cliente';
            const totalFila = document.querySelector('#tabla-pagos tr td:nth-child(2)');
            const total = totalFila ? totalFila.innerText : '';
            const fecha = document.getElementById('ticket-fecha').innerText || '';
            const almacen = document.getElementById('almacen-nombre').innerText || 'CF System';

            const correoPorDefecto = 'saulenriquealbatapia252@gmail.com';

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

            if (!correoDestino) return;

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
                const titulo = `Ticket ${folio} - ${almacen}`;
                const descripcion =
                    `Buenas tardes ${cliente},\n\n` +
                    `Por este medio le enviamos su ticket de compra con folio ${folio} ` +
                    `con fecha ${fecha}.\n\n` +
                    (total ? `Total: ${total}\n\n` : '') +
                    `Gracias por su preferencia.`;

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
                    #ticket-notas { white-space: normal; word-break: break-word; }
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
        }

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
</body>

</html>