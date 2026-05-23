  window.finalizarViaje = async function(vehiculoId, folioRuta) {
        if (!confirm(`¿Confirmar llegada de la unidad ${folioRuta}?`)) return;
        try {
            const formData = new FormData();
            formData.append('vehiculo_id', vehiculoId);
            formData.append('viaje_folio', folioRuta);
            const resp = await fetch(`/cfsistem/app/controllers/repartosController.php?action=finalizar_viaje`, { method: 'POST', body: formData });
            const res = await resp.json();
            if (res.success) {
                Swal.fire('Éxito', res.message, 'success');
                cargarMonitorViajes();
                cargarPendientes();
            }
        } catch (e) { console.error(e); }
    };
