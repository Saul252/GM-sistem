<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/cfsistem/config/conexion.php';

$id_venta = isset($_GET['id']) ? intval($_GET['id']) : 0;
$mostrar_precios = isset($_GET['precios']) ? intval($_GET['precios']) : 1;

if ($id_venta <= 0) die("Error: ID no válido.");

// 1. Datos de la Venta
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

// 2. Detalle
$sqlDetalle = "SELECT dv.*, p.nombre as producto_nombre, p.sku FROM detalle_venta dv 
               JOIN productos p ON dv.producto_id = p.id WHERE dv.venta_id = ?";
$stmtD = $conexion->prepare($sqlDetalle);
$stmtD->bind_param("i", $id_venta);
$stmtD->execute();
$detalles = $stmtD->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        @page { margin: 0; }
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            width: 75mm; margin: 0 auto; padding: 15px; color: #1a1a1a; 
            background-color: #fff;
            position: relative;
        }
        /* Marca de Agua */
        body::before {
            content: "CFSISTEM";
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 60px;
            color: rgba(0, 0, 0, 0.04); /* Gris casi invisible */
            z-index: -1;
            font-weight: bold;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .bold { font-weight: 700; }
        .divider { border-top: 1px double #000; margin: 10px 0; }
        .divider-thin { border-top: 1px solid #eee; margin: 5px 0; }
        
        .header-title { font-size: 18px; letter-spacing: 1px; margin-bottom: 2px; }
        .ticket-type { 
            background: #000; color: #fff; padding: 3px 10px; 
            display: inline-block; font-size: 10px; margin-top: 5px; 
        }
        
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { font-size: 11px; border-bottom: 2px solid #000; padding: 5px 0; text-transform: uppercase; }
        td { padding: 6px 0; font-size: 11px; vertical-align: top; }
        
        .total-box { margin-top: 10px; padding: 8px; border: 1px solid #000; }
        .footer { font-size: 10px; color: #555; margin-top: 25px; }
        
        @media print { .no-print { display: none; } }
    </style>
</head>
<body onload="window.print();">

    <div class="no-print" style="margin-bottom: 10px; text-align: center;">
        <button onclick="window.print()" style="background:#000; color:#fff; border:none; padding:8px 15px; cursor:pointer;">IMPRIMIR TICKET</button>
    </div>

    <div class="text-center">
        <div class="bold header-title"><?php echo strtoupper($venta['nombre_almacen']); ?></div>
        <div style="font-size: 10px;"><?php echo $venta['direccion_almacen']; ?></div>
        <div class="ticket-type bold">
            <?php echo ($mostrar_precios) ? 'COMPROBANTE DE VENTA' : 'GUÍA DE DESPACHO'; ?>
        </div>
    </div>

    <div class="divider"></div>

    <table style="margin-top:0;">
        <tr>
            <td><span class="bold">FOLIO:</span> <?php echo $venta['folio']; ?></td>
            <td class="text-right"><?php echo date("d/m/Y", strtotime($venta['fecha'])); ?></td>
        </tr>
        <tr>
            <td colspan="2"><span class="bold">CLIENTE:</span> <?php echo strtoupper($venta['nombre_comercial']); ?></td>
        </tr>
        <tr>
            <td colspan="2" style="font-size: 9px;"><?php echo $venta['rfc']; ?></td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th class="text-left">CANT</th>
                <th class="text-left">DESCRIPCIÓN</th>
                <?php if($mostrar_precios): ?><th class="text-right">TOTAL</th><?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php while($item = $detalles->fetch_assoc()): ?>
            <tr>
                <td style="width: 15%"><?php echo number_format($item['cantidad'], 0); ?></td>
                <td>
                    <div class="bold"><?php echo $item['producto_nombre']; ?></div>
                    <div style="font-size: 9px; color: #666;">SKU: <?php echo $item['sku']; ?></div>
                </td>
                <?php if($mostrar_precios): ?>
                    <td class="text-right bold">$<?php echo number_format($item['subtotal'], 2); ?></td>
                <?php endif; ?>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <div class="divider"></div>

    <?php if($mostrar_precios): ?>
        <table style="width: 80%; margin-left: 20%;">
            <tr>
                <td>SUBTOTAL:</td>
                <td class="text-right">$<?php echo number_format($venta['subtotal'], 2); ?></td>
            </tr>
            <?php if($venta['descuento'] > 0): ?>
            <tr>
                <td>DESCUENTO:</td>
                <td class="text-right">-$<?php echo number_format($venta['descuento'], 2); ?></td>
            </tr>
            <?php endif; ?>
            <tr class="bold">
                <td style="font-size: 14px; padding-top: 5px;">TOTAL:</td>
                <td class="text-right" style="font-size: 14px; padding-top: 5px;">$<?php echo number_format($venta['total'], 2); ?></td>
            </tr>
        </table>
    <?php else: ?>
        <div style="margin-top: 50px;" class="text-center">
            <div style="border-top: 1px solid #000; width: 70%; margin: 0 auto;"></div>
            <div class="bold" style="font-size: 10px; margin-top: 5px;">FIRMA DE RECIBIDO</div>
            <div style="font-size: 9px;">DNI / FECHA</div>
        </div>
    <?php endif; ?>

    <div class="footer text-center">
        <div class="divider-thin"></div>
        <p class="m-0">Vendedor: <?php echo $venta['nombre_vendedor']; ?></p>
        <p class="bold" style="margin-top: 10px;">¡GRACIAS POR SU COMPRA!</p>
        <p style="font-size: 8px;">Software de Gestión: cfsistem.v1</p>
    </div>

</body>
</html>