<?php
require_once __DIR__ . '/Database.php';

class Showtime {
    
    /**
     * Film ID'sine göre seansları getir
     */
    public static function getByMovieId(int $movieId, ?string $date = null): array {
        $sql = "
            SELECT 
                s.*,
                h.name as hall_name,
                h.hall_type,
                c.name as cinema_name,
                c.city,
                c.district
            FROM showtimes s
            INNER JOIN halls h ON h.id = s.hall_id
            INNER JOIN cinemas c ON c.id = h.cinema_id
            WHERE s.movie_id = ? 
            AND s.is_active = 1
            AND s.show_date >= CURDATE()
        ";
        
        $params = [$movieId];
        
        if ($date) {
            $sql .= " AND s.show_date = ?";
            $params[] = $date;
        }
        
        $sql .= " ORDER BY s.show_date ASC, s.show_time ASC";
        
        return Database::fetchAll($sql, $params);
    }
    
    /**
     * Seans ID'sine göre detay getir
     */
    public static function getById(int $id): ?array {
        return Database::fetch("
            SELECT 
                s.*,
                m.title as movie_title,
                m.poster as movie_poster,
                m.duration as movie_duration,
                m.slug as movie_slug,
                h.name as hall_name,
                h.hall_type,
                h.total_rows,
                h.seats_per_row,
                c.name as cinema_name,
                c.city,
                c.district,
                c.address as cinema_address
            FROM showtimes s
            INNER JOIN movies m ON m.id = s.movie_id
            INNER JOIN halls h ON h.id = s.hall_id
            INNER JOIN cinemas c ON c.id = h.cinema_id
            WHERE s.id = ? AND s.is_active = 1
        ", [$id]);
    }
    
    /**
     * Filme göre müsait tarihleri getir
     */
    public static function getAvailableDates(int $movieId): array {
        return Database::fetchAll("
            SELECT DISTINCT show_date 
            FROM showtimes 
            WHERE movie_id = ? 
            AND is_active = 1 
            AND show_date >= CURDATE()
            ORDER BY show_date ASC
            LIMIT 14
        ", [$movieId]);
    }
    
    /**
     * Seansları sinemaya göre grupla
     */
    public static function groupByCinema(array $showtimes): array {
        $grouped = [];
        
        foreach ($showtimes as $showtime) {
            $cinemaId = $showtime['cinema_name'];
            
            if (!isset($grouped[$cinemaId])) {
                $grouped[$cinemaId] = [
                    'cinema_name' => $showtime['cinema_name'],
                    'city' => $showtime['city'],
                    'district' => $showtime['district'],
                    'halls' => []
                ];
            }
            
            $hallKey = $showtime['hall_name'] . ' - ' . $showtime['hall_type'];
            
            if (!isset($grouped[$cinemaId]['halls'][$hallKey])) {
                $grouped[$cinemaId]['halls'][$hallKey] = [
                    'hall_name' => $showtime['hall_name'],
                    'hall_type' => $showtime['hall_type'],
                    'showtimes' => []
                ];
            }
            
            $grouped[$cinemaId]['halls'][$hallKey]['showtimes'][] = $showtime;
        }
        
        return $grouped;
    }
}
