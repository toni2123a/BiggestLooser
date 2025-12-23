<?php ob_start(); ?>
<h1>Gruppen</h1>
<section class="grid">
    <div class="card">
        <h3>Neue Gruppe</h3>
        <form method="POST" action="/groups/create">
            <?= csrf_field(); ?>
            <label>Name<br><input type="text" name="name" required></label>
            <button type="submit">Erstellen</button>
        </form>
    </div>
    <div class="card">
        <h3>Beitritt</h3>
        <form method="POST" action="/groups/join">
            <?= csrf_field(); ?>
            <label>Einladungscode<br><input type="text" name="invite_code" required></label>
            <button type="submit">Beitreten</button>
        </form>
    </div>
</section>
<table>
    <thead><tr><th>Name</th><th>Owner</th><th>Invite-Code</th></tr></thead>
    <tbody>
    <?php foreach ($groups as $g): ?>
        <tr>
            <td><?= e($g['name']) ?></td>
            <td><?= e($g['owner_nick']) ?></td>
            <td><?= e($g['invite_code']) ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<?php $content = ob_get_clean(); include __DIR__ . '/../layout.php'; ?>
