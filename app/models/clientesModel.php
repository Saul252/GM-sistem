<?php
class ClientesModel {
    private $db;

    public function __construct($conexion) {
        $this->db = $conexion;
    }

    public function listarTodos($almacen_id) {
    // Explicación: 
    // - Si el admin consulta (0), ve todos.
    // - Si un almacén consulta, ve los suyos O el de Público en General (RFC genérico).
    $sql = "SELECT * FROM clientes 
            WHERE (? = 0 OR almacen_id = ? OR rfc = 'XAXX010101000') 
            AND activo = 1 
            ORDER BY (rfc = 'XAXX010101000') DESC, nombre_comercial ASC";
    
    $stmt = $this->db->prepare($sql);
    $stmt->bind_param("ii", $almacen_id, $almacen_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $clientes = [];
    while ($row = $result->fetch_assoc()) {
        $clientes[] = $row;
    }
    return $clientes;
    }

 public function guardar($datos) {
    // 1. Lógica de asignación de Almacén
    $almacen_id_sesion = $_SESSION['almacen_id'] ?? 0; 
    
    // Si el Admin (0) mandó un almacen_id en el formulario, usamos ese.
    // Si es un vendedor (>0), usamos forzosamente su ID de sesión.
    if ($almacen_id_sesion == 0) {
        $almacen_id_insertar = (!empty($datos['almacen_id'])) ? intval($datos['almacen_id']) : null;
    } else {
        $almacen_id_insertar = $almacen_id_sesion;
    }

    // 2. Validación de RFC duplicado (Jerarquía Admin/Sucursal)
    $checkSql = "SELECT id FROM clientes WHERE rfc = ? AND (? = 0 OR almacen_id = ?) AND activo = 1";
    $stmtCheck = $this->db->prepare($checkSql);
    $stmtCheck->bind_param("sii", $datos['rfc'], $almacen_id_sesion, $almacen_id_sesion);
    $stmtCheck->execute();
    
    if ($stmtCheck->get_result()->num_rows > 0) {
        throw new Exception("El RFC ya está registrado en este almacén.");
    }

    // 3. Generamos Token y Estado
    $api_token = bin2hex(random_bytes(16)); 
    $activo = 1;

    // 4. Inserción (12 columnas y 12 placeholders)
    $sql = "INSERT INTO clientes (
        nombre_comercial, razon_social, rfc, regimen_fiscal, 
        codigo_postal, correo, telefono, direccion, uso_cfdi, 
        almacen_id, api_token, activo
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $this->db->prepare($sql);

    // sssssssssisi -> 12 parámetros
    $stmt->bind_param("sssssssssisi", 
        $datos['nombre_comercial'], // 1
        $datos['razon_social'],     // 2
        $datos['rfc'],              // 3
        $datos['regimen_fiscal'],   // 4
        $datos['codigo_postal'],    // 5
        $datos['correo'],           // 6
        $datos['telefono'],         // 7
        $datos['direccion'],        // 8
        $datos['uso_cfdi'],         // 9
        $almacen_id_insertar,       // 10
        $api_token,                 // 11
        $activo                     // 12
    );

    if ($stmt->execute()) {
        return ['success' => true, 'id' => $this->db->insert_id, 'api_token' => $api_token];
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

    public function obtenerPorId($id) {
        $stmt = $this->db->prepare("SELECT * FROM clientes WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function cambiarEstado($id, $estado) {
        $stmt = $this->db->prepare("UPDATE clientes SET activo = ? WHERE id = ?");
        $stmt->bind_param("ii", $estado, $id);
        return $stmt->execute();
    }
    }
