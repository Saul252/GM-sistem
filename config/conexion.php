<?php
$conexion = new mysqli("localhost", "root", "", "sistema_almacenes");
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}
?>
