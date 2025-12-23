<?php
/**
 * Database Test Script
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../models/Database.php';

echo "<h2>Sinematix Database Test</h2>";

try {
    $db = Database::getInstance();
    echo "<p style='color: green;'>✅ Database connection successful</p>";
    
    // Test SELECT
    $users = Database::fetchAll("SELECT id, name, email FROM users LIMIT 5");
    echo "<h3>Users:</h3><pre>" . print_r($users, true) . "</pre>";
    
    // Test reservations
    $reservations = Database::fetchAll("SELECT id, user_id, reservation_code, status FROM reservations LIMIT 10");
    echo "<h3>Reservations:</h3><pre>" . print_r($reservations, true) . "</pre>";
    
    // Test UPDATE - try to update a reservation status
    if (!empty($reservations)) {
        $testResId = $reservations[0]['id'];
        echo "<h3>Test UPDATE on reservation $testResId:</h3>";
        
        $stmt = Database::query("UPDATE reservations SET status = 'cancelled' WHERE id = ?", [$testResId]);
        echo "<p>Rows affected: " . $stmt->rowCount() . "</p>";
        
        // Verify
        $updated = Database::fetch("SELECT id, status FROM reservations WHERE id = ?", [$testResId]);
        echo "<p>New status: " . ($updated['status'] ?? 'NOT FOUND') . "</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
