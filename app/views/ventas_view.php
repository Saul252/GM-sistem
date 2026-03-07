<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Ventas | Sistema</title>
    <?php cargarEstilos(); ?>
    <link href="/cfsistem/css/ventas.css" rel="stylesheet">
    <style>
        body{
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
                                <?php while($cat = $categorias->fetch_assoc()): ?>
                                <option value="<?= $cat['id'] ?>">
                                    <?= htmlspecialchars($cat['nombre']) ?>
                                </option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <select id="filtroAlmacen" class="form-select"
                                <?= ($almacen_usuario > 0) ? 'disabled' : '' ?>>
                                <?php if($almacen_usuario == 0): ?>
                                <option value="">Todos los almacenes</option>
                                <?php endif; ?>

                                <?php 
                                // Reiniciamos el puntero del resultado de almacenes para el loop
                                $almacenes->data_seek(0); 
                                while($alm = $almacenes->fetch_assoc()): 
                                ?>
                                <option value="<?= $alm['id'] ?>"
                                    <?= ($almacen_usuario == $alm['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($alm['nombre']) ?>
                                </option>
                                <?php endwhile; ?>
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
                // Verificamos si tiene unidad de reporte válida (ej. Tonelada)
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
                                            <option value="<?= $p['precio_minorista'] ?>">
                                                Minorista - $<?= number_format($p['precio_minorista'],2) ?>
                                            </option>
                                            <option value="<?= $p['precio_mayorista'] ?>">
                                                Mayorista - $<?= number_format($p['precio_mayorista'],2) ?>
                                            </option>
                                            <option value="<?= $p['precio_distribuidor'] ?>">
                                                Distribuidor - $<?= number_format($p['precio_distribuidor'],2) ?>
                                            </option>
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

                    <script>
                    function validarYAgregar(btn) {
                        const fila = btn.closest('tr');
                        const modo = fila.querySelector('.select-modo-venta')?.value || 'individual';
                        const inputCant = fila.querySelector('.cantidad');
                        const factor = parseFloat(fila.dataset.factor) || 1;
                        const stockDisponible = parseFloat(fila.querySelector('.badge').innerText);

                        let cantidadOriginal = parseFloat(inputCant.value);
                        let cantidadAProcesar = cantidadOriginal;

                        // Si seleccionó Unidad de Reporte (ej. Tonelada), multiplicamos
                        if (modo === 'referencia') {
                            cantidadAProcesar = cantidadOriginal * factor;
                        }

                        // Validar stock antes de mandar al carrito
                        if (cantidadAProcesar > stockDisponible) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Stock insuficiente',
                                text: `Estás intentando agregar ${cantidadAProcesar} unidades, pero solo hay ${stockDisponible} en stock.`
                            });
                            return;
                        }

                        // Temporalmente cambiamos el valor del input para que tu función original agregarProducto(btn) 
                        // tome la cantidad ya multiplicada
                        const valorOriginalInput = inputCant.value;
                        inputCant.value = cantidadAProcesar;

                        // Llamamos a tu función original que ya tienes en carrito.js
                        if (typeof agregarProducto === "function") {
                            agregarProducto(btn);
                        }

                        // Restauramos el input a 1 para la siguiente venta
                        inputCant.value = 1;
                    }
                    </script>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card p-3 carrito">

                    <h5 class="fw-bold mb-3">
                        <i class="bi bi-bag-fill text-success"></i> Carrito
                    </h5>

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

                    <h4 class="text-end fw-bold">
                        Total: $<span id="total">0.00</span>
                    </h4>

                    <button class="btn btn-primary w-100 mt-3" onclick="abrirModalFinalizar()">
                        <i class="bi bi-cash-stack"></i> Finalizar Venta
                    </button>

                </div>
            </div>

        </div>
    </div>

    <div class="modal fade" id="modalFinalizarVenta" tabindex="-1">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title fw-bold"><i class="bi bi-receipt-cutoff me-2"></i>Finalizar Transacción</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body p-4">
                    <div class="row g-4">
                        <div class="col-lg-7 border-end">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="text-uppercase fw-bold m-0 text-primary">Detalle de Salida de Material</h6>
                                <span class="badge bg-warning text-dark">Stock en tiempo real</span>
                            </div>

                            <div class="table-responsive" style="max-height: 350px;">
                                <table class="table table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Producto</th>
                                            <th class="text-center" width="100">Venta</th>
                                            <th class="text-center" width="120">Entregar Hoy</th>
                                            <th class="text-end" width="100">Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tablaConfirmacion"></tbody>
                                </table>
                            </div>

                            <div class="card bg-primary bg-opacity-10 border-0 mt-3">
                                <div class="card-body p-3">
                                    <div class="text-end">
                                        <input type="hidden" id="descuentoGeneral" value="0">
                                        <span class="text-muted small d-block fw-bold text-uppercase">Total a
                                            Cobrar</span>
                                        <h2 class="fw-bold mb-0 text-primary">$<span id="totalFinalModal">0.00</span>
                                        </h2>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-5">
                            <h6 class="text-uppercase fw-bold mb-3 text-primary">Información del Cliente</h6>
                            <div class="input-group mb-3">
                                <select id="selectCliente" class="form-select border-primary">
                                    <?php 
                                        $clientes->data_seek(0); 
                                        while($c = $clientes->fetch_assoc()): 
                                    ?>
                                    <option value="<?= $c['id'] ?>" data-rfc="<?= $c['rfc'] ?>"
                                        data-rs="<?= $c['razon_social'] ?>" data-cp="<?= $c['codigo_postal'] ?>"
                                        data-regimen="<?= $c['regimen_fiscal'] ?>">
                                        <?= htmlspecialchars($c['nombre_comercial']) ?>
                                    </option>
                                    <?php endwhile; ?>
                                </select>
                                <button class="btn btn-outline-primary" type="button"
                                    onclick="abrirModalNuevoCliente()">
                                    <i class="bi bi-person-plus"></i>
                                </button>
                            </div>

                            <div class="p-3 border rounded mb-3 bg-white shadow-sm">
                                <div class="row g-2">
                                    <div class="col-12"><small class="text-muted d-block text-uppercase"
                                            style="font-size: 0.7rem;">Razón Social:</small><span id="f_razon_social"
                                            class="fw-bold small text-truncate d-block">---</span></div>
                                    <div class="col-6"><small class="text-muted d-block text-uppercase"
                                            style="font-size: 0.7rem;">RFC:</small><span id="f_rfc"
                                            class="fw-bold">---</span></div>
                                    <div class="col-6"><small class="text-muted d-block text-uppercase"
                                            style="font-size: 0.7rem;">Régimen:</small><span id="f_regimen"
                                            class="badge bg-info">---</span></div>
                                </div>
                            </div>

                            <div class="p-3 border rounded mb-3 bg-light border-success border-opacity-25">
                                <h6 class="text-uppercase fw-bold mb-3 small text-success"><i
                                        class="bi bi-cash-coin"></i> Registro de Pago</h6>
                                <div class="row g-2">
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold">Monto Recibido</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-success text-white border-success">$</span>
                                            <input type="number" id="monto_pagar"
                                                class="form-control border-success fw-bold text-success" value="0"
                                                step="0.01" min="0">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold">Método</label>
                                        <select id="metodo_pago" class="form-select border-success">
                                            <option value="Efectivo">Efectivo</option>
                                            <option value="Transferencia">Transferencia</option>
                                            <option value="Tarjeta">Tarjeta</option>
                                        </select>
                                    </div>
                                </div>
                                <div id="pago_aviso" class="small mt-2 text-center fw-bold"></div>
                            </div>

                            <div class="mb-0">
                                <label class="form-label small fw-bold text-muted"><i class="bi bi-pencil"></i>
                                    Observaciones de Venta</label>
                                <textarea id="obsVenta" class="form-control" rows="2"
                                    placeholder="Notas adicionales..."></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-light border-0">
                    <button class="btn btn-link text-muted me-auto" data-bs-dismiss="modal">Cerrar</button>
                    <button class="btn btn-success btn-lg px-5 shadow fw-bold" onclick="procesarVenta()">
                        <i class="bi bi-check-circle-fill"></i> FINALIZAR VENTA
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalNuevoCliente" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Registrar Nuevo Cliente</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="formNuevoCliente">
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Nombre Comercial</label>
                                <input type="text" name="nombre_comercial" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Razón Social</label>
                                <input type="text" name="razon_social" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">RFC</label>
                                <input type="text" name="rfc" class="form-control" placeholder="XAXX010101000" required
                                    maxlength="13">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Régimen Fiscal (SAT)</label>
                                <input type="text" name="regimen_fiscal" class="form-control" placeholder="601"
                                    maxlength="3">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Uso CFDI</label>
                                <select name="uso_cfdi" class="form-select">
                                    <option value="G03">G03 - Gastos en general</option>
                                    <option value="P01">P01 - Por definir</option>
                                    <option value="S01">S01 - Sin efectos fiscales</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Correo Electrónico</label>
                                <input type="email" name="correo" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Teléfono</label>
                                <input type="text" name="telefono" class="form-control">
                            </div>
                            <div class="col-md-10">
                                <label class="form-label small fw-bold">Dirección</label>
                                <input type="text" name="direccion" class="form-control">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small fw-bold">CP</label>
                                <input type="text" name="codigo_postal" class="form-control" required maxlength="5">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary px-4">Guardar Cliente</button>
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
    <script src="/cfsistem/app/backend/js_ventas/modal_finalizar.js"></script>
    <script src="/cfsistem/app/backend/js_ventas/procesar_venta.js"></script>

    <script>
    // Validación de límites y aviso de estado de pago
    document.getElementById('monto_pagar').addEventListener('input', function() {
        const totalTexto = document.getElementById('totalFinalModal').innerText.replace(/,/g, '');
        const totalFinal = parseFloat(totalTexto) || 0;
        let valor = parseFloat(this.value) || 0;
        const aviso = document.getElementById('pago_aviso');

        if (valor < 0) {
            this.value = 0;
            valor = 0;
        } else if (valor > totalFinal) {
            this.value = totalFinal;
            valor = totalFinal;
        }

        if (valor === totalFinal && totalFinal > 0) {
            aviso.innerHTML = '<span class="text-success"><i class="bi bi-check-all"></i> PAGO COMPLETO</span>';
        } else if (valor > 0 && valor < totalFinal) {
            aviso.innerHTML =
                '<span class="text-warning"><i class="bi bi-pie-chart"></i> PAGO PARCIAL (CRÉDITO)</span>';
        } else if (valor === 0 && totalFinal > 0) {
            aviso.innerHTML =
                '<span class="text-danger"><i class="bi bi-exclamation-triangle"></i> VENTA A CRÉDITO</span>';
        } else {
            aviso.innerHTML = '';
        }
    });

    document.getElementById('selectCliente').addEventListener('change', function() {
        const selected = this.options[this.selectedIndex];
        if (document.getElementById('f_rfc'))
            document.getElementById('f_rfc').textContent = selected.dataset.rfc || '---';
        if (document.getElementById('f_razon_social'))
            document.getElementById('f_razon_social').textContent = selected.dataset.rs || '---';
        if (document.getElementById('f_regimen'))
            document.getElementById('f_regimen').textContent = selected.dataset.regimen || '---';
    });

    document.addEventListener('DOMContentLoaded', function() {
        const select = document.getElementById('selectCliente');
        if (select) select.dispatchEvent(new Event('change'));
    });
    </script>
</body>

</html>