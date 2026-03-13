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

   
}