<?php
class TrabajadorModel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function listar() {
        $sql = "SELECT * FROM trabajadores ORDER BY nombre ASC";
        $res = $this->db->query($sql);
        return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function guardar($d) {
        $nombre = $this->db->real_escape_string($d['nombre']);
        $tel = $this->db->real_escape_string($d['telefono']);
        $rol = $this->db->real_escape_string($d['rol']);
        $estado = $this->db->real_escape_string($d['estado']);

        if (!empty($d['id'])) {
            // EDITAR
            $id = intval($d['id']);
            $sql = "UPDATE trabajadores SET nombre='$nombre', telefono='$tel', rol='$rol', estado='$estado' WHERE id=$id";
        } else {
            // INSERTAR
            $sql = "INSERT INTO trabajadores (nombre, telefono, rol, estado) VALUES ('$nombre', '$tel', '$rol', '$estado')";
        }
        return $this->db->query($sql);
    }

    public function eliminar($id) {
        $id = intval($id);
        return $this->db->query("DELETE FROM trabajadores WHERE id = $id");
    }
}