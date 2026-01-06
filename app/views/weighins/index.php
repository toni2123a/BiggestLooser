<?php ob_start(); ?>
<h1>Gewichtseinträge</h1>
<form method="GET" class="row">
    <label>Von <input type="date" name="from" value="<?= e($_GET['from'] ?? '') ?>"></label>
    <label>Bis <input type="date" name="to" value="<?= e($_GET['to'] ?? '') ?>"></label>
    <button type="submit">Filtern</button>
    <a class="button" href="<?= e(url('/weighins/export')) ?>">Export CSV</a>
    <a class="button" href="<?= e(url('/import')) ?>">Import</a>
</form>
<table>
    <thead><tr><th>Datum</th><th>Gewicht</th><th>Notiz</th><th>Wasser</th><th>Schritte</th></tr></thead>
    <tbody>
    <?php foreach ($entries as $e): ?>
        <tr>
            <td><?= e($e['date']) ?></td>
            <td><?= e($e['weight_kg']) ?> kg</td>
            <td><?= e($e['note']) ?></td>
            <td><?= e($e['water_l']) ?> L</td>
            <td><?= e($e['steps']) ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<?php $content = ob_get_clean(); include __DIR__ . '/../layout.php'; ?>
