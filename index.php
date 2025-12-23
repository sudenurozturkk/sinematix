<?php
/**
 * Sinematix - Main Entry Point
 * Cinema Ticket Booking System
 */

session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Get requested page
$page = $_GET['page'] ?? 'home';

// Route to appropriate view
switch ($page) {
    case 'home':
        include __DIR__ . '/views/home.php';
        break;
        
    case 'movie':
        include __DIR__ . '/views/movie-detail.php';
        break;
        
    case 'select-seat':
        include __DIR__ . '/views/select-seat.php';
        break;
        
    case 'checkout':
        include __DIR__ . '/views/checkout.php';
        break;
        
    case 'complete':
        include __DIR__ . '/views/confirmation.php';
        break;
        
    case 'my-tickets':
        include __DIR__ . '/views/my-tickets.php';
        break;
    
    case 'login':
        include __DIR__ . '/views/login.php';
        break;
        
    case 'register':
        include __DIR__ . '/views/register.php';
        break;
        
    case 'profile':
        include __DIR__ . '/views/profile.php';
        break;
        
    case 'logout':
        include __DIR__ . '/views/logout.php';
        break;
        
    default:
        include __DIR__ . '/views/home.php';
        break;
}
