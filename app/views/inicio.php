<?php
require_once __DIR__ . '/../../includes/auth.php';
protegerPagina();
require_once __DIR__ . '/../controllers/LayoutController.php';
require_once __DIR__ . '/../../includes/permisos.php';
$paginaActual = 'Inicio';

// OPTIMIZACIÓN: Centralizamos los módulos en un array para evitar repetir HTML
$modulos = [

    // =========================
    // VENTAS Y CLIENTES
    // =========================
    ['id' => 'ventas', 'url' => '/cfsistem/app/controllers/ventasController.php', 'icon' => 'bi-cart-check', 'class' => 'icon-ventas', 'label' => 'Ventas', 'desc' => 'Salidas'],
    ['id' => 'remisiones', 'url' => '/cfsistem/app/controllers/requisicionesController.php', 'icon' => 'bi-receipt-cutoff', 'class' => 'icon-ventas', 'label' => 'Remisiones', 'desc' => 'Remisiones'],
   
    ['id' => 'cajaRapida', 'url' => '/cfsistem/app/controllers/cajaRapidaController.php', 'icon' => 'bi-lightning-charge', 'class' => 'icon-ventas', 'label' => 'Caja Rapida', 'desc' => 'Salidas en existencia'],
    ['id' => 'cotizaciones', 'url' => '/cfsistem/app/controllers/cotizacionesController.php', 'icon' => 'bi-file-earmark-text', 'class' => 'icon-clientes', 'label' => 'Cotizaciones', 'desc' => 'Cotizaciones'],
    ['id' => 'clientes', 'url' => '/cfsistem/app/controllers/clientesController.php', 'icon' => 'bi-person-lines-fill', 'class' => 'icon-clientes', 'label' => 'Clientes', 'desc' => 'Cartera'],
    ['id' => 'clientesEstatus', 'url' => '/cfsistem/app/controllers/clientesEstatusController.php', 'icon' => 'bi-person-check', 'class' => 'text-success', 'label' => 'Estatus', 'desc' => 'Créditos'],
       ['id' => 'ventasVendedor', 'url' => '/cfsistem/app/controllers/historialPedidosVendedorController.php', 'icon' => 'bi-person-badge-fill','class' => 'text-success', 'label' => 'Ventas Vendedor', 'desc' => 'Ventas Vendedor'],
   ['id' => 'ventashistorial', 'url' => '/cfsistem/app/controllers/ventasHistorialController.php', 'icon' => 'bi-receipt', 'class' => 'icon-historial', 'label' => 'Historial de Ventas', 'desc' => 'Historial de Ventas'],
['id' => 'comprobantes', 'url' => '/cfsistem/app/controllers/comprobantesPagoController.php', 'icon' => 'bi-file-earmark-check', 'class' => 'icon-historial', 'label' => 'Crear comprobante de pagos', 'desc' => 'Crear comprobantes de pago'],

    ['id' => 'registrarPagos', 'url' => '/cfsistem/app/controllers/registrarPagosController.php', 'icon' => 'bi-credit-card', 'class' => 'icon-historial', 'label' => 'Registrar Pagos a Ventas', 'desc' => 'Registrar Pagos a ventas'],

    // =========================
    // COMPRAS Y PROVEEDORES
    // =========================
    ['id' => 'compras', 'url' => '/cfsistem/app/controllers/egresosController.php', 'icon' => 'bi-bag-check', 'class' => 'icon-compras', 'label' => 'Compras y Gastos', 'desc' => 'Entradas'],
    ['id' => 'proveedores', 'url' => '/cfsistem/app/controllers/proveedoresController.php', 'icon' => 'bi-person-vcard', 'class' => 'icon-proveedores', 'label' => 'Proveedores', 'desc' => 'Gestión'],
    ['id' => 'solicitudesCompra', 'url' => '/cfsistem/app/controllers/solicitudesCompraController.php', 'icon' => 'bi-cart-check-fill', 'class' => 'text-info', 'label' => 'Solicitudes de Compra', 'desc' => 'Sol. Compra'],

    // =========================
    // INVENTARIO Y ALMACÉN
    // =========================
    ['id' => 'almacenes', 'url' => '/cfsistem/app/controllers/almacenes.php', 'icon' => 'bi-box-seam', 'class' => 'icon-almacen', 'label' => 'Almacenes', 'desc' => 'Inventario'],
    ['id' => 'movimientos', 'url' => '/cfsistem/app/controllers/movimientosController.php', 'icon' => 'bi-arrow-left-right', 'class' => 'icon-movimientos', 'label' => 'Movimientos', 'desc' => 'Kardex'],
    ['id' => 'Mermas', 'url' => '/cfsistem/app/controllers/mermasController.php', 'icon' => 'bi-exclamation-triangle', 'class' => 'icon-mermas', 'label' => 'Mermas', 'desc' => 'Pérdidas'],
    ['id' => 'transmutaciones', 'url' => '/cfsistem/app/controllers/transmutacionesController.php', 'icon' => 'bi-arrow-repeat', 'class' => 'icon-transmutaciones', 'label' => 'Conversiones', 'desc' => 'Procesos'],
    ['id' => 'historialLotes', 'url' => '/cfsistem/app/controllers/lotesHistorialController.php', 'icon' => 'bi-clock-history', 'label' => 'Historial de Lotes', 'desc' => 'Historial de lotes'],
['id' => 'comprasHistorial', 'url' => '/cfsistem/app/controllers/comprasHistorialController.php', 'icon' => 'bi-collection', 'label' => 'Historial de compras', 'desc' => 'Historial de compras'],

    // =========================
    // FINANZAS Y TESORERÍA
    // =========================
    ['id' => 'finanzas', 'url' => '/cfsistem/app/controllers/finanzasController.php', 'icon' => 'bi-graph-up-arrow', 'class' => 'text-primary', 'label' => 'Finanzas', 'desc' => 'Estado financiero'],
    ['id' => 'finanzas_admin', 'url' => '/cfsistem/app/controllers/finanzasAdmController.php', 'icon' => 'bi-bar-chart-line', 'class' => 'text-dark', 'label' => 'Finanzas Admin', 'desc' => 'Control general'],
    ['id' => 'prestamos', 'url' => '/cfsistem/app/controllers/prestamosController.php', 'icon' => 'bi-cash-coin', 'class' => 'text-success', 'label' => 'Préstamos', 'desc' => 'Control de préstamos'],
    ['id' => 'corteCaja', 'url' => '/cfsistem/app/controllers/corteCajaController.php', 'icon' => 'bi-cash-stack', 'class' => 'text-success', 'label' => 'Corte Caja', 'desc' => 'Cierres diarios'],
    ['id' => 'tesoreria', 'url' => '/cfsistem/app/controllers/tesoreriaController.php', 'icon' => 'bi-safe', 'class' => 'text-secondary', 'label' => 'Tesorería', 'desc' => 'Fondos y bancos'],

    // =========================
    // LOGÍSTICA Y DISTRIBUCIÓN
    // =========================
    ['id' => 'entregas', 'url' => '/cfsistem/app/controllers/entregasController.php', 'icon' => 'bi-truck', 'class' => 'text-warning', 'label' => 'Despachos', 'desc' => 'Salida física'],
    ['id' => 'vehiculos', 'url' => '/cfsistem/app/controllers/vehiculosController.php', 'icon' => 'bi-truck-front-fill', 'class' => 'text-secondary', 'label' => 'Vehículos', 'desc' => 'Control Flota'],
    ['id' => 'mantenimientos', 'url' => '/cfsistem/app/controllers/mantenimientosController.php', 'icon' => 'bi-wrench-adjustable-circle-fill', 'class' => 'text-secondary', 'label' => 'mantenimientos', 'desc' => 'Control Flota'],
    ['id' => 'repartos', 'url' => '/cfsistem/app/controllers/repartosController.php', 'icon' => 'bi-truck-flatbed', 'class' => 'text-info', 'label' => 'Repartos', 'desc' => 'Monitor Ruta'],
    ['id' => 'misRepartos', 'url' => '/cfsistem/app/controllers/misRepartosController.php', 'icon' => 'bi-map-fill', 'class' => 'text-primary', 'label' => 'Mis repartos', 'desc' => 'Repartos activos y evidencias'],

    // =========================
    // RECURSOS HUMANOS
    // =========================
    ['id' => 'nomina', 'url' => '/cfsistem/app/controllers/nominaController.php', 'icon' => 'bi-cash', 'class' => 'text-primary', 'label' => 'nomina', 'desc' => 'Salarios.'],
   ['id' => 'trabajadores', 'url' => '/cfsistem/app/controllers/trabajadoresController.php', 'icon' => 'bi-people-fill', 'class' => 'text-primary', 'label' => 'Trabajadores', 'desc' => 'Recursos H.'],
    ['id' => 'viajesTrabajadores', 'url' => '/cfsistem/app/controllers/viajesTrabajadoresController.php', 'icon' => 'bi-person-workspace', 'class' => 'text-primary', 'label' => 'Viajes Trabajadores', 'desc' => 'Historial de viajes por trabajador'],

    // =========================
    // ADMINISTRACIÓN
    // =========================
    ['id' => 'usuarios', 'url' => '/cfsistem/app/controllers/usuariosController.php', 'icon' => 'bi-people', 'class' => 'icon-usuarios', 'label' => 'Usuarios', 'desc' => 'Accesos'],

];
?><!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
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
        border-radius: 20px; /* Curva nativa iOS */
        padding: 1.25rem 0.75rem;
        height: 100%;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 12px rgba(0,0,0,0.03);
    }

    .icono-modulo {
        font-size: 2.4rem; 
        margin-bottom: 8px;
        display: inline-block;
    }

    .modulo-titulo { 
        font-weight: 600; 
        font-size: 0.9rem; 
        color: #1c1c1e; 
        margin-bottom: 2px;
        letter-spacing: -0.2px;
        line-height: 1.2;
        /* Permitir quiebre de palabra corto si es muy largo en móviles */
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .modulo-desc { 
        font-size: 0.72rem; 
        color: #8e8e93; 
        font-weight: 400;
        line-height: 1.2;
        padding: 0 4px;
    }

    /* --- AJUSTES PARA MÓVIL REFINADO --- */
    @media (max-width: 576px) { 
        .main-content { 
            margin-left: 0 !important; 
            padding: 12px 10px; 
            padding-top: calc(var(--navbar-height) + 12px); 
        }

        /* Espaciado perfecto de rejilla de 2 columnas sin romper paddings padres */
        .grid-responsiva {
            --bs-gutter-x: 0.6rem !important;
            --bs-gutter-y: 0.6rem !important;
        }

        .card-modulo {
            padding: 1rem 0.5rem;
            border-radius: 16px; /* Bordes ligeramente más reducidos en móvil */
        }

        .icono-modulo {
            font-size: 2.1rem;
            margin-bottom: 6px;
        }

        .modulo-titulo {
            font-size: 0.82rem;
        }

        .modulo-desc {
            font-size: 0.68rem;
        }

        h2 { font-size: 1.4rem !important; }

        /* Efecto de presión táctil nativo de iOS */
        .card-modulo:active {
            transform: scale(0.95);
            background-color: #f2f2f7;
            opacity: 0.9;
        }
    }

    /* Hover exclusivo de escritorio */
    @media (min-width: 992px) {
        .card-modulo:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 24px rgba(0,0,0,0.06);
        }
    }
</style>
</head>
<body>

    <?php if (function_exists('renderizarLayout')) { renderizarLayout($paginaActual); } ?>

    <main class="main-content">
        <div class="container-fluid p-0">
            
            <div class="d-flex align-items-center mb-3 mb-md-4 header-section px-1">
                <div class="p-3 bg-white rounded-4 shadow-sm me-3 d-none d-sm-block">
                    <i class="bi bi-grid-1x2-fill text-primary fs-4"></i>
                </div>
                <div>
                    <h2 class="fw-bold m-0" style="letter-spacing: -0.6px;">Panel Principal</h2>
                    <p class="text-muted mb-0" style="font-size: 0.85rem;">Gestión de recursos cfsistem</p>
                </div>
            </div>

            <div class="row grid-responsiva row-cols-2 row-cols-sm-3 row-cols-md-3 row-cols-lg-4 row-cols-xl-6 g-3">
                
                <?php foreach ($modulos as $m): ?>
                    <?php if (puedeVerModulo($m['id'])): ?>
                    <div class="col">
                        <a href="<?= $m['url'] ?>" class="text-decoration-none h-100 d-block">
                            <div class="card card-modulo text-center">
                                <i class="bi <?= $m['icon'] ?> icono-modulo <?= $m['class'] ?? 'text-primary' ?>"></i>
                                <span class="modulo-titulo"><?= $m['label'] ?></span>
                                <span class="modulo-desc text-truncate w-100"><?= $m['desc'] ?? '' ?></span>
                            </div>
                        </a>
                    </div>
                    <?php endif; ?>
                <?php endforeach; ?>

            </div>
        </div>
    </main>

    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
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