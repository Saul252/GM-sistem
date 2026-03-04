function editarProducto(productoId, almacenId) {
    // 1. Limpiamos cualquier rastro anterior
    console.log("Editando ID:", productoId, "Almacén:", almacenId);

    fetch(`/cfsistem/app/backend/almacen/obtener_producto_individual.php?id=${productoId}&almacen_id=${almacenId}`)
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                const p = data.producto;
                console.log("Datos recibidos del servidor:", p); // Revisa esto en F12

                // Función segura para evitar errores de 'null'
                const setVal = (id, val) => {
                    const el = document.getElementById(id);
                    if (el) {
                        if (el.tagName === 'SPAN') el.innerText = val || '';
                        else el.value = val || '';
                    } else {
                        console.warn("No se encontró el ID en el HTML:", id);
                    }
                };

                // Llenado de IDs y Títulos
                setVal('edit_id', p.id);
                setVal('edit_almacen_id', almacenId);
                setVal('edit_nombre_titulo', p.nombre);
                setVal('edit_almacen_nombre', p.almacen_nombre);

                // Datos Generales
                setVal('edit_sku', p.sku);
                setVal('edit_nombre', p.nombre);
                setVal('edit_categoria', p.categoria_id);
                setVal('edit_descripcion', p.descripcion);
                
                // SAT
                setVal('edit_fiscal_clave_prod', p.fiscal_clave_prod);
                setVal('edit_fiscal_clave_unidad', p.fiscal_clave_unidad);
                setVal('edit_impuesto_iva', p.impuesto_iva);

                // Precios
                setVal('edit_p_min', p.precio_minorista);
                setVal('edit_p_may', p.precio_mayorista);
                setVal('edit_p_dist', p.precio_distribuidor);
                
                // --- UNIDADES Y FACTOR (Los que te fallan) ---
                setVal('edit_unidad_reporte', p.unidad_reporte);
                setVal('edit_factor_conversion', p.factor_conversion);
                setVal('edit_unidad_medida', p.unidad_medida);

                // Stock
                setVal('edit_stock', p.stock);
                setVal('edit_s_min', p.stock_minimo);

                // Mostrar Modal
                const modalEl = document.getElementById('modalEditarProducto');
                const myModal = bootstrap.Modal.getOrCreateInstance(modalEl);
                myModal.show();
            } else {
                alert("Error: " + data.message);
            }
        })
        .catch(err => console.error("Error crítico:", err));
}