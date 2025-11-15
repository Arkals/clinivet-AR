<?php include __DIR__ . '/../layouts/main.php'; ?>
<style>
  /* Local styles for checkout form */
  #card-element { border: none; }
  .card .form-control, .card .form-select { border-radius:8px; }
  /* Visual payment card tweaks */
  .payment-card { border-radius:12px; overflow:hidden; }
  .payment-card .card { background:linear-gradient(135deg,#0b8f8f 0%, #2db5b0 100%); }
  @media(max-width:575px){ .payment-card .card { padding:12px!important } }
</style>
<?php
// Load stripe keys from backend helpers (compute project-root relative path)
$stripePath = dirname(__DIR__, 4) . '/backend/app/Helpers/stripe_keys.php';
if (file_exists($stripePath)) {
  require_once $stripePath;
} else {
  // Log or show a friendly message in dev
  error_log("stripe_keys.php not found at: $stripePath");
  // Avoid fatal error: define empty constants so view doesn't crash
  if (!defined('STRIPE_SECRET_KEY')) define('STRIPE_SECRET_KEY', '');
  if (!defined('STRIPE_PUBLISHABLE_KEY')) define('STRIPE_PUBLISHABLE_KEY', '');
}
// Compute publishable key and whether to use Stripe.js (disabled on localhost/HTTP)
$pk = (defined('STRIPE_PUBLISHABLE_KEY') ? STRIPE_PUBLISHABLE_KEY : '');
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$isHttp = empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] === 'off';
if ($isHttp || stripos($host, 'localhost') !== false) { $pk = ''; }
$useStripe = ($pk !== '');
?>
<div class="row justify-content-center">
  <div class="col-md-6">
    <div class="card p-4">
      <h4 class="mb-3">Finalizar compra</h4>
      <?php if (!empty($_SESSION['flash_error'])): ?>
        <div class="alert alert-danger"><?=htmlspecialchars($_SESSION['flash_error'])?></div>
        <?php unset($_SESSION['flash_error']); ?>
      <?php endif; ?>
      <form method="post" id="checkoutForm" novalidate>
        <!-- Tarjeta visual / inputs de tarjeta -->
        <div class="mb-2">
          <?php if ($useStripe): ?>
            <div class="card p-2" style="background:linear-gradient(135deg,#0b8f8f 0%, #2db5b0 100%); color:#fff; border-radius:10px; min-height:110px; max-width:340px; margin:auto; box-shadow:0 2px 8px rgba(11,143,143,0.10);">
              <div class="d-flex justify-content-between align-items-center mb-2">
                <div style="font-weight:700;letter-spacing:0.6px;font-size:1.1em;">Clinivet</div>
                <div id="card-brand" style="font-size:18px;font-weight:700">&nbsp;</div>
              </div>
              <div class="card p-1 mb-1" style="background:rgba(255,255,255,0.10); border-radius:8px;">
                <div id="card-element" style="min-height:32px; padding:4px; color:#fff;"></div>
              </div>
              <div class="d-flex justify-content-between" style="font-size:13px;opacity:0.92;">
                <div><small>Nombre</small> <span><?=htmlspecialchars($_SESSION['user']['name'] ?? '')?></span></div>
                <div><small>Expira</small> <span>MM/AA</span></div>
              </div>
              <div id="card-errors" role="alert" class="form-text text-warning mt-1" style="margin-bottom:2px;"></div>
            </div>
          <?php else: ?>
            <div class="card p-3" style="background:linear-gradient(135deg,#0b8f8f 0%, #2db5b0 100%); color:#fff; border-radius:10px; max-width:420px; margin:auto; box-shadow:0 2px 8px rgba(11,143,143,0.10);">
              <div class="d-flex justify-content-between align-items-center mb-2">
                <div style="font-weight:700;letter-spacing:0.6px;font-size:1.1em;">Clinivet</div>
                <div id="card-brand" style="font-size:18px;font-weight:700">&nbsp;</div>
              </div>
              <div class="row g-2">
                <div class="col-12">
                  <label class="form-label text-white-50 mb-1">Número de tarjeta</label>
                  <input type="text" inputmode="numeric" autocomplete="cc-number" class="form-control form-control-sm" id="cc-number" placeholder="4111 1111 1111 1111" maxlength="19" required>
                </div>
                <div class="col-6">
                  <label class="form-label text-white-50 mb-1">MM/AA</label>
                  <input type="text" inputmode="numeric" autocomplete="cc-exp" class="form-control form-control-sm" id="cc-exp" placeholder="MM/AA" maxlength="5" required>
                </div>
                <div class="col-6">
                  <label class="form-label text-white-50 mb-1">CVC</label>
                  <input type="text" inputmode="numeric" autocomplete="cc-csc" class="form-control form-control-sm" id="cc-cvc" placeholder="123" maxlength="4" required>
                </div>
              </div>
              <div id="card-errors" role="alert" class="form-text text-warning mt-2" style="margin-bottom:2px;"></div>
            </div>
          <?php endif; ?>
        </div>
        <!-- Datos del cliente -->
        <div class="mb-2">
          <label class="form-label">Nombre</label>
          <input class="form-control" name="name" value="<?=htmlspecialchars($_SESSION['user']['name'] ?? '')?>" required>
        </div>
        <div class="mb-2">
          <label class="form-label">Email</label>
          <input class="form-control" name="email" value="<?=htmlspecialchars($_SESSION['user']['email'] ?? '')?>" required>
        </div>
        <div class="mb-2">
          <label class="form-label">RFC (opcional)</label>
          <input class="form-control" name="rfc" value="<?=htmlspecialchars($_SESSION['user']['rfc'] ?? '')?>">
        </div>
  <input type="hidden" name="payment_method" value="card">
  <input type="hidden" id="stripeToken" name="stripeToken" value="">

        <div class="d-flex align-items-center mt-3">
          <button class="btn btn-primary" id="payBtn" type="submit">
            <span id="payBtnText">Pagar</span>
            <span id="paySpinner" class="spinner-border spinner-border-sm ms-2" role="status" style="display:none" aria-hidden="true"></span>
          </button>
          <span id="payLoader" class="ms-3 text-muted" style="display:none">Procesando pago...</span>
        </div>
      </form>
    </div>
  </div>
</div>
<?php ?>
<?php if ($pk): ?>
  <script src="https://js.stripe.com/v3/"></script>
  <script>
    var stripe = Stripe('<?=htmlspecialchars($pk)?>');
    var elements = stripe.elements();
    var style = { base: { fontSize: '16px', color: '#32325d' } };
    var card = elements.create('card', { style: style });
    card.mount('#card-element');
    card.on('change', function(event) {
      var displayError = document.getElementById('card-errors');
      displayError.textContent = event.error ? event.error.message : '';
      // Card brand detection
      var brandEl = document.getElementById('card-brand');
      if (event.brand) {
        var b = event.brand;
        var icon = '';
        if (b === 'visa') icon = '<img src="https://cdn.jsdelivr.net/gh/edent/SuperTinyIcons/images/svg/visa.svg" style="height:20px;filter:invert(1);">';
        else if (b === 'mastercard') icon = '<img src="https://cdn.jsdelivr.net/gh/edent/SuperTinyIcons/images/svg/mastercard.svg" style="height:20px;filter:invert(1);">';
        else if (b === 'amex') icon = '<span style="font-weight:700">AMEX</span>';
        else icon = b.toUpperCase();
        brandEl.innerHTML = icon;
      } else {
        brandEl.innerHTML = '&nbsp;';
      }
    });
    var form = document.getElementById('checkoutForm');
    form.addEventListener('submit', function(ev) {
      ev.preventDefault();
      document.getElementById('payBtn').disabled = true;
      document.getElementById('paySpinner').style.display = 'inline-block';
      document.getElementById('payLoader').style.display = 'inline-block';
      stripe.createToken(card).then(function(result) {
        if (result.error || !result.token) {
          document.getElementById('card-errors').textContent = result.error ? result.error.message : 'No se pudo generar el token de pago. Verifica los datos de la tarjeta.';
          document.getElementById('payBtn').disabled = false;
          document.getElementById('paySpinner').style.display = 'none';
          document.getElementById('payLoader').style.display = 'none';
        } else {
          document.getElementById('stripeToken').value = result.token.id;
          // Solo envía si el token existe
          form.submit();
        }
      });
    });
  </script>
<?php else: ?>
  <script>
    // Formateo básico de tarjeta
    (function() {
      const num = document.getElementById('cc-number');
      const exp = document.getElementById('cc-exp');
      const cvc = document.getElementById('cc-cvc');
      const brandEl = document.getElementById('card-brand');
      const form = document.getElementById('checkoutForm');
      const err = document.getElementById('card-errors');

      function detectBrand(digits) {
        if (/^4/.test(digits)) return 'VISA';
        if (/^5[1-5]/.test(digits)) return 'MASTERCARD';
        if (/^3[47]/.test(digits)) return 'AMEX';
        return '';
      }

      function formatNumber(v) {
        return v.replace(/\D/g,'').slice(0,16).replace(/(\d{4})(?=\d)/g,'$1 ').trim();
      }
      function formatExp(v) {
        v = v.replace(/\D/g,'').slice(0,4);
        if (v.length >= 3) return v.slice(0,2) + '/' + v.slice(2,4);
        if (v.length >= 1 && parseInt(v[0],10) > 1) v = '0' + v; // quick autocorrect
        return v;
      }

      num && num.addEventListener('input', function() {
        const pos = this.selectionStart;
        const prev = this.value;
        this.value = formatNumber(this.value);
        const digits = this.value.replace(/\s/g,'');
        const b = detectBrand(digits);
        brandEl && (brandEl.textContent = b);
      });
      exp && exp.addEventListener('input', function() {
        this.value = formatExp(this.value);
      });
      cvc && cvc.addEventListener('input', function() {
        this.value = this.value.replace(/\D/g,'').slice(0,4);
      });

      form && form.addEventListener('submit', function(e) {
        e.preventDefault();
        err.textContent = '';

        // Validación mínima
        const digits = (num?.value || '').replace(/\s/g,'');
        const expVal = (exp?.value || '');
        const cvcVal = (cvc?.value || '');
        let ok = true;
        if (!digits || digits.length < 13) { ok = false; err.textContent = 'Número de tarjeta inválido.'; }
        else if (!/^(0[1-9]|1[0-2])\/\d{2}$/.test(expVal)) { ok = false; err.textContent = 'Fecha de expiración inválida.'; }
        else if (cvcVal.length < 3) { ok = false; err.textContent = 'CVC inválido.'; }

        if (!ok) return;

        // UI de procesamiento
        document.getElementById('payBtn').disabled = true;
        document.getElementById('paySpinner').style.display = 'inline-block';
        document.getElementById('payLoader').style.display = 'inline-block';

        // Simular token de Stripe y enviar
        document.getElementById('stripeToken').value = 'tok_demo_' + Date.now();
        setTimeout(function(){ form.submit(); }, 600);
      });
    })();
  </script>
<?php endif; ?>
