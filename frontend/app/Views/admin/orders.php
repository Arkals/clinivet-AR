<?php include __DIR__ . '/../../Views/layouts/main.php'; ?>
<?php if (empty($_SESSION['user']) || empty($_SESSION['user']['is_admin'])) { $BASE = \App\Helpers\Security::base(); header('Location: ' . $BASE . '/home'); exit; } ?>
<div class="row">
  <div class="col-md-10 offset-md-1">
    <h3>Panel de administración - Órdenes</h3>
    <table class="table">
      <thead><tr><th>ID</th><th>Usuario</th><th>Total</th><th>Status</th><th>Acciones</th></tr></thead>
      <tbody>
        <?php foreach($orders as $o): ?>
          <tr>
            <td><?=intval($o['id'])?></td>
            <td><?=htmlspecialchars($o['user_email'] ?? $o['user_id'])?></td>
            <td>$<?=number_format($o['total'],2)?></td>
            <td><?=htmlspecialchars($o['status'])?></td>
            <td>
              <?php if ($o['status'] !== 'completed'): ?>
                <form method="post" style="display:inline-block;">
                  <input type="hidden" name="action" value="mark_paid">
                  <input type="hidden" name="order_id" value="<?=intval($o['id'])?>">
                  <button class="btn btn-sm btn-success">Marcar como pagada</button>
                </form>
              <?php else: ?>
                <span class="text-muted">Sin acciones</span>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
</body>
</html>
