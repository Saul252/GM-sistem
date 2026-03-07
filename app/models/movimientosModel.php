<?php
class MovimientoModel {
    private $db;

    public function __construct($conexion) {
        $this->db = $conexion;
    }

    /**
     * Obtiene el historial de movimientos con filtros avanzados
     * @param array $filtros Datos provenientes de $_GET o $_POST
     * @param int $almacen_usuario_sesion ID del almacén del usuario (0 si es admin)
     */
    public function obtenerHistorial($filtros, $almacen_usuario_sesion) {
        // 1. Sanitización y Variables iniciales
        $periodo = $filtros['periodo'] ?? 'hoy';
        $tipo = $filtros['tipo'] ?? '';
        $f_inicio_user = $filtros['f_inicio'] ?? '';
        $f_fin_user = $filtros['f_fin'] ?? '';
        $almacen_filtro = intval($filtros['almacen_id'] ?? 0);

        // 2. Lógica de Rango de Fechas
        $hoy = date('Y-m-d');
        $inicio = $hoy; 
        $fin = $hoy;

        if ($periodo !== 'personalizado') {
            switch ($periodo) {
                case 'ayer': 
                    $inicio = date('Y-m-d', strtotime('-1 day')); 
                    $fin = $inicio; 
                    break;
                case 'semana': 
                    $inicio = date('Y-m-d', strtotime('-7 days')); 
                    break;
                case 'mes': 
                    $inicio = date('Y-m-01'); 
                    break;
                case 'hoy':
                default:
                    $inicio = $hoy;
                    $fin = $hoy;
                    break;
            }
        } else {
            $inicio = !empty($f_inicio_user) ? $f_inicio_user : $hoy;
            $fin = !empty($f_fin_user) ? $f_fin_user : $hoy;
        }

        // 3. Construcción dinámica del WHERE
        // Usamos DATE(m.fecha) para ignorar las horas en la comparación del rango
        $where = "WHERE DATE(m.fecha) BETWEEN '$inicio' AND '$fin'";
        
        // Filtro por tipo de operación
        if (!empty($tipo)) {
            $where .= " AND m.tipo = '" . $this->db->real_escape_string($tipo) . "'";
        }

        // 4. LÓGICA CRÍTICA: Filtro por Almacén (Origen O Destino)
        // Si el usuario tiene un almacén asignado, mandamos ese. 
        // Si es admin ($almacen_usuario_sesion == 0), usamos el del filtro del select.
        $target_almacen = ($almacen_usuario_sesion > 0) ? $almacen_usuario_sesion : $almacen_filtro;

        if ($target_almacen > 0) {
            // Los paréntesis son obligatorios aquí para que el OR no anule los filtros anteriores
            $where .= " AND (m.almacen_origen_id = $target_almacen OR m.almacen_destino_id = $target_almacen)";
        }

        // 5. Consulta SQL con Joins
        $sql = "SELECT 
                    m.*, 
                    p.nombre as prod_nombre, 
                    p.sku, 
                    p.factor_conversion, 
                    p.unidad_reporte,
                    a1.nombre as origen_nombre, 
                    a2.nombre as destino_nombre, 
                    u.nombre as usuario_nombre
                FROM movimientos m 
                INNER JOIN productos p ON m.producto_id = p.id
                LEFT JOIN almacenes a1 ON m.almacen_origen_id = a1.id
                LEFT JOIN almacenes a2 ON m.almacen_destino_id = a2.id
                LEFT JOIN usuarios u ON m.usuario_registra_id = u.id
                $where 
                ORDER BY m.fecha DESC";

        $resultado = $this->db->query($sql);
        $data = [];

        // Configuración de colores para la vista
        $config_estilos = [
            'entrada'  => ['color' => 'success', 'label' => 'Entrada'],
            'salida'   => ['color' => 'danger',  'label' => 'Salida'],
            'traspaso' => ['color' => 'primary', 'label' => 'Traspaso'],
            'ajuste'   => ['color' => 'warning', 'label' => 'Ajuste']
        ];

        if ($resultado) {
            while ($row = $resultado->fetch_assoc()) {
                $tipo_key = strtolower($row['tipo']);
                
                $data[] = [
                    'id'                => $row['id'],
                    'fecha_format'      => date('d/m/Y H:i', strtotime($row['fecha'])),
                    'producto'          => $row['prod_nombre'],
                    'sku'               => $row['sku'],
                    'tipo'              => $config_estilos[$tipo_key]['label'] ?? $row['tipo'],
                    'color'             => $config_estilos[$tipo_key]['color'] ?? 'secondary',
                    'cantidad'          => $row['cantidad'],
                    'factor_conversion' => $row['factor_conversion'] ?? 1,
                    'unidad_reporte'    => $row['unidad_reporte'] ?? 'PZA',
                    'origen'            => $row['origen_nombre'] ?? 'EXTERNO',
                    'destino'           => $row['destino_nombre'] ?? 'EXTERNO',
                    'almacen_origen_id' => $row['almacen_origen_id'],
                    'almacen_destino_id'=> $row['almacen_destino_id'],
                    'u_reg'             => $row['usuario_nombre'] ?? 'Sist.',
                    'obs'               => $row['observaciones'] ?? ''
                ];
            }
        }

        return $data;
    }
}