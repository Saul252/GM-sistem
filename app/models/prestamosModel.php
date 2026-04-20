<?php

class PrestamosModel {
    private $db;

    public function __construct($conexion) {
        $this->db = $conexion;
    }

    /* =========================
        CREAR PRÉSTAMO
    ========================= */
    public function crearPrestamo($data) {
        $sql = "INSERT INTO prestamos 
                (trabajador_id, almacen_id, monto_total, descripcion, estado, fecha_registro)
                VALUES (?, ?, ?, ?,?, NOW())";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $data['trabajador_id'],
            $data['almacen_id'],
            $data['monto_total'],
            $data['descripcion'],
            $data['estado']
        ]);
    }

public function registrarAbono($data) {

    // obtener número de pago automático
    $sqlNum = "SELECT COUNT(*) + 1 as numero 
               FROM prestamos_abonos 
               WHERE prestamo_id = ?";

    $stmtNum = $this->db->prepare($sqlNum);
    $stmtNum->bind_param("i", $data['prestamo_id']);
    $stmtNum->execute();

    $resNum = $stmtNum->get_result();
    $rowNum = $resNum->fetch_assoc();
    $numero = $rowNum['numero'] ?? 1;

    $sql = "INSERT INTO prestamos_abonos
            (prestamo_id, monto_abono, numero_pago, metodo_pago, usuario_registro_id, fecha_abono, observaciones)
            VALUES (?, ?, ?, ?, ?, NOW(), ?)";

    $stmt = $this->db->prepare($sql);

    $stmt->bind_param(
        "idisis",
        $data['prestamo_id'],
        $data['monto_abono'],
        $numero,
        $data['metodo_pago'],
        $data['usuario_id'],
        $data['observaciones']
    );

    return $stmt->execute();
}
public function obtenerPrestamo($id) {

    $sql = "
        SELECT 
            p.*,
            t.nombre AS trabajador,
            COALESCE(SUM(pa.monto_abono), 0) AS total_abonado,
            (p.monto_total - COALESCE(SUM(pa.monto_abono),0)) AS saldo_pendiente
        FROM prestamos p
        LEFT JOIN trabajadores t ON t.id = p.trabajador_id
        LEFT JOIN prestamos_abonos pa ON pa.prestamo_id = p.id
        WHERE p.id = ?
        GROUP BY p.id
    ";

    $stmt = $this->db->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();

    $res = $stmt->get_result();
    return $res->fetch_assoc();
}
public function listarPrestamos($almacen_id = 0, $f_inicio = null, $f_fin = null) {

    $filtro = "";
    $params = [];

    // =========================
    // FILTRO ALMACÉN
    // =========================
    if ($almacen_id != 0) {
        $filtro = "WHERE p.almacen_id = ?";
        $params[] = $almacen_id;
    } else {
        $filtro = "WHERE 1=1";
    }

    // =========================
    // FILTRO FECHAS
    // =========================
    if (!empty($f_inicio) && !empty($f_fin)) {
        $filtro .= " AND DATE(p.fecha_registro) BETWEEN ? AND ?";
        $params[] = $f_inicio;
        $params[] = $f_fin;
    }

    $sql = "
        SELECT 
            p.*,
            t.nombre AS trabajador,
            COALESCE(SUM(pa.monto_abono), 0) AS total_abonado,
            (p.monto_total - COALESCE(SUM(pa.monto_abono),0)) AS saldo_pendiente
        FROM prestamos p
        LEFT JOIN trabajadores t ON t.id = p.trabajador_id
        LEFT JOIN prestamos_abonos pa ON pa.prestamo_id = p.id
        $filtro
        GROUP BY p.id
        ORDER BY p.fecha_registro DESC
    ";

    $stmt = $this->db->prepare($sql);
    if (!$stmt) return [];

    if (!empty($params)) {
        $types = str_repeat('s', count($params));
        $stmt->bind_param($types, ...$params);
    }

    $stmt->execute();
    $res = $stmt->get_result();

    return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
}
public function listarAbonos($prestamo_id) {

    $sql = "
        SELECT *
        FROM prestamos_abonos
        WHERE prestamo_id = ?
        ORDER BY numero_pago ASC
    ";

    $stmt = $this->db->prepare($sql);
    $stmt->bind_param("i", $prestamo_id);
    $stmt->execute();

    $res = $stmt->get_result();
    return $res->fetch_all(MYSQLI_ASSOC);
}
public function cerrarPrestamoSiPagado($prestamo_id) {

    $sql = "
        SELECT 
            p.monto_total,
            COALESCE(SUM(pa.monto_abono),0) as abonado
        FROM prestamos p
        LEFT JOIN prestamos_abonos pa ON pa.prestamo_id = p.id
        WHERE p.id = ?
        GROUP BY p.id
    ";

    $stmt = $this->db->prepare($sql);
    $stmt->bind_param("i", $prestamo_id);
    $stmt->execute();

    $res = $stmt->get_result();
    $data = $res->fetch_assoc();

    if (!$data) return false;

    if ($data['abonado'] >= $data['monto_total']) {

        $update = $this->db->prepare("UPDATE prestamos SET estado = 'pagado' WHERE id = ?");
        $update->bind_param("i", $prestamo_id);

        return $update->execute();
    }

    return true;
}
}