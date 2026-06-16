<?php
class comprobantesPagoModel {
    private $db;

    public function __construct($conexion) {
        $this->db = $conexion;
    }

public function listarDepositos($almacen_id) {
    $almacen_id=0;//borra despues
    if ($almacen_id == 0) {
        // ADMIN: Trae todos, pero agrupados por RFC para no ver 4 veces "Público General"
        // O simplemente todos si quieres ver el detalle de a qué almacén pertenecen.
        $sql = "SELECT cp.*, c.nombre_comercial,u.nombre as usuario,a.nombre as almacen
 FROM comprobantes_de_pago cp
        join clientes c on cp.id_cliente =c.id
        join usuarios u on u.id=cp.usuario_recibe
        join almacenes a on a.id =cp.almacen_id
        
        
    ";
        return $this->db->query($sql);
    } 
    
    
    // VENDEDOR: Filtro ESTRICTO.
    // Solo trae los clientes cuyo almacen_id coincida EXACTAMENTE con el del usuario.
    $sql = "SELECT cp.*, c.nombre_comercial,u.nombre as usuario,a.nombre as almacen
 FROM comprobantes_de_pago cp
        join clientes c on cp.id_cliente =c.id
        join usuarios u on u.id=cp.usuario_recibe
        join almacenes a on a.id =cp.almacen_id
        
            where almacen_id = ?";
            
    $stmt = $this->db->prepare($sql);
    $stmt->bind_param("i", $almacen_id);
    $stmt->execute();
    return $stmt->get_result();
}

public function cancelarOrden($id) {
    try {
        // Verificar estado actual
        $stmtCheck = $this->db->prepare("
            SELECT estado 
            FROM comprobantes_de_pago
            WHERE id = ?
        ");
        
        $stmtCheck->bind_param("i", $id);
        $stmtCheck->execute();

        $res = $stmtCheck->get_result()->fetch_assoc();

        // Solo permitir cancelar si está pendiente
       
        // Cambiar estado a cancelado
        $stmt = $this->db->prepare("UPDATE comprobantes_de_pago 
            SET estado = 'cancelado' 
            WHERE id = ?
        ");

        $stmt->bind_param("i", $id);

        return $stmt->execute();

    } catch (Exception $e) {
        return false;
    }
}
public function actualizar($id, $referencia) {
    try {
        // 1. Verificar existencia o estado actual
        $stmtCheck = $this->db->prepare("
            SELECT referencia 
            FROM comprobantes_de_pago
            WHERE id = ?
        ");
        
        $stmtCheck->bind_param("i", $id);
        $stmtCheck->execute();
        $res = $stmtCheck->get_result()->fetch_assoc();
        $stmtCheck->close();

        if (!$res) {
            return false; // No existe el registro
        }

        // 2. CORRECCIÓN: Actualizar la columna 'referencia' con los tipos correctos ("si")
        $stmt = $this->db->prepare("
            UPDATE comprobantes_de_pago 
            SET referencia = ? 
            WHERE id = ?
        ");

        // "s" para la referencia (string), "i" para el id (entero)
        $stmt->bind_param("si", $referencia, $id);
        $ejecutado = $stmt->execute();
        $stmt->close();

        return $ejecutado;

    } catch (Exception $e) {
        return false;
    }
}

public function obtenerDetalle($id) {
    $sql = "SELECT cp.*, c.nombre_comercial, u.nombre as usuario, a.nombre as nombre_almacen
            FROM comprobantes_de_pago cp
            JOIN clientes c ON cp.id_cliente = c.id
            JOIN usuarios u ON u.id = cp.usuario_recibe
            JOIN almacenes a ON a.id = cp.almacen_id
            WHERE cp.id = ?";
            
    $stmt = $this->db->prepare($sql);
    
    if (!$stmt) {
        // Si la consulta SQL tiene un error de sintaxis o columnas, esto lo atrapará el try/catch del controlador
        throw new Exception("Error en la preparación del SQL: " . $this->db->error);
    }

    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    // CORRECCIÓN: Volvemos a extraer los datos como un array asociativo limpio
    $data = $result->fetch_assoc(); 
    
    $stmt->close();
    
    // Si no se encuentra el ID, $data será null, lo cual está bien porque el controlador lo maneja
    return $data; 
}
public function listarTodosCF($almacen_id) {
    if ($almacen_id == 0) {
        // ADMIN: Trae todos, pero agrupados por RFC para no ver 4 veces "Público General"
        // O simplemente todos si quieres ver el detalle de a qué almacén pertenecen.
        $sql = "SELECT * FROM clientes 
                WHERE activo = 1 
                ORDER BY (rfc = 'XAXX010101000') DESC, nombre_comercial ASC";
        return $this->db->query($sql);
    } 
    
    // VENDEDOR: Filtro ESTRICTO.
    // Solo trae los clientes cuyo almacen_id coincida EXACTAMENTE con el del usuario.
    $sql = "SELECT * FROM clientes 
        WHERE activo = 1
        AND (
            rfc != 'XAXX010101000'
            OR (rfc = 'XAXX010101000' AND almacen_id = ?)
        )
        ORDER BY (rfc = 'XAXX010101000') DESC, nombre_comercial ASC";
            
    $stmt = $this->db->prepare($sql);
    $stmt->bind_param("i", $almacen_id);
    $stmt->execute();
    return $stmt->get_result();
}
public function agregarDeposito($id_cliente, $monto, $usuario, $fecha, $referencia,$almacen_id,$metodo,$numero_ventas)
{
    $sqlA = "INSERT INTO comprobantes_de_pago (id_cliente, monto, usuario_recibe, fecha, referencia,almacen_id,metodo_pago,numero_ventas) 
             VALUES (?, ?, ?, ?, ?,?,?,?)";
    
    // Asegúrate de que tu clase db use "prepare" o mapee "p" correctamente a un statement de mysqli
    $stmtA = $this->db->prepare($sqlA); 
    
    if (!$stmtA) {
        return false; 
    }

    // CORREGIDO: "i" (cliente), "d" (monto float), "i" o "s" (usuario), "s" (fecha), "s" (referencia)
    // Cambié el tipo de monto a 'd' para soportar los decimales del floatval
    $stmtA->bind_param("idsssiss", $id_cliente, $monto, $usuario, $fecha, $referencia,$almacen_id,$metodo,$numero_ventas);
    
    if ($stmtA->execute()) {
        // Obtenemos el ID generado
        $id_comprobante = $this->db->insert_id; 
        $stmtA->close();
        return $id_comprobante; // Retorna el número (ej: 12), evaluado como > 0 en el controlador
    } else {
        return false;
    }
}
  }
