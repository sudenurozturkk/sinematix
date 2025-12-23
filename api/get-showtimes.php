<?php
/**
 * Get Showtimes API
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../models/Showtime.php';

$movieId = (int)($_GET['movie_id'] ?? 0);
$date = $_GET['date'] ?? date('Y-m-d');

if (!$movieId) {
    echo json_encode(['success' => false, 'error' => 'Movie ID required']);
    exit;
}

$showtimes = Showtime::getByMovieId($movieId, $date);

echo json_encode([
    'success' => true,
    'showtimes' => $showtimes
]);
