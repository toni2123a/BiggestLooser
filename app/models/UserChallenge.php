<?php
namespace App\Models;

class UserChallenge
{
    private \PDO $pdo;
    public function __construct(\PDO $pdo) { $this->pdo = $pdo; }

    public function join(int $challengeId, int $userId, ?int $groupId = null): void
    {
        $stmt = $this->pdo->prepare('INSERT IGNORE INTO user_challenges (challenge_id, user_id, group_id, progress_json, started_at) VALUES (:cid, :uid, :gid, :progress, NOW())');
        $stmt->execute([
            'cid' => $challengeId,
            'uid' => $userId,
            'gid' => $groupId,
            'progress' => json_encode(['percent' => 0]),
        ]);
    }

    public function forUser(int $userId)
    {
        $stmt = $this->pdo->prepare('SELECT uc.*, c.title FROM user_challenges uc JOIN challenges c ON c.id=uc.challenge_id WHERE uc.user_id=:uid');
        $stmt->execute(['uid' => $userId]);
        return $stmt->fetchAll();
    }

    public function updateProgress(int $id, array $progress, bool $completed = false): void
    {
        $stmt = $this->pdo->prepare('UPDATE user_challenges SET progress_json=:progress, status=:status, completed_at=IF(:completed, NOW(), completed_at) WHERE id=:id');
        $stmt->execute([
            'progress' => json_encode($progress),
            'status' => $completed ? 'completed' : 'active',
            'completed' => $completed ? 1 : 0,
            'id' => $id,
        ]);
    }
}
