<?php
class Cliente {
    private $db;

    public function __construct($conexion) {
        $this->db = $conexion;
    }

    public function listarTodos() {
        $sql = "SELECT * FROM clientes ORDER BY activo DESC, nombre_comercial ASC";
        $res = $this->db->query($sql);
        return $res->fetch_all(MYSQLI_ASSOC);
    }

    public function guardar($d) {
        $nombre = mysqli_real_escape_string($this->db, $d['nombre_comercial']);
        $razon  = mysqli_real_escape_string($this->db, $d['razon_social']);
        $rfc    = strtoupper(mysqli_real_escape_string($this->db, $d['rfc']));
        $cp     = mysqli_real_escape_string($this->db, $d['codigo_postal']);
        $reg    = mysqli_real_escape_string($this->db, $d['regimen_fiscal']);
        $correo = mysqli_real_escape_string($this->db, $d['correo']);
        $tel    = mysqli_real_escape_string($this->db, $d['telefono']);
        $dir    = mysqli_real_escape_string($this->db, $d['direccion']);
        $uso    = mysqli_real_escape_string($this->db, $d['uso_cfdi']);

        $sql = "INSERT INTO clientes (nombre_comercial, razon_social, rfc, codigo_postal, regimen_fiscal, correo, telefono, direccion, uso_cfdi, activo) 
                VALUES ('$nombre', '$razon', '$rfc', '$cp', '$reg', '$correo', '$tel', '$dir', '$uso', 1)";
        return $this->db->query($sql);
    }

    public function editar($id, $d) {
        $id = intval($id);
        $nombre = mysqli_real_escape_string($this->db, $d['nombre_comercial']);
        $razon  = mysqli_real_escape_string($this->db, $d['razon_social']);
        $rfc    = strtoupper(mysqli_real_escape_string($this->db, $d['rfc']));
        $cp     = mysqli_real_escape_string($this->db, $d['codigo_postal']);
        $reg    = mysqli_real_escape_string($this->db, $d['regimen_fiscal']);
        $correo = mysqli_real_escape_string($this->db, $d['correo']);
        $tel    = mysqli_real_escape_string($this->db, $d['telefono']);
        $dir    = mysqli_real_escape_string($this->db, $d['direccion']);
        $uso    = mysqli_real_escape_string($this->db, $d['uso_cfdi']);

        $sql = "UPDATE clientes SET nombre_comercial='$nombre', razon_social='$razon', rfc='$rfc', 
                codigo_postal='$cp', regimen_fiscal='$reg', correo='$correo', telefono='$tel', direccion='$dir', uso_cfdi='$uso'
                WHERE id = $id";
        return $this->db->query($sql);
    }

    public function toggleEstado($id) {
        $id = intval($id);
        return $this->db->query("UPDATE clientes SET activo = 1 - activo WHERE id = $id");
    }
}