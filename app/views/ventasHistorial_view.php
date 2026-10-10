<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Entregas | Sistema</title>
    <link rel="icon" type="image/png" href="/cfsistem/public/assets/logo.png">

    <link rel="shortcut icon" href="/cfsistem/public/assets/logo.ico" type="image/x-icon">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <?php require_once __DIR__ . '/layout/icono.php' ?>
    <?php if (function_exists('cargarEstilos')) {
        cargarEstilos();
    } ?>

    <style>
        .btn-animado-entrega {
            position: relative;
            overflow: hidden;
            color: #fff;
            font-weight: 600;
            letter-spacing: .3px;
            transition: all .25s ease;

            background: linear-gradient(270deg,
                    #7c3aed,
                    #ec4899,
                    #f97316,
                    #3b82f6,
                    #7c3aed);

            background-size: 600% 600%;
            animation: moverGradiente 8s ease infinite;

            box-shadow:
                0 4px 18px rgba(124, 58, 237, .35),
                0 2px 8px rgba(236, 72, 153, .25);
        }

        .btn-animado-entrega:hover {
            transform: translateY(-2px) scale(1.02);
            box-shadow:
                0 8px 24px rgba(124, 58, 237, .45),
                0 4px 14px rgba(236, 72, 153, .35);
        }

        .btn-animado-entrega:disabled {
            opacity: .7;
            cursor: not-allowed;
        }

        .btn-animado-entrega::before {
            content: '';
            position: absolute;
            top: 0;
            left: -120%;
            width: 80%;
            height: 100%;

            background: linear-gradient(120deg,
                    transparent,
                    rgba(255, 255, 255, .35),
                    transparent);

            animation: brillo 2.8s linear infinite;
        }

        @keyframes moverGradiente {
            0% {
                background-position: 0% 50%;
            }

            50% {
                background-position: 100% 50%;
            }

            100% {
                background-position: 0% 50%;
            }
        }

        @keyframes brillo {
            0% {
                left: -120%;
            }

            100% {
                left: 140%;
            }
        }

        :root {
            --sidebar-width: 250px;
            --primary-dark: #2c3e50;
            --accent-color: #34495e;
            --bg-body: #f8f9fa;
        }

        body {
            background-color: var(--bg-body);
            overflow-x: hidden;
            padding-top: 20px;
            text-transform: uppercase !important;
        }

        .main-content {
            margin-left: var(--sidebar-width);
            padding: 2rem;
            min-height: 100vh;
            transition: all 0.3s;
        }

        .scroll-table {
            background: white;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            overflow: hidden;
        }

        .table thead th {
            background-color: var(--primary-dark);
            color: white;
            font-weight: 500;
            text-transform: uppercase;
            font-size: 0.75rem;
            padding: 12px;
            border: none;
        }

        .btn-action {
            background-color: var(--accent-color);
            color: white;
            border: none;
        }

        .btn-action:hover {
            background-color: var(--primary-dark);
            color: white;
        }

        .filter-card {
            border: none;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            border-radius: 10px;
        }

        .modal-header {
            background-color: var(--primary-dark) !important;
            color: white;
            border: none;
        }

        .input-entrega {
            border: 2px solid #28a745 !important;
            max-width: 90px;
            text-align: center;
            font-weight: bold;
        }

        @media (max-width: 992px) {
            .main-content {
                margin-left: 0;
                padding: 1rem;
            }
        }
    </style>
</head>

<body>
    <?php if (function_exists('renderizarLayout')) {
        renderizarLayout($paginaActual);
    } ?>
    <div class="main-content">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="fw-bold card-title-text m-0">Historial de Ventas</h3>
                <div id="loader" class="spinner-border spinner-border-sm text-secondary d-none"></div>
            </div>
            <a class="d-inline-flex align-items-center gap-2 btn btn-outline-primary btn-sm rounded-pill px-3 py-2"
                href="/cfsistem/app/controllers/misRepartosController.php">
                <i class="bi bi-list-ul"></i>
                Gestionar mis repartos
            </a>
            <div class="card border-0 shadow-sm rounded-4 mb-4 filter-card">
                <div class="card-body p-4">

                    <!-- Header del card -->
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <div class="d-flex align-items-center gap-2">
                            <div class="d-flex align-items-center justify-content-center rounded-3 bg-primary-subtle text-primary"
                                style="width: 38px; height: 38px;">
                                <i class="bi bi-funnel-fill fs-5"></i>
                            </div>
                            <div>
                                <h6 class="mb-0 fw-bold">Filtros de búsqueda</h6>
                                <small class="text-body-secondary">Refina los resultados de ventas</small>
                            </div>
                        </div>
                        <button type="button" class="btn btn-sm btn-light rounded-pill px-3" onclick="limpiarFiltros()">
                            <i class="bi bi-arrow-counterclockwise me-1"></i> Limpiar
                        </button>
                    </div>

                    <!-- Filtros -->
                    <div class="row g-3 align-items-end">

                        <div class="col-md-3">
                            <label
                                class="form-label small fw-semibold text-body-secondary text-uppercase mb-1">Buscador</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-body-tertiary border-end-0 rounded-start-3">
                                    <i class="bi bi-search text-body-secondary"></i>
                                </span>
                                <input type="text" id="f_search" class="form-control border-start-0 rounded-end-3"
                                    placeholder="Folio o Cliente..." onkeyup="getVentas()">
                            </div>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label small fw-semibold text-body-secondary text-uppercase mb-1">Estatus
                                Entrega</label>
                            <select id="f_status" class="form-select form-select-sm rounded-3" onchange="getVentas()">
                                <option value="">Todos</option>
                                <option value="pendiente">Pendiente</option>
                                <option value="parcial">Parcial</option>
                                <option value="entregado">Entregado</option>
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label for="select-usuarios"
                                class="form-label small fw-semibold text-body-secondary text-uppercase mb-1">Vendedor</label>
                            <select class="form-select form-select-sm rounded-3" id="select-usuarios" name="usuario_id"
                                onchange="getVentas()">
                                <option value="">Seleccione vendedor</option>
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label small fw-semibold text-body-secondary text-uppercase mb-1">Estatus
                                Pago</label>
                            <select id="f_pago" class="form-select form-select-sm rounded-3" onchange="getVentas()">
                                <option value="">Todos</option>
                                <option value="deuda">Con Deuda</option>
                                <option value="pagado">Pagados</option>
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label
                                class="form-label small fw-semibold text-body-secondary text-uppercase mb-1">Periodo</label>
                            <select id="f_rango" class="form-select form-select-sm rounded-3" onchange="togglePerso()">
                                <option value="hoy">Hoy</option>
                                <option value="ayer">Ayer</option>
                                <option value="semana" selected>Semana</option>
                                <option value="mes">Mes</option>
                                <option value="todos">Historial Completo</option>
                                <option value="personalizado">Rango...</option>
                            </select>
                        </div>

                        <div class="col-md-3 d-none" id="div_p">
                            <label
                                class="form-label small fw-semibold text-body-secondary text-uppercase mb-1">Fechas</label>
                            <div class="input-group input-group-sm">
                                <input type="date" id="f_ini" class="form-control rounded-start-3"
                                    value="<?= date('Y-m-d') ?>" onchange="getVentas()">
                                <span class="input-group-text bg-body-tertiary border-start-0 border-end-0 px-2">
                                    <i class="bi bi-arrow-right text-body-secondary"></i>
                                </span>
                                <input type="date" id="f_fin" class="form-control rounded-end-3"
                                    value="<?= date('Y-m-d') ?>" onchange="getVentas()">
                            </div>
                        </div>

                        <div class="col-md-2">
                            <label
                                class="form-label small fw-semibold text-body-secondary text-uppercase mb-1">Ubicación</label>
                            <select id="f_almacen" class="form-select form-select-sm rounded-3" onchange="getVentas()">
                                <?php if ($esadmin): ?>
                                    <option value="0">Todos</option>
                                <?php endif; ?>
                                <?php foreach ($almacenes as $a): ?>
                                    <option value="<?= $a['id'] ?>" <?= ($a['id'] == $_SESSION['almacen_id']) ? 'selected' : '' ?>>
                                        <?= $a['nombre'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label small fw-semibold text-body-secondary text-uppercase mb-1">Estatus
                                Factura</label>
                            <select id="estado_factura" class="form-select form-select-sm rounded-3"
                                onchange="getVentas()">
                                <option value="">Todos</option>
                                <option value="1">Facturada</option>
                                <option value="0">No facturada</option>
                            </select>
                        </div>

                    </div>
                </div>
            </div>

            <div class="scroll-table shadow-sm">
                <div class="table-responsive" style="max-height: 60vh;">
                    <table class="table table-hover align-middle mb-0" id="tablaVentas">
                        <thead>
                            <tr>

                                <th class="ps-3">Fecha</th>
                                <th>Folio</th>
                                <th>Almacén</th>
                                <th>Vendedor</th>
                                <th>Cliente</th>
                                <th>Total</th>
                                <th>Saldo Cobro</th>
                                <th>Facturada</th>
                                <th class="text-center">Estado Entrega</th>
                                <th class="text-end pe-3">Acciones</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>

        </div>



        <div class="modal fade" id="modalCancelarVenta" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">Cancelar Venta</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <input type="hidden" id="cancelar_id_venta">

                        <div class="mb-3">
                            <label class="form-label">Motivo de la cancelación</label>
                            <textarea id="cancelar_motivo" class="form-control text-uppercase" rows="4"
                                placeholder="Escriba el motivo..."></textarea>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-success" onclick="procesarCancelacion(true)">
                            Con Saldo a Favor
                        </button>

                        <button class="btn btn-danger" onclick="procesarCancelacion(false)">
                            Sin Saldo
                        </button>

                        <button class="btn btn-secondary" data-bs-dismiss="modal">
                            Regresar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
        <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

        <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <?php require_once __DIR__ . '/ventasHistorialModales/imprimirDetalleVenta.php'; ?>
        <?php require_once __DIR__ . '/ventasHistorialModales/registarAbono.php'; ?>
        <?php require_once __DIR__ . '/ventasHistorialModales/evidenciasEntregaModel.php'; ?>
        <?php require_once __DIR__ . '/ventasHistorialModales/editarVentaModal.php'; ?>
        <?php require_once __DIR__ . '/ventasHistorialModales/facturaModal.php'; ?>
        <?php require_once __DIR__ . '/ventasHistorialModales/verDetalleModal.php'; ?>
        <?php require_once __DIR__ . '/ventasHistorialModales/modalImprimirRuta.php'; ?>
        <?php require_once __DIR__ . '/ventasHistorialModales/modalSolicitudCancelacion.php'; ?>
        <?php require_once __DIR__ . '/entregasComponets/modalEntregaVentas.php'; ?>

        <script>
            let modalCancelarVenta;

            document.addEventListener('DOMContentLoaded', () => {
                modalCancelarVenta = new bootstrap.Modal(
                    document.getElementById('modalCancelarVenta')
                );
            });

            function abrirModalCancelacion(idVenta, folio) {

                document.getElementById('cancelar_id_venta').value = idVenta;
                document.getElementById('cancelar_motivo').value = '';
                document.querySelector('#modalCancelarVenta .modal-title').innerHTML =
                    `Cancelar Venta ${folio}`;
                modalCancelarVenta.show();
            }
            async function procesarCancelacion(conSaldo) {

                const idVenta = document.getElementById('cancelar_id_venta').value;
                const motivo = document.getElementById('cancelar_motivo').value.trim();

                if (!motivo) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Motivo requerido',
                        text: 'Debe capturar el motivo de la cancelación'
                    });
                    return;
                }

                modalCancelarVenta.hide();

                const accion = conSaldo ?
                    'cancelarVenta' :
                    'cancelarVentaSinSaldo';

                Swal.fire({
                    title: 'Procesando...',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });

                try {

                    const response = await fetch(`${URL_CONTROLLER}?action=${accion}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            id_venta: idVenta,
                            motivo: motivo
                        })
                    });

                    const res = await response.json();

                    if (res.status === 'success') {

                        Swal.fire({
                            icon: 'success',
                            title: 'Venta cancelada',
                            text: res.message
                        });

                        getVentas();

                    } else {

                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: res.message
                        });

                    }

                } catch (error) {

                    console.error(error);

                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'No se pudo conectar con el servidor'
                    });
                }
            }



            cargarUsuariosSelect();
            async function cargarUsuariosSelect() {
                const select = document.getElementById('select-usuarios');
                if (!select) return; // Seguridad por si el select no está en la vista actual

                try {
                    // 1. Realizar la petición a tu controlador de Cf System
                    const url = '/cfsistem/app/controllers/ventasHistorialController.php?action=obtenerUsuarios';
                    const respuesta = await fetch(url);

                    if (!respuesta.ok) throw new Error('Error en la respuesta del servidor');

                    const resultado = await respuesta.json();

                    // 2. Verificar que la respuesta sea exitosa y contenga los datos
                    if (resultado.success && Array.isArray(resultado.data)) {

                        // Limpiamos el select y dejamos una opción inicial neutra
                        // select.innerHTML = '<option value="" selected disabled> Seleccione vendedor</option>';

                        // 3. Recorrer los usuarios y crear las opciones
                        resultado.data.forEach(usuario => {
                            const opcion = document.createElement('option');
                            opcion.value = usuario.id; // El ID que se enviará en el formulario

                            // Formateamos el texto: "Nombre (Almacén - Rol)" para que sea súper descriptivo
                            const almacen = usuario.almacen_nombre || 'Sin Almacén';
                            opcion.textContent = `${usuario.nombre}`;

                            // Agregamos la opción al select
                            select.appendChild(opcion);
                        });

                    } else {
                        select.innerHTML = '<option value="">No se pudieron cargar los usuarios</option>';
                        console.error('El backend no devolvió success:true o la estructura cambió');
                    }

                } catch (error) {
                    select.innerHTML = '<option value="">Error al cargar la lista</option>';
                    console.error('Error al ejecutar cargarUsuariosSelect:', error);
                }
            }

            const modalObj = new bootstrap.Modal('#modalDetalle');
            let ventaActual = null;
            // La ruta al controlador (ajusta si el nombre del archivo varía)
            const URL_CONTROLLER = '/cfsistem/app/controllers/ventasHistorialController.php';

            async function getVentas() {
                $('#loader').removeClass('d-none');


                const params = new URLSearchParams({
                    action: 'listar',
                    // <--- Nuevo parámetro para el ID de venta
                    f_search: $('#f_search').val(),
                    f_rango: $('#f_rango').val(),
                    f_inicio: $('#f_ini').val(),
                    f_fin: $('#f_fin').val(),
                    f_almacen: $('#f_almacen').val(),
                    f_status: $('#f_status').val(),
                    f_pago: $('#f_pago').val(),
                    f_vendedor: $('#select-usuarios').val() ?? '',
                    f_factura: $('#estado_factura').val() ?? ''
                });

                try {
                    const res = await fetch(`${URL_CONTROLLER}?${params.toString()}`);
                    const data = await res.json();
                    //<td class="ps-3 small">${v.id}</td>
                    let totalVendido = 0;
                    let deuda = 0;

                    $('#tablaVentas tbody').html(data.map(v => {
                        let total = 0;
                        let pagado = 0;
                        if (v.estado_general != 'cancelada') {
                            total = parseFloat(v.total) || 0;
                            pagado = parseFloat(v.pagado) || 0;
                        }
                        let saldo = total - pagado;

                        if (v.estado_general == 'activa') {
                            totalVendido += total;
                            deuda += (total - pagado);
                        }

                        let badgeCobro = (saldo <= 0) ?
                            '<span class="text-success small fw-bold"><i class="bi bi-check-circle"></i> Pagado</span>' :
                            `<span class="text-danger small fw-bold">Debe: $${saldo.toFixed(2)}</span>`;

                        let entrega = (v.estado_general == 'activa') ?
                            `<span class="badge ${v.estado_entrega == 'entregado' ? 'bg-success' : (v.estado_entrega == 'parcial' ? 'bg-warning card-title-text' : 'bg-danger')}">
            ${v.estado_entrega.toUpperCase()}
        </span>` :
                            '<span class="text-danger small fw-bold"><i class="bi bi-check-circle"></i> Cancelado</span>';

                        let factura = (v.estado_general == 'activa') ?
                            `${v.factura}
        <button type="button" class="btn btn-link text-primary p-1 border-0" onclick="modalFactura(${v.id},${v.factura})" title="Agregar Factura">
            <i class="bi bi-pencil-square me-2"></i>
        </button>` : '';
                        let rolAct = <?= $rol ?>;
                        let botonCancelar = rolAct == 1 ? `<button type="button" 
        class="btn btn-glass-danger rounded-3 border-0 d-inline-flex align-items-center justify-content-center" 
        onclick="abrirModalCancelacion('${v.id}','${v.folio}')" 
        data-bs-toggle="tooltip" 
        data-bs-placement="top" 
        title="Cancelar Venta">
    <i class="bi bi-x-circle-fill fs-6"></i>
</button>

<style>
.btn-glass-danger {
    width: 36px;
    height: 36px;
    background-color: rgba(241, 232, 232, 0.98);
    color: #ff0808;
    transition: all 0.2s ease-in-out;
}

.btn-glass-danger:hover {
    background-color: rgba(11, 11, 11, 0.25);
    color: #f70000;
    transform: scale(1.08);
}
</style>` : `<button type="button" 
        class="btn btn-glass-danger rounded-3 border-0 d-inline-flex align-items-center justify-content-center" 
        onclick="abrirModalSolicitudCancelacion('${v.id}')" 
        data-bs-toggle="tooltip" 
        data-bs-placement="top" 
        title="Cancelar Venta">
    <i class="bi bi-x-circle-fill fs-6"></i>
</button>

<style>
.btn-glass-danger {
    width: 36px;
    height: 36px;
    background-color: rgba(255, 255, 255, 0.1);
    color: #020202;
    transition: all 0.2s ease-in-out;
}

.btn-glass-danger:hover {
    background-color: rgba(255, 0, 25, 0.25);
    color: #fa0a0a;
    transform: scale(1.08);
}
</style>`;
                        let cancelada = (v.estado_general == 'activa') ? `
        

        <div class="btn-group" role="group">
            <button type="button" class="btn btn-link text-secondary btn-sm px-3 border-0 dropdown-toggle remove-caret" 
                    data-bs-toggle="dropdown" 
                    aria-expanded="false"
                    data-bs-toggle="tooltip" 
                    data-bs-placement="top" 
                     title="Más opciones">
                <i class="bi bi-three-dots fs-5"></i>
            </button>
           
                <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-4 p-2 mt-2 animated--fade-in" style="min-width: 220px;">
    <li>
        <button class="dropdown-item py-2 px-3 rounded-3 d-flex align-items-center fw-semibold card-title-text hover-bg-light" 
                onclick="gestionarSolicitud(${v.id})">
            <i class="bi bi-pencil-square me-2 text-primary fs-6"></i> Editar
        </button>
    </li>
    
    <li><hr class="dropdown-divider my-2 opacity-25"></li>
    
    <li>
        <a class="dropdown-item py-2 px-3 rounded-3 d-flex align-items-center text-secondary hover-primary" 
           href="/cfsistem/app/backend/ventas/ticket_venta.php?id=${v.id}" target="_blank">
            <i class="bi bi-receipt me-2 text-primary"></i> Imprimir Ticket
        </a>
    </li>
    
    <li>
        <a class="dropdown-item py-2 px-3 rounded-3 d-flex align-items-center text-secondary hover-info" 
           href="/cfsistem/app/backend/ventas/ticketFormal.php?id=${v.id}" target="_blank">
            <i class="bi bi-file-earmark-check me-2 card-title-text"></i> Imprimir Remision
        </a>
    </li>

            </ul>
        </div>${botonCancelar}` : ``;

                        return `<tr>
        <td class="ps-3 small">${v.fecha}</td>
        <td class="fw-bold">${v.folio}</td>
        <td><span class="badge bg-light text-dark border fw-normal">${v.almacen_nombre}</span></td>
        <td><div class="small fw-bold">${v.vendedor}</div></td>
        <td><div class="small fw-bold">${v.cliente}</div></td>
        <td class="fw-bold card-title-text">$${total.toFixed(2)}</td>
        <td>${v.estado_general == 'activa' ? badgeCobro : '<span class="text-danger small fw-bold"><i class="bi bi-check-circle"></i> Cancelado</span>'}</td>
        <td><div class="small fw-bold">${factura}</div></td>
        <td class="text-center">${entrega}</td>
        <td class="text-end pe-3">
            <div class="btn-group bg-white rounded-3 shadow-sm border p-1" role="group" aria-label="Acciones de venta">
                <button type="button" 
        class="btn btn-glass-eye rounded-3 border-0 d-inline-flex align-items-center justify-content-center" 
        onclick="verDetalle(${v.id})" 
        data-bs-toggle="tooltip" 
        data-bs-placement="top" 
        title="Gestionar Venta">
    <i class="bi bi-eye fs-5"></i>
</button>

<style>
.btn-glass-eye {
    width: 36px;
    height: 36px;
    background-color: rgba(13, 110, 253, 0.08);
    color: #0d6efd;
    transition: all 0.2s ease-in-out;
}

.btn-glass-eye:hover {
    background-color: rgba(13, 110, 253, 0.2);
    color: #0a58ca;
    transform: scale(1.08);
}
</style>
                ${cancelada}
            </div>
        </td>
    </tr>`;
                    }).join(''));

                    // Fila de totales corregida (Sin 'v.almacen_nombre' para evitar errores)
                    let totales = `<tr class="table-light fw-bold border-top border-dark">
    <td class="ps-3 small"></td>
    <td class="fw-bold">TOTALES</td>
    <td></td>
    <td></td>
    <td></td>
    <td class="card-title-text">Total: $${totalVendido.toFixed(2)}</td>
    <td class="text-success">Cobrado: $${(totalVendido - deuda).toFixed(2)}</td>
    <td class="text-danger">Por Cobrar: $${deuda.toFixed(2)}</td>
    <td></td>
    <td></td>
</tr>`;



                    // CORRECCIÓN AQUÍ: Agregamos la fila al final del tbody usando .append() sin .join()
                    $('#tablaVentas tbody').append(totales);
                } catch (e) {
                    console.error("Error al cargar ventas:", e);
                } finally {
                    $('#loader').addClass('d-none');
                }
            }
            document.addEventListener('input', e => {

                if (e.target.classList.contains('input-entrega1')) {

                    const max = parseFloat(e.target.max) || 0;
                    const min = parseFloat(e.target.min) || 0;
                    const factor = parseFloat(e.target.dataset.factor) || 1;

                    let value = e.target.value;

                    // 👉 PERMITIR BORRADO COMPLETO
                    if (value === "") {
                        const contenedor = e.target.parentElement;
                        const inputEntrega = contenedor.querySelector('.input-entrega');

                        if (inputEntrega) {
                            inputEntrega.value = "";
                        }
                        return; // 🔥 importante: no seguir procesando
                    }

                    value = parseFloat(value);

                    if (isNaN(value)) return;

                    if (value > max) value = max;
                    if (value < min) value = min;

                    e.target.value = value;

                    const contenedor = e.target.parentElement;
                    const inputEntrega = contenedor.querySelector('.input-entrega');

                    if (inputEntrega) {
                        inputEntrega.value = (value * factor).toFixed(2);
                    }
                }
            });


            async function procesarEntrega() {
                const fd = new FormData();
                let ok = false;

                $('.input-entrega').each(function () {

                    const cant = parseFloat($(this).val());

                    console.log($(this).data('dvid'), cant);

                    if (cant > 0) {

                        fd.append(
                            `productos[${$(this).data('dvid')}]`,
                            cant
                        );

                        ok = true;
                    }
                });

                if (!ok) return Swal.fire('Atención', 'Indique al menos una cantidad válida para entregar', 'warning');

                fd.append('venta_id', ventaActual.info.id);

                try {
                    const res = await fetch(`${URL_CONTROLLER}?action=guardarEntrega`, {
                        method: 'POST',
                        body: fd
                    });

                    // Verificamos si la respuesta del servidor es un JSON válido
                    const result = await res.json();

                    if (result.status === 'success') {

                        modalObj.hide();

                        getVentas();

                        Swal.fire({
                            title: '¡Listo!',
                            text: 'Entrega guardada correctamente',
                            icon: 'success',
                            timer: 500,
                            showConfirmButton: false
                        });

                        // 🔥 volver a abrir automáticamente
                        setTimeout(() => {

                            verDetalle(ventaActual.info.id);

                        }, 501);

                    } else {
                        // AQUÍ MANEJAMOS EL ERROR DE STOCK (o cualquier otro error del Model)
                        // Usamos result.message que es el que trae "Stock insuficiente en almacén..."
                        Swal.fire('No se pudo entregar', result.message || 'Error desconocido', 'error');
                    }

                } catch (e) {
                    console.error("Error al procesar entrega:", e);
                    Swal.fire('Error Técnico', 'Hubo un problema de conexión con el servidor', 'error');
                }
            } // Instanciamos el nuevo modal
            const modalAbonoObj = new bootstrap.Modal('#modalAbono');



            function togglePerso() {
                $('#div_p').toggleClass('d-none', $('#f_rango').val() !== 'personalizado');
                getVentas();
            }

            function alternarModo(e) {
                $('.col-input').toggleClass('d-none', !e);
                $('#btnHabilitar').toggle(!e && ventaActual.info.estado_entrega !== 'entregado');
                $('#controlesGuardar').toggleClass('d-none', !e);
            }

            $(document).ready(function () {
                // 1. Carga inicial de datos
                getVentas();

                // 2. Escuchadores para filtros (opcional, pero recomendado para centralizar)
                $('#f_rango').on('change', togglePerso);
                // getVentas ya se llama mediante onchange/onkeyup en tu HTML, lo cual está bien.

                console.log("Sistema de historial listo.");
            });
        </script>
        <script>
            async function confirmarCancelacion(idVenta, folio, total, pagado) {

                // 1. Lanzamos el SweetAlert con las 3 opciones
                const result = await Swal.fire({
                    title: `¿Cancelar Venta ${folio}?`,
                    text: "Selecciona si deseas reintegrar el dinero al saldo del cliente o solo anular la venta.",
                    icon: 'warning',
                    input: 'text',
                    inputLabel: 'Motivo de la cancelación',
                    inputPlaceholder: 'Escriba por qué se cancela...',
                    showCancelButton: true,
                    showDenyButton: true,
                    confirmButtonColor: '#28a745', // Verde -> Con Saldo
                    denyButtonColor: '#d33', // Rojo -> Sin Saldo
                    cancelButtonColor: '#6c757d', // Gris -> Regresar
                    confirmButtonText: '<i class="bi bi-cash-stack"></i> Con Saldo a Favor',
                    denyButtonText: '<i class="bi bi-x-circle"></i> Sin Saldo',
                    cancelButtonText: 'Regresar',
                    inputValidator: (value) => {
                        if (!value) return '¡El motivo es obligatorio!';
                    }
                });

                // 2. Si se presionó cualquiera de los dos botones de ejecución (Confirmar o Denegar)
                if (result.isConfirmed || result.isDenied) {
                    // IMPORTANTE: Capturamos el motivo desde result.value
                    const motivo = 'cancelacion';

                    // Elegimos la ruta del controlador según el botón
                    const accion = result.isConfirmed ? 'cancelarVenta' : 'cancelarVentaSinSaldo';

                    Swal.fire({
                        title: 'Procesando...',
                        didOpen: () => {
                            Swal.showLoading()
                        },
                        allowOutsideClick: false
                    });

                    try {
                        const response = await fetch(`${URL_CONTROLLER}?action=${accion}`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                id_venta: idVenta,
                                motivo: motivo
                            })
                        });

                        const res = await response.json();

                        if (res.status === 'success') {
                            await Swal.fire({
                                title: '¡Venta Cancelada!',
                                text: res.message,
                                icon: 'success',
                                timer: 2000,
                                showConfirmButton: false
                            });

                            // Refrescamos la tabla de ventas
                            if (typeof getVentas === 'function') getVentas();

                        } else {
                            Swal.fire('Error', res.message, 'error');
                        }
                    } catch (error) {
                        console.error("Error en la petición:", error);
                        Swal.fire('Error', 'No se pudo conectar con el servidor', 'error');
                    }
                }
            }
        </script>
        <script>
            // Selecciona todos los inputs de texto y también los textareas
            document.querySelectorAll('input[type="text"], textarea').forEach(elemento => {
                elemento.addEventListener('input', function () {
                    // Convierte el valor a mayúsculas en tiempo real
                    this.value = this.value.toUpperCase();
                });
            });
        </script>
</body>

</html>