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
                                   step="0.0001"
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