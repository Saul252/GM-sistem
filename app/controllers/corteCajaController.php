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

// 1. Identificar jerarquía del usuario
$almacen_sesion = $_SESSION['almacen_id'] ?? 0;
$usuario_id     = $_SESSION['id_usuario'] ?? 0;

// --- LÓGICA DE GUARDADO (POST) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'guardarCorte') {
    header('Content-Type: application/json');

    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $usuario_id = isset($_SESSION['usuario_id']) ? intval($_SESSION['usuario_id']) : 0;

    if ($usuario_id <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'Usuario no válido en sesión']);
        exit;
    }

    $fecha_corte   = $_POST['fecha_corte'] ?? date('Y-m-d');
    $almacen_req   = isset($_POST['almacen_id']) ? intval($_POST['almacen_id']) : 0;
    $observaciones = $_POST['observaciones'] ?? 'Corte manual';

    $target_save = ($almacen_sesion != 0) ? $almacen_sesion : $almacen_req;

    if ($target_save <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'Almacén no válido']);
        exit;
    }

    try {

        // ===============================
        // 🔥 DATOS DESDE FRONTEND (CORRECTO)
        // ===============================
        $efectivo_real   = floatval($_POST['total_efectivo'] ?? 0);
        $tarjeta         = floatval($_POST['total_tarjeta'] ?? 0);
        $transferencia   = floatval($_POST['total_transferencia'] ?? 0);

        $abono_efectivo      = floatval($_POST['abono_efectivo'] ?? 0);
        $abono_tarjeta       = floatval($_POST['abono_tarjeta'] ?? 0);
        $abono_transferencia = floatval($_POST['abono_transferencia'] ?? 0);

        $abonos_totales = $abono_efectivo + $abono_tarjeta + $abono_transferencia;

        $saldo_favor_usado = floatval($_POST['saldo_favor_usado'] ?? 0);
        $deuda_pendiente   = floatval($_POST['deuda_pendiente'] ?? 0);

        $gastos_totales  = floatval($_POST['gastos_totales'] ?? 0);
        $compras_totales = floatval($_POST['compras_totales'] ?? 0);

        $gran_total_ingresos = floatval($_POST['gran_total_ingresos'] ?? 0);

        // ===============================
        // 🔥 TU FÓRMULA (SE RESPETA)
        // ===============================
        $venta_bruta_calculada = ($efectivo_real + $tarjeta + $transferencia + $deuda_pendiente) - $abonos_totales;

        // ===============================
        // 🔥 PREPARAR DATOS
        // ===============================
        $datosParaGuardar = [
            'fecha_corte'         => $fecha_corte,
            'almacen_id'          => $target_save,
            'usuario_id'          => $usuario_id,
            'venta_bruta'         => $venta_bruta_calculada,
            'total_efectivo'      => $efectivo_real,
            'total_transferencia' => $transferencia,
            'total_tarjeta'       => $tarjeta,
            'abono_efectivo'      => $abono_efectivo,
            'abono_tarjeta'       => $abono_tarjeta,
            'abono_transferencia' => $abono_transferencia,
            'abonos_totales'      => $abonos_totales,
            'saldo_favor_usado'   => $saldo_favor_usado,
            'cobrado_total'       => $efectivo_real + $tarjeta + $transferencia,
            'gastos_totales'      => $gastos_totales,
            'compras_totales'     => $compras_totales,
            'gran_total_ingresos' => $gran_total_ingresos,
            'deuda_pendiente'     => $deuda_pendiente,
            'observaciones'       => $observaciones
        ];

        $resultado = $modelo->agregarCorteManual($datosParaGuardar);

        // ===============================
        // 🔥 APERTURA AUTOMÁTICA
        // ===============================
        if ($resultado['status'] === 'success') {

            $desglose_para_historial = [
                
                'efectivo'      => $efectivo_real,
                'tarjeta'       => $tarjeta,
                'transferencia' => $transferencia
            ];

            $modelo->registrarAperturaDesdeCierre(
                $target_save,
                $usuario_id,
                $desglose_para_historial,
                $fecha_corte
            );
        }

        echo json_encode($resultado);

    } catch (Exception $e) {
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    }

    exit;
}

// --- LÓGICA DE CONSULTA AJAX (GET) ---
if (isset($_GET['ajax'])) {
    header('Content-Type: application/json');

    try {

        $periodo  = $_GET['periodo'] ?? 'hoy';
        $f_inicio = $_GET['f_inicio'] ?? date('Y-m-d');
        $f_fin    = $_GET['f_fin'] ?? date('Y-m-d');
        $almacen_id_req = isset($_GET['almacen_id']) ? intval($_GET['almacen_id']) : 0;

        if ($periodo === 'hoy') {
            $f_inicio = $f_fin = date('Y-m-d');
        } elseif ($periodo === 'ayer') {
            $f_inicio = $f_fin = date('Y-m-d', strtotime("-1 day"));
        }

        $target = ($almacen_sesion != 0) ? $almacen_sesion : $almacen_id_req;

        $esUnSoloDia = ($f_inicio === $f_fin);

        $detalles = $modelo->obtenerVentasDetalladas($periodo, $f_inicio, $f_fin, $target);
        $totales  = $modelo->obtenerSumasCorte($periodo, $f_inicio, $f_fin, $target);

        // 🔥 DEBUG REAL
        $comprasTotales = $egresoModel->obtenerSumaEgresos($f_inicio, $f_fin, $target, 'compra');
        $gastosTotales  = $egresoModel->obtenerSumaEgresos($f_inicio, $f_fin, $target, 'gasto');
        

        // 🔥 FORZAR VALOR CORRECTO
        $comprasTotales = is_array($comprasTotales) ? ($comprasTotales['total'] ?? 0) : $comprasTotales;
        $gastosTotales  = is_array($gastosTotales) ? ($gastosTotales['total'] ?? 0) : $gastosTotales;

        $comprasTotales = floatval($comprasTotales);
        $gastosTotales  = floatval($gastosTotales);

        $saldo_data = null;
        if ($esUnSoloDia) {
            $saldo_data = $modelo->obtenerSaldoInicialMonitor($target, $f_inicio, $f_fin);
        }

        echo json_encode([
            'status'          => 'success',
            'detalles'        => $detalles,
            'totales'         => $totales,
            'saldo_inicial'   => $saldo_data,
            'es_lista'        => ($target == 0),
            'mostrar_saldo'   => $esUnSoloDia,
            'comprasTotales'  => $comprasTotales,
            'gastosTotales'   => $gastosTotales
        ]);

    } catch (Exception $e) {

        // 🔥 AQUÍ VAS A VER EL ERROR REAL
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    }

    exit;
}
// --- CARGA INICIAL DE LA VISTA ---
$listaAlmacenes = $almacenModel->getAlmacenes($almacen_sesion); 
$paginaActual = 'Corte de Caja';
require_once __DIR__ . '/../views/corteCaja_view.php';