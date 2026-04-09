<?php session_start(); ?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>G-M SISTEM | Acceso</title>
    <link rel="icon" type="image/png" href="/cfsistem/public/assets/logo.png">

    <link rel="shortcut icon" href="/cfsistem/public/assets/logo.ico" type="image/x-icon">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="/cfsistem/css/index.css" rel="stylesheet">


   
</head>

<body>

    <div class="split-container">
        <div class="left-side">
            <div id="labCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel">
                <div class="carousel-inner">
                    <div class="carousel-item active"><img src="public/assets/almacen3.jpg" class="d-block w-100"></div>
                    <div class="carousel-item"><img src="public/assets/almacen2.jpg" class="d-block w-100"></div>
                </div>
            </div>
            <div class="carousel-overlay">
                <div class="text-center text-white px-4">
                    <h1 class="fw-bold">Eficiencia en cada movimiento.</h1>
                    <p class="lead">G-M Sistem optimiza tu inventario en tiempo real.</p>
                </div>
            </div>
        </div>

        <div class="right-side">
            <div class="login-card">
                <div class="logo-title">G-M SISTEM</div>
                <div class="logo-subtitle">Gestión Inteligente de Inventarios</div>

                <form id="formLogin">
                    <div class="mb-3 text-start">
                        <label class="form-label fw-semibold small">Usuario</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-person"></i></span>
                            <input type="text" name="usuario" class="form-control user-input" placeholder="Ej: admin"
                                required>
                        </div>
                    </div>

                    <div class="mb-4 text-start">
                        <label class="form-label fw-semibold small">Contraseña</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock"></i></span>
                            <input type="password" name="password" id="passwordField" class="form-control"
                                placeholder="••••••••" required style="border-right: none;">
                            <button type="button" class="btn btn-show-pass" id="togglePassword">
                                <i class="bi bi-eye" id="eyeIcon"></i>
                            </button>
                        </div>
                    </div>

                    <button type="submit" id="btnIngresar" class="btn btn-login w-100 text-white shadow-sm">
                        <span>Ingresar al Sistema</span>
                    </button>
                </form>

                <div class="login-footer">
                    © <?php echo date('Y'); ?> <span class="fw-bold">G-M SISTEM</span><br>
                    <small>Todos los derechos reservados</small>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
    // --- LÓGICA PARA VER/OCULTAR CONTRASEÑA ---
    const togglePassword = document.querySelector('#togglePassword');
    const passwordField = document.querySelector('#passwordField');
    const eyeIcon = document.querySelector('#eyeIcon');

    togglePassword.addEventListener('click', function() {
        // Cambiamos el tipo de input
        const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordField.setAttribute('type', type);

        // Cambiamos el icono
        eyeIcon.classList.toggle('bi-eye');
        eyeIcon.classList.toggle('bi-eye-slash');
    });


    // --- LÓGICA DE LOGIN ---
    document.getElementById('formLogin').addEventListener('submit', async (e) => {
        e.preventDefault();

        const btn = document.getElementById('btnIngresar');
        const originalText = btn.innerHTML;

        btn.disabled = true;
        btn.innerHTML =
            `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Validando...`;

        const formData = new FormData(e.target);

        try {
            const response = await fetch('validar_login.php', {
                method: 'POST',
                body: formData
            });

            const res = await response.json();

            if (res.status === 'success') {
                localStorage.setItem('config_hora_cierre', res.hora_cierre || '18:00');

                Swal.fire({
                    icon: 'success',
                    title: '¡Acceso Correcto!',
                    text: res.message,
                    showConfirmButton: false,
                    timer: 1500,
                    timerProgressBar: true
                }).then(() => {
                    window.location.href = res.redirect;;
                });
            } else {
                Swal.fire({
                    icon: res.status,
                    title: 'Atención',
                    text: res.message,
                    confirmButtonColor: '#a80909'
                });
                btn.disabled = false;
                btn.innerHTML = originalText;
            }
        } catch (error) {
            Swal.fire({
                icon: 'error',
                title: 'Error de conexión',
                text: 'No se pudo conectar con el servidor. Inténtalo más tarde.',
                confirmButtonColor: '#a80909'
            });
            btn.disabled = false;
            btn.innerHTML = originalText;
        }

    });
    </script>
</body>

</html>