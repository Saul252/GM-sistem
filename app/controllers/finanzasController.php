<?php
// 1. Requerimientos de sesión y conexión
require_once __DIR__ . '/../../includes/auth.php'; 
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../controllers/LayoutController.php';
require_once __DIR__ . '/../models/finanzasModel.php';

// 2. Cargar el Modelo
protegerPagina('finanzas');
$model = new FinanzasModel($conexion);

// 3. Obtener Datos (Lógica del Controlador)
$kpis = $model->getKPIs();
$totalVentas = $kpis['ventas_mes'] ?? 0;
$totalEgresos = ($kpis['compras_mes'] ?? 0) + ($kpis['gastos_mes'] ?? 0);
$utilidad = $totalVentas - $totalEgresos;

$resAlmacenes = $model->getStockAlmacenes();
$resTopProd = $model->getTopProductos();
$resCritico = $model->getStockCritico();
$pendientes = $model->getPendientes();
$resUsuarios = $model->getUsuariosActivos();
$totalUsuarios = $resUsuarios->num_rows; // Guardamos el conteo en una variable simple
$paginaActual = 'finanzas';

// 4. "Llamar" a la Vista
// Al incluirla aquí, la vista tiene acceso a todas las variables arriba definidas
require_once __DIR__ . '/../views/finanzas_view.php';