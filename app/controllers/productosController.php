<?php

// 🔧 SESSION_START SIEMPRE PRIMERO
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

ob_start();

require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../models/mermasModel.php';
require_once __DIR__ . '/../models/almacen_model.php';
require_once __DIR__ . '/../models/almacen/productosModel.php';

$productosModel = new ProductoModel($conexion);
$mermasModel    = new MermasModel($conexion);
$almacenModel   = new AlmacenModel($conexion);


// ========================================
// ACTION
// ========================================

$action = $_GET['action'] ?? '';

switch ($action) {

    // ========================================
    // GUARDAR OPCIÓN DE MEDIDA
    // ========================================

    case 'guardarOpcionMedida':

        while (ob_get_level()) ob_end_clean();

        header('Content-Type: application/json; charset=utf-8');

        try {

            $data = [
                'producto_id'  => intval($_POST['producto_id'] ?? 0),
                'nombre'       => trim($_POST['nombre'] ?? ''),
                'equivalencia' => floatval($_POST['equivalencia'] ?? 0)
            ];

            if ($data['producto_id'] <= 0) {
                throw new Exception("Producto inválido");
            }

            if ($data['nombre'] === '') {
                throw new Exception("Nombre requerido");
            }

            if ($data['equivalencia'] <= 0) {
                throw new Exception("Equivalencia inválida");
            }

            $resultado = $productosModel->guardarOpcionMedida($data);

            echo json_encode($resultado);

        } catch (Exception $e) {

            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }

    exit;



    // ========================================
    // OBTENER DETALLE PRODUCTO
    // ========================================

    case 'obtenerProductoDetalle':

        while (ob_get_level()) ob_end_clean();

        header('Content-Type: application/json');

        try {

            $id         = intval($_GET['id'] ?? 0);
            $almacen_id = intval($_GET['almacen_id'] ?? 0);

            if ($id <= 0 || $almacen_id <= 0) {

                echo json_encode([
                    'status'  => 'error',
                    'message' => 'Parámetros incompletos'
                ]);

                exit;
            }

            $resultado = $productosModel
                ->obtenerProductoPorAlmacen($id, $almacen_id);

            if ($resultado['status']) {

                echo json_encode([
                    'status'   => 'success',
                    'producto' => $resultado['data']
                ]);

            } else {

                echo json_encode([
                    'status'  => 'error',
                    'message' => $resultado['msg']
                ]);
            }

        } catch (Exception $e) {

            echo json_encode([
                'status'  => 'error',
                'message' => $e->getMessage()
            ]);
        }

    exit;

  case 'obtnerMedidas':

        while (ob_get_level()) ob_end_clean();

        header('Content-Type: application/json');

        try {

            $id= intval($_GET['id'] ?? 0);
           
            if ($id <= 0 ) {

                echo json_encode([
                    'status'  => 'error',
                    'message' => 'Parámetros incompletos'
                ]);

                exit;
            }

            $medidas = $productosModel
                ->listarMedidas($id);

            if ($medidas['status']) {

                echo json_encode([
                    'status'   => 'success',
                    'producto' => $medidas
                ]);

            } else {

                echo json_encode([
                    'status'  => 'error',
                    'message' => $medidas['msg']
                ]);
            }

        } catch (Exception $e) {

            echo json_encode([
                'status'  => 'error',
                'message' => $e->getMessage()
            ]);
        }

    exit;

    // ========================================
    // ACTUALIZAR MEDIDA
    // ========================================

    case 'actualizarMedidaAdicional':

        while (ob_get_level()) ob_end_clean();

        header('Content-Type: application/json');

        try {

            $id            = intval($_POST['id'] ?? 0);
            $producto_id   = intval($_POST['producto_id'] ?? 0);
            $nombre        = trim($_POST['nombre_edit'] ?? '');
            $equivalencia  = floatval($_POST['equivalencia'] ?? 0);

            if ($id <= 0) {
                throw new Exception("ID inválido");
            }

            if ($producto_id <= 0) {
                throw new Exception("Producto inválido");
            }

            if ($nombre === '') {
                throw new Exception("Nombre requerido");
            }

            if ($equivalencia <= 0) {
                throw new Exception("Equivalencia inválida");
            }

            $resultado = $productosModel->actualizarMedidaAdicional(
                $id,
                $producto_id,
                $nombre,
                $equivalencia
            );

            echo json_encode($resultado);

        } catch (Exception $e) {

            echo json_encode([
                'status'  => false,
                'message' => $e->getMessage()
            ]);
        }

    exit;



    // ========================================
    // ELIMINAR MEDIDA
    // ========================================

    case 'eliminarMedidaAdicional':

        while (ob_get_level()) ob_end_clean();

        header('Content-Type: application/json');

        try {

            $id = intval($_POST['id'] ?? 0);

            if ($id <= 0) {
                throw new Exception("ID inválido");
            }

            $resultado = $productosModel->eliminarMedidaAdicional($id);

            echo json_encode($resultado);

        } catch (Exception $e) {

            echo json_encode([
                'status'  => false,
                'message' => $e->getMessage()
            ]);
        }

    exit;



    // ========================================
    // DEFAULT
    // ========================================

    default:

        while (ob_get_level()) ob_end_clean();

        header('Content-Type: application/json');

        echo json_encode([
            'status'  => false,
            'message' => 'Acción no válida'
        ]);

    exit;
}