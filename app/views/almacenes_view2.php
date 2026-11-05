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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/exceljs/4.4.0/exceljs.min.js"></script>

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
            <!-- Barra de Filtros (responsive) -->
            <div class="row mb-4 g-2 g-md-3 align-items-center">

                <!-- Filtros: categoría + almacén -->
                <div class="col-12 col-md-auto d-flex gap-2">
                    <select id="filtroCategoria" class="form-select form-select-sm rounded-3 shadow-none flex-fill">
                        <option value="">Todas las Categorías</option>
                        <?php foreach ($categorias as $cat): ?>
                            <option value="<?= $cat['id'] ?>">
                                <?= htmlspecialchars($cat['nombre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <select id="filtroAlmacen" class="form-select form-select-sm rounded-3 shadow-none flex-fill"
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

                <!-- Buscador -->
                <div class="col-12 col-md flex-grow-1">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-body-tertiary border-0 text-secondary rounded-start-3">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" id="buscador"
                            class="form-control border-0 bg-body-tertiary ps-1 shadow-none rounded-end-3"
                            placeholder="Buscar...">
                    </div>
                </div>

                <!-- Acciones -->
                <div class="col-12 col-md-auto ms-md-auto">
                    <div class="d-flex gap-2 align-items-center flex-wrap flex-md-nowrap justify-content-end">

                        <!-- Agregar Producto -->
                        <button type="button"
                            class="btn btn-sm btn-dark px-3 rounded-pill fw-medium d-flex align-items-center gap-2 btn-apple flex-grow-1 flex-md-grow-0 justify-content-center"
                            data-bs-toggle="modal" data-bs-target="#modalAgregarProducto">
                            <i class="bi bi-plus-lg"></i> Agregar Producto
                        </button>

                        <!-- Herramientas -->
                        <div class="dropdown position-relative flex-grow-1 flex-md-grow-0">
                            <button type="button"
                                class="btn btn-sm btn-light px-3 rounded-pill fw-medium d-flex align-items-center justify-content-center gap-2 btn-apple dropdown-toggle w-100"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-tools"></i> Herramientas
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm rounded-3 border-0">
                                <li>
                                    <button class="btn btn-success dropdown-item d-flex align-items-center gap-2"
                                        onclick="exportarTablaExcel()">
                                        <i class="bi bi-file-earmark-excel me-1"></i> Exportar Inventario a Excel
                                    </button>
                                </li>
                                <li>
                                    <button class="dropdown-item d-flex align-items-center gap-2" data-bs-toggle="modal"
                                        onclick="abrirModalInventarioInicial()">
                                        <i class="bi bi-sliders"></i> Cargar Inventario por producto
                                    </button>
                                </li>
                                <li>
                                    <button class="dropdown-item d-flex align-items-center gap-2"
                                        onclick="descargarProductos()">
                                        <i class="bi bi-download"></i> Descargar Excel para carga masiva
                                    </button>
                                </li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li>
                                    <button class="dropdown-item d-flex align-items-center gap-2"
                                        onclick="abrirModalCargarCSV()">
                                        <i class="bi bi-filetype-csv"></i> Cargar Inventario masivo
                                    </button>
                                </li>
                            </ul>
                        </div>

                        <!-- Traspasos -->
                        <div class="dropdown position-relative flex-grow-1 flex-md-grow-0">
                            <button type="button"
                                class="btn btn-sm btn-light px-3 rounded-pill fw-medium d-flex align-items-center justify-content-center gap-2 btn-apple dropdown-toggle w-100"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-arrow-left-right"></i> Traspasos
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm rounded-3 border-0">
                                <li>
                                    <button class="dropdown-item d-flex align-items-center gap-2" data-bs-toggle="modal"
                                        data-bs-target="#modalTraspaso">
                                        <i class="bi bi-plus-circle"></i> Nuevo Traspaso
                                    </button>
                                </li>
                                <li>
                                    <button class="dropdown-item d-flex align-items-center gap-2" data-bs-toggle="modal"
                                        data-bs-target="#modalTraspasosGestion" onclick="cargarTraspasos()">
                                        <i class="bi bi-check2-circle"></i> Autorizar Traspasos
                                    </button>
                                </li>
                            </ul>
                        </div>

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
    <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>

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
        async function descargarProductos() {

            // 1. Realizar la petición a tu controlador de Cf System
            const resp = await fetch(
                `/cfsistem/app/controllers/cotizacionesController.php?action=obtenerProductos`
            );


            const res = await resp.json();
            console.log(res.data);
            exportarProductosAExcel(res.data)

        }
        async function exportarProductosAExcel(productos) {
            // El nombre se conserva por compatibilidad con el onclick existente,
            // pero ahora genera CSV.

            if (!Array.isArray(productos) || productos.length === 0) {
                Swal.fire({ icon: 'warning', title: 'Sin datos', text: 'No hay productos para exportar.' });
                return;
            }

            const almacenCargar = document.getElementById('filtroAlmacen')?.value || '';

            // 1. Cabeceras (mismo orden que tenías en Excel)
            const headers = [
                'ID Producto',
                'SKU',
                'Almacén',
                'Nombre del Producto',
                'U. Reporte',
                'Factor Conv. (1/factor)',
                'P. Minorista',
                'P. Mayorista',
                'P. Distribuidor',
                'Existencias',
                'Costo Lote'
            ];

            // 2. Helper para escapar campos CSV (comillas, comas, saltos de línea)
            const escCSV = (v) => {
                const s = String(v ?? '');
                // Si contiene coma, comilla o salto de línea → envolver en comillas y duplicar comillas internas
                if (/[",\n\r]/.test(s)) {
                    return '"' + s.replace(/"/g, '""') + '"';
                }
                return s;
            };

            // 3. Construir filas
            const lineas = [headers.map(escCSV).join(',')];

            productos.forEach((prod) => {
                const factor = parseFloat(prod.factor_conversion || 0);

                const fila = [
                    prod.producto_id ?? '',
                    prod.sku ?? '',
                    almacenCargar,
                    prod.nombre ?? '',
                    prod.unidad_reporte ?? '',
                    factor, // número crudo; Excel lo formatea
                    parseFloat(prod.precio_minorista || 0),
                    parseFloat(prod.precio_mayorista || 0),
                    parseFloat(prod.precio_distribuidor || 0),
                    0, // Existencias (para llenar)
                    0  // Costo Lote (para llenar)
                ];

                lineas.push(fila.map(escCSV).join(','));
            });

            // 4. Añadir BOM UTF-8 para que Excel respete tildes/ñ
            const csvContent = '\uFEFF' + lineas.join('\r\n');

            // 5. Descargar
            const filename = 'Reporte_Productos_Inventario.csv';
            const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });

            const link = document.createElement('a');
            link.href = URL.createObjectURL(blob);
            link.download = filename;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);

            setTimeout(() => URL.revokeObjectURL(link.href), 1000);
        }

    </script>
    <script>
        /**
     * Procesa el CSV descargado (editado por el usuario en Existencias y Costo Lote)
     * y envía cada fila al backend usando la MISMA lógica que enviarFormularioProducto:
     *  - producto_id
     *  - sku
     *  - factor_conversion (real, no el inverso del Excel)
     *  - precio_adquisicion (costo unitario = Costo Lote / Existencias)
     *  - almacenes[ID][stock] = Existencias * factor
     *  - almacenes[ID][stock_minimo] = 0
     *  - almacenes[ID][precio_minorista / mayorista / distribuidor]
     *
     * El CSV trae: ID Producto, SKU, Almacén, Nombre, U. Reporte,
     * Factor Conv. (1/factor), P. Minorista, P. Mayorista, P. Distribuidor,
     * Existencias, Costo Lote
     */
        async function procesarCSVExistencias(file) {
            if (!file) {
                Swal.fire({ icon: 'warning', title: 'Atención', text: 'Debes seleccionar un archivo CSV.' });
                return;
            }

            // 1. Leer archivo
            const texto = await leerArchivoComoTexto(file);

            // 2. Parsear CSV
            const filas = parseCSV(texto);
            if (filas.length < 2) {
                Swal.fire({ icon: 'warning', title: 'Atención', text: 'El CSV está vacío o no tiene datos.' });
                return;
            }

            // 3. Mapear cabeceras
            const headers = filas[0].map(h => String(h).trim());
            const idx = {
                producto_id: headers.findIndex(h => /id\s*producto/i.test(h)),
                sku: headers.findIndex(h => /^sku$/i.test(h)),
                almacen_id: headers.findIndex(h => /almac[eé]n/i.test(h)),
                factor: headers.findIndex(h => /factor/i.test(h)),
                precio_minorista: headers.findIndex(h => /minorista/i.test(h)),
                precio_mayorista: headers.findIndex(h => /mayorista/i.test(h)),
                precio_distribuidor: headers.findIndex(h => /distribuidor/i.test(h)),
                existencias: headers.findIndex(h => /existencias/i.test(h)),
                costo_lote: headers.findIndex(h => /costo/i.test(h))
            };

            const faltantes = Object.entries(idx).filter(([, v]) => v === -1).map(([k]) => k);
            if (faltantes.length) {
                Swal.fire({
                    icon: 'error',
                    title: 'CSV inválido',
                    text: `Faltan columnas: ${faltantes.join(', ')}`
                });
                return;
            }

            // 4. Construir filas válidas
            const filasValidas = [];
            const omitidas = [];   // 👈 filas con existencias = 0

            for (let i = 1; i < filas.length; i++) {
                const row = filas[i];
                if (!row || row.length === 0 || row.every(c => !String(c).trim())) continue;

                const productoId = String(row[idx.producto_id] || '').trim();
                const almacenId = String(row[idx.almacen_id] || '').trim();
                if (!productoId || !almacenId) continue;

                const factorReal = parseNumero(row[idx.factor]) || 1;
                const existencias = parseNumero(row[idx.existencias]);
                const costoLote = parseNumero(row[idx.costo_lote]);
                const precioMin = parseNumero(row[idx.precio_minorista]);
                const precioMay = parseNumero(row[idx.precio_mayorista]);
                const precioDis = parseNumero(row[idx.precio_distribuidor]);

                // Stock total = Existencias × Factor
                const stockTotal = existencias * factorReal;

                // 👇 Si existencias es 0 (o el stockTotal da 0), NO se procesa
                if (existencias === 0 || stockTotal === 0) {
                    omitidas.push({ producto_id: productoId, almacen_id: almacenId, motivo: 'existencias = 0' });
                    console.warn(`[Fila ${i}] OMITIDA prod=${productoId} alm=${almacenId} (existencias = 0)`);
                    continue;
                }

                // 👇 Se manda el COSTO TOTAL del lote; el backend lo divide por el stock
                const precioAdquisicion = costoLote;

                // Diagnóstico (quítalo cuando confirmes)
                console.log(`[Fila ${i}] prod=${productoId} alm=${almacenId} ` +
                    `factor=${factorReal} exist=${existencias} ` +
                    `stock=${stockTotal} costo_lote=${costoLote} ` +
                    `precio_adquisicion=${precioAdquisicion} ` +
                    `pMin=${precioMin} pMay=${precioMay} pDis=${precioDis}`);

                filasValidas.push({
                    producto_id: productoId,
                    sku: String(row[idx.sku] || '').trim(),
                    almacen_id: almacenId,
                    factor_conversion: factorReal,
                    precio_adquisicion: precioAdquisicion,
                    stock: stockTotal,
                    stock_minimo: 0,
                    precio_minorista: precioMin,
                    precio_mayorista: precioMay,
                    precio_distribuidor: precioDis
                });
            }

            // 👇 Si todas las filas fueron omitidas
            if (filasValidas.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Nada que procesar',
                    html: `No se encontraron filas con existencias mayores a 0.
                   <br><br>Filas omitidas: <b>${omitidas.length}</b>`
                });
                return;
            }

            // 5. Confirmar (mostrando también las omitidas, si hay)
            const resumenOmitidas = omitidas.length
                ? `<br><br><span class="text-warning">Se omitirán <b>${omitidas.length}</b> fila(s) con existencias = 0.</span>`
                : '';

            const confirm = await Swal.fire({
                icon: 'question',
                title: '¿Procesar CSV?',
                html: `Se procesarán <b>${filasValidas.length}</b> fila(s).${resumenOmitidas}`,
                showCancelButton: true,
                confirmButtonText: 'Sí, procesar',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#0d6efd'
            });
            if (!confirm.isConfirmed) return;

            // 6. Procesar una por una
            let ok = 0, fail = 0;
            const errores = [];

            Swal.fire({
                title: 'Procesando...',
                html: `<b>0</b> / ${filasValidas.length}`,
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            for (let i = 0; i < filasValidas.length; i++) {
                const f = filasValidas[i];

                const formData = new FormData();
                formData.append('producto_id', f.producto_id);
                formData.append('sku', f.sku);
                formData.append('factor_conversion', f.factor_conversion);
                formData.append('precio_adquisicion', f.precio_adquisicion);

                formData.append(`almacenes[${f.almacen_id}][stock]`, f.stock);
                formData.append(`almacenes[${f.almacen_id}][stock_minimo]`, f.stock_minimo);
                formData.append(`almacenes[${f.almacen_id}][precio_minorista]`, f.precio_minorista);
                formData.append(`almacenes[${f.almacen_id}][precio_mayorista]`, f.precio_mayorista);
                formData.append(`almacenes[${f.almacen_id}][precio_distribuidor]`, f.precio_distribuidor);

                try {
                    const resp = await fetch('/cfsistem/app/controllers/almacenes.php?action=guardarCantidadesIniciales', {
                        method: 'POST',
                        body: formData
                    });
                    const data = await resp.json();
                    if (data.status === 'success') {
                        ok++;
                    } else {
                        fail++;
                        errores.push(`Prod ${f.producto_id} / Alm ${f.almacen_id}: ${data.message || 'error'}`);
                    }
                } catch (e) {
                    fail++;
                    errores.push(`Prod ${f.producto_id} / Alm ${f.almacen_id}: ${e.message}`);
                }

                Swal.update({ html: `<b>${i + 1}</b> / ${filasValidas.length}` });
            }

            // 7. Resultado
            if (fail === 0) {
                Swal.fire({
                    icon: 'success',
                    title: '¡Listo!',
                    html: `Se procesaron correctamente <b>${ok}</b> fila(s).
                   ${omitidas.length ? `<br><br><small class="text-muted">Omitidas por existencias = 0: <b>${omitidas.length}</b></small>` : ''}`,
                    confirmButtonColor: '#0d6efd'
                }).then(() => location.reload());
            } else {
                Swal.fire({
                    icon: 'warning',
                    title: 'Procesado con errores',
                    html: `OK: <b>${ok}</b><br>Fallidos: <b>${fail}</b>
                   ${omitidas.length ? `<br>Omitidas (exist. = 0): <b>${omitidas.length}</b>` : ''}
                   <br><br>
                   <small style="text-align:left;display:block;max-height:200px;overflow:auto;">
                   ${errores.slice(0, 15).map(e => `• ${e}`).join('<br>')}
                   ${errores.length > 15 ? '<br>...' : ''}
                   </small>`,
                    confirmButtonColor: '#dc3545'
                });
            }
        } /* ============================================================
           UTILIDADES
           ============================================================ */

        function parseNumero(v) {
            if (v === null || v === undefined) return 0;
            let s = String(v).trim();
            if (!s) return 0;

            s = s.replace(/[$€\s]/g, '');

            if (s.includes(',') && s.includes('.')) {
                s = s.replace(/,/g, '');
            } else if (s.includes(',')) {
                const partes = s.split(',');
                if (partes[partes.length - 1].length === 3) {
                    s = s.replace(/,/g, '');
                } else {
                    s = s.replace(',', '.');
                }
            }

            const n = parseFloat(s);
            return isNaN(n) ? 0 : n;
        }

        function leerArchivoComoTexto(file) {
            return new Promise((resolve, reject) => {
                const reader = new FileReader();
                reader.onload = e => resolve(e.target.result);
                reader.onerror = reject;
                reader.readAsText(file, 'UTF-8');
            });
        }

        function parseCSV(texto) {
            if (texto.charCodeAt(0) === 0xFEFF) texto = texto.slice(1);

            const primeraLinea = texto.split(/\r?\n/)[0] || '';

            let sep = ',';
            if (primeraLinea.includes('\t')) sep = '\t';
            else if ((primeraLinea.match(/;/g) || []).length > (primeraLinea.match(/,/g) || []).length) sep = ';';

            const filas = [];
            let fila = [], campo = '', enComillas = false;

            for (let i = 0; i < texto.length; i++) {
                const c = texto[i], next = texto[i + 1];

                if (enComillas) {
                    if (c === '"' && next === '"') { campo += '"'; i++; }
                    else if (c === '"') { enComillas = false; }
                    else { campo += c; }
                } else {
                    if (c === '"') enComillas = true;
                    else if (c === sep) { fila.push(campo); campo = ''; }
                    else if (c === '\n') { fila.push(campo); filas.push(fila); fila = []; campo = ''; }
                    else if (c === '\r') { /* ignorar */ }
                    else { campo += c; }
                }
            }
            if (campo !== '' || fila.length) { fila.push(campo); filas.push(fila); }

            return filas;
        }
        function leerArchivoComoTexto(file) {
            return new Promise((resolve, reject) => {
                const reader = new FileReader();
                reader.onload = e => resolve(e.target.result);
                reader.onerror = reject;
                reader.readAsText(file, 'UTF-8');
            });
        }

        function parseCSV(texto) {
            const primeraLinea = texto.split(/\r?\n/)[0] || '';
            const sep = (primeraLinea.match(/;/g) || []).length > (primeraLinea.match(/,/g) || []).length ? ';' : ',';

            const filas = [];
            let fila = [], campo = '', enComillas = false;

            for (let i = 0; i < texto.length; i++) {
                const c = texto[i], next = texto[i + 1];
                if (enComillas) {
                    if (c === '"' && next === '"') { campo += '"'; i++; }
                    else if (c === '"') { enComillas = false; }
                    else campo += c;
                } else {
                    if (c === '"') enComillas = true;
                    else if (c === sep) { fila.push(campo); campo = ''; }
                    else if (c === '\n') { fila.push(campo); filas.push(fila); fila = []; campo = ''; }
                    else if (c === '\r') { /* ignorar */ }
                    else campo += c;
                }
            }
            if (campo !== '' || fila.length) { fila.push(campo); filas.push(fila); }
            return filas;
        }
        function abrirModalCargarCSV() {
            Swal.fire({
                title: 'Cargar CSV de Existencias',
                html: `
            <div class="text-start">
                <p class="mb-2 text-muted" style="font-size: .9rem;">
                    Selecciona el archivo CSV descargado y editado (Existencias y Costo Lote).
                </p>
                <input type="file" id="swalCsvFile" accept=".csv" class="form-control">
            </div>
        `,
                showCancelButton: true,
                confirmButtonText: 'Procesar',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#0d6efd',
                cancelButtonColor: '#6c757d',
                focusConfirm: false,
                didOpen: () => {
                    // Forzar estilos para que el input se vea bien dentro de Swal
                    const input = document.getElementById('swalCsvFile');
                    input.style.width = '100%';
                },
                preConfirm: () => {
                    const input = document.getElementById('swalCsvFile');
                    const file = input?.files?.[0];
                    if (!file) {
                        Swal.showValidationMessage('Debes seleccionar un archivo CSV.');
                        return false;
                    }
                    // Validar extensión
                    if (!/\.csv$/i.test(file.name)) {
                        Swal.showValidationMessage('El archivo debe ser .csv');
                        return false;
                    }
                    return file;
                }
            }).then((result) => {
                if (result.isConfirmed && result.value) {
                    procesarCSVExistencias(result.value);
                }
            });
        }
        function exportarTablaExcel(nombreArchivo = 'Productos') {
            const tbody = document.getElementById('tbodyProductos');
            if (!tbody) {
                Swal.fire('Error', 'No se encontró la tabla de productos.', 'error');
                return;
            }

            const filas = tbody.querySelectorAll('tr');
            if (!filas.length) {
                Swal.fire('Sin datos', 'No hay productos para exportar.', 'warning');
                return;
            }

            // Detectar si la tabla está vacía (mensaje de "no hay productos")
            if (filas.length === 1 && filas[0].querySelector('td[colspan]')) {
                Swal.fire('Sin datos', 'No hay productos para exportar.', 'warning');
                return;
            }

            // ============ ENCABEZADOS ============
            // Ajusta estos nombres a los que quieras en el Excel
            const encabezados = [
                'SKU',
                'Producto',
                'Descripción',
                'Categoría',
                'Stock',
                'Almacén'
            ];

            const datos = [encabezados];

            // ============ FILAS ============
            filas.forEach(tr => {
                const celdas = tr.querySelectorAll('td');
                if (celdas.length < 7) return; // fila inválida o mensaje vacío

                // Extraemos el texto limpio (sin HTML interno de badges/botones)
                const sku = celdas[0].innerText.trim();
                const nombre = celdas[1].innerText.trim();
                const descripcion = celdas[2].innerText.trim();
                const categoria = celdas[3].innerText.trim();
                const stock = celdas[4].innerText.trim();
                const almacen = celdas[5].innerText.trim();

                datos.push([sku, nombre, descripcion, categoria, stock, almacen]);
            });

            // ============ CREAR LIBRO ============
            const wb = XLSX.utils.book_new();
            const ws = XLSX.utils.aoa_to_sheet(datos);

            // Anchos de columna (aproximados)
            ws['!cols'] = [
                { wch: 15 },  // SKU
                { wch: 30 },  // Producto
                { wch: 40 },  // Descripción
                { wch: 20 },  // Categoría
                { wch: 15 },  // Stock
                { wch: 20 }   // Almacén
            ];

            XLSX.utils.book_append_sheet(wb, ws, 'Productos');

            // ============ DESCARGAR ============
            const fecha = new Date().toISOString().slice(0, 10); // YYYY-MM-DD
            XLSX.writeFile(wb, `${nombreArchivo}_${fecha}.xlsx`);
        }
    </script>
</body>

</html>