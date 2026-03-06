<?php
class ProductoModel {
    private $db;

    public function __construct($conexion) {
        $this->db = $conexion;
    }

    /**
     * Guarda un producto y sus precios generales para todos los almacenes
     */
    public function guardarCompleto($datos) {
        try {
            $this->db->begin_transaction();

            // 1. Insertar en la tabla 'productos'
            $sqlProd = "INSERT INTO productos (
                sku, nombre, descripcion, unidad_medida, unidad_reporte, 
                factor_conversion, fiscal_clave_prod, fiscal_clave_unidad, 
                precio_adquisicion, impuesto_iva, categoria_id, activo
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1)";

            $stmt = $this->db->prepare($sqlProd);
            $stmt->bind_param(
                "sssssdssddi",
                $datos['sku'],
                $datos['nombre'],
                $datos['descripcion'],
                $datos['unidad_medida'],
                $datos['unidad_reporte'],
                $datos['factor_conversion'],
                $datos['fiscal_clave_prod'],
                $datos['fiscal_clave_unit'], // Clave unidad SAT
                $datos['precio_adquisicion'],
                $datos['impuesto_iva'],
                $datos['categoria_id']
            );

            if (!$stmt->execute()) throw new Exception("Error al insertar producto");
            
            $productoId = $this->db->insert_id;

            // 2. Obtener todos los almacenes activos para asignarles los precios
            $resAlmacenes = $this->db->query("SELECT id FROM almacenes WHERE activo = 1");
            
            $sqlPrecios = "INSERT INTO precios_producto (
                producto_id, almacen_id, precio_minorista, precio_mayorista, precio_distribuidor
            ) VALUES (?, ?, ?, ?, ?)";
            
            $stmtPrecios = $this->db->prepare($sqlPrecios);

            while ($alm = $resAlmacenes->fetch_assoc()) {
                $stmtPrecios->bind_param(
                    "iiddd",
                    $productoId,
                    $alm['id'],
                    $datos['precio_minorista'],
                    $datos['precio_mayorista'],
                    $datos['precio_distribuidor']
                );
                if (!$stmtPrecios->execute()) throw new Exception("Error al insertar precios para el almacén " . $alm['id']);
            }

            $this->db->commit();
            return $productoId;

        } catch (Exception $e) {
            $this->db->rollback();
            error_log($e->getMessage());
            return false;
        }
    }

    public function existeSku($sku) {
        $stmt = $this->db->prepare("SELECT id FROM productos WHERE sku = ?");
        $stmt->bind_param("s", $sku);
        $stmt->execute();
        return $stmt->get_result()->num_rows > 0;
    }
    /**
 * Obtiene todos los productos con los campos necesarios para el modal de compras
 */
/**
 * Obtiene los productos optimizados para el catálogo de compras
 */
/**
     * Obtiene los productos optimizados para el catálogo de compras (Versión MySQLi)
     */
    public function getProductos() {
        try {
            $sql = "SELECT 
                        p.id, 
                        p.sku, 
                        p.nombre, 
                        p.unidad_medida, 
                        p.unidad_reporte, 
                        p.factor_conversion,
                        c.nombre as nombre_categoria
                    FROM productos p
                    LEFT JOIN categorias c ON p.categoria_id = c.id
                    WHERE p.activo = 1 
                    ORDER BY p.nombre ASC";
            
            $result = $this->db->query($sql);
            
            if (!$result) {
                return [];
            }

            $productos = [];
            while ($row = $result->fetch_assoc()) {
                // Forzamos que los valores numéricos sean tratados como tales
                $row['id'] = (int)$row['id'];
                $row['factor_conversion'] = (float)$row['factor_conversion'];
                $productos[] = $row;
            }
            
            return $productos;
        } catch (Exception $e) {
            error_log("Error en ProductoModel::getProductos -> " . $e->getMessage());
            return [];
        }
    }
}