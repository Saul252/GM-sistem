<!-- =========================================================
MODAL CREAR MEDIDA ADICIONAL
========================================================= -->

<style>

#modalMedidaAdicional{
    z-index:99999 !important;
}
.miSwalZ{
    z-index: 999999 !important;
}

.swal2-container{
    z-index: 999999 !important;
}


#modalMedidaAdicional .modal-content{
    border:none;
    border-radius:22px;
    overflow:hidden;
    box-shadow:0 18px 45px rgba(0,0,0,.18);
}

#modalMedidaAdicional .modal-header{
    background:linear-gradient(135deg,#111827,#1f2937);
    border:none;
}

#modalMedidaAdicional .form-control{
    height:48px;
    border-radius:14px;
    border:1px solid #dbe2ea;
}

#modalMedidaAdicional .form-control:focus{
    border-color:#111827;
    box-shadow:0 0 0 .2rem rgba(17,24,39,.12);
}

#modalMedidaAdicional .formula-box{
    background:#f8fafc;
    border:1px dashed #cbd5e1;
    border-radius:18px;
    padding:18px;
}

#equivalencia{
    background:#fff8e1;
    font-size:1.1rem;
    font-weight:700;
    text-align:center;
}

.tipo-card{
    border:1px solid #dbe2ea;
    border-radius:16px;
    padding:14px;
    cursor:pointer;
    transition:.2s ease;
    background:#fff;
}

.tipo-card:hover{
    border-color:#111827;
    transform:translateY(-1px);
}

.tipo-card input{
    transform:scale(1.2);
}

</style>

<div class="modal fade"
     id="modalMedidaAdicional"
     tabindex="-1"
     aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content">

            <!-- HEADER -->
            <div class="modal-header text-white p-4">

                <div>

                    <h5 class="modal-title fw-bold mb-1">

                        <i class="bi bi-rulers me-2"></i>
                        Nueva Medida

                    </h5>

                    <small id="infoProductoModal"
                           class="text-white-50">

                        Configura equivalencia

                    </small>

                </div>

                <button type="button"
                        class="btn-close btn-close-white"
                        data-bs-dismiss="modal">
                </button>

            </div>

            <!-- FORM -->
            <form id="formMedidaAdicional">

                <input type="hidden"
                       name="producto_id"
                       id="id_producto_crear">

                <input type="hidden"
                       name="almacen_id"
                       id="id_almacen_crear">

                <!-- BODY -->
                <div class="modal-body p-4">

                    <!-- NOMBRE -->
                    <div class="mb-4">

                        <label class="form-label fw-semibold small text-uppercase text-muted">

                            Nombre de la nueva unidad

                        </label>

                        <input type="text"
                               name="nombre"
                               id="nombreNuevaUnidad"
                               class="form-control"
                               placeholder="Ej: Caja, Gramo, Tonelada"
                               required>

                    </div>

                    <!-- TIPO -->
                    <div class="mb-4">

                        <label class="form-label fw-semibold small text-uppercase text-muted mb-3">

                            Tipo de conversión

                        </label>

                        <div class="row g-3">

                            <!-- MÁS GRANDE -->
                            <div class="col-md-6">

                                <label class="tipo-card w-100">

                                    <div class="d-flex align-items-center gap-2">

                                        <input type="radio"
                                               name="tipoConversion"
                                               value="grande"
                                               checked>

                                        <div>

                                            <div class="fw-bold">

                                                Unidad MÁS GRANDE

                                            </div>

                                            <small class="text-muted">

                                                Ej: Tonelada

                                            </small>

                                        </div>

                                    </div>

                                </label>

                            </div>

                            <!-- MÁS PEQUEÑA -->
                            <div class="col-md-6">

                                <label class="tipo-card w-100">

                                    <div class="d-flex align-items-center gap-2">

                                        <input type="radio"
                                               name="tipoConversion"
                                               value="pequena">

                                        <div>

                                            <div class="fw-bold">

                                                Unidad MÁS PEQUEÑA

                                            </div>

                                            <small class="text-muted">

                                                Ej: Gramo

                                            </small>

                                        </div>

                                    </div>

                                </label>

                            </div>

                        </div>

                    </div>

                    <!-- FORMULA -->
                    <div class="formula-box">

                        <div class="mb-3">

                            <label class="form-label fw-semibold small text-uppercase text-muted">

                                Conversión

                            </label>

                            <input type="number"
                                   id="cantidadConversion"
                                   class="form-control text-center fw-bold"
                                   step="0.0001"
                                   min="0"
                                   placeholder="Escribe una cantidad">

                        </div>

                        <!-- TEXTO -->
                        <div class="alert alert-light border text-center mb-3">

                            <span id="textoFormula"
                                  class="fw-semibold">

                                Fórmula de conversión

                            </span>

                        </div>

                        <!-- RESULTADO -->
                        <div>

                            <label class="form-label fw-semibold small text-uppercase text-muted">

                                Equivalencia calculada

                            </label>

                            <input type="number"
                                   id="equivalencia"
                                   name="equivalencia"
                                   class="form-control"
                                   step="0.001"
                                   readonly>

                        </div>

                    </div>

                    <!-- EJEMPLO -->
                    <div class="alert alert-warning border mt-4 mb-0">

                        <small id="ejemploConversion">

                            Esperando datos...

                        </small>

                    </div>

                </div>

                <!-- FOOTER -->
                <div class="modal-footer border-0 bg-white px-4 pb-4">

                    <button type="button"
                            class="btn btn-light rounded-pill px-4"
                            data-bs-dismiss="modal">

                        Cancelar

                    </button>

                    <button type="submit"
                            class="btn btn-dark rounded-pill px-5">

                        Guardar

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

<script>

// =====================================================
// 🔥 VARIABLES
// =====================================================

let unidadBaseActual = 'Unidad';

// =====================================================
// 🔥 ABRIR MODAL
// =====================================================

window.prepararNuevaMedida = function (

    idProducto,
    idAlmacen,
    nombreProducto,
    unidadBase

){

    unidadBaseActual = unidadBase;

    document.getElementById(
        'id_producto_crear'
    ).value = idProducto;

    document.getElementById(
        'id_almacen_crear'
    ).value = idAlmacen;

    document.getElementById(
        'infoProductoModal'
    ).innerText =
        `Producto: ${nombreProducto}`;

    document.getElementById(
        'cantidadConversion'
    ).value = '';

    document.getElementById(
        'equivalencia'
    ).value = '';

    document.getElementById(
        'nombreNuevaUnidad'
    ).value = '';

    actualizarFormula();

    const modal =
        bootstrap.Modal.getOrCreateInstance(
            document.getElementById(
                'modalMedidaAdicional'
            )
        );

    modal.show();
};

// =====================================================
// 🔥 ACTUALIZAR FORMULA
// =====================================================

function actualizarFormula(){

    const tipo =
        document.querySelector(
            'input[name="tipoConversion"]:checked'
        ).value;

    const cantidad =
        parseFloat(
            document.getElementById(
                'cantidadConversion'
            ).value
        ) || 0;

    const nuevaUnidad =
        document.getElementById(
            'nombreNuevaUnidad'
        ).value || 'Nueva Unidad';

    const texto =
        document.getElementById(
            'textoFormula'
        );

    const equivalencia =
        document.getElementById(
            'equivalencia'
        );

    const ejemplo =
        document.getElementById(
            'ejemploConversion'
        );

    // =================================================
    // 🔥 MÁS GRANDE
    // 1000 KG = 1 TON
    // equivalencia = 0.001
    // =================================================

    if(tipo === 'grande'){

        texto.innerHTML = `
            ${cantidad || '?'} ${unidadBaseActual}
            caben en
            1 ${nuevaUnidad}
        `;

        if(cantidad > 0){

            equivalencia.value =
                (1 / cantidad).toFixed(8);

            ejemplo.innerHTML = `
                ${cantidad} ${unidadBaseActual}
                = 1 ${nuevaUnidad}
                <br>
                Entonces:
                1 ${unidadBaseActual}
                =
                ${(1 / cantidad).toFixed(8)}
                ${nuevaUnidad}
            `;
        }
    }

    // =================================================
    // 🔥 MÁS PEQUEÑA
    // 1 KG = 1000 GR
    // equivalencia = 1000
    // =================================================

    else{

        texto.innerHTML = `
            1 ${unidadBaseActual}
            contiene
            ${cantidad || '?'}
            ${nuevaUnidad}
        `;

        if(cantidad > 0){

            equivalencia.value =
                cantidad.toFixed(8);

            ejemplo.innerHTML = `
                1 ${unidadBaseActual}
                =
                ${cantidad}
                ${nuevaUnidad}
            `;
        }
    }
}

// =====================================================
// 🔥 EVENTOS
// =====================================================

document
.getElementById('cantidadConversion')
.addEventListener('input', actualizarFormula);

document
.getElementById('nombreNuevaUnidad')
.addEventListener('input', actualizarFormula);

document
.querySelectorAll(
    'input[name="tipoConversion"]'
)
.forEach(radio => {

    radio.addEventListener(
        'change',
        actualizarFormula
    );

});

// =====================================================
// 🔥 GUARDAR
// =====================================================

document
.getElementById('formMedidaAdicional')
.addEventListener('submit', async function(e){

    e.preventDefault();

    try{

        Swal.fire({
            title:'Guardando...',
            allowOutsideClick:false,
            didOpen:()=>Swal.showLoading()
        });

        const formData =
            new FormData(this);

        const resp = await fetch(

            '/cfsistem/app/controllers/productosController.php?action=guardarOpcionMedida',

            {
                method:'POST',
                body:formData
            }
        );

        const data = await resp.json();

        Swal.close();

        if(data.success || data.status === 'success'){

            await Swal.fire({

                icon:'success',
                title:'Guardado',
                text:'Medida agregada correctamente',
                timer:1500,
                showConfirmButton:false,
                 customClass: {
                         popup: 'miSwalZ'
                    }
            });

            bootstrap.Modal
            .getInstance(
                document.getElementById(
                    'modalMedidaAdicional'
                )
            )
            .hide();

            document
            .getElementById(
                'formMedidaAdicional'
            )
            .reset();

            if(typeof recargarModalMedidas === 'function'){
                recargarModalMedidas();
            }

        }else{

            Swal.fire({
                icon:'error',
                title:'Error',
                text:data.message ||
                     'No se pudo guardar',
                      customClass: {
                         popup: 'miSwalZ'
                    }
            });
        }

    }catch(error){

        console.error(error);

        Swal.fire({
            icon:'error',
            title:'Error',
            text:'Falló la comunicación con el servidor'
        });
    }
});

</script>