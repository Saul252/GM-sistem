<?php
/**
 * repartos_view.php 
 * Ajustada para interactuar con repartosController.php
 */
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logística | cfsistem</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    
    <?php if (function_exists('cargarEstilos')) { cargarEstilos(); } ?>
   
    <style>
        :root { 
            --sidebar-width: 260px; 
            --navbar-height: 65px;
            --glass-bg: rgba(255, 255, 255, 0.85);
            --accent-color: #007aff;
        }

        body { 
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
        }

        .main-content { 
            margin-left: var(--sidebar-width); 
            padding: 40px; 
            padding-top: calc(var(--navbar-height) + 20px); 
            transition: all 0.3s ease;
        }

        /* Card Elegante con Efecto Cristal */
        .card-premium {
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.3);
            border-radius: 24px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.05);
            padding: 30px;
        }

        /* Micro Cards Rediseñadas */
        .ios-micro-card {
            background: white;
            border-radius: 18px;
            padding: 12px 20px;
            border: none;
            box-shadow: 0 4px 15px rgba(0,0,0,0.03);
            display: flex;
            align-items: center;
            gap: 15px;
            transition: transform 0.2s;
        }
        .ios-micro-card:hover { transform: translateY(-3px); }
        .ios-icon-circle {
            width: 40px; height: 40px;
            background: #eef6ff;
            color: var(--accent-color);
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.2rem;
        }

        /* Badges de Estado Premium */
        .badge-premium {
            padding: 8px 14px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 0.72rem;
            letter-spacing: 0.5px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border: 1px solid rgba(0,0,0,0.05);
        }
        .st-disponible { background: #e6ffed; color: #1e7e34; }
        .st-ruta { background: #e8f4ff; color: #007aff; }
        .st-taller { background: #fff8e6; color: #d97706; }
        .st-fuera { background: #fff0f0; color: #d11a2a; }

        /* Estilo de Tabla */
        .table thead th {
            background: transparent;
            color: #8e8e93;
            font-weight: 700;
            font-size: 0.75rem;
            text-transform: uppercase;
            border: none;
            padding-bottom: 20px;
        }
        .table tbody tr {
            border-bottom: 1px solid #f2f2f7;
            transition: background 0.2s;
        }
        .table tbody tr:hover { background: rgba(0,122,255,0.02); }

        /* Botón Guardar Gradiente */
        .btn-gradient {
            background: linear-gradient(135deg, #007aff 0%, #0056b3 100%);
            color: white; border: none; border-radius: 12px;
            padding: 10px 25px; font-weight: 600;
            box-shadow: 0 8px 20px rgba(0,122,255,0.25);
        }
        .btn-gradient:hover { color: white; opacity: 0.9; transform: translateY(-1px); }

        @media (max-width: 768px) { .main-content { margin-left: 0; padding: 20px; padding-top: 100px; } }
    </style>
</head>
<body>
    <?php renderizarLayout($paginaActual); ?>

    <main class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold m-0">Logística de Salidas</h2>
                <p class="text-muted small mb-0">Gestión de unidades para mercancía despachada en patio</p>
            </div>

            <div class="d-flex gap-3">
                <div class="bg-white rounded-4 p-2 px-3 shadow-sm d-flex align-items-center gap-3">
                    <div class="ios-icon-circle" style="color: #ff9500;"><i class="bi bi-truck"></i></div>
                    <div>
                        <small class="text-muted fw-bold d-block" style="font-size: 0.65rem;">PENDIENTES</small>
                        <span class="fs-5 fw-bold" id="count_pendientes">0</span>
                    </div>
                </div>
                <button class="btn btn-white shadow-sm rounded-4 border px-3" onclick="cargarPendientes()">
                    <i class="bi bi-arrow-clockwise"></i>
                </button>
            </div>
        </div>

        <div class="card card-premium">
            <div class="mb-4">
                <div class="input-group shadow-sm rounded-3 overflow-hidden border">
                    <span class="input-group-text bg-white border-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" id="buscarSalida" class="form-control border-0 p-2" placeholder="Buscar por folio o producto...">
                </div>
            </div>

            <div class="table-responsive">
                <table class="table align-middle" id="tablaPrincipal">
                   <thead class="text-muted small">
    <tr>
        <th class="border-0">FOLIO / FECHA</th>
        <th class="border-0">PRODUCTO</th>
        <th class="border-0">ALMACÉN ORIGEN</th>
        <th class="border-0">DESPACHADO POR</th>
         <th class="border-0 text-center">ESTADO ENTREGA</th> 
         <th class="border-0 text-end">ACCIÓN</th>
    </tr>
</thead>
                    <tbody id="bodyPendientes">
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="spinner-border text-primary spinner-border-sm" role="status"></div>
                                <span class="ms-2 text-muted">Sincronizando con Patio...</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div id="noData" class="text-center py-5 d-none">
                <i class="bi bi-box2 text-muted opacity-25" style="font-size: 4rem;"></i>
                <p class="text-muted fw-medium mt-3">No hay productos pendientes de asignar a ruta.</p>
            </div>
        </div>

    </main>

   
    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
     <?php require_once __DIR__ . '/entregasComponets/repartoModal.php'; ?>
                   <?php require_once __DIR__ . '/entregasComponets/monitorDeRuta.php'; ?>



<script>
// 1. CONFIGURACIÓN GLOBAL
window.CONTROLLER = '/cfsistem/app/controllers/repartosController.php';

// 2. FORMATEO DE CANTIDADES (Global para que el modal lo vea)
window.formatQty = function(cantidad, factor, unidad) {
    const cant = parseFloat(cantidad);
    const fac = parseFloat(factor || 1);
    
    if(fac > 1 && cant >= fac) {
        const uReporte = Math.floor(cant / fac);
        const resto = Math.round((cant % fac) * 100) / 100;
        return `<div class="fw-bold text-dark fs-6">${uReporte} ${unidad}</div>` +
               (resto > 0 ? `<small class="text-muted">+ ${resto} pzas</small>` : '');
    }
    return `<div class="fw-bold text-dark fs-6">${cant} <small class="fw-normal text-muted">pzas</small></div>`;
};
window.cargarPendientes = async function() {
    const body = $('#bodyPendientes');
    try {
        const resp = await fetch(`${window.CONTROLLER}?action=listar_pendientes_ruta`);
        const res = await resp.json();
        
        if (!res.success) {
            body.html('<tr><td colspan="6" class="text-center py-4">Sin datos</td></tr>');
            return;
        }
        
        $('#count_pendientes').text(res.data.length);
        body.empty();

        res.data.forEach(item => {
            // Si el estado no es 'pendiente' ni 'cancelado', asumimos que está en ruta/transito
            const enRuta = (item.estado_reparto !== 'pendiente' && item.estado_reparto !== 'cancelado');

            const btnAccion = enRuta 
                ? `<button class="btn btn-info btn-sm" onclick="imprimirReparto(${item.movimiento_id})">
                        <i class="fas fa-print"></i> Imprimir
                   </button>`
                : `<button class="btn btn-gradient btn-sm" onclick="prepararModalReparto(${item.movimiento_id}, ${item.almacen_origen_id})">
            Asignar
       </button>`;

            const badge = enRuta
                ? `<span class="badge bg-info">EN RUTA</span>`
                : `<span class="badge bg-success">EN PATIO</span>`;

            body.append(`
                <tr>
                    <td>#${item.folio_venta || 'S/F'}</td>
                    <td>${item.producto}<br><small>${item.cantidad} ${item.unidad_reporte}</small></td>
                    <td>${item.almacen_origen}</td>
                    <td>${item.despacho_por || 'Sistema'}</td>
                    <td class="text-center">${badge}</td>
                    <td class="text-end">${btnAccion}</td>
                </tr>
            `);
        });
    } catch (e) { console.error(e); }
};
$(document).ready(function() {
    window.cargarPendientes();
    // Buscador
    $("#buscarSalida").on("keyup", function() {
        const value = $(this).val().toLowerCase();
        $("#bodyPendientes tr").filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
        });
    });
});
</script>
</body>
</html> 