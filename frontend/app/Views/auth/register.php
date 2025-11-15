<?php include __DIR__ . '/../layouts/main.php'; ?>

<div class="auth-container">
  <div class="row justify-content-center align-items-center min-vh-100">
    <div class="col-xl-5 col-lg-6 col-md-7 col-sm-9">
      <div class="auth-card" data-aos="fade-up">
        <!-- Header -->
        <div class="auth-header">
          <div class="auth-logo">
            <i class="bi bi-person-plus-fill"></i>
          </div>
          <h2 class="auth-title">Únete a Clinivet</h2>
          <p class="auth-subtitle">Crea tu cuenta y cuida mejor a tu mascota</p>
        </div>

        <!-- Error Alert -->
        <?php if (!empty($error)): ?>
          <div class="alert alert-danger alert-modern" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <?=htmlspecialchars($error)?>
          </div>
        <?php endif; ?>

        <!-- Registration Form -->
        <form method="post" class="auth-form" id="registerForm">
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label class="form-label">
                  <i class="bi bi-person me-2"></i>
                  Nombre completo
                </label>
                <div class="input-wrapper">
                  <input 
                    type="text" 
                    class="form-control modern-input" 
                    name="name" 
                    placeholder="Tu nombre completo"
                    required
                  >
                  <div class="input-focus-border"></div>
                </div>
              </div>
            </div>
            
            <div class="col-md-6">
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
                  >
                  <div class="input-focus-border"></div>
                </div>
              </div>
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
                placeholder="Mínimo 8 caracteres"
                required
                minlength="8"
              >
              <button type="button" class="password-toggle" onclick="togglePassword(this)">
                <i class="bi bi-eye"></i>
              </button>
              <div class="input-focus-border"></div>
            </div>
            <div class="password-strength">
              <div class="strength-bar">
                <div class="strength-fill"></div>
              </div>
              <small class="strength-text">La contraseña debe tener al menos 8 caracteres</small>
            </div>
          </div>

          <div class="form-group">
            <label class="form-label">
              <i class="bi bi-receipt me-2"></i>
              RFC (opcional)
              <span class="badge bg-info ms-2">Para facturación</span>
            </label>
            <div class="input-wrapper">
              <input 
                type="text" 
                class="form-control modern-input" 
                name="rfc" 
                placeholder="ABCD123456XXX"
                maxlength="13"
                style="text-transform: uppercase;"
              >
              <div class="input-focus-border"></div>
            </div>
            <small class="form-text text-muted">
              <i class="bi bi-info-circle me-1"></i>
              El RFC es necesario solo si requieres facturación fiscal
            </small>
          </div>

          <!-- Terms and Conditions -->
          <div class="form-group">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="terms" required>
              <label class="form-check-label" for="terms">
                Acepto los <a href="#" class="terms-link">términos y condiciones</a> 
                y la <a href="#" class="terms-link">política de privacidad</a>
              </label>
            </div>
          </div>

          <!-- Marketing Consent -->
          <div class="form-group">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="marketing">
              <label class="form-check-label" for="marketing">
                Quiero recibir ofertas especiales y noticias sobre productos
              </label>
            </div>
          </div>

          <button type="submit" class="btn btn-primary btn-modern">
            <span class="btn-text">
              <i class="bi bi-person-plus me-2"></i>
              Crear mi cuenta
            </span>
            <span class="btn-loading">
              <i class="bi bi-arrow-repeat spin"></i>
              Creando cuenta...
            </span>
          </button>
        </form>

        <!-- Social Registration -->
        <div class="social-login">
          <div class="divider">
            <span>o regístrate con</span>
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
            $loginUrl = $BASE . '/iniciar-sesion' . ($redirect ? '?redirect=' . urlencode($redirect) : '');
          ?>
          <p>¿Ya tienes cuenta? 
            <a href="<?=htmlspecialchars($loginUrl)?>" class="auth-link">
              Inicia sesión aquí
            </a>
          </p>
        </div>
      </div>

      <!-- Benefits Section -->
      <div class="benefits-section">
        <div class="row text-center">
          <div class="col-4">
            <div class="benefit-item">
              <i class="bi bi-truck"></i>
              <span>Envío gratis</span>
            </div>
          </div>
          <div class="col-4">
            <div class="benefit-item">
              <i class="bi bi-award"></i>
              <span>Garantía</span>
            </div>
          </div>
          <div class="col-4">
            <div class="benefit-item">
              <i class="bi bi-headset"></i>
              <span>Soporte 24/7</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
/* Extend auth styles from login page */
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

.auth-header {
  text-align: center;
  margin-bottom: 40px;
}

.auth-logo {
  width: 80px;
  height: 80px;
  background: linear-gradient(135deg, #28a745, #20c997);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 25px;
  font-size: 35px;
  color: white;
  box-shadow: 0 10px 30px rgba(40,167,69,0.3);
  animation: logoFloat 6s ease-in-out infinite;
}

.auth-title {
  font-size: 2rem;
  font-weight: 800;
  color: #333;
  margin-bottom: 10px;
  background: linear-gradient(135deg, #333, #28a745);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

.auth-subtitle {
  color: #666;
  font-size: 1rem;
  margin: 0;
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
  width: 100%;
}

.modern-input:focus {
  border-color: #28a745;
  box-shadow: 0 0 0 3px rgba(40,167,69,0.1);
  background: white;
  outline: none;
}

.input-focus-border {
  position: absolute;
  bottom: 0;
  left: 0;
  width: 0;
  height: 3px;
  background: linear-gradient(90deg, #28a745, #20c997);
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
  z-index: 2;
}

.password-toggle:hover {
  background: rgba(40,167,69,0.1);
  color: #28a745;
}

/* Password Strength */
.password-strength {
  margin-top: 10px;
}

.strength-bar {
  width: 100%;
  height: 4px;
  background: #e9ecef;
  border-radius: 2px;
  overflow: hidden;
  margin-bottom: 5px;
}

.strength-fill {
  height: 100%;
  width: 0%;
  background: linear-gradient(90deg, #dc3545, #ffc107, #28a745);
  transition: width 0.3s ease;
  border-radius: 2px;
}

.strength-text {
  color: #666;
  font-size: 0.875rem;
}

/* Terms Link */
.terms-link {
  color: #0b8f8f;
  text-decoration: none;
  font-weight: 600;
}

.terms-link:hover {
  text-decoration: underline;
}

/* Form Check */
.form-check-input:checked {
  background-color: #28a745;
  border-color: #28a745;
}

/* Modern Button */
.btn-modern {
  width: 100%;
  padding: 15px;
  font-size: 16px;
  font-weight: 700;
  border-radius: 15px;
  border: none;
  background: linear-gradient(135deg, #28a745, #20c997);
  color: white;
  position: relative;
  overflow: hidden;
  transition: all 0.3s ease;
  box-shadow: 0 8px 25px rgba(40,167,69,0.3);
}

.btn-modern:hover {
  transform: translateY(-2px);
  box-shadow: 0 12px 35px rgba(40,167,69,0.4);
  background: linear-gradient(135deg, #218838, #1aa179);
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

/* Benefits Section */
.benefits-section {
  margin-top: 30px;
  background: rgba(255,255,255,0.1);
  border-radius: 20px;
  padding: 20px;
  backdrop-filter: blur(10px);
}

.benefit-item {
  color: rgba(255,255,255,0.9);
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 8px;
  font-size: 14px;
  font-weight: 600;
}

.benefit-item i {
  font-size: 24px;
  margin-bottom: 5px;
}

/* Alert */
.alert-modern {
  border: none;
  border-radius: 15px;
  padding: 15px 20px;
  margin-bottom: 30px;
  background: linear-gradient(135deg, #f8d7da, #f5c2c7);
  color: #721c24;
  box-shadow: 0 4px 15px rgba(220,53,69,0.1);
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
  
  .benefits-section .row {
    gap: 15px;
  }
}

/* Animations */
@keyframes backgroundFloat {
  0%, 100% { transform: translateY(0px) rotate(0deg); }
  50% { transform: translateY(-20px) rotate(5deg); }
}

@keyframes logoFloat {
  0%, 100% { transform: translateY(0px) scale(1); }
  50% { transform: translateY(-10px) scale(1.05); }
}

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

// Password strength checker
document.querySelector('input[name="password"]').addEventListener('input', function() {
  const password = this.value;
  const strengthBar = document.querySelector('.strength-fill');
  const strengthText = document.querySelector('.strength-text');
  
  let strength = 0;
  let message = '';
  
  if (password.length >= 8) strength += 25;
  if (/[a-z]/.test(password)) strength += 25;
  if (/[A-Z]/.test(password)) strength += 25;
  if (/[0-9]/.test(password)) strength += 25;
  
  strengthBar.style.width = strength + '%';
  
  if (strength < 50) {
    message = 'Contraseña débil';
    strengthBar.style.background = '#dc3545';
  } else if (strength < 75) {
    message = 'Contraseña media';
    strengthBar.style.background = '#ffc107';
  } else {
    message = 'Contraseña fuerte';
    strengthBar.style.background = '#28a745';
  }
  
  strengthText.textContent = message;
});

// RFC formatter
document.querySelector('input[name="rfc"]').addEventListener('input', function() {
  this.value = this.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
});

// Form submission with loading state
document.getElementById('registerForm').addEventListener('submit', function(e) {
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

// Social registration handlers (placeholder)
document.querySelectorAll('.btn-social').forEach(btn => {
  btn.addEventListener('click', function(e) {
    e.preventDefault();
    const provider = this.classList.contains('btn-google') ? 'Google' : 'Facebook';
    alert(`Registro con ${provider} estará disponible próximamente`);
  });
});

// Terms and conditions link handlers
document.querySelectorAll('.terms-link').forEach(link => {
  link.addEventListener('click', function(e) {
    e.preventDefault();
    alert('Los términos y condiciones se abrirán en una nueva ventana próximamente');
  });
});
</script>
