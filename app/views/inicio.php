<?php
require_once __DIR__ . '/../../includes/auth.php';
protegerPagina();
require_once __DIR__ . '/../controllers/LayoutController.php';
require_once __DIR__ . '/../../includes/permisos.php';

$paginaActual = 'Inicio';
$archivoActual = basename($_SERVER['PHP_SELF']);

// Estructura de módulos por categoría
$gruposModulos = [
    [
        'id_grupo' => 'ventas_clientes',
        'titulo'   => 'Ventas y Clientes',
        'icono'    => 'bi-cart-check-fill',
        'theme'    => 'emerald',
        'submodulos' => [
            ['id' => 'ventas', 'url' => '/cfsistem/app/controllers/ventasController.php', 'icon' => 'bi-cart-check', 'class' => 'text-success', 'label' => 'Ventas'],
            ['id' => 'remisiones', 'url' => '/cfsistem/app/controllers/requisicionesController.php', 'icon' => 'bi-receipt-cutoff', 'class' => 'text-success', 'label' => 'Remisiones'],
            ['id' => 'cajaRapida', 'url' => '/cfsistem/app/controllers/cajaRapidaController.php', 'icon' => 'bi-lightning-charge-fill', 'class' => 'text-warning', 'label' => 'Caja Rápida'],
            ['id' => 'cotizaciones', 'url' => '/cfsistem/app/controllers/cotizacionesController.php', 'icon' => 'bi-file-earmark-text', 'class' => 'text-info', 'label' => 'Cotizaciones'],
            ['id' => 'clientes', 'url' => '/cfsistem/app/controllers/clientesController.php', 'icon' => 'bi-person-lines-fill', 'class' => 'text-primary', 'label' => 'Clientes'],
            ['id' => 'clientesEstatus', 'url' => '/cfsistem/app/controllers/clientesEstatusController.php', 'icon' => 'bi-person-check', 'class' => 'text-success', 'label' => 'Estatus Clientes'],
            ['id' => 'ventasVendedor', 'url' => '/cfsistem/app/controllers/historialPedidosVendedorController.php', 'icon' => 'bi-person-badge-fill', 'class' => 'text-primary', 'label' => 'Ventas Vendedor'],
            ['id' => 'ventashistorial', 'url' => '/cfsistem/app/controllers/ventasHistorialController.php', 'icon' => 'bi-receipt', 'class' => 'text-secondary', 'label' => 'Historial Ventas'],
            ['id' => 'comprobantes', 'url' => '/cfsistem/app/controllers/comprobantesPagoController.php', 'icon' => 'bi-file-earmark-check', 'class' => 'text-info', 'label' => 'Comprobantes'],
            ['id' => 'registrarPagos', 'url' => '/cfsistem/app/controllers/registrarPagosController.php', 'icon' => 'bi-credit-card-2-front-fill', 'class' => 'text-success', 'label' => 'Registrar Pagos'],
        ]
    ],
    [
        'id_grupo' => 'compras_proveedores',
        'titulo'   => 'Compras y Proveedores',
        'icono'    => 'bi-bag-check-fill',
        'theme'    => 'sky',
        'submodulos' => [
            ['id' => 'compras', 'url' => '/cfsistem/app/controllers/egresosController.php', 'icon' => 'bi-bag-check', 'class' => 'text-danger', 'label' => 'Compras y Gastos'],
            ['id' => 'proveedores', 'url' => '/cfsistem/app/controllers/proveedoresController.php', 'icon' => 'bi-person-vcard', 'class' => 'text-dark', 'label' => 'Proveedores'],
            ['id' => 'solicitudesCompra', 'url' => '/cfsistem/app/controllers/solicitudesCompraController.php', 'icon' => 'bi-cart-check-fill', 'class' => 'text-info', 'label' => 'Solicitudes Compra'],
        ]
    ],
    [
        'id_grupo' => 'inventario_almacen',
        'titulo'   => 'Inventario y Almacén',
        'icono'    => 'bi-box-seam-fill',
        'theme'    => 'amber',
        'submodulos' => [
            ['id' => 'almacenes', 'url' => '/cfsistem/app/controllers/almacenes.php', 'icon' => 'bi-box-seam', 'class' => 'text-warning', 'label' => 'Almacén'],
            ['id' => 'movimientos', 'url' => '/cfsistem/app/controllers/movimientosController.php', 'icon' => 'bi-arrow-left-right', 'class' => 'text-primary', 'label' => 'Movimientos'],
            ['id' => 'Mermas', 'url' => '/cfsistem/app/controllers/mermasController.php', 'icon' => 'bi-exclamation-triangle-fill', 'class' => 'text-danger', 'label' => 'Mermas'],
            ['id' => 'transmutaciones', 'url' => '/cfsistem/app/controllers/transmutacionesController.php', 'icon' => 'bi-arrow-repeat', 'class' => 'text-secondary', 'label' => 'Conversiones'],
            ['id' => 'historialLotes', 'url' => '/cfsistem/app/controllers/lotesHistorialController.php', 'icon' => 'bi-clock-history', 'class' => 'text-body-secondary', 'label' => 'Historial Lotes'],
            ['id' => 'comprasHistorial', 'url' => '/cfsistem/app/controllers/comprasHistorialController.php', 'icon' => 'bi-collection', 'class' => 'text-dark', 'label' => 'Historial Compras'],
        ]
    ],
    [
        'id_grupo' => 'finanzas_tesoreria',
        'titulo'   => 'Finanzas y Tesorería',
        'icono'    => 'bi-bank',
        'theme'    => 'indigo',
        'submodulos' => [
            ['id' => 'finanzas', 'url' => '/cfsistem/app/controllers/finanzasController.php', 'icon' => 'bi-graph-up-arrow', 'class' => 'text-primary', 'label' => 'Finanzas'],
            ['id' => 'finanzas_admin', 'url' => '/cfsistem/app/controllers/finanzasAdmController.php', 'icon' => 'bi-bar-chart-line-fill', 'class' => 'text-dark', 'label' => 'Finanzas Admin'],
            ['id' => 'corteCaja', 'url' => '/cfsistem/app/controllers/corteCajaController.php', 'icon' => 'bi-cash-stack', 'class' => 'text-success', 'label' => 'Corte de Caja'],
            ['id' => 'tesoreria', 'url' => '/cfsistem/app/controllers/tesoreriaController.php', 'icon' => 'bi-safe-fill', 'class' => 'text-secondary', 'label' => 'Tesorería'],
        ]
    ],
    [
        'id_grupo' => 'logistica_distribucion',
        'titulo'   => 'Logística y Distribución',
        'icono'    => 'bi-truck-front-fill',
        'theme'    => 'slate',
        'submodulos' => [
            ['id' => 'entregas', 'url' => '/cfsistem/app/controllers/entregasController.php', 'icon' => 'bi-truck', 'class' => 'text-warning', 'label' => 'Despachos'],
            ['id' => 'vehiculos', 'url' => '/cfsistem/app/controllers/vehiculosController.php', 'icon' => 'bi-truck-front', 'class' => 'text-secondary', 'label' => 'Vehículos'],
            ['id' => 'verificaciones', 'url' => '/cfsistem/app/controllers/verificacionesController.php', 'icon' => 'bi-patch-check-fill',  'class' => 'text-success', 'label' => 'verificaciones', ],
         
            ['id' => 'mantenimientos', 'url' => '/cfsistem/app/controllers/mantenimientosController.php', 'icon' => 'bi-wrench-adjustable-circle-fill', 'class' => 'text-danger', 'label' => 'Mantenimientos'],
            ['id' => 'repartos', 'url' => '/cfsistem/app/controllers/repartosController.php', 'icon' => 'bi-truck-flatbed', 'class' => 'text-info', 'label' => 'Repartos'],
            ['id' => 'misRepartos', 'url' => '/cfsistem/app/controllers/misRepartosController.php', 'icon' => 'bi-map-fill', 'class' => 'text-primary', 'label' => 'Mis Repartos'],
            ['id' => 'viajesTrabajadores', 'url' => '/cfsistem/app/controllers/viajesTrabajadoresController.php', 'icon' => 'bi-person-workspace', 'class' => 'text-primary', 'label' => 'Viajes Personal'],
        ]
    ],
    [
        'id_grupo' => 'recursos_humanos',
        'titulo'   => 'Recursos Humanos',
        'icono'    => 'bi-people-fill',
        'theme'    => 'rose',
        'submodulos' => [
            ['id' => 'trabajadores', 'url' => '/cfsistem/app/controllers/trabajadoresController.php', 'icon' => 'bi-people-fill', 'class' => 'text-primary', 'label' => 'Trabajadores'],
            ['id' => 'nomina', 'url' => '/cfsistem/app/controllers/nominaController.php', 'icon' => 'bi-cash', 'class' => 'text-success', 'label' => 'Nómina'],
            ['id' => 'prestamos', 'url' => '/cfsistem/app/controllers/prestamosController.php', 'icon' => 'bi-cash-coin', 'class' => 'text-warning', 'label' => 'Préstamos'],
            ['id' => 'faltas', 'url' => '/cfsistem/app/controllers/faltasController.php', 'icon' => 'bi-calendar-x-fill', 'class' => 'text-danger', 'label' => 'Faltas'],
            ['id' => 'pagos_viajes', 'url' => '/cfsistem/app/controllers/pagos_viajesController.php', 'icon' => 'bi-person-gear', 'class' => 'text-success', 'label' => 'Pagos Viajes'],
         ['id' => 'vacaciones', 'url' => '/cfsistem/app/controllers/vacacionesController.php', 'icon' => 'bi-sun-fill',  'class' => 'text-dark','label' => 'Vacaciones'],
      
            ]
    ],
    [
        'id_grupo' => 'administracion',
        'titulo'   => 'Administración',
        'icono'    => 'bi-gear-fill',
        'theme'    => 'red',
        'submodulos' => [
            ['id' => 'usuarios', 'url' => '/cfsistem/app/controllers/usuariosController.php', 'icon' => 'bi-people', 'class' => 'text-danger', 'label' => 'Usuarios', 'active' => ($archivoActual == 'usuariosController.php')],
        ]
    ]
];
?>
<!DOCTYPE html>
<html lang="es" data-bs-theme="light">
<head>
    <meta charset="UTF-8"name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Inicio - Sistema de Almacenes</title>
    
    <!-- Script Anti-Parpadeo (Carga la preferencia inmediatamente) -->
    <script>
        (function() {
            const savedTheme = localStorage.getItem('theme') || 'light';
            document.documentElement.setAttribute('data-bs-theme', savedTheme);
        })();
    </script>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
   
    <?php require_once __DIR__ . '/layout/icono.php' ?>
    <?php if (function_exists('cargarEstilos')) { cargarEstilos(); } ?>

    <style>
    :root { 
        --sidebar-width: 260px; 
        --navbar-height: 65px; 
        --card-radius: 18px;
        --transition-smooth: all 0.35s cubic-bezier(0.16, 1, 0.3, 1);
        --bg-gradient:  rgba(246, 245, 245, 0.87);;
        
        /* Variables de Tarjeta Modo Claro */
        --card-bg: #ffffff;
        --card-border: rgba(226, 232, 240, 0.8);
        --card-border-hover: rgba(203, 213, 225, 1);
        --card-header-bg: #ffffff;
        --card-header-hover: #fafafa;
        --card-body-bg: #f8fafc;
        --card-text-color: #1e293b;
        --btn-bg: #ffffff;
        --btn-border: #e2e8f0;
        --btn-text: #334155;
        --btn-hover-bg: #ffffff;
        --btn-hover-border: #cbd5e1;
    }

    [data-bs-theme="dark"] {
        --bg-gradient: linear-gradient(270deg, #0f172a, #1e1b4b, #1e293b, #0f172a)!important;
        
        
        /* Variables de Tarjeta Modo Oscuro */
        --card-bg: #1e293b;
        --card-border: rgba(51, 65, 85, 0.7);
        --card-border-hover: rgba(71, 85, 105, 1);
        --card-header-bg: #1e293b;
        --card-header-hover: #334155;
        --card-body-bg: #0f172a;
        --card-text-color: #f8fafc;
        --btn-bg: #334155;
        --btn-border: #475569;
        --btn-text: #e2e8f0;
        --btn-hover-bg: #475569;
        --btn-hover-border: #64748b;
    }

    body { 
        position: relative;
        overflow-x: hidden;
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; 
        font-weight: 600;
        letter-spacing: .3px;

        background: var(--bg-gradient)!important;
        background-size: 600% 600%;
        animation: moverGradiente 12s ease infinite !important;
        transition: background 0.5s ease;
    }

    @keyframes moverGradiente {
        0%   { background-position: 0% 50%; }
        50%  { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }

    .main-content { 
        margin-left: var(--sidebar-width); 
         background-color: var(--bg-gradient)!important;
        padding: 28px; 
        padding-top: calc(var(--navbar-height) + 18px); 
        min-height: 100vh;
    }

    /* Temas de iconos adaptados */
    .theme-emerald { background-color: rgba(16, 185, 129, 0.15); color: #10b981; }
    .theme-sky     { background-color: rgba(2, 132, 199, 0.15); color: #0284c7; }
    .theme-amber   { background-color: rgba(245, 158, 11, 0.15); color: #f59e0b; }
    .theme-indigo  { background-color: rgba(99, 102, 241, 0.15); color: #6366f1; }
    .theme-slate   { background-color: rgba(100, 116, 139, 0.15); color: #64748b; }
    .theme-rose    { background-color: rgba(244, 63, 94, 0.15); color: #f43f5e; }
    .theme-red     { background-color: rgba(239, 68, 68, 0.15); color: #ef4444; }

    /* Tarjeta Elegante Adaptable */
    .card-elegant {
        background: var(--card-bg);
        border: 1px solid var(--card-border);
        border-radius: var(--card-radius);
        box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.15);
        overflow: hidden;
        transition: var(--transition-smooth);
    }

    .card-elegant:hover {
        border-color: var(--card-border-hover);
        box-shadow: 0 12px 28px -6px rgba(0, 0, 0, 0.25);
    }

    .card-header-toggle {
        padding: 16px 20px;
        background: var(--card-header-bg);
        cursor: pointer;
        user-select: none;
        display: flex;
        align-items: center;
        justify-content: space-between;
        transition: background 0.2s ease;
    }

    .card-header-toggle:hover {
        background-color: var(--card-header-hover);
    }

    .icon-wrapper {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.15rem;
        flex-shrink: 0;
        transition: transform 0.3s ease;
    }

    .card-header-toggle:hover .icon-wrapper {
        transform: scale(1.05);
    }

    .card-title-text {
        font-size: 0.95rem;
        font-weight: 700;
        color: var(--card-text-color);
        margin: 0;
    }

    .arrow-icon {
        font-size: 0.85rem;
        color: #94a3b8;
        transition: transform 0.3s ease, color 0.2s ease;
    }

    .card-header-toggle:not(.collapsed) .arrow-icon {
        transform: rotate(180deg);
        color: #38bdf8;
    }

    .card-body-content {
        padding: 12px 18px 18px 18px;
        background-color: var(--card-body-bg);
        border-top: 1px solid var(--card-border);
    }

    /* Botón Módulo Adaptable */
    .btn-module {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 10px 14px;
        border-radius: 12px;
        background-color: var(--btn-bg);
        border: 1px solid var(--btn-border);
        color: var(--btn-text);
        text-decoration: none;
        font-size: 0.81rem;
        font-weight: 600;
        transition: var(--transition-smooth);
        height: 100%;
    }

    .btn-module i {
        font-size: 1.15rem;
        flex-shrink: 0;
        transition: transform 0.2s ease;
    }

    .btn-module:hover {
        background-color: var(--btn-hover-bg);
        border-color: var(--btn-hover-border);
        color: #38bdf8;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .btn-module:hover i {
        transform: scale(1.15);
    }

    .counter-badge {
        font-size: 0.72rem;
        font-weight: 600;
        color: var(--btn-text);
        background-color: var(--card-body-bg);
        border: 1px solid var(--btn-border);
        padding: 2px 8px;
        border-radius: 20px;
    }

    .btn-toggle-all {
        font-size: 0.78rem;
        font-weight: 600;
        color: var(--btn-text);
        background: var(--btn-bg);
        border: 1px solid var(--btn-border);
        padding: 6px 14px;
        border-radius: 10px;
        transition: var(--transition-smooth);
    }

    .btn-toggle-all:hover {
        background: var(--card-header-hover);
        color: var(--card-text-color);
        border-color: var(--btn-hover-border);
    }

    @media (max-width: 576px) { 
        .main-content { 
            margin-left: 0 !important; 
            padding: 14px; 
            padding-top: calc(var(--navbar-height) + 14px); 
        }
        .card-header-toggle { padding: 14px; }
        .btn-module { padding: 8px 10px; font-size: 0.78rem; }
    }
    </style>
</head>
<body>

    <?php if (function_exists('renderizarLayout')) { renderizarLayout($paginaActual); } ?>

    <main class="main-content">
        <div class="container-fluid p-0">
            
            <div class="d-flex align-items-center justify-content-between mb-4 header-section flex-wrap gap-2">
                <div>
                    <h2 class="fw-bold m-0 card-title-text" style="letter-spacing: -0.5px; font-size: 1.45rem;">Panel Principal</h2>
                    <p class="card-title-text-50 mb-0" style="font-size: 0.85rem;">Selecciona el módulo con el que vas a trabajar</p>
                </div>

                <div class="d-flex gap-2 align-items-center">
                    <!-- Botón Selector de Modo Oscuro -->
                  

                    <button type="button" class="btn btn-toggle-all" onclick="toggleTodosRecuadros(true)">
                        <i class="bi bi-arrows-expand me-1"></i> Expandir
                    </button>
                    <button type="button" class="btn btn-toggle-all" onclick="toggleTodosRecuadros(false)">
                        <i class="bi bi-arrows-collapse me-1"></i> Contraer
                    </button>
                </div>
            </div>

            <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-3">
                
                <?php foreach ($gruposModulos as $index => $grupo): ?>
                    
                    <?php 
                    $submodulosPermitidos = array_filter($grupo['submodulos'], function($m) {
                        return puedeVerModulo($m['id']);
                    });
                    
                    if (empty($submodulosPermitidos)) continue;

                    $estaAbierto = ($index < 2);
                    $idCollapse = "desplegable_" . $grupo['id_grupo'];
                    ?>

                    <div class="col">
                        <div class="card-elegant">
                            
                            <div class="card-header-toggle <?= $estaAbierto ? '' : 'collapsed' ?>" 
                                 data-bs-toggle="collapse" 
                                 data-bs-target="#<?= $idCollapse ?>" 
                                 aria-expanded="<?= $estaAbierto ? 'true' : 'false' ?>" 
                                 aria-controls="<?= $idCollapse ?>">
                                
                                <div class="d-flex align-items-center gap-3">
                                    <div class="icon-wrapper theme-<?= $grupo['theme'] ?>">
                                        <i class="bi <?= $grupo['icono'] ?>"></i>
                                    </div>
                                    <h3 class="card-title-text"><?= $grupo['titulo'] ?></h3>
                                </div>

                                <div class="d-flex align-items-center gap-2">
                                    <span class="counter-badge">
                                        <?= count($submodulosPermitidos) ?>
                                    </span>
                                    <i class="bi bi-chevron-down arrow-icon"></i>
                                </div>
                            </div>

                            <div id="<?= $idCollapse ?>" class="collapse <?= $estaAbierto ? 'show' : '' ?>">
                                <div class="card-body-content">
                                    <div class="row row-cols-1 row-cols-sm-2 g-2">
                                        <?php foreach ($submodulosPermitidos as $m): ?>
                                            <div class="col">
                                                <a href="<?= $m['url'] ?>" class="btn-module">
                                                    <i class="bi <?= $m['icon'] ?> <?= $m['class'] ?? '' ?>"></i>
                                                    <span class="text-truncate"><?= $m['label'] ?></span>
                                                </a>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                <?php endforeach; ?>

            </div>

        </div>
    </main>

    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
       

       

        function toggleTodosRecuadros(abrir) {
            const collapses = document.querySelectorAll('.card-elegant .collapse');
            collapses.forEach(el => {
                const bsCollapse = bootstrap.Collapse.getOrCreateInstance(el, { toggle: false });
                if (abrir) {
                    bsCollapse.show();
                } else {
                    bsCollapse.hide();
                }
            });
        }

        // Sincronizar el ícono del botón al cargar
        
    </script>
</body>
</html>