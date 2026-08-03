<!-- =========================================
MODAL LISTA DE MEDIDAS
========================================= -->
<div class="modal fade" id="modalListaMedidas" tabindex="-1" aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered modal-lg">

        <div class="modal-content border-0 shadow-lg overflow-hidden">

            <!-- HEADER: Gradiente adaptativo -->
            <div class="modal-header bg-primary bg-gradient text-white border-0 p-4 position-relative">

                <div class="pe-4">
                    <h5 class="modal-title fw-bold mb-1 d-flex align-items-center gap-2">
                        <i class="bi bi-rulers fs-4"></i>
                        Medidas Disponibles
                    </h5>

                    <small id="subtituloListaMedidas" class="text-white-50 fw-medium">
                        Cargando detalles...
                    </small>
                </div>

                <button type="button" 
                        class="btn-close btn-close-white position-absolute top-0 end-0 m-4" 
                        data-bs-dismiss="modal" 
                        aria-label="Close">
                </button>

            </div>
  
            <!-- BODY -->
            <div class="modal-body p-0 bg-body">

                <!-- Barra superior de acciones -->
                <div class="p-3 border-bottom bg-body-tertiary d-flex justify-content-between align-items-center">
                    <span class="text-body-secondary small fw-semibold text-uppercase tracking-wide">
                        Lista de Equivalencias
                    </span>
                    <button type="button" 
                            id="agregarMedida" 
                            class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm d-flex align-items-center gap-2">
                        <i class="bi bi-plus-circle-fill"></i>
                        <span>Agregar Medida</span>
                    </button>
                </div>

                <div class="table-responsive">

                    <table class="table table-hover align-middle mb-0">

                        <thead class="bg-body-tertiary border-bottom">

                            <tr>
                                <th class="ps-4 text-body-secondary small text-uppercase">Nombre Medida</th>
                                <th class="text-body-secondary small text-uppercase">Equivalencia</th>
                                <th class="text-end pe-4 text-body-secondary small text-uppercase">Acciones</th>
                            </tr>

                        </thead>

                        <tbody id="tablaCuerpoMedidas">

                            <!-- JS Populate -->

                        </tbody>

                    </table>

                </div>

                <!-- EMPTY STATE -->
                <div id="listaVacia" class="text-center py-5 d-none">

                    <i class="bi bi-inbox fs-1 text-body-tertiary d-block mb-2"></i>

                    <p class="text-body-secondary fw-medium mb-0">
                        No hay medidas adicionales configuradas para este producto.
                    </p>

                </div>

            </div>

            <!-- FOOTER -->
            <div class="modal-footer border-top bg-body-tertiary px-4 py-3">

                <button type="button" 
                        class="btn btn-outline-secondary rounded-pill px-4 fw-semibold" 
                        data-bs-dismiss="modal">
                    Cerrar
                </button>

            </div>

        </div>

    </div>

</div>


<!-- =========================================
MODAL EDITAR MEDIDA
========================================= -->
<div class="modal fade" id="modalEditarMedida" tabindex="-1" aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content border-0 shadow-lg overflow-hidden">

            <!-- HEADER -->
            <div class="modal-header bg-primary bg-gradient text-white border-0 p-4 position-relative">

                <div class="pe-4">
                    <h5 class="modal-title fw-bold mb-1 d-flex align-items-center gap-2">
                        <i class="bi bi-pencil-square fs-4"></i>
                        Editar Medida
                    </h5>

                    <small class="text-white-50 fw-medium">
                        Modifica el nombre y la equivalencia
                    </small>
                </div>

                <button type="button" 
                        class="btn-close btn-close-white position-absolute top-0 end-0 m-4" 
                        data-bs-dismiss="modal" 
                        aria-label="Close">
                </button>

            </div>

            <!-- FORM -->
            <form id="formEditarMedida">

                <input type="hidden" id="edit_medida_id" name="id">
                <input type="hidden" id="edit_producto_id" name="producto_id">

                <div class="modal-body p-4 bg-body">

                    <!-- NOMBRE -->
                    <div class="mb-4">
                        <label for="edit_nombre_medida" class="form-label fw-bold small text-uppercase text-body-secondary">
                            Nombre de la medida
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-body-tertiary border-end-0 text-body-secondary">
                                <i class="bi bi-tag-fill"></i>
                            </span>
                            <input type="text" 
                                   id="edit_nombre_medida" 
                                   class="form-control border-start-0 ps-0" 
                                   name="nombre_edit" 
                                   placeholder="Ej. Caja, Gramo" 
                                   required>
                        </div>
                    </div>

                    <!-- EQUIVALENCIA -->
                    <div class="mb-3">
                        <label for="edit_equivalencia" class="form-label fw-bold small text-uppercase text-body-secondary">
                            Equivalencia
                        </label>

                        <div class="input-group">
                            <input type="number" 
                                   class="form-control fw-bold fs-5 text-primary" 
                                   id="edit_equivalencia" 
                                   name="equivalencia" 
                                   step="0.000000001" 
                                   min="0.0001" 
                                   required>

                            <span class="input-group-text bg-body-tertiary fw-semibold" id="edit_unidad_text">
                                Unidades
                            </span>
                        </div>
                    </div>

                </div>

                <!-- FOOTER -->
                <div class="modal-footer border-0 bg-body px-4 pb-4 pt-0 gap-2">

                    <button type="button" 
                            class="btn btn-outline-secondary rounded-pill px-4 fw-semibold" 
                            data-bs-dismiss="modal">
                        Cancelar
                    </button>

                    <button type="submit" 
                            class="btn btn-primary rounded-pill px-5 fw-semibold shadow-sm">
                        <i class="bi bi-check-lg me-1"></i>
                        Guardar Cambios
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>


<!-- =========================================
ESTILOS ADICIONALES (CSS)
========================================= -->
<style>
/* Z-Index para modales anidados */
#modalEditarMedida {
    z-index: 1065 !important;
}

#modalEditarMedida + .modal-backdrop {
    z-index: 1060 !important;
}

.miSwalZ {
    z-index: 10000 !important;
}

.tracking-wide {
    letter-spacing: 0.05em;
}
</style>


<!-- =========================================
JAVASCRIPT
========================================= -->
<script>
const URL_MEDIDAS = '/cfsistem/app/controllers/productosController.php';

let ultimaMedidaProductoId = 0;
let ultimaMedidaAlmacenId = 0;
let ultimaMedidaNombreProducto = '';
let ultimaUnidadMedida = '';

// =========================================
// VER LISTA MEDIDAS
// =========================================
async function verListaMedidas(idProducto, idAlmacen, nombreProducto, unidad_medida) {
    ultimaMedidaProductoId = idProducto;
    ultimaMedidaAlmacenId = idAlmacen;
    ultimaMedidaNombreProducto = nombreProducto;
    ultimaUnidadMedida = unidad_medida;

    const tbody = document.getElementById('tablaCuerpoMedidas');
    const subtitulo = document.getElementById('subtituloListaMedidas');
    const emptyState = document.getElementById('listaVacia');

    subtitulo.innerText = `Producto: ${nombreProducto}`;
    emptyState.classList.add('d-none');

    tbody.innerHTML = `
        <tr>
            <td colspan="3" class="text-center py-4">
                <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                <span class="ms-2 text-body-secondary fw-medium">Cargando medidas...</span>
            </td>
        </tr>
    `;

    const modalEl = document.getElementById('modalListaMedidas');
    let myModal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
    myModal.show();

    try {
        const resp = await fetch(`${URL_MEDIDAS}?action=obtnerMedidas&id=${idProducto}`);
        
        if (!resp.ok) throw new Error('Error en la red');

        const data = await resp.json();
        tbody.innerHTML = '';

        if (data.status && data.producto.medidas && data.producto.medidas.length > 0) {
            
            $('#agregarMedida').attr(
                'onclick',
                `prepararNuevaMedida(${idProducto}, ${idAlmacen}, '${nombreProducto}', '${unidad_medida}')`
            );

           data.producto.medidas.forEach(m => {
    const medidaData = encodeURIComponent(JSON.stringify(m));

    // Convertir a número por seguridad
    const equiv = parseFloat(m.equivalencia) || 0;
    
    // Cálculo de la relación inversa (1 / equivalencia)
    const inversa = equiv > 0 ? (1 / equiv) : 0;

    // Formateo inteligente para limpiar ceros innecesarios a la derecha
    const equivFormateada = Number(equiv.toFixed(6));
    const inversaFormateada = Number(inversa.toFixed(6));

    // Determinar la frase según el tipo de equivalencia para máxima claridad
    let textoRelacion = '';
    
    if (equiv >= 1) {
        // Caso: Unidad menor (Ej: 1 KG = 1000 Gramos)
        textoRelacion = `
            <span class="badge bg-body-tertiary text-body border border-translucent rounded-pill px-3 py-2 fw-normal d-inline-flex align-items-center gap-2 shadow-sm">
                <span><strong class="text-primary">${equivFormateada}</strong> ${m.nombre}(s)= <strong>1</strong> ${unidad_medida} </span>
            </span>
        `;
    } else {
        // Caso: Unidad mayor (Ej: 1 Tonelada = 0.001 -> 1000 KG = 1 Tonelada)
        textoRelacion = `
            <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-2 fw-normal d-inline-flex align-items-center gap-2 shadow-sm">
                <i class="bi bi-arrow-left-right opacity-75"></i>
                <span> <strong>1</strong> ${m.nombre} = <strong>${inversaFormateada}</strong> ${unidad_medida}(s) </span>
            </span>
        `;
    }

    const fila = `
        <tr class="align-middle">
            <!-- NOMBRE DE LA MEDIDA -->
            <td class="ps-4 py-3">
                <div class="d-flex align-items-center gap-2">
                    <div class="p-2 bg-body-tertiary rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                        <i class="bi bi-rulers text-primary fs-6"></i>
                    </div>
                    <div>
                        <div class="fw-bold text-body mb-0">${m.nombre}</div>
                        <small class="text-body-secondary fs-7">
                            Valor base: ${equivFormateada}
                        </small>
                    </div>
                </div>
            </td>

            <!-- RELACIÓN / EQUIVALENCIA ELEGANTE -->
            <td>
                ${textoRelacion}
            </td>

            <!-- ACCIONES -->
            <td class="text-end pe-4">
                <div class="d-inline-flex gap-1">
                    <button class="btn btn-sm btn-light-hover text-primary rounded-circle border-0 p-2 d-inline-flex align-items-center justify-content-center"
                            title="Editar medida"
                            style="width: 34px; height: 34px;"
                            onclick="abrirEditarMedida('${medidaData}')">
                        <i class="bi bi-pencil-fill fs-6"></i>
                    </button>
                    <button class="btn btn-sm btn-light-hover text-danger rounded-circle border-0 p-2 d-inline-flex align-items-center justify-content-center"
                            title="Eliminar medida"
                            style="width: 34px; height: 34px;"
                            onclick="eliminarMedida(${m.id})">
                        <i class="bi bi-trash-fill fs-6"></i>
                    </button>
                </div>
            </td>
        </tr>
    `;

    tbody.insertAdjacentHTML('beforeend', fila);
});
        } else {
            emptyState.classList.remove('d-none');
            $('#agregarMedida').attr(
                'onclick',
                `prepararNuevaMedida(${idProducto}, ${idAlmacen}, '${nombreProducto}', '${unidad_medida}')`
            );
        }

    } catch (error) {
        console.error("Error:", error);
        tbody.innerHTML = `
            <tr>
                <td colspan="3" class="text-center py-4 text-danger">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
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
    const medida = JSON.parse(decodeURIComponent(data));

    const inputId = document.getElementById('edit_medida_id');
    const inputProdId = document.getElementById('edit_producto_id');
    const inputNombre = document.getElementById('edit_nombre_medida');
    const inputEquiv = document.getElementById('edit_equivalencia');
    const textUnidad = document.getElementById('edit_unidad_text');

    if (inputId) inputId.value = medida.id;
    if (inputProdId) inputProdId.value = medida.producto_id;
    if (inputEquiv) inputEquiv.value = medida.equivalencia;
    if (textUnidad) textUnidad.innerText = medida.nombre;
    if (inputNombre) inputNombre.value = medida.nombre;

    const modalEl = document.getElementById('modalEditarMedida');
    const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
    modal.show();
}

// =========================================
// GUARDAR CAMBIOS
// =========================================
document.getElementById('formEditarMedida').addEventListener('submit', async function(e) {
    e.preventDefault();

    try {
        Swal.fire({
            title: 'Actualizando...',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading(),
            customClass: { popup: 'miSwalZ' }
        });

        const formData = new FormData(this);

        const resp = await fetch(`${URL_MEDIDAS}?action=actualizarMedidaAdicional`, {
            method: 'POST',
            body: formData
        });

        const data = await resp.json();
        Swal.close();

        if (data.status || data.success) {
            await Swal.fire({
                icon: 'success',
                title: 'Actualizado',
                text: 'La medida fue actualizada correctamente',
                timer: 1500,
                showConfirmButton: false,
                customClass: { popup: 'miSwalZ' }
            });

            const modalEditar = bootstrap.Modal.getInstance(document.getElementById('modalEditarMedida'));
            if (modalEditar) modalEditar.hide();

            recargarModalMedidas();

        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: data.message || 'No se pudo actualizar',
                customClass: { popup: 'miSwalZ' }
            });
        }

    } catch (error) {
        console.error(error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Falló la comunicación con el servidor',
            customClass: { popup: 'miSwalZ' }
        });
    }
});

// =========================================
// ELIMINAR MEDIDA
// =========================================
async function eliminarMedida(id) {
    const swalConfig = {
        title: '¿Estás seguro?',
        text: "Esta acción no se puede deshacer",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#0d6efd',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar',
        customClass: { container: 'miSwalZ' }
    };

    const confirmacion = await Swal.fire(swalConfig);

    if (confirmacion.isConfirmed) {
        try {
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