window.procesarVenta = function() {
    // 1. Validar que haya productos en el carrito global
    if (!window.carrito || window.carrito.length === 0) {
        Swal.fire('Carrito vacío', 'Debes agregar al menos un producto.', 'warning');
        return;
    }

    // 2. Validar Cliente
    const idCliente = document.getElementById('selectCliente').value;
    if (!idCliente) {
        Swal.fire('Falta Cliente', 'Por favor selecciona un cliente para la venta.', 'warning');
        return;
    }

    // 3. Capturar valores de pago y totales del modal
    const totalTexto = document.getElementById('totalFinalModal').innerText.replace(/[$,]/g, '');
    const totalVenta = parseFloat(totalTexto) || 0;
    const montoPagado = parseFloat(document.getElementById('monto_pagar').value) || 0;
    const metodoPago = document.getElementById('metodo_pago').value;
    const observaciones = document.getElementById('obsVenta').value;

    // 4. Confirmación visual
    Swal.fire({
        title: '¿Finalizar Venta?',
        html: `Total: <b>$${totalVenta.toFixed(2)}</b><br>Recibido: <b>$${montoPagado.toFixed(2)}</b>`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, finalizar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            
            const btnFinalizar = document.querySelector('#modalFinalizarVenta .btn-primary');
            if(btnFinalizar) btnFinalizar.disabled = true;
            
            Swal.fire({
                title: 'Procesando...',
                text: 'Guardando venta y actualizando stock...',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });

            // 5. MAPEO DEL CARRITO CON DATOS DE ENTREGA
            const carritoFinal = window.carrito.map((item, index) => {
                const inputEntrega = document.querySelector(`.input-entrega-modal[data-index="${index}"]`);
                let entregado = item.entrega_hoy; 
                if (inputEntrega) {
                    entregado = parseFloat(inputEntrega.value);
                }

                return {
                    producto_id: parseInt(item.producto_id),
                    almacen_id: parseInt(item.almacen_id),
                    cantidad: parseFloat(item.cantidad),
                    entrega_hoy: isNaN(entregado) ? 0 : entregado, 
                    precio_unitario: parseFloat(item.precio_unitario),
                    subtotal: parseFloat(item.subtotal),
                    tipo_precio: item.tipo_precio
                };
            });

            // 6. Preparar objeto de envío
            const datos = {
                id_cliente: parseInt(idCliente),
                descuento: 0,
                monto_pagado: montoPagado,
                metodo_pago: metodoPago,
                total_venta: totalVenta,
                observaciones: observaciones,
                carrito: carritoFinal
            };

            // 7. Envío al servidor
            fetch('/cfsistem/app/backend/ventas/procesar_venta.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(datos)
            })
            .then(res => {
                if (!res.ok) throw new Error('Error en la respuesta del servidor');
                return res.json();
            })
            .then(res => {
              if (res.status === 'success') {
                    // 1. Determinar si es éxito total o parcial para el icono
                    const iconoFinal = res.entregado_total ? 'success' : 'warning';
                    const tituloFinal = res.entregado_total ? '¡Venta Exitosa!' : 'Atención: Entrega Incompleta';

                    Swal.fire({
                        title: tituloFinal,
                        // 2. USAR EL MENSAJE DETALLADO DEL PHP (res.message)
                        html: `
                            <div class="alert ${res.entregado_total ? 'alert-success' : 'alert-warning'} border-0 small shadow-sm text-start">
                                ${res.message}
                            </div>
                            <p class="mb-1">Folio generado: <b>${res.folio}</b></p>
                            <p class="text-muted small">¿Deseas imprimir el ticket ahora?</p>
                        `,
                        icon: iconoFinal,
                        showDenyButton: true,
                        showCancelButton: true,
                        confirmButtonText: '<i class="bi bi-currency-dollar"></i> Con Precios',
                        denyButtonText: '<i class="bi bi-hash"></i> Sin Precios',
                        cancelButtonText: 'Cerrar',
                        confirmButtonColor: '#198754',
                        denyButtonColor: '#0dcaf0',
                    }).then((result) => {
                        let url = '';
                        if (result.isConfirmed) {
                            url = `/cfsistem/app/backend/ventas/ticket_venta.php?id=${res.id_venta}`;
                        } else if (result.isDenied) {
                            url = `/cfsistem/app/backend/ventas/ticket_sin_precio.php?id=${res.id_venta}`;
                        }

                        if (url !== '') {
                            window.open(url, '_blank');
                            location.reload(); 
                        } else {
                            location.reload();
                        }
                    });
                } else {
                    // Error crítico (ej: stock 0 absoluto o error de SQL)
                    Swal.fire('Error al procesar', res.message || 'Error desconocido', 'error');
                    if(btnFinalizar) btnFinalizar.disabled = false;
                }
            })
            .catch(err => {
                console.error("Error en Fetch:", err);
                Swal.fire('Error Crítico', 'No se pudo conectar con el servidor.', 'error');
                const btnFinalizarBtn = document.querySelector('#modalFinalizarVenta .btn-primary');
                if(btnFinalizarBtn) btnFinalizarBtn.disabled = false;
            });
        }
    });
}