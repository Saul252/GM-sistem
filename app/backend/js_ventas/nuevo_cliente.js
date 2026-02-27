function abrirModalNuevoCliente() {
    new bootstrap.Modal(document.getElementById('modalNuevoCliente')).show();
}
document.getElementById('formNuevoCliente').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const datos = Object.fromEntries(formData.entries());

    Swal.fire({
        title: 'Guardando cliente...',
        allowOutsideClick: false,
        didOpen: () => { Swal.showLoading(); }
    });

    fetch('/cfsistem/app/backend/clientes_js/agregar_cliente..php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(datos)
    })
    .then(res => res.json())
    .then(res => {
        if(res.status === 'success') {
            Swal.fire('¡Éxito!', 'Cliente registrado correctamente', 'success');
            
            // 1. Agregar el nuevo cliente al select y seleccionarlo
            const selectCliente = document.getElementById('selectCliente');
            const option = new Option(datos.nombre_comercial, res.id_cliente, true, true);
            selectCliente.add(option);
            
            // 2. Cerrar modal y limpiar form
            const modal = bootstrap.Modal.getInstance(document.getElementById('modalNuevoCliente'));
            modal.hide();
            this.reset();
        } else {
            Swal.fire('Error', res.message, 'error');
        }
    })
    .catch(err => {
        console.error(err);
        Swal.fire('Error', 'No se pudo registrar el cliente', 'error');
    });
});