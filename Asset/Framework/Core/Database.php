<?php

declare(strict_types=1);

/**
 * Last Hammer Framework 2.0
 * PHP Version 8.3 (Required).
 *
 * @see https://github.com/arcanisgk/LH-Framework
 *
 * @author    Walter Nuñez (arcanisgk/original founder) <icarosnet@gmail.com>
 * @copyright 2017 - 2024
 * @license   http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 * @note      This program is distributed in the hope that it will be useful
 * WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY
 * or FITNESS FOR A PARTICULAR PURPOSE.
 */

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
     * @param array $config
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
            $conn = new PDO("mysql:host=".$host.";dbname=".$db, $user, $pass);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            return true;
        } catch (PDOException $e) {
            return false;
        }
    }
}