<?php
$base_path = $_SERVER['DOCUMENT_ROOT'] . '/cfsistem';
require_once $base_path . '/includes/auth.php';
require_once $base_path . '/config/conexion.php';

if (session_status() === PHP_SESSION_NONE) { session_start(); }
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conexion->begin_transaction();
    try {
        $user_id = $_SESSION['user_id'] ?? 1;
        $tipo = mysqli_real_escape_string($conexion, $_POST['tipo_egreso']);
        $folio = mysqli_real_escape_string($conexion, $_POST['folio']);
        $entidad = mysqli_real_escape_string($conexion, $_POST['entidad']);
        $total_final = floatval($_POST['total_final']);
        $items = json_decode($_POST['items_json'], true);
        $tiene_faltantes = intval($_POST['tiene_faltantes'] ?? 0);

        if ($tipo === 'compra') {
            // 1. Insertar Cabecera
            $sqlC = "INSERT INTO compras (folio, proveedor, fecha_compra, almacen_id, total, estado, usuario_registra_id, tiene_faltantes) 
                     VALUES (?, ?, NOW(), 1, ?, 'confirmada', ?, ?)";
            $stmtC = $conexion->prepare($sqlC);
            $stmtC->bind_param("ssdii", $folio, $entidad, $total_final, $user_id, $tiene_faltantes);
            $stmtC->execute();
            $compra_id = $conexion->insert_id;

            // 2. Procesar Items
            foreach ($items as $item) {
                $p_id = intval($item['producto_id']);
                $cant_fac = floatval($item['cantidad']); // Lo que dice la factura
                $cant_fal = floatval($item['cantidad_faltante']); // Lo que NO llegó
                $cant_real = $cant_fac - $cant_fal; // LO QUE REALMENTE ENTRA AL STOCK
                $precio = floatval($item['precio']);
                $subtotal = floatval($item['subtotal']);
                $estado_e = ($cant_fal > 0) ? 'incompleto' : 'completo';

                // Guardar Detalle (Referencia de lo facturado vs faltante)
                $sqlD = "INSERT INTO detalle_compra (compra_id, producto_id, cantidad, cantidad_faltante, precio_unitario, subtotal, estado_entrega) 
                         VALUES (?, ?, ?, ?, ?, ?, ?)";
                $stmtD = $conexion->prepare($sqlD);
                $stmtD->bind_param("iidddds", $compra_id, $p_id, $cant_fac, $cant_fal, $precio, $subtotal, $estado_e);
                $stmtD->execute();

                // NUEVO: Si hay faltante, insertar en la tabla espejo faltantes_ingreso
                if ($cant_fal > 0) {
                    $sqlF = "INSERT INTO faltantes_ingreso (compra_id, producto_id, cantidad_pendiente) VALUES (?, ?, ?)";
                    $stmtF = $conexion->prepare($sqlF);
                    $stmtF->bind_param("iid", $compra_id, $p_id, $cant_fal);
                    $stmtF->execute();
                }

                // 3. Inventario y Movimientos (Solo lo que llegó físicamente)
                if ($cant_real > 0 && isset($item['distribucion'])) {
                    foreach ($item['distribucion'] as $dist) {
                        $alm_id = intval($dist['almacen_id']);
                        $c_dist = floatval($dist['cantidad']);

                        // Actualizar Stock
                        $sqlInv = "INSERT INTO inventario (almacen_id, producto_id, stock) VALUES (?, ?, ?) 
                                   ON DUPLICATE KEY UPDATE stock = stock + VALUES(stock)";
                        $stmtInv = $conexion->prepare($sqlInv);
                        $stmtInv->bind_param("iid", $alm_id, $p_id, $c_dist);
                        $stmtInv->execute();

                        // Registrar Movimiento
                        $obs = ($cant_fal > 0) ? "Entrada parcial. Faltaron $cant_fal" : "Compra completa";
                        $sqlMov = "INSERT INTO movimientos (producto_id, tipo, cantidad, almacen_destino_id, usuario_registra_id, referencia_id, observaciones) 
                                   VALUES (?, 'entrada', ?, ?, ?, ?, ?)";
                        $stmtMov = $conexion->prepare($sqlMov);
                        $stmtMov->bind_param("idiiis", $p_id, $c_dist, $alm_id, $user_id, $compra_id, $obs);
                        $stmtMov->execute();
                    }
                }
            }
        } else {
            // GASTOS (Lógica simple)
            $sqlG = "INSERT INTO gastos (folio, fecha_gasto, almacen_id, usuario_registra_id, beneficiario, total) VALUES (?, NOW(), 1, ?, ?, ?)";
            $stmtG = $conexion->prepare($sqlG);
            $stmtG->bind_param("sisd", $folio, $user_id, $entidad, $total_final);
            $stmtG->execute();
        }

        $conexion->commit();
        echo json_encode(["status" => "success", "message" => "Registro guardado correctamente"]);
    } catch (Exception $e) {
        $conexion->rollback();
        echo json_encode(["status" => "error", "message" => $e->getMessage()]);
    }
}