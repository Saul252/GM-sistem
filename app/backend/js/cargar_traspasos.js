  function cargarTraspasos() {
    const almacenId = document.getElementById('admin_filtro_almacen')?.value || '';
    const contenedorArribos = document.getElementById('contenedor-arribos');
    const contenedorEnvios = document.getElementById('contenedor-envios');

    // Mostrar un "Cargando..."
    contenedorArribos.innerHTML = '<tr><td colspan="6" class="text-center">Buscando movimientos...</td></tr>';

    fetch(`/cfsistem/app/backend/almacen/obtener_traspasos.php?almacen_id=${almacenId}`)
        .then(response => response.json())
        .then(data => {
            // Llenar Arribos
            contenedorArribos.innerHTML = '';
            if (data.arribos.length === 0) {
                contenedorArribos.innerHTML = '<tr><td colspan="6" class="text-center text-muted">No hay mercancía pendiente de recibir.</td></tr>';
            } else {
                data.arribos.forEach(mov => {
                    contenedorArribos.innerHTML += `
                        <tr>
                            <td>${mov.fecha}</td>
                            <td><small class="fw-bold">${mov.sku}</small><br>${mov.producto}</td>
                            <td class="text-primary fw-bold">${mov.cantidad}</td>
                            <td>${mov.origen}</td>
                            <td>${mov.enviado_por}</td>
                            <td>
                                <button class="btn btn-success btn-sm" onclick="autorizarRecibo(${mov.id})">
                                    <i class="bi bi-check-circle"></i> Recibir
                                </button>
                            </td>
                        </tr>
                    `;
                });
            }

            // Llenar Envíos
            contenedorEnvios.innerHTML = '';
            data.envios.forEach(mov => {
                const badgeClass = mov.estado === 'Completado' ? 'bg-success' : 'bg-warning text-dark';
                contenedorEnvios.innerHTML += `
                    <tr>
                        <td>${mov.fecha}</td>
                        <td>${mov.producto}</td>
                        <td>${mov.cantidad}</td>
                        <td>${mov.destino}</td>
                        <td><span class="badge ${badgeClass}">${mov.estado}</span></td>
                    </tr>
                `;
            });
        });
}
