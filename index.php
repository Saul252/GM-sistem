<?php session_start(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title> G-M SISTEM</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">

<style>
body {
    font-family: 'Inter', sans-serif;
    margin: 0;
    height: 100vh;
    overflow: hidden;
}

/* ======== LAYOUT 50/50 ======== */
.split-container {
    display: flex;
    height: 100vh;
}

/* ======== LADO IZQUIERDO (CARRUSEL) ======== */
.left-side {
    width: 50%;
    position: relative;
}

.carousel-item img {
    height: 100vh;
    object-fit: cover;
}

/* Overlay elegante */
.carousel-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(to bottom, rgba(0,0,0,0.4), rgba(0,0,0,0.4));
}

/* ======== LADO DERECHO ======== */
.right-side {
    width: 50%;
    background: #f4f7fb;
    display: flex;
    justify-content: center;
    align-items: center;
}

/* ======== CARD LOGIN ======== */
.login-card {
    background: #ffffff;
    padding: 45px;
    border-radius: 18px;
    width: 380px;
    box-shadow: 0 20px 50px rgba(0,0,0,0.08);
}

/* ======== LOGO ======== */
.logo-title {
    font-weight: 700;
    font-size: 28px;
    letter-spacing: 3px;
    color: #02aa02;
}

.logo-subtitle {
    font-size: 13px;
    color: #6c757d;
    margin-bottom: 30px;
}

/* ======== INPUTS ======== */
.form-control {
    border-radius: 10px;
    padding: 12px;
    border: 1px solid #dee2e6;
}

.form-control:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 2px rgba(13,110,253,.15);
}

/* ======== BOTÓN ======== */
.btn-login {
    border-radius: 12px;
    padding: 12px;
    font-weight: 600;
    background: #a80909;
    border: none;
    transition: 0.3s;
}

.btn-login:hover {
    background: #128606;
}

/* ======== FOOTER ======== */
.login-footer {
    font-size: 12px;
    color: #6c757d;
    margin-top: 20px;
    text-align: center;
}

/* ======== RESPONSIVE ======== */
@media(max-width: 992px){
    .left-side {
        display: none;
    }
    .right-side {
        width: 100%;
    }
}
</style>
</head>

<body>

<div class="split-container">

    <!-- LADO IZQUIERDO -->
    <div class="left-side">
        <div id="labCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel" data-bs-interval="4000">
            <div class="carousel-inner">

                <div class="carousel-item active">
                    <img src="public/assets/almacen3.jpg" class="d-block w-100">
                </div>

                <div class="carousel-item">
                    <img src="public/assets/almacen2.jpg" class="d-block w-100">
                </div>

                <div class="carousel-item">
                    <img src="public/assets/almacen3.jpg" class="d-block w-100">
                </div>

            </div>
        </div>
        <div class="carousel-overlay"></div>
    </div>

    <!-- LADO DERECHO -->
    <div class="right-side">

        <div class="login-card text-center">

            <div class="logo-title">G-M SISTEM</div>
            <div class="logo-subtitle">
                Sistema de Gestion de Inventarios
            </div>

            <form method="POST" action="validar_login.php">
                <div class="mb-3 text-start">
                    <label class="form-label">Usuario</label>
                    <input type="text" name="usuario" class="form-control" required>
                </div>

                <div class="mb-3 text-start">
                    <label class="form-label">Contraseña</label>
                    <input type="password" name="password" class="form-control" required>
                </div>

                <button class="btn btn-login w-100 text-white">
                    Ingresar
                </button>
            </form>

            <div class="login-footer">
                © <?php echo date('Y'); ?> G-M SISTEM • Acceso restringido
            </div>

        </div>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>