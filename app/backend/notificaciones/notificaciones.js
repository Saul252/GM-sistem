
let ultimoConteoTraspasos = 0;

// --- NUEVO: Solicitar permisos de notificación apenas cargue la página ---
if (Notification.permission !== "granted" && Notification.permission !== "denied") {
    Notification.requestPermission();
}

// 1. Lógica para abrir/cerrar el menú manualmente
document.addEventListener('click', function(e) {
    const btn = document.getElementById('btnNotif');
    const menu = document.getElementById('menuNotif');
    
    if (!btn || !menu) return;

    if (btn.contains(e.target)) {
        menu.style.display = (menu.style.display === 'none' || menu.style.display === '') ? 'block' : 'none';
        e.preventDefault();
    } 
    else if (!menu.contains(e.target)) {
        menu.style.display = 'none';
    }
});

// 2. Función para aceptar traspaso
function aceptarTraspasoRapido(idMovimiento) {
    if (!confirm("¿Confirmas la recepción? El stock se actualizará y la página se recargará.")) return;

    const formData = new FormData();
    formData.append('id', idMovimiento);

    fetch('/cfsistem/app/backend/movimientos/procesar_transaccion_rapida.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success || data.status === 'success') {
            location.reload(); 
        } else {
            alert("❌ Error: " + (data.error || data.message));
        }
    })
    .catch(err => {
        console.error("Error:", err);
        alert("Error de conexión.");
    });
}

// 3. Consulta de datos y Disparo de Notificación
function verificarNotificaciones() {
    fetch('/cfsistem/app/backend/movimientos/get_notificaciones_traspaso.php?t=' + Date.now())
        .then(response => response.json())
        .then(data => {
            const badge = document.getElementById('notif-badge');
            const lista = document.getElementById('lista-notificaciones');
            const cantidadActual = parseInt(data.cantidad) || 0;
            
            if (badge) {
                badge.innerText = cantidadActual;
                cantidadActual > 0 ? badge.classList.remove('d-none') : badge.classList.add('d-none');
            }

            // --- NUEVO: Disparar notificación de escritorio si el número subió ---
            if (cantidadActual > ultimoConteoTraspasos) {
                if (Notification.permission === "granted") {
                    new Notification("📦 Nuevo Traspaso", {
                        body: `Tienes ${cantidadActual} producto(s) pendientes de recibir en tu almacén.`,
                        icon: '/cfsistem/assets/img/logo.png' // Verifica que esta ruta exista
                    });
                }
            }
            // Actualizamos el contador para la siguiente revisión
            ultimoConteoTraspasos = cantidadActual;

            if (lista && data.items) {
                if (cantidadActual === 0) {
                    lista.innerHTML = '<li class="p-3 text-center text-muted small">Sin pendientes</li>';
                } else {
                    lista.innerHTML = data.items.map(item => `
                        <li class="p-2 border-bottom d-flex justify-content-between align-items-center mx-2">
                            <div style="font-size: 0.8rem; max-width: 80%">
                                <b>${item.producto}</b><br>
                                <span class="text-muted">Cant: ${item.cantidad}</span>
                            </div>
                            <button onclick="aceptarTraspasoRapido(${item.id})" class="btn btn-sm btn-success p-1">
                                <i class="bi bi-check2"></i>
                            </button>
                        </li>
                    `).join('');
                }
            }
        });
}

// Iniciar
document.addEventListener('DOMContentLoaded', () => {
    verificarNotificaciones();
    setInterval(verificarNotificaciones, 30000);
});
