<?php
namespace App\Controllers;
use App\Helpers\DB;
use App\Helpers\Security;
use App\Helpers\PACClient;
class OrderController {
    public function checkoutForm() {
        $cart = $_SESSION['cart'] ?? [];
        $BASE = \App\Helpers\Security::base();
        if (empty($cart)) {
            header('Location: ' . $BASE . '/home');
            exit;
        }
        include __DIR__ . '/../../../frontend/app/Views/orders/checkout.php';
    }

    // Demo fallback: generate files without DB for environments where DB is not configured
    private function placeOrderDemo() {
        $orderId = 'demo_' . time();
        $xmlPath = __DIR__ . '/../../../storage/invoices/order_' . $orderId . '.xml';
        $pdfPath = __DIR__ . '/../../../storage/invoices/order_' . $orderId . '.pdf';
        $dir = dirname($xmlPath);
        if (!is_dir($dir)) mkdir($dir, 0777, true);

        $emisor = [
            'rfc' => 'AAA010101AAA',
            'nombre' => 'Clinivet S.A. de C.V.',
            'regimen' => '601',
            'razon_social' => 'Clinivet S.A. de C.V.'
        ];
        $name = $_POST['name'] ?? 'Cliente Demo';
        $email = $_POST['email'] ?? 'cliente@local';
        $receptor = [
            'rfc' => ($_POST['rfc'] ?? 'XAXX010101000'),
            'nombre' => $name,
            'email' => $email,
            'uso_cfdi' => 'G03'
        ];
        $productos = [ ['name'=>'Demo producto','qty'=>1,'price'=>10.0] ];
        $total = 10.0;
        $serie = 'D';
        $folio = $orderId;
        $uuid = strtoupper(bin2hex(random_bytes(8)));
        $fecha = date('Y-m-d H:i:s');
        $metodo_pago = 'PUE';
        $forma_pago = '04';

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<cfdi:Comprobante Serie="' . $serie . '" Folio="' . $folio . '" Fecha="' . $fecha . '" Total="' . number_format($total,2,'.','') . '" />' . "\n";
        file_put_contents($xmlPath, $xml);

        $pdfData = \generarFacturaPDF($emisor, $receptor, $productos, $total, $uuid, $folio, $fecha, $metodo_pago, $forma_pago);
        file_put_contents($pdfPath, $pdfData);

    // Mostrar la pantalla de éxito después de la compra
        include __DIR__ . '/../../../frontend/app/Views/orders/success.php';
        return;
    }
    public function placeOrder() {
    // Ensure helper files are available from backend/app/Helpers
    $helpersDir = dirname(__DIR__) . '/Helpers';
    $stripePath = $helpersDir . '/stripe_keys.php';
    $facturaPath = $helpersDir . '/FacturaPDF.php';
    if (file_exists($stripePath)) {
        require_once $stripePath;
    }
    if (file_exists($facturaPath)) {
        require_once $facturaPath;
    }
    // If function still not available, try a fallback from project root
    if (!function_exists('generarFacturaPDF')) {
        $alt = dirname(__DIR__, 3) . '/backend/app/Helpers/FacturaPDF.php';
        if (file_exists($alt)) require_once $alt;
    }
    // Try to get DB connection; if it fails, fall back to demo mode to keep checkout functional
    try {
        $pdo = DB::get();
    } catch (\Throwable $e) {
        // Log and use demo flow
        @file_put_contents(__DIR__ . '/../../../storage/invoices/error_log.txt', date('c') . " - DB unavailable: " . $e->getMessage() . "\n", FILE_APPEND);
        return $this->placeOrderDemo();
    }
        
        $cart = $_SESSION['cart'] ?? [];
        $BASE = \App\Helpers\Security::base();
        if (empty($cart)) {
            header('Location: ' . $BASE . '/home');
            exit;
        }
        $name = Security::sanitizeString($_POST['name'] ?? 'Cliente');
        $email = trim($_POST['email'] ?? 'cliente@local');
        
        // Validate email
        if (!Security::validateEmail($email)) {
            $_SESSION['flash_error'] = 'Email inválido';
            header('Location: ' . $BASE . '/checkout');
            exit;
        }
        // Only card payments supported here; bank transfer was removed
        $paymentMethod = 'card';
        $stripeToken = $_POST['stripeToken'] ?? null;
        // OMITIR pago real: simular éxito aunque no haya token
        // if (!$stripeToken) {
        //     $_SESSION['flash_error'] = 'No se recibió el token de pago. Por favor revisa los datos de tarjeta o configura STRIPE_PUBLISHABLE_KEY.';
        //     header('Location: index.php?page=checkout');
        //     exit;
        // }
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        $user = $stmt->fetch(\PDO::FETCH_ASSOC);
        if (!$user) {
            $insu = $pdo->prepare("INSERT INTO users (name, email, password, rfc) VALUES (?,?,?,?)");
            $insu->execute([$name, $email, 'guest', 'XAXX010101000']);
            $userId = $pdo->lastInsertId();
        } else {
            $userId = $user['id'];
        }
        $pdo->beginTransaction();
        try {
            $total = 0;
            $ids = array_keys($cart);
            $ids = array_map('intval', $ids);
            $in = implode(',', array_fill(0, count($ids), '?'));
            $stmt = $pdo->prepare("SELECT * FROM products WHERE id IN ($in)");
            $stmt->execute($ids);
            $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            foreach ($rows as $r) {
                $qty = $cart[$r['id']];
                $total += $qty * $r['price'];
            }
            // Attempt to charge via Stripe when configured
            $pagoExitoso = false;
            $orderStatus = 'paid'; // match DB ENUM('pending','paid','shipped','cancelled')
            $chargeId = null;
            // OMITIR pago real: simular éxito siempre
            $pagoExitoso = true;
            if (!$pagoExitoso) {
                $_SESSION['flash_error'] = 'El pago no fue exitoso. Revisa los datos de tu tarjeta.';
                header('Location: ' . $BASE . '/checkout');
                exit;
            }
            $stmt = $pdo->prepare("INSERT INTO orders (user_id, total, status) VALUES (?,?,?)");
            $stmt->execute([$userId, $total, $orderStatus]);
            $orderId = $pdo->lastInsertId();
            $ins = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, unit_price) VALUES (?,?,?,?)");
            foreach ($rows as $r) {
                $qty = $cart[$r['id']];
                $ins->execute([$orderId, $r['id'], $qty, $r['price']]);
            }
            $xmlPath = __DIR__ . '/../../../storage/invoices/order_' . $orderId . '.xml';
            $pdfPath = __DIR__ . '/../../../storage/invoices/order_' . $orderId . '.pdf';
            $dir = dirname($xmlPath);
            if (!is_dir($dir)) mkdir($dir, 0777, true);
            $emisor = [
                'rfc' => 'AAA010101AAA',
                'nombre' => 'Clinivet S.A. de C.V.',
                'regimen' => '601',
                'razon_social' => 'Clinivet S.A. de C.V.'
            ];
            $receptor = [
                'rfc' => ($_POST['rfc'] ?? 'XAXX010101000'),
                'nombre' => $name,
                'email' => $email,
                'uso_cfdi' => 'G03'
            ];
            $serie = 'A';
            $folio = $orderId;
            $uuid = strtoupper(bin2hex(random_bytes(16)));
            $fecha = date('Y-m-d\TH:i:s');
            $metodo_pago = 'PUE';
            $forma_pago = '04';
            $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
            $xml .= '<cfdi:Comprobante xmlns:cfdi="http://www.sat.gob.mx/cfd/4" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sat.gob.mx/cfd/4 http://www.sat.gob.mx/sitio_internet/cfd/4/cfdv40.xsd" Serie="' . $serie . '" Folio="' . $folio . '" Fecha="' . $fecha . '" Total="' . number_format($total,2,'.','') . '" Moneda="MXN" MetodoPago="' . $metodo_pago . '" FormaPago="' . $forma_pago . '" TipoDeComprobante="I" LugarExpedicion="64000">' . "\n";
            $xml .= '  <cfdi:Emisor Rfc="' . $emisor['rfc'] . '" Nombre="' . $emisor['razon_social'] . '" RegimenFiscal="' . $emisor['regimen'] . '" />' . "\n";
            $xml .= '  <cfdi:Receptor Rfc="' . $receptor['rfc'] . '" Nombre="' . htmlspecialchars($receptor['nombre']) . '" UsoCFDI="' . $receptor['uso_cfdi'] . '" />' . "\n";
            $xml .= '  <cfdi:Conceptos>' . "\n";
            foreach ($rows as $r) {
                $qty = $cart[$r['id']];
                $xml .= '    <cfdi:Concepto ClaveProdServ="01010101" Cantidad="' . $qty . '" ClaveUnidad="H87" Unidad="Pieza" Descripcion="' . htmlspecialchars($r['name']) . '" ValorUnitario="' . number_format($r['price'],2,'.','') . '" Importe="' . number_format($qty * $r['price'],2,'.','') . '" />' . "\n";
            }
            $xml .= '  </cfdi:Conceptos>' . "\n";
            $xml .= '  <cfdi:Complemento>' . "\n";
            $xml .= '    <tfd:TimbreFiscalDigital xmlns:tfd="http://www.sat.gob.mx/TimbreFiscalDigital" Version="1.1" UUID="' . $uuid . '" FechaTimbrado="' . $fecha . '" RfcProvCertif="AAA010101AAA" SelloCFD="SELLOFAKE" NoCertificadoSAT="00001000000403258748" />' . "\n";
            $xml .= '  </cfdi:Complemento>' . "\n";
            $xml .= '</cfdi:Comprobante>' . "\n";
            file_put_contents($xmlPath, $xml);
            $productos = [];
            foreach ($rows as $r) {
                $productos[] = [
                    'name' => $r['name'],
                    'qty' => $cart[$r['id']],
                    'price' => $r['price']
                ];
            }
            $pdfData = \generarFacturaPDF($emisor, $receptor, $productos, $total, $uuid, $folio, $fecha, $metodo_pago, $forma_pago);
            file_put_contents($pdfPath, $pdfData);
            $pdo->prepare("INSERT INTO invoices (order_id, uuid, xml_path, pdf_path, timbre_status) VALUES (?,?,?,?,?)")
                ->execute([$orderId, $uuid, $xmlPath, $pdfPath, 'timbrado']);
            // Try to save stripe charge id if column exists
            if (!empty($chargeId)) {
                try {
                    $cols = $pdo->query("SHOW COLUMNS FROM invoices LIKE 'stripe_charge_id'")->fetchAll(\PDO::FETCH_ASSOC);
                    if (count($cols) > 0) {
                        // find the invoice we just inserted
                        $invId = $pdo->lastInsertId();
                        $pdo->prepare("UPDATE invoices SET stripe_charge_id = ? WHERE id = ?")->execute([$chargeId, $invId]);
                    }
                } catch (\Exception $e) { /* ignore if not possible */ }
            }
            $pdo->commit();
            unset($_SESSION['cart']);

            // Cargar autoload de Composer para usar PHPMailer y otras librerías
            if (!class_exists('PHPMailer\\PHPMailer\\PHPMailer') && file_exists(__DIR__ . '/../../../vendor/autoload.php')) {
                require_once __DIR__ . '/../../../vendor/autoload.php';
            }

            if (class_exists('PHPMailer\\PHPMailer\\PHPMailer')) {
                $mailClass = 'PHPMailer\\PHPMailer\\PHPMailer';
                $mail = new $mailClass(true);
                try {
                    // Ensure proper encoding for Spanish accents
                    $mail->CharSet = 'UTF-8';
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com'; 
                    $mail->SMTPAuth = true;
                    $mail->Username = getenv('SMTP_USER') ?: 'puntodeventaumb@gmail.com'; 
                    $mail->Password = getenv('SMTP_PASS') ?: 'gficrgeogbibtaae'; 
                    $mail->SMTPSecure = 'tls';
                    $mail->Port = 587;
                    $mail->setFrom('puntodeventaumb@gmail.com', 'Clinivet');
                    $mail->addAddress($email, $name);
                    $mail->Subject = 'Factura electr\u00f3nica de tu compra en MiTienda';
                    $mail->isHTML(true);
                    $mail->Body = '<h2 style="color:#388e3c;">¡Gracias por tu compra!</h2>' .
                        '<p>Adjuntamos tu factura electr\u00f3nica en PDF. <br>El XML timbrado est\u00e1 disponible para descarga en tu perfil.</p>' .
                        '<ul>' .
                        '<li><b>Emisor:</b> ' . $emisor['razon_social'] . ' (' . $emisor['rfc'] . ')</li>' .
                        '<li><b>Receptor:</b> ' . htmlspecialchars($receptor['nombre']) . ' (' . $receptor['rfc'] . ')</li>' .
                        '<li><b>Uso CFDI:</b> ' . $receptor['uso_cfdi'] . '</li>' .
                        '<li><b>Serie/Folio:</b> ' . $serie . '-' . $folio . '</li>' .
                        '<li><b>UUID:</b> ' . $uuid . '</li>' .
                        '<li><b>Fecha:</b> ' . $fecha . '</li>' .
                        '<li><b>M\u00e9todo de pago:</b> Tarjeta (PUE)</li>' .
                        '</ul>' .
                        '<p style="color:#888;">Cualquier duda, cont\u00e1ctanos.</p>';
                    $mail->addAttachment($pdfPath, 'factura_' . $folio . '.pdf');
                    if (file_exists($xmlPath)) {
                        $mail->addAttachment($xmlPath, 'factura_' . $folio . '.xml');
                    }
                    $mail->send();
                } catch (\Exception $e) {
                    // Log mail failure for troubleshooting, but don't block the flow
                    $log = date('c') . " - PHPMailer error sending to $email: " . $e->getMessage() . "\n";
                    @file_put_contents(__DIR__ . '/../../../storage/invoices/mail_log.txt', $log, FILE_APPEND);
                }
            } else {
            // Si PHPMailer no está disponible, intentar enviar el correo con mail() (sin adjuntos)
                    $subject = 'Factura electronica de tu compra en Clinivet';
                    $message = "Gracias por tu compra!\n\n";
                    $message .= "Adjuntamos tu factura electr\u00f3nica en PDF (si tu servidor soporta adjuntos por mail()).\n";
                    $message .= "UUID: $uuid\nSerie/Folio: $serie-$folio\nTotal: $total\n";
                    $headers = "From: Clinivet <" . (getenv('SMTP_USER') ?: 'puntodeventaumb@gmail.com') . ">\r\n" .
                               "MIME-Version: 1.0\r\n" .
                               "Content-Type: text/plain; charset=UTF-8\r\n";
                    $mailSent = false;
                    if (function_exists('mail')) {
                        $mailSent = @mail($email, $subject, $message, $headers);
                    }
                    $log = date('c') . " - PHPMailer no disponible. mail() used: " . ($mailSent ? 'OK' : 'FAILED') . ". Sent to $email.\n";
                    @file_put_contents(__DIR__ . '/../../../storage/invoices/mail_log.txt', $log, FILE_APPEND);
            }

            include __DIR__ . '/../../../frontend/app/Views/orders/success.php';
        } catch (\Exception $e) {
            $pdo->rollBack();
            $_SESSION['flash_error'] = 'Ocurrió un error procesando la orden: ' . $e->getMessage();
            header('Location: ' . $BASE . '/checkout');
            exit;
        }
    }

    // Panel de administración: listar órdenes y permitir marcarlas como pagadas/completadas
    public function adminOrders() {
        $BASE = \App\Helpers\Security::base();
        if (empty($_SESSION['user']) || empty($_SESSION['user']['is_admin'])) { header('Location: ' . $BASE . '/home'); exit; }
        $pdo = DB::get();
    // Procesar acción del formulario de administración de órdenes
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'mark_paid' && !empty($_POST['order_id'])) {
            $oid = intval($_POST['order_id']);
            $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?")->execute(['paid', $oid]);
        }
        $stmt = $pdo->query("SELECT o.*, u.email as user_email FROM orders o LEFT JOIN users u ON o.user_id = u.id ORDER BY o.id DESC");
        $orders = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        include __DIR__ . '/../../../frontend/app/Views/admin/orders.php';
    }
}
