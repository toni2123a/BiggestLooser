<?php
namespace App\Services;

use App\Models\Badge;
use App\Models\UserBadge;
use App\Models\WeighIn;

class BadgeService
{
    private \PDO $pdo;
    private Badge $badgeModel;
    private UserBadge $userBadgeModel;
    private WeighIn $weighInModel;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->badgeModel = new Badge($pdo);
        $this->userBadgeModel = new UserBadge($pdo);
        $this->weighInModel = new WeighIn($pdo);
    }

    public function evaluate(int $userId, array $user): void
    {
        $entries = $this->weighInModel->getByUser($userId);
        $badgeMap = [];
        foreach ($this->badgeModel->all() as $b) {
            $badgeMap[$b['code']] = $b['id'];
        }
        if (count($entries) > 0 && isset($badgeMap['first_entry'])) {
            $this->userBadgeModel->award($userId, $badgeMap['first_entry']);
        }
        $streak = $this->currentStreak($entries);
        if ($streak >= 7 && isset($badgeMap['streak_7'])) {
            $this->userBadgeModel->award($userId, $badgeMap['streak_7']);
        }
        $startWeight = $user['start_weight'] ?? null;
        if (!$startWeight && count($entries) > 0) {
            $startWeight = end($entries)['weight_kg'];
        }
        if ($startWeight && count($entries) > 0) {
            $latest = $entries[0]['weight_kg'];
            $loss = $startWeight - $latest;
            if ($loss >= 1 && isset($badgeMap['loss_1kg'])) {
                $this->userBadgeModel->award($userId, $badgeMap['loss_1kg']);
            }
            if ($loss >= 5 && isset($badgeMap['loss_5kg'])) {
                $this->userBadgeModel->award($userId, $badgeMap['loss_5kg']);
            }
            if ($loss >= 10 && isset($badgeMap['loss_10kg'])) {
                $this->userBadgeModel->award($userId, $badgeMap['loss_10kg']);
            }
            if ($user['goal_weight'] && $latest <= $user['goal_weight'] && isset($badgeMap['goal_reached'])) {
                $this->userBadgeModel->award($userId, $badgeMap['goal_reached']);
            }
        }
        if ($this->entriesInLastDays($entries, 30) >= 25 && isset($badgeMap['month_full'])) {
            $this->userBadgeModel->award($userId, $badgeMap['month_full']);
        }
        if ($this->consistencyScore($entries) >= 3 && isset($badgeMap['consistency'])) {
            $this->userBadgeModel->award($userId, $badgeMap['consistency']);
        }
    }

    private function currentStreak(array $entries): int
    {
        $streak = 0;
        $today = new \DateTime();
        foreach ($entries as $entry) {
            $entryDate = new \DateTime($entry['date']);
            if ($entryDate->format('Y-m-d') === $today->format('Y-m-d')) {
                $streak = max($streak, 1);
            }
        }
        $dates = array_column($entries, 'date');
        $dates = array_map(fn($d) => (new \DateTime($d))->format('Y-m-d'), $dates);
        $dates = array_unique($dates);
        rsort($dates);
        $current = new \DateTime();
        foreach ($dates as $d) {
            if ($d === $current->format('Y-m-d')) {
                $streak++;
            } else {
                $current->modify('-1 day');
                if ($d === $current->format('Y-m-d')) {
                    $streak++;
                    continue;
                }
                break;
            }
        }
        return $streak;
    }

    private function entriesInLastDays(array $entries, int $days): int
    {
        $count = 0;
        $threshold = (new \DateTime("-$days days"))->format('Y-m-d');
        foreach ($entries as $entry) {
            if ($entry['date'] >= $threshold) $count++;
        }
        return $count;
    }

    private function consistencyScore(array $entries): float
    {
        // simple: average entries per week over 8 weeks
        $threshold = (new \DateTime('-56 days'))->format('Y-m-d');
        $counts = [];
        foreach ($entries as $entry) {
            if ($entry['date'] >= $threshold) {
                $week = date('oW', strtotime($entry['date']));
                $counts[$week] = ($counts[$week] ?? 0) + 1;
            }
        }
        if (empty($counts)) return 0;
        return array_sum($counts) / count($counts);
    }
}
