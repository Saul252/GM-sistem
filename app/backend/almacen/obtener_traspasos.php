<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/cfsistem/config/conexion.php';
session_start();

header('Content-Type: application/json');

$usuario_id    = $_SESSION['usuario_id'] ?? 0;
$rol_id        = $_SESSION['rol_id'] ?? 0;
$almacen_sesion = $_SESSION['almacen_id'] ?? 0; // Almacén del usuario logueado
$almacen_get    = $_GET['almacen_id'] ?? null; // Almacén seleccionado por Admin

$response = ['arribos' => [], 'envios' => []];

// DETERMINAR EL ALMACÉN A CONSULTAR
// Si es admin, usa el del GET. Si no, usa el de su sesión.
$almacen_a_consultar = ($rol_id == 1) ? $almacen_get : $almacen_sesion;

if (!$almacen_a_consultar) {
    echo json_encode($response);
    exit;
}

try {
    // 1. CONSULTA DE ARRIBOS (Lo que mi almacén debe recibir)
    // Filtramos donde almacen_destino_id es mi almacén y nadie ha firmado de recibido
    $sqlArribos = "SELECT m.id, m.fecha, p.nombre as producto, p.sku, m.cantidad, 
                          ao.nombre as origen, u.nombre as enviado_por
                   FROM movimientos m
                   JOIN productos p ON m.producto_id = p.id
                   JOIN almacenes ao ON m.almacen_origen_id = ao.id
                   JOIN usuarios u ON m.usuario_envia_id = u.id
                   WHERE m.tipo = 'traspaso' 
                   AND m.usuario_recibe_id IS NULL 
                   AND m.almacen_destino_id = ? 
                   ORDER BY m.fecha DESC";
    
    $stmtA = $conexion->prepare($sqlArribos);
    $stmtA->bind_param("i", $almacen_a_consultar);
    $stmtA->execute();
    $resA = $stmtA->get_result();
    while($row = $resA->fetch_assoc()) {
        $response['arribos'][] = $row;
    }

    // 2. CONSULTA DE ENVÍOS (Lo que mi almacén mandó a otros)
    // Filtramos donde almacen_origen_id es mi almacén
    $sqlEnvios = "SELECT m.id, m.fecha, p.nombre as producto, m.cantidad, 
                         ad.nombre as destino, m.usuario_recibe_id
                  FROM movimientos m
                  JOIN productos p ON m.producto_id = p.id
                  JOIN almacenes ad ON m.almacen_destino_id = ad.id
                  WHERE m.tipo = 'traspaso' 
                  AND m.almacen_origen_id = ? 
                  ORDER BY m.fecha DESC LIMIT 20";
    
    $stmtE = $conexion->prepare($sqlEnvios);
    $stmtE->bind_param("i", $almacen_a_consultar);
    $stmtE->execute();
    $resE = $stmtE->get_result();
    while($row = $resE->fetch_assoc()) {
        $row['estado'] = ($row['usuario_recibe_id']) ? 'Completado' : 'En Tránsito';
        $response['envios'][] = $row;
    }

    echo json_encode($response);

} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}