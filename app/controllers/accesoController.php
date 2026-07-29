<?php
/**
 * ventasHistorialController.php
 * Controlador para la gestión de Entregas y Abonos (Historial de Ventas)
 */

require_once __DIR__ . '/../../includes/auth.php'; // Tu función de seguridad
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../controllers/LayoutController.php';
require_once __DIR__ . '/../models/ventasHistorialModel.php';
require_once __DIR__ . '/../models/ventas_model.php';
require_once __DIR__ . '/../models/clientesModel.php';
require_once __DIR__ . '/../models/RepartosModel.php';
require_once __DIR__ . '/../models/usuariosModel.php';
require_once __DIR__ . '/../models/almacen_model.php'; 
require_once __DIR__ . '/../models/entregasModel.php'; 

require_once __DIR__ . '/../models/almacen/productosModel.php';
// Instancias de Modelos
$sendModelo    = new EntregaModel($conexion);
$almacenModel  = new AlmacenModel($conexion);
$modelo        = new UsuarioModel($conexion);
$ventasModel   = new VentaHistorialModel($conexion);
$clientesModel = new ClientesModel($conexion);
$repartosModel = new RepartoModel($conexion); // <-- Nombre correcto e inicializado

$productosModel = new ProductoModel($conexion);

// ==========================================
// ACCIÓN: Obtener Usuarios
// ==========================================
if (isset($_GET['action']) && $_GET['action'] === 'obtenerUsuarios') {
    if (ob_get_level()) ob_clean();
    header('Content-Type: application/json');
    
    try {
        $rol = $_SESSION['rol_id'] ?? 0;
        $id = intval($_SESSION['usuario_id'] ?? 0);
        
        if ($rol < 4) {
            $usuarios = $modelo->listarUsuarios(0);
        } else {
            $usuarios = $modelo->listarUsuarios($id);
        }
        
        if ($usuarios) {
            echo json_encode(['success' => true, 'data' => $usuarios]);
        } else {
            throw new Exception('Usuarios no encontrados.');
        }
    } catch (Throwable $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}

// ==========================================
// ACCIÓN: Obtener IDs Pendientes Venta
// ==========================================
if (isset($_GET['action']) && $_GET['action'] === 'get_ids_pendientes_venta') {
    if (ob_get_level()) ob_clean();
    header('Content-Type: application/json');
    
    try {
        $venta_id = intval($_GET['venta_id'] ?? 0);
        if ($venta_id <= 0) {
            throw new Exception('ID de venta no válido.');
        }
        
        // CORREGIDO: Se cambió $repartoM por $repartosModel
        $ids = $repartosModel->listarIdsPendientesPorVenta($venta_id);
        
        echo json_encode(['success' => true, 'ids' => $ids ?? []]);
    } catch (Throwable $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}

// ==========================================
// ACCIÓN: Obtener ID Almacén
// ==========================================
if (isset($_GET['action']) && $_GET['action'] === 'obtener_id_almacen') {
    if (ob_get_level()) ob_clean();
    header('Content-Type: application/json');
    
    try {
        $id = intval($_GET['id'] ?? 0);
        if ($id <= 0) {
            throw new Exception('ID no válido.');
        }
        
        // OJO: Verifica si en tu EntregaModel el método se escribe "obtener_almecen_id" o "obtener_almacen_id"
        $almacen = $sendModelo->obtener_almecen_id($id); 
        
        echo json_encode([
            "success" => true,
            "almacen" => $almacen
        ]);
    } catch (Throwable $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}
if (isset($_GET['action']) && $_GET['action'] === 'obtenerProductos') {
    header('Content-Type: application/json');

    $productos = $productosModel->obtenerTodosProductos(0);
    $medidasAdicionales = $productosModel->obtenerMedidas();

    $medidasPorProducto = [];

    foreach ($medidasAdicionales as $medida) {
        $producto_id = $medida['producto_id'];

        if (!isset($medidasPorProducto[$producto_id])) {
            $medidasPorProducto[$producto_id] = [];
        }

        $medidasPorProducto[$producto_id][] = $medida;
    }

    foreach ($productos as &$producto) {

        // AQUÍ
        $idProducto = $producto['producto_id'];

        $producto['medidas_adicionales'] =
            $medidasPorProducto[$idProducto] ?? [];
    }

    unset($producto);

    echo json_encode([
        'success' => true,
        'data' => $productos
    ]);

    exit;
}
if (isset($_GET['action']) && $_GET['action'] === 'obtenerProductosAlmacen') {
    header('Content-Type: application/json');
$almacen_usuario = !empty($_GET['id']) 
        ? intval($_GET['id']) 
        : (int)($_SESSION['almacen_id'] ?? 0);
    $productos = $productosModel->obtenerTodosProductosAlmacen($almacen_usuario);
    $medidasAdicionales = $productosModel->obtenerMedidas();

    $medidasPorProducto = [];

    foreach ($medidasAdicionales as $medida) {
        $producto_id = $medida['producto_id'];

        if (!isset($medidasPorProducto[$producto_id])) {
            $medidasPorProducto[$producto_id] = [];
        }

        $medidasPorProducto[$producto_id][] = $medida;
    }

    foreach ($productos as &$producto) {

        // AQUÍ
        $idProducto = $producto['producto_id'];

        $producto['medidas_adicionales'] =
            $medidasPorProducto[$idProducto] ?? [];
    }

    unset($producto);

    echo json_encode([
        'success' => true,
        'data' => $productos
    ]);

    exit;
}