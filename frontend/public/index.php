<?php
// Frontend public entry: serves the public site and loads backend controllers via autoload
require_once __DIR__ . '/../../vendor/autoload.php';
// Load backend app code
require_once __DIR__ . '/../../backend/app/Helpers/DB.php';
require_once __DIR__ . '/../../backend/app/Helpers/PACClient.php';
require_once __DIR__ . '/../../backend/app/Controllers/ProductController.php';
require_once __DIR__ . '/../../backend/app/Controllers/OrderController.php';
require_once __DIR__ . '/../../backend/app/Controllers/AuthController.php';
use App\Controllers\ProductController;
use App\Controllers\OrderController;
use App\Controllers\AuthController;
session_start();

$BASE = \App\Helpers\Security::base();
$page = $_GET['page'] ?? 'home';
$id = isset($_GET['id']) ? intval($_GET['id']) : null;
$q = $_GET['q'] ?? null;
$cat = $_GET['cat'] ?? null;
$pc = new ProductController();
$oc = new OrderController();
$ac = new AuthController();

// Front-facing routes reuse the same controllers and view files
if ($page === 'register') {
    $ac->register();
} elseif ($page === 'login') {
    $ac->login();
} elseif ($page === 'logout') {
    $ac->logout();
} elseif ($page === 'invoices') {
    // include invoices view (use dirname to avoid relative path issues)
    $invView = dirname(__DIR__) . '/app/Views/auth/invoices.php';
    if (file_exists($invView)) {
        include $invView;
    } else {
        error_log("Invoices view not found: $invView");
        header('HTTP/1.1 500 Internal Server Error');
        echo '<h3>Vista de facturas no encontrada.</h3>';
        exit;
    }
} elseif ($page === 'home') {
    $pc->home();
} elseif ($page === 'category' && $id) {
    $pc->listByCategory($id);
} elseif ($page === 'products') {
    if ($q) $pc->search($q, $cat ?: null);
    else $pc->list();
} elseif ($page === 'add_to_cart' && $id) {
    $pc->addToCart($id);
} elseif ($page === 'update_cart' && $id && isset($_GET['action'])) {
    $pc->updateCart($id, $_GET['action']);
} elseif ($page === 'cart') {
    $pc->cart();
} elseif ($page === 'edit_product' && $id) {
    include __DIR__ . '/../../app/Views/products/edit.php';
} elseif ($page === 'admin_products') {
    $pc->adminProducts();
} elseif ($page === 'admin_create_product') {
    $pc->adminCreateProduct();
} elseif ($page === 'admin_orders') {
    $oc->adminOrders();
} elseif ($page === 'download_invoice') {
    // Download invoice file (pdf or xml) - checks ownership
    if (empty($_SESSION['user'])) { header('HTTP/1.1 403 Forbidden'); exit; }
    $type = $_GET['type'] ?? 'pdf';
    $oid = intval($_GET['oid'] ?? 0);
    $pdo = \App\Helpers\DB::get();
    $stmt = $pdo->prepare("SELECT o.user_id, i.pdf_path, i.xml_path FROM invoices i LEFT JOIN orders o ON i.order_id = o.id WHERE o.id = ? LIMIT 1");
    $stmt->execute([$oid]);
    $row = $stmt->fetch(\PDO::FETCH_ASSOC);
    if (!$row) { header('HTTP/1.1 404 Not Found'); exit; }
    if ($row['user_id'] != $_SESSION['user']['id'] && empty($_SESSION['user']['is_admin'])) { header('HTTP/1.1 403 Forbidden'); exit; }
    $path = ($type === 'xml') ? $row['xml_path'] : $row['pdf_path'];
    if (!file_exists($path)) { header('HTTP/1.1 404 Not Found'); exit; }
    $basename = basename($path);
    header('Content-Description: File Transfer');
    header('Content-Type: ' . (($type==='xml') ? 'application/xml' : 'application/pdf'));
    header('Content-Disposition: attachment; filename="' . $basename . '"');
    header('Content-Length: ' . filesize($path));
    readfile($path);
    exit;
} elseif ($page === 'checkout') {
    if (empty($_SESSION['user'])) {
        header('Location: ' . $BASE . '/iniciar-sesion?redirect=checkout');
        exit;
    }
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $oc->placeOrder();
    } else {
        $oc->checkoutForm();
    }
} else {
    $pc->home();
}
