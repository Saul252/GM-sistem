<?php
// 1. Tus archivos de conexión y seguridad
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../controllers/LayoutController.php';

// 2. Importar el Modelo
require_once __DIR__ . '/../models/egresos_model.php';

// Verificamos permiso para este módulo
$paginaActual = 'compras'; 

$egresoModel = new EgresoModel($conexion); // $conexion viene de tu config/conexion.php

// --- LÓGICA PARA CARGAR LA VISTA (GET) ---
if ($_SERVER['REQUEST_METHOD'] === 'GET' && !isset($_GET['action'])) {
    $fecha_desde = $_GET['desde'] ?? date('Y-m-01');
    $fecha_hasta = $_GET['hasta'] ?? date('Y-m-d');
    $filtro_usuario = $_GET['usuario_busqueda'] ?? null;

    // Obtener datos unificados
    $egresos = $egresoModel->obtenerTodosLosEgresos($fecha_desde, $fecha_hasta);

    // Calcular Totales para los KPIs
    $totalSumCompras = 0;
    $totalSumGastos = 0;
    foreach ($egresos as $e) {
        if ($e['tipo'] == 'compra') $totalSumCompras += $e['total'];
        else $totalSumGastos += $e['total'];
    }
    $granTotalEgresos = $totalSumCompras + $totalSumGastos;
    $almacenes = $egresoModel->obtenerAlmacenesActivos(); // Crea este método simple en tu modelo

    // Cargar la Vista
    $tituloPagina = "Gestión de Egresos";
    require_once __DIR__ . '/../views/egresos_view.php';
}
// --- DENTRO DE LA SECCIÓN GUARDAR GASTO ---
// --- DENTRO DE egresosController.php ---
if (isset($_GET['action']) && $_GET['action'] === 'guardarGasto') {
    header('Content-Type: application/json');
    try {
        // 1. DETERMINAR ALMACÉN
        $rol_id = $_SESSION['rol_id'] ?? 0;
        if ($rol_id == 1) { 
            $almacen_final = intval($_POST['almacen_id'] ?? 0);
        } else {
            $almacen_final = intval($_SESSION['almacen_id'] ?? 0);
        }

        if ($almacen_final <= 0) {
            throw new Exception("Error: El almacén destino no es válido.");
        }

        // 2. PROCESAR ARCHIVO (Asegurar que la variable exista siempre)
   $urlDocumento = null;

// DEBUG: Vamos a verificar si PHP siquiera recibió el archivo
if (!isset($_FILES['documento'])) {
    // Si llegas aquí, el formulario no tiene el enctype="multipart/form-data"
} else if ($_FILES['documento']['error'] !== UPLOAD_ERR_OK) {
    // Si el error es 4, es que no seleccionaste archivo. Si es otro, es un problema de PHP.
    if ($_FILES['documento']['error'] !== 4) {
        throw new Exception("Error de PHP al subir: " . $_FILES['documento']['error']);
    }
} else {
    // Si hay archivo y no hay error de PHP, procedemos
    $nombreOriginal = $_FILES['documento']['name'];
    $rutaTemporal = $_FILES['documento']['tmp_name'];
    
    // USAMOS RUTA RELATIVA para evitar errores de DOCUMENT_ROOT
    // Ajusta los "../" según qué tan profundo esté tu controlador
  // Esto construye la ruta completa desde la raíz del sistema (/opt/lampp/htdocs/...)
$rutaCarpeta = $_SERVER['DOCUMENT_ROOT'] . "/cfsistem/uploads/evidencias/";

if (!is_dir($rutaCarpeta)) {
    mkdir($rutaCarpeta, 0777, true);
    // Si se crea la carpeta por código, hay que asegurar el permiso de nuevo
    chmod($rutaCarpeta, 0777); 
}
   

    $ext = pathinfo($nombreOriginal, PATHINFO_EXTENSION);
    $nuevoNombre = "GASTO_" . time() . "_" . uniqid() . "." . $ext;
    $destino = $rutaCarpeta . $nuevoNombre;

    if (move_uploaded_file($rutaTemporal, $destino)) {
        $urlDocumento = $nuevoNombre;
    } else {
        // ESTO TE DIRÁ SI ES UN PROBLEMA DE PERMISOS
        $error_fisico = error_get_last();
        throw new Exception("No se pudo mover a la carpeta. ¿Tiene permisos de escritura? Detalle: " . $error_fisico['message']);
    }
}

        // 3. ARMAR CABECERA
        $cabecera = [
            'folio'        => $_POST['folio'] ?? 'S/F',
            'fecha'        => date('Y-m-d'),
            'almacen_id'   => $almacen_final,
            'usuario_id'   => $_SESSION['user_id'] ?? 1,
            'beneficiario' => $_POST['beneficiario'] ?? '',
            'metodo_pago'  => $_POST['metodo_pago'] ?? 'Efectivo',
            'total'        => $_POST['total_final'] ?? 0,
            'documento_url'=> $urlDocumento,
            'observaciones'=> $_POST['observaciones'] ?? ''
        ];

        // 4. EJECUTAR MODELO (Solo una vez)
        // Pasamos los datos y capturamos si el modelo lanza una excepción
        $resultado = $egresoModel->registrarGasto(
            $cabecera, 
            $_POST['desc'] ?? [], 
            $_POST['cant'] ?? [], 
            $_POST['precio'] ?? []
        );

        if ($resultado === true) {
            echo json_encode(['success' => true, 'message' => 'Gasto guardado correctamente']);
        } else {
            throw new Exception("El modelo devolvió un error inesperado.");
        }

    } catch (Exception $e) {
        // Aquí atrapamos CUALQUIER error de arriba y lo mandamos al SweetAlert
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}