<?php
// Iniciar buffer para atrapar cualquier salida accidental (espacios o warnings)
ob_start();

ini_set('display_errors', 0);
error_reporting(E_ALL);
header('Content-Type: application/json');

date_default_timezone_set('America/Mexico_City');
$path_conexion = $_SERVER['DOCUMENT_ROOT'] . '/cfsistem/config/conexion.php';

if (!file_exists($path_conexion)) {
    ob_clean(); // Limpiar basura antes de enviar JSON
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

    $subtotal = 0;
    foreach ($carrito as $item) { $subtotal += floatval($item['subtotal']); }
    $total = $subtotal - $descuento;
    $folio = "V-" . date('ymdHis');
    $id_almacen = intval($carrito[0]['almacen_id']);

    // 1. VENTA
    $sqlV = "INSERT INTO ventas (folio, id_cliente, almacen_id, usuario_id, subtotal, descuento, total, estado_pago, estado_entrega, estado_general, observaciones) 
             VALUES (?, ?, ?, ?, ?, ?, ?, 'pagado', 'pendiente', 'activa', ?)";
    $stmtV = $conexion->prepare($sqlV);
    $stmtV->bind_param("siiiddds", $folio, $id_cliente, $id_almacen, $id_usuario, $subtotal, $descuento, $total, $obs);
    $stmtV->execute();
    $id_venta = $conexion->insert_id;
// 2. DETALLES
    foreach ($carrito as $item) {
        $p_id = intval($item['producto_id']);
        $cant = floatval($item['cantidad']);
        $prec = floatval($item['precio_unitario']);
        $subt = floatval($item['subtotal']);
        
        // Limpieza de tipo_precio
        $raw_tp = strtolower(trim($item['tipo_precio']));
        if (strpos($raw_tp, 'dist') !== false) $tp = 'distribuidor';
        elseif (strpos($raw_tp, 'may') !== false) $tp = 'mayorista';
        else $tp = 'minorista';

        // SQL corregido: 7 columnas, 7 signos de interrogación
        $sqlD = "INSERT INTO detalle_venta (venta_id, producto_id, cantidad, cantidad_entregada, precio_unitario, tipo_precio, subtotal, estado_entrega) 
                 VALUES (?, ?, ?, 0, ?, ?, ?, 'pendiente')";
        
        $stmtD = $conexion->prepare($sqlD);
        
        // TIPOS: i=venta, i=prod, d=cant, d=precio, s=tipo, d=subtotal
        // Aquí faltaba un parámetro o sobraba un signo en tu código anterior
        $stmtD->bind_param("iiddsd", $id_venta, $p_id, $cant, $prec, $tp, $subt);
        
        if (!$stmtD->execute()) {
            // Esto te dirá EXACTAMENTE qué falló y qué valores llevaban
            throw new Exception("Error en DB: " . $stmtD->error . " | Datos: Venta:$id_venta, Prod:$p_id, Cant:$cant, Tipo:$tp");
        }
    }

    $conexion->commit();
    
    // IMPORTANTE: Limpiar el buffer y enviar respuesta exitosa
    ob_clean();
    echo json_encode([
        'status' => 'success', 
        'id_venta' => $id_venta, 
        'folio' => $folio,
        'estado' => 'activa' // Agregado para que el JS no falle
    ]);

} catch (Exception $e) {
    if (isset($conexion)) $conexion->rollback();
    ob_clean();
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}


//funciona pero no guarda en loas demas tablas solo en venta y detalle de venta