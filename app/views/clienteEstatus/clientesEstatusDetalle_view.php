<?php
/**
 * clientesEstatusDetalle_view.php
 * Expediente 360° Sincronizado
 */
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $tituloPagina ?> | Sistema</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <?php if (function_exists('cargarEstilos')) { cargarEstilos(); } ?>

    <style>
        :root { --sidebar-width: 260px; --navbar-height: 65px; }
        body { background-color: #f0f2f5; font-family: 'Segoe UI', sans-serif; }
        .main-content { margin-left: var(--sidebar-width); padding: 30px; padding-top: calc(var(--navbar-height) + 20px); transition: all 0.3s ease; }
        .card-expediente { border: none; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        .header-expediente { background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%); color: white; border-radius: 12px; }
        .nav-pills .nav-link.active { background-color: #0d6efd; }
        @media (max-width: 768px) { .main-content { margin-left: 0; padding: 15px; padding-top: 90px; } }
    </style>
</head>
<body>

    <?php if (function_exists('renderizarLayout')) { renderizarLayout($paginaActual); } ?>

    <main class="main-content">
        <div class="card header-expediente p-4 mb-4 border-0 shadow-sm">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <a href="clientesEstatusController.php" class="btn btn-sm btn-light rounded-pill mb-3">
                        <i class="bi bi-arrow-left"></i> Volver
                    </a>
                    <h1 class="fw-bold mb-1"><?= $infoCliente['nombre_comercial'] ?></h1>
                    <p class="mb-0 opacity-75">
                        <span class="me-3"><i class="bi bi-hash"></i> RFC: <?= $infoCliente['rfc'] ?></span>
                        <span><i class="bi bi-telephone"></i> <?= $infoCliente['telefono'] ?? 'S/T' ?></span>
                    </p>
                </div>
                <div class="col-md-4 text-md-end mt-3 mt-md-0">
                    <div class="bg-white bg-opacity-25 p-3 rounded-3">
                        <small class="d-block opacity-75">Saldo Total Deudor</small>
                        <h2 class="fw-bold mb-0" id="txtSaldoGlobal">$ 0.00</h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card card-expediente p-4">
                    <ul class="nav nav-pills mb-4" id="expedienteTabs" role="tablist">
                        <li class="nav-item">
                            <button class="nav-link active" data-bs-toggle="pill" data-bs-target="#tab-folios">
                                <i class="bi bi-file-earmark-text me-2"></i>Ventas y Lotes
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-entregas">
                                <i class="bi bi-box-seam me-2"></i>Pendientes de Entrega
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="tab-folios">
                            <div class="table-responsive">
                                <table class="table align-middle">
                                    <thead class="bg-dark text-white">
                                        <tr><th class="py-3 px-4">Desglose Maestro por Folio</th></tr>
                                    </thead>
                                    <tbody id="bodyFolios">
                                        <tr><td class="text-center py-5"><div class="spinner-border text-primary"></div></td></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="tab-entregas">
                            <table class="table align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Producto</th>
                                        <th class="text-center">Vendido</th>
                                        <th class="text-center">Surtido</th>
                                        <th class="text-center text-primary">Faltante</th>
                                        <th>Folio</th>
                                    </tr>
                                </thead>
                                <tbody id="bodyEntregas"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card card-expediente p-4">
                    <h5 class="fw-bold mb-4"><i class="bi bi-cash-stack me-2 text-success"></i>Cronología de Pagos</h5>
                    <div id="timelinePagos"></div>
                </div>
            </div>
        </div>
    </main>

    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
    const clienteId = "<?= $infoCliente['id'] ?>";

    $(document).ready(function() {
        if (clienteId && clienteId !== "0") { fetchData(); }
    });

    function fetchData() {
        $.ajax({
            url: '/cfsistem/app/controllers/clientesEstatusController.php', 
            type: 'GET',
            data: { action: 'obtenerDetalle', id: clienteId },
            dataType: 'json',
            success: function(res) {
                console.log("Datos del servidor:", res);
                if (res.success && res.data) {
                    renderFolios(res.data);
                    prepararTimelinePagos(res.data);
                    prepararEntregasPendientes(res.data);
                } else {
                    $('#bodyFolios').html('<tr><td class="text-center py-4">Error: ' + (res.message || 'Sin datos') + '</td></tr>');
                }
            },
            error: function(xhr) {
                console.error("Error AJAX:", xhr.responseText);
                $('#bodyFolios').html('<tr><td class="text-center text-danger py-4">Error de conexión con el servidor.</td></tr>');
            }
        });
    }

  function renderFolios(data) {
    let html = '';
    let totalDeudaGlobal = 0;

    if (!data || data.length === 0) {
        $('#bodyFolios').html('<tr><td class="text-center py-5">No hay ventas registradas.</td></tr>');
        return;
    }

    data.forEach(v => {
        const listaProductos = v.productos || [];
        const listaPagos = v.pagos || [];
        const totalVenta = parseFloat(v.total || 0);
        const totalPagado = parseFloat(v.total_pagado || 0);
        const saldo = totalVenta - totalPagado;
        totalDeudaGlobal += saldo;

        // --- LÓGICA DE AGRUPACIÓN POR PRODUCTO ---
        // Usamos un objeto para sumar totales por SKU/Nombre
        const resumenProductos = {};
        listaProductos.forEach(p => {
            const key = p.sku || p.producto;
            if (!resumenProductos[key]) {
                resumenProductos[key] = {
                    nombre: p.producto,
                    sku: p.sku,
                    cantidadTotal: 0,
                    precioVenta: parseFloat(p.precio_venta || 0),
                    lotes: []
                };
            }
            resumenProductos[key].cantidadTotal += parseFloat(p.cantidad || 0);
            resumenProductos[key].lotes.push({
                codigo: p.lote_codigo || 'S/L',
                cantidadLote: parseFloat(p.cantidad || 0),
                costo: parseFloat(p.precio_lote_compra || 0)
            });
        });

        // --- RENDERIZADO DE FILAS ---
        let filasProductos = '';
        Object.values(resumenProductos).forEach(prod => {
            // Fila Maestra (El total que compró)
            filasProductos += `
            <tr class="table-light">
                <td>
                    <div class="fw-bold text-dark">${prod.nombre}</div>
                    <small class="text-muted">${prod.sku}</small>
                </td>
                <td class="text-center fw-bold">Total: ${prod.cantidadTotal}</td>
                <td class="text-end fw-bold">$ ${prod.precioVenta.toLocaleString('es-MX', {minimumFractionDigits:2})}</td>
                <td colspan="2" class="text-center small text-uppercase text-muted pt-2">Desglose por Lotes ↓</td>
            </tr>`;

            // Filas de Lotes (De dónde se sacó cada parte)
            prod.lotes.forEach(l => {
                filasProductos += `
                <tr>
                    <td class="ps-4 text-muted"><i class="bi bi-arrow-return-right me-2"></i> Partida de Lote</td>
                    <td class="text-center small">${l.cantidadLote}</td>
                    <td class="text-end">---</td>
                    <td class="text-center"><span class="badge bg-dark rounded-pill" style="font-size: 0.7rem;">${l.codigo}</span></td>
                    <td class="text-end text-muted small pe-3">$ ${l.costo.toLocaleString('es-MX', {minimumFractionDigits:2})}</td>
                </tr>`;
            });
        });

        // Renderizado de Pagos (Igual que antes)
        let filasPagos = '';
        listaPagos.forEach(pag => {
            filasPagos += `
            <tr class="table-success border-top border-white">
                <td colspan="2" class="small fw-bold px-3">PAGO RECIBIDO</td>
                <td class="text-end fw-bold text-success">$ ${parseFloat(pag.monto || 0).toLocaleString('es-MX', {minimumFractionDigits:2})}</td>
                <td colspan="2" class="small text-muted text-end pe-3">
                    ${pag.fecha || ''} | POR: ${pag.usuario_recibio || 'S/N'}
                </td>
            </tr>`;
        });

        // Estructura de la Tarjeta Folio
        html += `
        <tr>
            <td class="p-0 border-0">
                <div class="card m-3 border-0 shadow-sm overflow-hidden" style="border-radius: 12px;">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom">
                        <div>
                            <span class="badge bg-primary px-3 rounded-pill">FOLIO: ${v.folio}</span>
                            <small class="ms-2 text-muted">${v.fecha}</small>
                        </div>
                        <div class="text-end">
                            <span class="badge ${saldo > 0 ? 'bg-danger' : 'bg-success'} rounded-pill px-3">
                                SALDO FOLIO: $ ${saldo.toLocaleString('es-MX', {minimumFractionDigits:2})}
                            </span>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-sm mb-0">
                            <thead class="bg-light small text-uppercase" style="font-size: 0.75rem;">
                                <tr>
                                    <th class="ps-3">Concepto / Producto</th>
                                    <th class="text-center">Cant.</th>
                                    <th class="text-end">Precio Venta</th>
                                    <th class="text-center">Lote</th>
                                    <th class="text-end pe-3">Costo Adq.</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${filasProductos}
                                ${filasPagos}
                            </tbody>
                        </table>
                    </div>
                </div>
            </td>
        </tr>`;
    });

    $('#bodyFolios').html(html);
    $('#txtSaldoGlobal').text(`$ ${totalDeudaGlobal.toLocaleString('es-MX', {minimumFractionDigits:2})}`);
}  function prepararTimelinePagos(expediente) {
        let pagos = [];
        expediente.forEach(v => {
            if (v.pagos) {
                v.pagos.forEach(p => {
                    pagos.push({ monto: p.monto, fecha: p.fecha, folio: v.folio, usuario: p.usuario_recibio });
                });
            }
        });
        pagos.sort((a, b) => new Date(b.fecha) - new Date(a.fecha));

        let html = pagos.length ? '' : '<p class="text-center text-muted">Sin abonos.</p>';
        pagos.forEach(p => {
            html += `
            <div class="d-flex mb-3 border-bottom pb-2">
                <div class="text-success me-3"><i class="bi bi-plus-circle-fill"></i></div>
                <div class="w-100">
                    <div class="d-flex justify-content-between">
                        <span class="fw-bold">$ ${parseFloat(p.monto).toLocaleString()}</span>
                        <small class="text-muted">${p.fecha}</small>
                    </div>
                    <small class="d-block text-secondary">Venta: ${p.folio} | ${p.usuario || 'S/U'}</small>
                </div>
            </div>`;
        });
        $('#timelinePagos').html(html);
    }

    function prepararEntregasPendientes(expediente) {
        let html = '';
        expediente.forEach(v => {
            (v.productos || []).forEach(p => {
                const cant = parseFloat(p.cantidad || 0);
                const surtido = parseFloat(p.cantidad_entregada || 0);
                if (cant > surtido) {
                    html += `
                    <tr>
                        <td>${p.producto}</td>
                        <td class="text-center">${cant}</td>
                        <td class="text-center">${surtido}</td>
                        <td class="text-center fw-bold text-primary">${cant - surtido}</td>
                        <td><span class="badge bg-light text-dark">${v.folio}</span></td>
                    </tr>`;
                }
            });
        });
        $('#bodyEntregas').html(html || '<tr><td colspan="5" class="text-center py-4">Todo entregado.</td></tr>');
    }
    </script>
</body>
</html>