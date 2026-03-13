<div class="card card-table">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0"><i class="fas fa-exchange-alt"></i> Nueva Transmutación</h5>
    </div>
    <div class="card-body">
        <form id="formTransmutacion" action="/cfsistem/app/controllers/transmutacionesController.php?action=guardar" method="POST">
            
            <div class="row g-3">
                <div class="col-md-12">
                    <h6 class="text-primary border-bottom pb-2">1. Producto Origen (Lo que se va a transformar)</h6>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">Almacén</label>
                    <select name="almacen_id" id="trans_almacen" class="form-select" required>
                        <option value="">Seleccione...</option>
                        <?php foreach ($almacenes as $a): ?>
                            <option value="<?= $a['id'] ?>"><?= htmlspecialchars($a['nombre']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">Producto Origen</label>
                    <select name="producto_origen_id" id="trans_producto_origen" class="form-select" disabled required>
                        <option value="">Seleccione almacén</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">Lote Origen</label>
                    <select name="lote_origen_id" id="trans_lote_origen" class="form-select" disabled required>
                        <option value="">Seleccione producto</option>
                    </select>
                </div>

                <div class="col-md-12 mt-4">
                    <h6 class="text-success border-bottom pb-2">2. Producto Destino (El resultado)</h6>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Convertir a:</label>
                    <select name="producto_destino_id" id="trans_producto_destino" class="form-select" disabled required>
                        <option value="">Seleccione producto origen</option>
                    </select>
                    <div id="info_conversion" class="form-text text-primary"></div>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">Cantidad Origen</label>
                    <input type="number" step="0.01" name="cantidad_origen" id="trans_cant_origen" class="form-control" placeholder="Ej: 1 bulto" required>
                    <div class="form-text">Disponible: <span id="trans_stock_disp">0</span></div>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">Cantidad Destino (Real)</label>
                    <input type="number" step="0.01" name="cantidad_destino" id="trans_cant_destino" class="form-control" placeholder="Ej: 24 kg" required>
                    <div class="form-text text-danger" id="alerta_merma"></div>
                </div>

                <div class="col-md-12">
                    <label class="form-label fw-bold">Observaciones / Motivo de la conversión</label>
                    <textarea name="observaciones" class="form-control" rows="2" placeholder="Ej: Se rompió bulto, conversión a granel..."></textarea>
                </div>
            </div>

            <div class="mt-4 text-end">
                <button type="button" class="btn btn-secondary" onclick="resetTransForm()">Limpiar</button>
                <button type="submit" class="btn btn-success btn-lg">
                    <i class="fas fa-sync"></i> Procesar Transmutación
                </button>
            </div>
        </form>
    </div>
</div>
<script>
    // --- LÓGICA DE TRANSMUTACIONES ---
const transAlmacen = document.getElementById('trans_almacen');
const transProdOrigen = document.getElementById('trans_producto_origen');
const transLoteOrigen = document.getElementById('trans_lote_origen');
const transProdDestino = document.getElementById('trans_producto_destino');
const transCantOrigen = document.getElementById('trans_cant_origen');
const transCantDestino = document.getElementById('trans_cant_destino');
const infoConversion = document.getElementById('info_conversion');

// 1. Cargar productos origen al cambiar almacén
transAlmacen.addEventListener('change', async function() {
    if(!this.value) return;
    const res = await fetch(`${baseUrl}?action=obtenerProductosAlmacen&almacen_id=${this.value}`);
    const data = await res.json();
    
    transProdOrigen.innerHTML = '<option value="">Seleccione origen</option>';
    data.forEach(p => transProdOrigen.add(new Option(`${p.sku} - ${p.nombre}`, p.id)));
    transProdOrigen.disabled = false;
});

// 2. Cargar lotes Y productos destino compatibles
transProdOrigen.addEventListener('change', async function() {
    const pId = this.value;
    const aId = transAlmacen.value;
    if(!pId) return;

    // Cargar Lotes
    const resLotes = await fetch(`${baseUrl}?action=obtenerLotes&producto_id=${pId}&almacen_id=${aId}`);
    const lotes = await resLotes.json();
    transLoteOrigen.innerHTML = '<option value="">Seleccione lote</option>';
    lotes.forEach(l => {
        const opt = new Option(`${l.codigo_lote} (Disp: ${l.cantidad_actual})`, l.id);
        opt.dataset.stock = l.cantidad_actual;
        transLoteOrigen.add(opt);
    });
    transLoteOrigen.disabled = false;

    // Cargar Destinos Compatibles (Desde la nueva tabla config_transmutaciones)
    const resDest = await fetch(`${baseUrl}?action=obtenerDestinosCompatibles&producto_id=${pId}`);
    const destinos = await resDest.json();
    transProdDestino.innerHTML = '<option value="">Seleccione destino</option>';
    destinos.forEach(d => {
        const opt = new Option(d.nombre, d.id);
        opt.dataset.factor = d.factor_rendimiento_teorico;
        transProdDestino.add(opt);
    });
    transProdDestino.disabled = false;
});

// 3. Cálculo sugerido de cantidad
transCantOrigen.addEventListener('input', calcularSugerido);
transProdDestino.addEventListener('change', calcularSugerido);

function calcularSugerido() {
    const factor = parseFloat(transProdDestino.selectedOptions[0]?.dataset.factor || 0);
    const cantOrig = parseFloat(transCantOrigen.value || 0);
    if(factor && cantOrig) {
        const sugerido = (factor * cantOrig).toFixed(2);
        infoConversion.textContent = `Rendimiento teórico: ${sugerido} unidades destino.`;
        transCantDestino.placeholder = `Sugerido: ${sugerido}`;
    }
}
</script>