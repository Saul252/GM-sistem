function abrirModalNuevoCliente() {
    new bootstrap.Modal(document.getElementById('modalNuevoCliente')).show();
}

document.getElementById('formNuevoCliente').addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Usamos FormData directamente para que PHP lo reciba en $_POST correctamente
    const formData = new FormData(this);

    Swal.fire({
        title: 'Guardando cliente...',
        allowOutsideClick: false,
        didOpen: () => { Swal.showLoading(); }
    });

    // Apuntamos a la ruta del nuevo controlador con la acción 'guardar'
    fetch('/cfsistem/app/controllers/clientesController.php?action=guardar', {
        method: 'POST',
        body: formData // Enviamos formData directamente
    })
    .then(res => res.json())
    .then(res => {
        // Ajustamos a 'success' que es lo que devuelve tu nuevo controlador
        if(res.success === true) {
            Swal.fire('¡Éxito!', res.message, 'success');
            
            // 1. Agregar el nuevo cliente al select y seleccionarlo
            // Nota: Si tu controlador no devuelve el ID, podrías necesitar recargar el select o devolverlo en el JSON
            const selectCliente = document.getElementById('selectCliente');
            if (selectCliente) {
                const nombre = formData.get('nombre_comercial');
                const option = new Option(nombre, res.id || '', true, true);
                selectCliente.add(option);
            }
            
            // 2. Cerrar modal y limpiar form
            const modalElement = document.getElementById('modalNuevoCliente');
            const modal = bootstrap.Modal.getInstance(modalElement) || new bootstrap.Modal(modalElement);
            modal.hide();
            this.reset();
            
            // Si tienes una tabla de clientes, podrías llamar a fetchData() aquí
            if (typeof fetchData === 'function') fetchData($('#filtroAlmacen').val() || 0);

        } else {
            Swal.fire('Error', res.message, 'error');
        }
    })
    .catch(err => {
        console.error(err);
        Swal.fire('Error', 'No se pudo conectar con el servidor', 'error');
    });
});