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
public function listarTodosViewClientes($almacen_id) {
    if ($almacen_id == 0) {
        // ADMIN: Trae todos, pero agrupados por RFC para no ver 4 veces "Público General"
        // O simplemente todos si quieres ver el detalle de a qué almacén pertenecen.
        $sql = "SELECT * FROM clientes 
              
                ORDER BY (rfc = 'XAXX010101000') DESC, nombre_comercial ASC";
        return $this->db->query($sql);
    } 
    
    // VENDEDOR: Filtro ESTRICTO.
    // Solo trae los clientes cuyo almacen_id coincida EXACTAMENTE con el del usuario.
    $sql = "SELECT * FROM clientes 
           
            WHERE almacen_id = ?
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
    $almacen_id_sesion = $_SESSION['almacen_id'] ?? 0;

    // 1. Validación de RFC duplicado
    // Buscamos si el RFC ya existe en OTRO registro que no sea este ($id)
    $checkSql = "SELECT id FROM clientes 
                 WHERE rfc = ? AND id != ? AND activo = 1";
    
    // Si no es admin, limitamos la búsqueda de duplicados a su propio almacén
    if ($almacen_id_sesion > 0) {
        $checkSql .= " AND almacen_id = " . intval($almacen_id_sesion);
    }

    $stmtCheck = $this->db->prepare($checkSql);
    $stmtCheck->bind_param("si", $datos['rfc'], $id);
    $stmtCheck->execute();
    
    if ($stmtCheck->get_result()->num_rows > 0) {
        throw new Exception("El RFC ingresado ya está registrado con otro cliente.");
    }

    // 2. Construcción dinámica del SQL
    // Empezamos con los campos básicos
    $campos = [
        "nombre_comercial = ?", "razon_social = ?", "rfc = ?", 
        "regimen_fiscal = ?", "codigo_postal = ?", "correo = ?", 
        "telefono = ?", "direccion = ?", "uso_cfdi = ?"
    ];
    $params = [
        $datos['nombre_comercial'], $datos['razon_social'], $datos['rfc'],
        $datos['regimen_fiscal'], $datos['codigo_postal'], $datos['correo'],
        $datos['telefono'], $datos['direccion'], $datos['uso_cfdi']
    ];
    $tipos = "sssssssss";

    // --- EL CAMBIO CLAVE ---
    // Si el usuario es ADMIN y envió un nuevo almacen_id, lo agregamos al UPDATE
    if ($almacen_id_sesion == 0 && isset($datos['almacen_id'])) {
        $campos[] = "almacen_id = ?";
        $params[] = intval($datos['almacen_id']);
        $tipos .= "i";
    }

    $sql = "UPDATE clientes SET " . implode(", ", $campos) . " WHERE id = ?";
    
    // Seguridad para no-admins
    if ($almacen_id_sesion > 0) {
        $sql .= " AND almacen_id = " . intval($almacen_id_sesion);
    }

    // Agregamos el ID al final de los parámetros
    $params[] = $id;
    $tipos .= "i";

    $stmt = $this->db->prepare($sql);
    
    // Usamos call_user_func_array porque el número de parámetros es dinámico (si cambió almacén o no)
    $stmt->bind_param($tipos, ...$params);

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
 public function getResumenClientes($almacen_id) {
    // 1. Limpiamos el ID
    $id = intval($almacen_id);

    // 2. CONTEO GLOBAL (Esto es lo que ve el Admin)
    // Contamos todos los clientes activos sin importar a qué almacén pertenecen
    $sqlGlobal = "SELECT COUNT(*) as total FROM clientes ";
    $queryGlobal = $this->db->query($sqlGlobal);
    $totalSistema = ($queryGlobal) ? intval($queryGlobal->fetch_assoc()['total']) : 0;

    // 3. RETORNO DE LÓGICA
    if ($id === 0) {
        // --- CASO ADMINISTRADOR ---
        // Para el Admin, "mis_clientes" es igual al "total_sistema"
        return [
            "tipo"          => "admin",
            "nombre"        => "Control Global",
            "mis_clientes"  => $totalSistema, 
            "total_sistema" => $totalSistema
        ];
    } else {
        // --- CASO VENDEDOR (ID > 0) ---
        // Solo contamos los que le pertenecen a SU almacén
        $sqlLocal = "SELECT COUNT(*) as total FROM clientes WHERE almacen_id = $id ";
        $queryLocal = $this->db->query($sqlLocal);
        $totalLocal = ($queryLocal) ? intval($queryLocal->fetch_assoc()['total']) : 0;

        // Traemos el nombre del almacén para el footer del widget
        $sqlNom = "SELECT nombre FROM almacenes WHERE id = $id LIMIT 1";
        $resNom = $this->db->query($sqlNom)->fetch_assoc();

        return [
            "tipo"          => "vendedor",
            "nombre"        => $resNom['nombre'] ?? 'Sucursal',
            "mis_clientes"  => $totalLocal,
            "total_sistema" => $totalSistema
        ];
    }
}
    }
