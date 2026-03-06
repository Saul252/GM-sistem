<?php
class ProveedoresModel {
    private $db;

    public function __construct($conexion) {
        $this->db = $conexion;
    }

    public function listarTodos() {
        $sql = "SELECT * FROM proveedores ORDER BY activo DESC, nombre_comercial ASC";
        $result = $this->db->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function guardar($datos) {
        $sql = "INSERT INTO proveedores (nombre_comercial, razon_social, rfc, correo, telefono, activo) 
                VALUES (?, ?, ?, ?, ?, 1)";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("sssss", 
            $datos['nombre_comercial'], $datos['razon_social'], 
            $datos['rfc'], $datos['correo'], $datos['telefono']
        );
        return $stmt->execute();
    }

    public function obtenerPorId($id) {
        $stmt = $this->db->prepare("SELECT * FROM proveedores WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function cambiarEstado($id, $estado) {
        $stmt = $this->db->prepare("UPDATE proveedores SET activo = ? WHERE id = ?");
        $stmt->bind_param("ii", $estado, $id);
        return $stmt->execute();
    }
}