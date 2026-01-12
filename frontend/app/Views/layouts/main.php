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

    <!-- Per-tab session coordination (BroadcastChannel fallback to localStorage) -->
    <div id="session-coordination">
      <div id="session-transfer-modal" class="modal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">Sesión activa en otra pestaña</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
              <p>Se detectó que tu sesión está abierta en otra pestaña. ¿Deseas usar la sesión aquí y cerrar la sesión en la otra pestaña?</p>
            </div>
            <div class="modal-footer">
              <button type="button" id="session-ignore" class="btn btn-outline-secondary" data-bs-dismiss="modal">Ignorar</button>
              <button type="button" id="session-take" class="btn btn-primary">Usar sesión aquí</button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <script>
      (function(){
        // per-tab identifier
        try {
          var TAB_KEY = 'clinivet_tab_id';
          var tabId = sessionStorage.getItem(TAB_KEY);
          if (!tabId) { tabId = 'tab_' + Math.random().toString(36).slice(2,12) + '_' + Date.now().toString(36); sessionStorage.setItem(TAB_KEY, tabId); }
        } catch(e) { var tabId = 'tab_fallback_' + Date.now(); }

        var bc = null; try { bc = new BroadcastChannel('clinivet_session_channel'); } catch(e){ bc = null; }
        var isUserLoggedIn = <?= !empty($_SESSION['user']) ? 'true' : 'false' ?>;
        var askedWhoisAt = null;

        function sendWhoIsActive(){
          askedWhoisAt = Date.now();
          var msg = {type:'whois-active', tab: tabId, t: askedWhoisAt};
          if (bc) bc.postMessage(msg); else try { localStorage.setItem('clinivet_session_msg', JSON.stringify(msg)); } catch(e){}
        }

        // When this tab sees a whois-active and it's the active session it should reply
        function replyActive(){
          var msg = {type:'active', tab: tabId, t: Date.now()};
          if (bc) bc.postMessage(msg); else try { localStorage.setItem('clinivet_session_msg', JSON.stringify(msg)); } catch(e){}
        }

        document.addEventListener('DOMContentLoaded', function(){
          // new tab asks who is active
          setTimeout(sendWhoIsActive, 300);
        });

        function handleMsg(msg){
          try { var data = (typeof msg === 'string') ? JSON.parse(msg) : msg; } catch(e){ return; }
          if (!data || !data.type) return;
          if (data.type === 'whois-active'){
            // if this tab is logged in and visible, respond that we are active
            if (isUserLoggedIn && document.visibilityState === 'visible') { replyActive(); }
          } else if (data.type === 'active'){
            // only consider 'active' responses that arrive shortly after our whois query
            if (!askedWhoisAt) return;
            if ((Date.now() - askedWhoisAt) > 5000) return; // too late
            if (data.tab && data.tab !== tabId) {
              // another tab is active -> show transfer modal
              showTransferModal();
            }
          } else if (data.type === 'take-session'){
            if (data.tab && data.tab !== tabId) {
              if (isUserLoggedIn) {
                // silent logout
                fetch('<?= $BASE ?>/logout', {method:'GET', headers:{'X-Requested-With':'XMLHttpRequest'}})
                  .then(function(){ isUserLoggedIn = false; try{ location.reload(); } catch(e){} })
                  .catch(function(){ try{ location.reload(); } catch(e){} });
              }
            }
          }
        }

        if (bc) { bc.onmessage = function(ev){ handleMsg(ev.data); }; }
        else { window.addEventListener('storage', function(e){ if (e.key === 'clinivet_session_msg' && e.newValue) handleMsg(e.newValue); }); }

        var transferModalEl = document.getElementById('session-transfer-modal');
        var transferModal = null; try { transferModal = new bootstrap.Modal(transferModalEl); } catch(e){ transferModal = null; }
        function showTransferModal(){ if (transferModal) transferModal.show(); else if (confirm('Sesión activa en otra pestaña. ¿Usar sesión aquí y cerrar la otra?')) { takeSession(); } }

        function takeSession(){
          var msg = {type:'take-session', tab: tabId, t: Date.now()};
          if (bc) bc.postMessage(msg); else try { localStorage.setItem('clinivet_session_msg', JSON.stringify(msg)); } catch(e){}
          // reload to ensure authoritative UI
          setTimeout(function(){ try{ location.reload(); } catch(e){} }, 300);
        }

        var takeBtn = document.getElementById('session-take'); if (takeBtn) takeBtn.addEventListener('click', function(){ if (transferModal) transferModal.hide(); takeSession(); });
        var ignoreBtn = document.getElementById('session-ignore'); if (ignoreBtn) ignoreBtn.addEventListener('click', function(){ if (transferModal) transferModal.hide(); });

        // Broadcast local manual logout as take-session with empty tab so other tabs refresh
        var logoutLinks = document.querySelectorAll('a[href="<?= $BASE ?>/logout"]');
        logoutLinks.forEach(function(a){ a.addEventListener('click', function(){ var msg = {type:'take-session', tab:'', t: Date.now()}; if (bc) bc.postMessage(msg); else try { localStorage.setItem('clinivet_session_msg', JSON.stringify(msg)); } catch(e){} }); });
      })();
    </script>
  </body>
  </html>
