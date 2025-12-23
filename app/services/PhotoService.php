<?php
namespace App\Services;

use App\Models\Photo;

class PhotoService
{
    private Photo $photo;
    public function __construct(\PDO $pdo)
    {
        $this->photo = new Photo($pdo);
    }

    public function upload(int $userId, array $file, string $date, ?string $note): ?string
    {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return 'Upload fehlgeschlagen.';
        }
        if ($file['size'] > MAX_UPLOAD_SIZE) {
            return 'Datei zu groß.';
        }
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($file['tmp_name']);
        $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
        if (!isset($allowed[$mime])) {
            return 'Ungültiger Dateityp.';
        }
        $ext = $allowed[$mime];
        $dir = UPLOAD_DIR . '/' . $userId;
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        $filename = bin2hex(random_bytes(8)) . '.' . $ext;
        $path = $dir . '/' . $filename;
        if (!move_uploaded_file($file['tmp_name'], $path)) {
            return 'Konnte Datei nicht speichern.';
        }
        $this->photo->create($userId, $date, $filename, $note);
        return null;
    }
}
