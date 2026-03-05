function abrirModalGasto() {
    $('#formNuevoGasto')[0].reset();
    // Limpia la tabla dejando solo la primera fila
    $('#tablaConceptosGasto tbody tr:not(:first)').remove();
    // Limpia los inputs de la primera fila
    $('#tablaConceptosGasto tbody tr:first input').val('');
    $('#tablaConceptosGasto tbody tr:first .cant').val(1);
    $('#tablaConceptosGasto tbody tr:first .precio').val('0.00');
    
    calcularGasto();
    $('#modalGasto').modal('show');
}

function agregarFilaGasto() {
    let html = `<tr>
        <td><input type="text" name="desc[]" class="form-control form-control-sm" required></td>
        <td><input type="number" name="cant[]" class="form-control form-control-sm cant" value="1" step="any" oninput="calcularGasto()"></td>
        <td><input type="number" name="precio[]" class="form-control form-control-sm precio" value="0.00" step="any" oninput="calcularGasto()"></td>
        <td class="text-end fw-bold subtotal_fila">$0.00</td>
        <td><button type="button" class="btn btn-sm btn-danger" onclick="$(this).closest('tr').remove(); calcularGasto();"><i class="bi bi-trash"></i></button></td>
    </tr>`;
    $('#tablaConceptosGasto tbody').append(html);
}

function calcularGasto() {
    let totalGral = 0;
    $('#tablaConceptosGasto tbody tr').each(function() {
        let cant = parseFloat($(this).find('.cant').val()) || 0;
        let precio = parseFloat($(this).find('.precio').val()) || 0;
        let subtotal = cant * precio;
        $(this).find('.subtotal_fila').text('$' + subtotal.toFixed(2));
        totalGral += subtotal;
    });
    $('#txtTotalGasto').text('$' + totalGral.toLocaleString('en-US', { minimumFractionDigits: 2 }));
    $('#inputTotalGasto').val(totalGral);

}
  // Envío del Formulario vía AJAX
    $('#formNuevoGasto').on('submit', function(e) {
        e.preventDefault();

        // 1. CREAR EL OBJETO FORMDATA
        var formData = new FormData(this);

        $.ajax({
            // Opción A: Ruta absoluta (la más segura)
url: '/cfsistem/app/controllers/egresosController.php?action=guardarCompraInventario',

// Opción B: Ruta relativa (subiendo niveles)
// url: '../../controllers/egresosController.php?action=guardarCompraInventario',
            type: 'POST',
            data: formData, // <--- Enviamos el objeto FormData
            contentType: false, // <--- IMPORTANTE: No dejar que jQuery ponga el content-type
            processData: false, // <--- IMPORTANTE: No dejar que jQuery procese los datos
            success: function(res) {
                if (res.success) {
                    Swal.fire('¡Éxito!', 'Gasto guardado', 'success').then(() => location.reload());
                } else {
                    Swal.fire('Error', res.message, 'error');
                }
            }
        });
    });
  