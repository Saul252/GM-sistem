<?php
$almacen_usuario = intval($_SESSION['almacen_id'] ?? 0); // 0 = admin
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial de Lotes</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <?php require_once __DIR__ . '/layout/icono.php' ?>
  
    <?php if (function_exists('cargarEstilos')) { cargarEstilos(); } ?>
    
    <style>
        :root { 
            --sidebar-width: 260px; 
            --navbar-height: 65px;
            --apple-bg: #f5f5f7;
            --accent-blue: #007aff;
        }

        body { 
            background-color: var(--apple-bg); 
            font-family: 'SF Pro Display', -apple-system, sans-serif;
            color: #1d1d1f;
        }

        .main-content { 
            margin-left: var(--sidebar-width); 
            padding: 40px; 
            padding-top: calc(var(--navbar-height) + 20px); 
        }

        .card-premium { 
            border: none; 
            border-radius: 20px; 
            box-shadow: 0 8px 30px rgba(0,0,0,0.04); 
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
        }

        .badge-ubicacion { 
            background-color: #f2f2f7; 
            color: #1d1d1f; 
            border: 1px solid #d1d1d6; 
            padding: 0.4rem 0.7rem;
            font-size: 0.75rem;
            font-weight: 600;
            border-radius: 8px;
        }

        /* DataTables Custom */
        .dataTables_wrapper .pagination .page-item.active .page-link {
            background-color: var(--accent-blue);
            border-color: var(--accent-blue);
            border-radius: 8px;
        }

        .table thead th {
            background: #fbfbfd;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #86868b;
            border-bottom: 1px solid #d1d1d6;
        }

        @media (max-width: 768px) { 
            .main-content { margin-left: 0; padding: 20px; padding-top: 90px; } 
        }
    </style>
</head>
<body>
    <?php if (function_exists('renderizarLayout')) { renderizarLayout($paginaActual); } ?>

    <main class="main-content">
       



    <h3 class="mb-3">📦 Historial de Lotes</h3>

    <!-- FILTROS -->
    <div class="card p-3 mb-3">

        <div class="row g-3">

            <!-- ALMACÉN SOLO ADMIN -->
            <?php if ($almacen_usuario == 0): ?>
            <div class="col-md-4">
                <label>Almacén</label>
                <select id="filtroAlmacen" class="form-select">
                    <option value="0">Todos</option>
                    <?php foreach ($listaAlmacenes as $a): ?>
                        <option value="<?= $a['id'] ?>">
                            <?= htmlspecialchars($a['nombre']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php endif; ?>

            <!-- PRODUCTO -->
            <div class="col-md-6">
                <label>Producto</label>
                <select id="filtroProducto" class="form-select">
                    <option value="">Selecciona producto</option>
                    <?php foreach ($productos as $p): ?>
                        <option value="<?= $p['id'] ?>">
                            <?= htmlspecialchars($p['nombre']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- BOTÓN -->
            <div class="col-md-2 d-grid">
                <label class="invisible">.</label>
                <button class="btn btn-dark" onclick="cargarHistorial()">
                    Consultar
                </button>
            </div>

        </div>
    </div>
<div class="row g-3 mb-3">

    <!-- TOTAL CANTIDAD INICIAL -->
    <div class="col-md-6 col-lg-3">
        <div class="card shadow-sm border-0 rounded-4 p-3 widget-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small">Cantidad Inicial</div>
                    <h3 class="fw-bold mb-0" id="total_inicial">0</h3>
                </div>
                <div class="fs-2 text-primary">
                    📦
                </div>
            </div>
        </div>
    </div>

    <!-- TOTAL CANTIDAD ACTUAL -->
    <div class="col-md-6 col-lg-3">
        <div class="card shadow-sm border-0 rounded-4 p-3 widget-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small">Cantidad Actual</div>
                    <h3 class="fw-bold mb-0" id="total_actual">0</h3>
                </div>
                <div class="fs-2 text-success">
                    📊
                </div>
            </div>
        </div>
    </div>

</div>
    <!-- TABLA -->
    <div class="card p-3">
        <div class="table-responsive">
            

            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Lote</th>
                        <th>Producto</th>
                        <th>Almacén</th>
                        <th>Fecha</th>
                        <th>Inicial</th>
                        <th>Actual</th>
                        <th>Costo</th>
                        <th>Estado</th>
                        <th>Movimientos</th>
                    </tr>
                </thead>

                <tbody id="tablaHistorial">
                    <tr>
                        <td colspan="9" class="text-center text-muted">
                            Selecciona un producto
                        </td>
                    </tr>
                </tbody>

            </table>

        </div>
    </div>

</div>
<div class="card p-3 mt-4">
    <h5 class="mb-3">🧾 Movimientos del lote</h5>

    <div class="table-responsive">
        <table class="table table-sm table-hover">
            <thead>
                <tr>
                     <th>Venta</th>
                     <th>cliente</th>
                    <th>Fecha</th>
                    <th>Lote</th>
                    <th>Cantidad</th>
                    <th>Precio Venta</th>
                    <th>Costo</th>
                    <th>Ganancia</th>
                </tr>
            </thead>

            <tbody id="tablaMovimientosLote">
                <tr>
                    <td colspan="5" class="text-center text-muted">
                        Da clic en "Ver"
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
<!-- JS -->
<div class="card p-3 mt-4">
    <h5 class="mb-3">📊 Traspasos del lote</h5>

    <div class="table-responsive">
        <table class="table table-sm table-bordered">
            <thead>
                <tr>
                    <th>Tipo</th>
                    <th>Fecha</th>
                     <th>Movimiento id</th>
                     <th>Almacen origen</th>                    
                    <th>Lote origen</th>
                    <th>Almacen destino</th>
                    <th>lote destino</th>
                    <th>Cantidad</th>
                    
                </tr>
            </thead>
            <tbody id="tablaTraspasosLote">
                <tr>
                    <td colspan="9" class="text-center text-muted">
                        Selecciona un lote
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>


</body>
</html>
          </main>

 
    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function cargarHistorial() {

    const producto = $('#filtroProducto').val();
    const almacen  = $('#filtroAlmacen').val() ?? 0;

    if (!producto) {
        alert("Selecciona un producto");
        return;
    }

    // 🔥 dispara SIEMPRE traspasos (no depende del resultado)
    cargarTraspasos(producto, almacen);

    $.ajax({
        url: '/cfsistem/app/controllers/lotesHistorialController.php',
        type: 'GET',
        data: {
            action: 'obtenerLotes',
            producto_id: producto,
            almacen_id: almacen
        },
        dataType: 'json',

        success: function(res) {

            console.log(res);

            $('#total_inicial').text(res.totales.total_cantidad_inicial);
            $('#total_actual').text(res.totales.total_cantidad_actual);

            let html = '';

            if (!res.success || !res.data.length) {

                $('#tablaHistorial').html(`
                    <tr><td colspan="9" class="text-center text-muted">Sin datos</td></tr>
                `);

                $('#tablaMovimientosLote').html(`
                    <tr><td colspan="8" class="text-center text-muted">Sin datos</td></tr>
                `);

                return;
            }

            res.data.forEach(lote => {

                let color = 'secondary';
                if (lote.estado_lote === 'activo') color = 'primary';
                if (lote.estado_lote === 'agotado') color = 'danger';
                if (lote.estado_lote === 'bloqueado') color = 'dark';

                html += `
                    <tr>
                        <td>${lote.codigo_lote}</td>
                        <td>${lote.producto_nombre}</td>
                        <td>${lote.almacen_nombre}</td>
                        <td>${lote.fecha_ingreso}</td>
                        <td>${lote.cantidad_inicial}</td>
                        <td>${lote.cantidad_actual}</td>
                        <td>$${parseFloat(lote.precio_compra_unitario || 0).toFixed(2)}</td>
                        <td>
                            <span class="badge bg-${color}">
                                ${lote.estado_lote}
                            </span>
                        </td>
                        <td>
                            <button 
                                class="btn btn-sm btn-outline-primary"
                                onclick="verMovimientos(${lote.lote_id})">
                                Ver
                            </button>
                        </td>
                    </tr>
                `;
            });

            $('#tablaHistorial').html(html);
        },

        error: function(xhr) {
            console.log(xhr.responseText);
            alert("Error en servidor");
        }
    });
}
function cargarTraspasos(producto, almacen) {

    // 🔥 fallback por si llegan undefined/null
    producto = producto || $('#filtroProducto').val();
    almacen  = (almacen !== undefined && almacen !== null) 
                ? almacen 
                : ($('#filtroAlmacen').val() ?? 0);

    // 🔥 evita que dispare vacío (pero sin alert molesto)
    if (!producto) return;

    $.ajax({
        url: '/cfsistem/app/controllers/lotesHistorialController.php',
        type: 'GET',
        data: {
            action: 'obtenerTraspasos',
            producto_id: producto,
            almacen_id: almacen
        },
        dataType: 'json',

        success: function(res) {

            console.log('TRASPASOS:', res);

            let html = '';

            if (!res.success || !res.data || res.data.length === 0) {
                $('#tablaTraspasosLote').html(`
                    <tr>
                        <td colspan="8" class="text-center text-muted">
                            Sin traspasos
                        </td>
                    </tr>
                `);
                return;
            }

            res.data.forEach(t => {
                html += `
                    <tr>
                        <td>TRASPASO</td>
                        <td>${t.fecha ?? '-'}</td>
                         

                        <td>${t.movimiento_id?? '-'}</td>
                         <td>${t.almacen_origen_id ?? '-'}</td>
                         <td>${t.lote_origen_id ?? '-'}</td>
                          
                    
                        <td>${t.almacen_destino_id ?? '-'}</td>
                        <td>${t.lote_destino_id ?? '-'}</td>                     
                        
                        <td>${t.cantidad ?? 0}</td>
                       
                    </tr>
                `;
            });

            $('#tablaTraspasosLote').html(html);
        },

        error: function(xhr) {
            console.error(xhr.responseText);
            alert("Error al cargar traspasos");
        }
    });
}

// 🔥 sin cambios relevantes aquí (solo limpio)
function verMovimientos(lote_id) {

    $.ajax({
        url: '/cfsistem/app/controllers/lotesHistorialController.php',
        type: 'GET',
        data: {
            action: 'obtenerVentasLote',
            lote_id: lote_id
        },
        dataType: 'json',

        success: function(res) {

            let html = '';

            if (!res.success || !res.data || res.data.length === 0) {
                $('#tablaMovimientosLote').html(`
                    <tr>
                        <td colspan="8" class="text-center text-muted">
                            Sin movimientos
                        </td>
                    </tr>
                `);
                return;
            }

            res.data.forEach(mov => {

                let ganancia = (
                    (mov.precio_venta_pactado || 0) - 
                    (mov.costo_compra_historico || 0)
                ) * (mov.cantidad_salida || 0);

                html += `
                    <tr>
                        <td>${mov.venta_id}</td>
                        <td>${mov.cliente}</td>
                        <td>${mov.fecha_movimiento}</td>
                        <td>${mov.nombre_lote}</td>
                        <td>${mov.cantidad_salida}</td>
                        <td>$${parseFloat(mov.precio_venta_pactado || 0).toFixed(2)}</td>
                        <td>$${parseFloat(mov.costo_compra_historico || 0).toFixed(2)}</td>
                        <td class="text-success">
                            $${ganancia.toFixed(2)}
                        </td>
                    </tr>
                `;
            });

            $('#tablaMovimientosLote').html(html);
        },

        error: function(xhr) {
            console.error(xhr.responseText);
            alert("Error al cargar movimientos");
        }
    });
}


// 🔥 cambio de almacén (igual)
$('#filtroAlmacen').on('change', function () {

    let almacen = $(this).val();

    $.ajax({
        url: '/cfsistem/app/controllers/lotesHistorialController.php',
        type: 'GET',
        data: {
            action: 'productos',
            almacen_id: almacen
        },
        dataType: 'json',
        success: function (res) {

            let html = `<option value="">Selecciona producto</option>`;

            if (res.success && res.data.length > 0) {
                res.data.forEach(p => {
                    html += `<option value="${p.id}">${p.nombre}</option>`;
                });
            }

            $('#filtroProducto').html(html);
        },
        error: function () {
            alert("Error al cargar productos");
        }
    });
});
</script>
</body>
</html>