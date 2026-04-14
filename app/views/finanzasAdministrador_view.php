<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Finanzas Globales | Cf System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    
    <?php require_once __DIR__ . '/layout/icono.php' ?>
    <?php if (function_exists('cargarEstilos')) { cargarEstilos(); } ?>
    <style>
        :root { --apple-bg: #f5f5f7; --apple-blue: #007aff; }
        body { background-color: var(--apple-bg); font-family: -apple-system, sans-serif; }
        .main-content { margin-left: 260px; padding: 80px 20px; transition: 0.3s; }
        
        /* Glass Style */
   
    </style>
</head>
<body>

    <?php renderizarLayout($paginaActual); ?>

    <main class="main-content">
    <div class="container-fluid py-4" style="background-color: #f5f7fa; min-height: 100vh;">
    
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 20px; background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(10px);">
        <div class="card-body p-4">
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-secondary">ALMACÉN</label>
                    <select id="filtro_almacen" class="form-select border-0 bg-light" style="border-radius: 12px;">
                        <?php if ($rol_id == 1): ?>
                            <option value="0">Todos los Almacenes</option>
                            <?php foreach ($listaAlmacenes as $a): ?>
                                <option value="<?= $a['id'] ?>"><?= $a['nombre'] ?></option>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <option value="<?= $almacen_sesion ?>">Mi Almacén</option>
                        <?php endif; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-secondary">PERIODO</label>
                    <select id="filtro_periodo" class="form-select border-0 bg-light" style="border-radius: 12px;">
                        <option value="hoy">Hoy</option>
                        <option value="ayer">Ayer</option>
                        <option value="semana">Esta Semana</option>
                        <option value="mes">Este Mes</option>
                        <option value="personalizado">Personalizado</option>
                    </select>
                </div>
                <div id="rango_personalizado" class="col-md-4 d-none">
                    <div class="d-flex gap-2">
                        <input type="date" id="f_inicio" class="form-control border-0 bg-light" style="border-radius: 12px;" value="<?= date('Y-m-d') ?>">
                        <input type="date" id="f_fin" class="form-control border-0 bg-light" style="border-radius: 12px;" value="<?= date('Y-m-d') ?>">
                    </div>
                </div>
                <div class="col-md-2">
                    <button onclick="cargarData()" class="btn btn-primary w-100 fw-bold" style="border-radius: 12px; background: #007AFF; border: none; height: 45px;">
                        <i class="fas fa-sync-alt me-2"></i>Actualizar
                    </button>
                </div>
            </div>
        </div>
    </div>
<div id="contenedor-totales-egresos" class="mb-4">
    </div>
    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card border-0 shadow-sm" style="border-radius: 20px; overflow: hidden;">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h6 class="fw-bold"><i class="fas fa-vault me-2 text-primary"></i>Saldos Iniciales / Apertura</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead class="small text-secondary">
                                <tr>
                                    <th>Almacén</th>
                                    <th class="text-end">Efectivo</th>
                                    <th class="text-end">Total</th>
                                </tr>
                            </thead>
                            <tbody id="body_apertura" class="small"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card border-0 shadow-sm" style="border-radius: 20px; overflow: hidden;">
                <div class="col-md-12 mb-4"> <div class="card border-0 shadow-sm" style="border-radius: 20px; overflow: hidden;">
        <div class="card-header bg-white border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
            <h6 class="fw-bold"><i class="fas fa-shopping-cart me-2 text-success"></i>Ventas Detalladas</h6>
            <select id="metodo_pago_filtro" class="form-select form-select-sm w-auto border-0 bg-light" onchange="FinanzasUI.cargarTodo()">
                <option value="todos">Todos los métodos</option>
                <option value="EFECTIVO">Efectivo</option>
                <option value="TARJETA">Tarjeta</option>
                <option value="TRANSFERENCIA">Transferencia</option>
                <option value="Saldo a Favor">Saldo a Favor</option>
                <option value="Null">Cuentas por Cobrar</option>
            </select>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="tablaDetalles" class="table align-middle">
                    <thead class="small text-secondary text-uppercase" style="font-size: 10px;">
                        <tr>
                            <th class="ps-4">Folio / Tipo</th>
                            <th>Cliente</th>
                            <th>Productos</th>
                            <th>Método</th>
                            <th class="text-end">Ingreso Real</th>
                            <th class="text-end">Uso Saldo</th>
                            <th class="text-end pe-4">Estatus/Deuda</th>
                        </tr>
                    </thead>
                    <tbody id="body_ventas"></tbody>
                </table>
            </div>
        </div>
    </div>
</div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card border-0 shadow-sm" style="border-radius: 20px; overflow: hidden;">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h6 class="fw-bold"><i class="fas fa-arrow-down me-2 text-danger"></i>Gastos Operativos</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead class="small text-secondary">
                                <tr>
                                     <th>Almacen</th>
                                       <th>Fecha</th>
                                    <th>Folio</th>
                                    <th>Beneficiario</th>
                                    <th class="text-end">Monto</th>
                                </tr>
                            </thead>
                            <tbody id="body_gastos" class="small"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card border-0 shadow-sm" style="border-radius: 20px; overflow: hidden;">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h6 class="fw-bold"><i class="fas fa-truck me-2 text-warning"></i>Compras de Inventario</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead class="small text-secondary">
                                <tr>
                                    <th>Almacen</th>
                                    <th>Fecha</th>
                                    <th>Folio</th>
                                    <th>Proveedor</th>
                                    <th class="text-end">Total</th>
                                </tr>
                            </thead>
                            <tbody id="body_compras" class="small"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


    </main>

    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
const url = "/cfsistem/app/controllers/finanzasAdmController.php";

const FinanzasUI = {
    // Helper para moneda (iOS Style)
    formatMoney: (n) => `$${parseFloat(n || 0).toLocaleString('es-MX', { minimumFractionDigits: 2 })}`,

    getFilters: function() {
        return {
            ajax: 1,
            almacen_id: document.getElementById('filtro_almacen').value,
            periodo: document.getElementById('filtro_periodo').value,
            f_inicio: document.getElementById('f_inicio').value,
            f_fin: document.getElementById('f_fin').value
        };
    },

    setLoading: function(ids) {
        ids.forEach(id => {
            const el = document.getElementById(id);
            if (el) el.innerHTML = '<tr><td colspan="10" class="text-center py-3"><div class="spinner-border spinner-border-sm text-primary"></div></td></tr>';
        });
    },

    cargarTodo: async function() {
        this.setLoading(['body_apertura', 'body_ventas', 'body_gastos', 'body_compras']);
        
        try {
            const params = new URLSearchParams(this.getFilters());
            const response = await fetch(`${url}?${params}`); 
            const res = await response.json();

            if (res.status === 'success') {
                console.log(res.gastosTotales);
                console.log(res.comprasTotales);
                const esLista = (document.getElementById('filtro_almacen').value == 0);
                
                // 1. Aperturas (Saldo Inicial)
                this.renderSaldoInicial(res.saldos_raw || [], esLista);
                
                // 2. Ventas (Usa la lógica detallada de 7 columnas)
                this.renderVentas(res.ventas || []);
                
                // 3. Egresos (Gastos y Compras comparten estructura simple)
                this.renderEgresos('body_gastos', res.gastos || []);
                this.renderEgresos('body_compras', res.compras || []);
                this.renderTarjetasTotales(res.gastosTotales, res.comprasTotales);
               
            }
        } catch (error) {
            console.error("Error cargando datos:", error);
        }
    },
      renderEgresos: function(data, esLista) {
        const body = document.getElementById('body_apertura');
        if (!body) return;

        if (!Array.isArray(data)) data = (data && typeof data === 'object') ? [data] : [];

        if (esLista) {
            let html = data.length > 0 ? data.map(s => `
                <tr>
                    <td><span class="badge bg-light text-dark border-0">${s?.almacen || s?.almacen_nombre || 'Sucursal'}</span></td>
                    <td class="text-end">${this.formatMoney(s.monto_efectivo)}</td>
                    <td class="text-end">${this.formatMoney(s.monto_tarjeta)}</td>
                    <td class="text-end">${this.formatMoney(s.monto_transferencia)}</td>
                    <td class="text-end fw-bold text-primary">${this.formatMoney(s.monto)}</td>
                </tr>
            `).join('') : '<tr><td colspan="5" class="text-center py-3 text-muted small">Sin registros</td></tr>';
            body.innerHTML = html;
        } else {
            const d = data[0] || {};
            body.innerHTML = `
                <tr>
                    <td class="fw-bold text-primary">SALDO TOTAL</td>
                    <td class="text-end">${this.formatMoney(d.monto_efectivo)}</td>
                    <td class="text-end">${this.formatMoney(d.monto_tarjeta)}</td>
                    <td class="text-end">${this.formatMoney(d.monto_transferencia)}</td>
                    <td class="text-end fw-bold text-dark" style="font-size:1.1em;">${this.formatMoney(d.monto)}</td>
                </tr>`;
        }
    },
// Dentro de FinanzasUI...

renderTarjetasTotales: function(gastosTotal, comprasTotal) {
    const $contenedor = $('#contenedor-totales-egresos');
    if (!$contenedor.length) return;

    const totalGeneral = parseFloat(gastosTotal || 0) + parseFloat(comprasTotal || 0);

    $contenedor.html(`
        <div class="card border-0 shadow-sm animate__animated animate__fadeIn" 
             style="border-radius: 20px; background: #fff; overflow: hidden;">
            <div class="card-body p-0">
                <div class="row g-0">
                    <div class="col-6 p-4 border-end" style="background: linear-gradient(145deg, #ffffff, #fdf8f8);">
                        <div class="d-flex align-items-center mb-2">
                            <div class="icon-shape bg-danger-subtle text-danger rounded-circle p-2 me-2">
                                <i class="bi bi-arrow-down-right-circle-fill fs-5"></i>
                            </div>
                            <small class="fw-bold text-muted text-uppercase" style="font-size: 10px; letter-spacing: 1px;">Gastos Operativos</small>
                        </div>
                        <h3 class="fw-bold text-dark mb-0">${this.formatMoney(gastosTotal)}</h3>
                    </div>

                    <div class="col-6 p-4" style="background: linear-gradient(145deg, #ffffff, #fffdf8);">
                        <div class="d-flex align-items-center mb-2">
                            <div class="icon-shape bg-warning-subtle text-warning rounded-circle p-2 me-2">
                                <i class="bi bi-cart-dash-fill fs-5"></i>
                            </div>
                            <small class="fw-bold text-muted text-uppercase" style="font-size: 10px; letter-spacing: 1px;">Compras Inventario</small>
                        </div>
                        <h3 class="fw-bold text-dark mb-0">${this.formatMoney(comprasTotal)}</h3>
                    </div>
                </div>
                
                <div class="bg-light p-2 text-center border-top">
                    <small class="text-muted small">Total Egresos: <span class="fw-bold text-dark">${this.formatMoney(totalGeneral)}</span></small>
                </div>
            </div>
        </div>
    `);
},
    renderSaldoInicial: function(data, esLista) {
        const body = document.getElementById('body_apertura');
        if (!body) return;

        if (!Array.isArray(data)) data = (data && typeof data === 'object') ? [data] : [];

        if (esLista) {
            let html = data.length > 0 ? data.map(s => `
                <tr>
                    <td><span class="badge bg-light text-dark border-0">${s?.almacen || s?.almacen_nombre || 'Sucursal'}</span></td>
                    <td class="text-end">${this.formatMoney(s.monto_efectivo)}</td>
                    <td class="text-end">${this.formatMoney(s.monto_tarjeta)}</td>
                    <td class="text-end">${this.formatMoney(s.monto_transferencia)}</td>
                    <td class="text-end fw-bold text-primary">${this.formatMoney(s.monto)}</td>
                </tr>
            `).join('') : '<tr><td colspan="5" class="text-center py-3 text-muted small">Sin registros</td></tr>';
            body.innerHTML = html;
        } else {
            const d = data[0] || {};
            body.innerHTML = `
                <tr>
                    <td class="fw-bold text-primary">SALDO TOTAL</td>
                    <td class="text-end">${this.formatMoney(d.monto_efectivo)}</td>
                    <td class="text-end">${this.formatMoney(d.monto_tarjeta)}</td>
                    <td class="text-end">${this.formatMoney(d.monto_transferencia)}</td>
                    <td class="text-end fw-bold text-dark" style="font-size:1.1em;">${this.formatMoney(d.monto)}</td>
                </tr>`;
        }
    },

    renderVentas: function(data) {
        const body = document.getElementById('body_ventas');
        if (!body) return;

        const metodoFiltro = document.getElementById('metodo_pago_filtro')?.value || 'todos';

        // Filtrado por método de pago
        const dataFiltrada = data.filter(v => {
            const metodo = (v.metodo_pago || '').toUpperCase();
            const deuda = parseFloat(v.deuda_viva || 0);
            const saldo = parseFloat(v.uso_saldo_favor || 0);

            if (metodoFiltro === 'todos') return true;
            if (metodoFiltro === 'EFECTIVO') return metodo === 'EFECTIVO';
            if (metodoFiltro === 'TARJETA') return metodo === 'TARJETA';
            if (metodoFiltro === 'TRANSFERENCIA') return metodo === 'TRANSFERENCIA';
            if (metodoFiltro === 'Saldo a Favor') return metodo.includes('SALDO') || saldo > 0;
            if (metodoFiltro === 'Null') return deuda > 0;
            return true;
        });

        if (dataFiltrada.length === 0) {
            body.innerHTML = '<tr><td colspan="7" class="text-center py-5 text-muted">No hay resultados</td></tr>';
            return;
        }

        let html = '';
        dataFiltrada.forEach(v => {
            let productosHtml = '';
            if (v.productos && Array.isArray(v.productos)) {
                v.productos.forEach(p => {
                    productosHtml += `
                        <div class="d-flex flex-column mb-1" style="line-height:1;">
                            <span class="small fw-semibold text-dark text-truncate" style="max-width:150px">${p.producto}</span>
                            <small class="text-muted" style="font-size:9px;">Cant: ${p.cantidad}</small>
                        </div>`;
                });
            }

            html += `
                <tr class="border-bottom animate__animated animate__fadeInUp">
                    <td class="ps-4">
                        <span class="fw-bold d-block text-dark small">${v.folio || 'S/F'}</span>
                        <span class="badge ${v.tipo === 'VENTA DÍA' ? 'bg-primary-subtle text-primary' : 'bg-warning-subtle text-warning'}" style="font-size:8px;">${v.tipo}</span>
                        <small class="d-block text-muted" style="font-size:9px;">${v.fecha || ''}</small>
                    </td>
                    <td>
                        <span class="fw-semibold d-block small">${v.cliente || 'Público General'}</span>
                        <small class="text-muted" style="font-size:9px;">${v.almacen_nombre || 'N/A'}</small>
                    </td>
                    <td>${productosHtml}</td>
                    <td><span class="badge bg-white text-dark border shadow-sm" style="font-size:9px;">${v.metodo_pago}</span></td>
                    <td class="text-end fw-bold text-success">${this.formatMoney(v.dinero_real || v.efectivo)}</td>
                    <td class="text-end fw-semibold text-warning">${v.uso_saldo_favor > 0 ? this.formatMoney(v.uso_saldo_favor) : '-'}</td>
                    <td class="text-end pe-4 ${v.deuda_viva > 0 ? 'text-danger fw-bold' : 'text-muted small'}">
                        ${v.deuda_viva > 0 ? this.formatMoney(v.deuda_viva) : '<i class="bi bi-check-circle-fill text-success"></i>'}
                    </td>
                </tr>`;
        });
        body.innerHTML = html;
    },

    renderEgresos: function(id, data) {
        const body = document.getElementById(id);
        if (!body) return;

        if (data.length === 0) {
            body.innerHTML = '<tr><td colspan="10" class="text-center text-muted small py-3">No hay datos disponibles</td></tr>';
            return;
        }

        let html = '';
        data.forEach(item => {
            const entidad = item.entidad || item.proveedor || item.beneficiario || 'N/A';
            html += `
                <tr>
                    <td class="small fw-bold text-secondary">${item.almacen_nombre || 'N/A'}</td>
                    <td class="small text-muted">${item.fecha || ''}</td>
                    <td class="fw-bold">${item.folio}</td>
                    <td>${entidad}</td>
                    <td class="text-end fw-bold text-danger">-${this.formatMoney(item.total)}</td>
                </tr>`;
        });
        body.innerHTML = html;
    }
};

// Eventos
document.addEventListener('DOMContentLoaded', () => {
    FinanzasUI.cargarTodo();
    
    document.getElementById('filtro_almacen').addEventListener('change', () => FinanzasUI.cargarTodo());

    document.getElementById('filtro_periodo').addEventListener('change', function() {
        const rango = document.getElementById('rango_personalizado');
        if (this.value === 'personalizado') {
            rango.classList.remove('d-none');
        } else {
            rango.classList.add('d-none');
            FinanzasUI.cargarTodo();
        }
    });
});

function cargarData() {
    FinanzasUI.cargarTodo();
}
</script>

<style>
    .table thead th { border: none; letter-spacing: 0.5px; }
    .table td { border-top: 1px solid #f0f2f5; padding: 12px 8px; }
    .btn-primary:active { transform: scale(0.98); }
</style>
</script>
</body>
</html>