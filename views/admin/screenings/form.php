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
        $isEdit = !empty($screening);
        $action = $isEdit
            ? APP_URL . '/admin/screenings/' . $screening['screening_id']
            : APP_URL . '/admin/screenings';
        $old    = $old ?? $screening ?? [];
    ?>

    <form action="<?= $action ?>" method="POST" class="form-card">
        <?= Csrf::field() ?>
        <?php if ($isEdit): ?>
            <input type="hidden" name="_method" value="PUT">
        <?php endif; ?>

        <div class="form-group">
            <label for="movie_id">Movie <span class="required">*</span></label>
            <select id="movie_id" name="movie_id" required>
                <option value="">— Select movie —</option>
                <?php foreach ($movies as $m): ?>
                    <option value="<?= $m['movie_id'] ?>"
                        <?= ($old['movie_id'] ?? '') == $m['movie_id'] ? 'selected' : '' ?>>
                        <?= View::e($m['title']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="hall_id">Hall <span class="required">*</span></label>
            <select id="hall_id" name="hall_id" required>
                <option value="">— Select hall —</option>
                <?php foreach ($halls as $h): ?>
                    <option value="<?= $h['hall_id'] ?>"
                        <?= ($old['hall_id'] ?? '') == $h['hall_id'] ? 'selected' : '' ?>>
                        <?= View::e($h['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="start_time">Start time <span class="required">*</span></label>
                <input type="datetime-local" id="start_time" name="start_time"
                       value="<?= View::e(isset($old['start_time']) ? date('Y-m-d\TH:i', strtotime($old['start_time'])) : '') ?>"
                       required>
            </div>
            <div class="form-group">
                <label for="end_time">End time <span class="required">*</span></label>
                <input type="datetime-local" id="end_time" name="end_time"
                       value="<?= View::e(isset($old['end_time']) ? date('Y-m-d\TH:i', strtotime($old['end_time'])) : '') ?>"
                       required>
            </div>
        </div>

        <?php if ($isEdit): ?>
            <div class="form-group">
                <label for="status">Status</label>
                <select id="status" name="status">
                    <?php foreach (['SCHEDULED', 'CANCELLED', 'COMPLETED'] as $st): ?>
                        <option value="<?= $st ?>"
                            <?= ($old['status'] ?? 'SCHEDULED') === $st ? 'selected' : '' ?>>
                            <?= $st ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        <?php endif; ?>

        <div class="form-actions">
            <a href="<?= APP_URL ?>/admin/screenings" class="btn btn--outline">Cancel</a>
            <button type="submit" class="btn btn--primary">
                <?= $isEdit ? 'Update Screening' : 'Schedule Screening' ?>
            </button>
        </div>
    </form>
</div>
