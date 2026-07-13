<?php
/**
 * trabajadores_view.php
 * Vista de administración de personal: Filtros, CRUD por Modales y AJAX.
 */
// Definimos los roles y estados que coinciden con el ENUM de la BD para validación visual
$rolesEnum = ['administrador', 'vendedor', 'chofer', 'almacenista', 'cargador'];
$estadosEnum = ['activo', 'inactivo', 'vacaciones', 'en_ruta'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Personal | Sistema</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  
  
  <?php require_once __DIR__ . '/layout/icono.php' ?>
    <?php if (function_exists('cargarEstilos')) { cargarEstilos(); } ?>
     <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <style>
        :root { --sidebar-width: 260px; --navbar-height: 65px; }
        body { background-color: #f4f7f6; }
        .main-content { margin-left: var(--sidebar-width); padding: 40px; padding-top: calc(var(--navbar-height) + 20px); }
        .card-table { border: none; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); background: white; }
        
        /* Estilo Micro-Widget iOS */
        .ios-micro-card {
            background: #ffffff !important;
            border-radius: 12px !important;
            border: 1px solid rgba(0,0,0,0.05) !important;
            padding: 4px 10px !important;
            min-width: 85px !important;
            box-shadow: 0 2px 4px rgba(0,0,0,0.02) !important;
            display: flex !important;
            flex-direction: column !important;
            justify-content: center !important;
            border-left: 3px solid #34c759 !important; /* Verde iOS */
        }
        .ios-m-label { 
            color: #8e8e93; font-size: 0.55rem; font-weight: 700; 
            text-transform: uppercase; letter-spacing: 0.05em; line-height: 1.1; margin: 0;
        }
        .ios-m-value { 
            color: #1c1c1e; font-size: 1rem; font-weight: 700; 
            letter-spacing: -0.02em; line-height: 1; margin-top: 1px;
        }

        @media (max-width: 768px) { 
            .main-content { margin-left: 0; padding: 20px; padding-top: 90px; } 
        }
    </style>
</head>
<body>
    <?php if (function_exists('renderizarLayout')) { renderizarLayout($paginaActual); } ?>

    <main class="main-content">
        <div class="d-flex justify-content-between align-items-center flex-wrap mb-4" style="gap: 15px; width: 100%;">
            <div style="flex: 1; min-width: 200px;">
                <h2 class="fw-bold m-0 text-uppercase" style="letter-spacing: -0.02em; color: #1c1c1e;">Gestión de SALARIOS</h2>
                 </div>

            <div class="d-flex align-items-center" style="gap: 12px;">
                <div class="ios-micro-card">
                    <p class="ios-m-label">Staff Total</p>
                    <div class="ios-m-value" id="conteoTrabajadores">
                        <?= count($trabajadores) ?>
                    </div>
                </div>

                <button class="btn btn-primary rounded-pill px-4 shadow-sm" onclick="nuevoTrabajador()" style="height: 34px; font-weight: 600; font-size: 0.85rem;">
                    <i class="bi bi-person-plus-fill me-1"></i> Agregar
                </button><button class="btn btn-primary rounded-pill px-4 shadow-sm" onclick="imprimirContenidoModal()" style="height: 34px; font-weight: 600; font-size: 0.85rem;">
                    <i class="bi bi-person-plus-fill me-1"></i> Imprimir Nomina
                </button>
            </div>
        </div>

        <div class="card card-table p-4">
            <div class="row mb-4 g-3">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                        <input type="text" id="busquedaTrabajador" class="form-control border-start-0" placeholder="Buscar por nombre o teléfono...">
                    </div>
                </div>
                <div class="col-md-3">
                    <select id="filtroRol" class="form-select">
                        <option value="">Todos los Roles</option>
                        <?php foreach($rolesEnum as $rol): ?>
                            <option value="<?= $rol ?>"><?= ucfirst($rol) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <button class="btn btn-outline-secondary w-100" onclick="limpiarFiltros()">
                        <i class="bi bi-arrow-clockwise"></i> Limpiar
                    </button>
                </div>
            </div>

            <div class="table-responsive">
                <table id="tablaTrabajadores" class="table table-hover align-middle w-100">
                    <thead class="table-dark">
                        <tr>
                            <th>Nombre</th>
                            <th>Teléfono</th>
                            <th>Rol / Puesto</th>
                            <th>Almacén</th>
                            <th>Salario</th>
                          <th>Prestamos Activos</th>
                          <th>Total Nomina</th>
                          
                            <th>Estado</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($trabajadores as $t): ?>
                        <tr class="fila-trabajador" data-rol="<?= $t['rol'] ?>">
                            <td><strong><?= htmlspecialchars($t['nombre']) ?></strong></td>
                            <td>
                                <a href="https://wa.me/52<?= $t['telefono'] ?>" target="_blank" class="text-decoration-none text-dark small">
                                    <i class="bi bi-whatsapp text-success me-1"></i> <?= htmlspecialchars($t['telefono']) ?>
                                </a>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border fw-normal text-uppercase" style="font-size: 0.7rem;">
                                    <?= $t['rol'] ?>
                                </span>
                            </td>
                            <td>
                                <span class="small text-muted"><i class="bi bi-geo-alt"></i> <?= $t['nombreAlmacen'] ?></span>
                            </td><td>
                                <span class="small text-muted"><i class="bi bi-geo-alt"></i> <?= $t['salario'] ?></span>
                            </td><td>
                                <span class="small text-muted"><i class="bi bi-geo-alt"></i> <?= $t['total_prestamos_pendientes'] ?></span>
                            </td><td>
                                <span class="small text-muted"><i class="bi bi-geo-alt"></i> <?= $t['salario_disponible'] ?></span>
                            </td>
                                           <td>
                                <?php 
                                    $claseEstado = match($t['estado']) {
                                        'activo' => 'bg-success',
                                        'vacaciones' => 'bg-warning text-dark',
                                        default => 'bg-danger'
                                    };
                                ?>
                                <span class="badge rounded-pill <?= $claseEstado ?>" style="font-size: 0.7rem;">
                                    <?= strtoupper($t['estado']) ?>
                                </span>
                            </td>
                            <td class="text-end">
                                <div class="btn-group">
                                    <button class="btn btn-sm btn-outline-primary border-0" onclick="editarTrabajador(<?= htmlspecialchars(json_encode($t)) ?>)">
                                        <i class="bi bi-pencil-square fs-5"></i>
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

    <div class="modal fade" id="modalTrabajador" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <form id="formTrabajador">
                    <div class="modal-header bg-dark text-white">
                        <h5 class="modal-title" id="modalTitulo">Nuevo Trabajador</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id" id="trabajador_id" value="0">
                        <input type="hidden" name="action" value="guardar">
                        
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label fw-bold small">Nombre Completo</label>
                                <input type="text" name="nombre" id="t_nombre" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">Teléfono</label>
                                <input type="text" name="telefono" id="t_telefono" class="form-control" maxlength="10" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">Puesto / Rol</label>
                                <select name="rol" id="t_rol" class="form-select" required>
                                    <?php foreach($rolesEnum as $rol): ?>
                                        <option value="<?= $rol ?>"><?= ucfirst($rol) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label fw-bold small">Almacén / Sucursal</label>
                                <?php if ($_SESSION['almacen_id'] == 0): ?>
                                    <select name="almacen_id" id="t_almacen_id" class="form-select" required>
                                        <option value="">Seleccionar Almacén...</option>
                                        <?php foreach($listaAlmacenes as $alm): ?>
                                            <option value="<?= $alm['id'] ?>"><?= htmlspecialchars($alm['nombre']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                <?php else: ?>
                                    <input type="text" class="form-control bg-light" value="Asignación Automática" readonly>
                                    <input type="hidden" name="almacen_id" id="t_almacen_id" value="<?= $_SESSION['almacen_id'] ?>">
                                <?php endif; ?>
                            </div>
                            

                            <div class="col-md-12">
                                <label class="form-label fw-bold small">Estado Laboral</label>
                                <select name="estado" id="t_estado" class="form-select">
                                    <?php foreach($estadosEnum as $est): ?>
                                        <option value="<?= $est ?>"><?= ucfirst($est) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-12 center">
                                <label class="form-label fw-bold small">salario</label>
                                <input type="money" name="salario" id="t_salario" class="form-control" maxlength="10" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-0">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary px-4">Guardar Cambios</button>
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
    // Selecciona todos los inputs de texto y también los textareas
    document.querySelectorAll('input[type="text"], textarea').forEach(elemento => {
        elemento.addEventListener('input', function() {
            // Convierte el valor a mayúsculas en tiempo real
            this.value = this.value.toUpperCase();
        });
    });
</script>
    <script>
    let tabla;

    $(document).ready(function() {
        tabla = $('#tablaTrabajadores').DataTable({
            "language": { "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json" },
            "dom": 'rt<"row mt-3"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
            "pageLength": 10,
            "order": [[0, 'asc']]
        });

        $('#busquedaTrabajador').on('keyup', function() {
            tabla.search(this.value).draw();
        });

        $('#filtroRol').on('change', function() {
            const val = $(this).val();
            tabla.column(2).search(val ? `^${val}$` : '', true, false).draw();
        });
    });

    function nuevoTrabajador() {
        $('#formTrabajador')[0].reset();
        $('#trabajador_id').val('0');
        // Si el select de almacén existe, resetearlo también
        if ($('#t_almacen_id').is('select')) $('#t_almacen_id').val('');
        $('#modalTitulo').text('Nuevo Trabajador');
        $('#modalTrabajador').modal('show');
    }

    function editarTrabajador(t) {
        $('#modalTitulo').text('Editar Trabajador');
        $('#trabajador_id').val(t.id);
        $('#t_nombre').val(t.nombre);
        $('#t_telefono').val(t.telefono);
        $('#t_rol').val(t.rol);
        $('#t_estado').val(t.estado);
         $('#t_salario').val(t.salario);
        // Seteamos el almacén
        if ($('#t_almacen_id').is('select')) {
            $('#t_almacen_id').val(t.almacen_id);
        }
        $('#modalTrabajador').modal('show');
    }

    $('#formTrabajador').on('submit', async function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        
        try {
            const resp = await fetch('/cfsistem/app/controllers/trabajadoresController.php', { method: 'POST', body: formData });
            const res = await resp.json();
            if (res.status === 'success') {
                Swal.fire({ icon: 'success', title: '¡Éxito!', showConfirmButton: false, timer: 1000 })
                .then(() => location.reload());
            } else {
                Swal.fire('Error', res.message, 'error');
            }
        } catch (e) { Swal.fire('Error', 'No se pudo guardar', 'error'); }
    });

    async function eliminarTrabajador(id) {
        const result = await Swal.fire({
            title: '¿Eliminar trabajador?',
            text: "Esta acción no se puede deshacer",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Sí, eliminar'
        });

        if (result.isConfirmed) {
            const fd = new FormData();
            fd.append('action', 'eliminar');
            fd.append('id', id);
            const resp = await fetch('/cfsistem/app/controllers/trabajadoresController.php', { method: 'POST', body: fd });
            const res = await resp.json();
            if(res.status === 'success') location.reload();
        }
    }

    function limpiarFiltros() {
        $('#busquedaTrabajador').val('');
        $('#filtroRol').val('');
        tabla.search('').column(2).search('').draw();
    }


function subirDocumentoCompra(trabajador_id) {
    
                
           

    Swal.fire({
        title: 'Documento de Vehiculo',
        html: `
            <div class="text-start">
                <label class="fw-bold small mb-2">Subir / Reemplazar documento</label>
                <input type="file" id="swal_file_doc" class="form-control mb-2" accept=".pdf,image/*">
                
                
            </div>
        `,
        icon: 'info',
        showCancelButton: true,
        confirmButtonText: 'Guardar',
        confirmButtonColor: '#198754',
        focusConfirm: false,

        preConfirm: async () => {

            const fileInput = document.getElementById('swal_file_doc');
            const file = fileInput?.files[0];

            if (!file) {
                Swal.showValidationMessage('Selecciona un archivo');
                return false;
            }

            const formData = new FormData();
            
            formData.append('trabajador_id', trabajador_id);
           
            formData.append('documento', file);
            console.log(file,trabajador_id);
            
             

            try {

    const response = await fetch(
        '/cfsistem/app/controllers/trabajadoresController.php?action=subirDocumento',
        {
            method: 'POST',
            body: formData
        }
    );

    console.log('Status:', response.status);

    const text = await response.text();

    console.log('Respuesta completa:', text);

    if (!response.ok) {
        throw new Error(`HTTP ${response.status}`);
    }

    let res;

    try {
        res = JSON.parse(text);
    } catch {
        throw new Error('El servidor devolvió HTML o texto inválido');
    }

    if (!res.success) {
        throw new Error(res.message || 'Error al subir archivo');
    }

    return res;

} catch (err) {
    console.error(err);
    Swal.showValidationMessage(err.message);
    return false;
}
        }

    }).then(result => {

        if (!result.isConfirmed || !result.value) return;

       Swal.fire({
    icon: 'success',
    title: 'Guardado',
    text: 'Documento actualizado correctamente',
    timer: 1800,
    showConfirmButton: false
}).then(() => {
    location.reload();
});
       
    });
}

function eliminarDocumento(id) {
    
                console.log('gasto');
           

    Swal.fire({
        title: 'Eliminar Documento',
        
        icon: 'info',
        showCancelButton: true,
        confirmButtonText: 'Guardar',
        confirmButtonColor: '#ed0909',
        focusConfirm: false,

        preConfirm: async () => {

         

            const formData = new FormData();
            
             formData.append('id', id);
             

            try {
                const response = await fetch('/cfsistem/app/controllers/trabajadoresController.php?action=eliminarDocumento', {
                    method: 'POST',
                    body: formData
                });

                // 🔥 LEEMOS COMO TEXTO PRIMERO (ANTI "Unexpected token <")
                const text = await response.text();
                console.log('RESPUESTA CRUDA:', text);

                let res;
                try {
                    res = JSON.parse(text);
                } catch (e) {
                    throw new Error('El servidor no devolvió JSON válido');
                }

                if (!res.success) {
                    throw new Error(res.message || 'Error al subir archivo');
                }

                return res;

            } catch (err) {
                Swal.showValidationMessage(err.message);
                return false;
            }
        }

    }).then(result => {

        if (!result.isConfirmed || !result.value) return;

       Swal.fire({
    icon: 'success',
    title: 'Eliminado',
    text: 'Documento eliminado correctamente',
    timer: 1800,
    showConfirmButton: false
}).then(() => {
    location.reload();
});
        if (typeof cargarCompras === 'function') {
            cargarCompras();
        }
    });
} 
async function imprimirContenidoModal() {
        
        
            const res = await fetch(`http://localhost/cfsistem/app/controllers/nominaController.php?action=listar`);
            const data = await res.json();
            //<td class="ps-3 small">${v.id}</td>
            let totalVendido=0;
            let deuda=0;

    // 1. Obtener los elementos clave del modal actual
    const folio = $('#spanFolio').text();
    const cliente = $('#detCliente').text();
    const almacen = $('#detAlmacen').text();
    
    // 2. Clonar las tablas de datos para no alterar el modal visual
    const tablaProductos = $('#tbodyDetalle').html();
    const tablaEntregas = $('#tbodyHistorial').html();
    const tablaPagos = $('#tbodyPagos').html();
    
    const total = $('#detTotalLabel').text();
    const saldo = $('#detSaldoLabel').text();

    // 3. Crear una nueva ventana temporal en el navegador
    const ventanaImpresion = window.open('', '_blank');
    let tabla='';

data.map(t => {
    tabla+=` <tr class="fila-trabajador" >
    
                            <td><strong>${t.nombre}</strong></td>
                            
                            <td>
                                <span class="badge bg-light text-dark border fw-normal text-uppercase" style="font-size: 0.7rem;">
                                     ${t.rol}
                                </span>
                            </td>
                            <td>
                                <span class="small text-muted">  ${t.nombreAlmacen}</span>
                            </td><td>
                                <span class="small text-muted">  ${t.salario}</span>
                            </td><td>
                                <span class="small text-muted">  ${t.total_prestamos_pendientes}</span>
                            </td><td>
                                <span class="small text-muted">  ${t.salario_disponible}</span>
                            </td>
                                           <td>
                                
                                    
                                
                            </td>
                            </tr>
                            `
                            

})

    // 4. Inyectar el HTML estructurado con estilos limpios y profesionales
    ventanaImpresion.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>Nomina</title>
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
            <style>
                body {font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; padding: 30px; color: #333; }
                .ticket-header { border-bottom: 2px solid #007aff; padding-bottom: 15px; margin-bottom: 20px; }
                .meta-box { background: #f8f9fa; padding: 12px; border-radius: 8px; margin-bottom: 15px; }
                .section-title { font-size: 0.85rem; font-weight: bold; text-transform: uppercase; color: #666; margin-top: 25px; margin-bottom: 10px; letter-spacing: 0.5px; }
                .table-responsive { max-height: none !important; overflow: visible !important; }
                .d-none { display: none !important; } /* Oculta columnas de inputs si están activas */
                @media print {
                    body { padding: 40px;  }
                    .btn-imprimir { display: none; }
                }
                     @page { 
                        margin: 0; /* Esto elimina el título de arriba y la fecha/hora de abajo */
                    }
            </style>
        </head>
        <body>
         <div id="areaImpresion" class="text-uppercase  bg-white" style="min-height: 650px; font-size: 0.95rem;">
 <img
    src="/cfsistem/public/assets/logo.ico"
    style="
        position: fixed;
        top: 30%;                  /* Centro vertical */
        left: 50%;                 /* Centro horizontal */
        transform: translate(-50%, -50%); /* Compensa el propio tamaño de la imagen */
        width: 240px;
        opacity: 0.08;
        z-index: 1;               /* Cambiado a -1 para que quede detrás del texto y no tape los clics */
        pointer-events: none;      /* Evita que interfiera si alguien intenta hacer clic sobre ella */
    "
>
                        <!-- ENCABEZADO -->
                        
<div class=" ">

    <!-- Logo + Título -->
    <div class="">

        <img src="/cfsistem/public/assets/logo.ico"
             alt="Logo"
             width="55"
             height="55"
             class="me-3">

         <div class="ticket-header d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="fw-bold m-0">CF SYSTEM NOMINA</h4>
                    
                </div>
               
            </div>

            
    </div>

  


                        </div>
                        <div class="row g-3">
                
                
            </div>
            <div class="table-responsive" style="max-height: 180px;">
                                <table class="table table-sm align-middle mb-0">
                                    <thead class="table-light">
                                        <tr class="small text-uppercase">
                                             <th>Nombre</th>
                           
                            <th>Rol / Puesto</th>
                            <th>Almacén</th>
                            <th>Salario</th>
                          <th>Prestamos Activos</th>
                          <th>Total Nomina</th>
                          
                           
                                        </tr>
                                    </thead>
                                    <tbody >${tabla}</tbody>
                                </table>
                            </div>
                       

                            

                          

</div>
                       

                        

                    </div>
             <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"><\/script> 
                <script>
   window.addEventListener('DOMContentLoaded', () => {
        // 1. Detectar si el usuario está en un dispositivo móvil
        const esMovil = /Android|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);

        // 2. Esperar 1 segundo a que carguen estilos, fuentes e imágenes
        setTimeout(() => {
            if (esMovil) {
                // --- COMPORTAMIENTO EN CELULARES: DESCARGA DE PDF AUTOMÁTICA ---
                const elementoParaConvertir = document.getElementById('areaImpresion');

                const opciones = {
                    margin:       1,
                    filename:     'nomina_${folio}.pdf',
                    image:        { type: 'jpeg', quality: 0.98 },
                    html2canvas:  { scale: 2, useCORS: true }, // Mayor calidad visual
                    jsPDF:        { unit: 'cm', format: 'letter', orientation: 'portrait' }
                };

                // Generar y descargar el PDF directamente
                html2pdf().set(opciones).from(elementoParaConvertir).save();
                
            } else {
                // --- COMPORTAMIENTO EN COMPUTADORAS: DIÁLOGO NATIVO DE IMPRESIÓN ---
                window.print();
            }
        }, 1000); // 1000 milisegundos = 1 segundo de espera
    });
 <\/script>
        </body>
        </html>
    `);

    ventanaImpresion.document.close();
}

    </script>
    
</body>
</html>