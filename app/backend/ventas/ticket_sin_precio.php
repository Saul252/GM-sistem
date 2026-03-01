<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/cfsistem/config/conexion.php';

// Capturamos el ID y la opción de precios (1 = Sí, 0 = No)
$id_venta = isset($_GET['id']) ? intval($_GET['id']) : 0;
$mostrar_precios = isset($_GET['precios']) ? intval($_GET['precios']) : 1;

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

// 2. Consulta de Detalles
$sqlDetalle = "SELECT dv.*, p.nombre as producto_nombre, p.sku, p.unidad_medida 
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
    <style>
        @page { size: 80mm auto; margin: 0; }
        body { 
            font-family: 'Courier New', Courier, monospace; 
            width: 72mm; margin: 0 auto; padding: 5mm; color: #000;
            background-color: #fff; position: relative;
        }
        /* Marca de Agua */
        body::before {
            content: "<?php echo ($mostrar_precios) ? 'ORIGINAL' : 'ENTREGA'; ?>";
            position: absolute; top: 45%; left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 40px; color: rgba(0, 0, 0, 0.05);
            z-index: -1; font-weight: bold;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .bold { font-weight: bold; }
        .header-title { font-size: 16px; margin-bottom: 5px; text-transform: uppercase; }
        .ticket-type { 
            border: 1px solid #000; padding: 2px 8px; 
            display: inline-block; font-size: 11px; margin-top: 5px;
            background-color: <?php echo ($mostrar_precios) ? '#fff' : '#000'; ?>;
            color: <?php echo ($mostrar_precios) ? '#000' : '#fff'; ?>;
        }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { font-size: 11px; border-bottom: 1px dashed #000; padding: 5px 0; }
        td { padding: 5px 0; font-size: 11px; vertical-align: top; }
        .divider { border-top: 1px double #000; margin: 10px 0; }
        .footer { font-size: 10px; margin-top: 20px; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body onload="window.print();">

    <div class="no-print" style="text-align:center; padding:10px;">
        <button onclick="window.print()">IMPRIMIR AHORA</button>
    </div>

    <div class="text-center">
        <div class="bold header-title"><?php echo $venta['nombre_almacen']; ?></div>
        <div style="font-size: 10px;"><?php echo $venta['direccion_almacen']; ?></div>
        <div class="ticket-type bold">
            <?php echo ($mostrar_precios) ? 'TICKET DE VENTA' : 'NOTA DE REMISIÓN'; ?>
        </div>
    </div>

    <div class="divider"></div>

    <div style="font-size: 11px;">
        <div><b>FOLIO:</b> <?php echo $venta['folio']; ?></div>
        <div><b>FECHA:</b> <?php echo date("d/m/Y H:i", strtotime($venta['fecha'])); ?></div>
        <div><b>CLIENTE:</b> <?php echo $venta['nombre_comercial']; ?></div>
        <?php if(!$mostrar_precios): ?>
            <div><b>DIR:</b> <?php echo $venta['direccion'] ?: 'N/A'; ?></div>
        <?php endif; ?>
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-left" style="width: 15%;">CANT</th>
                <th class="text-left">DESCRIPCIÓN</th>
                <?php if($mostrar_precios): ?>
                    <th class="text-right" style="width: 25%;">TOTAL</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php while($item = $detalles->fetch_assoc()): ?>
            <tr>
                <td><?php echo number_format($item['cantidad'], 0); ?></td>
                <td>
                    <b><?php echo $item['producto_nombre']; ?></b>
                    <?php if(!$mostrar_precios): ?>
                        <br><small>SKU: <?php echo $item['sku']; ?></small>
                    <?php endif; ?>
                </td>
                <?php if($mostrar_precios): ?>
                    <td class="text-right">$<?php echo number_format($item['subtotal'], 2); ?></td>
                <?php endif; ?>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <div class="divider"></div>

    <?php if($mostrar_precios): ?>
        <table style="width: 100%;">
            <tr>
                <td class="text-right bold">TOTAL:</td>
                <td class="text-right bold" style="font-size: 14px; width: 40%;">$<?php echo number_format($venta['total'], 2); ?></td>
            </tr>
        </table>
    <?php else: ?>
        <div style="margin-top: 35px;" class="text-center">
            <p>___________________________</p>
            <div class="bold">FIRMA DE CONFORMIDAD</div>
            <div style="font-size: 9px; margin-top: 5px;">
                Al firmar, el cliente acepta haber recibido <br>
                la mercancía en perfecto estado.
            </div>
        </div>
    <?php endif; ?>

    <div class="footer text-center">
        <p>Vendedor: <?php echo $venta['nombre_vendedor']; ?></p>
        <p class="bold">¡GRACIAS POR SU PREFERENCIA!</p>
        <p style="font-size: 8px;">ID Transacción: #<?php echo $venta['id']; ?></p>
    </div>

</body>
</html>