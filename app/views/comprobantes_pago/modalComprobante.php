<div class="modal fade" id="modalCotizacion" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content"
            style="border-radius: 20px; border: none; overflow: hidden; box-shadow: 0 15px 50px rgba(0,0,0,0.2);">
            <form id="formSolicitud">
                <div class="modal-header bg-white border-0 pt-4 px-4">
                    <div class="d-flex align-items-center">
                        <div class="bg-primary text-white rounded-3 p-2 me-3 shadow-sm">
                            <i class="bi bi-file-earmark-plus fs-4"></i>
                        </div>
                        <div>
                            <h4 class="fw-bold mb-0">Nueva Cotizacion</h4>
                            <p class="text-muted small mb-0">Complete los datos para requerir materiales al almacén
                            </p>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body px-4">
                    <div class="row g-3 mb-4 p-3 rounded-4 bg-light shadow-sm align-items-end">

               
<div class="col-md-3">
    <label class="form-label small fw-bold">
        <i class="bi bi-box-seam"></i> Almacén de Cargo
    </label>

    <div class="input-group shadow-sm">
        <select 
            name="almacen_id" 
            id="almacen_id"
            class="form-select"
            required
        >
            <option value="">Seleccionar ubicación...</option>

            <?php foreach($almacenes as $a): ?>
                <option value="<?= $a['id'] ?>">
                    <?= $a['nombre'] ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
</div>
                        <div class="col-md-3">
                             <div class="input-group">
                            <label class="form-label small fw-bold text-muted text-uppercase">Cliente</label>
                            <select name="cliente_id" id="cliente_id"class="form-select select2-modal" required>
                                <option value="">Seleccionar cliente...</option>
                                <?php foreach($clientes as $p): ?>
                                <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['nombre_comercial']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                           <button class="btn btn-outline-primary flex-shrink-0" type="button"
                                onclick="abrirModalNuevoCliente()" style="border-radius:10px;">
                                <i class="bi bi-person-plus"></i>
                            </button>
                        </div>
                        </div>

                        <div class="col-md-6">

                            <label class="form-label small fw-bold text-muted text-uppercase">Añadir Producto (SKU o
                                Nombre)</label>
                            <div class="input-group">


                                <select id="buscadorProductos" class="form-select select2-modal border-start-0">
                                    <option value="">Escribe para buscar...</option>
                                    <?php foreach($listaProductos as $pr): ?>
                                    <option value="<?= $pr['producto_id'] ?>"
                                        data-nombre="<?= htmlspecialchars($pr['nombre']) ?>"
                                        data-sku="<?= htmlspecialchars($pr['sku']) ?>"
                                        data-um="<?= htmlspecialchars($pr['unidad_medida']) ?>"
                                        data-ur="<?= htmlspecialchars($pr['unidad_reporte']) ?>"
                                        data-factor="<?= $pr['factor_conversion'] ?? 1 ?>">
                                        [<?= $pr['sku'] ?>] <?= $pr['nombre'] ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                                <button type="button" class="btn btn-primary d-flex align-items-center"
                                    onclick="abrirModalProducto()" title="Agregar nuevo producto">
                                    <i class="bi bi-plus-lg me-1"></i>
                                    <span class="d-none d-xl-inline">Nuevo</span>
                                </button>
                            </div>
                        </div>
                    </div>

                  
                    <div class="text-end mt-3">

                      
                      
                    
            

                    </div>
                </div>

                <div class="modal-footer border-0 p-4">
                    <button type="button" class="btn btn-link text-decoration-none text-muted fw-bold"
                        data-bs-dismiss="modal">
                        Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary rounded-pill px-5 py-2 fw-bold shadow">
                        <i class="bi bi-check2-circle me-2"></i> Crear Comprobante
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
const URL_CONTROLADOR = '/cfsistem/app/controllers/cotizacionesController.php';

// =====================================================
// SELECT2
// =====================================================

$('.select2-modal').select2({
    theme: 'bootstrap-5',
    dropdownParent: $('#modalCotizacion')
});

// =====================================================
// CALCULAR TOTAL
// =====================================================

// 🔥 EVITAR LOOPS
let recalculandoFila = false;
let totaLCompra;


// =====================================================
// AGREGAR PRODUCTO
// =====================================================

// =====================================================
// GUARDAR SOLICITUD
// =====================================================
// // =====================================================
// CONVERTIR A COMPRA
// =====================================================

$('#formSolicitud').on('submit', async function(e) {

    e.preventDefault();

    if (!$('#tablaDetalle tbody tr').length) {
        Swal.fire('Error', 'Agregue productos', 'warning');
        return;
    }

   const payload = {
    almacen_id: $('#almacen_id').val(),
    cliente_id: $('#cliente_id').val(),
    totalCotizacion: $('#totalCotizacion').val(),
    items: []
};
    $('#tablaDetalle tbody tr').each(function() {

        const fila = $(this);
        const id = fila.attr('id').replace('fila-', '');

        const unidadSelect = fila.find('.unidad-select option:selected');
        const tipoPrecioSelect = fila.find('.tipoPrecio-select option:selected');

        payload.items.push({
            producto_id: id,

            cantidad: fila.find('.cantidad').val(),

            unidad: unidadSelect.val(),
            unidad_id: unidadSelect.data('medida-id'),
            equivalencia: unidadSelect.data('equivalencia'),

            tipoPrecio: tipoPrecioSelect.val(),

            precioUnitario: fila.find('.precio-unitario').val(),

            precio: fila.find('.precio-total').val()
        });
    });

    console.log('JSON ENVIADO:', payload);

    Swal.fire({
        title: 'Guardando...',
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading()
    });

    try {

        const resp = await fetch(`${URL_CONTROLADOR}?action=guardar`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(payload)
        });

        const res = await resp.json();

        console.log('RESPUESTA:', res);

        if (res.status === 'success') {

            await Swal.fire({
                icon: 'success',
                title: '¡Éxito!',
                text: res.message,
                timer: 1500,
                showConfirmButton: false
            });

            location.reload();

        } else {
            Swal.fire('Error', res.message, 'error');
        }

    } catch (e) {
        console.error(e);
        Swal.fire('Error', 'Fallo de conexión', 'error');
    }
});
// $('#formConvertirCompra').on('submit', async function(e) {

//     e.preventDefault();

//     Swal.fire({
//         title: 'Procesando ingreso...',
//         allowOutsideClick: false,
//         didOpen: () => Swal.showLoading()
//     });

//     try {

//         const resp = await fetch(
//             `${URL_CONTROLADOR}?action=convertirACompra`, {
//                 method: 'POST',
//                 body: new FormData(this)
//             }
//         );

//         const res = await resp.json();

//         if (res.status === 'success') {

//             await Swal.fire({
//                 icon: 'success',
//                 title: 'Ingresado',
//                 text: res.message
//             });

//             location.reload();

//         } else {

//             Swal.fire(
//                 'Error',
//                 res.message,
//                 'error'
//             );
//         }

//     } catch (e) {

//         Swal.fire(
//             'Error',
//             'Fallo de conexión',
//             'error'
//         );
//     }
// });

// =====================================================
// ELIMINAR FILA
// =====================================================

function quitarFila(id) {

    $(`#fila-${id}`).remove();

    if (!$('#tablaDetalle tbody tr').length) {

        $('#emptyState').removeClass('d-none');
    }
}

// =====================================================
// NUEVA SOLICITUD
// =====================================================

function nuevaCotizacion() {

    $('#formSolicitud')[0].reset();

    $('#tablaDetalle tbody').empty();

    $('#emptyState').removeClass('d-none');

    $('#modalCotizacion').modal('show');
    recargarProductos();
}
</script>