<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../controllers/LayoutController.php';
require_once __DIR__ . '/../models/egresos_model.php';
require_once __DIR__ . '/../models/corteCajaModel.php'; // <--- AGREGADO: Necesitas el modelo de cortes

protegerPagina('finanzas_admin');

$egresoModel = new EgresoModel($conexion);
$corteModel = new CorteCajaModel($conexion); // <--- AGREGADO: Instancia el modelo

date_default_timezone_set('America/Mexico_City');

$almacen_sesion = $_SESSION['almacen_id'] ?? 0;
$rol_id = $_SESSION['rol_id'] ?? 0;

if (isset($_GET['ajax'])) {
    if (ob_get_level()) ob_clean();
    header('Content-Type: application/json');

    $periodo    = $_GET['periodo'] ?? 'hoy';
    $almacen_id = isset($_GET['almacen_id']) ? intval($_GET['almacen_id']) : 0;
    $target = ($rol_id != 1) ? $almacen_sesion : $almacen_id;

    $f_inicio = date('Y-m-d');
    $f_fin    = date('Y-m-d');

    switch ($periodo) {
        case 'ayer':
            $f_inicio = $f_fin = date('Y-m-d', strtotime("-1 day"));
            break;
        case 'semana':
            $f_inicio = date('Y-m-d', strtotime('monday this week'));
            $f_fin    = date('Y-m-d', strtotime('sunday this week'));
            break;
        case 'mes':
            $f_inicio = date('Y-m-01');
            $f_fin    = date('Y-m-t');
            break;
        case 'personalizado':
            if (!empty($_GET['f_inicio'])) $f_inicio = $_GET['f_inicio'];
            if (!empty($_GET['f_fin']))    $f_fin    = $_GET['f_fin'];
            break;
    }

    try {
        // Consultas
        $compras = $egresoModel->obtenerTodosLosEgresos($f_inicio, $f_fin, $target, 'compra');
        $gastos  = $egresoModel->obtenerTodosLosEgresos($f_inicio, $f_fin, $target, 'gasto');
        $comprasTotales = $egresoModel->obtenerSumaEgresos($f_inicio, $f_fin, $target, 'compra');
        $gastosTotales  = $egresoModel->obtenerSumaEgresos($f_inicio, $f_fin, $target, 'gasto');
        // Corregido: Enviamos las fechas correctas según tu lógica de modelo
        $saldos_raw = $corteModel->obtenerSaldoInicialMonitor($target, $f_fin, $f_inicio);
        $ventas = $corteModel->obtenerVentasDetalladas($periodo, $f_inicio, $f_fin, $target);

        echo json_encode([
            'status'  => 'success',
            'compras' => $compras,
            'gastos'  => $gastos,
            'comprasTotales' => $comprasTotales,
            'gastosTotales'  => $gastosTotales,
            
            'saldos_raw' => $saldos_raw, // <--- AGREGADO AL JSON
            'ventas'  => $ventas,     // <--- AGREGADO AL JSON
            'filtros' => [
                'inicio' => $f_inicio,
                'fin'    => $f_fin,
                'target' => $target
            ]
        ]);
        exit;

    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        exit;
    }
}

// Carga de almacenes para el select
$listaAlmacenes = ($rol_id == 1) ? $egresoModel->obtenerAlmacenesActivos() : [];

$tituloPagina = "Panel de Finanzas - Egresos";
require_once __DIR__ . '/../views/finanzasAdministrador_view.php';