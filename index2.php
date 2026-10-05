<?php
// test_correo_terminos.php
// Prueba: enviar el HTML de Términos y Condiciones como PDF adjunto

error_reporting(E_ALL);
ini_set('display_errors', 1);

$url = 'http://localhost/cfsistem/app/controllers/correoController.php';

// ============================================
// Capturamos el HTML de términos y condiciones
// ============================================
ob_start();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Términos y Condiciones de Uso | CF System</title>
    <style>
        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            color: #333;
            line-height: 1.5;
            font-size: 11px;
        }

        h1 {
            color: #1e293b;
            font-size: 20px;
            margin-bottom: 5px;
        }

        h4 {
            color: #0d6efd;
            font-size: 14px;
            margin-top: 20px;
        }

        h5 {
            color: #1e293b;
            font-size: 13px;
            margin-top: 20px;
            border-bottom: 1px solid #ccc;
            padding-bottom: 4px;
        }

        p,
        li {
            text-align: justify;
            margin: 6px 0;
        }

        ul,
        ol {
            margin: 6px 0 6px 18px;
        }

        .header {
            background: #1e293b;
            color: #fff;
            padding: 20px;
            text-align: center;
            margin-bottom: 20px;
        }

        .header h1 {
            color: #fff;
            margin: 0;
        }

        .header p {
            color: #cbd5e1;
            margin: 5px 0 0;
        }

        .alert {
            background: #e7f1ff;
            border-left: 4px solid #0d6efd;
            padding: 10px;
            margin-bottom: 15px;
        }

        .footer {
            margin-top: 30px;
            padding-top: 10px;
            border-top: 1px solid #ccc;
            text-align: center;
            font-size: 10px;
            color: #888;
        }
    </style>
</head>

<body>

    <div class="header">
        <h1>Términos y Condiciones de Servicio</h1>
        <p>Contrato de Adhesión para el Licenciamiento de Software SaaS (CF System)</p>
    </div>

    <p><strong>Última actualización:</strong> <?php echo date('d/m/Y'); ?></p>

    <div class="alert">
        <strong>Aviso Importante:</strong> Al registrarse, ingresar o hacer uso de la plataforma, usted acepta
        formalmente el cumplimiento de estas cláusulas bajo el marco de los artículos 1803 y 1805 del Código Civil
        Federal y el artículo 80 del Código de Comercio de los Estados Unidos Mexicanos.
    </div>

    <h4>DECLARACIÓN PRELIMINAR Y ACEPTACIÓN DEL USUARIO</h4>
    <p>El presente instrumento regula el acceso, uso, licenciamiento y operación del sistema informático de gestión
        empresarial (en adelante, <strong>"El Sistema"</strong>). Al presionar el botón de aceptación, registrarse,
        contratar, ingresar o hacer uso de la plataforma, la persona física o moral (en adelante, <strong>"El
            Cliente"</strong>) manifiesta su consentimiento expreso, libre e informado.</p>

    <h5>CLÁUSULA PRIMERA: DEL OBJETO Y ESQUEMA DE CONTRATACIÓN</h5>
    <ol>
        <li><strong>Modelos de Cobro y Módulos Adicionales:</strong> La renta o contraprestación por el uso de El
            Sistema se calculará de manera individual por cada almacén, nodo o sucursal contratada.</li>
        <li><strong>Exclusión de Servicios Gratuitos:</strong> El Sistema no otorgará bajo ninguna circunstancia
            módulos, funciones o servicios gratuitos, salvo convenio especial por escrito.</li>
        <li><strong>Periodos de Prueba y Suspensión:</strong> Los periodos de prueba tendrán una duración improrrogable
            de siete (7) días naturales a un (1) mes calendario como máximo.</li>
    </ol>

    <h5>CLÁUSULA SEGUNDA: CICLO DE FACTURACIÓN, PRORRATEO, SUSPENSIÓN Y PAGOS</h5>
    <ol>
        <li><strong>Periodicidad Mensual y Fecha de Corte:</strong> El esquema es estrictamente mensual, fijando como
            fecha única de vencimiento el día <strong>primero (1) de cada mes calendario</strong>.</li>
        <li><strong>Periodos de Gracia y Prorrateos:</strong> Si la contratación se realiza a partir del día 28 del mes,
            se otorgará un periodo de gracia no cobrable hasta el fin de dicho mes.</li>
        <li><strong>Mora y Suspensión:</strong> Si no se liquida la mensualidad el día 1, el acceso se suspenderá
            automáticamente, con un periodo de tolerancia de quince (15) días naturales.</li>
        <li><strong>Validación de Transferencias:</strong> La reactivación se procesará tras la verificación manual del
            ingreso de fondos en las cuentas bancarias de La Empresa.</li>
    </ol>

    <h5>CLÁUSULA TERCERA: DESARROLLO PERSONALIZADO, COSTOS Y PENALIZACIONES</h5>
    <ol>
        <li><strong>Cotización y Anticipo Inicial:</strong> Cualquier desarrollo a medida tendrá un costo base mínimo de
            <strong>$3,000.00 MXN</strong>, con un pago inicial del 50%.
        </li>
        <li><strong>Obligación de Continuidad:</strong> El Cliente debe mantener activa la contratación durante el
            desarrollo del módulo personalizado.</li>
        <li><strong>Pena Convencional:</strong> Si cancela durante el primer mes, cubrirá hasta el 50% del costo total
            del módulo contratado.</li>
    </ol>

    <h5>CLÁUSULA CUARTA: TUTORÍAS, CAPACITACIONES Y VIÁTICOS</h5>
    <ol>
        <li><strong>Tutoría Virtual Incluida:</strong> Una (1) sesión inicial en línea de máximo tres (3) horas.</li>
        <li><strong>Sesiones Adicionales:</strong> Virtual $400.00 MXN/hora; Presencial $600.00 MXN/hora.</li>
        <li><strong>Viáticos:</strong> En tutorías presenciales, El Cliente reembolsará el 100% de los gastos de
            traslado, hospedaje y alimentación.</li>
    </ol>

    <h5>CLÁUSULA QUINTA: CONTINUIDAD DEL SERVICIO (SLA) Y DESLINDES</h5>
    <ol>
        <li><strong>Compensación por Caídas:</strong> Los días de inactividad atribuibles a La Empresa serán compensados
            con descuento proporcional.</li>
        <li><strong>Falla Estructural Masiva:</strong> Se reembolsará el 100% de la mensualidad pagada como tope máximo
            de responsabilidad.</li>
        <li><strong>Exención por Infraestructura del Cliente:</strong> La Empresa no asume responsabilidad por fallas en
            equipos, redes o servidores del Cliente.</li>
    </ol>

    <h5>CLÁUSULA SEXTA: RESPONSABILIDAD SOBRE LA INFORMACIÓN</h5>
    <ol>
        <li><strong>Imputabilidad de Operaciones:</strong> El Cliente es el único responsable por la creación,
            modificación o eliminación de registros.</li>
        <li><strong>Credenciales de Acceso:</strong> La custodia de usuarios y contraseñas corresponde exclusivamente a
            El Cliente.</li>
    </ol>

    <h5>CLÁUSULA SÉPTIMA: TRATAMIENTO DE DATOS PERSONALES (LFPDPPP)</h5>
    <p>El Cliente actúa como único <strong>Responsable</strong> del tratamiento de datos personales de sus clientes,
        proveedores y empleados. La Empresa actúa como <strong>Encargado</strong> del tratamiento tecnológico.</p>

    <h5>CLÁUSULA OCTAVA: ARQUITECTURA DE ALMACENES Y COMPATIBILIDAD</h5>
    <ol>
        <li><strong>Almacenes Independientes:</strong> No existirá vinculación ni sincronización de datos entre
            almacenes individuales.</li>
        <li><strong>Modalidad Multialmacén:</strong> Las sucursales compartirán catálogos, manteniendo independencia en
            inventario.</li>
        <li><strong>Compatibilidad de Planes:</strong> La vinculación requiere que todos los almacenes pertenezcan al
            mismo plan (N con N).</li>
        <li><strong>Ventana de Migración:</strong> La conversión de Individual a Multialmacén requiere hasta siete (7)
            días hábiles.</li>
    </ol>

    <h5>CLÁUSULA NOVENA: NOTIFICACIÓN DE RESCISIÓN Y DATOS SUSPENDIDOS</h5>
    <ol>
        <li><strong>Notificación:</strong> Se realizará por correo electrónico o WhatsApp, perfeccionándose a los dos
            (2) días hábiles.</li>
        <li><strong>Datos Suspendidos:</strong> La base de datos no se elimina; permanece resguardada en estado
            suspendido.</li>
        <li><strong>Módulo de Exportación:</strong> El Cliente podrá descargar su información comercial primaria.</li>
    </ol>

    <h5>CLÁUSULA DÉCIMA: PROPIEDAD INTELECTUAL E INGENIERÍA INVERSA</h5>
    <ol>
        <li><strong>Protección Legal:</strong> El Sistema está protegido por la Ley Federal del Derecho de Autor y la
            Ley de Propiedad Industrial.</li>
        <li><strong>Prohibición:</strong> Queda prohibida la ingeniería inversa, descompilación o modificación del
            código fuente.</li>
        <li><strong>Sanciones:</strong> La infracción faculta a La Empresa a rescindir el servicio y ejercitar acciones
            civiles y penales.</li>
    </ol>

    <h5>CLÁUSULA DÉCIMA PRIMERA: CASO FORTUITO Y FUERZA MAYOR</h5>
    <p>La Empresa no será responsable por interrupciones causadas por desastres naturales, cortes de energía, actos de
        autoridad, huelgas, fallas de proveedores de nube o ciberataques masivos.</p>

    <h5>CLÁUSULA DÉCIMA SEGUNDA: CONFIDENCIALIDAD (LFPPI)</h5>
    <p>Ambas partes se obligan a guardar estricta confidencialidad respecto a la información técnica, comercial y
        operativa compartida durante la relación contractual.</p>

    <h5>CLÁUSULA DÉCIMA TERCERA: AJUSTES TARIFARIOS</h5>
    <p>La Empresa se reserva el derecho de modificar tarifas, notificando con al menos <strong>treinta (30) días
            naturales de anticipación</strong>.</p>

    <h5>CLÁUSULA DÉCIMA CUARTA: MANTENIMIENTOS PROGRAMADOS</h5>
    <p>Los mantenimientos programados en horarios de bajo tráfico no darán lugar a compensaciones.</p>

    <h5>CLÁUSULA DÉCIMA QUINTA: CIBERSEGURIDAD Y RESPALDOS</h5>
    <ol>
        <li><strong>Suspensión de Emergencia:</strong> La Empresa puede suspender el acceso ante incidentes críticos de
            seguridad.</li>
        <li><strong>Respaldos:</strong> Los backups pueden no estar 100% actualizados al último segundo previo al
            incidente.</li>
    </ol>

    <h5>CLÁUSULA DÉCIMA SEXTA: CESIÓN, REVENTA Y MULTA</h5>
    <ol>
        <li><strong>Derecho de Cesión:</strong> La Empresa puede ceder la titularidad de El Sistema sin responsabilidad
            alguna.</li>
        <li><strong>Prohibición de Reventa:</strong> Queda prohibido sublicenciar, revender o compartir accesos.</li>
        <li><strong>Sanciones:</strong> La reventa no autorizada faculta a La Empresa a suspender la cuenta y exigir
            multa convencional.</li>
    </ol>

    <h5>CLÁUSULA DÉCIMA SÉPTIMA: DIVISIBILIDAD E INTEGRIDAD</h5>
    <p>Si alguna cláusula es declarada nula, las restantes mantienen su plena validez. Este instrumento constituye la
        manifestación completa de la voluntad entre las partes.</p>

    <h5>CLÁUSULA DÉCIMA OCTAVA: JURISDICCIÓN Y LEGISLACIÓN APLICABLE</h5>
    <p>Las partes se someten a las leyes de los Estados Unidos Mexicanos y a la jurisdicción de los tribunales
        competentes de la Ciudad de México.</p>

    <div class="footer">
        &copy; <?php echo date('Y'); ?> CF System. Todos los derechos reservados.
    </div>

</body>

</html>
<?php
$htmlTerminos = ob_get_clean();

// ============================================
// Datos del correo
// ============================================
$datos = [
    'modo' => 'archivos',
    'para' => 'saulenriquealbatapia252@gmail.com',
    'asunto' => 'Términos y Condiciones - CF System',
    'contenido' => '<h2>Bienvenido a CF System</h2><p>Adjunto encontrarás nuestros Términos y Condiciones de Servicio en formato PDF.</p><p>Por favor, revísalos y consérvalos para futuras referencias.</p>',
    'adjuntos' => [
        [
            'html' => $htmlTerminos,
            'nombre' => 'Terminos_y_Condiciones_CFSystem.pdf'
        ]
    ]
];

// ============================================
// Enviar por cURL
// ============================================
$ch = curl_init($url);
curl_setopt_array($ch, [
    CURLOPT_POST => true,
    CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
    CURLOPT_POSTFIELDS => json_encode($datos),
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 60,
]);

$respuesta = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$errorCurl = curl_error($ch);
curl_close($ch);

// ============================================
// Mostrar resultado
// ============================================
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Prueba de envío con PDF</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light p-5">
    <div class="container" style="max-width: 800px;">

        <h1 class="mb-4">📄 Prueba: Enviar Términos y Condiciones como PDF</h1>

        <div class="card mb-4">
            <div class="card-header bg-dark text-white">
                <strong>Diagnóstico</strong>
            </div>
            <div class="card-body">
                <p><strong>URL llamada:</strong> <?= htmlspecialchars($url) ?></p>
                <p><strong>Código HTTP:</strong> <?= $httpCode ?></p>
                <p><strong>Error cURL:</strong> <?= $errorCurl ? htmlspecialchars($errorCurl) : 'Ninguno' ?></p>
                <p><strong>Tamaño HTML enviado:</strong> <?= number_format(strlen($htmlTerminos)) ?> caracteres</p>
            </div>
        </div>

        <?php if ($errorCurl): ?>
            <div class="alert alert-danger">
                <strong>❌ Error de conexión:</strong> <?= htmlspecialchars($errorCurl) ?>
            </div>
        <?php else: ?>
            <?php
            $data = json_decode($respuesta, true);
            $ok = $data['ok'] ?? false;
            ?>
            <div class="alert alert-<?= $ok ? 'success' : 'danger' ?>">
                <h4 class="alert-heading"><?= $ok ? '✅ Éxito' : '❌ Error' ?></h4>
                <p class="mb-0">
                    <?= htmlspecialchars($ok ? ($data['mensaje'] ?? '') : ($data['error'] ?? 'Error desconocido')) ?>
                </p>
            </div>

            <div class="card">
                <div class="card-header bg-dark text-white">
                    <strong>Respuesta completa del servidor</strong>
                </div>
                <div class="card-body">
                    <pre class="mb-0"
                        style="font-size: 13px;"><?= htmlspecialchars(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) ?></pre>
                </div>
            </div>
        <?php endif; ?>

        <div class="mt-4">
            <a href="test_correo_terminos.php" class="btn btn-primary">🔄 Reintentar</a>
        </div>

    </div>
</body>

</html>