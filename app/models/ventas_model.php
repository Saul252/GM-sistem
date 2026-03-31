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
public static function procesarVenta($conexion, $data, $id_usuario) {
    $conexion->begin_transaction();

    try {
        $id_cliente   = intval($data['id_cliente']);
        $descuento    = floatval($data['descuento']);
        $obs          = $data['observaciones'] ?? '';
        $carrito      = $data['carrito'];
        $monto_pagado = floatval($data['monto_pagado']);
        $metodo_pago  = $data['metodo_pago'] ?? 'Efectivo';

        // 1. VALIDACIÓN DE STOCK Y CÁLCULO DE TOTALES
        $subtotal = 0;
        $total_vendido_global = 0;
        $total_entregado_global = 0;

        foreach ($carrito as $key => $item) {
            $p_id = intval($item['producto_id']);
            $alm_id = intval($item['almacen_id']);
            $entrega_solicitada = floatval($item['entrega_hoy']);

            $stmtS = $conexion->prepare("SELECT stock FROM inventario WHERE producto_id = ? AND almacen_id = ? FOR UPDATE");
            $stmtS->bind_param("ii", $p_id, $alm_id);
            $stmtS->execute();
            $stockActual = floatval($stmtS->get_result()->fetch_assoc()['stock'] ?? 0);

            if ($entrega_solicitada > $stockActual) {
                $carrito[$key]['entrega_hoy'] = $stockActual;
            }

            $subtotal += floatval($item['subtotal']);
            $total_vendido_global += floatval($item['cantidad']);
            $total_entregado_global += $carrito[$key]['entrega_hoy'];
        }

        $total = $subtotal - $descuento;

        // 2. GENERAR FOLIO DINÁMICO
        $resFolio = $conexion->query("SELECT MAX(id) as ultimo_id FROM ventas");
        $filaFolio = $resFolio->fetch_assoc();
        $proximo_id = ($filaFolio['ultimo_id'] ?? 0) + 1;
        $folio = "V-" . str_pad($proximo_id, 2, "0", STR_PAD_LEFT);
        
        $id_almacen_vta = intval($carrito[0]['almacen_id']);
        $estado_entrega_vta = ($total_entregado_global >= $total_vendido_global) ? 'entregado' : (($total_entregado_global > 0) ? 'parcial' : 'pendiente');
        $estado_pago = ($monto_pagado >= $total) ? 'pagado' : (($monto_pagado > 0) ? 'parcial' : 'pendiente');

        // 3. INSERTAR CABECERA DE VENTA
        $sqlV = "INSERT INTO ventas (folio, id_cliente, almacen_id, usuario_id, subtotal, descuento, total, estado_pago, estado_entrega, estado_general, observaciones) 
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'activa', ?)";
        $stmtV = $conexion->prepare($sqlV);
        $stmtV->bind_param("siiidddsss", $folio, $id_cliente, $id_almacen_vta, $id_usuario, $subtotal, $descuento, $total, $estado_pago, $estado_entrega_vta, $obs);
        $stmtV->execute();
        $id_venta = $conexion->insert_id;

        // --- LÓGICA DEL MÓDULO DE SALDOS (VISOR GENERAL) ---
        $deuda_generada = $total - $monto_pagado;

        // A. Actualizar resumen de saldo del cliente
        $stmtSld = $conexion->prepare("INSERT INTO clientes_saldos (cliente_id, saldo_en_contra, ultima_venta_id) 
                                      VALUES (?, ?, ?) 
                                      ON DUPLICATE KEY UPDATE 
                                      saldo_en_contra = saldo_en_contra + ?, 
                                      ultima_venta_id = ?");
        $stmtSld->bind_param("ididi", $id_cliente, $deuda_generada, $id_venta, $deuda_generada, $id_venta);
        $stmtSld->execute();

        // B. Registrar Log para el Visor (Corregido el error de truncado de tipo_movimiento)
        $tipo_mov = ($deuda_generada > 0) ? 'cargo' : 'abono';
        $obs_log  = ($deuda_generada > 0) ? "Venta con saldo pendiente" : "Venta liquidada";

        $stmtLog = $conexion->prepare("INSERT INTO clientes_saldos_log (
            cliente_id, 
            venta_id, 
            tipo_movimiento, 
            monto, 
            monto_operacion_total, 
            monto_pagado_momento, 
            referencia_tipo, 
            observaciones, 
            usuario_id
        ) VALUES (?, ?, ?, ?, ?, ?, 'venta', ?, ?)");

        // Cadena: i(int), i(int), s(string), d(double), d(double), d(double), s(string), i(int)
        $stmtLog->bind_param("iisdddsi", 
            $id_cliente, 
            $id_venta, 
            $tipo_mov, 
            $deuda_generada, 
            $total, 
            $monto_pagado, 
            $obs_log, 
            $id_usuario
        );
        $stmtLog->execute();

        // 4. REGISTRAR PAGO (Si existe)
        if ($monto_pagado > 0) {
            $stmtP = $conexion->prepare("INSERT INTO historial_pagos (venta_id, usuario_id, monto, metodo_pago) VALUES (?, ?, ?, ?)");
            $stmtP->bind_param("iids", $id_venta, $id_usuario, $monto_pagado, $metodo_pago);
            $stmtP->execute();
        }

        // 5. PROCESAR ENTREGAS FÍSICAS E INVENTARIO
        $id_entrega_maestro = null;
        if ($total_entregado_global > 0) {
            $stmtE = $conexion->prepare("INSERT INTO entregas_venta (venta_id, usuario_id, fecha, observaciones) VALUES (?, ?, NOW(), ?)");
            $obs_e = "Entrega inicial. Folio: $folio";
            $stmtE->bind_param("iis", $id_venta, $id_usuario, $obs_e);
            $stmtE->execute();
            $id_entrega_maestro = $conexion->insert_id;
        }

        foreach ($carrito as $item) {
            $p_id      = intval($item['producto_id']);
            $alm_id    = intval($item['almacen_id']);
            $cant_ped  = floatval($item['cantidad']);
            $cant_real = floatval($item['entrega_hoy']); 
            $prec      = floatval($item['precio_unitario']);
            $subt      = floatval($item['subtotal']);
            
            $st_fila = ($cant_real >= $cant_ped) ? 'entregado' : (($cant_real > 0) ? 'parcial' : 'pendiente');
            
            $sqlD = "INSERT INTO detalle_venta (venta_id, producto_id, cantidad, cantidad_entregada, precio_unitario, subtotal, estado_entrega) 
                     VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmtD = $conexion->prepare($sqlD);
            $stmtD->bind_param("iidddds", $id_venta, $p_id, $cant_ped, $cant_real, $prec, $subt, $st_fila);
            $stmtD->execute();
            $id_detalle_venta = $conexion->insert_id;

            if ($cant_real > 0 && $id_entrega_maestro) {
                // Detalle entrega
                $stmtDE = $conexion->prepare("INSERT INTO detalle_entrega (entrega_id, detalle_venta_id, cantidad) VALUES (?, ?, ?)");
                $stmtDE->bind_param("iid", $id_entrega_maestro, $id_detalle_venta, $cant_real);
                $stmtDE->execute();

                // Actualizar Inventario
                $stmtInv = $conexion->prepare("UPDATE inventario SET stock = stock - ? WHERE producto_id = ? AND almacen_id = ?");
                $stmtInv->bind_param("dii", $cant_real, $p_id, $alm_id);
                $stmtInv->execute();
                
                // Kardex
                $mov_obs = "Salida Venta: $folio. Entregado: $cant_real / $cant_ped";
                $stmtMov = $conexion->prepare("INSERT INTO movimientos (producto_id, tipo, cantidad, almacen_origen_id, usuario_registra_id, referencia_id, observaciones) 
                                               VALUES (?, 'salida', ?, ?, ?, ?, ?)");
                $stmtMov->bind_param("idiiss", $p_id, $cant_real, $alm_id, $id_usuario, $id_venta, $mov_obs);
                $stmtMov->execute();
            }
        }

        $conexion->commit();
        return [
            'status' => 'success', 
            'id_venta' => $id_venta, 
            'folio' => $folio, 
            'total_pedido' => $total_vendido_global, 
            'total_entregado' => $total_entregado_global
        ];

    } catch (Exception $e) {
        $conexion->rollback();
        return ['status' => 'error', 'message' => $e->getMessage()];
    }
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
/**
 * Obtiene el ID del cliente asociado a una venta específica
 * @param int $venta_id
 * @return int|false Retorna el ID del cliente o false si no existe
 */

}