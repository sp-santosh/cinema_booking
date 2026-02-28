<div class="admin-layout">

    <!-- Sidebar -->
    <aside class="admin-sidebar">
        <div class="admin-brand">🎬 CineBook Admin</div>
        <nav class="admin-nav">
            <a href="<?= APP_URL ?>/admin" class="admin-nav__item">📊 Dashboard</a>
            <a href="<?= APP_URL ?>/admin/movies" class="admin-nav__item">🎬 Movies</a>
            <a href="<?= APP_URL ?>/admin/screenings" class="admin-nav__item">🗓 Screenings</a>
            <a href="<?= APP_URL ?>/admin/bookings" class="admin-nav__item">🎟 Bookings</a>
            <a href="<?= APP_URL ?>/admin/users" class="admin-nav__item">👥 Users</a>
            <hr>
            <a href="<?= APP_URL ?>/" class="admin-nav__item">← Back to Site</a>
        </nav>
    </aside>

    <!-- Main -->
    <div class="admin-main">
        <header class="admin-header">
            <h1><?= View::e($pageTitle ?? 'Dashboard') ?></h1>
            <span><?= View::e(Auth::user()['first_name'] . ' ' . Auth::user()['last_name']) ?></span>
        </header>

        <!-- Flash -->
        <?php if (!empty($_SESSION['flash'])): ?>
            <?php foreach ($_SESSION['flash'] as $type => $msg): ?>
                <div class="alert alert--<?= View::e($type) ?>"><?= View::e($msg) ?></div>
            <?php endforeach; ?>
            <?php unset($_SESSION['flash']); ?>
        <?php endif; ?>

        <!-- KPI cards -->
        <div class="kpi-grid">
            <div class="kpi-card">
                <div class="kpi-card__value"><?= $stats['total_movies'] ?></div>
                <div class="kpi-card__label">Active Movies</div>
            </div>
            <div class="kpi-card">
                <div class="kpi-card__value"><?= $stats['total_users'] ?></div>
                <div class="kpi-card__label">Registered Users</div>
            </div>
            <div class="kpi-card">
                <div class="kpi-card__value"><?= $stats['total_bookings'] ?></div>
                <div class="kpi-card__label">Total Bookings</div>
            </div>
            <div class="kpi-card kpi-card--highlight">
                <div class="kpi-card__value">£<?= number_format($stats['total_revenue'], 2) ?></div>
                <div class="kpi-card__label">Total Revenue</div>
            </div>
        </div>

        <!-- Recent bookings -->
        <section class="admin-section">
            <div class="section-header">
                <h2>Recent Bookings</h2>
                <a href="<?= APP_URL ?>/admin/bookings" class="btn btn--sm btn--outline">View All</a>
            </div>
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Customer</th>
                        <th>Movie</th>
                        <th>Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentBookings as $b): ?>
                        <tr>
                            <td><?= $b['booking_id'] ?></td>
                            <td><?= View::e($b['first_name'] . ' ' . $b['last_name']) ?></td>
                            <td><?= View::e($b['movie_title']) ?></td>
                            <td><?= date('j M Y', strtotime($b['start_time'])) ?></td>
                            <td>
                                <span class="status-badge status-badge--<?= strtolower($b['status']) ?>">
                                    <?= View::e($b['status']) ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>
    </div>

</div>
