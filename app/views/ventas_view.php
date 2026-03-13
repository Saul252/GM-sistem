<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Ventas | Sistema</title>
    <?php cargarEstilos(); ?>
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
                            <h6 class="text-uppercase fw-bold mb-3 text-primary">Detalle de Salida de Material</h6>
                            <div class="table-responsive" style="max-height: 350px;">
                                <table class="table table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Producto</th>
                                            <th class="text-center">Venta</th>
                                            <th class="text-center">Entregar Hoy</th>
                                            <th class="text-end">Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tablaConfirmacion"></tbody>
                                </table>
                            </div>
                            <div class="card bg-primary bg-opacity-10 border-0 mt-3">
                                <div class="card-body p-3 text-end">
                                    <input type="hidden" id="descuentoGeneral" value="0">
                                    <span class="text-muted small d-block fw-bold text-uppercase">Total a Cobrar</span>
                                    <h2 class="fw-bold mb-0 text-primary">$<span id="totalFinalModal">0.00</span></h2>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-5">
                            <h6 class="text-uppercase fw-bold mb-3 text-primary">Información del Cliente</h6>
                            <div class="input-group mb-3">
                                <select id="selectCliente" class="form-select border-primary">
                                    <?php foreach($clientes as $c): 
    $almacen_usuario = $_SESSION['almacen_id'] ?? 0;
        // Lógica de filtro doble en la vista
        $esAdmin = ($almacen_usuario == 0);
        $esSuAlmacen = ($c['almacen_id'] == $almacen_usuario);
        $esGlobal = (is_null($c['almacen_id']) || $c['almacen_id'] == '');
        $esPublicoGeneral = ($c['rfc'] === 'XAXX010101000');

        // Solo mostramos si es admin O si cumple alguna condición de vendedor
        if ($esAdmin || $esSuAlmacen || $esGlobal || $esPublicoGeneral): 
    ?>
                                    <option value="<?= $c['id'] ?>" data-rfc="<?= $c['rfc'] ?>"
                                        data-rs="<?= $c['razon_social'] ?>" data-cp="<?= $c['codigo_postal'] ?>"
                                        data-regimen="<?= $c['regimen_fiscal'] ?>">
                                        <?= htmlspecialchars($c['nombre_comercial']) ?>
                                    </option>
                                    <?php 
        endif; 
    endforeach; 
    ?>
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
                            <textarea id="obsVenta" class="form-control" rows="2"
                                placeholder="Notas adicionales..."></textarea>
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
    <script src="/cfsistem/app/backend/js_ventas/modal_finalizar.js"></script>
    <script src="/cfsistem/app/backend/js_ventas/procesar_venta.js"></script>

    <script>
    // Lógica de validación de pago y avisos

    document.getElementById('monto_pagar').addEventListener('input', function() {
        const totalTexto = document.getElementById('totalFinalModal').innerText.replace(/,/g, '');
        const totalFinal = parseFloat(totalTexto) || 0;
        let valor = parseFloat(this.value) || 0;
        const aviso = document.getElementById('pago_aviso');

        if (valor < 0) this.value = 0;
        else if (valor > totalFinal) this.value = totalFinal;

        if (valor === totalFinal && totalFinal > 0) aviso.innerHTML =
            '<span class="text-success"><i class="bi bi-check-all"></i> PAGO COMPLETO</span>';
        else if (valor > 0 && valor < totalFinal) aviso.innerHTML =
            '<span class="text-warning"><i class="bi bi-pie-chart"></i> PAGO PARCIAL</span>';
        else if (valor === 0 && totalFinal > 0) aviso.innerHTML =
            '<span class="text-danger"><i class="bi bi-exclamation-triangle"></i> CRÉDITO</span>';
        else aviso.innerHTML = '';
    });

    document.getElementById('selectCliente').addEventListener('change', function() {
        const selected = this.options[this.selectedIndex];
        document.getElementById('f_rfc').textContent = selected?.dataset.rfc || '---';
        document.getElementById('f_razon_social').textContent = selected?.dataset.rs || '---';
        document.getElementById('f_regimen').textContent = selected?.dataset.regimen || '---';
    });

    document.addEventListener('DOMContentLoaded', () => {
        const select = document.getElementById('selectCliente');
        if (select) select.dispatchEvent(new Event('change'));
    });

    function validarYAgregar(btn) {
        const fila = btn.closest('tr');
        const modo = fila.querySelector('.select-modo-venta')?.value || 'individual';
        const inputCant = fila.querySelector('.cantidad');
        const factor = parseFloat(fila.dataset.factor) || 1;
        const stockDisponible = parseFloat(fila.querySelector('.badge').innerText);

        let cantidadUsuario = parseFloat(inputCant.value) || 0;
        let cantidadReal = (modo === 'referencia') ? (cantidadUsuario * factor) : cantidadUsuario;

        if (cantidadReal > stockDisponible) {
            Swal.fire('Stock insuficiente', `No puedes agregar ${cantidadReal} unidades. Stock: ${stockDisponible}`,
                'error');
            return;
        }

        inputCant.value = cantidadReal; // Ajuste temporal para agregarProducto
        if (typeof agregarProducto === "function") agregarProducto(btn);
        inputCant.value = 1; // Reset
    }
    </script>
</body>

</html>