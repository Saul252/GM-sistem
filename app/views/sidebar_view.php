<?php
/**
 * CONFIGURACIÓN DE MÓDULOS
 * Optimizamos la renderización mediante un bucle para evitar repetir HTML.
 */
$modulos = [
    ['id' => 'inicio', 'url' => '/cfsistem/app/views/inicio.php', 'icon' => 'bi-house-door', 'label' => 'Inicio', 'active' => ($archivoActual == 'inicio.php')],
    ['id' => 'ventas', 'url' => '/cfsistem/app/controllers/ventasController.php', 'icon' => 'bi-cart-check', 'label' => 'Ventas', 'active' => ($archivoActual == 'ventasController.php')],
    ['id' => 'cajaRapida', 'url' => '/cfsistem/app/controllers/cajaRapidaController.php', 'icon' => 'bi-cart-check', 'label' => 'Caja Rapida', 'active' => ($archivoActual == 'ventasController.php')],
   
    ['id' => 'almacenes', 'url' => '/cfsistem/app/controllers/almacenes.php', 'icon' => 'bi-box-seam', 'label' => 'Almacén', 'active' => ($archivoActual == 'almacenes.php' || $archivoActual == 'almacen.php')],
    ['id' => 'movimientos', 'url' => '/cfsistem/app/controllers/movimientosController.php', 'icon' => 'bi-arrow-left-right', 'label' => 'Movimientos', 'active' => ($archivoActual == 'movimientosController.php')],
    ['id' => 'ventashistorial', 'url' => '/cfsistem/app/controllers/ventasHistorialController.php', 'icon' => 'bi-receipt', 'label' => 'Historial', 'active' => ($archivoActual == 'ventasHistorialController.php')],
    ['id' => 'usuarios', 'url' => '/cfsistem/app/controllers/usuariosController.php', 'icon' => 'bi-people', 'label' => 'Usuarios', 'active' => ($archivoActual == 'usuariosController.php')],
    ['id' => 'compras', 'url' => '/cfsistem/app/controllers/egresosController.php', 'icon' => 'bi-bag-check', 'label' => 'Compras', 'active' => ($archivoActual == 'egresosController.php' || $archivoActual == 'gastos.php')],
    ['id' => 'proveedores', 'url' => '/cfsistem/app/controllers/proveedoresController.php', 'icon' => 'bi-person-badge', 'label' => 'Proveedores', 'active' => ($archivoActual == 'proveedoresController.php')],
    ['id' => 'clientes', 'url' => '/cfsistem/app/controllers/clientesController.php', 'icon' => 'bi-person-lines-fill', 'label' => 'Clientes', 'active' => ($archivoActual == 'clientesController.php')],
    ['id' => 'Mermas', 'url' => '/cfsistem/app/controllers/mermasController.php', 'icon' => 'bi-exclamation-triangle', 'label' => 'Mermas', 'active' => ($archivoActual == 'mermasController.php')],
    ['id' => 'transmutaciones', 'url' => '/cfsistem/app/controllers/transmutacionesController.php', 'icon' => 'bi-arrow-repeat', 'label' => 'Conversiones', 'active' => ($archivoActual == 'transmutacionesController.php')],
    ['id' => 'finanzas', 'url' => '/cfsistem/app/controllers/finanzasController.php', 'icon' => 'bi-graph-up-arrow', 'label' => 'Finanzas', 'active' => ($archivoActual == 'finanzasController.php')],
    ['id' => 'corteCaja', 'url' => '/cfsistem/app/controllers/corteCajaController.php', 'icon' => 'bi-calculator', 'label' => 'Corte de Caja', 'active' => ($archivoActual == 'corteCajaController.php')],
    ['id' => 'entregas', 'url' => '/cfsistem/app/controllers/entregasController.php', 'icon' => 'bi-truck', 'label' => 'Despachos', 'active' => ($archivoActual == 'entregasController.php')],
    ['id' => 'clientesEstatus', 'url' => '/cfsistem/app/controllers/clientesEstatusController.php', 'icon' => 'bi-person-badge', 'label' => 'Estatus Clientes', 'active' => ($paginaActual == 'clientesEstatus')],
    ['id' => 'solicitudesCompra', 'url' => '/cfsistem/app/controllers/solicitudesCompraController.php', 'icon' => 'bi-cart-check-fill', 'label' => 'Solicitudes Compra', 'active' => ($paginaActual == 'solicitudesCompraontroller')],
    ['id' => 'trabajadores', 'url' => '/cfsistem/app/controllers/trabajadoresController.php', 'icon' => 'bi-people-fill', 'label' => 'Trabajadores', 'active' => ($paginaActual == 'trabjadoresController')],
    ['id' => 'vehiculos', 'url' => '/cfsistem/app/controllers/vehiculosController.php', 'icon' => 'bi-truck-front-fill', 'label' => 'Vehículos', 'active' => ($paginaActual == 'vehiculosController')],
    ['id' => 'repartos', 'url' => '/cfsistem/app/controllers/repartosController.php', 'icon' => 'bi-truck-flatbed', 'label' => 'Repartos', 'active' => ($paginaActual == 'repartosController')],
    ['id' => 'pedidosVendedor', 'url' => '/cfsistem/app/controllers/pedidosVendedorController.php', 'icon' => 'bi-person-badge-fill', 'label' => 'Pedidos Vendedor', 'desc' => 'Preventa', 'active' => ($paginaActual == 'pedidosVendedorController')],
    ['id' => 'Configuracion', 'url' => '/cfsistem/app/controllers/configuracionController.php', 'icon' => 'bi-gear-fill', 'label' => 'Configuración', 'active' => ($paginaActual == 'configuracionController')],
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
                <a href="javascript:void(0);" class="text-white position-relative p-2" id="btnNotif" data-bs-toggle="dropdown">
                    <i class="bi bi-bell fs-4"></i>
                    <span id="notif-badge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger d-none">0</span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 p-0" id="menuNotif" style="width: 320px; max-width: 90vw; max-height: 400px; overflow-y: auto;">
                    <li class="p-3 border-bottom bg-light">
                        <h6 class="mb-0 fw-bold text-dark">Traspasos Pendientes</h6>
                    </li>
                    <div id="lista-notificaciones">
                        <li class="p-3 text-center text-muted small">Cargando...</li>
                    </div>
                    <li><hr class="dropdown-divider m-0"></li>
                    <li><a class="dropdown-item text-center py-2 small text-primary fw-bold" href="/cfsistem/app/views/almacenes.php">Ver todos</a></li>
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
                    <a href="<?= $m['url'] ?>" class="nav-link d-flex align-items-center gap-3 <?= $m['active'] ? 'active shadow-sm' : 'text-dark' ?>">
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
// --- 1. LÓGICA DEL SIDEBAR ---
document.addEventListener('click', function(e) {
    if (e.target.closest('#toggleSidebar')) {
        const sidebar = document.getElementById('sidebar');
        if (sidebar) {
            sidebar.classList.toggle('hidden');
            document.body.classList.toggle('sidebar-hidden');
        }
    }
});
</script>
<script>
/**
 * SISTEMA DE NOTIFICACIONES DE TRASPASOS - VERSIÓN FINAL
 * - Manejo de unidades inteligentes (Millares + Piezas)
 * - Forzado de visibilidad para evitar errores de "no pintado"
 * - Actualización en tiempo real cada 30 segundos
 */

let ultimoConteoTraspasos = 0;
let primeraCarga = true;

function verificarNotificaciones() {
    // Agregamos timestamp para evitar caché del navegador
    const url = '/cfsistem/app/backend/movimientos/get_notificaciones_traspaso.php?t=' + Date.now();

    fetch(url)
        .then(response => response.json())
        .then(data => {
            const badge = document.getElementById('notif-badge');
            const lista = document.getElementById('lista-notificaciones');
            const cantidadActual = parseInt(data.cantidad) || 0;

            // 1. ACTUALIZAR BADGE DE LA CAMPANA
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

            // 2. ALERTA FLOTANTE (TOASTIFY)
            if (cantidadActual > 0 && (primeraCarga || cantidadActual > ultimoConteoTraspasos)) {
                if (typeof Toastify === "function") {
                    const u = data.items[0] || {};
                    const textoCant = u.cantidad_texto || (u.cantidad + ' PZA');

                    Toastify({
                        text: `📦 ¡SOLICITUD DE TRASPASO RECIBIDA!\n${u.emisor} envió ${textoCant} de ${u.producto}`,
                        duration: 6000,
                        close: true,
                        gravity: "top",
                        position: "right",
                        style: {
                            background: "linear-gradient(to right, #1e3c72, #2a5298)",
                            borderRadius: "12px",
                            boxShadow: "0 5px 15px rgba(0,0,0,0.3)"
                        },
                        onClick: function() {
                            window.location.href = "/cfsistem/app/controllers/almacenes.php";
                        }
                    }).showToast();
                }
                primeraCarga = false;
            }

            ultimoConteoTraspasos = cantidadActual;

            // 3. RENDERIZADO DEL MENÚ DESPLEGABLE (TARJETAS)
            if (lista && data.items) {
                if (cantidadActual === 0) {
                    lista.innerHTML =
                        '<div style="padding: 20px; text-align: center; color: #999; font-size: 0.8rem;">Sin traspasos pendientes</div>';
                } else {
                    lista.innerHTML = data.items.map(item => {
                        // Aseguramos que mostrarCantidad tenga el valor procesado del PHP
                        const mostrarCantidad = item.cantidad_texto ? item.cantidad_texto : (item.cantidad +
                            ' PZA');

                        return `
                        <div style="padding: 12px 15px; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center; background: #ffffff !important; color: #333 !important; min-height: 80px;">
                            <div style="flex: 1; padding-right: 10px; line-height: 1.3;">
                                <b style="color: #1e3c72; font-size: 0.85rem; display: block; text-transform: uppercase; margin-bottom: 2px;">${item.producto}</b>
                                <div style="font-size: 0.75rem; color: #555;">De: <strong>${item.emisor}</strong></div>
                                
                                <div style="margin-top: 6px; background: #f8f9fa; padding: 3px 8px; border-radius: 5px; display: inline-block; border: 1px solid #ddd;">
                                    <span style="color: #000000 !important; font-weight: 900 !important; font-size: 0.9rem !important; display: inline-block !important; visibility: visible !important;">
                                        CANT: ${mostrarCantidad}
                                    </span>
                                </div>

                                <div style="font-size: 0.65rem; color: #999; margin-top: 6px;">
                                    <i class="bi bi-clock"></i> ${item.hora} • ${item.origen}
                                </div>
                            </div>
                            <button onclick="procesarRecepcion(${item.id})" 
                                    title="Confirmar Recepción"
                                    style="background: #198754; color: white; border: none; border-radius: 50%; width: 35px; height: 35px; cursor: pointer; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 5px rgba(0,0,0,0.2); flex-shrink: 0;">
                                <i class="bi bi-check-lg" style="font-size: 1.2rem;"></i>
                            </button>
                        </div>`;
                    }).join('');
                }
            }
        })
        .catch(err => console.error("❌ Error en Notificaciones:", err));
}

/**
 * Función para procesar la recepción rápida mediante AJAX
 */
function procesarRecepcion(id) {
    if (!confirm("¿Deseas confirmar la recepción de este producto? El stock se actualizará de inmediato.")) return;

    const formData = new FormData();
    formData.append('id', id);

    fetch('/cfsistem/app/backend/movimientos/procesar_transaccion_rapida.php', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success || data.status === 'success') {
                // Recarga la página para actualizar inventarios visibles
                location.reload();
            } else {
                alert("Error: " + (data.message || "No se pudo procesar la recepción"));
            }
        })
        .catch(err => {
            console.error("Error en fetch:", err);
            alert("Error de conexión al servidor.");
        });
}

/**
 * CONTROL MANUAL DEL MENÚ DESPLEGABLE (Dropdown)
 */
document.addEventListener('click', function(e) {
    const btn = document.getElementById('btnNotif');
    const menu = document.getElementById('menuNotif');
    if (!btn || !menu) return;

    if (btn.contains(e.target)) {
        const isVisible = (menu.style.display === 'block');
        menu.style.display = isVisible ? 'none' : 'block';
        e.preventDefault();
        e.stopPropagation();
    } else if (!menu.contains(e.target)) {
        menu.style.display = 'none';
    }
});

/**
 * INICIALIZACIÓN AL CARGAR LA PÁGINA
 */
document.addEventListener('DOMContentLoaded', () => {
    // Ejecución inmediata al cargar
    verificarNotificaciones();
    // Ciclo de autorefresco cada 30 segundos
    setInterval(verificarNotificaciones, 30000);
});
</script>