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
// 2. Detalle de Venta (Traemos Factor y Unidad de la tabla Productos)
$sqlPago = "SELECT h.*  FROM historial_pagos h 
               

               WHERE h.venta_id = ?";
$stmtPago = $conexion->prepare($sqlPago);
$stmtPago->bind_param("i", $id_venta);
$stmtPago->execute();
$detallesPago = $stmtPago->get_result();
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
<style>
    *{
        box-sizing:border-box;
    }

    body{
        font-family:'Courier New', monospace;
        font-size:11px;
        margin:0;
        padding:4px;
        color:#000;
        background:#fff;
        width:78mm;
    }

    .ticket{
        width:100%;
    }

    .text-center{
        text-align:center;
    }

    .bold{
        font-weight:bold;
    }

    .titulo{
        font-size:15px;
        font-weight:bold;
        text-transform:uppercase;
        line-height:1.2;
    }

    .subtitulo{
        font-size:10px;
        line-height:1.3;
    }

    .divider{
        border-top:1px dashed #000;
        margin:6px 0;
    }

    table{
        width:100%;
        border-collapse:collapse;
    }

    td,th{
        padding:2px 0;
        vertical-align:top;
    }

    th{
        font-size:10px;
        text-transform:uppercase;
    }

    .item-row{
        border-bottom:1px dashed #ddd;
    }

    .producto{
        font-weight:bold;
        font-size:11px;
        margin-bottom:2px;
        word-break:break-word;
    }

    .detalle{
        font-size:10px;
        line-height:1.2;
    }

    .entrega{
        margin-top:3px;
        padding-left:4px;
        border-left:2px solid #000;
        font-size:10px;
    }

    .total{
        font-size:15px;
        font-weight:bold;
    }

    .metodo-box{
        margin-top:5px;
        font-size:10px;
    }

    .firma{
        margin-top:18px;
        text-align:center;
        font-size:10px;
    }

    .gracias{
        text-align:center;
        margin-top:10px;
        font-size:11px;
        font-weight:bold;
    }

    .no-print{
        margin-bottom:10px;
    }

    .no-print button{
        width:100%;
        padding:8px;
        border:none;
        background:#000;
        color:#fff;
        font-weight:bold;
        border-radius:4px;
    }

    @media print{

        body{
            width:78mm;
            padding:0;
        }

        .no-print{
            display:none;
        }

        @page{
            margin:2mm;
        }
    }
</style>

<body onload="window.print();">

<div class="ticket">

    <div class="no-print">
        <button onclick="window.print()">
            IMPRIMIR TICKET
        </button>
    </div>

    <div class="text-center">

        <div class="titulo">
            <?php echo strtoupper($venta['nombre_almacen']); ?>
        </div>

        <div class="subtitulo">
            <?php echo $venta['direccion_almacen']; ?>
        </div>

        <div class="bold" style="margin-top:4px;">
            <?php echo ($mostrar_precios) ? 'TICKET DE VENTA' : 'VALE DE ENTREGA'; ?>
        </div>

    </div>

    <div class="divider"></div>

    <table>

        <tr>
            <td><b>FOLIO:</b></td>
            <td align="right"><?php echo $venta['folio']; ?></td>
        </tr>

        <tr>
            <td><b>FECHA:</b></td>
            <td align="right">
                <?php echo date("d/m/Y H:i", strtotime($venta['fecha'])); ?>
            </td>
        </tr>

    </table>

    <div style="margin-top:4px;">
        <b>CLIENTE:</b><br>
        <?php echo substr($venta['nombre_comercial'], 0, 45); ?>
    </div>

    <div class="divider"></div>

    <table>

        <thead>

            <tr>

                <th align="left">DESC</th>

                <?php if($mostrar_precios): ?>
                    <th align="right">IMP</th>
                <?php endif; ?>

            </tr>

        </thead>

        <tbody>

        <?php while($item = $detalles->fetch_assoc()): 

            $f = ($item['factor'] > 0) ? $item['factor'] : 1;
            $unidad = $item['unidad_reporte'] ? $item['unidad_reporte'] : $item['unidad_medida'];

            $equiv = floatval($item['odmaEquivalencia'] ?? 1);

            $cantEntera = floor($item['cantidad'] / $f);

            $cantResto = round(fmod($item['cantidad'], $f), 2);

            $cantidadMostrar = $item['cantidad'];
            $unidadMostrar = $item['unidad_medida'];

            if (
                isset($item['unidadMedida']) &&
                $equiv > 0
            ) {

                $cantidadConvertida =
                    $item['cantidad'] * $equiv;

                if ($cantidadConvertida >= 1) {

                    $cantidadMostrar = number_format($cantidadConvertida,3);
                    $unidadMostrar = $item['odmaNombre'];
                }
            }

            $cantidadMostrarFormateada =
                number_format($cantidadMostrar, 1);

        ?>

        <tr class="item-row">

            <td>

                <div class="producto">
                    <?php echo $item['producto_nombre']; ?>
                </div>

                <div class="detalle">

                    <?php echo $cantidadMostrarFormateada; ?>
                    <?php echo $unidadMostrar; ?>
<?php if($cantResto > 0): ?>

    <?php 
        $mostrarResto = (
            $unidadMostrar != $item['unidad_medida']
            &&
            floatval($cantidadMostrarFormateada) != floatval($cantResto)
        );
    ?>

    <?php if($mostrarResto): ?>

        (
        <?php echo number_format($cantResto, 2); ?>
        <?php echo $item['unidad_medida']; ?>
        )

    <?php endif; ?>

<?php endif; ?>

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

    <table>

        <tr>

            <td class="total">
                TOTAL
            </td>

            <td align="right" class="total">
                $<?php echo number_format($venta['subtotal'], 2); ?>
            </td>

        </tr>

    </table>

    <div style="font-size:10px; margin-top:2px;">
        Estado:
        <b><?php echo $venta['estado_pago']; ?></b>
    </div>

    <?php while($pago = $detallesPago->fetch_assoc()): ?>

        <div class="metodo-box">

            <div>

                <b>Método:</b>

                <?php

                if(
                    $pago['metodo_pago']=='Efectivo'
                    &&
                    $pago['efectivoPagado']>0
                ){
                    echo 'Efectivo $'.$pago['efectivoPagado'];
                }else{
                    echo $pago['monto'].' '.$pago['metodo_pago'];
                }

                ?>

            </div>

            <div>

                <?php

                $cambio = $pago['efectivoPagado'] - $pago['monto'];

                if($cambio >= 0){

                    echo 'Cambio:     $'.number_format($cambio,2);

                }else{

                    echo 'Pendiente: $'.number_format(($cambio*(-1)),2);

                }

                ?>

            </div>

        </div>

    <?php endwhile; ?>

    <?php endif; ?>

    <div class="firma">

        ______________________
        <br>
        FIRMA DE RECIBIDO

    </div>

    <div style="margin-top:10px; font-size:10px;">
        Vendedor:
        <?php echo $venta['nombre_vendedor']; ?>
    </div>

    <div class="gracias">
        ¡GRACIAS POR SU COMPRA!
    </div>

</div>

</body>
</html>