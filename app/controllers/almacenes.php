<?php
// 1. Reporte de errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 2. Seguridad y Sesión
require_once __DIR__ . '/../../includes/auth.php';


// 3. Carga de dependencias
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../models/almacen_model.php';
require_once __DIR__ . '/../models/almacen/productosModel.php';
require_once __DIR__ . '/../models/almacen/categoriasModel.php'; 

require_once __DIR__ . '/../controllers/LayoutController.php';
protegerPagina('almacenes'); 
class AlmacenController {
    private $model;
    private $productoModel;
    private $categoriaModel; 
    private $conexion;

    public function __construct($conexion) {
        $this->conexion = $conexion;
        $this->model = new AlmacenModel($conexion);
        $this->productoModel = new ProductoModel($conexion);
        $this->categoriaModel = new CategoriaModel($conexion); 
    }

   public function index() {

    $paginaActual = 'almacenes'; 
    // Mantenemos el ID de sesión para los filtros de las tablas de abajo
    $almacen_usuario = $_SESSION['almacen_id'] ?? 0;

    try {
        // 1. Cargamos el catálogo y almacenes para los selectores/tablas
        $categorias = $this->model->getCategorias();
        $almacenes = $this->model->getAlmacenes($almacen_usuario);
        $todosLosAlmacenes = $this->model->getAlmacenesDestino($almacen_usuario);
        
        // 2. Cargamos el inventario detallado para el DataTable
        $productos = $this->model->getInventario($almacen_usuario);

        // --- 3. NUEVA LÓGICA: RESUMEN AUTOMÁTICO PARA LAS TARJETAS ---
        // El modelo detectará por sesión si es Admin o Vendedor
       
        $resumenData = $this->model->getResumenStock( $almacen_usuario);

// AÑADE ESTO TEMPORALMENTE PARA TESTEAR:
// var_dump($resumenData); die();
        // -------------------------------------------------------------

        // Validaciones de seguridad para evitar errores en la vista
        if ($categorias === null) $categorias = [];
        if ($almacenes === null) $almacenes = [];
        if ($productos === null) $productos = [];
        if ($resumenData === null) {
            $resumenData = [
                'tipo' => 'error', 
                'nombre' => 'No disponible', 
                'mis_productos' => 0, 
                'total_sistema' => 0
            ];
        }

        // 4. Renderizamos la vista (ya lleva $resumenData inyectado)
        $tituloPagina='Almacenes';
        require_once __DIR__ . '/../views/almacenes_view.php';

    } catch (Exception $e) {
        // Un mensaje un poco más limpio para el usuario final
        error_log("Error en AlmacenController: " . $e->getMessage());
        die("Lo sentimos, hubo un problema al cargar el inventario. Por favor, intenta más tarde.");
    }
}

    /**
     * AJAX: Obtener lista completa de productos para refrescar Selects en Compras
     */
    public function getListaProductosJson() {
        while (ob_get_level()) ob_end_clean(); // Limpiar búfer para JSON puro
        header('Content-Type: application/json; charset=utf-8');
        try {
            // Nota: Usamos getProductos() o el método que tengas en tu productoModel 
            // que devuelva el catálogo básico (id, nombre, sku, factor, unidades)
            $productos = $this->productoModel->getProductos(); 
            echo json_encode($productos ?: []);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
        exit;
    }

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
            echo json_encode(['status' => 'success', 'id' => $id, 'nombre' => $nombre]);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
        exit;
    }
    
    public function getCategoriasJSON() {
        while (ob_get_level()) ob_end_clean(); 
        header('Content-Type: application/json; charset=utf-8');
        try {
            $categorias = $this->model->getCategorias();
            echo json_encode($categorias ?: []);
        } catch (Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
        exit;
    }

    public function guardarProducto() {
        while (ob_get_level()) ob_end_clean(); // Asegurar respuesta limpia
        header('Content-Type: application/json');

        $datos = [
            'sku'                 => trim($_POST['sku'] ?? ''),
            'nombre'              => trim($_POST['nombre'] ?? ''),
            'categoria_id'        => $_POST['categoria_id'] ?? null,
            'unidad_medida'       => $_POST['unidad_medida'] ?? 'PZA',
            'unidad_reporte'      => $_POST['unidad_reporte'] ?? '',
            'factor_conversion'   => floatval($_POST['factor_conversion'] ?? 1),
            'precio_adquisicion'  => 0,
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
            exit;
        }

        $nuevoId = $this->productoModel->guardarCompleto($datos);

        if ($nuevoId) {
            echo json_encode(['status' => 'success', 'message' => 'Producto registrado exitosamente', 'id' => $nuevoId]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error al guardar el producto.']);
        }
        exit;
    }
    // Añade este método antes del final de la llave de la clase }
public function obtenerListaAlmacenes() {
    // 1. Limpiamos cualquier salida previa (espacios, warnings, etc)
    while (ob_get_level()) ob_end_clean(); 
    
    // 2. Cabeceras obligatorias
    header('Content-Type: application/json; charset=utf-8');
    
    try {
        // Llamamos a tu modelo con 0 para traer todos
        $almacenes = $this->model->getAlmacenes(0); 
        
        if (!$almacenes) {
            echo json_encode([]);
        } else {
            echo json_encode($almacenes);
        }
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
    // 3. Terminamos la ejecución para que no se pegue el HTML del Layout
    exit; 
}
/**
     * AJAX: Obtiene el resumen de productos (Mi Almacén vs Total Sistema)
     */


}

/**
 * LÓGICA DE ENRUTAMIENTO
 */
if (isset($conexion)) {
    $controller = new AlmacenController($conexion);
    $action = $_GET['action'] ?? 'index';

    switch ($action) {
        case 'guardar':
            $controller->guardarProducto();
            break;
        case 'guardarCategoria':
            $controller->guardarCategoria();
            break;
        case 'getCategoriasJSON':
            $controller->getCategoriasJSON();
            break;
        case 'getListaProductosJson': // <--- SECCIÓN PARA ACTUALIZAR SELECTS
            $controller->getListaProductosJson();
            break;
            case 'getAlmacenesJSON': // <--- AÑADE ESTO
        $controller->obtenerListaAlmacenes();
        break;
   // --- NUEVO CASO AQUÍ ---
        
        // -----------------------
        default:
            $controller->index();
            break;
    }
} else {
    die("Error: No se pudo establecer la conexión.");
}