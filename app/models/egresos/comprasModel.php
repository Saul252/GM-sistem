<?php
class CompraModel {
    private $db;

    public function __construct($conexion) {
        $this->db = $conexion;
    }

public function guardarCompraCompleta($items, $folio, $proveedor, $evidencia, $almacen_id, $user_id) {
    $this->db->begin_transaction();
    try {
        $documento_url = null;
        if ($evidencia && $evidencia['error'] === UPLOAD_ERR_OK) {
            // --- VALIDACIÓN Y CREACIÓN DE CARPETA ---
            $ruta_carpeta = $_SERVER['DOCUMENT_ROOT'] . "/cfsistem/uploads/compras/";
            if (!is_dir($ruta_carpeta)) {
                mkdir($ruta_carpeta, 0777, true);
            }

            $extension = pathinfo($evidencia['name'], PATHINFO_EXTENSION);
            $nombre_archivo = "compra_" . preg_replace('/[^a-zA-Z0-9]/', '_', $folio) . "_" . time() . "." . $extension;
            $ruta_destino = $ruta_carpeta . $nombre_archivo;
            
            if (move_uploaded_file($evidencia['tmp_name'], $ruta_destino)) {
                $documento_url = "uploads/compras/" . $nombre_archivo;
            }
        }

        // 1. Calcular el total primero
        $total_final = 0;
        foreach ($items as $item) {
            $total_final += floatval($item['total_item']);
        }

        // 2. INSERTAR CABECERA (CORREGIDO: Se agregó columna documento_url)
        // Agregamos la columna y un "?" extra
        $sqlC = "INSERT INTO compras (folio, proveedor, fecha_compra, almacen_id, total, usuario_registra_id, estado, documento_url) 
                 VALUES (?, ?, NOW(), ?, ?, ?, 'confirmada', ?)";
        
        $stmtC = $this->db->prepare($sqlC);
        
        // REVISIÓN DE BIND_PARAM ACTUALIZADA:
        // s(folio), s(proveedor), i(almacen), d(total), i(usuario), s(url)
        // TOTAL: 6 variables -> "ssidis"
        $stmtC->bind_param("ssidis", $folio, $proveedor, $almacen_id, $total_final, $user_id, $documento_url);
        
        if (!$stmtC->execute()) {
            throw new Exception("Error en cabecera: " . $stmtC->error);
        }
        $compra_id = $stmtC->insert_id;

        // 3. PROCESAR ITEMS (Bucle de detalle e inventario se mantiene igual)
        foreach ($items as $item) {
            $p_id = intval($item['producto_id']);
            $cant_fac = floatval($item['cantidad_total_piezas']);
            $subtotal = floatval($item['total_item']);
            $precio_u = $cant_fac > 0 ? ($subtotal / $cant_fac) : 0;

            $sqlD = "INSERT INTO detalle_compra (compra_id, producto_id, cantidad, precio_unitario, subtotal) 
                     VALUES (?, ?, ?, ?, ?)";
            $stmtD = $this->db->prepare($sqlD);
            $stmtD->bind_param("iiddd", $compra_id, $p_id, $cant_fac, $precio_u, $subtotal);
            $stmtD->execute();

            if (isset($item['almacenes'])) {
                foreach ($item['almacenes'] as $id_alm_dest => $dist) {
                    if (isset($dist['activo']) && $dist['activo'] === 'on') {
                        $cant_reparto = floatval($dist['cantidad']);
                        if ($cant_reparto <= 0) continue;

                        $sqlI = "INSERT INTO inventario (almacen_id, producto_id, stock) 
                                 VALUES (?, ?, ?) 
                                 ON DUPLICATE KEY UPDATE stock = stock + VALUES(stock)";
                        $stmtI = $this->db->prepare($sqlI);
                        $stmtI->bind_param("iid", $id_alm_dest, $p_id, $cant_reparto);
                        $stmtI->execute();

                        $sqlM = "INSERT INTO movimientos (producto_id, tipo, cantidad, almacen_destino_id, usuario_registra_id, referencia_id, observaciones) 
                                 VALUES (?, 'entrada', ?, ?, ?, ?, ?)";
                        $stmtM = $this->db->prepare($sqlM);
                        $obs = "Compra Folio: $folio";
                        $stmtM->bind_param("idiiis", $p_id, $cant_reparto, $id_alm_dest, $user_id, $compra_id, $obs);
                        $stmtM->execute();
                    }
                }
            }
        }

        $this->db->commit();
        return ['success' => true, 'message' => 'Compra guardada con evidencia correctamente.'];

    } catch (Exception $e) {
        $this->db->rollback();
        // Limpieza de archivo si algo falló en la BD
        if (isset($ruta_destino) && file_exists($ruta_destino)) { unlink($ruta_destino); }
        return ['success' => false, 'message' => 'Error SQL: ' . $e->getMessage()];
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