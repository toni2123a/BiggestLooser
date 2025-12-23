<?php
namespace App\Models;

class UserBadge
{
    private \PDO $pdo;
    public function __construct(\PDO $pdo) { $this->pdo = $pdo; }

    public function userBadges(int $userId)
    {
        $stmt = $this->pdo->prepare('SELECT b.* , ub.awarded_at FROM user_badges ub JOIN badges b ON b.id=ub.badge_id WHERE ub.user_id=:uid ORDER BY ub.awarded_at DESC');
        $stmt->execute(['uid' => $userId]);
        return $stmt->fetchAll();
    }

    public function award(int $userId, int $badgeId): void
    {
        $stmt = $this->pdo->prepare('INSERT IGNORE INTO user_badges (user_id, badge_id, awarded_at) VALUES (:uid, :bid, NOW())');
        $stmt->execute(['uid' => $userId, 'bid' => $badgeId]);
    }
}
