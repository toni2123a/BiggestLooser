<?php ob_start(); ?>
<h1>Hallo <?= e($user['nickname']) ?></h1>
<section class="grid">
    <div class="card">
        <h3>Letztes Gewicht</h3>
        <p><?= $latest ? e($latest['weight_kg']) . ' kg am ' . e($latest['date']) : 'Noch kein Eintrag' ?></p>
    </div>
    <div class="card">
        <h3>Trend gesamt</h3>
        <p><?= e($trend) ?> kg seit Start</p>
    </div>
    <div class="card">
        <h3>Durchschnitt (7 Tage)</h3>
        <p><?= $avg7 ? e($avg7) . ' kg' : 'n/a' ?></p>
    </div>
</section>
<section class="card">
    <h3>Schnell-Eintrag</h3>
    <form method="POST" action="<?= e(url('/weighins/create')) ?>">
        <?= csrf_field(); ?>
        <div class="row">
            <label>Datum<br><input type="date" name="date" value="<?= date('Y-m-d') ?>"></label>
            <label>Gewicht (kg)<br><input type="number" step="0.1" name="weight_kg" required></label>
            <label>Notiz<br><input type="text" name="note"></label>
            <label>Wasser (L)<br><input type="number" step="0.1" name="water_l"></label>
            <label>Schritte<br><input type="number" name="steps"></label>
        </div>
        <button type="submit">Speichern</button>
    </form>
</section>
<section class="card">
    <h3>Verlauf</h3>
    <canvas id="chart" height="120"></canvas>
</section>
<section class="card">
    <h3>Challenges</h3>
    <ul>
        <?php foreach ($challenges as $c): ?>
            <li><?= e($c['title']) ?> – Fortschritt: <?= e($c['progress']['percent'] ?? 0) ?>%</li>
        <?php endforeach; ?>
    </ul>
</section>
<script>
const ctx = document.getElementById('chart');
const labels = <?= json_encode(array_column(array_reverse($entries), 'date')) ?>;
const data = <?= json_encode(array_column(array_reverse($entries), 'weight_kg')) ?>;
if (labels.length > 0) {
    new Chart(ctx, {
        type: 'line',
        data: {labels, datasets: [{label: 'Gewicht', data, borderColor: '#3b82f6', fill:false}]},
        options: {scales: {y: {beginAtZero:false}}}
    });
}
</script>
<?php $content = ob_get_clean(); include __DIR__ . '/../layout.php'; ?>
