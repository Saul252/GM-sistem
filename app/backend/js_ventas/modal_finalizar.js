
    /* ================= CARRITO ================= */
    /* ================= MODAL ================= */

  function abrirModalFinalizar() {
    if (carrito.length === 0) return alert("El carrito está vacío");

    let tabla = document.getElementById("tablaConfirmacion");
    tabla.innerHTML = "";
    
    carrito.forEach((item, index) => {
        tabla.innerHTML += `
        <tr>
            <td>
                <div class="fw-bold">${item.nombre}</div>
                <small class="text-muted">${item.almacen_nombre}</small>
            </td>
            <td class="text-center">
                <span class="badge bg-secondary fs-6">${item.cantidad}</span>
            </td>
            <td>
                <div class="input-group input-group-sm">
                    <input type="number" 
                           class="form-control text-center input-entrega" 
                           data-index="${index}" 
                           value="${item.cantidad}" 
                           min="0" 
                           max="${item.cantidad}">
                    <span class="input-group-text"><i class="bi bi-box-seam"></i></span>
                </div>
            </td>
            <td class="text-end fw-bold">$${item.subtotal.toLocaleString('es-MX', {minimumFractionDigits: 2})}</td>
        </tr>`;
    });

    recalcularTotalModal();
    new bootstrap.Modal(document.getElementById('modalFinalizarVenta')).show();
} function recalcularTotalModal() {

        let total = 0;
        carrito.forEach(i => total += i.subtotal);

        let descuento = parseFloat(
            document.getElementById("descuentoGeneral").value
        ) || 0;

        total -= descuento;
        if (total < 0) total = 0;

        document.getElementById("totalFinalModal")
            .innerText = total.toFixed(2);
    }

    document.addEventListener("DOMContentLoaded", function() {
        let descuentoInput = document.getElementById("descuentoGeneral");
        if (descuentoInput) {
            descuentoInput.addEventListener("input", recalcularTotalModal);
        }
    });