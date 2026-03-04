<?php
/**
 * AuthController – handles login, register, logout.
 */
class AuthController extends Controller
{
    private User $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    // GET /login
    public function showLogin(): void
    {
        Middleware::guest('/');
        $this->render('auth/login', [], 'layouts/auth');
    }

    // POST /login
    public function login(): void
    {
        Middleware::guest('/');
        Csrf::verify();

        $v = new Validator($_POST);
        $v->required(['email', 'password'])->email('email');

        if ($v->fails()) {
            $this->render('auth/login', ['errors' => $v->errors(), 'old' => $_POST], 'layouts/auth');
            return;
        }

        $user = $this->userModel->findByEmail($this->input('email'));

        if (!$user || !$this->userModel->verifyPassword($this->input('password'), $user['password_hash'])) {
            $this->render('auth/login', [
                'errors' => ['email' => 'Invalid email or password.'],
                'old'    => $_POST,
            ], 'layouts/auth');
            return;
        }

        if (!(bool) $user['is_active']) {
            $this->render('auth/login', [
                'errors' => ['email' => 'Your account has been deactivated.'],
                'old'    => $_POST,
            ], 'layouts/auth');
            return;
        }

        Auth::login($user);

        $intended = $_SESSION['intended_url'] ?? null;
        unset($_SESSION['intended_url']);

        $this->redirect($intended ?? APP_URL . '/');
    }

    // GET /register
    public function showRegister(): void
    {
        Middleware::guest('/');
        $this->render('auth/register', [], 'layouts/auth');
    }

    // POST /register
    public function register(): void
    {
        Middleware::guest('/');
        Csrf::verify();

        $v = new Validator($_POST);
        $v->required(['first_name', 'last_name', 'email', 'password', 'password_confirm'])
          ->email('email')
          ->min('password', 8)
          ->matches('password_confirm', 'password');

        if ($v->fails()) {
            $this->render('auth/register', ['errors' => $v->errors(), 'old' => $_POST], 'layouts/auth');
            return;
        }

        // Check email uniqueness.
        if ($this->userModel->findByEmail($this->input('email'))) {
            $this->render('auth/register', [
                'errors' => ['email' => 'That email address is already registered.'],
                'old'    => $_POST,
            ], 'layouts/auth');
            return;
        }

        $userId = $this->userModel->create([
            'first_name' => $this->input('first_name'),
            'last_name'  => $this->input('last_name'),
            'email'      => $this->input('email'),
            'password'   => $this->input('password'),
            'phone'      => $this->input('phone'),
            'role_id'    => ROLE_CUSTOMER,
        ]);

        // Auto-login after registration.
        $user = $this->userModel->findById($userId);
        Auth::login($user);

        $this->flash('success', 'Welcome to CineBook! Your account has been created.');
        
        // Trigger verification email job
        $job = new SendVerificationEmailJob();
        $job->handle([$userId]);

        $this->redirect(APP_URL . '/');
    }

    // POST /logout
    public function logout(): void
    {
        Auth::logout();
        $this->redirect(APP_URL . '/login');
    }

    // GET /verify
    public function verify(): void
    {
        $token = $this->query('token');

        if (!$token) {
            $this->flash('error', 'Invalid verification link.');
            $this->redirect(APP_URL . '/');
        }

        $user = $this->userModel->findByToken($token);

        if (!$user) {
            $this->flash('error', 'The verification link is invalid or has expired.');
            $this->redirect(APP_URL . '/');
        }

        $this->userModel->markVerified($user['user_id']);
        
        // Update session user data
        if (Auth::id() === (int)$user['user_id']) {
            $updatedUser = $this->userModel->findById($user['user_id']);
            Auth::login($updatedUser);
        }

        $this->flash('success', 'Email verified successfully! You can now log in.');
        $this->redirect(APP_URL . '/login');
    }

    // POST /verify/resend
    public function resendVerification(): void
    {
        $this->requireAuth();
        Csrf::verify();

        if (Auth::isVerified()) {
            $this->flash('info', 'Your email is already verified.');
            $this->redirect(APP_URL . '/');
        }

        $job = new SendVerificationEmailJob();
        $job->handle([Auth::id()]);

        $this->flash('success', 'A new verification link has been sent to your email.');
        $this->redirect($_SERVER['HTTP_REFERER'] ?? APP_URL . '/');
    }

    // GET /forgot-password
    public function showForgotPassword(): void
    {
        Middleware::guest('/');
        $this->render('auth/forgot_password', [], 'layouts/auth');
    }

    // POST /forgot-password
    public function forgotPassword(): void
    {
        Middleware::guest('/');
        Csrf::verify();

        $v = new Validator($_POST);
        $v->required(['email'])->email('email');

        if ($v->fails()) {
            $this->render('auth/forgot_password', ['errors' => $v->errors(), 'old' => $_POST], 'layouts/auth');
            return;
        }

        $user = $this->userModel->findByEmail($this->input('email'));

        if ($user) {
            $token = bin2hex(random_bytes(32));
            $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));
            $this->userModel->setResetToken($user['user_id'], $token, $expiry);

            $emailService = new EmailService();
            $emailService->sendPasswordResetEmail($user, $token);
        }

        // Always show success message for security (don't reveal if email exists)
        $this->flash('info', 'If an account exists for that email, a password reset link has been sent.');
        $this->redirect(APP_URL . '/forgot-password');
    }

    // GET /reset-password
    public function showResetPassword(): void
    {
        Middleware::guest('/');
        $token = $this->query('token');

        if (!$token) {
            $this->flash('error', 'Invalid reset link.');
            $this->redirect(APP_URL . '/forgot-password');
        }

        $user = $this->userModel->findByResetToken($token);

        if (!$user || strtotime($user['reset_token_expiry']) < time()) {
            $this->flash('error', 'The reset link is invalid or has expired.');
            $this->redirect(APP_URL . '/forgot-password');
        }

        $this->render('auth/reset_password', ['token' => $token], 'layouts/auth');
    }

    // POST /reset-password
    public function resetPassword(): void
    {
        Middleware::guest('/');
        Csrf::verify();

        $v = new Validator($_POST);
        $v->required(['token', 'password', 'password_confirm'])
          ->min('password', 8)
          ->matches('password_confirm', 'password');

        if ($v->fails()) {
            $this->render('auth/reset_password', [
                'errors' => $v->errors(),
                'token'  => $this->input('token')
            ], 'layouts/auth');
            return;
        }

        $token = $this->input('token');
        $user = $this->userModel->findByResetToken($token);

        if (!$user || strtotime($user['reset_token_expiry']) < time()) {
            $this->flash('error', 'The reset link is invalid or has expired.');
            $this->redirect(APP_URL . '/forgot-password');
            return;
        }

        $this->userModel->updatePassword($user['user_id'], $this->input('password'));

        $this->flash('success', 'Your password has been reset. You can now log in.');
        $this->redirect(APP_URL . '/login');
    }
}
