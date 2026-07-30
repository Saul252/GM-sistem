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
            ['id' => 'comprobantes', 'url' => '/cfsistem/app/controllers/comprobantesPagoController.php', 'icon' => 'bi-file-earmark-check', 'label' => 'Crear Comprobantes', 'active' => ($archivoActual == 'comprobantesPagoController.php')],
            ['id' => 'registrarPagos', 'url' => '/cfsistem/app/controllers/registrarPagosController.php', 'icon' => 'bi-credit-card', 'label' => 'Registrar Pagos', 'active' => ($archivoActual == 'registrarPagosController.php')],
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


<nav class="navbar fixed-top navbar-expand navbar-dark navbar-premium shadow-sm">
    <div class="container-fluid px-2 px-md-4">
        <div class="d-flex align-items-center gap-2 gap-md-3">
            <button class="btn btn-toggle border-0" id="toggleSidebar" aria-label="Abrir Menú">
                <i class="bi bi-list fs-2 text-white"></i>
            </button>
        </div>

        <div class="d-flex align-items-center gap-2 gap-md-3">
            <button type="button" class="btn btn-toggle-all d-flex align-items-center gap-2" id="btnThemeToggle" onclick="alternarModoOscuro()">
                <i class="bi bi-moon-stars-fill" id="themeIcon"></i> 
                <span id="themeLabel">Modo Oscuro</span>
            </button>

            <div class="dropdown">
                <a href="javascript:void(0);" class="text-white position-relative p-2" id="btnNotif" data-bs-toggle="dropdown">
                    <i class="bi bi-bell fs-4"></i>
                    <span id="notif-badge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger d-none">0</span>
                </a>
                <!-- Menú desplegable con bg-body y border adaptable -->
                <ul class="dropdown-menu dropdown-menu-end shadow-lg border p-0" id="menuNotif" style="width: 320px; max-width: 90vw; max-height: 400px; overflow-y: auto;">
                    <li class="p-3 border-bottom bg-body-tertiary">
                        <h6 class="mb-0 fw-bold text-body">Traspasos Pendientes</h6>
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
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>';
<script>
/**
 * CF SYSTEM - LÓGICA GLOBAL DE INTERFAZ Y NOTIFICACIONES
 * Versión: 2.0 (Optimizado para Móvil y Escritorio - No jQuery)
 */
/* CF SYSTEM - LÓGICA GLOBAL DE INTERFAZ Y NOTIFICACIONES
 * Versión: 2.0 (Optimizado para Móvil y Escritorio - No jQuery)
 */

document.addEventListener('DOMContentLoaded', () => {
let ultimoConteoMantenimientos = 0;
let primeraCargaMantenimiento = true;

function verificarMantenimientos() {

    const url = "/cfsistem/app/controllers/mantenimientosController.php?action=listarProximoMantenimiento";

    fetch(url)
        .then(r => r.json())
        .then(data => {
            console.log(data);

           

            const badge = document.getElementById("badge-mantenimientos");
            const lista = document.getElementById("lista-mantenimientos");

            const cantidadActual = parseInt((data.length)) || 0;
 console.log(cantidadActual);
            // Badge
            if (badge) {

                if (cantidadActual > 0) {

                    badge.innerText = cantidadActual;
                    badge.classList.remove("d-none");

                } else {

                    badge.classList.add("d-none");

                }

            }

            // Toast
            if (cantidadActual > 0 &&
                (primeraCargaMantenimiento || cantidadActual > ultimoConteoMantenimientos)) {

                const item = data[0];

                Toastify({

                    text:
`🚗 PRÓXIMO MANTENIMIENTO
${item.estado}

${item.vehiculo}   ${item.placas}`,

                    duration: 7000,
                    gravity: "top",
                    position: "right",
                    close: true,
                    stopOnFocus: true,

                    className: "toast-mantenimiento",

                    style: {

                        background: "#fff",

                        color: "#111",

                        border: "1px solid #dee2e6",

                        borderLeft: "5px solid #ffc107",

                        borderRadius: "15px",

                        boxShadow: "0 10px 25px rgba(0,0,0,.15)",

                        fontWeight: "500",

                        fontSize: "14px",

                        padding: "18px"

                    },

                    onClick: function(){

                        window.location.href="/cfsistem/app/controllers/mantenimientosController.php";

                    }

                }).showToast();

                primeraCargaMantenimiento = false;

            }

            ultimoConteoMantenimientos = cantidadActual;

            // Lista del dropdown

            if(lista){

                if(cantidadActual==0){

                    lista.innerHTML=`
                        <div class="p-4 text-center text-muted">
                            No hay mantenimientos próximos.
                        </div>`;

                }else{

                    lista.innerHTML=data.map(item=>{

                        let color="success";

                        if(item.dias_restantes<=0){

                            color="danger";

                        }else if(item.dias_restantes<=3){

                            color="warning";

                        }

                        return`

<div class="d-flex justify-content-between align-items-center p-3 border-bottom hover-notif">

<div>

<div class="small">

<b>${item.estado}</b>

</div>
<div class="fw-bold text-${color}">
Vehiculo: ${item.vehiculo}
</div>


<div class="small text-secondary">

${item.placas}

</div>



</div>

<button class="btn btn-outline-primary btn-sm rounded-circle"

onclick="window.location='/cfsistem/app/controllers/mantenimientos.php?id=${item.id_mantenimiento}'">

<i class="bi bi-arrow-right"></i>

</button>

</div>

`;

                    }).join("");

                }

            }

        })
        .catch(err=>console.error(err));

}
    // --- 1. VARIABLES DE ELEMENTOS ---
    const toggleBtn = document.getElementById('toggleSidebar');
    const sidebar = document.getElementById('sidebar');
    const btnNotif = document.getElementById('btnNotif');
    const menuNotif = document.getElementById('menuNotif');

    // Crear el overlay para móvil si no existe
    let overlay = document.querySelector('.sidebar-overlay');
    if (!overlay) {
        overlay = document.createElement('div');
        overlay.className = 'sidebar-overlay';
        document.body.appendChild(overlay);
    }

    // --- 2. ESTADO OCULTO POR DEFAULT ---
    const isMobile = window.innerWidth <= 992;
    if (isMobile) {
        // En móvil asegura que no tenga las clases activas
        sidebar.classList.remove('show');
        overlay.classList.remove('active');
    } else {
        // En escritorio agrega las clases para que inicie oculto
        sidebar.classList.add('hidden');
        document.body.classList.add('sidebar-hidden');
    }

    // --- 3. LÓGICA DEL SIDEBAR (RESPONSIVO) ---
    function toggleMenu() {
        const isMobileNow = window.innerWidth <= 992;

        if (isMobileNow) {
            sidebar.classList.toggle('show');
            overlay.classList.toggle('active');
        } else {
            sidebar.classList.toggle('hidden');
            document.body.classList.toggle('sidebar-hidden');
        }
    }

    if (toggleBtn) {
        toggleBtn.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            toggleMenu();
        });
    }

    overlay.addEventListener('click', () => {
        sidebar.classList.remove('show');
        overlay.classList.remove('active');
    });

    // --- 4. CIERRE DE MENÚS AL HACER CLIC FUERA ---
    document.addEventListener('click', (e) => {
        // Cerrar notificaciones si está abierto y se da clic fuera
        if (menuNotif && btnNotif && !menuNotif.contains(e.target) && !btnNotif.contains(e.target)) {
            menuNotif.style.display = 'none';
        }
    });

    // --- 3. SISTEMA DE NOTIFICACIONES DE TRASPASOS ---
    let ultimoConteoTraspasos = 0;
    let primeraCarga = true;
    let corteProcesadoHoy = false;

    function verificarNotificaciones() {
        const url = '/cfsistem/app/backend/movimientos/get_notificaciones_traspaso.php?t=' + Date.now();

        fetch(url)
            .then(response => response.json())
            .then(data => {
                const badge = document.getElementById('notif-badge');
                const lista = document.getElementById('lista-notificaciones');
                const cantidadActual = parseInt(data.cantidad) || 0;

                if (badge) {
                    if (cantidadActual > 0) {
                        badge.innerText = cantidadActual;
                        badge.classList.remove('d-none');
                        badge.style.display = 'inline-block';
                    } else {
                        badge.classList.add('d-none');
                        badge.style.display = 'none';
                    }
                }

                if (cantidadActual > 0 && (primeraCarga || cantidadActual > ultimoConteoTraspasos)) {
                    if (typeof Toastify === "function") {
                        const u = data.items[0] || {};
                        const textoCant = u.cantidad_texto || (u.cantidad + ' PZA');

                        Toastify({
                            text: `📦 TRASPASO RECIBIDO\n${u.emisor} envió ${textoCant} de ${u.producto}`,
                            duration: 6000,
                            close: true,
                            gravity: "top",
                            position: "right",
                            stopOnFocus: true,
                            className: "toast-traspaso",
                            style: {
                                background: "#ffffff",
                                color: "#000000",
                                borderRadius: "14px",
                                border: "1px solid #e2e8f0",
                                boxShadow: "0 10px 15px -3px rgba(0,0,0,0.1)",
                                fontFamily: "'Segoe UI', Roboto, sans-serif",
                                fontSize: "14px",
                                fontWeight: "500",
                                padding: "16px 20px"
                            },
                            offset: {
                                x: 15,
                                y: 15
                            },
                            onClick: function() {
                                window.location.href =
                                "/cfsistem/app/controllers/almacenes.php";
                            }
                        }).showToast();

                        if (!document.getElementById('style-toast-red-close')) {
                            const style = document.createElement('style');
                            style.id = 'style-toast-red-close';
                            style.innerHTML =
                                `.toast-traspaso .toast-close { color: #ff0000 !important; opacity: 1; font-weight: bold; font-size: 20px; margin-left: 10px; }`;
                            document.head.appendChild(style);
                        }
                    }
                    primeraCarga = false;
                }

                ultimoConteoTraspasos = cantidadActual;

                if (lista && data.items) {
                    if (cantidadActual === 0) {
                        lista.innerHTML =
                            '<div class="p-4 text-center text-muted small">Sin traspasos pendientes</div>';
                    } else {
                        lista.innerHTML = data.items.map(item => {
                            const mostrarCantidad = item.cantidad_texto ? item.cantidad_texto : (
                                item.cantidad + ' PZA');
                            return `
                            <div class="d-flex align-items-center justify-content-between p-3 border-bottom bg-white hover-notif">
                                <div style="flex: 1; line-height: 1.4;">
                                    <b class="text-primary d-block small text-uppercase text-success">${item.producto}</b>
                                    <b class="d-block text-muted text-success" style="font-size: 0.75rem;">De: ${item.emisor}</b>
                                    <div class="mt-1">
                                        <b class="text-primary d-block small text-uppercase text-success">${mostrarCantidad}</b>
                                    </div>
                                </div>
                                <button onclick="procesarRecepcionRapida(${item.id})" 
                                        class="btn btn-success btn-sm rounded-circle shadow-sm d-flex align-items-center justify-content-center" 
                                        style="width: 32px; height: 32px;">
                                    <i class="bi bi-check-lg"></i>
                                </button>
                            </div>`;
                        }).join('');
                    }
                }
            })
            .catch(err => console.error("❌ Error fetch notificaciones:", err));
    }

    function verificarCorteCaja() {
        const hoy = new Date().toISOString().split('T')[0];
        const corteYaHecho = localStorage.getItem('corte_finalizado_fecha') === hoy;
        const estaProcesando = localStorage.getItem('corte_en_progreso') === 'true';

        if (corteYaHecho || estaProcesando) return;

        const ahora = new Date();
        const horaActual = ahora.getHours().toString().padStart(2, '0') + ":" + ahora.getMinutes().toString()
            .padStart(2, '0');
        const horaCierreConfig = localStorage.getItem('config_hora_cierre') || '12:01';

        console.log("corte de caja", horaCierreConfig);
        if (horaActual >= horaCierreConfig) {
            ejecutarRondaDeCorte();
        }
    }

    function ejecutarRondaDeCorte() {
        localStorage.setItem('corte_en_progreso', 'true');
        console.log("🚀 Procesando bloque de almacenes...");

        fetch('/cfsistem/app/backend/funciones/corteApi.php?action=check_sistema', {
                method: 'POST'
            })
            .then(response => {
                // Si la respuesta no es 200-299, extraemos el texto para ver el error de PHP
                if (!response.ok) {
                    return response.text().then(text => {
                        throw new Error(`Servidor respondió con ${response.status}: ${text}`);
                    });
                }
                return response.json();
            })
            .then(res => {
                if (res.status === 'success') {
                    if (res.hay_mas) {
                        console.log("⏳ Bloque completado, solicitando siguiente...");
                        ejecutarRondaDeCorte();
                    } else {
                        const hoy = new Date().toISOString().split('T')[0];
                        localStorage.setItem('corte_finalizado_fecha', hoy);
                        localStorage.setItem('corte_en_progreso', 'false');
                        console.log("✅ Proceso completo.");
                        actualizarInterfazCorte(true);
                    }
                } else {
                    console.error("❌ El servidor devolvió un error de lógica:", res.mensaje);
                    localStorage.setItem('corte_en_progreso', 'false');
                }
            })
            .catch(err => {
                localStorage.setItem('corte_en_progreso', 'false');
                // Aquí es donde verás el fallo real en la consola de F12
                console.error("🚨 FALLO CRÍTICO EN LA COMUNICACIÓN:");
                console.error(err.message);
            });
    }

    function actualizarInterfazCorte(estado) {
        const hoy = new Date().toISOString().split('T')[0];
        if (localStorage.getItem('corte_finalizado_fecha') === hoy) {
            estado = true;
        }
        const badgeCorte = document.getElementById('badgeCorte');
        if (badgeCorte && estado) {
            badgeCorte.classList.remove('bg-secondary', 'opacity-50');
            badgeCorte.classList.add('bg-success', 'shadow-sm');
            badgeCorte.innerHTML = '<i class="bi bi-check-circle-fill"></i> Caja Cerrada';
        }
    }

    window.procesarRecepcionRapida = function(id) {
        if (!confirm("¿Confirmar recepción de material?")) return;
        const formData = new FormData();
        formData.append('id', id);

        fetch('/cfsistem/app/controllers/traspasosController.php?action=recibirTraspaso', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success || data.status === 'success') {
                    location.reload();
                } else {
                    alert("Error: " + (data.message || "No se pudo procesar"));
                }
            })
            .catch(err => console.error("Error en recepción:", err));
    };

    if (btnNotif && menuNotif) {
        btnNotif.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            const isVisible = (menuNotif.style.display === 'block');
            menuNotif.style.display = isVisible ? 'none' : 'block';
        });
    }

    function mantenimientoSistema() {
        
        verificarNotificaciones();
        // verificarCorteCaja();
    }

    mantenimientoSistema();
    setInterval(mantenimientoSistema, 35000);
    verificarMantenimientos();
setInterval(verificarMantenimientos(), 35000);

    window.addEventListener('resize', () => {
        if (window.innerWidth > 992) {
            if (sidebar) sidebar.classList.remove('show');
            if (overlay) overlay.classList.remove('active');
        }
    });
});

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
