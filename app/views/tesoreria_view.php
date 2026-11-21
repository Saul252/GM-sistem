<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tesorería | Cf System</title>
    <link rel="icon" type="image/png" href="/cfsistem/public/assets/logo.png">

    <link rel="shortcut icon" href="/cfsistem/public/assets/logo.ico" type="image/x-icon">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />

    <?php require_once __DIR__ . '/layout/icono.php' ?>
    <?php if (function_exists('cargarEstilos')) {
        cargarEstilos();
    } ?>

    <style>
        :root {
            --apple-bg: #f5f5f7;
            --apple-blue: #007aff;
        }

        body {
            background-color: var(--apple-bg);
            font-family: -apple-system, sans-serif;
        }

        .main-content {
            margin-left: 260px;
            padding: 80px 20px;
            transition: 0.3s;
        }

        .glass-card {
            backdrop-filter: blur(15px);
            border-radius: 22px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        }

        .ios-input {
            border-radius: 12px;
            padding: 10px;
            font-size: 14px;
        }

        .ios-input:focus {
            box-shadow: 0 0 0 3px rgba(0, 122, 255, 0.1);
        }

        .table thead th {
            background: transparent;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            font-weight: 700;
            color: #8e8e93;
            font-size: 11px;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            padding: 15px 10px;
        }

        .btn-ios {
            border-radius: 14px;
            font-weight: 600;
            transition: 0.2s;
        }

        .icon-box {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            background: rgba(0, 122, 255, 0.1);
            color: var(--apple-blue);
        }

        .avatar-sucursal {
            width: 38px;
            height: 38px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(0, 122, 255, 0.1);
            color: var(--apple-blue);
            font-weight: 700;
            border-radius: 10px;
            border: 1px solid rgba(0, 122, 255, 0.2);
        }

        @media (max-width: 992px) {
            .main-content {
                margin-left: 0;
            }
        }
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
                <button type="button" class="btn btn-primary btn-ios px-4 py-2 shadow-sm"
                    onclick="ModalMovimiento.abrir()">
                    <i class="bi bi-plus-lg me-2"></i> Registrar Movimiento
                </button>
            </div>

            <div class="glass-card p-4 mb-4 animate__animated animate__fadeIn">
                <form id="formFiltrosTesoreria" class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="small fw-bold text-body-secondary text-uppercase mb-2 d-block">Fecha de
                            Corte</label>
                        <input type="date" id="filtro_fecha" class="form-control ios-input"
                            value="<?= date('Y-m-d') ?>">
                    </div>
                    <div class="col-md-2">
                        <button type="button" onclick="Tesoreria.listar()" class="btn btn-dark w-100 btn-ios py-2">
                            <i class="bi bi-arrow-clockwise me-1"></i> Actualizar
                        </button>
                    </div>
                </form>
            </div>

            <div class="glass-card position-relative overflow-hidden mb-4 animate__animated animate__fadeInUp">
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

            <div class="row g-4">
                <!-- 🔵 CAJAS FUERTES -->
                <div class="col-lg-6">
                    <div class="glass-card h-100 position-relative overflow-hidden animate__animated animate__fadeInUp"
                        style="border-radius:18px; backdrop-filter: blur(12px);">
                        <div class="p-3 border-bottom bg-opacity-50">
                            <h6 class="mb-0 fw-semibold">🏦 Cajas Fuertes</h6>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-3">Almacén</th>
                                        <th class="text-end">Caja</th>
                                        <th class="text-end pe-3">Saldo</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($saldoCajas as $caja): ?>
                                        <tr>
                                            <td class="ps-3 fw-medium"><?= $caja['almacen'] ?></td>
                                            <td class="text-end"><?= $caja['nombre'] ?></td>
                                            <td class="text-end pe-3 fw-semibold text-success">
                                                $<?= number_format($caja['saldo'], 2) ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- 🟣 BANCOS -->
                <div class="col-lg-6">
                    <div class="glass-card h-100 position-relative overflow-hidden animate__animated animate__fadeInUp"
                        style="border-radius:18px; backdrop-filter: blur(12px);">
                        <div class="p-3 border-bottom bg-opacity-50">
                            <h6 class="mb-0 fw-semibold">🏛️ Cuentas Bancarias</h6>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-3">Almacén</th>
                                        <th class="text-end">Cuenta</th>
                                        <th class="text-end pe-3">Saldo</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($saldosCuentasBancarias as $cb): ?>
                                        <tr>
                                            <td class="ps-3 fw-medium"><?= $cb['almacen'] ?></td>
                                            <td class="text-end"><?= $cb['nombre'] ?></td>
                                            <td class="text-end pe-3 fw-semibold text-primary">
                                                $<?= number_format($cb['saldo'], 2) ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="glass-card position-relative overflow-hidden animate__animated animate__fadeInUp mt-3">
                <div class="col-md-4 mt-3">
                    <label class="small fw-bold text-body-secondary text-uppercase mb-2 d-block">Buscar por
                        Concepto</label>
                    <div class="position-relative">
                        <i
                            class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3 text-body-secondary"></i>
                        <input type="text" id="filtro_concepto" onkeyup="Tesoreria.filtrarLocal()"
                            class="form-control ios-input ps-5" placeholder="Escribe para buscar...">
                    </div>
                </div>
                <div class="p-3 border-bottom">
                    <h6 class="m-0 fw-bold text-body-secondary"><i class="bi bi-list-check me-2"></i>DETALLE DE
                        AFECTACIONES</h6>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th class="ps-4">Fecha del movimiento</th>
                                <th class="ps-4">SUCURSAL AFECTADA</th>
                                <th class="text-end">CONCEPTO</th>
                                <th class="text-end">EFECTIVO</th>
                                <th class="text-end">TARJETA</th>
                                <th class="text-end">TRANSFERENCIA</th>
                                <th class="text-end">SUBTOTAL</th>
                                <!-- 🔧 FIX: faltaba este <th> (había 7 ths y 8 tds) -->
                                <th class="text-end pe-4">ACCIÓN</th>
                            </tr>
                        </thead>
                        <tbody id="tbodyAfectaciones"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.querySelectorAll('input[type="text"], textarea').forEach(elemento => {
            elemento.addEventListener('input', function () {
                this.value = this.value.toUpperCase();
            });
        });
    </script>
    <script>
        const Tesoreria = {
            url: '/cfsistem/app/controllers/tesoreriaController.php',
            datosHistorial: [],

            init: function () {
                this.listar();
            },

            listar: function () {
                const params = {
                    action: 'listar',
                    almacen_id: $('#filtro_almacen_id').val(),
                    fecha: $('#filtro_fecha').val()
                };

                $.getJSON(this.url, params, (res) => {
                    let html = '';
                    let htmlHistorial = '';

                    if (res.status === 'success') {
                        if (res.data) {
                            if (Array.isArray(res.data)) {
                                res.data.forEach(m => html += this.renderFila(m));
                            } else {
                                html = this.renderFila(res.data);
                            }
                        }

                        if (res.movimientosHistorial) {
                            this.datosHistorial = Array.isArray(res.movimientosHistorial)
                                ? res.movimientosHistorial
                                : [res.movimientosHistorial];

                            this.datosHistorial.forEach(mh => {
                                htmlHistorial += this.renderFilaHistorial(mh);
                            });
                        }
                    }

                    $('#tbodyTesoreria').html(html || '<tr><td colspan="5" class="text-center py-5 text-body-secondary">No hay registros de saldos</td></tr>');
                    $('#tbodyAfectaciones').html(htmlHistorial || '<tr><td colspan="8" class="text-center py-5 text-body-secondary">Sin movimientos detallados</td></tr>');

                    $('#filtro_concepto').val('');
                }).fail(function (xhr, status, err) {
                    /* 🔧 FIX: visibilidad si la carga principal falla */
                    console.error('Error al listar tesorería:', status, err, xhr.responseText);
                    Swal.fire('Error', 'No se pudo cargar la información de tesorería.', 'error');
                });
            },

            filtrarLocal: function () {
                const busqueda = $('#filtro_concepto').val().toLowerCase();

                const filtrados = this.datosHistorial.filter(m => {
                    const concepto = (m.concepto || '').toLowerCase();
                    const almacen = (m.almacen || '').toLowerCase();
                    return concepto.includes(busqueda) || almacen.includes(busqueda);
                });

                let html = '';
                filtrados.forEach(m => html += this.renderFilaHistorial(m));
                $('#tbodyAfectaciones').html(html || '<tr><td colspan="8" class="text-center py-5 text-body-secondary">No se encontraron coincidencias</td></tr>');
            },

            renderFila: function (m) {
                const colorClass = (val) => parseFloat(val) < 0 ? 'text-danger' : 'text-secondary';
                const badgeClass = (val) => parseFloat(val) < 0 ? 'bg-danger' : 'bg-primary';
                return `
                <tr class="animate__animated animate__fadeIn">
                    <td class="ps-4">
                        <div class="d-flex align-items-center">
                            <div class="icon-box me-3"><i class="bi bi-shop"></i></div>
                            <div>
                                <span class="fw-bold d-block">${m.almacen || 'Sucursal'}</span>
                                <span class="text-body-secondary" style="font-size:10px;">SALDO ACTUAL</span>
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

            renderFilaHistorial: function (m) {

                /* 🔧 FIX: resolver el id desde varios posibles nombres que devuelva el controlador */
                const idMov = m.id ?? m.movimiento_id ?? m.id_movimiento ?? m.mov_id ?? null;
                const idSeguro = (idMov !== null && !isNaN(parseInt(idMov, 10))) ? parseInt(idMov, 10) : '';

                return `
                <tr class="animate__animated animate__fadeIn">
                    <td class="ps-4">
                        <span class="d-block fw-bold">${m.fecha_movimiento || 'S/F'}</span>
                    </td>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="avatar-sucursal me-3">
                                ${m.almacen ? m.almacen.substring(0, 1).toUpperCase() : 'A'}
                            </div>
                            <span class="fw-bold">${m.almacen || 'Almacén'}</span>
                        </div>
                    </td>
                    <td>
                        <span class="d-block fw-bold text-uppercase small">${m.concepto || 'Sin Concepto'}</span>
                    </td>
                    <td class="text-end fw-medium">${this.f(m.monto_efectivo)}</td>
                    <td class="text-end fw-medium">${this.f(m.monto_tarjeta)}</td>
                    <td class="text-end fw-medium">${this.f(m.monto_transferencia)}</td>
                    <td class="text-end">
                        <span class="fw-bolder fs-6 text-primary">
                            ${this.f(m.monto)}
                        </span>
                    </td>
                    <td class="text-end pe-4">
                        <button type="button"
                            class="btn btn-sm btn-outline-primary rounded-pill"
                            onclick="abrirModalDetalleMovimiento('${idSeguro}')">
                            📄 Ver detalle
                        </button>
                    </td>
                </tr>`;
            },

            f: function (n) {
                return '$' + parseFloat(n || 0).toLocaleString('es-MX', { minimumFractionDigits: 2 });
            }
        };

        $(document).ready(() => {
            Tesoreria.init();

            $('#filtro_concepto').on('keyup', function () {
                Tesoreria.filtrarLocal();
            });
        });
    </script>
    <div class="modal fade" id="modalDetalleMovimiento" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content glass-card p-2">
                <div class="modal-header">
                    <h5 class="fw-bold m-0">
                        Detalle de Movimiento
                        <span id="det_folio_badge" class="badge bg-primary-subtle text-primary ms-1">#—</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body" id="areaImpresionMovimiento">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="text-xs fw-bold text-body-secondary text-uppercase d-block">Concepto /
                                Observación</label>
                            <div class="p-2 rounded-4 bg-light border">
                                <span id="det_concepto" class="fw-medium text-uppercase">—</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2 rounded-4 bg-light border">
                                <small class="text-xs fw-bold text-body-secondary text-uppercase d-block">Método /
                                    Fecha</small>
                                <span id="det_metodo_pago" class="fw-bold text-uppercase d-block">—</span>

                            </div>
                        </div>



                        <div class="col-6">
                            <label class="text-xs fw-bold text-body-secondary text-uppercase d-block">Usuario Registro
                                ID</label>
                            <span id="det_usuario_registro_id" class="fw-semibold">—</span>
                        </div>


                    </div>

                    <div class="mt-4 d-flex gap-2">
                        <button type="button" onclick="imprimirDetalleMovimiento()"
                            class="btn btn-primary w-100 btn-ios py-3 shadow">
                            🖨️ Imprimir Ticket (70 mm)
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let ultimoMovimientoDetalle = null;

        function fmtMonedaDetalle(valor) {
            const num = parseFloat(valor) || 0;
            return '$' + num.toLocaleString('es-MX', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }

        function fmtTextoDetalle(valor, fallback = '—') {
            if (valor === null || valor === undefined || String(valor).trim() === '') return fallback;
            return String(valor);
        }

        function escapeHtmlTicket(str) {
            return String(str)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;');
        }

        function mostrarModalDetalleBS() {
            const el = document.getElementById('modalDetalleMovimiento');
            if (!el) return;
            if (window.bootstrap && bootstrap.Modal) {
                bootstrap.Modal.getOrCreateInstance(el).show();
            } else if (typeof window.$ !== 'undefined' && typeof $(el).modal === 'function') {
                $(el).modal('show');
            }
        }

        function abrirModalDetalleMovimiento(movimientoId) {
            const id = parseInt(movimientoId, 10) || 0;
            if (id <= 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Sin ID válido',
                    html: 'El movimiento no tiene un <b>ID</b> válido para consultar el detalle.'
                });
                return;
            }

            $.getJSON('/cfsistem/app/controllers/tesoreriaController.php', {
                action: 'obtener_movimiento_capital',
                id: id
            }, function (res) {
                if (res && res.status === 'success' && res.data) {
                    const d = res.data;
                    ultimoMovimientoDetalle = d;

                    const idTexto = '#' + fmtTextoDetalle(d.id, id);
                    $('#det_folio_badge').text(idTexto);
                    $('#det_metodo_pago').text(fmtTextoDetalle(d.metodo_pago));


                    $('#det_usuario_registro_id').text(d.usuario_que_registro);
                    $('#det_concepto').text(fmtTextoDetalle(d.concepto, 'Sin concepto'));

                    mostrarModalDetalleBS();
                } else {
                    Swal.fire('Aviso', (res && res.message) || 'No se pudo cargar el detalle.', 'warning');
                }
            }).fail(function () {
                Swal.fire('Error', 'No se pudo consultar el detalle del movimiento.', 'error');
            });
        }

        function imprimirDetalleMovimiento() {
            const d = ultimoMovimientoDetalle || {};
            const folio = escapeHtmlTicket(fmtTextoDetalle(d.id, $('#det_folio_badge').text().replace('#', '') || '—'));
            const metodo = escapeHtmlTicket(fmtTextoDetalle(d.metodo_pago, $('#det_metodo_pago').text()).toUpperCase());

            const usuarioId = escapeHtmlTicket(fmtTextoDetalle(d.usuario_que_registro, $('#det_usuario_registro_id').text()));
            const concepto = escapeHtmlTicket(fmtTextoDetalle(d.concepto, $('#det_concepto').text()).toUpperCase());
            const fechaImp = escapeHtmlTicket(new Date().toLocaleString('es-MX'));

            const htmlTicket = `<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Ticket Movimiento #${folio}</title>
<style>
  @page {
    size: 70mm auto;
    margin: 2mm;
  }
  * {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
  }
  body {
    font-family: 'Courier New', Courier, monospace, Arial, sans-serif;
    font-size: 11px;
    line-height: 1.35;
    color: #000;
    background: #f4f4f4;
    padding: 12px;
  }
  .ticket {
    width: 70mm;
    max-width: 100%;
    margin: 0 auto;
    padding: 4mm;
    background: #fff;
    border: 1px dashed #999;
    page-break-inside: avoid;
    break-inside: avoid;
  }
  .center { text-align: center; }
  .title {
    font-size: 13px;
    font-weight: 800;
    letter-spacing: 0.3px;
  }
  .sub {
    font-size: 9px;
    margin-top: 2px;
  }
  .divider {
    border-top: 1px dashed #000;
    margin: 6px 0;
  }
  .row-line {
    display: flex;
    justify-content: space-between;
    gap: 6px;
    margin-bottom: 4px;
    word-break: break-word;
  }
  .label {
    font-weight: 700;
  }
  .monto-box {
    border: 1px solid #000;
    padding: 5px;
    margin: 6px 0;
    text-align: center;
  }
  .monto-label {
    font-size: 9px;
    text-transform: uppercase;
  }
  .monto-val {
    font-size: 15px;
    font-weight: 800;
    margin-top: 2px;
  }
  .concepto-box {
    margin-top: 4px;
    word-wrap: break-word;
    overflow-wrap: break-word;
  }
  .firma-area {
    margin-top: 16mm;
    text-align: center;
    page-break-inside: avoid;
    break-inside: avoid;
  }
  .firma-linea {
    border-top: 1px solid #000;
    width: 85%;
    margin: 0 auto 4px auto;
  }
  .firma-texto {
    font-size: 9px;
    font-weight: 700;
    letter-spacing: 0.5px;
  }

  @media print {
    body {
      background: #fff;
      padding: 0;
      width: 66mm;
      font-size: 10px;
      line-height: 1.3;
    }
    .ticket {
      width: 100%;
      margin: 0;
      padding: 1mm 0;
      border: none;
    }
  }
</style>
</head>
<body>
  <div class="ticket">
    <div class="center">
      <div class="title">COMPROBANTE DE MOVIMIENTO</div>
      <div class="sub">TESORERÍA · FOLIO #${folio}</div>
      <div class="sub">Impreso: ${fechaImp}</div>
    </div>

    <div class="divider"></div>

    <div class="concepto-box">
      <div class="label">Concepto:</div>
      <div>${concepto}</div>
    </div>

    <div class="row-line"><span class="label">Método:</span><span>${metodo}</span></div>
    
    <div class="row-line"><span class="label">Usuario ID:</span><span>${usuarioId}</span></div>

    <div class="divider"></div>

    

    <div class="firma-area">
      <div class="firma-linea"></div>
      <div class="firma-texto">FIRMA DE RECIBIDO</div>
    </div>
  </div>

  <script>
    window.addEventListener('load', function () {
      setTimeout(function () {
        window.focus();
        window.print();
      }, 150);
    });
  <\/script>
</body>
</html>`;

            const win = window.open('', '_blank');
            if (!win) {
                Swal.fire('Bloqueador de ventanas', 'Permite las ventanas emergentes para imprimir el ticket.', 'warning');
                return;
            }
            win.document.open();
            win.document.write(htmlTicket);
            win.document.close();
        }
    </script>
    <?php require_once __DIR__ . '/tesoreriaModal/ajusteModal.php'; ?>
</body>

</html>