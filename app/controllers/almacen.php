<?php
require_once __DIR__ . '/../models/AlmacenModel.php';

class AlmacenController {
    private $model;

    public function __construct($conexion) {
        $this->model = new AlmacenModel($conexion);
    }

    public function index() {
        // Lógica de sesión (protegerPagina ya debe estar incluido)
        $almacen_usuario = $_SESSION['almacen_id'] ?? 0;

        // Pedimos los datos al modelo
        $categorias = $this->model->getCategorias();
        $almacenes = $this->model->getAlmacenes($almacen_usuario);
        $productos = $this->model->getInventario($almacen_usuario);

        // Cargamos la vista (pasamos las variables)
        require_once __DIR__ . '/../views/almacen/almacenes_view.php';
    }
}