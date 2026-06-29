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
                    <div class="row g-3 mb-4 p-4 rounded-4 bg-white shadow-sm align-items-end border">
    
    <!-- Almacén de Cargo -->
    <div class="col-md-3">
        <label for="almacen_id" class="form-label small fw-semibold text-secondary text-uppercase mb-2">
            <i class="bi bi-box-seam me-1"></i> Almacén de Cargo
        </label>
        <select name="almacen_id" id="almacen_id" class="form-select border-light-subtle shadow-sm" required>
            <option value="">Seleccionar ubicación...</option>
            <?php foreach($almacenes as $a): ?>
                <option value="<?= $a['id'] ?>"><?= $a['nombre'] ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <!-- Cliente -->
    <div class="col-md-3">
        <label for="cliente_id" class="form-label small fw-semibold text-secondary text-uppercase mb-2">
            <i class="bi bi-person me-1"></i> Cliente
        </label>
        <div class="input-group shadow-sm">
            <select name="cliente_id" id="cliente_id" class="form-select select2-modal border-light-subtle" required>
                <option value="">Seleccionar cliente...</option>
                <?php foreach($clientes as $p): ?>
                    <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['nombre_comercial']) ?></option>
                <?php endforeach; ?>
            </select>
            <button class="btn btn-outline-primary" type="button" onclick="abrirModalNuevoCliente()" title="Nuevo Cliente">
                <i class="bi bi-person-plus-fill"></i>
            </button>
        </div>
    </div>

    <!-- Vendedor -->
    <div class="col-md-2">
        <label for="vendedor-select" class="form-label small fw-semibold text-secondary text-uppercase mb-2">
            <i class="bi bi-person-badge me-1"></i> Vendedor
        </label>
        <select class="form-select border-light-subtle shadow-sm" id="vendedor-select" name="usuario_id3">
            <option value="">Seleccione vendedor</option>
        </select>
    </div>

    <!-- Añadir Producto -->
    <div class="col-md-4">
        <label for="buscadorProductos" class="form-label small fw-semibold text-secondary text-uppercase mb-2">
            <i class="bi bi-search me-1"></i> Añadir Producto (SKU o Nombre)
        </label>
        <div class="input-group shadow-sm">
            <select id="buscadorProductos" class="form-select select2-modal border-light-subtle">
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
            <button type="button" class="btn btn-primary d-flex align-items-center" onclick="abrirModalProducto()" title="Agregar nuevo producto">
                <i class="bi bi-plus-lg me-1"></i>
                <span>Nuevo</span>
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
                                    <th style="width: 25%;">Tipo de precio</th>
                                    <th  class="ps-4">Precio Unitario</th>
                                    <th style="width: 50%";>Precio</th>
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
                         <input 
            type="hidden"
            id="totalCotizacion"
            name="totalCotizacion"
            
           
           >
            

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

    async function cargarVendedores() {
    const select = document.getElementById('vendedor-select');
    if (!select) return; // Seguridad por si el select no está en la vista actual

    try {
        // 1. Realizar la petición a tu controlador de Cf System
        const url = '/cfsistem/app/controllers/ventasHistorialController.php?action=obtenerUsuarios';
        const respuesta = await fetch(url);
        
        if (!respuesta.ok) throw new Error('Error en la respuesta del servidor');
        
        const resultado = await respuesta.json();

        // 2. Verificar que la respuesta sea exitosa y contenga los datos
        if (resultado.success && Array.isArray(resultado.data)) {
            
            // Limpiamos el select y dejamos una opción inicial neutra
           // select.innerHTML = '<option value="" selected disabled> Seleccione vendedor</option>';

            // 3. Recorrer los usuarios y crear las opciones
            resultado.data.forEach(usuario => {
                const opcion = document.createElement('option');
                opcion.value = usuario.id; // El ID que se enviará en el formulario
                
                // Formateamos el texto: "Nombre (Almacén - Rol)" para que sea súper descriptivo
                const almacen = usuario.almacen_nombre || 'Sin Almacén';
                opcion.textContent = `${usuario.nombre}`;
                
                // Agregamos la opción al select
                select.appendChild(opcion);
            });

        } else {
            select.innerHTML = '<option value="">No se pudieron cargar los usuarios</option>';
            console.error('El backend no devolvió success:true o la estructura cambió');
        }

    } catch (error) {
        select.innerHTML = '<option value="">Error al cargar la lista</option>';
        console.error('Error al ejecutar cargarUsuariosSelect:', error);
    }
}

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

        const precioUnitarioOriginal = parseFloat(
            fila.querySelector('.precio-unitario').value
        ) || 0;

        // ✅ obtener select correcto
        const selectUnidad = fila.querySelector('.unidad-select');

        // ✅ obtener equivalencia del option seleccionado
        const equivalencia = parseFloat(
            selectUnidad?.selectedOptions[0]?.dataset?.equivalencia
        ) || 0;

        console.log('equivalencia:', equivalencia);

        // 🔥 APLICAR equivalencia al precio
        const precioUnitarioAjustado =
            precioUnitarioOriginal;

        // 🔥 TOTAL
        const precioTotal = (cantidad) * precioUnitarioAjustado;

        console.log('precio ajustado:', precioUnitarioAjustado);
        console.log('total:', precioTotal);

        fila.querySelector('.precio-total').value =
            precioTotal.toFixed(2);

        // =====================================
        // SUMA GENERAL
        // =====================================

        let totaLCompra = 0;

        document.querySelectorAll('.precio-total')
            .forEach(el => {
                totaLCompra += parseFloat(el.value) || 0;
            });

        document.getElementById('costoTotalCompra')
            .textContent = totaLCompra.toLocaleString('es-MX', {
                style: 'currency',
                currency: 'MXN'
            });
             document.getElementById('totalCotizacion').value=totaLCompra;

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
            `/cfsistem/app/controllers/cotizacionesController.php?action=obtenerProductos`
        );


        const res = await resp.json();
        console.log(res);

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
            option.dataset.medidas = JSON.stringify(pr.medidas_adicionales || []);
            option.dataset.sku = pr.sku;
            option.dataset.um = pr.unidad_medida;
            option.dataset.ur = pr.unidad_reporte;
            option.dataset.preMin = pr.precio_minorista;
            option.dataset.preMat = pr.precio_mayorista;
            option.dataset.preDis = pr.precio_distribuidor;
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
    const medidas = JSON.parse(d.medidas || '[]');

    let opcionesUnidad = ``;
    console.log(medidas);
    medidas.forEach(m => {

        opcionesUnidad += `
   
    <option 
        value="${m.id}"
        data-equivalencia="${m.equivalencia}"
        data-medida-id="${m.id}"
    >
        ${m.nombre}
    </option>

    `;
    });

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
            value="0"
            min="0.01"
            required
            oninput="calcularTotalSol(this)">
        
    </td>

    <!-- UNIDAD -->
    <td>
        <select 
            name="items[${id}][unidad]" 
            class="form-select unidad-select unidad"
            onchange="calcularPreciosugerido(this)">
           <option 
    value="0"
    data-equivalencia="0"
    data-medida-id="0">
    Seleccione
    </option>
            ${opcionesUnidad}
        </select>
        
    </td>
    
<td>
    <select 
        name="items[${id}][tipoPrecio]" 
        class="form-select tipoPrecio-select tipoPrecio"
        onchange="calcularPreciosugerido(this)"
    > <option value="seleccionar" data-precio="0">
          seleccione
        </option>
        <option value="minorista" data-precio="${d.preMin }">
            Min ${d.preMin * d.factor} x ${d.ur}
        </option>

        <option value="mayorista" data-precio="${d.preMat }">
            May ${d.preMat* d.factor} x ${d.ur}
        </option>

        <option value="distribuidor" data-precio="${d.preDis }">
            Dis ${d.preDis* d.factor} x ${d.ur}
        </option>
    </select>
</td>
    <!-- COSTO UNITARIO -->
    <td>
        <input 
            type="number"
            lang="en-US"
            name="items[${id}][precioUnitario]"
            class="form-control precio-unitario precio_unitario"
            step="0.01"
            
            min="0"
            placeholder="0.00"
            required
            oninput="calcularTotalSol(this)"
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
            oninput="calcularTotalSol(this)"
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

function calcularPreciosugerido(select) {

    const fila = select.closest('tr');

    const inputPrecio = fila.querySelector('.precio-unitario');

    const unidadSelect = fila.querySelector('.unidad-select');
    const tipoSelect = fila.querySelector('.tipoPrecio-select');

    const unidadOption = unidadSelect.options[unidadSelect.selectedIndex];
    const tipoOption = tipoSelect.options[tipoSelect.selectedIndex];

    const equivalencia = Number(unidadOption?.dataset.equivalencia || 1);
    const precioBase = Number(tipoOption?.dataset.precio || 0);

    const sugerido = precioBase / equivalencia;

    // SOLO PLACEHOLDER
    inputPrecio.placeholder = sugerido.toFixed(4);
}
document.addEventListener('input', function(e) {

    if (e.target.classList.contains('precio-unitario')) {
        e.target.dataset.editado = "1";
    }
});
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
    vendedor: $('#vendedor-select').val(),
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
    cargarVendedores();
}
</script>