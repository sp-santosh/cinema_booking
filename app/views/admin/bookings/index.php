<div class="container">
    <h1>All Bookings</h1>

    <table class="table">
        <thead>
            <tr>
                <th>Booking #</th>
                <th>Customer</th>
                <th>Movie</th>
                <th>Screening</th>
                <th>Total</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($bookings as $b): ?>
                <tr>
                    <td>#<?= str_pad((string)$b['booking_id'], 6, '0', STR_PAD_LEFT) ?></td>
                    <td><?= View::e($b['first_name'] . ' ' . $b['last_name']) ?></td>
                    <td><?= View::e($b['movie_title']) ?></td>
                    <td><?= date('j M Y H:i', strtotime($b['start_time'])) ?></td>
                    <td>£<?= number_format((float)$b['total_amount'], 2) ?></td>
                    <td>
                        <span class="status-badge status-badge--<?= strtolower($b['status']) ?>">
                            <?= View::e($b['status']) ?>
                        </span>
                    </td>
                    <td>
                        <?php if ($b['status'] === 'CONFIRMED'): ?>
                            <form action="<?= APP_URL ?>/admin/bookings/<?= $b['booking_id'] ?>/cancel"
                                  method="POST" class="inline-form">
                                <?= Csrf::field() ?>
                                <button type="submit" class="btn btn--sm btn--danger"
                                        onclick="return confirm('Cancel booking #<?= $b['booking_id'] ?>?')">
                                    Cancel
                                </button>
                            </form>
                        <?php else: ?>
                            <span class="text-muted">—</span>
                        <?php endif; ?>
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
