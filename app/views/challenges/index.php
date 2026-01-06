<?php ob_start(); ?>
<h1>Challenges</h1>
<h3>Verfügbare Herausforderungen</h3>
<ul>
    <?php foreach ($challenges as $c): ?>
        <li>
            <strong><?= e($c['title']) ?></strong> (<?= e($c['code']) ?>)<br>
            <?= e($c['description']) ?>
            <form method="POST" action="<?= e(url('/challenges/join')) ?>" style="margin-top:6px;">
                <?= csrf_field(); ?>
                <input type="hidden" name="challenge_id" value="<?= e($c['id']) ?>">
                <button type="submit">Teilnehmen</button>
            </form>
        </li>
    <?php endforeach; ?>
</ul>
<h3>Dein Fortschritt</h3>
<ul>
    <?php foreach ($userChallenges as $uc): ?>
        <li><?= e($uc['title']) ?> – <?= e($uc['progress']['percent'] ?? 0) ?>% (Status: <?= e($uc['status']) ?>)</li>
    <?php endforeach; ?>
</ul>
<?php $content = ob_get_clean(); include __DIR__ . '/../layout.php'; ?>
