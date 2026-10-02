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
           FONDO
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
           ESCENA
           ============================================================ */
        .scene {
            position: relative;
            z-index: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 46px;
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
           CONTENEDOR DE LA RULETA
           ============================================================ */
        .wheel-wrap {
            position: relative;
            width: 200px;
            height: 200px;
            display: flex;
            align-items: center;
            justify-content: center;
            perspective: 1000px;
        }

        /* Halo brillante detrás */
        .wheel-wrap::before {
            content: '';
            position: absolute;
            inset: -30px;
            border-radius: 50%;
            background: radial-gradient(circle,
                    rgba(0, 113, 227, 0.35) 0%,
                    rgba(0, 113, 227, 0.1) 40%,
                    transparent 70%);
            filter: blur(20px);
            animation: haloPulse 3s ease-in-out infinite;
            z-index: 0;
            pointer-events: none;
        }

        @keyframes haloPulse {

            0%,
            100% {
                opacity: 0.6;
                transform: scale(1);
            }

            50% {
                opacity: 1;
                transform: scale(1.08);
            }
        }

        /* ============================================================
           RULETA — 4 CUADRANTES GIRANDO
           ============================================================ */
        .wheel {
            position: relative;
            width: 180px;
            height: 180px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            grid-template-rows: 1fr 1fr;
            gap: 8px;
            transform-style: preserve-3d;
            animation: wheelSpin 4.5s cubic-bezier(0.6, 0, 0.4, 1) forwards;
            animation-delay: 0.4s;
            will-change: transform;
        }

        /* Giro completo tipo ruleta */
        @keyframes wheelSpin {
            0% {
                transform: rotate(0deg) scale(1);
            }

            15% {
                transform: rotate(-15deg) scale(1.05);
            }

            70% {
                transform: rotate(540deg) scale(1);
            }

            85% {
                transform: rotate(680deg) scale(0.95);
            }

            100% {
                transform: rotate(720deg) scale(0.9);
            }
        }

        /* ============================================================
           CADA CUADRANTE
           ============================================================ */
        .quad {
            position: relative;
            border-radius: 14px;
            background: linear-gradient(145deg, #0071e3 0%, #005bb8 100%);
            box-shadow:
                0 8px 24px rgba(0, 113, 227, 0.35),
                inset 0 1px 0 rgba(255, 255, 255, 0.25),
                inset 0 -2px 6px rgba(0, 0, 0, 0.15);
            overflow: hidden;
            animation: quadClose 0.9s cubic-bezier(0.6, 0, 0.4, 1) forwards;
            will-change: opacity, transform, filter;
        }

        /* Cuadrante 1 (arriba izq) — primero */
        .quad:nth-child(1) {
            animation-delay: 3.2s;
            transform-origin: top left;
        }

        /* Cuadrante 2 (arriba der) */
        .quad:nth-child(2) {
            animation-delay: 3.5s;
            transform-origin: top right;
        }

        /* Cuadrante 4 (abajo der) */
        .quad:nth-child(4) {
            animation-delay: 3.8s;
            transform-origin: bottom right;
        }

        /* Cuadrante 3 (abajo izq) */
        .quad:nth-child(3) {
            animation-delay: 4.1s;
            transform-origin: bottom left;
        }

        /* Cada cuadrante se cierra alejándose hacia su esquina */
        @keyframes quadClose {
            0% {
                opacity: 1;
                transform: scale(1) rotate(0deg);
                filter: blur(0);
            }

            40% {
                opacity: 1;
                transform: scale(1.05) rotate(3deg);
                filter: blur(0);
            }

            100% {
                opacity: 0;
                transform: scale(0.15) rotate(-25deg);
                filter: blur(12px);
            }
        }

        /* Brillo superior cristal */
        .quad::before {
            content: '';
            position: absolute;
            top: 0;
            left: 10%;
            right: 10%;
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.9), transparent);
        }

        /* Reflejo diagonal interno */
        .quad::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg,
                    rgba(255, 255, 255, 0.22) 0%,
                    transparent 40%,
                    transparent 60%,
                    rgba(0, 0, 0, 0.18) 100%);
        }

        /* ============================================================
           CENTRO DE LA RULETA (aro decorativo)
           ============================================================ */
        .wheel-center {
            position: absolute;
            top: 50%;
            left: 50%;
            width: 16px;
            height: 16px;
            transform: translate(-50%, -50%);
            border-radius: 50%;
            background: radial-gradient(circle at 35% 35%, #4aa3ff 0%, #0071e3 60%, #004a99 100%);
            box-shadow:
                0 0 12px rgba(74, 163, 255, 0.8),
                0 0 24px rgba(0, 113, 227, 0.5),
                inset 0 1px 2px rgba(255, 255, 255, 0.5);
            z-index: 10;
            animation: centerGlow 1.5s ease-in-out infinite;
        }

        @keyframes centerGlow {

            0%,
            100% {
                box-shadow: 0 0 12px rgba(74, 163, 255, 0.8), 0 0 24px rgba(0, 113, 227, 0.5), inset 0 1px 2px rgba(255, 255, 255, 0.5);
            }

            50% {
                box-shadow: 0 0 18px rgba(74, 163, 255, 1), 0 0 36px rgba(0, 113, 227, 0.7), inset 0 1px 2px rgba(255, 255, 255, 0.6);
            }
        }

        /* ============================================================
           TEXTO
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
            width: 220px;
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
            animation: progressFill 6s linear forwards;
            animation-delay: 0.4s;
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
           MARCA
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
           FUNDIDO FINAL
           ============================================================ */
        .curtain {
            position: fixed;
            inset: 0;
            z-index: 100;
            background: #000;
            opacity: 0;
            pointer-events: none;
            animation: curtainFade 1s ease-in forwards;
            animation-delay: 5.8s;
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
            .wheel-wrap {
                width: 160px;
                height: 160px;
            }

            .wheel {
                width: 150px;
                height: 150px;
                gap: 6px;
            }

            .quad {
                border-radius: 12px;
            }

            .status-title {
                font-size: 1.25rem;
            }

            .scene {
                gap: 38px;
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

    <div class="bg"></div>

    <div class="scene">

        <!-- RULETA -->
        <div class="wheel-wrap">
            <div class="wheel">
                <div class="quad"></div>
                <div class="quad"></div>
                <div class="quad"></div>
                <div class="quad"></div>
            </div>
            <div class="wheel-center"></div>
        </div>

        <!-- TEXTO -->
        <div class="status">
            <div class="status-title">Cerrando sesión</div>
            <div class="status-subtitle">
                Finalizando de forma segura
                <span class="dot">.</span><span class="dot">.</span><span class="dot">.</span>
            </div>
        </div>

        <!-- BARRA -->
        <div class="progress">
            <div class="progress-bar"></div>
        </div>

    </div>

    <!-- Marca -->
    <div class="brand">
        <span>G-M Sistem</span>
        <span class="brand-dot"></span>
        <span><?php echo date('Y'); ?></span>
    </div>

    <!-- Fundido final -->
    <div class="curtain"></div>

    <script>
        setTimeout(() => {
            window.location.href = 'index.php';
        }, 6800);
    </script>

</body>

</html>