<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mermas | Control de Inventario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    <?php if (function_exists('cargarEstilos')) { cargarEstilos(); } ?>
    <style>
        :root { 
            --sidebar-width: 260px; 
            --navbar-height: 65px;
            --apple-gray: #f5f5f7;
            --apple-blue: #0071e3;
            --apple-red: #ff3b30;
        }

        body { 
            background-color: var(--apple-gray); 
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            color: #1d1d1f;
        }

        .main-content { 
            margin-left: var(--sidebar-width); 
            padding: 40px; 
            padding-top: calc(var(--navbar-height) + 20px); 
        }

        /* Estilo Tarjeta Mac */
        .card-apple { 
            border: none; 
            border-radius: 20px; 
            box-shadow: 0 4px 24px rgba(0,0,0,0.04); 
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255,255,255,0.3);
        }

        .header-title {
            font-weight: 600;
            letter-spacing: -0.02em;
            color: #1d1d1f;
        }

        /* Inputs Estilo iOS/macOS */
        .form-control, .form-select {
            border-radius: 10px;
            border: 1px solid #d2d2d7;
            padding: 0.6rem 1rem;
            background-color: rgba(255, 255, 255, 0.5);
            transition: all 0.2s ease;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--apple-blue);
            box-shadow: 0 0 0 4px rgba(0, 113, 227, 0.1);
            background-color: #fff;
        }

        .btn-apple-danger {
            background-color: var(--apple-red);
            border: none;
            border-radius: 12px;
            padding: 12px 24px;
            font-weight: 500;
            transition: transform 0.1s ease;
        }

        .btn-apple-danger:hover {
            background-color: #e32d24;
            transform: scale(1.02);
        }

        .stock-badge {
            background: #e8e8ed;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            color: #424245;
        }

        @media (max-width: 768px) { .main-content { margin-left: 0; padding: 20px; padding-top: 90px; } }
    </style>
</head>
<body>
    <?php if (function_exists('renderizarLayout')) renderizarLayout('Mermas'); ?>

    <div class="main-content">
        <div class="container-fluid" style="max-width: 1000px;">
            
            <?php if (isset($_GET['msg'])): ?>
                <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4">
                    <i class="fas fa-check-circle me-2"></i> Merma registrada con éxito.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="d-flex align-items-center mb-4">
                <div class="bg-danger text-white rounded-3 p-3 me-3 shadow-sm">
                    <i class="fas fa-box-open fa-lg"></i>
                </div>
                <div>
                    <h2 class="header-title mb-0">Gestión de Mermas</h2>
                    <p class="text-muted mb-0">Registra pérdidas o bajas de inventario</p>
                </div>
            </div>

            <div class="card card-apple">
                <div class="card-body p-4 p-md-5">
                    <form id="formMerma" action="/cfsistem/app/controllers/mermasController.php?action=guardarMerma" method="POST">
                        <div class="row g-4">
                            <div class="col-md-4">
                                <label class="form-label small text-uppercase fw-bold text-muted">Almacén de Origen</label>
                                <select name="almacen_id" id="merma_almacen" class="form-select" required>
                                    <option value="">Seleccione...</option>
                                    <?php foreach ($almacenes as $a): ?>
                                        <option value="<?= $a['id'] ?>"><?= htmlspecialchars($a['nombre']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small text-uppercase fw-bold text-muted">Producto</label>
                                <select name="producto_id" id="merma_producto" class="form-select" disabled required>
                                    <option value="">Seleccione almacén</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small text-uppercase fw-bold text-muted">Lote Específico</label>
                                <select name="lote_id" id="merma_lote" class="form-select" disabled required>
                                    <option value="">Seleccione producto</option>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label small text-uppercase fw-bold text-muted">Cantidad a Retirar</label>
                                <input type="number" step="0.01" min="0.01" name="cantidad" id="merma_cantidad" class="form-control form-control-lg" placeholder="0.00" required>
                                <div class="mt-2">
                                    <span class="stock-badge">Disponible: <strong id="stock_disponible">0</strong></span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small text-uppercase fw-bold text-muted">Motivo de Merma</label>
                                <select name="tipo_merma" class="form-select form-select-lg" required>
                                    <option value="daño">📦 Daño / Rotura</option>
                                    <option value="robo">⚠️ Robo / Extravío</option>
                                    <option value="caducidad">⌛ Caducidad</option>
                                    <option value="otro">🔍 Otro motivo</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small text-uppercase fw-bold text-muted">Observaciones</label>
                                <textarea name="observaciones" class="form-control" rows="1" placeholder="Detalles adicionales..."></textarea>
                            </div>
                        </div>

                        <div class="mt-5 border-top pt-4 text-end">
                            <button type="submit" class="btn btn-apple-danger btn-lg text-white">
                                <i class="fas fa-minus-circle me-2"></i> Confirmar Registro de Merma
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="card card-apple">
  <div class="table-responsive p-0" id="contenedor-tabla">
    <table class="table table-hover align-middle mb-0" style="font-size: 0.95rem;">
        <thead class="bg-light">
            <tr>
                <th class="ps-4 border-0 py-3 text-uppercase small fw-bold text-muted">Fecha</th>
                <th class="border-0 py-3 text-uppercase small fw-bold text-muted">Producto / Lote</th>
                <th class="border-0 py-3 text-uppercase small fw-bold text-muted">Almacén</th>
                <th class="border-0 py-3 text-uppercase small fw-bold text-muted text-center">Cantidad</th>
                <th class="border-0 py-3 text-uppercase small fw-bold text-muted text-center">Motivo</th>
                <th class="pe-4 border-0 py-3 text-uppercase small fw-bold text-muted text-end">Responsable</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($mermas as $m): 
                // Asignación de colores según el tipo de merma
                $badgeClass = match($m['tipo_merma']) {
                    'daño'     => 'bg-warning text-dark',
                    'robo'     => 'bg-danger text-white',
                    'caducidad' => 'bg-info text-dark',
                    default     => 'bg-secondary text-white'
                };
            ?>
            <tr>
                <td class="ps-4">
                    <div class="fw-bold"><?= date('d/m/Y', strtotime($m['fecha_reporte'])) ?></div>
                    <div class="small text-muted opacity-75"><?= date('H:i', strtotime($m['fecha_reporte'])) ?> h</div>
                </td>
                <td>
                    <div class="fw-bold text-dark"><?= htmlspecialchars($m['producto_nombre']) ?></div>
                    <div class="small text-muted text-uppercase" style="font-size: 0.75rem;">
                        LOTE: <?= htmlspecialchars($m['codigo_lote'] ?? 'N/A') ?>
                    </div>
                </td>
                <td>
                    <span class="text-muted small">
                        <i class="fas fa-warehouse me-1 text-secondary opacity-50"></i> 
                        <?= htmlspecialchars($m['almacen_nombre']) ?>
                    </span>
                </td>
                <td class="text-center">
                    <span class="fw-bold text-danger">-<?= number_format($m['cantidad'], 2) ?></span>
                </td>
                <td class="text-center">
                    <span class="badge rounded-pill <?= $badgeClass ?> px-3 py-1 fw-medium shadow-sm" style="font-size: 0.75rem;">
                        <?= ucfirst($m['tipo_merma']) ?>
                    </span>
                </td>
                <td class="pe-4 text-end">
                    <div class="small fw-medium text-dark"><?= htmlspecialchars($m['responsable'] ?? 'S/R') ?></div>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <?php if (empty($mermas)): ?>
        <div class="text-center py-5">
            <i class="fas fa-box-open fa-3x text-light mb-3"></i>
            <p class="text-muted fw-light">No hay registros de mermas para mostrar.</p>
        </div>
    <?php endif; ?>
</div>
</div>

<div class="d-flex justify-content-between align-items-center mt-3 px-2">
    <div class="small text-muted fw-light">
        Mostrando <strong><?= count($mermas) ?></strong> de <strong><?= $totalMermas ?></strong> registros
    </div>
    <nav>
        <ul class="pagination pagination-sm mb-0">
            <li class="page-item <?= ($pagina <= 1) ? 'disabled' : '' ?>">
                <a class="page-link border-0 rounded-3 shadow-sm mx-1" href="?p=<?= max(1, $pagina - 1) ?>">
                    <i class="fas fa-chevron-left"></i>
                </a>
            </li>
            <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
                <li class="page-item <?= ($i == $pagina) ? 'active' : '' ?>">
                    <a class="page-link border-0 rounded-3 shadow-sm mx-1 <?= ($i == $pagina) ? 'bg-dark text-white shadow' : 'bg-white text-muted' ?>" href="?p=<?= $i ?>">
                        <?= $i ?>
                    </a>
                </li>
            <?php endfor; ?>
            <li class="page-item <?= ($pagina >= $totalPaginas) ? 'disabled' : '' ?>">
                <a class="page-link border-0 rounded-3 shadow-sm mx-1" href="?p=<?= min($totalPaginas, $pagina + 1) ?>">
                    <i class="fas fa-chevron-right"></i>
                </a>
            </li>
        </ul>
    </nav>
</div>
    </div>






<script>
    if (typeof Swal === 'undefined') {
        document.write('<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"><\/script>');
    }
</script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
   <script>
document.addEventListener('DOMContentLoaded', function() {
    const baseUrl = '/cfsistem/app/controllers/mermasController.php';
    const almacenSelect = document.getElementById('merma_almacen');
    const productoSelect = document.getElementById('merma_producto');
    const loteSelect = document.getElementById('merma_lote');
    const cantidadInput = document.getElementById('merma_cantidad');
    const stockSpan = document.getElementById('stock_disponible');
    const form = document.getElementById('formMerma');

    // Configuración base para estética Mac
    const toastMac = Swal.mixin({
        customClass: {
            confirmButton: 'btn btn-danger px-4 mx-2',
            cancelButton: 'btn btn-light px-4 mx-2'
        },
        buttonsStyling: false,
        border: 'none'
    });

    // Cambio de almacén -> cargar productos
    almacenSelect.addEventListener('change', async function() {
        const almacenId = this.value;
        if (!almacenId) return resetForm();
        productoSelect.innerHTML = '<option>Cargando...</option>';
        productoSelect.disabled = true;

        try {
            const response = await fetch(`${baseUrl}?action=obtenerProductosAlmacen&almacen_id=${almacenId}`);
            const productos = await response.json();
            productoSelect.innerHTML = '<option value="">Seleccione producto</option>';
            productos.forEach(p => {
                const option = new Option(`${p.sku} - ${p.nombre} (Stock: ${p.stock})`, p.id);
                productoSelect.appendChild(option);
            });
            productoSelect.disabled = false;
        } catch (e) {
            productoSelect.innerHTML = '<option>Error al cargar</option>';
        }
    });

    // Cambio de producto -> cargar lotes
    productoSelect.addEventListener('change', async function() {
        const productoId = this.value;
        const almacenId = almacenSelect.value;
        if (!productoId || !almacenId) return resetLotes();
        loteSelect.innerHTML = '<option>Cargando...</option>';
        loteSelect.disabled = true;

        try {
            const response = await fetch(`${baseUrl}?action=obtenerLotes&producto_id=${productoId}&almacen_id=${almacenId}`);
            const lotes = await response.json();
            loteSelect.innerHTML = '<option value="">Seleccione lote</option>';
            lotes.forEach(l => {
                const option = new Option(`${l.codigo_lote} (Disp: ${l.cantidad_actual})`, l.id);
                option.dataset.stock = l.cantidad_actual;
                loteSelect.appendChild(option);
            });
            loteSelect.disabled = false;
        } catch (e) {
            loteSelect.innerHTML = '<option>Error al cargar</option>';
        }
    });

    // Cambio de lote -> mostrar stock
    loteSelect.addEventListener('change', function() {
        const selectedOption = this.selectedOptions[0];
        const stock = parseFloat(selectedOption?.dataset.stock || 0);
        stockSpan.textContent = stock.toLocaleString('es-MX', { minimumFractionDigits: 2 });
        cantidadInput.max = stock;
    });

    // Submit con SweetAlert2
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const stock = parseFloat(stockSpan.textContent.replace(/,/g,'')) || 0;
        const cantidad = parseFloat(cantidadInput.value) || 0;

        // Validación previa
        if (cantidad > stock) {
            Swal.fire({
                icon: 'warning',
                title: 'Stock Insuficiente',
                text: `No puedes retirar ${cantidad} si solo hay ${stock} disponibles.`,
                confirmButtonColor: '#0071e3'
            });
            return;
        }

        // Confirmación
        const confirmacion = await toastMac.fire({
            title: '¿Registrar Merma?',
            text: "Esta acción descontará el stock de forma permanente.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sí, registrar',
            cancelButtonText: 'Cancelar',
            background: '#ffffff',
            padding: '2em'
        });

        if (confirmacion.isConfirmed) {
            // Mostrar cargando
            Swal.fire({
                title: 'Procesando...',
                didOpen: () => { Swal.showLoading() },
                allowOutsideClick: false
            });

            try {
                const formData = new FormData(form);
                const response = await fetch(form.action, { method: 'POST', body: formData });
                const result = await response.json();

                if (result.status === 'success') {
                    await Swal.fire({
                        icon: 'success',
                        title: 'Completado',
                        text: result.message || 'La merma ha sido registrada.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                    window.location.reload();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: result.message || 'No se pudo procesar la solicitud.'
                    });
                }
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error de conexión',
                    text: 'Hubo un problema al contactar con el servidor.'
                });
            }
        }
    });

    function resetForm() {
        productoSelect.innerHTML = '<option value="">Seleccione almacén</option>';
        productoSelect.disabled = true;
        resetLotes();
        stockSpan.textContent = '0';
    }

    function resetLotes() {
        loteSelect.innerHTML = '<option value="">Seleccione producto</option>';
        loteSelect.disabled = true;
    }
});
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const baseUrl = '/cfsistem/app/controllers/mermasController.php';
    const form = document.getElementById('formMerma');
    const stockSpan = document.getElementById('stock_disponible');

    // Manejo de envío del formulario vía AJAX
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        // Validación de stock (limpia comas de formato MX)
        const stockActual = parseFloat(stockSpan.textContent.replace(/,/g,'')) || 0;
        const cantSolicitada = parseFloat(document.getElementById('merma_cantidad').value) || 0;

        if (cantSolicitada > stockActual) {
            Swal.fire({ icon: 'error', title: 'Stock Insuficiente', text: 'La cantidad supera la disponibilidad del lote seleccionado.' });
            return;
        }

        // Confirmación nativa de SweetAlert2
        const confirmacion = await Swal.fire({
            title: '¿Registrar Merma?',
            text: "Se descontará el stock del inventario.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sí, confirmar',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#ff3b30', // Rojo Apple
            borderRadius: '15px'
        });

        if (confirmacion.isConfirmed) {
            Swal.fire({ title: 'Procesando...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });

            try {
                const formData = new FormData(form);
                const resp = await fetch(`${baseUrl}?action=guardarMerma`, {
                    method: 'POST',
                    body: formData
                });
                
                const res = await resp.json();

                if (res.status === 'success') {
                    await Swal.fire({ 
                        icon: 'success', 
                        title: 'Completado', 
                        text: res.message, 
                        timer: 1500, 
                        showConfirmButton: false 
                    });
                    // Recargamos para refrescar la tabla y el contador de paginación
                    window.location.reload();
                } else {
                    Swal.fire({ icon: 'error', title: 'Error', text: res.message });
                }
            } catch (err) {
                Swal.fire({ icon: 'error', title: 'Error de Servidor', text: 'No se pudo procesar la solicitud.' });
            }
        }
    });
});
</script>
</html>