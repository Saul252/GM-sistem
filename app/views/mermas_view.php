
    
    <?php if (function_exists('cargarEstilos')) { cargarEstilos(); } ?>

    
    <?php if (function_exists('renderizarLayout')) {
        renderizarLayout($paginaActual); 
    } ?>
<div class="container mt-4">

    <h3 class="mb-4">Control de Mermas</h3>

    <button class="btn btn-danger mb-3" data-bs-toggle="modal" data-bs-target="#modalMerma">
        Registrar Merma
    </button>

    <button class="btn btn-warning mb-3" data-bs-toggle="modal" data-bs-target="#modalConversion">
        Convertir Producto
    </button>


    <table class="table table-bordered table-striped">

        <thead class="table-dark">

            <tr>
                <th>Fecha</th>
                <th>Almacén</th>
                <th>Producto</th>
                <th>Cantidad</th>
                <th>Tipo</th>
                <th>Responsable</th>
                <th>Descripción</th>
            </tr>

        </thead>

        <tbody>

            <?php while($m = $mermas->fetch_assoc()){ ?>

            <tr>

                <td><?= $m['fecha_reporte'] ?></td>
                <td><?= $m['almacen'] ?></td>
                <td><?= $m['producto'] ?></td>
                <td><?= $m['cantidad'] ?></td>
                <td><?= $m['tipo_merma'] ?></td>
                <td><?= $m['responsable_declaracion'] ?></td>
                <td><?= $m['descripcion_suceso'] ?></td>

            </tr>

            <?php } ?>

        </tbody>

    </table>

</div>


<!-- MODAL MERMA SIMPLE -->

<div class="modal fade" id="modalMerma">

    <div class="modal-dialog">

        <div class="modal-content">

            <div class="modal-header">
                <h5>Registrar Merma</h5>
            </div>

            <div class="modal-body">

                <form id="formMerma">

                    <input type="hidden" name="almacen_id" value="<?= $almacen_id ?>">

                    <div class="mb-3">

                        <label>Producto</label>

                        <select name="producto_id" id="productoMerma" class="form-control">

                            <option value="">Seleccione</option>

                            <?php while($p = $productos->fetch_assoc()){ ?>

                            <option value="<?= $p['id'] ?>">

                                <?= $p['nombre'] ?> (Stock: <?= $p['stock'] ?>)

                            </option>

                            <?php } ?>

                        </select>

                    </div>

                    <div class="mb-3">

                        <label>Cantidad</label>

                        <input type="number" step="0.01" name="cantidad" class="form-control">

                    </div>

                    <div class="mb-3">

                        <label>Tipo Merma</label>

                        <select name="tipo_merma" class="form-control">

                            <option value="robo">Robo</option>
                            <option value="daño">Daño</option>
                            <option value="caducidad">Caducidad</option>
                            <option value="otro">Otro</option>

                        </select>

                    </div>

                    <div class="mb-3">

                        <label>Responsable</label>

                        <input type="text" name="responsable" class="form-control">

                    </div>

                    <div class="mb-3">

                        <label>Descripción</label>

                        <textarea name="descripcion" class="form-control"></textarea>

                    </div>

                    <button class="btn btn-danger w-100">

                        Registrar

                    </button>

                </form>

            </div>

        </div>

    </div>

</div>



<!-- MODAL CONVERSION -->

<div class="modal fade" id="modalConversion">

    <div class="modal-dialog">

        <div class="modal-content">

            <div class="modal-header">

                <h5>Conversión de Producto</h5>

            </div>

            <div class="modal-body">

                <form id="formConversion">

                    <input type="hidden" name="almacen_id" value="<?= $almacen_id ?>">

                    <div class="mb-3">

                        <label>Producto Origen</label>

                        <select name="producto_origen" id="productoOrigen" class="form-control">

                            <option value="">Seleccione</option>

                            <?php
$productos->data_seek(0);
while($p = $productos->fetch_assoc()){
?>

                            <option value="<?= $p['id'] ?>" data-factor="<?= $p['factor_conversion'] ?>">

                                <?= $p['nombre'] ?> (Stock <?= $p['stock'] ?>)

                            </option>

                            <?php } ?>

                        </select>

                    </div>


                    <div class="mb-3">

                        <label>Cantidad Origen</label>

                        <input type="number" step="0.01" id="cantidadOrigen" name="cantidad_origen"
                            class="form-control">

                    </div>


                    <div class="mb-3">

                        <label>Producto Destino</label>

                        <select name="producto_destino" class="form-control">

                            <?php
$productos->data_seek(0);
while($p = $productos->fetch_assoc()){
?>

                            <option value="<?= $p['id'] ?>">

                                <?= $p['nombre'] ?>

                            </option>

                            <?php } ?>

                        </select>

                    </div>


                    <div class="mb-3">

                        <label>Cantidad Destino</label>

                        <input type="number" step="0.01" id="cantidadDestino" name="cantidad_destino"
                            class="form-control">

                        <small id="maxPermitido" class="text-danger"></small>

                    </div>


                    <div class="mb-3">

                        <label>Responsable</label>

                        <input type="text" name="responsable" class="form-control">

                    </div>


                    <div class="mb-3">

                        <label>Descripción</label>

                        <textarea name="descripcion" class="form-control"></textarea>

                    </div>


                    <button class="btn btn-warning w-100">

                        Convertir

                    </button>

                </form>

            </div>

        </div>

    </div>

</div>



<script>
/* CALCULAR FACTOR */

document.getElementById("productoOrigen").addEventListener("change", function() {

    let factor = this.options[this.selectedIndex].dataset.factor;

    document.getElementById("productoOrigen").dataset.factor = factor;

});


document.getElementById("cantidadOrigen").addEventListener("input", function() {

    let origen = this.value;

    let factor = document.getElementById("productoOrigen").dataset.factor;

    let maximo = origen * factor;

    document.getElementById("maxPermitido").innerText = "Máximo permitido: " + maximo;

});


/* GUARDAR MERMA */

document.getElementById("formMerma").addEventListener("submit", function(e) {

    e.preventDefault();

    let form = new FormData(this);

    fetch("../../controllers/MermaController.php?action=guardar", {

            method: "POST",
            body: form

        })

        .then(r => r.json())
        .then(data => {

            if (data.success) {

                location.reload();

            } else {

                alert(data.error);

            }

        });

});


/* CONVERTIR PRODUCTO */

document.getElementById("formConversion").addEventListener("submit", function(e) {

    e.preventDefault();

    let origen = parseFloat(document.getElementById("cantidadOrigen").value);

    let destino = parseFloat(document.getElementById("cantidadDestino").value);

    let factor = document.getElementById("productoOrigen").dataset.factor;

    let maximo = origen * factor;

    if (destino > maximo) {

        alert("Excede el máximo permitido");

        return;

    }

    let form = new FormData(this);

    fetch("../../controllers/MermaController.php?action=convertir", {

            method: "POST",
            body: form

        })

        .then(r => r.json())
        .then(data => {

            if (data.success) {

                location.reload();

            } else {

                alert(data.error);

            }

        });

});
</script>