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
                // 1. Recolección de datos básicos del POST
                $categoria_id = intval($_POST['categoria_id'] ?? 0);
                $almacen_id = intval($_POST['almacen_id'] ?? 0);
                $usuario_id = intval($_SESSION['usuario_id'] ?? 0);
                $monto_input = floatval($_POST['monto'] ?? 0);
                $concepto = trim($_POST['conceptos'] ?? '');
                $metodo = $_POST['metodo_pago'] ?? 'efectivo';
                $fecha_sel = $_POST['fecha_movimiento'] ?? date('Y-m-d');

                // Destinos opcionales
                $almacen_destino_id = intval($_POST['almacen_destino_id'] ?? 0);
                $caja_fuerte_id = intval($_POST['caja_fuerte_id'] ?? 0);
                $banco_id = intval($_POST['banco_id'] ?? 0);

                // 1.1 Validaciones básicas
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

                // 2. OBTENER TIPO DE OPERACIÓN (Crucial para los cálculos siguientes)
                $sql_cat = "SELECT tipo_operacion FROM capital_categorias WHERE id = ?";
                $stmt_c = $db_conn->prepare($sql_cat);
                $stmt_c->bind_param("i", $categoria_id);
                $stmt_c->execute();
                $cat_info = $stmt_c->get_result()->fetch_assoc();
                $tipo_op = $cat_info['tipo_operacion'] ?? 'entrada';

                // 3. REVISIÓN DE SALDOS ACTUALES (foto acumulada hasta la fecha)
                $saldos_actuales = $corteCaja->obtenerSaldoInicialMonitor($almacen_id, '2000-01-01', $fecha_sel);

                // Normalizar llaves del resultado por si el modelo usa otros nombres
                $saldo_efectivo = floatval($saldos_actuales['monto_efectivo'] ?? 0);
                $saldo_tarjeta = floatval($saldos_actuales['monto_tarjeta'] ?? 0);
                $saldo_transferencia = floatval($saldos_actuales['monto_transferencia'] ?? 0);

                // 4. LÓGICA DE CÁLCULO DE MOVIMIENTO
                $operador = ($tipo_op === 'salida' || $tipo_op === 'traspaso') ? -1 : 1;
                $cambio = $monto_input * $operador;

                // 5. VALIDACIÓN DE SALDO SUFICIENTE (solo para salidas/traspasos)
                if ($cambio < 0) {
                    $saldo_metodo = 0;
                    switch ($metodo) {
                        case 'efectivo':
                            $saldo_metodo = $saldo_efectivo;
                            break;
                        case 'tarjeta':
                            $saldo_metodo = $saldo_tarjeta;
                            break;
                        case 'transferencia':
                            $saldo_metodo = $saldo_transferencia;
                            break;
                    }
                    if (($saldo_metodo + $cambio) < 0) {
                        echo json_encode([
                            "status" => "error",
                            "message" => "Saldo insuficiente en $metodo. Disponible: $" . number_format($saldo_metodo, 2)
                        ]);
                        break;
                    }
                }

                // 5.1 Validación específica de traspaso
                if ($tipo_op === 'traspaso') {
                    if ($almacen_destino_id <= 0) {
                        echo json_encode(["status" => "error", "message" => "Debe seleccionar un almacén destino para el traspaso"]);
                        break;
                    }
                    if ($almacen_destino_id === $almacen_id) {
                        echo json_encode(["status" => "error", "message" => "El almacén destino no puede ser el mismo que el origen"]);
                        break;
                    }
                }

                // 6. CALCULAR NUEVO DESGLOSE PARA EL ALMACÉN ORIGEN
                $nuevo_desglose = [
                    'efectivo' => $saldo_efectivo,
                    'tarjeta' => $saldo_tarjeta,
                    'transferencia' => $saldo_transferencia
                ];
                $nuevo_desglose[$metodo] += $cambio;

                // 7. PREPARACIÓN DE DATA PARA REGISTRO EN ORIGEN
                $data = [
                    'almacen_id' => $almacen_id,
                    'usuario_id' => $usuario_id,
                    'categoria_id' => $categoria_id,
                    'monto' => $monto_input,
                    'metodo_pago' => $metodo,
                    'fecha_movimiento' => $fecha_sel,
                    'concepto' => $concepto,
                    'tipo_operacion' => $tipo_op,
                    'monto_efectivo' => $nuevo_desglose['efectivo'],
                    'monto_tarjeta' => $nuevo_desglose['tarjeta'],
                    'monto_transferencia' => $nuevo_desglose['transferencia'],
                    'almacen_destino_id' => $almacen_destino_id ?: null,
                    'caja_fuerte_id' => $caja_fuerte_id ?: null,
                    'banco_id' => $banco_id ?: null
                ];

                // 8. GUARDAR EL REGISTRO EN EL ALMACÉN ORIGEN
                $res = $corteCaja->registrarAperturaDesdeCierreConcepto($data);

                // 9. SI ES TRASPASO, REGISTRAR LA ENTRADA EN EL ALMACÉN DESTINO
                if ($res && $tipo_op === 'traspaso' && $almacen_destino_id > 0) {
                    $saldos_destino = $corteCaja->obtenerSaldoInicialMonitor($almacen_destino_id, '2000-01-01', $fecha_sel);

                    $nuevo_desglose_destino = [
                        'efectivo' => floatval($saldos_destino['monto_efectivo'] ?? 0),
                        'tarjeta' => floatval($saldos_destino['monto_tarjeta'] ?? 0),
                        'transferencia' => floatval($saldos_destino['monto_transferencia'] ?? 0)
                    ];
                    // En destino el dinero entra, por lo tanto suma
                    $nuevo_desglose_destino[$metodo] += $monto_input;

                    $data_destino = [
                        'almacen_id' => $almacen_destino_id,
                        'usuario_id' => $usuario_id,
                        'categoria_id' => $categoria_id,
                        'monto' => $monto_input,
                        'metodo_pago' => $metodo,
                        'fecha_movimiento' => $fecha_sel,
                        'concepto' => 'TRASPASO RECIBIDO: ' . $concepto,
                        'tipo_operacion' => 'entrada',
                        'monto_efectivo' => $nuevo_desglose_destino['efectivo'],
                        'monto_tarjeta' => $nuevo_desglose_destino['tarjeta'],
                        'monto_transferencia' => $nuevo_desglose_destino['transferencia'],
                        'almacen_destino_id' => null,
                        'caja_fuerte_id' => null,
                        'banco_id' => null
                    ];

                    $res_destino = $corteCaja->registrarAperturaDesdeCierreConcepto($data_destino);

                    if (!$res_destino) {
                        echo json_encode([
                            "status" => "error",
                            "message" => "Traspaso registrado en origen pero falló en destino. Contacte al administrador."
                        ]);
                        break;
                    }
                }

                echo json_encode([
                    "status" => $res ? "success" : "error",
                    "message" => $res
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