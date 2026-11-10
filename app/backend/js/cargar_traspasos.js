function cargarTraspasos() {
    const almacenId = document.getElementById('admin_filtro_almacen')?.value || '';
    const contenedorArribos = document.getElementById('contenedor-arribos');
    const contenedorEnvios = document.getElementById('contenedor-envios');

    // Fechas del filtro (si existen en el DOM)
    const fechaInicio = document.getElementById('filtro_fecha_inicio')?.value || '';
    const fechaFin = document.getElementById('filtro_fecha_fin')?.value || '';

    contenedorArribos.innerHTML = '<tr><td colspan="6" class="text-center">Buscando movimientos...</td></tr>';
    contenedorEnvios.innerHTML = '<tr><td colspan="5" class="text-center">Buscando movimientos...</td></tr>';

    const params = new URLSearchParams({
        action: 'resumen',
        almacen_id: almacenId
    });
    if (fechaInicio) params.append('fecha_inicio', fechaInicio);
    if (fechaFin) params.append('fecha_fin', fechaFin);

    fetch(`/cfsistem/app/controllers/traspasosController.php?${params.toString()}`)
        .then(r => r.json())
        .then(data => {

            // --- FUNCIÓN INTERNA PARA CONVERSIÓN VISUAL ---
            const formatearCantidad = (cant, factor, unidad) => {
                const c = parseFloat(cant);
                const f = parseFloat(factor) || 1;
                const u = unidad || 'Unid.';
                if (f > 1 && c >= f) {
                    const entero = Math.floor(c / f);
                    const resto = Math.round((c % f) * 100) / 100;
                    let texto = `<strong>${entero} ${u}</strong>`;
                    if (resto > 0) texto += ` + ${resto} pzas`;
                    return texto;
                }
                return `<strong>${c}</strong> pzas`;
            };

            // --- PARSEAR productos_lista del masivo ---
            const parsearProductosLista = (listaStr) => {
                if (!listaStr) return [];
                return listaStr.split('|').map(p => {
                    const [nombre, cantidad, factor, unidad] = p.split('§');
                    return {
                        nombre: nombre || '',
                        cantidad: parseFloat(cantidad) || 0,
                        factor: parseFloat(factor) || 1,
                        unidad: unidad || 'Unid.'
                    };
                });
            };

            // --- RENDER DE LA LISTA DE PRODUCTOS DEL MASIVO (desplegable) ---
            const renderProductosMasivo = (productosLista, idTraspaso) => {
                const productos = parsearProductosLista(productosLista);
                if (!productos.length) return '';

                const filas = productos.map(p => `
                    <tr>
                        <td class="text-start small">${p.nombre}</td>
                        <td class="text-end small">${formatearCantidad(p.cantidad, p.factor, p.unidad)}</td>
                    </tr>
                `).join('');

                return `
                    <div class="mt-1">
                        <a class="small text-decoration-none" data-bs-toggle="collapse" href="#prod-masivo-${idTraspaso}" role="button">
                            <i class="bi bi-chevron-down"></i> Ver ${productos.length} producto(s)
                        </a>
                        <div class="collapse mt-1" id="prod-masivo-${idTraspaso}">
                            <table class="table table-sm table-borderless mb-0" style="font-size:0.75rem;">
                                <tbody>${filas}</tbody>
                            </table>
                        </div>
                    </div>
                `;
            };

            // =========================================================
            // 1. LLENAR ARRIBOS (individuales + masivos)
            // =========================================================
            contenedorArribos.innerHTML = '';

            const arribos = data.arribos || [];
            if (arribos.length === 0) {
                contenedorArribos.innerHTML = '<tr><td colspan="6" class="text-center text-body-secondary">No hay mercancía pendiente de recibir.</td></tr>';
            } else {
                // Ordenar: masivos primero, luego por fecha desc
                arribos.sort((a, b) => {
                    if (a.tipo !== b.tipo) return a.tipo === 'masivo' ? -1 : 1;
                    return new Date(b.fecha) - new Date(a.fecha);
                });

                arribos.forEach(mov => {
                    if (mov.tipo === 'masivo') {
                        // ============================
                        // ARRIBO MASIVO
                        // ============================
                        const cantDisplay = formatearCantidad(
                            mov.cant_total,
                            mov.factor_conversion,
                            mov.unidad_reporte
                        );

                        contenedorArribos.innerHTML += `
                            <tr class="table-light">
                                <td>
                                    <span class="badge bg-primary me-1">MASIVO</span><br>
                                    <small class="text-body-secondary">${mov.fecha}</small>
                                </td>
                                <td>
                                    <div class="fw-bold">${mov.codigo_traspaso || ('#' + mov.id_traspaso_id)}</div>
                                    <div class="small text-body-secondary">
                                        ${mov.total_productos} producto(s) — de <b>${mov.origen}</b>
                                    </div>
                                    ${renderProductosMasivo(mov.productos_lista, mov.id_traspaso_id)}
                                </td>
                                <td class="text-primary">
                                    ${cantDisplay}<br>
                                    <small class="text-body-secondary" style="font-size:0.7rem">
                                        Total: ${mov.cant_total} pzas
                                    </small>
                                </td>
                                <td>${mov.origen} -> ${mov.destino}</td>
                                <td>${mov.enviado_por}</td>
                                <td>
                                    <button class="btn btn-success btn-sm mb-1 w-100"
                                        onclick="recibirTraspasoMasivo(${mov.id_traspaso_id})">
                                        <i class="bi bi-check2-all"></i> Recibir todo
                                    </button>
                                    <button class="btn btn-outline-danger btn-sm w-100"
                                        onclick="eliminarTraspasoMasivo(${mov.id_traspaso_id})">
                                        <i class="bi bi-trash"></i> Eliminar masivo
                                    </button>
                                </td>
                            </tr>
                        `;
                    } else {
                        // ============================
                        // ARRIBO INDIVIDUAL
                        // ============================
                        const cantDisplay = formatearCantidad(
                            mov.cantidad,
                            mov.factor_conversion,
                            mov.unidad_reporte
                        );

                        contenedorArribos.innerHTML += `
                            <tr>
                                <td>
                                    <span class="badge bg-secondary me-1">INDIVIDUAL</span><br>
                                    <small class="text-body-secondary">${mov.fecha}</small>
                                </td>
                                <td>
                                    <small class="fw-bold">${mov.sku || ''}</small><br>
                                    ${mov.producto}
                                </td>
                                <td class="text-primary">
                                    ${cantDisplay}<br>
                                    <small class="text-body-secondary" style="font-size:0.7rem">
                                        Total: ${mov.cantidad} pzas
                                    </small>
                                </td>
                                <td>${mov.origen} -> ${mov.destino}</td>
                                <td>${mov.enviado_por}</td>
                                <td>
                                    <button class="btn btn-success btn-sm mb-1 w-100"
                                        onclick="autorizarRecibo(${mov.id})">
                                        <i class="bi bi-check-circle"></i> Recibir
                                    </button>
                                    <button class="btn btn-danger btn-sm w-100"
                                        onclick="eliminarTraspaso(${mov.id})">
                                        <i class="bi bi-trash"></i> Eliminar
                                    </button>
                                </td>
                            </tr>
                        `;
                    }
                });
            }

            // =========================================================
            // 2. LLENAR ENVÍOS (individuales + masivos)
            // =========================================================
            contenedorEnvios.innerHTML = '';

            const envios = data.envios || [];
            if (envios.length === 0) {
                contenedorEnvios.innerHTML = '<tr><td colspan="5" class="text-center text-body-secondary">No se encontraron envíos realizados.</td></tr>';
            } else {
                // Ordenar: masivos primero, luego por fecha desc
                envios.sort((a, b) => {
                    if (a.tipo !== b.tipo) return a.tipo === 'masivo' ? -1 : 1;
                    return new Date(b.fecha) - new Date(a.fecha);
                });

                envios.forEach(mov => {
                    if (mov.tipo === 'masivo') {
                        // ============================
                        // ENVÍO MASIVO
                        // ============================
                        const cantDisplay = formatearCantidad(
                            mov.cant_total,
                            mov.factor_conversion,
                            mov.unidad_reporte
                        );

                        const badgeClass = mov.estado === 'Completado'
                            ? 'bg-success'
                            : (mov.estado === 'Cancelado' ? 'bg-secondary' : 'bg-warning text-dark');

                        const progreso = `${mov.recibidos || 0}/${mov.total_productos}`;

                        contenedorEnvios.innerHTML += `
                            <tr class="table-light">
                                <td>
                                    <span class="badge bg-primary me-1">MASIVO</span><br>
                                    <small class="text-body-secondary">${mov.fecha}</small>
                                </td>
                                <td>
                                    <div class="fw-bold">${mov.codigo_traspaso || ('#' + mov.id_traspaso_id)}</div>
                                    <div class="small text-body-secondary">
                                        ${mov.total_productos} producto(s) — a <b>${mov.destino}</b>
                                    </div>
                                    ${renderProductosMasivo(mov.productos_lista, mov.id_traspaso_id)}
                                </td>
                                <td>
                                    ${cantDisplay}<br>
                                    <small class="text-body-secondary" style="font-size:0.7rem">
                                        Total: ${mov.cant_total} pzas
                                    </small>
                                </td>
                                <td>${mov.destino}</td>
                                <td>
                                    <span class="badge ${badgeClass}">${mov.estado}</span>
                                    <br>
                                    <small class="text-body-secondary" style="font-size:0.7rem">
                                        <i class="bi bi-box-seam"></i> ${progreso} recibidos
                                    </small>
                                </td>
                            </tr>
                        `;
                    } else {
                        // ============================
                        // ENVÍO INDIVIDUAL
                        // ============================
                        const cantDisplay = formatearCantidad(
                            mov.cantidad,
                            mov.factor_conversion,
                            mov.unidad_reporte
                        );
                        const badgeClass = mov.estado === 'Completado'
                            ? 'bg-success'
                            : 'bg-warning text-dark';

                        contenedorEnvios.innerHTML += `
                            <tr>
                                <td>
                                    <span class="badge bg-secondary me-1">INDIVIDUAL</span><br>
                                    <small class="text-body-secondary">${mov.fecha}</small>
                                </td>
                                <td>${mov.producto}</td>
                                <td>
                                    ${cantDisplay}<br>
                                    <small class="text-body-secondary" style="font-size:0.7rem">
                                        Total: ${mov.cantidad} pzas
                                    </small>
                                </td>
                                <td>${mov.destino}</td>
                                <td><span class="badge ${badgeClass}">${mov.estado}</span></td>
                            </tr>
                        `;
                    }
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            contenedorArribos.innerHTML = '<tr><td colspan="6" class="text-center text-danger">Error al cargar datos.</td></tr>';
            contenedorEnvios.innerHTML = '<tr><td colspan="5" class="text-center text-danger">Error al cargar datos.</td></tr>';
        });
}
async function eliminarTraspaso(movimientoId) {
    // 1. Confirmación antes de eliminar
    const confirmacion = await Swal.fire({
        title: '¿Eliminar traspaso?',
        text: 'El stock regresará al almacén de origen. Esta acción no se puede deshacer.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        reverseButtons: true
    });

    if (!confirmacion.isConfirmed) return;

    // 2. Petición al backend
    const fd = new FormData();
    fd.append('movimiento_id', movimientoId);

    try {
        const resp = await fetch('/cfsistem/app/backend/almacen/eliminar_traspaso.php', {
            method: 'POST',
            body: fd
        });

        const data = await resp.json();

        if (data.status === 'success') {
            Swal.fire({
                title: 'Eliminado',
                text: data.message,
                icon: 'success',
                confirmButtonColor: '#198754'
            }).then(() => location.reload());
        } else {
            Swal.fire({
                title: 'Error',
                text: data.message,
                icon: 'error',
                confirmButtonColor: '#d33'
            });
        }
    } catch (err) {
        console.error('Error al eliminar traspaso:', err);
        Swal.fire({
            title: 'Error técnico',
            text: 'No se pudo conectar con el servidor.',
            icon: 'error',
            confirmButtonColor: '#d33'
        });
    }
} async function eliminarTraspasoMasivo(movimientoId) {
    // 1. Confirmación antes de eliminar
    const confirmacion = await Swal.fire({
        title: '¿Eliminar traspaso?',
        text: 'El stock regresará al almacén de origen. Esta acción no se puede deshacer.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        reverseButtons: true
    });

    if (!confirmacion.isConfirmed) return;

    // 2. Petición al backend
    const fd = new FormData();
    fd.append('id_traspaso_id', movimientoId);

    try {
        const resp = await fetch('/cfsistem/app/backend/almacen/eliminar_traspaso_masivo.php', {
            method: 'POST',
            body: fd
        });

        const data = await resp.json();

        if (data.status === 'success') {
            Swal.fire({
                title: 'Eliminado',
                text: data.message,
                icon: 'success',
                confirmButtonColor: '#198754'
            }).then(() => location.reload());
        } else {
            Swal.fire({
                title: 'Error',
                text: data.message,
                icon: 'error',
                confirmButtonColor: '#d33'
            });
        }
    } catch (err) {
        console.error('Error al eliminar traspaso:', err);
        Swal.fire({
            title: 'Error técnico',
            text: 'No se pudo conectar con el servidor.',
            icon: 'error',
            confirmButtonColor: '#d33'
        });
    }
}