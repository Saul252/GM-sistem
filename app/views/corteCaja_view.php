<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Corte de Caja | Cf System</title>
     <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    
    <?php require_once __DIR__ . '/layout/icono.php' ?>
    <?php if (function_exists('cargarEstilos')) { cargarEstilos(); } ?>
    <style>
        :root { --apple-bg: #f5f5f7; --apple-blue: #007aff; }
        body { background-color: var(--apple-bg); font-family: -apple-system, sans-serif; }
        .main-content { margin-left: 260px; padding: 80px 20px; transition: 0.3s; }
        .glass-card { background: rgba(255,255,255,0.8); backdrop-filter: blur(15px); border-radius: 20px; border: none; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .ios-input { border: none; background: #eef0f2; border-radius: 10px; padding: 10px; }
        .loading-overlay { position: absolute; top:0; left:0; width:100%; height:100%; background: rgba(255,255,255,0.7); display:none; align-items:center; justify-content:center; border-radius: 20px; z-index: 10; }
        .table thead th { background: #fafdff; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 700; color: #8e8e93; font-size: 10px; border: none; }
        .badge-metodo { font-size: 10px; padding: 5px 10px; border-radius: 8px; font-weight: 600; }
        .origen-tag { font-size: 9px; padding: 2px 6px; border-radius: 4px; text-transform: uppercase; margin-top: 4px; display: inline-block; }
        @media (max-width: 992px) { .main-content { margin-left: 0; } }
    </style>
</head>
<body>

    <?php renderizarLayout($paginaActual); ?>

    <main class="main-content">
        <div class="container-fluid">
            
            <div class="d-flex justify-content-between align-items-end mb-4">
                <div>
                    <h1 class="fw-bold m-0" style="letter-spacing: -1px;">Monitor de Caja</h1>
                    <p class="text-secondary m-0">Detalle de ingresos y movimientos operativos.</p>
                </div>
            </div>
<?php echo "Fecha y hora del servidor: " . date('Y-m-d H:i:s');
echo "<br>Zona horaria configurada: " . date_default_timezone_get();?>
            <div class="glass-card p-4 mb-4">
                <form id="formFiltros" class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="small fw-bold text-muted text-uppercase text-xs">Periodo</label>
                        <select id="periodo" class="form-select ios-input">
                            <option value="hoy" selected>Hoy</option>
                            <option value="ayer">Ayer</option>
                            <option value="semana">Última Semana</option>
                            <option value="mes">Este Mes</option>
                            <option value="personalizado">Personalizado</option>
                        </select>
                    </div>

                    <div id="div-fechas" class="col-md-4 d-none">
                        <div class="row g-2">
                            <div class="col-6">
                                <label class="small fw-bold text-muted">INICIO</label>
                                <input type="date" id="f_inicio" class="form-control ios-input" value="<?= date('Y-m-d') ?>">
                            </div>
                            <div class="col-6">
                                <label class="small fw-bold text-muted">FIN</label>
                                <input type="date" id="f_fin" class="form-control ios-input" value="<?= date('Y-m-d') ?>">
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <label class="small fw-bold text-muted text-uppercase">Almacén / Sucursal</label>
                        <select id="almacen_id" class="form-select ios-input" <?= ($almacen_sesion != 0) ? 'disabled' : '' ?>>
                            <?php if ($almacen_sesion == 0): ?>
                                <option value="0">🌐 Todas las Sucursales</option>
                            <?php endif; ?>

                            <?php if(isset($listaAlmacenes)) foreach($listaAlmacenes as $alm): ?>
                                <option value="<?= $alm['id'] ?>" <?= ($almacen_sesion == $alm['id']) ? 'selected' : '' ?>>
                                    📍 <?= $alm['nombre'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <button type="button" onclick="AppCaja.update()" class="btn btn-primary w-100 fw-bold rounded-3 py-2" style="background: var(--apple-blue);">
                            Actualizar
                        </button>
                    </div>
                </form>
            </div>

      <style>
    .glass-card {
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        transition: transform 0.2s;
        border: 1px solid #eef0f2;
    }
    .glass-card:hover {
        transform: translateY(-5px);
    }
    .icon-box {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 10px;
    }
    .text-xs { font-size: 0.7rem; letter-spacing: 0.5px; }
    .bg-soft-success { background-color: #e8f5e9; color: #2e7d32; }
    .bg-soft-primary { background-color: #e3f2fd; color: #1565c0; }
    .bg-soft-warning { background-color: #fff3e0; color: #ef6c00; }
    .bg-soft-danger { background-color: #ffebee; color: #c62828; }
</style>

<div class="row mb-4 g-3">
    <div class="col-md-3">
        <div class="glass-card p-3 border-bottom border-success border-4 h-100 text-center">
            <div class="icon-box bg-soft-success">
                <i class="fas fa-money-bill-wave"></i>
            </div>
            <small class="text-muted fw-bold d-block mb-1 text-xs">EFECTIVO EN CAJA</small>
            <h3 class="fw-bold text-dark mb-0" id="res-total">$0.00</h3>
            <small class="text-success fw-bold" style="font-size: 10px;">Ingreso Líquido</small>
        </div>
    </div>
<div class="col-md-3">
        <div class="glass-card p-3 border-bottom border-success border-4 h-100 text-center">
            <div class="icon-box bg-soft-success">
                <i class="fas fa-money-bill-wave"></i>
            </div>
            <small class="text-muted fw-bold d-block mb-1 text-xs">Venta Total</small>
            <h3 class="fw-bold text-dark mb-0" id="res-total-ventas">$0.00</h3>
            <small class="text-success fw-bold" style="font-size: 10px;">Ingreso Líquido</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="glass-card p-3 border-bottom border-primary border-4 h-100 text-center">
            <div class="icon-box bg-soft-primary">
                <i class="fas fa-university"></i>
            </div>
            <small class="text-muted fw-bold d-block mb-1 text-xs">BANCOS (TARJ/TRANS)</small>
            <h3 class="fw-bold text-dark mb-0" id="res-tarjeta">$0.00</h3>
            <small class="text-primary fw-bold" style="font-size: 10px;">Confirmar en cuenta</small>
        </div>
    </div>

    <div class="col-md-3">
        <div class="glass-card p-3 border-bottom border-warning border-4 h-100 text-center">
            <div class="icon-box bg-soft-warning">
                <i class="fas fa-ticket-alt"></i>
            </div>
            <small class="text-muted fw-bold d-block mb-1 text-xs">SALDO A FAVOR</small>
            <h3 class="fw-bold text-dark mb-0" id="res-saldo-usado">$0.00</h3>
            <small class="text-warning fw-bold" style="font-size: 10px;">Movimiento contable</small>
        </div>
    </div>

    <div class="col-md-3">
        <div class="glass-card p-3 border-bottom border-danger border-4 h-100 text-center">
            <div class="icon-box bg-soft-danger">
                <i class="fas fa-clock"></i>
            </div>
            <small class="text-muted fw-bold d-block mb-1 text-xs">PENDIENTE DE COBRO</small>
            <h3 class="fw-bold text-danger mb-0" id="res-deuda">$0.00</h3>
            <small class="text-muted fw-bold" style="font-size: 10px;">Ventas a crédito</small>
        </div>
    </div>
</div>

<div class="row mb-4 g-3">
    <div class="col-md-6">
        <div class="glass-card p-3 border-start border-danger border-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <small class="text-muted fw-bold d-block text-xs">EGRESOS TOTALES (SALIDAS)</small>
                    <div class="d-flex gap-4 mt-2">
                        <div>
                            <small class="d-block text-secondary">Compras</small>
                            <h4 class="fw-bold text-danger mb-0" id="rescompras-totales">$0.00</h4>
                        </div>
                        <div class="border-start ps-4">
                            <small class="d-block text-secondary">Gastos</small>
                            <h4 class="fw-bold text-danger mb-0" id="resgastos-totales">$0.00</h4>
                        </div>
                    </div>
                </div>
                <div class="text-end">
                    <i class="fas fa-file-invoice-dollar fa-2x text-light"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="glass-card p-3 border-start border-info border-4 h-100">
            <small class="text-muted fw-bold d-block mb-2 text-xs">RESUMEN DE COBRANZA</small>
            <div class="d-flex justify-content-between align-items-center mt-1">
                <span class="text-secondary small">Total Cobrado (Hoy + Saldos):</span>
                <span class="fw-bold text-dark h5 mb-0" id="res-cobrado-total">$0.00</span>
            </div>
            <div class="d-flex justify-content-between align-items-center mt-2 pt-2 border-top">
                <span class="text-secondary small">Abonos recibidos en efectivo:</span>
                <span class="badge bg-soft-success py-2 px-3" id="resabonos-totales">$0.00</span>
            </div>
        </div>
    </div>
</div>
<button type="button" 
        class="btn btn-dark shadow-sm px-4 py-2 d-flex align-items-center" 
        style="border-radius: 10px;"
        data-bs-toggle="modal" 
        data-bs-target="#modalCorteCaja"> <i class="fas fa-cash-register me-2"></i>
    <span class="fw-bold">FINALIZAR CORTE DE CAJA</span>
</button>

            <div class="glass-card position-relative overflow-hidden">
                <div id="tabla-loader" class="loading-overlay">
                    <div class="spinner-border text-primary"></div>
                </div>
                
                <div class="table-responsive">
                    <table id="tablaDetalles" class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th class="ps-4">FOLIO / ORIGEN</th>
                                <th>CLIENTE / PERSONAL</th>
                                <th>DETALLE VENTA</th>
                                <th>MÉTODO / RECIBIÓ</th>
                                <th class="text-end">INGRESO REAL</th>
                                <th class="text-end">SALDO FAVOR</th>
                                <th class="text-end pe-4">DEUDA VIVA</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
   
    <?php if (function_exists('cargarScripts')) { cargarScripts(); } ?>

   <script>
    const AppCaja = {
        config: { url: '/cfsistem/app/controllers/corteCajaController.php' },

        init: function() {
            this.bindEvents();
            this.update();
        },

        bindEvents: function() {
            $('#periodo').on('change', function() {
                if ($(this).val() === 'personalizado') {
                    $('#div-fechas').removeClass('d-none');
                } else {
                    $('#div-fechas').addClass('d-none');
                    AppCaja.update();
                }
            });
            $('#almacen_id').on('change', () => this.update());
        },

        update: function() {
            $('#tabla-loader').css('display', 'flex');
            
            const dataPura = {
                ajax: 1,
                periodo: $('#periodo').val(),
                f_inicio: $('#f_inicio').val(),
                f_fin: $('#f_fin').val(),
                almacen_id: $('#almacen_id').val()
            };

            $.getJSON(this.config.url, dataPura, (res) => {
                // Sincronizado con los nombres que devuelve tu controlador
                this.renderTotales(res.totales);
                this.renderTabla(res.detalles);
            }).fail((err) => {
                console.error("Error al obtener datos:", err.responseText);
            }).always(() => {
                $('#tabla-loader').hide();
            });
        },

 renderTotales: function(s) {
    console.log(s);
    // 1. Lo que entró a caja/bancos hoy (Ventas de hoy + Abonos de hoy)
    $('#res-total').text(this.formatMoney(s?.gran_total_ingresos || 0));
    let totalventas=s?.total_efectivo+s?.total_tarjeta+s?.total_transferencia-s?.abonos_totales;
    $('#res-total-ventas').text(this.formatMoney(totalventas || 0));
    // 2. CORRECCIÓN: Aquí debes usar abonos_totales, no gran_total_ingresos
    $('#res-abono').text(this.formatMoney(s?.abonos_totales || 0));
    
    // 3. Desglose de dinero físico/bancos
    $('#res-efectivo').text(this.formatMoney(s?.total_efectivo || 0));
    $('#res-tarjeta').text(this.formatMoney(s?.total_tarjeta || 0));
    
    // 4. Saldo y Deuda
    $('#res-saldo-usado').text(this.formatMoney(s?.saldo_favor_usado || 0));
    $('#res-deuda').text(this.formatMoney(s?.deuda_pendiente || 0));
    
    // Totales secundarios
    $('#res-cobrado-total').text(this.formatMoney(s?.cobrado_total || 0));
    
    // 5. OJO: Revisa si tu HTML tiene este ID exacto o si le falta un guion
    // Si en el HTML es id="res-abonos-totales", cámbialo aquí abajo:
    $('#resabonos-totales').text(this.formatMoney(s?.abonos_totales || 0));
    $('#rescompras-totales').text(this.formatMoney(s?.compras_totales || 0));
    $('#resgastos-totales').text(this.formatMoney(s?.gastos_totales || 0));
},
renderTabla: function(data) {
    let html = '';
    if (data && data.length > 0) {
        data.forEach(v => {
            const dReal  = parseFloat(v.dinero_real || 0);
            const sFavor = parseFloat(v.uso_saldo_favor || 0);
            const dViva  = parseFloat(v.deuda_viva || 0);

            // Construir lista de productos dentro de la celda
            let productosHtml = '';
            if (v.productos && Array.isArray(v.productos) && v.productos.length > 0) {
                v.productos.forEach(p => {
                    productosHtml += `
                        <div class="d-flex flex-column mb-1">
                            <span class="small fw-semibold text-dark text-truncate" style="max-width:150px">${p.producto}</span>
                            <small class="text-muted">${p.cantidad}</small>
                        </div>`;
                });
            }

            html += `
                <tr class="border-bottom">
                    <td class="ps-4">
                        <span class="fw-bold d-block text-dark">${v.folio}</span>
                        <span class="origen-tag ${v.tipo === 'VENTA DÍA' ? 'bg-primary-subtle text-primary' : 'bg-warning-subtle text-warning'}">${v.tipo}</span>
                    </td>
                    <td><span class="fw-semibold d-block small">${v.cliente}</span></td>
                    <td>${productosHtml}</td>
                    <td>
                        <span class="badge bg-white text-dark border shadow-sm badge-metodo">${v.metodo_pago}</span>
                    </td>
                    <td class="text-end fw-bold text-success">
                        ${dReal > 0 ? this.formatMoney(dReal) : '<span class="text-muted opacity-50">$0.00</span>'}
                    </td>
                    <td class="text-end fw-semibold text-warning">
                        ${sFavor > 0 ? this.formatMoney(sFavor) : '-'}
                    </td>
                    <td class="text-end pe-4 ${dViva > 0 ? 'text-danger fw-bold' : 'text-muted small'}">
                        ${dViva > 0 ? this.formatMoney(dViva) : 'PAGADO'}
                    </td>
                </tr>`;
        });
    } else {
        html = '<tr><td colspan="7" class="text-center py-5 text-muted">No hay movimientos</td></tr>';
    }
    $('#tablaDetalles tbody').html(html);
},   
formatMoney: function(amount) {
            return '$' + parseFloat(amount).toLocaleString('es-MX', {minimumFractionDigits: 2});
        }
    };

    $(document).ready(() => AppCaja.init());
    
</script>
 <?php require_once __DIR__ . '/corteCaja/guardarCorteDeCaja.php'; ?>
</body>
</html>