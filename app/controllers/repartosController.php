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
         * BLOQUE 1: ACCIONES DEL MODAL DE DESPACHO
         * -----------------------------------------------------------
         */

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

        if ($action === 'get_recursos_sucursal') {
            $almacen_id = intval($_GET['almacen_id'] ?? 0);
            echo json_encode([
                "success"  => true,
                "unidades" => $vehiculoM->listarPorAlmacen($almacen_id),
                "choferes" => $trabajadorM->listarPorAlmacen($almacen_id)
            ]);
            exit;
        }

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
         * BLOQUE 2: GESTIÓN DE VISTA Y FILTROS
         * -----------------------------------------------------------
         */

        if ($action === 'listar_pendientes_ruta') {
            // Prioridad: 1. El filtro del GET, 2. El almacén de la sesión
            $almacen_id = isset($_GET['almacen_id']) ? intval($_GET['almacen_id']) : intval($_SESSION['almacen_id']);
            
            // Pasamos el ID al modelo (asegúrate que tu modelo acepte este parámetro ahora)
            $lista = $entregaM->listarSoloDespachadosPatio($almacen_id);
            
            echo json_encode(['success' => true, 'data' => $lista]);
            exit;
        }

  if ($action === 'listar_viajes_activos') {
    // 1. Limpiamos cualquier salida previa para evitar JSON corrupto
    if (ob_get_length()) ob_clean();
    
    // 2. Definimos el ID por defecto
    $almacen_id = 0;

    // 3. PRIORIDAD TOTAL: Si el JS envía algo, eso es lo que manda (incluso si es 0)
    if (isset($_GET['almacen_id'])) {
        $almacen_id = intval($_GET['almacen_id']);
    } 
    // 4. RESPALDO: Si no viene en el GET, usamos la sesión del usuario
    else if (isset($_SESSION['almacen_id'])) {
        $almacen_id = intval($_SESSION['almacen_id']);
    }

    // 5. LLAMADA AL MODELO
    // Asegúrate de que tu modelo soporte recibir el 0 para "ver todos"
    $viajes = $repartoM->listarViajesActivos($almacen_id);

    // 6. RESPUESTA LIMPIA
    header('Content-Type: application/json; charset=utf-8');
    // Evitar que el navegador guarde en caché una respuesta vacía
    header('Cache-Control: no-cache, must-revalidate'); 
    
    echo json_encode([
        'success' => true, 
        'data'    => $viajes,
        'count'   => count($viajes),
        'debug'   => [
            'almacen_solicitado' => $almacen_id,
            'metodo' => isset($_GET['almacen_id']) ? 'GET' : 'SESSION'
        ]
    ]);
    exit;
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

// --- CARGA INICIAL PARA EL RENDERING ---
$almacen_sesion = intval($_SESSION['almacen_id'] ?? 0);
$unidadesLibres = $vehiculoM->listarPorAlmacen($almacen_sesion);
$totalUnidadesLibres = count($unidadesLibres);

// Obtener lista de almacenes para el select del Administrador
$listaAlmacenes = $almacenModel->getAlmacenes($almacen_sesion); 

$tituloPagina = "Gestión de Repartos y Logística";

require_once __DIR__ . '/../views/repartos_view.php';