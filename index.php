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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        /* ============================================================
           RESET
           ============================================================ */
        *,
        *::before,
        *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        :root {
            --gray-50: #fbfbfd;
            --gray-100: #f5f5f7;
            --gray-200: #e8e8ed;
            --gray-300: #d2d2d7;
            --gray-400: #a1a1a6;
            --gray-500: #86868b;
            --gray-600: #6e6e73;
            --gray-700: #48484a;
            --gray-800: #2c2c2e;
            --gray-900: #1d1d1f;

            --blue: #0071e3;
            --blue-hover: #0077ed;

            --radius-input: 14px;
            --radius-card: 28px;
            --radius-btn: 14px;
        }

        html,
        body {
            height: 100%;
            width: 100%;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'SF Pro Display', 'Segoe UI', sans-serif;
            color: var(--gray-900);
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            overflow: hidden;
            position: relative;
            letter-spacing: -0.011em;
            background: #000;
        }

        /* ============================================================
           FONDO — CARRUSEL DE IMÁGENES CON KEN BURNS
           ============================================================ */
        .bg-carousel {
            position: fixed;
            inset: 0;
            z-index: 0;
            overflow: hidden;
        }

        .bg-carousel .slide {
            position: absolute;
            inset: 0;
            opacity: 0;
            transition: opacity 1.8s ease-in-out;
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            animation: kenBurns 20s ease-in-out infinite alternate;
        }

        .bg-carousel .slide.active {
            opacity: 1;
        }

        /* Efecto "Ken Burns" (zoom + desplazamiento) */
        @keyframes kenBurns {
            0% {
                transform: scale(1) translate(0, 0);
            }

            100% {
                transform: scale(1.12) translate(-2%, -1.5%);
            }
        }

        /* Capa de oscurecimiento con gradiente */
        .bg-overlay {
            position: fixed;
            inset: 0;
            z-index: 1;
            background:
                linear-gradient(135deg,
                    rgba(15, 20, 35, 0.55) 0%,
                    rgba(15, 20, 35, 0.35) 40%,
                    rgba(15, 20, 35, 0.65) 100%),
                radial-gradient(ellipse at center,
                    transparent 0%,
                    rgba(0, 0, 0, 0.35) 100%);
            pointer-events: none;
        }

        /* ============================================================
           BURBUJAS DE LUZ FLOTANTES
           ============================================================ */
        .bg-blobs {
            position: fixed;
            inset: 0;
            z-index: 2;
            overflow: hidden;
            pointer-events: none;
        }

        .bg-blobs span {
            position: absolute;
            border-radius: 50%;
            filter: blur(90px);
            opacity: 0.35;
        }

        .bg-blobs .b1 {
            width: 420px;
            height: 420px;
            background: radial-gradient(circle, rgba(0, 113, 227, 0.6), transparent 70%);
            top: -100px;
            left: -100px;
            animation: floatA 16s ease-in-out infinite;
        }

        .bg-blobs .b2 {
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(120, 180, 255, 0.4), transparent 70%);
            bottom: -150px;
            right: -120px;
            animation: floatB 22s ease-in-out infinite;
        }

        .bg-blobs .b3 {
            width: 320px;
            height: 320px;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.25), transparent 70%);
            top: 55%;
            left: 60%;
            animation: floatC 26s ease-in-out infinite;
        }

        @keyframes floatA {

            0%,
            100% {
                transform: translate(0, 0) scale(1);
            }

            50% {
                transform: translate(60px, 50px) scale(1.08);
            }
        }

        @keyframes floatB {

            0%,
            100% {
                transform: translate(0, 0) scale(1);
            }

            50% {
                transform: translate(-80px, -50px) scale(1.06);
            }
        }

        @keyframes floatC {

            0%,
            100% {
                transform: translate(0, 0) scale(1);
            }

            50% {
                transform: translate(-50px, 60px) scale(1.12);
            }
        }

        /* ============================================================
           TARJETA DE LOGIN (glassmorphism oscuro)
           ============================================================ */
        .login-card {
            position: relative;
            z-index: 3;
            width: 100%;
            max-width: 420px;
            padding: 44px 40px 34px;
            border-radius: var(--radius-card);
            background: rgba(28, 28, 32, 0.55);
            backdrop-filter: blur(28px) saturate(180%);
            -webkit-backdrop-filter: blur(28px) saturate(180%);
            border: 1px solid rgba(255, 255, 255, 0.12);
            box-shadow:
                0 24px 70px rgba(0, 0, 0, 0.45),
                0 4px 12px rgba(0, 0, 0, 0.2),
                inset 0 1px 0 rgba(255, 255, 255, 0.15);
            animation: cardIn 0.9s cubic-bezier(0.22, 1, 0.36, 1);
            color: #f5f5f7;
        }

        /* Reflejo superior tipo cristal */
        .login-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 20%;
            right: 20%;
            height: 1px;
            background: linear-gradient(90deg,
                    transparent,
                    rgba(255, 255, 255, 0.8),
                    transparent);
            pointer-events: none;
        }

        @keyframes cardIn {
            from {
                opacity: 0;
                transform: translateY(28px) scale(0.97);
            }

            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        /* ============================================================
           LOGO
           ============================================================ */
        .logo-wrap {
            display: flex;
            justify-content: center;
            margin-bottom: 22px;
            animation: fadeIn 0.6s ease-out 0.15s backwards;
        }

        .logo-badge {
            width: 68px;
            height: 68px;
            border-radius: 20px;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid rgba(255, 255, 255, 0.18);
            box-shadow:
                0 6px 20px rgba(0, 0, 0, 0.25),
                inset 0 1px 0 rgba(255, 255, 255, 0.2);
        }

        .logo-badge img {
            width: 42px;
            height: 42px;
            object-fit: contain;
        }

        /* ============================================================
           TÍTULOS
           ============================================================ */
        .login-title {
            font-size: 1.65rem;
            font-weight: 600;
            color: #ffffff;
            text-align: center;
            letter-spacing: -0.025em;
            margin-bottom: 6px;
            animation: fadeIn 0.6s ease-out 0.2s backwards;
        }

        .login-subtitle {
            font-size: 0.9rem;
            font-weight: 400;
            color: rgba(255, 255, 255, 0.65);
            text-align: center;
            letter-spacing: -0.01em;
            margin-bottom: 32px;
            animation: fadeIn 0.6s ease-out 0.25s backwards;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(8px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* ============================================================
           CAMPOS FLOTANTES (dark glass)
           ============================================================ */
        .field {
            position: relative;
            margin-bottom: 16px;
            animation: fadeIn 0.6s ease-out 0.3s backwards;
        }

        .field:nth-of-type(2) {
            animation-delay: 0.35s;
        }

        .field input {
            width: 100%;
            height: 56px;
            padding: 22px 46px 8px 46px;
            font-family: inherit;
            font-size: 0.95rem;
            font-weight: 500;
            color: #ffffff;
            background: rgba(255, 255, 255, 0.07);
            border: 1px solid rgba(255, 255, 255, 0.14);
            border-radius: var(--radius-input);
            outline: none;
            transition: all 0.22s cubic-bezier(0.22, 1, 0.36, 1);
            -webkit-appearance: none;
        }

        .field input:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: rgba(255, 255, 255, 0.22);
        }

        .field input:focus {
            background: rgba(255, 255, 255, 0.13);
            border-color: rgba(0, 113, 227, 0.85);
            box-shadow:
                0 0 0 4px rgba(0, 113, 227, 0.22),
                0 0 24px rgba(0, 113, 227, 0.25);
        }

        /* Label flotante */
        .field label {
            position: absolute;
            top: 50%;
            left: 46px;
            transform: translateY(-50%);
            font-size: 0.95rem;
            font-weight: 500;
            color: rgba(255, 255, 255, 0.55);
            pointer-events: none;
            transition: all 0.18s cubic-bezier(0.22, 1, 0.36, 1);
            letter-spacing: -0.01em;
        }

        .field input:focus+label,
        .field input:not(:placeholder-shown)+label {
            top: 15px;
            font-size: 0.68rem;
            font-weight: 600;
            color: #4aa3ff;
            letter-spacing: 0.4px;
            text-transform: uppercase;
        }

        /* Ícono */
        .field .icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(255, 255, 255, 0.45);
            font-size: 1.05rem;
            transition: color 0.2s ease;
            pointer-events: none;
        }

        .field input:focus~.icon {
            color: #4aa3ff;
        }

        /* Botón ojo */
        .field .btn-eye {
            position: absolute;
            right: 8px;
            top: 50%;
            transform: translateY(-50%);
            width: 38px;
            height: 38px;
            border-radius: 10px;
            background: transparent;
            border: none;
            color: rgba(255, 255, 255, 0.55);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.15s ease;
            font-size: 1.05rem;
        }

        .field .btn-eye:hover {
            background: rgba(255, 255, 255, 0.1);
            color: #4aa3ff;
        }

        .field .btn-eye:active {
            transform: translateY(-50%) scale(0.92);
        }

        /* ============================================================
           BOTÓN LOGIN (iOS style)
           ============================================================ */
        .btn-login {
            width: 100%;
            height: 52px;
            margin-top: 12px;
            border: none;
            border-radius: var(--radius-btn);
            background: linear-gradient(180deg, #0082f0 0%, #0071e3 100%);
            color: #fff;
            font-family: inherit;
            font-size: 0.98rem;
            font-weight: 600;
            letter-spacing: -0.01em;
            cursor: pointer;
            position: relative;
            overflow: hidden;
            box-shadow:
                0 4px 18px rgba(0, 113, 227, 0.5),
                inset 0 1px 0 rgba(255, 255, 255, 0.28);
            transition: all 0.22s cubic-bezier(0.22, 1, 0.36, 1);
            animation: fadeIn 0.6s ease-out 0.4s backwards;
        }

        .btn-login::after {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(120deg,
                    transparent 30%,
                    rgba(255, 255, 255, 0.35) 50%,
                    transparent 70%);
            transition: left 0.7s ease;
        }

        .btn-login:hover {
            transform: translateY(-1px);
            box-shadow:
                0 8px 28px rgba(0, 113, 227, 0.6),
                inset 0 1px 0 rgba(255, 255, 255, 0.3);
        }

        .btn-login:hover::after {
            left: 100%;
        }

        .btn-login:active {
            transform: translateY(0) scale(0.985);
        }

        .btn-login:disabled {
            background: rgba(120, 120, 130, 0.7);
            box-shadow: none;
            cursor: not-allowed;
            transform: none;
        }

        /* ============================================================
           DIVISOR
           ============================================================ */
        .divider {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 24px 0 16px;
            font-size: 0.68rem;
            font-weight: 600;
            color: rgba(255, 255, 255, 0.45);
            text-transform: uppercase;
            letter-spacing: 0.7px;
            animation: fadeIn 0.6s ease-out 0.45s backwards;
        }

        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: linear-gradient(90deg,
                    transparent,
                    rgba(255, 255, 255, 0.18),
                    transparent);
        }

        /* ============================================================
           FOOTER
           ============================================================ */
        .login-footer {
            text-align: center;
            font-size: 0.72rem;
            color: rgba(255, 255, 255, 0.5);
            line-height: 1.65;
            letter-spacing: -0.005em;
            animation: fadeIn 0.6s ease-out 0.5s backwards;
        }

        .login-footer .brand {
            color: rgba(255, 255, 255, 0.85);
            font-weight: 600;
        }

        /* ============================================================
           RESPONSIVE
           ============================================================ */
        @media (max-width: 480px) {
            body {
                padding: 16px;
            }

            .login-card {
                padding: 36px 26px 28px;
                border-radius: 24px;
            }

            .login-title {
                font-size: 1.45rem;
            }

            .login-subtitle {
                margin-bottom: 24px;
            }

            .field input {
                height: 54px;
            }

            .bg-blobs span {
                filter: blur(70px);
                opacity: 0.3;
            }
        }

        @media (prefers-reduced-motion: reduce) {

            *,
            *::before,
            *::after {
                animation-duration: 0.01ms !important;
                transition-duration: 0.01ms !important;
            }
        }
    </style>
    <style>
        /* ============================================================
       SWEETALERT2 — ESTILO CRISTAL (glassmorphism)
       ============================================================ */

        /* Fondo oscuro con blur detrás del modal */
        .swal2-container.swal2-backdrop-show {
            background: rgba(20, 20, 30, 0.45) !important;
            backdrop-filter: blur(12px) saturate(180%);
            -webkit-backdrop-filter: blur(12px) saturate(180%);
        }

        /* La ventana del modal */
        .swal2-popup {
            background: rgba(255, 255, 255, 0.72) !important;
            backdrop-filter: blur(30px) saturate(180%) !important;
            -webkit-backdrop-filter: blur(30px) saturate(180%) !important;
            border: 1px solid rgba(255, 255, 255, 0.85) !important;
            border-radius: 24px !important;
            box-shadow:
                0 20px 60px rgba(0, 0, 0, 0.15),
                0 4px 12px rgba(0, 0, 0, 0.06),
                inset 0 1px 0 rgba(255, 255, 255, 1) !important;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif !important;
            padding: 32px 28px 24px !important;
            position: relative;
            overflow: hidden;
        }

        /* Reflejo superior tipo cristal */
        .swal2-popup::before {
            content: '';
            position: absolute;
            top: 0;
            left: 20%;
            right: 20%;
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 1), transparent);
            pointer-events: none;
        }

        /* Título */
        .swal2-title {
            color: #1d1d1f !important;
            font-size: 1.35rem !important;
            font-weight: 600 !important;
            letter-spacing: -0.02em !important;
            margin-bottom: 6px !important;
        }

        /* Texto */
        .swal2-html-container {
            color: #6e6e73 !important;
            font-size: 0.92rem !important;
            font-weight: 400 !important;
            letter-spacing: -0.01em !important;
            line-height: 1.5 !important;
        }

        /* Ícono */
        .swal2-icon {
            border-width: 3px !important;
            margin: 0 auto 18px !important;
        }

        .swal2-icon.swal2-success {
            border-color: rgba(52, 199, 89, 0.35) !important;
            color: #34c759 !important;
        }

        .swal2-icon.swal2-error {
            border-color: rgba(255, 59, 48, 0.35) !important;
            color: #ff3b30 !important;
        }

        .swal2-icon.swal2-warning {
            border-color: rgba(255, 204, 0, 0.35) !important;
            color: #ffcc00 !important;
        }

        .swal2-icon.swal2-info {
            border-color: rgba(0, 122, 255, 0.35) !important;
            color: #007aff !important;
        }

        /* Checkmark del ícono de éxito con fondo cristalino */
        .swal2-icon.swal2-success .swal2-success-ring {
            border: 3px solid rgba(52, 199, 89, 0.4) !important;
        }

        /* Botón */
        .swal2-confirm {
            background: linear-gradient(180deg, #0082f0 0%, #0071e3 100%) !important;
            border-radius: 14px !important;
            padding: 12px 26px !important;
            font-size: 0.92rem !important;
            font-weight: 600 !important;
            letter-spacing: -0.01em !important;
            box-shadow:
                0 4px 14px rgba(0, 113, 227, 0.4),
                inset 0 1px 0 rgba(255, 255, 255, 0.28) !important;
            transition: all 0.22s cubic-bezier(0.22, 1, 0.36, 1) !important;
        }

        .swal2-confirm:hover {
            transform: translateY(-1px) !important;
            box-shadow:
                0 8px 24px rgba(0, 113, 227, 0.5),
                inset 0 1px 0 rgba(255, 255, 255, 0.3) !important;
        }

        .swal2-confirm:focus {
            box-shadow:
                0 4px 14px rgba(0, 113, 227, 0.4),
                0 0 0 4px rgba(0, 113, 227, 0.25) !important;
        }

        /* Barra de progreso (timer) */
        .swal2-timer-progress-bar {
            background: linear-gradient(90deg, #0071e3 0%, #4aa3ff 100%) !important;
            height: 3px !important;
        }

        /* Animación de entrada más suave tipo Apple */
        .swal2-show {
            animation: swalCristalIn 0.45s cubic-bezier(0.22, 1, 0.36, 1) !important;
        }

        @keyframes swalCristalIn {
            from {
                opacity: 0;
                transform: translateY(20px) scale(0.96);
            }

            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        /* Modo oscuro opcional */
        @media (prefers-color-scheme: dark) {
            .swal2-popup {
                background: rgba(40, 40, 45, 0.72) !important;
                border-color: rgba(255, 255, 255, 0.12) !important;
            }

            .swal2-title {
                color: #f5f5f7 !important;
            }

            .swal2-html-container {
                color: rgba(255, 255, 255, 0.7) !important;
            }
        }
    </style>
</head>

<body>

    <!-- ============================================================
         FONDO: CARRUSEL DE IMÁGENES CON KEN BURNS
         ============================================================ -->
    <div class="bg-carousel" id="bgCarousel">
        <div class="slide active" style="background-image: url('public/assets/almacen3.jpg');"></div>
        <div class="slide" style="background-image: url('public/assets/almacen2.jpg');"></div>
    </div>

    <!-- Capa de oscurecimiento -->
    <div class="bg-overlay"></div>

    <!-- Burbujas de luz flotantes -->
    <div class="bg-blobs">
        <span class="b1"></span>
        <span class="b2"></span>
        <span class="b3"></span>
    </div>

    <!-- ============================================================
         TARJETA DE LOGIN
         ============================================================ -->
    <div class="login-card">

        <div class="logo-wrap">
            <div class="logo-badge">
                <img src="/cfsistem/public/assets/logo.png" alt="Logo">
            </div>
        </div>

        <h1 class="login-title">Iniciar sesión</h1>
        <p class="login-subtitle">Accede a tu cuenta de G-M Sistem</p>

        <form id="formLogin" autocomplete="on">

            <div class="field">
                <input type="text" id="usuario" name="usuario" placeholder=" " autocomplete="username" required>
                <label for="usuario">Usuario</label>
                <i class="bi bi-person icon"></i>
            </div>

            <div class="field">
                <input type="password" id="passwordField" name="password" placeholder=" "
                    autocomplete="current-password" required>
                <label for="passwordField">Contraseña</label>
                <i class="bi bi-lock icon"></i>
                <button type="button" class="btn-eye" id="togglePassword" aria-label="Mostrar contraseña">
                    <i class="bi bi-eye" id="eyeIcon"></i>
                </button>
            </div>

            <button type="submit" id="btnIngresar" class="btn-login">
                <span>Continuar</span>
            </button>

        </form>

        <div class="divider">Seguro y cifrado</div>

        <div class="login-footer">
            © <?php echo date('Y'); ?> <span class="brand">G-M SISTEM</span><br>
            <small>Todos los derechos reservados</small>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // ============================================================
        // CARRUSEL DE FONDO AUTOMÁTICO
        // ============================================================
        (function initBgCarousel() {
            const slides = document.querySelectorAll('.bg-carousel .slide');
            if (slides.length < 2) return;

            let current = 0;

            setInterval(() => {
                slides[current].classList.remove('active');
                current = (current + 1) % slides.length;
                slides[current].classList.add('active');
            }, 7000); // cambia cada 7s
        })();

        // ============================================================
        // VER / OCULTAR CONTRASEÑA
        // ============================================================
        const togglePassword = document.querySelector('#togglePassword');
        const passwordField = document.querySelector('#passwordField');
        const eyeIcon = document.querySelector('#eyeIcon');

        togglePassword.addEventListener('click', function () {
            const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordField.setAttribute('type', type);
            eyeIcon.classList.toggle('bi-eye');
            eyeIcon.classList.toggle('bi-eye-slash');
        });

        // ============================================================
        // LOGIN
        // ============================================================
        document.getElementById('formLogin').addEventListener('submit', async (e) => {
            e.preventDefault();

            const btn = document.getElementById('btnIngresar');
            const originalText = btn.innerHTML;

            btn.disabled = true;
            btn.innerHTML = `
            <span class="spinner-border spinner-border-sm me-2"
                  role="status" aria-hidden="true"
                  style="width:14px;height:14px;"></span>
            Validando...
        `;

            try {
                const response = await fetch('/cfsistem/app/controllers/authController.php?action=login', {
                    method: 'POST',
                    body: new FormData(document.getElementById('formLogin'))
                });

                const res = await response.json();

                if (res.status === 'success') {
                    localStorage.setItem('config_hora_cierre', res.hora_cierre || '18:00');

                    Swal.fire({
                        icon: 'success',
                        title: '¡Bienvenido!',
                        text: res.message,
                        showConfirmButton: false,
                        timer: 1400,
                        timerProgressBar: true
                    }).then(() => {
                        window.location.href = res.redirect;
                    });
                } else {
                    Swal.fire({
                        icon: res.status,
                        title: 'Atención',
                        text: res.message,
                        confirmButtonColor: '#0071e3',
                        confirmButtonText: 'Entendido'
                    });
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                }
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error de conexión',
                    text: 'No se pudo conectar con el servidor. Inténtalo más tarde.',
                    confirmButtonColor: '#0071e3',
                    confirmButtonText: 'Entendido'
                });
                btn.disabled = false;
                btn.innerHTML = originalText;
            }
        });
    </script>
</body>

</html>