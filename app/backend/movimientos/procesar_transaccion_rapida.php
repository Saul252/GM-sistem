<?php
ob_start();
ini_set('display_errors', 0); 
error_reporting(E_ALL);
header('Content-Type: application/json');

require_once $_SERVER['DOCUMENT_ROOT'] . '/cfsistem/config/conexion.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Recibir ID desde el Dropdown o Modal
$movimiento_id = $_POST['id'] ?? null;
$usuario_id = $_SESSION['usuario_id'] ?? null;
$rol_id = $_SESSION['rol_id'] ?? null;

if (!$movimiento_id || !$usuario_id) {
    ob_end_clean();
    echo json_encode(['success' => false, 'error' => 'Sesión expirada o ID inválido']);
    exit;
}

$conexion->begin_transaction();

try {
    // 1. Obtener datos del movimiento y bloquear fila para evitar doble click
    $stmt = $conexion->prepare("SELECT producto_id, cantidad, almacen_origen_id, almacen_destino_id, usuario_recibe_id 
                                FROM movimientos WHERE id = ? FOR UPDATE");
    $stmt->bind_param("i", $movimiento_id);
    $stmt->execute();
    $mov = $stmt->get_result()->fetch_assoc();

    if (!$mov) throw new Exception("El movimiento no existe.");
    if ($mov['usuario_recibe_id'] !== null) throw new Exception("Este traspaso ya fue recibido.");

    $p_id = $mov['producto_id'];
    $dest_id = $mov['almacen_destino_id'];
    $orig_id = $mov['almacen_origen_id'];
    $cantidad = $mov['cantidad'];

    // 2. Lógica de Inventario (Suma stock en el destino)
    $stmtInv = $conexion->prepare("INSERT INTO inventario (almacen_id, producto_id, stock, stock_minimo, stock_maximo) 
                                   VALUES (?, ?, ?, 0, 0) 
                                   ON DUPLICATE KEY UPDATE stock = stock + ?");
    $stmtInv->bind_param("iidd", $dest_id, $p_id, $cantidad, $cantidad);
    $stmtInv->execute();

    // 3. Lógica de Precios (Si el producto no existe en el destino, copia los del origen)
    $checkPrecios = $conexion->prepare("SELECT id FROM precios_producto WHERE producto_id = ? AND almacen_id = ?");
    $checkPrecios->bind_param("ii", $p_id, $dest_id);
    $checkPrecios->execute();
    
    if ($checkPrecios->get_result()->num_rows === 0) {
        $copyPrecios = $conexion->prepare("INSERT INTO precios_producto (producto_id, almacen_id, precio_minorista, precio_mayorista, precio_distribuidor)
                                           SELECT producto_id, ?, precio_minorista, precio_mayorista, precio_distribuidor 
                                           FROM precios_producto WHERE producto_id = ? AND almacen_id = ? LIMIT 1");
        $copyPrecios->bind_param("iii", $dest_id, $p_id, $orig_id);
        $copyPrecios->execute();
    }

    // 4. Actualizar Movimiento (Cerramos el traspaso)
    // Si es Admin (rol 1), también llenamos el campo de usuario_autoriza_id
    $col_autoriza = ($rol_id == 1) ? ", usuario_autoriza_id = ?" : "";
    $sqlFinal = "UPDATE movimientos SET usuario_recibe_id = ?, fecha = CURRENT_TIMESTAMP $col_autoriza WHERE id = ?";
    
    $stmtFinal = $conexion->prepare($sqlFinal);
    if ($rol_id == 1) {
        $stmtFinal->bind_param("iii", $usuario_id, $usuario_id, $movimiento_id);
    } else {
        $stmtFinal->bind_param("ii", $usuario_id, $movimiento_id);
    }
    $stmtFinal->execute();

    $conexion->commit();
    ob_end_clean();
    echo json_encode(['success' => true, 'message' => 'Stock actualizado y recibido']);

} catch (Exception $e) {
    $conexion->rollback();
    ob_end_clean();
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}