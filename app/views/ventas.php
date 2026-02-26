<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../../includes/auth.php';
protegerPagina();

require_once __DIR__ . '/../../includes/sidebar.php';
require_once __DIR__ . '/../../conexion.php';

$paginaActual = 'Ventas';

/* PRODUCTOS */
$sql = "SELECT 
    p.id,
    p.sku,
    p.nombre,
    i.stock,
    i.almacen_id,
    a.nombre AS almacen_nombre,
    IFNULL(pp.precio_minorista,0) precio_minorista,
    IFNULL(pp.precio_mayorista,0) precio_mayorista,
    IFNULL(pp.precio_distribuidor,0) precio_distribuidor
FROM inventario i
INNER JOIN productos p ON i.producto_id = p.id
INNER JOIN almacenes a ON i.almacen_id = a.id
LEFT JOIN precios_producto pp 
    ON pp.producto_id = p.id 
    AND pp.almacen_id = i.almacen_id
WHERE p.activo = 1
AND i.stock > 0
ORDER BY p.nombre ASC";

$result = $conexion->query($sql);
$productos = [];

while($row = $result->fetch_assoc()){
    $productos[] = $row;
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Ventas</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <style>
    body {
        background: #f4f6f9;
    }

    .main-content {
        margin-left: 260px;
        padding: 30px;
    }

    .card {
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, .08);
    }

    .tabla-scroll {
        max-height: 500px;
        overflow-y: auto;
    }

    .carrito {
        position: sticky;
        top: 20px;
    }
    </style>
</head>

<body>

    <?php renderSidebar($paginaActual); ?>

    <div class="main-content">

        <h2 class="mb-4 fw-bold">
            <i class="bi bi-cart-fill text-primary"></i> Módulo de Ventas
        </h2>

        <div class="row">

            <!-- PRODUCTOS -->
            <div class="col-lg-8">
                <div class="card p-3">
                    

                    <input type="text" id="buscador" class="form-control mb-3" placeholder="🔎 Buscar producto...">

                    <div class="table-responsive tabla-scroll">
                        <table class="table table-bordered table-hover tabla-productos">
                            <thead class="table-dark">
                                <tr>
                                    <th>SKU</th>
                                    <th>Producto</th>
                                    <th>Almacen</th>
                                    <th>Stock</th>
                                    <th>Precio</th>
                                    <th width="90">Cant</th>
                                    <th width="60"></th>
                                </tr>
                            </thead>
                            <tbody>

                                <?php foreach($productos as $p): ?>
                                <tr>
                                    <td><?= $p['sku'] ?></td>
                                    <td><?= htmlspecialchars($p['nombre']) ?></td>
                                    <td><span class="badge bg-success"><?= $p['stock'] ?></span></td>
                                    <th><?=$p['almacen_nombre']?></th>

                                    <td>
                                        <select class="form-select form-select-sm select-precio">
                                            <option value="<?= $p['precio_minorista'] ?>">
                                                Minorista - $<?= number_format($p['precio_minorista'],2) ?>
                                            </option>
                                            <option value="<?= $p['precio_mayorista'] ?>">
                                                Mayorista - $<?= number_format($p['precio_mayorista'],2) ?>
                                            </option>
                                            <option value="<?= $p['precio_distribuidor'] ?>">
                                                Distribuidor - $<?= number_format($p['precio_distribuidor'],2) ?>
                                            </option>
                                        </select>
                                    </td>

                                    <td>
                                        <input type="number" class="form-control form-control-sm cantidad" min="1"
                                            max="<?= $p['stock'] ?>" value="1">
                                    </td>

                                    <td class="text-center">
                                        <button type="button" class="btn btn-success btn-sm"
                                            data-producto-id="<?= $p['id'] ?>" data-almacen-id="<?= $p['almacen_id'] ?>"
                                            data-almacen="<?= htmlspecialchars($p['almacen_nombre']) ?>"
                                            onclick="agregarProducto(this)">
                                            <i class="bi bi-plus"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- CARRITO -->
            <div class="col-lg-4">
                <div class="card p-3 carrito">

                    <h5 class="fw-bold mb-3">
                        <i class="bi bi-bag-fill text-success"></i> Carrito
                    </h5>

                    <div class="table-responsive">
                        <table class="table table-sm" id="tablaCarrito">
                            <thead>
                                <tr>
                                    <th>Almacén</th>
                                    <th>Producto</th>
                                    <th>Cant</th>
                                    <th>Sub</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>

                    <hr>

                    <h4 class="text-end fw-bold">
                        Total: $<span id="total">0.00</span>
                    </h4>

                    <button type="button" class="btn btn-primary w-100 mt-3" onclick="abrirModalFinalizar()">
                        <i class="bi bi-cash-stack"></i> Finalizar Venta
                    </button>

                </div>
            </div>

        </div>
    </div>

    <!-- MODAL FINALIZAR -->
    <div class="modal fade" id="modalFinalizarVenta" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Confirmar Venta</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <div class="table-responsive mb-3">
                        <table class="table table-bordered">
                            <thead class="table-dark">
                                <tr>
                                    
                                    <th>Producto</th>
                                    <th>Cantidad</th>
                                    <th>Precio</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody id="tablaConfirmacion"></tbody>
                        </table>
                    </div>

                    <div class="mb-3">
                        <label>Descuento ($)</label>
                        <input type="number" id="descuentoGeneral" class="form-control" value="0">
                    </div>

                    <h4 class="text-end">
                        Total Final: $<span id="totalFinalModal">0.00</span>
                    </h4>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button class="btn btn-success">Confirmar Venta</button>
                </div>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
    let carrito = [];

    /* AGREGAR */
    function agregarProducto(btn) {

        let fila = btn.closest("tr");

        let nombre = fila.children[1].innerText;
        let cantidad = parseFloat(fila.querySelector(".cantidad").value);
        let select = fila.querySelector(".select-precio");
        let precio = parseFloat(select.value);

        let producto_id = btn.dataset.productoId;
        let almacen_id = btn.dataset.almacenId;
        let almacen_nombre = btn.dataset.almacen;

        if (!cantidad || cantidad <= 0) {
            alert("Cantidad inválida");
            return;
        }

        carrito.push({
            producto_id: producto_id,
            almacen_id: almacen_id,
            almacen_nombre: almacen_nombre,
            nombre: nombre,
            cantidad: cantidad,
            precio_unitario: precio,
            subtotal: cantidad * precio
        });

        renderCarrito();

        fila.querySelector(".cantidad").value = 1;
    }
    /* RENDER */
   function renderCarrito() {

    let tabla = document.querySelector("#tablaCarrito tbody");
    tabla.innerHTML = "";
    let total = 0;

    carrito.forEach((item, index) => {
        total += item.subtotal;

        tabla.innerHTML += `
<tr>
<td>${item.almacen_nombre}</td>
<td>${item.nombre}</td>
<td>${item.cantidad}</td>
<td>$${item.subtotal.toFixed(2)}</td>
<td>
<button class="btn btn-danger btn-sm"
onclick="eliminarProducto(${index})">
<i class="bi bi-x"></i>
</button>
</td>
</tr>`;
    });

    document.getElementById("total").innerText = total.toFixed(2);
}
    function eliminarProducto(index) {
        carrito.splice(index, 1);
        renderCarrito();
    }

    /* MODAL */
    function abrirModalFinalizar() {

        if (carrito.length === 0) {
            alert("Carrito vacío");
            return;
        }

        let tabla = document.getElementById("tablaConfirmacion");
        tabla.innerHTML = "";

        carrito.forEach(item => {
            tabla.innerHTML += `
<tr>
<td>${item.nombre}</td>
<td>${item.cantidad}</td>
<td>$${item.precio_unitario.toFixed(2)}</td>
<td>$${item.subtotal.toFixed(2)}</td>
</tr>`;
        });

        recalcularTotalModal();

        let modal = new bootstrap.Modal(document.getElementById('modalFinalizarVenta'));
        modal.show();
    }

    function recalcularTotalModal() {
        let total = 0;
        carrito.forEach(i => total += i.subtotal);
        let descuento = parseFloat(document.getElementById("descuentoGeneral").value) || 0;
        total -= descuento;
        if (total < 0) total = 0;
        document.getElementById("totalFinalModal").innerText = total.toFixed(2);
    }

    /* DESCUENTO EVENT */
    document.addEventListener("DOMContentLoaded", function() {
        let descuentoInput = document.getElementById("descuentoGeneral");
        if (descuentoInput) {
            descuentoInput.addEventListener("input", recalcularTotalModal);
        }
    });

    /* BUSCADOR */
    document.getElementById("buscador").addEventListener("keyup", function() {
        let filtro = this.value.toLowerCase();
        document.querySelectorAll(".tabla-productos tbody tr").forEach(fila => {
            fila.style.display = fila.innerText.toLowerCase().includes(filtro) ? "" : "none";
        });
    });
    </script>

</body>

</html>