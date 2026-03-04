<div class="container">
    <div class="movie-detail">
        <?php if(!empty($movie['poster_url'])): ?>
            <?php
                $posterSrc = $movie['poster_url'];
                if (str_starts_with($posterSrc, '/')) {
                    $posterSrc = APP_URL . $posterSrc;
                }
            ?>
            <div class="movie-detail__poster">
                <img src="<?= View::e($posterSrc) ?>" alt="<?= View::e($movie['title']) ?> Poster" referrerpolicy="no-referrer">
            </div>
        <?php endif; ?>
        <div class="movie-detail__info">
            <span class="badge"><?= View::e($movie['age_rating']) ?></span>
            <h1 class="movie-detail__title"><?= View::e($movie['title']) ?></h1>

            <div class="movie-detail__meta">
                <span>⏱ <?= $movie['duration_minutes'] ?> min</span>
                <?php if ($movie['movie_rating']): ?>
                    <span>⭐ <?= number_format((float)$movie['movie_rating'], 1) ?> / 10</span>
                <?php endif; ?>
            </div>

            <?php if ($movie['description']): ?>
                <p class="movie-detail__desc"><?= View::e($movie['description']) ?></p>
            <?php endif; ?>
        </div>
    </div>

        <!-- Screenings -->
        <section class="screenings-section">
            <h2>Available Screenings</h2>

            <?php if (empty($screenings)): ?>
                <p class="empty-state">No upcoming screenings are scheduled for this film.</p>
            <?php else: ?>
                <div class="screening-list">
                    <?php foreach ($screenings as $s): ?>
                        <div class="screening-card">
                            <div class="screening-card__venue">
                                <strong><?= View::e($s['cinema_name']) ?></strong>
                                <span><?= View::e($s['city']) ?> · <?= View::e($s['hall_name']) ?></span>
                            </div>
                            <div class="screening-card__time">
                                <span class="date"><?= date('D j M', strtotime($s['start_time'])) ?></span>
                                <span class="time"><?= date('H:i', strtotime($s['start_time'])) ?></span>
                            </div>
                            <a href="<?= APP_URL ?>/screenings/<?= $s['screening_id'] ?>/book"
                               class="btn btn--primary btn--sm">
                                Book
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
    </div>
</div>
