<?php
/* ============================================================
   COMPONENTE: Visor de comprobantes (iframe en modal propio)
   ------------------------------------------------------------
   Incluye HTML + CSS + JS. No depende de Bootstrap.
   Cierra con: botón X, clic afuera y tecla Esc.

   IMPORTAR (en la vista, antes de </body>):
       <?php require_once __DIR__ . '/componentes/visorComprobanteModal.php'; ?>

   USO:
       VisorCP.abrir(url, titulo, { tipo: 'ticket' | 'normal', alCerrar: fn });
       VisorCP.cerrar();

   IDs únicos: vcpOverlay, vcpCaja, vcpTitulo, vcpBtnCerrar, vcpIframe
   Clases únicas: .vcp-*   |   Global: window.VisorCP
   ============================================================ */
?>

<!-- ===================== ESTILOS ===================== -->
<style>
    .vcp-overlay {
        position: fixed;
        top: 0;
        right: 0;
        bottom: 0;
        left: 0;
        z-index: 20000;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 16px;
        background: rgba(0, 0, 0, .55);
    }

    .vcp-overlay.vcp-visible {
        display: flex;
    }

    .vcp-caja {
        background: #fff;
        color: #212529;
        width: 100%;
        max-width: 1100px;
        max-height: 94vh;
        border-radius: 16px;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        box-shadow: 0 20px 50px rgba(0, 0, 0, .35);
    }

    .vcp-caja.vcp-ticket {
        max-width: 480px;
    }

    .vcp-barra {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 8px 16px;
        border-bottom: 1px solid #dee2e6;
        background: #fff;
    }

    .vcp-titulo {
        font-weight: 700;
        font-size: .95rem;
    }

    .vcp-cerrar {
        border: 0;
        background: transparent;
        font-size: 1.8rem;
        line-height: 1;
        cursor: pointer;
        color: #6c757d;
        padding: 0 6px;
    }

    .vcp-cerrar:hover {
        color: #000;
    }

    .vcp-iframe {
        width: 100%;
        height: 80vh;
        border: 0;
        display: block;
        background: #fff;
    }

    body.vcp-sin-scroll {
        overflow: hidden;
    }
</style>

<!-- ===================== HTML ===================== -->
<div id="vcpOverlay" class="vcp-overlay" role="dialog" aria-modal="true" aria-labelledby="vcpTitulo">
    <div id="vcpCaja" class="vcp-caja">
        <div class="vcp-barra">
            <span id="vcpTitulo" class="vcp-titulo">Comprobante</span>
            <button type="button" id="vcpBtnCerrar" class="vcp-cerrar" aria-label="Cerrar">&times;</button>
        </div>
        <iframe id="vcpIframe" class="vcp-iframe" src="about:blank" title="Comprobante"></iframe>
    </div>
</div>

<!-- ===================== JAVASCRIPT ===================== -->
<script>
    (function () {
        'use strict';

        var alCerrarCb = null;

        function el(id) { return document.getElementById(id); }

        // Mueve el overlay a <body> para evitar problemas de z-index/contenedores
        function prepararOverlay() {
            var ov = el('vcpOverlay');
            if (ov && ov.parentNode !== document.body) {
                document.body.appendChild(ov);
            }
            return ov;
        }

        function onEscIframe(e) {
            if (e.key === 'Escape') cerrar();
        }

        function cerrar() {
            var ov = el('vcpOverlay');
            if (!ov || !ov.classList.contains('vcp-visible')) return;

            ov.classList.remove('vcp-visible');
            document.body.classList.remove('vcp-sin-scroll');

            var iframe = el('vcpIframe');
            if (iframe) {
                iframe.onload = null;
                iframe.src = 'about:blank';
            }

            var cb = alCerrarCb;
            alCerrarCb = null;
            if (typeof cb === 'function') cb();
        }

        function abrir(url, titulo, opciones) {
            opciones = opciones || {};

            var ov = prepararOverlay();
            if (!ov) {
                console.error('VisorCP: no se encontró #vcpOverlay');
                return;
            }

            el('vcpTitulo').textContent = titulo || 'Comprobante';
            el('vcpCaja').classList.toggle('vcp-ticket', opciones.tipo === 'ticket');
            alCerrarCb = (typeof opciones.alCerrar === 'function') ? opciones.alCerrar : null;

            var iframe = el('vcpIframe');
            iframe.onload = function () {
                try {
                    iframe.contentWindow.document.addEventListener('keydown', onEscIframe);
                } catch (err) { /* otro dominio: se ignora */ }
            };
            iframe.src = url;

            document.body.classList.add('vcp-sin-scroll');
            ov.classList.add('vcp-visible');
        }

        // Eventos por delegación (funcionan aunque el HTML se inyecte después)
        document.addEventListener('mousedown', function (e) {
            if (e.target && e.target.id === 'vcpOverlay') cerrar();      // clic afuera
        });
        document.addEventListener('click', function (e) {
            if (e.target && e.target.id === 'vcpBtnCerrar') cerrar();    // botón X
        });
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') cerrar();                            // Esc
        });

        window.VisorCP = { abrir: abrir, cerrar: cerrar };
    })();
</script>