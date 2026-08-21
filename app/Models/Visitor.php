<?php
/**
 * Visitor Model
 */
class VisitorModel
{
    /**
     * Get total unique visitor count
     */
    public static function count()
    {
        $db = Database::getInstance();
        return $db->count('visitors');
    }

    /**
     * Get today's visitor count
     */
    public static function todayCount()
    {
        $db = Database::getInstance();
        return $db->count('visitors', "date(visited_at) = date('now')");
    }

    /**
     * Record a visitor
     */
    public static function record($ip, $userAgent, $country = 'ID')
    {
        $db = Database::getInstance();
        return $db->insert('visitors', [
            'ip_address' => $ip,
            'user_agent' => $userAgent,
            'country' => $country,
            'visited_at' => date('Y-m-d H:i:s'),
        ]);
    }
}
