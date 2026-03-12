<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../controllers/LayoutController.php';
require_once __DIR__ . '/../models/ventas_model.php';
require_once __DIR__ . '/../models/clientesModel.php'; 

$paginaActual = 'Ventas';
$almacen_usuario = $_SESSION['almacen_id'] ?? 0;

// Instanciamos y obtenemos Clientes como ARRAY
$clientesModel = new ClientesModel($conexion);
$clientes = $clientesModel->listarTodos($almacen_usuario);

// Obtener datos de Ventas
$categorias = VentasModel::obtenerCategorias($conexion);
$almacenes = VentasModel::obtenerAlmacenes($conexion, $almacen_usuario);
$productos_res = VentasModel::obtenerProductos($conexion, $almacen_usuario);

$productos = [];
while($row = $productos_res->fetch_assoc()){
    $productos[] = $row;
}

include __DIR__ . '/../views/ventas_view.php';