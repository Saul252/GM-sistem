<?php
/**
 * Cf System - Controlador de Tesorería
 * Maneja el flujo de capital, bancos y caja fuerte.
 */

require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../controllers/LayoutController.php';
require_once __DIR__ . '/../models/almacen_model.php';
require_once __DIR__ . '/../models/tesoreriaModel.php';
require_once __DIR__ . '/../models/corteCajaModel.php';

// Verificación de acceso
protegerPagina('tesoreria');
date_default_timezone_set('America/Mexico_City');

// Buffer de salida para evitar que cualquier warning rompa el JSON
ob_start();

/**
 * Detectar variable de conexión.
 * El modelo usa MySQLi según la función que proporcionaste.
 */
$db_conn = $conexion ?? $db;

$tesoreria = new tesoreriaModel($db_conn);
$corteCaja = new CorteCajaModel($db_conn);
$almacenModel = new AlmacenModel($db_conn);

// Identificar la acción solicitada
$action = $_POST['action'] ?? $_GET['action'] ?? '';

// --- BLOQUE 1: PROCESAMIENTO DE PETICIONES AJAX ---
if (!empty($action)) {
    header('Content-Type: application/json; charset=utf-8');

    // Limpiar buffers para asegurar salida JSON limpia
    if (ob_get_length())
        ob_clean();

    try {
        switch ($action) {

            // ============================================================
            // REGISTRAR MOVIMIENTO (entrada / salida / traspaso)
            // ============================================================
            case 'registrar':
                // ============================================================
                // 1. RECOLECCIÓN DE DATOS
                // ============================================================
                $categoria_id = intval($_POST['categoria_id'] ?? 0);
                $almacen_id = intval($_POST['almacen_id'] ?? 0);
                $usuario_id = intval($_SESSION['usuario_id'] ?? 0);
                $monto_input = floatval($_POST['monto'] ?? 0);
                $concepto = trim($_POST['conceptos'] ?? '');
                $metodo = $_POST['metodo_pago'] ?? 'efectivo';
                $fecha_sel = $_POST['fecha_movimiento'] ?? date('Y-m-d');

                // Destinos opcionales (solo se respetan según la categoría)
                $almacen_destino_id = intval($_POST['almacen_destino_id'] ?? 0);
                $caja_fuerte_id = intval($_POST['caja_fuerte_id'] ?? 0);
                $banco_id = intval($_POST['banco_id'] ?? 0);

                // Categorías que SÍ afectan caja fuerte / banco (mismos IDs que el modal)
                $cats_caja_fuerte = [5, 10];   // Movimiento a Caja Fuerte / Ingreso desde Caja Fuerte
                $cats_banco = [11, 12];        // Retiro de Banco / Traspaso entre Bancos

                // ============================================================
                // 1.1 VALIDACIONES BÁSICAS
                // ============================================================
                if ($categoria_id <= 0) {
                    echo json_encode(["status" => "error", "message" => "Categoría inválida"]);
                    break;
                }
                if ($almacen_id <= 0) {
                    echo json_encode(["status" => "error", "message" => "Almacén inválido"]);
                    break;
                }
                if ($monto_input <= 0) {
                    echo json_encode(["status" => "error", "message" => "El monto debe ser mayor a 0"]);
                    break;
                }
                if (!in_array($metodo, ['efectivo', 'tarjeta', 'transferencia'], true)) {
                    echo json_encode(["status" => "error", "message" => "Método de pago inválido"]);
                    break;
                }

                // ============================================================
                // 1.2 DESTINOS: SOLO SI LA CATEGORÍA LOS REQUIERE
                // ============================================================
                if (!in_array($categoria_id, $cats_caja_fuerte, true)) {
                    $caja_fuerte_id = 0;   // no es movimiento de caja fuerte: no se toca la caja
                } elseif ($caja_fuerte_id <= 0) {
                    echo json_encode(["status" => "error", "message" => "Debe seleccionar la caja fuerte"]);
                    break;
                }

                if (!in_array($categoria_id, $cats_banco, true)) {
                    $banco_id = 0;         // no es movimiento de banco: no se toca el banco
                } elseif ($banco_id <= 0) {
                    echo json_encode(["status" => "error", "message" => "Debe seleccionar el banco"]);
                    break;
                }

                // ============================================================
                // 2. TIPO DE OPERACIÓN (la categoría debe existir y estar activa)
                // ============================================================
                $sql_cat = "SELECT tipo_operacion FROM capital_categorias WHERE id = ? AND estatus = 1";
                $stmt_c = $db_conn->prepare($sql_cat);
                $stmt_c->bind_param("i", $categoria_id);
                $stmt_c->execute();
                $cat_info = $stmt_c->get_result()->fetch_assoc();

                if (!$cat_info) {
                    echo json_encode(["status" => "error", "message" => "La categoría no existe o está inactiva"]);
                    break;
                }
                $tipo_op = $cat_info['tipo_operacion'];

                // Solo los traspasos entre almacenes usan almacén destino
                if ($tipo_op !== 'traspaso' || in_array($categoria_id, $cats_banco, true)) {
                    $almacen_destino_id = 0;
                }

                // ============================================================
                // 3. SALDOS ACTUALES DEL ALMACÉN ORIGEN
                // ============================================================
                $saldos_actuales = $corteCaja->obtenerSaldoInicialMonitor($almacen_id, '2000-01-01', $fecha_sel);

                $saldos = [
                    'efectivo' => floatval($saldos_actuales['monto_efectivo'] ?? 0),
                    'tarjeta' => floatval($saldos_actuales['monto_tarjeta'] ?? 0),
                    'transferencia' => floatval($saldos_actuales['monto_transferencia'] ?? 0)
                ];

                // ============================================================
                // 4. VALIDACIÓN DE SALDO SUFICIENTE (salidas y traspasos)
                // ============================================================
                if ($tipo_op === 'salida' || $tipo_op === 'traspaso') {
                    $saldo_metodo = $saldos[$metodo];

                    if ($monto_input > $saldo_metodo) {
                        echo json_encode([
                            "status" => "error",
                            "message" => "Saldo insuficiente en $metodo. Disponible: $" . number_format($saldo_metodo, 2)
                        ]);
                        break;
                    }
                }

                // ============================================================
                // 5. VALIDACIÓN ESPECÍFICA DE TRASPASO ENTRE ALMACENES
                // ============================================================
                if ($tipo_op === 'traspaso' && !in_array($categoria_id, $cats_banco, true)) {
                    if ($almacen_destino_id <= 0) {
                        echo json_encode(["status" => "error", "message" => "Debe seleccionar un almacén destino para el traspaso"]);
                        break;
                    }
                    if ($almacen_destino_id === $almacen_id) {
                        echo json_encode(["status" => "error", "message" => "El almacén destino no puede ser el mismo que el origen"]);
                        break;
                    }
                }

                // ============================================================
                // 6. DESGLOSE: SOLO EL MOVIMIENTO (no el saldo acumulado)
                //    El modelo suma efectivo + tarjeta + transferencia para
                //    obtener "monto", así que aquí solo va lo que se movió.
                // ============================================================
                $desglose = ['efectivo' => 0, 'tarjeta' => 0, 'transferencia' => 0];
                $desglose[$metodo] = $monto_input;

                // ============================================================
                // 7. DATA PARA EL MODELO
                // ============================================================
                $data = [
                    'almacen_id' => $almacen_id,
                    'usuario_id' => $usuario_id,
                    'categoria_id' => $categoria_id,
                    'monto' => $monto_input,
                    'metodo_pago' => $metodo,
                    'fecha_movimiento' => $fecha_sel,
                    'concepto' => $concepto,
                    'tipo_operacion' => $tipo_op,
                    'monto_efectivo' => $desglose['efectivo'],
                    'monto_tarjeta' => $desglose['tarjeta'],
                    'monto_transferencia' => $desglose['transferencia'],
                    'almacen_destino_id' => $almacen_destino_id ?: null,
                    'caja_fuerte_id' => $caja_fuerte_id ?: null,
                    'banco_id' => $banco_id ?: null
                ];

                // ============================================================
                // 8. GUARDAR
                //    El modelo ya registra la entrada en el almacén destino
                //    (PASO B) y afecta caja/banco (PASO C y D) dentro de una
                //    sola transacción. NO se vuelve a registrar el destino aquí.
                // ============================================================
                try {
                    $res = $corteCaja->registrarAperturaDesdeCierreConcepto($data);
                } catch (Exception $e) {
                    error_log("registrar movimiento: " . $e->getMessage());
                    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
                    break;
                }

                $historial_capital_id = ($res !== false && $res !== null) ? intval($res) : null;

                echo json_encode([
                    "status" => $historial_capital_id ? "success" : "error",
                    "id" => $historial_capital_id,
                    "historial_capital_id" => $historial_capital_id,
                    "message" => $historial_capital_id
                        ? "Movimiento de $tipo_op registrado correctamente"
                        : "Error al guardar en el historial"
                ]);
                break;
            // ============================================================
            // LISTAR MOVIMIENTOS Y SALDOS DEL RANGO
            // ============================================================
            case 'listar':
                $almacen_id = isset($_GET['almacen_id']) ? intval($_GET['almacen_id']) : ($_SESSION['almacen_id'] ?? 0);

                // Normalización de fechas para el rango completo del día
                $f_inicio = $_GET['f_inicio'] ?? $_GET['fecha'] ?? date('Y-m-d');
                $f_fin = $_GET['f_fin'] ?? $_GET['fecha'] ?? date('Y-m-d');

                // 1. Obtener saldos acumulados (foto) y detalle del historial
                $movimientos = $corteCaja->obtenerSaldoInicialMonitor($almacen_id, $f_inicio, $f_fin);
                $movimientosHistorial = $corteCaja->obtenerSaldoInicialMonitorTabla($almacen_id, $f_inicio, $f_fin);

                // 2. Saldo inicial del rango (apertura del día o acumulado previo)
                //    Si el modelo no lo devuelve, lo dejamos en 0 para no romper el JSON.
                $saldo_inicial = floatval($movimientos['saldo_inicial'] ?? 0);

                echo json_encode([
                    'status' => 'success',
                    'data' => $movimientos,
                    'movimientosHistorial' => $movimientosHistorial,
                    'saldo_inicial' => $saldo_inicial,
                    'es_lista' => ($almacen_id == 0)
                ]);
                break;
            // ============================================================
            // OBTENER MOVIMIENTO DE HISTORIAL_CAPITAL POR ID
            // ============================================================
            case 'obtener_movimiento_capital':
                $movimiento_id = intval($_GET['id'] ?? $_POST['id'] ?? $_GET['movimiento_id'] ?? $_POST['movimiento_id'] ?? 0);

                if ($movimiento_id <= 0) {
                    echo json_encode([
                        "status" => "error",
                        "message" => "ID de movimiento inválido"
                    ]);
                    break;
                }

                $movimiento = $tesoreria->obtenerMovimientoCapital($movimiento_id);

                if (!$movimiento) {
                    echo json_encode([
                        "status" => "error",
                        "message" => "Movimiento no encontrado"
                    ]);
                    break;
                }

                echo json_encode([
                    "status" => "success",
                    "data" => $movimiento
                ]);
                break;

            // ============================================================
            // OBTENER SALDOS DE UNA SUCURSAL A UNA FECHA DADA
            // ============================================================
            case 'obtener_saldos_sucursal':
                $almacen_id = isset($_GET['almacen_id']) ? intval($_GET['almacen_id']) : 0;
                $fecha_sel = $_GET['fecha'] ?? date('Y-m-d');

                // Fecha de inicio muy antigua para traer el acumulado histórico
                $f_inicio = $_GET['f_inicio'] ?? '2000-01-01';

                $saldos = $corteCaja->obtenerSaldoInicialMonitor($almacen_id, $f_inicio, $fecha_sel);

                echo json_encode([
                    'status' => 'success',
                    'saldos' => $saldos
                ]);
                break;

            // ============================================================
            // CATÁLOGOS PARA EL MODAL (categorías, cajas fuertes, bancos)
            // ============================================================
            case 'catalogos_modal':
                // Obtenemos el ID del almacén seleccionado en el modal (0 si es global/admin)
                $almacen_id = intval($_GET['almacen_id'] ?? 0);

                echo json_encode([
                    "status" => "success",
                    "categorias" => $tesoreria->getCategorias(),
                    "cajas_fuertes" => $tesoreria->getCajasFuertes($almacen_id),
                    "bancos" => $tesoreria->getCuentasBancarias($almacen_id)
                ]);
                break;

            // ============================================================
            // CANCELAR MOVIMIENTO
            // ============================================================
            case 'cancelar':
                $id = intval($_POST['id'] ?? 0);
                $user_id = intval($_SESSION['usuario_id'] ?? 0);

                if ($id <= 0) {
                    echo json_encode(["status" => "error", "message" => "ID inválido"]);
                    break;
                }

                $res = $tesoreria->cancelarMovimiento($id, $user_id);
                echo json_encode([
                    "status" => $res ? "success" : "error",
                    "message" => $res ? "Movimiento cancelado" : "No se pudo cancelar el movimiento"
                ]);
                break;
            // ============================================================
            // CRUD CAJA FUERTE (SOLO EFECTIVO)
            // ============================================================
            case 'crear_movimiento_caja_fuerte':
                $data = [
                    'caja_fuerte_id' => intval($_POST['caja_fuerte_id'] ?? 0),
                    'almacen_id' => intval($_POST['almacen_id'] ?? ($_SESSION['almacen_id'] ?? 0)),
                    'usuario_id' => intval($_SESSION['usuario_id'] ?? 0),
                    'tipo_movimiento' => $_POST['tipo_movimiento'] ?? '',
                    'monto' => floatval($_POST['monto'] ?? 0),
                    'concepto' => trim($_POST['concepto'] ?? ''),
                    'referencia_id' => !empty($_POST['referencia_id']) ? intval($_POST['referencia_id']) : null,
                    'fecha_movimiento' => $_POST['fecha_movimiento'] ?? date('Y-m-d H:i:s'),
                    'signo' => isset($_POST['signo']) ? intval($_POST['signo']) : 1,
                ];

                $res = $tesoreria->crearMovimientoCajaFuerte($data);
                echo json_encode([
                    "status" => $res ? "success" : "error",
                    "data" => $res,
                    "message" => $res ? "Movimiento de efectivo registrado" : "No se pudo registrar el movimiento"
                ]);
                break;

            case 'obtener_movimiento_caja_fuerte':
                $id = intval($_GET['id'] ?? $_POST['id'] ?? 0);
                if ($id <= 0) {
                    echo json_encode(["status" => "error", "message" => "ID inválido"]);
                    break;
                }

                $mov = $tesoreria->obtenerMovimientoCajaFuerte($id);
                echo json_encode([
                    "status" => $mov ? "success" : "error",
                    "data" => $mov,
                    "message" => $mov ? "OK" : "Movimiento no encontrado"
                ]);
                break;

            case 'listar_movimientos_caja_fuerte':
                $filtros = [
                    'caja_fuerte_id' => intval($_GET['caja_fuerte_id'] ?? 0),
                    'almacen_id' => intval($_GET['almacen_id'] ?? ($_SESSION['almacen_id'] ?? 0)),
                    'usuario_id' => intval($_GET['usuario_id'] ?? 0),
                    'tipo_movimiento' => $_GET['tipo_movimiento'] ?? '',
                    'fecha_desde' => !empty($_GET['f_inicio']) ? $_GET['f_inicio'] . ' 00:00:00' : '',
                    'fecha_hasta' => !empty($_GET['f_fin']) ? $_GET['f_fin'] . ' 23:59:59' : '',
                ];

                $lista = $tesoreria->listarMovimientosCajaFuerte($filtros);
                echo json_encode([
                    "status" => "success",
                    "data" => $lista
                ]);
                break;

            case 'actualizar_concepto_caja_fuerte':
                $id = intval($_POST['id'] ?? 0);
                $concepto = trim($_POST['concepto'] ?? '');
                if ($id <= 0 || $concepto === '') {
                    echo json_encode(["status" => "error", "message" => "ID o concepto inválido"]);
                    break;
                }

                $ok = $tesoreria->actualizarConceptoCajaFuerte($id, $concepto);
                echo json_encode([
                    "status" => $ok ? "success" : "error",
                    "message" => $ok ? "Concepto actualizado" : "No se pudo actualizar el concepto"
                ]);
                break;

            case 'cancelar_movimiento_caja_fuerte':
                $id = intval($_POST['id'] ?? 0);
                $usuario_id = intval($_SESSION['usuario_id'] ?? 0);
                $motivo = trim($_POST['motivo'] ?? '');
                if ($id <= 0 || $usuario_id <= 0) {
                    echo json_encode(["status" => "error", "message" => "Datos inválidos"]);
                    break;
                }

                $res = $tesoreria->cancelarMovimientoCajaFuerte($id, $usuario_id, $motivo);
                echo json_encode([
                    "status" => $res ? "success" : "error",
                    "data" => $res,
                    "message" => $res ? "Movimiento cancelado mediante reversa" : "No se pudo cancelar el movimiento"
                ]);
                break;
            default:
                echo json_encode(["status" => "error", "message" => "Acción no definida"]);
                break;
        }
    } catch (Exception $e) {
        // Captura de errores graves para evitar el Error 500 en blanco
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
    exit; // Terminar ejecución para no enviar el HTML de la vista
}

// --- BLOQUE 2: CARGA INICIAL DE LA VISTA ---

// Configuración de variables para el Layout y la Vista
$categoriasCapital = $tesoreria->getCategorias();
$almacen_sesion = $_SESSION['almacen_id'] ?? 0;
$listaAlmacenes = $almacenModel->getAlmacenes($almacen_sesion);
$saldoCajas = $corteCaja->saldoCajaFuerte($almacen_sesion);
$saldosCuentasBancarias = $corteCaja->saldoCuentasBancarias($almacen_sesion);
$paginaActual = 'tesoreria';

// Carga de la vista HTML
require_once __DIR__ . '/../views/tesoreria_view.php';