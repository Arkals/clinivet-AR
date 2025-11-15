<?php include __DIR__ . '/../../Views/layouts/main.php'; ?>
<?php if (empty($_SESSION['user']) || empty($_SESSION['user']['is_admin'])) { $BASE = \App\Helpers\Security::base(); header('Location: ' . $BASE . '/home'); exit; } ?>
<div class="row justify-content-center">
  <div class="col-md-8">
    <h3>Crear producto</h3>
    <?php if (!empty($msg)): ?><div class="alert alert-success"><?=htmlspecialchars($msg)?></div><?php endif; ?>
    <form method="post" enctype="multipart/form-data">
      <div class="mb-3">
        <label class="form-label">Nombre</label>
        <input class="form-control" name="name" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Descripción</label>
        <textarea class="form-control" name="description"></textarea>
      </div>
      <div class="mb-3">
        <label class="form-label">Precio</label>
        <input class="form-control" name="price" type="number" step="0.01" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Stock</label>
        <input class="form-control" name="stock" type="number" value="0">
      </div>
      <div class="mb-3">
        <label class="form-label">Categoría</label>
        <select name="category_id" class="form-select">
          <?php foreach($cats as $c): ?>
            <option value="<?=intval($c['id'])?>"><?=htmlspecialchars($c['name'])?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="mb-3">
        <label class="form-label">Imagen</label>
        <input type="file" name="image" class="form-control">
      </div>
      <button class="btn btn-success">Crear</button>
    </form>
    <?php $BASE = \App\Helpers\Security::base(); ?>
    <a class="btn btn-secondary mt-3" href="<?= $BASE ?>/admin/productos">Volver</a>
  </div>
</div>
</body>
</html>
