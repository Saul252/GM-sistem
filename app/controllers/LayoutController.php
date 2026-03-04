<?php
require_once __DIR__ . '/../../includes/auth.php'; 
require_once __DIR__ . '/../../includes/permisos.php'; 
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../models/sidebar_model.php';

function cargarEstilos() {
    echo '<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">';
    echo '<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">';
    echo '<link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">';
    echo '<link href="/cfsistem/css/layout.css" rel="stylesheet">';
    ?>

    <?php
}

function renderizarLayout($tituloPagina) {
    protegerPagina(); 
    $menu = SidebarModel::obtenerMenu();
    $paginaActual = $tituloPagina;
    $archivoActual = basename($_SERVER['PHP_SELF']);
    include __DIR__ . '/../views/sidebar_view.php';
}

function cargarScripts() {
    // Librerías base
    echo '<script src="https://code.jquery.com/jquery-3.7.0.js"></script>';
    echo '<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>';
    echo '<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>';
    echo '<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>';
    // Tu lógica de notificaciones
    echo '<script src="/cfsistem/app/backend/notificaciones/notificaciones.js"></script>';
     echo '<script src="/cfsistem/app/backend/sidebar/sidebar.js"></script>';

}