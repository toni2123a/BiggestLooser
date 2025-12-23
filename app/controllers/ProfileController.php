<?php
namespace App\Controllers;

use App\Models\User;
use App\Services\BadgeService;

class ProfileController
{
    private \PDO $pdo;
    private User $users;
    private BadgeService $badgeService;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->users = new User($pdo);
        $this->badgeService = new BadgeService($pdo);
    }

    public function show()
    {
        $user = $this->users->findById(auth_user()['id']);
        $badges = (new \App\Models\UserBadge($this->pdo))->userBadges($user['id']);
        return view('profile/show', ['title' => 'Profil', 'user' => $user, 'badges' => $badges]);
    }

    public function update()
    {
        $data = [
            'nickname' => trim($_POST['nickname'] ?? ''),
            'is_public' => isset($_POST['is_public']) ? 1 : 0,
            'height_cm' => $_POST['height_cm'] !== '' ? (int)$_POST['height_cm'] : null,
            'birth_year' => $_POST['birth_year'] !== '' ? (int)$_POST['birth_year'] : null,
            'activity_level' => $_POST['activity_level'] ?? null,
            'start_weight' => $_POST['start_weight'] !== '' ? (float)$_POST['start_weight'] : null,
            'goal_weight' => $_POST['goal_weight'] !== '' ? (float)$_POST['goal_weight'] : null,
            'goal_date' => $_POST['goal_date'] !== '' ? $_POST['goal_date'] : null,
        ];
        $this->users->updateProfile(auth_user()['id'], $data);
        $_SESSION['user'] = $this->users->findById(auth_user()['id']);
        $this->badgeService->evaluate(auth_user()['id'], $_SESSION['user']);
        return redirect('/profile');
    }

    public function changePassword()
    {
        $pwd = $_POST['password'] ?? '';
        if (strlen($pwd) < 6) {
            return view('profile/show', ['title' => 'Profil', 'user' => auth_user(), 'error' => 'Passwort zu kurz']);
        }
        $hash = password_hash($pwd, PASSWORD_DEFAULT);
        $this->users->updatePassword(auth_user()['id'], $hash);
        return redirect('/profile');
    }

    public function delete()
    {
        $this->users->softDelete(auth_user()['id']);
        session_destroy();
        return redirect('/login');
    }
}
