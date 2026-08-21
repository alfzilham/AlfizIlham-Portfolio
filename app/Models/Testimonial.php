<?php
/**
 * Testimonial Model
 */
class Testimonial
{
    /**
     * Get all testimonials
     */
    public static function all()
    {
        $db = Database::getInstance();
        return $db->fetchAll("SELECT * FROM testimonials ORDER BY sort_order ASC, id ASC");
    }
}
