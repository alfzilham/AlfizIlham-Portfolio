<?php
/**
 * Gallery Model
 */
class Gallery
{
    /**
     * Get all gallery items
     */
    public static function all()
    {
        $db = Database::getInstance();
        return $db->fetchAll("SELECT * FROM gallery ORDER BY sort_order ASC, id ASC");
    }
}
