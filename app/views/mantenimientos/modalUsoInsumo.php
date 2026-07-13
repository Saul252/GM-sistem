<div class="modal fade" id="modalAsignarInsumoMantenimiento" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 24px; background: #ffffff;">
            <form id="formAsignarInsumoMantenimiento" enctype="multipart/form-data">
                
                <input type="hidden" name="action" value="asignarInsumos">
                <input type="hidden" id="msign_almacen_id" name="almacen_id" value="1"> 

                <div class="modal-header border-0 pt-4 px-4 pb-2">
                    <h5 class="modal-title fw-bold text-dark d-flex align-items-center" style="letter-spacing: -0.5px;">
                        <i class="bi bi-tools text-primary me-2 fs-4"></i> Asignar Insumos a Mantenimiento
                    </h5>
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body px-4 py-3">
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Seleccionar Mantenimiento</label>
                            <select id="msign_mantenimiento_id" name="mantenimiento_id" class="form-select border-0 bg-light" style="border-radius: 12px; height: 42px;" required>
                                <option value="">Cargando mantenimientos...</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Vehículo / Carro</label>
                            <select id="msign_carro_id" name="carro_id" class="form-select border-0 bg-light" style="border-radius: 12px; height: 42px;" required>
                                <option value="">Seleccione uno...</option>  
                                <?php foreach($vehiculos as $ve): ?>
                                    <option value="<?= $ve['id'] ?>"><?= $ve['nombre'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Subir Evidencia (Documento/Foto)</label>
                            <input type="file" id="msign_documento" name="documento" class="form-control border-0 bg-light" style="border-radius: 12px; height: 42px;" accept=".jpg,.png,.pdf">
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-3 pt-2">
                        <h6 class="fw-bold mb-0 text-dark" style="letter-spacing: -0.3px;">Insumos a Utilizar</h6>
                        <button type="button" class="btn btn-sm text-primary fw-bold bg-transparent border-0 d-flex align-items-center" onclick="msign_agregarFilaInsumo()">
                            <i class="bi bi-plus-circle-fill me-1 fs-6"></i> Agregar Insumo
                        </button>
                    </div>

                    <div class="table-responsive mb-4" style="border-radius: 16px; background: #fafafa; padding: 10px;">
                        <table class="table table-borderless align-middle mb-0" id="msign_tablaInsumosAsignados">
                            <thead>
                                <tr class="text-secondary small fw-bold" style="font-size: 0.75rem;">
                                    <th>INSUMO DISPONIBLE</th>
                                    <th width="150" class="text-center">CANTIDAD DISPONIBLE</th>
                                    <th width="150" class="text-center">CANTIDAD A RETIRAR</th>
                                    <th width="40"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="bg-white">
                                    <td>
                                        <select name="msign_items[]" class="form-select form-select-sm border-0 msign_items bg-light msign_select_item_dinamico" style="border-radius: 8px; height: 36px;" onchange="msign_manejarCambioInsumo(this)" required>
                                            <option value="">Seleccione insumo...</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="number" name="msign_cantdisponible[]" class="form-control form-control-sm border-0 bg-light text-center" style="border-radius: 8px; height: 36px;" readonly>
                                    </td>
                                    <td>
                                        <input type="number" name="msign_cant[]" class="form-control form-control-sm msign_cant border-0 bg-light text-center" style="border-radius: 8px; height: 36px;" value="1" min="0.01" step="any" required>
                                    </td>
                                    <td class="text-center"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label small fw-semibold text-secondary">Notas u Observaciones del Ajuste</label>
                            <textarea id="msign_observaciones" name="observaciones" class="form-control text-uppercase border-0 bg-light" style="border-radius: 12px;" rows="2" placeholder="Detalles de la asignación..."></textarea>
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-0 px-4 pb-4 pt-2">
                    <button type="button" class="btn btn-light fw-semibold text-secondary border-0 me-2" data-bs-dismiss="modal" style="border-radius: 12px; padding: 10px 20px; background: #f0f0f2;">Cancelar</button>
                    <button type="submit" id="msign_btnGuardar" class="btn btn-primary fw-bold px-4 shadow-sm" style="border-radius: 12px; padding: 10px 24px;">Asignar Recursos</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Cache local para optimizar y no golpear constantemente la DB al agregar filas
let msign_listaInsumosCache = [];

document.addEventListener('DOMContentLoaded', function() {
    const modalAsignar = document.getElementById('modalAsignarInsumoMantenimiento');
    const formAsignar = document.getElementById('formAsignarInsumoMantenimiento');

    if (!modalAsignar || !formAsignar) return;

    // Se unificó el disparador en un solo evento consistente
    modalAsignar.addEventListener('show.bs.modal', async function() {
        formAsignar.reset();
        msign_limpiarTabla();
        
        // Ejecución en paralelo de la carga de catálogos iniciales
        await Promise.all([
            msign_cargarMantenimientos(),
            msign_cargarInsumosBase()
        ]);
    });

    formAsignar.addEventListener('submit', function(e) {
        e.preventDefault();
        msign_guardarAsignacion();
    });
});

// ==================== PETICIONES ASÍNCRONAS ====================

async function msign_cargarInsumosBase() {
    try {
        const resp = await fetch('/cfsistem/app/controllers/egresosController.php?action=obtenerInsumosSelect');
        const resultado = await resp.json();
        
        if (resultado && Array.isArray(resultado.data)) {
            msign_listaInsumosCache = resultado.data; // Guardamos en caché global
            
            // Llenamos el primer select de la tabla estática
            const primerSelect = document.querySelector('.msign_select_item_dinamico');
            if (primerSelect) {
                msign_inyectarOpciones(primerSelect);
            }
        }
    } catch (e) {
        console.error("Error al cargar el catálogo de insumos inicial:", e);
    }
}

function msign_inyectarOpciones(selectElement) {
    selectElement.innerHTML = '<option value="">Seleccione insumo...</option>';
    msign_listaInsumosCache.forEach(insumo => {
        const opcion = document.createElement('option');
        opcion.value = insumo.id;
        opcion.setAttribute('data-total', insumo.total_existencias || 0);
        opcion.textContent = `${insumo.nombre} (${insumo.total_existencias} en existencia)`;
        selectElement.appendChild(opcion);
    });
}

async function msign_cargarMantenimientos() {
    try {
        $('#loader').removeClass('d-none');
        const params = new URLSearchParams({
            action: 'listar',
            f_search: $('#f_search').val() || '',
            f_rango: $('#f_rango').val() || '',
            f_inicio: $('#f_ini').val() || '',
            f_fin: $('#f_fin').val() || '',
            f_almacen: $('#f_almacen').val() || '',
            f_vehiculo: $('#select-vehiculos').val() || ''
        });

        const res = await fetch(`/cfsistem/app/controllers/mantenimientosController.php?${params.toString()}`);
        const data = await res.json();
         
        let html = '<option value="">Seleccione mantenimiento...</option>';
        data.forEach(m => {
            html += `<option value="${m.id_mantenimiento}">FOLIO: ${m.id_mantenimiento} - ${m.razon || m.tipo}</option>`;
        });
       
        document.getElementById('msign_mantenimiento_id').innerHTML = html;
    } catch (e) { 
        console.error("Error al cargar mantenimientos:", e); 
    } finally {
        $('#loader').addClass('d-none');
    }
}

// ==================== GESTIÓN DE FILAS DINÁMICAS ====================

function msign_manejarCambioInsumo(selectElement) {
    const idInsumoSeleccionado = selectElement.value;
    const $filaActual = $(selectElement).closest('tr');
    const $inputCantidad = $filaActual.find('input[name="msign_cantdisponible[]"]');
    
    if (idInsumoSeleccionado === "") {
        $inputCantidad.val('');
        return;
    }

    const opcionSeleccionada = selectElement.options[selectElement.selectedIndex];
    const total = opcionSeleccionada.getAttribute('data-total') || 0;
    $inputCantidad.val(total);
}

function msign_agregarFilaInsumo() {
    const tbody = document.querySelector('#msign_tablaInsumosAsignados tbody');
    const fila = document.createElement('tr');
    fila.className = "border-bottom bg-white";
    
    fila.innerHTML = `
        <td>
            <select name="msign_items[]" class="form-select form-select-sm border-0 bg-light msign_select_item_dinamico msign_items" style="border-radius: 8px; height: 36px;" onchange="msign_manejarCambioInsumo(this)" required>
                </select>
        </td>
        <td>
            <input type="number" name="msign_cantdisponible[]" class="form-control form-control-sm border-0 bg-light text-center" style="border-radius: 8px; height: 36px;" readonly>
        </td>
        <td>
            <input type="number" name="msign_cant[]" class="form-control msign_cant form-control-sm border-0 bg-light text-center" style="border-radius: 8px; height: 36px;" value="1" min="0.01" step="any" required>
        </td>
        <td class="text-center">
            <button type="button" class="btn btn-sm text-danger border-0 bg-transparent" onclick="this.closest('tr').remove();">
                <i class="bi bi-trash"></i>
            </button>
        </td>`;
        
    tbody.appendChild(fila);
    // Inyectamos de golpe usando la variable local optimizada sin volver a hacer un fetch por cada click
    msign_inyectarOpciones(fila.querySelector('.msign_select_item_dinamico'));
}

function msign_limpiarTabla() {
    // Elimina todas las filas excepto la primera
    document.querySelectorAll('#msign_tablaInsumosAsignados tbody tr:not(:first-child)').forEach(f => f.remove());
    const primeraFilaSelect = document.querySelector('.msign_select_item_dinamico');
    if (primeraFilaSelect) {
        primeraFilaSelect.innerHTML = '<option value="">Seleccione insumo...</option>';
    }
}

// ==================== ENVÍO AL CONTROLADOR (POST) ====================

function msign_guardarAsignacion() {
    const form = document.getElementById('formAsignarInsumoMantenimiento');
    if (!form) return;
    
    const formData = new FormData(form);
    const btn = document.getElementById('msign_btnGuardar');
    if (!btn) return;
    
    const textoOriginal = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Procesando...';

    // Apuntamos al endpoint correcto del controlador de mantenimiento
    fetch('/cfsistem/app/controllers/mantenimientosController.php?action=insumok', {
        method: 'POST',
        body: formData
    })
    .then(async res => {
    const text = await res.text();
    alert(text);
})
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: '¡Asignado!',
                text: data.message,
                timer: 1500,
                showConfirmButton: false
            }).then(() => {
                const modalEl = document.getElementById('modalAsignarInsumoMantenimiento');
                if (modalEl) {
                    const inst = bootstrap.Modal.getInstance(modalEl);
                    if (inst) inst.hide();
                }
                location.reload(); 
            });
        } else {
            throw new Error(data.message || 'Error procesando la asignación');
        }
    })
    .catch(err => {
        console.error('❌ Error en asignación:', err);
        Swal.fire({ 
            icon: 'error', 
            title: 'Oops!', 
            text: err.message, 
            confirmButtonColor: '#0d6efd'
        });
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = textoOriginal;
    });
}
</script>