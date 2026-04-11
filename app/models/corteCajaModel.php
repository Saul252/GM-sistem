<?php
class CorteCajaModel {
    private $db;

    public function __construct($conexion) {
        $this->db = $conexion;
    }



    // FUNCIÓN 1: TABLA DETALLADA
public function obtenerVentasDetalladas($periodo, $f_inicio, $f_fin, $almacen_id) {
    date_default_timezone_set('America/Mexico_City');
    $target = intval($almacen_id);
    $inicio = $f_inicio;
    $fin = $f_fin;

    if ($periodo !== 'personalizado') {
        $hoy = date('Y-m-d');
        switch ($periodo) {
            case 'ayer': $inicio = $fin = date('Y-m-d', strtotime('-1 day')); break;
            case 'semana': $inicio = date('Y-m-d', strtotime('-7 days')); $fin = $hoy; break;
            case 'mes': $inicio = date('Y-m-01'); $fin = $hoy; break;
            default: $inicio = $fin = $hoy; break;
        }
    }

    $filtroAlmacen  = ($target > 0) ? " AND v.almacen_id = $target " : "";
    $filtroAlmacen2 = ($target > 0) ? " AND v.almacen_id = $target " : "";

    $sql = "SELECT * FROM (

        -- PARTE A: Ventas del día (agrupadas por venta, productos en JSON)
        SELECT 
            v.id,
            v.folio,
            a.nombre AS almacen,
            v.fecha AS fecha_movimiento,
            c.nombre_comercial AS cliente,
            u_vende.nombre AS vendedor,
            -- Productos agrupados en JSON
            CONCAT('[', GROUP_CONCAT(
                JSON_OBJECT(
                    'producto', p.nombre,
                    'cantidad', CONCAT(
                        FLOOR(dv.cantidad / IF(p.factor_conversion > 0, p.factor_conversion, 1)),
                        ' ', IFNULL(p.unidad_reporte, 'Unid.'),
                        IF(MOD(dv.cantidad, IF(p.factor_conversion > 0, p.factor_conversion, 1)) > 0,
                           CONCAT(' + ', MOD(dv.cantidad, IF(p.factor_conversion > 0, p.factor_conversion, 1)), ' pzas'), '')
                    )
                ) SEPARATOR ','
            ), ']') AS productos,
            (SELECT u2.nombre FROM historial_pagos hp2 
             JOIN usuarios u2 ON hp2.usuario_id = u2.id 
             WHERE hp2.venta_id = v.id ORDER BY hp2.id DESC LIMIT 1) AS quien_recibio,
            (SELECT GROUP_CONCAT(DISTINCT hp.metodo_pago SEPARATOR ' + ') 
             FROM historial_pagos hp WHERE hp.venta_id = v.id) AS metodo_pago,
            (SELECT IFNULL(SUM(hp.monto - hp.saldo_favor), 0) 
             FROM historial_pagos hp WHERE hp.venta_id = v.id AND hp.metodo_pago = 'Efectivo') AS efectivo,
            (SELECT IFNULL(SUM(hp.monto - hp.saldo_favor), 0) 
             FROM historial_pagos hp WHERE hp.venta_id = v.id AND hp.metodo_pago IN ('Tarjeta', 'Transferencia')) AS tarjeta_transferencia,
            (SELECT IFNULL(SUM(hp.monto - hp.saldo_favor), 0) 
             FROM historial_pagos hp WHERE hp.venta_id = v.id) AS dinero_real,
            (SELECT IFNULL(SUM(hp.saldo_favor), 0) 
             FROM historial_pagos hp WHERE hp.venta_id = v.id) AS uso_saldo_favor,
            0 AS monto_abono,
            GREATEST(0, v.total - (SELECT IFNULL(SUM(hp.monto), 0) 
             FROM historial_pagos hp WHERE hp.venta_id = v.id)) AS deuda_viva,
            'VENTA DÍA' AS tipo
        FROM ventas v
        INNER JOIN detalle_venta dv ON v.id = dv.venta_id
        INNER JOIN productos p ON dv.producto_id = p.id
        INNER JOIN usuarios u_vende ON v.usuario_id = u_vende.id
        INNER JOIN almacenes a ON v.almacen_id = a.id
        INNER JOIN clientes c ON v.id_cliente = c.id
        WHERE DATE(v.fecha) BETWEEN '$inicio' AND '$fin'
          AND v.estado_general = 'activa'
          $filtroAlmacen
        GROUP BY v.id, v.folio, a.nombre, v.fecha, c.nombre_comercial, u_vende.nombre

        UNION ALL

        -- PARTE B: Abonos de deudas anteriores (agrupados por venta + pago)
        SELECT 
            v.id,
            v.folio,
            a.nombre AS almacen,
            hp_pago.fecha AS fecha_movimiento,
            c.nombre_comercial AS cliente,
            u_vende.nombre AS vendedor,
            -- Productos agrupados en JSON
            CONCAT('[', GROUP_CONCAT(
                JSON_OBJECT(
                    'producto', p.nombre,
                    'cantidad', CONCAT(
                        FLOOR(dv.cantidad / IF(p.factor_conversion > 0, p.factor_conversion, 1)),
                        ' ', IFNULL(p.unidad_reporte, 'Unid.'),
                        IF(MOD(dv.cantidad, IF(p.factor_conversion > 0, p.factor_conversion, 1)) > 0,
                           CONCAT(' + ', MOD(dv.cantidad, IF(p.factor_conversion > 0, p.factor_conversion, 1)), ' pzas'), '')
                    )
                ) SEPARATOR ','
            ), ']') AS productos,
            u_cajero.nombre AS quien_recibio,
            hp_pago.metodo_pago,
            IF(hp_pago.metodo_pago = 'Efectivo', (hp_pago.monto - hp_pago.saldo_favor), 0) AS efectivo,
            IF(hp_pago.metodo_pago IN ('Tarjeta', 'Transferencia'), (hp_pago.monto - hp_pago.saldo_favor), 0) AS tarjeta_transferencia,
            (hp_pago.monto - hp_pago.saldo_favor) AS dinero_real,
            hp_pago.saldo_favor AS uso_saldo_favor,
            hp_pago.monto AS monto_abono,
            GREATEST(0, v.total - (SELECT IFNULL(SUM(monto), 0) 
             FROM historial_pagos WHERE venta_id = v.id)) AS deuda_viva,
            'ABONO DEUDA' AS tipo
        FROM historial_pagos hp_pago
        INNER JOIN ventas v ON hp_pago.venta_id = v.id
            AND v.estado_general = 'activa'
        INNER JOIN detalle_venta dv ON v.id = dv.venta_id
        INNER JOIN productos p ON dv.producto_id = p.id
        INNER JOIN almacenes a ON v.almacen_id = a.id
        INNER JOIN clientes c ON v.id_cliente = c.id
        INNER JOIN usuarios u_cajero ON hp_pago.usuario_id = u_cajero.id
        INNER JOIN usuarios u_vende ON v.usuario_id = u_vende.id
        WHERE DATE(hp_pago.fecha) BETWEEN '$inicio' AND '$fin'
          AND DATE(v.fecha) < '$inicio'
          $filtroAlmacen2
        GROUP BY v.id, v.folio, a.nombre, hp_pago.fecha, c.nombre_comercial, 
                 u_vende.nombre, u_cajero.nombre, hp_pago.metodo_pago,
                 hp_pago.monto, hp_pago.saldo_favor, v.total

    ) AS reporte_final ORDER BY fecha_movimiento DESC";

    $res = $this->db->query($sql);
    $data = [];
    while ($res && $row = $res->fetch_assoc()) {
        // Decodificar el JSON de productos
        $row['productos'] = json_decode($row['productos'], true) ?? [];
        $data[] = $row;
    }
    return $data;
}
    // FUNCIÓN 2: SUMAS TOTALES
public function obtenerSumasCorte($periodo, $f_inicio, $f_fin, $almacen_id)
{
    date_default_timezone_set('America/Mexico_City');
    $target = intval($almacen_id);
    $hoy    = date('Y-m-d');

    // --- 1. CONFIGURACIÓN DE TIEMPO ---
    $inicio = $hoy . ' 00:00:00';
    $fin    = $hoy . ' 23:59:59';

    switch ($periodo) {
        case 'personalizado':
            $inicio = date('Y-m-d', strtotime($f_inicio)) . ' 00:00:00';
            $fin    = date('Y-m-d', strtotime($f_fin))    . ' 23:59:59';
            break;
        case 'ayer':
            $inicio = date('Y-m-d', strtotime('-1 day')) . ' 00:00:00';
            $fin    = date('Y-m-d', strtotime('-1 day')) . ' 23:59:59';
            break;
        case 'semana':
            $inicio = date('Y-m-d', strtotime('-7 days')) . ' 00:00:00';
            $fin    = $hoy . ' 23:59:59';
            break;
        case 'mes':
            $inicio = date('Y-m-01') . ' 00:00:00';
            $fin    = $hoy . ' 23:59:59';
            break;
        default:
            $inicio = $hoy . ' 00:00:00';
            $fin    = $hoy . ' 23:59:59';
            break;
    }

    $filtroV  = ($target > 0) ? " AND v.almacen_id  = $target" : "";
    $filtroHP = ($target > 0) ? " AND v2.almacen_id = $target" : "";
    $filtroG  = ($target > 0) ? " AND almacen_id    = $target" : "";

    $sql = "SELECT 
                (base.ingreso_real + base.favor_usado)                                    AS cobrado_total,
                base.ingreso_real                                                          AS gran_total_ingresos,
                base.favor_usado                                                           AS saldo_favor_usado,
                -- DEUDA REAL: Total de ventas del periodo menos lo que se ha pagado de ESAS ventas
                (base.venta_bruta - base.pagos_de_ventas_periodo)                         AS deuda_pendiente,
                base.total_efectivo,
                base.total_tarjeta,
                base.total_transferencia,
                base.abono_efectivo,
                base.abono_tarjeta,
                base.abono_transferencia,
                (base.abono_efectivo + base.abono_tarjeta + base.abono_transferencia)     AS abonos_totales,
                base.gastos_totales,
                base.compras_totales
            FROM (
                SELECT
                    -- Suma de todas las ventas del periodo (incluye las que no tienen pago)
                    (SELECT IFNULL(SUM(total), 0) FROM ventas v 
                     WHERE v.fecha BETWEEN '$inicio' AND '$fin' 
                       AND v.estado_general = 'activa' $filtroV) AS venta_bruta,

                    -- Cuánto se ha pagado específicamente de las ventas de este periodo
                    (SELECT IFNULL(SUM(hp.monto), 0) FROM historial_pagos hp 
                     INNER JOIN ventas v2 ON hp.venta_id = v2.id 
                     WHERE v2.fecha BETWEEN '$inicio' AND '$fin' 
                       AND v2.estado_general = 'activa' $filtroHP) AS pagos_de_ventas_periodo,

                    -- Dinero real que entró HOY (independientemente de cuándo fue la venta)
                    (SELECT IFNULL(SUM(monto - saldo_favor), 0) FROM historial_pagos hp 
                     INNER JOIN ventas v2 ON hp.venta_id = v2.id 
                     WHERE hp.fecha BETWEEN '$inicio' AND '$fin' 
                       AND v2.estado_general = 'activa' $filtroHP) AS ingreso_real,

                    (SELECT IFNULL(SUM(saldo_favor), 0) FROM historial_pagos hp 
                     INNER JOIN ventas v2 ON hp.venta_id = v2.id 
                     WHERE hp.fecha BETWEEN '$inicio' AND '$fin' 
                       AND v2.estado_general = 'activa' $filtroHP) AS favor_usado,

                    -- Desglose por método de dinero entrante hoy
                    (SELECT IFNULL(SUM(monto - saldo_favor), 0) FROM historial_pagos hp INNER JOIN ventas v2 ON hp.venta_id = v2.id 
                     WHERE hp.fecha BETWEEN '$inicio' AND '$fin' AND v2.estado_general = 'activa' AND hp.metodo_pago = 'Efectivo' $filtroHP) AS total_efectivo,

                    (SELECT IFNULL(SUM(monto - saldo_favor), 0) FROM historial_pagos hp INNER JOIN ventas v2 ON hp.venta_id = v2.id 
                     WHERE hp.fecha BETWEEN '$inicio' AND '$fin' AND v2.estado_general = 'activa' AND hp.metodo_pago = 'Tarjeta' $filtroHP) AS total_tarjeta,

                    (SELECT IFNULL(SUM(monto - saldo_favor), 0) FROM historial_pagos hp INNER JOIN ventas v2 ON hp.venta_id = v2.id 
                     WHERE hp.fecha BETWEEN '$inicio' AND '$fin' AND v2.estado_general = 'activa' AND hp.metodo_pago = 'Transferencia' $filtroHP) AS total_transferencia,

                    -- Abonos (Ventas de antes, pagadas hoy)
                    (SELECT IFNULL(SUM(hp.monto - hp.saldo_favor), 0) FROM historial_pagos hp INNER JOIN ventas v2 ON hp.venta_id = v2.id 
                     WHERE hp.fecha BETWEEN '$inicio' AND '$fin' AND v2.fecha < '$inicio' AND hp.metodo_pago = 'Efectivo' $filtroHP) AS abono_efectivo,

                    (SELECT IFNULL(SUM(hp.monto - hp.saldo_favor), 0) FROM historial_pagos hp INNER JOIN ventas v2 ON hp.venta_id = v2.id 
                     WHERE hp.fecha BETWEEN '$inicio' AND '$fin' AND v2.fecha < '$inicio' AND hp.metodo_pago = 'Tarjeta' $filtroHP) AS abono_tarjeta,

                    (SELECT IFNULL(SUM(hp.monto - hp.saldo_favor), 0) FROM historial_pagos hp INNER JOIN ventas v2 ON hp.venta_id = v2.id 
                     WHERE hp.fecha BETWEEN '$inicio' AND '$fin' AND v2.fecha < '$inicio' AND hp.metodo_pago = 'Transferencia' $filtroHP) AS abono_transferencia,

                    -- Gastos y Compras
                    (SELECT IFNULL(SUM(total), 0) FROM gastos 
                     WHERE fecha_registro BETWEEN '$inicio' AND '$fin' AND estado = 'pagado' $filtroG) AS gastos_totales,

                    (SELECT IFNULL(SUM(total), 0) FROM compras 
                     WHERE fecha_registro BETWEEN '$inicio' AND '$fin' AND estado = 'confirmada' $filtroG) AS compras_totales
            ) AS base";

    $res = $this->db->query($sql);
    if (!$res) return ['error' => $this->db->error];

    $row = $res->fetch_assoc();

    return [
        'cobrado_total'       => (float)$row['cobrado_total'],
        'total_efectivo'      => (float)$row['total_efectivo'],
        'total_tarjeta'       => (float)$row['total_tarjeta'],
        'total_transferencia' => (float)$row['total_transferencia'],
        'abono_efectivo'      => (float)$row['abono_efectivo'],
        'abono_tarjeta'       => (float)$row['abono_tarjeta'],
        'abono_transferencia' => (float)$row['abono_transferencia'],
        'abonos_totales'      => (float)$row['abonos_totales'],
        'saldo_favor_usado'   => (float)$row['saldo_favor_usado'],
        'gran_total_ingresos' => (float)$row['gran_total_ingresos'],
        'deuda_pendiente'     => (float)$row['deuda_pendiente'],
        'gastos_totales'      => (float)$row['gastos_totales'],
        'compras_totales'     => (float)$row['compras_totales'],
        'diadehoy'            => $hoy,
    ];
}

public function obtenerSumasCorteCaja($filtros, $almacen_id_target) {
    date_default_timezone_set('America/Mexico_City');
    $target = intval($almacen_id_target);
    $ahora = date('Y-m-d H:i:s');

    // --- 1. CONFIGURACIÓN DE TIEMPO ---
    $periodo = $filtros['periodo'] ?? 'hoy';
    $inicioFiltro = date('Y-m-d 00:00:00'); 
    $fin = $ahora;

    if ($periodo !== 'personalizado') {
        switch ($periodo) {
            case 'ayer': 
                $inicioFiltro = date('Y-m-d 00:00:00', strtotime('-1 day'));
                $fin = date('Y-m-d 23:59:59', strtotime('-1 day'));
                break;
            case 'semana': 
                $inicioFiltro = date('Y-m-d 00:00:00', strtotime('-7 days')); 
                break;
            case 'mes': 
                $inicioFiltro = date('Y-m-01 00:00:00'); 
                break;
        }
    } else {
        $inicioFiltro = $filtros['f_inicio'] . " 00:00:00";
        $fin = $filtros['f_fin'] . " 23:59:59";
    }

    // --- 2. DETERMINAR INICIO REAL (DESDE ÚLTIMO CORTE) ---
    $sqlUltimo = "SELECT CONCAT(fecha_corte, ' ', hora_cierre) as ultimo_cierre 
                  FROM corte_de_caja 
                  WHERE almacen_id = $target 
                  ORDER BY id DESC LIMIT 1";
    $resUltimo = $this->db->query($sqlUltimo);
    $inicioReal = $inicioFiltro;

    if ($resUltimo && $resUltimo->num_rows > 0) {
        $datoCorte = $resUltimo->fetch_assoc();
        $fechaUltimoCorte = $datoCorte['ultimo_cierre'];
        if ($fechaUltimoCorte > $inicioFiltro) {
            $inicioReal = $fechaUltimoCorte;
        }
    }

    // --- 3. CONDICIONES ESTRICTAS ---
    $filtroAlmacenV  = ($target > 0) ? " AND v.almacen_id = $target"  : "";
    $filtroAlmacenV2 = ($target > 0) ? " AND v2.almacen_id = $target" : "";

    // El estado 'activa' va en el ON del JOIN para excluir ventas canceladas desde el inicio
    $condicionPagosBase = "INNER JOIN ventas v2 
                               ON hp.venta_id = v2.id 
                              AND v2.estado_general = 'activa'
                              $filtroAlmacenV2
                           WHERE hp.fecha > '$inicioReal' 
                             AND hp.fecha <= '$fin'";

    // --- 4. CONSULTA MAESTRA ---
    $sql = "SELECT 
                -- Venta Bruta (Solo activas)
                (SELECT IFNULL(SUM(v.total), 0) 
                 FROM ventas v 
                 WHERE v.fecha > '$inicioReal' 
                   AND v.fecha <= '$fin' 
                   AND v.estado_general = 'activa' 
                   $filtroAlmacenV) AS venta_bruta_total,

                -- Favor Usado (Solo de ventas activas)
                (SELECT IFNULL(SUM(hp.saldo_favor), 0) 
                 FROM historial_pagos hp $condicionPagosBase) AS favor_usado,
                
                -- Ingresos Líquidos por método (Solo de ventas activas)
                (SELECT IFNULL(SUM(CASE WHEN UPPER(hp.metodo_pago) = 'EFECTIVO' 
                               THEN (hp.monto - hp.saldo_favor) ELSE 0 END), 0) 
                 FROM historial_pagos hp $condicionPagosBase) AS efec_puro,

                (SELECT IFNULL(SUM(CASE WHEN UPPER(hp.metodo_pago) = 'TRANSFERENCIA' 
                               THEN (hp.monto - hp.saldo_favor) ELSE 0 END), 0) 
                 FROM historial_pagos hp $condicionPagosBase) AS trans_puro,

                (SELECT IFNULL(SUM(CASE WHEN UPPER(hp.metodo_pago) = 'TARJETA' 
                               THEN (hp.monto - hp.saldo_favor) ELSE 0 END), 0) 
                 FROM historial_pagos hp $condicionPagosBase) AS tarj_puro,
                
                -- Egresos
                (SELECT IFNULL(SUM(total), 0) FROM gastos 
                 WHERE estado = 'pagado' AND almacen_id = $target 
                   AND fecha_registro > '$inicioReal' AND fecha_registro <= '$fin' 
                   AND metodo_pago = 'Efectivo') AS g_efec,

                (SELECT IFNULL(SUM(total), 0) FROM gastos 
                 WHERE estado = 'pagado' AND almacen_id = $target 
                   AND fecha_registro > '$inicioReal' AND fecha_registro <= '$fin' 
                   AND metodo_pago = 'Transferencia') AS g_trans,

                (SELECT IFNULL(SUM(total), 0) FROM gastos 
                 WHERE estado = 'pagado' AND almacen_id = $target 
                   AND fecha_registro > '$inicioReal' AND fecha_registro <= '$fin' 
                   AND metodo_pago = 'Tarjeta') AS g_tarj,

                (SELECT IFNULL(SUM(total), 0) FROM compras 
                 WHERE estado = 'confirmada' AND almacen_id = $target 
                   AND fecha_registro > '$inicioReal' AND fecha_registro <= '$fin') AS compras_total";

    $res = $this->db->query($sql);

    if (!$res) {
        return ['error' => $this->db->error];
    }

    $row = $res->fetch_assoc();

    // --- 5. CÁLCULOS FINALES ---
    $bruta  = floatval($row['venta_bruta_total']);
    $favor  = floatval($row['favor_usado']);

    $efectivo_real = floatval($row['efec_puro']) - floatval($row['g_efec']) - floatval($row['compras_total']);
    $trans_real    = floatval($row['trans_puro']) - floatval($row['g_trans']);
    $tarjeta_real  = floatval($row['tarj_puro'])  - floatval($row['g_tarj']);

    $ingresos_liquidos = floatval($row['efec_puro']) + floatval($row['trans_puro']) + floatval($row['tarj_puro']);
    $cobradoTotal      = $ingresos_liquidos + $favor;

    return [
        'venta_bruta'       => $bruta,
        'efectivo_real'     => $efectivo_real,
        'transferencia'     => $trans_real,
        'tarjeta'           => $tarjeta_real,
        'saldo_favor_usado' => $favor,
        'cobrado_total'     => $cobradoTotal,
        'deuda_pendiente'   => max(0, $bruta - $cobradoTotal),
        'metadata'          => [
            'inicio' => $inicioReal,
            'fin'    => $fin
        ]
    ];
}
// 2. REGISTRAR UN SOLO ALMACÉN
// public function registrarCortePorAlmacen($id_almacen) {
//     date_default_timezone_set('America/Mexico_City');
//     $fecha_dia = date('Y-m-d');
//     $hora_cierre = date('H:i:s'); 

//     $filtros = ['periodo' => 'personalizado', 'f_inicio' => $fecha_dia, 'f_fin' => $fecha_dia];
//     $totales = $this->obtenerSumasCorte($filtros, $id_almacen);
    
//     // Asegurar que el usuario_id sea un entero
//     $usuario_id = isset($_SESSION['usuario_id']) ? intval($_SESSION['usuario_id']) : 1;

//     $sql = "INSERT INTO corte_de_caja (fecha_corte, hora_cierre, almacen_id, venta_bruta, efectivo_real, transferencia, tarjeta, saldo_favor_usado, cobrado_total, deuda_pendiente, usuario_id) 
//             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
//             ON DUPLICATE KEY UPDATE 
//                 hora_cierre = VALUES(hora_cierre),
//                 venta_bruta = VALUES(venta_bruta),
//                 efectivo_real = VALUES(efectivo_real),
//                 transferencia = VALUES(transferencia),
//                 tarjeta = VALUES(tarjeta),
//                 saldo_favor_usado = VALUES(saldo_favor_usado),
//                 cobrado_total = VALUES(cobrado_total),
//                 deuda_pendiente = VALUES(deuda_pendiente),
//                 usuario_id = VALUES(usuario_id)";

//     $stmt = $this->db->prepare($sql);
//     if (!$stmt) {
//         throw new Exception("Error en prepare: " . $this->db->error);
//     }

//     // "ssi" -> fecha (s), hora (s), almacen_id (i)
//     // "ddddddd" -> los 7 montos decimales (double)
//     // "i" -> usuario_id (i)
//     $stmt->bind_param("ssidddddddi", 
//         $fecha_dia, 
//         $hora_cierre, 
//         $id_almacen, 
//         $totales['venta_bruta'], 
//         $totales['efectivo_real'], 
//         $totales['transferencia'], 
//         $totales['tarjeta'], 
//         $totales['saldo_favor'], 
//         $totales['cobrado_total'], 
//         $totales['deuda_pendiente'], 
//         $usuario_id
//     );

//     if ($stmt->execute()) {
//         return ['status' => 'success', 'data' => $totales];
//     } else {
//         throw new Exception("Error al ejecutar el corte: " . $stmt->error);
//     }
// }
/**
 * Verifica si ya existe un registro de corte para un almacén y fecha específicos.
 * * @param string $fecha Formato 'YYYY-MM-DD'
 * @param int $id_almacen El ID del almacén a consultar
 * @return bool True si ya existe, False si no
 */
public function existeCorte($fecha, $id_almacen) {
    // Es vital filtrar por ambos: fecha Y almacén
    $sql = "SELECT id FROM corte_de_caja 
            WHERE fecha_corte = ? 
            AND almacen_id = ? 
            LIMIT 1";

    try {
        $stmt = $this->db->prepare($sql);
        
        // "s" para la fecha (string), "i" para el almacén (int)
        $stmt->bind_param("si", $fecha, $id_almacen);
        $stmt->execute();
        
        $resultado = $stmt->get_result();
        
        // Retorna true si encontró una fila, false si está vacío
        return $resultado->num_rows > 0;

    } catch (Exception $e) {
        // En caso de error de SQL, logueamos y retornamos false 
        // para permitir que el flujo intente el registro
        error_log("Error en existeCorte del Modelo: " . $e->getMessage());
        return false;
    }
}
 public function obtenerAlmacenesPendientes($id_almacen_sesion, $es_admin, $fecha) {
    if ($es_admin) {
        // AUTOMATIZACIÓN ADMIN: Busca almacenes con ventas que NO han cerrado hoy
        $sql = "SELECT DISTINCT a.id, a.nombre 
                FROM almacenes a
                INNER JOIN ventas v ON v.almacen_id = a.id
                WHERE a.activo = 1 
                AND DATE(v.fecha) = ?
                AND v.estado_general = 'activa'
                AND NOT EXISTS (
                    SELECT 1 FROM corte_de_caja c 
                    WHERE c.almacen_id = a.id AND c.fecha_corte = ?
                ) LIMIT 5";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ss", $fecha, $fecha);
    } else {
        // AUTOMATIZACIÓN USUARIO: Revisa si SU almacén ya cerró hoy
        $sql = "SELECT id, nombre FROM almacenes 
                WHERE id = ? 
                AND activo = 1 
                AND NOT EXISTS (
                    SELECT 1 FROM corte_de_caja 
                    WHERE almacen_id = ? AND fecha_corte = ?
                ) LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("iis", $id_almacen_sesion, $id_almacen_sesion, $fecha);
    }

    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
public function agregarCorteManual($datos) {
    date_default_timezone_set('America/Mexico_City');
    
    // --- PARÁMETROS SEPARADOS PARA VISIBILIDAD ---
    // Usamos la fecha que viene del modal, si no existe, usamos hoy
   $fecha_corte         = $datos['fecha_corte'] ?? date('Y-m-d'); 
    $hora_cierre         = date('H:i:s');
    $almacen_id          = intval($datos['almacen_id']);
    
    // Primero extraemos los valores necesarios para el cálculo
    $efectivo_real       = floatval($datos['total_efectivo']);
    $transferencia       = floatval($datos['total_transferencia']);
    $tarjeta             = floatval($datos['total_tarjeta']);
    $abonos_totales      = floatval($datos['abonos_totales']);
    $deuda_pendiente     = floatval($datos['deuda_pendiente']);

    // Ahora calculamos la venta bruta siguiendo tu orden
    $venta_bruta         = ($efectivo_real + $tarjeta + $transferencia + $deuda_pendiente) - $abonos_totales;

    // Continuamos con el resto de las variables en su posición original
    $abono_efectivo      = floatval($datos['abono_efectivo']);
    $abono_tarjeta       = floatval($datos['abono_tarjeta']);
    $abono_transferencia = floatval($datos['abono_transferencia']);
    $saldo_favor_usado   = floatval($datos['saldo_favor_usado']);
    $cobrado_total       = floatval($datos['cobrado_total']);
    $gastos_totales      = floatval($datos['gastos_totales']);
    $compras_totales     = floatval($datos['compras_totales']);
    $gran_total_ingresos = floatval($datos['gran_total_ingresos']);
    $usuario_id          = intval($datos['usuario_id']);
    $observaciones       = $datos['observaciones'] ?? '';
    $created_at          = date('Y-m-d H:i:s');

    /* OPCIONAL: Descomenta la siguiente línea para ver los datos en la consola 
       de red del navegador (Network tab) antes de que se inserten.
    */
    // var_dump($fecha_corte, $almacen_id, $cobrado_total); die();

    $sql = "INSERT INTO `corte_de_caja` (
                `fecha_corte`, 
                `hora_cierre`, 
                `almacen_id`, 
                `venta_bruta`, 
                `efectivo_real`, 
                `transferencia`, 
                `tarjeta`, 
                `abono_efectivo`,
                `abono_tarjeta`,
                `abono_transferencia`,
                `abonos_totales`,
                `saldo_favor_usado`, 
                `cobrado_total`, 
                `gastos_totales`,
                `compras_totales`,
                `gran_total_ingresos`,
                `deuda_pendiente`, 
                `usuario_id`, 
                `observaciones`, 
                `created_at`
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $this->db->prepare($sql);

    // Ajustamos el string de tipos (20 parámetros)
    // s = string, i = integer, d = double (float)
    // Orden: s, s, i, d, d, d, d, d, d, d, d, d, d, d, d, d, d, i, s, s
    $tipos = "ssiddddddddddddddiss";

    $stmt->bind_param(
        $tipos, 
        $fecha_corte,
        $hora_cierre,
        $almacen_id,
        $venta_bruta,
        $efectivo_real,
        $transferencia,
        $tarjeta,
        $abono_efectivo,
        $abono_tarjeta,
        $abono_transferencia,
        $abonos_totales,
        $saldo_favor_usado,
        $cobrado_total,
        $gastos_totales,
        $compras_totales,
        $gran_total_ingresos,
        $deuda_pendiente,
        $usuario_id,
        $observaciones,
        $created_at
    );

    if ($stmt->execute()) {
        return ['status' => 'success', 'id' => $this->db->insert_id];
    } else {
        return ['status' => 'error', 'message' => $this->db->error];
    }
}

}