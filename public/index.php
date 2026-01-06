<?php
require __DIR__ . '/../app/bootstrap.php';

use App\Middleware\AuthMiddleware;
use App\Middleware\CsrfMiddleware;
use App\Middleware\RateLimitMiddleware;

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/';
$basePath = rtrim(APP_URL, '/');
if ($basePath !== '') {
    if ($path === $basePath) {
        $path = '/';
    } elseif (str_starts_with($path, $basePath . '/')) {
        $path = substr($path, strlen($basePath));
        if ($path === '') {
            $path = '/';
        }
    }
}
if ($path === '/index.php') {
    $path = '/';
}
$method = $_SERVER['REQUEST_METHOD'];

require __DIR__ . '/../app/routes.php';

if (!isset($routes[$method])) {
    http_response_code(405);
    echo 'Methode nicht erlaubt';
    exit;
}

foreach ($routes[$method] as $pattern => $handler) {
    $regex = '#^' . preg_replace('#\{([a-zA-Z0-9_]+)\}#', '(?P<$1>[^/]+)', $pattern) . '$#';
    if (preg_match($regex, $path, $matches)) {
        if (in_array($pattern, $protectedPaths)) {
            AuthMiddleware::check();
        }
        if (in_array($method, $csrfMethods)) {
            CsrfMiddleware::validate();
        }
        if ($method === 'POST' && in_array($pattern, $rateLimited)) {
            RateLimitMiddleware::check($pdo, $_SERVER['REMOTE_ADDR'] ?? 'unknown', $_POST['email'] ?? '');
        }

        foreach ($matches as $k => $v) {
            if (is_int($k)) unset($matches[$k]);
        }
        [$controller, $action] = $handler;
        $controllerClass = "App\\Controllers\\{$controller}";
        $instance = new $controllerClass($pdo);
        echo $instance->$action($matches);
        exit;
    }
}

http_response_code(404);
echo 'Seite nicht gefunden';
