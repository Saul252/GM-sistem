<div class="modal fade" id="modalAjusteFaltante" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">

            <!-- HEADER -->
            <div class="modal-header bg-danger text-white px-4 py-3 border-0">
                <h5 class="modal-title fw-semibold d-flex align-items-center gap-2">
                    <i class="bi bi-diagram-3-fill fs-5"></i>
                    <span>Distribución de Faltantes</span>
                    <span id="folioAjuste" class="badge bg-white text-danger fw-bold ms-2 px-3 py-1"></span>
                </h5>
                <button type="button" class="btn-close btn-close-white opacity-75" data-bs-dismiss="modal"></button>
            </div>

            <form id="formAjusteFaltante">
                <div class="modal-body bg-light px-4 py-4">

                    <input type="hidden" name="compra_id" id="ajuste_compra_id">

                    <!-- ALERTA PREMIUM -->
                    <div
                        class="d-flex align-items-start gap-3 p-3 mb-4 rounded-4 bg-white shadow-sm border-start border-4 border-danger">
                        <div>
                            <i class="bi bi-exclamation-triangle-fill text-danger fs-4"></i>
                        </div>
                        <div class="small text-muted">
                            <div class="fw-semibold text-dark mb-1">Control de Entradas</div>
                            Habilite el almacén de destino y después capture la cantidad recibida para evitar errores en
                            inventario.
                        </div>
                    </div>

                    <!-- CONTENEDOR -->
                    <div id="listaProductosFaltantes" class="row g-4">
                        <!-- contenido dinámico -->
                    </div>

                </div>

                <!-- FOOTER -->
                <div class="modal-footer bg-white px-4 py-3 border-0 d-flex justify-content-between">

                    <button type="button" class="btn btn-light rounded-pill px-4 shadow-sm" data-bs-dismiss="modal">
                        Cancelar
                    </button>

                    <div class="d-flex gap-2">

                        <button type="button" class="btn btn-outline-danger rounded-pill px-4 fw-semibold shadow-sm"
                            onclick="aplicarFaltantesCompra()">
                            <i class="bi bi-arrow-repeat me-1"></i> Ajustar compra
                        </button>

                        <button type="button" class="btn btn-danger rounded-pill px-4 fw-bold shadow"
                         onclick="procesarAjuste()">
                            <i class="bi bi-check-circle-fill me-1"></i> Registrar entrada
                            
                        </button>

                    </div>
                </div>

            </form>
        </div>
    </div>
</div>

<script>
// Función para habilitar/deshabilitar inputs de cantidad
function toggleAlmacen(check, prodId, almId) {
    const input = document.querySelector(`input[name="distribucion[${prodId}][${almId}]"]`);
    if (check.checked) {
        input.disabled = false;
        input.classList.remove('bg-light');
        input.focus();
    } else {
        input.disabled = true;
        input.value = ''; // Limpiamos el valor si se deshabilita
        input.classList.add('bg-light');
    }
}

function aplicarFaltantesCompra() {
    const compra_id = document.getElementById('ajuste_compra_id').value;
    console.log(compra_id);

    Swal.fire({
        title: '¿Aplicar faltantes?',
        text: 'Se descontarán del total y los faltantes se pondrán en 0.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, aplicar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33'
    }).then((result) => {

        if (result.isConfirmed) {

            // 🔄 Loader
            Swal.fire({
                title: 'Procesando...',
                text: 'Aplicando cambios',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            fetch(
                    `/cfsistem/app/controllers/egresosController.php?action=aplicarFaltantesCompras&compra_id=${compra_id}`)
                .then(res => res.json())
                .then(data => {

                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Actualizado',
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            location.reload();
                        });

                        // 🔥 Opcional: recargar tabla o vista
                        // cargarCompras();

                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.message || 'No se pudo aplicar'
                        });
                    }

                })
                .catch(err => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error de conexión',
                        text: 'No se pudo conectar con el servidor'
                    });
                    console.error(err);
                });

        }

    });
}

function abrirModalAjuste(id, folio) {
    
    const modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('modalAjusteFaltante'));
    document.getElementById('folioAjuste').innerText = folio;
    document.getElementById('ajuste_compra_id').value = id;

    const contenedor = document.getElementById('listaProductosFaltantes');
    contenedor.innerHTML = '<div class="col-12 text-center py-5"><div class="spinner-border text-danger"></div></div>';

    modal.show();

    fetch(`/cfsistem/app/controllers/egresosController.php?action=obtenerFaltantes&compra_id=${id}`)
        .then(res => res.json())
        .then(data => {
            contenedor.innerHTML = '';

            data.forEach(p => {
                let tablaAlmacenes = `
                    <table class="table table-sm align-middle mb-0">
                        <thead class="bg-light text-muted" style="font-size: 0.75rem;">
                            <tr>
                                <th width="50">Envío</th>
                                <th>Almacén Destino</th>
                                <th width="140">Cantidad</th>
                            </tr>
                        </thead>
                        <tbody>`;

                window.DATA_COMPRAS.almacenes.forEach(alm => {
                    tablaAlmacenes += `
                        <tr>
                            <td class="text-center">
                                <div class="form-check form-switch d-inline-block">
                                    <input class="form-check-input pointer" type="checkbox" 
                                           onchange="toggleAlmacen(this, ${p.producto_id}, ${alm.id})">
                                </div>
                            </td>
                            <td class="small fw-semibold text-secondary">${alm.nombre}</td>
                            
                           <td>
    <input type="number" 
       name="distribucion[${p.producto_id}][${alm.id}]1" 
       class="form-control form-control-sm border-danger input-dist1 bg-light" 
       data-prod-id="${p.producto_id}"
       data-max="${p.cantidad_pendiente}"
       disabled 
       placeholder="0.00" 
       step=".01" 
       min="0"
       oninput="recalcularRestante(${p.producto_id},${p.factor_conversion})">
 <input type="hidden" 
       name="distribucion[${p.producto_id}][${alm.id}]" 
       class="form-control form-control-sm border-danger input-dist bg-light" 
       data-prod-id="${p.producto_id}"
       data-max="${p.cantidad_pendiente}"
       disabled 
       placeholder="0.00" 
       step=".01" 
       min="0"
       oninput="recalcularRestante(${p.producto_id},${p.factor_conversion})">

    <div class="small text-danger fw-bold mt-2 restante-prod"
         data-restante="${p.producto_id}">
        Restante por asignar: ${(p.cantidad_pendiente)/p.factor_conversion}
    </div>
</td>
                        </tr>`;
                });

                tablaAlmacenes += `</tbody></table>`;

                contenedor.innerHTML += `
                    <div class="col-lg-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-white border-bottom-0 py-3 d-flex justify-content-between align-items-center">
                                <h6 class="fw-bold mb-0 text-dark">${p.nombre}</h6>
                                <span class="badge rounded-pill bg-danger-subtle text-danger border border-danger-subtle">
                                    Pendiente: ${(p.cantidad_pendiente)/(p.factor_conversion)} ${p.unidad_reporte}
                                </span>
                            </div>
                            <div class="card-body pt-0">
                                <div class="border rounded-3 overflow-hidden">
                                    ${tablaAlmacenes}
                                </div>
                            </div>
                        </div>
                    </div>`;
            });
        });
}

function procesarAjuste() {
    const form = document.getElementById('formAjusteFaltante');
    const formData = new FormData(form);

    let hayDatos = false;
    let erroresExceso = [];
    const sumasGlobales = {};

    // Recorremos solo los inputs que NO están deshabilitados (los habilitados por el switch)
    form.querySelectorAll('.input-dist:not(:disabled)').forEach(input => {
        const cant = parseFloat(input.value) || 0;
        if (cant > 0) {
            hayDatos = true;
            const prodId = input.dataset.prodId;
            const max = parseFloat(input.dataset.max);

            sumasGlobales[prodId] = (sumasGlobales[prodId] || 0) + cant;
           

            if (sumasGlobales[prodId] > max) {
                erroresExceso.push(
                    `Exceso en <b>${prodId}</b>: Ingresó ${sumasGlobales[prodId]} de ${max} pendientes.`);
            }
        }
    });

    if (!hayDatos) return Swal.fire('Sin datos', 'Habilite al menos un almacén e ingrese cantidad.', 'warning');
    if (erroresExceso.length > 0) return Swal.fire('Error de Cantidades', erroresExceso.join('<br>'), 'error');

    Swal.fire({
        title: '¿Confirmar Ingreso?',
        text: "Se afectará el stock de los almacenes habilitados.",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        confirmButtonText: 'Sí, registrar'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('/cfsistem/app/controllers/egresosController.php?action=procesarAjusteFaltante', {
                    method: 'POST',
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire('¡Éxito!', data.message, 'success').then(() => location.reload());
                    } else {
                        Swal.fire('Error', data.message, 'error');
                    }
                });
        }
    });
}
</script>
<script>

function toggleAlmacen(check, prodId, almId) {

    const inputVisible = document.querySelector(
        `input[name="distribucion[${prodId}][${almId}]1"]`
    );

    const inputHidden = document.querySelector(
        `input[name="distribucion[${prodId}][${almId}]"]`
    );

    if (check.checked) {

        inputVisible.disabled = false;
        inputHidden.disabled = false;

        inputVisible.classList.remove('bg-light');

        inputVisible.focus();

    } else {

        inputVisible.disabled = true;
        inputHidden.disabled = true;

        inputVisible.value = '';
        inputHidden.value = '';

        inputVisible.classList.add('bg-light');

        recalcularRestante(prodId);
    }
}

function recalcularRestante(prodId, factor = 1) {

    // INPUTS VISIBLES
    const inputs = document.querySelectorAll(
        `input[name^="distribucion[${prodId}]"][name$="]1"]`
    );

    let suma = 0;
    let maximo = 0;

    inputs.forEach(input => {

        if (!input.disabled) {

            const valor = parseFloat(input.value) || 0;

            // SUMA EN PIEZAS
            suma += valor * factor;
            
            maximo = parseFloat(input.dataset.max) || 0;
            

            // ACTUALIZA EL HIDDEN
            const hiddenName =
                input.name.replace(']1', ']');

            const hidden =
                document.querySelector(
                    `input[name="${hiddenName}"]`
                );

            if (hidden) {

                hidden.value =
                    (valor * factor).toFixed(2);
            }
        }
    });

    suma =
        Math.round(suma * 1000) / 1000;

    const restante =
        (Math.round((maximo - suma) * 1000) / 1000)/factor;

    // TEXTO RESTANTE
    const textos = document.querySelectorAll(
        `.restante-prod[data-restante="${prodId}"]`
    );

    textos.forEach(texto => {

        texto.innerHTML =
            `Restante por asignar: <b>${restante}</b>`;
    });

    // VALIDAR EXCESO
    if (suma > maximo) {

        Swal.fire({
            icon: 'warning',
            title: 'Cantidad excedida',
            text: `No puedes asignar más de ${maximo}`
        });

        const ultimoInput = document.activeElement;

        if (ultimoInput) {

            const actual =
                parseFloat(ultimoInput.value) || 0;

            const exceso =
                (suma - maximo) / factor;

            ultimoInput.value =
                Math.max(0, actual - exceso).toFixed(2);
        }

        recalcularRestante(prodId, factor);
    }
}

</script>
<style>
.pointer {
    cursor: pointer;
}

.form-switch .form-check-input:checked {
    background-color: #dc3545;
    border-color: #dc3545;
}
</style>