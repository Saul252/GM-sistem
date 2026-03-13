<?php
// 🔧 SESSION_START SIEMPRE PRIMERO
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

ob_start();

require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../models/mermasModel.php';
require_once __DIR__ . '/../models/almacen_model.php';
protegerPagina(); 
// ❌ QUITAR: LayoutController no se necesita aquí
 require_once __DIR__ . '/../controllers/LayoutController.php';

// Verificar sesión ANTES de instanciar modelos
if (!isset($_SESSION['id']) && !isset($_SESSION['usuario_id'])) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => '❌ Sesión requerida']);
        exit;
    }
    // Para GET redirigir
    header('Location: /cfsistem/login.php');
    exit;
}
$paginaActual = 'Mermas';
$mermasModel = new MermasModel($conexion);
$almacenModel = new AlmacenModel($conexion);

// ============================
// 🔍 AJAX: OBTENER PRODUCTOS
// ============================
if (isset($_GET['action']) && $_GET['action'] === 'obtenerProductosAlmacen') {
    ob_clean();
    header('Content-Type: application/json');
    $almacen_id = intval($_GET['almacen_id'] ?? 0);
    $productos = $almacenModel->getInventarioConId($almacen_id);
    echo json_encode($productos ?: []);
    exit;
}

// ============================
// 🔍 AJAX: OBTENER LOTES
// ============================
if (isset($_GET['action']) && $_GET['action'] === 'obtenerLotes') {
    ob_clean();
    header('Content-Type: application/json');
    $producto_id = intval($_GET['producto_id'] ?? 0);
    $almacen_id = intval($_GET['almacen_id'] ?? 0);
    $lotes = ($producto_id > 0 && $almacen_id > 0) 
        ? $mermasModel->getLotesPorProducto($almacen_id, $producto_id) 
        : [];
    echo json_encode($lotes);
    exit;
}

// ============================
// 💾 POST: GUARDAR MERMA (MEJORADO)
// ============================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action']) && $_GET['action'] === 'guardarMerma') {
    ob_clean();
    header('Content-Type: application/json');
    
    try {
        $usuario_id = $_SESSION['id'] ?? $_SESSION['usuario_id'];
        $responsable = $_SESSION['nombre'] ?? 'Usuario #' . $usuario_id;

        // 🔍 VALIDACIÓN DETALLADA
        $producto_id = intval($_POST['producto_id'] ?? 0);
        $almacen_id = intval($_POST['almacen_id'] ?? 0);
        $lote_id = intval($_POST['lote_id'] ?? 0);
        $cantidad = floatval($_POST['cantidad'] ?? 0);
        $tipo_merma = trim($_POST['tipo_merma'] ?? 'otro');
        $motivo = trim($_POST['observaciones'] ?? '');

        // Validaciones específicas
        if ($producto_id <= 0) throw new Exception("Producto inválido (ID: $producto_id)");
        if ($almacen_id <= 0) throw new Exception("Almacén inválido (ID: $almacen_id)");
        if ($lote_id <= 0) throw new Exception("Lote inválido (ID: $lote_id)");
        if ($cantidad <= 0) throw new Exception("Cantidad inválida ($cantidad)");
        if (!in_array($tipo_merma, ['daño','robo','caducidad','otro'])) {
            throw new Exception("Tipo de merma inválido: $tipo_merma");
        }

        // 🔍 VERIFICAR STOCK SUFICIENTE
        $stmt = $conexion->prepare("SELECT cantidad_actual FROM lotes_stock WHERE id = ?");
        $stmt->bind_param("i", $lote_id);
        $stmt->execute();
        $resultado = $stmt->get_result()->fetch_assoc();
        
        if (!$resultado) throw new Exception("Lote no encontrado (ID: $lote_id)");
        if ($cantidad > $resultado['cantidad_actual']) {
            throw new Exception("Stock insuficiente. Disponible: " . $resultado['cantidad_actual']);
        }

        $datos = [
            'producto_id' => $producto_id,
            'almacen_id' => $almacen_id,
            'lote_id' => $lote_id,
            'cantidad' => $cantidad,
            'tipo_merma' => $tipo_merma,
            'motivo' => $motivo,
            'usuario_id' => $usuario_id,
            'responsable' => $responsable
        ];

        $resultado = $mermasModel->registrarMerma($datos);

        if ($resultado === true) {
            echo json_encode([
                'status' => 'success', 
                'message' => '✅ Merma registrada correctamente'
            ]);
        } else {
            throw new Exception("Error en modelo: " . $resultado);
        }
        
    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode([
            'status' => 'error', 
            'message' => $e->getMessage()
        ]);
    }
    exit;
}

// ============================
// 📄 VISTA PRINCIPAL (GET)
// ============================
try {
    // 1. Identificar Almacén (0 si es admin)
    $almacen_sesion = $_SESSION['almacen_id'] ?? 0;
    
    // 2. Lógica de Paginación
    $limit = 15; 
    $pagina = isset($_GET['p']) ? max(1, intval($_GET['p'])) : 1;
    $offset = ($pagina - 1) * $limit;

    // 3. Obtener Datos
    $almacenes = $almacenModel->getAlmacenes($almacen_sesion);
    
    // Para la tabla, usamos el almacen_sesion para filtrar (Admin ve todo si es 0)
    $mermas = $mermasModel->obtenerMermasPaginadas($almacen_sesion, $limit, $offset);
    $totalMermas = $mermasModel->contarTotalMermas($almacen_sesion);
    $totalPaginas = ceil($totalMermas / $limit);

    include __DIR__ . '/../views/mermas_view.php';
} catch (Exception $e) {
    die("Error crítico en el sistema de mermas: " . $e->getMessage());
}
?>