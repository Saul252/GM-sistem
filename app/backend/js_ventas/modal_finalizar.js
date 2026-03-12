/**
 * 4. FUNCIÓN PARA ABRIR EL MODAL DE FINALIZACIÓN
 */
window.abrirModalFinalizar = function() {
    if (!window.carrito || window.carrito.length === 0) {
        Swal.fire('Carrito vacío', 'Agrega productos antes de finalizar la venta.', 'warning');
        return;
    }

    const tabla = document.getElementById("tablaConfirmacion");
    if (!tabla) return;
    tabla.innerHTML = "";
    
    window.carrito.forEach((item, index) => {
        if (item.entrega_hoy === undefined || item.entrega_hoy === null) {
            item.entrega_hoy = item.cantidad;
        }

        // Cálculos iniciales de venta total
        const cantFactorVenta = Math.floor(item.cantidad / item.factor);
        const piezasRestantesVenta = Math.round((item.cantidad % item.factor) * 100) / 100;

        // Cálculos dinámicos de lo que se está ENTREGANDO
        const fEntregar = Math.floor(item.entrega_hoy / item.factor);
        const pEntregar = Math.round((item.entrega_hoy % item.factor) * 100) / 100;

        const tr = document.createElement("tr");
        tr.innerHTML = `
            <td>
                <div class="fw-bold" style="font-size: 0.85rem;">${item.nombre}</div>
                <small class="text-muted d-block">${item.almacen_nombre} | ${item.tipo_precio.toUpperCase()}</small>
                
                <div class="mt-1" style="font-size: 0.7rem; color: #055160; background: #e3f2fd; padding: 4px 8px; border-radius: 4px; border-left: 3px solid #0d6efd;">
                    <i class="bi bi-info-circle-fill"></i> Factor: 1 <b>${item.unidad_reporte}</b> = <b>${item.factor}</b> pzas.<br>
                    Vendido: ${cantFactorVenta} ${item.unidad_reporte} + ${piezasRestantesVenta} pzas.<br>
                    
                </div>
            </td>
            <td class="text-center">
                <div class="fw-bold" style="font-size: 0.9rem;">${item.cantidad}</div>
                <small class="text-muted" style="font-size: 0.65rem;">Pzas Totales</small>
            </td>
            <td>
                <div class="input-group input-group-sm">
                    <input type="number" 
                           class="form-control text-center input-entrega-modal" 
                           data-index="${index}" 
                           value="${item.cantidad}" 
                           min="0" 
                           max="${item.cantidad}"
                           step="any">
                    <span class="input-group-text"><i class="bi bi-box-seam"></i></span>
                </div>
                <small class="text-muted d-block text-center" style="font-size: 0.65rem;">Piezas a entregar hoy</small>
            </td>
            <td class="text-end fw-bold">$${item.subtotal.toFixed(2)}</td>
        `;
        tabla.appendChild(tr);
    });

    // Llamada segura a la función local
    window.recalcularTotalModal();

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
            total += parseFloat(i.subtotal || 0);
        });
    }

    const totalDisplay = document.getElementById("totalFinalModal");
    if (totalDisplay) totalDisplay.innerText = total.toFixed(2);

    const inputPago = document.getElementById("monto_pagar");
    if (inputPago) {
        inputPago.value = total.toFixed(2);
        inputPago.dispatchEvent(new Event('input'));
    }
};

/**
 * 6. LISTENER PARA ACTUALIZAR DESGLOSE Y ENTREGA EN TIEMPO REAL
 */document.addEventListener('input', function(e) {
    if (e.target.classList.contains('input-entrega-modal')) {
        const index = e.target.dataset.index;
        const item = window.carrito[index];
        
        // CORRECCIÓN: Usa parseFloat para permitir decimales o cantidades enteras mayores a 1
        let valor = parseFloat(e.target.value);
        
        if (isNaN(valor)) valor = 0;

        // Validar que no entregue más de lo vendido
        if (valor > item.cantidad) {
            valor = item.cantidad;
            e.target.value = valor;
        }
        
        // Guardamos el valor real (ej. 2, 5, 10...)
        item.entrega_hoy = valor;

        // Actualizar el texto informativo (Aquí sí usamos floor solo para mostrar el texto de "bultos")
        const f = Math.floor(valor / item.factor);
        const p = Math.round((valor % item.factor) * 100) / 100;
        
        const elDesglose = document.getElementById(`desglose-entrega-${index}`);
        if (elDesglose) {
            elDesglose.innerHTML = `Entregando: ${f} ${item.unidad_reporte} + ${p} pzas.`;
        }
    }
});