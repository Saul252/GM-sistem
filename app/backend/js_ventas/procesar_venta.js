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
    // Limpiamos el texto del total por si tiene símbolos o comas
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
            
            // Bloqueo del botón para evitar duplicados
            // Buscamos el botón de finalizar venta dentro del modal
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
                // Intentamos capturar el valor del input físico en el modal por si el listener falló
                const inputEntrega = document.querySelector(`.input-entrega[data-index="${index}"]`);
                
                // Prioridad: 1. Valor del input en el modal, 2. Valor guardado en el objeto, 3. Total vendido
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

            // Debug en consola para que verifiques antes de que se cierre el proceso
            console.log("Datos a enviar:", datos);

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
                    Swal.fire({
                        title: '¡Venta Exitosa!',
                        text: `Folio: ${res.folio || 'N/A'}. Stock actualizado.`,
                        icon: 'success',
                        confirmButtonText: 'Ver Ticket'
                    }).then(() => {
                        window.location.href = "ticket_venta.php?id=" + res.id_venta;
                    });
                } else {
                    Swal.fire('Error', res.message || 'Error desconocido', 'error');
                    if(btnFinalizar) btnFinalizar.disabled = false;
                }
            })
            .catch(err => {
                console.error("Error en Fetch:", err);
                Swal.fire('Error Crítico', 'No se pudo conectar con el servidor.', 'error');
                if(btnFinalizar) btnFinalizar.disabled = false;
            });
        }
    });
}