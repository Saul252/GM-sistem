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
    $sql = "SELECT 
                m.id as movimiento_id,
                p.nombre as producto,
                m.cantidad,
                v.folio as folio_venta,
                rp.descripcion_punto as destino,
                rp.id as punto_id
            FROM transporte_consolidacion tc
            INNER JOIN transporte_repartos_maestro trm ON tc.reparto_id = trm.id
            INNER JOIN movimientos m ON trm.entrega_venta_id = m.id
            INNER JOIN productos p ON m.producto_id = p.id
            LEFT JOIN ventas v ON m.referencia_id = v.id
            LEFT JOIN transporte_rutas_puntos rp ON trm.id = rp.reparto_id
            WHERE tc.viaje_folio = ?
            GROUP BY m.id";

    $stmt = $this->db->prepare($sql);
    $stmt->bind_param("s", $folio_viaje);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
}
