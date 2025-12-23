<?php
namespace App\Models;

class WeighIn
{
    private \PDO $pdo;
    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function create(array $data): void
    {
        $stmt = $this->pdo->prepare('INSERT INTO weigh_ins (user_id, date, weight_kg, note, water_l, steps, created_at) VALUES (:user_id, :date, :weight_kg, :note, :water_l, :steps, NOW()) ON DUPLICATE KEY UPDATE weight_kg=:weight_kg, note=:note, water_l=:water_l, steps=:steps');
        $stmt->execute($data);
    }

    public function getByUser(int $userId, ?string $from = null, ?string $to = null)
    {
        $sql = 'SELECT * FROM weigh_ins WHERE user_id=:uid';
        $params = ['uid' => $userId];
        if ($from) { $sql .= ' AND date >= :from'; $params['from'] = $from; }
        if ($to) { $sql .= ' AND date <= :to'; $params['to'] = $to; }
        $sql .= ' ORDER BY date DESC';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function latest(int $userId)
    {
        $stmt = $this->pdo->prepare('SELECT * FROM weigh_ins WHERE user_id=:uid ORDER BY date DESC LIMIT 1');
        $stmt->execute(['uid' => $userId]);
        return $stmt->fetch();
    }
}
