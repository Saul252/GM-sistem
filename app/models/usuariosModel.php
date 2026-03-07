<?php
class UsuarioModel {
    private $db;

    public function __construct($conexion) {
        $this->db = $conexion;
    }
public function listarUsuarios() {
    $sql = "SELECT u.id, u.nombre, u.username, u.rol_id, u.almacen_id, u.activo,
                   r.nombre AS rol_nombre, IFNULL(a.nombre, 'Acceso Global') AS almacen_nombre
            FROM usuarios u
            LEFT JOIN roles r ON u.rol_id = r.id
            LEFT JOIN almacenes a ON u.almacen_id = a.id
            ORDER BY u.nombre ASC";
    $res = $this->db->query($sql);
    $data = [];
    while($row = $res->fetch_assoc()){
        $data[] = $row;
    }
    return $data;
}
    public function getRoles() {
        return $this->db->query("SELECT id, nombre FROM roles ORDER BY nombre ASC")->fetch_all(MYSQLI_ASSOC);
    }

    public function getAlmacenes() {
        return $this->db->query("SELECT id, nombre FROM almacenes WHERE activo = 1 ORDER BY nombre ASC")->fetch_all(MYSQLI_ASSOC);
    }
}