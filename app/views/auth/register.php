<?php ob_start(); ?>
<h1>Registrieren</h1>
<form method="POST" action="<?= e(url('/register')) ?>">
    <?= csrf_field(); ?>
    <label>E-Mail<br><input type="email" name="email" required></label>
    <label>Nickname (öffentlich)<br><input type="text" name="nickname" required></label>
    <label>Passwort (min. 6 Zeichen)<br><input type="password" name="password" required></label>
    <label><input type="checkbox" name="agree" required> Ich stimme Datenschutz & AGB zu</label>
    <button type="submit">Account erstellen</button>
</form>
<?php $content = ob_get_clean(); include __DIR__ . '/../layout.php'; ?>
