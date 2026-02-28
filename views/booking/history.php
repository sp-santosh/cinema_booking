<div class="container">
    <h1>My Bookings</h1>

    <?php if (empty($bookings)): ?>
        <div class="empty-state">
            <p>You have no bookings yet.</p>
            <a href="<?= APP_URL ?>/movies" class="btn btn--primary">Browse Movies</a>
        </div>
    <?php else: ?>
        <div class="booking-list">
            <?php foreach ($bookings as $b): ?>
                <div class="booking-card booking-card--<?= strtolower(View::e($b['status'])) ?>">
                    <div class="booking-card__info">
                        <h3><?= View::e($b['movie_title']) ?></h3>
                        <p><?= View::e($b['cinema_name']) ?> · <?= View::e($b['hall_name']) ?></p>
                        <p>🗓 <?= date('D j M Y, H:i', strtotime($b['start_time'])) ?></p>
                    </div>
                    <div class="booking-card__status">
                        <span class="status-badge status-badge--<?= strtolower(View::e($b['status'])) ?>">
                            <?= View::e($b['status']) ?>
                        </span>
                        <p class="booking-card__amount">£<?= number_format((float)$b['total_amount'], 2) ?></p>
                    </div>
                    <div class="booking-card__actions">
                        <a href="<?= APP_URL ?>/bookings/<?= $b['booking_id'] ?>" class="btn btn--sm btn--outline">View</a>
                        <?php if ($b['status'] === 'CONFIRMED'): ?>
                            <form action="<?= APP_URL ?>/bookings/<?= $b['booking_id'] ?>/cancel" method="POST" class="inline-form">
                                <?= Csrf::field() ?>
                                <button type="submit" class="btn btn--sm btn--danger"
                                        onclick="return confirm('Cancel this booking?')">Cancel</button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
