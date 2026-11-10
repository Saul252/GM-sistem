<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$base_path = $_SERVER['DOCUMENT_ROOT'] . '/cfsistem';
require_once $base_path . '/includes/auth.php';
require_once $base_path . '/config/conexion.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $producto_id = $_POST['producto_id'] ?? null;
    $origen_id = $_POST['almacen_origen_id'] ?? null;
    $destino_id = $_POST['almacen_destino_id'] ?? null;
    $cantidad = $_POST['cantidad'] ?? 0;
    $obs = $_POST['observaciones'] ?? '';
    $usuario_id = $_SESSION['usuario_id'] ?? 1;
    $lote = $_POST['lote_id'] ?? '';

    if (!$producto_id || !$origen_id || !$destino_id || $cantidad <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'Datos incompletos para el traspaso.']);
        exit;
    }

    $conexion->begin_transaction();

    try {
        // 1. Verificar stock actual en origen (inventario)
        $stmtStock = $conexion->prepare("SELECT stock FROM inventario WHERE producto_id = ? AND almacen_id = ? FOR UPDATE");
        $stmtStock->bind_param("ii", $producto_id, $origen_id);
        $stmtStock->execute();
        $resStock = $stmtStock->get_result()->fetch_assoc();

        if (!$resStock || $resStock['stock'] < $cantidad) {
            throw new Exception("Stock insuficiente en el almacén de origen.");
        }

        // 2. Restar stock del inventario origen
        $stmtOut = $conexion->prepare("UPDATE inventario SET stock = stock - ? WHERE producto_id = ? AND almacen_id = ?");
        $stmtOut->bind_param("dii", $cantidad, $producto_id, $origen_id);
        $stmtOut->execute();

        // 2.b Descontar de lotes_stock en origen (PEPS)
        if ($lote !== '' && $lote !== null) {
            // Lote específico seleccionado
            $sqlLotes = "SELECT id, cantidad_actual
                         FROM lotes_stock
                         WHERE id = ?
                           AND producto_id = ?
                           AND almacen_id = ?
                           AND cantidad_actual > 0
                         FOR UPDATE";
            $stmtL = $conexion->prepare($sqlLotes);
            $stmtL->bind_param("iii", $lote, $producto_id, $origen_id);
        } else {
            // PEPS automático (FIFO por fecha de ingreso)
            $sqlLotes = "SELECT id, cantidad_actual
                         FROM lotes_stock
                         WHERE producto_id = ?
                           AND almacen_id = ?
                           AND cantidad_actual > 0
                           AND estado_lote = 'activo'
                         ORDER BY fecha_ingreso ASC, id ASC
                         FOR UPDATE";
            $stmtL = $conexion->prepare($sqlLotes);
            $stmtL->bind_param("ii", $producto_id, $origen_id);
        }

        $stmtL->execute();
        $resLotes = $stmtL->get_result();

        $porRestar = (float) $cantidad;

        while ($l = $resLotes->fetch_assoc()) {
            if ($porRestar <= 0)
                break;

            $idLote = (int) $l['id'];
            $actual = (float) $l['cantidad_actual'];

            $aQuitar = ($actual <= $porRestar) ? $actual : $porRestar;
            $nuevoStock = $actual - $aQuitar;
            $nuevoEstado = ($nuevoStock <= 0) ? 'agotado' : 'activo';

            $up = $conexion->prepare("UPDATE lotes_stock SET cantidad_actual = ?, estado_lote = ? WHERE id = ?");
            $up->bind_param("dsi", $nuevoStock, $nuevoEstado, $idLote);
            $up->execute();

            $porRestar -= $aQuitar;
        }

        if ($porRestar > 0) {
            throw new Exception("Stock insuficiente en lotes del almacén origen.");
        }

        // 3. Registrar en la tabla movimientos
        // usuario_envia_id se llena ahora; usuario_recibe_id queda NULL.
        $tipo = 'traspaso';
        $stmtMov = $conexion->prepare("INSERT INTO movimientos 
            (producto_id, tipo, cantidad, almacen_origen_id, almacen_destino_id, usuario_registra_id, usuario_envia_id, observaciones,referencia_id) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?,?)");

        $stmtMov->bind_param(
            "isdiiiisi",
            $producto_id,
            $tipo,
            $cantidad,
            $origen_id,
            $destino_id,
            $usuario_id,
            $usuario_id,
            $obs,
            $lote
        );
        $stmtMov->execute();

        $conexion->commit();
        echo json_encode(['status' => 'success', 'message' => 'Envío registrado. Pendiente de recepción en destino.']);

    } catch (Exception $e) {
        $conexion->rollback();
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}