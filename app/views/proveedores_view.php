<?php
/**
 * Vista de Proveedores - Sistema CFDI
 */
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proveedores | Sistema</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    
     <?php require_once __DIR__ . '/layout/icono.php' ?>
    <?php if (function_exists('cargarEstilos')) { cargarEstilos(); } ?>
    
   <style>
    .card-table { 
        border: none; 
        border-radius: 15px; 
        box-shadow: 0 10px 30px rgba(0,0,0,0.05); 
        background: white; 
    }
    .fila-inactiva { 
        opacity: 0.5; 
        filter: grayscale(1); 
        background-color: #f8f9fa; 
    }
    .main-content { 
        padding: 20px; 
        transition: all 0.3s; 
        /* AJUSTE AQUÍ: Margen superior para librar el Navbar */
        margin-top: 70px; 
    }
    @media (min-width: 768px) { 
        .main-content { 
            margin-left: 260px; 
            /* Mantenemos el margen arriba en escritorio */
            margin-top: 70px; 
        } 
    }
</style>
</head>

<body>

    <?php if (function_exists('renderizarLayout')) { renderizarLayout($tituloPagina); } ?>

    <main class="main-content py-4">

    <div class="container-fluid">

        <!-- HEADER -->
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">

            <div>
                <h2 class="fw-bold mb-1">Proveedores</h2>
                <p class="text-muted small mb-0">
                    Gestiona y administra tus proveedores
                </p>
            </div>

            <button class="btn btn-primary rounded-pill px-4 shadow-sm d-flex align-items-center"
                onclick="abrirModalNuevoProveedor()">
                <i class="bi bi-plus-lg me-2"></i>
                Nuevo Proveedor
            </button>

        </div>

        <!-- CARD -->
        <div class="card border-0 shadow-sm rounded-4">

            <!-- CARD HEADER -->
            <div class="card-header bg-white border-0 py-3 px-4 d-flex justify-content-between align-items-center">

                <span class="fw-semibold text-muted small">
                    Lista de proveedores
                </span>

                <!-- puedes meter filtro o buscador aquí después -->
            </div>

            <!-- TABLA -->
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="tablaProveedores">

                    <thead class="bg-light">
                        <tr class="text-muted small">
                            <th class="ps-4">Nombre</th>
                            <th>RFC</th>
                            <th>Correo</th>
                            <th>Teléfono</th>
                            <th class="text-end pe-4">Acciones</th>
                        </tr>
                    </thead>

                    <tbody>
                        <!-- dinámico -->
                    </tbody>

                </table>
            </div>

        </div>

    </div>

</main>


    
<div class="modal fade" id="modalProveedor" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 rounded-4 shadow-lg overflow-hidden">

            <!-- HEADER -->
            <div class="modal-header border-0 bg-white">
                <h5 class="modal-title fw-semibold fs-5" id="tituloModal">
                    ✏️ Editar Proveedor
                </h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>
<style>
#formProveedor .card-seccion{
    border: 1px solid #eef1f5;
    border-radius: 18px;
    background: #fff;
    box-shadow: 0 2px 10px rgba(0,0,0,.04);
    padding: 18px;
    height: 100%;
}

#formProveedor .titulo-seccion{
    font-size: .78rem;
    font-weight: 700;
    text-transform: uppercase;
    color: #6c757d;
    letter-spacing: .6px;
    margin-bottom: 1rem;
}

#formProveedor .form-label{
    font-size: .78rem;
    font-weight: 600;
    color: #6c757d;
    margin-bottom: .35rem;
}

#formProveedor .form-control,
#formProveedor .form-select{
    border-radius: 14px;
    border: 1px solid #e4e7ec;
    min-height: 46px;
    font-size: .92rem;
    transition: all .2s ease;
    box-shadow: none;
}

#formProveedor textarea.form-control{
    min-height: 90px;
}

#formProveedor .form-control:focus,
#formProveedor .form-select:focus{
    border-color: #4f46e5;
    box-shadow: 0 0 0 .15rem rgba(79,70,229,.12);
}

#formProveedor .input-group-text{
    border-radius: 14px 0 0 14px;
    background: #f8fafc;
    border-color: #e4e7ec;
}

#formProveedor .btn-guardar{
    border-radius: 14px;
    padding: .8rem 1.6rem;
    font-weight: 600;
    border: none;
    background: linear-gradient(135deg,#4f46e5,#7c3aed);
    box-shadow: 0 8px 20px rgba(79,70,229,.25);
}

#formProveedor .btn-cancelar{
    border-radius: 14px;
    padding: .8rem 1.6rem;
    font-weight: 600;
}

#formProveedor .header-form{
    background: linear-gradient(135deg,#4f46e5,#7c3aed);
    border-radius: 18px;
    padding: 18px 22px;
    color: #fff;
    margin-bottom: 1rem;
}

#formProveedor .header-form h5{
    margin: 0;
    font-weight: 700;
}

#formProveedor .header-form small{
    opacity: .85;
}
</style>

<form id="formProveedor">

    <div class="modal-body px-4 py-3">

        <div class="header-form">
            <h5>Gestión de proveedor</h5>
            <small>Información general, contacto y ubicación</small>
        </div>

        <input type="hidden" id="proveedor_id" name="id">

        <div class="row g-4">

            <!-- INFORMACION GENERAL -->
            <div class="col-lg-6">
                <div class="card-seccion">

                    <div class="titulo-seccion">
                        <i class="bi bi-building me-1"></i>
                        Información General
                    </div>

                    <div class="row g-3">

                        <div class="col-md-6">
                            <label class="form-label">Almacén</label>

                            <select name="almacen_id"
                                id="almacen_id"
                                class="form-select <?= $_SESSION['almacen_id']==0 ? '' : 'bg-light' ?>"
                                <?= $_SESSION['almacen_id'] != 0 ? 'disabled' : '' ?>
                                >

                                <?php if ($_SESSION['almacen_id']==0): ?>
                                    <option value="">Seleccionar ubicación...</option>
                                <?php endif; ?>

                                <?php foreach($almacenes as $a): ?>
                                    <option value="<?= $a['id'] ?>"
                                        <?= ($a['id'] == $_SESSION['almacen_id']) ? 'selected' : '' ?>>
                                        <?= $a['nombre'] ?>
                                    </option>
                                <?php endforeach; ?>

                            </select>

                            <?php if ($_SESSION['almacen_id'] != 0): ?>
                                <input type="hidden"
                                    name="almacen_id"
                                    value="<?= $_SESSION['almacen_id'] ?>">
                            <?php endif; ?>
                        </div>
                         
                            <input type="hidden"
                                class="form-control"
                                id="almacen_id2"
                                name="almacen_id2">
                      

                        <div class="col-md-6">
                            <label class="form-label">Estado</label>

                            <select class="form-select"
                                id="activo"
                                name="activo">

                                <option value="1">🟢 Activo</option>
                                <option value="0">⚫ Inactivo</option>

                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Nombre Comercial</label>

                            <input type="text"
                                class="form-control"
                                id="nombre_comercial"
                                name="nombre_comercial">
                        </div>

                        <div class="col-12">
                            <label class="form-label">Razón Social</label>

                            <input type="text"
                                class="form-control"
                                id="razon_social"
                                name="razon_social">
                        </div>

                        <div class="col-12">
                            <label class="form-label">RFC</label>

                            <input type="text"
                                class="form-control text-uppercase"
                                id="rfc"
                                name="rfc">
                        </div>

                    </div>

                </div>
            </div>

            <!-- CONTACTO -->
            <div class="col-lg-6">
                <div class="card-seccion">

                    <div class="titulo-seccion">
                        <i class="bi bi-person-lines-fill me-1"></i>
                        Información de Contacto
                    </div>

                    <div class="row g-3">

                        <div class="col-12">
                            <label class="form-label">Correo</label>

                            <input type="email"
                                class="form-control"
                                id="correo"
                                name="correo">
                        </div>

                        <div class="col-12">
                            <label class="form-label">Contacto</label>

                            <input type="text"
                                class="form-control"
                                id="contacto"
                                name="contacto">
                        </div>

                        <div class="col-md-8">
                            <label class="form-label">Teléfono</label>

                            <input type="text"
                                class="form-control"
                                id="telefono"
                                name="telefono">
                        </div>

                        <div class="col-md-8">
                            <label class="form-label">Teléfono 2</label>

                            <input type="text"
                                class="form-control"
                                id="telefono2"
                                name="telefono2">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Extensión</label>

                            <input type="text"
                                class="form-control"
                                id="extencion"
                                name="extencion">
                        </div>

                    </div>

                </div>
            </div>

            <!-- DIRECCION -->
            <div class="col-12">
                <div class="card-seccion">

                    <div class="titulo-seccion">
                        <i class="bi bi-geo-alt-fill me-1"></i>
                        Dirección
                    </div>

                    <div class="row g-3">

                        <div class="col-12">
                            <label class="form-label">Dirección</label>

                            <textarea class="form-control"
                                id="direccion"
                                name="direccion"></textarea>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">No. Exterior</label>

                            <input type="text"
                                class="form-control"
                                id="numeroExt"
                                name="numeroExt">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">No. Interior</label>

                            <input type="text"
                                class="form-control"
                                id="numeroInt"
                                name="numeroInt">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Colonia</label>

                            <input type="text"
                                class="form-control"
                                id="colonia"
                                name="colonia">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Ciudad</label>

                            <input type="text"
                                class="form-control"
                                id="ciudad"
                                name="ciudad">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Fecha creación</label>

                            <input type="text"
                                class="form-control bg-light"
                                id="creado_at"
                                readonly>
                        </div>

                    </div>

                </div>
            </div>

        </div>

    </div>

    <div class="modal-footer border-0 px-4 pb-4">

        <button type="button"
            class="btn btn-light btn-cancelar"
            data-bs-dismiss="modal">
            Cancelar
        </button>

        <button type="button"
            class="btn btn-primary btn-guardar"
            onclick="guardarProveedor()">

            <i class="bi bi-check2-circle me-1"></i>
            Guardar proveedor
        </button>

    </div>

</form>

        </div>
    </div>
</div>
    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<?php require_once __DIR__ . '/egresosComponets/modalProveedoresCompraP.php'; ?>

    <script>

let tabla;

/* =========================
   CARGAR PROVEEDORES
========================= */
function cargarProveedores() {
    $.get('/cfsistem/app/controllers/proveedoresController.php?ajax=1', function(res) {

        if (res.status === 'success') {

            if (tabla) tabla.destroy();

            let html = '';
            console.log(res.data);

            res.data.forEach(p => {
                html += `
                    <tr>
                        <td>${p.nombre_comercial}</td>
                        <td>${p.rfc ?? ''}</td>
                        <td>${p.correo ?? ''}</td>
                        <td>${p.telefono ?? ''}</td>
                        <td> <button class="btn btn-link btn-sm text-primary p-2" onclick="editarProveedor(${p.id ?? ''})">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                   <button 
    class="btn btn-sm btn-link p-2 ${p.activo == 1 ? 'text-success' : 'text-secondary'}"
    onclick="cambiarEstado(${p.id})"
    title="Cambiar estado">

    <i class="bi ${p.activo == 1 ? 'bi-toggle-on' : 'bi-toggle-off'} fs-5"></i>

</button>
                              </td>
                    </tr>
                `;
            });

            $('#tablaProveedores tbody').html(html);

            tabla = $('#tablaProveedores').DataTable({
                language: { url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json' }
            });

        }

    }, 'json');
}

/* =========================
   NUEVO
========================= */
function nuevoProveedor() {

    $('#formProveedor')[0].reset();
    $('#proveedor_id').val('');
    $('#tituloModal').text('Nuevo Proveedor');

    new bootstrap.Modal(document.getElementById('modalProveedor')).show();
}

/* =========================
   Editar
========================= */

async function editarProveedor(id) {
    console.log(id);

    try {
        const resp = await fetch(`/cfsistem/app/controllers/proveedoresController.php?action=obtenerProveedor&id=${id}`);
        const res = await resp.json();
        console.log(res);

        if (!res.success) throw new Error(res.message);

        const p = res.data;

        // 🔥 LLENAR CAMPOS
        document.getElementById('proveedor_id').value = p.id || '';
        document.getElementById('nombre_comercial').value = p.nombre_comercial || '';
        document.getElementById('razon_social').value = p.razon_social || '';
        document.getElementById('rfc').value = p.rfc || '';
        document.getElementById('correo').value = p.correo || '';
        document.getElementById('contacto').value = p.contacto || '';
        document.getElementById('telefono').value = p.telefono || '';
        document.getElementById('telefono2').value = p.telefono2 || 0;
        document.getElementById('extencion').value = p.extencion || 0;
        document.getElementById('direccion').value = p.direccion || '';
        document.getElementById('ciudad').value = p.ciudad || '';
        document.getElementById('colonia').value = p.colonia || '';
        document.getElementById('numeroExt').value = p.numeroExt || '';
        document.getElementById('numeroInt').value = p.numeroInt || '';
             const almacenInput = document.getElementById('almacen_id');

document.getElementById('almacen_id').value = p.almacen_id || 0;
document.getElementById('almacen_id2').value = p.almacen_id || 0
;
        document.getElementById('activo').value = p.activo ?? 1;
        document.getElementById('creado_at').value = p.creado_at || '';

        // 🔥 CAMBIAR TÍTULO
        document.getElementById('tituloModal').innerText = 'Editar Proveedor';

        // 🔥 ABRIR MODAL
        const modal = new bootstrap.Modal(document.getElementById('modalProveedor'));
        modal.show();

    } catch (e) {
        console.error(e);
        Swal.fire('Error', e.message, 'error');
    }
}
/* =========================
   INIT
========================= */
$(document).ready(function() {
    cargarProveedores();
});

</script>
<script>
function guardarProveedor() {

    const formData = new FormData();

    // 🔥 ID
    formData.append(
        'id',
        document.getElementById('proveedor_id').value || 0
    );

    // 🔥 DATOS GENERALES
    formData.append(
        'nombre_comercial',
        document.getElementById('nombre_comercial').value.trim()
    );

    formData.append(
        'razon_social',
        document.getElementById('razon_social').value.trim()
    );

    formData.append(
        'rfc',
        document.getElementById('rfc').value.trim()
    );

    formData.append(
        'correo',
        document.getElementById('correo').value.trim()
    );

    // 🔥 TELEFONOS
    formData.append(
        'telefono',
        document.getElementById('telefono').value.trim() || 0
    );

    formData.append(
        'telefono2',
        document.getElementById('telefono2').value.trim() || 0
    );

    formData.append(
        'extencion',
        document.getElementById('extencion').value.trim() || 0
    );

    // 🔥 DIRECCION
    formData.append(
        'direccion',
        document.getElementById('direccion').value.trim()
    );

    formData.append(
        'colonia',
        document.getElementById('colonia').value.trim()
    );

    formData.append(
        'ciudad',
        document.getElementById('ciudad').value.trim()
    );

    // 🔥 NUMEROS
    formData.append(
        'numeroExt',
        document.getElementById('numeroExt').value.trim() || 0
    );

    formData.append(
        'numeroInt',
        document.getElementById('numeroInt').value.trim() || 0
    );

    // 🔥 ALMACEN
    const almacen = document.getElementById('almacen_id');
const almacen2 = document.getElementById('almacen_id2');
    formData.append(
        'almacen_id',
        almacen.value==0?  almacen2.value:0|| 0
    );

    // 🔥 ESTADO
    formData.append(
        'activo',
        document.getElementById('activo').value || 1
    );

    // 🔥 DEBUG
    for (let pair of formData.entries()) {
        console.log(pair[0] + ':', pair[1]);
    }

    // 🔥 PETICION
    fetch('/cfsistem/app/controllers/proveedoresController.php?action=actualizarProveedor', {
        method: 'POST',
        body: formData
    })
    .then(async res => {

        const text = await res.text();

        console.log('RESPUESTA RAW:', text);

        try {
            return JSON.parse(text);
        } catch (e) {
            throw new Error('La respuesta no es JSON válido');
        }
    })
    .then(data => {

        console.log(data);

        if (data.success) {

            Swal.fire({
                icon: 'success',
                title: 'Proveedor actualizado',
                text: data.message,
                timer: 1600,
                showConfirmButton: false
            });

            // 🔥 CERRAR MODAL
            const modalEl = document.getElementById('modalProveedor');

            const modal = bootstrap.Modal.getInstance(modalEl);

            if (modal) {
                modal.hide();
            }

            // 🔥 RECARGAR
            setTimeout(() => {
                location.reload();
            }, 1600);

        } else {

            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: data.message || 'No se pudo actualizar'
            });

        }

    })
    .catch(err => {

        console.error(err);

        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: err.message || 'Error en la petición'
        });

    });
}
</script>
<script>
function cambiarEstado(id) {

    const formData = new FormData();
    formData.append('id', id);

    fetch('/cfsistem/app/controllers/proveedoresController.php?action=eliminarProveedor', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {

        if (data.success) {

            Swal.fire({
                icon: 'success',
                title: 'Estado actualizado',
                timer: 1000,
                showConfirmButton: false
            });

            // 🔥 OPCIÓN 1: recargar
            setTimeout(() => location.reload(), 1000);

            // 🔥 OPCIÓN 2 (pro): solo actualizar icono (te lo hago si quieres)

        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: data.message
            });
        }

    })
    .catch(() => {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Error en la petición'
        });
    });
}
</script>
</body>
</html>