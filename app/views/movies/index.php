<div class="container">

    <!-- Hero -->
    <section class="hero">
        <h1 class="hero__title">Book Your Perfect Cinema Experience</h1>
        <p class="hero__sub">Browse now-showing films and reserve your seats in seconds.</p>
        <form action="<?= APP_URL ?>/movies" method="GET" class="hero__search">
            <input type="search" name="search" placeholder="Search for a movie…" value="<?= View::e($search ?? '') ?>">
            <button type="submit" class="btn btn--primary">Search</button>
        </form>
    </section>

    <!-- Movie grid -->
    <section class="section">
        <h2 class="section__title">
            <?= !empty($search) ? 'Results for "' . View::e($search) . '"' : 'Now Showing' ?>
        </h2>

        <?php if (empty($movies)): ?>
            <p class="empty-state">No movies found. <a href="<?= APP_URL ?>/movies">Clear search</a></p>
        <?php else: ?>
            <div class="movie-grid">
                <?php foreach ($movies as $movie): ?>
                    <a href="<?= APP_URL ?>/movies/<?= $movie['movie_id'] ?>" class="movie-card">
                        <?php if(!empty($movie['poster_url'])): ?>
                            <?php
                                $posterSrc = $movie['poster_url'];
                                if (str_starts_with($posterSrc, '/')) {
                                    $posterSrc = APP_URL . $posterSrc;
                                }
                            ?>
                            <div class="movie-card__poster">
                                <img src="<?= View::e($posterSrc) ?>" alt="<?= View::e($movie['title']) ?>" loading="lazy" referrerpolicy="no-referrer">
                            </div>
                        <?php else: ?>
                            <div class="movie-card__poster movie-card__poster--placeholder">🎬</div>
                        <?php endif; ?>
                        <div class="movie-card__badge"><?= View::e($movie['age_rating']) ?></div>
                        <div class="movie-card__body">
                            <h3 class="movie-card__title"><?= View::e($movie['title']) ?></h3>
                            <p class="movie-card__meta">
                                <?= $movie['duration_minutes'] ?> min
                                <?php if ($movie['movie_rating']): ?>
                                    &nbsp;·&nbsp; ⭐ <?= number_format((float)$movie['movie_rating'], 1) ?>
                                <?php endif; ?>
                            </p>
                            <p class="movie-card__desc"><?= View::e(mb_substr($movie['description'] ?? '', 0, 100)) ?>…</p>
                        </div>
                        <div class="movie-card__footer">
                            <span class="btn btn--sm btn--primary">Book Now</span>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>

            <!-- Pagination -->
            <?php if (!empty($pages) && $pages > 1): ?>
                <div class="pagination">
                    <?php for ($i = 1; $i <= $pages; $i++): ?>
                        <a href="?page=<?= $i ?>" class="pagination__item <?= $page === $i ? 'active' : '' ?>">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </section>

</div>
