<?php
/**
 * egresosController.php
 * Controlador para la gestión unificada de Egresos (Compras y Gastos)
 */
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../controllers/LayoutController.php';
require_once __DIR__ . '/../models/egresos_model.php';
require_once __DIR__ . '/../models/egresos/comprasModel.php';
require_once __DIR__ . '/../models/proveedoresModel.php';
require_once __DIR__ . '/../models/almacen/categoriasModel.php';
require_once __DIR__ . '/../models/egresos/gastosModel.php';
require_once __DIR__ . '/../models/categoriasGastosModel.php';

protegerPagina('compras'); 

$egresoModel = new EgresoModel($conexion);
$comprasModel = new CompraModel($conexion);
$gastosModel = new GastoModel($conexion);
$categoriasModel = new CategoriaModel($conexion);
$gastosCategorias = new CategoriasGasto($conexion);
$proveedorModel = new ProveedoresModel($conexion);

// --- CORRECCIÓN DE WARNINGS: Definición global de $action ---
$action = $_GET['action'] ?? $_POST['action'] ?? '';
$paginaActual = 'compras';

// =========================================================================
// --- NUEVAS ACCIONES: CRUD CATEGORÍAS DE GASTOS ---
// =========================================================================

if ($action === 'get_categorias_egresos') {
    if (ob_get_level()) ob_clean();
    header('Content-Type: application/json');
    try {
        $res = $gastosCategorias->listarTodas();
        echo json_encode(["success" => true, "data" => $res]);
    } catch (Exception $e) {
        echo json_encode(["success" => false, "message" => $e->getMessage()]);
    }
    exit;
}

if ($action === 'guardar_categoria_gasto') {
    if (ob_get_level()) ob_clean();
    header('Content-Type: application/json');
    try {
        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        $nombre = trim($_POST['nombre'] ?? '');
        $descripcion = trim($_POST['descripcion'] ?? '');

        if (empty($nombre)) throw new Exception("El nombre de la categoría es obligatorio.");

        if ($id > 0) {
            $resultado = $gastosCategorias->actualizar($id, $nombre, $descripcion);
            $mensaje = "Categoría actualizada correctamente";
            $id_final = $id;
        } else {
            $id_final = $gastosCategorias->guardar($nombre, $descripcion);
            $resultado = $id_final ? true : false;
            $mensaje = "Categoría creada correctamente";
        }
        echo json_encode(["success" => $resultado, "message" => $mensaje, "id_insertado" => $id_final]);
    } catch (Exception $e) {
        echo json_encode(["success" => false, "message" => $e->getMessage()]);
    }
    exit;
}

if ($action === 'eliminar_categoria') {
    if (ob_get_level()) ob_clean();
    header('Content-Type: application/json');
    try {
        $id = intval($_POST['id'] ?? 0);
        if ($id <= 0) throw new Exception("ID no válido.");
        $resultado = $gastosCategorias->eliminar($id);
        echo json_encode(["success" => $resultado]);
    } catch (Exception $e) {
        echo json_encode(["success" => false, "message" => $e->getMessage()]);
    }
    exit;
}

// =========================================================================
// --- ACCIONES ORIGINALES ---
// =========================================================================

if ($action === 'guardarCompraInventario') {
    if (ob_get_length()) ob_clean(); 
    header('Content-Type: application/json');
    try {
        $user_id = $_SESSION['usuario_id'] ?? 1;
        $rol_id  = $_SESSION['rol_id'] ?? 0;

        if ($rol_id == 1 && isset($_POST['almacen_id_cabecera'])) {
            $almacen_principal = intval($_POST['almacen_id_cabecera']);
        } else {
            $almacen_principal = $_SESSION['almacen_id'] ?? null;
        }

        if (!$almacen_principal) throw new Exception("No se pudo determinar el almacén de cargo.");

        $resultado = $comprasModel->guardarCompraCompleta(
            $_POST['items'] ?? [], 
            $_POST['folio'] ?? 'S/F', 
            $_POST['proveedor'] ?? 'Sin Proveedor',
            (isset($_FILES['evidencia_compra']) && $_FILES['evidencia_compra']['error'] === UPLOAD_ERR_OK) ? $_FILES['evidencia_compra'] : null,
            $almacen_principal,
            $user_id
        );
        echo json_encode($resultado ?? ['success' => false, 'message' => 'El modelo no respondió']);
    } catch (Throwable $e) {
        echo json_encode(['success' => false, 'message' => 'Fallo en el Sistema: ' . $e->getMessage()]);
    }
    exit;
}

if ($action === 'guardarGasto') {
    header('Content-Type: application/json');
    try {
        $rol_id = $_SESSION['rol_id'] ?? 0;
        $almacen_final = ($rol_id == 1) ? intval($_POST['almacen_id'] ?? 0) : intval($_SESSION['almacen_id'] ?? 0);
        if ($almacen_final <= 0) throw new Exception("Almacén no válido.");

        $urlDocumento = null;
        if (isset($_FILES['documento']) && $_FILES['documento']['error'] === UPLOAD_ERR_OK) {
            $rutaCarpeta = $_SERVER['DOCUMENT_ROOT'] . "/cfsistem/uploads/evidencias/";
            if (!is_dir($rutaCarpeta)) mkdir($rutaCarpeta, 0777, true);
            $ext = pathinfo($_FILES['documento']['name'], PATHINFO_EXTENSION);
            $nuevoNombre = "GASTO_" . time() . "_" . uniqid() . "." . $ext;
            if (move_uploaded_file($_FILES['documento']['tmp_name'], $rutaCarpeta . $nuevoNombre)) {
                $urlDocumento = $nuevoNombre;
            }
        }

        $cabecera = [
            'folio'        => $_POST['folio'] ?? 'S/F',
            'fecha'        => date('Y-m-d'),
            'almacen_id'   => $almacen_final,
            'categoria_id' => $_POST['categoria_id'] ?? null, // <--- Nuevo campo integrado
            'usuario_id'   => $_SESSION['usuario_id'] ?? 1,
            'beneficiario' => $_POST['beneficiario'] ?? '',
            'metodo_pago'  => $_POST['metodo_pago'] ?? 'Efectivo',
            'total'        => $_POST['total_final'] ?? 0,
            'documento_url'=> $urlDocumento,
            'observaciones'=> $_POST['observaciones'] ?? ''
        ];

        $res = $egresoModel->registrarGasto($cabecera, $_POST['desc'] ?? [], $_POST['cant'] ?? [], $_POST['precio'] ?? []);
        echo json_encode(['success' => true, 'message' => 'Gasto guardado correctamente']);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}

if ($action === 'buscarProductos') {
    header('Content-Type: application/json');
    $termino = $_GET['q'] ?? '';
    $productos = $comprasModel->obtenerProductos($termino);
    echo json_encode($productos);
    exit;
}

if ($action === 'obtenerFaltantes') {
    header('Content-Type: application/json');
    $compra_id = intval($_GET['compra_id'] ?? 0);
    $faltantes = $comprasModel->obtenerDetalleFaltantes($compra_id);
    echo json_encode($faltantes);
    exit;
}

if ($action === 'procesarAjusteFaltante') {
    header('Content-Type: application/json');
    try {
        $compra_id = intval($_POST['compra_id'] ?? 0);
        $distribucion = $_POST['distribucion'] ?? [];
        $user_id = $_SESSION['usuario_id'] ?? 1;
        if ($compra_id <= 0 || empty($distribucion)) throw new Exception("Datos no válidos.");
        $res = $comprasModel->procesarAjusteFaltante($compra_id, $distribucion, $user_id);
        echo json_encode($res);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}

if ($action === 'getSiguienteFolioGasto') {
    if (ob_get_length()) ob_clean();
    header('Content-Type: application/json');
    try {
        $siguiente = $gastosModel->generarSiguienteFolioGasto();
        echo json_encode(['success' => true, 'folio' => $siguiente]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}

if ($action === 'getSiguienteFolio') {
    header('Content-Type: application/json');
    $siguiente = $comprasModel->generarSiguienteFolio();
    echo json_encode(['success' => true, 'folio' => $siguiente]);
    exit;
}

if ($action === 'obtenerDetalleMovimiento') {
    while (ob_get_level()) ob_end_clean(); 
    header('Content-Type: application/json');
    $tipo = $_GET['tipo'] ?? '';
    $id = intval($_GET['id'] ?? 0);
    try {
        $resultado = $egresoModel->obtenerDetalleCompleto($tipo, $id);
        if ($resultado && $resultado['cabecera']) {
            echo json_encode(['success' => true, 'cabecera' => $resultado['cabecera'], 'items' => $resultado['items']]);
        } else {
            echo json_encode(['success' => false, 'message' => 'No se encontró el registro.']);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}

if ($action === 'getProveedoresJSON') {
    while (ob_get_level()) ob_end_clean(); 
    header('Content-Type: application/json; charset=utf-8');
    try {
        $lista = $proveedorModel->listarTodos();
        echo json_encode($lista ?: []);
    } catch (Exception $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
    exit;
}

if ($action === 'guardarProveedor') {
    while (ob_get_level()) ob_end_clean(); 
    header('Content-Type: application/json; charset=utf-8');
    try {
        $datos = [
            'nombre_comercial' => trim($_POST['nombre_comercial'] ?? ''),
            'razon_social'     => trim($_POST['razon_social'] ?? ''),
            'rfc'              => trim($_POST['rfc'] ?? 'XAXX010101000'),
            'correo'           => trim($_POST['correo'] ?? ''),
            'telefono'         => trim($_POST['telefono'] ?? '')
        ];
        if (empty($datos['nombre_comercial'])) throw new Exception("El nombre comercial es obligatorio.");
        if ($proveedorModel->guardar($datos)) {
            echo json_encode(['success' => true, 'message' => 'Proveedor guardado', 'nuevo_nombre' => $datos['nombre_comercial']]);
        } else {
            throw new Exception("Error interno al registrar.");
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}

if ($action === 'cancelarCompra') {
    if (ob_get_level()) ob_end_clean(); 
    header('Content-Type: application/json; charset=utf-8');
    try {
        if (!isset($_SESSION['usuario_id'])) throw new Exception("Sesión expirada.");
        $id_compra = intval($_POST['id'] ?? 0);
        $id_usuario = $_SESSION['usuario_id'];
        if ($id_compra <= 0) throw new Exception("ID de compra inválido.");
        $resultado = $comprasModel->cancelarCompra($id_compra, $id_usuario);
        echo json_encode($resultado);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}

if ($action === 'cancelarGasto') {
    if (ob_get_level()) ob_end_clean(); 
    header('Content-Type: application/json; charset=utf-8');
    try {
        if (!isset($_SESSION['usuario_id'])) throw new Exception("Sesión expirada.");
        $id_gasto = intval($_POST['id'] ?? 0);
        $id_usuario = $_SESSION['usuario_id'];
        $razon = trim($_POST['razon'] ?? '');
        if ($id_gasto <= 0) throw new Exception("ID de gasto inválido.");
        if (empty($razon)) throw new Exception("Es obligatorio proporcionar una razón.");
        $resultado = $gastosModel->cancelarGastoConRazon($id_gasto, $id_usuario, $razon);
        echo json_encode($resultado);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}

// =========================================================================
// --- CARGA DE VISTA (GET) ---
// =========================================================================

if ($_SERVER['REQUEST_METHOD'] === 'GET' && empty($action)) {
    $fecha_desde = $_GET['desde'] ?? date('Y-m-01');
    $fecha_hasta = $_GET['hasta'] ?? date('Y-m-d');
    $tipo_filtro = $_GET['tipo_filtro'] ?? 'todos'; 

    $rol_id = $_SESSION['rol_id'] ?? 0;
    $mi_almacen_id = $_SESSION['almacen_id'] ?? 0;
    $almacen_a_consultar = ($rol_id == 1) ? (isset($_GET['almacen_filtro']) ? intval($_GET['almacen_filtro']) : 0) : $mi_almacen_id;

    $egresos = $egresoModel->obtenerTodosLosEgresos($fecha_desde, $fecha_hasta, $almacen_a_consultar, $tipo_filtro);

    $totalSumCompras = 0;
    $totalSumGastos = 0;
    foreach ($egresos as $e) {
        if ($e['tipo'] == 'compra') $totalSumCompras += $e['total'];
        else $totalSumGastos += $e['total'];
    }
    
    $granTotalEgresos = $totalSumCompras + $totalSumGastos;
    $almacenes = $egresoModel->obtenerAlmacenesActivos();
    $productos = $comprasModel->obtenerProductos(); 
    $proveedores = $proveedorModel->listarTodos(); 

    $tituloPagina = "Gestión de Egresos";
    require_once __DIR__ . '/../views/egresos_view.php';
    exit;
}