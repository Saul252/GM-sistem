<?php
// 🔧 SESSION_START SIEMPRE PRIMERO
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

ob_start();

// Blindaje contra errores silenciosos: Si algo falla, lo veremos en el Network tab
error_reporting(E_ALL);
ini_set('display_errors', 0); // No ensuciar el JSON, pero loguear

require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../config/conexion.php';
// Mantenemos tu nombre de archivo con "i"
require_once __DIR__ . '/../models/transmitacionesModel.php';
require_once __DIR__ . '/../models/almacen_model.php';
require_once __DIR__ . '/../models/mermasModel.php'; 
require_once __DIR__ . '/../models/almacen/productosModel.php';
require_once __DIR__ . '/../controllers/LayoutController.php';

protegerPagina(); 

// Verificar sesión de forma estricta
$usuario_id = $_SESSION['id'] ?? $_SESSION['usuario_id'] ?? null;
if (!$usuario_id) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
// 💾 POST: GUARDAR TRANSMUTACIÓN (REVISADO)
// ============================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'guardar') {
    ob_clean();
    header('Content-Type: application/json');
    
    try {
        $responsable = $_SESSION['nombre'] ?? 'Usuario #' . $usuario_id;

        // Captura y limpieza de datos
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

        // LOG DE ENTRADA: Esto es lo que verás en consola
        $debug_info = [
            'peticion_recibida' => date('Y-m-d H:i:s'),
            'parametros_post'   => $_POST,
            'datos_limpios'     => $datos
        ];

        // Si envías "debug=true" desde JS, se detiene aquí y muestra la tabla
        if (($_POST['debug'] ?? '') === 'true') {
            echo json_encode([
                'status' => 'debug',
                'message' => '🔍 Modo Debug: Revisión de datos',
                'detalles' => $debug_info
            ]);
            exit;
        }

        // Validaciones de negocio
        if ($datos['almacen_id'] <= 0) throw new Exception("Error: Almacén no seleccionado.");
        if ($datos['producto_origen_id'] <= 0 || $datos['producto_destino_id'] <= 0) throw new Exception("Error: Productos no válidos.");
        if ($datos['cant_origen'] <= 0) throw new Exception("Error: Cantidad de origen debe ser mayor a 0.");

        // Ejecución en el Modelo
        $resultado = $transModel->registrarTransmutacion($datos);
        
        // Adjuntamos el debug al éxito para que siempre sepas qué se envió
        $resultado['debug_enviado'] = $datos;

        echo json_encode($resultado);
        
    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode([
            'status' => 'error', 
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ]);
    }
    exit;
}

// ============================================
// 🔍 AJAX: OBTENER DESTINOS COMPATIBLES
// ============================================
if ($action === 'obtenerDestinosCompatibles') {
    ob_clean();
    header('Content-Type: application/json');
    $producto_id = intval($_GET['producto_id'] ?? 0);
    echo json_encode($producto_id > 0 ? $transModel->obtenerDestinosCompatibles($producto_id) : []);
    exit;
}

// ============================================
// 🔍 AJAX: OBTENER LOTES
// ============================================
if ($action === 'obtenerLotes') {
    ob_clean();
    header('Content-Type: application/json');
    $producto_id = intval($_GET['producto_id'] ?? 0);
    $almacen_id = intval($_GET['almacen_id'] ?? 0);
    echo json_encode(($producto_id > 0 && $almacen_id > 0) ? $mermasModel->getLotesPorProducto($almacen_id, $producto_id) : []);
    exit;
}

// ============================================
// 💾 POST: GUARDAR EQUIVALENCIA (REGLA)
// ============================================
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
// ============================================
// 🔍 AJAX: OBTENER HISTORIAL (LISTAR)
// ============================================
if ($action === 'listar') {
    ob_clean();
    header('Content-Type: application/json');
    $almacen_id = intval($_GET['almacen_id'] ?? $_SESSION['almacen_id'] ?? 0);
    
    if ($almacen_id > 0) {
        echo json_encode($transModel->listarTransmutaciones($almacen_id));
    } else {
        echo json_encode([]);
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
        $todosLosProductos = $productoModel->getProductos() ?: []; 
        
        // Carga inicial del historial para que la tabla no nazca vacía
        $historial = ($almacen_sesion > 0) ? $transModel->listarTransmutaciones($almacen_sesion) : [];

        include __DIR__ . '/../views/transmutaciones.php';
    } catch (Exception $e) {
        die("Error vista transmutaciones: " . $e->getMessage());
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
        $todosLosProductos = $productoModel->getProductos() ?: []; 
        include __DIR__ . '/../views/transmutaciones.php';
    } catch (Exception $e) {
        die("Error vista transmutaciones: " . $e->getMessage());
    }
    exit;
}