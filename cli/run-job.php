<?php
/**
 * CLI Entry point to run a specific asynchronous job.
 * Usage: php cli/run-job.php JobClassName [args...]
 */

require_once __DIR__ . '/../app/config/config.php';
require_once __DIR__ . '/../app/core/Database.php';
require_once __DIR__ . '/../app/core/Model.php';

// Dynamically load class dependencies
spl_autoload_register(function ($class) {
    if (file_exists(APP_PATH . "/jobs/{$class}.php")) {
        require_once APP_PATH . "/jobs/{$class}.php";
    } elseif (file_exists(APP_PATH . "/models/{$class}.php")) {
        require_once APP_PATH . "/models/{$class}.php";
    } elseif (file_exists(APP_PATH . "/services/{$class}.php")) {
        require_once APP_PATH . "/services/{$class}.php";
    }
});

if ($argc < 2) {
    echo "Usage: php cli/run-job.php <JobClassName> [args...]\n";
    exit(1);
}

$jobClass = $argv[1];
$jobArgs  = array_slice($argv, 2);

if (!class_exists($jobClass)) {
    echo "Error: Job class '{$jobClass}' not found in app/jobs/.\n";
    exit(1);
}

try {
    $job = new $jobClass();
    
    if (!method_exists($job, 'handle')) {
        echo "Error: Job class '{$jobClass}' must implement a handle() method.\n";
        exit(1);
    }

    echo "Running job: {$jobClass}...\n";
    $job->handle($jobArgs);
} catch (Exception $e) {
    echo "Job Error: " . $e->getMessage() . "\n";
    exit(1);
}
