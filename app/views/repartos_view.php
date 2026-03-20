<?php
/**
 * repartos_view.php 
 * Gestión de logística: Pendientes en Patio, En Ruta y Completados
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
            --glass-bg: rgba(255, 255, 255, 0.92);
            --accent-color: #007aff;
            --ios-gray: #f2f2f7;
            --text-main: #1d1d1f;
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

        /* Card Estilo Premium */
        .card-premium {
            background: var(--glass-bg);
            backdrop-filter: blur(25px);
            border: 1px solid rgba(255,255,255,0.7);
            border-radius: 20px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.03);
            overflow: hidden;
            margin-bottom: 2rem;
        }

        .card-header-ios {
            background: rgba(255, 255, 255, 0.5);
            border-bottom: 1px solid rgba(0,0,0,0.04);
            padding: 1.2rem 1.5rem;
        }

        /* Badges de Estado */
        .badge-premium {
            padding: 5px 12px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.68rem;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        .st-disponible { background: #f2f2f7; color: #8e8e93; border: 1px solid #d1d1d6; }
        .st-ruta { background: #e8f4ff; color: #007aff; border: 1px solid #cce5ff; }
        .st-completado { background: #e6ffed; color: #1a7f37; border: 1px solid #bef5cb; }

        /* Botones */
        .btn-gradient {
            background: #007aff; color: white; border: none; border-radius: 12px;
            padding: 10px 24px; font-weight: 500; transition: all 0.2s;
        }
        .btn-gradient:hover { background: #0066cc; transform: translateY(-1px); color: white; }

        .form-select-ios {
            border-radius: 12px; border: 1px solid transparent;
            background-color: var(--ios-gray); padding: 10px 16px;
        }

        @media (max-width: 768px) { .main-content { margin-left: 0; padding: 20px; } }
    </style>
</head>
<body>
    <?php if (function_exists('renderizarLayout')) { renderizarLayout($paginaActual); } ?>

    <main class="main-content">
        <div id="contenedor-monitor-dinamico"></div>

        <div class="d-flex justify-content-between align-items-center mb-5 animate__animated animate__fadeIn">
            <div>
                <h2 class="fw-bold m-0" style="font-size: 1.8rem;">Logística de Salidas</h2>
                <p class="text-muted mb-0">Gestión de carga y asignación de unidades</p>
            </div>
            <div class="bg-white rounded-4 p-3 shadow-sm border d-flex align-items-center gap-3">
                <div class="text-primary fs-3"><i class="bi bi-box-seam-fill"></i></div>
                <div>
                    <small class="text-muted fw-bold d-block" style="font-size: 0.6rem;">REGISTROS TOTALES</small>
                    <span class="fs-4 fw-bold" id="count_pendientes">0</span>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-4 animate__animated animate__fadeInUp">
            <div class="col-md-8">
                <div class="card-premium p-2 px-3 mb-0 d-flex align-items-center shadow-sm">
                    <i class="bi bi-search text-muted me-3"></i>
                    <input type="text" id="buscarSalida" class="form-control border-0 bg-transparent py-2" placeholder="Buscar por folio o producto...">
                </div>
            </div>
            <div class="col-md-4">
                <select id="filtroAlmacen" class="form-select-ios h-100 w-100 shadow-sm" onchange="cargarPendientes()">
                    <option value="0">--- Todas las Sucursales ---</option>
                    <?php if(isset($listaAlmacenes)) foreach($listaAlmacenes as $alm): ?>
                        <option value="<?= $alm['id'] ?>"><?= $alm['nombre'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="card card-premium animate__animated animate__fadeInUp">
            <div class="card-header-ios d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold text-uppercase small"><i class="bi bi-list-ul me-2 text-primary"></i> Ordenes de Entrega</h6>
                <button class="btn btn-sm btn-light border" onclick="cargarPendientes()">
                    <i class="bi bi-arrow-clockwise"></i>
                </button>
            </div>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th class="ps-4">Folio / Fecha</th>
                            <th>Producto / Detalle</th>
                            <th>Almacén</th>
                            <th class="text-center">Estado</th> 
                            <th class="text-end pe-4">Gestión</th>
                        </tr>
                    </thead>
                    <tbody id="bodyPendientes"></tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between align-items-center px-4 py-4 border-top bg-light-subtle">
                <div class="small text-muted fw-medium" id="pageIndicatorText"></div>
                <nav><ul class="pagination mb-0" id="paginationBootstrap"></ul></nav>
            </div>
        </div>
    </main>

    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <?php require_once __DIR__ . '/entregasComponets/repartoModal.php'; ?>
    <?php require_once __DIR__ . '/entregasComponets/monitorDeRuta.php'; ?>

<script>
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
        body.html('<tr><td colspan="5" class="text-center py-5 text-muted">No se encontraron registros.</td></tr>');
        return;
    }

    items.forEach(item => {
        let badge = '';
        let btnAccion = '';
        const estado = (item.estado_reparto || '').toLowerCase().trim();

        if (estado === 'completado') {
            badge = '<span class="badge-premium st-completado"><i class="bi bi-check-circle-fill"></i> COMPLETADO</span>';
            btnAccion = `<button class="btn btn-outline-success btn-sm rounded-pill px-3" onclick="verEntrega(${item.movimiento_id})"><i class="bi bi-eye"></i> Ver</button>`;
        } 
        else if (estado === 'en_transito') {
            badge = '<span class="badge-premium st-ruta"><i class="bi bi-truck"></i> EN CAMINO</span>';
            btnAccion = `<button class="btn btn-light btn-sm rounded-pill border shadow-sm px-3" onclick="imprimirReparto(${item.movimiento_id})"><i class="bi bi-printer"></i> Ticket</button>`;
        } 
        else {
            badge = '<span class="badge-premium st-disponible"><i class="bi bi-house-door"></i> EN PATIO</span>';
            btnAccion = `<button class="btn btn-gradient btn-sm px-3" onclick="prepararModalReparto(${item.movimiento_id}, ${item.almacen_origen_id})">ASIGNAR RUTA</button>`;
        }

        body.append(`
            <tr class="animate__animated animate__fadeIn">
                <td class="ps-4">
                    <div class="fw-bold text-dark">#${item.folio_venta || 'S/F'}</div>
                    <div class="text-muted small">${item.fecha_format || ''}</div>
                </td>
                <td>
                    <div class="fw-medium">${item.producto}</div>
                    <div class="text-muted small">${item.cantidad} ${item.unidad_reporte}</div>
                </td>
                <td><span class="small text-muted">${item.almacen_origen}</span></td>
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
    $('#pageIndicatorText').text(`Página ${currentPage} de ${totalPages || 1}`);

    container.append(`<li class="page-item ${currentPage === 1 ? 'disabled' : ''}"><a class="page-link" href="javascript:void(0)" onclick="changePage(${currentPage - 1})"><</a></li>`);
    for (let i = 1; i <= totalPages; i++) {
        if(i === 1 || i === totalPages || (i >= currentPage - 1 && i <= currentPage + 1)) {
            container.append(`<li class="page-item ${i === currentPage ? 'active' : ''}"><a class="page-link" href="javascript:void(0)" onclick="changePage(${i})">${i}</a></li>`);
        }
    }
    container.append(`<li class="page-item ${currentPage === totalPages || totalPages === 0 ? 'disabled' : ''}"><a class="page-link" href="javascript:void(0)" onclick="changePage(${currentPage + 1})">></a></li>`);
}

window.changePage = function(p) {
    currentPage = p;
    renderTable();
};

$(document).ready(function() {
    cargarPendientes();
    $("#buscarSalida").on("keyup", function() {
        const val = $(this).val().toLowerCase();
        filteredData = allData.filter(i => `${i.folio_venta} ${i.producto}`.toLowerCase().includes(val));
        currentPage = 1;
        renderTable();
    });
});
</script>
</body>
</html>