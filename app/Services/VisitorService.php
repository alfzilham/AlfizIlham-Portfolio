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

        // Detect country from IP
        $country = self::getCountry($ip);

        VisitorModel::record($ip, $ua, $country);
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

    /**
     * Detect country from IP using ip-api.com (free, no key)
     */
    private static function getCountry($ip)
    {
        // Skip localhost/private IPs
        if ($ip === '127.0.0.1' || $ip === '::1' || preg_match('/^192\.168\./', $ip) || preg_match('/^10\./', $ip)) {
            return 'ID';
        }

        try {
            $context = stream_context_create(['http' => ['timeout' => 2]]);
            $response = @file_get_contents("http://ip-api.com/json/{$ip}?fields=countryCode", false, $context);
            if ($response) {
                $data = json_decode($response, true);
                if (!empty($data['countryCode'])) {
                    return $data['countryCode'];
                }
            }
        } catch (\Exception $e) {
            // Fail silently, default to ID
        }

        return 'ID';
    }

    /**
     * Get visitor count by country
     */
    public static function getByCountry()
    {
        return VisitorModel::countByCountry();
    }
}
