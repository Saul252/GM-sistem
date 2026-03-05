<?php
// 1. Reporte de errores para depuración
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 2. Seguridad y Sesión
require_once __DIR__ . '/../../includes/auth.php';
protegerPagina(); 

// 3. Carga de dependencias (Base de Datos y Modelos)
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../models/almacen_model.php';

// 4. Carga del Layout (Asegúrate que la ruta sea correcta)
// Si LayoutController.php no define 'renderizarLayout', 
// revisa si es una clase o una función simple.
require_once __DIR__ . '/../controllers/LayoutController.php';

class AlmacenController {
    private $model;

    public function __construct($conexion) {
        // Inicializamos el modelo pasando la conexión activa
        $this->model = new AlmacenModel($conexion);
    }

    public function index() {
        // Variables requeridas por el Layout y la Vista
        $paginaActual = 'almacenes'; 
        $almacen_usuario = $_SESSION['almacen_id'] ?? 0;

        try {
            // Pedimos los datos al modelo
            $categorias = $this->model->getCategorias();
            $almacenes = $this->model->getAlmacenes($almacen_usuario);
            $productos = $this->model->getInventario($almacen_usuario);
            $todosLosAlmacenes= $this->model->getAlmacenes($almacen_usuario);

            // Verificación de datos para evitar errores en la vista
            if ($categorias === null) $categorias = [];
            if ($almacenes === null) $almacenes = [];
            if ($productos === null) $productos = [];

            // Cargamos la vista
            // Nota: Al usar require_once aquí, todas las variables de arriba 
            // ($categorias, $productos, etc.) estarán disponibles en almacenes_view.php
            require_once __DIR__ . '/../views/almacenes_view.php';

        } catch (Exception $e) {
            die("Error en el Controlador de Almacén: " . $e->getMessage());
        }
    }
}

/**
 * AUTO-EJECUCIÓN DEL CONTROLADOR
 * Importante: Si este archivo se llama directamente desde el navegador, 
 * necesitamos crear el objeto y llamar al método index().
 */
if (isset($conexion)) {
    $controller = new AlmacenController($conexion);
    $controller->index();
} else {
    die("Error: No se pudo establecer la conexión con la base de datos.");
}