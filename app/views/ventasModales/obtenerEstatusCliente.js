function consultarEstatusFinanciero(id) {
    const $widget = elements.widgetEstadoCuenta;
    const $lista = elements.listaMovimientos;
    const $header = elements.widgetHeader;

    if (!$widget || !$lista) return;

    // Cancelar petición anterior si existe
    if (currentController) {
        currentController.abort();
    }

    $widget.style.display = 'block';
    $lista.innerHTML =
        `<div class="text-center py-3"><div class="spinner-border spinner-border-sm text-secondary"></div></div>`;

    // Usar AbortController para cancelar peticiones
    currentController = new AbortController();

    fetch(`/cfsistem/app/controllers/ventasController.php?action=obtenerEstatusCliente&id=${id}`, {
            signal: currentController.signal
        })
        .then(r => r.json())
        .then(data => {
            currentController = null;
            if (!data || data.nombre_comercial === undefined) throw new Error("Datos no encontrados");

            const res = data;
            const saldo = parseFloat(res.saldo_neto || 0);
            const condicion = res.estatus_financiero || 'AL DIA';

            // --- 1. LÓGICA DEL SWITCH DE SALDO A FAVOR ---
            const saldoAFavor = parseFloat(res.saldo_a_favor || 0);
            saldoDisponibleCliente = saldoAFavor; // Actualizamos la variable global

            const $panelSaldo = elements.contenedorSaldoFavor;
            const $chkSaldo = elements.checkUsarSaldo;

            if (saldoAFavor > 0) {
                $panelSaldo.style.display = 'block'; // Muestra el contenedor verde
            } else {
                $panelSaldo.style.display = 'none'; // Lo oculta si no hay saldo
                $chkSaldo.checked = false; // Resetea el switch
                toggleSaldoInput(); // Oculta el input de cantidad
            }
            // ----------------------------------------------

            // --- Lógica de Colores del Header ---
            $header.className = '';
            if (condicion === 'CON DEUDA') {
                $header.classList.add('widget-header-deuda');
            } else if (condicion === 'SALDO A FAVOR') {
                $header.classList.add('widget-header-ok');
            } else {
                $header.classList.add('widget-header-neutral');
            }

            // Actualizar montos principales
            elements.lblSaldoTotal.textContent = _fmt.format(Math.abs(saldo));
            elements.txtUltimaCarga.textContent =
                `Corte: ${new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}`;

            // Badge dinámico - cache de iconos
            const iconMap = {
                'CON DEUDA': 'bi-exclamation-triangle-fill',
                'SALDO A FAVOR': 'bi-plus-circle-fill',
                'default': 'bi-check-circle-fill'
            };
            const icon = iconMap[condicion] || iconMap.default;

            elements.widgetBadge.innerHTML = `
                <span style="background:rgba(255,255,255,0.2);color:white;font-size:0.6rem;font-weight:700;padding:3px 10px;border-radius:20px;">
                    <i class="bi ${icon} me-1"></i>${condicion}
                </span>`;

            // Resumen en el cuerpo - cache de strings
            const saldoEnContra = _fmt.format(res.saldo_en_contra || 0);
            const saldoAFavorFmt = _fmt.format(res.saldo_a_favor || 0);
            const estadoColor = saldo > 0 ? 'text-danger' : 'text-success';
            const estadoTexto = condicion === 'CON DEUDA' ? 'Pendiente de Pago' :
                (condicion === 'SALDO A FAVOR' ? 'Crédito Disponible' : 'Sin Adeudos');

            $lista.innerHTML = `
                <div class="p-2 small">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="text-muted">Por Pagar:</span>
                        <span class="fw-bold text-danger">${saldoEnContra}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="text-muted">A Favor:</span>
                        <span class="fw-bold text-success">${saldoAFavorFmt}</span>
                    </div>
                    <hr class="my-1" style="opacity:0.1">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted">Estado:</span>
                        <span class="fw-bold ${estadoColor}">${estadoTexto}</span>
                    </div>
                </div>`;
        })
        .catch(err => {
            currentController = null;
            if (err.name !== 'AbortError') {
                console.error("Error:", err);
                $lista.innerHTML =
                    `<div class="text-center p-2 text-danger small">Error al consultar estatus</div>`;
            }
        });
}
