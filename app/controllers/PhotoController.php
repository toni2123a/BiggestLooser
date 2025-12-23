<?php
namespace App\Controllers;

use App\Services\PhotoService;
use App\Models\Photo;

class PhotoController
{
    private PhotoService $service;
    private Photo $photo;

    public function __construct(\PDO $pdo)
    {
        $this->service = new PhotoService($pdo);
        $this->photo = new Photo($pdo);
    }

    public function index()
    {
        $photos = $this->photo->byUser(auth_user()['id']);
        return view('photos/index', ['title' => 'Fortschrittsfotos', 'photos' => $photos]);
    }

    public function upload()
    {
        $date = $_POST['date'] ?? date('Y-m-d');
        $note = trim($_POST['note'] ?? '');
        $error = $this->service->upload(auth_user()['id'], $_FILES['photo'], $date, $note);
        return $error ? view('photos/index', ['title' => 'Fortschrittsfotos', 'photos' => $this->photo->byUser(auth_user()['id']), 'error' => $error]) : redirect('/photos');
    }
}
