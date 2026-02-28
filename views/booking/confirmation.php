<div class="container confirmation-page">

    <div class="confirmation-hero">
        <div class="confirmation-icon">🎉</div>
        <h1>Booking Confirmed!</h1>
        <p>Your seats are reserved. See you at the cinema!</p>
    </div>

    <div class="confirmation-card">
        <h2><?= View::e($booking['movie_title']) ?></h2>

        <div class="confirmation-details">
            <div class="detail-row">
                <span>📍 Cinema</span>
                <strong><?= View::e($booking['cinema_name']) ?>, <?= View::e($booking['city']) ?></strong>
            </div>
            <div class="detail-row">
                <span>🎭 Hall</span>
                <strong><?= View::e($booking['hall_name']) ?></strong>
            </div>
            <div class="detail-row">
                <span>🗓 Date &amp; Time</span>
                <strong><?= date('D j M Y, H:i', strtotime($booking['start_time'])) ?></strong>
            </div>
            <div class="detail-row">
                <span>🎟 Seats</span>
                <strong>
                    <?php foreach ($tickets as $ticket): ?>
                        <?= View::e($ticket['row_label']) ?><?= $ticket['seat_number'] ?>
                        <?= !$loop_last ? ', ' : '' ?>
                    <?php endforeach; ?>
                </strong>
            </div>
            <div class="detail-row">
                <span>💳 Total Paid</span>
                <strong>£<?= number_format((float)$booking['total_amount'], 2) ?></strong>
            </div>
            <div class="detail-row">
                <span>📋 Booking Ref</span>
                <strong>#<?= str_pad((string)$booking['booking_id'], 6, '0', STR_PAD_LEFT) ?></strong>
            </div>
        </div>
    </div>

    <div class="confirmation-actions">
        <a href="<?= APP_URL ?>/bookings" class="btn btn--outline">View All Bookings</a>
        <a href="<?= APP_URL ?>/movies" class="btn btn--primary">Browse More Movies</a>
    </div>

</div>
