<nav class="navbar fixed-top navbar-expand-lg navbar-dark navbar-premium">
    <div class="container-fluid px-4">
        <div class="d-flex align-items-center gap-3">
            <button class="btn btn-toggle" id="toggleSidebar"><i class="bi bi-list fs-3 text-white"></i></button>
            <span class="fw-semibold text-white"><?= $tituloPagina ?></span>
        </div>
        <div class="d-flex align-items-center gap-3">
            <div class="user-badge"><span><?= $_SESSION['nombre'] ?></span></div>
            <a href="/cfsistem/logout.php" class="btn btn-logout"><i class="bi bi-box-arrow-right"></i></a>
        </div>
    </div>
</nav>

<aside id="sidebar">
    <div class="p-3">
        <h5 class="text-center mb-4">Menú</h5>
        <ul class="nav nav-pills flex-column gap-1">
            <?php foreach ($menu as $item): ?>
                <li class="nav-item">
                    <a href="/cfsistem/app/views/<?= $item['url'] ?>" 
                       class="nav-link <?= (basename($_SERVER['PHP_SELF']) == $item['url']) ? 'active' : '' ?>">
                        <i class="bi <?= $item['icon'] ?>"></i><span><?= $item['label'] ?></span>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</aside>