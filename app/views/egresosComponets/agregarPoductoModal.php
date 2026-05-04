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
                    <div class="row g-3">

                        <input type="hidden" name="precio_adquisicion" value="0">

                        <div class="col-md-4">
                            <label class="mac-label">SKU / Código</label>
                            <input type="text" name="sku" class="form-control" required>
                        </div>

                        <div class="col-md-8">
                            <label class="mac-label">Nombre del Producto</label>
                            <input type="text" name="nombre" class="form-control" required>
                        </div>

                        <div class="col-md-6">
                            <div class="d-flex justify-content-between">
                                <label class="mac-label">Categoría</label>
                                <button type="button" class="btn btn-sm btn-link p-0"
                                    onclick="abrirSubModalCategoria()">
                                    <i class="bi bi-plus-circle-fill"></i>
                                </button>
                            </div>

                            <select name="categoria_id" id="select_categoria_id" class="form-select" required>
                                <option value="">Cargando...</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="mac-label">Unidad Base</label>
                            <input type="text" id="u_base" name="unidad_medida" class="form-control" value="PZA">
                        </div>

                        <div class="col-md-6">
                            <label class="mac-label">Unidad Mayoreo</label>
                            <input type="text" id="u_mayoreo" name="unidad_reporte" class="form-control">
                        </div>

                        <div class="col-md-6">
                            <label class="mac-label">Factor</label>
                            <input type="number" id="f_conversion" name="factor_conversion" class="form-control" value="1">
                            <small id="helper-conversion" class="text-primary fw-bold"></small>
                        </div>

                        <div class="col-md-4">
                            <label class="mac-label">IVA (%)</label>
                            <input type="number" name="impuesto_iva" class="form-control" value="16">
                        </div>

                        <div class="col-md-4">
                            <label class="mac-label">Clave SAT</label>
                            <input type="text" name="fiscal_clave_prod" class="form-control">
                        </div>
                        <div class="col-md-4">
                                        <label class="small text-muted">Clave Unidad</label>
                                        <input type="text" name="fiscal_clave_unidad" id="fiscal_clave_unidad" class="form-control form-control-sm border-0 shadow-sm">
                                    </div>

                        <div class="col-12">
                            <div class="card p-3 shadow-sm border-0">
                                <h6 class="small text-muted">PRECIOS</h6>
                                <div class="row g-2">

                                    <div class="col-md-4">
                                        <input type="number" name="precio_minorista" class="form-control" placeholder="Minorista">
                                    </div>

                                    <div class="col-md-4">
                                        <input type="number" name="precio_mayorista" class="form-control" placeholder="Mayorista">
                                    </div>

                                    <div class="col-md-4">
                                        <input type="number" name="precio_distribuidor" class="form-control" placeholder="Distribuidor">
                                    </div>

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
                        <i class="bi bi-save me-2"></i> Guardar
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
                            <input type="text" name="nombre" id="nombre" class="form-control"
                                placeholder="Ej: Herramientas" required>
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

                fetch('/cfsistem/app/controllers/egresosController?action=getCategoriasJSON')
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
                url: this.urlControlador + '?action=getCategoriasJSON',
                type: 'GET',
                dataType: 'json',

                success: (data) => {

                    select.empty().append('<option value="">Seleccionar...</option>');

                    if (Array.isArray(data)) {
                        data.forEach(cat => {
                            select.append(`<option value="${cat.id}">${cat.nombre}</option>`);
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

        $.post('almacenes.php?action=guardarCategoria', { nombre }, function(res) {

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