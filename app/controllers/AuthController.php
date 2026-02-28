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
        $this->redirect(APP_URL . '/');
    }

    // POST /logout
    public function logout(): void
    {
        Auth::logout();
        $this->redirect(APP_URL . '/login');
    }
}
