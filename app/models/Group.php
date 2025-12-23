<?php
namespace App\Models;

class Group
{
    private \PDO $pdo;
    public function __construct(\PDO $pdo) { $this->pdo = $pdo; }

    public function all()
    {
        return $this->pdo->query('SELECT g.*, u.nickname AS owner_nick FROM groups g JOIN users u ON u.id=g.owner_user_id')->fetchAll();
    }

    public function create(int $ownerId, string $name): int
    {
        $stmt = $this->pdo->prepare('INSERT INTO groups (name, owner_user_id, invite_code, created_at) VALUES (:name, :owner, :invite, NOW())');
        $stmt->execute(['name' => $name, 'owner' => $ownerId, 'invite' => bin2hex(random_bytes(4))]);
        return (int)$this->pdo->lastInsertId();
    }

    public function findByInvite(string $code)
    {
        $stmt = $this->pdo->prepare('SELECT * FROM groups WHERE invite_code=:code AND is_closed=0');
        $stmt->execute(['code' => $code]);
        return $stmt->fetch();
    }

    public function find(int $id)
    {
        $stmt = $this->pdo->prepare('SELECT * FROM groups WHERE id=:id');
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }
}
