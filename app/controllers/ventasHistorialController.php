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
protegerPagina('ventashistorial');
$ventasModel = new VentaHistorialModel($conexion);

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
        $venta_id   = intval($_POST['venta_id']);
        $monto      = floatval($_POST['monto']);
        $metodo     = $_POST['metodo_pago'] ?? 'Efectivo'; 
        $usuario_id = $_SESSION['usuario_id'] ?? $_SESSION['id_usuario'] ?? 1;

        // --- CAPTURA DE FECHA ---
        // Si el JS mandó 'fecha_pago', la usamos. Si no, usamos la del servidor.
        $fecha_pago = !empty($_POST['fecha_pago']) ? $_POST['fecha_pago'] : date('Y-m-d H:i:s');

        // IMPORTANTE: Asegúrate de que tu modelo reciba este 5to parámetro ($fecha_pago)
        $resultado = $ventasModel->registrarAbono($venta_id, $monto, $usuario_id, $metodo, $fecha_pago);
        
        if ($resultado) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'No se pudo insertar el registro del abono']);
        }

    } catch (Throwable $e) {
        // Logueamos el error interno para depuración
        error_log("Error en guardarAbono: " . $e->getMessage());
        echo json_encode(['status' => 'error', 'message' => 'Ocurrió un error: ' . $e->getMessage()]);
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
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action']) && $_GET['action'] === 'cancelarVenta') {
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

// --- CARGA DE VISTA ---
if ($_SERVER['REQUEST_METHOD'] === 'GET' && !isset($_GET['action'])) {
    $tituloPagina = "Control de Entregas";
    require_once __DIR__ . '/../views/ventasHistorial_view.php';
}