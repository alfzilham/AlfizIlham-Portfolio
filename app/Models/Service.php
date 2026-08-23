<?php
/**
 * Service Model
 */
class Service
{
    /**
     * Ensure image column exists (lazy migration)
     */
    public static function ensureImageColumn()
    {
        static $done = false;
        if ($done) return;
        $db = Database::getInstance();
        $hasImage = false;
        foreach ($db->fetchAll("PRAGMA table_info(services)") as $col) {
            if ($col['name'] === 'image') {
                $hasImage = true;
                break;
            }
        }
        if (!$hasImage) {
            $db->exec("ALTER TABLE services ADD COLUMN image TEXT");
        }
        $done = true;
    }

    /**
     * Get all services
     */
    public static function all()
    {
        self::ensureImageColumn();
        $db = Database::getInstance();
        return $db->fetchAll("SELECT * FROM services ORDER BY sort_order ASC, id ASC");
    }
}
