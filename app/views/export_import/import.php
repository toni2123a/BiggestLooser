<?php ob_start(); ?>
<h1>CSV Import</h1>
<form method="POST" action="<?= e(url('/weighins/import')) ?>" enctype="multipart/form-data">
    <?= csrf_field(); ?>
    <label>CSV-Datei<br><input type="file" name="csv" accept="text/csv" required></label>
    <button type="submit">Import starten</button>
</form>
<p>Spalten: datum, gewicht, notiz, wasser_l, schritte</p>
<?php $content = ob_get_clean(); include __DIR__ . '/../layout.php'; ?>
