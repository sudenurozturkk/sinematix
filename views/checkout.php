<?php
/**
 * Checkout Page
 */

require_once __DIR__ . '/../models/Showtime.php';
require_once __DIR__ . '/../models/Seat.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['showtime_id']) || empty($_POST['selected_seats'])) {
    header('Location: index.php');
    exit;
}

$showtimeId = (int)$_POST['showtime_id'];
$selectedSeatIds = json_decode($_POST['selected_seats'], true);

if (!$selectedSeatIds || !is_array($selectedSeatIds)) {
    header('Location: index.php');
    exit;
}

$showtime = Showtime::getById($showtimeId);
$seats = Seat::getByIds($selectedSeatIds);

if (!$showtime || empty($seats)) {
    header('Location: index.php');
    exit;
}

$totalAmount = count($seats) * $showtime['price'];
$pageTitle = 'Ödeme';

$dateObj = new DateTime($showtime['show_date']);
?>

<?php include __DIR__ . '/layouts/header.php'; ?>

<div class="checkout-page">
    <div class="container">
        <h1 style="margin-bottom: 40px;">Ödeme</h1>
        
        <form action="index.php?page=complete" method="POST" class="checkout-grid" onsubmit="return validateCheckoutForm(this)">
            <input type="hidden" name="showtime_id" value="<?= $showtimeId ?>">
            <input type="hidden" name="selected_seats" value='<?= htmlspecialchars(json_encode($selectedSeatIds)) ?>'>
            <input type="hidden" name="total_amount" value="<?= $totalAmount ?>">
            
            <!-- Customer Form -->
            <div class="checkout-form">
                <h3 class="form-title">Müşteri Bilgileri</h3>
                
                <div class="form-group">
                    <label class="form-label">Ad Soyad *</label>
                    <input type="text" name="customer_name" class="form-input" placeholder="Adınız Soyadınız" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">E-posta Adresi *</label>
                    <input type="email" name="customer_email" class="form-input" placeholder="ornek@email.com" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Telefon Numarası</label>
                    <input type="tel" name="customer_phone" class="form-input" placeholder="0532 123 4567">
                </div>
                
                <h3 class="form-title" style="margin-top: 30px;">Kart Bilgileri (Demo)</h3>
                
                <div class="form-group">
                    <label class="form-label">Kart Numarası</label>
                    <input type="text" class="form-input" placeholder="1234 5678 9012 3456" value="4242 4242 4242 4242" readonly>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Son Kullanma</label>
                        <input type="text" class="form-input" placeholder="AA/YY" value="12/28" readonly>
                    </div>
                    <div class="form-group">
                        <label class="form-label">CVV</label>
                        <input type="text" class="form-input" placeholder="123" value="123" readonly>
                    </div>
                </div>
                
                <p style="color: var(--text-muted); font-size: 0.85rem; margin-top: 15px;">
                    ℹ️ Bu demo bir uygulamadır. Gerçek ödeme alınmayacaktır.
                </p>
                
                <button type="submit" class="btn btn-primary btn-lg" style="width: 100%; margin-top: 25px;">
                    🎫 Ödemeyi Tamamla
                </button>
            </div>
            
            <!-- Order Summary -->
            <div class="order-summary">
                <h3 class="form-title">Sipariş Özeti</h3>
                
                <div class="order-movie">
                    <img src="<?= htmlspecialchars($showtime['movie_poster']) ?>" alt="">
                    <div class="order-movie-info">
                        <h4><?= htmlspecialchars($showtime['movie_title']) ?></h4>
                        <div class="order-detail">📍 <?= htmlspecialchars($showtime['cinema_name']) ?></div>
                        <div class="order-detail">🎬 <?= htmlspecialchars($showtime['hall_name']) ?> (<?= $showtime['hall_type'] ?>)</div>
                        <div class="order-detail">📅 <?= $dateObj->format('d.m.Y') ?> • <?= substr($showtime['show_time'], 0, 5) ?></div>
                    </div>
                </div>
                
                <div class="order-items">
                    <h4 style="margin-bottom: 15px;">Seçilen Koltuklar</h4>
                    <?php foreach ($seats as $seat): ?>
                    <div class="order-item">
                        <span>Koltuk <?= $seat['row_letter'] . $seat['seat_number'] ?></span>
                        <span><?= number_format($showtime['price'], 0, ',', '.') ?> ₺</span>
                    </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="order-total">
                    <span>Toplam</span>
                    <span><?= number_format($totalAmount, 0, ',', '.') ?> ₺</span>
                </div>
            </div>
        </form>
    </div>
</div>

<?php include __DIR__ . '/layouts/footer.php'; ?>
