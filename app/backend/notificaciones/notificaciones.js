let ultimoConteoTraspasos = 0;

function verificarNotificaciones() {
    fetch('/cfsistem/app/backend/movimientos/get_notificaciones_traspaso.php?t=' + Date.now())
        .then(response => response.json())
        .then(data => {
            const badge = document.getElementById('notif-badge');
            const lista = document.getElementById('lista-notificaciones');
            const cantidadActual = parseInt(data.cantidad) || 0;
            
            // 1. Actualizar Badge de la campana
            if (badge) {
                badge.innerText = cantidadActual;
                cantidadActual > 0 ? badge.classList.remove('d-none') : badge.classList.add('d-none');
            }

            // 2. Disparar Toastify si hay nuevos traspasos
            if (cantidadActual > ultimoConteoTraspasos) {
                Toastify({
                    text: `📦 NUEVO TRASPASO: Tienes ${cantidadActual} pendientes de recibir.`,
                    duration: 7000,
                    destination: "/cfsistem/app/views/almacenes.php",
                    newWindow: false,
                    close: true,
                    gravity: "top", // top o bottom
                    position: "right", // left, center o right
                    stopOnFocus: true, // Evita que se cierre al pasar el mouse
                    style: {
                        background: "linear-gradient(to right, #00b09b, #96c93d)",
                        borderRadius: "10px",
                        fontWeight: "bold"
                    },
                    onClick: function(){} // Callback después de hacer click
                }).showToast();
            }

            ultimoConteoTraspasos = cantidadActual;

            // 3. Renderizar lista en el dropdown
            if (lista && data.items) {
                if (cantidadActual === 0) {
                    lista.innerHTML = '<li class="p-3 text-center text-muted small">Sin pendientes</li>';
                } else {
                    lista.innerHTML = data.items.map(item => `
                        <li class="p-2 border-bottom d-flex justify-content-between align-items-center mx-2">
                            <div style="font-size: 0.8rem; max-width: 75%">
                                <b>${item.producto}</b><br>
                                <span class="text-muted">Cant: ${item.cantidad}</span>
                            </div>
                            <button onclick="procesarRecepcion(${item.id})" class="btn btn-sm btn-success p-1">
                                <i class="bi bi-check-lg"></i>
                            </button>
                        </li>
                    `).join('');
                }
            }
        });
}

// Función para procesar el click del botón verde
function procesarRecepcion(id) {
    // Usamos el confirm nativo o podrías usar un modal de Bootstrap
    if (!confirm("¿Deseas confirmar la recepción de este producto?")) return;

    const formData = new FormData();
    formData.append('id', id);

    fetch('/cfsistem/app/backend/movimientos/procesar_transaccion_rapida.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success || data.status === 'success') {
            Toastify({
                text: "✅ Producto recibido correctamente",
                backgroundColor: "#28a745"
            }).showToast();
            setTimeout(() => location.reload(), 1000);
        } else {
            alert("Error: " + data.message);
        }
    });
}

// Lógica del Dropdown (Abrir/Cerrar)
document.addEventListener('click', function(e) {
    const btn = document.getElementById('btnNotif');
    const menu = document.getElementById('menuNotif');
    if (!btn || !menu) return;

    if (btn.contains(e.target)) {
        menu.style.display = (menu.style.display === 'none' || menu.style.display === '') ? 'block' : 'none';
        e.preventDefault();
    } else if (!menu.contains(e.target)) {
        menu.style.display = 'none';
    }
});

document.addEventListener('DOMContentLoaded', () => {
    verificarNotificaciones();
    setInterval(verificarNotificaciones, 30000);
});