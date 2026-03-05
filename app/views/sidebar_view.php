

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
                <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 p-0" id="menuNotif"
                    style="width: 320px; max-height: 400px; overflow-y: auto; display: none; position: absolute; right: 0;">
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
                <a href="/cfsistem/app/views/inicio.php" class="nav-link <?= $archivoActual == 'inicio.php' ? 'active' : '' ?>">
                    <i class="bi bi-house-door"></i><span>Inicio</span>
                </a>
            </li>
            <?php endif; ?>

            <?php if (puedeVerModulo('ventas')): ?>
            <li class="nav-item">
                <a href="/cfsistem/app/controllers/ventas.php" class="nav-link <?= $archivoActual == 'ventas.php' ? 'active' : '' ?>">
                    <i class="bi bi-cart-check"></i><span>Ventas</span>
                </a>
            </li>
            <?php endif; ?>

            <?php if (puedeVerModulo('almacenes')): ?>
            <li class="nav-item">
                <a href="/cfsistem/app/controllers/almacenes.php" class="nav-link <?= ($archivoActual == 'almacenes.php' || $archivoActual == 'almacen.php') ? 'active' : '' ?>">
                    <i class="bi bi-box-seam"></i><span>Almacén</span>
                </a>
            </li>
            <?php endif; ?>

            <?php if (puedeVerModulo('movimientos')): ?>
            <li class="nav-item">
                <a href="/cfsistem/app/views/movimientos.php" class="nav-link <?= $archivoActual == 'movimientos.php' ? 'active' : '' ?>">
                    <i class="bi bi-arrow-left-right"></i><span>Movimientos</span>
                </a>
            </li>
            <?php endif; ?>

            <?php if (puedeVerModulo('ventashistorial')): ?>
            <li class="nav-item">
                <a href="/cfsistem/app/views/ventashistorial.php" class="nav-link <?= $archivoActual == 'ventashistorial.php' ? 'active' : '' ?>">
                    <i class="bi bi-receipt"></i><span>Historial</span>
                </a>
            </li>
            <?php endif; ?>

            <?php if (puedeVerModulo('caja')): ?>
            <li class="nav-item">
                <a href="/cfsistem/pantallas/caja.php" class="nav-link <?= $archivoActual == 'caja.php' ? 'active' : '' ?>">
                    <i class="bi bi-cash-stack"></i><span>Caja</span>
                </a>
            </li>
            <?php endif; ?>

            <?php if (puedeVerModulo('usuarios')): ?>
            <li class="nav-item">
                <a href="/cfsistem/app/controllers/usuarios.php" class="nav-link <?= $archivoActual == 'usuarios.php' ? 'active' : '' ?>">
                    <i class="bi bi-people"></i><span>Usuarios</span>
                </a>
            </li>
            <?php endif; ?>

            <?php if (puedeVerModulo('compras')): ?>
            <li class="nav-item">
                <a href="/cfsistem/app/controllers/egresosController.php" class="nav-link <?= ($archivoActual == 'compras.php' || $archivoActual == 'gastos.php') ? 'active' : '' ?>">
                    <i class="bi bi-bag-check"></i><span>Compras</span>
                </a>
            </li>
            <?php endif; ?>

            <?php if (puedeVerModulo('clientes')): ?>
            <li class="nav-item">
                <a href="/cfsistem/app/views/clientes.php" class="nav-link <?= $archivoActual == 'clientes.php' ? 'active' : '' ?>">
                    <i class="bi bi-person-lines-fill"></i><span>Clientes</span>
                </a>
            </li>
            <?php endif; ?>

            <?php if (puedeVerModulo('mermas')): ?>
            <li class="nav-item">
                <a href="/cfsistem/pantallas/mermas.php" class="nav-link <?= $archivoActual == 'mermas.php' ? 'active' : '' ?>">
                    <i class="bi bi-exclamation-triangle"></i><span>Mermas</span>
                </a>
            </li>
            <?php endif; ?>

            <?php if (puedeVerModulo('finanzas')): ?>
            <li class="nav-item">
                <a href="/cfsistem/app/views/finanzas.php" class="nav-link <?= $archivoActual == 'finanzas.php' ? 'active' : '' ?>">
                    <i class="bi bi-graph-up-arrow"></i><span>Finanzas</span>
                </a>
            </li>
            <?php endif; ?>
        </ul>
    </div>
</aside>