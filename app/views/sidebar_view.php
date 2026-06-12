<?php
date_default_timezone_set('America/Mexico_City');
/**
 * CONFIGURACIÓN DE MÓDULOS
 * Optimizamos la renderización mediante un bucle para evitar repetir HTML.
 */$modulos = [

   // =========================
   // GENERAL
   // =========================
   ['id' => 'inicio', 'url' => '/cfsistem/app/views/inicio.php', 'icon' => 'bi-house-door', 'label' => 'Inicio', 'active' => ($archivoActual == 'inicio.php')],

   // =========================
   // VENTAS Y CLIENTES
   // =========================
   ['id' => 'ventas', 'url' => '/cfsistem/app/controllers/ventasController.php', 'icon' => 'bi-cart-check', 'label' => 'Ventas', 'active' => ($archivoActual == 'ventasController.php')],
   ['id' => 'cajaRapida', 'url' => '/cfsistem/app/controllers/cajaRapidaController.php', 'icon' => 'bi-lightning-charge', 'label' => 'Caja Rapida', 'active' => ($archivoActual == 'cajaRapidaController.php')],
   ['id' => 'cotizaciones', 'url' => '/cfsistem/app/controllers/cotizacionesController.php', 'icon' => 'bi-person-badge-fill', 'label' => 'Cotizaciones', 'desc' => 'Preventa', 'active' => ($archivoActual == 'cotizacionesController.php')],
   ['id' => 'clientes', 'url' => '/cfsistem/app/controllers/clientesController.php', 'icon' => 'bi-person-lines-fill', 'label' => 'Clientes', 'active' => ($archivoActual == 'clientesController.php')],
   ['id' => 'clientesEstatus', 'url' => '/cfsistem/app/controllers/clientesEstatusController.php', 'icon' => 'bi-person-badge', 'label' => 'Estatus Clientes', 'active' => ($archivoActual == 'clientesEstatus.php')],
   ['id' => 'pedidosVendedor', 'url' => '/cfsistem/app/controllers/pedidosVendedorController.php', 'icon' => 'bi-person-badge-fill', 'label' => 'Pedidos Vendedor', 'desc' => 'Preventa', 'active' => ($archivoActual == 'pedidosVendedorController.php')],
   ['id' => 'ventashistorial', 'url' => '/cfsistem/app/controllers/ventasHistorialController.php', 'icon' => 'bi-receipt', 'label' => 'Historial de Ventas', 'active' => ($archivoActual == 'ventasHistorialController.php')],
['id' => 'registrarPagos', 'url' => '/cfsistem/app/controllers/registrarPagosController.php', 'icon' => 'bi-journal-text', 'label' => 'Registrar Pagos', 'active' => ($archivoActual == 'registrarPagosController.php')],

   // =========================
   // COMPRAS Y PROVEEDORES
   // =========================
   ['id' => 'compras', 'url' => '/cfsistem/app/controllers/egresosController.php', 'icon' => 'bi-bag-check', 'label' => 'Compras y gastos', 'active' => ($archivoActual == 'egresosController.php' || $archivoActual == 'gastos.php')],
   ['id' => 'proveedores', 'url' => '/cfsistem/app/controllers/proveedoresController.php', 'icon' => 'bi-person-badge', 'label' => 'Proveedores', 'active' => ($archivoActual == 'proveedoresController.php')],
   ['id' => 'solicitudesCompra', 'url' => '/cfsistem/app/controllers/solicitudesCompraController.php', 'icon' => 'bi-cart-check-fill', 'label' => 'Solicitudes Compra', 'active' => ($archivoActual == 'solicitudesCompraController.php')],

   // =========================
   // INVENTARIO Y ALMACÉN
   // =========================
   ['id' => 'almacenes', 'url' => '/cfsistem/app/controllers/almacenes.php', 'icon' => 'bi-box-seam', 'label' => 'Almacén', 'active' => ($archivoActual == 'almacenes.php' || $archivoActual == 'almacen.php')],
   ['id' => 'movimientos', 'url' => '/cfsistem/app/controllers/movimientosController.php', 'icon' => 'bi-arrow-left-right', 'label' => 'Movimientos', 'active' => ($archivoActual == 'movimientosController.php')],
   ['id' => 'Mermas', 'url' => '/cfsistem/app/controllers/mermasController.php', 'icon' => 'bi-exclamation-triangle', 'label' => 'Mermas', 'active' => ($archivoActual == 'mermasController.php')],
   ['id' => 'transmutaciones', 'url' => '/cfsistem/app/controllers/transmutacionesController.php', 'icon' => 'bi-arrow-repeat', 'label' => 'Conversiones', 'active' => ($archivoActual == 'transmutacionesController.php')],
   ['id' => 'historialLotes', 'url' => '/cfsistem/app/controllers/lotesHistorialController.php', 'icon' => 'bi-clock-history', 'label' => 'Historial de Lotes', 'active' => ($archivoActual == 'lotesHistorialController.php')],
['id' => 'comprasHistorial', 'url' => '/cfsistem/app/controllers/comprasHistorialController.php', 'icon' => 'bi-collection', 'label' => 'Historial de Compras', 'active' => ($archivoActual == 'comprasHistorialController.php')],


   // =========================
   // FINANZAS Y TESORERÍA
   // =========================
   ['id' => 'finanzas', 'url' => '/cfsistem/app/controllers/finanzasController.php', 'icon' => 'bi-graph-up-arrow', 'label' => 'Finanzas', 'active' => ($archivoActual == 'finanzasController.php')],
   ['id' => 'finanzas_admin', 'url' => '/cfsistem/app/controllers/finanzasAdmController.php', 'icon' => 'bi-bar-chart-line', 'label' => 'Finanzas Admin', 'active' => ($archivoActual == 'finanzasAdmController.php')],
   ['id' => 'corteCaja', 'url' => '/cfsistem/app/controllers/corteCajaController.php', 'icon' => 'bi-calculator', 'label' => 'Corte de Caja', 'active' => ($archivoActual == 'corteCajaController.php')],
   ['id' => 'tesoreria', 'url' => '/cfsistem/app/controllers/tesoreriaController.php', 'icon' => 'bi-safe', 'label' => 'Tesorería', 'active' => ($archivoActual == 'tesoreriaController.php')],

   // =========================
   // LOGÍSTICA Y DISTRIBUCIÓN
   // =========================
   ['id' => 'entregas', 'url' => '/cfsistem/app/controllers/entregasController.php', 'icon' => 'bi-truck', 'label' => 'Despachos', 'active' => ($archivoActual == 'entregasController.php')],
   ['id' => 'vehiculos', 'url' => '/cfsistem/app/controllers/vehiculosController.php', 'icon' => 'bi-truck-front-fill', 'label' => 'Vehículos', 'active' => ($archivoActual == 'vehiculosController.php')],
   ['id' => 'repartos', 'url' => '/cfsistem/app/controllers/repartosController.php', 'icon' => 'bi-truck-flatbed', 'label' => 'Repartos', 'active' => ($archivoActual == 'repartosController.php')],
['id' => 'misRepartos', 'url' => '/cfsistem/app/controllers/misRepartosController.php', 'icon' => 'bi-map-fill', 'label' => 'Mis repartos', 'active' => ($archivoActual == 'misRepartosController.php')],
['id' => 'viajesTrabajadores', 'url' => '/cfsistem/app/controllers/viajesTrabajadoresController.php', 'icon' => 'bi-person-workspace', 'label' => 'Viajes Trabajadores', 'active' => ($archivoActual == 'viajesTrabajadoresController.php')],
 
   // =========================
   // RECURSOS HUMANOS
   // =========================
   ['id' => 'trabajadores', 'url' => '/cfsistem/app/controllers/trabajadoresController.php', 'icon' => 'bi-people-fill', 'label' => 'Trabajadores', 'active' => ($archivoActual == 'trabajadoresController.php')],

   // =========================
   // ADMINISTRACIÓN
   // =========================
   ['id' => 'usuarios', 'url' => '/cfsistem/app/controllers/usuariosController.php', 'icon' => 'bi-people', 'label' => 'Usuarios', 'active' => ($archivoActual == 'usuariosController.php')],
//['id' => 'Configuracion', 'url' => '/cfsistem/app/controllers/configuracionController.php', 'icon' => 'bi-gear-fill', 'label' => 'Configuración', 'active' => ($archivoActual == 'configuracionController.php')],

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
            <div class="dropdown">
                <a href="javascript:void(0);" class="text-white position-relative p-2" id="btnNotif"
                    data-bs-toggle="dropdown">
                    <i class="bi bi-bell fs-4"></i>
                    <span id="notif-badge"
                        class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger d-none">0</span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 p-0" id="menuNotif"
                    style="width: 320px; max-width: 90vw; max-height: 400px; overflow-y: auto;">
                    <li class="p-3 border-bottom bg-light">
                        <h6 class="mb-0 fw-bold text-dark">Traspasos Pendientes</h6>
                    </li>
                    <div id="lista-notificaciones">
                        <li class="p-3 text-center text-muted small">Cargando...</li>
                    </div>
                    <li>
                        <hr class="dropdown-divider m-0">
                    </li>
                    <li></li>
                </ul>
            </div>

            <div class="user-badge d-flex align-items-center text-white bg-white bg-opacity-10 px-3 py-1 rounded-pill">
                <i class="bi bi-person-circle fs-5"></i>
                <span class="ms-2 d-none d-md-inline small"><?= $_SESSION['nombre'] ?? 'Usuario' ?></span>
            </div>

            <a href="/cfsistem/logout.php" class="btn btn-sm btn-outline-light border-0 rounded-circle"
                title="Cerrar Sesión">
                <i class="bi bi-box-arrow-right fs-4"></i>
            </a>
        </div>
    </div>
</nav>

<aside id="sidebar" class="bg-white border-end shadow-sm">
    <div class="p-3">
        <div class="text-center mb-4">
            <h5 class="fw-bold text-primary mb-1">Menú</h5>
            <?php if (!empty($_SESSION['rol'])): ?>
            <span class="badge bg-light text-secondary border">Rol: <?= ucfirst($_SESSION['rol']) ?></span>
            <?php endif; ?>
        </div>

        <ul class="nav nav-pills flex-column gap-1">
            <?php foreach ($modulos as $m): ?>
            <?php if (puedeVerModulo($m['id'])): ?>
            <li class="nav-item">
                <a href="<?= $m['url'] ?>"
                    class="nav-link d-flex align-items-center gap-3 <?= $m['active'] ? 'active shadow-sm' : 'text-dark' ?>">
                    <i class="<?= $m['icon'] ?> fs-5"></i>
                    <span><?= $m['label'] ?></span>
                </a>
            </li>
            <?php endif; ?>
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

document.addEventListener('DOMContentLoaded', () => {

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

    // --- 2. LÓGICA DEL SIDEBAR (RESPONSIVO) ---
    function toggleMenu() {
        const isMobile = window.innerWidth <= 992;

        if (isMobile) {
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

    document.addEventListener('click', (e) => {
        if (menuNotif && !menuNotif.contains(e.target) && !btnNotif.contains(e.target)) {
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

    window.addEventListener('resize', () => {
        if (window.innerWidth > 992) {
            if (sidebar) sidebar.classList.remove('show');
            if (overlay) overlay.classList.remove('active');
        }
    });
});
</script>