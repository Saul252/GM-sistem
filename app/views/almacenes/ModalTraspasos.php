<div class="modal fade" id="modalTraspaso" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title"><i class="bi bi-arrow-left-right"></i> Nuevo Traspaso</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="formTraspaso" action="/cfsistem/app/backend/almacen/procesar_traspaso.php" method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">1. Almacén de Origen</label>
                        <select name="almacen_origen_id" id="origen_id" class="form-select border-primary" required
                            onchange="filtrarProductosPorOrigen()">
                            <option value="">Seleccione donde sale la mercancía...</option>
                            <?php foreach ($almacenes as $alm): ?>
                                <option value="<?= $alm['id'] ?>"><?= htmlspecialchars($alm['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">2. Producto a mover</label>
                        <!-- 🔒 Este es el ÚNICO select con Select2 -->
                        <select name="producto_id" id="traspaso_producto" class="form-select" required
                            onchange="actualizarMaximo()">
                            <option value="">Primero seleccione un origen...</option>
                        </select>
                        <div id="info_stock" class="form-text text-primary fw-bold"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Seleccione lote</label>
                        <select name="lote_id" id="lote_id" class="form-select">
                            <option value="">seleccione lote (opcional)...</option>
                        </select>
                        <div id="info_stock" class="form-text text-primary fw-bold"></div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label fw-bold">3. Almacén Destino</label>
                            <select name="almacen_destino_id" id="destino_id" class="form-select" required>
                                <option value="">¿A dónde va la mercancía?</option>
                                <?php foreach ($todosLosAlmacenes as $alm_dest): ?>
                                    <?php if ($almacen_usuario > 0 && $alm_dest['id'] == $almacen_usuario)
                                        continue; ?>
                                    <option value="<?= $alm_dest['id'] ?>"><?= htmlspecialchars($alm_dest['nombre']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class=" row mb-3">
                            <label class="form-label fw-bold">4. Cantidad a Traspasar</label>
                            <div class=" col-md-6 mb-3">
                                <input type="number" id="traspaso_factor_input" class="form-control text-center"
                                    placeholder="0" min="0" step="0.01">
                                <span class="input-group-text" id="label_unidad_reporte"
                                    style="min-width: 80px;">Unid.</span>
                            </div>
                            <div id="bloque_traspaso" class="col-md-6 mb-3">
                                <input type="number" id="traspaso_piezas_input" class="form-control text-center"
                                    placeholder="0" min="0" step=".01">

                                <span class="input-group-text" id="label_unidad_medida"
                                    style="min-width: 80px;">Unid.</span>
                            </div>


                            <input type="hidden" name="cantidad" id="cantidad_traspaso_final" required>

                            <div id="resumen_conversion"
                                class="mt-2 p-2 rounded border border-subtle border-start border-4 border-primary"
                                style="display:none; font-size: 0.9rem;">
                                <strong>Movimiento total:</strong> <span id="txt_total_pzas">0</span> <span
                                    id="unidadF"></span>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Observaciones</label>
                        <textarea name="observaciones" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="btnGuardarTraspaso">Solicitar
                        Movimiento</button>
                </div>
            </form>
        </div>
    </div>
</div>


<div class="modal fade" id="modalTraspasosGestion" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Gestión de Traspasos entre Almacenes</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">

                <?php if ($_SESSION['rol_id'] == 1): ?>
                    <div class="row mb-3 border-subtle p-3 rounded border">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Ver movimientos del Almacén:</label>
                            <select id="admin_filtro_almacen" class="form-select" onchange="cargarTraspasos()">
                                <option value="">Seleccione un almacén para autorizar...</option>
                                <?php foreach ($almacenes as $a): ?>
                                    <option value="<?= $a['id'] ?>">
                                        <?= $a['nombre'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- ✅ FIX: Inputs de fecha con valores por defecto = mes actual -->
                <div class="row g-3 mb-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label fw-bold small text-uppercase">
                            <i class="bi bi-calendar3 me-1"></i> Desde
                        </label>
                        <input type="date" id="filtro_fecha_inicio" class="form-control" value="<?= date('Y-m-01') ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold small text-uppercase">
                            <i class="bi bi-calendar3 me-1"></i> Hasta
                        </label>
                        <input type="date" id="filtro_fecha_fin" class="form-control" value="<?= date('Y-m-t') ?>">
                    </div>
                    <div class="col-md-4">
                        <button type="button" class="btn btn-primary w-100" onclick="cargarTraspasos()">
                            <i class="bi bi-funnel-fill"></i> Filtrar
                        </button>
                    </div>
                </div>

                <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" data-bs-toggle="pill" data-bs-target="#pills-arribos">
                            📥 Arribos (Por Recibir)
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" data-bs-toggle="pill" data-bs-target="#pills-envios">
                            📤 Envíos (En Tránsito)
                        </button>
                    </li>
                </ul>

                <div class="tab-content" id="pills-tabContent">
                    <div class="tab-pane fade show active" id="pills-arribos">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Producto</th>
                                        <th>Cant.</th>
                                        <th>Origen</th>
                                        <th>Enviado por</th>
                                        <th>Acción</th>
                                    </tr>
                                </thead>
                                <tbody id="contenedor-arribos">
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="pills-envios">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead class="table-light">
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Producto</th>
                                        <th>Cant.</th>
                                        <th>Destino</th>
                                        <th>Estado</th>
                                    </tr>
                                </thead>
                                <tbody id="contenedor-envios">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    /* =========================================================
   1. VARIABLE GLOBAL (se llena desde la API)
   ========================================================= */
    let productosInventario = [];

    /* Cache opcional por almacén para no repetir fetch */
    const cacheProductosPorAlmacen = {};

    /* =========================================================
       2. SELECT2 — SOLO para #traspaso_producto dentro de #modalTraspaso
       ========================================================= */
    function initSelect2ProductoTraspaso() {
        // Protección: jQuery + Select2 disponibles
        if (typeof window.jQuery === 'undefined' || !window.jQuery.fn.select2) return;

        const $modal = window.jQuery('#modalTraspaso');
        const $select = $modal.find('#traspaso_producto'); // 🔒 acotado al modal

        if (!$select.length) return;

        // Si ya estaba inicializado, destruir antes de recrear
        if ($select.hasClass('select2-hidden-accessible')) {
            $select.select2('destroy');
        }

        $select.select2({
            theme: 'bootstrap-5',
            width: '100%',
            dropdownParent: $modal,        // 🔑 dropdown dentro del modal
            placeholder: 'Seleccione producto...',
            allowClear: true,
            language: {
                noResults: function () { return 'No hay productos disponibles'; },
                searching: function () { return 'Buscando...'; }
            }
        });

        // Propagar eventos de Select2 al onchange nativo (actualizarMaximo)
        $select
            .off('select2:select.select2Traspaso select2:clear.select2Traspaso')
            .on('select2:select.select2Traspaso select2:clear.select2Traspaso', function () {
                actualizarMaximo();
            });
    }

    /* =========================================================
       3. CARGA DE PRODUCTOS DESDE LA API
       ========================================================= */
    async function cargarProductosDesdeAPI(origenId) {
        if (!origenId) return [];

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
       4. FILTRAR Y PINTAR EL SELECT DE PRODUCTOS (+ reinit Select2)
       ========================================================= */
    async function filtrarProductosPorOrigen() {
        const origenId = document.getElementById('origen_id').value;
        const selectProd = document.getElementById('traspaso_producto');
        const infoStock = document.getElementById('info_stock');

        selectProd.innerHTML = '<option value="">Seleccione producto...</option>';
        if (infoStock) infoStock.innerText = '';

        if (!origenId) {
            selectProd.disabled = true;
            initSelect2ProductoTraspaso(); // refrescar estado vacío
            return;
        }

        await cargarProductosDesdeAPI(origenId);

        const productosDisponibles = productosInventario.filter(
            p => p.almacen_id == origenId && p.stock > 0
        );

        if (productosDisponibles.length > 0) {
            productosDisponibles.forEach(p => {
                const option = document.createElement('option');
                option.value = p.producto_id ?? p.id;
                option.text = `${p.nombre} [ ${p.sku} ]`;
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

        // 🔁 Re-inicializar Select2 tras repoblar opciones
        initSelect2ProductoTraspaso();
    }

    /* =========================================================
       5. VARIABLES GLOBALES DE CONVERSIÓN
       ========================================================= */
    let factorTraspasoActual = 1;
    let stockMaximoTraspaso = 0;
    let unidadNombre = 'Unid.';
    let unidadMedida = 'Unid.';

    /* =========================================================
       6. LOTES
       ========================================================= */
    async function lotes(producto_id, almacenId) {
        const select = document.getElementById("lote_id");
        if (!select) return;




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
        select.innerHTML = '';

        data.data.forEach(lote => {
            const option = document.createElement('option');
            option.value = lote.id;
            option.textContent = `${lote.codigo_lote} (${lote.cantidad_actual})`;
            select.appendChild(option);
        });

        console.log("lotes() ->", producto_id, almacenId, data);
    }

    /* =========================================================
       7. ACTUALIZAR MÁXIMO AL CAMBIAR PRODUCTO
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

            lotes(selectedOption.value, almacen.value);

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

            document.getElementById('traspaso_factor_input').value = '';
            document.getElementById('traspaso_piezas_input').value = '';
            calcularTotalTraspaso();
        }
    }

    /* =========================================================
       8. CÁLCULO DE TOTAL Y VALIDACIÓN DE STOCK
       ========================================================= */
    function calcularTotalTraspaso() {
        const cantMayor = parseFloat(document.getElementById('traspaso_factor_input').value) || 0;
        const cantSueltas = parseFloat(document.getElementById('traspaso_piezas_input').value) || 0;

        const totalPiezas = (cantMayor * factorTraspasoActual) + cantSueltas;

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
       9. EVENTOS
       ========================================================= */
    document.getElementById('traspaso_factor_input').addEventListener('input', calcularTotalTraspaso);
    document.getElementById('traspaso_piezas_input').addEventListener('input', calcularTotalTraspaso);

    // Inicializar Select2 al abrir el modal (por si se abre antes de elegir origen)
    document.addEventListener('DOMContentLoaded', function () {
        const modalEl = document.getElementById('modalTraspaso');
        if (!modalEl) return;

        modalEl.addEventListener('shown.bs.modal', function () {
            initSelect2ProductoTraspaso();
        });

        // Si Select2 se carga async, reintentar al cargar la ventana
        window.addEventListener('load', initSelect2ProductoTraspaso);
    });

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
</script>
<script src="/cfsistem/app/backend/js/cargar_traspasos.js"></script>
<script src="/cfsistem/app/backend/js/aceptar_arribo.js"></script>