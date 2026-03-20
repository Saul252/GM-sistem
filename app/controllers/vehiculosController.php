<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Rutas de archivos base
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../controllers/LayoutController.php';
require_once __DIR__ . '/../models/vehiculos_model.php';
require_once __DIR__ . '/../models/almacen_model.php'; // Necesario para el selector de admin

protegerPagina('vehiculos'); 

$vehiculoModel = new VehiculoModel($conexion);
$almacenModel = new AlmacenModel($conexion); // Instanciamos para obtener la lista de sucursales
$paginaActual = 'vehiculos';

// --- MANEJO DE PETICIONES POST (AJAX) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if (ob_get_level()) ob_end_clean(); 
    header('Content-Type: application/json');
    
    try {
        $action = $_POST['action'];

        if ($action === 'guardar') {
            // Lógica de "Selector Inteligente" para el Almacén
            $datos = $_POST;
            $almacenSesion = intval($_SESSION['almacen_id'] ?? 0);

            // Si no es admin global, forzamos su almacen_id de sesión por seguridad
            if ($almacenSesion !== 0) {
                $datos['almacen_id'] = $almacenSesion;
            }

            $res = $vehiculoModel->guardar($datos);
            echo json_encode(['status' => $res ? 'success' : 'error']);
            
        } elseif ($action === 'eliminar') {
            $id = intval($_POST['id'] ?? 0);
            $res = $vehiculoModel->eliminar($id);
            echo json_encode(['status' => $res ? 'success' : 'error']);
        }

    } catch (Throwable $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
    exit;
}

// --- CARGA DE LA VISTA (GET) ---
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        $almacenSesion = intval($_SESSION['almacen_id'] ?? 0);

        // Si es admin global (0), ve todos. Si no, solo los de su sucursal.
        if ($almacenSesion === 0) {
            $vehiculos = $vehiculoModel->listar();
            $listaAlmacenes = $almacenModel->getAlmacenes($almacenSesion); // Para el select del modal
        } else {
            $vehiculos = $vehiculoModel->listarPorAlmacen($almacenSesion);
            $listaAlmacenes = []; // No lo necesita porque se le asigna el suyo automáticamente
        }

        $tituloPagina = "Control de Flota";
        $vistaRuta = __DIR__ . '/../views/vehiculos_view.php';
        
        if (file_exists($vistaRuta)) {
            require_once $vistaRuta;
        } else {
            throw new Exception("La vista 'vehiculos_view.php' no existe.");
        }
    } catch (Exception $e) {
        die("Error al cargar la flota: " . $e->getMessage());
    }
}