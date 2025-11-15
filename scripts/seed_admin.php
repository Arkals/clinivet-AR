<?php
// Seed admin user and clean problematic products safely
// Usage (PowerShell): php scripts/seed_admin.php

require_once __DIR__ . '/../backend/app/Helpers/DB.php';

use App\Helpers\DB;

$pdo = DB::get();

// Ensure categories exist
$pdo->exec("INSERT IGNORE INTO categories (id,name) VALUES (1,'Gatos'),(2,'Perros')");

// Upsert admin (email unique)
$email = getenv('ADMIN_EMAIL') ?: 'admin@clinivet.local';
$name = getenv('ADMIN_NAME') ?: 'Administrador';
$plain = getenv('ADMIN_PASS') ?: 'admin123';
$hash = password_hash($plain, PASSWORD_BCRYPT);

$stmt = $pdo->prepare("INSERT INTO users (name,email,password,rfc,is_admin) VALUES (?,?,?,?,1)
ON DUPLICATE KEY UPDATE name=VALUES(name), password=VALUES(password), rfc=VALUES(rfc), is_admin=VALUES(is_admin)");
$stmt->execute([$name, $email, $hash, 'AAA010101AAA']);

echo "Admin actualizado: $email\n";

// Remove problematic product by name if any
$pdo->prepare("DELETE FROM products WHERE name IN ('Bolsa de adiestramiento para perros','Bolsa de adiestramiento')")->execute();
echo "Producto 'Bolsa de adiestramiento' eliminado si existía.\n";

// Optional: prune products with non-existing image files
$base = realpath(__DIR__ . '/../frontend/public/');
$query = $pdo->query("SELECT id,image FROM products WHERE image IS NOT NULL AND image <> ''");
$removed = 0;
foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
    $rel = ltrim($row['image'], '/');
    $rel = preg_replace('#^(frontend/public/)+#', '', $rel);
    $rel = preg_replace('#^(public/)+#', '', $rel);
    $path = $base . DIRECTORY_SEPARATOR . $rel;
    if (!is_file($path)) {
        $pdo->prepare('DELETE FROM products WHERE id = ?')->execute([$row['id']]);
        $removed++;
    }
}

if ($removed > 0) {
    echo "Productos sin imagen eliminados: $removed\n";
} else {
    echo "Sin productos huérfanos de imagen.\n";
}
