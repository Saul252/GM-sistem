
<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once __DIR__ . '/../../includes/auth.php';
protegerPagina(); 

require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../includes/sidebar.php';

$paginaActual = 'Clientes';

$usosCFDI = [
    'G01' => 'Adquisición de mercancías',
    'G03' => 'Gastos en general',
    'I01' => 'Construcciones',
    'P01' => 'Por definir',
    'S01' => 'Sin efectos fiscales'
];

// Carga la vista y le pasa las variables anteriores
include __DIR__ . '/../views/clientes_view.php';