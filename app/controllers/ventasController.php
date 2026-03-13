<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../controllers/LayoutController.php';
require_once __DIR__ . '/../models/ventas_model.php';
require_once __DIR__ . '/../models/clientesModel.php';
require_once __DIR__ . '/../models/almacen_model.php';
require_once __DIR__ . '/../models/categoriasModel.php';
protegerPagina(); 
$paginaActual = 'ventas';

// 1. Identificar al usuario (Admin = 0, Vendedor = ID Almacén)
$almacen_usuario = $_SESSION['almacen_id'] ?? 0;
// 2. Obtener Almacenes
$almacenModel = new AlmacenModel($conexion);
$almacenes = $almacenModel->getAlmacenes($almacen_usuario);

// 3. Obtener Categorías
$categorias_res = CategoriasModel::listar($conexion);
$categorias = ($categorias_res) ? $categorias_res->fetch_all(MYSQLI_ASSOC) : [];

// 4. Obtener Productos
$productos_res = VentasModel::obtenerProductos($conexion, $almacen_usuario);
$productos = [];
if ($productos_res) {
    while($row = $productos_res->fetch_assoc()){
        $productos[] = $row;
    }
}

// 5. OBTENER CLIENTES (Aquí estaba el detalle)
$clientesModel = new ClientesModel($conexion);
$clientes_res = $clientesModel->listarTodos($almacen_usuario); 

$clientes = []; // Esta es la variable que usa tu vista
if ($clientes_res) {
    while($row = $clientes_res->fetch_assoc()){
        $clientes[] = $row;
    }
}

// 6. Cargar la Vista
include __DIR__ . '/../views/ventas_view.php';