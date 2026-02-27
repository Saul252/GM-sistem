<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require_once __DIR__ . '/config/conexion.php';

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: index.php");
    exit();
}

$usuario  = isset($_POST['usuario']) ? trim($_POST['usuario']) : '';
$password = isset($_POST['password']) ? trim($_POST['password']) : '';

if ($usuario === '' || $password === '') {
    header("Location: index.php?error=campos");
    exit();
}

$sql = "SELECT u.id, u.nombre, u.username, u.password, u.rol_id, r.nombre AS rol
        FROM usuarios u
        INNER JOIN roles r ON u.rol_id = r.id
        WHERE u.username = ? AND u.activo = 1
        LIMIT 1";

$stmt = $conexion->prepare($sql);

if (!$stmt) {
    die("Error en prepare: " . $conexion->error);
}

$stmt->bind_param("s", $usuario);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado && $resultado->num_rows === 1) {

    $row = $resultado->fetch_assoc();

    if (password_verify($password, $row['password'])) {

        session_regenerate_id(true);

        $_SESSION['usuario_id'] = $row['id'];
        $_SESSION['username']   = $row['username'];
        $_SESSION['nombre']     = $row['nombre'];
        $_SESSION['rol_id']     = $row['rol_id'];
        $_SESSION['rol']        = $row['rol'];
        $_SESSION['login']      = true;

        header("Location: app/views/inicio.php");
        exit();

    } else {
        header("Location: index.php?error=password");
        exit();
    }

} else {
    header("Location: index.php?error=usuario");
    exit();
}
?>