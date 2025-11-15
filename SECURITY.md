# Seguridad Implementada en Clinivet

## Protecciones Contra Inyección SQL

### ✅ Prepared Statements
Todas las consultas a la base de datos usan **prepared statements** con parámetros vinculados (`?`), previniendo inyecciones SQL:

**Ejemplos:**
```php
// AuthController
$stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
$stmt->execute([$email]);

// ProductController
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);

// OrderController
$ins = $pdo->prepare("INSERT INTO orders (user_id, total, status) VALUES (?,?,?)");
$ins->execute([$userId, $total, $orderStatus]);
```

### ✅ Validación de Tipos
- IDs: forzados a `intval()` antes de usar
- Búsquedas: sanitizadas con `strip_tags()`
- Categorías: validadas con `ctype_digit()`

## Protecciones XSS (Cross-Site Scripting)

### ✅ Sanitización de Salida
- Todos los outputs usan `htmlspecialchars()` en las vistas
- Headers de seguridad: `X-XSS-Protection`, `X-Content-Type-Options`

### ✅ Sanitización de Entrada
- Clase `Security::sanitizeString()` elimina HTML/scripts
- Validación estricta de formatos (email, RFC)

## Protecciones CSRF (Cross-Site Request Forgery)

### ✅ Tokens CSRF
- Generación: `Security::generateCSRFToken()`
- Verificación: `Security::verifyCSRFToken($token)`
- Almacenados en sesión, válidos por sesión

## Rate Limiting

### ✅ Límites por Acción
- **Login**: 5 intentos por 5 minutos
- **Registro**: 3 intentos por 10 minutos
- Basado en IP + sesión

```php
if (!Security::checkRateLimit('login', 5, 300)) {
    $error = "Demasiados intentos...";
}
```

## Protección de Sesiones

### ✅ Session Fixation
- Regeneración de ID de sesión tras login/registro exitoso
- `Security::regenerateSession()` usa `session_regenerate_id(true)`

### ✅ Configuración Segura
```php
session_start([
    'cookie_httponly' => true,  // No accesible por JS
    'cookie_secure' => true,     // Solo HTTPS (producción)
    'cookie_samesite' => 'Strict' // CSRF protection
]);
```

## Validación de Uploads

### ✅ Validación de Imágenes
- Tipo MIME real (no solo extensión)
- Tamaño máximo: 5MB
- Solo: JPG, PNG, GIF, WEBP
- Verificación con `getimagesize()`

```php
$validation = Security::validateImageUpload($_FILES['image']);
if (!$validation['valid']) {
    die($validation['error']);
}
```

### ✅ Sanitización de Nombres
- Elimina caracteres especiales
- Limita longitud a 100 chars
- Solo alfanuméricos, guiones, guiones bajos

## Headers de Seguridad

### ✅ Headers HTTP Implementados
```php
X-Frame-Options: SAMEORIGIN              // Anti-clickjacking
X-Content-Type-Options: nosniff          // Anti-MIME sniffing
X-XSS-Protection: 1; mode=block          // XSS browser protection
Referrer-Policy: strict-origin-when-cross-origin
Content-Security-Policy: [política restrictiva]
```

## Validación de Entrada

### ✅ Validaciones por Tipo

**Email:**
```php
Security::validateEmail($email) // FILTER_VALIDATE_EMAIL
```

**RFC (México):**
```php
Security::validateRFC($rfc) // Regex 12-13 chars alfanuméricos
```

**Contraseñas:**
- Mínimo 6 caracteres
- Hasheadas con `PASSWORD_BCRYPT`
- Soporte legacy con migración automática

## Protección Path Traversal

### ✅ Validación de Rutas
```php
// Bloquea: ../, <, >, ", ', null bytes
if (preg_match('/[\.]{2,}|[<>"']|\x00/', $route)) {
    http_response_code(400);
    die('Invalid route');
}
```

## Configuración Apache

### ✅ .htaccess
- `Options -Indexes` (sin listados de directorios)
- `IndexIgnore *` en uploads
- Rewrite rules seguras

## Resumen de Capas de Seguridad

| Amenaza | Protección | Implementación |
|---------|-----------|----------------|
| **SQL Injection** | Prepared Statements | Todas las queries |
| **XSS** | htmlspecialchars + CSP | Todas las vistas |
| **CSRF** | Tokens + verificación | Formularios POST |
| **Brute Force** | Rate Limiting | Login/Registro |
| **Session Fixation** | Regenerate ID | Login/Logout |
| **File Upload** | Validación MIME + tamaño | Admin uploads |
| **Path Traversal** | Regex validation | Router |
| **Clickjacking** | X-Frame-Options | Headers globales |

## Recomendaciones Adicionales

### Para Producción:
1. **HTTPS obligatorio**: configura `cookie_secure => true`
2. **Variables de entorno**: mueve credenciales a `.env`
3. **Logs seguros**: implementa logging de intentos fallidos
4. **Actualiza dependencias**: `composer update` regular
5. **Backup BD**: automatiza respaldos diarios
6. **WAF**: considera Cloudflare o similar

### Auditoría:
- Revisa logs de rate limiting mensualmente
- Monitorea intentos de login fallidos
- Actualiza Security.php con nuevas amenazas

---

**Última actualización:** Noviembre 2025  
**Versión:** 1.0
