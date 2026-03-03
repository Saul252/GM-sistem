<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/cfsistem/config/conexion.php';
session_start();
header('Content-Type: application/json');

$usuario_id     = $_SESSION['usuario_id'] ?? $_SESSION['id'] ?? 0;
$almacen_sesion = $_SESSION['almacen_id'] ?? 0;
$rol_id         = $_SESSION['rol_id'] ?? 0; // Importante obtener el rol de la sesión

if (!$usuario_id) {
    echo json_encode(['success' => false, 'cantidad' => 0]);
    exit;
}

try {
    // 1. DETERMINAR EL FILTRO SQL
    // Si el rol es 1 (Admin), no filtramos por almacén. Si no, filtramos por el del usuario.
    $esAdmin = ($rol_id == 1);
    $whereAlmacen = $esAdmin ? "" : " AND m.almacen_destino_id = ?";

    // 2. CONTAR EL TOTAL
    $sqlContar = "SELECT COUNT(*) as total FROM movimientos m
                  WHERE m.tipo = 'traspaso' AND m.usuario_recibe_id IS NULL" . $whereAlmacen;
    
    $stmtC = $conexion->prepare($sqlContar);
    
    if (!$esAdmin) {
        $stmtC->bind_param("i", $almacen_sesion);
    }
    
    $stmtC->execute();
    $cantidad = $stmtC->get_result()->fetch_assoc()['total'];

    // 3. OBTENER LOS ÚLTIMOS 5 PARA EL DROPDOWN
    $items = [];
    if ($cantidad > 0) {
        // Añadimos el nombre del almacén destino para que el admin sepa a dónde va la mercancía
        $sqlItems = "SELECT m.id, p.nombre as producto, m.cantidad, ao.nombre as origen, ad.nombre as destino
                     FROM movimientos m
                     JOIN productos p ON m.producto_id = p.id
                     JOIN almacenes ao ON m.almacen_origen_id = ao.id
                     JOIN almacenes ad ON m.almacen_destino_id = ad.id
                     WHERE m.tipo = 'traspaso' AND m.usuario_recibe_id IS NULL" . $whereAlmacen . "
                     ORDER BY m.fecha DESC LIMIT 5";
        
        $stmtI = $conexion->prepare($sqlItems);
        
        if (!$esAdmin) {
            $stmtI->bind_param("i", $almacen_sesion);
        }
        
        $stmtI->execute();
        $resI = $stmtI->get_result();
        while($row = $resI->fetch_assoc()) {
            // Si es admin, personalizamos el texto del "origen" para mostrar origen -> destino
            if ($esAdmin) {
                $row['origen'] = $row['origen'] . " ➔ " . $row['destino'];
            }
            $items[] = $row;
        }
    }

    echo json_encode([
        'success' => true,
        'cantidad' => (int)$cantidad,
        'items' => $items,
        'debug_mode' => $esAdmin ? 'admin' : 'usuario'
    ]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}