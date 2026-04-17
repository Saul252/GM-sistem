<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../controllers/LayoutController.php';
require_once __DIR__ . '/../models/prestamosModel.php';
require_once __DIR__ . '/../models/trabajadores_model.php';
require_once __DIR__ . '/../models/almacen_model.php';
require_once __DIR__ . '/../models/tesoreriaModel.php';
require_once __DIR__ . '/../models/corteCajaModel.php';
require_once __DIR__ . '/../models/egresos_model.php';
require_once __DIR__ . '/../models/egresos/gastosModel.php';
protegerPagina('prestamos');

$usuario_id = $_SESSION['usuario_id'] ?? 0;

/* =========================
   MODELOS
========================= */
$prestamosModel    = new PrestamosModel($conexion);
$trabajadoresModel = new TrabajadorModel($conexion);
$almacenModel      = new AlmacenModel($conexion);
$tesoreria         = new tesoreriaModel($conexion);
$corteCaja         = new CorteCajaModel($conexion);
$egreso            = new EgresoModel($conexion);
$gastosModel       = new GastoModel($conexion);

/* =========================
   SESIÓN
========================= */
$almacen_usuario = $_SESSION['almacen_id'] ?? 0;

/* =========================
   INPUT FILTROS
========================= */
$periodo  = $_GET['periodo'] ?? 'hoy';
$f_inicio = $_GET['f_inicio'] ?? date('Y-m-d');
$f_fin    = $_GET['f_fin'] ?? date('Y-m-d');
$almacen_id_req = isset($_GET['almacen_id']) ? intval($_GET['almacen_id']) : 0;

/* =========================
   FECHAS AUTOMÁTICAS
========================= */
if ($periodo === 'hoy') {
    $f_inicio = $f_fin = date('Y-m-d');
} elseif ($periodo === 'ayer') {
    $f_inicio = $f_fin = date('Y-m-d', strtotime("-1 day"));
}

/* =========================
   ALMACÉN ACTIVO
========================= */
$target = ($almacen_usuario != 0)
    ? $almacen_usuario
    : ($almacen_id_req ?: 0);

/* =========================
   AJAX
========================= */
if (isset($_GET['action']) && $_GET['action'] === 'ajax') {
    header('Content-Type: application/json');

    try {

        $prestamos     = $prestamosModel->listarPrestamos($target);
        $trabajadores  = $trabajadoresModel->listarTrabajadores($target);
        $cajasFuertes  = $tesoreria->getCajasFuertes($target);
        $saldo         = $corteCaja->obtenerSaldoInicialMonitor($target, $f_inicio, $f_fin);

        if (!isset($saldo[0])) {
            $saldo = [[
                'idAlmacen' => $target,
                'almacen'   => 'Sucursal',
                'monto'     => $saldo['monto'] ?? 0
            ]];
        }

        echo json_encode([
            'status' => 'success',
            'almacen_activo' => $target,
            'prestamos' => $prestamos,
            'trabajadores' => $trabajadores,
            'cajasFuertes' => $cajasFuertes,
            'saldo' => $saldo
        ]);

    } catch (Exception $e) {
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    }
    exit;
}

/* =========================
   CREAR PRÉSTAMO + GASTO
========================= */
if (isset($_GET['action']) && $_GET['action'] === 'crear') {
    header('Content-Type: application/json');

    try {
 $almacen = intval($_POST['almacen_id'] ?? 0);
        $trabajador_id = intval($_POST['trabajador_id'] ?? 0);
        $monto         = floatval($_POST['monto_total'] ?? 0);
      $metodo_pago     =   ($_POST['metodo_pago'] ?? 'Efectivo');
$descripcion   = trim($_POST['descripcion'] ?? '');

        if ($trabajador_id <= 0 || $monto <= 0) {
            throw new Exception("Datos inválidos");
        }

        /* ===== 1. PRÉSTAMO ===== */
        $data = [
            'trabajador_id' => $trabajador_id,
            'almacen_id'    => $almacen,
            'monto_total'   => $monto,
            'estado'        =>'activo',
            'descripcion'   => $descripcion
        ];

        $ok = $prestamosModel->crearPrestamo($data);

        if (!$ok) {
            throw new Exception("Error al registrar préstamo");
        }


        /* ===== 2. OBTENER NOMBRE TRABAJADOR (DIRECTO BD SEGURO) ===== */
      $nombreTrabajador = $trabajadoresModel->nombreTrabajador($trabajador_id);

  $siguiente = $gastosModel->generarSiguienteFolioGasto();
        /* ===== 3. CREAR GASTO ===== */
        $folio = 'PREST-' . time();

        $concepto = "Préstamo a {$nombreTrabajador} por motivo de {$descripcion}";

        $cabecera = [
            'folio' => $siguiente,
            'fecha' => date('Y-m-d'),
            'almacen_id' => $almacen,
            'categoria_id' => 8,
            'usuario_id' => $usuario_id,
            'beneficiario' => $nombreTrabajador,
            'metodo_pago'  => $_POST['metodo_pago'] ?? 'Efectivo', // ⚠️ en minúscula
            'total' => $monto,
            'documento_url' => '',
            'observaciones' => $concepto
        ];
       

        $descripciones = [$concepto];
        $cantidades    = [1];
        $precios       = [$monto];

        $resultGasto = $egreso->registrarGasto(
            $cabecera,
            $descripciones,
            $cantidades,
            $precios
        );

        if (!$resultGasto || empty($resultGasto['success'])) {
            throw new Exception("Error al registrar gasto");
        }

        echo json_encode([
            'success' => true,
            'message' => 'Préstamo y gasto registrados correctamente'
        ]);

    } catch (Throwable $e) {

        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
    exit;
}

/* =========================
   ABONAR
========================= */
if (isset($_GET['action']) && $_GET['action'] === 'abonar') {
    // 1. Limpiar cualquier eco o basura previa
    while (ob_get_level()) ob_end_clean();
    ob_start();
    
    header('Content-Type: application/json');

    try {
        // Log de entrada (ver en el error_log del servidor)
        error_log("--- Iniciando proceso de abono ---");
        error_log("POST recibidos: " . print_r($_POST, true));

        // Validación de modelos
        if (!isset($prestamosModel)) throw new Exception("Modelo 'prestamosModel' no instanciado.");
        if (!isset($corteCaja)) throw new Exception("Modelo 'corteCaja' no instanciado.");

        $id_almacen = intval($_POST['almacen'] ?? $_POST['almacen_id'] ?? 0);
        $monto = floatval($_POST['monto_abono'] ?? 0);
        $id_prestamo = intval($_POST['prestamo_id'] ?? 0);

        if($id_almacen <= 0) throw new Exception("ID de almacén inválido o no recibido.");

        $data = [
            'almacen_id'    => $id_almacen,
            'prestamo_id'   => $id_prestamo,
            'monto_abono'   => $monto,
            'metodo_pago'   => $_POST['metodo_pago'] ?? 'efectivo',
            'usuario_id'    => $usuario_id ?? 0,
            'observaciones' => trim($_POST['observaciones'] ?? '')
        ];

        // Paso 1: Registrar el abono
        $ok = $prestamosModel->registrarAbono($data);
        if (!$ok) throw new Exception("Fallo en registrarAbono() del modelo.");

        // Paso 2: Cerrar si aplica
        $prestamosModel->cerrarPrestamoSiPagado($id_prestamo);

        // Paso 3: Inyectar a caja
        $data2 = [
            'almacen_id'         => $id_almacen,
            'usuario_id'         => $usuario_id ?? 0,
            'categoria_id'       => 13,
            'monto'              => $monto,
            'metodo_pago'        => $data['metodo_pago'],
            'fecha_movimiento'   => date('Y-m-d'), 
            'concepto'           => 'Abono a préstamo ID: ' . $id_prestamo,
            'tipo_operacion'     => 'Entrada',
            'monto_efectivo'     => ($data['metodo_pago'] == 'efectivo') ? $monto : 0,
            'monto_tarjeta'      => 0,
            'monto_transferencia'=> 0,
            'almacen_destino_id' => $id_almacen,
            'caja_fuerte_id'     => null, 
            'banco_id'           => null
        ];

        $res_corte = $corteCaja->registrarAperturaDesdeCierreConceptoAbono($data2);
        
        // Limpiar el buffer por si hubo warnings
        ob_end_clean();

        echo json_encode([
            'success' => true,
            'message' => 'Abono registrado con éxito',
            'debug_corte' => $res_corte
        ]);

    } catch (Throwable $e) {
        // Si algo falló, limpiamos el buffer y mandamos el error real
        if (ob_get_level()) ob_end_clean();
        
        error_log("ERROR EN ABONAR: " . $e->getMessage());
        
        echo json_encode([
            'success' => false, 
            'message' => 'Error: ' . $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString() // Esto te dice el camino del error
        ]);
    }
    exit;
}
/* =========================
   LISTAR
========================= */
if (isset($_GET['action']) && $_GET['action'] === 'listar') {
    header('Content-Type: application/json');

    try {

        $data = $prestamosModel->listarPrestamos($target);

        echo json_encode([
            'success' => true,
            'data' => $data,
            'almacen_activo' => $target
        ]);

    } catch (Throwable $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}

/* =========================
   DETALLE
========================= */
if (isset($_GET['action']) && $_GET['action'] === 'detalle') {
    header('Content-Type: application/json');

    try {

        $id = intval($_GET['id'] ?? 0);

        $prestamo = $prestamosModel->obtenerPrestamo($id);
        $abonos   = $prestamosModel->listarAbonos($id);

        echo json_encode([
            'success' => true,
            'prestamo' => $prestamo,
            'abonos' => $abonos
        ]);

    } catch (Throwable $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}

/* =========================
   VISTA
========================= */
if ($_SERVER['REQUEST_METHOD'] === 'GET' && !isset($_GET['action'])) {

    $prestamos = $prestamosModel->listarPrestamos($target);
    $trabajadores = $trabajadoresModel->listarTrabajadores($target);
    $cajasFuertes = $tesoreria->getCajasFuertes($target);
    $saldo = $corteCaja->obtenerSaldoInicialMonitor($target, $f_inicio, $f_fin);

    if (!isset($saldo[0])) {
        $saldo = [[
            'idAlmacen' => $target,
            'almacen'   => 'Sucursal',
            'monto'     => $saldo['monto'] ?? 0
        ]];
    }

    $almacenes = $almacenModel->getAlmacenes($almacen_usuario);
    $paginaActual = 'prestamos';

    require_once __DIR__ . '/../views/prestamos_view.php';
}