

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Almacenes | Sistema</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    
    <link href="/cfsistem/css/style.css" rel="stylesheet"> 
    
    <link href="/cfsistem/css/almacenes.css" rel="stylesheet">
    <?php 
    // Llamamos a la función que imprime Bootstrap y layout.css
    if (function_exists('cargarEstilos')) {
        cargarEstilos(); 
    }
    ?>
    
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

    <?php renderizarLayout($paginaActual); ?>

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



    <div class="modal fade" id="modalCategoria" tabindex="-1" aria-labelledby="modalCategoriaLabel" aria-hidden="true"
        style="z-index: 1060;">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content shadow-lg">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modalCategoriaLabel">Nueva Categoría</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form id="formNuevaCategoria">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nombre de la categoría</label>
                            <input type="text" name="nombre" class="form-control" placeholder="Escribe el nombre..."
                                required>
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
            didOpen: () => {
                Swal.showLoading();
            }
        });

        fetch('/cfsistem/app/backend/almacen/guardar_categoria.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(datos)
            })
            .then(res => res.json())
            .then(res => {
                if (res.status === 'success') {
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
                        <select name="almacen_origen_id" id="origen_id" class="form-select border-primary" required onchange="filtrarProductosPorOrigen()">
                            <option value="">Seleccione donde sale la mercancía...</option>
                            <?php foreach($almacenes as $alm): ?>
                                <option value="<?= $alm['id'] ?>"><?= htmlspecialchars($alm['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">2. Producto a mover</label>
                        <select name="producto_id" id="traspaso_producto" class="form-select" required disabled onchange="actualizarMaximo()">
                            <option value="">Primero seleccione un origen...</option>
                        </select>
                        <div id="info_stock" class="form-text text-primary fw-bold"></div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label fw-bold">3. Almacén Destino</label>
                            <select name="almacen_destino_id" id="destino_id" class="form-select" required>
                                <option value="">¿A dónde va la mercancía?</option>
                                <?php foreach($todosLosAlmacenes as $alm_dest): ?>
                                    <?php if ($almacen_usuario > 0 && $alm_dest['id'] == $almacen_usuario) continue; ?>
                                    <option value="<?= $alm_dest['id'] ?>"><?= htmlspecialchars($alm_dest['nombre']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-12 mb-3">
                            <label class="form-label fw-bold">4. Cantidad a Traspasar</label>
                            <div class="input-group">
                                <input type="number" id="traspaso_factor_input" class="form-control text-center" placeholder="0" min="0">
                                <span class="input-group-text" id="label_unidad_reporte" style="min-width: 80px;">Unid.</span>
                                
                                <input type="number" id="traspaso_piezas_input" class="form-control text-center" placeholder="0" min="0" step="any">
                                <span class="input-group-text">Pzas.</span>
                            </div>
                            
                            <input type="hidden" name="cantidad" id="cantidad_traspaso_final" required>

                            <div id="resumen_conversion" class="mt-2 p-2 rounded bg-light border-start border-4 border-primary" style="display:none; font-size: 0.9rem;">
                                <strong>Movimiento total:</strong> <span id="txt_total_pzas">0</span> piezas.
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
                    <button type="submit" class="btn btn-primary" id="btnGuardarTraspaso">Solicitar Movimiento</button>
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
 
    <div class="modal fade" id="modalAgregarProducto" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content shadow-lg border-0">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title"><i class="bi bi-box-seam me-2"></i> Nuevo Producto y Entrada de Almacén</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <form id="formAgregarProducto" action="/cfsistem/app/backend/almacen/guardar_producto.php"
                    method="POST">
                    <div class="modal-body p-4">

                        <h6 class="fw-bold mb-3 text-success border-bottom pb-2">Información General</h6>
                        <div class="row mb-3 g-3">
                            <div class="col-md-2">
                                <label class="form-label small fw-bold">SKU</label>
                                <input type="text" name="sku" class="form-control" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Nombre del Producto</label>
                                <input type="text" name="nombre" class="form-control" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-bold">Categoría</label>
                                <select name="categoria_id" class="form-select" required>
                                    <option value="">Seleccionar...</option>
                                    <?php foreach($categorias as $cat): ?>
                                    <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['nombre']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-bold">Unidad de Medida (Venta/Base)</label>
                                <input type="text" name="unidad_medida" class="form-control"
                                    placeholder="Ej: Bulto, PZA" required>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label small fw-bold">Descripción Corta</label>
                                <input type="text" name="description" class="form-control"
                                    placeholder="Detalles adicionales del producto...">
                            </div>
                        </div>

                        <h6 class="fw-bold mb-3 text-primary border-bottom pb-2">Información Fiscal (SAT)</h6>
                        <div class="row mb-4 g-3">
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Clave SAT (Producto/Servicio)</label>
                                <input type="text" name="fiscal_clave_prod" class="form-control"
                                    placeholder="Ej: 43231500">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Clave Unidad SAT</label>
                                <input type="text" name="fiscal_clave_unit" class="form-control" placeholder="Ej: H87">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">IVA %</label>
                                <select name="impuesto_iva" class="form-select">
                                    <option value="16.00">16%</option>
                                    <option value="8.00">8%</option>
                                    <option value="0.00">0%</option>
                                    <option value="exento">Exento</option>
                                </select>
                            </div>
                        </div>

                        <div class="card bg-light border-warning mb-4 shadow-sm">
                            <div class="card-body">
                                <h6 class="fw-bold text-dark mb-3"><i class="bi bi-calculator-fill text-warning"></i>
                                    Control de Entrada y Conversión</h6>

                                <div class="row g-3 align-items-end">
                                    <div class="col-md-3">
                                        <label class="small fw-bold">Unidad de Compra (Reporte):</label>
                                        <input type="text" name="unidad_reporte" class="form-control border-warning"
                                            placeholder="Ej: Tonelada, Millar">
                                    </div>

                                    <div class="col-md-2">
                                        <label class="small fw-bold text-primary">Factor (Equivalencia):</label>
                                        <input type="number" id="inputFactor" name="factor_conversion"
                                            class="form-control border-primary fw-bold" value="1" step="0.01"
                                            oninput="actualizarLimiteMaestro()">
                                        <small class="text-muted" style="font-size: 0.6rem;">Ej: 40 bultos por
                                            Ton.</small>
                                    </div>

                                    <div class="col-md-3">
                                        <label class="small fw-bold text-danger">Cantidad Recibida:</label>
                                        <input type="number" id="inputLlegadaMaestra"
                                            class="form-control border-danger fw-bold" step="0.01" placeholder="0.00"
                                            oninput="actualizarLimiteMaestro()">
                                    </div>

                                    <div class="col-md-4 text-center">
                                        <div class="p-2 border rounded bg-white shadow-sm border-dark">
                                            <span class="small text-muted d-block text-uppercase fw-bold"
                                                style="font-size: 0.6rem;">Total Unidades Base a Repartir</span>
                                            <span id="displayLimiteBultos" class="fw-bold fs-4 text-dark">0.00</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="fw-bold mb-0 text-success"><i class="bi bi-houses-fill"></i> Distribución por
                                Almacén</h6>
                            <div class="badge bg-secondary p-2 shadow-sm" style="font-size: 0.9rem;">
                                Asignado: <span id="displayAsignado" class="fw-bold">0.00</span> |
                                Restante: <span id="displayRestante" class="fw-bold">0.00</span>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-sm table-bordered align-middle">
                                <thead class="table-dark small text-center">
                                    <tr>
                                        <th width="40">Act.</th>
                                        <th>Almacén</th>
                                        <th width="130">Stock Inicial</th>
                                        <th width="100">Stock Mín.</th>
                                        <th width="110">P. Minorista</th>
                                        <th width="110">P. Mayorista</th>
                                        <th width="110">P. Distribuidor</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($almacenes as $a): ?>
                                    <tr>
                                        <td class="text-center">
                                            <input type="checkbox" name="almacenes[<?= $a['id'] ?>][activo]" value="1"
                                                class="form-check-input" checked>
                                        </td>
                                        <td class="small fw-bold"><?= htmlspecialchars($a['nombre']) ?></td>
                                        <td>
                                            <input type="number" step="0.01" name="almacenes[<?= $a['id'] ?>][stock]"
                                                class="form-control form-control-sm input-calculo border-primary fw-bold text-center"
                                                oninput="validarReparto()" value="0">
                                        </td>
                                        <td>
                                            <input type="number" step="0.01"
                                                name="almacenes[<?= $a['id'] ?>][stock_minimo]"
                                                class="form-control form-control-sm text-center" placeholder="0">
                                        </td>
                                        <td><input type="number" step="0.01"
                                                name="almacenes[<?= $a['id'] ?>][precio_minorista]"
                                                class="form-control form-control-sm" placeholder="$"></td>
                                        <td><input type="number" step="0.01"
                                                name="almacenes[<?= $a['id'] ?>][precio_mayorista]"
                                                class="form-control form-control-sm" placeholder="$"></td>
                                        <td><input type="number" step="0.01"
                                                name="almacenes[<?= $a['id'] ?>][precio_distribuidor]"
                                                class="form-control form-control-sm" placeholder="$"></td>
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

<div class="modal fade" id="modalEditarProducto" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square"></i> Editar Producto: <span id="edit_nombre_titulo"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formEditarProducto">
                <input type="hidden" name="producto_id" id="edit_id">
                <input type="hidden" name="almacen_actual_id" id="edit_almacen_id">

                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4 border-end">
                            <h6 class="fw-bold text-primary mb-3">Datos Generales</h6>
                            <div class="mb-2">
                                <label class="small fw-bold">SKU</label>
                                <input type="text" name="sku" id="edit_sku" class="form-control" required>
                            </div>
                            <div class="mb-2">
                                <label class="small fw-bold">Nombre</label>
                                <input type="text" name="nombre" id="edit_nombre" class="form-control" required>
                            </div>
                            <div class="mb-2">
                                <label class="small fw-bold">Categoría</label>
                                <select name="categoria_id" id="edit_categoria" class="form-select">
                                    <?php foreach($categorias as $cat): ?>
                                    <option value="<?= $cat['id'] ?>"><?= $cat['nombre'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-2">
                                <label class="small fw-bold">Descripción</label>
                                <textarea name="descripcion" id="edit_descripcion" class="form-control" rows="2"></textarea>
                            </div>

                            <h6 class="fw-bold text-info mt-3 mb-2">Datos SAT</h6>
                            <div class="row g-2">
                                <div class="col-6">
                                    <label class="small">Clave Prod.</label>
                                    <input type="text" name="fiscal_clave_prod" id="edit_fiscal_clave_prod" class="form-control form-control-sm">
                                </div>
                                <div class="col-6">
                                    <label class="small">Clave Unidad</label>
                                    <input type="text" name="fiscal_clave_unidad" id="edit_fiscal_clave_unidad" class="form-control form-control-sm">
                                </div>
                                <div class="col-12">
                                    <label class="small">IVA (%)</label>
                                    <input type="number" step="0.01" name="impuesto_iva" id="edit_impuesto_iva" class="form-control form-control-sm">
                                </div>
                            </div>
                        </div>

                        <div class="col-md-8">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="fw-bold text-success m-0">Precios en: <span id="edit_almacen_nombre" class="badge bg-light text-dark"></span></h6>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="check_todos_almacenes" name="aplicar_global">
                                    <label class="form-check-label fw-bold text-danger small" for="check_todos_almacenes">¿Actualizar precios en TODOS los almacenes?</label>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4 mb-3"><label class="small fw-bold">Minorista</label><input type="number" step="0.01" name="precio_minorista" id="edit_p_min" class="form-control"></div>
                                <div class="col-md-4 mb-3"><label class="small fw-bold">Mayorista</label><input type="number" step="0.01" name="precio_mayorista" id="edit_p_may" class="form-control"></div>
                                <div class="col-md-4 mb-3"><label class="small fw-bold text-truncate">Distribuidor</label><input type="number" step="0.01" name="precio_distribuidor" id="edit_p_dist" class="form-control"></div>
                            </div>

                            <hr>
                            <h6 class="fw-bold text-dark">Unidades y Conversión</h6>
                            <div class="row">
                                <div class="col-md-4">
                                    <label class="small fw-bold">Unidad Compra</label>
                                    <input type="text" name="unidad_reporte" id="edit_unidad_reporte" class="form-control" placeholder="Ej: CAJA">
                                </div>
                                <div class="col-md-4">
                                    <label class="small fw-bold">Factor (Contenido)</label>
                                    <input type="number" step="0.01" name="factor_conversion" id="edit_factor_conversion" class="form-control border-primary fw-bold">
                                </div>
                                <div class="col-md-4">
                                    <label class="small fw-bold">Unidad Base</label>
                                    <input type="text" name="unidad_medida" id="edit_unidad_medida" class="form-control" placeholder="Ej: PIEZA">
                                </div>
                            </div>

                            <hr>
                            <h6 class="fw-bold text-secondary">Ajuste de Inventario (Este Almacén)</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="small fw-bold">Stock Actual</label>
                                    <input type="number" step="0.01" name="stock" id="edit_stock" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label class="small fw-bold">Stock Mínimo</label>
                                    <input type="number" step="0.01" name="stock_minimo" id="edit_s_min" class="form-control">
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
     <!-- --- Lógica de Control de Conversión --- -->
    <script src="/cfsistem/app/backend/js/calculo_de_conversion.js"></script>
    <script src="/cfsistem/app/backend/js/editar_producto.js"></script>

 <script src="/cfsistem/app/backend/js/actualizar_producto.js"></script>
<script>
    let factorGlobal = 1;
let unidadGlobal = 'Unid.';

function filtrarProductosPorOrigen() {
    const origenId = document.getElementById('origen_id').value;
    const selectProd = document.getElementById('traspaso_producto');
    
    selectProd.innerHTML = '<option value="">Seleccione producto...</option>';
    
    if (!origenId) {
        selectProd.disabled = true;
        return;
    }

    // Filtramos los productos del array que viene de tu consulta PHP
    const disponibles = productosInventario.filter(p => p.almacen_id == origenId && p.stock > 0);

    if (disponibles.length > 0) {
        disponibles.forEach(p => {
            const option = document.createElement('option');
            option.value = p.id;
            option.text = `${p.sku} - ${p.nombre} (Stock: ${p.stock})`;
            
            // Guardamos los datos del modelo en el dataset del option
            option.dataset.max = p.stock;
            option.dataset.factor = p.factor_conversion;
            option.dataset.unidad = p.unidad_reporte || 'Unid.';
            
            selectProd.appendChild(option);
        });
        selectProd.disabled = false;
    } else {
        selectProd.innerHTML = '<option value="">Sin stock en este almacén</option>';
        selectProd.disabled = true;
    }
}

function actualizarMaximo() {
    const selectProd = document.getElementById('traspaso_producto');
    const selected = selectProd.options[selectProd.selectedIndex];
    
    if(selected.value !== "") {
        // Actualizamos variables de conversión
        factorGlobal = parseFloat(selected.dataset.factor) || 1;
        unidadGlobal = selected.dataset.unidad;
        const maxStock = parseFloat(selected.dataset.max);

        // Actualizamos interfaz
        document.getElementById('label_unidad_reporte').innerText = unidadGlobal;
        document.getElementById('info_stock').innerText = `Stock disponible: ${maxStock} piezas`;
        document.getElementById('resumen_conversion').style.display = 'block';
        
        // Limpiamos inputs
        document.getElementById('traspaso_factor_input').value = '';
        document.getElementById('traspaso_piezas_input').value = '';
        calcularConversionTraspaso();
    }
}

function calcularConversionTraspaso() {
    const fVal = parseFloat(document.getElementById('traspaso_factor_input').value) || 0;
    const pVal = parseFloat(document.getElementById('traspaso_piezas_input').value) || 0;
    
    // El cálculo que va a la BD: (Factor * Valor Unidad) + Piezas
    const totalPiezas = (fVal * factorGlobal) + pVal;
    
    document.getElementById('cantidad_traspaso_final').value = totalPiezas;
    document.getElementById('txt_total_pzas').innerText = totalPiezas.toFixed(2);

    // Validación visual de stock
    const max = parseFloat(document.getElementById('traspaso_producto').options[document.getElementById('traspaso_producto').selectedIndex].dataset.max);
    const info = document.getElementById('info_stock');
    if(totalPiezas > max) {
        info.className = "form-text text-danger fw-bold";
        info.innerText = `⚠️ ¡Exceso! Solo hay ${max} piezas disponibles.`;
    } else {
        info.className = "form-text text-primary fw-bold";
        info.innerText = `Stock disponible: ${max} piezas`;
    }
}

// Lógica de "Brinco": Si pone 20 bultos y el factor es 20, se vuelve 1 Tonelada automáticamente
document.getElementById('traspaso_piezas_input').addEventListener('change', function() {
    let pzas = parseFloat(this.value) || 0;
    if (pzas >= factorGlobal && factorGlobal > 1) {
        let unidadesMas = Math.floor(pzas / factorGlobal);
        let restoPzas = pzas % factorGlobal;
        
        let inputFactor = document.getElementById('traspaso_factor_input');
        inputFactor.value = (parseFloat(inputFactor.value) || 0) + unidadesMas;
        this.value = restoPzas;
    }
    calcularConversionTraspaso();
});

document.getElementById('traspaso_factor_input').addEventListener('input', calcularConversionTraspaso);
document.getElementById('traspaso_piezas_input').addEventListener('input', calcularConversionTraspaso);
</script>

    
</body>

</html>