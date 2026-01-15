<?php
use App\Helpers\Security;
$BASE = Security::base();
$baseUrl = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/')), '/');
if ($baseUrl === '/' || $baseUrl === '\\') { $baseUrl = ''; }
$assetBaseUrl = $baseUrl;
if (preg_match('#/backend/public$#', $baseUrl)) {
  $assetBaseUrl = preg_replace('#/backend/public$#', '/frontend/public', $baseUrl);
}
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Clinivet - Tienda para mascotas</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
  <style>
    :root{
      --primary:#0b8f8f; /* teal */
      --primary-dark:#086f6f;
      --accent:#ffd166; /* warm accent */
      --muted:#7b8b8b;
      --card-bg: #ffffff;
      --page-bg: #f6fbfb;
    }
    html,body{height:100%;}
    body { font-family: 'Poppins', system-ui, -apple-system, 'Segoe UI', Roboto, 'Helvetica Neue', Arial; background:var(--page-bg); color:#133; }
    .topbar{ background:linear-gradient(90deg,var(--primary) 0%, #2db5b0 100%); box-shadow:0 4px 14px rgba(11,143,143,0.12); }
    .topbar .container{ display:flex; align-items:center; gap:16px; padding:14px 18px; }
    .brand { color:#fff; font-weight:700; font-size:22px; display:flex; align-items:center; gap:10px; text-decoration:none; cursor:pointer; }
    .brand:focus, .brand:focus-visible { outline: 3px solid rgba(255,255,255,0.18); outline-offset:3px; border-radius:10px; }
    .brand .logo-badge{ background:rgba(255,255,255,0.12); padding:8px; border-radius:10px; display:flex; align-items:center; justify-content:center; }
    .search-bar{ flex:1; max-width:920px; }
    .search-bar .form-control, .search-bar .form-select{ border-radius:10px; }
    .nav-actions{ display:flex; align-items:center; gap:8px; }
    .btn-primary, .btn-success{ background:var(--primary); border:none; color:#fff; border-radius:8px; }
    .btn-primary:hover, .btn-success:hover{ background:var(--primary-dark); }
    .btn-outline-primary{ color:var(--primary); border-color:rgba(11,143,143,0.12); }
    .cart-badge{ background: #fff; color:var(--primary); padding:4px 9px; border-radius:12px; font-weight:600; }
    .content-wrap{ padding:28px 0; }
    .card{ border-radius:12px; box-shadow:0 6px 18px rgba(22,46,46,0.06); }
    .product-card .price{ color:var(--primary); font-weight:700; }
    footer.site-footer{ margin-top:48px; padding:28px 0; background:linear-gradient(180deg, rgba(11,143,143,0.04), transparent); color:var(--muted); }
    .hero-note{ background:linear-gradient(90deg, rgba(11,143,143,0.06), rgba(45,181,176,0.04)); border-left:4px solid var(--primary); padding:12px 16px; border-radius:8px; color:var(--muted); }
    @media(max-width:767px){ .search-bar{ max-width:100%; } .topbar .container{ flex-direction:column; align-items:stretch; gap:10px; } }
  </style>
</head>
<body>
<div class="topbar">
  <div class="container">
    <a class="brand" href="<?= $BASE ?>/home" role="button" aria-label="Ir al inicio - Clinivet">
      <div class="logo-badge"><i class="bi bi-heart-pulse-fill" style="font-size:20px;color:#fff"></i></div>
      Clinivet <small style="opacity:0.9;font-weight:600;margin-left:6px">Tienda y servicios</small>
    </a>
    <div class="search-bar">
      <form method="get" action="<?= $BASE ?>/productos" class="d-flex">
        <input class="form-control me-2" type="search" name="q" placeholder="Buscar productos, p.ej. alimento para perros" aria-label="Buscar" value="<?=htmlspecialchars($_GET['q'] ?? '')?>">
        <select name="cat" class="form-select me-2" style="max-width:180px">
          <option value="">Todas las categorías</option>
          <?php
            $catsLocal = (isset($cats)) ? $cats : [];
            foreach($catsLocal as $c) {
              $sel = (isset($_GET['cat']) && $_GET['cat']==$c['id']) ? 'selected' : '';
              echo "<option value=\"{$c['id']}\" $sel>" . htmlspecialchars($c['name']) . "</option>";
            }
          ?>
        </select>
        <button class="btn btn-primary" type="submit"><i class="bi bi-search"></i></button>
      </form>
    </div>
    <div class="nav-actions ms-auto">
      <?php if (!empty($_SESSION['user'])): ?>
        <div class="d-flex align-items-center gap-2">
          <span style="color:#fff; opacity:0.95; font-weight:600;"><i class="bi bi-person-circle" style="margin-right:6px"></i><?=htmlspecialchars($_SESSION['user']['name'])?></span>
          <a href="<?= $BASE ?>/facturas" class="btn btn-outline-primary btn-sm"> <i class="bi bi-receipt"></i> Mis facturas</a>
          <a href="<?= $BASE ?>/logout" class="btn btn-danger btn-sm">Salir</a>
          <?php if (!empty($_SESSION['user']['is_admin'])): ?>
            <a href="<?= $BASE ?>/admin/productos" class="btn btn-warning btn-sm"><i class="bi bi-gear"></i> Admin</a>
          <?php endif; ?>
        </div>
      <?php else: ?>
        <a href="<?= $BASE ?>/iniciar-sesion" class="btn btn-outline-primary btn-sm"><i class="bi bi-box-arrow-in-right"></i> Iniciar sesión</a>
      <?php endif; ?>
      <a href="<?= $BASE ?>/carrito" class="btn btn-success ms-2">
        <i class="bi bi-cart" style="margin-right:6px"></i>
        <span class="d-none d-sm-inline">Carrito</span>
        <span class="cart-badge ms-2"><?= isset($_SESSION['cart']) ? array_sum($_SESSION['cart']):0 ?></span>
      </a>
    </div>
  </div>
</div>
<div class="container content-wrap">
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

  <!-- Cookie Consent Banner (visibility handled by JS based on cookie) -->
  <div id="cookie-consent" class="cookie-consent" style="display:none">
    <div class="cookie-inner">
      <div class="cookie-text">
        <strong>Usamos cookies necesarias</strong> para el funcionamiento básico del sitio.
        Puedes aceptar estas cookies esenciales o rechazar las no esenciales. Consulta el <a href="<?= $BASE ?>/politica-cookies" class="link-light text-decoration-underline">uso de cookies</a> y el <a href="<?= $BASE ?>/aviso-privacidad" class="link-light text-decoration-underline">aviso de privacidad</a>.
      </div>
      <div class="cookie-actions">
        <button id="cookie-accept" class="btn btn-light btn-sm">Aceptar</button>
        <button id="cookie-reject" class="btn btn-outline-light btn-sm">Rechazar</button>
      </div>
    </div>
    <style>
      .cookie-consent{position:fixed;left:16px;right:16px;bottom:16px;z-index:9999;background:rgba(11,143,143,0.96);color:#fff;border-radius:14px;box-shadow:0 10px 28px rgba(11,143,143,0.25);transition:opacity .25s ease}
      .cookie-consent .cookie-inner{display:flex;gap:16px;align-items:center;justify-content:space-between;padding:14px 18px}
      .cookie-consent .cookie-text{font-size:14px;line-height:1.4}
      .cookie-consent .cookie-actions{display:flex;gap:8px}
      @media(max-width:576px){.cookie-consent .cookie-inner{flex-direction:column;align-items:flex-start}}
    </style>
    <script>
      (function(){
        var hasConsent = document.cookie.indexOf('cookie_consent=') !== -1;
        var el = document.getElementById('cookie-consent');
        if(!hasConsent && el){ el.style.display='block'; el.style.opacity='1'; }
        function hide(){ if(el){ el.style.opacity='0'; setTimeout(function(){ el.style.display='none'; }, 250); } }
        function send(choice){
          // Store UI cookie for fast checks; server sets HttpOnly cookie via endpoint
          document.cookie = 'cookie_consent='+choice+'; max-age='+(365*24*60*60)+'; path=/';
          fetch('<?= $BASE ?>/cookies-consent', {method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body:'choice='+encodeURIComponent(choice)})
            .catch(function(){});
          hide();
        }
        var a=document.getElementById('cookie-accept'); if(a) a.addEventListener('click', function(){send('accept')});
        var r=document.getElementById('cookie-reject'); if(r) r.addEventListener('click', function(){send('reject')});
        })();
      </script>
    </div>

    <!-- Multi-tab session management modal -->
    <div id="sessionTakeoverModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="sessionModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header bg-info text-white">
            <h5 class="modal-title" id="sessionModalLabel">Sesión activa detectada</h5>
          </div>
          <div class="modal-body">
            <p>Ya tienes una sesión activa en otra pestaña. ¿Quieres usar la sesión en esta pestaña?</p>
            <p style="font-size: 0.9rem; color: #666; margin-bottom: 0;">Si continúas aquí, la otra pestaña será cerrada automáticamente.</p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" id="sessionModalCancel" data-dismiss="modal">Mantener otra pestaña</button>
            <button type="button" class="btn btn-primary" id="sessionModalTakeSession">Usar sesión aquí</button>
          </div>
        </div>
      </div>
    </div>

    <script>
      // Multi-tab session management system
      (function(){
        // Configuration
        var CHECK_INTERVAL = 300; // 300ms delay before checking for active tabs
        var RESPONSE_TIMEOUT = 2000; // 2s timeout for waiting responses
        var BASE_URL = '<?= $BASE ?>';
        
        // Generate unique tab ID
        function getOrCreateTabId() {
          var tabId = sessionStorage.getItem('clinivet_tab_id');
          if (!tabId) {
            tabId = 'tab_' + Math.random().toString(36).slice(2, 12) + '_' + Date.now().toString(36);
            try {
              sessionStorage.setItem('clinivet_tab_id', tabId);
            } catch(e) {
              tabId = 'tab_fallback_' + Date.now();
            }
          }
          return tabId;
        }

        var tabId = getOrCreateTabId();
        var isLoggedIn = <?= !empty($_SESSION['user']) ? 'true' : 'false' ?>;
        var modalShown = false;
        var sessionTaken = false;

        // Event channel for inter-tab communication
        var SESSION_CHANNEL = 'clinivet_session_channel';
        var STORAGE_PREFIX = 'clinivet_';
        
        // Broadcast a message to other tabs
        function broadcastMessage(type, data) {
          var message = {
            type: type,
            tabId: tabId,
            timestamp: Date.now(),
            data: data || {}
          };
          try {
            localStorage.setItem(STORAGE_PREFIX + 'event_' + Date.now() + '_' + Math.random(), JSON.stringify(message));
          } catch(e) {
            console.warn('localStorage not available for broadcast');
          }
        }

        // Listen for messages from other tabs
        function setupStorageListener() {
          window.addEventListener('storage', function(event) {
            if (!event.key || !event.key.startsWith(STORAGE_PREFIX + 'event_')) {
              return;
            }
            
            try {
              var message = JSON.parse(event.newValue);
              if (!message || message.tabId === tabId) return; // Ignore own messages
              
              handleMessage(message);
            } catch(e) {
              console.warn('Invalid message received', e);
            }
          });
        }

        // Handle incoming messages
        function handleMessage(message) {
          if (message.type === 'whois-active') {
            // Another tab is asking who's active
            if (isLoggedIn && !sessionTaken) {
              // We're logged in, respond that we're active
              setTimeout(function() {
                broadcastMessage('active', { respondingTabId: tabId });
              }, Math.random() * 100); // Small random delay to avoid collision
            }
          } 
          else if (message.type === 'active') {
            // Another tab responded that it's active - show modal
            if (!modalShown && isLoggedIn) {
              showSessionTakeoverModal();
              modalShown = true;
            }
          }
          else if (message.type === 'take-session') {
            // Another tab is taking over the session
            if (message.data.requestingTabId !== tabId && isLoggedIn) {
              // We need to logout silently and reload
              performSilentLogout();
            }
          }
          else if (message.type === 'session-taken') {
            // Another tab has successfully taken over
            if (message.data.requestingTabId !== tabId && isLoggedIn && !sessionTaken) {
              // They took it, reload this page
              location.reload();
            }
          }
        }

        // Show the modal for taking over session
        function showSessionTakeoverModal() {
          var modalEl = document.getElementById('sessionTakeoverModal');
          if (!modalEl) return;
          
          // Using Bootstrap 5 modal API
          var modal = new (window.bootstrap ? window.bootstrap.Modal : function(){
            // Fallback if Bootstrap not loaded yet
            modalEl.style.display = 'block';
            modalEl.classList.add('show');
            modalEl.style.backgroundColor = 'rgba(0,0,0,0.5)';
          })(modalEl);
          
          if (window.bootstrap) {
            modal.show();
          }
        }

        // Handle "Usar sesión aquí" button click
        function setupModalHandlers() {
          var cancelBtn = document.getElementById('sessionModalCancel');
          var takeBtn = document.getElementById('sessionModalTakeSession');
          var modal = document.getElementById('sessionTakeoverModal');
          
          if (takeBtn) {
            takeBtn.addEventListener('click', function() {
              sessionTaken = true;
              // Notify other tabs that we're taking the session
              broadcastMessage('take-session', { requestingTabId: tabId });
              
              // Give other tabs time to logout
              setTimeout(function() {
                broadcastMessage('session-taken', { requestingTabId: tabId });
                // Reload to ensure clean state
                setTimeout(function() {
                  location.reload();
                }, 500);
              }, 1000);
            });
          }
          
          if (cancelBtn) {
            cancelBtn.addEventListener('click', function() {
              // Keep current tab as is, don't take session
              if (modal && window.bootstrap) {
                var bootstrapModal = window.bootstrap.Modal.getInstance(modal);
                if (bootstrapModal) bootstrapModal.hide();
              }
            });
          }
        }

        // Perform silent logout in the background
        function performSilentLogout() {
          fetch(BASE_URL + '/logout?silent=1', {
            method: 'GET',
            credentials: 'include'
          })
          .then(function() {
            // Reload page after logout
            setTimeout(function() {
              location.reload();
            }, 500);
          })
          .catch(function(error) {
            console.error('Silent logout failed:', error);
            // Still reload even if logout failed
            setTimeout(function() {
              location.reload();
            }, 500);
          });
        }

        // Initialize on DOMContentLoaded
        document.addEventListener('DOMContentLoaded', function() {
          setupStorageListener();
          setupModalHandlers();
          
          // Only if logged in and we're a "new" tab, ask who's active
          if (isLoggedIn) {
            setTimeout(function() {
              // Send whois-active query
              broadcastMessage('whois-active', { queryingTabId: tabId });
              
              // Wait a bit to see if anyone responds
              setTimeout(function() {
                if (!modalShown) {
                  // No one responded, we're the active tab
                  try {
                    localStorage.setItem(STORAGE_PREFIX + 'active_tab', tabId);
                  } catch(e) {}
                }
              }, RESPONSE_TIMEOUT);
            }, CHECK_INTERVAL);
          }
        });

        // Store active tab ID on page visibility change
        document.addEventListener('visibilitychange', function() {
          if (!isLoggedIn) return;
          
          if (document.hidden) {
            // Tab hidden
          } else {
            // Tab became visible
            try {
              localStorage.setItem(STORAGE_PREFIX + 'active_tab', tabId);
            } catch(e) {}
          }
        });
      })();
    </script>
  </body>
  </html>


