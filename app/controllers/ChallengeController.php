<?php
namespace App\Controllers;

use App\Models\Challenge;
use App\Services\ChallengeService;

class ChallengeController
{
    private Challenge $challenges;
    private ChallengeService $service;

    public function __construct(\PDO $pdo)
    {
        $this->challenges = new Challenge($pdo);
        $this->service = new ChallengeService($pdo);
    }

    public function index()
    {
        $all = $this->challenges->all();
        $userChallenges = $this->service->computeProgress(auth_user()['id']);
        return view('challenges/index', ['title' => 'Challenges', 'challenges' => $all, 'userChallenges' => $userChallenges]);
    }

    public function join()
    {
        $cid = (int)($_POST['challenge_id'] ?? 0);
        $gid = $_POST['group_id'] !== '' ? (int)$_POST['group_id'] : null;
        $this->service->join($cid, auth_user()['id'], $gid);
        redirect('/challenges');
    }
}
