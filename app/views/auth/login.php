<div class="auth-form-wrap">
    <h1 class="auth-title">Welcome back</h1>
    <p class="auth-subtitle">Sign in to your CineBook account</p>

    <?php if (!empty($errors)): ?>
        <div class="form-errors">
            <?php foreach ($errors as $error): ?>
                <p><?= View::e($error) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form action="<?= APP_URL ?>/login" method="POST" novalidate>
        <?= Csrf::field() ?>

        <div class="form-group">
            <label for="email">Email address</label>
            <input
                type="email"
                id="email"
                name="email"
                value="<?= View::e($old['email'] ?? '') ?>"
                placeholder="you@example.com"
                required
                autocomplete="email"
            >
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input
                type="password"
                id="password"
                name="password"
                placeholder="••••••••"
                required
                autocomplete="current-password"
            >
            <div style="text-align: right; margin-top: 5px;">
                <a href="<?= APP_URL ?>/forgot-password" style="font-size: 0.85rem; color: #aaa;">Forgot password?</a>
            </div>
        </div>

        <button type="submit" class="btn btn--primary btn--full">Sign in</button>
    </form>

    <p class="auth-switch">
        Don't have an account? <a href="<?= APP_URL ?>/register">Create one</a>
    </p>
</div>
