<div class="auth-form-wrap">
    <h1 class="auth-title">Create your account</h1>
    <p class="auth-subtitle">Book tickets in seconds</p>

    <?php if (!empty($errors)): ?>
        <div class="form-errors">
            <?php foreach ($errors as $error): ?>
                <p><?= View::e($error) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form action="<?= APP_URL ?>/register" method="POST" novalidate>
        <?= Csrf::field() ?>

        <div class="form-row">
            <div class="form-group">
                <label for="first_name">First name</label>
                <input
                    type="text"
                    id="first_name"
                    name="first_name"
                    value="<?= View::e($old['first_name'] ?? '') ?>"
                    required
                    autocomplete="given-name"
                >
            </div>
            <div class="form-group">
                <label for="last_name">Last name</label>
                <input
                    type="text"
                    id="last_name"
                    name="last_name"
                    value="<?= View::e($old['last_name'] ?? '') ?>"
                    required
                    autocomplete="family-name"
                >
            </div>
        </div>

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
            <label for="phone">Phone number <span class="optional">(optional)</span></label>
            <input
                type="tel"
                id="phone"
                name="phone"
                value="<?= View::e($old['phone'] ?? '') ?>"
                autocomplete="tel"
            >
        </div>

        <div class="form-group">
            <label for="password">Password <span class="optional">(min. 8 characters)</span></label>
            <input
                type="password"
                id="password"
                name="password"
                required
                autocomplete="new-password"
            >
        </div>

        <div class="form-group">
            <label for="password_confirm">Confirm password</label>
            <input
                type="password"
                id="password_confirm"
                name="password_confirm"
                required
                autocomplete="new-password"
            >
        </div>

        <button type="submit" class="btn btn--primary btn--full">Create account</button>
    </form>

    <p class="auth-switch">
        Already have an account? <a href="<?= APP_URL ?>/login">Sign in</a>
    </p>
</div>
