<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/cfsistem/config/conexion.php';
session_start();

header('Content-Type: application/json');

// Obtenemos los datos de la sesión (ajustado a tu lógica de roles)
$usuario_id     = $_SESSION['usuario_id'] ?? $_SESSION['id'] ?? 0;
$rol_id         = $_SESSION['rol_id'] ?? 0;
$almacen_sesion = $_SESSION['almacen_id'] ?? 0;

// Si no hay usuario, respondemos 0
if (!$usuario_id) {
    echo json_encode(['success' => false, 'cantidad' => 0]);
    exit;
}

try {
    // Usamos el almacén de la sesión (puedes añadir la lógica de Admin si quieres que vea todas)
    $almacen_a_consultar = $almacen_sesion;

    // CONTAR ARRIBOS PENDIENTES (Lo que viene en camino y no he firmado)
    $sql = "SELECT COUNT(*) as total 
            FROM movimientos 
            WHERE tipo = 'traspaso' 
            AND usuario_recibe_id IS NULL 
            AND almacen_destino_id = ?";
    
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $almacen_a_consultar);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $cantidad = (int)$result['total'];

    // También obtenemos el nombre del rol para el debug del script
    $sqlRol = "SELECT nombre FROM roles WHERE id = ?";
    $stmtR = $conexion->prepare($sqlRol);
    $stmtR->bind_param("i", $rol_id);
    $stmtR->execute();
    $nombre_rol = $stmtR->get_result()->fetch_assoc()['nombre'] ?? 'Usuario';

    echo json_encode([
        'success' => true,
        'cantidad' => $cantidad,
        'debug' => [
            'rol' => $nombre_rol,
            'almacen_id' => $almacen_a_consultar
        ]
    ]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage(), 'cantidad' => 0]);
}