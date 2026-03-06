<script>
// PHP le pasa estos valores a JS una sola vez al cargar la página
const USER_ALMACEN_ID = <?= json_encode($_SESSION['almacen_id']) ?>;
const ES_ADMIN = <?= ($_SESSION['rol_id'] == 1) ? 'true' : 'false' ?>;
</script>
<style>
:root {
    --mac-border: #d1d1d6;
    --mac-accent: #007aff; /* Azul clásico de Apple */
    --mac-bg: #ffffff;
}

/* Contenedor Principal */
.mac-select-container {
    padding: 12px;
    background: rgba(255, 255, 255, 0.7);
    backdrop-filter: blur(10px); /* Efecto cristal de Mac */
    border-radius: 12px;
    border: 1px solid var(--mac-border);
    transition: all 0.3s ease;
}

/* Etiqueta */
.mac-label {
    font-size: 11px;
    font-weight: 600;
    color: #8e8e93;
    text-transform: uppercase;
    letter-spacing: 0.8px;
    margin-bottom: 8px;
    display: block;
}

/* El Selector */
.select-wrapper {
    position: relative;
    display: flex;
    align-items: center;
}

.mac-select {
    width: 100%;
    appearance: none; /* Quitamos la flecha fea por defecto */
    background: var(--mac-bg);
    border: 1px solid var(--mac-border);
    border-radius: 8px;
    padding: 8px 30px 8px 12px;
    font-size: 14px;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    color: #1d1d1f;
    cursor: pointer;
    transition: border-color 0.2s, box-shadow 0.2s;
}

/* Borde de color elegante al hacer foco */
.mac-select.admin-active:focus {
    outline: none;
    border-color: var(--mac-accent);
    box-shadow: 0 0 0 4px rgba(0, 122, 255, 0.15);
}

/* Estado bloqueado para usuarios */
.mac-select.user-locked:disabled {
    background-color: #f5f5f7;
    border-color: #d1d1d6;
    color: #86868b;
    cursor: default;
    border-left: 4px solid var(--mac-accent); /* Acento de color lateral */
}

/* Flecha personalizada */
.custom-arrow {
    position: absolute;
    right: 12px;
    font-size: 10px;
    color: #8e8e93;
    pointer-events: none;
}

/* Badge inferior */
.mac-badge-locked {
    display: inline-block;
    margin-top: 8px;
    font-size: 10px;
    font-weight: 500;
    color: var(--mac-accent);
    background: rgba(0, 122, 255, 0.08);
    padding: 2px 8px;
    border-radius: 10px;
}

</style>
<style>
/* Contenedor con el borde inicial sutil */
.mac-select-container {
    padding: 10px 14px;
    background: #ffffff;
    border: 1px solid #d1d1d6; /* Borde gris clásico de Mac */
    border-radius: 12px;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
}

/* El "Borde de Color Elegante" cuando el usuario entra al campo */
.mac-select-container:focus-within {
    border-color: #007aff; /* Azul Apple */
    box-shadow: 0 0 0 4px rgba(0, 122, 255, 0.15); /* Brillo exterior */
    background: #fff;
}

/* Estilo de la etiqueta superior */
.mac-label {
    font-size: 11px;
    font-weight: 700;
    color: #8e8e93;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 4px;
    display: block;
}

/* Limpieza del select nativo */
.mac-select {
    width: 100%;
    border: none !important;
    outline: none !important;
    background: transparent;
    font-size: 15px;
    font-family: -apple-system, sans-serif;
    color: #1d1d1f;
    cursor: pointer;
    appearance: none;
}

</style>
<div class="modal fade" id="modalNuevaCompra" tabindex="-1" aria-labelledby="modalNuevaCompraLabel" aria-hidden="true"
    data-bs-backdrop="static">
    <div class="modal-dialog modal-xl">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="modalNuevaCompraLabel">
                    <i class="bi bi-box-seam-fill me-2"></i> Registrar Compra / Entrada de Inventario
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>

            <form id="formNuevaCompra" enctype="multipart/form-data" autocomplete="off">
                <div class="modal-body bg-light">
                    <div class="card mb-4 border-0 shadow-sm">
                        <div class="card-body row g-3">
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Proveedor</label>
                                <input type="text" name="proveedor" class="form-control"
                                    placeholder="Nombre del proveedor" required>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small fw-bold">Folio de Factura</label>
                                <input type="text" name="folio" class="form-control" placeholder="Ej: F-123" required>
                            </div>
                          <div class="col-md-4">
    <div class="mac-select-container">
        <label class="mac-label">
            <i class="bi bi-box-seam"></i> Almacén de Cargo
        </label>
        
        <?php $es_admin = ($_SESSION['rol_id'] == 1); ?>
        
        <div class="select-wrapper">
            <select id="almacen_id_cabecera_visual"
                class="mac-select <?= $es_admin ? 'admin-active' : 'user-locked' ?>"
                <?= !$es_admin ? 'disabled' : 'name="almacen_id_cabecera"' ?> required>
                
                <?php if ($es_admin): ?>
                    <option value="">Seleccionar ubicación...</option>
                <?php endif; ?>

                <?php foreach($almacenes as $a): ?>
                    <option value="<?= $a['id'] ?>"
                        <?= ($a['id'] == $_SESSION['almacen_id']) ? 'selected' : '' ?>>
                        <?= $a['nombre'] ?> <?= ($a['id'] == $_SESSION['almacen_id'] && !$es_admin) ? ' •' : '' ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <i class="bi bi-chevron-down custom-arrow"></i>
        </div>

        <?php if (!$es_admin): ?>
            <input type="hidden" name="almacen_id_cabecera" value="<?= $_SESSION['almacen_id'] ?>">
            <span class="mac-badge-locked">
                <i class="bi bi-lock-fill"></i> Privilegios de sede actual
            </span>
        <?php endif; ?>
    </div>
</div>
                            <div class="col-md-3">
                                <label class="form-label small fw-bold">Evidencia (PDF/IMG)</label>
                                <input type="file" name="evidencia_compra" class="form-control" accept="image/*,.pdf">
                            </div>
                            <div class="col-md-3 text-end">
                                <label class="form-label small fw-bold text-muted">TOTAL FACTURA</label>
                                <div class="h3 text-success fw-bold" id="granTotalCompra">$ 0.00</div>
                            </div>
                        </div>
                    </div>

                    <h6 class="fw-bold mb-3 d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-list-check me-2"></i>Detalle de Productos</span>
                        <span class="badge bg-dark" id="conteoItems">0 Productos</span>
                    </h6>

                    <div id="contenedorItemsCompra"></div>

                    <div class="mt-3">
                        <button type="button" class="btn btn-outline-primary btn-sm rounded-pill px-4"
                            onclick="agregarFilaCompra()">
                            <i class="bi bi-plus-circle me-1"></i> Agregar Producto a la Lista
                        </button>
                    </div>
                </div>

                <div class="modal-footer bg-white shadow-sm">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-success px-5" id="btnGuardarCompra"
                        onclick="procesarGuardadoCompra(); return false;">
                        <i class="bi bi-save me-2"></i> Guardar Compra e Inventario
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/agregarPoductoModal.php'; ?>

<script>
/**
 * LÓGICA DE COMPRAS - CF SISTEM
 */
function abrirModalCompra() {
    // Resetear el formulario pero mantener el almacén seleccionado por PHP
    const almacenPreseleccionado = $('#almacen_id_cabecera').val();
    $('#formNuevaCompra')[0].reset();
    $('#almacen_id_cabecera').val(almacenPreseleccionado); // Restauramos lo que PHP eligió

    $('#contenedorItemsCompra').empty();
    $('#granTotalCompra').text('$ 0.00');

    if (ES_ADMIN) {
        setTimeout(() => {
            $('.select2-cabecera').select2({
                theme: 'bootstrap-5',
                dropdownParent: $('#modalNuevaCompra .modal-content')
            });
        }, 100);
    }

    agregarFilaCompra();
    $('#modalNuevaCompra').modal('show');
}

function agregarFilaCompra() {
    const idUnico = Date.now();

    let opcionesProd = '<option value="">-- Buscar Producto --</option>';
    DATA_COMPRAS.productos.forEach(p => {
        opcionesProd +=
            `<option value="${p.id}" data-factor="${p.factor_conversion}" data-ubase="${p.unidad_medida}" data-urep="${p.unidad_reporte}">${p.nombre} (${p.sku})</option>`;
    });

    let filasAlmacenes = '';
    
    // LÓGICA DE FILTRADO: 
    // Si es Admin, ve todos. Si no, solo ve el que coincida con USER_ALMACEN_ID
    const almacenesAMostrar = ES_ADMIN 
        ? DATA_COMPRAS.almacenes 
        : DATA_COMPRAS.almacenes.filter(alm => alm.id == USER_ALMACEN_ID);

    almacenesAMostrar.forEach(alm => {
        // Si no es admin, bloqueamos el checkbox para que no pueda desmarcar su propio almacén
        const inputBloqueado = !ES_ADMIN ? 'onclick="return false;" style="opacity: 0.7;"' : '';
        const filaResaltada = alm.id == USER_ALMACEN_ID ? 'table-info' : '';

        filasAlmacenes += `
        <tr class="${filaResaltada}">
            <td class="text-center align-middle">
                <input type="checkbox" name="items[${idUnico}][almacenes][${alm.id}][activo]" 
                       class="form-check-input check-activo" checked ${inputBloqueado} 
                       onchange="recalcularTotales(${idUnico})">
            </td>
            <td class="small align-middle fw-bold">
                ${alm.nombre} 
                ${alm.id == USER_ALMACEN_ID ? '<br><small class="text-primary">(Tu almacén)</small>' : ''}
            </td>
            <td>
                <input type="number" name="items[${idUnico}][almacenes][${alm.id}][cantidad]" 
                       class="form-control form-control-sm input-reparto border-primary" 
                       placeholder="Confirmar cantidad" min="0" step="0.01" 
                       oninput="validarReparto(${idUnico})">
            </td>
        </tr>`;
    });

    const html = `
    <div class="card mb-4 border-start border-4 border-success shadow-sm item-compra" id="card_item_${idUnico}">
        <div class="card-body">
            <div class="row g-3 mb-3">
           <div class="col-md-3">
    <div class="mac-select-container h-100">
        <div class="d-flex justify-content-between align-items-center mb-1">
            <label class="mac-label m-0">
                <i class="bi bi-search"></i> Producto
            </label>
            <button type="button" 
                    class="btn btn-outline-primary btn-sm rounded-circle border-0 p-0" 
                    style="width: 20px; height: 20px; font-size: 14px; background: rgba(0, 122, 255, 0.1);"
                    onclick="$('#modalAgregarProducto').modal('show')"
                    data-bs-toggle="tooltip" 
                    title="Registrar nuevo producto">
                <i class="bi bi-plus-lg"></i>
            </button>
        </div>
        
        <div class="select-wrapper">
            <select name="items[${idUnico}][producto_id]" 
                class="mac-select admin-active select2-compra" 
                onchange="actualizarLabelsUnidad(${idUnico}, this)" required>
                ${opcionesProd}
            </select>
            </div>
    </div>
</div>
                <div class="col-md-2">
                    <label class="small fw-bold label-urep">Cant. Mayoreo</label>
                    <input type="number" class="form-control input-mayoreo" value="0" min="0" step="0.01" oninput="recalcularTotales(${idUnico})">
                </div>
                <div class="col-md-1">
                    <label class="small fw-bold label-ubase">Sueltas</label>
                    <input type="number" class="form-control input-sueltas" value="0" min="0" step="0.01" oninput="recalcularTotales(${idUnico})">
                </div>
                
                <div class="col-md-2">
                    <label class="small fw-bold text-danger">
                        <input type="checkbox" class="form-check-input check-habilitar-faltante" onchange="toggleFaltante(${idUnico}, this)"> 
                        ¿Faltante?
                    </label>
                    <div class="input-group input-group-sm">
                        <input type="number" class="form-control input-faltante border-danger" value="0" min="0" step="0.01" id="faltante_${idUnico}" disabled oninput="recalcularTotales(${idUnico})">
                        <input type="hidden" name="items[${idUnico}][cantidad_faltante]" class="hidden-faltante" value="0">
                    </div>
                </div>

                <div class="col-md-3">
                    <label class="small fw-bold">Costo Total Renglón</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light fw-bold">$</span>
                        <input type="number" name="items[${idUnico}][total_item]" class="form-control input-costo-total" value="0" step="0.01" oninput="actualizarGranTotal()" required>
                    </div>
                </div>

                <div class="col-md-1 d-flex align-items-end">
                    <button type="button" class="btn btn-outline-danger btn-sm w-100" onclick="$('#card_item_${idUnico}').remove(); actualizarGranTotal();">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>
            
            <div class="row g-3">
                <div class="col-md-4">
                    <div class="p-2 bg-dark text-white rounded text-center">
                        <small class="d-block opacity-75">STOCK TOTAL A INGRESAR:</small>
                        <span class="h5 mb-0 fw-bold span-total-base">0</span> <small class="label-ubase-text">pzas</small>
                        <input type="hidden" class="hidden-factor" value="1">
                        <input type="hidden" class="hidden-total-piezas" name="items[${idUnico}][cantidad_total_piezas]" value="0">
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="alert alert-info py-2 px-3 m-0 small d-flex justify-content-between align-items-center h-100 border-0 shadow-sm rounded">
                        <span><i class="bi bi-info-circle-fill me-2"></i>Confirma las piezas recibidas en tu almacén:</span>
                        <span class="badge bg-danger" id="error_reparto_${idUnico}" style="display:none;">Suma no coincide</span>
                    </div>
                </div>
            </div>
            <hr class="my-3">
            <table class="table table-sm table-borderless align-middle mb-0">
                <thead class="text-muted" style="font-size: 0.75rem;">
                    <tr><th width="10%" class="text-center">¿USAR?</th><th width="50%">ALMACÉN DESTINO</th><th width="40%">PIEZAS FÍSICAS</th></tr>
                </thead>
                <tbody>${filasAlmacenes}</tbody>
            </table>
        </div>
    </div>`;

    $('#contenedorItemsCompra').append(html);
    setTimeout(() => {
        $(`#card_item_${idUnico} .select2-compra`).select2({
            theme: 'bootstrap-5',
            dropdownParent: $('#modalNuevaCompra .modal-content')
        });
    }, 50);
    actualizarConteo();
}
function actualizarLabelsUnidad(id, select) {
    const opt = $(select).find(':selected');
    const factor = opt.data('factor') || 1;
    const uBase = opt.data('ubase') || 'Piezas';
    const uRep = opt.data('urep') || 'Mayoreo';
    const card = $(`#card_item_${id}`);
    card.find('.hidden-factor').val(factor);
    card.find('.label-urep').text(uRep);
    card.find('.label-ubase').text(uBase);
    card.find('.label-ubase-text').text(uBase);
    recalcularTotales(id);
}

function recalcularTotales(id) {
    const card = $(`#card_item_${id}`);
    const factor = parseFloat(card.find('.hidden-factor').val()) || 0;
    const inputFaltante = card.find('.input-faltante');

    // 1. Cantidad según lo que dice la factura (Mayoreo * Factor + Sueltas)
    const cantidadFacturada = (parseFloat(card.find('.input-mayoreo').val()) || 0) * factor +
        (parseFloat(card.find('.input-sueltas').val()) || 0);

    // 2. Si el input está deshabilitado, el faltante es 0. Si no, tomamos su valor.
    const faltante = inputFaltante.is(':disabled') ? 0 : (parseFloat(inputFaltante.val()) || 0);

    // 3. Lo que realmente llegó
    const totalReal = cantidadFacturada - faltante;

    // Actualizar labels y campos ocultos
    card.find('.span-total-base').text(totalReal.toLocaleString());
    card.find('.hidden-total-piezas').val(totalReal);
    card.find('.hidden-faltante').val(faltante);

    validarReparto(id);
    actualizarGranTotal();
}

function validarReparto(id) {
    const card = $(`#card_item_${id}`);
    const total = parseFloat(card.find('.hidden-total-piezas').val()) || 0;
    let suma = 0;
    card.find('.input-reparto').each(function() {
        if ($(this).closest('tr').find('.check-activo').is(':checked')) suma += parseFloat($(this).val()) || 0;
    });
    const error = $(`#error_reparto_${id}`);
    if (Math.abs(suma - total) > 0.001 && total > 0) {
        card.find('.alert').addClass('alert-danger text-danger').removeClass('alert-info text-dark');
        error.show().text(`Diferencia: ${(total - suma).toFixed(2)}`);
    } else {
        card.find('.alert').addClass('alert-info text-dark').removeClass('alert-danger text-danger');
        error.hide();
    }
}

function toggleFaltante(id, checkbox) {
    const inputFaltante = $(`#faltante_${id}`);

    if (checkbox.checked) {
        inputFaltante.prop('disabled', false).focus();
    } else {
        inputFaltante.prop('disabled', true).val(0); // Si lo desmarcan, vuelve a cero
        recalcularTotales(id); // Recalculamos para que el stock vuelva a la normalidad
    }
}

function actualizarGranTotal() {
    let granTotal = 0;
    $('.input-costo-total').each(function() {
        granTotal += parseFloat($(this).val()) || 0;
    });
    $('#granTotalCompra').text('$ ' + granTotal.toLocaleString(undefined, {
        minimumFractionDigits: 2
    }));
    actualizarConteo();
}

function actualizarConteo() {
    const n = $('.item-compra').length;
    $('#conteoItems').text(`${n} Producto${n !== 1 ? 's' : ''}`);
}
function refrescarListaProductosCompra(nuevoIdSeleccionar = null) {
    // 1. Volvemos a pedir los productos al servidor o actualizamos el DATA_COMPRAS
    // Por simplicidad, si ya tienes el objeto res.id, puedes añadirlo manualmente al array DATA_COMPRAS.productos
    // O hacer una petición rápida:
    $.get('/cfsistem/app/controllers/AlmacenController.php?action=getListaProductosJson', function(data) {
        DATA_COMPRAS.productos = data; // Actualizamos la base de datos local
        
        // 2. Refrescamos todos los select2 que ya existen en las filas de compra
        $('.select2-compra').each(function() {
            const select = $(this);
            const valorActual = select.val();
            
            let html = '<option value="">-- Buscar Producto --</option>';
            DATA_COMPRAS.productos.forEach(p => {
                html += `<option value="${p.id}" data-factor="${p.factor_conversion}" data-ubase="${p.unidad_medida}" data-urep="${p.unidad_reporte}">${p.nombre} (${p.sku})</option>`;
            });
            
            select.html(html).val(valorActual).trigger('change');
        });
        
        // Si acabamos de crear uno, lo seleccionamos en la última fila vacía
        if(nuevoIdSeleccionar) {
             console.log("Nuevo producto listo para seleccionar:", nuevoIdSeleccionar);
        }
    }, 'json');
}
/**
 * MANEJO DEL SUBMIT (BLINDADO)
 */
function procesarGuardadoCompra(event) {
    if (event) {
        event.preventDefault();
        event.stopPropagation();
    }

    console.log("Iniciando proceso de guardado...");

    // 1. VALIDACIÓN: Comparamos lo repartido vs lo que FÍSICAMENTE llegó
    let inconsistencias = 0;
    let mensajeDetalle = "";

    $('.item-compra').each(function(index) {
        // 'hidden-total-piezas' ya tiene restado el faltante por la función recalcularTotales
        const totalFisicoReal = parseFloat($(this).find('.hidden-total-piezas').val()) || 0;
        const nombreProd = $(this).find('.select2-compra option:selected').text() || "Producto " + (index + 1);

        let sumaAlmacenes = 0;
        $(this).find('.input-reparto').each(function() {
            if ($(this).closest('tr').find('.check-activo').is(':checked')) {
                sumaAlmacenes += parseFloat($(this).val()) || 0;
            }
        });

        if (Math.abs(totalFisicoReal - sumaAlmacenes) > 0.01) {
            inconsistencias++;
            mensajeDetalle += `\n- ${nombreProd}: Debes repartir ${totalFisicoReal} (llevas ${sumaAlmacenes})`;
        }
    });

    if (inconsistencias > 0) {
        Swal.fire('Atención', 'La distribución en almacenes no coincide con lo recibido físicamente:' + mensajeDetalle,
            'warning');
        return false;
    }

    // 2. DETECTAR SI HAY FALTANTES PARA EL CONFIRM
    let hayFaltantes = false;
    $('.hidden-faltante').each(function() {
        if (parseFloat($(this).val()) > 0) hayFaltantes = true;
    });

    // 3. CONFIRMACIÓN Y ENVÍO AJAX (Tu bloque original)
    Swal.fire({
        title: hayFaltantes ? '¿Registrar con Faltantes?' : '¿Confirmar Registro?',
        text: hayFaltantes ?
            "La mercancía incompleta se guardará como pendiente." :
            "Se actualizará el stock y se registrará el gasto.",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#198754',
        confirmButtonText: 'Sí, guardar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            const formElement = document.getElementById('formNuevaCompra');
            const formData = new FormData(formElement);

            // Importante: Aseguramos que el controlador reciba si hay faltantes
            formData.append('tiene_faltantes', hayFaltantes ? 1 : 0);

            $.ajax({
                url: '/cfsistem/app/controllers/egresosController.php?action=guardarCompraInventario',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                cache: false,
                beforeSend: function() {
                    $('#btnGuardarCompra').prop('disabled', true).html(
                        '<span class="spinner-border spinner-border-sm"></span> Guardando...');
                },
                success: function(res) {
                    try {
                        const data = typeof res === 'string' ? JSON.parse(res) : res;
                        if (data.success) {
                            Swal.fire('¡Éxito!', data.message, 'success').then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire('Error', data.message, 'error');
                            $('#btnGuardarCompra').prop('disabled', false).html(
                                '<i class="bi bi-save me-2"></i> Guardar Compra e Inventario');
                        }
                    } catch (err) {
                        console.error("Error parseo JSON:", res);
                        Swal.fire('Error Crítico', 'Respuesta no válida del servidor.', 'error');
                        $('#btnGuardarCompra').prop('disabled', false).html('Guardar');
                    }
                },
                error: function(xhr) {
                    console.error("Error 500:", xhr.responseText);
                    Swal.fire('Error de Servidor', 'El controlador falló (500).', 'error');
                    $('#btnGuardarCompra').prop('disabled', false).html('Guardar');
                }
            });
        }
    });
}

</script>