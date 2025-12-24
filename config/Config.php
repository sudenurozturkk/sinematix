<?php
/**
 * Configuration Management
 * Centralized configuration with environment variable support
 */

class Config
{
    private static ?array $config = null;
    private static string $envFile = __DIR__ . '/../.env';

    /**
     * Load configuration from environment file
     */
    private static function load(): void
    {
        if (self::$config !== null) {
            return;
        }

        self::$config = [];

        // Load .env file if exists
        if (file_exists(self::$envFile)) {
            $lines = file(self::$envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                if (strpos(trim($line), '#') === 0) {
                    continue;
                }

                [$key, $value] = explode('=', $line, 2) + [null, null];
                if ($key && $value !== null) {
                    self::$config[trim($key)] = trim($value);
                }
            }
        }

        // Fallback to defaults if .env doesn't exist
        if (empty(self::$config)) {
            self::loadDefaults();
        }
    }

    /**
     * Load default configuration
     */
    private static function loadDefaults(): void
    {
        self::$config = [
            'DB_HOST' => 'localhost',
            'DB_NAME' => 'sinematix',
            'DB_USER' => 'root',
            'DB_PASS' => '',
            'DB_CHARSET' => 'utf8mb4',
            'APP_ENV' => 'development',
            'APP_DEBUG' => 'true',
            'APP_NAME' => 'Sinematix',
            'SESSION_LIFETIME' => '7200',
            'SESSION_SECURE' => 'false',
            'SESSION_HTTPONLY' => 'true',
            'CACHE_ENABLED' => 'true',
            'CACHE_TTL' => '900',
        ];
    }

    /**
     * Get configuration value
     */
    public static function get(string $key, $default = null)
    {
        self::load();
        return self::$config[$key] ?? $default;
    }

    /**
     * Get all configuration
     */
    public static function all(): array
    {
        self::load();
        return self::$config;
    }

    /**
     * Get database configuration
     */
    public static function database(): array
    {
        return [
            'host' => self::get('DB_HOST', 'localhost'),
            'dbname' => self::get('DB_NAME', 'sinematix'),
            'username' => self::get('DB_USER', 'root'),
            'password' => self::get('DB_PASS', ''),
            'charset' => self::get('DB_CHARSET', 'utf8mb4'),
            'options' => [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]
        ];
    }

    /**
     * Check if debug mode is enabled
     */
    public static function isDebug(): bool
    {
        return self::get('APP_DEBUG', 'false') === 'true';
    }

    /**
     * Get environment
     */
    public static function env(): string
    {
        return self::get('APP_ENV', 'development');
    }
}
