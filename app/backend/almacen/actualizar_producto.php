<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/cfsistem/config/conexion.php';
header('Content-Type: application/json');

$id = $_POST['producto_id'];
$alm_id = $_POST['almacen_actual_id'];
$aplicar_global = isset($_POST['aplicar_global']); // Checkbox

$conexion->begin_transaction();

try {
    // 1. Actualizar Datos Globales (Siempre afecta a todos los almacenes)
    $stmt1 = $conexion->prepare("UPDATE productos SET sku = ?, nombre = ?, categoria_id = ? WHERE id = ?");
    $stmt1->bind_param("ssii", $_POST['sku'], $_POST['nombre'], $_POST['categoria_id'], $id);
    $stmt1->execute();

    // 2. Actualizar Precios
    if ($aplicar_global) {
        // Afectar a todos los almacenes donde existe el producto
        $stmt2 = $conexion->prepare("UPDATE precios_producto SET precio_minorista = ?, precio_mayorista = ?, precio_distribuidor = ? WHERE producto_id = ?");
        $stmt2->bind_param("dddi", $_POST['precio_minorista'], $_POST['precio_mayorista'], $_POST['precio_distribuidor'], $id);
    } else {
        // Solo al almacén actual
        $stmt2 = $conexion->prepare("UPDATE precios_producto SET precio_minorista = ?, precio_mayorista = ?, precio_distribuidor = ? WHERE producto_id = ? AND almacen_id = ?");
        $stmt2->bind_param("dddii", $_POST['precio_minorista'], $_POST['precio_mayorista'], $_POST['precio_distribuidor'], $id, $alm_id);
    }
    $stmt2->execute();

    // 3. Actualizar Stock Mínimo (Específico del almacén)
    $stmt3 = $conexion->prepare("UPDATE inventario SET stock_minimo = ? WHERE producto_id = ? AND almacen_id = ?");
    $stmt3->bind_param("dii", $_POST['stock_minimo'], $id, $alm_id);
    $stmt3->execute();

    $conexion->commit();
    echo json_encode(['status' => 'success', 'message' => 'Producto actualizado correctamente']);

} catch (Exception $e) {
    $conexion->rollback();
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}