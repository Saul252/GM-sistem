function editarProducto(productoId, almacenId) {
        // Primero obtenemos los datos del producto y sus precios en ese almacén
        fetch(`/cfsistem/app/backend/almacen/obtener_producto_individual.php?id=${productoId}&almacen_id=${almacenId}`)
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    const p = data.producto;
                    // Llenar campos
                    document.getElementById('edit_id').value = p.id;
                    document.getElementById('edit_almacen_id').value = almacenId;
                    document.getElementById('edit_nombre_titulo').innerText = p.nombre;
                    document.getElementById('edit_nombre').value = p.nombre;
                    document.getElementById('edit_sku').value = p.sku;
                    document.getElementById('edit_categoria').value = p.categoria_id;
                    document.getElementById('edit_almacen_nombre').innerText = p.almacen_nombre;

                    document.getElementById('edit_p_min').value = p.precio_minorista;
                    document.getElementById('edit_p_may').value = p.precio_mayorista;
                    document.getElementById('edit_p_dist').value = p.precio_distribuidor;
                    document.getElementById('edit_stock').value = p.stock;
                    document.getElementById('edit_s_min').value = p.stock_minimo;

                    new bootstrap.Modal(document.getElementById('modalEditarProducto')).show();
                }
            });
    }

    // Guardar cambios
    document.getElementById('formEditarProducto').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);

        Swal.fire({
            title: '¿Confirmar cambios?',
            text: formData.get('aplicar_global') ?
                "¡OJO! Los precios se cambiarán en TODOS los almacenes." :
                "Se actualizará solo este registro.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, actualizar'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('/cfsistem/app/backend/almacen/actualizar_producto.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.status === 'success') {
                            Swal.fire('¡Actualizado!', data.message, 'success').then(() => location
                                .reload());
                        } else {
                            Swal.fire('Error', data.message, 'error');
                        }
                    });
            }
        });
    });
    