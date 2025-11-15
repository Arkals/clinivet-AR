<?php
namespace App\Controllers;
use App\Helpers\DB;
use App\Helpers\Security;
class ProductController {
    public function home() {
        $pdo = DB::get();
        $cats = $pdo->query("SELECT * FROM categories")->fetchAll(\PDO::FETCH_ASSOC);
        $stmt = $pdo->query("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id ORDER BY p.id LIMIT 8");
        $products = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        // Filter out products without a visible image
        $products = $this->filterProductsWithVisibleImage($products);
        $BASE = \App\Helpers\Security::base();
        include __DIR__ . '/../../../frontend/app/Views/home.php';
    }
    public function list() {
        $pdo = DB::get();
        $stmt = $pdo->query("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id");
        $products = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        // Filter out products without a visible image
        $products = $this->filterProductsWithVisibleImage($products);
        $cats = $pdo->query("SELECT * FROM categories")->fetchAll(\PDO::FETCH_ASSOC);
        include __DIR__ . '/../../../frontend/app/Views/products/list.php';
    }
    public function listByCategory($categoryId) {
        $pdo = DB::get();
        $stmt = $pdo->prepare("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.category_id = ?");
        $stmt->execute([$categoryId]);
        $products = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        // Filter out products without a visible image
        $products = $this->filterProductsWithVisibleImage($products);
        $cats = $pdo->query("SELECT * FROM categories")->fetchAll(\PDO::FETCH_ASSOC);
        include __DIR__ . '/../../../frontend/app/Views/products/list.php';
    }
    public function search($term, $categoryId = null) {
        $pdo = DB::get();
        $sql = "SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE (p.name LIKE ? OR p.description LIKE ?)";
        $params = ["%$term%","%$term%"];
        if ($categoryId) {
            $sql .= " AND p.category_id = ?";
            $params[] = $categoryId;
        }
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $products = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        // Filter out products without a visible image
        $products = $this->filterProductsWithVisibleImage($products);
        $cats = $pdo->query("SELECT * FROM categories")->fetchAll(\PDO::FETCH_ASSOC);
        include __DIR__ . '/../../../frontend/app/Views/products/list.php';
    }
    public function addToCart($id) {
        $pdo = DB::get();
        $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$id]);
        $product = $stmt->fetch(\PDO::FETCH_ASSOC);
        if (!$product) {
            if ($this->isAjax()) {
                http_response_code(404);
                echo json_encode(['ok'=>false, 'msg'=>'Producto no encontrado']);
                exit;
            }
            $BASE = \App\Helpers\Security::base();
            header('Location: ' . $BASE . '/home');
            exit;
        }
        if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
        if (!isset($_SESSION['cart'][(int)$id])) $_SESSION['cart'][(int)$id] = 0;
        $_SESSION['cart'][(int)$id]++;
        if ($this->isAjax()) {
            echo json_encode([
                'ok'=>true,
                'cart_count'=>array_sum($_SESSION['cart']),
                'product_id'=>$id,
                'qty'=>$_SESSION['cart'][(int)$id]
            ]);
            exit;
        }
        $BASE = \App\Helpers\Security::base();
        $redirect = $_SERVER['HTTP_REFERER'] ?? ($BASE . '/home');
        header('Location: ' . $redirect);
        exit;
    }

    public function updateCart($id, $action) {
        if (!isset($_SESSION['cart'][(int)$id])) {
            echo json_encode(['ok'=>false, 'msg'=>'No existe']);
            exit;
        }
        if ($action === 'inc') {
            $_SESSION['cart'][(int)$id]++;
        } elseif ($action === 'dec') {
            $_SESSION['cart'][(int)$id]--;
            if ($_SESSION['cart'][(int)$id] < 1) unset($_SESSION['cart'][(int)$id]);
        } elseif ($action === 'del') {
            unset($_SESSION['cart'][(int)$id]);
        }
        echo json_encode([
            'ok'=>true,
            'cart_count'=>array_sum($_SESSION['cart']),
            'product_id'=>$id,
            'qty'=>$_SESSION['cart'][(int)$id] ?? 0
        ]);
        exit;
    }

    private function isAjax() {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest';
    }

    private function filterProductsWithVisibleImage(array $rows): array {
        $baseDir = __DIR__ . '/../../../frontend/public/';
        $filtered = [];
        foreach ($rows as $r) {
            $img = $r['image'] ?? '';
            if (!$img) {
                continue;
            }
            $img = ltrim($img, '/');
            // Normalize any stored prefixes
            $img = preg_replace('#^(frontend/public/)+#', '', $img);
            $img = preg_replace('#^(public/)+#', '', $img);
            $disk = $baseDir . $img;
            if (is_file($disk) && file_exists($disk)) {
                $filtered[] = $r;
            }
        }
        return $filtered;
    }

    public function cart() {
        $cart = $_SESSION['cart'] ?? [];
        $items = [];
        if ($cart && count($cart) > 0) {
            $pdo = DB::get();
            $ids = array_keys($cart);
            $ids = array_filter($ids, 'is_numeric');
            if (count($ids) > 0) {
                $ids = array_map('intval', $ids);
                $in = implode(',', array_fill(0, count($ids), '?'));
                $stmt = $pdo->prepare("SELECT * FROM products WHERE id IN ($in)");
                $stmt->execute($ids);
                $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
                foreach ($rows as $r) {
                    $r['quantity'] = $cart[$r['id']];
                    $items[] = $r;
                }
            }
        }
        include __DIR__ . '/../../../frontend/app/Views/products/cart.php';
    }

    public function clearCart() {
        unset($_SESSION['cart']);
        $BASE = \App\Helpers\Security::base();
        header('Location: ' . $BASE . '/carrito');
        exit;
    }

    // Admin: list products
    public function adminProducts() {
        if (empty($_SESSION['user']) || empty($_SESSION['user']['is_admin'])) {
            $BASE = \App\Helpers\Security::base();
            header('Location: ' . $BASE . '/home'); exit;
        }
        $pdo = DB::get();
        $stmt = $pdo->query("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id ORDER BY p.id DESC");
        $products = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        include __DIR__ . '/../../../frontend/app/Views/admin/products.php';
    }

    // Admin: create product
    public function adminCreateProduct() {
        if (empty($_SESSION['user']) || empty($_SESSION['user']['is_admin'])) {
            $BASE = \App\Helpers\Security::base();
            header('Location: ' . $BASE . '/home'); exit;
        }
        $pdo = DB::get();
        $cats = $pdo->query("SELECT * FROM categories")->fetchAll(\PDO::FETCH_ASSOC);
        $msg = null;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = Security::sanitizeString($_POST['name'] ?? '');
            $desc = Security::sanitizeString($_POST['description'] ?? '');
            $price = floatval($_POST['price'] ?? 0);
            $stock = intval($_POST['stock'] ?? 0);
            $cat = intval($_POST['category_id'] ?? 0);
            
            // Validations
            if (empty($name)) {
                $msg = 'El nombre del producto es obligatorio.';
            } elseif ($price <= 0) {
                $msg = 'El precio debe ser mayor a 0.';
            } elseif ($stock < 0) {
                $msg = 'El stock no puede ser negativo.';
            } else {
                $imagePath = null;
                if (!empty($_FILES['image']['name'])) {
                    // Validate image
                    $validation = Security::validateImageUpload($_FILES['image']);
                    if (!$validation['valid']) {
                        $msg = $validation['error'];
                    } else {
                        $safeName = Security::sanitizeFilename($_FILES['image']['name']);
                        $imgName = uniqid('prod_') . '_' . $safeName;
                        $destDir = __DIR__ . '/../../../frontend/public/uploads/products/';
                        if (!is_dir($destDir)) mkdir($destDir, 0777, true);
                        $dest = $destDir . $imgName;
                        if (move_uploaded_file($_FILES['image']['tmp_name'], $dest)) {
                            $imagePath = 'uploads/products/' . $imgName;
                        }
                    }
                }
                
                if (!isset($msg)) {
                    $ins = $pdo->prepare("INSERT INTO products (category_id, name, description, price, stock, image) VALUES (?,?,?,?,?,?)");
                    $ins->execute([$cat, $name, $desc, $price, $stock, $imagePath]);
                    $msg = 'Producto creado correctamente.';
                }
            }
        }
        include __DIR__ . '/../../../frontend/app/Views/admin/create_product.php';
    }
}
