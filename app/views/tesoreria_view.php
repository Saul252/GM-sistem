<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Tesorería | Cf System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    
    <?php require_once __DIR__ . '/layout/icono.php' ?>
    <?php if (function_exists('cargarEstilos')) { cargarEstilos(); } ?>
    
    <style>
        :root { --apple-bg: #f5f5f7; --apple-blue: #007aff; }
        body { background-color: var(--apple-bg); font-family: -apple-system, sans-serif; }
        .main-content { margin-left: 260px; padding: 80px 20px; transition: 0.3s; }
        .glass-card { background: rgba(255,255,255,0.8); backdrop-filter: blur(15px); border-radius: 22px; border: none; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .ios-input { border: none; background: #eef0f2; border-radius: 12px; padding: 10px; font-size: 14px; }
        .ios-input:focus { background: #fff; box-shadow: 0 0 0 3px rgba(0,122,255,0.1); }
        .table thead th { background: transparent; text-transform: uppercase; letter-spacing: 0.8px; font-weight: 700; color: #8e8e93; font-size: 11px; border-bottom: 1px solid rgba(0,0,0,0.05); padding: 15px 10px; }
        .btn-ios { border-radius: 14px; font-weight: 600; transition: 0.2s; }
        .icon-box { width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; border-radius: 12px; background: rgba(0,122,255,0.1); color: var(--apple-blue); }
        @media (max-width: 992px) { .main-content { margin-left: 0; } }
    </style>
</head>
<body>

    <?php renderizarLayout($paginaActual); ?>

    <main class="main-content">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-end mb-4 animate__animated animate__fadeInDown">
                <div>
                    <h1 class="fw-bold m-0" style="letter-spacing: -1.5px;">Monitor de Capital</h1>
                    <p class="text-secondary m-0">Estado de saldos y flujos en tiempo real.</p>
                </div>
                <button type="button" class="btn btn-primary btn-ios px-4 py-2 shadow-sm" onclick="ModalMovimiento.abrir()">
                    <i class="bi bi-plus-lg me-2"></i> Registrar Movimiento
                </button>
            </div>

            <div class="glass-card p-4 mb-4 animate__animated animate__fadeIn">
                <form id="formFiltrosTesoreria" class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="small fw-bold text-muted text-uppercase mb-2 d-block">Sucursal a Monitorear</label>
                        <select id="filtro_almacen_id" class="form-select ios-input">
                            <option value="0">🌐 Todas las Sucursales</option>
                            <?php foreach($listaAlmacenes as $alm): ?>
                                <option value="<?= $alm['id'] ?>">📍 <?= $alm['nombre'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="small fw-bold text-muted text-uppercase mb-2 d-block">Fecha de Corte</label>
                        <input type="date" id="filtro_fecha" class="form-control ios-input" value="<?= date('Y-m-d') ?>">
                    </div>
                    <div class="col-md-2">
                        <button type="button" onclick="Tesoreria.listar()" class="btn btn-dark w-100 btn-ios py-2">
                            <i class="bi bi-arrow-clockwise me-1"></i> Actualizar
                        </button>
                    </div>
                </form>
            </div>

            <div class="glass-card position-relative overflow-hidden animate__animated animate__fadeInUp">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th class="ps-4">ALMACÉN / SUCURSAL</th>
                                <th class="text-end">EFECTIVO</th>
                                <th class="text-end">TARJETA</th>
                                <th class="text-end">TRANSFERENCIA</th>
                                <th class="text-end pe-4">CAPITAL TOTAL</th>
                            </tr>
                        </thead>
                        <tbody id="tbodyTesoreria"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

  

    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
    const Tesoreria = {
        url: '/cfsistem/app/controllers/tesoreriaController.php',
        init: function() {
            this.listar();
        },
        listar: function() {
            const params = {
                action: 'listar',
                almacen_id: $('#filtro_almacen_id').val(),
                fecha: $('#filtro_fecha').val()
            };
            $.getJSON(this.url, params, (res) => {
                let html = '';
                if (res.status === 'success' && res.data) {
                    if (Array.isArray(res.data)) {
                        res.data.forEach(m => html += this.renderFila(m));
                    } else {
                        html = this.renderFila(res.data);
                    }
                }
                $('#tbodyTesoreria').html(html || '<tr><td colspan="5" class="text-center py-5 text-muted">No hay registros</td></tr>');
            });
        },
        renderFila: function(m) {
            const colorClass = (val) => parseFloat(val) < 0 ? 'text-danger' : 'text-secondary';
            const badgeClass = (val) => parseFloat(val) < 0 ? 'bg-danger' : 'bg-primary';
            return `
                <tr class="animate__animated animate__fadeInUp">
                    <td class="ps-4">
                        <div class="d-flex align-items-center">
                            <div class="icon-box me-3"><i class="bi bi-shop"></i></div>
                            <div>
                                <span class="fw-bold d-block text-dark">${m.almacen || 'Sucursal'}</span>
                                <span class="text-muted" style="font-size:10px;">MONITOR EN TIEMPO REAL</span>
                            </div>
                        </div>
                    </td>
                    <td class="text-end fw-semibold ${colorClass(m.monto_efectivo)}">${this.f(m.monto_efectivo)}</td>
                    <td class="text-end fw-semibold ${colorClass(m.monto_tarjeta)}">${this.f(m.monto_tarjeta)}</td>
                    <td class="text-end fw-semibold ${colorClass(m.monto_transferencia)}">${this.f(m.monto_transferencia)}</td>
                    <td class="text-end pe-4">
                        <span class="badge ${badgeClass(m.monto)} px-3 py-2" style="border-radius:10px; font-size:13px;">
                            ${this.f(m.monto)}
                        </span>
                    </td>
                </tr>`;
        },
        f: function(n) {
            return '$' + parseFloat(n || 0).toLocaleString('es-MX', { minimumFractionDigits: 2 });
        }
    };
    $(document).ready(() => Tesoreria.init());
    </script>

<?php require_once __DIR__ . '/tesoreriaModal/ajusteModal.php'; ?>
</body>
</html>