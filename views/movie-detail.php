<?php
/**
 * Movie Detail Page
 */

require_once __DIR__ . '/../models/Movie.php';
require_once __DIR__ . '/../models/Showtime.php';

$slug = $_GET['slug'] ?? '';
$movie = Movie::getBySlug($slug);

if (!$movie) {
    header('Location: index.php');
    exit;
}

$dates = Showtime::getAvailableDates($movie['id']);
$selectedDate = $_GET['date'] ?? ($dates[0]['show_date'] ?? date('Y-m-d'));
$showtimes = Showtime::getByMovieId($movie['id'], $selectedDate);
$groupedShowtimes = Showtime::groupByCinema($showtimes);
$pageTitle = $movie['title'];

$dayNames = ['Paz', 'Pzt', 'Sal', 'Çar', 'Per', 'Cum', 'Cmt'];
$monthNames = ['Oca', 'Şub', 'Mar', 'Nis', 'May', 'Haz', 'Tem', 'Ağu', 'Eyl', 'Eki', 'Kas', 'Ara'];
?>

<?php include __DIR__ . '/layouts/header.php'; ?>

<!-- Movie Hero -->
<section class="movie-detail-hero">
    <div class="movie-detail-backdrop">
        <img src="<?= htmlspecialchars($movie['backdrop'] ?: $movie['poster']) ?>" alt="">
    </div>
    <div class="container">
        <div class="movie-detail-content">
            <div class="movie-detail-poster animate-fadeIn">
                <img src="<?= htmlspecialchars($movie['poster']) ?>" alt="<?= htmlspecialchars($movie['title']) ?>">
            </div>
            <div class="movie-detail-info animate-slideUp">
                <h1><?= htmlspecialchars($movie['title']) ?></h1>
                <?php if ($movie['original_title'] && $movie['original_title'] !== $movie['title']): ?>
                <p class="movie-detail-original-title"><?= htmlspecialchars($movie['original_title']) ?></p>
                <?php endif; ?>
                
                <div class="movie-detail-meta">
                    <span class="meta-tag">⏱️ <?= ViewHelper::formatDuration($movie['duration']) ?></span>
                    <span class="meta-tag">🎭 <?= htmlspecialchars($movie['genre']) ?></span>
                    <span class="meta-tag">🗣️ <?= htmlspecialchars($movie['language']) ?></span>
                    <span class="meta-tag age-limit"><?= htmlspecialchars($movie['age_limit']) ?></span>
                </div>
                
                <div class="movie-detail-rating">
                    <span class="rating-score"><?= $movie['rating'] ?></span>
                    <div class="rating-stars">
                        <?php for ($i = 0; $i < 5; $i++): ?>
                            <?= ($i < $movie['rating']/2) ? '⭐' : '☆' ?>
                        <?php endfor; ?>
                    </div>
                </div>
                
                <p class="movie-detail-description"><?= htmlspecialchars($movie['description']) ?></p>
                
                <div class="movie-detail-credits">
                    <div class="credit-item">
                        <label>Yönetmen</label>
                        <span><?= htmlspecialchars($movie['director']) ?></span>
                    </div>
                    <div class="credit-item">
                        <label>Oyuncular</label>
                        <span><?= htmlspecialchars($movie['cast']) ?></span>
                    </div>
                </div>
                
                <div class="movie-detail-actions">
                    <?php if ($movie['trailer_url']): ?>
                    <a href="<?= htmlspecialchars($movie['trailer_url']) ?>" target="_blank" class="btn btn-secondary btn-lg">
                        ▶️ Fragmanı İzle
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Showtimes Section -->
<section class="showtime-section">
    <div class="container">
        <h2 class="section-title" style="margin-bottom: 30px;">Seans Seçin</h2>
        
        <input type="hidden" id="movie-id" value="<?= $movie['id'] ?>">
        
        <!-- Date Picker -->
        <div class="date-picker">
            <?php foreach ($dates as $i => $date): 
                $dateObj = new DateTime($date['show_date']);
                $isActive = ($date['show_date'] === $selectedDate);
            ?>
            <a href="index.php?page=movie&slug=<?= $slug ?>&date=<?= $date['show_date'] ?>" 
               class="date-item <?= $isActive ? 'active' : '' ?>"
               data-date="<?= $date['show_date'] ?>">
                <span class="day-name"><?= $dayNames[(int)$dateObj->format('w')] ?></span>
                <span class="day-number"><?= $dateObj->format('d') ?></span>
                <span class="month"><?= $monthNames[(int)$dateObj->format('n') - 1] ?></span>
            </a>
            <?php endforeach; ?>
        </div>
        
        <!-- Showtimes -->
        <div class="showtimes-container">
            <?php if (empty($groupedShowtimes)): ?>
            <p style="text-align: center; color: var(--text-muted);">Bu tarihte seans bulunamadı.</p>
            <?php else: ?>
                <?php foreach ($groupedShowtimes as $cinema): ?>
                <div class="cinema-card animate-slideUp">
                    <div class="cinema-header">
                        <h3 class="cinema-name"><?= htmlspecialchars($cinema['cinema_name']) ?></h3>
                        <span class="cinema-location">
                            📍 <?= htmlspecialchars($cinema['district']) ?>, <?= htmlspecialchars($cinema['city']) ?>
                        </span>
                    </div>
                    
                    <?php foreach ($cinema['halls'] as $hall): ?>
                    <div class="hall-row">
                        <div class="hall-info">
                            <span class="hall-name"><?= htmlspecialchars($hall['hall_name']) ?></span>
                            <span class="hall-type"><?= htmlspecialchars($hall['hall_type']) ?></span>
                        </div>
                        <div class="showtimes-list">
                            <?php foreach ($hall['showtimes'] as $showtime): ?>
                            <a href="index.php?page=select-seat&showtime=<?= $showtime['id'] ?>" class="showtime-btn">
                                <span class="time"><?= substr($showtime['show_time'], 0, 5) ?></span>
                                <span class="price"><?= number_format($showtime['price'], 0, ',', '.') ?> ₺</span>
                            </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php include __DIR__ . '/layouts/footer.php'; ?>
