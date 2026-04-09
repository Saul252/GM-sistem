<?php
// 1. Reporte de errores para desarrollo (quitar en producción)
ini_set('display_errors', 1);
error_reporting(E_ALL);

// 2. Seguridad y Sesión
require_once __DIR__ . '/../../../includes/auth.php';
require_once __DIR__ . '/../../../config/conexion.php';
require_once __DIR__ . '/../../models/corteCajaModel.php';

header('Content-Type: application/json');

// IMPORTANTE: Hora de CDMX
date_default_timezone_set('America/Mexico_City');

$action = $_GET['action'] ?? '';

if ($action === 'check_sistema') {
    $corteModel = new CorteCajaModel($conexion);
    
    $hoy = date('Y-m-d');
    $hora_actual = (int)date('H'); // Formato 24h

    $respuesta = [
        'status' => 'idle',
        'corte_status' => 'pendiente',
        'detalles' => []
    ];

    // CAMBIO: Ahora validamos que sean las 18:00 (6 PM) o más
    if ($hora_actual >= 18) {
        
        // 1. Obtener almacenes activos
        $sql_alm = "SELECT id, nombre FROM almacenes WHERE estado = 1";
        $res_alm = $conexion->query($sql_alm);
        
        $resumen = [];

        while ($alm = $res_alm->fetch_assoc()) {
            $id_alm = $alm['id'];
            $nombre_alm = $alm['nombre'];

            // 2. ¿Ya existe corte para ESTE almacén hoy?
            if (!$corteModel->existeCorte($hoy, $id_alm)) {
                
                // 3. Ejecutar el registro individual
                $ejecucion = $corteModel->registrarCortePorAlmacen($id_alm);
                
                $resumen[] = [
                    'almacen' => $nombre_alm,
                    'status'  => $ejecucion['status'],
                    'accion'  => 'NUEVO_CORTE_GENERADO'
                ];
            } else {
                $resumen[] = [
                    'almacen' => $nombre_alm,
                    'status'  => 'success',
                    'accion'  => 'YA_EXISTIA'
                ];
            }
        }

        $respuesta['status'] = 'success';
        $respuesta['corte_status'] = 'realizado';
        $respuesta['detalles'] = $resumen;
        $respuesta['mensaje'] = 'Proceso de cierre de las 18:00 finalizado.';

    } else {
        $respuesta['mensaje'] = 'Esperando a las 18:00 para el cierre automático.';
        $respuesta['hora_servidor'] = date('H:i:s');
    }

    echo json_encode($respuesta);
}