
        <?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../controllers/LayoutController.php';
require_once __DIR__ . '/../models/corteCajaModel.php'; 
require_once __DIR__ . '/../models/egresos_model.php';
require_once __DIR__ . '/../models/almacen_model.php';

protegerPagina('corteCaja');
$egresoModel = new EgresoModel($conexion);
$modelo = new CorteCajaModel($conexion);
$almacenModel = new AlmacenModel($conexion);
date_default_timezone_set('America/Mexico_City');
if (ob_get_level()) ob_clean();

// 1. Identificar jerarquía del usuario
$almacen_sesion = $_SESSION['almacen_id'] ?? 0;
$usuario_id     = $_SESSION['id_usuario'] ?? 0;

// --- LÓGICA DE GUARDADO (POST) ---


// --- LÓGICA DE CONSULTA AJAX (GET) ---
if (isset($_GET['ajax'])) {

    if (ob_get_level()) ob_clean(); // 🔥 evita JSON corrupto
    header('Content-Type: application/json');

    try {

        $periodo  = $_GET['periodo'] ?? 'hoy';
        $f_inicio = $_GET['f_inicio'] ?? date('Y-m-d');
        $f_fin    = $_GET['f_fin'] ?? date('Y-m-d');
        $almacen_id_req = isset($_GET['almacen_id']) ? intval($_GET['almacen_id']) : 0;

        // 🔥 manejar periodos
        if ($periodo === 'hoy') {
            $f_inicio = $f_fin = date('Y-m-d');
        } elseif ($periodo === 'ayer') {
            $f_inicio = $f_fin = date('Y-m-d', strtotime("-1 day"));
        }

        // 🔥 IMPORTANTE: respeta selección del usuario
        $target = ($almacen_id_req > 0) ? $almacen_id_req : $almacen_sesion;

        $esUnSoloDia = ($f_inicio === $f_fin);

        // 🔥 FIX FECHAS (DATETIME)
        $f_inicio_sql = $f_inicio . " 00:00:00";
        $f_fin_sql    = $f_fin . " 23:59:59";

        // ================= DATOS PRINCIPALES =================
        $detalles = $modelo->obtenerVentasDetalladas($periodo, $f_inicio, $f_fin, $target);
        $totales  = $modelo->obtenerSumasCorte($periodo, $f_inicio, $f_fin, $target);

        // ================= EGRESOS =================
        $egresos = $egresoModel->obtenerTodosLosEgresos(
            $f_inicio_sql,
            $f_fin_sql,
            $target,
            'todos'
        );

        $compras = [];
        $gastos  = [];

        foreach ($egresos as $e) {
            if (($e['tipo'] ?? '') === 'compra') {
                $compras[] = $e;
            } else {
                $gastos[] = $e;
            }
        }

        // ================= TOTALES =================
        $comprasTotales = $egresoModel->obtenerSumaEgresos($f_inicio_sql, $f_fin_sql, $target, 'compra');
        $gastosTotales  = $egresoModel->obtenerSumaEgresos($f_inicio_sql, $f_fin_sql, $target, 'gasto');

        $comprasTotales = is_array($comprasTotales) ? ($comprasTotales['total'] ?? 0) : $comprasTotales;
        $gastosTotales  = is_array($gastosTotales) ? ($gastosTotales['total'] ?? 0) : $gastosTotales;

        $comprasTotales = floatval($comprasTotales);
        $gastosTotales  = floatval($gastosTotales);

        // ================= MÉTODOS =================
        $gastosMetodo   = $egresoModel->obtenerGastosPorMetodo($f_inicio_sql, $f_fin_sql, $target);
        $comprasMetodo  = $egresoModel->obtenerComprasPorMetodo($f_inicio_sql, $f_fin_sql, $target);

        // ================= SALDO =================
        $saldo_data = null;
        if ($esUnSoloDia) {
            $saldo_data = $modelo->obtenerSaldoInicialMonitor($target, $f_inicio, $f_fin);
        }

        // ================= RESPUESTA =================
        echo json_encode([
            'status'          => 'success',
            'detalles'        => $detalles,
            'compras'         => $compras,
            'gastos'          => $gastos,
            'totales'         => $totales,
            'saldo_inicial'   => $saldo_data,
            'es_lista'        => ($target == 0),
            'mostrar_saldo'   => $esUnSoloDia,
            'comprasTotales'  => $comprasTotales,
            'gastosTotales'   => $gastosTotales,
            'gastosMetodo'    => $gastosMetodo,
            'comprasMetodo'   => $comprasMetodo,
            'filtros' => [
                'inicio' => $f_inicio_sql,
                'fin'    => $f_fin_sql,
                'target' => $target
            ]
        ]);

    } catch (Exception $e) {

        echo json_encode([
            'status'  => 'error',
            'message' => $e->getMessage()
        ]);
    }

    exit;
}
// --- CARGA INICIAL DE LA VISTA ---
$listaAlmacenes = $almacenModel->getAlmacenes($almacen_sesion); 
$paginaActual = 'Corte de Caja';
require_once __DIR__ . '/../views/finanzasAdministrador_view.php';//version actualizada