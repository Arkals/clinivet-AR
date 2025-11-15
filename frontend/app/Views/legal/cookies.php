<?php include __DIR__ . '/../layouts/main.php'; ?>
<div class="container" style="max-width:960px;padding:30px 0 60px">
  <h1 class="mb-3"><i class="bi bi-cookie text-primary me-2"></i>Política de cookies</h1>
  <p class="text-muted">Última actualización: <?= date('Y-m-d') ?></p>
  <div class="card p-4">
    <h5>¿Qué cookies usamos?</h5>
    <ul>
      <li><strong>Esenciales (obligatorias):</strong> sesión, seguridad, carrito.</li>
      <li><strong>Preferencias:</strong> recordamos tu consentimiento de cookies.</li>
    </ul>
    <h5 class="mt-3">Gestión del consentimiento</h5>
    <p>Puedes aceptar o rechazar las no esenciales desde el banner o borrando la cookie <code>cookie_consent</code>.</p>
    <p class="mt-3">Consulta nuestro <a href="<?= \App\Helpers\Security::base() ?>/aviso-privacidad">Aviso de privacidad</a> para más información.</p>
  </div>
</div>
</body>
</html>
