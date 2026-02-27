/**
 * 4. FUNCIÓN PARA ABRIR EL MODAL DE FINALIZACIÓN
 */
window.abrirModalFinalizar = function() {
    // 1. Validar si hay productos
    if (!window.carrito || window.carrito.length === 0) {
        Swal.fire('Carrito vacío', 'Agrega productos antes de finalizar la venta.', 'warning');
        return;
    }

    const tabla = document.getElementById("tablaConfirmacion");
    if (!tabla) return;

    // 2. Limpiar y llenar la tabla del modal
    tabla.innerHTML = "";
    
    window.carrito.forEach((item, index) => {
        // SEGURIDAD: Si entrega_hoy no existe o es nulo, igualarlo a la cantidad vendida
        if (item.entrega_hoy === undefined || item.entrega_hoy === null) {
            item.entrega_hoy = item.cantidad;
        }

        const tr = document.createElement("tr");
        tr.innerHTML = `
            <td>
                <div class="fw-bold" style="font-size: 0.85rem;">${item.nombre}</div>
                <small class="text-muted">${item.almacen_nombre} | ${item.tipo_precio.toUpperCase()}</small>
            </td>
            <td class="text-center">
                <span class="badge bg-secondary">${item.cantidad}</span>
            </td>
            <td>
                <div class="input-group input-group-sm">
                    <input type="number" 
                           class="form-control text-center input-entrega" 
                           data-index="${index}" 
                           value="${item.entrega_hoy}" 
                           min="0" 
                           max="${item.cantidad}"
                           step="any">
                    <span class="input-group-text"><i class="bi bi-box-seam"></i></span>
                </div>
            </td>
            <td class="text-end fw-bold">$${item.subtotal.toFixed(2)}</td>
        `;
        tabla.appendChild(tr);
    });

    // 3. Actualizar totales del modal
    window.recalcularTotalModal();

    // 4. Mostrar el modal usando Bootstrap
    const modalElement = document.getElementById('modalFinalizarVenta');
    if (modalElement) {
        const myModal = new bootstrap.Modal(modalElement);
        myModal.show();
    }
};

/**
 * 5. RECALCULAR TOTALES DENTRO DEL MODAL
 */
window.recalcularTotalModal = function() {
    let total = 0;
    if (window.carrito) {
        window.carrito.forEach(i => {
            total += parseFloat(i.subtotal);
        });
    }

    const totalDisplay = document.getElementById("totalFinalModal");
    if (totalDisplay) {
        totalDisplay.innerText = total.toFixed(2);
    }

    const inputPago = document.getElementById("monto_pagar");
    if (inputPago) {
        inputPago.value = total.toFixed(2);
        inputPago.dispatchEvent(new Event('input'));
    }
};