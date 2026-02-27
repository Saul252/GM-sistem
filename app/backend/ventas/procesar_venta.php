<?php
ob_start();
ini_set('display_errors', 0);
error_reporting(E_ALL);
header('Content-Type: application/json');

date_default_timezone_set('America/Mexico_City');
$path_conexion = $_SERVER['DOCUMENT_ROOT'] . '/cfsistem/config/conexion.php';

if (!file_exists($path_conexion)) {
    ob_clean();
    echo json_encode(['status' => 'error', 'message' => 'Ruta de conexion invalida']);
    exit;
}

require_once $path_conexion;
session_start();

$input = file_get_contents("php://input");
$data = json_decode($input, true);

if (!$data) {
    ob_clean();
    echo json_encode(['status' => 'error', 'message' => 'No se recibieron datos JSON']);
    exit;
}

$conexion->begin_transaction();

try {
    $id_usuario = $_SESSION['usuario_id'] ?? 1;
    $id_cliente = intval($data['id_cliente']);
    $descuento  = floatval($data['descuento']);
    $obs        = $data['observaciones'] ?? '';
    $carrito    = $data['carrito'];
    
    // --- LÓGICA DE ESTADO MEJORADA ---
    $subtotal = 0;
    $total_vendido = 0;
    $total_entregado = 0;

    foreach ($carrito as $item) { 
        $subtotal += floatval($item['subtotal']); 
        $total_vendido += floatval($item['cantidad']);
        $total_entregado += floatval($item['entrega_hoy']);
    }

    if ($total_entregado >= $total_vendido) {
        $estado_entrega_global = 'entregado';
    } elseif ($total_entregado > 0) {
        $estado_entrega_global = 'parcial';
    } else {
        $estado_entrega_global = 'pendiente';
    }

    $hay_entrega = ($total_entregado > 0);
    // ---------------------------------
    
    $total = $subtotal - $descuento;
    $folio = "V-" . date('ymdHis');
    $id_almacen_principal = intval($carrito[0]['almacen_id']);

    // 1. INSERTAR EN TABLA: ventas
    $sqlV = "INSERT INTO ventas (folio, id_cliente, almacen_id, usuario_id, subtotal, descuento, total, estado_pago, estado_entrega, estado_general, observaciones) 
             VALUES (?, ?, ?, ?, ?, ?, ?, 'pagado', ?, 'activa', ?)";
    $stmtV = $conexion->prepare($sqlV);
    $stmtV->bind_param("siiidddss", $folio, $id_cliente, $id_almacen_principal, $id_usuario, $subtotal, $descuento, $total, $estado_entrega_global, $obs);
    $stmtV->execute();
    $id_venta = $conexion->insert_id;

    // 2. CREAR REGISTRO DE ENTREGA SI HAY PRODUCTOS A ENTREGAR
    $id_entrega_maestro = null;
    if ($hay_entrega) {
        $sqlE = "INSERT INTO entregas_venta (venta_id, usuario_id, observaciones) VALUES (?, ?, ?)";
        $obs_e = "Entrega inicial generada en venta";
        $stmtE = $conexion->prepare($sqlE);
        $stmtE->bind_param("iis", $id_venta, $id_usuario, $obs_e);
        $stmtE->execute();
        $id_entrega_maestro = $conexion->insert_id;
    }

    // 3. PROCESAR DETALLES
    foreach ($carrito as $item) {
        $p_id      = intval($item['producto_id']);
        $alm_id    = intval($item['almacen_id']);
        $cant_vend = floatval($item['cantidad']);
        $cant_entr = floatval($item['entrega_hoy']); 
        $prec      = floatval($item['precio_unitario']);
        $subt      = floatval($item['subtotal']);
        
        $estado_fila = 'pendiente';
        if ($cant_entr >= $cant_vend) $estado_fila = 'entregado';
        elseif ($cant_entr > 0) $estado_fila = 'parcial';

        $raw_tp = strtolower(trim($item['tipo_precio']));
        $tp = 'minorista';
        if (strpos($raw_tp, 'dist') !== false) $tp = 'distribuidor';
        elseif (strpos($raw_tp, 'may') !== false) $tp = 'mayorista';

        $sqlD = "INSERT INTO detalle_venta (venta_id, producto_id, cantidad, cantidad_entregada, precio_unitario, tipo_precio, subtotal, estado_entrega) 
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmtD = $conexion->prepare($sqlD);
        $stmtD->bind_param("iiddssds", $id_venta, $p_id, $cant_vend, $cant_entr, $prec, $tp, $subt, $estado_fila);
        $stmtD->execute();
        $id_detalle_venta = $conexion->insert_id;

        if ($cant_entr > 0 && $id_entrega_maestro) {
            $sqlDE = "INSERT INTO detalle_entrega (entrega_id, detalle_venta_id, cantidad) VALUES (?, ?, ?)";
            $stmtDE = $conexion->prepare($sqlDE);
            $stmtDE->bind_param("iid", $id_entrega_maestro, $id_detalle_venta, $cant_entr);
            $stmtDE->execute();
        }

        if ($cant_entr > 0) {
            $sqlInv = "UPDATE inventario SET stock = stock - ? WHERE producto_id = ? AND almacen_id = ?";
            $stmtInv = $conexion->prepare($sqlInv);
            $stmtInv->bind_param("dii", $cant_entr, $p_id, $alm_id);
            $stmtInv->execute();
            
            $sqlMov = "INSERT INTO movimientos (producto_id, tipo, cantidad, almacen_origen_id, usuario_registra_id, referencia_id, observaciones) 
                       VALUES (?, 'salida', ?, ?, ?, ?, ?)";
            $mov_obs = "Salida por venta folio: $folio (Cant. Entregada: $cant_entr)";
            $stmtMov = $conexion->prepare($sqlMov);
            $stmtMov->bind_param("idiiss", $p_id, $cant_entr, $alm_id, $id_usuario, $id_venta, $mov_obs);
            $stmtMov->execute();
        }
    }

    $conexion->commit();
    
    ob_clean();
    echo json_encode([
        'status' => 'success', 
        'id_venta' => $id_venta, 
        'folio' => $folio
    ]);

} catch (Exception $e) {
    if (isset($conexion)) $conexion->rollback();
    ob_clean();
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}