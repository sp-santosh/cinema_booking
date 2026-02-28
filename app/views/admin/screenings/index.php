<div class="container">
    <div class="page-header">
        <h1>Manage Screenings</h1>
        <a href="<?= APP_URL ?>/admin/screenings/create" class="btn btn--primary">+ Schedule Screening</a>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Movie</th>
                <th>Cinema / Hall</th>
                <th>Start Time</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($screenings as $s): ?>
                <tr>
                    <td><?= $s['screening_id'] ?></td>
                    <td><?= View::e($s['title']) ?></td>
                    <td><?= View::e($s['cinema_name']) ?> · <?= View::e($s['hall_name']) ?></td>
                    <td><?= date('j M Y H:i', strtotime($s['start_time'])) ?></td>
                    <td>
                        <span class="status-badge status-badge--<?= strtolower($s['status']) ?>">
                            <?= View::e($s['status']) ?>
                        </span>
                    </td>
                    <td class="table-actions">
                        <a href="<?= APP_URL ?>/admin/screenings/<?= $s['screening_id'] ?>/edit"
                           class="btn btn--sm btn--outline">Edit</a>
                        <form action="<?= APP_URL ?>/admin/screenings/<?= $s['screening_id'] ?>/delete"
                              method="POST" class="inline-form">
                            <?= Csrf::field() ?>
                            <button type="submit" class="btn btn--sm btn--danger"
                                    onclick="return confirm('Delete this screening?')">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
