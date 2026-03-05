<?php
class EgresoModel {
    private $db;

    public function __construct($conexion) {
        $this->db = $conexion;
    }
public function obtenerAlmacenesActivos() {
    $sql = "SELECT id, nombre FROM almacenes WHERE activo = 1 ORDER BY nombre ASC";
    $res = $this->db->query($sql);
    return $res->fetch_all(MYSQLI_ASSOC);
}
    /**
     * 1. OBTIENE TODO EL FLUJO (COMPRAS + GASTOS)
     * Usa un UNION para juntar ambas tablas en una sola lista para la tabla principal
     */
    public function obtenerTodosLosEgresos($desde, $hasta, $usuario_id = null) {
        $sql = "
            (SELECT id, folio, fecha_compra AS fecha, proveedor AS entidad, total, 'compra' AS tipo, tiene_faltantes, documento_url 
             FROM compras 
             WHERE fecha_compra BETWEEN ? AND ?)
            UNION ALL
            (SELECT id, folio, fecha_gasto AS fecha, beneficiario AS entidad, total, 'gasto' AS tipo, 0 AS tiene_faltantes, documento_url 
             FROM gastos 
             WHERE fecha_gasto BETWEEN ? AND ?)
            ORDER BY fecha DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ssss", $desde, $hasta, $desde, $hasta);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * 2. REGISTRA UN GASTO (CON EVIDENCIA Y DESCRIPCIÓN)
     * Según tu tabla 'gastos' y 'detalle_gasto'
     */
public function registrarGasto($cabecera, $descripciones, $cantidades, $precios) {
    // 1. Iniciar transacción
    $this->db->begin_transaction();
    
    try {
        // 2. Insertar Cabecera
        $sql = "INSERT INTO gastos (folio, fecha_gasto, almacen_id, usuario_registra_id, beneficiario, metodo_pago, total, documento_url, observaciones, estado) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'pagado')";
        
        $stmt = $this->db->prepare($sql);
        if (!$stmt) throw new Exception("Error en Prepare Cabecera: " . $this->db->error);

        $stmt->bind_param("ssiissdss", 
            $cabecera['folio'], $cabecera['fecha'], $cabecera['almacen_id'], 
            $cabecera['usuario_id'], $cabecera['beneficiario'], $cabecera['metodo_pago'], 
            $cabecera['total'], $cabecera['documento_url'], $cabecera['observaciones']
        );
        
        if (!$stmt->execute()) {
            throw new Exception("Error al ejecutar Cabecera: " . $stmt->error);
        }

        $gasto_id = $this->db->insert_id;

        // 3. Insertar Detalles
        $sqlDet = "INSERT INTO detalle_gasto (gasto_id, descripcion, cantidad, precio_unitario, subtotal) VALUES (?, ?, ?, ?, ?)";
        $stmtD = $this->db->prepare($sqlDet);
        if (!$stmtD) throw new Exception("Error en Prepare Detalle: " . $this->db->error);

        foreach ($descripciones as $i => $desc) {
            if (empty($desc)) continue; // Evitar filas vacías
            
            $cant = floatval($cantidades[$i]);
            $prec = floatval($precios[$i]);
            $subt = $cant * $prec;

            $stmtD->bind_param("isddd", $gasto_id, $desc, $cant, $prec, $subt);
            if (!$stmtD->execute()) {
                throw new Exception("Error al ejecutar Detalle en fila $i: " . $stmtD->error);
            }
        }

        // 4. EL PASO FINAL: Si llegamos aquí, guardamos de verdad
        if ($this->db->commit()) {
            return true;
        } else {
            throw new Exception("Error al hacer Commit en la base de datos.");
        }

    } catch (Exception $e) {
        $this->db->rollback();
        // ESTO ES VITAL: Mandamos el error de vuelta al controlador
        throw $e; 
    }
}
    /**
     * 3. REGISTRA UNA COMPRA (AFECTA INVENTARIO)
     * Según tu tabla 'compras' y 'detalle_compra'
     */
    public function registrarCompra($cabecera, $productos) {
        $this->db->begin_transaction();
        try {
            // 1. Insertar Cabecera Compra
            $sqlCompra = "INSERT INTO compras (folio, proveedor, fecha_compra, almacen_id, total, usuario_registra_id, estado) 
                          VALUES (?, ?, ?, ?, ?, ?, 'confirmada')";
            $stmt = $this->db->prepare($sqlCompra);
            $stmt->bind_param("sssdis", $cabecera['folio'], $cabecera['proveedor'], $cabecera['fecha'], $cabecera['almacen_id'], $cabecera['total'], $cabecera['usuario_id']);
            $stmt->execute();
            $compra_id = $this->db->insert_id;

            // 2. Detalle y Actualización de Stock
            $sqlDetalle = "INSERT INTO detalle_compra (compra_id, producto_id, cantidad, precio_unitario, subtotal) VALUES (?, ?, ?, ?, ?)";
            $sqlStock = "UPDATE inventario SET stock = stock + ? WHERE producto_id = ? AND almacen_id = ?";
            
            $stmtD = $this->db->prepare($sqlDetalle);
            $stmtS = $this->db->prepare($sqlStock);

            foreach ($productos as $p) {
                // Guardar detalle
                $stmtD->bind_param("iiddd", $compra_id, $p['id'], $p['cantidad'], $p['precio'], $p['subtotal']);
                $stmtD->execute();

                // Afectar Inventario (Kardex/Stock)
                $stmtS->bind_param("dii", $p['cantidad'], $p['id'], $cabecera['almacen_id']);
                $stmtS->execute();
            }

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollback();
            return false;
        }
    }
    
}