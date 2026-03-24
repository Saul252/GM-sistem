<div class="modal fade" id="modalGasto" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 22px;">
            <form id="formNuevoGasto" enctype="multipart/form-data">
                <div class="modal-header border-0 bg-warning text-dark" style="border-radius: 22px 22px 0 0;">
                    <h5 class="modal-title fw-bold"><i class="bi bi-cash-stack me-2"></i> Registrar Nuevo Gasto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Folio/Factura</label>
                            <input type="text" id="folio_gasto" name="folio" class="form-control border-0 bg-light" style="border-radius: 12px;" placeholder="Cargando..." readonly required>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label small fw-bold">Almacén Destino</label>
                            <select name="almacen_id" class="form-select border-0 bg-light" style="border-radius: 12px;" <?= ($_SESSION['rol_id'] != 1) ? 'readonly style="pointer-events: none;"' : '' ?> required>
                                <?php foreach($almacenes as $alm): ?>
                                <option value="<?= $alm['id'] ?>" <?= ($_SESSION['almacen_id'] == $alm['id']) ? 'selected' : '' ?>>
                                    <?= $alm['nombre'] ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-primary">Categoría de Gasto</label>
                            <div class="input-group">
                                <select id="select_categoria_gasto" name="categoria_id" class="form-select border-0 bg-light" style="border-radius: 12px 0 0 12px;" required>
                                    <option value="">Seleccione categoría...</option>
                                    </select>
                                <button type="button" class="btn btn-primary" style="border-radius: 0 12px 12px 0;" onclick="abrirModalNuevaCategoria()">
                                    <i class="bi bi-plus-lg"></i>
                                </button>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Beneficiario (Quién recibe)</label>
                            <input type="text" name="beneficiario" class="form-control border-0 bg-light" style="border-radius: 12px;" placeholder="Ej: CFE, Gasolinera..." required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Método de Pago</label>
                            <select name="metodo_pago" class="form-select border-0 bg-light" style="border-radius: 12px;">
                                <option value="Efectivo">Efectivo</option>
                                <option value="Transferencia">Transferencia</option>
                                <option value="Tarjeta">Tarjeta</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Comprobante (Evidencia)</label>
                            <input type="file" name="documento" class="form-control border-0 bg-light" style="border-radius: 12px;" accept=".jpg,.png,.pdf">
                        </div>
                    </div>

                    <hr class="text-muted">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="fw-bold mb-0 text-dark">Conceptos del Gasto</h6>
                        <button type="button" class="btn btn-sm btn-outline-dark border-0 fw-bold" onclick="agregarFilaGasto()">
                            <i class="bi bi-plus-circle-fill"></i> Agregar Concepto
                        </button>
                    </div>

                    <div class="table-responsive mb-3">
                        <table class="table table-borderless align-middle" id="tablaConceptosGasto">
                            <thead class="bg-light">
                                <tr class="small text-uppercase">
                                    <th>Descripción</th>
                                    <th width="100">Cant.</th>
                                    <th width="130">Precio</th>
                                    <th width="120" class="text-end pe-3">Subtotal</th>
                                    <th width="40"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="border-bottom">
                                    <td><input type="text" name="desc[]" class="form-control form-control-sm border-0 bg-light" style="border-radius: 8px;" required></td>
                                    <td><input type="number" name="cant[]" class="form-control form-control-sm border-0 bg-light cant text-center" style="border-radius: 8px;" value="1" step="any" oninput="calcularGasto()"></td>
                                    <td><input type="number" name="precio[]" class="form-control form-control-sm border-0 bg-light precio" style="border-radius: 8px;" value="0.00" step="any" oninput="calcularGasto()"></td>
                                    <td class="text-end fw-bold subtotal_fila pe-3">$0.00</td>
                                    <td></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="row align-items-center">
                        <div class="col-md-7">
                            <label class="form-label small fw-bold">Observaciones</label>
                            <textarea name="observaciones" class="form-control border-0 bg-light" style="border-radius: 12px;" rows="2" placeholder="Notas internas..."></textarea>
                        </div>
                        <div class="col-md-5 text-end">
                            <h4 class="text-muted small fw-bold mb-0">TOTAL</h4>
                            <h2 class="fw-bold text-dark" id="txtTotalGasto">$ 0.00</h2>
                            <input type="hidden" name="total_final" id="inputTotalGasto" value="0">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light fw-bold" data-bs-dismiss="modal" style="border-radius: 12px;">Cancelar</button>
                    <button type="submit" class="btn btn-warning fw-bold px-4 shadow-sm" style="border-radius: 12px;">Guardar Gasto</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalNuevaCategoriaGasto" tabindex="-1" aria-hidden="true" style="background: rgba(0,0,0,0.4);">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header border-0">
                <h6 class="modal-title fw-bold">Nueva Categoría</h6>
                <button type="button" class="btn-close" onclick="$('#modalNuevaCategoriaGasto').modal('hide')"></button>
            </div>
            <div class="modal-body pt-0">
                <div class="mb-3">
                    <label class="small fw-bold text-muted">Nombre</label>
                    <input type="text" id="nuevo_nombre_cat" class="form-control border-0 bg-light" style="border-radius: 10px;" placeholder="Ej: Servicios">
                </div>
                <div class="mb-1">
                    <label class="small fw-bold text-muted">Descripción</label>
                    <input type="text" id="nuevo_desc_cat" class="form-control border-0 bg-light" style="border-radius: 10px;">
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-primary w-100 fw-bold" onclick="guardarNuevaCategoria()" style="border-radius: 10px;">Agregar</button>
            </div>
        </div>
    </div>
</div>
<script>
// VARIABLES GLOBALES (FUERA del DOMContentLoaded)
const modalGastoEl = document.getElementById('modalGasto');
const formGasto = document.getElementById('formNuevoGasto');

document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Sistema Gastos INICIADO');
    
    if (!modalGastoEl || !formGasto) {
        console.error('❌ Modal o Form no encontrados');
        return;
    }

    // 1. CARGAR CATEGORÍAS
    cargarCategorias();

    // 2. AL ABRIR MODAL
    modalGastoEl.addEventListener('show.bs.modal', function() {
        console.log('🟢 Modal ABIERTO');
        formGasto.reset();
        limpiarTabla();
        cargarFolio();
        calcularGasto();
    });

    // 3. SUBMIT FORM
    formGasto.addEventListener('submit', function(e) {
        e.preventDefault();
        console.log('📤 GUARDANDO...');
        guardarGasto();
    });

    // 4. CALCULAR TOTAL
    document.addEventListener('input', function(e) {
        if (e.target.matches('.cant, .precio')) calcularGasto();
    });
});

// ==================== FUNCIONES (GLOBALES) ====================
function cargarCategorias() {
    console.log('📂 Categorías...');
    fetch('/cfsistem/app/controllers/egresosController.php?action=get_categorias_egresos')
        .then(res => {
            if (!res.ok) throw new Error('HTTP: ' + res.status);
            return res.json();
        })
        .then(data => {
            const select = document.getElementById('select_categoria_gasto');
            if (data.success && select) {
                let html = '<option value="">Seleccione...</option>';
                data.data.forEach(cat => {
                    html += `<option value="${cat.id}">${cat.nombre}</option>`;
                });
                select.innerHTML = html;
                console.log('✅ Categorías:', data.data.length);
            }
        })
        .catch(err => {
            console.error('❌ Categorías:', err);
            mostrarError('Error cargando categorías');
        });
}

function cargarFolio() {
    console.log('📄 Folio...');
    fetch('/cfsistem/app/controllers/egresosController.php?action=getSiguienteFolioGasto')
        .then(res => {
            if (!res.ok) throw new Error('HTTP: ' + res.status);
            return res.json();
        })
        .then(data => {
            const input = document.getElementById('folio_gasto');
            if (data.success && input) {
                input.value = data.folio;
                console.log('✅ Folio:', data.folio);
            }
        })
        .catch(err => {
            console.error('❌ Folio:', err);
            document.getElementById('folio_gasto').value = 'ERR-' + Date.now();
        });
}
function guardarGasto() {
    // 1. Obtener los elementos de forma segura
    const inputTotal = document.getElementById('inputTotalGasto');
    const selectCat = document.getElementById('select_categoria_gasto');
    
    // 2. Validaciones previas
    if (!selectCat || !selectCat.value) {
        return mostrarError('Por favor, seleccione una categoría');
    }

    const total = parseFloat(inputTotal ? inputTotal.value : 0);
    if (total <= 0) {
        return mostrarError('El total del gasto debe ser mayor a 0');
    }
    
    // 3. Preparar el envío
    const formData = new FormData(formGasto);
    
    // Debug para que veas en consola qué se está yendo realmente
    console.log('ID Categoría detectado:', formData.get('categoria_id'));

    const btn = formGasto.querySelector('button[type="submit"]');
    const textoOriginal = btn.innerHTML;
    
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Guardando...';
    
    fetch('/cfsistem/app/controllers/egresosController.php?action=guardarGasto', {
        method: 'POST',
        body: formData
    })
    .then(res => {
        if (!res.ok) throw new Error(`Error servidor: ${res.status}`);
        return res.json();
    })
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: '¡Guardado!',
                text: data.message,
                timer: 1500,
                showConfirmButton: false
            }).then(() => {
                const inst = bootstrap.Modal.getInstance(modalGastoEl);
                if(inst) inst.hide();
                location.reload();
            });
        } else {
            throw new Error(data.message || 'Error al procesar el gasto');
        }
    })
    .catch(err => {
        console.error('❌ Error en guardado:', err);
        mostrarError(err.message);
        btn.disabled = false;
        btn.innerHTML = textoOriginal;
    });
}
function limpiarTabla() {
    document.querySelectorAll('#tablaConceptosGasto tbody tr:not(:first-child)')
        .forEach(f => f.remove());
    
    const primera = document.querySelector('#tablaConceptosGasto tbody tr');
    if (primera) {
        primera.querySelector('input[name="desc[]"]').value = '';
        primera.querySelector('.cant').value = '1';
        primera.querySelector('.precio').value = '0.00';
    }
}

function calcularGasto() {
    let total = 0;
    document.querySelectorAll('#tablaConceptosGasto tbody tr').forEach(fila => {
        const cant = parseFloat(fila.querySelector('.cant').value) || 0;
        const precio = parseFloat(fila.querySelector('.precio').value) || 0;
        const subtotal = cant * precio;
        fila.querySelector('.subtotal_fila').textContent = '$' + subtotal.toFixed(2);
        total += subtotal;
    });
    
    const txtTotal = document.getElementById('txtTotalGasto');
    const inputTotal = document.getElementById('inputTotalGasto');
    txtTotal.textContent = '$' + total.toLocaleString('es-MX', {minimumFractionDigits: 2});
    inputTotal.value = total;
}

function mostrarError(msg) {
    Swal.fire({
        icon: 'error',
        title: 'Oops!',
        text: msg,
        toast: true,
        position: 'top-end'
    });
}

function abrirModalGasto() {
    if (modalGastoEl) {
        new bootstrap.Modal(modalGastoEl).show();
    }
}

function agregarFilaGasto() {
    const tbody = document.querySelector('#tablaConceptosGasto tbody');
    const fila = document.createElement('tr');
    fila.innerHTML = `
        <td><input type="text" name="desc[]" class="form-control form-control-sm border-0 bg-light" style="border-radius: 8px;" required></td>
        <td><input type="number" name="cant[]" class="form-control form-control-sm border-0 bg-light cant text-center" style="border-radius: 8px;" value="1" min="0" step="any"></td>
        <td><input type="number" name="precio[]" class="form-control form-control-sm border-0 bg-light precio" style="border-radius: 8px;" value="0.00" min="0" step="0.01"></td>
        <td class="text-end fw-bold subtotal_fila pe-3">$0.00</td>
        <td><button type="button" class="btn btn-sm text-danger" onclick="this.closest('tr').remove(); calcularGasto();">
            <i class="bi bi-trash"></i>
        </button></td>`;
    tbody.appendChild(fila);
    calcularGasto();
}
</script>