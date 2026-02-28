<?php
// 1. Establecer el encabezado JSON antes de cualquier salida
header('Content-Type: application/json; charset=utf-8');

// 2. Incluir la conexión (ajusta la ruta si es necesario)
// Si tu archivo está en /app/backend/compras/obtener_pendientes.php 
// y tu config está en /config/conexion.php, la ruta sería:
require_once __DIR__ . '/../../../config/conexion.php';

// Opcional: Validar sesión si tienes el archivo auth.php
// require_once __DIR__ . '/../../../includes/auth.php';

try {
    // 3. Validar que se recibió el ID
    if (!isset($_GET['id']) || empty($_GET['id'])) {
        throw new Exception("ID de compra no proporcionado");
    }

    $compra_id = intval($_GET['id']);

    // 4. Consulta SQL para traer solo los productos que tienen faltantes
    // Unimos con la tabla productos para mostrar el nombre y el SKU en el modal
    $sql = "SELECT 
                d.id, 
                d.producto_id, 
                d.cantidad_faltante, 
                p.nombre, 
                p.sku 
            FROM detalle_compra d 
            INNER JOIN productos p ON d.producto_id = p.id 
            WHERE d.compra_id = ? AND d.cantidad_faltante > 0";

    $stmt = $conexion->prepare($sql);
    
    if (!$stmt) {
        throw new Exception("Error en la preparación de la consulta: " . $conexion->error);
    }

    $stmt->bind_param("i", $compra_id);
    $stmt->execute();
    $resultado = $stmt->get_result();

    $pendientes = [];
    while ($row = $resultado->fetch_assoc()) {
        // Limpiar datos para evitar errores de caracteres especiales en JSON
        $pendientes[] = [
            "id" => $row['id'],
            "producto_id" => $row['producto_id'],
            "cantidad_faltante" => floatval($row['cantidad_faltante']),
            "nombre" => htmlspecialchars($row['nombre']),
            "sku" => htmlspecialchars($row['sku'])
        ];
    }

    // 5. Retornar el array (aunque esté vacío)
    echo json_encode($pendientes);

} catch (Exception $e) {
    // En caso de error, enviar un código de error HTTP y el mensaje
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage()
    ]);
}