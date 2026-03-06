<div class="modal fade" id="modalAgregarProducto" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
            <div class="modal-header bg-dark text-white py-3">
                <h5 class="modal-title">
                    <i class="bi bi-plus-circle-fill me-2"></i> Nuevo Producto al Catálogo
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="formAgregarProducto" autocomplete="off">
                <div class="modal-body bg-light p-4">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="mac-select-container">
                                <label class="mac-label">SKU / Código</label>
                                <input type="text" name="sku" class="form-control border-0 bg-transparent p-0" placeholder="Ej: PROD-001" required>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="mac-select-container">
                                <label class="mac-label">Nombre del Producto</label>
                                <input type="text" name="nombre" class="form-control border-0 bg-transparent p-0" placeholder="Nombre descriptivo" required>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mac-select-container">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <label class="mac-label m-0">Categoría</label>
                                    <button type="button" class="btn btn-link p-0 text-decoration-none" 
                                            onclick="$('#modalAgregarCategoria').modal('show')">
                                        <i class="bi bi-plus-circle-fill text-primary" style="font-size: 1.1rem;"></i>
                                    </button>
                                </div>
                                <select name="categoria_id" id="select_categoria_id" class="mac-select" required>
                                    <option value="">Cargando...</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mac-select-container border-primary">
                                <label class="mac-label text-primary">Unidad Mayoreo</label>
                                <input type="text" id="u_mayoreo" name="unidad_reporte" class="form-control border-0 bg-transparent p-0" placeholder="Ej: MILLAR, TON">
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mac-select-container">
                                <label class="mac-label">Unidad Base (Venta)</label>
                                <input type="text" id="u_base" name="unidad_medida" class="form-control border-0 bg-transparent p-0" placeholder="Ej: PZA, KG" value="PZA">
                            </div>
                        </div>

                        <div class="col-md-4 offset-md-4">
                            <div class="mac-select-container border-primary shadow-sm">
                                <label class="mac-label text-primary fw-bold">Factor de Mayoreo</label>
                                <input type="number" id="f_conversion" name="factor_conversion" class="form-control border-0 bg-transparent p-0 fw-bold" value="1" step="0.01">
                            </div>
                            <small id="helper-conversion" class="text-primary fw-bold" style="font-size: 10px;"></small>
                        </div>

                        <input type="hidden" name="precio_adquisicion" value="0">

                        <div class="col-md-2">
                            <div class="mac-select-container">
                                <label class="mac-label">IVA (%)</label>
                                <input type="number" name="impuesto_iva" class="form-control border-0 bg-transparent p-0" value="16.00">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="mac-select-container">
                                <label class="mac-label">SAT</label>
                                <input type="text" name="fiscal_clave_prod" class="form-control border-0 bg-transparent p-0" placeholder="Código">
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="card border-0 shadow-sm bg-white p-3" style="border-radius: 12px;">
                                <h6 class="small fw-bold text-muted mb-3">PRECIOS DE VENTA SUGERIDOS</h6>
                                <div class="row g-2">
                                    <div class="col-md-4">
                                        <label class="x-small fw-bold mb-1">P. MINORISTA</label>
                                        <input type="number" name="precio_minorista" class="form-control form-control-sm border-primary" value="0" step="0.01">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="x-small fw-bold mb-1">P. MAYORISTA</label>
                                        <input type="number" name="precio_mayorista" class="form-control form-control-sm border-info" value="0" step="0.01">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="x-small fw-bold mb-1">P. DISTRIBUIDOR</label>
                                        <input type="number" name="precio_distribuidor" class="form-control form-control-sm border-warning" value="0" step="0.01">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-white border-0 px-4 pb-4">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-dark rounded-pill px-4" id="btnGuardarProducto">
                        <i class="bi bi-save me-2"></i> Guardar e Integrar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/modalCategoria.php'; ?>

<style>
.x-small { font-size: 10px; }
.mac-select-container { padding: 8px 12px; background: #fff; border: 1px solid #d1d1d6; border-radius: 10px; position: relative; }
.mac-label { font-size: 10px; font-weight: 700; color: #8e8e93; text-transform: uppercase; display: block; }
.mac-select { width: 100%; border: none; outline: none; background: transparent; font-size: 14px; cursor: pointer; }
</style>
<script>
function iniciarModuloProducto() {
    // Si $ no existe, reintentamos en 100ms
    if (typeof $ === 'undefined') {
        setTimeout(iniciarModuloProducto, 100);
        return;
    }

    const ProdModulo = {
        // Al estar en egresosController.php, la ruta relativa al archivo vecino es solo el nombre
        urlControlador: 'almacenes.php', 

        init: function() {
            this.bindEvents();
            this.cargarCategorias();
            this.actualizarTexto();
        },

        bindEvents: function() {
            // Escuchar cambios en los inputs de unidades y factor
            $('#u_mayoreo, #u_base, #f_conversion').on('input', () => this.actualizarTexto());
            
            // Recargar categorías al abrir el modal
            $('#modalAgregarProducto').on('show.bs.modal', () => this.cargarCategorias());

            // Evitar duplicidad de submits
            $("#formAgregarProducto").off("submit").on("submit", (e) => {
                e.preventDefault();
                this.guardar();
            });
        },

        cargarCategorias: function() {
            const select = $('#select_categoria_id');
            // Forzamos la limpieza del select antes de cargar
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
                error: (jqXHR) => {
                    console.error("Error en categorías. URL intentada:", this.urlControlador);
                    select.empty().append('<option value="">Error al cargar</option>');
                }
            });
        },

        actualizarTexto: function() {
            let m = $('#u_mayoreo').val() || 'Unidad';
            let b = $('#u_base').val() || 'PZA';
            let f = $('#f_conversion').val() || '1';
            $('#helper-conversion').text(`1 ${m} = ${f} ${b}(s)`);
        },

        guardar: function() {
            const btn = $("#btnGuardarProducto");
            btn.prop("disabled", true).text("Guardando...");

            $.ajax({
                url: this.urlControlador + '?action=guardar',
                type: 'POST',
                data: $("#formAgregarProducto").serialize(),
                dataType: 'json',
                success: (res) => {
                    if (res.status === "success") {
                        Swal.fire('¡Éxito!', res.message, 'success');
                        $("#modalAgregarProducto").modal("hide");
                        $("#formAgregarProducto")[0].reset();
                        this.actualizarTexto();
                        if (typeof refrescarListaProductosCompra === "function") {
                            refrescarListaProductosCompra(res.id);
                        }
                    } else {
                        Swal.fire('Error', res.message, 'error');
                    }
                },
                error: () => Swal.fire('Error', 'Error de conexión con el servidor', 'error'),
                complete: () => btn.prop("disabled", false).html('<i class="bi bi-save me-2"></i> Guardar')
            });
        }
    };

    // Lanzamos el módulo
    ProdModulo.init();
    
    // Función global para el modal de categorías (hijo)
    window.ejecutarGuardarCategoria = function() {
        const nombre = $('#inputNombreCategoria').val().trim();
        if (!nombre) return;
        
        $.post('almacenes.php?action=guardarCategoria', { nombre: nombre }, function(res) {
            if (res.status === "success") {
                ProdModulo.cargarCategorias();
                setTimeout(() => {
                    $('#select_categoria_id').val(res.id);
                }, 500);
                $('#modalAgregarCategoria').modal('hide');
                $('#inputNombreCategoria').val('');
            }
        }, 'json');
    };
}

// Ejecución inicial
iniciarModuloProducto();
</script>