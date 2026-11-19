<?php

class TransmutacionesModel
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }


    // --- MÉTODOS DE APOYO (REVISADOS) ---

    public function registrarTransmutacion($datos)
    {
        $this->db->begin_transaction();
        try {
            // 0. Obtener costo base del lote de origen
            $costo_origen = $this->obtenerCostoLote($datos['lote_origen_id']);

            // 1. INSERTAR CABECERA
            $sqlC = "INSERT INTO `transmutaciones` (`usuario_id`, `almacen_id`, `observaciones`) VALUES (?, ?, ?)";
            $stmtC = $this->db->prepare($sqlC);
            $stmtC->bind_param("iis", $datos['usuario_id'], $datos['almacen_id'], $datos['observaciones']);
            $stmtC->execute();
            $t_id = $this->db->insert_id;

            // 2. PROCESAR SALIDA (Materia Prima)
            $m_salida = $this->registrarMovimientoKardex(
                $datos['producto_origen_id'],
                'salida',
                $datos['cant_origen'],
                $datos['almacen_id'],
                $datos['usuario_id'],
                $datos['responsable'],
                "Salida Transmutación #$t_id"
            );

            $this->insertarDetalle($t_id, $m_salida, 'salida', $datos['producto_origen_id'], $datos['lote_origen_id'], $datos['cant_origen'], $costo_origen);
            $this->actualizarStockSustractivo($datos['producto_origen_id'], $datos['almacen_id'], $datos['lote_origen_id'], $datos['cant_origen']);

            // 3. DETERMINAR COSTO Y LOTE DE ENTRADA (Producto Terminado)
            if (!empty($datos['lote_destino_id']) && $datos['lote_destino_id'] > 0) {
                $costo_destino = $this->obtenerCostoLote($datos['lote_destino_id']);
                $l_dest_id = $datos['lote_destino_id'];
            } else {
                // Prorrateo de costo: (Cantidad Origen * Costo Origen) / Cantidad Destino
                $costo_destino = ($datos['cant_destino'] > 0) ? (($datos['cant_origen'] * $costo_origen) / $datos['cant_destino']) : 0;
                $l_dest_id = $this->obtenerOCrearLoteDestino($datos, $costo_destino);
            }

            // 4. PROCESAR ENTRADA
            $m_entrada = $this->registrarMovimientoKardex(
                $datos['producto_destino_id'],
                'entrada',
                $datos['cant_destino'],
                $datos['almacen_id'],
                $datos['usuario_id'],
                $datos['responsable'],
                "Entrada Transmutación #$t_id"
            );

            $this->insertarDetalle($t_id, $m_entrada, 'entrada', $datos['producto_destino_id'], $l_dest_id, $datos['cant_destino'], $costo_destino);

            // Si el lote ya existía, sumamos. Si fue nuevo, el método crearLote ya puso el stock inicial.
            if (!empty($datos['lote_destino_id']) && $datos['lote_destino_id'] > 0) {
                $this->actualizarStockAditivo($datos['producto_destino_id'], $datos['almacen_id'], $l_dest_id, $datos['cant_destino']);
            } else {
                // Solo sumamos al inventario general porque el lote nuevo nace con su stock
                $this->actualizarInventarioGeneral($datos['producto_destino_id'], $datos['almacen_id'], $datos['cant_destino'], 'suma');
            }

            $this->db->commit();
            return ['status' => 'success', 't_id' => $t_id];

        } catch (Exception $e) {
            $this->db->rollback();
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    // --- MÉTODOS DE ESCRITURA ---

    private function registrarMovimientoKardex($p_id, $tipo, $cant, $a_id, $u_id, $resp, $obs)
    {
        $colAlmacen = ($tipo === 'salida') ? 'almacen_origen_id' : 'almacen_destino_id';
        $sql = "INSERT INTO `movimientos` 
                (`producto_id`, `tipo`, `cantidad`, `$colAlmacen`, `usuario_registra_id`, `origen_movimiento`, `observaciones`) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";

        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("isdiiss", $p_id, $tipo, $cant, $a_id, $u_id, $resp, $obs);
        if (!$stmt->execute())
            throw new Exception("Error en Kardex: " . $stmt->error);
        return $this->db->insert_id;
    }

    private function insertarDetalle($t_id, $m_id, $tipo, $p_id, $l_id, $cant, $costo)
    {
        // Se inserta en ambas columnas de costo para evitar errores de restricción NOT NULL
        $sql = "INSERT INTO `transmutacion_detalle` 
                (`transmutacion_id`, `movimiento_id`, `tipo`, `producto_id`, `lote_id`, `cantidad`, `costo_unitario`, `costo_unitario_historico`) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("iisiiddd", $t_id, $m_id, $tipo, $p_id, $l_id, $cant, $costo, $costo);
        if (!$stmt->execute())
            throw new Exception("Error en Detalle: " . $stmt->error);
    }

    private function actualizarStockSustractivo($p_id, $a_id, $l_id, $cant)
    {
        $stmt = $this->db->prepare("UPDATE lotes_stock SET cantidad_actual = cantidad_actual - ? WHERE id = ?");
        $stmt->bind_param("di", $cant, $l_id);
        $stmt->execute();

        $this->actualizarInventarioGeneral($p_id, $a_id, $cant, 'resta');
        $this->db->query("UPDATE lotes_stock SET estado_lote = 'agotado' WHERE id = $l_id AND cantidad_actual <= 0.001");
    }

    private function actualizarStockAditivo($p_id, $a_id, $l_id, $cant)
    {
        $stmt = $this->db->prepare("UPDATE lotes_stock SET cantidad_actual = cantidad_actual + ?, estado_lote = 'activo' WHERE id = ?");
        $stmt->bind_param("di", $cant, $l_id);
        $stmt->execute();

        $this->actualizarInventarioGeneral($p_id, $a_id, $cant, 'suma');
    }

    private function actualizarInventarioGeneral($p_id, $a_id, $cant, $operacion)
    {
        $signo = ($operacion === 'suma') ? "+" : "-";
        $sql = "UPDATE inventario SET stock = stock $signo ? WHERE almacen_id = ? AND producto_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("dii", $cant, $a_id, $p_id);
        if (!$stmt->execute())
            throw new Exception("Error al actualizar inventario global");
    }

    private function obtenerOCrearLoteDestino($datos, $costo)
    {
        $nuevoCod = "TR-" . date('ymd') . "-" . strtoupper(substr(uniqid(), -4));
        $sql = "INSERT INTO lotes_stock (producto_id, almacen_id, codigo_lote, cantidad_inicial, cantidad_actual, precio_compra_unitario, estado_lote) 
                VALUES (?, ?, ?, ?, ?, ?, 'activo')";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("iisddd", $datos['producto_destino_id'], $datos['almacen_id'], $nuevoCod, $datos['cant_destino'], $datos['cant_destino'], $costo);
        if (!$stmt->execute())
            throw new Exception("Error al crear Lote: " . $stmt->error);
        return $this->db->insert_id;
    }

    // --- MÉTODOS DE CONSULTA ---


    public function obtenerCostoLote($l_id)
    {
        $sql = "SELECT `precio_compra_unitario` FROM `lotes_stock` WHERE `id` = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $l_id);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();

        return (float) ($res['precio_compra_unitario'] ?? 0);
    }
    public function obtenerDestinosCompatibles($producto_origen_id)
    {
        $sql = "SELECT p.id, p.nombre,p.unidad_medida, p.sku, c.rendimiento_teorico 
                FROM config_transmutaciones c
                INNER JOIN productos p ON c.producto_destino_id = p.id
                WHERE c.producto_origen_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $producto_origen_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    public function agregarConfiguracion($almacen_id, $origen, $destino, $factor, $usuario_id, $notas)
    {
        // IMPORTANTE: Asegúrate de que tu tabla tenga el campo usuario_id y almacen_id
        $sql = "INSERT INTO config_transmutaciones 
            (almacen_id, producto_origen_id, producto_destino_id, rendimiento_teorico, usuario_id, notas) 
            VALUES (?, ?, ?, ?, ?, ?) 
            ON DUPLICATE KEY UPDATE 
            rendimiento_teorico = ?, 
            usuario_id = ?, 
            notas = ?";

        $stmt = $this->db->prepare($sql);

        // Tipos de datos: i (int), i (int), i (int), d (decimal), i (int), s (string)
        // Luego para el Update: d, i, s
        $stmt->bind_param(
            "iiididsis",
            $almacen_id,
            $origen,
            $destino,
            $factor,
            $usuario_id,
            $notas,
            $factor,
            $usuario_id,
            $notas
        );

        return $stmt->execute();

    }
    public function eliminarTransmutacion($transmutacion_id)
    {
        $t_id = (int) $transmutacion_id;
        if ($t_id <= 0) {
            return ['status' => 'error', 'message' => 'ID de transmutación no válido.'];
        }

        $this->db->begin_transaction();

        try {
            // 1. Verificar cabecera y bloquear fila
            $stmtCab = $this->db->prepare("SELECT `id`, `almacen_id` FROM `transmutaciones` WHERE `id` = ? FOR UPDATE");
            $stmtCab->bind_param("i", $t_id);
            $stmtCab->execute();
            $cabecera = $stmtCab->get_result()->fetch_assoc();

            if (!$cabecera) {
                throw new Exception("La transmutación #$t_id no existe.");
            }

            $almacen_id = (int) $cabecera['almacen_id'];

            // 2. Obtener detalles de la transmutación
            $stmtDet = $this->db->prepare("
            SELECT `id`, `movimiento_id`, `tipo`, `producto_id`, `lote_id`, `cantidad`
            FROM `transmutacion_detalle`
            WHERE `transmutacion_id` = ?
        ");
            $stmtDet->bind_param("i", $t_id);
            $stmtDet->execute();
            $detalles = $stmtDet->get_result()->fetch_all(MYSQLI_ASSOC);

            if (empty($detalles)) {
                throw new Exception("La transmutación #$t_id no tiene detalles para revertir.");
            }

            $movimiento_ids = [];
            $lotes_creados_a_eliminar = [];

            // 3. Revertir cada detalle ('salida' = producto origen devuelto; 'entrada' = producto destino revertido)
            foreach ($detalles as $det) {
                $tipo = $det['tipo']; // 'salida' o 'entrada'
                $producto_id = (int) $det['producto_id'];
                $lote_id = (int) $det['lote_id'];
                $cantidad = (float) $det['cantidad'];

                if (!empty($det['movimiento_id'])) {
                    $movimiento_ids[] = (int) $det['movimiento_id'];
                }

                if ($tipo === 'salida') {
                    // Se había quitado al convertir -> devolver a lotes_stock (reactivar si estaba agotado) y sumar a inventario
                    $this->revertirSalidaEnLoteEInventario($producto_id, $almacen_id, $lote_id, $cantidad);
                } elseif ($tipo === 'entrada') {
                    // Se había agregado al convertir -> restar de inventario y eliminar lote creado (o restar si existía)
                    $loteCreadoId = $this->revertirEntradaEnLoteEInventario($producto_id, $almacen_id, $lote_id, $cantidad);
                    if ($loteCreadoId > 0) {
                        $lotes_creados_a_eliminar[] = $loteCreadoId;
                    }
                } else {
                    throw new Exception("Tipo de detalle desconocido ('$tipo') en transmutación #$t_id.");
                }
            }

            $movimiento_ids = array_values(array_unique($movimiento_ids));

            // 4. Eliminar registros de kardex_movimientos_lotes vinculados a esta transmutación
            $obsLike = "%Transmutación #$t_id%";
            if (!empty($movimiento_ids)) {
                $placeholders = implode(',', array_fill(0, count($movimiento_ids), '?'));
                $types = str_repeat('i', count($movimiento_ids)) . 's';
                $params = array_merge($movimiento_ids, [$obsLike]);

                $stmtDelKml = $this->db->prepare("
                DELETE FROM `kardex_movimientos_lotes`
                WHERE `movimiento_id` IN ($placeholders) OR `observaciones` LIKE ?
            ");
                $stmtDelKml->bind_param($types, ...$params);
            } else {
                $stmtDelKml = $this->db->prepare("
                DELETE FROM `kardex_movimientos_lotes`
                WHERE `observaciones` LIKE ?
            ");
                $stmtDelKml->bind_param("s", $obsLike);
            }

            if (!$stmtDelKml->execute()) {
                throw new Exception("Error al eliminar movimientos en kardex_movimientos_lotes: " . $stmtDelKml->error);
            }

            // 5. Eliminar detalles de transmutacion_detalle (libera FK hacia movimientos / lotes_stock)
            $stmtDelDet = $this->db->prepare("DELETE FROM `transmutacion_detalle` WHERE `transmutacion_id` = ?");
            $stmtDelDet->bind_param("i", $t_id);
            if (!$stmtDelDet->execute()) {
                throw new Exception("Error al eliminar detalles de la transmutación: " . $stmtDelDet->error);
            }

            // 6. Eliminar movimientos de la tabla movimientos
            if (!empty($movimiento_ids)) {
                $placeholders = implode(',', array_fill(0, count($movimiento_ids), '?'));
                $types = str_repeat('i', count($movimiento_ids));

                $stmtDelMov = $this->db->prepare("DELETE FROM `movimientos` WHERE `id` IN ($placeholders)");
                $stmtDelMov->bind_param($types, ...$movimiento_ids);
                if (!$stmtDelMov->execute()) {
                    throw new Exception("Error al eliminar movimientos: " . $stmtDelMov->error);
                }
            }

            // 7. Eliminar los lotes creados por la transmutación de lotes_stock
            if (!empty($lotes_creados_a_eliminar)) {
                $lotes_creados_a_eliminar = array_values(array_unique($lotes_creados_a_eliminar));
                foreach ($lotes_creados_a_eliminar as $loteElimId) {
                    $stmtDelLote = $this->db->prepare("DELETE FROM `lotes_stock` WHERE `id` = ?");
                    $stmtDelLote->bind_param("i", $loteElimId);
                    if (!$stmtDelLote->execute()) {
                        throw new Exception("No se pudo eliminar el lote creado #$loteElimId de lotes_stock: " . $stmtDelLote->error);
                    }
                }
            }

            // 8. Eliminar cabecera de transmutaciones
            $stmtDelCab = $this->db->prepare("DELETE FROM `transmutaciones` WHERE `id` = ?");
            $stmtDelCab->bind_param("i", $t_id);
            if (!$stmtDelCab->execute()) {
                throw new Exception("Error al eliminar la cabecera de la transmutación: " . $stmtDelCab->error);
            }

            $this->db->commit();
            return [
                'status' => 'success',
                't_id' => $t_id,
                'message' => "Transmutación #$t_id revertida y eliminada correctamente."
            ];

        } catch (Exception $e) {
            $this->db->rollback();
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    /**
     * Devuelve al lote de origen (reactivándolo si estaba agotado) y suma al inventario general.
     */
    private function revertirSalidaEnLoteEInventario($producto_id, $almacen_id, $lote_id, $cantidad)
    {
        if ($lote_id > 0) {
            $stmtLock = $this->db->prepare("SELECT `cantidad_actual`, `estado_lote` FROM `lotes_stock` WHERE `id` = ? FOR UPDATE");
            $stmtLock->bind_param("i", $lote_id);
            $stmtLock->execute();
            $lote = $stmtLock->get_result()->fetch_assoc();

            if (!$lote) {
                throw new Exception("No se encontró el lote de origen #$lote_id en lotes_stock.");
            }

            $nuevaCantidad = (float) $lote['cantidad_actual'] + $cantidad;
            $nuevoEstado = ($nuevaCantidad > 0.001) ? 'activo' : 'agotado';

            $stmtUpdLote = $this->db->prepare("
            UPDATE `lotes_stock`
            SET `cantidad_actual` = ?, `estado_lote` = ?
            WHERE `id` = ?
        ");
            $stmtUpdLote->bind_param("dsi", $nuevaCantidad, $nuevoEstado, $lote_id);
            if (!$stmtUpdLote->execute()) {
                throw new Exception("Error al restaurar el lote #$lote_id: " . $stmtUpdLote->error);
            }
        }

        // Sumar lo que se había quitado al inventario general
        $this->actualizarInventarioGeneral($producto_id, $almacen_id, $cantidad, 'suma');
    }

    /**
     * Resta de inventario general lo que se había agregado al convertir;
     * si el lote fue creado en la transmutación, retorna su ID para eliminarlo de lotes_stock.
     */
    private function revertirEntradaEnLoteEInventario($producto_id, $almacen_id, $lote_id, $cantidad)
    {
        // 1. Restar de inventario general lo que se agregó
        $this->actualizarInventarioGeneral($producto_id, $almacen_id, $cantidad, 'resta');

        if ($lote_id <= 0) {
            return 0;
        }

        $stmtLock = $this->db->prepare("
        SELECT `id`, `codigo_lote`, `cantidad_inicial`, `cantidad_actual`
        FROM `lotes_stock`
        WHERE `id` = ? FOR UPDATE
    ");
        $stmtLock->bind_param("i", $lote_id);
        $stmtLock->execute();
        $lote = $stmtLock->get_result()->fetch_assoc();

        if (!$lote) {
            return 0;
        }

        // Detecta si el lote fue creado por la transmutación (código TR-... y cantidad inicial igual a lo ingresado)
        $esLoteCreado = (
            strpos((string) $lote['codigo_lote'], 'TR-') === 0 &&
            abs((float) $lote['cantidad_inicial'] - $cantidad) <= 0.001
        );

        if ($esLoteCreado) {
            return (int) $lote['id'];
        }

        // Si era un lote preexistente, restar la cantidad agregada
        $nuevaCantidad = (float) $lote['cantidad_actual'] - $cantidad;
        if ($nuevaCantidad < 0) {
            $nuevaCantidad = 0;
        }

        $stmtUpdLote = $this->db->prepare("UPDATE `lotes_stock` SET `cantidad_actual` = ? WHERE `id` = ?");
        $stmtUpdLote->bind_param("di", $nuevaCantidad, $lote_id);
        if (!$stmtUpdLote->execute()) {
            throw new Exception("Error al restar cantidad del lote #$lote_id: " . $stmtUpdLote->error);
        }

        $stmtAgotado = $this->db->prepare("
        UPDATE `lotes_stock`
        SET `estado_lote` = 'agotado'
        WHERE `id` = ? AND `cantidad_actual` <= 0.001
    ");
        $stmtAgotado->bind_param("i", $lote_id);
        $stmtAgotado->execute();

        return 0;
    }
    /**
     * Obtiene el historial de transmutaciones con nombres de productos
     */
    // public function listarTransmutaciones($almacen_id) {
//     // Si el almacen_id es 0, es admin y ve todo (1=1)
//     $where = ($almacen_id > 0) ? "WHERE t.almacen_id = ?" : "WHERE 1=1";

    //     $sql = "SELECT 
//                 t.id, 
//                 t.fecha as fecha_registro,
//                 t.observaciones,
//                 u.nombre as usuario_nombre,

    //                 -- Producto de salida (origen)
//                 p1.nombre as producto_origen,
//                 p1.unidad_medida as unidad_origen,
//                 td1.cantidad as cant_origen,

    //                 -- Producto de entrada (destino)
//                 p2.nombre as producto_destino,
//                 p2.unidad_medida as unidad_destino,
//                 td2.cantidad as cant_destino,
//                 a.nombre as almacen

    //             FROM transmutaciones t
//             LEFT JOIN usuarios u ON t.usuario_id = u.id

    //             -- Unión para obtener el producto que SALE (origen)
//             LEFT JOIN transmutacion_detalle td1 ON t.id = td1.transmutacion_id AND td1.tipo = 'salida'
//             LEFT JOIN productos p1 ON td1.producto_id = p1.id

    //             -- Unión para obtener el producto que ENTRA (destino)
//             LEFT JOIN transmutacion_detalle td2 ON t.id = td2.transmutacion_id AND td2.tipo = 'entrada'
//             LEFT JOIN productos p2 ON td2.producto_id = p2.id
//             join almacenes a on a.id=t.almacen_id

    //             $where

    //             -- Agrupamos por el ID de transmutación para asegurar una fila por registro
//             GROUP BY t.id
//             ORDER BY t.fecha DESC 
//             LIMIT 100";

    //     $stmt = $this->db->prepare($sql);

    //     if ($almacen_id > 0) {
//         $stmt->bind_param("i", $almacen_id);
//     }

    //     $stmt->execute();
//     return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
// }
    public function listarTransmutaciones($filtros = [], $rol_id = 1, $almacen_sesion = 0)
    {
        $where = " WHERE 1=1 ";

        // 1. Seguridad por Almacén / Filtro de Almacén
        if ($rol_id != 1) {
            // Si no es admin, restringe al almacén de su sesión
            $where .= " AND t.almacen_id = " . intval($almacen_sesion) . " ";
        } elseif (!empty($filtros['almacen'])) {
            // Si es admin y seleccionó un almacén específico
            $where .= " AND t.almacen_id = " . intval($filtros['almacen']) . " ";
        }

        // 2. Filtro por Producto (ID de Producto en Origen o Destino)
        if (!empty($filtros['producto'])) {
            $prod_id = intval($filtros['producto']);
            $where .= " AND (td1.producto_id = $prod_id OR td2.producto_id = $prod_id) ";
        }

        // 3. Buscador General (ID, Observaciones o Nombres de Productos)
        if (!empty($filtros['search'])) {
            $s = $this->db->real_escape_string($filtros['search']);
            $where .= " AND (t.id LIKE '%$s%' 
                    OR t.observaciones LIKE '%$s%' 
                    OR p1.nombre LIKE '%$s%' 
                    OR p2.nombre LIKE '%$s%') ";
        }

        // 4. Rango de Fechas
        if (!empty($filtros['rango']) && $filtros['rango'] !== 'todos') {
            $where .= $this->construirFiltroFecha($filtros, 't.fecha');
        }

        $sql = "SELECT 
                t.id, 
                t.fecha as fecha_registro,
                t.observaciones,
                u.nombre as usuario_nombre,
                
                -- Producto de salida (origen)
                p1.nombre as producto_origen,
                p1.unidad_medida as unidad_origen,
                td1.cantidad as cant_origen,
                
                -- Producto de entrada (destino)
                p2.nombre as producto_destino,
                p2.unidad_medida as unidad_destino,
                td2.cantidad as cant_destino,
                a.nombre as almacen

            FROM transmutaciones t
            LEFT JOIN usuarios u ON t.usuario_id = u.id
            
            -- Unión para obtener el producto que SALE (origen)
            LEFT JOIN transmutacion_detalle td1 ON t.id = td1.transmutacion_id AND td1.tipo = 'salida'
            LEFT JOIN productos p1 ON td1.producto_id = p1.id
            
            -- Unión para obtener el producto que ENTRA (destino)
            LEFT JOIN transmutacion_detalle td2 ON t.id = td2.transmutacion_id AND td2.tipo = 'entrada'
            LEFT JOIN productos p2 ON td2.producto_id = p2.id
            JOIN almacenes a ON a.id = t.almacen_id
            
            $where
            
            GROUP BY t.id
            ORDER BY t.fecha DESC";

        $res = $this->db->query($sql);
        return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    }
    private function construirFiltroFecha($f, $campoFecha = 't.fecha')
    {
        if (empty($f['rango']) || $f['rango'] === 'todos') {
            return "";
        }

        switch ($f['rango']) {
            case 'hoy':
                return " AND DATE($campoFecha) = CURDATE() ";
            case 'ayer':
                return " AND DATE($campoFecha) = SUBDATE(CURDATE(), 1) ";
            case 'semana':
                return " AND YEARWEEK($campoFecha, 1) = YEARWEEK(CURDATE(), 1) ";
            case 'mes':
                return " AND MONTH($campoFecha) = MONTH(CURDATE()) AND YEAR($campoFecha) = YEAR(CURDATE()) ";
            case 'personalizado':
                if (!empty($f['inicio']) && !empty($f['fin'])) {
                    $ini = $this->db->real_escape_string($f['inicio']);
                    $fin = $this->db->real_escape_string($f['fin']);
                    return " AND DATE($campoFecha) BETWEEN '$ini' AND '$fin' ";
                }
                return "";
            default:
                return "";
        }
    }
    /**
     * Obtiene las reglas de transmutación configuradas.
     * @param int $almacen_id Si es 0, actúa como SuperAdmin y trae todas.
     * @return array Lista de reglas con nombres de productos y almacenes.
     */
    public function listarConfiguraciones($almacen_id = 0)
    {
        try {
            // Añadimos ct.almacen_id para que el PHP pueda agrupar
            $sql = "SELECT 
                    ct.id,
                    ct.almacen_id, 
                    a.nombre AS almacen,
                    p1.sku AS sku_origen,
                    p1.nombre AS producto_origen,
                    p2.sku AS sku_destino,
                    p2.nombre AS producto_destino,
                    ct.rendimiento_teorico,
                    p1.unidad_medida AS unidad,
                    ct.notas
                FROM config_transmutaciones ct
                JOIN almacenes a ON ct.almacen_id = a.id
                JOIN productos p1 ON ct.producto_origen_id = p1.id
                JOIN productos p2 ON ct.producto_destino_id = p2.id";

            if ($almacen_id > 0) {
                $sql .= " WHERE ct.almacen_id = ?";
            }

            $sql .= " ORDER BY a.nombre ASC";

            $stmt = $this->db->prepare($sql);

            if ($almacen_id > 0) {
                $stmt->bind_param("i", $almacen_id);
            }

            $stmt->execute();
            return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        } catch (Exception $e) {
            error_log("Error en listarConfiguraciones: " . $e->getMessage());
            return [];
        }
    }
}