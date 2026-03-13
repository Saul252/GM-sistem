<?php
/**
 * editarVentaController.php
 * Controlador para la gestión y edición de ventas existentes.
 */

// 1. Verificación de Rutas (Asegúrate de que estas rutas sean correctas en tu servidor)
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../config/conexion.php'; 
require_once __DIR__ . '/../models/ventasEditarModel.php'; 
require_once __DIR__ . '/../controllers/LayoutController.php';

class VentaHistorialController {
    private $model;

    public function __construct($db) {
        $this->model = new VentaHistorialModel($db);
    }

    /**
     * Carga los datos para visualizar la venta antes de cualquier edición.
     * Invocado por: cargarDatosVenta() en el JS
     */
    public function cargarDetalleVenta() {
        header('Content-Type: application/json');
        try {
            $id = intval($_GET['id'] ?? 0);
            if ($id <= 0) throw new Exception("ID de venta no proporcionado o inválido.");
            
            $detalle = $this->model->obtenerDetalleCompleto($id);
            
            if (!$detalle) {
                echo json_encode(["status" => "error", "message" => "No se encontró la información para la venta #$id"]);
            } else {
                echo json_encode($detalle);
            }
        } catch (Exception $e) {
            echo json_encode(["status" => "error", "message" => $e->getMessage()]);
        }
    }

    /**
     * Procesa la edición de cantidades y totales (Cambio de contrato).
     * Invocado por: enviarEdicion() en el JS
     */
    public function guardarEdicionVenta() {
        header('Content-Type: application/json');
        try {
            $json = file_get_contents('php://input');
            $data = json_decode($json, true);

            if (!$data) throw new Exception("Error al decodificar los datos de edición.");

            if (session_status() === PHP_SESSION_NONE) { session_start(); }
            $data['usuario_id'] = $_SESSION['usuario_id'] ?? 1;

            $res = $this->model->recalcularYEditarVenta($data);
            echo json_encode($res);
        } catch (Exception $e) {
            echo json_encode(["status" => "error", "message" => $e->getMessage()]);
        }
    }

    /**
     * Registra un nuevo pago/abono a la venta.
     */
    public function registrarAbono() {
        header('Content-Type: application/json');
        try {
            $venta_id = intval($_POST['venta_id'] ?? 0);
            $monto = floatval($_POST['monto'] ?? 0);
            
            if ($venta_id <= 0 || $monto <= 0) throw new Exception("Monto o ID de venta inválidos.");

            if (session_status() === PHP_SESSION_NONE) { session_start(); }
            $usuario_id = $_SESSION['usuario_id'] ?? 1;

            $res = $this->model->registrarAbono($venta_id, $monto, $usuario_id);
            echo json_encode($res);
        } catch (Exception $e) {
            echo json_encode(["status" => "error", "message" => $e->getMessage()]);
        }
    }

    /**
     * Registra la salida física de mercancía (Entrega parcial).
     */
    public function registrarEntrega() {
        header('Content-Type: application/json');
        try {
            $venta_id = intval($_POST['venta_id'] ?? 0);
            $productosRaw = $_POST['productos'] ?? []; 
            
            if (empty($productosRaw)) throw new Exception("No se seleccionaron productos para entregar.");

            $productosArr = [];
            foreach ($productosRaw as $dv_id => $cantidad) {
                if ($cantidad > 0) {
                    $productosArr[] = [
                        'detalle_venta_id' => $dv_id,
                        'cantidad_a_entregar' => $cantidad
                    ];
                }
            }

            if (session_status() === PHP_SESSION_NONE) { session_start(); }
            $usuario_id = $_SESSION['usuario_id'] ?? 1;

            $res = $this->model->procesarEntregaParcial($venta_id, $productosArr, $usuario_id);
            echo json_encode($res);
        } catch (Exception $e) {
            echo json_encode(["status" => "error", "message" => $e->getMessage()]);
        }
    }

/**
     * Obtiene el catálogo de productos disponibles para el almacén de la venta.
     * Invocado por: cargarCatalogoProductos() o similar en el JS.
     */
    public function cargarProductosAlmacen() {
        header('Content-Type: application/json');
        try {
            // Recibimos el almacen_id por GET
            $almacen_id = intval($_GET['almacen_id'] ?? 0);
            
            if ($almacen_id <= 0) {
                throw new Exception("ID de almacén no proporcionado.");
            }
            
            $productos = $this->model->obtenerProductosAlmacen($almacen_id);
            
            echo json_encode([
                "status" => "success",
                "data" => $productos
            ]);
        } catch (Exception $e) {
            echo json_encode([
                "status" => "error", 
                "message" => $e->getMessage()
            ]);
        }
    }

}
// --- RUTEADOR DE ACCIONES AJAX ---
// La variable $conexion viene de config/conexion.php
$controller = new VentaHistorialController($conexion);

if (isset($_GET['action'])) {
    switch ($_GET['action']) {
        case 'obtenerDetalle': 
            $controller->cargarDetalleVenta(); 
            break;
        case 'obtenerProductos': // <-- NUEVO CASO
            $controller->cargarProductosAlmacen();
            break;
        case 'guardarEdicion': 
            $controller->guardarEdicionVenta();
            break;
        case 'guardarAbono':
            $controller->registrarAbono();
            break;
        case 'guardarEntrega':
            $controller->registrarEntrega();
            break;
        default:
            echo json_encode(["status" => "error", "message" => "Acción '{$_GET['action']}' no reconocida."]);
            break;
    }
    exit; 
}

// Si no hay 'action', cargamos la interfaz visual
require_once __DIR__ . '/../views/editarventasController.php';