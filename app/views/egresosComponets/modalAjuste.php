<div class="modal fade" id="modalAjusteFaltante" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-diagram-3-fill me-2"></i> 
                    Distribución de Faltantes: <span id="folioAjuste" class="badge bg-white text-danger ms-2"></span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            
            <form id="formAjusteFaltante">
                <div class="modal-body bg-light">
                    <input type="hidden" name="compra_id" id="ajuste_compra_id">
                    
                    <div class="alert alert-warning d-flex align-items-center mb-4 shadow-sm border-0">
                        <i class="bi bi-shield-check me-3 h4 mb-0 text-danger"></i>
                        <div class="small">
                            <strong>Control de Entradas:</strong> Primero <b>habilite</b> el almacén de destino con el interruptor y luego ingrese la cantidad recibida.
                        </div>
                    </div>

                    <div id="listaProductosFaltantes" class="row g-4">
                        </div>
                </div>

                <div class="modal-footer bg-white border-top">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-danger px-5 fw-bold shadow-sm" onclick="procesarAjuste()">
                        <i class="bi bi-check-all me-2"></i> REGISTRAR ENTRADA
                    </button>
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
                                       name="distribucion[${p.producto_id}][${alm.id}]" 
                                       class="form-control form-control-sm border-danger input-dist bg-light" 
                                       data-prod-id="${p.producto_id}"
                                       data-max="${p.cantidad_pendiente}"
                                       disabled 
                                       placeholder="0.00" 
                                       step="any" min="0">
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
                                    Pendiente: ${p.cantidad_pendiente}
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
        if(cant > 0) {
            hayDatos = true;
            const prodId = input.dataset.prodId;
            const max = parseFloat(input.dataset.max);
            
            sumasGlobales[prodId] = (sumasGlobales[prodId] || 0) + cant;
            
            if(sumasGlobales[prodId] > max) {
                erroresExceso.push(`Exceso en <b>${prodId}</b>: Ingresó ${sumasGlobales[prodId]} de ${max} pendientes.`);
            }
        }
    });

    if(!hayDatos) return Swal.fire('Sin datos', 'Habilite al menos un almacén e ingrese cantidad.', 'warning');
    if(erroresExceso.length > 0) return Swal.fire('Error de Cantidades', erroresExceso.join('<br>'), 'error');

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
                if(data.success) {
                    Swal.fire('¡Éxito!', data.message, 'success').then(() => location.reload());
                } else {
                    Swal.fire('Error', data.message, 'error');
                }
            });
        }
    });
}
</script>

<style>
.pointer { cursor: pointer; }
.form-switch .form-check-input:checked {
    background-color: #dc3545;
    border-color: #dc3545;
}
</style>