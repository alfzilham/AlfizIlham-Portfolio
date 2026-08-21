<?php
/**
 * VisitorService — Track and count visitors
 */
class VisitorService
{
    /**
     * Track current visitor (once per session)
     */
    public static function track()
    {
        // Skip if already tracked this session
        if (isset($_SESSION['visitor_tracked'])) {
            return;
        }

        $ip = Request::ip();
        $ua = Request::userAgent();

        // Skip bots
        if (self::isBot($ua)) {
            return;
        }

        VisitorModel::record($ip, $ua, 'ID');
        $_SESSION['visitor_tracked'] = true;
    }

    /**
     * Get total visitor count
     */
    public static function getCount()
    {
        return VisitorModel::count();
    }

    /**
     * Get today's visitor count
     */
    public static function getTodayCount()
    {
        return VisitorModel::todayCount();
    }

    /**
     * Check if user agent is a bot
     */
    private static function isBot($ua)
    {
        $bots = ['bot', 'crawler', 'spider', 'curl', 'wget', 'googlebot', 'bingbot', 'slurp'];
        $ua = strtolower($ua);
        foreach ($bots as $bot) {
            if (strpos($ua, $bot) !== false) {
                return true;
            }
        }
        return false;
    }
}
