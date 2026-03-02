<?php
session_start();
require_once __DIR__ . '/config/conexion.php';

// Indicamos que la respuesta será JSON
header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["status" => "error", "message" => "Método no permitido"]);
    exit();
}

$usuario  = isset($_POST['usuario']) ? trim($_POST['usuario']) : '';
$password = isset($_POST['password']) ? trim($_POST['password']) : '';

if ($usuario === '' || $password === '') {
    echo json_encode(["status" => "error", "message" => "Por favor, completa todos los campos"]);
    exit();
}

// 1. Buscamos al usuario (quitamos activo=1 de la consulta para validar el estado después)
$sql = "SELECT u.id, u.nombre, u.username, u.password, u.rol_id, u.almacen_id, u.activo, r.nombre AS rol
        FROM usuarios u
        INNER JOIN roles r ON u.rol_id = r.id
        WHERE u.username = ? 
        LIMIT 1";

$stmt = $conexion->prepare($sql);
if (!$stmt) {
    echo json_encode(["status" => "error", "message" => "Error interno en el servidor"]);
    exit();
}

$stmt->bind_param("s", $usuario);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado && $resultado->num_rows === 1) {
    $row = $resultado->fetch_assoc();

    // 2. ¿El usuario existe pero está deshabilitado?
    if ($row['activo'] == 0) {
        echo json_encode(["status" => "warning", "message" => "Tu usuario está deshabilitado. Contacta al administrador."]);
        exit();
    }

    // 3. Verificar Contraseña
    if (password_verify($password, $row['password'])) {
        session_regenerate_id(true);

        // Guardamos las variables globales en la sesión
        $_SESSION['usuario_id'] = $row['id'];
        $_SESSION['username']   = $row['username'];
        $_SESSION['nombre']     = $row['nombre'];
        $_SESSION['rol_id']     = $row['rol_id'];
        $_SESSION['rol']        = $row['rol'];
        $_SESSION['almacen_id'] = $row['almacen_id'] ?? 0;
        $_SESSION['login']      = true;

        echo json_encode(["status" => "success", "message" => "¡Bienvenido, " . $row['nombre'] . "!"]);
    } else {
        echo json_encode(["status" => "error", "message" => "La contraseña es incorrecta"]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "El usuario ingresado no existe"]);
}
exit();