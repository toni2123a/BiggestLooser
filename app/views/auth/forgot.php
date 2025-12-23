<?php ob_start(); ?>
<h1>Passwort vergessen</h1>
<form method="POST" action="/password/forgot">
    <?= csrf_field(); ?>
    <label>E-Mail<br><input type="email" name="email" required></label>
    <button type="submit">Token anfordern</button>
</form>
<?php if (!empty($token)): ?>
<p>Dev-Hinweis: Token: <code><?= e($token) ?></code></p>
<?php endif; ?>
<?php $content = ob_get_clean(); include __DIR__ . '/../layout.php'; ?>
