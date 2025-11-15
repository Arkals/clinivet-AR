<?php include __DIR__ . '/../layouts/main.php'; ?>
<div class="row">
  <div class="col-md-10 offset-md-1">
    <h4>Mis facturas</h4>
    <?php
      if (empty($_SESSION['user'])) {
        echo '<div class="alert alert-warning">Debes iniciar sesión para ver tus facturas.</div>';
      } else {
        $pdo = \App\Helpers\DB::get();
        $stmt = $pdo->prepare("SELECT i.*, o.user_id, o.id as order_id FROM invoices i LEFT JOIN orders o ON i.order_id = o.id WHERE o.user_id = ? ORDER BY i.id DESC");
        $stmt->execute([$_SESSION['user']['id']]);
        $invoices = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (empty($invoices)) {
          echo '<div class="alert alert-info">No se encontraron facturas para tu cuenta.</div>';
        } else {
          echo '<table class="table"><thead><tr><th>Folio</th><th>UUID</th><th>Fecha</th><th>Total</th><th>Descargas</th></tr></thead><tbody>';
          foreach ($invoices as $inv) {
            $xml = htmlspecialchars(basename($inv['xml_path']));
            $pdf = htmlspecialchars(basename($inv['pdf_path']));
            $folio = htmlspecialchars($inv['order_id']);
            echo "<tr>";
            echo "<td>$folio</td>";
            echo "<td>" . htmlspecialchars($inv['uuid']) . "</td>";
            echo "<td>" . date('Y-m-d H:i', file_exists($inv['pdf_path']) ? filemtime($inv['pdf_path']) : time()) . "</td>";
            echo "<td>" . (isset($inv['total']) ? htmlspecialchars($inv['total']) : '-') . "</td>";
            $BASE = \App\Helpers\Security::base();
            echo "<td> <a class=\"btn btn-sm btn-primary\" href=\"{$BASE}/descargar-factura?type=pdf&f=$pdf&oid=$folio\" download target=\"_blank\" rel=\"noopener\">PDF</a> ";
            echo " <a class=\"btn btn-sm btn-outline-primary\" href=\"{$BASE}/descargar-factura?type=xml&f=$xml&oid=$folio\" target=\"_blank\" rel=\"noopener\">XML</a></td>";
            echo "</tr>";
          }
          echo '</tbody></table>';
        }
      }
    ?>
  </div>
</div>
