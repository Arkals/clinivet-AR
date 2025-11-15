<?php include __DIR__ . '/layouts/main.php'; ?>

<!-- Hero Section -->
<div class="hero-section mb-5">
  <div class="row align-items-center">
    <div class="col-lg-6">
      <div class="hero-content">
        <h1 class="display-4 fw-bold text-primary mb-3">
          <i class="bi bi-heart-pulse-fill text-danger me-3"></i>
          Clinivet Store
        </h1>
        <p class="lead text-muted mb-4">
          Tu tienda de confianza para el cuidado y bienestar de tus mascotas. 
          Productos de calidad premium con entrega rápida y servicio excepcional.
        </p>
        <div class="d-flex gap-3 mb-4">
          <div class="feature-badge">
            <i class="bi bi-truck text-success"></i>
            <span>Envío gratis</span>
          </div>
          <div class="feature-badge">
            <i class="bi bi-shield-check text-primary"></i>
            <span>Calidad garantizada</span>
          </div>
          <div class="feature-badge">
            <i class="bi bi-headset text-info"></i>
            <span>Soporte 24/7</span>
          </div>
        </div>
          <?php $BASE = \App\Helpers\Security::base(); ?>
          <a href="<?= $BASE ?>/productos" class="btn btn-primary btn-lg">
          <i class="bi bi-shop"></i> Explorar productos
        </a>
      </div>
    </div>
    <div class="col-lg-6">
      <div class="hero-image text-center">
        <div class="hero-card">
          <i class="bi bi-heart-pulse-fill hero-icon"></i>
          <h3>¡Bienvenido a Clinivet!</h3>
          <p>Cuidamos lo que más amas</p>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Categories Section -->
<div class="row mb-5">
  <div class="col-12">
    <h2 class="text-center mb-4">
      <i class="bi bi-grid-3x3-gap-fill text-primary me-2"></i>
      Explora por categorías
    </h2>
    <div class="categories-grid">
      <?php foreach($cats as $index => $c): ?>
        <div class="category-card" data-aos="fade-up" data-aos-delay="<?= $index * 100 ?>">
          <a href="<?= $BASE ?>/categoria/<?=$c['id']?>" class="text-decoration-none">
            <div class="category-icon">
              <?php
              $nameNormalized = function_exists('mb_strtolower') ? mb_strtolower($c['name'], 'UTF-8') : strtolower($c['name']);
              $nameAscii = @iconv('UTF-8', 'ASCII//TRANSLIT', $nameNormalized) ?: $nameNormalized;

              $isCat = strpos($nameAscii, 'gat') !== false || strpos($nameAscii, 'fel') !== false || strpos($nameAscii, 'cat') !== false;
              $isDog = strpos($nameAscii, 'perr') !== false || strpos($nameAscii, 'can') !== false || strpos($nameAscii, 'dog') !== false;

              if ($isCat) {
                echo '<svg class="pet-svg cat" viewBox="0 0 64 64" role="img" aria-label="Categoría para gatos">'
                   . '<path d="M16 18l8 8a20 20 0 0 1 16 0l8-8 4 16v12c0 11-9 20-20 20s-20-9-20-20V34z" fill="currentColor"/>'
                   . '<circle cx="24" cy="40" r="3" fill="#0f4c4c"/>'
                   . '<circle cx="40" cy="40" r="3" fill="#0f4c4c"/>'
                   . '<path d="M32 44l4 6h-8z" fill="#0f4c4c"/>'
                   . '</svg>';
              } elseif ($isDog) {
                echo '<svg class="pet-svg dog" viewBox="0 0 64 64" role="img" aria-label="Categoría para perros">'
                   . '<path d="M12 24c0-7 5-12 12-12 4 0 8 2 10 6 2-4 6-6 10-6 7 0 12 5 12 12v10c0 11-9 20-22 20S12 45 12 34V24z" fill="currentColor"/>'
                   . '<circle cx="24" cy="38" r="3" fill="#0f4c4c"/>'
                   . '<circle cx="40" cy="38" r="3" fill="#0f4c4c"/>'
                   . '<path d="M24 50c4 4 12 4 16 0" stroke="#0f4c4c" stroke-width="2" stroke-linecap="round" fill="none"/>'
                   . '</svg>';
              } else {
                $icons = ['bi-bag-heart-fill', 'bi-cup-straw', 'bi-heart-fill', 'bi-star-fill', 'bi-gift-fill', 'bi-house-heart-fill'];
                $colors = ['text-primary', 'text-success', 'text-danger', 'text-warning', 'text-info', 'text-secondary'];
                $icon = $icons[$index % count($icons)];
                $color = $colors[$index % count($colors)];
                echo '<i class="bi ' . $icon . ' ' . $color . '"></i>';
              }
              ?>
            </div>
            <h5 class="category-name"><?=htmlspecialchars($c['name'])?></h5>
            <span class="category-link">Ver productos <i class="bi bi-arrow-right"></i></span>
          </a>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>

<!-- Featured Products -->
<div class="row">
  <div class="col-12">
    <h2 class="text-center mb-4">
      <i class="bi bi-star-fill text-warning me-2"></i>
      Productos destacados
    </h2>
    <div class="products-grid">
      <?php foreach($products as $index => $p): ?>
        <div class="product-card" data-aos="zoom-in" data-aos-delay="<?= $index * 150 ?>">
          <div class="product-image">
            <?php if (!empty($p['image'])): ?>
              <?php
                $__img = $p['image'];
                $__img = ltrim($__img, '/');
                $__img = preg_replace('#^(frontend/public/)+#', '', $__img);
                $__img = preg_replace('#^(public/)+#', '', $__img);
                $__img = '/' . ltrim($__img, '/');
              ?>
              <img src="<?=htmlspecialchars(($assetBaseUrl ?? $baseUrl) . $__img)?>" alt="<?=htmlspecialchars($p['name'])?>" />
            <?php else: ?>
              <div class="placeholder-image">
                <i class="bi bi-heart-pulse-fill"></i>
              </div>
            <?php endif; ?>
            <div class="product-overlay">
              <button class="btn btn-primary btn-add-cart" data-id="<?=$p['id']?>">
                <i class="bi bi-cart-plus"></i> Agregar
              </button>
            </div>
          </div>
          <div class="product-info">
            <div class="product-category"><?=htmlspecialchars($p['category_name'])?></div>
            <h5 class="product-title"><?=htmlspecialchars($p['name'])?></h5>
            <div class="product-price">$<?=number_format($p['price'],2)?></div>
            <div class="product-rating">
              <i class="bi bi-star-fill text-warning"></i>
              <i class="bi bi-star-fill text-warning"></i>
              <i class="bi bi-star-fill text-warning"></i>
              <i class="bi bi-star-fill text-warning"></i>
              <i class="bi bi-star text-muted"></i>
              <span class="ms-1 text-muted">(4.2)</span>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>

<!-- Stats Section -->
<div class="stats-section mt-5 mb-4">
  <div class="row text-center">
    <div class="col-md-3 col-6">
      <div class="stat-card">
        <i class="bi bi-people-fill text-primary"></i>
        <h3>5,000+</h3>
        <p>Clientes felices</p>
      </div>
    </div>
    <div class="col-md-3 col-6">
      <div class="stat-card">
        <i class="bi bi-box-seam-fill text-success"></i>
        <h3>10,000+</h3>
        <p>Productos entregados</p>
      </div>
    </div>
    <div class="col-md-3 col-6">
      <div class="stat-card">
        <i class="bi bi-award-fill text-warning"></i>
        <h3>98%</h3>
        <p>Satisfacción</p>
      </div>
    </div>
    <div class="col-md-3 col-6">
      <div class="stat-card">
        <i class="bi bi-truck text-info"></i>
        <h3>24h</h3>
        <p>Entrega rápida</p>
      </div>
    </div>
  </div>
</div>

<style>
/* Hero Section */
.hero-section {
  background: linear-gradient(135deg, rgba(11,143,143,0.05) 0%, rgba(45,181,176,0.08) 100%);
  border-radius: 20px;
  padding: 60px 40px;
  margin-bottom: 60px;
  position: relative;
  overflow: hidden;
}

.hero-section::before {
  content: '';
  position: absolute;
  top: -50%;
  right: -50%;
  width: 100%;
  height: 200%;
  background: radial-gradient(circle, rgba(11,143,143,0.08) 0%, transparent 70%);
  animation: float 6s ease-in-out infinite;
}

.hero-content h1 {
  font-weight: 800;
  background: linear-gradient(135deg, #0b8f8f, #2db5b0);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

.feature-badge {
  background: white;
  padding: 12px 18px;
  border-radius: 50px;
  box-shadow: 0 4px 15px rgba(0,0,0,0.1);
  display: flex;
  align-items: center;
  gap: 8px;
  font-weight: 600;
  font-size: 14px;
}

.hero-card {
  background: linear-gradient(135deg, #fff 0%, #f8fdfd 100%);
  padding: 60px 40px;
  border-radius: 30px;
  box-shadow: 0 20px 60px rgba(11,143,143,0.15);
  text-align: center;
  position: relative;
}

.hero-icon {
  font-size: 80px;
  background: linear-gradient(135deg, #0b8f8f, #2db5b0);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  margin-bottom: 20px;
}

/* Categories Grid */
.categories-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 25px;
  margin-bottom: 40px;
}

.category-card {
  background: white;
  padding: 35px 25px;
  border-radius: 20px;
  text-align: center;
  box-shadow: 0 8px 25px rgba(0,0,0,0.06);
  transition: all 0.3s ease;
  border: 2px solid transparent;
}

.category-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 15px 40px rgba(11,143,143,0.15);
  border-color: rgba(11,143,143,0.2);
}

.category-icon {
  width: 80px;
  height: 80px;
  margin: 0 auto 20px;
  background: linear-gradient(135deg, rgba(11,143,143,0.1), rgba(45,181,176,0.08));
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 35px;
}

.category-icon .pet-svg {
  width: 48px;
  height: 48px;
  color: #0b8f8f;
}

.category-icon .pet-svg.cat {
  color: #ffd166;
}

.category-name {
  font-weight: 700;
  color: #333;
  margin-bottom: 15px;
}

.category-link {
  color: #0b8f8f;
  font-weight: 600;
  font-size: 14px;
}

/* Products Grid */
.products-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 30px;
}

.product-image {
  position: relative;
  /* Fallback minimum height for older browsers */
  min-height: 220px;
  overflow: hidden;
  /* Center the image and avoid clipping */
  display: flex;
  align-items: center;
  justify-content: center;
  background: #fff;
  border-radius: 16px;
  /* Make the image adapt to container size preserving an aspect ratio */
  aspect-ratio: 4 / 3;
}
.product-image img {
  width: 100%;
  height: 100%;
  max-width: 100%;
  max-height: 100%;
  object-fit: contain;
  transition: transform 0.3s ease;
}
.product-image {
  position: relative;
  /* Fallback fixed height for older browsers */
  height: 220px;
  overflow: hidden;
  /* Make the image adapt to container size preserving an aspect ratio */
  aspect-ratio: 4 / 3;
}

.product-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.3s ease;
}

.product-card:hover .product-image img {
  transform: scale(1.1);
}

.placeholder-image {
  width: 100%;
  height: 100%;
  background: linear-gradient(135deg, #f8fdfd, #e6f7f7);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 60px;
  color: #0b8f8f;
}

.product-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(11,143,143,0.9);
  display: flex;
  align-items: center;
  justify-content: center;
  opacity: 0;
  transition: opacity 0.3s ease;
}

.product-card:hover .product-overlay {
  opacity: 1;
}

.product-info {
  padding: 25px;
}

.product-category {
  color: #0b8f8f;
  font-size: 12px;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 1px;
  margin-bottom: 8px;
}

.product-title {
  font-weight: 700;
  color: #333;
  margin-bottom: 15px;
  font-size: 18px;
  line-height: 1.3;
}

.product-price {
  font-size: 24px;
  font-weight: 800;
  color: #0b8f8f;
  margin-bottom: 12px;
}

.product-rating {
  font-size: 14px;
}

/* Stats Section */
.stats-section {
  background: linear-gradient(135deg, #0b8f8f 0%, #2db5b0 100%);
  border-radius: 25px;
  padding: 50px 20px;
  color: white;
}

.stat-card {
  padding: 20px;
}

.stat-card i {
  font-size: 50px;
  margin-bottom: 15px;
  color: white !important;
}

.stat-card h3 {
  font-size: 36px;
  font-weight: 800;
  margin-bottom: 8px;
}

.stat-card p {
  font-size: 16px;
  opacity: 0.9;
  margin: 0;
}

/* Animations */
@keyframes float {
  0%, 100% { transform: translateY(0px) rotate(0deg); }
  50% { transform: translateY(-20px) rotate(5deg); }
}

/* Responsive */
@media (max-width: 768px) {
  .hero-section {
    padding: 40px 20px;
    text-align: center;
  }
  
  .categories-grid {
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 15px;
  }
  
  .products-grid {
    grid-template-columns: 1fr;
  }
  
  .hero-content h1 {
    font-size: 2.5rem;
  }
}
</style>

<!-- AOS Animation Library -->
<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

<script>
// Initialize AOS animations
AOS.init({
  duration: 800,
  once: true,
  offset: 100
});

// Add to cart functionality with enhanced feedback
document.querySelectorAll('.btn-add-cart').forEach(btn => {
  btn.onclick = function() {
    let id = this.dataset.id;
    let originalText = this.innerHTML;
    
    // Loading state
    this.innerHTML = '<i class="bi bi-arrow-repeat spin"></i> Agregando...';
    this.disabled = true;
    
    fetch('<?= $BASE ?>/agregar-carrito?id='+id, {headers:{'X-Requested-With':'XMLHttpRequest'}})
      .then(r=>r.json())
      .then(data=>{
        if(data.ok) {
          // Update cart count
          document.querySelectorAll('.cart-badge').forEach(e => e.textContent = data.cart_count);
          
          // Success state
          this.innerHTML = '<i class="bi bi-check-circle-fill"></i> ¡Agregado!';
          this.classList.remove('btn-primary');
          this.classList.add('btn-success');
          
          // Reset after 2 seconds
          setTimeout(()=>{
            this.innerHTML = originalText;
            this.classList.remove('btn-success');
            this.classList.add('btn-primary');
            this.disabled = false;
          }, 2000);
        }
      })
      .catch(() => {
        // Error state
        this.innerHTML = '<i class="bi bi-exclamation-circle"></i> Error';
        this.classList.add('btn-danger');
        setTimeout(()=>{
          this.innerHTML = originalText;
          this.classList.remove('btn-danger');
          this.disabled = false;
        }, 2000);
      });
  }
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
</script>
</body>
</html>
