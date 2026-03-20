<?php
/**
 * TrabajadorController.php 
 * Ajustado para integrarse con TrabajadorModel
 */

require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../controllers/LayoutController.php';
require_once __DIR__ . '/../models/trabajadores_model.php';
require_once __DIR__ . '/../models/almacen_model.php';
// Protegemos la página
protegerPagina('trabajadores'); 

$trabajadorModel = new TrabajadorModel($conexion);
$almacenesModel= new AlmacenModel($conexion);
$paginaActual = 'trabajadores';

// --- ACCIÓN: GUARDAR / ACTUALIZAR TRABAJADOR (AJAX) ---
// --- ACCIÓN: GUARDAR / ACTUALIZAR TRABAJADOR (AJAX) ---
if (isset($_POST['action']) && $_POST['action'] === 'guardar') {
    if (ob_get_level()) ob_clean(); 
    header('Content-Type: application/json');
    
    try {
        $datos = [
            'id'         => intval($_POST['id'] ?? 0),
            'nombre'     => trim($_POST['nombre'] ?? ''),
            'telefono'   => trim($_POST['telefono'] ?? ''),
            'rol'        => $_POST['rol'] ?? 'vendedor',
            'estado'     => $_POST['estado'] ?? 'activo',
            // Si el usuario es admin (0), toma el del select; si no, toma el de su sesión
            'almacen_id' => ($_SESSION['almacen_id'] == 0) ? intval($_POST['almacen_id'] ?? 0) : intval($_SESSION['almacen_id'])
        ];

        if (empty($datos['nombre']) || empty($datos['telefono'])) {
            throw new Exception("El nombre y el teléfono son obligatorios.");
        }
        
        if ($datos['almacen_id'] <= 0) {
            throw new Exception("Debes asignar un almacén válido al trabajador.");
        }

        $resultado = $trabajadorModel->guardar($datos);
        
        echo json_encode([
            'status'  => 'success', 
            'message' => "Operación exitosa.",
            'id'      => ($datos['id'] > 0) ? $datos['id'] : $conexion->insert_id
        ]);

    } catch (Throwable $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
    exit;
}

// --- CARGA DE VISTA (GET) ---
if ($_SERVER['REQUEST_METHOD'] === 'GET' && !isset($_GET['action'])) {
    try {
        $almacenusu = $_SESSION['almacen_id'];
        
        // Si es admin (0), listamos todos; si no, solo los de su almacén
        $trabajadores = ($almacenusu == 0) ? $trabajadorModel->listar() : $trabajadorModel->listarPorAlmacen($almacenusu);
        
        // Obtenemos lista de almacenes para el selector del modal
        $listaAlmacenes = $almacenesModel->getAlmacenes($almacenusu); 
        
        $tituloPagina = "Gestión de Personal";
        require_once __DIR__ . '/../views/trabajadores_view.php';
        
    } catch (Exception $e) {
        die("Error al cargar la vista: " . $e->getMessage());
    }
}
// --- ACCIÓN: ELIMINAR TRABAJADOR (AJAX) ---
if (isset($_POST['action']) && $_POST['action'] === 'eliminar') {
    if (ob_get_level()) ob_clean();
    header('Content-Type: application/json');
    
    try {
        $id = intval($_POST['id'] ?? 0);
        if ($id <= 0) throw new Exception("ID no válido.");

        $resultado = $trabajadorModel->eliminar($id);
        
        if ($resultado) {
            echo json_encode(['status' => 'success', 'message' => 'Trabajador eliminado.']);
        } else {
            throw new Exception("No se pudo eliminar el registro.");
        }
    } catch (Throwable $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
    exit;
}

// --- CARGA DE VISTA (GET) ---
if ($_SERVER['REQUEST_METHOD'] === 'GET' && !isset($_GET['action'])) {
    try {
        $almacenusu=$_SESSION['almacen_id'];
        $trabajadores = $trabajadorModel->listar();
        $almacenesModel= $almacenes->getAlmacenes($almacenusu);
        $tituloPagina = "Gestión de Personal";
        
        // Asegúrate de que la ruta a la vista sea correcta
        require_once __DIR__ . '/../views/trabajadores_view.php';
        
    } catch (Exception $e) {
        die("Error al cargar la vista de trabajadores: " . $e->getMessage());
    }
}