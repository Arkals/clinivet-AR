<?php include __DIR__ . '/../layouts/main.php'; ?>
<div class="row">
  <div class="col-md-8 offset-md-2">
    <div class="card p-4">
      <?php $pm = $_POST['payment_method'] ?? null; ?>
      <?php if ($pm === 'bank'): ?>
        <h3>Orden registrada (Pago pendiente)</h3>
        <p>Hemos registrado tu orden. Para completar la compra, realiza la transferencia a la siguiente cuenta:</p>
        <ul>
          <li><strong>Banco:</strong> BBVA</li>
          <li><strong>Cuenta:</strong> <code>4152314001002164</code></li>
          <li><strong>Titular:</strong> Alexis Arce</li>
        </ul>
        <p>Cuando recibamos la confirmación del pago procederemos a procesar tu pedido y generaremos la factura definitiva.</p>
      <?php else: ?>
        <h3>Pago recibido</h3>
        <p>Gracias por tu compra. Se ha generado la factura y se ha enviado por correo.</p>
      <?php endif; ?>
      <?php $BASE = \App\Helpers\Security::base(); ?>
      <a href="<?= $BASE ?>/home" class="btn btn-primary">Volver a inicio</a>
    </div>
  </div>
</div>
