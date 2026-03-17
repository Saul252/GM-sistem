<?php
class ProductosModel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    /**
     * Obtiene todos los productos con su categoría
     */
    public function listarTodos() {
        $sql = "SELECT p.*, c.nombre as categoria_nombre 
                FROM productos p
                LEFT JOIN categorias c ON p.categoria_id = c.id
                WHERE p.activo = 1 
                ORDER BY p.nombre ASC";
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * ESTA ES LA FUNCIÓN CLAVE PARA TUS VISTAS:
     * Obtiene productos vinculando Stock y Precios de un almacén específico
     */
    public function listarPorAlmacen($almacen_id) {
        $sql = "SELECT 
                    p.id, 
                    p.sku, 
                    p.nombre, 
                    p.unidad_medida, 
                    p.unidad_reporte, 
                    p.factor_conversion,
                    c.nombre as categoria_nombre,
                    COALESCE(i.stock, 0) as stock,
                    COALESCE(pp.precio_minorista, 0) as precio_minorista,
                    COALESCE(pp.precio_mayorista, 0) as precio_mayorista,
                    COALESCE(pp.precio_distribuidor, 0) as precio_distribuidor,
                    a.nombre as almacen_nombre,
                    a.id as almacen_id
                FROM productos p
                LEFT JOIN categorias c ON p.categoria_id = c.id
                -- Unimos con inventario para saber cuánto hay en ESTE almacén
                LEFT JOIN inventario i ON p.id = i.producto_id AND i.almacen_id = ?
                -- Unimos con precios para saber cuánto cuesta en ESTE almacén
                LEFT JOIN precios_producto pp ON p.id = pp.producto_id AND pp.almacen_id = ?
                -- Unimos con almacenes para tener el nombre del lugar
                LEFT JOIN almacenes a ON a.id = ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$almacen_id, $almacen_id, $almacen_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene un producto específico con sus detalles
     */
    public function obtenerPorId($id) {
        $sql = "SELECT p.*, c.nombre as categoria_nombre 
                FROM productos p 
                LEFT JOIN categorias c ON p.categoria_id = c.id 
                WHERE p.id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Para el buscador de productos (opcional)
     */
    public function buscar($termino, $almacen_id) {
        $sql = "SELECT p.*, i.stock 
                FROM productos p
                LEFT JOIN inventario i ON p.id = i.producto_id AND i.almacen_id = ?
                WHERE (p.nombre LIKE ? OR p.sku LIKE ?) AND p.activo = 1";
        $stmt = $this->db->prepare($sql);
        $term = "%$termino%";
        $stmt->execute([$almacen_id, $term, $term]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function listarTodo() {
    // Traemos todos los productos activos con sus unidades y factores
    $sql = "SELECT id as producto_id, nombre, sku, unidad_medida, unidad_reporte, factor_conversion 
            FROM productos 
            WHERE activo = 1 
            ORDER BY nombre ASC";
    
    $result = $this->db->query($sql);
    return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
}
}