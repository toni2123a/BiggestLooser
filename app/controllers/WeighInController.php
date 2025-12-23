<?php
namespace App\Controllers;

use App\Models\WeighIn;
use App\Services\BadgeService;

class WeighInController
{
    private \PDO $pdo;
    private WeighIn $weighIns;
    private BadgeService $badgeService;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->weighIns = new WeighIn($pdo);
        $this->badgeService = new BadgeService($pdo);
    }

    public function index()
    {
        $from = $_GET['from'] ?? null;
        $to = $_GET['to'] ?? null;
        $entries = $this->weighIns->getByUser(auth_user()['id'], $from, $to);
        return view('weighins/index', ['title' => 'Gewichtsverlauf', 'entries' => $entries]);
    }

    public function create()
    {
        $user = auth_user();
        $date = $_POST['date'] ?? date('Y-m-d');
        $weight = (float)($_POST['weight_kg'] ?? 0);
        $note = trim($_POST['note'] ?? '');
        $water = $_POST['water_l'] !== '' ? (float)$_POST['water_l'] : null;
        $steps = $_POST['steps'] !== '' ? (int)$_POST['steps'] : null;
        if ($weight < 30 || $weight > 400) {
            return view('weighins/index', ['title' => 'Gewicht', 'error' => 'Bitte plausibles Gewicht angeben.', 'entries' => $this->weighIns->getByUser($user['id'])]);
        }
        if ($date > date('Y-m-d')) {
            return view('weighins/index', ['title' => 'Gewicht', 'error' => 'Datum darf nicht in der Zukunft liegen.', 'entries' => $this->weighIns->getByUser($user['id'])]);
        }
        $entries = $this->weighIns->getByUser($user['id']);
        if ($entries) {
            $last = $entries[0];
            if (abs($last['weight_kg'] - $weight) > 3) {
                // simple warning flag shown in view
                $_SESSION['flash_warning'] = 'Großer Sprung! Wir haben trotzdem gespeichert.';
            }
        }
        $this->weighIns->create([
            'user_id' => $user['id'],
            'date' => $date,
            'weight_kg' => $weight,
            'note' => $note,
            'water_l' => $water,
            'steps' => $steps,
        ]);
        $this->badgeService->evaluate($user['id'], $user);
        redirect('/weighins');
    }
}
