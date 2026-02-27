/**
 * SISTEMA DE VENTAS - Gestión de Carrito y Entregas
 */

// Inicialización del carrito global
window.carrito = window.carrito || [];

/**
 * 1. AGREGAR PRODUCTO AL CARRITO
 */
window.agregarProducto = function(btn) {
    const fila = btn.closest("tr");
    
    // Captura de datos desde el botón
    const producto_id = parseInt(btn.dataset.productoId || btn.getAttribute("data-producto-id"));
    const almacen_id = parseInt(btn.dataset.almacenId);
    const almacen_nombre = btn.dataset.almacen;

    // Captura de datos desde la fila
    const nombre = fila.cells[1].innerText; 
    const cantidadInput = fila.querySelector(".cantidad");
    const cantidadNueva = parseFloat(cantidadInput.value);
    
    const selectPrecio = fila.querySelector(".select-precio");
    const precioUnitario = parseFloat(selectPrecio.value);
    
    // Determinar tipo de precio (Minorista, Mayorista, Distribuidor)
    const textoPrecio = selectPrecio.options[selectPrecio.selectedIndex].text.toLowerCase();
    let tipo_p = "minorista"; 
    if (textoPrecio.includes("dist")) tipo_p = "distribuidor";
    else if (textoPrecio.includes("may")) tipo_p = "mayorista";

    // Validaciones
    if (!producto_id) return console.error("ID de producto no encontrado.");
    if (isNaN(cantidadNueva) || cantidadNueva <= 0) {
        Swal.fire('Atención', 'Ingresa una cantidad válida', 'warning');
        return;
    }

    // Buscar si ya existe para agrupar
    let itemExistente = window.carrito.find(item => 
        item.producto_id === producto_id && 
        item.almacen_id === almacen_id && 
        item.tipo_precio === tipo_p
    );

    if (itemExistente) {
        itemExistente.cantidad += cantidadNueva;
        // Al aumentar la venta, la entrega por defecto sube al nuevo total
        itemExistente.entrega_hoy = itemExistente.cantidad; 
        itemExistente.subtotal = itemExistente.cantidad * itemExistente.precio_unitario;
    } else {
        window.carrito.push({
            producto_id: producto_id,
            almacen_id: almacen_id,
            almacen_nombre: almacen_nombre,
            nombre: nombre,
            cantidad: cantidadNueva,
            entrega_hoy: cantidadNueva, // <--- Dato clave para el stock
            precio_unitario: precioUnitario,
            tipo_precio: tipo_p,
            subtotal: cantidadNueva * precioUnitario
        });
    }

    window.renderCarrito();
    cantidadInput.value = 1; // Reset del input de la tabla de productos
};

/**
 * 2. RENDERIZAR TABLA PRINCIPAL DEL CARRITO
 */
window.renderCarrito = function() {
    const tablaBody = document.querySelector("#tablaCarrito tbody");
    if (!tablaBody) return;
    
    tablaBody.innerHTML = "";
    let totalAcumulado = 0;

    window.carrito.forEach((item, index) => {
        totalAcumulado += item.subtotal;
        
        const tr = document.createElement("tr");
        tr.innerHTML = `
            <td><small class="text-muted">${item.almacen_nombre}</small></td>
            <td>
                <div class="fw-bold" style="font-size: 0.9rem;">${item.nombre}</div>
                <small class="badge bg-light text-dark border">${item.tipo_precio.toUpperCase()}</small>
            </td>
            <td style="width: 85px;">
                <input type="number" class="form-control form-control-sm text-center input-update-carrito" 
                    data-index="${index}" value="${item.cantidad}" min="1" step="any">
            </td>
            <td class="text-end fw-bold">$${item.subtotal.toFixed(2)}</td>
            <td class="text-center">
                <button type="button" class="btn btn-link text-danger p-0 btn-remove-item" data-index="${index}">
                    <i class="bi bi-x-circle-fill" style="font-size: 1.2rem;"></i>
                </button>
            </td>
        `;
        tablaBody.appendChild(tr);
    });

    // Actualizar totales en la UI
    const totalFinal = totalAcumulado.toFixed(2);
    document.getElementById("total").innerText = totalFinal;

    // Sincronización automática con el Modal de Pago
    const totalModal = document.getElementById("totalFinalModal");
    const inputPago = document.getElementById("monto_pagar");
    
    if (totalModal) totalModal.innerText = totalFinal;
    if (inputPago) {
        inputPago.value = totalFinal;
        inputPago.dispatchEvent(new Event('input'));
    }
};

/**
 * 3. LISTENERS DE EVENTOS (Delegación)
 */

// Clic en eliminar o botones dinámicos
document.addEventListener('click', function(e) {
    const btnDelete = e.target.closest('.btn-remove-item');
    if (btnDelete) {
        const index = btnDelete.dataset.index;
        window.carrito.splice(index, 1);
        window.renderCarrito();
    }
});// Cambios en inputs (Cantidades y Entregas)
document.addEventListener('input', function(e) { // <--- CAMBIADO DE 'change' A 'input'
    
    // A. Cambio de cantidad en la tabla del carrito (Venta total)
    if (e.target.classList.contains('input-update-carrito')) {
        const index = e.target.dataset.index;
        const nuevaCant = parseFloat(e.target.value);
        if (nuevaCant > 0) {
            window.carrito[index].cantidad = nuevaCant;
            window.carrito[index].entrega_hoy = nuevaCant; 
            window.carrito[index].subtotal = nuevaCant * window.carrito[index].precio_unitario;
            window.renderCarrito();
        }
    }

    // B. Cambio de cantidad a ENTREGAR en el MODAL
    if (e.target.classList.contains('input-entrega')) {
        const index = e.target.dataset.index;
        if (!window.carrito[index]) return; // Seguridad

        let valorEntregar = parseFloat(e.target.value);
        const maxVendido = window.carrito[index].cantidad;

        // Validar límites
        if (isNaN(valorEntregar) || valorEntregar < 0) valorEntregar = 0;
        if (valorEntregar > maxVendido) {
            valorEntregar = maxVendido;
            e.target.value = maxVendido;
        }

        // GUARDADO INMEDIATO
        window.carrito[index].entrega_hoy = valorEntregar;
        console.log(`✅ Memoria actualizada: ${window.carrito[index].nombre} -> Entrega: ${valorEntregar}`);
    }
});