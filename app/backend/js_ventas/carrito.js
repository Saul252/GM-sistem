function agregarProducto(btn) {
    let fila = btn.closest("tr");
    
    // CAPTURA ESTRICTA DEL ID
    // Intenta obtener 'id' o 'productoId'. Asegúrate que en tu HTML diga data-id="..."
    let producto_id = btn.getAttribute("data-id") || btn.dataset.id || btn.dataset.productoId;
    
    if (!producto_id || producto_id === "undefined") {
        console.error("Botón sin ID de producto:", btn);
        Swal.fire('Error de Sistema', 'El botón no tiene un ID de producto válido.', 'error');
        return;
    }

    let nombre = fila.children[1].innerText;
    let cantidad = parseFloat(fila.querySelector(".cantidad").value);
    let selectPrecio = fila.querySelector(".select-precio");
    let precio = parseFloat(selectPrecio.value);
    
    let textoPrecio = selectPrecio.options[selectPrecio.selectedIndex].text.toLowerCase();
    let tipo_precio_final = "minorista"; 

    if (textoPrecio.includes("dist")) tipo_precio_final = "distribuidor";
    else if (textoPrecio.includes("may")) tipo_precio_final = "mayorista";

    let almacen_id = btn.dataset.almacenId;
    let almacen_nombre = btn.dataset.almacen;

    if (isNaN(cantidad) || cantidad <= 0) {
        Swal.fire('Atención', 'Ingresa una cantidad válida', 'warning');
        return;
    }

    carrito.push({
        producto_id: parseInt(producto_id), // Convertimos a entero para la BD
        almacen_id: parseInt(almacen_id),
        almacen_nombre: almacen_nombre,
        nombre: nombre,
        cantidad: cantidad,
        precio_unitario: precio,
        tipo_precio: tipo_precio_final,
        subtotal: cantidad * precio,
        entrega_hoy: cantidad 
    });

    renderCarrito();
    fila.querySelector(".cantidad").value = 1;
}


    function renderCarrito() {

        let tabla = document.querySelector("#tablaCarrito tbody");
        tabla.innerHTML = "";
        let total = 0;

        carrito.forEach((item, index) => {
            total += item.subtotal;

            tabla.innerHTML += `
<tr>
<td>${item.almacen_nombre}</td>
<td>${item.nombre}</td>
<td>${item.cantidad}</td>
<td>$${item.subtotal.toFixed(2)}</td>
<td>
<button class="btn btn-danger btn-sm"
onclick="eliminarProducto(${index})">
<i class="bi bi-x"></i>
</button>
</td>
</tr>`;
        });

        document.getElementById("total").innerText = total.toFixed(2);
    }

    function eliminarProducto(index) {
        carrito.splice(index, 1);
        renderCarrito();
    }
   