<?php
/**
 * My Tickets Page
 */

require_once __DIR__ . '/../models/Reservation.php';

$reservationCode = trim($_GET['code'] ?? '');
$reservation = null;
$error = null;

if ($reservationCode) {
    $reservation = Reservation::getByCode($reservationCode);
    if (!$reservation) {
        $error = 'Rezervasyon bulunamadı.';
    }
}

$pageTitle = 'Biletlerim';
?>

<?php include __DIR__ . '/layouts/header.php'; ?>

<div style="padding: 120px 0 60px;">
    <div class="container">
        <h1 style="margin-bottom: 40px; text-align: center;">Biletlerimi Sorgula</h1>
        
        <div style="max-width: 500px; margin: 0 auto;">
            <form action="" method="GET" class="checkout-form" style="padding: 30px;">
                <input type="hidden" name="page" value="my-tickets">
                
                <div class="form-group">
                    <label class="form-label">Rezervasyon Kodu</label>
                    <input type="text" name="code" class="form-input" 
                           placeholder="Örn: SNX231223ABC123" 
                           value="<?= htmlspecialchars($reservationCode) ?>" required>
                </div>
                
                <button type="submit" class="btn btn-primary" style="width: 100%;">
                    🔍 Biletimi Bul
                </button>
            </form>
            
            <?php if ($error): ?>
            <div style="background: rgba(239, 68, 68, 0.2); border: 1px solid #ef4444; border-radius: 12px; padding: 20px; margin-top: 20px; text-align: center;">
                <p style="color: #ef4444;">❌ <?= htmlspecialchars($error) ?></p>
            </div>
            <?php endif; ?>
            
            <?php if ($reservation): ?>
            <div class="confirmation-card animate-slideUp" style="margin-top: 30px; padding: 30px;">
                <h3 style="margin-bottom: 20px; text-align: center;">Bilet Bilgileri</h3>
                
                <div class="ticket-details" style="margin-bottom: 20px;">
                    <div class="ticket-detail-row">
                        <label>Rezervasyon Kodu</label>
                        <span style="color: var(--accent-primary); font-weight: 700;"><?= $reservation['reservation_code'] ?></span>
                    </div>
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
                        <span><?= htmlspecialchars($reservation['hall_name']) ?></span>
                    </div>
                    <div class="ticket-detail-row">
                        <label>Tarih / Saat</label>
                        <span><?= date('d.m.Y', strtotime($reservation['show_date'])) ?> - <?= substr($reservation['show_time'], 0, 5) ?></span>
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
                        <label>Durum</label>
                        <span style="color: #22c55e;">✓ <?= ucfirst($reservation['status']) ?></span>
                    </div>
                </div>
                
                <button onclick="window.print()" class="btn btn-secondary" style="width: 100%;">
                    🖨️ Bileti Yazdır
                </button>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include __DIR__ . '/layouts/footer.php'; ?>
