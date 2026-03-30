<div class="modal fade" id="modalDespachoVentaGfin" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius:20px; overflow:hidden;">

            <div class="modal-header bg-dark text-white py-3">
                <div class="d-flex align-items-center">
                    <div class="bg-primary p-2 rounded-3 me-3">
                        <i class="bi bi-calculator fs-4"></i>
                    </div>
                    <div>
                        <h5 class="mb-0 fw-bold">Despacho con Análisis Financiero</h5>
                        <span id="gfin_txtFolioVenta" class="badge bg-secondary mt-1">Cargando...</span>
                    </div>
                </div>
                <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body p-0 bg-light">
                <div class="row g-0">
                    <div class="col-lg-7 border-end bg-white">
                        <div class="p-4 border-bottom bg-light">
                            <div class="row text-center">
                                <div class="col-6 border-end">
                                    <small class="text-muted text-uppercase fw-bold" style="font-size: 0.65rem;">Artículos</small>
                                    <h4 id="gfin_totalProductos" class="mb-0 fw-bold text-dark">0</h4>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted text-uppercase fw-bold" style="font-size: 0.65rem;">Utilidad Neta</small>
                                    <h4 id="gfin_totalUtilidad" class="mb-0 fw-bold text-success">$0.00</h4>
                                </div>
                            </div>
                        </div>

                        <div id="gfin_listaItems" style="max-height:500px; overflow-y:auto; padding:20px;"></div>
                    </div>

                    <div class="col-lg-5 bg-white">
                        <div class="p-4">
                            <div class="d-flex align-items-center mb-4">
                                <i class="bi bi-truck text-primary fs-5 me-2"></i>
                                <h6 class="fw-bold mb-0">Logística de Salida</h6>
                            </div>

                            <div class="mb-4">
                                <label class="small fw-bold text-muted text-uppercase mb-2 d-block">Modo de Entrega</label>
                                <div class="btn-group w-100 shadow-sm">
                                    <input type="radio" class="btn-check" name="gfin_tipo_logistica" id="gfin_optPatio" value="patio" checked onchange="gfin_toggleRuta(false)">
                                    <label class="btn btn-outline-primary py-2 fw-bold" for="gfin_optPatio">
                                        <i class="bi bi-shop me-2"></i>PATIO
                                    </label>

                                    <input type="radio" class="btn-check" name="gfin_tipo_logistica" id="gfin_optRuta" value="ruta" onchange="gfin_toggleRuta(true)">
                                    <label class="btn btn-outline-dark py-2 fw-bold" for="gfin_optRuta">
                                        <i class="bi bi-map me-2"></i>RUTA
                                    </label>
                                </div>
                            </div>

                            <div id="gfin_formRuta" class="d-none animate__animated animate__fadeIn">
                                <div class="card border-0 bg-light p-3 rounded-4 mb-3">
                                    <div class="mb-3">
                                        <label class="small fw-bold text-primary">DIRECCIÓN / DESTINO</label>
                                        <textarea id="gfin_direccion" class="form-control border-0 shadow-sm" rows="2"></textarea>
                                    </div>

                                    <div class="row g-2 mb-3">
                                        <div class="col-md-6">
                                            <label class="small fw-bold text-muted">UNIDAD</label>
                                            <select id="gfin_vehiculo_id" class="form-select border-0 shadow-sm"></select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="small fw-bold text-muted">CHOFER</label>
                                            <select id="gfin_chofer_id" class="form-select border-0 shadow-sm"></select>
                                        </div>
                                    </div>

                                    <div class="mb-0">
                                        <label class="small fw-bold text-muted">AYUDANTES</label>
                                        <select id="gfin_tripulantes" class="form-select border-0 shadow-sm" multiple size="3"></select>
                                    </div>
                                </div>
                            </div>
                            
                            <div id="gfin_loader" class="text-center py-5">
                                <div class="spinner-grow text-primary mb-2" role="status"></div>
                                <p class="small text-muted fw-bold">Sincronizando recursos...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer bg-white border-top-0 py-3 px-4">
                <button class="btn btn-link text-muted fw-bold text-decoration-none" data-bs-dismiss="modal">Cerrar</button>
                <button id="gfin_btnConfirmar" class="btn btn-success rounded-pill px-5 fw-bold shadow-sm" disabled>
                    <i class="bi bi-check-circle me-2"></i>EJECUTAR DESPACHO
                </button>
            </div>
        </div>
    </div>
</div>
<script>
    /**
 * Alterna el formulario de ruta (Namespace Gfin)
 */
function gfin_toggleRuta(mostrar) {
    const form = document.getElementById('gfin_formRuta');
    mostrar ? form.classList.remove('d-none') : form.classList.add('d-none');
}

/**
 * ABRE EL MODAL Y CARGA DATOS FINANCIEROS
 */
async function abrirModalDespachoVentaGfin(venta_id, almacenId) {
    const URL_ENTREGAS = '/cfsistem/app/controllers/entregasController.php'; 
    
    // Referencias a elementos con IDs nuevos
    const contenedor = document.getElementById('gfin_listaItems');
    const txtFolio = document.getElementById('gfin_txtFolioVenta');
    const btnConfirmar = document.getElementById('gfin_btnConfirmar');
    const loader = document.getElementById('gfin_loader');
    const labelTotal = document.getElementById('gfin_totalProductos');
    const labelUtilidad = document.getElementById('gfin_totalUtilidad');

    // UI Reset
    txtFolio.innerHTML = `<span class="spinner-border spinner-border-sm me-2"></span>Sincronizando...`;
    contenedor.innerHTML = `<div class="text-center py-5"><div class="spinner-border text-success opacity-25"></div><p class="text-muted mt-2">Analizando costos y lotes...</p></div>`;
    loader.classList.remove('d-none');
    btnConfirmar.disabled = true;
    window.gfin_data_tickets = []; // Variable global renombrada

    const modalInstance = new bootstrap.Modal(document.getElementById('modalDespachoVentaGfin'));
    modalInstance.show();

    try {
        // 1. Obtener IDs pendientes
        const respIds = await fetch(`${URL_ENTREGAS}?ajax=entregas_pendientes&venta_id=${venta_id}`);
        const dataIds = await respIds.json();
        
        if (!dataIds.success || !dataIds.ids?.length) throw new Error("Sin artículos pendientes.");
        const idsParaProcesar = dataIds.ids;

        // 2. Cargar ganancias en paralelo
        const promesas = idsParaProcesar.map(id => 
            fetch(`${URL_ENTREGAS}?ajax=imprimirGanancia&id=${id}`).then(r => r.json())
        );

        const resultados = await Promise.all(promesas);
        
        let htmlFinal = '';
        let sumaUtilidad = 0;
        
        resultados.forEach((res, index) => {
            if (res.success && res.data) {
                const d = res.data;
                window.gfin_data_tickets.push(d);
                sumaUtilidad += parseFloat(d.ganancia_neta || 0);

                let filasLotes = '';
                if (d.detalle_financiero) {
                    d.detalle_financiero.split('___').forEach((reg) => {
                        const c = reg.split('|'); 
                        if (c.length === 4) {
                            const cant = parseFloat(c[1]);
                            const util = (cant * parseFloat(c[3])) - (cant * parseFloat(c[2]));
                            filasLotes += `
                                <tr>
                                    <td class="text-start small fw-bold text-secondary">${c[0]}</td>
                                    <td class="text-center fw-bold">${cant}</td>
                                    <td class="text-end fw-bold ${util < 0 ? 'text-danger' : 'text-success'}">$${util.toFixed(2)}</td>
                                </tr>`;
                        }
                    });
                }

                htmlFinal += `
                    <div class="card mb-3 border-0 shadow-sm animate__animated animate__fadeIn" style="border-radius: 15px; border-left: 5px solid #0d6efd !important;">
                        <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                            <div><h6 class="mb-0 fw-bold text-dark">${d.producto}</h6></div>
                            <span class="badge bg-success-subtle text-success p-2 px-3 rounded-pill fw-bold">+$${parseFloat(d.ganancia_neta).toFixed(2)}</span>
                        </div>
                        <div class="card-body pt-0">
                            <table class="table table-sm table-borderless mb-0" style="font-size: 0.72rem;">
                                <tbody>${filasLotes}</tbody>
                            </table>
                        </div>
                    </div>`;
            }
        });

        txtFolio.innerHTML = `VENTA #${venta_id}`;
        contenedor.innerHTML = htmlFinal;
        labelTotal.innerText = idsParaProcesar.length;
        labelUtilidad.innerText = `$${sumaUtilidad.toLocaleString('en-US', {minimumFractionDigits:2})}`;

        // 3. Recursos Logísticos (Carga igual, IDs de destino diferentes)
        const [resLog, resCat] = await Promise.all([
            fetch(`${URL_ENTREGAS}?ajax=get_recursos_reparto&id=${idsParaProcesar[0]}`).then(r => r.json()),
            fetch(`${URL_ENTREGAS}?ajax=get_recursos_sucursal&almacen_id=${almacenId}`).then(r => r.json())
        ]);

        if (resLog.success && resCat.success) {
            $('#gfin_direccion').val(resLog.data.entrega.cliente_direccion_fiscal || '');
            
            const vSelect = $('#gfin_vehiculo_id').empty().append('<option value="">Seleccionar...</option>');
            resCat.unidades.forEach(u => vSelect.append(`<option value="${u.id}">${u.nombre} [${u.placas || 'S/P'}]</option>`));
            
            const cSelect = $('#gfin_chofer_id').empty().append('<option value="">Seleccionar...</option>');
            const tSelect = $('#gfin_tripulantes').empty();
            resCat.choferes.forEach(c => {
                cSelect.append(`<option value="${c.id}">${c.nombre}</option>`);
                tSelect.append(`<option value="${c.id}">${c.nombre}</option>`);
            });

            $('#gfin_chofer_id').off('change').on('change', function() {
                const cid = $(this).val();
                $(`#gfin_tripulantes option`).prop('disabled', false).show();
                if(cid) $(`#gfin_tripulantes option[value="${cid}"]`).prop('disabled', true).hide();
            });

            loader.classList.add('d-none');
            btnConfirmar.disabled = false;
            btnConfirmar.onclick = () => gfin_ejecutarDespachoFinal(idsParaProcesar, btnConfirmar);
        }

    } catch (err) {
        contenedor.innerHTML = `<div class="alert alert-danger m-2 small">${err.message}</div>`;
    }
}

/**
 * ACCIÓN FINAL DE GUARDADO
 */
async function gfin_ejecutarDespachoFinal(ids, btn) {
    const tipo = $('input[name="gfin_tipo_logistica"]:checked').val();
    
    if (tipo === 'ruta' && (!$('#gfin_vehiculo_id').val() || !$('#gfin_chofer_id').val())) {
        return Swal.fire('Atención', 'Seleccione unidad y chofer.', 'warning');
    }

    btn.disabled = true;
    btn.innerHTML = `<span class="spinner-border spinner-border-sm"></span>`;

    try {
        const fd = new FormData();
        fd.append('ajax', 'despachar_venta_completaFaltantesEntrega'); 
        fd.append('tipo_logistica', tipo);
        fd.append('vehiculo_id', $('#gfin_vehiculo_id').val() || 0);
        fd.append('chofer_id', $('#gfin_chofer_id').val() || 0);
        fd.append('direccion', $('#gfin_direccion').val() || '');
        
        ($('#gfin_tripulantes').val() || []).forEach(tId => fd.append('tripulantes[]', tId));
        ids.forEach(id => fd.append('ids_movimientos[]', id));

        const resp = await fetch('/cfsistem/app/controllers/entregasController.php', { method: 'POST', body: fd });
        const res = await resp.json();

        if (res.success) {
            Swal.fire({ icon: 'success', title: 'Listo', text: res.message, timer: 1500 }).then(() => location.reload());
        } else {
            throw new Error(res.message);
        }
    } catch (e) {
        Swal.fire('Error', e.message, 'error');
        btn.disabled = false;
        btn.innerHTML = 'EJECUTAR DESPACHO';
    }
}
</script>