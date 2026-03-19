<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Rutas de archivos base
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../controllers/LayoutController.php';
require_once __DIR__ . '/../models/vehiculos_model.php';

protegerPagina('vehiculos'); 

$vehiculoModel = new VehiculoModel($conexion);
$paginaActual = 'vehiculos';

// --- MANEJO DE PETICIONES POST (AJAX) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    // Limpiamos cualquier salida previa para asegurar un JSON puro
    if (ob_get_level()) ob_end_clean(); 
    header('Content-Type: application/json');
    
    try {
        $res = false;
        $action = $_POST['action'];

        if ($action === 'guardar') {
            $res = $vehiculoModel->guardar($_POST);
        } elseif ($action === 'eliminar') {
            $id = intval($_POST['id'] ?? 0);
            $res = $vehiculoModel->eliminar($id);
        }

        echo json_encode(['status' => $res ? 'success' : 'error']);
    } catch (Throwable $e) {
        // En caso de error de SQL o código, devolvemos el mensaje al SweetAlert
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
    exit;
}

// --- CARGA DE LA VISTA (GET) ---
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $vehiculos = $vehiculoModel->listar();
    $tituloPagina = "Control de Flota";
    
    // Corregido: nombre del archivo de la vista
    $vistaRuta = __DIR__ . '/../views/vehiculos_view.php';
    
    if (file_exists($vistaRuta)) {
        require_once $vistaRuta;
    } else {
        die("Error: La vista 'vehiculos_view.php' no existe en la ruta especificada.");
    }
}