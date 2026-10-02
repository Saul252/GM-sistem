<?php
session_start();
$_SESSION = [];
session_destroy();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cerrando sesión | G-M SISTEM</title>

    <link rel="icon" type="image/png" href="/cfsistem/public/assets/logo.png">
    <link rel="shortcut icon" href="/cfsistem/public/assets/logo.ico" type="image/x-icon">

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

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
            --blue-light: #4aa3ff;
            --blue-dark: #005bb8;
        }

        html,
        body {
            height: 100%;
            width: 100%;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'SF Pro Display', 'Segoe UI', sans-serif;
            background: #000;
            color: #fff;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
            letter-spacing: -0.011em;
        }

        /* ============================================================
           FONDO: GRADIENTE MUY SUTIL
           ============================================================ */
        .bg {
            position: fixed;
            inset: 0;
            z-index: 0;
            background:
                radial-gradient(ellipse at 20% 30%, rgba(0, 113, 227, 0.15) 0%, transparent 50%),
                radial-gradient(ellipse at 80% 70%, rgba(120, 160, 220, 0.1) 0%, transparent 50%),
                radial-gradient(ellipse at 50% 50%, rgba(30, 30, 40, 1) 0%, #05050a 100%);
        }

        /* ============================================================
           ESCENA CENTRAL
           ============================================================ */
        .scene {
            position: relative;
            z-index: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 42px;
            animation: sceneIn 1s cubic-bezier(0.22, 1, 0.36, 1);
        }

        @keyframes sceneIn {
            from {
                opacity: 0;
                transform: scale(0.95);
            }

            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        /* ============================================================
           LOGO 2x2 — VENTANAS QUE SE CIERRAN
           ============================================================ */
        .grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            width: 180px;
            height: 180px;
            perspective: 800px;
            transform-style: preserve-3d;
            animation: gridFloat 4s ease-in-out infinite;
        }

        @keyframes gridFloat {

            0%,
            100% {
                transform: translateY(0) rotateX(0deg);
            }

            50% {
                transform: translateY(-6px) rotateX(2deg);
            }
        }

        .quad {
            position: relative;
            border-radius: 14px;
            background: linear-gradient(145deg, #0071e3 0%, #005bb8 100%);
            box-shadow:
                0 8px 24px rgba(0, 113, 227, 0.35),
                inset 0 1px 0 rgba(255, 255, 255, 0.25),
                inset 0 -2px 6px rgba(0, 0, 0, 0.15);
            transform-origin: center center;
            transform-style: preserve-3d;
            overflow: hidden;
            animation: fadeQuad 1s ease-in-out forwards;
            will-change: opacity, transform, filter;
        }

        /* Cuadrante 1 (arriba izq) — se desvanece primero */
        .quad:nth-child(1) {
            animation-delay: 1.2s;
            --origin: 0% 0%;
        }

        /* Cuadrante 2 (arriba der) */
        .quad:nth-child(2) {
            animation-delay: 1.6s;
            --origin: 100% 0%;
        }

        /* Cuadrante 3 (abajo izq) */
        .quad:nth-child(3) {
            animation-delay: 2.0s;
            --origin: 0% 100%;
        }

        /* Cuadrante 4 (abajo der) */
        .quad:nth-child(4) {
            animation-delay: 2.4s;
            --origin: 100% 100%;
        }

        /* Brillo superior tipo cristal */
        .quad::before {
            content: '';
            position: absolute;
            top: 0;
            left: 10%;
            right: 10%;
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.9), transparent);
        }

        /* Reflejo interno diagonal */
        .quad::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg,
                    rgba(255, 255, 255, 0.2) 0%,
                    transparent 40%,
                    transparent 60%,
                    rgba(0, 0, 0, 0.15) 100%);
        }

        /* Animación: cada cuadro se encoge y desvanece como si se cerrara */
        @keyframes fadeQuad {
            0% {
                opacity: 1;
                transform: scale(1) rotate(0deg);
                filter: blur(0);
            }

            40% {
                opacity: 1;
                transform: scale(0.98) rotate(0.5deg);
                filter: blur(0);
            }

            100% {
                opacity: 0;
                transform: scale(0.2) rotate(-8deg);
                filter: blur(8px);
            }
        }

        /* ============================================================
           TEXTO DE ESTADO
           ============================================================ */
        .status {
            text-align: center;
            animation: fadeIn 1s ease-out 0.4s backwards;
        }

        .status-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: #fff;
            letter-spacing: -0.025em;
            margin-bottom: 8px;
        }

        .status-subtitle {
            font-size: 0.88rem;
            font-weight: 400;
            color: rgba(255, 255, 255, 0.55);
            letter-spacing: -0.01em;
        }

        .status-subtitle .dot {
            display: inline-block;
            animation: dotBlink 1.4s ease-in-out infinite;
        }

        .status-subtitle .dot:nth-child(2) {
            animation-delay: 0.2s;
        }

        .status-subtitle .dot:nth-child(3) {
            animation-delay: 0.4s;
        }

        @keyframes dotBlink {

            0%,
            60%,
            100% {
                opacity: 0.25;
            }

            30% {
                opacity: 1;
            }
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
           BARRA DE PROGRESO
           ============================================================ */
        .progress {
            width: 200px;
            height: 3px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 3px;
            overflow: hidden;
            animation: fadeIn 1s ease-out 0.6s backwards;
        }

        .progress-bar {
            height: 100%;
            width: 0%;
            background: linear-gradient(90deg, #0071e3 0%, #4aa3ff 100%);
            border-radius: 3px;
            animation: progressFill 4.5s linear forwards;
            animation-delay: 0.5s;
            box-shadow: 0 0 10px rgba(74, 163, 255, 0.6);
        }

        @keyframes progressFill {
            from {
                width: 0%;
            }

            to {
                width: 100%;
            }
        }

        /* ============================================================
           LOGO DE MARCA (abajo)
           ============================================================ */
        .brand {
            position: fixed;
            bottom: 28px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.72rem;
            font-weight: 500;
            color: rgba(255, 255, 255, 0.4);
            letter-spacing: 0.5px;
            text-transform: uppercase;
            animation: fadeIn 1s ease-out 0.8s backwards;
        }

        .brand-dot {
            width: 4px;
            height: 4px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.4);
        }

        /* ============================================================
           FUNDIDO FINAL A NEGRO
           ============================================================ */
        .curtain {
            position: fixed;
            inset: 0;
            z-index: 100;
            background: #000;
            opacity: 0;
            pointer-events: none;
            animation: curtainFade 1s ease-in forwards;
            animation-delay: 5.2s;
        }

        @keyframes curtainFade {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        /* ============================================================
           RESPONSIVE
           ============================================================ */
        @media (max-width: 480px) {
            .grid {
                width: 140px;
                height: 140px;
                gap: 8px;
            }

            .status-title {
                font-size: 1.25rem;
            }

            .scene {
                gap: 34px;
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
</head>

<body>

    <!-- Fondo -->
    <div class="bg"></div>

    <!-- Escena -->
    <div class="scene">

        <!-- Grid 2x2 tipo "ventanas" -->
        <div class="grid">
            <div class="quad"></div>
            <div class="quad"></div>
            <div class="quad"></div>
            <div class="quad"></div>
        </div>

        <!-- Texto -->
        <div class="status">
            <div class="status-title">Cerrando sesión</div>
            <div class="status-subtitle">
                Finalizando de forma segura
                <span class="dot">.</span><span class="dot">.</span><span class="dot">.</span>
            </div>
        </div>

        <!-- Barra -->
        <div class="progress">
            <div class="progress-bar"></div>
        </div>

    </div>

    <!-- Marca inferior -->
    <div class="brand">
        <span>G-M Sistem</span>
        <span class="brand-dot"></span>
        <span><?php echo date('Y'); ?></span>
    </div>

    <!-- Fundido final -->
    <div class="curtain"></div>

    <script>
        // Redirigir tras la animación
        setTimeout(() => {
            window.location.href = 'index.php';
        }, 6000);
    </script>

</body>

</html>