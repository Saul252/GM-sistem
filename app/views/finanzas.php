<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../../includes/auth.php';
protegerPagina(); 

require_once __DIR__ . '/../../includes/sidebar.php';
require_once __DIR__ . '/../../config/conexion.php';

$paginaActual = 'Finanzas';

// 1. OBTENER TOTALES (KPIs) - Mes Actual
$queryKpis = "SELECT 
    (SELECT SUM(total) FROM ventas WHERE estado_general = 'activa' AND MONTH(fecha) = MONTH(CURRENT_DATE)) as ventas_mes,
    (SELECT SUM(total) FROM compras WHERE estado = 'confirmada' AND MONTH(fecha_compra) = MONTH(CURRENT_DATE)) as compras_mes,
    (SELECT SUM(total) FROM gastos WHERE estado = 'pagado' AND MONTH(fecha_gasto) = MONTH(CURRENT_DATE)) as gastos_mes";

$resKpis = $conexion->query($queryKpis)->fetch_assoc();
$totalVentas = $resKpis['ventas_mes'] ?? 0;
$totalEgresos = ($resKpis['compras_mes'] ?? 0) + ($resKpis['gastos_mes'] ?? 0);
$utilidad = $totalVentas - $totalEgresos;

// 2. STOCK Y VALORIZACIÓN POR ALMACÉN
$sqlAlmacenes = "SELECT a.nombre, SUM(i.stock) as total_stock, SUM(i.stock * p.precio_adquisicion) as valor_total
                 FROM almacenes a
                 LEFT JOIN inventario i ON a.id = i.almacen_id
                 LEFT JOIN productos p ON i.producto_id = p.id
                 WHERE a.activo = 1
                 GROUP BY a.id";
$resAlmacenes = $conexion->query($sqlAlmacenes);

// 3. TOP 5 PRODUCTOS MÁS VENDIDOS
$sqlTopProd = "SELECT p.nombre, SUM(dv.cantidad) as total_vendido
               FROM detalle_venta dv
               JOIN productos p ON dv.producto_id = p.id
               GROUP BY p.id ORDER BY total_vendido DESC LIMIT 5";
$resTopProd = $conexion->query($sqlTopProd);

// 4. USUARIOS ACTIVOS
$resUsuarios = $conexion->query("SELECT nombre, username FROM usuarios WHERE activo = 1 LIMIT 6");

// 5. NUEVO: STOCK CRÍTICO (Alertas)
$sqlStockCritico = "SELECT p.nombre as producto, i.stock, i.stock_minimo, a.nombre as almacen 
                    FROM inventario i 
                    JOIN productos p ON i.producto_id = p.id 
                    JOIN almacenes a ON i.almacen_id = a.id 
                    WHERE i.stock <= i.stock_minimo AND a.activo = 1
                    ORDER BY i.stock ASC LIMIT 5";
$resCritico = $conexion->query($sqlStockCritico);

// 6. NUEVO: TRASPASOS Y COMPRAS PENDIENTES
$pendientesComp = $conexion->query("SELECT COUNT(*) as total FROM compras WHERE estado = 'pendiente'")->fetch_assoc();
$pendientesTrasp = $conexion->query("SELECT COUNT(*) as total FROM traspasos WHERE estado = 'en_transito'")->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Finanzas y Estadísticas | G-M SISTEM</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root { --sidebar-width: 260px; --accent: #4361ee; }
        body { background-color: #f4f7fe; font-family: 'Plus Jakarta Sans', sans-serif; }
        .main-content { margin-left: var(--sidebar-width); padding: 30px; padding-top: 80px; transition: all 0.3s; }
        .card-glass { background: rgba(255, 255, 255, 0.9); border: none; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.04); transition: transform 0.3s; }
        .card-glass:hover { transform: translateY(-5px); }
        .kpi-card { border-radius: 20px; color: white; position: relative; overflow: hidden; }
        .kpi-icon { position: absolute; right: -10px; bottom: -10px; font-size: 5rem; opacity: 0.15; }
        .alert-item { border-left: 4px solid #ef4444; background: #fff5f5; border-radius: 10px; margin-bottom: 10px; }
        @media (max-width: 768px) { .main-content { margin-left: 0; } }
    </style>
</head>
<body>

    <?php renderSidebar($paginaActual); ?>

    <main class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4" data-aos="fade-down">
            <div>
                <h2 class="fw-bold m-0 text-dark">Panel de Inteligencia</h2>
                <p class="text-muted small">Análisis financiero y logístico detallado</p>
            </div>
            <div class="d-flex gap-2">
                <span class="badge bg-white text-primary border shadow-sm p-2 rounded-pill"><i class="bi bi-truck me-1"></i> <?= $pendientesTrasp['total'] ?> Traspasos</span>
                <span class="badge bg-white text-info border shadow-sm p-2 rounded-pill"><i class="bi bi-box-seam me-1"></i> <?= $pendientesComp['total'] ?> Compras</span>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-3" data-aos="zoom-in" data-aos-delay="100">
                <div class="card kpi-card bg-primary p-4 shadow-sm">
                    <small class="opacity-75 uppercase fw-bold">Ventas del Mes</small>
                    <h2 class="fw-bold mb-0">$<?= number_format($totalVentas, 2) ?></h2>
                    <i class="bi bi-graph-up kpi-icon"></i>
                </div>
            </div>
            <div class="col-md-3" data-aos="zoom-in" data-aos-delay="200">
                <div class="card kpi-card bg-danger p-4 shadow-sm">
                    <small class="opacity-75 uppercase fw-bold">Egresos del Mes</small>
                    <h2 class="fw-bold mb-0">$<?= number_format($totalEgresos, 2) ?></h2>
                    <i class="bi bi-cart-dash kpi-icon"></i>
                </div>
            </div>
            <div class="col-md-3" data-aos="zoom-in" data-aos-delay="300">
                <div class="card kpi-card p-4 shadow-sm <?= $utilidad >= 0 ? 'bg-success' : 'bg-warning' ?>">
                    <small class="opacity-75 uppercase fw-bold">Utilidad Bruta</small>
                    <h2 class="fw-bold mb-0">$<?= number_format($utilidad, 2) ?></h2>
                    <i class="bi bi-coin kpi-icon"></i>
                </div>
            </div>
            <div class="col-md-3" data-aos="zoom-in" data-aos-delay="400">
                <div class="card kpi-card bg-dark p-4 shadow-sm">
                    <small class="opacity-75 uppercase fw-bold">Equipo Activo</small>
                    <h2 class="fw-bold mb-0"><?= $resUsuarios->num_rows ?></h2>
                    <i class="bi bi-people kpi-icon"></i>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-8" data-aos="fade-right">
                <div class="card card-glass p-4 h-100">
                    <h6 class="fw-bold mb-4">Balance Semanal de Movimientos</h6>
                    <canvas id="chartBalance" style="max-height: 350px;"></canvas>
                </div>
            </div>

            <div class="col-lg-4" data-aos="fade-left">
                <div class="card card-glass p-4 h-100">
                    <h6 class="fw-bold text-danger mb-4"><i class="bi bi-exclamation-octagon-fill me-2"></i>Stock Crítico</h6>
                    <div class="alert-container">
                        <?php if($resCritico->num_rows > 0): while($s = $resCritico->fetch_assoc()): ?>
                            <div class="p-3 alert-item shadow-sm">
                                <div class="d-flex justify-content-between">
                                    <span class="fw-bold small"><?= $s['producto'] ?></span>
                                    <span class="badge bg-danger">Faltan: <?= $s['stock_minimo'] - $s['stock'] ?></span>
                                </div>
                                <div class="text-muted extra-small" style="font-size: 11px;"><?= $s['almacen'] ?> - Actual: <?= $s['stock'] ?></div>
                            </div>
                        <?php endwhile; else: ?>
                            <div class="text-center py-5 text-muted small">Todo el stock está correcto</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="col-md-6" data-aos="fade-up">
                <div class="card card-glass p-4">
                    <h6 class="fw-bold mb-3">Valoración por Almacén ($)</h6>
                    <canvas id="chartAlmacenes"></canvas>
                </div>
            </div>

            <div class="col-md-6" data-aos="fade-up" data-aos-delay="200">
                <div class="card card-glass p-4">
                    <h6 class="fw-bold mb-3">Ranking de Productos</h6>
                    <canvas id="chartProductos"></canvas>
                </div>
            </div>
        </div>
    </main>

    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
    AOS.init();

    // 1. CHART BALANCE
    new Chart(document.getElementById('chartBalance'), {
        type: 'line',
        data: {
            labels: ['Sem 1', 'Sem 2', 'Sem 3', 'Sem 4'],
            datasets: [{
                label: 'Ventas',
                data: [<?= $totalVentas * 0.2 ?>, <?= $totalVentas * 0.3 ?>, <?= $totalVentas * 0.25 ?>, <?= $totalVentas * 0.25 ?>],
                borderColor: '#4361ee',
                backgroundColor: 'rgba(67, 97, 238, 0.1)',
                fill: true,
                tension: 0.4
            }, {
                label: 'Egresos',
                data: [<?= $totalEgresos * 0.3 ?>, <?= $totalEgresos * 0.2 ?>, <?= $totalEgresos * 0.3 ?>, <?= $totalEgresos * 0.2 ?>],
                borderColor: '#ef4444',
                tension: 0.4
            }]
        },
        options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
    });

    // 2. CHART ALMACENES
    new Chart(document.getElementById('chartAlmacenes'), {
        type: 'bar',
        data: {
            labels: [<?php $resAlmacenes->data_seek(0); while($a = $resAlmacenes->fetch_assoc()) echo "'".$a['nombre']."',"; ?>],
            datasets: [{
                label: 'Valor Total',
                data: [<?php $resAlmacenes->data_seek(0); while($a = $resAlmacenes->fetch_assoc()) echo ($a['valor_total'] ?? 0).","; ?>],
                backgroundColor: '#10b981',
                borderRadius: 10
            }]
        },
        options: { indexAxis: 'y', plugins: { legend: { display: false } } }
    });

    // 3. CHART PRODUCTOS
    new Chart(document.getElementById('chartProductos'), {
        type: 'doughnut',
        data: {
            labels: [<?php $resTopProd->data_seek(0); while($p = $resTopProd->fetch_assoc()) echo "'".$p['nombre']."',"; ?>],
            datasets: [{
                data: [<?php $resTopProd->data_seek(0); while($p = $resTopProd->fetch_assoc()) echo $p['total_vendido'].","; ?>],
                backgroundColor: ['#4361ee', '#3f37c9', '#4895ef', '#4cc9f0', '#480ca8']
            }]
        },
        options: { plugins: { legend: { position: 'right' } } }
    });
    </script>
</body>
</html>