<div class="modal fade" id="modalImprimirSolicitud" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">

            <!-- HEADER -->
            <div class="modal-header text-white border-0"
                style="background: linear-gradient(135deg, #1f2a37 0%, #334155 100%);">
                <h5 class="fw-bold mb-0">
                    <i class="bi bi-printer me-2"></i>Vista de Impresión
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body p-0">

                <div id="areaImpresion" class="p-3 " style="min-height:650px;font-size:12px;color:#1f2937;">

                    <!-- ENCABEZADO -->
                    <div class="d-flex justify-content-between align-items-start border-bottom pb-2 mb-3">

                        <div>

                            <div class="row g-1">
                                <!-- -->
                                <div class="col-6">
                                    <div class="fw-bold text-uppercase mb-0" style="letter-spacing:.5px;">
                                        BODEGA FORTALEZA CHALCO S.A DE C,V
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="fw-bold text-uppercase text-end mb-0" style="letter-spacing:.5px;">
                                        Solicitud de Compra <spam class="fw-bold text-uppercase mb-0" id="print-folio">

                                        </spam>
                                    </div>

                                </div>
                                <div class="col-6">
                                    <div style="letter-spacing:.5px; font-size:11px;">
                                        <spam class="fw-bold text-uppercase mb-0">RFC:</spam> BFC121126CKA
                                    </div>
                                    <div style="letter-spacing:.5px; font-size:11px;">
                                        <spam class="fw-bold text-uppercase mb-0">DOMICILIO:</spam> EMILIANO ZAPATA
                                        S/N SAN MARTIN CUAUTLALPAN CHALCO ESTADO DE MEXICO <spam
                                            class="fw-bold text-uppercase mb-0"> CP:</spam> 56644
                                    </div>
                                </div>
                                <div class="col-6 text-end">
                                    <div class="fw-bold text-uppercase mb-0" style="letter-spacing:.5px;">
                                        <div class="">

                                            <spam class="text-body-secondary text end" id="print-fecha"></spam>
                                            <input type="hidden" id="dp_correo">
                                        </div>
                                    </div>
                                </div>

                            </div>

                        </div>



                    </div>


                    <!-- DATOS PROVEEDOR -->
                    <div class="border rounded p-2 mb-3">

                        <div class="row g-1">
                            <!-- BODEGA FORTALEZA CHALCO S.A DE C,V RFC:BFC121126CKA DOMICILIO EMILIANO ZAPATA S/N SAN MARTIN CUAUTLALPAN CHALCO ESTADO DE MEXICO CP:56644 -->
                            <div class="col-12">
                                <small style="font-size:11px;"
                                    class="text-dark-secondary text-uppercase">PROVEEDOR</small><br>
                                <span style="font-size:11px;" class="fw-bold text-uppercase"
                                    id="print-proveedor"></span>
                            </div>

                            <div class="col-6">
                                <small style="font-size:11px;"
                                    class="text-dark-secondary text-uppercase">RFC</small><br>
                                <span style="font-size:11px;" class="text-uppercase" id="print-rfc"></span>
                            </div>

                            <div class="col-6">
                                <small style="font-size:11px;"
                                    class="text-dark-secondary text-uppercase">TELÉFONO</small><br>
                                <span style="font-size:11px;" class="text-uppercase" id="print-telefono"></span>
                            </div>

                            <div class="col-8">
                                <small style="font-size:11px;" class="text-dark-secondary text-uppercase">DIRECCIÓN
                                    FISCAL</small><br>
                                <span style="font-size:11px;" class="text-uppercase" id="print-direccion"></span>
                            </div>

                            <div class="col-2">
                                <small style="font-size:11px;" class="text-dark-secondary text-uppercase">TEL.
                                    2</small><br>
                                <span style="font-size:11px;" class="text-uppercase" id="print-telefono2"></span>
                            </div>

                            <div class="col-2">
                                <small style="font-size:11px;"
                                    class="text-dark-secondary text-uppercase">EXT.</small><br>
                                <span style="font-size:11px;" class="text-uppercase" id="print-extencion"></span>
                            </div>

                            <div class="col-8">
                                <small style="font-size: 11px;"
                                    class="text-dark-secondary text-uppercase d-block mb-1">DIRECCIÓN DE
                                    RECOLECCIÓN</small>
                                <span type="text" id="print-direccion-recogida"
                                    class="form-control form-control-sm border-0 bg-transparent p-0 text-dark shadow-none w-100 fw-semibold text-uppercase"
                                    style="font-size: 11px;" readonly></span>
                            </div>

                        </div>

                    </div>


                    <!-- TABLA -->
                    <table class="table table-bordered table-sm mb-3" style="font-size:11px;">

                        <thead class="table-dark">

                            <tr>

                                <th style="width:48%;">DESCRIPCIÓN</th>
                                <th width="14%" class="text-center">CANT.</th>
                                <th width="19%" class="text-end">P. UNIT.</th>
                                <th width="19%" class="text-end">Importe</th>

                            </tr>

                        </thead>

                        <tbody id="print-tabla-cuerpo"></tbody>

                    </table>


                    <!-- TOTAL -->
                    <div class="d-flex justify-content-end mb-4">

                        <table style="width:220px;font-size:13px;">

                            <tr>

                                <td class="text-end fw-bold">
                                    Total:
                                </td>

                                <td class="text-end fw-bold fs-6" id="costo_total">

                                </td>

                            </tr>

                        </table>

                    </div>


                    <!-- FIRMAS -->
                    <div class="mt-5">

                        <div class="row text-center">

                            <div class="col-4">

                                <div style="border-top:1px solid #444;padding-top:6px;">
                                    <small class="text-uppercase fw-semibold">
                                        Solicita
                                    </small>
                                </div>

                            </div>

                            <div class="col-4">

                                <div style="border-top:1px solid #444;padding-top:6px;">
                                    <small class="text-uppercase fw-semibold">
                                        Autoriza
                                    </small>
                                </div>

                            </div>

                            <div class="col-4">

                                <div style="border-top:1px solid #444;padding-top:6px;">
                                    <small class="text-uppercase fw-semibold">
                                        Recibe
                                    </small>
                                </div>

                            </div>

                        </div>

                    </div>

                </div>
            </div>

            <!-- FOOTER -->
            <div class="modal-footer border border-subtle border-0">
                <button class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">
                    Cerrar
                </button>
                <button type="button" class="btn btn-success rounded-pill px-4" onclick="abrirModalEnviar()">
                    📧 Enviar por correo
                </button>

                <button class="btn btn-dark rounded-pill px-4"
                    style="background: linear-gradient(135deg,#1f2a37,#334155); border:0;"
                    onclick="ejecutarImpresion()">
                    Imprimir
                </button>
            </div>

        </div>
    </div>
</div>
<script>
    function prepararImpresion(id) {
        fetch(`/cfsistem/app/controllers/solicitudesCompraController.php?action=obtenerDetalle&id=${id}`)
            .then(async (res) => {
                const text = await res.text(); // 👈 primero ver crudo

                console.log('RAW TEXT RESPONSE:', text);

                try {
                    return JSON.parse(text); // 👈 intento manual
                } catch (e) {
                    console.error('ERROR JSON:', e);
                    throw new Error('Respuesta no es JSON válido');
                }
            })
            .then(res => {

                console.log('RAW RESPONSE OBJ:', res);
                console.log('STATUS:', res.status);
                console.log('DATA:', res.data);
                console.log('COSTO:', res.costo);

                if (res.status !== 'success') return;

                const data = res.data;
                const infoBase = data[0];
                const costo = res.costo;

                console.log('datos');
                console.log(infoBase);
                if (infoBase.ubicacion_destino > 0) {
                    cargarUbicacionParaEditar(infoBase.ubicacion_destino);

                }
                const partesDireccion = [
                    infoBase.dp_direccion,
                    infoBase.dp_numeroInt,   // ojo: es dp_numeroInt, no numeroInt
                    infoBase.dp_colonia,
                    infoBase.dp_ciudad,
                    infoBase.dp_codigo_postal,
                ]
                    .filter(v => v != null && String(v).trim() !== '' && String(v).trim() !== '0')
                    .map(v => String(v).trim());

                const direccionProveedor = partesDireccion.join(', ');


                $('#print-folio').text(` #${id.toString().padStart(5, '0')}`);
                $('#print-fecha').text(`Fecha: ${new Date().toLocaleDateString()}`);
                $('#print-almacen').text(infoBase.almacen_nombre);
                $('#print-proveedor').text(infoBase.proveedor_nombre || 'No especificado');
                $('#print-direccion').text(direccionProveedor || 'No especificado');
                $('#print-rfc').text(infoBase.dp_rfc || 'No especificado');
                $('#print-telefono').text(infoBase.dp_telefono || 'No especificado');
                $('#print-direccion-recoleccion').text(infoBase.ubicacion_destino || 'No especificado');
                $('#dp_correo').val(infoBase.dp_correo);

                $('#costo_total').text(costo.toLocaleString('es-MX', { style: 'currency', currency: 'MXN' }) || 'nohay costo');

                let html = '';

                data.forEach(i => {

                    const factor = parseFloat(i.factor_conversion) || 1;
                    const uBase = i.unidad_medida || 'pzas';
                    const uRep = i.unidad_reporte || 'Mayoreo';
                    const costo = parseFloat(i.costo) || 0;

                    const cantidad = parseFloat(i.cantidad) || 0;

                    const costoUnitario = cantidad > 0 ? (costo / cantidad) : 0;

                    html += `
                <tr>
                    <td class="fw-bold">${i.producto_nombre}</td>

                    <td class="text-center">
                        ${cantidad} ${uRep}
                    </td>

                    <td class="text-center">
                        ${costoUnitario.toLocaleString('es-MX', { style: 'currency', currency: 'MXN' })}
                    </td>

                    <td class="text-center">
                        ${costo.toLocaleString('es-MX', { style: 'currency', currency: 'MXN' })}
                    </td>
                </tr>`;
                });

                $('#print-tabla-cuerpo').html(html);

                new bootstrap.Modal(document.getElementById('modalImprimirSolicitud')).show();
            })
            .catch(err => {
                console.error('FETCH ERROR:', err);
            });
    }

    /**
     * Imprime el contenido de #areaImpresion usando un iframe oculto.
     * Al terminar de imprimir (o al cancelar), el iframe se elimina automáticamente
     * para que el sistema NO se trabe si el diálogo de impresión queda abierto.
     */
    function ejecutarImpresion() {
        // 1. Obtener el contenido del área de impresión
        const contenido = document.getElementById('areaImpresion').innerHTML;
        const folio = $('#print-folio').text();

        // 2. Crear iframe oculto dentro de la misma página (no abre ventana nueva)
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

        // 3. Escribir el HTML dentro del iframe
        const doc = iframe.contentWindow.document;
        doc.open();
        doc.write(`
        <html>
            <head>
                <title>Imprimir ${folio}</title>
                <link rel="icon" type="image/png" href="/cfsistem/public/assets/logo.png">
                <link rel="shortcut icon" href="/cfsistem/public/assets/logo.ico" type="image/x-icon">
                <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
                <style>
                    body { font-family: 'Inter', sans-serif; padding: 30px; }
                    .table { background-color: #efefef00 !important; }
                    .table-bordered th, .table-bordered td { border: 1px solid #000 !important; }
                    .fw-bold { font-weight: bold !important; }
                    @media print {
                        .no-print { display: none; }
                        @page { margin: 0 !important; }
                    }
                    .firma-linea { border-top: 1px solid #000; margin-top: 50px; text-align: center; padding-top: 5px; font-size: 12px; }
                </style>
            </head>
            <body>
                <img
                    src="/cfsistem/public/assets/logo.ico"
                    style="
                        position: fixed;
                        top: 19.5%;
                        left: 50%;
                        transform: translate(-70%, -70%);
                        width: 180px;
                        opacity: 0.08;
                        z-index: -1;
                    "
                >

                ${contenido}
            </body>
        </html>
    `);
        doc.close();

        const win = iframe.contentWindow;

        // 4. Función de limpieza: elimina el iframe del DOM (se ejecuta UNA sola vez)
        let limpiado = false;
        const limpiar = () => {
            if (limpiado) return;
            limpiado = true;
            if (iframe && iframe.parentNode) {
                iframe.parentNode.removeChild(iframe);
            }

        };

        // 5. Registrar el cierre ANTES de imprimir
        win.onafterprint = limpiar;

        // 6. Esperar un instante a que carguen estilos/imágenes y luego imprimir
        setTimeout(() => {
            try {
                win.focus();
                win.print();
            } catch (e) {
                console.error('Error al imprimir:', e);
                limpiar();
            }

            // 7. Respaldo: si onafterprint no se disparó, limpiar igualmente
            setTimeout(limpiar, 2000);
        }, 500);
    }

</script>