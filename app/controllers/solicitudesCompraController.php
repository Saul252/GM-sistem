<?php
// 1. Reporte de errores para debug (quitar en producción)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../controllers/LayoutController.php';

// 2. Carga de Modelos
require_once __DIR__ . '/../models/solicitudCompraModel.php'; 
require_once __DIR__ . '/../models/productosModel.php';
require_once __DIR__ . '/../models/proveedoresModel.php';
require_once __DIR__ . '/../models/almacen_model.php'; 
require_once __DIR__ . '/../models/egresos/comprasModel.php';

require_once __DIR__ . '/../models/egresos_model.php';


protegerPagina('solicitudesCompra'); 

$solicitudModel = new SolicitudCompra($conexion);
$productosModel = new ProductosModel($conexion);
$almacenModel   = new AlmacenModel($conexion);
$egresoModel   = new EgresoModel($conexion);
$proveedorModel = new ProveedoresModel($conexion);
$comprasModel = new CompraModel($conexion);
$paginaActual = 'solicitudesCompra'; 
$almacen_usuario = $_SESSION['almacen_id'] ?? 0;
$es_admin = ($_SESSION['rol_id'] == 1 || $almacen_usuario == 0);

// --- ACCIÓN: GUARDAR (AJAX) ---
if (isset($_GET['action']) && $_GET['action'] === 'guardar') {

    if (ob_get_level()) ob_clean();
    header('Content-Type: application/json; charset=utf-8');

    try {

        // 🔹 Validación de almacén
        $almacen_id = $es_admin 
            ? intval($_POST['almacen_id'] ?? 0) 
            : intval($almacen_usuario);

        if ($almacen_id <= 0) {
            throw new Exception("ID de almacén no válido.");
        }

        // 🔹 Cabecera
        $data = [
            'usuario_id'   => intval($_SESSION['usuario_id']),
            'almacen_id'   => $almacen_id,
            'proveedor_id' => intval($_POST['proveedor_id'] ?? 0)
        ];

        if ($data['proveedor_id'] <= 0) {
            throw new Exception("Debe seleccionar un proveedor.");
        }

        // 🔹 Procesar items
        $items_post = $_POST['items'] ?? [];
        $items_procesados = [];

        foreach ($items_post as $id_producto => $campos) {

            $id_producto = intval($id_producto);

            $cant   = floatval($campos['cant'] ?? 0);
            $factor = floatval($campos['unidad'] ?? 1);

            // 🔥 CORRECCIÓN CLAVE
            $costo  = floatval($campos['precio'] ?? 0);

            $total_base = $cant * $factor;

            // 🔥 permitir guardar aunque uno sea 0
            if ($total_base > 0 || $costo > 0) {

                $items_procesados[$id_producto] = [
                    'cantidad' => $total_base,
                    'costo'    => $costo
                ];
            }
        }

        if (empty($items_procesados)) {
            throw new Exception("No hay productos válidos en la lista.");
        }

        // 🔹 Guardar
        $resultado = $solicitudModel->crear($data, $items_procesados);

        if ($resultado === true) {
            echo json_encode([
                'status' => 'success',
                'message' => '¡Solicitud guardada con éxito!'
            ]);
        } else {
            throw new Exception($resultado ?: "Error en la base de datos.");
        }

    } catch (Throwable $e) {

        http_response_code(400);

        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    }

    exit;
}

// --- ACCIÓN: ELIMINAR --- (Sin cambios, está correcta)
if (isset($_GET['action']) && $_GET['action'] === 'eliminar') {
    if (ob_get_level()) ob_clean();
    header('Content-Type: application/json');
    try {
        $id = intval($_POST['id'] ?? 0);
        if ($id <= 0) throw new Exception("ID no válido.");
        if ($solicitudModel->eliminar($id)) {
            echo json_encode(['status' => 'success', 'message' => 'Eliminado.']);
        } else {
            throw new Exception("Error al eliminar.");
        }
    } catch (Throwable $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
    exit;
}
if (isset($_GET['action']) && $_GET['action'] === 'getSiguienteFolio') {
    header('Content-Type: application/json');
    $siguiente = $comprasModel->generarSiguienteFolio();
    echo json_encode(['success' => true, 'folio' => $siguiente]);
    exit;
}
// --- CARGA DE VISTA ---
if ($_SERVER['REQUEST_METHOD'] === 'GET' && !isset($_GET['action'])) {
    try {
        $solicitudes = $solicitudModel->listar($es_admin, $almacen_usuario);
        // Nota: Verifica que sea listarTodo() o listarTodos() según tu ProductosModel
        $productos   = $productosModel->listarTodo(); 
        $proveedores = $proveedorModel->listarTodos();
        $almacenes   = $almacenModel->getAlmacenes($almacen_usuario); 

        $tituloPagina = "Solicitudes de Compra";
      
        require_once __DIR__ . '/../views/solicitudesCompra_view.php';
        
    } catch (Exception $e) {
        die("Error fatal: " . $e->getMessage());
    }
}if (isset($_GET['action']) && $_GET['action'] === 'obtenerDetalle') {
    // Limpieza de buffer para evitar basura en el JSON
    if (ob_get_level()) ob_end_clean();
    header('Content-Type: application/json; charset=utf-8');

    try {
        $id = intval($_GET['id'] ?? 0);
        
        // Llamamos al modelo. Si el modelo tiene 'return', $detalle tendrá los datos.
        $detalle = $solicitudModel->obtenerDetalle($id);
        

        if ($detalle === null) {
            throw new Exception("El modelo no devolvió datos (Void).");
        }
        $proveedor_id = $detalle[0]['proveedor_id'] ?? 0;
        $deudas = $proveedorModel->ProveedorYDeuda($proveedor_id);

        echo json_encode([
            'status' => 'success',
            'data'   => $detalle,
            'deuda'  => $deudas
        ]);


    } catch (Throwable $e) {
        echo json_encode([
            'status'  => 'error', 
            'message' => $e->getMessage()
        ]);
    }
    exit;
}
if (isset($_POST['action']) && $_POST['action'] === 'guardarCompraCompleta') {
    if (ob_get_level()) ob_clean();
    header('Content-Type: application/json; charset=utf-8');

    try {
        // 🔥 Decodificar UNA sola vez
        $items = json_decode($_POST['items'], true);

        // 🔥 Validar JSON correctamente
        if (!$items || json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception("Error en items JSON: " . json_last_error_msg());
        }

        $almacen_id = intval($_POST['almacen_id'] ?? 0);
        $solicitud_id = intval($_POST['solicitud_id'] ?? 0);
        $metodo_pago = $_POST['metodo_pago'] ?? 'Efectivo';
       $proveedorData = $proveedorModel->obtenerProveedorPorNombre($_POST['proveedor']);
$proveedor = $proveedorData['id'] ?? 0;
        if ($almacen_id <= 0) throw new Exception("ID de almacén no válido.");
        if (empty($items)) throw new Exception("No hay productos para procesar.");

        // 1. Guardar la compra
        $resultado = $comprasModel->guardarCompraCompleta(
            $items, 
            $_POST['folio'] ?? '', 
            $proveedor, 
            $_FILES['evidencia_compra'] ?? null, 
            $almacen_id, 
            $_SESSION['usuario_id'] ?? 0,
            $metodo_pago
        );

        // 2. Actualizar solicitud si aplica
        if ($resultado['success'] === true && $solicitud_id > 0) {
            $id_generado = $resultado['compra_id'] ?? null;

            $solicitudModel->actualizarEstado($solicitud_id, 'recibido', $id_generado);

            $resultado['message'] .= " (Solicitud #$solicitud_id finalizada)";
        }
         $saldo = floatval($_POST['saldo_a_pagar'] ?? 0);
        if ($saldo > 0) {
            $proveedor_id = $proveedor;
            if ($proveedor_id <= 0) throw new Exception("Proveedor inválido para pago de deuda");

            $deudas = $proveedorModel->ProveedorYDeuda($proveedor_id);
            if (empty($deudas)) throw new Exception("El proveedor no tiene deudas pendientes");

            foreach ($deudas as $deuda) {
                if ($saldo <= 0) break;

                $cuenta_id = intval($deuda['compra_id']);
                $pendiente = floatval($deuda['pendiente']);
                $metodoPago = $_POST['metodo_pago'] ?? 'Efectivo';

                if ($cuenta_id <= 0 || $pendiente <= 0) continue;

                $pago_aplicado = min($saldo, $pendiente);

                // A. Actualizar saldo en la tabla de deuda
                $res = $egresoModel->pagarDeudaCompra($cuenta_id, $pago_aplicado);
                $proveedorNombre=$proveedorModel->obtenerPorId($proveedor_id);
                
                // B. Registrar en historial de pagos
                $desc = 'Pago de deuda (Compra #' . $cuenta_id . ') por $' . number_format($pago_aplicado, 2);
                $ref = "PC-" . $cuenta_id; // Evitar string vacío

                $regPago = $egresoModel->registrarPagoCuentaPorPagar(
                    $almacen_id,
                    $proveedor_id,
                    $cuenta_id,
                    $pago_aplicado,
                    $metodoPago,
                    $ref,
                    $_SESSION['usuario_id'] ?? 0,
                    $desc
                );

                if (!$res || (isset($res['success']) && !$res['success'])) {
                    throw new Exception("Error al descontar saldo de la deuda ID: $cuenta_id");
                }
                
                $saldo -= $pago_aplicado;
            }
        }


        echo json_encode($resultado);

    } catch (Throwable $e) {
        echo json_encode([
            'success' => false, 
            'message' => $e->getMessage()
        ]);
    }
    exit;
}