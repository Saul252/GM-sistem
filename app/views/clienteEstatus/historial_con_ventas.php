<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expediente: <?= htmlspecialchars($cliente['nombre_comercial']) ?> | CF System</title>
    <link rel="icon" type="image/png" href="/cfsistem/public/assets/logo.png">

    <link rel="shortcut icon" href="/cfsistem/public/assets/logo.ico" type="image/x-icon">


    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css"
        rel="stylesheet" />

    <!-- Select2 JS (Colócalo después de jQuery) -->


    <style>
        :root {
            --bs-primary: #007aff;
            --bs-info: #3abaf4;
            --bs-success: #1cc88a;
            --bs-danger: #e74a3b;
            --bs-warning: #f6c23e;
            --bg-light: #f8f9fc;
        }

        body {
            background-color: var(--bg-light);
            font-family: 'Inter', sans-serif;
            color: #4e73df;
        }

        .header-expediente {
            background: white;
            border-bottom: 1px solid #e3e6f0;
            padding: 1rem 2rem;
        }

        /* ===== KPIs: solo tamaños, layout lo hace Bootstrap ===== */
        .kpi-widget {
            background: transparent;
            border: none;
            padding: 0.25rem;
            text-align: center;
        }

        .kpi-label {
            font-size: 9px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            line-height: 1.1;
            margin-bottom: 2px;
            color: #8a92a3;
        }

        .kpi-value {
            font-size: 13px;
            font-weight: 700;
            line-height: 1.2;
            color: #5a5c69;
        }

        .border-left-primary {
            border-left-color: var(--bs-primary) !important;
        }

        .border-left-success {
            border-left-color: var(--bs-success) !important;
        }

        .border-left-danger {
            border-left-color: var(--bs-danger) !important;
        }

        .border-left-info {
            border-left-color: var(--bs-info) !important;
        }

        .folio-container {
            background: white;
            border-radius: 12px;
            border: 1px solid #e3e6f0;
            margin-bottom: 2rem;
            overflow: hidden;
        }

        .folio-debe {
            border-left: 5px solid var(--bs-danger);
        }

        .folio-liquidado {
            border-left: 5px solid var(--bs-success);
            background-color: #f6fff9;
        }

        .folio-favor {
            border-left: 5px solid var(--bs-info);
            background-color: #f0f7ff;
        }

        .folio-cancelado {
            border-left: 5px solid #858796;
            background-color: #f8f9fc;
        }

        .folio-header {
            background-color: rgba(0, 0, 0, 0.02);
            padding: 1rem 1.5rem;
            border-bottom: 1px solid #e3e6f0;
        }

        .col-pagos {
            background-color: #fafbfc;
            border-left: 1px solid #e3e6f0;
            padding: 1.5rem;
        }

        .payment-pill {
            background: white;
            border: 1px solid #e3e6f0;
            border-left: 4px solid var(--bs-success);
            border-radius: 8px;
            padding: 10px;
            margin-bottom: 8px;
        }

        @media (min-width: 992px) {
            .w-lg-60 {
                width: 60% !important;
            }
        }

        /* ===== Fila de productos (fila intercalada) ===== */
        .productos-row {
            background: transparent;
        }

        .productos-row>td {
            padding: 0 !important;
            border-top: none !important;
        }

        .table-hover>tbody>tr.productos-row:hover>td {
            background-color: transparent !important;
        }

        /* ===== Contenedor de productos ===== */
        .productos-container {
            padding: 0.4rem 1rem 0.75rem 1rem;
            background: #fafbfc;
            border-left: 3px solid #007aff;
            border-radius: 0 0 8px 8px;
            margin: 0 0.5rem 0.75rem 0.5rem;
        }

        .productos-container table {
            font-size: 12.5px;
            width: 100%;
            table-layout: fixed;
            margin-bottom: 0;
        }

        .productos-container thead th {
            font-size: 10px;
            text-transform: uppercase;
            color: #5a6272;
            font-weight: 700;
            letter-spacing: 0.4px;
            border-bottom: 1px solid #e3e6f0;
            padding: 6px 10px;
            background: #f3f6fa;
        }

        .productos-container tbody td {
            font-size: 12.5px;
            padding: 6px 10px;
            border-bottom: 1px solid #f0f1f5;
            vertical-align: middle;
        }

        .productos-container tbody tr:last-child td {
            border-bottom: none;
        }

        .productos-container tbody td b {
            font-size: 13px;
        }

        .productos-container tbody td small {
            font-size: 10.5px;
        }

        .productos-container .badge {
            font-size: 10px;
            padding: 3px 8px;
        }

        /* Alineación por columna de productos (6 columnas) */
        .productos-container thead th:nth-child(1),
        .productos-container tbody td:nth-child(1) {
            width: 36%;
            text-align: left;
        }

        .productos-container thead th:nth-child(2),
        .productos-container tbody td:nth-child(2) {
            width: 15%;
            text-align: center;
        }

        .productos-container thead th:nth-child(3),
        .productos-container tbody td:nth-child(3) {
            width: 13%;
            text-align: right;
        }

        .productos-container thead th:nth-child(4),
        .productos-container tbody td:nth-child(4) {
            width: 14%;
            text-align: center;
        }

        .productos-container thead th:nth-child(5),
        .productos-container tbody td:nth-child(5) {
            width: 12%;
            text-align: center;
        }

        .productos-container thead th:nth-child(6),
        .productos-container tbody td:nth-child(6) {
            width: 10%;
            text-align: center;
        }

        /* Separador visual entre cada venta */
        .venta-row>td {
            border-bottom: none !important;
        }

        .venta-row:not(:first-child)>td {
            border-top: 2px solid #e3e6f0 !important;
        }

        /* ===== Card de Folios (estilo impresión) ===== */
        .card.w-lg-60 {
            background: transparent;
            box-shadow: none !important;
            padding: 0;
        }

        .card.w-lg-60>h5 {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.7px;
            color: #007aff;
            text-align: center;
            padding-bottom: 4px;
            border-bottom: 1px solid #dfe3ea;
            margin: 12px auto 6px;
            max-width: 100%;
            font-weight: 800;
        }

        .card.w-lg-60 .table {
            font-size: 11px;
        }

        .card.w-lg-60 .table thead th {
            font-size: 9px;
            padding: 5px 10px;
            background: #f3f6fa;
            color: #5a6272;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            font-weight: 700;
            border-bottom: 1px solid #e9edf3;
        }

        .card.w-lg-60 .table tbody td {
            padding: 5px 10px;
        }

        .card.w-lg-60 .table tbody tr.venta-row:not(:first-child)>td {
            border-top: 1px solid #dfe3ea !important;
        }

        .card.w-lg-60 .table tbody tr.venta-row>td {
            border-bottom: none !important;
        }

        .card.w-lg-60 .table tbody tr.productos-row>td {
            padding: 0 !important;
            border-top: none !important;
            border-bottom: none !important;
        }

        /* ===== Banner saldo a favor compacto ===== */
        @media (min-width: 992px) {
            .alert.alert-info {
                max-width: 60%;
                margin-left: auto;
                margin-right: auto;
            }
        }

        .alert.alert-info {
            box-shadow: none;
            border-radius: 6px;
            padding: 10px 14px;
            font-size: 11px;
        }

        .alert.alert-info h5 {
            font-size: 12px;
        }

        .alert.alert-info .bi-info-square-fill {
            font-size: 1.5rem;
        }
    </style>
</head>

<body>

    <header class="header-expediente shadow-sm mb-4">
        <div class="container-fluid d-flex align-items-center flex-wrap gap-2">

            <!-- Título + RFC -->
            <div class="d-flex align-items-center gap-2 me-auto">
                <h5 class="fw-bold mb-0 text-dark text-nowrap">
                    <?= htmlspecialchars($cliente['nombre_comercial']) ?>
                </h5>
                <span class="badge bg-primary-subtle text-primary rounded-pill px-2 py-1 fw-semibold">
                    RFC:
                    <?= htmlspecialchars($cliente['rfc']) ?>
                </span>
            </div>

            <?php
            date_default_timezone_set('America/Mexico_City');
            $fechaInicio = $_GET['fecha_inicio'] ?? date('Y-m-01');
            $fechaFin = $_GET['fecha_fin'] ?? date('Y-m-t');
            ?>

            <!-- Cliente -->
            <div style="min-width: 200px;">
                <select name="cliente_id_editar" id="cliente_id_editar"
                    class="form-select form-select-sm select2-pagina" onchange="filtrarExpediente()" required>
                </select>
            </div>

            <!-- Desde -->
            <div>
                <input type="date" id="fecha_inicio" class="form-control form-control-sm"
                    value="<?= htmlspecialchars($fechaInicio) ?>" title="Fecha inicio">
            </div>

            <!-- Hasta -->
            <div>
                <input type="date" id="fecha_fin" class="form-control form-control-sm"
                    value="<?= htmlspecialchars($fechaFin) ?>" title="Fecha fin">
            </div>

            <!-- Botones -->
            <button class="btn btn-primary btn-sm px-3" onclick="filtrarExpediente()" title="Filtrar">
                <i class="bi bi-funnel"></i>
            </button>
            <button class="btn btn-dark btn-sm px-3" onclick="imprimirEstadoCuenta()" title="Imprimir">
                <i class="bi bi-printer"></i>
            </button>

        </div>
    </header>


    <div class="container-fluid px-4">

        <?php if ($resumen['saldo_total'] < -0.01): ?>
            <div class="alert alert-info border-0 mb-4 d-flex align-items-center p-3" style="border-radius: 6px;">
                <i class="bi bi-info-square-fill fs-3 me-3 text-info"></i>
                <div>
                    <h5 class="mb-0 fw-bold">Saldo a Favor General</h5>
                    <span>El cliente tiene <b>$ <?= number_format(abs($resumen['saldo_total']), 2) ?></b> disponible.</span>
                </div>
            </div>
        <?php endif; ?>
        <div class="d-flex justify-content-center align-items-center flex-wrap gap-4 mb-4">
            <div class="kpi-widget border-left-primary text-center">
                <div class="kpi-label text-uppercase text-secondary fw-bold">Compras Totales</div>
                <div class="kpi-value fw-bold text-primary">$ <?= number_format($resumen['total_comprado'], 2) ?></div>
            </div>

            <div class="kpi-widget border-left-success text-center">
                <div class="kpi-label text-uppercase text-secondary fw-bold">Total Pagado</div>
                <div class="kpi-value fw-bold text-success">$ <?= number_format($resumen['total_pagado'], 2) ?></div>
            </div>

            <?php
            $saldoReal = $resumen['saldo_total'];
            $esSaldoAFavor = $saldoReal < -0.01;
            $claseColor = $esSaldoAFavor ? 'border-left-info' : 'border-left-danger';
            ?>

            <div class="kpi-widget <?= $claseColor ?> text-center">
                <div class="kpi-label text-uppercase text-secondary fw-bold">
                    <?= $esSaldoAFavor ? 'A Favor' : 'Saldo Pendiente' ?>
                </div>
                <div class="kpi-value fw-bold <?= $esSaldoAFavor ? 'text-info' : 'text-danger' ?>">
                    $ <?= number_format(abs($saldoReal), 2) ?>
                </div>
            </div>


        </div>


        <div class="d-flex justify-content-center px-2">
            <div class="card border-0 mb-3 w-100 w-lg-60">


                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
                            <tr>
                                <th scope="col" class="ps-3">Fecha</th>
                                <th scope="col">Folio </th>
                                <th scope="col" class="text-end">Total</th>
                                <th scope="col" class="text-end">Pagado</th>
                                <th scope="col" class="text-end">Saldo</th>

                                <th scope="col" class="text-center pe-3">Opciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($expediente as $v):
                                $idActual = $v['venta_id'] ?? $v['id'];
                                $esCancelada = (isset($v['estado_general']) && $v['estado_general'] == 'cancelada');
                                $saldoFolio = floatval($v['total']) - floatval($v['total_pagado']);
                                $folioLiquidado = abs($saldoFolio) <= 0.01;
                                $folioAFavorIndividual = $saldoFolio < -0.01;
                                ?>
                                <!-- FILA PRINCIPAL DE LA VENTA -->
                                <tr class="venta-row p-4">
                                    <td class="ps-3">
                                        <small
                                            class="text-body-secondary"><?= date('d/m/Y', strtotime($v['fecha'])) ?></small>
                                    </td>

                                    <td>
                                        <span class="badge bg-light text-dark border">
                                            <?= htmlspecialchars($v['folio']) ?>
                                        </span>
                                    </td>

                                    <td class="text-end fw-semibold text-dark">
                                        $<?= number_format($v['total'], 2) ?>
                                    </td>

                                    <td class="text-end fw-semibold text-success">
                                        $<?= number_format($v['total_pagado'], 2) ?>
                                    </td>

                                    <td class="text-end fw-bold <?= $saldoFolio > 0 ? 'text-danger' : 'text-info' ?>">
                                        $<?= number_format(abs($saldoFolio), 2) ?>
                                    </td>



                                    <td class="text-center pe-3">
                                        <div class="d-flex gap-1 justify-content-center">
                                            <a class="btn btn-outline-primary btn-sm"
                                                href="/cfsistem/app/backend/ventas/ticket_venta.php?id=<?= $idActual ?>"
                                                target="_blank" title="Ticket">
                                                <i class="bi bi-receipt"></i>
                                            </a>
                                            <a class="btn btn-outline-secondary btn-sm"
                                                href="/cfsistem/app/backend/ventas/ticketFormal.php?id=<?= $idActual ?>"
                                                target="_blank" title="Remisión">
                                                <i class="bi bi-file-earmark-text"></i>
                                            </a>

                                        </div>
                                    </td>
                                </tr>

                                <!-- FILA DE PRODUCTOS (se llena automáticamente vía JS) -->
                                <tr class="productos-row" id="productos-row-<?= $idActual ?>">
                                    <td colspan="7">
                                        <div class="productos-container" data-venta-id="<?= $idActual ?>">
                                            <div class="text-center py-2">
                                                <div class="spinner-border spinner-border-sm text-primary"></div>
                                                <small class="text-muted ms-2">Cargando productos...</small>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>


        </div>

        <!-- 2. Despúes carga tu archivo modal.js -->
        <!-- ✅ jQuery PRIMERO -->
        <script src="https://code.jquery.com/jquery-3.7.0.js"></script>

        <!-- ✅ Select2 DESPUÉS -->
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

        <!-- ✅ Otros que dependan de jQuery -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <?php require_once __DIR__ . '/../ventasHistorialModales/registarAbonoCliente.php' ?>


        <script>
            cargarClientes();
            // CALCULAR TOTAL EDITAR
            // =====================================================
            async function cargarClientes() {
                console.log("cargo clientes");

                const select = document.getElementById('cliente_id_editar');
                if (!select) return;

                // 🔥 1. Capturamos el ID del cliente desde la URL
                const urlParams = new URLSearchParams(window.location.search);
                const clienteIdUrl = urlParams.get('id');
                console.log('[cargarClientes] ID desde URL:', clienteIdUrl);

                try {
                    const url = '/cfsistem/app/controllers/accesoController.php?action=obtenerClientes';
                    const respuesta = await fetch(url);

                    if (!respuesta.ok) throw new Error('Error en la respuesta del servidor');

                    const resultado = await respuesta.json();
                    console.log(resultado);

                    if (resultado.success && Array.isArray(resultado.data)) {

                        const clientes = resultado.data;

                        // 🔥 2. Construir y vaciar opciones en el select nativo primero
                        const fragment = document.createDocumentFragment();

                        const first = document.createElement('option');
                        first.value = '';
                        first.textContent = '-- Seleccione un cliente --';
                        fragment.appendChild(first);

                        clientes.forEach(cliente => {
                            const opcion = document.createElement('option');
                            opcion.value = cliente.id;
                            opcion.textContent = String(cliente.nombre_comercial || '').toUpperCase();

                            opcion.dataset.nombre = String(cliente.nombre_comercial || '');
                            opcion.dataset.razonSocial = String(cliente.razon_social || '');
                            opcion.dataset.contacto = String(cliente.contacto || '');

                            fragment.appendChild(opcion);
                        });

                        select.replaceChildren(fragment);

                        // 🔥 3. Destruir Select2 si ya estaba inicializado previamente
                        if ($(select).hasClass('select2-hidden-accessible')) {
                            $(select).select2('destroy');
                        }

                        // 🔥 4. Inicializar Select2 con el DOM ya completamente poblado
                        $(select).select2({
                            theme: 'bootstrap-5',
                            width: '100%',
                            dropdownParent: $(select).parent(),
                            matcher: function (params, data) {
                                if (!params.term || params.term.trim() === '') return data;

                                const term = String(params.term).toLowerCase().trim();
                                const text = String(data.text || '').toLowerCase();

                                const $el = $(data.element);
                                const razonSocial = String($el.data('razonSocial') || '').toLowerCase();
                                const contacto = String($el.data('contacto') || '').toLowerCase();

                                if (text.includes(term) || razonSocial.includes(term) || contacto.includes(term)) {
                                    return data;
                                }

                                return null;
                            },
                            templateResult: function (state) {
                                if (!state.id) return state.text;

                                const $opt = $(state.element);
                                const nombre = String($opt.data('nombre') || state.text || '');
                                const razonSocial = String($opt.data('razonSocial') || '');
                                const contacto = String($opt.data('contacto') || '');

                                if (!razonSocial && !contacto) {
                                    return $(`<span class="fw-semibold">${nombre.toUpperCase()}</span>`);
                                }

                                return $(`
                        <div class="d-flex flex-column">
                            <span class="fw-semibold">${nombre.toUpperCase()}</span>
                            <div class="d-flex justify-content-between align-items-center gap-2 mt-1">
                                <small class="text-truncate" style="max-width:65%;">${razonSocial.toUpperCase()}</small>
                                <small class="fw-semibold text-nowrap">${contacto ? `👤 ${contacto.toUpperCase()}` : ''}</small>
                            </div>
                        </div>
                    `);
                            },
                            templateSelection: function (state) {
                                if (!state.id) return state.text;
                                const nombre = String($(state.element).data('nombre') || state.text || '');
                                return nombre.toUpperCase();
                            }
                        });

                        // 🔥 5. Validar la coincidencia con el ID de la URL una vez cargado y pintado todo el sistema
                        const targetId = clienteIdUrl ? String(clienteIdUrl).trim() : null;

                        if (targetId) {
                            let coincide = false;

                            Array.from(select.options).forEach(opt => {
                                if (String(opt.value).trim() === targetId) {
                                    coincide = true;
                                }
                            });

                            if (coincide) {
                                console.log("coincidencia:", targetId);
                                // Asignamos el valor directamente y actualizamos Select2
                                $(select).val(targetId).trigger('change.select2');
                                console.log('[cargarClientes] Selección final:', $(select).val());
                            } else {
                                console.warn('[cargarClientes] Cliente id', targetId, 'no está en la lista.');
                            }
                        }

                    } else {
                        select.innerHTML = '<option value="">No se pudieron cargar los usuarios</option>';
                        $(select).trigger('change.select2');
                    }
                } catch (error) {
                    select.innerHTML = '<option value="">Error al cargar la lista</option>';
                    $(select).trigger('change.select2');
                    console.error('Error al ejecutar cargarClientes:', error);
                }
            }
            let ventaActual = null;
            // La ruta al controlador (ajusta si el nombre del archivo varía)
            const URL_CONTROLLER = '/cfsistem/app/controllers/ventasHistorialController.php';
            $(document).ready(function () {
                renderCharts();
            });

            function renderCharts() {
                const ctx = document.getElementById('chartDona');
                if (!ctx) return;

                new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        datasets: [{
                            data: [
                                <?= isset($resumen['total_pagado']) ? floatval($resumen['total_pagado']) : 0 ?>,
                                <?= isset($resumen['saldo_total']) ? max(0, floatval($resumen['saldo_total'])) : 0 ?>
                            ],
                            backgroundColor: ['#1cc88a', '#e74a3b'],
                            borderWidth: 0
                        }]
                    },
                    options: {
                        cutout: '75%',
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        responsive: true,
                        maintainAspectRatio: false
                    }
                });
            }

        </script>

        <script>
            function filtrarExpediente() {
                const fechaInicio = document.getElementById('fecha_inicio').value;
                const fechaFin = document.getElementById('fecha_fin').value;

                const urlParams = new URLSearchParams(window.location.search);

                const id = $('#cliente_id_editar').val();

                console.log("ID:", id);

                // REDIRECCIÓN
                window.location.href =
                    `/cfsistem/app/controllers/expedienteClienteProdutosController.php?id=${id}&fecha_inicio=${fechaInicio}&fecha_fin=${fechaFin}`;


            }
        </script>
        <script>
            function togglePerso() {
                $('#div_p').toggleClass('d-none', $('#f_rango').val() !== 'personalizado');
                getVentas();
            }
        </script>

        <!-- ======================================================= -->
        <!-- CARGA AUTOMÁTICA DE PRODUCTOS EN CADA FILA DE VENTA     -->
        <!-- ======================================================= -->
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                cargarProductosDeTodasLasVentas();
            });

            async function cargarProductosDeTodasLasVentas() {
                const contenedores = document.querySelectorAll('.productos-container[data-venta-id]');

                // Carga escalonada para no saturar el servidor
                const tareas = Array.from(contenedores).map((contenedor, index) =>
                    (async () => {
                        await new Promise(r => setTimeout(r, index * 40));
                        const ventaId = contenedor.dataset.ventaId;
                        if (!ventaId) return;

                        try {
                            const res = await fetch(
                                `/cfsistem/app/controllers/historialPedidosVendedorController.php?action=obtenerDetalle&id=${ventaId}`
                            );
                            const data = await res.json();
                            contenedor.innerHTML = renderProductosInline(data.productos);
                        } catch (e) {
                            console.error('Error cargando productos de venta', ventaId, e);
                            contenedor.innerHTML =
                                '<div class="text-danger small p-2"><i class="bi bi-exclamation-triangle"></i> No se pudieron cargar los productos.</div>';
                        }
                    })()
                );

                await Promise.all(tareas);
            }

            function renderProductosInline(productos) {
                if (!productos || productos.length === 0) {
                    return '<div class="text-muted small p-2"><i class="bi bi-inbox"></i> Sin productos registrados</div>';
                }
                console.log(productos);

                const rows = productos.map(p => {
                    const cant = parseFloat(p.cantidad) || 0;
                    const entregada = parseFloat(p.cantidad_entregada) || 0;
                    const pendiente = Math.max(0, cant - entregada);
                    const factor = parseFloat(p.factor_conversion) || 1;
                    const uRep = p.unidad_reporte || p.unidad_medida || '';
                    const uMed = p.unidad_medida || '';

                    // Cálculo del costo total por producto (Cantidad * Precio Unitario)
                    const precioVenta = parseFloat(p.precio_unitario
                    ) || 0;
                    const costoTotal = cant * precioVenta;
                    const costo = `$${costoTotal.toFixed(2)}`;

                    // ---- Vendido ----
                    let vendidoTxt;
                    if (factor > 1 && cant >= factor) {
                        const um = cant / factor;
                        vendidoTxt = `<b>${Number.isInteger(um) ? um : um.toFixed(2)}</b> ${uRep}
                          <small class="text-muted">(${cant} ${uMed})</small>`;
                    } else {
                        vendidoTxt = `${cant} ${uMed}`;
                    }

                    // ---- Entregado ----
                    let entregadoTxt;
                    if (factor > 1 && entregada >= factor) {
                        const um = entregada / factor;
                        entregadoTxt = `<b>${Number.isInteger(um) ? um : um.toFixed(2)}</b> ${uRep}
                            <small class="text-muted">(${entregada} ${uMed})</small>`;
                    } else {
                        entregadoTxt = `${entregada} ${uMed}`;
                    }

                    // ---- Pendiente ----
                    let pendienteTxt;
                    if (factor > 1 && pendiente >= factor) {
                        const um = pendiente / factor;
                        pendienteTxt = `<b>${Number.isInteger(um) ? um : um.toFixed(2)}</b> ${uRep}
                            <small class="text-muted">(${pendiente.toFixed(2)} ${uMed})</small>`;
                    } else {
                        pendienteTxt = `${pendiente.toFixed(2)} ${uMed}`;
                    }

                    // ---- Estado ----
                    let estado;
                    if (pendiente <= 0.001) {
                        estado = '<span class="badge bg-success">Entregado</span>';
                    } else if (entregada > 0.001) {
                        estado = '<span class="badge bg-warning text-dark">Parcial</span>';
                    } else {
                        estado = '<span class="badge bg-secondary">Pendiente</span>';
                    }

                    return `<tr>
            <td class="fw-semibold text-dark">${p.producto}</td>
            <td class="text-center">${vendidoTxt}</td>
             <td class="text-center fw-bold text-dark">${costo}</td>
            <td class="text-center text-success">${entregadoTxt}</td>
            <td class="text-center ${pendiente > 0.001 ? 'text-danger fw-bold' : 'text-success fw-semibold'}">${pendienteTxt}</td>
            <td class="text-center">${estado}</td>
           
        </tr>`;
                }).join('');

                return `
       
        <table class="table table-sm table-borderless mb-0">
            <thead>
                <tr>
                    <th>Producto</th>
                    <th class="text-center">Vendido</th>
                     <th class="text-center">Costo</th>
                    <th class="text-center">Entregado</th>
                    <th class="text-center">Pendiente</th>
                    <th class="text-center">Estado</th>
                   
                </tr>
            </thead>
            <tbody>${rows}</tbody>
        </table>
    `;
            } 
        </script>
        <script>


            function imprimirEstadoCuenta() {
                // ─────────────────────────────────────────────────
                // 1. EXTRAER DATOS DEL DOM
                // ─────────────────────────────────────────────────
                const header = document.querySelector('.header-expediente');
                const cliente = header.querySelector('h4, h5')?.textContent.trim() || '';
                const rfc = header.querySelector('.badge')?.textContent.replace(/RFC:\s*/i, '').trim() || '';

                const fechaInicio = document.getElementById('fecha_inicio')?.value || '';
                const fechaFin = document.getElementById('fecha_fin')?.value || '';

                const _fmt = (s) => {
                    if (!s) return '';
                    const p = s.split('-');
                    return p.length === 3 ? `${p[2]}/${p[1]}/${p[0]}` : s;
                };

                // ── Ventas + productos ──
                const ventas = [];
                document.querySelectorAll('.venta-row').forEach((row) => {
                    const tds = row.querySelectorAll('td');
                    if (tds.length < 6) return;

                    const badge = tds[5].querySelector('.badge');
                    let estadoClass = '';
                    if (badge) {
                        if (badge.classList.contains('bg-success')) estadoClass = 'liquidado';
                        else if (badge.classList.contains('bg-danger')) estadoClass = 'pendiente';
                        else if (badge.classList.contains('bg-info')) estadoClass = 'favor';
                        else if (badge.classList.contains('bg-secondary')) estadoClass = 'cancelada';
                    }

                    const venta = {
                        fecha: tds[0].textContent.trim(),
                        folio: tds[1].textContent.trim(),
                        total: tds[2].textContent.trim(),
                        pagado: tds[3].textContent.trim(),
                        saldo: tds[4].textContent.trim(),
                        estado: badge ? badge.textContent.trim() : '',
                        estadoClass,
                        productos: []
                    };

                    const productosRow = row.nextElementSibling;
                    if (productosRow && productosRow.classList.contains('productos-row')) {
                        const cont = productosRow.querySelector('.productos-container');
                        cont?.querySelectorAll('tbody tr').forEach((tr) => {
                            const c = tr.querySelectorAll('td');
                            if (c.length < 6) return;
                            venta.productos.push({
                                producto: c[0].querySelector('.fw-semibold')?.textContent.trim() || c[0].textContent.trim(),
                                vendido: c[1].innerHTML.trim(),
                                costo: c[2].innerHTML.trim(),
                                entregado: c[3].innerHTML.trim(),
                                pendiente: c[4].innerHTML.trim(),
                                estado: c[5].textContent.trim()
                            });
                        });
                    }
                    ventas.push(venta);
                });

                // ── Saldo a favor (banner) ──
                const alertFavor = document.querySelector('.alert.alert-info');
                const saldoFavorMsg = alertFavor ? alertFavor.textContent.replace(/\s+/g, ' ').trim() : '';

                // ─────────────────────────────────────────────────
                // 2. GENERAR HTML BASE (SIN script de auto-print)
                // ─────────────────────────────────────────────────
                const fechaGen = new Date().toLocaleString('es-MX', { dateStyle: 'long', timeStyle: 'short' });

                const ventasHTML = ventas.map(v => {
                    const productosHTML = v.productos.length ? `
            <table class="tbl-prod">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th class="tc">Vendido</th>
                        <th class="tc">Costo</th>
                    </tr>
                </thead>
                <tbody>
                    ${v.productos.map(p => `
                        <tr>
                            <td class="prod-name">${p.producto}</td>
                            <td class="tc">${p.vendido}</td>
                            <td class="tc">${p.costo}</td>
                        </tr>
                    `).join('')}
                </tbody>
            </table>` : `<div class="empty">Sin productos registrados</div>`;

                    const saldoClass = v.estadoClass === 'favor'
                        ? 'c-info'
                        : (v.estadoClass === 'liquidado' ? 'c-success' : 'c-danger');

                    const estadoExtra = v.estadoClass === 'cancelada'
                        ? ' <span class="tag-cancel">CANCELADA</span>'
                        : '';

                    return `
        <div class="folio-block">
            <div class="folio-line">
                <div class="folio-info">
                    <span class="folio-num">#${v.folio}</span>
                    <span class="folio-fecha">${v.fecha}${estadoExtra}</span>
                </div>
                <div class="folio-nums">
                    <span>Pagado: <b class="c-success">${v.pagado}</b></span>
                    <span>Deuda: <b class="${saldoClass}">${v.saldo}</b></span>
                </div>
            </div>
            ${productosHTML}
        </div>`;
                }).join('');

                const htmlBase = `<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Estado de Cuenta · ${cliente}</title>
<style>
    *,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
    body{font-family:'Segoe UI',system-ui,-apple-system,sans-serif;color:#2c3038;background:#fff;
         font-size:12px;line-height:1.45;padding:26px 32px}

    .banner-favor{background:#e8f5fd;border-left:4px solid #3abaf4;color:#1a7fa4;
                  padding:10px 14px;border-radius:6px;margin:0 auto 12px;max-width:60%;
                  font-size:11px;font-weight:600}

    .section-title{font-size:11px;font-weight:800;color:#007aff;text-transform:uppercase;
                   letter-spacing:.7px;text-align:center;margin:12px auto 6px;max-width:60%;
                   padding-bottom:4px;border-bottom:1px solid #dfe3ea}

    .folio-block{max-width:60%;margin:0 auto 6px;page-break-inside:avoid;break-inside:avoid}
    .folio-line{display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;
                padding:4px 2px;border-bottom:1px solid #cfd5e0;margin-bottom:3px}
    .folio-info{display:flex;align-items:baseline;gap:10px}
    .folio-num{font-weight:700;color:#1b1f27;font-size:12px}
    .folio-fecha{color:#8a92a3;font-size:10px}
    .folio-nums{display:flex;gap:18px;font-size:11px;color:#6b7280}
    .folio-nums b{font-weight:700;font-size:11.5px}
    .tag-cancel{background:#e8e9ee;color:#4e5665;padding:1px 7px;border-radius:8px;
                font-size:8.5px;font-weight:800;letter-spacing:.6px;margin-left:4px}

    .c-success{color:#1cc88a!important}
    .c-danger {color:#e74a3b!important}
    .c-info   {color:#3abaf4!important}

    .tbl-prod{width:100%;table-layout:fixed;border-collapse:collapse;font-size:10.5px;background:#fff;
              border:1px solid #e9edf3;border-radius:5px;overflow:hidden}
    .tbl-prod thead th{background:#f3f6fa;padding:5px 10px;font-size:9px;
                       text-transform:uppercase;letter-spacing:.5px;color:#5a6272;font-weight:700}
    .tbl-prod tbody td{padding:5px 10px;border-top:1px solid #f0f2f7;color:#495261;vertical-align:middle}
    .tbl-prod tbody tr:first-child td{border-top:none}
    .prod-name{font-weight:600;color:#1b1f27}
    .tc{text-align:center}
    .empty{padding:10px;color:#9aa1af;font-style:italic;text-align:center;font-size:10.5px}

    .tbl-prod th:first-child,
    .tbl-prod td:first-child{ width:45%; text-align:left; }
    .tbl-prod th:nth-child(2),
    .tbl-prod td:nth-child(2){ width:30%; text-align:center; white-space:nowrap; }
    .tbl-prod th:nth-child(3),
    .tbl-prod td:nth-child(3){ width:25%; text-align:right;  white-space:nowrap; }

    b{font-weight:700;color:#1b1f27}
    small{font-size:.85em}
    .text-muted{color:#8a92a3}
    .text-danger{color:#e74a3b}
    .text-success{color:#1cc88a}
    .text-primary{color:#007aff}
    .badge{display:inline-block;padding:2px 8px;border-radius:10px;font-size:9px;font-weight:700}
    .badge.bg-success   {background:#d4f5e5;color:#0d7a4e}
    .badge.bg-warning   {background:#fff3cd;color:#7a5e00}
    .badge.bg-secondary{background:#e8e9ee;color:#4e5665}
    .badge.bg-danger   {background:#fbe0dd;color:#a32b1e}
    .badge.bg-info     {background:#d4effc;color:#1a7fa4}

    .footer{margin-top:16px;padding-top:10px;border-top:1px solid #dfe3ea;
            text-align:center;font-size:9px;color:#9aa1af}
    .footer strong{color:#007aff}

    /* 🔥 Eliminados los bordes por defecto del navegador al imprimir */
    @page{margin: 0;}
    @media print{
        body{padding: 12mm; font-size: 11px}
        .folio-block{page-break-inside:avoid}
    }
</style>
</head>
<body>

<header class="head">
    <style>
        .head {
            background: #ffffff;
            border: none; /* 🔥 Sin bordes exteriores */
            border-bottom: 2px solid #cbd5e1; /* Solo una línea abajo */
            padding: 10px 0;
            font-family: system-ui, -apple-system, sans-serif;
            font-size: 0.85rem;
            color: #334155;
            margin-bottom: 16px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            flex-wrap: wrap;
        }
        .head__brand {
            display: flex;
            align-items: baseline;
            gap: 10px;
        }
        .head__brand .empresa {
            font-weight: 700;
            color: #0f172a;
            font-size: 0.9rem;
            text-transform: uppercase;
        }
        .head__brand .titulo {
            color: #64748b;
            font-weight: 400;
        }
        .head__info {
            display: flex;
            gap: 16px;
            flex-wrap: wrap;
            align-items: center;
            color: #475569;
        }
        .head__info span strong {
            color: #1e293b;
            font-weight: 600;
        }
    </style>

    <div class="head__brand">
        <span class="empresa">BODEGA FORTALEZA CENTRO</span>
        <span class="titulo">/ Estado de Cuenta</span>
    </div>

    <div class="head__info">
        <span class="cliente"><strong>Cliente:</strong> ${cliente}</span>
        <span class="rfc"><strong>RFC:</strong> ${rfc}</span>
        <span class="periodo"><strong>Periodo:</strong> ${_fmt(fechaInicio)} &mdash; ${_fmt(fechaFin)}</span>
    </div>
</header>
 <img src="/cfsistem/public/assets/logo.ico" style="
                        position: fixed;
                        top: 19.5%;
                        left: 50%;
                        transform: translate(-50%, -50%);
                        width: 180px;
                        opacity: 0.08;
                        z-index: -1;
                    ">

${saldoFavorMsg ? `<div class="banner-favor">💰 ${saldoFavorMsg}</div>` : ''}

${ventasHTML || '<div class="empty">No hay folios en este periodo</div>'}

<footer class="footer">
    Documento generado el ${fechaGen} &middot; <strong>CF SYSTEM</strong>
</footer>

</body>
</html>`;

                // ─────────────────────────────────────────────────
                // 3. DETECTAR DISPOSITIVO Y EJECUTAR
                // ─────────────────────────────────────────────────
                const esMovil = /Android|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);

                if (esMovil) {
                    const autoScript = `
<script>
    window.addEventListener('load', () => {
        setTimeout(() => {
            const s = document.createElement('script');
            s.src = 'https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js';
            s.onload = () => html2pdf().set({
                margin: 1,
                filename: 'estado_cuenta.pdf',
                image: { type: 'jpeg', quality: 0.98 },
                html2canvas: { scale: 2, useCORS: true },
                jsPDF: { unit: 'cm', format: 'letter', orientation: 'portrait' }
            }).from(document.body).save();
            document.head.appendChild(s);
        }, 400);
    });
<\/script>`;

                    const htmlFull = htmlBase.replace('</body>', autoScript + '</body>');

                    const win = window.open('', '_blank');
                    win.document.write(htmlFull);
                    win.document.close();

                } else {
                    const prev = document.getElementById('printFrameExpediente');
                    if (prev) prev.remove();

                    const iframe = document.createElement('iframe');
                    iframe.id = 'printFrameExpediente';
                    iframe.style.cssText = 'position:fixed;right:0;bottom:0;width:0;height:0;border:0;visibility:hidden;';
                    document.body.appendChild(iframe);

                    const doc = iframe.contentWindow.document;
                    doc.open();
                    doc.write(htmlBase);
                    doc.close();

                    setTimeout(() => {
                        try {
                            iframe.contentWindow.focus();
                            iframe.contentWindow.print();
                        } catch (e) {
                            console.error('Error al imprimir desde iframe:', e);
                        }
                        setTimeout(() => iframe.remove(), 2000);
                    }, 600);
                }
            }



        </script>

    </div>
</body>

</html>