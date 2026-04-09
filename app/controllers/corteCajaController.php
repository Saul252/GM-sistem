<?php
/**
 * Controlador para el Corte de Caja
 * Ubicación: controllers/corteCajaController.php
 */

require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../controllers/LayoutController.php';
require_once __DIR__ . '/../models/corteCajaModel.php';

// Verificación de permisos
protegerPagina('corteCaja');

$modelo = new CorteCajaModel($conexion);
$almacen_usuario = $_SESSION['almacen_id'] ?? 0;

// --- MANEJO DE PETICIÓN AJAX ---
if (isset($_GET['ajax'])) {
    try {
        // Aseguramos que la salida sea JSON limpio
        if (ob_get_length()) ob_clean();
        header('Content-Type: application/json');
        
        // 1. Obtener el almacén objetivo
        // Prioridad: 1. El valor seleccionado en el filtro (incluso si es 0), 2. El almacén del usuario
        $almacen_target = isset($_GET['almacen_id']) ? intval($_GET['almacen_id']) : $almacen_usuario;
        
        // 2. Preparar los filtros adicionales (periodo, fechas, etc.)
        $filtros = $_GET;

        // 3. Obtener las ventas detalladas para la tabla principal
        // Pasamos el $almacen_target para que la tabla también se filtre
        $ventas = $modelo->obtenerVentasDetalladas($filtros, $almacen_target);
        
        // 4. Obtener los totales calculados (Ventas netas, Gastos, Compras, Arqueos)
        $totales = $modelo->obtenerSumasCorte($filtros, $almacen_target);
        
        // 5. Respuesta exitosa
        echo json_encode([
            'status' => 'success',
            'data'   => $ventas,  // Listado para el tbody de la tabla
            'sumas'  => $totales  // Datos para las tarjetas superiores y gráficas
        ]);
        
    } catch (Exception $e) {
        // En caso de error (ej. columna inexistente en SQL), devolvemos el error al JS
        http_response_code(500);
        echo json_encode([
            'status' => 'error',
            'error'  => $e->getMessage()
        ]);
    }
    exit;
}

// --- CARGA NORMAL DE LA PÁGINA (VISTA) ---
$paginaActual = 'Corte de Caja';

/**
 * Nota: La vista debe contener los selectores con los IDs:
 * #almacen_id
 * #periodo
 */
require_once __DIR__ . '/../views/corteCaja_view.php';