<?php
/**
 * Register Page
 */

require_once __DIR__ . '/../models/User.php';

$error = null;
$success = null;

// Redirect if already logged in
if (User::isLoggedIn()) {
    header('Location: index.php?page=profile');
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $passwordConfirm = $_POST['password_confirm'] ?? '';
    
    // Validation
    if (empty($name) || empty($email) || empty($password)) {
        $error = 'Ad Soyad, E-posta ve Şifre gereklidir.';
    } elseif (strlen($password) < 6) {
        $error = 'Şifre en az 6 karakter olmalıdır.';
    } elseif ($password !== $passwordConfirm) {
        $error = 'Şifreler eşleşmiyor.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Geçerli bir e-posta adresi giriniz.';
    } elseif (User::emailExists($email)) {
        $error = 'Bu e-posta adresi zaten kullanılıyor.';
    } else {
        $userId = User::register([
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'phone' => $phone
        ]);
        
        if ($userId) {
            // Auto login after registration
            $user = User::getById($userId);
            User::setSession($user);
            
            header('Location: index.php?page=profile&welcome=1');
            exit;
        } else {
            $error = 'Kayıt sırasında bir hata oluştu.';
        }
    }
}

$pageTitle = 'Kayıt Ol';
?>

<?php include __DIR__ . '/layouts/header.php'; ?>

<div style="padding: 120px 0 60px;">
    <div class="container">
        <div style="max-width: 450px; margin: 0 auto;">
            <div class="checkout-form" style="padding: 40px;">
                <div style="text-align: center; margin-bottom: 30px;">
                    <h2>🎬 Hesap Oluştur</h2>
                    <p style="color: var(--text-muted); margin-top: 10px;">Sinematix'e üye ol, bilet al!</p>
                </div>
                
                <?php if ($error): ?>
                <div style="background: rgba(239, 68, 68, 0.2); border: 1px solid #ef4444; border-radius: 12px; padding: 15px; margin-bottom: 20px; text-align: center;">
                    <p style="color: #ef4444; margin: 0;">❌ <?= htmlspecialchars($error) ?></p>
                </div>
                <?php endif; ?>
                
                <form method="POST" action="">
                    <div class="form-group">
                        <label class="form-label">Ad Soyad *</label>
                        <input type="text" name="name" id="reg-name" class="form-input" 
                               placeholder="Adınız Soyadınız" 
                               value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">E-posta Adresi *</label>
                        <input type="email" name="email" id="reg-email" class="form-input" 
                               placeholder="ornek@email.com" 
                               value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Telefon Numarası</label>
                        <input type="tel" name="phone" id="reg-phone" class="form-input" 
                               placeholder="0532 123 4567"
                               value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Şifre *</label>
                        <input type="password" name="password" id="reg-password" class="form-input" 
                               placeholder="En az 6 karakter" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Şifre Tekrar *</label>
                        <input type="password" name="password_confirm" id="reg-password-confirm" class="form-input" 
                               placeholder="Şifrenizi tekrar girin" required>
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-lg" style="width: 100%; margin-top: 10px;">
                        Kayıt Ol →
                    </button>
                </form>
                
                <div style="text-align: center; margin-top: 25px; padding-top: 25px; border-top: 1px solid var(--border-color);">
                    <p style="color: var(--text-muted);">
                        Zaten hesabınız var mı? 
                        <a href="index.php?page=login" style="color: var(--accent-primary); font-weight: 600;">Giriş Yap</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/layouts/footer.php'; ?>
