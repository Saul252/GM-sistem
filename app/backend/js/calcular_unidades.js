
function gestionarFactor() {
    const unidad = document.getElementById('unidadMaestra').value;
    const columnaFactor = document.getElementById('columnaFactor');
    const labelFactor = document.getElementById('labelFactor');
    const inputFactor = document.getElementById('inputFactor');

    if (unidad === 'ton') {
        columnaFactor.style.display = 'block';
        labelFactor.innerText = "¿Pzas por Tonelada?";
        inputFactor.value = 20; 
    } 
    else {
        columnaFactor.style.display = 'none';
        inputFactor.value = 1;
    }
    actualizarLimiteMaestro();
}

function actualizarLimiteMaestro() {
    const cant = parseFloat(document.getElementById('inputLlegadaMaestra').value) || 0;
    const unidad = document.getElementById('unidadMaestra').value;
    const factorInput = parseFloat(document.getElementById('inputFactor').value) || 1;

    let limiteFinal = 0;

    if (unidad === 'ton') {
        limiteFinal = cant * factorInput;
    } else if (unidad === 'kg') {
        limiteFinal = (factorInput > 0) ? (cant / factorInput) : 0;
    } else {
        limiteFinal = cant;
    }

    window.limiteGlobalPiezas = limiteFinal;
    document.getElementById('displayLimiteBultos').innerText = limiteFinal.toFixed(2);
    validarReparto();
}

function validarReparto() {
    let sumaAsignada = 0;
    document.querySelectorAll('.input-calculo').forEach(input => {
        sumaAsignada += parseFloat(input.value) || 0;
    });

    const displayAsignado = document.getElementById('displayAsignado');
    const displayRestante = document.getElementById('displayRestante');
    const btnGuardar = document.getElementById('btnGuardarProducto');
    const limite = window.limiteGlobalPiezas || 0;
    
    let restante = limite - sumaAsignada;
    
    displayAsignado.innerText = sumaAsignada.toFixed(2);
    displayRestante.innerText = restante.toFixed(2);

    // Validación de exceso
    if (sumaAsignada > (limite + 0.001) && limite > 0) {
        displayAsignado.classList.replace('text-primary', 'text-danger');
        btnGuardar.disabled = true;
        btnGuardar.classList.replace('btn-success', 'btn-danger');
        btnGuardar.innerHTML = '<i class="bi bi-exclamation-triangle-fill me-2"></i> EXCESO DE UNIDADES';
    } else {
        displayAsignado.classList.replace('text-danger', 'text-primary');
        btnGuardar.disabled = false;
        btnGuardar.classList.replace('btn-danger', 'btn-success');
        btnGuardar.innerHTML = '<i class="bi bi-save me-2"></i> GUARDAR PRODUCTO';
    }
}
