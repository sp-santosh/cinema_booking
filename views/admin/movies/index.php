<div class="container">
    <div class="page-header">
        <h1>Manage Movies</h1>
        <a href="<?= APP_URL ?>/admin/movies/create" class="btn btn--primary">+ Add Movie</a>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Duration</th>
                <th>Rating</th>
                <th>Age</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($movies as $m): ?>
                <tr>
                    <td><?= $m['movie_id'] ?></td>
                    <td><?= View::e($m['title']) ?></td>
                    <td><?= $m['duration_minutes'] ?> min</td>
                    <td><?= $m['movie_rating'] ? '⭐ ' . number_format((float)$m['movie_rating'], 1) : '—' ?></td>
                    <td><?= View::e($m['age_rating']) ?></td>
                    <td class="table-actions">
                        <a href="<?= APP_URL ?>/admin/movies/<?= $m['movie_id'] ?>/edit" class="btn btn--sm btn--outline">Edit</a>
                        <form action="<?= APP_URL ?>/admin/movies/<?= $m['movie_id'] ?>/delete" method="POST" class="inline-form">
                            <?= Csrf::field() ?>
                            <input type="hidden" name="_method" value="DELETE">
                            <button type="submit" class="btn btn--sm btn--danger"
                                    onclick="return confirm('Delete this movie?')">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Pagination -->
    <?php if (!empty($pages) && $pages > 1): ?>
        <div class="pagination">
            <?php for ($i = 1; $i <= $pages; $i++): ?>
                <a href="?page=<?= $i ?>" class="pagination__item <?= $page === $i ? 'active' : '' ?>"><?= $i ?></a>
            <?php endfor; ?>
        </div>
    <?php endif; ?>
</div>
