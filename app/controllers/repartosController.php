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
         * NUEVA ACCIÓN: Obtener el resumen de quién y cómo entregó la mercancía
         * Se activa desde el botón del "Ojito" en la tabla.
         */
       if ($action === 'get_resumen_despacho') {
    $movimiento_id = intval($_REQUEST['id'] ?? 0);
    
    // 1. Obtenemos el resumen completo (que ya trae cliente y tripulantes básicos)
    $resumen = $repartoM->obtenerHistorialFisico($movimiento_id);
    
    if ($resumen) {
        // 2. IMPORTANTE: Solo sobreescribe tripulantes si el array viene vacío 
        // y realmente es un reparto de logística.
        if (empty($resumen['tripulantes']) && !empty($resumen['reparto_id'])) {
            $resumen['tripulantes'] = $repartoM->getTripulantesPorReparto($resumen['reparto_id']);
        }

        // 3. Forzamos el header para que el JS reciba JSON puro
        header('Content-Type: application/json');
        echo json_encode(["success" => true, "data" => $resumen]);
    } else {
        header('Content-Type: application/json');
        echo json_encode(["success" => false, "message" => "No se encontraron registros de salida física."]);
    }
    exit;
}
        /**
         * -----------------------------------------------------------
         * BLOQUE 2: GESTIÓN DE VISTA Y FILTROS
         * -----------------------------------------------------------
         */
        if ($action === 'cancelar_viaje_completo') {
            // Recibimos los datos del monitor (GET o POST)
            $folio_viaje = $_REQUEST['folio'] ?? '';
            $vehiculo_id = intval($_REQUEST['vehiculo_id'] ?? 0);

            if (empty($folio_viaje) || $vehiculo_id === 0) {
                throw new Exception("Datos de viaje insuficientes para cancelar.");
            }

            // 1. Ejecutamos la limpieza masiva en el modelo
            $repartoM->cancelarViajeCompleto($folio_viaje, $vehiculo_id);

            // 2. IMPORTANTE: Regresamos el vehículo a estado disponible
            if (method_exists($vehiculoM, 'actualizarEstado')) {
                $vehiculoM->actualizarEstado($vehiculo_id, 'disponible');
            }

            echo json_encode([
                'success' => true, 
                'message' => 'Ruta anulada y unidades liberadas correctamente.'
            ]);
            exit;
        }
if ($action === 'get_detalles_viaje') {
    $folio = $_GET['folio'] ?? '';
    $detalles = $repartoM->getDetallesViaje($folio);
    
    if ($detalles) {
        echo json_encode(["success" => true, "data" => $detalles]);
    } else {
        echo json_encode(["success" => false, "message" => "No se encontró información"]);
    }
    exit;
}

if ($action === 'actualizar_logistica_completa') {
    // Esta es la función que procesa los cambios del modal (Chofer, Ayudantes y Destinos)
    $res = $repartoM->actualizarLogisticaCompleta($_POST);
    echo json_encode(["success" => true, "message" => "Ruta actualizada correctamente"]);
    exit;
}
// --- HISTORIAL DE REPARTOS FINALIZADOS ---
if ($action === 'listar_historial') {
    // Limpiamos cualquier salida previa (Warnings) para que el JSON sea puro
    ob_clean(); 
    header('Content-Type: application/json');

    try {
        $almacen_id = isset($_GET['almacen_id']) ? intval($_GET['almacen_id']) : 0;
        $historial = $repartoM->listarHistorialDeRepartos($almacen_id);

        // Si el modelo regresa false o null, lo convertimos en array vacío
        $data = $historial ? $historial : [];

        echo json_encode([
            "success" => true, 
            "data" => $data
        ]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            "success" => false, 
            "message" => $e->getMessage()
        ]);
    }
    exit;
}

        // if ($action === 'cancelar_entrega_individual') {
        //     $movimiento_id = intval($_REQUEST['movimiento_id'] ?? 0);
            
        //     if ($movimiento_id === 0) {
        //         throw new Exception("ID de movimiento no válido.");
        //     }

        //     // Esta llama a la función que limpia un solo movimiento_id
        //     $repartoM->cancelarEntregaIndividual();

        //     echo json_encode([
        //         'success' => true, 
        //         'message' => 'La entrega ha sido removida del reparto.'
        //     ]);
        //     exit;
        // }

       if ($action === 'listar_pendientes_ruta') {
    // Tomamos el almacen_id del GET (filtro) o de la sesión (por defecto)
    $almacen_id = isset($_GET['almacen_id']) ? intval($_GET['almacen_id']) : intval($_SESSION['almacen_id'] ?? 0);
    
    // El modelo ya devuelve el array con 'cantidad_display' incluido
    $lista = $entregaM->listarSoloDespachadosPatio($almacen_id);
    
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true, 
        'data' => $lista
    ]);
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