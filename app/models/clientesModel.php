<?php
class ClientesModel {
    private $db;

    public function __construct($conexion) {
        $this->db = $conexion;
    }

public function listarTodos($almacen_id) {
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
            AND almacen_id = ?
            ORDER BY (rfc = 'XAXX010101000') DESC, nombre_comercial ASC";
            
    $stmt = $this->db->prepare($sql);
    $stmt->bind_param("i", $almacen_id);
    $stmt->execute();
    return $stmt->get_result();
}

 public function guardar($datos) {
    // 1. Lógica de asignación de Almacén
    $almacen_id_sesion = $_SESSION['almacen_id'] ?? 0; 
    
    if ($almacen_id_sesion == 0) {
        $almacen_id_insertar = (!empty($datos['almacen_id'])) ? intval($datos['almacen_id']) : null;
    } else {
        $almacen_id_insertar = $almacen_id_sesion;
    }

    // 2. Validación de RFC duplicado (Corregida para usar el almacén de destino)
    // Buscamos si el RFC ya existe en el almacén donde se va a guardar
    $checkSql = "SELECT id FROM clientes WHERE rfc = ? AND almacen_id = ? AND activo = 1";
    $stmtCheck = $this->db->prepare($checkSql);
    $stmtCheck->bind_param("si", $datos['rfc'], $almacen_id_insertar);
    $stmtCheck->execute();
    
    if ($stmtCheck->get_result()->num_rows > 0) {
        throw new Exception("El RFC ya está registrado en la sucursal seleccionada.");
    }

    // 3. Generamos Token y Estado
    $api_token = bin2hex(random_bytes(16)); 
    $activo = 1;

    // 4. Limpieza de datos (Prevenir errores de bind_param con nulos)
    $razon_social   = !empty($datos['razon_social']) ? $datos['razon_social'] : null;
    $regimen_fiscal = !empty($datos['regimen_fiscal']) ? $datos['regimen_fiscal'] : null;
    $correo         = !empty($datos['correo']) ? $datos['correo'] : null;
    $telefono       = !empty($datos['telefono']) ? $datos['telefono'] : null;
    $direccion      = !empty($datos['direccion']) ? $datos['direccion'] : null;
    $uso_cfdi       = !empty($datos['uso_cfdi']) ? $datos['uso_cfdi'] : 'G03';

    // 5. Inserción
    $sql = "INSERT INTO clientes (
        nombre_comercial, razon_social, rfc, regimen_fiscal, 
        codigo_postal, correo, telefono, direccion, uso_cfdi, 
        almacen_id, api_token, activo
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $this->db->prepare($sql);

    // sssssssssisi -> 12 parámetros
    $stmt->bind_param("sssssssssisi", 
        $datos['nombre_comercial'], // 1
        $razon_social,              // 2 (Limpiado)
        $datos['rfc'],              // 3
        $regimen_fiscal,            // 4 (Limpiado)
        $datos['codigo_postal'],    // 5
        $correo,                    // 6 (Limpiado)
        $telefono,                  // 7 (Limpiado)
        $direccion,                 // 8 (Limpiado)
        $uso_cfdi,                  // 9 (Limpiado)
        $almacen_id_insertar,       // 10
        $api_token,                 // 11
        $activo                     // 12
    );

    if ($stmt->execute()) {
        return [
            'success' => true, 
            'id' => $this->db->insert_id, 
            'api_token' => $api_token,
            'message' => 'Cliente guardado correctamente'
        ];
    }
    
    return false;
}
 public function actualizar($id, $datos) {
    // 1. Obtenemos el ID de la sesión (0 si es Admin)
    $almacen_id_sesion = $_SESSION['almacen_id'] ?? 0;

    // 2. Validación de RFC (Aislamiento de seguridad)
    // El Admin (0) valida contra toda la tabla, el Usuario solo contra su almacén
    $checkSql = "SELECT id FROM clientes 
                 WHERE rfc = ? AND (? = 0 OR almacen_id = ?) AND id != ? AND activo = 1";
    $stmtCheck = $this->db->prepare($checkSql);
    $stmtCheck->bind_param("siii", $datos['rfc'], $almacen_id_sesion, $almacen_id_sesion, $id);
    $stmtCheck->execute();
    
    if ($stmtCheck->get_result()->num_rows > 0) {
        throw new Exception("El RFC ingresado ya está registrado con otro cliente.");
    }

    // 3. Ejecutar la actualización de datos personales
    // NOTA: No incluimos 'almacen_id' en el SET para que se respete el original
    $sql = "UPDATE clientes SET 
                nombre_comercial = ?, 
                razon_social = ?, 
                rfc = ?, 
                regimen_fiscal = ?, 
                codigo_postal = ?, 
                correo = ?, 
                telefono = ?, 
                direccion = ?, 
                uso_cfdi = ?
            WHERE id = ?";
    
    // Si NO es administrador, añadimos el candado de seguridad
    // para que un vendedor no pueda editar clientes de otro almacén vía URL
    if ($almacen_id_sesion > 0) {
        $sql .= " AND almacen_id = " . intval($almacen_id_sesion);
    }

    $stmt = $this->db->prepare($sql);

    // Contamos: 9 campos de datos + 1 ID = 10 parámetros
    $stmt->bind_param("sssssssssi", 
        $datos['nombre_comercial'], 
        $datos['razon_social'], 
        $datos['rfc'],
        $datos['regimen_fiscal'], 
        $datos['codigo_postal'], 
        $datos['correo'],
        $datos['telefono'], 
        $datos['direccion'], 
        $datos['uso_cfdi'], 
        $id
    );

    return $stmt->execute();
}
public function cambiarEstado($id, $estado, $almacen_id = 0) {
        // SQL con candado de seguridad por almacén
        $sql = "UPDATE clientes SET activo = ? WHERE id = ?";
        if ($almacen_id > 0) {
            $sql .= " AND almacen_id = " . intval($almacen_id);
        }

        // Aquí es donde fallaba: $this->db debe ser el objeto de conexión
        $stmt = $this->db->prepare($sql); 
        $stmt->bind_param("ii", $estado, $id);
        
        if ($stmt->execute()) {
            return $stmt->affected_rows > 0;
        }
        return false;
    }

    public function obtenerPorId($id, $almacen_id = 0) {
        $sql = "SELECT * FROM clientes WHERE id = ?";
        if ($almacen_id > 0) {
            $sql .= " AND (almacen_id = " . intval($almacen_id) . " OR rfc = 'XAXX010101000')";
        }

        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
    }
