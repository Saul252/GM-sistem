<?php
$almacen_usuario = intval($_SESSION['almacen_id'] ?? 0);
$paginaActual = $paginaActual ?? 'prestamos';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Préstamos Trabajadores | cfsistem</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

    <?php require_once __DIR__ . '/layout/icono.php' ?>
    <?php if (function_exists('cargarEstilos')) { cargarEstilos(); } ?>
    <style>
        :root { --accent: #007aff; --bg: #f5f5f7; }
        body { background: var(--bg); font-family: -apple-system, sans-serif; }
        .main-content { margin-left: 260px; padding: 40px; }
        .card-ui { border: none; border-radius: 18px; background: rgba(255,255,255,0.9); backdrop-filter: blur(10px); box-shadow: 0 8px 25px rgba(0,0,0,0.05); }
        .badge-estado { padding: 6px 12px; border-radius: 20px; font-size: 11px; font-weight: 600; }
        .estado-activo { background:#fff3e0; color:#ef6c00; }
        .estado-liquidado { background:#e8f5e9; color:#2e7d32; }
        .progress { height: 6px; border-radius: 10px; }
        .d-none { display: none !important; }
    </style>
</head>

<body>

<?php renderizarLayout($paginaActual); ?>

<main class="main-content">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold m-0">Préstamos a Trabajadores</h2>
            <small class="text-muted" id="status-almacen">Mostrando datos de: <?= $almacen_usuario == 0 ? 'Todas las sucursales' : 'Sucursal Actual' ?></small>
        </div>

        <button class="btn btn-primary rounded-pill px-4" onclick="nuevoPrestamo()">
            <i class="bi bi-cash-stack me-2"></i> Nuevo Préstamo
        </button>
    </div>

    <div class="card card-ui p-4">
        <div class="row mb-4 g-3">
            <div class="col-md-5">
                <div class="input-group bg-light rounded-3 p-1">
                    <span class="input-group-text bg-transparent border-0"><i class="bi bi-search"></i></span>
                    <input type="text" id="busqueda" class="form-control border-0 bg-transparent" placeholder="Buscar trabajador...">
                </div>
            </div>

            <?php if ($almacen_usuario == 0): ?>
            <div class="col-md-4">
                <select id="filtroSucursal" class="form-select border-0 bg-light rounded-3">
                    <option value="0">🌐 Todas las sucursales</option>
                    <?php foreach ($almacenes as $a): ?>
                        <option value="<?= $a['id'] ?>"><?= $a['nombre'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php endif; ?>
        </div>

        <div class="table-responsive">
            <table id="tablaPrestamos" class="table align-middle w-100">
                <thead>
                    <tr>
                        <th>Almacen</th>
                        <th>Trabajador</th>
                        <th>Descripción</th>
                        <th>Total</th>
                        <th>Abonado</th>
                        <th>Saldo</th>
                        <th>Progreso</th>
                        <th>Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody id="bodyPrestamos">
                    <?php foreach($prestamos as $p): 
                        $abonado = $p['total_abonado'] ?? 0;
                        $saldo_p = $p['saldo_pendiente'] ?? ($p['monto_total'] - $abonado);
                        $porcentaje = $p['monto_total'] > 0 ? ($abonado / $p['monto_total']) * 100 : 0;
                    ?>
                    <tr data-almacen="<?= $p['almacen_id'] ?>">
                        <td><?= $p['almacen_id'] ?></td>
                        <td class="fw-semibold"><?= $p['trabajador'] ?></td>
                        <td class="small text-muted"><?= $p['descripcion'] ?></td>
                        <td class="fw-bold">$<?= number_format($p['monto_total'],2) ?></td>
                        <td class="text-success fw-bold">$<?= number_format($abonado,2) ?></td>
                        <td class="<?= $saldo_p > 0 ? 'text-danger fw-bold' : 'text-success fw-bold' ?>">$<?= number_format($saldo_p,2) ?></td>
                        <td>
                            <div class="progress"><div class="progress-bar" style="width: <?= $porcentaje ?>%"></div></div>
                        </td>
                        <td>
                            <span class="badge-estado <?= $saldo_p > 0 ? 'estado-activo' : 'estado-liquidado' ?>">
                                <?= $saldo_p > 0 ? 'PENDIENTE' : 'LIQUIDADO' ?>
                            </span>
                        </td>
                        <td class="text-end">
                            <button class="btn btn-sm btn-light" onclick="verPrestamo(<?= $p['id'] ?>)"><i class="bi bi-eye"></i></button>
                            <button class="btn btn-sm btn-success" onclick="modalAbonar(<?= $p['id'] ?>, '<?= addslashes($p['trabajador']) ?>', <?= $saldo_p ?>,<?= $p['almacen_id']?>)"><i class="bi bi-cash"></i></button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="modal fade" id="modalPrestamo" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0" style="border-radius:20px;">
                <form id="formPrestamo">
                    <div class="modal-header bg-dark text-white">
                        <h5 class="modal-title">Registrar Préstamo</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Almacén Origen</label>
                                <select name="almacen_id" id="modal_almacen_id" class="form-select" required>
                                    <option value="">-- Seleccionar --</option>
                                    <?php foreach($almacenes as $a): ?>
                                        <option value="<?= $a['id'] ?>" <?= ($almacen_usuario == $a['id']) ? 'selected' : '' ?>><?= $a['nombre'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Trabajador</label>
                                <select name="trabajador_id" id="modal_trabajador_id" class="form-select" required>
                                    <option value="0">Seleccione un trabajador</option>
                                    <?php foreach($trabajadores as $t): ?>
                                        <option value="<?= $t['id'] ?>"> <?= $t['nombre'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Monto a Prestar</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" step="0.01" name="monto_total" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-6">
                                <label class="text-xs fw-bold text-muted text-uppercase">Método</label>
                                <select name="metodo_pago" class="form-select" required>
                                    <option value="efectivo">💵 Efectivo</option>
                                    <option value="tarjeta">💳 Tarjeta</option>
                                    <option value="transferencia">🏛️ Transferencia</option>
                                </select>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Motivo / Descripción</label>
                                <textarea name="descripcion" class="form-control" rows="2" placeholder="Ej. Adelanto de quincena"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-primary px-4">Confirmar Préstamo</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalMovimientoDinero" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0" style="border-radius:20px;">
                <form id="formMovimientoDinero">
                    <div class="modal-header bg-dark text-white">
                        <h5 class="modal-title">
                            <i class="bi bi-arrow-left-right me-2"></i>
                            Movimiento de Dinero (Abono)
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Movimiento</label>
                                <input type="hidden" name="prestamo_id" id="mov_prestamo_id">
                                <div class="form-control bg-light">
                                    ID: <span id="mov_prestamo_id_text"></span>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Saldo</label>
                                <div class="form-control bg-light">
                                    Deuda: <span id="mov_saldo_text"></span>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Almacén</label>
                                <select name="almacen_id" id="mov_almacen_id" class="form-select" required>
                                    <option value="">-- Seleccionar --</option>
                                    <?php foreach($almacenes as $a): ?>
                                        <option value="<?= $a['id'] ?>"><?= $a['nombre'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Monto del Abono</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" step="0.01" name="monto_abono" class="form-control" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Destino del Dinero</label>
                                <select name="tipo_destino" id="tipo_destino" class="form-select" required>
                                    <option value="">-- Seleccionar --</option>
                                    <option value="caja_fuerte">Caja Fuerte</option>
                                    <option value="banco">Banco</option>
                                    <option value="saldo_inicial">Caja del Día (Saldo Inicial)</option>
                                </select>
                            </div>

                            <div class="col-md-6 d-none" id="wrap_caja">
                                <label class="form-label">Seleccionar Caja Fuerte</label>
                                <select name="caja_fuerte_id" id="select_caja_fuerte" class="form-select">
                                    <option value="">-- Seleccionar Caja --</option>
                                    <?php foreach($cajasFuertes as $c): ?>
                                        <option value="<?= $c['id'] ?>"><?= $c['nombre'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-6 d-none" id="wrap_banco">
                                <label class="form-label">Seleccionar Banco</label>
                                <select name="banco_id" id="select_banco" class="form-select">
                                    <option value="">-- Seleccionar Banco --</option>
                                    <?php foreach($bancos as $b): ?>
                                        <option value="<?= $b['id'] ?>"><?= $b['nombre'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Observaciones</label>
                                <textarea name="observaciones" class="form-control" rows="2"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-primary px-4">Confirmar Abono</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main> 

<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
let tabla;
const CONTROLLER = '/cfsistem/app/controllers/prestamosController.php';

$(document).ready(function() {

    // =========================
    // DATATABLE
    // =========================
    tabla = $('#tablaPrestamos').DataTable({
        pageLength: 15,
        dom: 'rtp',
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
        }
    });

    // =========================
    // BUSCADOR
    // =========================
    $('#busqueda').on('keyup', function() {
        tabla.search(this.value).draw();
    });

    // =========================
    // FILTRO SUCURSAL
    // =========================
    $('#filtroSucursal').on('change', function() {
        const id = $(this).val();
        cargarDatosAlmacen(id);
    });

    // =========================
    // CAMBIO ALMACÉN MODAL
    // =========================
    $('#modal_almacen_id').on('change', function() {
        cargarDatosAlmacen(this.value, true);
    });

    // =====================================================
    // 🔥 LÓGICA DE DESTINO (Caja Fuerte / Banco)
    // =====================================================
    $(document).on('change', '#tipo_destino', function() {
        const destino = $(this).val();
        $('#wrap_caja, #wrap_banco').addClass('d-none');
        $('#select_caja_fuerte, #select_banco').val('').removeAttr('required');

        if (destino === 'caja_fuerte') {
            $('#wrap_caja').removeClass('d-none');
            $('#select_caja_fuerte').attr('required', true);
        } else if (destino === 'banco') {
            $('#wrap_banco').removeClass('d-none');
            $('#select_banco').attr('required', true);
        }
    });

    // =========================
    // FORM PRESTAMO (NUEVO)
    // =========================
    $('#formPrestamo').on('submit', async function(e) {
        e.preventDefault();
        const formData = new FormData(this);

        try {
            const resp = await fetch(`${CONTROLLER}?action=crear`, {
                method: 'POST',
                body: formData
            });

            const res = await resp.json();
            if (res.success) {
                Swal.fire('Éxito', res.message, 'success').then(() => location.reload());
            } else {
                Swal.fire('Error', res.message || 'Error desconocido', 'error');
            }
        } catch (err) {
            console.error(err);
            Swal.fire('Error', 'Error en la petición', 'error');
        }
    });

    // =========================
    // FORM ABONO
    // =========================
    $('#formMovimientoDinero').on('submit', async function(e) {
        e.preventDefault();
        const formData = new FormData(this);

        try {
            const resp = await fetch(`${CONTROLLER}?action=abonar`, {
                method: 'POST',
                body: formData
            });

            const res = await resp.json();
            if (res.success) {
                Swal.fire('Éxito', res.message, 'success').then(() => location.reload());
            } else {
                Swal.fire('Error', res.message || 'Error', 'error');
            }
        } catch (err) {
            console.error(err);
            Swal.fire('Error', 'Error en la petición', 'error');
        }
    });
});

// =====================================================
// FUNCIONES DE CARGA Y MODALES
// =====================================================

async function cargarDatosAlmacen(almacenId, esParaModal = false) {
    try {
        const resp = await fetch(`${CONTROLLER}?action=ajax&almacen_id=${almacenId}`);
        const data = await resp.json();

        if (data.status === 'success') {
            if (esParaModal) {
                let htmlT = '<option value="0">Seleccione un trabajador</option>';
                data.trabajadores.forEach(t => {
                    htmlT += `<option value="${t.id}">${t.nombre}</option>`;
                });
                $('#modal_trabajador_id').html(htmlT);
            } else {
                // 🔥 REPOBLAR TABLA SIN ROMPER DATATABLES
                tabla.clear();
                data.prestamos.forEach(p => {
                    const abonado = parseFloat(p.total_abonado || 0);
                    const montoTotal = parseFloat(p.monto_total || 0);
                    const saldoP = parseFloat(p.saldo_pendiente || (montoTotal - abonado));
                    const porcentaje = montoTotal > 0 ? (abonado / montoTotal) * 100 : 0;

                    const rowNode = tabla.row.add([
                        p.almacen_id,
                        `<span class="fw-semibold">${p.trabajador}</span>`,
                        `<span class="small text-muted">${p.descripcion || ''}</span>`,
                        `$${montoTotal.toFixed(2)}`,
                        `<span class="text-success fw-bold">$${abonado.toFixed(2)}</span>`,
                        `<span class="${saldoP > 0 ? 'text-danger' : 'text-success'} fw-bold">$${saldoP.toFixed(2)}</span>`,
                        `<div class="progress"><div class="progress-bar" style="width: ${porcentaje}%"></div></div>`,
                        `<span class="badge-estado ${saldoP > 0 ? 'estado-activo' : 'estado-liquidado'}">${saldoP > 0 ? 'PENDIENTE' : 'LIQUIDADO'}</span>`,
                        `<div class="text-end">
                            <button class="btn btn-sm btn-light" onclick="verPrestamo(${p.id})"><i class="bi bi-eye"></i></button>
                            <button class="btn btn-sm btn-success" onclick="modalAbonar(${p.id}, '${p.trabajador.replace(/'/g, "\\'")}', ${saldoP}, ${p.almacen_id})"><i class="bi bi-cash"></i></button>
                        </div>`
                    ]).node();
                    $(rowNode).attr('data-almacen', p.almacen_id);
                });
                tabla.draw();
                
                // Actualizar info visual
                $('#status-almacen').text(`Mostrando datos de: ${almacenId == 0 ? 'Todas las sucursales' : 'Sucursal seleccionada'}`);
            }
        }
    } catch (e) {
        console.error("Error cargando datos:", e);
    }
}

function nuevoPrestamo() {
    $('#formPrestamo')[0].reset();
    const modal = new bootstrap.Modal(document.getElementById('modalPrestamo'));
    modal.show();
    if ($('#modal_almacen_id').val()) {
        $('#modal_almacen_id').trigger('change');
    }
}

function modalAbonar(id, nombre, saldo, almacen_id) {
    const form = $('#formMovimientoDinero')[0];
    if (form) form.reset();

    $('#wrap_caja, #wrap_banco').addClass('d-none');
    $('#mov_prestamo_id').val(id);
    $('#mov_prestamo_id_text').text(id);
    $('#mov_saldo_text').text(saldo.toFixed(2));
    $('#mov_almacen_id').val(almacen_id);

    const modalEl = document.getElementById('modalMovimientoDinero');
    let modal = bootstrap.Modal.getInstance(modalEl);
    if (!modal) modal = new bootstrap.Modal(modalEl);
    modal.show();
}

function verPrestamo(id) {
    window.location.href = `${CONTROLLER}?action=detalle&id=${id}`;
}
</script>

</body>
</html>