<?php ob_start(); ?>
<h1>Ranglisten</h1>
<form method="GET" class="row">
    <label>Zeitraum
        <select name="range">
            <option value="30" <?= $range==30?'selected':'' ?>>30 Tage</option>
            <option value="90" <?= $range==90?'selected':'' ?>>90 Tage</option>
            <option value="365" <?= $range==365?'selected':'' ?>>365 Tage</option>
            <option value="0" <?= $range==0?'selected':'' ?>>Gesamt</option>
        </select>
    </label>
    <button type="submit">Aktualisieren</button>
</form>
<table>
    <thead><tr><th>#</th><th>Nickname</th><th>% Abnahme</th><th>Streak</th><th>Badges</th><th>BMI Kategorie</th><th>Konstanz</th></tr></thead>
    <tbody>
    <?php $i=1; foreach ($data as $row): ?>
        <tr>
            <td><?= $i++ ?></td>
            <td><?= e($row['nickname']) ?></td>
            <td><?= e($row['percent']) ?>%</td>
            <td><?= e($row['streak']) ?> Tage</td>
            <td><?= e($row['badges']) ?></td>
            <td><?= e($row['bmi_category'] ?? 'n/a') ?></td>
            <td><?= e($row['entries']) ?> Einträge</td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<?php $content = ob_get_clean(); include __DIR__ . '/../layout.php'; ?>
