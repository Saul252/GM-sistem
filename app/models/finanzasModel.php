<?php
class FinanzasModel {
    private $db;

    public function __construct($conexion) {
        $this->db = $conexion;
    }
public function getUsuariosActivos() {
    // Si solo necesitas el número, es más eficiente un COUNT
    return $this->db->query("SELECT nombre, username FROM usuarios WHERE activo = 1 LIMIT 6");
}
    public function getKPIs() {
        $query = "SELECT 
            (SELECT SUM(total) FROM ventas WHERE estado_general = 'activa' AND MONTH(fecha) = MONTH(CURRENT_DATE)) as ventas_mes,
            (SELECT SUM(total) FROM compras WHERE estado = 'confirmada' AND MONTH(fecha_compra) = MONTH(CURRENT_DATE)) as compras_mes,
            (SELECT SUM(total) FROM gastos WHERE estado = 'pagado' AND MONTH(fecha_gasto) = MONTH(CURRENT_DATE)) as gastos_mes";
        return $this->db->query($query)->fetch_assoc();
    }

    public function getStockAlmacenes() {
        $sql = "SELECT a.nombre, SUM(i.stock) as total_stock, SUM(i.stock * p.precio_adquisicion) as valor_total
                FROM almacenes a
                LEFT JOIN inventario i ON a.id = i.almacen_id
                LEFT JOIN productos p ON i.producto_id = p.id
                WHERE a.activo = 1
                GROUP BY a.id";
        return $this->db->query($sql);
    }

    public function getTopProductos() {
        $sql = "SELECT p.nombre, SUM(dv.cantidad) as total_vendido
                FROM detalle_venta dv
                JOIN productos p ON dv.producto_id = p.id
                GROUP BY p.id ORDER BY total_vendido DESC LIMIT 5";
        return $this->db->query($sql);
    }

    public function getStockCritico() {
        $sql = "SELECT p.nombre as producto, i.stock, i.stock_minimo, a.nombre as almacen 
                FROM inventario i 
                JOIN productos p ON i.producto_id = p.id 
                JOIN almacenes a ON i.almacen_id = a.id 
                WHERE i.stock <= i.stock_minimo AND a.activo = 1
                ORDER BY i.stock ASC LIMIT 5";
        return $this->db->query($sql);
    }

    public function getPendientes() {
        return [
            'compras' => $this->db->query("SELECT COUNT(*) as total FROM compras WHERE estado = 'pendiente'")->fetch_assoc()['total'],
            'traspasos' => $this->db->query("SELECT COUNT(*) as total FROM traspasos WHERE estado = 'en_transito'")->fetch_assoc()['total']
        ];
    }
}