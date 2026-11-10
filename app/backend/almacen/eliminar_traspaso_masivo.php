<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$base_path = $_SERVER['DOCUMENT_ROOT'] . '/cfsistem';
require_once $base_path . '/includes/auth.php';
require_once $base_path . '/config/conexion.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Método no permitido.']);
    exit;
}

// Recibimos el id_traspaso_id que viene por POST
$id_traspaso_id = intval($_POST['id_traspaso_id'] ?? 0);

if ($id_traspaso_id <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'ID de traspaso masivo inválido.']);
    exit;
}

$conexion->begin_transaction();

try {
    // 1. Buscar todos los movimientos asociados a este id_traspaso_id en la tabla traspaso_masivo
    $stmtMasivo = $conexion->prepare("
        SELECT id, movimiento_id, codigo_traspaso, estado 
        FROM traspaso_masivo 
        WHERE id_traspaso_id = ?
    ");
    $stmtMasivo->bind_param("i", $id_traspaso_id);
    $stmtMasivo->execute();
    $resMasivo = $stmtMasivo->get_result();

    $movimientos_a_cancelar = [];
    while ($row = $resMasivo->fetch_assoc()) {
        // Opcional: Validar si ya está cancelado o si quieres omitir algún estado
        if ($row['estado'] === 'cancelado') {
            continue;
        }
        $movimientos_a_cancelar[] = intval($row['movimiento_id']);
    }

    if (empty($movimientos_a_cancelar)) {
        throw new Exception("No se encontraron movimientos activos para este traspaso masivo.");
    }

    $traspasos_cancelados_count = 0;

    // 2. Iterar sobre cada movimiento encontrado y aplicar la cancelación individual
    foreach ($movimientos_a_cancelar as $movimiento_id) {

        // A. Buscar el movimiento y bloquearlo
        $stmtMov = $conexion->prepare("
            SELECT id, producto_id, cantidad, almacen_origen_id, tipo, usuario_recibe_id, referencia_id
            FROM movimientos
            WHERE id = ?
            FOR UPDATE
        ");
        $stmtMov->bind_param("i", $movimiento_id);
        $stmtMov->execute();
        $mov = $stmtMov->get_result()->fetch_assoc();

        // Si el movimiento ya no existe en la tabla movimientos, saltamos al siguiente
        if (!$mov) {
            continue;
        }

        if ($mov['tipo'] !== 'traspaso') {
            throw new Exception("El movimiento ID $movimiento_id no es de tipo traspaso.");
        }
        if (!empty($mov['usuario_recibe_id'])) {
            throw new Exception("No se puede cancelar el traspaso ID $movimiento_id: ya fue recibido en destino.");
        }

        // B. Devolver el stock global al almacén de origen en la tabla inventario
        $stmtStock = $conexion->prepare("
            UPDATE inventario
            SET stock = stock + ?
            WHERE producto_id = ? AND almacen_id = ?
        ");
        $stmtStock->bind_param("dii", $mov['cantidad'], $mov['producto_id'], $mov['almacen_origen_id']);
        $stmtStock->execute();

        // C. Devolver la cantidad al lote específico usando referencia_id
        $lote_id = intval($mov['referencia_id'] ?? 0);
        $cantidad_a_regresar = (float) $mov['cantidad'];

        if ($lote_id > 0 && $cantidad_a_regresar > 0) {
            $stmtLoteCheck = $conexion->prepare("SELECT cantidad_actual FROM lotes_stock WHERE id = ? FOR UPDATE");
            $stmtLoteCheck->bind_param("i", $lote_id);
            $stmtLoteCheck->execute();
            $loteInfo = $stmtLoteCheck->get_result()->fetch_assoc();

            if ($loteInfo) {
                $nuevo_stock_lote = (float) $loteInfo['cantidad_actual'] + $cantidad_a_regresar;
                $nuevo_estado = ($nuevo_stock_lote > 0) ? 'activo' : 'agotado';

                $stmtUpLote = $conexion->prepare("
                    UPDATE lotes_stock 
                    SET cantidad_actual = ?, estado_lote = ? 
                    WHERE id = ?
                ");
                $stmtUpLote->bind_param("dsi", $nuevo_stock_lote, $nuevo_estado, $lote_id);
                $stmtUpLote->execute();
            }
        }

        // D. Eliminar el movimiento principal
        $stmtDel = $conexion->prepare("DELETE FROM movimientos WHERE id = ?");
        $stmtDel->bind_param("i", $movimiento_id);
        $stmtDel->execute();

        $traspasos_cancelados_count++;
    }

    // 3. Actualizar el estado en la tabla traspaso_masivo (por ejemplo, cambiar estado a 'cancelado' o eliminarlos)
    // Opción A: Actualizar estado a 'cancelado'
    $stmtUpMasivo = $conexion->prepare("UPDATE traspaso_masivo SET estado = 'cancelado' WHERE id_traspaso_id = ?");
    $stmtUpMasivo->bind_param("i", $id_traspaso_id);
    $stmtUpMasivo->execute();

    // (O si prefieres borrarlos de la tabla masiva, descomentarías la siguiente línea en lugar de la actualización):
    // $stmtDelMasivo = $conexion->prepare("DELETE FROM traspaso_masivo WHERE id_traspaso_id = ?");
    // $stmtDelMasivo->bind_param("i", $id_traspaso_id);
    // $stmtDelMasivo->execute();

    $conexion->commit();

    echo json_encode([
        'status' => 'success',
        'message' => "Traspaso masivo cancelado con éxito. Se procesaron y revirtieron $traspasos_cancelados_count movimiento(s) a sus respectivos lotes e inventarios."
    ]);

} catch (Exception $e) {
    $conexion->rollback();
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}