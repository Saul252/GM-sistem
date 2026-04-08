<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../controllers/LayoutController.php';
require_once __DIR__ . '/../models/corteCajaModel.php';

protegerPagina('corteCaja');

$modelo = new CorteCajaModel($conexion);
$almacen_usuario = $_SESSION['almacen_id'] ?? 0;

// Manejo de petición AJAX para la tabla
if (isset($_GET['ajax'])) {
    try {
        header('Content-Type: application/json');
        $data = $modelo->obtenerVentasDetalladas($_GET, $almacen_usuario);
        echo json_encode(['data' => $data]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }
    exit;
}

// Carga normal de la página
$paginaActual = 'Corte de Caja';
require_once __DIR__ . '/../views/corteCaja_view.php';