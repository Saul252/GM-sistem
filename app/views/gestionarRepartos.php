<?php
// Recibimos el folio por URL.
$folio_viaje = $_GET['folio'] ?? '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Cf System - Gestión de Ruta</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    
    <style>
        :root {
            --ios-blue: #007AFF;
            --ios-bg: #F2F2F7;
            --card-shadow: 0 8px 20px rgba(0,0,0,0.08);
        }

        body {
            background-color: var(--ios-bg);
            font-family: -apple-system, BlinkMacSystemFont, "SF Pro Display", sans-serif;
            padding-bottom: 30px;
        }

        /* Header con efecto Blur */
        .header-ios {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            position: sticky;
            top: 0;
            z-index: 1000;
            border-bottom: 1px solid rgba(0,0,0,0.1);
            padding: 12px 16px;
        }

        /* Cards Estilizadas */
        .card-entrega {
            background: #fff;
            border-radius: 20px;
            border: none;
            box-shadow: var(--card-shadow);
            margin-bottom: 15px;
            transition: transform 0.2s;
        }

        .card-entrega.visitado {
            border-left: 8px solid #34C759;
            opacity: 0.8;
        }

        /* Botón de Cámara */
        .btn-camera {
            background-color: #fff;
            color: var(--ios-blue);
            border: 2px dashed #d1d1d6;
            border-radius: 18px;
            padding: 25px;
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .preview-img {
            width: 100%;
            border-radius: 18px;
            margin-top: 15px;
            display: none;
            max-height: 200px;
            object-fit: cover;
        }

        .btn-primary-ios {
            background: var(--ios-blue);
            border: none;
            border-radius: 14px;
            padding: 14px;
            font-weight: 600;
            color: white;
        }

        .info-label {
            font-size: 0.7rem;
            color: #8e8e93;
            text-transform: uppercase;
            font-weight: 600;
        }
    </style>
</head>
<body>

<div class="header-ios d-flex align-items-center justify-content-between">
    <div class="d-flex align-items-center">
        <a href="javascript:history.back()" class="text-decoration-none me-3">
            <i class="bi bi-chevron-left fs-4" style="color: var(--ios-blue);"></i>
        </a>
        <div>
            <h1 class="h6 mb-0 fw-bold">Ruta de Reparto</h1>
            <small class="text-muted"><?php echo htmlspecialchars($folio_viaje); ?></small>
        </div>
    </div>
    <button class="btn btn-light rounded-circle shadow-sm" onclick="cargarEntregas()">
        <i class="bi bi-arrow-clockwise text-primary"></i>
    </button>
</div>

<div class="container mt-3" id="contenedor-entregas">
    <div class="text-center py-5">
        <div class="spinner-border text-primary" role="status"></div>
    </div>
</div>

<div class="modal fade" id="modalEvidencia" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered mx-3">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 25px;">
            <div class="modal-header border-0 pb-0">
                <div class="d-flex justify-content-between w-100 align-items-center">
                    <h5 class="modal-title fw-bold">Finalizar Entrega</h5>
                    <span class="badge bg-light text-muted border rounded-pill px-3 py-2" style="font-size: 0.7rem;">
                        ID MOV: <span id="m_id_visible">0</span>
                    </span>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            
            <form id="formEvidencia">
                <input type="hidden" name="id_movimiento" id="m_mov_id">
                <input type="hidden" name="id_venta" id="m_venta_id">
                <input type="hidden" name="vehiculo_id" id="m_vehiculo_id">
                <input type="hidden" name="action" value="subir_evidencia_reparto">

                <div class="modal-body">
                    <div class="mb-3 p-3 rounded-4" style="background-color: #f2f2f7;">
                        <div class="info-label">Cliente / Folio de Venta</div>
                        <div id="m_cliente_full" class="fw-bold mb-2"></div>
                        <div class="info-label">Dirección de Entrega</div>
                        <div id="m_direccion_full" class="small text-muted"></div>
                    </div>

                    <div class="mb-3">
                        <label class="info-label">Estado de la Visita</label>
                        <select name="estatus_entrega" class="form-select border-0 bg-light rounded-3">
                            <option value="Entregado">Entregado Total</option>
                            <option value="Parcial">Entrega Parcial</option>
                            <option value="Rechazado">Rechazado por Cliente</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="info-label">Fotografía de Evidencia</label>
                        <button type="button" class="btn-camera" onclick="document.getElementById('input-foto').click()">
                            <i class="bi bi-camera-fill fs-1"></i>
                            <span class="fw-bold small">Tomar Foto</span>
                        </button>
                        <input type="file" name="evidencia_foto" id="input-foto" accept="image/*" capture="environment" class="d-none" onchange="previewImagen(this)">
                        <img id="img-preview" class="preview-img">
                    </div>

                    <div class="mb-2">
                        <label class="info-label">Observaciones</label>
                        <textarea name="comentario" class="form-control border-0 bg-light rounded-3" rows="2" placeholder="Notas opcionales..."></textarea>
                    </div>
                </div>

                <div class="modal-footer border-0 pt-0">
                    <button type="submit" class="btn btn-primary-ios w-100" id="btnGuardar">Guardar y Finalizar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
const FOLIO = "<?php echo $folio_viaje; ?>";
const API_URL = "/cfsistem/app/controllers/gestionarRepartoController.php";
let datosTemporales = []; 

$(document).ready(() => {
    cargarEntregas();
});

function cargarEntregas() {
    fetch(`${API_URL}?action=get_entregas_folio&folio=${FOLIO}`)
        .then(res => res.json())
        .then(res => {
            const container = document.getElementById('contenedor-entregas');
            container.innerHTML = '';
            datosTemporales = res.data || [];

            if(datosTemporales.length === 0) {
                container.innerHTML = '<div class="text-center py-5 text-muted">No hay paradas pendientes.</div>';
                return;
            }

            datosTemporales.forEach((item, index) => {
                const estado = (item.estado_punto || 'pendiente').toLowerCase();
                const esVisitado = estado === 'visitado';
                
                container.innerHTML += `
                    <div class="card card-entrega ${esVisitado ? 'visitado' : ''} animate__animated animate__fadeIn">
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="badge rounded-pill ${esVisitado ? 'bg-success-subtle text-success' : 'bg-primary-subtle text-primary'}">
                                    ${estado}
                                </span>
                                <span class="small text-muted">Venta: ${item.folio_venta || 'S/F'}</span>
                            </div>
                            <h6 class="fw-bold mb-1">${item.cliente || 'Cliente'}</h6>
                            <p class="small text-muted mb-3"><i class="bi bi-geo-alt me-1"></i>${item.direccion_entrega || 'Sin dirección'}</p>
                            
                            <div class="p-2 rounded-3 mb-3 bg-light" style="font-size: 0.85rem;">
                                <strong>Material:</strong> ${item.producto_nombre} <br>
                                <strong>Cantidad:</strong> ${item.cantidad} ${item.um || ''}
                            </div>

                            ${!esVisitado ? 
                                `<button class="btn btn-primary-ios w-100" onclick="abrirModalPorIndex(${index})">
                                    Reportar Entrega
                                 </button>` : 
                                `<div class="text-center text-success fw-bold small"><i class="bi bi-check-circle-fill"></i> Completado</div>`
                            }
                        </div>
                    </div>`;
            });
        })
        .catch(err => {
            console.error("Error al cargar:", err);
            document.getElementById('contenedor-entregas').innerHTML = '<div class="alert alert-danger">Error de conexión.</div>';
        });
}

function abrirModalPorIndex(index) {
    const data = datosTemporales[index];
    if(!data) return;

    // Detectamos el ID (trp.id es el que necesitamos para el update del punto)
    const idReal = data.id_movimiento || data.id_punto || data.id || 0;

    // Asignación a campos ocultos y visuales
    document.getElementById('m_mov_id').value = idReal;
    document.getElementById('m_id_visible').innerText = idReal;
    
    document.getElementById('m_venta_id').value = data.id_venta || 0;
    document.getElementById('m_vehiculo_id').value = data.vehiculo_id || 0; 
    document.getElementById('m_cliente_full').innerText = `${data.cliente || 'S/N'} (${data.folio_venta || 'S/F'})`;
    document.getElementById('m_direccion_full').innerText = data.direccion_entrega || 'Sin dirección';
    
    // Limpieza de formulario
    document.getElementById('formEvidencia').reset();
    document.getElementById('img-preview').style.display = 'none';
    
    // Verificación de ID válido
    const btn = document.getElementById('btnGuardar');
    if(idReal == 0 || idReal === undefined) {
        btn.disabled = true;
        Swal.fire('Error de Datos', 'Esta parada no tiene un ID de ruta válido.', 'error');
    } else {
        btn.disabled = false;
    }

    const myModal = new bootstrap.Modal(document.getElementById('modalEvidencia'));
    myModal.show();
}

function previewImagen(input) {
    const preview = document.getElementById('img-preview');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = (e) => {
            preview.src = e.target.result;
            preview.style.display = 'block';
        }
        reader.readAsDataURL(input.files[0]);
    }
}

document.getElementById('formEvidencia').onsubmit = function(e) {
    e.preventDefault();
    const btn = document.getElementById('btnGuardar');
    const formData = new FormData(this);

    // --- DEBUG: Ver que estamos mandando realmente ---
    console.log("--- ENVIANDO DATOS AL SERVIDOR ---");
    for (let [key, value] of formData.entries()) {
        console.log(`${key}: ${value instanceof File ? value.name : value}`);
    }

    // Validación de foto
    if(!document.getElementById('input-foto').files[0]) {
        Swal.fire('Foto Necesaria', 'Por favor, captura la evidencia de entrega.', 'warning');
        return;
    }

    // Validación de ID
    if(!formData.get('id_movimiento') || formData.get('id_movimiento') == "0") {
        Swal.fire('Error de ID', 'No se detectó un ID de movimiento válido.', 'error');
        return;
    }

    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Procesando...';

    // CORRECCIÓN: Inyectamos la acción en la URL para que el controlador no se pierda
    fetch(`${API_URL}?action=subir_evidencia_reparto`, {
        method: 'POST',
        body: formData
    })
    .then(async res => {
        const text = await res.text();
        try {
            return JSON.parse(text);
        } catch(e) {
            console.error("Respuesta no válida del servidor:", text);
            throw new Error("El servidor respondió algo que no es JSON. Revisa la consola.");
        }
    })
    .then(res => {
        if(res.success) {
            Swal.fire({ icon: 'success', title: '¡Hecho!', text: res.message, timer: 1500, showConfirmButton: false });
            
            // Cerrar modal limpiamente
            const modalEl = document.getElementById('modalEvidencia');
            const instance = bootstrap.Modal.getInstance(modalEl);
            if(instance) instance.hide();
            
            cargarEntregas(); // Recargar la lista
        } else {
            throw new Error(res.message || "Error desconocido");
        }
    })
    .catch(err => {
        console.error("Error en Fetch:", err);
        Swal.fire('Error', err.message, 'error');
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = 'Guardar y Finalizar';
    });
};
</script>
</body>
</html>