<?php
/**
 * Faq Model
 */
class Faq
{
    /**
     * Get all FAQs
     */
    public static function all()
    {
        $db = Database::getInstance();
        return $db->fetchAll("SELECT * FROM faqs ORDER BY sort_order ASC, id ASC");
    }

    /**
     * Get FAQs by category
     */
    public static function byCategory($category)
    {
        $db = Database::getInstance();
        return $db->fetchAll(
            "SELECT * FROM faqs WHERE category = ? ORDER BY sort_order ASC, id ASC",
            [$category]
        );
    }

    /**
     * Get all unique categories
     */
    public static function categories()
    {
        $db = Database::getInstance();
        $rows = $db->fetchAll("SELECT DISTINCT category FROM faqs ORDER BY category ASC");
        return array_column($rows, 'category');
    }
}
