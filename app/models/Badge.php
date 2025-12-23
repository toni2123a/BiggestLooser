<?php
namespace App\Models;

class Badge
{
    private \PDO $pdo;
    public function __construct(\PDO $pdo) { $this->pdo = $pdo; }

    public function all()
    {
        return $this->pdo->query('SELECT * FROM badges')->fetchAll();
    }

    public function findByCode(string $code)
    {
        $stmt = $this->pdo->prepare('SELECT * FROM badges WHERE code=:code');
        $stmt->execute(['code' => $code]);
        return $stmt->fetch();
    }
}
