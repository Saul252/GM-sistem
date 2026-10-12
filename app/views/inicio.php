<?php
require_once __DIR__ . '/../../includes/auth.php';
protegerPagina();
require_once __DIR__ . '/../controllers/LayoutController.php';
require_once __DIR__ . '/../../includes/permisos.php';

$paginaActual = 'Inicio';
$archivoActual = basename($_SERVER['PHP_SELF']);

// Estructura de módulos por categoría
$gruposModulos = [
    [
        'id_grupo' => 'ventas_clientes',
        'titulo' => 'Ventas y Clientes',
        'icono' => 'bi-cart-check-fill',
        'theme' => 'emerald',
        'submodulos' => [
            //   ['id' => 'ventas', 'url' => '/cfsistem/app/controllers/ventasController.php', 'icon' => 'bi-cart-check', 'class' => 'text-success', 'label' => 'Ventas'],
            ['id' => 'remisiones', 'url' => '/cfsistem/app/controllers/requisicionesController.php', 'icon' => 'bi-receipt-cutoff', 'class' => 'text-success', 'label' => 'Remisiones'],
            ['id' => 'cajaRapida', 'url' => '/cfsistem/app/controllers/cajaRapidaController.php', 'icon' => 'bi-lightning-charge-fill', 'class' => 'text-warning', 'label' => 'Caja Rápida'],
            ['id' => 'cotizaciones', 'url' => '/cfsistem/app/controllers/cotizacionesController.php', 'icon' => 'bi-file-earmark-text', 'class' => 'text-info', 'label' => 'Cotizaciones'],
            ['id' => 'clientes', 'url' => '/cfsistem/app/controllers/clientesController.php', 'icon' => 'bi-person-lines-fill', 'class' => 'text-primary', 'label' => 'Clientes'],
            ['id' => 'clientesEstatus', 'url' => '/cfsistem/app/controllers/clientesEstatusController.php', 'icon' => 'bi-person-check', 'class' => 'text-success', 'label' => 'Estatus Clientes'],
            ['id' => 'ventasVendedor', 'url' => '/cfsistem/app/controllers/historialPedidosVendedorController.php', 'icon' => 'bi-person-badge-fill', 'class' => 'text-primary', 'label' => 'Ventas Vendedor'],
            ['id' => 'ventashistorial', 'url' => '/cfsistem/app/controllers/ventasHistorialController.php', 'icon' => 'bi-receipt', 'class' => 'text-secondary', 'label' => 'Historial Ventas'],
            ['id' => 'comprobantes', 'url' => '/cfsistem/app/controllers/comprobantesPagoController.php', 'icon' => 'bi-file-earmark-check', 'class' => 'text-info', 'label' => 'Comprobantes'],
            ['id' => 'registrarPagos', 'url' => '/cfsistem/app/controllers/registrarPagosController.php', 'icon' => 'bi-credit-card-2-front-fill', 'class' => 'text-success', 'label' => 'Registrar Pagos'],
            ['id' => 'historialPagos', 'url' => '/cfsistem/app/controllers/historialPagosController.php', 'icon' => 'bi-coin', 'class' => 'text-success', 'label' => 'historial Pagos'],
        ]
    ],
    [
        'id_grupo' => 'compras_proveedores',
        'titulo' => 'Compras y Proveedores',
        'icono' => 'bi-bag-check-fill',
        'theme' => 'sky',
        'submodulos' => [
            ['id' => 'compras', 'url' => '/cfsistem/app/controllers/egresosController.php', 'icon' => 'bi-bag-check', 'class' => 'text-danger', 'label' => 'Compras y Gastos'],
            ['id' => 'proveedores', 'url' => '/cfsistem/app/controllers/proveedoresController.php', 'icon' => 'bi-person-vcard', 'class' => 'text-dark', 'label' => 'Proveedores'],
            ['id' => 'solicitudesCompra', 'url' => '/cfsistem/app/controllers/solicitudesCompraController.php', 'icon' => 'bi-cart-check-fill', 'class' => 'text-info', 'label' => 'Solicitudes Compra'],
        ]
    ],
    [
        'id_grupo' => 'inventario_almacen',
        'titulo' => 'Inventario y Almacén',
        'icono' => 'bi-box-seam-fill',
        'theme' => 'amber',
        'submodulos' => [
            ['id' => 'almacenes', 'url' => '/cfsistem/app/controllers/almacenes.php', 'icon' => 'bi-box-seam', 'class' => 'text-warning', 'label' => 'Almacén'],
            ['id' => 'movimientos', 'url' => '/cfsistem/app/controllers/movimientosController.php', 'icon' => 'bi-arrow-left-right', 'class' => 'text-primary', 'label' => 'Movimientos'],
            ['id' => 'Mermas', 'url' => '/cfsistem/app/controllers/mermasController.php', 'icon' => 'bi-exclamation-triangle-fill', 'class' => 'text-danger', 'label' => 'Mermas'],
            ['id' => 'transmutaciones', 'url' => '/cfsistem/app/controllers/transmutacionesController.php', 'icon' => 'bi-arrow-repeat', 'class' => 'text-secondary', 'label' => 'Conversiones'],
            ['id' => 'historialLotes', 'url' => '/cfsistem/app/controllers/lotesHistorialController.php', 'icon' => 'bi-clock-history', 'class' => 'text-body-secondary', 'label' => 'Historial Lotes'],
            ['id' => 'comprasHistorial', 'url' => '/cfsistem/app/controllers/comprasHistorialController.php', 'icon' => 'bi-collection', 'class' => 'text-dark', 'label' => 'Historial Compras'],
        ]
    ],
    [
        'id_grupo' => 'finanzas_tesoreria',
        'titulo' => 'Finanzas y Tesorería',
        'icono' => 'bi-bank',
        'theme' => 'indigo',
        'submodulos' => [
            ['id' => 'finanzas', 'url' => '/cfsistem/app/controllers/finanzasController.php', 'icon' => 'bi-graph-up-arrow', 'class' => 'text-primary', 'label' => 'Finanzas'],
            ['id' => 'finanzas_admin', 'url' => '/cfsistem/app/controllers/finanzasAdmController.php', 'icon' => 'bi-bar-chart-line-fill', 'class' => 'text-dark', 'label' => 'Finanzas Admin'],
            ['id' => 'corteCaja', 'url' => '/cfsistem/app/controllers/corteCajaController.php', 'icon' => 'bi-cash-stack', 'class' => 'text-success', 'label' => 'Corte de Caja'],
            ['id' => 'tesoreria', 'url' => '/cfsistem/app/controllers/tesoreriaController.php', 'icon' => 'bi-safe-fill', 'class' => 'text-secondary', 'label' => 'Tesorería'],
        ]
    ],
    [
        'id_grupo' => 'logistica_distribucion',
        'titulo' => 'Logística y Distribución',
        'icono' => 'bi-truck-front-fill',
        'theme' => 'slate',
        'submodulos' => [
            ['id' => 'entregasVista', 'url' => '/cfsistem/app/controllers/entregasVistaController.php', 'icon' => 'bi-truck', 'class' => 'text-warning', 'label' => 'Despachos'],
            ['id' => 'vehiculos', 'url' => '/cfsistem/app/controllers/vehiculosController.php', 'icon' => 'bi-truck-front', 'class' => 'text-secondary', 'label' => 'Vehículos'],
            ['id' => 'verificaciones', 'url' => '/cfsistem/app/controllers/verificacionesController.php', 'icon' => 'bi-patch-check-fill', 'class' => 'text-success', 'label' => 'verificaciones',],

            ['id' => 'mantenimientos', 'url' => '/cfsistem/app/controllers/mantenimientosController.php', 'icon' => 'bi-wrench-adjustable-circle-fill', 'class' => 'text-danger', 'label' => 'Mantenimientos'],
            ['id' => 'repartosVista', 'url' => '/cfsistem/app/controllers/repartosVistaController.php', 'icon' => 'bi-truck-flatbed', 'class' => 'text-info', 'label' => 'Repartos'],
            ['id' => 'misRepartos', 'url' => '/cfsistem/app/controllers/misRepartosController.php', 'icon' => 'bi-map-fill', 'class' => 'text-primary', 'label' => 'Mis Repartos'],
            ['id' => 'viajesTrabajadores', 'url' => '/cfsistem/app/controllers/viajesTrabajadoresController.php', 'icon' => 'bi-person-workspace', 'class' => 'text-primary', 'label' => 'Viajes Personal'],
        ]
    ],
    [
        'id_grupo' => 'recursos_humanos',
        'titulo' => 'Recursos Humanos',
        'icono' => 'bi-people-fill',
        'theme' => 'rose',
        'submodulos' => [
            ['id' => 'trabajadores', 'url' => '/cfsistem/app/controllers/trabajadoresController.php', 'icon' => 'bi-people-fill', 'class' => 'text-primary', 'label' => 'Trabajadores'],
            ['id' => 'nomina', 'url' => '/cfsistem/app/controllers/nominaController.php', 'icon' => 'bi-cash', 'class' => 'text-success', 'label' => 'Nómina'],
            ['id' => 'prestamos', 'url' => '/cfsistem/app/controllers/prestamosController.php', 'icon' => 'bi-cash-coin', 'class' => 'text-warning', 'label' => 'Préstamos'],
            ['id' => 'faltas', 'url' => '/cfsistem/app/controllers/faltasController.php', 'icon' => 'bi-calendar-x-fill', 'class' => 'text-danger', 'label' => 'Faltas'],
            ['id' => 'pagos_viajes', 'url' => '/cfsistem/app/controllers/pagos_viajesController.php', 'icon' => 'bi-person-gear', 'class' => 'text-success', 'label' => 'Pagos Viajes'],
            ['id' => 'vacaciones', 'url' => '/cfsistem/app/controllers/vacacionesController.php', 'icon' => 'bi-sun-fill', 'class' => 'text-dark', 'label' => 'Vacaciones'],

        ]
    ],
    [
        'id_grupo' => 'administracion',
        'titulo' => 'Administración',
        'icono' => 'bi-gear-fill',
        'theme' => 'red',
        'submodulos' => [
            ['id' => 'usuarios', 'url' => '/cfsistem/app/controllers/usuariosController.php', 'icon' => 'bi-people', 'class' => 'text-danger', 'label' => 'Usuarios', 'active' => ($archivoActual == 'usuariosController.php')],
        ]
    ]
];
?>
<!DOCTYPE html>
<html lang="es" data-bs-theme="light">

<head>
    <meta charset="UTF-8" name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Inicio - Sistema de Almacenes</title>
    <link rel="icon" type="image/png" href="/cfsistem/public/assets/logo.png">

    <link rel="shortcut icon" href="/cfsistem/public/assets/logo.ico" type="image/x-icon">


    <!-- Script Anti-Parpadeo (Carga la preferencia inmediatamente) -->
    <script>
        (function () {
            const savedTheme = localStorage.getItem('theme') || 'light';
            document.documentElement.setAttribute('data-bs-theme', savedTheme);
        })();
    </script>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <?php require_once __DIR__ . '/layout/icono.php' ?>
    <?php if (function_exists('cargarEstilos')) {
        cargarEstilos();
    } ?>

    <style>
        :root {
            --sidebar-width: 260px;
            --navbar-height: 65px;
            --card-radius: 18px;
            --transition-smooth: all 0.35s cubic-bezier(0.16, 1, 0.3, 1);


            /* =========================================
   TEMA CLARO: MAÑANA
   Cielo azul, sol dorado y nubes luminosas
========================================= */
            --bg-light-morning:
                /* Sol matutino brillante */
                radial-gradient(circle 280px at 82% 14%,
                    rgba(255, 245, 200, 0.98) 0%,
                    rgba(255, 215, 130, 0.65) 28%,
                    rgba(255, 190, 110, 0.22) 55%,
                    transparent 75%),

                /* Resplandor del amanecer extendido */
                radial-gradient(ellipse 70% 40% at 80% 8%,
                    rgba(255, 230, 190, 0.55) 0%,
                    rgba(255, 210, 170, 0.2) 55%,
                    transparent 80%),

                /* Nubes blancas suaves */
                radial-gradient(ellipse 52% 30% at 22% 20%,
                    rgba(255, 255, 255, 0.88) 0%,
                    rgba(255, 250, 240, 0.5) 55%,
                    transparent 78%),
                radial-gradient(ellipse 46% 26% at 62% 13%,
                    rgba(255, 250, 235, 0.75) 0%,
                    transparent 75%),

                /* Colinas cálidas */
                radial-gradient(ellipse 82% 50% at 12% 100%,
                    rgba(180, 90, 60, 0.38) 0%,
                    rgba(215, 135, 90, 0.2) 45%,
                    transparent 78%),
                radial-gradient(ellipse 80% 50% at 95% 100%,
                    rgba(120, 80, 60, 0.32) 0%,
                    rgba(175, 120, 85, 0.18) 48%,
                    transparent 78%),

                /* Hexágonos elegantes */
                conic-gradient(from 30deg at 50% 50%,
                    transparent 0deg 59deg,
                    rgba(255, 255, 255, 0.35) 59deg 61deg,
                    transparent 61deg 119deg,
                    rgba(255, 255, 255, 0.35) 119deg 121deg,
                    transparent 121deg 179deg,
                    rgba(255, 255, 255, 0.35) 179deg 181deg,
                    transparent 181deg 239deg,
                    rgba(255, 255, 255, 0.35) 239deg 241deg,
                    transparent 241deg 299deg,
                    rgba(255, 255, 255, 0.35) 299deg 301deg,
                    transparent 301deg 360deg),

                /* Cielo base con degradado más rico */
                linear-gradient(180deg,
                    #7FB8DD 0%,
                    #A8D4E8 30%,
                    #DCE8E0 55%,
                    #E8D3B8 78%,
                    #C58B6B 100%);
            --bg-light-noon:
                /* ☀️ Sol brillante centrado arriba */
                radial-gradient(circle 320px at 50% 8%,
                    rgba(255, 250, 200, 0.98) 0%,
                    rgba(255, 230, 140, 0.7) 25%,
                    rgba(255, 210, 120, 0.3) 50%,
                    rgba(255, 190, 100, 0.08) 70%,
                    transparent 82%),

                /* Resplandor difuso del sol (centrado) */
                radial-gradient(ellipse 60% 40% at 50% 6%,
                    rgba(255, 245, 210, 0.55) 0%,
                    rgba(255, 235, 190, 0.2) 55%,
                    transparent 80%),

                /* ☁️ Nubes a los lados del sol */
                radial-gradient(ellipse 42% 26% at 15% 18%,
                    rgba(255, 255, 255, 0.95) 0%,
                    rgba(255, 255, 255, 0.5) 55%,
                    transparent 78%),
                radial-gradient(ellipse 38% 22% at 85% 16%,
                    rgba(255, 255, 255, 0.9) 0%,
                    rgba(255, 255, 255, 0.45) 55%,
                    transparent 78%),
                radial-gradient(ellipse 40% 22% at 30% 32%,
                    rgba(255, 255, 255, 0.8) 0%,
                    transparent 75%),
                radial-gradient(ellipse 42% 24% at 72% 35%,
                    rgba(255, 255, 255, 0.75) 0%,
                    transparent 75%),

                /* 🌿 Colinas verdes brillantes */
                radial-gradient(ellipse 85% 52% at 12% 100%,
                    rgba(120, 200, 100, 0.75) 0%,
                    rgba(90, 175, 80, 0.42) 45%,
                    transparent 80%),
                radial-gradient(ellipse 82% 50% at 95% 100%,
                    rgba(140, 215, 110, 0.7) 0%,
                    rgba(105, 185, 90, 0.38) 48%,
                    transparent 78%),

                /* 🔷 Hexágonos sutiles */
                conic-gradient(from 30deg at 50% 50%,
                    transparent 0deg 59deg,
                    rgba(255, 255, 255, 0.4) 59deg 61deg,
                    transparent 61deg 119deg,
                    rgba(255, 255, 255, 0.4) 119deg 121deg,
                    transparent 121deg 179deg,
                    rgba(255, 255, 255, 0.4) 179deg 181deg,
                    transparent 181deg 239deg,
                    rgba(255, 255, 255, 0.4) 239deg 241deg,
                    transparent 241deg 299deg,
                    rgba(255, 255, 255, 0.4) 299deg 301deg,
                    transparent 301deg 360deg),

                /* 🌤️ Cielo azul brillante */
                linear-gradient(180deg,
                    #4FA8E0 0%,
                    #7CC5ED 30%,
                    #B8E0F5 55%,
                    #DFF0E8 78%,
                    #C8E0B8 100%);
            --bg-light-night:
                /* Última luz del sol */
                radial-gradient(circle 300px at 78% 18%,
                    rgba(255, 195, 130, 0.58) 0%,
                    rgba(210, 120, 100, 0.28) 38%,
                    rgba(170, 85, 95, 0.08) 65%,
                    transparent 80%),

                /* Estrellas brillantes */
                radial-gradient(circle at 12% 16%, rgba(255, 255, 255, 0.98) 0 1.2px, transparent 2.5px),
                radial-gradient(circle at 28% 31%, rgba(255, 248, 225, 0.95) 0 1px, transparent 2px),
                radial-gradient(circle at 43% 12%, rgba(255, 255, 255, 0.92) 0 1.2px, transparent 2.5px),
                radial-gradient(circle at 59% 25%, rgba(255, 248, 225, 0.98) 0 1.5px, transparent 2.8px),
                radial-gradient(circle at 72% 38%, rgba(255, 255, 255, 0.88) 0 1px, transparent 2px),
                radial-gradient(circle at 89% 20%, rgba(255, 248, 225, 0.95) 0 1.2px, transparent 2.5px),
                radial-gradient(circle at 19% 48%, rgba(255, 255, 255, 0.85) 0 1px, transparent 2px),
                radial-gradient(circle at 81% 55%, rgba(255, 255, 255, 0.9) 0 1px, transparent 2px),
                radial-gradient(circle at 35% 62%, rgba(255, 240, 220, 0.85) 0 1px, transparent 2px),
                radial-gradient(circle at 68% 75%, rgba(255, 255, 255, 0.8) 0 1px, transparent 2px),

                /* Colinas rojizas y violáceas */
                radial-gradient(ellipse 82% 50% at 10% 100%,
                    rgba(150, 55, 55, 0.48) 0%,
                    rgba(120, 45, 65, 0.25) 45%,
                    transparent 72%),
                radial-gradient(ellipse 80% 50% at 95% 100%,
                    rgba(75, 45, 55, 0.5) 0%,
                    rgba(90, 55, 70, 0.28) 48%,
                    transparent 75%),

                /* Hexágonos sutiles */
                conic-gradient(from 30deg at 50% 50%,
                    transparent 0deg 59deg,
                    rgba(255, 220, 200, 0.18) 59deg 61deg,
                    transparent 61deg 119deg,
                    rgba(255, 220, 200, 0.18) 119deg 121deg,
                    transparent 121deg 179deg,
                    rgba(255, 220, 200, 0.18) 179deg 181deg,
                    transparent 181deg 239deg,
                    rgba(255, 220, 200, 0.18) 239deg 241deg,
                    transparent 241deg 299deg,
                    rgba(255, 220, 200, 0.18) 299deg 301deg,
                    transparent 301deg 360deg),

                /* Cielo del crepúsculo */
                linear-gradient(180deg,
                    #4A4E6E 0%,
                    #7A6E85 30%,
                    #B887A0 52%,
                    #C68478 75%,
                    #6E4A4D 100%);

            --bg-dark-morning:
                /* Sol frío, luz dorada tenue */
                radial-gradient(circle 300px at 82% 15%,
                    rgba(255, 210, 140, 0.42) 0%,
                    rgba(230, 155, 95, 0.18) 45%,
                    transparent 78%),

                /* Resplandor extendido */
                radial-gradient(ellipse 60% 32% at 80% 10%,
                    rgba(210, 170, 130, 0.2) 0%,
                    transparent 78%),

                /* Nubes azuladas */
                radial-gradient(ellipse 55% 32% at 22% 20%,
                    rgba(130, 165, 195, 0.28) 0%,
                    transparent 75%),
                radial-gradient(ellipse 44% 25% at 62% 15%,
                    rgba(110, 145, 180, 0.2) 0%,
                    transparent 75%),

                /* Colinas */
                radial-gradient(ellipse 82% 50% at 10% 100%,
                    rgba(120, 60, 60, 0.45) 0%,
                    rgba(85, 45, 50, 0.22) 45%,
                    transparent 78%),
                radial-gradient(ellipse 80% 50% at 95% 100%,
                    rgba(50, 35, 45, 0.5) 0%,
                    transparent 75%),

                /* Hexágonos */
                conic-gradient(from 30deg at 50% 50%,
                    transparent 0deg 59deg,
                    rgba(202, 222, 238, 0.18) 59deg 61deg,
                    transparent 61deg 119deg,
                    rgba(202, 222, 238, 0.18) 119deg 121deg,
                    transparent 121deg 179deg,
                    rgba(202, 222, 238, 0.18) 179deg 181deg,
                    transparent 181deg 239deg,
                    rgba(202, 222, 238, 0.18) 239deg 241deg,
                    transparent 241deg 299deg,
                    rgba(202, 222, 238, 0.18) 299deg 301deg,
                    transparent 301deg 360deg),

                /* Cielo nocturno-amanecer */
                linear-gradient(180deg,
                    #1E3348 0%,
                    #2C4459 35%,
                    #4A4550 65%,
                    #2A2632 100%);

            --bg-dark-noon:
                /* Sol cobre */
                radial-gradient(circle 340px at 82% 14%,
                    rgba(235, 145, 80, 0.36) 0%,
                    rgba(175, 80, 60, 0.16) 45%,
                    transparent 78%),

                /* Resplandor cálido */
                radial-gradient(ellipse 60% 32% at 80% 10%,
                    rgba(200, 120, 80, 0.18) 0%,
                    transparent 78%),

                /* Nubes oscuras */
                radial-gradient(ellipse 52% 30% at 22% 20%,
                    rgba(110, 70, 80, 0.32) 0%,
                    transparent 75%),
                radial-gradient(ellipse 46% 26% at 62% 15%,
                    rgba(95, 60, 70, 0.24) 0%,
                    transparent 75%),

                /* Colinas granate */
                radial-gradient(ellipse 82% 50% at 12% 100%,
                    rgba(140, 45, 45, 0.48) 0%,
                    rgba(110, 35, 40, 0.25) 45%,
                    transparent 78%),
                radial-gradient(ellipse 80% 50% at 95% 100%,
                    rgba(55, 40, 40, 0.55) 0%,
                    transparent 75%),

                /* Hexágonos */
                conic-gradient(from 30deg at 50% 50%,
                    transparent 0deg 59deg,
                    rgba(255, 224, 198, 0.16) 59deg 61deg,
                    transparent 61deg 119deg,
                    rgba(255, 224, 198, 0.16) 119deg 121deg,
                    transparent 121deg 179deg,
                    rgba(255, 224, 198, 0.16) 179deg 181deg,
                    transparent 181deg 239deg,
                    rgba(255, 224, 198, 0.16) 239deg 241deg,
                    transparent 241deg 299deg,
                    rgba(255, 224, 198, 0.16) 299deg 301deg,
                    transparent 301deg 360deg),

                /* Cielo oscuro cálido */
                linear-gradient(180deg,
                    #2A3546 0%,
                    #48404B 35%,
                    #654540 65%,
                    #261F26 100%);

            --bg-dark-night:
                /* Bruma violeta profunda */
                radial-gradient(ellipse 65% 48% at 78% 18%,
                    rgba(120, 90, 160, 0.28) 0%,
                    rgba(90, 70, 130, 0.12) 50%,
                    transparent 78%),

                /* Segunda bruma azulada */
                radial-gradient(ellipse 55% 40% at 22% 25%,
                    rgba(70, 90, 140, 0.22) 0%,
                    transparent 75%),

                /* Estrellas brillantes */
                radial-gradient(circle at 8% 12%, rgba(255, 255, 255, 1) 0 1.3px, transparent 2.5px),
                radial-gradient(circle at 17% 27%, rgba(205, 224, 255, 0.95) 0 1px, transparent 2px),
                radial-gradient(circle at 29% 9%, rgba(255, 255, 255, 0.92) 0 1.2px, transparent 2.5px),
                radial-gradient(circle at 37% 37%, rgba(255, 255, 255, 0.98) 0 1.5px, transparent 2.8px),
                radial-gradient(circle at 48% 18%, rgba(205, 224, 255, 0.92) 0 1px, transparent 2px),
                radial-gradient(circle at 57% 8%, rgba(255, 255, 255, 0.95) 0 1.2px, transparent 2.5px),
                radial-gradient(circle at 68% 30%, rgba(255, 255, 255, 1) 0 1.3px, transparent 2.5px),
                radial-gradient(circle at 78% 12%, rgba(205, 224, 255, 0.95) 0 1px, transparent 2px),
                radial-gradient(circle at 91% 29%, rgba(255, 255, 255, 0.92) 0 1.2px, transparent 2.5px),
                radial-gradient(circle at 13% 48%, rgba(255, 255, 255, 0.85) 0 1px, transparent 2px),
                radial-gradient(circle at 84% 48%, rgba(205, 224, 255, 0.9) 0 1px, transparent 2px),
                radial-gradient(circle at 61% 45%, rgba(255, 255, 255, 0.88) 0 1px, transparent 2px),
                radial-gradient(circle at 25% 68%, rgba(255, 255, 255, 0.85) 0 1px, transparent 2px),
                radial-gradient(circle at 72% 62%, rgba(205, 224, 255, 0.9) 0 1px, transparent 2px),

                /* Colinas rojo vino */
                radial-gradient(ellipse 82% 50% at 10% 100%,
                    rgba(130, 42, 55, 0.45) 0%,
                    rgba(95, 30, 45, 0.22) 45%,
                    transparent 75%),
                radial-gradient(ellipse 80% 50% at 95% 100%,
                    rgba(55, 38, 55, 0.6) 0%,
                    rgba(70, 45, 65, 0.3) 48%,
                    transparent 75%),

                /* Hexágonos oscuros */
                conic-gradient(from 30deg at 50% 50%,
                    transparent 0deg 59deg,
                    rgba(200, 180, 220, 0.12) 59deg 61deg,
                    transparent 61deg 119deg,
                    rgba(200, 180, 220, 0.12) 119deg 121deg,
                    transparent 121deg 179deg,
                    rgba(200, 180, 220, 0.12) 179deg 181deg,
                    transparent 181deg 239deg,
                    rgba(200, 180, 220, 0.12) 239deg 241deg,
                    transparent 241deg 299deg,
                    rgba(200, 180, 220, 0.12) 299deg 301deg,
                    transparent 301deg 360deg),

                /* Cielo nocturno con más profundidad */
                linear-gradient(180deg,
                    #0D1220 0%,
                    #1A1F35 30%,
                    #29223A 58%,
                    #3A2A3F 82%,
                    #141824 100%);

            /* El elemento que utiliza el fondo */



            /* Variables de Tarjeta Modo Claro */
            --card-bg: #ff000005;
            --card-border: rgba(226, 232, 240, 0.8);
            --card-border-hover: rgba(203, 213, 225, 1);
            --card-header-bg: #ffffff0a;
            --card-header-hover: #fafafa;
            --card-body-bg: #f8fafc1f;
            --card-text-color: #1e293b;
            --btn-bg: #ffffff;
            --btn-border: #e2e8f0;
            --btn-text: #334155;
            --btn-hover-bg: #ffffff;
            --btn-hover-border: #cbd5e1;
        }

        [data-bs-theme="dark"] {
            --bg-gradient: linear-gradient(270deg, #0f172a, #1e1b4b, #1e293b, #0f172a) !important;


            /* Variables de Tarjeta Modo Oscuro */
            --card-bg: #1e293b;
            --card-border: rgba(51, 65, 85, 0.7);
            --card-border-hover: rgba(71, 85, 105, 1);
            --card-header-bg: #1e293b;
            --card-header-hover: #334155;
            --card-body-bg: #0f172a;
            --card-text-color: #f8fafc;
            --btn-bg: #334155;
            --btn-border: #475569;
            --btn-text: #e2e8f0;
            --btn-hover-bg: #475569;
            --btn-hover-border: #64748b;
        }

        body {
            position: relative;
            overflow-x: hidden;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            font-weight: 600;
            letter-spacing: .3px;

            background: var(--bg-gradient) !important;
            background-size: 600% 600%;
            animation: moverGradiente 12s ease infinite !important;
            transition: background 0.5s ease;
        }


        .main-content {
            margin-left: var(--sidebar-width);
            background-color: var(--bg-gradient) !important;
            padding: 28px;
            padding-top: calc(var(--navbar-height) + 18px);
            min-height: 100vh;
            background-size: 600% 600%;
            animation: moverGradiente 12s ease infinite !important;
            transition: background 0.5s ease;
        }

        /* Temas de iconos adaptados */
        .theme-emerald {
            background-color: rgba(16, 185, 129, 0.15);
            color: #10b981;
        }

        .theme-sky {
            background-color: rgba(2, 132, 199, 0.15);
            color: #0284c7;
        }

        .theme-amber {
            background-color: rgba(245, 158, 11, 0.15);
            color: #f59e0b;
        }

        .theme-indigo {
            background-color: rgba(99, 102, 241, 0.15);
            color: #6366f1;
        }

        .theme-slate {
            background-color: rgba(100, 116, 139, 0.15);
            color: #64748b;
        }

        .theme-rose {
            background-color: rgba(244, 63, 94, 0.15);
            color: #f43f5e;
        }

        .theme-red {
            background-color: rgba(239, 68, 68, 0.15);
            color: #ef4444;
        }

        /* Tarjeta Elegante Adaptable */
        .card-elegant {
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: var(--card-radius);
            box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.15);
            overflow: hidden;
            transition: var(--transition-smooth);
        }

        .card-elegant:hover {
            border-color: var(--card-border-hover);
            box-shadow: 0 12px 28px -6px rgba(0, 0, 0, 0.25);
        }

        .card-header-toggle {
            padding: 16px 20px;
            background: var(--card-header-bg);
            cursor: pointer;
            user-select: none;
            display: flex;
            align-items: center;
            justify-content: space-between;
            transition: background 0.2s ease;
        }

        .card-header-toggle:hover {
            background-color: var(--card-header-hover);
        }

        .icon-wrapper {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.15rem;
            flex-shrink: 0;
            transition: transform 0.3s ease;
        }

        .card-header-toggle:hover .icon-wrapper {
            transform: scale(1.05);
        }

        .card-title-text {
            font-size: 0.95rem;
            font-weight: 700;
            color: var(--card-text-color);
            margin: 0;
        }

        .arrow-icon {
            font-size: 0.85rem;
            color: #94a3b8;
            transition: transform 0.3s ease, color 0.2s ease;
        }

        .card-header-toggle:not(.collapsed) .arrow-icon {
            transform: rotate(180deg);
            color: #38bdf8;
        }

        .card-body-content {
            padding: 12px 18px 18px 18px;
            background-color: var(--card-body-bg);
            border-top: 1px solid var(--card-border);
        }

        /* Botón Módulo Adaptable */
        .btn-module {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 14px;
            border-radius: 12px;
            background-color: var(--btn-bg);
            border: 1px solid var(--btn-border);
            color: var(--btn-text);
            text-decoration: none;
            font-size: 0.81rem;
            font-weight: 600;
            transition: var(--transition-smooth);
            height: 100%;
        }

        .btn-module i {
            font-size: 1.15rem;
            flex-shrink: 0;
            transition: transform 0.2s ease;
        }

        .btn-module:hover {
            background-color: var(--btn-hover-bg);
            border-color: var(--btn-hover-border);
            color: #38bdf8;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .btn-module:hover i {
            transform: scale(1.15);
        }

        .counter-badge {
            font-size: 0.72rem;
            font-weight: 600;
            color: var(--btn-text);
            background-color: var(--card-body-bg);
            border: 1px solid var(--btn-border);
            padding: 2px 8px;
            border-radius: 20px;
        }

        .btn-toggle-all {
            font-size: 0.78rem;
            font-weight: 600;
            color: var(--btn-text);
            background: var(--btn-bg);
            border: 1px solid var(--btn-border);
            padding: 6px 14px;
            border-radius: 10px;
            transition: var(--transition-smooth);
        }

        .btn-toggle-all:hover {
            background: var(--card-header-hover);
            color: var(--card-text-color);
            border-color: var(--btn-hover-border);
        }

        @media (max-width: 576px) {
            .main-content {
                margin-left: 0 !important;
                padding: 14px;
                padding-top: calc(var(--navbar-height) + 14px);
            }

            .card-header-toggle {
                padding: 14px;
            }

            .btn-module {
                padding: 8px 10px;
                font-size: 0.78rem;
            }
        }

        @keyframes moverGradiente {
            0% {
                background-position: 0% 50%;
            }

            50% {
                background-position: 100% 50%;
            }

            100% {
                background-position: 0% 50%;
            }
        }
    </style>
</head>

<body>

    <?php if (function_exists('renderizarLayout')) {
        renderizarLayout($paginaActual);
    } ?>

    <main class="main-content">
        <div class="container-fluid p-0">

            <div class="d-flex align-items-center justify-content-between mb-4 header-section flex-wrap gap-2">
                <div>
                    <h2 class="fw-bold m-0 card-title-text" style="letter-spacing: -0.5px; font-size: 1.45rem;">Panel
                        Principal</h2>
                    <p class="card-title-text-50 mb-0" style="font-size: 0.85rem;">Selecciona el módulo con el que vas a
                        trabajar</p>
                </div>

                <div class="d-flex gap-2 align-items-center">
                    <!-- Botón Selector de Modo Oscuro -->


                    <button type="button" class="btn btn-toggle-all" onclick="toggleTodosRecuadros(true)">
                        <i class="bi bi-arrows-expand me-1"></i> Expandir
                    </button>
                    <button type="button" class="btn btn-toggle-all" onclick="toggleTodosRecuadros(false)">
                        <i class="bi bi-arrows-collapse me-1"></i> Contraer
                    </button>
                </div>
            </div>

            <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-3">

                <?php foreach ($gruposModulos as $index => $grupo): ?>

                    <?php
                    $submodulosPermitidos = array_filter($grupo['submodulos'], function ($m) {
                        return puedeVerModulo($m['id']);
                    });

                    if (empty($submodulosPermitidos))
                        continue;

                    $estaAbierto = ($index < 2);
                    $idCollapse = "desplegable_" . $grupo['id_grupo'];
                    ?>

                    <div class="col">
                        <div class="card-elegant">

                            <div class="card-header-toggle <?= $estaAbierto ? '' : 'collapsed' ?>" data-bs-toggle="collapse"
                                data-bs-target="#<?= $idCollapse ?>" aria-expanded="<?= $estaAbierto ? 'true' : 'false' ?>"
                                aria-controls="<?= $idCollapse ?>">

                                <div class="d-flex align-items-center gap-3">
                                    <div class="icon-wrapper theme-<?= $grupo['theme'] ?>">
                                        <i class="bi <?= $grupo['icono'] ?>"></i>
                                    </div>
                                    <h3 class="card-title-text"><?= $grupo['titulo'] ?></h3>
                                </div>

                                <div class="d-flex align-items-center gap-2">
                                    <span class="counter-badge">
                                        <?= count($submodulosPermitidos) ?>
                                    </span>
                                    <i class="bi bi-chevron-down arrow-icon"></i>
                                </div>
                            </div>

                            <div id="<?= $idCollapse ?>" class="collapse <?= $estaAbierto ? 'show' : '' ?>">
                                <div class="card-body-content">
                                    <div class="row row-cols-1 row-cols-sm-2 g-2">
                                        <?php foreach ($submodulosPermitidos as $m): ?>
                                            <div class="col">
                                                <a href="<?= $m['url'] ?>" class="btn-module">
                                                    <i class="bi <?= $m['icon'] ?> <?= $m['class'] ?? '' ?>"></i>
                                                    <span class="text-truncate"><?= $m['label'] ?></span>
                                                </a>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                <?php endforeach; ?>

            </div>

        </div>
    </main>

    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>




        function toggleTodosRecuadros(abrir) {
            const collapses = document.querySelectorAll('.card-elegant .collapse');
            collapses.forEach(el => {
                const bsCollapse = bootstrap.Collapse.getOrCreateInstance(el, { toggle: false });
                if (abrir) {
                    bsCollapse.show();
                } else {
                    bsCollapse.hide();
                }
            });
        }

        // Sincronizar el ícono del botón al cargar

    </script>

    <script>
        seleccionarFondoInicio();
        function seleccionarFondoInicio() {
            const html = document.documentElement;
            const body = document.body;

            if (!body) return;

            const tema = html.getAttribute('data-bs-theme') || 'light';
            const hora = new Date().getHours();

            let periodo;

            if (hora >= 6 && hora < 12) {
                periodo = 'morning';
            } else if (hora >= 12 && hora < 14) {
                periodo = 'noon';
            } else {
                periodo = 'night';
            }

            const nombreVariable = `--bg-${tema}-${periodo}`;
            const fondo = getComputedStyle(html)
                .getPropertyValue(nombreVariable)
                .trim();

            if (!fondo) {
                console.error('No existe la variable CSS:', nombreVariable);
                return;
            }

            html.style.setProperty('--bg-gradient', fondo);

            body.style.setProperty('background', fondo, 'important');
            body.style.setProperty('background-size', '600% 600%');
        }

        // Ejecutar automáticamente al iniciar
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', seleccionarFondoInicio, {
                once: true
            });
        } else {
            seleccionarFondoInicio();
        }

    </script>

</body>

</html>