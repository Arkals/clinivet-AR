<?php include __DIR__ . '/../layouts/main.php'; ?>
<?php $csrf = \App\Helpers\Security::generateCSRFToken(); ?>

<div class="auth-container">
  <div class="row justify-content-center align-items-center min-vh-100">
    <div class="col-xl-4 col-lg-5 col-md-6 col-sm-8">
      <div class="auth-card" data-aos="fade-up">
        <!-- Header -->
        <div class="auth-header">
          <div class="auth-logo">
            <i class="bi bi-heart-pulse-fill"></i>
          </div>
          <h2 class="auth-title">Bienvenido de nuevo</h2>
          <p class="auth-subtitle">Inicia sesión en tu cuenta de Clinivet</p>
        </div>

        <!-- Error Alert -->
        <?php if (!empty($error)): ?>
          <div class="alert alert-danger alert-modern" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <?=htmlspecialchars($error)?>
          </div>
        <?php endif; ?>

        <!-- Login Form -->
        <form method="post" class="auth-form" autocomplete="on">
          <input type="hidden" name="csrf_token" value="<?=htmlspecialchars($csrf)?>">
          <div class="form-group">
            <label class="form-label">
              <i class="bi bi-envelope me-2"></i>
              Correo electrónico
            </label>
            <div class="input-wrapper">
              <input 
                type="email" 
                class="form-control modern-input" 
                name="email" 
                placeholder="tu@email.com"
                required
                autocomplete="username email" autocapitalize="off" spellcheck="false"
              >
              <div class="input-focus-border"></div>
            </div>
          </div>

          <div class="form-group">
            <label class="form-label">
              <i class="bi bi-lock me-2"></i>
              Contraseña
            </label>
            <div class="input-wrapper">
              <input 
                type="password" 
                class="form-control modern-input" 
                name="password" 
                placeholder="Tu contraseña"
                required
                autocomplete="current-password" autocapitalize="off" spellcheck="false"
              >
              <button type="button" class="password-toggle" onclick="togglePassword(this)">
                <i class="bi bi-eye"></i>
              </button>
              <div class="input-focus-border"></div>
            </div>
          </div>

          <div class="form-options">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="remember">
              <label class="form-check-label" for="remember">
                Recordarme
              </label>
            </div>
            <a href="#" class="forgot-password">¿Olvidaste tu contraseña?</a>
          </div>

          <button type="submit" class="btn btn-primary btn-modern">
            <span class="btn-text">Iniciar sesión</span>
            <span class="btn-loading">
              <i class="bi bi-arrow-repeat spin"></i>
              Iniciando...
            </span>
          </button>
        </form>

        <!-- Social Login -->
        <div class="social-login">
          <div class="divider">
            <span>o continúa con</span>
          </div>
          <div class="social-buttons">
            <button class="btn btn-social btn-google">
              <i class="bi bi-google"></i>
              Google
            </button>
            <button class="btn btn-social btn-facebook">
              <i class="bi bi-facebook"></i>
              Facebook
            </button>
          </div>
        </div>

        <!-- Footer -->
        <div class="auth-footer">
          <?php
            $redirect = isset($_GET['redirect']) ? urlencode($_GET['redirect']) : '';
            $BASE = \App\Helpers\Security::base();
            $regUrl = $BASE . '/registro' . ($redirect ? '?redirect=' . urlencode($redirect) : '');
          ?>
          <p>¿No tienes cuenta? 
            <a href="<?=htmlspecialchars($regUrl)?>" class="auth-link">
              Regístrate aquí
            </a>
          </p>
        </div>
      </div>

      <!-- Security Badge -->
      <div class="security-badge">
        <i class="bi bi-shield-fill-check me-2"></i>
        Tu información está protegida con encriptación SSL
      </div>
    </div>
  </div>
</div>

<style>
/* Auth Container */
.auth-container {
  min-height: 100vh;
  background: linear-gradient(135deg, #0b8f8f 0%, #2db5b0 50%, #4dc4bf 100%);
  position: relative;
  overflow: hidden;
}

.auth-container::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: 
    radial-gradient(circle at 20% 50%, rgba(255,255,255,0.1) 0%, transparent 50%),
    radial-gradient(circle at 80% 20%, rgba(255,255,255,0.1) 0%, transparent 50%),
    radial-gradient(circle at 40% 80%, rgba(255,255,255,0.1) 0%, transparent 50%);
  animation: backgroundFloat 20s ease-in-out infinite;
}

@keyframes backgroundFloat {
  0%, 100% { transform: translateY(0px) rotate(0deg); }
  50% { transform: translateY(-20px) rotate(5deg); }
}

/* Auth Card */
.auth-card {
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(20px);
  border-radius: 30px;
  padding: 40px;
  box-shadow: 
    0 25px 60px rgba(0,0,0,0.1),
    0 0 0 1px rgba(255,255,255,0.2);
  border: 1px solid rgba(255,255,255,0.3);
  position: relative;
  overflow: hidden;
}

.auth-card::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 4px;
  background: linear-gradient(90deg, #0b8f8f, #2db5b0, #4dc4bf);
}

/* Auth Header */
.auth-header {
  text-align: center;
  margin-bottom: 40px;
}

.auth-logo {
  width: 80px;
  height: 80px;
  background: linear-gradient(135deg, #0b8f8f, #2db5b0);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 25px;
  font-size: 35px;
  color: white;
  box-shadow: 0 10px 30px rgba(11,143,143,0.3);
  animation: logoFloat 6s ease-in-out infinite;
}

@keyframes logoFloat {
  0%, 100% { transform: translateY(0px) scale(1); }
  50% { transform: translateY(-10px) scale(1.05); }
}

.auth-title {
  font-size: 2rem;
  font-weight: 800;
  color: #333;
  margin-bottom: 10px;
  background: linear-gradient(135deg, #333, #0b8f8f);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

.auth-subtitle {
  color: #666;
  font-size: 1rem;
  margin: 0;
}

/* Modern Alert */
.alert-modern {
  border: none;
  border-radius: 15px;
  padding: 15px 20px;
  margin-bottom: 30px;
  background: linear-gradient(135deg, #f8d7da, #f5c2c7);
  color: #721c24;
  box-shadow: 0 4px 15px rgba(220,53,69,0.1);
}

/* Form Styles */
.auth-form .form-group {
  margin-bottom: 25px;
}

.form-label {
  font-weight: 600;
  color: #333;
  margin-bottom: 10px;
  display: flex;
  align-items: center;
}

.input-wrapper {
  position: relative;
}

.modern-input {
  border: 2px solid #e9ecef;
  border-radius: 15px;
  padding: 15px 20px;
  font-size: 16px;
  transition: all 0.3s ease;
  background: rgba(255,255,255,0.8);
  backdrop-filter: blur(10px);
}

.modern-input:focus {
  border-color: #0b8f8f;
  box-shadow: 0 0 0 3px rgba(11,143,143,0.1);
  background: white;
  outline: none;
}

.input-focus-border {
  position: absolute;
  bottom: 0;
  left: 0;
  width: 0;
  height: 3px;
  background: linear-gradient(90deg, #0b8f8f, #2db5b0);
  transition: width 0.3s ease;
  border-radius: 0 0 15px 15px;
}

.modern-input:focus + .password-toggle + .input-focus-border,
.modern-input:focus + .input-focus-border {
  width: 100%;
}

.password-toggle {
  position: absolute;
  right: 15px;
  top: 50%;
  transform: translateY(-50%);
  background: none;
  border: none;
  color: #666;
  cursor: pointer;
  padding: 5px;
  border-radius: 50%;
  transition: all 0.3s ease;
}

.password-toggle:hover {
  background: rgba(11,143,143,0.1);
  color: #0b8f8f;
}

/* Form Options */
.form-options {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 30px;
}

.form-check-input:checked {
  background-color: #0b8f8f;
  border-color: #0b8f8f;
}

.forgot-password {
  color: #0b8f8f;
  text-decoration: none;
  font-weight: 600;
  transition: color 0.3s ease;
}

.forgot-password:hover {
  color: #086f6f;
}

/* Modern Button */
.btn-modern {
  width: 100%;
  padding: 15px;
  font-size: 16px;
  font-weight: 700;
  border-radius: 15px;
  border: none;
  background: linear-gradient(135deg, #0b8f8f, #2db5b0);
  color: white;
  position: relative;
  overflow: hidden;
  transition: all 0.3s ease;
  box-shadow: 0 8px 25px rgba(11,143,143,0.3);
}

.btn-modern:hover {
  transform: translateY(-2px);
  box-shadow: 0 12px 35px rgba(11,143,143,0.4);
  background: linear-gradient(135deg, #086f6f, #25a3a8);
}

.btn-modern:active {
  transform: translateY(0);
}

.btn-loading {
  display: none;
}

.btn-modern.loading .btn-text {
  display: none;
}

.btn-modern.loading .btn-loading {
  display: inline;
}

/* Social Login */
.social-login {
  margin: 30px 0;
}

.divider {
  text-align: center;
  margin-bottom: 20px;
  position: relative;
}

.divider::before {
  content: '';
  position: absolute;
  top: 50%;
  left: 0;
  right: 0;
  height: 1px;
  background: #e9ecef;
}

.divider span {
  background: rgba(255,255,255,0.95);
  padding: 0 20px;
  color: #666;
  font-size: 14px;
  position: relative;
  z-index: 1;
}

.social-buttons {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 15px;
}

.btn-social {
  padding: 12px;
  border-radius: 12px;
  border: 2px solid #e9ecef;
  background: white;
  color: #666;
  font-weight: 600;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
}

.btn-google:hover {
  border-color: #db4437;
  background: #db4437;
  color: white;
}

.btn-facebook:hover {
  border-color: #4267B2;
  background: #4267B2;
  color: white;
}

/* Auth Footer */
.auth-footer {
  text-align: center;
  margin-top: 30px;
  color: #666;
}

.auth-link {
  color: #0b8f8f;
  text-decoration: none;
  font-weight: 700;
  transition: color 0.3s ease;
}

.auth-link:hover {
  color: #086f6f;
  text-decoration: underline;
}

/* Security Badge */
.security-badge {
  text-align: center;
  margin-top: 20px;
  color: rgba(255,255,255,0.8);
  font-size: 14px;
  display: flex;
  align-items: center;
  justify-content: center;
}

/* Responsive */
@media (max-width: 768px) {
  .auth-card {
    margin: 20px;
    padding: 30px;
    border-radius: 20px;
  }
  
  .auth-title {
    font-size: 1.8rem;
  }
  
  .social-buttons {
    grid-template-columns: 1fr;
  }
}

/* Animations */
.spin {
  animation: spin 1s linear infinite;
}

@keyframes spin {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}
</style>

<!-- AOS Animation Library -->
<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

<script>
// Initialize AOS animations
AOS.init({
  duration: 800,
  once: true
});

// Password toggle functionality
function togglePassword(button) {
  const input = button.parentElement.querySelector('input');
  const icon = button.querySelector('i');
  
  if (input.type === 'password') {
    input.type = 'text';
    icon.classList.remove('bi-eye');
    icon.classList.add('bi-eye-slash');
  } else {
    input.type = 'password';
    icon.classList.remove('bi-eye-slash');
    icon.classList.add('bi-eye');
  }
}

// Form submission with loading state
document.querySelector('.auth-form').addEventListener('submit', function(e) {
  const submitBtn = this.querySelector('.btn-modern');
  submitBtn.classList.add('loading');
  submitBtn.disabled = true;
  
  // Re-enable button after 3 seconds (in case of error)
  setTimeout(() => {
    submitBtn.classList.remove('loading');
    submitBtn.disabled = false;
  }, 3000);
});

// Input focus effects
document.querySelectorAll('.modern-input').forEach(input => {
  input.addEventListener('focus', function() {
    this.parentElement.classList.add('focused');
  });
  
  input.addEventListener('blur', function() {
    this.parentElement.classList.remove('focused');
  });
});

// Social login handlers (placeholder)
document.querySelectorAll('.btn-social').forEach(btn => {
  btn.addEventListener('click', function(e) {
    e.preventDefault();
    const provider = this.classList.contains('btn-google') ? 'Google' : 'Facebook';
    alert(`Funcionalidad de ${provider} estará disponible próximamente`);
  });
});
</script>
