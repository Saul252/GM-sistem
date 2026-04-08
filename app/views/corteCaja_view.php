<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Corte de Caja | Sistema de Almacenes</title>
      <?php require_once __DIR__ . '/layout/icono.php' ?>
    <?php if (function_exists('cargarEstilos')) { cargarEstilos(); } ?>
    
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    
   <style>
    :root {
        --sidebar-width: 260px;
        --navbar-height: 70px;
        --primary-gradient: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
        --glass-bg: rgba(255, 255, 255, 0.15);
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
        border-radius: 20px;
        position: relative;
        overflow: hidden;
        border: none;
    }

    /* Icono de fondo decorativo */
    .stat-card .bg-icon {
        position: absolute;
        right: -15px;
        top: -15px;
        font-size: 8rem;
        color: rgba(255, 255, 255, 0.1);
        transform: rotate(-15deg);
        pointer-events: none;
    }

    .stat-card h6 {
        letter-spacing: 1px;
        font-size: 0.75rem;
    }

    .indicator-container {
        background: var(--glass-bg);
        backdrop-filter: blur(5px);
        border-radius: 12px;
        padding: 12px 5px;
    }

    .indicator-item {
        flex: 1;
        text-align: center;
    }

    .indicator-item:not(:last-child) {
        border-right: 1px solid rgba(255, 255, 255, 0.2);
    }

    @media (max-width: 991.98px) {
        .main-content { margin-left: 0; }
    }

    @media print {
        header, nav, aside, .sidebar, #sidebar, .navbar, .no-print, 
        #formFiltros, .btn, .live-indicator, #loader, .card-header,
        .row.mb-4 {
            display: none !important;
        }
        .main-content { margin: 0 !important; padding: 0 !important; width: 100% !important; }
        .page-content { padding: 0 !important; }
        .card {
            border: 1px solid #000 !important;
            box-shadow: none !important;
            border-radius: 0 !important;
            margin-bottom: 5px !important;
            break-inside: avoid;
        }
        .h5, .h4, .fw-bold { color: #000 !important; font-size: 11pt !important; }
        .table td, .table th { border: 1px solid #ddd !important; padding: 4px !important; font-size: 9pt !important; }
        .rounded-circle { display: none !important; }
        .bg-light, .bg-opacity-10, .bg-opacity-50 { background: none !important; }
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
                    <div class="col-md-2">
                        <label class="form-label fw-bold small">RANGO TEMPORAL</label>
                        <select name="periodo" id="periodo" class="form-select border-0 bg-light shadow-none">
                            <option value="hoy">Hoy (Tiempo Real)</option>
                            <option value="ayer">Ayer</option>
                            <option value="semana">Últimos 7 días</option>
                            <option value="mes">Este Mes</option>
                            <option value="personalizado">Personalizado</option>
                        </select>
                    </div>
                    <div class="col-md-3">
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
                    <div class="col-md-3">
                        <label class="form-label fw-bold small">MÉTODO DE PAGO</label>
                        <select id="filtro_pago" class="form-select border-0 bg-light shadow-none">
                            <option value="TODOS">Todos los métodos</option>
                            <option value="Efectivo">Efectivo</option>
                            <option value="Transferencia">Transferencia</option>
                            <option value="Tarjeta">Tarjeta</option>
                            <option value="Crédito">Crédito / Deuda</option>
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
    <div class="card stat-card h-100 shadow-lg">
        <i class="bi bi-wallet2 bg-icon"></i>
        
        <div class="card-body d-flex flex-column justify-content-center text-center py-4 px-3" style="position: relative; z-index: 1;">
            <h6 class="text-uppercase fw-bold mb-2 opacity-75">Venta Bruta Total</h6>
            <h1 class="fw-extrabold mb-4 display-6" id="totalVentaTxt">$0.00</h1>
            
            <div class="indicator-container d-flex align-items-center">
                <div class="indicator-item">
                    <small class="d-block opacity-75 smaller mb-1">Cobrado</small>
                    <span class="fw-bold" id="txtTotalCobrado" style="color: #00ff88;">$0.00</span>
                </div>
                
                <div class="indicator-item">
                    <small class="d-block opacity-75 smaller mb-1">Saldo Usado</small>
                    <span class="fw-bold text-warning" id="txtTotalSaldoFavor">$0.00</span>
                </div>

                <div class="indicator-item">
                    <small class="d-block opacity-75 smaller mb-1">Pendiente</small>
                    <span class="fw-bold text-danger" id="txtTotalDeuda" style="color: #ff6b6b !important;">$0.00</span>
                </div>
            </div>
        </div>
    </div>
</div>
            </div>

            <div class="card card-custom overflow-hidden">
                <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 fw-bold text-dark">Detalle de Operaciones y Auditoría</h6>
                    <div class="d-flex justify-content-end gap-2 mb-4 no-print">
                        <button onclick="exportarCSV()" class="btn btn-outline-success rounded-pill px-3">
                            <i class="bi bi-file-earmark-spreadsheet me-1"></i> Exportar a Excel (CSV)
                        </button>
                        <button onclick="imprimirReporte()" class="btn btn-primary rounded-pill px-4">
                            <i class="bi bi-printer me-1"></i> Imprimir / Guardar PDF
                        </button>
                    </div>
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
let ultimoFolio = '';
let datosActualesReporte = []; 
function fetchCorteData(isSilent = false) {
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
            
            datosActualesReporte = res.data || [];

            let sumaVentaBruta = 0;
            let totalCobrado = 0;
            let saldoAFavor=0;
            let ent_ok = 0, ent_pend = 0;
            const soloPendientes = $('#solo_pendientes').is(':checked');
            const metodoFiltro = $('#filtro_pago').val(); // Obtener filtro seleccionado

            if (res.data && res.data.length > 0) {
                ultimoFolio = res.data[0].folio;
                
                const ventasPorFolio = {};
                res.data.forEach(v => {
                    // Lógica de filtro por Método de Pago
                    if (metodoFiltro !== 'TODOS' && !v.metodo.includes(metodoFiltro)) return;

                    const f = v.folio;
                    if (!ventasPorFolio[f]) {
                        ventasPorFolio[f] = {
                            info: v, 
                            productos: [], 
                            subtotalFolio: 0,
                            pagoParcialFolio: 0, 
                            deudaFolio: 0, 
                            pendMat: 0,
                            saldoFavorFolio: 0 // <--- Inicializar
                        };
                    }
                    ventasPorFolio[f].saldoFavorFolio = parseFloat(v.saldo_favor_usado) || 0;
                    ventasPorFolio[f].productos.push({
                        nombre: v.producto, cant: v.cantidad_texto,
                        monto: parseFloat(v.monto) || 0
                    });
                    ventasPorFolio[f].subtotalFolio += parseFloat(v.monto) || 0;
                    ventasPorFolio[f].pagoParcialFolio += parseFloat(v.pago_parcial) || 0;
                    if (parseFloat(v.deuda_dinero) > 0.01) ventasPorFolio[f].deudaFolio = parseFloat(v.deuda_dinero);
                    if (parseFloat(v.pendiente_material) > 0) ventasPorFolio[f].pendMat = 1;
                });

                let html = '';
                Object.values(ventasPorFolio).forEach((venta) => {
                    const v = venta.info;
                    const esPagado = venta.deudaFolio <= 0.01;
                    if (soloPendientes && esPagado && venta.pendMat <= 0) return;
                    
                    // CORRECCIÓN: Se usa la propiedad acumulada en el objeto 'venta'
                    saldoAFavor += venta.saldoFavorFolio;

                    sumaVentaBruta += venta.subtotalFolio;
                    
                    // CORRECCIÓN: Se usa pagoParcialFolio para no duplicar sumas
                    totalCobrado += esPagado ? venta.subtotalFolio : venta.pagoParcialFolio;
                    
                    // Mantenemos esta línea por tu instrucción, aunque el total ya incluya el saldo
                    // totalCobrado += venta.saldoFavorFolio; 

                    if (venta.pendMat > 0) ent_pend++; else ent_ok++;

                    html += `
                        <tr style="height: 25px;"><td colspan="7" class="border-0"></td></tr>
                        <tr class="align-middle border-0">
                            <td colspan="7" class="p-0 border-0">
                                <div class="card shadow-sm border-0 mb-0" style="overflow: hidden; border-radius: 12px; border-left: 5px solid ${esPagado ? '#198754' : '#dc3545'} !important;">
                                    <div class="card-header bg-white border-bottom-0 py-3 px-4">
                                        <div class="row align-items-center">
                                            <div class="col-md-3">
                                                <div class="d-flex align-items-center">
                                                    <div class="rounded-circle bg-primary bg-opacity-10 p-2 me-3 text-primary"><i class="bi bi-receipt fs-4"></i></div>
                                                    <div><span class="text-muted smaller fw-bold d-block text-uppercase">Folio</span><span class="h5 fw-bold mb-0 text-dark">${v.folio}</span></div>
                                                </div>
                                            </div>
                                            <div class="col-md-3 border-start ps-4">
                                                <div class="d-flex align-items-center">
                                                    <div class="rounded-circle bg-secondary bg-opacity-10 p-2 me-3 text-secondary"><i class="bi bi-person-circle fs-4"></i></div>
                                                    <div><span class="text-muted smaller fw-bold d-block text-uppercase">Atendió</span><span class="fw-semibold text-dark">${v.vendedor}</span></div>
                                                </div>
                                            </div>
                                            <div class="col-md-3 border-start ps-4">
                                                <div class="d-flex align-items-center">
                                                    <div class="rounded-circle bg-info bg-opacity-10 p-2 me-3 text-info"><i class="bi bi-shop fs-4"></i></div>
                                                    <div><span class="text-muted smaller fw-bold d-block text-uppercase">Almacén</span><span class="badge bg-light text-dark border">${v.almacen}</span></div>
                                                </div>
                                            </div>
                                            <div class="col-md-3 text-end">
                                                ${esPagado 
                                                    ? `<span class="badge bg-success py-2 px-3 fs-6 rounded-pill">PAGADO TOTAL</span>`
                                                    : `<span class="badge bg-danger py-2 px-3 fs-6 rounded-pill">DEBE: $${venta.deudaFolio.toLocaleString('es-MX', {minimumFractionDigits: 2})}</span>`}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body bg-light bg-opacity-50 py-0">
                                        <table class="table table-borderless m-0">
                                            <tbody>
                                                ${venta.productos.map(p => `
                                                    <tr class="border-top" style="border-color: #eee !important;">
                                                        <td class="ps-5 py-2"><i class="bi bi-box me-2 text-muted"></i>${p.nombre}</td>
                                                        <td class="text-center text-secondary">${p.cant}</td>
                                                        <td class="text-center">${venta.pendMat > 0 ? '<span class="text-warning small">PENDIENTE</span>' : '<span class="text-info small">OK</span>'}</td>
                                                        <td class="text-end pe-4 fw-bold text-dark">$${p.monto.toLocaleString('es-MX', {minimumFractionDigits: 2})}</td>
                                                    </tr>
                                                `).join('')}
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="card-footer bg-white py-3 px-4 d-flex justify-content-between align-items-center">
                                        <div class="small text-muted">
                                            ${v.fecha} | <strong>${v.metodo}</strong>
                                            ${venta.saldoFavorFolio > 0 
                                                ? `<span class="ms-2 badge bg-warning text-dark border border-warning">
                                                    <i class="bi bi-star-fill me-1"></i>Saldo a Favor aplicado: $${venta.saldoFavorFolio.toLocaleString('es-MX', {minimumFractionDigits: 2})}
                                                   </span>` 
                                                : ''}
                                        </div>
                                        <div class="text-end"><span class="h4 fw-bold text-primary">$${venta.subtotalFolio.toLocaleString('es-MX', {minimumFractionDigits: 2})}</span></div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    `;
                });

                let deudaFinal = Math.max(0, sumaVentaBruta - totalCobrado);
                 let dineroRealEfectivo = totalCobrado - saldoAFavor;
                $('#tablaCorte tbody').html(html || '<tr><td colspan="7" class="text-center py-5">No hay datos para este filtro</td></tr>');
                $('#totalVentaTxt').text('$' + sumaVentaBruta.toLocaleString('es-MX', {minimumFractionDigits: 2}));
                $('#txtTotalCobrado').text('$' + dineroRealEfectivo.toLocaleString('es-MX', {minimumFractionDigits: 2}));
                $('#txtTotalDeuda').text('$' + deudaFinal.toLocaleString('es-MX', {minimumFractionDigits: 2}));
                updateAuditCharts(totalCobrado, deudaFinal, ent_ok, ent_pend);
               

                console.log("Cobrado Total:", totalCobrado);
                console.log("De lo cual fue Saldo a Favor:", saldoAFavor);
                console.log("Dinero Físico esperado:", dineroRealEfectivo);
                // ... al final de fetchCorteData, después de los otros .text()
$('#txtTotalSaldoFavor').text('$' + saldoAFavor.toLocaleString('es-MX', {minimumFractionDigits: 2}));
            }
        },
        complete: () => $('#loader').addClass('d-none')
    });
}
function exportarCSV() {
    if (datosActualesReporte.length === 0) return alert("No hay datos para exportar");
    const metodoFiltro = $('#filtro_pago').val();

    const agrupado = datosActualesReporte.reduce((acc, v) => {
        // Respetar el filtro también en la exportación
        if (metodoFiltro !== 'TODOS' && !v.metodo.includes(metodoFiltro)) return acc;

        if (!acc[v.folio]) {
            acc[v.folio] = {
                folio: v.folio,
                fecha: v.fecha,
                vendedor: v.vendedor,
                almacen: v.almacen,
                metodo: v.metodo,
                total: 0,
                deuda: parseFloat(v.deuda_dinero) || 0,
                productos: []
            };
        }
        acc[v.folio].total += parseFloat(v.monto) || 0;
        acc[v.folio].productos.push(`${v.producto} (${v.cantidad_texto})`);
        return acc;
    }, {});

    let csv = "\ufeffFolio,Fecha,Vendedor,Almacen,Metodo,Productos,Total Venta,Saldo Pendiente,Estatus\n";
    Object.values(agrupado).forEach(v => {
        const listaProductos = v.productos.join(" / ");
        const estatus = v.deuda > 0.01 ? "PENDIENTE" : "LIQUIDADO";
        csv += `"${v.folio}","${v.fecha}","${v.vendedor}","${v.almacen}","${v.metodo}","${listaProductos}","${v.total.toFixed(2)}","${v.deuda.toFixed(2)}","${estatus}"\n`;
    });

    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement("a");
    link.href = URL.createObjectURL(blob);
    link.download = `Corte_Caja_${new Date().toISOString().slice(0,10)}.csv`;
    link.click();
}

function imprimirReporte() {
    window.print();
}

function updateAuditCharts(pagado, deuda, ok, pend) {
    const common = { cutout: '82%', plugins: { legend: { position: 'bottom' } } };
    if (chartPagos) chartPagos.destroy();
    chartPagos = new Chart(document.getElementById('chartPagos'), {
        type: 'doughnut',
        data: {
            labels: ['Cobrado', 'Deuda'],
            datasets: [{ data: [pagado, deuda], backgroundColor: ['#198754', '#dc3545'], borderWidth: 0 }]
        },
        options: common
    });
    if (chartEntregas) chartEntregas.destroy();
    chartEntregas = new Chart(document.getElementById('chartEntregas'), {
        type: 'pie',
        data: {
            labels: ['Entregado', 'Pendiente'],
            datasets: [{ data: [ok, pend], backgroundColor: ['#0dcaf0', '#ffc107'], borderWidth: 2, borderColor: '#fff' }]
        },
        options: { plugins: { legend: { position: 'bottom' } } }
    });
}

$(document).ready(function() {
    fetchCorteData();
    // Escuchar cambios en el nuevo filtro
    $('#btnFiltrar, #solo_pendientes, #filtro_pago').on('click change', () => fetchCorteData());
    $('#periodo').on('change', () => fetchCorteData());
});
</script>
</body>
</html>