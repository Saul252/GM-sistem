<?php
require_once __DIR__ . '/../../includes/auth.php';
protegerPagina();

require_once __DIR__ . '/../controllers/LayoutController.php';
require_once __DIR__ . '/../../includes/permisos.php';

$paginaActual = 'Inicio';
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Inicio - Sistema de Almacenes</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
<link href="/cfsistem/css/inicio.css" rel="stylesheet">


    <?php if (function_exists('cargarEstilos')) { cargarEstilos(); } ?>

</head>

<body>
 <?php if (function_exists('renderizarLayout')) {
        renderizarLayout($paginaActual); 
    } ?>
<div class="main">

<h3 class="titulo-panel mb-4">
    <i class="bi bi-house-door me-2"></i> Panel Principal
</h3>

<div class="row g-4">

<!-- VENTAS -->
<?php if (puedeVerModulo('ventas')): ?>
<div class="col-md-4 col-lg-3">
    <a href="/cfsistem/app/controllers/ventas.php" class="text-decoration-none text-dark">
        <div class="card card-modulo text-center">
            <i class="bi bi-cart-check icono-modulo icon-ventas"></i>
            <h6>Ventas</h6>
            <small class="text-muted">Venta de productos</small>
        </div>
    </a>
</div>
<?php endif; ?>

<!-- COMPRAS -->
<?php if (puedeVerModulo('compras')): ?>
<div class="col-md-4 col-lg-3">
    <a href="/cfsistem/app/controllers/egresosController.php" class="text-decoration-none text-dark">
        <div class="card card-modulo text-center">
            <i class="bi bi-bag-check icono-modulo icon-compras"></i>
            <h6>Compras</h6>
            <small class="text-muted">Compra de productos</small>
        </div>
    </a>
</div>
<?php endif; ?>

<!-- ALMACENES -->
<?php if (puedeVerModulo('almacenes')): ?>
<div class="col-md-4 col-lg-3">
    <a href="/cfsistem/app/controllers/almacenes.php" class="text-decoration-none text-dark">
        <div class="card card-modulo text-center">
            <i class="bi bi-box-seam icono-modulo icon-almacen"></i>
            <h6>Almacenes</h6>
            <small class="text-muted">Inventario y productos</small>
        </div>
    </a>
</div>
<?php endif; ?>
<!-- Movimientos -->
<?php if (puedeVerModulo('movimientos')): ?>
<div class="col-md-4 col-lg-3">
    <a href="/cfsistem/app/controllers/movimientosController.php" class="text-decoration-none text-dark">
        <div class="card card-modulo text-center p-3">
            <i class="bi bi-arrow-left-right icono-modulo icon-movimientos"></i>
            <h6 class="mt-2">Movimientos</h6>
            <small class="text-muted">Entradas y salidas</small>
        </div>
    </a>
</div>
<?php endif; ?>
<!-- HIstorial de ventas -->
<?php if (puedeVerModulo('ventashistorial')): ?>
<div class="col-md-4 col-lg-3">
    <a href="ventashistorial.php" class="text-decoration-none text-dark">
        <div class="card card-modulo text-center p-3">
            <i class="bi bi-receipt icono-modulo icon-historial"></i>
            <h6 class="mt-2">Historial de ventas</h6>
            <small class="text-muted">Registro de facturación</small>
        </div>
    </a>
</div>
<?php endif; ?>
<!-- USUARIOS -->
<?php if (puedeVerModulo('usuarios')): ?>
<div class="col-md-4 col-lg-3">
    <a href="/cfsistem/app/controllers/usuariosController.php" class="text-decoration-none text-dark">
        <div class="card card-modulo text-center">
            <i class="bi bi-people icono-modulo icon-usuarios"></i>
            <h6>Usuarios</h6>
            <small class="text-muted">Gestión de usuarios</small>
        </div>
    </a>
</div>
<?php endif; ?>

<!-- MERMAS -->
<?php if (puedeVerModulo('mermas')): ?>
<div class="col-md-4 col-lg-3">
    <a href="/cfsistem/app/controllers/mermasController.php" class="text-decoration-none text-dark">
        <div class="card card-modulo text-center">
            <i class="bi bi-exclamation-triangle icono-modulo icon-mermas"></i>
            <h6>Mermas</h6>
            <small class="text-muted">Control de pérdidas</small>
        </div>
    </a>
</div>
<?php endif; ?>
<?php if (puedeVerModulo('clientes')): ?>
<div class="col-md-4 col-lg-3">
    <a href="/cfsistem/app/controllers/clientesController.php" class="text-decoration-none text-dark">
        <div class="card card-modulo text-center">
            <i class="bi bi-person-lines-fill icono-modulo icon-clientes"></i>
            <h6>Clientes</h6>
            <small class="text-muted">Cartera y facturación</small>
        </div>
    </a>
</div>
<?php endif; ?>



<?php if (puedeVerModulo('corteCaja')): ?>
<div class="col-md-4 col-lg-3">
    <a href="/cfsistem/app/controllers/corteCajaController.php" class="text-decoration-none text-dark">
        <div class="card card-modulo text-center">
            <div class="icono-modulo">
                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="#27ae60" viewBox="0 0 16 16">
                    <path d="M1 4a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v1H1V4z"/>
                    <path d="M15 5v10h1V5h-1zM1 5v10h1V5H1zM3 5v10h10V5H3zM2 14h12v1H2v-1z"/>
                    <path d="M6 1h4v1H6V1z"/>
                    <path d="M3 6h10v1H3V6zm0 2h10v1H3V8zm0 2h10v1H3v-1zm0 2h10v1H3v-1z"/>
                </svg>
            </div>
            <h6>Corte de Caja</h6>
            <small class="text-muted">Cierres, ingresos y egresos</small>
        </div>
    </a>
</div>
<?php endif; ?>

<?php if (puedeVerModulo('finanzas')): ?>



<div class="col-md-4 col-lg-3">
    <a href="finanzas.php" class="text-decoration-none text-dark">
        <div class="card card-modulo text-center shadow-sm h-100">
            <i class="bi bi-graph-up-arrow icono-modulo text-primary" style="font-size: 2.5rem; margin-bottom: 10px;"></i>
            <h6 class="fw-bold">Finanzas</h6>
            <small class="text-muted">Estadísticas, Utilidades y Gastos</small>
            
           
        </div>
    </a>
</div>
<?php endif; ?>

<?php if (puedeVerModulo(modulo: 'entregas')): ?>
<div class="col-md-4 col-lg-3">
    <a href="entregasController.php" class="text-decoration-none text-dark">
        <div class="card card-modulo text-center shadow-sm h-100">
            <i class="bi bi-truck icono-modulo text-warning" style="font-size: 2.5rem; margin-bottom: 10px;"></i>
            <h6 class="fw-bold">Despachos</h6>
            <small class="text-muted">Logística, Envíos y Entregas</small>
        </div>
    </a>
</div>
<?php endif; ?>
</div>
</div>

</body>
</html>