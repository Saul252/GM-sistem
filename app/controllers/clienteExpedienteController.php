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
        // Usamos floatval y intval para asegurar limpieza de datos
        $v_id = intval($_POST['venta_id'] ?? 0);
        $amt  = floatval($_POST['monto'] ?? 0); // Este es el monto que el usuario tecleó
        $met  = $_POST['metodo_pago'] ?? 'Efectivo'; 
        $u_id = $_SESSION['usuario_id'] ?? 1;
        $fec  = !empty($_POST['fecha_pago']) ? $_POST['fecha_pago'] : date('Y-m-d H:i:s');
        $c_id = intval($_POST['cliente_id'] ?? 0);

        // --- 1. VALIDACIÓN ---
        if ($amt <= 0) throw new Exception("El monto del abono debe ser mayor a 0.");
        
        // Si no viene el cliente, lo buscamos
        if (!$c_id && $v_id > 0) {
            $c_id = $ventasModel->obtenerClientePorVenta($conexion, $v_id);
        }
        if (!$c_id) throw new Exception("No se halló cliente para procesar el abono.");

        // --- 2. LÓGICA DE SALDOS QUIRÚRGICA ---
        
        if ($met === 'Saldo a Favor') {
            /**
             * ESCENARIO A: EL CLIENTE PAGA USANDO SU "AHORRO"
             * 1. Restamos de su bolsa de FAVOR (Monto negativo)
             * 2. Restamos de su bolsa de CONTRA (Monto negativo)
             */
            
            // Restar del "Ahorro" (Bolsa Favor)
            $clientesModel->agregar_saldo_a_favor($c_id, ($amt * -1), $v_id, $fec);
            
            // Restar de la "Deuda" (Bolsa Contra)
            $clientesModel->agregar_saldo_en_contra($c_id, ($amt * -1), $v_id, $fec);

            // Log específico para rastreo
            $clientesModel->abono_saldos_log($c_id, $v_id, $amt, $u_id, 'USO_SALDO_A_FAVOR', $fec);
            error_log("CF_SYSTEM_LOG: Abono con Saldo a Favor. Cliente: $c_id, Monto: $amt");

        } else {
            /**
             * ESCENARIO B: ABONO NORMAL (Efectivo, Transferencia, etc.)
             * Solo restamos de la bolsa de CONTRA (Deuda). El saldo a favor NO se toca.
             */
            
            $clientesModel->agregar_saldo_en_contra($c_id, ($amt * -1), $v_id, $fec);

            // Log del abono según el método (Ej: ABONO_Efectivo)
            $clientesModel->abono_saldos_log($c_id, $v_id, $amt, $u_id, "ABONO_" . str_replace(' ', '_', $met), $fec);
            error_log("CF_SYSTEM_LOG: Abono normal ($met). Cliente: $c_id, Monto: $amt");
        }

        // --- 3. REGISTRO EN HISTORIAL Y ACTUALIZACIÓN DE NOTA ---
        
        // Registrar en la tabla de pagos/abonos de la venta
        if (!$ventasModel->registrarAbono($v_id, $amt, $u_id, $met, $fec)) {
            throw new Exception("Error al registrar el movimiento en el historial de pagos.");
        }

        
        // --- 4. ÉXITO ---
        $conexion->commit();

        echo json_encode([
            'status'   => 'success', 
            'message'  => 'Abono procesado correctamente.',
            'detalles' => [
                'monto'  => number_format($amt, 2),
                'metodo' => $met,
                'cliente' => $c_id
            ]
        ]);

    } catch (Throwable $e) {
        if (isset($conexion)) $conexion->rollback();
        error_log("CF_SYSTEM_LOG: ERROR CRÍTICO EN ABONO: " . $e->getMessage());
        echo json_encode([
            'status'  => 'error',
            'message' => "Error al procesar: " . $e->getMessage()
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