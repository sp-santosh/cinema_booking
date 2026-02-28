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

    <form action="<?= $action ?>" method="POST" class="form-card">
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
            <textarea id="description" name="description" rows="5"><?= View::e($old['description'] ?? '') ?></textarea>
        </div>

        <div class="form-actions">
            <a href="<?= APP_URL ?>/admin/movies" class="btn btn--outline">Cancel</a>
            <button type="submit" class="btn btn--primary">
                <?= $isEdit ? 'Update Movie' : 'Add Movie' ?>
            </button>
        </div>
    </form>
</div>
