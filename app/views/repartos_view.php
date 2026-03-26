<?php
/**
 * repartos_view.php 
 * Gestión de logística: Monitor de Viajes y Órdenes de Entrega
 */
$mi_almacen = intval($_SESSION['almacen_id'] ?? 0);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logística | cfsistem</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
     <?php if (function_exists('cargarEstilos')) { cargarEstilos(); } ?>
   
    <style>
        :root { 
            --sidebar-width: 260px; 
            --navbar-height: 65px;
            --glass-bg: rgba(255, 255, 255, 0.95);
            --accent-color: #007aff;
            --ios-gray: #f2f2f7;
            --text-main: #1d1d1f;
            --apple-dark: #1d1d1f;
        }

        body { 
            background: #f5f5f7;
            min-height: 100vh;
            font-family: 'SF Pro Display', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            color: var(--text-main);
            letter-spacing: -0.015em;
        }

        .main-content { 
            margin-left: var(--sidebar-width); 
            padding: 40px; 
            padding-top: calc(var(--navbar-height) + 20px); 
        }

        /* --- CONTENEDORES PREMIUM --- */
        .card-premium {
            background: var(--glass-bg);
            backdrop-filter: blur(25px);
            -webkit-backdrop-filter: blur(25px);
            border: 1px solid rgba(255,255,255,0.7);
            border-radius: 24px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.04);
            overflow: hidden;
            margin-bottom: 2rem;
            transition: transform 0.3s ease;
        }

        .card-header-ios {
            background: rgba(255, 255, 255, 0.5);
            border-bottom: 1px solid rgba(0,0,0,0.04);
            padding: 1.2rem 1.5rem;
        }

        /* --- MONITOR DE VIAJES (CABECERA OSCURA) --- */
        .header-monitor {
            background: var(--apple-dark);
            color: white;
            padding: 1.2rem 1.5rem;
            border: none;
        }

        .table-monitor thead th {
            font-size: 0.72rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #86868b;
            font-weight: 600;
            padding: 1.2rem;
            border-bottom: 2px solid #f2f2f7;
        }

        /* --- TABLAS Y ELEMENTOS --- */
        .table thead th {
            background: #fbfbfd;
            font-size: 0.75rem;
            color: #86868b;
            text-transform: uppercase;
            font-weight: 600;
            padding: 1rem;
            border-bottom: 1px solid #f2f2f7;
        }

        .avatar-chofer {
            width: 38px; height: 38px;
            background: var(--accent-color);
            color: white; border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.1rem; box-shadow: 0 4px 12px rgba(0, 122, 255, 0.2);
        }

        .badge-premium {
            padding: 6px 14px; border-radius: 10px;
            font-weight: 700; font-size: 0.65rem;
            display: inline-flex; align-items: center; gap: 6px;
            text-transform: uppercase; letter-spacing: 0.3px;
        }
        .st-disponible { background: #f2f2f7; color: #1d1d1f; border: 1px solid #d1d1d6; }
        .st-ruta { background: rgba(0, 122, 255, 0.1); color: #007aff; border: 1px solid rgba(0, 122, 255, 0.1); }
        .st-completado { background: rgba(52, 199, 89, 0.1); color: #28a745; border: 1px solid rgba(52, 199, 89, 0.1); }

        .carga-scroll {
            background: #f5f5f7; border-radius: 14px; padding: 12px;
            font-size: 0.82rem; color: #424245; max-height: 110px;
            overflow-y: auto; border: 1px solid rgba(0,0,0,0.03);
        }

        /* --- BOTONES --- */
        .btn-finish {
            background: #34c759; color: white; border: none; border-radius: 12px;
            padding: 8px 20px; font-weight: 600; transition: all 0.3s;
        }
        .btn-finish:hover { background: #28a745; transform: scale(1.02); box-shadow: 0 4px 15px rgba(52, 199, 89, 0.3); }

        .btn-gradient {
            background: var(--accent-color); color: white; border: none; border-radius: 12px;
            padding: 10px 24px; font-weight: 600; font-size: 0.75rem; transition: all 0.3s;
        }
        .btn-gradient:hover { background: #0066cc; transform: translateY(-1px); box-shadow: 0 6px 20px rgba(0, 122, 255, 0.25); }

        .form-select-ios {
            border-radius: 14px; border: 1px solid rgba(0,0,0,0.05);
            background-color: white; padding: 10px 18px; font-weight: 500;
        }

        .badge-folio {
            background: #e8f4ff; color: #007aff;
            font-family: 'SF Mono', monospace; font-weight: 700;
            padding: 4px 10px; border-radius: 8px; font-size: 0.68rem;
        }

        @keyframes pulse-soft {
            0% { opacity: 1; } 50% { opacity: 0.5; } 100% { opacity: 1; }
        }
        .animate-pulse-soft { animation: pulse-soft 2s infinite ease-in-out; }

        @media (max-width: 768px) { .main-content { margin-left: 0; padding: 20px; } }
    </style>
</head>
<body>
    <?php if (function_exists('renderizarLayout')) { renderizarLayout($paginaActual); } ?>

    <main class="main-content">
        
        <div class="d-flex justify-content-between align-items-end mb-4 animate__animated animate__fadeIn">
            <div>
                <h2 class="fw-bold m-0" style="font-size: 2.2rem; letter-spacing: -0.04em;">Centro de Logística</h2>
                <p class="text-muted mb-0" style="font-size: 1.1rem;">Supervisión de entregas y control de flota en tiempo real.</p>
            </div>
            <div class="bg-white rounded-4 p-3 shadow-sm border d-flex align-items-center gap-3" style="min-width: 200px;">
                <div class="text-primary fs-3"><i class="bi bi-truck-flatbed"></i></div>
                <div>
                    <small class="text-muted fw-bold d-block" style="font-size: 0.6rem; letter-spacing: 0.05em;">ÓRDENES PENDIENTES</small>
                    <span class="fs-4 fw-bold" id="count_pendientes">0</span>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-4 animate__animated animate__fadeInUp">
            <div class="col-md-8">
                <div class="card-premium p-2 px-3 mb-0 d-flex align-items-center shadow-sm" style="border-radius: 16px;">
                    <i class="bi bi-search text-muted me-3 fs-5"></i>
                    <input type="text" id="buscarSalida" class="form-control border-0 bg-transparent py-2 shadow-none" placeholder="Buscar por folio, cliente o producto...">
                </div>
            </div>
            <div class="col-md-4">
                <select id="filtroAlmacen" class="form-select-ios h-100 w-100 shadow-sm" onchange="cargarPendientes(); cargarMonitorViajes();">
                    <option value="0">🌐 Todas las Sucursales</option>
                    <?php if(isset($listaAlmacenes)) foreach($listaAlmacenes as $alm): ?>
                        <option value="<?= $alm['id'] ?>">📍 <?= $alm['nombre'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="card card-premium card-monitor animate__animated animate__fadeInUp" style="animation-delay: 0.1s;">
            <div class="header-monitor d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold text-uppercase small">
                    <i class="bi bi-broadcast me-2 text-primary animate-pulse-soft"></i> Monitor de Unidades en Tránsito
                </h6>
                <button class="btn btn-sm btn-outline-light rounded-pill px-3 border-opacity-25" onclick="cargarMonitorViajes()">
                    <i class="bi bi-arrow-repeat me-1"></i> Actualizar Monitor
                </button>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-monitor align-middle mb-0">
                        <thead>
                            <tr>
                                <th class="ps-4">Unidad / Folio Ruta</th>
                                <th>Chofer Responsable</th>
                                <th>Tripulación</th>
                                <th>Carga Consolidada</th>
                                <th class="text-end pe-4">Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="bodyMonitorViajes">
                            </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="card card-premium animate__animated animate__fadeInUp" style="animation-delay: 0.2s;">
            <div class="card-header-ios d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold text-uppercase small" style="color: #424245;">
                    <i class="bi bi-stack me-2 text-primary"></i> Órdenes de Entrega y Patio
                </h6>
                <button class="btn btn-sm btn-light border rounded-pill px-3" onclick="cargarPendientes()">
                    <i class="bi bi-arrow-clockwise me-1"></i> Sincronizar
                </button>
            </div>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th class="ps-4">Folio / Fecha</th>
                            <th>Producto / Detalle</th>
                            <th>Almacén Origen</th>
                            <th class="text-center">Estatus</th> 
                            <th class="text-end pe-4">Gestión</th>
                        </tr>
                    </thead>
                    <tbody id="bodyPendientes">
                        </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between align-items-center px-4 py-3 border-top bg-light-subtle">
                <div class="small text-muted fw-bold" id="pageIndicatorText" style="font-size: 0.7rem; letter-spacing: 0.05em;"></div>
                <nav><ul class="pagination pagination-sm mb-0" id="paginationBootstrap"></ul></nav>
            </div>
        </div>
    </main>

    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <?php require_once __DIR__ . '/entregasComponets/repartoModal.php'; ?>
<?php require_once __DIR__ . '/entregasComponets/editarRepartoModal.php'; ?>
<?php require_once __DIR__ . '/entregasComponets/minitordeHistorialDeReparto.php'; ?>
<?php require_once __DIR__ . '/entregasComponets/modalVerEntrega.php' ?>

    <script>
    // --- LÓGICA DE MONITOR DE VIAJES ---
   window.cargarMonitorViajes = async function() {
    const body = $('#bodyMonitorViajes');
    const almacenId = $('#filtroAlmacen').val() || 0;
    
    try {
        body.html('<tr><td colspan="5" class="text-center py-5"><div class="spinner-border text-primary spinner-border-sm"></div><div class="mt-2 text-muted small">Consultando satélite...</div></td></tr>');
        
        const resp = await fetch(`/cfsistem/app/controllers/repartosController.php?action=listar_viajes_activos&almacen_id=${almacenId}`);
        const result = await resp.json();
        const data = result.data || result; 

        if (!data || data.length === 0) {
            body.html('<tr><td colspan="5" class="text-center py-5 text-muted opacity-50"><i class="bi bi-geo-alt fs-2 d-block mb-2"></i> No hay unidades activas en ruta</td></tr>');
            return;
        }

        body.empty();
        data.forEach(v => {
            const listaAyudantes = v.tripulantes 
                ? `<div class="small text-muted fw-medium"><i class="bi bi-people-fill me-1 text-primary"></i> ${v.tripulantes}</div>`
                : `<span class="badge bg-light text-secondary fw-normal border" style="font-size:0.6rem;">Solo Conductor</span>`;

            body.append(`
                <tr class="animate__animated animate__fadeIn border-bottom" style="border-color: #f2f2f7 !important;">
                    <td class="ps-4">
                        <div class="fw-bold text-dark" style="font-size:0.95rem; letter-spacing:-0.01em;">${v.unidad}</div>
                        <div class="badge-folio mt-1"><i class="bi bi-hash"></i>${v.viaje_folio}</div>
                        <div class="small text-muted mt-1" style="font-size:0.7rem;">📍 ${v.almacen_nombre || 'N/A'}</div>
                    </td>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="avatar-chofer me-3">
                                <i class="bi bi-person-badge"></i>
                            </div>
                            <div>
                                <div class="fw-bold text-uppercase" style="font-size: 0.72rem; color:#1d1d1f; letter-spacing:0.02em;">${v.chofer}</div>
                                <small class="text-muted" style="font-size: 0.62rem;">Operador Logístico</small>
                            </div>
                        </div>
                    </td>
                    <td>${listaAyudantes}</td>
                    <td><div class="carga-scroll" style="font-size:0.75rem; color:#424245;">${v.detalles_carga}</div></td>
                    <td class="text-end pe-4">
                    <button class="btn btn-sm btn-light border-0" 
                onclick="abrirModalEdicionViaje('${v.viaje_folio}', ${v.vehiculo_id}, ${v.chofer_id})"
                style="border-radius: 10px; color: #007aff; background: #f2f2f7;">
            <i class="bi bi-pencil-square"></i>
        </button>
                        <div class="d-flex justify-content-end" style="gap: 8px;">
                            <button class="btn btn-sm d-flex align-items-center justify-content-center" 
                                    onclick="confirmarCancelacionViaje(${v.vehiculo_id}, '${v.viaje_folio}')"
                                    style="background: #fff; color: #ff3b30; border: 1px solid #ff3b30; border-radius: 10px; padding: 6px 12px; font-weight: 600; font-size: 0.68rem; transition: all 0.3s ease;">
                                <i class="bi bi-x-circle me-1"></i> CANCELAR
                            </button>

                            <button class="btn btn-finish btn-sm d-flex align-items-center justify-content-center" 
                                    onclick="finalizarViaje(${v.vehiculo_id}, '${v.viaje_folio}')"
                                    style="background: #14c41d; color: #fff; border: none; border-radius: 10px; padding: 6px 14px; font-weight: 600; font-size: 0.68rem;">
                                <i class="bi bi-check2-all me-1"></i> FINALIZAR
                            </button>
                        </div>
                    </td>
                </tr>
            `);
        });
    } catch (e) { 
        body.html('<tr><td colspan="5" class="text-center py-4 text-danger">Error de conexión</td></tr>');
    }
};

// --- 1. FUNCIÓN DE CONFIRMACIÓN (TUYA, AJUSTADA) ---
window.confirmarCancelacionViaje = function(vehiculoId, folioViaje) {
    Swal.fire({
        title: '¿Anular este viaje?',
        text: `Se cancelarán todas las entregas asociadas al folio ${folioViaje} y los materiales volverán a estar disponibles.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ff3b30',
        cancelButtonColor: '#8e8e93',
        confirmButtonText: 'Sí, cancelar ruta',
        cancelButtonText: 'Mantener activo',
        customClass: {
            popup: 'rounded-4 shadow',
            confirmButton: 'rounded-3 px-4',
            cancelButton: 'rounded-3 px-4'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            // Llamamos a la función que realmente ejecuta el borrado
            cancelarTodoElViaje(vehiculoId, folioViaje);
        }
    });
};

// --- 2. FUNCIÓN DE EJECUCIÓN (LA QUE SE COMUNICA CON EL CONTROLLER) ---
async function cancelarTodoElViaje(vehiculoId, folioViaje) {
    try {
        // Mostramos un loader sutil para que el usuario sepa que se está procesando
        $('#loader').removeClass('d-none');

        const resp = await fetch(`/cfsistem/app/controllers/repartosController.php?action=cancelar_viaje_completo&folio=${folioViaje}&vehiculo_id=${vehiculoId}`);
        const res = await resp.json();

        if (res.success) {
            Swal.fire({
                title: 'Ruta Anulada',
                text: res.message,
                icon: 'success',
                confirmButtonColor: '#1c1c1e',
                customClass: { popup: 'rounded-4' }
            });
            // Recargamos el monitor de viajes para que la unidad desaparezca
            window.cargarMonitorViajes();
        } else {
            Swal.fire('Error', res.message || 'No se pudo anular', 'error');
        }
    } catch (e) {
        console.error(e);
        Swal.fire('Error de sistema', 'Ocurrió un error al conectar con el satélite.', 'error');
    } finally {
        $('#loader').addClass('d-none');
    }
}
    window.CONTROLLER = '/cfsistem/app/controllers/repartosController.php';
    let allData = [];
    let filteredData = [];
    let currentPage = 1;
    const rowsPerPage = 10;

    window.cargarPendientes = async function() {
        const body = $('#bodyPendientes');
        const idAlmacen = $('#filtroAlmacen').val();
        try {
            body.html('<tr><td colspan="5" class="text-center py-5"><div class="spinner-border text-primary spinner-border-sm"></div></td></tr>');
            const resp = await fetch(`${window.CONTROLLER}?action=listar_pendientes_ruta&almacen_id=${idAlmacen}`);
            const res = await resp.json();
            
            allData = res.success ? res.data : [];
            filteredData = [...allData];
            $('#count_pendientes').text(allData.length);
            currentPage = 1;
            renderTable();
        } catch (e) { console.error(e); }
    };













function renderTable() {
    const body = $('#bodyPendientes');
    body.empty();
    const start = (currentPage - 1) * rowsPerPage;
    const end = start + rowsPerPage;
    const items = filteredData.slice(start, end);

    if (items.length === 0) {
        body.html('<tr><td colspan="5" class="text-center py-5 text-muted">Bandeja de entrada vacía</td></tr>');
        return;
    }

    items.forEach(item => {
        // --- LÓGICA DE CÁLCULO DE UNIDADES (SIN CEROS) ---
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

            // Unimos con " + " solo si existen ambas partes
            displayEntrega = partes.length > 0 ? partes.join(' + ') : `0 ${uMedida}`;
        } else {
            displayEntrega = `<strong>${cantidad}</strong> ${uMedida}`;
        }

        // --- LÓGICA DE BADGES Y BOTONES ---
        let badge = '';
        let btnAccion = '';
        const estado = (item.estado_reparto || '').toLowerCase().trim();

        if (estado === 'completado') {
            badge = '<span class="badge-premium st-completado"><i class="bi bi-check-circle-fill"></i> Entregado</span>';
            btnAccion = `<button class="btn btn-outline-success btn-sm rounded-pill px-3" onclick="verEntrega(${item.movimiento_id})"><i class="bi bi-eye"></i></button>`;
        } 
        else if (estado === 'en_transito') {
            badge = '<span class="badge-premium st-ruta"><i class="bi bi-truck animate-pulse-soft"></i> En Tránsito</span>';
            btnAccion = `<button class="btn btn-light btn-sm rounded-pill border shadow-sm px-3" onclick="verEntrega(${item.movimiento_id})"><i class="bi-truck"></i></button>`;
        } 
        else {
            badge = '<span class="badge-premium st-disponible"><i class="bi bi-house"></i> En Patio</span>';
            btnAccion = `<button class="btn btn-gradient btn-sm px-3" onclick="prepararModalReparto(${item.movimiento_id}, ${item.almacen_origen_id})">ASIGNAR RUTA</button>`;
        }

        // --- RENDERIZADO DE LA FILA ---
        body.append(`
            <tr class="animate__animated animate__fadeIn">
                <td class="ps-4">
                    <div class="fw-bold text-dark" style="font-size: 0.9rem;">#${item.folio_venta || 'S/F'}</div>
                    <div class="text-muted" style="font-size: 0.75rem;">${item.fecha_format || ''}</div>
                </td>
                <td>
                    <div class="fw-bold text-dark" style="font-size: 0.85rem;">${item.producto}</div>
                    <div class="text-muted small">${displayEntrega}</div>
                </td>
                <td><span class="small text-muted fw-bold">📍 ${item.almacen_origen}</span></td>
                <td class="text-center">${badge}</td>
                <td class="text-end pe-4">${btnAccion}</td>
            </tr>
        `);
    });
    renderPagination();
}

















    function renderPagination() {
        const totalPages = Math.ceil(filteredData.length / rowsPerPage);
        const container = $('#paginationBootstrap');
        container.empty();
        $('#pageIndicatorText').text(`VISUALIZANDO PÁGINA ${currentPage} DE ${totalPages || 1}`);

        container.append(`<li class="page-item ${currentPage === 1 ? 'disabled' : ''}"><a class="page-link" href="javascript:void(0)" onclick="changePage(${currentPage - 1})">Anterior</a></li>`);
        for (let i = 1; i <= totalPages; i++) {
            if(i === 1 || i === totalPages || (i >= currentPage - 1 && i <= currentPage + 1)) {
                container.append(`<li class="page-item ${i === currentPage ? 'active' : ''}"><a class="page-link" href="javascript:void(0)" onclick="changePage(${i})">${i}</a></li>`);
            }
        }
        container.append(`<li class="page-item ${currentPage === totalPages || totalPages === 0 ? 'disabled' : ''}"><a class="page-link" href="javascript:void(0)" onclick="changePage(${currentPage + 1})">Siguiente</a></li>`);
    }

    window.changePage = function(p) { currentPage = p; renderTable(); };

    window.finalizarViaje = async function(vehiculoId, folioRuta) {
        if (!confirm(`¿Confirmar llegada de la unidad ${folioRuta}?`)) return;
        try {
            const formData = new FormData();
            formData.append('vehiculo_id', vehiculoId);
            formData.append('viaje_folio', folioRuta);
            const resp = await fetch(`/cfsistem/app/controllers/repartosController.php?action=finalizar_viaje`, { method: 'POST', body: formData });
            const res = await resp.json();
            if (res.success) {
                Swal.fire('Éxito', res.message, 'success');
                cargarMonitorViajes();
                cargarPendientes();
            }
        } catch (e) { console.error(e); }
    };

    $(document).ready(function() {
        cargarPendientes();
        cargarMonitorViajes();
        $("#buscarSalida").on("keyup", function() {
            const val = $(this).val().toLowerCase();
            filteredData = allData.filter(i => `${i.folio_venta} ${i.producto} ${i.almacen_origen}`.toLowerCase().includes(val));
            currentPage = 1;
            renderTable();
        });
    });
    </script>
</body>
</html>