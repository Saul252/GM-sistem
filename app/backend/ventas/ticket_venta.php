<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/cfsistem/config/conexion.php';

$id_venta = isset($_GET['id']) ? intval($_GET['id']) : 0;
$mostrar_precios = isset($_GET['precios']) ? intval($_GET['precios']) : 1;

if ($id_venta <= 0) die("Error: ID de venta no válido.");

// 1. Datos de la Venta (Cabecera)
$sqlVenta = "SELECT v.*, c.nombre_comercial, c.rfc, c.direccion, u.nombre as nombre_vendedor,
                    a.nombre as nombre_almacen, a.ubicacion as direccion_almacen
             FROM ventas v
             JOIN clientes c ON v.id_cliente = c.id
             JOIN usuarios u ON v.usuario_id = u.id
             JOIN almacenes a ON v.almacen_id = a.id
             WHERE v.id = ?";
$stmt = $conexion->prepare($sqlVenta);
$stmt->bind_param("i", $id_venta);
$stmt->execute();
$venta = $stmt->get_result()->fetch_assoc();

if (!$venta) die("Error: Venta no encontrada.");

// 2. Detalle de Venta (Traemos Factor y Unidad de la tabla Productos)
$sqlDetalle = "SELECT dv.*, p.nombre as producto_nombre, p.sku, 
                      p.factor_conversion as factor, p.unidad_reporte,p.unidad_medida,   odma.id as odmaIdunidadMedida, odma.nombre as odmaNombre, odma.equivalencia as odmaEquivalencia 
               FROM detalle_venta dv 
               
join opciones_de_medida_adicional odma on odma.id= dv.unidadMedida

               JOIN productos p ON dv.producto_id = p.id 
               WHERE dv.venta_id = ?";
$stmtD = $conexion->prepare($sqlDetalle);
$stmtD->bind_param("i", $id_venta);
$stmtD->execute();
$detalles = $stmtD->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ticket #<?php echo $venta['folio']; ?></title>
    <style>
        @page { margin: 0; }
        body { 
            font-family: 'Courier New', Courier, monospace; 
            width: 72mm; margin: 0 auto; padding: 5px; color: #000; font-size: 12px;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .bold { font-weight: bold; }
        .divider { border-top: 1px dashed #000; margin: 5px 0; }
        
        table { width: 100%; border-collapse: collapse; }
        .item-row td { padding: 5px 0; vertical-align: top; }
        
        /* Caja de desglose especial para el cliente */
        .aclaracion-factor {
            font-size: 11px;
            margin-top: 2px;
            padding-left: 5px;
            border-left: 2px solid #000;
            display: block;
        }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body onload="window.print();">

    <div class="no-print text-center" style="margin-bottom: 20px;">
        <button onclick="window.print()" style="padding: 10px; width: 100%;">IMPRIMIR TICKET</button>
    </div>

    <div class="text-center">
        <span class="bold" style="font-size: 14px;"><?php echo strtoupper($venta['nombre_almacen']); ?></span><br>
        <?php echo $venta['direccion_almacen']; ?><br>
        <span class="bold"><?php echo ($mostrar_precios) ? 'TICKET DE VENTA' : 'VALE DE ENTREGA'; ?></span>
    </div>

    <div class="divider"></div>

    <table>
        <tr><td>FOLIO: <?php echo $venta['folio']; ?></td></tr>
        <tr><td>FECHA: <?php echo date("d/m/Y H:i", strtotime($venta['fecha'])); ?></td></tr>
        <tr><td>CLIENTE: <?php echo substr($venta['nombre_comercial'], 0, 30); ?></td></tr>
    </table>

    <div class="divider"></div>

    <table>
        <thead>
            <tr>
                <th align="left">DESC.</th>
                <?php if($mostrar_precios): ?><th align="right">SUBT.</th><?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php while($item = $detalles->fetch_assoc()): 
                // LÓGICA DE CONVERSIÓN
                $f = ($item['factor'] > 0) ? $item['factor'] : 1;
                $unidad = $item['unidad_reporte'] ? $item['unidad_reporte'] : $item['unidad_medida'];
               // Calculamos cuántos factores enteros y cuántas piezas sobran
              $equiv = floatval($item['odmaEquivalencia'] ?? 1);
                $nombreMedida=$item['odmaNombre'];
                $cantEntera = floor($item['cantidad'] / $f);
                $cantResto = round(fmod($item['cantidad'], $f), 2);
            ?>
            <tr class="item-row">
                <td>
                    <?php

// ===============================
// PRESENTACIÓN BONITA DE CANTIDAD
// ===============================

$cantidadMostrar = $item['cantidad'];
$unidadMostrar = 'pzas';
$cantidadMostrar = $item['cantidad'];
$unidadMostrar = $item['unidad_medida'];

$equiv = floatval($item['odmaEquivalencia'] ?? 0);

$cantidadMostrar = $item['cantidad'];
$unidadMostrar = $item['unidad_medida'];

if (
    isset($item['unidadMedida']) &&
    $equiv > 0
) {

    $cantidadConvertida =
        $item['cantidad'] * $equiv;

    if ($cantidadConvertida >= 1) {

        $cantidadMostrar = $cantidadConvertida;
        $unidadMostrar = $item['odmaNombre'];
    }
}

$cantidadMostrarFormateada =
    number_format($cantidadMostrar, 2);

$cantidadMostrarFormateada =
    number_format($cantidadMostrar, 2);
// Formato bonito
$cantidadMostrarFormateada =
    number_format($cantidadMostrar, 2);

?>

<div class="bold" style="font-size:13px;">
    <?php echo $item['producto_nombre']; ?>
</div>

<div style="
    margin-top:3px;
    font-size:12px;
">
    Cantidad:
    <span class="bold">
        <?php echo $cantidadMostrarFormateada; ?>
        <?php echo $unidadMostrar; ?>
         <?php if($cantResto > 0): ?>
           
          <b>

    <?php if($unidadMostrar!=$item['unidad_medida']){echo '(' .number_format($cantResto, 2) . ' ' . $item['unidad_medida'].')';} ?>
</b>         <?php endif; ?>
    </span>
</div>

<div class="aclaracion-factor" style="
    margin-top:5px;
    background:#f5f5f5;
    padding:6px;
    border-radius:4px;
">

    <div>
        Entrega:
        <b>
            <?php if ($cantEntera>0){echo $cantEntera.'' .$unidad;}?>
           
        </b>
        <?php if ($cantEntera>0 && $cantResto>0){echo'+';}?>

        <?php if($cantResto > 0): ?>
           
          <b>
    <?php echo number_format($cantResto, 2) . ' ' . $item['unidad_medida']; ?>
</b>         <?php endif; ?>
    </div>

    <small style="color:#555;">
        Factor:
        <?php echo number_format($f,2).' '.$item['unidad_medida'].'s ='; ?>
        <?php echo $unidad; ?>
    </small>

</div>
                </td>
                <?php if($mostrar_precios): ?>
                <td align="right" class="bold">
                    $<?php echo number_format($item['subtotal'], 2); ?>
                </td>
                <?php endif; ?>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <div class="divider"></div>

    <?php if($mostrar_precios): ?>
    <table style="font-size: 14px;">
        <tr class="bold">
            <td align="right">TOTAL:</td>
            <td align="right" style="width: 40%;">$<?php echo number_format($venta['cantidad'], 2); ?></td>
        </tr>
    </table>
    <?php else: ?>
    <div style="margin-top: 30px;" class="text-center">
       
    </div>
    <?php endif; ?>
      <div style="margin-top: 30px;" class="text-center">
       <br> __________________________
      <br>  FIRMA DE RECIBIDO
    </div>

    <div class="text-center" style="margin-top: 15px;">
        <p>Vendedor: <?php echo $venta['nombre_vendedor']; ?></p>
        <p class="bold">¡GRACIAS POR SU COMPRA!</p>
    </div>

</body>
</html>