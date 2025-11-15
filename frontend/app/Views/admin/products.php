<?php include __DIR__ . '/../../Views/layouts/main.php'; ?>
<?php if (empty($_SESSION['user']) || empty($_SESSION['user']['is_admin'])) { $BASE = \App\Helpers\Security::base(); header('Location: ' . $BASE . '/home'); exit; } ?>
<div class="row">
  <div class="col-md-10 offset-md-1">
    <h3>Panel de administración - Productos</h3>
    <?php $BASE = \App\Helpers\Security::base(); ?>
    <a class="btn btn-success mb-3" href="<?= $BASE ?>/admin/productos/nuevo">Nuevo producto</a>
    <table class="table">
      <thead><tr><th>ID</th><th>Nombre</th><th>Categoría</th><th>Precio</th><th>Stock</th></tr></thead>
      <tbody>
        <?php foreach($products as $p): ?>
          <tr>
            <td><?=htmlspecialchars($p['id'])?></td>
            <td><?=htmlspecialchars($p['name'])?></td>
            <td><?=htmlspecialchars($p['category_name'] ?? '')?></td>
            <td>$<?=number_format($p['price'],2)?></td>
            <td><?=intval($p['stock'] ?? 0)?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
</body>
</html>
