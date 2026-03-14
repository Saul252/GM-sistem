<?php
require_once __DIR__ . '/../../includes/auth.php';


require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../models/entregasModel.php'; 
require_once __DIR__ . '/../controllers/LayoutController.php';
protegerPagina('entregas'); 
$paginaActual='entregas';
$modelo = new EntregaModel($conexion);

// Datos de sesión
$almacen_usuario = $_SESSION['almacen_id'] ?? 0;
$es_admin        = (isset($_SESSION['rol_id']) && $_SESSION['rol_id'] == 1); 

/**
 * IMPORTANTE: Asegúrate de que en tu auth.php la sesión se guarde como 'id_usuario' 
 * o 'usuario_id'. Aquí homologamos para que coincida con tu Modelo.
 */
if(!isset($_SESSION['id_usuario']) && isset($_SESSION['usuario_id'])){
    $_SESSION['id_usuario'] = $_SESSION['usuario_id'];
}

// --- MANEJO DE PETICIONES AJAX (GET) ---
if (isset($_GET['ajax'])) {
    header('Content-Type: application/json');
    try {
        switch ($_GET['ajax']) {
            case 'listar':
                $entregas = $modelo->listarSalidasPendientes($_GET, $almacen_usuario, $es_admin);
                echo json_encode(['data' => $entregas]);
                break;

            case 'simular':
                $id = intval($_GET['id'] ?? 0);
                echo json_encode($modelo->simularDespachoLotes($id));
                break;

            case 'imprimir':
                $id = intval($_GET['id'] ?? 0);
                if ($id <= 0) throw new Exception("ID de movimiento no válido.");
                
                $datos = $modelo->obtenerDatosImpresion($id);
                if (!$datos) throw new Exception("No se encontraron datos para la impresión.");
                
                // Enviamos los datos. El campo 'detalle_json' ya vendrá estructurado desde el modelo.
                echo json_encode(['success' => true, 'data' => $datos]);
                break;
            case 'imprimirGanancia':
                $id = intval($_GET['id'] ?? 0);
                if ($id <= 0) throw new Exception("ID de movimiento no válido.");
                
                $datos = $modelo->obtenerDatosVentaGananciaImpresion($id);
                if (!$datos) throw new Exception("No se encontraron datos para la impresión.");
                
                // Enviamos los datos. El campo 'detalle_json' ya vendrá estructurado desde el modelo.
                echo json_encode(['success' => true, 'data' => $datos]);
                break;
                
            default:
                throw new Exception("Acción AJAX no reconocida.");
        }
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit; 
}

// --- MANEJO DE PETICIONES AJAX (POST) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['ajax'] ?? '';
    
    if ($accion === 'despachar') {
        header('Content-Type: application/json');
        try {
            $id_usuario = $_SESSION['id_usuario'] ?? 0;
            if ($id_usuario <= 0) throw new Exception("Sesión expirada o usuario no identificado.");

            $id_movimiento = intval($_POST['id_movimiento'] ?? 0);
            if ($id_movimiento <= 0) throw new Exception("ID de movimiento no válido.");

            $resultado = $modelo->procesarDespachoFisico($id_movimiento);
            echo json_encode($resultado);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }
}

// --- CARGA NORMAL DE LA VISTA ---
$paginaActual = 'Entregas';
require_once __DIR__ . '/../views/entregas_view.php';