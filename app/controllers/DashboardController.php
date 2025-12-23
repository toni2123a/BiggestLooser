<?php
namespace App\Controllers;

use App\Models\WeighIn;
use App\Services\BadgeService;
use App\Services\ChallengeService;

class DashboardController
{
    private \PDO $pdo;
    private WeighIn $weighIns;
    private BadgeService $badgeService;
    private ChallengeService $challengeService;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->weighIns = new WeighIn($pdo);
        $this->badgeService = new BadgeService($pdo);
        $this->challengeService = new ChallengeService($pdo);
    }

    public function index()
    {
        $user = auth_user();
        $entries = $this->weighIns->getByUser($user['id']);
        $latest = $this->weighIns->latest($user['id']);
        $trend = $this->trend($entries);
        $avg7 = $this->avgLastDays($entries, 7);
        $this->badgeService->evaluate($user['id'], $user);
        $challenges = $this->challengeService->computeProgress($user['id']);
        return view('dashboard/index', [
            'title' => 'Dashboard',
            'entries' => $entries,
            'latest' => $latest,
            'trend' => $trend,
            'avg7' => $avg7,
            'challenges' => $challenges,
            'user' => $user,
        ]);
    }

    private function trend(array $entries): float
    {
        if (count($entries) < 2) return 0;
        $first = end($entries)['weight_kg'];
        $last = $entries[0]['weight_kg'];
        return round($last - $first, 1);
    }

    private function avgLastDays(array $entries, int $days): ?float
    {
        $threshold = (new \DateTime("-$days days"))->format('Y-m-d');
        $vals = array_filter($entries, fn($e) => $e['date'] >= $threshold);
        if (empty($vals)) return null;
        $sum = array_sum(array_column($vals, 'weight_kg'));
        return round($sum / count($vals), 1);
    }
}
