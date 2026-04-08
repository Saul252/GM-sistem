/**
 * Carga y renderiza la lista de entregas en la tabla principal.
 * Maneja lógica de agrupación por venta y estados dinámicos de botones.
 */
function cargarEntregas() {
    // Mostrar feedback visual de carga
    $('#loader').removeClass('d-none');
    
    // Verificar si el usuario desea ver los productos agrupados por Folio de Venta
    const agrupar = $('#checkAgruparVenta').is(':checked');

    $.ajax({
        url: 'entregasController.php',
        data: {
            ajax: 'listar',
            periodo: $('#selectorPeriodo').val(),
            f_inicio: $('#f_inicio').val(),
            f_fin: $('#f_fin').val(),
            almacen_id: $('#filtroAlmacen').val()
        },
        dataType: 'json',
        success: function(res) {
            // Limpiar tabla antes de insertar nuevos datos
            tabla.clear();
            
            // Validar si hay datos en la respuesta
            if (!res.data) { 
                tabla.draw(); 
                return; 
            }

            let datosAMostrar = res.data;

            // --- PROCESAMIENTO DE AGRUPACIÓN ---
            if (agrupar) {
                const grupos = {};
                res.data.forEach(item => {
                    const folio = item.folio_venta || 'SIN-FOLIO';
                    
                    // Si el folio no existe en el acumulador, se crea el objeto base
                    if (!grupos[folio]) {
                        grupos[folio] = { 
                            ...item, 
                            cliente: item.cliente || 'Público General', // Se asegura de mantener el cliente al agrupar
                            total_items: 0, 
                            items_despachados: 0, 
                            items_en_ruta: 0,
                            items_completados: 0,
                            ids_movimientos: [] 
                        };
                    }
                    
                    // Incrementar contadores generales del grupo
                    grupos[folio].total_items++;
                    
                    // Contadores lógicos según el estado de cada item para decidir la acción global
                    if (parseInt(item.ya_despachado) === 1) grupos[folio].items_despachados++;
                    if (item.estado_reparto === 'en_transito') grupos[folio].items_en_ruta++;
                    if (item.estado_reparto === 'completado') grupos[folio].items_completados++;
                    
                    grupos[folio].ids_movimientos.push(item.id);
                });
                // Convertir el objeto de grupos de nuevo a un array para el forEach
                datosAMostrar = Object.values(grupos);
            }

            // --- RENDERIZADO DE FILAS ---
            datosAMostrar.forEach(m => {
                let accionHtml = ''; // Almacenará los botones/badges
                let prodCol = '';   // Nombre del producto o título de grupo
                let cantCol = '';   // Cantidad formateada o resumen de items

                // CASO A: VISTA AGRUPADA (Cuando hay más de un producto bajo el mismo folio)
                if (agrupar && m.total_items > 1) {
                    const todoDespachado = (m.total_items === m.items_despachados);
                    const todoCompletado = (m.total_items === m.items_completados);
                    const algoEnRuta     = (m.items_en_ruta > 0);

                    cantCol = `<div class="text-center text-muted small">${m.total_items} Artículos</div>`;
                    prodCol = `<b>Venta Consolidada</b><br><small class="text-muted">Folio: ${m.folio_venta}</small>`;

                    if (todoCompletado) {
                        // Estado: Entrega finalizada para todos los items
                        accionHtml = `
                            <div class="card border-0 shadow-sm mb-3" style="border-radius: 18px; background: rgba(255, 255, 255, 0.8); backdrop-filter: blur(15px); border: 1px solid rgba(255, 255, 255, 0.4) !important; padding: 12px 16px;">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center" style="gap: 15px;">
                                        <button class="btn d-flex align-items-center p-0 transition-ios" style="background: transparent; border: none; color: #8e8e93; font-weight: 600; font-size: 0.65rem; letter-spacing: 0.5px;" onclick="verDetalleGananciaVenta(${m.venta_id})">
                                            <i class="bi bi-shield-check me-1" style="font-size: 0.9rem;"></i> AUDITORÍA
                                        </button>
                                        <button class="btn d-flex align-items-center p-0 transition-ios" style="background: transparent; border: none; color: #007aff; font-weight: 600; font-size: 0.65rem; letter-spacing: 0.5px;" onclick="verDetalleDespachoAlmacen(${m.venta_id})">
                                            <i class="bi bi-box-seam me-1" style="font-size: 0.9rem;"></i> LOGÍSTICA
                                        </button>
                                    </div>
                                    <div class="d-flex align-items-center px-3" style="background: rgba(52, 199, 89, 0.12); color: #248a3d; border: 1px solid rgba(52, 199, 89, 0.2); border-radius: 20px; height: 28px;">
                                        <i class="bi bi-check-circle-fill me-1" style="font-size: 0.75rem;"></i>
                                        <span style="font-size: 0.6rem; font-weight: 800; letter-spacing: 0.5px;">ENTREGADO</span>
                                    </div>
                                </div>
                            </div>
                            <style>
                                .transition-ios { transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1); }
                                .transition-ios:hover { opacity: 0.7; transform: translateY(-1px); }
                                .transition-ios:active { transform: scale(0.95); opacity: 1; }
                            </style>`;
                    } 
                    else if (algoEnRuta) {
                        // Estado: Al menos una parte de la venta está viajando
                        accionHtml = `
                            <div class="text-end pe-3">
                                <span class="badge rounded-pill p-2 px-3" style="background: rgba(255, 149, 0, 0.1); color: #ff9500; border: 1px solid #ff9500;">
                                    <i class="bi bi-truck me-1"></i> MERCANCÍA EN TRÁNSITO
                                </span>
                            </div>
                            <button onclick="verDetalleGanancia(${m.id})" class="btn btn-link ms-2 text-decoration-none" style="color: #ceced2;">
                                <i class="bi bi-graph-up-arrow fs-6"></i>
                            </button>`;
                    }
                    else if (todoDespachado) {
                        // Estado: Salieron de almacén pero falta asignarles ruta/destino
                        accionHtml = `
                            <div class="d-flex align-items-center gap-2 py-1">
                                <button onclick="abrirModalDespachoVentaGfin(${m.venta_id}, ${m.almacen_origen_id})" class="btn d-flex align-items-center justify-content-center shadow-sm transition-ios" style="background: rgba(0, 122, 255, 0.1); color: #007aff; border: 1px solid rgba(0, 122, 255, 0.2); border-radius: 12px; font-weight: 700; height: 32px; padding: 0 15px;">
                                    <i class="bi bi-geo-alt-fill me-2" style="font-size: 0.8rem;"></i>
                                    <span style="font-size: 0.65rem; letter-spacing: 0.3px; text-transform: uppercase;">Destino Entrega</span>
                                </button>
                                <button class="btn d-flex align-items-center opacity-75-hover" style="background: transparent; border: none; color: #8e8e93; font-weight: 600; font-size: 0.7rem; letter-spacing: 0.5px;" onclick="verDetalleGananciaVenta(${m.venta_id})">
                                    <i class="bi bi-shield-check me-1" style="font-size: 0.9rem;"></i> AUDITORÍA
                                </button>
                            </div>`;
                    }
                    else {
                        // Estado Inicial: Pendiente de procesar salida
                        accionHtml = `
                            <div class="text-end pe-3">
                                <button class="btn btn-sm rounded-pill btn-dark px-4 shadow-sm" onclick="abrirModalDespachoVentaTotal(${m.venta_id},${m.almacen_origen_id})">
                                    <i class="bi bi-list-check me-1"></i> GESTIONAR VENTA
                                </button>
                            </div>`;
                    }
                } 
                // CASO B: VISTA INDIVIDUAL (Un solo registro por fila)
                else {
                    const yaDespachado = (parseInt(m.ya_despachado) === 1);
                    const enRuta       = (m.estado_reparto === 'en_transito');
                    const completado   = (m.estado_reparto === 'completado');

                    if (completado || enRuta) {
                        const color = completado ? '#28a745' : '#ff9500';
                        const texto = completado ? 'MATERIAL ENTREGADO' : 'MERCANCÍA EN TRÁNSITO';
                        accionHtml = `
                            <div class="d-flex align-items-center justify-content-end pe-3 py-1">
                                <span class="fw-bold me-3" style="color: ${color}; font-size: 0.7rem;">${texto}</span>
                                <button onclick="imprimirComprobante(${m.id})" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                    <i class="bi bi-printer"></i>
                                </button>
                                <button onclick="verDetalleGanancia(${m.id})" class="btn btn-link ms-2 text-decoration-none" style="color: #ceced2;">
                                    <i class="bi bi-graph-up-arrow fs-6"></i>
                                </button>
                            </div>`;
                    } 
                    else if (yaDespachado) {
                        // Acciones post-despacho: Reversar o elegir logística (Patio/Ruta)
                        accionHtml = `
                            <div class="d-flex align-items-center justify-content-end pe-3 py-1" style="gap: 10px;">
                                <button type="button" class="btn btn-sm d-flex align-items-center opacity-75-hover" style="background: transparent; border: none; color: #ff3b30; font-weight: 600; font-size: 0.7rem; letter-spacing: 0.5px;" onclick="confirmarReversaDespacho(${m.id})">
                                    <i class="bi bi-arrow-counterclockwise me-1" style="font-size: 0.9rem;"></i> REVERSAR
                                </button>
                                <button onclick="prepararModalPatio(${m.id}, ${m.almacen_origen_id})" class="btn d-flex align-items-center justify-content-center shadow-sm transition-ios" style="background: rgba(0, 122, 255, 0.1); color: #007aff; border: 1px solid rgba(0, 122, 255, 0.2); border-radius: 12px; font-weight: 700; height: 32px; padding: 0 15px;">
                                    <i class="bi bi-box-seam me-2" style="font-size: 0.8rem;"></i>
                                    <span style="font-size: 0.65rem; letter-spacing: 0.3px;">PATIO</span>
                                </button>
                                <button onclick="prepararModalReparto(${m.id}, ${m.almacen_origen_id})" class="btn d-flex align-items-center justify-content-center shadow-sm transition-ios" style="background: #1c1c1e; color: #fff; border: none; border-radius: 12px; font-weight: 700; height: 32px; padding: 0 15px;">
                                    <i class="bi bi-truck me-2" style="font-size: 0.8rem;"></i>
                                    <span style="font-size: 0.65rem; letter-spacing: 0.3px;">RUTA</span>
                                </button>
                            </div>`;
                    } 
                    else {
                        // Acción primaria: Despachar producto
                        accionHtml = `
                            <div class="pe-3 text-end py-1">
                                <button onclick="prepararDespacho(${m.id})" class="btn d-inline-flex align-items-center justify-content-center shadow-sm transition-ios" style="background: rgba(88, 86, 214, 0.12); color: #5856d6; border: 1px solid rgba(88, 86, 214, 0.25); border-radius: 12px; height: 32px; padding: 0 18px; font-weight: 700;">
                                    <i class="bi bi-file-earmark-check-fill me-2" style="font-size: 0.85rem;"></i> 
                                    <span style="font-size: 0.65rem; letter-spacing: 0.6px; text-transform: uppercase;">Despachar</span>
                                </button>
                            </div>`;
                    }
                    prodCol = `<b>${m.producto}</b><br><small class="text-primary font-monospace">${m.sku}</small>`;
                    cantCol = `<div class="text-center">${formatQty(m.cantidad, m.factor_conversion, m.unidad_reporte)}</div>`;
                }

                // Insertar los datos procesados en la fila del DataTable 
                // Se agrega la columna Cliente en la posición 3 (índice 2)
                tabla.row.add([
                    `<span class="ps-3 fw-bold text-secondary">#${m.id}</span>`,
                    `<span class="fw-bold text-primary">${m.folio_venta || '---'}</span>`,
                    `<span class="text-uppercase fw-semibold" style="color: #48484a; font-size: 0.75rem;">${m.cliente || '---'}</span>`,
                    `<span class="text-dark small">${m.fecha_format}</span>`,
                    prodCol,
                    cantCol,
                    `<div><span class="badge bg-light text-dark border small"><i class="bi bi-geo-alt me-1"></i>${m.origen}</span></div>`,
                    accionHtml
                ]);
            });

            // Dibujar la tabla con los nuevos datos
            tabla.draw();
        },
        // Ocultar loader siempre al finalizar la petición (éxito o error)
        complete: () => $('#loader').addClass('d-none')
    });
}