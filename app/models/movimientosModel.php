<?php

date_default_timezone_set('America/Mexico_City');
// El controlador ya definió $almacen_usuario y $conexion

class MovimientoModel {
    private $db;

    public function __construct($conexion) {
        $this->db = $conexion;
    }

    /**
     * Obtiene el historial basándose en la presencia de usuario_recibe_id para determinar el estado
     */
    public function obtenerHistorial($filtros, $almacen_usuario_sesion) {
        $periodo = $filtros['periodo'] ?? 'hoy';
        $tipo = $filtros['tipo'] ?? '';
        $f_inicio_user = $filtros['f_inicio'] ?? '';
        $f_fin_user = $filtros['f_fin'] ?? '';
        
        $almacen_filtro = intval($filtros['almacen_id'] ?? 0);
        $target_almacen = ($almacen_usuario_sesion > 0) ? $almacen_usuario_sesion : $almacen_filtro;

        $hoy = date('Y-m-d');
        $inicio = $hoy; $fin = $hoy;

        if ($periodo !== 'personalizado') {
            switch ($periodo) {
                case 'ayer': $inicio = date('Y-m-d', strtotime('-1 day')); $fin = $inicio; break;
                case 'semana': $inicio = date('Y-m-d', strtotime('-7 days')); break;
                case 'mes': $inicio = date('Y-m-01'); break;
            }
        } else {
            $inicio = !empty($f_inicio_user) ? $f_inicio_user : $hoy;
            $fin = !empty($f_fin_user) ? $f_fin_user : $hoy;
        }

        $where = "WHERE DATE(m.fecha) BETWEEN '$inicio' AND '$fin'";
        
        if (!empty($tipo)) {
            $where .= " AND m.tipo = '" . $this->db->real_escape_string($tipo) . "'";
        }

        if ($target_almacen > 0) {
            $where .= " AND (m.almacen_origen_id = $target_almacen OR m.almacen_destino_id = $target_almacen)";
        }

        // Consulta usando tus nombres de columna actuales
        $sql = "SELECT 
                    m.*, 
                    p.nombre as prod_nombre, p.sku, p.factor_conversion, p.unidad_reporte,
                    a1.nombre as origen_nombre, a2.nombre as destino_nombre, 
                    u1.nombre as usuario_nombre, u3.nombre as usuario_recibe_nombre
                FROM movimientos m 
                INNER JOIN productos p ON m.producto_id = p.id
                LEFT JOIN almacenes a1 ON m.almacen_origen_id = a1.id
                LEFT JOIN almacenes a2 ON m.almacen_destino_id = a2.id
                LEFT JOIN usuarios u1 ON m.usuario_registra_id = u1.id
                LEFT JOIN usuarios u3 ON m.usuario_recibe_id = u3.id
                $where 
                ORDER BY m.fecha DESC";

        $resultado = $this->db->query($sql);
        $data = [];

        $config_estilos = [
            'entrada'  => ['color' => 'success', 'label' => 'Entrada'],
            'salida'   => ['color' => 'danger',  'label' => 'Salida'],
            'traspaso' => ['color' => 'primary', 'label' => 'Traspaso'],
            'ajuste'   => ['color' => 'warning', 'label' => 'Ajuste']
        ];

        if ($resultado) {
            while ($row = $resultado->fetch_assoc()) {
                $tipo_key = strtolower($row['tipo']);
                
                // LÓGICA SIN TABLA MODIFICADA:
                // Si el tipo es traspaso y usuario_recibe_id es nulo/vacío, está pendiente.
                $es_traspaso = ($tipo_key === 'traspaso');
                $esta_pendiente = ($es_traspaso && (empty($row['usuario_recibe_id']) || $row['usuario_recibe_id'] == 0));

                $color = $config_estilos[$tipo_key]['color'] ?? 'secondary';
                $label = $config_estilos[$tipo_key]['label'] ?? $row['tipo'];

                if ($esta_pendiente) {
                    $color = 'info';
                    $label = 'Traspaso (En tránsito)';
                }

                $data[] = [
                    'id'                => $row['id'],
                    'fecha_format'      => date('d/m/Y H:i', strtotime($row['fecha'])),
                    'producto'          => $row['prod_nombre'],
                    'sku'               => $row['sku'],
                    'tipo'              => $label,
                    'color'             => $color,
                    'es_pendiente'      => $esta_pendiente,
                    'cantidad'          => $row['cantidad'],
                    'factor_conversion' => $row['factor_conversion'] ?? 1,
                    'unidad_reporte'    => $row['unidad_reporte'] ?? 'PZA',
                    'origen'            => $row['origen_nombre'] ?? '---',
                    'destino'           => $row['destino_nombre'] ?? '---',
                    'almacen_origen_id' => $row['almacen_origen_id'],
                    'almacen_destino_id'=> $row['almacen_destino_id'],
                    'u_reg'             => $row['usuario_nombre'] ?? 'Sist.',
                    'u_rec'             => $row['usuario_recibe_nombre'] ?? '---',
                    'obs'               => $row['observaciones'] ?? ''
                ];
            }
        }
        return $data;
    }

    /**
     * Procesa la aceptación usando el campo usuario_recibe_id como bandera de "completado"
     */
    public function confirmarRecepcionTraspaso($idMovimiento, $idUsuario) {
        $this->db->begin_transaction();

        try {
            // 1. Validar que el movimiento existe y NO tiene receptor aún
            $sqlMov = "SELECT producto_id, almacen_destino_id, cantidad, usuario_recibe_id 
                       FROM movimientos WHERE id = $idMovimiento FOR UPDATE";
            $resMov = $this->db->query($sqlMov);
            $mov = $resMov->fetch_assoc();

            if (!$mov) {
                throw new Exception("El movimiento no existe.");
            }
            if (!empty($mov['usuario_recibe_id']) && $mov['usuario_recibe_id'] > 0) {
                throw new Exception("Este traspaso ya fue recibido anteriormente.");
            }

            $producto_id = $mov['producto_id'];
            $almacen_id  = $mov['almacen_destino_id'];
            $cantidad    = $mov['cantidad'];

            // 2. Actualizar stock en el almacén destino
            // Usamos tu lógica de ON DUPLICATE KEY para asegurar que el registro exista
            $sqlStock = "INSERT INTO stock_almacen (producto_id, almacen_id, cantidad) 
                         VALUES ($producto_id, $almacen_id, $cantidad) 
                         ON DUPLICATE KEY UPDATE cantidad = cantidad + $cantidad";
            
            if (!$this->db->query($sqlStock)) {
                throw new Exception("Error al actualizar el inventario de destino.");
            }

            // 3. Registrar quién recibe y cuándo (esto "cierra" el movimiento según nuestra lógica)
            $ahora = date('Y-m-d H:i:s');
            $sqlUpdate = "UPDATE movimientos SET 
                            usuario_recibe_id = $idUsuario,
                            fecha_recepcion = '$ahora' 
                          WHERE id = $idMovimiento";
            
            if (!$this->db->query($sqlUpdate)) {
                throw new Exception("Error al registrar la recepción del movimiento.");
            }

            $this->db->commit();
            return ['success' => true];

        } catch (Exception $e) {
            $this->db->rollback();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    public function obtenerIdMovimientoPorVenta($venta_id) {
    $ids = []; // Array para almacenar los movimientos
    
    $sql = "SELECT m.id 
            FROM movimientos m 
            INNER JOIN ventas v ON m.referencia_id = v.id 
            WHERE v.id = ?";
            
    $stmt = $this->db->prepare($sql);
    $stmt->bind_param("i", $venta_id);
    $stmt->execute();
    $resultado = $stmt->get_result();
    
    // Recorremos todos los resultados, no solo el primero
    while ($fila = $resultado->fetch_assoc()) {
        $ids[] = $fila['id'];
    }
    
    return $ids; // Ahora regresa un array, ej: [45, 46, 47]
}
}