<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Simple .env loader
if (file_exists(__DIR__ . '/.env')) {
    $lines = file(__DIR__ . '/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);
        if (!array_key_exists($name, $_SERVER) && !array_key_exists($name, $_ENV)) {
            putenv(sprintf('%s=%s', $name, $value));
            $_ENV[$name] = $value;
            $_SERVER[$name] = $value;
        }
    }
}

$host = getenv('DB_HOST') ?: '127.0.0.1';
$port = getenv('DB_PORT') ?: 3306;
$db   = getenv('DB_DATABASE') ?: 'retech';
$user = getenv('DB_USERNAME') ?: 'root';
$pass = getenv('DB_PASSWORD') !== false ? getenv('DB_PASSWORD') : '';

$dsn = "mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    // In production, don't leak connection details
    error_log("Database Connection Error: " . $e->getMessage());
    die("A database error occurred. Please try again later.");
}

// --- Global Security Helpers ---


/**
 * Sanitize output for HTML
 */
if (!function_exists('safe')) {
    function safe($v) {
        return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
    }
}

/**
 * Generate/Return CSRF token
 */
function get_csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * HTML hidden input for CSRF
 */
function csrf_field() {
    $token = safe(get_csrf_token());
    return "<input type='hidden' name='csrf_token' value='{$token}'>";
}

/**
 * Verify CSRF token from POST
 */
function check_csrf() {
    $token = $_POST['csrf_token'] ?? '';
    if (!hash_equals(get_csrf_token(), (string)$token)) {
        header('HTTP/1.1 403 Forbidden');
        die("Security Alert: Invalid CSRF token. Request blocked.");
    }
}

/**
 * Get a stable app secret for public anti-scraping
 */
function get_app_secret() {
    return getenv('APP_SECRET') ?: 'ktu_magic_permanent_secret_2026';
}

/**
 * Anti-Scraping URL Signature
 * Generates a hex signature based on parameters and a secret
 */
function get_url_sig($params, $use_session = false) {
    ksort($params);
    $query = http_build_query($params);
    $secret = $use_session ? get_csrf_token() : get_app_secret();
    return hash_hmac('sha256', $query, $secret); // Upgraded from sha1 for security
}

/**
 * Sign an internal URL to prevent parameter tampering
 */
function sign_url($base, $params, $use_session = false) {
    $params['sig'] = get_url_sig($params, $use_session);
    return $base . '?' . http_build_query($params);
}

/**
 * Verify GET request signature
 */
function verify_url_sig($use_session = false) {
    $params = $_GET;
    if (!isset($params['sig'])) return false;
    
    $received_sig = $params['sig'];
    unset($params['sig']);
    
    return hash_equals(get_url_sig($params, $use_session), $received_sig);
}

/**
 * Strict Sanitize Input (for text fields)
 */
function sanitize_input($data) {
    if ($data === null) return '';
    $data = trim((string)$data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

/**
 * Flash message helper
 */
function flash($msg = null, $type = 'info') {
    if ($msg === null) {
        $f = $_SESSION['flash'] ?? [];
        unset($_SESSION['flash']);
        return $f;
    }
    $_SESSION['flash'][] = ['msg'=>$msg,'type'=>$type];
}
