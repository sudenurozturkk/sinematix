<?php
require_once __DIR__ . '/Database.php';

class Movie {
    
    /**
     * Get all active movies
     */
    public static function getAll(): array {
        return Database::fetchAll("
            SELECT * FROM movies 
            WHERE is_active = 1 
            ORDER BY release_date DESC
        ");
    }
    
    /**
     * Get now showing movies
     */
    public static function getNowShowing(): array {
        return Database::fetchAll("
            SELECT DISTINCT m.* FROM movies m
            INNER JOIN showtimes s ON s.movie_id = m.id
            WHERE m.is_active = 1 
            AND s.show_date >= CURDATE()
            AND s.is_active = 1
            ORDER BY m.rating DESC
        ");
    }
    
    /**
     * Get coming soon movies
     */
    public static function getComingSoon(): array {
        return Database::fetchAll("
            SELECT * FROM movies 
            WHERE is_active = 1 
            AND release_date > CURDATE()
            ORDER BY release_date ASC
        ");
    }
    
    /**
     * Get movie by slug
     */
    public static function getBySlug(string $slug): ?array {
        return Database::fetch("
            SELECT * FROM movies 
            WHERE slug = ? AND is_active = 1
        ", [$slug]);
    }
    
    /**
     * Get movie by ID
     */
    public static function getById(int $id): ?array {
        return Database::fetch("
            SELECT * FROM movies 
            WHERE id = ? AND is_active = 1
        ", [$id]);
    }
    
    /**
     * Search movies
     */
    public static function search(string $query): array {
        $searchTerm = "%{$query}%";
        return Database::fetchAll("
            SELECT * FROM movies 
            WHERE is_active = 1 
            AND (title LIKE ? OR original_title LIKE ? OR director LIKE ? OR cast LIKE ?)
            ORDER BY rating DESC
        ", [$searchTerm, $searchTerm, $searchTerm, $searchTerm]);
    }
    
    /**
     * Get movies by genre
     */
    public static function getByGenre(string $genre): array {
        return Database::fetchAll("
            SELECT * FROM movies 
            WHERE is_active = 1 AND genre LIKE ?
            ORDER BY rating DESC
        ", ["%{$genre}%"]);
    }
}
