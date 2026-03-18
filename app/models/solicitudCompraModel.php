<?php
class SolicitudCompra {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function crear($data, $items) {
        try {
            // MySQLi Transaction
            $this->db->begin_transaction();

            $sqlCab = "INSERT INTO solicitudes_compra (administrador_id, almacen_id, proveedor_id, estado) 
                       VALUES (?, ?, ?, 'pendiente')";
            $stmt = $this->db->prepare($sqlCab);
            $stmt->bind_param("iii", $data['usuario_id'], $data['almacen_id'], $data['proveedor_id']);
            
            if (!$stmt->execute()) throw new Exception("Error al insertar cabecera: " . $stmt->error);
            
            $solicitud_id = $this->db->insert_id;

            $sqlDet = "INSERT INTO detalle_solicitud_compra (solicitud_id, producto_id, cantidad) 
                       VALUES (?, ?, ?)";
            $stmtDet = $this->db->prepare($sqlDet);

            foreach ($items as $id_producto => $cantidad_base) {
                $id_prod = intval($id_producto);
                $cant = floatval($cantidad_base);
                $stmtDet->bind_param("iid", $solicitud_id, $id_prod, $cant);
                if (!$stmtDet->execute()) throw new Exception("Error al insertar detalle: " . $stmtDet->error);
            }

            $this->db->commit();
            return true;
        } catch (Throwable $e) {
            $this->db->rollback();
            return $e->getMessage();
        }
    }

    public function listar($es_admin, $almacen_id) {
        $sql = "SELECT s.*, p.nombre_comercial as proveedor_nombre, a.nombre as almacen_nombre, u.nombre as admin_nombre
                FROM solicitudes_compra s
                LEFT JOIN proveedores p ON s.proveedor_id = p.id
                LEFT JOIN almacenes a ON s.almacen_id = a.id
                LEFT JOIN usuarios u ON s.administrador_id = u.id";
        
        if (!$es_admin) {
            $sql .= " WHERE s.almacen_id = " . intval($almacen_id);
        }
        
        $sql .= " ORDER BY s.fecha_creacion DESC";
        
        $result = $this->db->query($sql);
        return ($result) ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

public function obtenerDetalle($id) {
    $sql = "SELECT d.*, p.nombre as producto_nombre, p.sku, p.unidad_medida, 
                   p.unidad_reporte, p.factor_conversion, s.almacen_id as almacen_origen_id,
                   a.nombre as almacen_nombre, prov.nombre_comercial as proveedor_nombre
            FROM detalle_solicitud_compra d
            INNER JOIN productos p ON d.producto_id = p.id
            INNER JOIN solicitudes_compra s ON d.solicitud_id = s.id
            INNER JOIN almacenes a ON s.almacen_id = a.id
            LEFT JOIN proveedores prov ON s.proveedor_id = prov.id
            WHERE d.solicitud_id = ?";
            
    $stmt = $this->db->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    // IMPORTANTE: Aquí es donde entregamos los datos al controlador
    return $result->fetch_all(MYSQLI_ASSOC); 
}
    public function eliminar($id) {
        try {
            // Verificar estado con MySQLi
            $stmtCheck = $this->db->prepare("SELECT estado FROM solicitudes_compra WHERE id = ?");
            $stmtCheck->bind_param("i", $id);
            $stmtCheck->execute();
            $res = $stmtCheck->get_result()->fetch_assoc();

            if (!$res || $res['estado'] !== 'pendiente') {
                return false; 
            }

            $stmt = $this->db->prepare("DELETE FROM solicitudes_compra WHERE id = ?");
            $stmt->bind_param("i", $id);
            return $stmt->execute();
        } catch (Exception $e) {
            return false;
        }
    }

 public function actualizarEstado($id, $nuevoEstado, $compra_id = null) {
    // Forzamos minúsculas para que coincida con el ENUM('pendiente', 'recibido'...)
    $nuevoEstado = strtolower($nuevoEstado); 
    
    $sql = "UPDATE solicitudes_compra SET estado = ?, compra_id_final = ? WHERE id = ?";
    $stmt = $this->db->prepare($sql);
    
    // "sii" -> string (estado), int (compra_id), int (id_solicitud)
    $stmt->bind_param("sii", $nuevoEstado, $compra_id, $id);
    
    return $stmt->execute();
}
}
