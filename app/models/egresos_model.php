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
    (SELECT 
        c.id, 
        c.folio, 
        c.fecha_compra AS fecha, 
        c.proveedor AS entidad, 
        c.total, 
        'compra' AS tipo, 
        c.tiene_faltantes, 
        c.documento_url,
        /* Calculamos el total de piezas pendientes desde la tabla de faltantes */
        IFNULL((SELECT SUM(cantidad_pendiente) FROM faltantes_ingreso WHERE compra_id = c.id), 0) AS piezas_faltantes
     FROM compras c
     WHERE c.fecha_compra BETWEEN ? AND ?)
    
    UNION ALL
    
    (SELECT 
        id, 
        folio, 
        fecha_gasto AS fecha, 
        beneficiario AS entidad, 
        total, 
        'gasto' AS tipo, 
        0 AS tiene_faltantes, 
        documento_url,
        0 AS piezas_faltantes /* Los gastos no tienen inventario, por lo tanto 0 faltantes */
     FROM gastos 
     WHERE fecha_gasto BETWEEN ? AND ?)
    
    ORDER BY fecha DESC, id DESC";

    $stmt = $this->db->prepare($sql);
    $stmt->bind_param("ssss", $desde, $hasta, $desde, $hasta);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

    /**
     * 2. REGISTRA UN GASTO (CON EVIDENCIA Y DESCRIPCIÓN)
     * Según tu tabla 'gastos' y 'detalle_gasto'
     */
// CONSULTA 1: Obtener productos para el buscador
    public function buscarProductos($termino) {
        $query = "SELECT 
                    id, 
                    nombre, 
                    sku, 
                    unidad_medida,    -- Ej: 'Pieza'
                    unidad_reporte,   -- Ej: 'Millar'
                    factor_conversion -- Ej: 1000
                  FROM productos 
                  WHERE (nombre LIKE ? OR sku LIKE ?) 
                  AND estado = 1 
                  ";
                  
        $stmt = $this->db->prepare($query);
        $likeTerm = "%$termino%";
        $stmt->bind_param("ss", $likeTerm, $likeTerm);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    /**
 * Obtiene todos los productos activos para llenar el selector del modal
 */
public function listarProductos() {
    $query = "SELECT 
                id, 
                nombre, 
                sku, 
                unidad_medida, 
                unidad_reporte, 
                factor_conversion 
              FROM productos 
              WHERE estado = 1 
              ORDER BY nombre ASC";
              
    $res = $this->db->query($query);
    if (!$res) return [];
    return $res->fetch_all(MYSQLI_ASSOC);
}
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
public function obtenerDetalleMovimientosFisicos($compra_id) {
    $sql = "SELECT 
                dc.producto_id,
                p.sku,
                p.nombre as producto_nombre,
                p.unidad_reporte, -- Ejemplo: Tonelada
                p.unidad_medida, -- Ejemplo: Bultos/Pzas
                dc.factor_conversion, -- Ejemplo: 20
                dc.cantidad as total_pedido,
                /* Traemos el desglose de movimientos: Almacén y cuánto entró ahí */
                (SELECT GROUP_CONCAT(CONCAT(a.nombre, ': ', m.cantidad) SEPARATOR ' | ')
                 FROM movimientos m 
                 JOIN almacenes a ON m.almacen_destino_id = a.id
                 WHERE m.referencia_id = dc.compra_id 
                 AND m.producto_id = dc.producto_id 
                 AND m.tipo = 'entrada') as desglose_entradas,
                /* Suma total recibida */
                (SELECT IFNULL(SUM(m.cantidad), 0) 
                 FROM movimientos m 
                 WHERE m.referencia_id = dc.compra_id 
                 AND m.producto_id = dc.producto_id 
                 AND m.tipo = 'entrada') as total_recibido,
                /* Faltante */
                IFNULL((SELECT f.cantidad_pendiente FROM faltantes_ingreso f 
                        WHERE f.compra_id = dc.compra_id 
                        AND f.producto_id = dc.producto_id), 0) as faltante
            FROM detalle_compra dc
            JOIN productos p ON dc.producto_id = p.id
            WHERE dc.compra_id = ?";
    
    $stmt = $this->db->prepare($sql);
    $stmt->bind_param("i", $compra_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
public function obtenerDetalleCompleto($tipo, $id) {
    $response = ['tipo_documento' => $tipo];

    if ($tipo === 'compra') {
        // CABECERA COMPRAS
        $sql = "SELECT c.*, a.nombre as almacen_nombre, u.nombre as usuario_nombre 
                FROM compras c 
                JOIN almacenes a ON c.almacen_id = a.id 
                JOIN usuarios u ON c.usuario_registra_id = u.id 
                WHERE c.id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $response['cabecera'] = $stmt->get_result()->fetch_assoc();

        // DETALLE COMPRAS CON TRAZABILIDAD (9 y 9 en Rancho...)
        $sqlDet = "SELECT dc.*, p.sku, p.nombre as producto_nombre, p.unidad_medida, p.unidad_reporte, p.factor_conversion as factor_prod,
                    (SELECT GROUP_CONCAT(CONCAT(a.nombre, ' [', m.cantidad, ']') SEPARATOR '||')
                     FROM movimientos m 
                     JOIN almacenes a ON m.almacen_destino_id = a.id
                     WHERE m.referencia_id = dc.compra_id AND m.producto_id = dc.producto_id AND m.tipo = 'entrada') as desglose_movimientos,
                    (SELECT IFNULL(SUM(m.cantidad), 0) FROM movimientos m 
                     WHERE m.referencia_id = dc.compra_id AND m.producto_id = dc.producto_id AND m.tipo = 'entrada') as cantidad_recibida
                   FROM detalle_compra dc
                   JOIN productos p ON dc.producto_id = p.id
                   WHERE dc.compra_id = ?";
        $stmtDet = $this->db->prepare($sqlDet);
        $stmtDet->bind_param("i", $id);
        $stmtDet->execute();
        $response['items'] = $stmtDet->get_result()->fetch_all(MYSQLI_ASSOC);

    } else {
        // CABECERA GASTOS
        $sql = "SELECT g.*, a.nombre as almacen_nombre, u.nombre as usuario_nombre 
                FROM gastos g 
                JOIN almacenes a ON g.almacen_id = a.id 
                JOIN usuarios u ON g.usuario_registra_id = u.id 
                WHERE g.id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $response['cabecera'] = $stmt->get_result()->fetch_assoc();

        // DETALLE GASTOS (Simple descripción)
        $sqlDet = "SELECT * FROM detalle_gasto WHERE gasto_id = ?";
        $stmtDet = $this->db->prepare($sqlDet);
        $stmtDet->bind_param("i", $id);
        $stmtDet->execute();
        $response['items'] = $stmtDet->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    return $response;
}
    
}