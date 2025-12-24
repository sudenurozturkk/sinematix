<?php
/**
 * Simple PSR-4 Autoloader
 */

spl_autoload_register(function ($class) {
    // Base directory for the namespace prefix
    $baseDir = __DIR__ . '/src/';
    
    // Replace namespace separators with directory separators
    $file = $baseDir . str_replace('\\', '/', $class) . '.php';
    
    // If the file exists, require it
    if (file_exists($file)) {
        require $file;
        return true;
    }
    
    return false;
});
