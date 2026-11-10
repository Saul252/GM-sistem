<?php
date_default_timezone_set('America/Mexico_City');

$modulos = [

    [
        'id_grupo' => 'ventas_clientes',
        'titulo' => 'Ventas y Clientes',
        'icono' => 'bi-cart-check',
        'color' => '#198754',
        'submodulos' => [
            ['id' => 'remisiones', 'url' => '/cfsistem/app/controllers/requisicionesController.php', 'icon' => 'bi-receipt-cutoff', 'label' => 'Remisiones', 'active' => ($archivoActual == 'requisicionesController.php'), 'color' => '#198754'],
            ['id' => 'cajaRapida', 'url' => '/cfsistem/app/controllers/cajaRapidaController.php', 'icon' => 'bi-lightning-charge', 'label' => 'Caja Rápida', 'active' => ($archivoActual == 'cajaRapidaController.php'), 'color' => '#198754'],
            ['id' => 'cotizaciones', 'url' => '/cfsistem/app/controllers/cotizacionesController.php', 'icon' => 'bi-person-badge-fill', 'label' => 'Cotizaciones', 'active' => ($archivoActual == 'cotizacionesController.php'), 'color' => '#198754'],
            ['id' => 'clientes', 'url' => '/cfsistem/app/controllers/clientesController.php', 'icon' => 'bi-person-lines-fill', 'label' => 'Clientes', 'active' => ($archivoActual == 'clientesController.php'), 'color' => '#198754'],
            ['id' => 'clientesEstatus', 'url' => '/cfsistem/app/controllers/clientesEstatusController.php', 'icon' => 'bi-person-badge', 'label' => 'Estatus Clientes', 'active' => ($archivoActual == 'clientesEstatus.php'), 'color' => '#198754'],
            ['id' => 'ventasVendedor', 'url' => '/cfsistem/app/controllers/historialPedidosVendedorController.php', 'icon' => 'bi-person-badge-fill', 'label' => 'Ventas Vendedor', 'active' => ($archivoActual == 'historialPedidosVendedorController.php'), 'color' => '#198754'],
            ['id' => 'ventashistorial', 'url' => '/cfsistem/app/controllers/ventasHistorialController.php', 'icon' => 'bi-receipt', 'label' => 'Historial de Ventas', 'active' => ($archivoActual == 'ventasHistorialController.php'), 'color' => '#198754'],
            ['id' => 'comprobantes', 'url' => '/cfsistem/app/controllers/comprobantesPagoController.php', 'icon' => 'bi-file-earmark-check', 'label' => 'Comprobantes de Pago', 'active' => ($archivoActual == 'comprobantesPagoController.php'), 'color' => '#198754'],
            ['id' => 'registrarPagos', 'url' => '/cfsistem/app/controllers/registrarPagosController.php', 'icon' => 'bi-credit-card', 'label' => 'Registrar Pagos', 'active' => ($archivoActual == 'registrarPagosController.php'), 'color' => '#198754'],
            ['id' => 'historialPagos', 'url' => '/cfsistem/app/controllers/historialPagosController.php', 'icon' => 'bi-coin', 'label' => 'Historial Pagos', 'active' => ($archivoActual == 'registrarPagosController.php'), 'color' => '#198754'],
        ]
    ],
    [
        'id_grupo' => 'compras_proveedores',
        'titulo' => 'Compras y Proveedores',
        'icono' => 'bi-bag-check',
        'color' => '#fd7e14',
        'submodulos' => [
            ['id' => 'compras', 'url' => '/cfsistem/app/controllers/egresosController.php', 'icon' => 'bi-bag-check', 'label' => 'Compras y Gastos', 'active' => ($archivoActual == 'egresosController.php' || $archivoActual == 'gastos.php'), 'color' => '#fd7e14'],
            ['id' => 'proveedores', 'url' => '/cfsistem/app/controllers/proveedoresController.php', 'icon' => 'bi-person-badge', 'label' => 'Proveedores', 'active' => ($archivoActual == 'proveedoresController.php'), 'color' => '#fd7e14'],
            ['id' => 'solicitudesCompra', 'url' => '/cfsistem/app/controllers/solicitudesCompraController.php', 'icon' => 'bi-cart-check-fill', 'label' => 'Solicitudes Compra', 'active' => ($archivoActual == 'solicitudesCompraController.php'), 'color' => '#fd7e14'],
        ]
    ],
    [
        'id_grupo' => 'inventario_almacen',
        'titulo' => 'Inventario y Almacén',
        'icono' => 'bi-box-seam',
        'color' => '#6f42c1',
        'submodulos' => [
            ['id' => 'almacenes', 'url' => '/cfsistem/app/controllers/almacenes.php', 'icon' => 'bi-box-seam', 'label' => 'Almacén', 'active' => ($archivoActual == 'almacenes.php' || $archivoActual == 'almacen.php'), 'color' => '#6f42c1'],
            ['id' => 'movimientos', 'url' => '/cfsistem/app/controllers/movimientosController.php', 'icon' => 'bi-arrow-left-right', 'label' => 'Movimientos', 'active' => ($archivoActual == 'movimientosController.php'), 'color' => '#6f42c1'],
            ['id' => 'Mermas', 'url' => '/cfsistem/app/controllers/mermasController.php', 'icon' => 'bi-exclamation-triangle', 'label' => 'Mermas', 'active' => ($archivoActual == 'mermasController.php'), 'color' => '#6f42c1'],
            ['id' => 'transmutaciones', 'url' => '/cfsistem/app/controllers/transmutacionesController.php', 'icon' => 'bi-arrow-repeat', 'label' => 'Conversiones', 'active' => ($archivoActual == 'transmutacionesController.php'), 'color' => '#6f42c1'],
            ['id' => 'historialLotes', 'url' => '/cfsistem/app/controllers/lotesHistorialController.php', 'icon' => 'bi-clock-history', 'label' => 'Historial de Lotes', 'active' => ($archivoActual == 'lotesHistorialController.php'), 'color' => '#6f42c1'],
            ['id' => 'comprasHistorial', 'url' => '/cfsistem/app/controllers/comprasHistorialController.php', 'icon' => 'bi-collection', 'label' => 'Historial de Compras', 'active' => ($archivoActual == 'comprasHistorialController.php'), 'color' => '#6f42c1'],
        ]
    ],
    [
        'id_grupo' => 'finanzas_tesoreria',
        'titulo' => 'Finanzas y Tesorería',
        'icono' => 'bi-graph-up-arrow',
        'color' => '#20c997',
        'submodulos' => [
            ['id' => 'finanzas', 'url' => '/cfsistem/app/controllers/finanzasController.php', 'icon' => 'bi-graph-up-arrow', 'label' => 'Finanzas', 'active' => ($archivoActual == 'finanzasController.php'), 'color' => '#20c997'],
            ['id' => 'finanzas_admin', 'url' => '/cfsistem/app/controllers/finanzasAdmController.php', 'icon' => 'bi-bar-chart-line', 'label' => 'Finanzas Admin', 'active' => ($archivoActual == 'finanzasAdmController.php'), 'color' => '#20c997'],
            ['id' => 'corteCaja', 'url' => '/cfsistem/app/controllers/corteCajaController.php', 'icon' => 'bi-calculator', 'label' => 'Corte de Caja', 'active' => ($archivoActual == 'corteCajaController.php'), 'color' => '#20c997'],
            ['id' => 'tesoreria', 'url' => '/cfsistem/app/controllers/tesoreriaController.php', 'icon' => 'bi-safe', 'label' => 'Tesorería', 'active' => ($archivoActual == 'tesoreriaController.php'), 'color' => '#20c997'],
        ]
    ],
    [
        'id_grupo' => 'logistica_distribucion',
        'titulo' => 'Logística y Distribución',
        'icono' => 'bi-truck',
        'color' => '#dc3545',
        'submodulos' => [
            ['id' => 'entregasVista', 'url' => '/cfsistem/app/controllers/entregasVistaController.php', 'icon' => 'bi-truck', 'label' => 'Despachos', 'active' => ($archivoActual == 'entregasVistaController.php'), 'color' => '#dc3545'],
            ['id' => 'vehiculos', 'url' => '/cfsistem/app/controllers/vehiculosController.php', 'icon' => 'bi-truck-front-fill', 'label' => 'Vehículos', 'active' => ($archivoActual == 'vehiculosController.php'), 'color' => '#dc3545'],
            ['id' => 'repartosVista', 'url' => '/cfsistem/app/controllers/repartosVistaController.php', 'icon' => 'bi-truck-flatbed', 'label' => 'Repartos', 'active' => ($archivoActual == 'repartosVistaController.php'), 'color' => '#dc3545'],
            ['id' => 'misRepartos', 'url' => '/cfsistem/app/controllers/misRepartosController.php', 'icon' => 'bi-map-fill', 'label' => 'Mis Repartos', 'active' => ($archivoActual == 'misRepartosController.php'), 'color' => '#dc3545'],
            ['id' => 'viajesTrabajadores', 'url' => '/cfsistem/app/controllers/viajesTrabajadoresController.php', 'icon' => 'bi-person-workspace', 'label' => 'Viajes Trabajadores', 'active' => ($archivoActual == 'viajesTrabajadoresController.php'), 'color' => '#dc3545'],
            ['id' => 'mantenimientos', 'url' => '/cfsistem/app/controllers/mantenimientosController.php', 'icon' => 'bi-wrench-adjustable-circle-fill', 'label' => 'Mantenimientos', 'active' => ($archivoActual == 'mantenimientosController.php'), 'color' => '#dc3545'],
            ['id' => 'verificaciones', 'url' => '/cfsistem/app/controllers/verificacionesController.php', 'icon' => 'bi-patch-check-fill', 'label' => 'verificaciones', 'active' => ($archivoActual == 'verificacionesController.php'), 'color' => '#dc3545'],
        ]
    ],
    [
        'id_grupo' => 'recursos_humanos',
        'titulo' => 'Recursos Humanos',
        'icono' => 'bi-people-fill',
        'color' => '#d63384',
        'submodulos' => [
            ['id' => 'trabajadores', 'url' => '/cfsistem/app/controllers/trabajadoresController.php', 'icon' => 'bi-people-fill', 'label' => 'Trabajadores', 'active' => ($archivoActual == 'trabajadoresController.php'), 'color' => '#d63384'],
            ['id' => 'nomina', 'url' => '/cfsistem/app/controllers/nominaController.php', 'icon' => 'bi-cash', 'label' => 'nomina', 'active' => ($archivoActual == 'nominaController.php'), 'color' => '#d63384'],
            ['id' => 'prestamos', 'url' => '/cfsistem/app/controllers/prestamosController.php', 'icon' => 'bi-cash', 'label' => 'prestamos', 'active' => ($archivoActual == 'prestamosController.php'), 'color' => '#d63384'],
            ['id' => 'faltas', 'url' => '/cfsistem/app/controllers/faltasController.php', 'icon' => 'bi-calendar-x', 'label' => 'Faltas', 'active' => ($archivoActual == 'faltasController.php'), 'color' => '#d63384'],
            ['id' => 'vacaciones', 'url' => '/cfsistem/app/controllers/vacacionesController.php', 'icon' => 'bi-sun-fill', 'label' => 'Vacaciones', 'active' => ($archivoActual == 'vacacionesController.php'), 'color' => '#d63384'],
            ['id' => 'pagos_viajes', 'url' => '/cfsistem/app/controllers/pagos_viajesController.php', 'icon' => 'bi-person-gear', 'label' => 'pagos_viajes', 'active' => ($archivoActual == 'pagos_viajesController.php'), 'color' => '#d63384'],
        ]
    ],
    [
        'id_grupo' => 'administracion',
        'titulo' => 'Administración',
        'icono' => 'bi-gear',
        'color' => '#6c757d',
        'submodulos' => [
            ['id' => 'usuarios', 'url' => '/cfsistem/app/controllers/usuariosController.php', 'icon' => 'bi-people', 'label' => 'Usuarios', 'active' => ($archivoActual == 'usuariosController.php'), 'color' => '#6c757d'],
        ]
    ]
];
?>

<link rel="icon" type="image/png" href="/cfsistem/public/assets/logo.png">
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
<nav class="navbar fixed-top navbar-expand navbar-dark navbar-premium shadow-sm py-0" style="min-height:56px;">
    <div class="container-fluid px-2 px-md-4">
        <div class="d-flex align-items-center gap-2 gap-md-3">
            <button class="btn btn-toggle border-0 py-1" id="toggleSidebar" aria-label="Abrir Menú">
                <i class="bi bi-list fs-5 text-white"></i>
            </button>
        </div>

        <div class="d-flex align-items-center gap-2 gap-md-3">
            <button type="button" class="btn d-flex align-items-center gap-2 py-1 fw-normal" id="btnThemeToggle"
                onclick="alternarModoOscuro()">
                <i class="bi bi-moon-stars-fill fs-6" id="themeIcon"></i>
                <span class="texto fw-normal" id="themeLabel" style="font-size:0.8rem;">Modo Oscuro</span>
            </button>

            <!-- ===== CAMPANA DE NOTIFICACIONES (solo lista) ===== -->
            <div class="dropdown">
                <a href="javascript:void(0);" class="text-white position-relative" id="btnNotif"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-bell fs-6"></i>
                    <span id="notif-badge"
                        class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger d-none"
                        style="font-size:0.6rem;">0</span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow-lg border p-0" id="menuNotif"
                    style="width: 320px; max-width: 90vw; max-height: 400px; overflow-y: auto;">
                    <li class="p-3 border-bottom bg-body-tertiary">
                        <h6 class="mb-0 fw-bold text-body">Notificaciones</h6>
                    </li>
                    <div id="lista-notificaciones">
                        <li class="p-3 text-center text-body-secondary small">Cargando...</li>
                    </div>
                </ul>
            </div>

            <!-- ===== BOTÓN CONFIG DE AVISOS (checkbox por tipo) ===== -->
            <div class="dropdown">
                <button class="btn btn-sm btn-outline-light border-0 dropdown-toggle py-1 fw-normal" type="button"
                    id="btnConfigNotif" data-bs-toggle="dropdown" data-bs-auto-close="outside"
                    title="Configurar avisos">
                    <i id="notifIcon" class="bi bi-sliders fs-6"></i>
                    <span id="notifLabel" class="d-none d-md-inline fw-normal" style="font-size:0.8rem;">Avisos</span>
                </button>
                <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-end p-3 shadow"
                    aria-labelledby="btnConfigNotif" data-bs-theme="dark" style="min-width: 280px;">
                    <li class="small text-muted mb-2">Avisos activos:</li>
                    <li id="listaNotificaciones"></li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li>
                        <button class="btn btn-sm btn-primary w-100" id="btnAplicarNotif">Aplicar</button>
                    </li>
                </ul>
            </div>

            <div class="user-badge d-flex align-items-center text-white bg-white bg-opacity-10 px-3 py-1 rounded-pill">
                <i class="bi bi-person-circle fs-6"></i>
                <span class="ms-2 d-none d-md-inline fw-normal"
                    style="font-size:0.8rem;"><?= $_SESSION['nombre'] ?? 'Usuario' ?></span>
            </div>

            <a href="/cfsistem/logout.php" class="btn btn-sm btn-outline-light border-0 rounded-circle py-1"
                title="Cerrar Sesión">
                <i class="bi bi-box-arrow-right fs-5"></i>
            </a>
        </div>
    </div>
</nav>
<aside id="sidebar" class="bg-body-tertiary border-end shadow-sm">
    <div class="p-3">
        <div class="text-center mb-4">
            <h5 class="fw-bold text-primary mb-1">Menú</h5>
            <?php if (!empty($_SESSION['rol'])): ?>
                <span class="badge bg-body-secondary text-body-secondary border">Rol:
                    <?= ucfirst($_SESSION['rol']) ?></span>
            <?php endif; ?>
        </div>

        <ul class="nav nav-pills flex-column gap-1">

            <!-- ===== INICIO (acceso directo, sin grupo) ===== -->
            <?php if (puedeVerModulo('inicio')): ?>
                <li class="nav-item">
                    <a href="/cfsistem/app/views/inicio.php"
                        class="nav-link d-flex align-items-center gap-3 <?= ($archivoActual == 'inicio.php') ? 'active shadow-sm' : 'text-body' ?>">
                        <span class="icon-reflect" data-icon="bi-house-door">
                            <i class="bi-house-door fs-5"
                                style="color: <?= ($archivoActual == 'inicio.php') ? '#fff' : '#0d6efd' ?>;"></i>
                        </span>
                        <span>Inicio</span>
                    </a>
                </li>
            <?php endif; ?>
            <!-- ===== FIN INICIO ===== -->

            <?php foreach ($modulos as $grupo): ?>
                <?php
                $submodulosPermitidos = array_filter($grupo['submodulos'], function ($sub) {
                    return puedeVerModulo($sub['id']);
                });

                if (empty($submodulosPermitidos))
                    continue;

                $grupoActivo = false;
                foreach ($submodulosPermitidos as $sub) {
                    if ($sub['active']) {
                        $grupoActivo = true;
                        break;
                    }
                }

                $colorGrupo = $grupo['color'] ?? '#0d6efd';
                ?>

                <li class="nav-item">
                    <a href="#drop-<?= $grupo['id_grupo'] ?>"
                        class="nav-link d-flex align-items-center justify-content-between gap-3 <?= $grupoActivo ? 'bg-body-secondary text-body fw-bold' : 'text-body' ?>"
                        data-bs-toggle="collapse" aria-expanded="<?= $grupoActivo ? 'true' : 'false' ?>">

                        <div class="d-flex align-items-center gap-3">
                            <span class="icon-reflect" data-icon="<?= $grupo['icono'] ?>">
                                <i class="<?= $grupo['icono'] ?> fs-5" style="color: <?= $colorGrupo ?>;"></i>
                            </span>
                            <span><?= $grupo['titulo'] ?></span>
                        </div>
                        <i class="bi bi-chevron-down small transition-icon"></i>
                    </a>

                    <div class="collapse <?= $grupoActivo ? 'show' : '' ?>" id="drop-<?= $grupo['id_grupo'] ?>">
                        <ul class="nav nav-pills flex-column gap-1 ps-4 pt-1 pb-1">
                            <?php foreach ($submodulosPermitidos as $m): ?>
                                <?php $colorSub = $m['color'] ?? $colorGrupo; ?>
                                <li class="nav-item">
                                    <a href="<?= $m['url'] ?>"
                                        class="nav-link d-flex align-items-center gap-3 <?= $m['active'] ? 'active shadow-sm' : 'text-body-secondary' ?>"
                                        style="font-size: 0.95rem;">
                                        <span class="icon-reflect" data-icon="<?= $m['icon'] ?>">
                                            <i class="<?= $m['icon'] ?> fs-6"
                                                style="color: <?= $m['active'] ? '#fff' : $colorSub ?>;"></i>
                                        </span>
                                        <span><?= $m['label'] ?></span>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</aside>
<!-- Modal Confirmación de Cancelación -->
<div class="modal fade" id="modalConfirmarCancelacion" tabindex="-1" aria-labelledby="modalConfirmarCancelacionLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalConfirmarCancelacionLabel">Confirmar Cancelación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p id="modalTextoDetalle" class="fw-semibold mb-2"></p>
                <p id="modalTextoSub" class="text-muted small"></p>
                <div id="wrapperMotivoSinPago" class="d-none mt-3">
                    <label class="form-label small fw-bold">Motivo de la cancelación:</label>
                    <input type="text" id="inputMotivoSinPago" class="form-field form-control"
                        placeholder="Escriba el motivo...">
                </div>
            </div>
            <div class="modal-footer d-flex justify-content-between">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-lg"></i> Regresar
                </button>
                <div id="contenedorBotonesAccion" class="d-flex gap-2">
                    <!-- Los botones dinámicos se insertan desde JS -->
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
<script>
    /**
     * CF SYSTEM - LÓGICA GLOBAL DE INTERFAZ Y NOTIFICACIONES
     * Versión: 2.1 (Dropdowns separados + config de avisos)
     */
    let usuario = <?= intval($_SESSION['usuario_id'] ?? 0) ?>;

    // Almacén global para acumular los ítems de todos los módulos
    const almacenNotificaciones = {
        mantenimientos: [],
        cancelaciones: [],
        traspasos: [],
        verificaciones: []
    };

    // Estados de control para Toastify
    const estadoToasts = {
        verificacion: { primeraCarga: true, ultimoConteo: 0 },
        mantenimiento: { primeraCarga: true, ultimoConteo: 0 },
        cancelacion: { primeraCarga: true, ultimoConteo: 0 },
        traspaso: { primeraCarga: true, ultimoConteo: 0 }
    };

    document.addEventListener('DOMContentLoaded', () => {

        inyectarEstilosToast();

        // UI Elements
        const toggleBtn = document.getElementById('toggleSidebar');
        const sidebar = document.getElementById('sidebar');

        let overlay = document.querySelector('.sidebar-overlay') || document.createElement('div');
        if (!overlay.parentElement) {
            overlay.className = 'sidebar-overlay';
            document.body.appendChild(overlay);
        }

        // Sidebar responsive
        if (sidebar) {
            if (window.innerWidth <= 992) {
                sidebar.classList.remove('show');
                if (overlay) overlay.classList.remove('active');
            } else {
                sidebar.classList.add('hidden');
                document.body.classList.add('sidebar-hidden');
            }
        }

        if (toggleBtn) {
            toggleBtn.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                if (window.innerWidth <= 992) {
                    sidebar.classList.toggle('show');
                    if (overlay) overlay.classList.toggle('active');
                } else {
                    sidebar.classList.toggle('hidden');
                    document.body.classList.toggle('sidebar-hidden');
                }
            });
        }

        // ✅ Inicializar módulo de avisos ANTES del polling
        if (typeof initModuloNotificaciones === 'function') {
            initModuloNotificaciones();
        }

        // Polling
        mantenimientoSistema();
        setInterval(mantenimientoSistema, 35000);
    });

    /* ==========================================================================
       RENDERIZADOR CENTRALIZADO (COMBINA TODAS LAS NOTIFICACIONES)
       ========================================================================== */
    function renderizarListaNotificaciones() {
        const lista = document.getElementById('lista-notificaciones');
        const badge = document.getElementById('notif-badge');

        const todosLosHTML = [
            ...almacenNotificaciones.traspasos,
            ...almacenNotificaciones.cancelaciones,
            ...almacenNotificaciones.mantenimientos,
            ...almacenNotificaciones.verificaciones
        ];

        const totalNotificaciones = todosLosHTML.length;

        if (badge) {
            if (totalNotificaciones > 0) {
                badge.innerText = totalNotificaciones;
                badge.classList.remove('d-none');
                badge.style.display = 'inline-block';
            } else {
                badge.classList.add('d-none');
                badge.style.display = 'none';
            }
        }

        if (lista) {
            if (totalNotificaciones === 0) {
                lista.innerHTML = '<div class="p-4 text-center text-body-secondary small">Sin notificaciones pendientes</div>';
            } else {
                lista.innerHTML = todosLosHTML.join('');
            }
        }
    }

    /* ==========================================================================
       CONSULTAS AL BACKEND
       ========================================================================== */
    function mantenimientoSistema() {
        if (typeof usuario === 'undefined' || !usuario) return;
        if (typeof Notificaciones === 'undefined') return;

        const activas = Notificaciones.getActivas(usuario);

        const puedeVerTodas = usuario <= 2;
        const permitidas = puedeVerTodas
            ? NOTIFICACIONES.map(n => n.id)
            : NOTIFICACIONES.slice(0, 2).map(n => n.id);

        const mapaFn = {
            cancelaciones: verificarCancelacionesRecientes,
            notificaciones: verificarNotificaciones,
            mantenimientos: verificarMantenimientos,
            solicitudes: verificarSolicitudesCancelacion,
            verificaciones: verificarVerificaciones,
        };

        activas
            .filter(id => permitidas.includes(id))
            .forEach(id => {
                const fn = mapaFn[id];
                if (typeof fn === 'function') fn();
            });
    }

    /* ==========================================================================
       CANCELACIONES RECIENTES
       ========================================================================== */
    const idsCancelacionesNotificadas = new Set();

    function verificarCancelacionesRecientes() {
        fetch('/cfsistem/app/controllers/ventasHistorialController.php?action=obtenerCancelacionesRecientes')
            .then(res => res.json())
            .then(res => {
                if (res.status === 'success' && Array.isArray(res.data) && res.data.length > 0) {

                    res.data.forEach(item => {
                        if (!idsCancelacionesNotificadas.has(item.id)) {

                            idsCancelacionesNotificadas.add(item.id);

                            if (typeof Toastify === "function") {
                                Toastify({
                                    text: `🗑️ VENTA ELIMINADA\nSe canceló la venta #${item.id_venta}\nMotivo: ${item.razon || 'Sin motivo especificado'}`,
                                    duration: 1500,
                                    close: true,
                                    gravity: "top",
                                    position: "right",
                                    stopOnFocus: true,
                                    style: {
                                        background: "#dc3545",
                                        color: "#ffffff",
                                        borderRadius: "12px",
                                        fontWeight: "600",
                                        padding: "14px 18px",
                                        boxShadow: "0 4px 12px rgba(0,0,0,0.15)"
                                    }
                                }).showToast();
                            }

                            if (typeof getVentas === 'function') getVentas();
                        }
                    });
                }
            })
            .catch(err => console.error("Error cancelaciones recientes:", err));
    }

    function verificarSolicitudesCancelacion() {
        fetch('/cfsistem/app/controllers/ventasHistorialController.php?action=obtenerSolicitudesPendientes')
            .then(res => res.json())
            .then(res => {
                const items = res.data || res.items || [];
                const cantidad = items.length;
                const estado = estadoToasts.cancelacion;

                if (cantidad > 0 && (estado.primeraCarga || cantidad > estado.ultimoConteo)) {
                    if (typeof Toastify === "function") {
                        const u = items[0] || {};
                        Toastify({
                            text: `⚠️ CANCELACIÓN SOLICITADA\nVenta #${u.id_venta || ''}\nMotivo: ${u.razon || 'Sin motivo'}`,
                            duration: 1500,
                            close: true,
                            gravity: "top",
                            position: "right",
                            className: "toast-cancelacion toast-cancel-close",
                            style: { background: "#ffffff", color: "#000", borderRadius: "14px", padding: "16px 20px" },
                            onClick: () => window.location.href = "/cfsistem/app/controllers/ventasHistorialController.php"
                        }).showToast();
                    }
                    estado.primeraCarga = false;
                }
                estado.ultimoConteo = cantidad;

                almacenNotificaciones.cancelaciones = items.map(item => `
                <div class="d-flex align-items-center justify-content-between p-3 border-bottom bg-body-tertiary hover-notif">
                    <div style="flex: 1; line-height: 1.4;">
                        <b class="text-danger d-block small text-uppercase">Cancelacion de Venta #${item.id_venta}</b>
                        <b class="d-block text-body-secondary" style="font-size: 0.75rem;">Motivo: ${item.razon || 'Sin especificación'}</b>
                        <div class="mt-1"><small class="text-body-tertiary" style="font-size: 0.70rem;">Por: ${item.usuario_nombre || 'Usuario'}</small></div>
                    </div>
                    <div class="d-flex gap-1 ms-2">
                        <button onclick="procesarAceptarCancelacion(${item.idVenta},${item.id},${item.pagado},${item.venta_total},'${item.folio}','${item.razon}')" class="btn btn-success btn-sm rounded-circle shadow-sm" style="width:32px; height:32px;" title="Aceptar"><i class="bi bi-check-lg"></i></button>
                        <button onclick="procesarEliminarCancelacion(${item.id})" class="btn btn-danger btn-sm rounded-circle shadow-sm" style="width:32px; height:32px;" title="Eliminar"><i class="bi bi-trash"></i></button>
                    </div>
                </div>`);

                renderizarListaNotificaciones();
            })
            .catch(err => console.error("Error cancelaciones:", err));
    }
    function verificarNotificaciones() {
        fetch('/cfsistem/app/backend/movimientos/get_notificaciones_traspaso.php?t=' + Date.now())
            .then(res => res.json())
            .then(data => {
                const items = data.items || [];
                const cantidad = parseInt(data.cantidad) || 0;
                const estado = estadoToasts.traspaso;

                // -------- 1) TOAST (solo si hay nuevos) --------
                if (cantidad > 0 && (estado.primeraCarga || cantidad > estado.ultimoConteo)) {

                    if (typeof Toastify === "function") {
                        const u = items[0] || {};

                        // Texto del toast según tipo
                        let textoToast;
                        if (u.tipo === 'masivo') {
                            const listaProd = (u.productos || [])
                                .slice(0, 3)
                                .map(p => `• ${p.nombre} (${p.cantidad_texto})`)
                                .join('\n');
                            const extra = (u.productos || []).length > 3
                                ? `\n… y ${u.productos.length - 3} más`
                                : '';
                            textoToast =
                                `📦 DESPACHO MASIVO ${u.codigo_traspaso || ''}\n` +
                                `${u.emisor} → ${u.destino}\n` +
                                `${u.total_productos || 0} productos:\n${listaProd}${extra}`;
                        } else {
                            textoToast =
                                `📦 TRASPASO EN PUERTA\n` +
                                `${u.emisor} envió ${u.cantidad_texto || u.cantidad} de ${u.producto} a ${u.destino}`;
                        }

                        Toastify({
                            text: textoToast,
                            duration: 3500,
                            close: true,
                            gravity: "top",
                            position: "right",
                            className: "toast-traspaso toast-red-close",
                            style: {
                                background: "#ffffff",
                                color: "#000",
                                borderRadius: "14px",
                                padding: "16px 20px",
                                whiteSpace: "pre-line",
                                maxWidth: "360px"
                            },
                            onClick: () => window.location.href = "/cfsistem/app/controllers/almacenes.php"
                        }).showToast();
                    }
                    estado.primeraCarga = false;
                }
                estado.ultimoConteo = cantidad;

                // -------- 2) LISTA DE NOTIFICACIONES --------
                almacenNotificaciones.traspasos = items.map(item => {

                    // ========== CASO MASIVO ==========
                    if (item.tipo === 'masivo') {
                        const listaProductos = (item.productos || []).map(p => `
                        <li class="d-flex justify-content-between align-items-start small">
                            <b class="text-success text-nowrap">${p.nombre}
                            ${p.cantidad_texto}</b>
                        </li>
                    `).join('');

                        return `
<div class="col-12 mb-2 p-3 rounded-3 border bg-body-tertiary shadow-sm">
    <!-- Encabezado: Despacho Masivo y Total de Productos -->
    <div class="d-flex justify-content-between align-items-center mb-1">
        <b class="badge text-success  border border-success-subtle rounded-pill px-2 py-1" style="font-size:0.65rem;">
            <i class="bi bi-boxes me-1"></i> DESPACHO MASIVO
        </b>
        <b class="badge  text-primary rounded-pill" style="font-size:0.6rem;">
            ${item.total_productos || 0} productos
        </b>
    </div>

    <!-- Ruta (Origen → Destino) -->
    <div class="mb-2">
        <div class="text-sencondary fw-semibold text-truncate" style="font-size:0.75rem;">
            ${item.origen} -> ${item.destino}
        </div>
    </div>

    <hr class="my-2 opacity-50">

    <!-- Metadatos (Folio y Emisor) -->
    <div class="mb-2 d-flex flex-column gap-1">
        <small class="text-body-secondary" style="font-size:0.7rem;">
            <i class="bi bi-upc-scan text-muted me-1"></i> Folio:${item.codigo_traspaso || '—'}
        </small>
        <small class="text-body-secondary" style="font-size:0.7rem;">
            <i class="bi bi-person text-muted me-1"></i> De: ${item.emisor}
        </small>
    </div>

    <!-- Lista de Productos -->
    <div class="mb-2">
        <ul class="list-unstyled mb-0 ps-1" style="max-height:120px; overflow-y:auto; font-size:0.75rem;">
            ${listaProductos}
        </ul>
    </div>

    <!-- Botón de Acción -->
    <div class="d-flex justify-content-end pt-1">
        <button onclick="procesarRecepcionMasiva(${item.id_traspaso_id})"
            class="btn btn-success btn-sm rounded-pill shadow-sm px-3"
            style="font-size:0.75rem;"
            title="Recibir todo el lote">
            <i class="bi bi-check2-all me-1"></i> Recibir lote
        </button>
    </div>
</div>`;
                    }

                    // ========== CASO SIMPLE ==========
                    return `
                <div class="row align-items-center ROW p-3 border-bottom hover-notif">
                    <div class="col-12 mb-2">
                        <b class="d-block text-success text-uppercase" style="font-size: 0.75rem;">
                            TRASPASO PARA ${item.destino}
                        </b>
                    </div>
                    <div class="col-8" style="line-height: 1.4;">
                        <b class="text-primary d-block small text-uppercase text-success">${item.producto}</b>
                        <b class="d-block text-body-secondary text-success" style="font-size: 0.75rem;">
                            De: ${item.emisor}
                        </b>
                        <div class="mt-1">
                            <b class="text-primary d-block small text-uppercase text-success">
                                ${item.cantidad_texto || (item.cantidad + ' PZA')}
                            </b>
                        </div>
                    </div>
                    <div class="col-4 text-end">
                        <button onclick="procesarRecepcionRapida(${item.id})"
                            class="btn btn-success btn-sm rounded-circle shadow-sm"
                            style="width:32px; height:32px;"
                            title="Recibir traspaso">
                            <i class="bi bi-check-lg"></i>
                        </button>
                    </div>
                </div>`;
                });

                renderizarListaNotificaciones();
            })
            .catch(err => console.error("Error traspasos:", err));
    }
    function verificarMantenimientos() {
        fetch("/cfsistem/app/controllers/mantenimientosController.php?action=listarProximoMantenimiento")
            .then(r => r.json())
            .then(data => {
                const items = Array.isArray(data) ? data : [];
                const cantidad = items.length;
                const estado = estadoToasts.mantenimiento;

                if (cantidad > 0 && (estado.primeraCarga || cantidad > estado.ultimoConteo)) {
                    const item = items[0];
                    Toastify({
                        text: `🚗 PRÓXIMO MANTENIMIENTO\n${item.estado}\n\n${item.vehiculo} ${item.placas}`,
                        duration: 1500,
                        gravity: "top",
                        position: "right",
                        style: { background: "#ffffff", color: "#111", borderLeft: "5px solid #ffc107", borderRadius: "15px", padding: "18px" },
                        onClick: () => window.location.href = "/cfsistem/app/controllers/mantenimientosController.php"
                    }).showToast();
                    estado.primeraCarga = false;
                }
                estado.ultimoConteo = cantidad;

                almacenNotificaciones.mantenimientos = items.map(item => {
                    const dias = parseInt(item.dias_restantes);
                    const color = dias <= 0 ? "danger" : (dias <= 3 ? "warning" : "success");
                    return `
                    <div class="d-flex justify-content-between align-items-center p-3 border-bottom hover-notif">
                        <div>
                            <div class="small"><b>Proximo mantenimiento: ${item.estado}</b></div>
                            <div class="fw-bold text-${color}">Vehículo: ${item.vehiculo}</div>
                            <div class="small text-secondary">${item.placas}</div>
                        </div>
                        <button class="btn btn-dark btn-sm rounded-circle" onclick="window.location='/cfsistem/app/controllers/mantenimientosController.php?id=${item.id_mantenimiento}'">
                            <i class="bi bi-arrow-right text-danger"></i>
                        </button>
                    </div>`;
                });

                renderizarListaNotificaciones();
            })
            .catch(err => console.error("Error mantenimientos:", err));
    }

    function verificarVerificaciones() {
        fetch("/cfsistem/app/controllers/verificacionesController.php?action=listarProximaVerificacion")
            .then(r => r.json())
            .then(data => {
                const items = Array.isArray(data) ? data : [];
                const cantidad = items.length;
                const estado = estadoToasts.verificacion;

                if (cantidad > 0 && (estado.primeraCarga || cantidad > estado.ultimoConteo)) {
                    const item = items[0];
                    Toastify({
                        text: `📋 PRÓXIMA VERIFICACIÓN\n${item.estado}\n\n${item.vehiculo} ${item.placas}`,
                        duration: 1500,
                        gravity: "top",
                        position: "right",
                        style: {
                            background: "#ffffff",
                            color: "#111",
                            borderLeft: "5px solid #0d6efd",
                            borderRadius: "15px",
                            padding: "18px"
                        },
                        onClick: () => window.location.href = "/cfsistem/app/controllers/verificacionesController.php"
                    }).showToast();
                    estado.primeraCarga = false;
                }
                estado.ultimoConteo = cantidad;

                almacenNotificaciones.verificaciones = items.map(item => {
                    const dias = parseInt(item.dias_restantes);
                    const color = dias <= 0 ? "danger" : (dias <= 3 ? "warning" : "success");
                    return `
                    <div class="d-flex justify-content-between align-items-center p-3 border-bottom hover-notif">
                        <div>
                            <div class="small"><b>Proxima verificacion: ${item.estado}</b></div>
                            <div class="fw-bold text-${color}">Vehículo: ${item.vehiculo}</div>
                            <div class="small text-secondary">${item.placas}</div>
                        </div>
                        <button class="btn btn-success btn-sm rounded-circle" onclick="window.location='/cfsistem/app/controllers/verificacionesController.php?action=obtenerDetalle&id=${item.id}'">
                            <i class="bi bi-arrow-right"></i>
                        </button>
                    </div>`;
                });

                renderizarListaNotificaciones();
            })
            .catch(err => console.error("Error verificaciones:", err));
    }

    let ultimoIdVentaEliminadaNotificada = 0;

    function verificarVentasEliminadasGlobales() {
        fetch('/cfsistem/app/controllers/ventasHistorialController.php?action=obtenerUltimaVentaEliminada')
            .then(res => res.json())
            .then(data => {
                if (data && data.id_venta && data.id_venta !== ultimoIdVentaEliminadaNotificada) {

                    Toastify({
                        text: `🚨 ATENCIÓN GLOBAL\nSe ha cancelado/eliminado la Venta #${data.folio || data.id_venta}`,
                        duration: 1500,
                        close: true,
                        gravity: "top",
                        position: "right",
                        stopOnFocus: true,
                        style: {
                            background: "#dc3545",
                            color: "#ffffff",
                            borderRadius: "12px",
                            fontWeight: "600",
                            padding: "16px"
                        }
                    }).showToast();

                    ultimoIdVentaEliminadaNotificada = data.id_venta;

                    if (typeof getVentas === 'function') getVentas();
                }
            })
            .catch(err => console.error("Error al consultar ventas eliminadas:", err));
    }

    /* ==========================================================================
       HANDLERS Y ESTILOS AUXILIARES
       ========================================================================== */
    function inyectarEstilosToast() {
        if (!document.getElementById('style-toast-custom')) {
            const style = document.createElement('style');
            style.id = 'style-toast-custom';
            style.innerHTML = `
            .toast-close { opacity: 1 !important; font-weight: bold; font-size: 20px; margin-left: 10px; }
            .toast-red-close .toast-close { color: #ff0000 !important; }
            .toast-cancel-close .toast-close { color: #dc3545 !important; }
        `;
            document.head.appendChild(style);
        }
    }

    function procesarAceptarCancelacion(idVenta, id, pagado, total, folio, razon) {
        const montoPagado = parseFloat(pagado) || 0;
        const modalElem = document.getElementById('modalConfirmarCancelacion');
        const modalBs = bootstrap.Modal.getOrCreateInstance(modalElem);

        const textoDetalle = document.getElementById('modalTextoDetalle');
        const textoSub = document.getElementById('modalTextoSub');
        const wrapperMotivo = document.getElementById('wrapperMotivoSinPago');
        const contenedorBotones = document.getElementById('contenedorBotonesAccion');

        textoDetalle.textContent = `¿Aceptar y Cancelar Venta ${folio || '#' + idVenta}?`;

        if (montoPagado > 0) {
            textoSub.textContent = `Motivo reportado: "${razon}". Selecciona si deseas reintegrar el dinero al saldo del cliente o solo anular la venta.`;
            wrapperMotivo.classList.add('d-none');

            contenedorBotones.innerHTML = `
            <button class="btn btn-danger" onclick="ejecutarCancelacionEncadenada(${idVenta}, ${id}, false, '${razon}')">
                <i class="bi bi-x-circle"></i> Sin Saldo
            </button>
            <button class="btn btn-success" onclick="ejecutarCancelacionEncadenada(${idVenta}, ${id}, true, '${razon}')">
                <i class="bi bi-cash-stack"></i> Con Saldo a Favor
            </button>
        `;
        } else {
            textoSub.textContent = "Esta venta no tiene pagos registrados. Se procederá a cancelarla sin saldo.";
            wrapperMotivo.classList.remove('d-none');
            document.getElementById('inputMotivoSinPago').value = razon || '';

            contenedorBotones.innerHTML = `
            <button class="btn btn-danger" onclick="confirmarSinPago(${idVenta}, ${id})">
                <i class="bi bi-check-lg"></i> Sí, cancelar venta
            </button>
        `;
        }

        modalBs.show();
    }

    function confirmarSinPago(idVenta, id) {
        const motivoInput = document.getElementById('inputMotivoSinPago').value.trim();
        if (!motivoInput) {
            alert("¡El motivo es obligatorio!");
            return;
        }
        ejecutarCancelacionEncadenada(idVenta, id, false, motivoInput);
    }

    async function ejecutarCancelacionEncadenada(idVenta, id, conSaldo, motivo) {
        const modalElem = document.getElementById('modalConfirmarCancelacion');
        const modalBs = bootstrap.Modal.getInstance(modalElem);
        if (modalBs) modalBs.hide();

        const esOscuro = document.documentElement.getAttribute('data-bs-theme') === 'dark';
        const esModoOscuroObj = {
            background: esOscuro ? '#1e293b' : '#ffffff',
            color: esOscuro ? '#f8fafc' : '#1e2022'
        };

        Swal.fire({
            title: 'Procesando cancelación...',
            text: 'Por favor espere...',
            didOpen: () => Swal.showLoading(),
            allowOutsideClick: false,
            ...esModoOscuroObj
        });

        try {
            const formData = new FormData();
            formData.append('id', id);

            const respSolicitud = await fetch('/cfsistem/app/controllers/ventasHistorialController.php?action=aceptarSolicitudCancelacion', {
                method: 'POST',
                body: formData
            });
            const dataSolicitud = await respSolicitud.json();

            if (dataSolicitud.status !== 'success' && !dataSolicitud.success) {
                Swal.fire({ icon: 'error', title: 'Error', text: dataSolicitud.message || 'Error al aceptar solicitud.' });
                return;
            }

            const accion = conSaldo ? 'cancelarVenta' : 'cancelarVentaSinSaldo';
            const urlController = typeof URL_CONTROLLER !== 'undefined' ? URL_CONTROLLER : '/cfsistem/app/controllers/ventasHistorialController.php';

            const respCancelacion = await fetch(`${urlController}?action=${accion}`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id_venta: idVenta, motivo: motivo })
            });
            const resCancelacion = await respCancelacion.json();

            if (resCancelacion.status === 'success' || resCancelacion.success) {
                await Swal.fire({
                    title: '¡Venta Cancelada!',
                    text: resCancelacion.message || 'Procesado correctamente.',
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false
                });

                if (typeof verificarSolicitudesCancelacion === 'function') verificarSolicitudesCancelacion();
                if (typeof getVentas === 'function') getVentas();
            } else {
                Swal.fire({ icon: 'error', title: 'Error', text: resCancelacion.message });
            }

        } catch (error) {
            console.error("Error:", error);
            Swal.fire({ icon: 'error', title: 'Error de conexión', text: 'No se pudo conectar con el servidor.' });
        }
    }

    async function procesarEliminarCancelacion(id) {
        const esOscuro = document.documentElement.getAttribute('data-bs-theme') === 'dark';
        const confirm = await Swal.fire({
            title: '¿Eliminar solicitud?',
            text: 'Esta acción borrará físicamente el registro.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#dc3545',
            background: esOscuro ? '#1e293b' : '#ffffff',
            color: esOscuro ? '#f8fafc' : '#1e2022'
        });

        if (!confirm.isConfirmed) return;

        const formData = new FormData();
        formData.append('id', id);

        try {
            const response = await fetch('/cfsistem/app/controllers/ventasHistorialController.php?action=eliminarSolicitudCancelacion', { method: 'POST', body: formData });
            const data = await response.json();
            if (data.status === 'success') verificarSolicitudesCancelacion();
            else Swal.fire({ icon: 'error', title: 'Error', text: data.message });
        } catch (err) { console.error(err); }
    }

    window.procesarRecepcionRapida = function (id) {
        Swal.fire({
            title: '¿Confirmar recepción?',
            text: 'Se registrará la entrada del material en el almacén destino.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, recibir',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (!result.isConfirmed) return;

            // Loader mientras procesa
            Swal.fire({
                title: 'Procesando...',
                text: 'Registrando recepción de material',
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            const formData = new FormData();
            formData.append('id', id);

            fetch('/cfsistem/app/controllers/traspasosController.php?action=recibirTraspaso', {
                method: 'POST',
                body: formData
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success || data.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Recepción registrada',
                            text: data.message || 'Material recibido correctamente.',
                            confirmButtonColor: '#28a745'
                        }).then(() => location.reload());
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'No se pudo procesar',
                            text: data.message || 'Ocurrió un error inesperado.',
                            confirmButtonColor: '#dc3545'
                        });
                    }
                })
                .catch(err => {
                    console.error(err);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error de conexión',
                        text: 'No se pudo contactar al servidor. Intenta de nuevo.',
                        confirmButtonColor: '#dc3545'
                    });
                });
        });
    };

    window.procesarRecepcionMasiva = function (idTraspasoId) {
        Swal.fire({
            title: '¿Confirmar recepción masiva?',
            html: 'Se recibirán <b>todos los movimientos</b> de este traspaso masivo.<br>Esta acción no se puede revertir.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, recibir todo',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (!result.isConfirmed) return;

            // Loader mientras procesa
            Swal.fire({
                title: 'Procesando...',
                text: 'Registrando recepción masiva de material',
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            const formData = new FormData();
            formData.append('id_traspaso_id', idTraspasoId);

            fetch('/cfsistem/app/controllers/traspasosController.php?action=recibirTraspasoMasivo', {
                method: 'POST',
                body: formData
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success || data.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Recepción masiva registrada',
                            text: data.message || 'Todos los movimientos fueron recibidos correctamente.',
                            confirmButtonColor: '#28a745'
                        }).then(() => location.reload());
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'No se pudo procesar',
                            text: data.message || 'Ocurrió un error inesperado.',
                            confirmButtonColor: '#dc3545'
                        });
                    }
                })
                .catch(err => {
                    console.error(err);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error de conexión',
                        text: 'No se pudo contactar al servidor. Intenta de nuevo.',
                        confirmButtonColor: '#dc3545'
                    });
                });
        });
    };
</script>
<script>
    function actualizarBotonTema(tema) {
        const icon = document.getElementById('themeIcon');
        const label = document.getElementById('themeLabel');
        if (!icon || !label) return;

        if (tema === 'dark') {
            icon.className = 'bi bi-sun-fill text-warning';
            label.textContent = 'Modo Claro';
        } else {
            icon.className = 'bi bi-moon-stars-fill';
            label.textContent = 'Modo Oscuro';
        }
    }
    function alternarModoOscuro() {
        const html = document.documentElement;
        const nuevoTema = html.getAttribute('data-bs-theme') === 'dark' ? 'light' : 'dark';

        html.setAttribute('data-bs-theme', nuevoTema);
        localStorage.setItem('theme', nuevoTema);

        if (typeof actualizarBotonTema === 'function') {
            actualizarBotonTema(nuevoTema);
        }
        if (typeof seleccionarFondoInicio === 'function') {
            seleccionarFondoInicio();
        }
    }
    (function () {
        const savedTheme = localStorage.getItem('theme') || 'light';
        document.documentElement.setAttribute('data-bs-theme', savedTheme);
    })();
</script>
<script>
    const NOTIFICACIONES = [
        { id: 'cancelaciones', nombre: 'Cancelaciones recientes', fn: 'verificarCancelacionesRecientes' },
        { id: 'notificaciones', nombre: 'Traspasos', fn: 'verificarNotificaciones' },
        { id: 'mantenimientos', nombre: 'Mantenimientos', fn: 'verificarMantenimientos' },
        { id: 'solicitudes', nombre: 'Solicitudes de cancelación', fn: 'verificarSolicitudesCancelacion' },
        { id: 'verificaciones', nombre: 'Verificaciones', fn: 'verificarVerificaciones' },
    ];
    const Notificaciones = {
        NOTIFICACIONES,
        _key(usuario) { return `notifActivas_u${usuario}`; },

        getActivas(usuario) {
            try {
                const raw = JSON.parse(localStorage.getItem(this._key(usuario)) || '[]');
                return Array.isArray(raw) ? raw : [];
            } catch { return []; }
        },

        setActivas(usuario, ids) {
            localStorage.setItem(this._key(usuario), JSON.stringify(ids));
            document.dispatchEvent(new CustomEvent('notificaciones:change', {
                detail: { usuario, activas: ids }
            }));
        },

        puedeActivar(usuario, id) {
            if (usuario <= 2) return true;
            return NOTIFICACIONES.slice(0, 2).some(n => n.id === id);
        },

        inicializar(usuario) {
            const yaGuardado = localStorage.getItem(this._key(usuario));
            if (yaGuardado !== null) return;
            const defaultIds = usuario <= 2
                ? NOTIFICACIONES.map(n => n.id)
                : NOTIFICACIONES.slice(0, 2).map(n => n.id);
            this.setActivas(usuario, defaultIds);
        }
    };

    window.Notificaciones = Notificaciones;

    function renderDropdown(usuario) {
        const cont = document.getElementById('listaNotificaciones');
        if (!cont) return;

        const activas = Notificaciones.getActivas(usuario);

        cont.innerHTML = NOTIFICACIONES.map(n => {
            const permitido = Notificaciones.puedeActivar(usuario, n.id);
            const checked = activas.includes(n.id) ? 'checked' : '';
            const disabled = permitido ? '' : 'disabled';
            const candado = permitido ? '' : '<i class="bi bi-lock-fill ms-auto text-muted" style="font-size:10px;"></i>';

            return `
      <label class="form-check d-flex align-items-center gap-2 mb-1 ${permitido ? '' : 'opacity-50'}">
        <input class="form-check-input m-0 notif-item" type="checkbox"
               value="${n.id}" ${checked} ${disabled}>
        <span class="text-body">${n.nombre}</span>
        ${candado}
      </label>
    `;
        }).join('');
    }
    function initModuloNotificaciones() {
        if (typeof usuario === 'undefined' || !usuario) return;

        Notificaciones.inicializar(usuario);
        renderDropdown(usuario);

        const btnAplicar = document.getElementById('btnAplicarNotif');
        if (btnAplicar && !btnAplicar.dataset.bound) {
            btnAplicar.dataset.bound = '1';
            btnAplicar.addEventListener('click', () => {
                const seleccionadas = [...document.querySelectorAll('.notif-item:checked')]
                    .map(cb => cb.value)
                    .filter(id => Notificaciones.puedeActivar(usuario, id));

                Notificaciones.setActivas(usuario, seleccionadas);
                renderDropdown(usuario);

                const btn = document.getElementById('btnConfigNotif');
                if (btn && window.bootstrap?.Dropdown) {
                    bootstrap.Dropdown.getOrCreateInstance(btn).hide();
                }
            });
        }
    }

    window.initModuloNotificaciones = initModuloNotificaciones;
</script>