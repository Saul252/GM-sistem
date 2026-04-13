<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../controllers/LayoutController.php';
require_once __DIR__ . '/../models/almacen_model.php';
require_once __DIR__ . '/../models/egresos_model.php'; // Cambiado a egresos_model
require_once __DIR__ . '/../models/corteCajaModel.php'; 

// Protección de página
protegerPagina('finanzas_admin');

// Inicialización de Modelos
$almacenModel = new AlmacenModel($conexion);
$egresosModel = new EgresoModel($conexion);
$corteModel   = new CorteCajaModel($conexion);

date_default_timezone_set('America/Mexico_City');

// 1. Identificar jerarquía del usuario
$almacen_sesion = $_SESSION['almacen_id'] ?? 0;
$usuario_id     = $_SESSION['id_usuario'] ?? 0;

// --- LÓGICA DE CONSULTA AJAX (GET) ---
if (isset($_GET['ajax'])) {
    header('Content-Type: application/json');
    
    $periodo    = $_GET['periodo'] ?? 'hoy';
    $f_inicio   = $_GET['f_inicio'] ?? date('Y-m-d');
    $f_fin      = $_GET['f_fin'] ?? date('Y-m-d');
    $almacen_id_req = isset($_GET['almacen_id']) ? intval($_GET['almacen_id']) : 0;

    // Normalización de fechas para coherencia
    if ($periodo === 'hoy') {
        $f_inicio = $f_fin = date('Y-m-d');
    } elseif ($periodo === 'ayer') {
        $f_inicio = $f_fin = date('Y-m-d', strtotime("-1 day"));
    }

    $target = ($almacen_sesion != 0) ? $almacen_sesion : $almacen_id_req;

    try {
        // --- A. SALDOS INICIALES (APERTURAS) ---
        $saldosIniciales = $corteModel->obtenerSaldoInicialMonitor($target, $f_inicio, $f_fin);
        
        $resumenSaldos = ['efectivo' => 0, 'tarjeta' => 0, 'transferencia' => 0, 'total' => 0];

        if ($target == 0) { // Vista Administrador (Consolidado)
            foreach ($saldosIniciales as $s) {
                $resumenSaldos['efectivo']      += $s['monto_efectivo'];
                $resumenSaldos['tarjeta']       += $s['monto_tarjeta'];
                $resumenSaldos['transferencia'] += $s['monto_transferencia'];
                $resumenSaldos['total']         += $s['monto'];
            }
        } else { // Vista Sucursal única
            $resumenSaldos = $saldosIniciales;
        }

        // --- B. VENTAS E INGRESOS ---
        $ventasDetalle = $corteModel->obtenerVentasDetalladas($periodo, $f_inicio, $f_fin, $target);
        
        $resumenVentas = ['efectivo' => 0, 'tarjeta_trans' => 0, 'total_real' => 0, 'abonos' => 0];
        foreach ($ventasDetalle as $v) {
            $resumenVentas['efectivo']      += $v['efectivo'];
            $resumenVentas['tarjeta_trans'] += $v['tarjeta_transferencia'];
            $resumenVentas['total_real']    += $v['dinero_real'];
            $resumenVentas['abonos']        += $v['monto_abono'];
        }

        // --- C. EGRESOS (COMPRAS Y GASTOS) ---
        $egresosDetalle = $egresosModel->obtenerTodosLosEgresos($f_inicio, $f_fin, $target);
        
        $resumenEgresos = ['compras' => 0, 'gastos' => 0, 'total' => 0];
        foreach ($egresosDetalle as $e) {
            if ($e['tipo'] === 'compra') $resumenEgresos['compras'] += $e['total'];
            else $resumenEgresos['gastos'] += $e['total'];
            $resumenEgresos['total'] += $e['total'];
        }

        // --- D. FLUJO DE CAJA FINAL ---
        // (Apertura + Ingresos del día) - Egresos realizados
        $flujoFinal = ($resumenSaldos['total'] + $resumenVentas['total_real']) - $resumenEgresos['total'];

        echo json_encode([
            'status'   => 'success',
            'resumen'  => [
                'apertura'    => $resumenSaldos,
                'ingresos'    => $resumenVentas,
                'egresos'     => $resumenEgresos,
                'flujo_final' => $flujoFinal
            ],
            'detalles' => [
                'ventas'  => $ventasDetalle,
                'egresos' => $egresosDetalle
            ],
            'filtros' => [
                'target' => $target,
                'es_consolidado' => ($target == 0)
            ]
        ]);
        exit;

    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        exit;
    }
}

// --- CARGA INICIAL DE LA VISTA ---
$listaAlmacenes = $almacenModel->getAlmacenes($almacen_sesion); 
$paginaActual = 'Finanzas Administrador';

// Incluimos la vista (ajusta la ruta si el archivo se llama distinto)
require_once __DIR__ . '/../views/finanzas_admin_view.php';