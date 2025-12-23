<?php
namespace App\Middleware;

class CsrfMiddleware
{
    public static function ensureToken(): void
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
    }

    public static function validate(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            return;
        }
        $token = $_POST['csrf_token'] ?? $_GET['csrf_token'] ?? '';
        if (!$token || !hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
            http_response_code(400);
            echo 'Ungültiges CSRF Token';
            exit;
        }
    }
}
