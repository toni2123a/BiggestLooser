<?php
namespace App\Services;

use App\Models\WeighIn;

class CsvService
{
    private WeighIn $weighIn;
    public function __construct(\PDO $pdo)
    {
        $this->weighIn = new WeighIn($pdo);
    }

    public function export(array $entries): string
    {
        $fh = fopen('php://temp', 'w+');
        fputcsv($fh, ['datum', 'gewicht', 'notiz', 'wasser_l', 'schritte']);
        foreach ($entries as $entry) {
            fputcsv($fh, [$entry['date'], $entry['weight_kg'], $entry['note'], $entry['water_l'], $entry['steps']]);
        }
        rewind($fh);
        return stream_get_contents($fh);
    }

    public function parseImport(string $csv): array
    {
        $rows = [];
        $fh = fopen('php://temp', 'r+');
        fwrite($fh, $csv);
        rewind($fh);
        $header = fgetcsv($fh);
        while (($data = fgetcsv($fh)) !== false) {
            $rows[] = [
                'date' => $data[0] ?? '',
                'weight_kg' => (float)($data[1] ?? 0),
                'note' => $data[2] ?? null,
                'water_l' => isset($data[3]) ? (float)$data[3] : null,
                'steps' => isset($data[4]) ? (int)$data[4] : null,
            ];
        }
        return $rows;
    }
}
