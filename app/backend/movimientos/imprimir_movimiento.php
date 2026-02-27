<?php
date_default_timezone_set('America/Mexico_City');
require_once $_SERVER['DOCUMENT_ROOT'] . '/cfsistem/config/conexion.php';

$id = $_GET['id'] ?? null;
if (!$id) die("ID de movimiento no proporcionado.");

// Consulta detallada del movimiento
$sql = "SELECT m.*, p.nombre as prod, p.sku, p.unidad_medida,
               a1.nombre as origen, a2.nombre as destino,
               u1.nombre as usuario_reg, u2.nombre as usuario_env, u3.nombre as usuario_rec
        FROM movimientos m
        INNER JOIN productos p ON m.producto_id = p.id
        LEFT JOIN almacenes a1 ON m.almacen_origen_id = a1.id
        LEFT JOIN almacenes a2 ON m.almacen_destino_id = a2.id
        LEFT JOIN usuarios u1 ON m.usuario_registra_id = u1.id
        LEFT JOIN usuarios u2 ON m.usuario_envia_id = u2.id
        LEFT JOIN usuarios u3 ON m.usuario_recibe_id = u3.id
        WHERE m.id = $id";

$res = $conexion->query($sql);
$m = $res->fetch_assoc();

if (!$m) die("Movimiento no encontrado.");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Comprobante_Movimiento_#<?= $id ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f0f0f0; font-family: 'Segoe UI', sans-serif; }
        .ticket { background: white; width: 21cm; min-height: 14cm; margin: 1cm auto; padding: 2cm; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        .header-table { width: 100%; border-bottom: 2px solid #333; margin-bottom: 1rem; }
        .info-box { background: #f8f9fa; border: 1px solid #dee2e6; padding: 15px; border-radius: 8px; margin-bottom: 20px; }
        .label-sm { font-size: 0.75rem; text-transform: uppercase; color: #6c757d; font-weight: bold; }
        @media print {
            body { background: white; margin: 0; }
            .ticket { box-shadow: none; margin: 0; width: 100%; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="container no-print mt-3 text-center">
        <button onclick="window.print();" class="btn btn-primary btn-lg"><i class="bi bi-printer"></i> Imprimir Comprobante</button>
    </div>

    <div class="ticket">
        <table class="header-table">
            <tr>
                <td>
                    <h2 class="fw-bold mb-0">CF SISTEM</h2>
                    <p class="text-muted small">Control de Inventarios y Almacenes</p>
                </td>
                <td class="text-end">
                    <h4 class="mb-0 text-uppercase text-primary"><?= $m['tipo'] ?></h4>
                    <p class="mb-0">Folio: <b>#<?= str_pad($m['id'], 6, "0", STR_PAD_LEFT) ?></b></p>
                    <p class="small text-muted">Generado: <?= date('d/m/Y H:i:s') ?></p>
                </td>
            </tr>
        </table>

        <div class="row info-box">
            <div class="col-6">
                <div class="label-sm">Fecha del Movimiento</div>
                <div><?= date('d/m/Y h:i A', strtotime($m['fecha'])) ?></div>
            </div>
            <div class="col-6 text-end">
                <div class="label-sm">Responsable del Registro</div>
                <div><?= $m['usuario_reg'] ?></div>
            </div>
        </div>

        <table class="table table-bordered mt-4">
            <thead class="table-light">
                <tr>
                    <th>SKU / Código</th>
                    <th>Descripción del Producto</th>
                    <th class="text-center">Cantidad</th>
                    <th class="text-center">U. Medida</th>
                </tr>
            </thead>
            <tbody>
                <tr style="height: 80px;">
                    <td><b><?= $m['sku'] ?></b></td>
                    <td><?= $m['prod'] ?></td>
                    <td class="text-center h4 fw-bold"><?= number_format($m['cantidad'], 2) ?></td>
                    <td class="text-center"><?= $m['unidad_medida'] ?? 'Pza' ?></td>
                </tr>
            </tbody>
        </table>

        <div class="row mt-4">
            <div class="col-6">
                <div class="card card-body h-100">
                    <div class="label-sm">Origen</div>
                    <div class="fw-bold text-danger"><?= $m['origen'] ?? '---' ?></div>
                </div>
            </div>
            <div class="col-6">
                <div class="card card-body h-100">
                    <div class="label-sm">Destino</div>
                    <div class="fw-bold text-success"><?= $m['destino'] ?? '---' ?></div>
                </div>
            </div>
        </div>

        <div class="mt-4">
            <div class="label-sm">Observaciones / Motivo</div>
            <p class="border-bottom pb-2"><?= $m['observaciones'] ?: 'Sin observaciones adicionales.' ?></p>
        </div>

        <div class="row mt-5 pt-5 text-center">
            <div class="col-4">
                <div style="border-top: 1px solid #000; padding-top: 5px;">
                    <small class="label-sm">Registró</small><br>
                    <small><?= $m['usuario_reg'] ?></small>
                </div>
            </div>
            <div class="col-4">
                <div style="border-top: 1px solid #000; padding-top: 5px;">
                    <small class="label-sm">Envió</small><br>
                    <small><?= $m['usuario_env'] ?: 'N/A' ?></small>
                </div>
            </div>
            <div class="col-4">
                <div style="border-top: 1px solid #000; padding-top: 5px;">
                    <small class="label-sm">Recibió</small><br>
                    <small><?= $m['usuario_rec'] ?: 'N/A' ?></small>
                </div>
            </div>
        </div>

        <footer class="mt-5 pt-5 text-center text-muted small">
            Este documento es un comprobante interno de almacén generado por CF SISTEM.
        </footer>
    </div>
</body>
</html>