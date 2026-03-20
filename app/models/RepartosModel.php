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
}