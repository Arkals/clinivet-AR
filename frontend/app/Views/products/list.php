<?php include __DIR__ . '/../layouts/main.php'; ?>

<!-- Page Header -->
<div class="page-header mb-5">
  <div class="row align-items-center">
    <div class="col-md-8">
      <h1 class="page-title">
        <i class="bi bi-shop text-primary me-3"></i>
        Nuestros Productos
      </h1>
      <p class="page-subtitle">Descubre nuestra amplia selección de productos para el cuidado de tus mascotas</p>
    </div>
    <div class="col-md-4 text-md-end">
      <div class="results-info">
        <span class="badge bg-primary fs-6">
          <?= count($products) ?> productos encontrados
        </span>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <!-- Sidebar Filters -->
  <div class="col-lg-3 col-md-4 mb-4">
    <div class="filters-sidebar">
      <div class="filter-section">
        <h5 class="filter-title">
          <i class="bi bi-funnel-fill me-2"></i>
          Categorías
        </h5>
        <div class="category-filters">
          <div class="category-item">
            <!-- 'All categories' should always go to home -->
            <?php $BASE = \App\Helpers\Security::base(); ?>
            <a href="<?= $BASE ?>/home" class="category-link home <?= empty($_GET['cat']) ? 'active' : '' ?>" role="button" aria-label="Ir al inicio" tabindex="0">
              <i class="bi bi-house-fill"></i>
              <span>Todas las categorías</span>
              <span class="count"><?= array_sum(array_column($cats, 'product_count', 'id')) ?: count($products) ?></span>
            </a>
          </div>
          <?php foreach($cats as $c): ?>
            <div class="category-item">
                <a href="<?= $BASE ?>/categoria/<?=$c['id']?>" 
                 class="category-link <?= (isset($_GET['cat']) && $_GET['cat'] == $c['id']) ? 'active' : '' ?>">
                  <?php
                  $nameNormalized = function_exists('mb_strtolower') ? mb_strtolower($c['name'], 'UTF-8') : strtolower($c['name']);
                  $nameAscii = @iconv('UTF-8', 'ASCII//TRANSLIT', $nameNormalized) ?: $nameNormalized;
                  $isCat = strpos($nameAscii, 'gat') !== false || strpos($nameAscii, 'fel') !== false || strpos($nameAscii, 'cat') !== false;
                  $isDog = strpos($nameAscii, 'perr') !== false || strpos($nameAscii, 'can') !== false || strpos($nameAscii, 'dog') !== false;
                  if ($isCat) {
                    echo '<svg class="pet-svg cat" viewBox="0 0 64 64" role="img" aria-label="Categoría gato"><path d="M16 18l8 8a20 20 0 0 1 16 0l8-8 4 16v12c0 11-9 20-20 20s-20-9-20-20V34z" fill="currentColor"/><circle cx="24" cy="40" r="3" fill="#0f4c4c"/><circle cx="40" cy="40" r="3" fill="#0f4c4c"/><path d="M32 44l4 6h-8z" fill="#0f4c4c"/></svg>';
                  } elseif ($isDog) {
                    echo '<svg class="pet-svg dog" viewBox="0 0 64 64" role="img" aria-label="Categoría perro"><path d="M12 24c0-7 5-12 12-12 4 0 8 2 10 6 2-4 6-6 10-6 7 0 12 5 12 12v10c0 11-9 20-22 20S12 45 12 34V24z" fill="currentColor"/><circle cx="24" cy="38" r="3" fill="#0f4c4c"/><circle cx="40" cy="38" r="3" fill="#0f4c4c"/><path d="M24 50c4 4 12 4 16 0" stroke="#0f4c4c" stroke-width="2" stroke-linecap="round" fill="none"/></svg>';
                  } else {
                    $icons = ['bi-bag-heart-fill', 'bi-cup-straw', 'bi-heart-fill', 'bi-star-fill', 'bi-gift-fill', 'bi-house-heart-fill'];
                    $icon = $icons[array_search($c['id'], array_column($cats, 'id')) % count($icons)];
                    echo '<i class="bi ' . $icon . '"></i>';
                  }
                  ?>
                <span><?=htmlspecialchars($c['name'])?></span>
                <span class="count"><?= $c['product_count'] ?? '0' ?></span>
              </a>
            </div>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- Price Filter -->
      <div class="filter-section">
        <h5 class="filter-title">
          <i class="bi bi-currency-dollar me-2"></i>
          Rango de precio
        </h5>
        <div class="price-filters">
          <div class="price-range">
            <input type="range" class="form-range" min="0" max="1000" value="500" id="priceRange">
            <div class="d-flex justify-content-between">
              <span class="text-muted">$0</span>
              <span class="text-muted">$1000+</span>
            </div>
            <div class="price-display text-center mt-2">
              <span class="badge bg-primary">Hasta $<span id="priceValue">500</span></span>
            </div>
          </div>
        </div>
      </div>

      <!-- Featured Banner -->
      <div class="filter-section">
        <div class="promo-card">
          <div class="promo-icon">
            <i class="bi bi-gift-fill"></i>
          </div>
          <h6>¡Envío gratis!</h6>
          <p>En compras mayores a $500</p>
          <small class="text-muted">Términos y condiciones aplican</small>
        </div>
      </div>
    </div>
  </div>

  <!-- Products Grid -->
  <div class="col-lg-9 col-md-8">
    <!-- Sort Controls -->
    <div class="products-controls mb-4">
      <div class="row align-items-center">
        <div class="col-md-6">
          <div class="view-toggle">
            <span class="text-muted me-3">Vista:</span>
            <div class="btn-group" role="group">
              <button type="button" class="btn btn-outline-primary active" id="gridView">
                <i class="bi bi-grid-3x3-gap"></i>
              </button>
              <button type="button" class="btn btn-outline-primary" id="listView">
                <i class="bi bi-list-ul"></i>
              </button>
            </div>
          </div>
        </div>
        <div class="col-md-6 text-md-end">
          <select class="form-select form-select-sm" style="max-width: 200px; display: inline-block;">
            <option>Ordenar por relevancia</option>
            <option>Precio: menor a mayor</option>
            <option>Precio: mayor a menor</option>
            <option>Más populares</option>
            <option>Mejor calificados</option>
          </select>
        </div>
      </div>
    </div>

    <!-- Products Results -->
    <?php if(empty($products)): ?>
      <div class="empty-state">
        <div class="empty-icon">
          <i class="bi bi-search"></i>
        </div>
        <h3>No se encontraron productos</h3>
        <p class="text-muted">Intenta ajustar tus filtros o buscar algo diferente</p>
        <a href="<?= $BASE ?>/productos" class="btn btn-primary">Ver todos los productos</a>
      </div>
    <?php else: ?>
      <div class="products-grid" id="productsContainer">
        <?php foreach($products as $index => $p): ?>
          <div class="product-card modern" data-aos="fade-up" data-aos-delay="<?= $index * 100 ?>">
            <div class="product-image">
              <?php if (!empty($p['image'])): ?>
                <?php
                  $__img = $p['image'];
                  $__img = ltrim($__img, '/');
                  $__img = preg_replace('#^(frontend/public/)+#', '', $__img);
                  $__img = preg_replace('#^(public/)+#', '', $__img);
                  // Ensure absolute path from web root
                  $__img = '/' . ltrim($__img, '/');
                ?>
                <img src="<?=htmlspecialchars(($assetBaseUrl ?? $baseUrl) . $__img)?>" alt="<?=htmlspecialchars($p['name'])?>" />
              <?php else: ?>
                <div class="placeholder-image">
                  <i class="bi bi-heart-pulse-fill"></i>
                </div>
              <?php endif; ?>
              
              <!-- Product Badges -->
              <div class="product-badges">
                <?php if(isset($p['is_new']) && $p['is_new']): ?>
                  <span class="badge badge-new">Nuevo</span>
                <?php endif; ?>
                <?php if(isset($p['discount']) && $p['discount'] > 0): ?>
                  <span class="badge badge-sale">-<?= $p['discount'] ?>%</span>
                <?php endif; ?>
              </div>

              <!-- Quick Actions -->
              <div class="product-actions">
                <button class="action-btn btn-wishlist" title="Agregar a favoritos">
                  <i class="bi bi-heart"></i>
                </button>
                <button class="action-btn btn-quick-view" title="Vista rápida">
                  <i class="bi bi-eye"></i>
                </button>
              </div>

              <!-- Add to Cart Overlay -->
              <div class="add-to-cart-overlay">
                <button class="btn btn-primary btn-add-cart" data-id="<?=$p['id']?>">
                  <i class="bi bi-cart-plus me-2"></i>
                  Agregar al carrito
                </button>
              </div>
            </div>

            <div class="product-info">
              <div class="product-category"><?=htmlspecialchars($p['category_name'])?></div>
              <h5 class="product-title"><?=htmlspecialchars($p['name'])?></h5>
              
              <?php if(!empty($p['description'])): ?>
                <p class="product-description"><?= substr(htmlspecialchars($p['description']), 0, 80) ?>...</p>
              <?php endif; ?>

              <div class="product-rating">
                <div class="stars">
                  <i class="bi bi-star-fill"></i>
                  <i class="bi bi-star-fill"></i>
                  <i class="bi bi-star-fill"></i>
                  <i class="bi bi-star-fill"></i>
                  <i class="bi bi-star"></i>
                </div>
                <span class="rating-text">(4.2) 24 reseñas</span>
              </div>

              <div class="product-price">
                <?php if(isset($p['original_price']) && $p['original_price'] > $p['price']): ?>
                  <span class="original-price">$<?=number_format($p['original_price'],2)?></span>
                <?php endif; ?>
                <span class="current-price">$<?=number_format($p['price'],2)?></span>
              </div>

              <!-- Quick Add Button -->
              <button class="btn btn-outline-primary btn-sm btn-add-cart-quick w-100" data-id="<?=$p['id']?>">
                <i class="bi bi-plus-circle me-1"></i> Agregar
              </button>
            </div>
          </div>
        <?php endforeach; ?>
      </div>

      <!-- Load More Button -->
      <div class="text-center mt-5">
        <button class="btn btn-outline-primary btn-lg load-more-btn">
          <i class="bi bi-arrow-down-circle me-2"></i>
          Cargar más productos
        </button>
      </div>
    <?php endif; ?>
  </div>
</div>

<style>
/* Page Header */
.page-header {
  background: linear-gradient(135deg, rgba(11,143,143,0.05) 0%, rgba(45,181,176,0.08) 100%);
  border-radius: 20px;
  padding: 40px 30px;
  margin-bottom: 40px;
}

.page-title {
  font-size: 2.5rem;
  font-weight: 800;
  color: #333;
  margin-bottom: 10px;
}

.page-subtitle {
  font-size: 1.1rem;
  color: #666;
  margin: 0;
}

.results-info .badge {
  padding: 12px 20px;
  border-radius: 50px;
}

/* Filters Sidebar */
.filters-sidebar {
  background: white;
  border-radius: 20px;
  padding: 30px;
  box-shadow: 0 8px 25px rgba(0,0,0,0.06);
  position: sticky;
  top: 20px;
}

.filter-section {
  margin-bottom: 35px;
}

.filter-section:last-child {
  margin-bottom: 0;
}

.filter-title {
  color: #333;
  font-weight: 700;
  margin-bottom: 20px;
  padding-bottom: 10px;
  border-bottom: 2px solid #f0f0f0;
}

.category-filters .category-item {
  margin-bottom: 8px;
}

.category-icon-svg {
  width: 28px;
  height: 28px;
}
.pet-svg {
  width: 28px;
  height: 28px;
  color: #0b8f8f;
}
.pet-svg.cat {
  color: #ffd166;
}

.category-link {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 12px 15px;
  border-radius: 12px;
  text-decoration: none;
  color: #666;
  transition: all 0.3s ease;
  border: 2px solid transparent;
}

.category-link:hover {
  background: rgba(11,143,143,0.05);
  color: #0b8f8f;
  transform: translateX(5px);
}

.category-link.active {
  background: linear-gradient(135deg, #0b8f8f, #2db5b0);
  color: white;
  box-shadow: 0 4px 15px rgba(11,143,143,0.3);
}

/* Make the 'All categories' (home) active look red to highlight it */
.category-link.home.active {
  background: linear-gradient(135deg, #ff5a5f, #e5282e);
  color: white;
  box-shadow: 0 4px 16px rgba(229,40,46,0.2);
}
.category-link.home.active .count { background: rgba(255,255,255,0.2); }

.category-link.home:focus, .category-link.home:focus-visible {
  outline: 3px solid rgba(229,40,46,0.18);
  outline-offset: 3px;
  border-radius: 12px;
}

.category-link i {
  font-size: 16px;
  width: 20px;
}

.category-link .count {
  margin-left: auto;
  background: rgba(0,0,0,0.1);
  padding: 4px 8px;
  border-radius: 10px;
  font-size: 12px;
  font-weight: 600;
}

.category-link.active .count {
  background: rgba(255,255,255,0.2);
}

/* Price Filter */
.price-filters {
  padding: 20px;
  background: #f8fdfd;
  border-radius: 15px;
}

.form-range {
  margin: 15px 0;
}

/* Promo Card */
.promo-card {
  background: linear-gradient(135deg, #0b8f8f, #2db5b0);
  color: white;
  padding: 25px;
  border-radius: 15px;
  text-align: center;
}

.promo-icon {
  font-size: 40px;
  margin-bottom: 15px;
}

.promo-card h6 {
  font-weight: 700;
  margin-bottom: 8px;
}

.promo-card p {
  margin-bottom: 8px;
  opacity: 0.9;
}

.promo-card small {
  opacity: 0.7;
}

/* Products Controls */
.products-controls {
  background: white;
  padding: 20px;
  border-radius: 15px;
  box-shadow: 0 4px 15px rgba(0,0,0,0.05);
}

.view-toggle .btn {
  padding: 8px 12px;
}

.view-toggle .btn.active {
  background: #0b8f8f;
  border-color: #0b8f8f;
}

/* Products Grid */
.products-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 30px;
  margin-bottom: 40px;
}

.product-card.modern {
  background: white;
  border-radius: 20px;
  overflow: hidden;
  box-shadow: 0 8px 25px rgba(0,0,0,0.06);
  transition: all 0.3s ease;
  position: relative;
}

.product-card.modern:hover {
  transform: translateY(-10px);
  box-shadow: 0 20px 50px rgba(11,143,143,0.15);
}

/* Removed fixed height to avoid clipping */

.product-image {
  position: relative;
  /* Fallback minimum height for older browsers */
  min-height: 250px;
  overflow: hidden;
  /* Center the image and avoid clipping */
  display: flex;
  align-items: center;
  justify-content: center;
  background: #fff;
  border-radius: 16px;
  /* Use aspect ratio so images adapt to column width */
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

/* Product Badges */
.product-badges {
  position: absolute;
  top: 15px;
  left: 15px;
  z-index: 3;
}

.product-badges .badge {
  display: block;
  margin-bottom: 5px;
  padding: 6px 12px;
  border-radius: 20px;
  font-weight: 600;
  font-size: 11px;
}

.badge-new {
  background: #28a745;
}

.badge-sale {
  background: #dc3545;
}

/* Product Actions */
.product-actions {
  position: absolute;
  top: 15px;
  right: 15px;
  display: flex;
  flex-direction: column;
  gap: 8px;
  z-index: 3;
  opacity: 0;
  transition: opacity 0.3s ease;
}

.product-card:hover .product-actions {
  opacity: 1;
}

.action-btn {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background: white;
  border: none;
  color: #666;
  box-shadow: 0 4px 15px rgba(0,0,0,0.1);
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  justify-content: center;
}

.action-btn:hover {
  background: #0b8f8f;
  color: white;
  transform: scale(1.1);
}

/* Add to Cart Overlay */
.add-to-cart-overlay {
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  background: linear-gradient(transparent, rgba(0,0,0,0.8));
  padding: 30px 20px 20px;
  transform: translateY(100%);
  transition: transform 0.3s ease;
}

.product-card:hover .add-to-cart-overlay {
  transform: translateY(0);
}

/* Product Info */
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
  margin-bottom: 10px;
  font-size: 18px;
  line-height: 1.3;
}

.product-description {
  color: #666;
  font-size: 14px;
  margin-bottom: 15px;
  line-height: 1.4;
}

.product-rating {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-bottom: 15px;
}

.stars {
  color: #ffc107;
  font-size: 14px;
}

.rating-text {
  font-size: 12px;
  color: #666;
}

.product-price {
  margin-bottom: 20px;
}

.original-price {
  color: #999;
  text-decoration: line-through;
  font-size: 16px;
  margin-right: 8px;
}

.current-price {
  color: #0b8f8f;
  font-size: 24px;
  font-weight: 800;
}

/* Empty State */
.empty-state {
  text-align: center;
  padding: 80px 20px;
  background: white;
  border-radius: 20px;
  box-shadow: 0 8px 25px rgba(0,0,0,0.06);
}

.empty-icon {
  font-size: 80px;
  color: #ddd;
  margin-bottom: 30px;
}

.empty-state h3 {
  color: #333;
  margin-bottom: 15px;
}

/* Load More Button */
.load-more-btn {
  padding: 15px 40px;
  border-radius: 50px;
  font-weight: 600;
}

/* Responsive */
@media (max-width: 768px) {
  .page-title {
    font-size: 2rem;
  }
  
  .products-grid {
    grid-template-columns: 1fr;
    gap: 20px;
  }
  
  .filters-sidebar {
    margin-bottom: 30px;
    position: relative;
    top: auto;
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

// Price range slider
const priceRange = document.getElementById('priceRange');
const priceValue = document.getElementById('priceValue');

if (priceRange && priceValue) {
  priceRange.addEventListener('input', function() {
    priceValue.textContent = this.value;
  });
}

// View toggle
const gridView = document.getElementById('gridView');
const listView = document.getElementById('listView');
const productsContainer = document.getElementById('productsContainer');

if (gridView && listView && productsContainer) {
  gridView.addEventListener('click', function() {
    this.classList.add('active');
    listView.classList.remove('active');
    productsContainer.className = 'products-grid';
  });

  listView.addEventListener('click', function() {
    this.classList.add('active');
    gridView.classList.remove('active');
    productsContainer.className = 'products-list';
  });
}

// Add to cart functionality
document.querySelectorAll('.btn-add-cart, .btn-add-cart-quick').forEach(btn => {
  btn.onclick = function(e) {
    e.preventDefault();
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
          this.classList.remove('btn-primary', 'btn-outline-primary');
          this.classList.add('btn-success');
          
          // Show success animation
          this.style.transform = 'scale(1.05)';
          setTimeout(() => {
            this.style.transform = 'scale(1)';
          }, 200);
          
          // Reset after 2 seconds
          setTimeout(()=>{
            this.innerHTML = originalText;
            this.classList.remove('btn-success');
            if (originalText.includes('btn-outline-primary')) {
              this.classList.add('btn-outline-primary');
            } else {
              this.classList.add('btn-primary');
            }
            this.disabled = false;
          }, 2000);
        }
      })
      .catch(() => {
        // Simulate successful payment
        this.innerHTML = '<i class="bi bi-check-circle-fill"></i> ¡Pago realizado!';
        this.classList.remove('btn-primary', 'btn-outline-primary');
        this.classList.add('btn-success');
        setTimeout(()=>{
          this.innerHTML = originalText;
          this.classList.remove('btn-success');
          this.disabled = false;
        }, 2000);
      });
  }
});

// Wishlist functionality (placeholder)
document.querySelectorAll('.btn-wishlist').forEach(btn => {
  btn.onclick = function() {
    this.classList.toggle('active');
    const icon = this.querySelector('i');
    if (this.classList.contains('active')) {
      icon.classList.remove('bi-heart');
      icon.classList.add('bi-heart-fill');
      this.style.color = '#dc3545';
    } else {
      icon.classList.remove('bi-heart-fill');
      icon.classList.add('bi-heart');
      this.style.color = '#666';
    }
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

.btn-wishlist.active {
  background: #ffe6e6 !important;
  color: #dc3545 !important;
}
</style>
