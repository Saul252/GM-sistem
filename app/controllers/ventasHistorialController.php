<?php
/**
 * ventasHistorialController.php
 * Controlador para la gestión de Entregas y Abonos (Historial de Ventas)
 */

require_once __DIR__ . '/../../includes/auth.php';
 // Tu función de seguridad
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../controllers/LayoutController.php';
require_once __DIR__ . '/../models/ventasHistorialModel.php';
require_once __DIR__ . '/../models/ventas_model.php';
require_once __DIR__ . '/../models/clientesModel.php';

protegerPagina('ventashistorial');
$ventasModel = new VentaHistorialModel($conexion);
$clientesModel = new ClientesModel($conexion);

$paginaActual = 'ventashistorial';

// --- ACCIÓN: LISTADO AJAX (Con filtros) ---
if (isset($_GET['action']) && $_GET['action'] === 'listar') {
    if (ob_get_level()) ob_clean(); 
    header('Content-Type: application/json');
    
    try {
        $filtros = [
            'search'   => $_GET['f_search'] ?? '',
            'status'   => $_GET['f_status'] ?? '',
            'pago'     => $_GET['f_pago'] ?? '',
            'rango'    => $_GET['f_rango'] ?? 'todos',
            'inicio'   => $_GET['f_inicio'] ?? '',
            'fin'      => $_GET['f_fin'] ?? '',
            'almacen'  => $_GET['f_almacen'] ?? 0
        ];

        $rol_id = $_SESSION['rol_id'] ?? 2;
        $id_almacen_usuario = $_SESSION['almacen_id'] ?? 0;

        $data = $ventasModel->obtenerVentasFiltradas($filtros, $rol_id, $id_almacen_usuario);
        echo json_encode($data);

    } catch (Throwable $e) {
        echo json_encode(['error' => true, 'message' => $e->getMessage()]);
    }
    exit;
}

if (isset($_GET['action']) && $_GET['action'] === 'guardarEntrega') {
    // Limpiamos cualquier salida previa para que solo salga el JSON
    if (ob_get_level()) ob_clean();
    header('Content-Type: application/json');

    try {
        if (empty($_POST['venta_id'])) throw new Exception("ID de venta no recibido.");
        
        $venta_id = intval($_POST['venta_id']);
        $productos = $_POST['productos'] ?? [];
        $usuario_id = $_SESSION['usuario_id'] ?? 1;

        $resultado = $ventasModel->procesarEntrega($venta_id, $productos, $usuario_id);
        
        echo json_encode(['status' => 'success', 'message' => 'Entrega procesada correctamente']);

    } catch (Exception $e) {
        // Importante: Mandar el mensaje real de la excepción (ej. "Stock insuficiente...")
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    } catch (Throwable $t) {
        echo json_encode(['status' => 'error', 'message' => 'Error crítico en el servidor']);
    }
    exit;
}

/// --- ACCIÓN: GUARDAR ABONO ---
if (isset($_GET['action']) && $_GET['action'] === 'guardarAbono') {
    if (ob_get_level()) ob_clean();
    header('Content-Type: application/json');

    try {
        // --- CAPTURA INICIAL ---
        $v_id = intval($_POST['venta_id']);
        $amt  = floatval($_POST['monto']);
        $met  = $_POST['metodo_pago'] ?? 'Efectivo'; 
        $u_id = $_SESSION['usuario_id'] ?? $_SESSION['id_usuario'] ?? 1;
        $fec  = !empty($_POST['fecha_pago']) ? $_POST['fecha_pago'] : date('Y-m-d H:i:s');

        error_log("--- INICIO DE PROCESO DE ABONO --- Venta: $v_id");

        // --- PASO 0: OBTENER EL CLIENTE REAL (Tu nueva función) ---
        // Pasamos $this->db como la conexión que requiere tu función
        $c_id = $ventasModel->obtenerClientePorVenta($conexion, $v_id);

        if (!$c_id) {
            error_log("ERROR CRÍTICO: No se encontró un cliente para la venta $v_id en la base de datos.");
            throw new Exception("La venta #$v_id no tiene un cliente válido asociado.");
        }
        error_log("CLIENTE DETECTADO: ID $c_id (Validado desde DB)");

        // --- PASO 1: REGISTRAR EN CAJA (Tu función original) ---
        $resOriginal = $ventasModel->registrarAbono($v_id, $amt, $u_id, $met, $fec);
        
        if ($resOriginal) {
            error_log("PASO 1 EXITOSO: Guardado en historial_pagos.");

            // --- PASO 2: LOG DE SALDOS (La función abono_saldos_log) ---
            $resLog = $clientesModel->abono_saldos_log($c_id, $v_id, $amt, $u_id, $met, $fec);

            if ($resLog) {
                error_log("PASO 2 EXITOSO: Guardado en clientes_saldos_log.");

                // --- PASO 3: ACTUALIZAR SALDO MAESTRO (La función abono_saldos) ---
                $resSaldos = $clientesModel->abono_saldos($c_id, $amt, $v_id, $fec);

                if ($resSaldos) {
                    error_log("PASO 3 EXITOSO: Saldo restado en clientes_saldos. PROCESO FINALIZADO.");
                    echo json_encode([
                        'status' => 'success', 
                        'message' => '¡Abono registrado y saldo actualizado correctamente!'
                    ]);
                } else {
                    error_log("ERROR PASO 3: Falló abono_saldos (Maestra).");
                    echo json_encode(['status' => 'warning', 'message' => 'Pago guardado, pero falló la actualización del saldo neto.']);
                }
            } else {
                error_log("ERROR PASO 2: Falló abono_saldos_log.");
                echo json_encode(['status' => 'warning', 'message' => 'Pago en caja OK, pero falló el registro en el historial de saldos.']);
            }
        } else {
            error_log("ERROR PASO 1: Falló registrarAbono en ventasModel.");
            echo json_encode(['status' => 'error', 'message' => 'No se pudo registrar el pago en caja.']);
        }

    } catch (Throwable $e) {
        error_log("FALLO GENERAL EN GUARDAR ABONO: " . $e->getMessage());
        echo json_encode([
            'status' => 'error', 
            'message' => 'Error de sistema: ' . $e->getMessage()
        ]);
    }
    exit;
}
// --- ACCIÓN: OBTENER DETALLE ---
if (isset($_GET['action']) && $_GET['action'] === 'obtenerDetalle') {
    if (ob_get_level()) ob_clean();
    header('Content-Type: application/json');

    try {
        $id = intval($_GET['id'] ?? 0);
        $detalle = $ventasModel->obtenerDetalleCompleto($id);
        echo json_encode($detalle);
    } catch (Throwable $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
    exit;
}
// --- ACCIÓN: CANCELAR VENTA (POST) ---

// --- ACCIÓN: CANCELAR VENTA (POST) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action']) && $_GET['action'] === 'cancelarVentaSinSaldo') {
    if (ob_get_level()) ob_clean();
    header('Content-Type: application/json');

    try {
        // Leemos el cuerpo de la petición (JSON)
        $input = json_decode(file_get_contents("php://input"), true);
        
        $venta_id   = intval($input['id_venta'] ?? 0);
        $motivo     = trim($input['motivo'] ?? 'Cancelación desde historial');
        $usuario_id = $_SESSION['usuario_id'] ?? 1;

        if ($venta_id <= 0) {
            throw new Exception("ID de venta no proporcionado o inválido.");
        }

        // Ejecutamos la lógica en el modelo
        $resultado = VentasModel::cancelarVenta($conexion, $venta_id, $usuario_id, $motivo);
        
        echo json_encode($resultado);

    } catch (Throwable $e) {
        echo json_encode([
            'status'  => 'error', 
            'message' => $e->getMessage()
        ]);
    }
    exit;
}

// --- ACCIÓN: CANCELAR VENTA (POST) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action']) && $_GET['action'] === 'cancelarVenta') {
    if (ob_get_level()) ob_clean();
    header('Content-Type: application/json');

    try {
        $input = json_decode(file_get_contents("php://input"), true);
        $venta_id   = intval($input['id_venta'] ?? 0);
        $motivo     = trim($input['motivo'] ?? 'Cancelación de venta');
        $usuario_id = $_SESSION['usuario_id'] ?? 1;
        $fecha_act  = date('Y-m-d H:i:s');

        if ($venta_id <= 0) throw new Exception("ID de venta no válido.");

        // --- PASO 1: OBTENER DETALLE COMPLETO ---
        // Aprovechamos tu función que ya suma los pagos automáticamente
        $detalle = $ventasModel->obtenerDetalleCompleto($venta_id);
        
        if (!$detalle || empty($detalle['info'])) {
            throw new Exception("No se encontró la información de la venta #$venta_id");
        }

        $infoVenta    = $detalle['info'];
        $cliente_id   = intval($infoVenta['id_cliente']);
        $total_pagado = floatval($infoVenta['total_pagado'] ?? 0);

        error_log("Procesando cancelación - Venta: $venta_id, Cliente: $cliente_id, Monto a devolver: $total_pagado");

        // --- PASO 2: ABONAR AL SALDO (Si la venta tuvo pagos) ---
        // Si el cliente ya había dado dinero, se lo regresamos como saldo a favor
        if ($total_pagado > 0) {
            
            // A) Registrar en el Log de Saldos para auditoría
            $obs_log = "Saldo recuperado por cancelación de Venta #$venta_id. Motivo: $motivo";
            $clientesModel->abono_saldos_log(
                $cliente_id, 
                $venta_id, 
                $total_pagado, 
                $usuario_id, 
                'AJUSTE', // Lo marcamos como ajuste/devolución
                $fecha_act
            );
            
            // B) Impactar tabla maestra (clientes_saldos)
            // Tu función abono_saldos restará este monto del 'saldo_en_contra'
            $clientesModel->abono_saldosAFavor(
                $cliente_id, 
                $total_pagado, 
                $venta_id, 
                $fecha_act
            );
        }

        // --- PASO 3: CANCELAR LA VENTA ---
        // Cambiamos el estado a 'cancelada' en la tabla ventas
        $resultado = VentasModel::cancelarVenta($conexion, $venta_id, $usuario_id, $motivo);

        // Agregamos el monto devuelto a la respuesta para que el Front-end avise al usuario
        if ($resultado['status'] === 'success') {
            $resultado['monto_devuelto'] = $total_pagado;
        }

        echo json_encode($resultado);

    } catch (Throwable $e) {
        error_log("Error en cancelación de venta: " . $e->getMessage());
        echo json_encode([
            'status'  => 'error', 
            'message' => 'Error al cancelar: ' . $e->getMessage()
        ]);
    }
    exit;
}
// --- CARGA DE VISTA ---
if ($_SERVER['REQUEST_METHOD'] === 'GET' && !isset($_GET['action'])) {
    $tituloPagina = "Control de Entregas";
    require_once __DIR__ . '/../views/ventasHistorial_view.php';
}