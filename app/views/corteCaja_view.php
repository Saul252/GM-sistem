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
    <!-- CONTENEDOR DEL SALDO INICIAL: empieza visible, JS lo controla -->
    <div id="contenedor-saldo-inicial" class="mb-4 animate__animated animate__fadeIn">
    </div>

   <div class="row g-3">
    <div class="col-md-4">
        <div class="glass-card p-3 border-bottom border-dark border-4 h-100 text-center">
            <small class="text-muted fw-bold d-block mb-1 text-xs">VENTA TOTAL (BRUTA)</small>
            <h3 class="fw-bold text-dark mb-0" id="res-venta-bruta">$0.00</h3>
            <small class="text-muted" style="font-size: 10px;">Todo lo vendido (Pagado + Deuda)</small>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="glass-card p-3 border-bottom border-danger border-4 h-100 text-center">
            <small class="text-muted fw-bold d-block mb-1 text-xs">NOS DEBEN (DE HOY)</small>
            <h3 class="fw-bold text-danger mb-0" id="res-deuda">$0.00</h3>
            <small class="text-danger" style="font-size: 10px;">Ventas a crédito de este periodo</small>
        </div>
    </div>

    <div class="col-md-4">
        <div class="glass-card p-3 border-bottom border-warning border-4 h-100 text-center">
            <small class="text-muted fw-bold d-block mb-1 text-xs">SALDO A FAVOR USADO</small>
            <h3 class="fw-bold text-warning mb-0" id="res-saldo-favor">$0.00</h3>
            <small class="text-warning" style="font-size: 10px;">Crédito de clientes aplicado</small>
        </div>
    </div>

    <hr class="my-3 opacity-0"> <div class="col-md-4">
        <div class="glass-card p-3 border-start border-success border-4 h-100">
            <small class="text-muted fw-bold d-block mb-2 text-xs text-center">TOTAL EFECTIVO (CAJA)</small>
            <h4 class="fw-bold text-center mb-0" id="res-total-efectivo">$0.00</h4>
        </div>
    </div>
    <div class="col-md-4">
        <div class="glass-card p-3 border-start border-primary border-4 h-100">
            <small class="text-muted fw-bold d-block mb-2 text-xs text-center">TOTAL TARJETA</small>
            <h4 class="fw-bold text-center mb-0" id="res-total-tarjeta">$0.00</h4>
        </div>
    </div>
    <div class="col-md-4">
        <div class="glass-card p-3 border-start border-info border-4 h-100">
            <small class="text-muted fw-bold d-block mb-2 text-xs text-center">TOTAL TRANSFERENCIA</small>
            <h4 class="fw-bold text-center mb-0" id="res-total-trans">$0.00</h4>
        </div>
    </div>

    <hr class="my-3 opacity-0"> <div class="col-md-4">
        <div class="glass-card p-3 border-start border-success border-4 h-100">
            <small class="text-muted fw-bold d-block mb-2 text-xs text-center">TOTAL EFECTIVO MAS SALDO INICIAL</small>
            <h4 class="fw-bold text-center mb-0" id="res-total-efectivoMasSaldo">$0.00</h4>
        </div>
    </div>
    <div class="col-md-4">
        <div class="glass-card p-3 border-start border-primary border-4 h-100">
            <small class="text-muted fw-bold d-block mb-2 text-xs text-center">TOTAL TARJETA MAS SALDO INICIAL</small>
            <h4 class="fw-bold text-center mb-0" id="res-total-tarjetaMasSaldo">$0.00</h4>
        </div>
    </div>
    <div class="col-md-4">
        <div class="glass-card p-3 border-start border-info border-4 h-100">
            <small class="text-muted fw-bold d-block mb-2 text-xs text-center">TOTAL TRANSFERENCIA MAS SALDO INICIAL</small>
            <h4 class="fw-bold text-center mb-0" id="res-total-transMasSaldo">$0.00</h4>
        </div>
    </div>

    <div class="col-12 mt-4"><h6 class="fw-bold text-muted">¿Cuánto entró por Ventas de Hoy?</h6></div>
    <div class="col-md-4">
        <div class="glass-card p-3 h-100 bg-soft-info">
            <small class="text-xs d-block">Efectivo de Ventas</small>
            <span class="h5 fw-bold" id="res-v-efectivo">$0.00</span>
        </div>
    </div>
    <div class="col-md-4">
        <div class="glass-card p-3 h-100 bg-soft-info">
            <small class="text-xs d-block">Tarjeta de Ventas</small>
            <span class="h5 fw-bold" id="res-v-tarjeta">$0.00</span>
        </div>
    </div>
    <div class="col-md-4">
        <div class="glass-card p-3 h-100 bg-soft-info">
            <small class="text-xs d-block">Transf. de Ventas</small>
            <span class="h5 fw-bold" id="res-v-trans">$0.00</span>
        </div>
    </div>

    <div class="col-12 mt-4"><h6 class="fw-bold text-muted">¿Cuánto entró por Abonos (Deudas Viejas)?</h6></div>
    <div class="col-md-4">
        <div class="glass-card p-3 h-100 bg-soft-warning">
            <small class="text-xs d-block">Efectivo de Abonos</small>
            <span class="h5 fw-bold" id="res-a-efectivo">$0.00</span>
        </div>
    </div>
    <div class="col-md-4">
        <div class="glass-card p-3 h-100 bg-soft-warning">
            <small class="text-xs d-block">Tarjeta de Abonos</small>
            <span class="h5 fw-bold" id="res-a-tarjeta">$0.00</span>
        </div>
    </div>
    <div class="col-md-4">
        <div class="glass-card p-3 h-100 bg-soft-warning">
            <small class="text-xs d-block">Transf. de Abonos</small>
            <span class="h5 fw-bold" id="res-a-trans">$0.00</span>
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

   
</div>

<button type="button" 
        class="btn btn-dark shadow-sm px-4 py-2 d-flex align-items-center" 
        style="border-radius: 10px;"
        data-bs-toggle="modal" 
        data-bs-target="#modalCorteCaja">
    <i class="fas fa-cash-register me-2"></i>
    <span class="fw-bold">FINALIZAR CORTE DE CAJA</span>
</button>

            <div class="glass-card position-relative overflow-hidden mt-4">
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
/**
 * Determina si el periodo seleccionado requiere mostrar el saldo inicial.
 * Solo se muestra para "hoy" y "ayer".
 */
function periodoRequiereSaldo(periodo) {
    return periodo === 'hoy' || periodo === 'ayer';
}

const AppCaja = {
    config: { 
        url: '/cfsistem/app/controllers/corteCajaController.php' 
    },

    init: function() {
        this.bindEvents();
        this.update(); 
    },

    bindEvents: function() {
        const self = this;

        $('#periodo').on('change', function() {
            const periodo = $(this).val();

            // Si el periodo NO requiere saldo, ocultamos y vaciamos el contenedor de inmediato
            if (!periodoRequiereSaldo(periodo)) {
                $('#contenedor-saldo-inicial').empty().hide();
            }

            // Manejo del selector de fechas personalizadas
            if (periodo === 'personalizado') {
                $('#div-fechas').removeClass('d-none').addClass('animate__animated animate__fadeIn');
            } else {
                $('#div-fechas').addClass('d-none');
                self.update();
            }
        });

        $('#almacen_id').on('change', function() {
            self.update();
        });
    },

    update: function() {
        $('#tabla-loader').css('display', 'flex');

        const params = {
            ajax: 1,
            periodo: $('#periodo').val(),
            f_inicio: $('#f_inicio').val(),
            f_fin: $('#f_fin').val(),
            almacen_id: $('#almacen_id').val()
        };

        $.getJSON(this.config.url, params, (res) => {
            if (res.status === 'success' || res.totales) {
                // La decisión de mostrar el saldo la toma el CLIENTE según el periodo
                // No se usa res.mostrar_saldo para evitar que el servidor sobreescriba la lógica
                const mostrar = periodoRequiereSaldo(params.periodo);
                this.renderSaldoInicial(res.saldo_inicial, res.es_lista, mostrar);
                this.renderTotales(res.totales, res.saldo_inicial, res.es_lista, mostrar);
                this.renderTabla(res.detalles);
            }
        }).always(() => {
            $('#tabla-loader').hide();
        });
    },

    renderSaldoInicial: function(data, esLista, mostrar) {
        const $contenedor = $('#contenedor-saldo-inicial');
        if (!$contenedor.length) return;

        // Si el periodo no requiere saldo: limpiar, ocultar y salir
        if (!mostrar) {
            $contenedor.empty().hide();
            return;
        }

        // El periodo requiere saldo: renderizar y mostrar
        if (esLista) {
            let filas = (data && data.length > 0)
                ? data.map(s => `
                    <tr>
                        <td class="ps-4 fw-bold text-secondary small text-uppercase">${s.almacen}</td>
                        <td class="text-end fw-semibold text-dark small">${this.formatMoney(s?.monto_efectivo)}</td>
                        <td class="text-end fw-semibold text-dark small">${this.formatMoney(s?.monto_tarjeta)}</td>
                        <td class="text-end fw-semibold text-dark small">${this.formatMoney(s?.monto_transferencia)}</td>
                        <td class="text-end pe-4 fw-bold text-primary">${this.formatMoney(s?.monto)}</td>
                    </tr>
                `).join('')
                : '<tr><td colspan="5" class="text-center py-3 text-muted small">Sin registros</td></tr>';

            $contenedor.html(`
                <div class="glass-card overflow-hidden animate__animated animate__fadeIn">
                    <div class="p-3 border-bottom bg-light bg-opacity-50">
                        <h6 class="m-0 fw-bold text-dark text-xs text-uppercase">
                            <i class="bi bi-houses-fill me-2 text-primary"></i> Apertura Global por Sucursal
                        </h6>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr class="text-muted" style="font-size: 10px;">
                                    <th class="ps-4">ALMACÉN</th>
                                    <th class="text-end">EFECTIVO</th>
                                    <th class="text-end">TARJETA</th>
                                    <th class="text-end">TRANSF.</th>
                                    <th class="text-end pe-4">TOTAL</th>
                                </tr>
                            </thead>
                            <tbody>${filas}</tbody>
                        </table>
                    </div>
                </div>
            `).show();

        } else {
            const d = data || {};
            const total = this.formatMoney(d.monto || 0);
            const efec  = this.formatMoney(d.monto_efectivo || 0);
            const tarj  = this.formatMoney(d.monto_tarjeta || 0);
            const tran  = this.formatMoney(d.monto_transferencia || 0);

            $contenedor.html(`
                <div class="card border-0 shadow-sm text-white animate__animated animate__fadeInRight" 
                     style="background: linear-gradient(90deg, #007aff, #00c6ff); border-radius:15px;">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <small class="text-white-50 fw-bold d-block text-xs text-uppercase" style="letter-spacing:1px;">Saldo Inicial (Total)</small>
                                <h2 class="fw-bold mb-0">${total}</h2>
                            </div>
                            <i class="bi bi-safe2 fs-4 bg-white bg-opacity-20 p-2 rounded-circle"></i>
                        </div>
                        <div class="row g-0 pt-2 border-top border-white border-opacity-10 text-center">
                            <div class="col-4 border-end border-white border-opacity-10">
                                <small class="d-block text-white-50 text-xxs">EFECTIVO</small>
                                <span class="fw-bold small">${efec}</span>
                            </div>
                            <div class="col-4 border-end border-white border-opacity-10">
                                <small class="d-block text-white-50 text-xxs">TARJETA</small>
                                <span class="fw-bold small">${tarj}</span>
                            </div>
                            <div class="col-4">
                                <small class="d-block text-white-50 text-xxs">TRANSF.</small>
                                <span class="fw-bold small">${tran}</span>
                            </div>
                        </div>
                    </div>
                </div>
            `).show();
        }
    },

  renderTotales: function(s, saldoIni, esLista, mostrar) {
    if (!s) return;
    const dIni = saldoIni || {};

    // Saldo inicial (si aplica)
    const iniEfec = (mostrar && !esLista) ? parseFloat(dIni.monto_efectivo || 0) : 0;
    const iniTar  = (mostrar && !esLista) ? parseFloat(dIni.monto_tarjeta || 0) : 0;
    const iniTra  = (mostrar && !esLista) ? parseFloat(dIni.monto_transferencia || 0) : 0;

    // --- 1. TOTALES GENERALES ---
    $('#res-venta-bruta').text(this.formatMoney(s.venta_bruta || 0));
    $('#res-deuda').text(this.formatMoney(s.deuda_pendiente || 0));
    $('#res-saldo-favor').text(this.formatMoney(s.saldo_favor_usado || 0));

   // --- 2. CAJA TOTAL (Independiente de dónde venga) ---
// Usamos directamente las propiedades que ya vienen sumadas del PHP

// Totales de lo que entró HOY (Venta + Abono)
$('#res-total-efectivo').text(this.formatMoney(parseFloat(s.ingreso_total_efectivo || 0)));
$('#res-total-tarjeta').text(this.formatMoney(parseFloat(s.ingreso_total_tarjeta || 0)));
$('#res-total-trans').text(this.formatMoney(parseFloat(s.ingreso_total_transfer || 0)));

// Totales incluyendo el Saldo Inicial (Caja Real al momento)
$('#res-total-efectivoMasSaldo').text(this.formatMoney(parseFloat(iniEfec || 0) + parseFloat(s.ingreso_total_efectivo || 0)));
$('#res-total-tarjetaMasSaldo').text(this.formatMoney(parseFloat(iniTar || 0) + parseFloat(s.ingreso_total_tarjeta || 0)));
$('#res-total-transMasSaldo').text(this.formatMoney(parseFloat(iniTra || 0) + parseFloat(s.ingreso_total_transfer || 0)));

// --- 3. SOLO VENTAS DE HOY ---
$('#res-v-efectivo').text(this.formatMoney(parseFloat(s.solo_venta_efectivo || 0)));
$('#res-v-tarjeta').text(this.formatMoney(parseFloat(s.solo_venta_tarjeta || 0)));
$('#res-v-trans').text(this.formatMoney(parseFloat(s.solo_venta_transfer || 0)));

// --- 4. SOLO ABONOS (RECUPERACIÓN) ---
$('#res-a-efectivo').text(this.formatMoney(parseFloat(s.abono_efectivo || 0)));
$('#res-a-tarjeta').text(this.formatMoney(parseFloat(s.abono_tarjeta || 0)));
$('#res-a-trans').text(this.formatMoney(parseFloat(s.abono_transferencia || 0)));
},
    renderTabla: function(data) {
        let html = '';
        if (data && data.length > 0) {
            data.forEach(v => {
                const dReal  = parseFloat(v.dinero_real || 0);
                const sFavor = parseFloat(v.uso_saldo_favor || 0);
                const dViva  = parseFloat(v.deuda_viva || 0);

                let productosHtml = '';
                if (v.productos && Array.isArray(v.productos)) {
                    v.productos.forEach(p => {
                        productosHtml += `
                            <div class="d-flex flex-column mb-1">
                                <span class="small fw-semibold text-dark text-truncate" style="max-width:180px">${p.producto}</span>
                                <small class="text-muted" style="font-size:9px;">Cant: ${p.cantidad}</small>
                            </div>`;
                    });
                }

                html += `
                    <tr class="border-bottom animate__animated animate__fadeInUp">
                        <td class="ps-4">
                            <span class="fw-bold d-block text-dark">${v.folio || 'S/F'}</span>
                            <span class="origen-tag ${v.tipo === 'VENTA DÍA' ? 'bg-primary-subtle text-primary' : 'bg-warning-subtle text-warning'}">${v.tipo}</span>
                        </td>
                        <td><span class="fw-semibold d-block small">${v.cliente || 'Público General'}</span></td>
                        <td>${productosHtml}</td>
                        <td><span class="badge bg-white text-dark border shadow-sm badge-metodo">${v.metodo_pago}</span></td>
                        <td class="text-end fw-bold text-success">${this.formatMoney(dReal)}</td>
                        <td class="text-end fw-semibold text-warning">${sFavor > 0 ? this.formatMoney(sFavor) : '-'}</td>
                        <td class="text-end pe-4 ${dViva > 0 ? 'text-danger fw-bold' : 'text-muted small'}">
                            ${dViva > 0 ? this.formatMoney(dViva) : '<i class="bi bi-check-circle-fill text-success me-1"></i>PAGADO'}
                        </td>
                    </tr>`;
            });
        } else {
            html = '<tr><td colspan="7" class="text-center py-5 text-muted">No se encontraron movimientos</td></tr>';
        }
        $('#tablaDetalles tbody').html(html);
    },

    formatMoney: function(amount) {
        return '$' + parseFloat(amount || 0).toLocaleString('es-MX', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }
};

$(document).ready(() => AppCaja.init());
</script>

<?php require_once __DIR__ . '/corteCaja/guardarCorteDeCaja.php'; ?>
</body>
</html>