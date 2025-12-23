<?php
require_once __DIR__ . '/Database.php';

class Seat {
    
    /**
     * Salon koltuk haritasını getir
     */
    public static function getHallSeats(int $hallId): array {
        return Database::fetchAll("
            SELECT id, row_letter, seat_number, seat_type
            FROM seats
            WHERE hall_id = ? AND is_active = 1
            ORDER BY row_letter ASC, seat_number ASC
        ", [$hallId]);
    }
    
    /**
     * Seans için koltuk durumlarını getir
     */
    public static function getSeatsWithStatus(int $showtimeId): array {
        // Önce seans bilgisini al
        $showtime = Database::fetch("
            SELECT hall_id FROM showtimes WHERE id = ?
        ", [$showtimeId]);
        
        if (!$showtime) {
            return [];
        }
        
        // Tüm koltukları ve durumlarını getir
        return Database::fetchAll("
            SELECT 
                s.id,
                s.row_letter,
                s.seat_number,
                s.seat_type,
                CASE 
                    WHEN rs.id IS NOT NULL THEN 'occupied'
                    ELSE 'available'
                END as status
            FROM seats s
            LEFT JOIN reservation_seats rs ON rs.seat_id = s.id
            LEFT JOIN reservations r ON r.id = rs.reservation_id 
                AND r.showtime_id = ? 
                AND r.status = 'confirmed'
            WHERE s.hall_id = ? AND s.is_active = 1
            ORDER BY s.row_letter ASC, s.seat_number ASC
        ", [$showtimeId, $showtime['hall_id']]);
    }
    
    /**
     * Koltukları satır bazlı grupla
     */
    public static function groupByRow(array $seats): array {
        $grouped = [];
        
        foreach ($seats as $seat) {
            $row = $seat['row_letter'];
            if (!isset($grouped[$row])) {
                $grouped[$row] = [];
            }
            $grouped[$row][] = $seat;
        }
        
        return $grouped;
    }
    
    /**
     * Koltuk ID'lerini kontrol et
     */
    public static function validateSeats(array $seatIds, int $showtimeId): bool {
        if (empty($seatIds)) {
            return false;
        }
        
        $placeholders = implode(',', array_fill(0, count($seatIds), '?'));
        
        // Koltukların zaten rezerve edilip edilmediğini kontrol et
        $params = array_merge([$showtimeId], $seatIds);
        
        $occupied = Database::fetch("
            SELECT COUNT(*) as count
            FROM reservation_seats rs
            INNER JOIN reservations r ON r.id = rs.reservation_id
            WHERE r.showtime_id = ?
            AND r.status = 'confirmed'
            AND rs.seat_id IN ({$placeholders})
        ", $params);
        
        return ($occupied['count'] ?? 0) == 0;
    }
    
    /**
     * ID'lere göre koltuk bilgilerini getir
     */
    public static function getByIds(array $seatIds): array {
        if (empty($seatIds)) {
            return [];
        }
        
        $placeholders = implode(',', array_fill(0, count($seatIds), '?'));
        
        return Database::fetchAll("
            SELECT * FROM seats 
            WHERE id IN ({$placeholders})
            ORDER BY row_letter ASC, seat_number ASC
        ", $seatIds);
    }
}
