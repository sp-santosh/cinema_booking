<?php
/**
 * Middleware – simple middleware pipeline.
 *
 * Middleware closures receive the request and a $next callable.
 * Currently used as a callable collection; invoke with Middleware::run().
 */

class Middleware
{
    /** Ensure a guest (unauthenticated) user, redirect if already logged in. */
    public static function guest(string $redirectTo = '/'): void
    {
        if (Auth::check()) {
            header('Location: ' . APP_URL . $redirectTo);
            exit;
        }
    }

    /** Ensure an authenticated user; redirect to login if not. */
    public static function auth(string $loginPath = '/login'): void
    {
        if (!Auth::check()) {
            $_SESSION['intended_url'] = $_SERVER['REQUEST_URI'] ?? '/';
            header('Location: ' . APP_URL . $loginPath);
            exit;
        }
    }

    /** Ensure the current user is an admin. */
    public static function admin(): void
    {
        self::auth();

        if (!Auth::isAdmin()) {
            http_response_code(403);
            View::render('errors/403', [], 'layouts/main');
            exit;
        }
    }
}
