<?php

class MermaModel {

    private $conn;

    public function __construct($conexion)
    {
        $this->conn = $conexion;
    }

    /* ===============================
    OBTENER PRODUCTOS CON STOCK
    =============================== */
    public function obtenerProductosPorAlmacen($almacen_id)
    {
        $sql = "
        SELECT 
            p.id,
            p.nombre,
            p.unidad_medida,
            p.factor_conversion,
            i.stock
        FROM inventario i
        JOIN productos p ON p.id = i.producto_id
        WHERE i.almacen_id = ?
        AND i.stock > 0
        ORDER BY p.nombre
        ";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i",$almacen_id);
        $stmt->execute();

        return $stmt->get_result();
    }


    /* ===============================
    OBTENER STOCK
    =============================== */
    public function obtenerStock($producto_id,$almacen_id)
    {
        $sql = "
        SELECT stock
        FROM inventario
        WHERE producto_id = ?
        AND almacen_id = ?
        ";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii",$producto_id,$almacen_id);
        $stmt->execute();

        $res = $stmt->get_result();
        return $res->fetch_assoc();
    }


    /* ===============================
    OBTENER FACTOR CONVERSION
    =============================== */
    public function obtenerFactorConversion($producto_id)
    {
        $sql = "
        SELECT factor_conversion
        FROM productos
        WHERE id = ?
        ";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i",$producto_id);
        $stmt->execute();

        $res = $stmt->get_result();
        return $res->fetch_assoc();
    }


    /* ===============================
    REGISTRAR MERMA SIMPLE
    =============================== */
    public function registrarMerma($data)
    {

        $producto_id = $data['producto_id'];
        $almacen_id = $data['almacen_id'];
        $cantidad = $data['cantidad'];
        $tipo_merma = $data['tipo_merma'];
        $responsable = $data['responsable'];
        $descripcion = $data['descripcion'];
        $usuario_id = $data['usuario_id'];

        $stock = $this->obtenerStock($producto_id,$almacen_id);

        if(!$stock || $cantidad > $stock['stock']){
            return ["error" => "Stock insuficiente"];
        }

        $this->conn->begin_transaction();

        try {

            /* MOVIMIENTO */
            $sqlMov = "
            INSERT INTO movimientos
            (producto_id,tipo,cantidad,almacen_origen_id,usuario_registra_id,observaciones)
            VALUES (?,?,?,?,?,?)
            ";

            $tipo = "salida";
            $obs = "Merma registrada";

            $stmt = $this->conn->prepare($sqlMov);
            $stmt->bind_param(
                "isdiis",
                $producto_id,
                $tipo,
                $cantidad,
                $almacen_id,
                $usuario_id,
                $obs
            );

            $stmt->execute();

            $movimiento_id = $this->conn->insert_id;


            /* ACTUALIZAR INVENTARIO */
            $sqlInv = "
            UPDATE inventario
            SET stock = stock - ?
            WHERE producto_id = ?
            AND almacen_id = ?
            ";

            $stmt2 = $this->conn->prepare($sqlInv);
            $stmt2->bind_param("dii",$cantidad,$producto_id,$almacen_id);
            $stmt2->execute();


            /* REGISTRAR MERMA */
            $sqlMerma = "
            INSERT INTO mermas
            (movimiento_id,almacen_id,producto_id,cantidad,tipo_merma,responsable_declaracion,descripcion_suceso)
            VALUES (?,?,?,?,?,?,?)
            ";

            $stmt3 = $this->conn->prepare($sqlMerma);
            $stmt3->bind_param(
                "iiidsss",
                $movimiento_id,
                $almacen_id,
                $producto_id,
                $cantidad,
                $tipo_merma,
                $responsable,
                $descripcion
            );

            $stmt3->execute();

            $this->conn->commit();

            return ["success"=>true];

        } catch (Exception $e){

            $this->conn->rollback();
            return ["error"=>$e->getMessage()];
        }

    }



    /* ===============================
    CONVERSION DE PRODUCTO
    =============================== */
    public function convertirProducto($data)
    {

        $producto_origen = $data['producto_origen'];
        $producto_destino = $data['producto_destino'];
        $cantidad_origen = $data['cantidad_origen'];
        $cantidad_destino = $data['cantidad_destino'];
        $almacen_id = $data['almacen_id'];
        $usuario_id = $data['usuario_id'];
        $descripcion = $data['descripcion'];
        $responsable = $data['responsable'];

        /* VALIDAR STOCK */

        $stock = $this->obtenerStock($producto_origen,$almacen_id);

        if(!$stock || $cantidad_origen > $stock['stock']){
            return ["error"=>"Stock insuficiente"];
        }

        /* VALIDAR FACTOR */

        $factor = $this->obtenerFactorConversion($producto_origen);

        $factor_conversion = $factor['factor_conversion'];

        $maximo = $cantidad_origen * $factor_conversion;

        if($cantidad_destino > $maximo){

            return [
                "error" => "Cantidad excede el máximo permitido ($maximo)"
            ];

        }

        $this->conn->begin_transaction();

        try {

            /* SALIDA PRODUCTO ORIGINAL */

            $sqlSalida = "
            INSERT INTO movimientos
            (producto_id,tipo,cantidad,almacen_origen_id,usuario_registra_id,observaciones)
            VALUES (?,?,?,?,?,?)
            ";

            $tipo = "salida";
            $obs = "Salida por conversión de merma";

            $stmt1 = $this->conn->prepare($sqlSalida);

            $stmt1->bind_param(
                "isdiis",
                $producto_origen,
                $tipo,
                $cantidad_origen,
                $almacen_id,
                $usuario_id,
                $obs
            );

            $stmt1->execute();

            $movimiento_id = $this->conn->insert_id;


            /* ENTRADA PRODUCTO CONVERTIDO */

            $sqlEntrada = "
            INSERT INTO movimientos
            (producto_id,tipo,cantidad,almacen_destino_id,usuario_registra_id,observaciones)
            VALUES (?,?,?,?,?,?)
            ";

            $tipo2 = "entrada";
            $obs2 = "Entrada por conversión";

            $stmt2 = $this->conn->prepare($sqlEntrada);

            $stmt2->bind_param(
                "isdiis",
                $producto_destino,
                $tipo2,
                $cantidad_destino,
                $almacen_id,
                $usuario_id,
                $obs2
            );

            $stmt2->execute();


            /* INVENTARIO ORIGEN */

            $sqlInv1 = "
            UPDATE inventario
            SET stock = stock - ?
            WHERE producto_id = ?
            AND almacen_id = ?
            ";

            $stmt3 = $this->conn->prepare($sqlInv1);
            $stmt3->bind_param("dii",$cantidad_origen,$producto_origen,$almacen_id);
            $stmt3->execute();


            /* INVENTARIO DESTINO */

            $sqlInv2 = "
            UPDATE inventario
            SET stock = stock + ?
            WHERE producto_id = ?
            AND almacen_id = ?
            ";

            $stmt4 = $this->conn->prepare($sqlInv2);
            $stmt4->bind_param("dii",$cantidad_destino,$producto_destino,$almacen_id);
            $stmt4->execute();


            /* REGISTRAR MERMA */

            $sqlMerma = "
            INSERT INTO mermas
            (movimiento_id,almacen_id,producto_id,cantidad,tipo_merma,responsable_declaracion,descripcion_suceso)
            VALUES (?,?,?,?,?,?,?)
            ";

            $tipo_merma = "daño";

            $stmt5 = $this->conn->prepare($sqlMerma);

            $stmt5->bind_param(
                "iiidsss",
                $movimiento_id,
                $almacen_id,
                $producto_origen,
                $cantidad_origen,
                $tipo_merma,
                $responsable,
                $descripcion
            );

            $stmt5->execute();


            $this->conn->commit();

            return ["success"=>true];

        } catch (Exception $e){

            $this->conn->rollback();
            return ["error"=>$e->getMessage()];
        }

    }



    /* ===============================
    LISTAR MERMAS
    =============================== */
    public function listarMermas($almacen_id = null)
    {

        $sql = "
        SELECT
            m.id,
            a.nombre AS almacen,
            p.nombre AS producto,
            m.cantidad,
            m.tipo_merma,
            m.responsable_declaracion,
            m.descripcion_suceso,
            m.fecha_reporte
        FROM mermas m
        JOIN productos p ON p.id = m.producto_id
        JOIN almacenes a ON a.id = m.almacen_id
        ";

        if($almacen_id){

            $sql .= " WHERE m.almacen_id = ".intval($almacen_id);

        }

        $sql .= " ORDER BY m.fecha_reporte DESC";

        return $this->conn->query($sql);
    }

}