<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../controllers/LayoutController.php';
require_once __DIR__ . '/../models/corteCajaModel.php'; 
require_once __DIR__ . '/../models/almacen_model.php';

protegerPagina('corteCaja');

$modelo = new CorteCajaModel($conexion);
$almacenModel = new AlmacenModel($conexion);
date_default_timezone_set('America/Mexico_City');

// 1. Identificar jerarquía del usuario
$almacen_sesion = $_SESSION['almacen_id'] ?? 0;
$usuario_id     = $_SESSION['id_usuario'] ?? 0;

// --- LÓGICA DE GUARDADO (POST) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'guardarCorte') {
    header('Content-Type: application/json');

    $fecha_corte   = $_POST['fecha_corte'] ?? date('Y-m-d');
    $almacen_req   = isset($_POST['almacen_id']) ? intval($_POST['almacen_id']) : 0;
    $observaciones = isset($_POST['observaciones']) ? trim($_POST['observaciones']) : 'Corte manual';

    $target_save = ($almacen_sesion != 0) ? $almacen_sesion : $almacen_req;

    if ($target_save <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'Almacén no válido']);
        exit;
    }

    try {
        $resumen = $modelo->obtenerSumasCorte('personalizado', $fecha_corte, $fecha_corte, $target_save);

        if (!$resumen || isset($resumen['error'])) {
            throw new Exception("Error al calcular montos para la fecha seleccionada.");
        }

        // Definimos las variables con el ajuste de Venta Bruta
        $efectivo_real       = floatval($resumen['total_efectivo']);
        $transferencia       = floatval($resumen['total_transferencia']);
        $tarjeta             = floatval($resumen['total_tarjeta']);
        $deuda_pendiente     = floatval($resumen['deuda_pendiente']);
        $abonos_totales      = floatval($resumen['abonos_totales']);
        
        $venta_bruta_calculada = ($efectivo_real + $tarjeta + $transferencia + $deuda_pendiente) - $abonos_totales;

        $datosParaGuardar = [
            'fecha_corte'         => $fecha_corte,
            'almacen_id'          => $target_save,
            'usuario_id'          => $usuario_id,
            'venta_bruta'         => $venta_bruta_calculada,
            'total_efectivo'      => $efectivo_real,
            'total_transferencia' => $transferencia,
            'total_tarjeta'       => $tarjeta,
            'abono_efectivo'      => $resumen['abono_efectivo'],
            'abono_tarjeta'       => $resumen['abono_tarjeta'],
            'abono_transferencia' => $resumen['abono_transferencia'],
            'abonos_totales'      => $abonos_totales,
            'saldo_favor_usado'   => $resumen['saldo_favor_usado'],
            'cobrado_total'       => $resumen['cobrado_total'],
            'gastos_totales'      => $resumen['gastos_totales'],
            'compras_totales'     => $resumen['compras_totales'],
            'gran_total_ingresos' => $resumen['gran_total_ingresos'],
            'deuda_pendiente'     => $deuda_pendiente,
            'observaciones'       => $observaciones
        ];

        // 3. Ejecutamos el insert del corte
        $resultado = $modelo->agregarCorteManual($datosParaGuardar);

        // 4. SI EL CORTE SE GUARDÓ, REGISTRAMOS EL SALDO INICIAL DESGLOSADO EN EL HISTORIAL
        if ($resultado['status'] === 'success') {
            /**
             * Preparamos el desglose dinámico. 
             * Usamos 'gran_total_ingresos' para el efectivo porque ya resta gastos/compras,
             * asegurando que lo que se siembra es lo que REALMENTE quedó en caja.
             */
            $desglose_para_historial = [
                'efectivo'      => floatval($resumen['gran_total_ingresos']), 
                'tarjeta'       => $tarjeta,
                'transferencia' => $transferencia
            ];
            
            // Llamamos a la función actualizada que recibe el array de desgloses
            $modelo->registrarAperturaDesdeCierre(
                $target_save, 
                $usuario_id, 
                $desglose_para_historial, 
                $fecha_corte
            );
        }

        echo json_encode($resultado);

    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
    exit;
}

// --- LÓGICA DE CONSULTA AJAX (GET) ---
if (isset($_GET['ajax'])) {
    header('Content-Type: application/json');
    
    $periodo  = $_GET['periodo'] ?? 'hoy';
    $f_inicio = $_GET['f_inicio'] ?? date('Y-m-d');
    $f_fin    = $_GET['f_fin'] ?? date('Y-m-d');
    $almacen_id_req = isset($_GET['almacen_id']) ? intval($_GET['almacen_id']) : 0;

    // 1. Normalización estricta de fechas para que el modelo no se pierda
    if ($periodo === 'hoy') {
        $f_inicio = $f_fin = date('Y-m-d');
    } elseif ($periodo === 'ayer') {
        $f_inicio = $f_fin = date('Y-m-d', strtotime("-1 day"));
    }

    $target = ($almacen_sesion != 0) ? $almacen_sesion : $almacen_id_req;

    /**
     * 2. LÓGICA DE VISIBILIDAD
     * Solo mostramos el saldo inicial si la consulta es de UN SOLO DÍA.
     * Si f_inicio es igual a f_fin, significa que es Hoy, Ayer o un solo día del calendario.
     */
    $esUnSoloDia = ($f_inicio === $f_fin);

    // 3. Ejecutar consultas principales
    $detalles = $modelo->obtenerVentasDetalladas($periodo, $f_inicio, $f_fin, $target);
    $totales  = $modelo->obtenerSumasCorte($periodo, $f_inicio, $f_fin, $target);
    
    // 4. Saldo Inicial: Solo si es un solo día, llamamos al modelo.
    $saldo_data = null;
    if ($esUnSoloDia) {
        $saldo_data = $modelo->obtenerSaldoInicialMonitor($target, $f_inicio, $f_fin);
    }
    
    echo json_encode([
        'status'         => 'success',
        'detalles'       => $detalles,
        'totales'        => $totales,
        'saldo_inicial'  => $saldo_data,
        'es_lista'       => ($target == 0),
        'mostrar_saldo'  => $esUnSoloDia // Enviamos esta bandera al JS
    ]);
    exit;
}
// --- CARGA INICIAL DE LA VISTA ---
$listaAlmacenes = $almacenModel->getAlmacenes($almacen_sesion); 
$paginaActual = 'Corte de Caja';
require_once __DIR__ . '/../views/corteCaja_view.php';