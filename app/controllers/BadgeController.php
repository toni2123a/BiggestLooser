<?php
namespace App\Controllers;

use App\Models\UserBadge;

class BadgeController
{
    private \PDO $pdo;
    private UserBadge $userBadge;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->userBadge = new UserBadge($pdo);
    }

    public function index()
    {
        $badges = $this->userBadge->userBadges(auth_user()['id']);
        return view('badges/index', ['title' => 'Auszeichnungen', 'badges' => $badges]);
    }
}
