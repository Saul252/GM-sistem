<?php
/**
 * vehiculos_view.php
 * Optimizado: filtros, seguridad, docs, estilos.
 */
$estadosUnidad = [
    'disponible'      => ['label' => 'Disponible',        'class' => 'st-disponible', 'dot' => '#28a745'],
    'en_ruta'         => ['label' => 'En Ruta',           'class' => 'st-ruta',       'dot' => '#007aff'],
    'mantenimiento'   => ['label' => 'Taller',            'class' => 'st-taller',     'dot' => '#ff9500'],
    'fuera_servicio'  => ['label' => 'Fuera de Servicio', 'class' => 'st-fuera',      'dot' => '#ff3b30'],
];

// Normalizamos sesión una sola vez
$almacenSesion   = (int)($_SESSION['almacen_id'] ?? 0);
$esAdminAlmacen  = $almacenSesion === 0;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transporte | cfsistem</title>
    <link rel="icon" type="image/png" href="/cfsistem/public/assets/logo.png">
    <link rel="shortcut icon" href="/cfsistem/public/assets/logo.ico" type="image/x-icon">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

    <?php require_once __DIR__ . '/layout/icono.php'; ?>
    <?php if (function_exists('cargarEstilos')) cargarEstilos(); ?>

    <style>
        :root {
            --sidebar-width: 260px;
            --navbar-height: 65px;
            --accent-color: #007aff;
        }
        body { min-height: 100vh; font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; }

        .main-content {
            margin-left: var(--sidebar-width);
            padding: 40px;
            padding-top: calc(var(--navbar-height) + 20px);
            transition: all .3s ease;
        }
        .card-premium {
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,.3);
            border-radius: 24px;
            box-shadow: 0 15px 35px rgba(0,0,0,.05);
            padding: 30px;
        }
        .ios-micro-card {
            background: #fff; border-radius: 18px; padding: 12px 20px; border: none;
            box-shadow: 0 4px 15px rgba(0,0,0,.03);
            display: flex; align-items: center; gap: 15px;
            transition: transform .2s;
        }
        .ios-micro-card:hover { transform: translateY(-3px); }
        .ios-icon-circle {
            width: 40px; height: 40px; background: #eef6ff; color: var(--accent-color);
            border-radius: 12px; display: flex; align-items: center; justify-content: center;
            font-size: 1.2rem;
        }
        .ios-m-label { font-size: .65rem; letter-spacing: .5px; color: #8e8e93; font-weight: 700; }

        .badge-premium {
            padding: 8px 14px; border-radius: 12px; font-weight: 600;
            font-size: .72rem; letter-spacing: .5px;
            display: inline-flex; align-items: center; gap: 6px;
            border: 1px solid rgba(0,0,0,.05);
        }
        .st-disponible { background: #e6ffed; color: #1e7e34; }
        .st-ruta       { background: #e8f4ff; color: #007aff; }
        .st-taller     { background: #fff8e6; color: #d97706; }
        .st-fuera      { background: #fff0f0; color: #d11a2a; }

        .table thead th {
            background: transparent; color: #8e8e93; font-weight: 700;
            font-size: .75rem; text-transform: uppercase; border: none; padding-bottom: 20px;
        }
        .table tbody tr { border-bottom: 1px solid #f2f2f7; transition: background .2s; }
        .table tbody tr:hover { background: rgba(0,122,255,.02); }

        .btn-gradient {
            background: linear-gradient(135deg, #007aff 0%, #0056b3 100%);
            color: #fff; border: none; border-radius: 12px;
            padding: 10px 25px; font-weight: 600;
            box-shadow: 0 8px 20px rgba(0,122,255,.25);
        }
        .btn-gradient:hover { color: #fff; opacity: .9; transform: translateY(-1px); }

        /* Contenedor dropdown documentos */
        .doc-dropdown { min-width: 340px; max-height: 360px; overflow-y: auto; }
        .doc-dropdown .dropdown-item { white-space: normal; }

        @media (max-width: 768px) {
            .main-content { margin-left: 0; padding: 20px; padding-top: 100px; }
        }
    </style>
</head>

<body>
<?php if (function_exists('renderizarLayout')) renderizarLayout($paginaActual); ?>

<main class="main-content">
    <div class="d-flex justify-content-between align-items-end flex-wrap mb-5" style="gap: 20px;">
        <div>
            <h1 class="fw-bold m-0" style="letter-spacing: -1px;">Flota Vehicular</h1>
            <span class="text-body-secondary fw-medium">Panel de control logístico de cfsistem</span>
        </div>

        <div class="d-flex align-items-center gap-3">
            <div class="ios-micro-card">
                <div class="ios-icon-circle"><i class="bi bi-truck"></i></div>
                <div>
                    <p class="ios-m-label mb-0">TOTAL</p>
                    <div class="fs-5 fw-bold"><?= count($vehiculos) ?> Unid.</div>
                </div>
            </div>
            <button class="btn btn-gradient btn-lg px-4" onclick="nuevoVehiculo()">
                <i class="bi bi-plus-lg me-2"></i>Nueva Unidad
            </button>
        </div>
    </div>

    <div class="card card-premium">
        <div class="row mb-4 g-3 align-items-center">
            <div class="col-md-5">
                <label class="form-label">Buscar</label>
                <div class="input-group search-box shadow-sm rounded-4 overflow-hidden">
                    <span class="input-group-text bg-white"><i class="bi bi-search text-body-secondary"></i></span>
                    <input type="text" id="busquedaVehiculo" class="form-control p-3"
                           placeholder="Buscar por placa, VIN o nombre...">
                </div>
            </div>
            <div class="col-md-3">
                <label class="form-label">Estado</label>
                <select id="filtroEstado" class="form-select shadow-sm p-3 rounded-4 fw-medium">
                    <option value="">Todos los estados</option>
                    <?php foreach ($estadosUnidad as $key => $val): ?>
                        <option value="<?= $key ?>"><?= $val['label'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Almacén</label>
                <select id="almacen_id" class="form-select shadow-sm p-3 rounded-4 fw-medium"
                        <?= $esAdminAlmacen ? '' : 'disabled' ?>>
                    <?php if ($esAdminAlmacen): ?>
                        <option value="">Seleccionar ubicación...</option>
                    <?php endif; ?>
                    <?php foreach ($listaAlmacenes as $a): ?>
                        <option value="<?= (int)$a['id'] ?>"
                            <?= ((int)$a['id'] === $almacenSesion) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($a['nombre']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if (!$esAdminAlmacen): ?>
                    <input type="hidden" id="almacen_id_hidden" value="<?= $almacenSesion ?>">
                <?php endif; ?>
            </div>
            <div class="col text-end">
                <label class="form-label d-block">&nbsp;</label>
                <button class="btn btn-light rounded-4 p-3 border shadow-sm fw-bold" onclick="limpiarFiltros()">
                    <i class="bi bi-arrow-counterclockwise me-2"></i>Reset
                </button>
            </div>
        </div>

        <div class="table-responsive">
            <table id="tablaVehiculos" class="table align-middle">
                <thead>
                <tr>
                    <th>Unidad</th>
                    <th>Identificación</th>
                    <th>Capacidad</th>
                    <th>Almacén</th>
                    <th>Tipo</th>
                    <th>Documentos</th>
                    <th>Estado</th>
                    <th class="text-end">Opciones</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($vehiculos as $v):
                    $docs       = !empty($v['documentos_url']) ? explode(';;;', $v['documentos_url']) : [];
                    $docsCount  = 0;
                    $docsItems  = [];
                    foreach ($docs as $doc) {
                        $partes    = explode('|||', $doc);
                        $nombreDoc = $partes[0] ?? '';
                        $dirDoc    = $partes[1] ?? '';
                        $idDoc     = (int)($partes[2] ?? 0);
                        if ($dirDoc === '') continue;
                        $docsItems[] = ['nombre' => $nombreDoc, 'dir' => $dirDoc, 'id' => $idDoc];
                        $docsCount++;
                    }
                ?>
                    <tr data-estado="<?= htmlspecialchars($v['estado_unidad']) ?>"
                        data-almacen="<?= (int)$v['almacen_id'] ?>">
                        <td class="py-4">
                            <div class="d-flex align-items-center">
                                <div class="p-3 rounded-4 shadow-sm me-3 bg-white">
                                    <i class="bi bi-truck-front text-primary fs-4"></i>
                                </div>
                                <div>
                                    <div class="fw-bold fs-6"><?= htmlspecialchars($v['nombre']) ?></div>
                                    <span class="text-body-secondary small">
                                        Mod. <?= htmlspecialchars($v['modelo_año'] ?: 'N/A') ?>
                                    </span>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="border px-3 py-2 rounded-pill shadow-sm mb-1">
                                <i class="bi bi-card-text me-2 text-primary"></i>
                                <strong><?= htmlspecialchars($v['placas']) ?></strong>
                            </div>
                            <div class="text-body-secondary small ps-2">
                                VIN: <?= htmlspecialchars($v['serie_vin'] ?: '---') ?>
                            </div>
                        </td>
                        <td>
                            <div class="fw-bold">
                                <span class="fs-5"><?= number_format((float)$v['capacidad_carga_kg'], 0) ?></span>
                                <small class="text-body-secondary fw-normal">kg</small>
                            </div>
                        </td>
                        <td>
                            <span class="small text-body-secondary">
                                <i class="bi bi-geo-alt"></i> <?= htmlspecialchars($v['nombre_almacen']) ?>
                            </span>
                        </td>
                        <td><span class="small text-body-secondary"><?= htmlspecialchars($v['tipo']) ?></span></td>

                        <td class="text-center">
                            <div class="d-flex justify-content-center align-items-center gap-1">
                            <?php if ($docsCount > 0): ?>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-light border position-relative" type="button"
                                            data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="bi bi-folder2-open text-success"></i>
                                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-primary">
                                            <?= $docsCount ?>
                                        </span>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow doc-dropdown">
                                        <li class="d-flex align-items-center justify-content-between px-3 py-2 border-bottom">
                                            <h6 class="m-0 small fw-bold"><i class="bi bi-files me-1"></i>Documentos</h6>
                                            <button class="btn btn-sm btn-outline-primary rounded-pill"
                                                    onclick="subirDocumentoCompra(<?= (int)$v['id'] ?>)">
                                                Agregar <i class="bi bi-upload"></i>
                                            </button>
                                        </li>
                                        <?php foreach ($docsItems as $d): ?>
                                            <li>
                                                <div class="dropdown-item d-flex justify-content-between align-items-center py-2">
                                                    <a href="/cfsistem/<?= htmlspecialchars(ltrim($d['dir'], '/')) ?>"
                                                       target="_blank" rel="noopener"
                                                       class="text-decoration-none flex-grow-1">
                                                        <i class="bi bi-file-earmark-pdf text-danger me-2"></i>
                                                        <span class="small"><?= htmlspecialchars($d['nombre']) ?></span>
                                                    </a>
                                                    <button class="btn btn-sm btn-outline-danger"
                                                            title="Eliminar documento"
                                                            onclick="eliminarDocumento(<?= $d['id'] ?>)">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </div>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php else: ?>
                                <button class="btn btn-sm btn-outline-primary rounded-pill"
                                        onclick="subirDocumentoCompra(<?= (int)$v['id'] ?>)">
                                    <i class="bi bi-upload"></i>
                                </button>
                            <?php endif; ?>
                            </div>
                        </td>

                        <td>
                            <span class="badge-premium <?= $estadosUnidad[$v['estado_unidad']]['class'] ?>">
                                <span style="height:8px;width:8px;background:<?= $estadosUnidad[$v['estado_unidad']]['dot'] ?>;border-radius:50%"></span>
                                <?= strtoupper($estadosUnidad[$v['estado_unidad']]['label']) ?>
                            </span>
                        </td>
                        <td class="text-end">
                            <button class="btn btn-white btn-sm rounded-3 shadow-sm border me-2"
                                    onclick='editarVehiculo(<?= json_encode($v, JSON_HEX_APOS | JSON_HEX_QUOT) ?>)'>
                                <i class="bi bi-pencil-fill text-primary"></i>
                            </button>
                            <button class="btn btn-white btn-sm rounded-3 shadow-sm border"
                                    onclick="eliminarVehiculo(<?= (int)$v['id'] ?>)">
                                <i class="bi bi-trash-fill text-danger"></i>
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<!-- Modal -->
<div class="modal fade" id="modalVehiculo" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow" style="border-radius:30px;overflow:hidden;">
            <form id="formVehiculo">
                <div class="modal-header p-4 pb-0">
                    <h4 class="fw-bold" id="modalTitulo">Detalles Técnicos</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4 row g-4">
                    <input type="hidden" name="id" id="v_id" value="0">
                    <input type="hidden" name="action" value="guardar">

                    <div class="col-12">
                        <label class="form-label fw-bold text-secondary">Descripción de Unidad</label>
                        <input type="text" name="nombre" id="v_nombre"
                               class="form-control form-control-lg rounded-4" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-secondary">Placas</label>
                        <input type="text" name="placas" id="v_placas" class="form-control rounded-4 text-uppercase" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-bold text-secondary">Unidad Económica</label>
                        <input type="text" name="tipo" id="v_tipo" class="form-control rounded-4 text-uppercase">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-secondary">Capacidad (kg)</label>
                        <input type="number" step="0.01" name="capacidad_carga_kg" id="v_capacidad" class="form-control rounded-4">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-secondary">Estado</label>
                        <select name="estado_unidad" id="v_estado" class="form-select rounded-4 fw-medium">
                            <?php foreach ($estadosUnidad as $key => $val): ?>
                                <option value="<?= $key ?>"><?= $val['label'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-secondary">Año</label>
                        <input type="number" name="modelo_año" id="v_modelo" class="form-control rounded-4">
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-bold text-secondary">Almacén Asignado</label>
                        <?php if ($esAdminAlmacen): ?>
                            <select name="almacen_id" id="v_almacen_id" class="form-select rounded-4 fw-medium" required>
                                <option value="">Seleccionar Almacén...</option>
                                <?php foreach ($listaAlmacenes as $alm): ?>
                                    <option value="<?= (int)$alm['id'] ?>"><?= htmlspecialchars($alm['nombre']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        <?php else: ?>
                            <input type="text" class="form-control rounded-4" value="Sucursal Actual" readonly>
                            <input type="hidden" name="almacen_id" id="v_almacen_id" value="<?= $almacenSesion ?>">
                        <?php endif; ?>
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-bold text-secondary">VIN / Serie</label>
                        <input type="text" name="serie_vin" id="v_vin" class="form-control rounded-4 text-uppercase">
                    </div>
                </div>
                <div class="modal-footer p-4">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-gradient px-5">Confirmar y Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
const VEHICULOS_ENDPOINT = '/cfsistem/app/controllers/vehiculosController.php';
const ALMACEN_FIJO = <?= $esAdminAlmacen ? 'null' : (int)$almacenSesion ?>;

/* ---------- Utilidades ---------- */
async function postForm(url, formData) {
    const resp = await fetch(url, { method: 'POST', body: formData });
    const text = await resp.text();
    let data;
    try { data = JSON.parse(text); }
    catch { throw new Error('Respuesta inválida del servidor'); }
    return data;
}

function getAlmacenFiltro() {
    if (ALMACEN_FIJO !== null) return String(ALMACEN_FIJO);
    return $('#almacen_id').val() || '';
}

/* ---------- Mayúsculas en vivo ---------- */
document.addEventListener('input', (e) => {
    if (e.target.matches('input[type="text"], textarea')) {
        e.target.value = e.target.value.toUpperCase();
    }
});

/* ---------- DataTable + Filtro ---------- */
let tabla;
let filtroGlobalIndex = null;

$(document).ready(function () {
    // Filtro por estado + almacén
    filtroGlobalIndex = $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
        if (settings.nTable.id !== 'tablaVehiculos') return true;
        const estadoFiltro = $('#filtroEstado').val();
        const almacenFiltro = getAlmacenFiltro();
        const $row = $(tabla.row(dataIndex).node());
        const okEstado  = !estadoFiltro || $row.attr('data-estado') === estadoFiltro;
        const okAlmacen = !almacenFiltro || $row.attr('data-almacen') === almacenFiltro;
        return okEstado && okAlmacen;
    });

    tabla = $('#tablaVehiculos').DataTable({
        language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json' },
        dom: 'rt<"d-flex justify-content-between align-items-center mt-4 px-2"ip>',
        pageLength: 8,
        responsive: true,
        columnDefs: [{ orderable: false, targets: -1 }]  // última columna
    });

    $('#busquedaVehiculo').on('keyup', function () { tabla.search(this.value).draw(); });
    $('#filtroEstado, #almacen_id').on('change', function () { tabla.draw(); });

    if (getAlmacenFiltro() !== '') tabla.draw();
});

/* ---------- CRUD vehículo ---------- */
function nuevoVehiculo() {
    $('#formVehiculo')[0].reset();
    $('#v_id').val('0');
    if ($('#v_almacen_id').is('select')) $('#v_almacen_id').val('');
    $('#modalTitulo').html('<i class="bi bi-plus-circle me-2"></i>Añadir Nueva Unidad');
    $('#modalVehiculo').modal('show');
}

function editarVehiculo(v) {
    $('#modalTitulo').html('<i class="bi bi-pencil-square me-2"></i>Gestionar Unidad');
    $('#v_id').val(v.id);
    $('#v_nombre').val(v.nombre);
    $('#v_placas').val(v.placas);
    $('#v_modelo').val(v.modelo_año);
    $('#v_capacidad').val(v.capacidad_carga_kg);
    $('#v_estado').val(v.estado_unidad);
    $('#v_tipo').val(v.tipo);
    $('#v_vin').val(v.serie_vin);
    if ($('#v_almacen_id').is('select')) $('#v_almacen_id').val(v.almacen_id);
    $('#modalVehiculo').modal('show');
}

$('#formVehiculo').on('submit', async function (e) {
    e.preventDefault();
    Swal.fire({ title: 'Procesando...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
    try {
        const res = await postForm(VEHICULOS_ENDPOINT, new FormData(this));
        if (res.status === 'success') {
            Swal.fire({ icon: 'success', title: '¡Éxito!', timer: 1500, showConfirmButton: false })
                .then(() => location.reload());
        } else {
            Swal.fire('Error', res.message || 'No se pudo guardar', 'error');
        }
    } catch (err) {
        Swal.fire('Error', err.message || 'Error de conexión', 'error');
    }
});

async function eliminarVehiculo(id) {
    const c = await Swal.fire({
        title: '¿Confirmar Baja?',
        text: 'La unidad se marcará como inactiva.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ff3b30',
        confirmButtonText: 'Sí, dar de baja'
    });
    if (!c.isConfirmed) return;

    const fd = new FormData();
    fd.append('action', 'eliminar');
    fd.append('id', id);
    try {
        const res = await postForm(VEHICULOS_ENDPOINT, fd);
        if (res.status === 'success') location.reload();
        else Swal.fire('Error', res.message || 'No se pudo eliminar', 'error');
    } catch (err) {
        Swal.fire('Error', err.message, 'error');
    }
}

/* ---------- Filtros ---------- */
function limpiarFiltros() {
    $('#busquedaVehiculo').val('');
    $('#filtroEstado').val('');
    if (ALMACEN_FIJO === null) $('#almacen_id').val('');
    // Ya NO se hace pop(); el filtro global sigue activo pero sin valores → muestra todo
    tabla.search('').draw();
}

/* ---------- Documentos ---------- */
function subirDocumentoCompra(vehiculo_id) {
    Swal.fire({
        title: 'Documento de Vehículo',
        html: `
            <div class="text-start">
                <label class="fw-bold small mb-2">Subir / Reemplazar documento</label>
                <input type="file" id="swal_file_doc" class="form-control mb-2" accept=".pdf,image/*">
            </div>`,
        icon: 'info',
        showCancelButton: true,
        confirmButtonText: 'Guardar',
        confirmButtonColor: '#198754',
        focusConfirm: false,
        preConfirm: async () => {
            const file = document.getElementById('swal_file_doc')?.files[0];
            if (!file) { Swal.showValidationMessage('Selecciona un archivo'); return false; }
            const fd = new FormData();
            fd.append('vehiculo_id', vehiculo_id);
            fd.append('documento', file);
            try {
                const res = await postForm(`${VEHICULOS_ENDPOINT}?action=subirDocumento`, fd);
                if (!res.success) throw new Error(res.message || 'Error al subir archivo');
                return res;
            } catch (err) {
                Swal.showValidationMessage(err.message);
                return false;
            }
        }
    }).then(r => {
        if (!r.isConfirmed || !r.value) return;
        Swal.fire({ icon: 'success', title: 'Guardado', text: 'Documento actualizado', timer: 1500, showConfirmButton: false })
            .then(() => location.reload());
    });
}

function eliminarDocumento(id) {
    Swal.fire({
        title: 'Eliminar Documento',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Eliminar',
        confirmButtonColor: '#ed0909',
        preConfirm: async () => {
            const fd = new FormData();
            fd.append('id', id);
            try {
                const res = await postForm(`${VEHICULOS_ENDPOINT}?action=eliminarDocumento`, fd);
                if (!res.success) throw new Error(res.message || 'Error al eliminar');
                return res;
            } catch (err) {
                Swal.showValidationMessage(err.message);
                return false;
            }
        }
    }).then(r => {
        if (!r.isConfirmed || !r.value) return;
        Swal.fire({ icon: 'success', title: 'Eliminado', timer: 1500, showConfirmButton: false })
            .then(() => location.reload());
    });
}
</script>
</body>
</html>