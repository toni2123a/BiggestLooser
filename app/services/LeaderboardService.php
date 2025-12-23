<?php
namespace App\Services;

class LeaderboardService
{
    private \PDO $pdo;
    public function __construct(\PDO $pdo) { $this->pdo = $pdo; }

    public function percentLoss(int $days = 0): array
    {
        $params = [];
        $where = 'u.is_public = 1 AND u.deleted_at IS NULL';
        $dateFilter = '';
        if ($days > 0) {
            $dateFilter = 'AND w.date >= :from';
            $params['from'] = (new \DateTime("-$days days"))->format('Y-m-d');
        }
        $sql = "SELECT u.id, u.nickname, u.height_cm, MIN(w.weight_kg) AS start_w, MAX(w.weight_kg) AS last_w, MIN(w.date) AS first_date, MAX(w.date) AS last_date, COUNT(w.id) AS entries
                FROM users u
                JOIN weigh_ins w ON w.user_id = u.id
                WHERE $where $dateFilter
                GROUP BY u.id, u.nickname, u.height_cm
                HAVING start_w IS NOT NULL AND last_w IS NOT NULL";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $result = [];
        while ($row = $stmt->fetch()) {
            $loss = $row['start_w'] - $row['last_w'];
            $percent = $row['start_w'] > 0 ? ($loss / $row['start_w']) * 100 : 0;
            $bmi = $row['height_cm'] ? $row['last_w'] / pow($row['height_cm']/100,2) : null;
            $result[] = [
                'nickname' => $row['nickname'],
                'percent' => round($percent, 1),
                'streak' => $this->streakForUser((int)$row['id']),
                'badges' => $this->badgeCount((int)$row['id']),
                'bmi_category' => $this->bmiCategory($bmi),
                'entries' => $row['entries'],
            ];
        }
        usort($result, fn($a,$b) => $b['percent'] <=> $a['percent']);
        return $result;
    }

    private function streakForUser(int $userId): int
    {
        $stmt = $this->pdo->prepare('SELECT date FROM weigh_ins WHERE user_id=:uid ORDER BY date DESC');
        $stmt->execute(['uid' => $userId]);
        $dates = $stmt->fetchAll();
        $streak = 0;
        $current = new \DateTime();
        foreach ($dates as $row) {
            $d = new \DateTime($row['date']);
            if ($d->format('Y-m-d') === $current->format('Y-m-d')) {
                $streak++;
            } else {
                $current->modify('-1 day');
                if ($d->format('Y-m-d') === $current->format('Y-m-d')) {
                    $streak++;
                    continue;
                }
                break;
            }
        }
        return $streak;
    }

    private function badgeCount(int $userId): int
    {
        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM user_badges WHERE user_id=:uid');
        $stmt->execute(['uid' => $userId]);
        return (int)$stmt->fetchColumn();
    }

    private function bmiCategory(?float $bmi): ?string
    {
        if (!$bmi) return null;
        if ($bmi < 18.5) return 'Untergewicht';
        if ($bmi < 25) return 'Normal';
        if ($bmi < 30) return 'Übergewicht';
        return 'Adipositas';
    }
}
