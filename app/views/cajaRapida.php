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
                                    $almacen_u = $_SESSION['almacen_id'] ?? 0;
                                    $esAdmin = ($almacen_u == 0);
                                    $esSuAlmacen = ($c['almacen_id'] == $almacen_u);
                                    $esGlobal = (is_null($c['almacen_id']) || $c['almacen_id'] == '');
                                    $esPublicoGeneral = ($c['rfc'] === 'XAXX010101000');

                                    if ($esAdmin || $esSuAlmacen || $esGlobal || $esPublicoGeneral): 
                                ?>
                                <option value="<?= $c['id'] ?>" 
                                        data-rfc="<?= $c['rfc'] ?>"
                                        data-rs="<?= $c['razon_social'] ?>" 
                                        data-regimen="<?= $c['regimen_fiscal'] ?>">
                                    <?= htmlspecialchars($c['nombre_comercial']) ?>
                                </option>
                                <?php endif; endforeach; ?>
                            </select>
                            <button class="btn btn-outline-primary" type="button" onclick="abrirModalNuevoCliente()">
                                <i class="bi bi-person-plus"></i>
                            </button>
                        </div>

                        <div class="p-3 border rounded mb-3 bg-white shadow-sm">
                            <div class="row g-2">
                                <div class="col-12"><small class="text-muted d-block text-uppercase" style="font-size: 0.7rem;">Razón Social:</small><span id="f_razon_social" class="fw-bold small text-truncate d-block">---</span></div>
                                <div class="col-6"><small class="text-muted d-block text-uppercase" style="font-size: 0.7rem;">RFC:</small><span id="f_rfc" class="fw-bold">---</span></div>
                                <div class="col-6"><small class="text-muted d-block text-uppercase" style="font-size: 0.7rem;">Régimen:</small><span id="f_regimen" class="badge bg-info">---</span></div>
                            </div>
                        </div>

                        <div class="p-3 border rounded mb-3 bg-light border-success border-opacity-25 shadow-sm">
                            <h6 class="text-uppercase fw-bold mb-3 small text-success"><i class="bi bi-cash-coin"></i> Registro de Pago</h6>
                            <div class="row g-2">
                                <div class="col-md-4">
                                    <label class="small fw-bold text-muted">A Cobrar</label>
                                    <input type="number" id="monto_pagar" class="form-control form-control-sm border-primary fw-bold" readonly>
                                </div>
                                <div class="col-md-4">
                                    <label class="small fw-bold text-success">Efectivo</label>
                                    <input type="number" id="efectivo_recibido" class="form-control form-control-sm border-success fw-bold text-success" placeholder="0.00" step="0.01">
                                </div>
                                <div class="col-md-4">
                                    <label class="small fw-bold">Método</label>
                                    <select id="metodo_pago" class="form-select form-select-sm border-success">
                                        <option value="Efectivo">Efectivo</option>
                                        <option value="Transferencia">Transferencia</option>
                                        <option value="Tarjeta">Tarjeta</option>
                                    </select>
                                </div>
                            </div>

                            <div id="contenedor_cambio" class="mt-3 p-2 rounded text-center d-none" style="background-color: rgba(52, 199, 89, 0.1); border: 1px dashed #34c759;">
                                <span class="text-muted small fw-bold text-uppercase" style="font-size: 0.6rem;">Cambio para el cliente:</span>
                                <h3 class="fw-bold text-success mb-0" id="texto_cambio">$0.00</h3>
                            </div>
                        </div>

                        <textarea id="obsVenta" class="form-control shadow-sm" rows="2" placeholder="Notas adicionales de la venta..."></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light border-0">
                <button class="btn btn-link text-muted me-auto" data-bs-dismiss="modal">Cerrar</button>
                <button id="btnFinalizarVenta" class="btn btn-success btn-lg px-5 shadow fw-bold" onclick="procesarVenta()">
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
   
    
<script>
    // Escuchar cambios en el efectivo recibido
document.addEventListener('input', function(e) {
    if (e.target.id === 'efectivo_recibido' || e.target.id === 'monto_pagar') {
        calcularCambio();
    }
});

function calcularCambio() {
    const totalVenta = parseFloat(document.getElementById('monto_pagar').value) || 0;
    const efectivo = parseFloat(document.getElementById('efectivo_recibido').value) || 0;
    const contenedor = document.getElementById('contenedor_cambio');
    const textoCambio = document.getElementById('texto_cambio');

    if (efectivo > 0) {
        const cambio = efectivo - totalVenta;
        
        // Mostrar el contenedor de cambio
        contenedor.classList.remove('d-none');
        
        if (cambio < 0) {
            // Si falta dinero
            textoCambio.classList.replace('text-success', 'text-danger');
            textoCambio.innerText = `Faltan: $${Math.abs(cambio).toFixed(2)}`;
        } else {
            // Si hay cambio o es exacto
            textoCambio.classList.replace('text-danger', 'text-success');
            textoCambio.innerText = `Cambio: $${cambio.toFixed(2)}`;
        }
    } else {
        contenedor.classList.add('d-none');
    }
}

// Resetear calculadora cuando se abra el modal
const modalVenta = document.getElementById('modalFinalizarVenta');
if(modalVenta) {
    modalVenta.addEventListener('shown.bs.modal', function () {
        document.getElementById('efectivo_recibido').value = "";
        document.getElementById('contenedor_cambio').classList.add('d-none');
        document.getElementById('efectivo_recibido').focus(); // Auto-focus para rapidez
    });
}
document.addEventListener('change', function(e) {
    if (e.target.id === 'metodo_pago') {
        const metodo = e.target.value;
        const campoEfectivo = document.getElementById('efectivo_recibido');
        const contenedorCambio = document.getElementById('contenedor_cambio');
        
        // Contenedor del input (el col-md-4) para ocultarlo por completo
        const columnaEfectivo = campoEfectivo.closest('.col-md-4');

        if (metodo === 'Efectivo') {
            // Mostrar campos de calculadora
            columnaEfectivo.classList.remove('d-none');
            // El contenedor de cambio se mostrará solo si hay un valor (vía calcularCambio)
        } else {
            // Ocultar campos para Tarjeta o Transferencia
            columnaEfectivo.classList.add('d-none');
            contenedorCambio.classList.add('d-none');
            
            // Limpiar valores para no enviar basura al controlador
            campoEfectivo.value = "";
        }
    }
});
</script>
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
    <script>
        window.procesarVenta = function() {
    // 1. Validaciones previas
    if (!window.carrito || window.carrito.length === 0) {
        return Swal.fire('Carrito vacío', 'Agrega productos antes de cobrar.', 'warning');
    }

    const idCliente = document.getElementById('selectCliente').value;
    if (!idCliente) {
        return Swal.fire('Cliente requerido', 'Selecciona un cliente para continuar.', 'warning');
    }

    // 2. Captura de datos del DOM
    const totalTexto = document.getElementById('totalFinalModal').innerText.replace(/[$,]/g, '');
    const totalVenta = parseFloat(totalTexto) || 0;
    const montoPagado = parseFloat(document.getElementById('monto_pagar').value) || 0;
    const metodoPago = document.getElementById('metodo_pago').value;
    const observaciones = document.getElementById('obsVenta').value;

    // 3. Confirmación de seguridad
    Swal.fire({
        title: '¿Confirmar Cobro?',
        html: `Total: <b class="text-primary">$${totalVenta.toFixed(2)}</b><br>Recibido: <b>$${montoPagado.toFixed(2)}</b>`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Sí, finalizar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#34c759'
    }).then((result) => {
        if (result.isConfirmed) {
            
            const btnFinalizar = document.querySelector('#modalFinalizarVenta .btn-success');
            if(btnFinalizar) {
                btnFinalizar.disabled = true;
                btnFinalizar.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Guardando...';
            }

            // 4. Mapeo de datos para el VentasModel
            const datosVenta = {
                id_cliente: parseInt(idCliente),
                descuento: 0,
                monto_pagado: montoPagado,
                metodo_pago: metodoPago,
                total_venta: totalVenta,
                observaciones: observaciones,
                carrito: window.carrito.map(item => ({
                    producto_id: parseInt(item.producto_id),
                    almacen_id: parseInt(item.almacen_id),
                    cantidad: parseFloat(item.cantidad),
                    entrega_hoy: parseFloat(item.entrega_hoy), 
                    precio_unitario: parseFloat(item.precio_unitario),
                    tipo_precio: item.tipo_precio
                }))
            };

            // 5. Envío al Controlador (Ruta exacta proporcionada)
            fetch('/cfsistem/app/controllers/cajaRapidaController.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(datosVenta)
            })
            .then(res => {
                if (!res.ok) throw new Error('Error en la comunicación con el servidor');
                return res.json();
            })
            .then(res => {
                if (res.status === 'success') {
                    // Verificamos si hubo faltantes según la lógica de tu controlador
                    const esParcial = res.message.includes('⚠️');
                    console.log(res.debug_id_movimiento);
                    
                    Swal.fire({
                        title: esParcial ? 'Venta Parcial' : '¡Venta Exitosa!',
                        html: `<div class="text-start small">${res.message}</div>`,
                        icon: esParcial ? 'warning' : 'success',
                        showDenyButton: true,
                        confirmButtonText: '<i class="bi bi-printer"></i> Ticket',
                        denyButtonText: '<i class="bi bi-file-earmark-pdf"></i> Nota sin precios',
                        confirmButtonColor: '#007aff',
                        denyButtonColor: '#6c757d'
                    }).then((result) => {
                        // Rutas de impresión (ajustar si tus tickets están en otro lado)
                        let printUrl = '';
                        if (result.isConfirmed) {
                            printUrl = `/cfsistem/app/backend/ventas/ticket_venta.php?id=${res.id_venta}`;
                        } else if (result.isDenied) {
                            printUrl = `/cfsistem/app/backend/ventas/ticket_sin_precio.php?id=${res.id_venta}`;
                        }

                        if (printUrl) window.open(printUrl, '_blank');
                        
                        // Recarga limpia para actualizar stocks en la vista
                       
                    });
                } else {
                    Swal.fire('Error', res.message || 'No se pudo guardar la venta', 'error');
                    if(btnFinalizar) {
                        btnFinalizar.disabled = false;
                        btnFinalizar.innerHTML = '<i class="bi bi-check-circle-fill"></i> CONFIRMAR Y GUARDAR';
                    }
                }
            })
            .catch(err => {
                console.error(err);
                Swal.fire('Error Crítico', 'Hubo un problema al conectar con el controlador.', 'error');
                if(btnFinalizar) btnFinalizar.disabled = false;
            });
        }
    });
};
    </script>
    <script>
        /**
 * 4. FUNCIÓN PARA ABRIR EL MODAL DE FINALIZACIÓN
 */
window.abrirModalFinalizar = function() {
    if (!window.carrito || window.carrito.length === 0) {
        Swal.fire('Carrito vacío', 'Agrega productos antes de finalizar la venta.', 'warning');
        return;
    }

    const tabla = document.getElementById("tablaConfirmacion");
    if (!tabla) return;
    tabla.innerHTML = "";
    
    window.carrito.forEach((item, index) => {
        if (item.entrega_hoy === undefined || item.entrega_hoy === null) {
            item.entrega_hoy = item.cantidad;
        }

        // Cálculos iniciales de venta total
        const cantFactorVenta = Math.floor(item.cantidad / item.factor);
        const piezasRestantesVenta = Math.round((item.cantidad % item.factor) * 100) / 100;

        // Cálculos dinámicos de lo que se está ENTREGANDO
        const fEntregar = Math.floor(item.entrega_hoy / item.factor);
        const pEntregar = Math.round((item.entrega_hoy % item.factor) * 100) / 100;

        const tr = document.createElement("tr");
        tr.innerHTML = `
            <td>
                <div class="fw-bold" style="font-size: 0.85rem;">${item.nombre}</div>
                <small class="text-muted d-block">${item.almacen_nombre} | ${item.tipo_precio.toUpperCase()}</small>
                
                <div class="mt-1" style="font-size: 0.7rem; color: #055160; background: #e3f2fd; padding: 4px 8px; border-radius: 4px; border-left: 3px solid #0d6efd;">
                    <i class="bi bi-info-circle-fill"></i> Factor: 1 <b>${item.unidad_reporte}</b> = <b>${item.factor}</b> pzas.<br>
                    Vendido: ${cantFactorVenta} ${item.unidad_reporte} + ${piezasRestantesVenta} pzas.<br>
                    
                </div>
            </td>
            <td class="text-center">
                <div class="fw-bold" style="font-size: 0.9rem;">${item.cantidad}</div>
                <small class="text-muted" style="font-size: 0.65rem;">Pzas Totales</small>
            </td>
            <td>
                <div class="input-group input-group-sm">
                    <input type="number" 
                           class="form-control text-center input-entrega-modal" 
                           data-index="${index}" 
                           value="${item.cantidad}" 
                           min="0" 
                           max="${item.cantidad}"
                           step="any" readonly>
                    <span class="input-group-text"><i class="bi bi-box-seam"></i></span>
                </div>
                <small class="text-muted d-block text-center" style="font-size: 0.65rem;">Piezas a entregar hoy</small>
            </td>
            <td class="text-end fw-bold">$${item.subtotal.toFixed(2)}</td>
        `;
        tabla.appendChild(tr);
    });

    // Llamada segura a la función local
    window.recalcularTotalModal();

    const modalElement = document.getElementById('modalFinalizarVenta');
    if (modalElement) {
        const myModal = new bootstrap.Modal(modalElement);
        myModal.show();
    }
};

/**
 * 5. RECALCULAR TOTALES DENTRO DEL MODAL
 */
window.recalcularTotalModal = function() {
    let total = 0;
    if (window.carrito) {
        window.carrito.forEach(i => {
            total += parseFloat(i.subtotal || 0);
        });
    }

    const totalDisplay = document.getElementById("totalFinalModal");
    if (totalDisplay) totalDisplay.innerText = total.toFixed(2);

    const inputPago = document.getElementById("monto_pagar");
    if (inputPago) {
        inputPago.value = total.toFixed(2);
        inputPago.dispatchEvent(new Event('input'));
    }
};

/**
 * 6. LISTENER PARA ACTUALIZAR DESGLOSE Y ENTREGA EN TIEMPO REAL
 */document.addEventListener('input', function(e) {
    if (e.target.classList.contains('input-entrega-modal')) {
        const index = e.target.dataset.index;
        const item = window.carrito[index];
        
        // CORRECCIÓN: Usa parseFloat para permitir decimales o cantidades enteras mayores a 1
        let valor = parseFloat(e.target.value);
        
        if (isNaN(valor)) valor = 0;

        // Validar que no entregue más de lo vendido
        if (valor > item.cantidad) {
            valor = item.cantidad;
            e.target.value = valor;
        }
        
        // Guardamos el valor real (ej. 2, 5, 10...)
        item.entrega_hoy = valor;

        // Actualizar el texto informativo (Aquí sí usamos floor solo para mostrar el texto de "bultos")
        const f = Math.floor(valor / item.factor);
        const p = Math.round((valor % item.factor) * 100) / 100;
        
        const elDesglose = document.getElementById(`desglose-entrega-${index}`);
        if (elDesglose) {
            elDesglose.innerHTML = `Entregando: ${f} ${item.unidad_reporte} + ${p} pzas.`;
        }
    }
});
    </script>
</body>

</html>