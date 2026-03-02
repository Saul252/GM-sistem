<?php
function puedeVerModulo(string $modulo): bool
{
    if (!isset($_SESSION['rol'])) {
        return false;
    }

  $permisos = [

    'administrador' => [
        'inicio',
        'ventas',
        'compras',
        'almacenes',
        'clientes',
        'movimientos',
        'ventashistorial', 
        'usuarios',
        'mermas',
        'finanzas'
    ],

    'gestor_almacen' => [
        'inicio',
        'ventashistorial',
        
        'almacenes',
        'movimientos'
    ],

    'cajero' => [
        'inicio'
    ]
];

    $rol = strtolower($_SESSION['rol']);

    return isset($permisos[$rol]) && in_array($modulo, $permisos[$rol]);
}
?>