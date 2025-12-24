<?php
/**
 * View Helper Functions
 * Utility functions for views
 */

class ViewHelper
{
    /**
     * Format duration from minutes to hours and minutes
     */
    public static function formatDuration(int $minutes): string
    {
        $hours = floor($minutes / 60);
        $mins = $minutes % 60;
        return $hours > 0 ? "{$hours}s {$mins}dk" : "{$mins}dk";
    }

    /**
     * Format date
     */
    public static function formatDate(string $date, string $format = 'd.m.Y'): string
    {
        return date($format, strtotime($date));
    }

    /**
     * Format price
     */
    public static function formatPrice(float $price): string
    {
        return number_format($price, 0, ',', '.') . ' ₺';
    }

    /**
     * Truncate text
     */
    public static function truncate(string $text, int $length = 100, string $suffix = '...'): string
    {
        if (mb_strlen($text) <= $length) {
            return $text;
        }
        return mb_substr($text, 0, $length) . $suffix;
    }

    /**
     * Escape HTML
     */
    public static function e(string $text): string
    {
        return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Generate asset URL
     */
    public static function asset(string $path): string
    {
        return '/assets/' . ltrim($path, '/');
    }
}
