<?php ob_start(); ?>
<h1>Profil</h1>
<form method="POST" action="<?= e(url('/profile/update')) ?>" class="grid">
    <?= csrf_field(); ?>
    <label>Nickname<br><input type="text" name="nickname" value="<?= e($user['nickname']) ?>" required></label>
    <label>Öffentliches Profil?<br><input type="checkbox" name="is_public" <?= $user['is_public'] ? 'checked' : '' ?>></label>
    <label>Größe (cm)<br><input type="number" name="height_cm" value="<?= e($user['height_cm']) ?>"></label>
    <label>Geburtsjahr<br><input type="number" name="birth_year" value="<?= e($user['birth_year']) ?>"></label>
    <label>Aktivitätslevel<br><input type="text" name="activity_level" value="<?= e($user['activity_level']) ?>"></label>
    <label>Startgewicht<br><input type="number" step="0.1" name="start_weight" value="<?= e($user['start_weight']) ?>"></label>
    <label>Zielgewicht<br><input type="number" step="0.1" name="goal_weight" value="<?= e($user['goal_weight']) ?>"></label>
    <label>Zieldatum<br><input type="date" name="goal_date" value="<?= e($user['goal_date']) ?>"></label>
    <button type="submit">Speichern</button>
</form>
<h2>Passwort ändern</h2>
<form method="POST" action="<?= e(url('/profile/password')) ?>">
    <?= csrf_field(); ?>
    <label>Neues Passwort<br><input type="password" name="password" required></label>
    <button type="submit">Aktualisieren</button>
</form>
<h2>Account löschen</h2>
<form method="POST" action="<?= e(url('/profile/delete')) ?>" onsubmit="return confirm('Wirklich löschen?');">
    <?= csrf_field(); ?>
    <button type="submit" class="danger">Löschen</button>
</form>
<h2>Badges</h2>
<ul class="badges">
    <?php foreach ($badges as $b): ?>
        <li><strong><?= e($b['title']) ?></strong><br><small><?= e($b['description']) ?></small></li>
    <?php endforeach; ?>
</ul>
<?php $content = ob_get_clean(); include __DIR__ . '/../layout.php'; ?>
