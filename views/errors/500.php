<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 - Sunucu Hatası | Sinematix</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .error-page {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
            text-align: center;
        }
        .error-content {
            max-width: 600px;
        }
        .error-code {
            font-size: 8rem;
            font-weight: 800;
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            line-height: 1;
            margin-bottom: 20px;
        }
        .error-message {
            font-size: 1.5rem;
            margin-bottom: 15px;
            color: var(--text-primary);
        }
        .error-description {
            color: var(--text-secondary);
            margin-bottom: 40px;
        }
    </style>
</head>
<body>
    <div class="error-page">
        <div class="error-content">
            <div class="error-code">500</div>
            <h1 class="error-message">Bir Hata Oluştu</h1>
            <p class="error-description">
                Üzgünüz, sunucuda bir hata oluştu. Lütfen daha sonra tekrar deneyin.
                Sorun devam ederse lütfen destek ekibimizle iletişime geçin.
            </p>
            <div style="display: flex; gap: 15px; justify-content: center;">
                <a href="javascript:history.back()" class="btn btn-secondary btn-lg">← Geri Dön</a>
                <a href="index.php" class="btn btn-primary btn-lg">🏠 Ana Sayfa</a>
            </div>
        </div>
    </div>
</body>
</html>
