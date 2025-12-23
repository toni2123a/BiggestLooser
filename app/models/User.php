<?php
namespace App\Models;

class User
{
    private \PDO $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function findByEmail(string $email)
    {
        $stmt = $this->pdo->prepare('SELECT * FROM users WHERE email = :email AND deleted_at IS NULL');
        $stmt->execute(['email' => $email]);
        return $stmt->fetch();
    }

    public function findById(int $id)
    {
        $stmt = $this->pdo->prepare('SELECT * FROM users WHERE id = :id AND deleted_at IS NULL');
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    public function create(array $data): int
    {
        $stmt = $this->pdo->prepare('INSERT INTO users (email, nickname, password_hash, created_at, updated_at, is_public, start_weight, goal_weight, goal_date) VALUES (:email, :nickname, :password_hash, NOW(), NOW(), :is_public, :start_weight, :goal_weight, :goal_date)');
        $stmt->execute([
            'email' => $data['email'],
            'nickname' => $data['nickname'],
            'password_hash' => $data['password_hash'],
            'is_public' => $data['is_public'] ?? 0,
            'start_weight' => $data['start_weight'] ?? null,
            'goal_weight' => $data['goal_weight'] ?? null,
            'goal_date' => $data['goal_date'] ?? null,
        ]);
        return (int)$this->pdo->lastInsertId();
    }

    public function updateProfile(int $id, array $data): void
    {
        $stmt = $this->pdo->prepare('UPDATE users SET nickname=:nickname, is_public=:is_public, height_cm=:height_cm, birth_year=:birth_year, activity_level=:activity_level, start_weight=:start_weight, goal_weight=:goal_weight, goal_date=:goal_date, updated_at=NOW() WHERE id=:id');
        $stmt->execute([
            'nickname' => $data['nickname'],
            'is_public' => $data['is_public'],
            'height_cm' => $data['height_cm'],
            'birth_year' => $data['birth_year'],
            'activity_level' => $data['activity_level'],
            'start_weight' => $data['start_weight'],
            'goal_weight' => $data['goal_weight'],
            'goal_date' => $data['goal_date'],
            'id' => $id,
        ]);
    }

    public function updatePassword(int $id, string $hash): void
    {
        $stmt = $this->pdo->prepare('UPDATE users SET password_hash=:hash, updated_at=NOW() WHERE id=:id');
        $stmt->execute(['hash' => $hash, 'id' => $id]);
    }

    public function softDelete(int $id): void
    {
        $stmt = $this->pdo->prepare('UPDATE users SET deleted_at=NOW() WHERE id=:id');
        $stmt->execute(['id' => $id]);
    }

    public function recordResetToken(string $email, string $token): void
    {
        $stmt = $this->pdo->prepare('UPDATE users SET password_token=:token, password_token_expires=DATE_ADD(NOW(), INTERVAL 1 HOUR) WHERE email=:email');
        $stmt->execute(['token' => $token, 'email' => $email]);
    }

    public function findByResetToken(string $token)
    {
        $stmt = $this->pdo->prepare('SELECT * FROM users WHERE password_token=:token AND password_token_expires>NOW()');
        $stmt->execute(['token' => $token]);
        return $stmt->fetch();
    }

    public function consumeResetToken(int $id): void
    {
        $stmt = $this->pdo->prepare('UPDATE users SET password_token=NULL, password_token_expires=NULL WHERE id=:id');
        $stmt->execute(['id' => $id]);
    }
}
