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
    // 1. Validaciones iniciales
    if (empty($_POST['vehiculo_id']) || empty($_POST['chofer_id']) || empty($_POST['movimiento_id'])) {
        throw new Exception("Faltan datos obligatorios: Unidad, Chofer o ID de Movimiento.");
    }

    $vehiculo_id = intval($_POST['vehiculo_id']);
    
    // 2. Lógica de Consolidación: ¿El camión ya tiene una ruta abierta?
    // Usamos la función auxiliar que ya tienes en tu modelo
    $rutaActiva = $repartoM->buscarRutaAbierta($vehiculo_id);

    if ($rutaActiva) {
        // CASO A: El camión ya está en ruta, reutilizamos el folio existente
        $_POST['folio_viaje'] = $rutaActiva['viaje_folio'];
    } else {
        // CASO B: Es la primera carga del camión para esta ruta
        // Generamos un folio nuevo único
        $_POST['folio_viaje'] = "RUT-" . date('ymd') . "-" . str_pad($vehiculo_id, 2, "0", STR_PAD_LEFT) . "-" . rand(10, 99);
        
        // MVC: Actualizamos el estado del vehículo a 'en_ruta'
        // Esto solo ocurre cuando se inicia la ruta por primera vez
        if (method_exists($vehiculoM, 'actualizarEstado')) {
            $vehiculoM->actualizarEstado($vehiculo_id, 'en_ruta');
        }
    }

    // 3. Llamamos a tu función original iniciarReparto
    // Ahora $_POST ya lleva el 'folio_viaje' que requiere la tabla de consolidación
    $reparto_id = $repartoM->iniciarReparto($_POST);

    // 4. Respuesta al cliente
    echo json_encode([
        'success' => true, 
        'message' => '¡Logística confirmada!',
        'folio'   => $_POST['folio_viaje'], // Informamos qué folio se asignó
        'reparto_id' => $reparto_id
    ]);
    exit;
}
        if ($action === 'listar_viajes_activos') {
    $viajes = $repartoM->listarViajesActivos();
    echo json_encode(['success' => true, 'data' => $viajes]);
}

if ($action === 'finalizar_viaje') {
    try {
        $v_id = intval($_POST['vehiculo_id']);
        $viaje_folio = $_POST['viaje_folio'];

        if (empty($v_id) || empty($viaje_folio)) {
            throw new Exception("Faltan datos obligatorios para finalizar.");
        }

        // 1. Cerramos la logística (Repartos y Folio)
        $repartoM->finalizarViajeLogistica($v_id, $viaje_folio);

        // 2. Liberamos el vehículo (Cambiamos el ENUM a 'disponible')
        // Usamos la función que ya tenías o la genérica de actualizar estado
        $vehiculoM->actualizarEstado($v_id, 'disponible');

        echo json_encode([
            'success' => true, 
            'message' => '¡Viaje finalizado! El vehículo está disponible y los pedidos se marcaron como completados.'
        ]);

    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
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