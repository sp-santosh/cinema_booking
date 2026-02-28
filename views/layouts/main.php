<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= View::e($pageTitle ?? 'CineBook') ?></title>
    <meta name="description" content="<?= View::e($metaDescription ?? 'Book your cinema tickets online with CineBook.') ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/app.css">
</head>
<body>

<!-- ── Navigation ──────────────────────────────────────────────── -->
<header class="navbar">
    <div class="container navbar__inner">
        <a href="<?= APP_URL ?>/" class="navbar__brand">
            🎬 <span>CineBook</span>
        </a>

        <nav class="navbar__links">
            <a href="<?= APP_URL ?>/movies">Movies</a>
            <?php if (Auth::check()): ?>
                <a href="<?= APP_URL ?>/bookings">My Bookings</a>
                <?php if (Auth::isAdmin()): ?>
                    <a href="<?= APP_URL ?>/admin" class="btn btn--sm btn--outline">Admin</a>
                <?php endif; ?>
                <form action="<?= APP_URL ?>/logout" method="POST" style="display:inline">
                    <?= Csrf::field() ?>
                    <button type="submit" class="btn btn--sm btn--ghost">Log out</button>
                </form>
            <?php else: ?>
                <a href="<?= APP_URL ?>/login" class="btn btn--sm btn--outline">Log in</a>
                <a href="<?= APP_URL ?>/register" class="btn btn--sm btn--primary">Register</a>
            <?php endif; ?>
        </nav>
    </div>
</header>

<!-- ── Flash messages ───────────────────────────────────────────── -->
<?php if (!empty($_SESSION['flash'])): ?>
    <?php foreach ($_SESSION['flash'] as $type => $msg): ?>
        <div class="alert alert--<?= View::e($type) ?>">
            <?= View::e($msg) ?>
        </div>
    <?php endforeach; ?>
    <?php unset($_SESSION['flash']); ?>
<?php endif; ?>

<!-- ── Main content ─────────────────────────────────────────────── -->
<main class="main">
    <?= $content ?>
</main>

<!-- ── Footer ───────────────────────────────────────────────────── -->
<footer class="footer">
    <div class="container">
        <p>&copy; <?= date('Y') ?> CineBook. All rights reserved.</p>
    </div>
</footer>

<script src="<?= APP_URL ?>/assets/js/app.js"></script>
</body>
</html>
