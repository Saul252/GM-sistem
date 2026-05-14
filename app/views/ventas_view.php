<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Ventas | Sistema</title>
    <?php require_once __DIR__ . '/layout/icono.php' ?>
    <?php if (function_exists('cargarEstilos')) { cargarEstilos(); } ?>
    <link href="/cfsistem/css/ventas.css" rel="stylesheet">
    <style>
    body {
        padding-top: 20px;
    }

    .tabla-scroll {
        max-height: 60vh;
        overflow-y: auto;
    }

    .carrito {
        position: sticky;
        top: 85px;
    }

    .badge-stock {
        font-size: 0.8rem;
        padding: 5px 10px;
    }

    .modalc {
        z-index: 2000 !important;
    }

    .swal-zindex {
        z-index: 2000 !important;
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
                                            <option value="<?= $p['precio_minorista'] ?>">Publico -
                                                $<?= number_format($p['precio_minorista'],2) ?></option>
                                            <option value="<?= $p['precio_mayorista'] ?>">Constructora -
                                                $<?= number_format($p['precio_mayorista'],2) ?></option>
                                            <option value="<?= $p['precio_distribuidor'] ?>">Distribuidor -
                                                $<?= number_format($p['precio_distribuidor'],2) ?></option>
                                        </select>
                                    </td>

                            
<td style="width:1px; padding:0; border:none;">
    <?php if($tieneReporte): ?>
    <select 
        class="form-select form-select-sm select-modo-venta"
        style="
            opacity:0;
            position:absolute;
            pointer-events:none;
            height:0;
            width:0;
            padding:0;
            border:0;
        ">

        <option value="individual"
            data-nombre="<?= htmlspecialchars($p['unidad_medida'] ?? 'PZA') ?>">

            <?= htmlspecialchars($p['unidad_medida'] ?? 'PZA') ?>

        </option>

        <option value="referencia"
            data-nombre="<?= htmlspecialchars($p['unidad_reporte']) ?>">

            <?= htmlspecialchars($p['unidad_reporte']) ?>

        </option>

    </select>
    <?php else: ?>
    <span class="d-none">Individual</span>
    <?php endif; ?>
                      
                                        <select class="form-select border-primary medidas_adicionales"
                                            <?= empty($p['medidas_adicionales']) ? 'disabled' : '' ?>>
                                              <option value='0'>Seleccione</option>
                                            <?php foreach($p['medidas_adicionales'] as $ma): ?>
                                            <option value="<?= $ma['equivalencia'] ?>" data-id="<?= $ma['id'] ?> "
                                                data-nombre="<?= $ma['nombre'] ?>">
                                                <?= htmlspecialchars($ma['nombre']) ?>
                                            </option>
                                            <?php endforeach; ?>
                                        </select>

                                    </td>
                                    <td>
                                        <!-- usuario -->
                                        <input type="number" class="form-control form-control-sm cantidad_usuario"
                                            min="1" value="1">
                                        <!-- REAL -->
                                        <input type="hidden" class="cantidad" value="0">
                                        <!-- visible -->
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



    <div class="modal fade modalc" id="modalNuevoCliente" tabindex="-1" aria-labelledby="modalNuevoClienteLabel"
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
    <?php require_once __DIR__ . '/ventasModales/finalizarVenta.php'; ?>

    <?php cargarScripts(); ?>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script src="/cfsistem/app/backend/js_ventas/filtros.js"></script>
    <script src="/cfsistem/app/backend/js_ventas/nuevo_cliente.js"></script>
    <script src="/cfsistem/app/backend/js_ventas/modal_finalizar.js"></script>



    <script>
    /**
     * Soporte para agregar productos al presionar ENTER
     */
    document.addEventListener('keydown', function(e) {
        // 1. Verificamos que la tecla sea Enter y que el foco esté en un input de cantidad
        if (e.key === 'Enter' && e.target.classList.contains('cantidad_usuario')) {

            // Evitamos que el Enter haga un submit accidental del formulario principal
            e.preventDefault();

            // 2. Localizamos la fila (tr) donde se presionó Enter
            const fila = e.target.closest('tr');

            // 3. Buscamos el botón de "Agregar" (+) en esa misma fila
            const btnAgregar = fila.querySelector('button.btn-success');

            if (btnAgregar) {
                // 4. Ejecutamos la función de agregar que ya tienes definida
                validarYAgregar(btnAgregar);

                // Opcional: Feedback visual rápido para el usuario
                btnAgregar.style.transform = "scale(0.9)";
                setTimeout(() => btnAgregar.style.transform = "scale(1)", 100);
            }
        }
    });
    </script>
    <script>
    document.addEventListener('input', actualizarCantidadReal);
    document.addEventListener('change', actualizarCantidadReal);

    function actualizarCantidadReal(e) {

        const fila = e.target.closest('tr');

        if (!fila) return;

        const inputUsuario =
            fila.querySelector('.cantidad_usuario');
            

        const inputReal =
            fila.querySelector('.cantidad');

        const selectMedida =
            fila.querySelector('.medidas_adicionales');

        if (!inputUsuario || !inputReal) return;

        const cantidadUsuario =
            parseFloat(inputUsuario.value) || 0;

        const equivalencia =
            parseFloat(selectMedida?.value) || 0;
  

        // REAL
        const totalReal =
            cantidadUsuario / equivalencia;

        inputReal.value = totalReal.toFixed(6);

        console.log({
            usuario: cantidadUsuario,
            equivalencia,
            real: totalReal
        });
    }
    </script>
</body>

</html>