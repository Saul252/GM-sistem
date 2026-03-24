<?php
class RepartoModel {
    private $db;

    public function __construct($conexion) {
        $this->db = $conexion;
    }

   /**
 * Procesa la asignación de una ruta de logística completa.
 * @param array $datos Provienen directamente del $_POST del formulario.
 * @return int ID del reparto generado.
 * @throws Exception Si ocurre un error en la base de datos.
 */
public function iniciarReparto($datos) {
    try {
        $vehiculo_id    = intval($datos['vehiculo_id']);
        $chofer_id      = intval($datos['chofer_id']);
        $movimiento_id  = intval($datos['movimiento_id']);
        $direccion      = !empty($datos['direccion_entrega']) ? $datos['direccion_entrega'] : 'Entrega en Obra';
        $tripulantes    = isset($datos['tripulantes']) && is_array($datos['tripulantes']) ? $datos['tripulantes'] : [];
        
        // Recuperamos el folio que viene desde el controlador
        $folio_viaje    = $datos['folio_viaje'] ?? ''; 

        // --- VALIDACIÓN DE INTEGRIDAD EN TRANSPORTE ---
        $sqlCheck = "SELECT rp.id FROM transporte_rutas_puntos rp
                     INNER JOIN transporte_repartos_maestro trm ON rp.reparto_id = trm.id
                     WHERE trm.entrega_venta_id = ? 
                     AND trm.estado_reparto != 'cancelado' LIMIT 1";
        
        $stmtCheck = $this->db->prepare($sqlCheck);
        $stmtCheck->bind_param("i", $movimiento_id);
        $stmtCheck->execute();
        
        if ($stmtCheck->get_result()->num_rows > 0) {
            throw new Exception("Ya existe una ruta programada para este despacho.");
        }

        $this->db->begin_transaction();

        // 1. Crear el Maestro del Reparto
        $sqlM = "INSERT INTO transporte_repartos_maestro (
                    vehiculo_id, 
                    usuario_encargado_id, 
                    entrega_venta_id, 
                    fecha_programada, 
                    estado_reparto
                ) VALUES (?, ?, ?, CURDATE(), 'en_transito')";
        
        $stmtM = $this->db->prepare($sqlM);
        $stmtM->bind_param("iii", $vehiculo_id, $chofer_id, $movimiento_id);
        $stmtM->execute();
        $reparto_id = $this->db->insert_id;

        // 1.1 NUEVO: Registro en la tabla de consolidación para agrupar
        // Esta tabla vincula este reparto específico con el folio de viaje actual
        $sqlC = "INSERT INTO transporte_consolidacion (
                    viaje_folio, 
                    vehiculo_id, 
                    reparto_id, 
                    estatus_consolidado
                ) VALUES (?, ?, ?, 'abierto')";
        $stmtC = $this->db->prepare($sqlC);
        $stmtC->bind_param("sii", $folio_viaje, $vehiculo_id, $reparto_id);
        $stmtC->execute();

        // 2. Insertar el Punto de Ruta
        $sqlP = "INSERT INTO transporte_rutas_puntos (
                    reparto_id, 
                    orden_visita, 
                    descripcion_punto, 
                    estado_punto
                ) VALUES (?, 1, ?, 'pendiente')";
        
        $stmtP = $this->db->prepare($sqlP);
        $stmtP->bind_param("is", $reparto_id, $direccion);
        $stmtP->execute();

        // 3. Registrar Tripulación
        if (!empty($tripulantes)) {
            $sqlT = "INSERT INTO transporte_tripulantes_detalle (reparto_id, usuario_id) VALUES (?, ?)";
            $stmtT = $this->db->prepare($sqlT);
            foreach ($tripulantes as $u_id) {
                if (intval($u_id) === $chofer_id) continue;
                $uid = intval($u_id);
                $stmtT->bind_param("ii", $reparto_id, $uid);
                $stmtT->execute();
            }
        }

        $this->db->commit();
        return $reparto_id;

    } catch (Exception $e) {
        if (isset($this->db) && $this->db->in_transaction) $this->db->rollback();
        throw $e;
    }
}
public function entregarEnPatioCliente($datos) {
    try {
        // --- PARÁMETROS ---
        $vehiculo_virtual_id = 999; 
        $movimiento_id       = intval($datos['movimiento_id']);
        $trabajador_id       = intval($datos['chofer_id']); 
        $usuario_operador_id = intval($datos['usuario_sistema_id']); 
        $observaciones       = !empty($datos['observaciones']) ? $datos['observaciones'] : 'Entrega Directa en Patio';
        $tripulantes         = isset($datos['tripulantes']) && is_array($datos['tripulantes']) ? $datos['tripulantes'] : [];

        // --- 1. VALIDACIÓN DE INTEGRIDAD ---
        $sqlCheck = "SELECT rp.id FROM transporte_rutas_puntos rp
                     INNER JOIN transporte_repartos_maestro trm ON rp.reparto_id = trm.id
                     WHERE trm.entrega_venta_id = ? 
                     AND trm.estado_reparto != 'cancelado' LIMIT 1";
        
        $stmtCheck = $this->db->prepare($sqlCheck);
        $stmtCheck->bind_param("i", $movimiento_id);
        $stmtCheck->execute();
        
        if ($stmtCheck->get_result()->num_rows > 0) {
            throw new Exception("Ya existe un proceso de entrega activo para este despacho.");
        }

        $this->db->begin_transaction();

        // --- 2. CREAR EL MAESTRO DEL REPARTO ---
        // Estado 'completado' y registramos hora de llegada inmediata
        $estado_maestro = 'completado'; 

        $sqlM = "INSERT INTO transporte_repartos_maestro (
                    vehiculo_id, 
                    usuario_encargado_id, 
                    entrega_venta_id, 
                    fecha_programada, 
                    estado_reparto,
                    hora_llegada_real
                ) VALUES (?, ?, ?, CURDATE(), ?, NOW())";
        
        $stmtM = $this->db->prepare($sqlM);
        $stmtM->bind_param("iiis", $vehiculo_virtual_id, $usuario_operador_id, $movimiento_id, $estado_maestro);
        $stmtM->execute();
        $reparto_id = $this->db->insert_id;

        // --- 3. INSERTAR EL PUNTO DE RUTA ---
        $estado_punto = 'visitado'; 
        $descripcion  = "ENTREGA EN PATIO: " . $observaciones;

        $sqlP = "INSERT INTO transporte_rutas_puntos (
                    reparto_id, 
                    orden_visita, 
                    descripcion_punto, 
                    estado_punto
                ) VALUES (?, 1, ?, ?)";
        
        $stmtP = $this->db->prepare($sqlP);
        $stmtP->bind_param("iss", $reparto_id, $descripcion, $estado_punto);
        $stmtP->execute();

        // --- 4. REGISTRAR TRIPULACIÓN ---
        if (!empty($tripulantes)) {
            $sqlT = "INSERT INTO transporte_tripulantes_detalle (reparto_id, usuario_id) VALUES (?, ?)";
            $stmtT = $this->db->prepare($sqlT);
            foreach ($tripulantes as $u_id) {
                $uid = intval($u_id);
                $stmtT->bind_param("ii", $reparto_id, $uid);
                $stmtT->execute();
            }
        }

        // --- 5. ASEGURAR DISPONIBILIDAD DEL VEHÍCULO VIRTUAL ---
        // Forzamos que el 999 siempre esté listo para el siguiente cliente
        $sqlV = "UPDATE transporte_vehiculos SET estado_unidad = 'disponible' WHERE id = ?";
        $stmtV = $this->db->prepare($sqlV);
        $stmtV->bind_param("i", $vehiculo_virtual_id);
        $stmtV->execute();

        $this->db->commit();
        
        return [
            'success' => true,
            'reparto_id' => $reparto_id,
            'message' => '¡Entrega finalizada! El mostrador sigue disponible para otros clientes.'
        ];

    } catch (Exception $e) {
        if (isset($this->db) && $this->db->in_transaction) $this->db->rollback();
        throw $e;
    }
}
// Función auxiliar para el controlador
public function buscarRutaAbierta($vehiculo_id) {
    $sql = "SELECT viaje_folio FROM transporte_consolidacion 
            WHERE vehiculo_id = ? AND estatus_consolidado = 'abierto' LIMIT 1";
    $stmt = $this->db->prepare($sql);
    $stmt->bind_param("i", $vehiculo_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}
public function listarViajesActivos($almacen_id = 0) {
    $almacen_id = intval($almacen_id);
    
    $sql = "SELECT 
                tc.viaje_folio,
                tc.vehiculo_id,
                tv.nombre as unidad,
                tv.placas,
                -- Obtenemos el nombre del chofer desde trabajadores (usuario_encargado_id)
                (SELECT nombre FROM trabajadores WHERE id = trm.usuario_encargado_id LIMIT 1) as chofer,
                -- Concatenamos los tripulantes
                (SELECT GROUP_CONCAT(tr.nombre SEPARATOR ', ') 
                 FROM transporte_tripulantes_detalle ttd
                 INNER JOIN trabajadores tr ON ttd.usuario_id = tr.id
                 WHERE ttd.reparto_id = trm.id) as tripulantes,
                -- Detalles de lo que lleva el camión
                GROUP_CONCAT(
                    DISTINCT CONCAT(
                        '• <b>[', COALESCE(v.folio, 'S/F'), ']</b> ',
                        m.cantidad, ' ', p.unidad_medida,
                        ' - ', p.nombre
                    ) 
                    SEPARATOR '<br>'
                ) as detalles_carga
            FROM transporte_consolidacion tc
            INNER JOIN transporte_repartos_maestro trm ON tc.reparto_id = trm.id
            INNER JOIN transporte_vehiculos tv ON tc.vehiculo_id = tv.id
            LEFT JOIN movimientos m ON trm.entrega_venta_id = m.id
            LEFT JOIN productos p ON m.producto_id = p.id
            LEFT JOIN ventas v ON m.referencia_id = v.id -- Unimos con ventas para sacar el almacén
            WHERE tc.estatus_consolidado = 'abierto'";

    // FILTRO DINÁMICO POR ALMACÉN
    if ($almacen_id > 0) {
        // Filtramos por el almacén de la venta original
        $sql .= " AND v.almacen_id = $almacen_id";
    }

    $sql .= " GROUP BY tc.viaje_folio, tc.vehiculo_id, tv.nombre, tv.placas";
    $sql .= " ORDER BY tc.viaje_folio DESC";
            
    $res = $this->db->query($sql);
    return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
}
public function listarHistorialDeRepartos($almacen_id = 0) {
    $almacen_id = intval($almacen_id);
    
    $sql = "SELECT 
                tc.viaje_folio,
                tc.vehiculo_id,
                tc.estatus_consolidado AS estado_final,
                tv.nombre as unidad,
                tv.placas,
                -- 1. Chofer: Tomamos el encargado del primer reparto del viaje
                (SELECT t.nombre FROM trabajadores t 
                 INNER JOIN transporte_repartos_maestro trm2 ON t.id = trm2.usuario_encargado_id 
                 WHERE trm2.id = tc.reparto_id LIMIT 1) as chofer,
                 
                -- 2. Tripulantes: Concatenamos todos los ayudantes de los repartos del viaje
                (SELECT GROUP_CONCAT(DISTINCT tr.nombre SEPARATOR ', ') 
                 FROM transporte_tripulantes_detalle ttd
                 INNER JOIN trabajadores tr ON ttd.usuario_id = tr.id
                 INNER JOIN transporte_consolidacion tc2 ON ttd.reparto_id = tc2.reparto_id
                 WHERE tc2.viaje_folio = tc.viaje_folio) as tripulantes,
                 
                -- 3. DESTINOS CORREGIDOS: Busca todos los puntos de todos los repartos del mismo folio
                (SELECT GROUP_CONCAT(DISTINCT COALESCE(rp.descripcion_punto, 'Entrega en Obra') SEPARATOR '<br>')
                 FROM transporte_rutas_puntos rp 
                 INNER JOIN transporte_consolidacion tc3 ON rp.reparto_id = tc3.reparto_id
                 WHERE tc3.viaje_folio = tc.viaje_folio) as ruta_destinos,
                 
                -- 4. Detalles de carga consolidada
                GROUP_CONCAT(
                    DISTINCT CONCAT(
                        '• <b>[', COALESCE(v.folio, 'S/F'), ']</b> ',
                        m.cantidad, ' ', p.unidad_medida,
                        ' - ', p.nombre
                    ) 
                    SEPARATOR '<br>'
                ) as detalles_carga
                
            FROM transporte_consolidacion tc
            INNER JOIN transporte_repartos_maestro trm ON tc.reparto_id = trm.id
            INNER JOIN transporte_vehiculos tv ON tc.vehiculo_id = tv.id
            LEFT JOIN movimientos m ON trm.entrega_venta_id = m.id
            LEFT JOIN productos p ON m.producto_id = p.id
            LEFT JOIN ventas v ON m.referencia_id = v.id 
            WHERE tc.estatus_consolidado != 'abierto'";

    if ($almacen_id > 0) {
        $sql .= " AND v.almacen_id = $almacen_id";
    }

    $sql .= " GROUP BY tc.viaje_folio, tc.vehiculo_id, tv.nombre, tv.placas, tc.estatus_consolidado";
    $sql .= " ORDER BY tc.viaje_folio DESC";
            
    $res = $this->db->query($sql);
    return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
}
public function finalizarViajeVehiculo($vehiculo_id) {
    try {
        $this->db->begin_transaction();

        // 1. Actualizar Maestro de Repartos
        $sqlM = "UPDATE transporte_repartos_maestro 
                 SET estado_reparto = 'entregado', 
                     fecha_entrega = NOW() 
                 WHERE vehiculo_id = ? AND estado_reparto = 'en_transito'";
        $stmtM = $this->db->prepare($sqlM);
        $stmtM->bind_param("i", $vehiculo_id);
        $stmtM->execute();

        // 2. Actualizar Puntos de Ruta (Opcional, para que queden como completados)
        $sqlP = "UPDATE transporte_rutas_puntos rp
                 INNER JOIN transporte_repartos_maestro trm ON rp.reparto_id = trm.id
                 SET rp.estado_punto = 'visitado'
                 WHERE trm.vehiculo_id = ? AND trm.estado_reparto = 'entregado'";
        $stmtP = $this->db->prepare($sqlP);
        $stmtP->bind_param("i", $vehiculo_id);
        $stmtP->execute();

        $this->db->commit();
        return true;
    } catch (Exception $e) {
        $this->db->rollback();
        throw $e;
    }
}
public function finalizarViajeLogistica($vehiculo_id, $viaje_folio) {
    try {
        $this->db->begin_transaction();

        // 1. Cerramos la tabla de consolidación (la que creamos para agrupar)
        $sqlC = "UPDATE transporte_consolidacion 
                 SET estatus_consolidado = 'cerrado' 
                 WHERE viaje_folio = ? AND vehiculo_id = ?";
        $stmtC = $this->db->prepare($sqlC);
        $stmtC->bind_param("si", $viaje_folio, $vehiculo_id);
        $stmtC->execute();

        // 2. Actualizamos los repartos maestros
        // Usamos 'completado' (que es el valor de tu ENUM) y 'hora_llegada_real' (que sí existe)
        $sqlR = "UPDATE transporte_repartos_maestro trm
                 INNER JOIN transporte_consolidacion tc ON trm.id = tc.reparto_id
                 SET trm.estado_reparto = 'completado', 
                     trm.hora_llegada_real = NOW() 
                 WHERE tc.viaje_folio = ?";
        
        $stmtR = $this->db->prepare($sqlR);
        $stmtR->bind_param("s", $viaje_folio);
        $stmtR->execute();

        $this->db->commit();
        return true;
    } catch (Exception $e) {
        $this->db->rollback();
        throw $e;
    }
}
public function cancelarViajeCompleto($folio_viaje, $vehiculo_id) {
    try {
        $vehiculo_id = intval($vehiculo_id);
        
        // 1. Buscamos todos los repartos asociados a este folio y vehículo
        $sqlBusqueda = "SELECT reparto_id FROM transporte_consolidacion 
                        WHERE viaje_folio = ? AND vehiculo_id = ?";
        
        $stmtB = $this->db->prepare($sqlBusqueda);
        $stmtB->bind_param("si", $folio_viaje, $vehiculo_id);
        $stmtB->execute();
        $resB = $stmtB->get_result();

        if ($resB->num_rows === 0) {
            throw new Exception("No se encontraron entregas activas para este folio de viaje.");
        }

        $repartos_ids = [];
        while ($row = $resB->fetch_assoc()) {
            $repartos_ids[] = $row['reparto_id'];
        }

        $this->db->begin_transaction();

        // Convertimos el array de IDs para la cláusula WHERE IN (?,?,?)
        $placeholders = implode(',', array_fill(0, count($repartos_ids), '?'));
        $types = str_repeat('i', count($repartos_ids));

        // 2. Limpiamos Tripulación de todos los repartos del viaje
        $stmtT = $this->db->prepare("DELETE FROM transporte_tripulantes_detalle WHERE reparto_id IN ($placeholders)");
        $stmtT->bind_param($types, ...$repartos_ids);
        $stmtT->execute();

        // 3. Limpiamos Puntos de Ruta
        $stmtP = $this->db->prepare("DELETE FROM transporte_rutas_puntos WHERE reparto_id IN ($placeholders)");
        $stmtP->bind_param($types, ...$repartos_ids);
        $stmtP->execute();

        // 4. Limpiamos la Consolidación (Libera el folio de viaje)
        $stmtC = $this->db->prepare("DELETE FROM transporte_consolidacion WHERE reparto_id IN ($placeholders)");
        $stmtC->bind_param($types, ...$repartos_ids);
        $stmtC->execute();

        // 5. Eliminamos los registros Maestros
        // Al eliminarlos, los movimientos originales en el listado vuelven a mostrar "ASIGNAR RUTA"
        $stmtM = $this->db->prepare("DELETE FROM transporte_repartos_maestro WHERE id IN ($placeholders)");
        $stmtM->bind_param($types, ...$repartos_ids);
        $stmtM->execute();

        $this->db->commit();
        return true;

    } catch (Exception $e) {
        if (isset($this->db) && $this->db->in_transaction) $this->db->rollback();
        throw $e;
    }
}
public function cancelarEntregaIndividual() {
   
}
public function actualizarLogisticaCompleta($datos) {
    try {
        $this->db->begin_transaction();

        $folio_viaje     = $datos['viaje_folio'];
        $vehiculo_id     = intval($datos['vehiculo_id']);
        $nuevo_chofer_id = intval($datos['chofer_id']);
        $nuevos_trip     = isset($datos['tripulantes']) ? $datos['tripulantes'] : [];
        // 'destinos' debe ser un array: [ ['movimiento_id' => 10, 'destino' => 'Calle Falsa 123'], ... ]
        $destinos_editados = isset($datos['destinos']) ? $datos['destinos'] : [];

        // 1. ACTUALIZAR CHOFER (Responsable)
        $sqlU = "UPDATE transporte_repartos_maestro trm
                 INNER JOIN transporte_consolidacion tc ON trm.id = tc.reparto_id
                 SET trm.usuario_encargado_id = ?
                 WHERE tc.viaje_folio = ? AND tc.vehiculo_id = ?";
        $stmtU = $this->db->prepare($sqlU);
        $stmtU->bind_param("isi", $nuevo_chofer_id, $folio_viaje, $vehiculo_id);
        $stmtU->execute();

        // 2. OBTENER REPARTOS PARA TRIPULACIÓN Y DESTINOS
        $sqlR = "SELECT tc.reparto_id, trm.entrega_venta_id 
                 FROM transporte_consolidacion tc
                 INNER JOIN transporte_repartos_maestro trm ON tc.reparto_id = trm.id
                 WHERE tc.viaje_folio = ?";
        $stmtR = $this->db->prepare($sqlR);
        $stmtR->bind_param("s", $folio_viaje);
        $stmtR->execute();
        $repartos = $stmtR->get_result()->fetch_all(MYSQLI_ASSOC);

        foreach ($repartos as $r) {
            $rid = $r['reparto_id'];
            $mov_id = $r['entrega_venta_id'];

            // 2.1 REFRESCAR TRIPULACIÓN (Ayudantes)
            $this->db->query("DELETE FROM transporte_tripulantes_detalle WHERE reparto_id = $rid");
            if (!empty($nuevos_trip)) {
                $stmtT = $this->db->prepare("INSERT INTO transporte_tripulantes_detalle (reparto_id, usuario_id) VALUES (?, ?)");
                foreach ($nuevos_trip as $u_id) {
                    if (intval($u_id) === $nuevo_chofer_id) continue;
                    $uid = intval($u_id);
                    $stmtT->bind_param("ii", $rid, $uid);
                    $stmtT->execute();
                }
            }

            // 2.2 ACTUALIZAR DESTINO INDIVIDUAL
            // Buscamos si en los datos recibidos hay un nuevo destino para este movimiento específico
            foreach ($destinos_editados as $edit) {
                if (intval($edit['movimiento_id']) === intval($mov_id)) {
                    $nuevo_destino = $edit['destino'];
                    $stmtD = $this->db->prepare("UPDATE transporte_rutas_puntos SET descripcion_punto = ? WHERE reparto_id = ?");
                    $stmtD->bind_param("si", $nuevo_destino, $rid);
                    $stmtD->execute();
                    break; 
                }
            }
        }

        $this->db->commit();
        return true;
    } catch (Exception $e) {
        if ($this->db->in_transaction) $this->db->rollback();
        throw $e;
    }
}
public function getDetallesViaje($folio_viaje) {
    // 1. Buscamos la cabecera, incluyendo el almacen_id de la venta
    $sqlHeader = "SELECT 
                    tc.viaje_folio,
                    tc.vehiculo_id,
                    trm.usuario_encargado_id as chofer_id,
                    v.almacen_id  -- <--- Aquí lo obtenemos del movimiento/venta
                  FROM transporte_consolidacion tc
                  INNER JOIN transporte_repartos_maestro trm ON tc.reparto_id = trm.id
                  LEFT JOIN movimientos m ON trm.entrega_venta_id = m.id
                  LEFT JOIN ventas v ON m.referencia_id = v.id
                  WHERE tc.viaje_folio = ? LIMIT 1";
    
    $stmtH = $this->db->prepare($sqlHeader);
    $stmtH->bind_param("s", $folio_viaje);
    $stmtH->execute();
    $header = $stmtH->get_result()->fetch_assoc();

    if (!$header) return null;

    // 2. IDs de Tripulantes (para el select múltiple)
    $sqlT = "SELECT usuario_id FROM transporte_tripulantes_detalle 
             WHERE reparto_id IN (SELECT reparto_id FROM transporte_consolidacion WHERE viaje_folio = ?)";
    $stmtT = $this->db->prepare($sqlT);
    $stmtT->bind_param("s", $folio_viaje);
    $stmtT->execute();
    $resT = $stmtT->get_result();
    $header['tripulantes_ids'] = [];
    while($r = $resT->fetch_assoc()) $header['tripulantes_ids'][] = $r['usuario_id'];

    // 3. Materiales y sus destinos
    $sqlMat = "SELECT m.id as movimiento_id, p.nombre as producto, m.cantidad, 
                      v.folio as folio_venta, rp.descripcion_punto as destino
               FROM transporte_consolidacion tc
               INNER JOIN transporte_repartos_maestro trm ON tc.reparto_id = trm.id
               INNER JOIN movimientos m ON trm.entrega_venta_id = m.id
               INNER JOIN productos p ON m.producto_id = p.id
               LEFT JOIN ventas v ON m.referencia_id = v.id
               LEFT JOIN transporte_rutas_puntos rp ON trm.id = rp.reparto_id
               WHERE tc.viaje_folio = ?";
    $stmtM = $this->db->prepare($sqlMat);
    $stmtM->bind_param("s", $folio_viaje);
    $stmtM->execute();
    $header['materiales'] = $stmtM->get_result()->fetch_all(MYSQLI_ASSOC);

    return $header;
}
public function getResumenDespacho($movimiento_id) {
    $sql = "SELECT 
                m.id as movimiento_id,
                m.cantidad,
                p.nombre as producto_nombre,
                v.folio as folio_venta,
                c.nombre_comercial as cliente,
                
                -- Usuario que registró el movimiento
                u_mov.nombre as administrador_sistema,
                
                -- Logística
                trm.id as reparto_id,
                trm.estado_reparto,
                tv.nombre as vehiculo,
                tv.placas,
                u_chofer.nombre as chofer_nombre,
                tc.viaje_folio,
                
                -- Patio
                rsl.fecha_despacho as fecha_patio,
                u_patio.nombre as despachador_patio,
                u_despacho.nombre as administrador_patio
                
            FROM movimientos m
            INNER JOIN productos p ON m.producto_id = p.id
            LEFT JOIN ventas v ON m.referencia_id = v.id
            LEFT JOIN clientes c ON v.id_cliente = c.id
            LEFT JOIN usuarios u_mov ON m.usuario_registra_id = u_mov.id
            
            LEFT JOIN transporte_repartos_maestro trm ON m.id = trm.entrega_venta_id
            LEFT JOIN transporte_vehiculos tv ON trm.vehiculo_id = tv.id
            LEFT JOIN usuarios u_chofer ON trm.usuario_encargado_id = u_chofer.id
            LEFT JOIN transporte_consolidacion tc ON trm.id = tc.reparto_id
            
            LEFT JOIN registro_salida_lotes rsl ON m.id = rsl.movimiento_id
            LEFT JOIN usuarios u_patio ON rsl.usuario_patio_id = u_patio.id
            LEFT JOIN usuarios u_despacho ON rsl.usuario_despacho_id = u_despacho.id
            
            WHERE m.id = ? LIMIT 1";

    $stmt = $this->db->prepare($sql);
    $stmt->bind_param("i", $movimiento_id);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();

    if (!$res) return null;

    // Tripulantes: transporte_tripulantes_detalle.usuario_id -> usuarios
    $res['tripulantes'] = [];
    if ($res['reparto_id']) {
        $sqlT = "SELECT u.nombre 
                 FROM transporte_tripulantes_detalle ttd
                 INNER JOIN usuarios u ON ttd.usuario_id = u.id
                 WHERE ttd.reparto_id = ?";
        $stmtT = $this->db->prepare($sqlT);
        $stmtT->bind_param("i", $res['reparto_id']);
        $stmtT->execute();
        $res['tripulantes'] = $stmtT->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    return $res;
}

public function obtenerHistorialFisico($movimiento_id) {
    $sql = "SELECT 
                m.id as movimiento_id,
                m.cantidad,
                m.fecha as fecha_movimiento,
                p.nombre as producto_nombre,
                v.folio as folio_venta,
                c.nombre_comercial as cliente,
                
                -- DIRECCIÓN DE ENTREGA (Desde los puntos de ruta)
                trp.descripcion_punto as direccion_entrega,
                
                -- TRAZABILIDAD DE SISTEMA (Usuarios que operan el software)
                u_asigna.nombre as usuario_asigno_sistema,    -- El que capturó el movimiento/reparto
                u_valida.nombre as usuario_valida_patio,    -- El que dio salida oficial en el sistema
                
                -- TRAZABILIDAD FÍSICA (Trabajadores que mueven el material)
                t_chofer.nombre as trabajador_entrega_ruta,  -- Chofer asignado
                t_patio.nombre as trabajador_despacho_patio, -- Almacenista que cargó
                
                -- DATOS DE LOGÍSTICA
                trm.id as reparto_id,
                trm.estado_reparto,
                tv.nombre as vehiculo,
                tv.placas,
                tc.viaje_folio,
                
                -- DATOS DE PATIO
                rsl.fecha_despacho as fecha_patio
                
            FROM movimientos m
            INNER JOIN productos p ON m.producto_id = p.id
            LEFT JOIN ventas v ON m.referencia_id = v.id
            LEFT JOIN clientes c ON v.id_cliente = c.id
            
            -- ¿Quién asignó/creó el movimiento en el sistema?
            LEFT JOIN usuarios u_asigna ON m.usuario_registra_id = u_asigna.id
            
            -- LOGÍSTICA: Relación con Repartos
            LEFT JOIN transporte_repartos_maestro trm ON m.id = trm.entrega_venta_id
            LEFT JOIN transporte_vehiculos tv ON trm.vehiculo_id = tv.id
            LEFT JOIN transporte_consolidacion tc ON trm.id = tc.reparto_id
            -- El chofer es un trabajador
            LEFT JOIN trabajadores t_chofer ON trm.usuario_encargado_id = t_chofer.id
            -- Dirección del primer punto de la ruta (donde se entrega)
            LEFT JOIN transporte_rutas_puntos trp ON trm.id = trp.reparto_id AND trp.orden_visita = 1
            
            -- PATIO: Registro físico de salida
            LEFT JOIN registro_salida_lotes rsl ON m.id = rsl.movimiento_id
            -- El despachador físico es un trabajador
            LEFT JOIN trabajadores t_patio ON rsl.usuario_patio_id = t_patio.id
            -- El que valida la salida en el sistema es un usuario (Administrativo de patio)
            LEFT JOIN usuarios u_valida ON rsl.usuario_despacho_id = u_valida.id
            
            WHERE m.id = ? LIMIT 1";

    $stmt = $this->db->prepare($sql);
    $stmt->bind_param("i", $movimiento_id);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();

    if (!$res) return null;

    // Tripulantes/Ayudantes (Siempre de la tabla trabajadores)
    $res['tripulantes'] = [];
    if ($res['reparto_id']) {
        $sqlT = "SELECT t.nombre 
                 FROM transporte_tripulantes_detalle ttd
                 INNER JOIN trabajadores t ON ttd.usuario_id = t.id
                 WHERE ttd.reparto_id = ?";
        $stmtT = $this->db->prepare($sqlT);
        $stmtT->bind_param("i", $res['reparto_id']);
        $stmtT->execute();
        $res['tripulantes'] = $stmtT->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    return $res;
}

public function getTripulantesPorReparto($reparto_id) {
    $sql = "SELECT u.nombre 
            FROM transporte_tripulantes_detalle ttd
            INNER JOIN usuarios u ON ttd.usuario_id = u.id
            WHERE ttd.reparto_id = ?";
    $stmt = $this->db->prepare($sql);
    $stmt->bind_param("i", $reparto_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
public function getMonitorEntregas($almacen_id = 0, $inicio = 0, $limite = 25) {
    if (ob_get_level()) ob_clean();

    // Filtro dinámico por almacén
    $where_almacen = ($almacen_id > 0) ? " AND m.almacen_origen_id = ? " : "";

    $sql = "SELECT 
                -- 1. IDs CRÍTICOS PARA EL MODAL (Agregados)
                m.id AS movimiento_id, 
                trm.id AS reparto_id,
                
                -- Agrupador: Si hay viaje consolidado, colapsa por folio de viaje, si no, por movimiento
                IFNULL(tc.viaje_folio, CONCAT('MOV-', m.id)) AS grupo_id,
                v.id AS venta_id,
                tc.viaje_folio AS numero_ruta,
                IF(tc.viaje_folio IS NOT NULL, 'RUTA', 'MOSTRADOR') AS tipo_salida,
                
                -- Identificador Visual: Folio de Viaje o Folio de Venta
                COALESCE(tc.viaje_folio, v.folio) AS identificador_visual,
                
                -- Cliente
                CASE 
                    WHEN tc.viaje_folio IS NOT NULL THEN 'VARIOS CLIENTES (RUTA)'
                    ELSE c.nombre_comercial
                END AS cliente_display,
                
                -- Producto
                CASE 
                    WHEN tc.viaje_folio IS NOT NULL THEN 'MATERIALES DIVERSOS (CARGA CONSOLIDADA)'
                    ELSE p.nombre 
                END AS producto_nombre,

                SUM(m.cantidad) as total_bultos,
                p.unidad_reporte,
                p.unidad_medida,
                p.factor_conversion,

                -- Vehículo y Responsable Operativo
                CASE 
                    WHEN tc.viaje_folio IS NOT NULL THEN IFNULL(tv.nombre, 'POR ASIGNAR') 
                    ELSE 'RECOLECCIÓN PROPIA' 
                END AS vehiculo,
                COALESCE(t_chofer.nombre, t_patio.nombre, u_reg.nombre, 'POR ASIGNAR') AS responsable,

                -- Extracción de Lotes
                (SELECT GROUP_CONCAT(DISTINCT ls.codigo_lote SEPARATOR ', ')
                 FROM lotes_movimientos_salida lms
                 INNER JOIN lotes_stock ls ON lms.lote_id = ls.id
                 WHERE lms.entrega_venta_id = m.id 
                 OR (lms.detalle_venta_id > 0 AND lms.detalle_venta_id IN (
                     SELECT dv.id FROM detalle_venta dv WHERE dv.venta_id = v.id
                 ))
                ) AS lotes_involucrados,
                
                -- Fecha con formato para el JS
                DATE_FORMAT(MAX(IFNULL(rsl.fecha_despacho, m.fecha)), '%d/%m/%Y %H:%i') AS fecha_evento

            FROM movimientos m
            INNER JOIN productos p ON m.producto_id = p.id
            INNER JOIN ventas v ON m.referencia_id = v.id
            LEFT JOIN clientes c ON v.id_cliente = c.id
            LEFT JOIN usuarios u_reg ON m.usuario_registra_id = u_reg.id
            
            -- Logística
            LEFT JOIN transporte_repartos_maestro trm ON m.id = trm.entrega_venta_id AND trm.estado_reparto != 'cancelado'
            LEFT JOIN transporte_vehiculos tv ON trm.vehiculo_id = tv.id
            LEFT JOIN transporte_consolidacion tc ON trm.id = tc.reparto_id
            LEFT JOIN trabajadores t_chofer ON trm.usuario_encargado_id = t_chofer.id
            
            -- Patio
            LEFT JOIN registro_salida_lotes rsl ON m.id = rsl.movimiento_id
            LEFT JOIN trabajadores t_patio ON rsl.usuario_despacho_id = t_patio.id 

            WHERE m.tipo = 'salida' 
            $where_almacen

            GROUP BY grupo_id
            ORDER BY MAX(m.fecha) DESC 
            LIMIT ?, ?";

    $stmt = $this->db->prepare($sql);
    
    if ($almacen_id > 0) {
        $stmt->bind_param("iii", $almacen_id, $inicio, $limite);
    } else {
        $stmt->bind_param("ii", $inicio, $limite);
    }

    $stmt->execute();
    $result = $stmt->get_result();
    $data = [];

    while ($row = $result->fetch_assoc()) {
        // Lógica de visualización de cantidades (Conversión a Enteros/Restos)
        if ($row['numero_ruta'] != null) {
            $row['lectura_fisica'] = "CARGA CONSOLIDADA";
        } else {
            $txt = "";
            $f = (float)$row['factor_conversion'];
            $val = (float)$row['total_bultos'];
            
            if ($f > 1) {
                $entero = floor($val / $f);
                $resto = fmod($val, $f);
                if ($entero > 0) $txt .= (int)$entero . " " . $row['unidad_reporte'];
                if ($resto > 0) $txt .= ($txt !== "" ? " y " : "") . $resto . " " . $row['unidad_medida'];
            } else {
                $txt = $val . " " . $row['unidad_medida'];
            }
            $row['lectura_fisica'] = ($txt === "") ? "0" : $txt;
        }

        $row['lotes_involucrados'] = $row['lotes_involucrados'] ?? 'SIN LOTE';
        $data[] = $row;
    }

    return $data;
}
/**
 * Detalle de un movimiento simple (sin ruta/reparto)
 * Trae: cliente, producto, cantidad, quién despachó en sistema,
 * quién entregó físicamente, cuándo se entregó.
 */
public function getDetalleMovimientoNormal($movimiento_id) {
    $sql = "SELECT 
                m.id AS movimiento_id,
                m.cantidad,
                p.nombre AS producto,
                p.unidad_medida,
                p.unidad_reporte,
                p.factor_conversion,
                DATE_FORMAT(m.fecha, '%d/%m/%Y %H:%i') AS fecha_salida,

                -- Venta y Cliente
                v.folio AS folio_venta,
                c.nombre_comercial AS cliente,
                c.telefono AS cliente_telefono,
                c.direccion AS cliente_direccion,

                -- Quién registró el movimiento en el sistema
                u_asigna.nombre AS usuario_asigno_sistema,

                -- Quién validó la salida en el sistema (admin de patio)
                u_despacho.nombre AS usuario_valida_patio,

                -- Quién entregó físicamente (trabajador de patio)
                u_patio.nombre AS trabajador_despacho_patio,

                -- Cuándo salió físicamente
                DATE_FORMAT(rsl.fecha_despacho, '%d/%m/%Y %H:%i') AS fecha_despacho

            FROM movimientos m
            INNER JOIN productos p ON m.producto_id = p.id
            LEFT JOIN ventas v ON m.referencia_id = v.id
            LEFT JOIN clientes c ON v.id_cliente = c.id
            LEFT JOIN usuarios u_asigna ON m.usuario_registra_id = u_asigna.id
            LEFT JOIN registro_salida_lotes rsl ON m.id = rsl.movimiento_id
            LEFT JOIN usuarios u_patio ON rsl.usuario_patio_id = u_patio.id
            LEFT JOIN usuarios u_despacho ON rsl.usuario_despacho_id = u_despacho.id
            WHERE m.id = ?
            LIMIT 1";

    $stmt = $this->db->prepare($sql);
    $stmt->bind_param("i", $movimiento_id);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();

    if (!$res) return null;

    // Formateo de cantidad con factor de conversión
    $cantidad = floatval($res['cantidad']);
    $factor   = floatval($res['factor_conversion'] ?? 1);

    if ($factor > 1) {
        $enteros   = (int) floor($cantidad / $factor);
        $sobrantes = fmod($cantidad, $factor);
        $res['cantidad_display'] = $sobrantes > 0
            ? "{$enteros} {$res['unidad_reporte']} + {$sobrantes} {$res['unidad_medida']}"
            : "{$enteros} {$res['unidad_reporte']}";
    } else {
        $res['cantidad_display'] = "{$cantidad} {$res['unidad_medida']}";
    }

    return $res;
}

/**
 * Obtiene el listado completo de viajes con detalle de rutas, 
 * productos, conductores y ayudantes.
 * * @param PDO $db Conexión a la base de datos sistema_almacenes
 * @return array Lista de movimientos logísticos
 */
/**
 * Obtiene el reporte de viajes. 
 * Si se envía un folio, filtra por ese específico; si no, trae todos.
 */
/**
 * Obtiene el reporte detallado de un viaje por su folio o el listado general.
 * Adaptado para el sistema cfsistem.
 * * @param string|null $folio_folio El folio del viaje (ej: RUT-260324-02-25)
 * @return array Arreglo asociativo con los datos para el modal
 */
public function obtenerViajesLogistica($folio_folio = null) {
    try {
        $sql = "SELECT 
                    tc.viaje_folio AS folio_viaje,
                    tc.fecha_creacion AS fecha_viaje,
                    trm.estado_reparto AS estatus_logistico,
                    tv.nombre AS unidad_nombre,
                    tv.placas AS unidad_placas,
                    u_chofer.nombre AS nombre_chofer,
                    (SELECT GROUP_CONCAT(u_ayu.nombre SEPARATOR ' / ') 
                     FROM transporte_tripulantes_detalle ttd
                     INNER JOIN usuarios u_ayu ON ttd.usuario_id = u_ayu.id
                     WHERE ttd.reparto_id = tc.reparto_id) AS ayudantes,
                    trp.orden_visita,
                    trp.descripcion_punto AS direccion_entrega,
                    trp.estado_punto AS estatus_parada,
                    trp.latitud, 
                    trp.longitud,
                    v.folio AS folio_venta,
                    c.nombre_comercial AS cliente,
                    c.telefono AS tel_cliente,
                    p.nombre AS producto_nombre,
                    m.cantidad,
                    p.unidad_medida AS um,
                    p.sku AS SKU
                FROM transporte_consolidacion tc
                INNER JOIN transporte_repartos_maestro trm ON tc.reparto_id = trm.id
                INNER JOIN transporte_vehiculos tv ON tc.vehiculo_id = tv.id
                INNER JOIN transporte_rutas_puntos trp ON trm.id = trp.reparto_id 
                INNER JOIN movimientos m ON trm.entrega_venta_id = m.id
                INNER JOIN productos p ON m.producto_id = p.id
                LEFT JOIN ventas v ON m.referencia_id = v.id
                LEFT JOIN clientes c ON v.id_cliente = c.id
                LEFT JOIN usuarios u_chofer ON trm.usuario_encargado_id = u_chofer.id";

        if (!empty($folio_folio)) {
            $sql .= " WHERE tc.viaje_folio = ?";
        }

        $sql .= " ORDER BY tc.fecha_creacion DESC, tc.viaje_folio ASC, trp.orden_visita ASC";

        $stmt = $this->db->prepare($sql);

        if (!empty($folio_folio)) {
            $stmt->bind_param("s", $folio_folio);
        }

        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    } catch (Exception $e) {
        throw new Exception("Error en la base de datos: " . $e->getMessage());
    }
}
}
