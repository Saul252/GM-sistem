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
         * BLOQUE: DETALLE EXTENDIDO PARA MONITOR (MODAL FLECHITA)
         * -----------------------------------------------------------
         */
   // --- ACCIÓN: OBTENER DETALLE DE TRAZABILIDAD (MONITOR) ---
if ($action === 'get_detalle_trazabilidad') {
    if (ob_get_level()) ob_clean();
    header('Content-Type: application/json');

    $tipo = $_GET['tipo'] ?? 'MOSTRADOR';
    $id   = intval($_GET['id']);

    if ($id <= 0) {
        echo json_encode(["success" => false, "message" => "ID de seguimiento no válido."]);
        exit;
    }

    try {
        $data = null;

        if ($tipo === 'RUTA') {
            // getDetalleRutaMonitor recibe reparto_id
            // devuelve: viaje_folio, vehiculo, placas, chofer, usuario_asigno_sistema,
            //           lista_productos[] (producto, cantidad, cliente_destino, ticket),
            //           tripulantes[] (nombre)
            $data = $repartoM->getDetalleRutaCompleta($id);

        } else {
            // getDetalleMovimientoNormal recibe movimiento_id
            // devuelve: movimiento_id, cantidad, fecha_salida, producto, folio_venta,
            //           cliente, usuario_asigno_sistema, usuario_patio, fecha_patio
            $data = $repartoM->getDetalleMovimientoNormal($id);
        }

        if ($data) {
            echo json_encode([
                "success"        => true,
                "tipo_procesado" => $tipo,
                "data"           => $data
            ]);
        } else {
            echo json_encode([
                "success" => false,
                "message" => "No se encontró información para este registro."
            ]);
        }

    } catch (Exception $e) {
        echo json_encode([
            "success" => false,
            "message" => "Error interno: " . $e->getMessage()
        ]);
    }
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
         * BLOQUE: MONITOR DE ENTREGAS COMPLETADAS
         * -----------------------------------------------------------
         */

       if ($action === 'get_monitor_entregas') {
    header('Content-Type: application/json'); // Importante declarar el tipo de contenido al inicio

    // 1. Obtenemos el almacén (del filtro o de la sesión)
    $almacen_id = isset($_GET['almacen_id']) ? intval($_GET['almacen_id']) : intval($_SESSION['almacen_id'] ?? 0);
    
    // 2. Parámetros de paginación (opcionales, por defecto 0 y 25)
    $inicio = isset($_GET['inicio']) ? intval($_GET['inicio']) : 0;
    $limite = isset($_GET['limite']) ? intval($_GET['limite']) : 25;
    
    // 3. Llamamos a la función pasando los 3 parámetros requeridos
    $registros = $repartoM->getMonitorEntregas($almacen_id, $inicio, $limite);
    
    // 4. Verificamos si es un array (aunque esté vacío, el modelo debería devolver [])
    if (is_array($registros)) {
        echo json_encode([
            "success" => true, 
            "data" => $registros,
            "count" => count($registros),
            "offset" => $inicio
        ]);
    } else {
        echo json_encode([
            "success" => false, 
            "message" => "Error al obtener la trazabilidad de entregas."
        ]);
    }
    exit;
}
        /**
         * NOTA: Para el modal del "Ojito" en el monitor, 
         * usaremos la acción 'get_resumen_despacho' que ya tienes arriba, 
         * solo asegúrate de que tu función 'obtenerHistorialFisico' en el modelo 
         * reciba el 'movimiento_id' y retorne los datos de la ruta si el movimiento 
         * está asociado a una.
         */
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
// --- 
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