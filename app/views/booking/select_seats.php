<div class="seat-map-section container">
    <h1>Select Your Seats</h1>
    <!-- Reuse the screenings/show seat-selection form -->
    <?php View::partial('screenings/show', ['screening' => $screening, 'seats' => $seats]); ?>
</div>
