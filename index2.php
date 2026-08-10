<?php session_start(); ?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>G-M SISTEM | Acceso</title>
      <link rel="icon" type="image/png" href="/cfsistem/public/assets/logo.png">

    <link rel="shortcut icon" href="/cfsistem/public/assets/logo.ico" type="image/x-icon">

    <link rel="icon" type="image/png" href="/cfsistem/public/assets/logo.png">
    <link rel="shortcut icon" href="/cfsistem/public/assets/logo.ico" type="image/x-icon">

    <!-- Bootstrap & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        * {
            box-sizing: border-box;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        body, html {
            height: 100%;
            margin: 0;
            overflow-x: hidden;
            background-color: #0d1117;
        }

        .split-container {
            display: flex;
            min-height: 100vh;
            width: 100vw;
            position: relative;
        }

        /* --- LADO IZQUIERDO: CARROUSEL --- */
        .left-side {
            flex: 1.2;
            position: relative;
            display: none;
            overflow: hidden;
        }

        @media (min-width: 992px) {
            .left-side {
                display: block;
            }
        }

        .carousel, .carousel-inner, .carousel-item {
            height: 100%;
        }

        .carousel-item img {
            height: 100vh;
            object-fit: cover;
            filter: brightness(0.65) contrast(1.1);
        }

        .carousel-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(13, 17, 23, 0.8) 0%, rgba(13, 17, 23, 0.3) 100%);
            display: flex;
            align-items: flex-end;
            padding: 4rem;
            z-index: 2;
        }

        .glass-badge {
            background: rgba(255, 255, 255, 0.12);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 6px 16px;
            border-radius: 30px;
            font-size: 0.85rem;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        /* --- LADO DERECHO: VIDRIO ESMERILADO (GLASSMORPHISM) --- */
        .right-side {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            padding: 2rem;
            background: radial-gradient(circle at 50% 50%, #1a2332 0%, #0d1117 100%);
            z-index: 1;
        }

        /* Decoración de luz de fondo para el cristal */
        .right-side::before {
            content: '';
            position: absolute;
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(13, 110, 253, 0.35) 0%, rgba(0,0,0,0) 70%);
            top: 15%;
            right: 15%;
            z-index: -1;
            border-radius: 50%;
            filter: blur(50px);
        }

        .right-side::after {
            content: '';
            position: absolute;
            width: 250px;
            height: 250px;
            background: radial-gradient(circle, rgba(168, 9, 9, 0.25) 0%, rgba(0,0,0,0) 70%);
            bottom: 15%;
            left: 15%;
            z-index: -1;
            border-radius: 50%;
            filter: blur(50px);
        }

        /* CARD EFFECTO CRISTAL */
        .login-card {
            width: 100%;
            max-width: 420px;
            padding: 3rem 2.5rem;
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 24px;
            box-shadow: 0 30px 60px rgba(0, 0, 0, 0.4),
                        inset 0 1px 0 rgba(255, 255, 255, 0.2);
            color: #ffffff;
            transition: all 0.3s ease;
        }

        .logo-title {
            font-size: 2rem;
            font-weight: 800;
            letter-spacing: -0.5px;
            background: linear-gradient(135deg, #ffffff 0%, #a5b4fc 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-align: center;
        }

        .logo-subtitle {
            font-size: 0.875rem;
            color: rgba(255, 255, 255, 0.6);
            text-align: center;
            margin-bottom: 2rem;
        }

        /* FORM INPUTS CRISTALIZADOS */
        .form-label {
            color: rgba(255, 255, 255, 0.85);
            font-size: 0.825rem;
            letter-spacing: 0.5px;
            margin-bottom: 6px;
        }

        .input-group {
            background: rgba(0, 0, 0, 0.25);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 12px;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .input-group:focus-within {
            border-color: #3b82f6;
            box-shadow: 0 0 12px rgba(59, 130, 246, 0.3);
            background: rgba(0, 0, 0, 0.4);
        }

        .input-group-text {
            background: transparent;
            border: none;
            color: rgba(255, 255, 255, 0.5);
            padding-left: 1.2rem;
        }

        .form-control {
            background: transparent !important;
            border: none !important;
            color: #ffffff !important;
            padding: 0.75rem 1rem;
            font-size: 0.95rem;
        }

        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.3);
        }

        .form-control:focus {
            box-shadow: none;
        }

        .btn-show-pass {
            background: transparent;
            border: none;
            color: rgba(255, 255, 255, 0.5);
            padding-right: 1.2rem;
            transition: color 0.2s;
        }

        .btn-show-pass:hover {
            color: #ffffff;
        }

        /* BOTÓN PRINCIPAL */
        .btn-login {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            padding: 0.85rem;
            font-weight: 600;
            font-size: 0.95rem;
            letter-spacing: 0.5px;
            margin-top: 1rem;
            transition: all 0.3s ease;
            box-shadow: 0 8px 20px rgba(37, 99, 235, 0.35);
        }

        .btn-login:hover {
            background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%);
            transform: translateY(-2px);
            box-shadow: 0 12px 24px rgba(37, 99, 235, 0.45);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .login-footer {
            margin-top: 2.5rem;
            text-align: center;
            font-size: 0.775rem;
            color: rgba(255, 255, 255, 0.4);
        }
    </style>
</head>

<body>

    <div class="split-container">
        <!-- Lado Izquierdo (Carrusel) -->
        <div class="left-side">
            <div id="labCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel" data-bs-interval="4000">
                <div class="carousel-inner">
                    <div class="carousel-item active">
                        <img src="public/assets/almacen3.jpg" class="d-block w-100" alt="Almacén 1">
                    </div>
                    <div class="carousel-item">
                        <img src="public/assets/almacen2.jpg" class="d-block w-100" alt="Almacén 2">
                    </div>
                </div>
            </div>
            <div class="carousel-overlay">
                <div class="text-white px-2">
                    <span class="glass-badge mb-3 d-inline-block">G-M SISTEM v2.0</span>
                    <h1 class="fw-bold display-5 mt-2">Eficiencia en cada movimiento.</h1>
                    <p class="lead opacity-75">Optimiza e integra tu inventario logístico en tiempo real.</p>
                </div>
            </div>
        </div>

        <!-- Lado Derecho (Login Glassmorphism) -->
        <div class="right-side">
            <div class="login-card">
                <div class="logo-title">G-M SISTEM</div>
                <div class="logo-subtitle">Gestión Inteligente de Inventarios</div>

                <form id="formLogin">
                    <div class="mb-3 text-start">
                        <label class="form-label">USUARIO</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-person"></i></span>
                            <input type="text" name="usuario" class="form-control" placeholder="Ingresa tu usuario" required autocomplete="off">
                        </div>
                    </div>

                    <div class="mb-4 text-start">
                        <label class="form-label">CONTRASEÑA</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock"></i></span>
                            <input type="password" name="password" id="passwordField" class="form-control" placeholder="••••••••" required>
                            <button type="button" class="btn btn-show-pass" id="togglePassword">
                                <i class="bi bi-eye" id="eyeIcon"></i>
                            </button>
                        </div>
                    </div>

                    <button type="submit" id="btnIngresar" class="btn btn-login w-100 text-white">
                        <span>Ingresar al Sistema</span>
                    </button>
                </form>

                <div class="login-footer">
                    © <?php echo date('Y'); ?> <span class="fw-semibold text-white-50">G-M SISTEM</span><br>
                    <span>Todos los derechos reservados</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
    // Toggle para mostrar/ocultar contraseña
    const togglePassword = document.querySelector('#togglePassword');
    const passwordField = document.querySelector('#passwordField');
    const eyeIcon = document.querySelector('#eyeIcon');

    togglePassword.addEventListener('click', function() {
        const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordField.setAttribute('type', type);

        eyeIcon.classList.toggle('bi-eye');
        eyeIcon.classList.toggle('bi-eye-slash');
    });

    // Petición AJAX de Login
    document.getElementById('formLogin').addEventListener('submit', async (e) => {
        e.preventDefault();

        const btn = document.getElementById('btnIngresar');
        const originalText = btn.innerHTML;

        btn.disabled = true;
        btn.innerHTML =
            `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Validando...`;

        const formData = new FormData(e.target);

        fetch('/cfsistem/app/controllers/authController.php?action=login', {
    method: 'POST',
    body: new FormData(document.getElementById('formLogin'))
})
.then(async response => {

    console.log('STATUS HTTP:', response.status);
    console.log('STATUS TEXT:', response.statusText);
    console.log('HEADERS:', [...response.headers.entries()]);

    const texto = await response.text();

    console.log('RESPUESTA CRUDA DEL SERVIDOR:');
    console.log(texto);

    if (!response.ok) {
        throw new Error(
            `HTTP ${response.status} ${response.statusText}\n\n${texto}`
        );
    }

    try {
        return JSON.parse(texto);
    } catch (error) {
        throw new Error(
            `El servidor NO devolvió JSON válido.\n\nRespuesta:\n${texto}`
        );
    }
})
.then(data => {

    console.log('JSON RECIBIDO:', data);

    if (data.status === 'success') {

        console.log('LOGIN CORRECTO');
        console.log('Redireccionando a:', data.redirect);

        window.location.href = data.redirect;

    } else {

        console.warn('RESPUESTA DEL CONTROLADOR:', data);

        Swal.fire({
            icon: data.status || 'error',
            title: 'Aviso',
            text: data.message || 'Error desconocido'
        });
    }

})
.catch(error => {

    console.error('========== ERROR FETCH ==========');
    console.error('Mensaje:', error.message);
    console.error('Error completo:', error);
    console.error('=================================');

    Swal.fire({
        icon: 'error',
        title: 'Error de conexión',
        html: `<pre style="text-align:left;white-space:pre-wrap;">${error.message}</pre>`
    });
});
    });
    </script>
</body>

</html>