<?php
session_start();
$_SESSION = [];
session_destroy();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Saliendo del Sistema | G-M SISTEM</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --truck-body: #a80909; /* Rojo de tu sistema */
            --truck-cab: #333;
            --wheel-color: #222;
            --road-color: #dee2e6;
        }

        body {
            background-color: #f4f7fb;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            overflow: hidden;
            font-family: 'Inter', sans-serif;
        }

        /* ESCENARIO */
        .stage {
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 100%;
            perspective: 1000px;
        }

        /* GLOBO DE TEXTO */
        .bubble {
            background: #02aa02;
            color: white;
            padding: 12px 25px;
            border-radius: 15px;
            font-weight: bold;
            position: absolute;
            top: -60px;
            opacity: 0;
            transform: scale(0);
            animation: pop-text 0.5s 3s forwards;
            z-index: 100;
        }

        .bubble::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            border-left: 10px solid transparent;
            border-right: 10px solid transparent;
            border-top: 10px solid #02aa02;
        }

        /* --- CAMIÓN CSS --- */
        .truck-container {
            position: relative;
            width: 200px;
            height: 100px;
            /* Animación: El camión avanza y se va */
            animation: drive-away 5s ease-in-out forwards;
        }

        /* CABINA */
        .cab {
            position: absolute;
            width: 60px;
            height: 70px;
            background: var(--truck-cab);
            right: 0;
            bottom: 25px;
            border-radius: 10px 15px 5px 5px;
        }

        .window {
            position: absolute;
            width: 35px;
            height: 25px;
            background: #87CEEB;
            top: 10px;
            right: 5px;
            border-radius: 5px;
        }

        /* REMOLQUE (CARGA) */
        .trailer {
            position: absolute;
            width: 135px;
            height: 80px;
            background: var(--truck-body);
            left: 0;
            bottom: 25px;
            border-radius: 5px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 14px;
            border-right: 3px solid rgba(0,0,0,0.1);
        }

        /* RUEDAS */
        .wheel {
            position: absolute;
            width: 25px;
            height: 25px;
            background: var(--wheel-color);
            border-radius: 50%;
            bottom: 12px;
            border: 3px dashed #555;
            animation: spin 0.5s infinite linear;
        }
        .w1 { left: 15px; }
        .w2 { left: 45px; }
        .w3 { right: 8px; }

        /* HUMO DEL ESCAPE */
        .smoke {
            position: absolute;
            width: 10px;
            height: 10px;
            background: #bbb;
            border-radius: 50%;
            left: -10px;
            bottom: 30px;
            opacity: 0;
            animation: exhaust 0.8s infinite;
        }

        /* CARRETERA */
        .road {
            width: 100%;
            height: 4px;
            background: var(--road-color);
            position: absolute;
            bottom: 10px;
            border-radius: 2px;
        }

        /* --- ANIMACIONES --- */
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        @keyframes drive-away {
            0% { transform: scale(0.5) translateX(-200%); opacity: 0; }
            20% { transform: scale(1) translateX(0); opacity: 1; }
            80% { transform: scale(1.1) translateX(20px); opacity: 1; }
            100% { transform: scale(0.2) translateX(1500px); opacity: 0; }
        }

        @keyframes pop-text {
            to { opacity: 1; transform: scale(1); }
        }

        @keyframes exhaust {
            0% { transform: translate(0, 0) scale(1); opacity: 0.8; }
            100% { transform: translate(-30px, -20px) scale(2); opacity: 0; }
        }

        /* FUNDIDO FINAL */
        .curtain {
            position: fixed;
            inset: 0;
            background: #000;
            z-index: 999;
            opacity: 0;
            pointer-events: none;
            animation: fade-out 1s 4.5s forwards;
        }

        @keyframes fade-out { to { opacity: 1; } }
    </style>
</head>
<body>

    <div class="curtain"></div>

    <div class="stage">
        <div class="bubble">¡Ruta completada, adiós! 🚛</div>

        <div class="truck-container">
            <div class="smoke"></div>
            <div class="trailer"><b>G-M SISTEM</b></div>
            <div class="cab">
                <div class="window"></div>
            </div>
            <div class="wheel w1"></div>
            <div class="wheel w2"></div>
            <div class="wheel w3"></div>
        </div>
        
        <div class="road"></div>
        <p class="text-muted mt-5">Cerrando sesión de forma segura...</p>
    </div>

    <script>
        setTimeout(() => {
            window.location.href = 'index.php';
        }, 5500);
    </script>
</body>
</html>