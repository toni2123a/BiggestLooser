<?php
namespace App\Models;

class GroupMember
{
    private \PDO $pdo;
    public function __construct(\PDO $pdo) { $this->pdo = $pdo; }

    public function add(int $groupId, int $userId, string $role = 'member'): void
    {
        $stmt = $this->pdo->prepare('INSERT IGNORE INTO group_members (group_id, user_id, role, joined_at) VALUES (:gid, :uid, :role, NOW())');
        $stmt->execute(['gid' => $groupId, 'uid' => $userId, 'role' => $role]);
    }

    public function members(int $groupId)
    {
        $stmt = $this->pdo->prepare('SELECT gm.*, u.nickname FROM group_members gm JOIN users u ON u.id=gm.user_id WHERE gm.group_id=:gid');
        $stmt->execute(['gid' => $groupId]);
        return $stmt->fetchAll();
    }

    public function remove(int $groupId, int $userId): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM group_members WHERE group_id=:gid AND user_id=:uid');
        $stmt->execute(['gid' => $groupId, 'uid' => $userId]);
    }
}
