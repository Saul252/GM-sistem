<?php
class VentasModel {
    public static function obtenerCategorias($conexion) {
        return $conexion->query("SELECT id, nombre FROM categorias ORDER BY nombre ASC");
    }

    public static function obtenerAlmacenes($conexion, $almacen_usuario) {
        $sql = "SELECT id, nombre FROM almacenes WHERE activo = 1";
        if ($almacen_usuario > 0) {
            $sql .= " AND id = " . intval($almacen_usuario);
        }
        $sql .= " ORDER BY nombre ASC";
        return $conexion->query($sql);
    }

    public static function obtenerProductos($conexion, $almacen_usuario) {
        $sql = "SELECT p.id, p.sku, p.nombre, p.categoria_id, c.nombre AS categoria_nombre,
                i.stock, i.almacen_id, a.nombre AS almacen_nombre,
                IFNULL(pp.precio_minorista,0) precio_minorista,
                IFNULL(pp.precio_mayorista,0) precio_mayorista,
                IFNULL(pp.precio_distribuidor,0) precio_distribuidor
                FROM inventario i
                INNER JOIN productos p ON i.producto_id = p.id
                INNER JOIN almacenes a ON i.almacen_id = a.id
                LEFT JOIN categorias c ON p.categoria_id = c.id
                LEFT JOIN precios_producto pp ON pp.producto_id = p.id AND pp.almacen_id = i.almacen_id
                WHERE p.activo = 1 AND i.stock > 0";

        if ($almacen_usuario > 0) {
            $sql .= " AND i.almacen_id = " . intval($almacen_usuario);
        }
        $sql .= " ORDER BY p.nombre ASC";
        return $conexion->query($sql);
    }

    public static function obtenerClientes($conexion) {
        return $conexion->query("SELECT * FROM clientes WHERE activo = 1 ORDER BY nombre_comercial ASC");
    }
}