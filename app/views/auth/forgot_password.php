    <h1 class="auth-title">Forgot Password</h1>
    <p class="auth-subtitle">Enter your email address and we'll send you a link to reset your password.</p>

    <form action="<?= APP_URL ?>/forgot-password" method="POST" autocomplete="off">
        <?= Csrf::field() ?>

        <div class="form-group">
            <label for="email">Email address</label>
            <input type="email" id="email" name="email" 
                   value="<?= View::e($old['email'] ?? '') ?>" 
                   placeholder="you@example.com" required autofocus>
            <?php if (isset($errors['email'])): ?>
                <span class="error-text"><?= View::e($errors['email']) ?></span>
            <?php endif; ?>
        </div>

        <button type="submit" class="btn btn--primary btn--full">Send Reset Link</button>
    </form>

    <p class="auth-switch">
        Remember your password? <a href="<?= APP_URL ?>/login">Back to Login</a>
    </p>
