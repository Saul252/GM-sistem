<?php

require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../includes/auth.php';

require_once __DIR__ . '/../controllers/LayoutController.php';
require_once __DIR__ . '/../models/mermasModel.php';

protegerPagina();

$paginaActual = 'Mermas';
class MermaController {

    private $model;
    private $conn;

    public function __construct($conexion)
    {
        $this->conn = $conexion;
        $this->model = new MermaModel($conexion);
    }


    /* ===============================
    MOSTRAR PAGINA MERMAS
    =============================== */
    public function index()
    {

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $usuario_id = $_SESSION['usuario_id'];
        $rol_id = $_SESSION['rol_id'];
        $almacen_usuario = $_SESSION['almacen_id'];

        /* ADMIN puede ver todo */
        if($rol_id == 1){

            $almacen_id = $_GET['almacen_id'] ?? null;

        }else{

            $almacen_id = $almacen_usuario;

        }

        $productos = null;

        if($almacen_id){

            $productos = $this->model->obtenerProductosPorAlmacen($almacen_id);

        }

        $mermas = $this->model->listarMermas($almacen_id);

        require_once __DIR__ . '/../views/mermas/index.php';
    }



    /* ===============================
    REGISTRAR MERMA
    =============================== */
    public function guardarMerma()
    {

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $data = [

            "producto_id" => $_POST['producto_id'],
            "almacen_id" => $_POST['almacen_id'],
            "cantidad" => $_POST['cantidad'],
            "tipo_merma" => $_POST['tipo_merma'],
            "responsable" => $_POST['responsable'],
            "descripcion" => $_POST['descripcion'],
            "usuario_id" => $_SESSION['usuario_id']

        ];

        $result = $this->model->registrarMerma($data);

        echo json_encode($result);

    }



    /* ===============================
    CONVERSION DE PRODUCTO
    =============================== */
    public function convertir()
    {

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $data = [

            "producto_origen" => $_POST['producto_origen'],
            "producto_destino" => $_POST['producto_destino'],
            "cantidad_origen" => $_POST['cantidad_origen'],
            "cantidad_destino" => $_POST['cantidad_destino'],
            "almacen_id" => $_POST['almacen_id'],
            "descripcion" => $_POST['descripcion'],
            "responsable" => $_POST['responsable'],
            "usuario_id" => $_SESSION['usuario_id']

        ];

        $result = $this->model->convertirProducto($data);

        echo json_encode($result);

    }



    /* ===============================
    OBTENER STOCK (AJAX)
    =============================== */
    public function obtenerStock()
    {

        $producto_id = $_GET['producto_id'];
        $almacen_id = $_GET['almacen_id'];

        $stock = $this->model->obtenerStock($producto_id,$almacen_id);

        echo json_encode($stock);

    }



    /* ===============================
    OBTENER FACTOR CONVERSION
    =============================== */
    public function obtenerFactor()
    {

        $producto_id = $_GET['producto_id'];

        $factor = $this->model->obtenerFactorConversion($producto_id);

        echo json_encode($factor);

    }

}
require_once __DIR__ . '/../views/mermas_view.php';