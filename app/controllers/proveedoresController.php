<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../controllers/LayoutController.php';
require_once __DIR__ . '/../models/proveedoresModel.php';
protegerPagina(); 
$model = new ProveedoresModel($conexion);
$paginaActual = 'proveedores';

if (isset($_GET['action'])) {
    if (ob_get_level()) ob_clean();
    header('Content-Type: application/json');
    
    try {
        if ($_GET['action'] === 'guardar') {
            $datos = [
                'nombre_comercial' => trim($_POST['nombre_comercial']),
                'razon_social'     => trim($_POST['razon_social'] ?? ''),
                'rfc'              => strtoupper(trim($_POST['rfc'] ?? '')),
                'correo'           => trim($_POST['correo'] ?? ''),
                'telefono'         => trim($_POST['telefono'] ?? '')
            ];
            $model->guardar($datos);
            echo json_encode(['success' => true, 'message' => 'Proveedor guardado']);
        }
        // ... puedes agregar cambiarEstado u obtenerPorId aquí igual que en clientes
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}

$proveedores = $model->listarTodos();
$tituloPagina = "Catálogo de Proveedores";
require_once __DIR__ . '/../views/proveedores_view.php';