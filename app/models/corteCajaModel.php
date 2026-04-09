<?php
class CorteCajaModel {
    private $db;

    public function __construct($conexion) {
        $this->db = $conexion;
    }

   public function obtenerVentasDetalladas($filtros, $almacen_usuario_sesion) {
    // 1. Capturar filtros (Sin cambios)
    $periodo = $filtros['periodo'] ?? 'hoy';
    $almacen_id = intval($filtros['almacen_id'] ?? 0);
    
    $hoy = date('Y-m-d');
    $inicio = $hoy; 
    $fin = $hoy;

    // 2. Lógica de fechas (Sin cambios)
    if ($periodo !== 'personalizado') {
        switch ($periodo) {
            case 'ayer': $inicio = date('Y-m-d', strtotime('-1 day')); $fin = $inicio; break;
            case 'semana': $inicio = date('Y-m-d', strtotime('-7 days')); break;
            case 'mes': $inicio = date('Y-m-01'); break;
        }
    } else {
        $inicio = !empty($filtros['f_inicio']) ? $filtros['f_inicio'] : $hoy;
        $fin = !empty($filtros['f_fin']) ? $filtros['f_fin'] : $hoy;
    }

    $target = ($almacen_usuario_sesion > 0) ? $almacen_usuario_sesion : $almacen_id;
    $where = "WHERE DATE(v.fecha) BETWEEN '$inicio' AND '$fin' AND v.estado_general = 'activa'";
    if ($target > 0) { $where .= " AND v.almacen_id = $target"; }

    // 3. Consulta SQL Actualizada
    // Se agregó la subconsulta para 'total_saldo_favor'
    $sql = "SELECT 
                v.id as venta_id, 
                v.folio, 
                v.fecha, 
                v.total as venta_total, 
                v.estado_entrega,
                a.nombre as almacen_nom,
                u.nombre as vendedor_nom,
                dv.cantidad, 
                dv.cantidad_entregada, 
                dv.subtotal as linea_subtotal,
                p.nombre as prod_nom, 
                p.sku, 
                p.factor_conversion, 
                p.unidad_reporte,
                (SELECT IFNULL(SUM(monto), 0) FROM historial_pagos WHERE venta_id = v.id) as total_pagado,
                (SELECT IFNULL(SUM(saldo_favor), 0) FROM historial_pagos WHERE venta_id = v.id) as total_saldo_favor,
                (SELECT GROUP_CONCAT(DISTINCT metodo_pago SEPARATOR ', ') FROM historial_pagos WHERE venta_id = v.id) as metodos
            FROM ventas v
            INNER JOIN detalle_venta dv ON v.id = dv.venta_id
            INNER JOIN productos p ON dv.producto_id = p.id
            INNER JOIN almacenes a ON v.almacen_id = a.id
            INNER JOIN usuarios u ON v.usuario_id = u.id
            $where
            ORDER BY v.fecha DESC";

    $res = $this->db->query($sql);
    if (!$res) return []; 

    $data = [];
    while ($row = $res->fetch_assoc()) {
        // --- LÓGICA DE CONVERSIÓN DE UNIDADES (Sin cambios) ---
        $cant = floatval($row['cantidad']);
        $fact = floatval($row['factor_conversion']) ?: 1;
        $unidad = $row['unidad_reporte'] ?: 'Unid.';
        
        if ($fact > 1 && $cant >= $fact) {
            $mayores = floor($cant / $fact);
            $resto = $cant % $fact;
            $txt_cant = "<b>$mayores $unidad</b>" . ($resto > 0 ? " + $resto pzas" : "");
        } else {
            $txt_cant = "<b>$cant</b> pzas";
        }

        // --- LÓGICA DE AUDITORÍA ACTUALIZADA ---
        $pagado = floatval($row['total_pagado']);
        $saldo_favor_vta = floatval($row['total_saldo_favor']); // Nueva captura
        $total_vta = floatval($row['venta_total']);
        $deuda_dinero = $total_vta - $pagado;

        $cant_pedida = floatval($row['cantidad']);
        $cant_entregada = floatval($row['cantidad_entregada']);
        $pendiente_material = $cant_pedida - $cant_entregada;

        $data[] = [
            'folio'             => $row['folio'],
            'fecha'             => date('H:i', strtotime($row['fecha'])),
            'almacen'           => $row['almacen_nom'],
            'vendedor'          => $row['vendedor_nom'],
            'producto'          => $row['prod_nom'],
            'sku'               => $row['sku'],
            'cantidad_texto'    => $txt_cant,
            'metodo'            => $row['metodos'] ?: 'Pendiente',
            'monto'             => $row['linea_subtotal'],
            // Campos de auditoría ajustados
            'deuda_dinero'      => $deuda_dinero,
            'saldo_favor_usado' => $saldo_favor_vta, // Se envía a la vista
            'monto_real_efectivo'=> $pagado - $saldo_favor_vta, // Útil para el corte de caja físico
            'pendiente_material' => $pendiente_material,
            'estado_entrega'    => $row['estado_entrega']
        ];
    }
    return $data;
}
// 1. OBTENER SUMAS (Se mantiene casi igual, pero aseguramos el filtrado)
public function obtenerSumasCorte($filtros, $almacen_id_target) {
    date_default_timezone_set('America/Mexico_City');
    
    $periodo = $filtros['periodo'] ?? 'hoy';
    $hoy = date('Y-m-d');
    $inicio = $hoy; $fin = $hoy;

    if ($periodo !== 'personalizado') {
        switch ($periodo) {
            case 'ayer': $inicio = date('Y-m-d', strtotime('-1 day')); $fin = $inicio; break;
            case 'semana': $inicio = date('Y-m-d', strtotime('-7 days')); break;
            case 'mes': $inicio = date('Y-m-01'); break;
        }
    } else {
        $inicio = $filtros['f_inicio'];
        $fin = $filtros['f_fin'];
    }

    // Filtro estricto por el ID del almacén que se está iterando
    $target = intval($almacen_id_target);
    $condicionAlmacen = ($target > 0) ? " AND v2.almacen_id = $target" : "";
    $condicionAlmacenVenta = ($target > 0) ? " AND v.almacen_id = $target" : "";
    
    $condicionPagos = "INNER JOIN ventas v2 ON hp.venta_id = v2.id 
                       WHERE DATE(v2.fecha) BETWEEN '$inicio' AND '$fin' 
                       AND v2.estado_general = 'activa' $condicionAlmacen";

    $sql = "SELECT 
                SUM(v.total) as venta_bruta_total,
                (SELECT IFNULL(SUM(hp.saldo_favor), 0) FROM historial_pagos hp $condicionPagos) as total_saldo_favor,
                (SELECT IFNULL(SUM(CASE WHEN hp.metodo_pago = 'EFECTIVO' THEN (hp.monto - hp.saldo_favor) ELSE 0 END), 0) 
                 FROM historial_pagos hp $condicionPagos) as efectivo_puro,
                (SELECT IFNULL(SUM(CASE WHEN hp.metodo_pago = 'TRANSFERENCIA' THEN (hp.monto - hp.saldo_favor) ELSE 0 END), 0) 
                 FROM historial_pagos hp $condicionPagos) as transferencia_puro,
                (SELECT IFNULL(SUM(CASE WHEN hp.metodo_pago = 'TARJETA' THEN (hp.monto - hp.saldo_favor) ELSE 0 END), 0) 
                 FROM historial_pagos hp $condicionPagos) as tarjeta_puro
            FROM ventas v
            WHERE DATE(v.fecha) BETWEEN '$inicio' AND '$fin' 
            AND v.estado_general = 'activa' $condicionAlmacenVenta";

    $res = $this->db->query($sql);
    $row = $res->fetch_assoc();

    $bruta = floatval($row['venta_bruta_total']);
    $favor = floatval($row['total_saldo_favor']);
    $efectivo = floatval($row['efectivo_puro']);
    $trans = floatval($row['transferencia_puro']);
    $tarjeta = floatval($row['tarjeta_puro']);
    $cobradoTotal = $efectivo + $trans + $tarjeta + $favor;

    return [
        'venta_bruta'    => $bruta,
        'efectivo_real'  => $efectivo,
        'transferencia'  => $trans,
        'tarjeta'        => $tarjeta,
        'saldo_favor'    => $favor,
        'cobrado_total'  => $cobradoTotal,
        'deuda_pendiente'=> max(0, $bruta - $cobradoTotal)
    ];
}

// 2. REGISTRAR UN SOLO ALMACÉN
public function registrarCortePorAlmacen($id_almacen) {
    date_default_timezone_set('America/Mexico_City');
    $fecha_dia = date('Y-m-d');
    $ahora = date('Y-m-d H:i:s'); 

    $filtros = ['periodo' => 'personalizado', 'f_inicio' => $fecha_dia, 'f_fin' => $fecha_dia];
    $totales = $this->obtenerSumasCorte($filtros, $id_almacen);
    
    $usuario_id = $_SESSION['usuario_id'] ?? 1;

    $sql = "INSERT INTO corte_de_caja (fecha_corte, hora_cierre, almacen_id, venta_bruta, efectivo_real, transferencia, tarjeta, saldo_favor_usado, cobrado_total, deuda_pendiente, usuario_id) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE 
                hora_cierre = VALUES(hora_cierre),
                venta_bruta = VALUES(venta_bruta),
                efectivo_real = VALUES(efectivo_real),
                transferencia = VALUES(transferencia),
                tarjeta = VALUES(tarjeta),
                saldo_favor_usado = VALUES(saldo_favor_usado),
                cobrado_total = VALUES(cobrado_total),
                deuda_pendiente = VALUES(deuda_pendiente),
                usuario_id = VALUES(usuario_id)";

    $stmt = $this->db->prepare($sql);
    $hora_cierre = date('H:i:s');
    $stmt->bind_param("ssidddddddi", $fecha_dia, $hora_cierre, $id_almacen, $totales['venta_bruta'], $totales['efectivo_real'], $totales['transferencia'], $totales['tarjeta'], $totales['saldo_favor'], $totales['cobrado_total'], $totales['deuda_pendiente'], $usuario_id);

    if ($stmt->execute()) {
        return ['status' => 'success', 'data' => $totales];
    } else {
        return ['status' => 'error', 'message' => $this->db->error];
    }
}
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
}