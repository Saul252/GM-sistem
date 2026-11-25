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
            ['id' => 'clientesEstatus', 'url' => '/cfsistem/app/controllers/clientesEstatusController.php', 'icon' => 'bi-person-check', 'class' => 'text-success', 'label' => 'Historial de Clientes'],
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
    <link rel="shortcut icon" href="/cfsistem/public/assets/logo.ico" type="image/x-icon">




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
                url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 800 500' preserveAspectRatio='xMidYMax slice'%3E%3Cdefs%3E%3ClinearGradient id='sky' x1='0' y1='0' x2='0' y2='1'%3E%3Cstop offset='0' stop-color='%238bb8e8'/%3E%3Cstop offset='0.4' stop-color='%23c6d8ef'/%3E%3Cstop offset='0.7' stop-color='%23f8d3bd'/%3E%3Cstop offset='1' stop-color='%23ffc99b'/%3E%3C/linearGradient%3E%3ClinearGradient id='gr' x1='0' y1='0' x2='0' y2='1'%3E%3Cstop offset='0' stop-color='%23fffaf3'/%3E%3Cstop offset='1' stop-color='%23e6d8e6'/%3E%3C/linearGradient%3E%3CradialGradient id='halo'%3E%3Cstop offset='0' stop-color='%23ffe9a0' stop-opacity='.95'/%3E%3Cstop offset='1' stop-color='%23ffd36b' stop-opacity='0'/%3E%3C/radialGradient%3E%3CradialGradient id='mhalo'%3E%3Cstop offset='0' stop-color='%23cfe0ff' stop-opacity='.5'/%3E%3Cstop offset='1' stop-color='%23cfe0ff' stop-opacity='0'/%3E%3C/radialGradient%3E%3Cg id='tree'%3E%3Crect x='-6' y='0' width='12' height='18' fill='%235a3a22'/%3E%3Cpolygon points='0,-95 -42,-30 42,-30' fill='%231f6b46'/%3E%3Cpolygon points='0,-65 -52,5 52,5' fill='%2325804f'/%3E%3Cpolygon points='0,-40 -62,32 62,32' fill='%232c9a5d'/%3E%3Cpath d='M-14,-78Q0,-70 14,-78L0,-95Z M-34,-40Q-10,-30 10,-44L0,-64L-24,-48Z M-46,0Q-20,8 6,-6L0,-30L-30,-12Z' fill='%23fff'/%3E%3C/g%3E%3Cg id='tl'%3E%3Ccircle cx='-18' cy='-12' r='4' fill='%23ff5a5a'/%3E%3Ccircle cx='14' cy='-25' r='4' fill='%23ffd34d'/%3E%3Ccircle cx='-4' cy='-48' r='3.5' fill='%236bd5ff'/%3E%3Ccircle cx='28' cy='10' r='4' fill='%23ff5a5a'/%3E%3Ccircle cx='-34' cy='18' r='4' fill='%23ffd34d'/%3E%3Ccircle cx='4' cy='12' r='4' fill='%236bd5ff'/%3E%3Ccircle cx='-8' cy='-70' r='3' fill='%23ff5a5a'/%3E%3Cpolygon points='0,-109 4,-102 12,-102 8,-95 12,-88 4,-88 0,-81 -4,-88 -12,-88 -8,-95 -12,-102 -4,-102' fill='%23ffd34d'/%3E%3C/g%3E%3Cg id='snowman'%3E%3Ccircle cx='0' cy='-20' r='30' fill='%23fff'/%3E%3Ccircle cx='0' cy='-62' r='22' fill='%23fff'/%3E%3Ccircle cx='0' cy='-92' r='16' fill='%23fff'/%3E%3Cpath d='M-26,-70L-48,-90M-44,-82L-52,-80M26,-70L50,-84M44,-79L50,-70' stroke='%235a3a22' stroke-width='3' stroke-linecap='round' fill='none'/%3E%3Ccircle cx='-5' cy='-95' r='2' fill='%23222'/%3E%3Ccircle cx='5' cy='-95' r='2' fill='%23222'/%3E%3Cpolygon points='0,-91 14,-88 0,-86' fill='%23ff8a1f'/%3E%3Crect x='-13' y='-120' width='26' height='22' fill='%23222'/%3E%3Crect x='-20' y='-103' width='40' height='6' rx='2' fill='%23222'/%3E%3Crect x='-13' y='-108' width='26' height='5' fill='%23c62828'/%3E%3Cpath d='M-17,-76Q0,-68 17,-76L17,-70Q0,-62 -17,-70Z' fill='%23c62828'/%3E%3Crect x='10' y='-72' width='7' height='20' fill='%23c62828'/%3E%3Ccircle cx='0' cy='-30' r='3' fill='%23222'/%3E%3Ccircle cx='0' cy='-18' r='3' fill='%23222'/%3E%3Ccircle cx='0' cy='-6' r='3' fill='%23222'/%3E%3C/g%3E%3Cg id='cabin'%3E%3Crect x='-55' y='-60' width='110' height='60' fill='%237a4a28'/%3E%3Cpath d='M-55 -50H55M-55 -38H55M-55 -26H55M-55 -14H55' stroke='%235a3519' stroke-width='2'/%3E%3Cpolygon points='-68,-56 0,-112 68,-56' fill='%238a2f2f'/%3E%3Cpolygon points='-64,-58 0,-108 64,-58 52,-58 0,-96 -52,-58' fill='%23fff'/%3E%3Crect x='30' y='-120' width='14' height='30' fill='%235a3a22'/%3E%3Crect x='28' y='-124' width='18' height='6' fill='%23fff'/%3E%3Crect x='-12' y='-34' width='24' height='34' fill='%234a2d1a'/%3E%3Ccircle cx='0' cy='-24' r='7' fill='none' stroke='%232c9a5d' stroke-width='3'/%3E%3Ccircle cx='0' cy='-31' r='2' fill='%23e53935'/%3E%3Ccircle cx='37' cy='-135' r='6' fill='%23fff' opacity='.7'/%3E%3Ccircle cx='43' cy='-148' r='8' fill='%23fff' opacity='.5'/%3E%3Ccircle cx='52' cy='-164' r='10' fill='%23fff' opacity='.3'/%3E%3C/g%3E%3Cg id='cabinL'%3E%3Crect x='-46' y='-48' width='24' height='20' fill='%23ffd36b'/%3E%3Crect x='22' y='-48' width='24' height='20' fill='%23ffd36b'/%3E%3Cpath d='M-34,-48V-28M-46,-38H-22M34,-48V-28M22,-38H46' stroke='%237a4a28' stroke-width='2'/%3E%3Cellipse cx='0' cy='-30' rx='80' ry='40' fill='url(%23halo)' opacity='.55'/%3E%3C/g%3E%3Cg id='stable'%3E%3Crect x='318' y='298' width='164' height='108' fill='%234a2d1a'/%3E%3Crect x='330' y='310' width='140' height='94' fill='%237a4a28'/%3E%3Cpath d='M345,310V404M365,310V404M385,310V404M405,310V404M425,310V404M445,310V404' stroke='%235a3519' stroke-width='2'/%3E%3Crect x='313' y='292' width='10' height='116' fill='%235a3a22'/%3E%3Crect x='477' y='292' width='10' height='116' fill='%235a3a22'/%3E%3Cpath d='M286,306L400,220L514,306L500,308L400,236L300,308Z' fill='%238a5a33'/%3E%3Cpath d='M292,300Q330,280 365,262Q400,244 400,230Q405,246 440,262Q475,282 508,300Q470,288 400,252Q330,288 292,300Z' fill='%23fff'/%3E%3Cpath d='M322,406Q400,390 478,406L478,414L322,414Z' fill='%23e3b94f'/%3E%3Cg transform='translate(336,386)'%3E%3Cellipse rx='18' ry='11' fill='%238d8d95'/%3E%3Ccircle cx='14' cy='-8' r='8' fill='%238d8d95'/%3E%3Cpath d='M12,-15L10,-26L16,-16Z M17,-14L20,-25L21,-12Z' fill='%238d8d95'/%3E%3Ccircle cx='17' cy='-9' r='1.4' fill='%23222'/%3E%3Cellipse cx='19' cy='-4' rx='4' ry='3' fill='%23c9c9d0'/%3E%3C/g%3E%3Cg transform='translate(470,388)'%3E%3Cellipse rx='20' ry='12' fill='%23a8734a'/%3E%3Ccircle cx='-15' cy='-8' r='9' fill='%23a8734a'/%3E%3Cpath d='M-22,-14Q-28,-18 -26,-24M-9,-14Q-5,-20 -8,-25' stroke='%23f1e6cf' stroke-width='3' fill='none' stroke-linecap='round'/%3E%3Cellipse cx='-19' cy='-4' rx='5' ry='3.5' fill='%23e8c9b0'/%3E%3Ccircle cx='-16' cy='-10' r='1.4' fill='%23222'/%3E%3C/g%3E%3Cpath d='M376,376L424,376L418,402L382,402Z' fill='%237a4a28'/%3E%3Cpath d='M382,378Q400,368 418,378L418,382L382,382Z' fill='%23e3b94f'/%3E%3Cellipse cx='400' cy='376' rx='14' ry='6' fill='%23fff'/%3E%3Ccircle cx='392' cy='372' r='6.5' fill='%23f4c9a0'/%3E%3Cg transform='translate(352,0)'%3E%3Cpath d='M0,404Q-14,372 0,350Q14,372 24,404Z' fill='%232a56b0'/%3E%3Cpath d='M-4,404Q-18,376 -2,354Q-10,380 -2,404Z' fill='%231d3f87'/%3E%3Cpath d='M-6,344Q4,326 14,344Q16,362 10,372L-2,372Q-8,360 -6,344Z' fill='%23f2f2f2'/%3E%3Ccircle cx='6' cy='344' r='8' fill='%23f4c9a0'/%3E%3Ccircle cx='5' cy='336' r='13' fill='none' stroke='%23ffe27a' stroke-width='1.6'/%3E%3Cpath d='M14,372Q24,376 36,378' stroke='%232a56b0' stroke-width='6' stroke-linecap='round' fill='none'/%3E%3C/g%3E%3Cg transform='translate(448,0)'%3E%3Cpath d='M-4,404Q-14,362 4,346Q22,362 20,404Z' fill='%23a8622e'/%3E%3Ccircle cx='8' cy='338' r='9' fill='%23f4c9a0'/%3E%3Cpath d='M0,336Q8,322 16,336Q16,326 8,324Q0,326 0,336Z' fill='%234a2d1a'/%3E%3Cpath d='M4,344Q8,354 14,344Z' fill='%234a2d1a'/%3E%3Ccircle cx='8' cy='334' r='13' fill='none' stroke='%23ffe27a' stroke-width='1.6'/%3E%3Cpath d='M34,300V406' stroke='%235a3a22' stroke-width='4' stroke-linecap='round'/%3E%3Cpath d='M18,368Q26,372 34,366' stroke='%23a8622e' stroke-width='6' stroke-linecap='round' fill='none'/%3E%3C/g%3E%3C/g%3E%3Cg id='stableL'%3E%3Cellipse cx='400' cy='360' rx='150' ry='90' fill='url(%23halo)' opacity='.75'/%3E%3Cellipse cx='400' cy='372' rx='30' ry='22' fill='url(%23halo)'/%3E%3C/g%3E%3Cg id='gift'%3E%3Crect width='30' height='22' fill='%23c62828'/%3E%3Crect x='12' width='6' height='22' fill='%23ffd34d'/%3E%3Crect y='8' width='30' height='5' fill='%23ffd34d'/%3E%3Cpath d='M15,0q-12,-12 -3,-12q6,0 3,12q-3,-12 3,-12q9,0 -3,12' fill='%23ffd34d'/%3E%3C/g%3E%3Cg id='gift2'%3E%3Crect width='26' height='20' fill='%232a8f5b'/%3E%3Crect x='10' width='6' height='20' fill='%23fff'/%3E%3Crect y='7' width='26' height='5' fill='%23fff'/%3E%3C/g%3E%3C/defs%3E%3Crect width='800' height='500' fill='url(%23sky)'/%3E%3Cg transform='translate(150,90) scale(1)' fill='%23fff' opacity='0.95'%3E%3Cellipse cx='0' cy='0' rx='60' ry='16'/%3E%3Cellipse cx='-28' cy='-10' rx='30' ry='16'/%3E%3Cellipse cx='10' cy='-18' rx='34' ry='20'/%3E%3Cellipse cx='38' cy='-6' rx='26' ry='13'/%3E%3C/g%3E%3Cg transform='translate(600,70) scale(0.8)' fill='%23ffe9e0' opacity='0.9'%3E%3Cellipse cx='0' cy='0' rx='60' ry='16'/%3E%3Cellipse cx='-28' cy='-10' rx='30' ry='16'/%3E%3Cellipse cx='10' cy='-18' rx='34' ry='20'/%3E%3Cellipse cx='38' cy='-6' rx='26' ry='13'/%3E%3C/g%3E%3Cg transform='translate(380,140) scale(0.7)' fill='%23fff' opacity='0.7'%3E%3Cellipse cx='0' cy='0' rx='60' ry='16'/%3E%3Cellipse cx='-28' cy='-10' rx='30' ry='16'/%3E%3Cellipse cx='10' cy='-18' rx='34' ry='20'/%3E%3Cellipse cx='38' cy='-6' rx='26' ry='13'/%3E%3C/g%3E%3Ccircle cx='463' cy='286' r='3' fill='%23fff' opacity='0.8'/%3E%3Ccircle cx='462' cy='260' r='1.8' fill='%23fff' opacity='0.8'/%3E%3Ccircle cx='189' cy='262' r='3' fill='%23fff' opacity='0.8'/%3E%3Ccircle cx='644' cy='314' r='1.8' fill='%23fff' opacity='0.8'/%3E%3Ccircle cx='96' cy='228' r='2.4' fill='%23fff' opacity='0.8'/%3E%3Ccircle cx='145' cy='46' r='1.2' fill='%23fff' opacity='0.8'/%3E%3Ccircle cx='609' cy='202' r='3' fill='%23fff' opacity='0.8'/%3E%3Ccircle cx='669' cy='378' r='1.8' fill='%23fff' opacity='0.8'/%3E%3Ccircle cx='638' cy='7' r='1.2' fill='%23fff' opacity='0.8'/%3E%3Ccircle cx='60' cy='18' r='1.8' fill='%23fff' opacity='0.8'/%3E%3Ccircle cx='247' cy='307' r='1.2' fill='%23fff' opacity='0.8'/%3E%3Ccircle cx='796' cy='237' r='2.4' fill='%23fff' opacity='0.8'/%3E%3Ccircle cx='451' cy='302' r='1.8' fill='%23fff' opacity='0.8'/%3E%3Ccircle cx='531' cy='119' r='2.4' fill='%23fff' opacity='0.8'/%3E%3Ccircle cx='511' cy='2' r='1.2' fill='%23fff' opacity='0.8'/%3E%3Ccircle cx='468' cy='335' r='2.4' fill='%23fff' opacity='0.8'/%3E%3Ccircle cx='416' cy='282' r='1.2' fill='%23fff' opacity='0.8'/%3E%3Ccircle cx='724' cy='130' r='2.4' fill='%23fff' opacity='0.8'/%3E%3Ccircle cx='776' cy='117' r='2.4' fill='%23fff' opacity='0.8'/%3E%3Ccircle cx='30' cy='35' r='1.2' fill='%23fff' opacity='0.8'/%3E%3Ccircle cx='410' cy='55' r='2.4' fill='%23fff' opacity='0.8'/%3E%3Ccircle cx='395' cy='34' r='1.2' fill='%23fff' opacity='0.8'/%3E%3Ccircle cx='701' cy='0' r='1.8' fill='%23fff' opacity='0.8'/%3E%3Ccircle cx='214' cy='26' r='3' fill='%23fff' opacity='0.8'/%3E%3Ccircle cx='384' cy='362' r='3' fill='%23fff' opacity='0.8'/%3E%3Ccircle cx='429' cy='37' r='1.8' fill='%23fff' opacity='0.8'/%3E%3Ccircle cx='797' cy='345' r='2.4' fill='%23fff' opacity='0.8'/%3E%3Ccircle cx='344' cy='44' r='2.4' fill='%23fff' opacity='0.8'/%3E%3Ccircle cx='340' cy='7' r='3' fill='%23fff' opacity='0.8'/%3E%3Ccircle cx='776' cy='60' r='1.8' fill='%23fff' opacity='0.8'/%3E%3Ccircle cx='252' cy='361' r='1.2' fill='%23fff' opacity='0.8'/%3E%3Ccircle cx='11' cy='30' r='3' fill='%23fff' opacity='0.8'/%3E%3Ccircle cx='498' cy='90' r='1.8' fill='%23fff' opacity='0.8'/%3E%3Ccircle cx='458' cy='260' r='1.8' fill='%23fff' opacity='0.8'/%3E%3Ccircle cx='749' cy='67' r='3' fill='%23fff' opacity='0.8'/%3E%3Ccircle cx='659' cy='196' r='1.2' fill='%23fff' opacity='0.8'/%3E%3Ccircle cx='404' cy='215' r='1.8' fill='%23fff' opacity='0.8'/%3E%3Ccircle cx='0' cy='138' r='2.4' fill='%23fff' opacity='0.8'/%3E%3Ccircle cx='20' cy='107' r='1.8' fill='%23fff' opacity='0.8'/%3E%3Ccircle cx='403' cy='308' r='1.2' fill='%23fff' opacity='0.8'/%3E%3Ccircle cx='560' cy='330' r='136' fill='url(%23halo)' opacity='.8'/%3E%3Ccircle cx='560' cy='330' r='34' fill='%23fff3c2'/%3E%3Cpath d='M0,330Q120,290 250,325T520,320T800,310L800,500L0,500Z' fill='%23b9a9cf'/%3E%3Cpath d='M0,360Q160,320 330,355T620,345T800,350L800,500L0,500Z' fill='%23d9cde6'/%3E%3Cpath d='M0,405Q200,380 400,402T800,392L800,500L0,500Z' fill='url(%23gr)'/%3E%3Cuse href='%23tree' transform='translate(60,395) scale(1.1)'/%3E%3Cuse href='%23tree' transform='translate(215,400) scale(0.8)'/%3E%3Cuse href='%23tree' transform='translate(725,392) scale(1.05)'/%3E%3Cuse href='%23tree' transform='translate(615,402) scale(0.7)'/%3E%3Cuse href='%23cabin' transform='translate(330,412) scale(1.05)'/%3E%3Cuse href='%23snowman' transform='translate(130,452) scale(0.9)'/%3E%3Cuse href='%23snowman' transform='translate(180,460) scale(0.6)'/%3E%3Cuse href='%23gift' transform='translate(470,436) scale(1)'/%3E%3Cuse href='%23gift2' transform='translate(505,442) scale(1)'/%3E%3Crect width='800' height='500' fill='%23ffb68a' fill-opacity='0.06'/%3E%3Cuse href='%23tl' transform='translate(60,395) scale(1.1)'/%3E%3Cuse href='%23tl' transform='translate(215,400) scale(0.8)'/%3E%3Cuse href='%23tl' transform='translate(725,392) scale(1.05)'/%3E%3Cuse href='%23tl' transform='translate(615,402) scale(0.7)'/%3E%3Cuse href='%23cabinL' transform='translate(330,412) scale(1.05)'/%3E%3C/svg%3E") center bottom / cover no-repeat,
                linear-gradient(180deg, #14234F 0%, #E9A86B 100%);

            /* Día: nacimiento bajo el sol */
            --bg-light-noon:
                url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 800 500' preserveAspectRatio='xMidYMax slice'%3E%3Cdefs%3E%3ClinearGradient id='sky' x1='0' y1='0' x2='0' y2='1'%3E%3Cstop offset='0' stop-color='%233f9ee0'/%3E%3Cstop offset='0.4' stop-color='%237cc6ee'/%3E%3Cstop offset='0.75' stop-color='%23c8e8f7'/%3E%3Cstop offset='1' stop-color='%23eaf5ef'/%3E%3C/linearGradient%3E%3ClinearGradient id='gr' x1='0' y1='0' x2='0' y2='1'%3E%3Cstop offset='0' stop-color='%23ffffff'/%3E%3Cstop offset='1' stop-color='%23d6e6f5'/%3E%3C/linearGradient%3E%3CradialGradient id='halo'%3E%3Cstop offset='0' stop-color='%23ffe9a0' stop-opacity='.95'/%3E%3Cstop offset='1' stop-color='%23ffd36b' stop-opacity='0'/%3E%3C/radialGradient%3E%3CradialGradient id='mhalo'%3E%3Cstop offset='0' stop-color='%23cfe0ff' stop-opacity='.5'/%3E%3Cstop offset='1' stop-color='%23cfe0ff' stop-opacity='0'/%3E%3C/radialGradient%3E%3Cg id='tree'%3E%3Crect x='-6' y='0' width='12' height='18' fill='%235a3a22'/%3E%3Cpolygon points='0,-95 -42,-30 42,-30' fill='%231f6b46'/%3E%3Cpolygon points='0,-65 -52,5 52,5' fill='%2325804f'/%3E%3Cpolygon points='0,-40 -62,32 62,32' fill='%232c9a5d'/%3E%3Cpath d='M-14,-78Q0,-70 14,-78L0,-95Z M-34,-40Q-10,-30 10,-44L0,-64L-24,-48Z M-46,0Q-20,8 6,-6L0,-30L-30,-12Z' fill='%23fff'/%3E%3C/g%3E%3Cg id='tl'%3E%3Ccircle cx='-18' cy='-12' r='4' fill='%23ff5a5a'/%3E%3Ccircle cx='14' cy='-25' r='4' fill='%23ffd34d'/%3E%3Ccircle cx='-4' cy='-48' r='3.5' fill='%236bd5ff'/%3E%3Ccircle cx='28' cy='10' r='4' fill='%23ff5a5a'/%3E%3Ccircle cx='-34' cy='18' r='4' fill='%23ffd34d'/%3E%3Ccircle cx='4' cy='12' r='4' fill='%236bd5ff'/%3E%3Ccircle cx='-8' cy='-70' r='3' fill='%23ff5a5a'/%3E%3Cpolygon points='0,-109 4,-102 12,-102 8,-95 12,-88 4,-88 0,-81 -4,-88 -12,-88 -8,-95 -12,-102 -4,-102' fill='%23ffd34d'/%3E%3C/g%3E%3Cg id='snowman'%3E%3Ccircle cx='0' cy='-20' r='30' fill='%23fff'/%3E%3Ccircle cx='0' cy='-62' r='22' fill='%23fff'/%3E%3Ccircle cx='0' cy='-92' r='16' fill='%23fff'/%3E%3Cpath d='M-26,-70L-48,-90M-44,-82L-52,-80M26,-70L50,-84M44,-79L50,-70' stroke='%235a3a22' stroke-width='3' stroke-linecap='round' fill='none'/%3E%3Ccircle cx='-5' cy='-95' r='2' fill='%23222'/%3E%3Ccircle cx='5' cy='-95' r='2' fill='%23222'/%3E%3Cpolygon points='0,-91 14,-88 0,-86' fill='%23ff8a1f'/%3E%3Crect x='-13' y='-120' width='26' height='22' fill='%23222'/%3E%3Crect x='-20' y='-103' width='40' height='6' rx='2' fill='%23222'/%3E%3Crect x='-13' y='-108' width='26' height='5' fill='%23c62828'/%3E%3Cpath d='M-17,-76Q0,-68 17,-76L17,-70Q0,-62 -17,-70Z' fill='%23c62828'/%3E%3Crect x='10' y='-72' width='7' height='20' fill='%23c62828'/%3E%3Ccircle cx='0' cy='-30' r='3' fill='%23222'/%3E%3Ccircle cx='0' cy='-18' r='3' fill='%23222'/%3E%3Ccircle cx='0' cy='-6' r='3' fill='%23222'/%3E%3C/g%3E%3Cg id='cabin'%3E%3Crect x='-55' y='-60' width='110' height='60' fill='%237a4a28'/%3E%3Cpath d='M-55 -50H55M-55 -38H55M-55 -26H55M-55 -14H55' stroke='%235a3519' stroke-width='2'/%3E%3Cpolygon points='-68,-56 0,-112 68,-56' fill='%238a2f2f'/%3E%3Cpolygon points='-64,-58 0,-108 64,-58 52,-58 0,-96 -52,-58' fill='%23fff'/%3E%3Crect x='30' y='-120' width='14' height='30' fill='%235a3a22'/%3E%3Crect x='28' y='-124' width='18' height='6' fill='%23fff'/%3E%3Crect x='-12' y='-34' width='24' height='34' fill='%234a2d1a'/%3E%3Ccircle cx='0' cy='-24' r='7' fill='none' stroke='%232c9a5d' stroke-width='3'/%3E%3Ccircle cx='0' cy='-31' r='2' fill='%23e53935'/%3E%3Ccircle cx='37' cy='-135' r='6' fill='%23fff' opacity='.7'/%3E%3Ccircle cx='43' cy='-148' r='8' fill='%23fff' opacity='.5'/%3E%3Ccircle cx='52' cy='-164' r='10' fill='%23fff' opacity='.3'/%3E%3C/g%3E%3Cg id='cabinL'%3E%3Crect x='-46' y='-48' width='24' height='20' fill='%23ffd36b'/%3E%3Crect x='22' y='-48' width='24' height='20' fill='%23ffd36b'/%3E%3Cpath d='M-34,-48V-28M-46,-38H-22M34,-48V-28M22,-38H46' stroke='%237a4a28' stroke-width='2'/%3E%3Cellipse cx='0' cy='-30' rx='80' ry='40' fill='url(%23halo)' opacity='.55'/%3E%3C/g%3E%3Cg id='stable'%3E%3Crect x='318' y='298' width='164' height='108' fill='%234a2d1a'/%3E%3Crect x='330' y='310' width='140' height='94' fill='%237a4a28'/%3E%3Cpath d='M345,310V404M365,310V404M385,310V404M405,310V404M425,310V404M445,310V404' stroke='%235a3519' stroke-width='2'/%3E%3Crect x='313' y='292' width='10' height='116' fill='%235a3a22'/%3E%3Crect x='477' y='292' width='10' height='116' fill='%235a3a22'/%3E%3Cpath d='M286,306L400,220L514,306L500,308L400,236L300,308Z' fill='%238a5a33'/%3E%3Cpath d='M292,300Q330,280 365,262Q400,244 400,230Q405,246 440,262Q475,282 508,300Q470,288 400,252Q330,288 292,300Z' fill='%23fff'/%3E%3Cpath d='M322,406Q400,390 478,406L478,414L322,414Z' fill='%23e3b94f'/%3E%3Cg transform='translate(336,386)'%3E%3Cellipse rx='18' ry='11' fill='%238d8d95'/%3E%3Ccircle cx='14' cy='-8' r='8' fill='%238d8d95'/%3E%3Cpath d='M12,-15L10,-26L16,-16Z M17,-14L20,-25L21,-12Z' fill='%238d8d95'/%3E%3Ccircle cx='17' cy='-9' r='1.4' fill='%23222'/%3E%3Cellipse cx='19' cy='-4' rx='4' ry='3' fill='%23c9c9d0'/%3E%3C/g%3E%3Cg transform='translate(470,388)'%3E%3Cellipse rx='20' ry='12' fill='%23a8734a'/%3E%3Ccircle cx='-15' cy='-8' r='9' fill='%23a8734a'/%3E%3Cpath d='M-22,-14Q-28,-18 -26,-24M-9,-14Q-5,-20 -8,-25' stroke='%23f1e6cf' stroke-width='3' fill='none' stroke-linecap='round'/%3E%3Cellipse cx='-19' cy='-4' rx='5' ry='3.5' fill='%23e8c9b0'/%3E%3Ccircle cx='-16' cy='-10' r='1.4' fill='%23222'/%3E%3C/g%3E%3Cpath d='M376,376L424,376L418,402L382,402Z' fill='%237a4a28'/%3E%3Cpath d='M382,378Q400,368 418,378L418,382L382,382Z' fill='%23e3b94f'/%3E%3Cellipse cx='400' cy='376' rx='14' ry='6' fill='%23fff'/%3E%3Ccircle cx='392' cy='372' r='6.5' fill='%23f4c9a0'/%3E%3Cg transform='translate(352,0)'%3E%3Cpath d='M0,404Q-14,372 0,350Q14,372 24,404Z' fill='%232a56b0'/%3E%3Cpath d='M-4,404Q-18,376 -2,354Q-10,380 -2,404Z' fill='%231d3f87'/%3E%3Cpath d='M-6,344Q4,326 14,344Q16,362 10,372L-2,372Q-8,360 -6,344Z' fill='%23f2f2f2'/%3E%3Ccircle cx='6' cy='344' r='8' fill='%23f4c9a0'/%3E%3Ccircle cx='5' cy='336' r='13' fill='none' stroke='%23ffe27a' stroke-width='1.6'/%3E%3Cpath d='M14,372Q24,376 36,378' stroke='%232a56b0' stroke-width='6' stroke-linecap='round' fill='none'/%3E%3C/g%3E%3Cg transform='translate(448,0)'%3E%3Cpath d='M-4,404Q-14,362 4,346Q22,362 20,404Z' fill='%23a8622e'/%3E%3Ccircle cx='8' cy='338' r='9' fill='%23f4c9a0'/%3E%3Cpath d='M0,336Q8,322 16,336Q16,326 8,324Q0,326 0,336Z' fill='%234a2d1a'/%3E%3Cpath d='M4,344Q8,354 14,344Z' fill='%234a2d1a'/%3E%3Ccircle cx='8' cy='334' r='13' fill='none' stroke='%23ffe27a' stroke-width='1.6'/%3E%3Cpath d='M34,300V406' stroke='%235a3a22' stroke-width='4' stroke-linecap='round'/%3E%3Cpath d='M18,368Q26,372 34,366' stroke='%23a8622e' stroke-width='6' stroke-linecap='round' fill='none'/%3E%3C/g%3E%3C/g%3E%3Cg id='stableL'%3E%3Cellipse cx='400' cy='360' rx='150' ry='90' fill='url(%23halo)' opacity='.75'/%3E%3Cellipse cx='400' cy='372' rx='30' ry='22' fill='url(%23halo)'/%3E%3C/g%3E%3Cg id='gift'%3E%3Crect width='30' height='22' fill='%23c62828'/%3E%3Crect x='12' width='6' height='22' fill='%23ffd34d'/%3E%3Crect y='8' width='30' height='5' fill='%23ffd34d'/%3E%3Cpath d='M15,0q-12,-12 -3,-12q6,0 3,12q-3,-12 3,-12q9,0 -3,12' fill='%23ffd34d'/%3E%3C/g%3E%3Cg id='gift2'%3E%3Crect width='26' height='20' fill='%232a8f5b'/%3E%3Crect x='10' width='6' height='20' fill='%23fff'/%3E%3Crect y='7' width='26' height='5' fill='%23fff'/%3E%3C/g%3E%3C/defs%3E%3Crect width='800' height='500' fill='url(%23sky)'/%3E%3Cg transform='translate(330,70) scale(1.2)' fill='%23fff' opacity='0.9'%3E%3Cellipse cx='0' cy='0' rx='60' ry='16'/%3E%3Cellipse cx='-28' cy='-10' rx='30' ry='16'/%3E%3Cellipse cx='10' cy='-18' rx='34' ry='20'/%3E%3Cellipse cx='38' cy='-6' rx='26' ry='13'/%3E%3C/g%3E%3Cg transform='translate(560,120) scale(0.9)' fill='%23fff' opacity='0.9'%3E%3Cellipse cx='0' cy='0' rx='60' ry='16'/%3E%3Cellipse cx='-28' cy='-10' rx='30' ry='16'/%3E%3Cellipse cx='10' cy='-18' rx='34' ry='20'/%3E%3Cellipse cx='38' cy='-6' rx='26' ry='13'/%3E%3C/g%3E%3Cg transform='translate(80,170) scale(0.7)' fill='%23fff' opacity='0.8'%3E%3Cellipse cx='0' cy='0' rx='60' ry='16'/%3E%3Cellipse cx='-28' cy='-10' rx='30' ry='16'/%3E%3Cellipse cx='10' cy='-18' rx='34' ry='20'/%3E%3Cellipse cx='38' cy='-6' rx='26' ry='13'/%3E%3C/g%3E%3Ccircle cx='485' cy='137' r='2.4' fill='%23fff' opacity='0.9'/%3E%3Ccircle cx='146' cy='195' r='1.2' fill='%23fff' opacity='0.9'/%3E%3Ccircle cx='383' cy='247' r='2.4' fill='%23fff' opacity='0.9'/%3E%3Ccircle cx='658' cy='235' r='1.8' fill='%23fff' opacity='0.9'/%3E%3Ccircle cx='571' cy='0' r='1.8' fill='%23fff' opacity='0.9'/%3E%3Ccircle cx='450' cy='188' r='1.8' fill='%23fff' opacity='0.9'/%3E%3Ccircle cx='347' cy='107' r='1.2' fill='%23fff' opacity='0.9'/%3E%3Ccircle cx='590' cy='102' r='1.2' fill='%23fff' opacity='0.9'/%3E%3Ccircle cx='525' cy='351' r='2.4' fill='%23fff' opacity='0.9'/%3E%3Ccircle cx='697' cy='207' r='1.2' fill='%23fff' opacity='0.9'/%3E%3Ccircle cx='19' cy='31' r='1.8' fill='%23fff' opacity='0.9'/%3E%3Ccircle cx='93' cy='216' r='3' fill='%23fff' opacity='0.9'/%3E%3Ccircle cx='115' cy='338' r='3' fill='%23fff' opacity='0.9'/%3E%3Ccircle cx='138' cy='276' r='2.4' fill='%23fff' opacity='0.9'/%3E%3Ccircle cx='636' cy='285' r='1.8' fill='%23fff' opacity='0.9'/%3E%3Ccircle cx='713' cy='26' r='1.8' fill='%23fff' opacity='0.9'/%3E%3Ccircle cx='519' cy='42' r='3' fill='%23fff' opacity='0.9'/%3E%3Ccircle cx='622' cy='214' r='3' fill='%23fff' opacity='0.9'/%3E%3Ccircle cx='488' cy='311' r='3' fill='%23fff' opacity='0.9'/%3E%3Ccircle cx='554' cy='15' r='1.2' fill='%23fff' opacity='0.9'/%3E%3Ccircle cx='196' cy='339' r='2.4' fill='%23fff' opacity='0.9'/%3E%3Ccircle cx='364' cy='185' r='3' fill='%23fff' opacity='0.9'/%3E%3Ccircle cx='689' cy='158' r='1.2' fill='%23fff' opacity='0.9'/%3E%3Ccircle cx='258' cy='120' r='2.4' fill='%23fff' opacity='0.9'/%3E%3Ccircle cx='372' cy='190' r='1.8' fill='%23fff' opacity='0.9'/%3E%3Ccircle cx='28' cy='195' r='3' fill='%23fff' opacity='0.9'/%3E%3Ccircle cx='33' cy='265' r='1.2' fill='%23fff' opacity='0.9'/%3E%3Ccircle cx='227' cy='345' r='3' fill='%23fff' opacity='0.9'/%3E%3Ccircle cx='44' cy='198' r='1.8' fill='%23fff' opacity='0.9'/%3E%3Ccircle cx='790' cy='310' r='1.2' fill='%23fff' opacity='0.9'/%3E%3Ccircle cx='771' cy='281' r='1.8' fill='%23fff' opacity='0.9'/%3E%3Ccircle cx='182' cy='39' r='2.4' fill='%23fff' opacity='0.9'/%3E%3Ccircle cx='37' cy='221' r='2.4' fill='%23fff' opacity='0.9'/%3E%3Ccircle cx='510' cy='176' r='1.2' fill='%23fff' opacity='0.9'/%3E%3Ccircle cx='526' cy='235' r='2.4' fill='%23fff' opacity='0.9'/%3E%3Ccircle cx='213' cy='173' r='2.4' fill='%23fff' opacity='0.9'/%3E%3Ccircle cx='466' cy='243' r='3' fill='%23fff' opacity='0.9'/%3E%3Ccircle cx='245' cy='85' r='3' fill='%23fff' opacity='0.9'/%3E%3Ccircle cx='562' cy='186' r='1.8' fill='%23fff' opacity='0.9'/%3E%3Ccircle cx='193' cy='112' r='1.2' fill='%23fff' opacity='0.9'/%3E%3Ccircle cx='274' cy='174' r='1.8' fill='%23fff' opacity='0.9'/%3E%3Ccircle cx='247' cy='4' r='1.2' fill='%23fff' opacity='0.9'/%3E%3Ccircle cx='253' cy='316' r='1.2' fill='%23fff' opacity='0.9'/%3E%3Ccircle cx='369' cy='279' r='1.8' fill='%23fff' opacity='0.9'/%3E%3Ccircle cx='24' cy='165' r='2.4' fill='%23fff' opacity='0.9'/%3E%3Ccircle cx='654' cy='60' r='1.8' fill='%23fff' opacity='0.9'/%3E%3Ccircle cx='539' cy='257' r='3' fill='%23fff' opacity='0.9'/%3E%3Ccircle cx='501' cy='321' r='2.4' fill='%23fff' opacity='0.9'/%3E%3Ccircle cx='268' cy='350' r='3' fill='%23fff' opacity='0.9'/%3E%3Ccircle cx='756' cy='83' r='3' fill='%23fff' opacity='0.9'/%3E%3Ccircle cx='130' cy='85' r='128' fill='url(%23halo)' opacity='.8'/%3E%3Ccircle cx='130' cy='85' r='32' fill='%23fff5b8'/%3E%3Cpath d='M0,330Q120,290 250,325T520,320T800,310L800,500L0,500Z' fill='%239fc4e6'/%3E%3Cpath d='M0,360Q160,320 330,355T620,345T800,350L800,500L0,500Z' fill='%23c3dbf0'/%3E%3Cpath d='M0,405Q200,380 400,402T800,392L800,500L0,500Z' fill='url(%23gr)'/%3E%3Cuse href='%23tree' transform='translate(60,395) scale(1.1)'/%3E%3Cuse href='%23tree' transform='translate(215,400) scale(0.8)'/%3E%3Cuse href='%23tree' transform='translate(745,392) scale(1.05)'/%3E%3Cuse href='%23tree' transform='translate(660,402) scale(0.75)'/%3E%3Cuse href='%23stable'/%3E%3Cuse href='%23snowman' transform='translate(140,450) scale(0.85)'/%3E%3Cuse href='%23snowman' transform='translate(700,456) scale(0.7)'/%3E%3Cuse href='%23gift' transform='translate(250,436) scale(1)'/%3E%3Cuse href='%23gift2' transform='translate(560,440) scale(1)'/%3E%3Cuse href='%23tl' transform='translate(60,395) scale(1.1)'/%3E%3Cuse href='%23tl' transform='translate(215,400) scale(0.8)'/%3E%3Cuse href='%23tl' transform='translate(745,392) scale(1.05)'/%3E%3Cuse href='%23tl' transform='translate(660,402) scale(0.75)'/%3E%3Cuse href='%23stableL' opacity='.55'/%3E%3C/svg%3E") center bottom / cover no-repeat,
                linear-gradient(180deg, #14234F 0%, #E9A86B 100%);

            /* Crepúsculo: trineo de Santa y cabaña */
            --bg-light-night:
                url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 800 500' preserveAspectRatio='xMidYMax slice'%3E%3Cdefs%3E%3ClinearGradient id='sky' x1='0' y1='0' x2='0' y2='1'%3E%3Cstop offset='0' stop-color='%234f60a6'/%3E%3Cstop offset='0.4' stop-color='%238e82b8'/%3E%3Cstop offset='0.75' stop-color='%23e3a0a8'/%3E%3Cstop offset='1' stop-color='%23f6c694'/%3E%3C/linearGradient%3E%3ClinearGradient id='gr' x1='0' y1='0' x2='0' y2='1'%3E%3Cstop offset='0' stop-color='%23fbf3ff'/%3E%3Cstop offset='1' stop-color='%23cfc1e2'/%3E%3C/linearGradient%3E%3CradialGradient id='halo'%3E%3Cstop offset='0' stop-color='%23ffe9a0' stop-opacity='.95'/%3E%3Cstop offset='1' stop-color='%23ffd36b' stop-opacity='0'/%3E%3C/radialGradient%3E%3CradialGradient id='mhalo'%3E%3Cstop offset='0' stop-color='%23cfe0ff' stop-opacity='.5'/%3E%3Cstop offset='1' stop-color='%23cfe0ff' stop-opacity='0'/%3E%3C/radialGradient%3E%3Cg id='tree'%3E%3Crect x='-6' y='0' width='12' height='18' fill='%235a3a22'/%3E%3Cpolygon points='0,-95 -42,-30 42,-30' fill='%231f6b46'/%3E%3Cpolygon points='0,-65 -52,5 52,5' fill='%2325804f'/%3E%3Cpolygon points='0,-40 -62,32 62,32' fill='%232c9a5d'/%3E%3Cpath d='M-14,-78Q0,-70 14,-78L0,-95Z M-34,-40Q-10,-30 10,-44L0,-64L-24,-48Z M-46,0Q-20,8 6,-6L0,-30L-30,-12Z' fill='%23fff'/%3E%3C/g%3E%3Cg id='tl'%3E%3Ccircle cx='-18' cy='-12' r='4' fill='%23ff5a5a'/%3E%3Ccircle cx='14' cy='-25' r='4' fill='%23ffd34d'/%3E%3Ccircle cx='-4' cy='-48' r='3.5' fill='%236bd5ff'/%3E%3Ccircle cx='28' cy='10' r='4' fill='%23ff5a5a'/%3E%3Ccircle cx='-34' cy='18' r='4' fill='%23ffd34d'/%3E%3Ccircle cx='4' cy='12' r='4' fill='%236bd5ff'/%3E%3Ccircle cx='-8' cy='-70' r='3' fill='%23ff5a5a'/%3E%3Cpolygon points='0,-109 4,-102 12,-102 8,-95 12,-88 4,-88 0,-81 -4,-88 -12,-88 -8,-95 -12,-102 -4,-102' fill='%23ffd34d'/%3E%3C/g%3E%3Cg id='snowman'%3E%3Ccircle cx='0' cy='-20' r='30' fill='%23fff'/%3E%3Ccircle cx='0' cy='-62' r='22' fill='%23fff'/%3E%3Ccircle cx='0' cy='-92' r='16' fill='%23fff'/%3E%3Cpath d='M-26,-70L-48,-90M-44,-82L-52,-80M26,-70L50,-84M44,-79L50,-70' stroke='%235a3a22' stroke-width='3' stroke-linecap='round' fill='none'/%3E%3Ccircle cx='-5' cy='-95' r='2' fill='%23222'/%3E%3Ccircle cx='5' cy='-95' r='2' fill='%23222'/%3E%3Cpolygon points='0,-91 14,-88 0,-86' fill='%23ff8a1f'/%3E%3Crect x='-13' y='-120' width='26' height='22' fill='%23222'/%3E%3Crect x='-20' y='-103' width='40' height='6' rx='2' fill='%23222'/%3E%3Crect x='-13' y='-108' width='26' height='5' fill='%23c62828'/%3E%3Cpath d='M-17,-76Q0,-68 17,-76L17,-70Q0,-62 -17,-70Z' fill='%23c62828'/%3E%3Crect x='10' y='-72' width='7' height='20' fill='%23c62828'/%3E%3Ccircle cx='0' cy='-30' r='3' fill='%23222'/%3E%3Ccircle cx='0' cy='-18' r='3' fill='%23222'/%3E%3Ccircle cx='0' cy='-6' r='3' fill='%23222'/%3E%3C/g%3E%3Cg id='cabin'%3E%3Crect x='-55' y='-60' width='110' height='60' fill='%237a4a28'/%3E%3Cpath d='M-55 -50H55M-55 -38H55M-55 -26H55M-55 -14H55' stroke='%235a3519' stroke-width='2'/%3E%3Cpolygon points='-68,-56 0,-112 68,-56' fill='%238a2f2f'/%3E%3Cpolygon points='-64,-58 0,-108 64,-58 52,-58 0,-96 -52,-58' fill='%23fff'/%3E%3Crect x='30' y='-120' width='14' height='30' fill='%235a3a22'/%3E%3Crect x='28' y='-124' width='18' height='6' fill='%23fff'/%3E%3Crect x='-12' y='-34' width='24' height='34' fill='%234a2d1a'/%3E%3Ccircle cx='0' cy='-24' r='7' fill='none' stroke='%232c9a5d' stroke-width='3'/%3E%3Ccircle cx='0' cy='-31' r='2' fill='%23e53935'/%3E%3Ccircle cx='37' cy='-135' r='6' fill='%23fff' opacity='.7'/%3E%3Ccircle cx='43' cy='-148' r='8' fill='%23fff' opacity='.5'/%3E%3Ccircle cx='52' cy='-164' r='10' fill='%23fff' opacity='.3'/%3E%3C/g%3E%3Cg id='cabinL'%3E%3Crect x='-46' y='-48' width='24' height='20' fill='%23ffd36b'/%3E%3Crect x='22' y='-48' width='24' height='20' fill='%23ffd36b'/%3E%3Cpath d='M-34,-48V-28M-46,-38H-22M34,-48V-28M22,-38H46' stroke='%237a4a28' stroke-width='2'/%3E%3Cellipse cx='0' cy='-30' rx='80' ry='40' fill='url(%23halo)' opacity='.55'/%3E%3C/g%3E%3Cg id='stable'%3E%3Crect x='318' y='298' width='164' height='108' fill='%234a2d1a'/%3E%3Crect x='330' y='310' width='140' height='94' fill='%237a4a28'/%3E%3Cpath d='M345,310V404M365,310V404M385,310V404M405,310V404M425,310V404M445,310V404' stroke='%235a3519' stroke-width='2'/%3E%3Crect x='313' y='292' width='10' height='116' fill='%235a3a22'/%3E%3Crect x='477' y='292' width='10' height='116' fill='%235a3a22'/%3E%3Cpath d='M286,306L400,220L514,306L500,308L400,236L300,308Z' fill='%238a5a33'/%3E%3Cpath d='M292,300Q330,280 365,262Q400,244 400,230Q405,246 440,262Q475,282 508,300Q470,288 400,252Q330,288 292,300Z' fill='%23fff'/%3E%3Cpath d='M322,406Q400,390 478,406L478,414L322,414Z' fill='%23e3b94f'/%3E%3Cg transform='translate(336,386)'%3E%3Cellipse rx='18' ry='11' fill='%238d8d95'/%3E%3Ccircle cx='14' cy='-8' r='8' fill='%238d8d95'/%3E%3Cpath d='M12,-15L10,-26L16,-16Z M17,-14L20,-25L21,-12Z' fill='%238d8d95'/%3E%3Ccircle cx='17' cy='-9' r='1.4' fill='%23222'/%3E%3Cellipse cx='19' cy='-4' rx='4' ry='3' fill='%23c9c9d0'/%3E%3C/g%3E%3Cg transform='translate(470,388)'%3E%3Cellipse rx='20' ry='12' fill='%23a8734a'/%3E%3Ccircle cx='-15' cy='-8' r='9' fill='%23a8734a'/%3E%3Cpath d='M-22,-14Q-28,-18 -26,-24M-9,-14Q-5,-20 -8,-25' stroke='%23f1e6cf' stroke-width='3' fill='none' stroke-linecap='round'/%3E%3Cellipse cx='-19' cy='-4' rx='5' ry='3.5' fill='%23e8c9b0'/%3E%3Ccircle cx='-16' cy='-10' r='1.4' fill='%23222'/%3E%3C/g%3E%3Cpath d='M376,376L424,376L418,402L382,402Z' fill='%237a4a28'/%3E%3Cpath d='M382,378Q400,368 418,378L418,382L382,382Z' fill='%23e3b94f'/%3E%3Cellipse cx='400' cy='376' rx='14' ry='6' fill='%23fff'/%3E%3Ccircle cx='392' cy='372' r='6.5' fill='%23f4c9a0'/%3E%3Cg transform='translate(352,0)'%3E%3Cpath d='M0,404Q-14,372 0,350Q14,372 24,404Z' fill='%232a56b0'/%3E%3Cpath d='M-4,404Q-18,376 -2,354Q-10,380 -2,404Z' fill='%231d3f87'/%3E%3Cpath d='M-6,344Q4,326 14,344Q16,362 10,372L-2,372Q-8,360 -6,344Z' fill='%23f2f2f2'/%3E%3Ccircle cx='6' cy='344' r='8' fill='%23f4c9a0'/%3E%3Ccircle cx='5' cy='336' r='13' fill='none' stroke='%23ffe27a' stroke-width='1.6'/%3E%3Cpath d='M14,372Q24,376 36,378' stroke='%232a56b0' stroke-width='6' stroke-linecap='round' fill='none'/%3E%3C/g%3E%3Cg transform='translate(448,0)'%3E%3Cpath d='M-4,404Q-14,362 4,346Q22,362 20,404Z' fill='%23a8622e'/%3E%3Ccircle cx='8' cy='338' r='9' fill='%23f4c9a0'/%3E%3Cpath d='M0,336Q8,322 16,336Q16,326 8,324Q0,326 0,336Z' fill='%234a2d1a'/%3E%3Cpath d='M4,344Q8,354 14,344Z' fill='%234a2d1a'/%3E%3Ccircle cx='8' cy='334' r='13' fill='none' stroke='%23ffe27a' stroke-width='1.6'/%3E%3Cpath d='M34,300V406' stroke='%235a3a22' stroke-width='4' stroke-linecap='round'/%3E%3Cpath d='M18,368Q26,372 34,366' stroke='%23a8622e' stroke-width='6' stroke-linecap='round' fill='none'/%3E%3C/g%3E%3C/g%3E%3Cg id='stableL'%3E%3Cellipse cx='400' cy='360' rx='150' ry='90' fill='url(%23halo)' opacity='.75'/%3E%3Cellipse cx='400' cy='372' rx='30' ry='22' fill='url(%23halo)'/%3E%3C/g%3E%3Cg id='gift'%3E%3Crect width='30' height='22' fill='%23c62828'/%3E%3Crect x='12' width='6' height='22' fill='%23ffd34d'/%3E%3Crect y='8' width='30' height='5' fill='%23ffd34d'/%3E%3Cpath d='M15,0q-12,-12 -3,-12q6,0 3,12q-3,-12 3,-12q9,0 -3,12' fill='%23ffd34d'/%3E%3C/g%3E%3Cg id='gift2'%3E%3Crect width='26' height='20' fill='%232a8f5b'/%3E%3Crect x='10' width='6' height='20' fill='%23fff'/%3E%3Crect y='7' width='26' height='5' fill='%23fff'/%3E%3C/g%3E%3C/defs%3E%3Crect width='800' height='500' fill='url(%23sky)'/%3E%3Ccircle cx='265' cy='148' r='1.8' fill='%23fff' opacity='0.8'/%3E%3Ccircle cx='667' cy='118' r='1.8' fill='%23fff' opacity='0.8'/%3E%3Ccircle cx='230' cy='328' r='1.8' fill='%23fff' opacity='0.8'/%3E%3Ccircle cx='133' cy='36' r='1.8' fill='%23fff' opacity='0.8'/%3E%3Ccircle cx='762' cy='150' r='1.2' fill='%23fff' opacity='0.8'/%3E%3Ccircle cx='441' cy='64' r='1.2' fill='%23fff' opacity='0.8'/%3E%3Ccircle cx='282' cy='75' r='1.2' fill='%23fff' opacity='0.8'/%3E%3Ccircle cx='268' cy='230' r='3' fill='%23fff' opacity='0.8'/%3E%3Ccircle cx='142' cy='131' r='2.4' fill='%23fff' opacity='0.8'/%3E%3Ccircle cx='239' cy='249' r='3' fill='%23fff' opacity='0.8'/%3E%3Ccircle cx='686' cy='186' r='3' fill='%23fff' opacity='0.8'/%3E%3Ccircle cx='653' cy='160' r='1.2' fill='%23fff' opacity='0.8'/%3E%3Ccircle cx='354' cy='304' r='2.4' fill='%23fff' opacity='0.8'/%3E%3Ccircle cx='707' cy='229' r='1.8' fill='%23fff' opacity='0.8'/%3E%3Ccircle cx='450' cy='346' r='3' fill='%23fff' opacity='0.8'/%3E%3Ccircle cx='548' cy='90' r='2.4' fill='%23fff' opacity='0.8'/%3E%3Ccircle cx='207' cy='91' r='2.4' fill='%23fff' opacity='0.8'/%3E%3Ccircle cx='259' cy='190' r='3' fill='%23fff' opacity='0.8'/%3E%3Ccircle cx='268' cy='310' r='2.4' fill='%23fff' opacity='0.8'/%3E%3Ccircle cx='405' cy='66' r='3' fill='%23fff' opacity='0.8'/%3E%3Ccircle cx='563' cy='120' r='1.8' fill='%23fff' opacity='0.8'/%3E%3Ccircle cx='199' cy='359' r='2.4' fill='%23fff' opacity='0.8'/%3E%3Ccircle cx='134' cy='37' r='3' fill='%23fff' opacity='0.8'/%3E%3Ccircle cx='666' cy='322' r='3' fill='%23fff' opacity='0.8'/%3E%3Ccircle cx='399' cy='1' r='3' fill='%23fff' opacity='0.8'/%3E%3Ccircle cx='44' cy='115' r='1.8' fill='%23fff' opacity='0.8'/%3E%3Ccircle cx='509' cy='357' r='3' fill='%23fff' opacity='0.8'/%3E%3Ccircle cx='654' cy='129' r='1.8' fill='%23fff' opacity='0.8'/%3E%3Ccircle cx='750' cy='211' r='2.4' fill='%23fff' opacity='0.8'/%3E%3Ccircle cx='778' cy='179' r='1.8' fill='%23fff' opacity='0.8'/%3E%3Ccircle cx='722' cy='322' r='2.4' fill='%23fff' opacity='0.8'/%3E%3Ccircle cx='532' cy='366' r='1.8' fill='%23fff' opacity='0.8'/%3E%3Ccircle cx='720' cy='308' r='1.8' fill='%23fff' opacity='0.8'/%3E%3Ccircle cx='740' cy='254' r='1.8' fill='%23fff' opacity='0.8'/%3E%3Ccircle cx='632' cy='21' r='1.2' fill='%23fff' opacity='0.8'/%3E%3Ccircle cx='367' cy='298' r='2.4' fill='%23fff' opacity='0.8'/%3E%3Ccircle cx='186' cy='151' r='1.8' fill='%23fff' opacity='0.8'/%3E%3Ccircle cx='168' cy='350' r='1.2' fill='%23fff' opacity='0.8'/%3E%3Ccircle cx='230' cy='85' r='1.2' fill='%23fff' opacity='0.8'/%3E%3Ccircle cx='439' cy='329' r='1.2' fill='%23fff' opacity='0.8'/%3E%3Ccircle cx='660' cy='90' r='52.800000000000004' fill='url(%23mhalo)'/%3E%3Cmask id='cm'%3E%3Crect width='800' height='500' fill='%23fff'/%3E%3Ccircle cx='670.8' cy='84.0' r='20.4' fill='%23000'/%3E%3C/mask%3E%3Ccircle cx='660' cy='90' r='24' fill='%23fff6d8' mask='url(%23cm)'/%3E%3Ccircle cx='248' cy='156' r='1.2' fill='%23fff' opacity='0.63'/%3E%3Ccircle cx='623' cy='126' r='0.9' fill='%23fff' opacity='0.81'/%3E%3Ccircle cx='18' cy='125' r='1.6' fill='%23fff' opacity='0.81'/%3E%3Ccircle cx='244' cy='54' r='2' fill='%23fff' opacity='0.81'/%3E%3Ccircle cx='567' cy='126' r='2' fill='%23fff' opacity='0.81'/%3E%3Ccircle cx='159' cy='64' r='1.2' fill='%23fff' opacity='0.81'/%3E%3Ccircle cx='404' cy='194' r='0.9' fill='%23fff' opacity='0.81'/%3E%3Ccircle cx='70' cy='45' r='0.9' fill='%23fff' opacity='0.63'/%3E%3Ccircle cx='36' cy='73' r='2' fill='%23fff' opacity='0.81'/%3E%3Ccircle cx='741' cy='104' r='2' fill='%23fff' opacity='0.63'/%3E%3Ccircle cx='750' cy='152' r='2' fill='%23fff' opacity='0.45'/%3E%3Ccircle cx='379' cy='29' r='0.9' fill='%23fff' opacity='0.45'/%3E%3Ccircle cx='511' cy='60' r='1.6' fill='%23fff' opacity='0.81'/%3E%3Ccircle cx='451' cy='165' r='1.6' fill='%23fff' opacity='0.63'/%3E%3Ccircle cx='524' cy='103' r='1.6' fill='%23fff' opacity='0.81'/%3E%3Ccircle cx='604' cy='109' r='1.2' fill='%23fff' opacity='0.63'/%3E%3Ccircle cx='703' cy='12' r='1.6' fill='%23fff' opacity='0.81'/%3E%3Ccircle cx='692' cy='183' r='1.2' fill='%23fff' opacity='0.81'/%3E%3Ccircle cx='339' cy='143' r='0.9' fill='%23fff' opacity='0.81'/%3E%3Ccircle cx='676' cy='59' r='1.6' fill='%23fff' opacity='0.63'/%3E%3Ccircle cx='132' cy='21' r='2' fill='%23fff' opacity='0.81'/%3E%3Ccircle cx='500' cy='27' r='1.6' fill='%23fff' opacity='0.45'/%3E%3Ccircle cx='425' cy='43' r='0.9' fill='%23fff' opacity='0.63'/%3E%3Ccircle cx='442' cy='111' r='0.9' fill='%23fff' opacity='0.45'/%3E%3Ccircle cx='624' cy='162' r='0.9' fill='%23fff' opacity='0.63'/%3E%3Ccircle cx='740' cy='155' r='1.6' fill='%23fff' opacity='0.81'/%3E%3Ccircle cx='290' cy='134' r='1.2' fill='%23fff' opacity='0.45'/%3E%3Ccircle cx='322' cy='6' r='0.9' fill='%23fff' opacity='0.45'/%3E%3Ccircle cx='619' cy='142' r='0.9' fill='%23fff' opacity='0.45'/%3E%3Ccircle cx='422' cy='79' r='1.6' fill='%23fff' opacity='0.45'/%3E%3Ccircle cx='711' cy='15' r='1.6' fill='%23fff' opacity='0.63'/%3E%3Ccircle cx='373' cy='40' r='2' fill='%23fff' opacity='0.63'/%3E%3Ccircle cx='476' cy='138' r='2' fill='%23fff' opacity='0.81'/%3E%3Ccircle cx='614' cy='179' r='0.9' fill='%23fff' opacity='0.81'/%3E%3Ccircle cx='524' cy='74' r='2' fill='%23fff' opacity='0.81'/%3E%3Ccircle cx='742' cy='188' r='1.2' fill='%23fff' opacity='0.63'/%3E%3Ccircle cx='452' cy='71' r='1.6' fill='%23fff' opacity='0.81'/%3E%3Ccircle cx='352' cy='7' r='2' fill='%23fff' opacity='0.81'/%3E%3Ccircle cx='327' cy='10' r='2' fill='%23fff' opacity='0.81'/%3E%3Ccircle cx='608' cy='166' r='1.2' fill='%23fff' opacity='0.45'/%3E%3Ccircle cx='653' cy='165' r='1.6' fill='%23fff' opacity='0.63'/%3E%3Ccircle cx='366' cy='178' r='1.6' fill='%23fff' opacity='0.81'/%3E%3Ccircle cx='728' cy='76' r='2' fill='%23fff' opacity='0.45'/%3E%3Ccircle cx='608' cy='20' r='0.9' fill='%23fff' opacity='0.63'/%3E%3Ccircle cx='262' cy='165' r='2' fill='%23fff' opacity='0.63'/%3E%3Ccircle cx='611' cy='158' r='1.6' fill='%23fff' opacity='0.45'/%3E%3Ccircle cx='377' cy='52' r='1.6' fill='%23fff' opacity='0.63'/%3E%3Ccircle cx='614' cy='72' r='1.6' fill='%23fff' opacity='0.63'/%3E%3Ccircle cx='112' cy='11' r='1.2' fill='%23fff' opacity='0.63'/%3E%3Ccircle cx='517' cy='61' r='1.6' fill='%23fff' opacity='0.45'/%3E%3Ccircle cx='340' cy='52' r='2' fill='%23fff' opacity='0.81'/%3E%3Ccircle cx='719' cy='29' r='0.9' fill='%23fff' opacity='0.81'/%3E%3Ccircle cx='334' cy='90' r='1.2' fill='%23fff' opacity='0.63'/%3E%3Ccircle cx='178' cy='25' r='1.6' fill='%23fff' opacity='0.81'/%3E%3Ccircle cx='670' cy='60' r='2' fill='%23fff' opacity='0.63'/%3E%3Ccircle cx='235' cy='35' r='0.9' fill='%23fff' opacity='0.81'/%3E%3Ccircle cx='200' cy='85' r='1.2' fill='%23fff' opacity='0.63'/%3E%3Ccircle cx='353' cy='169' r='0.9' fill='%23fff' opacity='0.81'/%3E%3Ccircle cx='358' cy='155' r='1.2' fill='%23fff' opacity='0.63'/%3E%3Ccircle cx='303' cy='137' r='1.6' fill='%23fff' opacity='0.63'/%3E%3Cg transform='translate(180,130) scale(1.05) rotate(-7)'%3E%3Cg transform='translate(60,-6)' fill='%232a2147' stroke='%232a2147'%3E%3Cellipse rx='14' ry='7' stroke='none'/%3E%3Ccircle cx='15' cy='-7' r='5' stroke='none'/%3E%3Cpath d='M-8,6L-13,18M-3,7L-4,19M7,7L11,18M11,5L16,16' stroke-width='2' fill='none'/%3E%3Cpath d='M15,-11L12,-21M15,-11L21,-19M13,-16L9,-18M18,-15L23,-14' stroke-width='1.5' fill='none'/%3E%3C/g%3E%3Cg transform='translate(100,-12)' fill='%232a2147' stroke='%232a2147'%3E%3Cellipse rx='14' ry='7' stroke='none'/%3E%3Ccircle cx='15' cy='-7' r='5' stroke='none'/%3E%3Cpath d='M-8,6L-13,18M-3,7L-4,19M7,7L11,18M11,5L16,16' stroke-width='2' fill='none'/%3E%3Cpath d='M15,-11L12,-21M15,-11L21,-19M13,-16L9,-18M18,-15L23,-14' stroke-width='1.5' fill='none'/%3E%3C/g%3E%3Cg transform='translate(140,-18)' fill='%232a2147' stroke='%232a2147'%3E%3Cellipse rx='14' ry='7' stroke='none'/%3E%3Ccircle cx='15' cy='-7' r='5' stroke='none'/%3E%3Cpath d='M-8,6L-13,18M-3,7L-4,19M7,7L11,18M11,5L16,16' stroke-width='2' fill='none'/%3E%3Cpath d='M15,-11L12,-21M15,-11L21,-19M13,-16L9,-18M18,-15L23,-14' stroke-width='1.5' fill='none'/%3E%3C/g%3E%3Cpath d='M24,-4L58,-6M60,-6L100,-12M100,-12L140,-18' stroke='%232a2147' stroke-width='1' fill='none'/%3E%3Cpath d='M-50,-2Q-52,-22 -30,-24L-2,-24Q10,-24 12,-10L10,0Q0,8 -45,6Z' fill='%232a2147'/%3E%3Cpath d='M-54,12Q-30,16 18,10Q28,6 24,-6' stroke='%232a2147' stroke-width='3' fill='none'/%3E%3Cpath d='M-20,12V4M0,12V4' stroke='%232a2147' stroke-width='2'/%3E%3Ccircle cx='-8' cy='-30' r='6' fill='%232a2147'/%3E%3Cpolygon points='-14,-33 -6,-45 0,-32' fill='%232a2147'/%3E%3Cellipse cx='-40' cy='-30' rx='11' ry='9' fill='%232a2147'/%3E%3C/g%3E%3Cpath d='M0,330Q120,290 250,325T520,320T800,310L800,500L0,500Z' fill='%237c78ad'/%3E%3Cpath d='M0,360Q160,320 330,355T620,345T800,350L800,500L0,500Z' fill='%23a597c6'/%3E%3Cpath d='M0,405Q200,380 400,402T800,392L800,500L0,500Z' fill='url(%23gr)'/%3E%3Cuse href='%23tree' transform='translate(60,395) scale(1.1)'/%3E%3Cuse href='%23tree' transform='translate(200,402) scale(0.85)'/%3E%3Cuse href='%23tree' transform='translate(735,392) scale(1.05)'/%3E%3Cuse href='%23tree' transform='translate(340,410) scale(0.7)'/%3E%3Cuse href='%23cabin' transform='translate(520,412) scale(1.05)'/%3E%3Cuse href='%23snowman' transform='translate(140,452) scale(0.9)'/%3E%3Cuse href='%23gift' transform='translate(420,440) scale(1)'/%3E%3Cuse href='%23gift2' transform='translate(455,445) scale(0.8)'/%3E%3Cuse href='%23tl' transform='translate(60,395) scale(1.1)'/%3E%3Cuse href='%23tl' transform='translate(200,402) scale(0.85)'/%3E%3Cuse href='%23tl' transform='translate(735,392) scale(1.05)'/%3E%3Cuse href='%23tl' transform='translate(340,410) scale(0.7)'/%3E%3Cuse href='%23cabinL' transform='translate(520,412) scale(1.05)'/%3E%3C/svg%3E") center bottom / cover no-repeat,
                linear-gradient(180deg, #14234F 0%, #E9A86B 100%);

            /* Madrugada: estrella de Belén y nacimiento */
            --bg-dark-morning:
                url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 800 500' preserveAspectRatio='xMidYMax slice'%3E%3Cdefs%3E%3ClinearGradient id='sky' x1='0' y1='0' x2='0' y2='1'%3E%3Cstop offset='0' stop-color='%230f1b3d'/%3E%3Cstop offset='0.45' stop-color='%232b3a74'/%3E%3Cstop offset='0.75' stop-color='%238a5f8f'/%3E%3Cstop offset='1' stop-color='%23f0a070'/%3E%3C/linearGradient%3E%3ClinearGradient id='gr' x1='0' y1='0' x2='0' y2='1'%3E%3Cstop offset='0' stop-color='%23aebcdc'/%3E%3Cstop offset='1' stop-color='%236a7aa8'/%3E%3C/linearGradient%3E%3CradialGradient id='halo'%3E%3Cstop offset='0' stop-color='%23ffe9a0' stop-opacity='.95'/%3E%3Cstop offset='1' stop-color='%23ffd36b' stop-opacity='0'/%3E%3C/radialGradient%3E%3CradialGradient id='mhalo'%3E%3Cstop offset='0' stop-color='%23cfe0ff' stop-opacity='.5'/%3E%3Cstop offset='1' stop-color='%23cfe0ff' stop-opacity='0'/%3E%3C/radialGradient%3E%3Cg id='tree'%3E%3Crect x='-6' y='0' width='12' height='18' fill='%235a3a22'/%3E%3Cpolygon points='0,-95 -42,-30 42,-30' fill='%231f6b46'/%3E%3Cpolygon points='0,-65 -52,5 52,5' fill='%2325804f'/%3E%3Cpolygon points='0,-40 -62,32 62,32' fill='%232c9a5d'/%3E%3Cpath d='M-14,-78Q0,-70 14,-78L0,-95Z M-34,-40Q-10,-30 10,-44L0,-64L-24,-48Z M-46,0Q-20,8 6,-6L0,-30L-30,-12Z' fill='%23fff'/%3E%3C/g%3E%3Cg id='tl'%3E%3Ccircle cx='-18' cy='-12' r='4' fill='%23ff5a5a'/%3E%3Ccircle cx='14' cy='-25' r='4' fill='%23ffd34d'/%3E%3Ccircle cx='-4' cy='-48' r='3.5' fill='%236bd5ff'/%3E%3Ccircle cx='28' cy='10' r='4' fill='%23ff5a5a'/%3E%3Ccircle cx='-34' cy='18' r='4' fill='%23ffd34d'/%3E%3Ccircle cx='4' cy='12' r='4' fill='%236bd5ff'/%3E%3Ccircle cx='-8' cy='-70' r='3' fill='%23ff5a5a'/%3E%3Cpolygon points='0,-109 4,-102 12,-102 8,-95 12,-88 4,-88 0,-81 -4,-88 -12,-88 -8,-95 -12,-102 -4,-102' fill='%23ffd34d'/%3E%3C/g%3E%3Cg id='snowman'%3E%3Ccircle cx='0' cy='-20' r='30' fill='%23fff'/%3E%3Ccircle cx='0' cy='-62' r='22' fill='%23fff'/%3E%3Ccircle cx='0' cy='-92' r='16' fill='%23fff'/%3E%3Cpath d='M-26,-70L-48,-90M-44,-82L-52,-80M26,-70L50,-84M44,-79L50,-70' stroke='%235a3a22' stroke-width='3' stroke-linecap='round' fill='none'/%3E%3Ccircle cx='-5' cy='-95' r='2' fill='%23222'/%3E%3Ccircle cx='5' cy='-95' r='2' fill='%23222'/%3E%3Cpolygon points='0,-91 14,-88 0,-86' fill='%23ff8a1f'/%3E%3Crect x='-13' y='-120' width='26' height='22' fill='%23222'/%3E%3Crect x='-20' y='-103' width='40' height='6' rx='2' fill='%23222'/%3E%3Crect x='-13' y='-108' width='26' height='5' fill='%23c62828'/%3E%3Cpath d='M-17,-76Q0,-68 17,-76L17,-70Q0,-62 -17,-70Z' fill='%23c62828'/%3E%3Crect x='10' y='-72' width='7' height='20' fill='%23c62828'/%3E%3Ccircle cx='0' cy='-30' r='3' fill='%23222'/%3E%3Ccircle cx='0' cy='-18' r='3' fill='%23222'/%3E%3Ccircle cx='0' cy='-6' r='3' fill='%23222'/%3E%3C/g%3E%3Cg id='cabin'%3E%3Crect x='-55' y='-60' width='110' height='60' fill='%237a4a28'/%3E%3Cpath d='M-55 -50H55M-55 -38H55M-55 -26H55M-55 -14H55' stroke='%235a3519' stroke-width='2'/%3E%3Cpolygon points='-68,-56 0,-112 68,-56' fill='%238a2f2f'/%3E%3Cpolygon points='-64,-58 0,-108 64,-58 52,-58 0,-96 -52,-58' fill='%23fff'/%3E%3Crect x='30' y='-120' width='14' height='30' fill='%235a3a22'/%3E%3Crect x='28' y='-124' width='18' height='6' fill='%23fff'/%3E%3Crect x='-12' y='-34' width='24' height='34' fill='%234a2d1a'/%3E%3Ccircle cx='0' cy='-24' r='7' fill='none' stroke='%232c9a5d' stroke-width='3'/%3E%3Ccircle cx='0' cy='-31' r='2' fill='%23e53935'/%3E%3Ccircle cx='37' cy='-135' r='6' fill='%23fff' opacity='.7'/%3E%3Ccircle cx='43' cy='-148' r='8' fill='%23fff' opacity='.5'/%3E%3Ccircle cx='52' cy='-164' r='10' fill='%23fff' opacity='.3'/%3E%3C/g%3E%3Cg id='cabinL'%3E%3Crect x='-46' y='-48' width='24' height='20' fill='%23ffd36b'/%3E%3Crect x='22' y='-48' width='24' height='20' fill='%23ffd36b'/%3E%3Cpath d='M-34,-48V-28M-46,-38H-22M34,-48V-28M22,-38H46' stroke='%237a4a28' stroke-width='2'/%3E%3Cellipse cx='0' cy='-30' rx='80' ry='40' fill='url(%23halo)' opacity='.55'/%3E%3C/g%3E%3Cg id='stable'%3E%3Crect x='318' y='298' width='164' height='108' fill='%234a2d1a'/%3E%3Crect x='330' y='310' width='140' height='94' fill='%237a4a28'/%3E%3Cpath d='M345,310V404M365,310V404M385,310V404M405,310V404M425,310V404M445,310V404' stroke='%235a3519' stroke-width='2'/%3E%3Crect x='313' y='292' width='10' height='116' fill='%235a3a22'/%3E%3Crect x='477' y='292' width='10' height='116' fill='%235a3a22'/%3E%3Cpath d='M286,306L400,220L514,306L500,308L400,236L300,308Z' fill='%238a5a33'/%3E%3Cpath d='M292,300Q330,280 365,262Q400,244 400,230Q405,246 440,262Q475,282 508,300Q470,288 400,252Q330,288 292,300Z' fill='%23fff'/%3E%3Cpath d='M322,406Q400,390 478,406L478,414L322,414Z' fill='%23e3b94f'/%3E%3Cg transform='translate(336,386)'%3E%3Cellipse rx='18' ry='11' fill='%238d8d95'/%3E%3Ccircle cx='14' cy='-8' r='8' fill='%238d8d95'/%3E%3Cpath d='M12,-15L10,-26L16,-16Z M17,-14L20,-25L21,-12Z' fill='%238d8d95'/%3E%3Ccircle cx='17' cy='-9' r='1.4' fill='%23222'/%3E%3Cellipse cx='19' cy='-4' rx='4' ry='3' fill='%23c9c9d0'/%3E%3C/g%3E%3Cg transform='translate(470,388)'%3E%3Cellipse rx='20' ry='12' fill='%23a8734a'/%3E%3Ccircle cx='-15' cy='-8' r='9' fill='%23a8734a'/%3E%3Cpath d='M-22,-14Q-28,-18 -26,-24M-9,-14Q-5,-20 -8,-25' stroke='%23f1e6cf' stroke-width='3' fill='none' stroke-linecap='round'/%3E%3Cellipse cx='-19' cy='-4' rx='5' ry='3.5' fill='%23e8c9b0'/%3E%3Ccircle cx='-16' cy='-10' r='1.4' fill='%23222'/%3E%3C/g%3E%3Cpath d='M376,376L424,376L418,402L382,402Z' fill='%237a4a28'/%3E%3Cpath d='M382,378Q400,368 418,378L418,382L382,382Z' fill='%23e3b94f'/%3E%3Cellipse cx='400' cy='376' rx='14' ry='6' fill='%23fff'/%3E%3Ccircle cx='392' cy='372' r='6.5' fill='%23f4c9a0'/%3E%3Cg transform='translate(352,0)'%3E%3Cpath d='M0,404Q-14,372 0,350Q14,372 24,404Z' fill='%232a56b0'/%3E%3Cpath d='M-4,404Q-18,376 -2,354Q-10,380 -2,404Z' fill='%231d3f87'/%3E%3Cpath d='M-6,344Q4,326 14,344Q16,362 10,372L-2,372Q-8,360 -6,344Z' fill='%23f2f2f2'/%3E%3Ccircle cx='6' cy='344' r='8' fill='%23f4c9a0'/%3E%3Ccircle cx='5' cy='336' r='13' fill='none' stroke='%23ffe27a' stroke-width='1.6'/%3E%3Cpath d='M14,372Q24,376 36,378' stroke='%232a56b0' stroke-width='6' stroke-linecap='round' fill='none'/%3E%3C/g%3E%3Cg transform='translate(448,0)'%3E%3Cpath d='M-4,404Q-14,362 4,346Q22,362 20,404Z' fill='%23a8622e'/%3E%3Ccircle cx='8' cy='338' r='9' fill='%23f4c9a0'/%3E%3Cpath d='M0,336Q8,322 16,336Q16,326 8,324Q0,326 0,336Z' fill='%234a2d1a'/%3E%3Cpath d='M4,344Q8,354 14,344Z' fill='%234a2d1a'/%3E%3Ccircle cx='8' cy='334' r='13' fill='none' stroke='%23ffe27a' stroke-width='1.6'/%3E%3Cpath d='M34,300V406' stroke='%235a3a22' stroke-width='4' stroke-linecap='round'/%3E%3Cpath d='M18,368Q26,372 34,366' stroke='%23a8622e' stroke-width='6' stroke-linecap='round' fill='none'/%3E%3C/g%3E%3C/g%3E%3Cg id='stableL'%3E%3Cellipse cx='400' cy='360' rx='150' ry='90' fill='url(%23halo)' opacity='.75'/%3E%3Cellipse cx='400' cy='372' rx='30' ry='22' fill='url(%23halo)'/%3E%3C/g%3E%3Cg id='gift'%3E%3Crect width='30' height='22' fill='%23c62828'/%3E%3Crect x='12' width='6' height='22' fill='%23ffd34d'/%3E%3Crect y='8' width='30' height='5' fill='%23ffd34d'/%3E%3Cpath d='M15,0q-12,-12 -3,-12q6,0 3,12q-3,-12 3,-12q9,0 -3,12' fill='%23ffd34d'/%3E%3C/g%3E%3Cg id='gift2'%3E%3Crect width='26' height='20' fill='%232a8f5b'/%3E%3Crect x='10' width='6' height='20' fill='%23fff'/%3E%3Crect y='7' width='26' height='5' fill='%23fff'/%3E%3C/g%3E%3C/defs%3E%3Crect width='800' height='500' fill='url(%23sky)'/%3E%3Ccircle cx='246' cy='82' r='0.9' fill='%23fff' opacity='0.90'/%3E%3Ccircle cx='410' cy='127' r='1.2' fill='%23fff' opacity='0.50'/%3E%3Ccircle cx='73' cy='10' r='2' fill='%23fff' opacity='0.90'/%3E%3Ccircle cx='301' cy='209' r='0.9' fill='%23fff' opacity='0.50'/%3E%3Ccircle cx='537' cy='142' r='1.6' fill='%23fff' opacity='0.70'/%3E%3Ccircle cx='181' cy='216' r='0.9' fill='%23fff' opacity='0.70'/%3E%3Ccircle cx='224' cy='11' r='1.6' fill='%23fff' opacity='0.70'/%3E%3Ccircle cx='203' cy='47' r='1.6' fill='%23fff' opacity='0.70'/%3E%3Ccircle cx='647' cy='227' r='1.6' fill='%23fff' opacity='0.50'/%3E%3Ccircle cx='625' cy='91' r='2' fill='%23fff' opacity='0.90'/%3E%3Ccircle cx='259' cy='50' r='1.2' fill='%23fff' opacity='0.70'/%3E%3Ccircle cx='291' cy='27' r='1.6' fill='%23fff' opacity='0.50'/%3E%3Ccircle cx='303' cy='151' r='1.6' fill='%23fff' opacity='0.90'/%3E%3Ccircle cx='204' cy='110' r='2' fill='%23fff' opacity='0.90'/%3E%3Ccircle cx='300' cy='115' r='2' fill='%23fff' opacity='0.50'/%3E%3Ccircle cx='243' cy='83' r='1.6' fill='%23fff' opacity='0.50'/%3E%3Ccircle cx='88' cy='16' r='2' fill='%23fff' opacity='0.90'/%3E%3Ccircle cx='292' cy='137' r='2' fill='%23fff' opacity='0.90'/%3E%3Ccircle cx='356' cy='42' r='1.2' fill='%23fff' opacity='0.50'/%3E%3Ccircle cx='427' cy='56' r='2' fill='%23fff' opacity='0.70'/%3E%3Ccircle cx='193' cy='96' r='2' fill='%23fff' opacity='0.90'/%3E%3Ccircle cx='607' cy='87' r='1.2' fill='%23fff' opacity='0.70'/%3E%3Ccircle cx='108' cy='219' r='0.9' fill='%23fff' opacity='0.90'/%3E%3Ccircle cx='239' cy='76' r='1.2' fill='%23fff' opacity='0.50'/%3E%3Ccircle cx='344' cy='50' r='1.6' fill='%23fff' opacity='0.70'/%3E%3Ccircle cx='31' cy='15' r='1.6' fill='%23fff' opacity='0.90'/%3E%3Ccircle cx='89' cy='78' r='1.6' fill='%23fff' opacity='0.50'/%3E%3Ccircle cx='335' cy='78' r='1.6' fill='%23fff' opacity='0.50'/%3E%3Ccircle cx='672' cy='110' r='0.9' fill='%23fff' opacity='0.70'/%3E%3Ccircle cx='637' cy='54' r='2' fill='%23fff' opacity='0.70'/%3E%3Ccircle cx='144' cy='69' r='2' fill='%23fff' opacity='0.90'/%3E%3Ccircle cx='167' cy='89' r='0.9' fill='%23fff' opacity='0.70'/%3E%3Ccircle cx='50' cy='121' r='1.2' fill='%23fff' opacity='0.70'/%3E%3Ccircle cx='376' cy='79' r='0.9' fill='%23fff' opacity='0.70'/%3E%3Ccircle cx='217' cy='113' r='1.2' fill='%23fff' opacity='0.50'/%3E%3Ccircle cx='65' cy='20' r='0.9' fill='%23fff' opacity='0.90'/%3E%3Ccircle cx='177' cy='157' r='1.2' fill='%23fff' opacity='0.90'/%3E%3Ccircle cx='46' cy='144' r='2' fill='%23fff' opacity='0.90'/%3E%3Ccircle cx='260' cy='87' r='0.9' fill='%23fff' opacity='0.50'/%3E%3Ccircle cx='546' cy='79' r='2' fill='%23fff' opacity='0.90'/%3E%3Ccircle cx='210' cy='127' r='1.2' fill='%23fff' opacity='0.50'/%3E%3Ccircle cx='454' cy='110' r='2' fill='%23fff' opacity='0.50'/%3E%3Ccircle cx='229' cy='112' r='2' fill='%23fff' opacity='0.50'/%3E%3Ccircle cx='667' cy='114' r='1.2' fill='%23fff' opacity='0.70'/%3E%3Ccircle cx='197' cy='13' r='0.9' fill='%23fff' opacity='0.70'/%3E%3Ccircle cx='264' cy='67' r='1.2' fill='%23fff' opacity='0.50'/%3E%3Ccircle cx='432' cy='227' r='1.6' fill='%23fff' opacity='0.50'/%3E%3Ccircle cx='337' cy='18' r='1.6' fill='%23fff' opacity='0.90'/%3E%3Ccircle cx='124' cy='150' r='2' fill='%23fff' opacity='0.90'/%3E%3Ccircle cx='674' cy='227' r='0.9' fill='%23fff' opacity='0.70'/%3E%3Ccircle cx='401' cy='28' r='2' fill='%23fff' opacity='0.50'/%3E%3Ccircle cx='591' cy='47' r='1.6' fill='%23fff' opacity='0.70'/%3E%3Ccircle cx='677' cy='125' r='1.6' fill='%23fff' opacity='0.70'/%3E%3Ccircle cx='545' cy='60' r='1.6' fill='%23fff' opacity='0.70'/%3E%3Ccircle cx='406' cy='132' r='0.9' fill='%23fff' opacity='0.70'/%3E%3Ccircle cx='647' cy='176' r='1.2' fill='%23fff' opacity='0.50'/%3E%3Ccircle cx='409' cy='163' r='1.2' fill='%23fff' opacity='0.70'/%3E%3Ccircle cx='688' cy='224' r='0.9' fill='%23fff' opacity='0.50'/%3E%3Ccircle cx='709' cy='167' r='2' fill='%23fff' opacity='0.90'/%3E%3Ccircle cx='488' cy='196' r='2' fill='%23fff' opacity='0.70'/%3E%3Ccircle cx='228' cy='209' r='0.9' fill='%23fff' opacity='0.50'/%3E%3Ccircle cx='165' cy='8' r='1.6' fill='%23fff' opacity='0.50'/%3E%3Ccircle cx='410' cy='212' r='2' fill='%23fff' opacity='0.50'/%3E%3Ccircle cx='568' cy='18' r='1.2' fill='%23fff' opacity='0.50'/%3E%3Ccircle cx='692' cy='160' r='1.6' fill='%23fff' opacity='0.90'/%3E%3Ccircle cx='487' cy='139' r='2' fill='%23fff' opacity='0.50'/%3E%3Ccircle cx='85' cy='13' r='0.9' fill='%23fff' opacity='0.70'/%3E%3Ccircle cx='579' cy='226' r='1.6' fill='%23fff' opacity='0.90'/%3E%3Ccircle cx='147' cy='15' r='1.6' fill='%23fff' opacity='0.50'/%3E%3Ccircle cx='791' cy='230' r='0.9' fill='%23fff' opacity='0.70'/%3E%3Ccircle cx='109' cy='315' r='1.8' fill='%23fff' opacity='0.7'/%3E%3Ccircle cx='277' cy='376' r='2.4' fill='%23fff' opacity='0.7'/%3E%3Ccircle cx='298' cy='375' r='1.2' fill='%23fff' opacity='0.7'/%3E%3Ccircle cx='674' cy='230' r='2.4' fill='%23fff' opacity='0.7'/%3E%3Ccircle cx='477' cy='350' r='3' fill='%23fff' opacity='0.7'/%3E%3Ccircle cx='403' cy='60' r='2.4' fill='%23fff' opacity='0.7'/%3E%3Ccircle cx='228' cy='161' r='2.4' fill='%23fff' opacity='0.7'/%3E%3Ccircle cx='266' cy='184' r='1.8' fill='%23fff' opacity='0.7'/%3E%3Ccircle cx='164' cy='282' r='2.4' fill='%23fff' opacity='0.7'/%3E%3Ccircle cx='168' cy='5' r='1.2' fill='%23fff' opacity='0.7'/%3E%3Ccircle cx='125' cy='304' r='2.4' fill='%23fff' opacity='0.7'/%3E%3Ccircle cx='28' cy='42' r='2.4' fill='%23fff' opacity='0.7'/%3E%3Ccircle cx='209' cy='195' r='3' fill='%23fff' opacity='0.7'/%3E%3Ccircle cx='597' cy='224' r='1.2' fill='%23fff' opacity='0.7'/%3E%3Ccircle cx='656' cy='351' r='1.2' fill='%23fff' opacity='0.7'/%3E%3Ccircle cx='592' cy='301' r='2.4' fill='%23fff' opacity='0.7'/%3E%3Ccircle cx='185' cy='48' r='3' fill='%23fff' opacity='0.7'/%3E%3Ccircle cx='526' cy='344' r='1.8' fill='%23fff' opacity='0.7'/%3E%3Ccircle cx='277' cy='231' r='1.8' fill='%23fff' opacity='0.7'/%3E%3Ccircle cx='496' cy='144' r='2.4' fill='%23fff' opacity='0.7'/%3E%3Ccircle cx='104' cy='62' r='1.2' fill='%23fff' opacity='0.7'/%3E%3Ccircle cx='285' cy='143' r='1.2' fill='%23fff' opacity='0.7'/%3E%3Ccircle cx='28' cy='82' r='3' fill='%23fff' opacity='0.7'/%3E%3Ccircle cx='116' cy='351' r='1.2' fill='%23fff' opacity='0.7'/%3E%3Ccircle cx='435' cy='245' r='1.8' fill='%23fff' opacity='0.7'/%3E%3Ccircle cx='547' cy='197' r='3' fill='%23fff' opacity='0.7'/%3E%3Ccircle cx='316' cy='248' r='3' fill='%23fff' opacity='0.7'/%3E%3Ccircle cx='447' cy='213' r='1.2' fill='%23fff' opacity='0.7'/%3E%3Ccircle cx='262' cy='242' r='3' fill='%23fff' opacity='0.7'/%3E%3Ccircle cx='241' cy='230' r='3' fill='%23fff' opacity='0.7'/%3E%3Ccircle cx='68' cy='291' r='1.8' fill='%23fff' opacity='0.7'/%3E%3Ccircle cx='500' cy='360' r='2.4' fill='%23fff' opacity='0.7'/%3E%3Ccircle cx='585' cy='225' r='1.2' fill='%23fff' opacity='0.7'/%3E%3Ccircle cx='721' cy='207' r='3' fill='%23fff' opacity='0.7'/%3E%3Ccircle cx='21' cy='177' r='2.4' fill='%23fff' opacity='0.7'/%3E%3Ccircle cx='482' cy='134' r='2.4' fill='%23fff' opacity='0.7'/%3E%3Ccircle cx='309' cy='271' r='1.8' fill='%23fff' opacity='0.7'/%3E%3Ccircle cx='43' cy='321' r='3' fill='%23fff' opacity='0.7'/%3E%3Ccircle cx='262' cy='48' r='3' fill='%23fff' opacity='0.7'/%3E%3Ccircle cx='100' cy='17' r='1.8' fill='%23fff' opacity='0.7'/%3E%3Cpath d='M0,330Q120,290 250,325T520,320T800,310L800,500L0,500Z' fill='%232f3d72'/%3E%3Cpath d='M0,360Q160,320 330,355T620,345T800,350L800,500L0,500Z' fill='%2347568c'/%3E%3Cpath d='M0,405Q200,380 400,402T800,392L800,500L0,500Z' fill='url(%23gr)'/%3E%3Cuse href='%23tree' transform='translate(60,395) scale(1.1)'/%3E%3Cuse href='%23tree' transform='translate(215,400) scale(0.8)'/%3E%3Cuse href='%23tree' transform='translate(745,392) scale(1.05)'/%3E%3Cuse href='%23tree' transform='translate(660,402) scale(0.75)'/%3E%3Cuse href='%23stable'/%3E%3Cuse href='%23snowman' transform='translate(140,450) scale(0.85)'/%3E%3Cuse href='%23snowman' transform='translate(700,456) scale(0.7)'/%3E%3Cuse href='%23gift' transform='translate(250,436) scale(1)'/%3E%3Cuse href='%23gift2' transform='translate(560,440) scale(1)'/%3E%3Crect width='800' height='500' fill='%230a1030' fill-opacity='0.22'/%3E%3Cuse href='%23tl' transform='translate(60,395) scale(1.1)'/%3E%3Cuse href='%23tl' transform='translate(215,400) scale(0.8)'/%3E%3Cuse href='%23tl' transform='translate(745,392) scale(1.05)'/%3E%3Cuse href='%23tl' transform='translate(660,402) scale(0.75)'/%3E%3Cuse href='%23stableL'/%3E%3Cg transform='translate(400,100)'%3E%3Ccircle r='70' fill='url(%23halo)'/%3E%3Cpolygon points='0,-60 8,-8 60,0 8,8 0,60 -8,8 -60,0 -8,-8' fill='%23fff3b0'/%3E%3Cpolygon points='0,-30 4,-4 30,0 4,4 0,30 -4,4 -30,0 -4,-4' fill='%23fff'/%3E%3C/g%3E%3Cpath d='M400,150Q395,260 400,300' stroke='%23ffe9a0' stroke-opacity='.3' stroke-width='22' fill='none' stroke-linecap='round'/%3E%3C/svg%3E") center bottom / cover no-repeat,
                linear-gradient(180deg, #14234F 0%, #E9A86B 100%);

            /* Día nublado: cabaña iluminada */
            --bg-dark-noon:
                url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 800 500' preserveAspectRatio='xMidYMax slice'%3E%3Cdefs%3E%3ClinearGradient id='sky' x1='0' y1='0' x2='0' y2='1'%3E%3Cstop offset='0' stop-color='%2337465f'/%3E%3Cstop offset='0.5' stop-color='%23566782'/%3E%3Cstop offset='0.8' stop-color='%237c8ba4'/%3E%3Cstop offset='1' stop-color='%239aa7bb'/%3E%3C/linearGradient%3E%3ClinearGradient id='gr' x1='0' y1='0' x2='0' y2='1'%3E%3Cstop offset='0' stop-color='%23c8d3e6'/%3E%3Cstop offset='1' stop-color='%238fa0be'/%3E%3C/linearGradient%3E%3CradialGradient id='halo'%3E%3Cstop offset='0' stop-color='%23ffe9a0' stop-opacity='.95'/%3E%3Cstop offset='1' stop-color='%23ffd36b' stop-opacity='0'/%3E%3C/radialGradient%3E%3CradialGradient id='mhalo'%3E%3Cstop offset='0' stop-color='%23cfe0ff' stop-opacity='.5'/%3E%3Cstop offset='1' stop-color='%23cfe0ff' stop-opacity='0'/%3E%3C/radialGradient%3E%3Cg id='tree'%3E%3Crect x='-6' y='0' width='12' height='18' fill='%235a3a22'/%3E%3Cpolygon points='0,-95 -42,-30 42,-30' fill='%231f6b46'/%3E%3Cpolygon points='0,-65 -52,5 52,5' fill='%2325804f'/%3E%3Cpolygon points='0,-40 -62,32 62,32' fill='%232c9a5d'/%3E%3Cpath d='M-14,-78Q0,-70 14,-78L0,-95Z M-34,-40Q-10,-30 10,-44L0,-64L-24,-48Z M-46,0Q-20,8 6,-6L0,-30L-30,-12Z' fill='%23fff'/%3E%3C/g%3E%3Cg id='tl'%3E%3Ccircle cx='-18' cy='-12' r='4' fill='%23ff5a5a'/%3E%3Ccircle cx='14' cy='-25' r='4' fill='%23ffd34d'/%3E%3Ccircle cx='-4' cy='-48' r='3.5' fill='%236bd5ff'/%3E%3Ccircle cx='28' cy='10' r='4' fill='%23ff5a5a'/%3E%3Ccircle cx='-34' cy='18' r='4' fill='%23ffd34d'/%3E%3Ccircle cx='4' cy='12' r='4' fill='%236bd5ff'/%3E%3Ccircle cx='-8' cy='-70' r='3' fill='%23ff5a5a'/%3E%3Cpolygon points='0,-109 4,-102 12,-102 8,-95 12,-88 4,-88 0,-81 -4,-88 -12,-88 -8,-95 -12,-102 -4,-102' fill='%23ffd34d'/%3E%3C/g%3E%3Cg id='snowman'%3E%3Ccircle cx='0' cy='-20' r='30' fill='%23fff'/%3E%3Ccircle cx='0' cy='-62' r='22' fill='%23fff'/%3E%3Ccircle cx='0' cy='-92' r='16' fill='%23fff'/%3E%3Cpath d='M-26,-70L-48,-90M-44,-82L-52,-80M26,-70L50,-84M44,-79L50,-70' stroke='%235a3a22' stroke-width='3' stroke-linecap='round' fill='none'/%3E%3Ccircle cx='-5' cy='-95' r='2' fill='%23222'/%3E%3Ccircle cx='5' cy='-95' r='2' fill='%23222'/%3E%3Cpolygon points='0,-91 14,-88 0,-86' fill='%23ff8a1f'/%3E%3Crect x='-13' y='-120' width='26' height='22' fill='%23222'/%3E%3Crect x='-20' y='-103' width='40' height='6' rx='2' fill='%23222'/%3E%3Crect x='-13' y='-108' width='26' height='5' fill='%23c62828'/%3E%3Cpath d='M-17,-76Q0,-68 17,-76L17,-70Q0,-62 -17,-70Z' fill='%23c62828'/%3E%3Crect x='10' y='-72' width='7' height='20' fill='%23c62828'/%3E%3Ccircle cx='0' cy='-30' r='3' fill='%23222'/%3E%3Ccircle cx='0' cy='-18' r='3' fill='%23222'/%3E%3Ccircle cx='0' cy='-6' r='3' fill='%23222'/%3E%3C/g%3E%3Cg id='cabin'%3E%3Crect x='-55' y='-60' width='110' height='60' fill='%237a4a28'/%3E%3Cpath d='M-55 -50H55M-55 -38H55M-55 -26H55M-55 -14H55' stroke='%235a3519' stroke-width='2'/%3E%3Cpolygon points='-68,-56 0,-112 68,-56' fill='%238a2f2f'/%3E%3Cpolygon points='-64,-58 0,-108 64,-58 52,-58 0,-96 -52,-58' fill='%23fff'/%3E%3Crect x='30' y='-120' width='14' height='30' fill='%235a3a22'/%3E%3Crect x='28' y='-124' width='18' height='6' fill='%23fff'/%3E%3Crect x='-12' y='-34' width='24' height='34' fill='%234a2d1a'/%3E%3Ccircle cx='0' cy='-24' r='7' fill='none' stroke='%232c9a5d' stroke-width='3'/%3E%3Ccircle cx='0' cy='-31' r='2' fill='%23e53935'/%3E%3Ccircle cx='37' cy='-135' r='6' fill='%23fff' opacity='.7'/%3E%3Ccircle cx='43' cy='-148' r='8' fill='%23fff' opacity='.5'/%3E%3Ccircle cx='52' cy='-164' r='10' fill='%23fff' opacity='.3'/%3E%3C/g%3E%3Cg id='cabinL'%3E%3Crect x='-46' y='-48' width='24' height='20' fill='%23ffd36b'/%3E%3Crect x='22' y='-48' width='24' height='20' fill='%23ffd36b'/%3E%3Cpath d='M-34,-48V-28M-46,-38H-22M34,-48V-28M22,-38H46' stroke='%237a4a28' stroke-width='2'/%3E%3Cellipse cx='0' cy='-30' rx='80' ry='40' fill='url(%23halo)' opacity='.55'/%3E%3C/g%3E%3Cg id='stable'%3E%3Crect x='318' y='298' width='164' height='108' fill='%234a2d1a'/%3E%3Crect x='330' y='310' width='140' height='94' fill='%237a4a28'/%3E%3Cpath d='M345,310V404M365,310V404M385,310V404M405,310V404M425,310V404M445,310V404' stroke='%235a3519' stroke-width='2'/%3E%3Crect x='313' y='292' width='10' height='116' fill='%235a3a22'/%3E%3Crect x='477' y='292' width='10' height='116' fill='%235a3a22'/%3E%3Cpath d='M286,306L400,220L514,306L500,308L400,236L300,308Z' fill='%238a5a33'/%3E%3Cpath d='M292,300Q330,280 365,262Q400,244 400,230Q405,246 440,262Q475,282 508,300Q470,288 400,252Q330,288 292,300Z' fill='%23fff'/%3E%3Cpath d='M322,406Q400,390 478,406L478,414L322,414Z' fill='%23e3b94f'/%3E%3Cg transform='translate(336,386)'%3E%3Cellipse rx='18' ry='11' fill='%238d8d95'/%3E%3Ccircle cx='14' cy='-8' r='8' fill='%238d8d95'/%3E%3Cpath d='M12,-15L10,-26L16,-16Z M17,-14L20,-25L21,-12Z' fill='%238d8d95'/%3E%3Ccircle cx='17' cy='-9' r='1.4' fill='%23222'/%3E%3Cellipse cx='19' cy='-4' rx='4' ry='3' fill='%23c9c9d0'/%3E%3C/g%3E%3Cg transform='translate(470,388)'%3E%3Cellipse rx='20' ry='12' fill='%23a8734a'/%3E%3Ccircle cx='-15' cy='-8' r='9' fill='%23a8734a'/%3E%3Cpath d='M-22,-14Q-28,-18 -26,-24M-9,-14Q-5,-20 -8,-25' stroke='%23f1e6cf' stroke-width='3' fill='none' stroke-linecap='round'/%3E%3Cellipse cx='-19' cy='-4' rx='5' ry='3.5' fill='%23e8c9b0'/%3E%3Ccircle cx='-16' cy='-10' r='1.4' fill='%23222'/%3E%3C/g%3E%3Cpath d='M376,376L424,376L418,402L382,402Z' fill='%237a4a28'/%3E%3Cpath d='M382,378Q400,368 418,378L418,382L382,382Z' fill='%23e3b94f'/%3E%3Cellipse cx='400' cy='376' rx='14' ry='6' fill='%23fff'/%3E%3Ccircle cx='392' cy='372' r='6.5' fill='%23f4c9a0'/%3E%3Cg transform='translate(352,0)'%3E%3Cpath d='M0,404Q-14,372 0,350Q14,372 24,404Z' fill='%232a56b0'/%3E%3Cpath d='M-4,404Q-18,376 -2,354Q-10,380 -2,404Z' fill='%231d3f87'/%3E%3Cpath d='M-6,344Q4,326 14,344Q16,362 10,372L-2,372Q-8,360 -6,344Z' fill='%23f2f2f2'/%3E%3Ccircle cx='6' cy='344' r='8' fill='%23f4c9a0'/%3E%3Ccircle cx='5' cy='336' r='13' fill='none' stroke='%23ffe27a' stroke-width='1.6'/%3E%3Cpath d='M14,372Q24,376 36,378' stroke='%232a56b0' stroke-width='6' stroke-linecap='round' fill='none'/%3E%3C/g%3E%3Cg transform='translate(448,0)'%3E%3Cpath d='M-4,404Q-14,362 4,346Q22,362 20,404Z' fill='%23a8622e'/%3E%3Ccircle cx='8' cy='338' r='9' fill='%23f4c9a0'/%3E%3Cpath d='M0,336Q8,322 16,336Q16,326 8,324Q0,326 0,336Z' fill='%234a2d1a'/%3E%3Cpath d='M4,344Q8,354 14,344Z' fill='%234a2d1a'/%3E%3Ccircle cx='8' cy='334' r='13' fill='none' stroke='%23ffe27a' stroke-width='1.6'/%3E%3Cpath d='M34,300V406' stroke='%235a3a22' stroke-width='4' stroke-linecap='round'/%3E%3Cpath d='M18,368Q26,372 34,366' stroke='%23a8622e' stroke-width='6' stroke-linecap='round' fill='none'/%3E%3C/g%3E%3C/g%3E%3Cg id='stableL'%3E%3Cellipse cx='400' cy='360' rx='150' ry='90' fill='url(%23halo)' opacity='.75'/%3E%3Cellipse cx='400' cy='372' rx='30' ry='22' fill='url(%23halo)'/%3E%3C/g%3E%3Cg id='gift'%3E%3Crect width='30' height='22' fill='%23c62828'/%3E%3Crect x='12' width='6' height='22' fill='%23ffd34d'/%3E%3Crect y='8' width='30' height='5' fill='%23ffd34d'/%3E%3Cpath d='M15,0q-12,-12 -3,-12q6,0 3,12q-3,-12 3,-12q9,0 -3,12' fill='%23ffd34d'/%3E%3C/g%3E%3Cg id='gift2'%3E%3Crect width='26' height='20' fill='%232a8f5b'/%3E%3Crect x='10' width='6' height='20' fill='%23fff'/%3E%3Crect y='7' width='26' height='5' fill='%23fff'/%3E%3C/g%3E%3C/defs%3E%3Crect width='800' height='500' fill='url(%23sky)'/%3E%3Cg transform='translate(120,70) scale(1.4)' fill='%235f6f8a' opacity='0.85'%3E%3Cellipse cx='0' cy='0' rx='60' ry='16'/%3E%3Cellipse cx='-28' cy='-10' rx='30' ry='16'/%3E%3Cellipse cx='10' cy='-18' rx='34' ry='20'/%3E%3Cellipse cx='38' cy='-6' rx='26' ry='13'/%3E%3C/g%3E%3Cg transform='translate(420,55) scale(1.6)' fill='%236a7a95' opacity='0.8'%3E%3Cellipse cx='0' cy='0' rx='60' ry='16'/%3E%3Cellipse cx='-28' cy='-10' rx='30' ry='16'/%3E%3Cellipse cx='10' cy='-18' rx='34' ry='20'/%3E%3Cellipse cx='38' cy='-6' rx='26' ry='13'/%3E%3C/g%3E%3Cg transform='translate(690,95) scale(1.2)' fill='%235f6f8a' opacity='0.85'%3E%3Cellipse cx='0' cy='0' rx='60' ry='16'/%3E%3Cellipse cx='-28' cy='-10' rx='30' ry='16'/%3E%3Cellipse cx='10' cy='-18' rx='34' ry='20'/%3E%3Cellipse cx='38' cy='-6' rx='26' ry='13'/%3E%3C/g%3E%3Cg transform='translate(300,150) scale(1)' fill='%237d8ca6' opacity='0.6'%3E%3Cellipse cx='0' cy='0' rx='60' ry='16'/%3E%3Cellipse cx='-28' cy='-10' rx='30' ry='16'/%3E%3Cellipse cx='10' cy='-18' rx='34' ry='20'/%3E%3Cellipse cx='38' cy='-6' rx='26' ry='13'/%3E%3C/g%3E%3Ccircle cx='610' cy='110' r='30' fill='%23d8e0ee' opacity='.55'/%3E%3Cpath d='M0,330Q120,290 250,325T520,320T800,310L800,500L0,500Z' fill='%2356668a'/%3E%3Cpath d='M0,360Q160,320 330,355T620,345T800,350L800,500L0,500Z' fill='%236b7b9c'/%3E%3Cpath d='M0,405Q200,380 400,402T800,392L800,500L0,500Z' fill='url(%23gr)'/%3E%3Cuse href='%23tree' transform='translate(70,395) scale(1.15)'/%3E%3Cuse href='%23tree' transform='translate(215,402) scale(0.85)'/%3E%3Cuse href='%23tree' transform='translate(735,392) scale(1.1)'/%3E%3Cuse href='%23tree' transform='translate(610,404) scale(0.75)'/%3E%3Cuse href='%23cabin' transform='translate(400,412) scale(1.15)'/%3E%3Cuse href='%23snowman' transform='translate(150,452) scale(0.9)'/%3E%3Cuse href='%23snowman' transform='translate(205,458) scale(0.6)'/%3E%3Cuse href='%23snowman' transform='translate(700,456) scale(0.7)'/%3E%3Cuse href='%23gift' transform='translate(520,438) scale(1)'/%3E%3Cuse href='%23gift2' transform='translate(555,444) scale(0.85)'/%3E%3Crect width='800' height='500' fill='%230a1030' fill-opacity='0.16'/%3E%3Cuse href='%23tl' transform='translate(70,395) scale(1.15)'/%3E%3Cuse href='%23tl' transform='translate(215,402) scale(0.85)'/%3E%3Cuse href='%23tl' transform='translate(735,392) scale(1.1)'/%3E%3Cuse href='%23tl' transform='translate(610,404) scale(0.75)'/%3E%3Cuse href='%23cabinL' transform='translate(400,412) scale(1.15)'/%3E%3Ccircle cx='213' cy='5' r='1.8' fill='%23fff' opacity='0.85'/%3E%3Ccircle cx='161' cy='122' r='1.8' fill='%23fff' opacity='0.85'/%3E%3Ccircle cx='56' cy='412' r='2.4' fill='%23fff' opacity='0.85'/%3E%3Ccircle cx='711' cy='188' r='2.4' fill='%23fff' opacity='0.85'/%3E%3Ccircle cx='119' cy='173' r='3.6' fill='%23fff' opacity='0.85'/%3E%3Ccircle cx='725' cy='182' r='3' fill='%23fff' opacity='0.85'/%3E%3Ccircle cx='401' cy='134' r='3' fill='%23fff' opacity='0.85'/%3E%3Ccircle cx='234' cy='105' r='3' fill='%23fff' opacity='0.85'/%3E%3Ccircle cx='320' cy='114' r='3' fill='%23fff' opacity='0.85'/%3E%3Ccircle cx='744' cy='261' r='3.6' fill='%23fff' opacity='0.85'/%3E%3Ccircle cx='236' cy='295' r='3.6' fill='%23fff' opacity='0.85'/%3E%3Ccircle cx='429' cy='250' r='1.8' fill='%23fff' opacity='0.85'/%3E%3Ccircle cx='472' cy='289' r='3' fill='%23fff' opacity='0.85'/%3E%3Ccircle cx='449' cy='291' r='3' fill='%23fff' opacity='0.85'/%3E%3Ccircle cx='710' cy='330' r='3.6' fill='%23fff' opacity='0.85'/%3E%3Ccircle cx='414' cy='397' r='1.8' fill='%23fff' opacity='0.85'/%3E%3Ccircle cx='502' cy='9' r='2.4' fill='%23fff' opacity='0.85'/%3E%3Ccircle cx='143' cy='273' r='2.4' fill='%23fff' opacity='0.85'/%3E%3Ccircle cx='628' cy='401' r='1.8' fill='%23fff' opacity='0.85'/%3E%3Ccircle cx='293' cy='271' r='3.6' fill='%23fff' opacity='0.85'/%3E%3Ccircle cx='155' cy='51' r='1.8' fill='%23fff' opacity='0.85'/%3E%3Ccircle cx='404' cy='24' r='1.8' fill='%23fff' opacity='0.85'/%3E%3Ccircle cx='479' cy='371' r='3' fill='%23fff' opacity='0.85'/%3E%3Ccircle cx='411' cy='257' r='2.4' fill='%23fff' opacity='0.85'/%3E%3Ccircle cx='183' cy='278' r='3' fill='%23fff' opacity='0.85'/%3E%3Ccircle cx='80' cy='120' r='3' fill='%23fff' opacity='0.85'/%3E%3Ccircle cx='318' cy='84' r='3' fill='%23fff' opacity='0.85'/%3E%3Ccircle cx='396' cy='243' r='3' fill='%23fff' opacity='0.85'/%3E%3Ccircle cx='452' cy='400' r='1.8' fill='%23fff' opacity='0.85'/%3E%3Ccircle cx='775' cy='407' r='3' fill='%23fff' opacity='0.85'/%3E%3Ccircle cx='485' cy='72' r='1.8' fill='%23fff' opacity='0.85'/%3E%3Ccircle cx='692' cy='252' r='3.6' fill='%23fff' opacity='0.85'/%3E%3Ccircle cx='385' cy='7' r='3' fill='%23fff' opacity='0.85'/%3E%3Ccircle cx='758' cy='47' r='3' fill='%23fff' opacity='0.85'/%3E%3Ccircle cx='65' cy='21' r='3.6' fill='%23fff' opacity='0.85'/%3E%3Ccircle cx='60' cy='130' r='2.4' fill='%23fff' opacity='0.85'/%3E%3Ccircle cx='511' cy='289' r='3.6' fill='%23fff' opacity='0.85'/%3E%3Ccircle cx='510' cy='22' r='1.8' fill='%23fff' opacity='0.85'/%3E%3Ccircle cx='485' cy='41' r='3.6' fill='%23fff' opacity='0.85'/%3E%3Ccircle cx='237' cy='398' r='3.6' fill='%23fff' opacity='0.85'/%3E%3Ccircle cx='709' cy='369' r='3' fill='%23fff' opacity='0.85'/%3E%3Ccircle cx='694' cy='43' r='1.8' fill='%23fff' opacity='0.85'/%3E%3Ccircle cx='57' cy='170' r='3.6' fill='%23fff' opacity='0.85'/%3E%3Ccircle cx='444' cy='177' r='1.8' fill='%23fff' opacity='0.85'/%3E%3Ccircle cx='633' cy='361' r='3' fill='%23fff' opacity='0.85'/%3E%3Ccircle cx='609' cy='228' r='3' fill='%23fff' opacity='0.85'/%3E%3Ccircle cx='561' cy='420' r='1.8' fill='%23fff' opacity='0.85'/%3E%3Ccircle cx='441' cy='197' r='1.8' fill='%23fff' opacity='0.85'/%3E%3Ccircle cx='685' cy='178' r='3.6' fill='%23fff' opacity='0.85'/%3E%3Ccircle cx='530' cy='1' r='2.4' fill='%23fff' opacity='0.85'/%3E%3Ccircle cx='257' cy='127' r='3.6' fill='%23fff' opacity='0.85'/%3E%3Ccircle cx='524' cy='169' r='3' fill='%23fff' opacity='0.85'/%3E%3Ccircle cx='249' cy='350' r='3' fill='%23fff' opacity='0.85'/%3E%3Ccircle cx='503' cy='163' r='1.8' fill='%23fff' opacity='0.85'/%3E%3Ccircle cx='619' cy='181' r='2.4' fill='%23fff' opacity='0.85'/%3E%3Ccircle cx='348' cy='30' r='2.4' fill='%23fff' opacity='0.85'/%3E%3Ccircle cx='656' cy='258' r='1.8' fill='%23fff' opacity='0.85'/%3E%3Ccircle cx='741' cy='294' r='3.6' fill='%23fff' opacity='0.85'/%3E%3Ccircle cx='342' cy='65' r='3' fill='%23fff' opacity='0.85'/%3E%3Ccircle cx='100' cy='161' r='3' fill='%23fff' opacity='0.85'/%3E%3C/svg%3E") center bottom / cover no-repeat,
                linear-gradient(180deg, #14234F 0%, #E9A86B 100%);

            /* Noche: luna, trineo y cabañas */
            --bg-dark-night:
                url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 800 500' preserveAspectRatio='xMidYMax slice'%3E%3Cdefs%3E%3ClinearGradient id='sky' x1='0' y1='0' x2='0' y2='1'%3E%3Cstop offset='0' stop-color='%230f1b3d'/%3E%3Cstop offset='0.45' stop-color='%232b3a74'/%3E%3Cstop offset='0.75' stop-color='%238a5f8f'/%3E%3Cstop offset='1' stop-color='%23f0a070'/%3E%3C/linearGradient%3E%3ClinearGradient id='gr' x1='0' y1='0' x2='0' y2='1'%3E%3Cstop offset='0' stop-color='%23aebcdc'/%3E%3Cstop offset='1' stop-color='%236a7aa8'/%3E%3C/linearGradient%3E%3CradialGradient id='halo'%3E%3Cstop offset='0' stop-color='%23ffe9a0' stop-opacity='.95'/%3E%3Cstop offset='1' stop-color='%23ffd36b' stop-opacity='0'/%3E%3C/radialGradient%3E%3CradialGradient id='mhalo'%3E%3Cstop offset='0' stop-color='%23cfe0ff' stop-opacity='.5'/%3E%3Cstop offset='1' stop-color='%23cfe0ff' stop-opacity='0'/%3E%3C/radialGradient%3E%3Cg id='tree'%3E%3Crect x='-6' y='0' width='12' height='18' fill='%235a3a22'/%3E%3Cpolygon points='0,-95 -42,-30 42,-30' fill='%231f6b46'/%3E%3Cpolygon points='0,-65 -52,5 52,5' fill='%2325804f'/%3E%3Cpolygon points='0,-40 -62,32 62,32' fill='%232c9a5d'/%3E%3Cpath d='M-14,-78Q0,-70 14,-78L0,-95Z M-34,-40Q-10,-30 10,-44L0,-64L-24,-48Z M-46,0Q-20,8 6,-6L0,-30L-30,-12Z' fill='%23fff'/%3E%3C/g%3E%3Cg id='tl'%3E%3Ccircle cx='-18' cy='-12' r='4' fill='%23ff5a5a'/%3E%3Ccircle cx='14' cy='-25' r='4' fill='%23ffd34d'/%3E%3Ccircle cx='-4' cy='-48' r='3.5' fill='%236bd5ff'/%3E%3Ccircle cx='28' cy='10' r='4' fill='%23ff5a5a'/%3E%3Ccircle cx='-34' cy='18' r='4' fill='%23ffd34d'/%3E%3Ccircle cx='4' cy='12' r='4' fill='%236bd5ff'/%3E%3Ccircle cx='-8' cy='-70' r='3' fill='%23ff5a5a'/%3E%3Cpolygon points='0,-109 4,-102 12,-102 8,-95 12,-88 4,-88 0,-81 -4,-88 -12,-88 -8,-95 -12,-102 -4,-102' fill='%23ffd34d'/%3E%3C/g%3E%3Cg id='snowman'%3E%3Ccircle cx='0' cy='-20' r='30' fill='%23fff'/%3E%3Ccircle cx='0' cy='-62' r='22' fill='%23fff'/%3E%3Ccircle cx='0' cy='-92' r='16' fill='%23fff'/%3E%3Cpath d='M-26,-70L-48,-90M-44,-82L-52,-80M26,-70L50,-84M44,-79L50,-70' stroke='%235a3a22' stroke-width='3' stroke-linecap='round' fill='none'/%3E%3Ccircle cx='-5' cy='-95' r='2' fill='%23222'/%3E%3Ccircle cx='5' cy='-95' r='2' fill='%23222'/%3E%3Cpolygon points='0,-91 14,-88 0,-86' fill='%23ff8a1f'/%3E%3Crect x='-13' y='-120' width='26' height='22' fill='%23222'/%3E%3Crect x='-20' y='-103' width='40' height='6' rx='2' fill='%23222'/%3E%3Crect x='-13' y='-108' width='26' height='5' fill='%23c62828'/%3E%3Cpath d='M-17,-76Q0,-68 17,-76L17,-70Q0,-62 -17,-70Z' fill='%23c62828'/%3E%3Crect x='10' y='-72' width='7' height='20' fill='%23c62828'/%3E%3Ccircle cx='0' cy='-30' r='3' fill='%23222'/%3E%3Ccircle cx='0' cy='-18' r='3' fill='%23222'/%3E%3Ccircle cx='0' cy='-6' r='3' fill='%23222'/%3E%3C/g%3E%3Cg id='cabin'%3E%3Crect x='-55' y='-60' width='110' height='60' fill='%237a4a28'/%3E%3Cpath d='M-55 -50H55M-55 -38H55M-55 -26H55M-55 -14H55' stroke='%235a3519' stroke-width='2'/%3E%3Cpolygon points='-68,-56 0,-112 68,-56' fill='%238a2f2f'/%3E%3Cpolygon points='-64,-58 0,-108 64,-58 52,-58 0,-96 -52,-58' fill='%23fff'/%3E%3Crect x='30' y='-120' width='14' height='30' fill='%235a3a22'/%3E%3Crect x='28' y='-124' width='18' height='6' fill='%23fff'/%3E%3Crect x='-12' y='-34' width='24' height='34' fill='%234a2d1a'/%3E%3Ccircle cx='0' cy='-24' r='7' fill='none' stroke='%232c9a5d' stroke-width='3'/%3E%3Ccircle cx='0' cy='-31' r='2' fill='%23e53935'/%3E%3Ccircle cx='37' cy='-135' r='6' fill='%23fff' opacity='.7'/%3E%3Ccircle cx='43' cy='-148' r='8' fill='%23fff' opacity='.5'/%3E%3Ccircle cx='52' cy='-164' r='10' fill='%23fff' opacity='.3'/%3E%3C/g%3E%3Cg id='cabinL'%3E%3Crect x='-46' y='-48' width='24' height='20' fill='%23ffd36b'/%3E%3Crect x='22' y='-48' width='24' height='20' fill='%23ffd36b'/%3E%3Cpath d='M-34,-48V-28M-46,-38H-22M34,-48V-28M22,-38H46' stroke='%237a4a28' stroke-width='2'/%3E%3Cellipse cx='0' cy='-30' rx='80' ry='40' fill='url(%23halo)' opacity='.55'/%3E%3C/g%3E%3Cg id='stable'%3E%3Crect x='318' y='298' width='164' height='108' fill='%234a2d1a'/%3E%3Crect x='330' y='310' width='140' height='94' fill='%237a4a28'/%3E%3Cpath d='M345,310V404M365,310V404M385,310V404M405,310V404M425,310V404M445,310V404' stroke='%235a3519' stroke-width='2'/%3E%3Crect x='313' y='292' width='10' height='116' fill='%235a3a22'/%3E%3Crect x='477' y='292' width='10' height='116' fill='%235a3a22'/%3E%3Cpath d='M286,306L400,220L514,306L500,308L400,236L300,308Z' fill='%238a5a33'/%3E%3Cpath d='M292,300Q330,280 365,262Q400,244 400,230Q405,246 440,262Q475,282 508,300Q470,288 400,252Q330,288 292,300Z' fill='%23fff'/%3E%3Cpath d='M322,406Q400,390 478,406L478,414L322,414Z' fill='%23e3b94f'/%3E%3Cg transform='translate(336,386)'%3E%3Cellipse rx='18' ry='11' fill='%238d8d95'/%3E%3Ccircle cx='14' cy='-8' r='8' fill='%238d8d95'/%3E%3Cpath d='M12,-15L10,-26L16,-16Z M17,-14L20,-25L21,-12Z' fill='%238d8d95'/%3E%3Ccircle cx='17' cy='-9' r='1.4' fill='%23222'/%3E%3Cellipse cx='19' cy='-4' rx='4' ry='3' fill='%23c9c9d0'/%3E%3C/g%3E%3Cg transform='translate(470,388)'%3E%3Cellipse rx='20' ry='12' fill='%23a8734a'/%3E%3Ccircle cx='-15' cy='-8' r='9' fill='%23a8734a'/%3E%3Cpath d='M-22,-14Q-28,-18 -26,-24M-9,-14Q-5,-20 -8,-25' stroke='%23f1e6cf' stroke-width='3' fill='none' stroke-linecap='round'/%3E%3Cellipse cx='-19' cy='-4' rx='5' ry='3.5' fill='%23e8c9b0'/%3E%3Ccircle cx='-16' cy='-10' r='1.4' fill='%23222'/%3E%3C/g%3E%3Cpath d='M376,376L424,376L418,402L382,402Z' fill='%237a4a28'/%3E%3Cpath d='M382,378Q400,368 418,378L418,382L382,382Z' fill='%23e3b94f'/%3E%3Cellipse cx='400' cy='376' rx='14' ry='6' fill='%23fff'/%3E%3Ccircle cx='392' cy='372' r='6.5' fill='%23f4c9a0'/%3E%3Cg transform='translate(352,0)'%3E%3Cpath d='M0,404Q-14,372 0,350Q14,372 24,404Z' fill='%232a56b0'/%3E%3Cpath d='M-4,404Q-18,376 -2,354Q-10,380 -2,404Z' fill='%231d3f87'/%3E%3Cpath d='M-6,344Q4,326 14,344Q16,362 10,372L-2,372Q-8,360 -6,344Z' fill='%23f2f2f2'/%3E%3Ccircle cx='6' cy='344' r='8' fill='%23f4c9a0'/%3E%3Ccircle cx='5' cy='336' r='13' fill='none' stroke='%23ffe27a' stroke-width='1.6'/%3E%3Cpath d='M14,372Q24,376 36,378' stroke='%232a56b0' stroke-width='6' stroke-linecap='round' fill='none'/%3E%3C/g%3E%3Cg transform='translate(448,0)'%3E%3Cpath d='M-4,404Q-14,362 4,346Q22,362 20,404Z' fill='%23a8622e'/%3E%3Ccircle cx='8' cy='338' r='9' fill='%23f4c9a0'/%3E%3Cpath d='M0,336Q8,322 16,336Q16,326 8,324Q0,326 0,336Z' fill='%234a2d1a'/%3E%3Cpath d='M4,344Q8,354 14,344Z' fill='%234a2d1a'/%3E%3Ccircle cx='8' cy='334' r='13' fill='none' stroke='%23ffe27a' stroke-width='1.6'/%3E%3Cpath d='M34,300V406' stroke='%235a3a22' stroke-width='4' stroke-linecap='round'/%3E%3Cpath d='M18,368Q26,372 34,366' stroke='%23a8622e' stroke-width='6' stroke-linecap='round' fill='none'/%3E%3C/g%3E%3C/g%3E%3Cg id='stableL'%3E%3Cellipse cx='400' cy='360' rx='150' ry='90' fill='url(%23halo)' opacity='.75'/%3E%3Cellipse cx='400' cy='372' rx='30' ry='22' fill='url(%23halo)'/%3E%3C/g%3E%3Cg id='gift'%3E%3Crect width='30' height='22' fill='%23c62828'/%3E%3Crect x='12' width='6' height='22' fill='%23ffd34d'/%3E%3Crect y='8' width='30' height='5' fill='%23ffd34d'/%3E%3Cpath d='M15,0q-12,-12 -3,-12q6,0 3,12q-3,-12 3,-12q9,0 -3,12' fill='%23ffd34d'/%3E%3C/g%3E%3Cg id='gift2'%3E%3Crect width='26' height='20' fill='%232a8f5b'/%3E%3Crect x='10' width='6' height='20' fill='%23fff'/%3E%3Crect y='7' width='26' height='5' fill='%23fff'/%3E%3C/g%3E%3C/defs%3E%3Crect width='800' height='500' fill='url(%23sky)'/%3E%3Ccircle cx='246' cy='82' r='0.9' fill='%23fff' opacity='0.90'/%3E%3Ccircle cx='410' cy='127' r='1.2' fill='%23fff' opacity='0.50'/%3E%3Ccircle cx='73' cy='10' r='2' fill='%23fff' opacity='0.90'/%3E%3Ccircle cx='301' cy='209' r='0.9' fill='%23fff' opacity='0.50'/%3E%3Ccircle cx='537' cy='142' r='1.6' fill='%23fff' opacity='0.70'/%3E%3Ccircle cx='181' cy='216' r='0.9' fill='%23fff' opacity='0.70'/%3E%3Ccircle cx='224' cy='11' r='1.6' fill='%23fff' opacity='0.70'/%3E%3Ccircle cx='203' cy='47' r='1.6' fill='%23fff' opacity='0.70'/%3E%3Ccircle cx='647' cy='227' r='1.6' fill='%23fff' opacity='0.50'/%3E%3Ccircle cx='625' cy='91' r='2' fill='%23fff' opacity='0.90'/%3E%3Ccircle cx='259' cy='50' r='1.2' fill='%23fff' opacity='0.70'/%3E%3Ccircle cx='291' cy='27' r='1.6' fill='%23fff' opacity='0.50'/%3E%3Ccircle cx='303' cy='151' r='1.6' fill='%23fff' opacity='0.90'/%3E%3Ccircle cx='204' cy='110' r='2' fill='%23fff' opacity='0.90'/%3E%3Ccircle cx='300' cy='115' r='2' fill='%23fff' opacity='0.50'/%3E%3Ccircle cx='243' cy='83' r='1.6' fill='%23fff' opacity='0.50'/%3E%3Ccircle cx='88' cy='16' r='2' fill='%23fff' opacity='0.90'/%3E%3Ccircle cx='292' cy='137' r='2' fill='%23fff' opacity='0.90'/%3E%3Ccircle cx='356' cy='42' r='1.2' fill='%23fff' opacity='0.50'/%3E%3Ccircle cx='427' cy='56' r='2' fill='%23fff' opacity='0.70'/%3E%3Ccircle cx='193' cy='96' r='2' fill='%23fff' opacity='0.90'/%3E%3Ccircle cx='607' cy='87' r='1.2' fill='%23fff' opacity='0.70'/%3E%3Ccircle cx='108' cy='219' r='0.9' fill='%23fff' opacity='0.90'/%3E%3Ccircle cx='239' cy='76' r='1.2' fill='%23fff' opacity='0.50'/%3E%3Ccircle cx='344' cy='50' r='1.6' fill='%23fff' opacity='0.70'/%3E%3Ccircle cx='31' cy='15' r='1.6' fill='%23fff' opacity='0.90'/%3E%3Ccircle cx='89' cy='78' r='1.6' fill='%23fff' opacity='0.50'/%3E%3Ccircle cx='335' cy='78' r='1.6' fill='%23fff' opacity='0.50'/%3E%3Ccircle cx='672' cy='110' r='0.9' fill='%23fff' opacity='0.70'/%3E%3Ccircle cx='637' cy='54' r='2' fill='%23fff' opacity='0.70'/%3E%3Ccircle cx='144' cy='69' r='2' fill='%23fff' opacity='0.90'/%3E%3Ccircle cx='167' cy='89' r='0.9' fill='%23fff' opacity='0.70'/%3E%3Ccircle cx='50' cy='121' r='1.2' fill='%23fff' opacity='0.70'/%3E%3Ccircle cx='376' cy='79' r='0.9' fill='%23fff' opacity='0.70'/%3E%3Ccircle cx='217' cy='113' r='1.2' fill='%23fff' opacity='0.50'/%3E%3Ccircle cx='65' cy='20' r='0.9' fill='%23fff' opacity='0.90'/%3E%3Ccircle cx='177' cy='157' r='1.2' fill='%23fff' opacity='0.90'/%3E%3Ccircle cx='46' cy='144' r='2' fill='%23fff' opacity='0.90'/%3E%3Ccircle cx='260' cy='87' r='0.9' fill='%23fff' opacity='0.50'/%3E%3Ccircle cx='546' cy='79' r='2' fill='%23fff' opacity='0.90'/%3E%3Ccircle cx='210' cy='127' r='1.2' fill='%23fff' opacity='0.50'/%3E%3Ccircle cx='454' cy='110' r='2' fill='%23fff' opacity='0.50'/%3E%3Ccircle cx='229' cy='112' r='2' fill='%23fff' opacity='0.50'/%3E%3Ccircle cx='667' cy='114' r='1.2' fill='%23fff' opacity='0.70'/%3E%3Ccircle cx='197' cy='13' r='0.9' fill='%23fff' opacity='0.70'/%3E%3Ccircle cx='264' cy='67' r='1.2' fill='%23fff' opacity='0.50'/%3E%3Ccircle cx='432' cy='227' r='1.6' fill='%23fff' opacity='0.50'/%3E%3Ccircle cx='337' cy='18' r='1.6' fill='%23fff' opacity='0.90'/%3E%3Ccircle cx='124' cy='150' r='2' fill='%23fff' opacity='0.90'/%3E%3Ccircle cx='674' cy='227' r='0.9' fill='%23fff' opacity='0.70'/%3E%3Ccircle cx='401' cy='28' r='2' fill='%23fff' opacity='0.50'/%3E%3Ccircle cx='591' cy='47' r='1.6' fill='%23fff' opacity='0.70'/%3E%3Ccircle cx='677' cy='125' r='1.6' fill='%23fff' opacity='0.70'/%3E%3Ccircle cx='545' cy='60' r='1.6' fill='%23fff' opacity='0.70'/%3E%3Ccircle cx='406' cy='132' r='0.9' fill='%23fff' opacity='0.70'/%3E%3Ccircle cx='647' cy='176' r='1.2' fill='%23fff' opacity='0.50'/%3E%3Ccircle cx='409' cy='163' r='1.2' fill='%23fff' opacity='0.70'/%3E%3Ccircle cx='688' cy='224' r='0.9' fill='%23fff' opacity='0.50'/%3E%3Ccircle cx='709' cy='167' r='2' fill='%23fff' opacity='0.90'/%3E%3Ccircle cx='488' cy='196' r='2' fill='%23fff' opacity='0.70'/%3E%3Ccircle cx='228' cy='209' r='0.9' fill='%23fff' opacity='0.50'/%3E%3Ccircle cx='165' cy='8' r='1.6' fill='%23fff' opacity='0.50'/%3E%3Ccircle cx='410' cy='212' r='2' fill='%23fff' opacity='0.50'/%3E%3Ccircle cx='568' cy='18' r='1.2' fill='%23fff' opacity='0.50'/%3E%3Ccircle cx='692' cy='160' r='1.6' fill='%23fff' opacity='0.90'/%3E%3Ccircle cx='487' cy='139' r='2' fill='%23fff' opacity='0.50'/%3E%3Ccircle cx='85' cy='13' r='0.9' fill='%23fff' opacity='0.70'/%3E%3Ccircle cx='579' cy='226' r='1.6' fill='%23fff' opacity='0.90'/%3E%3Ccircle cx='147' cy='15' r='1.6' fill='%23fff' opacity='0.50'/%3E%3Ccircle cx='791' cy='230' r='0.9' fill='%23fff' opacity='0.70'/%3E%3Ccircle cx='109' cy='315' r='1.8' fill='%23fff' opacity='0.7'/%3E%3Ccircle cx='277' cy='376' r='2.4' fill='%23fff' opacity='0.7'/%3E%3Ccircle cx='298' cy='375' r='1.2' fill='%23fff' opacity='0.7'/%3E%3Ccircle cx='674' cy='230' r='2.4' fill='%23fff' opacity='0.7'/%3E%3Ccircle cx='477' cy='350' r='3' fill='%23fff' opacity='0.7'/%3E%3Ccircle cx='403' cy='60' r='2.4' fill='%23fff' opacity='0.7'/%3E%3Ccircle cx='228' cy='161' r='2.4' fill='%23fff' opacity='0.7'/%3E%3Ccircle cx='266' cy='184' r='1.8' fill='%23fff' opacity='0.7'/%3E%3Ccircle cx='164' cy='282' r='2.4' fill='%23fff' opacity='0.7'/%3E%3Ccircle cx='168' cy='5' r='1.2' fill='%23fff' opacity='0.7'/%3E%3Ccircle cx='125' cy='304' r='2.4' fill='%23fff' opacity='0.7'/%3E%3Ccircle cx='28' cy='42' r='2.4' fill='%23fff' opacity='0.7'/%3E%3Ccircle cx='209' cy='195' r='3' fill='%23fff' opacity='0.7'/%3E%3Ccircle cx='597' cy='224' r='1.2' fill='%23fff' opacity='0.7'/%3E%3Ccircle cx='656' cy='351' r='1.2' fill='%23fff' opacity='0.7'/%3E%3Ccircle cx='592' cy='301' r='2.4' fill='%23fff' opacity='0.7'/%3E%3Ccircle cx='185' cy='48' r='3' fill='%23fff' opacity='0.7'/%3E%3Ccircle cx='526' cy='344' r='1.8' fill='%23fff' opacity='0.7'/%3E%3Ccircle cx='277' cy='231' r='1.8' fill='%23fff' opacity='0.7'/%3E%3Ccircle cx='496' cy='144' r='2.4' fill='%23fff' opacity='0.7'/%3E%3Ccircle cx='104' cy='62' r='1.2' fill='%23fff' opacity='0.7'/%3E%3Ccircle cx='285' cy='143' r='1.2' fill='%23fff' opacity='0.7'/%3E%3Ccircle cx='28' cy='82' r='3' fill='%23fff' opacity='0.7'/%3E%3Ccircle cx='116' cy='351' r='1.2' fill='%23fff' opacity='0.7'/%3E%3Ccircle cx='435' cy='245' r='1.8' fill='%23fff' opacity='0.7'/%3E%3Ccircle cx='547' cy='197' r='3' fill='%23fff' opacity='0.7'/%3E%3Ccircle cx='316' cy='248' r='3' fill='%23fff' opacity='0.7'/%3E%3Ccircle cx='447' cy='213' r='1.2' fill='%23fff' opacity='0.7'/%3E%3Ccircle cx='262' cy='242' r='3' fill='%23fff' opacity='0.7'/%3E%3Ccircle cx='241' cy='230' r='3' fill='%23fff' opacity='0.7'/%3E%3Ccircle cx='68' cy='291' r='1.8' fill='%23fff' opacity='0.7'/%3E%3Ccircle cx='500' cy='360' r='2.4' fill='%23fff' opacity='0.7'/%3E%3Ccircle cx='585' cy='225' r='1.2' fill='%23fff' opacity='0.7'/%3E%3Ccircle cx='721' cy='207' r='3' fill='%23fff' opacity='0.7'/%3E%3Ccircle cx='21' cy='177' r='2.4' fill='%23fff' opacity='0.7'/%3E%3Ccircle cx='482' cy='134' r='2.4' fill='%23fff' opacity='0.7'/%3E%3Ccircle cx='309' cy='271' r='1.8' fill='%23fff' opacity='0.7'/%3E%3Ccircle cx='43' cy='321' r='3' fill='%23fff' opacity='0.7'/%3E%3Ccircle cx='262' cy='48' r='3' fill='%23fff' opacity='0.7'/%3E%3Ccircle cx='100' cy='17' r='1.8' fill='%23fff' opacity='0.7'/%3E%3Cpath d='M0,330Q120,290 250,325T520,320T800,310L800,500L0,500Z' fill='%232f3d72'/%3E%3Cpath d='M0,360Q160,320 330,355T620,345T800,350L800,500L0,500Z' fill='%2347568c'/%3E%3Cpath d='M0,405Q200,380 400,402T800,392L800,500L0,500Z' fill='url(%23gr)'/%3E%3Cuse href='%23tree' transform='translate(60,395) scale(1.1)'/%3E%3Cuse href='%23tree' transform='translate(215,400) scale(0.8)'/%3E%3Cuse href='%23tree' transform='translate(745,392) scale(1.05)'/%3E%3Cuse href='%23tree' transform='translate(660,402) scale(0.75)'/%3E%3Cuse href='%23stable'/%3E%3Cuse href='%23snowman' transform='translate(140,450) scale(0.85)'/%3E%3Cuse href='%23snowman' transform='translate(700,456) scale(0.7)'/%3E%3Cuse href='%23gift' transform='translate(250,436) scale(1)'/%3E%3Cuse href='%23gift2' transform='translate(560,440) scale(1)'/%3E%3Crect width='800' height='500' fill='%230a1030' fill-opacity='0.22'/%3E%3Cuse href='%23tl' transform='translate(60,395) scale(1.1)'/%3E%3Cuse href='%23tl' transform='translate(215,400) scale(0.8)'/%3E%3Cuse href='%23tl' transform='translate(745,392) scale(1.05)'/%3E%3Cuse href='%23tl' transform='translate(660,402) scale(0.75)'/%3E%3Cuse href='%23stableL'/%3E%3Cg transform='translate(400,100)'%3E%3Ccircle r='70' fill='url(%23halo)'/%3E%3Cpolygon points='0,-60 8,-8 60,0 8,8 0,60 -8,8 -60,0 -8,-8' fill='%23fff3b0'/%3E%3Cpolygon points='0,-30 4,-4 30,0 4,4 0,30 -4,4 -30,0 -4,-4' fill='%23fff'/%3E%3C/g%3E%3Cpath d='M400,150Q395,260 400,300' stroke='%23ffe9a0' stroke-opacity='.3' stroke-width='22' fill='none' stroke-linecap='round'/%3E%3C/svg%3E") center bottom / cover no-repeat,
                linear-gradient(180deg, #14234F 0%, #E9A86B 100%);
            /* El elemento que utiliza el fondo */
            /* Variables de Tarjeta Modo Claro */
            --card-bg: #ffffffec;
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

        /* =========================================================
   TARJETAS ADAPTATIVAS AL BRILLO DEL FONDO
   --fondo-alfa se ajusta automáticamente por JS según
   el brillo promedio de la imagen de fondo actual.
========================================================= */
        :root {
            --fondo-alfa: 0.80;
            /* valor por defecto mientras detecta */
        }

        body.fondo-claro {
            --fondo-alfa: 0.94;
        }

        /* fondo muy brillante → casi opacas */
        body.fondo-medio {
            --fondo-alfa: 0.86;
        }

        /* fondo medio → semitransparentes */
        body.fondo-oscuro {
            --fondo-alfa: 0.78;
        }

        /* fondo oscuro → más transparentes */

        /* Contenedores que deben respetar el alfa */
        html[data-bs-theme="light"] body {
            --bs-card-bg: rgba(255, 255, 255, var(--fondo-alfa));
            --bs-modal-bg: rgba(255, 255, 255, var(--fondo-alfa));
            --bs-offcanvas-bg: rgba(255, 255, 255, var(--fondo-alfa));
            --bs-dropdown-bg: rgba(255, 255, 255, var(--fondo-alfa));
            --bs-accordion-bg: rgba(255, 255, 255, var(--fondo-alfa));
            --bs-list-group-bg: rgba(255, 255, 255, var(--fondo-alfa));
        }

        html[data-bs-theme="dark"] body {
            --bs-card-bg: rgba(33, 37, 41, var(--fondo-alfa));
            --bs-modal-bg: rgba(33, 37, 41, var(--fondo-alfa));
            --bs-offcanvas-bg: rgba(33, 37, 41, var(--fondo-alfa));
            --bs-dropdown-bg: rgba(33, 37, 41, var(--fondo-alfa));
            --bs-accordion-bg: rgba(33, 37, 41, var(--fondo-alfa));
            --bs-list-group-bg: rgba(33, 37, 41, var(--fondo-alfa));
        }

        /* Transición suave cuando cambia de turno/tema */
        .card,
        .modal-content,
        .offcanvas,
        .dropdown-menu,
        .accordion-item,
        .list-group-item {
            transition: background-color 0.45s ease;
        }

        /* Si usas navbar "flotante" (bg-body), que también respete el alfa */
        .navbar.bg-body {
            background-color: rgba(var(--bs-body-bg-rgb, 255 255 255), var(--fondo-alfa)) !important;
        }

        /* En fondos muy brillantes, un pelín de sombra al texto ayuda a la legibilidad */
        body.fondo-claro .card-body,
        body.fondo-claro .modal-body,
        body.fondo-claro .offcanvas-body {
            text-shadow: 0 1px 1px rgba(0, 0, 0, 0.08);
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
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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
    <script>
        (function () {
            /* =========================================================
               Mide el brillo promedio de la imagen de fondo actual y
               aplica body.fondo-claro / fondo-medio / fondo-oscuro.
               No necesita saber cómo cambias el fondo: lo lee del body.
            ========================================================= */
            const CLASES = ['fondo-claro', 'fondo-medio', 'fondo-oscuro'];
            let ultimaUrl = '';

            // Luminancia perceptual Rec.709 con muestreo 48x48
            async function medirBrillo(url) {
                return new Promise((resolve) => {
                    const img = new Image();
                    img.crossOrigin = 'anonymous';          // por si algún día vienen de CDN
                    const timeout = setTimeout(() => resolve(null), 4000);

                    img.onload = () => {
                        clearTimeout(timeout);
                        try {
                            const c = document.createElement('canvas');
                            c.width = 48; c.height = 48;
                            const ctx = c.getContext('2d', { willReadFrequently: true });
                            ctx.drawImage(img, 0, 0, 48, 48);
                            const d = ctx.getImageData(0, 0, 48, 48).data;
                            let suma = 0;
                            for (let i = 0; i < d.length; i += 4) {
                                suma += 0.2126 * d[i] + 0.7152 * d[i + 1] + 0.0722 * d[i + 2];
                            }
                            resolve(suma / (d.length / 4)); // 0–255
                        } catch (e) {
                            resolve(null); // canvas tainted
                        }
                    };
                    img.onerror = () => { clearTimeout(timeout); resolve(null); };
                    img.src = url;
                });
            }

            // Saca la primera url(...) del background del body
            function extraerUrl(bg) {
                if (!bg || bg === 'none') return null;
                const m = bg.match(/url\(\s*(['"]?)(.*?)\1\s*\)/s);
                return m ? m[2] : null;
            }

            async function adaptar() {
                const bg = getComputedStyle(document.body).backgroundImage;
                const url = extraerUrl(bg);
                if (!url) return;

                // Si la imagen no cambió y ya tenemos clase aplicada, no re-trabajar
                const yaClasificado = CLASES.some(c => document.body.classList.contains(c));
                if (url === ultimaUrl && yaClasificado) return;
                ultimaUrl = url;

                const brillo = await medirBrillo(url);

                CLASES.forEach(c => document.body.classList.remove(c));

                if (brillo === null) {
                    // No se pudo medir (imagen rota o canvas tainted) → modo seguro
                    document.body.classList.add('fondo-claro');
                    return;
                }

                if (brillo >= 170) document.body.classList.add('fondo-claro');
                else if (brillo >= 90) document.body.classList.add('fondo-medio');
                else document.body.classList.add('fondo-oscuro');

                // Valor expuesto por si quieres usarlo en otros estilos
                document.documentElement.style.setProperty('--bg-brillo', brillo.toFixed(1));
            }

            // 1) Al cargar
            window.addEventListener('load', adaptar);

            // 2) Cuando cambia el tema (atributo data-bs-theme en <html>)
            new MutationObserver(adaptar).observe(document.documentElement, {
                attributes: true,
                attributeFilter: ['data-bs-theme']
            });

            // 3) Polling ligero: si tu lógica externa cambia solo el style del body,
            //    detectamos el nuevo background en <=2s. Coste: ~0 (solo mira una propiedad CSS).
            setInterval(adaptar, 2000);
        })();
    </script>
</body>

</html>