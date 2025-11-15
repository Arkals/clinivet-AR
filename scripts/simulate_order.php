<?php
// Simula la generación de factura y el envío de correo sin usar la base de datos.
require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../backend/app/Helpers/FacturaPDF.php';
require __DIR__ . '/../backend/app/Helpers/PACClient.php';

$now = time();
$orderId = 'sim_' . $now;
$xmlPath = __DIR__ . '/../storage/invoices/order_' . $orderId . '.xml';
$pdfPath = __DIR__ . '/../storage/invoices/order_' . $orderId . '.pdf';
@mkdir(dirname($xmlPath), 0777, true);

$emisor = [
    'rfc' => 'AAA010101AAA',
    'nombre' => 'Clinivet S.A. de C.V.',
    'regimen' => '601',
    'razon_social' => 'Clinivet S.A. de C.V.'
];
$receptor = [
    'rfc' => 'XAXX010101000',
    'nombre' => 'Cliente Simulado',
    'email' => 'test@example.local',
    'uso_cfdi' => 'G03'
];
$productos = [
    ['name'=>'Producto A','qty'=>2,'price'=>45.5],
    ['name'=>'Producto B','qty'=>1,'price'=>12.0],
];
$total = 2*45.5 + 1*12.0;
$serie = 'SIM';
$folio = $orderId;
$uuid = strtoupper(bin2hex(random_bytes(8)));
$fecha = date('Y-m-d H:i:s');
$metodo_pago = 'PUE';
$forma_pago = '04';

// Crear XML simple
$xml = '<?xml version="1.0" encoding="UTF-8"?>\n';
$xml .= '<cfdi:Comprobante Serie="' . $serie . '" Folio="' . $folio . '" Fecha="' . $fecha . '" Total="' . number_format($total,2,'.','') . '" />\n';
file_put_contents($xmlPath, $xml);

echo "XML creado: $xmlPath\n";

// Generar PDF usando la función existente
try {
    $pdfData = \generarFacturaPDF($emisor, $receptor, $productos, $total, $uuid, $folio, $fecha, $metodo_pago, $forma_pago);
    file_put_contents($pdfPath, $pdfData);
    echo "PDF creado: $pdfPath (" . strlen($pdfData) . " bytes)\n";
} catch (Exception $e) {
    echo "Error generando PDF: " . $e->getMessage() . "\n";
}

// Intentar enviar email con PHPMailer si está disponible
$to = $receptor['email'];
$subject = "Factura simulada $serie-$folio";
$body = "Adjuntamos la factura simulada.\n";

if (class_exists('PHPMailer\\PHPMailer\\PHPMailer')) {
    $mailClass = 'PHPMailer\\PHPMailer\\PHPMailer';
    $mail = new $mailClass(true);
    try {
        $mail->isSMTP();
        $mail->Host = getenv('SMTP_HOST') ?: 'smtp.example.local';
        $mail->SMTPAuth = false;
        $mail->setFrom('no-reply@example.local', 'MiTienda');
        $mail->addAddress($to);
        $mail->Subject = $subject;
        $mail->Body = $body;
        if (file_exists($pdfPath)) $mail->addAttachment($pdfPath);
        $mail->send();
        echo "Email enviado (PHPMailer) a $to\n";
    } catch (Exception $e) {
        echo "PHPMailer fallo: " . $e->getMessage() . "\n";
    }
} else {
    // Fallback: log
    $log = date('c') . " - PHPMailer no disponible. PDF: $pdfPath. Email simulado a $to\n";
    file_put_contents(__DIR__ . '/../storage/invoices/mail_log.txt', $log, FILE_APPEND);
    echo "PHPMailer no disponible — log generado en storage/invoices/mail_log.txt\n";
}

echo "Simulación completa. Revisa storage/invoices/ para los archivos.\n";