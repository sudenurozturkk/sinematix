<?php
require_once __DIR__ . '/Database.php';

class Reservation {
    
    /**
     * Create new reservation
     */
    public static function create(array $data): ?int {
        try {
            return Database::transaction(function() use ($data) {
                // Generate reservation code
                $code = self::generateCode();
                
                // Create main reservation record
                Database::query("
                    INSERT INTO reservations 
                    (user_id, showtime_id, reservation_code, customer_name, customer_email, customer_phone, total_amount, status)
                    VALUES (?, ?, ?, ?, ?, ?, ?, 'confirmed')
                ", [
                    $data['user_id'] ?? null,
                    $data['showtime_id'],
                    $code,
                    $data['customer_name'],
                    $data['customer_email'],
                    $data['customer_phone'] ?? null,
                    $data['total_amount']
                ]);
                
                $reservationId = Database::lastInsertId();
                
                // Add seats
                foreach ($data['seats'] as $seat) {
                    Database::query("
                        INSERT INTO reservation_seats (reservation_id, seat_id, price)
                        VALUES (?, ?, ?)
                    ", [$reservationId, $seat['id'], $seat['price']]);
                }
                
                return $reservationId;
            });
        } catch (Exception $e) {
            error_log("Reservation creation failed: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Rezervasyon koduna göre getir
     */
    public static function getByCode(string $code): ?array {
        $reservation = Database::fetch("
            SELECT 
                r.*,
                s.show_date,
                s.show_time,
                s.price as ticket_price,
                m.title as movie_title,
                m.poster as movie_poster,
                m.duration as movie_duration,
                h.name as hall_name,
                h.hall_type,
                c.name as cinema_name,
                c.city,
                c.district,
                c.address as cinema_address
            FROM reservations r
            INNER JOIN showtimes s ON s.id = r.showtime_id
            INNER JOIN movies m ON m.id = s.movie_id
            INNER JOIN halls h ON h.id = s.hall_id
            INNER JOIN cinemas c ON c.id = h.cinema_id
            WHERE r.reservation_code = ?
        ", [$code]);
        
        if ($reservation) {
            // Koltukları da getir
            $reservation['seats'] = Database::fetchAll("
                SELECT 
                    rs.*,
                    s.row_letter,
                    s.seat_number
                FROM reservation_seats rs
                INNER JOIN seats s ON s.id = rs.seat_id
                WHERE rs.reservation_id = ?
                ORDER BY s.row_letter, s.seat_number
            ", [$reservation['id']]);
        }
        
        return $reservation;
    }
    
    /**
     * ID'ye göre getir
     */
    public static function getById(int $id): ?array {
        $reservation = Database::fetch("
            SELECT 
                r.*,
                s.show_date,
                s.show_time,
                s.price as ticket_price,
                m.title as movie_title,
                m.poster as movie_poster,
                m.duration as movie_duration,
                m.slug as movie_slug,
                h.name as hall_name,
                h.hall_type,
                c.name as cinema_name,
                c.city,
                c.district,
                c.address as cinema_address
            FROM reservations r
            INNER JOIN showtimes s ON s.id = r.showtime_id
            INNER JOIN movies m ON m.id = s.movie_id
            INNER JOIN halls h ON h.id = s.hall_id
            INNER JOIN cinemas c ON c.id = h.cinema_id
            WHERE r.id = ?
        ", [$id]);
        
        if ($reservation) {
            $reservation['seats'] = Database::fetchAll("
                SELECT 
                    rs.*,
                    s.row_letter,
                    s.seat_number
                FROM reservation_seats rs
                INNER JOIN seats s ON s.id = rs.seat_id
                WHERE rs.reservation_id = ?
                ORDER BY s.row_letter, s.seat_number
            ", [$reservation['id']]);
        }
        
        return $reservation;
    }
    
    /**
     * Kullanıcının rezervasyonları
     */
    public static function getByUserId(int $userId): array {
        return Database::fetchAll("
            SELECT 
                r.*,
                s.show_date,
                s.show_time,
                m.title as movie_title,
                m.poster as movie_poster,
                c.name as cinema_name
            FROM reservations r
            INNER JOIN showtimes s ON s.id = r.showtime_id
            INNER JOIN movies m ON m.id = s.movie_id
            INNER JOIN halls h ON h.id = s.hall_id
            INNER JOIN cinemas c ON c.id = h.cinema_id
            WHERE r.user_id = ?
            ORDER BY r.created_at DESC
        ", [$userId]);
    }
    
    /**
     * Rezervasyon iptal
     */
    public static function cancel(int $id): bool {
        $result = Database::query("
            UPDATE reservations SET status = 'cancelled' WHERE id = ?
        ", [$id]);
        
        return $result->rowCount() > 0;
    }
    
    /**
     * Benzersiz rezervasyon kodu oluştur
     */
    private static function generateCode(): string {
        $prefix = 'SNX';
        $timestamp = date('ymd');
        $random = strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 6));
        
        return $prefix . $timestamp . $random;
    }
}
