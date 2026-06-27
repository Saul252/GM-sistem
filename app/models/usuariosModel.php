<?php
class UsuarioModel {
    private $db;

    public function __construct($conexion) {
        $this->db = $conexion;
    }public function listarUsuarios($id = 0) {
    // 1. Estructura base de la consulta (WHERE va antes de ORDER BY)
    $sql = "SELECT u.id, u.nombre, u.username, u.rol_id, u.almacen_id, u.activo,
                   r.nombre AS rol_nombre, IFNULL(a.nombre, 'Acceso Global') AS almacen_nombre
            FROM usuarios u
            LEFT JOIN roles r ON u.rol_id = r.id
            LEFT JOIN almacenes a ON u.almacen_id = a.id
            WHERE 1=1";
            
    $params = [];
    $types = "";

    // 2. Filtro dinámico
    if ($id > 0) {
        $sql .= " AND u.id = ?";
        $types .= "i";
        $params[] = $id;
    }

    // 3. El ordenamiento se concatena al final de todo
    $sql .= " ORDER BY u.nombre ASC";

    // 4. Ejecución segura con Query Prepared Statements
    $data = [];
    
    if ($id > 0) {
        // Si hay parámetros, preparamos la consulta para evitar Inyección SQL
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $res = $stmt->get_result();
    } else {
        // Si no hay parámetros, se ejecuta directo de forma segura
        $res = $this->db->query($sql);
    }

    // 5. Llenado del array de resultados
    if ($res) {
        while ($row = $res->fetch_assoc()) {
            $data[] = $row;
        }
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