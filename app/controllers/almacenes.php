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
        
        $unidadesMedida = $this->model->getUnidadesMedida();

        $unidadesMedidam = $this->model->getUnidadesMedida();

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
        require_once __DIR__ . '/../views/almacenes_view2.php';

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
     public function getUnidadesMedidaJSON() {
        while (ob_get_level()) ob_end_clean(); 
        header('Content-Type: application/json; charset=utf-8');
        try {
            $unidadesMedida = $this->model->getCategorias();
            echo json_encode($unidadesMedida ?: []);
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
            'fiscal_clave_unidad'   => $_POST['fiscal_clave_unidad'] ?? '',
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

public function guardarProductoCompleto() {
   while (ob_get_level()) ob_end_clean();

ini_set('display_errors', 0);
error_reporting(0);

header('Content-Type: application/json');
    try {

        // 🔹 1. Armar datos
        $data = [
            'sku' => trim($_POST['sku'] ?? ''),
            'nombre' => trim($_POST['nombre'] ?? ''),
            'descripcion' => $_POST['description'] ?? '',
            'categoria_id' => !empty($_POST['categoria_id']) ? $_POST['categoria_id'] : null,
            'unidad_medida' => $_POST['unidad_medida'] ?? 'PZA',
            'unidad_reporte' => $_POST['unidad_reporte'] ?? null,
            'factor_conversion' => floatval($_POST['factor_conversion'] ?? 1),
            'precio_adquisicion' => floatval($_POST['precio_adquisicion'] ?? 0),
            'fiscal_clave_prod' => $_POST['fiscal_clave_prod'] ?? null,
            'fiscal_clave_unit' => $_POST['fiscal_clave_unit'] ?? null,
            'impuesto_iva' => floatval($_POST['impuesto_iva'] ?? 16),
            'almacenes' => $_POST['almacenes'] ?? [],
            'usuario_id' => $_SESSION['usuario_id'] ?? 1
        ];

        // 🔹 2. Validación básica
        if (empty($data['sku']) || empty($data['nombre'])) {
            echo json_encode([
                'status' => 'error',
                'message' => 'SKU y Nombre son obligatorios'
            ]);
            exit;
        }

        // 🔹 3. Llamar al modelo PRO
        $resultado = $this->productoModel->crearProducto($data);

        // 🔹 4. Respuesta
        if ($resultado['status']) {
            echo json_encode([
                'status' => 'success',
                'message' => 'Producto guardado correctamente'
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => $resultado['msg']
            ]);
        }

    } catch (Exception $e) {
    error_log($e->getMessage()); // 👈 guarda error real en logs

    echo json_encode([
        'status' => 'error',
        'message' => 'Error interno del servidor'
    ]);

    }

    exit;
}
/**
     * AJAX: Obtiene el resumen de productos (Mi Almacén vs Total Sistema)
     */
public function obtenerProductoDetalle()
{
    while (ob_get_level()) ob_end_clean();
    header('Content-Type: application/json');

    $id = $_GET['id'] ?? 0;
    $almacen_id = $_GET['almacen_id'] ?? 0;

    if (!$id || !$almacen_id) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Parámetros incompletos'
        ]);
        exit;
    }

    $resultado = $this->productoModel->obtenerProductoPorAlmacen($id, $almacen_id);

    if ($resultado['status']) {
        echo json_encode([
            'status' => 'success',
            'producto' => $resultado['data']
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => $resultado['msg']
        ]);
    }

    exit;
}
public function actualizarProducto()
{
    while (ob_get_level()) ob_end_clean();
    header('Content-Type: application/json');

    try {

        $data = [
            'id' => $_POST['producto_id'] ?? 0,
            'almacen_id' => $_POST['almacen_actual_id'] ?? 0,
            'sku' => $_POST['sku'] ?? '',
            'nombre' => $_POST['nombre'] ?? '',
            'descripcion' => $_POST['descripcion'] ?? '',
            'categoria_id' => $_POST['categoria_id'] ?? null,

            'fiscal_clave_prod' => $_POST['fiscal_clave_prod'] ?? '',
            'fiscal_clave_unit' => $_POST['fiscal_clave_unidad'] ?? '',
            'impuesto_iva' => floatval($_POST['impuesto_iva'] ?? 0),

            'unidad_reporte' => $_POST['unidad_reporte'] ?? '',
            'factor_conversion' => floatval($_POST['factor_conversion'] ?? 1),
            'unidad_medida' => $_POST['unidad_medida'] ?? '',

            'precio_minorista' => floatval($_POST['precio_minorista'] ?? 0),
            'precio_mayorista' => floatval($_POST['precio_mayorista'] ?? 0),
            'precio_distribuidor' => floatval($_POST['precio_distribuidor'] ?? 0),

            'stock' => floatval($_POST['stock'] ?? 0),
            'stock_minimo' => floatval($_POST['stock_minimo'] ?? 0),

            'aplicar_global' => isset($_POST['aplicar_global'])
        ];

        if (!$data['id']) {
            echo json_encode([
                'status' => 'error',
                'message' => 'ID inválido'
            ]);
            exit;
        }

        $res = $this->productoModel->actualizarProductoCompleto($data);

        if ($res['status']) {
            echo json_encode([
                'status' => 'success',
                'message' => 'Producto actualizado correctamente'
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => $res['msg']
            ]);
        }

    } catch (Exception $e) {
        error_log($e->getMessage());

        echo json_encode([
            'status' => 'error',
            'message' => 'Error interno del servidor'
        ]);
    }

    exit;
}
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
            case 'guardarCompleto':
            $controller->guardarProductoCompleto();
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
            case 'getProducto': // <--- SECCIÓN PARA ACTUALIZAR SELECTS
            $controller->obtenerProductoDetalle();
            break;
            case 'actualizarProducto':
    $controller->actualizarProducto();
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