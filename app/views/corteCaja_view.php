<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Corte de Caja | Sistema de Almacenes</title>
    
    <?php cargarEstilos(); ?>
    
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    
    <style>
        :root {
            --sidebar-width: 260px;
            --navbar-height: 70px;
            --primary-gradient: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
        }

        body { 
            background-color: #f0f2f5; 
            margin: 0; 
            overflow-x: hidden;
            font-family: 'Inter', sans-serif;
        }

        .main-content {
            margin-left: var(--sidebar-width);
            padding-top: var(--navbar-height);
            min-height: 100vh;
            transition: all 0.3s ease;
        }

        .page-content { padding: 2rem; }

        .card-custom {
            border: none;
            border-radius: 15px;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.05);
            background: #fff;
            margin-bottom: 1.5rem;
        }

        .stat-card {
            background: var(--primary-gradient);
            color: white;
            border-radius: 15px;
            position: relative;
            overflow: hidden;
        }

        .stat-card i {
            position: absolute;
            right: -10px;
            bottom: -10px;
            font-size: 5rem;
            opacity: 0.2;
        }

        .live-indicator {
            padding: 5px 12px; font-size: 0.75rem; font-weight: 700;
            display: inline-flex; align-items: center;
        }

        .dot {
            height: 8px; width: 8px; background-color: #fff;
            border-radius: 50%; display: inline-block; margin-right: 8px;
        }

        .table thead th {
            background-color: #f8f9fc; text-transform: uppercase;
            font-size: 0.75rem; font-weight: 700; color: #4e73df;
            border-bottom: 1px solid #e3e6f0;
        }

        .badge-audit { font-size: 0.7rem; padding: 4px 8px; }

        @media (max-width: 991.98px) {
            .main-content { margin-left: 0; }
        }
    </style>
</head>
<body>

    <?php renderizarLayout($paginaActual); ?>

    <main class="main-content">
        <div class="page-content">
            
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <div class="d-flex align-items-center mb-1">
                        <h2 class="fw-bold text-dark m-0">Monitor de Caja y Auditoría</h2>
                        <span id="liveStatus" class="live-indicator badge rounded-pill bg-success ms-3 animate__animated animate__pulse animate__infinite">
                            <span class="dot"></span> EN VIVO
                        </span>
                    </div>
                    <p class="text-muted mb-0">Control de ingresos, saldos pendientes y entregas de material.</p>
                </div>
                <div id="loader" class="spinner-border text-primary d-none" role="status"></div>
            </div>

            <div class="card card-custom p-3">
                <form id="formFiltros" class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label fw-bold small">RANGO TEMPORAL</label>
                        <select name="periodo" id="periodo" class="form-select border-0 bg-light shadow-none">
                            <option value="hoy">Hoy (Tiempo Real)</option>
                            <option value="ayer">Ayer</option>
                            <option value="semana">Últimos 7 días</option>
                            <option value="mes">Este Mes</option>
                            <option value="personalizado">Personalizado</option>
                        </select>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label fw-bold small">ALMACÉN</label>
                        <select name="almacen_id" id="almacen_id" class="form-select border-0 bg-light shadow-none" <?= $almacen_usuario > 0 ? 'disabled' : '' ?>>
                            <option value="0">Todos los puntos de venta</option>
                            <?php 
                            $res = $conexion->query("SELECT id, nombre FROM almacenes WHERE activo = 1 ORDER BY nombre ASC");
                            while($a = $res->fetch_assoc()):
                                $sel = ($almacen_usuario == $a['id']) ? 'selected' : '';
                                echo "<option value='{$a['id']}' $sel>{$a['nombre']}</option>";
                            endwhile; 
                            ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <button type="button" id="btnFiltrar" class="btn btn-primary w-100 fw-bold shadow-sm py-2">
                            <i class="bi bi-arrow-repeat me-2"></i>ACTUALIZAR DATOS
                        </button>
                    </div>
                    <div class="col-12">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="solo_pendientes">
                            <label class="form-check-label small fw-bold text-danger" for="solo_pendientes">MOSTRAR SOLO VENTAS CON PENDIENTES (DEUDAS O ENTREGAS)</label>
                        </div>
                    </div>
                </form>
            </div>

            <div class="row mb-4">
                <div class="col-lg-4">
                    <div class="card card-custom p-3 h-100">
                        <h6 class="fw-bold text-muted small mb-3 text-center">COBRANZA (PAGADO VS DEUDA)</h6>
                        <div style="height: 200px;">
                            <canvas id="chartPagos"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card card-custom p-3 h-100">
                        <h6 class="fw-bold text-muted small mb-3 text-center">ESTADO DE ENTREGAS (PRODUCTOS)</h6>
                        <div style="height: 200px;">
                            <canvas id="chartEntregas"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card card-custom stat-card h-100 shadow-lg">
                        <div class="card-body d-flex flex-column justify-content-center text-center py-5">
                            <h6 class="text-uppercase opacity-75 fw-bold mb-1 small">Venta Bruta (Suma de Ventas)</h6>
                            <h2 class="fw-bold mb-3" id="totalVentaTxt">$0.00</h2>
                            <div class="d-flex justify-content-around border-top pt-3 bg-white bg-opacity-10 rounded">
                                <div>
                                    <small class="d-block opacity-75">Cobrado Real</small>
                                    <span class="fw-bold text-success" id="txtTotalCobrado">$0.00</span>
                                </div>
                                <div>
                                    <small class="d-block opacity-75">Por Cobrar (Deuda)</small>
                                    <span class="fw-bold text-danger" id="txtTotalDeuda">$0.00</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card card-custom overflow-hidden">
                <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 fw-bold text-dark">Detalle de Operaciones y Auditoría</h6>
                    <button class="btn btn-sm btn-outline-secondary border-0" onclick="exportarExcel()">
                        <i class="bi bi-download"></i>
                    </button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table id="tablaCorte" class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th class="ps-4">Folio / Hora</th>
                                    <th>Almacén</th>
                                    <th>Vendedor</th>
                                    <th>Producto / Cantidad</th>
                                    <th class="text-center">Estatus Pago</th>
                                    <th class="text-center">Estatus Entrega</th>
                                    <th class="text-end pe-4">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </main>

    <?php cargarScripts(); ?>

 <script>
let chartPagos, chartEntregas;
let autoRefreshInterval;
let ultimoFolio = '';

/**
 * Función principal: Obtiene y procesa los datos con la lógica de Totales vs Parciales
 */function fetchCorteData(isSilent = false) {
    if (!isSilent) $('#loader').removeClass('d-none');

    const params = {
        ajax: 1,
        periodo: $('#periodo').val(),
        almacen_id: $('#almacen_id').val()
    };

    $.ajax({
        url: 'corteCajaController.php',
        type: 'GET',
        data: params,
        dataType: 'json',
        success: function(res) {
            if (isSilent && res.data.length > 0 && res.data[0].folio === ultimoFolio) return;
            
            let html = '';
            
            // --- VARIABLES DE SUMA ESTRICTA ---
            let sumaVentaBruta = 0;       // El total del papel (Subtotales)
            let totalCobrado = 0;         // Dinero real que entró (Abonos + Pagos Totales)
            // La deuda ya no la sumaremos fila por fila, la calcularemos al final
            
            let ent_ok = 0, ent_pend = 0;
            const soloPendientes = $('#solo_pendientes').is(':checked');

            if (res.data && res.data.length > 0) {
                ultimoFolio = res.data[0].folio;
                
                res.data.forEach(v => {
                    const subtotal = parseFloat(v.monto) || 0;
                    const deudaFila = parseFloat(v.deuda_dinero) || 0;
                    const pagoParcial = parseFloat(v.pago_parcial) || 0; 
                    const pendMat = parseFloat(v.pendiente_material) || 0;

                    if (soloPendientes && deudaFila <= 0.01 && pendMat <= 0) return;

                    // 1. Acumulamos el total de la venta (lo que debería ser)
                    sumaVentaBruta += subtotal;

                    // 2. Acumulamos el COBRADO REAL
                    if (deudaFila <= 0.01) {
                        // Si está pagado total, el cobrado es el subtotal
                        totalCobrado += subtotal;
                    } else {
                        // Si es parcial, sumamos lo que realmente pagó (abono)
                        totalCobrado += pagoParcial;
                    }

                    // Conteos para gráficas
                    if (pendMat > 0) ent_pend++; else ent_ok++;

                    const badgePago = deudaFila > 0.01 
                        ? `<span class="badge bg-danger-subtle text-danger border-danger badge-audit">PARCIAL (Falta: $${deudaFila.toFixed(2)})</span>`
                        : `<span class="badge bg-success-subtle text-success border-success badge-audit">PAGADO TOTAL</span>`;

                    html += `
                        <tr class="animate__animated animate__fadeIn">
                            <td class="ps-4">
                                <span class="fw-bold text-primary">${v.folio}</span><br>
                                <small class="text-muted">${v.fecha}</small>
                            </td>
                            <td><span class="badge bg-light text-dark border">${v.almacen}</span></td>
                            <td class="small">${v.vendedor}</td>
                            <td>
                                <div class="fw-bold text-dark small">${v.producto}</div>
                            </td>
                            <td class="text-center">${badgePago}<br><small class="smaller">${v.metodo}</small></td>
                            <td class="text-center">${pendMat > 0 ? 'Pendiente' : 'Entregado'}</td>
                            <td class="text-end pe-4 fw-bold">$${subtotal.toLocaleString('es-MX', {minimumFractionDigits: 2})}</td>
                        </tr>`;
                });

                // --- LA OPERACIÓN MÁGICA ---
                // Deuda = Lo que se vendió menos lo que se cobró.
                let deudaFinalCalculada = sumaVentaBruta - totalCobrado;

                // Si por algún redondeo sale un número negativo ínfimo, lo ponemos en 0
                if(deudaFinalCalculada < 0) deudaFinalCalculada = 0;

                // Actualizar interfaz
                $('#tablaCorte tbody').html(html);
                $('#totalVentaTxt').text('$' + sumaVentaBruta.toLocaleString('es-MX', {minimumFractionDigits: 2}));
                $('#txtTotalCobrado').text('$' + totalCobrado.toLocaleString('es-MX', {minimumFractionDigits: 2}));
                $('#txtTotalDeuda').text('$' + deudaFinalCalculada.toLocaleString('es-MX', {minimumFractionDigits: 2}));
                
                updateAuditCharts(totalCobrado, deudaFinalCalculada, ent_ok, ent_pend);

                console.log("Auditoría Finalizada:", { 
                    ventaBruta: sumaVentaBruta, 
                    dineroEnCaja: totalCobrado, 
                    deudaCalculada: deudaFinalCalculada 
                });

            } else {
                $('#tablaCorte tbody').html('<tr><td colspan="7" class="text-center py-5 text-muted">Sin movimientos.</td></tr>');
                $('#totalVentaTxt, #txtTotalCobrado, #txtTotalDeuda').text('$0.00');
                updateAuditCharts(0, 0, 0, 0);
            }
        },
        complete: () => $('#loader').addClass('d-none')
    });
}
/**
 * Renderiza gráficas
 */
function updateAuditCharts(cobrado, deuda, ent_ok, ent_pend) {
    const ctxP = document.getElementById('chartPagos').getContext('2d');
    if (chartPagos) chartPagos.destroy();
    chartPagos = new Chart(ctxP, {
        type: 'doughnut',
        data: {
            labels: ['Cobrado Real', 'Deuda Pendiente'],
            datasets: [{ 
                data: [cobrado, deuda], 
                backgroundColor: ['#28a745', '#dc3545'] 
            }]
        },
        options: { cutout: '75%', plugins: { legend: { position: 'bottom' } } }
    });

    const ctxE = document.getElementById('chartEntregas').getContext('2d');
    if (chartEntregas) chartEntregas.destroy();
    chartEntregas = new Chart(ctxE, {
        type: 'pie',
        data: {
            labels: ['OK', 'Pendiente'],
            datasets: [{ data: [ent_ok, ent_pend], backgroundColor: ['#0dcaf0', '#ffc107'] }]
        },
        options: { plugins: { legend: { position: 'bottom' } } }
    });
}

/**
 * Refresco automático
 */
function toggleRealTime() {
    if (autoRefreshInterval) clearInterval(autoRefreshInterval);
    if ($('#periodo').val() === 'hoy') {
        $('#liveStatus').fadeIn();
        autoRefreshInterval = setInterval(() => fetchCorteData(true), 15000);
    } else {
        $('#liveStatus').fadeOut();
    }
}

/**
 * Eventos iniciales
 */
$(document).ready(function() {
    fetchCorteData();
    toggleRealTime();
    $('#btnFiltrar, #solo_pendientes').on('click', () => fetchCorteData());
    $('#periodo').on('change', function() {
        fetchCorteData();
        toggleRealTime();
    });
});

function exportarExcel() { alert("Exportando..."); }
</script>
</body>
</html>