<div class="container">
    <div class="screening-detail">
        <h1><?= View::e($screening['title']) ?></h1>
        <p class="screening-meta">
            📍 <?= View::e($screening['cinema_name']) ?>, <?= View::e($screening['city']) ?>
            &nbsp;·&nbsp; <?= View::e($screening['hall_name']) ?>
            &nbsp;·&nbsp; 🕐 <?= date('D j M Y, H:i', strtotime($screening['start_time'])) ?>
        </p>

        <!-- Seat map -->
        <section class="seat-map-section">
            <h2>Choose Your Seats</h2>
            <div class="seat-legend">
                <span class="seat seat--available">Available</span>
                <span class="seat seat--taken">Taken</span>
                <span class="seat seat--selected">Selected</span>
            </div>

            <div class="screen-label">SCREEN</div>

            <form action="<?= APP_URL ?>/bookings" method="POST" id="booking-form">
                <?= Csrf::field() ?>
                <input type="hidden" name="screening_id" value="<?= $screening['screening_id'] ?>">

                <?php
                    $rows = [];
                    foreach ($seats as $seat) {
                        $rows[$seat['row_label']][] = $seat;
                    }
                ?>

                <div class="seat-map">
                    <?php foreach ($rows as $rowLabel => $rowSeats): ?>
                        <div class="seat-row">
                            <span class="row-label"><?= View::e($rowLabel) ?></span>
                            <?php foreach ($rowSeats as $seat): ?>
                                <?php if ($seat['is_available']): ?>
                                    <label class="seat seat--available" title="Row <?= $rowLabel ?> Seat <?= $seat['seat_number'] ?>">
                                        <input type="checkbox" name="seat_ids[]" value="<?= $seat['seat_id'] ?>" class="seat-checkbox">
                                        <?= $seat['seat_number'] ?>
                                    </label>
                                <?php else: ?>
                                    <span class="seat seat--taken" title="Taken"><?= $seat['seat_number'] ?></span>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="booking-summary" id="booking-summary" style="display:none">
                    <p>Selected: <strong id="seat-count">0</strong> seat(s) · Total: <strong id="seat-total">£0.00</strong></p>
                    <button type="submit" class="btn btn--primary btn--lg">Confirm Booking</button>
                </div>
            </form>
        </section>
    </div>
</div>
