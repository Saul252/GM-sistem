<?php
// 1. Reporte de errores para debug (quitar en producción)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../controllers/LayoutController.php';

// 2. Carga de Modelos
require_once __DIR__ . '/../models/solicitudCompraModel.php'; 
require_once __DIR__ . '/../models/productosModel.php';
require_once __DIR__ . '/../models/proveedoresModel.php';
require_once __DIR__ . '/../models/almacen_model.php'; 

protegerPagina('solicitudesCompra'); 

$solicitudModel = new SolicitudCompra($conexion);
$productosModel = new ProductosModel($conexion);
$almacenModel   = new AlmacenModel($conexion);
$proveedorModel = new ProveedoresModel($conexion);

$paginaActual = 'solicitudesCompra'; 
$almacen_usuario = $_SESSION['almacen_id'] ?? 0;
$es_admin = ($_SESSION['rol_id'] == 1 || $almacen_usuario == 0);

// --- ACCIÓN: GUARDAR (AJAX) ---
if (isset($_GET['action']) && $_GET['action'] === 'guardar') {
    // IMPORTANTE: Limpiar cualquier salida previa para no corromper el JSON
    if (ob_get_level()) ob_clean(); 
    header('Content-Type: application/json; charset=utf-8');
    
    try {
        // Validación de Almacén
        $almacen_id = $es_admin ? intval($_POST['almacen_id'] ?? 0) : intval($almacen_usuario);
        
        if ($almacen_id <= 0) throw new Exception("ID de almacén no válido.");

        $data = [
            'usuario_id'   => intval($_SESSION['usuario_id']),
            'almacen_id'   => $almacen_id,
            'proveedor_id' => intval($_POST['proveedor_id'] ?? 0)
        ];

        if ($data['proveedor_id'] <= 0) throw new Exception("Debe seleccionar un proveedor.");

        // Procesar items
        $items_post = $_POST['items'] ?? [];
        $items_procesados = [];

        foreach ($items_post as $id_producto => $campos) {
            $cant = floatval($campos['cant'] ?? 0);
            $factor = floatval($campos['unidad'] ?? 1); 
            $total_base = $cant * $factor;

            if ($total_base > 0) {
                $items_procesados[intval($id_producto)] = $total_base;
            }
        }

        if (empty($items_procesados)) throw new Exception("No hay productos válidos en la lista.");

        // LLAMADA AL MODELO
        $resultado = $solicitudModel->crear($data, $items_procesados);
        
        if ($resultado === true) {
            echo json_encode(['status' => 'success', 'message' => '¡Solicitud #'.($conexion->insert_id ?? '').' guardada con éxito!']);
        } else {
            // Si el modelo falló internamente
            throw new Exception($resultado ?: "Error en la base de datos al insertar.");
        }
    } catch (Throwable $e) {
        http_response_code(400); // Opcional: marca error HTTP
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
    exit;
}

// --- ACCIÓN: ELIMINAR --- (Sin cambios, está correcta)
if (isset($_GET['action']) && $_GET['action'] === 'eliminar') {
    if (ob_get_level()) ob_clean();
    header('Content-Type: application/json');
    try {
        $id = intval($_POST['id'] ?? 0);
        if ($id <= 0) throw new Exception("ID no válido.");
        if ($solicitudModel->eliminar($id)) {
            echo json_encode(['status' => 'success', 'message' => 'Eliminado.']);
        } else {
            throw new Exception("Error al eliminar.");
        }
    } catch (Throwable $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
    exit;
}

// --- CARGA DE VISTA ---
if ($_SERVER['REQUEST_METHOD'] === 'GET' && !isset($_GET['action'])) {
    try {
        $solicitudes = $solicitudModel->listar($es_admin, $almacen_usuario);
        // Nota: Verifica que sea listarTodo() o listarTodos() según tu ProductosModel
        $productos   = $productosModel->listarTodo(); 
        $proveedores = $proveedorModel->listarTodos();
        $almacenes   = $almacenModel->getAlmacenes(); 

        $tituloPagina = "Solicitudes de Compra";
        require_once __DIR__ . '/../views/solicitudesCompra_view.php';
        
    } catch (Exception $e) {
        die("Error fatal: " . $e->getMessage());
    }
}
// --- ACCIÓN: OBTENER DETALLE JSON (AJAX) ---
if (isset($_GET['action']) && $_GET['action'] === 'getDetalleJSON') {
    // 1. Forzamos la limpieza del búfer para evitar espacios en blanco
    if (ob_get_level()) ob_end_clean(); 
    
    header('Content-Type: application/json; charset=utf-8');
    
    try {
        $id = intval($_GET['id'] ?? 0);
        if ($id <= 0) throw new Exception("ID de solicitud no válido.");

        $detalle = $solicitudModel->obtenerDetalle($id);
        
        // 2. Si es null o false, devolvemos array vacío
        $respuesta = ($detalle !== false) ? $detalle : [];
        
        echo json_encode($respuesta);
    } catch (Throwable $e) {
        // Enviar el error real en formato JSON
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
    // 3. Importante: terminar la ejecución aquí
    exit;
}