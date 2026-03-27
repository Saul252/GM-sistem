<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../models/entregasModel.php'; 
// Asegúrate de que estos nombres de archivo de modelo sean los correctos en tu carpeta models
require_once __DIR__ . '/../models/RepartosModel.php'; 
require_once __DIR__ . '/../models/vehiculos_model.php'; 
require_once __DIR__ . '/../models/trabajadores_model.php'; 
require_once __DIR__ . '/../controllers/LayoutController.php';

protegerPagina('entregas'); 
$paginaActual = 'entregas';

// Instanciación de modelos
$modelo      = new EntregaModel($conexion);
$repartoM    = new RepartoModel($conexion);
$vehiculoM   = new VehiculoModel($conexion);
$trabajadorM = new TrabajadorModel($conexion);

// Datos de sesión
$almacen_usuario = $_SESSION['almacen_id'] ?? 0;
$es_admin        = (isset($_SESSION['rol_id']) && $_SESSION['rol_id'] == 1); 

if(!isset($_SESSION['id_usuario']) && isset($_SESSION['usuario_id'])){
    $_SESSION['id_usuario'] = $_SESSION['usuario_id'];
}

// --- MANEJO DE PETICIONES AJAX (GET) ---
if (isset($_GET['ajax'])) {
    header('Content-Type: application/json');
    try {
        switch ($_GET['ajax']) {
            case 'listar':
                $entregas = $modelo->listarSalidasPendientes($_GET, $almacen_usuario, $es_admin);
                echo json_encode(['data' => $entregas]);
                break;

            case 'get_recursos_reparto':
                $movimiento_id = $_GET['id'] ?? 0;
                $detalle = $modelo->getDetalleParaDespacho($movimiento_id);
                if (!$detalle) {
                    echo json_encode(["success" => false, "message" => "Movimiento no encontrado"]);
                } else {
                    echo json_encode(["success" => true, "data" => ["entrega" => $detalle]]);
                }
                break;

            case 'get_recursos_sucursal':
                $almacen_id = intval($_GET['almacen_id'] ?? 0);
                echo json_encode([
                    "success"  => true,
                    "unidades" => $vehiculoM->listarPorAlmacen($almacen_id),
                    "choferes" => $trabajadorM->listarPorAlmacen($almacen_id)
                ]);
                break;

            case 'simular':
                $id = intval($_GET['id'] ?? 0);
                echo json_encode($modelo->simularDespachoLotes($id));
                break;
case 'get_resumen_despacho':
                $movimiento_id = intval($_GET['id'] ?? 0);
                $resumen = $repartoM->obtenerHistorialFisico($movimiento_id);
                
                if ($resumen) {
                    if (empty($resumen['tripulantes']) && !empty($resumen['reparto_id'])) {
                        $resumen['tripulantes'] = $repartoM->getTripulantesPorReparto($resumen['reparto_id']);
                    }
                    echo json_encode(["success" => true, "data" => $resumen]);
                } else {
                    echo json_encode(["success" => false, "message" => "No se encontró registro."]);
                }
                break;
                case 'get_ids_pendientes_venta':
        // Obtiene solo los IDs [193, 194...] de una venta para procesarlos
        $venta_id = intval($_GET['venta_id'] ?? 0);
        $ids = $repartoM->listarIdsPendientesPorVenta($venta_id);
        echo json_encode(['success' => true, 'ids' => $ids]);
        break;

    case 'simular_masivo':
        // Muestra qué lotes se verán afectados antes de hacer el movimiento real
        // Recibe: entregasController.php?ajax=simular_masivo&ids[]=193&ids[]=194
        $ids = $_GET['ids'] ?? [];
        if (empty($ids)) throw new Exception("No hay IDs para simular.");
        echo json_encode($repartoM->simularDespachoLotesMasivo($ids));
        break;


            case 'imprimir':
            case 'imprimirGanancia':
                $id = intval($_GET['id'] ?? 0);
                if ($id <= 0) throw new Exception("ID de movimiento no válido.");
                $datos = ($_GET['ajax'] === 'imprimir') ? $modelo->obtenerDatosImpresion($id) : $modelo->obtenerDatosVentaGananciaImpresion($id);
                if (!$datos) throw new Exception("No se encontraron datos para la impresión.");
                echo json_encode(['success' => true, 'data' => $datos]);
                break;
                
            default:
                throw new Exception("Acción AJAX no reconocida.");
        }
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit; 
}

// --- MANEJO DE PETICIONES AJAX (POST) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['ajax'] ?? '';
    header('Content-Type: application/json');

    try {
        // Validación centralizada de sesión
        $id_usuario_sesion = $_SESSION['id_usuario'] ?? $_SESSION['usuario_id'] ?? 0;
        if ($id_usuario_sesion <= 0) {
            throw new Exception("Sesión expirada o usuario no identificado.");
        }

        // 1. DESPACHO MASIVO POR VENTA (Lógica procesarDespachoFisicoMasivo)
        if ($accion === 'despachar_venta_completa') {
            $ids = $_POST['ids_movimientos'] ?? [];
            if (empty($ids)) {
                throw new Exception("No se seleccionaron productos para el despacho masivo.");
            }
            // Esta función ejecuta la transacción para todos los IDs enviados
            echo json_encode($repartoM->procesarDespachoFisicoMasivo($ids));
        } 

        // 2. DESPACHO INDIVIDUAL (Lógica original)
        elseif ($accion === 'despachar') {
            $id_movimiento = intval($_POST['id_movimiento'] ?? 0);
            if ($id_movimiento <= 0) {
                throw new Exception("ID de movimiento no válido.");
            }
            echo json_encode($modelo->procesarDespachoFisico($id_movimiento));
        } 
        
        // 3. LOGÍSTICA Y RUTAS DE REPARTO
        elseif ($accion === 'guardar_reparto') {
            if (empty($_POST['vehiculo_id']) || empty($_POST['chofer_id']) || empty($_POST['movimiento_id'])) {
                throw new Exception("Faltan datos obligatorios para el reparto.");
            }

            $vehiculo_id = intval($_POST['vehiculo_id']);
            $rutaActiva = $repartoM->buscarRutaAbierta($vehiculo_id);

            if ($rutaActiva) {
                $_POST['folio_viaje'] = $rutaActiva['viaje_folio'];
            } else {
                $_POST['folio_viaje'] = "RUT-" . date('ymd') . "-" . str_pad($vehiculo_id, 2, "0", STR_PAD_LEFT) . "-" . rand(10, 99);
                if (method_exists($vehiculoM, 'actualizarEstado')) {
                    $vehiculoM->actualizarEstado($vehiculo_id, 'en_ruta');
                }
            }

            $repartoM->iniciarReparto($_POST);
            echo json_encode([
                'success' => true, 
                'message' => '¡Logística confirmada!',
                'folio'   => $_POST['folio_viaje']
            ]);
        }

        // 4. ENTREGA DIRECTA AL CLIENTE (PATIO)
        elseif ($accion === 'entregar_en_patio') {
            if (empty($_POST['movimiento_id']) || empty($_POST['chofer_id'])) {
                throw new Exception("Error: Debe indicar el movimiento y el personal responsable de la entrega.");
            }

            // Datos forzados para la consistencia de la base de datos
            $_POST['usuario_sistema_id'] = $id_usuario_sesion;
            $_POST['vehiculo_id'] = 999; // Vehículo virtual (Patio)

            $resultado = $repartoM->entregarEnPatioCliente($_POST);
            echo json_encode($resultado);
        }

        // CASO POR DEFECTO
        else {
            throw new Exception("Acción POST '$accion' no reconocida.");
        }

    } catch (Exception $e) {
        // Respuesta unificada para errores capturados
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}
// --- CARGA NORMAL DE LA VISTA ---
$paginaActual = 'Entregas';
require_once __DIR__ . '/../views/entregas_view.php';