<div class="container">
    <h1>Booking #<?= str_pad((string)$booking['booking_id'], 6, '0', STR_PAD_LEFT) ?></h1>

    <div class="booking-detail-card">
        <h2><?= View::e($booking['movie_title']) ?></h2>

        <div class="detail-grid">
            <div class="detail-item">
                <span class="label">Cinema</span>
                <span><?= View::e($booking['cinema_name']) ?>, <?= View::e($booking['city']) ?></span>
            </div>
            <div class="detail-item">
                <span class="label">Hall</span>
                <span><?= View::e($booking['hall_name']) ?></span>
            </div>
            <div class="detail-item">
                <span class="label">Date &amp; Time</span>
                <span><?= date('D j M Y, H:i', strtotime($booking['start_time'])) ?></span>
            </div>
            <div class="detail-item">
                <span class="label">Status</span>
                <span class="status-badge status-badge--<?= strtolower(View::e($booking['status'])) ?>">
                    <?= View::e($booking['status']) ?>
                </span>
            </div>
            <div class="detail-item">
                <span class="label">Total</span>
                <span>£<?= number_format((float)$booking['total_amount'], 2) ?></span>
            </div>
        </div>

        <h3>Tickets</h3>
        <table class="table">
            <thead>
                <tr>
                    <th>Ticket #</th>
                    <th>Seat</th>
                    <th>Price</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tickets as $i => $ticket): ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td><?= View::e($ticket['row_label']) ?><?= $ticket['seat_number'] ?></td>
                        <td>£<?= number_format((float)$ticket['price'], 2) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="actions">
        <a href="<?= APP_URL ?>/bookings" class="btn btn--outline">← Back to My Bookings</a>
        <?php if ($booking['status'] === 'CONFIRMED'): ?>
            <form action="<?= APP_URL ?>/bookings/<?= $booking['booking_id'] ?>/cancel" method="POST" class="inline-form">
                <?= Csrf::field() ?>
                <button type="submit" class="btn btn--danger"
                        onclick="return confirm('Are you sure you want to cancel this booking?')">
                    Cancel Booking
                </button>
            </form>
        <?php endif; ?>
    </div>
</div>
