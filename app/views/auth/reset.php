<?php ob_start(); ?>
<h1>Passwort zurücksetzen</h1>
<form method="POST" action="/password/reset">
    <?= csrf_field(); ?>
    <input type="hidden" name="token" value="<?= e($token ?? '') ?>">
    <label>Neues Passwort<br><input type="password" name="password" required></label>
    <button type="submit">Speichern</button>
</form>
<?php $content = ob_get_clean(); include __DIR__ . '/../layout.php'; ?>
