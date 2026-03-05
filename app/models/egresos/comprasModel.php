<?php
class CompraModel {
    private $db;

    public function __construct($conexion) {
        $this->db = $conexion;
    }

public function guardarCompraCompleta($items, $folio, $proveedor, $evidencia, $almacen_id, $user_id) {
    $this->db->begin_transaction();
    try {
        // --- 1. Gestión de Evidencia (Archivo) ---
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

        // --- 2. Cálculos Previos ---
        $total_final = 0;
        $tiene_faltantes_global = 0;
        foreach ($items as $item) {
            $total_final += floatval($item['total_item']);
            if (floatval($item['cantidad_faltante'] ?? 0) > 0) {
                $tiene_faltantes_global = 1;
            }
        }

        // --- 3. Insertar Cabecera (Tabla: compras) ---
        // Incluimos estado 'confirmada', documento_url y tiene_faltantes
        $sqlC = "INSERT INTO compras (folio, proveedor, fecha_compra, almacen_id, total, estado, usuario_registra_id, documento_url, tiene_faltantes) 
                 VALUES (?, ?, NOW(), ?, ?, 'confirmada', ?, ?, ?)";
        $stmtC = $this->db->prepare($sqlC);
        // Bind: s(folio), s(proveedor), i(almacen), d(total), i(usuario), s(url), i(faltante)
        $stmtC->bind_param("ssidisi", $folio, $proveedor, $almacen_id, $total_final, $user_id, $documento_url, $tiene_faltantes_global);
        
        if (!$stmtC->execute()) { throw new Exception("Error en cabecera: " . $stmtC->error); }
        $compra_id = $stmtC->insert_id;

        // --- 4. Procesar Items ---
        foreach ($items as $item) {
            $p_id = intval($item['producto_id']);
            $factor = floatval($item['hidden_factor'] ?? 1);
            
            // Cantidad total que dice la factura (en piezas)
            // Calculamos la base: (Mayoreo * Factor) + Sueltas
            $cant_fac = (floatval($item['input_mayoreo'] ?? 0) * $factor) + floatval($item['input_sueltas'] ?? 0);
            
            // Cantidad que el usuario marcó como faltante (ya viene en piezas desde el JS)
            $cant_fal = floatval($item['cantidad_faltante'] ?? 0);
            
            $subtotal = floatval($item['total_item']);
            $precio_u = $cant_fac > 0 ? ($subtotal / $cant_fac) : 0;
            $estado_e = ($cant_fal > 0) ? 'incompleto' : 'completo';

            // --- 5. Insertar Detalle (Tabla: detalle_compra) ---
            $sqlD = "INSERT INTO detalle_compra (compra_id, producto_id, cantidad, cantidad_faltante, precio_unitario, subtotal, estado_entrega) 
                     VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmtD = $this->db->prepare($sqlD);
            $stmtD->bind_param("iidddds", $compra_id, $p_id, $cant_fac, $cant_fal, $precio_u, $subtotal, $estado_e);
            $stmtD->execute();

            // --- 6. Registrar Faltante (Tabla: faltantes_ingreso) ---
            if ($cant_fal > 0) {
                $sqlF = "INSERT INTO faltantes_ingreso (compra_id, producto_id, cantidad_pendiente) VALUES (?, ?, ?)";
                $stmtF = $this->db->prepare($sqlF);
                $stmtF->bind_param("iid", $compra_id, $p_id, $cant_fal);
                $stmtF->execute();
            }

            // --- 7. Inventario y Movimientos (Solo lo que llegó físicamente) ---
            if (isset($item['almacenes'])) {
                foreach ($item['almacenes'] as $id_alm_dest => $dist) {
                    if (isset($dist['activo']) && $dist['activo'] === 'on') {
                        $cant_reparto = floatval($dist['cantidad']);
                        if ($cant_reparto <= 0) continue;

                        // Actualizar Stock en inventario
                        $sqlI = "INSERT INTO inventario (almacen_id, producto_id, stock) 
                                 VALUES (?, ?, ?) 
                                 ON DUPLICATE KEY UPDATE stock = stock + VALUES(stock)";
                        $stmtI = $this->db->prepare($sqlI);
                        $stmtI->bind_param("iid", $id_alm_dest, $p_id, $cant_reparto);
                        $stmtI->execute();

                        // Registrar Movimiento de entrada
                        $sqlM = "INSERT INTO movimientos (producto_id, tipo, cantidad, almacen_destino_id, usuario_registra_id, referencia_id, observaciones) 
                                 VALUES (?, 'entrada', ?, ?, ?, ?, ?)";
                        $stmtM = $this->db->prepare($sqlM);
                        $obs = "Compra Folio: $folio " . ($cant_fal > 0 ? " (Faltante: $cant_fal pzas)" : "");
                        $stmtM->bind_param("idiiis", $p_id, $cant_reparto, $id_alm_dest, $user_id, $compra_id, $obs);
                        $stmtM->execute();
                    }
                }
            }
        }

        $this->db->commit();
        return ['success' => true, 'message' => 'Compra registrada. ' . ($tiene_faltantes_global ? 'Estado: Incompleta.' : 'Estado: Completa.')];

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
}