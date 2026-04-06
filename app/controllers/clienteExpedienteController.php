<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../models/clientesEstatusModel.php';
require_once __DIR__ . '/../models/clientesModel.php';
// Asegúrate de requerir los modelos necesarios para la lógica de abono
require_once __DIR__ . '/../models/ventasHistorialModel.php'; 

$model = new ClientesEstatusModel($conexion);
$clientesModel=new clientesModel($conexion);
$ventasModel = new VentaHistorialModel($conexion); // Instancia para manejar la lógica de caja

$id_cliente = intval($_GET['id'] ?? $_POST['id_cliente'] ?? 0);
// --- RUTAS AJAX ---
if (isset($_GET['action'])) {
    if (ob_get_level()) ob_clean(); // Limpiar basura de salida
    header('Content-Type: application/json');
    
    try {
        switch ($_GET['action']) {
          
       case 'guardarAbono':
    if (ob_get_level()) ob_clean();
    header('Content-Type: application/json');

    $conexion->begin_transaction();

    try {
        // --- 0. CAPTURA DE DATOS ---
        $v_id = intval($_POST['venta_id'] ?? 0);
        $amt  = floatval($_POST['monto'] ?? 0);
        $met  = $_POST['metodo_pago'] ?? 'Efectivo'; 
        $u_id = $_SESSION['usuario_id'] ?? $_SESSION['id_usuario'] ?? 1;
        $fec  = !empty($_POST['fecha_pago']) ? $_POST['fecha_pago'] : date('Y-m-d H:i:s');

        // --- 1. CLIENTE ---
        $c_id = $ventasModel->obtenerClientePorVenta($conexion, $v_id);
        if (!$c_id) throw new Exception("Paso 1: No se halló cliente para la venta #$v_id");

        // --- 2. CAJA (HISTORIAL) ---
        if (!$ventasModel->registrarAbono($v_id, $amt, $u_id, $met, $fec)) {
            throw new Exception("Paso 2: Falló registrarAbono en la tabla historial_pagos.");
        }

        // --- 3. LOG DE SALDOS (EL QUE TE FALLA) ---
        // Envolvemos solo esta llamada para capturar el error exacto
        try {
            $resLog = $clientesModel->abono_saldos_log($c_id, $v_id, $amt, $u_id, $met, $fec);
            if (!$resLog) {
                // Si la función devuelve false pero no lanza excepción, forzamos el error
                throw new Exception("La función abono_saldos_log devolvió FALSE.");
            }
        } catch (Throwable $e_log) {
            // Aquí atrapamos si hay error de SQL, de parámetros o de conexión en esa función
            throw new Exception("Paso 3 Detenido: " . $e_log->getMessage());
        }

        // --- 4. BOLSAS MAESTRAS ---
        $resFinal = $ventasModel->actualizarSaldosMaestros($c_id, $v_id, $amt, $fec);
        if (!$resFinal) {
            throw new Exception("Paso 4: Error en la lógica de bolsas (actualizarSaldosMaestros).");
        }

        // --- 5. ÉXITO ---
        $conexion->commit();

        echo json_encode([
            'status' => 'success', 
            'message' => 'Abono procesado correctamente.',
            'detalles' => [
                'deuda' => number_format($resFinal['nuevo_contra'], 2),
                'favor' => number_format($resFinal['nuevo_favor'], 2)
            ]
        ]);

    } catch (Throwable $e) {
        if (isset($conexion)) $conexion->rollback();
        
        error_log("DETALLE ERROR ABONO: " . $e->getMessage());
        
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage() // Aquí SweetAlert te dirá "Paso 3 Detenido: ..."
        ]);
    }
    exit;
    break;     
        }
    } catch (Exception $e) {
        error_log("Error AJAX CF System: " . $e->getMessage());
        echo json_encode(['status' => 'error', 'success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}

// --- CARGA DE VISTA ---
$cliente = $model->obtenerDatosBasicos($id_cliente);
$estatusCliente=$clientesModel->obtenerEstatus($conexion,$id_cliente);
if (!$cliente) die("Cliente no encontrado.");

$expediente = $model->obtenerExpedienteCompleto($id_cliente);
$resumen = [
    'total_comprado' => array_sum(array_column($expediente, 'total')),
    'total_pagado'   => array_sum(array_column($expediente, 'total_pagado')),
];

// Cálculo del saldo total (para que la vista sepa si hay saldo a favor global)
$resumen['saldo_total'] = $resumen['total_comprado'] - $resumen['total_pagado'];

require_once __DIR__ . '/../views/clienteEstatus/expedienteDetalle_view.php';