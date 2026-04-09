<?php 
/**
 * Vista: Monitor de Corte de Caja y Auditoría
 * Ubicación: views/corteCaja_view.php
 */
date_default_timezone_set('America/Mexico_City'); 
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Corte de Caja | Monitor de Auditoría</title>
    <?php require_once __DIR__ . '/layout/icono.php' ?>
    <?php if (function_exists('cargarEstilos')) { cargarEstilos(); } ?>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    
    <style>
        :root {
            --sidebar-width: 260px;
            --navbar-height: 70px;
            --primary-gradient: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
            --ios-blur: saturate(180%) blur(20px);
        }

        body { background-color: #f8f9fc; margin: 0; font-family: 'Inter', system-ui, -apple-system, sans-serif; }
        .main-content { margin-left: var(--sidebar-width); padding-top: var(--navbar-height); min-height: 100vh; transition: all 0.3s ease; }
        .page-content { padding: 2rem; }
        
        /* Estilo iOS Card */
        .card-custom { 
            border: none; 
            border-radius: 18px; 
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.04); 
            background: rgba(255, 255, 255, 0.9); 
            backdrop-filter: var(--ios-blur);
            margin-bottom: 1.5rem; 
        }

        .stat-card { 
            background: var(--primary-gradient); 
            color: white; 
            border-radius: 22px; 
            position: relative; 
            overflow: hidden; 
            border: none; 
            box-shadow: 0 15px 35px rgba(34, 74, 190, 0.2);
        }

        .stat-card .bg-icon { 
            position: absolute; 
            right: -20px; 
            top: -20px; 
            font-size: 9rem; 
            color: rgba(255, 255, 255, 0.1); 
            transform: rotate(-15deg); 
            pointer-events: none; 
        }

        .indicator-container { 
            background: rgba(255, 255, 255, 0.12); 
            backdrop-filter: blur(10px); 
            border-radius: 15px; 
            padding: 15px 5px; 
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .indicator-item { flex: 1; text-align: center; border-right: 1px solid rgba(255, 255, 255, 0.2); }
        .indicator-item:last-child { border-right: none; }

        .form-select-custom {
            border: 1px solid #e3e6f0;
            border-radius: 10px;
            padding: 10px;
            background-color: #fdfdfd;
        }

        @media (max-width: 991.98px) { .main-content { margin-left: 0; } }
    </style>
</head>
<body>

    <?php renderizarLayout($paginaActual); ?>

    <main class="main-content">
        <div class="page-content">
            
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold text-dark m-0">Monitor de Caja y Auditoría</h2>
                    <p class="text-muted mb-0" id="rangoFechasTxt">Calculando periodo...</p>
                </div>
                <div id="loader" class="spinner-border text-primary d-none" role="status"></div>
            </div>

            <div class="card card-custom p-4">
                <form id="formFiltros" class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label fw-bold small text-uppercase">Rango Temporal</label>
                        <select id="periodo" class="form-select form-select-custom">
                            <option value="">Automático (Corte Anterior)</option>
                            <option value="hoy">Hoy</option>
                            <option value="ayer">Ayer</option>
                            <option value="semana">Últimos 7 días</option>
                            <option value="mes">Este Mes</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold small text-uppercase">Almacén / Sucursal</label>
                        <?php $esAdmin = ($almacen_usuario == 0); ?>
                        <select id="almacen_id" class="form-select form-select-custom" <?= !$esAdmin ? 'disabled' : '' ?>>
                            <?php if ($esAdmin): ?>
                                <option value="0">Global (Todos los almacenes)</option>
                            <?php endif; ?>
                            
                            <?php 
                            $queryAlmacenes = "SELECT id, nombre FROM almacenes WHERE activo = 1 ORDER BY nombre ASC";
                            $res = $conexion->query($queryAlmacenes);
                            while($a = $res->fetch_assoc()):
                                $selected = ($almacen_usuario == $a['id']) ? 'selected' : '';
                                echo "<option value='{$a['id']}' $selected>{$a['nombre']}</option>";
                            endwhile; 
                            ?>
                        </select>
                    </div>
                    <div class="col-md-5">
                        <button type="button" id="btnFiltrar" class="btn btn-primary w-100 fw-bold py-2 shadow-sm">
                            <i class="bi bi-arrow-repeat me-2"></i>RECARGAR AUDITORÍA
                        </button>
                    </div>
                </form>
            </div>

            <div class="row mb-4">
                <div class="col-lg-8">
                    <div class="card stat-card h-100 shadow-lg animate__animated animate__fadeIn">
                        <i class="bi bi-wallet2 bg-icon"></i>
                        <div class="card-body d-flex flex-column justify-content-center text-center py-5">
                            <h6 class="text-uppercase fw-bold mb-2 opacity-75">Venta Bruta Registrada</h6>
                            <h1 class="display-3 fw-bold mb-4" id="totalVentaTxt">$0.00</h1>
                            
                            <div class="indicator-container d-flex">
                                <div class="indicator-item">
                                    <small class="d-block opacity-75">Efectivo Real</small>
                                    <span class="fw-bold fs-5" id="txtTotalCobrado" style="color: #00ff88;">$0.00</span>
                                </div>
                                <div class="indicator-item">
                                    <small class="d-block opacity-75">Saldo Favor</small>
                                    <span class="fw-bold fs-5 text-warning" id="txtTotalSaldoFavor">$0.00</span>
                                </div>
                                <div class="indicator-item">
                                    <small class="d-block opacity-75">Por Cobrar</small>
                                    <span class="fw-bold fs-5 text-danger" id="txtTotalDeuda" style="color: #ff6b6b !important;">$0.00</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <div class="card card-custom p-4 h-100 shadow-sm text-center d-flex flex-column justify-content-center">
                        <h6 class="fw-bold text-muted small mb-3">COBERTURA DE COBRANZA</h6>
                        <div style="height: 200px;"><canvas id="chartPagos"></canvas></div>
                    </div>
                </div>
            </div>

            <div class="card card-custom overflow-hidden shadow-sm animate__animated animate__fadeInUp">
                <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 fw-bold text-dark"><i class="bi bi-list-check me-2"></i>Movimientos del Periodo</h6>
                </div>
                <div class="table-responsive">
                    <table id="tablaCorte" class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4 border-0">Folio / Fecha</th>
                                <th class="border-0">Almacén / Vendedor</th>
                                <th class="border-0">Productos</th>
                                <th class="text-center border-0">Estado</th>
                                <th class="text-end pe-4 border-0">Total Venta</th>
                            </tr>
                        </thead>
                        <tbody>
                            </tbody>
                    </table>
                </div>
            </div>

        </div>
    </main>

    <?php if (function_exists('cargarScripts')) { cargarScripts(); } ?>

<script>
let chartPagos;

function formatMoney(n) {
    return '$' + parseFloat(n || 0).toLocaleString('es-MX', {minimumFractionDigits: 2, maximumFractionDigits: 2});
}

function updateChart(cobrado, deuda) {
    if (chartPagos) chartPagos.destroy();
    const ctx = document.getElementById('chartPagos').getContext('2d');
    
    // Si ambos son 0, mostrar gráfica vacía
    const total = parseFloat(cobrado) + parseFloat(deuda);
    const dataValues = total === 0 ? [0, 1] : [cobrado, deuda];
    const colors = total === 0 ? ['#e3e6f0', '#e3e6f0'] : ['#00ff88', '#ff6b6b'];

    chartPagos = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Cobrado', 'Pendiente'],
            datasets: [{
                data: dataValues,
                backgroundColor: colors,
                borderWidth: 0,
                hoverOffset: 4
            }]
        },
        options: {
            cutout: '80%',
            responsive: true,
            maintainAspectRatio: false,
            plugins: { 
                legend: { position: 'bottom', labels: { boxWidth: 12, padding: 15 } } 
            }
        }
    });
}

function fetchCorteData() {
    $('#loader').removeClass('d-none');
    
    // Capturar almacen_id aunque esté disabled
    const almacenVal = $('#almacen_id').val();

    const params = {
        ajax: 1,
        periodo: $('#periodo').val(),
        almacen_id: almacenVal
    };

    $.ajax({
        url: '/cfsistem/app/controllers/corteCajaController.php',
        type: 'GET',
        data: params,
        dataType: 'json',
        success: function(res) {
            if (res.status === 'success') {
                const s = res.sumas;
                
                // Actualizar Tarjetas
                $('#totalVentaTxt').text(formatMoney(s.venta_bruta));
                $('#txtTotalCobrado').text(formatMoney(s.efectivo_real));
                $('#txtTotalSaldoFavor').text(formatMoney(s.saldo_favor));
                $('#txtTotalDeuda').text(formatMoney(s.deuda_pendiente));
                
                // Texto de rango
                $('#rangoFechasTxt').html(`<i class="bi bi-calendar3 me-1"></i> Desde: <strong>${s.fecha_inicio}</strong> hasta <strong>${s.fecha_fin}</strong>`);

                updateChart(s.efectivo_real, s.deuda_pendiente);

                // Llenar Tabla
                let html = '';
                if (res.data && res.data.length > 0) {
                    const agrupado = {};
                    res.data.forEach(v => {
                        if (!agrupado[v.folio]) {
                            agrupado[v.folio] = { info: v, productos: [], subtotal: 0 };
                        }
                        agrupado[v.folio].productos.push(v.producto);
                        agrupado[v.folio].subtotal += parseFloat(v.monto) || 0;
                    });

                    Object.values(agrupado).forEach(v => {
                        const esDeuda = parseFloat(v.info.deuda_dinero) > 0.05;
                        html += `
                            <tr>
                                <td class="ps-4">
                                    <span class="fw-bold text-primary">${v.info.folio}</span><br>
                                    <small class="text-muted">${v.info.fecha}</small>
                                </td>
                                <td>
                                    <span class="small fw-bold">${v.info.almacen}</span><br>
                                    <small class="text-muted">${v.info.vendedor}</small>
                                </td>
                                <td><div class="text-truncate" style="max-width: 250px;"><small>${v.productos.join(', ')}</small></div></td>
                                <td class="text-center">
                                    <span class="badge ${esDeuda ? 'bg-light text-danger border border-danger' : 'bg-light text-success border border-success'} rounded-pill px-3">
                                        ${esDeuda ? 'PENDIENTE' : 'PAGADO'}
                                    </span>
                                </td>
                                <td class="text-end pe-4 fw-bold text-dark">${formatMoney(v.subtotal)}</td>
                            </tr>
                        `;
                    });
                } else {
                    html = '<tr><td colspan="5" class="text-center py-5 text-muted">No se encontraron movimientos para el filtro seleccionado.</td></tr>';
                }
                $('#tablaCorte tbody').html(html);
            }
        },
        error: function(xhr) {
            console.error("Error en servidor:", xhr.responseText);
        },
        complete: () => $('#loader').addClass('d-none')
    });
}

$(document).ready(function() {
    fetchCorteData();
    $('#btnFiltrar').on('click', fetchCorteData);
    $('#periodo, #almacen_id').on('change', fetchCorteData);
});
</script>
</body>
</html>