<?php session_start(); ?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>G-M SISTEM | Acceso</title>
    <link rel="icon" type="image/png" href="/cfsistem/public/assets/logo.png">
    <link rel="shortcut icon" href="/cfsistem/public/assets/logo.ico" type="image/x-icon">

    <!-- Bootstrap & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        :root {
            --bg-dark: #090d16;
            --card-bg: rgba(22, 27, 34, 0.55);
            --card-border: rgba(255, 255, 255, 0.12);
            --accent-primary: #3b82f6;
            --accent-glow: rgba(59, 130, 246, 0.4);
            --accent-cyan: #06b6d4;
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
        }

        * {
            box-sizing: border-box;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        body, html {
            height: 100%;
            margin: 0;
            overflow-x: hidden;
            background-color: var(--bg-dark);
            color: var(--text-main);
        }

        .split-container {
            display: flex;
            min-height: 100vh;
            width: 100vw;
            position: relative;
        }

        /* --- LADO IZQUIERDO: CAROUSEL CON KEN BURNS EFFECT --- */
        .left-side {
            flex: 1.3;
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
            filter: brightness(0.55) contrast(1.15) saturate(1.1);
            transform: scale(1);
            transition: transform 6s cubic-bezier(0.25, 1, 0.5, 1);
        }

        .carousel-item.active img {
            transform: scale(1.08);
        }

        .carousel-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, rgba(9, 13, 22, 0.4) 0%, rgba(9, 13, 22, 0.95) 100%),
                        linear-gradient(90deg, rgba(9, 13, 22, 0.2) 0%, rgba(9, 13, 22, 0.8) 100%);
            display: flex;
            align-items: flex-end;
            padding: 4.5rem;
            z-index: 2;
        }

        .glass-badge {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.18);
            padding: 7px 18px;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            color: #60a5fa;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
            animation: pulseGlow 3s infinite alternate;
        }

        @keyframes pulseGlow {
            0% { box-shadow: 0 0 10px rgba(59, 130, 246, 0.2); }
            100% { box-shadow: 0 0 22px rgba(59, 130, 246, 0.6); }
        }

        /* --- LADO DERECHO: VIDRIO ESMERILADO (GLASSMORPHISM) --- */
        .right-side {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            padding: 2rem;
            background: var(--bg-dark);
            z-index: 1;
            overflow: hidden;
        }

        /* Ambient Orbs (Fondo Neón Animado) */
        .orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(90px);
            z-index: -1;
            pointer-events: none;
            opacity: 0.6;
            animation: floatAmbient 10s infinite ease-in-out alternate;
        }

        .orb-1 {
            width: 320px;
            height: 320px;
            background: radial-gradient(circle, rgba(59, 130, 246, 0.4) 0%, rgba(0,0,0,0) 70%);
            top: 10%;
            right: 10%;
        }

        .orb-2 {
            width: 280px;
            height: 280px;
            background: radial-gradient(circle, rgba(147, 51, 234, 0.35) 0%, rgba(0,0,0,0) 70%);
            bottom: 10%;
            left: 10%;
            animation-delay: -5s;
        }

        @keyframes floatAmbient {
            0% { transform: translate(0, 0) scale(1); }
            100% { transform: translate(30px, -20px) scale(1.1); }
        }

        /* TARJETA DE LOGIN ULTRA-GLASS */
        .login-card {
            width: 100%;
            max-width: 430px;
            padding: 3.2rem 2.5rem;
            background: var(--card-bg);
            backdrop-filter: blur(25px);
            -webkit-backdrop-filter: blur(25px);
            border: 1px solid var(--card-border);
            border-radius: 28px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.7),
                        inset 0 1px 0 rgba(255, 255, 255, 0.15);
            animation: cardEntrance 0.8s cubic-bezier(0.16, 1, 0.3, 1);
            position: relative;
        }

        @keyframes cardEntrance {
            from {
                opacity: 0;
                transform: translateY(30px) scale(0.96);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .logo-title {
            font-size: 2.2rem;
            font-weight: 800;
            letter-spacing: -1px;
            background: linear-gradient(135deg, #ffffff 30%, #93c5fd 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-align: center;
            margin-bottom: 0.2rem;
        }

        .logo-subtitle {
            font-size: 0.85rem;
            color: var(--text-muted);
            text-align: center;
            margin-bottom: 2.2rem;
            font-weight: 500;
        }

        /* FORM INPUTS */
        .form-label {
            color: #cbd5e1;
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 1px;
            margin-bottom: 8px;
            text-transform: uppercase;
        }

        .input-group {
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 14px;
            overflow: hidden;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .input-group:focus-within {
            border-color: var(--accent-primary);
            box-shadow: 0 0 0 4px var(--accent-glow);
            background: rgba(15, 23, 42, 0.85);
            transform: translateY(-1px);
        }

        .input-group-text {
            background: transparent;
            border: none;
            color: #64748b;
            padding-left: 1.2rem;
            transition: color 0.3s;
        }

        .input-group:focus-within .input-group-text {
            color: #60a5fa;
        }

        .form-control {
            background: transparent !important;
            border: none !important;
            color: #ffffff !important;
            padding: 0.85rem 1rem;
            font-size: 0.95rem;
            font-weight: 500;
        }

        .form-control::placeholder {
            color: #475569;
        }

        .form-control:focus {
            box-shadow: none;
        }

        .btn-show-pass {
            background: transparent;
            border: none;
            color: #64748b;
            padding-right: 1.2rem;
            transition: color 0.2s;
        }

        .btn-show-pass:hover {
            color: #f8fafc;
        }

        /* BOTÓN PRINCIPAL CON SHIMMER */
        .btn-login {
            position: relative;
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 14px;
            padding: 0.95rem;
            font-weight: 700;
            font-size: 0.95rem;
            letter-spacing: 0.5px;
            margin-top: 1.2rem;
            overflow: hidden;
            transition: all 0.3s ease;
            box-shadow: 0 10px 25px -5px rgba(37, 99, 235, 0.5);
        }

        .btn-login::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.25), transparent);
            transition: 0.5s;
        }

        .btn-login:hover::before {
            left: 100%;
        }

        .btn-login:hover {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            transform: translateY(-2px);
            box-shadow: 0 15px 30px -5px rgba(37, 99, 235, 0.6);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .login-footer {
            margin-top: 2.5rem;
            text-align: center;
            font-size: 0.8rem;
            color: #64748b;
        }

        /* Custom SweetAlert Dark Theme */
        .swal2-popup.swal2-toast, .swal2-popup {
            background: #161b22 !important;
            color: #f8fafc !important;
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
            border-radius: 18px !important;
            backdrop-filter: blur(15px);
        }
        .swal2-title {
            color: #f8fafc !important;
        }
    </style>
</head>

<body>

    <div class="split-container">
        <!-- Lado Izquierdo (Carrusel) -->
        <div class="left-side">
            <div id="labCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel" data-bs-interval="5000">
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
                    <h1 class="fw-extrabold display-5 mt-2 mb-3" style="font-weight: 800; letter-spacing: -1px;">Eficiencia en cada movimiento.</h1>
                    <p class="lead text-slate-300" style="color: #cbd5e1; font-size: 1.1rem;">Optimiza e integra tu inventario logístico en tiempo real.</p>
                </div>
            </div>
        </div>

        <!-- Lado Derecho (Login Glassmorphism) -->
        <div class="right-side">
            <!-- Background Orbs -->
            <div class="orb orb-1"></div>
            <div class="orb orb-2"></div>

            <div class="login-card">
                <div class="logo-title">G-M SISTEM</div>
                <div class="logo-subtitle">Gestión Inteligente de Inventarios</div>

                <form id="formLogin">
                    <div class="mb-35 text-start mb-3">
                        <label class="form-label">USUARIO</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-person-gear fs-5"></i></span>
                            <input type="text" name="usuario" class="form-control" placeholder="Ingresa tu usuario" required autocomplete="off">
                        </div>
                    </div>

                    <div class="mb-4 text-start">
                        <label class="form-label">CONTRASEÑA</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-shield-lock fs-5"></i></span>
                            <input type="password" name="password" id="passwordField" class="form-control" placeholder="••••••••" required>
                            <button type="button" class="btn btn-show-pass" id="togglePassword" tabindex="-1">
                                <i class="bi bi-eye" id="eyeIcon"></i>
                            </button>
                        </div>
                    </div>

                    <button type="submit" id="btnIngresar" class="btn btn-login w-100 text-white">
                        <span>Ingresar al Sistema</span>
                    </button>
                </form>

                <div class="login-footer">
                    © <?php echo date('Y'); ?> <span class="fw-semibold text-slate-400" style="color: #94a3b8;">G-M SISTEM</span><br>
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
        const originalContent = btn.innerHTML;

        btn.disabled = true;
        btn.innerHTML = `<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Validando...`;

        try {
            const response = await fetch('/cfsistem/app/controllers/authController.php?action=login', {
                method: 'POST',
                body: new FormData(e.target)
            });

            const texto = await response.text();

            if (!response.ok) {
                throw new Error(`HTTP ${response.status} ${response.statusText}\n\n${texto}`);
            }

            let data;
            try {
                data = JSON.parse(texto);
            } catch (error) {
                throw new Error(`El servidor NO devolvió JSON válido.\n\nRespuesta:\n${texto}`);
            }

            if (data.status === 'success') {
                window.location.href = data.redirect;
            } else {
                Swal.fire({
                    icon: data.status || 'error',
                    title: 'Aviso',
                    text: data.message || 'Error desconocido',
                    confirmButtonColor: '#2563eb'
                });
                btn.disabled = false;
                btn.innerHTML = originalContent;
            }

        } catch (error) {
            console.error('Error Login:', error);

            Swal.fire({
                icon: 'error',
                title: 'Error de conexión',
                html: `<pre style="text-align:left;white-space:pre-wrap;font-size:0.8rem;color:#f87171;">${error.message}</pre>`,
                confirmButtonColor: '#2563eb'
            });

            btn.disabled = false;
            btn.innerHTML = originalContent;
        }
    });
    </script>
</body>

</html>