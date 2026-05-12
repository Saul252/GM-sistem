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
                $datos['fiscal_clave_unidad'], // Clave unidad SAT
                $datos['precio_adquisicion'],
                $datos['impuesto_iva'],
                $datos['categoria_id']
            );

            if (!$stmt->execute()) throw new Exception("Error al insertar producto");
            
            $productoId = $this->db->insert_id;
// 🔹 CREAR OPCIONES DE MEDIDA AUTOMÁTICAS

$factor = floatval($datos['factor_conversion']);

$medidas = [
    [
        'nombre'       => $datos['unidad_reporte'],
        'equivalencia' => ($factor > 0 ? (1 / $factor) : 1)
    ],
    [
        'nombre'       => $datos['unidad_medida'],
        'equivalencia' => 1
    ]
];

foreach ($medidas as $medida) {

    $this->guardarOpcionMedida([
        'producto_id'  => $productoId,
        'nombre'       => $medida['nombre'],
        'equivalencia' => $medida['equivalencia']
    ]);
}

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
public function crearProducto($data)
{
    $this->db->begin_transaction();

    try {

        // 🔹 VALIDAR SKU
        $checkSku = $this->db->prepare("SELECT id FROM productos WHERE sku = ?");
        if (!$checkSku) throw new Exception("Error prepare SKU");

        $checkSku->bind_param("s", $data['sku']);
        $checkSku->execute();

        if ($checkSku->get_result()->num_rows > 0) {
            throw new Exception("El SKU '{$data['sku']}' ya está registrado.");
        }

        // 🔹 INSERT PRODUCTO
        $stmtProd = $this->db->prepare("INSERT INTO productos 
            (sku, nombre, descripcion, unidad_medida, unidad_reporte, factor_conversion, fiscal_clave_prod, fiscal_clave_unidad, precio_adquisicion, impuesto_iva, categoria_id) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        if (!$stmtProd) throw new Exception("Error prepare producto");

        $stmtProd->bind_param(
            "sssssdsssdd",
            $data['sku'],
            $data['nombre'],
            $data['descripcion'],
            $data['unidad_medida'],
            $data['unidad_reporte'],
            $data['factor_conversion'],
            $data['fiscal_clave_prod'],
            $data['fiscal_clave_unit'],
            $data['precio_adquisicion'],
            $data['impuesto_iva'],
            $data['categoria_id']
        );

        if (!$stmtProd->execute()) {
            throw new Exception("Error al insertar producto: " . $stmtProd->error);
        }

        $producto_id = $this->db->insert_id;
        // 🔹 CREAR OPCIÓN DE MEDIDA AUTOMÁTICA
// 🔹 CREAR OPCIONES DE MEDIDA AUTOMÁTICAS

$factor = floatval($data['factor_conversion']);

$medidas = [
    [
        'nombre'       => $data['unidad_reporte'],
        'equivalencia' => ($factor > 0 ? (1 / $factor) : 1)
    ],
    [
        'nombre'       => $data['unidad_medida'],
        'equivalencia' => 1
    ]
];

foreach ($medidas as $medida) {

    $this->guardarOpcionMedida([
        'producto_id'  => $producto_id,
        'nombre'       => $medida['nombre'],
        'equivalencia' => $medida['equivalencia']
    ]);
}

        // 🔹 ALMACENES
        foreach ($data['almacenes'] as $almacen_id => $datos) {

           $stock = isset($datos['stock']) ? floatval($datos['stock']) : 0;

$min = floatval($datos['stock_minimo'] ?? 0);

// 👉 si no hay stock, precios en 0
if ($stock <= 0) {
    $p_minorista = 0;
    $p_mayorista = 0;
    $p_distribuidor = 0;
} else {
    $p_minorista = floatval($datos['precio_minorista'] ?? 0);
    $p_mayorista = floatval($datos['precio_mayorista'] ?? 0);
    $p_distribuidor = floatval($datos['precio_distribuidor'] ?? 0);
}
            // INVENTARIO
            $stmtInv = $this->db->prepare("INSERT INTO inventario 
                (almacen_id, producto_id, stock, stock_minimo) 
                VALUES (?, ?, ?, ?)");

            if (!$stmtInv) throw new Exception("Error prepare inventario");

            $stmtInv->bind_param("iidd", $almacen_id, $producto_id, $stock, $min);

            if (!$stmtInv->execute()) {
                throw new Exception("Error inventario: " . $stmtInv->error);
            }
            
if ($stock > 0) {
            // 🔹 PROTEGER DIVISIÓN
            $precioIndividual = $stock > 0 ? ($data['precio_adquisicion'] / $stock) : 0;

            $codigo_lote = "L-" . $data['sku'] . "-" . date('His');

            $stmtLote = $this->db->prepare("INSERT INTO lotes_stock 
                (producto_id, almacen_id, codigo_lote, cantidad_inicial, cantidad_actual, precio_compra_unitario, estado_lote) 
                VALUES (?, ?, ?, ?, ?, ?, 'activo')");

            if (!$stmtLote) throw new Exception("Error prepare lote");

            $stmtLote->bind_param("iisddd", $producto_id, $almacen_id, $codigo_lote, $stock, $stock, $precioIndividual);

            if (!$stmtLote->execute()) {
                throw new Exception("Error lote: " . $stmtLote->error);
            }

            // PRECIOS
            $stmtPre = $this->db->prepare("INSERT INTO precios_producto 
                (producto_id, almacen_id, precio_minorista, precio_mayorista, precio_distribuidor) 
                VALUES (?, ?, ?, ?, ?)");

            if (!$stmtPre) throw new Exception("Error prepare precios");

            $stmtPre->bind_param("iiddd", $producto_id, $almacen_id, $p_minorista, $p_mayorista, $p_distribuidor);

            if (!$stmtPre->execute()) {
                throw new Exception("Error precios: " . $stmtPre->error);
            }

            // MOVIMIENTO
            $obs = "Carga inicial (Lote: $codigo_lote)";

            $stmtMov = $this->db->prepare("INSERT INTO movimientos 
                (producto_id, tipo, cantidad, almacen_destino_id, usuario_registra_id, observaciones) 
                VALUES (?, 'entrada', ?, ?, ?, ?)");

            if (!$stmtMov) throw new Exception("Error prepare movimiento");

            $stmtMov->bind_param(
                "idiis",
                $producto_id,
                $stock,
                $almacen_id,
                $data['usuario_id'],
                $obs
            );

            if (!$stmtMov->execute()) {
                throw new Exception("Error movimiento: " . $stmtMov->error);
            }
        }
        }

        $this->db->commit();
        return ['status' => true];

    } catch (Exception $e) {

        $this->db->rollback();
        error_log("ERROR crearProducto: " . $e->getMessage());

        return [
            'status' => false,
            'msg' => $e->getMessage()
        ];
    }
}
public function obtenerProductoPorAlmacen($productoId, $almacenId)
{
    try {
        $sql = "SELECT 
                    p.id, 
                    p.sku, 
                    p.nombre, 
                    p.descripcion, 
                    p.categoria_id, 
                    p.unidad_medida, 
                    p.unidad_reporte, 
                    p.factor_conversion,
                    p.fiscal_clave_prod, 
                    p.fiscal_clave_unidad, 
                    p.impuesto_iva,
                    i.stock, 
                    i.stock_minimo, 
                    a.nombre AS almacen_nombre,
                    pp.precio_minorista, 
                    pp.precio_mayorista, 
                    pp.precio_distribuidor
                FROM productos p
                INNER JOIN inventario i 
                    ON p.id = i.producto_id
                INNER JOIN almacenes a 
                    ON i.almacen_id = a.id
                LEFT JOIN precios_producto pp 
                    ON p.id = pp.producto_id 
                    AND pp.almacen_id = a.id
                WHERE p.id = ? 
                AND i.almacen_id = ?
                LIMIT 1";

        $stmt = $this->db->prepare($sql);

        if (!$stmt) {
            throw new Exception("Error al preparar consulta: " . $this->db->error);
        }

        $stmt->bind_param("ii", $productoId, $almacenId);

        if (!$stmt->execute()) {
            throw new Exception("Error al ejecutar consulta: " . $stmt->error);
        }

        $resultado = $stmt->get_result();

        if ($resultado->num_rows === 0) {
            return [
                'status' => false,
                'msg' => 'Producto no encontrado en este almacén'
            ];
        }

        $producto = $resultado->fetch_assoc();

        // 🔹 Tipado limpio (evita bugs en frontend)
        $producto['id'] = (int)$producto['id'];
        $producto['categoria_id'] = (int)$producto['categoria_id'];
        $producto['factor_conversion'] = (float)$producto['factor_conversion'];
        $producto['stock'] = (float)$producto['stock'];
        $producto['stock_minimo'] = (float)$producto['stock_minimo'];
        $producto['precio_minorista'] = (float)$producto['precio_minorista'];
        $producto['precio_mayorista'] = (float)$producto['precio_mayorista'];
        $producto['precio_distribuidor'] = (float)$producto['precio_distribuidor'];

        return [
            'status' => true,
            'data' => $producto
        ];

    } catch (Exception $e) {

        error_log("ERROR obtenerProductoPorAlmacen: " . $e->getMessage());

        return [
            'status' => false,
            'msg' => 'Error interno del servidor'
        ];
    }
}
public function actualizarProductoCompleto($data)
{
    $this->db->begin_transaction();

    try {
        // 🔹 1. UPDATE productos (Siempre existe porque viene de una edición)
        $sqlProd = "UPDATE productos SET 
                        sku = ?, nombre = ?, descripcion = ?, categoria_id = ?, 
                        unidad_medida = ?, unidad_reporte = ?, factor_conversion = ?, 
                        fiscal_clave_prod = ?, fiscal_clave_unidad = ?, impuesto_iva = ? 
                    WHERE id = ?";

        $stmt1 = $this->db->prepare($sqlProd);
        $stmt1->bind_param(
            "sssisssdssi",
            $data['sku'], $data['nombre'], $data['descripcion'], $data['categoria_id'],
            $data['unidad_medida'], $data['unidad_reporte'], $data['factor_conversion'],
            $data['fiscal_clave_prod'], $data['fiscal_clave_unit'], $data['impuesto_iva'],
            $data['id']
        );
        $stmt1->execute();

        // 🔹 2. LÓGICA DE PRECIOS: CONSULTAR ANTES DE GUARDAR
        if ($data['aplicar_global']) {
            // Si es global, actualizamos todos los registros que coincidan con el producto
            $sqlPre = "UPDATE precios_producto SET 
                            precio_minorista = ?, precio_mayorista = ?, precio_distribuidor = ? 
                        WHERE producto_id = ?";
            $stmt2 = $this->db->prepare($sqlPre);
            $stmt2->bind_param("dddi", 
                $data['precio_minorista'], $data['precio_mayorista'], $data['precio_distribuidor'], 
                $data['id']
            );
            $stmt2->execute();
        } else {
            // 1. Consultar si existe el registro para este producto en este almacén
            $sqlCheckPre = "SELECT id FROM precios_producto WHERE producto_id = ? AND almacen_id = ?";
            $stmtCheck = $this->db->prepare($sqlCheckPre);
            $stmtCheck->bind_param("ii", $data['id'], $data['almacen_id']);
            $stmtCheck->execute();
            $resPre = $stmtCheck->get_result();

            if ($resPre->num_rows > 0) {
                // 2a. SI EXISTE -> UPDATE
                $sqlPre = "UPDATE precios_producto SET 
                                precio_minorista = ?, precio_mayorista = ?, precio_distribuidor = ? 
                            WHERE producto_id = ? AND almacen_id = ?";
                $stmt2 = $this->db->prepare($sqlPre);
                $stmt2->bind_param("dddii", 
                    $data['precio_minorista'], $data['precio_mayorista'], $data['precio_distribuidor'], 
                    $data['id'], $data['almacen_id']
                );
            } else {
                // 2b. NO EXISTE -> INSERT
                $sqlPre = "INSERT INTO precios_producto 
                            (producto_id, almacen_id, precio_minorista, precio_mayorista, precio_distribuidor) 
                           VALUES (?, ?, ?, ?, ?)";
                $stmt2 = $this->db->prepare($sqlPre);
                $stmt2->bind_param("iiddd", 
                    $data['id'], $data['almacen_id'], 
                    $data['precio_minorista'], $data['precio_mayorista'], $data['precio_distribuidor']
                );
            }
            $stmt2->execute();
        }

        // 🔹 3. LÓGICA DE INVENTARIO: CONSULTAR ANTES DE GUARDAR
        $sqlCheckInv = "SELECT id FROM inventario WHERE producto_id = ? AND almacen_id = ?";
        $stmtCheckInv = $this->db->prepare($sqlCheckInv);
        $stmtCheckInv->bind_param("ii", $data['id'], $data['almacen_id']);
        $stmtCheckInv->execute();
        $resInv = $stmtCheckInv->get_result();

        if ($resInv->num_rows > 0) {
            // SI EXISTE -> UPDATE
            $sqlInv = "UPDATE inventario SET stock = ?, stock_minimo = ? 
                       WHERE producto_id = ? AND almacen_id = ?";
            $stmt3 = $this->db->prepare($sqlInv);
            $stmt3->bind_param("ddii", $data['stock'], $data['stock_minimo'], $data['id'], $data['almacen_id']);
        } else {
            // NO EXISTE -> INSERT
            $sqlInv = "INSERT INTO inventario (producto_id, almacen_id, stock, stock_minimo) VALUES (?, ?, ?, ?)";
            $stmt3 = $this->db->prepare($sqlInv);
            $stmt3->bind_param("iidd", $data['id'], $data['almacen_id'], $data['stock'], $data['stock_minimo']);
        }
        $stmt3->execute();

        $this->db->commit();
        return ['status' => true];

    } catch (Exception $e) {
        $this->db->rollback();
        return ['status' => false, 'msg' => $e->getMessage()];
    }
}
public function guardarOpcionMedida($datos) {

    $sqlOpcionesMedida = "
        INSERT INTO opciones_de_medida_adicional 
        (
            producto_id,
            nombre,
            equivalencia
        )
        VALUES (?, ?, ?)
    ";

    $stmtOpcionesMedida = $this->db->prepare($sqlOpcionesMedida);

    if (!$stmtOpcionesMedida) {

        return [
            'success' => false,
            'message' => 'Error al preparar consulta'
        ];
    }

    $producto_id  = intval($datos['producto_id'] ?? 0);
    $nombre       = trim($datos['nombre'] ?? '');
    $equivalencia = floatval($datos['equivalencia'] ?? 0);

    // VALIDACIONES
    if ($producto_id <= 0) {

        return [
            'success' => false,
            'message' => 'Producto inválido'
        ];
    }

    if ($nombre === '') {

        return [
            'success' => false,
            'message' => 'Nombre requerido'
        ];
    }

    // Evitar equivalencia 0
    if ($equivalencia <= 0) {

        return [
            'success' => false,
            'message' => 'La equivalencia debe ser mayor a 0'
        ];
    }

    $stmtOpcionesMedida->bind_param(
        "isd",
        $producto_id,
        $nombre,
        $equivalencia
    );

    if ($stmtOpcionesMedida->execute()) {

        return [
            'success' => true,
            'message' => 'Opción de medida registrada',
            'id' => $stmtOpcionesMedida->insert_id
        ];
    }

    return [
        'success' => false,
        'message' => $stmtOpcionesMedida->error
    ];
}
// 🔥 ACTUALIZAR MEDIDA ADICIONAL
public function actualizarMedidaAdicional($id, $producto_id, $nombre, $equivalencia)
{
    $sql = "UPDATE opciones_de_medida_adicional 
            SET producto_id = ?, 
                nombre = ?, 
                equivalencia = ?
            WHERE id = ?";

    $stmt = $this->db->prepare($sql);

    if (!$stmt) {
        return [
            'status' => false,
            'message' => $this->db->error
        ];
    }

    $stmt->bind_param(
        "isdi",
        $producto_id,
        $nombre,
        $equivalencia,
        $id
    );

    if ($stmt->execute()) {
        return [
            'status' => true,
            'message' => 'Medida actualizada correctamente'
        ];
    }

    return [
        'status' => false,
        'message' => $stmt->error
    ];
}
public function listarMedidas($producto_id)
{
    $medidas = [];
    // Filtramos solo por producto_id
    $sql = "SELECT * FROM opciones_de_medida_adicional WHERE producto_id = ? ORDER BY id DESC";

    $stmt = $this->db->prepare($sql);

    if (!$stmt) {
        return [
            'status' => false,
            'message' => "Error en la preparación: " . $this->db->error
        ];
    }

    // "i" indica que el parámetro es un entero (integer)
    $stmt->bind_param("i", $producto_id);

    if ($stmt->execute()) {
        // Obtenemos el set de resultados
        $resultado = $stmt->get_result();

        // Llenamos el array con los registros encontrados
        while ($h = $resultado->fetch_assoc()) {
            $medidas[] = $h;
        }

        return [
            'status' => true,
            'medidas' => $medidas
        ];
    }

    return [
        'status' => false,
        'message' => "Error en ejecución: " . $stmt->error
    ];
}

// 🔥 ELIMINAR MEDIDA ADICIONAL
public function eliminarMedidaAdicional($id)
{
    $sql = "DELETE FROM opciones_de_medida_adicional 
            WHERE id = ?";

    $stmt = $this->db->prepare($sql);

    if (!$stmt) {
        return [
            'status' => false,
            'message' => $this->db->error
        ];
    }

    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        return [
            'status' => true,
            'message' => 'Medida eliminada correctamente'
        ];
    }

    return [
        'status' => false,
        'message' => $stmt->error
    ];
}
public function obtenerMedidas()
{
    $medidas = [];

    $sql = "
        SELECT 
            id,
            producto_id,
            nombre,
            equivalencia
        FROM opciones_de_medida_adicional
    ";

    $resultado = $this->db->query($sql);

    if (!$resultado) {

        die($this->db->error);
    }

    while ($row = $resultado->fetch_assoc()) {

        $medidas[] = $row;
    }

    return $medidas;
}
}