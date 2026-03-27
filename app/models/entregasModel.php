<?php
date_default_timezone_set('America/Mexico_City');

class EntregaModel {
    private $db;

    public function __construct($conexion) {
        $this->db = $conexion;
    }

    // MANTIENE TU FUNCIÓN ORIGINAL DE LISTADO
public function listarSalidasPendientes($filtros, $almacen_usuario_sesion, $es_admin) {
    $periodo = $filtros['periodo'] ?? 'semana';
    $f_inicio_user = $filtros['f_inicio'] ?? '';
    $f_fin_user = $filtros['f_fin'] ?? '';
    $hoy = date('Y-m-d');
    
    $inicio = $hoy; 
    $fin = $hoy;

    if ($periodo !== 'personalizado') {
        switch ($periodo) {
            case 'ayer':   $inicio = date('Y-m-d', strtotime('-1 day')); $fin = $inicio; break;
            case 'semana': $inicio = date('Y-m-d', strtotime('-7 days')); break;
            case 'mes':    $inicio = date('Y-m-01'); break;
            case 'hoy':    $inicio = $hoy; $fin = $hoy; break;
        }
    } else {
        $inicio = !empty($f_inicio_user) ? $f_inicio_user : $hoy;
        $fin = !empty($f_fin_user) ? $f_fin_user : $hoy;
    }

    $almacen_filtro = intval($filtros['almacen_id'] ?? 0);
    $target_almacen = ($almacen_usuario_sesion > 0) ? $almacen_usuario_sesion : $almacen_filtro;

    $where = "WHERE m.tipo = 'salida' 
              AND (m.usuario_recibe_id IS NULL OR m.usuario_recibe_id = 0)
              AND DATE(m.fecha) BETWEEN '$inicio' AND '$fin'
              AND (v.id IS NULL OR v.estado_general = 'activa')
              AND td.id IS NULL"; 
    
    if ($target_almacen > 0) { 
        $where .= " AND m.almacen_origen_id = $target_almacen"; 
    }

    // CAMBIO: Se agregó IFNULL(trm.estado_reparto, 'pendiente') y el LEFT JOIN con transporte_repartos_maestro
    $sql = "SELECT 
                m.*, 
                v.folio as folio_venta,
                p.nombre as prod_nombre, p.sku, p.factor_conversion, p.unidad_reporte,
                a1.nombre as origen_nombre,
                u1.nombre as usuario_nombre,
                IF(rsl.id IS NOT NULL, 1, 0) as ya_despachado,
                IFNULL(trm.estado_reparto, 'pendiente') as estado_reparto
            FROM movimientos m 
            INNER JOIN productos p ON m.producto_id = p.id
            LEFT JOIN ventas v ON m.referencia_id = v.id
            LEFT JOIN almacenes a1 ON m.almacen_origen_id = a1.id
            LEFT JOIN usuarios u1 ON m.usuario_registra_id = u1.id
            LEFT JOIN registro_salida_lotes rsl ON m.id = rsl.movimiento_id
            LEFT JOIN transmutacion_detalle td ON m.id = td.movimiento_id
            LEFT JOIN transporte_repartos_maestro trm ON m.id = trm.entrega_venta_id
            $where 
            GROUP BY m.id 
            ORDER BY m.id DESC";

    $resultado = $this->db->query($sql);
    $data = [];

    if ($resultado) {
        while ($row = $resultado->fetch_assoc()) {
            $data[] = [
                'id'                => $row['id'], 
                'almacen_origen_id' => $row['almacen_origen_id'],
                'folio_venta'       => $row['folio_venta'] ?? '---',
                'fecha_format'      => date('d/m/Y H:i', strtotime($row['fecha'])),
                'producto'          => $row['prod_nombre'],
                'sku'               => $row['sku'],
                'cantidad'          => $row['cantidad'],
                'factor_conversion' => $row['factor_conversion'],
                'unidad_reporte'    => $row['unidad_reporte'] ?? 'PZA',
                'origen'            => $row['origen_nombre'] ?? '---',
                'u_reg'             => $row['usuario_nombre'] ?? 'Sist.',
                'ya_despachado'     => intval($row['ya_despachado']),
                'estado_reparto'    => $row['estado_reparto'] // CAMBIO: Se envía al JS
            ];
        }
    }
    return $data;
}
    // MANTIENE TU FUNCIÓN ORIGINAL DE PROCESO DE STOCK
    public function procesarDespachoFisico($idMovimiento) {
        $this->db->begin_transaction();

        try {
            $sqlMov = "SELECT m.producto_id, m.almacen_origen_id, m.cantidad, m.referencia_id,
                              dv.id as det_venta_id, dv.precio_unitario as precio_pactado,
                              ev.id as entrega_id
                       FROM movimientos m
                       LEFT JOIN detalle_venta dv ON m.referencia_id = dv.venta_id AND m.producto_id = dv.producto_id
                       LEFT JOIN entregas_venta ev ON dv.venta_id = ev.venta_id
                       WHERE m.id = $idMovimiento";
            
            $resMov = $this->db->query($sqlMov);
            $mov = $resMov->fetch_assoc();

            if (!$mov) throw new Exception("Movimiento no encontrado.");

            $prod_id = $mov['producto_id'];
            $alm_id  = $mov['almacen_origen_id'];
            $cantidad_restante = floatval($mov['cantidad']);
            
            $entrega_venta_id = intval($mov['entrega_id'] ?? 0);
            $detalle_venta_id = intval($mov['det_venta_id'] ?? 0);
            $precio_pactado   = floatval($mov['precio_pactado'] ?? 0);

            $sqlLotes = "SELECT id, cantidad_actual, precio_compra_unitario 
                         FROM lotes_stock 
                         WHERE producto_id = $prod_id 
                           AND almacen_id = $alm_id 
                           AND cantidad_actual > 0 
                           AND estado_lote = 'activo'
                         ORDER BY fecha_ingreso ASC";
            
            $resLotes = $this->db->query($sqlLotes);

            if ($resLotes->num_rows == 0) {
                throw new Exception("No hay lotes disponibles en este almacén.");
            }

            while ($cantidad_restante > 0 && $lote = $resLotes->fetch_assoc()) {
                $lote_id = $lote['id'];
                $stock_lote = floatval($lote['cantidad_actual']);
                $costo_historico = $lote['precio_compra_unitario'];

                $a_tomar = min($cantidad_restante, $stock_lote);
                $nuevo_stock_lote = $stock_lote - $a_tomar;
                $nuevo_estado = ($nuevo_stock_lote <= 0) ? 'agotado' : 'activo';

                $this->db->query("UPDATE lotes_stock SET cantidad_actual = $nuevo_stock_lote, estado_lote = '$nuevo_estado' WHERE id = $lote_id");

                $sqlSalida = "INSERT INTO lotes_movimientos_salida (lote_id, entrega_venta_id, detalle_venta_id, cantidad_salida, costo_compra_historico, precio_venta_pactado) 
                              VALUES ($lote_id, $entrega_venta_id, $detalle_venta_id, $a_tomar, $costo_historico, $precio_pactado)";
                
                if (!$this->db->query($sqlSalida)) { throw new Exception("Error al insertar salida de lote."); }

                $cantidad_restante -= $a_tomar;
            }

            $id_usuario =  $_SESSION['usuario_id'] ?? 0;
            if ($id_usuario <= 0) { throw new Exception("Error: Sesión de usuario no válida."); }

            $sqlPuente = "INSERT INTO registro_salida_lotes (movimiento_id, usuario_patio_id, usuario_despacho_id) 
                          VALUES ($idMovimiento, $id_usuario, $id_usuario)";

            if (!$this->db->query($sqlPuente)) { throw new Exception("Error en registro físico: " . $this->db->error); }

            $this->db->commit();
            return ['success' => true, 'message' => 'Despacho procesado y registrado correctamente.'];

        } catch (Exception $e) {
            $this->db->rollback();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    // AJUSTE: SIMULACIÓN CON FACTORES
    public function simularDespachoLotes($idMovimiento) {
        try {
            $resMov = $this->db->query("
                SELECT m.producto_id, m.almacen_origen_id, m.cantidad, 
                       p.factor_conversion, p.unidad_reporte 
                FROM movimientos m 
                INNER JOIN productos p ON m.producto_id = p.id 
                WHERE m.id = $idMovimiento");
            
            $mov = $resMov->fetch_assoc();
            if (!$mov) throw new Exception("Movimiento no encontrado.");

            $prod_id = $mov['producto_id'];
            $alm_id  = $mov['almacen_origen_id'];
            $restante = floatval($mov['cantidad']);
            $factor = floatval($mov['factor_conversion'] ?: 1);

            $sql = "SELECT id, codigo_lote, cantidad_actual, fecha_ingreso 
                    FROM lotes_stock 
                    WHERE producto_id = $prod_id AND almacen_id = $alm_id 
                    AND cantidad_actual > 0 AND estado_lote = 'activo' 
                    ORDER BY fecha_ingreso ASC";
            
            $resLotes = $this->db->query($sql);
            $simulacion = [];

            while ($restante > 0 && $lote = $resLotes->fetch_assoc()) {
                $tomar = min($restante, floatval($lote['cantidad_actual']));
                $simulacion[] = [
                    'lote_id' => $lote['id'],
                    'codigo' => $lote['codigo_lote'],
                    'fecha_entrada' => date('d/m/Y', strtotime($lote['fecha_ingreso'])),
                    'cantidad_en_lote' => $lote['cantidad_actual'],
                    'cantidad_a_extraer' => $tomar,
                    'saldo_final' => $lote['cantidad_actual'] - $tomar
                ];
                $restante -= $tomar;
            }

            return [
                'success' => true, 
                'lotes' => $simulacion, 
                'total_solicitado' => $mov['cantidad'],
                'unidad_reporte' => $mov['unidad_reporte'],
                'factor_conversion' => $factor,
                'pendiente' => $restante
            ];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    // AJUSTE: IMPRESIÓN CON FACTORES
    public function obtenerDatosImpresion($idMovimiento) {
        $sql = "SELECT 
                    m.id as movimiento_id,
                    m.fecha as fecha_solicitud,
                    m.cantidad as cantidad_total,
                    p.nombre as producto,
                    p.sku,
                    p.unidad_reporte,
                    p.factor_conversion,
                    a_orig.nombre as almacen_origen,
                    u_patio.nombre as usuario_despacho,
                    rsl.fecha_despacho,
                    (SELECT GROUP_CONCAT(CONCAT(ls.codigo_lote, ' (', lms.cantidad_salida, ' pzas)') SEPARATOR '\n')
                     FROM lotes_movimientos_salida lms
                     INNER JOIN lotes_stock ls ON lms.lote_id = ls.id
                     WHERE lms.detalle_venta_id = (
                         SELECT dv.id FROM detalle_venta dv 
                         WHERE dv.venta_id = m.referencia_id AND dv.producto_id = m.producto_id LIMIT 1
                     ) OR (m.tipo = 'salida' AND lms.detalle_venta_id = 0 AND ls.producto_id = m.producto_id)
                    ) as detalle_lotes
                FROM movimientos m
                INNER JOIN productos p ON m.producto_id = p.id
                LEFT JOIN almacenes a_orig ON m.almacen_origen_id = a_orig.id
                INNER JOIN registro_salida_lotes rsl ON m.id = rsl.movimiento_id
                LEFT JOIN usuarios u_patio ON rsl.usuario_patio_id = u_patio.id
                WHERE m.id = $idMovimiento";

        $res = $this->db->query($sql);
        $data = $res->fetch_assoc();

        if ($data) {
            $cant = floatval($data['cantidad_total']);
            $factor = floatval($data['factor_conversion'] ?: 1);
            
            if ($factor > 1 && $cant >= $factor) {
                $unidades = floor($cant / $factor);
                $resto = round($cant % $factor, 2);
                $data['cantidad_convertida'] = "$unidades " . $data['unidad_reporte'] . ($resto > 0 ? " + $resto pzas" : "");
            } else {
                $data['cantidad_convertida'] = "$cant pzas";
            }
        }
        return $data;
    }

    public function obtenerDatosVentaGananciaImpresion($idMovimiento) {
    // Sanitizamos el ID
    $idMovimiento = intval($idMovimiento);

    $sql = "SELECT 
                m.id as movimiento_id,
                m.fecha as fecha_solicitud,
                m.cantidad as cantidad_total,
                p.nombre as producto,
                p.sku,
                p.unidad_reporte,
                p.factor_conversion,
                a_orig.nombre as almacen_origen,
                u_patio.nombre as usuario_despacho,
                rsl.fecha_despacho,
                
                /* Detalle de lotes delimitado para el JS */
                (SELECT GROUP_CONCAT(
                    CONCAT(
                        ls.codigo_lote, '|', 
                        lms.cantidad_salida, '|', 
                        lms.costo_compra_historico, '|', 
                        lms.precio_venta_pactado
                    ) SEPARATOR '___'
                )
                 FROM lotes_movimientos_salida lms
                 INNER JOIN lotes_stock ls ON lms.lote_id = ls.id
                 WHERE lms.id IN (
                    SELECT lms2.id 
                    FROM lotes_movimientos_salida lms2
                    INNER JOIN registro_salida_lotes rsl2 ON rsl2.movimiento_id = $idMovimiento
                    WHERE lms2.entrega_venta_id = (SELECT ev.id FROM entregas_venta ev WHERE ev.venta_id = m.referencia_id LIMIT 1)
                    OR (m.tipo = 'salida' AND lms2.detalle_venta_id = 0 AND ls.producto_id = m.producto_id)
                 )
                ) as detalle_financiero,

                /* Totales basados EXACTAMENTE en los mismos lotes del detalle anterior */
                (SELECT SUM(lms3.costo_compra_historico * lms3.cantidad_salida) 
                 FROM lotes_movimientos_salida lms3 
                 WHERE lms3.id IN (
                    SELECT lms4.id FROM lotes_movimientos_salida lms4
                    INNER JOIN registro_salida_lotes rsl4 ON rsl4.movimiento_id = $idMovimiento
                    WHERE lms4.entrega_venta_id = (SELECT ev4.id FROM entregas_venta ev4 WHERE ev4.venta_id = m.referencia_id LIMIT 1)
                    OR (m.tipo = 'salida' AND lms4.detalle_venta_id = 0)
                 )
                ) as total_costo,

                (SELECT SUM(lms5.precio_venta_pactado * lms5.cantidad_salida) 
                 FROM lotes_movimientos_salida lms5 
                 WHERE lms5.id IN (
                    SELECT lms6.id FROM lotes_movimientos_salida lms6
                    INNER JOIN registro_salida_lotes rsl6 ON rsl6.movimiento_id = $idMovimiento
                    WHERE lms6.entrega_venta_id = (SELECT ev5.id FROM entregas_venta ev5 WHERE ev5.venta_id = m.referencia_id LIMIT 1)
                    OR (m.tipo = 'salida' AND lms6.detalle_venta_id = 0)
                 )
                ) as total_venta

            FROM movimientos m
            INNER JOIN productos p ON m.producto_id = p.id
            LEFT JOIN almacenes a_orig ON m.almacen_origen_id = a_orig.id
            INNER JOIN registro_salida_lotes rsl ON m.id = rsl.movimiento_id
            LEFT JOIN usuarios u_patio ON rsl.usuario_patio_id = u_patio.id
            WHERE m.id = $idMovimiento";

    $res = $this->db->query($sql);
    $data = $res->fetch_assoc();

    if ($data) {
        $cant = floatval($data['cantidad_total']);
        $factor = floatval($data['factor_conversion'] ?: 1);
        
        // Formateo de cantidad convertida
        if ($factor > 1 && $cant >= $factor) {
            $unidades = floor($cant / $factor);
            $resto = round($cant % $factor, 2);
            $data['cantidad_convertida'] = "$unidades " . $data['unidad_reporte'] . ($resto > 0 ? " + $resto pzas" : "");
        } else {
            $data['cantidad_convertida'] = "$cant pzas";
        }

        // Cálculos finales
        $total_c = floatval($data['total_costo'] ?? 0);
        $total_v = floatval($data['total_venta'] ?? 0);
        $data['ganancia_neta'] = round($total_v - $total_c, 2);
    }
    return $data;
}
public function listarSoloDespachadosPatio($almacen_id = 0) {
    $sql = "SELECT 
                m.id as movimiento_id,
                v.folio as folio_venta,
                m.fecha as fecha_movimiento,
                p.nombre as producto,
                p.sku,
                p.unidad_medida,       -- Agregado: necesario para el cálculo
                p.unidad_reporte,
                p.factor_conversion,   -- Agregado: necesario para el cálculo
                m.almacen_origen_id,
                m.cantidad,
                a.nombre as almacen_origen,
                rsl.fecha_despacho, 
                u.nombre as despacho_por,
                1 as ya_despachado,
                IFNULL(trm.estado_reparto, 'pendiente') as estado_reparto
            FROM movimientos m
            INNER JOIN registro_salida_lotes rsl ON m.id = rsl.movimiento_id
            INNER JOIN productos p ON m.producto_id = p.id
            LEFT JOIN ventas v ON m.referencia_id = v.id
            LEFT JOIN almacenes a ON m.almacen_origen_id = a.id
            LEFT JOIN usuarios u ON rsl.usuario_patio_id = u.id
            LEFT JOIN transporte_repartos_maestro trm ON m.id = trm.entrega_venta_id
            LEFT JOIN transmutacion_detalle td ON m.id = td.movimiento_id
            WHERE m.tipo = 'salida' 
              AND td.id IS NULL
              AND (v.id IS NULL OR v.estado_general = 'activa')
              AND (trm.estado_reparto IS NULL OR trm.estado_reparto != 'cancelado')";

    if (intval($almacen_id) > 0) {
        $sql .= " AND m.almacen_origen_id = " . intval($almacen_id);
    }

    $sql .= " ORDER BY rsl.fecha_despacho DESC";

    $res = $this->db->query($sql);
    $data = [];

    if($res) {
        while ($row = $res->fetch_assoc()) {
            $row['fecha_format'] = !empty($row['fecha_despacho']) 
                ? date('d/m/Y H:i', strtotime($row['fecha_despacho'])) 
                : 'S/F';

            // --- Lógica de Desglose ---
            $cantidad = floatval($row['cantidad']);
            $factor   = floatval($row['factor_conversion'] ?? 1);
            $u_rep    = $row['unidad_reporte'] ?: 'Unid.';
            $u_med    = $row['unidad_medida'] ?: 'Pz';

            if ($factor > 1) {
                $enteros   = (int) floor($cantidad / $factor);
                $sobrantes = fmod($cantidad, $factor);

                if ($sobrantes > 0) {
                    $row['cantidad_display'] = "{$enteros} {$u_rep} + {$sobrantes} {$u_med}";
                } else {
                    $row['cantidad_display'] = "{$enteros} {$u_rep}";
                }
            } else {
                $row['cantidad_display'] = "{$cantidad} {$u_med}";
            }

            $data[] = $row;
        }
    }
    return $data;
}
public function getDetalleParaDespacho($movimiento_id) {
    $sql = "SELECT 
                m.id AS movimiento_id,
                m.cantidad,
                p.nombre AS producto_nombre,
                p.unidad_reporte,
                p.unidad_medida,       -- <--- Agregado: Vital para el desglose en JS
                p.factor_conversion,
                v.folio AS folio_venta,
                c.nombre_comercial AS cliente_nombre,
                c.direccion AS cliente_direccion_fiscal,
                c.telefono AS cliente_telefono
            FROM movimientos m
            INNER JOIN productos p ON m.producto_id = p.id
            LEFT JOIN ventas v ON m.referencia_id = v.id
            LEFT JOIN clientes c ON v.id_cliente = c.id
            WHERE m.id = ? 
            LIMIT 1";
            
    $stmt = $this->db->prepare($sql);
    
    if (!$stmt) {
        return null;
    }

    $stmt->bind_param("i", $movimiento_id);
    $stmt->execute();
    $resultado = $stmt->get_result()->fetch_assoc();

    // Pequeña validación de seguridad para el factor
    if ($resultado) {
        $resultado['factor_conversion'] = floatval($resultado['factor_conversion'] ?? 1);
        $resultado['cantidad'] = floatval($resultado['cantidad'] ?? 0);
    }

    return $resultado;
}
public function cajaRapidaEntregarEnPatioCliente($datos) {
    try {
        $vehiculo_virtual_id = 999; 
        $movimiento_id       = intval($datos['movimiento_id']);
        $chofer_id           = intval($datos['chofer_id']); 
        $usuario_operador_id = intval($datos['usuario_sistema_id']); 
        $observaciones       = !empty($datos['observaciones']) ? $datos['observaciones'] : 'Entrega Directa en Patio';
        
        // Unimos al chofer y los ayudantes en una sola lista de personal
        $tripulantes = isset($datos['tripulantes']) && is_array($datos['tripulantes']) ? $datos['tripulantes'] : [];
        if ($chofer_id > 0) array_unshift($tripulantes, $chofer_id); // El chofer va primero
        $tripulantes = array_unique($tripulantes);

        // 1. VALIDACIÓN: Ahora checamos por MOVIMIENTO, no por VENTA completa
        // para que si la venta tiene 3 productos, cada uno pueda registrar su salida.
        $sqlCheck = "SELECT id FROM transporte_repartos_maestro 
                     WHERE entrega_venta_id = ? AND estado_reparto != 'cancelado' LIMIT 1";
        
        $stmtCheck = $this->db->prepare($sqlCheck);
        $stmtCheck->bind_param("i", $movimiento_id);
        $stmtCheck->execute();
        
        if ($stmtCheck->get_result()->num_rows > 0) {
            return ['success' => true, 'message' => 'Movimiento ya procesado.']; 
        }

        $this->db->begin_transaction();

        // 2. MAESTRO (Incluimos el chofer responsable en el query si tu tabla tiene la columna)
        // He añadido 'id_chofer_asignado' asumiendo que existe, si no, bórralo del query.
        $sqlM = "INSERT INTO transporte_repartos_maestro (
                    vehiculo_id, usuario_encargado_id, entrega_venta_id, 
                    fecha_programada, estado_reparto, hora_llegada_real
                ) VALUES (?, ?, ?, CURDATE(), 'completado', NOW())";
        
        $stmtM = $this->db->prepare($sqlM);
        $stmtM->bind_param("iii", $vehiculo_virtual_id, $usuario_operador_id, $movimiento_id);
        $stmtM->execute();
        $reparto_id = $this->db->insert_id;

        // 3. PUNTO DE RUTA
        $sqlP = "INSERT INTO transporte_rutas_puntos (reparto_id, orden_visita, descripcion_punto, estado_punto) 
                 VALUES (?, 1, ?, 'visitado')";
        $descripcion = "PATIO: " . $observaciones;
        $stmtP = $this->db->prepare($sqlP);
        $stmtP->bind_param("is", $reparto_id, $descripcion);
        $stmtP->execute();

        // 4. TRIPULACIÓN (Bucle seguro)
        if (!empty($tripulantes)) {
            $sqlT = "INSERT INTO transporte_tripulantes_detalle (reparto_id, usuario_id) VALUES (?, ?)";
            $stmtT = $this->db->prepare($sqlT);
            foreach ($tripulantes as $u_id) {
                $uid = intval($u_id);
                if($uid > 0) {
                    $stmtT->bind_param("ii", $reparto_id, $uid);
                    $stmtT->execute();
                }
            }
        }

        // 5. VEHÍCULO SIEMPRE LIBRE
        $this->db->query("UPDATE transporte_vehiculos SET estado_unidad = 'disponible' WHERE id = $vehiculo_virtual_id");

        $this->db->commit();
        return ['success' => true, 'reparto_id' => $reparto_id];

    } catch (Exception $e) {
        // Rollback genérico más seguro
        if ($this->db->connect_errno == 0) $this->db->rollback();
        throw $e;
    }
}
}