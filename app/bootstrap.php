<?php
use App\Middleware\CsrfMiddleware;

require __DIR__ . '/config.php';

spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    if (file_exists($file)) {
        require $file;
    }
});

date_default_timezone_set('Europe/Berlin');

session_set_cookie_params([
    'httponly' => true,
    'secure' => !empty($_SERVER['HTTPS']),
    'samesite' => 'Lax',
]);
if (session_status() === PHP_SESSION_NONE) {
    session_start();
    session_regenerate_id(true);
}

$dsn = sprintf('mysql:host=%s;dbname=%s;charset=utf8mb4', DB_HOST, DB_NAME);
try {
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    die('DB-Verbindung fehlgeschlagen: ' . htmlspecialchars($e->getMessage()));
}

CsrfMiddleware::ensureToken();

function url(string $path = ''): string {
    $base = rtrim(APP_URL, '/');
    if ($path === '') {
        return $base !== '' ? $base : '/';
    }
    if (preg_match('#^https?://#i', $path)) {
        return $path;
    }
    if ($path[0] !== '/') {
        $path = '/' . $path;
    }
    if ($base !== '' && ($path === $base || str_starts_with($path, $base . '/'))) {
        return $path;
    }
    return $base !== '' ? $base . $path : $path;
}

function view(string $template, array $data = []) {
    extract($data);
    ob_start();
    require __DIR__ . '/views/' . $template . '.php';
    return ob_get_clean();
}

function redirect(string $path) {
    header('Location: ' . url($path));
    exit;
}

function csrf_field() {
    $token = $_SESSION['csrf_token'] ?? '';
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token) . '">';
}

function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function auth_user() {
    return $_SESSION['user'] ?? null;
}
