<style>
/* ── Widget Estado de Cuenta ─────────────────────────── */
#widgetEstadoCuenta {
    border-radius: 16px;
    overflow: hidden;
    margin-top: 10px;
    border: 1px solid rgba(0,0,0,0.06) !important;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
}

.widget-header-deuda {
    background: linear-gradient(135deg, #ff3b30 0%, #c0392b 100%);
    padding: 16px 18px;
    color: white;
}

.widget-header-ok {
    background: linear-gradient(135deg, #1d7a45 0%, #155d35 100%);
    padding: 16px 18px;
    color: white;
}

.widget-header-neutral {
    background: linear-gradient(135deg, #1c1c1e 0%, #2c2c2e 100%);
    padding: 16px 18px;
    color: white;
}

.widget-saldo-label {
    font-size: 0.6rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    opacity: 0.75;
    margin-bottom: 2px;
}

.widget-saldo-monto {
    font-size: 1.8rem;
    font-weight: 800;
    letter-spacing: -0.04em;
    line-height: 1;
}

.widget-update-time {
    font-size: 0.6rem;
    opacity: 0.6;
    margin-top: 4px;
}

.widget-body {
    background: #f8f9fb;
    max-height: 240px;
    overflow-y: auto;
    padding: 10px 12px;
}

.widget-body::-webkit-scrollbar { width: 3px; }
.widget-body::-webkit-scrollbar-thumb { background: #d1d1d6; border-radius: 4px; }

.mov-item {
    display: flex;
    align-items: center;
    gap: 10px;
    background: white;
    border-radius: 12px;
    padding: 10px 12px;
    margin-bottom: 7px;
    box-shadow: 0 1px 4px rgba(0,0,0,0.05);
    border: 1px solid rgba(0,0,0,0.04);
    transition: transform 0.15s;
}

.mov-item:last-child { margin-bottom: 0; }
.mov-item:hover { transform: translateX(2px); }

.mov-icon {
    width: 34px; height: 34px;
    border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1rem;
    flex-shrink: 0;
}

.mov-icon.cargo   { background: #fff1f0; color: #ff3b30; }
.mov-icon.abono   { background: #e6faea; color: #28a745; }

.mov-folio {
    font-size: 0.72rem;
    font-weight: 700;
    color: #1d1d1f;
}

.mov-obs {
    font-size: 0.62rem;
    color: #86868b;
    line-height: 1.3;
    margin-top: 1px;
}

.mov-fecha {
    font-size: 0.55rem;
    color: #aeaeb2;
    margin-top: 3px;
}

.mov-monto {
    font-size: 0.82rem;
    font-weight: 800;
    white-space: nowrap;
}

.mov-monto.cargo { color: #ff3b30; }
.mov-monto.abono { color: #28a745; }

.widget-footer {
    background: white;
    padding: 10px 12px;
    border-top: 1px solid rgba(0,0,0,0.05);
}

/* ── Ficha del cliente ───────────────────────────────── */
.ficha-cliente {
    background: #f8f9fb;
    border: 1px solid rgba(0,0,0,0.06);
    border-radius: 14px;
    padding: 12px 14px;
    margin-bottom: 12px;
}

.ficha-label {
    font-size: 0.55rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: #aeaeb2;
    margin-bottom: 2px;
}

.ficha-valor {
    font-size: 0.8rem;
    font-weight: 600;
    color: #1d1d1f;
}

/* ── Bloque de pago ──────────────────────────────────── */
.pago-block {
    background: #f0faf4;
    border: 1px solid rgba(40,167,69,0.2);
    border-radius: 14px;
    padding: 14px;
    margin-bottom: 12px;
}

.pago-block .pago-title {
    font-size: 0.65rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: #1d7a45;
    margin-bottom: 10px;
}
</style>

<div class="modal fade" id="modalFinalizarVenta" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">

            <div class="modal-header bg-dark text-white py-3">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-receipt-cutoff me-2"></i>Finalizar Transacción
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body p-0">
                <div class="row g-0">

                    <!-- ── Columna izquierda: detalle de productos ── -->
                    <div class="col-lg-7 p-4 border-end">
                        <h6 class="text-uppercase fw-bold mb-3 text-primary" style="font-size:0.68rem;letter-spacing:0.08em;">
                            Detalle de Salida de Material
                        </h6>
                        <div class="table-responsive border rounded-3 bg-white mb-3" style="max-height: 320px;">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light sticky-top">
                                    <tr class="small text-uppercase text-muted">
                                        <th class="ps-3">Producto</th>
                                        <th class="text-center">Venta</th>
                                        <th class="text-center">Entregar Hoy</th>
                                        <th class="text-end pe-3">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody id="tablaConfirmacion"></tbody>
                            </table>
                        </div>

                        <!-- Total -->
                        <div style="background:#eff6ff;border-radius:14px;padding:14px 18px;text-align:right;">
                            <input type="hidden" id="descuentoGeneral" value="0">
                            <div style="font-size:0.6rem;font-weight:700;text-transform:uppercase;letter-spacing:0.08em;color:#6b7280;margin-bottom:2px;">
                                Total a Cobrar
                            </div>
                            <div style="font-size:2rem;font-weight:800;color:#0071e3;letter-spacing:-0.04em;line-height:1;">
                                $<span id="totalFinalModal">0.00</span>
                            </div>
                        </div>
                    </div>

                    <!-- ── Columna derecha: cliente + pago ── -->
                    <div class="col-lg-5 p-4" style="background:#fafafa;">

                        <h6 class="text-uppercase fw-bold mb-3 text-primary" style="font-size:0.68rem;letter-spacing:0.08em;">
                            Información del Cliente
                        </h6>

                        <!-- Selector de cliente -->
                        <div class="d-flex gap-2 mb-2">
                            <select id="selectCliente" class="form-select border-primary" style="border-radius:10px;">
                                <?php foreach($clientes as $c):
                                    $almacen_usuario = $_SESSION['almacen_id'] ?? 0;
                                    $esAdmin         = ($almacen_usuario == 0);
                                    $esSuAlmacen     = ($c['almacen_id'] == $almacen_usuario);
                                    $esGlobal        = (is_null($c['almacen_id']) || $c['almacen_id'] == '');
                                    $esPublicoGeneral= ($c['rfc'] === 'XAXX010101000');
                                    if ($esAdmin || $esSuAlmacen || $esGlobal || $esPublicoGeneral):
                                ?>
                                <option value="<?= $c['id'] ?>"
                                    data-rfc="<?= $c['rfc'] ?>"
                                    data-rs="<?= $c['razon_social'] ?>"
                                    data-cp="<?= $c['codigo_postal'] ?>"
                                    data-regimen="<?= $c['regimen_fiscal'] ?>">
                                    <?= htmlspecialchars($c['nombre_comercial']) ?>
                                </option>
                                <?php endif; endforeach; ?>
                            </select>
                            <button class="btn btn-outline-primary flex-shrink-0" type="button"
                                onclick="abrirModalNuevoCliente()" style="border-radius:10px;">
                                <i class="bi bi-person-plus"></i>
                            </button>
                        </div>

                        <!-- Widget Estado de Cuenta — FUERA del input-group -->
                        <div id="widgetEstadoCuenta" style="display:none;">
                            <!-- Header dinámico (se reemplaza por JS) -->
                            <div id="widgetHeader" class="widget-header-neutral">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <div class="widget-saldo-label">
                                            <i class="bi bi-wallet2 me-1"></i>Estado de Cuenta
                                        </div>
                                        <div class="widget-saldo-monto" id="lblSaldoTotal">$0.00</div>
                                    </div>
                                    <span id="txtUltimaCarga" class="widget-update-time"></span>
                                </div>
                                <div id="widgetBadge" class="mt-2"></div>
                            </div>

                            <!-- Lista de movimientos -->
                            <div class="widget-body" id="listaMovimientos">
                                <div class="text-center py-4 text-muted small">
                                    <div class="spinner-border spinner-border-sm"></div>
                                </div>
                            </div>

                            <!-- Footer con botón de abono -->
                            <div class="widget-footer">
                                <button class="btn btn-dark w-100 fw-bold rounded-pill"
                                    style="font-size:0.78rem;padding:8px;" onclick="abrirModalAbono()">
                                    <i class="bi bi-plus-circle me-1"></i> Registrar Abono
                                </button>
                            </div>
                        </div>

                        <!-- Ficha fiscal del cliente -->
                        <div class="ficha-cliente mt-3">
                            <div class="row g-2">
                                <div class="col-12">
                                    <div class="ficha-label">Razón Social</div>
                                    <div class="ficha-valor text-truncate" id="f_razon_social">---</div>
                                </div>
                                <div class="col-6">
                                    <div class="ficha-label">RFC</div>
                                    <div class="ficha-valor" id="f_rfc">---</div>
                                </div>
                                <div class="col-6">
                                    <div class="ficha-label">Régimen</div>
                                    <span id="f_regimen" class="badge bg-info text-dark">---</span>
                                </div>
                            </div>
                        </div>

                        <!-- Bloque de pago -->
                        <div class="pago-block">
                            <div class="pago-title"><i class="bi bi-cash-coin me-1"></i>Registro de Pago</div>
                            <div class="row g-2">
                                <div class="col-7">
                                    <label class="form-label small fw-bold mb-1">Monto Recibido</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-success text-white border-success fw-bold">$</span>
                                        <input type="number" id="monto_pagar"
                                            class="form-control border-success fw-bold text-success"
                                            value="0" step="0.01" min="0" style="border-radius:0 8px 8px 0;">
                                    </div>
                                </div>
                                <div class="col-5">
                                    <label class="form-label small fw-bold mb-1">Método</label>
                                    <select id="metodo_pago" class="form-select border-success" style="border-radius:8px;">
                                        <option value="Efectivo">Efectivo</option>
                                        <option value="Transferencia">Transferencia</option>
                                        <option value="Tarjeta">Tarjeta</option>
                                    </select>
                                </div>
                            </div>
                            <div id="pago_aviso" class="small mt-2 text-center fw-bold"></div>
                        </div>

                        <textarea id="obsVenta" class="form-control" rows="2"
                            placeholder="Notas adicionales..."
                            style="border-radius:10px;font-size:0.85rem;"></textarea>
                    </div>

                </div>
            </div>

            <div class="modal-footer bg-light border-0" style="border-radius:0 0 20px 20px;">
                <button class="btn btn-link text-muted me-auto" data-bs-dismiss="modal">Cerrar</button>
                <button class="btn btn-success btn-lg px-5 shadow fw-bold rounded-pill" onclick="procesarVenta()">
                    <i class="bi bi-check-circle-fill me-1"></i> FINALIZAR VENTA
                </button>
            </div>

        </div>
    </div>
</div>

<script>
    document.getElementById('selectCliente').addEventListener('change', function() {
        const selected = this.options[this.selectedIndex];
        
        // 1. Actualizar textos de la ficha fiscal
        document.getElementById('f_rfc').textContent = selected?.dataset.rfc || '---';
        document.getElementById('f_razon_social').textContent = selected?.dataset.rs || '---';
        document.getElementById('f_regimen').textContent = selected?.dataset.regimen || '---';

        // 2. Ejecutar consulta de estatus financiero
        const idCliente = this.value;
        if (idCliente) {
            consultarEstatusFinanciero(idCliente);
        }
    });

    // Función para realizar la petición al servidor
  function consultarEstatusFinanciero(id) {
    const $widget = document.getElementById('widgetEstadoCuenta');
    const $lista = document.getElementById('listaMovimientos');
    const $header = document.getElementById('widgetHeader');
    const _fmt = new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' });

    if (!$widget || !$lista) return;

    $widget.style.display = 'block';
    $lista.innerHTML = `<div class="text-center py-3"><div class="spinner-border spinner-border-sm text-secondary"></div></div>`;

    fetch(`/cfsistem/app/controllers/ventasController.php?action=obtenerEstatusCliente&id=${id}`)
        .then(r => r.json())
        .then(data => {
            // Ajuste: Ahora data es el objeto directo que devuelve obtenerEstatus
            if (!data || data.nombre_comercial === undefined) throw new Error("Datos no encontrados");

            const res = data; 
            const saldo = parseFloat(res.saldo_neto || 0); // (saldo_en_contra - saldo_a_favor)
            const condicion = res.estatus_financiero || 'AL DIA';

            // --- Lógica de Colores del Header (Sin romper tus clases CSS) ---
            $header.className = ''; // Limpiamos para evitar duplicados
            if (condicion === 'CON DEUDA') {
                $header.classList.add('widget-header-deuda');
            } else if (condicion === 'SALDO A FAVOR') {
                $header.classList.add('widget-header-ok'); 
            } else {
                $header.classList.add('widget-header-neutral');
            }

            // Actualizar montos principales
            document.getElementById('lblSaldoTotal').textContent = _fmt.format(Math.abs(saldo));
            document.getElementById('txtUltimaCarga').textContent = `Corte: ${new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}`;

            // Badge dinámico con nuevos iconos para Saldo a Favor
            let icon = 'bi-check-circle-fill';
            if (condicion === 'CON DEUDA') icon = 'bi-exclamation-triangle-fill';
            else if (condicion === 'SALDO A FAVOR') icon = 'bi-plus-circle-fill';

            document.getElementById('widgetBadge').innerHTML = `
                <span style="background:rgba(255,255,255,0.2);color:white;font-size:0.6rem;font-weight:700;padding:3px 10px;border-radius:20px;">
                    <i class="bi ${icon} me-1"></i>${condicion}
                </span>`;

            // Resumen en el cuerpo: Ajustado a los campos de la tabla maestra
            $lista.innerHTML = `
                <div class="p-2 small">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="text-muted">Por Pagar:</span>
                        <span class="fw-bold text-danger">${_fmt.format(res.saldo_en_contra || 0)}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="text-muted">A Favor:</span>
                        <span class="fw-bold text-success">${_fmt.format(res.saldo_a_favor || 0)}</span>
                    </div>
                    <hr class="my-1" style="opacity:0.1">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted">Estado:</span>
                        <span class="fw-bold ${saldo > 0 ? 'text-danger' : 'text-success'}">
                            ${condicion === 'CON DEUDA' ? 'Pendiente de Pago' : (condicion === 'SALDO A FAVOR' ? 'Crédito Disponible' : 'Sin Adeudos')}
                        </span>
                    </div>
                </div>`;
        })
        .catch(err => {
            console.error("Error:", err);
            $lista.innerHTML = `<div class="text-center p-2 text-danger small">Error al consultar estatus</div>`;
        });
}
    document.addEventListener('DOMContentLoaded', () => {
        const select = document.getElementById('selectCliente');
        // Esto dispara el cambio inicial para el cliente seleccionado por defecto
        if (select) select.dispatchEvent(new Event('change'));

        // También forzamos el disparo cuando el modal de Bootstrap termina de abrirse
        const modal = document.getElementById('modalFinalizarVenta');
        if (modal) {
            modal.addEventListener('shown.bs.modal', () => {
                if (select) select.dispatchEvent(new Event('change'));
            });
        }
    });
</script>