<?php
class CompraModel {
    private $db;

    public function __construct($conexion) {
        $this->db = $conexion;
    }

public function guardarCompraCompleta($items, $folio, $proveedor, $evidencia, $almacen_id, $user_id) {
        $this->db->begin_transaction();
        try {
            // --- 1. Gestión de Evidencia ---
            $documento_url = null;
            if ($evidencia && $evidencia['error'] === UPLOAD_ERR_OK) {
                $ruta_carpeta = $_SERVER['DOCUMENT_ROOT'] . "/cfsistem/uploads/compras/";
                if (!is_dir($ruta_carpeta)) { mkdir($ruta_carpeta, 0777, true); }
                $extension = pathinfo($evidencia['name'], PATHINFO_EXTENSION);
                $nombre_archivo = "compra_" . preg_replace('/[^a-zA-Z0-9]/', '_', $folio) . "_" . time() . "." . $extension;
                $ruta_destino = $ruta_carpeta . $nombre_archivo;
                if (move_uploaded_file($evidencia['tmp_name'], $ruta_destino)) {
                    $documento_url = "uploads/compras/" . $nombre_archivo;
                }
            }

            // --- 2. Totales y Faltantes ---
            $total_final = 0;
            $tiene_faltantes_global = 0;
            foreach ($items as $item) {
                $total_final += floatval($item['total_item']);
                if (floatval($item['cantidad_faltante'] ?? 0) > 0) $tiene_faltantes_global = 1;
            }

            // --- 3. Insertar Cabecera ---
            $sqlC = "INSERT INTO compras (folio, proveedor, fecha_compra, almacen_id, total, estado, usuario_registra_id, documento_url, tiene_faltantes) 
                     VALUES (?, ?, NOW(), ?, ?, 'confirmada', ?, ?, ?)";
            $stmtC = $this->db->prepare($sqlC);
            $stmtC->bind_param("ssidisi", $folio, $proveedor, $almacen_id, $total_final, $user_id, $documento_url, $tiene_faltantes_global);
            if (!$stmtC->execute()) throw new Exception("Error en cabecera: " . $stmtC->error);
            $compra_id = $stmtC->insert_id;

            // --- 4. Procesar Items ---
            foreach ($items as $item) {
                $p_id = intval($item['producto_id']);
                $factor = floatval($item['hidden_factor'] ?? 1);
                $cant_fac = (floatval($item['input_mayoreo'] ?? 0) * $factor) + floatval($item['input_sueltas'] ?? 0);
                $cant_fal = floatval($item['cantidad_faltante'] ?? 0);
                $subtotal = floatval($item['total_item']);
                
                // IMPORTANTE: El precio unitario para el detalle histórico y para el LOTE
                $precio_lote = floatval($item['precio_lote'] ?? 0); 
                $estado_e = ($cant_fal > 0) ? 'incompleto' : 'completo';

                // --- 5. Insertar Detalle Histórico ---
                $sqlD = "INSERT INTO detalle_compra (compra_id, producto_id, cantidad, cantidad_faltante, precio_unitario, subtotal, estado_entrega) 
                         VALUES (?, ?, ?, ?, ?, ?, ?)";
                $stmtD = $this->db->prepare($sqlD);
                $stmtD->bind_param("iidddds", $compra_id, $p_id, $cant_fac, $cant_fal, $precio_lote, $subtotal, $estado_e);
                $stmtD->execute();
                $detalle_id = $stmtD->insert_id;

                // --- 6. Registrar Faltante Pendiente ---
                if ($cant_fal > 0) {
                    $sqlF = "INSERT INTO faltantes_ingreso (compra_id, producto_id, cantidad_pendiente) VALUES (?, ?, ?)";
                    $stmtF = $this->db->prepare($sqlF);
                    $stmtF->bind_param("iid", $compra_id, $p_id, $cant_fal);
                    $stmtF->execute();
                }

                // --- 7. Inventario, MOVIMIENTOS Y LOTES ---
                if (isset($item['almacenes'])) {
                    foreach ($item['almacenes'] as $id_alm_dest => $dist) {
                        if (isset($dist['activo']) && $dist['activo'] === 'on') {
                            $cant_reparto = floatval($dist['cantidad']);
                            if ($cant_reparto <= 0) continue;

                            // A. Actualizar Inventario General
                            $sqlI = "INSERT INTO inventario (almacen_id, producto_id, stock) 
                                     VALUES (?, ?, ?) 
                                     ON DUPLICATE KEY UPDATE stock = stock + VALUES(stock)";
                            $stmtI = $this->db->prepare($sqlI);
                            $stmtI->bind_param("iid", $id_alm_dest, $p_id, $cant_reparto);
                            $stmtI->execute();

                            // B. CREAR EL LOTE (Para trazabilidad de costos)
                            $codigo_lote = "LOTE-" . $compra_id . "-" . $p_id . "-" . $id_alm_dest;
                            $sqlL = "INSERT INTO lotes_stock (producto_id, almacen_id, codigo_lote, cantidad_inicial, cantidad_actual, precio_compra_unitario, estado_lote) 
                                     VALUES (?, ?, ?, ?, ?, ?, 'activo')";
                            $stmtL = $this->db->prepare($sqlL);
                            $stmtL->bind_param("iisddd", $p_id, $id_alm_dest, $codigo_lote, $cant_reparto, $cant_reparto, $precio_lote);
                            $stmtL->execute();
                            $lote_id = $stmtL->insert_id;

                            // C. Vincular Lote con la Compra (lotes_ingresos_detalle)
                            $sqlLI = "INSERT INTO lotes_ingresos_detalle (lote_id, detalle_compra_id, cantidad_recibida, costo_aplicado) 
                                      VALUES (?, ?, ?, ?)";
                            $stmtLI = $this->db->prepare($sqlLI);
                            $stmtLI->bind_param("iidd", $lote_id, $detalle_id, $cant_reparto, $precio_lote);
                            $stmtLI->execute();

                            // D. Registrar Movimiento (Kardex)
                            $sqlM = "INSERT INTO movimientos (producto_id, tipo, cantidad, almacen_destino_id, usuario_registra_id, referencia_id, observaciones) 
                                     VALUES (?, 'entrada', ?, ?, ?, ?, ?)";
                            $stmtM = $this->db->prepare($sqlM);
                            $obs = "Compra Folio: $folio (Lote: $codigo_lote)";
                            $stmtM->bind_param("idiiis", $p_id, $cant_reparto, $id_alm_dest, $user_id, $compra_id, $obs);
                            $stmtM->execute();
                        }
                    }
                }
            }

            $this->db->commit();
            return ['success' => true, 'message' => 'Compra y lotes generados correctamente.'];

        } catch (Exception $e) {
            $this->db->rollback();
            if (isset($ruta_destino) && file_exists($ruta_destino)) { unlink($ruta_destino); }
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }
    /**
     * Obtiene productos activos para el selector de compras
     * @param string $termino Buscador opcional para Select2 o filtros
     */
    public function obtenerProductos($termino = '') {
        $sql = "SELECT 
                    id, 
                    sku, 
                    nombre, 
                    unidad_medida,
                    unidad_reporte, 
                    factor_conversion, 
                    precio_adquisicion as precio 
                FROM productos 
                WHERE activo = 1";
        
        // Si hay un término de búsqueda (útil para Select2)
        if (!empty($termino)) {
            $sql .= " AND (nombre LIKE ? OR sku LIKE ?)";
            $stmt = $this->db->prepare($sql);
            $busqueda = "%$termino%";
            $stmt->bind_param("ss", $busqueda, $busqueda);
            $stmt->execute();
            $result = $stmt->get_result();
        } else {
            $result = $this->db->query($sql);
        }

        $productos = [];
        while ($row = $result->fetch_assoc()) {
            $productos[] = $row;
        }
        return $productos;
    }

    public function obtenerDetalleFaltantes($compra_id) {
    $sql = "SELECT 
                f.producto_id, 
                f.cantidad_pendiente, 
                p.nombre 
            FROM faltantes_ingreso f
            INNER JOIN productos p ON f.producto_id = p.id
            WHERE f.compra_id = ?";
            
    $stmt = $this->db->prepare($sql);
    $stmt->bind_param("i", $compra_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
/**
 * Procesa el ingreso físico de productos que estaban marcados como faltantes.
 * Realiza la afectación triple: Inventario, Pendientes y Auditoría de Compra.
 */
public function procesarAjusteFaltante($compra_id, $distribucion, $user_id) {
    $this->db->begin_transaction();
    try {
        // 1. Obtener Folio para el historial (Kardex)
        $sqlC = "SELECT folio FROM compras WHERE id = ?";
        $stmtC = $this->db->prepare($sqlC);
        $stmtC->bind_param("i", $compra_id);
        $stmtC->execute();
        $resC = $stmtC->get_result()->fetch_assoc();
        $folio = $resC['folio'] ?? 'S/F';

        foreach ($distribucion as $p_id => $almacenes) {
            $total_recibido_producto = 0;

            foreach ($almacenes as $alm_id => $cantidad) {
                $cantidad = floatval($cantidad);
                if ($cantidad <= 0) continue; // Si el switch estaba ON pero la cantidad es 0, ignoramos

                $total_recibido_producto += $cantidad;

                // A. Actualizar Inventario (Suma en el almacén destino seleccionado)
                $sqlInv = "INSERT INTO inventario (almacen_id, producto_id, stock) 
                           VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE stock = stock + VALUES(stock)";
                $stmtInv = $this->db->prepare($sqlInv);
                $stmtInv->bind_param("iid", $alm_id, $p_id, $cantidad);
                $stmtInv->execute();

                // B. Registrar Movimiento (Kardex)
                $obs = "Entrada Faltante (Compra: $folio)";
                $sqlK = "INSERT INTO movimientos (producto_id, tipo, cantidad, almacen_destino_id, usuario_registra_id, referencia_id, observaciones) 
                         VALUES (?, 'entrada', ?, ?, ?, ?, ?)";
                $stmtK = $this->db->prepare($sqlK);
                $stmtK->bind_param("idiiis", $p_id, $cantidad, $alm_id, $user_id, $compra_id, $obs);
                $stmtK->execute();
            }

            // C. Si hubo ingresos para este producto, descontamos de los saldos pendientes
            if ($total_recibido_producto > 0) {
                // Descontar de la tabla operativa de faltantes
                $this->db->query("UPDATE faltantes_ingreso 
                                  SET cantidad_pendiente = cantidad_pendiente - $total_recibido_producto 
                                  WHERE compra_id = $compra_id AND producto_id = $p_id");

                // Descontar del detalle histórico de la compra
                $this->db->query("UPDATE detalle_compra 
                                  SET cantidad_faltante = cantidad_faltante - $total_recibido_producto 
                                  WHERE compra_id = $compra_id AND producto_id = $p_id");
            }
        }

        // 3. LIMPIEZA: Eliminar registros de pendientes que ya llegaron a 0
        $this->db->query("DELETE FROM faltantes_ingreso WHERE cantidad_pendiente <= 0");

        // 4. ACTUALIZAR CABECERA: Si ya no queda NADA pendiente de esta compra, marcar como finalizada
        $check = $this->db->query("SELECT COUNT(*) as total FROM faltantes_ingreso WHERE compra_id = $compra_id");
        if ($check->fetch_assoc()['total'] == 0) {
            $this->db->query("UPDATE compras SET tiene_faltantes = 0 WHERE id = $compra_id");
        }

        $this->db->commit();
        return ['success' => true, 'message' => 'Distribución de faltantes procesada con éxito.'];

    } catch (Exception $e) {
        $this->db->rollback();
        return ['success' => false, 'message' => 'Error en base de datos: ' . $e->getMessage()];
    }
}
}
