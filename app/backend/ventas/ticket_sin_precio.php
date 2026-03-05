<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/cfsistem/config/conexion.php';

// Capturamos el ID.
$id_venta = isset($_GET['id']) ? intval($_GET['id']) : 0;
// Forzamos mostrar_precios a 0 para este archivo de Remisión
$mostrar_precios = 0; 

if ($id_venta <= 0) die("Error: ID de venta no válido.");

// 1. Consulta de Venta con Relaciones
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

// 2. Consulta de Detalles (Añadimos factor_conversion y unidad_reporte)
$sqlDetalle = "SELECT dv.*, p.nombre as producto_nombre, p.sku, 
                      p.factor_conversion as factor, p.unidad_reporte 
               FROM detalle_venta dv 
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
    <title>Remisión #<?php echo $venta['folio']; ?></title>
    <style>
        @page { size: 80mm auto; margin: 0; }
        body { 
            font-family: 'Courier New', Courier, monospace; 
            width: 72mm; margin: 0 auto; padding: 5mm; color: #000;
            background-color: #fff;
            line-height: 1.2;
        }
        .text-center { text-align: center; }
        .bold { font-weight: bold; }
        .header-title { font-size: 14px; margin-bottom: 5px; text-transform: uppercase; }
        
        .ticket-type { 
            border: 1px solid #000; padding: 3px 10px; 
            display: inline-block; font-size: 12px; margin: 10px 0;
            background-color: #000; color: #fff;
        }

        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { font-size: 11px; border-bottom: 1px dashed #000; padding: 5px 0; text-align: left; }
        td { padding: 5px 0; font-size: 11px; vertical-align: top; }
        
        /* Estilo para el cuadro de conversión */
        .conversion-box {
            margin-top: 3px;
            padding-left: 5px;
            border-left: 2px solid #000;
            font-size: 10px;
        }

        .divider { border-top: 1px double #000; margin: 10px 0; }
        .footer { font-size: 10px; margin-top: 15px; border-top: 1px dashed #000; padding-top: 10px;}

        .firmas-container { margin-top: 30px; font-size: 10px; width: 100%; }
        .firma-box { text-align: center; margin-bottom: 25px; }
        .linea-firma { width: 85%; border-top: 1px solid #000; margin: 0 auto 5px; }

        @media print { .no-print { display: none; } }
    </style>
</head>
<body onload="window.print();">

    <div class="no-print" style="text-align:center; padding:10px; background:#eee; margin-bottom:10px;">
        <button onclick="window.print()" style="padding: 10px 20px; cursor: pointer; font-weight: bold;">🖨️ IMPRIMIR REMISIÓN</button>
    </div>

    <div class="text-center">
        <div class="bold header-title"><?php echo strtoupper($venta['nombre_almacen']); ?></div>
        <div style="font-size: 10px;"><?php echo $venta['direccion_almacen']; ?></div>
        <div class="ticket-type bold">REMISION DE ENTREGA</div>
        <div style="font-size: 9px; color: #444;">GUÍA DE DESPACHO SIN PRECIOS</div>
    </div>

    <div class="divider"></div>

    <div style="font-size: 11px;">
        <div><b>FOLIO:</b> <?php echo $venta['folio']; ?></div>
        <div><b>FECHA:</b> <?php echo date("d/m/Y H:i", strtotime($venta['fecha'])); ?></div>
        <div><b>CLIENTE:</b> <?php echo strtoupper($venta['nombre_comercial']); ?></div>
        <div><b>VENDEDOR:</b> <?php echo $venta['nombre_vendedor']; ?></div>
    </div>

    <div class="divider"></div>

    <table>
        <thead>
            <tr>
                <th style="width: 25%;">CANT.</th>
                <th>DESCRIPCIÓN</th>
            </tr>
        </thead>
        <tbody>
            <?php while($item = $detalles->fetch_assoc()): 
                // Lógica de Desglose
                $f = ($item['factor'] > 0) ? $item['factor'] : 1;
                $unidad = $item['unidad_reporte'] ?: 'Unid.';
                $cantEntera = floor($item['cantidad'] / $f);
                $cantResto = round(fmod($item['cantidad'], $f), 2);
            ?>
            <tr>
                <td class="bold">
                    <?php echo number_format($item['cantidad'], 0); ?><br>
                    <small style="font-weight:normal;">Piezas</small>
                </td>
                <td>
                    <b style="text-transform: uppercase;"><?php echo $item['producto_nombre']; ?></b><br>
                    <small>SKU: <?php echo $item['sku']; ?></small>
                    
                    <div class="conversion-box">
                        Entrega: <b><?php echo $cantEntera; ?> <?php echo $unidad; ?></b>
                        <?php if($cantResto > 0) echo " + <b>$cantResto pzas</b>"; ?>
                        <br>
                        <small>Factor: <?php echo $f; ?> pzas x <?php echo $unidad; ?></small>
                    </div>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <div class="divider"></div>

    <div class="firmas-container">
        <div class="firma-box">
            <div class="linea-firma"></div>
            <div class="bold">ENTREGADO POR</div>
            <div>ALMACÉN / DESPACHO</div>
        </div>

        <div class="firma-box" style="margin-top: 40px;">
            <div class="linea-firma"></div>
            <div class="bold">RECIBIDO POR</div>
            <div>NOMBRE Y FIRMA CLIENTE</div>
        </div>
        
        <div style="font-size: 8px; text-align: center; margin-top: 10px; font-style: italic;">
            "Mercancía recibida en buen estado y a entera satisfacción."
        </div>
    </div>

    <div class="footer text-center">
        <p class="bold">¡GRACIAS POR SU PREFERENCIA!</p>
        <p style="font-size: 8px;">ID: #<?php echo $venta['id']; ?> | <?php echo date("d/m/Y H:i"); ?></p>
        <p style="font-size: 8px;">Software: cfsistem.v1</p>
    </div>

</body>
</html>