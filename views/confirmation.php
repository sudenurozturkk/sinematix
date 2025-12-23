<?php
/**
 * Confirmation Page
 */

require_once __DIR__ . '/../models/Showtime.php';
require_once __DIR__ . '/../models/Seat.php';
require_once __DIR__ . '/../models/Reservation.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$showtimeId = (int)$_POST['showtime_id'];
$selectedSeatIds = json_decode($_POST['selected_seats'], true);
$totalAmount = (float)$_POST['total_amount'];
$customerName = trim($_POST['customer_name'] ?? '');
$customerEmail = trim($_POST['customer_email'] ?? '');
$customerPhone = trim($_POST['customer_phone'] ?? '');

if (!$showtimeId || !$selectedSeatIds || !$customerName || !$customerEmail) {
    header('Location: index.php');
    exit;
}

$showtime = Showtime::getById($showtimeId);
$seats = Seat::getByIds($selectedSeatIds);

if (!$showtime || empty($seats)) {
    header('Location: index.php');
    exit;
}

// Koltuk müsaitliğini kontrol et
if (!Seat::validateSeats($selectedSeatIds, $showtimeId)) {
    header('Location: index.php?error=seats_taken');
    exit;
}

// Rezervasyon oluştur
$seatData = [];
foreach ($seats as $seat) {
    $seatData[] = [
        'id' => $seat['id'],
        'price' => $showtime['price']
    ];
}

$reservationId = Reservation::create([
    'user_id' => $_SESSION['user_id'] ?? null,
    'showtime_id' => $showtimeId,
    'customer_name' => $customerName,
    'customer_email' => $customerEmail,
    'customer_phone' => $customerPhone,
    'total_amount' => $totalAmount,
    'seats' => $seatData
]);

if (!$reservationId) {
    header('Location: index.php?error=reservation_failed');
    exit;
}

$reservation = Reservation::getById($reservationId);
$pageTitle = 'Bilet Onayı';

$dateObj = new DateTime($reservation['show_date']);
?>

<?php include __DIR__ . '/layouts/header.php'; ?>

<div class="confirmation-page">
    <div class="container">
        <div class="confirmation-card animate-slideUp">
            <div class="confirmation-icon">✓</div>
            
            <h1 class="confirmation-title">Biletiniz Hazır!</h1>
            <p class="confirmation-subtitle">Rezervasyon bilgileriniz e-posta adresinize gönderildi.</p>
            
            <div class="ticket-code">
                <label>Rezervasyon Kodu</label>
                <div class="code"><?= $reservation['reservation_code'] ?></div>
            </div>
            
            <div class="ticket-details">
                <div class="ticket-detail-row">
                    <label>Film</label>
                    <span><?= htmlspecialchars($reservation['movie_title']) ?></span>
                </div>
                <div class="ticket-detail-row">
                    <label>Sinema</label>
                    <span><?= htmlspecialchars($reservation['cinema_name']) ?></span>
                </div>
                <div class="ticket-detail-row">
                    <label>Salon</label>
                    <span><?= htmlspecialchars($reservation['hall_name']) ?> (<?= $reservation['hall_type'] ?>)</span>
                </div>
                <div class="ticket-detail-row">
                    <label>Tarih</label>
                    <span><?= $dateObj->format('d.m.Y') ?></span>
                </div>
                <div class="ticket-detail-row">
                    <label>Saat</label>
                    <span><?= substr($reservation['show_time'], 0, 5) ?></span>
                </div>
                <div class="ticket-detail-row">
                    <label>Koltuklar</label>
                    <span>
                        <?php 
                        $seatLabels = array_map(fn($s) => $s['row_letter'] . $s['seat_number'], $reservation['seats']);
                        echo implode(', ', $seatLabels);
                        ?>
                    </span>
                </div>
                <div class="ticket-detail-row">
                    <label>Toplam</label>
                    <span style="font-weight: 700; color: var(--accent-primary);">
                        <?= number_format($reservation['total_amount'], 0, ',', '.') ?> ₺
                    </span>
                </div>
            </div>
            
            <div style="display: flex; gap: 15px; justify-content: center;">
                <a href="index.php" class="btn btn-primary">🏠 Ana Sayfa</a>
                <button onclick="window.print()" class="btn btn-secondary">🖨️ Yazdır</button>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/layouts/footer.php'; ?>
