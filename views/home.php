<?php
/**
 * Home Page - Movie Listing
 */

require_once __DIR__ . '/../models/Movie.php';

$movies = Movie::getNowShowing();
$featuredMovie = $movies[0] ?? null;
$pageTitle = 'Vizyondaki Filmler';
?>

<?php include __DIR__ . '/layouts/header.php'; ?>

<?php if ($featuredMovie): ?>
<!-- Hero Section -->
<section class="hero">
    <div class="hero-backdrop">
        <img src="<?= htmlspecialchars($featuredMovie['backdrop'] ?: $featuredMovie['poster']) ?>" alt="">
    </div>
    <div class="container">
        <div class="hero-content animate-slideUp">
            <span class="hero-badge">⭐ Öne Çıkan Film</span>
            <h1 class="hero-title"><?= htmlspecialchars($featuredMovie['title']) ?></h1>
            <div class="hero-meta">
                <span>🎬 <?= htmlspecialchars($featuredMovie['director']) ?></span>
                <span>⏱️ <?= Movie::formatDuration($featuredMovie['duration']) ?></span>
                <span>⭐ <?= $featuredMovie['rating'] ?>/10</span>
                <span>🎭 <?= htmlspecialchars($featuredMovie['genre']) ?></span>
            </div>
            <p class="hero-description">
                <?= htmlspecialchars(substr($featuredMovie['description'], 0, 200)) ?>...
            </p>
            <div class="hero-actions">
                <a href="index.php?page=movie&slug=<?= $featuredMovie['slug'] ?>" class="btn btn-primary btn-lg">
                    🎫 Bilet Al
                </a>
                <a href="index.php?page=movie&slug=<?= $featuredMovie['slug'] ?>" class="btn btn-secondary btn-lg">
                    ℹ️ Detaylar
                </a>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Movies Section -->
<section class="section">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">Vizyondaki Filmler</h2>
        </div>
        
        <div class="movies-grid">
            <?php foreach ($movies as $movie): ?>
            <a href="index.php?page=movie&slug=<?= $movie['slug'] ?>" class="movie-card">
                <div class="movie-poster">
                    <img src="<?= htmlspecialchars($movie['poster']) ?>" alt="<?= htmlspecialchars($movie['title']) ?>">
                    <div class="movie-rating">
                        <span class="star">⭐</span>
                        <?= $movie['rating'] ?>
                    </div>
                    <div class="movie-overlay">
                        <span class="btn btn-primary">Bilet Al</span>
                    </div>
                </div>
                <div class="movie-info">
                    <h3 class="movie-title"><?= htmlspecialchars($movie['title']) ?></h3>
                    <div class="movie-meta">
                        <span class="movie-genre"><?= explode(',', $movie['genre'])[0] ?></span>
                        <span>⏱️ <?= Movie::formatDuration($movie['duration']) ?></span>
                    </div>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php include __DIR__ . '/layouts/footer.php'; ?>
