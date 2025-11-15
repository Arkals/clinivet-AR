<?php
namespace App\Controllers;
use App\Helpers\DB;
use App\Helpers\Security;

class AuthController {
    private function mapPageToSlug($page) {
        $map = [
            'home' => 'home',
            'products' => 'productos',
            'cart' => 'carrito',
            'login' => 'iniciar-sesion',
            'register' => 'registro',
            'logout' => 'logout',
            'invoices' => 'facturas',
            'admin_products' => 'admin/productos',
            'admin_create_product' => 'admin/productos/nuevo',
            'checkout' => 'checkout',
            'download_invoice' => 'descargar-factura',
            'cart_clear' => 'cart-vaciar'
        ];
        return $map[$page] ?? 'home';
    }
    public function register() {
        $error = null;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Rate limiting
            if (!Security::checkRateLimit('register', 3, 600)) {
                $error = "Demasiados intentos. Intenta de nuevo en 10 minutos.";
            } else {
                $name = Security::sanitizeString($_POST['name'] ?? '');
                $email = trim($_POST['email'] ?? '');
                $pass = $_POST['password'] ?? '';
                $rfc = trim($_POST['rfc'] ?? '');
                
                // Validations
                if (!$name || !$email || !$pass) {
                    $error = "Todos los campos obligatorios deben llenarse.";
                } elseif (!Security::validateEmail($email)) {
                    $error = "Email inválido.";
                } elseif (strlen($pass) < 6) {
                    $error = "La contraseña debe tener al menos 6 caracteres.";
                } elseif ($rfc && !Security::validateRFC($rfc)) {
                    $error = "RFC inválido.";
                } else {
                $pdo = DB::get();
                $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
                $stmt->execute([$email]);
                if ($stmt->fetch()) {
                    $error = "El correo ya está registrado.";
                } else {
                    $hash = password_hash($pass, PASSWORD_BCRYPT);
                    $ins = $pdo->prepare("INSERT INTO users (name, email, password, rfc) VALUES (?,?,?,?)");
                    $ins->execute([$name, $email, $hash, $rfc ?: 'XAXX010101000']);
                    
                    // Regenerate session to prevent fixation
                    Security::regenerateSession();
                    
                    $_SESSION['user'] = [
                        'id' => $pdo->lastInsertId(),
                        'name' => $name,
                        'email' => $email,
                        'is_admin' => 0
                    ];
                    $redirect = $_GET['redirect'] ?? 'home';
                    $slug = $this->mapPageToSlug($redirect);
                    $BASE = \App\Helpers\Security::base();
                    header('Location: ' . $BASE . '/' . $slug);
                    exit;
                }
                }
            }
        }
        include __DIR__ . '/../../../frontend/app/Views/auth/register.php';
    }
    
    public function login() {
        $error = null;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Rate limiting: max 5 attempts per 5 minutes
            if (!Security::checkRateLimit('login', 5, 300)) {
                $error = "Demasiados intentos de inicio de sesión. Intenta en 5 minutos.";
            } else {
                $email = trim($_POST['email'] ?? '');
                $pass = $_POST['password'] ?? '';
                
                if (!Security::validateEmail($email)) {
                    $error = "Email inválido";
                } else {
                    $pdo = DB::get();
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
            $stmt->execute([$email]);
            $user = $stmt->fetch(\PDO::FETCH_ASSOC);
            // Verifica contraseña con soporte a registros antiguos en texto plano
            $isValid = false;
            if ($user) {
                $stored = (string)$user['password'];
                if (strlen($stored) >= 60 && substr($stored, 0, 4) === '$2y$') {
                    $isValid = password_verify($pass, $stored);
                } else {
                    // Soporte legado: texto plano
                    $isValid = hash_equals($stored, $pass);
                    // Si coincide en texto plano, migrar a hash seguro
                    if ($isValid) {
                        try {
                            $newHash = password_hash($pass, PASSWORD_BCRYPT);
                            $upd = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
                            $upd->execute([$newHash, $user['id']]);
                            $user['password'] = $newHash;
                        } catch (\Throwable $t) {
                            // Silencioso: no bloquear login por fallo de migración
                        }
                    }
                }
            }
            if ($user && $isValid) {
                // Regenerate session on successful login
                Security::regenerateSession();
                
                $_SESSION['user'] = [
                    'id' => $user['id'],
                    'name' => $user['name'],
                    'email' => $user['email'],
                    'is_admin' => $user['is_admin']
                ];
                $redirect = $_GET['redirect'] ?? 'home';
                $slug = $this->mapPageToSlug($redirect);
                $BASE = \App\Helpers\Security::base();
                header('Location: ' . $BASE . '/' . $slug);
                exit;
            } else {
                $error = "Usuario o contraseña incorrectos";
            }
                }
            }
        }
        include __DIR__ . '/../../../frontend/app/Views/auth/login.php';
    }

    public function logout() {
        unset($_SESSION['user']);
        Security::regenerateSession();
        $BASE = \App\Helpers\Security::base();
        header('Location: ' . $BASE . '/home');
        exit;
    }
}
