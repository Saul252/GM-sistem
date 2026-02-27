<?php
// Limpiar cualquier salida previa
ob_start();

// Configuración de errores para desarrollo (No mostrar a pantalla)
ini_set('display_errors', 0);
error_reporting(E_ALL);

header('Content-Type: application/json');
require_once $_SERVER['DOCUMENT_ROOT'] . '/cfsistem/config/conexion.php';

$id = $_GET['id'] ?? null;
$alm_id = $_GET['almacen_id'] ?? null;

$response = ['status' => 'error', 'message' => 'Error desconocido'];

if (!$id || !$alm_id) {
    $response['message'] = 'Faltan parámetros (ID: ' . $id . ', Almacén: ' . $alm_id . ')';
} else {
    try {
        $sql = "SELECT 
                    p.id, p.sku, p.nombre, p.categoria_id, 
                    i.stock, i.stock_minimo, 
                    a.nombre as almacen_nombre,
                    pp.precio_minorista, pp.precio_mayorista, pp.precio_distribuidor
                FROM productos p
                INNER JOIN inventario i ON p.id = i.producto_id
                INNER JOIN almacenes a ON i.almacen_id = a.id
                LEFT JOIN precios_producto pp ON p.id = pp.producto_id AND pp.almacen_id = a.id
                WHERE p.id = ? AND i.almacen_id = ?
                LIMIT 1";

        $stmt = $conexion->prepare($sql);
        if (!$stmt) throw new Exception("Error en la consulta: " . $conexion->error);

        $stmt->bind_param("ii", $id, $alm_id);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $producto = $resultado->fetch_assoc();

        if ($producto) {
            $response = [
                'status' => 'success',
                'producto' => $producto
            ];
        } else {
            $response['message'] = 'No se encontró el producto en el almacén especificado.';
        }
    } catch (Exception $e) {
        $response['message'] = $e->getMessage();
    }
}

// Limpiar buffer y enviar JSON
ob_end_clean();
echo json_encode($response);
exit;