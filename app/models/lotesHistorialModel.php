<?php

class HistorialLotesModel {

    private $db;

    public function __construct($conexion) {
        $this->db = $conexion;
    }

    public function obtenerTotalesLotes($producto_id, $almacen_id, $fecha_inicio, $fecha_fin)
{
    $sql = "SELECT 
                IFNULL(SUM(cantidad_inicial), 0) AS total_cantidad_inicial,
                IFNULL(SUM(cantidad_actual), 0) AS total_cantidad_actual
            FROM lotes_stock
            WHERE producto_id = ?
            AND almacen_id = ?
            AND fecha_ingreso BETWEEN ? AND ?";

    $stmt = $this->db->prepare($sql);

    $stmt->bind_param("iiss", $producto_id, $almacen_id, $fecha_inicio, $fecha_fin);

    $stmt->execute();

    return $stmt->get_result()->fetch_assoc();
}

    // 🔥 1. LISTADO COMPLETO DE LOTES
    public function obtenerLotes($producto_id, $almacen_id, $fecha_inicio, $fecha_fin) {

        $sql = "SELECT 
                    ls.id AS lote_id,
                    ls.codigo_lote,
                    ls.producto_id,
                    p.nombre AS producto_nombre,
                    ls.almacen_id,
                    a.nombre AS almacen_nombre,

                    ls.cantidad_inicial,
                    ls.cantidad_actual,
                    ls.precio_compra_unitario,

                    li.fecha_registro AS fecha_compra,

                    ls.estado_lote,

                    IFNULL(SUM(lms.cantidad_salida), 0) AS total_vendido,

                    IFNULL(SUM(lms.cantidad_salida * lms.precio_venta_pactado), 0) AS ingreso_total,

                    IFNULL(SUM(lms.cantidad_salida * lms.costo_compra_historico), 0) AS costo_total,

                    IFNULL(SUM(
                        (lms.precio_venta_pactado - lms.costo_compra_historico) * lms.cantidad_salida
                    ), 0) AS ganancia_total

                FROM lotes_stock ls

                LEFT JOIN productos p ON p.id = ls.producto_id
                LEFT JOIN almacenes a ON a.id = ls.almacen_id

                LEFT JOIN lotes_ingresos_detalle li ON li.lote_id = ls.id
                LEFT JOIN lotes_movimientos_salida lms ON lms.lote_id = ls.id

                WHERE ls.producto_id = ?
                AND ls.almacen_id = ?
                AND ls.fecha_ingreso BETWEEN ? AND ?

                GROUP BY ls.id
                ORDER BY ls.fecha_ingreso DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("iiss", $producto_id, $almacen_id, $fecha_inicio, $fecha_fin);
        $stmt->execute();

        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // 🧾 2.1 VENTAS POR LOTE
    public function obtenerVentasLote($lote_id) {

        $sql = "SELECT  
    'VENTA' AS tipo_movimiento,

    lms.lote_id,
    lms.fecha_movimiento,
    lt.codigo_lote AS nombre_lote,

    v.id AS venta_id,
    v.folio,
    v.fecha AS fecha_venta,

    dv.producto_id,
    dv.cantidad,
    c.nombre_comercial AS cliente,

    lms.cantidad_salida,
    lms.precio_venta_pactado,
    lms.costo_compra_historico,

    (lms.precio_venta_pactado - lms.costo_compra_historico) * lms.cantidad_salida AS ganancia,

    ev.id AS entrega_id

FROM lotes_movimientos_salida lms

INNER JOIN detalle_venta dv 
    ON dv.id = lms.detalle_venta_id

INNER JOIN ventas v 
    ON v.id = dv.venta_id

INNER JOIN lotes_stock lt 
    ON lms.lote_id = lt.id

LEFT JOIN entregas_venta ev 
    ON ev.id = lms.entrega_venta_id

INNER JOIN clientes c 
    ON v.id_cliente = c.id


                WHERE lms.lote_id = ?";

        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $lote_id);
        $stmt->execute();

        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
public function obtenerConsumoLotesPorProducto($producto_id, $almacen_id = 0, $fecha_inicio = null, $fecha_fin = null) {
    try {
        // 1. Configurar zona horaria y fechas por defecto (CDMX)
        date_default_timezone_set('America/Mexico_City');
        
        if (empty($fecha_inicio)) {
            $fecha_inicio = date('Y-m-d');
        }
        if (empty($fecha_fin)) {
            $fecha_fin = date('Y-m-d');
        }

        // Aseguramos formato de día completo para el filtrado (de 00:00:00 a 23:59:59)
        $f_inicio_full = $fecha_inicio . " 00:00:00";
        $f_fin_full    = $fecha_fin . " 23:59:59";

        $sql = "SELECT 
            v.id AS venta_id,
            v.folio,
            c.nombre_comercial AS cliente,
            lt.fecha_ingreso AS fecha_de_ingreso,
            lt.id AS lote_id,
            lt.codigo_lote,
            lms.id AS movimiento_id,
            lms.fecha_movimiento,
            lt.cantidad_inicial,
            lms.cantidad_salida,
            -- Saldo histórico acumulado hasta este movimiento específico
            lt.cantidad_inicial - (
                SELECT SUM(lms2.cantidad_salida)
                FROM lotes_movimientos_salida lms2
                WHERE lms2.lote_id = lms.lote_id
                AND lms2.id <= lms.id
            ) AS saldo_final
        FROM lotes_movimientos_salida lms
        INNER JOIN lotes_stock lt ON lt.id = lms.lote_id
        INNER JOIN detalle_venta dv ON dv.id = lms.detalle_venta_id
        INNER JOIN ventas v ON v.id = dv.venta_id
        INNER JOIN clientes c ON c.id = v.id_cliente
        WHERE dv.producto_id = ? 
        AND lms.fecha_movimiento BETWEEN ? AND ?";

        // 2. Agregar filtro de almacén si aplica
        if ($almacen_id != 0) {
            $sql .= " AND lt.almacen_id = ?";
        }

        $sql .= " ORDER BY lms.fecha_movimiento ASC, lms.id ASC";

        $stmt = $this->db->prepare($sql);

        // 3. Binding dinámico de parámetros
        if ($almacen_id != 0) {
            // i = producto, s = fecha_inicio, s = fecha_fin, i = almacen
            $stmt->bind_param("issi", $producto_id, $f_inicio_full, $f_fin_full, $almacen_id);
        } else {
            // i = producto, s = fecha_inicio, s = fecha_fin
            $stmt->bind_param("iss", $producto_id, $f_inicio_full, $f_fin_full);
        }

        $stmt->execute();
        $resultado = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        return [
            'success' => true,
            'data'    => $resultado,
            'filtros' => [ // Útil para depuración en el JS
                'desde' => $f_inicio_full,
                'hasta' => $f_fin_full
            ]
        ];

    } catch (Throwable $e) {
        return [
            'success' => false,
            'message' => $e->getMessage()
        ];
    }
}
    // 🔄 2.2 TRASPASOS
   public function obtenerTraspasos($producto_id, $almacen_id) {

    $sql = "SELECT        
        m.id AS movimiento_id,
        km.lote_origen_id as lote_origen_id,
        km.lote_destino_id as lote_destino_id,
        m.fecha as fecha,
        m.tipo as tipo,
        m.producto_id producto_id,
        m.cantidad as cantidad,
        m.almacen_origen_id as almacen_origen_id,
        m.almacen_destino_id as almacen_destino_id,
        m.referencia_id,
        m.observaciones

    FROM movimientos m
    JOIN kardex_movimientos_lotes km 
        ON m.id = km.movimiento_id

    WHERE m.producto_id = ?
    AND m.tipo = 'traspaso'
    AND (m.almacen_origen_id = ? OR m.almacen_destino_id = ?)";

    $stmt = $this->db->prepare($sql);
    $stmt->bind_param("iii", $producto_id, $almacen_id, $almacen_id);
    $stmt->execute();

    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

    // ⚙️ 2.3 AJUSTES
    public function obtenerAjustes($producto_id, $almacen_id) {

        $sql = "SELECT 
                    'AJUSTE' AS tipo_movimiento,

                    m.id,
                    m.fecha,
                    m.producto_id,
                    m.cantidad,
                    m.observaciones

                FROM movimientos m

                WHERE m.producto_id = ?
                AND m.tipo = 'ajuste'
                AND m.almacen_origen_id = ?";

        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ii", $producto_id, $almacen_id);
        $stmt->execute();

        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // 📦 2.4 ENTRADAS (COMPRAS)
    public function obtenerEntradasLote($lote_id) {

        $sql = "SELECT 
                    'ENTRADA' AS tipo_movimiento,

                    li.lote_id,
                    li.fecha_registro,
                    li.cantidad_recibida,
                    li.costo_aplicado,

                    dc.compra_id

                FROM lotes_ingresos_detalle li

                INNER JOIN detalle_compra dc ON dc.id = li.detalle_compra_id

                WHERE li.lote_id = ?";

        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $lote_id);
        $stmt->execute();

        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // 🔥 EXTRA: TODO EL HISTORIAL JUNTO (para dashboard)
    public function obtenerHistorialCompleto($producto_id, $almacen_id, $lote_id) {

        return [
            'ventas'     => $this->obtenerVentasLote($lote_id),
            'traspasos'  => $this->obtenerTraspasos($producto_id, $almacen_id),
            'ajustes'    => $this->obtenerAjustes($producto_id, $almacen_id),
            'entradas'   => $this->obtenerEntradasLote($lote_id)
        ];
    }

}