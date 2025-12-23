<?php
namespace App\Models;

class Challenge
{
    private \PDO $pdo;
    public function __construct(\PDO $pdo) { $this->pdo = $pdo; }

    public function all()
    {
        return $this->pdo->query('SELECT * FROM challenges')->fetchAll();
    }

    public function find(int $id)
    {
        $stmt = $this->pdo->prepare('SELECT * FROM challenges WHERE id=:id');
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }
}
