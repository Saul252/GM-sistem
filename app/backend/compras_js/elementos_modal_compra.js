function agregarFila() {
    const tipo = $('#tipo_egreso').val();
    let html = '';

    if (tipo === 'compra') {
        // GENERAR LAS OPCIONES DEL SELECT USANDO EL ARRAY DE JAVASCRIPT
        let opcionesProductos = '<option value="">Seleccione...</option>';
        
        // Verificamos que productosBase exista para evitar errores
        if (typeof productosBase !== 'undefined' && productosBase.length > 0) {
            productosBase.forEach(p => {
                opcionesProductos += `<option value="${p.id}">${p.nombre} (${p.sku})</option>`;
            });
        }

        html = `
            <div class="row g-2 mb-1 item-row align-items-end border-bottom pb-2" data-dist='[]'>
                <div class="col-md-1 text-center">
                    <button type="button" class="btn btn-sm btn-outline-success mb-1" onclick="modalProd.show()">+</button>
                </div>
                <div class="col-md-3">
                    <label class="small fw-bold">Producto</label>
                    <select class="form-select form-select-sm select-prod" required>
                        ${opcionesProductos}
                    </select>
                </div>
                <div class="col-md-1">
                    <label class="small fw-bold">Precio Fac.</label>
                    <input type="number" step="0.01" class="form-control form-control-sm precio_u" required>
                </div>
                <div class="col-md-1">
                    <label class="small fw-bold">Cant. Fac.</label>
                    <input type="number" step="0.01" class="form-control form-control-sm cant" required>
                </div>
                <div class="col-md-1 text-center">
                    <label class="small fw-bold text-danger">Faltante</label>
                    <div class="form-check d-flex justify-content-center">
                        <input class="form-check-input check-faltante" type="checkbox">
                    </div>
                </div>
                <div class="col-md-1">
                    <label class="small fw-bold text-danger">¿Cuánto?</label>
                    <input type="number" step="0.01" class="form-control form-control-sm input-faltante" value="0" disabled>
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-outline-secondary btn-sm w-100 btn-abrir-dist">Repartir Real</button>
                </div>
                <div class="col-md-1">
                    <label class="small fw-bold">Subtotal</label>
                    <input type="text" class="form-control form-control-sm subtotal bg-light" readonly value="0.00">
                </div>
                <div class="col-md-1 text-end">
                    <button type="button" class="btn btn-link text-danger p-0 mb-1" onclick="$(this).closest('.row').remove(); calcularTotal();">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
                <input type="hidden" class="cant_real_entrada" value="0">
            </div>`;
    } else {
        html = `
            <div class="row g-2 mb-1 item-row align-items-end">
                <div class="col-md-6">
                    <label class="small fw-bold">Descripción del Gasto</label>
                    <input type="text" class="form-control form-control-sm desc" placeholder="Ej. Pago de Renta" required>
                </div>
                <div class="col-md-2">
                    <label class="small fw-bold">Monto</label>
                    <input type="number" step="0.01" class="form-control form-control-sm precio_u" required>
                </div>
                <input type="hidden" class="cant" value="1">
                <input type="hidden" class="subtotal" value="0.00">
                <div class="col-md-1 text-end">
                    <button type="button" class="btn btn-link text-danger p-0 mb-1" onclick="$(this).closest('.row').remove(); calcularTotal();">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>`;
    }
    $('#contenedorItems').append(html);
}
    // MANEJO DE CHECKBOX FALTANTE
    $(document).on('change', '.check-faltante', function() {
        let row = $(this).closest('.row');
        let input = row.find('.input-faltante');
        if ($(this).is(':checked')) {
            input.prop('disabled', false).focus();
        } else {
            input.prop('disabled', true).val(0);
            calcularSubfila(row);
        }
    });

    // RE-CÁLCULO AL ESCRIBIR EN CUALQUIER INPUT
    $(document).on('input', '.cant, .input-faltante, .precio_u, #total_factura', function() {
        let row = $(this).closest('.row');
        if (row.length) calcularSubfila(row);
        calcularTotal();
    });

    function calcularSubfila(row) {
        let cantFact = parseFloat(row.find('.cant').val()) || 0;
        let faltante = parseFloat(row.find('.input-faltante').val()) || 0;
        let precio = parseFloat(row.find('.precio_u').val()) || 0;

        let cantReal = cantFact - faltante;
        row.find('.cant_real_entrada').val(cantReal);

        // El subtotal siempre es Precio x Cantidad Facturada (lo que se paga)
        let sub = cantFact * precio;
        row.find('.subtotal').val(sub.toFixed(2));

        if (cantReal > 0) {
            row.find('.btn-abrir-dist').text('Repartir ' + cantReal);
        }
    }

    function calcularTotal() {
        let totalFactura = parseFloat($('#total_factura').val()) || 0;
        let sumaDesglose = 0;

        $('.item-row').each(function() {
            sumaDesglose += parseFloat($(this).find('.subtotal').val()) || 0;
        });

        let diferencia = totalFactura - sumaDesglose;
        $('#txtDiferencia').text('$ ' + diferencia.toFixed(2));

        if (Math.abs(diferencia) > 0.01) {
            $('#txtDiferencia').addClass('text-danger').removeClass('text-success');
            $('#alertaMonto').removeClass('d-none');
        } else {
            $('#txtDiferencia').addClass('text-success').removeClass('text-danger');
            $('#alertaMonto').addClass('d-none');
        }
    }

    // LÓGICA DISTRIBUCIÓN (ACTUALIZADA PARA USAR CANTIDAD REAL)
    $(document).on('click', '.btn-abrir-dist', function() {
        filaEnDistribucion = $(this).closest('.row');
        let cantReal = parseFloat(filaEnDistribucion.find('.cant_real_entrada').val()) || 0;
        if (cantReal <= 0) return Swal.fire('Error', 'No hay cantidad física para repartir', 'warning');

        let currentDist = JSON.parse(filaEnDistribucion.attr('data-dist') || '[]');
        let html = `<p class="small mb-2">Repartiendo entrada física de <b>${cantReal}</b> unidades:</p>`;

        almacenes.forEach(a => {
            let obj = currentDist.find(d => d.almacen_id == a.id);
            let val = obj ? obj.cantidad : 0;
            html += `
                <div class="row mb-1 align-items-center">
                    <div class="col-8 small">${a.nombre}</div>
                    <div class="col-4"><input type="number" step="0.01" class="form-control form-control-sm dist-input" data-id="${a.id}" value="${val}"></div>
                </div>`;
        });
        $('#listaAlmacenesDist').html(html);
        modalDist.show();
    });

    $('#btnConfirmarDist').click(function() {
        let cantReal = parseFloat(filaEnDistribucion.find('.cant_real_entrada').val());
        let suma = 0;
        let data = [];
        $('.dist-input').each(function() {
            let v = parseFloat($(this).val()) || 0;
            suma += v;
            if (v > 0) data.push({
                almacen_id: $(this).data('id'),
                cantidad: v
            });
        });

        if (Math.abs(suma - cantReal) > 0.01) return Swal.fire('Error',
            'La suma no coincide con la cantidad real de entrada', 'error');

        filaEnDistribucion.attr('data-dist', JSON.stringify(data));
        filaEnDistribucion.find('.btn-abrir-dist').addClass('btn-success text-white').html(
            '<i class="bi bi-check"></i> Listo');
        modalDist.hide();
    });
