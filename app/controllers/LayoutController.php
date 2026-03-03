<?php
require_once __DIR__ . '/../../includes/auth.php'; 
require_once __DIR__ . '/../../includes/permisos.php'; 
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../models/sidebar_model.php';

function cargarEstilos() {
    echo '<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">';
    echo '<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">';
    echo '<link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">';
    ?>
    <style>
        /* NAVBAR PREMIUM */
        .navbar-premium { 
            background: #111827 !important; 
            height: 65px; 
            border-bottom: 1px solid rgba(255, 255, 255, .1); 
            z-index: 1060; 
        }
        
        /* SIDEBAR NEGRO/OSCURO */
        #sidebar { 
            width: 260px; 
            height: 100vh; 
            position: fixed; 
            top: 65px; 
            left: 0; 
            background: #111827; /* Cambiado a negro */
            border-right: 1px solid rgba(255,255,255,0.1); 
            transition: .3s ease; 
            z-index: 1050; 
        }

        #sidebar h5 { color: #ffffff; padding-top: 10px; }
        
        /* LINKS DEL SIDEBAR EN MODO OSCURO */
        #sidebar .nav-link { 
            display: flex; 
            align-items: center; 
            gap: 10px; 
            color: #9ca3af !important; 
            border-radius: 8px; 
            padding: 10px 15px; 
            margin: 4px 10px;
            transition: all .2s;
        }

        #sidebar .nav-link:hover { 
            background: rgba(255,255,255,0.05); 
            color: #ffffff !important; 
        }

        #sidebar .nav-link.active { 
            background: #2563eb !important; 
            color: #ffffff !important; 
        }

        /* CONTENIDO PRINCIPAL */
        .main-content { 
            margin-left: 260px; 
            padding-top: 85px; 
            padding-left: 20px; 
            padding-right: 20px; 
            transition: .3s ease; 
        }

        body.sidebar-hidden .main-content { margin-left: 0; }
        #sidebar.hidden { transform: translateX(-260px); }
    </style>
    <?php
}

function renderizarLayout($tituloPagina) {
    protegerPagina(); 
    $menu = SidebarModel::obtenerMenu();
    $paginaActual = $tituloPagina;
    $archivoActual = basename($_SERVER['PHP_SELF']);
    include __DIR__ . '/../views/layout/sidebar_view.php';
}

function cargarScripts() {
    // Librerías base
    echo '<script src="https://code.jquery.com/jquery-3.7.0.js"></script>';
    echo '<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>';
    echo '<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>';
    echo '<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>';
    // Tu lógica de notificaciones
    echo '<script src="/cfsistem/app/backend/notificaciones/notificaciones.js"></script>';
}