<?php
/**
 * Seat Selection Page
 */

require_once __DIR__ . '/../models/Showtime.php';
require_once __DIR__ . '/../models/Seat.php';
require_once __DIR__ . '/../models/Movie.php';

$showtimeId = (int)($_GET['showtime'] ?? 0);
$showtime = Showtime::getById($showtimeId);

if (!$showtime) {
    header('Location: index.php');
    exit;
}

$seats = Seat::getSeatsWithStatus($showtimeId);
$groupedSeats = Seat::groupByRow($seats);
$pageTitle = 'Koltuk Seçimi - ' . $showtime['movie_title'];

$dayNames = ['Pazar', 'Pazartesi', 'Salı', 'Çarşamba', 'Perşembe', 'Cuma', 'Cumartesi'];
$dateObj = new DateTime($showtime['show_date']);
?>

<?php include __DIR__ . '/layouts/header.php'; ?>

<div style="padding-top: 100px;">
    <div class="container">
        <!-- Breadcrumb Info -->
        <div style="margin-bottom: 30px; text-align: center;">
            <h2><?= htmlspecialchars($showtime['movie_title']) ?></h2>
            <p style="color: var(--text-secondary); margin-top: 10px;">
                <?= htmlspecialchars($showtime['cinema_name']) ?> • 
                <?= htmlspecialchars($showtime['hall_name']) ?> (<?= $showtime['hall_type'] ?>) •
                <?= $dayNames[(int)$dateObj->format('w')] ?>, <?= $dateObj->format('d.m.Y') ?> •
                <?= substr($showtime['show_time'], 0, 5) ?>
            </p>
        </div>
        
        <!-- Seat Selection -->
        <section class="seat-selection">
            <input type="hidden" id="ticket-price" value="<?= $showtime['price'] ?>">
            <input type="hidden" id="showtime-id" value="<?= $showtimeId ?>">
            
            <!-- Screen -->
            <div class="screen">
                <div class="screen-visual"></div>
                <span class="screen-label">Perde</span>
            </div>
            
            <!-- Seats Grid -->
            <div class="seats-container">
                <?php foreach ($groupedSeats as $row => $rowSeats): ?>
                <div class="seat-row">
                    <span class="row-label"><?= $row ?></span>
                    <?php foreach ($rowSeats as $seat): ?>
                    <div class="seat <?= $seat['status'] === 'occupied' ? 'occupied' : '' ?> <?= $seat['seat_type'] === 'vip' ? 'vip' : '' ?>"
                         data-seat-id="<?= $seat['id'] ?>"
                         data-row="<?= $seat['row_letter'] ?>"
                         data-number="<?= $seat['seat_number'] ?>"
                         title="<?= $seat['row_letter'] . $seat['seat_number'] ?>">
                    </div>
                    <?php endforeach; ?>
                    <span class="row-label"><?= $row ?></span>
                </div>
                <?php endforeach; ?>
            </div>
            
            <!-- Legend -->
            <div class="seat-legend">
                <div class="legend-item">
                    <div class="legend-seat available"></div>
                    <span>Boş</span>
                </div>
                <div class="legend-item">
                    <div class="legend-seat selected"></div>
                    <span>Seçili</span>
                </div>
                <div class="legend-item">
                    <div class="legend-seat occupied"></div>
                    <span>Dolu</span>
                </div>
            </div>
        </section>
    </div>
</div>

<!-- Booking Summary (Fixed Bottom) -->
<form action="index.php?page=checkout" method="POST" class="booking-summary">
    <input type="hidden" name="showtime_id" value="<?= $showtimeId ?>">
    <input type="hidden" name="selected_seats" id="selected-seats" value="">
    
    <div class="summary-content">
        <div class="summary-info">
            <div class="summary-movie">
                <img src="<?= htmlspecialchars($showtime['movie_poster']) ?>" alt="">
                <div>
                    <strong><?= htmlspecialchars($showtime['movie_title']) ?></strong>
                    <p style="color: var(--text-muted); font-size: 0.85rem;">
                        <?= $dateObj->format('d.m.Y') ?> • <?= substr($showtime['show_time'], 0, 5) ?>
                    </p>
                </div>
            </div>
            <div class="summary-seats"></div>
        </div>
        <div style="display: flex; align-items: center; gap: 30px;">
            <div class="summary-total">
                <span class="total-label">Toplam</span>
                <span class="total-amount">0 ₺</span>
            </div>
            <button type="submit" class="btn btn-primary btn-lg">Devam Et →</button>
        </div>
    </div>
</form>

<?php include __DIR__ . '/layouts/footer.php'; ?>
