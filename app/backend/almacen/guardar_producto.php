<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$base_path = $_SERVER['DOCUMENT_ROOT'] . '/cfsistem';
require_once $base_path . '/includes/auth.php';
require_once $base_path . '/config/conexion.php';

echo '<!DOCTYPE html>
<html lang="es">
<head>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>body { font-family: sans-serif; }</style>
</head>
<body>';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sku = $_POST['sku'] ?? '';
    $nombre = $_POST['nombre'] ?? '';
    $descripcion = $_POST['descripcion'] ?? '';
    $categoria_id = !empty($_POST['categoria_id']) ? $_POST['categoria_id'] : null;
    $unidad_medida = $_POST['unidad_medida'] ?? '';
    $precio_adquisicion = $_POST['precio_adquisicion'] ?? 0;
    $fiscal_clave_prod = $_POST['fiscal_clave_prod'] ?? null;
    $fiscal_clave_unit = $_POST['fiscal_clave_unit'] ?? null;
    $impuesto_iva = $_POST['impuesto_iva'] ?? 16.00;

    $conexion->begin_transaction();

    try {
        $checkSku = $conexion->prepare("SELECT id FROM productos WHERE sku = ?");
        $checkSku->bind_param("s", $sku);
        $checkSku->execute();
        if ($checkSku->get_result()->num_rows > 0) {
            throw new Exception("El SKU '$sku' ya está registrado.");
        }

        $stmtProd = $conexion->prepare("INSERT INTO productos 
            (sku, nombre, descripcion, unidad_medida, fiscal_clave_prod, fiscal_clave_unidad, precio_adquisicion, impuesto_iva, categoria_id) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        $stmtProd->bind_param("ssssssddi", 
            $sku, $nombre, $descripcion, $unidad_medida, 
            $fiscal_clave_prod, $fiscal_clave_unit, $precio_adquisicion, 
            $impuesto_iva, $categoria_id
        );
        $stmtProd->execute();
        $producto_id = $conexion->insert_id;

        if (isset($_POST['almacenes']) && is_array($_POST['almacenes'])) {
            foreach ($_POST['almacenes'] as $almacen_id => $datos) {
                if (isset($datos['activo']) && $datos['activo'] == '1') {
                    $stock = !empty($datos['stock']) ? $datos['stock'] : 0;
                    $min = !empty($datos['stock_minimo']) ? $datos['stock_minimo'] : 0;
                    $p_minorista = !empty($datos['precio_minorista']) ? $datos['precio_minorista'] : 0;
                    $p_mayorista = !empty($datos['precio_mayorista']) ? $datos['precio_mayorista'] : 0;
                    $p_distribuidor = !empty($datos['precio_distribuidor']) ? $datos['precio_distribuidor'] : 0;

                    $stmtInv = $conexion->prepare("INSERT INTO inventario (almacen_id, producto_id, stock, stock_minimo) VALUES (?, ?, ?, ?)");
                    $stmtInv->bind_param("iidd", $almacen_id, $producto_id, $stock, $min);
                    $stmtInv->execute();

                    $stmtPre = $conexion->prepare("INSERT INTO precios_producto (producto_id, almacen_id, precio_minorista, precio_mayorista, precio_distribuidor) VALUES (?, ?, ?, ?, ?)");
                    $stmtPre->bind_param("iiddd", $producto_id, $almacen_id, $p_minorista, $p_mayorista, $p_distribuidor);
                    $stmtPre->execute();

                    if ($stock > 0) {
                        $usuario_id = $_SESSION['usuario_id'] ?? 1;
                        $obs = "Carga inicial de producto";
                        $stmtMov = $conexion->prepare("INSERT INTO movimientos (producto_id, tipo, cantidad, almacen_destino_id, usuario_registra_id, observaciones) VALUES (?, 'entrada', ?, ?, ?, ?)");
                        $stmtMov->bind_param("idiis", $producto_id, $stock, $almacen_id, $usuario_id, $obs);
                        $stmtMov->execute();
                    }
                }
            }
        }

        $conexion->commit();
        echo "<script>
            Swal.fire({ icon: 'success', title: '¡Éxito!', text: 'Producto guardado correctamente.', confirmButtonColor: '#198754' })
            .then(() => { window.location.href = '/cfsistem/app/views/almacenes.php'; });
        </script>";

    } catch (Exception $e) {
        $conexion->rollback();
        $msg = addslashes($e->getMessage());
        echo "<script>
            Swal.fire({ icon: 'error', title: 'Error', text: '$msg', confirmButtonColor: '#d33' })
            .then(() => { window.history.back(); });
        </script>";
    }
}
echo '</body></html>';