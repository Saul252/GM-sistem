function procesarVenta() {
    if (carrito.length === 0) {
        Swal.fire('Carrito vacío', 'Debes agregar al menos un producto.', 'warning');
        return;
    }

    const idCliente = document.getElementById('selectCliente').value;
    if (!idCliente) {
        Swal.fire('Falta Cliente', 'Por favor selecciona un cliente para la venta.', 'warning');
        return;
    }

    // Confirmación antes de procesar
    Swal.fire({
        title: '¿Finalizar Venta?',
        text: "Se registrará la venta y se afectará el stock.",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, finalizar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            
            // Bloquear botón y mostrar loading
            const btnFinalizar = document.querySelector('button[onclick="procesarVenta()"]');
            btnFinalizar.disabled = true;
            
            // Mostrar SweetAlert de carga
            Swal.fire({
                title: 'Procesando...',
                text: 'Guardando datos y actualizando inventario',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });

            const carritoFinal = carrito.map((item, index) => {
                const inputEntrega = document.querySelector(`.input-entrega[data-index="${index}"]`);
                return {
                    producto_id: parseInt(item.producto_id),
                    almacen_id: parseInt(item.almacen_id),
                    cantidad: parseFloat(item.cantidad),
                    entrega_hoy: inputEntrega ? parseFloat(inputEntrega.value) : parseFloat(item.cantidad),
                    precio_unitario: parseFloat(item.precio_unitario),
                    subtotal: parseFloat(item.subtotal),
                    tipo_precio: item.tipo_precio
                };
            });

            const datos = {
                id_cliente: parseInt(idCliente),
                descuento: parseFloat(document.getElementById('descuentoGeneral').value) || 0,
                observaciones: document.getElementById('obsVenta').value,
                carrito: carritoFinal
            };

            fetch('/cfsistem/app/backend/ventas/procesar_venta.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(datos)
            })
            .then(res => res.json())
            .then(res => {
                if (res.status === 'success') {
                    // Alerta de éxito con redirección
                    Swal.fire({
                        title: '¡Venta Exitosa!',
                        text: `Folio generado correctamente. Estado: ${res.estado.toUpperCase()}`,
                        icon: 'success',
                        confirmButtonText: 'Imprimir Ticket'
                    }).then(() => {
                        window.location.href = "ticket_venta.php?id=" + res.id_venta;
                    });
                } else {
                    Swal.fire('Error en la venta', res.message, 'error');
                    btnFinalizar.disabled = false;
                }
            })
            .catch(err => {
                console.error(err);
                Swal.fire('Error Crítico', 'No se pudo conectar con el servidor.', 'error');
                btnFinalizar.disabled = false;
            });
        }
    });
}
