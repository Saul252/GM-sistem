
function filtrarProductosPorOrigen() {
    const origenId = document.getElementById('origen_id').value;
    const selectProd = document.getElementById('traspaso_producto');
    const infoStock = document.getElementById('info_stock');
    
    // Resetear select de productos
    selectProd.innerHTML = '<option value="">Seleccione producto...</option>';
    infoStock.innerText = '';
    
    if (!origenId) {
        selectProd.disabled = true;
        return;
    }

    // Filtrar productos que pertenecen a ese almacén y tienen stock > 0
    const productosDisponibles = productosInventario.filter(p => p.almacen_id == origenId && p.stock > 0);

    if (productosDisponibles.length > 0) {
        productosDisponibles.forEach(p => {
            const option = document.createElement('option');
            option.value = p.id;
            option.text = `${p.sku} - ${p.nombre} (Disponibles: ${p.stock})`;
            option.dataset.max = p.stock; // Guardamos el stock máximo en un atributo data
            selectProd.appendChild(option);
        });
        selectProd.disabled = false;
    } else {
        selectProd.innerHTML = '<option value="">No hay productos con stock aquí</option>';
        selectProd.disabled = true;
    }
}

function actualizarMaximo() {
    const selectProd = document.getElementById('traspaso_producto');
    const inputCant = document.getElementById('cantidad_traspaso');
    const infoStock = document.getElementById('info_stock');
    
    const selectedOption = selectProd.options[selectProd.selectedIndex];
    const maxStock = selectedOption.dataset.max;

    if (maxStock) {
        infoStock.innerText = `Límite máximo de envío: ${maxStock}`;
        inputCant.max = maxStock;
        inputCant.placeholder = `Máx ${maxStock}`;
    }
}

// Validación final antes de enviar
document.getElementById('formTraspaso').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const destino = document.getElementById('destino_id').value;
    const origen = document.getElementById('origen_id').value;

    if(origen === destino) {
        Swal.fire('Error', 'El destino no puede ser igual al origen', 'error');
        return;
    }

    Swal.fire({
        title: '¿Confirmar envío?',
        text: "La mercancía se descontará de tu stock actual.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, enviar'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('/cfsistem/app/backend/almacen/procesar_traspaso.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if(data.status === 'success') {
                    Swal.fire('¡Enviado!', data.message, 'success')
                    .then(() => location.reload());
                } else {
                    Swal.fire('Error', data.message, 'error');
                }
            });
        }
    });
});
    