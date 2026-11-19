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
$almacen_usuario = $_SESSION['rol_id'] == 3 ? 0 : $_SESSION['almacen_id'] ?? 0;

$almacenModel = new AlmacenModel($conexion);
// Obtenemos los almacenes para la vista
$almacenes = $almacenModel->getAlmacenes($almacen_usuario);

/**
 * Helper para obtener rango de fechas
 * CORRECCIÓN: Ahora retorna [inicio, fin] correctamente
 */
function obtenerFechas()
{

    $f_inicio = !empty($_GET['f_inicio'])
        ? $_GET['f_inicio'] . ' 00:00:00'
        : null;

    $f_fin = !empty($_GET['f_fin'])
        ? date('Y-m-d', strtotime($_GET['f_fin'] . ' +1 day')) . ' 00:00:00'
        : null;
    // CORREGIDO: Antes era [$f_fin, $f_fin]
    return [$f_inicio, $f_fin];
}

// =====================================================
// 🔥 ACCIÓN: OBTENER LOTES
// =====================================================
if (isset($_GET['action']) && $_GET['action'] === 'obtenerLotes') {
    if (ob_get_level())
        ob_clean();
    header('Content-Type: application/json');

    try {
        $producto_id = intval($_GET['producto_id'] ?? 0);
        $almacen_id = intval($_GET['almacen_id'] ?? $almacen_usuario);

        if ($almacen_usuario != 0) {
            $almacen_id = $almacen_usuario;
        }

        list($fecha_inicio, $fecha_fin) = obtenerFechas();

        if ($producto_id <= 0) {
            throw new Exception("Producto inválido.");
        }

        $data = $model->obtenerLotes($producto_id, $almacen_id, $fecha_inicio, $fecha_fin);
        $suma = $model->obtenerTotalesLotes($producto_id, $almacen_id, $fecha_inicio, $fecha_fin);

        echo json_encode([
            'success' => true,
            'data' => $data,
            'totales' => $suma
        ]);

    } catch (Throwable $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}

// =====================================================
// 🔁 TRASPASOS
// =====================================================
if (isset($_GET['action']) && $_GET['action'] === 'obtenerTraspasos') {
    if (ob_get_level())
        ob_clean();
    header('Content-Type: application/json');

    try {
        $lote_id = intval($_GET['lote_id'] ?? 0);

        list($fecha_inicio, $fecha_fin) = obtenerFechas();

        $data = $model->obtenerTraspasos($lote_id, $fecha_inicio, $fecha_fin);

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
// 📦 CONSUMO DE LOTES (Kárdex extendido)
// =====================================================
if (isset($_GET['action']) && $_GET['action'] === 'obtenerConsumoLotes') {
    if (ob_get_level())
        ob_clean();
    header('Content-Type: application/json');

    try {
        $producto_id = intval($_GET['producto_id'] ?? 0);
        $almacen_id = intval($_GET['almacen_id'] ?? 0);

        // Si no vienen fechas en el GET, obtenerFechas() dará el rango por defecto
        list($fecha_inicio, $fecha_fin) = obtenerFechas();

        // Sobrescribir si vienen específicamente por parámetros directos
        if (!empty($_GET['fecha_inicio']))
            $fecha_inicio = $_GET['fecha_inicio'];
        if (!empty($_GET['fecha_fin']))
            $fecha_fin = $_GET['fecha_fin'];

        if ($producto_id <= 0) {
            throw new Exception("Producto inválido.");
        }

        $data = $model->obtenerConsumoLotesPorProducto(
            $producto_id,
            $almacen_id,
            $fecha_inicio,
            $fecha_fin
        );

        echo json_encode(['success' => true, 'data' => $data]);

    } catch (Throwable $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}
if (isset($_GET['action']) && $_GET['action'] === 'obtenerLotesEInventario') {
    if (ob_get_level())
        ob_clean();
    header('Content-Type: application/json');

    try {
        $producto_id = intval($_GET['producto_id'] ?? 0);
        $almacen_id = intval($_GET['almacen_id'] ?? 0);
        $fecha_inicio = trim($_GET['fecha_inicio'] ?? $_GET['inicio'] ?? '');
        $fecha_fin = trim($_GET['fecha_fin'] ?? $_GET['fin'] ?? '');

        if ($producto_id <= 0) {
            throw new Exception("Producto inválido.");
        }
        if ($almacen_id <= 0) {
            throw new Exception("Almacén inválido.");
        }

        $data = $model->obtenerLotesEInventario($almacen_id, $producto_id, $fecha_inicio, $fecha_fin);

        echo json_encode(['success' => true, 'data' => $data]);

    } catch (Throwable $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}

if (isset($_GET['action']) && $_GET['action'] === 'actualizarLotesEInventario') {
    if (ob_get_level()) {
        ob_clean();
    }
    header('Content-Type: application/json');

    try {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            throw new Exception("Método no permitido.");
        }

        // Acepta JSON puro o form-data
        $raw = file_get_contents('php://input');
        $body = json_decode($raw, true);

        if (json_last_error() !== JSON_ERROR_NONE || !is_array($body)) {
            $body = $_POST;
        }

        $almacen_id = isset($body['almacen_id']) ? (int) $body['almacen_id'] : 0;
        $producto_id = isset($body['producto_id']) ? (int) $body['producto_id'] : 0;
        $lotes = $body['lotes'] ?? null;
        $inventario = $body['inventario'] ?? null;

        if ($almacen_id <= 0) {
            throw new Exception("Almacén inválido.");
        }
        if ($producto_id <= 0) {
            throw new Exception("Producto inválido.");
        }
        if (!is_array($lotes)) {
            throw new Exception("El campo 'lotes' es obligatorio y debe ser un array.");
        }

        // Validar estructura mínima de cada lote: id, cantidad_actual, estado_lote
        foreach ($lotes as $i => $lote) {
            if (!is_array($lote)) {
                throw new Exception("El elemento en lotes[{$i}] debe ser un objeto.");
            }
            if (!isset($lote['id']) || (int) $lote['id'] <= 0) {
                throw new Exception("El lote en lotes[{$i}] requiere un 'id' válido.");
            }
            if (!isset($lote['cantidad_actual']) || !is_numeric($lote['cantidad_actual'])) {
                throw new Exception("El lote en lotes[{$i}] requiere 'cantidad_actual' numérico.");
            }
            $estado = $lote['estado_lote'] ?? null;
            if (!in_array($estado, ['activo', 'agotado'], true)) {
                throw new Exception("El lote en lotes[{$i}] requiere 'estado_lote' ('activo' o 'agotado').");
            }
        }

        // Inventario debe ser un número (ej. 8) o null
        if ($inventario !== null && !is_numeric($inventario)) {
            throw new Exception("El campo 'inventario' debe ser un número si se envía.");
        }
        $inventario = ($inventario !== null) ? (float) $inventario : null;

        $reporte = $model->actualizarLotesEInventario(
            $almacen_id,
            $producto_id,
            $lotes,
            $inventario
        );

        if (!$reporte['ok']) {
            throw new Exception($reporte['error'] ?? 'No se pudo actualizar.');
        }

        echo json_encode([
            'success' => true,
            'data' => $reporte,
            'message' => 'Lotes e inventario actualizados correctamente.'
        ]);
    } catch (Throwable $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}
// =====================================================
// 🔄 VENTAS POR LOTE (Individual)
// =====================================================
if (isset($_GET['action']) && $_GET['action'] === 'obtenerVentasLote') {
    if (ob_get_level())
        ob_clean();
    header('Content-Type: application/json');

    try {
        $lote_id = intval($_GET['lote_id'] ?? 0);
        if ($lote_id <= 0)
            throw new Exception("Lote inválido.");

        $data = $model->obtenerVentasLote($lote_id);
        echo json_encode(['success' => true, 'data' => $data]);
    } catch (Throwable $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}

// =====================================================
// 📦 PRODUCTOS
// =====================================================
if (isset($_GET['action']) && $_GET['action'] === 'productos') {
    if (ob_get_level())
        ob_clean();
    header('Content-Type: application/json');

    $almacen_id = intval($_GET['almacen_id'] ?? 0);
    $data = $productosModel->listarProductosTodos($almacen_id);

    echo json_encode(['success' => true, 'data' => $data]);
    exit;
}

// =====================================================
// 🖥️ CARGA DE VISTA
// =====================================================
if ($_SERVER['REQUEST_METHOD'] === 'GET' && !isset($_GET['action'])) {
    try {
        $tituloPagina = "historialLotes";
        // Aseguramos que la variable coincida con lo que la vista espera
        $listaAlmacenes = $almacenes;
        $productos = $productosModel->listarProductosConStock($almacen_usuario);

        require_once __DIR__ . '/../views/editar_inventario_view.php';
    } catch (Exception $e) {
        die("Error al cargar la vista: " . $e->getMessage());
    }
}