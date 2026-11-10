<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$base_path = $_SERVER['DOCUMENT_ROOT'] . '/cfsistem';
require_once $base_path . '/includes/auth.php';
require_once $base_path . '/config/conexion.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Método no permitido.']);
    exit;
}

$movimiento_id = intval($_POST['movimiento_id'] ?? 0);

if ($movimiento_id <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'ID de movimiento inválido.']);
    exit;
}

$conexion->begin_transaction();

try {
    // 1. Buscar el movimiento, la cantidad, el lote origen (en referencia_id) y bloquearlo
    $stmt = $conexion->prepare("
        SELECT id, producto_id, cantidad, almacen_origen_id, tipo, usuario_recibe_id, referencia_id
        FROM movimientos
        WHERE id = ?
        FOR UPDATE
    ");
    $stmt->bind_param("i", $movimiento_id);
    $stmt->execute();
    $mov = $stmt->get_result()->fetch_assoc();

    if (!$mov) {
        throw new Exception("El movimiento no existe.");
    }
    if ($mov['tipo'] !== 'traspaso') {
        throw new Exception("Solo se pueden eliminar movimientos de tipo traspaso.");
    }
    if (!empty($mov['usuario_recibe_id'])) {
        throw new Exception("No se puede eliminar: el traspaso ya fue recibido en destino.");
    }

    // 2. Devolver el stock global al almacén de origen en la tabla inventario
    $stmtStock = $conexion->prepare("
        UPDATE inventario
        SET stock = stock + ?
        WHERE producto_id = ? AND almacen_id = ?
    ");
    $stmtStock->bind_param("dii", $mov['cantidad'], $mov['producto_id'], $mov['almacen_origen_id']);
    $stmtStock->execute();

    // 3. Devolver la cantidad al lote específico usando referencia_id
    $lote_id = intval($mov['referencia_id'] ?? 0);
    $cantidad_a_regresar = (float) $mov['cantidad'];

    if ($lote_id > 0 && $cantidad_a_regresar > 0) {
        // Consultar el lote para asegurar que existe y calcular su nuevo estado
        $stmtLoteCheck = $conexion->prepare("SELECT cantidad_actual FROM lotes_stock WHERE id = ? FOR UPDATE");
        $stmtLoteCheck->bind_param("i", $lote_id);
        $stmtLoteCheck->execute();
        $loteInfo = $stmtLoteCheck->get_result()->fetch_assoc();

        if ($loteInfo) {
            $nuevo_stock_lote = (float) $loteInfo['cantidad_actual'] + $cantidad_a_regresar;
            // Si vuelve a tener stock, pasa a activo (o se mantiene activo)
            $nuevo_estado = ($nuevo_stock_lote > 0) ? 'activo' : 'agotado';

            $stmtUpLote = $conexion->prepare("
                UPDATE lotes_stock 
                SET cantidad_actual = ?, estado_lote = ? 
                WHERE id = ?
            ");
            $stmtUpLote->bind_param("dsi", $nuevo_stock_lote, $nuevo_estado, $lote_id);
            $stmtUpLote->execute();
        }
    }

    // 4. Eliminar el movimiento principal
    $stmtDel = $conexion->prepare("DELETE FROM movimientos WHERE id = ?");
    $stmtDel->bind_param("i", $movimiento_id);
    $stmtDel->execute();

    $conexion->commit();

    echo json_encode([
        'status' => 'success',
        'message' => 'Traspaso cancelado. Stock general e inventario del lote devueltos al origen correctamente.'
    ]);

} catch (Exception $e) {
    $conexion->rollback();
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}