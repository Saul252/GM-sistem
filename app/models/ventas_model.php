<?php

    // ... (los demás métodos para categorías y clientes se mantienen igual)
    class VentasModel {
    public static function obtenerProductos($conexion, $almacen_id = 0) {
        // SQL Robusto: Une Inventario para stock y Precios_Producto para el costo actual en ESE almacén
        $sql = "SELECT 
                    p.id, 
                    p.sku, 
                    p.nombre, 
                    p.unidad_medida, 
                    p.unidad_reporte, 
                    p.factor_conversion, 
                    p.categoria_id,
                    i.stock, 
                    i.almacen_id, 
                    a.nombre AS almacen_nombre,
                    pp.precio_minorista, 
                    pp.precio_mayorista, 
                    pp.precio_distribuidor
                FROM productos p
                INNER JOIN inventario i ON p.id = i.producto_id
                INNER JOIN almacenes a ON i.almacen_id = a.id
                LEFT JOIN precios_producto pp ON (p.id = pp.producto_id AND i.almacen_id = pp.almacen_id)
                WHERE p.activo = 1";

        if ($almacen_id > 0) {
            $sql .= " AND i.almacen_id = " . intval($almacen_id);
        }

        $sql .= " ORDER BY a.nombre ASC, p.nombre ASC";
        
        return $conexion->query($sql);
    }

   public static function cancelarVenta($conexion, $id_venta, $id_usuario, $motivo = 'Cancelación de venta') {
    $conexion->begin_transaction();

    try {
        // 1. Obtener datos de la venta y bloquear fila
        $stmtV = $conexion->prepare("SELECT estado_general, folio, almacen_id FROM ventas WHERE id = ? FOR UPDATE");
        $stmtV->bind_param("i", $id_venta);
        $stmtV->execute();
        $venta = $stmtV->get_result()->fetch_assoc();

        if (!$venta) throw new Exception("La venta no existe.");
        if ($venta['estado_general'] === 'cancelada') throw new Exception("Esta venta ya ha sido cancelada.");

        $folio = $venta['folio'];
        $id_almacen = $venta['almacen_id'];

        // 2. Consultar el detalle para devolver stock
        $stmtD = $conexion->prepare("SELECT producto_id, cantidad_entregada FROM detalle_venta WHERE venta_id = ?");
        $stmtD->bind_param("i", $id_venta);
        $stmtD->execute();
        $detalles = $stmtD->get_result();

        while ($item = $detalles->fetch_assoc()) {
            $p_id = $item['producto_id'];
            $cant_entregada = floatval($item['cantidad_entregada']);

            if ($cant_entregada > 0) {
                // A. Reingreso al inventario
                $stmtInv = $conexion->prepare("UPDATE inventario SET stock = stock + ? WHERE producto_id = ? AND almacen_id = ?");
                $stmtInv->bind_param("dii", $cant_entregada, $p_id, $id_almacen);
                $stmtInv->execute();

                // B. Registro en Movimientos (Kardex) - El ENUM 'entrada' sí existe en tu tabla movimientos
                $mov_obs = "REINGRESO POR CANCELACIÓN - Folio: $folio. Motivo: $motivo";
                $stmtMov = $conexion->prepare("INSERT INTO movimientos (producto_id, tipo, cantidad, almacen_origen_id, usuario_registra_id, referencia_id, observaciones) 
                                               VALUES (?, 'entrada', ?, ?, ?, ?, ?)");
                $stmtMov->bind_param("idiiss", $p_id, $cant_entregada, $id_almacen, $id_usuario, $id_venta, $mov_obs);
                $stmtMov->execute();
            }
        }

        // 3. Actualizar la cabecera (SOLO valores permitidos por tus ENUM)
        // estado_general permite 'cancelada'
        // NO tocamos estado_pago ni estado_entrega para evitar el error 'Data truncated'
        $stmtUpd = $conexion->prepare("UPDATE ventas SET estado_general = 'cancelada' WHERE id = ?");
        $stmtUpd->bind_param("i", $id_venta);
        $stmtUpd->execute();

        // 4. Limpiamos historial de pagos (opcional, pero recomendado para saldos)
        // Como tu tabla historial_pagos tiene ON DELETE CASCADE, si quisiéramos borrar:
        // $conexion->query("DELETE FROM historial_pagos WHERE venta_id = $id_venta");
        // O simplemente los dejamos ahí ya que la venta ya no es 'activa'.

        $conexion->commit();
        return ['status' => 'success', 'message' => "Venta $folio cancelada correctamente."];

    } catch (Exception $e) {
        $conexion->rollback();
        return ['status' => 'error', 'message' => $e->getMessage()];
    }
}
}