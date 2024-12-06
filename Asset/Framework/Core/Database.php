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

use Asset\Framework\Trait\SingletonTrait;
use DateTime;
use PDO;
use PDOException;
use Throwable;

/**
 * Class Database
 * A simple ...
 */
class Database
{
    use SingletonTrait;

    /**
     * @var PDO|null
     */
    private ?PDO $connection = null;

    /**
     * @var array
     */
    private array $config;

    /**
     * @var QueryBuilder
     */
    private QueryBuilder $builder;

    /**
     * @var array
     */
    private array $queryStack = [];

    /**
     * @var DateTime|null
     */
    private ?DateTime $dateTime;

    /**
     * @var bool
     */
    private bool $returnFetch = false;

    /**
     *
     */
    public function __construct()
    {
        $this->builder  = new QueryBuilder();
        $this->dateTime = new DateTime();
    }

    /**
     * @param array $config
     * @return void
     */
    public function connect(array $config): void
    {
        $this->config = $config;
        try {
            $dsn              = "mysql:host={$config['host']};dbname={$config['db']};charset=utf8mb4";
            $this->connection = new PDO($dsn, $config['user'], $config['pass'], [
                PDO::ATTR_ERRMODE                  => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE       => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES         => false,
                PDO::MYSQL_ATTR_INIT_COMMAND       => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci",
                PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
                PDO::ATTR_PERSISTENT               => false,
            ]);
        } catch (PDOException $e) {
            throw new PDOException("Connection failed: ".$e->getMessage());
        }
    }

    /**
     * @return QueryBuilder
     */
    public function getBuilder(): QueryBuilder
    {
        return $this->builder;
    }

    /**
     * @param string $dbName
     * @return void
     */
    public function setQueryTarget(string $dbName): void
    {
        if (!isset($this->queryStack[$dbName])) {
            $this->queryStack[$dbName] = [];
        }
    }

    /**
     * @param string $query
     * @param array $params
     * @return void
     */
    public function addQuery(string $query, array $params = []): void
    {
        $currentDb                      = array_key_last($this->queryStack) ?? $this->config['db'];
        $this->queryStack[$currentDb][] = [
            'query'  => $query,
            'params' => $params,
        ];
    }

    /**
     * @param bool $fetch
     * @return array
     */
    public function executeStack(bool $fetch = false): array
    {
        $startTime = microtime(true);
        $results   = [
            'count_reg' => 0,
            'time'      => 0,
            'exec'      => false,
            'data'      => [],
        ];

        try {
            $this->connection->beginTransaction();

            foreach ($this->queryStack as $dbName => $queries) {
                $this->changeDatabase($dbName);

                foreach ($queries as $queryData) {
                    $stmt = $this->connection->prepare($queryData['query']);

                    foreach ($queryData['params'] as $key => $value) {
                        $type = $this->getParamType($value);
                        $stmt->bindValue($key + 1, $value, $type);
                    }

                    $results['exec'] = $stmt->execute();

                    if ($fetch) {
                        $fetchedData          = $stmt->fetchAll();
                        $results['data']      = array_merge($results['data'], $fetchedData);
                        $results['count_reg'] += count($fetchedData);
                    }
                }
            }

            $this->connection->commit();
            $results['time'] = number_format(microtime(true) - $startTime, 3).' seconds';

        } catch (Throwable $e) {
            $this->connection->rollBack();
            throw new PDOException("Query execution failed: ".$e->getMessage());
        } finally {
            $this->resetQueryStack();
        }

        return $results;
    }

    /**
     * @param string $dbName
     * @return void
     */
    public function changeDatabase(string $dbName): void
    {
        try {
            $this->connection->exec("USE `$dbName`;");
        } catch (PDOException $e) {
            throw new PDOException("Database change failed: ".$e->getMessage());
        }
    }

    /**
     * @param $value
     * @return int
     */
    private function getParamType($value): int
    {
        return match (true) {
            is_int($value) => PDO::PARAM_INT,
            is_bool($value) => PDO::PARAM_BOOL,
            is_null($value) => PDO::PARAM_NULL,
            default => PDO::PARAM_STR
        };
    }

    /**
     * @return void
     */
    private function resetQueryStack(): void
    {
        $this->queryStack = [];
    }

    /**
     * @param callable $operations
     * @return mixed
     */
    public function transaction(callable $operations): mixed
    {
        $this->connection->beginTransaction();

        try {
            $result = $operations($this);
            $this->connection->commit();

            return $result;
        } catch (Throwable $e) {
            $this->connection->rollBack();
            throw new PDOException("Transaction failed: ".$e->getMessage());
        }
    }

    /**
     * @param array $operations
     * @return array
     */
    public function batchTransaction(array $operations): array
    {
        $results = [];
        $this->connection->beginTransaction();

        try {
            foreach ($operations as $key => $operation) {
                if (!is_callable($operation)) {
                    throw new PDOException("Operation $key is not callable");
                }
                $results[$key] = $operation($this);
            }

            $this->connection->commit();

            return $results;
        } catch (Throwable $e) {
            $this->connection->rollBack();
            throw new PDOException("Batch transaction failed: ".$e->getMessage());
        }
    }

    /**
     * @param string $query
     * @param array $parameters
     * @return array
     */
    public function raw(string $query, array $parameters = []): array
    {
        try {
            $stmt = $this->connection->prepare($query);

            foreach ($parameters as $key => $value) {
                $stmt->bindValue($key + 1, $value, $this->getParamType($value));
            }

            $stmt->execute();

            return $stmt->fetchAll();
        } catch (PDOException $e) {
            throw new PDOException("Raw query execution failed: ".$e->getMessage());
        }
    }

    /**
     * @return string
     */
    public function lastInsertId(): string
    {
        return $this->connection->lastInsertId();
    }

    /**
     * @param array $config
     * @return bool
     */
    public function testConnection(array $config): bool
    {
        try {
            $dsn            = "mysql:host={$config['host']};charset=utf8mb4";
            $testConnection = new PDO($dsn, $config['user'], $config['pass'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_TIMEOUT => 5,
            ]);

            return true;
        } catch (PDOException $e) {

            return false;
        }
    }

    /**
     * @param string $dbKey
     * @return array
     */
    public function getDatabaseConfiguration(string $dbKey): array
    {
        return [
            'host' => CONFIG->db->$dbKey->getDbHost(),
            'user' => CONFIG->db->$dbKey->getDbUser(),
            'pass' => CONFIG->db->$dbKey->getDbPassword(),
            'db'   => CONFIG->db->$dbKey->getDbName(),
        ];
    }
}
