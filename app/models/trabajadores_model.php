<?php
class TrabajadorModel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    // Listar todos (Solo para Admin Global)
    public function listar() {
        $sql = "SELECT * FROM trabajadores ORDER BY nombre ASC";
        $res = $this->db->query($sql);
        return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    }

    // NUEVO: Listar por almacén específico
    public function listarPorAlmacen($almacen_id) {
        $id = intval($almacen_id);
        $sql = "SELECT * FROM trabajadores WHERE almacen_id = $id ORDER BY nombre ASC";
        $res = $this->db->query($sql);
        return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function guardar($d) {
        $nombre = $this->db->real_escape_string($d['nombre']);
        $tel    = $this->db->real_escape_string($d['telefono']);
        $rol    = $this->db->real_escape_string($d['rol']);
        $estado = $this->db->real_escape_string($d['estado']);
        $alm_id = intval($d['almacen_id']); // Nueva columna crítica

        if (!empty($d['id'])) {
            // EDITAR: Incluimos almacen_id por si el admin global lo mueve de sucursal
            $id = intval($d['id']);
            $sql = "UPDATE trabajadores 
                    SET nombre='$nombre', telefono='$tel', rol='$rol', estado='$estado', almacen_id=$alm_id 
                    WHERE id=$id";
        } else {
            // INSERTAR: Obligatorio asignar el almacén desde el inicio
            $sql = "INSERT INTO trabajadores (nombre, telefono, rol, estado, almacen_id) 
                    VALUES ('$nombre', '$tel', '$rol', '$estado', $alm_id)";
        }
        return $this->db->query($sql);
    }

    public function eliminar($id) {
        $id = intval($id);
        return $this->db->query("DELETE FROM trabajadores WHERE id = $id");
    }

    // Ajustado para logística filtrando por almacén
    public function listarPersonalLogistica($almacen_id = 0) {
        // Si mandas 0, busca en todos (opcional), si no, filtra por sucursal
        $whereAlmacen = ($almacen_id > 0) ? " AND almacen_id = " . intval($almacen_id) : "";
        
        $sql = "SELECT id, nombre, rol 
                FROM trabajadores 
                WHERE estado = 'activo' 
                AND rol IN ('chofer', 'cargador') 
                $whereAlmacen
                ORDER BY nombre ASC";
                
        $res = $this->db->query($sql);
        return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    }
}