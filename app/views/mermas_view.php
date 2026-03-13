<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mermas y Transmutaciones</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
     <?php if (function_exists('cargarEstilos')) { cargarEstilos(); } ?>
    <style>
        :root { --sidebar-width: 260px; --navbar-height: 65px; }
        body { background-color: #f4f7f6; }
        .main-content { margin-left: var(--sidebar-width); padding: 40px; padding-top: calc(var(--navbar-height) + 20px); }
        .card-table { border: none; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); background: white; }
        @media (max-width: 768px) { .main-content { margin-left: 0; padding: 20px; padding-top: 90px; } }
    </style>
</head>
<body>
    <?php if (function_exists('renderizarLayout')) renderizarLayout('Mermas'); ?>

    <div class="main-content">
        <div class="container-fluid">
            <?php if (isset($_GET['msg'])): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    Merma registrada con éxito.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <h2 class="mb-4"><i class="fas fa-dumpster-fire text-danger"></i> Mermas y Transmutaciones</h2>

            <ul class="nav nav-tabs mb-4">
                <li class="nav-item">
                    <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#merma">
                        <i class="fas fa-minus-circle"></i> Registrar Merma
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#transmutacion">
                        <i class="fas fa-sync-alt"></i> Transmutación
                    </button>
                </li>
            </ul>

            <div class="tab-content">
                <!-- TAB MERMAS -->
                <div class="tab-pane fade show active" id="merma">
                    <div class="card card-table">
                        <div class="card-body">
                            <form id="formMerma" action="/cfsistem/app/controllers/mermasController.php?action=guardarMerma" method="POST">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label fw-bold">Almacén</label>
                                        <select name="almacen_id" id="merma_almacen" class="form-select" required>
                                            <option value="">Seleccione...</option>
                                            <?php foreach ($almacenes as $a): ?>
                                                <option value="<?= $a['id'] ?>"><?= htmlspecialchars($a['nombre']) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-bold">Producto</label>
                                        <select name="producto_id" id="merma_producto" class="form-select" disabled required>
                                            <option value="">Seleccione almacén</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-bold">Lote</label>
                                        <select name="lote_id" id="merma_lote" class="form-select" disabled required>
                                            <option value="">Seleccione producto</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row g-3 mt-3">
                                    <div class="col-md-3">
                                        <label class="form-label fw-bold">Cantidad</label>
                                        <input type="number" step="0.01" min="0.01" name="cantidad" id="merma_cantidad" class="form-control" required>
                                        <div class="form-text">Disponible: <span id="stock_disponible">0</span></div>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label fw-bold">Tipo Merma</label>
                                        <select name="tipo_merma" class="form-select" required>
                                            <option value="daño">Daño/Rotura</option>
                                            <option value="robo">Robo/Extravío</option>
                                            <option value="caducidad">Caducidad</option>
                                            <option value="otro">Otro</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Observaciones</label>
                                        <textarea name="observaciones" class="form-control" rows="2" placeholder="Motivo detallado de la merma..."></textarea>
                                    </div>
                                </div>
                                <div class="mt-4">
                                    <button type="submit" class="btn btn-danger btn-lg">
                                        <i class="fas fa-save"></i> Registrar Merma
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- TAB TRANSMUTACIÓN (placeholder) -->
                
            </div>
        </div>
    </div>

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

        // Cambio de almacén → cargar productos
        almacenSelect.addEventListener('change', async function() {
            const almacenId = this.value;
            if (!almacenId) return resetForm();

            productoSelect.innerHTML = '<option>Cargando...</option>';
            productoSelect.disabled = true;
            loteSelect.disabled = true;

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
                console.error('Error productos:', e);
                productoSelect.innerHTML = '<option>Error al cargar</option>';
            }
        });

        // Cambio de producto → cargar lotes
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
                console.error('Error lotes:', e);
                loteSelect.innerHTML = '<option>Error al cargar</option>';
            }
        });

        // Cambio de lote → mostrar stock disponible
        loteSelect.addEventListener('change', function() {
            const selectedOption = this.selectedOptions[0];
            const stock = parseFloat(selectedOption?.dataset.stock || 0);
            stockSpan.textContent = stock.toLocaleString('es-MX', { minimumFractionDigits: 2 });
            cantidadInput.max = stock;
            cantidadInput.value = '';
        });

        // Submit formulario
        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(form);
            const stock = parseFloat(stockSpan.textContent) || 0;
            const cantidad = parseFloat(cantidadInput.value) || 0;

            if (cantidad > stock) {
                alert('❌ Cantidad mayor al stock disponible');
                return;
            }

            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();
                
                if (result.status === 'success') {
                    alert('✅ ' + result.message);
                    resetForm();
                } else {
                    alert('❌ ' + result.message);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error de conexión');
            }
        });

        function resetForm() {
            productoSelect.innerHTML = '<option value="">Seleccione almacén</option>';
            productoSelect.disabled = true;
            resetLotes();
            stockSpan.textContent = '0';
            cantidadInput.value = '';
        }

        function resetLotes() {
            loteSelect.innerHTML = '<option value="">Seleccione producto</option>';
            loteSelect.disabled = true;
        }
    });
    </script>
</body>
</html>