<?php



require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../controllers/LayoutController.php';
require_once __DIR__ . '/../models/corteCajaModel.php';
protegerPagina();
$modelo = new CorteCajaModel($conexion);
$paginaActual = 'corteCaja';
$almacen_usuario = $_SESSION['almacen_id'] ?? 0;

if (isset($_GET['ajax'])) {
    header('Content-Type: application/json');
    $data = $modelo->obtenerVentasDetalladas($_GET, $almacen_usuario);
    echo json_encode(['data' => $data]);
    exit;
}

$paginaActual = 'Corte de Caja';
require_once __DIR__ . '/../views/corteCaja_view.php';