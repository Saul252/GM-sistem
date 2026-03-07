<?php
class CorteCajaModel {
    private $db;

    public function __construct($conexion) {
        $this->db = $conexion;
    }

    public function obtenerVentasDetalladas($filtros, $almacen_usuario_sesion) {
        // 1. Capturar filtros con valores por defecto
        $periodo = $filtros['periodo'] ?? 'hoy';
        $almacen_id = intval($filtros['almacen_id'] ?? 0);
        
        $hoy = date('Y-m-d');
        $inicio = $hoy; 
        $fin = $hoy;

        // 2. Lógica de fechas
        if ($periodo !== 'personalizado') {
            switch ($periodo) {
                case 'ayer': 
                    $inicio = date('Y-m-d', strtotime('-1 day')); 
                    $fin = $inicio; 
                    break;
                case 'semana': 
                    $inicio = date('Y-m-d', strtotime('-7 days')); 
                    break;
                case 'mes': 
                    $inicio = date('Y-m-01'); 
                    break;
            }
        } else {
            $inicio = !empty($filtros['f_inicio']) ? $filtros['f_inicio'] : $hoy;
            $fin = !empty($filtros['f_fin']) ? $filtros['f_fin'] : $hoy;
        }

        // 3. Seguridad de almacén (Si el usuario tiene almacén fijo, se ignora el filtro)
        $target = ($almacen_usuario_sesion > 0) ? $almacen_usuario_sesion : $almacen_id;

        $where = "WHERE DATE(v.fecha) BETWEEN '$inicio' AND '$fin' AND v.estado_general = 'activa'";
        if ($target > 0) {
            $where .= " AND v.almacen_id = $target";
        }

        // 4. Consulta SQL Avanzada (Incluye Pagos y Entregas)
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
            // --- LÓGICA DE CONVERSIÓN DE UNIDADES ---
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

            // --- LÓGICA DE AUDITORÍA (DINERO Y MATERIAL) ---
            $pagado = floatval($row['total_pagado']);
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
                // Campos de auditoría para la vista
                'deuda_dinero'      => $deuda_dinero,
                'pendiente_material' => $pendiente_material,
                'estado_entrega'    => $row['estado_entrega']
            ];
        }
        return $data;
    }
}