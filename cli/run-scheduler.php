<?php
/**
 * Simplified cron-compatible Scheduler.
 * Runs logic based on time, to be executed via crontab:
 * * * * * * php /path/to/cli/run-scheduler.php
 */

require_once __DIR__ . '/../app/config/config.php';
require_once __DIR__ . '/../app/core/Database.php';

// Dispatch command helper
function dispatchJob($jobName) {
    $cliPath = __DIR__ . '/run-job.php';
    // Run asynchronously ignoring output
    exec("php $cliPath $jobName > /dev/null 2>&1 &");
    echo "Dispatched $jobName.\n";
}

$currentHour = (int) date('H');
$currentMin  = (int) date('i');

echo "Scheduler running at " . date('Y-m-d H:i:s') . "\n";

// Example: Run ReleaseMoviesJob every day at 3:00 AM
if ($currentHour === 3 && $currentMin === 0) {
    dispatchJob('ReleaseMoviesJob');
}

// Example: Run HotMoviesJob every hour on the hour
if ($currentMin === 0) {
    dispatchJob('HotMoviesJob');
}

echo "Scheduler finished.\n";
