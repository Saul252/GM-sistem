<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../models/autentificacionModel.php';

$loginModel = new AuthModel($conexion);

// --- ACCIÓN: LOGIN (AJAX POST) ---
if (isset($_GET['action']) && $_GET['action'] === 'login') {

    if (ob_get_level()) ob_clean();
    header('Content-Type: application/json; charset=utf-8');

    try {
        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            throw new Exception("Método no permitido.");
        }

        $usuario  = trim($_POST['usuario'] ?? '');
        $password = trim($_POST['password'] ?? '');

        if ($usuario === '' || $password === '') {
            throw new Exception("Por favor, completa todos los campos.");
        }

        // 🔹 1. Buscar usuario
        $row = $loginModel->obtenerPorUsername($usuario);

        if (!$row) {
            throw new Exception("El usuario ingresado no existe.");
        }

        // 🔹 2. Validar estado activo
        if ((int)$row['activo'] === 0) {
            echo json_encode([
                'status'  => 'warning',
                'message' => 'Tu usuario está deshabilitado. Contacta al administrador.'
            ]);
            exit;
        }

        // 🔹 3. Verificar Contraseña
        if (!password_verify($password, $row['password'])) {
            throw new Exception("La contraseña es incorrecta.");
        }

        // 🔹 4. Iniciar Sesión y asignar variables
        session_regenerate_id(true);

        $_SESSION['usuario_id'] = $row['id'];
        $_SESSION['username']   = $row['username'];
        $_SESSION['nombre']     = $row['nombre'];
        $_SESSION['rol_id']     = $row['rol_id'];
        $_SESSION['rol']        = $row['rol'];
        $_SESSION['almacen_id'] = $row['almacen_id'] ?? 0;
        $_SESSION['login']      = true;

        // 🔹 5. Configuración de Almacén y Hora de Cierre
        $id_almacen_usuario = intval($_SESSION['almacen_id']);
        $hora_cierre_config = "11:58";

        if ($id_almacen_usuario > 0) {
            $hora_cierre_config = $loginModel->obtenerHoraCierreAlmacen($id_almacen_usuario);
        }
        $_SESSION['hora_cierre'] = $hora_cierre_config;

        // 🔹 6. Vinculación automática de perfil Trabajador
        if (strpos($_SESSION['username'], 'Trabajador') !== false) {
            $nombreLimpio = str_replace('Trabajador', '', $_SESSION['username']);
            $resT = $loginModel->buscarTrabajadorPorNombre($nombreLimpio);

            if ($resT) {
                $_SESSION['trabajador_id'] = $resT['id'];
                if (!empty($resT['almacen_id'])) {
                    $_SESSION['almacen_id'] = $resT['almacen_id'];
                }
            } else {
                $_SESSION['trabajador_id'] = 0;
            }
        } else {
            $_SESSION['trabajador_id'] = 0;
        }

        // 🔹 7. Respuesta JSON exitosa
        echo json_encode([
            'status'      => 'success',
            'message'     => '¡Bienvenido, ' . $row['nombre'] . '!',
            'hora_cierre' => $_SESSION['hora_cierre'],
            'redirect'    => 'app/views/inicio.php'
        ]);

    } catch (Throwable $e) {

        http_response_code(400);

        echo json_encode([
            'status'  => 'error',
            'message' => $e->getMessage()
        ]);
    }

    exit;
}