<?php
// 1. Configuración de cabeceras y conexión
header('Content-Type: text/html; charset=utf-8');
require_once __DIR__ . '/../../../config/conexion.php';

// 2. Validar parámetros
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$tipo = isset($_GET['tipo']) ? $_GET['tipo'] : '';

if ($id <= 0 || empty($tipo)) {
    echo '<div class="alert alert-danger">Datos insuficientes para mostrar el detalle.</div>';
    exit;
}

try {
    if ($tipo === 'compra') {
        // --- LÓGICA PARA COMPRAS ---
        // Consultar cabecera de la compra
        $sqlC = "SELECT c.*, u.nombre as usuario 
                 FROM compras c 
                 LEFT JOIN usuarios u ON c.usuario_registra_id = u.id 
                 WHERE c.id = ?";
        $stmtC = $conexion->prepare($sqlC);
        $stmtC->bind_param("i", $id);
        $stmtC->execute();
        $compra = $stmtC->get_result()->fetch_assoc();

        if (!$compra) throw new Exception("Compra no encontrada.");

        // Consultar los productos del detalle
        $sqlD = "SELECT d.*, p.nombre as producto_nombre, p.sku 
                 FROM detalle_compra d 
                 JOIN productos p ON d.producto_id = p.id 
                 WHERE d.compra_id = ?";
        $stmtD = $conexion->prepare($sqlD);
        $stmtD->bind_param("i", $id);
        $stmtD->execute();
        $detalle = $stmtD->get_result();

        // Generar HTML
        echo '<div class="row mb-3 shadow-sm p-3 bg-light rounded">';
        echo '  <div class="col-md-6"><b>Folio:</b> ' . $compra['folio'] . '</div>';
        echo '  <div class="col-md-6 text-end"><b>Fecha:</b> ' . date('d/m/Y H:i', strtotime($compra['fecha_compra'])) . '</div>';
        echo '  <div class="col-md-12"><b>Proveedor:</b> ' . htmlspecialchars($compra['proveedor']) . '</div>';
        echo '</div>';

        echo '<div class="table-responsive">
                <table class="table table-sm table-hover border">
                    <thead class="table-dark">
                        <tr>
                            <th>Producto / SKU</th>
                            <th class="text-center">Cant. Facturada</th>
                            <th class="text-center">Faltante</th>
                            <th class="text-center">Entregado</th>
                            <th class="text-end">Precio U.</th>
                            <th class="text-end">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>';

        while ($item = $detalle->fetch_assoc()) {
            $entregado = $item['cantidad'] - $item['cantidad_faltante'];
            $claseFaltante = ($item['cantidad_faltante'] > 0) ? 'table-warning text-danger fw-bold' : '';
            
            echo "<tr class='{$claseFaltante}'>
                    <td>{$item['producto_nombre']}<br><small class='text-muted'>{$item['sku']}</small></td>
                    <td class='text-center'>{$item['cantidad']}</td>
                    <td class='text-center'>{$item['cantidad_faltante']}</td>
                    <td class='text-center text-success fw-bold'>{$entregado}</td>
                    <td class='text-end'>$" . number_format($item['precio_unitario'], 2) . "</td>
                    <td class='text-end'>$" . number_format($item['subtotal'], 2) . "</td>
                  </tr>";
        }
        echo '    </tbody>
                  <tfoot>
                    <tr>
                        <th colspan="5" class="text-end">TOTAL:</th>
                        <th class="text-end text-primary">$' . number_format($compra['total'], 2) . '</th>
                    </tr>
                  </tfoot>
                </table>
              </div>';

    } else {
        // --- LÓGICA PARA GASTOS ---
        $sqlG = "SELECT * FROM gastos WHERE id = ?";
        $stmtG = $conexion->prepare($sqlG);
        $stmtG->bind_param("i", $id);
        $stmtG->execute();
        $gasto = $stmtG->get_result()->fetch_assoc();

        if (!$gasto) throw new Exception("Gasto no encontrado.");

        echo '<div class="p-4 text-center">';
        echo '  <i class="bi bi-cash-coin text-warning" style="font-size: 3rem;"></i>';
        echo '  <h4 class="mt-3">Gasto Operativo</h4>';
        echo '  <hr>';
        echo '  <p class="mb-1"><b>Folio:</b> ' . $gasto['folio'] . '</p>';
        echo '  <p class="mb-1"><b>Beneficiario:</b> ' . htmlspecialchars($gasto['beneficiario']) . '</p>';
        echo '  <h2 class="text-primary mt-3">$' . number_format($gasto['total'], 2) . '</h2>';
        echo '</div>';
    }

} catch (Exception $e) {
    echo '<div class="alert alert-danger">Error: ' . $e->getMessage() . '</div>';
}