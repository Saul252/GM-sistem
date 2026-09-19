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
    
    <link rel="icon" type="image/png" href="/cfsistem/public/assets/logo.png">
    <link rel="shortcut icon" href="/cfsistem/public/assets/logo.ico" type="image/x-icon">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

    <?php require_once __DIR__ . '/layout/icono.php' ?>
    <?php if (function_exists('cargarEstilos')) { cargarEstilos(); } ?>

    <style>
        .card-table {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
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
            margin-top: 70px;
        }

        @media (min-width: 768px) {
            .main-content {
                margin-left: 260px;
                margin-top: 70px;
            }
        }

        /* Estilos mejorados para secciones del modal */
        #formProveedor .card-seccion {
            border: 1px solid #eef1f5;
            border-radius: 18px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, .04);
            padding: 18px;
            height: 100%;
            background-color: #fff;
        }

        #formProveedor .titulo-seccion {
            font-size: .78rem;
            font-weight: 700;
            text-transform: uppercase;
            color: #6c757d;
            letter-spacing: .6px;
            margin-bottom: 1rem;
        }

        #formProveedor .form-label {
            font-size: .78rem;
            font-weight: 600;
            color: #6c757d;
            margin-bottom: .35rem;
        }

        #formProveedor .form-control,
        #formProveedor .form-select {
            border-radius: 14px;
            border: 1px solid #e4e7ec;
            min-height: 46px;
            font-size: .92rem;
            transition: all .2s ease;
            box-shadow: none;
        }

        #formProveedor textarea.form-control {
            min-height: 90px;
        }

        #formProveedor .form-control:focus,
        #formProveedor .form-select:focus {
            border-color: #4f46e5;
            box-shadow: 0 0 0 .15rem rgba(79, 70, 229, .12);
        }

        #formProveedor .btn-guardar {
            border-radius: 14px;
            padding: .8rem 1.6rem;
            font-weight: 600;
            border: none;
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            box-shadow: 0 8px 20px rgba(79, 70, 229, .25);
            color: #fff;
        }

        #formProveedor .btn-cancelar {
            border-radius: 14px;
            padding: .8rem 1.6rem;
            font-weight: 600;
        }

        .header-form-modal {
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            border-radius: 16px;
            padding: 18px 22px;
            color: #fff;
            margin-bottom: 1.2rem;
        }

        .header-form-modal h5 {
            margin: 0;
            font-weight: 700;
        }

        .header-form-modal small {
            opacity: .85;
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
                    <p class="text-body-secondary small mb-0">
                        Gestiona y administra tus proveedores
                    </p>
                </div>

                <button class="btn btn-primary rounded-pill px-4 shadow-sm d-flex align-items-center"
                    onclick="nuevoProveedor()">
                    <i class="bi bi-plus-lg me-2"></i>
                    Nuevo Proveedor
                </button>
            </div>

            <!-- CARD -->
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header border-0 bg-transparent py-3 px-4 d-flex justify-content-between align-items-center">
                    <span class="fw-semibold text-body-secondary small">
                        Lista de proveedores
                    </span>
                </div>

                <!-- TABLA -->
                <div class="table-responsive rounded-4 bg-body p-3">
                    <table class="table table-hover align-middle mb-0" id="tablaProveedores">
                        <thead class="bg-body-tertiary">
                            <tr class="text-body-secondary" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                                <th class="ps-4 py-3 text-uppercase fw-bold">PROVEEDOR / RAZÓN SOCIAL</th>
                                <th class="py-3 text-uppercase fw-bold">RFC</th>
                                <th class="py-3 text-uppercase fw-bold">CORREO</th>
                                <th class="py-3 text-uppercase fw-bold">TELÉFONO</th>
                                <th class="py-3 text-uppercase fw-bold text-center">ESTADO</th>
                                <th class="text-end pe-4 py-3 text-uppercase fw-bold">ACCIONES</th>
                            </tr>
                        </thead>
                        <tbody class="border-top-0">
                            <!-- Cargado dinámicamente -->
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </main>

    <!-- MODAL PROVEEDOR -->
    <div class="modal fade" id="modalProveedor" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 rounded-4 shadow-lg overflow-hidden">

                <!-- HEADER MODAL -->
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-semibold fs-5" id="tituloModal">
                        ✏️ Gestionar Proveedor
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <form id="formProveedor">
                    <div class="modal-body px-4 py-3">

                        <div class="header-form-modal">
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
                                            <select name="almacen_id" id="almacen_id"
                                                class="form-select"
                                                <?= $_SESSION['almacen_id'] != 0 ? 'disabled' : '' ?>>
                                                <?php if ($_SESSION['almacen_id'] == 0): ?>
                                                    <option value="">Seleccionar ubicación...</option>
                                                <?php endif; ?>
                                                <?php foreach($almacenes as$a): ?>
                                                    <option value="<?= $a['id'] ?>"
                                                        <?= ($a['id'] ==$_SESSION['almacen_id']) ? 'selected' : '' ?>>
                                                        <?= $a['nombre'] ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                            <?php if ($_SESSION['almacen_id'] != 0): ?>
                                                <input type="hidden" name="almacen_id" value="<?= $_SESSION['almacen_id'] ?>">
                                            <?php endif; ?>
                                        </div>

                                        <input type="hidden" class="form-control" id="almacen_id2" name="almacen_id2">

                                        <div class="col-md-6">
                                            <label class="form-label">Estado</label>
                                            <select class="form-select" id="activo" name="activo">
                                                <option value="1">🟢 Activo</option>
                                                <option value="0">⚫ Inactivo</option>
                                            </select>
                                        </div>

                                        <div class="col-12">
                                            <label class="form-label">Nombre Comercial</label>
                                            <input type="text" class="form-control" id="nombre_comercial" name="nombre_comercial">
                                        </div>

                                        <div class="col-12">
                                            <label class="form-label">Razón Social</label>
                                            <input type="text" class="form-control" id="razon_social" name="razon_social">
                                        </div>

                                        <div class="col-12">
                                            <label class="form-label">RFC</label>
                                            <input type="text" class="form-control text-uppercase" id="rfc" name="rfc">
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
                                            <input type="email" class="form-control" id="correo" name="correo">
                                        </div>

                                        <div class="col-12">
                                            <label class="form-label">Contacto</label>
                                            <input type="text" class="form-control" id="contacto" name="contacto">
                                        </div>

                                        <div class="col-md-8">
                                            <label class="form-label">Teléfono</label>
                                            <input type="text" class="form-control" id="telefono" name="telefono">
                                        </div>

                                        <div class="col-md-4">
                                            <label class="form-label">Extensión</label>
                                            <input type="text" class="form-control" id="extencion" name="extencion">
                                        </div>

                                        <div class="col-12">
                                            <label class="form-label">Teléfono 2</label>
                                            <input type="text" class="form-control" id="telefono2" name="telefono2">
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
                                            <textarea class="form-control text-uppercase" id="direccion" name="direccion"></textarea>
                                        </div>

                                        <div class="col-md-3">
                                            <label class="form-label">No. Exterior</label>
                                            <input type="text" class="form-control" id="numeroExt" name="numeroExt">
                                        </div>

                                        <div class="col-md-3">
                                            <label class="form-label">No. Interior</label>
                                            <input type="text" class="form-control" id="numeroInt" name="numeroInt">
                                        </div>

                                        <div class="col-md-3">
                                            <label class="form-label">Colonia</label>
                                            <input type="text" class="form-control" id="colonia" name="colonia">
                                        </div>

                                        <div class="col-md-3">
                                            <label class="form-label">Ciudad</label>
                                            <input type="text" class="form-control" id="ciudad" name="ciudad">
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label">Código Postal</label>
                                            <input type="text" class="form-control" id="codigoPostal" name="codigoPostal">
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label">Fecha Creación</label>
                                            <input type="text" class="form-control" id="creado_at" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                    </div>

                    <div class="modal-footer border-0 px-4 pb-4">
                        <button type="button" class="btn btn-light btn-cancelar" data-bs-dismiss="modal">
                            Cancelar
                        </button>
                        <button type="button" class="btn btn-guardar" onclick="guardarProveedor()">
                            <i class="bi bi-check2-circle me-1"></i>
                            Guardar proveedor
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <!-- SCRIPTS -->
    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <?php require_once __DIR__ . '/egresosComponets/direccionUbicacionModal.php'; ?>
    <?php require_once __DIR__ . '/egresosComponets/verEditarUbicaciones.php'; ?>

    <script>
    let tabla;

    /* =========================
       CARGAR PROVEEDORES
    ========================= */
    function cargarProveedores() {
        $.get('/cfsistem/app/controllers/proveedoresController.php?ajax=1', function(res) {
            if (res.status === 'success') {
                if ($.fn.DataTable.isDataTable('#tablaProveedores')) {
                    $('#tablaProveedores').DataTable().destroy();
                }

                let html = '';

                res.data.forEach(p => {
                    const inicial = (p.nombre_comercial || 'P').charAt(0).toUpperCase();

                    const rfc = p.rfc 
                        ? `<span class="badge bg-body-secondary text-body border font-monospace fw-semibold">${p.rfc}</span>` 
                        : `<span class="text-body-tertiary">-</span>`;

                    const correo = p.correo 
                        ? `<a href="mailto:${p.correo}" class="text-body text-decoration-none small"><i class="bi bi-envelope text-body-tertiary me-1"></i>${p.correo}</a>` 
                        : `<span class="text-body-tertiary">-</span>`;

                    const telefono = p.telefono 
                        ? `<span class="text-body small"><i class="bi bi-telephone text-body-tertiary me-1"></i>${p.telefono}</span>` 
                        : `<span class="text-body-tertiary">-</span>`;

                    const estadoBadge = p.activo == 1 
                        ? `<span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1"><i class="bi bi-circle-fill me-1" style="font-size: 6px;"></i>Activo</span>` 
                        : `<span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill px-3 py-1"><i class="bi bi-circle-fill me-1" style="font-size: 6px;"></i>Inactivo</span>`;

                    html += `
                    <tr>
                        <td class="ps-4 py-3">
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded-circle bg-primary bg-opacity-10 text-primary fw-bold d-flex align-items-center justify-content-center flex-shrink-0" style="width: 38px; height: 38px; font-size: 0.9rem;">
                                    ${inicial}
                                </div>
                                <div>
                                    <div class="fw-semibold text-body">${p.nombre_comercial || 'Sin Nombre'}</div>
                                    <div class="text-body-tertiary small">ID: #${p.id || 'N/A'}</div>
                                </div>
                            </div>
                        </td>
                        <td>${rfc}</td>
                        <td>${correo}</td>
                        <td>${telefono}</td>
                        <td class="text-center">${estadoBadge}</td>
                        <td class="text-end pe-4">
                            <div class="d-inline-flex gap-1">
           <button type="button" class="btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-1 rounded-pill px-3 py-1 shadow-sm" onclick=" abrirModalGestionUbicaciones(${p.id ?? ''}, '${p.nombre_comercial ?? ''}')" title="Agregar Dirección proveedor">
    <i class="bi bi-map"></i>
    <span>ubicaciones</span>
</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary rounded-2 px-2 py-1" onclick="editarProveedor(${p.id ?? ''})" title="Editar proveedor">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button type="button" class="btn btn-sm ${p.activo == 1 ? 'btn-outline-danger' : 'btn-outline-success'} rounded-2 px-2 py-1" onclick="cambiarEstado(${p.id})" title="${p.activo == 1 ? 'Desactivar' : 'Activar'}">
                                    <i class="bi ${p.activo == 1 ? 'bi-toggle-on' : 'bi-toggle-off'} fs-6"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    `;
                });

                $('#tablaProveedores tbody').html(html);

                tabla = $('#tablaProveedores').DataTable({
                    language: {
                        url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
                    },
                    pageLength: 10,
                    responsive: true
                });
            }
        }, 'json');
    }

    /* =========================
          <button type="button" class="btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-1 rounded-pill px-3 py-1 shadow-sm" onclick="abrirModalNuevaDireccion(${p.id ?? ''}, '${p.nombre_comercial ?? ''}')" title="Agregar Dirección proveedor">
    <i class="bi bi-plus-lg"></i>
    <span>Dirección</span>
</button>  
       NUEVO
    ========================= */
    function nuevoProveedor() {
        $('#formProveedor')[0].reset();
        $('#proveedor_id').val('');
        $('#tituloModal').text('Nuevo Proveedor');

        new bootstrap.Modal(document.getElementById('modalProveedor')).show();
    }

    /* =========================
       EDITAR
    ========================= */
    async function editarProveedor(id) {
        try {
            const resp = await fetch(`/cfsistem/app/controllers/proveedoresController.php?action=obtenerProveedor&id=${id}`);
            const res = await resp.json();

            if (!res.success) throw new Error(res.message);

            const p = res.data;

            document.getElementById('proveedor_id').value = p.id || '';
            document.getElementById('nombre_comercial').value = p.nombre_comercial || '';
            document.getElementById('razon_social').value = p.razon_social || '';
            document.getElementById('rfc').value = p.rfc || '';
            document.getElementById('correo').value = p.correo || '';
            document.getElementById('contacto').value = p.contacto || '';
            document.getElementById('telefono').value = p.telefono || '';
            document.getElementById('telefono2').value = p.telefono2 || '';
            document.getElementById('extencion').value = p.extencion || '';
            document.getElementById('direccion').value = p.direccion || '';
            document.getElementById('ciudad').value = p.ciudad || '';
            document.getElementById('colonia').value = p.colonia || '';
            document.getElementById('numeroExt').value = p.numeroExt || '';
            document.getElementById('numeroInt').value = p.numeroInt || '';
            document.getElementById('codigoPostal').value = p.codigo_postal || '';

            document.getElementById('almacen_id').value = p.almacen_id || 0;
            document.getElementById('almacen_id2').value = p.almacen_id || 0;
            document.getElementById('activo').value = p.activo ?? 1;
            document.getElementById('creado_at').value = p.creado_at || '';

            document.getElementById('tituloModal').innerText = 'Editar Proveedor';

            const modal = new bootstrap.Modal(document.getElementById('modalProveedor'));
            modal.show();

        } catch (e) {
            console.error(e);
            Swal.fire('Error', e.message, 'error');
        }
    }

    /* =========================
       GUARDAR
    ========================= */
    function guardarProveedor() {
        const formData = new FormData();

        formData.append('id', document.getElementById('proveedor_id').value || 0);
        formData.append('nombre_comercial', document.getElementById('nombre_comercial').value.trim());
        formData.append('razon_social', document.getElementById('razon_social').value.trim());
        formData.append('rfc', document.getElementById('rfc').value.trim());
        formData.append('correo', document.getElementById('correo').value.trim());
        formData.append('telefono', document.getElementById('telefono').value.trim() || 0);
        formData.append('telefono2', document.getElementById('telefono2').value.trim() || 0);
        formData.append('extencion', document.getElementById('extencion').value.trim() || 0);
        formData.append('direccion', document.getElementById('direccion').value.trim());
        formData.append('colonia', document.getElementById('colonia').value.trim());
        formData.append('ciudad', document.getElementById('ciudad').value.trim());
        formData.append('numeroExt', document.getElementById('numeroExt').value.trim() || 0);
        formData.append('numeroInt', document.getElementById('numeroInt').value.trim() || 0);
        formData.append('codigoPostal', document.getElementById('codigoPostal').value.trim() || 0);

        const almacen = document.getElementById('almacen_id');
        const almacen2 = document.getElementById('almacen_id2');
        formData.append('almacen_id', almacen.value == 0 ? almacen2.value : almacen.value || 0);
        formData.append('activo', document.getElementById('activo').value || 1);

        fetch('/cfsistem/app/controllers/proveedoresController.php?action=actualizarProveedor', {
            method: 'POST',
            body: formData
        })
        .then(async res => {
            const text = await res.text();
            try {
                return JSON.parse(text);
            } catch (e) {
                throw new Error('La respuesta no es JSON válido');
            }
        })
        .then(data => {
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Proveedor actualizado',
                    text: data.message,
                    timer: 1600,
                    showConfirmButton: false
                });

                const modalEl = document.getElementById('modalProveedor');
                const modal = bootstrap.Modal.getInstance(modalEl);
                if (modal) {
                    modal.hide();
                }

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

    /* =========================
       CAMBIAR ESTADO
    ========================= */
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
                setTimeout(() => location.reload(), 1000);
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

    /* =========================
       INIT
    ========================= */
    $(document).ready(function() {
        cargarProveedores();
    });
    </script>
</body>
</html>