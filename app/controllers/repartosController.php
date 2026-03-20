<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// ... resto de tus requires
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../controllers/LayoutController.php';

// Modelos necesarios
require_once __DIR__ . '/../models/RepartosModel.php';
require_once __DIR__ . '/../models/trabajadores_model.php';
require_once __DIR__ . '/../models/vehiculos_model.php';
require_once __DIR__ . '/../models/entregasModel.php';

protegerPagina('repartos'); 
$paginaActual = 'repartos';

// Inicializamos los modelos con la conexión $conexion
$repartoM    = new RepartoModel($conexion);
$trabajadorM = new TrabajadorModel($conexion);
$vehiculoM   = new VehiculoModel($conexion);
$entregaM    = new EntregaModel($conexion);

// --- PROCESAMIENTO DE ACCIONES AJAX ---
if (isset($_GET['action']) || isset($_POST['action'])) {
    if (ob_get_level()) ob_clean();
    header('Content-Type: application/json');
    
    $action = $_REQUEST['action'] ?? '';

    try {
        if ($action === 'get_recursos_ruta') {
            echo json_encode([
                'success'  => true,
                'unidades' => $vehiculoM->listarDisponiblesRuta(),
                'personal' => $trabajadorM->listarPersonalLogistica()
            ]);
        }
if ($action === 'get_recursos_reparto') {
            $movimiento_id = $_REQUEST['id'] ?? 0;
            
            $detalle = $entregaM->getDetalleParaDespacho($movimiento_id);
            
            if (!$detalle) {
                echo json_encode(["success" => false, "message" => "No se encontró el movimiento"]);
                exit;
            }

            echo json_encode([
                "success" => true,
                "data" => [
                    "entrega"  => $detalle,
                    "unidades" => $vehiculoM->listarDisponiblesRuta(),
                    "choferes" => $trabajadorM->listarPersonalLogistica()
                ]
            ]);
            exit; // Importante para que no siga ejecutando
        }
        // 2. Listar pendientes corregido
        if ($action === 'listar_pendientes_ruta') {
            // Llamamos a la función que ya trae el SQL filtrado
            $lista = $entregaM->listarSoloDespachadosPatio();
            
            // Enviamos la lista directa, ya que el SQL se encarga de excluir lo entregado
            echo json_encode(['success' => true, 'data' => $lista]);
        }
        
if ($action === 'guardar_reparto') {
            // Validamos que los datos mínimos existan antes de llamar al modelo
            if (empty($_POST['vehiculo_id']) || empty($_POST['chofer_id']) || empty($_POST['movimiento_id'])) {
                throw new Exception("Faltan datos obligatorios: Unidad, Chofer o ID de Movimiento.");
            }

            // Llamamos al método del modelo y capturamos el ID del nuevo reparto
            // El modelo se encarga de la transacción y los inserts
            $reparto_id = $repartoM->iniciarReparto($_POST);

            // Si llegamos aquí, todo salió bien
            echo json_encode([
                'success' => true, 
                'message' => '¡Logística confirmada! Se generó el Folio de Salida #' . $reparto_id
            ]);
            exit;
        }
        if ($action === 'listar_viajes_activos') {
    $viajes = $repartoM->listarViajesActivos();
    echo json_encode(['success' => true, 'data' => $viajes]);
}

if ($action === 'finalizar_viaje') {
    $v_id = intval($_POST['vehiculo_id']);
    $repartoM->finalizarViajeVehiculo($v_id);
    echo json_encode(['success' => true, 'message' => 'Viaje finalizado y pedidos marcados como entregados.']);
}

    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}
// repartosController.php


// --- CARGA INICIAL DE LA VISTA ---
$unidadesLibres = $vehiculoM->listarDisponiblesRuta();
$totalUnidadesLibres = count($unidadesLibres);
$tituloPagina = "Gestión de Repartos y Logística";

require_once __DIR__ . '/../views/repartos_view.php';


