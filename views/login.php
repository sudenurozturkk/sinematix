<?php
/**
 * Login Page
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
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        $error = 'E-posta ve şifre gereklidir.';
    } else {
        $user = User::login($email, $password);
        
        if ($user) {
            User::setSession($user);
            
            // Redirect to intended page or profile
            $redirect = $_GET['redirect'] ?? 'index.php?page=profile';
            header('Location: ' . $redirect);
            exit;
        } else {
            $error = 'E-posta veya şifre hatalı.';
        }
    }
}

$pageTitle = 'Giriş Yap';
?>

<?php include __DIR__ . '/layouts/header.php'; ?>

<div style="padding: 120px 0 60px;">
    <div class="container">
        <div style="max-width: 450px; margin: 0 auto;">
            <div class="checkout-form" style="padding: 40px;">
                <div style="text-align: center; margin-bottom: 30px;">
                    <h2>🎬 Giriş Yap</h2>
                    <p style="color: var(--text-muted); margin-top: 10px;">Hesabınıza giriş yapın</p>
                </div>
                
                <?php if ($error): ?>
                <div style="background: rgba(239, 68, 68, 0.2); border: 1px solid #ef4444; border-radius: 12px; padding: 15px; margin-bottom: 20px; text-align: center;">
                    <p style="color: #ef4444; margin: 0;">❌ <?= htmlspecialchars($error) ?></p>
                </div>
                <?php endif; ?>
                
                <form method="POST" action="">
                    <div class="form-group">
                        <label class="form-label">E-posta Adresi</label>
                        <input type="email" name="email" class="form-input" 
                               placeholder="ornek@email.com" 
                               value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Şifre</label>
                        <input type="password" name="password" class="form-input" 
                               placeholder="••••••••" required>
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-lg" style="width: 100%; margin-top: 10px;">
                        Giriş Yap →
                    </button>
                </form>
                
                <div style="text-align: center; margin-top: 25px; padding-top: 25px; border-top: 1px solid var(--border-color);">
                    <p style="color: var(--text-muted);">
                        Hesabınız yok mu? 
                        <a href="index.php?page=register" style="color: var(--accent-primary); font-weight: 600;">Kayıt Ol</a>
                    </p>
                </div>
                
                <div style="text-align: center; margin-top: 20px; padding: 15px; background: var(--bg-tertiary); border-radius: 10px;">
                    <p style="color: var(--text-muted); font-size: 0.85rem; margin: 0;">
                        🎫 Demo hesap: <strong>demo@sinematix.com</strong> / <strong>password</strong>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/layouts/footer.php'; ?>
