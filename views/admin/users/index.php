<div class="container">
    <div class="page-header">
        <h1>Manage Users</h1>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Registered</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($customers as $u): ?>
                <tr>
                    <td><?= $u['user_id'] ?></td>
                    <td><?= View::e($u['first_name'] . ' ' . $u['last_name']) ?></td>
                    <td><?= View::e($u['email']) ?></td>
                    <td><?= View::e($u['phone'] ?? '—') ?></td>
                    <td><?= date('j M Y', strtotime($u['created_at'])) ?></td>
                    <td>
                        <span class="status-badge status-badge--<?= $u['is_active'] ? 'confirmed' : 'cancelled' ?>">
                            <?= $u['is_active'] ? 'Active' : 'Inactive' ?>
                        </span>
                    </td>
                    <td>
                        <form action="<?= APP_URL ?>/admin/users/<?= $u['user_id'] ?>/toggle"
                              method="POST" class="inline-form">
                            <?= Csrf::field() ?>
                            <button type="submit" class="btn btn--sm <?= $u['is_active'] ? 'btn--danger' : 'btn--outline' ?>">
                                <?= $u['is_active'] ? 'Deactivate' : 'Activate' ?>
                            </button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
