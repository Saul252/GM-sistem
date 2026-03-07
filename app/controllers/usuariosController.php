<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../controllers/LayoutController.php';
require_once __DIR__ . '/../models/usuariosModel.php';


$modelo = new UsuarioModel($conexion);

// Variables para la vista
$paginaActual = 'Usuarios';
$usuarios = $modelo->listarUsuarios();
$rolesArray = $modelo->getRoles();
$almacenesArray = $modelo->getAlmacenes();

// Cargar la vista (archivo HTML)
include __DIR__ . '/../views/usuarios_view.php';