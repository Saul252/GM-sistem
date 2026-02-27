<?php
require_once __DIR__ . '/../../includes/auth.php';
protegerPagina();
require_once __DIR__ . '/../../includes/sidebar.php';
require_once __DIR__ . '/../../config/conexion.php';

$paginaActual = 'ventashistorial';

// Lógica de Permisos
$id_usuario = $_SESSION['id'];
$rol_usuario = $_SESSION['rol_id']; // 1 = Admin
$almacen_usuario = $_SESSION['almacen_id']; 

$es_admin = ($rol_usuario == 1);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Ventas y Entregas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .badge-pendiente { background-color: #ffc107; color: #000; }
        .badge-parcial { background-color: #17a2b8; color: #fff; }
        .badge-entregado { background-color: #28a745; color: #fff; }
        .modal-xl { max-width: 90%; }
    </style>
</head>
<body class="bg-light">

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h4"><i class="bi bi-receipt-cutoff text-primary"></i> Monitor de Ventas y Logística</h2>
    </div>

    <div class="card shadow-sm mb-4 border-0">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-2">
                    <label class="form-label small fw-bold">Almacén</label>
                    <?php if($es_admin): ?>
                        <select id="filtroAlmacen" class="form-select form-select-sm" onchange="cargarVentas()">
                            <option value="todos">Todos</option>
                            <?php
                            $res = $conexion->query("SELECT id, nombre FROM almacenes");
                            while($a = $res->fetch_assoc()) echo "<option value='{$a['id']}'>{$a['nombre']}</option>";
                            ?>
                        </select>
                    <?php else: ?>
                        <input type="hidden" id="filtroAlmacen" value="<?php echo $almacen_usuario; ?>">
                        <input type="text" class="form-control form-control-sm bg-white" value="Mi Almacén" readonly>
                    <?php endif; ?>
                </div>

                <div class="col-md-2">
                    <label class="form-label small fw-bold">Periodo</label>
                    <select id="filtroFecha" class="form-select form-select-sm" onchange="manejarFecha(this.value)">
                        <option value="hoy">Hoy</option>
                        <option value="ayer">Ayer</option>
                        <option value="semana">Esta Semana</option>
                        <option value="mes">Este Mes</option>
                        <option value="personalizado">Personalizado...</option>
                    </select>
                </div>

                <div class="col-md-5">
                    <label class="form-label small fw-bold">Buscador Inteligente</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                        <input type="text" id="buscador" class="form-control border-start-0" 
                               placeholder="Folio, cliente o producto..." onkeyup="filtrarLocalmente()">
                    </div>
                </div>

                <div class="col-md-2">
                    <label class="form-label small fw-bold">Estatus Entrega</label>
                    <select id="filtroEstatus" class="form-select form-select-sm" onchange="filtrarLocalmente()">
                        <option value="todos">Todos</option>
                        <option value="pendiente">Pendiente</option>
                        <option value="parcial">Parcial</option>
                        <option value="entregado">Terminado</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="tablaVentas">
                <thead class="bg-dark text-white">
                    <tr>
                        <th>Folio</th>
                        <th>Fecha</th>
                        <th>Almacén</th>
                        <th>Cliente</th>
                        <th>Total</th>
                        <th>Surtido</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody id="tbodyVentas">
                    </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="modalDetalleVenta" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div id="contenidoModalDetalle">
                </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Al cargar la página
document.addEventListener('DOMContentLoaded', cargarVentas);

function cargarVentas() {
    const almacen = document.getElementById('filtroAlmacen').value;
    const rango = document.getElementById('filtroFecha').value;
    
    // Aquí puedes agregar lógica para fechas personalizadas con Swal.fire si rango == 'personalizado'

    fetch(`backend/ventas/get_historial.php?almacen=${almacen}&rango=${rango}`)
    .then(res => res.json())
    .then(data => {
        const tbody = document.getElementById('tbodyVentas');
        tbody.innerHTML = data.map(v => `
            <tr data-busqueda="${v.folio} ${v.cliente} ${v.productos_lista}" data-estatus="${v.estado_entrega}">
                <td><span class="fw-bold">${v.folio}</span></td>
                <td><small>${v.fecha}</small></td>
                <td><span class="badge bg-secondary font-monospace">${v.almacen_nombre}</span></td>
                <td>${v.cliente}</td>
                <td>$${v.total}</td>
                <td><span class="badge badge-${v.estado_entrega}">${v.estado_entrega.toUpperCase()}</span></td>
                <td class="text-center">
                    <button class="btn btn-sm btn-primary" onclick="verDetalle(${v.id})">
                        <i class="bi bi-eye"></i> Gestionar
                    </button>
                    <a href="ticket.php?id=${v.id}" target="_blank" class="btn btn-sm btn-light border">
                        <i class="bi bi-printer"></i>
                    </a>
                </td>
            </tr>
        `).join('');
    });
}

function filtrarLocalmente() {
    const query = document.getElementById('buscador').value.toLowerCase();
    const estatus = document.getElementById('filtroEstatus').value;
    const filas = document.querySelectorAll('#tbodyVentas tr');

    filas.forEach(f => {
        const texto = f.getAttribute('data-busqueda').toLowerCase();
        const estStatus = f.getAttribute('data-estatus');
        const coincideBusqueda = texto.includes(query);
        const coincideEstatus = (estatus === 'todos' || estStatus === estatus);
        
        f.style.display = (coincideBusqueda && coincideEstatus) ? '' : 'none';
    });
}

function verDetalle(idVenta) {
    fetch(`backend/ventas/modal_detalle_venta.php?id=${idVenta}`)
    .then(res => res.text())
    .then(html => {
        document.getElementById('contenidoModalDetalle').innerHTML = html;
        new bootstrap.Modal(document.getElementById('modalDetalleVenta')).show();
    });
}
</script>
</body>
</html>