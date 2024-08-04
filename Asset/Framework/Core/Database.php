<?php

declare(strict_types=1);

namespace Asset\Framework\Core;

use PDO;
use PDOException;

/**
 * Class Database
 * A simple ...
 */
class Database
{
    /**
     * @var Database|null Singleton instance of the Database.
     */
    private static ?self $instance = null;

    /**
     * Get the singleton instance of Database.
     *
     * @return Database The singleton instance.
     */
    public static function getInstance(): self
    {
        if (!self::$instance instanceof self) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * @param  array  $config
     *
     * @return bool
     */
    public function testConnection(array $config): bool
    {
        $host = $config['host'];
        $db   = $config['db'];
        $user = $config['user'];
        $pass = $config['pass'];
        try {
            $conn = new PDO("mysql:host=" . $host . ";dbname=" . $db, $user, $pass);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            return true;
        } catch (PDOException $e) {
            return false;
        }
    }
}