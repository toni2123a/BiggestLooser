<?php
namespace App\Controllers;

use App\Models\User;
use App\Models\LoginAttempt;

class AuthController
{
    private \PDO $pdo;
    private User $users;
    private LoginAttempt $loginAttempts;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->users = new User($pdo);
        $this->loginAttempts = new LoginAttempt($pdo);
    }

    public function showLogin()
    {
        return view('auth/login', ['title' => 'Login']);
    }

    public function showRegister()
    {
        return view('auth/register', ['title' => 'Registrieren']);
    }

    public function login()
    {
        $email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
        $password = $_POST['password'] ?? '';
        $user = $email ? $this->users->findByEmail($email) : null;
        $success = false;
        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['user'] = $user;
            session_regenerate_id(true);
            $success = true;
            $this->loginAttempts->log($email, $_SERVER['REMOTE_ADDR'] ?? 'unknown', true);
            redirect('/');
        }
        $this->loginAttempts->log($email, $_SERVER['REMOTE_ADDR'] ?? 'unknown', false);
        $error = 'Login fehlgeschlagen';
        return view('auth/login', ['title' => 'Login', 'error' => $error]);
    }

    public function register()
    {
        $email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
        $nickname = trim($_POST['nickname'] ?? '');
        $password = $_POST['password'] ?? '';
        $agree = isset($_POST['agree']);
        if (!$email || !$nickname || strlen($password) < 6 || !$agree) {
            $error = 'Bitte alle Felder korrekt ausfüllen und AGB akzeptieren.';
            return view('auth/register', ['title' => 'Registrieren', 'error' => $error]);
        }
        if ($this->users->findByEmail($email)) {
            $error = 'E-Mail bereits vergeben';
            return view('auth/register', ['title' => 'Registrieren', 'error' => $error]);
        }
        $id = $this->users->create([
            'email' => $email,
            'nickname' => $nickname,
            'password_hash' => password_hash($password, PASSWORD_DEFAULT),
            'is_public' => 1,
        ]);
        $_SESSION['user'] = $this->users->findById($id);
        redirect('/');
    }

    public function logout()
    {
        session_destroy();
        redirect('/login');
    }

    public function showForgot()
    {
        return view('auth/forgot', ['title' => 'Passwort vergessen']);
    }

    public function forgot()
    {
        $email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
        if ($email && ($user = $this->users->findByEmail($email))) {
            $token = bin2hex(random_bytes(16));
            $this->users->recordResetToken($email, $token);
            return view('auth/forgot', ['title' => 'Passwort vergessen', 'token' => $token, 'info' => 'Token generiert (für Demo angezeigt).']);
        }
        return view('auth/forgot', ['title' => 'Passwort vergessen', 'error' => 'E-Mail nicht gefunden']);
    }

    public function showReset()
    {
        return view('auth/reset', ['title' => 'Passwort zurücksetzen', 'token' => $_GET['token'] ?? '']);
    }

    public function reset()
    {
        $token = $_POST['token'] ?? '';
        $password = $_POST['password'] ?? '';
        $user = $token ? $this->users->findByResetToken($token) : null;
        if ($user && strlen($password) >= 6) {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $this->users->updatePassword((int)$user['id'], $hash);
            $this->users->consumeResetToken((int)$user['id']);
            return view('auth/login', ['title' => 'Login', 'info' => 'Passwort gesetzt, bitte einloggen.']);
        }
        return view('auth/reset', ['title' => 'Passwort zurücksetzen', 'error' => 'Ungültiger Token oder Passwort.', 'token' => $token]);
    }
}
