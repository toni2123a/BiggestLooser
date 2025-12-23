<?php
namespace App\Controllers;

use App\Services\CsvService;
use App\Models\WeighIn;

class ExportImportController
{
    private CsvService $csv;
    private WeighIn $weighIns;

    public function __construct(\PDO $pdo)
    {
        $this->csv = new CsvService($pdo);
        $this->weighIns = new WeighIn($pdo);
    }

    public function export()
    {
        $entries = $this->weighIns->getByUser(auth_user()['id']);
        $csv = $this->csv->export($entries);
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="weigh_ins.csv"');
        echo $csv;
        exit;
    }

    public function showImport()
    {
        return view('export_import/import', ['title' => 'Import']);
    }

    public function import()
    {
        if (!isset($_FILES['csv']) || $_FILES['csv']['error'] !== UPLOAD_ERR_OK) {
            return view('export_import/import', ['title' => 'Import', 'error' => 'Upload fehlgeschlagen']);
        }
        $content = file_get_contents($_FILES['csv']['tmp_name']);
        $rows = $this->csv->parseImport($content);
        foreach ($rows as $row) {
            if ($row['weight_kg'] < 30 || $row['weight_kg'] > 400) continue;
            $this->weighIns->create([
                'user_id' => auth_user()['id'],
                'date' => $row['date'],
                'weight_kg' => $row['weight_kg'],
                'note' => $row['note'],
                'water_l' => $row['water_l'],
                'steps' => $row['steps'],
            ]);
        }
        return redirect('/weighins');
    }
}
