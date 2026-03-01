   // GUARDADO EGRESO COMPLETO ACTUALIZADO
  
    // GUARDADO EGRESO COMPLETO ACTUALIZADO
    $('#formEgreso').on('submit', function(e) {
        e.preventDefault();

        // 1. EXTRAER LOS DATOS DE LAS FILAS DINÁMICAMENTE
        let itemsParaEnviar = [];
        let hayFaltantesGlobal = 0;

        $('.item-row').each(function() {
            let row = $(this);
            let p_id = row.find('.select-prod').val() || 0;
            let desc = row.find('.desc').val() || ''; // Para gastos
            let cant = parseFloat(row.find('.cant').val()) || 0;
            let faltante = parseFloat(row.find('.input-faltante').val()) || 0;
            let precio = parseFloat(row.find('.precio_u').val()) || 0;
            let sub = parseFloat(row.find('.subtotal').val()) || 0;
            let dist = row.attr('data-dist') ? JSON.parse(row.attr('data-dist')) : [];

            if(faltante > 0) hayFaltantesGlobal = 1;

            itemsParaEnviar.push({
                producto_id: p_id,
                descripcion: desc,
                cantidad: cant,
                cantidad_faltante: faltante,
                precio: precio,
                subtotal: sub,
                distribucion: dist
            });
        });

        // 2. VALIDACIONES BÁSICAS
        if (itemsParaEnviar.length === 0) {
            return Swal.fire("Error", "Debes agregar al menos una partida", "warning");
        }

        // 3. PREPARAR EL ENVÍO
        var fd = new FormData(this); 
        fd.append('items_json', JSON.stringify(itemsParaEnviar)); 
        fd.append('tiene_faltantes', hayFaltantesGlobal);
        // Calculamos el total final sumando los subtotales para el backend
        let totalFinal = itemsParaEnviar.reduce((acc, item) => acc + item.subtotal, 0);
        fd.append('total_final', totalFinal);

        $.ajax({
            url: '/cfsistem/app/backend/compras/compras.php',
            type: 'POST',
            data: fd,
            contentType: false,
            processData: false,
            dataType: 'json', 
            beforeSend: function() {
                $('#btnGuardarEgreso').prop('disabled', true).text('Procesando...');
            },
            success: function(response) {
                // Aquí ya NO hay JSON.parse, así que no dará error
                if (response.status === 'success') {
                    // Limpiamos ruidos de modales para evitar error de aria-hidden
                    $('.modal').modal('hide'); 
                    
                    Swal.fire({
                        icon: 'success',
                        title: '¡Logrado!',
                        text: response.message
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire("Error", response.message, "error");
                    $('#btnGuardarEgreso').prop('disabled', false).text('GUARDAR REGISTRO');
                }
            },
            error: function(xhr) {
                console.error(xhr.responseText);
                Swal.fire("Error Crítico", "El servidor no respondió correctamente. Revisa la consola.", "error");
                $('#btnGuardarEgreso').prop('disabled', false).text('GUARDAR REGISTRO');
            }
        });
    });
