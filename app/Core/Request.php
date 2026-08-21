<?php
/**
 * Request — HTTP Request Wrapper
 */
class Request
{
    /**
     * Get a GET parameter
     */
    public static function get($key, $default = null)
    {
        return $_GET[$key] ?? $default;
    }

    /**
     * Get a POST parameter
     */
    public static function post($key, $default = null)
    {
        return $_POST[$key] ?? $default;
    }

    /**
     * Get all POST data
     */
    public static function all()
    {
        return $_POST;
    }

    /**
     * Check if request method is POST
     */
    public static function isPost()
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    /**
     * Check if request is AJAX
     */
    public static function isAjax()
    {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH'])
            && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    /**
     * Get JSON body from request
     */
    public static function json()
    {
        $raw = file_get_contents('php://input');
        return json_decode($raw, true) ?? [];
    }

    /**
     * Get client IP address
     */
    public static function ip()
    {
        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }

    /**
     * Get user agent
     */
    public static function userAgent()
    {
        return $_SERVER['HTTP_USER_AGENT'] ?? '';
    }

    /**
     * Sanitize a string input
     */
    public static function sanitize($value)
    {
        return htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
    }
}
