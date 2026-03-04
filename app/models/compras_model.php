<?php
class CompraModel {

    private $conexion;

    public function __construct($conexion) {
        $this->conexion = $conexion;
    }

    /* =========================
       USUARIOS ACTIVOS
    ========================== */
    public function obtenerUsuarios() {
        $sql = "SELECT id, nombre FROM usuarios WHERE activo = 1 ORDER BY nombre ASC";
        return $this->conexion->query($sql);
    }

    /* =========================
       ALMACENES
    ========================== */
    public function obtenerAlmacenes() {
        $sql = "SELECT id, nombre, codigo FROM almacenes WHERE activo = 1";
        return $this->conexion->query($sql);
    }

    /* =========================
       CATEGORIAS
    ========================== */
    public function obtenerCategorias() {
        $sql = "SELECT * FROM categorias ORDER BY nombre ASC";
        return $this->conexion->query($sql);
    }

    /* =========================
       PRODUCTOS ACTIVOS
    ========================== */
    public function obtenerProductos() {
        $sql = "SELECT id, nombre, sku FROM productos WHERE activo = 1 ORDER BY nombre ASC";
        return $this->conexion->query($sql);
    }

    /* =========================
       TOTALES
    ========================== */
    public function obtenerTotales($desde, $hasta, $folio, $usuario) {

        $condC = "WHERE c.fecha_compra BETWEEN '$desde' AND '$hasta'";
        $condG = "WHERE g.fecha_gasto BETWEEN '$desde' AND '$hasta'";

        if ($folio != '') {
            $condC .= " AND c.folio LIKE '%$folio%'";
            $condG .= " AND g.folio LIKE '%$folio%'";
        }

        if ($usuario != '') {
            $condC .= " AND c.usuario_registra_id = '$usuario'";
            $condG .= " AND g.usuario_registra_id = '$usuario'";
        }

        $resCompras = $this->conexion->query("SELECT SUM(c.total) as total FROM compras c $condC");
        $totalCompras = $resCompras->fetch_assoc()['total'] ?? 0;

        $resGastos = $this->conexion->query("SELECT SUM(g.total) as total FROM gastos g $condG");
        $totalGastos = $resGastos->fetch_assoc()['total'] ?? 0;

        return [
            'compras' => $totalCompras,
            'gastos' => $totalGastos,
            'total' => $totalCompras + $totalGastos
        ];
    }

    /* =========================
       LISTADO UNIFICADO
    ========================== */
    public function obtenerEgresos($desde, $hasta, $folio, $usuario) {

        $condC = "WHERE c.fecha_compra BETWEEN '$desde' AND '$hasta'";
        $condG = "WHERE g.fecha_gasto BETWEEN '$desde' AND '$hasta'";

        if ($folio != '') {
            $condC .= " AND c.folio LIKE '%$folio%'";
            $condG .= " AND g.folio LIKE '%$folio%'";
        }

        if ($usuario != '') {
            $condC .= " AND c.usuario_registra_id = '$usuario'";
            $condG .= " AND g.usuario_registra_id = '$usuario'";
        }

        $sql = "(SELECT c.id, c.folio, c.proveedor as entidad, c.fecha_compra as fecha, 
                c.total, 'compra' as tipo, c.tiene_faltantes, 
                u.nombre as usuario_nombre
                FROM compras c 
                LEFT JOIN usuarios u ON c.usuario_registra_id = u.id
                $condC)

                UNION

                (SELECT g.id, g.folio, g.beneficiario as entidad, g.fecha_gasto as fecha, 
                g.total, 'gasto' as tipo, 0 as tiene_faltantes, 
                u.nombre as usuario_nombre
                FROM gastos g
                LEFT JOIN usuarios u ON g.usuario_registra_id = u.id
                $condG)

                ORDER BY folio DESC";

        return $this->conexion->query($sql);
    }
}