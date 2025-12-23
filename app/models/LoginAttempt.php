<?php
namespace App\Models;

class LoginAttempt
{
    private \PDO $pdo;
    public function __construct(\PDO $pdo) { $this->pdo = $pdo; }

    public function log(?string $email, string $ip, bool $success): void
    {
        $stmt = $this->pdo->prepare('INSERT INTO login_attempts (email, ip, success, created_at) VALUES (:email, :ip, :success, NOW())');
        $stmt->execute(['email' => $email, 'ip' => $ip, 'success' => $success ? 1 : 0]);
    }
}
