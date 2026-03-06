<?php
// 1. Reporte de errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 2. Seguridad y Sesión
require_once __DIR__ . '/../../includes/auth.php';
protegerPagina(); 

// 3. Carga de dependencias
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../models/almacen_model.php';
require_once __DIR__ . '/../models/almacen/productosModel.php';
// CARGA DEL NUEVO MODELO DE CATEGORÍAS
require_once __DIR__ . '/../models/almacen/categoriasModel.php'; 

require_once __DIR__ . '/../controllers/LayoutController.php';

class AlmacenController {
    private $model;
    private $productoModel;
    private $categoriaModel; // Propiedad para el modelo de categorías
    private $conexion;

    public function __construct($conexion) {
        $this->conexion = $conexion;
        $this->model = new AlmacenModel($conexion);
        $this->productoModel = new ProductoModel($conexion);
        $this->categoriaModel = new CategoriaModel($conexion); // Instancia de categorías
    }

    public function index() {
        $paginaActual = 'almacenes'; 
        $almacen_usuario = $_SESSION['almacen_id'] ?? 0;

        try {
            $categorias = $this->model->getCategorias();
            $almacenes = $this->model->getAlmacenes($almacen_usuario);
            $productos = $this->model->getInventario($almacen_usuario);
            $todosLosAlmacenes = $this->model->getAlmacenes($almacen_usuario);

            if ($categorias === null) $categorias = [];
            if ($almacenes === null) $almacenes = [];
            if ($productos === null) $productos = [];

            require_once __DIR__ . '/../views/almacenes_view.php';

        } catch (Exception $e) {
            die("Error en el Controlador de Almacén: " . $e->getMessage());
        }
    }

    /**
     * AJAX: Guardar Categoría
     */
    public function guardarCategoria() {
        header('Content-Type: application/json');
        $nombre = trim($_POST['nombre'] ?? '');

        if (empty($nombre)) {
            echo json_encode(['status' => 'error', 'message' => 'El nombre es obligatorio']);
            return;
        }

        try {
            if ($this->categoriaModel->existe($nombre)) {
                echo json_encode(['status' => 'error', 'message' => 'Esta categoría ya existe']);
                return;
            }

            $id = $this->categoriaModel->guardar($nombre);
            if ($id) {
                echo json_encode(['status' => 'success', 'id' => $id, 'nombre' => $nombre]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'No se pudo guardar la categoría']);
            }
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    /**
     * AJAX: Guardar Producto
     */
    public function guardarProducto() {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['status' => 'error', 'message' => 'Acceso no permitido']);
            return;
        }

        $datos = [
            'sku'                 => trim($_POST['sku'] ?? ''),
            'nombre'              => trim($_POST['nombre'] ?? ''),
            'categoria_id'        => $_POST['categoria_id'] ?? null,
            'unidad_medida'       => $_POST['unidad_medida'] ?? 'PZA',
            'unidad_reporte'      => $_POST['unidad_reporte'] ?? '',
            'factor_conversion'   => floatval($_POST['factor_conversion'] ?? 1),
            'precio_adquisicion'  => 0, // Como pediste: forzado a cero
            'impuesto_iva'        => floatval($_POST['impuesto_iva'] ?? 16.00),
            'descripcion'         => $_POST['description'] ?? '',
            'fiscal_clave_prod'   => $_POST['fiscal_clave_prod'] ?? '',
            'fiscal_clave_unit'   => $_POST['fiscal_clave_unit'] ?? '',
            'precio_minorista'    => floatval($_POST['precio_minorista'] ?? 0),
            'precio_mayorista'    => floatval($_POST['precio_mayorista'] ?? 0),
            'precio_distribuidor' => floatval($_POST['precio_distribuidor'] ?? 0)
        ];

        if (empty($datos['sku']) || empty($datos['nombre'])) {
            echo json_encode(['status' => 'error', 'message' => 'SKU y Nombre son obligatorios']);
            return;
        }

        $nuevoId = $this->productoModel->guardarCompleto($datos);

        if ($nuevoId) {
            echo json_encode(['status' => 'success', 'message' => 'Producto registrado exitosamente', 'id' => $nuevoId]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error al guardar el producto.']);
        }
    }
}

/**
 * LÓGICA DE ENRUTAMIENTO MEJORADA
 */
if (isset($conexion)) {
    $controller = new AlmacenController($conexion);
    
    // Obtenemos la acción del URL
    $action = $_GET['action'] ?? 'index';

    switch ($action) {
        case 'guardar':
            $controller->guardarProducto();
            break;
        case 'guardarCategoria':
            $controller->guardarCategoria();
            break;
        default:
            $controller->index();
            break;
    }
} else {
    die("Error: No se pudo establecer la conexión.");
}