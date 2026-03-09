<?php
/**
 * egresosController.php
 * Controlador para la gestión unificada de Egresos (Compras y Gastos)
 */

require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../controllers/LayoutController.php';
require_once __DIR__ . '/../models/egresos_model.php';
require_once __DIR__ . '/../models/egresos/comprasModel.php';

require_once __DIR__ . '/../models/almacen/categoriasModel.php';

$egresoModel = new EgresoModel($conexion);
$comprasModel = new CompraModel($conexion);
$categoriasModel= new CategoriaModel($conexion);
$paginaActual = 'compras';
if (isset($_GET['action']) && $_GET['action'] === 'guardarCompraInventario') {
    if (ob_get_length()) ob_clean(); 
    header('Content-Type: application/json');
    
    try {
        // 1. Identificar al usuario y su almacén
        $user_id = $_SESSION['user_id'] ?? 1;
        $rol_id  = $_SESSION['rol_id'] ?? 0; // Asumiendo que 1 es Administrador

        // 2. Lógica de Almacén de Cargo (Cabecera)
        // Si es Admin (rol 1), toma el del select. Si no, toma el de su sesión.
        if ($rol_id == 1 && isset($_POST['almacen_id_cabecera'])) {
            $almacen_principal = intval($_POST['almacen_id_cabecera']);
        } else {
            $almacen_principal = $_SESSION['almacen_id'] ?? null;
        }

        if (!$almacen_principal) {
            throw new Exception("No se pudo determinar el almacén de cargo para esta operación.");
        }

        // 3. Recoger datos restantes
        $items = $_POST['items'] ?? [];
        $folio = $_POST['folio'] ?? 'S/F';
        $proveedor = $_POST['proveedor'] ?? 'Sin Proveedor';
        
        $evidencia = (isset($_FILES['evidencia_compra']) && $_FILES['evidencia_compra']['error'] === UPLOAD_ERR_OK) 
                     ? $_FILES['evidencia_compra'] 
                     : null;

        if (empty($items)) {
            throw new Exception("No se recibieron datos de productos.");
        }

        // 4. Llamada al modelo enviando el Almacén de Cabecera validado
        $resultado = $comprasModel->guardarCompraCompleta(
            $items, 
            $folio, 
            $proveedor,
            $evidencia,
            $almacen_principal, // Nuevo parámetro para el modelo
            $user_id            // Pasamos el ID del usuario logueado
        );

        echo json_encode($resultado ?? ['success' => false, 'message' => 'El modelo no respondió']);

    } catch (Throwable $e) {
        echo json_encode([
            'success' => false, 
            'message' => 'Fallo en el Sistema: ' . $e->getMessage()
        ]);
    }
    exit;
}
// --- ACCIÓN: GUARDAR GASTO (AJAX) ---
if (isset($_GET['action']) && $_GET['action'] === 'guardarGasto') {
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
            'usuario_id'   => $_SESSION['user_id'] ?? 1,
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

// --- ACCIÓN: BUSCAR PRODUCTOS (AJAX para Select2) ---
if (isset($_GET['action']) && $_GET['action'] === 'buscarProductos') {
    header('Content-Type: application/json');
    $termino = $_GET['q'] ?? '';
    $productos = $comprasModel->obtenerProductos($termino);
    echo json_encode($productos);
    exit;
}
// --- CARGA DE VISTA (GET) ---
// --- CARGA DE VISTA (GET) ---
if ($_SERVER['REQUEST_METHOD'] === 'GET' && !isset($_GET['action'])) {
    $fecha_desde = $_GET['desde'] ?? date('Y-m-01');
    $fecha_hasta = $_GET['hasta'] ?? date('Y-m-d');

    // 1. OBTENEMOS DATOS DE SESIÓN (Basado en tu validar.php)
    $rol_id = $_SESSION['rol_id'] ?? 0;
    $mi_almacen_id = $_SESSION['almacen_id'] ?? 0;

    // 2. LÓGICA DE SEGURIDAD PARA EL FILTRO
    // Si es Administrador (Rol 1), puede usar el filtro del select.
    // Si no es Admin, ignoramos lo que venga por URL y le clavamos su almacén de sesión.
    if ($rol_id == 1) {
        $almacen_a_consultar = isset($_GET['almacen_filtro']) ? intval($_GET['almacen_filtro']) : 0;
    } else {
        $almacen_a_consultar = $mi_almacen_id;
    }

    // 3. PASAMOS EL FILTRO AL MODELO (Debes actualizar el modelo para recibir este 3er parámetro)
    $egresos = $egresoModel->obtenerTodosLosEgresos($fecha_desde, $fecha_hasta, $almacen_a_consultar);

    $totalSumCompras = 0;
    $totalSumGastos = 0;
    foreach ($egresos as $e) {
        if ($e['tipo'] == 'compra') $totalSumCompras += $e['total'];
        else $totalSumGastos += $e['total'];
    }
    
    $granTotalEgresos = $totalSumCompras + $totalSumGastos;
    
    // 1. Cargamos Almacenes para el select (El Admin los verá todos)
    $almacenes = $egresoModel->obtenerAlmacenesActivos();

    // 2. Cargamos los productos
    $productos = $comprasModel->obtenerProductos(); 

    $tituloPagina = "Gestión de Egresos";
    require_once __DIR__ . '/../views/egresos_view.php';
}
// --- ACCIÓN: OBTENER FALTANTES PARA EL MODAL (AJAX) ---
if (isset($_GET['action']) && $_GET['action'] === 'obtenerFaltantes') {
    header('Content-Type: application/json');
    $compra_id = intval($_GET['compra_id'] ?? 0);
    // Llamamos al modelo (asegúrate de agregar esta función en comprasModel.php)
    $faltantes = $comprasModel->obtenerDetalleFaltantes($compra_id);
    echo json_encode($faltantes);
    exit;
}

// --- ACCIÓN: PROCESAR AJUSTE DE FALTANTES (AJAX) ---
if (isset($_GET['action']) && $_GET['action'] === 'procesarAjusteFaltante') {
    header('Content-Type: application/json');
    try {
        $compra_id    = intval($_POST['compra_id'] ?? 0);
        $distribucion = $_POST['distribucion'] ?? []; // Array [prod_id][alm_id] => cant
        $user_id      = $_SESSION['user_id'] ?? 1;

        if ($compra_id <= 0 || empty($distribucion)) {
            throw new Exception("No se recibieron datos de distribución válidos.");
        }

        // Llamamos al modelo pasando la matriz de distribución
        $res = $comprasModel->procesarAjusteFaltante($compra_id, $distribucion, $user_id);
        echo json_encode($res);

    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}
// --- ACCIÓN: OBTENER DETALLE PARA TICKET (AJAX) ---
if (isset($_GET['action']) && $_GET['action'] === 'obtenerDetalleMovimiento') {
    while (ob_get_level()) ob_end_clean(); 
    header('Content-Type: application/json');
    
    $tipo = $_GET['tipo'] ?? '';
    $id = intval($_GET['id'] ?? 0);

    try {
        // Llamamos a la nueva función del modelo
        $resultado = $egresoModel->obtenerDetalleCompleto($tipo, $id);

        if ($resultado && $resultado['cabecera']) {
            echo json_encode([
                'success' => true, 
                'cabecera' => $resultado['cabecera'], 
                'items' => $resultado['items']
            ]);
        } else {
            echo json_encode([
                'success' => false, 
                'message' => 'No se encontró el registro en la base de datos.'
            ]);
        }
    } catch (Exception $e) {
        echo json_encode([
            'success' => false, 
            'message' => $e->getMessage()
        ]);
    }
    exit;
}