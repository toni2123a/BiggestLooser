<?php ob_start(); ?>
<h1>Deine Auszeichnungen</h1>
<ul class="badges">
    <?php foreach ($badges as $b): ?>
        <li><strong><?= e($b['title']) ?></strong><br><?= e($b['description']) ?><br><small><?= e($b['awarded_at']) ?></small></li>
    <?php endforeach; ?>
</ul>
<?php $content = ob_get_clean(); include __DIR__ . '/../layout.php'; ?>
