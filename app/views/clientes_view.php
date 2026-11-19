<?php
/**
 * clientes_view.php
 * Vista de administración de clientes: Filtros, CRUD por Modales y AJAX.
 * Lógica de permisos: Admin global vs Usuario de sucursal.
 */
$usosCFDI = ['G01' => 'Adquisición', 'G03' => 'Gastos', 'P01' => 'Por definir', 'S01' => 'Sin efectos'];
$almacen_usuario = intval($_SESSION['almacen_id'] ?? 0); // 0 es Admin
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clientes | cfsistem</title>
    <link rel="icon" type="image/png" href="/cfsistem/public/assets/logo.png">
    <link rel="shortcut icon" href="/cfsistem/public/assets/logo.ico" type="image/x-icon">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <?php require_once __DIR__ . '/layout/icono.php' ?>

    <?php if (function_exists('cargarEstilos')) {
        cargarEstilos();
    } ?>

    <style>
        :root {
            --sidebar-width: 260px;
            --navbar-height: 65px;
            --apple-bg: #f5f5f7;
            --accent-blue: #007aff;
        }

        body {
            background-color: var(--apple-bg);
            font-family: 'SF Pro Display', -apple-system, sans-serif;
        }

        .main-content {
            margin-left: var(--sidebar-width);
            padding: 40px;
            padding-top: calc(var(--navbar-height) + 20px);
            max-width: 100%;
            overflow-x: hidden;
        }

        .card-premium {
            border: none;
            border-radius: 20px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.04);
            backdrop-filter: blur(10px);
        }

        .badge-ubicacion {
            background-color: #f2f2f7;
            color: #1d1d1f;
            border: 1px solid #d1d1d6;
            padding: 0.4rem 0.7rem;
            font-size: 0.75rem;
            font-weight: 600;
            border-radius: 8px;
        }

        /* DataTables Custom */
        .dataTables_wrapper .pagination .page-item.active .page-link {
            background-color: var(--accent-blue);
            border-color: var(--accent-blue);
            border-radius: 8px;
        }

        .table thead th {
            background: #fbfbfd;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #86868b;
            border-bottom: 1px solid #d1d1d6;
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
                padding: 16px;
                padding-top: 90px;
                width: 100%;
                max-width: 100vw;
                overflow-x: hidden;
            }

            .table-responsive {
                overflow-x: auto !important;
                -webkit-overflow-scrolling: touch;
                width: 100%;
                border-radius: 12px;
            }

            #tablaClientes {
                min-width: 720px;
                white-space: nowrap;
            }

            #tablaClientes td {
                white-space: nowrap;
            }

            .card-premium {
                overflow: visible;
            }

            .btn-primary.rounded-pill {
                font-size: 0.8rem;
                padding-left: 1rem !important;
                padding-right: 1rem !important;
            }

            h2.card-title-text {
                font-size: 1.5rem !important;
            }

            .row.mb-4>div {
                width: 100%;
            }
        }

        .table-responsive::-webkit-scrollbar {
            height: 8px;
        }

        .table-responsive::-webkit-scrollbar-track {
            background: #f2f2f7;
            border-radius: 10px;
        }

        .table-responsive::-webkit-scrollbar-thumb {
            background: #c7c7cc;
            border-radius: 10px;
        }

        .table-responsive::-webkit-scrollbar-thumb:hover {
            background: #a1a1a6;
        }

        /* ============================================================
           MODAL CLIENTE — iOS look & feel + 90% ancho + footer fijo
           ============================================================ */
        #modalCliente .modal-dialog {
            max-width: 90vw;
            width: 90vw;
            max-height: 92vh;
            margin: 4vh auto;
        }

        #modalCliente .modal-content {
            max-height: 92vh;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            border-radius: 24px;
            background-color: var(--bs-body-bg, #ffffff);
            border: 1px solid rgba(0, 0, 0, 0.08);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }

        #modalCliente .modal-header {
            flex-shrink: 0;
            border-top-left-radius: 24px;
            border-top-right-radius: 24px;
            background: transparent !important;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }

        #modalCliente form {
            flex: 1 1 auto;
            display: flex;
            flex-direction: column;
            min-height: 0;
            overflow: hidden;
        }

        #modalCliente .modal-body {
            flex: 1 1 auto;
            overflow-y: auto;
            min-height: 0;
        }

        #modalCliente .modal-footer {
            flex-shrink: 0;
            border-top: 1px solid rgba(0, 0, 0, 0.05);
            background: transparent;
        }

        @media (max-width: 991px) {
            #modalCliente .modal-dialog {
                max-width: 95vw;
                width: 95vw;
            }
        }

        #modalCliente .modal-body::-webkit-scrollbar {
            width: 8px;
        }

        #modalCliente .modal-body::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }

        #modalCliente .modal-body::-webkit-scrollbar-track {
            background: transparent;
        }

        /* Inputs iOS compactos */
        #modalCliente .form-control,
        #modalCliente .form-select {
            border-radius: 8px;
            padding: 0.35rem 0.65rem;
            font-size: 0.85rem;
            background-color: rgba(0, 0, 0, 0.02);
            border: 1px solid rgba(0, 0, 0, 0.12);
            transition: all 0.2s ease-in-out;
        }

        #modalCliente .form-control:focus,
        #modalCliente .form-select:focus {
            background-color: transparent;
            border-color: #007aff;
            box-shadow: 0 0 0 3px rgba(0, 122, 255, 0.15);
        }

        #modalCliente .form-label {
            font-size: 0.8rem;
            font-weight: 500;
            color: #6c757d;
            margin-bottom: 0;
        }

        #modalCliente .btn {
            border-radius: 10px;
            padding: 0.5rem 1rem;
            font-weight: 500;
            font-size: 0.85rem;
            transition: transform 0.1s ease, background-color 0.2s;
        }

        #modalCliente .btn:active {
            transform: scale(0.97);
        }

        /* Títulos de sección */
        #modalCliente .form-section-title {
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #007aff;
            margin: 1rem 0 0.75rem;
            padding-bottom: 0.35rem;
            border-bottom: 2px solid rgba(0, 122, 255, 0.15);
        }

        #modalCliente .form-section-title:first-child {
            margin-top: 0;
        }

        /* Modo oscuro */
        [data-bs-theme="dark"] #modalCliente .modal-content,
        body.dark-mode #modalCliente .modal-content {
            background-color: #1c1c1e;
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.6);
        }

        [data-bs-theme="dark"] #modalCliente .form-control,
        [data-bs-theme="dark"] #modalCliente .form-select,
        body.dark-mode #modalCliente .form-control,
        body.dark-mode #modalCliente .form-select {
            background-color: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.15);
            color: #f5f5f7;
        }

        [data-bs-theme="dark"] #modalCliente .form-label,
        body.dark-mode #modalCliente .form-label {
            color: #98989f;
        }

        [data-bs-theme="dark"] #modalCliente .modal-header,
        body.dark-mode #modalCliente .modal-header {
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }
    </style>
</head>

<body>
    <?php if (function_exists('renderizarLayout')) {
        renderizarLayout($paginaActual);
    } ?>

    <main class="main-content">

        <div class="d-flex justify-content-between align-items-center mb-5 animate__animated animate__fadeIn">
            <div>
                <h2 class="fw-bold m-0 card-title-text" style="font-size: 2rem; letter-spacing: -0.03em;">Cartera de
                    Clientes</h2>
                <p class="text-body-secondary mb-0 card-title-text">Gestión de datos fiscales y comerciales</p>
            </div>
            <button class="btn btn-primary rounded-pill px-4 shadow-sm d-flex align-items-center"
                onclick="nuevoCliente()"
                style="background: var(--accent-blue); border:none; height: 42px; font-weight: 600;">
                <i class="bi bi-person-plus-fill me-2 fs-5"></i> NUEVO CLIENTE
            </button>
        </div>

        <div class="card card-premium p-4 animate__animated animate__fadeInUp">
            <div class="row mb-4 g-3">
                <div class="col-md-5">
                    <div class="input-group border border-subtle rounded-3 p-1">
                        <span class="input-group-text bg-transparent border-0 text-body-secondary"><i
                                class="bi bi-search"></i></span>
                        <input type="text" id="busquedaCliente" class="form-control border-0 bg-transparent shadow-none"
                            placeholder="Buscar por nombre, RFC o correo...">
                    </div>
                </div>

                <?php if ($almacen_usuario == 0): ?>
                    <div class="col-md-4">
                        <select id="filtroAlmacenVista" class="form-select border-subtle rounded-3 h-100 shadow-none">
                            <option value="">🌐 Todas las Sucursales</option>
                            <?php foreach ($almacenes as $alm): ?>
                                <option value="<?= $alm['id'] ?>">📍 <?= htmlspecialchars($alm['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button class="btn btn-outline-secondary w-100 rounded-3 border-0 fw-bold text-body-secondary"
                            onclick="limpiarFiltros()">
                            <i class="bi bi-arrow-clockwise me-1"></i> RESET
                        </button>
                    </div>
                <?php endif; ?>
            </div>

            <div class="table-responsive">
                <table id="tablaClientes" class="table table-hover align-middle w-100">
                    <thead>
                        <tr>
                            <th class="ps-3">Nombre Comercial / Razón Social</th>
                            <th>Identificación (RFC)</th>
                            <th>Sucursal</th>
                            <th>Estatus</th>
                            <th class="text-end pe-3">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($clientes as $c): ?>
                            <tr class="fila-cliente" data-almacen-id="<?= $c['almacen_id'] ?>">
                                <td class="ps-3">
                                    <div class="fw-bold card-title-text"><?= htmlspecialchars($c['nombre_comercial']) ?>
                                    </div>
                                    <?php if (!empty($c['razon_social'])): ?>
                                        <div class="text-body-secondary small" style="font-size: 0.7rem;">
                                            <?= htmlspecialchars($c['razon_social']) ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge bg-white text-dark border px-2 py-1 fw-bold"
                                        style="font-family: monospace;">
                                        <?= htmlspecialchars($c['rfc']) ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge-ubicacion">
                                        <i class="bi bi-geo-alt me-1"></i>
                                        <?php
                                        $nombreAlmacen = 'Global';
                                        foreach ($almacenes as $alm) {
                                            if ($alm['id'] == $c['almacen_id']) {
                                                $nombreAlmacen = $alm['nombre'];
                                                break;
                                            }
                                        }
                                        echo htmlspecialchars($nombreAlmacen);
                                        ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" <?= $c['activo'] ? 'checked' : '' ?>
                                            onchange="cambiarEstado(<?= $c['id'] ?>, this.checked ? 1 : 0)"
                                            id="switch_<?= $c['id'] ?>">
                                        <label class="form-check-label small fw-medium" for="switch_<?= $c['id'] ?>">
                                            <?= $c['activo'] ? 'Activo' : 'Inactivo' ?>
                                        </label>
                                    </div>
                                </td>
                                <td class="text-end pe-3">
                                    <div class="btn-group shadow-sm rounded-pill bg-white px-1">
                                        <button class="btn btn-link btn-sm text-success p-2"
                                            onclick="abrirModalUbicaciones(<?= $c['id'] ?>, '<?= htmlspecialchars($c['nombre_comercial'], ENT_QUOTES) ?>')"
                                            title="Gestionar Sucursales / Ubicaciones">
                                            <i class="bi bi-geo-alt-fill"></i>
                                        </button>
                                        <button class="btn btn-link btn-sm text-primary p-2"
                                            onclick="editarCliente(<?= $c['id'] ?>)">
                                            <i class="bi bi-pencil-square"></i> <i class="bi bi-eye"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <!-- ============================================================
         MODAL CLIENTE — Estilo iOS
         ============================================================ -->
    <div class="modal fade" id="modalCliente" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

                <!-- Cabecera minimalista tipo iOS -->
                <div class="modal-header px-4 pt-4 pb-2">
                    <h5 class="modal-title fw-bold fs-5" id="modalTitulo">Nuevo Cliente</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form id="formCliente">
                    <div class="modal-body px-4 py-3">
                        <input type="hidden" name="cliente_id" id="cliente_id" value="0">

                        <!-- ============================================ -->
                        <!-- SECCIÓN 1: INFORMACIÓN GENERAL                -->
                        <!-- ============================================ -->
                        <div class="form-section-title">Información General</div>

                        <div class="d-flex flex-column gap-2 mb-2">

                            <div class="row align-items-center g-0">
                                <div class="col-md-4 text-md-end pe-md-3 mb-1 mb-md-0">
                                    <label class="form-label">Nombre Comercial *</label>
                                </div>
                                <div class="col-md-8">
                                    <input type="text" name="nombre_comercial" id="nombre_comercial"
                                        class="form-control text-uppercase" placeholder="EJ. ABARROTES LA ESPERANZA"
                                        required>
                                </div>
                            </div>

                            <div class="row align-items-center g-0">
                                <div class="col-md-4 text-md-end pe-md-3 mb-1 mb-md-0">
                                    <label class="form-label">Razón Social</label>
                                </div>
                                <div class="col-md-8">
                                    <input type="text" name="razon_social" id="razon_social"
                                        class="form-control text-uppercase" placeholder="NOMBRE LEGAL COMPLETO">
                                </div>
                            </div>

                            <div class="row align-items-center g-0">
                                <div class="col-md-4 text-md-end pe-md-3 mb-1 mb-md-0">
                                    <label class="form-label">Contacto *</label>
                                </div>
                                <div class="col-md-8">
                                    <input type="text" name="contacto" id="contacto" class="form-control text-uppercase"
                                        placeholder="NOMBRE DEL CONTACTO">
                                </div>
                            </div>

                            <div class="row align-items-center g-0">
                                <div class="col-md-4 text-md-end pe-md-3 mb-1 mb-md-0">
                                    <label class="form-label">Correo Electrónico</label>
                                </div>
                                <div class="col-md-8">
                                    <input type="email" name="correo" id="correo" class="form-control"
                                        placeholder="CLIENTE@CORREO.COM">
                                </div>
                            </div>

                            <div class="row align-items-center g-0">
                                <div class="col-md-4 text-md-end pe-md-3 mb-1 mb-md-0">
                                    <label class="form-label">Teléfono</label>
                                </div>
                                <div class="col-md-8">
                                    <input type="tel" name="telefono" id="telefono" class="form-control"
                                        placeholder="55 0000 0000">
                                </div>
                            </div>

                            <div class="row align-items-center g-0">
                                <div class="col-md-4 text-md-end pe-md-3 mb-1 mb-md-0">
                                    <label class="form-label">Código Postal *</label>
                                </div>
                                <div class="col-md-8">
                                    <input type="text" name="codigo_postal" id="codigo_postal"
                                        class="form-control text-uppercase" maxlength="5" placeholder="00000">
                                </div>
                            </div>
                        </div>

                        <!-- ============================================ -->
                        <!-- SECCIÓN 2: DATOS FISCALES                     -->
                        <!-- ============================================ -->
                        <div class="form-section-title">Datos Fiscales</div>

                        <div class="d-flex flex-column gap-2 mb-2">

                            <div class="row align-items-center g-0">
                                <div class="col-md-4 text-md-end pe-md-3 mb-1 mb-md-0">
                                    <label class="form-label">RFC *</label>
                                </div>
                                <div class="col-md-8">
                                    <input type="text" name="rfc" id="rfc" class="form-control text-uppercase"
                                        maxlength="13" placeholder="ABCD000000XXX">
                                </div>
                            </div>

                            <div class="row align-items-center g-0 d-none">
                                <div class="col-md-4 text-md-end pe-md-3 mb-1 mb-md-0">
                                    <label class="form-label">Uso de CFDI</label>
                                </div>
                                <div class="col-md-8">
                                    <select name="uso_cfdi" id="uso_cfdi" class="form-select">
                                        <?php foreach ($usosCFDI as $key => $val): ?>
                                            <option value="<?= $key ?>"><?= $key ?> - <?= $val ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- ============================================ -->
                        <!-- SECCIÓN 3: DIRECCIÓN                          -->
                        <!-- ============================================ -->
                        <div class="form-section-title">Dirección</div>

                        <div class="d-flex flex-column gap-2 mb-2">

                            <div class="row align-items-start g-0">
                                <div class="col-md-4 text-md-end pe-md-3 pt-1 mb-1 mb-md-0">
                                    <label class="form-label">Calle</label>
                                </div>
                                <div class="col-md-8">
                                    <textarea name="calle" id="calle" class="form-control text-uppercase" rows="2"
                                        placeholder="CALLE Y NÚMERO"></textarea>
                                </div>
                            </div>

                            <div class="row align-items-start g-0">
                                <div class="col-md-4 text-md-end pe-md-3 pt-1 mb-1 mb-md-0">
                                    <label class="form-label">Colonia</label>
                                </div>
                                <div class="col-md-8">
                                    <textarea name="colonia" id="colonia" class="form-control text-uppercase" rows="2"
                                        placeholder="COLONIA..."></textarea>
                                </div>
                            </div>

                            <div class="row align-items-start g-0">
                                <div class="col-md-4 text-md-end pe-md-3 pt-1 mb-1 mb-md-0">
                                    <label class="form-label">Población</label>
                                </div>
                                <div class="col-md-8">
                                    <textarea name="pueblo" id="pueblo" class="form-control text-uppercase" rows="2"
                                        placeholder="POBLACION"></textarea>
                                </div>
                            </div>

                            <div class="row align-items-start g-0">
                                <div class="col-md-4 text-md-end pe-md-3 pt-1 mb-1 mb-md-0">
                                    <label class="form-label">Ciudad</label>
                                </div>
                                <div class="col-md-8">
                                    <textarea name="ciudad" id="ciudad" class="form-control text-uppercase" rows="2"
                                        placeholder="CIUDAD"></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- ============================================ -->
                        <!-- SECCIÓN 4: ASIGNACIÓN (solo admin)            -->
                        <!-- ============================================ -->
                        <?php if ($almacen_usuario == 0): ?>
                            <!-- Usamos d-none para ocultar visualmente la sección y el select sin romper el DOM -->
                            <div class="d-none">
                                <div class="form-section-title">Asignación</div>

                                <div class="row align-items-center g-0 mb-2">
                                    <div class="col-md-4 text-md-end pe-md-3 mb-1 mb-md-0">
                                        <label class="form-label text-primary">Asignar a Sucursal *</label>
                                    </div>
                                    <div class="col-md-8">
                                        <!-- Se quitó el 'required' para que no bloquee el envío al estar oculto, y se asigna un valor por defecto o se maneja por backend -->
                                        <select name="almacen_id" id="almacen_id_modal" class="form-select border-primary">
                                            <?php foreach ($almacenes as $alm): ?>
                                                <option value="<?= $alm['id'] ?>">
                                                    <?= htmlspecialchars($alm['nombre']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <!-- Input oculto de respaldo para enviar un ID por defecto si el select está oculto -->
                            <input type="hidden" name="almacen_id" value="1">
                        <?php else: ?>
                            <input type="hidden" name="almacen_id" value="<?= $almacen_usuario ?>">
                        <?php endif; ?>
                    </div>

                    <!-- Footer delicado (siempre visible) -->
                    <div class="modal-footer px-4 py-3 border-0">
                        <button type="button" class="btn btn-light border px-4" data-bs-dismiss="modal">
                            Cancelar
                        </button>
                        <button type="submit" class="btn btn-primary px-4 shadow-sm">
                            Guardar Cliente
                        </button>
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
        let tabla;
        function obtenerOGenerarRFC() {
            let rfc = $('#rfc').val() ? $('#rfc').val().trim() : '';
            let nombre = $('#nombre_comercial').val() ? $('#nombre_comercial').val().trim() : '';

            if (rfc !== '') {
                return rfc.toUpperCase();
            }

            let limpio = nombre
                .normalize("NFD")
                .replace(/[\u0300-\u036f]/g, "")
                .toUpperCase()
                .replace(/[^A-Z\s]/g, "");

            let letras = limpio.replace(/\s+/g, '');

            let rfcGenerado = '';
            if (letras.length >= 4) {
                rfcGenerado = letras.substring(0, 4) + '010101XXX';
            } else if (letras.length > 0) {
                rfcGenerado = letras.padEnd(4, 'X') + '010101XXX';
            } else {
                rfcGenerado = 'XAXX010101000';
            }

            $('#rfc').val(rfcGenerado);
            return rfcGenerado;
        }

        $(document).ready(function () {
            tabla = $('#tablaClientes').DataTable({
                "language": { "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json" },
                "dom": 'rt<"row mt-3 px-3"<"col-sm-12 col-md-5 small text-body-secondary"i><"col-sm-12 col-md-7"p>>',
                "pageLength": 15,
                "order": [[0, 'asc']],
                "autoWidth": false,
                "columnDefs": [
                    { "targets": [4], "orderable": false },
                    { "targets": [2], "visible": <?= ($almacen_usuario == 0) ? 'true' : 'false' ?> }
                ]
            });

            $('#busquedaCliente').on('keyup', function () { tabla.search(this.value).draw(); });

            $('#filtroAlmacenVista').on('change', function () {
                const val = $(this).val();
                $.fn.dataTable.ext.search.pop();
                if (val !== "") {
                    $.fn.dataTable.ext.search.push(function (s, d, i) {
                        return $(tabla.row(i).node()).attr('data-almacen-id') == val;
                    });
                }
                tabla.draw();
            });
        });

        function limpiarFiltros() {
            $('#busquedaCliente').val('');
            $('#filtroAlmacenVista').val('');
            $.fn.dataTable.ext.search.pop();
            tabla.search('').draw();
        }

        function nuevoCliente() {
            $('#formCliente')[0].reset();
            $('#cliente_id').val('0');
            $('#modalTitulo').text('Nuevo Registro de Cliente');

            // Auto-seleccionar almacén si hay filtro activo
            const filtro = $('#filtroAlmacenVista').val();
            if (filtro) $('#almacen_id_modal').val(filtro);

            $('#modalCliente').modal('show');
        }

        async function editarCliente(id) {
            try {
                const resp = await fetch(`clientesController.php?action=obtenerPorId&id=${id}`);
                const res = await resp.json();
                if (res.success) {
                    const c = res.data;
                    $('#modalTitulo').text('Actualizar Cliente');
                    $('#cliente_id').val(c.id);
                    $('#contacto').val(c.contacto);
                    $('#nombre_comercial').val(c.nombre_comercial);
                    $('#razon_social').val(c.razon_social);
                    $('#rfc').val(c.rfc);
                    $('#telefono').val(c.telefono);
                    const direccion = c.direccion || '';

                    const calle = (direccion.match(/calle\s(.*?)(?=,\scol|$)/i) || [, ''])[1];
                    const colonia = (direccion.match(/col\s(.*?)(?=,\spueblo|$)/i) || [, ''])[1];
                    const pueblo = (direccion.match(/pueblo\s(.*?)(?=,\sciudad|$)/i) || [, ''])[1];
                    const ciudad = (direccion.match(/ciudad\s(.*)$/i) || [, ''])[1];

                    $('#calle').val(calle.trim());
                    $('#colonia').val(colonia.trim());
                    $('#pueblo').val(pueblo.trim());
                    $('#ciudad').val(ciudad.trim());
                    $('#correo').val(c.correo);
                    $('#codigo_postal').val(c.codigo_postal);
                    $('#almacen_id_modal').val(c.almacen_id);
                    $('#modalCliente').modal('show');
                }
            } catch (e) { console.error(e); }
        }

        $('#formCliente').on('submit', async function (e) {
            e.preventDefault();
            obtenerOGenerarRFC();
            try {
                const resp = await fetch('clientesController.php?action=guardar', {
                    method: 'POST',
                    body: new FormData(this)
                });
                const res = await resp.json();
                if (res.success) {
                    Swal.fire({ icon: 'success', title: 'Éxito', text: res.message, timer: 1500, showConfirmButton: false })
                        .then(() => location.reload());
                } else {
                    Swal.fire('Error', res.message, 'error');
                }
            } catch (e) { console.error(e); }
        });

        async function cambiarEstado(id, estado) {
            const fd = new FormData();
            fd.append('id', id); fd.append('estado', estado);
            fetch('clientesController.php?action=cambiarEstado', { method: 'POST', body: fd });
        }

        function verDetalles(id) {
            window.location.href = `clientesController.php?action=detalles&id=${id}`;
        }
    </script>

    <script>
        // Convierte a mayúsculas en tiempo real
        document.querySelectorAll('input[type="text"], textarea').forEach(elemento => {
            elemento.addEventListener('input', function () {
                this.value = this.value.toUpperCase();
            });
        });
    </script>
    <!-- ============================================================ -->
    <!-- MODAL DE GESTIÓN DE UBICACIONES DE CLIENTES (Nombres Únicos)   -->
    <!-- ============================================================ -->
    <div class="modal fade" id="cliUbicModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content" style="border-radius: 24px;">

                <div class="modal-header px-4 pt-4 pb-2 border-0">
                    <h5 class="modal-title fw-bold fs-5" id="cliUbicModalTitulo">Sucursales / Ubicaciones del Cliente
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body px-4 py-3">
                    <!-- Información general del cliente actual -->
                    <input type="hidden" id="cliUbicClienteId" value="0">
                    <div
                        class="alert alert-light border mb-3 d-flex justify-content-between align-items-center py-2 px-3 rounded-3">
                        <div>
                            <span class="small text-muted d-block">Cliente seleccionado:</span>
                            <strong id="cliUbicClienteNombre" class="text-primary">--</strong>
                        </div>
                        <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm"
                            onclick="cliUbicAbrirFormularioNuevo()">
                            <i class="bi bi-plus-circle me-1"></i> Nueva Ubicación
                        </button>
                    </div>

                    <!-- Formulario Oculto/Desplegable para Agregar o Editar Ubicación -->
                    <div id="cliUbicContenedorForm" class="card border shadow-none p-3 mb-3 bg-light d-none rounded-4">
                        <h6 class="fw-bold text-secondary mb-3" id="cliUbicFormSubtitulo">Registrar Nueva Ubicación</h6>
                        <form id="cliUbicFormulario">
                            <input type="hidden" name="id" id="cliUbicId" value="0">
                            <input type="hidden" name="cliente_id" id="cliUbicFormClienteId" value="0">

                            <div class="mb-2">
                                <label class="form-label small fw-bold">Nombre / Alias de la Ubicación *</label>
                                <input type="text" name="nombre" id="cliUbicNombreInput"
                                    class="form-control text-uppercase" placeholder="EJ. BODEGA NORTE / SUCURSAL CENTRO"
                                    required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-bold">Dirección Completa *</label>
                                <textarea name="direccion_completa" id="cliUbicDireccionInput"
                                    class="form-control text-uppercase" rows="2"
                                    placeholder="CALLE, NÚMERO, COLONIA, CIUDAD, C.P." required></textarea>
                            </div>

                            <div class="d-flex justify-content-end gap-2">
                                <button type="button" class="btn btn-sm btn-light border px-3"
                                    onclick="cliUbicCancelarForm()">Cancelar</button>
                                <button type="submit" class="btn btn-sm btn-success px-4 shadow-sm">Guardar
                                    Ubicación</button>
                            </div>
                        </form>
                    </div>

                    <!-- Listado de Ubicaciones en Tabla -->
                    <div class="table-responsive">
                        <table class="table table-hover align-middle w-100" id="cliUbicTablaListado">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">Nombre / Alias</th>
                                    <th>Dirección Completa</th>
                                    <th class="text-end pe-3">Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="cliUbicTablaCuerpo">
                                <!-- Se llena dinámicamente vía JS -->
                            </tbody>
                        </table>
                    </div>

                </div>

                <div class="modal-footer px-4 py-3 border-0">
                    <button type="button" class="btn btn-secondary px-4 rounded-3"
                        data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- SCRIPT DE GESTIÓN DE UBICACIONES -->
    <script>
        // 1. Abrir el modal de ubicaciones al recibir el ID del cliente y su nombre
        async function abrirModalUbicaciones(clienteId, nombreCliente) {
            $('#cliUbicClienteId').val(clienteId);
            $('#cliUbicFormClienteId').val(clienteId);
            $('#cliUbicClienteNombre').text(nombreCliente);

            cliUbicCancelarForm();
            await cliUbicCargarTabla(clienteId);

            const modalUbic = new bootstrap.Modal(document.getElementById('cliUbicModal'));
            modalUbic.show();
        }

        // 2. Cargar listado de ubicaciones desde el controlador (Blindado contra errores de formato)
        async function cliUbicCargarTabla(clienteId) {
            const tbody = $('#cliUbicTablaCuerpo');
            tbody.html('<tr><td colspan="3" class="text-center py-3 text-muted">Cargando ubicaciones...</td></tr>');

            try {
                const resp = await fetch(`/cfsistem/app/controllers/clientesController.php?action=listarUbicaciones&cliente_id=${clienteId}`);
                const textResponse = await resp.text();
                console.log("Respuesta cruda [Listar]:", textResponse);

                let res;
                try {
                    res = JSON.parse(textResponse);
                } catch (e) {
                    throw new Error("La respuesta del servidor no es un JSON válido (ver consola).");
                }

                if (res.success && res.data && res.data.length > 0) {
                    let html = '';
                    res.data.forEach(u => {
                        html += `
                        <tr>
                            <td class="ps-3 fw-bold">${escapeHtml(u.nombre)}</td>
                            <td class="text-secondary small">${escapeHtml(u.direccion_completa)}</td>
                            <td class="text-end pe-3">
                                <button class="btn btn-sm btn-outline-primary me-1" onclick="cliUbicEditar(${u.id}, '${escapeHtml(u.nombre).replace(/'/g, "\\'")}', '${escapeHtml(u.direccion_completa).replace(/'/g, "\\'")}')" title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger" onclick="cliUbicEliminar(${u.id}, ${clienteId})" title="Eliminar">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                    `;
                    });
                    tbody.html(html);
                } else {
                    tbody.html('<tr><td colspan="3" class="text-center py-3 text-muted">No hay ubicaciones registradas para este cliente.</td></tr>');
                }
            } catch (error) {
                console.error("Error en cliUbicCargarTabla:", error);
                tbody.html(`<tr><td colspan="3" class="text-center py-3 text-danger">${error.message}</td></tr>`);
            }
        }

        // 3. Mostrar formulario para nueva ubicación
        function cliUbicAbrirFormularioNuevo() {
            $('#cliUbicFormulario')[0].reset();
            $('#cliUbicId').val('0');
            $('#cliUbicFormSubtitulo').text('Registrar Nueva Ubicación');
            $('#cliUbicContenedorForm').removeClass('d-none');
        }

        // 4. Cancelar / Ocultar formulario
        function cliUbicCancelarForm() {
            $('#cliUbicContenedorForm').addClass('d-none');
            $('#cliUbicFormulario')[0].reset();
            $('#cliUbicId').val('0');
        }

        // 5. Preparar formulario para editar ubicación existente
        function cliUbicEditar(id, nombre, direccion) {
            $('#cliUbicId').val(id);
            $('#cliUbicNombreInput').val(nombre);
            $('#cliUbicDireccionInput').val(direccion);
            $('#cliUbicFormSubtitulo').text('Editar Ubicación');
            $('#cliUbicContenedorForm').removeClass('d-none');
            $('#cliUbicContenedorForm')[0].scrollIntoView({ behavior: 'smooth' });
        }

        // 6. Enviar formulario (Guardar o Actualizar) mediante AJAX/Fetch
        $('#cliUbicFormulario').on('submit', async function (e) {
            e.preventDefault();
            const formData = new URLSearchParams(new FormData(this));
            const clienteId = $('#cliUbicClienteId').val();

            try {
                const resp = await fetch(`/cfsistem/app/controllers/clientesController.php?action=guardarUbicacion`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: formData
                });

                const textResponse = await resp.text();
                console.log("Respuesta cruda [Guardar]:", textResponse);

                let res;
                try {
                    res = JSON.parse(textResponse);
                } catch (e) {
                    throw new Error("El servidor no devolvió una respuesta JSON válida.");
                }

                if (res.success) {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Éxito!',
                        text: res.message,
                        timer: 1500,
                        showConfirmButton: false
                    });
                    cliUbicCancelarForm();
                    await cliUbicCargarTabla(clienteId);
                } else {
                    Swal.fire('Error', res.message || 'No se pudo guardar la ubicación.', 'error');
                }
            } catch (error) {
                console.error("Error en submit de ubicación:", error);
                Swal.fire('Error', error.message || 'Ocurrió un error de conexión.', 'error');
            }
        });

        // 7. Eliminar ubicación
        async function cliUbicEliminar(id, clienteId) {
            const confirm = await Swal.fire({
                title: '¿Estás seguro?',
                text: "Esta acción eliminará la sucursal/ubicación de forma permanente.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            });

            if (confirm.isConfirmed) {
                try {
                    const resp = await fetch(`/cfsistem/app/controllers/clientesController.php?action=eliminarUbicacion`, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: `id=${id}`
                    });

                    const textResponse = await resp.text();
                    console.log("Respuesta cruda [Eliminar]:", textResponse);

                    let res;
                    try {
                        res = JSON.parse(textResponse);
                    } catch (e) {
                        throw new Error("El servidor devolvió una respuesta no válida al intentar eliminar.");
                    }

                    if (res.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Eliminado',
                            text: res.message,
                            timer: 1200,
                            showConfirmButton: false
                        });
                        await cliUbicCargarTabla(clienteId);
                    } else {
                        Swal.fire('Error', res.message, 'error');
                    }
                } catch (error) {
                    console.error("Error al eliminar ubicación:", error);
                    Swal.fire('Error', error.message || 'Error al procesar la solicitud.', 'error');
                }
            }
        }

        // Utilidad auxiliar para escapar caracteres HTML de forma segura
        function escapeHtml(text) {
            if (!text) return '';
            return text.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;");
        }
    </script>
</body>

</html>