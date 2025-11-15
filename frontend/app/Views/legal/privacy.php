<?php include __DIR__ . '/../layouts/main.php'; ?>
<div class="container" style="max-width:960px;padding:30px 0 60px">
  <h1 class="mb-3"><i class="bi bi-shield-lock text-primary me-2"></i>Aviso de privacidad</h1>
  <p class="text-muted">Última actualización: <?= date('Y-m-d') ?></p>
  <div class="card p-4">
    <p>En Clinivet, respetamos tu privacidad. Esta página describe qué datos recopilamos, cómo los usamos y tus derechos.</p>
    <h5 class="mt-3">Datos que recopilamos</h5>
    <ul>
      <li>Datos de cuenta: nombre, correo electrónico.</li>
      <li>Datos de compra: productos, montos y comprobantes.</li>
    </ul>
    <h5 class="mt-3">Uso de datos</h5>
    <ul>
      <li>Prestar el servicio de tienda y facturación.</li>
      <li>Seguridad y prevención de fraude.</li>
      <li>Comunicaciones esenciales sobre tu cuenta o pedidos.</li>
    </ul>
    <h5 class="mt-3">Tus derechos</h5>
    <p>Puedes solicitar acceso, rectificación o eliminación de tus datos contactándonos.</p>
    <p class="mt-3">Para más detalles sobre cookies, consulta nuestra <a href="<?= \App\Helpers\Security::base() ?>/politica-cookies">Política de cookies</a>.</p>
  </div>
</div>
</body>
</html>
