<?php
namespace App\Helpers;

class Security {
    /**
     * Return URL prefix for the app. If the app is mounted under /clinivet,
     * returns "/clinivet"; if served from document root, returns ''.
     */
    public static function base() {
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        $script = $_SERVER['SCRIPT_NAME'] ?? '';
        if (strpos($uri, '/clinivet/') === 0 || $uri === '/clinivet' || strpos($script, '/clinivet/') === 0) {
            return '/clinivet';
        }
        return '';
    }
    /**
     * Generate CSRF token and store in session
     */
    public static function generateCSRFToken() {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * Verify CSRF token from POST request
     */
    public static function verifyCSRFToken($token) {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }

    /**
     * Sanitize string input (basic XSS protection)
     */
    public static function sanitizeString($input) {
        return htmlspecialchars(trim($input), ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    /**
     * Validate email format
     */
    public static function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Validate RFC format (Mexican tax ID)
     */
    public static function validateRFC($rfc) {
        // Básico: 12-13 caracteres alfanuméricos
        return preg_match('/^[A-Z&Ñ]{3,4}\d{6}[A-Z0-9]{2,3}$/i', $rfc);
    }

    /**
     * Rate limiting check (simple IP-based)
     */
    public static function checkRateLimit($action, $maxAttempts = 5, $window = 300) {
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $key = "rate_limit_{$action}_{$ip}";
        
        if (!isset($_SESSION[$key])) {
            $_SESSION[$key] = ['count' => 0, 'start' => time()];
        }
        
        $data = $_SESSION[$key];
        
        // Reset if window expired
        if (time() - $data['start'] > $window) {
            $_SESSION[$key] = ['count' => 1, 'start' => time()];
            return true;
        }
        
        // Check limit
        if ($data['count'] >= $maxAttempts) {
            return false;
        }
        
        $_SESSION[$key]['count']++;
        return true;
    }

    /**
     * Set security headers
     */
    public static function setSecurityHeaders() {
        // Prevent clickjacking
        header('X-Frame-Options: SAMEORIGIN');
        // XSS protection
        header('X-Content-Type-Options: nosniff');
        header('X-XSS-Protection: 1; mode=block');
        // Referrer policy
        header('Referrer-Policy: strict-origin-when-cross-origin');
        // Content Security Policy (básica, versión previa)
        header("Content-Security-Policy: default-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net https://unpkg.com https://fonts.googleapis.com https://fonts.gstatic.com; img-src 'self' data: https:; script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net https://unpkg.com https://js.stripe.com;");
    }

    /**
     * Validate file upload (images)
     */
    public static function validateImageUpload($file) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $maxSize = 5 * 1024 * 1024; // 5MB
        
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return ['valid' => false, 'error' => 'Error al subir archivo'];
        }
        
        if ($file['size'] > $maxSize) {
            return ['valid' => false, 'error' => 'Archivo muy grande (máx 5MB)'];
        }
        
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        
        if (!in_array($mimeType, $allowedTypes)) {
            return ['valid' => false, 'error' => 'Tipo de archivo no permitido'];
        }
        
        // Check actual image
        $imageInfo = @getimagesize($file['tmp_name']);
        if ($imageInfo === false) {
            return ['valid' => false, 'error' => 'El archivo no es una imagen válida'];
        }
        
        return ['valid' => true];
    }

    /**
     * Generate safe filename
     */
    public static function sanitizeFilename($filename) {
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        $name = pathinfo($filename, PATHINFO_FILENAME);
        
        // Remove special chars, keep only alphanumeric, dash, underscore
        $name = preg_replace('/[^a-zA-Z0-9_-]/', '_', $name);
        $name = substr($name, 0, 100); // Limit length
        
        // Validate extension
        $allowedExts = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        if (!in_array(strtolower($ext), $allowedExts)) {
            $ext = 'jpg';
        }
        
        return $name . '.' . strtolower($ext);
    }

    /**
     * Prevent session fixation
     */
    public static function regenerateSession() {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_regenerate_id(true);
        }
    }
}
