<?php
class ClientesModel {
    private $db;

    public function __construct($conexion) {
        $this->db = $conexion;
    }

    public function listarTodos() {
        $sql = "SELECT * FROM clientes ORDER BY activo DESC, nombre_comercial ASC";
        $result = $this->db->query($sql);
        $clientes = [];
        while ($row = $result->fetch_assoc()) {
            $clientes[] = $row;
        }
        return $clientes;
    }

    public function guardar($datos) {
        $sql = "INSERT INTO clientes (
            nombre_comercial, razon_social, rfc, regimen_fiscal, 
            codigo_postal, correo, telefono, direccion, uso_cfdi, activo
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 1)";

        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("sssssssss", 
            $datos['nombre_comercial'], $datos['razon_social'], $datos['rfc'],
            $datos['regimen_fiscal'], $datos['codigo_postal'], $datos['correo'],
            $datos['telefono'], $datos['direccion'], $datos['uso_cfdi']
        );
        return $stmt->execute();
    }

    public function actualizar($id, $datos) {
        $sql = "UPDATE clientes SET 
            nombre_comercial = ?, razon_social = ?, rfc = ?, 
            regimen_fiscal = ?, codigo_postal = ?, correo = ?, 
            telefono = ?, direccion = ?, uso_cfdi = ?
            WHERE id = ?";

        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("sssssssssi", 
            $datos['nombre_comercial'], $datos['razon_social'], $datos['rfc'],
            $datos['regimen_fiscal'], $datos['codigo_postal'], $datos['correo'],
            $datos['telefono'], $datos['direccion'], $datos['uso_cfdi'], $id
        );
        return $stmt->execute();
    }

    public function obtenerPorId($id) {
        $stmt = $this->db->prepare("SELECT * FROM clientes WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function cambiarEstado($id, $estado) {
        $stmt = $this->db->prepare("UPDATE clientes SET activo = ? WHERE id = ?");
        $stmt->bind_param("ii", $estado, $id);
        return $stmt->execute();
    }
}