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
                'producto_id'  => $_POST['producto_id'] ?? 0,
                'nombre'       => $_POST['nombre'] ?? '',
                'equivalencia' => $_POST['equivalencia'] ?? 0
            ];

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

            $id         = $_GET['id'] ?? 0;
            $almacen_id = $_GET['almacen_id'] ?? 0;

            if (!$id || !$almacen_id) {

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

}