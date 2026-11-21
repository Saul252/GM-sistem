<?php
/* ============================================================
   MODAL: Registrar Movimiento (versión corregida)
   ------------------------------------------------------------
   - Al registrar con éxito muestra 2 acciones:
     1) "Ver detalle" -> abre abrirModalDetalleMovimiento(id)
     2) "Cerrar" -> recarga la página (location.reload())
   ============================================================ */
?>
<div class="modal fade" id="modalMovimiento" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content glass-card p-2">
            <div class="modal-header">
                <h5 class="fw-bold m-0">Registrar Movimiento</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <!-- Saldos del almacén seleccionado -->
                <div class="row g-2 mb-3 text-center">
                    <div class="col-4">
                        <div class="p-2 rounded-4 bg-light border">
                            <small class="text-body-secondary d-block text-uppercase"
                                style="font-size: 9px; font-weight: 800;">Efectivo</small>
                            <span id="display_saldo_efectivo" class="fw-bold text-dark">$0.00</span>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="p-2 rounded-4 bg-light border">
                            <small class="text-body-secondary d-block text-uppercase"
                                style="font-size: 9px; font-weight: 800;">Tarjeta</small>
                            <span id="display_saldo_tarjeta" class="fw-bold text-dark">$0.00</span>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="p-2 rounded-4 bg-light border">
                            <small class="text-body-secondary d-block text-uppercase"
                                style="font-size: 9px; font-weight: 800;">Transf.</small>
                            <span id="display_saldo_transferencia" class="fw-bold text-dark">$0.00</span>
                        </div>
                    </div>
                </div>

                <form id="formNuevoMovimiento">
                    <div class="row g-3">

                        <!-- Sucursal origen -->
                        <div class="col-12">
                            <label class="text-xs fw-bold text-body-secondary text-uppercase">Sucursal Origen</label>
                            <select name="almacen_id" id="sel_almacen_origen" class="form-select ios-input" required>
                                <?php if ($esAdmin): ?>
                                    <option value="0">🌐 Todas (Administrador)</option>
                                <?php endif; ?>
                                <?php foreach ($listaAlmacenes as $alm): ?>
                                    <option value="<?= $alm['id'] ?>"><?= htmlspecialchars($alm['nombre']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Categoría -->
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label class="text-xs fw-bold text-body-secondary text-uppercase m-0">Categoría</label>
                                <span id="badge_tipo_operacion" class="badge"
                                    style="font-size: 10px; display: none;"></span>
                            </div>
                            <select name="categoria_id" id="sel_categoria" class="form-select ios-input" required>
                                <option value="">Seleccione una categoría...</option>
                                <?php foreach ($categoriasCapital as $cat): ?>
                                    <option value="<?= $cat['id'] ?>" data-tipo="<?= $cat['tipo_operacion'] ?>">
                                        <?= htmlspecialchars($cat['nombre']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Fecha -->
                        <div class="col-6">
                            <label class="text-xs fw-bold text-body-secondary text-uppercase">Fecha</label>
                            <input type="date" name="fecha_movimiento" id="fecha_movimiento"
                                class="form-control ios-input" value="<?= date('Y-m-d') ?>" required>
                        </div>

                        <!-- Método -->
                        <div class="col-6" id="col_metodo_pago">
                            <label class="text-xs fw-bold text-body-secondary text-uppercase">Método</label>
                            <select name="metodo_pago" id="sel_metodo_pago" class="form-select ios-input" required>
                                <option value="efectivo">💵 Efectivo</option>
                                <option value="tarjeta">💳 Tarjeta</option>
                                <option value="transferencia">🏛️ Transferencia</option>
                            </select>
                        </div>

                        <!-- Destino: almacén (solo traspasos entre almacenes) -->
                        <div id="col_destino_almacen" class="col-12 d-none">
                            <label class="text-xs fw-bold text-body-secondary text-uppercase">Almacén Destino</label>
                            <select name="almacen_destino_id" id="sel_almacen_destino" class="form-select ios-input"
                                disabled>
                                <option value="">Seleccione destino...</option>
                                <?php foreach ($listaAlmacenes as $alm): ?>
                                    <option value="<?= $alm['id'] ?>"><?= htmlspecialchars($alm['nombre']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Destino: caja fuerte -->
                        <div id="col_destino_caja" class="col-12 d-none">
                            <label class="text-xs fw-bold text-body-secondary text-uppercase">Caja Fuerte
                                Destino</label>
                            <select name="caja_fuerte_id" id="sel_caja_fuerte" class="form-select ios-input" disabled>
                                <option value="">Seleccione caja fuerte...</option>
                            </select>
                        </div>

                        <!-- Destino: banco -->
                        <div id="col_destino_banco" class="col-12 d-none">
                            <label class="text-xs fw-bold text-body-secondary text-uppercase">Banco Destino</label>
                            <select name="banco_id" id="sel_banco" class="form-select ios-input" disabled>
                                <option value="">Seleccione banco...</option>
                            </select>
                        </div>

                        <!-- Monto -->
                        <div class="col-md-12">
                            <label class="text-xs fw-bold text-body-secondary text-uppercase">Monto del
                                Movimiento</label>
                            <input type="number" step="0.01" min="0.01" name="monto"
                                class="form-control ios-input fw-bold" placeholder="0.00" required>
                        </div>

                        <!-- Concepto -->
                        <div class="col-12">
                            <label class="text-xs fw-bold text-body-secondary text-uppercase">Concepto /
                                Observación</label>
                            <textarea name="conceptos" class="form-control text-uppercase ios-input" rows="2"
                                placeholder="Describa el motivo..."></textarea>
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary w-100 btn-ios py-3 shadow">Confirmar
                            Operación</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function ocultarModalMovimientoBS() {
        const el = document.getElementById('modalMovimiento');
        if (!el) return;
        if (window.bootstrap && bootstrap.Modal) {
            const inst = bootstrap.Modal.getInstance(el);
            if (inst) {
                inst.hide();
                return;
            }
        }
        if (typeof window.$ !== 'undefined' && typeof $(el).modal === 'function') {
            $(el).modal('hide');
        }
    }

    const ModalMovimiento = {
        url: '/cfsistem/app/controllers/tesoreriaController.php',
        saldosLocales: { efectivo: 0, tarjeta: 0, transferencia: 0 },

        // IDs de categorías (deben coincidir con el controlador)
        CAT_CAJA_FUERTE: [5, 10],   // Movimiento a Caja Fuerte / Ingreso desde Caja Fuerte
        CAT_BANCO: [11, 12],        // Retiro de Banco / Traspaso entre Bancos
        CAT_ALMACEN: [2],           // Traspaso entre Almacenes

        init: function () {
            this.eventos();
            this.cargarCatalogos($('#sel_almacen_origen').val());
            this.actualizarDestinos();
        },

        actualizarDestinos: function () {
            const $opt = $('#sel_categoria').find('option:selected');
            const catId = parseInt($opt.val(), 10) || 0;
            const tipoOp = $opt.data('tipo');

            const $badge = $('#badge_tipo_operacion');
            $badge.removeClass('bg-success-subtle bg-danger-subtle bg-warning-subtle text-success text-danger text-warning-emphasis');
            if (tipoOp) {
                $badge.text(String(tipoOp).toUpperCase()).show();
                if (tipoOp === 'entrada') $badge.addClass('bg-success-subtle text-success');
                else if (tipoOp === 'salida') $badge.addClass('bg-danger-subtle text-danger');
                else $badge.addClass('bg-warning-subtle text-warning-emphasis');
            } else {
                $badge.hide();
            }

            $('#col_destino_almacen, #col_destino_caja, #col_destino_banco').addClass('d-none');
            $('#sel_almacen_destino, #sel_caja_fuerte, #sel_banco').prop('disabled', true).val('');

            let $col = null;
            if (this.CAT_BANCO.includes(catId)) $col = $('#col_destino_banco');
            else if (this.CAT_CAJA_FUERTE.includes(catId)) $col = $('#col_destino_caja');
            else if (this.CAT_ALMACEN.includes(catId) || (tipoOp === 'traspaso' && !this.CAT_BANCO.includes(catId))) {
                $col = $('#col_destino_almacen');
            }

            if ($col) {
                $col.removeClass('d-none').find('select').prop('disabled', false);
            }
        },

        eventos: function () {
            const self = this;

            $('#sel_almacen_origen, #fecha_movimiento').on('change', function () {
                self.cargarCatalogos($('#sel_almacen_origen').val());
            });

            $('#sel_categoria').on('change', function () {
                self.actualizarDestinos();
            });

            $('#modalMovimiento').on('show.bs.modal hidden.bs.modal', function () {
                $('#formNuevoMovimiento')[0].reset();
                self.actualizarDestinos();
            });

            $('#formNuevoMovimiento').on('submit', function (e) {
                e.preventDefault();

                const montoIngresado = parseFloat($('input[name="monto"]').val()) || 0;
                const tipoOp = $('#sel_categoria').find('option:selected').data('tipo');
                const metodo = $('#sel_metodo_pago').val();
                const saldoDisponible = self.saldosLocales[metodo] || 0;

                if (!$('#col_destino_caja').hasClass('d-none') && !$('#sel_caja_fuerte').val()) {
                    Swal.fire('Falta destino', 'Selecciona la caja fuerte.', 'warning');
                    return false;
                }
                if (!$('#col_destino_banco').hasClass('d-none') && !$('#sel_banco').val()) {
                    Swal.fire('Falta destino', 'Selecciona el banco.', 'warning');
                    return false;
                }
                if (!$('#col_destino_almacen').hasClass('d-none')) {
                    const destino = $('#sel_almacen_destino').val();
                    if (!destino) {
                        Swal.fire('Falta destino', 'Selecciona el almacén destino.', 'warning');
                        return false;
                    }
                    if (destino === $('#sel_almacen_origen').val()) {
                        Swal.fire('Destino inválido', 'El almacén destino no puede ser el mismo que el origen.', 'warning');
                        return false;
                    }
                }

                if ((tipoOp === 'salida' || tipoOp === 'traspaso') && montoIngresado > saldoDisponible) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Saldo Insuficiente',
                        text: `Intenta retirar $${montoIngresado.toLocaleString()} en ${metodo}, pero solo hay $${saldoDisponible.toLocaleString()} disponible.`,
                        confirmButtonColor: '#007AFF'
                    });
                    return false;
                }

                const $btn = $(this).find('button[type="submit"]');
                $btn.prop('disabled', true).html('Procesando...');

                $.post(self.url, $(this).serialize() + '&action=registrar', (res) => {
                    if (res.status === 'success') {
                        ocultarModalMovimientoBS();
                        console.log(res);

                        const nuevoId = parseInt(res.historial_capital_id ?? res.id ?? 0, 10) || 0;

                        Swal.fire({
                            icon: 'success',
                            title: '¡Éxito!',
                            text: res.message,
                            showConfirmButton: nuevoId > 0,
                            confirmButtonText: '📄 Ver detalle',
                            confirmButtonColor: '#007AFF',
                            showCancelButton: true,
                            cancelButtonText: 'Cerrar',
                            cancelButtonColor: '#6c757d'
                        }).then((result) => {
                            if (result.isConfirmed && nuevoId > 0 && typeof abrirModalDetalleMovimiento === 'function') {
                                $('#modalDetalleMovimiento').one('hidden.bs.modal', function () {
                                    location.reload();
                                });
                                abrirModalDetalleMovimiento(res.historial_capital_id);
                            } else {
                                location.reload();
                            }
                        });
                    } else {
                        Swal.fire('Error', res.message, 'error');
                    }
                }, 'json').fail(() => {
                    Swal.fire('Error', 'No se pudo conectar con el servidor.', 'error');
                }).always(() => {
                    $btn.prop('disabled', false).text('Confirmar Operación');
                });
            });
        },

        cargarCatalogos: function (almacenId) {
            const self = this;
            const fecha = $('#fecha_movimiento').val();

            $.getJSON(this.url, { action: 'listar', almacen_id: almacenId, fecha: fecha }, (res) => {
                if (res.status === 'success') {
                    const d = res.data;

                    self.saldosLocales.efectivo = parseFloat(d.monto_efectivo) || 0;
                    self.saldosLocales.tarjeta = parseFloat(d.monto_tarjeta) || 0;
                    self.saldosLocales.transferencia = parseFloat(d.monto_transferencia) || 0;

                    const fmt = (n) => '$' + n.toLocaleString('es-MX', { minimumFractionDigits: 2 });
                    $('#display_saldo_efectivo').text(fmt(self.saldosLocales.efectivo));
                    $('#display_saldo_tarjeta').text(fmt(self.saldosLocales.tarjeta));
                    $('#display_saldo_transferencia').text(fmt(self.saldosLocales.transferencia));
                }
            });

            $.getJSON(this.url, { action: 'catalogos_modal', almacen_id: almacenId }, (res) => {
                if (res.status === 'success') {
                    const cajas = (res.cajas_fuertes || [])
                        .map(c => `<option value="${c.id}">${c.nombre}</option>`).join('');
                    $('#sel_caja_fuerte').html(
                        '<option value="">Seleccione caja fuerte...</option>' + cajas
                    );

                    const bancos = (res.bancos || [])
                        .map(b => `<option value="${b.id_cuenta}">${b.nombre_cuenta}</option>`).join('');
                    $('#sel_banco').html(
                        '<option value="">Seleccione banco...</option>' + bancos
                    );
                }
            });
        },

        abrir: function () {
            $('#modalMovimiento').modal('show');
        }
    };

    $(document).ready(() => ModalMovimiento.init());
</script>