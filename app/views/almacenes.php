<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../../includes/auth.php';
protegerPagina();

require_once __DIR__ . '/../../includes/sidebar.php';
require_once __DIR__ . '/../../config/conexion.php';

$paginaActual = 'Almacenes';
// Capturamos el almacén de la sesión (0 = Admin)
$almacen_usuario = $_SESSION['almacen_id'] ?? 0;

/* ================= CATEGORIAS ================= */
$catQuery = $conexion->query("SELECT id, nombre FROM categorias ORDER BY nombre ASC");
$categorias = [];
while ($row = $catQuery->fetch_assoc()) { $categorias[] = $row; }

/* ================= ALMACENES (Filtrado por Permiso) ================= */
$sqlAlm = "SELECT * FROM almacenes WHERE activo = 1";
if ($almacen_usuario > 0) {
    $sqlAlm .= " AND id = " . intval($almacen_usuario);
}
$sqlAlm .= " ORDER BY nombre ASC";

$almacenesQuery = $conexion->query($sqlAlm);
$almacenes = [];
while ($row = $almacenesQuery->fetch_assoc()) { $almacenes[] = $row; }

/* ================= PRODUCTOS (Filtrado por Permiso) ================= */
$sql = "SELECT 
    p.id, p.sku, p.nombre, p.categoria_id,
    c.nombre AS categoria_nombre,
    i.stock, i.almacen_id, a.nombre AS almacen_nombre
FROM inventario i
INNER JOIN productos p ON i.producto_id = p.id
INNER JOIN almacenes a ON i.almacen_id = a.id
LEFT JOIN categorias c ON p.categoria_id = c.id
WHERE p.activo = 1";

// Si NO es admin, solo ve el inventario de su almacén asignado
if ($almacen_usuario > 0) {
    $sql .= " AND i.almacen_id = " . intval($almacen_usuario);
}

$sql .= " ORDER BY p.nombre ASC";

$result = $conexion->query($sql);
$productos = [];
while($row = $result->fetch_assoc()){ $productos[] = $row; }
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Almacenes | Sistema</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="/cfsistem/css/almacenes.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>

    <?php renderSidebar($paginaActual); ?>

    <div class="main-content">
        <h2 class="mb-4 fw-bold">
            <i class="bi bi-box-seam text-primary"></i> Módulo de Almacén
        </h2>

        <div class="card p-3 shadow-sm">
            <div class="row mb-3 g-2 align-items-center">
                <div class="col-md-2">
                    <select id="filtroCategoria" class="form-select">
                        <option value="">Categorías</option>
                        <?php foreach($categorias as $cat): ?>
                        <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['nombre']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

               <div class="col-md-2">
    <select id="filtroAlmacen" class="form-select" <?= ($almacen_usuario > 0) ? 'disabled' : '' ?>>
        <?php if($almacen_usuario == 0): ?>
            <option value="">Todos los Almacenes</option>
        <?php endif; ?>
        
        <?php foreach($almacenes as $alm): ?>
            <option value="<?= $alm['id'] ?>" <?= ($almacen_usuario == $alm['id']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($alm['nombre']) ?>
            </option>
        <?php endforeach; ?>
    </select>
</div>
                <div class="col-md-3">
                    <input type="text" id="buscador" class="form-control" placeholder="🔎 Buscar...">
                </div>

                <div class="col-md-5">
                    <div class="d-flex gap-2">
                        <button class="btn btn-success w-100 flex-fill" data-bs-toggle="modal"
                            data-bs-target="#modalAgregarProducto">
                            <i class="bi bi-plus-lg"></i> Producto
                        </button>

                        <button class="btn btn-dark w-100 flex-fill" data-bs-toggle="modal"
                            data-bs-target="#modalTraspaso">
                            <i class="bi bi-arrow-left-right"></i> Traspaso
                        </button>

                        <button class="btn btn-primary w-100 flex-fill" data-bs-toggle="modal"
                            data-bs-target="#modalTraspasosGestion" onclick="cargarTraspasos()">
                            <i class="bi bi-shield-check"></i> Autorizar
                        </button>
                    </div>
                </div>
            </div>

            <div class="table-responsive tabla-scroll">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-dark sticky-header">
                        <tr>
                            <th>SKU</th>
                            <th>Producto</th>
                            <th>Categoría</th>
                            <th>Stock</th>
                            <th>Almacén</th>
                            <th width="60">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($productos as $p): ?>
                        <tr data-categoria="<?= $p['categoria_id'] ?>" data-almacen="<?= $p['almacen_id'] ?>">
                            <td class="fw-bold"><?= $p['sku'] ?></td>
                            <td><?= htmlspecialchars($p['nombre']) ?></td>
                            <td><span
                                    class="badge bg-light text-dark border"><?= htmlspecialchars($p['categoria_nombre'] ?? 'Sin Categoría') ?></span>
                            </td>
                            <td>
                                <?php 
                                $badgeClass = ($p['stock'] > 20) ? 'bg-success' : (($p['stock'] > 5) ? 'bg-warning text-dark' : 'bg-danger');
                                ?>
                                <span class="badge <?= $badgeClass ?> badge-stock"><?= $p['stock'] ?></span>
                            </td>
                            <td><?= htmlspecialchars($p['almacen_nombre'] ?? 'N/A') ?></td>
                            <td class="text-center">
                                <button class="btn btn-outline-warning btn-sm" 
        onclick="editarProducto(<?= $p['id'] ?>, <?= $p['almacen_id'] ?>)">
    <i class="bi bi-pencil"></i>
</button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

 

 <div class="modal fade" id="modalCategoria" tabindex="-1" aria-labelledby="modalCategoriaLabel" aria-hidden="true" style="z-index: 1060;">
    <div class="modal-dialog modal-dialog-centered"> <div class="modal-content shadow-lg"> <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalCategoriaLabel">Nueva Categoría</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formNuevaCategoria">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nombre de la categoría</label>
                        <input type="text" name="nombre" class="form-control" placeholder="Escribe el nombre..." required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar Categoría</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    /* Si abres este modal sobre otro, el backdrop debe estar un nivel abajo de este modal */
    .modal-backdrop:nth-of-type(even) {
        z-index: 1055 !important;
    }
</style>
<script>
document.getElementById('formNuevaCategoria').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const datos = Object.fromEntries(formData.entries());

    Swal.fire({
        title: 'Guardando...',
        didOpen: () => { Swal.showLoading(); }
    });

    fetch('/cfsistem/app/backend/almacen/guardar_categoria.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(datos)
    })
    .then(res => res.json())
    .then(res => {
        if(res.status === 'success') {
            Swal.fire('¡Éxito!', 'Categoría guardada correctamente', 'success').then(() => {
                // Si tienes un selector de categorías en la pantalla de productos, 
                // aquí podrías recargarlo o simplemente refrescar la página
                location.reload(); 
            });
        } else {
            Swal.fire('Error', res.message, 'error');
        }
    })
    .catch(err => {
        console.error(err);
        Swal.fire('Error', 'No se pudo conectar con el servidor', 'error');
    });
});
</script>
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
                                <?php foreach($almacenes as $alm): ?>
                                <option value="<?= $alm['id'] ?>"><?= htmlspecialchars($alm['nombre']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">2. Producto a mover</label>
                            <select name="producto_id" id="traspaso_producto" class="form-select" required disabled
                                onchange="actualizarMaximo()">
                                <option value="">Primero seleccione un origen...</option>
                            </select>
                            <div id="info_stock" class="form-text text-primary fw-bold"></div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">3. Almacén Destino</label>
                                <select name="almacen_destino_id" id="destino_id" class="form-select" required>
                                    <option value="">¿A dónde va?</option>
                                    <?php foreach($almacenes as $alm): ?>
                                    <option value="<?= $alm['id'] ?>"><?= htmlspecialchars($alm['nombre']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">4. Cantidad</label>
                                <input type="number" step="0.01" name="cantidad" id="cantidad_traspaso"
                                    class="form-control" required min="0.01">
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
                    <?php if($_SESSION['rol_id'] == 1): ?>
                    <div class="row mb-4 bg-light p-3 rounded border">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Ver movimientos del Almacén:</label>
                            <select id="admin_filtro_almacen" class="form-select" onchange="cargarTraspasos()">
                                <option value="">Seleccione un almacén para autorizar...</option>
                                <?php foreach($almacenes as $a): ?>
                                <option value="<?= $a['id'] ?>"><?= $a['nombre'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <?php endif; ?>

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
    <div class="modal fade" id="modalEditarProducto" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square"></i> Editar Producto: <span
                            id="edit_nombre_titulo"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="formEditarProducto">
                    <input type="hidden" name="producto_id" id="edit_id">
                    <input type="hidden" name="almacen_actual_id" id="edit_almacen_id">

                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-4 border-end">
                                <h6 class="fw-bold text-primary mb-3">Datos Generales (Global)</h6>
                                <div class="mb-2">
                                    <label class="form-label small">SKU</label>
                                    <input type="text" name="sku" id="edit_sku" class="form-control" required>
                                </div>
                                <div class="mb-2">
                                    <label class="form-label small">Nombre del Producto</label>
                                    <input type="text" name="nombre" id="edit_nombre" class="form-control" required>
                                </div>
                                <div class="mb-2">
                                    <label class="form-label small">Categoría</label>
                                    <select name="categoria_id" id="edit_categoria" class="form-select">
                                        <?php foreach($categorias as $cat): ?>
                                        <option value="<?= $cat['id'] ?>"><?= $cat['nombre'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-8">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="fw-bold text-success m-0">Precios en: <span id="edit_almacen_nombre"
                                            class="text-dark"></span></h6>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="check_todos_almacenes"
                                            name="aplicar_global">
                                        <label class="form-check-label fw-bold text-danger" for="check_todos_almacenes">
                                            ¿Aplicar estos precios a TODOS los almacenes?
                                        </label>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label small">Precio Minorista</label>
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="number" step="0.01" name="precio_minorista" id="edit_p_min"
                                                class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label small">Precio Mayorista</label>
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="number" step="0.01" name="precio_mayorista" id="edit_p_may"
                                                class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label small">Precio Distribuidor</label>
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="number" step="0.01" name="precio_distribuidor" id="edit_p_dist"
                                                class="form-control">
                                        </div>
                                    </div>
                                </div>

                                <hr>
                                <h6 class="fw-bold text-secondary">Ajuste de Inventario (Solo este almacén)</h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <label class="form-label small">Stock Actual</label>
                                        <input type="number" step="0.01" name="stock" id="edit_stock"
                                            class="form-control" readonly bg-light>
                                        <div class="form-text text-muted">Para mover stock use el módulo de Traspasos.
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small">Stock Mínimo</label>
                                        <input type="number" step="0.01" name="stock_minimo" id="edit_s_min"
                                            class="form-control">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-warning fw-bold">Guardar Cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
      <div class="modal fade" id="modalAgregarProducto" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content shadow-lg border-0">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i class="bi bi-box-seam me-2"></i> Nuevo Producto y Entrada de Almacén</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <form id="formAgregarProducto" action="/cfsistem/app/backend/almacen/guardar_producto.php" method="POST" onsubmit="confirmarEnvio(event)">
                <div class="modal-body p-4">
                    
                    <h6 class="fw-bold mb-3 text-success border-bottom pb-2">Información General</h6>
                    <div class="row mb-3 g-3">
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">SKU</label>
                            <input type="text" name="sku" class="form-control" required>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label small fw-bold">Nombre del Producto</label>
                            <input type="text" name="nombre" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Categoría</label>
                            <div class="input-group">
                                <select name="categoria_id" class="form-select" required>
                                    <option value="">Seleccionar...</option>
                                    <?php foreach($categorias as $cat): ?>
                                    <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['nombre']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <button type="button" class="btn btn-outline-primary" onclick="abrirModalCategoria()">
                                    <i class="bi bi-plus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Unidad de Medida (Base)</label>
                            <input type="text" name="unidad_medida" class="form-control" placeholder="Ej: PZA, KG">
                        </div>
                        <div class="col-md-8">
                            <label class="form-label small fw-bold">Descripción Corta</label>
                            <input type="text" name="description" class="form-control">
                        </div>
                    </div>

                    <h6 class="fw-bold mb-3 text-primary border-bottom pb-2">Información Fiscal (SAT)</h6>
                    <div class="row mb-4 g-3">
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">Clave SAT (Prod/Serv)</label>
                            <input type="text" name="fiscal_clave_prod" class="form-control" placeholder="Ej: 43231500">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">Clave Unidad SAT</label>
                            <input type="text" name="fiscal_clave_unit" class="form-control" placeholder="Ej: H87">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">IVA %</label>
                            <select name="impuesto_iva" class="form-select">
                                <option value="16.00">16%</option>
                                <option value="8.00">8%</option>
                                <option value="0.00">0%</option>
                                <option value="exento">Exento</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-danger">Costo de Adquisición</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" step="0.01" name="precio_adquisicion" class="form-control border-danger" required>
                            </div>
                        </div>
                    </div>

                    <div class="card bg-light border-warning mb-4 shadow-sm">
                        <div class="card-body">
                            <h6 class="fw-bold text-dark mb-3"><i class="bi bi-calculator-fill text-warning"></i> Control de Entrada Física y Conversión</h6>
                            
                            <div class="row g-3 align-items-end">
                                <div class="col-md-3">
                                    <label class="small fw-bold text-danger">Cantidad Recibida:</label>
                                    <div class="input-group">
                                        <input type="number" id="inputLlegadaMaestra" class="form-control border-danger fw-bold" step="0.01" placeholder="0.00" oninput="actualizarLimiteMaestro()">
                                        <select id="unidadMaestra" class="form-select border-danger" onchange="gestionarFactor()">
                                            <option value="1">PZA / Bultos</option>
                                            <option value="ton">Toneladas</option>
                                            <option value="kg">Kilos</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-3" id="columnaFactor" style="display: none;">
                                    <label class="small fw-bold text-primary" id="labelFactor">Factor de Conversión:</label>
                                    <div class="input-group">
                                        <input type="number" id="inputFactor" class="form-control border-primary fw-bold" value="20" oninput="actualizarLimiteMaestro()">
                                        <span class="input-group-text bg-primary text-white"><i class="bi bi-arrow-repeat"></i></span>
                                    </div>
                                </div>

                                <div class="col-md-3 text-center">
                                    <div class="p-2 border rounded bg-white shadow-sm">
                                        <span class="small text-muted d-block text-uppercase fw-bold" style="font-size: 0.6rem;">Máximo a Ingresar (Piezas)</span>
                                        <span id="displayLimiteBultos" class="fw-bold fs-5 text-dark">0.00</span>
                                    </div>
                                </div>

                                <div class="col-md-3 text-center">
                                    <div class="p-2 border rounded bg-white shadow-sm">
                                        <span class="small text-muted d-block text-uppercase fw-bold" style="font-size: 0.6rem;">Asignado / Restante</span>
                                        <div class="d-flex justify-content-center gap-1">
                                            <span id="displayAsignado" class="fw-bold fs-5 text-primary">0.00</span>
                                            <span class="fs-5 text-muted">/</span>
                                            <span id="displayRestante" class="fw-bold fs-5 text-secondary">0.00</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <h6 class="fw-bold mb-3 text-success border-bottom pb-2">Distribución por Almacén y Precios de Venta</h6>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered align-middle">
                            <thead class="table-dark">
                                <tr class="text-center small">
                                    <th width="40">Act.</th>
                                    <th>Almacén</th>
                                    <th width="140">Stock (Pzas)</th>
                                    <th width="100">Mínimo</th>
                                    <th>P. Minorista</th>
                                    <th>P. Mayorista</th>
                                    <th>P. Distribuidor</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($almacenes as $a): ?>
                                <tr>
                                    <td class="text-center">
                                        <input type="checkbox" name="almacenes[<?= $a['id'] ?>][activo]" value="1" class="form-check-input" checked>
                                    </td>
                                    <td class="small fw-bold"><?= htmlspecialchars($a['nombre']) ?></td>
                                    <td>
                                        <input type="number" step="0.01" name="almacenes[<?= $a['id'] ?>][stock]" 
                                               class="form-control form-control-sm input-calculo border-primary fw-bold" 
                                               oninput="validarReparto()">
                                    </td>
                                    <td><input type="number" step="0.01" name="almacenes[<?= $a['id'] ?>][stock_minimo]" class="form-control form-control-sm"></td>
                                    <td><input type="number" step="0.01" name="almacenes[<?= $a['id'] ?>][precio_minorista]" class="form-control form-control-sm" placeholder="$"></td>
                                    <td><input type="number" step="0.01" name="almacenes[<?= $a['id'] ?>][precio_mayorista]" class="form-control form-control-sm" placeholder="$"></td>
                                    <td><input type="number" step="0.01" name="almacenes[<?= $a['id'] ?>][precio_distribuidor]" class="form-control form-control-sm" placeholder="$"></td>
                                </tr>
                                <?php endforeach?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="submit" id="btnGuardarProducto" class="btn btn-success px-5 fw-bold shadow">
                        <i class="bi bi-save me-2"></i> GUARDAR PRODUCTO
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
// --- Lógica de Control de Conversión ---
<script src="/cfsistem/app/backend/js/calcular_unidades.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/cfsistem/app/backend/js/filtros_almacen.js"></script>
       <script src="/cfsistem/app/backend/js/guardar_producto.js"></script>
    <script>
    // Variable que guarda los datos de stock que ya tenemos en la tabla
    // Pasamos los datos de PHP a JS
    const productosInventario = <?php echo json_encode($productos); ?>;
    </script>
    <script src="/cfsistem/app/backend/js/informacion_productos_envio.js"></script>
    <script src="/cfsistem/app/backend/js/cargar_traspasos.js"></script>
    <script src="/cfsistem/app/backend/js/aceptar_arribo.js"></script>
 <script src="/cfsistem/app/backend/js/editar_producto.js">
    // Abrir modal y cargar datos
    </script>

</body>

</html>