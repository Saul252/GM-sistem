<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../controllers/LayoutController.php';

// Modelos necesarios
require_once __DIR__ . '/../models/RepartosModel.php';
require_once __DIR__ . '/../models/trabajadores_model.php';
require_once __DIR__ . '/../models/vehiculos_model.php';
require_once __DIR__ . '/../models/entregasModel.php';
require_once __DIR__ . '/../models/almacen_model.php';


protegerPagina('repartos'); 
$paginaActual = 'repartos';

$repartoM    = new RepartoModel($conexion);
$trabajadorM = new TrabajadorModel($conexion);
$vehiculoM   = new VehiculoModel($conexion);
$entregaM    = new EntregaModel($conexion);
$almacenModel = new AlmacenModel($conexion);

if (isset($_GET['action']) || isset($_POST['action'])) {
    if (ob_get_level()) ob_clean();
    header('Content-Type: application/json');
    $action = $_REQUEST['action'] ?? '';

    try {
        /**
         * -----------------------------------------------------------
         * BLOQUE 1: ACCIONES DEL MODAL DE DESPACHO (REUTILIZABLE)
         * -----------------------------------------------------------
         */

        // 1.1 Obtener datos del movimiento/producto
        if ($action === 'get_recursos_reparto') {
            $movimiento_id = $_REQUEST['id'] ?? 0;
            $detalle = $entregaM->getDetalleParaDespacho($movimiento_id);
            
            if (!$detalle) {
                echo json_encode(["success" => false, "message" => "Movimiento no encontrado"]);
                exit;
            }
            echo json_encode(["success" => true, "data" => ["entrega" => $detalle]]);
            exit; 
        }

        // 1.2 Obtener Unidades y Choferes por Almacén (Carga en tiempo real)
        if ($action === 'get_recursos_sucursal') {
            $almacen_id = intval($_GET['almacen_id'] ?? 0);
            echo json_encode([
                "success"  => true,
                "unidades" => $vehiculoM->listarPorAlmacen($almacen_id),
                "choferes" => $trabajadorM->listarPorAlmacen($almacen_id)
            ]);
            exit;
        }

        // 1.3 Guardar el despacho/reparto
        if ($action === 'guardar_reparto') {
            if (empty($_POST['vehiculo_id']) || empty($_POST['chofer_id']) || empty($_POST['movimiento_id'])) {
                throw new Exception("Faltan datos obligatorios.");
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

            $reparto_id = $repartoM->iniciarReparto($_POST);
            echo json_encode([
                'success' => true, 
                'message' => '¡Logística confirmada!',
                'folio'   => $_POST['folio_viaje']
            ]);
            exit;
        }

        /**
         * -----------------------------------------------------------
         * BLOQUE 2: GESTIÓN DE VISTA Y MONITOREO (REPARTOS)
         * -----------------------------------------------------------
         */

        if ($action === 'listar_pendientes_ruta') {
            $lista = $entregaM->listarSoloDespachadosPatio();
            echo json_encode(['success' => true, 'data' => $lista]);
        }

        if ($action === 'listar_viajes_activos') {
            $viajes = $repartoM->listarViajesActivos();
            echo json_encode(['success' => true, 'data' => $viajes]);
        }

        if ($action === 'finalizar_viaje') {
            $v_id = intval($_POST['vehiculo_id']);
            $viaje_folio = $_POST['viaje_folio'];
            $repartoM->finalizarViajeLogistica($v_id, $viaje_folio);
            $vehiculoM->actualizarEstado($v_id, 'disponible');
            echo json_encode(['success' => true, 'message' => '¡Viaje finalizado!']);
            exit;
        }

    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}

// --- CARGA INICIAL DE LA VISTA PRINCIPAL ---
$almacen_init = intval($_SESSION['almacen_id'] ?? 0);
$unidadesLibres = $vehiculoM->listarPorAlmacen($almacen_init);
$totalUnidadesLibres = count($unidadesLibres);
$tituloPagina = "Gestión de Repartos y Logística";

require_once __DIR__ . '/../views/repartos_view.php';