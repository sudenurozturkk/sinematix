<?php
/**
 * Application Bootstrap
 * Initialize application, load configuration, set error handlers
 */

// Load autoloader
require_once __DIR__ . '/autoload.php';

// Load configuration
require_once __DIR__ . '/config/Config.php';

// Load core classes
require_once __DIR__ . '/models/Database.php';

// Error handling based on environment
if (Config::isDebug()) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', '0');
    ini_set('log_errors', '1');
    ini_set('error_log', __DIR__ . '/logs/error.log');
}

// Set exception handler
set_exception_handler(function (Throwable $e) {
    if (Config::isDebug()) {
        echo "<pre>";
        echo "Error: " . $e->getMessage() . "\n";
        echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
        echo "Trace:\n" . $e->getTraceAsString();
        echo "</pre>";
    } else {
        error_log("Uncaught exception: " . $e->getMessage());
        http_response_code(500);
        if (file_exists(__DIR__ . '/views/errors/500.php')) {
            include __DIR__ . '/views/errors/500.php';
        } else {
            echo "An error occurred. Please try again later.";
        }
    }
});

// Start session with secure settings
if (session_status() === PHP_SESSION_NONE) {
    session_start([
        'cookie_lifetime' => (int)Config::get('SESSION_LIFETIME', 7200),
        'cookie_httponly' => Config::get('SESSION_HTTPONLY', 'true') === 'true',
        'cookie_secure' => Config::get('SESSION_SECURE', 'false') === 'true',
        'use_strict_mode' => true,
    ]);
}

// Set timezone
date_default_timezone_set('Europe/Istanbul');

// CSRF Token generation
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
