<?php
/**
 * Cancel Ticket Handler
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require_once __DIR__ . '/../models/User.php';

header('Content-Type: application/json');

// Debug mode
$debug = isset($_GET['debug']) || isset($_POST['debug']);

try {
    // Check if user is logged in
    if (!User::isLoggedIn()) {
        echo json_encode(['success' => false, 'error' => 'Giriş yapmanız gerekiyor.', 'debug' => $debug ? 'Not logged in' : null]);
        exit;
    }

    // Check request method
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['success' => false, 'error' => 'Geçersiz istek.', 'debug' => $debug ? 'Not POST' : null]);
        exit;
    }

    $reservationId = (int)($_POST['reservation_id'] ?? 0);

    if (!$reservationId) {
        echo json_encode(['success' => false, 'error' => 'Geçersiz rezervasyon.', 'debug' => $debug ? 'No reservation_id' : null]);
        exit;
    }

    $userId = $_SESSION['user_id'];
    
    if ($debug) {
        error_log("Cancel ticket: reservation_id=$reservationId, user_id=$userId");
    }

    $result = User::cancelTicket($reservationId, $userId);

    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Biletiniz başarıyla iptal edildi.']);
    } else {
        echo json_encode(['success' => false, 'error' => 'Bilet iptal edilemedi. Geçmiş seanslar iptal edilemez veya bu bilet size ait değil.', 'debug' => $debug ? "Failed: res_id=$reservationId, user_id=$userId" : null]);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Sistem hatası: ' . $e->getMessage()]);
}

