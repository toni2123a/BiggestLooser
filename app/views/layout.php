<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? APP_NAME) ?></title>
    <link rel="stylesheet" href="<?= e(url('/assets/css/app.css')) ?>">
    <script src="<?= e(url('/assets/js/chart.min.js')) ?>"></script>
</head>
<body>
<nav class="nav">
    <div class="nav-left">Abnehm-App</div>
    <div class="nav-right">
        <?php if (auth_user()): ?>
            <a href="<?= e(url('/')) ?>">Dashboard</a>
            <a href="<?= e(url('/weighins')) ?>">Gewicht</a>
            <a href="<?= e(url('/badges')) ?>">Badges</a>
            <a href="<?= e(url('/leaderboards')) ?>">Ranglisten</a>
            <a href="<?= e(url('/challenges')) ?>">Challenges</a>
            <a href="<?= e(url('/groups')) ?>">Gruppen</a>
            <a href="<?= e(url('/photos')) ?>">Fotos</a>
            <a href="<?= e(url('/profile')) ?>">Profil</a>
            <a href="<?= e(url('/logout')) ?>">Logout</a>
        <?php else: ?>
            <a href="<?= e(url('/login')) ?>">Login</a>
            <a href="<?= e(url('/register')) ?>">Registrieren</a>
        <?php endif; ?>
    </div>
</nav>
<main class="container">
    <?php if (!empty($error)): ?><div class="alert error"><?= e($error) ?></div><?php endif; ?>
    <?php if (!empty($info)): ?><div class="alert success"><?= e($info) ?></div><?php endif; ?>
    <?php if (!empty($_SESSION['flash_warning'])): ?><div class="alert warn"><?php echo e($_SESSION['flash_warning']); unset($_SESSION['flash_warning']); ?></div><?php endif; ?>
    <?= $content ?? '' ?>
</main>
</body>
</html>
