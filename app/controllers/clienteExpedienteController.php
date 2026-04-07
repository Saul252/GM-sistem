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
        // --- 0. CAPTURA DE DATOS (Lo que escribiste en el input del modal) ---
        $v_id = intval($_POST['venta_id'] ?? 0);
        $amt  = floatval($_POST['monto'] ?? 0); // Este es el monto que quieres usar
        $met  = $_POST['metodo_pago'] ?? 'Efectivo'; 
        $u_id = $_SESSION['usuario_id'] ?? $_SESSION['id_usuario'] ?? 1;
        $fec  = !empty($_POST['fecha_pago']) ? $_POST['fecha_pago'] : date('Y-m-d H:i:s');
        $c_id = intval($_POST['cliente_id'] ?? 0);

        // --- 1. VALIDACIÓN DEL CLIENTE ---
        if (!$c_id) {
            $c_id = $ventasModel->obtenerClientePorVenta($conexion, $v_id);
        }
        if (!$c_id) throw new Exception("No se halló cliente para la venta #$v_id");

        // --- 2. LÓGICA DE AFECTACIÓN DE SALDOS (COMO EN VENTAS) ---
        // Solo si el método es "Saldo a Favor" y hay un monto válido
        if ($met === 'Saldo a Favor' && $amt > 0) {
            
            // Usamos el monto que escribiste en el input como ajuste negativo
            $ajuste_negativo = $amt * -1;
            
            // Afectamos la tabla maestra de saldos (Bolsa de Favor vs Contra)
            $clientesModel->abono_saldosAFavor($c_id, $ajuste_negativo, $v_id, $fec);
            
            // Registramos el log de uso específico (USO_SALDO_A_FAVOR)
            $clientesModel->abono_saldos_log($c_id, $v_id, $amt, $u_id, 'USO_SALDO_A_FAVOR', $fec);
            
        } else {
            // Si es Efectivo, Transferencia, etc., solo registramos el log del abono normal
            $clientesModel->abono_saldos_log($c_id, $v_id, $amt, $u_id, "ABONO_$met", $fec);
        }

        // --- 3. REGISTRO EN HISTORIAL DE PAGOS (CAJA DE LA VENTA) ---
        // Esto asienta que la venta recibió el dinero por el método seleccionado
        if (!$ventasModel->registrarAbono($v_id, $amt, $u_id, $met, $fec)) {
            throw new Exception("Error al registrar abono en historial.");
        }

        // --- 4. ACTUALIZAR SALDO EN CONTRA DE LA VENTA ---
        // Esta función resta el monto ($amt) de la deuda de esta factura específica
        $resFinal = $ventasModel->actualizarSaldosMaestros($c_id, $v_id, $amt, $fec);
        if (!$resFinal) {
            throw new Exception("Error al actualizar deuda de la venta.");
        }

        // --- 5. ÉXITO ---
        $conexion->commit();

        echo json_encode([
            'status' => 'success', 
            'message' => 'Abono procesado correctamente.',
            'detalles' => [
                'monto' => number_format($amt, 2),
                'metodo' => $met
            ]
        ]);

    } catch (Throwable $e) {
        if (isset($conexion)) $conexion->rollback();
        error_log("CF_SYSTEM_LOG: ERROR CRÍTICO EN ABONO: " . $e->getMessage());
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
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