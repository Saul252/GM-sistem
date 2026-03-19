<?php
class VehiculoModel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function listar() {
        // Solo traemos los que no han sido eliminados lógicamente
        $sql = "SELECT * FROM transporte_vehiculos WHERE activo = 1 ORDER BY nombre ASC";
        $res = $this->db->query($sql);
        return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    }

  
// En VehiculoModel.php

public function guardar($d) {
    $id         = isset($d['id']) ? intval($d['id']) : 0;
    $nombre     = $this->db->real_escape_string($d['nombre']);
    $placas     = strtoupper($this->db->real_escape_string($d['placas']));
    $serie      = $this->db->real_escape_string($d['serie_vin'] ?? '');
    $modelo     = intval($d['modelo_año'] ?? 0);
    $capacidad  = floatval($d['capacidad_carga_kg'] ?? 0);
    $estado     = $this->db->real_escape_string($d['estado_unidad'] ?? 'disponible');

    if ($id > 0) {
        // Actualización
        $sql = "UPDATE transporte_vehiculos SET 
                nombre='$nombre', 
                placas='$placas', 
                serie_vin='$serie', 
                modelo_año=$modelo, 
                capacidad_carga_kg=$capacidad, 
                estado_unidad='$estado' 
                WHERE id=$id";
    } else {
        // Inserción - Incluimos 'activo = 1' por defecto
        $sql = "INSERT INTO transporte_vehiculos 
                (nombre, placas, serie_vin, modelo_año, capacidad_carga_kg, estado_unidad, activo) 
                VALUES 
                ('$nombre', '$placas', '$serie', $modelo, $capacidad, '$estado', 1)";
    }
    
    return $this->db->query($sql);
}
    public function eliminar($id) {
        $id = intval($id);
        // Hacemos un borrado lógico para no perder historial de viajes o mantenimientos
        return $this->db->query("UPDATE transporte_vehiculos SET activo = 0 WHERE id = $id");
    }
}