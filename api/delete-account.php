<?php
/**
 * Delete Account Handler
 */

session_start();
require_once __DIR__ . '/../models/User.php';

// Check if user is logged in
if (!User::isLoggedIn()) {
    header('Location: ../index.php?page=login');
    exit;
}

// Check request method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../index.php?page=profile');
    exit;
}

$password = $_POST['confirm_password'] ?? '';

if (empty($password)) {
    header('Location: ../index.php?page=profile&error=password_required');
    exit;
}

$result = User::deleteAccount($_SESSION['user_id'], $password);

if ($result) {
    // Clear session
    User::logout();
    session_start(); // Restart session for flash message
    $_SESSION['flash_message'] = 'Hesabınız başarıyla silindi.';
    header('Location: ../index.php');
} else {
    header('Location: ../index.php?page=profile&error=delete_failed&tab=settings');
}
exit;
