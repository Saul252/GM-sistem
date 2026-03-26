<?php
require_once __DIR__ . '/../../includes/auth.php';
protegerPagina();
require_once __DIR__ . '/../controllers/LayoutController.php';
require_once __DIR__ . '/../../includes/permisos.php';
$paginaActual = 'Inicio';

// OPTIMIZACIÓN: Centralizamos los módulos en un array para evitar repetir HTML
$modulos = [
    ['id' => 'ventas', 'url' => '/cfsistem/app/controllers/ventasController.php', 'icon' => 'bi-cart-check', 'class' => 'icon-ventas', 'label' => 'Ventas', 'desc' => 'Salidas'],
    ['id' => 'compras', 'url' => '/cfsistem/app/controllers/egresosController.php', 'icon' => 'bi-bag-check', 'class' => 'icon-compras', 'label' => 'Compras', 'desc' => 'Entradas'],
    ['id' => 'proveedores', 'url' => '/cfsistem/app/controllers/proveedoresController.php', 'icon' => 'bi-person-vcard', 'class' => 'icon-proveedores', 'label' => 'Proveedores', 'desc' => 'Gestión'],
    ['id' => 'almacenes', 'url' => '/cfsistem/app/controllers/almacenes.php', 'icon' => 'bi-box-seam', 'class' => 'icon-almacen', 'label' => 'Almacenes', 'desc' => 'Inventario'],
    ['id' => 'movimientos', 'url' => '/cfsistem/app/controllers/movimientosController.php', 'icon' => 'bi-arrow-left-right', 'class' => 'icon-movimientos', 'label' => 'Movimientos', 'desc' => 'Kardex'],
    ['id' => 'ventashistorial', 'url' => '/cfsistem/app/controllers/ventasHistorialController.php', 'icon' => 'bi-receipt', 'class' => 'icon-historial', 'label' => 'Historial', 'desc' => 'Facturación'],
    ['id' => 'usuarios', 'url' => '/cfsistem/app/controllers/usuariosController.php', 'icon' => 'bi-people', 'class' => 'icon-usuarios', 'label' => 'Usuarios', 'desc' => 'Accesos'],
    ['id' => 'Mermas', 'url' => '/cfsistem/app/controllers/mermasController.php', 'icon' => 'bi-exclamation-triangle', 'class' => 'icon-mermas', 'label' => 'Mermas', 'desc' => 'Pérdidas'],
    ['id' => 'transmutaciones', 'url' => '/cfsistem/app/controllers/transmutacionesController.php', 'icon' => 'bi-arrow-repeat', 'class' => 'icon-transmutaciones', 'label' => 'Conversiones', 'desc' => 'Procesos'],
    ['id' => 'clientes', 'url' => '/cfsistem/app/controllers/clientesController.php', 'icon' => 'bi-person-lines-fill', 'class' => 'icon-clientes', 'label' => 'Clientes', 'desc' => 'Cartera'],
    ['id' => 'finanzas', 'url' => '/cfsistem/app/controllers/finanzasController.php', 'icon' => 'bi-graph-up-arrow', 'class' => 'text-primary', 'label' => 'Finanzas', 'desc' => 'Estado financiero'],
    ['id' => 'entregas', 'url' => '/cfsistem/app/controllers/entregasController.php', 'icon' => 'bi-truck', 'class' => 'text-warning', 'label' => 'Despachos', 'desc' => 'Salida física'],
    ['id' => 'clientesEstatus', 'url' => '/cfsistem/app/controllers/clientesEstatusController.php', 'icon' => 'bi-person-check', 'class' => 'text-success', 'label' => 'Estatus', 'desc' => 'Créditos'],
    // Módulo agregado y estilizado
    ['id' => 'corteCaja', 'url' => '/cfsistem/app/controllers/corteCajaController.php', 'icon' => 'bi-cash-stack', 'class' => 'text-success', 'label' => 'Corte Caja', 'desc' => 'Cierres diarios'],
    ['id' => 'solicitudesCompra', 'url' => '/cfsistem/app/controllers/solicitudesCompraController.php', 'icon' => 'bi-cart-check-fill', 'class' => 'text-info', 'label' => 'Requisiciones', 'desc' => 'Sol. Compra'],
    ['id' => 'trabajadores', 'url' => '/cfsistem/app/controllers/trabajadoresController.php', 'icon' => 'bi-people-fill', 'class' => 'text-primary', 'label' => 'Trabajadores', 'desc' => 'Recursos H.'],
    ['id' => 'vehiculos', 'url' => '/cfsistem/app/controllers/vehiculosController.php', 'icon' => 'bi-truck-front-fill', 'class' => 'text-secondary', 'label' => 'Vehículos', 'desc' => 'Control Flota'],
    ['id' => 'repartos', 'url' => '/cfsistem/app/controllers/repartosController.php', 'icon' => 'bi-truck-flatbed', 'class' => 'text-info', 'label' => 'Repartos', 'desc' => 'Monitor Ruta'],
    ['id' => 'pedidosVendedor', 'url' => '/cfsistem/app/controllers/pedidosVendedorController.php', 'icon' => 'bi-person-badge-fill', 'class' => 'text-warning', 'label' => 'Pedidos Vendedor', 'desc' => 'Preventa'],
    ];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Inicio - Sistema de Almacenes</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="/cfsistem/css/inicio.css" rel="stylesheet">
     <?php require_once __DIR__ . '/layout/icono.php' ?>
    <?php if (function_exists('cargarEstilos')) { cargarEstilos(); } ?>

<style>
    :root { 
        --sidebar-width: 260px; 
        --navbar-height: 65px; 
        --ios-bg: #f2f2f7;
    }
    
    body { 
        background-color: var(--ios-bg); 
        font-family: -apple-system, BlinkMacSystemFont, "SF Pro Text", "Segoe UI", Roboto, sans-serif; 
        overflow-x: hidden;
    }
    
    .main-content { 
        margin-left: var(--sidebar-width); 
        padding: 30px; 
        padding-top: calc(var(--navbar-height) + 20px); 
        min-height: 100vh;
        transition: all 0.3s ease;
    }

    /* ESTILO IOS CARD REFINADO */
    .card-modulo {
        background: #ffffff;
        border: none;
        border-radius: 24px; /* Curva más estética */
        padding: 1.5rem 1rem;
        height: 100%;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 10px rgba(0,0,0,0.04);
    }

    .icono-modulo {
        font-size: 2.8rem; /* Un poco más grande para que no se vea vacío */
        margin-bottom: 10px;
        display: block;
    }

    .modulo-titulo { 
        font-weight: 700; 
        font-size: 0.95rem; 
        color: #1c1c1e; 
        margin-bottom: 3px;
        letter-spacing: -0.3px;
    }

    .modulo-desc { 
        font-size: 0.75rem; 
        color: #8e8e93; 
        font-weight: 400;
        line-height: 1.2;
    }

    /* --- AJUSTES PARA MÓVIL (ELIMINA EL EFECTO DE LEJANÍA) --- */
    @media (max-width: 768px) { 
        .main-content { 
            margin-left: 0 !important; 
            padding: 15px 12px; /* Reducimos padding lateral para ganar espacio */
            padding-top: calc(var(--navbar-height) + 15px); 
        }

        /* Forzamos 2 columnas que ocupen bien el ancho */
        .row-cols-2 > * {
            padding-left: 6px !important;
            padding-right: 6px !important;
        }

        .icono-modulo {
            font-size: 2.4rem;
        }

        h2 { font-size: 1.6rem !important; }

        /* Efecto de presión al tocar (Feedback táctil) */
        .card-modulo:active {
            transform: scale(0.94);
            background-color: #f9f9fb;
        }
    }

    /* Hover solo para Desktop */
    @media (min-width: 992px) {
        .card-modulo:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 25px rgba(0,0,0,0.08);
        }
    }
</style>

<body>
    <?php if (function_exists('renderizarLayout')) { renderizarLayout($paginaActual); } ?>

    <main class="main-content">
        <div class="container-fluid p-0">
            <div class="d-flex align-items-center mb-4 header-section px-2">
                <div class="p-3 bg-white rounded-4 shadow-sm me-3 d-none d-sm-block">
                    <i class="bi bi-grid-1x2-fill text-primary fs-4"></i>
                </div>
                <div>
                    <h2 class="fw-bold m-0" style="letter-spacing: -0.8px;">Panel Principal</h2>
                    <p class="text-muted mb-0" style="font-size: 0.9rem;">Gestión de recursos cfsistem</p>
                </div>
            </div>

            <div class="row g-2 g-md-4 row-cols-2 row-cols-sm-3 row-cols-md-3 row-cols-lg-4 row-cols-xl-6">
                
                <?php foreach ($modulos as $m): ?>
                    <?php if (puedeVerModulo($m['id'])): ?>
                    <div class="col">
                        <a href="<?= $m['url'] ?>" class="text-decoration-none h-100 d-block">
                            <div class="card card-modulo text-center">
                                <i class="bi <?= $m['icon'] ?> icono-modulo <?= $m['class'] ?>"></i>
                                <span class="modulo-titulo"><?= $m['label'] ?></span>
                                <span class="modulo-desc text-truncate w-100"><?= $m['desc'] ?></span>
                            </div>
                        </a>
                    </div>
                    <?php endif; ?>
                <?php endforeach; ?>

            </div>
        </div>
    </main>
</body>
   <!--< ?php > if (puedeVerModulo('corteCaja')): ?> -->
               <!-- 
                <div class="col">
                    <a href="/cfsistem/app/controllers/corteCajaController.php" class="text-decoration-none h-100 d-block">
                        <div class="card card-modulo text-center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="#27ae60" viewBox="0 0 16 16" class="mb-2">
                                <path d="M1 4a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v1H1V4z"/>
                                <path d="M15 5v10h1V5h-1zM1 5v10h1V5H1zM3 5v10h10V5H3zM2 14h12v1H2v-1z"/>
                                <path d="M6 1h4v1H6V1z"/>
                                <path d="M3 6h10v1H3V6zm0 2h10v1H3V8zm0 2h10v1H3v-1zm0 2h10v1H3v-1z"/>
                            </svg>
                            <span class="modulo-titulo">Corte Caja</span>
                            <span class="modulo-desc">Cierres</span>
                        </div>
                    </a>
                </div>
                -->
                 <!-- < ?php endif; ?> -->