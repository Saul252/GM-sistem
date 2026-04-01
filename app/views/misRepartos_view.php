<?php
/**
 * CF SYSTEM - Logística Híbrida
 * Vista con segmentación de viajes Activos vs Terminados.
 */
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Logística | cfsistem</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <?php require_once __DIR__ . '/layout/icono.php' ?>
    <?php if (function_exists('cargarEstilos')) { cargarEstilos(); } ?>
    
    <style>
        :root { 
            --apple-bg: #f5f5f7;
            --accent-blue: #007aff;
            --accent-green: #34c759;
            --sidebar-width: 260px; /* Ajusta según el ancho real de tu sidebar */
        }

        body { 
            background-color: var(--apple-bg); 
            font-family: 'SF Pro Display', -apple-system, sans-serif;
            color: #1d1d1f;
            overflow-x: hidden;
        }

        /* --- ESTRUCTURA PRINCIPAL --- */
        .main-wrapper { 
            margin-left: var(--sidebar-width); 
            padding: 30px; 
            padding-top: 90px; 
            min-height: 100vh;
            transition: all 0.3s ease;
        }

        /* --- CARDS ESTILO IOS --- */
        .card-ios {
            background: #ffffff;
            border-radius: 18px;
            border: none;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            margin-bottom: 25px;
            overflow: hidden;
            animation: fadeInUp 0.5s ease;
        }

        .header-premium {
            background: #1d1d1f;
            color: white;
            padding: 15px 20px;
        }

        /* --- TABLAS OPTIMIZADAS --- */
        .table-monitor {
            width: 100%;
            margin: 0;
            border-collapse: collapse;
        }

        .table-monitor thead th {
            font-size: 0.65rem;
            color: #8e8e93;
            text-transform: uppercase;
            padding: 12px 10px;
            background: #f9f9fb;
            letter-spacing: 0.03em;
        }

        .table-monitor tbody td {
            padding: 14px 10px;
            vertical-align: middle;
            border-top: 1px solid #f2f2f7;
            font-size: 0.85rem;
        }

        .avatar-chofer {
            width: 38px; height: 38px;
            background: var(--accent-blue);
            color: white; border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.2rem;
        }

        .carga-scroll {
            background: #f5f5f7; 
            border-radius: 10px; 
            padding: 8px;
            font-size: 0.75rem; 
            max-height: 70px; 
            overflow-y: auto;
            color: #424245;
        }

        /* --- RESPONSIVE --- */
        @media (max-width: 992px) {
            .main-wrapper { margin-left: 0; padding: 15px; padding-top: 80px; }
        }

        @media (max-width: 767px) {
            .table-monitor thead { display: none; }
            .table-monitor tbody tr {
                display: block;
                padding: 15px;
                border-bottom: 8px solid #f2f2f7;
            }
            .table-monitor tbody td {
                display: flex;
                justify-content: space-between;
                padding: 6px 0;
                border: none;
                text-align: right;
            }
            .table-monitor td::before {
                content: attr(data-label);
                font-weight: 700;
                color: #8e8e93;
                font-size: 0.7rem;
                text-transform: uppercase;
                float: left;
            }
        }
    </style>
</head>
<body>
    <?php if (function_exists('renderizarLayout')) { renderizarLayout($paginaActual); } ?>

    <main class="main-wrapper">
        
        <div class="card-ios">
            <div class="header-premium d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold text-uppercase small">
                    <i class="bi bi-broadcast me-2 text-primary"></i> Unidades en Tránsito
                </h6>
                <button class="btn btn-sm btn-outline-light rounded-pill px-3 border-0 bg-white bg-opacity-10" onclick="cargarMonitorViajes()">
                    <i class="bi bi-arrow-repeat me-1"></i> Actualizar
                </button>
            </div>
            <div class="table-responsive">
                <table class="table-monitor align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4">Unidad / Folio</th>
                            <th>Chofer Responsable</th>
                            <th>Tripulación</th>
                            <th>Carga</th>
                            <th class="text-end pe-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="bodyMonitorViajes">
                        </tbody>
                </table>
            </div>
        </div>

        <div class="card-ios p-3">
            <div class="d-flex justify-content-between align-items-center">
                <h6 class="m-0 fw-bold"><i class="bi bi-list-check me-2"></i>Monitor de Entregas</h6>
                <select id="filtro_almacen_monitor" class="form-select form-select-sm border-0 bg-light rounded-3" style="width: auto;" onchange="cargarMonitor()">
                    <option value="0">Todos los Almacenes</option>
                    <?php if(isset($listaAlmacenes)) foreach ($listaAlmacenes as $alm): ?>
                        <option value="<?= $alm['id'] ?>"><?= $alm['nombre'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="card-ios">
            <div class="table-responsive">
                <table class="table-monitor">
                    <thead>
                        <tr>
                            <th class="text-center">Modo</th>
                            <th>Folio</th>
                            <th>Cliente</th>
                            <th>Producto</th>
                            <th>Cant.</th>
                            <th>Responsable</th>
                            <th class="text-center">Fecha</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="tbodyMonitor">
                        </tbody>
                </table>
            </div>
            <div class="p-3 text-center border-top">
                <button class="btn btn-sm btn-link text-decoration-none w-100 fw-bold text-primary" id="btnCargarMas" onclick="cargarMas()">
                    MOSTRAR MÁS REGISTROS
                </button>
            </div>
        </div>
    </main>

    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
let offsetActual = 0;
const limiteCarga = 25;

$(document).ready(function() { 
    cargarMonitor(); 
    cargarMonitorViajes();
});

// --- CARGAR MONITOR DE ENTREGAS (HISTORIAL) ---
function cargarMonitor() {
    offsetActual = 0;
    const idAlmacen = $('#filtro_almacen_monitor').val();
    $('#tbodyMonitor').html('<tr><td colspan="8" class="text-center py-5"><div class="spinner-border spinner-border-sm text-primary"></div></td></tr>');

    $.ajax({
        url: '/cfsistem/app/controllers/misRepartosController.php',
        type: 'GET',
        data: { action: 'get_monitor_entregas', almacen_id: idAlmacen, inicio: offsetActual, limite: limiteCarga },
        dataType: 'json',
        success: function(response) {
            if(response.success && response.data.length > 0) { renderizarFilas(response.data, false); }
            else { 
                $('#tbodyMonitor').html('<tr><td colspan="8" class="text-center text-muted py-5">No hay movimientos pendientes.</td></tr>'); 
                $('#btnCargarMas').hide();
            }
        }
    });
}

function renderizarFilas(data, append) {
    let html = '';
    data.forEach(row => {
        const esEnRuta = (row.estado_reparto === 'en_ruta');
        const icon = (row.tipo_salida === 'RUTA') ? '🚚' : '🏬';
        
        html += `
            <tr class="align-middle ${esEnRuta ? 'fw-bold' : ''}" ${esEnRuta ? 'style="background-color: #f0f7ff;"' : ''}>
                <td class="text-center" data-label="Modo">${icon}</td>
                <td data-label="Folio">
                    <span class="d-block">#${row.numero_ruta || row.reparto_id}</span>
                    <small class="badge bg-primary-subtle text-primary" style="font-size:0.6rem">${row.tipo_salida}</small>
                </td>
                <td data-label="Cliente"><div class="text-truncate" style="max-width:140px;">${row.cliente_display}</div></td>
                <td data-label="Producto"><div class="text-truncate" style="max-width:140px;">${row.producto_nombre}</div></td>
                <td data-label="Cantidad"><b>${row.lectura_fisica}</b></td>
                <td data-label="Responsable"><small>${row.responsable || '---'}</small></td>
                <td data-label="Fecha" class="text-center"><small>${row.fecha_evento || '---'}</small></td>
                <td class="text-end pe-3">
                    <button class="btn btn-sm btn-light rounded-circle shadow-sm" onclick="verDetalleEntrega('${row.tipo_salida}', ${row.reparto_id || row.movimiento_id})">
                        <i class="bi bi-chevron-right text-primary"></i>
                    </button>
                </td>
            </tr>`;
    });
    append ? $('#tbodyMonitor').append(html) : $('#tbodyMonitor').html(html);
}

// --- CARGAR MONITOR DE VIAJES (ACTIVOS) ---
window.cargarMonitorViajes = async function() {
    const body = $('#bodyMonitorViajes');
    try {
        body.html('<tr><td colspan="5" class="text-center py-5"><div class="spinner-border spinner-border-sm text-primary"></div></td></tr>');
        const resp = await fetch(`/cfsistem/app/controllers/misRepartosController.php?action=listar_viajes_activos`);
        const result = await resp.json();
        const data = result.data || result;

        if (!data || data.length === 0) {
            body.html('<tr><td colspan="5" class="text-center py-5 text-muted">No hay unidades activas en ruta</td></tr>');
            return;
        }

        body.empty();
        data.forEach(v => {
            body.append(`
                <tr class="animate__animated animate__fadeIn">
                    <td class="ps-4">
                        <div class="fw-bold">${v.unidad}</div>
                        <div class="badge bg-dark-subtle text-dark" style="font-size:0.65rem">#${v.viaje_folio}</div>
                    </td>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="avatar-chofer me-2"><i class="bi bi-person"></i></div>
                            <div class="fw-bold small text-uppercase">${v.chofer}</div>
                        </div>
                    </td>
                    <td><small class="text-muted">${v.tripulantes || 'Solo Conductor'}</small></td>
                    <td><div class="carga-scroll">${v.detalles_carga}</div></td>
                    <td class="text-end pe-4">
                        <div class="d-flex justify-content-end gap-2">
                            <a href="/cfsistem/app/controllers/gestionarRepartoController.php?folio=${v.viaje_folio}" 
   class="btn btn-sm rounded-pill px-3 fw-bold d-inline-flex align-items-center shadow-sm" 
   style="background-color: #007aff; color: #fff; font-size: 0.7rem; border: none; transition: all 0.2s ease;">
    <i class="bi bi-camera-fill me-1"></i> GESTIONAR
</a>
                        </div>
                    </td>
                </tr>
            `);
        });
    } catch (e) { body.html('<tr><td colspan="5" class="text-center text-danger">Error de conexión</td></tr>'); }
};



</script>

</body>
</html>