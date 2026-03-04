<?php
// require_once __DIR__ . '/../../includes/auth.php';
// protegerPagina();

// require_once __DIR__ . '/../controllers/sidebar_controller.php';
// require_once __DIR__ . '/../../config/conexion.php';
// require_once __DIR__ . '/../models/VentasModel.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../controllers/LayoutController.php';
require_once __DIR__ . '/../models/ventas_model.php';

$paginaActual = 'Ventas';
$almacen_usuario = $_SESSION['almacen_id'] ?? 0;

// Obtener datos del modelo
$categorias = VentasModel::obtenerCategorias($conexion);
$almacenes = VentasModel::obtenerAlmacenes($conexion, $almacen_usuario);
$productos_res = VentasModel::obtenerProductos($conexion, $almacen_usuario);
$clientes = VentasModel::obtenerClientes($conexion);

$productos = [];
while($row = $productos_res->fetch_assoc()){
    $productos[] = $row;
}

// Cargar la vista
include __DIR__ . '/../views/ventas_view.php';