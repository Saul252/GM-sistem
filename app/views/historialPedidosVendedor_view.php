<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Entregas | Sistema</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <?php require_once __DIR__ . '/layout/icono.php' ?>
    <?php if (function_exists('cargarEstilos')) { cargarEstilos(); } ?>


    <style>
        :root {
    --primary: #1f2937;
    --secondary: #374151;
    --bg: #f3f4f6;
    --card: #ffffff;
    --accent: #3b82f6;
}

body {
    background: var(--bg);
}

/* Contenedor tipo card moderno */
.scroll-table {
    background: var(--card);
    border-radius: 14px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.06);
    overflow: hidden;
    border: none;
}

/* Scroll interno más limpio */
.table-responsive {
    max-height: 60vh;
}

/* Tabla base */
.table {
    margin: 0;
    border-collapse: separate;
    border-spacing: 0;
}

/* Header sticky */
.table thead th {
    position: sticky;
    top: 0;
    z-index: 2;
    background: var(--primary);
    color: #fff;
    font-size: 0.72rem;
    letter-spacing: 0.05em;
    text-transform: uppercase;
    padding: 14px;
    border: none;
}

/* Celdas */
.table tbody td {
    padding: 14px;
    border-bottom: 1px solid #eef0f3;
    font-size: 0.9rem;
    color: #374151;
}

/* Hover tipo “card highlight” */
.table-hover tbody tr {
    transition: all 0.2s ease;
}

.table-hover tbody tr:hover {
    background: #f9fafb;
    transform: scale(1.002);
    box-shadow: inset 4px 0 0 var(--accent);
}

/* Botones más modernos */
.btn-action {
    background: var(--accent);
    color: white;
    border-radius: 8px;
    padding: 6px 10px;
    border: none;
    font-size: 0.8rem;
    transition: 0.2s;
}

.btn-action:hover {
    background: #2563eb;
    transform: translateY(-1px);
}

/* Inputs destacados */
.input-entrega {
    border: 2px solid #22c55e !important;
    border-radius: 8px;
    max-width: 90px;
    text-align: center;
    font-weight: 600;
    background: #f0fdf4;
}

/* Responsive */
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
                <h3 class="fw-bold text-dark m-0">Historial Ventas Vendedor</h3>
                <div id="loader" class="spinner-border spinner-border-sm text-secondary d-none"></div>
            </div>

            <div class="card filter-card mb-4">
                <div class="card-body">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">Buscador</label>
                            <input type="text" id="f_search" class="form-control form-control-sm"
                                placeholder="Folio o Cliente..." onkeyup="getVentas()">
                        </div>
                        <div class="col-md-2" style="display:none">
                            <label class="form-label small fw-bold">Estatus Entrega</label>
                            <select id="f_status" class="form-select form-select-sm" onchange="getVentas()">
                                <option value="">Todos</option>
                                <option value="pendiente">Pendiente</option>
                                <option value="parcial">Parcial</option>
                                <option value="entregado">Entregado</option>
                            </select>
                        </div>
                       <?php if ($puede == true): ?>
<div class="col-md-2">
    <label for="select-usuarios" class="form-label fw-bold small text-muted text-uppercase">Vendedor</label>
    <select class="form-select rounded-pill" id="select-usuarios" name="usuario_id" onchange="getVentas()">
       <option value="">Seleccione vendedor</option>
    </select>
</div>
<?php endif; ?>
                        <div class="col-md-2">
                            <label class="form-label small fw-bold">Estatus Pago</label>
                            <select id="f_pago" class="form-select form-select-sm" onchange="getVentas()">
                                <option value="">Todos</option>
                                <option value="deuda">Con Deuda</option>
                                <option value="pagado">Pagados</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small fw-bold">Periodo</label>
                            <select id="f_rango" class="form-select form-select-sm" onchange="togglePerso()">
  <option value="semana">Semana</option>
                                <option value="hoy">Hoy</option>
                                <option value="ayer">Ayer</option>
                                <option value="semana">Semana</option>
                                <option value="mes">Mes</option>
                                <option value="todos">Historial Completo</option>
                                <option value="personalizado">Rango...</option>
                            </select>
                        </div>
                        <div class="col-md-3 d-none" id="div_p">
                            <label class="form-label small fw-bold">Fechas</label>
                            <div class="input-group input-group-sm">
                                <input type="date" id="f_ini" class="form-control" onchange="getVentas()">
                                <input type="date" id="f_fin" class="form-control" onchange="getVentas()">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small fw-bold">Ubicación</label>
                            <select id="f_almacen" class="form-select form-select-sm" onchange="getVentas()">
                               <?php foreach($almacenes as $a): ?>
                                    <option value="<?= $a['id'] ?>"
                                        <?= ($a['id'] == $_SESSION['almacen_id']) ? 'selected' : '' ?>>
                                        <?= $a['nombre'] ?>
                                    </option>
                                    <?php endforeach; ?>
                            </select>
                        </div><div class="col-md-12"> <!-- Ampliado a col-md-3 para que respire mejor el dinero -->
    <div class="d-flex flex-column gap-3">
        <!-- Bloque Total Venta -->
        <div class="bg-light p-2 px-3 rounded-3 border-start border-primary border-4 shadow-sm">
            <small class="text-uppercase text-muted fw-bold d-block ls-wide style-label">Total Venta</small>
            <span id="venta" class="fs-4 fw-black text-primary d-block mt-1">
                $0.00
            </span>
        </div>
        
        <!-- Bloque Restante por Cobrar -->
        <div class="bg-light p-2 px-3 rounded-3 border-start border-danger border-4 shadow-sm">
            <small class="text-uppercase text-muted fw-bold d-block ls-wide style-label">Por Cobrar</small>
            <span id="deuda" class="fs-4 fw-black text-danger d-block mt-1">
                $0.00
            </span>
        </div>
    </div>
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
                               
                                <th class="text-end pe-3">Acciones</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalDetalle" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow">

            <!-- HEADER -->
            <div class="modal-header bg-dark text-white">
                <h6 class="modal-title fw-bold">
                    Información Venta: <span id="spanFolio" class="text-warning"></span>
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body p-0">
                <div class="row g-0">

                    <!-- SIDEBAR -->
                    <div class="col-md-3 bg-light border-end p-4">

                        <div class="mb-3">
                            <small class="text-uppercase text-muted fw-bold">Cliente</small>
                            <div id="detCliente" class="fw-semibold"></div>
                        </div>

                        <div class="mb-4">
                            <small class="text-uppercase text-muted fw-bold">Almacén</small>
                            <div id="detAlmacen" class="fw-semibold"></div>
                        </div>

                        <!-- RESUMEN -->
                        <div class="card border-0 shadow-sm mb-3">
                            <div class="card-body text-center">

                                <div class="mb-3">
                                    <small class="text-uppercase text-muted fw-bold d-block">
                                        Total de Venta
                                    </small>
                                    <span id="detTotalLabel" class="fs-5 fw-bold text-primary">
                                        $0.00
                                    </span>
                                </div>

                                <hr>

                                <div>
                                    <small class="text-uppercase text-muted fw-bold d-block">
                                        Saldo Pendiente
                                    </small>
                                    <span id="detSaldoLabel" class="fs-4 fw-bold text-danger">
                                        $0.00
                                    </span>
                                </div>

                            </div>
                        </div>

                        <!-- BOTÓN -->
                       <div id="boton"></div>
                    </div>

                    <!-- CONTENIDO -->
                    <div class="col-md-9 p-4">

                        <!-- DETALLE PRODUCTOS -->
                        <div class="card border-0 shadow-sm mb-3">
                            <div class="card-header bg-white fw-bold small text-uppercase text-muted">
                                Productos
                            </div>
                            <div class="table-responsive" style="max-height: 180px;">
                                <table class="table table-sm align-middle mb-0">
                                    <thead class="table-light">
                                        <tr class="small text-uppercase">
                                            <th>Producto</th>
                                            <th class="text-center">Venta</th>
                                            <th class="text-center">Surtido</th>
                                            <th class="text-center text-danger">Falta</th>
                                            <th class="text-center d-none">Entrega</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbodyDetalle"></tbody>
                                </table>
                            </div>
                        </div>

                        <div class="row g-3">

                            <!-- HISTORIAL PAGOS -->
                            <div class="col-12">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-header bg-white fw-bold small text-uppercase text-muted">
                                        Historial de Pagos
                                    </div>
                                    <div class="table-responsive" style="max-height: 180px;">
                                        <table class="table table-sm align-middle mb-0">
                                            <thead class="table-light">
                                                <tr class="small text-uppercase">
                                                    <th>Fecha</th>
                                                    <th>Monto</th>
                                                    <th>Método</th>
                                                    <th>REFERENCIA</th>
                                                </tr>
                                            </thead>
                                            <tbody id="tbodyPagos"></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- HISTORIAL ENTREGAS (OCULTO LO RESPETÉ) -->
                            <div class="col-12 d-none">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-header bg-white fw-bold small text-uppercase text-muted">
                                        Historial de Entregas
                                    </div>
                                    <div class="table-responsive" style="max-height: 180px;">
                                        <table class="table table-sm align-middle mb-0">
                                            <thead class="table-light">
                                                <tr class="small text-uppercase">
                                                    <th>Fecha</th>
                                                    <th>Responsable</th>
                                                    <th>Producto</th>
                                                    <th class="text-center">Cant</th>
                                                </tr>
                                            </thead>
                                            <tbody id="tbodyHistorial"></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- REPARTOS (OCULTO) -->
                            <div class="col-12 d-none">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-header bg-white fw-bold small text-uppercase text-muted">
                                        Repartos
                                    </div>
                                    <div class="table-responsive" style="max-height: 220px;">
                                        <table class="table table-sm align-middle mb-0">
                                            <thead class="table-light">
                                                <tr class="small text-uppercase">
                                                    <th># Reparto</th>
                                                    <th>Fecha Entrega</th>
                                                    <th>Estado</th>
                                                    <th class="text-center">Ruta</th>
                                                </tr>
                                            </thead>
                                            <tbody id="tbodyRepartos"></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                        </div>

                    </div>
                </div>
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
   
     <?php require_once __DIR__ . '/ventasHistorialModales/registarNuevoAbono.php'; ?>
    
    <script>


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
    const URL_CONTROLLER = '../controllers/historialPedidosVendedorController.php';

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
               f_vendedor:$('#select-usuarios').val() ?? ''
        });

        try {
            const res = await fetch(`${URL_CONTROLLER}?${params.toString()}`);
            const data = await res.json();
            let deuda=0;
            let totalVendido=0;


            $('#tablaVentas tbody').html(data.map(v => {
                let total = parseFloat(v.total) || 0;
                let pagado = parseFloat(v.pagado) || 0;
                let saldo = total - pagado;
                deuda+=saldo;
                totalVendido+=total;
                let badgeCobro = (saldo <= 0) ?
                    '<span class="text-success small fw-bold"><i class="bi bi-check-circle"></i> Pagado</span>' :
                    `<span class="text-danger small fw-bold">Debe: $${saldo.toFixed(2)}</span>`;
let agregarPago = (saldo <= 0) ?
                    '' :
                    `<button class="btn btn-sm btn-success shadow-sm" onclick="abrirNuevoAbono(${v.id})">
                            </i> Nuevo abono
                        </button>`;

                return `<tr>
              
                <td class="ps-3 small">${v.fecha}</td>
                <td class="fw-bold">${v.folio}</td>
                <td><span class="badge bg-light text-dark border fw-normal">${v.almacen_nombre}</span></td>
                 <td><div class="small fw-bold">${v.vendedor}</div></td>
                <td><div class="small fw-bold">${v.cliente}</div></td>
                <td class="fw-bold text-dark">$${total.toFixed(2)} </td>
                <td>${badgeCobro}</td>
                
                <td class="text-end pe-3">
                    <div class="btn-group">
                    
                    
                       
                        <button class="btn btn-sm btn-success " onclick="verDetalle(${v.id})">
                            <i class="bi bi-eye-fill"></i> ver
                        </button>
                      

                        
                       
                        
                    </div>
                </td>
            </tr>`;
            }).join(''));
            $('#deuda').text(deuda)
            $('#venta').text(totalVendido)
            console.log(deuda);
        } catch (e) {
            console.error("Error al cargar ventas:", e);
        } finally {
            $('#loader').addClass('d-none');
        }
    }
    async function verDetalle(id) {
        try {
            // 🔥 OBTENER IDS PENDIENTES
            const respIds = await fetch(
                `../controllers/entregasController.php?ajax=get_ids_pendientes_venta&venta_id=${id}`
            );
            const resNAlmacen = await fetch(
                `../controllers/entregasController.php?ajax=obtener_id_almacen&id=${id}`
            );

            const dataAlmacen = await resNAlmacen.json();
            const almacen_id_conseguido = dataAlmacen.almacen.almacen_id;
            console.log(dataAlmacen.almacen.almacen_id);

            const dataIds = await respIds.json();

            console.log(dataIds.ids);

            // =====================================================
            // 🔥 HABILITAR / DESHABILITAR BOTÓN
            // =====================================================

            if (
                Array.isArray(dataIds.ids) &&
                dataIds.ids.length > 0

            ) {
                console.log('hola');
                $('#btnGestionVenta')
                    .removeClass('d-none')
                    .prop('disabled', false)
                    .attr(
                        'onclick',
                        `abrirModalDespachoVentaTotal(${id}, ${almacen_id_conseguido})`
                    );

            } else {

                $('#btnGestionVenta')
                    .addClass('d-none')
                    .prop('disabled', true)
                    .removeAttr('onclick');

            }
            const res = await fetch(`${URL_CONTROLLER}?action=obtenerDetalle&id=${id}`);
           cargarRepartos(id);
            const data = await res.json();
           
            
            ventaActual = data;
            

            $('#spanFolio').text(data.info.folio);
            $('#detCliente').text(data.info.nombre_comercial);
            $('#detAlmacen').text(data.info.almacen);

            const total = parseFloat(data.info.total) || 0;
            const pagado = parseFloat(data.info.total_pagado) || 0;
            const deuda = total - pagado;
            $('#detTotalLabel').text('$' + total.toFixed(2));

            if (deuda <= 0) {
                $('#detSaldoLabel').text('LIQUIDADO').removeClass('text-danger').addClass('text-success');
                $('#btnAbonar').addClass('d-none');
            } else {
                $('#detSaldoLabel').text('$' + deuda.toFixed(2)).removeClass('text-success').addClass(
                    'text-danger');
                $('#btnAbonar').removeClass('d-none');
            }
            
            // --- RENDERIZADO DE PRODUCTOS CON CONVERSIÓN ---
            // --- RENDERIZADO DE PRODUCTOS CON CONVERSIÓN ---
            $('#tbodyDetalle').html(data.productos.map(p => {
                let cant = parseFloat(p.cantidad) || 0;
                let pendiente = (cant - (parseFloat(p.cantidad_entregada) || 0)).toFixed(3);

                let factor = parseFloat(p.factor_conversion) || 1;
                let cantPendiente = pendiente / factor;

                let pen = Number(pendiente);
                let pendi = Number(cantPendiente);
                let disponible = (p.disponible / factor);
                console.log(disponible);
                let entregada = p.cantidad_entregada / factor;

                console.log({
                    pen,
                    tipo: typeof pen,
                    comparacion: pen > 0
                });
                // 1. Definimos qué se verá en la columna "Venta"
                let visualizacionVenta = "";
                let infoEquivalenciaSub = "";
                let unm = (parseFloat(p.cantidad_entregada) / (1 / parseFloat(p.equivalencia)));
                console.log(unm);
                unm = unm % 1 !== 0 ? unm.toFixed(0) : unm;
                if (factor > 1 && cant >= factor) {
                    // Si alcanza el factor (Ej: 20 bultos >= 20 factor)
                    let unidadesMayores = (cant / factor);
                    // Formateamos para que si es entero no muestre .00 (Ej: 1 en vez de 1.00)
                    let totalUnidadesStr = Number.isInteger(unidadesMayores) ? unidadesMayores :
                        unidadesMayores.toFixed(2);


                    // Lo que se verá grande en la celda
                    visualizacionVenta =
                        `<span class="fw-bold">${totalUnidadesStr} ${p.unidad_reporte}</span> <br> <small class="text-muted">(${cant} ${p.unidad_medida})</small>`;

                    // Leyenda pequeña debajo del nombre del producto (opcional, para referencia)
                    infoEquivalenciaSub =
                        `<div class="text-muted small" style="font-size: 0.65rem;">1 ${p.unidad_reporte} = ${factor} ${p.unidad_medida}</div>`;
                } else {
                    // Si no llega al factor (Ej: 10 bultos) mostramos la unidad normal
                    //agregar observaciones en ticket 
                    visualizacionVenta = `<span>${cant} ${p.unidad_medida}</span>`;
                }


                return `<tr>
        <td>
            <div class="fw-bold text-dark">${p.producto}</div>
            ${infoEquivalenciaSub}
        </td>
        <td class="text-center">
        ${cant} ${cant/factor>=1?p.unidad_reporte:p.unidad_medida} 
      
        (${ p.equivalencia>=1?cant/(1/p.equivalencia).toFixed(2):(cant*(p.equivalencia)).toFixed(2)} ${p.nombre})
            
        </td>
        <td class="text-center">${entregada>1?entregada+ p.unidad_reporte:p.cantidad_entregada +p.unidad_medida}</td>
        
        <td class="text-center text-danger fw-bold">${(cantPendiente>=1?cantPendiente.toFixed(3):pen)} ${cantPendiente>=1?p.unidad_reporte:p.unidad_medida}</td>
         <td class="text-center col-input d-none">
            ${pen.toFixed(4) > 0 ? 
                `<input type="number"
    class="form-control form-control-sm input-entrega1 mx-auto"
    max="${pen<=p.disponible ? (pendi>=1 ? pendi : pen) : (disponible>1 ? disponible : p.disponible)}"
    min="0"
    step="0.01"
    value="0.00"
    data-dvid="${p.dvid}"
    data-id="${p.producto_id}"
    data-factor="${(pendi>=1 && disponible>=1) ? factor : 1}"
    style="width:70px">
                   <input type="hidden" class="form-control form-control-sm input-entrega mx-auto" 
                    value="0"data-dvid=${p.dvid} data-id="${p.producto_id}" style="width:70px"step="0.01" min="0">
                     <span class="badge bg-success">${
                    (pendi>=1&& disponible>=1)?p.unidad_reporte:p.unidad_medida}</span>` 
                     
                : '<span class="badge bg-success">Completo</span>'}
        </td>
    </tr>`;
            }).join(''));
            // ... (dentro de verDetalle, después de renderizar historial de entregas)
            $('#tbodyHistorial').html(data.historial.length > 0 ? data.historial.map(h => {
                    // 1. Extraemos los valores del historial
                    // Si salen vacíos o undefined, es que el PHP no los está mandando en el JSON de historial
                    let cantH = parseFloat(h.cantidad) || 0;
                    let factorH = parseFloat(h.factor_conversion) || 1;
                    let uReporteH = h.unidad_reporte || '';
                    let uMedidaH = h.unidad_medida || '';

                    let visualizacionHistorial = "";

                    // 2. Aplicamos la misma lógica que usas arriba
                    if (factorH > 1 && cantH >= factorH) {
                        let unidadesMayoresH = (cantH / factorH);
                        let totalUnidadesStrH = Number.isInteger(unidadesMayoresH) ?
                            unidadesMayoresH :
                            unidadesMayoresH.toFixed(2);

                        visualizacionHistorial = `
            <span class="fw-bold text-primary">${totalUnidadesStrH} ${uReporteH}</span> 
            <br> <small class="text-muted">(${cantH} ${uMedidaH})</small>
        `;
                    } else {
                        // Aquí verás si unidad_medida viene vacío desde la base de datos
                        visualizacionHistorial = `<span>${cantH} ${uMedidaH}</span>`;
                    }

                    return `
    <tr>
        <td class="small">${h.fecha}</td>
        <td class="small">${h.usuario_nombre}</td>
        <td>
            <div class="fw-bold" style="font-size:0.85rem;">${h.producto}</div>
        </td>
        <td class="text-center">
            ${visualizacionHistorial}
        </td>
    </tr>`;
                }).join('') :
                '<tr><td colspan="4" class="text-center text-muted p-3">No hay entregas registradas</td></tr>');


            // --- RENDERIZADO DE HISTORIAL DE PAGOS ---
            if (data.pagos && data.pagos.length > 0) {
                $('#tbodyPagos').html(data.pagos.map(p => `
        <tr>
            <td class="small">${p.fecha}</td>
            <td class="fw-bold text-success">$${parseFloat(p.monto).toFixed(2)}</td>
            <td>
                <span class="badge bg-light text-dark border fw-normal">${p.metodo_pago}</span>
                <div class="text-muted" style="font-size:0.65rem">Recibió: ${p.usuario_nombre}</div>
            </td>
             <td>
            <span>
    ${
        p.metodo_pago !== 'Efectivo' &&
        p.metodo_pago !== 'Saldo a Favor'
            ? (p.referencia ?? '')
            : '-'
    }
</span> 
            </td>
        </tr>
    `).join(''));
            } else {
                $('#tbodyPagos').html(
                    '<tr><td colspan="3" class="text-center text-muted p-3">No hay abonos registrados</td></tr>'
                );
            }
            alternarModo(false);
            modalObj.show();
        } catch (error) {
            console.error("Error al obtener detalle:", error);
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
    async function cargarRepartos(idVenta) {

    const resp = await fetch(
        `/cfsistem/app/controllers/repartosController.php?action=get_repartos_venta&id=${idVenta}`
    );

    const repartoViaje = await resp.json();

    const tbody = document.getElementById('tbodyRepartos');
    tbody.innerHTML = '';

    if (!repartoViaje.success) return;

    // ================================
    // AGRUPAR POR FOLIO VIAJE
    // ================================
    const grupos = {};
   
    repartoViaje.data.forEach(item => {

        if (!grupos[item.folio_viaje]) {

            grupos[item.folio_viaje] = {
                folio_viaje: item.folio_viaje,
                fecha_viaje: item.fecha_viaje,
                estatus_logistico: item.estatus_logistico,
                productos: [],
                clientes: new Set()
            };
        }

        grupos[item.folio_viaje].productos.push(item.productos);
        grupos[item.folio_viaje].clientes.add(item.cliente);
    });

    // ================================
    // RENDER TABLA
    // ================================
    Object.values(grupos).forEach(g => {

        const estadoClass =
            g.estatus_logistico === 'completado'
                ? 'bg-success'
                : 'bg-warning text-dark';

        const tr = `
            <tr>

                <td class="fw-bold">
                    ${g.folio_viaje}
                </td>

                <td>
                    ${g.fecha_viaje}
                </td>

                <td>
                    <span class="badge ${estadoClass}">
                        ${g.estatus_logistico}
                    </span>
                </td>

                <td class="text-center">

                    <button class="btn btn-sm btn-outline-primary"
                      onclick="imprimirRuta('${idVenta}','${g.folio_viaje}')">

                      
                        Ver Reparto 
                    </button>

                </td>

            </tr>
        `;

        tbody.insertAdjacentHTML('beforeend', tr);
    });
}
    
    const modalNuevoAbonoObj = new bootstrap.Modal('#modalNuevoAbono');



    function togglePerso() {
        $('#div_p').toggleClass('d-none', $('#f_rango').val() !== 'personalizado');
        getVentas();
    }

    function alternarModo(e) {
        $('.col-input').toggleClass('d-none', !e);
        $('#btnHabilitar').toggle(!e && ventaActual.info.estado_entrega !== 'entregado');
        $('#controlesGuardar').toggleClass('d-none', !e);
    }

    $(document).ready(function() {
        // 1. Carga inicial de datos
        getVentas();

        // 2. Escuchadores para filtros (opcional, pero recomendado para centralizar)
        $('#f_rango').on('change', togglePerso);
        // getVentas ya se llama mediante onchange/onkeyup en tu HTML, lo cual está bien.

        console.log("Sistema de historial listo.");
    });
    </script>
    <script>

   </script>
</body>

</html>