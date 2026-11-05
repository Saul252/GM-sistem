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
    // 1. Buscar el movimiento y bloquearlo
    $stmt = $conexion->prepare("
        SELECT id, producto_id, cantidad, almacen_origen_id, tipo, usuario_recibe_id
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

    // 2. Devolver el stock al almacén de origen
    $stmtStock = $conexion->prepare("
        UPDATE inventario
        SET stock = stock + ?
        WHERE producto_id = ? AND almacen_id = ?
    ");
    $stmtStock->bind_param("dii", $mov['cantidad'], $mov['producto_id'], $mov['almacen_origen_id']);
    $stmtStock->execute();

    // 3. Eliminar el movimiento
    $stmtDel = $conexion->prepare("DELETE FROM movimientos WHERE id = ?");
    $stmtDel->bind_param("i", $movimiento_id);
    $stmtDel->execute();

    $conexion->commit();

    echo json_encode([
        'status' => 'success',
        'message' => 'Traspaso eliminado. Stock devuelto al almacén de origen.'
    ]);

} catch (Exception $e) {
    $conexion->rollback();
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}