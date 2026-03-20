<?php
class VehiculoModel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    // Listar todos (Para Admin Global)
    public function listar() {
        $sql = "SELECT * FROM transporte_vehiculos WHERE activo = 1 ORDER BY nombre ASC";
        $res = $this->db->query($sql);
        return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    }

    // NUEVO: Listar vehículos por almacén específico
    public function listarPorAlmacen($almacen_id) {
        $id = intval($almacen_id);
        $sql = "SELECT * FROM transporte_vehiculos 
                WHERE activo = 1 AND almacen_id = $id 
                ORDER BY nombre ASC";
        $res = $this->db->query($sql);
        return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function guardar($d) {
        $id         = isset($d['id']) ? intval($d['id']) : 0;
        $nombre     = $this->db->real_escape_string($d['nombre']);
        $placas     = strtoupper($this->db->real_escape_string($d['placas']));
        $serie      = $this->db->real_escape_string($d['serie_vin'] ?? '');
        $modelo     = intval($d['modelo_año'] ?? 0);
        $capacidad  = floatval($d['capacidad_carga_kg'] ?? 0);
        $estado     = $this->db->real_escape_string($d['estado_unidad'] ?? 'disponible');
        $alm_id     = intval($d['almacen_id']); // Nuevo campo obligatorio

        if ($id > 0) {
            // Actualización - Incluimos almacen_id
            $sql = "UPDATE transporte_vehiculos SET 
                    nombre='$nombre', 
                    placas='$placas', 
                    serie_vin='$serie', 
                    modelo_año=$modelo, 
                    capacidad_carga_kg=$capacidad, 
                    estado_unidad='$estado',
                    almacen_id=$alm_id 
                    WHERE id=$id";
        } else {
            // Inserción - Se asigna el almacén desde el registro
            $sql = "INSERT INTO transporte_vehiculos 
                    (nombre, placas, serie_vin, modelo_año, capacidad_carga_kg, estado_unidad, activo, almacen_id) 
                    VALUES 
                    ('$nombre', '$placas', '$serie', $modelo, $capacidad, '$estado', 1, $alm_id)";
        }
        
        return $this->db->query($sql);
    }

    public function eliminar($id) {
        $id = intval($id);
        return $this->db->query("UPDATE transporte_vehiculos SET activo = 0 WHERE id = $id");
    }

    // Ajustado para el módulo de Repartos filtrando por sucursal
    public function listarDisponiblesRuta($almacen_id = 0) {
        $whereAlmacen = ($almacen_id > 0) ? " AND almacen_id = " . intval($almacen_id) : "";
        
        $sql = "SELECT id, nombre, placas, capacidad_carga_kg 
                FROM transporte_vehiculos 
                WHERE activo = 1 
                AND estado_unidad = 'disponible'
                $whereAlmacen
                ORDER BY nombre ASC";
        $res = $this->db->query($sql);
        return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function actualizarEstado($id, $nuevo_estado) {
        $id = intval($id);
        $estadosPermitidos = ['disponible', 'en_ruta', 'mantenimiento', 'fuera_servicio'];
        if (!in_array($nuevo_estado, $estadosPermitidos)) return false;

        $sql = "UPDATE transporte_vehiculos SET estado_unidad = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("si", $nuevo_estado, $id);
        return $stmt->execute();
    }

    public function obtenerEstado($id) {
        $sql = "SELECT estado_unidad FROM transporte_vehiculos WHERE id = ? LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();
        return $res ? $res['estado_unidad'] : null;
    }
}