<?php
namespace App\Middleware;

class RateLimitMiddleware
{
    public static function check(\PDO $pdo, string $ip, string $email): void
    {
        $windowStart = (new \DateTime('-15 minutes'))->format('Y-m-d H:i:s');
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM login_attempts WHERE (ip = :ip OR email = :email) AND created_at >= :start AND success = 0');
        $stmt->execute(['ip' => $ip, 'email' => $email, 'start' => $windowStart]);
        $failures = (int)$stmt->fetchColumn();
        if ($failures >= 5) {
            http_response_code(429);
            echo 'Zu viele Login-Versuche. Bitte warte ein wenig.';
            exit;
        }
    }
}
