<div class="modal fade" id="modalGasto" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow-lg" style="border-radius: 22px;">
            <form id="formNuevoGasto" enctype="multipart/form-data">
                <div class="modal-header bg-warning text-dark" style="border-radius: 22px 22px 0 0;">
                    <h5 class="modal-title fw-bold"><i class="bi bi-cash-stack me-2"></i> Registrar Nuevo Gasto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Folio/Factura</label>
                            <input type="text" id="folio_gasto" name="folio" class="form-control border border-subtle"
                                style="border-radius: 12px;" placeholder="Cargando..." readonly required>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label small fw-bold">Almacén Destino</label>
                            <select name="almacen_id" class="form-select border border-subtle"
                                style="border-radius: 12px;" <?= ($_SESSION['rol_id'] != 1) ? 'readonly style="pointer-events: none;"' : '' ?> required>
                                <?php foreach ($almacenes as $alm): ?>
                                    <option value="<?= $alm['id'] ?>" <?= ($_SESSION['almacen_id'] == $alm['id']) ? 'selected' : '' ?>>
                                        <?= $alm['nombre'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-primary">Categoría de Gasto</label>
                            <div class="input-group">
                                <select id="select_categoria_gasto" name="categoria_id"
                                    class="form-select border border-subtle" style="border-radius: 12px 0 0 12px;"
                                    required>
                                    <option value="">Seleccione categoría...</option>
                                </select>
                                <button type="button" class="btn btn-primary" style="border-radius: 0 12px 12px 0;"
                                    onclick="abrirModalNuevaCategoria()">
                                    <i class="bi bi-plus-lg"></i>
                                </button>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Elija un Proveedor o Escriba uno nuevo</label>
                            <div class="input-group">
                                <!-- Select de proveedores -->
                                <select class="form-select border border-subtle" id="select-proveedor"
                                    name="proveedor_id" style="border-radius: 12px 0 0 12px;" onchange="actualizar()">
                                    <option value="">Elija un proveedor...</option>
                                </select>

                                <!-- Input oculto por defecto (d-none) pero accesible en el DOM/FORM -->
                                <button class="btn btn-outline-success" type="button"
                                    style="border-radius: 0 12px 12px 0;" onclick="abrirModalNuevoProveedor()">
                                    <i class="bi bi-plus-lg"></i>
                                </button>

                            </div>
                            <input type="text" id="beneficiario" name="beneficiario"
                                class="form-control border border-subtle d-none "
                                style="border-radius: 12px 0 0 12px; padding-top: 10px;"
                                placeholder="Ej: CFE, Gasolinera..." required>


                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Método de Pago</label>
                            <select name="metodo_pago" class="form-select border border-subtle"
                                style="border-radius: 12px;">
                                <option value="Efectivo">Efectivo</option>
                                <option value="Transferencia">Transferencia</option>
                                <option value="Tarjeta">Tarjeta</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Comprobante (Evidencia)</label>
                            <input type="file" name="documento" class="form-control border border-subtle"
                                style="border-radius: 12px;" accept=".jpg,.png,.pdf">
                        </div>
                    </div>

                    <hr class="text-body-secondary">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="fw-bold mb-0 card-title-text">Conceptos del Gasto</h6>
                        <button type="button" class="btn btn-sm fw-bold border border-subtle card-title-text"
                            onclick="agregarFilaGasto()">
                            <i class="bi bi-plus-circle-fill"></i> Agregar Concepto
                        </button>
                    </div>

                    <div class="table-responsive mb-3">
                        <table class="table table-borderless align-middle" id="tablaConceptosGasto">
                            <thead class="border border-subtle">
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
                                    <td><input type="text" name="desc[]"
                                            class="form-control form-control-sm border border-subtle"
                                            style="border-radius: 8px;" required></td>
                                    <td><input type="number" name="cant[]"
                                            class="form-control form-control-sm border border-subtle cant text-center"
                                            style="border-radius: 8px;" value="1" step="any" oninput="calcularGasto()">
                                    </td>
                                    <td><input type="number" name="precio[]"
                                            class="form-control form-control-sm border border-subtle precio"
                                            style="border-radius: 8px;" value="0.00" step="any"
                                            oninput="calcularGasto()"></td>
                                    <td class="text-end fw-bold subtotal_fila pe-3">$0.00</td>
                                    <td></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="row align-items-center">
                        <div class="col-md-7">
                            <label class="form-label small fw-bold">Observaciones</label>
                            <textarea name="observaciones" class="form-control text-uppercase border border-subtle"
                                style="border-radius: 12px;" rows="2" placeholder="Notas internas..."></textarea>
                            <input type="date" id="fecha" name="fecha" value="<?= date("Y-m-d") ?>"
                                class="form-control border border-subtle mt-2" style="border-radius: 12px;">
                        </div>
                        <div class="col-md-5 text-end">
                            <h4 class="text-body-secondary small fw-bold mb-0">TOTAL</h4>
                            <h2 class="fw-bold text-dark" id="txtTotalGasto">$ 0.00</h2>
                            <input type="hidden" name="total_final" id="inputTotalGasto" value="0">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light fw-bold" data-bs-dismiss="modal"
                        style="border-radius: 12px;">Cancelar</button>
                    <button type="submit" class="btn btn-warning fw-bold px-4 shadow-sm"
                        style="border-radius: 12px;">Guardar Gasto</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalNuevaCategoriaGasto" tabindex="-1" aria-hidden="true"
    style="background: rgba(0,0,0,0.4);">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content shadow-lg" style="border-radius: 20px;">
            <div class="modal-header">
                <h6 class="modal-title fw-bold">Nueva Categoría</h6>
                <button type="button" class="btn-close" onclick="$('#modalNuevaCategoriaGasto').modal('hide')"></button>
            </div>
            <div class="modal-body pt-0">
                <div class="mb-3">
                    <label class="small fw-bold text-body-secondary">Nombre</label>
                    <input type="text" id="nuevo_nombre_cat" class="form-control border border-subtle"
                        style="border-radius: 10px;" placeholder="Ej: Servicios">
                </div>
            </div>
            <div class="modal-footer pt-0">
                <button type="button" class="btn btn-primary w-100 fw-bold" onclick="guardarNuevaCategoria()"
                    style="border-radius: 10px;">Agregar</button>
            </div>
        </div>
    </div>
</div>

<script>
    cargarProveedoresSelect();

    async function cargarProveedoresSelect() {
        const select = document.getElementById('select-proveedor');
        if (!select) return;

        try {
            const url = '/cfsistem/app/controllers/egresosController.php?action=obtenerProveedores';
            const respuesta = await fetch(url);
            if (!respuesta.ok) throw new Error('Error en la respuesta del servidor');

            const resultado = await respuesta.json();

            if (resultado.success && Array.isArray(resultado.data)) {
                select.innerHTML = '<option value="">Elija un proveedor...</option>';

                resultado.data.forEach(proveedor => {
                    const opcion = document.createElement('option');
                    opcion.value = proveedor.nombre_comercial;
                    opcion.textContent = proveedor.nombre_comercial;
                    select.appendChild(opcion);
                });

                // Opción especial para activar el modo de escritura manual
                const opcionOtro = document.createElement('option');
                opcionOtro.value = 'OTRO';
                opcionOtro.textContent = 'Otro (Escribir manualmente)';
                select.appendChild(opcionOtro);

            } else {
                select.innerHTML = '<option value="">No se pudieron cargar los proveedores</option>';
            }

        } catch (error) {
            select.innerHTML = '<option value="">Error al cargar la lista</option>';
            console.error('Error al ejecutar cargarProveedoresSelect:', error);
        }
    }

    // LÓGICA DE CONTROL DEL PROVEEDOR
    function actualizar() {
        const select = document.getElementById('select-proveedor');
        const input = document.getElementById('beneficiario');
        const val = select.value;

        if (val === 'OTRO') {
            // Mostrar input para escribir manualmente
            input.classList.remove('d-none');
            input.value = '';
            input.focus();
        } else {
            // Ocultar input y copiar valor seleccionado
            input.classList.add('d-none');
            input.value = val;
        }
    }

    // VARIABLES GLOBALES
    const modalGastoEl = document.getElementById('modalGasto');
    const formGasto = document.getElementById('formNuevoGasto');

    document.addEventListener('DOMContentLoaded', function () {
        console.log('🚀 Sistema Gastos INICIADO');

        if (!modalGastoEl || !formGasto) {
            console.error('❌ Modal o Form no encontrados');
            return;
        }

        cargarCategorias();

        modalGastoEl.addEventListener('show.bs.modal', function () {
            console.log('🟢 Modal ABIERTO');
            formGasto.reset();
            limpiarTabla();
            cargarFolio();
            calcularGasto();
            actualizar(); // Restablece visibilidad del input de proveedor
        });

        formGasto.addEventListener('submit', function (e) {
            e.preventDefault();
            console.log('📤 GUARDANDO...');
            guardarGasto();
        });

        document.addEventListener('input', function (e) {
            if (e.target.matches('.cant, .precio')) calcularGasto();
        });
    });

    function cargarCategorias() {
        fetch('/cfsistem/app/controllers/egresosController.php?action=get_categorias_egresos')
            .then(res => {
                if (!res.ok) throw new Error('HTTP: ' + res.status);
                return res.json();
            })
            .then(data => {
                const select = document.getElementById('select_categoria_gasto');
                if (data.success && select) {
                    let html = '<option value="">Seleccione categoría...</option>';
                    data.data.forEach(cat => {
                        html += `<option value="${cat.id}">${cat.nombre}</option>`;
                    });
                    select.innerHTML = html;
                }
            })
            .catch(err => {
                console.error('❌ Categorías:', err);
                mostrarError('Error cargando categorías');
            });
    }

    function cargarFolio() {
        fetch('/cfsistem/app/controllers/egresosController.php?action=getSiguienteFolioGasto')
            .then(res => {
                if (!res.ok) throw new Error('HTTP: ' + res.status);
                return res.json();
            })
            .then(data => {
                const input = document.getElementById('folio_gasto');
                if (data.success && input) {
                    input.value = data.folio;
                }
            })
            .catch(err => {
                console.error('❌ Folio:', err);
                document.getElementById('folio_gasto').value = 'ERR-' + Date.now();
            });
    }

    function guardarGasto() {
        const inputTotal = document.getElementById('inputTotalGasto');
        const selectCat = document.getElementById('select_categoria_gasto');
        const inputBeneficiario = document.getElementById('beneficiario');

        if (!selectCat || !selectCat.value) {
            return mostrarError('Por favor, seleccione una categoría');
        }

        if (!inputBeneficiario.value.trim()) {
            return mostrarError('Por favor, ingrese o seleccione un proveedor/beneficiario');
        }

        const total = parseFloat(inputTotal ? inputTotal.value : 0);
        if (total <= 0) {
            return mostrarError('El total del gasto debe ser mayor a 0');
        }

        const formData = new FormData(formGasto);
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
                        if (inst) inst.hide();
                        gastoDetalle_cargarVista('gasto', data.id);
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
        txtTotal.textContent = '$' + total.toLocaleString('es-MX', { minimumFractionDigits: 2 });
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
        <td><input type="text" name="desc[]" class="form-control form-control-sm border border-subtle" style="border-radius: 8px;" required></td>
        <td><input type="number" name="cant[]" class="form-control form-control-sm border border-subtle cant text-center" style="border-radius: 8px;" value="1" min="0" step="any"></td>
        <td><input type="number" name="precio[]" class="form-control form-control-sm border border-subtle precio" style="border-radius: 8px;" value="0.00" min="0" step="0.01"></td>
        <td class="text-end fw-bold subtotal_fila pe-3">$0.00</td>
        <td><button type="button" class="btn btn-sm text-danger" onclick="this.closest('tr').remove(); calcularGasto();">
            <i class="bi bi-trash"></i>
        </button></td>`;
        tbody.appendChild(fila);
        calcularGasto();
    }

    function abrirModalNuevaCategoria() {
        const modalCat = new bootstrap.Modal(document.getElementById('modalNuevaCategoriaGasto'));
        modalCat.show();
    }

    function guardarNuevaCategoria() {
        const nombreInput = document.getElementById('nuevo_nombre_cat');
        const nombre = nombreInput.value.trim();

        if (!nombre) {
            return Swal.fire({
                icon: 'warning',
                title: 'Campo requerido',
                text: 'Debes escribir el nombre de la categoría',
                toast: true,
                position: 'top-end',
                timer: 3000
            });
        }

        const datos = new FormData();
        datos.append('ajax', 'guardar_categoria_egreso');
        datos.append('nombre', nombre);

        const btn = document.querySelector('#modalNuevaCategoriaGasto .btn-primary');
        const textoOriginal = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

        fetch('/cfsistem/app/controllers/egresosController.php', {
            method: 'POST',
            body: datos
        })
            .then(res => {
                if (!res.ok) throw new Error('Respuesta del servidor no válida');
                return res.json();
            })
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Categoría creada',
                        timer: 1500,
                        showConfirmButton: false
                    });

                    nombreInput.value = '';
                    const modalEl = document.getElementById('modalNuevaCategoriaGasto');
                    const modalInst = bootstrap.Modal.getInstance(modalEl);
                    if (modalInst) modalInst.hide();

                    cargarCategorias();

                } else {
                    throw new Error(data.message || 'Error al guardar');
                }
            })
            .catch(err => {
                console.error('❌ Error Cat:', err);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
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

<script>
    // Evento Delegado para transformar a Mayúsculas entradas de texto y áreas de texto
    document.addEventListener('input', function (e) {
        if (e.target.matches('input[type="text"], textarea')) {
            e.target.value = e.target.value.toUpperCase();
        }
    });
</script>