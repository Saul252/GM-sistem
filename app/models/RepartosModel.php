<?php
class RepartoModel {
    private $db;

    public function __construct($conexion) {
        $this->db = $conexion;
    }

    public function iniciarReparto($datos) {
        $this->db->begin_transaction();
        try {
            $vehiculo_id  = intval($datos['vehiculo_id']);
            $encargado_id = intval($datos['encargado_id']);
            $entrega_id   = intval($datos['entrega_id']);
            $km_inicial   = intval($datos['km_inicial'] ?? 0);
            $tripulantes  = $datos['tripulantes'] ?? []; // Arreglo de IDs

            // 1. Insertar en Maestro de Repartos
            $queryMaestro = "INSERT INTO transporte_repartos_maestro 
                (vehiculo_id, usuario_encargado_id, entrega_venta_id, fecha_programada, hora_salida_real, km_inicial, estado_reparto) 
                VALUES (?, ?, ?, CURDATE(), NOW(), ?, 'en_transito')";
            
            $stmt = $this->db->prepare($queryMaestro);
            $stmt->bind_param("iiii", $vehiculo_id, $encargado_id, $entrega_id, $km_inicial);
            $stmt->execute();
            $reparto_id = $this->db->insert_id;

            // 2. Insertar Tripulantes (Ayudantes/Cargadores)
            if (!empty($tripulantes)) {
                $queryTrip = "INSERT INTO transporte_tripulantes_detalle (reparto_id, usuario_id, rol_secundario) VALUES (?, ?, 'Ayudante')";
                $stmtTrip = $this->db->prepare($queryTrip);
                foreach ($tripulantes as $t_id) {
                    $stmtTrip->bind_param("ii", $reparto_id, $t_id);
                    $stmtTrip->execute();
                }
            }

            // 3. Actualizar estado del Vehículo
            $this->db->query("UPDATE transporte_vehiculos SET estado_unidad = 'en_ruta' WHERE id = $vehiculo_id");

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }

 

   
}