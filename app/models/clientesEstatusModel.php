<?php
/**
 * ClientesEstatusModel.php
 * Versión optimizada para el esquema cfsistem (Marzo 2026)
 */

class ClientesEstatusModel {
    private $db;

    public function __construct($conexion) {
        $this->db = $conexion;
    }

    /**
     * Lista todos los clientes activos con el resumen financiero y logístico real.
     * Basado en tablas: ventas, historial_pagos y detalle_venta.
     */
   public function listarResumenClientes($almacen_id) {
    // Si es 0 (Admin), el WHERE siempre será verdadero (1=1)
    // Si no es 0, filtrará por el ID correspondiente.
    $sql = "SELECT 
                c.id, 
                c.nombre_comercial as nombre, 
                c.rfc,
                c.almacen_id, /* Importante para que el Admin sepa de quién es */
                (SELECT COUNT(*) FROM ventas v_count WHERE v_count.id_cliente = c.id AND v_count.estado_general = 'activa') as total_ventas,
                (SELECT IFNULL(SUM(v_pago.total), 0) - IFNULL((SELECT SUM(hp.monto) FROM historial_pagos hp INNER JOIN ventas v_hp ON hp.venta_id = v_hp.id WHERE v_hp.id_cliente = c.id AND v_hp.estado_general = 'activa'), 0) FROM ventas v_pago WHERE v_pago.id_cliente = c.id AND v_pago.estado_general = 'activa') as saldo_deuda,
                (SELECT COUNT(*) FROM detalle_venta dv INNER JOIN ventas v_entrega ON dv.venta_id = v_entrega.id WHERE v_entrega.id_cliente = c.id AND v_entrega.estado_general = 'activa' AND (dv.cantidad - dv.cantidad_entregada) > 0.01) as entregas_pendientes
            FROM clientes c
            WHERE c.activo = 1 
            AND (? = 0 OR c.almacen_id = ?) 
            ORDER BY total_ventas DESC, saldo_deuda DESC, nombre ASC";
    
    $stmt = $this->db->prepare($sql);
    // Pasamos el ID dos veces para la lógica del WHERE (? = 0 OR almacen_id = ?)
    $stmt->bind_param("ii", $almacen_id, $almacen_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
    /**
     * Detalle específico de folios para el expediente del cliente
     */
    public function obtenerDetalleFinanciero($id_cliente) {
    $sql = "SELECT 
                v.id as venta_id,
                v.folio, 
                v.fecha, 
                v.total, 
                v.estado_pago,
                v.estado_entrega,
                /* Sumamos los abonos que pertenecen a esta venta */
                (SELECT IFNULL(SUM(monto), 0) FROM historial_pagos WHERE venta_id = v.id) as total_pagado,
                /* Calculamos la diferencia en tiempo real */
                (v.total - (SELECT IFNULL(SUM(monto), 0) FROM historial_pagos WHERE venta_id = v.id)) as saldo_folio
            FROM ventas v
            WHERE v.id_cliente = ? 
            AND v.estado_general = 'activa'
            ORDER BY v.fecha DESC";
            
    $stmt = $this->db->prepare($sql);
    $stmt->bind_param("i", $id_cliente);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
public function obtenerHistorialPagosCompleto($id_cliente) {
    $sql = "SELECT 
                hp.monto, 
                hp.fecha, 
                v.folio,
                u.nombre as usuario_recibio
            FROM historial_pagos hp
            INNER JOIN ventas v ON hp.venta_id = v.id
            INNER JOIN usuarios u ON hp.usuario_id = u.id
            WHERE v.id_cliente = ?
            ORDER BY hp.fecha DESC";
            
    $stmt = $this->db->prepare($sql);
    $stmt->bind_param("i", $id_cliente);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
public function obtenerProductosPendientes($id_cliente) {
        $sql = "SELECT 
                    p.nombre as producto,
                    p.sku,
                    dv.cantidad as cantidad_total,
                    dv.cantidad_entregada,
                    (dv.cantidad - dv.cantidad_entregada) as faltante,
                    v.folio as folio_venta
                FROM detalle_venta dv
                INNER JOIN ventas v ON dv.venta_id = v.id
                INNER JOIN productos p ON dv.producto_id = p.id
                WHERE v.id_cliente = ? 
                AND v.estado_general = 'activa'
                AND (dv.cantidad - dv.cantidad_entregada) > 0.01"; // <--- Filtro de precisión
                
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id_cliente);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
/**
 * Obtiene el expediente 360: Ventas -> Productos (con Lotes y Costos) + Pagos (con Usuario)
 */
public function obtenerExpedienteCompleto($id_cliente) {
    // 1. Obtenemos todas las ventas activas del cliente
    $sqlVentas = "SELECT v.id, v.folio, v.fecha, v.total, v.estado_pago,
                         (SELECT IFNULL(SUM(monto), 0) FROM historial_pagos WHERE venta_id = v.id) as total_pagado
                  FROM ventas v
                  WHERE v.id_cliente = ? AND v.estado_general = 'activa'
                  ORDER BY v.fecha DESC";
    
    $stmtVentas = $this->db->prepare($sqlVentas);
    $stmtVentas->bind_param("i", $id_cliente);
    $stmtVentas->execute();
    $ventas = $stmtVentas->get_result()->fetch_all(MYSQLI_ASSOC);

    $expediente = [];

    foreach ($ventas as $venta) {
        $venta_id = $venta['id'];

        // 2. Detalle de productos AJUSTADO para mostrar la cantidad REAL por lote
        $sqlDetalle = "SELECT 
                            /* Si hay registro en movimientos de salida de lotes, usamos esa cantidad, 
                               de lo contrario, usamos la del detalle de venta */
                            IFNULL(lms.cantidad_salida, dv.cantidad) as cantidad, 
                            dv.cantidad_entregada,
                            dv.precio_unitario as precio_venta,
                            p.nombre as producto, 
                            p.sku,
                            IFNULL(ls.codigo_lote, 'S/L') as lote_codigo,
                            IFNULL(ls.precio_compra_unitario, 0) as precio_lote_compra
                       FROM detalle_venta dv
                       INNER JOIN productos p ON dv.producto_id = p.id
                       /* Vinculamos con la salida específica del lote */
                       LEFT JOIN lotes_movimientos_salida lms ON dv.id = lms.detalle_venta_id
                       LEFT JOIN lotes_stock ls ON lms.lote_id = ls.id
                       WHERE dv.venta_id = ?";
        
        $stmtDet = $this->db->prepare($sqlDetalle);
        $stmtDet->bind_param("i", $venta_id);
        $stmtDet->execute();
        $venta['productos'] = $stmtDet->get_result()->fetch_all(MYSQLI_ASSOC);

        // 3. Detalle de pagos
        $sqlPagos = "SELECT 
                        hp.monto, 
                        hp.fecha, 
                        u.nombre as usuario_recibio
                     FROM historial_pagos hp
                     INNER JOIN usuarios u ON hp.usuario_id = u.id
                     WHERE hp.venta_id = ?
                     ORDER BY hp.fecha ASC";
        
        $stmtPagos = $this->db->prepare($sqlPagos);
        $stmtPagos->bind_param("i", $venta_id);
        $stmtPagos->execute();
        $venta['pagos'] = $stmtPagos->get_result()->fetch_all(MYSQLI_ASSOC);

        $expediente[] = $venta;
    }

    return $expediente;
}

/**
 * Función mejorada para obtener datos del cliente (incluye estatus)
 */
public function obtenerDatosBasicos($id) {
    $stmt = $this->db->prepare("SELECT * FROM clientes WHERE id = ? AND activo = 1");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}
}