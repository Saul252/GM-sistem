<?php
/**
 * historialLotesController.php
 */

require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../controllers/LayoutController.php';
require_once __DIR__ . '/../models/lotesHistorialModel.php';
require_once __DIR__ . '/../models/almacen_model.php';
require_once __DIR__ . '/../models/productosModel.php';
protegerPagina('historialLotes');

$model = new HistorialLotesModel($conexion);
$productosModel = new ProductosModel($conexion);
$paginaActual = 'historialLotes';

// 🔥 almacén desde sesión
$almacen_usuario = $_SESSION['almacen_id'] ?? 0;

$almacenModel = new AlmacenModel($conexion);
$almacenes = $almacenModel->getAlmacenes($almacen_usuario);



// =====================================================
// 🔥 ACCIÓN: OBTENER LOTES
// =====================================================
if (isset($_GET['action']) && $_GET['action'] === 'obtenerLotes') {
    if (ob_get_level()) ob_clean();
    header('Content-Type: application/json');

    try {
        $producto_id = intval($_GET['producto_id'] ?? 0);
        $almacen_id  = intval($_GET['almacen_id'] ?? $almacen_usuario);

        // 🔥 si no es admin, forzamos su almacén
        if ($almacen_usuario != 0) {
            $almacen_id = $almacen_usuario;
        }

        $fecha_inicio = $_GET['fecha_inicio'] ?? '2026-01-01';
        $fecha_fin    = $_GET['fecha_fin'] ?? date('Y-m-d');

        if ($producto_id <= 0) {
            throw new Exception("Producto inválido.");
        }

        $data = $model->obtenerLotes($producto_id, $almacen_id, $fecha_inicio, $fecha_fin);
        $suma = $model->obtenerTotalesLotes($producto_id, $almacen_id, $fecha_inicio, $fecha_fin);

        echo json_encode([
            'success' => true,
            'data' => $data,
            'totales' =>$suma
        ]);

    } catch (Throwable $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}



// =====================================================
// 🧾 ACCIÓN: HISTORIAL COMPLETO DE UN LOTE
// =====================================================
if (isset($_GET['action']) && $_GET['action'] === 'obtenerHistorial') {
    if (ob_get_level()) ob_clean();
    header('Content-Type: application/json');

    try {
        $producto_id = intval($_GET['producto_id'] ?? 0);
        $lote_id     = intval($_GET['lote_id'] ?? 0);
        $almacen_id  = intval($_GET['almacen_id'] ?? $almacen_usuario);

        if ($almacen_usuario != 0) {
            $almacen_id = $almacen_usuario;
        }

        if ($producto_id <= 0 || $lote_id <= 0) {
            throw new Exception("Datos inválidos.");
        }

        $data = $model->obtenerHistorialCompleto($producto_id, $almacen_id, $lote_id);

        echo json_encode([
            'success' => true,
            'data' => $data
        ]);

    } catch (Throwable $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}



// =====================================================
// 🔄 ACCIÓN: SOLO VENTAS DE UN LOTE
// =====================================================
if (isset($_GET['action']) && $_GET['action'] === 'obtenerVentasLote') {
    if (ob_get_level()) ob_clean();
    header('Content-Type: application/json');

    try {
        $lote_id = intval($_GET['lote_id'] ?? 0);

        if ($lote_id <= 0) {
            throw new Exception("Lote inválido.");
        }

        $data = $model->obtenerVentasLote($lote_id);

        echo json_encode([
            'success' => true,
            'data' => $data
        ]);

    } catch (Throwable $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}



// =====================================================
// ⚙️ ACCIÓN: AJUSTES
// =====================================================
if (isset($_GET['action']) && $_GET['action'] === 'obtenerAjustes') {
    if (ob_get_level()) ob_clean();
    header('Content-Type: application/json');

    try {
        $producto_id = intval($_GET['producto_id'] ?? 0);
        $almacen_id  = intval($_GET['almacen_id'] ?? $almacen_usuario);

        if ($almacen_usuario != 0) {
            $almacen_id = $almacen_usuario;
        }

        $data = $model->obtenerAjustes($producto_id, $almacen_id);

        echo json_encode([
            'success' => true,
            'data' => $data
        ]);

    } catch (Throwable $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}



// =====================================================
// 🔁 ACCIÓN: TRASPASOS
// =====================================================
if (isset($_GET['action']) && $_GET['action'] === 'obtenerTraspasos') {
    if (ob_get_level()) ob_clean();
    header('Content-Type: application/json');

    try {
        $producto_id = intval($_GET['producto_id'] ?? 0);
        $almacen_id  = intval($_GET['almacen_id'] ?? $almacen_usuario);

        if ($almacen_usuario != 0) {
            $almacen_id = $almacen_usuario;
        }

        $data = $model->obtenerTraspasos($producto_id, $almacen_id);

        echo json_encode([
            'success' => true,
            'data' => $data
        ]);

    } catch (Throwable $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}



// =====================================================
// 📦 ACCIÓN: ENTRADAS
// =====================================================
if (isset($_GET['action']) && $_GET['action'] === 'obtenerEntradas') {
    if (ob_get_level()) ob_clean();
    header('Content-Type: application/json');

    try {
        $lote_id = intval($_GET['lote_id'] ?? 0);

        if ($lote_id <= 0) {
            throw new Exception("Lote inválido.");
        }

        $data = $model->obtenerEntradasLote($lote_id);

        echo json_encode([
            'success' => true,
            'data' => $data
        ]);

    } catch (Throwable $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}


if (isset($_GET['action']) && $_GET['action'] === 'productos') {

    if (ob_get_level()) ob_clean();
    header('Content-Type: application/json');

    $almacen_id = intval($_GET['almacen_id'] ?? 0);

    $data = $productosModel->listarProductosConStock($almacen_id);

    echo json_encode([
        'success' => true,
        'data' => $data
    ]);
    exit;
}
// =====================================================
// 🖥️ CARGA DE VISTA
// =====================================================
if ($_SERVER['REQUEST_METHOD'] === 'GET' && !isset($_GET['action'])) {
    try {

        $tituloPagina = "historialLotes";
        $listaAlmacenes = $almacenModel->getAlmacenes($almacen_sesion); 
       $productos =[];

        require_once __DIR__ . '/../views/historial_lotes_view.php';

    } catch (Exception $e) {
        die("Error al cargar la vista: " . $e->getMessage());
    }
}