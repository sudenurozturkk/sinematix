<?php
/**
 * Database Connection Class
 * Singleton pattern for PDO connection
 */

class Database {
    private static ?PDO $instance = null;
    
    public static function getInstance(): PDO {
        if (self::$instance === null) {
            $config = require __DIR__ . '/../config/database.php';
            
            try {
                $dsn = "mysql:host={$config['host']};dbname={$config['dbname']};charset={$config['charset']}";
                self::$instance = new PDO($dsn, $config['username'], $config['password'], $config['options']);
            } catch (PDOException $e) {
                // Eğer veritabanı yoksa, önce oluştur
                if ($e->getCode() == 1049) {
                    $dsn = "mysql:host={$config['host']};charset={$config['charset']}";
                    $pdo = new PDO($dsn, $config['username'], $config['password'], $config['options']);
                    $pdo->exec("CREATE DATABASE IF NOT EXISTS {$config['dbname']} CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
                    
                    // Tekrar bağlan
                    $dsn = "mysql:host={$config['host']};dbname={$config['dbname']};charset={$config['charset']}";
                    self::$instance = new PDO($dsn, $config['username'], $config['password'], $config['options']);
                } else {
                    throw new Exception("Database connection failed: " . $e->getMessage());
                }
            }
        }
        
        return self::$instance;
    }
    
    public static function query(string $sql, array $params = []): PDOStatement {
        $stmt = self::getInstance()->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }
    
    public static function fetchAll(string $sql, array $params = []): array {
        return self::query($sql, $params)->fetchAll();
    }
    
    public static function fetch(string $sql, array $params = []): ?array {
        $result = self::query($sql, $params)->fetch();
        return $result ?: null;
    }
    
    public static function lastInsertId(): string {
        return self::getInstance()->lastInsertId();
    }
    
    // Prevent cloning
    private function __clone() {}
}
