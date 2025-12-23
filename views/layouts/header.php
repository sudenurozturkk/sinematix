<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'Sinematix' ?> - Sinema Bileti Al</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header class="header">
        <div class="container">
            <nav class="nav">
                <a href="index.php" class="logo">
                    <span class="logo-icon">🎬</span>
                    SINEMATIX
                </a>
                <ul class="nav-links">
                    <li><a href="index.php" class="nav-link <?= (!isset($_GET['page']) || $_GET['page'] === 'home') ? 'active' : '' ?>">Filmler</a></li>
                    <li><a href="index.php?page=cinemas" class="nav-link">Sinemalar</a></li>
                </ul>
                <div class="nav-actions">
                    <div class="search-box">
                        <svg class="search-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="11" cy="11" r="8"></circle>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                        </svg>
                        <input type="text" class="search-input" placeholder="Film ara...">
                    </div>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <a href="index.php?page=profile" class="btn btn-secondary btn-sm">👤 <?= htmlspecialchars($_SESSION['user_name'] ?? 'Profil') ?></a>
                    <?php else: ?>
                        <a href="index.php?page=login" class="btn btn-secondary btn-sm">🔐 Giriş</a>
                        <a href="index.php?page=register" class="btn btn-primary btn-sm">Üye Ol</a>
                    <?php endif; ?>
                </div>
            </nav>
        </div>
    </header>
