<?php

class HistorialComprasModel {

    private $db;

    public function __construct($conexion) {
        $this->db = $conexion;
    }

    // =====================================================
    // 🔥 TOTALES LOTES
    // =====================================================
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

    // =====================================================
    // 🔥 LISTADO DE LOTES (CON FECHA)
    // =====================================================
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

    MAX(li.detalle_compra_id) AS compra,
    kml.lote_destino_id AS lote_destino,
    kml.lote_origen_id as lote_origen,
    MAX(com.folio) AS folio_compra,

    ls.fecha_ingreso AS fecha_compra,
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
LEFT JOIN kardex_movimientos_lotes kml ON ls.id = kml.lote_destino_id

LEFT JOIN lotes_ingresos_detalle li ON li.lote_id = ls.id
LEFT JOIN detalle_compra dc ON li.detalle_compra_id = dc.id
LEFT JOIN compras com ON dc.compra_id = com.id

LEFT JOIN lotes_movimientos_salida lms ON lms.lote_id = ls.id

WHERE ls.producto_id = ?
AND ls.almacen_id = ?
AND ls.fecha_ingreso BETWEEN ? AND ?
and com.folio!=''

GROUP BY ls.id
ORDER BY ls.fecha_ingreso DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("iiss", $producto_id, $almacen_id, $fecha_inicio, $fecha_fin);
        $stmt->execute();

        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // =====================================================
    // 🧾 VENTAS POR LOTE (SIN CAMBIO)
    // =====================================================
    public function obtenerVentasLote($lote_id) {

    $sql = "SELECT 
    tipo_movimiento,
    documento,
    cliente_proveedor,
    codigo_lote,
    fecha_lote,
    fecha_movimiento,
    cantidad_inicial,
    
    -- CANTIDAD ACTUAL: Es la inicial menos lo que salió en movimientos anteriores
    cantidad_inicial - COALESCE(SUM(cantidad_salida) OVER (
        ORDER BY fecha_movimiento ASC, movimiento_id ASC 
        ROWS BETWEEN UNBOUNDED PRECEDING AND 1 PRECEDING
    ), 0) AS cantidad_actual,
    
    cantidad_salida,
    
    -- SALDO FINAL: Es la inicial menos todo lo que ha salido hasta este momento
    cantidad_inicial - SUM(cantidad_salida) OVER (
        ORDER BY fecha_movimiento ASC, movimiento_id ASC
    ) AS saldo_final,
    
    costo_unitario,
    precio_venta,
    ganancia,
    referencia_extra
FROM (
    -- TU CONSULTA ORIGINAL (Simplificada para que el cálculo lo haga el bloque de arriba)
    SELECT 
        'VENTA' AS tipo_movimiento, v.id AS documento_id, v.folio AS documento, c.nombre_comercial AS cliente_proveedor,
        lt.id AS lote_id, lt.codigo_lote, lt.fecha_ingreso AS fecha_lote, lms.id AS movimiento_id, lms.fecha_movimiento AS fecha_movimiento,
        lt.cantidad_inicial, lms.cantidad_salida, lms.costo_compra_historico AS costo_unitario, lms.precio_venta_pactado AS precio_venta,
        (lms.precio_venta_pactado - lms.costo_compra_historico) * lms.cantidad_salida AS ganancia, '-' AS referencia_extra
    FROM lotes_movimientos_salida lms
    INNER JOIN lotes_stock lt ON lt.id = lms.lote_id
    INNER JOIN detalle_venta dv ON dv.id = lms.detalle_venta_id
    INNER JOIN ventas v ON v.id = dv.venta_id
    INNER JOIN clientes c ON c.id = v.id_cliente
    WHERE lt.id = ?

    UNION ALL

    SELECT 
        'TRASPASO', m.id, COALESCE(m.referencia_id, '-'), '-', 
        lt.id, lt.codigo_lote, lt.fecha_ingreso, m.id, m.fecha, 
        lt.cantidad_inicial, m.cantidad, 0, 0, 0, COALESCE(m.observaciones, '-')
    FROM movimientos m
    JOIN kardex_movimientos_lotes km ON km.movimiento_id = m.id
    JOIN lotes_stock lt ON km.lote_origen_id = lt.id
    WHERE lt.id = ?

    UNION ALL

    SELECT 
        'AJUSTE', m.id, '-', '-', 
        lt.id, lt.codigo_lote, lt.fecha_ingreso, m.id, m.fecha, 
        lt.cantidad_inicial, m.cantidad, 0, 0, 0, COALESCE(m.observaciones, '-')
    FROM movimientos m
    JOIN transmutacion_detalle td ON m.id = td.movimiento_id
    JOIN lotes_stock lt ON td.lote_id = lt.id
    WHERE lt.id = ?

    UNION ALL

    SELECT 
        'MERMA', m.id, '-', '-', 
        lt.id, lt.codigo_lote, lt.fecha_ingreso, m.id, m.fecha, 
        lt.cantidad_inicial, m.cantidad, 0, 0, 0, COALESCE(m.observaciones, '-')
    FROM movimientos m
    JOIN mermas merma ON m.id = merma.movimiento_id
    JOIN lotes_stock lt ON merma.lote_id = lt.id
    WHERE lt.id = ?
) AS t
ORDER BY fecha_movimiento DESC, movimiento_id DESC;";

    $stmt = $this->db->prepare($sql);

    // 🔥 FIX: ahora sí 4 parámetros correctos
    $stmt->bind_param("iiii", $lote_id, $lote_id, $lote_id, $lote_id);

    $stmt->execute();

    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
    // =====================================================
    // 📊 CONSUMO POR PRODUCTO (CON FECHAS CORREGIDO)
    // =====================================================
  public function obtenerConsumoLotesPorProducto($producto_id, $almacen_id = 0, $fecha_inicio = null, $fecha_fin = null) {
    try {

        date_default_timezone_set('America/Mexico_City');

        if (empty($fecha_inicio)) $fecha_inicio = date('Y-m-d');
        if (empty($fecha_fin)) $fecha_fin = date('Y-m-d');

        $f_inicio_full = $fecha_inicio . " 00:00:00";
        $f_fin_full    = $fecha_fin . " 23:59:59";

        $sql = "SELECT 
            v.id AS venta_id,
            v.folio,
            c.nombre_comercial AS cliente,

            lt.id AS lote_id,
            lt.codigo_lote,
            lt.fecha_ingreso AS fecha_de_ingreso,

            lms.id AS movimiento_id,
            lms.fecha_movimiento,

            -- 🔥 1. CANTIDAD INICIAL
            lt.cantidad_inicial AS cantidad_inicial,

            -- 🔥 2. SALDO ANTES DEL MOVIMIENTO
            lt.cantidad_inicial - COALESCE((
                SELECT SUM(lms2.cantidad_salida)
                FROM lotes_movimientos_salida lms2
                WHERE lms2.lote_id = lms.lote_id
                AND lms2.id < lms.id
            ),0) AS cantidad_actual,

            -- 🔥 3. SALIDA DEL MOVIMIENTO
            lms.cantidad_salida,

            -- 🔥 4. SALDO FINAL
            lt.cantidad_inicial - COALESCE((
                SELECT SUM(lms2.cantidad_salida)
                FROM lotes_movimientos_salida lms2
                WHERE lms2.lote_id = lms.lote_id
                AND lms2.id <= lms.id
            ),0) AS saldo_final

        FROM lotes_movimientos_salida lms

        INNER JOIN lotes_stock lt 
            ON lt.id = lms.lote_id

        INNER JOIN detalle_venta dv 
            ON dv.id = lms.detalle_venta_id

        INNER JOIN ventas v 
            ON v.id = dv.venta_id

        INNER JOIN clientes c 
            ON c.id = v.id_cliente

        WHERE dv.producto_id = ?
        AND lms.fecha_movimiento BETWEEN ? AND ?";

        // 🔥 filtro opcional de almacén
        if ($almacen_id != 0) {
            $sql .= " AND lt.almacen_id = ?";
        }

        $sql .= " ORDER BY lt.id ASC, lms.id ASC";

        $stmt = $this->db->prepare($sql);

        if ($almacen_id != 0) {
            $stmt->bind_param(
                "issi",
                $producto_id,
                $f_inicio_full,
                $f_fin_full,
                $almacen_id
            );
        } else {
            $stmt->bind_param(
                "iss",
                $producto_id,
                $f_inicio_full,
                $f_fin_full
            );
        }

        $stmt->execute();

        return [
            'success' => true,
            'data' => $stmt->get_result()->fetch_all(MYSQLI_ASSOC)
        ];

    } catch (Throwable $e) {
        return [
            'success' => false,
            'message' => $e->getMessage()
        ];
    }
}
    // =====================================================
    // 🔁 TRASPASOS (CORREGIDO SQL)
    // =====================================================
 public function obtenerTraspasos($lote_id, $fecha_inicio = null, $fecha_fin = null) {

    $sql = "SELECT        
        m.id AS movimiento_id,

        lt.codigo_lote AS codigo_lote_origen,
        ltd.codigo_lote AS codigo_lote_destino,

        km.lote_origen_id,
        km.lote_destino_id,

        m.fecha,
        m.tipo,
        m.producto_id,
        m.cantidad,
        m.almacen_origen_id,
        a.nombre as nombreOrigen,
        a2.nombre as nombreDestino,
        m.almacen_destino_id,
        m.referencia_id,
        m.observaciones

    FROM movimientos m

    JOIN kardex_movimientos_lotes km 
        ON m.id = km.movimiento_id

    JOIN almacenes a 
        ON m.almacen_origen_id = a.id

    JOIN almacenes a2 
        ON m.almacen_destino_id = a2.id

    JOIN lotes_stock lt 
        ON km.lote_origen_id = lt.id

    LEFT JOIN lotes_stock ltd 
        ON km.lote_destino_id = ltd.id

    WHERE km.lote_origen_id = ?
    AND m.tipo = 'traspaso'";

    // 🔥 FECHAS
    if (!empty($fecha_inicio) && !empty($fecha_fin)) {
        $sql .= " AND DATE(m.fecha) BETWEEN ? AND ?";
    }

    $sql .= " ORDER BY m.fecha ASC";

    $stmt = $this->db->prepare($sql);

    if (!empty($fecha_inicio) && !empty($fecha_fin)) {
        $stmt->bind_param("iss", $lote_id, $fecha_inicio, $fecha_fin);
    } else {
        $stmt->bind_param("i", $lote_id);
    }

    $stmt->execute();

    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
    // =====================================================
    // ⚙️ AJUSTES
    // =====================================================
   public function obtenerAjustes($producto_id, $almacen_id, $fecha_inicio = null, $fecha_fin = null) {

    $sql = "SELECT 
        'AJUSTE' AS tipo_movimiento,
        m.id,
        m.fecha,
        m.producto_id,
        m.cantidad,
        m.observaciones

    FROM movimientos m

    WHERE m.producto_id = ?
  AND m.observaciones LIKE '%Salida Transmutación%'
    AND m.almacen_origen_id = ?";

    // 🔥 filtro opcional por fecha
    if ($fecha_inicio && $fecha_fin) {
        $sql .= " AND m.fecha BETWEEN ? AND ?";
    }

    $sql .= " ORDER BY m.fecha ASC";

    $stmt = $this->db->prepare($sql);

    // 🔥 binding dinámico
    if ($fecha_inicio && $fecha_fin) {
        $stmt->bind_param("iiss", $producto_id, $almacen_id, $fecha_inicio, $fecha_fin);
    } else {
        $stmt->bind_param("ii", $producto_id, $almacen_id);
    }

    $stmt->execute();

    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
    // =====================================================
    // 📦 ENTRADAS
    // =====================================================
    public function obtenerEntradasLote($lote_id, $fecha_inicio = null, $fecha_fin = null) {

    $sql = "SELECT 
        'ENTRADA' AS tipo_movimiento,
        li.lote_id,
        li.fecha_registro,
        li.cantidad_recibida,
        li.costo_aplicado,
        dc.compra_id

    FROM lotes_ingresos_detalle li

    INNER JOIN detalle_compra dc 
        ON dc.id = li.detalle_compra_id

    WHERE li.lote_id = ?";

    // 🔥 filtro opcional por fecha
    if ($fecha_inicio && $fecha_fin) {
        $sql .= " AND li.fecha_registro BETWEEN ? AND ?";
    }

    $sql .= " ORDER BY li.fecha_registro ASC";

    $stmt = $this->db->prepare($sql);

    // 🔥 binding dinámico
    if ($fecha_inicio && $fecha_fin) {
        $stmt->bind_param("iss", $lote_id, $fecha_inicio, $fecha_fin);
    } else {
        $stmt->bind_param("i", $lote_id);
    }

    $stmt->execute();

    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
    // =====================================================
    // 🔥 HISTORIAL COMPLETO
    // =====================================================
    public function obtenerHistorialCompleto($producto_id, $almacen_id, $lote_id) {

        return [
            'ventas'    => $this->obtenerVentasLote($lote_id),
            'traspasos' => $this->obtenerTraspasos($producto_id, $almacen_id),
            'ajustes'   => $this->obtenerAjustes($producto_id, $almacen_id),
            'entradas'  => $this->obtenerEntradasLote($lote_id)
        ];
    }
}