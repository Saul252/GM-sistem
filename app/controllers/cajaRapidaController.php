<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../controllers/LayoutController.php';
require_once __DIR__ . '/../models/ventas_model.php'; 
require_once __DIR__ . '/../models/clientesModel.php';
require_once __DIR__ . '/../models/almacen_model.php';
require_once __DIR__ . '/../models/categoriasModel.php';
require_once __DIR__ . '/../models/cajaRapidaModel.php'; 
require_once __DIR__ . '/../models/entregasModel.php';
require_once __DIR__ . '/../models/movimientosModel.php';
require_once __DIR__ . '/../models/vehiculos_model.php'; // Verifica que el nombre de archivo coincida
require_once __DIR__ . '/../models/trabajadores_model.php';
require_once __DIR__ . '/../models/RepartosModel.php'; // Modelo necesario para patio

protegerPagina('ventas'); 
$paginaActual = 'ventas';

// Instancias
$modeloEntrega    = new EntregaModel($conexion);
$modeloMovimiento = new MovimientoModel($conexion);
$vehiculoM        = new VehiculoModel($conexion);
$trabajadorM      = new TrabajadorModel($conexion);
$repartoM         = new RepartoModel($conexion); // Instancia para la lógica de patio

// --- 1. LÓGICA DE PETICIONES AJAX (GET) ---
$action = $_GET['action'] ?? $_POST['action'] ?? '';

if ($action === 'get_recursos_reparto') {
    if (ob_get_length()) ob_clean();
    header('Content-Type: application/json');
    $movimiento_id = intval($_GET['id'] ?? 0);
    $detalle = $modeloEntrega->getDetalleParaDespacho($movimiento_id);
    echo json_encode($detalle ? ["success" => true, "data" => ["entrega" => $detalle]] : ["success" => false, "message" => "Movimiento no encontrado"]);
    exit;
}

if ($action === 'get_recursos_sucursal') {
    if (ob_get_length()) ob_clean();
    header('Content-Type: application/json');
    $almacen_id = intval($_GET['almacen_id'] ?? 0);
    echo json_encode([
        "success"  => true,
        "unidades" => $vehiculoM->listarPorAlmacen($almacen_id),
        "choferes" => $trabajadorM->listarPorAlmacen($almacen_id)
    ]);
    exit;
}

// --- 2. LÓGICA DE GUARDADO (POST) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (ob_get_length()) ob_clean();
    header('Content-Type: application/json');

    // CASO A: Entrega en Patio (Viene de FormData)
    if ($action === 'entregar_en_patio') {
        try {
            if (empty($_POST['movimiento_id']) || empty($_POST['chofer_id'])) {
                throw new Exception("Debe indicar el movimiento y el responsable de entrega.");
            }

            // Preparar datos para el modelo
            $datosPatio = $_POST;
            $datosPatio['usuario_sistema_id'] = $_SESSION['id_usuario'] ?? $_SESSION['usuario_id'] ?? 0;
            $datosPatio['vehiculo_id'] = 999; // Vehículo virtual para patio

            $resultadoPatio = $repartoM->entregarEnPatioCliente($datosPatio);
            echo json_encode($resultadoPatio);
        } catch (Exception $e) {
            echo json_encode(["success" => false, "message" => $e->getMessage()]);
        }
        exit;
    }

    // CASO B: Venta Rápida (Viene de JSON payload)
    $input = file_get_contents("php://input");
    $data = json_decode($input, true);

    if ($data) {
        $resultado = cajaRapidaModel::guardarVentaRapida($conexion, $data);
        
        if ($resultado['status'] === 'success') {
            $ventaid = $resultado['id_venta']; 
            $listaMovs = $modeloMovimiento->obtenerIdMovimientoPorVenta($ventaid);

            if (!empty($listaMovs) && is_array($listaMovs)) {
                foreach ($listaMovs as $idMov) {
                    $resDespacho = $modeloEntrega->procesarDespachoFisico($idMov);
                    if (!$resDespacho['success']) {
                        error_log("Error despacho lotes ID: " . $idMov . " - " . $resDespacho['message']);
                    }
                }
            }

            // Formatear mensaje de éxito/parcial
            $resultado['message'] = ($resultado['total_entregado'] < $resultado['total_pedido']) 
                ? "⚠️ Venta {$resultado['folio']} parcial por falta de stock." 
                : "✅ Venta {$resultado['folio']} completada y stock descontado.";
            
            $resultado['movimientos_ids'] = $listaMovs;
        }
        echo json_encode($resultado);
        exit;
    }
}

// --- 3. LÓGICA DE CARGA DE VISTA ---
$almacen_usuario = $_SESSION['almacen_id'] ?? 0;
$almacenModel = new AlmacenModel($conexion);
$almacenes = $almacenModel->getAlmacenes($almacen_usuario);

$categorias_res = CategoriasModel::listar($conexion);
$categorias = ($categorias_res) ? $categorias_res->fetch_all(MYSQLI_ASSOC) : [];

$productos_res = cajaRapidaModel::obtenerProductos($conexion, $almacen_usuario);
$productos = [];
if ($productos_res) {
    while($row = $productos_res->fetch_assoc()){ $productos[] = $row; }
}

$clientesModel = new ClientesModel($conexion);
$clientes_res = $clientesModel->listarTodos($almacen_usuario); 
$clientes = []; 
if ($clientes_res) {
    while($row = $clientes_res->fetch_assoc()){ $clientes[] = $row; }
}

include __DIR__ . '/../views/cajaRapida.php';