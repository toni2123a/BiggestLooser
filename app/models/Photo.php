<?php
namespace App\Models;

class Photo
{
    private \PDO $pdo;
    public function __construct(\PDO $pdo) { $this->pdo = $pdo; }

    public function create(int $userId, string $date, string $filename, ?string $note): void
    {
        $stmt = $this->pdo->prepare('INSERT INTO photos (user_id, date, filename, note, created_at) VALUES (:uid, :date, :filename, :note, NOW())');
        $stmt->execute(['uid' => $userId, 'date' => $date, 'filename' => $filename, 'note' => $note]);
    }

    public function byUser(int $userId)
    {
        $stmt = $this->pdo->prepare('SELECT * FROM photos WHERE user_id=:uid ORDER BY date DESC');
        $stmt->execute(['uid' => $userId]);
        return $stmt->fetchAll();
    }
}
