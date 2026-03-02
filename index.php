<?php session_start(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>G-M SISTEM | Acceso</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        :root {
            --primary-color: #02aa02;
            --accent-color: #a80909;
            --hover-color: #128606;
        }

        body {
            font-family: 'Inter', sans-serif;
            margin: 0;
            height: 100vh;
            background: #f4f7fb;
            overflow: hidden;
        }

        .split-container { display: flex; height: 100vh; }

        /* LADO IZQUIERDO */
        .left-side { width: 50%; position: relative; overflow: hidden; }
        .carousel-item img { height: 100vh; object-fit: cover; filter: brightness(0.7); }
        .carousel-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(45deg, rgba(0,0,0,0.6) 0%, rgba(0,0,0,0.2) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10;
        }

        /* LADO DERECHO */
        .right-side { width: 50%; display: flex; justify-content: center; align-items: center; padding: 20px; }
        
        .login-card {
            background: #ffffff;
            padding: 40px;
            border-radius: 24px;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 25px 50px -12px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }

        .logo-title {
            font-weight: 800;
            font-size: 32px;
            letter-spacing: -1px;
            color: var(--primary-color);
            margin-bottom: 5px;
        }

        .logo-subtitle { color: #6b7280; font-size: 14px; margin-bottom: 35px; }

        /* INPUTS */
        .input-group-text { background: #f9fafb; border-right: none; color: #9ca3af; border-radius: 12px 0 0 12px; }
        .form-control { 
            background: #f9fafb; 
            border-left: none; 
            border-radius: 0 12px 12px 0; 
            padding: 12px; 
            font-size: 15px;
        }
        .form-control:focus { background: #fff; box-shadow: none; border-color: #dee2e6; }
        .input-group:focus-within { box-shadow: 0 0 0 4px rgba(2, 170, 2, 0.1); border-radius: 12px; }

        /* BOTÓN */
        .btn-login {
            border-radius: 12px;
            padding: 14px;
            font-weight: 600;
            background: var(--accent-color);
            border: none;
            transition: all 0.3s ease;
            margin-top: 10px;
        }
        .btn-login:hover { background: var(--hover-color); transform: translateY(-1px); }

        .login-footer { font-size: 13px; color: #9ca3af; margin-top: 30px; }

        @media(max-width: 992px){
            .left-side { display: none; }
            .right-side { width: 100%; }
        }
    </style>
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
                        <input type="text" name="usuario" class="form-control" placeholder="Ej: admin" required>
                    </div>
                </div>

                <div class="mb-4 text-start">
                    <label class="form-label fw-semibold small">Contraseña</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-lock"></i></span>
                        <input type="password" name="password" class="form-control" placeholder="••••••••" required>
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
document.getElementById('formLogin').addEventListener('submit', async (e) => {
    e.preventDefault();

    const btn = document.getElementById('btnIngresar');
    const originalText = btn.innerHTML;
    
    // Estado de carga
    btn.disabled = true;
    btn.innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Validando...`;

    const formData = new FormData(e.target);

    try {
        const response = await fetch('validar_login.php', {
            method: 'POST',
            body: formData
        });

        // Verificamos si la respuesta es JSON válido
        const res = await response.json();

        if (res.status === 'success') {
            Swal.fire({
                icon: 'success',
                title: '¡Acceso Correcto!',
                text: res.message,
                showConfirmButton: false,
                timer: 1500,
                timerProgressBar: true
            }).then(() => {
                window.location.href = 'app/views/inicio.php';
            });
        } else {
            Swal.fire({
                icon: res.status, // error o warning
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