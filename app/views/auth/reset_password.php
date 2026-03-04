    <h1 class="auth-title">Reset Password</h1>
    <p class="auth-subtitle">Please enter your new password below.</p>

    <form action="<?= APP_URL ?>/reset-password" method="POST" autocomplete="off">
        <?= Csrf::field() ?>
        <input type="hidden" name="token" value="<?= View::e($token) ?>">

        <div class="form-group">
            <label for="password">New Password</label>
            <input type="password" id="password" name="password" 
                   placeholder="At least 8 characters" required autofocus>
            <?php if (isset($errors['password'])): ?>
                <span class="error-text"><?= View::e($errors['password']) ?></span>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="password_confirm">Confirm New Password</label>
            <input type="password" id="password_confirm" name="password_confirm" 
                   placeholder="Repeat your new password" required>
            <?php if (isset($errors['password_confirm'])): ?>
                <span class="error-text"><?= View::e($errors['password_confirm']) ?></span>
            <?php endif; ?>
        </div>

        <button type="submit" class="btn btn--primary btn--full">Update Password</button>
    </form>

    <p class="auth-switch">
        Wait, I remember it! <a href="<?= APP_URL ?>/login">Back to Login</a>
    </p>
