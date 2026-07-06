<?php
/**
 * CF SYSTEM - Logística Híbrida
 * Vista con Tabla Bootstrap Nativa y Paginación Clásica.
 */
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?= $es_supervisor ? 'Monitor Global' : 'Mis Repartos' ?> | cfsistem</title>
    
     <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
   <?php require_once __DIR__ . '/layout/icono.php' ?>
    <?php if (function_exists('cargarEstilos')) { cargarEstilos(); } ?>
    
    <style>
        :root { 
            --apple-bg: #f5f5f7;
            --accent-blue: #007aff;
            --sidebar-width: 260px;
        }

        body { 
            background-color: var(--apple-bg); 
            font-family: 'SF Pro Display', -apple-system, sans-serif;
            color: #1d1d1f;
        }

        .main-wrapper { 
            margin-left: var(--sidebar-width); 
            padding: 30px; 
            padding-top: 90px; 
            min-height: 100vh;
        }

        .card-ios {
            background: #ffffff;
            border-radius: 18px;
            border: none;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            margin-bottom: 25px;
            overflow: hidden;
        }

        .header-premium {
            background: #1d1d1f;
            color: white;
            padding: 15px 20px;
        }

        /* Ajustes para Tabla Bootstrap Nativa */
        .table thead th {
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.02em;
            color: #8e8e93;
            background-color: #f9f9fb;
            border-bottom: 1px solid #dee2e6;
        }

        .table-hover tbody tr:hover {
            background-color: rgba(0, 122, 255, 0.03);
        }

        .carga-scroll {
            background: #f5f5f7; 
            border-radius: 8px; 
            padding: 6px;
            font-size: 0.75rem; 
            max-height: 60px; 
            overflow-y: auto;
        }

        /* Paginación Bootstrap Custom */
        .pagination .page-link {
            color: var(--accent-blue);
            border: 1px solid #dee2e6;
            padding: 0.4rem 0.8rem;
            font-size: 0.85rem;
        }

        .pagination .page-item.active .page-link {
            background-color: var(--accent-blue);
            border-color: var(--accent-blue);
            color: white;
        }

        @media (max-width: 992px) {
            .main-wrapper { margin-left: 0; padding: 15px; padding-top: 80px; }
        }
    </style>
</head>
<body>
    <?php if (function_exists('renderizarLayout')) { renderizarLayout($paginaActual); } ?>

    <main class="main-wrapper">
        
        <div class="card-ios animate__animated animate__fadeIn">
            <div class="header-premium d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold small text-uppercase">
                    <i class="bi bi-broadcast me-2 text-primary"></i> 
                    <?= $es_supervisor ? 'Unidades en Tránsito' : 'Mi Ruta Activa' ?>
                </h6>
                <button class="btn btn-sm btn-outline-light rounded-pill px-3 border-0 bg-white bg-opacity-10" onclick="cargarMonitorViajes()">
                    <i class="bi bi-arrow-repeat me-1"></i> Actualizar
                </button>
            </div>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4">Unidad / Folio</th>
                            <th>Chofer Responsable</th>
                            <th>Tripulación</th>
                            <th>Carga Actual</th>
                            <th class="text-end pe-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="bodyMonitorViajes"></tbody>
                </table>
            </div>
        </div>

        <div class="card-ios p-3 shadow-sm">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h6 class="m-0 fw-bold">
                    <i class="bi bi-clock-history me-2 text-primary"></i>
                    <?= $es_supervisor ? 'Monitor General de Entregas' : 'Mis Entregas Recientes' ?>
                </h6>
                
                 <!-- 🔥 NUEVO -->
<span class="small text-muted">Fecha de Inicio:</span>
<input
    type="date"
    id="fecha_inicio_monitor"
    value="<?= date('Y-m-01') ?>"
    class="form-control form-control-sm"
    style="width:auto;"
    onchange="cargarMonitor(1)"
>

<span class="small text-muted">Fecha de Fin:</span>
<input
    type="date"
    id="fecha_fin_monitor"
    value="<?= date('Y-m-t') ?>"
    class="form-control form-control-sm"
    style="width:auto;"
    onchange="cargarMonitor(1)"
>


                
                <?php if ($es_supervisor): ?>
                <div class="d-flex align-items-center gap-2">
                    <span class="small text-muted">Almacén:</span>
                    <select id="filtro_almacen_monitor" class="form-select form-select-sm border rounded-3" style="width: auto;" onchange="cargarMonitor(1)">
              
<?php if ($es_admin): ?><option value="0">Todos</option>  <?php endif; ?>
                        <?php if(isset($listaAlmacenes)) foreach ($listaAlmacenes as $alm): ?>
                            <option value="<?= $alm['id'] ?>"><?= $alm['nombre'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php else: ?>
                    <input type="hidden" id="filtro_almacen_monitor" value="0">
                <?php endif; ?>
            </div>
        </div>

        <div class="card-ios animate__animated animate__fadeInUp">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="font-size: 0.85rem;">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 70px;">Modo</th>
                            <th>Folio</th>
                            <th>Cliente</th>
                            <th>Producto</th>
                            <th class="text-center">Cant.</th>
                            <th>Responsable</th>
                            <th class="text-center">Fecha</th>
                            <th class="text-end pe-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tbodyMonitor"></tbody>
                </table>
            </div>
            
            <div class="card-footer bg-white py-3 border-top-0">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div class="text-muted small" id="infoConteo">
                        Cargando registros...
                    </div>
                    <nav aria-label="Navegación">
                        <ul class="pagination pagination-sm mb-0" id="paginacionMonitor"></ul>
                    </nav>
                </div>
            </div>
        </div>
    </main>

    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <?php require_once __DIR__ . '/misRpetartosComponents/repartoEvidenciaModal.php' ?>
     <?php require_once __DIR__ . '/entregasComponets/editarRepartoModal.php'; ?>

<script>
const esSupervisor = <?= json_encode($es_supervisor) ?>;
const usernamePHP = <?= json_encode($_SESSION['username'] ?? '') ?>;
const filtroNombre = usernamePHP.replace('Trabajador', '').toUpperCase();

let paginaActual = 1;
const limitePorPagina = 15;

$(document).ready(function() { 
    cargarMonitor(1); 
    cargarMonitorViajes();
});

/**
 * CARGA DEL MONITOR (TABLA PRINCIPAL)
 */function cargarMonitor(pagina = 1) {

    // Aseguramos número válido
    paginaActual = parseInt(pagina) || 1;

    const idAlmacen = $('#filtro_almacen_monitor').val();

    // Fechas
    const fecha_inicio = $('#fecha_inicio_monitor').val();
    const fecha_fin = $('#fecha_fin_monitor').val();

    // Loader
    $('#tbodyMonitor').html(
        '<tr><td colspan="8" class="text-center py-5">' +
        '<div class="spinner-border spinner-border-sm text-primary me-2"></div> Sincronizando...</td></tr>'
    );

    $.ajax({
        url: '/cfsistem/app/controllers/misRepartosController.php',
        type: 'GET',
        data: { 
            action: 'get_monitor_entregas', 
            almacen_id: idAlmacen, 
            pagina: paginaActual, 
            limite: limitePorPagina,
            fecha_inicio: fecha_inicio,
            fecha_fin: fecha_fin
        },
        dataType: 'json',

        success: function(res) {

            // 🔥 DEBUG OPCIONAL
            // console.log(res);

            if (res.success && Array.isArray(res.data) && res.data.length > 0) { 
                
                renderizarFilas(res.data); 
                
                // 🔥 Normalizamos valores (CLAVE)
                const totalPags = parseInt(res.total_pages) || 0;
                const pagAct = parseInt(res.current_page) || 1;
                const totalRecs = parseInt(res.total_records) || 0;

                // 🔥 CONTROL REAL DE PAGINACIÓN
                if (totalPags <= 1) {
                    $('#paginacionMonitor').empty();
                } else {
                    renderizarPaginacion(totalPags, pagAct);
                }

                // 🔥 Opcional: ocultar texto si solo hay 1 página
                if (totalPags <= 1) {
                    $('#infoConteo').html(`Total: ${totalRecs} registros`);
                } else {
                    $('#infoConteo').html(
                        `Página <b>${pagAct}</b> de <b>${totalPags}</b> | Total: ${totalRecs} registros`
                    );
                }

            } else { 
                $('#tbodyMonitor').html(
                    '<tr><td colspan="8" class="text-center text-muted py-5">No se encontraron entregas.</td></tr>'
                ); 

                $('#paginacionMonitor').empty();
                $('#infoConteo').empty();
            }
        },

        error: () => {
            $('#tbodyMonitor').html(
                '<tr><td colspan="8" class="text-center text-danger py-5">Error al conectar con el servidor.</td></tr>'
            );

            $('#paginacionMonitor').empty();
            $('#infoConteo').empty();
        }
    });
}/**
 * DIBUJA LOS BOTONES DE PAGINACIÓN (BOOTSTRAP)
 */
function renderizarPaginacion(total, actual) {

    // 🔥 SI SOLO HAY 1 PÁGINA, NO MOSTRAR NADA
    if (!total || total <= 1) {
        $('#paginacionMonitor').empty();
        return;
    }

    let html = '';
    
    // Botón Anterior
    const claseAnt = (actual <= 1) ? 'disabled' : '';
    const clickAnt = (actual > 1) ? `onclick="cargarMonitor(${actual - 1})"` : '';
    
    html += `<li class="page-item ${claseAnt}">
                <a class="page-link" href="javascript:void(0)" ${clickAnt}>&laquo;</a>
             </li>`;

    // Páginas
    for (let i = 1; i <= total; i++) {
        if (i === 1 || i === total || (i >= actual - 2 && i <= actual + 2)) {
            html += `<li class="page-item ${i === actual ? 'active' : ''}">
                        <a class="page-link" href="javascript:void(0)" onclick="cargarMonitor(${i})">${i}</a>
                     </li>`;
        } else if (i === actual - 3 || i === actual + 3) {
            html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
        }
    }

    // Botón Siguiente
    const claseSig = (actual >= total) ? 'disabled' : '';
    const clickSig = (actual < total) ? `onclick="cargarMonitor(${actual + 1})"` : '';

    html += `<li class="page-item ${claseSig}">
                <a class="page-link" href="javascript:void(0)" ${clickSig}>&raquo;</a>
             </li>`;
             
    $('#paginacionMonitor').html(html);
}
/**
 * RENDERIZA FILAS DE LA TABLA
 */
function renderizarFilas(data) {
    let html = '';
    data.forEach(row => {
        if (!esSupervisor) {
            const resp = (row.responsable || '').toUpperCase();
            if (!resp.includes(filtroNombre)) return;
        }

        const icon = (row.tipo_salida === 'RUTA') ? '🚚' : '🏬';
        const folio = row.numero_ruta || row.reparto_id;
        
        html += `
            <tr class="align-middle">
                <td class="text-center">${icon}</td>
                <td>
                    <span class="fw-bold d-block">#${folio}</span>
                    <small class="badge bg-light text-dark border" style="font-size:0.6rem">${row.tipo_salida}</small>
                </td>
                <td><div class="text-truncate" style="max-width:150px;">${row.cliente_display}</div></td>
                <td><div class="text-truncate" style="max-width:150px;">${row.producto_nombre}</div></td>
                <td class="text-center"><b>${row.total_bultos || row.lectura_fisica}</b></td>
                <td><small class="text-uppercase">${row.responsable || '---'}</small></td>
                <td class="text-center text-muted"><small>${row.fecha_evento || '---'}</small></td>
                <td class="text-end pe-4">
                    <button class="btn btn-sm btn-dark rounded-pill px-3 fw-bold" 
                            onclick="verEvidenciasPorFolio('${folio}')" 
                            style="font-size: 0.65rem;">
                        <i class="bi bi-images me-1"></i> EVIDENCIAS
                    </button>
                </td>
            </tr>`;
    });
    $('#tbodyMonitor').html(html);
}

/**
 * CARGA DE UNIDADES ACTIVAS (FETCH)
 */
window.cargarMonitorViajes = async function() {
    const body = $('#bodyMonitorViajes');
    try {
        body.html('<tr><td colspan="5" class="text-center py-4"><div class="spinner-border spinner-border-sm text-primary"></div></td></tr>');
        const resp = await fetch(`/cfsistem/app/controllers/misRepartosController.php?action=listar_viajes_activos`);
        const res = await resp.json();
        console.log(res.data);
        const filtrados = esSupervisor ? res.data : (res.data || []).filter(v => 
            (v.chofer || '').toUpperCase().includes(filtroNombre) || (v.tripulantes || '').toUpperCase().includes(filtroNombre)
        );

        if (!filtrados || filtrados.length === 0) {
            body.html('<tr><td colspan="5" class="text-center py-4 text-muted small">No hay unidades activas en este momento.</td></tr>');
            return;
        }

        body.empty();
        filtrados.forEach(v => {
            body.append(`
                <tr class="animate__animated animate__fadeIn">
                    <td class="ps-4">
                        <div class="fw-bold">${v.unidad}</div>
                        <span class="badge bg-dark-subtle text-dark" style="font-size:0.65rem">Folio: #${v.viaje_folio}</span>
                    </td>
                    <td><div class="small fw-bold text-uppercase"><i class="bi bi-person-circle me-1 text-primary"></i> ${v.chofer}</div></td>
                    <td><small class="text-muted">${v.tripulantes || 'Solo Conductor'}</small></td>
                    <td><div class="carga-scroll" style="background: #f5f5f7; border-radius: 8px; padding: 6px; font-size: 0.75rem; max-height: 60px; overflow-y: auto;">${v.detalles_carga}</div></td>
                    <td class="text-end pe-4">
                      <button class="btn btn-sm btn-light border-0" onclick="abrirModalEdicionViaje('${v.viaje_folio}', ${v.vehiculo_id}, ${v.chofer_id})" style="border-radius: 10px; color: #007aff; background: #f2f2f7;"><i class="bi bi-pencil-square"></i></button>
                          
                        <a href="/cfsistem/app/controllers/gestionarRepartoController.php?folio=${v.viaje_folio}" 
                           class="btn btn-sm btn-primary rounded-pill px-3 fw-bold shadow-sm" style="font-size: 0.7rem;">
                            <i class="bi bi-camera-fill me-1"></i> GESTIONAR
                        </a>
                         <button class="btn btn-finish btn-sm d-flex align-items-center justify-content-center" 
                                    onclick="finalizar(${v.vehiculo_id}, '${v.viaje_folio}')"
                                    style="background: #14c41d; color: #fff; border: none; border-radius: 10px; padding: 6px 14px; font-weight: 600; font-size: 0.68rem;">
                                <i class="bi bi-check2-all me-1"></i> FINALIZAR
                            </button>
                    </td>
                </tr>
            `);
        });
    } catch (e) { body.html('<tr><td colspan="5" class="text-center text-danger py-4">Error de conexión</td></tr>'); }
};
async function finalizar (vehiculoId, folioRuta) {
     
         const container = document.getElementById('contenedor-entregas');
    fetch(`/cfsistem/app/controllers/gestionarRepartoController.php?action=get_entregas_folio&folio=${folioRuta}`)
        .then(res => res.json())
        .then(res => {
          
            console.log(res.data);
            datosTemporales = res.data || [];

            if(datosTemporales.length === 0) {
                    return;
            }
            let proseguir=1;

            datosTemporales.forEach((item, index) => {
            
              if (item.foto_registrada == null && item.nota_registrada == null) {

    Swal.fire({
        icon: 'warning',
        title: 'Evidencias pendientes',
        html: `
            La entrega <b>${item.id_venta}</b> no tiene foto ni nota registradas.
        `,
        confirmButtonText: 'Entendido'
    });

    proseguir = 0;
    return;
}
              if(proseguir==1)
              {
                finalizarViaje(vehiculoId, folioRuta);
     
              }
             
                
               
            });
        })
        .catch(err => {
            console.error(err);
                });
                   
    };
    window.finalizarViaje = async function(vehiculoId, folioRuta) {
       const result = await Swal.fire({
    title: '¿Finalizar viaje?',
    text: `¿Confirmar llegada de la unidad ${folioRuta}?`,
    icon: 'question',
    showCancelButton: true,
    confirmButtonText: 'Sí, finalizar',
    cancelButtonText: 'Cancelar',
    confirmButtonColor: '#198754'
});

if (!result.isConfirmed) return;

try {

    const formData = new FormData();
    formData.append('vehiculo_id', vehiculoId);
    formData.append('viaje_folio', folioRuta);

    const resp = await fetch(
        '/cfsistem/app/controllers/repartosController.php?action=finalizar_viaje',
        {
            method: 'POST',
            body: formData
        }
    );

    const res = await resp.json();

    if (res.success) {

        await Swal.fire({
            icon: 'success',
            title: 'Éxito',
            text: res.message,
            timer: 2000,
            showConfirmButton: false
        }).then(() => location.reload())

       

    } else {

        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: res.message || 'No se pudo finalizar el viaje'
        });

    }

} catch (e) {

    console.error(e);

    Swal.fire({
        icon: 'error',
        title: 'Error',
        text: 'Ocurrió un problema al comunicarse con el servidor'
    });

}
    };


</script>
</body>
</html>