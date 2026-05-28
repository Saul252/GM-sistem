<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expediente: <?= htmlspecialchars($cliente['nombre_comercial']) ?> | CF System</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <style>
        :root {
            --bs-primary: #007aff;
            --bs-info: #3abaf4;
            --bs-success: #1cc88a;
            --bs-danger: #e74a3b;
            --bs-warning: #f6c23e;
            --bg-light: #f8f9fc;
        }

        body { background-color: var(--bg-light); font-family: 'Inter', sans-serif; color: #4e73df; }
        .header-expediente { background: white; border-bottom: 1px solid #e3e6f0; padding: 1rem 2rem; }

        .kpi-widget {
            background: white; border-radius: 12px; padding: 1.25rem; border: none;
            border-left: 4px solid #e3e6f0; box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1); height: 100%;
        }
        .kpi-label { font-size: 0.7rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 5px; }
        .kpi-value { font-size: 1.4rem; font-weight: 700; color: #5a5c69; }
        
        .border-left-primary { border-left-color: var(--bs-primary) !important; }
        .border-left-success { border-left-color: var(--bs-success) !important; }
        .border-left-danger { border-left-color: var(--bs-danger) !important; }
        .border-left-info { border-left-color: var(--bs-info) !important; }

        .folio-container { background: white; border-radius: 12px; border: 1px solid #e3e6f0; margin-bottom: 2rem; overflow: hidden; }
        
        .folio-debe { border-left: 5px solid var(--bs-danger); }
        .folio-liquidado { border-left: 5px solid var(--bs-success); background-color: #f6fff9; }
        .folio-favor { border-left: 5px solid var(--bs-info); background-color: #f0f7ff; }
        .folio-cancelado { border-left: 5px solid #858796; background-color: #f8f9fc; }

        .folio-header { background-color: rgba(0,0,0,0.02); padding: 1rem 1.5rem; border-bottom: 1px solid #e3e6f0; }
        .col-pagos { background-color: #fafbfc; border-left: 1px solid #e3e6f0; padding: 1.5rem; }
        .payment-pill { background: white; border: 1px solid #e3e6f0; border-left: 4px solid var(--bs-success); border-radius: 8px; padding: 10px; margin-bottom: 8px; }
    </style>
</head>
<body>

<header class="header-expediente shadow-sm mb-4">
    <div class="container-fluid d-flex justify-content-between align-items-center">
        <div>
            <h4 class="fw-bold mb-0 text-dark"><?= htmlspecialchars($cliente['nombre_comercial']) ?></h4>
            <span class="badge bg-primary-subtle text-primary">RFC: <?= htmlspecialchars($cliente['rfc']) ?></span>
        </div>
        <a href="/cfsistem/app/controllers/clientesEstatusController.php" class="btn btn-sm btn-outline-secondary rounded-pill">
            <i class="bi bi-arrow-left"></i> Volver al Listado
        </a>
        <div style="display:flex; gap:10px; align-items:end; margin-bottom:15px; flex-wrap:wrap;">

    <div>
        <label style="font-size:12px;">Fecha inicio</label>
        <input type="date" id="fecha_inicio" class="form-control">
    </div>

    <div>
        <label style="font-size:12px;">Fecha fin</label>
        <input type="date" id="fecha_fin" class="form-control">
    </div>

    <button class="btn btn-primary" onclick="filtrarExpediente()">
        Filtrar
    </button>

   

</div>
     <button class="btn btn-dark btn-sm" onclick="imprimirEstadoCuenta()">
    <i class="bi bi-printer"></i> Imprimir
</button>
    </div>
</header>


<div class="container-fluid px-4">

    <?php if ($resumen['saldo_total'] < -0.01): ?>
    <div class="alert alert-info border-0 shadow-sm mb-4 d-flex align-items-center p-3" style="border-radius: 12px;">
        <i class="bi bi-info-square-fill fs-3 me-3 text-info"></i>
        <div>
            <h5 class="mb-0 fw-bold">Saldo a Favor General</h5>
            <span>El cliente tiene <b>$ <?= number_format(abs($resumen['saldo_total']), 2) ?></b> disponible.</span>
        </div>
    </div>
    <?php endif; ?>
    <div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="kpi-widget border-left-primary">
            <div class="kpi-label text-primary">Compras Totales</div>
            <div class="kpi-value">$ <?= number_format($resumen['total_comprado'], 2) ?></div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="kpi-widget border-left-success">
            <div class="kpi-label text-success">Total Pagado</div>
            <div class="kpi-value">$ <?= number_format($resumen['total_pagado'], 2) ?></div>
        </div>
    </div>

    <?php 
        $saldoReal = $resumen['saldo_total']; 
        $esSaldoAFavor = $saldoReal < -0.01;
        $claseColor = $esSaldoAFavor ? 'border-left-info' : 'border-left-danger';
    ?>

    <div class="col-md-3">
        <div class="kpi-widget <?= $claseColor ?>">
            <div class="kpi-label <?= $esSaldoAFavor ? 'text-info' : 'text-danger' ?>"><?= $esSaldoAFavor ? 'A Favor' : 'Saldo Pendiente' ?></div>
            <div class="kpi-value <?= $esSaldoAFavor ? 'text-info' : 'text-danger' ?>">
                $ <?= number_format(abs($saldoReal), 2) ?>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="kpi-widget d-flex align-items-center justify-content-between border-left-primary">
            <div style="width: 60px; height: 60px;"><canvas id="chartDona"></canvas></div>
            <div class="text-end">
                <div class="kpi-label">Estatus</div>
                <div class="fw-bold text-dark"><?= round(($resumen['total_pagado'] / max($resumen['total_comprado'], 1)) * 100) ?>%</div>
            </div>
        </div>
    </div>

    
  
 

    <h5 class="fw-bold mb-3 text-dark">Folios Detallados</h5>
    

    <?php foreach ($expediente as $v): 
        // USAMOS EL ID REAL (venta_id o id como fallback)
        $idActual = $v['venta_id'] ?? $v['id'];
        
        $esCancelada = (isset($v['estado_general']) && $v['estado_general'] == 'cancelada');
        $saldoFolio = floatval($v['total']) - floatval($v['total_pagado']);
        $folioLiquidado = abs($saldoFolio) <= 0.01;
        $folioAFavorIndividual = $saldoFolio < -0.01;
        
        $claseStatus = "folio-debe"; 
        if ($esCancelada) $claseStatus = "folio-cancelado";
        elseif ($folioAFavorIndividual) $claseStatus = "folio-favor";
        elseif ($folioLiquidado) $claseStatus = "folio-liquidado";
    ?>
    <div class="folio-container shadow-sm <?= $claseStatus ?>">
        
        <div class="folio-header d-flex justify-content-between align-items-center">
            <div>
                <span class="fw-bold <?= $esCancelada ? 'text-muted text-decoration-line-through' : 'text-dark' ?>">#<?= $idActual ?></span>
                <small class="text-muted ms-3"><?= date('d/m/Y', strtotime($v['fecha'])) ?></small>
                <span class="badge bg-light text-dark border ms-2"><?= htmlspecialchars($v['folio'] ?? 'S/F') ?></span>
            </div>
            <div class="d-flex align-items-center">
                <?php if ($esCancelada): ?>
                    <span class="badge bg-secondary rounded-pill px-3">CANCELADA</span>
                <?php elseif ($folioAFavorIndividual): ?>
                    <span class="badge bg-info rounded-pill px-3">A FAVOR: $ <?= number_format(abs($saldoFolio), 2) ?></span>
                <?php elseif ($folioLiquidado): ?>
                    <span class="badge bg-success rounded-pill px-3"><i class="bi bi-check-lg"></i> LIQUIDADO</span>
                <?php else: ?>
                   <button type="button" class="btn btn-primary btn-sm px-4 shadow-sm" 
                           onclick="abrirFlujoAbono(<?= intval($idActual) ?>, <?= intval($v['cliente_id']) ?>, '<?= $v['folio'] ?>', <?= floatval($saldoFolio) ?>)">
                        <i class="bi bi-plus-circle me-1"></i> ABONAR
                   </button>
                <?php endif; ?>
            </div>
        </div>

        <div class="row g-0">
            <div class="col-md-8 p-3">
                <table class="table table-sm table-borderless align-middle mb-0" style="font-size: 0.85rem;">
                    <thead class="text-muted border-bottom">
                        <tr><th>Fecha</th><th class="text-center">Folio </th><th class="text-end">SUBTOTAL</th><th>Tickets</th></tr>
                    </thead>
                    <tbody>
                        
                            <tr>
                               <td class="text-center">
    <?= $v['fecha'] ?? 0; ?>
</td></td>
                                <td>
                                    
                                    <div class="text-muted" style="font-size: 0.7rem;"> <?=  $v['venta_id'] ?? $v['id'];?></div>
                                </td>
                                           
                                <td class="text-end fw-bold">$ <?=   $v['total'] ?? 0;?></td>
                                <td>
                                    <a class="btn btn-sm btn-primary shadow-sm" href="/cfsistem/app/backend/ventas/ticket_venta.php?id=<?=  $v['venta_id'] ?? $v['id'];?>" target="_blank">
                            <i class="bi bi-currency-dollar"></i> Ticket
                        </a>
                        <a class="btn btn-sm btn-info text-white shadow-sm" href="/cfsistem/app/backend/ventas/ticket_sin_precio.php?id=<?=  $v['venta_id'] ?? $v['id'];?>" target="_blank" title="Imprimir Remisión sin Precios">
                            <i class="bi bi-file-earmark-text"></i> Remisión
                        </a>

                                </td>
                            </tr>
                          
                    </tbody>
                </table>
            </div>

            <div class="col-md-4 col-pagos">
                <p class="kpi-label mb-2">Flujo de Dinero</p>
                <div class="pagos-lista" style="max-height: 150px; overflow-y: auto;">
                    <?php if(empty($v['pagos'])): ?>
                        <p class="text-muted small">Sin registros de pago.</p>
                    <?php else: ?>
                        <?php foreach($v['pagos'] as $pago): ?>
                        <div class="payment-pill d-flex justify-content-between align-items-center shadow-sm">
                            <div>
                                <span class="fw-bold text-success" style="font-size: 0.85rem;">$ <?= number_format($pago['monto'], 2) ?></span><br>
                                <span class="text-muted" style="font-size: 0.65rem;">
                                    <i class="bi bi-calendar-event"></i> <?= date('d/m/y', strtotime($pago['fecha'])) ?>
                                </span>
                            </div>
                            <i class="bi bi-person-check text-muted" title="Recibió: <?= htmlspecialchars($pago['usuario_recibio']) ?>"></i>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                
                <div class="pt-2 mt-2 border-top">
                    <div class="d-flex justify-content-between small"><span>Total Folio:</span><b>$ <?= number_format($v['total'], 2) ?></b></div>
                    <div class="d-flex justify-content-between small text-success"><span>Abonado:</span><b>$ <?= number_format($v['total_pagado'], 2) ?></b></div>
                    <div class="d-flex justify-content-between border-top mt-1 pt-1">
                        <span class="small fw-bold">Balance:</span>
                        <b class="<?= ($saldoFolio < -0.01) ? 'text-info' : 'text-danger' ?>">
                            $ <?= number_format(abs($saldoFolio), 2) ?> 
                        </b>         
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<?php require_once __DIR__ . '/../ventasHistorialModales/registarAbonoCliente.php' ?>

<script>
$(document).ready(function() { 
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
            plugins: { legend: { display: false } },
            responsive: true,
            maintainAspectRatio: false
        }
    });
}
function abrirModalSaldarFavor(favorDisponible, deudaPendiente) {
    // Calculamos el límite: no podemos usar más de lo que hay, ni pagar más de lo que se debe
    const montoMaximo = Math.min(favorDisponible, deudaPendiente);

    Swal.fire({
        title: 'Compensación de Saldos',
        icon: 'info',
        html: `
            <div class="text-start border-bottom pb-2 mb-3" style="font-size: 0.9rem;">
                <div class="d-flex justify-content-between mb-1">
                    <span>Saldo a Favor:</span>
                    <b class="text-success">$ ${favorDisponible.toLocaleString('es-MX', {minimumFractionDigits: 2})}</b>
                </div>
                <div class="d-flex justify-content-between">
                    <span>Deuda en Contra:</span>
                    <b class="text-danger">$ ${deudaPendiente.toLocaleString('es-MX', {minimumFractionDigits: 2})}</b>
                </div>
            </div>
            <div class="text-start">
                <label class="form-label fw-bold small">Monto a compensar:</label>
                <div class="input-group">
                    <span class="input-group-text">$</span>
                    <input type="number" id="monto_cruce" class="form-control form-control-lg fw-bold" 
                           value="${montoMaximo.toFixed(2)}" 
                           max="${montoMaximo}" min="0.01" step="0.01">
                </div>
                <p class="text-muted mt-2" style="font-size: 0.75rem;">
                    <i class="bi bi-info-circle"></i> Este ajuste restará el monto de ambas cuentas para limpiar el historial del cliente.
                </p>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'Aplicar Ajuste',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#f6c23e',
        reverseButtons: true,
        preConfirm: () => {
            const monto = document.getElementById('monto_cruce').value;
            if (!monto || monto <= 0 || monto > montoMaximo) {
                Swal.showValidationMessage(`Monto inválido. Máximo permitido: $${montoMaximo.toFixed(2)}`);
            }
            return monto;
        }
    }).then((result) => {
        if (result.isConfirmed) {
            procesarAjusteContable(result.value);
        }
    });
}

function procesarAjusteContable(monto) {
    Swal.fire({ 
        title: 'Procesando ajuste...', 
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading() 
    });

    // Ajustamos la llamada para que coincida con tu estructura de 'accion'
    $.post('/cfsistem/app/controllers/clienteExpedienteController.php', {
        accion: 'saldar_deuda_con_favor', // El nombre del case que definimos
       id_cliente: <?= json_encode($id_cliente ?? 0) ?>,
        monto_a_usar: monto
    }, function(res) {
        if(res.status === 'success') {
            Swal.fire({
                title: '¡Éxito!',
                text: res.message,
                icon: 'success',
                confirmButtonColor: '#007aff'
            }).then(() => {
                location.reload(); // Recarga para actualizar los KPIs con los nuevos saldos
            });
        } else {
            Swal.fire('Error', res.message || 'Error al procesar el ajuste.', 'error');
        }
    }, 'json').fail(function() {
        Swal.fire('Error', 'No se pudo conectar con el servidor.', 'error');
    });
}





</script>
<script>
    async function imprimirEstadoCuenta() {
           const urlParams = new URLSearchParams(window.location.search);
const id = urlParams.get('id');

console.log("ID:", id);



       const fechaInicio = document.getElementById('fecha_inicio').value;
    const fechaFin    = document.getElementById('fecha_fin').value;

    const res = await fetch(
        `/cfsistem/app/controllers/clienteExpedienteController.php?action=getEstadoCuentaCliente&id_cliente=${id}&fecha_inicio=${fechaInicio}&fecha_fin=${fechaFin}`
    );

   

/**
 * AUTO CARGA
 */


    const data = await res.json();

    console.log("DATA:", data);

    
    

   

    if (!data || data.status !== 'success') {
        Swal.fire('Error', 'No se pudo cargar el estado de cuenta', 'error');
        return;
    }

    const { cliente, expediente = [], resumen = {} } = data;

    const w = window.open('', '_blank', 'width=1100,height=700');

    if (!w) {
        Swal.fire('Error', 'El navegador bloqueó la ventana emergente', 'error');
        return;
    }

    let html = '';

   expediente.forEach(v => {

    const deuda = (parseFloat(v.total) || 0) - (parseFloat(v.total_pagado) || 0);

    let productos = '';
    (v.productos || []).forEach(p => {
        
    });

  let pagos = '';
let saldo = parseFloat(v.total) || 0;
const diasTranscurridos = (fecha) => {
    const inicio = new Date(fecha);
    const hoy = new Date();
    return Math.floor((hoy - inicio) / (1000 * 60 * 60 * 24));
};

(v.pagos || []).forEach((p, index) => {
    const monto = parseFloat(p.monto) || 0;
    saldo -= monto;

    pagos += `
        <tr>
            ${
                index === 0
                ? `
                    <td rowspan="${v.pagos.length}">${v.fecha}<br>
                        <small>${diasTranscurridos(v.fecha)} días</small>
                    </td>
                    <td rowspan="${v.pagos.length}">${v.folio}</td>
                    <td rowspan="${v.pagos.length}" style="text-align:right;">
                        $${parseFloat(v.total).toFixed(2)}
                    </td>
                `
                : ''
            }

            <td>${p.fecha}</td>
            <td>${p.metodo_pago || 'N/A'}</td>
            <td style="text-align:right;">$${monto.toFixed(2)}</td>
            <td style="text-align:right; font-weight:bold; color:#dc2626;">
                $${saldo.toFixed(2)}
            </td>
        </tr>
    `;
});

    html += `
    <div style="
        margin-bottom:20px;
        border:1px solid #e5e7eb;
        border-radius:10px;
        overflow:hidden;
        font-family: Arial;
        background:#fff;
    ">

        <!-- ENCABEZADO DEL FOLIO -->
      
     

       

        <!-- Informacion -->
        <div ">
          
<table width="100%" cellpadding="6" cellspacing="0" style="font-size:13px;">
    <thead style="background:#f9fafb;">
        <tr>
            <th style="text-align:left;">Fecha</th>
            <th style="text-align:left;">Folio de venta</th>
            <th style="text-align:right;">Total Compra</th>
            <th style="text-align:left;">Fecha de pago</th>
            <th style="text-align:left;">Método</th>
            <th style="text-align:right;">Monto</th>
            <th style="text-align:right;">Saldo</th>
        </tr>
    </thead>

    <tbody>
        ${pagos}
    </tbody>
</table>
        </div>

    </div>
    `;
});
    const doc = `
        <html>
        <head>
            <title>Estado de Cuenta</title>
            <style>
                body { font-family: Arial; font-size: 12px; padding: 20px; }
                table { border-collapse: collapse; width: 100%; }
                th, td { border: 1px solid #ccc; padding: 5px; }
                th { background: #eee; }
                h2 { margin-bottom: 5px; }
            </style>
        </head>
        <body>

          


        








        <div style="
    font-family: Arial, sans-serif;
    border-bottom: 2px solid #007aff;
    padding-bottom: 12px;
    margin-bottom: 20px;
">

    <h2 style="
        margin: 0;
        font-size: 20px;
        color: #1f2937;
    ">
        ${cliente.nombre_comercial}
    </h2>

    <p style="
        margin: 4px 0;
        color: #6b7280;
        font-size: 12px;
    ">
        RFC: <b style="color:#111827">${cliente.rfc}</b>
    </p>

    <p style="
        margin: 4px 0 0;
        color: #6b7280;
        font-size: 12px;
    ">
        Dirección: <b style="color:#111827">${cliente.direccion}</b>
    </p>
</div>

<!-- KPIs -->
<div style="
    display: flex;
    gap: 10px;
    margin-bottom: 20px;
    font-family: Arial;
">

    <div style="
        flex: 1;
        background: #f3f4f6;
        border-left: 4px solid #3b82f6;
        padding: 10px;
        border-radius: 8px;
    ">
        <div style="font-size: 10px; color: #6b7280;">TOTAL COMPRADO</div>
        <div style="font-size: 16px; font-weight: bold; color:#111827;">
            $${(resumen.total_comprado || 0).toLocaleString('es-MX', {minimumFractionDigits:2})}
        </div>
    </div>

    <div style="
        flex: 1;
        background: #f3f4f6;
        border-left: 4px solid #10b981;
        padding: 10px;
        border-radius: 8px;
    ">
        <div style="font-size: 10px; color: #6b7280;">TOTAL PAGADO</div>
        <div style="font-size: 16px; font-weight: bold; color:#111827;">
            $${(resumen.total_pagado || 0).toLocaleString('es-MX', {minimumFractionDigits:2})}
        </div>
    </div>

    <div style="
        flex: 1;
        background: #f3f4f6;
        border-left: 4px solid #ef4444;
        padding: 10px;
        border-radius: 8px;
    ">
        <div style="font-size: 10px; color: #6b7280;">SALDO</div>
        <div style="font-size: 16px; font-weight: bold; color:#111827;">
            $${(resumen.saldo_total || 0).toLocaleString('es-MX', {minimumFractionDigits:2})}
        </div>
    </div>

</div>

            ${html}

        </body>
        </html>
    `;

    w.document.write(doc);
    w.document.close();

    w.onload = () => {
        w.print();
    };
}
</script>
<script>
  function filtrarExpediente(){
    const fechaInicio = document.getElementById('fecha_inicio').value;
    const fechaFin    = document.getElementById('fecha_fin').value;

    const urlParams = new URLSearchParams(window.location.search);
    const id = urlParams.get('id');

    console.log("ID:", id);

    // REDIRECCIÓN
    window.location.href =
        `/cfsistem/app/controllers/clienteExpedienteController.php?id=${id}&fecha_inicio=${fechaInicio}&fecha_fin=${fechaFin}`;


  }
    
</script>

</body>
</html>