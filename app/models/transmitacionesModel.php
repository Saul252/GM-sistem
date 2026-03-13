<?php

class TransmutacionesModel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    /**
     * Motor principal de la transmutación
     */
    public function registrarTransmutacion($datos) {
        $this->db->begin_transaction();
        try {
            // 1. CABECERA
            $sqlTrans = "INSERT INTO transmutaciones (usuario_id, almacen_id, observaciones) VALUES (?, ?, ?)";
            $stmtTrans = $this->db->prepare($sqlTrans);
            $stmtTrans->bind_param("iis", $datos['usuario_id'], $datos['almacen_id'], $datos['observaciones']);
            $stmtTrans->execute();
            $transmutacion_id = $this->db->insert_id;

            // Obtener costo del producto origen para prorratear
            $costo_origen = $this->obtenerCostoLote($datos['lote_origen_id']);

            // --- PARTE A: SALIDA (ORIGEN) ---
            $obsSalida = "Salida por transmutación #" . $transmutacion_id;
            $mov_salida_id = $this->registrarMovimientoKardex($datos['producto_origen_id'], 'salida', $datos['cant_origen'], $datos['almacen_id'], $datos['usuario_id'], $datos['responsable'], $obsSalida);

            $this->insertarDetalle($transmutacion_id, $mov_salida_id, 'salida', $datos['producto_origen_id'], $datos['lote_origen_id'], $datos['cant_origen'], $costo_origen);
            $this->actualizarStockSustractivo($datos['producto_origen_id'], $datos['almacen_id'], $datos['lote_origen_id'], $datos['cant_origen']);

            // --- PARTE B: ENTRADA (DESTINO) ---
            // Calcular costo prorrateado: (Costo Total Origen) / (Cantidad Real Destino)
            $costo_destino = ($costo_origen * $datos['cant_origen']) / $datos['cant_destino'];

            // Decidir si crear lote nuevo o usar uno existente
            $lote_destino_id = $this->obtenerOCrearLoteDestino($datos, $costo_destino);

            $obsEntrada = "Entrada por transmutación #" . $transmutacion_id . " (Ex-" . $datos['lote_origen_id'] . ")";
            $mov_entrada_id = $this->registrarMovimientoKardex($datos['producto_destino_id'], 'entrada', $datos['cant_destino'], $datos['almacen_id'], $datos['usuario_id'], $datos['responsable'], $obsEntrada);

            $this->insertarDetalle($transmutacion_id, $mov_entrada_id, 'entrada', $datos['producto_destino_id'], $lote_destino_id, $datos['cant_destino'], $costo_destino);
            $this->actualizarStockAditivo($datos['producto_destino_id'], $datos['almacen_id'], $lote_destino_id, $datos['cant_destino']);

            $this->db->commit();
            return ['status' => 'success', 'message' => 'Transmutación procesada correctamente', 'id' => $transmutacion_id];

        } catch (Exception $e) {
            $this->db->rollback();
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    /**
     * Busca destinos permitidos según la tabla de configuración
     */
    public function obtenerDestinosCompatibles($producto_origen_id) {
        $sql = "SELECT p.id, p.nombre, p.sku, c.rendimiento_teorico 
                FROM config_transmutaciones c
                INNER JOIN productos p ON c.producto_destino_id = p.id
                WHERE c.producto_origen_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $producto_origen_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // --- MÉTODOS PRIVADOS DE APOYO ---

    private function registrarMovimientoKardex($p_id, $tipo, $cant, $a_id, $u_id, $resp, $obs) {
        $colAlmacen = ($tipo == 'salida') ? 'almacen_origen_id' : 'almacen_destino_id';
        $sql = "INSERT INTO movimientos (producto_id, tipo, cantidad, $colAlmacen, usuario_registra_id, responsable_movimiento, observaciones) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("idiisss", $p_id, $tipo, $cant, $a_id, $u_id, $resp, $obs);
        $stmt->execute();
        return $this->db->insert_id;
    }

    private function insertarDetalle($t_id, $m_id, $tipo, $p_id, $l_id, $cant, $costo) {
        $sql = "INSERT INTO transmutacion_detalle (transmutacion_id, movimiento_id, tipo, producto_id, lote_id, cantidad, costo_unitario_historico) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("iiididd", $t_id, $m_id, $tipo, $p_id, $l_id, $cant, $costo);
        $stmt->execute();
    }

    private function obtenerOCrearLoteDestino($datos, $costo_destino) {
        if (!empty($datos['lote_destino_id']) && $datos['lote_destino_id'] > 0) {
            return $datos['lote_destino_id'];
        }

        $nuevoCodigo = "TR-" . date('Ymd-His');
        $sql = "INSERT INTO lotes_stock (producto_id, almacen_id, codigo_lote, cantidad_inicial, cantidad_actual, precio_compra_unitario, estado_lote) 
                VALUES (?, ?, ?, ?, ?, ?, 'activo')";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("iisddd", $datos['producto_destino_id'], $datos['almacen_id'], $nuevoCodigo, $datos['cant_destino'], $datos['cant_destino'], $costo_destino);
        $stmt->execute();
        return $this->db->insert_id;
    }

    private function actualizarStockSustractivo($p_id, $a_id, $l_id, $cant) {
        $this->db->query("UPDATE lotes_stock SET cantidad_actual = cantidad_actual - $cant, estado_lote = IF(cantidad_actual - $cant <= 0, 'agotado', estado_lote) WHERE id = $l_id");
        $this->db->query("UPDATE inventario SET stock = stock - $cant WHERE almacen_id = $a_id AND producto_id = $p_id");
    }

    private function actualizarStockAditivo($p_id, $a_id, $l_id, $cant) {
        $this->db->query("UPDATE lotes_stock SET cantidad_actual = cantidad_actual + $cant, estado_lote = 'activo' WHERE id = $l_id");
        $this->db->query("UPDATE inventario SET stock = stock + $cant WHERE almacen_id = $a_id AND producto_id = $p_id");
    }

    public function obtenerCostoLote($l_id) {
        $res = $this->db->query("SELECT precio_compra_unitario FROM lotes_stock WHERE id = $l_id");
        $row = $res->fetch_assoc();
        return $row['precio_compra_unitario'] ?? 0;
    }
}