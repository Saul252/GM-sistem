<?php
// 🔧 SESSION_START SIEMPRE PRIMERO
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

ob_start();

// Blindaje contra errores: Loguear todo pero no mostrar en pantalla para no romper el JSON
error_reporting(E_ALL);
ini_set('display_errors', 0); 

require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../models/transmutacionesModel.php';
require_once __DIR__ . '/../models/almacen_model.php';
require_once __DIR__ . '/../models/mermasModel.php'; 
require_once __DIR__ . '/../models/almacen/productosModel.php';
require_once __DIR__ . '/../controllers/LayoutController.php';

protegerPagina('transmutaciones'); 
$paginaActual='transmutaciones';
$usuario_id = $_SESSION['id'] ?? $_SESSION['usuario_id'] ?? null;
if (!$usuario_id) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' || isset($_GET['action'])) {
        ob_clean();
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => '❌ Sesión expirada']);
        exit;
    }
    header('Location: /cfsistem/login.php');
    exit;
}

$transModel = new TransmutacionesModel($conexion);
$almacenModel = new AlmacenModel($conexion);
$mermasModel = new MermasModel($conexion);
$productoModel = new ProductoModel($conexion);
        
$action = $_GET['action'] ?? 'index';

// ============================================
// 💾 POST: GUARDAR TRANSMUTACIÓN
// ============================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'guardar') {
    ob_clean();
    header('Content-Type: application/json');
    
    try {
        $responsable = $_SESSION['nombre'] ?? 'Usuario #' . $usuario_id;

        $datos = [
            'almacen_id'          => intval($_POST['almacen_id'] ?? 0),
            'producto_origen_id'  => intval($_POST['producto_origen_id'] ?? 0),
            'lote_origen_id'      => intval($_POST['lote_origen_id'] ?? 0),
            'cant_origen'         => floatval($_POST['cantidad_origen'] ?? 0),
            'producto_destino_id' => intval($_POST['producto_destino_id'] ?? 0),
            'lote_destino_id'     => intval($_POST['lote_destino_id'] ?? 0),
            'cant_destino'        => floatval($_POST['cantidad_destino'] ?? 0),
            'observaciones'       => trim($_POST['observaciones'] ?? ''),
            'usuario_id'          => $usuario_id,
            'responsable'         => $responsable
        ];

        if ($datos['almacen_id'] <= 0) throw new Exception("Error: Almacén no seleccionado.");
        if ($datos['producto_origen_id'] <= 0 || $datos['producto_destino_id'] <= 0) throw new Exception("Error: Productos no válidos.");
        if ($datos['cant_origen'] <= 0) throw new Exception("Error: Cantidad de origen insuficiente.");

        $resultado = $transModel->registrarTransmutacion($datos);
        echo json_encode($resultado);
        
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
    exit;
}

// ============================================
// 🔍 AJAX: LISTAR HISTORIAL (Para DataTables)
// ============================================
if ($action === 'listar') {
    ob_clean();
    header('Content-Type: application/json');
    
    // Prioridad: 1. El ID que venga por GET, 2. El de la SESIÓN
    $almacen_id = intval($_GET['almacen_id'] ?? $_SESSION['almacen_id'] ?? 0);
    
    $data = ($almacen_id > 0) ? $transModel->listarTransmutaciones($almacen_id) : [];
    
    // Importante: DataTables espera un array de objetos o un objeto con la propiedad "data"
    echo json_encode($data);
    exit;
}

// ============================================
// 🔍 AJAX: OBTENER LOTES / DESTINOS
// ============================================
if ($action === 'obtenerLotes') {
    ob_clean();
    header('Content-Type: application/json');
    $producto_id = intval($_GET['producto_id'] ?? 0);
    $almacen_id = intval($_GET['almacen_id'] ?? 0);
    echo json_encode(($producto_id > 0 && $almacen_id > 0) ? $mermasModel->getLotesPorProducto($almacen_id, $producto_id) : []);
    exit;
}

if ($action === 'obtenerDestinosCompatibles') {
    ob_clean();
    header('Content-Type: application/json');
    $producto_id = intval($_GET['producto_id'] ?? 0);
    echo json_encode($producto_id > 0 ? $transModel->obtenerDestinosCompatibles($producto_id) : []);
    exit;
}

// ============================================
// 🔍 AJAX: LISTAR HISTORIAL (Para DataTables)
// ============================================
if ($action === 'listar') {
    ob_clean();
    header('Content-Type: application/json');
    
    // Si viene por GET (filtro manual) usamos ese, sino el de la sesión
    $almacen_id = isset($_GET['almacen_id']) ? intval($_GET['almacen_id']) : intval($_SESSION['almacen_id'] ?? 0);
    
    // Llamamos al modelo. El modelo ya sabe que si recibe 0, trae todo.
    $data = $transModel->listarTransmutaciones($almacen_id);
    
    echo json_encode($data);
    exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'guardarEquivalencia') {
    ob_clean();
    header('Content-Type: application/json');
    try {
        $almacen_sesion = intval($_SESSION['almacen_id'] ?? 0);
        $almacen_id = ($almacen_sesion === 0) ? intval($_POST['almacen_id'] ?? 0) : $almacen_sesion;

        if ($almacen_id === 0) throw new Exception("Almacén obligatorio.");

        $p_origen  = intval($_POST['p_origen'] ?? 0);
        $p_destino = intval($_POST['p_destino'] ?? 0);
        $factor    = floatval($_POST['factor'] ?? 0);
        $notas     = trim($_POST['notas'] ?? '');

        $res = $transModel->agregarConfiguracion($almacen_id, $p_origen, $p_destino, $factor, $usuario_id, $notas);
        echo json_encode(['status' => 'success', 'message' => '✅ Regla guardada correctamente']);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
    exit;
}
// ... (otros bloques) ...

// ============================================
// 📄 VISTA PRINCIPAL
// ============================================
if ($action === 'index') {
    try {
        // Obtenemos el almacén de la sesión (0 para admin)
        $almacen_sesion = intval($_SESSION['almacen_id'] ?? 0);
        
        // El admin ve todos los almacenes en los select, el usuario solo el suyo
        $almacenes = $almacenModel->getAlmacenes($almacen_sesion);
        $todosLosProductos = $productoModel->getProductos() ?: []; 
        
        // CARGA INICIAL:
        // Pasamos el ID de la sesión. Si es 0, el modelo traerá todo el historial global.
        $historial = $transModel->listarTransmutaciones($almacen_sesion);
   $almacen_param = (int)($_SESSION['almacen_id'] ?? 0);
$listaConversiones = $transModel->listarConfiguraciones($almacen_param);

        include __DIR__ . '/../views/transmutaciones.php';
    } catch (Exception $e) {
        die("Error en el controlador: " . $e->getMessage());
    }
    exit;
}