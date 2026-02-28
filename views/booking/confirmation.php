<div class="container confirmation-page">

    <div class="confirmation-hero">
        <div class="confirmation-icon">🎉</div>
        <h1>Booking Confirmed!</h1>
        <p>Your seats are reserved. See you at the cinema!</p>
    </div>

    <div class="confirmation-card">
        <div class="ticket-header">
            <h2><?= View::e($booking['movie_title']) ?></h2>
            <span class="ticket-badge">VIP TICKET</span>
        </div>

        <div class="ticket-divider"></div>

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
                    <?php 
                        $seatLabels = array_map(function($ticket) {
                            return View::e($ticket['row_label']) . $ticket['seat_number'];
                        }, $tickets);
                        echo implode(', ', $seatLabels);
                    ?>
                </strong>
            </div>
            <div class="detail-row">
                <span>💳 <?= $payment ? 'Total Paid' : 'Total Due' ?></span>
                <strong>£<?= number_format((float)$booking['total_amount'], 2) ?></strong>
            </div>
            <div class="detail-row">
                <span>📋 Booking Ref</span>
                <strong>#<?= str_pad((string)$booking['booking_id'], 6, '0', STR_PAD_LEFT) ?></strong>
            </div>
            <div class="detail-row">
                <span>💳 Status</span>
                <?php if($payment): ?>
                    <span class="status-badge status-badge--confirmed">PAID</span>
                <?php else: ?>
                    <span class="status-badge status-badge--pending" style="color:var(--c-warning); font-weight: bold;">UNPAID</span>
                <?php endif; ?>
            </div>
        </div>

        <div class="ticket-footer">
            <div class="barcode">
                <span>||||||||||||||||||||||||||||||||</span>
            </div>
        </div>
    </div>

    <div class="confirmation-actions">
        <?php if(!$payment && $booking['status'] !== 'CANCELLED'): ?>
            <form action="<?= APP_URL ?>/payments/checkout" method="POST" style="width: 100%; display: flex; gap: var(--sp-md);">
                <?= Csrf::field() ?>
                <input type="hidden" name="booking_id" value="<?= $booking['booking_id'] ?>">
                <button type="submit" class="btn btn--primary" style="flex:1;">Pay Now (£<?= number_format((float)$booking['total_amount'], 2) ?>)</button>
                <a href="<?= APP_URL ?>/bookings" class="btn btn--outline" style="flex:1; text-align: center;">Pay Later</a>
            </form>
        <?php else: ?>
            <a href="<?= APP_URL ?>/bookings" class="btn btn--outline">View All Bookings</a>
            <a href="<?= APP_URL ?>/movies" class="btn btn--primary">Browse More Movies</a>
        <?php endif; ?>
    </div>

</div>
