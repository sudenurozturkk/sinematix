<?php
/**
 * Profile Page
 */

require_once __DIR__ . '/../models/User.php';

// Redirect if not logged in
if (!User::isLoggedIn()) {
    header('Location: index.php?page=login');
    exit;
}

$user = User::getCurrentUser();
$tickets = User::getTickets($user['id']);
$success = null;
$error = null;

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update') {
    $result = User::update($user['id'], [
        'name' => trim($_POST['name'] ?? ''),
        'phone' => trim($_POST['phone'] ?? ''),
        'password' => $_POST['new_password'] ?? ''
    ]);
    
    if ($result) {
        $success = 'Profil bilgileriniz güncellendi.';
        $user = User::getCurrentUser(); // Refresh user data
        $_SESSION['user_name'] = $user['name'];
    } else {
        $error = 'Güncelleme sırasında hata oluştu.';
    }
}

$isWelcome = isset($_GET['welcome']);
$pageTitle = 'Profilim';

$upcomingTickets = array_filter($tickets, fn($t) => strtotime($t['show_date']) >= strtotime('today') && ($t['status'] ?? 'confirmed') === 'confirmed');
$pastTickets = array_filter($tickets, fn($t) => strtotime($t['show_date']) < strtotime('today') || ($t['status'] ?? 'confirmed') === 'cancelled');

// Handle errors from URL
if (isset($_GET['error'])) {
    if ($_GET['error'] === 'delete_failed') {
        $error = 'Şifre hatalı veya hesap silinemedi.';
    } elseif ($_GET['error'] === 'password_required') {
        $error = 'Hesabı silmek için şifre gerekli.';
    }
}
?>

<?php include __DIR__ . '/layouts/header.php'; ?>

<div style="padding: 100px 0 60px;">
    <div class="container">
        <?php if ($isWelcome): ?>
        <div style="background: linear-gradient(135deg, rgba(34, 197, 94, 0.2) 0%, rgba(20, 184, 166, 0.2) 100%); border: 1px solid #22c55e; border-radius: 16px; padding: 25px; margin-bottom: 30px; text-align: center;">
            <h3 style="margin-bottom: 10px;">🎉 Hoş Geldiniz, <?= htmlspecialchars($user['name']) ?>!</h3>
            <p style="color: var(--text-secondary); margin: 0;">Hesabınız başarıyla oluşturuldu. Artık bilet alabilirsiniz!</p>
        </div>
        <?php endif; ?>
        
        <div style="display: grid; grid-template-columns: 300px 1fr; gap: 30px;">
            <!-- Profile Sidebar -->
            <div>
                <div class="checkout-form" style="padding: 30px; text-align: center;">
                    <div style="width: 100px; height: 100px; background: var(--gradient-primary); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; font-size: 2.5rem;">
                        <?= strtoupper(substr($user['name'], 0, 1)) ?>
                    </div>
                    <h3><?= htmlspecialchars($user['name']) ?></h3>
                    <p style="color: var(--text-muted); margin-top: 5px;"><?= htmlspecialchars($user['email']) ?></p>
                    
                    <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid var(--border-color);">
                        <div style="display: flex; justify-content: space-around; text-align: center;">
                            <div>
                                <div style="font-size: 1.5rem; font-weight: 700; color: var(--accent-primary);"><?= count($tickets) ?></div>
                                <div style="font-size: 0.85rem; color: var(--text-muted);">Toplam Bilet</div>
                            </div>
                            <div>
                                <div style="font-size: 1.5rem; font-weight: 700; color: var(--accent-teal);"><?= count($upcomingTickets) ?></div>
                                <div style="font-size: 0.85rem; color: var(--text-muted);">Yaklaşan</div>
                            </div>
                        </div>
                    </div>
                    
                    <a href="index.php?page=logout" class="btn btn-secondary" style="width: 100%; margin-top: 20px;">
                        🚪 Çıkış Yap
                    </a>
                </div>
            </div>
            
            <!-- Main Content -->
            <div>
                <!-- Tabs -->
                <div style="display: flex; gap: 10px; margin-bottom: 30px;">
                    <button class="btn btn-primary tab-btn active" data-tab="tickets">🎫 Biletlerim</button>
                    <button class="btn btn-secondary tab-btn" data-tab="settings">⚙️ Ayarlar</button>
                </div>
                
                <!-- Tickets Tab -->
                <div id="tab-tickets" class="tab-content active">
                    <?php if (count($upcomingTickets) > 0): ?>
                    <h3 style="margin-bottom: 20px;">📅 Yaklaşan Biletler</h3>
                    <div style="display: grid; gap: 20px; margin-bottom: 40px;">
                        <?php foreach ($upcomingTickets as $ticket): ?>
                        <div class="checkout-form" style="padding: 20px; display: flex; gap: 20px; align-items: center;">
                            <img src="<?= htmlspecialchars($ticket['movie_poster']) ?>" alt="" 
                                 style="width: 80px; height: 120px; object-fit: cover; border-radius: 10px;">
                            <div style="flex: 1;">
                                <h4><?= htmlspecialchars($ticket['movie_title']) ?></h4>
                                <p style="color: var(--text-muted); margin-top: 5px;">
                                    📍 <?= htmlspecialchars($ticket['cinema_name']) ?> - <?= htmlspecialchars($ticket['hall_name']) ?>
                                </p>
                                <p style="color: var(--text-secondary); margin-top: 5px;">
                                    📅 <?= date('d.m.Y', strtotime($ticket['show_date'])) ?> • 
                                    🕐 <?= substr($ticket['show_time'], 0, 5) ?> • 
                                    🪑 <?= $ticket['seat_labels'] ?>
                                </p>
                            </div>
                            <div style="text-align: right;">
                                <div style="background: var(--accent-purple); padding: 8px 15px; border-radius: 8px; font-weight: 600; margin-bottom: 10px;">
                                    <?= $ticket['reservation_code'] ?>
                                </div>
                                <span style="color: var(--accent-gold); font-weight: 600; display: block; margin-bottom: 10px;">
                                    <?= number_format($ticket['total_amount'], 0, ',', '.') ?> ₺
                                </span>
                                <button class="btn btn-sm cancel-ticket-btn" 
                                        style="background: rgba(239, 68, 68, 0.2); color: #ef4444; border: 1px solid #ef4444; font-size: 0.75rem; padding: 6px 12px;"
                                        data-reservation-id="<?= $ticket['id'] ?>">
                                    ❌ İptal Et
                                </button>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (count($pastTickets) > 0): ?>
                    <h3 style="margin-bottom: 20px;">📜 Geçmiş Biletler</h3>
                    <div style="display: grid; gap: 15px;">
                        <?php foreach ($pastTickets as $ticket): ?>
                        <div class="checkout-form" style="padding: 15px; display: flex; gap: 15px; align-items: center; opacity: 0.7;">
                            <img src="<?= htmlspecialchars($ticket['movie_poster']) ?>" alt="" 
                                 style="width: 50px; height: 75px; object-fit: cover; border-radius: 8px;">
                            <div style="flex: 1;">
                                <h4 style="font-size: 1rem;"><?= htmlspecialchars($ticket['movie_title']) ?></h4>
                                <p style="color: var(--text-muted); font-size: 0.85rem;">
                                    <?= date('d.m.Y', strtotime($ticket['show_date'])) ?> • <?= $ticket['seat_labels'] ?>
                                </p>
                            </div>
                            <span style="color: var(--text-muted);"><?= $ticket['reservation_code'] ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (count($tickets) === 0): ?>
                    <div style="text-align: center; padding: 60px 20px; background: var(--bg-glass); border-radius: 16px;">
                        <div style="font-size: 4rem; margin-bottom: 20px;">🎫</div>
                        <h3>Henüz biletiniz yok</h3>
                        <p style="color: var(--text-muted); margin-top: 10px;">Hemen bir film seçip bilet alın!</p>
                        <a href="index.php" class="btn btn-primary" style="margin-top: 20px;">🎬 Filmlere Göz At</a>
                    </div>
                    <?php endif; ?>
                </div>
                
                <!-- Settings Tab -->
                <div id="tab-settings" class="tab-content" style="display: none;">
                    <?php if ($success): ?>
                    <div style="background: rgba(34, 197, 94, 0.2); border: 1px solid #22c55e; border-radius: 12px; padding: 15px; margin-bottom: 20px;">
                        <p style="color: #22c55e; margin: 0;">✓ <?= htmlspecialchars($success) ?></p>
                    </div>
                    <?php endif; ?>
                    
                    <div class="checkout-form" style="padding: 30px;">
                        <h3 class="form-title">Profil Bilgileri</h3>
                        
                        <form method="POST" action="">
                            <input type="hidden" name="action" value="update">
                            
                            <div class="form-group">
                                <label class="form-label">Ad Soyad</label>
                                <input type="text" name="name" class="form-input" 
                                       value="<?= htmlspecialchars($user['name']) ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">E-posta</label>
                                <input type="email" class="form-input" 
                                       value="<?= htmlspecialchars($user['email']) ?>" disabled>
                                <small style="color: var(--text-muted);">E-posta adresi değiştirilemez.</small>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Telefon</label>
                                <input type="tel" name="phone" class="form-input" 
                                       placeholder="0532 123 4567"
                                       value="<?= htmlspecialchars($user['phone'] ?? '') ?>">
                            </div>
                            
                            <h3 class="form-title" style="margin-top: 30px;">Şifre Değiştir</h3>
                            
                            <div class="form-group">
                                <label class="form-label">Yeni Şifre</label>
                                <input type="password" name="new_password" class="form-input" 
                                       placeholder="Değiştirmek istemiyorsanız boş bırakın">
                            </div>
                            
                            <button type="submit" class="btn btn-primary" style="margin-top: 10px;">
                                💾 Kaydet
                            </button>
                        </form>
                    </div>
                    
                    <!-- Danger Zone: Delete Account -->
                    <div class="checkout-form" style="padding: 30px; margin-top: 30px; border: 1px solid rgba(239, 68, 68, 0.3);">
                        <h3 class="form-title" style="color: #ef4444;">⚠️ Tehlikeli Bölge</h3>
                        
                        <?php if ($error): ?>
                        <div style="background: rgba(239, 68, 68, 0.2); border: 1px solid #ef4444; border-radius: 12px; padding: 15px; margin-bottom: 20px;">
                            <p style="color: #ef4444; margin: 0;">❌ <?= htmlspecialchars($error) ?></p>
                        </div>
                        <?php endif; ?>
                        
                        <p style="color: var(--text-muted); margin-bottom: 20px;">
                            Hesabınızı sildiğinizde tüm verileriniz kalıcı olarak silinecektir. Bu işlem geri alınamaz.
                        </p>
                        
                        <form method="POST" action="api/delete-account.php" id="delete-account-form">
                            <div class="form-group">
                                <label class="form-label">Şifreinizi Girin (Onay için)</label>
                                <input type="password" name="confirm_password" class="form-input" 
                                       placeholder="Mevcut şifreniz" required>
                            </div>
                            
                            <button type="submit" class="btn" 
                                    style="background: #ef4444; color: white; margin-top: 10px;"
                                    onclick="return confirm('Hesabınızı silmek istediğinizden emin misiniz? Bu işlem geri alınamaz!');">
                                🗑️ Hesabımı Sil
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.querySelectorAll('.tab-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        document.querySelectorAll('.tab-btn').forEach(b => {
            b.classList.remove('active');
            b.classList.remove('btn-primary');
            b.classList.add('btn-secondary');
        });
        this.classList.add('active');
        this.classList.remove('btn-secondary');
        this.classList.add('btn-primary');
        
        document.querySelectorAll('.tab-content').forEach(c => c.style.display = 'none');
        document.getElementById('tab-' + this.dataset.tab).style.display = 'block';
    });
});

// Cancel ticket functionality
document.querySelectorAll('.cancel-ticket-btn').forEach(btn => {
    btn.addEventListener('click', async function() {
        if (!confirm('Bu bileti iptal etmek istediğinizden emin misiniz?')) {
            return;
        }
        
        const reservationId = this.dataset.reservationId;
        const ticketCard = this.closest('.checkout-form');
        
        try {
            const response = await fetch('api/cancel-ticket.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'reservation_id=' + reservationId
            });
            
            const result = await response.json();
            
            if (result.success) {
                ticketCard.style.opacity = '0.5';
                ticketCard.innerHTML += '<div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background: rgba(239, 68, 68, 0.9); padding: 10px 20px; border-radius: 8px; font-weight: bold;">İPTAL EDİLDİ</div>';
                ticketCard.style.position = 'relative';
                this.remove();
                
                setTimeout(() => location.reload(), 1500);
            } else {
                alert(result.error || 'Bilet iptal edilemedi.');
            }
        } catch (error) {
            alert('Bir hata oluştu. Lütfen tekrar deneyin.');
        }
    });
});

// Open settings tab if there's an error
<?php if (isset($_GET['tab']) && $_GET['tab'] === 'settings'): ?>
document.querySelector('[data-tab="settings"]').click();
<?php endif; ?>
</script>

<?php include __DIR__ . '/layouts/footer.php'; ?>
