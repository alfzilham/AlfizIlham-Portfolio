<?php
/**
 * i18n — Internationalization Helper
 */
class i18n
{
    private static $strings = [];
    private static $lang = 'en';

    /**
     * Load language file
     */
    public static function load($lang = 'en')
    {
        $file = LANG_PATH . '/' . $lang . '.php';
        if (file_exists($file)) {
            self::$strings = require $file;
            self::$lang = $lang;
        } else {
            // Fallback to English
            self::$strings = require LANG_PATH . '/en.php';
            self::$lang = 'en';
        }
    }

    /**
     * Get translated string by key
     *
     * @param string $key     Translation key (e.g. 'nav_about')
     * @param array  $params  Optional params for sprintf
     * @return string
     */
    public static function t($key, $params = [])
    {
        $string = self::$strings[$key] ?? $key;

        if (!empty($params)) {
            $string = vsprintf($string, $params);
        }

        return $string;
    }

    /**
     * Get current language
     */
    public static function lang()
    {
        return self::$lang;
    }

    /**
     * Check if current language is English
     */
    public static function isEn()
    {
        return self::$lang === 'en';
    }

    /**
     * Check if current language is Indonesian
     */
    public static function isId()
    {
        return self::$lang === 'id';
    }
}
