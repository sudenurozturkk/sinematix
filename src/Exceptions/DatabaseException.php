<?php
/**
 * Custom Exception for Database Errors
 */

class DatabaseException extends Exception
{
    public function __construct(string $message = "", int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        
        // Log error if debug is disabled
        if (!Config::isDebug()) {
            error_log("Database Error: " . $message);
        }
    }
}
