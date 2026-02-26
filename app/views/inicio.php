<?php
require_once __DIR__ . '/../../includes/auth.php';
protegerPagina();

require_once __DIR__ . '/../../includes/sidebar.php';
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
p

</head>

<body>

<?php renderSidebar($paginaActual); ?>

<div class="main">

<h3 class="titulo-panel mb-4">
    <i class="bi bi-house-door me-2"></i> Panel Principal
</h3>

<div class="row g-4">

<!-- VENTAS -->
<?php if (puedeVerModulo('ventas')): ?>
<div class="col-md-4 col-lg-3">
    <a href="ventas.php" class="text-decoration-none text-dark">
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
    <a href="compras.php" class="text-decoration-none text-dark">
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
    <a href="almacenes.php" class="text-decoration-none text-dark">
        <div class="card card-modulo text-center">
            <i class="bi bi-box-seam icono-modulo icon-almacen"></i>
            <h6>Almacenes</h6>
            <small class="text-muted">Inventario y productos</small>
        </div>
    </a>
</div>
<?php endif; ?>

<!-- MOVIMIENTOS -->
<?php if (puedeVerModulo('movimientos')): ?>
<div class="col-md-4 col-lg-3">
    <a href="movimientos.php" class="text-decoration-none text-dark">
        <div class="card card-modulo text-center">
            <i class="bi bi-arrow-left-right icono-modulo icon-movimientos"></i>
            <h6>Movimientos</h6>
            <small class="text-muted">Entradas y salidas</small>
        </div>
    </a>
</div>
<?php endif; ?>

<!-- USUARIOS -->
<?php if (puedeVerModulo('usuarios')): ?>
<div class="col-md-4 col-lg-3">
    <a href="usuarios.php" class="text-decoration-none text-dark">
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
    <a href="mermas.php" class="text-decoration-none text-dark">
        <div class="card card-modulo text-center">
            <i class="bi bi-exclamation-triangle icono-modulo icon-mermas"></i>
            <h6>Mermas</h6>
            <small class="text-muted">Control de pérdidas</small>
        </div>
    </a>
</div>
<?php endif; ?>

</div>
</div>

</body>
</html>