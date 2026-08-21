<?php
/**
 * Service Model
 */
class Service
{
    /**
     * Get all services
     */
    public static function all()
    {
        $db = Database::getInstance();
        return $db->fetchAll("SELECT * FROM services ORDER BY sort_order ASC, id ASC");
    }
}
