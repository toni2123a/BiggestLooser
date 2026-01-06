<?php ob_start(); ?>
<h1>Fortschrittsfotos</h1>
<form method="POST" action="<?= e(url('/photos/upload')) ?>" enctype="multipart/form-data">
    <?= csrf_field(); ?>
    <label>Datum<br><input type="date" name="date" value="<?= date('Y-m-d') ?>"></label>
    <label>Notiz<br><input type="text" name="note"></label>
    <label>Foto (jpg/png/webp, max 2MB)<br><input type="file" name="photo" accept="image/*" required></label>
    <button type="submit">Hochladen</button>
</form>
<div class="photo-grid">
    <?php foreach ($photos as $p): ?>
        <figure>
            <img src="<?= e(url('/uploads/' . auth_user()['id'] . '/' . $p['filename'])) ?>" alt="Foto">
            <figcaption><?= e($p['date']) ?> - <?= e($p['note']) ?></figcaption>
        </figure>
    <?php endforeach; ?>
</div>
<?php $content = ob_get_clean(); include __DIR__ . '/../layout.php'; ?>
