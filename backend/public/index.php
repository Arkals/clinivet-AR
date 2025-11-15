<?php
// Backend entry: router for admin/API actions and controllers
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../app/Helpers/DB.php';
require_once __DIR__ . '/../app/Helpers/Security.php';
require_once __DIR__ . '/../app/Helpers/PACClient.php';
require_once __DIR__ . '/../app/Controllers/ProductController.php';
require_once __DIR__ . '/../app/Controllers/OrderController.php';
require_once __DIR__ . '/../app/Controllers/AuthController.php';
use App\Controllers\ProductController;
use App\Controllers\OrderController;
use App\Controllers\AuthController;
use App\Helpers\Security;
// Apply secure cookie params before starting the session
Security::applySessionCookieParams();
session_start();

$BASE = \App\Helpers\Security::base();

// Pretty route support (if ?route= provided by root .htaccess). Sanitize and map to legacy page.
if (isset($_GET['route'])) {
    $rawRoute = $_GET['route'];
    // If multiple route params given (?route=a&route=b) PHP keeps last; extra noise ignored.
    if (is_array($rawRoute)) {
        $rawRoute = end($rawRoute);
    }
    $route = trim($rawRoute, '/');
    // Defensive: ignore any .php or empty artifacts
    if ($route === '' || preg_match('/\.php$/i', $route)) {
        $route = 'home';
    }
    // Block traversal / dangerous chars
    if (preg_match('/[\.]{2,}|[<>"\']|\x00/', $route)) {
        $route = 'home';
    }
    $seg = explode('/', strtolower($route));
    if (!empty($seg[0])) {
        switch ($seg[0]) {
            case 'home':
            case 'inicio':
                $_GET['page'] = 'home'; break;
            case 'productos':
            case 'products':
            case 'tienda':
                $_GET['page'] = 'products'; break;
            case 'categoria':
            case 'category':
                if (!empty($seg[1]) && ctype_digit($seg[1])) { $_GET['page'] = 'category'; $_GET['id'] = intval($seg[1]); }
                else { $_GET['page'] = 'home'; }
                break;
            case 'carrito':
            case 'cart':
                $_GET['page'] = 'cart'; break;
            case 'cart-vaciar':
            case 'vaciar-carrito':
                $_GET['page'] = 'cart_clear'; break;
            case 'iniciar-sesion':
            case 'login':
                $_GET['page'] = 'login'; break;
            case 'registro':
            case 'register':
                $_GET['page'] = 'register'; break;
            case 'logout':
            case 'salir':
                $_GET['page'] = 'logout'; break;
            case 'facturas':
            case 'invoices':
                $_GET['page'] = 'invoices'; break;
            case 'agregar-carrito':
                $_GET['page'] = 'add_to_cart'; break;
            case 'actualizar-carrito':
                $_GET['page'] = 'update_cart'; break;
            case 'checkout':
            case 'pago':
                $_GET['page'] = 'checkout'; break;
            case 'descargar-factura':
                $_GET['page'] = 'download_invoice'; break;
            case 'admin':
                if (!empty($seg[1]) && $seg[1] === 'productos' && empty($seg[2])) { $_GET['page'] = 'admin_products'; }
                elseif (!empty($seg[1]) && $seg[1] === 'productos' && !empty($seg[2]) && $seg[2] === 'nuevo') { $_GET['page'] = 'admin_create_product'; }
                elseif (!empty($seg[1]) && in_array($seg[1], ['pedidos','orders'])) { $_GET['page'] = 'admin_orders'; }
                else { $_GET['page'] = 'home'; }
                break;
            case 'cookies-consent':
            case 'cookies':
                $_GET['page'] = 'cookies_consent';
                break;
            default:
                $_GET['page'] = 'home';
        }
    } else {
        $_GET['page'] = 'home';
    }
}

// Apply security headers
Security::setSecurityHeaders();

$page = $_GET['page'] ?? 'home';
$id = isset($_GET['id']) ? intval($_GET['id']) : null;
$q = isset($_GET['q']) ? trim(strip_tags($_GET['q'])) : null;
$cat = isset($_GET['cat']) && ctype_digit($_GET['cat']) ? intval($_GET['cat']) : null;
// Validate $id is positive integer when set
if ($id !== null && $id <= 0) {
    http_response_code(400);
    die('Invalid ID');
}
$pc = new ProductController();
$oc = new OrderController();
$ac = new AuthController();

// Keep same routing as before for backend actions
if ($page === 'register') {
    $ac->register();
} elseif ($page === 'login') {
    $ac->login();
} elseif ($page === 'logout') {
    $ac->logout();
} elseif ($page === 'invoices') {
    // Prefer frontend view path when backend/public is the entrypoint
    $invViewBackend = dirname(__DIR__) . '/app/Views/auth/invoices.php';
    $invViewFrontend = dirname(__DIR__, 2) . '/frontend/app/Views/auth/invoices.php';
    $invView = file_exists($invViewFrontend) ? $invViewFrontend : $invViewBackend;
    if (file_exists($invView)) {
        include $invView;
    } else {
        error_log("Invoices view not found: $invViewFrontend | $invViewBackend");
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
} elseif ($page === 'cart_clear') {
    $pc->clearCart();
} elseif ($page === 'edit_product' && $id) {
    include __DIR__ . '/../../app/Views/products/edit.php';
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
} elseif ($page === 'admin_products') {
    $pc->adminProducts();
} elseif ($page === 'admin_create_product') {
    $pc->adminCreateProduct();
} elseif ($page === 'admin_orders') {
    $oc->adminOrders();
} elseif ($page === 'cookies_consent') {
    header('Content-Type: application/json');
    $choice = $_POST['choice'] ?? $_GET['choice'] ?? 'accept';
    $val = ($choice === 'reject') ? 'reject' : 'accept';
    \App\Helpers\Security::setSecureCookie('cookie_consent', $val);
    echo json_encode(['ok' => true, 'choice' => $val]);
    exit;
} else {
    $pc->home();
}
// Download invoice file (pdf or xml) - checks ownership
if ($page === 'download_invoice') {
    if (empty($_SESSION['user'])) { header('HTTP/1.1 403 Forbidden'); exit; }
    $type = $_GET['type'] ?? 'pdf';
    $file = $_GET['f'] ?? '';
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
}

