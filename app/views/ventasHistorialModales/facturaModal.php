<style>
    /* ═══════════════════════════════════════════════════════════
       MODAL AGREGAR FACTURA — Estilos aislados
       ═══════════════════════════════════════════════════════════ */

    /* ─── Z-INDEX: siempre al frente ─── */
    #modalAgregarFactura {
        z-index: 10080 !important;
    }


    /* ─── RESET: no heredar uppercase del body ─── */
    #modalAgregarFactura,
    #modalAgregarFactura * {
        text-transform: none;
    }

    #modalAgregarFactura .text-uppercase,
    #modalAgregarFactura .ls-wide {
        text-transform: uppercase !important;
    }

    /* ─── CONTENEDOR ─── */
    #modalAgregarFactura .modal-dialog {
        max-width: 400px;
    }

    #modalAgregarFactura .modal-content {
        border: none;
        border-radius: 16px;
        overflow: hidden;
        box-shadow:
            0 20px 60px rgba(15, 23, 42, 0.18),
            0 8px 24px rgba(15, 23, 42, 0.12);
        background: #ffffff;
    }

    [data-bs-theme="dark"] #modalAgregarFactura .modal-content {
        background: #1e293b;
        box-shadow:
            0 20px 60px rgba(0, 0, 0, 0.5),
            0 8px 24px rgba(0, 0, 0, 0.35);
    }

    /* ─── HEADER ─── */
    #modalAgregarFactura .modal-header {
        padding: 20px 24px 8px;
        background: transparent;
        border: none;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    #modalAgregarFactura .modal-title {
        font-size: 1.05rem;
        font-weight: 700;
        letter-spacing: -0.01em;
        color: #0f172a;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    [data-bs-theme="dark"] #modalAgregarFactura .modal-title {
        color: #f1f5f9;
    }

    /* Icono con cápsula suave */
    #modalAgregarFactura .modal-title i {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 34px;
        height: 34px;
        border-radius: 10px;
        background: linear-gradient(135deg, #e0e7ff 0%, #c7d2fe 100%);
        color: #4f46e5 !important;
        font-size: 1rem;
        box-shadow:
            inset 0 1px 0 rgba(255, 255, 255, 0.8),
            0 1px 3px rgba(79, 70, 229, 0.15);
    }

    [data-bs-theme="dark"] #modalAgregarFactura .modal-title i {
        background: linear-gradient(135deg, rgba(99, 102, 241, .3) 0%, rgba(99, 102, 241, .15) 100%);
        color: #a5b4fc !important;
    }

    /* Botón cerrar */
    #modalAgregarFactura .btn-close {
        opacity: 0.5;
        transition: opacity 0.2s ease, transform 0.2s ease;
        background-size: 0.75em;
    }

    #modalAgregarFactura .btn-close:hover {
        opacity: 1;
        transform: rotate(90deg);
    }

    /* ─── BODY ─── */
    #modalAgregarFactura .modal-body {
        padding: 8px 24px 16px;
    }

    /* ─── LABELS ─── */
    #modalAgregarFactura .form-label {
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.08em;
        color: #64748b;
        margin-bottom: 6px;
        display: block;
    }

    [data-bs-theme="dark"] #modalAgregarFactura .form-label {
        color: #94a3b8;
    }

    /* ─── INPUTS ─── */
    #modalAgregarFactura .form-control {
        padding: 10px 16px;
        font-size: 0.88rem;
        font-weight: 500;
        color: #0f172a;
        background: #f8fafc;
        border: 1.5px solid #e2e8f0;
        border-radius: 10px;
        transition: all 0.2s ease;
        box-shadow: none;
    }

    #modalAgregarFactura .form-control::placeholder {
        color: #94a3b8;
        font-weight: 400;
    }

    #modalAgregarFactura .form-control:focus {
        background: #ffffff;
        border-color: #6366f1;
        box-shadow:
            0 0 0 3px rgba(99, 102, 241, 0.12),
            inset 0 1px 0 rgba(255, 255, 255, 0.6);
        outline: none;
    }

    [data-bs-theme="dark"] #modalAgregarFactura .form-control {
        background: #0f172a;
        border-color: #334155;
        color: #f1f5f9;
    }

    [data-bs-theme="dark"] #modalAgregarFactura .form-control:focus {
        background: #0f172a;
        border-color: #818cf8;
        box-shadow: 0 0 0 3px rgba(129, 140, 248, 0.2);
    }

    /* ─── FOOTER ─── */
    #modalAgregarFactura .modal-footer {
        padding: 8px 24px 22px;
        background: transparent;
        border: none;
        display: flex;
        gap: 10px;
    }

    /* ─── BOTONES ─── */
    #modalAgregarFactura .btn {
        padding: 10px 18px;
        font-size: 0.85rem;
        font-weight: 600;
        letter-spacing: 0.01em;
        border-radius: 10px;
        border: none;
        transition: all 0.2s ease;
        position: relative;
        overflow: hidden;
    }

    /* Botón Cancelar */
    #modalAgregarFactura .btn-light {

        color: #475569;
    }

    #modalAgregarFactura .btn-light:hover {
        background: #e2e8f0;
        color: #334155;
        transform: translateY(-1px);
    }

    #modalAgregarFactura .btn-light:active {
        transform: translateY(0) scale(0.98);
    }

    [data-bs-theme="dark"] #modalAgregarFactura .btn-light {
        background: #334155;
        color: #cbd5e1;
    }

    [data-bs-theme="dark"] #modalAgregarFactura .btn-light:hover {
        background: #475569;
        color: #f1f5f9;
    }

    /* Botón Guardar (primario) */
    #modalAgregarFactura .btn-primary {
        background: linear-gradient(180deg, #6366f1 0%, #4f46e5 100%);
        color: #ffffff;
        box-shadow:
            0 2px 6px rgba(79, 70, 229, 0.28),
            inset 0 1px 0 rgba(255, 255, 255, 0.25);
    }

    #modalAgregarFactura .btn-primary:hover {
        background: linear-gradient(180deg, #4f46e5 0%, #4338ca 100%);
        transform: translateY(-1px);
        box-shadow:
            0 6px 16px rgba(79, 70, 229, 0.38),
            inset 0 1px 0 rgba(255, 255, 255, 0.3);
    }

    #modalAgregarFactura .btn-primary:active {
        transform: translateY(0) scale(0.98);
        box-shadow:
            0 2px 4px rgba(79, 70, 229, 0.3),
            inset 0 1px 0 rgba(255, 255, 255, 0.2);
    }

    /* Shimmer sutil en hover */
    #modalAgregarFactura .btn-primary::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(120deg,
                transparent 30%,
                rgba(255, 255, 255, 0.25) 50%,
                transparent 70%);
        transition: left 0.6s ease;
        pointer-events: none;
    }

    #modalAgregarFactura .btn-primary:hover::before {
        left: 100%;
    }

    /* ─── ANIMACIÓN DE ENTRADA ─── */
    #modalAgregarFactura.fade .modal-dialog {
        transform: translateY(-20px) scale(0.96);
        transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
    }

    #modalAgregarFactura.show .modal-dialog {
        transform: translateY(0) scale(1);
    }

    /* ─── RESPONSIVE ─── */
    @media (max-width: 576px) {
        #modalAgregarFactura .modal-dialog {
            max-width: calc(100% - 24px);
            margin: 12px auto;
        }

        #modalAgregarFactura .modal-header {
            padding: 18px 20px 6px;
        }

        #modalAgregarFactura .modal-body {
            padding: 6px 20px 14px;
        }

        #modalAgregarFactura .modal-footer {
            padding: 6px 20px 18px;
        }
    }

    /* El contenedor raíz de SweetAlert2 */
    .swal2-container {
        z-index: 99999 !important;
    }

    /* El backdrop de SweetAlert */
    .swal2-container.swal2-backdrop-show {
        z-index: 99999 !important;
    }

    /* El popup en sí */
    .swal2-popup {
        z-index: 100000 !important;
        position: relative;
    }

    /* El toast (notificaciones sin backdrop) */
    .swal2-toast-shown .swal2-container {
        z-index: 99999 !important;
    }
</style>
<div class="modal fade" id="modalAgregarFactura" tabindex="-1" aria-labelledby="modalAgregarFacturaLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content rounded-3 border-0 shadow">

            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold card-title-text fs-5" id="modalAgregarFacturaLabel">
                    <i class="bi bi-file-earmark-plus text-primary me-2"></i>Nueva Factura
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body py-3">
                <form id="formFactura" onsubmit="event.preventDefault(); ">
                    <div class="mb-2">
                        <input type="hidden" class="form-control rounded-pill border-secondary border-opacity-25"
                            id="id_venta_factura">

                        <label for="folio-factura"
                            class="form-label fw-bold small text-body-secondary text-uppercase ls-wide">
                            Folio o Número de Factura
                        </label>
                        <input type="text" class="form-control rounded-pill border-secondary border-opacity-25"
                            id="folio-factura" placeholder="Ej. FACT-12345" required autocomplete="off">
                    </div>
                </form>
            </div>

            <div class="modal-footer border-top-0 pt-0 d-flex gap-2">
                <button type="button" class="btn btn-sm btn-light rounded-pill flex-grow-1 fw-bold text-body-secondary"
                    data-bs-dismiss="modal">
                    Cancelar
                </button>
                <button type="button" class="btn btn-sm btn-primary rounded-pill flex-grow-1 fw-bold"
                    onclick="agregarFactura ($('#id_venta_factura').val(),$('#folio-factura').val())">
                    Guardar
                </button>
            </div>

        </div>
    </div>
</div>
<script>
    // PASO 1: Esta función se dispara al dar click al botón de la tabla (abre el modal)
    function modalFactura(id, factura) {
        const modalElement = document.getElementById('modalAgregarFactura');
        const folioInput = document.getElementById("folio-factura");

        // Limpiamos el input y errores previos por si acaso
        folioInput.value = factura;
        folioInput.classList.remove("is-invalid");

        // Guardamos el ID de la venta/viaje en el modal para no perderlo
        modalElement.setAttribute('data-id-actual', id);
        document.getElementById('id_venta_factura').value = id;

        // Abrimos el modal programáticamente con Bootstrap
        const modalInstance = new bootstrap.Modal(modalElement);
        modalInstance.show();
    }

    // PASO 2: Esta función se dispara al dar click en "Guardar" dentro del modal


    // Tu// Función final encargada del backend
    async function agregarFactura(id, folio) {
        console.log(`Guardando en BD -> ID: ${id}, Folio Factura: ${folio}`);

        // 1. Creamos el objeto FormData y le inyectamos los datos que necesita el controlador PHP
        const data = new FormData();
        data.append('venta_id', id);
        data.append('factura', folio);

        try {
            // Asumiendo que URL_CONTROLLER es tu constante global (ej: '../controllers/ventasController.php')
            const res = await fetch(
                `/cfsistem/app/controllers/ventasHistorialController.php?action=guardarFactura`, {
                method: 'POST',
                body: data // Enviamos el FormData con los valores
            });

            // Verificamos si la respuesta del servidor es un JSON válido
            const result = await res.json();

            if (result.status === 'success') {

                // Ojo: Si usaste la instancia limpia que te pasé en el paso anterior, 
                // puedes cerrar el modal de Bootstrap 5 así si no tienes 'modalObj' global:
                const modalElement = document.getElementById('modalAgregarFactura');
                const modalInstance = bootstrap.Modal.getInstance(modalElement);
                if (modalInstance) modalInstance.hide();

                // Recargamos la tabla principal de ventas
                if (typeof getVentas === 'function') getVentas();

                // Alerta de éxito con SweetAlert2
                Swal.fire({
                    title: '¡Listo!',
                    text: 'Factura guardada correctamente',
                    icon: 'success',
                    timer: 1000, // Subí a 1000ms (1 segundo) para que el usuario alcance a notar la palomita de éxito
                    showConfirmButton: false
                });

                // 🔥 Volver a abrir automáticamente el detalle si es necesario
                setTimeout(() => {
                    // Usamos el 'id' que entró originalmente por parámetro a esta función
                    if (typeof verDetalle === 'function') {
                        verDetalle(id);
                    }
                }, 1005);

            } else {
                // Aquí manejamos errores devueltos por el backend (Excepciones del try/catch de tu PHP)
                Swal.fire('No se pudo guardar', result.message || 'Error desconocido', 'error');
            }

        } catch (e) {
            console.error("Error al procesar la factura:", e);
            Swal.fire('Error Técnico', 'Hubo un problema de conexión con el servidor', 'error');
        }
    }

    // Esta es la función que necesitas que se ejecute:
</script>