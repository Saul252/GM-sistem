<?php
class ProveedoresModel {
    private $db;

    public function __construct($conexion) {
        $this->db = $conexion;
    }

    public function listarTodos() {
        $sql = "SELECT * FROM proveedores ORDER BY activo DESC, nombre_comercial ASC";
        $result = $this->db->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }
public function listarTodosProveedorsYDeuda() {

 $sql = "
    SELECT 
        p.*,

        cpp.id AS deuda_id,
        cpp.id_referencia_origen,
        cpp.monto_total,
        cpp.monto_pagado,
        cpp.estado,
        cpp.fecha_registro,

        (cpp.monto_total - IFNULL(cpp.monto_pagado,0)) AS pendiente

    FROM proveedores p

    LEFT JOIN cuentas_por_pagar cpp 
        ON cpp.id_proveedor = p.id
        AND cpp.estado != 'cancelado'

    ORDER BY p.activo DESC, p.nombre_comercial ASC
";

    $result = $this->db->query($sql);
    $rows = $result->fetch_all(MYSQLI_ASSOC);

    // 🔥 AGRUPAR EN PHP
    $proveedores = [];

    foreach ($rows as $row) {

        $pid = $row['id'];

        if (!isset($proveedores[$pid])) {
            $proveedores[$pid] = $row;
            $proveedores[$pid]['total_deuda'] = 0;
            $proveedores[$pid]['detalle_deudas'] = [];
        }

        // 🔥 SOLO SI TIENE DEUDA REAL
        if (!empty($row['deuda_id']) && $row['estado'] != 'pagado') {

            $pendiente = max($row['pendiente'], 0);

            if ($pendiente > 0) {

                $proveedores[$pid]['total_deuda'] += $pendiente;

                $proveedores[$pid]['detalle_deudas'][] = [
                    'compra_id'   => $row['id_referencia_origen'],
                    'monto_total' => $row['monto_total'],
                    'monto_pagado'=> $row['monto_pagado'],
                    'pendiente'   => $pendiente,
                    'estado'      => $row['estado'],
                    'fecha'       => $row['fecha_registro']
                ];
            }
        }
    }

    return array_values($proveedores);
}
public function ProveedorYDeuda($id) {

    $sql = "
        SELECT 
            cpp.id_referencia_origen AS compra_id,
            (cpp.monto_total - IFNULL(cpp.monto_pagado,0)) AS pendiente

        FROM cuentas_por_pagar cpp

        WHERE cpp.id_proveedor = ?
        AND cpp.estado NOT IN ('pagado', 'cancelado')
        AND (cpp.monto_total - IFNULL(cpp.monto_pagado,0)) > 0

        ORDER BY cpp.fecha_registro ASC
    ";

    $stmt = $this->db->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();

    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
public function obtenerProveedorPorNombre($nombre) {
    $sql = "
        SELECT 
            p.id
        FROM proveedores p
        WHERE p.nombre_comercial LIKE ?
        LIMIT 1
    ";

    $stmt = $this->db->prepare($sql);

    // 🔥 Importante: agregamos los % aquí
    $busqueda = "%" . $nombre . "%";

    $stmt->bind_param("s", $busqueda);
    $stmt->execute();

    $result = $stmt->get_result();
    return $result->fetch_assoc(); // devuelve ['id' => ...] o null
}
    public function guardar($datos) {
        $sql = "INSERT INTO proveedores (nombre_comercial, razon_social, rfc, correo, telefono, activo) 
                VALUES (?, ?, ?, ?, ?, 1)";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("sssss", 
            $datos['nombre_comercial'], $datos['razon_social'], 
            $datos['rfc'], $datos['correo'], $datos['telefono']
        );
        return $stmt->execute();
    }

    public function obtenerPorId($id) {
        $stmt = $this->db->prepare("SELECT * FROM proveedores WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function cambiarEstado($id, $estado) {
        $stmt = $this->db->prepare("UPDATE proveedores SET activo = ? WHERE id = ?");
        $stmt->bind_param("ii", $estado, $id);
        return $stmt->execute();
    }
    public function getResumenProveedores() {
    // Contamos solo los que están activos para que el número sea real
    $sql = "SELECT COUNT(*) as total FROM proveedores WHERE activo = 1";
    $query = $this->db->query($sql);
    $res = ($query) ? $query->fetch_assoc() : ['total' => 0];
    
    return [
        "total" => intval($res['total'] ?? 0),
        "etiqueta" => "Global"
    ];
}
}