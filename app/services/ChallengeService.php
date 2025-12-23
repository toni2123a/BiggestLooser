<?php
namespace App\Services;

use App\Models\Challenge;
use App\Models\UserChallenge;
use App\Models\WeighIn;

class ChallengeService
{
    private \PDO $pdo;
    private Challenge $challenge;
    private UserChallenge $userChallenge;
    private WeighIn $weighIn;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->challenge = new Challenge($pdo);
        $this->userChallenge = new UserChallenge($pdo);
        $this->weighIn = new WeighIn($pdo);
    }

    public function join(int $challengeId, int $userId, ?int $groupId = null): void
    {
        $this->userChallenge->join($challengeId, $userId, $groupId);
    }

    public function computeProgress(int $userId): array
    {
        $userChallenges = $this->userChallenge->forUser($userId);
        foreach ($userChallenges as &$uc) {
            $challenge = $this->challenge->find((int)$uc['challenge_id']);
            $entries = $this->weighIn->getByUser($userId, $challenge['start_date'], $challenge['end_date']);
            $progress = $this->evaluateRule($challenge['code'], $entries);
            $this->userChallenge->updateProgress((int)$uc['id'], $progress, $progress['percent'] >= 100);
            $uc['progress'] = $progress;
        }
        return $userChallenges;
    }

    private function evaluateRule(string $code, array $entries): array
    {
        $percent = 0;
        switch ($code) {
            case '30_days_log':
                $percent = min(100, count($entries) / 30 * 100);
                break;
            case '8w_consistency':
                $weeks = [];
                foreach ($entries as $e) {
                    $weeks[date('oW', strtotime($e['date']))] = ($weeks[date('oW', strtotime($e['date']))] ?? 0) + 1;
                }
                $completedWeeks = count(array_filter($weeks, fn($c) => $c >= 3));
                $percent = min(100, ($completedWeeks / 8) * 100);
                break;
            case 'steps_10k':
                $days = count(array_filter($entries, fn($e) => ($e['steps'] ?? 0) >= 10000));
                $percent = min(100, ($days / 30) * 100);
                break;
            default:
                $percent = min(100, count($entries) * 5);
        }
        return ['percent' => round($percent, 1)];
    }
}
