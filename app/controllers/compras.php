<?php
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../models/compras_model.php';

class ComprasController {

    private $model;

    public function __construct($conexion) {
        $this->model = new CompraModel($conexion);
    }

    public function index() {

        $fecha_desde = $_GET['desde'] ?? date('Y-m-01');
        $fecha_hasta = $_GET['hasta'] ?? date('Y-m-d');
        $folio = $_GET['folio_busqueda'] ?? '';
        $usuario = $_GET['usuario_busqueda'] ?? '';

        $usuarios = $this->model->obtenerUsuarios();
        $almacenes = $this->model->obtenerAlmacenes();
        $categorias = $this->model->obtenerCategorias();
        $productos = $this->model->obtenerProductos();

        $totales = $this->model->obtenerTotales($fecha_desde, $fecha_hasta, $folio, $usuario);
        $egresos = $this->model->obtenerEgresos($fecha_desde, $fecha_hasta, $folio, $usuario);

        require __DIR__ . '/../views/compras_view.php';
    }
}