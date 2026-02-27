<?php
// 1. INCLUSIONES Y SEGURIDAD
require_once __DIR__ . '/../../includes/auth.php';
protegerPagina();
require_once __DIR__ . '/../../includes/sidebar.php';
require_once __DIR__ . '/../../config/conexion.php';

session_start();
$paginaActual = 'Entregas'; 

// --- PROCESAR GUARDADO ---
if (isset($_POST['accion']) && $_POST['accion'] == 'guardar_entrega') {
    header('Content-Type: application/json');
    $venta_id = intval($_POST['venta_id']);
    $productos = $_POST['productos'] ?? [];
    $usuario_id = $_SESSION['usuario_id'] ?? 1;

    $conexion->begin_transaction();
    try {
        $stmt = $conexion->prepare("INSERT INTO entregas_venta (venta_id, usuario_id, fecha) VALUES (?, ?, NOW())");
        $stmt->bind_param("ii", $venta_id, $usuario_id);
        $stmt->execute();
        $entrega_id = $conexion->insert_id;

        foreach ($productos as $dv_id => $cant) {
            $cant = floatval($cant);
            if ($cant <= 0) continue;
            
            $res_v = $conexion->query("SELECT (cantidad - cantidad_entregada) as pendiente, producto_id FROM detalle_venta WHERE id = $dv_id")->fetch_assoc();
            if ($cant > $res_v['pendiente']) throw new Exception("Cantidad excede el pendiente.");
            
            $conexion->query("INSERT INTO detalle_entrega (entrega_id, detalle_venta_id, cantidad) VALUES ($entrega_id, $dv_id, $cant)");
            $conexion->query("UPDATE detalle_venta SET cantidad_entregada = cantidad_entregada + $cant WHERE id = $dv_id");
            
            $vta_info = $conexion->query("SELECT almacen_id FROM ventas WHERE id = $venta_id")->fetch_assoc();
            $conexion->query("UPDATE inventario SET stock = stock - $cant WHERE producto_id = {$res_v['producto_id']} AND almacen_id = {$vta_info['almacen_id']}");
        }
        
        $check = $conexion->query("SELECT SUM(cantidad - cantidad_entregada) as deuda FROM detalle_venta WHERE venta_id = $venta_id")->fetch_assoc();
        $st = ($check['deuda'] <= 0) ? 'entregado' : 'parcial';
        $conexion->query("UPDATE ventas SET estado_entrega = '$st' WHERE id = $venta_id");
        
        $conexion->commit();
        echo json_encode(['status' => 'success']);
    } catch (Exception $e) {
        $conexion->rollback();
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
    exit;
}

// --- API LISTADO ---
if (isset($_GET['ajax'])) {
    header('Content-Type: application/json');
    $rol_id = $_SESSION['rol_id'] ?? 2;
    $id_almacen_usuario = $_SESSION['almacen_id'] ?? 0;
    
    $where = " WHERE v.estado_general = 'activa' ";
    if ($rol_id != 1) { $where .= " AND v.almacen_id = $id_almacen_usuario "; }
    elseif (!empty($_GET['f_almacen'])) { $where .= " AND v.almacen_id = " . intval($_GET['f_almacen']); }
    if (!empty($_GET['f_status'])) { $where .= " AND v.estado_entrega = '".$conexion->real_escape_string($_GET['f_status'])."' "; }
    if (!empty($_GET['f_search'])) {
        $s = $conexion->real_escape_string($_GET['f_search']);
        $where .= " AND (c.nombre_comercial LIKE '%$s%' OR v.folio LIKE '%$s%') ";
    }
    
    $r = $_GET['f_rango'] ?? '';
    if($r == 'hoy') $where .= " AND DATE(v.fecha) = CURDATE() ";
    if($r == 'ayer') $where .= " AND DATE(v.fecha) = SUBDATE(CURDATE(),1) ";
    if($r == 'semana') $where .= " AND YEARWEEK(v.fecha, 1) = YEARWEEK(CURDATE(), 1) ";
    if($r == 'mes') $where .= " AND MONTH(v.fecha) = MONTH(CURDATE()) AND YEAR(v.fecha) = YEAR(CURDATE()) ";
    if($r == 'personalizado' && !empty($_GET['f_inicio'])) {
        $where .= " AND DATE(v.fecha) BETWEEN '{$_GET['f_inicio']}' AND '{$_GET['f_fin']}' ";
    }

    $sql = "SELECT v.*, c.nombre_comercial as cliente, a.nombre as almacen_nombre 
            FROM ventas v 
            JOIN clientes c ON v.id_cliente = c.id 
            JOIN almacenes a ON v.almacen_id = a.id $where ORDER BY v.fecha DESC";
    $res = $conexion->query($sql);
    $data = [];
    while ($row = $res->fetch_assoc()) { $data[] = $row; }
    echo json_encode($data);
    exit;
}

// --- API DETALLE ---
if (isset($_GET['detalle_id'])) {
    header('Content-Type: application/json');
    $id = intval($_GET['detalle_id']);
    $info = $conexion->query("SELECT v.*, c.nombre_comercial, a.nombre as almacen FROM ventas v JOIN clientes c ON v.id_cliente = c.id JOIN almacenes a ON v.almacen_id = a.id WHERE v.id = $id")->fetch_assoc();
    
    $prods = [];
    $resP = $conexion->query("SELECT dv.*, p.nombre as producto FROM detalle_venta dv JOIN productos p ON dv.producto_id = p.id WHERE dv.venta_id = $id");
    while($p = $resP->fetch_assoc()){ $prods[] = $p; }
    
    $historial = [];
    $sqlH = "SELECT ev.fecha, p.nombre as producto, de.cantidad, u.nombre as usuario_nombre
             FROM entregas_venta ev 
             JOIN detalle_entrega de ON ev.id = de.entrega_id 
             JOIN detalle_venta dv ON de.detalle_venta_id = dv.id
             JOIN productos p ON dv.producto_id = p.id
             JOIN usuarios u ON ev.usuario_id = u.id
             WHERE ev.venta_id = $id ORDER BY ev.fecha DESC";
    $resH = $conexion->query($sqlH);
    while($h = $resH->fetch_assoc()){ $historial[] = $h; }
    
    echo json_encode(['info' => $info, 'productos' => $prods, 'historial' => $historial]);
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Entregas | Sistema</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --sidebar-width: 250px;
            --primary-dark: #2c3e50;
            --accent-color: #34495e;
            --bg-body: #f8f9fa;
        }

        body { background-color: var(--bg-body); overflow-x: hidden; }

        /* Contenedor principal para no invadir sidebar */
        .main-content { 
            margin-left: var(--sidebar-width); 
            padding: 2rem; 
            min-height: 100vh;
            transition: all 0.3s;
        }

        /* Estilo sobrio para tablas */
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
            letter-spacing: 0.5px;
            padding: 12px;
            border: none;
        }

        /* Colores de botones - Sobrios */
        .btn-action { background-color: var(--accent-color); color: white; border: none; }
        .btn-action:hover { background-color: var(--primary-dark); color: white; }
        
        .filter-card { border: none; box-shadow: 0 2px 10px rgba(0,0,0,0.05); border-radius: 10px; }

        /* Modal con diseño limpio */
        .modal-header { background-color: var(--primary-dark); color: white; border: none; }
        .input-entrega { border: 2px solid #28a745 !important; max-width: 90px; text-align: center; font-weight: bold; }
        
        .historial-row { font-size: 0.85rem; border-bottom: 1px solid #eee; }
        
        @media (max-width: 992px) {
            .main-content { margin-left: 0; padding: 1rem; }
        }
    </style>
</head>
<body>
    <?php renderSidebar($paginaActual); ?>

    <div class="main-content">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="fw-bold text-dark m-0">Control de Entregas</h3>
                <div id="loader" class="spinner-border spinner-border-sm text-secondary d-none"></div>
            </div>

            <div class="card filter-card mb-4">
                <div class="card-body">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">Buscador</label>
                            <input type="text" id="f_search" class="form-control form-control-sm" placeholder="Folio o Cliente..." onkeyup="getVentas()">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small fw-bold">Estatus</label>
                            <select id="f_status" class="form-select form-select-sm" onchange="getVentas()">
                                <option value="">Todos</option>
                                <option value="pendiente">Pendiente</option>
                                <option value="parcial">Parcial</option>
                                <option value="entregado">Entregado</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small fw-bold">Periodo</label>
                            <select id="f_rango" class="form-select form-select-sm" onchange="togglePerso()">
                                <option value="todos">Cualquier fecha</option>
                                <option value="hoy">Hoy</option>
                                <option value="ayer">Ayer</option>
                                <option value="semana">Semana</option>
                                <option value="mes">Mes</option>
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
                            <select id="f_almacen" class="form-select form-select-sm" onchange="getVentas()" <?= ($_SESSION['rol_id'] != 1 ? 'disabled':'') ?>>
                                <option value="">Todas</option>
                                <?php 
                                $alms = $conexion->query("SELECT id, nombre FROM almacenes");
                                while($a = $alms->fetch_assoc()){
                                    $sel = ($_SESSION['rol_id'] != 1 && $_SESSION['almacen_id'] == $a['id']) ? 'selected':'';
                                    echo "<option value='{$a['id']}' $sel>{$a['nombre']}</option>";
                                }
                                ?>
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
                                <th>Cliente</th>
                                <th class="text-center">Estado</th>
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
            <div class="modal-content border-0">
                <div class="modal-header">
                    <h6 class="modal-title fw-bold">Gestión de Venta: <span id="spanFolio"></span></h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-0">
                    <div class="row g-0">
                        <div class="col-md-3 bg-light border-end p-4">
                            <label class="small text-muted text-uppercase d-block mb-1">Cliente</label>
                            <p id="detCliente" class="fw-bold small mb-3"></p>
                            
                            <label class="small text-muted text-uppercase d-block mb-1">Almacén</label>
                            <p id="detAlmacen" class="fw-bold small mb-4"></p>
                            
                            <div id="contenedorBoton">
                                <button id="btnHabilitar" class="btn btn-action w-100 mb-2 py-2 fw-bold" onclick="alternarModo(true)">
                                    <i class="bi bi-box-seam me-2"></i>Nueva Entrega
                                </button>
                            </div>
                            
                            <div id="controlesGuardar" class="d-none">
                                <button class="btn btn-success w-100 mb-2 py-2 fw-bold" onclick="procesarEntrega()">
                                    Guardar Cambios
                                </button>
                                <button class="btn btn-link text-secondary w-100 btn-sm" onclick="alternarModo(false)">Cancelar</button>
                            </div>
                        </div>

                        <div class="col-md-9 p-4">
                            <h6 class="fw-bold text-muted mb-3">Productos Pendientes</h6>
                            <div class="table-responsive border rounded mb-4" style="max-height: 200px;">
                                <table class="table table-sm align-middle mb-0">
                                    <thead class="table-light">
                                        <tr class="small text-uppercase">
                                            <th>Producto</th>
                                            <th class="text-center">Venta</th>
                                            <th class="text-center">Surtido</th>
                                            <th class="text-center text-danger">Falta</th>
                                            <th class="text-center col-input d-none">Entrega</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbodyDetalle" class="small"></tbody>
                                </table>
                            </div>

                            <h6 class="fw-bold text-muted mb-3">Historial de Salidas</h6>
                            <div class="table-responsive border rounded" style="max-height: 180px;">
                                <table class="table table-sm align-middle mb-0">
                                    <thead class="table-light">
                                        <tr class="small text-uppercase">
                                            <th>Fecha</th>
                                            <th>Responsable</th>
                                            <th>Producto</th>
                                            <th class="text-center">Cant</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbodyHistorial" class="small"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
    const modalObj = new bootstrap.Modal('#modalDetalle');
    let ventaActual = null;

    async function getVentas() {
        $('#loader').removeClass('d-none');
        const s = $('#f_search').val(), r = $('#f_rango').val(), i = $('#f_ini').val(), f = $('#f_fin').val(), a = $('#f_almacen').val(), st = $('#f_status').val();
        try {
            const res = await fetch(`?ajax=1&f_search=${s}&f_rango=${r}&f_inicio=${i}&f_fin=${f}&f_almacen=${a}&f_status=${st}`);
            const data = await res.json();
            $('#tablaVentas tbody').html(data.map(v => `
                <tr>
                    <td class="ps-3 small">${v.fecha}</td>
                    <td class="fw-bold">${v.folio}</td>
                    <td><div class="small fw-bold">${v.cliente}</div></td>
                    <td class="text-center"><span class="badge ${v.estado_entrega=='entregado'?'bg-success':(v.estado_entrega=='parcial'?'bg-warning text-dark':'bg-danger')}">${v.estado_entrega.toUpperCase()}</span></td>
                    <td class="text-end pe-3"><button class="btn btn-sm btn-outline-secondary" onclick="verDetalle(${v.id})">Gestionar</button></td>
                </tr>`).join(''));
        } catch (e) { console.error(e); }
        $('#loader').addClass('d-none');
    }

    async function verDetalle(id) {
        const res = await fetch('?detalle_id=' + id);
        const data = await res.json();
        ventaActual = data;
        $('#spanFolio').text(data.info.folio);
        $('#detCliente').text(data.info.nombre_comercial);
        $('#detAlmacen').text(data.info.almacen);

        $('#btnHabilitar').toggle(data.info.estado_entrega !== 'entregado');

        $('#tbodyDetalle').html(data.productos.map(p => {
            let pen = parseFloat(p.cantidad) - parseFloat(p.cantidad_entregada);
            return `<tr>
                <td class="fw-bold">${p.producto}</td>
                <td class="text-center">${p.cantidad}</td>
                <td class="text-center">${p.cantidad_entregada}</td>
                <td class="text-center fw-bold text-danger">${pen}</td>
                <td class="text-center col-input d-none">
                    ${pen > 0 ? `<input type="number" class="form-control form-control-sm input-entrega mx-auto" max="${pen}" min="0" value="0" data-id="${p.id}">` : '<i class="bi bi-check-circle text-success"></i>'}
                </td>
            </tr>`;
        }).join(''));

        $('#tbodyHistorial').html(data.historial.map(h => `
            <tr class="historial-row">
                <td>${h.fecha}</td>
                <td class="fw-bold">${h.usuario_nombre}</td>
                <td>${h.producto}</td>
                <td class="text-center fw-bold text-primary">${h.cantidad}</td>
            </tr>`).join('') || '<tr><td colspan="4" class="text-center p-3">Sin entregas previas</td></tr>');

        alternarModo(false);
        modalObj.show();
    }

    function togglePerso() {
        $('#div_p').toggleClass('d-none', $('#f_rango').val() !== 'personalizado');
        getVentas();
    }

    function alternarModo(e) { 
        $('.col-input').toggleClass('d-none', !e); 
        $('#btnHabilitar').toggle(!e && ventaActual.info.estado_entrega !== 'entregado'); 
        $('#controlesGuardar').toggleClass('d-none', !e); 
    }

    async function procesarEntrega() {
        const fd = new FormData();
        fd.append('accion', 'guardar_entrega');
        fd.append('venta_id', ventaActual.info.id);
        let ok = false;
        $('.input-entrega').each(function() { if($(this).val() > 0) { fd.append(`productos[${$(this).data('id')}]`, $(this).val()); ok = true; } });
        if(!ok) return Swal.fire('Error', 'Indique cantidades', 'warning');
        
        const res = await fetch(window.location.href, { method: 'POST', body: fd });
        if((await res.json()).status == 'success') { modalObj.hide(); getVentas(); Swal.fire('Listo', 'Entrega guardada', 'success'); }
    }

    $(document).ready(getVentas);
    </script>
</body>
</html>