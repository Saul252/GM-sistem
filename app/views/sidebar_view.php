<?php
date_default_timezone_set('America/Mexico_City');
$modulos = [
    [
        'id_grupo' => 'general',
        'titulo' => 'General',
        'icono' => 'bi-grid',
        'submodulos' => [
            ['id' => 'inicio', 'url' => '/cfsistem/app/views/inicio.php', 'icon' => 'bi-house-door', 'label' => 'Inicio', 'active' => ($archivoActual == 'inicio.php')],
        ]
    ],
    [
        'id_grupo' => 'ventas_clientes',
        'titulo' => 'Ventas y Clientes',
        'icono' => 'bi-cart-check',
        'submodulos' => [
            ['id' => 'ventas', 'url' => '/cfsistem/app/controllers/ventasController.php', 'icon' => 'bi-cart-check', 'label' => 'Ventas', 'active' => ($archivoActual == 'ventasController.php')],
            ['id' => 'remisiones', 'url' => '/cfsistem/app/controllers/requisicionesController.php', 'icon' => 'bi-receipt-cutoff', 'label' => 'Remisiones', 'active' => ($archivoActual == 'requisicionesController.php')],
            ['id' => 'cajaRapida', 'url' => '/cfsistem/app/controllers/cajaRapidaController.php', 'icon' => 'bi-lightning-charge', 'label' => 'Caja Rápida', 'active' => ($archivoActual == 'cajaRapidaController.php')],
            ['id' => 'cotizaciones', 'url' => '/cfsistem/app/controllers/cotizacionesController.php', 'icon' => 'bi-person-badge-fill', 'label' => 'Cotizaciones', 'active' => ($archivoActual == 'cotizacionesController.php')],
            ['id' => 'clientes', 'url' => '/cfsistem/app/controllers/clientesController.php', 'icon' => 'bi-person-lines-fill', 'label' => 'Clientes', 'active' => ($archivoActual == 'clientesController.php')],
            ['id' => 'clientesEstatus', 'url' => '/cfsistem/app/controllers/clientesEstatusController.php', 'icon' => 'bi-person-badge', 'label' => 'Estatus Clientes', 'active' => ($archivoActual == 'clientesEstatus.php')],
            ['id' => 'ventasVendedor', 'url' => '/cfsistem/app/controllers/historialPedidosVendedorController.php', 'icon' => 'bi-person-badge-fill', 'label' => 'Ventas Vendedor', 'active' => ($archivoActual == 'historialPedidosVendedorController.php')],
            ['id' => 'ventashistorial', 'url' => '/cfsistem/app/controllers/ventasHistorialController.php', 'icon' => 'bi-receipt', 'label' => 'Historial de Ventas', 'active' => ($archivoActual == 'ventasHistorialController.php')],
            ['id' => 'comprobantes', 'url' => '/cfsistem/app/controllers/comprobantesPagoController.php', 'icon' => 'bi-file-earmark-check', 'label' => 'Comprobantes de Pago', 'active' => ($archivoActual == 'comprobantesPagoController.php')],
            ['id' => 'registrarPagos', 'url' => '/cfsistem/app/controllers/registrarPagosController.php', 'icon' => 'bi-credit-card', 'label' => 'Registrar Pagos', 'active' => ($archivoActual == 'registrarPagosController.php')],
       ['id' => 'historialPagos', 'url' => '/cfsistem/app/controllers/historialPagosController.php', 'icon' => 'bi-coin', 'label' => 'Historial Pagos', 'active' => ($archivoActual == 'registrarPagosController.php')],
        ]
    ],
    [
        'id_grupo' => 'compras_proveedores',
        'titulo' => 'Compras y Proveedores',
        'icono' => 'bi-bag-check',
        'submodulos' => [
            ['id' => 'compras', 'url' => '/cfsistem/app/controllers/egresosController.php', 'icon' => 'bi-bag-check', 'label' => 'Compras y Gastos', 'active' => ($archivoActual == 'egresosController.php' || $archivoActual == 'gastos.php')],
            ['id' => 'proveedores', 'url' => '/cfsistem/app/controllers/proveedoresController.php', 'icon' => 'bi-person-badge', 'label' => 'Proveedores', 'active' => ($archivoActual == 'proveedoresController.php')],
            ['id' => 'solicitudesCompra', 'url' => '/cfsistem/app/controllers/solicitudesCompraController.php', 'icon' => 'bi-cart-check-fill', 'label' => 'Solicitudes Compra', 'active' => ($archivoActual == 'solicitudesCompraController.php')],
        ]
    ],
    [
        'id_grupo' => 'inventario_almacen',
        'titulo' => 'Inventario y Almacén',
        'icono' => 'bi-box-seam',
        'submodulos' => [
            ['id' => 'almacenes', 'url' => '/cfsistem/app/controllers/almacenes.php', 'icon' => 'bi-box-seam', 'label' => 'Almacén', 'active' => ($archivoActual == 'almacenes.php' || $archivoActual == 'almacen.php')],
            ['id' => 'movimientos', 'url' => '/cfsistem/app/controllers/movimientosController.php', 'icon' => 'bi-arrow-left-right', 'label' => 'Movimientos', 'active' => ($archivoActual == 'movimientosController.php')],
            ['id' => 'Mermas', 'url' => '/cfsistem/app/controllers/mermasController.php', 'icon' => 'bi-exclamation-triangle', 'label' => 'Mermas', 'active' => ($archivoActual == 'mermasController.php')],
            ['id' => 'transmutaciones', 'url' => '/cfsistem/app/controllers/transmutacionesController.php', 'icon' => 'bi-arrow-repeat', 'label' => 'Conversiones', 'active' => ($archivoActual == 'transmutacionesController.php')],
            ['id' => 'historialLotes', 'url' => '/cfsistem/app/controllers/lotesHistorialController.php', 'icon' => 'bi-clock-history', 'label' => 'Historial de Lotes', 'active' => ($archivoActual == 'lotesHistorialController.php')],
            ['id' => 'comprasHistorial', 'url' => '/cfsistem/app/controllers/comprasHistorialController.php', 'icon' => 'bi-collection', 'label' => 'Historial de Compras', 'active' => ($archivoActual == 'comprasHistorialController.php')],
        ]
    ],
    [
        'id_grupo' => 'finanzas_tesoreria',
        'titulo' => 'Finanzas y Tesorería',
        'icono' => 'bi-graph-up-arrow',
        'submodulos' => [
            ['id' => 'finanzas', 'url' => '/cfsistem/app/controllers/finanzasController.php', 'icon' => 'bi-graph-up-arrow', 'label' => 'Finanzas', 'active' => ($archivoActual == 'finanzasController.php')],
            ['id' => 'finanzas_admin', 'url' => '/cfsistem/app/controllers/finanzasAdmController.php', 'icon' => 'bi-bar-chart-line', 'label' => 'Finanzas Admin', 'active' => ($archivoActual == 'finanzasAdmController.php')],
            ['id' => 'corteCaja', 'url' => '/cfsistem/app/controllers/corteCajaController.php', 'icon' => 'bi-calculator', 'label' => 'Corte de Caja', 'active' => ($archivoActual == 'corteCajaController.php')],
            ['id' => 'tesoreria', 'url' => '/cfsistem/app/controllers/tesoreriaController.php', 'icon' => 'bi-safe', 'label' => 'Tesorería', 'active' => ($archivoActual == 'tesoreriaController.php')],
        ]
    ],
    [
        'id_grupo' => 'logistica_distribucion',
        'titulo' => 'Logística y Distribución',
        'icono' => 'bi-truck',
        'submodulos' => [
            ['id' => 'entregas', 'url' => '/cfsistem/app/controllers/entregasController.php', 'icon' => 'bi-truck', 'label' => 'Despachos', 'active' => ($archivoActual == 'entregasController.php')],
            ['id' => 'vehiculos', 'url' => '/cfsistem/app/controllers/vehiculosController.php', 'icon' => 'bi-truck-front-fill', 'label' => 'Vehículos', 'active' => ($archivoActual == 'vehiculosController.php')],
            ['id' => 'repartos', 'url' => '/cfsistem/app/controllers/repartosController.php', 'icon' => 'bi-truck-flatbed', 'label' => 'Repartos', 'active' => ($archivoActual == 'repartosController.php')],
            ['id' => 'misRepartos', 'url' => '/cfsistem/app/controllers/misRepartosController.php', 'icon' => 'bi-map-fill', 'label' => 'Mis Repartos', 'active' => ($archivoActual == 'misRepartosController.php')],
            ['id' => 'viajesTrabajadores', 'url' => '/cfsistem/app/controllers/viajesTrabajadoresController.php', 'icon' => 'bi-person-workspace', 'label' => 'Viajes Trabajadores', 'active' => ($archivoActual == 'viajesTrabajadoresController.php')],
              ['id' => 'mantenimientos', 'url' => '/cfsistem/app/controllers/mantenimientosController.php', 'icon' => 'bi-wrench-adjustable-circle-fill', 'label' => 'Mantenimientos', 'active' => ($archivoActual == 'mantenimientosController.php')],
           ['id' => 'verificaciones', 'url' => '/cfsistem/app/controllers/verificacionesController.php', 'icon' => 'bi-patch-check-fill', 'label' => 'verificaciones', 'active' => ($archivoActual == 'verificacionesController.php')],
          
        ]
    ],
    [
        'id_grupo' => 'recursos_humanos',
        'titulo' => 'Recursos Humanos',
        'icono' => 'bi-people-fill',
        'submodulos' => [
            ['id' => 'trabajadores', 'url' => '/cfsistem/app/controllers/trabajadoresController.php', 'icon' => 'bi-people-fill', 'label' => 'Trabajadores', 'active' => ($archivoActual == 'trabajadoresController.php')],
        ['id' => 'nomina', 'url' => '/cfsistem/app/controllers/nominaController.php', 'icon' => 'bi-cash', 'label' => 'nomina', 'active' => ($archivoActual == 'nominaController.php')],
        ['id' => 'prestamos', 'url' => '/cfsistem/app/controllers/prestamosController.php', 'icon' => 'bi-cash', 'label' => 'prestamos', 'active' => ($archivoActual == 'prestamosController.php')],
        ['id' => 'faltas', 'url' => '/cfsistem/app/controllers/faltasController.php', 'icon' => 'bi-calendar-x', 'label' => 'Faltas', 'active' => ($archivoActual == 'faltasController.php')],
       ['id' => 'vacaciones', 'url' => '/cfsistem/app/controllers/vacacionesController.php', 'icon' => 'bi-sun-fill', 'label' => 'Vacaciones', 'active' => ($archivoActual == 'vacacionesController.php')],
         ['id' => 'pagos_viajes', 'url' => '/cfsistem/app/controllers/pagos_viajesController.php', 'icon' => 'bi-person-gear', 'label' => 'pagos_viajes', 'active' => ($archivoActual == 'pagos_viajesController.php')],
        ]
    ],
    [
        'id_grupo' => 'administracion',
        'titulo' => 'Administración',
        'icono' => 'bi-gear',
        'submodulos' => [
            ['id' => 'usuarios', 'url' => '/cfsistem/app/controllers/usuariosController.php', 'icon' => 'bi-people', 'label' => 'Usuarios', 'active' => ($archivoActual == 'usuariosController.php')],
        ]
    ]
];
?>
<sytile>
    .texto{
        color:#000 !important;
    }
</sytile>
  <link rel="icon" type="image/png" href="/cfsistem/public/assets/logo.png">
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
<nav class="navbar fixed-top navbar-expand navbar-dark navbar-premium shadow-sm">
    <div class="container-fluid px-2 px-md-4">
        <div class="d-flex align-items-center gap-2 gap-md-3">
            <button class="btn btn-toggle border-0" id="toggleSidebar" aria-label="Abrir Menú">
                <i class="bi bi-list fs-2 text-white"></i>
            </button>
        </div>

        <div class="d-flex align-items-center gap-2 gap-md-3">
            <button type="button" class="btn  d-flex align-items-center gap-2" id="btnThemeToggle" onclick="alternarModoOscuro()">
                <i class="bi bi-moon-stars-fill" id="themeIcon"></i> 
                <span class="texto" id="themeLabel">Modo Oscuro</span>
            </button>

            <div class="dropdown">
                <a href="javascript:void(0);" class="text-white position-relative p-2" id="btnNotif" data-bs-toggle="dropdown">
                    <i class="bi bi-bell fs-4"></i>
                    <span id="notif-badge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger d-none">0</span>
                </a>
                <!-- Menú desplegable con bg-body y border adaptable -->
                <ul class="dropdown-menu dropdown-menu-end shadow-lg border p-0" id="menuNotif" style="width: 320px; max-width: 90vw; max-height: 400px; overflow-y: auto;">
                    <li class="p-3 border-bottom bg-body-tertiary">
                        <h6 class="mb-0 fw-bold text-body">Notificaciones</h6>
                    </li>
                    <div id="lista-notificaciones">
                        <li class="p-3 text-center text-body-secondary small">Cargando...</li>
                    </div>
                </ul>
            </div>

            <div class="user-badge d-flex align-items-center text-white bg-white bg-opacity-10 px-3 py-1 rounded-pill">
                <i class="bi bi-person-circle fs-5"></i>
                <span class="ms-2 d-none d-md-inline small"><?= $_SESSION['nombre'] ?? 'Usuario' ?></span>
            </div>

            <a href="/cfsistem/logout.php" class="btn btn-sm btn-outline-light border-0 rounded-circle" title="Cerrar Sesión">
                <i class="bi bi-box-arrow-right fs-4"></i>
            </a>
        </div>
    </div>
</nav>

<!-- Sidebar con bg-body-tertiary para adaptarse al tema -->
<aside id="sidebar" class="bg-body-tertiary border-end shadow-sm">
    <div class="p-3">
        <div class="text-center mb-4">
            <h5 class="fw-bold text-primary mb-1">Menú</h5>
            <?php if (!empty($_SESSION['rol'])): ?>
            <span class="badge bg-body-secondary text-body-secondary border">Rol: <?= ucfirst($_SESSION['rol']) ?></span>
            <?php endif; ?>
        </div>

        <ul class="nav nav-pills flex-column gap-1">
            <?php foreach ($modulos as $grupo): ?>
                <?php 
                $submodulosPermitidos = array_filter($grupo['submodulos'], function($sub) {
                    return puedeVerModulo($sub['id']);
                });

                if (empty($submodulosPermitidos)) continue;

                $grupoActivo = false;
                foreach ($submodulosPermitidos as $sub) {
                    if ($sub['active']) {
                        $grupoActivo = true;
                        break;
                    }
                }
                ?>

                <li class="nav-item">
                    <!-- Enlace principal con text-body y bg-body-secondary cuando está activo -->
                    <a href="#drop-<?= $grupo['id_grupo'] ?>" 
                       class="nav-link d-flex align-items-center justify-content-between gap-3 <?= $grupoActivo ? 'bg-body-secondary text-body fw-bold' : 'text-body' ?>" 
                       data-bs-toggle="collapse" 
                       aria-expanded="<?= $grupoActivo ? 'true' : 'false' ?>">
                        
                        <div class="d-flex align-items-center gap-3">
                            <i class="<?= $grupo['icono'] ?> fs-5"></i>
                            <span><?= $grupo['titulo'] ?></span>
                        </div>
                        <i class="bi bi-chevron-down small transition-icon"></i>
                    </a>

                    <div class="collapse <?= $grupoActivo ? 'show' : '' ?>" id="drop-<?= $grupo['id_grupo'] ?>">
                        <ul class="nav nav-pills flex-column gap-1 ps-4 pt-1 pb-1">
                            <?php foreach ($submodulosPermitidos as $m): ?>
                                <li class="nav-item">
                                    <a href="<?= $m['url'] ?>"
                                       class="nav-link d-flex align-items-center gap-3 <?= $m['active'] ? 'active shadow-sm' : 'text-body-secondary' ?>" style="font-size: 0.95rem;">
                                        <i class="<?= $m['icon'] ?> fs-6"></i>
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
<div class="modal fade" id="modalConfirmarCancelacion" tabindex="-1" aria-labelledby="modalConfirmarCancelacionLabel" aria-hidden="true">
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
                    <input type="text" id="inputMotivoSinPago" class="form-field form-control" placeholder="Escriba el motivo...">
                </div>
            </div>
            <div class="modal-footer d-flex justify-content-between">
                <!-- Botón Cancelar: SOLO CIERRA EL MODAL MEDIANTE BOOTSTRAP -->
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
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>';
<script>
/**
 * CF SYSTEM - LÓGICA GLOBAL DE INTERFAZ Y NOTIFICACIONES
 * Versión: 2.0 (Optimizado para Móvil y Escritorio - No jQuery)
 */
/* CF SYSTEM - LÓGICA GLOBAL DE INTERFAZ Y NOTIFICACIONES
 * Versión: 2.0 (Optimizado para Móvil y Escritorio - No jQuery)
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
    const btnNotif = document.getElementById('btnNotif');
    const menuNotif = document.getElementById('menuNotif');

    let overlay = document.querySelector('.sidebar-overlay') || document.createElement('div');
    if (!overlay.parentElement) {
        overlay.className = 'sidebar-overlay';
        document.body.appendChild(overlay);
    }

   // Eventos UI
if (sidebar) {
    // 1. OCULTAR EN AUTOMÁTICO AL CARGAR LA PÁGINA
    if (window.innerWidth <= 992) {
        sidebar.classList.remove('show');
        if (overlay) overlay.classList.remove('active');
    } else {
        sidebar.classList.add('hidden');
        document.body.classList.add('sidebar-hidden');
    }
}

if (toggleBtn) {
    // 2. EVENTO PARA ALTERNAR VISIBILIDAD EN CADA CLIC
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

    if (btnNotif && menuNotif) {
        btnNotif.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            menuNotif.style.display = (menuNotif.style.display === 'block') ? 'none' : 'block';
        });
    }

    document.addEventListener('click', (e) => {
        if (menuNotif && btnNotif && !menuNotif.contains(e.target) && !btnNotif.contains(e.target)) {
            menuNotif.style.display = 'none';
        }
    });

    // Iniciar Polling
    mantenimientoSistema();
    setInterval(mantenimientoSistema, 35000);
});

/* ==========================================================================
   RENDERIZADOR CENTRALIZADO (COMBINA TODAS LAS NOTIFICACIONES)
   ========================================================================== */
function renderizarListaNotificaciones() {
    const lista = document.getElementById('lista-notificaciones');
    const badge = document.getElementById('notif-badge');

    // Concatenar todos los HTMLs generados por cada módulo
    const todosLosHTML = [
        ...almacenNotificaciones.traspasos,
        ...almacenNotificaciones.cancelaciones,
        ...almacenNotificaciones.mantenimientos,
        ...almacenNotificaciones.verificaciones
    ];

    const totalNotificaciones = todosLosHTML.length;

    // 1. Actualizar el Badge con la suma total
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

    // 2. Renderizar la lista consolidada sin borrar los otros tipos
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
    verificarCancelacionesRecientes();
     verificarNotificaciones();
    if (usuario <= 2) {
       
        verificarMantenimientos();
        verificarSolicitudesCancelacion();
        verificarVerificaciones();
    }
}
/* ==========================================================================
   CONSULTA DE CANCELACIONES/ELIMINACIONES RECIENTES (RANGO 5 MINUTOS)
   ========================================================================== */
// Set en memoria para evitar que se repita la notificación durante los 5 minutos
const idsCancelacionesNotificadas = new Set();

function verificarCancelacionesRecientes() {
    fetch('/cfsistem/app/controllers/ventasHistorialController.php?action=obtenerCancelacionesRecientes')
        .then(res => res.json())
        .then(res => {
            if (res.status === 'success' && Array.isArray(res.data) && res.data.length > 0) {
                
                res.data.forEach(item => {
                    // Si el ID de la solicitud no ha sido notificado aún en este navegador
                    if (!idsCancelacionesNotificadas.has(item.id)) {
                        
                        // 1. Guardar en memoria local
                        idsCancelacionesNotificadas.add(item.id);

                        // 2. Disparar Toastify único para todos los usuarios
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

                        // 3. Recargar el listado de ventas si la pantalla del usuario la contiene
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

            // Alerta Toastify individual
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

            // Guardar HTMLs en el almacén
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

            if (cantidad > 0 && (estado.primeraCarga || cantidad > estado.ultimoConteo)) {
                if (typeof Toastify === "function") {
                    const u = items[0] || {};
                    Toastify({
                        text: `📦 TRASPASO RECIBIDO\n${u.emisor} envió ${u.cantidad_texto || u.cantidad} de ${u.producto}`,
                        duration: 1500,
                        close: true,
                        gravity: "top",
                        position: "right",
                        className: "toast-traspaso toast-red-close",
                        style: { background: "#ffffff", color: "#000", borderRadius: "14px", padding: "16px 20px" },
                        onClick: () => window.location.href = "/cfsistem/app/controllers/almacenes.php"
                    }).showToast();
                }
                estado.primeraCarga = false;
            }
            estado.ultimoConteo = cantidad;

            // Guardar HTMLs en el almacén
            almacenNotificaciones.traspasos = items.map(item => `
                <div class="d-flex align-items-center justify-content-between p-3 border-bottom bg-white hover-notif">
                    <div style="flex: 1; line-height: 1.4;">
                        <b class="text-primary d-block small text-uppercase text-success">${item.producto}</b>
                        <b class="d-block text-body-secondary text-success" style="font-size: 0.75rem;">De: ${item.emisor}</b>
                        <div class="mt-1"><b class="text-primary d-block small text-uppercase text-success">${item.cantidad_texto || (item.cantidad + ' PZA')}</b></div>
                    </div>
                    <button onclick="procesarRecepcionRapida(${item.id})" class="btn btn-success btn-sm rounded-circle shadow-sm" style="width:32px; height:32px;"><i class="bi bi-check-lg"></i></button>
                </div>`);

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

            // Guardar HTMLs en el almacén
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
                        <button class="btn btn-dark btn-sm rounded-circle" onclick="window.location='/cfsistem/app/controllers/mantenimientos.php?id=${item.id_mantenimiento}'">
                            <i class="bi bi-arrow-right text-danger"></i>
                        </button>
                    </div>`;
            });

            renderizarListaNotificaciones();
        })
        .catch(err => console.error("Error mantenimientos:", err));
}
function verificarVerificaciones() {
    // 1. Se corrigió la acción de la URL a "listarProximaVerificacion"
    fetch("/cfsistem/app/controllers/verificacionesController.php?action=listarProximaVerificacion")
        .then(r => r.json())
        .then(data => {
            const items = Array.isArray(data) ? data : [];
            const cantidad = items.length;
            const estado = estadoToasts.verificacion;

            // 2. Notificación Toastify con la misma estructura visual
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

            // 3. Guardar HTMLs en el almacén global (Igual que Mantenimientos)
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

            // 4. Renderizar la lista desde la función global
            renderizarListaNotificaciones();
        })
        .catch(err => console.error("Error verificaciones:", err));
}let ultimoIdVentaEliminadaNotificada = 0;

function verificarVentasEliminadasGlobales() {
    fetch('/cfsistem/app/controllers/ventasHistorialController.php?action=obtenerUltimaVentaEliminada')
        .then(res => res.json())
        .then(data => {
            // Si hay una venta eliminada reciente y no la hemos notificado aún en esta sesión
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

                // Actualizar tablas en pantalla si el usuario está viendo el listado
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
}function procesarAceptarCancelacion(idVenta, id, pagado, total, folio, razon) {
    const montoPagado = parseFloat(pagado) || 0;
    const modalElem = document.getElementById('modalConfirmarCancelacion');
    const modalBs = bootstrap.Modal.getOrCreateInstance(modalElem);
    
    const textoDetalle = document.getElementById('modalTextoDetalle');
    const textoSub = document.getElementById('modalTextoSub');
    const wrapperMotivo = document.getElementById('wrapperMotivoSinPago');
    const contenedorBotones = document.getElementById('contenedorBotonesAccion');

    textoDetalle.textContent = `¿Aceptar y Cancelar Venta ${folio || '#' + idVenta}?`;
    
    // Configurar estado según pago
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

// Handler auxiliar cuando no hay pago
function confirmarSinPago(idVenta, id) {
    const motivoInput = document.getElementById('inputMotivoSinPago').value.trim();
    if (!motivoInput) {
        alert("¡El motivo es obligatorio!");
        return;
    }
    ejecutarCancelacionEncadenada(idVenta, id, false, motivoInput);
}

// Ejecuta las peticiones backend tras cerrar el modal
async function ejecutarCancelacionEncadenada(idVenta, id, conSaldo, motivo) {
    // Cerrar modal Bootstrap
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
        // 1. Aceptar solicitud
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

        // 2. Anular / Reintegrar
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

window.procesarRecepcionRapida = function(id) {
    if (!confirm("¿Confirmar recepción de material?")) return;
    const formData = new FormData();
    formData.append('id', id);

    fetch('/cfsistem/app/controllers/traspasosController.php?action=recibirTraspaso', { method: 'POST', body: formData })
        .then(res => res.json())
        .then(data => {
            if (data.success || data.status === 'success') location.reload();
            else alert("Error: " + (data.message || "No se pudo procesar"));
        })
        .catch(err => console.error(err));
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
    // Si está en 'dark' lo cambia a 'light', si no, a 'dark'
    const nuevoTema = html.getAttribute('data-bs-theme') === 'dark' ? 'light' : 'dark';
    
    // 1. Aplica el atributo en el HTML (esto activa tus estilos CSS)
    html.setAttribute('data-bs-theme', nuevoTema);
    
    // 2. Guarda la elección en el navegador
    localStorage.setItem('theme', nuevoTema);
    
    // 3. Actualiza el icono/texto del botón si existe en la vista
    if (typeof actualizarBotonTema === 'function') {
        actualizarBotonTema(nuevoTema);
    }
}
    (function() {
        const savedTheme = localStorage.getItem('theme') || 'light';
        document.documentElement.setAttribute('data-bs-theme', savedTheme);
    })();
</script>
