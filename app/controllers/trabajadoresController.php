<?php
/**
 * TrabajadorController.php 
 * Ajustado para integrarse con TrabajadorModel
 */

require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../controllers/LayoutController.php';
require_once __DIR__ . '/../models/trabajadores_model.php';

// Protegemos la página
protegerPagina('trabajadores'); 

$trabajadorModel = new TrabajadorModel($conexion);
$paginaActual = 'trabajadores';

// --- ACCIÓN: GUARDAR / ACTUALIZAR TRABAJADOR (AJAX) ---
if (isset($_POST['action']) && $_POST['action'] === 'guardar') {
    if (ob_get_level()) ob_clean(); 
    header('Content-Type: application/json');
    
    try {
        // Preparamos los datos tal como los espera tu modelo guardar($d)
        $datos = [
            'id'       => intval($_POST['id'] ?? 0), // El modelo usa empty($d['id']) para decidir
            'nombre'   => trim($_POST['nombre'] ?? ''),
            'telefono' => trim($_POST['telefono'] ?? ''),
            'rol'      => $_POST['rol'] ?? 'vendedor',
            'estado'   => $_POST['estado'] ?? 'activo'
        ];

        if (empty($datos['nombre']) || empty($datos['telefono'])) {
            throw new Exception("El nombre y el teléfono son obligatorios.");
        }

        // Llamada ajustada a tu modelo: solo un parámetro
        $resultado = $trabajadorModel->guardar($datos);
        
        if ($resultado) {
            echo json_encode([
                'status'  => 'success', 
                'message' => ($datos['id'] > 0) ? "Datos actualizados correctamente." : "Trabajador registrado con éxito.",
                'id'      => ($datos['id'] > 0) ? $datos['id'] : $conexion->insert_id
            ]);
        } else {
            throw new Exception("Error al procesar la solicitud en la base de datos.");
        }

    } catch (Throwable $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
    exit;
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
        $trabajadores = $trabajadorModel->listar();
        $tituloPagina = "Gestión de Personal";
        
        // Asegúrate de que la ruta a la vista sea correcta
        require_once __DIR__ . '/../views/trabajadores_view.php';
        
    } catch (Exception $e) {
        die("Error al cargar la vista de trabajadores: " . $e->getMessage());
    }
}