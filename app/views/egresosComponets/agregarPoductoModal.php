<div class="modal fade" id="modalAgregarProducto" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">

            <!-- HEADER -->
            <div class="modal-header bg-dark text-white py-3">
                <h5 class="modal-title">
                    <i class="bi bi-plus-circle-fill me-2"></i> Nuevo Producto al Catálogo
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <!-- FORM -->
            <form id="formAgregarProducto" autocomplete="off">

                <div class="modal-body bg-light p-4">

                    <!-- 🔹 BLOQUE: INFORMACIÓN GENERAL -->
                    <div class="card border-0 shadow-sm mb-4 rounded-4">
                        <div class="card-body">
                            <h6 class="fw-bold mb-3 text-dark">
                                <i class="bi bi-box-seam me-2"></i>Información del Producto
                            </h6>

                            <div class="row g-3">

                                <input type="hidden" name="precio_adquisicion" value="0">

                                <div class="col-md-4">
                                    <label class="form-label small text-muted">SKU / Código</label>
                                    <input type="text" name="sku" class="form-control shadow-sm" required>
                                </div>

                                <div class="col-md-8">
                                    <label class="form-label small text-muted">Nombre del Producto</label>
                                    <input type="text" name="nombre" id="nombreProducto" class="form-control shadow-sm" required>
                                </div>

                                <div class="col-md-6">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <label class="form-label small text-muted">Categoría</label>
                                        <button type="button" class="btn btn-sm btn-light border rounded-circle"
                                            onclick="abrirSubModalCategoria()" title="Agregar categoría">
                                            <i class="bi bi-plus"></i>
                                        </button>
                                    </div>

                                    <select name="categoria_id" id="select_categoria_id" class="form-select shadow-sm"
                                        required>
                                        <option value="">Seleccionar categoría...</option>
                                    </select>
                                </div>

                            </div>
                        </div>
                    </div>

                    <!-- 🔹 BLOQUE: UNIDADES -->
                    <div class="card border-0 shadow-sm mb-4 rounded-4">
                        <div class="card-body">
                            <h6 class="fw-bold mb-3 text-dark">
                                <i class="bi bi-diagram-3 me-2"></i>Unidades y Conversión
                            </h6>

                            <div class="row g-3">
                                <div class="col-md-4">


                                    <label class="form-label small fw-bold text-secondary">UNIDAD BASE (VENTA)</label>
                                    <select required id="u_mayoreo" name="unidad_reporte"
                                        class="form-select border-0 shadow-sm fw-bold">
                                        <option value="">Seleccione...</option>
                                        
                                    </select>
                                </div>

                                <div class="col-md-4">

                                    <label class="form-label small fw-bold text-secondary">UNIDAD BASE (VENTA)</label>
                                    <select required name="unidad_medida" id="u_base"
                                        class="form-select border-0 shadow-sm fw-bold">
                                        <option value="">Seleccione...</option>
                                      
                                    </select>

                                </div>



                                <div class="col-md-4">
                                    <label class="form-label small text-muted">Factor de conversión</label>
                                    <input type="number" id="f_conversion" name="factor_conversion"
                                        class="form-control shadow-sm" value="1">
                                    <small id="helper-conversion" class="text-primary"></small>
                                </div>

                            </div>
                        </div>
                    </div>

                    <!-- 🔹 BLOQUE: DATOS FISCALES -->
                    <div class="card border-0 shadow-sm mb-4 rounded-4">
                        <div class="card-body">
                            <h6 class="fw-bold mb-3 text-dark">
                                <i class="bi bi-receipt me-2"></i>Datos Fiscales
                            </h6>

                            <div class="row g-3">

                                <div class="col-md-4">
                                    <label class="form-label small text-muted">IVA (%)</label>
                                    <input type="number" name="impuesto_iva" class="form-control shadow-sm" value="16">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label small text-muted">Clave SAT</label>
                                    <input type="text" name="fiscal_clave_prod" class="form-control shadow-sm">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label small text-muted">Clave Unidad</label>
                                    <input type="text" name="fiscal_clave_unidad" class="form-control shadow-sm">
                                </div>

                            </div>
                        </div>
                    </div>

                    <!-- 🔹 BLOQUE: PRECIOS -->
                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-body">
                            <h6 class="fw-bold mb-3 text-dark">
                                <i class="bi bi-cash-coin me-2"></i>Precios de Venta
                            </h6>

                            <div class="row g-3">

                                <div class="col-md-4">
                                    <label class="form-label small text-muted">Minorista</label>
                                    <input type="number" step="0.01" name="precio_minorista" class="form-control shadow-sm" value="0">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label small text-muted">Mayorista</label>
                                    <input type="number" step="0.01" name="precio_mayorista" class="form-control shadow-sm"value="0">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label small text-muted">Distribuidor</label>
                                    <input type="number" step="0.01" name="precio_distribuidor" class="form-control shadow-sm"value="0">
                                </div>

                            </div>
                        </div>
                    </div>

                </div>

                <!-- FOOTER -->
                <div class="modal-footer bg-white border-0 px-4 pb-4">

                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">
                        Cancelar
                    </button>

                    <button type="submit" id="btnGuardarProducto" class="btn btn-dark rounded-pill px-4">
                        <i class="bi bi-save me-2"></i> Guardar producto
                    </button>

                </div>

            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="modalNuevaCategoria" style="z-index: 10000;" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h6 class="modal-title">Nueva Categoría</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formRapidoCategoria">
                    <div class="mb-3">
                        <label class="form-label small">Nombre de la Categoría</label>
                        <input type="text" name="nombre" id="nombre" class="form-control" placeholder="Ej: Herramientas"
                            required>
                    </div>
                    <button type="button" onclick="guardarCategoriaRapida()" class="btn btn-success w-100">
                        <i class="bi bi-save"></i> Guardar
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    

    
function abrirSubModalCategoria() {
    // Simplemente abrimos el modal de categoría sin cerrar el anterior
    const myModal = new bootstrap.Modal(document.getElementById('modalNuevaCategoria'), {
        backdrop: 'static', // Evita que se cierre el de atrás si haces clic fuera
        keyboard: false
    });
    myModal.show();
}
function generarSKU(nombre) {
    if (!nombre) return '';

    // limpiar acentos y pasar a mayúsculas
    let limpio = nombre
        .normalize("NFD")
        .replace(/[\u0300-\u036f]/g, "")
        .toUpperCase();

    // separar palabras
    const palabras = limpio.split(' ').filter(p => p.length > 0);

    // tomar primeras 2 letras de la primera palabra
    let prefijo = palabras[0].substring(0, 2);

    // buscar número en todo el texto
    const matchNumero = limpio.match(/\d+/);
    let numero = matchNumero ? matchNumero[0] : 1;
 numerorandom= Math.floor(Math.random() * 10000); // 0 - 9999

    return numero ? `${prefijo}-${numero}-${numerorandom}` : prefijo;

}
document.addEventListener('DOMContentLoaded', () => {
    const inputNombre = document.querySelector('input[name="nombre"]');
    const inputSKU = document.querySelector('input[name="sku"]');

    inputNombre.addEventListener('input', function () {
        inputSKU.value = generarSKU(this.value);
    });
});
function guardarCategoriaRapida() {

    const input = document.getElementById('nombre');
    const nombre = input.value.trim();

    if (!nombre) {
        return Swal.fire('Error', 'Escribe un nombre', 'error');
    }

    const formData = new FormData();
    formData.append('nombre', nombre);

    fetch('/cfsistem/app/controllers/egresosController.php?action=guardarCategoria', {
            method: 'POST',
            body: formData
        })
        .then(res => res.text()) // 🔥 CAMBIO CLAVE
        .then(text => {

            console.log("RESPUESTA CRUDA:", text);

            let data;

            try {
                data = JSON.parse(text);
            } catch (e) {
                console.error("Error parseando JSON:", text);
                return Swal.fire('Error', 'Respuesta inválida del servidor', 'error');
            }

            if (data.status === 'success') {

                const id = data.id;

                // 🔥 1. ACTUALIZAR TODOS LOS SELECTS
                document.querySelectorAll('select[name="categoria_id"]').forEach(select => {

                    const existe = Array.from(select.options)
                        .some(opt => opt.value == id);

                    if (!existe) {
                        const nuevaOpcion = new Option(data.nombre, id);
                        select.add(nuevaOpcion);
                    }

                    select.value = String(id);
                });

                // 🔥 2. RECARGAR SELECT ESPECÍFICO
                const selectPrincipal = document.getElementById('select_categoria_id');

                if (selectPrincipal) {

                    fetch('/cfsistem/app/controllers/almacenes?action=getCategoriasJSON')
                        .then(res => res.json())
                        .then(categorias => {

                            selectPrincipal.innerHTML = '<option value="">Seleccione...</option>';

                            categorias.forEach(cat => {
                                const option = new Option(cat.nombre, cat.id);
                                selectPrincipal.add(option);
                            });

                            selectPrincipal.value = String(id);
                        });
                }

                // 🔥 3. CERRAR MODAL
                const modal = bootstrap.Modal.getOrCreateInstance(
                    document.getElementById('modalNuevaCategoria')
                );
                modal.hide();

                // 🔥 4. LIMPIAR INPUT
                input.value = '';

                // 🔥 5. FIX SCROLL
                setTimeout(() => {
                    if (document.querySelectorAll('.modal.show').length > 0) {
                        document.body.classList.add('modal-open');
                    }
                }, 300);

                // 🔥 6. MENSAJE
                Swal.fire({
                    title: '¡Éxito!',
                    text: 'Categoría guardada y seleccionada.',
                    icon: 'success',
                    timer: 1200,
                    showConfirmButton: false
                });

            } else {
                Swal.fire('Error', data.message || 'Error desconocido', 'error');
            }

        })
        .catch(error => {
            console.error("FETCH ERROR:", error);
            Swal.fire('Error', 'No se pudo procesar la categoría', 'error');
        });
}
</script>
<script>
function iniciarModuloProducto() {

    // Esperar a que jQuery esté listo (si lo usas)
    if (typeof $ === 'undefined') {
        setTimeout(iniciarModuloProducto, 100);
        return;
    }

    const ProdModulo = {

        urlControlador: 'almacenes.php',

        init: function() {
            this.bindEvents();
            this.cargarCategorias();
            this.actualizarTexto();
            this.cargarUnidades();
        },

        bindEvents: function() {

            // 🔥 Inputs dinámicos
            $('#u_mayoreo, #u_base, #f_conversion')
                .off('input')
                .on('input', () => this.actualizarTexto());

            // 🔥 Cuando se abre el modal
            const modalEl = document.getElementById('modalAgregarProducto');
            modalEl.addEventListener('show.bs.modal', () => {
                this.cargarCategorias();
                 this.cargarUnidades();
            });

            // 🔥 Submit
            $('#formAgregarProducto')
                .off('submit')
                .on('submit', (e) => {
                    e.preventDefault();
                    this.guardar();
                });
        },

        // 🔥 Cargar categorías
        cargarCategorias: function() {

            const select = $('#select_categoria_id');
            select.html('<option value="">Cargando...</option>');

            $.ajax({
                url: '/cfsistem/app/controllers/almacenes.php?action=getCategoriasJSON',
                type: 'GET',
                dataType: 'json',

                success: (data) => {

                    select.empty().append('<option value="">Seleccionar...</option>');

                    if (Array.isArray(data)) {
                        data.forEach(cat => {
                            select.append(
                                `<option value="${cat.id}">${cat.nombre}</option>`);
                        });
                    }
                },

                error: () => {
                    select.html('<option value="">Error al cargar</option>');
                }
            });
        },
cargarUnidades: function() {

            const select = $('#u_mayoreo');
            select.html('<option value="">Cargando...</option>')
            const select_unidad = $('#u_base');
            select_unidad.html('<option value="">Cargando...</option>');

            $.ajax({
                url: '/cfsistem/app/controllers/almacenes.php?action=getUnidadesMedidaJSON',
                type: 'GET',
                dataType: 'json',

                success: (data) => {

                    select.empty().append('<option value="">Seleccionar...</option>');
select_unidad.empty().append('<option value="">Seleccionar...</option>');

                    if (Array.isArray(data)) {
                        data.forEach(uni => {
                            select.append(
                             
                                        
                                        
                                `<option value="${uni.clave}">
                                            ${uni.nombre} - ${uni.clave}
                                        </option>`);
                                        select_unidad.append(
                             
                                        
                                        
                                `<option value="${uni.clave}">
                                            ${uni.nombre} - ${uni.clave}
                                        </option>`);
                        });
                       
                    }
                },

                error: () => {
                    select.html('<option value="">Error al cargar</option>');
                }
            });
        },
        // 🔥 Texto conversión
        actualizarTexto: function() {

            let m = $('#u_mayoreo').val() || 'Unidad';
            let b = $('#u_base').val() || 'PZA';
            let f = $('#f_conversion').val() || '1';

            $('#helper-conversion').text(`1 ${m} = ${f} ${b}(s)`);
        },

        // 🔥 Guardar producto
        guardar: function() {

            const btn = $('#btnGuardarProducto');

            btn.prop('disabled', true).html('Guardando...');

            $.ajax({
                url: this.urlControlador + '?action=guardar',
                type: 'POST',
                data: $('#formAgregarProducto').serialize(),
                dataType: 'json',

                success: (res) => {

                    if (res.status === 'success') {

                        Swal.fire({
                            icon: 'success',
                            title: 'Producto guardado',
                            text: res.message,
                            timer: 1500,
                            showConfirmButton: false
                        });
                        verListaMedidas(res.id,1,$('#nombreProducto').val() ,$('#u_base').val() );
                        if (typeof recargarProductos === 'function') {

     recargarProductos();

    // 🔥 si es async espera
 

}

                        // 🔥 cerrar modal (Bootstrap 5)
                        const modal = bootstrap.Modal.getInstance(
                            document.getElementById('modalAgregarProducto')
                        );
                        modal.hide();

                        // limpiar form
                        $('#formAgregarProducto')[0].reset();
                        this.actualizarTexto();

                        // refrescar si existe función externa
                        if (typeof refrescarListaProductosCompra === "function") {
                            refrescarListaProductosCompra(res.id);
                        }

                    } else {
                        Swal.fire('Error', res.message, 'error');
                    }
                },

                error: () => {
                    Swal.fire('Error', 'Error de conexión con el servidor', 'error');
                },

                complete: () => {
                    btn.prop('disabled', false)
                        .html('<i class="bi bi-save me-2"></i> Guardar');
                }
            });
        }
    };

    // 🔥 iniciar módulo
    ProdModulo.init();


    // ===============================
    // 🔥 FUNCIONES GLOBALES
    // ===============================

    // abrir modal producto
    window.abrirModalProducto = function() {
        new bootstrap.Modal(
            document.getElementById('modalAgregarProducto')
        ).show();
    };

    // abrir modal categoría
    window.abrirModalCategoria = function() {
        new bootstrap.Modal(
            document.getElementById('modalAgregarCategoria')
        ).show();
    };

    // guardar categoría
    window.ejecutarGuardarCategoria = function() {

        const nombre = $('#inputNombreCategoria').val().trim();

        if (!nombre) return;

        $.post('almacenes.php?action=guardarCategoria', {
            nombre
        }, function(res) {

            if (res.status === "success") {

                ProdModulo.cargarCategorias();

                setTimeout(() => {
                    $('#select_categoria_id').val(res.id);
                }, 300);

                const modalCat = bootstrap.Modal.getInstance(
                    document.getElementById('modalAgregarCategoria')
                );
                modalCat.hide();

                $('#inputNombreCategoria').val('');
            }

        }, 'json');
    };
}

// 🔥 iniciar todo
document.addEventListener('DOMContentLoaded', iniciarModuloProducto);
</script>
<!-- =========================================
MODAL LISTA DE MEDIDAS
========================================= -->
<div class="modal fade" id="modalListaMedidas" tabindex="-1" aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered modal-lg">

        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">

            <!-- HEADER -->
            <div class="modal-header bg-dark text-white border-0">

                <div>
                    <h5 class="modal-title fw-bold mb-0">
                        <i class="bi bi-rulers me-2"></i>
                        Medidas Disponibles
                    </h5>

                    <small
                        id="subtituloListaMedidas"
                        class="text-white-50">

                        Cargando detalles...
                    </small>
                </div>

                <button
                    type="button"
                    class="btn-close btn-close-white"
                    data-bs-dismiss="modal">
                </button>

            </div>
  
            <!-- BODY -->
            <div class="modal-body bg-light p-0">

                <div class="table-responsive">

                    <table class="table table-hover align-middle mb-0">
                         <button type="button" id="agregarMedida"
        class="btn btn-dark rounded-pill shadow-sm" 
       
    <i class="bi bi-plus-circle me-2"></i>
    Agregar Medida
</button>

                        <thead class="table-light">

                            <tr>
                                <th class="ps-4">Nombre Medida</th>
                                <th>Equivalencia</th>
                                <th class="text-end pe-4">Acciones</th>
                            </tr>

                        </thead>

                        <tbody id="tablaCuerpoMedidas">

                            <!-- JS -->

                        </tbody>

                    </table>

                </div>

                <!-- EMPTY -->
                <div
                    id="listaVacia"
                    class="text-center py-5 d-none">

                    <i class="bi bi-info-circle fs-2 text-muted"></i>

                    <p class="text-muted mt-2 mb-0">
                        No hay medidas adicionales para este producto.
                    </p>

                </div>

            </div>

            <!-- FOOTER -->
            <div class="modal-footer border-0 bg-white">

                <button
                    type="button"
                    class="btn btn-light rounded-pill px-4"
                    data-bs-dismiss="modal">

                    Cerrar
                </button>

            </div>

        </div>

    </div>

</div>


<script>

const URL_MEDIDAS =
    '/cfsistem/app/controllers/productosController.php';

    
let ultimaMedidaProductoId = 0;
let ultimaMedidaAlmacenId = 0;
let ultimaMedidaNombreProducto = '';
let ultimaUnidadMedida = '';


// =========================================
// VER LISTA MEDIDAS
// =========================================

async function verListaMedidas(
    idProducto,
    idAlmacen,
    nombreProducto,
    unidad_medida
) {
  ultimaMedidaProductoId = idProducto;
    ultimaMedidaAlmacenId = idAlmacen;
    ultimaMedidaNombreProducto = nombreProducto;
    ultimaUnidadMedida = unidad_medida;
    const tbody =
        document.getElementById('tablaCuerpoMedidas');

    const subtitulo =
        document.getElementById('subtituloListaMedidas');

    const emptyState =
        document.getElementById('listaVacia');


    // =========================================
    // PREPARAR UI
    // =========================================

    subtitulo.innerText =
        `Producto: ${nombreProducto}`;

    emptyState.classList.add('d-none');

    tbody.innerHTML = `
        <tr>
            <td colspan="3" class="text-center py-4">

                <div
                    class="spinner-border spinner-border-sm text-secondary"
                    role="status">
                </div>

                <span class="ms-2">
                    Cargando medidas...
                </span>

            </td>
        </tr>
    `;


    // =========================================
    // MODAL
    // =========================================

    const modalEl =
        document.getElementById('modalListaMedidas');

    let myModal =
        bootstrap.Modal.getInstance(modalEl);

    if (!myModal) {

        myModal =
            new bootstrap.Modal(modalEl);
    }

    myModal.show();


    try {

        // =========================================
        // FETCH
        // =========================================

        const resp = await fetch(
            `${URL_MEDIDAS}?action=obtnerMedidas&id=${idProducto}`
        );
        
        if (!resp.ok) {

            throw new Error('Error en la red');
        }

        const data = await resp.json();
        console.log(data.producto.medidas);


        tbody.innerHTML = '';


        // =========================================
        // DATOS
        // =========================================

        if (
            data.status 
           
           
        ) {
          
 $('#agregarMedida')
                  
                    .attr(
                        'onclick',
                        `prepararNuevaMedida(${idProducto}, ${idAlmacen},'${nombreProducto}','${unidad_medida}')`
                    );

            data.producto.medidas.forEach(m => {

                // 🔥 SOLUCIÓN SEGURA
                const medidaData =
                    encodeURIComponent(
                        JSON.stringify(m)
                    );

                const fila = `

                    <tr>

                        <td class="ps-4">

                            <div class="fw-bold text-dark">
                                ${m.nombre}
                            </div>

                        </td>

                        <td>

                            <span class="badge bg-light text-dark border px-3 py-2">
                            ${m.equivalencia} ${m.nombre}s =  1 ${unidad_medida} 
                               
                               

                            </span>

                        </td>

                        <td class="text-end pe-4">

                            <button
                                class="btn btn-sm btn-light rounded-circle me-1 shadow-sm"

                                onclick="abrirEditarMedida('${medidaData}')">

                                <i class="bi bi-pencil text-primary"></i>

                            </button>

                            <button
                                class="btn btn-sm btn-light rounded-circle shadow-sm"

                                onclick="eliminarMedida(${m.id})">

                                <i class="bi bi-trash text-danger"></i>

                            </button>

                        </td>

                    </tr>
                `;

                tbody.insertAdjacentHTML(
                    'beforeend',
                    fila
                );

            });

        } else {

            emptyState.classList.remove('d-none');
        }

    } catch (error) {

        console.error("Error:", error);

        tbody.innerHTML = `

            <tr>

                <td
                    colspan="3"
                    class="text-center py-4 text-danger">

                    <i class="bi bi-exclamation-triangle me-2"></i>

                    No se pudo cargar la información

                </td>

            </tr>
        `;
    }
}


function recargarModalMedidas() {

    verListaMedidas(
        ultimaMedidaProductoId,
        ultimaMedidaAlmacenId,
        ultimaMedidaNombreProducto,
        ultimaUnidadMedida
    );
}
// =========================================
// ABRIR EDITAR
// =========================================

function abrirEditarMedida(data) {

    const medida =
        JSON.parse(
            decodeURIComponent(data)
        );

    console.log(medida);

    // AQUÍ LLENAS TU MODAL
}


</script>
<!-- =========================================
MODAL EDITAR MEDIDA
========================================= -->
<div class="modal fade"
     id="modalEditarMedida"
     tabindex="-1"
     aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">

            <!-- HEADER -->
            <div class="modal-header bg-primary text-white border-0">

                <div>
                    <h5 class="modal-title fw-bold mb-0">
                        <i class="bi bi-pencil-square me-2"></i>
                        Editar Medida
                    </h5>

                    <small class="text-white-50">
                        Modifica la equivalencia
                    </small>
                </div>

                <button type="button"
                        class="btn-close btn-close-white"
                        data-bs-dismiss="modal">
                </button>

            </div>

            <!-- FORM -->
            <form id="formEditarMedida">

                <input type="hidden"
                       id="edit_medida_id"
                       name="id">

                <input type="hidden"
                       id="edit_producto_id"
                       name="producto_id">

                <div class="modal-body bg-light p-4">

                    <!-- NOMBRE -->
                    <div class="mb-3">

                      <!-- NOMBRE -->
<div class="mb-3">
    <label class="form-label fw-semibold small text-uppercase text-muted">
        Nombre
    </label>
 <input type="text"
       id="edit_nombre_medida"
       class="form-control rounded-3"
       name="nombre_edit"
       required>
</div>



                    </div>

                    <!-- EQUIVALENCIA -->
                    <div class="mb-3">

                        <label class="form-label fw-semibold small text-uppercase text-muted">
                            Equivalencia
                        </label>

                        <div class="input-group">

                            <input type="number"
                                   class="form-control"
                                   id="edit_equivalencia"
                                   name="equivalencia"
                                   step="0.000000001"
                                   min="0.0001"
                                   required>

                            <span class="input-group-text"
                                  id="edit_unidad_text">
                            </span>

                        </div>

                    </div>

                </div>

                <!-- FOOTER -->
                <div class="modal-footer border-0 bg-white px-4 pb-4">

                    <button type="button"
                            class="btn btn-light rounded-pill px-4"
                            data-bs-dismiss="modal">

                        Cancelar
                    </button>

                    <button type="submit"
                            class="btn btn-primary rounded-pill px-5 shadow-sm">

                        <i class="bi bi-check-circle me-2"></i>
                        Guardar Cambios
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

<style>

/* =========================================
Z INDEX MODAL
========================================= */

#modalEditarMedida {
    z-index: 9999 !important;
}

#modalEditarMedida + .modal-backdrop {
    z-index: 9998 !important;
}

</style>

<script>

// =========================================
// ABRIR MODAL EDITAR
// =========================================

function abrirEditarMedida(data) {
    // 1. Decodificar los datos
    const medida = JSON.parse(decodeURIComponent(data));
    console.log("Cargando en modal:", medida);

    // 2. Asignación mediante IDs únicos
    // Usamos value para inputs y innerText para etiquetas
    
    const inputId = document.getElementById('edit_medida_id');
    const inputProdId = document.getElementById('edit_producto_id');
    const inputNombre = document.getElementById('edit_nombre_medida'); // ID único
    const inputEquiv = document.getElementById('edit_equivalencia');
    const textUnidad = document.getElementById('edit_unidad_text');

    if (inputId) inputId.value = medida.id;
    if (inputProdId) inputProdId.value = medida.producto_id;
    if (inputEquiv) inputEquiv.value = medida.equivalencia;
    if (textUnidad) textUnidad.innerText = medida.nombre;

    // 3. LA CORRECCIÓN CRÍTICA:
    if (inputNombre) {
        inputNombre.value = medida.nombre;
        console.log("Nombre asignado al input:", inputNombre.value);
    } else {
        console.error("No se encontró el input con ID: edit_nombre_medida");
    }

    // 4. Abrir Modal
    const modalEl = document.getElementById('modalEditarMedida');
    const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
    modal.show();
}
// =========================================
// GUARDAR CAMBIOS
// =========================================

document
    .getElementById('formEditarMedida')
    .addEventListener('submit', async function(e) {

        e.preventDefault();

        try {

            Swal.fire({
                title: 'Actualizando...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading(),
                customClass: {
                    popup: 'miSwalZ'
                }
            });

            const formData =
                new FormData(this);

            const resp = await fetch(
                `${URL_MEDIDAS}?action=actualizarMedidaAdicional`,
                {
                    method: 'POST',
                    body: formData
                }
            );

            const data =
                await resp.json();

            Swal.close();

            if (data.status || data.success) {
      

                await Swal.fire({
                    icon: 'success',
                    title: 'Actualizado',
                    text: 'La medida fue actualizada correctamente',
                    timer: 1500,
                    showConfirmButton: false,
                    customClass: {
                        popup: 'miSwalZ'
                    }
                });
       
                // CERRAR MODAL
                const modalEditar =
    bootstrap.Modal.getInstance(
        document.getElementById('modalEditarMedida')
    );

if (modalEditar) {
    modalEditar.hide();
}
recargarModalMedidas();
               
              

                // RECARGAR LISTA
                const productoId =
                    document.getElementById('edit_producto_id').value;

                console.log('Recargar lista de medidas');

            } else {

                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: data.message || 'No se pudo actualizar',
                    customClass: {
                        popup: 'miSwalZ'
                    }
                });
                
            }

        } catch (error) {

            console.error(error);

            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Falló la comunicación con el servidor',
                customClass: {
                    popup: 'miSwalZ'
                }
            });
        }

    });
// =========================================
// ELIMINAR MEDIDA
// =========================================

async function eliminarMedida(id) {
    
    // Usamos el modal actual como target para que el alert herede el z-index o 
    // simplemente forzamos el z-index con customClass
    const swalConfig = {
        title: '¿Estás seguro?',
        text: "Esta acción no se puede deshacer",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#0d6efd',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar',
        customClass: {
            container: 'miSwalZ' // Asegúrate de que esta clase tenga z-index: 10000 en tu CSS
        }
    };

    const confirmacion = await Swal.fire(swalConfig);

    if (confirmacion.isConfirmed) {
        try {
            // Mostrar estado de carga
            Swal.fire({
                title: 'Eliminando...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading(),
                customClass: { container: 'miSwalZ' }
            });

            const formData = new FormData();
            formData.append('id', id);

            const resp = await fetch(`${URL_MEDIDAS}?action=eliminarMedidaAdicional`, {
                method: 'POST',
                body: formData
            });

            const data = await resp.json();

            if (data.status || data.success) {
                await Swal.fire({
                    icon: 'success',
                    title: '¡Eliminado!',
                    text: 'La medida ha sido removida.',
                    timer: 1500,
                    showConfirmButton: false,
                    customClass: { container: 'miSwalZ' }
                });
                recargarModalMedidas();

                // RECARGAR LA LISTA (Opcional: puedes llamar a verListaMedidas de nuevo)
                // location.reload(); // O tu lógica de refresco de tabla
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: data.message || 'No se pudo eliminar',
                    customClass: { container: 'miSwalZ' }
                });
            }
        } catch (error) {
            console.error(error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Fallo de comunicación con el servidor',
                customClass: { container: 'miSwalZ' }
            });
        }
    }
}
</script>
<!-- =========================================================
MODAL CREAR MEDIDA ADICIONAL
========================================================= -->

<style>

#modalMedidaAdicional{
    z-index:99999 !important;
}
.miSwalZ{
    z-index: 999999 !important;
}

.swal2-container{
    z-index: 999999 !important;
}


#modalMedidaAdicional .modal-content{
    border:none;
    border-radius:22px;
    overflow:hidden;
    box-shadow:0 18px 45px rgba(0,0,0,.18);
}

#modalMedidaAdicional .modal-header{
    background:linear-gradient(135deg,#111827,#1f2937);
    border:none;
}

#modalMedidaAdicional .form-control{
    height:48px;
    border-radius:14px;
    border:1px solid #dbe2ea;
}

#modalMedidaAdicional .form-control:focus{
    border-color:#111827;
    box-shadow:0 0 0 .2rem rgba(17,24,39,.12);
}

#modalMedidaAdicional .formula-box{
    background:#f8fafc;
    border:1px dashed #cbd5e1;
    border-radius:18px;
    padding:18px;
}

#equivalencia{
    background:#fff8e1;
    font-size:1.1rem;
    font-weight:700;
    text-align:center;
}

.tipo-card{
    border:1px solid #dbe2ea;
    border-radius:16px;
    padding:14px;
    cursor:pointer;
    transition:.2s ease;
    background:#fff;
}

.tipo-card:hover{
    border-color:#111827;
    transform:translateY(-1px);
}

.tipo-card input{
    transform:scale(1.2);
}

</style>

<div class="modal fade"
     id="modalMedidaAdicional"
     tabindex="-1"
     aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content">

            <!-- HEADER -->
            <div class="modal-header text-white p-4">

                <div>

                    <h5 class="modal-title fw-bold mb-1">

                        <i class="bi bi-rulers me-2"></i>
                        Nueva Medida

                    </h5>

                    <small id="infoProductoModal"
                           class="text-white-50">

                        Configura equivalencia

                    </small>

                </div>

                <button type="button"
                        class="btn-close btn-close-white"
                        data-bs-dismiss="modal">
                </button>

            </div>

            <!-- FORM -->
            <form id="formMedidaAdicional">

                <input type="hidden"
                       name="producto_id"
                       id="id_producto_crear">

                <input type="hidden"
                       name="almacen_id"
                       id="id_almacen_crear">

                <!-- BODY -->
                <div class="modal-body p-4">

                    <!-- NOMBRE -->
                    <div class="mb-4">

                        <label class="form-label fw-semibold small text-uppercase text-muted">

                            Nombre de la nueva unidad

                        </label>

                        <input type="text"
                               name="nombre"
                               id="nombreNuevaUnidad"
                               class="form-control"
                               placeholder="Ej: Caja, Gramo, Tonelada"
                               required>

                    </div>

                    <!-- TIPO -->
                    <div class="mb-4">

                        <label class="form-label fw-semibold small text-uppercase text-muted mb-3">

                            Tipo de conversión

                        </label>

                        <div class="row g-3">

                            <!-- MÁS GRANDE -->
                            <div class="col-md-6">

                                <label class="tipo-card w-100">

                                    <div class="d-flex align-items-center gap-2">

                                        <input type="radio"
                                               name="tipoConversion"
                                               value="grande"
                                               checked>

                                        <div>

                                            <div class="fw-bold">

                                                Unidad MÁS GRANDE

                                            </div>

                                            <small class="text-muted">

                                                Ej: Tonelada

                                            </small>

                                        </div>

                                    </div>

                                </label>

                            </div>

                            <!-- MÁS PEQUEÑA -->
                            <div class="col-md-6">

                                <label class="tipo-card w-100">

                                    <div class="d-flex align-items-center gap-2">

                                        <input type="radio"
                                               name="tipoConversion"
                                               value="pequena">

                                        <div>

                                            <div class="fw-bold">

                                                Unidad MÁS PEQUEÑA

                                            </div>

                                            <small class="text-muted">

                                                Ej: Gramo

                                            </small>

                                        </div>

                                    </div>

                                </label>

                            </div>

                        </div>

                    </div>

                    <!-- FORMULA -->
                    <div class="formula-box">

                        <div class="mb-3">

                            <label class="form-label fw-semibold small text-uppercase text-muted">

                                Conversión

                            </label>

                            <input type="number"
                                   id="cantidadConversion"
                                   class="form-control text-center fw-bold"
                                   step="0.00000001"
                                   min="0"
                                   placeholder="Escribe una cantidad">

                        </div>

                        <!-- TEXTO -->
                        <div class="alert alert-light border text-center mb-3">

                            <span id="textoFormula"
                                  class="fw-semibold">

                                Fórmula de conversión

                            </span>

                        </div>

                        <!-- RESULTADO -->
                        <div>

                            <label class="form-label fw-semibold small text-uppercase text-muted">

                                Equivalencia calculada

                            </label>

                            <input type="number"
                                   id="equivalencia"
                                   name="equivalencia"
                                   class="form-control"
                                   step="0.000000001"
                                   readonly>

                        </div>

                    </div>

                    <!-- EJEMPLO -->
                    <div class="alert alert-warning border mt-4 mb-0">

                        <small id="ejemploConversion">

                            Esperando datos...

                        </small>

                    </div>

                </div>

                <!-- FOOTER -->
                <div class="modal-footer border-0 bg-white px-4 pb-4">

                    <button type="button"
                            class="btn btn-light rounded-pill px-4"
                            data-bs-dismiss="modal">

                        Cancelar

                    </button>

                    <button type="submit"
                            class="btn btn-dark rounded-pill px-5">

                        Guardar

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

<script>

// =====================================================
// 🔥 VARIABLES
// =====================================================

let unidadBaseActual = 'Unidad';

// =====================================================
// 🔥 ABRIR MODAL
// =====================================================

window.prepararNuevaMedida = function (

    idProducto,
    idAlmacen,
    nombreProducto,
    unidadBase

){

    unidadBaseActual = unidadBase;

    document.getElementById(
        'id_producto_crear'
    ).value = idProducto;

    document.getElementById(
        'id_almacen_crear'
    ).value = idAlmacen;

    document.getElementById(
        'infoProductoModal'
    ).innerText =
        `Producto: ${nombreProducto}`;

    document.getElementById(
        'cantidadConversion'
    ).value = '';

    document.getElementById(
        'equivalencia'
    ).value = '';

    document.getElementById(
        'nombreNuevaUnidad'
    ).value = '';

    actualizarFormula();

    const modal =
        bootstrap.Modal.getOrCreateInstance(
            document.getElementById(
                'modalMedidaAdicional'
            )
        );

    modal.show();
};

// =====================================================
// 🔥 ACTUALIZAR FORMULA
// =====================================================

function actualizarFormula(){

    const tipo =
        document.querySelector(
            'input[name="tipoConversion"]:checked'
        ).value;

    const cantidad =
        parseFloat(
            document.getElementById(
                'cantidadConversion'
            ).value
        ) || 0;

    const nuevaUnidad =
        document.getElementById(
            'nombreNuevaUnidad'
        ).value || 'Nueva Unidad';

    const texto =
        document.getElementById(
            'textoFormula'
        );

    const equivalencia =
        document.getElementById(
            'equivalencia'
        );

    const ejemplo =
        document.getElementById(
            'ejemploConversion'
        );

    // =================================================
    // 🔥 MÁS GRANDE
    // 1000 KG = 1 TON
    // equivalencia = 0.001
    // =================================================

    if(tipo === 'grande'){

        texto.innerHTML = `
            ${cantidad || '?'} ${unidadBaseActual}
            caben en
            1 ${nuevaUnidad}
        `;

        if(cantidad > 0){

            equivalencia.value =
                (1 / cantidad).toFixed(8);

            ejemplo.innerHTML = `
                ${cantidad} ${unidadBaseActual}
                = 1 ${nuevaUnidad}
                <br>
                Entonces:
                1 ${unidadBaseActual}
                =
                ${(1 / cantidad).toFixed(8)}
                ${nuevaUnidad}
            `;
        }
    }

    // =================================================
    // 🔥 MÁS PEQUEÑA
    // 1 KG = 1000 GR
    // equivalencia = 1000
    // =================================================

    else{

        texto.innerHTML = `
            1 ${unidadBaseActual}
            contiene
            ${cantidad || '?'}
            ${nuevaUnidad}
        `;

        if(cantidad > 0){

            equivalencia.value =
                cantidad.toFixed(8);

            ejemplo.innerHTML = `
                1 ${unidadBaseActual}
                =
                ${cantidad}
                ${nuevaUnidad}
            `;
        }
    }
}

// =====================================================
// 🔥 EVENTOS
// =====================================================

document
.getElementById('cantidadConversion')
.addEventListener('input', actualizarFormula);

document
.getElementById('nombreNuevaUnidad')
.addEventListener('input', actualizarFormula);

document
.querySelectorAll(
    'input[name="tipoConversion"]'
)
.forEach(radio => {

    radio.addEventListener(
        'change',
        actualizarFormula
    );

});

// =====================================================
// 🔥 GUARDAR
// =====================================================

document
.getElementById('formMedidaAdicional')
.addEventListener('submit', async function(e){

    e.preventDefault();

    try{

        Swal.fire({
            title:'Guardando...',
            allowOutsideClick:false,
            didOpen:()=>Swal.showLoading()
        });

        const formData =
            new FormData(this);

        const resp = await fetch(

            '/cfsistem/app/controllers/productosController.php?action=guardarOpcionMedida',

            {
                method:'POST',
                body:formData
            }
        );

        const data = await resp.json();

        Swal.close();

        if(data.success || data.status === 'success'){

            await Swal.fire({

                icon:'success',
                title:'Guardado',
                text:'Medida agregada correctamente',
                timer:1500,
                showConfirmButton:false,
                 customClass: {
                         popup: 'miSwalZ'
                    }
            });

            bootstrap.Modal
            .getInstance(
                document.getElementById(
                    'modalMedidaAdicional'
                )
            )
            .hide();

            document
            .getElementById(
                'formMedidaAdicional'
            )
            .reset();

            if(typeof recargarModalMedidas === 'function'){
                recargarModalMedidas();
            }

        }else{

            Swal.fire({
                icon:'error',
                title:'Error',
                text:data.message ||
                     'No se pudo guardar',
                      customClass: {
                         popup: 'miSwalZ'
                    }
            });
        }

    }catch(error){

        console.error(error);

        Swal.fire({
            icon:'error',
            title:'Error',
            text:'Falló la comunicación con el servidor'
        });
    }
});

</script>