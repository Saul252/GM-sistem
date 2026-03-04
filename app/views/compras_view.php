<?php
require_once __DIR__ . '/../../../includes/sidebar.php';
$paginaActual = 'Egresos';
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Compras | Sistema</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="/cfsistem/css/compras.css" rel="stylesheet">
</head>

<body>

    <?php renderSidebar($paginaActual); ?>

    <main class="main-content">

        <!-- =========================
     HEADER
========================= -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold mb-0">Compras y Gastos</h2>
                <p class="text-muted">Gestión de Inventario y Egresos Operativos</p>
            </div>
            <div class="gap-2 d-flex">
                <button class="btn btn-primary px-4 shadow-sm" onclick="abrirModal('compra')">
                    <i class="bi bi-cart-plus me-2"></i> Nueva Compra
                </button>
                <button class="btn btn-warning px-4 shadow-sm" onclick="abrirModal('gasto')">
                    <i class="bi bi-cash-stack me-2"></i> Nuevo Gasto
                </button>
            </div>
        </div>

        <!-- =========================
     FILTROS
========================= -->
        <div class="card shadow-sm mb-4 border-0">
            <div class="card-body bg-light">
                <form method="GET" class="row g-3 align-items-end">

                    <div class="col-md-2">
                        <label class="form-label small fw-bold">DESDE</label>
                        <input type="date" name="desde" class="form-control"
                            value="<?= htmlspecialchars($_GET['desde'] ?? date('Y-m-01')) ?>">
                    </div>

                    <div class="col-md-2">
                        <label class="form-label small fw-bold">HASTA</label>
                        <input type="date" name="hasta" class="form-control"
                            value="<?= htmlspecialchars($_GET['hasta'] ?? date('Y-m-d')) ?>">
                    </div>

                    <div class="col-md-2">
                        <label class="form-label small fw-bold">FOLIO</label>
                        <input type="text" name="folio_busqueda" class="form-control"
                            value="<?= htmlspecialchars($_GET['folio_busqueda'] ?? '') ?>">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label small fw-bold">REGISTRADO POR</label>
                        <select name="usuario_busqueda" class="form-select">
                            <option value="">Todos</option>
                            <?php while($u = $usuarios->fetch_assoc()): ?>
                            <option value="<?= $u['id'] ?>"
                                <?= ($_GET['usuario_busqueda'] ?? '') == $u['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($u['nombre']) ?>
                            </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="col-md-3 d-flex gap-2">
                        <button type="submit" class="btn btn-primary w-100">Filtrar</button>
                        <a href="compras.php" class="btn btn-outline-secondary w-100">Limpiar</a>
                    </div>

                </form>
            </div>
        </div>

        <!-- =========================
     TARJETAS TOTALES
========================= -->
        <div class="row g-3 mb-4">

            <div class="col-md-4">
                <div class="card border-start border-4 border-primary shadow-sm rounded-4">
                    <div class="card-body">
                        <p class="small text-muted fw-bold mb-1">Compras</p>
                        <h4 class="fw-bold">$ <?= number_format($totales['compras'],2) ?></h4>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card border-start border-4 border-warning shadow-sm rounded-4">
                    <div class="card-body">
                        <p class="small text-muted fw-bold mb-1">Gastos</p>
                        <h4 class="fw-bold">$ <?= number_format($totales['gastos'],2) ?></h4>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card border-start border-4 border-danger shadow-sm rounded-4">
                    <div class="card-body">
                        <p class="small text-danger fw-bold mb-1">Total Egresos</p>
                        <h4 class="fw-bold">$ <?= number_format($totales['total'],2) ?></h4>
                    </div>
                </div>
            </div>

        </div>

        <!-- =========================
     TABLA
========================= -->
        <div class="card shadow-sm p-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle">

                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Folio</th>
                            <th>Tipo</th>
                            <th>Entidad</th>
                            <th>Registrado Por</th>
                            <th>Total</th>
                            <th>Estado</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>

                    <tbody>

                        <?php while($e = $egresos->fetch_assoc()): 
    $hayFaltantes = ($e['tipo'] == 'compra' && $e['tiene_faltantes'] == 1);
?>

                        <tr class="<?= $hayFaltantes ? 'table-warning' : '' ?>">

                            <td><?= date('d/m/Y', strtotime($e['fecha'])) ?></td>
                            <td class="fw-bold"><?= htmlspecialchars($e['folio']) ?></td>

                            <td>
                                <span class="badge <?= $e['tipo']=='compra' ? 'bg-primary' : 'bg-warning text-dark' ?>">
                                    <?= strtoupper($e['tipo']) ?>
                                </span>
                            </td>

                            <td><?= htmlspecialchars($e['entidad']) ?></td>
                            <td><small><?= htmlspecialchars($e['usuario_nombre'] ?? 'N/A') ?></small></td>
                            <td class="fw-bold text-primary">$<?= number_format($e['total'],2) ?></td>

                            <td>
                                <?php if($hayFaltantes): ?>
                                <span class="badge bg-danger">INCOMPLETO</span>
                                <?php else: ?>
                                <span class="badge bg-success">COMPLETO</span>
                                <?php endif; ?>
                            </td>

                            <td class="text-end">
                                <div class="btn-group">
                                    <button class="btn btn-sm btn-outline-dark"
                                        onclick="verDetalle('<?= $e['tipo'] ?>', <?= $e['id'] ?>)">
                                        Ver
                                    </button>

                                    <?php if($hayFaltantes): ?>
                                    <button class="btn btn-sm btn-danger" onclick="abrirAjuste(<?= $e['id'] ?>)">
                                        Ajuste
                                    </button>
                                    <?php endif; ?>

                                </div>
                            </td>

                        </tr>

                        <?php endwhile; ?>

                    </tbody>
                </table>
            </div>
        </div>

    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>
<script>
/* =========================================================
   INICIALIZACIÓN GENERAL
========================================================= */

const modalRegistro = new bootstrap.Modal('#modalRegistro');
const modalProd = new bootstrap.Modal('#modalNuevoProducto');
const modalDist = new bootstrap.Modal('#modalDistribucion');
const modalVer = new bootstrap.Modal('#modalVerDetalle');
const modalAjusteForm = new bootstrap.Modal('#modalAjuste');

let filaEnDistribucion = null;

/* =========================================================
   ABRIR MODAL COMPRA / GASTO
========================================================= */

function abrirModal(tipo) {

    document.getElementById('tipo_egreso').value = tipo;

    const header = document.getElementById('modalHeader');
    const lblEntidad = document.getElementById('lblEntidad');

    if (tipo === 'compra') {
        header.className = "modal-header bg-primary text-white";
        lblEntidad.innerText = "PROVEEDOR";
    } else {
        header.className = "modal-header bg-warning text-dark";
        lblEntidad.innerText = "BENEFICIARIO";
    }

    document.getElementById('contenedorItems').innerHTML = '';
    document.getElementById('formEgreso').reset();
    actualizarDiferencia();

    modalRegistro.show();
}

/* =========================================================
   AGREGAR FILA DE CONCEPTO
========================================================= */

function agregarFila() {

    let html = `
    <div class="row g-2 mb-2 fila-item">
        <div class="col-md-5">
            <input type="text" name="descripcion[]" class="form-control" placeholder="Descripción" required>
        </div>
        <div class="col-md-2">
            <input type="number" step="0.01" name="cantidad[]" class="form-control cantidad" placeholder="Cant" required>
        </div>
        <div class="col-md-2">
            <input type="number" step="0.01" name="precio[]" class="form-control precio" placeholder="Precio" required>
        </div>
        <div class="col-md-2">
            <input type="number" step="0.01" name="subtotal[]" class="form-control subtotal" readonly>
        </div>
        <div class="col-md-1">
            <button type="button" class="btn btn-danger btn-sm" onclick="eliminarFila(this)">X</button>
        </div>
    </div>
    `;

    document.getElementById('contenedorItems').insertAdjacentHTML('beforeend', html);
}

/* =========================================================
   ELIMINAR FILA
========================================================= */

function eliminarFila(btn) {
    btn.closest('.fila-item').remove();
    actualizarDiferencia();
}

/* =========================================================
   CALCULAR SUBTOTALES
========================================================= */

document.addEventListener('input', function(e) {

    if (e.target.classList.contains('cantidad') ||
        e.target.classList.contains('precio')) {

        let fila = e.target.closest('.fila-item');

        let cantidad = parseFloat(fila.querySelector('.cantidad').value) || 0;
        let precio = parseFloat(fila.querySelector('.precio').value) || 0;

        let subtotal = cantidad * precio;

        fila.querySelector('.subtotal').value = subtotal.toFixed(2);

        actualizarDiferencia();
    }
});

/* =========================================================
   DIFERENCIA FACTURA
========================================================= */

function actualizarDiferencia() {

    let totalFactura = parseFloat(document.getElementById('total_factura').value) || 0;
    let suma = 0;

    document.querySelectorAll('.subtotal').forEach(input => {
        suma += parseFloat(input.value) || 0;
    });

    let diferencia = totalFactura - suma;

    document.getElementById('txtDiferencia').innerText =
        "$ " + diferencia.toFixed(2);

    let alerta = document.getElementById('alertaMonto');

    if (diferencia !== 0) {
        alerta.classList.remove('d-none');
    } else {
        alerta.classList.add('d-none');
    }
}

/* =========================================================
   VER DETALLE
========================================================= */

function verDetalle(tipo, id) {

    fetch('/cfsistem/app/backend/compras/detalle.php?tipo=' + tipo + '&id=' + id)
        .then(res => res.text())
        .then(html => {
            document.getElementById('contenidoDetalle').innerHTML = html;
            modalVer.show();
        });
}

/* =========================================================
   ABRIR AJUSTE
========================================================= */

function abrirAjuste(idCompra) {

    document.getElementById('ajuste_compra_id').value = idCompra;

    fetch('/cfsistem/app/backend/compras/obtener_faltantes.php?id=' + idCompra)
        .then(res => res.text())
        .then(html => {
            document.getElementById('tablaAjuste').innerHTML = html;
            modalAjusteForm.show();
        });
}

/* =========================================================
   GUARDAR COMPRA / GASTO
========================================================= */

document.getElementById('formEgreso')
    .addEventListener('submit', function(e) {

        e.preventDefault();

        let formData = new FormData(this);

        Swal.fire({
            title: 'Guardando...',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });

        fetch('/cfsistem/app/backend/compras/guardar.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(res => {

                if (res.status === 'success') {

                    Swal.fire({
                        icon: 'success',
                        title: 'Registro guardado'
                    }).then(() => {
                        location.reload();
                    });

                } else {
                    Swal.fire('Error', res.message, 'error');
                }
            })
            .catch(() => {
                Swal.fire('Error', 'Error de conexión', 'error');
            });
    });

/* =========================================================
   GUARDAR AJUSTE
========================================================= */

document.getElementById('formAjuste')
    .addEventListener('submit', function(e) {

        e.preventDefault();

        let formData = new FormData(this);

        fetch('/cfsistem/app/backend/compras/guardar_ajuste.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(res => {
                if (res.status === 'success') {
                    Swal.fire('Correcto', 'Stock actualizado', 'success')
                        .then(() => location.reload());
                }
            });
    });
</script>