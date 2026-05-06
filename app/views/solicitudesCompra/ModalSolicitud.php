
    <div class="modal fade" id="modalSolicitud" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content"
                style="border-radius: 20px; border: none; overflow: hidden; box-shadow: 0 15px 50px rgba(0,0,0,0.2);">
                <form id="formSolicitud">
                    <div class="modal-header bg-white border-0 pt-4 px-4">
                        <div class="d-flex align-items-center">
                            <div class="bg-primary text-white rounded-3 p-2 me-3 shadow-sm">
                                <i class="bi bi-file-earmark-plus fs-4"></i>
                            </div>
                            <div>
                                <h4 class="fw-bold mb-0">Nueva Solicitud de Compra</h4>
                                <p class="text-muted small mb-0">Complete los datos para requerir materiales al almacén
                                </p>
                            </div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body px-4">
                        <div class="row g-3 mb-4 p-3 rounded-4 bg-light shadow-sm align-items-end">

                            <div class="col-md-3">
                                <label class="form-label small fw-bold text-muted text-uppercase">Almacén de
                                    Destino</label>
                                <?php if (isset($es_admin) && $es_admin): ?>
                                <select name="almacen_id" class="form-select select2-modal" required>
                                    <option value="">-- Seleccionar --</option>
                                    <?php foreach ($almacenes as $alm): ?>
                                    <option value="<?= $alm['id'] ?>"><?= htmlspecialchars($alm['nombre']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <?php else: ?>
                                <input type="text" class="form-control border-0 bg-white fw-bold"
                                    value="<?= htmlspecialchars($almacenes[0]['nombre'] ?? 'Almacén Asignado') ?>"
                                    readonly>
                                <input type="hidden" name="almacen_id"
                                    value="<?= $almacen_usuario ?? ($almacenes[0]['id'] ?? '') ?>">
                                <?php endif; ?>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label small fw-bold text-muted text-uppercase">Proveedor
                                    Sugerido</label>
                                <select name="proveedor_id" class="form-select select2-modal" required>
                                    <option value="">Seleccionar proveedor...</option>
                                    <?php foreach($proveedores as $p): ?>
                                    <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['nombre_comercial']) ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted text-uppercase">Añadir Producto (SKU o
                                    Nombre)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0"><i
                                            class="bi bi-search"></i></span>
                                    <select id="buscadorProductos" class="form-select select2-modal border-start-0">
                                        <option value="">Escribe para buscar...</option>
                                        <?php foreach($listaProductos as $pr): ?>
                                        <option value="<?= $pr['producto_id'] ?>"
                                            data-nombre="<?= htmlspecialchars($pr['nombre']) ?>"
                                            data-sku="<?= htmlspecialchars($pr['sku']) ?>"
                                            data-um="<?= htmlspecialchars($pr['unidad_medida']) ?>"
                                            data-ur="<?= htmlspecialchars($pr['unidad_reporte']) ?>"
                                            data-factor="<?= $pr['factor_conversion'] ?? 1 ?>">
                                            [<?= $pr['sku'] ?>] <?= $pr['nombre'] ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive border rounded-4 bg-white">
                            <table class="table align-middle mb-0" id="tablaDetalle">
                                <thead class="bg-light">
                                    <tr class="text-muted small uppercase">
                                        <th class="ps-4" style="width: 45%;">Producto</th>
                                        <th style="width: 20%;">Cantidad</th>
                                        <th style="width: 25%;">Presentación / Unidad</th>
                                         <th style="width: 25%;">Precio</th>
                                        <th style="width: 10%;" class="text-end pe-4">Acción</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>

                            <div id="emptyState" class="text-center py-5 text-muted">
                                <div class="mb-3">
                                    <i class="bi bi-cart-plus opacity-25" style="font-size: 3.5rem;"></i>
                                </div>
                                <p class="fw-medium">La lista está vacía</p>
                                <small>Utiliza el buscador de arriba para añadir artículos</small>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer border-0 p-4">
                        <button type="button" class="btn btn-link text-decoration-none text-muted fw-bold"
                            data-bs-dismiss="modal">
                            Cancelar
                        </button>
                        <button type="submit" class="btn btn-primary rounded-pill px-5 py-2 fw-bold shadow">
                            <i class="bi bi-check2-circle me-2"></i> Confirmar Solicitud
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<script>
     const URL_CONTROLADOR = '../controllers/solicitudesCompraController.php';

       $('.select2-modal').select2({
            theme: 'bootstrap-5',
            dropdownParent: $('#modalSolicitud')
        });

        $('#buscadorProductos').on('select2:select', function(e) {
            const d = e.params.data.element.dataset;
            const id = $(this).val();
            if ($(`#fila-${id}`).length) {
                Swal.fire('Aviso', 'El producto ya está en la lista', 'info');
                return;
            }
            $('#emptyState').addClass('d-none');
            $('#tablaDetalle tbody').append(`
                <tr id="fila-${id}">
                    <td class="ps-4"><b>${d.nombre}</b><br><small class="text-muted">${d.sku}</small></td>
                    <td><input type="number" name="items[${id}][cant]" class="form-control" step="0.01" value="1" required></td>
                    <td><select name="items[${id}][unidad]" class="form-select">
                        <option value="1">Unidad (${d.um})</option>
                        <option value="${d.factor}">Presentación (${d.ur})</option>
                    </select></td>
                     <td><input type="number" name="items[${id}][precio]" class="form-control" step="0.01" placeholder="costo" required></td>
                   
                    <td><button type="button" class="btn btn-link text-danger" onclick="quitarFila(${id})"><i class="bi bi-trash"></i></button></td>
                </tr>`);
            $(this).val(null).trigger('change');
        });

        $('#formSolicitud').on('submit', async function(e) {
            e.preventDefault();
            if (!$('#tablaDetalle tbody tr').length) {
                Swal.fire('Error', 'Agregue productos', 'warning');
                return;
            }
            Swal.fire({
                title: 'Guardando...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });
            try {
                const resp = await fetch(`${URL_CONTROLADOR}?action=guardar`, {
                    method: 'POST',
                    body: new FormData(this)
                });
                const res = await resp.json();
                if (res.status === 'success') {
                    await Swal.fire({
                        icon: 'success',
                        title: '¡Éxito!',
                        text: res.message,
                        timer: 1500,
                        showConfirmButton: false
                    });
                    location.reload();
                } else {
                    Swal.fire('Error', res.message, 'error');
                }
            } catch (e) {
                Swal.fire('Error', 'Fallo de conexión', 'error');
            }
        });

        // ENVÍO DEL FORMULARIO DE CONVERSIÓN
        $('#formConvertirCompra').on('submit', async function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Procesando ingreso...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });
            try {
                const resp = await fetch(`${URL_CONTROLADOR}?action=convertirACompra`, {
                    method: 'POST',
                    body: new FormData(this)
                });
                const res = await resp.json();
                if (res.status === 'success') {
                    await Swal.fire({
                        icon: 'success',
                        title: 'Ingresado',
                        text: res.message
                    });
                    location.reload();
                } else {
                    Swal.fire('Error', res.message, 'error');
                }
            } catch (e) {
                Swal.fire('Error', 'Fallo de conexión', 'error');
            }
        });
    
    function quitarFila(id) {
        $(`#fila-${id}`).remove();
        if (!$('#tablaDetalle tbody tr').length) $('#emptyState').removeClass('d-none');
    }

    function nuevaSolicitud() {
        $('#formSolicitud')[0].reset();
        $('#tablaDetalle tbody').empty();
        $('#emptyState').removeClass('d-none');
        $('#modalSolicitud').modal('show');
    }

</script>