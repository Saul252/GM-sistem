<?php
/**
 * ventasHistorialModel.php
 * Lógica de base de datos para historial de ventas, entregas y pagos.
 */

class VentaHistorialModel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function obtenerVentasFiltradas($filtros, $rol_id, $almacen_sesion) {
        $where = " WHERE v.estado_general = 'activa' ";
        
        // Seguridad por Almacén
        if ($rol_id != 1) { 
            $where .= " AND v.almacen_id = $almacen_sesion "; 
        } elseif (!empty($filtros['almacen'])) { 
            $where .= " AND v.almacen_id = " . intval($filtros['almacen']); 
        }

        // Buscador (Folio o Cliente)
        if (!empty($filtros['search'])) {
            $s = $this->db->real_escape_string($filtros['search']);
            $where .= " AND (c.nombre_comercial LIKE '%$s%' OR v.folio LIKE '%$s%') ";
        }

        // Estatus Entrega
        if (!empty($filtros['status'])) {
            $st = $this->db->real_escape_string($filtros['status']);
            $where .= " AND v.estado_entrega = '$st' ";
        }

        // Rango de Fechas
        if (!empty($filtros['rango']) && $filtros['rango'] !== 'todos') {
            $where .= $this->construirFiltroFecha($filtros);
        }

        // Filtro por Estado de Pago (Saldo)
        $having = "";
        if (!empty($filtros['pago'])) {
            $having = ($filtros['pago'] == 'deuda') 
                ? " HAVING (v.total - pagado) > 0.01 " 
                : " HAVING (v.total - pagado) <= 0.01 ";
        }

        $sql = "SELECT v.*, c.nombre_comercial as cliente, a.nombre as almacen_nombre,
                (SELECT IFNULL(SUM(monto), 0) FROM historial_pagos WHERE venta_id = v.id) as pagado
                FROM ventas v 
                JOIN clientes c ON v.id_cliente = c.id 
                JOIN almacenes a ON v.almacen_id = a.id 
                $where $having ORDER BY v.fecha DESC";

        $res = $this->db->query($sql);
        return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    }

    private function construirFiltroFecha($f) {
        switch($f['rango']) {
            case 'hoy': return " AND DATE(v.fecha) = CURDATE() ";
            case 'ayer': return " AND DATE(v.fecha) = SUBDATE(CURDATE(),1) ";
            case 'semana': return " AND YEARWEEK(v.fecha, 1) = YEARWEEK(CURDATE(), 1) ";
            case 'mes': return " AND MONTH(v.fecha) = MONTH(CURDATE()) AND YEAR(v.fecha) = YEAR(CURDATE()) ";
            case 'personalizado':
                $ini = $this->db->real_escape_string($f['inicio']);
                $fin = $this->db->real_escape_string($f['fin']);
                return " AND DATE(v.fecha) BETWEEN '$ini' AND '$fin' ";
            default: return "";
        }
    }

    public function obtenerDetalleCompleto($id) {
        $id = intval($id);
        
        // Info principal y saldo
        $sqlI = "SELECT v.*, c.nombre_comercial, a.nombre as almacen, 
                (SELECT IFNULL(SUM(monto), 0) FROM historial_pagos WHERE venta_id = v.id) as total_pagado 
                FROM ventas v JOIN clientes c ON v.id_cliente = c.id 
                JOIN almacenes a ON v.almacen_id = a.id WHERE v.id = $id";
        $info = $this->db->query($sqlI)->fetch_assoc();
        
        // Productos del detalle
        $prods = [];
        $resP = $this->db->query("SELECT dv.*, p.nombre as producto FROM detalle_venta dv JOIN productos p ON dv.producto_id = p.id WHERE dv.venta_id = $id");
        while($p = $resP->fetch_assoc()){ $prods[] = $p; }
        
        // Historial de entregas parciales
        $historial = [];
        $sqlH = "SELECT ev.fecha, p.nombre as producto, de.cantidad, u.nombre as usuario_nombre 
                 FROM entregas_venta ev 
                 JOIN detalle_entrega de ON ev.id = de.entrega_id 
                 JOIN detalle_venta dv ON de.detalle_venta_id = dv.id 
                 JOIN productos p ON dv.producto_id = p.id 
                 JOIN usuarios u ON ev.usuario_id = u.id 
                 WHERE ev.venta_id = $id ORDER BY ev.fecha DESC";
        $resH = $this->db->query($sqlH);
        while($h = $resH->fetch_assoc()){ $historial[] = $h; }
        
        return ['info' => $info, 'productos' => $prods, 'historial' => $historial];
    }

    public function procesarEntrega($venta_id, $productos, $usuario_id) {
        $this->db->begin_transaction();
        try {
            $vta_info = $this->db->query("SELECT almacen_id, folio FROM ventas WHERE id = $venta_id")->fetch_assoc();
            $almacen_id = $vta_info['almacen_id'];

            // 1. Crear cabecera de entrega
            $stmt = $this->db->prepare("INSERT INTO entregas_venta (venta_id, usuario_id, fecha) VALUES (?, ?, NOW())");
            $stmt->bind_param("ii", $venta_id, $usuario_id);
            $stmt->execute();
            $entrega_id = $this->db->insert_id;

            foreach ($productos as $dv_id => $cant) {
                $dv_id = intval($dv_id);
                $cant = floatval($cant);
                if ($cant <= 0) continue;

                // Verificar pendiente
                $res_v = $this->db->query("SELECT (cantidad - cantidad_entregada) as pendiente, producto_id FROM detalle_venta WHERE id = $dv_id")->fetch_assoc();
                if ($cant > $res_v['pendiente']) throw new Exception("Cantidad excede el pendiente para el producto ID: $dv_id");

                // 2. Registrar detalle de entrega y actualizar detalle_venta
                $this->db->query("INSERT INTO detalle_entrega (entrega_id, detalle_venta_id, cantidad) VALUES ($entrega_id, $dv_id, $cant)");
                $this->db->query("UPDATE detalle_venta SET cantidad_entregada = cantidad_entregada + $cant WHERE id = $dv_id");

                // 3. Descontar Stock e Insertar Movimiento
                $this->db->query("UPDATE inventario SET stock = stock - $cant WHERE producto_id = {$res_v['producto_id']} AND almacen_id = $almacen_id");
                
                $mov_obs = "Salida por entrega parcial. Folio Venta: " . $vta_info['folio'];
                $this->db->query("INSERT INTO movimientos (producto_id, tipo, cantidad, almacen_origen_id, usuario_registra_id, referencia_id, observaciones) 
                                 VALUES ({$res_v['producto_id']}, 'salida', $cant, $almacen_id, $usuario_id, $venta_id, '$mov_obs')");
            }

            // 4. Actualizar estado_entrega general de la venta
            $check = $this->db->query("SELECT SUM(cantidad - cantidad_entregada) as deuda FROM detalle_venta WHERE venta_id = $venta_id")->fetch_assoc();
            $st = ($check['deuda'] <= 0) ? 'entregado' : 'parcial';
            $this->db->query("UPDATE ventas SET estado_entrega = '$st' WHERE id = $venta_id");

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }

    public function registrarAbono($venta_id, $monto, $usuario_id) {
        $stmt = $this->db->prepare("INSERT INTO historial_pagos (venta_id, monto, fecha, usuario_id) VALUES (?, ?, NOW(), ?)");
        $stmt->bind_param("idi", $venta_id, $monto, $usuario_id);
        return $stmt->execute();
    }
}