<?php

class tesoreriaModel
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * CREATE: Registra un nuevo movimiento de capital con desgloses y destinos.
     */
    public function registrarMovimiento($datos)
    {
        try {
            $this->db->beginTransaction();

            $sql = "INSERT INTO historial_capital (
                        categoria_id, 
                        almacen_origen_id, 
                        almacen_destino_id, 
                        caja_fuerte_destino_id,
                        banco_destino_id,
                        monto, 
                        monto_efectivo,
                        monto_tarjeta,
                        monto_transferencia,
                        metodo_pago, 
                        usuario_registro_id, 
                        usuario_autoriza_id,
                        concepto, 
                        referencia_folio, 
                        fecha_movimiento
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";

            $stmt = $this->db->prepare($sql);

            // Validar destinos para enviar NULL si están vacíos
            $almDestino = !empty($datos['almacen_destino_id']) ? $datos['almacen_destino_id'] : null;
            $cajaFuerte = !empty($datos['caja_fuerte_id']) ? $datos['caja_fuerte_id'] : null;
            $bancoDestino = !empty($datos['banco_id']) ? $datos['banco_id'] : null;

            $stmt->execute([
                $datos['categoria_id'],
                $datos['almacen_id'],
                $almDestino,
                $cajaFuerte,
                $bancoDestino,
                $datos['monto'],
                $datos['monto_efectivo'] ?? 0,
                $datos['monto_tarjeta'] ?? 0,
                $datos['monto_transferencia'] ?? 0,
                $datos['metodo_pago'] ?? 'Efectivo',
                $datos['usuario_id'],
                $datos['autoriza_id'] ?? null,
                $datos['concepto'],
                $datos['referencia'] ?? null
            ]);

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Error en tesoreriaModel::registrarMovimiento -> " . $e->getMessage());
            return false;
        }
    }

    /**
     * READ: Lista los movimientos con filtros de almacén y fecha.
     */


    /**
     * UPDATE: Cancela un movimiento (Estatus 0) en lugar de eliminarlo.
     */
    public function cancelarMovimiento($id, $usuario_id)
    {
        try {
            $sql = "UPDATE historial_capital SET estatus = 0 WHERE id = ?";
            return $this->db->prepare($sql)->execute([$id]);
        } catch (Exception $e) {
            error_log("Error al cancelar movimiento: " . $e->getMessage());
            return false;
        }
    }

    /**
     * SALDO: Obtiene el saldo real actual de un almacén.
     */
    public function obtenerSaldoActual($almacen_id)
    {
        $sql = "SELECT 
                    SUM(CASE WHEN c.tipo_operacion = 'entrada' THEN h.monto ELSE 0 END) - 
                    SUM(CASE WHEN c.tipo_operacion = 'salida' THEN h.monto ELSE 0 END) as saldo_real
                FROM historial_capital h
                INNER JOIN capital_categorias c ON h.categoria_id = c.id
                WHERE h.almacen_origen_id = ? AND h.estatus = 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$almacen_id]);
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        return $res['saldo_real'] ?? 0;
    }

    /**
     * CATÁLOGOS: Obtener categorías, cajas fuertes y bancos para formularios.
     */
    public function getCategorias()
    {
        // Usando la lógica de MySQLi para ser compatible con el resto de tu código
        $sql = "SELECT * FROM capital_categorias WHERE estatus = 1";
        $result = $this->db->query($sql);

        if ($result) {
            return $result->fetch_all(MYSQLI_ASSOC);
        }

        return [];
    }
    /**
     * Obtiene las cajas fuertes. Si se pasa un almacen_id, filtra por este.
     * Útil para que un almacén solo vea sus propias cajas.
     */
    /**
     * Obtiene las cajas fuertes vinculadas forzosamente al almacén seleccionado.
     * @param int $almacen_id ID del almacén (0 para ver todas las de la empresa)
     */
    public function getCajasFuertes($almacen_id)
    {
        // Forzamos el casteo a entero para seguridad
        $id = intval($almacen_id);

        $sql = "SELECT id, nombre, almacen_id FROM cajas_fuertes WHERE estatus = 1";

        // Si el almacén es mayor a 0, filtramos estrictamente. 
        // Si es 0, el admin puede ver todas las cajas de todos los almacenes.
        if ($id > 0) {
            $sql .= " AND almacen_id = $id";
        }

        $result = $this->db->query($sql);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    /**
     * Obtiene las cuentas bancarias vinculadas forzosamente al almacén seleccionado.
     * @param int $almacen_id ID del almacén (0 para ver todos los bancos de la empresa)
     */
    public function getCuentasBancarias($almacen_id)
    {
        $id = intval($almacen_id);

        $sql = "SELECT id_cuenta, nombre_cuenta, id_almacen 
            FROM cuentas_bancarias 
            WHERE estatus = 1 AND tipo_cuenta = 'Banco'";

        // Aplicamos la misma lógica: 0 = Global/Admin, >0 = Sucursal específica
        if ($id > 0) {
            $sql .= " AND id_almacen = $id";
        }

        $result = $this->db->query($sql);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    /**
     * APERTURA: Registro automático desde el corte.
     */
    public function registrarAperturaDesdeCierre($almacen_id, $usuario_id, $desglose, $fecha_corte)
    {
        $monto_total = array_sum($desglose);
        $fecha_apertura = date('Y-m-d', strtotime($fecha_corte . ' +1 day')) . ' 00:00:01';
        $concepto = "Saldo inicial automático (Corte: " . $fecha_corte . ")";

        $sql = "INSERT INTO historial_capital (
                categoria_id, almacen_origen_id, monto, monto_efectivo, 
                monto_tarjeta, monto_transferencia, usuario_registro_id, 
                concepto, fecha_movimiento
            ) VALUES (1, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            error_log("Error preparando registrarAperturaDesdeCierre: " . $this->db->error);
            return false;
        }

        $efectivo = $desglose['efectivo'] ?? 0;
        $tarjeta = $desglose['tarjeta'] ?? 0;
        $transferencia = $desglose['transferencia'] ?? 0;

        $stmt->bind_param(
            "iddddiss",
            $almacen_id,
            $monto_total,
            $efectivo,
            $tarjeta,
            $transferencia,
            $usuario_id,
            $concepto,
            $fecha_apertura
        );

        if (!$stmt->execute()) {
            error_log("Error ejecutando registrarAperturaDesdeCierre: " . $stmt->error);
            return false;
        }

        $idInsertado = (int) $this->db->insert_id;
        $stmt->close();

        return $idInsertado > 0 ? $idInsertado : false;
    }
    // ================================================================
    // CRUD HISTORIAL CAJA FUERTE (SOLO EFECTIVO)
    // ================================================================

    public function crearMovimientoCajaFuerte(array $data): array
    {
        $caja_fuerte_id = intval($data['caja_fuerte_id'] ?? 0);
        $almacen_id = intval($data['almacen_id'] ?? 0);
        $usuario_id = intval($data['usuario_id'] ?? 0);
        $tipo_mov = $data['tipo_movimiento'] ?? '';
        $monto = round(floatval($data['monto'] ?? 0), 2);
        $concepto = trim($data['concepto'] ?? '');
        $referencia_id = !empty($data['referencia_id']) ? intval($data['referencia_id']) : null;
        $signo_cfg = isset($data['signo']) ? intval($data['signo']) : 1;

        $tipos_validos = ['entrada', 'salida', 'traspaso', 'apertura', 'cierre', 'ajuste'];

        if ($caja_fuerte_id <= 0 || $almacen_id <= 0 || $usuario_id <= 0) {
            throw new Exception("Caja fuerte, almacén o usuario no válidos.");
        }
        if (!in_array($tipo_mov, $tipos_validos, true)) {
            throw new Exception("Tipo de movimiento no válido.");
        }
        if ($monto <= 0 || $concepto === '') {
            throw new Exception("Monto o concepto no válidos.");
        }

        $fecha_movimiento = !empty($data['fecha_movimiento'])
            ? date('Y-m-d H:i:s', strtotime($data['fecha_movimiento']))
            : date('Y-m-d H:i:s');

        $this->db->begin_transaction();

        try {
            $stmtLock = $this->db->prepare("
                SELECT id, Saldo, estatus
                FROM cajas_fuertes
                WHERE id = ?
                FOR UPDATE
            ");
            if (!$stmtLock) {
                throw new Exception("Error preparando consulta de caja fuerte: " . $this->db->error);
            }

            $stmtLock->bind_param("i", $caja_fuerte_id);
            $stmtLock->execute();
            $caja = $stmtLock->get_result()->fetch_assoc();

            if (!$caja) {
                throw new Exception("La caja fuerte #{$caja_fuerte_id} no existe.");
            }

            $saldo_anterior = round(floatval($caja['Saldo']), 2);
            $operador = in_array($tipo_mov, ['salida', 'traspaso'], true) ? -1 : 1;
            if ($tipo_mov === 'ajuste' && $signo_cfg === -1) {
                $operador = -1;
            }

            $ajuste_saldo = round($monto * $operador, 2);
            $saldo_nuevo = round($saldo_anterior + $ajuste_saldo, 2);

            if ($saldo_nuevo < 0) {
                throw new Exception("Efectivo insuficiente en caja fuerte #{$caja_fuerte_id}.");
            }

            $stmtUpd = $this->db->prepare("UPDATE cajas_fuertes SET Saldo = ? WHERE id = ?");
            if (!$stmtUpd) {
                throw new Exception("Error preparando actualización de saldo: " . $this->db->error);
            }
            $stmtUpd->bind_param("di", $saldo_nuevo, $caja_fuerte_id);
            if (!$stmtUpd->execute()) {
                throw new Exception("Error actualizando saldo de caja fuerte: " . $stmtUpd->error);
            }

            $stmtIns = $this->db->prepare("
                INSERT INTO historial_caja_fuerte (
                    caja_fuerte_id,
                    almacen_id,
                    usuario_id,
                    tipo_movimiento,
                    monto,
                    ajuste_saldo,
                    saldo_anterior,
                    saldo_nuevo,
                    concepto,
                    referencia_id,
                    fecha_movimiento
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            if (!$stmtIns) {
                throw new Exception("Error preparando inserción de historial: " . $this->db->error);
            }

            $stmtIns->bind_param(
                "iiisddddsis",
                $caja_fuerte_id,
                $almacen_id,
                $usuario_id,
                $tipo_mov,
                $monto,
                $ajuste_saldo,
                $saldo_anterior,
                $saldo_nuevo,
                $concepto,
                $referencia_id,
                $fecha_movimiento
            );

            if (!$stmtIns->execute()) {
                throw new Exception("Error guardando movimiento en historial: " . $stmtIns->error);
            }

            $id = $this->db->insert_id;
            $this->db->commit();

            return [
                'id' => $id,
                'caja_fuerte_id' => $caja_fuerte_id,
                'saldo_anterior' => $saldo_anterior,
                'ajuste_saldo' => $ajuste_saldo,
                'saldo_nuevo' => $saldo_nuevo,
                'fecha_movimiento' => $fecha_movimiento,
            ];
        } catch (Throwable $e) {
            $this->db->rollback();
            throw $e;
        }
    }

    public function obtenerMovimientoCajaFuerte(int $id): ?array
    {
        $stmt = $this->db->prepare("
            SELECT
                h.id,
                h.caja_fuerte_id,
                cf.nombre AS caja_fuerte_nombre,
                h.almacen_id,
                h.usuario_id,
                h.tipo_movimiento,
                h.monto,
                h.ajuste_saldo,
                h.saldo_anterior,
                h.saldo_nuevo,
                h.concepto,
                h.referencia_id,
                h.fecha_movimiento
            FROM historial_caja_fuerte h
            INNER JOIN cajas_fuertes cf ON cf.id = h.caja_fuerte_id
            WHERE h.id = ?
            LIMIT 1
        ");
        if (!$stmt) {
            throw new Exception("Error preparando consulta: " . $this->db->error);
        }

        $stmt->bind_param("i", $id);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        return $row ?: null;
    }

    public function listarMovimientosCajaFuerte(array $filtros = []): array
    {
        $sql = "
            SELECT
                h.id,
                h.caja_fuerte_id,
                cf.nombre AS caja_fuerte_nombre,
                h.almacen_id,
                h.usuario_id,
                h.tipo_movimiento,
                h.monto,
                h.ajuste_saldo,
                h.saldo_anterior,
                h.saldo_nuevo,
                h.concepto,
                h.referencia_id,
                h.fecha_movimiento
            FROM historial_caja_fuerte h
            INNER JOIN cajas_fuertes cf ON cf.id = h.caja_fuerte_id
            WHERE 1 = 1
        ";

        $types = "";
        $params = [];

        if (!empty($filtros['caja_fuerte_id'])) {
            $sql .= " AND h.caja_fuerte_id = ?";
            $types .= "i";
            $params[] = intval($filtros['caja_fuerte_id']);
        }
        if (!empty($filtros['almacen_id'])) {
            $sql .= " AND h.almacen_id = ?";
            $types .= "i";
            $params[] = intval($filtros['almacen_id']);
        }
        if (!empty($filtros['usuario_id'])) {
            $sql .= " AND h.usuario_id = ?";
            $types .= "i";
            $params[] = intval($filtros['usuario_id']);
        }
        if (!empty($filtros['tipo_movimiento'])) {
            $sql .= " AND h.tipo_movimiento = ?";
            $types .= "s";
            $params[] = $filtros['tipo_movimiento'];
        }
        if (!empty($filtros['fecha_desde'])) {
            $sql .= " AND h.fecha_movimiento >= ?";
            $types .= "s";
            $params[] = $filtros['fecha_desde'];
        }
        if (!empty($filtros['fecha_hasta'])) {
            $sql .= " AND h.fecha_movimiento <= ?";
            $types .= "s";
            $params[] = $filtros['fecha_hasta'];
        }

        $sql .= " ORDER BY h.fecha_movimiento DESC, h.id DESC";

        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            throw new Exception("Error preparando listado: " . $this->db->error);
        }

        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function actualizarConceptoCajaFuerte(int $id, string $nuevoConcepto): bool
    {
        $nuevoConcepto = trim($nuevoConcepto);
        if ($id <= 0 || $nuevoConcepto === '') {
            throw new Exception("ID o concepto no válido.");
        }

        $stmt = $this->db->prepare("
            UPDATE historial_caja_fuerte
            SET concepto = ?
            WHERE id = ?
        ");
        if (!$stmt) {
            throw new Exception("Error preparando actualización: " . $this->db->error);
        }

        $stmt->bind_param("si", $nuevoConcepto, $id);
        $stmt->execute();
        return $stmt->affected_rows > 0;
    }
    /**
     * Obtiene los datos de un movimiento de historial_capital por su ID.
     *
     * @param int $movimiento_id
     * @return array|null
     */
    public function obtenerMovimientoCapital(int $movimiento_id): ?array
    {
        if ($movimiento_id <= 0) {
            return null;
        }

        $sql = "SELECT
                h.*,
                ur.nombre AS usuario_que_registro
            FROM historial_capital h
            LEFT JOIN usuarios ur ON ur.id = h.usuario_registro_id
            WHERE h.id = ?
            LIMIT 1";

        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            error_log("Error preparando obtenerMovimientoCapital: " . $this->db->error);
            return null;
        }

        $stmt->bind_param("i", $movimiento_id);
        if (!$stmt->execute()) {
            error_log("Error ejecutando obtenerMovimientoCapital: " . $stmt->error);
            return null;
        }

        $row = $stmt->get_result()->fetch_assoc();
        return $row ?: null;
    }
    public function cancelarMovimientoCajaFuerte(int $movimientoId, int $usuarioId, string $motivo = ''): array
    {
        $mov = $this->obtenerMovimientoCajaFuerte($movimientoId);
        if (!$mov) {
            throw new Exception("El movimiento #{$movimientoId} no existe.");
        }

        $ajusteOriginal = round(floatval($mov['ajuste_saldo']), 2);
        if ($ajusteOriginal == 0.0) {
            throw new Exception("El movimiento #{$movimientoId} no tiene ajuste de saldo.");
        }

        $montoReversa = abs($ajusteOriginal);
        $signoReversa = $ajusteOriginal > 0 ? -1 : 1;
        $conceptoReversa = "Cancelación de movimiento #{$movimientoId}" .
            ($motivo !== '' ? " - Motivo: " . trim($motivo) : "");

        return $this->crearMovimientoCajaFuerte([
            'caja_fuerte_id' => intval($mov['caja_fuerte_id']),
            'almacen_id' => intval($mov['almacen_id']),
            'usuario_id' => $usuarioId,
            'tipo_movimiento' => 'ajuste',
            'signo' => $signoReversa,
            'monto' => $montoReversa,
            'concepto' => $conceptoReversa,
            'referencia_id' => $movimientoId,
        ]);
    }
}