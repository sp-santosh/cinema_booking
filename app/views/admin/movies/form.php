<div class="container form-page">
    <h1><?= View::e($pageTitle) ?></h1>

    <?php if (!empty($errors)): ?>
        <div class="form-errors">
            <?php foreach ($errors as $e): ?>
                <p><?= View::e($e) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php
        $isEdit  = !empty($movie);
        $action  = $isEdit ? APP_URL . '/admin/movies/' . $movie['movie_id'] : APP_URL . '/admin/movies';
        $old     = $old ?? $movie ?? [];
    ?>

    <form action="<?= $action ?>" method="POST" enctype="multipart/form-data" class="form-card">
        <?= Csrf::field() ?>
        <?php if ($isEdit): ?>
            <input type="hidden" name="_method" value="PUT">
        <?php endif; ?>

        <div class="form-group">
            <label for="title">Title <span class="required">*</span></label>
            <input type="text" id="title" name="title"
                   value="<?= View::e($old['title'] ?? '') ?>" required>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="duration_minutes">Duration (minutes) <span class="required">*</span></label>
                <input type="number" id="duration_minutes" name="duration_minutes" min="1"
                       value="<?= View::e($old['duration_minutes'] ?? '') ?>" required>
            </div>
            <div class="form-group">
                <label for="age_rating">Age Rating <span class="required">*</span></label>
                <select id="age_rating" name="age_rating" required>
                    <?php foreach (['U', 'PG', '12A', '12', '15', '18'] as $rating): ?>
                        <option value="<?= $rating ?>"
                            <?= ($old['age_rating'] ?? '') === $rating ? 'selected' : '' ?>>
                            <?= $rating ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="movie_rating">User Rating (0–10)</label>
                <input type="number" id="movie_rating" name="movie_rating"
                       min="0" max="10" step="0.1"
                       value="<?= View::e($old['movie_rating'] ?? '') ?>">
            </div>
        </div>

        <div class="form-group">
            <label for="description">Description</label>
            <textarea id="description" name="description" rows="3"><?= View::e($old['description'] ?? '') ?></textarea>
        </div>

        <!-- Poster Section -->
        <fieldset style="border: 1px solid var(--c-border); border-radius: var(--rad-md); padding: var(--sp-lg); margin-bottom: var(--sp-lg);">
            <legend style="color: var(--c-text-main); font-weight: 600; padding: 0 var(--sp-sm);">Movie Poster</legend>

            <?php if (!empty($old['poster_url'])): ?>
                <?php
                    $previewSrc = $old['poster_url'];
                    if (str_starts_with($previewSrc, '/')) {
                        $previewSrc = APP_URL . $previewSrc;
                    }
                ?>
                <div style="margin-bottom: var(--sp-md); text-align: center;">
                    <img src="<?= View::e($previewSrc) ?>" alt="Current Poster"
                         style="max-height: 200px; border-radius: var(--rad-md); border: 1px solid var(--c-border);"
                         referrerpolicy="no-referrer">
                    <p style="font-size: 12px; color: var(--c-text-muted); margin-top: var(--sp-xs);">Current poster</p>
                </div>
            <?php endif; ?>

            <div class="form-group">
                <label for="poster_file">Upload Poster Image</label>
                <input type="file" id="poster_file" name="poster_file" accept="image/*"
                       style="padding: var(--sp-sm); background: var(--c-bg-surface); border: 1px dashed var(--c-border); border-radius: var(--rad-md); width: 100%; color: var(--c-text-muted);">
                <p style="font-size: 11px; color: var(--c-text-muted); margin-top: 4px;">Upload a JPG, PNG, or WebP image (max 5 MB).</p>
            </div>

            <div style="text-align: center; padding: var(--sp-sm) 0; color: var(--c-text-muted); font-size: 12px; font-weight: 600;">— OR —</div>

            <div class="form-group">
                <label for="poster_url">Poster Image URL</label>
                <input type="url" id="poster_url" name="poster_url"
                       placeholder="https://example.com/poster.jpg"
                       value="<?= View::e($old['poster_url'] ?? '') ?>">
                <p style="font-size: 11px; color: var(--c-text-muted); margin-top: 4px;">Paste a direct link to a poster image.</p>
            </div>
        </fieldset>

        <div class="form-actions">
            <a href="<?= APP_URL ?>/admin/movies" class="btn btn--outline">Cancel</a>
            <button type="submit" class="btn btn--primary">
                <?= $isEdit ? 'Update Movie' : 'Add Movie' ?>
            </button>
        </div>
    </form>
</div>
