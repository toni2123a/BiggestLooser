<?php ob_start(); ?>
<h1>Login</h1>
<form method="POST" action="/login">
    <?= csrf_field(); ?>
    <label>E-Mail<br><input type="email" name="email" required></label>
    <label>Passwort<br><input type="password" name="password" required></label>
    <button type="submit">Einloggen</button>
</form>
<p><a href="/password/forgot">Passwort vergessen?</a></p>
<?php $content = ob_get_clean(); include __DIR__ . '/../layout.php'; ?>
