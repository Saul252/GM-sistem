<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../controllers/LayoutController.php';
require_once __DIR__ . '/../models/ventas_model.php';
require_once __DIR__ . '/../models/clientesModel.php';
require_once __DIR__ . '/../models/almacen_model.php';
require_once __DIR__ . '/../models/categoriasModel.php';

// Instanciamos el modelo una sola vez
$clientesModel = new ClientesModel($conexion);

// --- ACCIONES POST (Guardar Venta) ---
$input = file_get_contents("php://input");
$data = json_decode($input, true);
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($data['accion'])) {
    header('Content-Type: application/json');
    if (ob_get_level()) ob_clean(); 

    try {
        $id_usuario = $_SESSION['usuario_id'] ?? 1;

        if ($data['accion'] === 'guardar_venta') {
            error_log("CF_SYSTEM_LOG: Iniciando guardado de venta");
            error_log("CF_SYSTEM_LOG: Datos recibidos: " . json_encode($data));

            // Cambiamos a guardarVentaRapida que es la función que revisamos antes
           $resultado = VentasModel::procesarVenta($conexion, $data, $id_usuario);
            
            
            error_log("CF_SYSTEM_LOG: Respuesta de VentasModel: " . json_encode($resultado));

            if ($resultado['status'] === 'success') {
                
                $id_venta = $resultado['id_venta'] ?? 0; // Usamos el ID que viene del modelo
                $id_cliente = intval($data['id_cliente'] ?? 0); // Ajustado a 'id_cliente' como en el JS
                $fecha_actual = date('Y-m-d H:i:s');

                // PASO 3: Validar datos de Saldo a Favor
                $usar_check = isset($data['usar_saldo_favor']) ? intval($data['usar_saldo_favor']) : 0;
                $monto_solicitado = isset($data['monto_usado_favor']) ? floatval($data['monto_usado_favor']) : 0;

                error_log("CF_SYSTEM_LOG: Intento de uso de saldo -> Check: $usar_check, Monto: $monto_solicitado");

                if ($usar_check === 1 && $monto_solicitado > 0 && $id_venta > 0) {
                    error_log("CF_SYSTEM_LOG: Entrando a la lógica de descuento de saldo para cliente: $id_cliente");
                    
                    $modeloSaldos = new ClientesModel($conexion); 

                    // Ajuste negativo para restar del saldo_a_favor
                    $ajuste_negativo = $monto_solicitado * -1;
                    
                    // PASO 4: Ejecutar actualización maestra (Usando $id_cliente e $id_venta consistentes)
                    $resMaestra = $modeloSaldos->abono_saldosAFavor($id_cliente, $ajuste_negativo, $id_venta, $fecha_actual);
                    error_log("CF_SYSTEM_LOG: Resultado abono_saldosAFavor: " . ($resMaestra ? 'EXITO' : 'FALLO'));

                    // PASO 5: Ejecutar Log
                    $resLog = $modeloSaldos->abono_saldos_log(
                        $id_cliente, 
                        $id_venta, 
                        $monto_solicitado, 
                        $id_usuario, 
                        'USO_SALDO_A_FAVOR', 
                        $fecha_actual
                    );
                    error_log("CF_SYSTEM_LOG: Resultado abono_saldos_log: " . ($resLog ? 'EXITO' : 'FALLO'));
                } else {
                    error_log("CF_SYSTEM_LOG: Se saltó la lógica de saldo (Check: $usar_check, Monto: $monto_solicitado, ID Venta: $id_venta)");
                }
            }
            echo json_encode($resultado);
        }
    } catch (Exception $e) {
        error_log("CF_SYSTEM_LOG: ERROR CRÍTICO: " . $e->getMessage());
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
    exit;
}

// --- ACCIONES GET (Estado de Cuenta) ---
if (isset($_GET['action']) && $_GET['action'] === 'obtenerEstadoCuenta') {
    header('Content-Type: application/json');
    if (ob_get_level()) ob_clean();
    
    try {
        $id = intval($_GET['id'] ?? 0);
        if ($id <= 0) throw new Exception("ID de cliente no válido.");

        // Obtenemos datos del modelo
        $resumen = ClientesModel::obtenerSaldoActual($conexion, $id);
        $historial_res = ClientesModel::obtenerHistorialLog($conexion, $id);
        
        $movimientos = [];
        if ($historial_res) {
            while ($row = $historial_res->fetch_assoc()) {
                $movimientos[] = $row;
            }
        }

        echo json_encode([
            'success' => true,
            'resumen' => $resumen,
            'movimientos' => $movimientos
        ]);
    } catch (Throwable $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;


    }






    
if (isset($_GET['action']) && $_GET['action'] === 'obtenerEstatusCliente') {
    if (ob_get_level()) ob_clean(); 
    header('Content-Type: application/json');
    
    try {
        $id = intval($_GET['id'] ?? 0);
        if ($id <= 0) {
            throw new Exception("ID de cliente no válido.");
        }

        // 1. Obtenemos los datos directamente (ya vienen como array desde el modelo)
        $datos = ClientesModel::obtenerEstatus($conexion, $id);

        if (!$datos) {
            throw new Exception("No se encontraron registros de saldo para este cliente.");
        }

        // 2. Mapeamos los datos para que el JS los reciba sin romperse
        // Usamos los nombres de columna reales de la tabla clientes_saldos
        echo json_encode([
            'success'            => true,
            'id'                 => $id,
            'nombre_comercial'   => $datos['nombre_comercial'] ?? 'Cliente',
            'saldo_neto'         => floatval($datos['saldo_neto'] ?? 0),
            'saldo_en_contra'    => floatval($datos['saldo_en_contra'] ?? 0),
            'saldo_a_favor'      => floatval($datos['saldo_a_favor'] ?? 0),
            'estatus_financiero' => $datos['estatus_financiero'] ?? 'AL DIA',
            // Mantenemos estos por compatibilidad si otros scripts los usan:
            'resumen' => [
                'saldo_total' => floatval($datos['saldo_neto'] ?? 0),
                'condicion'   => $datos['estatus_financiero'] ?? 'AL DIA'
            ]
        ]);

    } catch (Throwable $e) {
        error_log("Error en obtenerEstatusCliente: " . $e->getMessage());
        echo json_encode([
            'success' => false, 
            'message' => $e->getMessage()
        ]);
    }
    exit;
}
// --- CARGA DE VISTA ---
protegerPagina('ventas'); 
$paginaActual = 'ventas';
$almacen_usuario = $_SESSION['almacen_id'] ?? 0;

// Almacenes
$almacenModel = new AlmacenModel($conexion);
$almacenes = $almacenModel->getAlmacenes($almacen_usuario);

// Categorías
$categorias_res = CategoriasModel::listar($conexion);
$categorias = ($categorias_res) ? $categorias_res->fetch_all(MYSQLI_ASSOC) : [];

// Productos
$productos_res = VentasModel::obtenerProductos($conexion, $almacen_usuario);
$productos = ($productos_res) ? $productos_res->fetch_all(MYSQLI_ASSOC) : [];

// Clientes (Asegúrate de que listarTodos traiga rfc, razon_social, regimen_fiscal)
$clientes_res = $clientesModel->listarTodos($almacen_usuario); 
$clientes = ($clientes_res) ? $clientes_res->fetch_all(MYSQLI_ASSOC) : [];

include __DIR__ . '/../views/ventas_view.php';