<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/cfsistem/config/conexion.php';
session_start();
header('Content-Type: application/json');

$usuario_id = $_SESSION['usuario_id'] ?? 0;
$almacen_sesion = $_SESSION['almacen_id'] ?? 0;
$rol_id = $_SESSION['rol_id'] ?? 0;

if (!$usuario_id) {
    echo json_encode(['cantidad' => 0]);
    exit;
}

$esAdmin = ($rol_id == 1);
$whereAlmacen = $esAdmin ? "" : " AND m.almacen_destino_id = $almacen_sesion";

// ============================================================
// 1) TRASPASOS SIMPLES (los que NO están en traspaso_masivo)
// ============================================================
$sqlSimple = "
    SELECT
        'simple'                          AS tipo_fila,
        m.id                              AS movimiento_id,
        NULL                              AS id_traspaso_id,
        NULL                              AS codigo_traspaso,
        m.almacen_destino_id,
        a.nombre                          AS destino,
        p.nombre                          AS prod,
        m.cantidad                        AS cant_total,
        p.factor_conversion,
        p.unidad_reporte,
        u.nombre                          AS emisor,
        ao.nombre                         AS origen,
        DATE_FORMAT(m.fecha, '%H:%i')     AS hora,
        m.fecha                           AS fecha_orden,
        NULL                              AS productos_lista,
        NULL                              AS total_productos
    FROM movimientos m
    INNER JOIN productos p ON m.producto_id = p.id
    LEFT JOIN almacenes ao ON m.almacen_origen_id = ao.id
    LEFT JOIN almacenes a  ON m.almacen_destino_id = a.id
    LEFT JOIN usuarios u   ON m.usuario_registra_id = u.id
    LEFT JOIN traspaso_masivo tm ON tm.movimiento_id = m.id
    WHERE m.tipo = 'traspaso'
      AND m.usuario_recibe_id IS NULL
      AND tm.movimiento_id IS NULL
      $whereAlmacen
";

// ============================================================
// 2) TRASPASOS MASIVOS (agrupados por id_traspaso_id)
// ============================================================
$sqlMasivo = "
    SELECT
        'masivo'                          AS tipo_fila,
        MIN(m.id)                         AS movimiento_id,
        tm.id_traspaso_id                 AS id_traspaso_id,
        tm.codigo_traspaso                AS codigo_traspaso,
        m.almacen_destino_id,
        a.nombre                          AS destino,
        NULL                              AS prod,
        SUM(m.cantidad)                   AS cant_total,
        MAX(p.factor_conversion)          AS factor_conversion,
        MAX(p.unidad_reporte)             AS unidad_reporte,
        u.nombre                          AS emisor,
        ao.nombre                         AS origen,
        DATE_FORMAT(MIN(m.fecha), '%H:%i') AS hora,
        MIN(m.fecha)                      AS fecha_orden,
        GROUP_CONCAT(
            CONCAT(
                p.nombre,
                '§',
                m.cantidad,
                '§',
                p.factor_conversion,
                '§',
                p.unidad_reporte
            )
            SEPARATOR '|'
        )                                 AS productos_lista,
        COUNT(*)                          AS total_productos
    FROM movimientos m
    INNER JOIN productos p ON m.producto_id = p.id
    LEFT JOIN almacenes ao ON m.almacen_origen_id = ao.id
    LEFT JOIN almacenes a  ON m.almacen_destino_id = a.id
    LEFT JOIN usuarios u   ON m.usuario_registra_id = u.id
    INNER JOIN traspaso_masivo tm ON tm.movimiento_id = m.id
    WHERE m.tipo = 'traspaso'
      AND m.usuario_recibe_id IS NULL
      $whereAlmacen
    GROUP BY
        tm.id_traspaso_id,
        tm.codigo_traspaso,
        m.almacen_destino_id,
        a.nombre,
        u.nombre,
        ao.nombre
";

// ============================================================
// UNION + orden final
// ============================================================
$sql = "($sqlSimple) UNION ALL ($sqlMasivo) ORDER BY fecha_orden DESC";

$res = $conexion->query($sql);
if (!$res) {
    http_response_code(500);
    echo json_encode(['cantidad' => 0, 'error' => $conexion->error]);
    exit;
}

$items = [];

/**
 * Convierte una cantidad base + factor + unidad en el texto inteligente
 * (ej. "3 BULTO + 5 PZA" o "5 PZA").
 */
function formatearCantidad($cant, $factor, $unidad)
{
    $cant = (float) $cant;
    $factor = (float) $factor > 0 ? (float) $factor : 1;
    $unidad = !empty($unidad) ? $unidad : 'MLL';

    if ($cant < $factor) {
        return number_format($cant, 0) . " PZA";
    }

    $enteros = floor($cant / $factor);
    $sobrante = $cant % $factor;

    $texto = $enteros . " " . $unidad;
    if ($sobrante > 0) {
        $texto .= " + " . number_format($sobrante, 0) . " PZA";
    }
    return $texto;
}

while ($row = $res->fetch_assoc()) {

    if ($row['tipo_fila'] === 'masivo') {

        // Parsear la lista de productos (separador '|' entre productos,
        // y '§' entre campos internos: nombre, cantidad, factor, unidad)
        $productos = [];
        if (!empty($row['productos_lista'])) {
            $partes = explode('|', $row['productos_lista']);
            foreach ($partes as $parte) {
                $campos = explode('§', $parte);
                if (count($campos) < 4)
                    continue;

                [$nombre, $cant, $factor, $unidad] = $campos;
                $textoCantidad = formatearCantidad($cant, $factor, $unidad);

                $productos[] = [
                    'nombre' => $nombre,
                    'cantidad_texto' => $textoCantidad
                ];
            }
        }

        $items[] = [
            'tipo' => 'masivo',
            'id' => (int) $row['movimiento_id'], // representativo
            'id_traspaso_id' => (int) $row['id_traspaso_id'],
            'codigo_traspaso' => $row['codigo_traspaso'],
            'producto' => 'Despacho Masivo', // título genérico
            'productos' => $productos,        // lista completa
            'total_productos' => (int) $row['total_productos'],
            'emisor' => $row['emisor'] ?? 'Sistema',
            'destino' => $row['destino'] ?? 'Sistema',
            'origen' => $row['origen'] ?? 'Almacén',
            'cantidad_texto' => formatearCantidad(
                $row['cant_total'],
                $row['factor_conversion'],
                $row['unidad_reporte']
            ),
            'hora' => $row['hora']
        ];

    } else {

        // Traspaso simple (igual que antes)
        $items[] = [
            'tipo' => 'simple',
            'id' => (int) $row['movimiento_id'],
            'id_traspaso_id' => null,
            'codigo_traspaso' => null,
            'producto' => $row['prod'],
            'emisor' => $row['emisor'] ?? 'Sistema',
            'destino' => $row['destino'] ?? 'Sistema',
            'cantidad_texto' => formatearCantidad(
                $row['cant_total'],
                $row['factor_conversion'],
                $row['unidad_reporte']
            ),
            'origen' => $row['origen'] ?? 'Almacén',
            'hora' => $row['hora']
        ];
    }
}

echo json_encode([
    'cantidad' => count($items),
    'items' => $items
]);