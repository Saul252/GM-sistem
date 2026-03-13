<?php
// 🔧 SESSION_START SIEMPRE PRIMERO
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

ob_start();

require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../models/transmutacionesModel.php';
require_once __DIR__ . '/../models/almacen_model.php';
require_once __DIR__ . '/../models/mermasModel.php'; // Reutilizamos para obtener lotes originales
 require_once __DIR__ . '/../controllers/LayoutController.php';
protegerPagina(); 
// Verificar sesión
if (!isset($_SESSION['id']) && !isset($_SESSION['usuario_id'])) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => '❌ Sesión requerida']);
        exit;
    }
    header('Location: /cfsistem/login.php');
    exit;
}

$transModel = new TransmutacionesModel($conexion);
$almacenModel = new AlmacenModel($conexion);
$mermasModel = new MermasModel($conexion);

$action = $_GET['action'] ?? 'index';

// ============================================
// 🔍 AJAX: OBTENER DESTINOS COMPATIBLES
// ============================================
if ($action === 'obtenerDestinosCompatibles') {
    ob_clean();
    header('Content-Type: application/json');
    $producto_id = intval($_GET['producto_id'] ?? 0);
    $destinos = ($producto_id > 0) ? $transModel->obtenerDestinosCompatibles($producto_id) : [];
    echo json_encode($destinos);
    exit;
}

// ============================================
// 🔍 AJAX: OBTENER LOTES (Para origen y destino)
// ============================================
if ($action === 'obtenerLotes') {
    ob_clean();
    header('Content-Type: application/json');
    $producto_id = intval($_GET['producto_id'] ?? 0);
    $almacen_id = intval($_GET['almacen_id'] ?? 0);
    $lotes = ($producto_id > 0 && $almacen_id > 0) 
        ? $mermasModel->getLotesPorProducto($almacen_id, $producto_id) 
        : [];
    echo json_encode($lotes);
    exit;
}

// ============================================
// 💾 POST: GUARDAR TRANSMUTACIÓN
// ============================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'guardar') {
    ob_clean();
    header('Content-Type: application/json');
    
    try {
        $usuario_id = $_SESSION['id'] ?? $_SESSION['usuario_id'];
        $responsable = $_SESSION['nombre'] ?? 'Usuario #' . $usuario_id;

        $datos = [
            'almacen_id'          => intval($_POST['almacen_id'] ?? 0),
            'producto_origen_id'  => intval($_POST['producto_origen_id'] ?? 0),
            'lote_origen_id'      => intval($_POST['lote_origen_id'] ?? 0),
            'cant_origen'         => floatval($_POST['cantidad_origen'] ?? 0),
            'producto_destino_id' => intval($_POST['producto_destino_id'] ?? 0),
            'lote_destino_id'     => intval($_POST['lote_destino_id'] ?? 0), // 0 = Crear nuevo
            'cant_destino'        => floatval($_POST['cantidad_destino'] ?? 0),
            'observaciones'       => trim($_POST['observaciones'] ?? ''),
            'usuario_id'          => $usuario_id,
            'responsable'         => $responsable
        ];

        // Validaciones de negocio
        if ($datos['almacen_id'] <= 0) throw new Exception("Almacén no válido.");
        if ($datos['cant_origen'] <= 0 || $datos['cant_destino'] <= 0) throw new Exception("Las cantidades deben ser mayores a cero.");
        if ($datos['producto_origen_id'] === $datos['producto_destino_id']) throw new Exception("No se puede transmutar un producto a sí mismo.");

        // Verificar stock de origen en el momento
        $stmt = $conexion->prepare("SELECT cantidad_actual FROM lotes_stock WHERE id = ?");
        $stmt->bind_param("i", $datos['lote_origen_id']);
        $stmt->execute();
        $stock = $stmt->get_result()->fetch_assoc();
        
        if (!$stock || $datos['cant_origen'] > $stock['cantidad_actual']) {
            throw new Exception("Stock insuficiente en el lote de origen.");
        }

        // Ejecutar transmutación
        $resultado = $transModel->registrarTransmutacion($datos);

        echo json_encode($resultado);
        
    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
    exit;
}

// ============================================
// 📄 VISTA PRINCIPAL (GET)
// ============================================
if ($action === 'index') {
    try {
        $almacen_sesion = $_SESSION['almacen_id'] ?? 0;
        $almacenes = $almacenModel->getAlmacenes($almacen_sesion);
        // Nota: Asegúrate de que el archivo exista en esta ruta
        include __DIR__ . '/../views/transmutaciones_view.php';
    } catch (Exception $e) {
        die("Error vista transmutaciones: " . $e->getMessage());
    }
    exit;
}