<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['usuario_id'])) {
    header("Location: /cfsistem/index.php");
    exit;
}

require_once __DIR__ . '/permisos.php';

function renderSidebar(string $paginaActual = '')
{
    $archivoActual = basename($_SERVER['PHP_SELF']);
?>
<script>
document.addEventListener('DOMContentLoaded', () => {

    const toggleBtn = document.getElementById('toggleSidebar');
    const sidebar = document.getElementById('sidebar');

    if (!toggleBtn || !sidebar) return;

    toggleBtn.addEventListener('click', () => {
        sidebar.classList.toggle('hidden');
        document.body.classList.toggle('sidebar-hidden');
    });

});
</script>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

<style>

/* ===== NAVBAR PREMIUM ===== */
.navbar-premium{
    background: #111827;
    height: 65px;
    border-bottom: 1px solid rgba(255,255,255,.05);
}

.btn-toggle{
    background: transparent;
    border: none;
}

.user-badge{
    display: flex;
    align-items: center;
    background: rgba(255,255,255,.08);
    padding: 6px 14px;
    border-radius: 50px;
    color: #ffffff;
    font-weight: 500;
    font-size: 14px;
}

.btn-logout{
    background: transparent;
    border: none;
    color: #ffffff;
    font-size: 20px;
    transition: .2s ease;
}

.btn-logout:hover{
    color: #dc3545;
    transform: scale(1.1);
}

/* ===== SIDEBAR ===== */
#sidebar{
    width: 260px;
    height: 100vh;
    position: fixed;
    top: 65px;
    left: 0;
    background: #f9fafb;
    border-right: 1px solid #e5e7eb;
    box-shadow: 0 10px 30px rgba(0,0,0,.05);
    transition: .3s ease;
}

#sidebar.hidden{
    transform: translateX(-100%);
}

body.sidebar-hidden #sidebar{
    transform: translateX(-100%);
}

#sidebar h5{
    font-weight: 600;
    color: #111827;
}

/* Links */
#sidebar .nav-link{
    display: flex;
    align-items: center;
    gap: 10px;
    color: #4b5563 !important;
    border-radius: 12px;
    padding: 11px 16px;
    font-weight: 500;
    transition: all .2s ease;
}

#sidebar .nav-link i{
    font-size: 18px;
    opacity: .8;
    transition: .2s ease;
}

/* Hover */
#sidebar .nav-link:hover{
    background: #f3f4f6;
    color: #111827 !important;
}

#sidebar .nav-link:hover i{
    transform: scale(1.1);
    opacity: 1;
}

/* Activo */
#sidebar .nav-link.active{
    background: linear-gradient(90deg,#e0edff,#f0f7ff);
    color: #0d6efd !important;
    font-weight: 600;
}

#sidebar .nav-link.active i{
    color: #0d6efd;
    opacity: 1;
}

</style>

<nav class="navbar fixed-top navbar-expand-lg navbar-dark navbar-premium">
    <div class="container-fluid px-4">

        <div class="d-flex align-items-center gap-3">
            <button class="btn btn-toggle" id="toggleSidebar">
                <i class="bi bi-list fs-3 text-white"></i>
            </button>

            <span class="fw-semibold text-white">
                <?= $paginaActual ?>
            </span>
        </div>

        <div class="d-flex align-items-center gap-3">

            <div class="user-badge">
                <i class="bi bi-person-circle me-2"></i>
                <span><?= $_SESSION['nombre'] ?? 'Usuario' ?></span>
            </div>

            <a href="/cfsistem/logout.php" class="btn btn-logout">
                <i class="bi bi-box-arrow-right"></i>
            </a>

        </div>

    </div>
</nav>

<aside id="sidebar">
    <div class="p-3">
        <h5 class="text-center mb-4">Menú</h5>

        <?php if (!empty($_SESSION['rol'])): ?>
            <div class="text-center small text-secondary mb-3">
                Rol: <?= ucfirst($_SESSION['rol']) ?>
            </div>
        <?php endif; ?>

        <ul class="nav nav-pills flex-column gap-1">

            <?php if (puedeVerModulo('inicio')): ?>
            <li class="nav-item">
                <a href="/cfsistem/app/views/inicio.php"
                   class="nav-link <?= $archivoActual == 'inicio.php' ? 'active' : '' ?>">
                    <i class="bi bi-house-door"></i>
                    <span>Inicio</span>
                </a>
            </li>
            <?php endif; ?>

            <?php if (puedeVerModulo('ventas')): ?>
            <li class="nav-item">
                <a href="/cfsistem/app/views/ventas.php"
                   class="nav-link <?= $archivoActual == 'ventas.php' ? 'active' : '' ?>">
                    <i class="bi bi-cart-check"></i>
                    <span>Ventas</span>
                </a>
            </li>
            <?php endif; ?>

            <?php if (puedeVerModulo('almacenes')): ?>
            <li class="nav-item">
                <a href="/cfsistem/app/views/almacenes.php"
                   class="nav-link <?= $archivoActual == 'almacen.php' ? 'active' : '' ?>">
                    <i class="bi bi-box-seam"></i>
                    <span>Almacén</span>
                </a>
            </li>
            <?php endif; ?>

         <?php if (puedeVerModulo('movimientos')): ?>
            <li class="nav-item">
                <a href="/cfsistem/app/views/movimientos.php"
                   class="nav-link <?= $archivoActual == 'movimientos.php' ? 'active' : '' ?>">
                   <i class="bi bi-arrow-left-right icono-modulo icon-movimientos"></i>
                    <span>Movimientos</span>
                </a>
            </li>
            <?php endif; ?>
             <?php if (puedeVerModulo('ventashistorial')): ?>
            <li class="nav-item">
                <a href="/cfsistem/app/views/ventashistorial.php"
                   class="nav-link <?= $archivoActual == 'ventashistorial.php' ? 'active' : '' ?>">
                   <i class="bi bi-arrow-left-right icono-modulo icon-movimientos"></i>
                    <span>Historial de ventas</span>
                </a>
            </li>
            <?php endif; ?>

            <?php if (puedeVerModulo('caja')): ?>
            <li class="nav-item">
                <a href="/cfsistem/pantallas/caja.php"
                   class="nav-link <?= $archivoActual == 'caja.php' ? 'active' : '' ?>">
                    <i class="bi bi-cash-stack"></i>
                    <span>Caja</span>
                </a>
            </li>
            <?php endif; ?>

            <?php if (puedeVerModulo('usuarios')): ?>
            <li class="nav-item">
                <a href="/cfsistem/pantallas/usuarios.php"
                   class="nav-link <?= $archivoActual == 'usuarios.php' ? 'active' : '' ?>">
                    <i class="bi bi-people"></i>
                    <span>Usuarios</span>
                </a>
            </li>
            <?php endif; ?>

            <?php if (puedeVerModulo('compras')): ?>
            <li class="nav-item">
                <a href="/cfsistem/pantallas/gastos.php"
                   class="nav-link <?= $archivoActual == 'gastos.php' ? 'active' : '' ?>">
                    <i class="bi bi-bag-check"></i>
                    <span>Compras</span>
                </a>
            </li>
            <?php endif; ?>

            <?php if (puedeVerModulo('clientes')): ?>
            <li class="nav-item">
                <a href="/cfsistem/pantallas/clientes.php"
                   class="nav-link <?= $archivoActual == 'clientes.php' ? 'active' : '' ?>">
                    <i class="bi bi-person-lines-fill"></i>
                    <span>Clientes</span>
                </a>
            </li>
            <?php endif; ?>

            <?php if (puedeVerModulo('finanzas')): ?>
            <li class="nav-item">
                <a href="/cfsistem/pantallas/finanzas.php"
                   class="nav-link <?= $archivoActual == 'finanzas.php' ? 'active' : '' ?>">
                    <i class="bi bi-graph-up-arrow"></i>
                    <span>Finanzas</span>
                </a>
            </li>
            <?php endif; ?>

            <?php if (puedeVerModulo('mermas')): ?>
            <li class="nav-item">
                <a href="/cfsistem/pantallas/mermas.php"
                   class="nav-link <?= $archivoActual == 'mermas.php' ? 'active' : '' ?>">
                    <i class="bi bi-exclamation-triangle"></i>
                    <span>Mermas</span>
                </a>
            </li>
            <?php endif; ?>

        </ul>
    </div>
</aside>

<?php
}
?>