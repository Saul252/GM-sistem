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
    
}