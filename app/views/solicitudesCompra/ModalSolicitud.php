<div class="modal fade" id="modalSolicitud" tabindex="-1" aria-hidden="true">
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
                            <h4 class="fw-bold mb-0">Nueva Solicitud de Compra</h4>
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

                            <?php $es_admin = ($_SESSION['rol_id'] == 1); ?>

                            <div class="input-group shadow-sm">
                                <select id="almacen_id_cabecera_visual" name="almacen_id" id="almacen_id"
                                    class="form-select <?= $es_admin ? '' : 'bg-light' ?>"
                                    <?= !$es_admin ? 'disabled' : 'name="almacen_id_cabecera"' ?> required>

                                    <?php if ($es_admin): ?>
                                    <option value="">Seleccionar ubicación...</option>
                                    <?php endif; ?>

                                    <?php foreach($almacenes as $a): ?>
                                    <option value="<?= $a['id'] ?>"
                                        <?= ($a['id'] == $_SESSION['almacen_id']) ? 'selected' : '' ?>>
                                        <?= $a['nombre'] ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>

                                <?php if (!$es_admin): ?>
                                <span class="input-group-text bg-light text-muted">
                                    <i class="bi bi-lock-fill"></i>
                                </span>
                                <?php endif; ?>
                            </div>

                            <?php if (!$es_admin): ?>
                            <input type="hidden" name="almacen_id_cabecera" value="<?= $_SESSION['almacen_id'] ?>">
                            <small class="text-muted">
                                Privilegios de sede actual
                            </small>
                            <?php endif; ?>
                        </div>

                        <div class="col-md-4">
                            <div class="input-group">
                            <label class="form-label small fw-bold text-muted text-uppercase">Proveedor
                                Sugerido</label>
                            <select name="proveedor_id" id="proveedor_id"class="form-select select2-modal" required>
                                <option value="">Seleccionar proveedor...</option>
                                <?php foreach($proveedores as $p): ?>
                                <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['nombre_comercial']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                               <button class="btn btn-outline-success" type="button"
                                            onclick="abrirModalNuevoProveedor()">
                                            <i class="bi bi-plus-lg"></i>
                                        </button>
                        </div>
                        </div>

                        <div class="col-md-4">
                            
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

                    <div class="table-responsive border rounded-4 bg-white">
                        <table class="table align-middle mb-0" id="tablaDetalle">
                            <thead class="bg-light">
                                <tr class="text-muted small uppercase">
                                    <th class="ps-4" style="width: 45%;">Producto</th>
                                    <th style="width: 20%;">Cantidad</th>
                                    <th style="width: 25%;">Presentación / Unidad</th>
                                    <th style="width: 25%;">PrecioUnitario</th>
                                    <th style="width: 50%;">Precio</th>
                                    <th style="width: 10%;" class="text-end pe-4">Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>

                        </table>

                        <div id="emptyState" class="text-center py-5 text-muted">
                            <div class="mb-3">
                                <i class="bi bi-cart-plus opacity-25" style="font-size: 3.5rem;"></i>
                            </div>
                            <p class="fw-medium">La lista está vacía</p>
                            <small>Utiliza el buscador de arriba para añadir artículos</small>
                        </div>
                    </div>
                    <div class="text-end mt-3">

                        <small class="d-block text-muted fw-semibold mb-1" style="letter-spacing:.5px;">
                            COSTO TOTAL DE COMPRA
                        </small>

                        <div id="costoTotalCompra" class="fw-bold text-success" style="
            font-size:2rem;
            line-height:1;
        ">
                            $0.00
                        </div>

                    </div>
                </div>

                <div class="modal-footer border-0 p-4">
                    <button type="button" class="btn btn-link text-decoration-none text-muted fw-bold"
                        data-bs-dismiss="modal">
                        Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary rounded-pill px-5 py-2 fw-bold shadow">
                        <i class="bi bi-check2-circle me-2"></i> Confirmar Solicitud
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
const URL_CONTROLADOR = '/cfsistem/app/controllers/solicitudesCompraController.php';

// =====================================================
// SELECT2
// =====================================================

$('.select2-modal').select2({
    theme: 'bootstrap-5',
    dropdownParent: $('#modalSolicitud')
});

// =====================================================
// CALCULAR TOTAL
// =====================================================

// 🔥 EVITAR LOOPS
let recalculandoFila = false;
let totaLCompra;

function calcularTotalSol(input) {

    if (recalculandoFila) return;

    recalculandoFila = true;

    try {

        const fila = input.closest('tr');

        const cantidad = parseFloat(
            fila.querySelector('.cantidad').value
        ) || 0;

        const precioUnitario = parseFloat(
            fila.querySelector('.precio-unitario').value
        ) || 0;

        // =====================================
        // CALCULAR TOTAL
        // =====================================

        const precioTotal =
            cantidad * precioUnitario;
        console.log(precioTotal);

        fila.querySelector('.precio-total').value =
            precioTotal.toFixed(2);

        // =====================================
        // SUMAR TODO
        // =====================================

        totaLCompra = 0;
        console.log('hola');

        document.querySelectorAll('.precio-total')
            .forEach(el => {

                totaLCompra +=
                    parseFloat(el.value) || 0;
            });
        const cT = document.getElementById('costoTotalCompra');
        console.log(cT);

        cT.textContent = totaLCompra.toLocaleString('es-MX', {
            style: 'currency',
            currency: 'MXN'
        });

        console.log(totaLCompra);
        console.log(totaLCompra);

    } finally {

        recalculandoFila = false;
    }
}
// =====================================================
// AGREGAR PRODUCTO
// =====================================================
async function recargarProductos() {

    try {

        const resp = await fetch(
            `/cfsistem/app/controllers/egresosController.php?action=obtenerProductosSelect`
        );

        const res = await resp.json();

        if (!res.success) {
            throw new Error(res.message);
        }

        const select = document.getElementById('buscadorProductos');

        // 🔥 limpiar opciones
        select.innerHTML = `
            <option value="">
                Escribe para buscar...
            </option>
        `;

        // 🔥 volver a llenar
        res.data.forEach(pr => {

            const option = document.createElement('option');

            option.value = pr.producto_id;

            option.dataset.nombre = pr.nombre;
            option.dataset.sku = pr.sku;
            option.dataset.um = pr.unidad_medida;
            option.dataset.ur = pr.unidad_reporte;
            option.dataset.factor = pr.factor_conversion || 1;

            option.textContent =
                `[${pr.sku}] ${pr.nombre}`;

            select.appendChild(option);

        });

    } catch (e) {

        console.error(e);

        Swal.fire(
            'Error',
            'No se pudo actualizar la lista de productos',
            'error'
        );
    }
    $('#buscadorProductos').trigger('change.select2');
}
$('#buscadorProductos').on('select2:select', function(e) {

    const d = e.params.data.element.dataset;

    const id = $(this).val();

    // VALIDAR DUPLICADO
    if ($(`#fila-${id}`).length) {

        Swal.fire(
            'Aviso',
            'El producto ya está en la lista',
            'info'
        );

        return;
    }

    $('#emptyState').addClass('d-none');

    // AGREGAR FILA
    $('#tablaDetalle tbody').append(`

        <tr id="fila-${id}">

            <!-- PRODUCTO -->
            <td class="ps-4">
                <b>${d.nombre}</b><br>
                <small class="text-muted">${d.sku}</small>
            </td>

            <!-- CANTIDAD -->
            <td>
                <input 
                    type="number"
                    name="items[${id}][cant]"
                    class="form-control cantidad"
                    step="0.01"
                    value="1"
                    min="0.01"
                    required
                    oninput="calcularTotalSol(this)"
                >
            </td>

            <!-- UNIDAD -->
            <td>
                <select 
                    name="items[${id}][unidad]" 
                    class="form-select unidad-select"
                    onchange="calcularTotalSol(this)"
                >
                   

                    <option value="1">
                        (${d.ur})
                    </option>
                </select>
            </td>

            <!-- COSTO UNITARIO -->
<td>
    <input 
        type="number"
        lang="en-US"
        name="items[${id}][precioUnitario]"
        class="form-control precio-unitario"
        step="0.01"
        min="0"
        placeholder="0.00"
        required
        oninput="calcularTotalSol(this, 'unitario')"
    >
</td>

<!-- COSTO TOTAL -->
<td style="min-width:160px;">

    <input 
        type="number"
        lang="en-US"
        name="items[${id}][precio]"
        class="form-control precio-total fw-bold text-success bg-light"
        step="0.01"
        min="0"
        placeholder="0.00"
        oninput="calcularTotalSol(this, 'total')"
        style="
            font-size:1.1rem;
            height:45px;
            min-width:140px;
        "
    >

</td>
            <!-- ELIMINAR -->
            <td>
                <button 
                    type="button"
                    class="btn btn-link text-danger"
                    onclick="quitarFila(${id})"
                >
                    <i class="bi bi-trash"></i>
                </button>
            </td>

        </tr>
    `);

    // LIMPIAR SELECT
    $(this).val(null).trigger('change');
});

// =====================================================
// GUARDAR SOLICITUD
// =====================================================

$('#formSolicitud').on('submit', async function(e) {

    e.preventDefault();

    if (!$('#tablaDetalle tbody tr').length) {

        Swal.fire(
            'Error',
            'Agregue productos',
            'warning'
        );

        return;
    }

    Swal.fire({
        title: 'Guardando...',
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading()
    });

    try {

        const resp = await fetch(
            `${URL_CONTROLADOR}?action=guardar`, {
                method: 'POST',
                body: new FormData(this)
            }
        );

        const res = await resp.json();

        if (res.status === 'success') {
    await Swal.fire({
        icon: 'success',
        title: '¡Éxito!',
        text: res.message,
        // 1. Eliminamos 'timer' para que la alerta no se cierre sola
        showDenyButton: true,
        showCancelButton: true,
        confirmButtonText: 'IMPRIMIR',
        denyButtonText: 'SALIR',
        cancelButtonText: 'Cerrar',
        confirmButtonColor: '#34c759',
        denyButtonColor: '#5856d6',
        customClass: {
            popup: 'rounded-4 border-0 shadow-lg'
        }
    }).then((result) => {
        let url = '';
        
        if (result.isConfirmed) {
            $('#modalSolicitud').modal('hide');
            prepararImpresion(res.id);

    setTimeout(() => {
        ejecutarImpresion();
        cargarSolicitudes();
    }, 500);
        } else if (result.isDenied) {
            url = `/cfsistem/app/controllers/solicitudesCompraController.php`;
        }

        // Si se seleccionó una opción válida, abre la pestaña
        if (url !== '') {
            window.open(url, '_blank');
        }
        
        // Finalmente recarga la página actual
      
    });
} else {

            Swal.fire(
                'Error',
                res.message,
                'error'
            );
        }

    } catch (e) {

        Swal.fire(
            'Error',
            'Fallo de conexión',
            'error'
        );
    }
});

// =====================================================
// CONVERTIR A COMPRA
// =====================================================

$('#formConvertirCompra').on('submit', async function(e) {

    e.preventDefault();

    Swal.fire({
        title: 'Procesando ingreso...',
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading()
    });

    try {

        const resp = await fetch(
            `${URL_CONTROLADOR}?action=convertirACompra`, {
                method: 'POST',
                body: new FormData(this)
            }
        );

        const res = await resp.json();

        if (res.status === 'success') {

            await Swal.fire({
                icon: 'success',
                title: 'Ingresado',
                text: res.message
            });

            location.reload();

        } else {

            Swal.fire(
                'Error',
                res.message,
                'error'
            );
        }

    } catch (e) {

        Swal.fire(
            'Error',
            'Fallo de conexión',
            'error'
        );
    }
});

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

function nuevaSolicitud() {

    $('#formSolicitud')[0].reset();

    $('#tablaDetalle tbody').empty();

    $('#emptyState').removeClass('d-none');

    $('#modalSolicitud').modal('show');
    recargarProductos();
}
</script>