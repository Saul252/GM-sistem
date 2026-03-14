<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Transmutaciones | CF Sistem</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    
    <?php if (function_exists('cargarEstilos')) { cargarEstilos(); } ?>   
<link href="/cfsistem/css/transmutaciones.css" rel="stylesheet">
</head>
<body>
    <?php if (function_exists('renderizarLayout')) renderizarLayout('Mermas'); ?>

    <div class="main-content">
        <div class="container-fluid">
            
            <div class="page-header">
                <div class="page-title">
                    <h1><i class="fas fa-random text-primary me-2"></i>Transmutación de Productos</h1>
                    <small class="text-muted">Procesa la transformación de materiales e insumos</small>
                </div>
                <button type="button" class="btn btn-dark btn-sm shadow-sm" data-bs-toggle="modal" data-bs-target="#modalEquivalencia">
                    <i class="fas fa-cog me-1"></i> Configurar Equivalencias
                </button>
            </div>

            <div class="card card-custom">
                <div class="card-header-custom">
                    <h6 class="m-0 font-weight-bold" style="color: var(--primary-color);">
                        <i class="fas fa-plus-circle me-2"></i>Nueva Operación de Transformación
                    </h6>
                </div>
                <div class="card-body p-4">
                    <form id="formTransmutacion">
                        <div class="row align-items-stretch">
                            <div class="col-lg-5">
                                <div class="section-box box-origen">
                                    <div class="section-title text-danger">
                                        <i class="fas fa-minus-circle me-2"></i>Producto Origen (Salida)
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Almacén de Trabajo</label>
                                        <select name="almacen_id" id="trans_almacen" class="form-select shadow-sm" required>
                                            <option value="">Seleccione Almacén...</option>
                                            <?php foreach ($almacenes as $a): ?>
                                                <option value="<?= $a['id'] ?>"><?= htmlspecialchars($a['nombre']) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Producto a Transformar</label>
                                        <select name="producto_origen_id" id="trans_producto_origen" class="form-select" disabled required></select>
                                    </div>
                                    <div class="row g-2">
                                        <div class="col-md-7">
                                            <label class="form-label">Lote Origen</label>
                                            <select name="lote_origen_id" id="trans_lote_origen" class="form-select" disabled required></select>
                                        </div>
                                        <div class="col-md-5">
                                            <label class="form-label">Cantidad Salida</label>
                                            <input type="number" step="0.01" name="cantidad_origen" id="trans_cant_origen" class="form-control" required>
                                            <div class="small mt-1 text-muted">Stock: <span id="trans_stock_disp" class="fw-bold text-danger">0</span></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-2 conversion-arrow">
                                <i class="fas fa-arrow-right fa-3x d-none d-lg-block"></i>
                                <i class="fas fa-arrow-down fa-2x d-lg-none"></i>
                            </div>

                            <div class="col-lg-5">
                                <div class="section-box box-destino">
                                    <div class="section-title text-success">
                                        <i class="fas fa-plus-circle me-2"></i>Producto Destino (Entrada)
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Convertir a:</label>
                                        <select name="producto_destino_id" id="trans_producto_destino" class="form-select" disabled required>
                                            <option value="">Seleccione origen primero</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Lote Destino</label>
                                        <select name="lote_destino_id" id="trans_lote_destino" class="form-select" disabled>
                                            <option value="0">-- Crear Lote Nuevo --</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Cantidad Obtenida (Real)</label>
                                        <input type="number" step="0.01" name="cantidad_destino" id="trans_cant_destino" class="form-control" style="border-color: #68d391;" required>
                                        <div id="info_conversion" class="small mt-1 fw-bold text-primary"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-12">
                                <label class="form-label">Notas / Observaciones del Proceso</label>
                                <textarea name="observaciones" class="form-control" rows="2" placeholder="Describa el motivo o detalles de la transformación..."></textarea>
                            </div>
                        </div>

                        <div class="text-center mt-4">
                            <hr class="my-4" style="opacity: 0.1;">
                            <button type="reset" class="btn btn-light px-4 me-2">
                                <i class="fas fa-eraser me-1"></i> Limpiar
                            </button>
                            <button type="submit" class="btn btn-primary px-5 shadow">
                                <i class="fas fa-check-circle me-1"></i> Procesar Transmutación
                            </button>
                        </div>
                    </form>
                </div>
            </div>

     <div class="card card-custom">
    <div class="card-header-custom d-flex justify-content-between align-middle">
        <h6 class="m-0 font-weight-bold text-dark"><i class="fas fa-history me-2"></i>Historial de Movimientos</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle" id="tablaHistorial">
                <thead class="table-light">
                    <tr>
                        <th width="50px">ID</th>
                        <th>Fecha</th>
                        <th>Origen (Sale)</th>
                        <th>Cant.</th>
                        <th>Destino (Entra)</th>
                        <th>Cant.</th>
                        <th>Responsable</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($historial)): ?>
                        <?php foreach ($historial as $t): ?>
                        <tr>
                            <td><span class="badge bg-light text-dark border">#<?= $t['id'] ?></span></td>
                            <td><small><?= date('d/m/Y H:i', strtotime($t['fecha_registro'])) ?></small></td>
                            <td>
                                <i class="fas fa-minus-circle text-danger me-1"></i>
                                <?= htmlspecialchars($t['producto_origen'] ?? 'N/A') ?>
                            </td>
                            <td class="fw-bold"><?= number_format($t['cant_origen'], 2) ?></td>
                            <td>
                                <i class="fas fa-plus-circle text-success me-1"></i>
                                <?= htmlspecialchars($t['producto_destino'] ?? 'N/A') ?>
                            </td>
                            <td class="fw-bold"><?= number_format($t['cant_destino'], 2) ?></td>
                            <td>
                                <i class="fas fa-user-circle me-1 text-muted"></i>
                                <small><?= htmlspecialchars($t['usuario_nombre'] ?? 'Sistema') ?></small>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted p-4">
                                <i class="fas fa-info-circle me-1"></i> No se encontraron registros de transmutación.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
        </div>
    </div>

    <div class="modal fade" id="modalEquivalencia" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold"><i class="fas fa-cog me-2 text-primary"></i>Configurar Equivalencia</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formNuevaEquivalencia">
                    <div class="modal-body p-4">
                        <div class="alert alert-primary border-0 shadow-sm small d-flex align-items-center" style="border-radius: 12px;">
                            <i class="fas fa-info-circle fa-2x me-3"></i>
                            <div>Define cuántas unidades del producto destino se obtienen por cada unidad del producto origen.</div>
                        </div>
                        
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label">Almacén de Aplicación</label>
                               



    <?php 
    $idSesion = (int)($_SESSION['almacen_id'] ?? 0);
    $esAdmin = ($idSesion === 0);
    ?>

    <?php if ($esAdmin): ?>
        <select name="almacen_id" class="form-select shadow-sm border-primary" required>
            <option value="">-- Seleccione Almacén --</option>
            <?php foreach ($almacenes as $a): ?>
                <?php if($a['id'] > 0): ?>
                    <option value="<?= $a['id'] ?>"><?= htmlspecialchars($a['nombre']) ?></option>
                <?php endif; ?>
            <?php endforeach; ?>
        </select>
    <?php else: ?>
        <?php 
        // Buscamos el nombre real en el array de almacenes si no está en la sesión
        $nombreAlmacen = $_SESSION['almacen_nombre'] ?? 'Almacén Asignado';
        
        foreach ($almacenes as $a) {
            if ((int)$a['id'] === $idSesion) {
                $nombreAlmacen = $a['nombre'];
                break;
            }
        }
        ?>
        <div class="input-group shadow-sm">
            <span class="input-group-text bg-light border-0"><i class="fas fa-lock text-muted"></i></span>
            <input type="text" class="form-control bg-light border-0 fw-bold" value="<?= htmlspecialchars($nombreAlmacen) ?>" readonly>
        </div>
        <input type="hidden" name="almacen_id" value="<?= $idSesion ?>">
    <?php endif; ?>



                            </div>

                            <div class="col-md-6">
                                <label class="form-label text-danger">Producto Origen (Sale)</label>
                                <select name="p_origen" class="form-select border-danger-subtle" required>
                                    <option value="">Buscar producto...</option>
                                    <?php foreach($todosLosProductos as $p): ?>
                                        <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['sku'] . " - " . $p['nombre']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label text-success">Producto Destino (Entra)</label>
                                <select name="p_destino" class="form-select border-success-subtle" required>
                                    <option value="">Buscar producto...</option>
                                    <?php foreach($todosLosProductos as $p): ?>
                                        <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['sku'] . " - " . $p['nombre']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-12">
                                <div class="p-3 bg-light rounded-3 border">
                                    <label class="form-label d-block text-center mb-3">Factor de Rendimiento</label>
                                    <div class="input-group input-group-lg">
                                        <span class="input-group-text bg-white">1 unidad origen =</span>
                                        <input type="number" step="0.0001" name="factor" class="form-control text-center fw-bold text-primary" placeholder="0.00" required>
                                        <span class="input-group-text bg-white">unidades destino</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-primary">Guardar Configuración</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const baseUrl = '/cfsistem/app/controllers/transmutacionesController.php';
        
        // Selectores principales
        const transAlmacen = document.getElementById('trans_almacen');
        const transProdOrigen = document.getElementById('trans_producto_origen');
        const transLoteOrigen = document.getElementById('trans_lote_origen');
        const transProdDestino = document.getElementById('trans_producto_destino');
        const transLoteDestino = document.getElementById('trans_lote_destino');
        const transCantOrigen = document.getElementById('trans_cant_origen');
        const transCantDestino = document.getElementById('trans_cant_destino');
        const infoConversion = document.getElementById('info_conversion');
        const stockSpan = document.getElementById('trans_stock_disp');

        // Inicializar DataTable con diseño Bootstrap 5
   
        // 1. Al cambiar Almacén -> Cargar Productos Origen
        transAlmacen.addEventListener('change', async function() {
            const id = this.value;
            if(!id) return;
            
            try {
                const response = await fetch(`${baseUrl.replace('transmutaciones','mermas')}?action=obtenerProductosAlmacen&almacen_id=${id}`);
                const productos = await response.json();
                
                transProdOrigen.innerHTML = '<option value="">Seleccione Origen...</option>';
                productos.forEach(p => {
                    transProdOrigen.add(new Option(`${p.sku} - ${p.nombre}`, p.id));
                });
                transProdOrigen.disabled = false;
            } catch (e) { console.error("Error cargando productos", e); }
        });

        // 2. Al cambiar Producto Origen -> Lotes y Destinos
        transProdOrigen.addEventListener('change', async function() {
            const pId = this.value;
            const aId = transAlmacen.value;
            if(!pId) return;

            // Cargar Lotes
            const resLotes = await fetch(`${baseUrl}?action=obtenerLotes&producto_id=${pId}&almacen_id=${aId}`);
            const lotes = await resLotes.json();
            transLoteOrigen.innerHTML = '<option value="">Seleccione Lote...</option>';
            lotes.forEach(l => {
                const opt = new Option(`${l.codigo_lote} (Disp: ${l.cantidad_actual})`, l.id);
                opt.dataset.stock = l.cantidad_actual;
                transLoteOrigen.add(opt);
            });
            transLoteOrigen.disabled = false;

            // Cargar Destinos Compatibles
            const resDest = await fetch(`${baseUrl}?action=obtenerDestinosCompatibles&producto_id=${pId}`);
            const destinos = await resDest.json();
            transProdDestino.innerHTML = '<option value="">Seleccione Destino...</option>';
            destinos.forEach(d => {
                const opt = new Option(`${d.sku} - ${d.nombre}`, d.id);
                opt.dataset.factor = d.rendimiento_teorico;
                transProdDestino.add(opt);
            });
            transProdDestino.disabled = false;
        });

        // 3. Al cambiar Lote Origen -> Actualizar Stock Disponible
        transLoteOrigen.addEventListener('change', function() {
            const stock = parseFloat(this.selectedOptions[0]?.dataset.stock || 0);
            stockSpan.textContent = stock.toFixed(2);
            transCantOrigen.max = stock;
        });

        // 4. Al cambiar Producto Destino -> Lotes Destino
        transProdDestino.addEventListener('change', async function() {
            const pId = this.value;
            const aId = transAlmacen.value;
            if(!pId) return;

            const res = await fetch(`${baseUrl}?action=obtenerLotes&producto_id=${pId}&almacen_id=${aId}`);
            const lotes = await res.json();
            transLoteDestino.innerHTML = '<option value="0">-- Crear Lote Nuevo --</option>';
            lotes.forEach(l => {
                transLoteDestino.add(new Option(`Sumar a: ${l.codigo_lote} (Disp: ${l.cantidad_actual})`, l.id));
            });
            transLoteDestino.disabled = false;
            calcularTeorico();
        });

        function calcularTeorico() {
            const factor = parseFloat(transProdDestino.selectedOptions[0]?.dataset.factor || 0);
            const cant = parseFloat(transCantOrigen.value || 0);
            if(factor && cant) {
                const sugerido = (factor * cant).toFixed(2);
                infoConversion.innerHTML = `<i class="fas fa-magic me-1"></i> Rendimiento esperado: ${sugerido}`;
                transCantDestino.placeholder = sugerido;
            } else {
                infoConversion.innerHTML = "";
            }
        }

        transCantOrigen.addEventListener('input', calcularTeorico);

        // 5. Submit Formulario Transmutación
        document.getElementById('formTransmutacion').addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(this);

            if(parseFloat(transCantOrigen.value) > parseFloat(stockSpan.textContent)) {
                alert("⚠️ Cantidad insuficiente en el lote de origen.");
                return;
            }

            try {
                const res = await fetch(`${baseUrl}?action=guardar`, { method: 'POST', body: formData });
                const result = await res.json();
                
                console.log("Debug Respuesta:", result); // Para tu consola de debug
                
                if(result.status === 'success') {
                    alert("✅ Transmutación registrada correctamente.");
                    location.reload();
                } else {
                    alert("❌ Error: " + result.message);
                }
            } catch (e) { alert("Error de conexión con el servidor."); }
        });

        // 6. Submit Nueva Equivalencia
        document.getElementById('formNuevaEquivalencia').addEventListener('submit', async function(e) {
            e.preventDefault();
            const btn = this.querySelector('button[type="submit"]');
            btn.disabled = true;
            
            try {
                const formData = new FormData(this);
                const res = await fetch(`${baseUrl}?action=guardarEquivalencia`, { method: 'POST', body: formData });
                const result = await res.json();
                
                if(result.status === 'success') {
                    alert(result.message);
                    location.reload();
                } else {
                    alert("❌ " + result.message);
                }
            } catch (error) {
                alert("❌ Error de red");
            } finally { btn.disabled = false; }
        });
    });
    </script>
    <script>
        $(document).ready(function() {
    $('#tablaHistorial').DataTable({
        "language": {
            "url": "https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
        },
        "order": [[ 0, "desc" ]], // Ordenar por la primera columna (ID) de forma descendente
        "pageLength": 10,
        "responsive": true,
        "dom": '<"d-flex justify-content-between"f>rt<"d-flex justify-content-between"ip>',
        "drawCallback": function() {
            // Esto quita clases feas que a veces pone DataTables por defecto
            $('.dataTables_paginate > .pagination').addClass('pagination-sm');
        }
    });
});
    </script>
</body>
</html>