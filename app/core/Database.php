<?php
/**
 * Database – PDO singleton.
 *
 * Usage:  $pdo = Database::getInstance();
 */

class Database
{
    private static ?PDO $instance = null;

    private function __construct() {}

    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            $cfg = require APP_PATH . '/config/database.php';

            $dsn = sprintf(
                '%s:host=%s;port=%s;dbname=%s;charset=%s',
                $cfg['driver'],
                $cfg['host'],
                $cfg['port'],
                $cfg['dbname'],
                $cfg['charset']
            );

            self::$instance = new PDO($dsn, $cfg['username'], $cfg['password'], $cfg['options']);
        }

        return self::$instance;
    }

    private function __clone() {}

    public function __wakeup(): never
    {
        throw new \RuntimeException('Cannot unserialize a singleton.');
    }
}
