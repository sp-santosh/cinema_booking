<?php
/**
 * Application-wide configuration constants.
 */

// ── Application ──────────────────────────────────────────────────
define('APP_NAME',    'CineBook');
define('APP_VERSION', '1.0.0');
define('APP_ENV',     getenv('APP_ENV') ?: 'development'); // 'production' | 'development'
define('APP_DEBUG',   APP_ENV === 'development');

// ── Base URL (no trailing slash) ──────────────────────────────────
// In production set APP_URL in your environment instead.
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost:8000';
$scriptName = dirname($_SERVER['SCRIPT_NAME'] ?? '');
$basePath = ($scriptName === '/' || $scriptName === '\\') ? '' : str_replace('\\', '/', $scriptName);
define('APP_URL', getenv('APP_URL') ?: "$protocol://$host$basePath");

// ── Directory paths ───────────────────────────────────────────────
define('ROOT_PATH',        dirname(__DIR__, 2));           // project root
if (!defined('APP_PATH')) {
    define('APP_PATH',         ROOT_PATH . '/app');
}
if (!defined('VIEWS_PATH')) {
    define('VIEWS_PATH',       ROOT_PATH . '/views');
}
define('PUBLIC_PATH',      ROOT_PATH . '/public');
define('STORAGE_PATH',     ROOT_PATH . '/storage');
define('LOGS_PATH',        STORAGE_PATH . '/logs');

// ── Session ───────────────────────────────────────────────────────
define('SESSION_NAME',     'cinebook_session');
define('SESSION_LIFETIME', 7200);   // seconds (2 hours)

// ── Password hashing ──────────────────────────────────────────────
define('BCRYPT_COST', 12);

// ── Roles ─────────────────────────────────────────────────────────
define('ROLE_ADMIN',    1);
define('ROLE_CUSTOMER', 2);

// ── Ticket pricing (fallback defaults, overridden per screening) ──
define('DEFAULT_TICKET_PRICE', 12.50);

// ── Pagination ────────────────────────────────────────────────────
define('ITEMS_PER_PAGE', 10);
