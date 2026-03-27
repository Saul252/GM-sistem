<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Ventas | Sistema</title>
      <?php require_once __DIR__ . '/layout/icono.php' ?>
    <?php if (function_exists('cargarEstilos')) { cargarEstilos(); } ?>
    <link href="/cfsistem/css/ventas.css" rel="stylesheet">
    <style>
    
:root {
    --primary-color: #007aff; /* Azul iOS */
    --success-color: #34c759; /* Verde iOS */
    --bg-light: #f5f5f7;
    --card-shadow: 0 8px 30px rgba(0, 0, 0, 0.05);
}

.main-content {
    background-color: var(--bg-light);
    padding: 40px;
    min-height: 100vh;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
}

/* Cambio de nombre del título sin tocar HTML */
.main-content h2.fw-bold {
    font-size: 1.8rem;
    letter-spacing: -0.5px;
    color: #1d1d1f;
    visibility: hidden; /* Escondemos el original */
    position: relative;
}

.main-content h2.fw-bold::after {
    content: "Caja Rápida"; /* El nuevo nombre */
    visibility: visible;
    position: absolute;
    left: 40px; /* Ajuste por el icono bi-cart-fill */
    top: 0;
}

.main-content h2.fw-bold i {
    visibility: visible;
    color: var(--primary-color) !important;
}

/* --- Cards Estilo Elegante --- */
.card {
    border: none !important;
    border-radius: 16px !important;
    box-shadow: var(--card-shadow) !important;
    background: #ffffff;
    transition: transform 0.2s ease;
}

/* --- Tabla de Productos --- */
.tabla-productos {
    border: none !important;
}

.tabla-productos thead th {
    background-color: #f8f9fa !important;
    color: #86868b !important;
    text-transform: uppercase;
    font-size: 0.7rem;
    font-weight: 700;
    letter-spacing: 0.5px;
    border: none !important;
    padding: 12px;
}

.tabla-productos tbody tr {
    border-bottom: 1px solid #f2f2f2;
    transition: all 0.2s;
}

.tabla-productos tbody tr:hover {
    background-color: #fafafa !important;
}

.tabla-productos td {
    padding: 14px 12px !important;
    vertical-align: middle;
    border: none !important;
}

/* Inputs y Selects más limpios */
.form-control, .form-select {
    border: 1px solid #d2d2d7 !important;
    border-radius: 10px !important;
    font-size: 0.9rem;
    padding: 0.6rem;
}

.form-control:focus, .form-select:focus {
    border-color: var(--primary-color) !important;
    box-shadow: 0 0 0 4px rgba(0, 122, 255, 0.1) !important;
}

/* --- Carrito de Compras Lateral --- */
.carrito {
    position: sticky;
    top: 20px;
    border-top: 4px solid var(--success-color) !important;
}

#tablaCarrito thead th {
    font-size: 0.65rem;
    color: #86868b;
    border-bottom: 1px solid #eee;
}

#tablaCarrito td {
    font-size: 0.85rem;
    padding: 8px 4px;
}

#total {
    color: var(--primary-color);
}

/* --- Botones --- */
.btn-primary {
    background-color: var(--primary-color) !important;
    border: none !important;
    border-radius: 12px !important;
    padding: 10px 20px;
    font-weight: 600;
}

.btn-success {
    background-color: var(--success-color) !important;
    border: none !important;
    border-radius: 10px !important;
    font-weight: 600;
}

.btn-sm {
    padding: 5px 10px;
}

/* --- Modal Estilo Apple --- */
.modal-content {
    border-radius: 20px !important;
    overflow: hidden;
}

.modal-header {
    border-bottom: 1px solid #f2f2f2 !important;
    padding: 1.5rem !important;
}

.bg-dark {
    background-color: #1d1d1f !important;
}

.badge {
    border-radius: 6px !important;
    padding: 5px 8px !important;
    font-weight: 500 !important;
}

/* Scroll personalizado */
.tabla-scroll {
    max-height: 600px;
    overflow-y: auto;
}

.tabla-scroll::-webkit-scrollbar {
    width: 6px;
}

.tabla-scroll::-webkit-scrollbar-thumb {
    background: #d2d2d7;
    border-radius: 10px;
}

/* --- Efecto de Botón Flotante para el total --- */
.bg-primary.bg-opacity-10 {
    background-color: rgba(0, 122, 255, 0.05) !important;
    border: 1px dashed var(--primary-color) !important;
}
/* --- Corrección de Superposición de Modales --- */
/* --- Elevación de SweetAlert por encima de los modales --- */
.swal2-container {
    z-index: 2000 !important; /* Lo mandamos muy por encima del 1061 de los modales */
}

/* Ajuste preventivo para el fondo oscuro de los modales */
.modal-backdrop {
    z-index: 1050 !important; /* Mantenlo bajo para que no tape los modales activos */
}

/* Modal base */
#modalFinalizarVenta {
    z-index: 1055 !important;
}

/* Modal secundario (el que se abre después) */
#modalNuevoCliente {
    z-index: 1061 !important;
}
    </style>
</head>

<body>

    <?php renderizarLayout($paginaActual); ?>

    <div class="main-content">

        <h2 class="mb-4 fw-bold">
            <i class="bi bi-cart-fill text-primary"></i> Módulo de Ventas
        </h2>

        <div class="row">
            <div class="col-lg-8">
                <div class="card p-3">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <select id="filtroCategoria" class="form-select">
                                <option value="">Todas las categorías</option>
                                <?php foreach($categorias as $cat): ?>
                                <option value="<?= $cat['id'] ?>">
                                    <?= htmlspecialchars($cat['nombre']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <select id="filtroAlmacen" class="form-select"
                                <?= ($almacen_usuario > 0) ? 'disabled' : '' ?>>
                                <?php if($almacen_usuario == 0): ?>
                                <option value="">Todos los almacenes</option>
                                <?php endif; ?>

                                <?php foreach($almacenes as $alm): ?>
                                <option value="<?= $alm['id'] ?>"
                                    <?= ($almacen_usuario == $alm['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($alm['nombre']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <input type="text" id="buscador" class="form-control" placeholder="🔎 Buscar producto...">
                        </div>
                    </div>

                    <div class="table-responsive tabla-scroll">
                        <table class="table table-bordered table-hover tabla-productos">
                            <thead class="table-dark">
                                <tr>
                                    <th>SKU</th>
                                    <th>Producto</th>
                                    <th>Stock</th>
                                    <th>Almacén</th>
                                    <th>Precio</th>
                                    <th width="120">Venta por</th>
                                    <th width="90">Cant</th>
                                    <th width="60"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($productos as $p): 
                                    $tieneReporte = (!empty($p['unidad_reporte']) && $p['factor_conversion'] > 1);
                                ?>
                                <tr data-categoria="<?= $p['categoria_id'] ?>" data-almacen="<?= $p['almacen_id'] ?>"
                                    data-factor="<?= $p['factor_conversion'] ?>"
                                    data-reporte-nom="<?= htmlspecialchars($p['unidad_reporte']) ?>">

                                    <td><?= $p['sku'] ?></td>
                                    <td><?= htmlspecialchars($p['nombre']) ?></td>
                                    <td>
                                        <span class="badge bg-success"><?= $p['stock'] ?></span>
                                        <small class="d-block text-muted" style="font-size: 0.65rem;">
                                            <?= htmlspecialchars($p['unidad_medida'] ?? 'unid.') ?>
                                        </small>
                                    </td>
                                    <td><?= htmlspecialchars($p['almacen_nombre']) ?></td>

                                    <td>
                                        <select class="form-select form-select-sm select-precio">
                                            <option value="<?= $p['precio_minorista'] ?>">Minorista -
                                                $<?= number_format($p['precio_minorista'],2) ?></option>
                                            <option value="<?= $p['precio_mayorista'] ?>">Mayorista -
                                                $<?= number_format($p['precio_mayorista'],2) ?></option>
                                            <option value="<?= $p['precio_distribuidor'] ?>">Distribuidor -
                                                $<?= number_format($p['precio_distribuidor'],2) ?></option>
                                        </select>
                                    </td>

                                    <td>
                                        <?php if($tieneReporte): ?>
                                        <select class="form-select form-select-sm select-modo-venta">
                                            <option value="individual">
                                                <?= htmlspecialchars($p['unidad_medida'] ?? 'Individual') ?></option>
                                            <option value="referencia"><?= htmlspecialchars($p['unidad_reporte']) ?>
                                            </option>
                                        </select>
                                        <?php else: ?>
                                        <span class="text-muted small">Individual</span>
                                        <?php endif; ?>
                                    </td>

                                    <td>
                                        <input type="number" class="form-control form-control-sm cantidad" min="1"
                                            max="<?= $p['stock'] ?>" value="1">
                                    </td>

                                    <td class="text-center">
                                        <button type="button" class="btn btn-success btn-sm"
                                            data-producto-id="<?= $p['id'] ?>" data-almacen-id="<?= $p['almacen_id'] ?>"
                                            data-almacen="<?= htmlspecialchars($p['almacen_nombre']) ?>"
                                            onclick="validarYAgregar(this)">
                                            <i class="bi bi-plus"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card p-3 carrito">
                    <h5 class="fw-bold mb-3"><i class="bi bi-bag-fill text-success"></i> Carrito</h5>
                    <div class="table-responsive">
                        <table class="table table-sm" id="tablaCarrito">
                            <thead>
                                <tr>
                                    <th>Almacén</th>
                                    <th>Producto</th>
                                    <th>Cant. Fact</th>
                                    <th>Cant. Pza</th>
                                    <th>Sub</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                    <hr>
                    <h4 class="text-end fw-bold">Total: $<span id="total">0.00</span></h4>
                    <button class="btn btn-primary w-100 mt-3" onclick="abrirModalFinalizar()">
                        <i class="bi bi-cash-stack"></i> Finalizar Venta
                    </button>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="modalNuevoCliente" tabindex="-1" aria-labelledby="modalNuevoClienteLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="formNuevoCliente">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="modalNuevoClienteLabel">
                            <i class="fas fa-user-plus me-2"></i>Registrar Nuevo Cliente
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="almacen_id" value="<?= $almacen_usuario ?>">

                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label fw-bold">Nombre Comercial *</label>
                                <input type="text" name="nombre_comercial" class="form-control"
                                    placeholder="Ej. Materiales El Centro" required>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label fw-bold">Razón Social</label>
                                <input type="text" name="razon_social" class="form-control"
                                    placeholder="Nombre legal completo">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">RFC *</label>
                                <input type="text" name="rfc" class="form-control text-uppercase" maxlength="13"
                                    placeholder="ABCD000000XXX" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Código Postal *</label>
                                <input type="text" name="codigo_postal" class="form-control" maxlength="5"
                                    placeholder="00000" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Régimen Fiscal</label>
                                <input type="text" name="regimen_fiscal" class="form-control" maxlength="3"
                                    placeholder="Ej. 601">
                                <small class="text-muted">Clave del catálogo del SAT</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Uso de CFDI</label>
                                <select name="uso_cfdi" class="form-select">
                                    <option value="G03" selected>G03 - Gastos en general</option>
                                    <option value="S01">S01 - Sin efectos fiscales</option>
                                    <option value="G01">G01 - Adquisición de mercancías</option>
                                    <option value="P01">P01 - Por definir</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Correo Electrónico</label>
                                <input type="email" name="correo" class="form-control" placeholder="cliente@correo.com">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Teléfono</label>
                                <input type="tel" name="telefono" class="form-control" placeholder="55 0000 0000">
                            </div>

                            <div class="col-md-12">
                                <label class="form-label fw-bold">Dirección Completa</label>
                                <textarea name="direccion" class="form-control" rows="2"
                                    placeholder="Calle, número, colonia..."></textarea>
                            </div>
                            <div class="row g-3">
                                <?php if ($almacen_usuario == 0): ?>
                                <div class="col-md-12 mb-2">
                                    <label class="form-label fw-bold text-primary">Asignar a Almacén *</label>
                                    <select name="almacen_id" class="form-select border-primary" required>
                                        <option value="">-- Selecciona un almacén --</option>
                                        <?php foreach ($almacenes as $alm): ?>
                                        <option value="<?= $alm['id'] ?>"><?= htmlspecialchars($alm['nombre']) ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <small class="text-muted">Como administrador, debes elegir a qué sucursal pertenece
                                        este cliente.</small>
                                </div>
                                <?php else: ?>
                                <input type="hidden" name="almacen_id" value="<?= $almacen_usuario ?>">
                                <?php endif; ?>

                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary" id="btnGuardarCliente">
                            <i class="fas fa-save me-1"></i> Guardar Cliente
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php cargarScripts(); ?>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="/cfsistem/app/backend/js_ventas/carrito.js"></script>
    <script src="/cfsistem/app/backend/js_ventas/filtros.js"></script>
    <script src="/cfsistem/app/backend/js_ventas/nuevo_cliente.js"></script>
<?php require_once __DIR__ . '/cajaRapida/ModalFinalizarVenta.php'; ?>


</body>

</html>