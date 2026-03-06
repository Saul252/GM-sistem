<div class="modal fade" id="modalAgregarProducto" tabindex="-1" aria-labelledby="modalAgregarProductoLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
            <div class="modal-header bg-dark text-white py-3">
                <h5 class="modal-title" id="modalAgregarProductoLabel">
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
                                <label class="mac-label">Categoría</label>
                                <select name="categoria_id" class="mac-select" required>
                                    <option value="">Seleccionar...</option>
                                    <?php foreach($categorias as $cat): ?>
                                        <option value="<?= $cat['id'] ?>"><?= $cat['nombre'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mac-select-container">
                                <label class="mac-label">Unidad de Medida (Base)</label>
                                <input type="text" name="unidad_medida" class="form-control border-0 bg-transparent p-0" placeholder="Ej: PZA, KG" value="PZA">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mac-select-container">
                                <label class="mac-label">Unidad de Mayoreo</label>
                                <input type="text" name="unidad_reporte" class="form-control border-0 bg-transparent p-0" placeholder="Ej: CAJA, BULTO">
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="mac-select-container">
                                <label class="mac-label">Factor Conv.</label>
                                <input type="number" name="factor_conversion" class="form-control border-0 bg-transparent p-0" value="1" step="0.01">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mac-select-container border-success">
                                <label class="mac-label text-success">Costo Adq.</label>
                                <input type="number" name="precio_adquisicion" class="form-control border-0 bg-transparent p-0" value="0" step="0.01">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mac-select-container">
                                <label class="mac-label">Clave SAT</label>
                                <input type="text" name="fiscal_clave_prod" class="form-control border-0 bg-transparent p-0" placeholder="8-dígitos">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mac-select-container">
                                <label class="mac-label">IVA (%)</label>
                                <input type="number" name="impuesto_iva" class="form-control border-0 bg-transparent p-0" value="16.00">
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="card border-0 shadow-sm bg-white p-3" style="border-radius: 12px;">
                                <h6 class="small fw-bold text-muted mb-3"><i class="bi bi-tag-fill me-1"></i> PRECIOS DE VENTA SUGERIDOS (PARA TODOS LOS ALMACENES)</h6>
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

<style>
.x-small { font-size: 10px; }
/* Hereda los estilos .mac-select-container de tu componente Compras */
</style>

<script>
// Lógica AJAX para el guardado
$("#formAgregarProducto").on("submit", function(e) {
    e.preventDefault();
    const btn = $("#btnGuardarProducto");
    const originalText = btn.html();
    
    btn.prop("disabled", true).html('<span class="spinner-border spinner-border-sm"></span> Guardando...');

    $.ajax({
        url: 'app/controllers/AlmacenController.php?action=guardar',
        type: 'POST',
        data: $(this).serialize(),
        dataType: 'json',
        success: function(res) {
            if (res.status === "success") {
                Swal.fire('¡Éxito!', res.message, 'success');
                $("#modalAgregarProducto").modal("hide");
                $("#formAgregarProducto")[0].reset();
                
                // NOTA IMPORTANTE: Si estamos en el modal de compras, 
                // aquí llamamos a la función para refrescar la lista de productos
                if (typeof refrescarListaProductosCompra === "function") {
                    refrescarListaProductosCompra(res.id); 
                }
            } else {
                Swal.fire('Error', res.message, 'error');
            }
        },
        error: function() {
            Swal.fire('Error', 'No se pudo procesar la solicitud', 'error');
        },
        complete: function() {
            btn.prop("disabled", false).html(originalText);
        }
    });
});
</script>