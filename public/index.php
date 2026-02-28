<?php
declare(strict_types=1);

/**
 * CineBook - Front Controller
 * 
 * All HTTP requests are routed through this file.
 */

// Load the Bootstrap script (handles autoloading, configurations, errors, and sessions)
require_once dirname(__DIR__) . '/app/bootstrap.php';

// Load application routes
require_once APP_PATH . '/routes.php';

// Dispatch the current request
Router::dispatch();
