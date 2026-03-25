<nav class="navbar fixed-top navbar-expand-lg navbar-dark navbar-premium">
    <div class="container-fluid px-4">
        <div class="d-flex align-items-center gap-3">

            <button class="btn btn-toggle" id="toggleSidebar"><i class="bi bi-list fs-3 text-white"></i></button>
            <span class="fw-semibold text-white"><?= $tituloPagina ?></span>
        </div>
        <div class="d-flex align-items-center gap-3">
            <div class="dropdown me-2">
                <a href="javascript:void(0);" class="text-white position-relative" id="btnNotif">
                    <i class="bi bi-bell fs-4"></i>
                    <span id="notif-badge"
                        class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger d-none">0</span>
                </a>
                <ul class="shadow-lg border-0 p-0" id="menuNotif"
                    style="width: 320px; max-height: 400px; overflow-y: auto; display: none; position: absolute; right: 0; background: white; z-index: 1060; list-style: none; border-radius: 8px;">
                    <li class="p-3 border-bottom bg-light">
                        <h6 class="mb-0 fw-bold text-dark">Traspasos Pendientes</h6>
                    </li>
                    <div id="lista-notificaciones">
                        <li class="p-3 text-center text-muted small">Cargando...</li>
                    </div>
                    <li>
                        <hr class="dropdown-divider m-0">
                    </li>
                    <li><a class="dropdown-item text-center py-2 small text-primary fw-bold"
                            href="/cfsistem/app/views/almacenes.php">Ver todos</a></li>
                </ul>
            </div>
            <div class="user-badge">
                <i class="bi bi-person-circle me-2"></i>
                <span><?= $_SESSION['nombre'] ?? 'Usuario' ?></span>
            </div>
            <a href="/cfsistem/logout.php" class="btn btn-logout"><i class="bi bi-box-arrow-right text-white"></i></a>
        </div>
    </div>
</nav>

<aside id="sidebar">
    <div class="p-3">
        <h5 class="text-center mb-4">Menú</h5>
        <?php if (!empty($_SESSION['rol'])): ?>
        <div class="text-center small text-secondary mb-3">Rol: <?= ucfirst($_SESSION['rol']) ?></div>
        <?php endif; ?>

        <ul class="nav nav-pills flex-column gap-1">
            <?php if (puedeVerModulo('inicio')): ?>
            <li class="nav-item">
                <a href="/cfsistem/app/views/inicio.php"
                    class="nav-link <?= $archivoActual == 'inicio.php' ? 'active' : '' ?>">
                    <i class="bi bi-house-door"></i><span>Inicio</span>
                </a>
            </li>
            <?php endif; ?>

            <?php if (puedeVerModulo('ventas')): ?>
            <li class="nav-item">
                <a href="/cfsistem/app/controllers/ventasController.php"
                    class="nav-link <?= $archivoActual == 'ventasController.php' ? 'active' : '' ?>">
                    <i class="bi bi-cart-check"></i><span>Ventas</span>
                </a>
            </li>
            <?php endif; ?>

            <?php if (puedeVerModulo('almacenes')): ?>
            <li class="nav-item">
                <a href="/cfsistem/app/controllers/almacenes.php"
                    class="nav-link <?= ($archivoActual == 'almacenes.php' || $archivoActual == 'almacen.php') ? 'active' : '' ?>">
                    <i class="bi bi-box-seam"></i><span>Almacén</span>
                </a>
            </li>
            <?php endif; ?>

            <?php if (puedeVerModulo('movimientos')): ?>
            <li class="nav-item">
                <a href="/cfsistem/app/controllers/movimientosController.php"
                    class="nav-link <?= $archivoActual == 'movimientosController.php' ? 'active' : '' ?>">
                    <i class="bi bi-arrow-left-right"></i><span>Movimientos</span>
                </a>
            </li>
            <?php endif; ?>

            <?php if (puedeVerModulo('ventashistorial')): ?>
            <li class="nav-item">
                <a href="/cfsistem/app/controllers/ventasHistorialController.php"
                    class="nav-link <?= $archivoActual == 'ventasHistorialController.php' ? 'active' : '' ?>">
                    <i class="bi bi-receipt"></i><span>Historial</span>
                </a>
            </li>
            <?php endif; ?>

            <?php if (puedeVerModulo('caja')): ?>
            <li class="nav-item">
                <a href="/cfsistem/pantallas/caja.php"
                    class="nav-link <?= $archivoActual == 'cajaController.php' ? 'active' : '' ?>">
                    <i class="bi bi-cash-stack"></i><span>Caja</span>
                </a>
            </li>
            <?php endif; ?>

            <?php if (puedeVerModulo('usuarios')): ?>
            <li class="nav-item">
                <a href="/cfsistem/app/controllers/usuariosController.php"
                    class="nav-link <?= $archivoActual == 'usuariosController.php' ? 'active' : '' ?>">
                    <i class="bi bi-people"></i><span>Usuarios</span>
                </a>
            </li>
            <?php endif; ?>

            <?php if (puedeVerModulo('compras')): ?>
            <li class="nav-item">
                <a href="/cfsistem/app/controllers/egresosController.php"
                    class="nav-link <?= ($archivoActual == 'egresosController.php' || $archivoActual == 'gastos.php') ? 'active' : '' ?>">
                    <i class="bi bi-bag-check"></i><span>Compras</span>
                </a>
            </li>
            <?php endif; ?>
            <?php if (puedeVerModulo('proveedores')): ?>
            <li class="nav-item">
                <a href="/cfsistem/app/controllers/proveedoresController.php"
                    class="nav-link <?= ($archivoActual == 'proveedoresController.php') ? 'active' : '' ?>">
                   <i class="bi bi-person-badge"></i><span>Proveedores</span>
                </a>
            </li>
            <?php endif; ?>

            <?php if (puedeVerModulo('clientes')): ?>
            <li class="nav-item">
                <a href="/cfsistem/app/controllers/clientesController.php"
                    class="nav-link <?= $archivoActual == 'clientesController.php' ? 'active' : '' ?>">
                    <i class="bi bi-person-lines-fill"></i><span>Clientes</span>
                </a>
            </li>
            <?php endif; ?>

            <?php if (puedeVerModulo('Mermas')): ?>
            <li class="nav-item">
                <a href="/cfsistem/app/controllers/mermasController.php"
                    class="nav-link <?= $archivoActual == 'mermasController.php' ? 'active' : '' ?>">
                    <i class="bi bi-exclamation-triangle"></i><span>Mermas</span>
                </a>
            </li>
            <?php endif; ?>
            <?php if (puedeVerModulo('transmutaciones')): ?>
            <li class="nav-item">
                <a href="/cfsistem/app/controllers/transmutacionesController.php"
                    class="nav-link <?= $archivoActual == 'transmutacionesController.php' ? 'active' : '' ?>">
                    <i class="bi bi-arrow-repeat"></i><span>Conversiones</span>
                </a>
            </li>
            <?php endif; ?>

            <?php if (puedeVerModulo('finanzas')): ?>
            <li class="nav-item">
                <a href="/cfsistem/app/controllers/finanzasController.php"
                    class="nav-link <?= $archivoActual == 'finanzasController.php' ? 'active' : '' ?>">
                    <i class="bi bi-graph-up-arrow"></i><span>Finanzas</span>
                </a>
            </li>
            <?php endif; ?>
            <?php if (puedeVerModulo('corteCaja')): ?>
            <li class="nav-item">
                <a href="/cfsistem/app/controllers/corteCajaController.php"
                    class="nav-link <?= $archivoActual == 'corteCajaController.php' ? 'active' : '' ?>">
                    <i class="bi bi-calculator"></i><span>Corte de Caja</span>
                </a>
            </li>
            <?php endif; ?>
            <?php if (puedeVerModulo('entregas')): ?>
            <li class="nav-item">
                <a href="/cfsistem/app/controllers/entregasController.php"
                    class="nav-link <?= $archivoActual == 'entregasController.php' ? 'active' : '' ?>">
                    <i class="bi bi-truck"></i><span>Despachos</span>
                </a>
            </li>
            <?php endif; ?>
            <?php if (puedeVerModulo(modulo: 'clientesEstatus')): ?>
            <li class="nav-item">
                <a href="/cfsistem/app/controllers/clientesEstatusController.php"
                    class="nav-link <?= $paginaActual == 'clientesEstatus' ? 'active' : '' ?>">
                    <i class="bi bi-person-badge"></i><span>Estatus Clientes</span>
                </a>
            </li>
            <?php endif; ?>
               <?php if (puedeVerModulo(modulo: 'solicitudesCompra')): ?>
<li class="nav-item">
    <a href="/cfsistem/app/controllers/solicitudesCompraController.php" 
       class="nav-link <?= $paginaActual == 'solicitudesCompraontroller' ? 'active' : '' ?>">
       <i class="bi bi-cart-check-fill"></i><span>solicitudesCompra</span>
    </a>
</li>

<?php endif; ?>
 <?php if (puedeVerModulo(modulo: 'trabajadores')): ?>
<li class="nav-item">
    <a href="/cfsistem/app/controllers/trabajadoresController.php" 
       class="nav-link <?= $paginaActual == 'trabjadoresController' ? 'active' : '' ?>">
       <i class="bi bi-people-fill"></i><span>Trabajadores</span>
    </a>
</li>
<?php endif; ?>
   <?php if (puedeVerModulo(modulo: 'vehiculos')): ?>
<li class="nav-item">
    <a href="/cfsistem/app/controllers/vehiculosController.php" 
       class="nav-link <?= $paginaActual == 'vehiculosController' ? 'active' : '' ?>">
        <i class="bi bi-truck-front-fill"></i><span>Vehiculos</span>
    </a>
</li>
<?php endif; ?>
             <?php if (puedeVerModulo(modulo: 'repartos')): ?>
<li class="nav-item">
    <a href="/cfsistem/app/controllers/repartosController.php" 
       class="nav-link <?= $paginaActual == 'repartosController' ? 'active' : '' ?>">
       <i class="bi bi-truck-flatbed"></i><span>Repartos</span>
    </a>
</li>
<?php endif; ?>
             <?php if (puedeVerModulo(modulo: 'pedidosVendedor')): ?>
<li class="nav-item">
    <a href="/cfsistem/app/controllers/pedidosVendedorController.php" 
       class="nav-link <?= $paginaActual == 'pedidosVendedorController' ? 'active' : '' ?>">
        <i class="bi bi-gear-fill"></i><span>Pedidos Vendedor</span>
    </a>
</li>
<?php endif; ?>
             <?php if (puedeVerModulo(modulo: 'Configuracion')): ?>
<li class="nav-item">
    <a href="/cfsistem/app/controllers/configuracionController.php" 
       class="nav-link <?= $paginaActual == 'configuracionController' ? 'active' : '' ?>">
        <i class="bi bi-gear-fill"></i><span>Configuración</span>
    </a>
</li>
<?php endif; ?>
        
            <!-- 
<li class="nav-item">
    <a href="/cfsistem/app/views/gestionar_permisos.php" class="nav-link <?= $archivoActual == 'cofiguracionController.php' ? 'active' : '' ?>">
        <i class="bi bi-key"></i><span>Gestión de Permisos</span>
    </a>
</li> -->

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