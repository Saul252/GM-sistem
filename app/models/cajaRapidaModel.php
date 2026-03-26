<?php

class cajaRapidaModel {

    /**
     * Obtiene productos con su stock y precios específicos por almacén.
     */
    public static function obtenerProductos($conexion, $almacen_id = 0) {
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

    /**
     * Procesa una venta rápida validando stock en tiempo real.
     * Solo entrega lo que existe en inventario.
     */
    public static function guardarVentaRapida($conexion, $data) {
        $conexion->begin_transaction();

        try {
            $id_usuario   = $_SESSION['usuario_id'] ?? 1;
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

                // Bloqueo preventivo de fila para evitar ventas duplicadas en milisegundos
                $stmtS = $conexion->prepare("SELECT stock FROM inventario WHERE producto_id = ? AND almacen_id = ? FOR UPDATE");
                $stmtS->bind_param("ii", $p_id, $alm_id);
                $stmtS->execute();
                $resStock = $stmtS->get_result()->fetch_assoc();
                $stockActual = floatval($resStock['stock'] ?? 0);

                // AJUSTE: Solo se entrega lo que hay en existencias
                if ($entrega_solicitada > $stockActual) {
                    $carrito[$key]['entrega_hoy'] = $stockActual;
                }

                $subtotal += floatval($item['subtotal']);
                $total_vendido_global += floatval($item['cantidad']);
                $total_entregado_global += $carrito[$key]['entrega_hoy'];
            }

            $total = $subtotal - $descuento;

            // 2. GENERACIÓN DE FOLIO (V-01, V-02...)
            $resFolio = $conexion->query("SELECT MAX(id) as ultimo_id FROM ventas");
            $filaFolio = $resFolio->fetch_assoc();
            $proximo_id = ($filaFolio['ultimo_id'] ?? 0) + 1;
            $folio = "V-" . str_pad($proximo_id, 2, "0", STR_PAD_LEFT);
            
            // Asumimos el almacén del primer producto del carrito para la cabecera
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

            // 4. REGISTRAR PAGO
            if ($monto_pagado > 0) {
                $stmtP = $conexion->prepare("INSERT INTO historial_pagos (venta_id, usuario_id, monto, metodo_pago) VALUES (?, ?, ?, ?)");
                $stmtP->bind_param("iids", $id_venta, $id_usuario, $monto_pagado, $metodo_pago);
                $stmtP->execute();
            }

            // 5. CABECERA DE ENTREGA FÍSICA
            $id_entrega_maestro = null;
            if ($total_entregado_global > 0) {
                $stmtE = $conexion->prepare("INSERT INTO entregas_venta (venta_id, usuario_id, fecha, observaciones) VALUES (?, ?, NOW(), ?)");
                $obs_e = "Entrega inicial - Caja Rápida. Folio: $folio";
                $stmtE->bind_param("iis", $id_venta, $id_usuario, $obs_e);
                $stmtE->execute();
                $id_entrega_maestro = $conexion->insert_id;
            }

            // 6. DETALLES, INVENTARIO Y MOVIMIENTOS
            foreach ($carrito as $item) {
                $p_id      = intval($item['producto_id']);
                $alm_id    = intval($item['almacen_id']);
                $cant_ped  = floatval($item['cantidad']);
                $cant_real = floatval($item['entrega_hoy']);
                $prec      = floatval($item['precio_unitario']);
                $subt      = floatval($item['subtotal']);
                
                $st_fila = ($cant_real >= $cant_ped) ? 'entregado' : (($cant_real > 0) ? 'parcial' : 'pendiente');
                
                // Normalización de tipo de precio para el ENUM
                $raw_tp = strtolower($item['tipo_precio']);
                $tp = (strpos($raw_tp, 'dist') !== false) ? 'distribuidor' : ((strpos($raw_tp, 'may') !== false) ? 'mayorista' : 'minorista');

                $stmtD = $conexion->prepare("INSERT INTO detalle_venta (venta_id, producto_id, cantidad, cantidad_entregada, precio_unitario, tipo_precio, subtotal, estado_entrega) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $stmtD->bind_param("iiddssds", $id_venta, $p_id, $cant_ped, $cant_real, $prec, $tp, $subt, $st_fila);
                $stmtD->execute();
                $id_detalle_venta = $conexion->insert_id;

                if ($cant_real > 0 && $id_entrega_maestro) {
                    // Detalle entrega
                    $stmtDE = $conexion->prepare("INSERT INTO detalle_entrega (entrega_id, detalle_venta_id, cantidad) VALUES (?, ?, ?)");
                    $stmtDE->bind_param("iid", $id_entrega_maestro, $id_detalle_venta, $cant_real);
                    $stmtDE->execute();

                    // Descontar Inventario
                    $stmtInv = $conexion->prepare("UPDATE inventario SET stock = stock - ? WHERE producto_id = ? AND almacen_id = ?");
                    $stmtInv->bind_param("dii", $cant_real, $p_id, $alm_id);
                    $stmtInv->execute();
                    
                    // Movimiento Kardex
                    $mov_obs = "Salida por venta rápida folio: $folio. Entregado: $cant_real de $cant_ped";
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
                'total_entregado' => $total_entregado_global,
                'total_pedido' => $total_vendido_global
            ];

        } catch (Exception $e) {
            $conexion->rollback();
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    /**
     * Cancela una venta y devuelve el stock al inventario.
     */
    public static function cancelarVenta($conexion, $id_venta, $id_usuario, $motivo = 'Cancelación de venta') {
        $conexion->begin_transaction();

        try {
            $stmtV = $conexion->prepare("SELECT estado_general, folio, almacen_id FROM ventas WHERE id = ? FOR UPDATE");
            $stmtV->bind_param("i", $id_venta);
            $stmtV->execute();
            $venta = $stmtV->get_result()->fetch_assoc();

            if (!$venta) throw new Exception("La venta no existe.");
            if ($venta['estado_general'] === 'cancelada') throw new Exception("Esta venta ya ha sido cancelada.");

            $folio = $venta['folio'];
            $id_almacen = $venta['almacen_id'];

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

                    // B. Registro Kardex (Entrada por cancelación)
                    $mov_obs = "REINGRESO POR CANCELACIÓN - Folio: $folio. Motivo: $motivo";
                    $stmtMov = $conexion->prepare("INSERT INTO movimientos (producto_id, tipo, cantidad, almacen_origen_id, usuario_registra_id, referencia_id, observaciones) 
                                                   VALUES (?, 'entrada', ?, ?, ?, ?, ?)");
                    $stmtMov->bind_param("idiiss", $p_id, $cant_entregada, $id_almacen, $id_usuario, $id_venta, $mov_obs);
                    $stmtMov->execute();
                }
            }

            $stmtUpd = $conexion->prepare("UPDATE ventas SET estado_general = 'cancelada' WHERE id = ?");
            $stmtUpd->bind_param("i", $id_venta);
            $stmtUpd->execute();

            $conexion->commit();
            return ['status' => 'success', 'message' => "Venta $folio cancelada correctamente."];

        } catch (Exception $e) {
            $conexion->rollback();
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
}