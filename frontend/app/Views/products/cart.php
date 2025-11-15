<?php include __DIR__ . '/../layouts/main.php'; ?>

<!-- Page Header -->
<div class="cart-header mb-5">
  <div class="container">
    <div class="row align-items-center">
      <div class="col-md-8">
        <h1 class="cart-title">
          <i class="bi bi-cart-fill text-primary me-3"></i>
          Mi Carrito de Compras
        </h1>
        <p class="cart-subtitle">Revisa tus productos antes de proceder al pago</p>
      </div>
      <div class="col-md-4 text-md-end">
        <?php if (!empty($items)): ?>
          <div class="cart-summary-badge">
            <span class="items-count"><?= count($items) ?> productos</span>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<div class="container">
  <?php if (empty($items)): ?>
    <!-- Empty Cart State -->
    <div class="empty-cart">
      <div class="empty-cart-content">
        <div class="empty-cart-icon">
          <i class="bi bi-cart-x"></i>
        </div>
        <h3>Tu carrito está vacío</h3>
        <p class="text-muted">¡Explora nuestros productos y encuentra algo increíble para tu mascota!</p>
        <div class="empty-cart-actions">
          <?php $BASE = \App\Helpers\Security::base(); ?>
          <a href="<?= $BASE ?>/productos" class="btn btn-primary btn-lg">
            <i class="bi bi-shop me-2"></i>
            Explorar productos
          </a>
          <a href="<?= $BASE ?>/home" class="btn btn-outline-primary btn-lg">
            <i class="bi bi-house me-2"></i>
            Ir al inicio
          </a>
        </div>
      </div>
    </div>
  <?php else: ?>
    <div class="row">
      <!-- Cart Items -->
      <div class="col-lg-8">
        <div class="cart-items">
          <div class="cart-items-header">
            <h4><i class="bi bi-bag-check me-2"></i>Productos en tu carrito</h4>
            <button class="btn btn-outline-danger btn-sm clear-cart-btn">
              <i class="bi bi-trash me-1"></i>Vaciar carrito
            </button>
          </div>

          <div class="cart-items-list">
            <?php $sum = 0; foreach($items as $item): 
              $itemTotal = $item['price'] * $item['quantity'];
              $sum += $itemTotal; 
            ?>
              <div class="cart-item" data-id="<?=$item['id']?>" data-aos="fade-up">
                <div class="item-image">
                  <?php if (!empty($item['image'])): ?>
                    <?php
                      $__img = $item['image'];
                      $__img = ltrim($__img, '/');
                      $__img = preg_replace('#^(frontend/public/)+#', '', $__img);
                      $__img = preg_replace('#^(public/)+#', '', $__img);
                      $__img = '/' . ltrim($__img, '/');
                    ?>
                    <img src="<?=htmlspecialchars(($assetBaseUrl ?? $baseUrl) . $__img)?>" alt="<?=htmlspecialchars($item['name'])?>" />
                  <?php else: ?>
                    <div class="placeholder-image">
                      <i class="bi bi-heart-pulse-fill"></i>
                    </div>
                  <?php endif; ?>
                </div>

                <div class="item-details">
                  <h5 class="item-name"><?=htmlspecialchars($item['name'])?></h5>
                  <p class="item-category text-muted">Producto para mascotas</p>
                  <div class="item-features">
                    <span class="feature-badge">
                      <i class="bi bi-shield-check"></i>Calidad garantizada
                    </span>
                    <span class="feature-badge">
                      <i class="bi bi-truck"></i>Envío incluido
                    </span>
                  </div>
                </div>

                <div class="item-controls">
                  <div class="quantity-controls">
                    <button class="qty-btn qty-dec" data-id="<?=$item['id']?>">
                      <i class="bi bi-dash"></i>
                    </button>
                    <span class="quantity-display"><?=$item['quantity']?></span>
                    <button class="qty-btn qty-inc" data-id="<?=$item['id']?>">
                      <i class="bi bi-plus"></i>
                    </button>
                  </div>
                  
                  <div class="item-pricing">
                    <div class="unit-price">$<?=number_format($item['price'],2)?> c/u</div>
                    <div class="total-price">$<?=number_format($itemTotal,2)?></div>
                  </div>

                  <button class="remove-btn" data-id="<?=$item['id']?>">
                    <i class="bi bi-x-lg"></i>
                  </button>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>

      <!-- Order Summary -->
      <div class="col-lg-4">
        <div class="order-summary">
          <div class="summary-header">
            <h4><i class="bi bi-receipt me-2"></i>Resumen del pedido</h4>
          </div>

          <div class="summary-content">
            <div class="summary-row">
              <span>Subtotal (<?= count($items) ?> productos)</span>
              <span class="subtotal-amount">$<?=number_format($sum,2)?></span>
            </div>

            <div class="summary-row">
              <span>Envío</span>
              <span class="shipping-amount text-success">
                <?php if($sum >= 500): ?>
                  <del class="text-muted">$50.00</del> ¡Gratis!
                <?php else: ?>
                  $50.00
                <?php endif; ?>
              </span>
            </div>

            <?php if($sum >= 500): ?>
              <div class="shipping-promotion">
                <i class="bi bi-gift text-success me-2"></i>
                <span>¡Felicidades! Tienes envío gratis</span>
              </div>
            <?php else: ?>
              <div class="shipping-promotion">
                <i class="bi bi-info-circle text-primary me-2"></i>
                <span>Agrega $<?=number_format(500-$sum,2)?> más para envío gratis</span>
              </div>
            <?php endif; ?>

            <div class="summary-divider"></div>

            <div class="summary-total">
              <span>Total</span>
              <span class="total-amount">$<?=number_format($sum + ($sum >= 500 ? 0 : 50),2)?></span>
            </div>

            <div class="checkout-actions">
              <button class="btn btn-primary btn-lg btn-checkout">
                <i class="bi bi-credit-card me-2"></i>
                Proceder al pago
              </button>
              
              <div class="checkout-security">
                <div class="security-badges">
                  <div class="security-badge">
                    <i class="bi bi-shield-fill-check"></i>
                    <span>Pago seguro</span>
                  </div>
                  <div class="security-badge">
                    <i class="bi bi-truck"></i>
                    <span>Envío rápido</span>
                  </div>
                  <div class="security-badge">
                    <i class="bi bi-arrow-return-left"></i>
                    <span>Devoluciones</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Recommended Products -->
        <div class="recommended-products mt-4">
          <h5><i class="bi bi-stars me-2"></i>Te podría interesar</h5>
          <div class="recommended-list">
            <div class="recommended-item">
              <div class="rec-image">
                <div class="placeholder-image">
                  <i class="bi bi-heart-pulse-fill"></i>
                </div>
              </div>
              <div class="rec-details">
                <h6>Alimento Premium</h6>
                <div class="rec-price">$299.00</div>
              </div>
              <button class="btn btn-outline-primary btn-sm">
                <i class="bi bi-plus"></i>
              </button>
            </div>
            
            <div class="recommended-item">
              <div class="rec-image">
                <div class="placeholder-image">
                  <i class="bi bi-heart-pulse-fill"></i>
                </div>
              </div>
              <div class="rec-details">
                <h6>Juguete Interactivo</h6>
                <div class="rec-price">$159.00</div>
              </div>
              <button class="btn btn-outline-primary btn-sm">
                <i class="bi bi-plus"></i>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Continue Shopping -->
    <div class="continue-shopping">
      <a href="<?= $BASE ?>/productos" class="btn btn-outline-primary btn-lg">
        <i class="bi bi-arrow-left me-2"></i>
        Continuar comprando
      </a>
    </div>
  <?php endif; ?>
</div>

<style>
/* Cart Header */
.cart-header {
  background: linear-gradient(135deg, rgba(11,143,143,0.05) 0%, rgba(45,181,176,0.08) 100%);
  border-radius: 25px;
  padding: 50px 30px;
  margin-bottom: 40px;
}

.cart-title {
  font-size: 2.5rem;
  font-weight: 800;
  color: #333;
  margin-bottom: 10px;
}

.cart-subtitle {
  font-size: 1.1rem;
  color: #666;
  margin: 0;
}

.cart-summary-badge {
  background: linear-gradient(135deg, #0b8f8f, #2db5b0);
  color: white;
  padding: 15px 25px;
  border-radius: 50px;
  font-weight: 600;
  box-shadow: 0 8px 25px rgba(11,143,143,0.3);
}

/* Empty Cart */
.empty-cart {
  text-align: center;
  padding: 100px 20px;
  background: white;
  border-radius: 30px;
  box-shadow: 0 15px 50px rgba(0,0,0,0.08);
}

.empty-cart-icon {
  font-size: 120px;
  color: #ddd;
  margin-bottom: 30px;
}

.empty-cart h3 {
  font-size: 2rem;
  color: #333;
  margin-bottom: 15px;
}

.empty-cart-actions {
  margin-top: 40px;
  display: flex;
  gap: 15px;
  justify-content: center;
  flex-wrap: wrap;
}

/* Cart Items */
.cart-items {
  background: white;
  border-radius: 25px;
  padding: 30px;
  box-shadow: 0 10px 30px rgba(0,0,0,0.06);
  margin-bottom: 30px;
}

.cart-items-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 30px;
  padding-bottom: 20px;
  border-bottom: 2px solid #f0f0f0;
}

.cart-items-header h4 {
  color: #333;
  font-weight: 700;
  margin: 0;
}

.clear-cart-btn:hover {
  background: #dc3545;
  color: white;
  border-color: #dc3545;
}

/* Cart Item */
.cart-item {
  display: flex;
  align-items: center;
  gap: 20px;
  padding: 25px;
  margin-bottom: 20px;
  background: #f8fdfd;
  border-radius: 20px;
  border: 2px solid transparent;
  transition: all 0.3s ease;
}

.cart-item:hover {
  border-color: rgba(11,143,143,0.2);
  transform: translateY(-2px);
  box-shadow: 0 8px 25px rgba(11,143,143,0.1);
}

.item-image {
  width: 100px;
  height: 100px;
  border-radius: 15px;
  overflow: hidden;
  flex-shrink: 0;
}

.item-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.placeholder-image {
  width: 100%;
  height: 100%;
  background: linear-gradient(135deg, #e6f7f7, #d0f0f0);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 40px;
  color: #0b8f8f;
}

.item-details {
  flex: 1;
}

.item-name {
  font-weight: 700;
  color: #333;
  margin-bottom: 5px;
  font-size: 1.1rem;
}

.item-category {
  font-size: 0.9rem;
  margin-bottom: 15px;
}

.item-features {
  display: flex;
  gap: 10px;
  flex-wrap: wrap;
}

.feature-badge {
  background: rgba(11,143,143,0.1);
  color: #0b8f8f;
  padding: 4px 10px;
  border-radius: 15px;
  font-size: 0.8rem;
  font-weight: 600;
  display: flex;
  align-items: center;
  gap: 4px;
}

/* Item Controls */
.item-controls {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 15px;
  position: relative;
}

.quantity-controls {
  display: flex;
  align-items: center;
  background: white;
  border-radius: 50px;
  padding: 5px;
  box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.qty-btn {
  width: 35px;
  height: 35px;
  border: none;
  background: #0b8f8f;
  color: white;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.3s ease;
}

.qty-btn:hover {
  background: #086f6f;
  transform: scale(1.1);
}

.quantity-display {
  padding: 0 15px;
  font-weight: 700;
  font-size: 1.1rem;
  color: #333;
}

.item-pricing {
  text-align: center;
}

.unit-price {
  font-size: 0.9rem;
  color: #666;
  margin-bottom: 5px;
}

.total-price {
  font-size: 1.3rem;
  font-weight: 800;
  color: #0b8f8f;
}

.remove-btn {
  position: absolute;
  top: -10px;
  right: -10px;
  width: 30px;
  height: 30px;
  border: none;
  background: #dc3545;
  color: white;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.3s ease;
  box-shadow: 0 4px 15px rgba(220,53,69,0.3);
}

.remove-btn:hover {
  background: #c82333;
  transform: scale(1.1);
}

/* Order Summary */
.order-summary {
  background: white;
  border-radius: 25px;
  padding: 30px;
  box-shadow: 0 10px 30px rgba(0,0,0,0.06);
  position: sticky;
  top: 20px;
}

.summary-header h4 {
  color: #333;
  font-weight: 700;
  margin-bottom: 25px;
}

.summary-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 12px 0;
  border-bottom: 1px solid #f0f0f0;
}

.summary-row:last-child {
  border-bottom: none;
}

.subtotal-amount, .shipping-amount {
  font-weight: 600;
  color: #333;
}

.shipping-promotion {
  background: linear-gradient(135deg, rgba(11,143,143,0.1), rgba(45,181,176,0.08));
  padding: 15px;
  border-radius: 15px;
  margin: 20px 0;
  font-size: 0.9rem;
  font-weight: 600;
}

.summary-divider {
  height: 2px;
  background: linear-gradient(90deg, transparent, #0b8f8f, transparent);
  margin: 20px 0;
}

.summary-total {
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-size: 1.3rem;
  font-weight: 800;
  color: #333;
  margin-bottom: 30px;
}

.total-amount {
  color: #0b8f8f;
}

.btn-checkout {
  width: 100%;
  padding: 15px;
  font-weight: 700;
  border-radius: 15px;
  margin-bottom: 20px;
  background: linear-gradient(135deg, #0b8f8f, #2db5b0);
  border: none;
  box-shadow: 0 8px 25px rgba(11,143,143,0.3);
  transition: all 0.3s ease;
}

.btn-checkout:hover {
  transform: translateY(-2px);
  box-shadow: 0 12px 35px rgba(11,143,143,0.4);
}

.security-badges {
  display: flex;
  justify-content: space-around;
  gap: 10px;
}

.security-badge {
  text-align: center;
  font-size: 0.8rem;
  color: #666;
}

.security-badge i {
  display: block;
  font-size: 1.2rem;
  color: #0b8f8f;
  margin-bottom: 5px;
}

/* Recommended Products */
.recommended-products {
  background: white;
  border-radius: 20px;
  padding: 25px;
  box-shadow: 0 8px 25px rgba(0,0,0,0.06);
}

.recommended-products h5 {
  color: #333;
  font-weight: 700;
  margin-bottom: 20px;
}

.recommended-item {
  display: flex;
  align-items: center;
  gap: 15px;
  padding: 15px;
  border-radius: 15px;
  margin-bottom: 15px;
  background: #f8fdfd;
  transition: all 0.3s ease;
}

.recommended-item:hover {
  background: rgba(11,143,143,0.05);
}

.rec-image {
  width: 50px;
  height: 50px;
  border-radius: 10px;
  overflow: hidden;
}

.rec-details {
  flex: 1;
}

.rec-details h6 {
  margin: 0 0 5px 0;
  font-weight: 600;
  color: #333;
  font-size: 0.9rem;
}

.rec-price {
  color: #0b8f8f;
  font-weight: 700;
  font-size: 0.9rem;
}

/* Continue Shopping */
.continue-shopping {
  text-align: center;
  margin: 50px 0;
}

/* Responsive */
@media (max-width: 768px) {
  .cart-title {
    font-size: 2rem;
  }
  
  .cart-item {
    flex-direction: column;
    text-align: center;
    gap: 15px;
  }
  
  .item-controls {
    flex-direction: row;
    justify-content: space-between;
    width: 100%;
  }
  
  .empty-cart-actions {
    flex-direction: column;
    align-items: center;
  }
  
  .security-badges {
    flex-direction: column;
    gap: 15px;
  }
}
</style>

<!-- AOS Animation Library -->
<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

<script>
// Initialize AOS animations
AOS.init({
  duration: 600,
  once: true,
  offset: 100
});

function updateCartRow(id, qty, cartCount) {
  let item = document.querySelector('.cart-item[data-id="'+id+'"]');
  if (!item) return;
  
  if (qty <= 0) {
    // Animate removal
    item.style.transform = 'translateX(100%)';
    item.style.opacity = '0';
    setTimeout(() => {
      item.remove();
      // Check if cart is empty
      if (document.querySelectorAll('.cart-item').length === 0) {
        location.reload();
      }
    }, 300);
  } else {
    // Update quantity display
    item.querySelector('.quantity-display').textContent = qty;
    // Update total price (you might want to calculate this on the frontend)
    location.reload(); // For now, reload to update totals
  }
  
  // Update cart count in header
  document.querySelectorAll('.cart-badge').forEach(e => e.textContent = cartCount);
}

// Quantity controls
document.querySelectorAll('.qty-inc').forEach(btn => {
  btn.onclick = function() {
    let id = this.dataset.id;
    this.style.transform = 'scale(0.9)';
    setTimeout(() => this.style.transform = 'scale(1)', 150);
    
    fetch('<?= $BASE ?>/actualizar-carrito?id='+id+'&action=inc', {headers:{'X-Requested-With':'XMLHttpRequest'}})
      .then(r=>r.json())
      .then(data=>{ 
        if(data.ok) updateCartRow(id, data.qty, data.cart_count); 
      });
  }
});

document.querySelectorAll('.qty-dec').forEach(btn => {
  btn.onclick = function() {
    let id = this.dataset.id;
    this.style.transform = 'scale(0.9)';
    setTimeout(() => this.style.transform = 'scale(1)', 150);
    
    fetch('<?= $BASE ?>/actualizar-carrito?id='+id+'&action=dec', {headers:{'X-Requested-With':'XMLHttpRequest'}})
      .then(r=>r.json())
      .then(data=>{ 
        if(data.ok) updateCartRow(id, data.qty, data.cart_count); 
      });
  }
});

document.querySelectorAll('.remove-btn').forEach(btn => {
  btn.onclick = function() {
    let id = this.dataset.id;
    
    // Confirm removal
    if (confirm('¿Estás seguro de que quieres eliminar este producto?')) {
      fetch('<?= $BASE ?>/actualizar-carrito?id='+id+'&action=del', {headers:{'X-Requested-With':'XMLHttpRequest'}})
        .then(r=>r.json())
        .then(data=>{ 
          if(data.ok) updateCartRow(id, 0, data.cart_count); 
        });
    }
  }
});

// Clear cart
document.querySelector('.clear-cart-btn')?.addEventListener('click', function() {
  if (confirm('¿Estás seguro de que quieres vaciar todo el carrito?')) {
    // You might want to implement a clear cart endpoint
    location.href = '<?= $BASE ?>/cart-vaciar';
  }
});

// Checkout button
document.querySelector('.btn-checkout')?.addEventListener('click', function() {
  window.location.href = '<?= $BASE ?>/checkout';
});

// Add loading state to buttons
function addLoadingState(button) {
  const originalText = button.innerHTML;
  button.innerHTML = '<i class="bi bi-arrow-repeat spin"></i>';
  button.disabled = true;
  
  setTimeout(() => {
    button.innerHTML = originalText;
    button.disabled = false;
  }, 1000);
}

// Smooth scroll animations
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
  anchor.addEventListener('click', function (e) {
    e.preventDefault();
    document.querySelector(this.getAttribute('href')).scrollIntoView({
      behavior: 'smooth'
    });
  });
});
</script>

<style>
.spin {
  animation: spin 1s linear infinite;
}

@keyframes spin {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}
</style>
