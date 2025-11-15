<?php include __DIR__ . '/../layouts/main.php'; ?>
<h3>Editar producto</h3>
<?php
$pdo = \App\Helpers\DB::get();
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$product) {
  echo '<div class="alert alert-danger">Producto no encontrado.</div>';
  exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = trim($_POST['name']);
  $desc = trim($_POST['description']);
  $price = floatval($_POST['price']);
  $cat = intval($_POST['category_id']);
  $image = $product['image'];
  if (!empty($_FILES['image']['name'])) {
    $imgName = uniqid('prod_') . '_' . basename($_FILES['image']['name']);
    $dest = __DIR__ . '/../../../public/images/' . $imgName;
    if (move_uploaded_file($_FILES['image']['tmp_name'], $dest)) {
      $image = 'images/' . $imgName;
    }
  }
  $upd = $pdo->prepare("UPDATE products SET name=?, description=?, price=?, category_id=?, image=? WHERE id=?");
  $upd->execute([$name, $desc, $price, $cat, $image, $id]);
  echo '<div class="alert alert-success">Producto actualizado.</div>';
  $product = array_merge($product, ['name'=>$name,'description'=>$desc,'price'=>$price,'category_id'=>$cat,'image'=>$image]);
}
$cats = $pdo->query("SELECT * FROM categories")->fetchAll(PDO::FETCH_ASSOC);
?>
<form method="post" enctype="multipart/form-data">
  <div class="mb-3">
    <label class="form-label">Nombre</label>
    <input class="form-control" name="name" value="<?=htmlspecialchars($product['name'])?>" required>
  </div>
  <div class="mb-3">
    <label class="form-label">Descripción</label>
    <textarea class="form-control" name="description" required><?=htmlspecialchars($product['description'])?></textarea>
  </div>
  <div class="mb-3">
    <label class="form-label">Precio</label>
    <input class="form-control" name="price" type="number" step="0.01" value="<?=htmlspecialchars($product['price'])?>" required>
  </div>
  <div class="mb-3">
    <label class="form-label">Categoría</label>
    <select class="form-select" name="category_id" required>
      <?php foreach($cats as $c): ?>
        <option value="<?=$c['id']?>" <?=$product['category_id']==$c['id']?'selected':''?>><?=htmlspecialchars($c['name'])?></option>
      <?php endforeach; ?>
    </select>
  </div>
  <div class="mb-3">
    <label class="form-label">Imagen actual</label><br>
    <?php if (!empty($product['image'])): ?>
      <img src="/<?=htmlspecialchars($product['image'])?>" style="max-width:120px;max-height:120px;">
    <?php else: ?>
      <span class="text-muted">Sin imagen</span>
    <?php endif; ?>
  </div>
  <div class="mb-3">
    <label class="form-label">Cambiar imagen</label>
    <input class="form-control" type="file" name="image" accept="image/*">
  </div>
  <button class="btn btn-success">Guardar cambios</button>
</form>
<?php $BASE = \App\Helpers\Security::base(); ?>
<a class="btn btn-secondary mt-3" href="<?= $BASE ?>/productos">Volver a productos</a>
</div>
</body>
</html>
