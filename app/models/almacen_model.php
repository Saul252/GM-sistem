<?php
class AlmacenModel
{
    private $db;

    public function __construct($conexion)
    {
        $this->db = $conexion;
    }

    public function getCategorias()
    {
        return $this->db->query("SELECT id, nombre FROM categorias ORDER BY nombre ASC")->fetch_all(MYSQLI_ASSOC);
    }
    public function getUnidadesMedida()
    {
        return $this->db->query("SELECT id, nombre, clave FROM unidades_medida ORDER BY nombre ASC")->fetch_all(MYSQLI_ASSOC);
    }

    public function getAlmacenes($almacen_id)
    {
        $sql = "SELECT * FROM almacenes WHERE activo = 1";
        if ($almacen_id > 0)
            $sql .= " AND id = " . intval($almacen_id);
        $sql .= " ORDER BY nombre ASC";
        return $this->db->query($sql)->fetch_all(MYSQLI_ASSOC);
    }
    public function inversion($almacen_id)
    {
        try {
            // Consulta base limpia
            $sql = "SELECT 
                    SUM(`cantidad_actual` * `precio_compra_unitario`) AS `valor_total_inventario`
                FROM 
                    `lotes_stock`
                WHERE 
                    `estado_lote` = 'activo' 
                    AND `cantidad_actual` > 0";

            // CORRECCIÓN 1: Filtrar por la columna correcta 'almacen_id'
            if ($almacen_id > 0) {
                $sql .= " AND `almacen_id` = " . intval($almacen_id);
            }

            // CORRECCIÓN 2: Eliminado el ORDER BY que rompía la consulta
            $result = $this->db->query($sql);

            if (!$result) {
                return 0;
            }

            // CORRECCIÓN 3: fetch_assoc porque es una sola fila de totales
            $fila = $result->fetch_assoc();

            // Retornamos el número directo (convertido a float) o 0 si es null
            return isset($fila['valor_total_inventario']) ? floatval($fila['valor_total_inventario']) : 0.00;

        } catch (Exception $e) {
            return 0;
        }
    }
    /**
     * Modifica o registra el inventario inicial, lotes, movimientos y precios por almacén.
     * 
     * @param int $producto_id ID del producto a procesar.
     * @param array $data Arreglo con la información general y el sub-arreglo 'almacenes'.
     * @return bool True si todo se ejecuta correctamente.
     * @throws Exception Si ocurre un error en alguna consulta SQL.
     */
    public function modificarInventarioInicial(int $producto_id, array $data): bool
    {
        // 1. Validar que exista el arreglo de almacenes
        if (empty($data['almacenes']) || !is_array($data['almacenes'])) {
            return false;
        }

        // 2. Extraer y sanitizar datos generales de nivel $data
        $factor_conversion = floatval($data['factor_conversion'] ?? 1);
        if ($factor_conversion <= 0) {
            $factor_conversion = 1;
        }

        $precio_adquisicion = floatval($data['precio_adquisicion'] ?? 0);
        $sku = $data['sku'] ?? 'PROD-GEN';
        $usuario_id = isset($data['usuario_id']) ? intval($data['usuario_id']) : null;

        // 3. Recorrer los almacenes usando la estructura exacta de $datos
        foreach ($data['almacenes'] as $almacen_id => $datos) {

            // Variables que provienen de cada almacén ($datos)
            $stock = isset($datos['stock']) ? floatval($datos['stock']) : 0;
            $min = floatval($datos['stock_minimo'] ?? 0);

            // Precios por almacén calculados con el factor de conversión
            $pm = (!empty($datos['precio_minorista']))
                ? floatval($datos['precio_minorista']) / $factor_conversion
                : 0;

            $pma = (!empty($datos['precio_mayorista']))
                ? floatval($datos['precio_mayorista']) / $factor_conversion
                : 0;

            $pdi = (!empty($datos['precio_distribuidor']))
                ? floatval($datos['precio_distribuidor']) / $factor_conversion
                : 0;

            // --- INVENTARIO ---
            $stmtInv = $this->db->prepare("INSERT INTO inventario 
            (almacen_id, producto_id, stock, stock_minimo) 
            VALUES (?, ?, ?, ?)");

            if (!$stmtInv)
                throw new Exception("Error prepare inventario: " . $this->db->error);

            $stmtInv->bind_param("iidd", $almacen_id, $producto_id, $stock, $min);

            if (!$stmtInv->execute()) {
                throw new Exception("Error inventario: " . $stmtInv->error);
            }
            $stmtInv->close();

            // --- LOTES Y MOVIMIENTOS (Si hay stock) ---
            if ($stock > 0) {
                $precioIndividual = ($precio_adquisicion > 0)
                    ? ($precio_adquisicion / $stock)
                    : 0;

                $codigo_lote = "L-" . $sku . "-" . date('His');

                $stmtLote = $this->db->prepare("INSERT INTO lotes_stock 
                (producto_id, almacen_id, codigo_lote, cantidad_inicial, cantidad_actual, precio_compra_unitario, estado_lote) 
                VALUES (?, ?, ?, ?, ?, ?, 'activo')");

                if (!$stmtLote)
                    throw new Exception("Error prepare lote: " . $this->db->error);

                $stmtLote->bind_param(
                    "iisddd",
                    $producto_id,
                    $almacen_id,
                    $codigo_lote,
                    $stock,
                    $stock,
                    $precioIndividual
                );

                if (!$stmtLote->execute()) {
                    throw new Exception("Error lote: " . $stmtLote->error);
                }
                $stmtLote->close();

                // MOVIMIENTO DE ENTRADA
                $obs = "Carga inicial (Lote: $codigo_lote)";

                $stmtMov = $this->db->prepare("INSERT INTO movimientos 
                (producto_id, tipo, cantidad, almacen_destino_id, usuario_registra_id, observaciones) 
                VALUES (?, 'entrada', ?, ?, ?, ?)");

                if (!$stmtMov)
                    throw new Exception("Error prepare movimiento: " . $this->db->error);

                $stmtMov->bind_param(
                    "idiis",
                    $producto_id,
                    $stock,
                    $almacen_id,
                    $usuario_id,
                    $obs
                );

                if (!$stmtMov->execute()) {
                    throw new Exception("Error movimiento: " . $stmtMov->error);
                }
                $stmtMov->close();
            }

            // --- PRECIOS POR ALMACÉN (Siempre se guardan) ---
            $stmtPre = $this->db->prepare("INSERT INTO precios_producto 
            (producto_id, almacen_id, precio_minorista, precio_mayorista, precio_distribuidor) 
            VALUES (?, ?, ?, ?, ?)");

            if (!$stmtPre)
                throw new Exception("Error prepare precios: " . $this->db->error);

            $stmtPre->bind_param(
                "iiddd",
                $producto_id,
                $almacen_id,
                $pm,
                $pma,
                $pdi
            );

            if (!$stmtPre->execute()) {
                throw new Exception("Error precios: " . $stmtPre->error);
            }
            $stmtPre->close();
        }

        return true;
    }
    public function getAlmacenesDestino($almacen_id = 0)
    {
        // Iniciamos la consulta básica
        $sql = "SELECT id, nombre FROM almacenes WHERE activo = 1";

        // Si el almacén es mayor a cero, agregamos la exclusión
        if ($almacen_id > 0) {
            $sql .= " AND id != " . intval($almacen_id);
        }

        $sql .= " ORDER BY nombre ASC";

        $res = $this->db->query($sql);
        return ($res) ? $res->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function getInventario($almacen_id = 0)
    {
        $sql = "SELECT p.id, p.sku, p.nombre,p.descripcion, p.categoria_id, p.factor_conversion, p.unidad_reporte,p.unidad_medida,c.nombre AS categoria_nombre,

                       i.stock, i.almacen_id, a.nombre AS almacen_nombre
                FROM inventario i
                INNER JOIN productos p ON i.producto_id = p.id
                INNER JOIN almacenes a ON i.almacen_id = a.id
                LEFT JOIN categorias c ON p.categoria_id = c.id
                WHERE p.activo = 1";

        if ($almacen_id > 0)
            $sql .= " AND i.almacen_id = " . intval($almacen_id);
        $sql .= " ORDER BY p.nombre ASC";

        return $this->db->query($sql)->fetch_all(MYSQLI_ASSOC);
    }

    public function getInventarioConId($almacen_id = 0)
    {
        $sql = "SELECT 
                p.id, 
                p.sku, 
                p.nombre, 
                p.unidad_medida,
                i.stock, 
                i.almacen_id, 
                a.nombre AS almacen_nombre
            FROM inventario i
            INNER JOIN productos p ON i.producto_id = p.id
            INNER JOIN almacenes a ON i.almacen_id = a.id
            WHERE p.activo = 1";

        // Si se pasa un almacén específico, filtramos
        if ($almacen_id > 0) {
            $sql .= " AND i.almacen_id = " . intval($almacen_id);
        }

        $sql .= " AND i.stock > 0"; // Opcional: solo mostrar lo que tiene existencias
        $sql .= " ORDER BY p.nombre ASC";

        return $this->db->query($sql)->fetch_all(MYSQLI_ASSOC);
    }

    public function getResumenStock($almacen_id)
    {
        // 1. Conteo global de productos únicos ACTIVOS en el catálogo maestro
        $sqlGlobal = "SELECT COUNT(*) as total FROM productos WHERE activo = 1";
        $resGlobal = $this->db->query($sqlGlobal)->fetch_assoc();
        $totalRegistrados = intval($resGlobal['total'] ?? 0);

        $id = intval($almacen_id);

        if ($id > 0) {
            // --- CASO VENDEDOR: Conteo de lo que tiene sucursal vs el total ---
            $sqlAlmacen = "SELECT 
                            (SELECT COUNT(DISTINCT producto_id) FROM inventario WHERE almacen_id = $id) as total_en_almacen,
                            nombre as nombre_almacen
                        FROM almacenes WHERE id = $id";
            $res = $this->db->query($sqlAlmacen)->fetch_assoc();

            return [
                "tipo" => "vendedor",
                "nombre" => $res['nombre_almacen'] ?? 'Almacén No Identificado',
                "mis_productos" => intval($res['total_en_almacen'] ?? 0),
                "total_sistema" => $totalRegistrados
            ];
        } else {
            // --- CASO ADMINISTRADOR: Control Total del Sistema ---
            // Al ser admin, sus "productos" son el 100% del catálogo activo
            return [
                "tipo" => "admin",
                "nombre" => "Sede Central (Global)",
                "mis_productos" => $totalRegistrados, // Aquí forzamos el 100% de cobertura
                "total_sistema" => $totalRegistrados
            ];
        }
    }
}