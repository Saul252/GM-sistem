<?php
class AlmacenModel {
    private $db;

    public function __construct($conexion) {
        $this->db = $conexion;
    }

    public function getCategorias() {
        return $this->db->query("SELECT id, nombre FROM categorias ORDER BY nombre ASC")->fetch_all(MYSQLI_ASSOC);
    }

    public function getAlmacenes($almacen_id = 0) {
        $sql = "SELECT * FROM almacenes WHERE activo = 1";
        if ($almacen_id > 0) $sql .= " AND id = " . intval($almacen_id);
        $sql .= " ORDER BY nombre ASC";
        return $this->db->query($sql)->fetch_all(MYSQLI_ASSOC);
    }
     public function getAlmacenesDestino($almacen_id = 0) {
    // Iniciamos la consulta básica
    $sql = "SELECT id, nombre FROM almacenes WHERE activo = 1";
    
    // Si el almacén es mayor a cero, agregamos la exclusión
    if ($almacen_id > 0) {
        $sql .= " AND id != " . intval($almacen_id);
    }
    
    $sql .= " ORDER BY nombre ASC";
    
    $res = $this->db->query($sql);
    return ($res) ? $res->fetch_all(MYSQLI_ASSOC) : [];
}

    public function getInventario($almacen_id = 0) {
        $sql = "SELECT p.id, p.sku, p.nombre, p.categoria_id, p.factor_conversion, p.unidad_reporte,c.nombre AS categoria_nombre,

                       i.stock, i.almacen_id, a.nombre AS almacen_nombre
                FROM inventario i
                INNER JOIN productos p ON i.producto_id = p.id
                INNER JOIN almacenes a ON i.almacen_id = a.id
                LEFT JOIN categorias c ON p.categoria_id = c.id
                WHERE p.activo = 1";
        
        if ($almacen_id > 0) $sql .= " AND i.almacen_id = " . intval($almacen_id);
        $sql .= " ORDER BY p.nombre ASC";
        
        return $this->db->query($sql)->fetch_all(MYSQLI_ASSOC);
    }
    
    public function getInventarioConId($almacen_id = 0) {
    $sql = "SELECT 
                p.id, 
                p.sku, 
                p.nombre, 
                i.stock, 
                i.almacen_id, 
                a.nombre AS almacen_nombre
            FROM inventario i
            INNER JOIN productos p ON i.producto_id = p.id
            INNER JOIN almacenes a ON i.almacen_id = a.id
            WHERE p.activo = 1";
    
    // Si se pasa un almacén específico, filtramos
    if ($almacen_id > 0) {
        $sql .= " AND i.almacen_id = " . intval($almacen_id);
    }
    
    $sql .= " AND i.stock > 0"; // Opcional: solo mostrar lo que tiene existencias
    $sql .= " ORDER BY p.nombre ASC";
    
  return $this->db->query($sql)->fetch_all(MYSQLI_ASSOC);
}
}
