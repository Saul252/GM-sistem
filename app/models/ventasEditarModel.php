<?php
class VentaHistorialModel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    /**
     * LOGICA 3: CONSULTAR TODO EL DETALLE 
     * (Modificada SOLAMENTE para traer unidades y factor)
     */
public function obtenerDetalleCompleto($id) {
    $id = intval($id);
    
    // 1. Cabecera con Cliente y Almacén
    $sqlVenta = "SELECT v.*, c.nombre_comercial, a.nombre as almacen 
                 FROM ventas v 
                 INNER JOIN clientes c ON v.id_cliente = c.id 
                 INNER JOIN almacenes a ON v.almacen_id = a.id 
                 WHERE v.id = $id";
    
    $resVenta = $this->db->query($sqlVenta);
    if (!$resVenta || $resVenta->num_rows === 0) {
        throw new Exception("Venta no encontrada.");
    }
    $info = $resVenta->fetch_assoc();
    $almacen_id = intval($info['almacen_id']); // ID del almacén de la venta

    // Obtener total pagado desde historial_pagos
    $resPagos = $this->db->query("SELECT SUM(monto) as pagado FROM historial_pagos WHERE venta_id = $id");
    $pagoRow = $resPagos->fetch_assoc();
    $info['total_pagado'] = $pagoRow['pagado'] ?? 0;

    // 2. Detalle de productos incluyendo STOCK desde la tabla 'inventario'
    $productos = [];
    $sqlProd = "SELECT 
                    dv.*, 
                    p.nombre as producto, 
                    p.factor_conversion, 
                    p.unidad_medida as u_mayor, 
                    p.unidad_reporte as u_menor,
                    COALESCE(inv.stock, 0) as stock_actual 
                FROM detalle_venta dv 
                INNER JOIN productos p ON dv.producto_id = p.id 
                LEFT JOIN inventario inv ON p.id = inv.producto_id AND inv.almacen_id = $almacen_id
                WHERE dv.venta_id = $id";

    $resProd = $this->db->query($sqlProd);
    if ($resProd) {
        while ($row = $resProd->fetch_assoc()) {
            $productos[] = $row;
        }
    }

    // 3. Historial de entregas
    $historial = [];
    $sqlHis = "SELECT de.cantidad, e.fecha, u.nombre as usuario_nombre, p.nombre as producto
               FROM detalle_entrega de
               INNER JOIN entregas_venta e ON de.entrega_id = e.id
               INNER JOIN detalle_venta dv ON de.detalle_venta_id = dv.id
               INNER JOIN productos p ON dv.producto_id = p.id
               INNER JOIN usuarios u ON e.usuario_id = u.id
               WHERE e.venta_id = $id
               ORDER BY e.fecha DESC";
    
    $resHis = $this->db->query($sqlHis);
    if ($resHis) {
        while ($row = $resHis->fetch_assoc()) {
            $historial[] = $row;
        }
    }

    return [
        "status" => "success",
        "info" => $info,
        "productos" => $productos,
        "historial" => $historial
    ];
}

    /**
     * LOGICA 2: RECALCULAR Y EDITAR VENTA
     * (Aseguramos que actualice el subtotal de cada línea)
     */
    // public function recalcularYEditarVenta($data) {
    //     $this->db->begin_transaction();
    //     try {
    //         $v_id = intval($data['venta_id']);
    //         $u_id = intval($data['usuario_id']);
    //         $almacen_id = intval($data['almacen_id']);

    //         // Actualizar Cabecera de Venta
    //         $stmtV = $this->db->prepare("UPDATE ventas SET id_cliente = ?, subtotal = ?, total = ? WHERE id = ?");
    //         $stmtV->bind_param("iddi", $data['id_cliente'], $data['nuevo_subtotal'], $data['nuevo_total'], $v_id);
    //         $stmtV->execute();

    //         foreach ($data['productos'] as $prod) {
    //             $dv_id = intval($prod['detalle_id']);
    //             $p_id = intval($prod['producto_id']);
    //             $n_cant_total = floatval($prod['nueva_cantidad']);

    //             if ($dv_id > 0) {
    //                 $actual = $this->db->query("SELECT cantidad, cantidad_entregada, precio_unitario FROM detalle_venta WHERE id = $dv_id")->fetch_assoc();
                    
    //                 if ($n_cant_total < $actual['cantidad_entregada']) {
    //                     throw new Exception("No puedes bajar la cantidad a $n_cant_total porque ya entregaste {$actual['cantidad_entregada']}.");
    //                 }

    //                 // Lógica de Stock original
    //                 $dif = floatval($actual['cantidad']) - $n_cant_total;
    //                 if ($dif != 0) {
    //                     $this->db->query("UPDATE inventario SET stock = stock + ($dif) WHERE producto_id = $p_id AND almacen_id = $almacen_id");
    //                     $this->registrarMovimiento($p_id, ($dif > 0 ? 'entrada' : 'salida'), abs($dif), $almacen_id, $u_id, $v_id, "Ajuste por edición");
    //                 }

    //                 // Actualizar línea de detalle (Calculando subtotal por fila)
    //                 $nuevo_sub_fila = $n_cant_total * floatval($actual['precio_unitario']);
    //                 $this->db->query("UPDATE detalle_venta SET cantidad = $n_cant_total, subtotal = $nuevo_sub_fila WHERE id = $dv_id");
    //             }
    //         }

    //         $this->sincronizarEstadosEntrega($v_id);
    //         $this->db->commit();
    //         return ["status" => "success"];
    //     } catch (Exception $e) {
    //         $this->db->rollback();
    //         return ["status" => "error", "message" => $e->getMessage()];
    //     }
    // }

    // --- REUTILIZACIÓN DE TUS FUNCIONES ORIGINALES SIN CAMBIOS ---

    public function registrarAbono($venta_id, $monto, $usuario_id) {
        $stmt = $this->db->prepare("INSERT INTO historial_pagos (venta_id, monto, usuario_id, fecha) VALUES (?, ?, ?, NOW())");
        $stmt->bind_param("idi", $venta_id, $monto, $usuario_id);
        if ($stmt->execute()) {
            $v = $this->db->query("SELECT total FROM ventas WHERE id = $venta_id")->fetch_assoc();
            $p = $this->db->query("SELECT SUM(monto) as pagado FROM historial_pagos WHERE venta_id = $venta_id")->fetch_assoc();
            $nuevo_estado = ($p['pagado'] >= $v['total']) ? 'pagado' : 'parcial';
            $this->db->query("UPDATE ventas SET estado_pago = '$nuevo_estado' WHERE id = $venta_id");
            return ["status" => "success"];
        }
        return ["status" => "error", "message" => "Fallo al registrar abono"];
    }

    public function procesarEntregaParcial($venta_id, $productos_a_entregar, $usuario_id) {
        $this->db->begin_transaction();
        try {
            $v_info = $this->db->query("SELECT almacen_id, folio FROM ventas WHERE id = $venta_id")->fetch_assoc();
            $almacen_id = $v_info['almacen_id'];
            $stmtE = $this->db->prepare("INSERT INTO entregas_venta (venta_id, usuario_id, fecha) VALUES (?, ?, NOW())");
            $stmtE->bind_param("ii", $venta_id, $usuario_id);
            $stmtE->execute();
            $entrega_id = $this->db->insert_id;

            foreach ($productos_a_entregar as $p) {
                $dv_id = intval($p['detalle_venta_id']);
                $cant_hoy = floatval($p['cantidad_a_entregar']);
                if ($cant_hoy <= 0) continue;
                $res_v = $this->db->query("SELECT producto_id, (cantidad - cantidad_entregada) as pendiente FROM detalle_venta WHERE id = $dv_id")->fetch_assoc();
                if ($cant_hoy > $res_v['pendiente']) throw new Exception("Cantidad excede el pendiente.");
                
                $stmtDE = $this->db->prepare("INSERT INTO detalle_entrega (entrega_id, detalle_venta_id, cantidad) VALUES (?, ?, ?)");
                $stmtDE->bind_param("iid", $entrega_id, $dv_id, $cant_hoy);
                $stmtDE->execute();

                $this->db->query("UPDATE detalle_venta SET cantidad_entregada = cantidad_entregada + $cant_hoy WHERE id = $dv_id");
                $p_id = $res_v['producto_id'];
                $this->db->query("UPDATE inventario SET stock = stock - $cant_hoy WHERE producto_id = $p_id AND almacen_id = $almacen_id");
                $this->registrarMovimiento($p_id, 'salida', $cant_hoy, $almacen_id, $usuario_id, $venta_id, "Entrega parcial Folio: " . $v_info['folio']);
            }
            $this->sincronizarEstadosEntrega($venta_id);
            $this->db->commit();
            return ["status" => "success"];
        } catch (Exception $e) {
            $this->db->rollback();
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }

    private function sincronizarEstadosEntrega($venta_id) {
        $this->db->query("UPDATE detalle_venta SET estado_entrega = CASE WHEN cantidad_entregada >= cantidad THEN 'entregado' WHEN cantidad_entregada > 0 THEN 'parcial' ELSE 'pendiente' END WHERE venta_id = $venta_id");
        $res = $this->db->query("SELECT SUM(cantidad) as t, SUM(cantidad_entregada) as e FROM detalle_venta WHERE venta_id = $venta_id")->fetch_assoc();
        $st_g = ($res['e'] >= $res['t']) ? 'entregado' : ($res['e'] > 0 ? 'parcial' : 'pendiente');
        $this->db->query("UPDATE ventas SET estado_entrega = '$st_g' WHERE id = $venta_id");
    }

    private function registrarMovimiento($p_id, $tipo, $cant, $alm, $user, $ref, $obs) {
        $stmt = $this->db->prepare("INSERT INTO movimientos (producto_id, tipo, cantidad, almacen_origen_id, usuario_registra_id, referencia_id, observaciones) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isdiiss", $p_id, $tipo, $cant, $alm, $user, $ref, $obs);
        $stmt->execute();
    }
//  mandamos a trater todos los prodctos de la base de datos
 /**
     * Obtiene el catálogo completo de productos con precios y stock 
     * vinculados al almacén específico de la venta.
     */
    public function obtenerProductosAlmacen($almacen_id) {
        $almacen_id = intval($almacen_id);
        
        $sql = "SELECT 
                    p.id, 
                    p.sku, 
                    p.nombre, 
                    p.unidad_medida, 
                    p.unidad_reporte, 
                    p.factor_conversion,
                    pp.precio_minorista, 
                    pp.precio_mayorista, 
                    pp.precio_distribuidor,
                    IFNULL(i.stock, 0) as stock
                FROM productos p
                INNER JOIN precios_producto pp ON p.id = pp.producto_id
                LEFT JOIN inventario i ON p.id = i.producto_id AND i.almacen_id = $almacen_id
                WHERE pp.almacen_id = $almacen_id 
                AND p.activo = 1";
        
        $res = $this->db->query($sql);
        $productos = [];
        while ($row = $res->fetch_assoc()) {
            $productos[] = $row;
        }
        return $productos;
    }

    /**
     * Versión optimizada de Recalcular y Editar
     * Incluye la eliminación de productos que el usuario quitó de la tabla
     * y la inserción de productos nuevos (detalle_id = 0).
     */
    public function recalcularYEditarVenta($data) {
        $this->db->begin_transaction();
        try {
            $v_id = intval($data['venta_id']);
            $u_id = intval($data['usuario_id']);
            $almacen_id = intval($data['almacen_id']);

            // 1. ELIMINACIÓN FÍSICA de productos removidos en el front
            // Solo borra si NO han sido entregados (cantidad_entregada = 0)
            $ids_enviados = array_filter(array_column($data['productos'], 'detalle_id'));
            if (!empty($ids_enviados)) {
                $ids_string = implode(',', $ids_enviados);
                $this->db->query("DELETE FROM detalle_venta 
                                 WHERE venta_id = $v_id 
                                 AND cantidad_entregada = 0 
                                 AND id NOT IN ($ids_string)");
            }

            // 2. ACTUALIZAR CABECERA
            $stmtV = $this->db->prepare("UPDATE ventas SET id_cliente = ?, subtotal = ?, total = ? WHERE id = ?");
            $stmtV->bind_param("iddi", $data['id_cliente'], $data['nuevo_subtotal'], $data['nuevo_total'], $v_id);
            $stmtV->execute();

            // 3. PROCESAR PRODUCTOS (NUEVOS Y EXISTENTES)
            foreach ($data['productos'] as $prod) {
                $dv_id = intval($prod['detalle_id']);
                $p_id = intval($prod['producto_id']);
                $n_cant = floatval($prod['nueva_cantidad']);
                $precio = floatval($prod['precio_unitario']);
                $subtotal_fila = $n_cant * $precio;

                if ($dv_id == 0) {
                    // INSERTAR NUEVO PRODUCTO
                    $tipo_p = $prod['tipo_precio'] ?? 'minorista';
                    $stmtIns = $this->db->prepare("INSERT INTO detalle_venta (venta_id, producto_id, cantidad, precio_unitario, subtotal, tipo_precio) VALUES (?, ?, ?, ?, ?, ?)");
                    $stmtIns->bind_param("iiddss", $v_id, $p_id, $n_cant, $precio, $subtotal_fila, $tipo_p);
                    $stmtIns->execute();
                } else {
                    // ACTUALIZAR EXISTENTE
                    $actual = $this->db->query("SELECT cantidad_entregada FROM detalle_venta WHERE id = $dv_id")->fetch_assoc();
                    if ($n_cant < $actual['cantidad_entregada']) {
                        throw new Exception("No puedes reducir por debajo de lo ya entregado.");
                    }
                    $this->db->query("UPDATE detalle_venta SET cantidad = $n_cant, precio_unitario = $precio, subtotal = $subtotal_fila WHERE id = $dv_id");
                }
            }

            $this->sincronizarEstadosEntrega($v_id);
            $this->db->commit();
            return ["status" => "success"];
        } catch (Exception $e) {
            $this->db->rollback();
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }
}