<?php
/**
 * Base Controller.
 *
 * All controllers extend this class to get convenient helpers.
 */

abstract class Controller
{
    // ── View rendering ──────────────────────────────────────────────

    protected function render(string $template, array $data = [], string $layout = 'layouts/main'): void
    {
        View::render($template, $data, $layout);
    }

    // ── Redirects ───────────────────────────────────────────────────

    protected function redirect(string $url): never
    {
        header('Location: ' . $url);
        exit;
    }

    // ── Request helpers ─────────────────────────────────────────────

    /** Return sanitised POST value or default. */
    protected function input(string $key, mixed $default = null): mixed
    {
        return isset($_POST[$key]) ? trim($_POST[$key]) : $default;
    }

    /** Return sanitised GET value or default. */
    protected function query(string $key, mixed $default = null): mixed
    {
        return isset($_GET[$key]) ? trim($_GET[$key]) : $default;
    }

    /** Return all POST data, trimmed. */
    protected function allInput(): array
    {
        return array_map(fn($v) => is_string($v) ? trim($v) : $v, $_POST);
    }

    /** Check if current request is POST. */
    protected function isPost(): bool
    {
        return strtoupper($_SERVER['REQUEST_METHOD']) === 'POST';
    }

    /** Check if current request wants JSON (e.g. fetch/AJAX). */
    protected function wantsJson(): bool
    {
        return str_contains($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json');
    }

    // ── JSON responses ──────────────────────────────────────────────

    protected function json(mixed $data, int $status = 200): never
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    // ── Flash messages ──────────────────────────────────────────────

    protected function flash(string $type, string $message): void
    {
        $_SESSION['flash'][$type] = $message;
    }

    // ── Auth shortcuts ──────────────────────────────────────────────

    protected function isLoggedIn(): bool
    {
        return isset($_SESSION['user_id']);
    }

    protected function currentUser(): ?array
    {
        return $_SESSION['user'] ?? null;
    }

    protected function requireAuth(): void
    {
        if (!$this->isLoggedIn()) {
            $this->flash('error', 'You must be logged in to access that page.');
            $this->redirect(APP_URL . '/login');
        }
    }

    protected function requireAdmin(): void
    {
        $this->requireAuth();

        if (($_SESSION['user']['role_id'] ?? null) !== ROLE_ADMIN) {
            http_response_code(403);
            $this->render('errors/403', [], 'layouts/main');
            exit;
        }
    }
}
