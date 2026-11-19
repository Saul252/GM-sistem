<?php
$almacen_usuario = intval($_SESSION['almacen_id'] ?? 0);
$rol_id = intval($_SESSION['rol_id'] ?? 0);
$esAdmin = ($almacen_usuario === 0 || $rol_id === 3);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edición de Lotes</title>
    <link rel="icon" type="image/png" href="/cfsistem/public/assets/logo.png">
    <link rel="shortcut icon" href="/cfsistem/public/assets/logo.ico" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <?php require_once __DIR__ . '/layout/icono.php' ?>
    <?php if (function_exists('cargarEstilos'))
        cargarEstilos(); ?>

    <style>
        :root {
            --sidebar-width: 260px;
            --navbar-height: 65px;
            --apple-bg: #f5f5f7;
        }

        body {
            background-color: var(--apple-bg);
            font-family: 'SF Pro Display', -apple-system, sans-serif;
        }

        .main-content {
            margin-left: var(--sidebar-width);
            padding: 40px;
            padding-top: calc(var(--navbar-height) + 20px);
        }

        tr.table-warning td {
            background-color: #fff3cd !important;
        }

        tr.table-danger td {
            background-color: #f8d7da !important;
        }

        .card-proyeccion {
            border-left: 4px solid #0d6efd;
        }

        .card-proyeccion.desactivada {
            border-left-color: #6c757d;
            opacity: 0.85;
        }

        .proy-label {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: .05em;
            color: #6c757d;
        }

        .proy-valor {
            font-size: 1.4rem;
            font-weight: 700;
        }

        .proy-valor.positivo {
            color: #198754;
        }

        .proy-valor.negativo {
            color: #dc3545;
        }

        .proy-valor.neutro {
            color: #0d6efd;
        }

        .input-existencias {
            max-width: 130px;
        }

        .fila-lote td {
            vertical-align: middle;
        }

        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
                padding: 20px;
                padding-top: 90px;
            }
        }
    </style>
</head>

<body>
    <?php if (function_exists('renderizarLayout'))
        renderizarLayout($paginaActual); ?>

    <main class="main-content">

        <!-- ========== HEADER CON BOTONES ========== -->
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <h3 class="mb-0">📦 Edición de Lotes</h3>
            <div class="d-flex gap-2">
                <button id="btnModoEdicion" class="btn btn-primary" onclick="toggleModoEdicion()">
                    <i class="bi bi-pencil-square"></i> Editar
                </button>
                <button id="btnGuardar" class="btn btn-success" onclick="confirmarGuardado()" disabled>
                    <i class="bi bi-save"></i> Guardar cambios <span id="badgeCambios"
                        class="badge bg-light text-dark ms-1">0</span>
                </button>
            </div>
        </div>

        <!-- ========== FILTROS ========== -->
        <div class="card p-3 mb-3">
            <div class="row g-3">
                <?php if ($esAdmin): ?>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Almacén</label>
                        <select id="filtroAlmacen" class="form-select">
                            <option value="0">Selecciona almacén</option>
                            <?php foreach ($almacenes as $a): ?>
                                <option value="<?= (int) $a['id'] ?>"><?= htmlspecialchars($a['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                <?php endif; ?>

                <div class="col-md-3">
                    <label class="form-label small fw-bold">Producto</label>
                    <select id="filtroProducto" class="form-select select2">

                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold">Desde</label>
                    <input type="date" id="fecha_inicio" class="form-control" value="<?= date('Y-m-01') ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold">Hasta</label>
                    <input type="date" id="fecha_fin" class="form-control" value="<?= date('Y-m-d') ?>">
                </div>
                <div class="col-md-2 d-grid">
                    <label class="invisible">.</label>
                    <button class="btn btn-dark" onclick="cargarHistorial()">
                        <i class="bi bi-search"></i> Consultar
                    </button>
                </div>
            </div>
        </div>

        <!-- ========== RESUMEN ========== -->
        <div class="row g-3 mb-3">
            <div class="col-6 col-md-4">
                <div class="card shadow-sm border-0 rounded-4 p-3">
                    <div class="text-body-secondary small">Cant. inicial (lotes visibles)</div>
                    <h4 class="fw-bold mb-0" id="sum_inicial">0.00</h4>
                </div>
            </div>
            <div class="col-6 col-md-4">
                <div class="card shadow-sm border-0 rounded-4 p-3">
                    <div class="text-body-secondary small">Cant. actual (lotes visibles)</div>
                    <h4 class="fw-bold mb-0" id="sum_actual">0.00</h4>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="card shadow-sm border-0 rounded-4 p-3 border border-success">
                    <div class="text-body-secondary small">Stock actual en inventario</div>
                    <h4 class="fw-bold mb-0 text-success" id="stock_inv">0.00</h4>
                </div>
            </div>
        </div>

        <!-- ========== TOGGLE AFECTAR INVENTARIO ========== -->
        <div class="card card-proyeccion p-3 mb-3" id="cardProyeccion">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2">
                <div>
                    <h6 class="mb-1"><i class="bi bi-boxes text-primary"></i> ¿Afectar el inventario?</h6>
                    <small class="text-muted">
                        Si lo desactivas, los lotes se actualizarán pero el stock del inventario <strong>no
                            cambiará</strong>.
                    </small>
                </div>
                <div class="form-check form-switch fs-4 m-0">
                    <input class="form-check-input" type="checkbox" id="toggleAfectarInventario" checked
                        onchange="actualizarProyeccion()">
                    <label class="form-check-label small" for="toggleAfectarInventario" id="toggleLabel">Sí
                        afectar</label>
                </div>
            </div>

            <hr class="my-2">

            <div class="row g-3 text-center">
                <div class="col-4">
                    <div class="proy-label">Stock actual</div>
                    <div class="proy-valor neutro" id="proyStockActual">0.00</div>
                </div>
                <div class="col-4">
                    <div class="proy-label">Descuento por cambios</div>
                    <div class="proy-valor" id="proyDelta">0.00</div>
                    <small class="text-muted d-block" id="proyDeltaDetalle"></small>
                </div>
                <div class="col-4">
                    <div class="proy-label" id="proyFinalLabel">Stock resultante</div>
                    <div class="proy-valor" id="proyStockFinal">0.00</div>
                    <small class="text-muted d-block" id="proyFinalNota"></small>
                </div>
            </div>
        </div>

        <!-- ========== TABLA DE LOTES ========== -->
        <div class="card p-3">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Lote</th>
                            <th>Fecha ingreso</th>
                            <th class="text-end">Cant. inicial</th>
                            <th class="text-end" style="width:160px;">Cant. actual</th>
                            <th class="text-end">Costo</th>
                            <th>Estado</th>
                            <th class="text-center" style="width:100px;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tablaLotes">
                        <tr>
                            <td colspan="7" class="text-center text-body-secondary py-4">
                                Selecciona almacén y producto, luego pulsa <strong>Consultar</strong>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        const ALMACEN_USUARIO = <?= (int) $almacen_usuario ?>;
        const CONTROLLER_URL = '/cfsistem/app/controllers/editarInventarioController.php';

        let modoEdicion = false;
        let lotesData = [];
        let inventarioData = null;
        let cambios = {}; // { [loteId]: { id, cantidad_actual, estado_lote } }

        const n = v => parseFloat(v) || 0;
        const fmt = v => n(v).toFixed(2);
        const fmtDelta = v => (v >= 0 ? '+' : '−') + Math.abs(v).toFixed(2);

        // ---------- HELPERS ----------
        function getAlmacenId() {
            if ($('#filtroAlmacen').length) {
                return parseInt($('#filtroAlmacen').val()) || 0;
            }
            return ALMACEN_USUARIO;
        }
        function getProductoId() { return parseInt($('#filtroProducto').val()) || 0; }
        function getProductoNom() {
            const t = $('#filtroProducto option:selected').text().trim();
            return (t && t !== 'Selecciona producto' && t !== 'Buscar producto...') ? t : '—';
        }

        // ---------- CARGA DE PRODUCTOS (fuente única de verdad) ----------
        async function recargarProductosEditar() {
            const id = $('#filtroAlmacen').length
                ? parseInt($('#filtroAlmacen').val()) || 0
                : ALMACEN_USUARIO;

            const $select = $('#filtroProducto');

            if (!id) {
                $select.empty().append(new Option('Selecciona almacén primero...', '', true, true));
                $select.trigger('change');
                return;
            }

            try {
                const url = `/cfsistem/app/controllers/accesoController.php?action=obtenerProductosAlmacen&id=${id}`;
                const resp = await fetch(url);
                const texto = await resp.text();

                let res;
                try { res = JSON.parse(texto); }
                catch {
                    console.error("❌ Respuesta no es JSON válido:", texto);
                    return;
                }

                if (!res.success) {
                    console.error("❌ obtenerProductosAlmacen:", res.message);
                    return;
                }

                $select.empty();
                $select.append(new Option('Buscar producto...', '', true, true));

                (res.data || []).forEach(pr => {
                    const nombreUpper = (pr.nombre || '').toUpperCase();
                    const skuUpper = (pr.sku || '').toUpperCase();

                    const option = new Option(`[${skuUpper}] ${nombreUpper}`, pr.producto_id, false, false);

                    $(option).attr({
                        'data-nombre': nombreUpper,
                        'data-medidas': JSON.stringify(pr.medidas_adicionales || []),
                        'data-sku': skuUpper,
                        'data-um': pr.unidad_medida || '',
                        'data-ur': pr.unidad_reporte || '',
                        'data-premin': pr.precio_minorista || 0,
                        'data-premat': pr.precio_mayorista || 0,
                        'data-predis': pr.precio_distribuidor || 0,
                        'data-factor': pr.factor_conversion || 1,
                        'data-stock': pr.stock || 1
                    });
                    $select.append(option);
                });

                $select.trigger('change');
            } catch (e) {
                console.error("Error al recargar productos:", e);
            }
        }

        // ---------- CARGA DE LOTES ----------
        function cargarHistorial() {
            if (modoEdicion && Object.keys(cambios).length > 0) {
                Swal.fire({
                    title: "¿Recargar?",
                    text: "Tienes cambios sin guardar. Se descartarán al recargar.",
                    icon: "warning", showCancelButton: true,
                    confirmButtonText: "Sí, recargar", cancelButtonText: "Cancelar"
                }).then(r => { if (r.isConfirmed) { cambios = {}; _doCargar(); } });
                return;
            }
            _doCargar();
        }

        function _doCargar() {
            const producto = getProductoId();
            const almacen = getAlmacenId();
            const f_ini = $('#fecha_inicio').val();
            const f_fin = $('#fecha_fin').val();

            if (!producto) { Swal.fire("Aviso", "Selecciona un producto", "warning"); return; }
            if (!almacen) { Swal.fire("Aviso", "Selecciona un almacén específico", "warning"); return; }

            $.ajax({
                url: CONTROLLER_URL,
                type: 'GET',
                data: {
                    action: 'obtenerLotesEInventario',
                    producto_id: producto,
                    almacen_id: almacen,
                    fecha_inicio: f_ini,
                    fecha_fin: f_fin
                },
                dataType: 'json',
                success: function (res) {
                    if (!res.success) {
                        Swal.fire("Error", res.message || "No se pudo consultar.", "error");
                        return;
                    }
                    lotesData = res.data.lotes || [];
                    inventarioData = res.data.inventario || null;
                    cambios = {};

                    renderTabla();
                    renderResumen();
                    actualizarBotones();
                },
                error: () => Swal.fire("Error", "No se pudo conectar con el servidor.", "error")
            });
        }

        // ---------- RESUMEN + PROYECCIÓN ----------
        function renderResumen() {
            const sumIni = lotesData.reduce((a, l) => a + n(l.cantidad_inicial), 0);
            const sumAct = lotesData.reduce((a, l) => {
                const c = cambios[l.id];
                return a + (c ? c.cantidad_actual : n(l.cantidad_actual));
            }, 0);
            $('#sum_inicial').text(fmt(sumIni));
            $('#sum_actual').text(fmt(sumAct));
            $('#stock_inv').text(fmt(inventarioData ? inventarioData.stock : 0));
            actualizarProyeccion();
        }

        function actualizarProyeccion() {
            const afecta = $('#toggleAfectarInventario').is(':checked');
            const stockActual = inventarioData ? n(inventarioData.stock) : 0;

            let delta = 0;
            const detalles = [];
            Object.values(cambios).forEach(c => {
                const lote = lotesData.find(l => l.id == c.id);
                if (!lote) return;
                const d = c.cantidad_actual - n(lote.cantidad_actual);
                if (d !== 0) {
                    delta += d;
                    detalles.push({ codigo: lote.codigo_lote, orig: n(lote.cantidad_actual), nuevo: c.cantidad_actual });
                }
            });

            $('#proyStockActual').text(fmt(stockActual));
            $('#proyDelta').text(fmtDelta(delta))
                .removeClass('positivo negativo neutro')
                .addClass(delta > 0 ? 'positivo' : (delta < 0 ? 'negativo' : 'neutro'));

            if (detalles.length) {
                const primeros = detalles.slice(0, 3).map(d => `${d.codigo}: ${d.orig}→${d.nuevo}`).join(' · ');
                const resto = detalles.length > 3 ? ` (+${detalles.length - 3} más)` : '';
                $('#proyDeltaDetalle').text(primeros + resto);
            } else {
                $('#proyDeltaDetalle').text('');
            }

            const stockFinal = afecta ? Math.max(0, stockActual + delta) : stockActual;
            $('#proyStockFinal').text(fmt(stockFinal))
                .removeClass('positivo negativo neutro')
                .addClass(!afecta ? 'neutro' : (delta > 0 ? 'positivo' : (delta < 0 ? 'negativo' : 'neutro')));

            $('#proyFinalLabel').text(afecta ? 'Stock resultante' : 'Stock resultante (sin cambio)');
            $('#proyFinalNota').text(
                afecta ? (delta === 0 ? 'No hay descuento pendiente' : '')
                    : '⚠️ El inventario no se modificará'
            );
            $('#cardProyeccion').toggleClass('desactivada', !afecta);
            $('#toggleLabel').text(afecta ? 'Sí afectar' : 'No afectar');
        }

        // ---------- TABLA ----------
        function renderTabla() {
            if (!lotesData.length) {
                $('#tablaLotes').html(
                    '<tr><td colspan="7" class="text-center text-body-secondary py-4">Sin lotes en el período</td></tr>'
                );
                return;
            }

            const productoNom = getProductoNom();
            let html = '';
            lotesData.forEach(lote => {
                const cambio = cambios[lote.id];
                const cantMostrar = cambio ? cambio.cantidad_actual : n(lote.cantidad_actual);
                const estMostrar = cambio ? cambio.estado_lote : lote.estado_lote;

                const eliminado = cambio && cambio.estado_lote === 'agotado' && cambio.cantidad_actual === 0;
                const cambiado = !!cambio;
                const rowClass = eliminado ? 'table-danger' : (cambiado ? 'table-warning' : '');
                const color = estMostrar === 'activo' ? 'primary' : 'secondary';

                let celdaActual, celdaEstado, acciones;

                if (modoEdicion) {
                    celdaActual = `
                    <input type="number"
                        class="form-control form-control-sm text-end input-existencias ms-auto"
                        min="0" step="1"
                        value="${cantMostrar}"
                        ${eliminado ? 'disabled' : ''}
                        onchange="marcarCambio(${lote.id}, this.value)"
                        onkeyup="if(event.key==='Enter'){this.blur();}">
                `;
                    celdaEstado = eliminado
                        ? `<span class="badge bg-danger">eliminado (pendiente)</span>`
                        : `<span class="badge bg-${color}">${estMostrar}</span>`;

                    acciones = eliminado
                        ? `<button class="btn btn-sm btn-outline-secondary" title="Restaurar"
                        onclick="restaurarLote(${lote.id})"><i class="bi bi-arrow-counterclockwise"></i></button>`
                        : `<button class="btn btn-sm btn-danger" title="Eliminar (agotado + stock 0)"
                        onclick="marcarEliminado(${lote.id})"><i class="bi bi-trash"></i></button>`;
                } else {
                    celdaActual = `<span class="fw-semibold">${fmt(lote.cantidad_actual)}</span>`;
                    celdaEstado = `<span class="badge bg-${color}">${lote.estado_lote}</span>`;
                    acciones = `<span class="text-muted small">—</span>`;
                }

                html += `<tr data-lote-id="${lote.id}" class="fila-lote ${rowClass}">
                <td>
                    <div class="fw-semibold">${lote.codigo_lote}</div>
                    <div class="small text-muted">${productoNom}</div>
                </td>
                <td>${lote.fecha_ingreso || '—'}</td>
                <td class="text-end">${fmt(lote.cantidad_inicial)}</td>
                <td class="text-end">${celdaActual}</td>
                <td class="text-end">$${fmt(lote.precio_compra_unitario)}</td>
                <td>${celdaEstado}</td>
                <td class="text-center">${acciones}</td>
            </tr>`;
            });
            $('#tablaLotes').html(html);
        }

        // ---------- CAMBIOS ----------
        function marcarCambio(loteId, valor) {
            const lote = lotesData.find(l => l.id == loteId);
            if (!lote) return;

            const orig = n(lote.cantidad_actual);
            const v = parseFloat(valor);
            if (isNaN(v) || v < 0) return;

            if (!cambios[loteId]) {
                cambios[loteId] = {
                    id: lote.id,
                    cantidad_actual: orig,
                    estado_lote: lote.estado_lote
                };
            }
            cambios[loteId].cantidad_actual = v;
            cambios[loteId].estado_lote = v > 0 ? 'activo' : 'agotado';

            if (cambios[loteId].cantidad_actual === orig && cambios[loteId].estado_lote === lote.estado_lote) {
                delete cambios[loteId];
            }

            const $row = $(`tr[data-lote-id="${loteId}"]`);
            if (cambios[loteId]) $row.addClass('table-warning').removeClass('table-danger');
            else $row.removeClass('table-warning table-danger');

            renderResumen();
            actualizarBotones();
        }

        function marcarEliminado(loteId) {
            const lote = lotesData.find(l => l.id == loteId);
            if (!lote) return;
            if (n(lote.cantidad_actual) === 0 && lote.estado_lote === 'agotado') {
                Swal.fire("Info", "Este lote ya está agotado.", "info");
                return;
            }
            Swal.fire({
                title: "¿Eliminar lote?",
                html: `Se marcará el lote <strong>${lote.codigo_lote}</strong> como
                   <strong>agotado</strong> con cantidad <strong>0</strong>.<br>
                   <small class="text-muted">Se descontará <strong>${fmt(lote.cantidad_actual)}</strong> del inventario (si aplica).</small>`,
                icon: "warning", showCancelButton: true,
                confirmButtonText: "Sí, eliminar", cancelButtonText: "Cancelar",
                confirmButtonColor: "#d33"
            }).then(r => {
                if (!r.isConfirmed) return;
                cambios[loteId] = { id: lote.id, cantidad_actual: 0, estado_lote: 'agotado' };
                renderTabla();
                renderResumen();
                actualizarBotones();
            });
        }

        function restaurarLote(loteId) {
            delete cambios[loteId];
            renderTabla();
            renderResumen();
            actualizarBotones();
        }

        // ---------- BOTONES ----------
        function actualizarBotones() {
            const total = Object.keys(cambios).length;
            $('#badgeCambios').text(total);
            $('#btnGuardar').prop('disabled', total === 0);

            const $btn = $('#btnModoEdicion');
            if (modoEdicion) {
                $btn.removeClass('btn-primary').addClass('btn-danger')
                    .html('<i class="bi bi-x-lg"></i> Salir de edición');
            } else {
                $btn.removeClass('btn-danger').addClass('btn-primary')
                    .html('<i class="bi bi-pencil-square"></i> Editar');
            }
        }

        // ---------- MODO EDICIÓN ----------
        function toggleModoEdicion() {
            if (!modoEdicion) {
                if (!getAlmacenId()) {
                    Swal.fire("Aviso", "Selecciona un almacén específico para editar.", "warning");
                    return;
                }
                if (!lotesData.length) {
                    Swal.fire("Aviso", "Primero consulta los lotes del producto.", "warning");
                    return;
                }
            }

            if (modoEdicion && Object.keys(cambios).length > 0) {
                Swal.fire({
                    title: "¿Descartar cambios?",
                    text: "Tienes cambios sin guardar. ¿Salir del modo edición y descartarlos?",
                    icon: "warning", showCancelButton: true,
                    confirmButtonText: "Sí, descartar", cancelButtonText: "Cancelar"
                }).then(r => {
                    if (r.isConfirmed) {
                        cambios = {};
                        modoEdicion = false;
                        renderTabla();
                        renderResumen();
                        actualizarBotones();
                    }
                });
                return;
            }

            modoEdicion = !modoEdicion;
            cambios = {};
            renderTabla();
            renderResumen();
            actualizarBotones();
        }

        // ---------- GUARDADO ----------
        function fetchAllLotes(producto, almacen) {
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: CONTROLLER_URL, type: 'GET',
                    data: {
                        action: 'obtenerLotesEInventario',
                        producto_id: producto,
                        almacen_id: almacen,
                        fecha_inicio: '',
                        fecha_fin: ''
                    },
                    dataType: 'json',
                    success: res => {
                        if (!res.success) return reject(res.message || 'Error');
                        resolve(res.data.lotes || []);
                    },
                    error: (xhr, st, err) => reject(err || st)
                });
            });
        }

        async function confirmarGuardado() {
            const total = Object.keys(cambios).length;
            if (!total) return;

            const almacen = getAlmacenId();
            const producto = getProductoId();
            if (!almacen || !producto) {
                Swal.fire("Aviso", "Falta almacén o producto.", "warning");
                return;
            }

            const afecta = $('#toggleAfectarInventario').is(':checked');

            let deltaTotal = 0;
            let filas = '';
            Object.values(cambios).forEach(c => {
                const lote = lotesData.find(l => l.id == c.id);
                if (!lote) return;
                const orig = n(lote.cantidad_actual);
                const d = c.cantidad_actual - orig;
                deltaTotal += d;

                const tipo = (c.cantidad_actual === 0 && orig > 0)
                    ? '<span class="badge bg-danger">eliminado</span>'
                    : '<span class="badge bg-warning text-dark">editado</span>';

                filas += `<tr>
            <td class="text-start">${lote.codigo_lote}</td>
            <td class="text-end">${fmt(orig)}</td>
            <td class="text-end">${fmt(c.cantidad_actual)}</td>
            <td class="text-end fw-bold ${d > 0 ? 'text-success' : (d < 0 ? 'text-danger' : '')}">${fmtDelta(d)}</td>
            <td>${tipo}</td>
        </tr>`;
            });

            const stockActual = inventarioData ? n(inventarioData.stock) : 0;
            const stockFinal = afecta ? Math.max(0, stockActual + deltaTotal) : stockActual;
            const bloqueInv = afecta
                ? `<div class="alert alert-success mb-0 mt-3">
             <div><strong>El inventario SÍ se modificará:</strong></div>
             <div>Stock actual: <strong>${fmt(stockActual)}</strong></div>
             <div>Descuento total: <strong>${fmtDelta(deltaTotal)}</strong></div>
             <div>Stock final: <strong>${fmt(stockFinal)}</strong></div>
           </div>`
                : `<div class="alert alert-secondary mb-0 mt-3">
             <strong>⚠️ El inventario NO se modificará.</strong><br>
             <small>El stock se mantendrá en <strong>${fmt(stockActual)}</strong>.</small>
           </div>`;

            const confirm = await Swal.fire({
                title: "¿Guardar cambios?",
                html: `<div class="text-start">
            <div class="table-responsive">
                <table class="table table-sm table-bordered mb-0">
                    <thead class="table-light"><tr>
                        <th class="text-start">Lote</th>
                        <th class="text-end">Anterior</th>
                        <th class="text-end">Nuevo</th>
                        <th class="text-end">Delta</th>
                        <th>Tipo</th>
                    </tr></thead>
                    <tbody>${filas}</tbody>
                </table>
            </div>
            ${bloqueInv}
        </div>`,
                icon: "question",
                showCancelButton: true,
                confirmButtonText: "Sí, guardar",
                cancelButtonText: "Cancelar",
                width: 720
            });
            if (!confirm.isConfirmed) return;

            Swal.fire({
                title: "Guardando...",
                didOpen: () => Swal.showLoading(),
                allowOutsideClick: false
            });

            try {
                // Solo los lotes modificados o eliminados, con su estructura mínima
                const payloadLotes = Object.values(cambios).map(c => ({
                    id: parseInt(c.id, 10),
                    cantidad_actual: n(c.cantidad_actual),
                    estado_lote: c.estado_lote
                }));

                // Si se afecta inventario, envía el número resultante; si no, envía null
                const inventarioPayload = afecta ? stockFinal : null;

                $.ajax({
                    url: CONTROLLER_URL + '?action=actualizarLotesEInventario',
                    type: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify({
                        almacen_id: almacen,
                        producto_id: producto,
                        lotes: payloadLotes,
                        inventario: inventarioPayload
                    }),
                    dataType: 'json',
                    success: function (res) {
                        if (!res.success) {
                            Swal.fire("Error", res.message || "No se pudo guardar.", "error");
                            return;
                        }

                        const r = res.data || {};
                        const seModificoInv = afecta && (r.stock_final !== undefined);
                        const bloque = seModificoInv
                            ? `<p class="mb-1">Stock anterior: <strong>${r.stock_anterior}</strong></p>
                       <p class="mb-1">Δ aplicado: <strong>${fmtDelta(r.delta_inventario)}</strong></p>
                       <p class="mb-0">Stock final: <strong>${r.stock_final}</strong></p>`
                            : `<p class="mb-1">Stock sin cambio: <strong>${r.stock_final ?? stockActual}</strong></p>
                       <p class="mb-0 small text-muted">El inventario no fue modificado.</p>`;

                        Swal.fire({
                            title: "¡Cambios guardados!",
                            html: `<div class="text-start">
                        <p class="mb-2">Lotes actualizados: <strong>${(r.cambios || []).length}</strong></p>
                        ${bloque}
                    </div>`,
                            icon: "success"
                        }).then(() => {
                            cambios = {};
                            modoEdicion = false;
                            actualizarBotones();
                            _doCargar();
                        });
                    },
                    error: (xhr, st, err) =>
                        Swal.fire("Error", "Error de conexión: " + (err || st), "error")
                });
            } catch (e) {
                Swal.fire("Error", "No se pudo preparar el envío: " + e, "error");
            }
        }
        // ---------- EVENTOS ----------
        $('#filtroAlmacen').on('change', function () {
            if (modoEdicion && Object.keys(cambios).length > 0) {
                Swal.fire({
                    title: "Cambiaste de almacén",
                    text: "Los cambios pendientes se descartarán.",
                    icon: "warning", timer: 1600, showConfirmButton: false
                });
                cambios = {};
                modoEdicion = false;
            }

            lotesData = [];
            inventarioData = null;
            renderTabla();
            renderResumen();
            actualizarBotones();

            // ÚNICA fuente de verdad para llenar #filtroProducto
            recargarProductosEditar();
        });

        $('#filtroProducto').on('change', function () {
            if (modoEdicion && Object.keys(cambios).length > 0) {
                Swal.fire({
                    title: "Cambiaste de producto",
                    text: "Los cambios pendientes se descartarán.",
                    icon: "warning", timer: 1600, showConfirmButton: false
                });
                cambios = {};
                modoEdicion = false;
                actualizarBotones();
            }
            lotesData = [];
            inventarioData = null;
            renderTabla();
            renderResumen();
        });

        // ---------- INIT ----------
        $(document).ready(function () {
            $('#filtroProducto').select2({
                placeholder: 'Buscar producto...',
                allowClear: true,
                width: '100%'
            });

            // Carga inicial del select de productos
            if ($('#filtroAlmacen').length) {
                // Admin: si ya hay un almacén preseleccionado (> 0), cargar productos
                if (parseInt($('#filtroAlmacen').val()) > 0) {
                    recargarProductosEditar();
                } else {
                    $('#filtroProducto')
                        .empty()
                        .append(new Option('Selecciona almacén primero...', '', true, true))
                        .trigger('change');
                }
            } else if (ALMACEN_USUARIO > 0) {
                // No-admin: usar el almacén del usuario
                recargarProductosEditar();
            }
        });
    </script>
</body>

</html>