<?php
/**
 * Sinematix - Main Entry Point
 * Cinema Ticket Booking System
 */

// Load bootstrap
require_once __DIR__ . '/bootstrap.php';

// Load helpers
require_once __DIR__ . '/src/Helpers/ViewHelper.php';

// Get requested page
$page = $_GET['page'] ?? 'home';

// Allowed pages (whitelist for security)
$allowedPages = [
    'home', 'movie', 'select-seat', 'checkout', 'complete',
    'my-tickets', 'login', 'register', 'profile', 'logout'
];

// Validate page
if (!in_array($page, $allowedPages)) {
    http_response_code(404);
    if (file_exists(__DIR__ . '/views/errors/404.php')) {
        include __DIR__ . '/views/errors/404.php';
    } else {
        echo "Page not found";
    }
    exit;
}

// Map page to view file
$viewMap = [
    'home' => 'home.php',
    'movie' => 'movie-detail.php',
    'select-seat' => 'select-seat.php',
    'checkout' => 'checkout.php',
    'complete' => 'confirmation.php',
    'my-tickets' => 'my-tickets.php',
    'login' => 'login.php',
    'register' => 'register.php',
    'profile' => 'profile.php',
    'logout' => 'logout.php',
];

// Get view file
$viewFile = __DIR__ . '/views/' . $viewMap[$page];

// Check if view exists
if (!file_exists($viewFile)) {
    http_response_code(404);
    if (file_exists(__DIR__ . '/views/errors/404.php')) {
        include __DIR__ . '/views/errors/404.php';
    } else {
        echo "View file not found";
    }
    exit;
}

// Include view
try {
    include $viewFile;
} catch (Exception $e) {
    error_log("View error: " . $e->getMessage());
    http_response_code(500);
    if (file_exists(__DIR__ . '/views/errors/500.php')) {
        include __DIR__ . '/views/errors/500.php';
    } else {
        echo "An error occurred";
    }
}
