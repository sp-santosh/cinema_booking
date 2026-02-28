<?php
/**
 * CSRF – cross-site request forgery token helpers.
 *
 * Include a token in every form:
 *   <?= Csrf::field() ?>
 *
 * Verify on every mutating request:
 *   Csrf::verify();
 */

class Csrf
{
    private const TOKEN_KEY    = '_csrf_token';
    private const TOKEN_LENGTH = 32;

    /** Generate (once) and return the CSRF token for this session. */
    public static function token(): string
    {
        if (empty($_SESSION[self::TOKEN_KEY])) {
            $_SESSION[self::TOKEN_KEY] = bin2hex(random_bytes(self::TOKEN_LENGTH));
        }

        return $_SESSION[self::TOKEN_KEY];
    }

    /** Render a hidden HTML input carrying the CSRF token. */
    public static function field(): string
    {
        return '<input type="hidden" name="_csrf_token" value="' . self::token() . '">';
    }

    /**
     * Validate the token submitted with the current request.
     * Terminates with a 403 if the token is missing or invalid.
     */
    public static function verify(): void
    {
        $submitted = $_POST['_csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';

        if (!hash_equals(self::token(), $submitted)) {
            http_response_code(403);
            die('Invalid or missing CSRF token.');
        }
    }
}
