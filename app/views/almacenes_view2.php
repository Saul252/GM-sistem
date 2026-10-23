<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Almacenes | Sistema</title>
    <link rel="icon" type="image/png" href="/cfsistem/public/assets/logo.png">
    <link rel="shortcut icon" href="/cfsistem/public/assets/logo.ico" type="image/x-icon">

    <!-- ⚡ Preconnect a CDNs -->
    <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
    <link rel="preconnect" href="https://code.jquery.com" crossorigin>
    <link rel="dns-prefetch" href="https://cdn.jsdelivr.net">
    <link rel="dns-prefetch" href="https://code.jquery.com">

    <!-- Bootstrap (CSS) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Select2 CSS + tema BS5 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css"
        rel="stylesheet" />

    <!-- SweetAlert2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">

    <?php require_once __DIR__ . '/layout/icono.php' ?>
    <link href="/cfsistem/css/almacenes.css" rel="stylesheet">
    <?php if (function_exists('cargarEstilos'))
        cargarEstilos(); ?>
</head>

<body>

    <?php renderizarLayout($paginaActual); ?>

    <div class="main-content px-4 py-3">

        <!-- Cabecera -->
        <div class="d-flex justify-content-between align-items-center flex-wrap m-4 gap-3">
            <h2 class="fw-bold mb-0 text-body" style="letter-spacing: -0.03em; font-size: 1.75rem;">
                <i class="bi bi-box-seam text-primary me-2"></i> Módulo de Almacén
            </h2>

            <?php
            $rData = $resumenData ?? ['tipo' => 'error', 'nombre' => 'No disponible', 'mis_productos' => 0, 'total_sistema' => 0];
            $cant_prod = $rData['mis_productos'];
            $total_cat = $rData['total_sistema'];
            $cobertura = ($total_cat > 0) ? round(($cant_prod / $total_cat) * 100, 1) : 0;
            ?>

            <div class="d-flex align-items-center flex-wrap" style="gap: 10px;">
                <div class="ios-micro-card">
                    <span class="ios-micro-label">Inversión Total</span>
                    <div class="ios-micro-value text-primary">$<?= number_format($inversion, 2, '.', ',') ?></div>
                </div>
                <div class="ios-micro-card">
                    <span class="ios-micro-label"><?= ($rData['tipo'] == 'admin') ? 'Global' : 'Stock' ?></span>
                    <div class="ios-micro-value text-body"><?= number_format($cant_prod) ?></div>
                    <div class="ios-micro-footer text-muted text-truncate" style="max-width: 90px;"
                        title="<?= htmlspecialchars($rData['nombre']) ?>">
                        <?= htmlspecialchars($rData['nombre']) ?>
                    </div>
                </div>
                <div class="ios-micro-card">
                    <span class="ios-micro-label">Catálogo</span>
                    <div class="ios-micro-value text-body"><?= number_format($total_cat) ?></div>
                    <div class="ios-micro-footer text-muted">Items</div>
                </div>
                <div class="ios-micro-card">
                    <span class="ios-micro-label">Cobertura</span>
                    <div class="d-flex align-items-baseline">
                        <span class="ios-micro-value text-success"><?= $cobertura ?></span>
                        <span style="font-size: 0.65rem; font-weight: 700; margin-left: 1px;"
                            class="text-success">%</span>
                    </div>
                    <div class="progress"
                        style="height: 4px; background-color: var(--bs-tertiary-bg); border-radius: 10px; margin-top: 6px; width: 100%;">
                        <div class="progress-bar bg-success rounded-pill" style="width: <?= $cobertura ?>%;"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Panel Principal -->
        <div class="card border-0 shadow-sm p-4 mb-5" style="border-radius: 20px; background-color: var(--bs-body-bg);">

            <!-- Barra de Filtros -->
            <div class="row mb-4 g-3 align-items-center">

                <div class="col-xl-2 col-md-3">
                    <select id="filtroCategoria" class="form-select form-select-sm py-2 rounded-3 shadow-none">
                        <option value="">Todas las Categorías</option>
                        <?php foreach ($categorias as $cat): ?>
                            <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['nombre']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-xl-2 col-md-3">
                    <select id="filtroAlmacen" class="form-select form-select-sm py-2 rounded-3 shadow-none"
                        <?= ($almacen_usuario > 0) ? 'disabled' : '' ?>>
                        <?php if ($almacen_usuario == 0): ?>
                            <option value="0">Todos los Almacenes</option>
                        <?php endif; ?>
                        <?php foreach ($almacenes as $alm): ?>
                            <option value="<?= $alm['id'] ?>" <?= ($almacen_usuario == $alm['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($alm['nombre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>



                <div class="col-xl-5 col-md-6">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-transparent border-end-0 text-muted rounded-start-3"><i
                                class="bi bi-search"></i></span>
                        <input type="text" id="buscador"
                            class="form-control border-start-0 ps-0 py-2 shadow-none rounded-end-3"
                            placeholder="Buscar...">
                    </div>
                </div>

                <div class="col-xl-3 col-md-3">
                    <div class="d-flex gap-2 justify-content-md-end flex-wrap">
                        <button
                            class="btn btn-sm btn-success px-3 rounded-pill fw-semibold shadow-sm d-flex align-items-center gap-2"
                            data-bs-toggle="modal" data-bs-target="#modalAgregarProducto">
                            <i class="bi bi-box-seam-fill fs-6"></i> Producto
                        </button>
                        <button
                            class="btn btn-sm btn-info text-white px-3 rounded-pill fw-semibold shadow-sm d-flex align-items-center gap-2"
                            data-bs-toggle="modal" onclick="abrirModalInventarioInicial()">
                            <i class="bi bi-sliders fs-6"></i> Iniciales
                        </button>
                        <button
                            class="btn btn-sm btn-warning text-dark px-3 rounded-pill fw-semibold shadow-sm d-flex align-items-center gap-2"
                            data-bs-toggle="modal" data-bs-target="#modalTraspaso">
                            <i class="bi bi-arrow-left-right fs-6"></i> Traspaso
                        </button>
                        <button
                            class="btn btn-sm btn-primary px-3 rounded-pill fw-semibold shadow-sm d-flex align-items-center gap-2"
                            data-bs-toggle="modal" data-bs-target="#modalTraspasosGestion" onclick="cargarTraspasos()">
                            <i class="bi bi-check2-circle fs-6"></i> Autorizar
                        </button>
                    </div>
                </div>

            </div>

            <!-- Tabla -->
            <div class="table-responsive tabla-scroll">
                <table class="table table-borderless table-hover align-middle mb-0">
                    <thead class="text-muted text-uppercase fs-7 border-bottom"
                        style="font-size: 0.7rem; letter-spacing: 0.05em;">
                        <tr>
                            <th class="ps-3 py-3">SKU</th>
                            <th class="py-3">Producto</th>
                            <th class="py-3">Descripción</th>
                            <th class="py-3">Categoría</th>
                            <th class="py-3">Stock</th>
                            <th class="py-3">Almacén</th>
                            <th class="text-center pe-3 py-3" width="60">Acción</th>
                        </tr>
                    </thead>
                    <tbody id="tbodyProductos">
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                <div class="spinner-border spinner-border-sm me-2"></div> Cargando productos...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- =========================================================
         LIBRERÍAS JS — con defer (orden garantizado, sin retries)
         ========================================================= -->
    <script defer src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/i18n/es.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Modales (PHP) -->
    <?php require_once __DIR__ . '/almacenes/ModalAgregarCantidadesIniciales.php'; ?>
    <?php require_once __DIR__ . '/almacenes/ModalCategoria.php'; ?>
    <?php require_once __DIR__ . '/almacenes/ModalTraspasos.php'; ?>
    <?php require_once __DIR__ . '/almacenes/ModalAgregarProducto.php'; ?>
    <?php require_once __DIR__ . '/almacenes/ModalEditarProducto.php'; ?>
    <?php require_once __DIR__ . '/productos/modalMedidasAdicionales.php' ?>
    <?php require_once __DIR__ . '/productos/modalListaMedidas.php' ?>

    <!-- JS principal — externo, defer, corre después de jQuery+Select2 -->
    <script>
        /* =========================================================================
   Módulo Almacenes — JS optimizado
   - Sin toUpperCase global (usa data-uppercase delegado)
   - Select2 reconstruido en un solo innerHTML
   - Filtros con debounce y data-search (sin innerText por tecla)
   - Sin reintentos de 80ms (defer garantiza orden)
   ========================================================================= */
        (function () {
            'use strict';

            /* ---------- Utilidades ---------- */
            function esc(str) {
                return String(str ?? '')
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#039;');
            }

            function badgeStock(p) {
                const factor = parseFloat(p.factor_conversion) || 1;
                const cantidad = (parseFloat(p.stock) || 0) / factor;
                let cls = 'bg-success bg-opacity-15 text-white';
                if (cantidad <= 0) cls = 'bg-danger bg-opacity-15 text-white';
                else if (cantidad <= 5) cls = 'bg-warning bg-opacity-15 text-warning text-dark';
                else if (cantidad <= 20) cls = 'bg-info bg-opacity-15 text-white';

                const etiqueta = cantidad >= 1
                    ? `${cantidad.toFixed(2)} ${esc(p.unidad_reporte || '')}`
                    : `${(parseFloat(p.stock) || 0).toFixed(2)} ${esc(p.unidad_medida || '')}`;

                return `<span class="badge ${cls} px-2 py-1 rounded-pill fw-semibold" style="font-size:.75rem;">${etiqueta}</span>`;
            }

            /* ---------- Render tabla (una sola escritura al DOM) ---------- */
            function renderTabla(productos) {
                const tbody = document.getElementById('tbodyProductos');
                if (!tbody) return;

                if (!Array.isArray(productos) || productos.length === 0) {
                    tbody.innerHTML = `
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">
                        <i class="bi bi-inbox fs-4 d-block mb-1"></i>
                        No hay productos para mostrar.
                    </td>
                </tr>`;
                    return;
                }

                const filas = productos.map(p => {
                    const pid = p.producto_id ?? p.id;
                    // Precalculamos el texto para búsqueda (una sola vez)
                    const search = `${p.sku || ''} ${p.nombre || ''} ${p.descripcion || ''} ${p.categoria_nombre || ''}`.toLowerCase();

                    return `
            <tr data-categoria="${esc(p.categoria_id)}"
                data-almacen="${esc(p.almacen_id)}"
                data-producto-id="${esc(pid)}"
                data-search="${esc(search)}"
                style="border-bottom:1px solid var(--bs-border-color-translucent);">
                <td class="ps-3 fw-bold text-body">${esc(p.sku)}</td>
                <td class="fw-semibold text-body">${esc(p.nombre)}</td>
                <td class="text-muted small">${esc(p.descripcion || 'Sin descripción')}</td>
                <td>
                    <span class="badge bg-secondary bg-opacity-10 text-secondary px-2 py-1 rounded-pill fw-medium" style="font-size:.75rem;">
                        ${esc(p.categoria_nombre || 'Sin Categoría')}
                    </span>
                </td>
                <td>${badgeStock(p)}</td>
                <td class="text-muted small">${esc(p.almacen_nombre || 'N/A')}</td>
                <td class="text-center pe-3">
                    <button class="btn btn-sm rounded-circle p-1 d-inline-flex align-items-center justify-content-center shadow-none text-warning border"
                        style="width:32px;height:32px;"
                        onclick="editarProducto(${Number(pid)}, ${Number(p.almacen_id)})"
                        title="Editar">
                        <i class="bi bi-pencil fs-6"></i>
                    </button>
                </td>
            </tr>`;
                });

                tbody.innerHTML = filas.join('');
            }

            /* ---------- Select2: reconstruir en un solo innerHTML ---------- */
            function popularSelectProductos(productos) {
                const select = document.getElementById('buscadorProductosEditar');
                if (!select) return;

                let html = '<option value=""></option>';
                for (let i = 0; i < productos.length; i++) {
                    const p = productos[i];
                    const idProd = p.producto_id ?? p.id;
                    const medidas = esc(JSON.stringify(p.medidas_adicionales || []));

                    html += `<option value="${esc(idProd)}"
                data-nombre="${esc(p.nombre || '')}"
                data-sku="${esc(p.sku || '')}"
                data-um="${esc(p.unidad_medida || '')}"
                data-ur="${esc(p.unidad_reporte || '')}"
                data-premin="${p.precio_minorista || 0}"
                data-premat="${p.precio_mayorista || 0}"
                data-predis="${p.precio_distribuidor || 0}"
                data-factor="${p.factor_conversion || 1}"
                data-stock="${p.stock || 1}"
                data-medidas='${medidas}'
            >[${esc(p.sku || '')}] ${esc(p.nombre || '')}</option>`;
                }

                // Una sola escritura → un solo reflow de Select2
                select.innerHTML = html;

                // Solo si Select2 ya está activo, refrescamos opciones (no reconstruye dropdown)
                if (window.jQuery && window.jQuery(select).hasClass('select2-hidden-accessible')) {
                    // 'change' es más barato que 'change.select2'
                    window.jQuery(select).trigger('change');
                }
            }

            /* ---------- Carga de productos (con AbortController + anti-race) ---------- */
            let peticionActual = null;
            let ultimoRequestId = 0;
            let loaderTimer = null;

            async function recargarProductosEditar(almacenIdParam) {
                const tbody = document.getElementById('tbodyProductos');
                const selectAlmacen = document.getElementById('filtroAlmacen');
                if (!tbody || !selectAlmacen) return;

                const valorRaw = almacenIdParam ?? selectAlmacen.value;
                const id = (valorRaw === '' || valorRaw == null) ? 0 : Number(valorRaw);

                if (peticionActual) peticionActual.abort();
                peticionActual = new AbortController();
                const signal = peticionActual.signal;
                const requestId = ++ultimoRequestId;

                if (loaderTimer) clearTimeout(loaderTimer);
                loaderTimer = setTimeout(() => {
                    if (requestId !== ultimoRequestId) return;
                    tbody.innerHTML = `
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">
                        <div class="spinner-border spinner-border-sm me-2"></div> Cargando productos...
                    </td>
                </tr>`;
                }, 150);

                const url = `/cfsistem/app/controllers/almacenes.php?action=obtenerProductosParaAlmacen&id=${id}`;

                try {
                    const resp = await fetch(url, { signal });
                    if (!resp.ok) throw new Error('HTTP ' + resp.status);
                    const texto = await resp.text();

                    if (requestId !== ultimoRequestId) return;

                    let res;
                    try {
                        res = JSON.parse(texto);
                    } catch (e) {
                        console.error('❌ JSON inválido:\n', texto);
                        tbody.innerHTML = `<tr><td colspan="7" class="text-center text-danger py-4">Respuesta inválida del servidor.</td></tr>`;
                        return;
                    }

                    if (!res.success) {
                        tbody.innerHTML = `<tr><td colspan="7" class="text-center text-danger py-4">${esc(res.message || 'Error al cargar productos')}</td></tr>`;
                        return;
                    }

                    const productos = Array.isArray(res.data) ? res.data : [];

                    // 1) Poblar Select2 en un solo reflow
                    popularSelectProductos(productos);

                    // 2) Render de tabla en un solo reflow
                    renderTabla(productos);

                    // 3) Aplicar filtros vigentes
                    aplicarFiltros();

                    if (typeof window.cargarClientes === 'function') window.cargarClientes();

                } catch (error) {
                    if (error.name === 'AbortError') return;
                    console.error('Error en recargarProductosEditar:', error);
                    tbody.innerHTML = `<tr><td colspan="7" class="text-center text-danger py-4">Error de conexión con el servidor.</td></tr>`;
                } finally {
                    if (requestId === ultimoRequestId) {
                        if (loaderTimer) { clearTimeout(loaderTimer); loaderTimer = null; }
                        peticionActual = null;
                    }
                }
            }

            /* ---------- Filtros (sin innerText, con data-search) ---------- */
            function aplicarFiltros() {
                const texto = (document.getElementById('buscador')?.value || '').toLowerCase().trim();
                const categoria = document.getElementById('filtroCategoria')?.value || '';
                const almacen = document.getElementById('filtroAlmacen')?.value || '';
                const selectProd = document.getElementById('buscadorProductosEditar');
                const productoId = selectProd ? String(selectProd.value || '') : '';

                const filas = document.querySelectorAll('#tbodyProductos tr[data-producto-id]');
                // Recorrido directo sin reflows: leemos dataset y escribimos style
                for (let i = 0; i < filas.length; i++) {
                    const fila = filas[i];
                    const d = fila.dataset;

                    const okTexto = !texto || (d.search || '').includes(texto);
                    const okCat = !categoria || d.categoria === categoria;
                    const okAlm = !almacen || almacen === '0' || d.almacen === almacen;
                    const okProd = !productoId || d.productoId === productoId;

                    fila.style.display = (okTexto && okCat && okAlm && okProd) ? '' : 'none';
                }
            }

            /* ---------- Select2 init (una vez, sin retry) ---------- */
            function initSelect2Productos() {
                const $ = window.jQuery;
                if (!$ || !$.fn.select2) return;

                const $el = $('#buscadorProductosEditar');
                if (!$el.length || $el.hasClass('select2-hidden-accessible')) return;

                $el.select2({
                    theme: 'bootstrap-5',
                    width: '100%',
                    placeholder: 'Filtrar por producto...',
                    allowClear: true,
                    language: {
                        noResults: () => 'Sin resultados',
                        searching: () => 'Buscando...'
                    }
                });

                $el.on('select2:select select2:clear change', aplicarFiltros);
            }

            /* ---------- Uppercase delegado (solo donde se pida) ---------- */
            document.addEventListener('input', function (e) {
                const el = e.target;
                if (!el || !el.matches || !el.matches('[data-uppercase]')) return;

                const upper = el.value.toUpperCase();
                if (el.value === upper) return;

                const start = el.selectionStart;
                const end = el.selectionEnd;
                el.value = upper;
                // Restaurar cursor (solo si es input de texto)
                try { el.setSelectionRange(start, end); } catch (_) { }
            });

            /* ---------- Bootstrap de la página ---------- */
            function boot() {
                initSelect2Productos();

                // Buscador con debounce
                let filtroTimer;
                const buscador = document.getElementById('buscador');
                if (buscador) {
                    buscador.addEventListener('input', () => {
                        clearTimeout(filtroTimer);
                        filtroTimer = setTimeout(aplicarFiltros, 120);
                    });
                }

                // Cambios de filtros
                document.getElementById('filtroCategoria')?.addEventListener('change', aplicarFiltros);

                const selAlmacen = document.getElementById('filtroAlmacen');
                if (selAlmacen) {
                    selAlmacen.addEventListener('change', function () {
                        recargarProductosEditar(this.value);
                    });
                }

                // Carga inicial
                const inicialRaw = selAlmacen?.value;
                const inicialId = (inicialRaw === '' || inicialRaw == null) ? 0 : Number(inicialRaw);
                recargarProductosEditar(inicialId);

                // Traspasos desde URL
                (async () => {
                    const params = new URLSearchParams(window.location.search);
                    if (params.get('abrirTraspasos') === '1') {
                        if (typeof window.cargarTraspasos === 'function') await window.cargarTraspasos();
                        window.history.replaceState({}, document.title, window.location.pathname);
                    }
                })();
            }

            // Exponer lo que otros modales usen
            window.recargarProductosEditar = recargarProductosEditar;
            window.aplicarFiltros = aplicarFiltros;

            // Con defer, el DOM ya está parseado aquí:
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', boot);
            } else {
                boot();
            }
        })();
    </script>
</body>

</html>