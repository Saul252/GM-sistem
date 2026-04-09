<?php
date_default_timezone_set('America/Mexico_City');
class CorteCajaModel {
    private $db;

    public function __construct($conexion) {
        $this->db = $conexion;
    }

    // --- NUEVO MÉTODO PRIVADO PARA LA LÓGICA DE ARRASTRE ---
    private function obtenerUltimoCierreInfo($id_almacen) {
        $sql = "SELECT fecha_corte, hora_cierre 
                FROM corte_de_caja 
                WHERE almacen_id = ? 
                ORDER BY fecha_corte DESC, hora_cierre DESC LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id_almacen);
        $stmt->execute();
        $res = $stmt->get_result();
        return $res->fetch_assoc() ?: null;
    }

    public function obtenerVentasDetalladas($filtros, $almacen_usuario_sesion) {
        $periodo = $filtros['periodo'] ?? 'hoy';
        $almacen_id = intval($filtros['almacen_id'] ?? 0);
        $hoy = date('Y-m-d');
        $inicio = $hoy; 
        $fin = $hoy;

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

            $pagado = floatval($row['total_pagado']);
            $saldo_favor_vta = floatval($row['total_saldo_favor']); 
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
                'deuda_dinero'      => $deuda_dinero,
                'saldo_favor_usado' => $saldo_favor_vta, 
                'monto_real_efectivo'=> $pagado - $saldo_favor_vta, 
                'pendiente_material'=> $pendiente_material,
                'estado_entrega'    => $row['estado_entrega']
            ];
        }
        return $data;
    }

public function obtenerSumasCorte($filtros, $almacen_id_target) {
    date_default_timezone_set('America/Mexico_City');
    
    // El target 0 significa "Todos" (Admin)
    $target = intval($almacen_id_target);
    $ahora = date('Y-m-d H:i:s');
    $periodo = $filtros['periodo'] ?? '';

    // --- 1. DETERMINAR EL RANGO DE TIEMPO (FILTRO > ARRASTRE > HOY) ---
    $desde = null;

    if (!empty($periodo)) {
        // PRIORIDAD 1: Si el usuario movió el selector de fechas
        switch ($periodo) {
            case 'hoy': $desde = date('Y-m-d 00:00:00'); break;
            case 'ayer': 
                $desde = date('Y-m-d 00:00:00', strtotime('-1 day')); 
                $ahora = date('Y-m-d 23:59:59', strtotime('-1 day')); 
                break;
            case 'semana': $desde = date('Y-m-d 00:00:00', strtotime('-7 days')); break;
            case 'mes': $desde = date('Y-m-01 00:00:00'); break;
        }
    }

    if (!$desde) {
        // PRIORIDAD 2: Si no hay filtro, buscamos el último corte (Arrastre)
        $ultimoCorte = $this->obtenerUltimoCierreInfo($target);
        if ($ultimoCorte) {
            $desde = $ultimoCorte['fecha_corte'] . ' ' . $ultimoCorte['hora_cierre'];
        } else {
            // PRIORIDAD 3: Si no hay nada de lo anterior, lo del día actual
            $desde = date('Y-m-d 00:00:00');
        }
    }

    // --- 2. LÓGICA DE FILTRO POR ALMACÉN (ADMINISTRADOR) ---
    // Si $target es 0, las variables se quedan vacías -> El SQL suma todos los registros.
    // Si $target > 0, se inyecta el AND para filtrar esa sucursal.
    $fAlmacenV     = ($target > 0) ? " AND v.almacen_id = $target " : "";
    $fAlmacenV2    = ($target > 0) ? " AND v2.almacen_id = $target " : "";
    $fAlmacenGral  = ($target > 0) ? " AND almacen_id = $target " : "";

    // --- 3. EJECUCIÓN DE CONSULTAS ---
    $condicionTemporal = "v.fecha > '$desde' AND v.fecha <= '$ahora'";
    $condicionTempPagos = "v2.fecha > '$desde' AND v2.fecha <= '$ahora'";
    $condicionGral = "fecha_registro > '$desde' AND fecha_registro <= '$ahora'";

    // Consulta de Ventas y Pagos
    $condicionPagos = "INNER JOIN ventas v2 ON hp.venta_id = v2.id 
                       WHERE $condicionTempPagos 
                       AND v2.estado_general = 'activa' $fAlmacenV2";

    $sqlVentas = "SELECT 
                SUM(v.total) as venta_bruta_total,
                (SELECT IFNULL(SUM(hp.saldo_favor), 0) FROM historial_pagos hp $condicionPagos) as total_saldo_favor,
                (SELECT IFNULL(SUM(CASE WHEN hp.metodo_pago = 'EFECTIVO' THEN (hp.monto - hp.saldo_favor) ELSE 0 END), 0) FROM historial_pagos hp $condicionPagos) as efectivo_puro,
                (SELECT IFNULL(SUM(CASE WHEN hp.metodo_pago = 'TRANSFERENCIA' THEN (hp.monto - hp.saldo_favor) ELSE 0 END), 0) FROM historial_pagos hp $condicionPagos) as transferencia_puro,
                (SELECT IFNULL(SUM(CASE WHEN hp.metodo_pago = 'TARJETA' THEN (hp.monto - hp.saldo_favor) ELSE 0 END), 0) FROM historial_pagos hp $condicionPagos) as tarjeta_puro
            FROM ventas v
            WHERE $condicionTemporal 
            AND v.estado_general = 'activa' $fAlmacenV";

    $resVentas = $this->db->query($sqlVentas);
    $vnt = $resVentas->fetch_assoc();

    // Consulta de Gastos
    $sqlGastos = "SELECT metodo_pago, SUM(total) as total FROM gastos 
                  WHERE estado = 'pagado' $fAlmacenGral AND $condicionGral 
                  GROUP BY metodo_pago";
    $resG = $this->db->query($sqlGastos);
    $gastosArr = ['Efectivo' => 0, 'Transferencia' => 0, 'Tarjeta' => 0];
    while($g = ($resG ? $resG->fetch_assoc() : null)){ 
        $m = ucfirst(strtolower($g['metodo_pago']));
        if(isset($gastosArr[$m])) $gastosArr[$m] = floatval($g['total']); 
    }

    // Consulta de Compras e Ingresos Diversos
    $sqlCompras = "SELECT SUM(total) as total FROM compras WHERE estado = 'confirmada' $fAlmacenGral AND $condicionGral";
    $totalCompras = ($this->db->query($sqlCompras))->fetch_assoc()['total'] ?? 0;

    $sqlIngresos = "SELECT metodo_pago, SUM(monto) as total FROM caja_ingresos WHERE 1=1 $fAlmacenGral AND $condicionGral GROUP BY metodo_pago";
    $resI = $this->db->query($sqlIngresos);
    $ingresosDiv = ['Efectivo' => 0, 'Transferencia' => 0, 'Tarjeta' => 0];
    while($i = ($resI ? $resI->fetch_assoc() : null)){ 
        $m = ucfirst(strtolower($i['metodo_pago']));
        if(isset($ingresosDiv[$m])) $ingresosDiv[$m] = floatval($i['total']); 
    }

    // Arqueos (Salidas/Ajustes)
    $sqlArqueos = "SELECT SUM(monto) as total FROM caja_arqueos WHERE 1=1 $fAlmacenGral AND fecha_hora > '$desde' AND fecha_hora <= '$ahora'";
    $totalArqueos = ($this->db->query($sqlArqueos))->fetch_assoc()['total'] ?? 0;

    // --- 4. CÁLCULOS FINALES ---
    $bruta = floatval($vnt['venta_bruta_total'] ?? 0);
    $favor = floatval($vnt['total_saldo_favor'] ?? 0);
    
    $efectivoFinal = floatval($vnt['efectivo_puro'] ?? 0) + $ingresosDiv['Efectivo'] - $gastosArr['Efectivo'] - floatval($totalCompras) - floatval($totalArqueos);

    return [
        'venta_bruta'       => $bruta,
        'efectivo_real'     => $efectivoFinal, 
        'transferencia'     => floatval($vnt['transferencia_puro'] ?? 0) + $ingresosDiv['Transferencia'] - $gastosArr['Transferencia'],
        'tarjeta'           => floatval($vnt['tarjeta_puro'] ?? 0) + $ingresosDiv['Tarjeta'] - $gastosArr['Tarjeta'],
        'saldo_favor'       => $favor,
        'total_gastos'      => array_sum($gastosArr),
        'total_compras'     => $totalCompras,
        'total_arqueos'     => $totalArqueos,
        'total_ingresos'    => array_sum($ingresosDiv),
        'fecha_inicio'      => $desde,
        'fecha_fin'         => $ahora,
        'target_aplicado'   => $target // Informativo: 0 es "Global"
    ];
}
   public function registrarCortePorAlmacen($id_almacen) {
    date_default_timezone_set('America/Mexico_City');
    $fecha_dia = date('Y-m-d');
    $hora_cierre = date('H:i:s');

    // 1. Obtenemos los totales netos (Aquí ya viene restado el saldo a favor de cada método)
    $t = $this->obtenerSumasCorte([], $id_almacen);
    
    $usuario_id = isset($_SESSION['usuario_id']) ? intval($_SESSION['usuario_id']) : 1;

    // 2. SQL con todas las columnas de control
    $sql = "INSERT INTO corte_de_caja (
                fecha_corte, hora_cierre, almacen_id, 
                venta_bruta, efectivo_real, transferencia, tarjeta, 
                total_gastos_dia, total_compras_dia, total_arqueos_dia, total_ingresos_extra_dia,
                saldo_favor_usado, cobrado_total, deuda_pendiente, usuario_id
            ) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE 
                hora_cierre = VALUES(hora_cierre),
                venta_bruta = VALUES(venta_bruta),
                efectivo_real = VALUES(efectivo_real),
                transferencia = VALUES(transferencia),
                tarjeta = VALUES(tarjeta),
                total_gastos_dia = VALUES(total_gastos_dia),
                total_compras_dia = VALUES(total_compras_dia),
                total_arqueos_dia = VALUES(total_arqueos_dia),
                total_ingresos_extra_dia = VALUES(total_ingresos_extra_dia),
                saldo_favor_usado = VALUES(saldo_favor_usado),
                cobrado_total = VALUES(cobrado_total),
                deuda_pendiente = VALUES(deuda_pendiente),
                usuario_id = VALUES(usuario_id)";

    $stmt = $this->db->prepare($sql);
    if (!$stmt) { throw new Exception("Error en SQL: " . $this->db->error); }

    // 15 parámetros en total (s=string, i=int, d=double/decimal)
    $stmt->bind_param("ssidddddddddddi", 
        $fecha_dia, 
        $hora_cierre, 
        $id_almacen, 
        $t['venta_bruta'], 
        $t['efectivo_real'], 
        $t['transferencia'], 
        $t['tarjeta'], 
        $t['total_gastos'],
        $t['total_compras'],
        $t['total_arqueos'],
        $t['total_ingresos'],
        $t['saldo_favor'], 
        $t['cobrado_total'], 
        $t['deuda_pendiente'], 
        $usuario_id
    );

    if ($stmt->execute()) {
        return ['status' => 'success', 'data' => $t];
    } else {
        throw new Exception("Error al guardar el corte: " . $stmt->error);
    }
}

    public function existeCorte($fecha, $id_almacen) {
        $sql = "SELECT id FROM corte_de_caja WHERE fecha_corte = ? AND almacen_id = ? LIMIT 1";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("si", $fecha, $id_almacen);
            $stmt->execute();
            $resultado = $stmt->get_result();
            return $resultado->num_rows > 0;
        } catch (Exception $e) {
            error_log("Error en existeCorte: " . $e->getMessage());
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

}