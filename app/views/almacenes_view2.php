<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Almacenes | Sistema</title>
    <link rel="icon" type="image/png" href="/cfsistem/public/assets/logo.png">
    <link rel="shortcut icon" href="/cfsistem/public/assets/logo.ico" type="image/x-icon">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <?php require_once __DIR__ . '/layout/icono.php' ?>
    <link href="/cfsistem/css/almacenes.css" rel="stylesheet">
    <?php
    // Llamamos a la función que imprime Bootstrap y layout.css
    if (function_exists('cargarEstilos')) {
        cargarEstilos();
    }
    ?>
</head>

<body>

    <?php renderizarLayout($paginaActual); ?>

    <script>
        // Asegúrate de que el JSON incluya factor_conversion y unidad_reporte
        const productosInventario = <?= json_encode($productos) ?>;
    </script>

    <div class="main-content container-fluid px-4 py-3">

        <!-- Cabecera y Micro-tarjetas estilo iOS -->
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

                <!-- Inversión -->
                <div class="ios-micro-card">
                    <span class="ios-micro-label">Inversión Total</span>
                    <div class="ios-micro-value text-primary">$
                        <?= number_format($inversion, 2, '.', ',') ?>
                    </div>
                </div>

                <!-- Stock / Global -->
                <div class="ios-micro-card">
                    <span class="ios-micro-label">
                        <?= ($rData['tipo'] == 'admin') ? 'Global' : 'Stock' ?>
                    </span>
                    <div class="ios-micro-value text-body">
                        <?= number_format($cant_prod) ?>
                    </div>
                    <div class="ios-micro-footer text-muted text-truncate" style="max-width: 90px;"
                        title="<?= htmlspecialchars($rData['nombre']) ?>">
                        <?= htmlspecialchars($rData['nombre']) ?>
                    </div>
                </div>

                <!-- Catálogo -->
                <div class="ios-micro-card">
                    <span class="ios-micro-label">Catálogo</span>
                    <div class="ios-micro-value text-body">
                        <?= number_format($total_cat) ?>
                    </div>
                    <div class="ios-micro-footer text-muted">Items</div>
                </div>

                <!-- Cobertura -->
                <div class="ios-micro-card">
                    <span class="ios-micro-label">Cobertura</span>
                    <div class="d-flex align-items-baseline">
                        <span class="ios-micro-value text-success">
                            <?= $cobertura ?>
                        </span>
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

        <!-- Panel Principal / Filtros y Tabla -->
        <div class="card border-0 shadow-sm p-4 mb-5" style="border-radius: 20px; background-color: var(--bs-body-bg);">

            <!-- Barra de Filtros y Acciones -->
            <div class="row mb-4 g-3 align-items-center">
                <div class="col-xl-2 col-md-3">
                    <select id="filtroCategoria" class="form-select form-select-sm py-2 rounded-3 shadow-none">
                        <option value="">Todas las Categorías</option>
                        <?php foreach ($categorias as $cat): ?>
                            <option value="<?= $cat['id'] ?>">
                                <?= htmlspecialchars($cat['nombre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-xl-2 col-md-3">
                    <select id="filtroAlmacen" class="form-select form-select-sm py-2 rounded-3 shadow-none"
                        <?= ($almacen_usuario > 0) ? 'disabled' : '' ?>>
                        <?php if ($almacen_usuario == 0): ?>
                            <option value="">Todos los Almacenes</option>
                        <?php endif; ?>
                        <?php foreach ($almacenes as $alm): ?>
                            <option value="<?= $alm['id'] ?>" <?= ($almacen_usuario == $alm['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($alm['nombre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-xl-3 col-md-3">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-transparent border-end-0 text-muted rounded-start-3"><i
                                class="bi bi-search"></i></span>
                        <input type="text" id="buscador"
                            class="form-control border-start-0 ps-0 py-2 shadow-none rounded-end-3"
                            placeholder="Buscar producto...">
                    </div>
                </div>
                <div class="col-xl-5 col-md-3">
                    <div class="d-flex gap-2 justify-content-md-end flex-wrap">
                        <!-- Botón 1: Nuevo Producto -->
                        <button
                            class="btn btn-sm btn-success px-3 rounded-pill fw-semibold shadow-sm d-flex align-items-center gap-2 transition-all"
                            data-bs-toggle="modal" data-bs-target="#modalAgregarProducto">
                            <i class="bi bi-box-seam-fill fs-6"></i> Producto
                        </button>

                        <!-- Botón 2: Inventario Inicial -->
                        <button
                            class="btn btn-sm btn-info text-white px-3 rounded-pill fw-semibold shadow-sm d-flex align-items-center gap-2 transition-all"
                            data-bs-toggle="modal" onclick="abrirModalInventarioInicial()">
                            <i class="bi bi-sliders fs-6"></i> Iniciales
                        </button>

                        <!-- Botón 3: Traspaso -->
                        <button
                            class="btn btn-sm btn-warning text-dark px-3 rounded-pill fw-semibold shadow-sm d-flex align-items-center gap-2 transition-all"
                            data-bs-toggle="modal" data-bs-target="#modalTraspaso">
                            <i class="bi bi-arrow-left-right fs-6"></i> Traspaso
                        </button>

                        <!-- Botón 4: Autorizar -->
                        <button
                            class="btn btn-sm btn-primary px-3 rounded-pill fw-semibold shadow-sm d-flex align-items-center gap-2 transition-all"
                            data-bs-toggle="modal" data-bs-target="#modalTraspasosGestion" onclick="cargarTraspasos()">
                            <i class="bi bi-check2-circle fs-6"></i> Autorizar
                        </button>
                    </div>
                </div>

            </div>

            <!-- Tabla Estilo iOS / Limpia -->
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
                    <tbody>
                        <?php foreach ($productos as $p): ?>
                            <tr data-categoria="<?= $p['categoria_id'] ?>" data-almacen="<?= $p['almacen_id'] ?>"
                                style="border-bottom: 1px solid var(--bs-border-color-translucent);">
                                <td class="ps-3 fw-bold text-body">
                                    <?= htmlspecialchars($p['sku']) ?>
                                </td>
                                <td class="fw-semibold text-body">
                                    <?= htmlspecialchars($p['nombre']) ?>
                                </td>
                                <td class="text-muted small">
                                    <?= htmlspecialchars($p['descripcion'] ?? 'Sin descripción') ?>
                                </td>
                                <td>
                                    <span
                                        class="badge bg-secondary bg-opacity-10 text-secondary px-2 py-1 rounded-pill fw-medium"
                                        style="font-size: 0.75rem;">
                                        <?= htmlspecialchars($p['categoria_nombre'] ?? 'Sin Categoría') ?>
                                    </span>
                                </td>
                                <td>
                                    <?php
                                    $cantidad = $p['stock'] / ($p['factor_conversion'] ?: 1);

                                    if ($cantidad <= 0) {
                                        $badgeStyle = 'bg-danger bg-opacity-15 text-white';
                                    } elseif ($cantidad <= 5) {
                                        $badgeStyle = 'bg-warning bg-opacity-15 text-warning text-dark';
                                    } elseif ($cantidad <= 20) {
                                        $badgeStyle = 'bg-info bg-opacity-15 text-white';
                                    } else {
                                        $badgeStyle = 'bg-success bg-opacity-15 text-white';
                                    }
                                    ?>
                                    <span class="badge <?= $badgeStyle ?> px-2.5 py-1 rounded-pill fw-semibold"
                                        style="font-size: 0.75rem;">
                                        <?= $cantidad >= 1
                                            ? number_format($cantidad, 2) . ' ' . $p['unidad_reporte']
                                            : number_format($p['stock'], 2) . ' ' . $p['unidad_medida']
                                            ?>
                                    </span>
                                </td>
                                <td class="text-muted small">
                                    <?= htmlspecialchars($p['almacen_nombre'] ?? 'N/A') ?>
                                </td>
                                <td class="text-center pe-3">
                                    <button
                                        class="btn btn-sm rounded-circle p-1 d-inline-flex align-items-center justify-content-center shadow-none text-warning border"
                                        style="width: 32px; height: 32px;"
                                        onclick="editarProducto(<?= $p['id'] ?>, <?= $p['almacen_id'] ?>)" title="Editar">
                                        <i class="bi bi-pencil fs-6"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modales -->
    <?php require_once __DIR__ . '/almacenes/ModalAgregarCantidadesIniciales.php'; ?>
    <?php require_once __DIR__ . '/almacenes/ModalCategoria.php'; ?>
    <?php require_once __DIR__ . '/almacenes/ModalTraspasos.php'; ?>
    <?php require_once __DIR__ . '/almacenes/ModalAgregarProducto.php'; ?>
    <?php require_once __DIR__ . '/almacenes/ModalEditarProducto.php'; ?>
    <?php require_once __DIR__ . '/productos/modalMedidasAdicionales.php' ?>
    <?php require_once __DIR__ . '/productos/modalListaMedidas.php' ?>

    <!-- Scripts Externos -->
    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Script de Filtros y Lógica -->
    <script>
        // FILTROS CORREGIDOS
        function aplicarFiltros() {
            let texto = document.getElementById("buscador").value.toLowerCase();
            let categoria = document.getElementById("filtroCategoria").value;
            let almacen = document.getElementById("filtroAlmacen").value;

            document.querySelectorAll("tbody tr").forEach(fila => {
                let coincideTexto = fila.innerText.toLowerCase().includes(texto);

                // Forzamos conversión a String para prevenir errores de tipo de datos (String vs Number)
                let coincideCategoria = !categoria || String(fila.dataset.categoria) === String(categoria);
                let coincideAlmacen = !almacen || String(fila.dataset.almacen) === String(almacen);

                fila.style.display = (coincideTexto && coincideCategoria && coincideAlmacen) ? "" : "none";
            });
        }

        document.getElementById("buscador").addEventListener("keyup", aplicarFiltros);
        document.getElementById("filtroCategoria").addEventListener("change", aplicarFiltros);
        document.getElementById("filtroAlmacen").addEventListener("change", aplicarFiltros);

        // Convierte automáticamente inputs de texto y textareas a mayúsculas
        document.querySelectorAll('input[type="text"], textarea').forEach(elemento => {
            elemento.addEventListener('input', function () {
                this.value = this.value.toUpperCase();
            });
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', async function () {
            const params = new URLSearchParams(window.location.search);

            if (params.get('abrirTraspasos') === '1') {
                await cargarTraspasos();

                // Limpiar URL de parámetros temporales
                window.history.replaceState(
                    {},
                    document.title,
                    window.location.pathname
                );
            }
        });
    </script>
</body>

</html>