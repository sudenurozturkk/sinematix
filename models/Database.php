<?php
/**
 * Database Connection Class
 * Singleton pattern for PDO connection with improved error handling
 */

require_once __DIR__ . '/../config/Config.php';
require_once __DIR__ . '/../src/Exceptions/DatabaseException.php';

class Database
{
    private static ?PDO $instance = null;
    private static int $retryAttempts = 3;
    private static int $retryDelay = 1000; // milliseconds

    /**
     * Private constructor to prevent direct instantiation
     */
    private function __construct()
    {
    }

    /**
     * Prevent cloning of the instance
     */
    private function __clone()
    {
    }

    /**
     * Prevent unserialization of the instance
     */
    public function __wakeup()
    {
        throw new DatabaseException("Cannot unserialize singleton");
    }

    /**
     * Get database instance with retry logic
     */
    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            $config = Config::database();
            $attempts = 0;
            $lastException = null;

            while ($attempts < self::$retryAttempts) {
                try {
                    $dsn = "mysql:host={$config['host']};dbname={$config['dbname']};charset={$config['charset']}";
                    self::$instance = new PDO($dsn, $config['username'], $config['password'], $config['options']);
                    return self::$instance;
                } catch (PDOException $e) {
                    $lastException = $e;
                    
                    // If database doesn't exist, try to create it
                    if ($e->getCode() == 1049 && $attempts == 0) {
                        try {
                            self::createDatabase($config);
                            continue; // Retry connection
                        } catch (Exception $createEx) {
                            throw new DatabaseException("Failed to create database: " . $createEx->getMessage(), 0, $createEx);
                        }
                    }

                    $attempts++;
                    if ($attempts < self::$retryAttempts) {
                        usleep(self::$retryDelay * 1000);
                    }
                }
            }

            throw new DatabaseException(
                "Database connection failed after {$attempts} attempts: " . $lastException->getMessage(),
                $lastException->getCode(),
                $lastException
            );
        }

        return self::$instance;
    }

    /**
     * Create database if it doesn't exist
     */
    private static function createDatabase(array $config): void
    {
        $dsn = "mysql:host={$config['host']};charset={$config['charset']}";
        $pdo = new PDO($dsn, $config['username'], $config['password'], $config['options']);
        $dbName = $config['dbname'];
        $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$dbName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    }

    /**
     * Execute a query with parameters
     */
    public static function query(string $sql, array $params = []): PDOStatement
    {
        try {
            $stmt = self::getInstance()->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            throw new DatabaseException("Query execution failed: " . $e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Fetch all rows
     */
    public static function fetchAll(string $sql, array $params = []): array
    {
        return self::query($sql, $params)->fetchAll();
    }

    /**
     * Fetch single row
     */
    public static function fetch(string $sql, array $params = []): ?array
    {
        $result = self::query($sql, $params)->fetch();
        return $result ?: null;
    }

    /**
     * Get last insert ID
     */
    public static function lastInsertId(): string
    {
        return self::getInstance()->lastInsertId();
    }

    /**
     * Begin transaction
     */
    public static function beginTransaction(): bool
    {
        return self::getInstance()->beginTransaction();
    }

    /**
     * Commit transaction
     */
    public static function commit(): bool
    {
        return self::getInstance()->commit();
    }

    /**
     * Rollback transaction
     */
    public static function rollback(): bool
    {
        return self::getInstance()->rollBack();
    }

    /**
     * Execute within a transaction
     */
    public static function transaction(callable $callback)
    {
        self::beginTransaction();
        
        try {
            $result = $callback();
            self::commit();
            return $result;
        } catch (Exception $e) {
            self::rollback();
            throw $e;
        }
    }
}
