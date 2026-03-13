<?php
class MermasModel {
    private $db;

    public function __construct($conexion) {
        $this->db = $conexion;
    }

public function registrarMerma($datos) {
    $this->db->begin_transaction();
    try {
        // 1. INSERTAR EN MOVIMIENTOS (Historial / Kardex)
        $sqlMov = "INSERT INTO movimientos 
                   (producto_id, tipo, cantidad, almacen_origen_id, usuario_registra_id, responsable_movimiento, observaciones) 
                   VALUES (?, 'ajuste', ?, ?, ?, ?, ?)";
        $stmtMov = $this->db->prepare($sqlMov);
        $stmtMov->bind_param("idiiss", 
            $datos['producto_id'], 
            $datos['cantidad'], 
            $datos['almacen_id'], 
            $datos['usuario_id'], 
            $datos['responsable'], 
            $datos['motivo']
        );
        $stmtMov->execute();
        $movimiento_id = $this->db->insert_id;

        // 2. INSERTAR EN MERMAS (Detalle de la pérdida)
        $sqlMerma = "INSERT INTO mermas 
                     (movimiento_id, almacen_id, producto_id, lote_id, cantidad, tipo_merma, responsable_declaracion, descripcion_suceso) 
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmtMerma = $this->db->prepare($sqlMerma);
        $stmtMerma->bind_param("iiiidsss", 
            $movimiento_id, $datos['almacen_id'], $datos['producto_id'], $datos['lote_id'], 
            $datos['cantidad'], $datos['tipo_merma'], $datos['responsable'], $datos['motivo']
        );
        $stmtMerma->execute();

        // 3. ACTUALIZAR STOCK ESPECÍFICO (Lotes)
        $sqlLote = "UPDATE lotes_stock 
                    SET cantidad_actual = cantidad_actual - ?, 
                        estado_lote = IF(cantidad_actual - ? <= 0, 'agotado', estado_lote)
                    WHERE id = ? AND almacen_id = ?";
        $stmtLote = $this->db->prepare($sqlLote);
        // Usamos la cantidad dos veces (una para restar y otra para el IF del estado)
        $stmtLote->bind_param("ddii", $datos['cantidad'], $datos['cantidad'], $datos['lote_id'], $datos['almacen_id']);
        $stmtLote->execute();

        // 4. ACTUALIZAR STOCK GLOBAL (Tabla Inventario)
        $sqlInv = "UPDATE inventario 
                   SET stock = stock - ? 
                   WHERE almacen_id = ? AND producto_id = ?";
        $stmtInv = $this->db->prepare($sqlInv);
        $stmtInv->bind_param("dii", $datos['cantidad'], $datos['almacen_id'], $datos['producto_id']);
        $stmtInv->execute();

        // Verificamos que se haya actualizado el stock global
        if ($stmtInv->affected_rows === 0) {
            throw new Exception("Error: El producto no tiene un registro inicial en la tabla de inventario para este almacén.");
        }

        $this->db->commit();
        return true;

    } catch (Exception $e) {
        $this->db->rollback();
        return $e->getMessage();
    }
}

    // Método para cargar lotes dinámicamente
    public function getLotesPorProducto($almacen_id, $producto_id) {
        $sql = "SELECT id, codigo_lote, cantidad_actual, precio_compra_unitario 
                FROM lotes_stock 
                WHERE almacen_id = ? AND producto_id = ? AND estado_lote = 'activo' AND cantidad_actual > 0";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ii", $almacen_id, $producto_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
  public function obtenerMermasPaginadas($almacen_id, $limit = 10, $offset = 0) {
    // Si el almacen_id es 0, no filtramos (Admin)
    $filtroAlmacen = ($almacen_id > 0) ? "WHERE m.almacen_id = ?" : "";
    
    $sql = "SELECT 
                m.id,
                m.fecha_reporte, -- <--- Nombre corregido según tu DB
                m.cantidad,
                m.tipo_merma,
                mov.responsable_movimiento as responsable, -- Tomamos el responsable del movimiento
                a.nombre as almacen_nombre,
                p.nombre as producto_nombre,
                l.codigo_lote
            FROM mermas m
            JOIN movimientos mov ON m.movimiento_id = mov.id
            JOIN almacenes a ON m.almacen_id = a.id
            JOIN productos p ON m.producto_id = p.id
            LEFT JOIN lotes_stock l ON m.lote_id = l.id
            $filtroAlmacen
            ORDER BY m.fecha_reporte DESC
            LIMIT ? OFFSET ?";
            
    $stmt = $this->db->prepare($sql);

    if ($almacen_id > 0) {
        $stmt->bind_param("iii", $almacen_id, $limit, $offset);
    } else {
        $stmt->bind_param("ii", $limit, $offset);
    }

    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

// Método auxiliar para contar el total y saber cuántas páginas hay
public function contarTotalMermas($almacen_id) {
    $sql = "SELECT COUNT(*) as total FROM mermas";
    if ($almacen_id > 0) {
        $sql .= " WHERE almacen_id = " . intval($almacen_id);
    }
    $result = $this->db->query($sql);
    return $result->fetch_assoc()['total'];
}
}