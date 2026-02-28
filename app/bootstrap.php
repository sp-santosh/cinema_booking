<?php
declare(strict_types=1);

// ── Bootstrap ─────────────────────────────────────────────────────

require_once __DIR__ . '/config/config.php';

// ── Autoloader ────────────────────────────────────────────────────
spl_autoload_register(function ($class) {
    // List of directories to search for classes
    $directories = [
        APP_PATH . '/core/',
        APP_PATH . '/models/',
        APP_PATH . '/controllers/',
        APP_PATH . '/services/',
        APP_PATH . '/jobs/'
    ];

    foreach ($directories as $directory) {
        $file = $directory . $class . '.php';
        if (file_exists($file)) {
            require $file;
            return;
        }
    }
});

// ── Session ───────────────────────────────────────────────────────
Auth::startSession();

// ── Error handling ────────────────────────────────────────────────
if (defined('APP_DEBUG') && APP_DEBUG) {
    ini_set('display_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
    error_reporting(0);
}
