<?php
namespace App\Controllers;

use App\Services\LeaderboardService;

class LeaderboardController
{
    private LeaderboardService $service;

    public function __construct(\PDO $pdo)
    {
        $this->service = new LeaderboardService($pdo);
    }

    public function index()
    {
        $range = (int)($_GET['range'] ?? 90);
        $data = $this->service->percentLoss($range);
        return view('leaderboards/index', ['title' => 'Ranglisten', 'data' => $data, 'range' => $range]);
    }
}
