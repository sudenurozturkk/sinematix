<?php
require_once __DIR__ . '/Database.php';

class User {
    
    /**
     * Kullanıcı kaydı
     */
    public static function register(array $data): ?int {
        // Email kontrolü
        if (self::emailExists($data['email'])) {
            return null;
        }
        
        $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
        
        Database::query("
            INSERT INTO users (name, email, password, phone)
            VALUES (?, ?, ?, ?)
        ", [
            $data['name'],
            $data['email'],
            $hashedPassword,
            $data['phone'] ?? null
        ]);
        
        return (int)Database::lastInsertId();
    }
    
    /**
     * Kullanıcı girişi
     */
    public static function login(string $email, string $password): ?array {
        $user = Database::fetch("
            SELECT * FROM users WHERE email = ?
        ", [$email]);
        
        if ($user && password_verify($password, $user['password'])) {
            unset($user['password']); // Şifreyi döndürme
            return $user;
        }
        
        return null;
    }
    
    /**
     * ID ile kullanıcı getir
     */
    public static function getById(int $id): ?array {
        $user = Database::fetch("
            SELECT id, name, email, phone, created_at FROM users WHERE id = ?
        ", [$id]);
        
        return $user;
    }
    
    /**
     * Email mevcut mu kontrol et
     */
    public static function emailExists(string $email): bool {
        $result = Database::fetch("
            SELECT id FROM users WHERE email = ?
        ", [$email]);
        
        return $result !== null;
    }
    
    /**
     * Kullanıcı bilgilerini güncelle
     */
    public static function update(int $id, array $data): bool {
        $fields = [];
        $params = [];
        
        if (isset($data['name'])) {
            $fields[] = 'name = ?';
            $params[] = $data['name'];
        }
        
        if (isset($data['phone'])) {
            $fields[] = 'phone = ?';
            $params[] = $data['phone'];
        }
        
        if (isset($data['password']) && !empty($data['password'])) {
            $fields[] = 'password = ?';
            $params[] = password_hash($data['password'], PASSWORD_DEFAULT);
        }
        
        if (empty($fields)) {
            return false;
        }
        
        $params[] = $id;
        $sql = "UPDATE users SET " . implode(', ', $fields) . " WHERE id = ?";
        
        Database::query($sql, $params);
        return true;
    }
    
    /**
     * Kullanıcının biletlerini getir
     */
    public static function getTickets(int $userId): array {
        return Database::fetchAll("
            SELECT 
                r.*,
                s.show_date,
                s.show_time,
                m.title as movie_title,
                m.poster as movie_poster,
                m.slug as movie_slug,
                h.name as hall_name,
                h.hall_type,
                c.name as cinema_name,
                c.city,
                c.district,
                (SELECT GROUP_CONCAT(CONCAT(st.row_letter, st.seat_number) ORDER BY st.row_letter, st.seat_number SEPARATOR ', ')
                 FROM reservation_seats rs2
                 JOIN seats st ON st.id = rs2.seat_id
                 WHERE rs2.reservation_id = r.id) as seat_labels
            FROM reservations r
            INNER JOIN showtimes s ON s.id = r.showtime_id
            INNER JOIN movies m ON m.id = s.movie_id
            INNER JOIN halls h ON h.id = s.hall_id
            INNER JOIN cinemas c ON c.id = h.cinema_id
            WHERE r.user_id = ?
            ORDER BY s.show_date DESC, s.show_time DESC
        ", [$userId]);
    }
    
    /**
     * Session'dan giriş yapmış kullanıcıyı al
     */
    public static function getCurrentUser(): ?array {
        if (isset($_SESSION['user_id'])) {
            return self::getById($_SESSION['user_id']);
        }
        return null;
    }
    
    /**
     * Giriş durumunu kontrol et
     */
    public static function isLoggedIn(): bool {
        return isset($_SESSION['user_id']);
    }
    
    /**
     * Oturumu başlat
     */
    public static function setSession(array $user): void {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email'] = $user['email'];
    }
    
    /**
     * Çıkış yap
     */
    public static function logout(): void {
        unset($_SESSION['user_id']);
        unset($_SESSION['user_name']);
        unset($_SESSION['user_email']);
        session_destroy();
    }
    
    /**
     * Bilet iptal et
     */
    public static function cancelTicket(int $reservationId, int $userId): bool {
        // Rezervasyonun bu kullanıcıya ait olduğunu kontrol et
        $reservation = Database::fetch("
            SELECT id, showtime_id, status FROM reservations 
            WHERE id = ? AND user_id = ? AND status = 'confirmed'
        ", [$reservationId, $userId]);
        
        if (!$reservation) {
            return false;
        }
        
        // Seans tarihi geçmemişse iptal edilebilir (sadece tarih kontrolü, saat kontrolü yok)
        $showtime = Database::fetch("
            SELECT show_date, show_time FROM showtimes WHERE id = ?
        ", [$reservation['showtime_id']]);
        
        if (!$showtime) {
            return false;
        }
        
        // Sadece geçmiş günlerin seansları iptal edilemez
        // Bugünün seansları saat ne olursa olsun iptal edilebilir
        $showDate = strtotime($showtime['show_date']);
        $today = strtotime('today');
        
        if ($showDate < $today) {
            return false; // Geçmiş günün seansı iptal edilemez
        }
        
        // İptal et
        Database::query("
            UPDATE reservations SET status = 'cancelled' WHERE id = ?
        ", [$reservationId]);
        
        return true;
    }
    
    /**
     * Kullanıcı hesabını sil
     */
    public static function deleteAccount(int $userId, string $password): bool {
        // Şifreyi doğrula
        $user = Database::fetch("
            SELECT password FROM users WHERE id = ?
        ", [$userId]);
        
        if (!$user || !password_verify($password, $user['password'])) {
            return false;
        }
        
        // Kullanıcının tüm rezervasyonlarını iptal et
        Database::query("
            UPDATE reservations SET status = 'cancelled' WHERE user_id = ?
        ", [$userId]);
        
        // Kullanıcıyı sil
        Database::query("
            DELETE FROM users WHERE id = ?
        ", [$userId]);
        
        return true;
    }
}
