<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function protegerPagina() {
    if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
        // Obtenemos la raíz del servidor para que funcione donde sea
        $host = $_SERVER['HTTP_HOST'];
        header("Location: http://$host/cfsistem/index.php"); 
        exit;
    }
}
