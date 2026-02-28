<?php
/**
 * Auth – session-based authentication helpers.
 */

class Auth
{
    /** Start or resume the application session. Called once in bootstrap. */
    public static function startSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_name(SESSION_NAME);
            session_set_cookie_params([
                'lifetime' => SESSION_LIFETIME,
                'path'     => '/',
                'secure'   => APP_ENV === 'production',
                'httponly' => true,
                'samesite' => 'Lax',
            ]);
            session_start();
        }
    }

    /** Log a user in by storing their data in the session. */
    public static function login(array $user): void
    {
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['user']    = $user;
    }

    /** Destroy the session and log out. */
    public static function logout(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }
        session_destroy();
    }

    /** Check whether a user is currently authenticated. */
    public static function check(): bool
    {
        return isset($_SESSION['user_id']);
    }

    /** Return the currently authenticated user array, or null. */
    public static function user(): ?array
    {
        return $_SESSION['user'] ?? null;
    }

    /** Return the authenticated user's ID, or null. */
    public static function id(): ?int
    {
        return isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : null;
    }

    /** True if the current user has the admin role. */
    public static function isAdmin(): bool
    {
        return (self::user()['role_id'] ?? null) === ROLE_ADMIN;
    }
}
