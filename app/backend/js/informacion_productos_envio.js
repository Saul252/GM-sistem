/* =========================================================
   1. VARIABLE GLOBAL (se llena desde la API)
   ========================================================= */
let productosInventario = [];

/* Cache opcional por almacén para no repetir fetch */
const cacheProductosPorAlmacen = {};

/* =========================================================
   2. CARGA DE PRODUCTOS DESDE LA API
   ========================================================= */
async function cargarProductosDesdeAPI(origenId) {
    if (!origenId) return [];

    // Si ya lo tenemos en caché, lo usamos
    if (cacheProductosPorAlmacen[origenId]) {
        productosInventario = cacheProductosPorAlmacen[origenId];
        return productosInventario;
    }

    const url = `/cfsistem/app/controllers/accesoController.php?action=obtenerProductosAlmacen&id=${origenId}`;
    console.log("Consultando URL:", url);

    try {
        const resp = await fetch(url);
        const texto = await resp.text();
        console.log("=== RESPUESTA CRUDA ===", texto);

        let res;
        try {
            res = JSON.parse(texto);
        } catch (e) {
            console.error("❌ El servidor no devolvió JSON válido:", texto);
            return [];
        }

        if (!res.success || !Array.isArray(res.data)) {
            console.error("❌ API sin data válida:", res);
            return [];
        }

        // Normalizamos: aseguramos almacen_id y stock numérico
        productosInventario = res.data.map(p => ({
            ...p,
            almacen_id: p.almacen_id ?? origenId,
            stock: Number(p.stock ?? 0),
            factor_conversion: Number(p.factor_conversion ?? 1),
        }));

        cacheProductosPorAlmacen[origenId] = productosInventario;
        return productosInventario;
    } catch (err) {
        console.error("❌ Error fetch productos:", err);
        return [];
    }
}

/* =========================================================
   3. FILTRAR Y PINTAR EL SELECT DE PRODUCTOS
   ========================================================= */
async function filtrarProductosPorOrigen() {
    const origenId = document.getElementById('origen_id').value;
    console.log('almacen origen');
    console.log(origenId);
    const selectProd = document.getElementById('traspaso_producto');
    const infoStock = document.getElementById('info_stock');

    selectProd.innerHTML = '<option value="">Seleccione producto...</option>';
    if (infoStock) infoStock.innerText = '';

    if (!origenId) {
        selectProd.disabled = true;
        return;
    }

    // 🔥 Traemos los productos desde la API
    await cargarProductosDesdeAPI(origenId);

    // Filtro igual que antes, ahora sobre la data de la API
    const productosDisponibles = productosInventario.filter(
        p => p.almacen_id == origenId && p.stock > 0
    );

    if (productosDisponibles.length > 0) {
        productosDisponibles.forEach(p => {
            console.log(p);
            const option = document.createElement('option');
            // ⚠️ La API devuelve producto_id; usamos fallback a id
            option.value = p.producto_id ?? p.id;
            option.text = `${p.sku} - ${p.nombre}`;

            // ATRIBUTOS CRUCIALES PARA LA CONVERSIÓN
            option.dataset.stock = p.stock;
            option.dataset.factor = p.factor_conversion || 1;
            option.dataset.unidad = p.unidad_reporte || 'Unid.';
            option.dataset.medida = p.unidad_medida || 'Unid.';

            selectProd.appendChild(option);
        });
        selectProd.disabled = false;
    } else {
        selectProd.innerHTML = '<option value="">No hay productos disponibles</option>';
        selectProd.disabled = true;
    }
}

/* =========================================================
   4. VARIABLES GLOBALES DE CONVERSIÓN
   ========================================================= */
let factorTraspasoActual = 1;
let stockMaximoTraspaso = 0;
let unidadNombre = 'Unid.';
let unidadMedida = 'Unid.';

/* =========================================================
   5. LOTES
   ========================================================= */
async function lotes(producto_id, almacenId) {
    const select = document.getElementById("lote_id");
    if (!select) return;

    // Limpiar antes de llenar (evita duplicados al cambiar producto)
    select.innerHTML = '<option value="">Seleccione lote...</option>';

    const resp = await fetch(
        `/cfsistem/app/controllers/entregasController.php?ajax=despachar&prodId=${producto_id}&almacen=${almacenId}`
    );

    let data;
    try {
        data = await resp.json();
    } catch (e) {
        console.error("❌ lotes(): respuesta no JSON", e);
        return;
    }

    if (!data.data || !Array.isArray(data.data)) {
        console.warn("lotes(): sin data válida", data);
        return;
    }

    data.data.forEach(lote => {
        const option = document.createElement('option');
        option.value = lote.id;
        option.textContent = `${lote.codigo_lote} (${lote.cantidad_actual})`;
        select.appendChild(option);
    });

    console.log("lotes() ->", producto_id, almacenId, data);
}

/* =========================================================
   6. ACTUALIZAR MÁXIMO AL CAMBIAR PRODUCTO
   ========================================================= */
function actualizarMaximo() {
    const selectProd = document.getElementById('traspaso_producto');
    const infoStock = document.getElementById('info_stock');
    const labelUnidad = document.getElementById('label_unidad_reporte');
    const labelmenor = document.getElementById('label_unidad_medida');
    const almacen = document.getElementById('origen_id');

    const selectedOption = selectProd.options[selectProd.selectedIndex];

    if (selectedOption && selectedOption.value !== "") {
        stockMaximoTraspaso = parseFloat(selectedOption.dataset.stock) || 0;
        factorTraspasoActual = parseFloat(selectedOption.dataset.factor) || 1;
        unidadNombre = selectedOption.dataset.unidad || 'Unid.';
        unidadMedida = selectedOption.dataset.medida || 'Unid.';

        const cantidadReal = stockMaximoTraspaso / factorTraspasoActual;

        // Lotes del producto/almacén
        lotes(selectedOption.value, almacen.value);

        // Mostrar/ocultar bloque de conversión si las unidades son distintas
        const bloque = document.getElementById('bloque_traspaso');
        if (bloque) {
            bloque.style.display = (unidadMedida === unidadNombre) ? 'none' : 'block';
        }

        if (infoStock) {
            infoStock.innerText = `Stock disponible: ${cantidadReal >= 1
                ? cantidadReal + ' ' + unidadNombre
                : stockMaximoTraspaso + ' ' + unidadMedida
                }`;
        }
        if (labelUnidad) labelUnidad.innerText = unidadNombre;
        if (labelmenor) labelmenor.innerText = unidadMedida;

        // Limpiar inputs al cambiar de producto
        document.getElementById('traspaso_factor_input').value = '';
        document.getElementById('traspaso_piezas_input').value = '';
        calcularTotalTraspaso();
    }
}

/* =========================================================
   7. CÁLCULO DE TOTAL Y VALIDACIÓN DE STOCK
   ========================================================= */
function calcularTotalTraspaso() {
    const cantMayor = parseFloat(document.getElementById('traspaso_factor_input').value) || 0;
    const cantSueltas = parseFloat(document.getElementById('traspaso_piezas_input').value) || 0;

    // FÓRMULA: (CantidadMayor * Factor) + PiezasSueltas
    const totalPiezas = (cantMayor * factorTraspasoActual) + cantSueltas;

    // Input oculto que se envía al PHP
    document.getElementById('cantidad_traspaso_final').value = totalPiezas;

    const txtTotal = document.getElementById('txt_total_pzas');
    const unidadF = document.getElementById('unidadF');
    const resumen = document.getElementById('resumen_conversion');
    const btn = document.getElementById('btnGuardarTraspaso');

    if (totalPiezas > 0) {
        resumen.style.display = 'block';

        const totalReal = totalPiezas / factorTraspasoActual;

        txtTotal.innerText = totalReal >= 1
            ? totalReal.toFixed(2)
            : totalPiezas.toFixed(2);

        unidadF.innerText = totalReal >= 1 ? unidadNombre : unidadMedida;

        // Validar contra stock
        if (totalPiezas > stockMaximoTraspaso) {
            txtTotal.classList.add('text-danger');
            btn.disabled = true;
            btn.innerHTML = '<i class="bi bi-x-circle"></i> Stock Insuficiente';
        } else {
            txtTotal.classList.remove('text-danger');
            btn.disabled = false;
            btn.innerHTML = 'Solicitar Movimiento';
        }
    } else {
        resumen.style.display = 'none';
        btn.disabled = true;
    }
}

/* =========================================================
   8. EVENTOS
   ========================================================= */
document.getElementById('traspaso_factor_input').addEventListener('input', calcularTotalTraspaso);
document.getElementById('traspaso_piezas_input').addEventListener('input', calcularTotalTraspaso);

// Envío del formulario
document.getElementById('formTraspaso').addEventListener('submit', function (e) {
    e.preventDefault();

    const formData = new FormData(this);
    const destino = document.getElementById('destino_id').value;
    const origen = document.getElementById('origen_id').value;

    if (origen === destino) {
        Swal.fire('Error', 'El destino no puede ser igual al origen', 'error');
        return;
    }

    Swal.fire({
        title: '¿Confirmar envío?',
        text: 'La mercancía se descontará de tu stock actual.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, enviar'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('/cfsistem/app/backend/almacen/procesar_traspaso.php', {
                method: 'POST',
                body: formData
            })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success') {
                        Swal.fire('¡Enviado!', data.message, 'success')
                            .then(() => location.reload());
                    } else {
                        Swal.fire('Error', data.message, 'error');
                    }
                })
                .catch(err => {
                    console.error("❌ Error al enviar traspaso:", err);
                    Swal.fire('Error', 'No se pudo procesar el traspaso', 'error');
                });
        }
    });
});