<?php
/**
 * repartos_view.php 
 * Módulo Logístico: Monitor de Viajes y Órdenes de Entrega
 * Sistema: cfsistem
 */

// Obtención del almacén del usuario actual desde la sesión
$mi_almacen = intval($_SESSION['almacen_id'] ?? 0);
?>
<!DOCTYPE html>
<html lang="es" data-bs-theme="auto">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logística | cfsistem</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="/cfsistem/public/assets/logo.png">
    <link rel="shortcut icon" href="/cfsistem/public/assets/logo.ico" type="image/x-icon">

    <!-- CSS Frameworks y Librerías Externas -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    
    <!-- Estilos Dinámicos y Específicos del Módulo -->
    <?php if (function_exists('cargarEstilos')) { cargarEstilos(); } ?>
    <link href="/cfsistem/css/repartos.css" rel="stylesheet">

    <!-- Estilos CSS Inline para Garantizar Compatibilidad en Modo Oscuro -->
    <style>
        /* Adaptación de tarjetas para modo claro y oscuro */
        .card-premium {
            background-color: var(--bs-card-bg);
            border: 1px solid var(--bs-border-color-translucent);
            border-radius: 16px;
            transition: background-color 0.3s ease, border-color 0.3s ease;
        }

        /* Form estilo iOS adaptable */
        .form-select-ios {
            background-color: var(--bs-body-bg);
            color: var(--bs-body-color);
            border: 1px solid var(--bs-border-color);
        }

        /* Badges personalizados para estatus de entrega */
        .badge-premium {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }
        .st-completado { background-color: rgba(25, 135, 84, 0.15); color: #198754; }
        .st-ruta { background-color: rgba(13, 110, 253, 0.15); color: #0d6efd; }
        .st-disponible { background-color: rgba(108, 117, 125, 0.15); color: var(--bs-secondary-color); }

        /* Estilo para los contenedores scrollables de carga */
        .carga-scroll {
            max-height: 60px;
            overflow-y: auto;
            color: var(--bs-body-color);
        }
        .badge-folio {
            background: var(--bs-tertiary-bg);
            color: var(--bs-body-color);
            padding: 2px 8px;
            border-radius: 6px;
            font-size: 0.75rem;
            display: inline-block;
        }
    </style>
</head>
<body class="bg-body-tertiary">
    <!-- Layout Principal/Navbar -->
    <?php if (function_exists('renderizarLayout')) { renderizarLayout($paginaActual); } ?>

    <main class="main-content container-fluid px-4 py-3">
        
        <!-- ENCABEZADO DE PÁGINA Y CONTADOR -->
        <div class="d-flex flex-wrap justify-content-between align-items-end mb-4 animate__animated animate__fadeIn gap-3">
            <div>
                <h2 class="fw-bold m-0 text-body" style="font-size: 2.2rem; letter-spacing: -0.04em;">Centro de Logística</h2>
                <p class="text-body-secondary mb-0" style="font-size: 1.1rem;">Supervisión de entregas y control de flota en tiempo real.</p>
            </div>
            
            <!-- Card con Contador Total de Órdenes Pendientes -->
            <div class="card-premium p-3 shadow-sm d-flex align-items-center gap-3" style="min-width: 200px;">
                <div class="text-primary fs-3"><i class="bi bi-truck-flatbed"></i></div>
                <div>
                    <small class="text-body-secondary fw-bold d-block" style="font-size: 0.6rem; letter-spacing: 0.05em;">ÓRDENES DE ENTREGA</small>
                    <span class="fs-4 fw-bold text-body" id="count_pendientes">0</span>
                </div>
            </div>
        </div>

        <!-- BARRA DE FILTROS: BUSCADOR, RANGOS Y ALMACÉN -->
        <div class="row g-3 mb-4 animate__animated animate__fadeInUp">
            <!-- Input de Búsqueda por Texto -->
            <div class="col-md-5">
                <div class="card-premium p-2 px-3 d-flex align-items-center shadow-sm">
                    <i class="bi bi-search text-body-secondary me-3 fs-5"></i>
                    <input type="text" id="buscarSalida" class="form-control border-0 bg-transparent py-2 shadow-none text-body" placeholder="Buscar por folio, cliente o producto...">
                </div>
            </div>
            
            <!-- Selector de Rango de Fechas -->
            <div class="col-md-4 d-flex align-items-center gap-2">
                <label class="form-label mb-0 fw-bold text-body-secondary small">Rango:</label>
                <input type="date" id="inicio" class="form-control shadow-sm border-0 bg-body text-body" style="border-radius: 12px;">
                <span class="text-body-secondary">-</span>
                <input type="date" id="fin" class="form-control shadow-sm border-0 bg-body text-body" style="border-radius: 12px;">
            </div>

            <!-- Combo de Selección de Sucursales / Almacenes -->
            <div class="col-md-3">
                <select id="filtroAlmacen" class="form-select form-select-ios h-100 shadow-sm border-0 text-body" onchange="cargarPendientes(); cargarMonitorViajes();" style="border-radius: 12px;">
                    <?php if (isset($es_admin) && $es_admin): ?>
                        <option value="0">🌐 Todas las Sucursales</option>
                    <?php endif;?>
                    <?php if(isset($listaAlmacenes)): foreach($listaAlmacenes as $alm): ?>
                        <option value="<?= $alm['id'] ?>">📍 <?= htmlspecialchars($alm['nombre']) ?></option>
                    <?php endforeach; endif; ?>
                </select>
            </div>
        </div>

        <!-- TABLA 1: MONITOR DE UNIDADES EN TRÁNSITO -->
        <div class="card card-premium mb-4 animate__animated animate__fadeInUp" style="animation-delay: 0.1s;">
            <div class="card-header bg-transparent border-bottom d-flex justify-content-between align-items-center py-3">
                <h6 class="mb-0 fw-bold text-uppercase small text-body">
                    <i class="bi bi-broadcast me-2 text-primary animate-pulse-soft"></i> Monitor de Unidades en Tránsito
                </h6>
                <button class="btn btn-sm btn-outline-secondary rounded-pill px-3" onclick="cargarMonitorViajes()">
                    <i class="bi bi-arrow-repeat me-1"></i> Actualizar Monitor
                </button>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light border-bottom">
                            <tr>
                                <th class="ps-4">Unidad / Folio Ruta</th>
                                <th>Chofer Responsable</th>
                                <th>Tripulación</th>
                                <th>Carga Consolidada</th>
                                <th class="text-end pe-4">Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="bodyMonitorViajes"></tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- TABLA 2: ÓRDENES DE ENTREGA Y PATIO (DISPONIBLES) -->
        <div class="card card-premium animate__animated animate__fadeInUp" style="animation-delay: 0.2s;">
            <div class="card-header bg-transparent border-bottom d-flex justify-content-between align-items-center py-3">
                <h6 class="mb-0 fw-bold text-uppercase small text-body">
                    <i class="bi bi-stack me-2 text-primary"></i> Órdenes de Entrega y Patio
                </h6>
                <button class="btn btn-sm btn-outline-secondary rounded-pill px-3" onclick="cargarPendientes()">
                    <i class="bi bi-arrow-clockwise me-1"></i> Sincronizar
                </button>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light border-bottom">
                            <tr>
                                <th class="ps-4">Folio / Fecha</th>
                                <th>Cliente</th>
                                <th>Producto / Detalle</th>
                                <th>Almacén Origen</th>
                                <th class="text-center">Estatus</th> 
                                <th class="text-end pe-4">Gestión</th>
                            </tr>
                        </thead>
                        <tbody id="bodyPendientes"></tbody>
                    </table>
                </div>
            </div>

            <!-- Paginación -->
            <div class="d-flex justify-content-between align-items-center px-4 py-3 border-top">
                <div class="small text-body-secondary fw-bold" id="pageIndicatorText" style="font-size: 0.7rem; letter-spacing: 0.05em;"></div>
                <nav><ul class="pagination pagination-sm mb-0" id="paginationBootstrap"></ul></nav>
            </div>
        </div>
        
        <!-- Componentes Modales (Incluidos vía PHP) -->
        <?php require_once __DIR__ . '/entregasComponets/repartoModal.php'; ?>
        <?php require_once __DIR__ . '/entregasComponets/editarRepartoModal.php'; ?>
        <?php require_once __DIR__ . '/entregasComponets/minitordeHistorialDeReparto.php'; ?>
        <?php require_once __DIR__ . '/entregasComponets/modalVerEntrega.php'; ?>
    </main>

    <!-- SCRIPTS JS GENERALES (Inclusión Única y Ordenada) -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- LÓGICA DE JAVASCRIPT DEL MÓDULO -->
    <script>
    // Endpoint global del controlador
    window.CONTROLLER = '/cfsistem/app/controllers/repartosController.php';

    // Variables globales para paginación y filtrado local
    let allData = [];
    let filteredData = [];
    let currentPage = 1;
    const rowsPerPage = 10;

    /**
     * 1. Carga las rutas y unidades que se encuentran actualmente activas en tránsito
     */
    window.cargarMonitorViajes = async function() {
        const body = $('#bodyMonitorViajes');
        const almacenId = $('#filtroAlmacen').val() || 0;
        
        try {
            body.html('<tr><td colspan="5" class="text-center py-5"><div class="spinner-border text-primary spinner-border-sm"></div><div class="mt-2 text-body-secondary small">Consultando satélite...</div></td></tr>');
            
            const resp = await fetch(`${window.CONTROLLER}?action=listar_viajes_activos&almacen_id=${almacenId}`);
            const result = await resp.json();
            const data = result.data || result; 

            if (!data || data.length === 0) {
                body.html('<tr><td colspan="5" class="text-center py-5 text-body-secondary opacity-50"><i class="bi bi-geo-alt fs-2 d-block mb-2"></i> No hay unidades activas en ruta</td></tr>');
                return;
            }

            body.empty();
            data.forEach(v => {
                const listaAyudantes = v.tripulantes 
                    ? `<div class="small text-body-secondary fw-medium"><i class="bi bi-people-fill me-1 text-primary"></i> ${v.tripulantes}</div>` 
                    : `<span class="badge bg-body-tertiary text-secondary fw-normal border" style="font-size:0.6rem;">Solo Conductor</span>`;
                
                body.append(`
                    <tr class="animate__animated animate__fadeIn border-bottom">
                        <td class="ps-4">
                            <div class="fw-bold text-body" style="font-size:0.95rem;">${v.unidad}</div>
                            <div class="badge-folio mt-1"><i class="bi bi-hash"></i>${v.viaje_folio}</div>
                            <div class="small text-body-secondary mt-1" style="font-size:0.7rem;">📍 ${v.almacen_nombre || 'N/A'}</div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar-chofer me-3 text-primary fs-4"><i class="bi bi-person-badge"></i></div>
                                <div>
                                    <div class="fw-bold text-uppercase text-body" style="font-size: 0.72rem;">${v.chofer}</div>
                                    <small class="text-body-secondary" style="font-size: 0.62rem;">Operador Logístico</small>
                                </div>
                            </div>
                        </td>
                        <td>${listaAyudantes}</td>
                        <td><div class="carga-scroll" style="font-size:0.75rem;">${v.detalles_carga}</div></td>
                        <td class="text-end pe-4">
                            <div class="d-flex justify-content-end align-items-center gap-2">
                                <button class="btn btn-sm btn-outline-primary" onclick="abrirModalEdicionViaje('${v.viaje_folio}', ${v.vehiculo_id}, ${v.chofer_id})" title="Editar Ruta">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger" onclick="confirmarCancelacionViaje(${v.vehiculo_id}, '${v.viaje_folio}')" title="Cancelar Viaje">
                                    <i class="bi bi-x-circle me-1"></i> CANCELAR
                                </button>
                                <button class="btn btn-success btn-sm" onclick="finalizarViaje(${v.vehiculo_id}, '${v.viaje_folio}')" title="Finalizar Viaje">
                                    <i class="bi bi-check2-all me-1"></i> FINALIZAR
                                </button>
                            </div>
                        </td>
                    </tr>
                `);
            });
        } catch (e) { 
            console.error(e);
            body.html('<tr><td colspan="5" class="text-center py-4 text-danger">Error de conexión al cargar viajes</td></tr>'); 
        }
    };

    /**
     * 2. Confirma la anulación completa de un viaje en curso
     */
    window.confirmarCancelacionViaje = function(vehiculoId, folioViaje) {
        Swal.fire({
            title: '¿Anular este viaje?',
            text: `Se cancelarán todas las entregas asociadas al folio ${folioViaje} y los materiales volverán a estar disponibles.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, cancelar ruta',
            cancelButtonText: 'Mantener activo',
            customClass: { popup: 'rounded-4 shadow' }
        }).then((result) => {
            if (result.isConfirmed) cancelarTodoElViaje(vehiculoId, folioViaje);
        });
    };

    /**
     * Petición HTTP para cancelar el viaje
     */
    async function cancelarTodoElViaje(vehiculoId, folioViaje) {
        try {
            const resp = await fetch(`${window.CONTROLLER}?action=cancelar_viaje_completo&folio=${folioViaje}&vehiculo_id=${vehiculoId}`);
            const res = await resp.json();
            if (res.success) {
                Swal.fire('Ruta Anulada', res.message, 'success').then(() => {
                    cargarMonitorViajes();
                    cargarPendientes();
                });
            } else {
                Swal.fire('Error', res.message || 'No se pudo anular la ruta', 'error');
            }
        } catch (e) {
            Swal.fire('Error de sistema', 'Ocurrió un error en la solicitud.', 'error');
        }
    }

    /**
     * 3. Carga las órdenes de entrega/pendientes según los filtros seleccionados
     */
    window.cargarPendientes = async function() {
        const body = $('#bodyPendientes');
        const idAlmacen = $('#filtroAlmacen').val() || 0;
        const fechaInicio = document.getElementById('inicio').value;
        const fechaFin = document.getElementById('fin').value;

        try {
            body.html('<tr><td colspan="6" class="text-center py-5"><div class="spinner-border text-primary spinner-border-sm"></div></td></tr>');
            
            const resp = await fetch(`${window.CONTROLLER}?action=listar_pendientes_ruta&almacen_id=${idAlmacen}&fecha_inicio=${fechaInicio}&fecha_fin=${fechaFin}`);
            const res = await resp.json();
            
            allData = res.success ? res.data : [];
            filteredData = [...allData];
            $('#count_pendientes').text(allData.length);
            currentPage = 1;
            renderTable();
        } catch (e) { 
            console.error(e);
            body.html('<tr><td colspan="6" class="text-center py-4 text-danger">Error al cargar las órdenes pendientes</td></tr>');
        }
    };

    /**
     * 4. Renderiza la tabla de pendientes con paginación local
     */
    function renderTable() {
        const body = $('#bodyPendientes');
        body.empty();
        const start = (currentPage - 1) * rowsPerPage;
        const end = start + rowsPerPage;
        const items = filteredData.slice(start, end);

        if (items.length === 0) {
            body.html('<tr><td colspan="6" class="text-center py-5 text-body-secondary">Bandeja de entrada vacía o sin resultados</td></tr>');
            return;
        }

        items.forEach(item => {
            let cantidad = parseFloat(item.cantidad) || 0;
            let factor = parseFloat(item.factor_conversion) || 1;
            let uReporte = item.unidad_reporte || 'Unid.';
            let uMedida = item.unidad_medida || 'Pz';
            let displayEntrega = "";

            if (factor > 1) {
                let enteros = Math.floor(cantidad / factor);
                let sobrantes = cantidad % factor;
                let partes = [];
                if (enteros > 0) partes.push(`<strong>${enteros}</strong> ${uReporte}`);
                if (sobrantes > 0) partes.push(`<strong>${sobrantes}</strong> ${uMedida}`);
                displayEntrega = partes.length > 0 ? partes.join(' + ') : `0 ${uMedida}`;
            } else {
                displayEntrega = `<strong>${cantidad}</strong> ${uMedida}`;
            }

            let badge = '';
            let btnAccion = '';
            const estado = (item.estado_reparto || '').toLowerCase().trim();

            if (estado === 'completado') {
                badge = '<span class="badge-premium st-completado"><i class="bi bi-check-circle-fill"></i> Entregado</span>';
                btnAccion = `<button class="btn btn-outline-success btn-sm rounded-pill px-3" onclick="verEntrega(${item.movimiento_id})"><i class="bi bi-eye"></i></button>`;
            } else if (estado === 'en_transito') {
                badge = '<span class="badge-premium st-ruta"><i class="bi bi-truck animate-pulse-soft"></i> En Tránsito</span>';
                btnAccion = `<button class="btn btn-outline-secondary btn-sm rounded-pill px-3" onclick="verEntrega(${item.movimiento_id})"><i class="bi bi-truck"></i></button>`;
            } else {
                badge = '<span class="badge-premium st-disponible"><i class="bi bi-house"></i> En Patio</span>';
                btnAccion = `<button class="btn btn-primary btn-sm rounded-pill px-3" onclick="prepararModalReparto(${item.movimiento_id}, ${item.almacen_origen_id})">ASIGNAR RUTA</button>`;
            }

            body.append(`
                <tr class="animate__animated animate__fadeIn border-bottom">
                    <td class="ps-4">
                        <div class="fw-bold text-body" style="font-size: 0.9rem;">#${item.folio_venta || 'S/F'}</div>
                        <div class="text-body-secondary" style="font-size: 0.75rem;">${item.fecha_format || ''}</div>
                    </td>
                    <td>
                        <div class="fw-bold text-body" style="font-size: 0.9rem;">${item.cliente || 'S/F'}</div>
                    </td>
                    <td>
                        <div class="fw-bold text-body" style="font-size: 0.85rem;">${item.producto}</div>
                        <div class="text-body-secondary small">${displayEntrega}</div>
                    </td>
                    <td><span class="small text-body-secondary fw-bold">📍 ${item.almacen_origen}</span></td>
                    <td class="text-center">${badge}</td>
                    <td class="text-end pe-4">${btnAccion}</td>
                </tr>
            `);
        });

        renderPagination();
    }

    /**
     * Generador dinámico de botones de Paginación
     */
    function renderPagination() {
        const totalPages = Math.ceil(filteredData.length / rowsPerPage);
        const container = $('#paginationBootstrap');
        container.empty();
        $('#pageIndicatorText').text(`PÁGINA ${currentPage} DE ${totalPages || 1}`);

        container.append(`<li class="page-item ${currentPage === 1 ? 'disabled' : ''}"><a class="page-link" href="javascript:void(0)" onclick="changePage(${currentPage - 1})">Anterior</a></li>`);
        for (let i = 1; i <= totalPages; i++) {
            if(i === 1 || i === totalPages || (i >= currentPage - 1 && i <= currentPage + 1)) {
                container.append(`<li class="page-item ${i === currentPage ? 'active' : ''}"><a class="page-link" href="javascript:void(0)" onclick="changePage(${i})">${i}</a></li>`);
            }
        }
        container.append(`<li class="page-item ${currentPage === totalPages || totalPages === 0 ? 'disabled' : ''}"><a class="page-link" href="javascript:void(0)" onclick="changePage(${currentPage + 1})">Siguiente</a></li>`);
    }

    window.changePage = function(p) { currentPage = p; renderTable(); };

    /**
     * 5. Finalizar viaje y registrar llegada de la unidad
     */
    window.finalizarViaje = async function(vehiculoId, folioRuta) {
        if (!confirm(`¿Confirmar llegada de la unidad ${folioRuta}?`)) return;
        try {
            const formData = new FormData();
            formData.append('vehiculo_id', vehiculoId);
            formData.append('viaje_folio', folioRuta);
            
            const resp = await fetch(`${window.CONTROLLER}?action=finalizar_viaje`, { method: 'POST', body: formData });
            const res = await resp.json();
            
            if (res.success) {
                Swal.fire('Éxito', res.message, 'success');
                cargarMonitorViajes();
                cargarPendientes();
            } else {
                Swal.fire('Error', res.message || 'No se pudo finalizar el viaje', 'error');
            }
        } catch (e) { 
            console.error(e); 
            Swal.fire('Error', 'Fallo al procesar la finalización', 'error');
        }
    };

    /**
     * Inicialización en Document Ready
     */
    $(document).ready(function() {
        // Carga inicial
        cargarPendientes();
        cargarMonitorViajes();

        // Filtro dinámico en tiempo real por buscador de texto
        $("#buscarSalida").on("keyup", function() {
            const val = $(this).val().toLowerCase();
            filteredData = allData.filter(i => 
                `${i.folio_venta} ${i.cliente} ${i.producto} ${i.almacen_origen}`.toLowerCase().includes(val)
            );
            currentPage = 1;
            renderTable();
        });

        // Evento 'change' para filtrado dinámico por fecha
        $("#inicio, #fin").on("change", function() {
            cargarPendientes();
        });
    });
    </script>
</body>
</html>