<?php
/**
 * Logout Handler
 */

session_start();
require_once __DIR__ . '/../models/User.php';

User::logout();
header('Location: index.php');
exit;
